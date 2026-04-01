<?php

$views = 'app/Views';
$patterns = [
    'form-group'        => '/class="form-group"/',
    'data-toggle'       => '/data-toggle="/',
    'data-target'       => '/data-target="/',
    'sr-only BS4'       => '/class="sr-only"/',
    'data-placement'    => '/data-placement="/',
    'dt-local-load'     => '/<script[^>]+datatables\.net/',
    'modal-show-bs4'    => '/\$\([\'"][^"\']+[\'"]\)\.modal\([\'"]show[\'"]\)/',
    'modal-hide-bs4'    => '/\$\([\'"][^"\']+[\'"]\)\.modal\([\'"]hide[\'"]\)/',
    'float-dt-CSS'      => '/dataTables_(paginate|filter)[^}]+float\s*:/',
];

$results = array_fill_keys(array_keys($patterns), []);

$dirIter = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($views, RecursiveDirectoryIterator::SKIP_DOTS)
);

$skip = ['demo', 'emails', 'Examples'];

foreach ($dirIter as $file) {
    if ($file->getExtension() !== 'php') continue;
    $path = $file->getPathname();
    $skip_file = false;
    foreach ($skip as $s) {
        if (strpos($path, DIRECTORY_SEPARATOR . $s . DIRECTORY_SEPARATOR) !== false) {
            $skip_file = true; break;
        }
    }
    if ($skip_file) continue;

    $content = file_get_contents($path);
    if ($content === false) continue;

    foreach ($patterns as $k => $pat) {
        if (preg_match($pat, $content)) {
            $results[$k][] = str_replace(['app\\Views\\', 'app/Views/'], '', $path);
        }
    }
}

foreach ($results as $k => $files) {
    if (empty($files)) continue;
    echo "=== $k (" . count($files) . " files) ===\n";
    sort($files);
    foreach ($files as $f) {
        echo "  $f\n";
    }
    echo "\n";
}
