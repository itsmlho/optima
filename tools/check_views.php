<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "=== Finding broken views ===\n";

// Get all views
$r = $pdo->query("SELECT table_name FROM information_schema.views WHERE table_schema='optima_ci'");
$views = $r->fetchAll(PDO::FETCH_COLUMN);

$broken = [];
$working = [];

foreach ($views as $view) {
    try {
        $pdo->query("SELECT * FROM `$view` LIMIT 0");
        $working[] = $view;
    } catch (PDOException $e) {
        $broken[] = ['name' => $view, 'error' => $e->getMessage()];
        echo "  BROKEN: $view\n";
        echo "    Error: " . substr($e->getMessage(), 0, 200) . "\n";
        
        // Show the view definition
        try {
            $r2 = $pdo->query("SHOW CREATE VIEW `$view`");
            $def = $r2->fetch(PDO::FETCH_ASSOC);
            echo "    Definition: " . substr($def['Create View'], 0, 300) . "\n\n";
        } catch (PDOException $e2) {
            echo "    Cannot show definition\n\n";
        }
    }
}

echo "\nWorking views (" . count($working) . "): " . implode(', ', $working) . "\n";
echo "Broken views (" . count($broken) . "): " . implode(', ', array_column($broken, 'name')) . "\n";
