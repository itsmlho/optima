<?php
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci;charset=utf8mb4', 'root', '');

foreach (['spk_sparepart', 'spk_sparepart_returns', 'spk'] as $t) {
    echo "=== $t ===\n";
    try {
        $cols = $pdo->query("DESCRIBE $t")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) echo "  " . $c['Field'] . ' | ' . $c['Type'] . "\n";
    } catch (Exception $e) {
        echo "  TABLE NOT FOUND\n";
    }
    echo "\n";
}
