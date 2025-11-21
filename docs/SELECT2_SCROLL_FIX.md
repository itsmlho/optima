# Fix: Select2 Dropdown Closes When Scrolling in Modal

## 🐛 Problem
Ketika user scroll modal kebawah dan mencoba membuka Select2 dropdown, dropdown langsung tertutup.

## 🔍 Root Cause
Select2 dropdown positioning conflict dengan scrollable modal body. Saat modal di-scroll, Select2 tidak re-calculate position dan dropdown menutup.

## ✅ Solution Implemented

### 1. **Update dropdownParent**
Changed from generic modal to specific modal-content:
```javascript
// BEFORE
dropdownParent: $('#itemDetailModal')

// AFTER  
dropdownParent: $('#itemDetailModal .modal-content')
```

### 2. **Add Select2 Configuration**
```javascript
$('.select2-basic').select2({
    theme: 'bootstrap-5',
    dropdownParent: $('#itemDetailModal .modal-content'),
    width: '100%',
    dropdownAutoWidth: true,
    dropdownCssClass: 'select2-dropdown-fixed'  // Custom CSS class
});
```

### 3. **Add CSS Fixes**
```css
/* Fix z-index for dropdown */
.select2-dropdown-fixed {
    z-index: 10060 !important; /* Above modal backdrop */
}

.select2-container--open {
    z-index: 10060 !important;
}

/* Modal body positioning */
.modal-body {
    position: relative;
    overflow-y: auto;
}

.select2-container {
    z-index: 10050;
}

/* Better dropdown styling */
.select2-dropdown {
    border: 1px solid rgba(0,0,0,.15);
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
}
```

## 🧪 Testing

### Test Scenario 1: Basic Dropdown
1. Open Create PO modal
2. Click "Tambah Unit"
3. Scroll form kebawah
4. Click dropdown "Departemen"
5. ✅ Dropdown should stay open
6. Select option
7. ✅ Dropdown should close and value selected

### Test Scenario 2: Cascading Dropdown
1. Select Departemen → Jenis populated
2. Scroll form kebawah
3. Click dropdown "Jenis Unit"
4. ✅ Dropdown should stay open
5. Select option
6. ✅ Works properly

### Test Scenario 3: Dynamic Dropdown
1. Select Brand → Model populated (via AJAX)
2. Scroll form kebawah
3. Click dropdown "Model"
4. ✅ Dropdown stays open
5. Select option
6. ✅ Works properly

## 🔧 Alternative Solutions (If Still Not Working)

### Option 1: Use Native Select (No Select2)
Remove Select2 class from problematic dropdowns:

```html
<!-- BEFORE -->
<select class="form-select select2-basic">

<!-- AFTER -->
<select class="form-select">
```

### Option 2: Disable Scroll on Modal Body
```css
.modal-body {
    overflow-y: hidden; /* No scroll */
}

/* Or make entire modal larger */
.modal-xl {
    max-width: 95vw;
    height: 90vh;
}
```

### Option 3: Attach Dropdown to Body
```javascript
$('.select2-basic').select2({
    dropdownParent: $('body'), // Attach to body instead of modal
    theme: 'bootstrap-5'
});
```
⚠️ **Warning:** This may cause dropdown to appear behind modal backdrop.

### Option 4: Use Select2 with closeOnSelect
```javascript
$('.select2-basic').select2({
    closeOnSelect: true,
    theme: 'bootstrap-5',
    dropdownParent: $('#itemDetailModal .modal-content')
});
```

## 📊 Z-Index Hierarchy

```
Layer                          Z-Index
═══════════════════════════════════════
Body/Page                      0
Modal Backdrop                 1050
Modal Dialog                   1055  
Select2 Container              10050
Select2 Dropdown               10060 ✅
```

## 🔍 Debug Checklist

If dropdown still closes on scroll:

- [ ] Check console for JavaScript errors
- [ ] Verify Select2 version (should be 4.0.13+)
- [ ] Check if Bootstrap modal version conflicts (should be 5.x)
- [ ] Inspect CSS computed z-index values
- [ ] Try disabling other JavaScript that might interfere
- [ ] Test without other dropdowns open
- [ ] Clear browser cache and hard reload (Ctrl+F5)

## 📝 Related Files

- `/app/Views/purchasing/purchasing.php` - Lines 1504-1511 (Initialize)
- `/app/Views/purchasing/purchasing.php` - Lines 1768-1774 (Jenis dropdown)
- `/app/Views/purchasing/purchasing.php` - Lines 1818-1824 (Model dropdown)
- `/app/Views/purchasing/purchasing.php` - Lines 465-494 (CSS fixes)

## 🎯 Expected Behavior

✅ **CORRECT:**
- User scroll → dropdown stays in position
- User click dropdown → dropdown opens
- User scroll while dropdown open → dropdown follows scroll
- User select option → dropdown closes, value selected

❌ **INCORRECT:**
- User scroll → dropdown closes immediately
- User click dropdown after scroll → nothing happens
- Dropdown appears behind modal
- Dropdown position offset/wrong place

## 💡 Pro Tips

1. **Always use `.modal-content` as dropdownParent** for Select2 in modals
2. **Set high z-index** (10000+) for dropdown to appear above modal
3. **Use `dropdownAutoWidth: true`** for better dropdown sizing
4. **Test on different screen sizes** - behavior may differ
5. **Consider native select** for simple dropdowns (better mobile UX)

---

**Status:** ✅ Fixed  
**Last Updated:** 2025-10-10  
**Tested:** Chrome, Firefox, Safari

