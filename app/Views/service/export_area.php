<?php
/**
 * Export Excel - Area Management (Detailed)
 * Service Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Area_Management_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder for maximum control
    $db = \Config\Database::connect();
    
    // Main query with basic joins
    $query = $db->query("
        SELECT 
            a.*, 
            (
                SELECT COUNT(*) 
                FROM area_employee_assignments aea 
                WHERE aea.area_id = a.id 
                AND (aea.end_date IS NULL OR aea.end_date > CURDATE())
            ) as employee_count
        FROM areas a
        ORDER BY a.area_name ASC
    ");
    $areas = $query->getResult();
    
    // Output HTML Table
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Area Management</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
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
    
    // Report Information
    echo '<div class="header-info">';
    echo 'DETAILED AREA MANAGEMENT REPORT<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Areas: ' . count($areas) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="100">Kode Area</th>';
    echo '<th width="200">Nama Area</th>';
    echo '<th width="250">Deskripsi</th>';
    echo '<th width="100">Status</th>';
    echo '<th width="100">Jumlah Karyawan Aktif</th>';
    echo '<th width="120">Tanggal Dibuat</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($areas as $area) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        $status = $area->is_active ? 'Active' : 'Inactive';
        
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td>" . htmlspecialchars($area->area_code ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($area->area_name ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($area->area_description ?? '-') . "</td>";
        echo "<td>{$status}</td>";
        echo "<td align='center'>{$area->employee_count}</td>";
        echo "<td>" . date('d/m/Y', strtotime($area->created_at)) . "</td>";
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
