<?php
/**
 * Export Excel - Detailed PO Deliveries
 * Purchasing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ini_set('log_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="PO_Delivery_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    
    $query = $db->query("
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
    $deliveries = $query->getResult();

    // EXCEL OUTPUT
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PO Deliveries</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
            .header-info { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
            .bg-blue { background-color: #DAE7F5; }
          </style>';
    echo '</head>';
    echo '<body>';
    
    echo '<div class="header-info">';
    echo 'DETAILED PO DELIVERY REPORT<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Delivery Items: ' . count($deliveries) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="120">No PO</th>';
    echo '<th width="200">Supplier</th>';
    echo '<th width="150">Packing List</th>';
    echo '<th width="80">Del. Seq</th>';
    echo '<th width="120">Del. Date</th>';
    echo '<th width="150">Driver</th>';
    echo '<th width="120">Phone</th>';
    echo '<th width="150">Vehicle Info</th>';
    echo '<th width="120">Plate No</th>';
    echo '<th width="120">Item Type</th>';
    echo '<th width="200">Item Name</th>';
    echo '<th width="250">Description</th>';
    echo '<th width="80">Qty</th>';
    echo '<th width="150">Serial Numbers</th>';
    echo '<th width="100">PO Status</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($deliveries as $delivery) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td><b>" . htmlspecialchars($delivery->no_po ?? '') . "</b></td>";
        echo "<td>" . htmlspecialchars($delivery->nama_supplier ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->packing_list_no ?? '-') . "</td>";
        echo "<td align='center'>" . htmlspecialchars($delivery->delivery_sequence ?? '-') . "</td>";
        echo "<td>" . ($delivery->delivery_date ? date('d/m/Y', strtotime($delivery->delivery_date)) : '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->driver_name ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->driver_phone ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->vehicle_info ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->vehicle_plate ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->item_type ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->item_name ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->item_description ?? '-') . "</td>";
        echo "<td align='center'>" . htmlspecialchars($delivery->qty ?? 0) . "</td>";
        echo "<td>" . htmlspecialchars($delivery->serial_number ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($delivery->status ?? '-') . "</td>";
        echo '</tr>';
        $no++;
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</body>';
    echo '</html>';

} catch (\Exception $e) {
    echo "Error generating report: " . $e->getMessage();
}
