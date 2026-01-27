<?php
/**
 * Export CSV - Work Orders
 * Service Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Work_Orders_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    
    // Main Query
    $query = $db->query("
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
    $workorders = $query->getResult();

    // Output HTML Table
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Work Orders</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
            .header-info { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
            .bg-blue { background-color: #DAE7F5; }
            .priority-high { color: red; font-weight: bold; }
            .status-done { color: green; font-weight: bold; }
          </style>';
    echo '</head>';
    echo '<body>';
    
    // Check if we have data to display date range
    $dateRange = '';
    if (!empty($workorders)) {
        $lastDate = date('d M Y', strtotime($workorders[0]->report_date));
        $firstDate = date('d M Y', strtotime(end($workorders)->report_date));
        $dateRange = " ($firstDate - $lastDate)";
    }

    echo '<div class="header-info">';
    echo 'DETAILED WORK ORDER REPORT' . $dateRange . '<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Work Orders: ' . count($workorders) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="120">No WO</th>';
    echo '<th width="120">Tanggal Laporan</th>';
    echo '<th width="100">Status</th>';
    echo '<th width="100">Prioritas</th>';
    echo '<th width="120">No Unit</th>';
    echo '<th width="150">Serial Number</th>';
    echo '<th width="150">Lokasi Unit</th>';
    echo '<th width="120">Kategori</th>'; // Simplified column name
    echo '<th width="300">Deskripsi Keluhan</th>';
    echo '<th width="150">Admin</th>';
    echo '<th width="150">Foreman</th>';
    echo '<th width="150">Mechanic</th>';
    echo '<th width="150">Waktu Selesai</th>';
    echo '<th width="200">Catatan</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($workorders as $wo) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td><b>" . htmlspecialchars($wo->work_order_number ?? '') . "</b></td>";
        echo "<td>" . date('d/m/Y H:i', strtotime($wo->report_date)) . "</td>";
        
        // Status & Priority Styling
        $statusStyle = (strpos(strtolower($wo->status_name ?? ''), 'complete') !== false) ? 'class="status-done"' : '';
        $priorityStyle = (strpos(strtolower($wo->priority_name ?? ''), 'high') !== false || strpos(strtolower($wo->priority_name ?? ''), 'urgent') !== false) ? 'class="priority-high"' : '';
        
        echo "<td $statusStyle>" . htmlspecialchars($wo->status_name ?? '-') . "</td>";
        echo "<td $priorityStyle>" . htmlspecialchars($wo->priority_name ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($wo->no_unit ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($wo->serial_number ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($wo->lokasi_unit ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($wo->category_name ?? '-') . " (" . htmlspecialchars($wo->subcategory_name ?? '-') . ")</td>"; // Merged Category
        echo "<td>" . nl2br(htmlspecialchars($wo->complaint_description ?? '')) . "</td>";
        echo "<td>" . htmlspecialchars($wo->admin_name ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($wo->foreman_name ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($wo->mechanic_name ?? '-') . "</td>";
        
        $completedTime = $wo->completed_at ? date('d/m/Y H:i', strtotime($wo->completed_at)) : '-';
        echo "<td>{$completedTime}</td>";
        
        echo "<td>" . nl2br(htmlspecialchars($wo->notes ?? '-')) . "</td>";
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