<?php
/**
 * Mass-replace csrf_hash() with getCsrfToken() in all view files
 * Run once via CLI: php public/fix_csrf.php
 * DELETE AFTER USE!
 */

$viewsDir = __DIR__ . '/../app/Views';
$replaced = 0;
$filesModified = 0;

// Patterns to replace - old hardcoded csrf_hash() with dynamic getCsrfToken()
// Pattern 1: '<?= csrf_hash() ?>' → getCsrfToken()
// Pattern 2: "<?= csrf_hash() ?>" → getCsrfToken()
// Pattern 3: const csrfToken = '<?= csrf_hash() ?>' → const csrfToken = getCsrfToken()

$patterns = [
    // In JS string values: '<?= csrf_hash() ?>' -> getCsrfToken()
    "/'<\?= csrf_hash\(\) \?>'/" => "getCsrfToken()",
    // In JS string values: "<?= csrf_hash() ?>" -> getCsrfToken()  
    '/\"<\?= csrf_hash\(\) \?>\"/' => "getCsrfToken()",
    // As direct value: = '<?= csrf_hash() ?>'; -> = getCsrfToken();
    // Already covered by first pattern
];

$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($viewsDir));
$fileList = [];
foreach ($files as $file) {
    if ($file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        if (strpos($content, 'csrf_hash()') !== false) {
            $fileList[] = $file->getPathname();
        }
    }
}

echo "Files with csrf_hash(): " . count($fileList) . "\n";
foreach ($fileList as $path) {
    echo " - " . basename(dirname($path)) . "/" . basename($path) . "\n";
}

echo "\nApplying replacements...\n";

foreach ($fileList as $path) {
    $content = file_get_contents($path);
    $original = $content;
    
    // Replace common patterns
    $content = preg_replace("/'<\?= csrf_hash\(\) \?>'/" , "getCsrfToken()", $content);
    $content = preg_replace('/"<\?= csrf_hash\(\) \?>"/', "getCsrfToken()", $content);
    
    if ($content !== $original) {
        file_put_contents($path, $content);
        $filesModified++;
        $count = substr_count($original, 'csrf_hash()') - substr_count($content, 'csrf_hash()');
        $replaced += $count;
        echo "Modified: " . basename(dirname($path)) . "/" . basename($path) . " ($count replacements)\n";
    }
}

// Check remaining
$remaining = 0;
foreach ($fileList as $path) {
    $content = file_get_contents($path);
    if (strpos($content, 'csrf_hash()') !== false) {
        $remaining++;
        echo "REMAINING (needs manual fix): " . basename($path) . "\n";
    }
}

echo "\nDone! Modified $filesModified files, $replaced replacements total.\n";
echo "Remaining files with csrf_hash(): $remaining\n";
