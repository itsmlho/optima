# Dokumentasi Struktur Database OPTIMA

## 📊 Overview Database

**Database Name**: `optima_ci_test` (atau `optima_ci` untuk production)  
**Engine**: InnoDB  
**Charset**: utf8mb4  
**Collation**: utf8mb4_general_ci  
**Total Tables**: 100+ tabel

---

## 📋 Daftar Tabel (Alphabetical)

### A
- `activity_types` - Tipe aktivitas untuk logging
- `areas` - Area/lokasi service
- `area_employee_assignments` - Assignment employee ke area
- `attachment` - Master data attachment (fork positioner, clamp, dll)

### B
- `baterai` - Master data baterai

### C
- `charger` - Master data charger
- `contract_disconnection_log` - Log pemutusan kontrak
- `customers` - Master data customers
- `customer_contracts` - Kontrak customer
- `customer_locations` - Lokasi customer
- `customer_locations_backup` - Backup lokasi customer

### D
- `delivery_instructions` - Delivery Instruction (DI)
- `delivery_items` - Item dalam DI
- `delivery_workflow_log` - Log workflow delivery
- `departemen` - Master data departemen (DIESEL, ELECTRIC, GASOLINE)
- `di_workflow_stages` - Stages workflow DI
- `divisions` - Divisi perusahaan

### F
- `forklifts` - (Legacy) Data forklift

### I
- `inventory_attachment` - Inventory attachment, battery, charger
- `inventory_item_unit_log` - Log assignment item ke unit
- `inventory_spareparts` - Inventory sparepart
- `inventory_unit` - Inventory unit forklift

### J
- `jenis_perintah_kerja` - Jenis perintah kerja (ANTAR, TARIK, TUKAR, RELOKASI)
- `jenis_roda` - Master data jenis roda

### K
- `kapasitas` - Master data kapasitas unit
- `kontrak` - Kontrak penyewaan
- `kontrak_spesifikasi` - Spesifikasi kontrak
- `kontrak_status_changes` - Log perubahan status kontrak

### M
- `mesin` - Master data mesin
- `migrations` - CodeIgniter migrations
- `migration_log` - Log migrations
- `migration_log_di_workflow` - Log migration DI workflow
- `model_unit` - Master data model unit

### N
- `notifications` - Notifikasi sistem
- `notification_rules` - Rules untuk notifikasi

### O
- `optimization_additional_log` - Log optimasi tambahan
- `optimization_log` - Log optimasi database

### P
- `password_resets` - Password reset tokens
- `permissions` - Permissions untuk RBAC
- `positions` - Posisi/jabatan
- `po_attachment` - PO attachment items
- `po_deliveries` - Delivery dari PO
- `po_delivery_items` - Item dalam delivery
- `po_sparepart_items` - PO sparepart items
- `po_units` - PO unit items
- `po_verification` - Verifikasi PO
- `purchase_orders` - Purchase orders

### R
- `rbac_audit_log` - Audit log RBAC
- `rentals` - (Legacy) Data rental
- `reports` - Reports
- `roles` - Roles untuk RBAC
- `role_permissions` - Role-permission mapping

### S
- `sparepart` - Master data sparepart
- `spk` - Surat Perintah Kerja
- `spk_component_transactions` - Transaksi komponen SPK
- `spk_edit_permissions` - Permission edit SPK
- `spk_rollback_log` - Log rollback SPK
- `spk_status_history` - History status SPK
- `spk_units` - Unit dalam SPK
- `spk_unit_stages` - Stages unit dalam SPK
- `status_attachment` - Status attachment
- `status_eksekusi_workflow` - Status eksekusi workflow
- `status_unit` - Status unit
- `suppliers` - Master data suppliers
- `supplier_contacts` - Kontak supplier
- `supplier_documents` - Dokumen supplier
- `supplier_performance_log` - Log performa supplier
- `system_activity_log` - Log aktivitas sistem

### T
- `tipe_ban` - Master data tipe ban
- `tipe_mast` - Master data tipe mast
- `tipe_unit` - Master data tipe unit
- `tujuan_perintah_kerja` - Tujuan perintah kerja

### U
- `unit_replacement_log` - Log penggantian unit
- `unit_status_log` - Log status unit
- `unit_workflow_log` - Log workflow unit
- `users` - User accounts
- `user_otp` - OTP users
- `user_permissions` - User-permission mapping
- `user_roles` - User-role mapping
- `user_sessions` - Session users

### V
- `valve` - Master data valve

### W
- `work_orders` - Work orders
- `work_order_attachments` - Attachment work order
- `work_order_assignments` - Assignment employee ke work order
- `work_order_categories` - Kategori work order
- `work_order_comments` - Komentar work order
- `work_order_priorities` - Prioritas work order
- `work_order_sparepart_returns` - Return sparepart
- `work_order_sparepart_usage` - Usage sparepart
- `work_order_spareparts` - Sparepart dalam work order
- `work_order_status_history` - History status work order
- `work_order_statuses` - Status work order
- `work_order_subcategories` - Subkategori work order

---

## 🔗 Relasi Tabel Utama

### 1. User & Authorization
```
users
├── user_roles → roles
├── user_permissions → permissions
├── user_sessions
└── user_otp

roles
├── role_permissions → permissions
└── user_roles → users

permissions
├── role_permissions → roles
└── user_permissions → users
```

### 2. Customer & Contract
```
customers
├── customer_locations
│   └── kontrak
│       └── kontrak_spesifikasi
│           └── spk_units
└── customer_contracts
```

### 3. Marketing Workflow
```
quotations
├── quotation_specifications
└── kontrak (jika convert)

kontrak
├── kontrak_spesifikasi
│   └── spk_units
└── spk

spk
├── spk_units → inventory_unit
├── spk_status_history
└── delivery_instructions

delivery_instructions
├── delivery_items
└── di_workflow_stages
```

### 4. Purchasing Workflow
```
suppliers
├── supplier_contacts
├── supplier_documents
└── purchase_orders
    ├── po_units → inventory_unit
    ├── po_attachment → inventory_attachment
    ├── po_sparepart_items → inventory_spareparts
    ├── po_deliveries
    │   └── po_delivery_items
    └── po_verification
```

### 5. Warehouse Inventory
```
inventory_unit
├── inventory_attachment (battery, charger, attachment)
└── kontrak_spesifikasi (via spk_units)

inventory_attachment
├── attachment (master)
├── baterai (master)
├── charger (master)
└── inventory_unit (assignment)

inventory_spareparts
└── sparepart (master)
```

### 6. Service Workflow
```
work_orders
├── work_order_assignments → employees
├── work_order_spareparts → inventory_spareparts
├── work_order_attachments
├── work_order_status_history
└── work_order_comments

areas
├── area_employee_assignments → employees
└── work_orders (via area_id)
```

### 7. Master Data
```
tipe_unit
├── model_unit
│   └── inventory_unit
└── kapasitas

departemen
└── areas

mesin
└── inventory_unit

attachment (master)
├── inventory_attachment
└── kontrak_spesifikasi

baterai (master)
└── inventory_attachment

charger (master)
└── inventory_attachment

sparepart (master)
└── inventory_spareparts
```

---

## 📊 Struktur Tabel Detail

### Tabel Utama: `users`
```sql
- id (PK)
- username
- email
- password_hash
- first_name
- last_name
- avatar
- division_id → divisions
- position_id → positions
- is_active
- is_approved
- otp_enabled
- created_at
- updated_at
```

### Tabel Utama: `kontrak`
```sql
- id (PK)
- customer_location_id → customer_locations
- no_kontrak
- no_po_marketing
- nilai_total
- total_units
- jenis_sewa (BULANAN, HARIAN)
- tanggal_mulai
- tanggal_berakhir
- status (Aktif, Berakhir, Pending, Dibatalkan)
- dibuat_oleh → users
- dibuat_pada
- diperbarui_pada
```

### Tabel Utama: `spk`
```sql
- id (PK)
- kontrak_id → kontrak
- no_spk
- jenis_perintah_kerja_id → jenis_perintah_kerja
- tujuan_perintah_kerja_id → tujuan_perintah_kerja
- customer_location_id → customer_locations
- status (DIAJUKAN, DISETUJUI, PERSIAPAN, FABRIKASI, READY, DELIVERY, SELESAI)
- stage (current stage)
- created_by → users
- created_at
- updated_at
```

### Tabel Utama: `purchase_orders`
```sql
- id (PK)
- no_po
- supplier_id → suppliers
- tipe_po (UNIT, ATTACHMENT, SPAREPART)
- status (DRAFT, SUBMITTED, APPROVED, DELIVERY, VERIFIED, COMPLETED, CANCELLED)
- total_amount
- created_by → users
- created_at
- updated_at
```

### Tabel Utama: `inventory_unit`
```sql
- id (PK)
- no_unit
- tipe_unit_id → tipe_unit
- model_unit_id → model_unit
- kapasitas_id → kapasitas
- departemen_id → departemen
- mesin_id → mesin
- status_unit_id → status_unit
- sn_unit
- hour_meter
- lokasi
- created_at
- updated_at
```

### Tabel Utama: `inventory_attachment`
```sql
- id (PK)
- tipe_item (unit, attachment, battery, charger)
- inventory_unit_id → inventory_unit (nullable, jika assigned)
- attachment_id → attachment (nullable)
- baterai_id → baterai (nullable)
- charger_id → charger (nullable)
- sn_attachment
- sn_baterai
- sn_charger
- status_unit (1-9: berbagai status)
- attachment_status (AVAILABLE, USED, MAINTENANCE, dll)
- lokasi_rak
- created_at
- updated_at
```

### Tabel Utama: `work_orders`
```sql
- id (PK)
- no_wo
- inventory_unit_id → inventory_unit
- area_id → areas
- category_id → work_order_categories
- subcategory_id → work_order_subcategories
- priority_id → work_order_priorities
- status_id → work_order_statuses
- description
- created_by → users
- created_at
- updated_at
```

### Tabel Utama: `delivery_instructions`
```sql
- id (PK)
- spk_id → spk
- no_di
- jenis_perintah_kerja_id → jenis_perintah_kerja
- tujuan_perintah_kerja_id → tujuan_perintah_kerja
- customer_location_id → customer_locations
- status (DIAJUKAN, DISETUJUI, PERSIAPAN_UNIT, DALAM_PERJALANAN, UNIT_DITARIK, UNIT_PULANG, SAMPAI_KANTOR, SELESAI)
- created_by → users
- created_at
- updated_at
```

---

## 🔄 Workflow Status

### SPK Status Flow
```
DIAJUKAN → DISETUJUI → PERSIAPAN → FABRIKASI → READY → DELIVERY → SELESAI
```

### DI Status Flow
```
DIAJUKAN → DISETUJUI → PERSIAPAN_UNIT → DALAM_PERJALANAN → 
UNIT_DITARIK → UNIT_PULANG → SAMPAI_KANTOR → SELESAI
```

### PO Status Flow
```
DRAFT → SUBMITTED → APPROVED → DELIVERY → VERIFIED → COMPLETED
```

### Work Order Status Flow
```
OPEN → IN_PROGRESS → COMPLETED → CLOSED
```

### Kontrak Status
```
Pending → Aktif → Berakhir / Dibatalkan
```

---

## 📝 Indexes & Constraints

### Primary Keys
Semua tabel memiliki `id` sebagai PRIMARY KEY dengan AUTO_INCREMENT.

### Foreign Keys
Foreign keys didefinisikan untuk:
- User relationships (users → divisions, positions)
- Contract relationships (kontrak → customers, locations)
- SPK relationships (spk → kontrak, customer_locations)
- PO relationships (purchase_orders → suppliers)
- Inventory relationships (inventory_unit → master data)
- Work order relationships (work_orders → inventory_unit, areas)

### Indexes
Indexes dibuat untuk:
- Status columns (untuk filtering cepat)
- Foreign key columns
- No. kontrak, SPK, PO, DI (untuk unique constraint)
- Created_at, updated_at (untuk sorting)

---

## 🔐 Security Features

### 1. Password Security
- Password di-hash menggunakan CodeIgniter password hashing
- Password reset tokens dengan expiry

### 2. Session Management
- Multiple session support
- Session tracking
- Session timeout

### 3. OTP System
- Optional OTP untuk login
- OTP expiry time

### 4. Login Attempts
- Login attempt tracking
- Account locking setelah multiple failed attempts

### 5. Activity Logging
- Comprehensive activity logging
- Audit trail untuk semua perubahan penting

---

## 📈 Performance Optimization

### Indexes
- Indexes pada foreign keys
- Indexes pada status columns
- Indexes pada date columns (created_at, updated_at)
- Composite indexes untuk queries kompleks

### Triggers
- Auto-update timestamps
- Status synchronization
- Inventory status updates

### Stored Procedures
- Packing list number generation
- Status update procedures

---

## 🗄️ Database Migrations

File SQL migrations tersedia di:
- `databases/migrations/` - Migration scripts
- `app/Database/Migrations/` - CodeIgniter migrations

### Migration Categories:
1. **Structure Migrations**: Create/alter tables
2. **Data Migrations**: Seed master data
3. **Performance Migrations**: Add indexes, optimize
4. **Workflow Migrations**: Add workflow features

---

## 📋 Notes untuk Migrasi Laravel

### 1. Eloquent Models
Semua tabel perlu dibuat Eloquent models dengan relationships:
- `belongsTo`, `hasMany`, `hasOne`, `belongsToMany`

### 2. Migrations
Convert SQL CREATE TABLE ke Laravel migrations:
- `Schema::create()` untuk create table
- `$table->foreign()` untuk foreign keys
- `$table->index()` untuk indexes

### 3. Seeders
Convert INSERT statements ke Laravel seeders:
- Master data (customers, suppliers, dll)
- Default roles & permissions
- Default users

### 4. Relationships
Define relationships di models:
- User → Role, Permission, Division
- Kontrak → Customer, SPK
- SPK → Kontrak, DI, Units
- PO → Supplier, Items
- Inventory → Master data

### 5. Enums
Convert ENUM columns ke Laravel enums atau constants:
- Status columns
- Type columns

---

**Dokumen ini berisi struktur database lengkap untuk aplikasi OPTIMA.**  
**Last Updated**: 2025-01-XX



