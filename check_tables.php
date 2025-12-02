<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "=== CUSTOMER RELATED TABLES ===" . PHP_EOL;

// Check for customer tables
$result = $mysqli->query('SHOW TABLES LIKE "%customer%"');
echo "Customer tables:" . PHP_EOL;
while($row = $result->fetch_array()) {
    echo "  " . $row[0] . PHP_EOL;
}

echo PHP_EOL . "=== ALL BUSINESS TABLES ===" . PHP_EOL;

// Check all tables to understand structure
$result = $mysqli->query('SHOW TABLES');
echo "All tables:" . PHP_EOL;
while($row = $result->fetch_array()) {
    echo "  " . $row[0] . PHP_EOL;
}

// Now let's check the customer-related table structure
echo PHP_EOL . "=== CUSTOMER LOCATION TABLE ===" . PHP_EOL;
$result = $mysqli->query('DESCRIBE customer_location');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . PHP_EOL;
    }
} else {
    echo "  Error: " . $mysqli->error . PHP_EOL;
}

echo PHP_EOL . "=== CUSTOMER TABLE ===" . PHP_EOL;
$result = $mysqli->query('DESCRIBE customer');
if ($result) {
    while($row = $result->fetch_assoc()) {
        echo "  " . $row['Field'] . " | " . $row['Type'] . " | " . $row['Null'] . " | " . $row['Key'] . " | " . $row['Default'] . PHP_EOL;
    }
} else {
    echo "  Error: " . $mysqli->error . PHP_EOL;
}

$mysqli->close();

?>