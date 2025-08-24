<?php
// Direct SPK viewer for debugging

// Connect to the database
try {
    $pdo = new PDO('mysql:host=localhost;dbname=optima_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // List all SPKs for easy selection
    if (!isset($_GET['id'])) {
        echo "<h1>Available SPKs</h1>";
        $stmt = $pdo->query("SELECT id, nomor_spk, pelanggan, jenis_spk, status FROM spk ORDER BY id DESC");
        $spks = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>Nomor SPK</th><th>Pelanggan</th><th>Jenis</th><th>Status</th><th>Action</th></tr>";
        foreach ($spks as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['nomor_spk']}</td>";
            echo "<td>{$row['pelanggan']}</td>";
            echo "<td>{$row['jenis_spk']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "<td><a href='?id={$row['id']}'>View</a></td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }

    // Get the specific SPK by ID
    $spkId = (int)$_GET['id'];
    
    // Connect to the database
    $pdo = new PDO('mysql:host=localhost;dbname=optima_db', 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Get the SPK record
    $stmt = $pdo->prepare("SELECT * FROM spk WHERE id = ?");
    $stmt->execute([$spkId]);
    $spk = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$spk) {
        die("SPK with ID {$spkId} not found.");
    }
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>SPK #{$spkId} - {$spk['nomor_spk']}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            h1, h2 { color: #333; }
            .card { border: 1px solid #ddd; border-radius: 5px; padding: 15px; margin-bottom: 20px; }
            .card h2 { margin-top: 0; border-bottom: 1px solid #eee; padding-bottom: 10px; }
            pre { background-color: #f5f5f5; padding: 10px; border-radius: 5px; overflow-x: auto; }
            table { border-collapse: collapse; width: 100%; }
            th, td { text-align: left; padding: 8px; border: 1px solid #ddd; }
            tr:nth-child(even) { background-color: #f2f2f2; }
            th { background-color: #4CAF50; color: white; }
            .links { margin-bottom: 20px; }
            .links a { padding: 8px 16px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; margin-right: 10px; }
            .links a:hover { background-color: #45a049; }
        </style>
    </head>
    <body>
    <h1>SPK #{$spkId} ({$spk['nomor_spk']})</h1>
    
    <div class='links'>
        <a href='?'>Back to List</a>
        <a href='marketing/spk/print/{$spkId}' target='_blank'>View Print Format</a>
    </div>
    
    <!-- If there's a kontrak_spesifikasi_id, get that record too -->
    <div class='card'>
        <h2>SPK Details</h2>";
    
    $kontrakSpek = null;
    if (!empty($spk['kontrak_spesifikasi_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM kontrak_spesifikasi WHERE id = ?");
        $stmt->execute([$spk['kontrak_spesifikasi_id']]);
        $kontrakSpek = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($kontrakSpek) {
            echo "<p><strong>Associated Kontrak Spesifikasi:</strong> #{$kontrakSpek['id']} ({$kontrakSpek['spek_kode']})</p>";
        }
    }
    
    // Parse the spesifikasi JSON field
    $spesifikasi = null;
    if (!empty($spk['spesifikasi'])) {
        $spesifikasi = json_decode($spk['spesifikasi'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p>Error parsing spesifikasi JSON: " . json_last_error_msg() . "</p>";
            $spesifikasi = null;
        }
    }
    
    // Get unit data if available
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
                d.nama_departemen as departemen_name, 
                su.status_unit
            FROM inventory_unit iu
            LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
            LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
            LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
            LEFT JOIN mesin m ON m.id = iu.model_mesin_id
            LEFT JOIN baterai b ON b.id = iu.model_baterai_id
            LEFT JOIN charger chr ON chr.id_charger = iu.model_charger_id
            LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
            LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
            LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
            WHERE iu.id_inventory_unit = ? OR iu.no_unit = ?";
            
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$spk['persiapan_unit_id'], $spk['persiapan_unit_id']]);
        $unit = $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Display the SPK data in a user-friendly format
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    
    // Show important fields first
    $priorityFields = ['id', 'nomor_spk', 'jenis_spk', 'pelanggan', 'lokasi', 'po_kontrak_nomor', 'pic', 'kontak', 'status', 'jumlah_unit', 'kontrak_spesifikasi_id', 'persiapan_unit_id'];
    
    foreach ($priorityFields as $field) {
        if (isset($spk[$field])) {
            echo "<tr><td><strong>{$field}</strong></td><td>" . htmlspecialchars($spk[$field]) . "</td></tr>";
        }
    }
    
    // Other fields
    foreach ($spk as $key => $value) {
        if (!in_array($key, $priorityFields)) {
            // Handle special fields
            if ($key === 'spesifikasi') {
                echo "<tr><td><strong>{$key}</strong></td><td>";
                if ($spesifikasi) {
                    echo "<details><summary>Click to expand</summary>";
                    echo "<pre>" . htmlspecialchars(json_encode($spesifikasi, JSON_PRETTY_PRINT)) . "</pre>";
                    echo "</details>";
                } else {
                    echo "<em>No valid JSON data</em>";
                }
                echo "</td></tr>";
            } else {
                echo "<tr><td><strong>{$key}</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
            }
        }
    }
    echo "</table></div>";
    
    // Display kontrak_spesifikasi data if available
    if ($kontrakSpek) {
        echo "<div class='card'><h2>Kontrak Spesifikasi Data</h2>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        
        // Show important fields first
        $ksFields = ['id', 'spek_kode', 'aksesoris', 'kontrak_id'];
        
        foreach ($ksFields as $field) {
            if (isset($kontrakSpek[$field])) {
                // Handle the aksesoris field which is JSON
                if ($field === 'aksesoris') {
                    echo "<tr><td><strong>{$field}</strong></td><td>";
                if (!empty($value)) {
                    $aksArr = json_decode($value, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($aksArr)) {
                        echo implode(', ', $aksArr);
                    } else {
                        echo htmlspecialchars($value);
                    }
                } else {
                    echo "-";
                }
                echo "</td></tr>";
            } else {
                echo "<tr><td><strong>{$key}</strong></td><td>" . htmlspecialchars($value) . "</td></tr>";
            }
        }
        echo "</table>";
    }
    
    // Display unit data if available
    if ($unit) {
        echo "<div class='card'><h2>Unit Data</h2>";
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        
        // Show important fields first
        $unitFields = ['id_inventory_unit', 'no_unit', 'serial_number', 'merk_unit', 'model_unit', 'tipe_jenis', 
                      'jenis_unit', 'baterai_model', 'charger_model', 'departemen_name', 'kapasitas_name', 'mast_model'];
        
        foreach ($unitFields as $field) {
            if (isset($unit[$field])) {
                echo "<tr><td><strong>{$field}</strong></td><td>" . htmlspecialchars($unit[$field] ?? '-') . "</td></tr>";
            }
        }
        
        echo "</table></div>";
    }
    
    // Close the HTML page properly
    echo "</body></html>";
    
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
