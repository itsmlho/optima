<?php
/**
 * Export CSV - Area Management
 * Service Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Area_Management_' . date('Y-m-d_H-i-s') . '.csv"');
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
    $headers = ['No', 'Kode Area', 'Nama Area', 'Departemen', 'Deskripsi', 'Status', 'Created Date'];
    fputcsv($output, $headers);
    
    $stmt = $pdo->prepare("
        SELECT 
            a.*, 
            d.nama_departemen
        FROM areas a
        LEFT JOIN departemen d ON d.id_departemen = a.departemen_id
        ORDER BY a.area_name ASC
    ");
    $stmt->execute();
    $areas = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $no = 1;
    foreach ($areas as $area) {
        $row = [
            $no++,
            $area->area_code,
            $area->area_name,
            $area->nama_departemen,
            $area->area_description,
            $area->is_active ? 'Active' : 'Inactive',
            date('d/m/Y', strtotime($area->created_at))
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
