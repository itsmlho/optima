# Lazy Loading Implementation Guide

## Overview
This guide shows how to implement lazy loading in OPTIMA system for better performance.

## Basic Usage

### 1. Include Helper in Template
```php
<?= view('templates/lazy_loading_helper') ?>
```

### 2. Lazy Images
```php
<?php
$lazyService = new \App\Services\LazyLoadingService();
echo $lazyService->lazyImage('/uploads/product.jpg', 'Product Image');
?>
```

### 3. DataTable Images
```php
// In DataTable columns
$data[] = $lazyService->lazyDataTableImage($row['image_path'], $row['name'], '50px', '50px');
```

### 4. Background Images
```php
echo $lazyService->lazyBackground('/images/hero.jpg', '<h1>Hero Content</h1>', 'hero-section');
```

### 5. Lazy Content Loading
```php
echo $lazyService->lazyContent('product-specs', '/ajax/product-specs/' . $id, 'Loading specifications...');
```

## Performance Benefits
- Reduced initial page load time
- Lower bandwidth usage
- Better user experience
- Improved Core Web Vitals scores

## Browser Support
- Modern browsers with Intersection Observer
- Automatic fallback for older browsers
- Progressive enhancement approach

## Configuration
```php
$lazyService->setConfig('threshold', '100px');
$lazyService->setConfig('fade_duration', 500);
```
