# SPK Sparepart Tracking Flow

> **📢 UPDATE:** Backend implementation complete! See `SPK_SPAREPART_BACKEND_COMPLETE.md` for:
> - ✅ Sparepart planning during SPK creation
> - ✅ Print sparepart request for warehouse
> - ✅ Add additional spareparts during verification
> - ⏳ Frontend integration guide

## ✅ Struktur Database (SUDAH LENGKAP)

### Tabel: `spk_spareparts`
**Purpose:** Track sparepart planning dan actual usage untuk SPK

**Kolom Penting:**
- `sparepart_code` - NULL untuk manual entries (sparepart tanpa kode resmi)
- `sparepart_name` - Nama sparepart
- `item_type` - 'sparepart' atau 'tool'
- `quantity_brought` - Jumlah yang diambil dari warehouse
- `quantity_used` - Jumlah yang benar-benar digunakan
- `is_from_warehouse` - 1=Warehouse, 0=Bekas/Kanibal
- `source_type` - 'WAREHOUSE', 'BEKAS', atau 'KANIBAL'
- `sparepart_validated` - 1=Sudah divalidasi, 0=Masih rencana

### Tabel: `spk_sparepart_returns`
**Purpose:** Track pengembalian sparepart (brought > used)

**Status:**
- `PENDING` - Menunggu konfirmasi warehouse
- `CONFIRMED` - Sudah dikonfirmasi diterima kembali

---

## ✅ Flow Tracking (SAMA SEPERTI WORK ORDER)

### 1. SPK Creation/Planning
**File:** `app/Controllers/Service.php` atau Marketing SPK creation
- User membuat SPK baru
- Input sparepart yang akan digunakan (planning)
- Data tersimpan ke `spk_spareparts` dengan:
  - `sparepart_validated = 0` (masih rencana)
  - `quantity_brought` = jumlah yang direncanakan
  - `quantity_used = 0` (belum digunakan)

**Check Point:** 
```sql
-- Cek apakah SPK creation sudah menyimpan sparepart planning
SELECT COUNT(*) FROM spk_spareparts WHERE spk_id = [SPK_ID_BARU];
```

### 2. SPK Execution/Validation
**File:** `app/Controllers/Service.php::validateSpareparts()`
- Teknisi menggunakan sparepart selama pengerjaan SPK
- Update `quantity_used` dengan jumlah actual
- Set `sparepart_validated = 1`
- Jika `quantity_used < quantity_brought`, buat entry di `spk_sparepart_returns`

**Check Point:**
```sql
-- Cek apakah validation sudah berjalan
SELECT * FROM spk_spareparts 
WHERE spk_id = [SPK_ID] AND sparepart_validated = 1;

-- Cek returns yang pending
SELECT * FROM spk_sparepart_returns 
WHERE spk_id = [SPK_ID] AND status = 'PENDING';
```

### 3. Display di Warehouse Sparepart Usage Page
**File:** `app/Views/warehouse/sparepart_usage.php`
- Filter "SPK" akan menampilkan data dari `spk_spareparts`
- Tab **Usage** - Semua SPK dengan sparepart planning
- Tab **Returns** - SPK dengan pending/confirmed returns  
- Tab **Non-Warehouse** - SPK dengan manual entries (sparepart_code IS NULL)

---

## 🔍 Verification Checklist

### A. Database Structure ✅
- [x] Tabel `spk_spareparts` exists dengan kolom lengkap
- [x] Tabel `spk_sparepart_returns` exists
- [x] Foreign keys terpasang ke `spk` table
- [x] Indexes untuk performance (spk_id, sparepart_code, source_type)

### B. Backend Endpoints ✅
- [x] `service/get-spk-sparepart-usage` - DataTables untuk Usage tab
- [x] `service/get-spk-sparepart-returns` - DataTables untuk Returns tab  
- [x] `service/get-spk-spareparts/:id` - Detail spareparts per SPK
- [x] `warehouse/sparepart-usage/get-manual-entries-data?source_type=SPK` - Manual entries

### C. Frontend Integration ✅
- [x] Filter toggle (Work Order / SPK)
- [x] Tab Usage reload dengan SPK data
- [x] Tab Returns reload dengan SPK data
- [x] Tab Non-Warehouse reload dengan SPK manual entries
- [x] Badge count update dynamic
- [x] Expand/collapse spareparts detail untuk SPK

### D. SPK Creation Flow ⚠️ PERLU VERIFIKASI
**Action Required:** Pastikan saat SPK dibuat/diupdate, data sparepart tersimpan ke `spk_spareparts`

**File yang Perlu Dicek:**
1. `app/Controllers/Marketing.php::createSPK()` atau sejenisnya
2. `app/Controllers/Service.php` - SPK CRUD methods
3. SPK form submission handler

**Test Case:**
```php
// Create test SPK with spareparts
$spkId = // ID SPK baru
$spareparts = [
    ['code' => 'SP-001', 'name' => 'Ban Forklift', 'qty' => 4],
    ['code' => null, 'name' => 'Oli Manual Entry', 'qty' => 2] // Manual entry
];

// Verify data tersimpan
$saved = $db->table('spk_spareparts')->where('spk_id', $spkId)->get()->getResultArray();
// Expected: 2 rows
```

---

## 🎯 Expected Behavior

### Scenario 1: SPK Baru dengan Planning
**Given:** User create SPK baru dengan 3 sparepart
**When:** SPK tersimpan ke database
**Then:**
- 3 rows terinsert ke `spk_spareparts`
- `sparepart_validated = 0`
- Muncul di tab Usage dengan filter SPK
- Manual entries (code NULL) muncul di tab Non-Warehouse

### Scenario 2: SPK Validation
**Given:** SPK sudah ada dengan 3 sparepart planning
**When:** Teknisi validate usage (2 digunakan, 1 return)
**Then:**
- `spk_spareparts` updated: `sparepart_validated = 1`, `quantity_used` = actual
- 1 row terinsert ke `spk_sparepart_returns` dengan status PENDING
- Muncul di tab Returns (filter SPK)

### Scenario 3: Filter Switching
**Given:** User di halaman Warehouse > Sparepart Usage
**When:** Klik filter "SPK"
**Then:**
- Tab Usage reload → Tampilkan SPK dengan spareparts
- Tab Returns reload → Tampilkan SPK returns (filter source_type=SPK)
- Tab Non-Warehouse reload → Tampilkan SPK manual entries
- Badge update sesuai jumlah data SPK

---

## 📝 Next Steps

1. **Verify SPK Creation Flow**
   - Test create SPK baru dengan sparepart planning
   - Check apakah data tersimpan ke `spk_spareparts`
   
2. **Verify SPK Validation Flow**  
   - Test validate SPK sparepart usage
   - Check apakah returns tercatat dengan benar

3. **Test Filter Integration**
   - Switch filter Work Order ↔ SPK
   - Verify semua tabs menampilkan data yang benar
   - Check badge counts update dengan benar

4. **Performance Testing**
   - Test dengan 100+ SPK entries
   - Verify DataTables pagination & search work properly
   - Check query performance (indexes)

---

## 🔗 Related Files

**Controllers:**
- `app/Controllers/Service.php` - SPK sparepart methods
- `app/Controllers/Warehouse/SparepartUsageController.php` - Warehouse tracking

**Views:**
- `app/Views/warehouse/sparepart_usage.php` - Main tracking UI
- `app/Views/service/spk_service.php` - SPK management

**Migrations:**
- `databases/migrations/20260314_create_spk_sparepart_management.sql`

**Routes:**
- `service/get-spk-sparepart-usage` (POST)
- `service/get-spk-sparepart-returns` (POST)
- `service/get-spk-spareparts/:id` (GET)
