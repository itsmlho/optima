<?php
/**
 * Export Excel - Customer Management
 * Marketing Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Customer_Management_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

try {
    // Get data from database using CodeIgniter connection
    $db = \Config\Database::connect();
    
    // Detailed Query joining Customers -> Locations -> Contracts -> Units
    $sql = "
        SELECT 
            c.customer_code,
            c.customer_name,
            c.is_active as customer_status,
            c.created_at as customer_created,
            
            a.area_name,
            
            cl.location_name,
            cl.city,
            cl.address,
            cl.contact_person as pic_name,
            cl.phone as pic_phone,
            
            k.no_kontrak,
            k.no_po_marketing,
            k.jenis_sewa,
            k.tanggal_mulai,
            k.tanggal_berakhir,
            k.nilai_total,
            k.status as kontrak_status,
            
            iu.no_unit,
            iu.serial_number,
            iu.tahun_unit,
            mu.model_unit,
            mu.merk_unit,
            su.status_unit
        FROM customers c
        LEFT JOIN customer_locations cl ON cl.customer_id = c.id
        LEFT JOIN areas a ON a.id = cl.area_id
        LEFT JOIN kontrak k ON k.customer_location_id = cl.id
        LEFT JOIN inventory_unit iu ON iu.kontrak_id = k.id
        LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
        LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
        ORDER BY c.customer_name ASC, cl.location_name ASC, k.tanggal_mulai DESC, iu.no_unit ASC
    ";
    
    $query = $db->query($sql);
    $results = $query->getResult();

    // Create Excel output (HTML Table method)
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<meta http-equiv="content-type" content="application/vnd.ms-excel; charset=UTF-8">';
    echo '<!--[if gte mso 9]><xml>';
    echo '<x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Detailed Data</x:Name>';
    echo '<x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook>';
    echo '</xml><![endif]-->';
    echo '<style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #4472C4; color: #ffffff; border: 1px solid #000000; padding: 5px; text-align: center; vertical-align: middle; }
        td { border: 1px solid #000000; padding: 3px; vertical-align: top; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .bg-grey { background-color: #f2f2f2; }
    </style>';
    echo '</head>';
    echo '<body>';
    
    echo '<h3>CUSTOMER MASTER DATA REPORT (DETAILED)</h3>';
    echo '<p>Export Date: ' . date('d F Y H:i') . '</p>';

    echo '<table>';
    echo '<thead>';
    echo '<tr>';
    echo '<th>No</th>';
    echo '<th>Kode Customer</th>';
    echo '<th>Nama Customer</th>';
    echo '<th>Area</th>';
    echo '<th>Lokasi Cabang</th>';
    echo '<th>Kota</th>';
    echo '<th>Alamat</th>';
    echo '<th>PIC</th>';
    echo '<th>No Kontrak</th>';
    echo '<th>No PO</th>';
    echo '<th>Jenis Sewa</th>';
    echo '<th>Status Kontrak</th>';
    echo '<th>Nilai Kontrak</th>';
    echo '<th>Periode Sewa</th>';
    echo '<th>No Unit</th>';
    echo '<th>Model</th>';
    echo '<th>Serial Number</th>';
    echo '<th>Status Unit</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($results as $row) {
        $periode = '-';
        if ($row->tanggal_mulai && $row->tanggal_berakhir) {
            $periode = date('d/m/y', strtotime($row->tanggal_mulai)) . ' - ' . date('d/m/y', strtotime($row->tanggal_berakhir));
        }

        echo '<tr>';
        echo '<td class="text-center">' . $no++ . '</td>';
        echo '<td>' . htmlspecialchars($row->customer_code ?? '') . '</td>';
        echo '<td><b>' . htmlspecialchars($row->customer_name ?? '') . '</b></td>';
        echo '<td>' . htmlspecialchars($row->area_name ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->location_name ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->city ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->address ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->pic_name ?? '-') . ' ' . htmlspecialchars($row->pic_phone ?? '') . '</td>';
        
        // Contract Info
        echo '<td>' . htmlspecialchars($row->no_kontrak ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->no_po_marketing ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->jenis_sewa ?? '-') . '</td>';
        echo '<td>' . htmlspecialchars($row->kontrak_status ?? '-') . '</td>';
        echo '<td class="text-right">' . ($row->nilai_total ? number_format($row->nilai_total, 0) : '-') . '</td>';
        echo '<td class="text-center">' . $periode . '</td>';
        
        // Unit Info
        echo '<td class="bg-grey">' . htmlspecialchars($row->no_unit ?? '-') . '</td>';
        echo '<td class="bg-grey">' . htmlspecialchars($row->merk_unit ?? '') . ' ' . htmlspecialchars($row->model_unit ?? '') . '</td>';
        echo '<td class="bg-grey">' . htmlspecialchars($row->serial_number ?? '-') . '</td>';
        echo '<td class="bg-grey">' . htmlspecialchars($row->status_unit ?? '-') . '</td>';
        
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
