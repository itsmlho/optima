<?php

$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

$tables = ['departemen', 'tipe_unit', 'kapasitas'];
foreach($tables as $table) {
    echo "=== $table TABLE ===" . PHP_EOL;
    $result = $mysqli->query('DESCRIBE ' . $table);
    if ($result) {
        while($row = $result->fetch_assoc()) {
            echo "  " . $row['Field'] . " | " . $row['Type'] . PHP_EOL;
        }
        
        // Show sample data
        $sample = $mysqli->query("SELECT * FROM $table LIMIT 2");
        if ($sample) {
            echo "  Sample data:" . PHP_EOL;
            while($data = $sample->fetch_assoc()) {
                $values = array_values($data);
                echo "    " . implode(' | ', $values) . PHP_EOL;
            }
        }
    } else {
        echo "  Error: " . $mysqli->error . PHP_EOL;
    }
    echo PHP_EOL;
}

$mysqli->close();

?>