# Bootstrap 5 Toast Implementation

## Overview
Toast notifications di aplikasi OPTIMA telah diupdate menggunakan Bootstrap 5 Toast component sesuai dengan dokumentasi resmi: https://getbootstrap.com/docs/5.0/components/toasts/

## Perubahan yang Dilakukan

### 1. Toast Container
- **Sebelum**: `<div id="optima-toast-container" aria-live="polite" aria-atomic="true"></div>`
- **Setelah**: `<div class="toast-container position-fixed top-0 end-0 p-3" id="optima-toast-container" style="z-index: 1090;"></div>`

Container sekarang menggunakan class Bootstrap 5:
- `toast-container` - class Bootstrap 5 untuk container
- `position-fixed top-0 end-0` - posisi di pojok kanan atas
- `p-3` - padding 1rem untuk spacing

### 2. Struktur Toast
Toast sekarang menggunakan struktur Bootstrap 5 yang proper:

```html
<div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
        <i class="fas fa-check-circle text-success me-2"></i>
        <strong class="me-auto">Success</strong>
        <small>Sekarang</small>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        SPK Berhasil dibuat!
    </div>
</div>
```

**Fitur:**
- `toast-header` dengan icon, title, timestamp, dan tombol close
- `toast-body` untuk konten pesan
- Icon berwarna sesuai tipe (success, warning, danger, info)
- Tombol close dengan `btn-close` Bootstrap 5
- ARIA attributes untuk accessibility

### 3. CSS Styling
CSS telah diupdate untuk mengikuti standar Bootstrap 5:

```css
.toast {
    min-width: 350px;
    max-width: 350px;
    font-size: 0.875rem;
    background-color: rgba(255, 255, 255, 0.95);
    border: 1px solid rgba(0, 0, 0, 0.1);
    border-radius: 0.375rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
```

**Peningkatan:**
- Shadow lebih prominent (`box-shadow: 0 0.5rem 1rem`)
- Background semi-transparent
- Border yang subtle
- Border radius sesuai Bootstrap 5

## Cara Penggunaan

### Fungsi Utama: `createOptimaToast()`

```javascript
// Success notification
createOptimaToast({
    type: 'success',
    title: 'Berhasil',
    message: 'SPK berhasil dibuat!',
    duration: 5000
});

// Warning notification
createOptimaToast({
    type: 'warning',
    title: 'Peringatan',
    message: 'Data akan dihapus permanen',
    duration: 7000
});

// Error notification
createOptimaToast({
    type: 'error', // atau 'danger'
    title: 'Error',
    message: 'Gagal menyimpan data',
    duration: 5000
});

// Info notification
createOptimaToast({
    type: 'info',
    title: 'Info',
    message: 'Data sedang diproses',
    duration: 3000
});
```

### Alias Function: `showToast()`

```javascript
// Bisa juga menggunakan showToast (alias)
showToast({
    type: 'success',
    title: 'Success',
    message: 'Operasi berhasil'
});
```

### Backward Compatibility

Fungsi lama masih didukung:
```javascript
window.OptimaPro.showNotification('Pesan berhasil', 'success');
```

## Parameter

| Parameter | Type   | Default | Description                                    |
|-----------|--------|---------|------------------------------------------------|
| type      | string | 'info'  | Tipe toast: 'success', 'warning', 'error', 'danger', 'info' |
| title     | string | 'Info'  | Judul toast yang ditampilkan di header         |
| message   | string | ''      | Isi pesan toast                                |
| duration  | number | 5000    | Durasi tampil dalam milliseconds               |

## Icon Mapping

| Type      | Icon                          | Color Class    |
|-----------|-------------------------------|----------------|
| success   | fas fa-check-circle           | text-success   |
| warning   | fas fa-exclamation-triangle   | text-warning   |
| error     | fas fa-times-circle           | text-danger    |
| danger    | fas fa-times-circle           | text-danger    |
| info      | fas fa-info-circle            | text-info      |

## Fitur Bootstrap 5

### Auto-stacking
Toast akan otomatis stack (bertumpuk) di container dengan spacing yang proper.

### Auto-hide
Toast akan otomatis hilang setelah durasi yang ditentukan.

### Manual Close
User dapat menutup toast secara manual dengan tombol close.

### Smooth Animation
Fade in/out animation menggunakan Bootstrap 5 transitions.

### Accessibility
- ARIA attributes (`aria-live`, `aria-atomic`, `role`)
- Keyboard accessible (tombol close dapat diakses dengan keyboard)
- Screen reader friendly

## Browser Support
Bootstrap 5 Toast didukung oleh browser modern:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (latest)
- Mobile browsers

## Contoh Implementasi di Controller/View

### Di PHP (CodeIgniter)
```php
// Set flash data untuk toast
session()->setFlashdata('toast', [
    'type' => 'success',
    'title' => 'Berhasil',
    'message' => 'Data berhasil disimpan'
]);

// Di view, tampilkan toast jika ada
<?php if (session()->getFlashdata('toast')): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        createOptimaToast(<?= json_encode(session()->getFlashdata('toast')) ?>);
    });
</script>
<?php endif; ?>
```

### Di JavaScript (AJAX Response)
```javascript
// Success response
$.ajax({
    url: '/api/save-data',
    method: 'POST',
    data: formData,
    success: function(response) {
        if (response.success) {
            showToast({
                type: 'success',
                title: 'Berhasil',
                message: response.message
            });
        }
    },
    error: function(xhr) {
        showToast({
            type: 'error',
            title: 'Error',
            message: 'Terjadi kesalahan pada server'
        });
    }
});
```

## Testing
Untuk testing, buka browser console dan jalankan:

```javascript
// Test success toast
createOptimaToast({
    type: 'success',
    title: 'Test Success',
    message: 'This is a success message'
});

// Test multiple toasts (stacking)
['success', 'warning', 'error', 'info'].forEach((type, i) => {
    setTimeout(() => {
        createOptimaToast({
            type: type,
            title: type.toUpperCase(),
            message: `This is ${type} message`
        });
    }, i * 500);
});
```

## Files Modified

1. **app/Views/layouts/base.php**
   - Updated toast container HTML
   - Updated `createOptimaToast()` function
   - Added `showToast` alias

2. **public/assets/css/optima-sb-admin-pro.css**
   - Updated toast CSS styles
   - Enhanced shadow and border
   - Better spacing and colors

## References
- Bootstrap 5 Toasts Documentation: https://getbootstrap.com/docs/5.0/components/toasts/
- Bootstrap 5 Components: https://getbootstrap.com/docs/5.0/components/
