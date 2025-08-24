<?php
/**
 * Utility to view SPK and kontrak_spesifikasi data directly
 */

// Get the SPK ID from the URL, default to 17
$spk_id = $_GET['id'] ?? 17;

// Connect to the database
$host = 'localhost';
$dbname = 'optima_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the SPK record
    $stmt = $pdo->prepare("SELECT * FROM spk WHERE id = ?");
    $stmt->execute([$spk_id]);
    $spk = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$spk) {
        die("SPK #$spk_id not found");
    }
    
    // Get the kontrak_spesifikasi record if available
    $kontrak_spec = null;
    if (!empty($spk['kontrak_spesifikasi_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM kontrak_spesifikasi WHERE id = ?");
        $stmt->execute([$spk['kontrak_spesifikasi_id']]);
        $kontrak_spec = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get charger info if available
    $charger_info = null;
    if (!empty($kontrak_spec['charger_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM charger WHERE id_charger = ?");
        $stmt->execute([$kontrak_spec['charger_id']]);
        $charger_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Get unit info if available
    $unit_info = null;
    if (!empty($spk['persiapan_unit_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM inventory_unit WHERE id_inventory_unit = ? OR no_unit = ?");
        $stmt->execute([$spk['persiapan_unit_id'], $spk['persiapan_unit_id']]);
        $unit_info = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Parse spesifikasi JSON if available
    $spesifikasi = null;
    if (!empty($spk['spesifikasi'])) {
        $spesifikasi = json_decode($spk['spesifikasi'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $spesifikasi = "Error parsing JSON: " . json_last_error_msg();
        }
    }
    
    // Parse aksesoris JSON if available
    $aksesoris = null;
    if (!empty($kontrak_spec['aksesoris'])) {
        $aksesoris = json_decode($kontrak_spec['aksesoris'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $aksesoris = "Error parsing JSON: " . json_last_error_msg();
        }
    }
    
    // Output the data
    echo "<!DOCTYPE html>";
    echo "<html><head><title>SPK Viewer</title>";
    echo "<style>body{font-family: Arial, sans-serif;margin:20px;} 
    .data-section{margin-bottom:30px;padding:15px;border:1px solid #ddd;border-radius:5px;}
    h1,h2{color:#333;} 
    table{border-collapse:collapse;width:100%;}
    th,td{border:1px solid #ddd;padding:8px;text-align:left;}
    th{background-color:#f2f2f2;}
    .json{background-color:#f9f9f9;padding:10px;border:1px solid #ddd;overflow:auto;}</style>";
    echo "</head><body>";
    
    echo "<h1>SPK #$spk_id Details</h1>";
    
    // SPK Info
    echo "<div class='data-section'>";
    echo "<h2>SPK Information</h2>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    foreach ($spk as $key => $value) {
        if ($key === 'spesifikasi') continue; // Skip, we'll show this separately
        echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    echo "</table>";
    echo "</div>";
    
    // Kontrak Spesifikasi Info
    echo "<div class='data-section'>";
    echo "<h2>Kontrak Spesifikasi Information</h2>";
    if ($kontrak_spec) {
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($kontrak_spec as $key => $value) {
            if ($key === 'aksesoris') continue; // Skip, we'll show this separately
            echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p>No kontrak_spesifikasi data found for this SPK</p>";
    }
    echo "</div>";
    
    // Charger Info
    if ($charger_info) {
        echo "<div class='data-section'>";
        echo "<h2>Charger Information</h2>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($charger_info as $key => $value) {
            echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    
    // Unit Info
    if ($unit_info) {
        echo "<div class='data-section'>";
        echo "<h2>Unit Information</h2>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        foreach ($unit_info as $key => $value) {
            echo "<tr><td>$key</td><td>" . htmlspecialchars($value) . "</td></tr>";
        }
        echo "</table>";
        echo "</div>";
    }
    
    // Spesifikasi JSON
    echo "<div class='data-section'>";
    echo "<h2>Spesifikasi JSON</h2>";
    if (is_array($spesifikasi)) {
        echo "<pre class='json'>";
        echo htmlspecialchars(json_encode($spesifikasi, JSON_PRETTY_PRINT));
        echo "</pre>";
    } else {
        echo "<p>" . htmlspecialchars($spesifikasi ?? "No spesifikasi data") . "</p>";
    }
    echo "</div>";
    
    // Aksesoris JSON
    echo "<div class='data-section'>";
    echo "<h2>Aksesoris JSON</h2>";
    if (is_array($aksesoris)) {
        echo "<pre class='json'>";
        echo htmlspecialchars(json_encode($aksesoris, JSON_PRETTY_PRINT));
        echo "</pre>";
    } else {
        echo "<p>" . htmlspecialchars($aksesoris ?? "No aksesoris data") . "</p>";
    }
    echo "</div>";
    
    echo "</body></html>";
    
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
