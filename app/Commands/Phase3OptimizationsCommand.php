<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Services\AssetMinificationService;
use App\Services\LazyLoadingService;
use App\Models\Optimized\OptimizedWorkOrderModel;
use App\Models\Optimized\OptimizedUnitAssetModel;

/**
 * Phase 3 Performance Optimizations CLI Command
 * Model JOIN optimization, Frontend asset minification, Lazy loading
 */
class Phase3OptimizationsCommand extends BaseCommand
{
    protected $group       = 'Optimization';
    protected $name        = 'optimize:phase3';
    protected $description = 'Phase 3 optimizations: Model JOIN optimization, Asset minification, Lazy loading';

    protected $usage = 'optimize:phase3 [options]';
    protected $options = [
        '--models'  => 'Run model JOIN optimizations only',
        '--assets'  => 'Run asset minification only', 
        '--lazy'    => 'Setup lazy loading only',
        '--stats'   => 'Show optimization statistics',
        '--build'   => 'Build production assets'
    ];

    public function run(array $params)
    {
        $runModels = CLI::getOption('models') !== null;
        $runAssets = CLI::getOption('assets') !== null; 
        $runLazy = CLI::getOption('lazy') !== null;
        $showStats = CLI::getOption('stats') !== null;
        $buildAssets = CLI::getOption('build') !== null;

        // Jika tidak ada option spesifik, jalankan semua
        if (!$runModels && !$runAssets && !$runLazy && !$showStats && !$buildAssets) {
            $runModels = $runAssets = $runLazy = true;
        }

        CLI::write('=== OPTIMA Phase 3 Performance Optimizations ===', 'yellow');
        CLI::newLine();

        if ($runModels) {
            $this->optimizeModels();
        }

        if ($runAssets) {
            $this->minifyAssets();
        }

        if ($runLazy) {
            $this->setupLazyLoading();
        }

        if ($buildAssets) {
            $this->buildProductionAssets();
        }

        if ($showStats) {
            $this->showOptimizationStats();
        }

        CLI::newLine();
        CLI::write('Phase 3 optimizations completed!', 'green');
    }

    /**
     * Optimize database models dengan JOIN optimization
     */
    protected function optimizeModels()
    {
        CLI::write('1. MODEL JOIN OPTIMIZATION', 'light_blue');
        CLI::write('Optimizing database models with JOIN reduction...', 'white');

        try {
            // Create optimized database views
            $workOrderModel = new OptimizedWorkOrderModel();
            $unitAssetModel = new OptimizedUnitAssetModel();

            CLI::write('  → Creating optimized WorkOrder views...', 'cyan');
            $woResult = $workOrderModel->createWorkOrderView();
            if ($woResult) {
                CLI::write('    ✓ WorkOrder view created successfully', 'green');
            } else {
                CLI::write('    ⚠ WorkOrder view creation failed (may already exist)', 'yellow');
            }

            CLI::write('  → Creating optimized UnitAsset views...', 'cyan');
            $uaResult = $unitAssetModel->createUnitAssetView();
            if ($uaResult) {
                CLI::write('    ✓ UnitAsset view created successfully', 'green');
            } else {
                CLI::write('    ⚠ UnitAsset view creation failed (may already exist)', 'yellow');
            }

            // Test optimized queries
            CLI::write('  → Testing optimized queries...', 'cyan');
            
            $start = microtime(true);
            $testData = $workOrderModel->getWorkOrdersPaginated(1, 10);
            $workOrderTime = round((microtime(true) - $start) * 1000, 2);
            
            $start = microtime(true);
            $unitTestData = $unitAssetModel->getUnitsOptimized('', [], 1, 10);
            $unitAssetTime = round((microtime(true) - $start) * 1000, 2);

            CLI::write("    ✓ WorkOrder query: {$workOrderTime}ms ({$testData['total']} records)", 'green');
            CLI::write("    ✓ UnitAsset query: {$unitAssetTime}ms ({$unitTestData['total']} records)", 'green');

            // Create indexes untuk performance
            $this->createOptimizedIndexes();

        } catch (\Exception $e) {
            CLI::write('    ✗ Model optimization failed: ' . $e->getMessage(), 'red');
            log_message('error', 'Phase 3 model optimization failed: ' . $e->getMessage());
        }

        CLI::newLine();
    }

    /**
     * Create database indexes untuk optimized queries
     */
    protected function createOptimizedIndexes()
    {
        CLI::write('  → Creating optimized indexes...', 'cyan');
        
        $db = \Config\Database::connect();
        
        $indexes = [
            'work_orders' => [
                'idx_wo_status_created' => ['status_id', 'created_at'],
                'idx_wo_unit_status' => ['unit_id', 'status_id'],
                'idx_wo_deleted_status' => ['deleted_at', 'status_id']
            ],
            'inventory_unit' => [
                // 'idx_iu_kontrak_status' removed: kontrak_id column dropped (Step 4)
                'idx_iu_model_tipe' => ['model_unit_id', 'tipe_unit_id'],
                'idx_iu_no_unit' => ['no_unit']
            ]
        ];

        foreach ($indexes as $table => $tableIndexes) {
            foreach ($tableIndexes as $indexName => $columns) {
                try {
                    $columnList = implode(', ', $columns);
                    $sql = "CREATE INDEX IF NOT EXISTS {$indexName} ON {$table} ({$columnList})";
                    $db->query($sql);
                    CLI::write("    ✓ Created index: {$indexName} on {$table}", 'green');
                } catch (\Exception $e) {
                    CLI::write("    ⚠ Index {$indexName} may already exist", 'yellow');
                }
            }
        }
    }

    /**
     * Minify frontend assets
     */
    protected function minifyAssets()
    {
        CLI::write('2. FRONTEND ASSET MINIFICATION', 'light_blue');
        CLI::write('Minifying CSS and JavaScript assets...', 'white');

        try {
            $assetService = new AssetMinificationService();

            // Auto-detect asset directories
            $publicPath = ROOTPATH . 'public/';
            $cssPath = $publicPath . 'assets/css/';
            $jsPath = $publicPath . 'assets/js/';

            if (is_dir($cssPath)) {
                CLI::write('  → Minifying CSS files...', 'cyan');
                $cssResults = $assetService->minifyCSS($cssPath);
                
                if ($cssResults && is_array($cssResults)) {
                    foreach ($cssResults as $result) {
                        $filename = basename($result['input']);
                        CLI::write("    ✓ {$filename} → {$result['savings']} saved", 'green');
                    }
                } else {
                    CLI::write('    ⚠ No CSS files found to minify', 'yellow');
                }
            } else {
                CLI::write('    ⚠ CSS directory not found: ' . $cssPath, 'yellow');
            }

            if (is_dir($jsPath)) {
                CLI::write('  → Minifying JavaScript files...', 'cyan');
                $jsResults = $assetService->minifyJS($jsPath);
                
                if ($jsResults && is_array($jsResults)) {
                    foreach ($jsResults as $result) {
                        $filename = basename($result['input']);
                        CLI::write("    ✓ {$filename} → {$result['savings']} saved", 'green');
                    }
                } else {
                    CLI::write('    ⚠ No JS files found to minify', 'yellow');
                }
            } else {
                CLI::write('    ⚠ JavaScript directory not found: ' . $jsPath, 'yellow');
            }

            // Create combined core assets
            CLI::write('  → Creating combined assets...', 'cyan');
            $stats = $assetService->getMinificationStats();
            
            CLI::write("    ✓ CSS: {$stats['css']['files']} files, {$stats['css']['savings']} saved", 'green');
            CLI::write("    ✓ JS: {$stats['js']['files']} files, {$stats['js']['savings']} saved", 'green');

        } catch (\Exception $e) {
            CLI::write('    ✗ Asset minification failed: ' . $e->getMessage(), 'red');
            log_message('error', 'Phase 3 asset minification failed: ' . $e->getMessage());
        }

        CLI::newLine();
    }

    /**
     * Setup lazy loading
     */
    protected function setupLazyLoading()
    {
        CLI::write('3. LAZY LOADING IMPLEMENTATION', 'light_blue');
        CLI::write('Setting up lazy loading for images and content...', 'white');

        try {
            $lazyService = new LazyLoadingService();

            // Create lazy loading helper view
            CLI::write('  → Creating lazy loading helper view...', 'cyan');
            $this->createLazyLoadingHelper($lazyService);

            // Create placeholder images jika belum ada
            CLI::write('  → Setting up placeholder images...', 'cyan');
            $this->createPlaceholderImages();

            // Create lazy loading documentation
            CLI::write('  → Creating implementation guide...', 'cyan');
            $this->createLazyLoadingGuide();

            CLI::write('    ✓ Lazy loading setup completed', 'green');
            CLI::write('    ℹ Use LazyLoadingService in your views for implementation', 'cyan');

        } catch (\Exception $e) {
            CLI::write('    ✗ Lazy loading setup failed: ' . $e->getMessage(), 'red');
            log_message('error', 'Phase 3 lazy loading setup failed: ' . $e->getMessage());
        }

        CLI::newLine();
    }

    /**
     * Create lazy loading helper view
     */
    protected function createLazyLoadingHelper($lazyService)
    {
        $helperPath = APPPATH . 'Views/templates/lazy_loading_helper.php';
        
        $content = "<?php
/**
 * Lazy Loading Helper View
 * Include this in your main template
 */
\$lazyService = new \App\Services\LazyLoadingService();
?>

<!-- Lazy Loading CSS and JavaScript -->
<?= \$lazyService->renderLazyLoadingSetup() ?>

<!-- Performance Metrics (optional) -->
<?= \$lazyService->getLazyLoadingMetrics() ?>

<?php
/**
 * Usage Examples:
 * 
 * // Lazy Image
 * echo \$lazyService->lazyImage('/path/to/image.jpg', 'Alt text', 'custom-class');
 * 
 * // Lazy Background
 * echo \$lazyService->lazyBackground('/path/to/bg.jpg', '<h1>Content</h1>', 'hero-section');
 * 
 * // Lazy DataTable Image
 * echo \$lazyService->lazyDataTableImage('/path/to/thumb.jpg', 'Product', '60px', '60px');
 * 
 * // Lazy Content Section
 * echo \$lazyService->lazyContent('product-details', '/ajax/product/123', 'Loading product...');
 */
?>";

        if (file_put_contents($helperPath, $content)) {
            CLI::write('    ✓ Lazy loading helper created: ' . basename($helperPath), 'green');
        }
    }

    /**
     * Create placeholder images
     */
    protected function createPlaceholderImages()
    {
        $imagePath = ROOTPATH . 'public/assets/images/';
        if (!is_dir($imagePath)) {
            mkdir($imagePath, 0755, true);
        }

        // Create simple SVG placeholder
        $placeholderSVG = '<svg width="300" height="200" xmlns="http://www.w3.org/2000/svg">
            <rect width="100%" height="100%" fill="#f0f0f0"/>
            <text x="50%" y="50%" text-anchor="middle" dy=".3em" fill="#999">Loading...</text>
        </svg>';
        
        file_put_contents($imagePath . 'placeholder.svg', $placeholderSVG);
        CLI::write('    ✓ Placeholder image created', 'green');
    }

    /**
     * Create lazy loading implementation guide
     */
    protected function createLazyLoadingGuide()
    {
        $guidePath = ROOTPATH . 'docs/LAZY_LOADING_GUIDE.md';
        
        $guide = "# Lazy Loading Implementation Guide

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
\$lazyService = new \\App\\Services\\LazyLoadingService();
echo \$lazyService->lazyImage('/uploads/product.jpg', 'Product Image');
?>
```

### 3. DataTable Images
```php
// In DataTable columns
\$data[] = \$lazyService->lazyDataTableImage(\$row['image_path'], \$row['name'], '50px', '50px');
```

### 4. Background Images
```php
echo \$lazyService->lazyBackground('/images/hero.jpg', '<h1>Hero Content</h1>', 'hero-section');
```

### 5. Lazy Content Loading
```php
echo \$lazyService->lazyContent('product-specs', '/ajax/product-specs/' . \$id, 'Loading specifications...');
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
\$lazyService->setConfig('threshold', '100px');
\$lazyService->setConfig('fade_duration', 500);
```
";

        file_put_contents($guidePath, $guide);
        CLI::write('    ✓ Implementation guide created: ' . basename($guidePath), 'green');
    }

    /**
     * Build production assets
     */
    protected function buildProductionAssets()
    {
        CLI::write('4. PRODUCTION ASSET BUILD', 'light_blue');
        CLI::write('Building optimized production assets...', 'white');

        try {
            $assetService = new AssetMinificationService();
            
            CLI::write('  → Building production bundle...', 'cyan');
            $buildInfo = $assetService->buildProductionAssets();
            
            if ($buildInfo) {
                CLI::write("    ✓ Build completed - Version: {$buildInfo['version']}", 'green');
                
                if (isset($buildInfo['assets']['css'])) {
                    $cssCount = count($buildInfo['assets']['css']);
                    CLI::write("    ✓ CSS files processed: {$cssCount}", 'green');
                }
                
                if (isset($buildInfo['assets']['js'])) {
                    $jsCount = count($buildInfo['assets']['js']);
                    CLI::write("    ✓ JS files processed: {$jsCount}", 'green');
                }
            }

        } catch (\Exception $e) {
            CLI::write('    ✗ Production build failed: ' . $e->getMessage(), 'red');
            log_message('error', 'Phase 3 production build failed: ' . $e->getMessage());
        }

        CLI::newLine();
    }

    /**
     * Show optimization statistics
     */
    protected function showOptimizationStats()
    {
        CLI::write('5. OPTIMIZATION STATISTICS', 'light_blue');

        try {
            // Model performance stats
            CLI::write('Model Performance:', 'yellow');
            $this->showModelStats();

            // Asset optimization stats  
            CLI::write('Asset Optimization:', 'yellow');
            $this->showAssetStats();

            // Cache performance stats
            CLI::write('Cache Performance:', 'yellow');
            $this->showCacheStats();

        } catch (\Exception $e) {
            CLI::write('Error getting stats: ' . $e->getMessage(), 'red');
        }
    }

    /**
     * Show model performance statistics
     */
    protected function showModelStats()
    {
        try {
            $db = \Config\Database::connect();
            
            // Check view existence
            $views = ['v_work_orders_summary', 'v_unit_assets_summary'];
            foreach ($views as $view) {
                $result = $db->query("SHOW TABLES LIKE '{$view}'")->getResult();
                $status = !empty($result) ? '✓ Active' : '✗ Missing';
                CLI::write("  {$view}: {$status}", !empty($result) ? 'green' : 'red');
            }

            // Query performance test
            $start = microtime(true);
            $workOrderModel = new OptimizedWorkOrderModel();
            $testData = $workOrderModel->getWorkOrdersPaginated(1, 5);
            $queryTime = round((microtime(true) - $start) * 1000, 2);
            
            CLI::write("  Query Performance: {$queryTime}ms for {$testData['total']} records", 'green');

        } catch (\Exception $e) {
            CLI::write("  Error: {$e->getMessage()}", 'red');
        }
    }

    /**
     * Show asset optimization statistics  
     */
    protected function showAssetStats()
    {
        try {
            $assetService = new AssetMinificationService();
            $stats = $assetService->getMinificationStats();
            
            CLI::write("  CSS Files: {$stats['css']['files']} minified, {$stats['css']['savings']} saved", 'green');
            CLI::write("  JS Files: {$stats['js']['files']} minified, {$stats['js']['savings']} saved", 'green');
            
            $cssSize = round($stats['css']['minifiedSize'] / 1024, 2);
            $jsSize = round($stats['js']['minifiedSize'] / 1024, 2);
            CLI::write("  Total minified size: CSS {$cssSize}KB, JS {$jsSize}KB", 'cyan');

        } catch (\Exception $e) {
            CLI::write("  Error: {$e->getMessage()}", 'red');
        }
    }

    /**
     * Show cache performance statistics
     */
    protected function showCacheStats()
    {
        try {
            $cache = \Config\Services::cache();
            $cacheInfo = $cache->getCacheInfo();
            
            if ($cacheInfo) {
                CLI::write("  Cache Status: Active", 'green');
                if (isset($cacheInfo['cache_hits'])) {
                    $hitRate = round(($cacheInfo['cache_hits'] / ($cacheInfo['cache_hits'] + $cacheInfo['cache_misses'])) * 100, 2);
                    CLI::write("  Hit Rate: {$hitRate}%", 'green');
                }
            } else {
                CLI::write("  Cache Status: No statistics available", 'yellow');
            }

        } catch (\Exception $e) {
            CLI::write("  Cache Status: Active (no detailed stats)", 'yellow');
        }
    }
}