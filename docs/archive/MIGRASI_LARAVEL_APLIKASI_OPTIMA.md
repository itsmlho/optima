# Dokumentasi Aplikasi OPTIMA - Persiapan Migrasi ke Laravel

## 📋 Ringkasan Aplikasi

**OPTIMA** adalah sistem manajemen penyewaan forklift untuk **PT Sarana Mitra Luas Tbk**. Aplikasi ini dibangun menggunakan **CodeIgniter 4** dan mengelola seluruh siklus bisnis dari marketing, purchasing, warehouse, service, operational, hingga finance.

### Informasi Teknis
- **Framework**: CodeIgniter 4
- **PHP Version**: ^8.1
- **Database**: MySQL/MariaDB
- **Frontend**: Bootstrap 5, jQuery, DataTables
- **Dependencies**: dompdf, phpspreadsheet, laminas-escaper

---

## 🏗️ Arsitektur Aplikasi

### Struktur Direktori

```
optima/
├── app/
│   ├── Controllers/        # 50+ controllers
│   ├── Models/            # 74+ models
│   ├── Views/             # 150+ view files
│   ├── Config/             # Konfigurasi aplikasi
│   ├── Filters/            # Authentication, Permission, Language filters
│   ├── Helpers/            # Helper functions
│   ├── Services/           # Business logic services
│   ├── Database/
│   │   ├── Migrations/     # Database migrations
│   │   └── Seeds/          # Database seeders
│   └── Language/           # Multi-language support (id, en)
├── system/                 # CodeIgniter 4 core
├── public/                 # Public assets
├── databases/              # SQL scripts & backups
└── vendor/                 # Composer dependencies
```

---

## 🔄 Workflow Aplikasi

### 1. **MARKETING DIVISION**

#### 1.1 Quotation Management
- **Workflow**: Prospect → Quotation → Deal → Contract
- **Fitur**:
  - Membuat quotation untuk customer
  - Menambahkan spesifikasi unit (tipe, kapasitas, attachment, dll)
  - Tracking status quotation
  - Convert quotation ke kontrak
  - Print quotation PDF

#### 1.2 Contract Management (Kontrak)
- **Fitur**:
  - CRUD kontrak
  - Assign unit ke kontrak
  - Spesifikasi kontrak (multiple specifications)
  - Status: Pending → Aktif → Berakhir → Dibatalkan
  - Link kontrak dengan customer locations

#### 1.3 SPK (Surat Perintah Kerja) Management
- **Workflow**: 
  - Marketing membuat SPK dari kontrak
  - SPK memiliki stages: DIAJUKAN → DISETUJUI → PERSIAPAN → FABRIKASI → READY → DELIVERY
- **Fitur**:
  - Create SPK dari kontrak
  - Assign unit dan attachment ke SPK
  - Tracking status SPK
  - Print SPK PDF

#### 1.4 DI (Delivery Instruction) Management
- **Jenis Perintah Kerja**:
  - ANTAR: Pengantaran unit ke customer
  - TARIK: Penarikan unit dari customer
  - TUKAR: Tukar unit lama dengan baru
  - RELOKASI: Pemindahan unit antar lokasi
- **Workflow Stages**:
  - DIAJUKAN → DISETUJUI → PERSIAPAN_UNIT → DALAM_PERJALANAN → UNIT_DITARIK → UNIT_PULANG → SAMPAI_KANTOR → SELESAI

#### 1.5 Customer Management
- **Fitur**:
  - CRUD customers
  - Customer locations management
  - Customer contracts tracking
  - Customer profile status

---

### 2. **PURCHASING DIVISION**

#### 2.1 Purchase Order (PO) Management
- **Tipe PO**:
  - **PO Unit**: Purchase order untuk unit forklift baru
  - **PO Attachment**: Purchase order untuk attachment (fork positioner, clamp, dll)
  - **PO Sparepart**: Purchase order untuk sparepart
- **Workflow**:
  - Create PO → Submit → Approved → Delivery → Verification → Completed
- **Fitur**:
  - Dynamic form untuk unit/attachment/sparepart
  - Supplier management
  - PO tracking & status
  - Print PO PDF
  - Delivery management dengan packing list

#### 2.2 Supplier Management
- **Fitur**:
  - CRUD suppliers
  - Supplier contacts
  - Supplier documents
  - Supplier performance tracking

#### 2.3 Delivery Management
- **Fitur**:
  - Create delivery dari PO
  - Assign serial numbers
  - Delivery status tracking
  - Packing list generation
  - Driver & vehicle info

---

### 3. **WAREHOUSE DIVISION**

#### 3.1 Inventory Management
- **Inventory Types**:
  - **Inventory Unit**: Unit forklift
  - **Inventory Attachment**: Attachment, battery, charger
  - **Inventory Sparepart**: Sparepart stock
- **Fitur**:
  - Stock management
  - Location tracking (rak/lokasi)
  - Status tracking (Available, In Use, Maintenance, dll)
  - Component assignment (battery, charger, attachment ke unit)

#### 3.2 PO Verification
- **Fitur**:
  - Verify unit dari PO
  - Verify attachment dari PO
  - Verify sparepart dari PO
  - Re-verification untuk rejected items
  - Update inventory setelah verifikasi

#### 3.3 Sparepart Usage & Returns
- **Fitur**:
  - Track sparepart usage dari work orders
  - Sparepart returns management
  - Stock adjustment

---

### 4. **SERVICE DIVISION**

#### 4.1 Work Order Management
- **Fitur**:
  - Create work orders
  - Assign employees (mechanics) ke work order
  - Work order categories & subcategories
  - Priority levels
  - Status tracking: Open → In Progress → Completed → Closed
  - Sparepart usage tracking
  - Print work order

#### 4.2 SPK Service
- **Fitur**:
  - View SPK dari marketing
  - Approve SPK stages (FABRIKASI, READY)
  - Assign items (unit + attachment) ke SPK
  - Confirm SPK ready untuk delivery
  - Edit SPK (dengan permission)

#### 4.3 Unit Verification
- **Fitur**:
  - Verify unit setelah work order
  - Update unit status
  - Component verification (battery, charger, attachment)
  - Print verification document

#### 4.4 Area & Employee Management
- **Fitur**:
  - CRUD service areas
  - CRUD employees
  - Area-employee assignments
  - Employee availability tracking

#### 4.5 Data Unit (Service)
- **Fitur**:
  - View unit data
  - Update unit information
  - Maintenance history
  - Export unit data

---

### 5. **OPERATIONAL DIVISION**

#### 5.1 Delivery Management
- **Fitur**:
  - View DI (Delivery Instructions) dari marketing
  - Update DI status
  - Approve DI stages
  - Print delivery documents
  - Multi-unit delivery support

#### 5.2 Tracking
- **Fitur**:
  - Unit tracking
  - Delivery tracking
  - Audit trail
  - Temporary units report

---

### 6. **FINANCE DIVISION**

#### 6.1 Financial Management
- **Fitur**:
  - Invoice management
  - Payment tracking
  - Expense recording
  - Financial reports

---

### 7. **ADMINISTRATION**

#### 7.1 User Management
- **Fitur**:
  - CRUD users
  - Role assignment
  - Permission management
  - Division assignment
  - Position assignment
  - User approval workflow
  - OTP (One-Time Password) support
  - Session management

#### 7.2 Role & Permission Management
- **Sistem RBAC (Role-Based Access Control)**:
  - Roles: Admin, Marketing, Service, Warehouse, Purchasing, dll
  - Permissions: Resource-based permissions (view, create, edit, delete)
  - Role permissions assignment
  - User custom permissions

#### 7.3 Division & Position Management
- **Fitur**:
  - CRUD divisions
  - CRUD positions
  - Division-user assignments

#### 7.4 Activity Logging
- **Fitur**:
  - System activity log
  - User activity tracking
  - Audit trail
  - Activity statistics

#### 7.5 Notification System
- **Fitur**:
  - Real-time notifications (SSE - Server-Sent Events)
  - Notification rules configuration
  - Cross-division notifications
  - Priority levels (LOW, MEDIUM, HIGH, CRITICAL)
  - Notification history

---

## 🔐 Authentication & Authorization

### Authentication
- Login dengan email/username
- OTP verification (optional)
- Password reset
- Session management (multiple sessions)
- Login attempts tracking

### Authorization
- **RBAC System**:
  - Role-based access
  - Resource-based permissions
  - Division-based filtering
  - Permission filters pada routes

---

## 🌐 Multi-Language Support

- **Bahasa**: Indonesian (id) dan English (en)
- **Language Files**: 
  - `app/Language/id/` dan `app/Language/en/`
  - Modules: App, Auth, Common, Dashboard, Finance, Marketing, Service, Validation, Warehouse
- **Language Switching**: Route `/language/switch/{lang}`

---

## 📊 Database Overview

Database menggunakan **MySQL/MariaDB** dengan lebih dari **100 tabel**. Struktur database lengkap ada di file terpisah: `DATABASE_STRUCTURE.md`

### Tabel Utama:
- **users**: User accounts
- **roles, permissions, role_permissions**: RBAC system
- **customers, customer_locations**: Customer data
- **kontrak, kontrak_spesifikasi**: Contracts
- **quotations, quotation_specifications**: Quotations
- **spk, spk_units, spk_status_history**: SPK management
- **delivery_instructions, delivery_items**: DI management
- **purchase_orders, po_units, po_attachment, po_sparepart_items**: PO management
- **inventory_unit, inventory_attachment, inventory_spareparts**: Inventory
- **work_orders, work_order_assignments**: Work orders
- **notifications, notification_rules**: Notification system
- **system_activity_log**: Activity logging

---

## 🔧 Teknologi & Dependencies

### Backend
- **CodeIgniter 4**: Framework utama
- **PHP 8.1+**: Language requirement
- **MySQL/MariaDB**: Database
- **Composer**: Dependency management

### Frontend
- **Bootstrap 5**: UI framework
- **jQuery**: JavaScript library
- **DataTables**: Table management
- **Chart.js**: Charts & graphs
- **SweetAlert2**: Alert dialogs
- **Flatpickr**: Date picker
- **Moment.js**: Date manipulation

### Libraries
- **dompdf**: PDF generation
- **phpspreadsheet**: Excel import/export
- **laminas-escaper**: XSS protection

---

## 📝 Fitur Khusus

### 1. Real-time Notifications
- Server-Sent Events (SSE) untuk real-time updates
- Notification polling fallback
- Notification rules dengan kondisi kompleks

### 2. Activity Logging
- Comprehensive activity tracking
- Business impact levels
- Activity statistics & reports

### 3. Workflow Management
- Multi-stage workflows untuk SPK, DI, PO
- Status history tracking
- Approval workflows

### 4. Export/Import
- Excel export untuk berbagai modul
- PDF generation untuk documents
- CSV export support

### 5. Performance Optimization
- Database indexing
- Query optimization
- Caching support
- Background job processing

---

## 🚀 Poin Penting untuk Migrasi ke Laravel

### 1. **Routing**
- CodeIgniter menggunakan `Routes.php` dengan closure-based routing
- Laravel menggunakan `routes/web.php` dan `routes/api.php`
- Perlu mapping semua routes ke Laravel format

### 2. **Controllers**
- CodeIgniter controllers extend `BaseController`
- Laravel controllers extend `Controller`
- Perlu refactor semua controllers

### 3. **Models**
- CodeIgniter models extend `Model`
- Laravel models extend `Eloquent Model`
- Perlu convert semua models ke Eloquent

### 4. **Views**
- CodeIgniter menggunakan `view()` helper
- Laravel menggunakan Blade templates
- Perlu convert semua views ke Blade

### 5. **Database**
- CodeIgniter menggunakan Query Builder
- Laravel menggunakan Eloquent ORM
- Perlu convert semua queries

### 6. **Authentication**
- CodeIgniter custom auth system
- Laravel menggunakan Laravel Sanctum/Passport
- Perlu implementasi ulang auth system

### 7. **Permissions**
- Custom RBAC system
- Laravel menggunakan Spatie Permission atau custom
- Perlu migrasi permission system

### 8. **Multi-language**
- CodeIgniter Language system
- Laravel menggunakan Laravel Localization
- Perlu convert language files

### 9. **Notifications**
- Custom notification system dengan SSE
- Laravel menggunakan Laravel Notifications + Broadcasting
- Perlu refactor notification system

### 10. **File Structure**
- CodeIgniter structure berbeda dengan Laravel
- Perlu reorganisasi file structure

---

## 📋 Checklist Migrasi

### Phase 1: Setup & Planning
- [ ] Setup Laravel project
- [ ] Install dependencies
- [ ] Setup database connection
- [ ] Create migration files dari struktur database

### Phase 2: Core System
- [ ] Migrate authentication system
- [ ] Migrate RBAC/permission system
- [ ] Migrate user management
- [ ] Setup multi-language support

### Phase 3: Modules Migration
- [ ] Marketing module (Quotation, Kontrak, SPK, DI)
- [ ] Purchasing module (PO, Supplier)
- [ ] Warehouse module (Inventory, Verification)
- [ ] Service module (Work Orders, SPK Service)
- [ ] Operational module (Delivery, Tracking)
- [ ] Finance module

### Phase 4: Features
- [ ] Notification system
- [ ] Activity logging
- [ ] Export/Import features
- [ ] PDF generation
- [ ] Real-time features

### Phase 5: Testing & Deployment
- [ ] Unit testing
- [ ] Integration testing
- [ ] User acceptance testing
- [ ] Performance testing
- [ ] Deployment

---

## 📚 Referensi

- **CodeIgniter 4 Documentation**: https://codeigniter.com/user_guide/
- **Laravel Documentation**: https://laravel.com/docs
- **Database Structure**: Lihat `DATABASE_STRUCTURE.md`
- **API Documentation**: (Jika ada)

---

**Dokumen ini dibuat untuk membantu proses migrasi dari CodeIgniter 4 ke Laravel.**
**Last Updated**: 2025-01-XX



