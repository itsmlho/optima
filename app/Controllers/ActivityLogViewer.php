<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use Exception;

class ActivityLogViewer extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Activity Log | OPTIMA',
            'page_title' => 'System Activity Log',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/admin/activity-log' => 'Activity Log'
            ]
        ];

        return view('admin/activity_log', $data);
    }

    public function getData()
    {
        // Debug log untuk memastikan controller yang tepat dipanggil
        log_message('debug', 'ActivityLogViewer::getData() called');
        
        $request = service('request');
        $db = \Config\Database::connect();
        
        // DataTables parameters
        $draw = $request->getGet('draw') ?? $request->getPost('draw') ?? 1;
        $start = $request->getPost('start') ?: 0;
        $length = $request->getPost('length') ?: 25;
        $searchData = $request->getPost('search') ?? [];
        $searchValue = $searchData['value'] ?? '';
        $orderData = $request->getPost('order') ?? [];
        $orderColumn = $orderData[0]['column'] ?? 0;
        $orderDir = $orderData[0]['dir'] ?? 'desc';

        // Base query - menggunakan struktur tabel yang sudah dioptimasi
        $builder = $db->table('system_activity_log sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');

        // Search functionality
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('sal.action_description', $searchValue)
                ->orLike('sal.table_name', $searchValue)
                ->orLike('sal.action_type', $searchValue)
                ->orLike('sal.module_name', $searchValue)
                ->orLike('u.username', $searchValue)
                ->groupEnd();
        }

        // Count total records
        $totalRecords = $db->table('system_activity_log')->countAllResults();
        
        // Count filtered records
        $filteredRecords = $builder->countAllResults(false);

        // Order mapping
        $columns = ['sal.created_at', 'u.username', 'sal.module_name', 'sal.action_type', 'sal.table_name', 'sal.action_description', 'sal.business_impact'];
        $orderBy = $columns[$orderColumn] ?? 'sal.created_at';
        
        $builder->orderBy($orderBy, $orderDir);
        $builder->limit($length, $start);

        $query = $builder->get();
        $data = [];

        foreach ($query->getResultArray() as $row) {
            $detailedDescription = $this->generateDetailedDescription($row);
            
            $rowData = [
                'created_at' => date('d/m/Y H:i:s', strtotime($row['created_at'])),
                'username' => $row['username'] ?? 'System',
                'module_name' => $row['module_name'] ?? '-',
                'action_type' => '<span class="badge bg-' . $this->getActionBadgeColor($row['action_type']) . '">' . $row['action_type'] . '</span>',
                'table_name' => $row['table_name'],
                'action_description' => $detailedDescription,
                'business_impact' => '<span class="badge bg-' . $this->getImpactBadgeColor($row['business_impact']) . '">' . $row['business_impact'] . '</span>',
                'is_critical' => $row['is_critical'] ? '<i class="fas fa-exclamation-triangle text-warning"></i>' : '',
                'activity_id' => (int)$row['id'] // Pastikan ID adalah integer
            ];
            
            $data[] = $rowData;
        }

        // Debug log untuk melihat struktur data
        if (!empty($data)) {
            log_message('debug', 'ActivityLogViewer first row data: ' . json_encode($data[0]));
        }

        return $this->response->setJSON([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function getDetails($id)
    {
        $db = \Config\Database::connect();
        
        $builder = $db->table('system_activity_log sal');
        $builder->select('sal.*, u.username, u.first_name, u.last_name');
        $builder->join('users u', 'u.id = sal.user_id', 'left');
        $builder->where('sal.id', $id);
        
        $row = $builder->get()->getRowArray();
        
        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan']);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'id' => $row['id'],
                'created_at' => date('d F Y H:i:s', strtotime($row['created_at'])),
                'username' => $row['username'] ?? 'System',
                'full_name' => trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? '')),
                'module_name' => $row['module_name'],
                'action_type' => $row['action_type'],
                'table_name' => $row['table_name'],
                'record_id' => $row['record_id'],
                'action_description' => $row['action_description'],
                'old_values' => $row['old_values'] ? json_decode($row['old_values'], true) : null,
                'new_values' => $row['new_values'] ? json_decode($row['new_values'], true) : null,
                'affected_fields' => $row['affected_fields'] ? json_decode($row['affected_fields'], true) : null,
                'workflow_stage' => $row['workflow_stage'],
                'business_impact' => $row['business_impact'],
                'is_critical' => $row['is_critical']
            ]
        ]);
    }

    private function generateDetailedDescription($row)
    {
        $db = \Config\Database::connect();
        $description = $row['action_description'];
        
        try {
            switch ($row['table_name']) {
                case 'spk':
                    $description = $this->generateSPKDescription($row, $db);
                    break;
                case 'kontrak':
                    $description = $this->generateKontrakDescription($row, $db);
                    break;
                case 'kontrak_spesifikasi':
                    $description = $this->generateKontrakSpesifikasiDescription($row, $db);
                    break;
                case 'delivery_instructions':
                    $description = $this->generateDeliveryDescription($row, $db);
                    break;
                case 'inventory_unit':
                    $description = $this->generateInventoryDescription($row, $db);
                    break;
                default:
                    // For other tables, keep original description
                    break;
            }
        } catch (Exception $e) {
            // If error, return original description
            log_message('error', 'Error generating detailed description: ' . $e->getMessage());
        }
        
        return $description;
    }

    private function generateSPKDescription($row, $db)
    {
        // Get SPK number
        $spkQuery = $db->table('spk')->select('nomor_spk')->where('id', $row['record_id'])->get();
        $spkData = $spkQuery->getRowArray();
        $spkNumber = $spkData['nomor_spk'] ?? 'SPK#' . $row['record_id'];
        
        // Analyze what changed
        $oldValues = json_decode($row['old_values'], true);
        $newValues = json_decode($row['new_values'], true);
        
        if ($row['action_type'] === 'CREATE') {
            return "SPK {$spkNumber} dibuat dengan spesifikasi lengkap";
        }
        
        if ($row['action_type'] === 'UPDATE') {
            $changes = [];
            
            // Check for workflow stage changes
            if (isset($newValues['persiapan_unit_tanggal_approve']) && !isset($oldValues['persiapan_unit_tanggal_approve'])) {
                $unitId = $newValues['persiapan_unit_id'] ?? null;
                $unitNo = $this->getUnitNumber($unitId, $db);
                $mekanik = $newValues['persiapan_unit_mekanik'] ?? 'Unknown';
                $changes[] = "menyelesaikan persiapan unit {$unitNo} oleh {$mekanik}";
            }
            
            if (isset($newValues['fabrikasi_tanggal_approve']) && !isset($oldValues['fabrikasi_tanggal_approve'])) {
                $mekanik = $newValues['fabrikasi_mekanik'] ?? 'Unknown';
                $changes[] = "menyelesaikan fabrikasi oleh {$mekanik}";
            }
            
            if (isset($newValues['painting_tanggal_approve']) && !isset($oldValues['painting_tanggal_approve'])) {
                $mekanik = $newValues['painting_mekanik'] ?? 'Unknown';
                $changes[] = "menyelesaikan painting oleh {$mekanik}";
            }
            
            if (isset($newValues['pdi_tanggal_approve']) && !isset($oldValues['pdi_tanggal_approve'])) {
                $mekanik = $newValues['pdi_mekanik'] ?? 'Unknown';
                $changes[] = "menyelesaikan PDI oleh {$mekanik}";
            }
            
            // Check for assignment/preparation changes
            if (isset($newValues['persiapan_unit_mekanik']) && !isset($oldValues['persiapan_unit_mekanik'])) {
                $mekanik = $newValues['persiapan_unit_mekanik'];
                $changes[] = "memulai persiapan unit oleh {$mekanik}";
            }
            
            if (isset($newValues['fabrikasi_mekanik']) && !isset($oldValues['fabrikasi_mekanik'])) {
                $mekanik = $newValues['fabrikasi_mekanik'];
                $changes[] = "memulai fabrikasi oleh {$mekanik}";
            }
            
            if (isset($newValues['painting_mekanik']) && !isset($oldValues['painting_mekanik'])) {
                $mekanik = $newValues['painting_mekanik'];
                $changes[] = "memulai painting oleh {$mekanik}";
            }
            
            if (isset($newValues['pdi_mekanik']) && !isset($oldValues['pdi_mekanik'])) {
                $mekanik = $newValues['pdi_mekanik'];
                $changes[] = "memulai PDI oleh {$mekanik}";
            }
            
            // Check for status changes
            if (isset($newValues['status']) && isset($oldValues['status']) && $newValues['status'] !== $oldValues['status']) {
                $statusMap = [
                    'DRAFT' => 'Draft',
                    'IN_PROGRESS' => 'Sedang Diproses',
                    'READY' => 'Siap',
                    'COMPLETED' => 'Selesai',
                    'CANCELLED' => 'Dibatalkan'
                ];
                $oldStatus = $statusMap[$oldValues['status']] ?? $oldValues['status'];
                $newStatus = $statusMap[$newValues['status']] ?? $newValues['status'];
                $changes[] = "mengubah status dari {$oldStatus} ke {$newStatus}";
            }
            
            if (!empty($changes)) {
                return "SPK {$spkNumber} " . implode(' dan ', $changes);
            }
        }
        
        return "SPK {$spkNumber} " . strtolower($row['action_description']);
    }

    private function generateKontrakDescription($row, $db)
    {
        // Get kontrak number and customer
        $kontrakQuery = $db->table('kontrak')->select('nomor_kontrak, nama_customer')->where('id', $row['record_id'])->get();
        $kontrakData = $kontrakQuery->getRowArray();
        $kontrakNumber = $kontrakData['nomor_kontrak'] ?? 'KONTRAK#' . $row['record_id'];
        $customerName = $kontrakData['nama_customer'] ?? '';
        
        if ($row['action_type'] === 'CREATE') {
            $customerInfo = $customerName ? " untuk customer {$customerName}" : "";
            return "Kontrak {$kontrakNumber} dibuat{$customerInfo} dengan detail spesifikasi unit";
        }
        
        if ($row['action_type'] === 'UPDATE') {
            $oldValues = json_decode($row['old_values'], true);
            $newValues = json_decode($row['new_values'], true);
            
            $changes = [];
            
            if (isset($newValues['status']) && isset($oldValues['status']) && $newValues['status'] !== $oldValues['status']) {
                $statusMap = [
                    'DRAFT' => 'Draft',
                    'APPROVED' => 'Disetujui',
                    'ACTIVE' => 'Aktif',
                    'COMPLETED' => 'Selesai',
                    'CANCELLED' => 'Dibatalkan'
                ];
                $oldStatus = $statusMap[$oldValues['status']] ?? $oldValues['status'];
                $newStatus = $statusMap[$newValues['status']] ?? $newValues['status'];
                $changes[] = "mengubah status dari {$oldStatus} ke {$newStatus}";
            }
            
            if (isset($newValues['tanggal_mulai']) && $newValues['tanggal_mulai'] !== ($oldValues['tanggal_mulai'] ?? null)) {
                $tanggal = date('d/m/Y', strtotime($newValues['tanggal_mulai']));
                $changes[] = "mengubah tanggal mulai ke {$tanggal}";
            }
            
            if (isset($newValues['tanggal_berakhir']) && $newValues['tanggal_berakhir'] !== ($oldValues['tanggal_berakhir'] ?? null)) {
                $tanggal = date('d/m/Y', strtotime($newValues['tanggal_berakhir']));
                $changes[] = "mengubah tanggal berakhir ke {$tanggal}";
            }
            
            if (isset($newValues['total_sewa_bulanan']) && $newValues['total_sewa_bulanan'] !== ($oldValues['total_sewa_bulanan'] ?? null)) {
                $amount = number_format($newValues['total_sewa_bulanan'], 0, ',', '.');
                $changes[] = "mengubah total sewa bulanan menjadi Rp {$amount}";
            }
            
            if (!empty($changes)) {
                return "Kontrak {$kontrakNumber} " . implode(' dan ', $changes);
            }
        }
        
        if ($row['action_type'] === 'DELETE') {
            return "Kontrak {$kontrakNumber} dihapus dari sistem";
        }
        
        return "Kontrak {$kontrakNumber} " . strtolower($row['action_description']);
    }

    private function generateKontrakSpesifikasiDescription($row, $db)
    {
        // Get spek_kode and kontrak info
        $spekQuery = $db->table('kontrak_spesifikasi ks')
            ->select('ks.spek_kode, k.nomor_kontrak, k.nama_customer')
            ->join('kontrak k', 'k.id = ks.kontrak_id', 'left')
            ->where('ks.id', $row['record_id'])
            ->get();
        $spekData = $spekQuery->getRowArray();
        
        $spekKode = $spekData['spek_kode'] ?? 'SPEK#' . $row['record_id'];
        $kontrakNumber = $spekData['nomor_kontrak'] ?? 'Unknown';
        $customer = $spekData['nama_customer'] ?? '';
        
        if ($row['action_type'] === 'CREATE') {
            $customerInfo = $customer ? " untuk {$customer}" : "";
            return "Spesifikasi {$spekKode} ditambahkan ke kontrak {$kontrakNumber}{$customerInfo}";
        }
        
        if ($row['action_type'] === 'UPDATE') {
            $oldValues = json_decode($row['old_values'], true);
            $newValues = json_decode($row['new_values'], true);
            
            $changes = [];
            
            if (isset($newValues['jumlah_dibutuhkan']) && $newValues['jumlah_dibutuhkan'] !== ($oldValues['jumlah_dibutuhkan'] ?? null)) {
                $changes[] = "mengubah jumlah dari {$oldValues['jumlah_dibutuhkan']} ke {$newValues['jumlah_dibutuhkan']} unit";
            }
            
            if (isset($newValues['harga_per_unit_bulanan']) && $newValues['harga_per_unit_bulanan'] !== ($oldValues['harga_per_unit_bulanan'] ?? null)) {
                $oldPrice = number_format($oldValues['harga_per_unit_bulanan'] ?? 0, 0, ',', '.');
                $newPrice = number_format($newValues['harga_per_unit_bulanan'], 0, ',', '.');
                $changes[] = "mengubah harga bulanan dari Rp {$oldPrice} ke Rp {$newPrice}";
            }
            
            if (isset($newValues['tipe_unit_id']) && $newValues['tipe_unit_id'] !== ($oldValues['tipe_unit_id'] ?? null)) {
                $changes[] = "mengubah tipe unit";
            }
            
            if (!empty($changes)) {
                return "Spesifikasi {$spekKode} " . implode(' dan ', $changes);
            }
        }
        
        if ($row['action_type'] === 'DELETE') {
            return "Spesifikasi {$spekKode} dihapus dari kontrak {$kontrakNumber}";
        }
        
        return "Spesifikasi {$spekKode} " . strtolower($row['action_description']);
    }

    private function generateDeliveryDescription($row, $db)
    {
        // Get delivery instruction number
        $diQuery = $db->table('delivery_instructions')->select('nomor_di')->where('id', $row['record_id'])->get();
        $diData = $diQuery->getRowArray();
        $diNumber = $diData['nomor_di'] ?? 'DI#' . $row['record_id'];
        
        if ($row['action_type'] === 'CREATE') {
            return "Delivery Instruction {$diNumber} dibuat untuk pengiriman unit";
        }
        
        if ($row['action_type'] === 'UPDATE') {
            $oldValues = json_decode($row['old_values'], true);
            $newValues = json_decode($row['new_values'], true);
            
            if (isset($newValues['status']) && isset($oldValues['status']) && $newValues['status'] !== $oldValues['status']) {
                return "DI {$diNumber} mengubah status dari {$oldValues['status']} ke {$newValues['status']}";
            }
        }
        
        return "DI {$diNumber} " . strtolower($row['action_description']);
    }

    private function generateInventoryDescription($row, $db)
    {
        // Get unit number
        $unitQuery = $db->table('inventory_unit')->select('no_unit')->where('id_inventory_unit', $row['record_id'])->get();
        $unitData = $unitQuery->getRowArray();
        $unitNumber = $unitData['no_unit'] ?? $row['record_id'];
        
        if ($row['action_type'] === 'CREATE') {
            return "Unit #{$unitNumber} ditambahkan ke inventory";
        }
        
        if ($row['action_type'] === 'UPDATE') {
            $oldValues = json_decode($row['old_values'], true);
            $newValues = json_decode($row['new_values'], true);
            
            $changes = [];
            
            if (isset($newValues['spk_id']) && $newValues['spk_id'] !== ($oldValues['spk_id'] ?? null)) {
                $changes[] = "dihubungkan dengan SPK";
            }
            
            if (isset($newValues['delivery_instruction_id']) && $newValues['delivery_instruction_id'] !== ($oldValues['delivery_instruction_id'] ?? null)) {
                $changes[] = "dihubungkan dengan Delivery Instruction";
            }
            
            if (isset($newValues['status_unit_id']) && $newValues['status_unit_id'] !== ($oldValues['status_unit_id'] ?? null)) {
                $changes[] = "mengubah status unit";
            }
            
            if (isset($newValues['lokasi_unit']) && $newValues['lokasi_unit'] !== ($oldValues['lokasi_unit'] ?? null)) {
                $changes[] = "dipindah ke lokasi {$newValues['lokasi_unit']}";
            }
            
            if (!empty($changes)) {
                return "Unit #{$unitNumber} " . implode(', ', $changes);
            }
        }
        
        return "Unit #{$unitNumber} " . strtolower($row['action_description']);
    }

    private function getUnitNumber($unitId, $db)
    {
        if (!$unitId) return 'Unknown';
        
        $unitQuery = $db->table('inventory_unit')->select('no_unit')->where('id_inventory_unit', $unitId)->get();
        $unitData = $unitQuery->getRowArray();
        
        return $unitData['no_unit'] ?? "#{$unitId}";
    }

    private function getActionBadgeColor($action)
    {
        switch ($action) {
            case 'CREATE': return 'success';
            case 'UPDATE': return 'warning';
            case 'DELETE': return 'danger';
            case 'PRINT': return 'info';
            case 'DOWNLOAD': return 'secondary';
            default: return 'primary';
        }
    }

    private function getImpactBadgeColor($impact)
    {
        switch ($impact) {
            case 'LOW': return 'success';
            case 'MEDIUM': return 'warning';
            case 'HIGH': return 'danger';
            case 'CRITICAL': return 'dark';
            default: return 'secondary';
        }
    }
}
