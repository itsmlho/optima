<?php
// Try different database names
$possible_dbs = ['optima_ci', 'optima'];

foreach ($possible_dbs as $dbname) {
    echo "<h3>Testing Database: $dbname</h3>";
    
    mysqli_report(MYSQLI_REPORT_OFF);
    $mysqli = new mysqli('localhost', 'root', '', $dbname);
    
    if ($mysqli->connect_error) {
        echo "<p style='color:red;'>❌ Cannot connect to $dbname</p>";
        continue;
    }
    
    echo "<p style='color:green;'>✅ Connected to $dbname</p>";
    
    // Check if inventory_attachment table exists
    $check_table = $mysqli->query("SHOW TABLES LIKE 'inventory_attachment'");
    if (!$check_table || $check_table->num_rows == 0) {
        echo "<p>⚠️ Table 'inventory_attachment' not found</p>";
        $mysqli->close();
        continue;
    }
    
    // Check total records
    $query = $mysqli->query("SELECT COUNT(*) as total FROM inventory_attachment");
    if (!$query) {
        echo "<p style='color:red;'>Error querying table</p>";
        $mysqli->close();
        continue;
    }
    
    $total = $query->fetch_assoc();
    echo "<p><strong>Total records: {$total['total']}</strong></p>";
    
    if ($total['total'] > 0) {
        // This is the right database!
        echo "<h4 style='color:green;'>🎯 THIS IS THE ACTIVE DATABASE!</h4>";
        
        // Check types
        $query_types = $mysqli->query("
            SELECT tipe_item, COUNT(*) as count 
            FROM inventory_attachment 
            GROUP BY tipe_item
        ");
        echo "<h4>Counts by Type:</h4>";
        echo "<ul>";
        while ($row = $query_types->fetch_assoc()) {
            echo "<li>" . ($row['tipe_item'] ?? 'NULL') . ": {$row['count']}</li>";
        }
        echo "</ul>";
        
        // Check battery data with jenis_baterai
        $battery_check = $mysqli->query("
            SELECT 
                COUNT(*) as total_batteries,
                SUM(CASE WHEN b.jenis_baterai IS NOT NULL THEN 1 ELSE 0 END) as with_jenis,
                SUM(CASE WHEN b.jenis_baterai IS NULL THEN 1 ELSE 0 END) as without_jenis
            FROM inventory_attachment ia
            LEFT JOIN baterai b ON ia.baterai_id = b.id
            WHERE ia.tipe_item = 'battery'
        ");
        $battery_stats = $battery_check->fetch_assoc();
        echo "<h4>Battery Link Statistics:</h4>";
        echo "<ul>";
        echo "<li>Total batteries: {$battery_stats['total_batteries']}</li>";
        echo "<li>With jenis_baterai: {$battery_stats['with_jenis']}</li>";
        echo "<li>Without jenis_baterai: {$battery_stats['without_jenis']}</li>";
        echo "</ul>";
        
        // Check distinct jenis values
        $jenis_query = $mysqli->query("
            SELECT DISTINCT b.jenis_baterai, COUNT(*) as count
            FROM inventory_attachment ia
            INNER JOIN baterai b ON ia.baterai_id = b.id
            WHERE ia.tipe_item = 'battery' AND b.jenis_baterai IS NOT NULL
            GROUP BY b.jenis_baterai
        ");
        echo "<h4>Distinct jenis_baterai values:</h4>";
        echo "<ul>";
        while ($row = $jenis_query->fetch_assoc()) {
            $jenis = $row['jenis_baterai'] ?? 'NULL';
            echo "<li>'$jenis' (HEX: '" . bin2hex($jenis) . "'): {$row['count']} records</li>";
        }
        echo "</ul>";
    }
    
    $mysqli->close();
    echo "<hr>";
}
