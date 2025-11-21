<?php
/**
 * Export CSV - Attachment Inventory
 * Warehouse Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="Attachment_Inventory_' . date('Y-m-d_H-i-s') . '.csv"');
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
    $headers = ['No', 'Tipe Item', 'PO ID', 'No Unit', 'Attachment ID', 'SN Attachment', 'Baterai ID', 'SN Baterai', 'Charger ID', 'SN Charger', 'Status', 'Created Date'];
    fputcsv($output, $headers);
    
    $stmt = $pdo->prepare("
        SELECT 
            ia.*,
            iu.no_unit
        FROM inventory_attachment ia
        LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ia.id_inventory_unit
        ORDER BY ia.id_inventory_attachment DESC
    ");
    $stmt->execute();
    $attachments = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $no = 1;
    foreach ($attachments as $attachment) {
        $row = [
            $no++,
            $attachment->tipe_item,
            $attachment->po_id,
            $attachment->no_unit,
            $attachment->attachment_id,
            $attachment->sn_attachment,
            $attachment->baterai_id,
            $attachment->sn_baterai,
            $attachment->charger_id,
            $attachment->sn_charger,
            $attachment->status ?? 'Active',
            $attachment->created_at ? date('d/m/Y', strtotime($attachment->created_at)) : '-'
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}