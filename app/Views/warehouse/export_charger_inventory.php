<?php
/**
 * Export CSV - Charger Inventory
 * Warehouse Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Charger_Inventory_' . date('Y-m-d_H-i-s') . '.csv"');
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
    $headers = ['No', 'ID', 'Merk', 'Tipe', 'Serial Number', 'Kondisi Fisik', 'Status', 'Lokasi Penyimpanan', 'Tanggal Masuk', 'Keterangan'];
    fputcsv($output, $headers);
    
    $stmt = $pdo->prepare("
        SELECT
            ia.*,
            ks.kondisi_fisik,
            ss.status_stock
        FROM inventory_attachment ia
        LEFT JOIN kondisi_stock ks ON ks.id_kondisi = ia.kondisi_fisik_id
        LEFT JOIN status_stock ss ON ss.id_status = ia.status_stock_id
        WHERE ia.tipe_item = 'charger'
        ORDER BY ia.id_inventory_attachment ASC
    ");
    $stmt->execute();
    $chargers = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $no = 1;
    foreach ($chargers as $charger) {
        $row = [
            $no++,
            $charger->id_inventory_attachment,
            $charger->merk,
            $charger->tipe,
            $charger->serial_number,
            $charger->kondisi_fisik,
            $charger->status_stock,
            $charger->lokasi_penyimpanan,
            $charger->tanggal_masuk ? date('d/m/Y', strtotime($charger->tanggal_masuk)) : '-',
            $charger->keterangan ?? '-'
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
