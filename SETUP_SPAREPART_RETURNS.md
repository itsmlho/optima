# Setup Tabel Sparepart Returns

## ⚠️ Error: Table 'work_order_sparepart_returns' doesn't exist

Tabel database belum dibuat. Ikuti langkah berikut:

## 📋 Cara Setup

### Opsi 1: Via phpMyAdmin (Recommended)

1. Buka **phpMyAdmin** (http://localhost/phpmyadmin)
2. Pilih database **`optima_ci`**
3. Klik tab **SQL**
4. Copy-paste SQL berikut:

```sql
CREATE TABLE IF NOT EXISTS `work_order_sparepart_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_id` int(11) NOT NULL,
  `work_order_sparepart_id` int(11) DEFAULT NULL,
  `sparepart_code` varchar(50) NOT NULL,
  `sparepart_name` varchar(255) NOT NULL,
  `quantity_brought` int(11) NOT NULL,
  `quantity_used` int(11) NOT NULL DEFAULT 0,
  `quantity_return` int(11) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED') DEFAULT 'PENDING',
  `return_notes` text DEFAULT NULL,
  `confirmed_by` int(11) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_id` (`work_order_id`),
  KEY `work_order_sparepart_id` (`work_order_sparepart_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

5. Klik **Go** atau **Execute**
6. Refresh halaman Sparepart Returns

### Opsi 2: Via MySQL Command Line

```bash
mysql -u root -p optima_ci < app/Database/SQL/work_order_sparepart_returns.sql
```

### Opsi 3: Via CodeIgniter Migration

```bash
php spark migrate
```

## ✅ Verifikasi

Setelah setup, cek apakah tabel sudah dibuat:

```sql
SHOW TABLES LIKE 'work_order_sparepart_returns';
DESCRIBE work_order_sparepart_returns;
```

## 📁 File SQL

File SQL tersedia di:
- `app/Database/SQL/work_order_sparepart_returns.sql`
- `app/Database/Migrations/2025_01_15_000001_CreateWorkOrderSparepartReturnsTable.php`

## 🔄 Setelah Setup

1. Refresh halaman **Warehouse → Sparepart Returns**
2. Sistem akan otomatis mendeteksi tabel sudah ada
3. Mulai gunakan fitur pengembalian sparepart

