<?php
$db = new mysqli('127.0.0.1', 'root', '', 'optima_ci');
$r = $db->query("SELECT ro.id, ro.name, p.key_name FROM role_permissions rp JOIN roles ro ON ro.id=rp.role_id JOIN permissions p ON p.id=rp.permission_id WHERE ro.id=5 AND p.key_name LIKE 'operational.delivery%'");
while ($row = $r->fetch_assoc()) echo json_encode($row) . PHP_EOL;
