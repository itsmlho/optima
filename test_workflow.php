<?php
// Test SPK Status Update Workflow
$mysqli = new mysqli('localhost', 'root', '', 'optima_ci');

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}

echo "Testing SPK Status Update Workflow...\n";

$spkId = 94;

// Check current SPK status
$result = $mysqli->query("SELECT id, nomor_spk, status, jumlah_unit FROM spk WHERE id = {$spkId}");
if ($row = $result->fetch_assoc()) {
    echo "SPK {$spkId} current status: " . $row['status'] . ", Units: " . $row['jumlah_unit'] . "\n";
    $totalUnits = (int) $row['jumlah_unit'];
} else {
    die("SPK {$spkId} not found\n");
}

// Check completed PDI stages  
$result = $mysqli->query("SELECT COUNT(*) as count FROM spk_unit_stages WHERE spk_id = {$spkId} AND stage_name = 'pdi' AND tanggal_approve IS NOT NULL");
$completedPDI = $result->fetch_assoc()['count'];

echo "Completed PDI stages: {$completedPDI} / {$totalUnits}\n";

if ($completedPDI >= $totalUnits) {
    echo "All PDI stages completed, updating status to READY...\n";
    $mysqli->query("UPDATE spk SET status = 'READY', diperbarui_pada = NOW() WHERE id = {$spkId}");
    echo "Status updated successfully!\n";
} else {
    echo "Not all PDI stages completed yet.\n";
}

// Verify the update
$result = $mysqli->query("SELECT status FROM spk WHERE id = {$spkId}");
$newStatus = $result->fetch_assoc()['status'];
echo "New status: {$newStatus}\n";

$mysqli->close();