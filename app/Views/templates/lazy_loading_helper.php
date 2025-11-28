<?php
/**
 * Lazy Loading Helper View
 * Include this in your main template
 */
$lazyService = new \App\Services\LazyLoadingService();
?>

<!-- Lazy Loading CSS and JavaScript -->
<?= $lazyService->renderLazyLoadingSetup() ?>

<!-- Performance Metrics (optional) -->
<?= $lazyService->getLazyLoadingMetrics() ?>

<?php
/**
 * Usage Examples:
 * 
 * // Lazy Image
 * echo $lazyService->lazyImage('/path/to/image.jpg', 'Alt text', 'custom-class');
 * 
 * // Lazy Background
 * echo $lazyService->lazyBackground('/path/to/bg.jpg', '<h1>Content</h1>', 'hero-section');
 * 
 * // Lazy DataTable Image
 * echo $lazyService->lazyDataTableImage('/path/to/thumb.jpg', 'Product', '60px', '60px');
 * 
 * // Lazy Content Section
 * echo $lazyService->lazyContent('product-details', '/ajax/product/123', 'Loading product...');
 */
?>