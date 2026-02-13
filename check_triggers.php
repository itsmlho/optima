<?php
// Quick script to check triggers on inventory_attachment table
$db = new mysqli('localhost', 'root', '', 'optima_db');

if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "=== TRIGGERS ON inventory_attachment TABLE ===\n\n";

// Show all triggers
$result = $db->query("SHOW TRIGGERS WHERE `Table` = 'inventory_attachment'");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "Trigger: " . $row['Trigger'] . "\n";
        echo "Event: " . $row['Event'] . "\n";
        echo "Timing: " . $row['Timing'] . "\n";
        echo "Statement: " . substr($row['Statement'], 0, 200) . "...\n";
        echo str_repeat("-", 80) . "\n\n";
    }
} else {
    echo "No triggers found.\n";
}

// Show full CREATE TRIGGER statement for each trigger
echo "\n=== FULL TRIGGER DEFINITIONS ===\n\n";
$result = $db->query("SELECT TRIGGER_NAME, ACTION_TIMING, EVENT_MANIPULATION FROM information_schema.TRIGGERS WHERE EVENT_OBJECT_TABLE = 'inventory_attachment' AND EVENT_OBJECT_SCHEMA = 'optima_db'");

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $triggerName = $row['TRIGGER_NAME'];
        echo "Trigger: $triggerName (" . $row['ACTION_TIMING'] . " " . $row['EVENT_MANIPULATION'] . ")\n";
        $def = $db->query("SHOW CREATE TRIGGER `$triggerName`");
        if ($def && $defRow = $def->fetch_assoc()) {
            echo $defRow['SQL Original Statement'] . "\n";
        } else {
            echo "FAILED TO GET TRIGGER DEFINITION (might not exist)\n";
        }
        echo str_repeat("=", 80) . "\n\n";
    }
} else {
    echo "No triggers found in information_schema either.\n";
}

$db->close();
