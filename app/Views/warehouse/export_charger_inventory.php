<?php
/**
 * Export Excel - Charger Inventory
 * Warehouse Division - Detailed Report
 */

// Disable error reporting for clean output
error_reporting(0);
ini_set('display_errors', 0);

// Use CI4 Database Connection
$db = \Config\Database::connect();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Charger_Inventory_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Query Data
$builder = $db->table('inventory_attachment ia');
$builder->select('ia.*, iu.no_unit, c.merk_charger, c.tipe_charger');
$builder->join('inventory_unit iu', 'iu.id_inventory_unit = ia.id_inventory_unit', 'left');
$builder->join('charger c', 'c.id_charger = ia.charger_id', 'left');
$builder->where('ia.tipe_item', 'charger');
$builder->orderBy('ia.id_inventory_attachment', 'DESC');
$query = $builder->get();
$chargers = $query->getResult();

// EXCEL OUTPUT
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head>';
echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Charger Inventory</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
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
echo 'DETAILED CHARGER INVENTORY REPORT<br>';
echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
echo 'Total Records: ' . count($chargers) . '<br>';
echo '</div>';

echo '<table border="1">';
echo '<thead>';
echo '<tr>';
echo '<th width="50">No</th>';
echo '<th width="100">PO ID</th>';
echo '<th width="120">No Unit</th>';
echo '<th width="250">Charger Detail (Merk - Tipe)</th>';
echo '<th width="150">SN Charger</th>';
echo '<th width="100">Status</th>';
echo '<th width="150">Created Date</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$no = 1;
foreach ($chargers as $item) {
    $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
    
    // Format Charger Detail
    $chargerDetail = trim(($item->merk_charger ?? '') . ' ' . ($item->tipe_charger ?? ''));
    if (empty($chargerDetail)) {
        $chargerDetail = '-';
    }

    echo "<tr $bgClass>";
    echo "<td align='center'>{$no}</td>";
    echo "<td>" . htmlspecialchars($item->po_id ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($item->no_unit ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($chargerDetail) . "</td>";
    echo "<td>" . htmlspecialchars($item->serial_number ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($item->status ?? '-') . "</td>";
    echo "<td>" . ($item->created_at ? date('d/m/Y H:i', strtotime($item->created_at)) : '-') . "</td>";
    echo '</tr>';
    $no++;
}

echo '</tbody>';
echo '</table>';
echo '</body>';
echo '</html>';