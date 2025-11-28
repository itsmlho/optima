<?php
/**
 * Export CSV - Unit Inventory
 * Warehouse Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Unit_Inventory_' . date('Y-m-d_H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

try {
    // Get data from database using direct MySQL connection
    $host = 'localhost';
    $dbname = 'optima_ci';
    $username = 'root';
    $password = 'root';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Set headers
    $headers = ['No', 'No Unit', 'Serial Number', 'Tahun Unit', 'Lokasi Unit', 'Status Unit', 'Departemen', 'Tanggal Kirim', 'Keterangan'];
    fputcsv($output, $headers);
    
    $stmt = $pdo->prepare("
        SELECT 
            iu.*,
            d.nama_departemen,
            su.status_unit
        FROM inventory_unit iu
        LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
        LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
        ORDER BY iu.id_inventory_unit DESC
    ");
    $stmt->execute();
    $units = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $no = 1;
    foreach ($units as $unit) {
        $row = [
            $no++,
            $unit->no_unit,
            $unit->serial_number,
            $unit->tahun_unit,
            $unit->lokasi_unit,
            $unit->status_unit,
            $unit->nama_departemen,
            $unit->tanggal_kirim ? date('d/m/Y', strtotime($unit->tanggal_kirim)) : '-',
            $unit->keterangan
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}