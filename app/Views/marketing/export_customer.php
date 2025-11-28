<?php
/**
 * Export CSV - Customer Management
 * Marketing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Customer_Management_' . date('Y-m-d_H-i-s') . '.csv"');
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
        SELECT 
            c.*, 
            a.area_name,
            COUNT(DISTINCT cl.id) as total_lokasi
        FROM customers c
        LEFT JOIN customer_locations cl ON cl.customer_id = c.id
        LEFT JOIN areas a ON a.id = cl.area_id
        GROUP BY c.id
        ORDER BY c.created_at DESC
    ");
    $stmt->execute();
    $customers = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Set headers
    $headers = ['No', 'Kode Customer', 'Nama Customer', 'Area', 'Total Lokasi', 'Status', 'Created Date'];
    fputcsv($output, $headers);
    
    // Fill data
    $no = 1;
    foreach ($customers as $customer) {
        $row = [
            $no++,
            $customer->customer_code,
            $customer->customer_name,
            $customer->area_name,
            $customer->total_lokasi,
            $customer->is_active ? 'Active' : 'Inactive',
            date('d/m/Y', strtotime($customer->created_at))
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}