# ⚙️ FASE 2: STANDARDISASI CHARSET & COLLATION - OPTIMA CI

## 🎯 Target Standardisasi

### 1. Analisis Charset/Collation Saat Ini
```sql
-- Cek tabel dengan charset/collation berbeda-beda
SELECT 
    TABLE_NAME,
    TABLE_COLLATION,
    ENGINE
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'optima_ci' 
    AND TABLE_COLLATION IS NOT NULL
ORDER BY TABLE_COLLATION, TABLE_NAME;

-- Cek kolom dengan charset/collation berbeda
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    CHARACTER_SET_NAME,
    COLLATION_NAME,
    DATA_TYPE
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_ci' 
    AND CHARACTER_SET_NAME IS NOT NULL
    AND COLLATION_NAME != 'utf8mb4_unicode_ci'
ORDER BY TABLE_NAME, COLUMN_NAME;
```

### 2. Standar Baru: utf8mb4_unicode_ci
**Alasan memilih utf8mb4_unicode_ci:**
- ✅ Support penuh Unicode (emoji, karakter internasional)
- ✅ Sorting dan comparison yang lebih akurat
- ✅ Case-insensitive yang konsisten
- ✅ Standar modern MySQL/MariaDB

### 3. Script Konversi Charset
```sql
-- Set database default charset
ALTER DATABASE `optima_ci` 
CHARACTER SET = utf8mb4 
COLLATE = utf8mb4_unicode_ci;

-- Konversi semua tabel ke utf8mb4_unicode_ci
-- STEP 1: Tabel Master/Reference (kecil)
ALTER TABLE `activity_types` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `departments` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `divisions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `positions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `roles` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `permissions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- STEP 2: Tabel Users & Auth
ALTER TABLE `users` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_sessions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `user_otp` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `login_attempts` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- STEP 3: Tabel Master Data
ALTER TABLE `customers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `suppliers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `employees` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- STEP 4: Tabel Inventory (HATI-HATI - DATA BESAR)
-- Lakukan satu per satu dengan monitoring
ALTER TABLE `inventory_unit` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `inventory_attachment` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `sparepart` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- STEP 5: Tabel Transaksi (LAKUKAN DI MAINTENANCE WINDOW)
ALTER TABLE `purchase_orders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `po_units` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `po_sparepart_items` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `delivery_instructions` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `work_orders` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `spk` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- STEP 6: Tabel Log & Audit (BISA DILAKUKAN BATCH)
ALTER TABLE `system_activity_log` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `notifications` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 4. Verifikasi Konversi
```sql
-- Pastikan semua tabel sudah terkonversi
SELECT 
    TABLE_NAME,
    TABLE_COLLATION,
    COUNT(*) as total_rows
FROM information_schema.TABLES t
LEFT JOIN information_schema.TABLE_STATISTICS ts ON t.TABLE_NAME = ts.TABLE_NAME
WHERE TABLE_SCHEMA = 'optima_ci' 
    AND TABLE_COLLATION != 'utf8mb4_unicode_ci'
    AND TABLE_TYPE = 'BASE TABLE'
GROUP BY TABLE_NAME, TABLE_COLLATION;

-- Cek apakah ada kolom yang belum terkonversi
SELECT 
    TABLE_NAME,
    COLUMN_NAME,
    COLLATION_NAME
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'optima_ci' 
    AND CHARACTER_SET_NAME IS NOT NULL
    AND COLLATION_NAME != 'utf8mb4_unicode_ci'
ORDER BY TABLE_NAME, COLUMN_NAME;
```

## ⚠️ PERHATIAN KHUSUS

### Risiko & Mitigasi:
1. **Data Size Bertambah**: utf8mb4 bisa menambah ukuran 25-30%
2. **Downtime**: Konversi tabel besar membutuhkan waktu lama
3. **Index Rebuild**: Semua index akan di-rebuild otomatis

### Strategi Eksekusi:
1. **Maintenance Window**: Lakukan di luar jam kerja
2. **Monitoring**: Pantau space disk dan performance
3. **Rollback Plan**: Siapkan script rollback jika ada masalah
4. **Testing**: Test aplikasi setelah konversi

## 🎯 Expected Results
- Konsistensi charset/collation di seluruh database
- Support penuh untuk karakter Unicode
- Performa query yang lebih konsisten
- Compatibilitas lebih baik dengan aplikasi modern