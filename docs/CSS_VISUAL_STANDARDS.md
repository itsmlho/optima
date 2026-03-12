# Optima CSS & Visual Standards
**Version:** 2.0 (March 2026)  
**Purpose:** Panduan standarisasi tampilan untuk konsistensi UI/UX di seluruh module Optima

---

## 📋 Table of Contents
1. [Badge System](#badge-system)
2. [Page Layout Structure](#page-layout-structure)
3. [Table Design](#table-design)
4. [Filter & Form Components](#filter--form-components)
5. [Modal Design](#modal-design)
6. [Color Palette](#color-palette)
7. [Typography](#typography)
8. [Icons & Emoji Usage](#icons--emoji-usage)
9. [Quick Reference Cheatsheet](#quick-reference-cheatsheet)

---

## 🎨 Badge System

### Core Principle
**ALWAYS use `badge-soft-*` classes from optima-pro.css**  
❌ NEVER use Bootstrap default `bg-*` classes for badges

### Semantic Color Mapping

#### Status Badges
```html
<!-- ACTIVE / Success / Available / Paid -->
<span class="badge badge-soft-green">ACTIVE</span>

<!-- PENDING / Warning / Waiting / In Progress -->
<span class="badge badge-soft-yellow">PENDING</span>

<!-- EXPIRED / Danger / Cancelled / Overdue / Rejected -->
<span class="badge badge-soft-red">EXPIRED</span>

<!-- CANCELLED / Inactive / Disabled / N/A -->
<span class="badge badge-soft-gray">CANCELLED</span>
```

#### Type/Category Badges
```html
<!-- CONTRACT / Primary Info / Counts / IDs / Codes -->
<span class="badge badge-soft-blue">CONTRACT</span>
<span class="badge badge-soft-blue">5 units</span>
<span class="badge badge-soft-blue font-monospace">CUST-0001</span>

<!-- PO_ONLY / Secondary Info / Supporting Data -->
<span class="badge badge-soft-cyan">PO Only</span>

<!-- DAILY/SPOT / Tertiary Info -->
<span class="badge badge-soft-yellow">Daily Rental</span>

<!-- Special / Premium / VIP -->
<span class="badge badge-soft-purple">Premium Customer</span>

<!-- URGENT / Critical Warnings -->
<span class="badge badge-soft-orange">Expiring Soon</span>
```

#### 3-Tier Urgency System (Expiry/Deadline)
```html
<!-- Expired / Overdue (< 0 days) -->
<span class="badge badge-soft-red">Expired 5 days ago</span>

<!-- Critical / Urgent (1-30 days) -->
<span class="badge badge-soft-orange">15 days left</span>

<!-- Monitor / Watch (31-90 days) -->
<span class="badge badge-soft-cyan">60 days left</span>
```

### Badge Size Variants
```html
<!-- Default size -->
<span class="badge badge-soft-blue">Default</span>

<!-- Small (untuk inline text atau table compact) -->
<span class="badge badge-soft-blue" style="font-size: 0.75rem;">Small</span>

<!-- Large (untuk headers atau emphasis) -->
<span class="badge badge-soft-blue fs-6">Large</span>
```

### Badge dengan Icon
```html
<!-- Icon di kiri -->
<span class="badge badge-soft-blue">
    <i class="fas fa-file-contract me-1"></i>Contract
</span>

<!-- Icon only (untuk status compact) -->
<span class="badge badge-soft-green">
    <i class="fas fa-check"></i>
</span>
```

### Common Badge Patterns

#### Module-Specific Examples

**Marketing Quotations:**
```html
<!-- Status -->
<span class="badge badge-soft-green">ACCEPTED</span>
<span class="badge badge-soft-cyan">SENT</span>
<span class="badge badge-soft-yellow">DRAFT</span>
<span class="badge badge-soft-red">REJECTED</span>

<!-- Version/Revision dengan monospace -->
<span class="badge badge-soft-blue font-monospace">Q-2026-001-v2</span>

<!-- Specification types -->
<span class="badge badge-soft-orange">Spare Unit</span>
<span class="badge badge-soft-green">Billable Operator</span>
<span class="badge badge-soft-gray">Non-billable</span>
```

**Contract Management:**
```html
<!-- Contract Types -->
<span class="badge badge-soft-blue">
    <i class="fas fa-file-contract me-1"></i>Contract
</span>
<span class="badge badge-soft-cyan">
    <i class="fas fa-file-invoice me-1"></i>PO Only
</span>
<span class="badge badge-soft-yellow">
    <i class="fas fa-calendar-day me-1"></i>Daily
</span>

<!-- Contract Numbers -->
<span class="badge badge-soft-blue font-monospace">WFT/PROC/AGR/I/2025/001</span>
```

**Customer Management:**
```html
<!-- Customer Status -->
<span class="badge badge-soft-green">ACTIVE</span>
<span class="badge badge-soft-red">INACTIVE</span>

<!-- Customer Code -->
<span class="badge badge-soft-blue font-monospace">CUST-0001</span>
```

---

## 📐 Page Layout Structure

### Standard Module Layout Pattern
Gunakan struktur ini untuk SEMUA module marketing:

```html
<?= $this->extend('layouts/base') ?>

<?php
/**
 * [Module Name] Module
 * 
 * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
 * Direct CSS classes - tidak perlu JavaScript helper function
 * 
 * Quick Reference:
 * - Status ACTIVE    → <span class="badge badge-soft-green">ACTIVE</span>
 * - Status PENDING   → <span class="badge badge-soft-yellow">PENDING</span>
 * [... module-specific reference ...]
 * 
 * See optima-pro.css line ~2030 for complete badge standards
 */
helper('simple_rbac');
$can_view = can_view('marketing');
$can_create = can_create('marketing');
$can_export = can_export('marketing');
?>

<?= $this->section('content') ?>

<!-- Statistics Cards (optional, jika ada dashboard) -->
<div class="row mt-3 mb-4">
    <!-- Stat cards... -->
</div>

<!-- Filter Tabs/Card (optional) -->
<div class="card mb-3">
    <!-- Filters... -->
</div>

<!-- Main Data Table Card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-[icon] me-2 text-primary"></i>
                [Module Title]
            </h5>
            <p class="text-muted small mb-0">
                [Module description]
                <span class="ms-2 text-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <small>Tip: [User tip]</small>
                </span>
            </p>
        </div>
        <div class="d-flex gap-2">
            <!-- Action buttons -->
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="[module]Table" class="table table-striped table-hover mb-0">
                <!-- Table content -->
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
```

### Page Header - INSIDE Card Header ✅
```html
<!-- ✅ CORRECT - Header di dalam card -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-people me-2 text-primary"></i>
                Customer Management
            </h5>
            <p class="text-muted small mb-0">
                Manage customer profiles, contracts, and track unit deployments
                <span class="ms-2 text-info">
                    <i class="bi bi-keyboard me-1"></i>
                    <small>Tip: Click row or press Tab + Enter to view details</small>
                </span>
            </p>
        </div>
        <!-- Actions di kanan -->
    </div>
    ...
</div>
```

```html
<!-- ❌ WRONG - Standalone header di luar card -->
<div class="mb-3">
    <h4 class="fw-bold mb-1">Customer Management</h4>
    <p class="text-muted mb-0">Manage customer profiles...</p>
</div>
<div class="card">
    <!-- Card tanpa header info -->
</div>
```

### Statistics Cards Layout
```html
<div class="row mt-3 mb-4">
    <div class="col-xl-3 col-lg-6 col-md-6 mb-3">
        <div class="stat-card bg-primary-soft">
            <div class="d-flex align-items-center">
                <div class="me-3">
                    <i class="bi bi-people stat-icon text-primary"></i>
                </div>
                <div>
                    <div class="stat-value" id="stat-total">0</div>
                    <div class="text-muted">Total Items</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Repeat untuk stats lain dengan bg-success-soft, bg-warning-soft, bg-info-soft -->
</div>
```

**Color Mapping untuk Stat Cards:**
- `bg-primary-soft` → Total/General count
- `bg-success-soft` → Active/Positive metrics
- `bg-warning-soft` → Warning/Attention metrics
- `bg-info-soft` → Informational metrics
- `bg-danger-soft` → Critical/Negative metrics (jarang digunakan)

---

## 📊 Table Design

### Standard DataTable Structure
```html
<div class="card table-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <!-- Header content -->
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="dataTable" class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Column 1</th>
                        <th>Column 2</th>
                        <!-- ... -->
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate -->
                </tbody>
            </table>
        </div>
    </div>
</div>
```

### Table Cell Content Patterns

#### ID/Code Columns (monospace dengan badge)
```javascript
// DataTables render
{
    data: 'contract_no',
    render: function(data, type, row) {
        return `<span class="badge badge-soft-blue font-monospace" style="font-size: 0.75rem;">${data}</span>`;
    }
}
```

#### Numeric Columns (counts)
```javascript
{
    data: 'total_units',
    className: 'text-center',
    render: function(data, type, row) {
        return `<span class="badge badge-soft-blue">${data || 0}</span>`;
    }
}
```

#### Currency Columns
```javascript
{
    data: 'value',
    className: 'text-end',
    render: function(data, type, row) {
        if (!data || data === '—') return '—';
        return `<span class="text-success fw-semibold">${data}</span>`;
    }
}
```

#### Status Columns
```javascript
{
    data: 'status',
    render: function(data, type, row) {
        const colorMap = {
            'ACTIVE': 'badge-soft-green',
            'PENDING': 'badge-soft-yellow',
            'EXPIRED': 'badge-soft-red',
            'CANCELLED': 'badge-soft-gray'
        };
        const badgeClass = colorMap[data] || 'badge-soft-gray';
        return `<span class="badge ${badgeClass}">${data || 'N/A'}</span>`;
    }
}
```

#### Date Columns dengan Expiry Warning
```javascript
{
    data: 'end_date',
    render: function(data, type, row) {
        const dateStr = data ? new Date(data).toLocaleDateString('id-ID') : '—';
        let badge = '';
        
        if (row.days_remaining !== null) {
            if (row.days_remaining < 0) {
                badge = `<br><span class="badge badge-soft-red">Expired ${Math.abs(row.days_remaining)} days ago</span>`;
            } else if (row.days_remaining <= 30) {
                badge = `<br><span class="badge badge-soft-orange">${row.days_remaining} days left</span>`;
            } else if (row.days_remaining <= 90) {
                badge = `<br><span class="badge badge-soft-cyan">${row.days_remaining} days left</span>`;
            }
        }
        
        return `<small class="text-muted">${dateStr}</small>${badge}`;
    }
}
```

### Table Row Actions
```html
<!-- Standard action buttons -->
<div class="btn-group btn-group-sm" role="group">
    <button class="btn btn-outline-primary btn-sm" onclick="viewDetail(${id})" title="View Details">
        <i class="fas fa-eye"></i>
    </button>
    <?php if ($can_edit): ?>
    <button class="btn btn-outline-warning btn-sm" onclick="editRecord(${id})" title="Edit">
        <i class="fas fa-edit"></i>
    </button>
    <?php endif; ?>
    <?php if ($can_delete): ?>
    <button class="btn btn-outline-danger btn-sm" onclick="deleteRecord(${id})" title="Delete">
        <i class="fas fa-trash"></i>
    </button>
    <?php endif; ?>
</div>
```

---

## 🎛️ Filter & Form Components

### Filter Card Structure
```html
<div class="card mb-3">
    <div class="card-header bg-light">
        <h6 class="mb-0">
            <i class="fas fa-filter me-2 text-primary"></i>Filters
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label for="filter_status" class="form-label fw-semibold">
                    <i class="fas fa-check-circle text-success me-1"></i>Status
                </label>
                <select class="form-select" id="filter_status">
                    <option value="">🔍 All Status</option>
                    <option value="ACTIVE">✅ Active</option>
                    <option value="PENDING">⏳ Pending</option>
                    <option value="EXPIRED">❌ Expired</option>
                </select>
            </div>
            <!-- More filters -->
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-primary w-100" onclick="applyFilters()">
                    <i class="fas fa-search me-1"></i>Apply
                </button>
            </div>
        </div>
    </div>
</div>
```

### Dropdown Options Enhancement

**NOTE:** Dropdown filters may be replaced with tab-based navigation in some modules (e.g., Contract Management).

```html
<!-- Status Dropdown - Clean, text-only -->
<select class="form-select" id="status">
    <option value="">All Status</option>
    <option value="ACTIVE">Active</option>
    <option value="PENDING">Pending</option>
    <option value="EXPIRED">Expired</option>
    <option value="CANCELLED">Cancelled</option>
</select>

<!-- Type Dropdown -->
<select class="form-select" id="type">
    <option value="">All Types</option>
    <option value="CONTRACT">Contract</option>
    <option value="PO_ONLY">PO Only</option>
    <option value="DAILY_SPOT">Daily/Spot</option>
</select>

<!-- Customer Dropdown (populated via AJAX) -->
<select class="form-select" id="customer">
    <option value="">All Customers</option>
    <!-- Populated with format: CUST-0001 - Customer Name -->
</select>
```

### Form Label dengan Icon
```html
<label for="field_name" class="form-label fw-semibold">
    <i class="fas fa-[icon] text-[color] me-1"></i>Field Name
</label>
```

**Icon Colors untuk Labels:**
- `text-primary` → General/main fields
- `text-success` → Positive/active fields
- `text-info` → Informational fields
- `text-warning` → Warning/attention fields
- `text-danger` → Critical/required fields

---

## 🪟 Modal Design

### Standard Modal Structure
```html
<div class="modal fade modal-wide" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title">
                        <i class="bi bi-[icon] me-2"></i>
                        <span id="modalTitle">Modal Title</span>
                    </h5>
                    <small class="text-muted" id="modalSubtitle"></small>
                </div>
                <div class="d-flex gap-2">
                    <!-- Optional action buttons -->
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="printModal()">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <div class="modal-body">
                <!-- Tabbed content (optional) -->
                <ul class="nav nav-tabs mb-3" id="modalTabs">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab1">
                            <i class="fas fa-info-circle me-1"></i>Info
                        </button>
                    </li>
                    <!-- More tabs -->
                </ul>
                <div class="tab-content">
                    <div class="tab-pane fade show active" id="tab1">
                        <!-- Content -->
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
```

### Modal Content Display Pattern
```html
<!-- Info Grid Layout -->
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="text-muted small">Field Label</label>
        <p class="fw-semibold">Field Value</p>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="text-muted small">Status</label>
        <p><span class="badge badge-soft-green">ACTIVE</span></p>
    </div>
    
    <div class="col-md-4 mb-3">
        <label class="text-muted small">Contract Number</label>
        <p><span class="badge badge-soft-blue font-monospace">WFT-001</span></p>
    </div>
    
    <div class="col-md-6 mb-3">
        <label class="text-muted small">Total Value</label>
        <p><span class="text-success fw-semibold fs-5">Rp 50.000.000</span></p>
    </div>
</div>
```

---

## 🎨 Color Palette

### Optima Soft Badge Colors (optima-pro.css)
```css
/* Available badge classes */
.badge-soft-green   /* #d4edda / Success / Active */
.badge-soft-yellow  /* #fff3cd / Warning / Pending */
.badge-soft-red     /* #f8d7da / Danger / Expired */
.badge-soft-gray    /* #e2e3e5 / Neutral / Cancelled */
.badge-soft-blue    /* #cfe2ff / Primary / Info */
.badge-soft-cyan    /* #cff4fc / Info / Secondary */
.badge-soft-purple  /* #e0cffc / Special / Premium */
.badge-soft-orange  /* #ffe5b4 / Urgent / Warning */
```

### Text Colors untuk Emphasis
```html
<!-- Success (revenue, positive metrics) -->
<span class="text-success fw-semibold">Rp 10.000.000</span>

<!-- Muted (supporting info) -->
<small class="text-muted">Additional info</small>

<!-- Danger (overdue, critical) -->
<span class="text-danger fw-bold">Overdue 30 days</span>

<!-- Info (links, secondary info) -->
<a href="#" class="text-info">View Details</a>

<!-- Warning (attention needed) -->
<span class="text-warning">Pending Approval</span>
```

### Semantic Color Meanings
| Color | Meaning | Use Cases |
|-------|---------|-----------|
| **Green** | Success, Active, Positive | ACTIVE status, paid invoices, available units |
| **Yellow** | Warning, Pending, Waiting | PENDING status, awaiting approval |
| **Red** | Danger, Expired, Critical | EXPIRED contracts, overdue payments, cancelled |
| **Gray** | Neutral, Inactive, Disabled | CANCELLED status, archived records |
| **Blue** | Primary, Info, Counts | Contract types, IDs, counters |
| **Cyan** | Secondary, Supporting | PO types, additional info |
| **Orange** | Urgent, Critical Warning | Expiring soon, needs attention |
| **Purple** | Special, Premium, VIP | Premium customers, special features |

---

## 🧩 Global Utility Patterns (v2.1)

> Pola reusable yang wajib digunakan di semua modul mulai March 2026.  
> Semua class tersedia di `optima-pro.css` (bagian GLOBAL UTILITIES).

---

### Page Title & Subtitle

Untuk page header yang berdiri di **luar** card (breadcrumb / standalone title sebelum card grid):

```html
<div class="mb-4">
    <h4 class="page-title">
        <i class="fas fa-truck me-2 text-primary"></i>Surat Jalan
    </h4>
    <p class="page-subtitle">Manage perpindahan unit antar workshop</p>
</div>
```

```css
/* di optima-pro.css */
.page-title   { font-size: 1.25rem; font-weight: 700; margin-bottom: 0.25rem; color: #212529; }
.page-subtitle{ font-size: 0.875rem; color: #6c757d; margin-bottom: 0; }
```

---

### Card-Table Pattern

Untuk card yang isinya **tabel**. Menggabungkan `card-body p-0`, `table-responsive`, `table mb-0`, `thead table-light`:

```html
<div class="card card-table">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-primary"></i>Title
            </h5>
            <p class="text-muted small mb-0">Subtitle / description</p>
        </div>
        <div class="d-flex gap-2"><!-- action buttons --></div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Column</th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
```

---

### Filter Card Pattern

```html
<div class="card filter-card mb-3">
    <div class="card-header bg-light py-2">
        <h6 class="mb-0">
            <i class="fas fa-filter me-2 text-primary"></i>Filter
        </h6>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label fw-semibold">
                    <i class="fas fa-circle-check text-success me-1"></i>Status
                </label>
                <select class="form-select">
                    <option value="">All Status</option>
                    <option value="ACTIVE">Active</option>
                </select>
            </div>
        </div>
    </div>
</div>
```

---

### Toolbar Pattern

Untuk baris tombol aksi (Export, Add, Refresh) di card-header agar **auto-wrap** di mobile:

```html
<div class="toolbar">
    <button class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-sync me-1"></i>Refresh
    </button>
    <button class="btn btn-success btn-sm">
        <i class="fas fa-file-excel me-1"></i>Export
    </button>
    <button class="btn btn-primary btn-sm">
        <i class="fas fa-plus me-1"></i>Add
    </button>
</div>
```

```css
/* di optima-pro.css */
.toolbar { display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center; }
```

---

### Mono-label / Code-badge

Untuk nomor penting (kontrak, invoice, WO, unit, customer code):

```html
<!-- Dalam tabel -->
<span class="code-badge">WFT/2026/001</span>

<!-- Dalam badge (tabel row atau detail card) -->
<span class="badge badge-soft-blue font-monospace">CUST-0001</span>
```

```css
/* di optima-pro.css */
.code-badge { font-family: var(--bs-font-monospace); font-size: 0.8rem;
              background: #e7f1ff; color: #084298; padding: 0.2em 0.5em;
              border-radius: 4px; white-space: nowrap; }
```

---

### Chip / Tag

Untuk label kecil (tipe, sumber, model, tag) di detail view:

```html
<!-- Chip default -->
<span class="chip">Forklift</span>

<!-- Chip dengan warna soft -->
<span class="chip chip-blue">Attachment</span>
<span class="chip chip-green">Warehouse</span>
<span class="chip chip-yellow">Bekas</span>
<span class="chip chip-gray">Tool</span>
```

```css
/* di optima-pro.css */
.chip         { display:inline-block; padding:0.25em 0.6em; border-radius:20px;
                font-size:0.78rem; font-weight:500; background:#e9ecef; color:#495057; }
.chip-blue    { background:#e7f1ff; color:#084298; }
.chip-green   { background:#d1e7dd; color:#0a3622; }
.chip-yellow  { background:#fff3cd; color:#664d03; }
.chip-red     { background:#f8d7da; color:#58151c; }
.chip-gray    { background:#e9ecef; color:#495057; }
.chip-cyan    { background:#cff4fc; color:#055160; }
.chip-orange  { background:#ffe5d0; color:#653208; }
```

---

### Table Compact

Untuk tabel di dalam modal atau detail panel (font lebih kecil, padding lebih padat):

```html
<table class="table table-sm table-compact mb-0">
    <thead class="table-light"><tr><th>...</th></tr></thead>
    <tbody>...</tbody>
</table>
```

```css
/* di optima-pro.css */
.table-compact td, .table-compact th { font-size: 0.8rem; padding: 0.3rem 0.5rem; }
```

---

## 📝 Typography

### Font Weights
```html
<!-- Regular (default) -->
<p class="fw-normal">Regular text</p>

<!-- Semibold (untuk emphasis moderat) -->
<p class="fw-semibold">Emphasized text</p>

<!-- Bold (untuk strong emphasis) -->
<p class="fw-bold">Strong text</p>

<!-- Light (jarang digunakan) -->
<p class="fw-light">Light text</p>
```

### Font Sizes
```html
<!-- Heading sizes -->
<h1 class="display-4">Display Header</h1>
<h5 class="card-title">Card Title</h5>

<!-- Body text -->
<p>Regular paragraph</p>
<small class="text-muted">Small supporting text</small>

<!-- Custom sizes -->
<span class="fs-6">Font size 6</span>
<span class="fs-5">Font size 5</span>
```

### Monospace untuk Codes/IDs
```html
<!-- Contract numbers, customer codes, unit IDs -->
<span class="font-monospace">WFT/PROC/AGR/I/2025/001</span>

<!-- Dalam badge -->
<span class="badge badge-soft-blue font-monospace">CUST-0001</span>
```

---

## 🎯 Icons & Emoji Usage

### Bootstrap Icons (bi-*)
Digunakan untuk UI elements utama:
```html
<i class="bi bi-people"></i>          <!-- People/Customers -->
<i class="bi bi-file-earmark-text"></i> <!-- Contracts/Documents -->
<i class="bi bi-building"></i>         <!-- Company/Building -->
<i class="bi bi-truck"></i>            <!-- Units/Vehicles -->
<i class="bi bi-info-circle"></i>      <!-- Information -->
<i class="bi bi-check-circle"></i>     <!-- Success/Active -->
<i class="bi bi-x-circle"></i>         <!-- Cancel/Inactive -->
```

### Font Awesome Icons (fas fa-*)
Digunakan untuk actions dan details:
```html
<i class="fas fa-eye"></i>         <!-- View -->
<i class="fas fa-edit"></i>        <!-- Edit -->
<i class="fas fa-trash"></i>       <!-- Delete -->
<i class="fas fa-plus"></i>        <!-- Add -->
<i class="fas fa-search"></i>      <!-- Search -->
<i class="fas fa-filter"></i>      <!-- Filter -->
<i class="fas fa-file-contract"></i> <!-- Contract -->
<i class="fas fa-file-invoice"></i>  <!-- Invoice/PO -->
```

### Emoji Usage Guidelines

**Limited Use - Optional Enhancement Only**

```html
<!-- Console logs (debugging) -->
console.log('✅ Data loaded successfully');
console.error('❌ Failed to load data');

<!-- Notification messages -->
<div class="alert alert-success">✅ Contract created successfully!</div>
<div class="alert alert-warning">⚠️ Contract expires in 30 days</div>
```

**Emoji Guidelines:**
- ✅ DO: Use in console logs for debugging clarity
- ✅ DO: Use in notification/alert messages
- ✅ DO: Use in tips and helper text
- ❌ DON'T: Use in dropdown options (keep clean/professional)
- ❌ DON'T: Use in table headers or modal titles
- ❌ DON'T: Use in formal business documents

**Note:** Some modules (like Contract Management) may use tab-based navigation instead of dropdown filters.

---

## 🚀 Quick Reference Cheatsheet

### Badge Quick Reference
```
STATUS:
✅ ACTIVE      → badge-soft-green
⏳ PENDING     → badge-soft-yellow
❌ EXPIRED     → badge-soft-red
⛔ CANCELLED   → badge-soft-gray

TYPES:
📄 CONTRACT    → badge-soft-blue
📋 PO_ONLY     → badge-soft-cyan
📅 DAILY_SPOT  → badge-soft-yellow

URGENCY (Expiry):
🔴 Expired       → badge-soft-red
🟠 Critical <30d → badge-soft-orange
🔵 Monitor 31-90d→ badge-soft-cyan

COUNTERS:
🔢 Unit counts   → badge-soft-blue
💰 Total value   → text-success fw-semibold
```

### CSS Class Quick Reference
```
LAYOUT:
.card-header bg-light  → Card header (standar OPTIMA)
.card-body p-0         → Table card body (no padding)
.table-responsive      → Responsive table wrapper
.card-table            → Card yang isinya tabel (utility pola)
.filter-card           → Card filter/search section
.toolbar               → Baris tombol aksi (auto-wrap di mobile)
.page-title            → Judul halaman standalone
.page-subtitle         → Subtitle/deskripsi halaman

TABLE:
.table-striped         → Striped rows
.table-hover           → Hover effect
.table-light           → Light header background
.table-compact         → Compact tabel (modal/detail panel)

TEXT & LABELS:
.code-badge            → Nomor penting (kontrak, invoice, WO) - monospace
.chip                  → Label/tag kecil (neutral)
.chip-blue/green/etc.  → Label/tag dengan warna soft

TEXT:
.text-muted            → Gray supporting text
.text-success          → Green (values, positive)
.text-danger           → Red (critical)
.text-info             → Blue (links)
.fw-semibold           → Semibold weight
.fw-bold               → Bold weight
.font-monospace        → Monospace font

ALIGNMENT:
.text-start            → Left align
.text-center           → Center align
.text-end              → Right align

SPACING:
.mt-3, .mb-4, .p-0     → Margin/Padding (0-5)
.gap-2, .gap-3         → Gap between flex items
```

### DataTables Column Rendering Patterns
```javascript
// ID/Code Column
{ data: 'code', render: (d) => `<span class="badge badge-soft-blue font-monospace">${d}</span>` }

// Count Column
{ data: 'count', className: 'text-center', render: (d) => `<span class="badge badge-soft-blue">${d}</span>` }

// Currency Column
{ data: 'value', className: 'text-end', render: (d) => d ? `<span class="text-success fw-semibold">${d}</span>` : '—' }

// Status Column
{ data: 'status', render: (d) => {
    const colors = { ACTIVE:'green', PENDING:'yellow', EXPIRED:'red', CANCELLED:'gray' };
    return `<span class="badge badge-soft-${colors[d]}">${d}</span>`;
}}

// Date with Warning
{ data: 'date', render: (d, t, row) => {
    let badge = '';
    if (row.days < 0) badge = `<br><span class="badge badge-soft-red">Expired</span>`;
    else if (row.days <= 30) badge = `<br><span class="badge badge-soft-orange">${row.days}d left</span>`;
    return `<small>${d}</small>${badge}`;
}}
```

---

## 📋 Implementation Checklist

Gunakan checklist ini saat mengupdate atau membuat module baru:

### ✅ Page Structure
- [ ] Module documentation comment di atas file (dengan badge reference)
- [ ] Permission checks (can_view, can_create, can_edit, can_delete, can_export)
- [ ] Statistics cards pakai `stat-card bg-*-soft` (jika applicable)
- [ ] Filter pakai `filter-card` pattern (jika ada filtering)
- [ ] Page header INSIDE card-header (bukan standalone), atau pakai `.page-title` + `.page-subtitle`
- [ ] Page title dengan icon Font Awesome / Bootstrap
- [ ] Subtitle dengan user tip
- [ ] Toolbar aksi (Export, Add, Refresh) pakai `.toolbar` di card-header kanan
- [ ] Nomor penting pakai `.code-badge` atau `badge-soft-blue font-monospace`
- [ ] Label/tag pakai `.chip .chip-*` (bukan inline badge generik)

### ✅ Badge Implementation
- [ ] Semua status badges menggunakan `badge-soft-*`
- [ ] ID/Code fields menggunakan `badge-soft-blue font-monospace`
- [ ] Count badges menggunakan `badge-soft-blue`
- [ ] Expiry warnings menggunakan 3-tier system (red/orange/cyan)
- [ ] Tidak ada `bg-*` badge classes (kecuali stat-cards)
- [ ] Tidak ada `text-dark` atau `text-white` overrides di badges

### ✅ Table Design
- [ ] DataTable menggunakan class `table table-striped table-hover mb-0`
- [ ] Table header menggunakan `table-light`
- [ ] Card body untuk table menggunakan `p-0`
- [ ] Table wrapped dalam `table-responsive`
- [ ] Currency columns aligned `text-end` dengan color green
- [ ] Count columns aligned `text-center` dengan badge
- [ ] Action column aligned `text-center`

### ✅ Typography & Colors
- [ ] Supporting text menggunakan `text-muted`
- [ ] Currency/values menggunakan `text-success fw-semibold`
- [ ] Monospace untuk codes/IDs (`font-monospace`)
- [ ] Proper font weights (fw-normal, fw-semibold, fw-bold)

### ✅ Buttons
- [ ] Aksi utama pakai `btn-primary` (submit, save, create, add)
- [ ] Aksi positif pakai `btn-success` (approve, confirm, export)
- [ ] Aksi peringatan pakai `btn-warning` (edit, rollback)
- [ ] Aksi destruktif pakai `btn-danger` (delete, reject, cancel)
- [ ] Aksi sekunder pakai `btn-outline-secondary` (filter, back, refresh, print)
- [ ] View/detail pakai `btn-outline-primary`
- [ ] Tidak ada `btn-info` di workflow utama
- [ ] Icon selalu di kiri dengan `me-1`
- [ ] Action column pakai `btn-group btn-group-sm` + `btn-icon-only`
- [ ] Toolbar aksi di card-header pakai `.toolbar`

### ✅ Pagination
- [ ] DataTables pagination menggunakan CSS global (tanpa custom inline)
- [ ] Pagination `.page-link` mengikuti gaya optima-pro.css
- [ ] Info text font-size konsisten

### ✅ Notifications & Confirmations
- [ ] Success/error notification pakai `OptimaNotify.success/error/warning/info`
- [ ] Konfirmasi penting/destruktif pakai `OptimaConfirm.*` (wrapper `Swal.fire`)
- [ ] Tidak ada `alert()` kecuali session-expired fallback
- [ ] Tidak ada `confirm()` — selalu `OptimaConfirm` / `Swal.fire`
- [ ] Warna confirm button sesuai fungsi (danger=red, success=green, primary=blue)
- [ ] Notifikasi (toast, dropdown, Notification Center) bersifat **read-only** (tanpa tombol aksi & tanpa redirect ke halaman lain)

### ✅ Dropdowns & Filters
- [ ] Filter labels dengan icon dan `fw-semibold`
- [ ] Dropdown options dengan emoji prefix
- [ ] Customer dropdown format: 🏢 CODE • Name
- [ ] "All" options dengan 🔍 emoji

### ✅ Icons & Visual Elements
- [ ] Bootstrap icons (bi-*) untuk main elements
- [ ] Font Awesome (fas fa-*) untuk actions
- [ ] Icon colors sesuai semantic (primary, success, info, warning, danger)
- [ ] Emoji hanya di dropdown, notifications, tips

### ✅ Modals
- [ ] Modal title dengan icon
- [ ] Modal subtitle untuk context info
- [ ] Tabbed content (jika applicable)
- [ ] Info fields dengan label `text-muted small`
- [ ] Values dengan proper formatting (badges, colors)

---

## 🔧 Migration Guide: Old → New

### Find & Replace Patterns

**Badge Classes:**
```
FIND: bg-success
REPLACE: badge-soft-green

FIND: bg-danger
REPLACE: badge-soft-red

FIND: bg-warning text-dark
REPLACE: badge-soft-yellow

FIND: bg-info text-dark
REPLACE: badge-soft-cyan

FIND: bg-primary
REPLACE: badge-soft-blue

FIND: bg-secondary
REPLACE: badge-soft-gray
```

**Text Classes yang Perlu Dihapus:**
```
REMOVE: text-dark (dari badge)
REMOVE: text-white (dari badge)
```

**Header Structure:**
```html
<!-- BEFORE -->
<div class="mb-3">
    <h4>Module Title</h4>
    <p>Description</p>
</div>
<div class="card">
    <div class="card-header">Actions</div>
    ...
</div>

<!-- AFTER -->
<div class="card table-card">
    <div class="card-header d-flex justify-content-between">
        <div>
            <h5 class="card-title mb-0">
                <i class="bi bi-icon me-2 text-primary"></i>
                Module Title
            </h5>
            <p class="text-muted small mb-0">
                Description
                <span class="ms-2 text-info">
                    <i class="bi bi-info-circle me-1"></i>
                    <small>Tip: User tip</small>
                </span>
            </p>
        </div>
        <div>Actions</div>
    </div>
    ...
</div>
```

---

## 📚 Reference Files

**Live Examples untuk di-copy:**
- ✅ `app/Views/marketing/customer_management.php` - Perfect implementation
- ✅ `app/Views/marketing/quotations.php` - Complete with all badge types
- ✅ `app/Views/marketing/kontrak.php` - Recently updated (March 2026)

**CSS Reference:**
- ✅ `public/assets/css/optima-pro.css` - Badge definitions (~line 2030)

**Memory Reference:**
- ✅ `/memories/optima-badge-standards.md` - Badge color standards

---

## 🎯 Cursor AI Prompt Template

Gunakan prompt ini di Cursor AI untuk update CSS module:

```
Update this module to follow Optima CSS Visual Standards (docs/CSS_VISUAL_STANDARDS.md):

CRITICAL REQUIREMENTS:
1. Move page header INSIDE card-header (not standalone above card)
2. Replace ALL bg-* badge classes with badge-soft-* semantic classes
3. Add module documentation comment at top with badge reference
4. Clean dropdown options (text-only, no emoji)
5. Use badge-soft-blue for IDs/codes with font-monospace
6. Use badge-soft-blue for count badges
7. Use text-success fw-semibold for currency values
8. Implement 3-tier expiry warning system (red/orange/cyan)
9. Add icon to filter labels with fw-semibold
10. Add user tip in page subtitle with info icon

REFERENCE:
- Good example: app/Views/marketing/customer_management.php
- Badge system: badge-soft-green (ACTIVE), badge-soft-yellow (PENDING), badge-soft-red (EXPIRED)
- Currency format: <span class="text-success fw-semibold">Rp X</span>
- Code format: <span class="badge badge-soft-blue font-monospace">CODE</span>
- Dropdown format: <option value="ACTIVE">Active</option> (clean text)

DO NOT:
- Use bg-success, bg-danger, bg-warning in badges
- Add text-dark or text-white to badges
- Leave header outside card structure
- Mix badge standards with old Bootstrap classes
- Add emoji to dropdown options (keep professional)
```

---

**Last Updated:** March 12, 2026  
**Maintained by:** Development Team  
**Questions?** Refer to `docs/BADGE_STANDARDS.md` for badge quick reference

---

## 🔘 Button System (v2.2)

> Mapping warna tombol ke **fungsi**, bukan estetika.
> Semua class tersedia di Bootstrap 5 + utilitas tambahan di `optima-pro.css`.

### Semantic Button Mapping

| Fungsi | Class | Contoh |
|--------|-------|--------|
| **Aksi Utama** (Submit, Save, Create, Add) | `.btn-primary` | `<button class="btn btn-primary btn-sm"><i class="fas fa-plus me-1"></i>Tambah</button>` |
| **Aksi Positif** (Approve, Confirm, Verify) | `.btn-success` | `<button class="btn btn-success btn-sm"><i class="fas fa-check me-1"></i>Approve</button>` |
| **Aksi Peringatan** (Rollback, Reset, Edit) | `.btn-warning btn-sm` | `<button class="btn btn-warning btn-sm"><i class="fas fa-edit me-1"></i>Edit</button>` |
| **Aksi Destruktif** (Delete, Cancel, Reject) | `.btn-danger` | `<button class="btn btn-danger btn-sm"><i class="fas fa-trash me-1"></i>Hapus</button>` |
| **Aksi Sekunder** (Filter, Back, Detail, Refresh) | `.btn-outline-secondary` | `<button class="btn btn-outline-secondary btn-sm"><i class="fas fa-sync me-1"></i>Refresh</button>` |
| **Export** (Excel, CSV, PDF) | `.btn-success btn-sm` | `<button class="btn btn-success btn-sm"><i class="fas fa-file-excel me-1"></i>Export</button>` |
| **View/Detail** | `.btn-outline-primary btn-sm` | `<button class="btn btn-outline-primary btn-sm"><i class="fas fa-eye"></i></button>` |
| **Print** | `.btn-outline-secondary btn-sm` | `<button class="btn btn-outline-secondary btn-sm"><i class="fas fa-print me-1"></i>Print</button>` |
| **Close/Back** | `.btn-secondary` atau `.btn-outline-secondary` | `<button class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>` |

### Button Rules

1. **Ukuran**: Gunakan `.btn-sm` di tabel, toolbar, dan card-header. Gunakan default size di form/modal footer.
2. **Icon di kiri**: Selalu taruh icon sebelum teks dengan `me-1`.
3. **Icon-only buttons** (di action column tabel): Gunakan `.btn-icon-only` untuk padding konsisten.
4. **Toolbar pattern**: Kumpulan tombol di card-header kanan, bungkus dengan `.toolbar`.
5. **Jangan pakai `btn-info`** untuk aksi utama — gunakan `btn-primary` atau `btn-outline-primary`.

### Action Column di Tabel

```html
<td class="text-center">
    <div class="btn-group btn-group-sm" role="group">
        <button class="btn btn-outline-primary btn-icon-only" title="View"><i class="fas fa-eye"></i></button>
        <button class="btn btn-outline-warning btn-icon-only" title="Edit"><i class="fas fa-edit"></i></button>
        <button class="btn btn-outline-danger btn-icon-only" title="Delete"><i class="fas fa-trash"></i></button>
    </div>
</td>
```

---

## 📊 Table & Pagination (v2.2)

### Standard Table Pattern

Semua tabel data utama **wajib** mengikuti pola ini:

```html
<div class="card card-table">
    <div class="card-header bg-light d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="card-title mb-0"><i class="fas fa-list me-2 text-primary"></i>Title</h5>
        </div>
        <div class="toolbar"><!-- action buttons --></div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Column</th></tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
```

### DataTables Pagination

DataTables pagination otomatis mengikuti gaya Optima melalui CSS global di `optima-pro.css`.
Aturan:
- Gunakan `.dataTables_wrapper .dataTables_paginate` styling dari CSS global.
- Pagination `.page-link` mendapat border-radius dan hover yang seragam.
- Info text (Showing X to Y) menggunakan font-size yang konsisten.

### Table Variants

| Variant | Class | Use Case |
|---------|-------|----------|
| Default | `table table-striped table-hover mb-0` | Tabel data utama |
| Compact | `table table-sm table-compact mb-0` | Tabel di modal/detail panel |
| Borderless | `table table-borderless table-sm` | Tabel info key-value |
| Bordered | `table table-bordered table-sm mb-0` | Tabel form/input (PO items, sparepart validation) |

---

## 🔔 Notifications & Confirmations (v2.2)

### Notification Hierarchy

Optima menggunakan **unified toast system** yang sudah terdaftar di `layouts/base.php`:

```
OptimaNotify.success/error/warning/info  →  createOptimaToast  →  toast popup
OptimaPro.showNotification(msg, type)    →  createOptimaToast  →  toast popup
Swal.fire (simple, non-confirm)          →  auto-rerouted to toast via monkeypatch
Swal.fire (with showCancelButton)        →  stays as SweetAlert2 modal
```

### Standard Notification Calls

```javascript
// Preferred — use OptimaNotify
OptimaNotify.success('Data berhasil disimpan');
OptimaNotify.error('Gagal menyimpan data');
OptimaNotify.warning('Perhatian: data akan dihapus');
OptimaNotify.info('Memuat data...');

// Alternative — OptimaPro (legacy, still supported)
OptimaPro.showNotification('Berhasil', 'success');

// SweetAlert2 — ONLY for confirmations / complex dialogs
Swal.fire({
    title: 'Konfirmasi',
    text: 'Yakin ingin menghapus?',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc3545',
    confirmButtonText: 'Ya, Hapus!',
    cancelButtonText: 'Batal'
}).then((result) => {
    if (result.isConfirmed) { /* delete action */ }
});
```

### Confirmation Patterns

| Tipe | Icon | Confirm Button | Cancel Button |
|------|------|----------------|---------------|
| **Delete** | `warning` | `btn-danger` "Ya, Hapus!" | "Batal" |
| **Approve** | `question` | `btn-success` "Ya, Approve!" | "Batal" |
| **Submit/Send** | `question` | `btn-primary` "Ya, Kirim!" | "Batal" |
| **Rollback** | `warning` | `btn-warning` "Ya, Rollback!" | "Batal" |
| **Logout** | `question` | `btn-secondary` "Ya, Logout" | "Batal" |

### Rules

1. **Jangan pakai `alert()`** kecuali session-expired fallback.
2. **Jangan pakai `confirm()`** — selalu gunakan `Swal.fire` dengan `showCancelButton: true`.
3. **Simple success/error → OptimaNotify**, bukan Swal.fire.
4. **Swal.fire tanpa showCancelButton** otomatis di-redirect ke toast oleh monkeypatch di base.php.

---

## 🧹 Inline Style Rules (v2.2)

### Pola yang Harus Diganti dengan Class

| Inline Style | Ganti dengan |
|-------------|--------------|
| `style="cursor:pointer"` | `.cursor-pointer` |
| `style="font-size: 0.75rem"` / `0.8rem` | `.small` atau `.fs-*` Bootstrap |
| `style="min-width: 100px"` (badge label) | `.chip` atau `.chip-label` |
| `style="display:none"` (toggle) | `d-none` Bootstrap |
| `style="font-size: 10px"` (micro badge) | `.badge` + `style` hanya jika unik |

### Prinsip

- Jika pola muncul di **2+ modul**, pindahkan ke utilitas di `optima-pro.css`.
- Jika hanya **1 file, 1 tempat**, boleh inline (tapi prefer class).
- **Print styles** boleh tetap inline karena sifatnya lokal.

---

## 🗺️ Module Progress

Progress implementasi standar ini tersimpan di `docs/CURSOR_AI_WORKFLOW.md`  
bagian **"Progress Tambahan (Seluruh Web)"**.

Cek tabel tersebut untuk status tiap halaman:
- ✅ DONE — sudah sesuai standar
- ⏳ TODO — belum disentuh / masih punya `badge bg-*` atau inline style
