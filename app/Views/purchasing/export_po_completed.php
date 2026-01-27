<?php
/**
 * Export Excel - Detailed PO Completed
 * Purchasing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="PO_Completed_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    
    $query = $db->query("
        SELECT 
            po.*, 
            s.nama_supplier
        FROM purchase_orders po
        LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id
        WHERE po.status = 'Completed'
        ORDER BY po.created_at DESC
    ");
    $data = $query->getResult();

    // EXCEL OUTPUT
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PO Completed</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
            .header-info { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
            .bg-blue { background-color: #DAE7F5; }
            .status-done { color: green; font-weight: bold; }
          </style>';
    echo '</head>';
    echo '<body>';
    
    echo '<div class="header-info">';
    echo 'DETAILED PO COMPLETED REPORT<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Records: ' . count($data) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="120">No PO</th>';
    echo '<th width="250">Supplier</th>';
    echo '<th width="120">Tanggal PO</th>';
    echo '<th width="100">Total Items</th>';
    echo '<th width="150">Total Value</th>';
    echo '<th width="100">Status</th>';
    echo '<th width="120">Completed Date</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($data as $po) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td><b>" . htmlspecialchars($po->no_po ?? '') . "</b></td>";
        echo "<td>" . htmlspecialchars($po->nama_supplier ?? '') . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($po->tanggal_po)) . "</td>";
        echo "<td align='center'>" . ($po->total_items ?? 0) . "</td>";
        echo "<td align='right'>Rp " . number_format($po->total_value ?? 0, 0, ',', '.') . "</td>";
        echo "<td class='status-done'>" . htmlspecialchars($po->status ?? '-') . "</td>";
        echo "<td>" . date('d/m/Y', strtotime($po->updated_at)) . "</td>";
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
