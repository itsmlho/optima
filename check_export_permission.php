<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();

echo "=== Checking Export Silo Permission ===\n\n";

// Check perizinan export permission
$result = $db->query("SELECT key, name, module, category FROM permissions WHERE module = 'perizinan' AND (key LIKE '%export%' OR category = 'EXPORT')");
$rows = $result->getResultArray();

if (empty($rows)) {
    echo "❌ No export permission found for Perizinan module\n\n";
} else {
    echo "✅ Found export permissions:\n";
    foreach($rows as $row) {
        echo "  - {$row['key']} | {$row['name']} | Category: {$row['category']}\n";
    }
    echo "\n";
}

// Check role_permissions for perizinan export
$result2 = $db->query("
    SELECT r.name as role_name, p.key as permission_key
    FROM role_permissions rp
    JOIN roles r ON r.id = rp.role_id
    JOIN permissions p ON p.id = rp.permission_id
    WHERE p.module = 'perizinan' AND (p.key LIKE '%export%' OR p.category = 'EXPORT')
    ORDER BY r.name
");
$rows2 = $result2->getResultArray();

if (empty($rows2)) {
    echo "❌ No roles assigned to perizinan export permission\n\n";
} else {
    echo "✅ Roles with perizinan export permission:\n";
    foreach($rows2 as $row) {
        echo "  - {$row['role_name']} → {$row['permission_key']}\n";
    }
    echo "\n";
}

echo "=== Check Complete ===\n";
