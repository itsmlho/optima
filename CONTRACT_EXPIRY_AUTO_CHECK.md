# 📅 CONTRACT EXPIRY AUTO-CHECK
**Notification Otomatis untuk Kontrak yang Akan Expired**

---

## ✅ SUDAH AKTIF!

System akan otomatis check kontrak yang akan expired **setiap 24 jam** tanpa perlu CRON!

---

## 🎯 CARA KERJA

### **Pseudo-CRON (Application-Level)**

```
User mengakses web → 10% chance scheduler berjalan → 
Check: Sudah 24 jam sejak last check? →
YA: Jalankan contract expiry check →
TIDAK: Skip (tunggu besok)
```

**Keuntungan:**
- ✅ Tidak perlu akses CRON server
- ✅ Cocok untuk shared hosting
- ✅ Ringan (hanya 10% request yang trigger)
- ✅ Tetap reliable (check setiap hari)

---

## 📋 FITUR YANG AKTIF

### **Contract Expiry Warning**

**Trigger:** Setiap 24 jam (otomatis)  
**Target:** Kontrak yang akan expired dalam **30 hari**  
**Notification:** customer_contract_expired

**Logic:**
```sql
SELECT * FROM kontrak k
WHERE k.status = 'Aktif'
AND k.tanggal_berakhir >= NOW()
AND k.tanggal_berakhir <= NOW() + INTERVAL 30 DAY
```

**Data Notification:**
- Contract Number
- Customer Name
- Location Name
- Days Until Expiry (countdown)
- Contract Value
- Link ke detail kontrak

---

## 🔍 CARA MONITORING

### 1. Check Log File
```bash
# Windows
type writable\logs\log-2025-12-19.php | findstr "Scheduler"

# Linux/Mac
tail -f writable/logs/log-*.php | grep "Scheduler"
```

**Expected Output:**
```
INFO - [Scheduler] Contract expiry check completed - 3 notifications sent for 3 expiring contracts
INFO - [Scheduler] Contract expiry check skipped - last run: 2025-12-19 10:30:00
```

---

### 2. Check Database
```sql
-- Check kapan terakhir dijalankan
SELECT * FROM notifications 
WHERE trigger_event = 'customer_contract_expired'
ORDER BY created_at DESC 
LIMIT 10;

-- Check kontrak yang akan expired
SELECT 
    k.no_kontrak,
    c.customer_name,
    k.tanggal_berakhir,
    DATEDIFF(k.tanggal_berakhir, NOW()) as days_left,
    k.status
FROM kontrak k
LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
LEFT JOIN customers c ON c.id = cl.customer_id
WHERE k.status = 'Aktif'
AND k.tanggal_berakhir <= DATE_ADD(NOW(), INTERVAL 30 DAY)
ORDER BY k.tanggal_berakhir ASC;
```

---

### 3. Manual Test (Optional)
```php
// Buka browser console atau create test endpoint
helper('notification');
$result = check_contract_expiry_scheduled();
print_r($result);
```

**Expected Result:**
```php
Array (
    [status] => success
    [contracts_checked] => 3
    [notifications_sent] => 3
    [next_run] => 2025-12-20 10:30:00
)
```

---

## ⚙️ KONFIGURASI

### File yang Dimodifikasi:
1. ✅ `app/Helpers/notification_helper.php` - Function `check_contract_expiry_scheduled()`
2. ✅ `app/Controllers/BaseController.php` - Method `runBackgroundScheduler()`

### Cache Key:
- **Key:** `contract_expiry_last_check`
- **Duration:** 86400 seconds (24 hours)
- **Storage:** File cache (writable/cache/)

---

## 🎛️ FINE-TUNING (Optional)

### Ubah Warning Period (Default: 30 hari)

Edit di `notification_helper.php` line ~2919:
```php
// Ganti 30 jadi 60 untuk warning 60 hari sebelum expired
->where('k.tanggal_berakhir <=', date('Y-m-d', strtotime('+60 days')))
```

### Ubah Frequency Check (Default: 24 jam)

Edit di `notification_helper.php` line ~2908:
```php
// Ganti 86400 (24 jam) jadi 43200 (12 jam) untuk check 2x sehari
if ($lastRun && (time() - $lastRun) < 43200) {
```

### Ubah Probability Scheduler (Default: 10%)

Edit di `BaseController.php` line ~83:
```php
// Ganti rand(1, 10) jadi rand(1, 20) untuk 5% chance
if (rand(1, 20) === 1) {
```

---

## ✅ TESTING

### Test Scenario 1: Create Expiring Contract
```sql
-- Buat kontrak test yang akan expired 15 hari lagi
INSERT INTO kontrak (
    no_kontrak, 
    customer_location_id, 
    tanggal_mulai, 
    tanggal_berakhir,
    status,
    nilai_total
) VALUES (
    'TEST-2025-001',
    1, -- customer_location_id yang valid
    '2024-12-01',
    DATE_ADD(NOW(), INTERVAL 15 DAY), -- 15 hari dari sekarang
    'Aktif',
    50000000
);
```

### Test Scenario 2: Force Manual Check
```php
// Create test endpoint: app/Controllers/Test.php
public function testContractCheck()
{
    helper('notification');
    
    // Clear cache untuk force run
    cache()->delete('contract_expiry_last_check');
    
    // Run check
    $result = check_contract_expiry_scheduled();
    
    return $this->response->setJSON($result);
}
```

**Access:** `http://localhost/test/testContractCheck`

---

## 📊 PERFORMANCE IMPACT

| Metric | Value | Impact |
|--------|-------|--------|
| Probability | 10% | Low - only 1 in 10 requests |
| Frequency | 24 hours | Very Low - once per day max |
| Query Load | 1 query | Minimal - indexed query |
| Notification | Variable | Depends on expiring contracts |
| Cache Check | <1ms | Negligible |

**Kesimpulan:** Sangat ringan! Bahkan di shared hosting tidak akan terasa overhead-nya.

---

## 🔧 TROUBLESHOOTING

### Issue 1: Notification Tidak Terkirim
**Check:**
```sql
-- Apakah ada kontrak yang akan expired?
SELECT COUNT(*) FROM kontrak 
WHERE status = 'Aktif'
AND tanggal_berakhir <= DATE_ADD(NOW(), INTERVAL 30 DAY);
```

**Fix:**
- Pastikan ada kontrak yang memenuhi kriteria
- Check notification_rules untuk trigger `customer_contract_expired`
- Check log file untuk error

---

### Issue 2: Check Terlalu Sering
**Symptom:** Log menunjukkan check berjalan beberapa kali per hari

**Check:**
```bash
# Count berapa kali check hari ini
type writable\logs\log-2025-12-19.php | findstr "Contract expiry check completed" | find /c /v ""
```

**Fix:**
- Cache mungkin tidak tersave properly
- Check writable/cache/ folder permissions
- Pastikan cache driver aktif di config

---

### Issue 3: Scheduler Tidak Berjalan Sama Sekali
**Symptom:** Tidak ada log scheduler dalam 24 jam terakhir

**Check:**
- Apakah ada traffic ke website? (Scheduler triggered by user visits)
- Check BaseController apakah ada error

**Fix:**
```php
// Temporarily increase probability untuk testing
if (rand(1, 2) === 1) { // 50% chance instead of 10%
    helper('notification');
    check_contract_expiry_scheduled();
}
```

---

## 📚 NEXT STEPS (Optional - Future)

Jika perlu tambah fitur lain nanti:

1. **Invoice Overdue:** Check invoice lewat jatuh tempo
2. **PMPS Reminder:** Maintenance schedule reminder
3. **Sparepart Low Stock:** Alert stok sparepart menipis

*Tapi untuk sekarang, fokus contract expiry dulu saja!*

---

## ✅ SUMMARY

**Status:** ✅ **AKTIF & SIAP PRODUCTION**

**What's Implemented:**
- Contract expiry check every 24 hours
- Automatic notification 30 days before expiry
- Lightweight pseudo-CRON for shared hosting
- Cache-based to prevent duplicate checks

**What You Need to Do:**
1. Deploy ke production
2. Monitor log file hari pertama
3. Check notification bell setelah 24 jam
4. Verify contract owners receive alerts

**No Server Configuration Needed!** 🎉

---

*Last Updated: 19 Desember 2025*  
*Status: Production Ready*
