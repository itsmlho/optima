<?php
/**
 * Export Excel - Quotations
 * Marketing Division
 */

// Disable output buffering/reporting
error_reporting(0);
ini_set('display_errors', 0);

// Use CI4 Database Connection (if needed, but usually passed via controller)
// Data $quotations is passed from controller

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Quotations_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// EXCEL OUTPUT
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head>';
echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Quotations</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
echo '<style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
        td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
        .bg-blue { background-color: #DAE7F5; }
      </style>';
echo '</head>';
echo '<body>';

echo '<h3>QUOTATION REPORT</h3>';
echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
echo 'Total Records: ' . count($quotations) . '<br><br>';

echo '<table border="1">';
echo '<thead>';
echo '<tr>';
echo '<th width="50">No</th>';
echo '<th width="150">Quotation Number</th>';
echo '<th width="200">Prospect Name</th>';
echo '<th width="150">Contact Person</th>';
echo '<th width="250">Title</th>';
echo '<th width="100">Date</th>';
echo '<th width="100">Valid Until</th>';
echo '<th width="100">Stage</th>';
echo '<th width="150">Total Amount</th>';
echo '<th width="100">Assigned To</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$no = 1;

// Get users mapping for Assigned To if only ID is available
// Assuming $quotations might contain raw data or joined data.
// Based on typical CI4 models, if findAll is used, it returns array of results.
// If relations are not joined, assigned_to is an ID.
// I will check if I can join users in the controller or if I need to do it here.
// For now, let's assume raw data.

foreach ($quotations as $row) {
    if (is_object($row)) $row = (array) $row;
    
    $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
    
    $amount = ($row['currency'] ?? 'IDR') . ' ' . number_format($row['total_amount'] ?? 0, 2);
    
    echo "<tr $bgClass>";
    echo "<td align='center'>{$no}</td>";
    echo "<td>" . htmlspecialchars($row['quotation_number'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['prospect_name'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['prospect_contact_person'] ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($row['quotation_title'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['quotation_date'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['valid_until'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($row['stage'] ?? '') . "</td>";
    echo "<td>" . htmlspecialchars($amount) . "</td>";
    echo "<td>" . htmlspecialchars($row['assigned_to_name'] ?? $row['assigned_to'] ?? '-') . "</td>";
    echo '</tr>';
    $no++;
}

echo '</tbody>';
echo '</table>';
echo '</body>';
echo '</html>';
