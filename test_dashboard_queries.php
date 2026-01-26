<?php
// Test Dashboard Queries
// Run this file directly in browser: localhost/optima/test_dashboard_queries.php

// Simple database connection without full CI4 bootstrap
$dbConfig = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'optima_ci',
    'DBDriver' => 'MySQLi',
    'charset'  => 'utf8mb4',
];

try {
    $db = new mysqli(
        $dbConfig['hostname'],
        $dbConfig['username'],
        $dbConfig['password'],
        $dbConfig['database']
    );
    
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }
    
    $db->set_charset($dbConfig['charset']);
} catch (Exception $e) {
    die("Database connection error: " . $e->getMessage());
}

echo "<h1>Dashboard Query Audit</h1>";
echo "<style>table{border-collapse:collapse;width:100%;margin:20px 0}th,td{border:1px solid #ddd;padding:8px;text-align:left}th{background:#4CAF50;color:white}h2{color:#333;border-bottom:2px solid #4CAF50;padding-bottom:5px}</style>";

// 1. Test WO by Category
echo "<h2>1. Work Orders by Category (Bulan Ini)</h2>";
try {
    $query = $db->query("
        SELECT 
            woc.id,
            woc.category_name,
            COUNT(wo.id) as count
        FROM work_orders wo
        LEFT JOIN work_order_categories woc ON wo.category_id = woc.id
        WHERE DATE(wo.created_at) >= '" . date('Y-m-01') . "'
        GROUP BY wo.category_id, woc.category_name
        ORDER BY count DESC
        LIMIT 10
    ");
    $result = $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    
    echo "<p><strong>Total Records:</strong> " . count($result) . "</p>";
    if (count($result) > 0) {
        echo "<table><tr><th>ID</th><th>Category Name</th><th>Count</th></tr>";
        foreach ($result as $row) {
            echo "<tr><td>{$row['id']}</td><td>{$row['category_name']}</td><td>{$row['count']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>NO DATA FOUND - Checking all work_orders...</p>";
        
        // Check total work orders
        $qTotal = $db->query("SELECT COUNT(*) as total FROM work_orders");
        $totalWO = $qTotal ? $qTotal->fetch_object()->total : 0;
        echo "<p>Total Work Orders in database: <strong>{$totalWO}</strong></p>";
        
        // Check work orders this month
        $qMonth = $db->query("SELECT COUNT(*) as total FROM work_orders WHERE DATE(created_at) >= '" . date('Y-m-01') . "'");
        $woThisMonth = $qMonth ? $qMonth->fetch_object()->total : 0;
        echo "<p>Work Orders this month: <strong>{$woThisMonth}</strong></p>";
        
        // Check categories
        $qCat = $db->query("SELECT id, category_name FROM work_order_categories LIMIT 5");
        $categories = $qCat ? $qCat->fetch_all(MYSQLI_ASSOC) : [];
        echo "<p>Sample Categories:</p><pre>" . print_r($categories, true) . "</pre>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>ERROR: " . $e->getMessage() . "</p>";
}

// 2. Test SPK by Jenis Perintah
echo "<h2>2. SPK by Jenis Perintah Kerja (Bulan Ini)</h2>";
try {
    $query = $db->query("
        SELECT 
            jpk.id,
            jpk.nama as jenis,
            jpk.kode,
            COUNT(s.id) as count
        FROM spk s
        LEFT JOIN jenis_perintah_kerja jpk ON s.jenis_perintah_kerja_id = jpk.id
        WHERE DATE(s.dibuat_pada) >= '" . date('Y-m-01') . "'
        GROUP BY s.jenis_perintah_kerja_id, jpk.nama
        ORDER BY count DESC
    ");
    $result = $query ? $query->fetch_all(MYSQLI_ASSOC) : [];
    
    echo "<p><strong>Total Records:</strong> " . count($result) . "</p>";
    if (count($result) > 0) {
        echo "<table><tr><th>ID</th><th>Jenis</th><th>Kode</th><th>Count</th></tr>";
        foreach ($result as $row) {
            echo "<tr><td>{$row['id']}</td><td>{$row['jenis']}</td><td>{$row['kode']}</td><td>{$row['count']}</td></tr>";
        }
        echo "</table>";
        
        // Additional diagnostics for NULL jenis
        echo "<p style='color:orange'><strong>Diagnostics:</strong></p>";
        $qNullCheck = $db->query("SELECT COUNT(*) as total FROM spk WHERE jenis_perintah_kerja_id IS NULL AND DATE(dibuat_pada) >= '" . date('Y-m-01') . "'");
        $nullCount = $qNullCheck ? $qNullCheck->fetch_object()->total : 0;
        echo "<p>SPK with NULL jenis_perintah_kerja_id this month: <strong>{$nullCount}</strong></p>";
        
        $qWithJenis = $db->query("SELECT COUNT(*) as total FROM spk WHERE jenis_perintah_kerja_id IS NOT NULL AND DATE(dibuat_pada) >= '" . date('Y-m-01') . "'");
        $withJenis = $qWithJenis ? $qWithJenis->fetch_object()->total : 0;
        echo "<p>SPK with valid jenis_perintah_kerja_id this month: <strong>{$withJenis}</strong></p>";
        
        // Sample SPK records
        $qSample = $db->query("SELECT id, nomor_spk, jenis_spk, jenis_perintah_kerja_id, dibuat_pada FROM spk WHERE DATE(dibuat_pada) >= '" . date('Y-m-01') . "' LIMIT 5");
        $sampleSPK = $qSample ? $qSample->fetch_all(MYSQLI_ASSOC) : [];
        echo "<p>Sample SPK Records this month:</p>";
        echo "<table><tr><th>ID</th><th>Nomor SPK</th><th>Jenis SPK</th><th>Jenis Perintah Kerja ID</th><th>Dibuat Pada</th></tr>";
        foreach ($sampleSPK as $spk) {
            $jenisId = $spk['jenis_perintah_kerja_id'] ?? 'NULL';
            echo "<tr><td>{$spk['id']}</td><td>{$spk['nomor_spk']}</td><td>{$spk['jenis_spk']}</td><td style='color:red;font-weight:bold'>{$jenisId}</td><td>{$spk['dibuat_pada']}</td></tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color:red'>NO DATA FOUND - Checking all SPK...</p>";
        
        // Check total SPK
        $qTotal = $db->query("SELECT COUNT(*) as total FROM spk");
        $totalSPK = $qTotal ? $qTotal->fetch_object()->total : 0;
        echo "<p>Total SPK in database: <strong>{$totalSPK}</strong></p>";
        
        // Check SPK this month
        $qMonth = $db->query("SELECT COUNT(*) as total FROM spk WHERE DATE(dibuat_pada) >= '" . date('Y-m-01') . "'");
        $spkThisMonth = $qMonth ? $qMonth->fetch_object()->total : 0;
        echo "<p>SPK this month: <strong>{$spkThisMonth}</strong></p>";
        
        // Check jenis perintah kerja
        $qJenis = $db->query("SELECT id, kode, nama FROM jenis_perintah_kerja LIMIT 5");
        $jenisPerintah = $qJenis ? $qJenis->fetch_all(MYSQLI_ASSOC) : [];
        echo "<p>Sample Jenis Perintah:</p><pre>" . print_r($jenisPerintah, true) . "</pre>";
        
        // Check SPK structure
        $qSample = $db->query("SELECT * FROM spk LIMIT 1");
        $sampleSPK = $qSample ? $qSample->fetch_assoc() : [];
        echo "<p>Sample SPK Record:</p><pre>" . print_r($sampleSPK, true) . "</pre>";
    }
} catch (\Exception $e) {
    echo "<p style='color:red'>ERROR: " . $e->getMessage() . "</p>";
}

// 3. Test Customers
echo "<h2>3. Customers Data</h2>";
try {
    $qTotal = $db->query("SELECT COUNT(*) as total FROM customers");
    $totalCustomers = $qTotal ? $qTotal->fetch_object()->total : 0;
    
    $qLast = $db->query("SELECT COUNT(*) as total FROM customers WHERE DATE(created_at) < '" . date('Y-m-01') . "'");
    $lastMonthCustomers = $qLast ? $qLast->fetch_object()->total : 0;
    
    echo "<p>Total Customers: <strong>{$totalCustomers}</strong></p>";
    echo "<p>Customers before this month: <strong>{$lastMonthCustomers}</strong></p>";
    
    // Sample customers
    $qSample = $db->query("SELECT id, customer_name, created_at FROM customers ORDER BY created_at DESC LIMIT 5");
    $sampleCustomers = $qSample ? $qSample->fetch_all(MYSQLI_ASSOC) : [];
    echo "<table><tr><th>ID</th><th>Name</th><th>Created At</th></tr>";
    foreach ($sampleCustomers as $cust) {
        echo "<tr><td>{$cust['id']}</td><td>{$cust['customer_name']}</td><td>{$cust['created_at']}</td></tr>";
    }
    echo "</table>";
} catch (\Exception $e) {
    echo "<p style='color:red'>ERROR: " . $e->getMessage() . "</p>";
}

// 4. Test Contracts
echo "<h2>4. Contracts Data</h2>";
try {
    $qActive = $db->query("SELECT COUNT(*) as total FROM kontrak WHERE status = 'Aktif'");
    $activeContracts = $qActive ? $qActive->fetch_object()->total : 0;
    
    $qExpiring = $db->query("
        SELECT COUNT(*) as total FROM kontrak 
        WHERE status = 'Aktif' 
        AND tanggal_berakhir >= '" . date('Y-m-d') . "'
        AND tanggal_berakhir <= '" . date('Y-m-d', strtotime('+30 days')) . "'
    ");
    $expiringContracts = $qExpiring ? $qExpiring->fetch_object()->total : 0;
    
    echo "<p>Active Contracts: <strong>{$activeContracts}</strong></p>";
    echo "<p>Expiring in 30 days: <strong>{$expiringContracts}</strong></p>";
} catch (\Exception $e) {
    echo "<p style='color:red'>ERROR: " . $e->getMessage() . "</p>";
}

// 5. Test Assets
echo "<h2>5. Assets Data</h2>";
try {
    $qUnits = $db->query("SELECT COUNT(*) as total FROM inventory_unit");
    $totalUnits = $qUnits ? $qUnits->fetch_object()->total : 0;
    
    $qAtt = $db->query("SELECT COUNT(*) as total FROM inventory_attachment WHERE tipe_item = 'attachment' AND attachment_id IS NOT NULL");
    $totalAttachments = $qAtt ? $qAtt->fetch_object()->total : 0;
    
    $qCharge = $db->query("SELECT COUNT(*) as total FROM inventory_attachment WHERE tipe_item = 'charger' AND charger_id IS NOT NULL");
    $totalChargers = $qCharge ? $qCharge->fetch_object()->total : 0;
    
    $qBat = $db->query("SELECT COUNT(*) as total FROM inventory_attachment WHERE tipe_item = 'battery' AND baterai_id IS NOT NULL");
    $totalBaterai = $qBat ? $qBat->fetch_object()->total : 0;
    
    echo "<p>Total Units: <strong>{$totalUnits}</strong></p>";
    echo "<p>Total Attachments: <strong>{$totalAttachments}</strong></p>";
    echo "<p>Total Chargers: <strong>{$totalChargers}</strong></p>";
    echo "<p>Total Baterai: <strong>{$totalBaterai}</strong></p>";
    echo "<p>Total Assets: <strong>" . ($totalUnits + $totalAttachments + $totalChargers + $totalBaterai) . "</strong></p>";
} catch (\Exception $e) {
    echo "<p style='color:red'>ERROR: " . $e->getMessage() . "</p>";
}

echo "<hr><p style='color:green;font-weight:bold'>Audit Complete - " . date('Y-m-d H:i:s') . "</p>";
