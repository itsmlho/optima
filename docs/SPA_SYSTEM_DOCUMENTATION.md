# OPTIMA SPA System Documentation

## Overview
Sistem SPA (Single Page Application) unified untuk OPTIMA yang menyediakan navigasi tanpa reload sidebar dengan penanda menu aktif yang dinamis dan auto-refresh data.

## Features

### 1. SPA Navigation
- ✅ Navigasi tanpa reload penuh halaman
- ✅ Sidebar tetap stabil saat navigasi
- ✅ Hanya konten area yang di-refresh
- ✅ History API support (back/forward button)
- ✅ Loading indicators untuk request lambat

### 2. Active Menu States
- ✅ Penanda menu aktif dinamis dengan indikator visual
- ✅ Auto-scroll ke menu aktif
- ✅ Expand parent submenu otomatis
- ✅ Multiple strategy untuk mendeteksi menu aktif

### 3. Sidebar Enhancements
- ✅ Memory scroll position
- ✅ Mobile responsive toggle
- ✅ Smooth animations
- ✅ Enhanced visual effects

### 4. Data Refresh System
- ✅ Auto-refresh data saat navigasi
- ✅ Page-specific refresh handlers
- ✅ Interval-based auto refresh
- ✅ Debounced execution

### 5. Component Management
- ✅ Auto cleanup old components
- ✅ Re-initialize page components
- ✅ DataTable reload support
- ✅ Chart.js cleanup and re-init

## File Structure

```
public/assets/js/
├── optima-spa-unified.js      # Main SPA system
├── optima-data-refresh.js     # Data refresh handlers
└── optima-spa-test.js         # Test & debugging tools
```

## Core Classes

### OptimaSPAUnified
Main class yang menangani semua funktionalitas SPA.

**Methods:**
- `navigateTo(url)` - Navigate to URL dengan SPA
- `updateActiveStates(path)` - Update penanda menu aktif
- `refreshCurrentPage()` - Refresh konten halaman saat ini
- `scrollToActive()` - Scroll ke menu aktif
- `getCurrentPath()` - Get path saat ini

### OptimaDataRefresh
Class untuk mengelola auto-refresh data.

**Methods:**
- `register(pattern, handler, options)` - Daftarkan handler refresh
- `refresh(path)` - Execute refresh untuk path
- `initAutoRefresh(path)` - Inisialisasi auto refresh

## Usage Examples

### Basic Navigation
```javascript
// Navigate menggunakan SPA
window.optimaSPANavigate('/marketing/kontrak');

// Refresh halaman saat ini
window.optimaSPARefresh();

// Update active states manual
window.optimaSPAUpdateActiveStates('/current/path');
```

### Register Custom Data Refresh
```javascript
// Register handler untuk halaman custom
OptimaDataRefresh.register('/custom/page*', function(path) {
    console.log('Refreshing custom page data...');
    
    // Reload DataTable
    $('#customTable').DataTable().ajax.reload();
    
    // Refresh stats
    refreshCustomStats();
}, {
    autoRefresh: true,    // Auto refresh saat page load
    interval: 30000,      // Auto refresh every 30 seconds
    debounce: 500        // Debounce 500ms
});
```

### Testing
```javascript
// Run all tests
OptimaTestSPA.runAllTests();

// Test basic functionality
OptimaTestSPA.testBasicNavigation();

// Demo active states
OptimaTestSPA.demoActiveStates();

// Performance test
OptimaTestSPA.testPerformance();
```

## Configuration

### Default Refresh Handlers
System sudah include handler untuk:

- **Dashboard** (`/dashboard*`)
  - Auto refresh stats, charts, recent activities
  - Interval refresh setiap 60 detik

- **Kontrak** (`/marketing/kontrak*`)
  - Reload DataTable
  - Refresh stats dan charts

- **SPK** (`/marketing/spk*`)
  - Reload DataTable
  - Refresh stats

- **DI** (`/marketing/di*`)
  - Reload DataTable

### CSS Classes for Active States
```css
.nav-link.active {
    background: linear-gradient(135deg, rgba(0, 97, 242, 0.1) 0%, rgba(77, 140, 255, 0.1) 100%);
    border-left: 3px solid #0061f2;
    color: #0061f2 !important;
    font-weight: 600;
}

.nav-indicator {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #0061f2;
}
```

## Events

### spa:pageLoaded
Event yang di-trigger setelah halaman SPA berhasil dimuat.

```javascript
document.addEventListener('spa:pageLoaded', function(e) {
    console.log('Page loaded:', e.detail.path);
    
    // Custom initialization
    initializeCustomComponents();
});
```

## Browser Support
- ✅ Chrome 70+
- ✅ Firefox 65+
- ✅ Safari 12+
- ✅ Edge 79+

## Performance
- Fast navigation: ~50-200ms avg
- Memory efficient: Auto cleanup components
- Bandwidth efficient: Only loads new content

## Debugging

### Console Commands
```javascript
// Debug SPA status
debugOptimaSPA();

// Test SPA functionality
testSPA();

// Demo active states
demoSPA();
```

### Debug Information
- Initialization status
- Current path
- Loading state
- Active link detection
- Script tracking (development only)

## Troubleshooting

### Common Issues

1. **Menu tidak aktif**
   - Pastikan href link sesuai dengan route
   - Check console untuk error active state detection

2. **Data tidak refresh**
   - Pastikan handler terdaftar untuk path tersebut
   - Check console untuk error refresh handler

3. **Navigation tidak bekerja**
   - Check console untuk error SPA initialization
   - Pastikan link internal (bukan external)

### Debug Commands
```javascript
// Check SPA status
window.optimaSPA.getCurrentPath();
window.optimaSPA.isNavigationLoading();

// Check registered refresh handlers
OptimaDataRefresh.refreshHandlers;

// Manual refresh
OptimaDataRefresh.refresh('/current/path');
```

## Migration from Old System

### Removed Files
- ✅ `sidebar-advanced.js` - Replaced by unified system
- ✅ `spa-navigation.js` - Replaced by unified system  
- ✅ `sidebar-enhanced.js` - Functionality merged
- ✅ `optima-pro.js` - Functionality merged

### Breaking Changes
- Global variables moved to unified namespace
- Event handlers consolidated
- CSS classes standardized

### Migration Steps
1. Remove old script includes
2. Include new unified scripts
3. Update custom handlers to use new API
4. Test navigation and active states

## Future Enhancements
- [ ] Route-based loading strategies
- [ ] Advanced caching mechanisms
- [ ] Progressive loading indicators
- [ ] Enhanced error handling
- [ ] Analytics integration

---

**Version:** 3.0  
**Last Updated:** September 2025  
**Maintained by:** OPTIMA Development Team
