<?php
/**
 * Export CSV - Employee Management
 * Service Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Employee_Management_' . date('Y-m-d_H-i-s') . '.csv"');
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
    $headers = ['No', 'Nama Karyawan', 'Area', 'Departemen', 'Role', 'Status', 'Start Date', 'End Date'];
    fputcsv($output, $headers);
    
    $stmt = $pdo->prepare("
        SELECT 
            aea.*, 
            a.area_name, 
            d.nama_departemen,
            e.staff_name,
            e.staff_role
        FROM area_employee_assignments aea
        LEFT JOIN areas a ON a.id = aea.area_id
        LEFT JOIN departemen d ON d.id_departemen = a.departemen_id
        LEFT JOIN employees e ON e.id = aea.employee_id
        ORDER BY e.staff_name ASC
    ");
    $stmt->execute();
    $employees = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $no = 1;
    foreach ($employees as $emp) {
        $row = [
            $no++,
            $emp->staff_name,
            $emp->area_name,
            $emp->nama_departemen,
            $emp->staff_role,
            $emp->is_active ? 'Active' : 'Inactive',
            date('d/m/Y', strtotime($emp->start_date)),
            $emp->end_date ? date('d/m/Y', strtotime($emp->end_date)) : '-'
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}