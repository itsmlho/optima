<?php
/**
 * Export Excel - Detailed PO Unit & Attachment
 * Purchasing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="PO_Report_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    

    // FETCH DATA
    
    // 1. Pending
    $query1 = $db->query("SELECT po.*, s.nama_supplier FROM purchase_orders po LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id WHERE po.status = 'Pending' ORDER BY po.created_at DESC");
    $poProgres = $query1->getResult();
    
    // 2. Delivery
    $query2 = $db->query("SELECT po.*, s.nama_supplier FROM purchase_orders po LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id WHERE po.status = 'Partial Received' ORDER BY po.created_at DESC");
    $poDelivery = $query2->getResult();
    
    // 3. Completed
    $query3 = $db->query("SELECT po.*, s.nama_supplier FROM purchase_orders po LEFT JOIN suppliers s ON s.id_supplier = po.supplier_id WHERE po.status = 'Completed' ORDER BY po.created_at DESC");
    $poCompleted = $query3->getResult();

    // START EXCEL OUTPUT
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>PO Report</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
            th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
            .section-title { font-weight: bold; font-size: 14px; margin: 15px 0 5px 0; color: #4472C4; }
            .bg-blue { background-color: #DAE7F5; }
          </style>';
    echo '</head>';
    echo '<body>';
    
    echo '<div style="font-weight:bold; font-size:16px;">DETAILED PO REPORT</div>';
    echo '<div>Date: ' . date('d F Y H:i') . '</div><br>';

    // --- SECTION 1 ---
    echo '<div class="section-title">SECTION 1: PROGRESS / PENDING (' . count($poProgres) . ')</div>';
    echo '<table border="1"><thead><tr>
        <th width="40">No</th>
        <th width="120">No PO</th>
        <th width="250">Supplier</th>
        <th width="100">Date</th>
        <th width="80">Items</th>
        <th width="150">Value</th>
        <th width="100">Status</th>
    </tr></thead><tbody>';
    $no = 1;
    foreach ($poProgres as $po) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        echo "<tr $bgClass><td align='center'>$no</td>
        <td>" . htmlspecialchars($po->no_po) . "</td>
        <td>" . htmlspecialchars($po->nama_supplier) . "</td>
        <td>" . date('d/m/Y', strtotime($po->tanggal_po)) . "</td>
        <td align='center'>{$po->total_items}</td>
        <td align='right'>Rp " . number_format($po->total_value, 0, ',', '.') . "</td>
        <td>{$po->status}</td></tr>";
        $no++;
    }
    echo '</tbody></table>';

    // --- SECTION 2 ---
    echo '<div class="section-title">SECTION 2: DELIVERY / PARTIAL (' . count($poDelivery) . ')</div>';
    echo '<table border="1"><thead><tr>
        <th width="40">No</th>
        <th width="120">No PO</th>
        <th width="250">Supplier</th>
        <th width="100">Date</th>
        <th width="80">Items</th>
        <th width="150">Value</th>
        <th width="100">Status</th>
    </tr></thead><tbody>';
    $no = 1;
    foreach ($poDelivery as $po) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        echo "<tr $bgClass><td align='center'>$no</td>
        <td>" . htmlspecialchars($po->no_po) . "</td>
        <td>" . htmlspecialchars($po->nama_supplier) . "</td>
        <td>" . date('d/m/Y', strtotime($po->tanggal_po)) . "</td>
        <td align='center'>{$po->total_items}</td>
        <td align='right'>Rp " . number_format($po->total_value, 0, ',', '.') . "</td>
        <td>{$po->status}</td></tr>";
        $no++;
    }
    echo '</tbody></table>';

    // --- SECTION 3 ---
    echo '<div class="section-title">SECTION 3: COMPLETED (' . count($poCompleted) . ')</div>';
    echo '<table border="1"><thead><tr>
        <th width="40">No</th>
        <th width="120">No PO</th>
        <th width="250">Supplier</th>
        <th width="100">Date</th>
        <th width="80">Items</th>
        <th width="150">Value</th>
        <th width="100">Status</th>
    </tr></thead><tbody>';
    $no = 1;
    foreach ($poCompleted as $po) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        echo "<tr $bgClass><td align='center'>$no</td>
        <td>" . htmlspecialchars($po->no_po) . "</td>
        <td>" . htmlspecialchars($po->nama_supplier) . "</td>
        <td>" . date('d/m/Y', strtotime($po->tanggal_po)) . "</td>
        <td align='center'>{$po->total_items}</td>
        <td align='right'>Rp " . number_format($po->total_value, 0, ',', '.') . "</td>
        <td>{$po->status}</td></tr>";
        $no++;
    }
    echo '</tbody></table>';
    
    echo '</body></html>';
    
} catch (\Exception $e) {
    echo '<div style="color:red;">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}
exit;