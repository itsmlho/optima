# Database Schema Reference - OPTIMA

## 📋 Quick Reference untuk Migrasi Laravel

File ini berisi ringkasan struktur database utama untuk memudahkan migrasi ke Laravel.

> **Catatan**: File SQL lengkap ada di `optima_db_24-11-25_FINAL.sql`

---

## 🗄️ Database Configuration

```php
// Laravel .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=optima_ci
DB_USERNAME=root
DB_PASSWORD=
```

---

## 📊 Tabel Utama dengan Struktur

### 1. Users & Authentication

#### `users`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
username            VARCHAR(100) UNIQUE
email               VARCHAR(255) UNIQUE
password_hash       VARCHAR(255)
first_name          VARCHAR(100)
last_name           VARCHAR(100)
avatar              VARCHAR(255) NULL
division_id         INT → divisions.id
position_id         INT → positions.id
is_active           TINYINT(1) DEFAULT 1
is_approved         TINYINT(1) DEFAULT 0
otp_enabled         TINYINT(1) DEFAULT 0
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `roles`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
name                VARCHAR(100) UNIQUE
code                VARCHAR(50) UNIQUE
description         TEXT NULL
is_active           TINYINT(1) DEFAULT 1
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `permissions`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
name                VARCHAR(100) UNIQUE
code                VARCHAR(100) UNIQUE
module_name         VARCHAR(50)
resource_name       VARCHAR(50)
action              VARCHAR(50) (view, create, edit, delete)
description         TEXT NULL
is_active           TINYINT(1) DEFAULT 1
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `user_roles`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
user_id             INT → users.id
role_id             INT → roles.id
created_at          TIMESTAMP
UNIQUE KEY (user_id, role_id)
```

#### `role_permissions`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
role_id             INT → roles.id
permission_id       INT → permissions.id
created_at          TIMESTAMP
UNIQUE KEY (role_id, permission_id)
```

---

### 2. Customer & Contract

#### `customers`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
customer_code       VARCHAR(20) UNIQUE
customer_name       VARCHAR(255)
is_active           TINYINT(1) DEFAULT 1
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `customer_locations`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
customer_id         INT → customers.id
area_id             INT → areas.id NULL
location_name       VARCHAR(100)
location_code       VARCHAR(50)
location_type       ENUM('HEAD_OFFICE','BRANCH','WAREHOUSE','FACTORY')
address             TEXT
contact_person      VARCHAR(128) NULL
phone               VARCHAR(32) NULL
email               VARCHAR(128) NULL
city                VARCHAR(100)
province            VARCHAR(100)
is_primary          TINYINT(1) DEFAULT 0
is_active           TINYINT(1) DEFAULT 1
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `kontrak`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
customer_location_id INT → customer_locations.id
no_kontrak          VARCHAR(100) UNIQUE
no_po_marketing     VARCHAR(100) NULL
nilai_total         DECIMAL(15,2) NULL
total_units         INT DEFAULT 0
jenis_sewa          ENUM('BULANAN','HARIAN') DEFAULT 'BULANAN'
tanggal_mulai       DATE
tanggal_berakhir    DATE
status              ENUM('Aktif','Berakhir','Pending','Dibatalkan') DEFAULT 'Pending'
dibuat_oleh         INT → users.id NULL
dibuat_pada         DATETIME DEFAULT CURRENT_TIMESTAMP
diperbarui_pada     DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE
```

#### `kontrak_spesifikasi`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
kontrak_id          INT → kontrak.id
departemen_id       INT → departemen.id
tipe_unit_id        INT → tipe_unit.id
model_unit_id       INT → model_unit.id
kapasitas_id        INT → kapasitas.id
merk_unit           VARCHAR(100)
jumlah_unit         INT DEFAULT 1
baterai_id          INT → baterai.id NULL
charger_id          INT → charger.id NULL
attachment_id       INT → attachment.id NULL
valve_id            INT → valve.id NULL
tipe_mast_id        INT → tipe_mast.id NULL
tipe_ban_id         INT → tipe_ban.id NULL
jenis_roda_id       INT → jenis_roda.id NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### 3. Marketing: SPK & DI

#### `spk`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
kontrak_id          INT → kontrak.id
no_spk              VARCHAR(100) UNIQUE
jenis_perintah_kerja_id INT → jenis_perintah_kerja.id
tujuan_perintah_kerja_id INT → tujuan_perintah_kerja.id
customer_location_id INT → customer_locations.id
status              ENUM('DIAJUKAN','DISETUJUI','PERSIAPAN','FABRIKASI','READY','DELIVERY','SELESAI')
stage               VARCHAR(50)
created_by          INT → users.id
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `spk_units`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
spk_id              INT → spk.id
inventory_unit_id   INT → inventory_unit.id
kontrak_spesifikasi_id INT → kontrak_spesifikasi.id
status              VARCHAR(50)
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `delivery_instructions`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
spk_id              INT → spk.id
no_di               VARCHAR(100) UNIQUE
jenis_perintah_kerja_id INT → jenis_perintah_kerja.id
tujuan_perintah_kerja_id INT → tujuan_perintah_kerja.id
customer_location_id INT → customer_locations.id
status              ENUM('DIAJUKAN','DISETUJUI','PERSIAPAN_UNIT','DALAM_PERJALANAN','UNIT_DITARIK','UNIT_PULANG','SAMPAI_KANTOR','SELESAI')
created_by          INT → users.id
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `delivery_items`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
di_id               INT → delivery_instructions.id
inventory_unit_id   INT → inventory_unit.id
inventory_attachment_id INT → inventory_attachment.id NULL
item_type           ENUM('unit','attachment')
status              VARCHAR(50)
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### 4. Purchasing: PO

#### `suppliers`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
supplier_code       VARCHAR(50) UNIQUE
supplier_name       VARCHAR(255)
supplier_type       ENUM('UNIT','ATTACHMENT','SPAREPART','MIXED')
address             TEXT
phone               VARCHAR(32)
email               VARCHAR(128)
is_active           TINYINT(1) DEFAULT 1
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `purchase_orders`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
no_po               VARCHAR(100) UNIQUE
supplier_id         INT → suppliers.id
tipe_po             ENUM('UNIT','ATTACHMENT','SPAREPART')
status              ENUM('DRAFT','SUBMITTED','APPROVED','DELIVERY','VERIFIED','COMPLETED','CANCELLED')
total_amount        DECIMAL(15,2)
created_by          INT → users.id
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `po_units`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
po_id               INT → purchase_orders.id
tipe_unit_id        INT → tipe_unit.id
model_unit_id       INT → model_unit.id
kapasitas_id        INT → kapasitas.id
merk_unit           VARCHAR(100)
jumlah              INT
harga_satuan         DECIMAL(15,2)
subtotal             DECIMAL(15,2)
verification_status  ENUM('PENDING','VERIFIED','REJECTED') DEFAULT 'PENDING'
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `po_attachment`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
po_id               INT → purchase_orders.id
attachment_id       INT → attachment.id
baterai_id          INT → baterai.id NULL
charger_id          INT → charger.id NULL
jumlah              INT
harga_satuan         DECIMAL(15,2)
subtotal             DECIMAL(15,2)
verification_status  ENUM('PENDING','VERIFIED','REJECTED') DEFAULT 'PENDING'
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `po_sparepart_items`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
po_id               INT → purchase_orders.id
sparepart_id        INT → sparepart.id
jumlah              INT
harga_satuan         DECIMAL(15,2)
subtotal             DECIMAL(15,2)
verification_status  ENUM('PENDING','VERIFIED','REJECTED') DEFAULT 'PENDING'
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### 5. Warehouse: Inventory

#### `inventory_unit`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
no_unit             VARCHAR(50) UNIQUE
tipe_unit_id        INT → tipe_unit.id
model_unit_id       INT → model_unit.id
kapasitas_id        INT → kapasitas.id
departemen_id       INT → departemen.id
mesin_id            INT → mesin.id
status_unit_id      INT → status_unit.id
sn_unit             VARCHAR(100) NULL
hour_meter          DECIMAL(10,2) DEFAULT 0
lokasi              VARCHAR(255) NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `inventory_attachment`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
tipe_item           ENUM('unit','attachment','battery','charger')
inventory_unit_id   INT → inventory_unit.id NULL
attachment_id       INT → attachment.id NULL
baterai_id          INT → baterai.id NULL
charger_id          INT → charger.id NULL
sn_attachment       VARCHAR(100) NULL
sn_baterai          VARCHAR(100) NULL
sn_charger          VARCHAR(100) NULL
status_unit         INT (1-9: berbagai status)
attachment_status   ENUM('AVAILABLE','USED','MAINTENANCE','DAMAGED','RETIRED')
lokasi_rak          VARCHAR(100) NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `inventory_spareparts`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
sparepart_id        INT → sparepart.id
stok                INT DEFAULT 0
lokasi_rak          VARCHAR(100) NULL
updated_at          TIMESTAMP
```

---

### 6. Service: Work Orders

#### `work_orders`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
no_wo               VARCHAR(100) UNIQUE
inventory_unit_id   INT → inventory_unit.id
area_id             INT → areas.id
category_id         INT → work_order_categories.id
subcategory_id      INT → work_order_subcategories.id
priority_id         INT → work_order_priorities.id
status_id           INT → work_order_statuses.id
description         TEXT
created_by          INT → users.id
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `work_order_assignments`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
work_order_id       INT → work_orders.id
employee_id         INT → employees.id
assigned_at         DATETIME
completed_at        DATETIME NULL
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

#### `work_order_spareparts`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
work_order_id       INT → work_orders.id
sparepart_id        INT → sparepart.id
inventory_sparepart_id INT → inventory_spareparts.id
jumlah              INT
status              ENUM('REQUESTED','USED','RETURNED')
created_at          TIMESTAMP
updated_at          TIMESTAMP
```

---

### 7. Master Data

#### `tipe_unit`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
tipe_unit           VARCHAR(100)
```

#### `model_unit`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
merk_unit           VARCHAR(100)
model_unit          VARCHAR(100)
tipe_unit_id        INT → tipe_unit.id
```

#### `kapasitas`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
kapasitas_unit      VARCHAR(50)
```

#### `departemen`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
nama_departemen     VARCHAR(100)
```

#### `attachment`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
tipe                VARCHAR(100)
merk                VARCHAR(100)
model               VARCHAR(100)
```

#### `baterai`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
merk_baterai        VARCHAR(100)
tipe_baterai        VARCHAR(100)
jenis_baterai       VARCHAR(50)
```

#### `charger`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
merk_charger        VARCHAR(100)
tipe_charger        VARCHAR(100)
```

#### `sparepart`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
kode_sparepart      VARCHAR(50) UNIQUE
nama_sparepart      VARCHAR(255)
merk                VARCHAR(100)
tipe                VARCHAR(100)
harga_beli          DECIMAL(15,2) NULL
```

---

### 8. System

#### `notifications`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
user_id             INT → users.id
title               VARCHAR(255)
message             TEXT
type                ENUM('info','success','warning','error')
priority            ENUM('LOW','MEDIUM','HIGH','CRITICAL')
is_read             TINYINT(1) DEFAULT 0
url                 VARCHAR(500) NULL
metadata            JSON NULL
created_at          TIMESTAMP
```

#### `system_activity_log`
```sql
id                  INT PRIMARY KEY AUTO_INCREMENT
user_id             INT → users.id NULL
module_name         VARCHAR(50)
action              VARCHAR(50)
entity_type         VARCHAR(50)
entity_id           INT NULL
description         TEXT
business_impact      ENUM('LOW','MEDIUM','HIGH','CRITICAL')
ip_address          VARCHAR(45)
user_agent          TEXT
created_at          TIMESTAMP
```

---

## 🔄 Laravel Migration Examples

### Example 1: Users Table
```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('username', 100)->unique();
    $table->string('email', 255)->unique();
    $table->string('password_hash', 255);
    $table->string('first_name', 100);
    $table->string('last_name', 100);
    $table->string('avatar', 255)->nullable();
    $table->foreignId('division_id')->nullable()->constrained('divisions');
    $table->foreignId('position_id')->nullable()->constrained('positions');
    $table->boolean('is_active')->default(true);
    $table->boolean('is_approved')->default(false);
    $table->boolean('otp_enabled')->default(false);
    $table->timestamps();
});
```

### Example 2: Kontrak Table
```php
Schema::create('kontrak', function (Blueprint $table) {
    $table->id();
    $table->foreignId('customer_location_id')->nullable()->constrained('customer_locations');
    $table->string('no_kontrak', 100)->unique();
    $table->string('no_po_marketing', 100)->nullable();
    $table->decimal('nilai_total', 15, 2)->nullable();
    $table->unsignedInteger('total_units')->default(0);
    $table->enum('jenis_sewa', ['BULANAN', 'HARIAN'])->default('BULANAN');
    $table->date('tanggal_mulai');
    $table->date('tanggal_berakhir');
    $table->enum('status', ['Aktif', 'Berakhir', 'Pending', 'Dibatalkan'])->default('Pending');
    $table->foreignId('dibuat_oleh')->nullable()->constrained('users');
    $table->timestamp('dibuat_pada')->useCurrent();
    $table->timestamp('diperbarui_pada')->useCurrent()->useCurrentOnUpdate();
});
```

---

## 📝 Notes

1. **Timestamps**: Beberapa tabel menggunakan `dibuat_pada`/`diperbarui_pada` (Bahasa Indonesia), perlu diseragamkan ke `created_at`/`updated_at` saat migrasi.

2. **Foreign Keys**: Pastikan semua foreign keys didefinisikan dengan `constrained()` di Laravel migrations.

3. **Indexes**: Tambahkan indexes untuk:
   - Status columns
   - Foreign key columns
   - Unique columns (no_kontrak, no_spk, no_po, dll)

4. **Enums**: Convert ENUM ke Laravel enums atau constants.

5. **JSON Columns**: Beberapa kolom menggunakan JSON (metadata), pastikan menggunakan `json()` type di Laravel.

---

**File ini adalah quick reference untuk migrasi database ke Laravel.**  
**Untuk struktur lengkap, lihat file SQL: `optima_db_24-11-25_FINAL.sql`**



