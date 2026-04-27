<?php
$db = new mysqli('127.0.0.1', 'root', '', 'optima_ci');
if ($db->connect_error) die("Connect error: " . $db->connect_error);

echo "=== Permissions with 'delivery_instructions' ===\n";
$r = $db->query("SELECT id, permission_code, permission_name FROM rbac_permissions WHERE permission_code LIKE '%delivery_instructions%'");
while ($row = $r->fetch_assoc()) print_r($row);

echo "\n=== Role assignments for delivery_instructions ===\n";
$r2 = $db->query("SELECT rp.role_id, ro.role_name, p.permission_code 
    FROM rbac_role_permissions rp 
    JOIN rbac_roles ro ON ro.id=rp.role_id 
    JOIN rbac_permissions p ON p.id=rp.permission_id 
    WHERE p.permission_code LIKE '%delivery_instructions%'");
while ($row = $r2->fetch_assoc()) print_r($row);

echo "\n=== All roles ===\n";
$r3 = $db->query("SELECT id, role_name FROM rbac_roles ORDER BY id");
while ($row = $r3->fetch_assoc()) print_r($row);
