<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');
$r = $db->query('DESCRIBE kontrak_unit');
while($row = $r->fetch_assoc()) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . ' | ' . ($row['Default'] ?? 'NULL') . PHP_EOL;
}
echo "---\n";
$r2 = $db->query('SELECT COUNT(*) as c FROM kontrak_unit');
echo 'Current rows: ' . $r2->fetch_assoc()['c'] . PHP_EOL;
$db->close();
