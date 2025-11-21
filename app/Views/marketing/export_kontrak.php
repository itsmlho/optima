<?php
/**
 * Export CSV - Kontrak PO/Rental
 * Marketing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Kontrak_PO_Rental_' . date('Y-m-d_H-i-s') . '.csv"');
header('Cache-Control: max-age=0');

try {
    // Get data from database using direct MySQL connection
    $host = 'localhost';
    $dbname = 'optima_db';
    $username = 'root';
    $password = 'root';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->prepare("
        SELECT 
            k.*, 
            c.customer_name, 
            cl.location_name
        FROM kontrak k
        LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
        LEFT JOIN customers c ON c.id = cl.customer_id
        ORDER BY k.dibuat_pada DESC
    ");
    $stmt->execute();
    $kontraks = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Set headers
    $headers = ['No', 'Nomor Kontrak', 'No PO Marketing', 'Customer', 'Lokasi', 'Jenis Sewa', 'Tanggal Mulai', 'Tanggal Berakhir', 'Total Units', 'Nilai Total', 'Status'];
    fputcsv($output, $headers);
    
    // Fill data
    $no = 1;
    foreach ($kontraks as $kontrak) {
        $row = [
            $no++,
            $kontrak->no_kontrak,
            $kontrak->no_po_marketing,
            $kontrak->customer_name,
            $kontrak->location_name,
            $kontrak->jenis_sewa,
            date('d/m/Y', strtotime($kontrak->tanggal_mulai)),
            date('d/m/Y', strtotime($kontrak->tanggal_berakhir)),
            $kontrak->total_units,
            'Rp ' . number_format($kontrak->nilai_total, 0, ',', '.'),
            $kontrak->status
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}