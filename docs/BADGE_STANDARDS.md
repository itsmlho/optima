# Optima Badge System Standards

**Version:** 2.0 (March 11, 2026)  
**Author:** Optima Development Team  
**Purpose:** Standardized badge system untuk konsistensi visual di seluruh aplikasi Optima

---

## 📋 Overview

Badge system Optima menggunakan **soft color palette** yang telah didefinisikan di `public/assets/css/desktop/optima-pro.css`. Sistem ini:

- ✅ **No JavaScript required** - Pure CSS classes
- ✅ **Consistent colors** - Semua modul menggunakan warna yang sama untuk status yang sama
- ✅ **Easy to use** - Copy-paste HTML class
- ✅ **Accessible** - Kontras warna memenuhi standar WCAG
- ✅ **Maintainable** - Update 1 file CSS, semua modul ikut berubah

---

## 🎨 Available Badge Classes

### Soft Color Badges (Main System)

| Class Name | Color | Background | Text Color | Use Case |
|------------|-------|------------|------------|----------|
| `badge-soft-blue` | 🔵 Blue | `#e7f1ff` | `#084298` | Info, Contract Type, Counters |
| `badge-soft-green` | 🟢 Green | `#d1e7dd` | `#0a3622` | Active, Success, Approved |
| `badge-soft-red` | 🔴 Red | `#f8d7da` | `#58151c` | Inactive, Expired, Danger |
| `badge-soft-orange` | 🟠 Orange | `#ffe5d0` | `#653208` | Warning, Expiring Soon, Maintenance |
| `badge-soft-yellow` | 🟡 Yellow | `#fff3cd` | `#664d03` | Pending, Daily/Spot, Waiting |
| `badge-soft-purple` | 🟣 Purple | `#e0cffc` | `#59359a` | Quotation, Delivered, Special |
| `badge-soft-cyan` | 🔷 Cyan | `#cff4fc` | `#055160` | PO Only, Available, Stock |
| `badge-soft-gray` | ⚫ Gray | `#e9ecef` | `#495057` | Disabled, Unknown, Draft |

### Tab Badges (Auto State Change)

Tab badges berubah dari soft → solid saat parent tab menjadi active:

| Class Name | Inactive (Soft) | Active (Solid) | Use Case |
|------------|----------------|----------------|----------|
| `badge-tab-all` | Light Blue | Solid Blue | "All" filter tab |
| `badge-tab-active` | Light Green | Solid Green | "Active" filter tab |
| `badge-tab-inactive` | Light Red | Solid Red | "Inactive" filter tab |
| `badge-tab-no-contract` | Light Orange | Solid Orange | "No Contract" filter tab |

---

## 💻 HTML Usage Examples

### Basic Status Badges

```html
<!-- Active Status -->
<span class="badge badge-soft-green">ACTIVE</span>
<span class="badge badge-soft-green"><i class="bi bi-check-circle me-1"></i>ACTIVE</span>

<!-- Inactive Status -->
<span class="badge badge-soft-red">INACTIVE</span>
<span class="badge badge-soft-red"><i class="bi bi-x-circle me-1"></i>INACTIVE</span>

<!-- Pending Status -->
<span class="badge badge-soft-yellow">PENDING</span>

<!-- Draft Status -->
<span class="badge badge-soft-gray">DRAFT</span>
```

### Counter Badges

```html
<!-- Has items (>0) -->
<span class="badge badge-soft-blue">247</span>

<!-- Empty (0) -->
<span class="badge badge-soft-gray">0</span>

<!-- With rounded pill effect -->
<span class="badge badge-soft-blue rounded-pill">12</span>
```

### Time-Based Badges

```html
<!-- Expired -->
<span class="badge badge-soft-red"><i class="bi bi-exclamation-circle me-1"></i>Expired</span>

<!-- Expiring Soon -->
<span class="badge badge-soft-orange"><i class="bi bi-clock me-1"></i>5 days left</span>

<!-- Active/Valid -->
<span class="badge badge-soft-green">Valid until Dec 2026</span>
```

### Document Type Badges

```html
<!-- Contract -->
<span class="badge badge-soft-blue"><i class="fas fa-file-contract me-1"></i>Contract</span>

<!-- PO Only -->
<span class="badge badge-soft-cyan"><i class="fas fa-file-invoice me-1"></i>PO Only</span>

<!-- Daily/Spot -->
<span class="badge badge-soft-yellow"><i class="fas fa-calendar-day me-1"></i>Daily/Spot</span>

<!-- Quotation -->
<span class="badge badge-soft-purple"><i class="fas fa-file-alt me-1"></i>Quotation</span>
```

### Tab Filter Badges

```html
<!-- Tab navigation with auto state change -->
<ul class="nav nav-tabs">
    <li class="nav-item">
        <button class="nav-link active tab-all" data-status="all">
            All <span class="badge badge-tab-all ms-2">247</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link tab-active" data-status="active">
            Active <span class="badge badge-tab-active ms-2">243</span>
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link tab-inactive" data-status="inactive">
            Inactive <span class="badge badge-tab-inactive ms-2">4</span>
        </button>
    </li>
</ul>
```

---

## 🔧 JavaScript/Template Usage

### PHP/CodeIgniter Template

```php
<!-- Conditional badge based on status -->
<span class="badge <?= $customer->is_active == 1 ? 'badge-soft-green' : 'badge-soft-red' ?>">
    <?= $customer->is_active == 1 ? 'ACTIVE' : 'INACTIVE' ?>
</span>

<!-- Counter badge -->
<?php if ($contractCount > 0): ?>
    <span class="badge badge-soft-blue"><?= $contractCount ?></span>
<?php else: ?>
    <span class="badge badge-soft-gray">0</span>
<?php endif; ?>
```

### JavaScript Template Literals

```javascript
// Conditional badge
const statusBadge = data.is_active == 1 
    ? '<span class="badge badge-soft-green">ACTIVE</span>'
    : '<span class="badge badge-soft-red">INACTIVE</span>';

// Counter badge
const counterBadge = count > 0
    ? `<span class="badge badge-soft-blue">${count}</span>`
    : '<span class="badge badge-soft-gray">0</span>';

// With icon
const badge = `<span class="badge badge-soft-green">
    <i class="bi bi-check-circle me-1"></i>${statusText}
</span>`;
```

### DataTables Column Render

```javascript
{
    data: 'is_active',
    className: 'text-center',
    render: (data) => {
        if (data == 1 || data === 'ACTIVE') {
            return '<span class="badge badge-soft-green"><i class="bi bi-check-circle me-1"></i>ACTIVE</span>';
        } else {
            return '<span class="badge badge-soft-red"><i class="bi bi-x-circle me-1"></i>INACTIVE</span>';
        }
    }
}
```

---

## 📊 Usage Standards by Module

### Customer Management
- **Status**: `badge-soft-green` (Active) / `badge-soft-red` (Inactive)
- **Active Contracts**: `badge-soft-green` (>0) / `badge-soft-gray` (0)
- **Total Counters**: `badge-soft-blue`
- **Rental Type**: `badge-soft-blue` (Contract) / `badge-soft-cyan` (PO) / `badge-soft-yellow` (Daily)

### Unit Management
- **Operational Status**: `badge-soft-green`
- **Maintenance**: `badge-soft-orange`
- **Broken/Repair**: `badge-soft-red`
- **Available/Stock**: `badge-soft-cyan`
- **Sold/Delivered**: `badge-soft-purple`

### Contract Management
- **Active Contract**: `badge-soft-green`
- **Pending Contract**: `badge-soft-yellow`
- **Expired Contract**: `badge-soft-red`
- **Expiring Soon**: `badge-soft-orange`
- **Draft**: `badge-soft-gray`

### Quotation Module
- **Approved**: `badge-soft-green`
- **Pending Review**: `badge-soft-yellow`
- **Rejected**: `badge-soft-red`
- **Draft**: `badge-soft-gray`
- **Quotation Type**: `badge-soft-purple`

### SPK Marketing
- **Completed**: `badge-soft-green`
- **In Progress**: `badge-soft-blue`
- **Pending**: `badge-soft-yellow`
- **Cancelled**: `badge-soft-red`

---

## ⚠️ Important Guidelines

### DO ✅

1. **Use direct CSS classes** - No JavaScript helper functions needed
   ```html
   <span class="badge badge-soft-green">ACTIVE</span>
   ```

2. **Follow module standards** - Consistent colors for same meaning across modules
   ```html
   <!-- Active status always green -->
   <span class="badge badge-soft-green">ACTIVE</span>
   ```

3. **Add icons for clarity** - Bootstrap Icons or Font Awesome
   ```html
   <span class="badge badge-soft-red">
       <i class="bi bi-x-circle me-1"></i>INACTIVE
   </span>
   ```

4. **Use semantic naming** - Badge color should match meaning
   ```html
   <!-- Correct: Red for danger/inactive -->
   <span class="badge badge-soft-red">EXPIRED</span>
   ```

### DON'T ❌

1. **Don't use old Bootstrap classes** - Deprecated in Optima
   ```html
   <!-- ❌ DON'T USE -->
   <span class="badge bg-success">ACTIVE</span>
   <span class="badge bg-primary">247</span>
   
   <!-- ✅ USE INSTEAD -->
   <span class="badge badge-soft-green">ACTIVE</span>
   <span class="badge badge-soft-blue">247</span>
   ```

2. **Don't create custom badge colors** - Use existing classes
   ```html
   <!-- ❌ DON'T USE -->
   <span class="badge" style="background:#abc123; color:#fff">Custom</span>
   
   <!-- ✅ USE INSTEAD -->
   <span class="badge badge-soft-blue">Standard</span>
   ```

3. **Don't mix color meanings** - Keep consistency
   ```html
   <!-- ❌ INCONSISTENT -->
   <span class="badge badge-soft-blue">ACTIVE</span>  <!-- Wrong color for active -->
   
   <!-- ✅ CONSISTENT -->
   <span class="badge badge-soft-green">ACTIVE</span>  <!-- Correct -->
   ```

4. **Don't create JavaScript abstraction layers** - Keep it simple
   ```javascript
   // ❌ DON'T CREATE
   function makeBadge(type, text) { ... }
   
   // ✅ USE DIRECT HTML
   `<span class="badge badge-soft-green">${text}</span>`
   ```

---

## 🔍 Quick Reference Cheat Sheet

Copy-paste ini untuk referensi cepat:

```html
<!-- STATUS -->
<span class="badge badge-soft-green">ACTIVE</span>
<span class="badge badge-soft-red">INACTIVE</span>
<span class="badge badge-soft-yellow">PENDING</span>
<span class="badge badge-soft-gray">DRAFT</span>

<!-- TIME-BASED -->
<span class="badge badge-soft-red">Expired</span>
<span class="badge badge-soft-orange">5 days left</span>
<span class="badge badge-soft-green">Valid</span>

<!-- DOCUMENT TYPE -->
<span class="badge badge-soft-blue">Contract</span>
<span class="badge badge-soft-cyan">PO Only</span>
<span class="badge badge-soft-yellow">Daily/Spot</span>
<span class="badge badge-soft-purple">Quotation</span>

<!-- COUNTERS -->
<span class="badge badge-soft-blue">247</span>
<span class="badge badge-soft-gray">0</span>

<!-- OPERATIONAL -->
<span class="badge badge-soft-green">In Operation</span>
<span class="badge badge-soft-orange">Maintenance</span>
<span class="badge badge-soft-red">Broken</span>
<span class="badge badge-soft-cyan">Available</span>
```

---

## 📦 Files Involved

| File | Purpose |
|------|---------|
| `public/assets/css/desktop/optima-pro.css` | Badge class definitions (line ~2024-2120) |
| `docs/BADGE_STANDARDS.md` | This documentation |
| `app/Views/marketing/customer_management.php` | Reference implementation |

---

## 🚀 Migration Guide

### From Old Bootstrap Badges

```html
<!-- OLD (Bootstrap Default) -->
<span class="badge bg-success">ACTIVE</span>
<span class="badge bg-danger">INACTIVE</span>
<span class="badge bg-primary">247</span>
<span class="badge bg-secondary">N/A</span>

<!-- NEW (Optima Standards) -->
<span class="badge badge-soft-green">ACTIVE</span>
<span class="badge badge-soft-red">INACTIVE</span>
<span class="badge badge-soft-blue">247</span>
<span class="badge badge-soft-gray">N/A</span>
```

### Search & Replace Guide

Use these find/replace patterns in VS Code:

1. **Active badges:**
   - Find: `badge bg-success`
   - Replace: `badge badge-soft-green`

2. **Inactive/Danger badges:**
   - Find: `badge bg-danger`
   - Replace: `badge badge-soft-red`

3. **Info/Primary badges:**
   - Find: `badge bg-primary`
   - Replace: `badge badge-soft-blue`

4. **Warning badges:**
   - Find: `badge bg-warning`
   - Replace: `badge badge-soft-yellow`

5. **Secondary/Unknown badges:**
   - Find: `badge bg-secondary`
   - Replace: `badge badge-soft-gray`

---

## 📞 Support

**Questions?** Contact Optima Development Team  
**Updates:** Check `optima-pro.css` for latest badge classes  
**Issues:** Report to IT Support Optima

---

**Last Updated:** March 11, 2026  
**Version:** 2.0  
**Status:** ✅ Production Ready
