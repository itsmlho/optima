# CSS Folder Organization

## Structure

```
css/
├── mobile/          → Mobile-specific CSS files
├── desktop/         → Desktop-specific CSS files
├── plugins/         → Third-party plugin CSS files
└── README.md        → This file
```

## Mobile CSS (`mobile/`)
Mobile-optimized CSS untuk responsive design (max-width: 767px)
- `auth-mobile.css` - Auth pages mobile styling
- `dashboard-mobile.css` - Dashboard mobile styling
- `form-mobile.css` - Form mobile styling
- `mobile-utilities.css` - Mobile utility classes
- `navigation-mobile.css` - Navigation mobile styling
- `optima-mobile.css` - Main mobile styling
- `table-mobile.css` - Table mobile styling

## Desktop CSS (`desktop/`)
Desktop & main theme CSS files
- `dashboard-modern.css` - Modern dashboard theme
- `optima-pro.css` - Main OPTIMA Pro theme
- `optima-pro.min.css` - Minified version
- `optima-sb-admin-pro.css` - SB Admin Pro theme

## Plugins CSS (`plugins/`)
Third-party plugin & component CSS
- `select2-custom.css` - Custom Select2 styling
- `select2.min.css` - Select2 plugin (minified)
- `notification-popup.css` - Notification popup styling
- `global-permission.css` - Permission system styling

## Usage Guidelines

### Loading Mobile CSS
```html
<!-- Only load on mobile -->
<link href="<?= base_url('assets/css/mobile/auth-mobile.css') ?>" rel="stylesheet" media="(max-width: 767px)">
```

### Loading Desktop CSS
```html
<!-- Main desktop theme -->
<link href="<?= base_url('assets/css/desktop/optima-pro.min.css') ?>" rel="stylesheet">
```

### Loading Plugin CSS
```html
<!-- Plugin CSS -->
<link href="<?= base_url('assets/css/plugins/select2.min.css') ?>" rel="stylesheet">
```

## Best Practices

1. **Separation of Concerns**: Keep mobile, desktop, and plugin CSS separate
2. **Use Media Queries**: Mobile CSS should be loaded with media queries
3. **Minification**: Use .min.css in production for better performance
4. **Naming Convention**: Use descriptive names (purpose-context.css)

## Maintenance

Last updated: February 5, 2026
Organized by: GitHub Copilot
