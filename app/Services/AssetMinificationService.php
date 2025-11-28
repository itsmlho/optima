<?php

namespace App\Services;

/**
 * Frontend Asset Minification Service
 * Mengoptimasi CSS dan JS assets untuk production
 */
class AssetMinificationService
{
    protected $cache;
    protected $config;
    
    // Asset directories
    protected $cssDir;
    protected $jsDir;
    protected $minDir;
    
    public function __construct()
    {
        $this->cache = \Config\Services::cache();
        $this->config = config('App');
        
        // Set directories
        $this->cssDir = ROOTPATH . 'public/assets/css/';
        $this->jsDir = ROOTPATH . 'public/assets/js/';
        $this->minDir = ROOTPATH . 'public/assets/min/';
        
        // Ensure minified directory exists
        if (!is_dir($this->minDir)) {
            mkdir($this->minDir, 0755, true);
            mkdir($this->minDir . 'css/', 0755, true);
            mkdir($this->minDir . 'js/', 0755, true);
        }
    }

    /**
     * Minify CSS file atau folder
     */
    public function minifyCSS($input, $output = null)
    {
        try {
            if (is_dir($input)) {
                return $this->minifyCSSDirectory($input, $output);
            } else {
                return $this->minifyCSSFile($input, $output);
            }
        } catch (\Exception $e) {
            log_message('error', 'CSS Minification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Minify single CSS file
     */
    protected function minifyCSSFile($inputFile, $outputFile = null)
    {
        if (!file_exists($inputFile)) {
            throw new \Exception("CSS file not found: {$inputFile}");
        }

        $css = file_get_contents($inputFile);
        $minifiedCSS = $this->compressCSS($css);

        if ($outputFile === null) {
            $pathInfo = pathinfo($inputFile);
            $outputFile = $this->minDir . 'css/' . $pathInfo['filename'] . '.min.css';
        }

        $result = file_put_contents($outputFile, $minifiedCSS);
        
        if ($result !== false) {
            $originalSize = filesize($inputFile);
            $minifiedSize = filesize($outputFile);
            $savings = round((($originalSize - $minifiedSize) / $originalSize) * 100, 2);
            
            log_message('info', "CSS minified: {$inputFile} -> {$outputFile} (Saved: {$savings}%)");
            return [
                'success' => true,
                'input' => $inputFile,
                'output' => $outputFile,
                'originalSize' => $originalSize,
                'minifiedSize' => $minifiedSize,
                'savings' => $savings . '%'
            ];
        }

        return false;
    }

    /**
     * Minify CSS directory
     */
    protected function minifyCSSDirectory($inputDir, $outputDir = null)
    {
        if ($outputDir === null) {
            $outputDir = $this->minDir . 'css/';
        }

        $results = [];
        $files = glob($inputDir . '*.css');

        foreach ($files as $file) {
            $pathInfo = pathinfo($file);
            if (!strpos($pathInfo['filename'], '.min')) { // Skip already minified files
                $outputFile = $outputDir . $pathInfo['filename'] . '.min.css';
                $result = $this->minifyCSSFile($file, $outputFile);
                if ($result) {
                    $results[] = $result;
                }
            }
        }

        return $results;
    }

    /**
     * Compress CSS content
     */
    protected function compressCSS($css)
    {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        
        // Remove unnecessary whitespace
        $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
        
        // Remove whitespace around specific characters
        $css = preg_replace('/\s*([\{\};:,>\+~])\s*/', '$1', $css);
        
        // Remove empty rules
        $css = preg_replace('/[^\}]+\{\s*\}/', '', $css);
        
        // Remove trailing semicolon before closing brace
        $css = str_replace(';}', '}', $css);
        
        // Remove any remaining extra whitespace
        $css = trim($css);
        
        return $css;
    }

    /**
     * Minify JavaScript file atau folder
     */
    public function minifyJS($input, $output = null)
    {
        try {
            if (is_dir($input)) {
                return $this->minifyJSDirectory($input, $output);
            } else {
                return $this->minifyJSFile($input, $output);
            }
        } catch (\Exception $e) {
            log_message('error', 'JS Minification failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Minify single JavaScript file
     */
    protected function minifyJSFile($inputFile, $outputFile = null)
    {
        if (!file_exists($inputFile)) {
            throw new \Exception("JS file not found: {$inputFile}");
        }

        $js = file_get_contents($inputFile);
        $minifiedJS = $this->compressJS($js);

        if ($outputFile === null) {
            $pathInfo = pathinfo($inputFile);
            $outputFile = $this->minDir . 'js/' . $pathInfo['filename'] . '.min.js';
        }

        $result = file_put_contents($outputFile, $minifiedJS);
        
        if ($result !== false) {
            $originalSize = filesize($inputFile);
            $minifiedSize = filesize($outputFile);
            $savings = round((($originalSize - $minifiedSize) / $originalSize) * 100, 2);
            
            log_message('info', "JS minified: {$inputFile} -> {$outputFile} (Saved: {$savings}%)");
            return [
                'success' => true,
                'input' => $inputFile,
                'output' => $outputFile,
                'originalSize' => $originalSize,
                'minifiedSize' => $minifiedSize,
                'savings' => $savings . '%'
            ];
        }

        return false;
    }

    /**
     * Minify JS directory
     */
    protected function minifyJSDirectory($inputDir, $outputDir = null)
    {
        if ($outputDir === null) {
            $outputDir = $this->minDir . 'js/';
        }

        $results = [];
        $files = glob($inputDir . '*.js');

        foreach ($files as $file) {
            $pathInfo = pathinfo($file);
            if (!strpos($pathInfo['filename'], '.min')) { // Skip already minified files
                $outputFile = $outputDir . $pathInfo['filename'] . '.min.js';
                $result = $this->minifyJSFile($file, $outputFile);
                if ($result) {
                    $results[] = $result;
                }
            }
        }

        return $results;
    }

    /**
     * Basic JavaScript compression
     */
    protected function compressJS($js)
    {
        // Remove single line comments (but preserve URLs)
        $js = preg_replace('/(?<!:)\/\/.*$/m', '', $js);
        
        // Remove multi-line comments
        $js = preg_replace('/\/\*[\s\S]*?\*\//', '', $js);
        
        // Remove unnecessary whitespace
        $js = preg_replace('/\s+/', ' ', $js);
        
        // Remove whitespace around operators and punctuation
        $js = preg_replace('/\s*([\{\};:,=\(\)\[\]&\|<>\+\-\*\/])\s*/', '$1', $js);
        
        // Remove trailing semicolons before }
        $js = str_replace(';}', '}', $js);
        
        return trim($js);
    }

    /**
     * Combine dan minify multiple CSS files
     */
    public function combineAndMinifyCSS($files, $outputFile)
    {
        $combinedCSS = '';
        $sourceFiles = [];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $css = file_get_contents($file);
                $combinedCSS .= "\n/* Source: " . basename($file) . " */\n";
                $combinedCSS .= $css . "\n";
                $sourceFiles[] = $file;
            }
        }

        if (!empty($combinedCSS)) {
            $minifiedCSS = $this->compressCSS($combinedCSS);
            $result = file_put_contents($outputFile, $minifiedCSS);

            if ($result !== false) {
                $originalSize = strlen($combinedCSS);
                $minifiedSize = filesize($outputFile);
                $savings = round((($originalSize - $minifiedSize) / $originalSize) * 100, 2);

                return [
                    'success' => true,
                    'sourceFiles' => $sourceFiles,
                    'output' => $outputFile,
                    'originalSize' => $originalSize,
                    'minifiedSize' => $minifiedSize,
                    'savings' => $savings . '%'
                ];
            }
        }

        return false;
    }

    /**
     * Combine dan minify multiple JS files
     */
    public function combineAndMinifyJS($files, $outputFile)
    {
        $combinedJS = '';
        $sourceFiles = [];

        foreach ($files as $file) {
            if (file_exists($file)) {
                $js = file_get_contents($file);
                $combinedJS .= "\n/* Source: " . basename($file) . " */\n";
                $combinedJS .= $js . ";\n"; // Ensure proper statement termination
                $sourceFiles[] = $file;
            }
        }

        if (!empty($combinedJS)) {
            $minifiedJS = $this->compressJS($combinedJS);
            $result = file_put_contents($outputFile, $minifiedJS);

            if ($result !== false) {
                $originalSize = strlen($combinedJS);
                $minifiedSize = filesize($outputFile);
                $savings = round((($originalSize - $minifiedSize) / $originalSize) * 100, 2);

                return [
                    'success' => true,
                    'sourceFiles' => $sourceFiles,
                    'output' => $outputFile,
                    'originalSize' => $originalSize,
                    'minifiedSize' => $minifiedSize,
                    'savings' => $savings . '%'
                ];
            }
        }

        return false;
    }

    /**
     * Get asset file dengan automatic fallback ke minified version
     */
    public function getAsset($type, $filename)
    {
        $isProduction = ENVIRONMENT === 'production';
        $pathInfo = pathinfo($filename);
        
        if ($type === 'css') {
            $minFile = $this->minDir . 'css/' . $pathInfo['filename'] . '.min.css';
            $originalFile = $this->cssDir . $filename;
        } else {
            $minFile = $this->minDir . 'js/' . $pathInfo['filename'] . '.min.js';
            $originalFile = $this->jsDir . $filename;
        }

        // In production, prefer minified files
        if ($isProduction && file_exists($minFile)) {
            return str_replace(ROOTPATH . 'public/', '', $minFile);
        }

        // Fallback to original file
        if (file_exists($originalFile)) {
            return str_replace(ROOTPATH . 'public/', '', $originalFile);
        }

        return null;
    }

    /**
     * Auto minify assets on file change
     */
    public function autoMinifyOnChange($watchDir)
    {
        $cacheKey = 'asset_timestamps_' . md5($watchDir);
        $storedTimestamps = $this->cache->get($cacheKey) ?: [];
        $currentTimestamps = [];
        $changedFiles = [];

        // Check CSS files
        $cssFiles = glob($watchDir . 'css/*.css');
        foreach ($cssFiles as $file) {
            if (!strpos(basename($file), '.min')) {
                $timestamp = filemtime($file);
                $currentTimestamps[$file] = $timestamp;

                if (!isset($storedTimestamps[$file]) || $storedTimestamps[$file] !== $timestamp) {
                    $changedFiles[] = $file;
                    $this->minifyCSSFile($file);
                }
            }
        }

        // Check JS files
        $jsFiles = glob($watchDir . 'js/*.js');
        foreach ($jsFiles as $file) {
            if (!strpos(basename($file), '.min')) {
                $timestamp = filemtime($file);
                $currentTimestamps[$file] = $timestamp;

                if (!isset($storedTimestamps[$file]) || $storedTimestamps[$file] !== $timestamp) {
                    $changedFiles[] = $file;
                    $this->minifyJSFile($file);
                }
            }
        }

        // Update cache dengan timestamps terbaru
        $this->cache->save($cacheKey, $currentTimestamps, 86400); // 24 hours

        return $changedFiles;
    }

    /**
     * Build production assets dengan versioning
     */
    public function buildProductionAssets()
    {
        $buildInfo = [
            'timestamp' => time(),
            'version' => date('Y.m.d.H.i'),
            'assets' => []
        ];

        // Minify all CSS
        log_message('info', 'Building production CSS assets...');
        $cssResults = $this->minifyCSS($this->cssDir);
        if ($cssResults) {
            $buildInfo['assets']['css'] = $cssResults;
        }

        // Minify all JS
        log_message('info', 'Building production JS assets...');
        $jsResults = $this->minifyJS($this->jsDir);
        if ($jsResults) {
            $buildInfo['assets']['js'] = $jsResults;
        }

        // Create combined core files
        $this->buildCombinedAssets();

        // Save build info
        $buildInfoFile = $this->minDir . 'build-info.json';
        file_put_contents($buildInfoFile, json_encode($buildInfo, JSON_PRETTY_PRINT));

        log_message('info', 'Production assets build completed.');
        return $buildInfo;
    }

    /**
     * Build combined core assets
     */
    protected function buildCombinedAssets()
    {
        // Core CSS files (adjust based on your structure)
        $coreCSS = [
            $this->cssDir . 'bootstrap.css',
            $this->cssDir . 'style.css',
            $this->cssDir . 'custom.css'
        ];

        // Core JS files
        $coreJS = [
            $this->jsDir . 'jquery.min.js',
            $this->jsDir . 'bootstrap.bundle.min.js',
            $this->jsDir . 'app.js'
        ];

        // Filter existing files
        $coreCSS = array_filter($coreCSS, 'file_exists');
        $coreJS = array_filter($coreJS, 'file_exists');

        // Create combined files
        if (!empty($coreCSS)) {
            $this->combineAndMinifyCSS($coreCSS, $this->minDir . 'css/core.min.css');
        }

        if (!empty($coreJS)) {
            $this->combineAndMinifyJS($coreJS, $this->minDir . 'js/core.min.js');
        }
    }

    /**
     * Get minification statistics
     */
    public function getMinificationStats()
    {
        $stats = [
            'css' => [
                'files' => 0,
                'originalSize' => 0,
                'minifiedSize' => 0,
                'savings' => '0%'
            ],
            'js' => [
                'files' => 0,
                'originalSize' => 0,
                'minifiedSize' => 0,
                'savings' => '0%'
            ]
        ];

        // CSS stats
        $cssFiles = glob($this->minDir . 'css/*.min.css');
        foreach ($cssFiles as $minFile) {
            $originalFile = str_replace(['/min/css/', '.min.css'], ['/css/', '.css'], $minFile);
            if (file_exists($originalFile)) {
                $stats['css']['files']++;
                $stats['css']['originalSize'] += filesize($originalFile);
                $stats['css']['minifiedSize'] += filesize($minFile);
            }
        }

        // JS stats
        $jsFiles = glob($this->minDir . 'js/*.min.js');
        foreach ($jsFiles as $minFile) {
            $originalFile = str_replace(['/min/js/', '.min.js'], ['/js/', '.js'], $minFile);
            if (file_exists($originalFile)) {
                $stats['js']['files']++;
                $stats['js']['originalSize'] += filesize($originalFile);
                $stats['js']['minifiedSize'] += filesize($minFile);
            }
        }

        // Calculate savings
        if ($stats['css']['originalSize'] > 0) {
            $cssSavings = (($stats['css']['originalSize'] - $stats['css']['minifiedSize']) / $stats['css']['originalSize']) * 100;
            $stats['css']['savings'] = round($cssSavings, 2) . '%';
        }

        if ($stats['js']['originalSize'] > 0) {
            $jsSavings = (($stats['js']['originalSize'] - $stats['js']['minifiedSize']) / $stats['js']['originalSize']) * 100;
            $stats['js']['savings'] = round($jsSavings, 2) . '%';
        }

        return $stats;
    }

    /**
     * Clean old minified files
     */
    public function cleanOldAssets($olderThan = 604800) // 7 days default
    {
        $cleaned = [];
        $cutoff = time() - $olderThan;

        $files = array_merge(
            glob($this->minDir . 'css/*.min.css'),
            glob($this->minDir . 'js/*.min.js')
        );

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                if (unlink($file)) {
                    $cleaned[] = $file;
                }
            }
        }

        return $cleaned;
    }
}