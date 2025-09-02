<?php
// Simple script to check and delete SPK records with ID = 0
require_once 'system/bootstrap.php';

use Config\Database;

$db = Database::connect();

echo "Checking for SPK records with ID = 0...\n";

// Check for SPK records with ID = 0
$result = $db->table('spk')->where('id', 0)->get()->getResultArray();

echo 'Found ' . count($result) . " SPK records with ID = 0:\n";

if (count($result) > 0) {
    foreach ($result as $row) {
        echo "ID: {$row['id']}, Nomor SPK: {$row['nomor_spk']}, Status: {$row['status']}, Created: {$row['dibuat_pada']}\n";
    }

    echo "\nDo you want to delete these records? (y/n): ";
    $handle = fopen("php://stdin", "r");
    $response = trim(fgets($handle));
    fclose($handle);

    if (strtolower($response) === 'y') {
        // Delete the records
        $deleted = $db->table('spk')->where('id', 0)->delete();
        echo "Deleted $deleted SPK records with ID = 0\n";

        // Also check and delete related records in spk_status_history
        $statusHistory = $db->table('spk_status_history')->where('spk_id', 0)->get()->getResultArray();
        if (count($statusHistory) > 0) {
            $deletedHistory = $db->table('spk_status_history')->where('spk_id', 0)->delete();
            echo "Deleted $deletedHistory related status history records\n";
        }
    } else {
        echo "Deletion cancelled.\n";
    }
} else {
    echo "No SPK records with ID = 0 found.\n";
}
?>
