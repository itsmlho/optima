# Modern Sidebar Implementation Guide

## 📋 Overview
Implementasi sidebar modern bergaya CodePen untuk aplikasi OPTIMA dengan fitur collapsible, smooth animations, dan fully responsive.

---

## ✨ Features

### 1. **Collapsible Sidebar**
   - Full mode: 256px lebar
   - Collapsed mode: 80px lebar
   - Smooth animation transitions
   - Toggle button di header

### 2. **Active Item Highlight**
   - Gradient background untuk item aktif
   - Smooth transition saat navigasi
   - Hover effect untuk semua menu items

### 3. **User Profile Footer**
   - Avatar display (initial atau foto)
   - Expandable user menu
   - Quick access untuk Profile, Settings, Logout

### 4. **Fully Responsive**
   - Desktop: Fixed sidebar dengan toggle
   - Mobile: Overlay sidebar dengan hamburger button
   - Touch-friendly interface

### 5. **Modern Design**
   - Dark theme dengan gradient accents
   - Font Awesome icons
   - Rounded corners dan shadows
   - Smooth scrollbars

---

## 📁 File Structure

```
app/Views/layouts/
├── layout_modern.php       # Layout utama dengan modern sidebar
├── sidebar_modern.php      # Sidebar component
└── base.php               # Layout lama (backward compatible)

app/Views/demo/
└── modern_sidebar.php      # Demo page

app/Controllers/
└── Demo.php               # Demo controller

public/assets/css/desktop/
└── optima-sidebar-modern.css  # Stylesheet khusus sidebar modern
```

---

## 🚀 Quick Start

### 1. Akses Demo Page
Buka browser dan akses:
```
http://localhost/optima/public/demo/modernSidebar
```

### 2. Gunakan di View Baru
```php
<?= $this->extend('layouts/layout_modern') ?>

<?= $this->section('title') ?>Judul Halaman<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Judul Halaman<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
        <li class="breadcrumb-item active">Current Page</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
    <!-- Your content here -->
<?= $this->endSection() ?>
```

### 3. Tambahkan Custom CSS (Optional)
```php
<?= $this->section('css') ?>
<style>
    /* Your custom styles */
</style>
<?= $this->endSection() ?>
```

### 4. Tambahkan Custom JavaScript (Optional)
```php
<?= $this->section('javascript') ?>
<script>
    // Your custom scripts
</script>
<?= $this->endSection() ?>
```

---

## 🎨 Customization

### Ubah Warna Sidebar
Edit file: `public/assets/css/desktop/optima-sidebar-modern.css`

```css
:root {
    --navbar-width: 256px;              /* Lebar sidebar full */
    --navbar-width-min: 80px;           /* Lebar sidebar minimal */
    --navbar-dark-primary: #18283b;     /* Background utama (dark blue) */
    --navbar-dark-secondary: #2c3e50;   /* Background secondary (darker) */
    --navbar-light-primary: #f5f6fa;    /* Text primary (light) */
    --navbar-light-secondary: #8392a5;  /* Text secondary (gray) */
}
```

### Ubah Gradient Highlight
Cari class `.nav-content-highlight` dan ubah:
```css
background: linear-gradient(135deg, #0061f2 0%, #00ac69 100%);
```

### Tambah/Ubah Menu Items
Edit file: `app/Views/layouts/sidebar_modern.php`

```php
<!-- Tambah menu item baru -->
<a href="<?= base_url('/your-route') ?>" class="nav-button">
    <i class="fas fa-your-icon"></i>
    <span>Menu Name</span>
</a>
```

---

## 📱 Responsive Behavior

### Desktop (> 768px)
- Sidebar fixed di kiri dengan margin 1vw
- Toggle button untuk collapse/expand
- Content area menyesuaikan dengan sidebar

### Mobile (≤ 768px)
- Sidebar tersembunyi secara default
- Hamburger button untuk open sidebar
- Overlay background saat sidebar terbuka
- Auto-close setelah klik menu item

---

## 🔧 Integration dengan Route

### Tambahkan Route untuk Demo
Edit `app/Config/Routes.php`:

```php
// Demo routes
$routes->get('demo/modernSidebar', 'Demo::modernSidebar');
```

### Controller Example
```php
<?php
namespace App\Controllers;

class YourController extends BaseController
{
    public function index()
    {
        return view('your_module/index', [
            'title' => 'Your Page Title',
            'data' => $this->model->getData()
        ]);
    }
}
```

---

## ⚙️ Advanced Features

### 1. Dynamic Active State
Sidebar otomatis mendeteksi halaman aktif berdasarkan `uri_string()`:

```php
class="nav-button <?= (strpos(uri_string(), 'units') !== false) ? 'active' : '' ?>"
```

### 2. User Session Data
Footer sidebar mengambil data dari session:

```php
$userSession = session()->get('user');
$userName = $userSession['username'] ?? 'Guest';
$userRole = $userSession['role'] ?? 'User';
```

### 3. Avatar Display
Jika user punya avatar:
```php
<?php if (isset($userSession['avatar']) && !empty($userSession['avatar'])): ?>
    <img src="<?= base_url('assets/images/avatars/' . $userSession['avatar']) ?>">
<?php else: ?>
    <?= $userInitial ?>
<?php endif; ?>
```

---

## 🐛 Troubleshooting

### Sidebar Tidak Muncul
1. Pastikan CSS file ter-load:
   ```html
   <link href="<?= base_url('assets/css/desktop/optima-sidebar-modern.css') ?>" rel="stylesheet">
   ```

2. Clear browser cache (Ctrl + F5)

### Highlight Tidak Berfungsi
1. Pastikan JavaScript di `sidebar_modern.php` ter-load
2. Check console browser untuk error
3. Pastikan jQuery sudah ter-load sebelum script

### Mobile Sidebar Tidak Toggle
1. Pastikan fungsi `toggleMobileSidebar()` terdefinisi
2. Check z-index overlay dan sidebar
3. Pastikan checkbox input tidak ter-delete

### Content Overlap dengan Sidebar
1. Pastikan class `main-content-wrapper` aktif
2. Check margin-left calculation
3. Untuk mobile, pastikan padding-top: 70px

---

## 📊 Performance Tips

1. **CSS Minification**: Untuk production, minify CSS file
2. **Lazy Loading**: Load sidebar script setelah DOMContentLoaded
3. **Debounce Resize**: Optimize window resize event handler
4. **Icon Sprite**: Gunakan Font Awesome CDN atau local sprite

---

## 🔐 Security Notes

1. **CSRF Token**: Sidebar include CSRF token untuk AJAX
   ```javascript
   window.csrfTokenName = '<?= csrf_token() ?>';
   window.csrfTokenValue = '<?= csrf_hash() ?>';
   ```

2. **XSS Protection**: Semua output user data di-escape
   ```php
   <?= esc($userName) ?>
   ```

3. **Session Validation**: Check login status di controller

---

## 🎯 Migration dari Layout Lama

### Step 1: Backup
```bash
cp app/Views/your_view.php app/Views/your_view.php.bak
```

### Step 2: Update Extend
Ganti:
```php
<?= $this->extend('layouts/base') ?>
```

Dengan:
```php
<?= $this->extend('layouts/layout_modern') ?>
```

### Step 3: Update Sections
Tambahkan section baru:
```php
<?= $this->section('page_title') ?>Your Title<?= $this->endSection() ?>
<?= $this->section('breadcrumb') ?>...<?= $this->endSection() ?>
```

### Step 4: Test
- Akses halaman di browser
- Test responsive di mobile
- Verify menu navigation
- Check AJAX functionality

---

## 📝 Best Practices

1. **Konsisten Naming**: Gunakan nama yang descriptive untuk menu items
2. **Icon Selection**: Pilih Font Awesome icons yang relevant
3. **Active State**: Selalu set active state untuk user orientation
4. **Loading State**: Tambahkan loading indicator untuk AJAX operations
5. **Error Handling**: Handle navigation errors dengan graceful fallback

---

## 🆘 Support & Resources

- **CodeIgniter 4 Docs**: https://codeigniter.com/user_guide/
- **Font Awesome Icons**: https://fontawesome.com/icons
- **Bootstrap 5 Docs**: https://getbootstrap.com/docs/5.3/
- **Original Design**: CodePen by uahnbu

---

## 📜 Changelog

### Version 1.0.0 (March 6, 2026)
- ✅ Initial implementation
- ✅ Collapsible sidebar functionality
- ✅ Responsive mobile design
- ✅ User profile footer
- ✅ Active item highlighting
- ✅ Demo page and documentation

---

## 🎉 Conclusion

Sidebar modern ini siap digunakan untuk meningkatkan UX aplikasi OPTIMA. Silakan customize sesuai kebutuhan dan brand guidelines Anda!

**Happy Coding! 🚀**

---

**Developed for**: PT Sarana Mitra Luas Tbk - OPTIMA System  
**Date**: March 6, 2026  
**Inspired by**: CodePen modern sidebar design
