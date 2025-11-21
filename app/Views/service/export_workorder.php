<?php
/**
 * Export CSV - Work Orders
 * Service Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Work_Orders_' . date('Y-m-d_H-i-s') . '.csv"');
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
            wo.*,
            iu.no_unit,
            iu.serial_number,
            iu.tahun_unit,
            iu.lokasi_unit,
            ws.status_name,
            wp.priority_name,
            wc.category_name,
            wsc.subcategory_name,
            a.staff_name as admin_name,
            f.staff_name as foreman_name,
            m.staff_name as mechanic_name,
            d.nama_departemen
        FROM work_orders wo
        LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = wo.unit_id
        LEFT JOIN work_order_statuses ws ON ws.id = wo.status_id
        LEFT JOIN work_order_priorities wp ON wp.id = wo.priority_id
        LEFT JOIN work_order_categories wc ON wc.id = wo.category_id
        LEFT JOIN work_order_subcategories wsc ON wsc.id = wo.subcategory_id
        LEFT JOIN employees a ON a.id = wo.admin_id
        LEFT JOIN employees f ON f.id = wo.foreman_id
        LEFT JOIN employees m ON m.id = wo.mechanic_id
        LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
        ORDER BY wo.report_date DESC
    ");
    $stmt->execute();
    $workorders = $stmt->fetchAll(PDO::FETCH_OBJ);

    // Create CSV output
    $output = fopen('php://output', 'w');
    
    // Add BOM for UTF-8
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
    
    // Set headers - Lengkap seperti detail
    $headers = [
        'No', 'No WO', 'Tanggal Laporan', 'Jenis WO', 'Prioritas', 'Kategori', 'Sub Kategori',
        'No Unit', 'Serial Number', 'Tahun Unit', 'Lokasi Unit', 'Departemen',
        'Admin', 'Foreman', 'Mechanic', 'Status', 'Deskripsi Keluhan',
        'Waktu Perbaikan Diminta', 'Waktu Perbaikan Selesai', 'Catatan'
    ];
    fputcsv($output, $headers);
    
    // Fill data
    $no = 1;
    foreach ($workorders as $wo) {
        $row = [
            $no++,
            $wo->work_order_number,
            date('d/m/Y H:i', strtotime($wo->report_date)),
            $wo->order_type,
            $wo->priority_name,
            $wo->category_name,
            $wo->subcategory_name,
            $wo->no_unit,
            $wo->serial_number,
            $wo->tahun_unit,
            $wo->lokasi_unit,
            $wo->nama_departemen,
            $wo->admin_name,
            $wo->foreman_name,
            $wo->mechanic_name,
            $wo->status_name,
            $wo->complaint_description,
            $wo->requested_repair_time ? date('d/m/Y H:i', strtotime($wo->requested_repair_time)) : '-',
            $wo->completed_at ? date('d/m/Y H:i', strtotime($wo->completed_at)) : '-',
            $wo->notes ?? '-'
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}