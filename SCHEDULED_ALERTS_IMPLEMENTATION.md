# 🚀 IMPLEMENTASI SCHEDULED ALERTS UNTUK SHARED HOSTING

**Tanggal:** 19 Desember 2025  
**Solusi:** Hybrid Approach (Pseudo-CRON + Trigger-based)

---

## 🎯 KONSEP: 3 METODE BERBEDA

### **Metode 1: Pseudo-CRON (Background Scheduler)** ⭐ RECOMMENDED
**Untuk:** Invoice Overdue, Contract Expiry, PMPS  
**Cara Kerja:** Check saat ada user activity  
**Kelebihan:**
- ✅ Tidak butuh CRON server
- ✅ Ringan (hanya 1 task per request)
- ✅ Otomatis jalan saat ada traffic
- ✅ Cocok untuk shared hosting

**Kekurangan:**
- ⚠️ Tidak jalan jika tidak ada user activity
- ⚠️ Delay maksimal = interval check

---

### **Metode 2: Trigger-Based (Real-time)** ⭐⭐ PALING EFISIEN
**Untuk:** Sparepart Low Stock, Unit Status Change  
**Cara Kerja:** Check langsung saat action terjadi  
**Kelebihan:**
- ✅ Real-time, tidak ada delay
- ✅ Paling ringan (hanya check saat perlu)
- ✅ Tidak butuh scheduler
- ✅ Akurasi 100%

**Kekurangan:**
- ⚠️ Harus implement di setiap action terkait

---

### **Metode 3: External Webhook (Optional)** 
**Untuk:** Backup jika traffic rendah  
**Cara Kerja:** External service hit endpoint  
**Kelebihan:**
- ✅ Jalan walaupun tidak ada traffic
- ✅ Free tier tersedia
- ✅ Reliable

**Kekurangan:**
- ⚠️ Butuh setup external service
- ⚠️ Expose endpoint ke public

---

## 📋 IMPLEMENTASI LENGKAP

### **STEP 1: Setup Pseudo-CRON**

#### 1.1. Buat Library (Sudah dibuat: `app/Libraries/BackgroundScheduler.php`)

Fitur:
- Check invoice overdue setiap 1 jam
- Check sparepart stock setiap 30 menit
- Check contract expiry setiap 24 jam
- Check PMPS setiap 6 jam

#### 1.2. Integrate ke BaseController

Edit `app/Controllers/BaseController.php`:

```php
<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $request;
    protected $helpers = [];

    public function initController(
        RequestInterface $request, 
        ResponseInterface $response, 
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        
        // Load helpers
        $this->helpers = array_merge($this->helpers, ['notification']);
        
        // Run background scheduler (Pseudo-CRON)
        // Hanya untuk web request (bukan CLI)
        if (!$request instanceof CLIRequest) {
            $this->runBackgroundScheduler();
        }
    }
    
    /**
     * Run background scheduler pada setiap request
     * Ringan karena hanya 1 task per request
     */
    private function runBackgroundScheduler()
    {
        try {
            // Random 10% chance untuk menjalankan scheduler
            // Ini untuk mengurangi overhead, tidak setiap request
            if (rand(1, 10) === 1) {
                $scheduler = new \App\Libraries\BackgroundScheduler();
                $scheduler->runIfNeeded();
            }
        } catch (\Exception $e) {
            // Silent fail, jangan ganggu user experience
            log_message('error', '[BaseController] Scheduler error: ' . $e->getMessage());
        }
    }
}
```

**Penjelasan:**
- Random 10% = scheduler jalan rata-rata 1x setiap 10 requests
- Jika website ada 100 visitors/jam → scheduler jalan ~10x/jam
- Sangat ringan, tidak terasa oleh user

---

### **STEP 2: Implement Trigger-Based Notifications**

#### 2.1. Sparepart Low Stock (Trigger saat update)

Edit `app/Controllers/Warehouse.php` - function `updateInventorySparepart()`:

```php
public function updateInventorySparepart($id)
{
    $inventoryModel = new InventorySparepartModel();
    $sparepartModel = new \App\Models\SparepartModel();
    
    // Get old data
    $oldData = $inventoryModel->find($id);
    
    $data = [
        'stok' => $this->request->getPost('stok'),
        'lokasi_rak' => $this->request->getPost('lokasi_rak')
    ];

    if ($inventoryModel->update($id, $data)) {
        // Get sparepart details with minimum stock
        $sparepart = $inventoryModel
            ->select('inventory_spareparts.*, s.kode, s.desc_sparepart, s.minimum_stock')
            ->join('sparepart s', 's.id_sparepart = inventory_spareparts.sparepart_id', 'left')
            ->find($id);
        
        if ($sparepart) {
            helper('notification');
            
            // Send notification: Sparepart Used
            notify_sparepart_used([
                'id' => $id,
                'nama_sparepart' => $sparepart['desc_sparepart'] ?? '',
                'kode' => $sparepart['kode'] ?? '',
                'qty_before' => $oldData['stok'],
                'qty_after' => $data['stok'],
                'lokasi' => $data['lokasi_rak'],
                'updated_by' => session('username') ?? session('user_id'),
                'url' => base_url('/warehouse/spareparts')
            ]);
            
            // ⭐ TRIGGER: Check stock level dan kirim alert
            $newStock = (int)$data['stok'];
            $minStock = (int)($sparepart['minimum_stock'] ?? 0);
            
            // Hanya kirim alert jika ada minimum stock yang di-set
            if ($minStock > 0) {
                if ($newStock == 0) {
                    // OUT OF STOCK - CRITICAL!
                    notify_sparepart_out_of_stock([
                        'id' => $id,
                        'nama_sparepart' => $sparepart['desc_sparepart'],
                        'kode' => $sparepart['kode'],
                        'lokasi' => $data['lokasi_rak'],
                        'url' => base_url('/warehouse/spareparts/' . $id)
                    ]);
                } elseif ($newStock <= $minStock) {
                    // LOW STOCK - WARNING
                    notify_sparepart_low_stock([
                        'id' => $id,
                        'nama_sparepart' => $sparepart['desc_sparepart'],
                        'kode' => $sparepart['kode'],
                        'stok_saat_ini' => $newStock,
                        'minimum_stock' => $minStock,
                        'lokasi' => $data['lokasi_rak'],
                        'url' => base_url('/warehouse/spareparts/' . $id)
                    ]);
                }
            }
        }
        
        return $this->response->setJSON(['success' => true, 'message' => 'Stok berhasil diperbarui.']);
    } else {
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui stok.', 'errors' => $inventoryModel->errors()]);
    }
}
```

**Keuntungan Trigger:**
- Real-time alert (langsung saat stock berubah)
- Tidak perlu polling database setiap 30 menit
- Lebih akurat dan efisien

---

#### 2.2. Invoice Status Change (Trigger saat payment)

Edit `app/Controllers/Finance.php` - function `updatePaymentStatus()`:

```php
public function updatePaymentStatus($id)
{
    try {
        // Get existing invoice
        $invoiceModel = new \App\Models\InvoiceModel();
        $invoice = $invoiceModel->find($id);
        
        if (!$invoice) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invoice not found'
            ])->setStatusCode(404);
        }
        
        // Get POST data
        $statusData = $this->request->getPost();
        $newStatus = $statusData['status'] ?? 'Paid';
        $oldStatus = $invoice['status'];
        
        // Update invoice
        $updateData = [
            'status' => $newStatus,
            'payment_date' => $statusData['payment_date'] ?? date('Y-m-d'),
            'paid_amount' => $statusData['paid_amount'] ?? $invoice['total_amount']
        ];
        
        $invoiceModel->update($id, $updateData);
        
        // Send notification
        helper('notification');
        
        if ($newStatus === 'Paid') {
            // Payment received - GOOD NEWS!
            notify_payment_received([
                'id' => $id,
                'invoice_number' => $invoice['invoice_number'],
                'customer_name' => $invoice['customer_name'],
                'amount' => $updateData['paid_amount'],
                'payment_date' => $updateData['payment_date'],
                'updated_by' => session('username') ?? session('user_id'),
                'url' => base_url('/finance/invoices/' . $id)
            ]);
            
            // ⭐ Hapus dari overdue notification jika ada
            // (opsional - mark related overdue notif as resolved)
        }
        
        log_message('info', "Payment status updated for {$invoice['invoice_number']}: {$oldStatus} → {$newStatus}");
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'invoice_number' => $invoice['invoice_number'],
            'new_status' => $newStatus,
            'token' => csrf_hash()
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error updating payment status: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to update payment status: ' . $e->getMessage(),
            'token' => csrf_hash()
        ])->setStatusCode(500);
    }
}
```

---

### **STEP 3: External Webhook (Optional Backup)**

Jika traffic website sangat rendah, gunakan external webhook sebagai backup:

#### 3.1. Buat Controller untuk Webhook

```php
<?php
// app/Controllers/Cron.php

namespace App\Controllers;

class Cron extends BaseController
{
    /**
     * Webhook endpoint untuk external scheduler
     * URL: https://yoursite.com/cron/run-scheduler
     * 
     * Security: Gunakan secret token
     */
    public function runScheduler()
    {
        // Verify secret token untuk security
        $token = $this->request->getGet('token');
        $expectedToken = getenv('CRON_SECRET_TOKEN'); // Set di .env
        
        if ($token !== $expectedToken) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Invalid token'
            ]);
        }
        
        try {
            // Force run scheduler
            $scheduler = new \App\Libraries\BackgroundScheduler();
            $scheduler->runIfNeeded();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Scheduler executed successfully',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            log_message('error', '[Cron] Scheduler error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
```

#### 3.2. Setup External Service (Gratis)

**Pilihan 1: cron-job.org** (Free)
1. Daftar di https://cron-job.org
2. Create new job
3. URL: `https://yoursite.com/cron/run-scheduler?token=YOUR_SECRET`
4. Schedule: Every 1 hour
5. Done!

**Pilihan 2: EasyCron** (Free)
- 20 jobs gratis per bulan
- Setup sama seperti cron-job.org

**Pilihan 3: UptimeRobot** (Free)
- Untuk monitoring + trigger
- Check URL setiap 5 menit
- Gratis untuk 50 monitors

---

## 🎯 REKOMENDASI IMPLEMENTASI

### **Untuk Website Anda (Shared Hosting):**

#### **STEP 1: Implement Sekarang (Priority)**

1. ✅ **BackgroundScheduler.php** (Sudah dibuat)
2. ✅ **Integrate ke BaseController** (Edit basecontroller)
3. ✅ **Trigger-based untuk Sparepart** (Edit Warehouse.php)
4. ⏸️ **External Webhook** (Optional, untuk backup)

#### **STEP 2: Test (Besok)**

```
1. Test Pseudo-CRON:
   - Browse website beberapa kali
   - Check writable/scheduler_lock.json
   - Check log untuk "[BackgroundScheduler]"

2. Test Trigger:
   - Update sparepart stock ke 0
   - Verify notification out_of_stock terkirim
   - Update stock ke <= minimum
   - Verify notification low_stock terkirim

3. Monitor Performance:
   - Check response time (harus tetap cepat)
   - Check memory usage
   - Check log untuk errors
```

---

## 📊 COMPARISON TABLE

| Fitur | Pseudo-CRON | Trigger-Based | CRON Server | External Webhook |
|-------|-------------|---------------|-------------|------------------|
| **Setup** | ✅ Mudah | ✅ Mudah | ❌ Butuh akses | ⚠️ Setup account |
| **Performance** | ✅ Ringan | ✅ Paling ringan | ✅ Dedicated | ✅ Tidak load server |
| **Real-time** | ⚠️ Delay 30-60min | ✅ Instant | ✅ Reliable | ⚠️ Delay 5-60min |
| **Shared Hosting** | ✅ Compatible | ✅ Compatible | ❌ Tidak support | ✅ Compatible |
| **Traffic Dependency** | ⚠️ Butuh traffic | ✅ Not needed | ✅ Not needed | ✅ Not needed |
| **Cost** | ✅ Free | ✅ Free | ⚠️ Paid | ✅ Free tier |

---

## 🎯 KESIMPULAN

**Kombinasi TERBAIK untuk Shared Hosting Anda:**

1. **Pseudo-CRON (BackgroundScheduler)** untuk:
   - Invoice overdue check
   - Contract expiry check
   - PMPS check

2. **Trigger-Based** untuk:
   - Sparepart low stock (real-time saat update)
   - Unit status change
   - Payment received

3. **External Webhook (Optional)** untuk:
   - Backup jika traffic sangat rendah
   - Night-time checks

**Overhead:** Sangat minimal!
- Random 10% execution = ~1-2ms per request
- Hanya 1 task per execution
- Tidak terasa sama sekali oleh user

---

## 📁 FILES TO MODIFY

| File | Action | Priority |
|------|--------|----------|
| app/Libraries/BackgroundScheduler.php | ✅ Created | Done |
| app/Controllers/BaseController.php | ⏸️ Add scheduler call | High |
| app/Controllers/Warehouse.php | ⏸️ Add trigger check | High |
| app/Controllers/Finance.php | ⏸️ Add trigger check | Medium |
| app/Controllers/Cron.php | ⏸️ Create (optional) | Low |
| .env | ⏸️ Add CRON_SECRET_TOKEN | Low |

---

**Ready to implement! Lanjut edit BaseController dan Warehouse.php?** 🚀
