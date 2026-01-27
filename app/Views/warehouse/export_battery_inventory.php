<?php
/**
 * Export Excel - Detailed Battery Inventory
 * Warehouse Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Battery_Inventory_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    

    $query = $db->query("
        SELECT 
            ia.*,
            iu.no_unit,
            b.merk_baterai, b.tipe_baterai, b.jenis_baterai
        FROM inventory_attachment ia
        LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ia.id_inventory_unit
        LEFT JOIN baterai b ON b.id = ia.baterai_id
        WHERE ia.tipe_item = 'battery'
        ORDER BY ia.id_inventory_attachment DESC
    ");
    $batteries = $query->getResult();
    
    // EXCEL OUTPUT
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Battery Inventory</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
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
    echo 'DETAILED BATTERY INVENTORY REPORT<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Records: ' . count($batteries) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="100">PO ID</th>';
    echo '<th width="120">No Unit</th>';
    echo '<th width="200">Battery Detail</th>';
    echo '<th width="150">SN Battery</th>';
    echo '<th width="100">Status</th>';
    echo '<th width="120">Created Date</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($batteries as $battery) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        $detail = trim(($battery->merk_baterai ?? '') . ' ' . ($battery->tipe_baterai ?? '') . ' ' . ($battery->jenis_baterai ?? ''));
        
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td>" . htmlspecialchars($battery->po_id ?? '') . "</td>";
        echo "<td><b>" . htmlspecialchars($battery->no_unit ?? '') . "</b></td>";
        echo "<td>" . htmlspecialchars($detail) . "</td>";
        echo "<td>" . htmlspecialchars($battery->sn_baterai ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($battery->status ?? 'Active') . "</td>";
        echo "<td>" . ($battery->created_at ? date('d/m/Y', strtotime($battery->created_at)) : '-') . "</td>";
        echo '</tr>';
        $no++;
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</body>';
    echo '</html>';

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}