<?php

namespace App\Libraries;

/**
 * BackgroundScheduler - Pseudo-CRON untuk Shared Hosting
 * 
 * Cara kerja:
 * 1. Check dilakukan saat ada request ke aplikasi (user activity)
 * 2. Simpan last_check timestamp di file/database
 * 3. Jika sudah lewat interval tertentu, jalankan check
 * 4. Sangat ringan, tidak butuh CRON server
 * 
 * Usage di BaseController:
 * protected function afterConstruct() {
 *     $scheduler = new BackgroundScheduler();
 *     $scheduler->runIfNeeded();
 * }
 */
class BackgroundScheduler
{
    private $lockFile;
    private $db;
    
    // Interval check (dalam menit)
    private $intervals = [
        'invoice_overdue' => 60,      // Check setiap 1 jam
        'sparepart_stock' => 30,      // Check setiap 30 menit
        'contract_expiry' => 1440,    // Check setiap 24 jam
        'pmps_check' => 360           // Check setiap 6 jam
    ];
    
    public function __construct()
    {
        $this->lockFile = WRITEPATH . 'scheduler_lock.json';
        $this->db = \Config\Database::connect();
    }
    
    /**
     * Jalankan scheduler jika sudah waktunya
     * Method ini di-call dari BaseController atau event
     */
    public function runIfNeeded()
    {
        try {
            // Cek lock file untuk avoid double execution
            if ($this->isLocked()) {
                return; // Sedang berjalan, skip
            }
            
            // Load last check times
            $lastChecks = $this->getLastChecks();
            $now = time();
            
            // Check setiap task
            foreach ($this->intervals as $task => $interval) {
                $lastCheck = $lastChecks[$task] ?? 0;
                $intervalSeconds = $interval * 60;
                
                // Jika sudah lewat interval, jalankan
                if (($now - $lastCheck) >= $intervalSeconds) {
                    $this->lock(); // Set lock
                    
                    // Jalankan task
                    $this->runTask($task);
                    
                    // Update last check
                    $this->updateLastCheck($task, $now);
                    
                    $this->unlock(); // Release lock
                    
                    // Hanya jalankan 1 task per request untuk tetap ringan
                    break;
                }
            }
        } catch (\Exception $e) {
            log_message('error', '[BackgroundScheduler] Error: ' . $e->getMessage());
            $this->unlock(); // Pastikan lock di-release
        }
    }
    
    /**
     * Jalankan task spesifik
     */
    private function runTask($task)
    {
        log_message('info', "[BackgroundScheduler] Running task: {$task}");
        
        switch ($task) {
            case 'invoice_overdue':
                $this->checkInvoiceOverdue();
                break;
                
            case 'sparepart_stock':
                $this->checkSparepartStock();
                break;
                
            case 'contract_expiry':
                $this->checkContractExpiry();
                break;
                
            case 'pmps_check':
                $this->checkPMPS();
                break;
        }
    }
    
    /**
     * Check Invoice Overdue
     */
    private function checkInvoiceOverdue()
    {
        // Query invoice yang overdue (lewat due date dan belum lunas)
        $overdueInvoices = $this->db->table('invoices')
            ->where('status', 'Pending')
            ->where('due_date <', date('Y-m-d'))
            ->get()
            ->getResultArray();
        
        if (empty($overdueInvoices)) {
            return;
        }
        
        helper('notification');
        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = floor((strtotime(date('Y-m-d')) - strtotime($invoice['due_date'])) / 86400);
            
            // Hanya kirim notif untuk overdue 1, 7, 14, 30 hari (tidak spam setiap hari)
            if (in_array($daysOverdue, [1, 7, 14, 30])) {
                notify_invoice_overdue([
                    'id' => $invoice['id'],
                    'invoice_number' => $invoice['invoice_number'],
                    'customer_name' => $invoice['customer_name'],
                    'amount' => $invoice['total_amount'],
                    'days_overdue' => $daysOverdue,
                    'url' => base_url('/finance/invoices/' . $invoice['id'])
                ]);
            }
        }
        
        log_message('info', "[BackgroundScheduler] Checked {count($overdueInvoices)} overdue invoices");
    }
    
    /**
     * Check Sparepart Stock
     * CATATAN: Ini sebenarnya lebih baik di-trigger saat update stock (lihat method 2)
     */
    private function checkSparepartStock()
    {
        // Query sparepart dengan stok rendah
        $lowStockItems = $this->db->table('inventory_spareparts is')
            ->select('is.*, s.kode, s.desc_sparepart, s.minimum_stock')
            ->join('sparepart s', 's.id_sparepart = is.sparepart_id')
            ->where('s.minimum_stock IS NOT NULL')
            ->where('is.stok <=', 's.minimum_stock', false) // Low or out of stock
            ->get()
            ->getResultArray();
        
        if (empty($lowStockItems)) {
            return;
        }
        
        helper('notification');
        foreach ($lowStockItems as $item) {
            if ($item['stok'] == 0) {
                notify_sparepart_out_of_stock([
                    'id' => $item['id'],
                    'nama_sparepart' => $item['desc_sparepart'],
                    'kode' => $item['kode'],
                    'lokasi' => $item['lokasi_rak'],
                    'url' => base_url('/warehouse/spareparts')
                ]);
            } else {
                notify_sparepart_low_stock([
                    'id' => $item['id'],
                    'nama_sparepart' => $item['desc_sparepart'],
                    'kode' => $item['kode'],
                    'stok_saat_ini' => $item['stok'],
                    'minimum_stock' => $item['minimum_stock'],
                    'url' => base_url('/warehouse/spareparts')
                ]);
            }
        }
        
        log_message('info', "[BackgroundScheduler] Checked " . count($lowStockItems) . " low stock items");
    }
    
    /**
     * Check Contract Expiry
     */
    private function checkContractExpiry()
    {
        // Query kontrak yang akan expired dalam 30 hari
        $expiringContracts = $this->db->table('kontrak k')
            ->select('k.*, c.customer_name')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->where('k.status', 'ACTIVE')
            ->where('k.tanggal_berakhir >=', date('Y-m-d'))
            ->where('k.tanggal_berakhir <=', date('Y-m-d', strtotime('+30 days')))
            ->get()
            ->getResultArray();
        
        if (empty($expiringContracts)) {
            return;
        }
        
        helper('notification');
        foreach ($expiringContracts as $contract) {
            $daysUntilExpiry = floor((strtotime($contract['tanggal_berakhir']) - strtotime(date('Y-m-d'))) / 86400);
            
            // Kirim notif di milestone: 30, 14, 7, 3, 1 hari
            if (in_array($daysUntilExpiry, [30, 14, 7, 3, 1])) {
                notify_customer_contract_expired([
                    'id' => $contract['id'],
                    'contract_number' => $contract['no_kontrak'],
                    'customer_name' => $contract['customer_name'],
                    'expiry_date' => $contract['tanggal_berakhir'],
                    'days_until_expiry' => $daysUntilExpiry,
                    'url' => base_url('/marketing/contracts/' . $contract['id'])
                ]);
            }
        }
        
        log_message('info', "[BackgroundScheduler] Checked " . count($expiringContracts) . " expiring contracts");
    }
    
    /**
     * Check PMPS (Preventive Maintenance)
     */
    private function checkPMPS()
    {
        // Contoh: Query unit yang perlu maintenance
        // Sesuaikan dengan struktur tabel PMPS Anda
        
        // Query unit dengan jadwal maintenance yang due soon
        $dueMaintenances = $this->db->table('inventory_unit iu')
            ->select('iu.*, iu.no_unit')
            ->where('iu.next_maintenance_date IS NOT NULL')
            ->where('iu.next_maintenance_date <=', date('Y-m-d', strtotime('+7 days')))
            ->get()
            ->getResultArray();
        
        if (empty($dueMaintenances)) {
            return;
        }
        
        helper('notification');
        foreach ($dueMaintenances as $unit) {
            $daysUntilDue = floor((strtotime($unit['next_maintenance_date']) - strtotime(date('Y-m-d'))) / 86400);
            
            if ($daysUntilDue < 0) {
                // Overdue
                notify_pmps_overdue([
                    'id' => $unit['id_inventory_unit'],
                    'no_unit' => $unit['no_unit'],
                    'days_overdue' => abs($daysUntilDue),
                    'url' => base_url('/maintenance/pmps/' . $unit['id_inventory_unit'])
                ]);
            } else {
                // Due soon
                notify_pmps_due_soon([
                    'id' => $unit['id_inventory_unit'],
                    'no_unit' => $unit['no_unit'],
                    'days_until_due' => $daysUntilDue,
                    'url' => base_url('/maintenance/pmps/' . $unit['id_inventory_unit'])
                ]);
            }
        }
        
        log_message('info', "[BackgroundScheduler] Checked " . count($dueMaintenances) . " PMPS schedules");
    }
    
    /**
     * Get last check timestamps
     */
    private function getLastChecks()
    {
        if (!file_exists($this->lockFile)) {
            return [];
        }
        
        $data = json_decode(file_get_contents($this->lockFile), true);
        return $data['last_checks'] ?? [];
    }
    
    /**
     * Update last check timestamp untuk task tertentu
     */
    private function updateLastCheck($task, $timestamp)
    {
        $data = file_exists($this->lockFile) 
            ? json_decode(file_get_contents($this->lockFile), true) 
            : ['last_checks' => [], 'is_locked' => false];
        
        $data['last_checks'][$task] = $timestamp;
        
        file_put_contents($this->lockFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Check if scheduler is locked (sedang berjalan)
     */
    private function isLocked()
    {
        if (!file_exists($this->lockFile)) {
            return false;
        }
        
        $data = json_decode(file_get_contents($this->lockFile), true);
        $isLocked = $data['is_locked'] ?? false;
        $lockTime = $data['lock_time'] ?? 0;
        
        // Auto-unlock jika locked lebih dari 5 menit (stuck)
        if ($isLocked && (time() - $lockTime) > 300) {
            $this->unlock();
            return false;
        }
        
        return $isLocked;
    }
    
    /**
     * Set lock
     */
    private function lock()
    {
        $data = file_exists($this->lockFile) 
            ? json_decode(file_get_contents($this->lockFile), true) 
            : ['last_checks' => []];
        
        $data['is_locked'] = true;
        $data['lock_time'] = time();
        
        file_put_contents($this->lockFile, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    /**
     * Release lock
     */
    private function unlock()
    {
        if (!file_exists($this->lockFile)) {
            return;
        }
        
        $data = json_decode(file_get_contents($this->lockFile), true);
        $data['is_locked'] = false;
        unset($data['lock_time']);
        
        file_put_contents($this->lockFile, json_encode($data, JSON_PRETTY_PRINT));
    }
}
