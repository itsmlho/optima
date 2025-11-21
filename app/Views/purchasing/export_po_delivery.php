<?php
/**
 * Export CSV - PO Delivery
 * Purchasing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);
ob_clean();

// Set headers for CSV download
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="PO_Delivery_' . date('Y-m-d_H-i-s') . '.csv"');
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
    
    // Set headers - Data delivery yang lengkap
    $headers = [
        'No', 'No PO', 'Supplier', 'Packing List No', 'Delivery Sequence', 'Tanggal Delivery',
        'Driver Name', 'Driver Phone', 'Vehicle Info', 'Vehicle Plate', 'Item Type',
        'Item Name', 'Item Description', 'Qty', 'Serial Numbers', 'Status'
    ];
    fputcsv($output, $headers);
    
    $stmt = $pdo->prepare("
        SELECT 
            po.no_po,
            s.nama_supplier,
            pd.packing_list_no,
            pd.delivery_sequence,
            pd.delivery_date,
            pd.driver_name,
            pd.driver_phone,
            pd.vehicle_info,
            pd.vehicle_plate,
            pdi.item_type,
            pdi.item_name,
            pdi.item_description,
            pdi.qty,
            pdi.serial_number,
            po.status
        FROM po_deliveries pd
        LEFT JOIN purchase_orders po ON po.id_po = pd.po_id
        LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id
        LEFT JOIN po_delivery_items pdi ON pdi.delivery_id = pd.id_delivery
        WHERE po.status IN ('Partial Received', 'Completed')
        ORDER BY pd.delivery_date DESC, pd.delivery_sequence ASC
    ");
    $stmt->execute();
    $deliveries = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    $no = 1;
    foreach ($deliveries as $delivery) {
        $row = [
            $no++,
            $delivery->no_po,
            $delivery->nama_supplier,
            $delivery->packing_list_no,
            $delivery->delivery_sequence,
            $delivery->delivery_date ? date('d/m/Y', strtotime($delivery->delivery_date)) : '-',
            $delivery->driver_name,
            $delivery->driver_phone,
            $delivery->vehicle_info,
            $delivery->vehicle_plate,
            $delivery->item_type,
            $delivery->item_name,
            $delivery->item_description,
            $delivery->qty,
            $delivery->serial_number,
            $delivery->status
        ];
        fputcsv($output, $row);
    }
    
    fclose($output);
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
