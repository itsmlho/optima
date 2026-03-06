<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');

echo "=== CUSTOMERS ===\n";
$r = $db->query('SHOW COLUMNS FROM customers');
while ($row = $r->fetch_assoc()) {
    echo $row['Field'] . "\n";
}

echo "\n=== CUSTOMER_LOCATIONS ===\n";
$r = $db->query('SHOW COLUMNS FROM customer_locations');
while ($row = $r->fetch_assoc()) {
    echo $row['Field'] . "\n";
}

echo "\n=== KONTRAK ===\n";
$r = $db->query('SHOW COLUMNS FROM kontrak');
while ($row = $r->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
