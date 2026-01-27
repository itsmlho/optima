# Migration Guide - Inline CSS ke Centralized CSS

Panduan lengkap untuk mengganti inline CSS dengan class-based CSS terpusat menggunakan SB Admin Pro components.

---

## 📋 Persiapan

### 1. Pastikan CSS Master Sudah Ter-load

Di `app/Views/layouts/base.php`, pastikan baris ini ada:

```php
<!-- SB Admin Pro Master CSS (includes all components) -->
<link href="<?= base_url('assets/css/sb-admin-pro-master.css') ?>?v=<?= time() ?>" rel="stylesheet">
```

### 2. CSS Files yang Sudah Tersedia

✅ **sb-admin-pro-master.css** - Master file yang import semua
✅ **sb-admin-pro-components.css** - Core components (cards, buttons, tables, forms)
✅ **sb-admin-pro-datatables.css** - DataTables styling
✅ **sb-admin-pro-select2.css** - Select2 dropdown styling
✅ **sb-admin-utilities.css** - Utility classes (NEW)
✅ **sb-admin-advanced-components.css** - Advanced components (NEW)

---

## 🔄 Pattern Migration - Before & After

### 1. WIDTH & HEIGHT

#### Before (Inline):
```html
<div style="width: 200px;">Content</div>
<div style="min-width: 200px;">Content</div>
<div style="height: 300px;">Content</div>
<select style="width: auto;">...</select>
```

#### After (Class-based):
```html
<div class="w-200px">Content</div>
<div class="min-w-200px">Content</div>
<div class="h-300px">Content</div>
<select class="form-select-auto">...</select>
```

#### Available Classes:
```
Width: w-32px, w-40px, w-150px, w-200px, w-350px
Min-Width: min-w-35px, min-w-100px, min-w-200px, min-w-350px
Height: h-10px, h-16px, h-20px, h-22px, h-32px, h-40px, h-80px, h-120px, h-200px, h-250px, h-300px
Min-Height: min-h-80px, min-h-120px, min-h-200px
```

---

### 2. DISPLAY & VISIBILITY

#### Before (Inline):
```html
<div id="mySection" style="display: none;">Hidden content</div>
<div style="cursor: pointer;" onclick="doSomething()">Clickable</div>
```

#### After (Class-based):
```html
<div id="mySection" class="d-none-init">Hidden content</div>
<div class="cursor-pointer" onclick="doSomething()">Clickable</div>
```

#### Available Classes:
```
Display: d-none-init (untuk JavaScript toggle)
Cursor: cursor-pointer, cursor-default, cursor-not-allowed
```

---

### 3. SCROLLABLE CONTAINERS

#### Before (Inline):
```html
<div style="max-height: 200px; overflow-y: auto;">
    Long list content...
</div>
```

#### After (Class-based):
```html
<div class="max-h-200px-scroll">
    Long list content...
</div>

<!-- OR use component class -->
<div class="scrollable-list">
    Long list content...
</div>
```

#### Available Classes:
```
Scroll: max-h-200px-scroll, max-h-350px-scroll, max-h-400px-scroll
Components: scrollable-list, scrollable-list-lg, scrollable-list-xl
```

---

### 4. BORDER RADIUS

#### Before (Inline):
```html
<div style="border-radius: 12px;">Content</div>
<div style="border-radius: 12px 12px 0 0;">Header</div>
```

#### After (Class-based):
```html
<div class="rounded-12px">Content</div>
<div class="rounded-top-12px">Header</div>
```

#### Available Classes:
```
Rounded: rounded-4px, rounded-5px, rounded-6px, rounded-8px, rounded-12px, rounded-20px
Top: rounded-top-12px, rounded-top-8px
Bottom: rounded-bottom-12px, rounded-bottom-8px
```

---

### 5. TABLE COLUMN WIDTHS

#### Before (Inline):
```html
<th style="width: 5%;">No</th>
<th style="width: 40%;">Description</th>
<th style="width: 15%;">Qty</th>
```

#### After (Class-based):
```html
<th class="col-w-5">No</th>
<th class="col-w-40">Description</th>
<th class="col-w-15">Qty</th>
```

#### Available Classes:
```
Columns: col-w-5, col-w-10, col-w-15, col-w-20, col-w-25, col-w-30, 
         col-w-35, col-w-40, col-w-45, col-w-50, col-w-55, col-w-60, 
         col-w-65, col-w-70
```

---

### 6. AVATAR COMPONENTS

#### Before (Inline):
```html
<img src="avatar.jpg" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #e3e6f0;">

<div style="width: 40px; height: 40px; border-radius: 50%; background: #007bff; color: white; display: flex; align-items: center; justify-content: center; border: 2px solid #e3e6f0;">
    <i class="fas fa-user" style="font-size: 18px;"></i>
</div>
```

#### After (Class-based):
```html
<img src="avatar.jpg" class="avatar-circle">

<div class="avatar-placeholder avatar-placeholder-md">
    <i class="fas fa-user"></i>
</div>
```

#### Available Classes:
```
Avatar: avatar-circle, avatar-circle-sm, avatar-circle-lg
Placeholder: avatar-placeholder-sm, avatar-placeholder-md, avatar-placeholder-lg
```

---

### 7. PROGRESS BARS

#### Before (Inline):
```html
<div class="progress" style="height: 20px;">
    <div class="progress-bar" style="width: 75%;"></div>
</div>
```

#### After (Class-based):
```html
<div class="progress progress-20px">
    <div class="progress-bar" style="width: 75%;"></div>
</div>
```

**Note**: Width tetap inline karena dynamic dari PHP/JavaScript

#### Available Classes:
```
Heights: progress-10px, progress-16px, progress-20px, progress-22px
```

---

### 8. MODAL COMPONENTS

#### Before (Inline):
```html
<div class="modal-content" style="border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.12);">
    <div class="modal-header" style="background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%); border-radius: 12px 12px 0 0;">
        <h5 style="font-weight: 600;">Title</h5>
    </div>
    <div class="modal-body" style="padding: 0;">
        Content
    </div>
</div>
```

#### After (Class-based):
```html
<div class="modal-content modal-content-gradient">
    <div class="modal-header modal-header-gradient">
        <h5 class="modal-title">Title</h5>
    </div>
    <div class="modal-body modal-body-no-padding">
        Content
    </div>
</div>
```

#### Available Classes:
```
Modal: modal-content-gradient, modal-header-gradient, modal-footer-gradient
Body: modal-body-no-padding, modal-body-gradient-section
```

---

### 9. BADGE VARIANTS

#### Before (Inline):
```html
<span class="badge bg-light text-dark" style="min-width: 100px;">Label:</span>
<span class="badge bg-light text-dark" style="min-width: 35px; text-align: center;">1</span>
```

#### After (Class-based):
```html
<span class="badge bg-light text-dark badge-label">Label:</span>
<span class="badge bg-light text-dark badge-index">1</span>
```

#### Available Classes:
```
Badges: badge-label, badge-label-sm, badge-label-lg
Index: badge-index
Custom: badge-light-custom
```

---

### 10. CHART CONTAINERS

#### Before (Inline):
```html
<canvas id="myChart" style="height: 300px;"></canvas>
```

#### After (Class-based):
```html
<div class="chart-container-lg">
    <canvas id="myChart"></canvas>
</div>
```

#### Available Classes:
```
Charts: chart-container-sm (200px), chart-container-md (250px), 
        chart-container-lg (300px), chart-container-xl (400px)
```

---

### 11. FORM CONTROLS

#### Before (Inline):
```html
<input type="text" class="form-control" readonly style="background-color: #f8f9fa;">
<select class="form-select" style="width: auto;">...</select>
<select class="form-select" id="mySelect" style="min-width: 200px;">...</select>
```

#### After (Class-based):
```html
<input type="text" class="form-control form-control-readonly" readonly>
<select class="form-select form-select-auto">...</select>
<select class="form-select min-w-200px" id="mySelect">...</select>
```

#### Available Classes:
```
Forms: form-control-readonly, form-select-auto, form-select-with-search
```

---

### 12. EMPTY STATES

#### Before (Inline):
```html
<p style="text-align: center; color: #999; font-style: italic;">
    Tidak ada data
</p>
```

#### After (Class-based):
```html
<div class="empty-state">
    <div class="empty-state-icon">
        <i class="fas fa-inbox"></i>
    </div>
    <div class="empty-state-title">Tidak ada data</div>
    <div class="empty-state-text">Data akan muncul di sini</div>
</div>
```

#### Available Classes:
```
Empty: empty-state, empty-state-icon, empty-state-title, empty-state-text
```

---

### 13. COLLAPSIBLE SECTIONS

#### Before (Inline):
```html
<div style="border-radius: 8px; border: 1px solid #e9ecef; background: #f8f9fa;">
    <div style="cursor: pointer; padding: 12px;" onclick="toggleSection()">
        <i class="fas fa-chevron-down" style="color: #6c757d;"></i>
        <span>Section Title</span>
    </div>
    <div id="content" class="d-none">
        Content here
    </div>
</div>
```

#### After (Class-based):
```html
<div class="spec-group">
    <div class="spec-group-header" onclick="toggleSection()">
        <i class="fas fa-chevron-down spec-group-icon"></i>
        <span>Section Title</span>
    </div>
    <div id="content" class="spec-group-content d-none">
        Content here
    </div>
</div>
```

#### Available Classes:
```
Collapsible: spec-group, spec-group-header, spec-group-content, spec-group-icon
```

---

### 14. PRINT LAYOUTS

#### Before (Inline):
```html
<div style="page-break-inside: avoid;">
    Content that shouldn't break
</div>

<div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px;">
    <div style="text-align: center;">
        <div style="border-bottom: 1px solid #000; width: 150px; margin: 0 auto; height: 50px;"></div>
        <div>Signature 1</div>
    </div>
    <div style="text-align: center;">
        <div style="border-bottom: 1px solid #000; width: 150px; margin: 0 auto; height: 50px;"></div>
        <div>Signature 2</div>
    </div>
</div>
```

#### After (Class-based):
```html
<div class="page-break-avoid">
    Content that shouldn't break
</div>

<div class="signature-section">
    <div class="signature-box">
        <div class="signature-date">Tgl: ___/___/_____</div>
        <div class="signature-label">Signature 1</div>
        <div class="signature-line"></div>
        <div class="signature-name">Name</div>
    </div>
    <div class="signature-box">
        <div class="signature-date">Tgl: ___/___/_____</div>
        <div class="signature-label">Signature 2</div>
        <div class="signature-line"></div>
        <div class="signature-name">Name</div>
    </div>
</div>
```

#### Available Classes:
```
Print: page-break-avoid, page-break-before, page-break-after
       print-header, print-footer, print-section
Signature: signature-section, signature-box, signature-date, 
           signature-label, signature-line, signature-name
```

---

### 15. NOTIFICATION COMPONENTS

#### Before (Inline):
```html
<span class="badge bg-danger" id="notificationBadge" style="display: none;">5</span>

<ul class="dropdown-menu" style="min-width: 350px; max-height: 400px; overflow-y: auto;">
    <li>Notification items...</li>
</ul>

<button class="btn" style="font-size: 0.7rem;" onclick="markAllAsRead()">
    Mark All Read
</button>
```

#### After (Class-based):
```html
<span class="badge bg-danger notification-badge d-none-init" id="notificationBadge">5</span>

<ul class="dropdown-menu dropdown-menu-end notification-dropdown">
    <li>Notification items...</li>
</ul>

<button class="btn notification-mark-all-btn" onclick="markAllAsRead()">
    Mark All Read
</button>
```

#### Available Classes:
```
Notification: notification-badge, notification-dropdown
             notification-item, notification-item.unread
             notification-mark-all-btn
```

---

## 🎯 Priority Migration Order

### Phase 1: Layout & Base (CRITICAL)
**File**: `app/Views/layouts/base.php`

1. Replace avatar inline styles → `avatar-circle`, `avatar-placeholder`
2. Replace notification styles → `notification-badge`, `notification-dropdown`
3. Replace button styles → `user-profile-btn`

**Impact**: Affects ALL pages

---

### Phase 2: High-Traffic Modules (HIGH)

#### A. Purchasing Module
**File**: `app/Views/purchasing/purchasing.php` (100+ inline styles)

Priority replacements:
1. Modal styles → `modal-content-gradient`, `modal-header-gradient`
2. Progress bars → `progress-20px`, `progress-22px`
3. Badge labels → `badge-label`
4. Table columns → `col-w-*`
5. Scrollable areas → `scrollable-list`

#### B. Marketing Module
**Files**: `marketing/spk.php`, `marketing/print_spk.php`

Priority replacements:
1. Form controls → `min-w-200px`, `form-select-auto`
2. Scrollable lists → `scrollable-list`
3. Print layouts → `signature-section`, `page-break-avoid`
4. Table columns → `col-w-*`

#### C. Service Module
**Files**: `service/work_orders.php`, `service/unit_verification.php`

Priority replacements:
1. Show/hide sections → `d-none-init`
2. Min-height containers → `min-h-80px`, `min-h-120px`
3. Table columns → `col-w-*`
4. Form controls → `min-w-200px`

---

### Phase 3: Dashboard & Charts (MEDIUM)

**Files**: `dashboard/warehouse.php`, `admin/index.php`

Priority replacements:
1. Chart containers → `chart-container-*`
2. Progress bars → `progress-10px`, `progress-16px`

---

### Phase 4: Other Modules (LOW-MEDIUM)

Migrate remaining modules:
- Warehouse
- Finance
- Operational
- Reports
- etc.

---

## ⚠️ Important Notes

### 1. Dynamic Styles (Keep Inline)

Beberapa style HARUS tetap inline karena nilai dynamic dari PHP/JS:

```html
<!-- KEEP THIS - Dynamic width from PHP -->
<div class="progress-bar" style="width: <?= $progress ?>%;">

<!-- KEEP THIS - Dynamic column width from data -->
<th style="width: <?= $column['width'] ?>;">

<!-- KEEP THIS - Dynamic positioning from JavaScript -->
el.style.cssText = `top: ${topOffset}px;`;
```

### 2. Specificity Issues

Jika class tidak apply, gunakan `!important` di CSS atau tambahkan specificity:

```css
/* If needed, increase specificity */
.modal-content.modal-content-gradient {
    border-radius: 12px !important;
}
```

### 3. Testing Checklist

Setelah migration, test:
- [ ] Tampilan normal di desktop
- [ ] Tampilan di mobile/responsive
- [ ] Print layout (jika ada)
- [ ] JavaScript functionality (toggle, collapse, etc.)
- [ ] Form validation display
- [ ] Modal interactions

---

## 📊 Migration Progress Template

Gunakan template ini untuk track progress:

```markdown
## Module: [Module Name]
**File**: [File Path]
**Status**: [ ] Not Started / [ ] In Progress / [x] Completed
**Inline Styles Found**: XX

### Replacements Made:
- [ ] Width/Height utilities
- [ ] Display & visibility
- [ ] Border radius
- [ ] Table columns
- [ ] Forms
- [ ] Modal components
- [ ] Progress bars
- [ ] Badges
- [ ] Charts
- [ ] Print layouts

### Issues Encountered:
- Issue 1: Description
- Issue 2: Description

### Testing:
- [ ] Desktop view
- [ ] Mobile view
- [ ] Print (if applicable)
- [ ] JavaScript functionality
```

---

## 🛠️ Tools & Scripts

### Find Inline Styles in File:
```powershell
# Search for style attributes
Select-String -Path "app/Views/**/*.php" -Pattern 'style\s*=\s*["\']' | Select-Object Path, LineNumber, Line
```

### Count Inline Styles:
```powershell
(Select-String -Path "app/Views/purchasing/purchasing.php" -Pattern 'style\s*=\s*["\']').Count
```

---

## 📞 Support

Jika ada pertanyaan atau issue:
1. Check dokumentasi di `docs/CSS_COMPREHENSIVE_AUDIT.md`
2. Lihat contoh di `docs/SB_ADMIN_PRO_IMPLEMENTATION_GUIDE.md`
3. Hubungi team lead

---

**Good luck with the migration!** 🚀
