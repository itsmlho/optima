<?php
/**
 * Export CSV - Employee Management
 * Service Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Employee_Assignments_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    
    // Query focusing on Employee Assignments
    $query = $db->query("
        SELECT 
            aea.*, 
            a.area_name, 
            a.area_code,
            d.nama_departemen,
            e.staff_name,
            e.staff_role,
            e.email,
            e.contact_number
        FROM area_employee_assignments aea
        LEFT JOIN areas a ON a.id = aea.area_id
        LEFT JOIN departemen d ON d.id_departemen = a.departemen_id
        LEFT JOIN employees e ON e.id = aea.employee_id
        ORDER BY e.staff_name ASC, aea.start_date DESC
    ");
    $assignments = $query->getResult();
    
    // Output HTML Table
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Employee Assignments</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
            .header-info { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
            .bg-blue { background-color: #DAE7F5; }
            .status-active { color: green; font-weight: bold; }
            .status-inactive { color: red; }
          </style>';
    echo '</head>';
    echo '<body>';
    
    // Report Information
    echo '<div class="header-info">';
    echo 'DETAILED EMPLOYEE ASSIGNMENT REPORT<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Records: ' . count($assignments) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="200">Nama Karyawan</th>';
    echo '<th width="150">Role</th>';
    echo '<th width="150">Kontak</th>';
    echo '<th width="100">Kode Area</th>';
    echo '<th width="200">Area Assignment</th>';
    echo '<th width="150">Departemen</th>';
    echo '<th width="100">Status</th>';
    echo '<th width="120">Start Date</th>';
    echo '<th width="120">End Date</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($assignments as $row) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        $isActive = $row->is_active && (is_null($row->end_date) || strtotime($row->end_date) > time());
        $statusClass = $isActive ? 'class="status-active"' : 'class="status-inactive"';
        $statusLabel = $isActive ? 'Active' : 'Inactive';
        
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td><b>" . htmlspecialchars($row->staff_name ?? '') . "</b></td>";
        echo "<td>" . htmlspecialchars($row->staff_role ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row->contact_number ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row->area_code ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row->area_name ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($row->nama_departemen ?? '-') . "</td>";
        echo "<td $statusClass>{$statusLabel}</td>";
        echo "<td>" . date('d/m/Y', strtotime($row->start_date)) . "</td>";
        echo "<td>" . ($row->end_date ? date('d/m/Y', strtotime($row->end_date)) : 'Present') . "</td>";
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