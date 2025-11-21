<?php
/**
 * Export CSV - PO Progres
 * Purchasing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="PO_Progres_' . date('Y-m-d_H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

try {
    // Get data from database using direct MySQL connection
    $host = 'localhost';
    $dbname = 'optima_db';
    $username = 'root';
    $password = 'root';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Set headers
    $headers = ['No', 'No PO', 'Supplier', 'Tanggal PO', 'Total Items', 'Total Value', 'Status', 'Created Date'];
    fputcsv($output, $headers);
    
    $stmt = $pdo->prepare("
        SELECT 
            po.*, 
            s.nama_supplier
        FROM purchase_orders po
        LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id
        WHERE po.status = 'Pending'
        ORDER BY po.created_at DESC
    ");
    $stmt->execute();
    $poProgres = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $no = 1;
    foreach ($poProgres as $po) {
        $row = [
            $no++,
            $po->no_po,
            $po->nama_supplier,
            date('d/m/Y', strtotime($po->tanggal_po)),
            $po->total_items,
            'Rp ' . number_format($po->total_value, 0, ',', '.'),
            $po->status,
            date('d/m/Y', strtotime($po->created_at))
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
