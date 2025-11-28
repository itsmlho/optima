<?php
/**
 * Export CSV - Supplier Management
 * Purchasing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Supplier_Management_' . date('Y-m-d_H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

try {
    // Get data from database using direct MySQL connection
    $host = 'localhost';
    $dbname = 'optima_ci';
    $username = 'root';
    $password = 'root';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("
        SELECT * FROM suppliers 
        ORDER BY created_at DESC
    ");
    $stmt->execute();
    $suppliers = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Set headers
    $headers = ['No', 'Kode Supplier', 'Nama Supplier', 'Business Type', 'Contact Person', 'Telepon', 'Email', 'Website', 'Alamat', 'Rating', 'Total Orders', 'Status'];
    fputcsv($output, $headers);
    
    // Fill data
    $no = 1;
    foreach ($suppliers as $supplier) {
        $row = [
            $no++,
            $supplier->kode_supplier,
            $supplier->nama_supplier,
            $supplier->business_type,
            $supplier->contact_person,
            $supplier->phone,
            $supplier->email,
            $supplier->website,
            $supplier->address,
            $supplier->rating ?? '-',
            $supplier->total_orders ?? 0,
            $supplier->status
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}