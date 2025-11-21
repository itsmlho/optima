#!/usr/bin/env php
<?php
/**
 * OPTIMA CSS Cleanup Tool
 * Automatically removes duplicate CSS from view files
 * 
 * Usage: php tools/cleanup_css_inline.php
 */

// File patterns yang perlu dihapus (karena sudah ada di optima-pro.css)
$patterns_to_remove = [
    // Stats cards
    '/\.card-stats:hover\s*\{[^}]+\}/',
    '/\.table-card,\s*\.card-stats\s*\{[^}]+\}/',
    '/\.filter-card\.active\s*\{[^}]+\}/',
    '/\.filter-card:hover\s*\{[^}]+\}/',
    
    // Modals
    '/\.modal-header\s*\{\s*background:\s*linear-gradient[^}]+\}/',
    
    // Tabs
    '/\.nav-tabs\s*\{[^}]+\}/',
    '/\.nav-tabs \.nav-item\s*\{[^}]+\}/',
    '/\.nav-tabs \.nav-link\s*\{[^}]+\}/',
    '/\.nav-tabs \.nav-link:hover\s*\{[^}]+\}/',
    '/\.nav-tabs \.nav-link\.active\s*\{[^}]+\}/',
    '/\.nav-tabs \.badge\s*\{[^}]+\}/',
    '/\.tab-content\s*\{[^}]+\}/',
    '/\.tab-pane\s*\{[^}]+\}/',
    
    // Buttons
    '/\.btn-success\s*\{[^}]+\}/',
    '/\.btn-success:hover\s*\{[^}]+\}/',
    '/\.btn-success:active\s*\{[^}]+\}/',
    '/\.btn-success::before\s*\{[^}]+\}/',
    '/\.btn-outline-success\s*\{[^}]+\}/',
    '/\.btn-outline-success:hover\s*\{[^}]+\}/',
    '/\.btn:disabled\s*\{[^}]+\}/',
    
    // Forms
    '/\.form-control\.is-valid[^}]+\}/',
    '/\.form-select\.is-valid[^}]+\}/',
    '/\.form-control\.is-invalid[^}]+\}/',
    '/\.form-select\.is-invalid[^}]+\}/',
    '/\.form-label \.text-danger\s*\{[^}]+\}/',
    
    // Tables
    '/\.clickable-row\s*\{[^}]+\}/',
    '/\.clickable-row:hover\s*\{[^}]+\}/',
    
    // Badges
    '/\.work-order-badge\s*\{[^}]+\}/',
    '/\.priority-critical\s*\{[^}]+\}/',
    '/\.priority-high\s*\{[^}]+\}/',
    '/\.priority-medium\s*\{[^}]+\}/',
    '/\.priority-low\s*\{[^}]+\}/',
    '/\.priority-routine\s*\{[^}]+\}/',
    '/\.status-open\s*\{[^}]+\}/',
    '/\.status-assigned\s*\{[^}]+\}/',
    '/\.status-in-progress\s*\{[^}]+\}/',
    '/\.status-completed\s*\{[^}]+\}/',
    '/\.status-closed\s*\{[^}]+\}/',
    
    // Activity & Quick Actions
    '/\.activity-item\s*\{[^}]+\}/',
    '/\.activity-icon\s*\{[^}]+\}/',
    '/\.quick-action-card\s*\{[^}]+\}/',
    '/\.quick-action-icon\s*\{[^}]+\}/',
    
    // Border cards
    '/\.border-left-primary\s*\{[^}]+\}/',
    '/\.border-left-success\s*\{[^}]+\}/',
    '/\.border-left-warning\s*\{[^}]+\}/',
    '/\.border-left-danger\s*\{[^}]+\}/',
    '/\.border-left-info\s*\{[^}]+\}/',
];

// Find all PHP view files (exclude print files)
$viewPath = __DIR__ . '/../app/Views';
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($viewPath),
    RecursiveIteratorIterator::SELF_FIRST
);

$cleanedFiles = [];
$errors = [];

foreach ($files as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $filePath = $file->getPathname();
        
        // Skip print files
        if (strpos($filePath, 'print_') !== false) {
            continue;
        }
        
        $content = file_get_contents($filePath);
        $originalContent = $content;
        
        // Check if file has CSS section
        if (strpos($content, "section('css')") === false) {
            continue;
        }
        
        // Remove duplicate CSS patterns
        foreach ($patterns_to_remove as $pattern) {
            $content = preg_replace($pattern, '', $content);
        }
        
        // Clean up empty style tags
        $content = preg_replace('/<style>\s*<\/style>/', '', $content);
        
        // Clean up excessive whitespace in CSS section
        $content = preg_replace('/(<style>)\s+/', '$1\n    /* CSS umum sudah ada di optima-pro.css */\n', $content);
        
        // If content changed, save it
        if ($content !== $originalContent) {
            file_put_contents($filePath, $content);
            $cleanedFiles[] = str_replace($viewPath . '/', '', $filePath);
        }
    }
}

// Output results
echo "\n====================================\n";
echo "  OPTIMA CSS CLEANUP TOOL\n";
echo "====================================\n\n";

if (count($cleanedFiles) > 0) {
    echo "✅ Successfully cleaned " . count($cleanedFiles) . " files:\n\n";
    foreach ($cleanedFiles as $file) {
        echo "  - {$file}\n";
    }
} else {
    echo "✅ No files needed cleaning (all clean!)\n";
}

if (count($errors) > 0) {
    echo "\n❌ Errors encountered:\n\n";
    foreach ($errors as $error) {
        echo "  - {$error}\n";
    }
}

echo "\n====================================\n";
echo "Done! Please review changes with:\n";
echo "  git diff app/Views/\n";
echo "====================================\n\n";

exit(0);

