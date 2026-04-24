<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) die('Connect failed: ' . $db->connect_error . "\n");

// Check permission exists
$r = $db->query("SELECT * FROM permissions WHERE permission_code LIKE 'purchasing.unit_sale%'");
if (!$r) { echo 'permissions table error: ' . $db->error . "\n"; }
else {
    echo 'Permissions with purchasing.unit_sale:' . PHP_EOL;
    if ($r->num_rows === 0) echo '  [NONE FOUND]' . PHP_EOL;
    while ($row = $r->fetch_assoc()) echo '  ' . json_encode($row) . PHP_EOL;
}

// Check if permission granted to any role
$r2 = $db->query("
    SELECT rp.role_id, rp.permission_id, p.permission_code
    FROM role_permissions rp
    JOIN permissions p ON p.id = rp.permission_id
    WHERE p.permission_code LIKE 'purchasing.unit_sale%'
");
if (!$r2) { echo 'role_permissions error: ' . $db->error . "\n"; }
else {
    echo PHP_EOL . 'Role grants for purchasing.unit_sale:' . PHP_EOL;
    if ($r2->num_rows === 0) echo '  [NO ROLE GRANTS]' . PHP_EOL;
    while ($row = $r2->fetch_assoc()) echo '  ' . json_encode($row) . PHP_EOL;
}
