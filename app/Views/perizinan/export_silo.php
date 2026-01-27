<?php
/**
 * Export Excel - Silo Data
 */

// Disable error reporting for clean output
error_reporting(0);
ini_set('display_errors', 0);

// Use CI4 Database Connection
$db = \Config\Database::connect();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Data_Silo_' . date('Y-m-d_H-i-s') . '.xls"');
header('Cache-Control: max-age=0');

// Query Data
$builder = $db->table('silo s');
$builder->select('
    s.*, 
    iu.no_unit, 
    u.username as operator_name, 
    u2.username as checker_name,
    c.customer_name, 
    cl.address as location_address
');
$builder->join('inventory_unit iu', 'iu.id_inventory_unit = s.unit_id', 'left');
$builder->join('users u', 'u.id = s.created_by', 'left'); // As Operator?
$builder->join('users u2', 'u2.id = s.updated_by', 'left'); // As Pengecek/Updater?
$builder->join('customers c', 'c.id = iu.customer_id', 'left'); 
$builder->join('customer_locations cl', 'cl.id = iu.customer_location_id', 'left');
$builder->orderBy('s.id_silo', 'DESC');

$query = $builder->get();
$silos = $query->getResult();

// EXCEL OUTPUT
echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
echo '<head>';
echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Data Silo</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
echo '<style>
        body { font-family: Arial, sans-serif; }
        table { border-collapse: collapse; width: 100%; }
        th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
        td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
        .bg-blue { background-color: #DAE7F5; }
      </style>';
echo '</head>';
echo '<body>';

echo '<h3>DATA SILO REPORT</h3>';
echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
echo 'Total Records: ' . count($silos) . '<br><br>';

echo '<table border="1">';
echo '<thead>';
echo '<tr>';
echo '<th width="50">No</th>';
echo '<th width="100">No Unit</th>';
echo '<th width="100">Tanggal</th>';
echo '<th width="100">Jam</th>';
echo '<th width="200">Lokasi</th>';
echo '<th width="150">Status</th>';
echo '<th width="250">Keterangan</th>';
echo '<th width="150">Operator</th>';
echo '<th width="150">Pengecek</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

$no = 1;
foreach ($silos as $item) {
    $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
    
    // Determine Date/Time source
    // Prefer tanggal_terbit_silo or created_at
    $dateSource = $item->created_at; 
    
    $tanggal = $dateSource ? date('Y-m-d', strtotime($dateSource)) : '-';
    $jam = $dateSource ? date('H:i', strtotime($dateSource)) : '-';
    
    // Lokasi logic: lokasi_disnaker > customer_address > customer_name
    $lokasi = $item->lokasi_disnaker;
    if (empty($lokasi)) $lokasi = $item->location_address;
    if (empty($lokasi)) $lokasi = $item->customer_name;
    
    // Keterangan Logic: Combine available notes
    $ket = [];
    if (!empty($item->catatan_pengajuan_pjk3)) $ket[] = "PJK3: " . $item->catatan_pengajuan_pjk3;
    if (!empty($item->catatan_pengajuan_uptd)) $ket[] = "UPTD: " . $item->catatan_pengajuan_uptd;
    if (!empty($item->catatan_proses_uptd)) $ket[] = "Proses: " . $item->catatan_proses_uptd;
    $keterangan = !empty($ket) ? implode("; ", $ket) : '-';

    echo "<tr $bgClass>";
    echo "<td align='center'>{$no}</td>";
    echo "<td>" . htmlspecialchars($item->no_unit ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($tanggal) . "</td>";
    echo "<td>" . htmlspecialchars($jam) . "</td>";
    echo "<td>" . htmlspecialchars($lokasi ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($item->status ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($keterangan) . "</td>";
    echo "<td>" . htmlspecialchars($item->operator_name ?? '-') . "</td>";
    echo "<td>" . htmlspecialchars($item->checker_name ?? '-') . "</td>";
    echo '</tr>';
    $no++;
}

echo '</tbody>';
echo '</table>';
echo '</body>';
echo '</html>';
