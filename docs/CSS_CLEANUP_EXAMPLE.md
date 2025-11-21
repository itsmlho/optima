# 🧹 CONTOH CLEANUP CSS DARI FILE VIEW

## 📋 PANDUAN SINGKAT

File ini menunjukkan **SEBELUM** dan **SESUDAH** cleanup CSS dari file view.

---

## CONTOH 1: Marketing SPK (`app/Views/marketing/spk.php`)

### ❌ SEBELUM (Dengan CSS Inline ~43 baris):

```php
<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .modal-header { background: linear-gradient(135deg, #e9ecef 0%, #e9ecef 100%); color: white; border-radius: 15px 15px 0 0; }
    .filter-card.active { 
        transform: translateY(-3px); 
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); 
        border: 2px solid #fff; 
    }
    .filter-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25); 
    }
    
    /* DI Form Styling */
    .form-label .text-danger {
        font-size: 0.875em;
    }
    
    .form-control.is-valid, .form-select.is-valid {
        border-color: #198754;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='m2.3 6.73.98-.97-.97-.97 1.378-1.38.97.97 2.564-2.565 1.378 1.378-3.942 3.942L2.3 6.73z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem 1rem;
    }
    
    .form-control.is-invalid, .form-select.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 4.6 0.4 0.4 0.4-0.4M5.8 7.4 6.2 7 6.6 7.4'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 0.75rem center;
        background-size: 1rem 1rem;
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Content here -->
<?= $this->endSection() ?>
```

### ✅ SESUDAH (Tanpa CSS Inline):

```php
<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
    <!-- Semua CSS sudah ada di public/assets/css/optima-pro.css -->
    <!-- Tidak perlu section('css') lagi! -->
    
    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all">
                <div class="card-body">
                    <h2 class="fw-bold mb-1" id="stat-total-spk">0</h2>
                    <h6 class="card-title text-uppercase small">Total SPK</h6>
                </div>
            </div>
        </div>
    </div>

    <div class="card table-card mb-3">
        <div class="card-header d-flex flex-wrap gap-2 align-items-center justify-content-between">
            <h5 class="h5 mb-0 text-gray-800">Daftar SPK</h5>
        </div>
        <div class="card-body">
            <!-- Content -->
        </div>
    </div>
<?= $this->endSection() ?>
```

**HASIL**: File berkurang ~43 baris, lebih bersih!

---

## CONTOH 2: Service Work Orders (`app/Views/service/work_orders.php`)

### ❌ SEBELUM (Dengan CSS Inline ~150 baris):

```php
<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .card-stats:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); 
        transition: all 0.3s ease;
    }
    .table-card, .card-stats { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); 
        transition: all 0.3s ease;
    }
    
    .work-order-badge {
        font-size: 0.75rem;
        padding: 0.375rem 0.75rem;
        border-radius: 0.375rem;
    }
    .priority-critical { background-color: #dc3545 !important; }
    .priority-high { background-color: #fd7e14 !important; }
    .priority-medium { background-color: #17a2b8 !important; }
    .priority-low { background-color: #6c757d !important; }
    .priority-routine { background-color: #28a745 !important; }
    .status-open { background-color: #17a2b8 !important; }
    .status-assigned { background-color: #007bff !important; }
    .status-in-progress { background-color: #ffc107 !important; color: #000 !important; }
    .status-completed { background-color: #28a745 !important; }
    .status-closed { background-color: #343a40 !important; }
    
    /* Clickable row styling */
    .clickable-row {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }
    .clickable-row:hover {
        background-color: rgba(0, 123, 255, 0.1) !important;
    }
    
    /* Tab styling - Persis seperti User Management */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-item {
        margin-bottom: 0;
    }
    
    .nav-tabs .nav-link {
        padding: 0.5rem 1rem;
        border: 1px solid transparent;
        border-top-left-radius: 0.35rem;
        border-top-right-radius: 0.35rem;
        color: #6c757d;
        transition: all 0.15s ease-in-out;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: transparent;
    }
    
    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
        color: #4e73df;
    }
    
    .nav-tabs .nav-link.active {
        color: white !important;
        background-color: #4e73df !important;
        border-color: #4e73df !important;
    }
    
    /* ... 100+ baris lagi ... */
</style>
<?= $this->endSection() ?>
```

### ✅ SESUDAH (Hanya CDN Link, Tanpa CSS Inline):

```php
<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<!-- Hanya external CSS yang spesifik -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Semua styling sudah ada di optima-pro.css -->
    <div class="card table-card">
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill mb-0" id="workOrderTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#all">
                        <i class="fas fa-list"></i> Semua 
                        <span class="badge">25</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="all">
                    <table class="table table-hover">
                        <tbody>
                            <tr class="clickable-row">
                                <td>WO-001</td>
                                <td><span class="badge priority-high">High</span></td>
                                <td><span class="badge status-in-progress">In Progress</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
```

**HASIL**: File berkurang ~150 baris, jauh lebih ringkas!

---

## CONTOH 3: Warehouse Inventory (`app/Views/warehouse/inventory/invent_unit.php`)

### ❌ SEBELUM (CSS Inline ~142 baris):

```php
<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<style>
    .table-card { border: none; border-radius: 15px; box-shadow: 0 4px 25px rgba(0,0,0,0.1); }
    .card-stats { 
        border: none; 
        border-radius: 15px; 
        box-shadow: 0 4px 6px rgba(0,0,0,0.1); 
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .card-stats:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.15);
    }
    .card-stats.active {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        border: 2px solid #0d6efd;
    }
    
    /* Premium Export Button Styling */
    .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .btn-success:hover {
        background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
        box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
        transform: translateY(-2px);
    }
    
    /* ... CSS lainnya ... */
</style>
<?= $this->endSection() ?>
```

### ✅ SESUDAH (Hanya External Libraries):

```php
<?= $this->section('css') ?>
<!-- External libraries yang memang dibutuhkan -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
<!-- CSS OPTIMA sudah handle sisanya -->
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Inventory Table dengan Tab Terintegrasi -->
    <div class="card table-card">
        <!-- Tab Filter untuk Status Unit -->
        <div class="card-body p-0">
            <ul class="nav nav-tabs nav-fill mb-0" id="unitStatusTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#all">
                        <i class="fas fa-layer-group"></i> Semua Unit 
                        <span class="badge">50</span>
                    </a>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane active" id="all">
                    <!-- Content -->
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
```

**HASIL**: CSS berkurang drastis, hanya library external yang tersisa!

---

## CONTOH 4: Dashboard (`app/Views/dashboard.php`)

### ❌ SEBELUM:

```php
<?= $this->section('css') ?>
<!-- Additional CSS for dashboard specific styling -->
<style>
    .stats-card {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .stats-icon {
        font-size: 2.5rem;
        opacity: 0.8;
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    .quick-action-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        cursor: pointer;
        height: 100%;
    }
    
    .quick-action-card:hover {
        background: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
        color: white;
        transform: translateY(-3px);
        box-shadow: 0 0.5rem 1rem rgba(0, 97, 242, 0.25);
    }
    
    /* ... 60+ baris lagi ... */
</style>
<?= $this->endSection() ?>
```

### ✅ SESUDAH:

```php
<?= $this->section('content') ?>
    <!-- Dashboard Content - CSS sudah di optima-pro.css -->
    
    <!-- Stats Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total SPK
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">150</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-invoice fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row">
        <div class="col-md-3">
            <div class="quick-action-card">
                <div class="quick-action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <h6>Buat SPK Baru</h6>
                <p class="text-muted small mb-0">Tambah SPK Unit</p>
            </div>
        </div>
    </div>
    
    <!-- Activity Feed -->
    <div class="card mt-4">
        <div class="card-header">
            <h6 class="mb-0">Aktivitas Terbaru</h6>
        </div>
        <div class="card-body p-0">
            <div class="activity-item">
                <div class="activity-icon success">
                    <i class="fas fa-check"></i>
                </div>
                <div class="flex-grow-1">
                    <h6 class="mb-0">SPK-001 Selesai</h6>
                    <small class="text-muted">2 jam yang lalu</small>
                </div>
            </div>
        </div>
    </div>
<?= $this->endSection() ?>
```

**HASIL**: Dashboard lebih ringkas, CSS terpusat!

---

## 🎯 QUICK CHECKLIST CLEANUP

Untuk setiap file view, cek apakah ada CSS berikut yang bisa dihapus:

- [ ] `.card-stats` dengan hover effect
- [ ] `.table-card` styling
- [ ] `.filter-card` styling
- [ ] `.nav-tabs` dan `.nav-link` styling
- [ ] `.btn-success` gradient styling
- [ ] `.btn-outline-success` styling
- [ ] Form validation (`.is-valid`, `.is-invalid`)
- [ ] `.clickable-row` styling
- [ ] `.modal-header` gradient
- [ ] Badge status (`.priority-*`, `.status-*`)
- [ ] `.work-order-badge` styling
- [ ] `.activity-item` dan `.activity-icon`
- [ ] `.quick-action-card` styling
- [ ] `.border-left-*` utilities
- [ ] Pagination custom styles
- [ ] `.chart-container` basic styling

**Jika ada, HAPUS! Sudah ada di `optima-pro.css`.**

---

## ⚠️ PERHATIAN: CSS YANG TETAP DIPERTAHANKAN

**JANGAN hapus CSS yang:**
1. Spesifik untuk halaman tertentu (bukan komponen umum)
2. Override styling dengan logic khusus
3. Print-only styles
4. External library CDN links (DataTables, Select2, dll)
5. CSS animations yang custom
6. Media queries yang spesifik untuk halaman tersebut

---

## ✅ LANGKAH-LANGKAH CLEANUP

### 1. Backup File
```bash
cp app/Views/marketing/spk.php app/Views/marketing/spk.php.backup
```

### 2. Buka File & Identifikasi CSS
Cari section:
```php
<?= $this->section('css') ?>
<style>
    /* CSS di sini */
</style>
<?= $this->endSection() ?>
```

### 3. Cek CSS di optima-pro.css
Apakah CSS yang sama sudah ada? Jika iya, HAPUS!

### 4. Hapus atau Kosongkan Section
Jika masih ada CSS khusus:
```php
<?= $this->section('css') ?>
<!-- CSS khusus halaman ini jika ada -->
<style>
    /* Hanya CSS yang benar-benar custom */
</style>
<?= $this->endSection() ?>
```

Jika tidak ada CSS khusus, hapus section sepenuhnya!

### 5. Test Halaman
1. Clear browser cache (`Ctrl + Shift + R`)
2. Buka halaman
3. Pastikan tampilan masih sama
4. Test hover effects, animations, dll

### 6. Commit Changes
```bash
git add app/Views/marketing/spk.php
git commit -m "Cleanup: Remove duplicate CSS from marketing/spk.php"
```

---

## 📊 EXPECTED RESULTS

| File | Baris CSS Sebelum | Baris CSS Sesudah | Saving |
|------|-------------------|-------------------|--------|
| marketing/spk.php | 43 | 0 | 100% |
| service/work_orders.php | 150 | 5 (CDN) | 96% |
| warehouse/invent_unit.php | 142 | 10 (CDN) | 93% |
| dashboard.php | 100 | 0 | 100% |
| purchasing/index.php | 80 | 0 | 100% |

**Total Saving**: ~500+ baris CSS inline yang tidak perlu!

---

## 🎉 SELESAI!

Setelah cleanup:
- ✅ File view lebih bersih dan mudah dibaca
- ✅ Tidak ada duplikasi CSS
- ✅ Maintenance lebih mudah
- ✅ Performa loading lebih cepat
- ✅ Konsistensi terjamin di seluruh aplikasi

**Happy Cleaning! 🧹✨**

