<?php
/**
 * Export Excel - Detailed Supplier Management
 * Purchasing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Supplier_Management_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    

    // Query with calculated total orders
    $query = $db->query("
        SELECT 
            s.*,
            (SELECT COUNT(*) FROM purchase_orders po WHERE po.supplier_id = s.id_supplier) as manual_total_orders
        FROM suppliers s
        ORDER BY s.created_at DESC
    ");
    $suppliers = $query->getResult();

    // Output HTML Table
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Suppliers</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
            .header-info { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
            .bg-blue { background-color: #DAE7F5; }
            .status-active { color: green; font-weight: bold; }
          </style>';
    echo '</head>';
    echo '<body>';
    
    echo '<div class="header-info">';
    echo 'DETAILED SUPPLIER REPORT<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Suppliers: ' . count($suppliers) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="100">Kode Supplier</th>';
    echo '<th width="250">Nama Supplier</th>';
    echo '<th width="150">Tipe Bisnis</th>';
    echo '<th width="150">Contact Person</th>';
    echo '<th width="150">Telepon</th>';
    echo '<th width="200">Email</th>';
    echo '<th width="200">Website</th>';
    echo '<th width="300">Alamat</th>';
    echo '<th width="80">Rating</th>';
    echo '<th width="100">Total PO</th>';
    echo '<th width="100">Status</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($suppliers as $supplier) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        $realCount = $supplier->manual_total_orders;
        // Fallback to existing column if needed
        
        $statusStyle = (strtolower($supplier->status ?? '') == 'active') ? 'class="status-active"' : '';
        
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td>" . htmlspecialchars($supplier->kode_supplier ?? '') . "</td>";
        echo "<td><b>" . htmlspecialchars($supplier->nama_supplier ?? '') . "</b></td>";
        echo "<td>" . htmlspecialchars($supplier->business_type ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($supplier->contact_person ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($supplier->phone ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($supplier->email ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($supplier->website ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($supplier->address ?? '-') . "</td>";
        echo "<td align='center'>" . htmlspecialchars($supplier->rating ?? '-') . "</td>";
        echo "<td align='center'>{$realCount}</td>";
        echo "<td $statusStyle>" . htmlspecialchars($supplier->status ?? '-') . "</td>";
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