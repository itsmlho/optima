<?php
/**
 * Export Excel - Kontrak PO/Rental (Detailed)
 * Marketing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Kontrak_PO_Rental_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

try {
    // Get data from database using CodeIgniter connection
    $db = \Config\Database::connect();
    
    // Detailed query: Contract -> Units
    $query = $db->query("
        SELECT 
            k.*, 
            c.customer_name, 
            cl.location_name,
            cl.city,
            iu.no_unit,
            iu.serial_number,
            iu.tahun_unit,
            mu.model_unit,
            mu.merk_unit,
            su.status_unit
        FROM kontrak k
        LEFT JOIN customer_locations cl ON cl.id = k.customer_location_id
        LEFT JOIN customers c ON c.id = cl.customer_id
        LEFT JOIN inventory_unit iu ON iu.kontrak_id = k.id
        LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
        LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
        ORDER BY k.dibuat_pada DESC, iu.no_unit ASC
    ");
    $results = $query->getResult();

    // Create Excel output (HTML Table method)
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
    echo '<!--[if gte mso 9]><xml>';
    echo '<x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Contracts</x:Name>';
    echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook>';
    echo '</xml><![endif]-->';
    echo '<style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #4472C4; color: #ffffff; border: 1px solid #000000; padding: 5px; text-align: center; vertical-align: middle; }
        td { border: 1px solid #000000; padding: 3px; vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-yellow { background-color: #fff2cc; }
    </style>';
    echo '</head>';
    echo '<body>';
    
    echo '<h3>CONTRACT MANAGEMENT REPORT (DETAILED)</h3>';
    echo '<p>Export Date: ' . date('d F Y H:i') . '</p>';

    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>Nomor Kontrak</th>';
    echo '<th>No PO Marketing</th>';
    echo '<th>Customer</th>';
    echo '<th>Lokasi</th>';
    echo '<th>Kota</th>';
    echo '<th>Jenis Sewa</th>';
    echo '<th>Periode Sewa</th>';
    echo '<th>Nilai Kontrak</th>';
    echo '<th>Status Kontrak</th>';
    
    // Unit Details
    echo '<th>No Unit (CN)</th>';
    echo '<th>Brand</th>';
    echo '<th>Model</th>';
    echo '<th>Serial Number</th>';
    echo '<th>Tahun</th>';
    echo '<th>Status Unit</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($results as $row) {
        $periode = date('d/m/y', strtotime($row->tanggal_mulai)) . ' - ' . date('d/m/y', strtotime($row->tanggal_berakhir));
        
        echo '<tr>';
        echo '<td class="text-center">' . $no++ . '</td>';
        echo '<td><b>' . htmlspecialchars($row->no_kontrak ?? '-') . '</b></td>';
        echo '<td>' . htmlspecialchars($row->customer_po_number ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->customer_name ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->location_name ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->city ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->jenis_sewa ?? '-') . '</td>';
        echo '<td class="text-center">' . $periode . '</td>';
        echo '<td class="text-right">' . number_format($row->nilai_total, 0) . '</td>';
        echo '<td>' . htmlspecialchars($row->status) . '</td>';
        
        // Unit Columns
        echo '<td class="bg-yellow">' . htmlspecialchars($row->no_unit ?? '-') . '</td>';
        echo '<td class="bg-yellow">' . htmlspecialchars($row->merk_unit ?? '-') . '</td>';
        echo '<td class="bg-yellow">' . htmlspecialchars($row->model_unit ?? '-') . '</td>';
        echo '<td class="bg-yellow">' . htmlspecialchars($row->serial_number ?? '-') . '</td>';
        echo '<td class="bg-yellow">' . htmlspecialchars($row->tahun_unit ?? '') . '</td>';
        echo '<td class="bg-yellow">' . htmlspecialchars($row->status_unit ?? '-') . '</td>';
        echo '</tr>';
    }
    
    echo '</tbody>';
    echo '</table>';
    echo '</body>';
    echo '</html>';
    exit;

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
