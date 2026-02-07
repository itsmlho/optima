# Auth Mobile CSS - Centralized Styling Documentation

## Overview
File CSS terpusat untuk semua halaman authentication OPTIMA dengan full support mobile, tablet, dan desktop.

## File Location
```
public/assets/css/auth-mobile.css
```

## Features

### 1. **Mobile-First Responsive Design**
- Breakpoints:
  - Extra Small Mobile: max 375px
  - Small Mobile: max 576px  
  - Mobile Portrait: max 767px
  - Tablet: 768px - 1024px
  - Desktop: > 1024px

### 2. **Touch-Friendly Elements**
- Minimum button size: 44x44px (Apple & Android guidelines)
- Minimum input height: 44px
- Font size 16px on inputs (prevents iOS zoom)
- Adequate spacing between clickable elements

### 3. **Consistent Styling Across Pages**
- register.php
- login.php
- verify_email.php
- waiting_approval.php
- forgot_password.php
- verify_otp.php

## CSS Classes

### Layout Classes
- `.auth-container` - Main wrapper dengan gradient background
- `.auth-card` - Card putih dengan shadow dan border-radius
- `.auth-card-wide` - Card lebih lebar (max-width: 1000px)

### Icon & Header
- `.auth-icon` - Icon bulat dengan gradient dan animasi pulse
- `.header-section` - Section header dengan border-bottom
- `.auth-title` - Title utama (1.75rem)
- `.auth-subtitle` - Subtitle (0.95rem)

### Alerts
- `.alert` - Base alert dengan border-left
- `.alert-info` - Info alert (blue)
- `.alert-success` - Success alert (green)
- `.alert-warning` - Warning alert (yellow)
- `.alert-danger` - Danger alert (red)

### Form Elements
- `.form-label` - Label form (0.9rem, bold)
- `.form-control` - Input field
- `.form-select` - Select dropdown
- Touch-friendly: min-height 44px

### Buttons
- `.btn` - Base button
- `.btn-primary` - Primary button dengan gradient
- `.btn-outline-primary` - Outline button
- `.btn-block` - Full width button

### Step Instructions
- `.steps-container` - Container untuk step-by-step instructions
- `.step-item` - Individual step
- `.step-number` - Numbered circle
- `.step-content` - Step text content

### 2-Column Grid (waiting-approval)
- `.info-grid` - 2 kolom grid (responsive → 1 kolom di mobile)
- `.info-section` - Section dalam grid

### Email Display
- `.email-display` - Box untuk menampilkan email dengan dashed border

## Color Scheme
- Primary Blue: #0061f2
- Gradient Green: #00ac69  
- Warning Yellow: #ffc107
- Info Background: #e7f3ff
- Warning Background: #fff3cd
- Success Background: #d4edda
- Danger Background: #f8d7da

## Responsive Behavior

### Tablet (768px - 1024px)
- 2-column grid tetap 2 kolom
- Font sedikit lebih kecil
- Padding sedikit berkurang

### Mobile (max 767px)
- 2-column grid → 1 kolom
- Icon lebih kecil (70px)
- Font lebih kecil untuk hemat space
- Padding lebih compact

### Small Mobile (max 375px)
- Icon paling kecil (60px)
- Font minimal untuk readability
- Spacing sangat compact

### Landscape Mode (max-height 500px)
- Icon lebih kecil (50px)
- Header compact
- Margin minimal untuk fit dalam viewport

## Usage

### 1. Link CSS di <head>
```php
<link href="<?= base_url('assets/css/auth-mobile.css') ?>" rel="stylesheet">
```

### 2. Basic Structure
```html
<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="header-section">
                <div class="auth-icon">
                    <i class="fas fa-icon"></i>
                </div>
                <h1 class="auth-title">Title</h1>
                <p class="auth-subtitle">Subtitle</p>
            </div>
            
            <!-- Content here -->
        </div>
    </div>
</body>
```

### 3. Alert Example
```html
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <div>Alert message here</div>
</div>
```

### 4. Steps Example
```html
<div class="steps-container">
    <div class="step-item">
        <div class="step-number">1</div>
        <div class="step-content">
            <h6>Step Title</h6>
            <p>Step description</p>
        </div>
    </div>
</div>
```

## Browser Support
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Chrome Mobile)

## Performance
- File size: ~10KB (gzipped)
- No external dependencies
- Uses CSS animations (hardware-accelerated)

## Maintenance
Untuk menambah/edit styling auth pages:
1. Edit `public/assets/css/auth-mobile.css`
2. Clear browser cache
3. Test di berbagai device sizes
4. Verify touch targets (min 44px)

## Testing Checklist
- [ ] Desktop (1920x1080)
- [ ] Laptop (1366x768)
- [ ] Tablet Portrait (768x1024)
- [ ] Tablet Landscape (1024x768)
- [ ] Mobile Portrait (375x667)
- [ ] Mobile Landscape (667x375)
- [ ] Small Mobile (320x568)
