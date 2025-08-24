<?php
// SPK Viewer - Direct database access utility

// Connect to database
$pdo = new PDO('mysql:host=localhost;dbname=optima_db', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Header
echo "<!DOCTYPE html>
<html>
<head>
    <title>SPK Viewer</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1, h2 { color: #333; }
        table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        th, td { text-align: left; padding: 8px; border: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        th { background-color: #4CAF50; color: white; }
        pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .links a { margin-right: 10px; }
    </style>
</head>
<body>
    <h1>SPK Viewer</h1>";

// List all SPKs if no ID provided
if (!isset($_GET['id'])) {
    echo "<h2>Available SPKs</h2>";
    $stmt = $pdo->query("SELECT id, nomor_spk, pelanggan, jenis_spk, status FROM spk ORDER BY id DESC LIMIT 50");
    $spks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table>
        <tr>
            <th>ID</th>
            <th>SPK No.</th>
            <th>Customer</th>
            <th>Type</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>";
        
    foreach ($spks as $spk) {
        echo "<tr>
            <td>{$spk['id']}</td>
            <td>{$spk['nomor_spk']}</td>
            <td>{$spk['pelanggan']}</td>
            <td>{$spk['jenis_spk']}</td>
            <td>{$spk['status']}</td>
            <td>
                <a href='?id={$spk['id']}'>View Details</a> | 
                <a href='marketing/spk/print/{$spk['id']}' target='_blank'>Print</a>
            </td>
        </tr>";
    }
    echo "</table>";
} else {
    // Get SPK data
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM spk WHERE id = ?");
    $stmt->execute([$id]);
    $spk = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$spk) {
        echo "<div style='color: red;'>SPK with ID {$id} not found!</div>";
        echo "<p><a href='spk_viewer.php'>Back to list</a></p>";
        echo "</body></html>";
        exit;
    }
    
    echo "<div class='links'>
        <a href='spk_viewer.php'>Back to List</a> | 
        <a href='marketing/spk/print/{$id}' target='_blank'>Print View</a>
    </div>";
    
    echo "<h2>SPK #{$id} - {$spk['nomor_spk']}</h2>";
    
    // Get kontrak_spesifikasi data
    $kontrakSpec = null;
    if (!empty($spk['kontrak_spesifikasi_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM kontrak_spesifikasi WHERE id = ?");
        $stmt->execute([$spk['kontrak_spesifikasi_id']]);
        $kontrakSpec = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($kontrakSpec) {
            echo "<p><strong>Associated Kontrak Spesifikasi:</strong> #{$kontrakSpec['id']} ({$kontrakSpec['spek_kode']})</p>";
        }
    }
    
    // Get unit data
    $unit = null;
    if (!empty($spk['persiapan_unit_id'])) {
        $sql = "SELECT iu.*, 
                mu.merk_unit, mu.model_unit,
                tu.tipe as tipe_jenis, tu.jenis as jenis_unit,
                tm.tipe_mast as mast_model, 
                m.model_mesin as mesin_model, 
                b.tipe_baterai as baterai_model, 
                chr.tipe_charger as charger_model,
                k.kapasitas_unit as kapasitas_name, 
                d.nama_departemen as departemen_name
            FROM inventory_unit iu
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
            LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
            LEFT JOIN mesin m ON m.id = iu.model_mesin_id
            LEFT JOIN baterai b ON b.id = iu.model_baterai_id
            LEFT JOIN charger chr ON chr.id_charger = iu.model_charger_id
            LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
            LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
            WHERE iu.id_inventory_unit = ? OR iu.no_unit = ?";
            
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$spk['persiapan_unit_id'], $spk['persiapan_unit_id']]);
        $unit = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Parse SPK spesifikasi JSON
    $spesifikasi = null;
    if (!empty($spk['spesifikasi'])) {
        $spesifikasi = json_decode($spk['spesifikasi'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p style='color: red;'>Error parsing spesifikasi JSON: " . json_last_error_msg() . "</p>";
        }
    }
    
    // Display SPK Basic Info
    echo "<h3>Basic Info</h3>";
    echo "<table>
        <tr><th>Field</th><th>Value</th></tr>
        <tr><td>SPK No</td><td>{$spk['nomor_spk']}</td></tr>
        <tr><td>Type</td><td>{$spk['jenis_spk']}</td></tr>
        <tr><td>Customer</td><td>{$spk['pelanggan']}</td></tr>
        <tr><td>Location</td><td>{$spk['lokasi']}</td></tr>
        <tr><td>PO/Contract</td><td>{$spk['po_kontrak_nomor']}</td></tr>
        <tr><td>PIC</td><td>{$spk['pic']}</td></tr>
        <tr><td>Contact</td><td>{$spk['kontak']}</td></tr>
        <tr><td>Status</td><td>{$spk['status']}</td></tr>
        <tr><td>Unit Count</td><td>{$spk['jumlah_unit']}</td></tr>
    </table>";
    
    // Display Kontrak Spesifikasi Data
    if ($kontrakSpec) {
        echo "<h3>Kontrak Spesifikasi</h3>";
        echo "<table><tr><th>Field</th><th>Value</th></tr>";
        
        // Display important fields
        echo "<tr><td>Specification Code</td><td>{$kontrakSpec['spek_kode']}</td></tr>";
        
        // Format accessories
        $aksText = $kontrakSpec['aksesoris'] ?? '';
        if (!empty($aksText)) {
            try {
                $aksArray = json_decode($aksText, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($aksArray)) {
                    $aksText = implode(", ", $aksArray);
                }
            } catch (Exception $e) {
                // Keep original format if parsing fails
            }
        }
        echo "<tr><td>Accessories</td><td>{$aksText}</td></tr>";
        
        echo "</table>";
    }
    
    // Display Unit Data
    if ($unit) {
        echo "<h3>Unit Data</h3>";
        echo "<table><tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>Unit No</td><td>{$unit['no_unit']}</td></tr>";
        echo "<tr><td>Serial Number</td><td>{$unit['serial_number']}</td></tr>";
        echo "<tr><td>Brand & Model</td><td>{$unit['merk_unit']} {$unit['model_unit']}</td></tr>";
        echo "<tr><td>Type</td><td>{$unit['tipe_jenis']}</td></tr>";
        echo "<tr><td>Battery</td><td>{$unit['baterai_model']}</td></tr>";
        echo "<tr><td>Charger</td><td>{$unit['charger_model']}</td></tr>";
        echo "<tr><td>Department</td><td>{$unit['departemen_name']}</td></tr>";
        echo "<tr><td>Capacity</td><td>{$unit['kapasitas_name']}</td></tr>";
        echo "<tr><td>Mast</td><td>{$unit['mast_model']}</td></tr>";
        echo "</table>";
    }
    
    // Display Spesifikasi JSON
    if ($spesifikasi) {
        echo "<h3>Spesifikasi JSON Data</h3>";
        echo "<pre>" . htmlspecialchars(json_encode($spesifikasi, JSON_PRETTY_PRINT)) . "</pre>";
    }
    
    // Display Raw SPK Data
    echo "<h3>All SPK Fields</h3>";
    echo "<table><tr><th>Field</th><th>Value</th></tr>";
    foreach ($spk as $key => $value) {
        if ($key === 'spesifikasi') {
            continue; // Skip, we displayed it separately above
        }
        echo "<tr><td>{$key}</td><td>" . htmlspecialchars($value) . "</td></tr>";
    }
    echo "</table>";
}

echo "</body></html>";
?>
