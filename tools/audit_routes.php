<?php
/**
 * Audit: Routes pointing to non-existent controller methods
 * Run: php tools/audit_routes.php
 */

$routesContent = file_get_contents(__DIR__ . '/../app/Config/Routes.php');
$controllersBase = __DIR__ . '/../app/Controllers/';

// Extract all Controller::method references
preg_match_all('/[\'"]([A-Za-z][A-Za-z0-9\\\\\/]+)::([A-Za-z][A-Za-z0-9_]+)[\'"]/', $routesContent, $m);

$checked = [];
$missing = [];

for ($i = 0; $i < count($m[1]); $i++) {
    $cls    = $m[1][$i];
    $method = $m[2][$i];
    $key    = $cls . '::' . $method;
    if (isset($checked[$key])) continue;
    $checked[$key] = true;

    // Build file path
    $clsPath = str_replace(['\\', '\\\\'], '/', $cls);
    $file    = $controllersBase . $clsPath . '.php';

    if (!file_exists($file)) {
        $missing[] = "MISSING FILE  : $file  (route: $cls::$method)";
        continue;
    }

    $content = file_get_contents($file);
    if (!preg_match('/function\s+' . preg_quote($method, '/') . '\s*\(/', $content)) {
        $missing[] = "MISSING METHOD: $cls::$method";
    }
}

if (empty($missing)) {
    echo "All routes OK - no missing methods found.\n";
} else {
    echo count($missing) . " issue(s) found:\n\n";
    foreach ($missing as $m) {
        echo "  $m\n";
    }
}
