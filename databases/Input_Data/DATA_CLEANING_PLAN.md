# RENCANA PEMBERSIHAN DATA KONTRAK

## MASALAH YANG DITEMUKAN

1. **Gap data kontrak**: DB hanya 579, seharusnya 1087 (kurang 508)
2. **kontrak_unit sudah diimport**: 1949 rows, kemungkinan ada yang merujuk kontrak_id tidak exist
3. **Dua sumber data berbeda**:
   - `kontrak.csv` (ACC) - master kontrak
   - `data_marketing.csv` (Marketing) - detail unit per kontrak

## STRATEGI PEMBERSIHAN (Opsi 1: Full Reset)

### STEP 1: Backup & Truncate
```sql
-- Backup data existing
CREATE TABLE kontrak_backup_20260305 AS SELECT * FROM kontrak;
CREATE TABLE kontrak_unit_backup_20260305 AS SELECT * FROM kontrak_unit;

-- Truncate (keep structure)
TRUNCATE TABLE kontrak_unit;
TRUNCATE TABLE kontrak;
```

### STEP 2: Import Master Kontrak
- Source: `kontrak.csv` (1087 rows)
- Target: `kontrak` table
- Validasi:
  - customer_id must exist in customers
  - customer_location_id must exist in customer_locations
  - Dates format conversion (DD/MM/YYYY → YYYY-MM-DD)
  - NULL handling

### STEP 3: Reconcile & Import kontrak_unit
Ada 2 opsi:

**Opsi A: Gunakan kontrak_unit.csv existing**
- Sudah diimport (1949 rows)
- Perlu validasi: semua kontrak_id exist di kontrak table
- Perlu validasi: semua unit_id exist di inventory_unit

**Opsi B: Generate dari data_marketing.csv**
- 2076 rows dengan detail lengkap
- Perlu mapping:
  - Customer name → customer_id
  - Lokasi → customer_location_id
  - No Unit → unit_id (dari inventory_unit)
  - KONTRAK → nomor kontrak → kontrak_id
- Lebih akurat karena from source

### STEP 4: Cross-validation
```sql
-- Check orphaned kontrak_unit (kontrak_id not exist)
SELECT ku.* FROM kontrak_unit ku 
LEFT JOIN kontrak k ON ku.kontrak_id = k.id 
WHERE k.id IS NULL;

-- Check unit count per kontrak
SELECT k.id, k.no_kontrak, COUNT(ku.id) as unit_count
FROM kontrak k
LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id
GROUP BY k.id
ORDER BY unit_count DESC;

-- Check kontrak without units
SELECT k.* FROM kontrak k
LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id
WHERE ku.id IS NULL;
```

## STRATEGI PEMBERSIHAN (Opsi 2: Incremental Import)

### STEP 1: Identify Missing Kontrak
```sql
-- Export existing kontrak IDs
SELECT id FROM kontrak ORDER BY id;
-- Compare dengan kontrak.csv
-- Import only missing 508 records
```

### STEP 2: Update kontrak_unit References
- Validate all kontrak_id exist
- Fix any broken references

## PERTANYAAN UNTUK USER

1. **Mana sumber data yang lebih akurat?**
   - kontrak.csv (1087) vs data yang sudah di DB (579)?
   
2. **Data marketing vs kontrak_unit.csv - mana yang dipakai?**
   - data_marketing.csv memiliki detail lebih lengkap (unit info)
   - kontrak_unit.csv lebih simple, sudah cleaned?

3. **Apakah boleh truncate (hapus semua) dan import ulang?**
   - Atau harus incremental (tambah yang kurang saja)?

4. **Data temporal (created_at, updated_at) penting?**
   - Atau bisa di-generate waktu import?

## REKOMENDASI

**GUNAKAN OPSI 1 (Full Reset)** karena:
- Gap terlalu besar (508 kontrak missing)
- Lebih clean daripada patching
- Data masih development phase
- Bisa validasi 100% dari source

**SEQUENCE:**
1. Backup existing tables
2. Truncate kontrak + kontrak_unit
3. Import kontrak.csv → kontrak table
4. Import data_marketing.csv → kontrak_unit (dengan mapping)
5. Validate referential integrity
6. Verify totals match source
