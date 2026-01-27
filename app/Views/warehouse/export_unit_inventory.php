<?php
/**
 * Export Excel - Detailed Unit Inventory
 * Warehouse Division
 */

// Disable all output buffering and error reporting
error_reporting(0);
ini_set('display_errors', 0);
ob_clean();

// Set headers for Excel download
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="Unit_Inventory_Detailed_' . date('Y-m-d_H-i-s') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

try {
    // Get data directly using Query Builder
    $db = \Config\Database::connect();
    
    // 1. Get Main Unit Data
    $query = $db->query("
        SELECT 
            iu.*,
            d.nama_departemen,
            su.status_unit,
            mu.merk_unit, mu.model_unit,
            tu.tipe,
            k.kapasitas_unit,
            tm.tipe_mast,
            m.merk_mesin, m.model_mesin,
            ctr.no_kontrak, ctr.status AS status_kontrak,
            sl.status AS status_silo,
            jr.tipe_roda,
            tb.tipe_ban,
            v.jumlah_valve
        FROM inventory_unit iu
        LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
        LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
        LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
        LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
        LEFT JOIN kapasitas k ON k.id_kapasitas = iu.kapasitas_unit_id
        LEFT JOIN tipe_mast tm ON tm.id_mast = iu.model_mast_id
        LEFT JOIN mesin m ON m.id = iu.model_mesin_id
        LEFT JOIN kontrak ctr ON ctr.id = iu.kontrak_id
        LEFT JOIN silo sl ON sl.unit_id = iu.id_inventory_unit
        LEFT JOIN jenis_roda jr ON jr.id_roda = iu.roda_id
        LEFT JOIN tipe_ban tb ON tb.id_ban = iu.ban_id
        LEFT JOIN valve v ON v.id_valve = iu.valve_id
        ORDER BY iu.id_inventory_unit DESC
    ");
    $units = $query->getResult();

    // 2. Fetch All Linked Attachments/Components
    $unitIds = array_column($units, 'id_inventory_unit');
    $mappedAttachments = [];
    $mappedBatteries = [];
    $mappedChargers = [];
    
    if (!empty($unitIds)) {
        // Safe implode for IN clause
        $ids = implode(',', array_map('intval', $unitIds));
        
        $attQuery = $db->query("
            SELECT ia.id_inventory_unit, ia.tipe_item,
                a.merk as merk_att, a.model as model_att, a.tipe as tipe_att,
                b.merk_baterai, b.tipe_baterai, b.jenis_baterai,
                c.merk_charger, c.tipe_charger
            FROM inventory_attachment ia
            LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
            LEFT JOIN baterai b ON ia.baterai_id = b.id
            LEFT JOIN charger c ON ia.charger_id = c.id_charger
            WHERE ia.id_inventory_unit IN ($ids)
        ");
        
        foreach ($attQuery->getResult() as $att) {
            $uId = $att->id_inventory_unit;
            
            // Format based on tipe_item and place in specific array
            if ($att->tipe_item === 'attachment') {
                $parts = array_filter([$att->tipe_att, $att->merk_att, $att->model_att]);
                $detail = implode(' ', $parts);
                if ($detail) {
                    if (!isset($mappedAttachments[$uId])) $mappedAttachments[$uId] = [];
                    $mappedAttachments[$uId][] = $detail;
                }
            } elseif ($att->tipe_item === 'battery') {
                $parts = array_filter([$att->merk_baterai, $att->tipe_baterai, $att->jenis_baterai]);
                $detail = implode(' ', $parts);
                if ($detail) {
                    if (!isset($mappedBatteries[$uId])) $mappedBatteries[$uId] = [];
                    $mappedBatteries[$uId][] = $detail;
                }
            } elseif ($att->tipe_item === 'charger') {
                $parts = array_filter([$att->merk_charger, $att->tipe_charger]);
                $detail = implode(' ', $parts);
                if ($detail) {
                    if (!isset($mappedChargers[$uId])) $mappedChargers[$uId] = [];
                    $mappedChargers[$uId][] = $detail;
                }
            }
        }
    }
    
    // EXCEL OUTPUT
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head>';
    echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>Unit Inventory</x:Name><x:WorksheetOptions><x:Print><x:ValidPrinterInfo/></x:Print></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
    echo '<style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th { background-color: #4472C4; color: white; border: 1px solid #000000; padding: 10px; text-align: left; vertical-align: middle; }
            td { border: 1px solid #000000; padding: 5px; vertical-align: top; }
            .header-info { margin-bottom: 20px; font-weight: bold; font-size: 14px; }
            .bg-blue { background-color: #DAE7F5; }
            .multi-line { white-space: pre-wrap; }
          </style>';
    echo '</head>';
    echo '<body>';
    
    echo '<div class="header-info">';
    echo 'DETAILED UNIT INVENTORY REPORT<br>';
    echo 'Generated Date: ' . date('d F Y H:i') . '<br>';
    echo 'Total Units: ' . count($units) . '<br>';
    echo '</div>';
    
    echo '<table border="1">';
    echo '<thead>';
    echo '<tr>';
    echo '<th width="50">No</th>';
    echo '<th width="120">No Unit</th>';
    echo '<th width="150">Serial Number</th>';
    echo '<th width="200">Merk / Model</th>';
    echo '<th width="120">Tipe</th>';
    echo '<th width="100">Kapasitas</th>';
    echo '<th width="80">Tahun</th>';
    // Specifications Columns
    echo '<th width="150">Mast</th>';
    echo '<th width="150">Engine</th>';
    echo '<th width="120">Roda</th>';
    echo '<th width="120">Ban</th>';
    echo '<th width="80">Valve</th>';
    // Components Columns
    echo '<th width="200">Battery</th>';
    echo '<th width="200">Charger</th>';
    echo '<th width="200">Attachments</th>';
    
    echo '<th width="150">Lokasi</th>';
    echo '<th width="150">Departemen</th>';
    echo '<th width="120">Status Unit</th>';
    echo '<th width="120">Status Silo</th>';
    echo '<th width="200">Info Kontrak</th>';
    echo '<th width="120">Tanggal Kirim</th>';
    echo '<th width="200">Keterangan</th>';
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    
    $no = 1;
    foreach ($units as $u) {
        $bgClass = ($no % 2 == 0) ? 'class="bg-blue"' : '';
        
        // Format Basic Info
        $merkModel = trim(($u->merk_unit ?? '') . ' ' . ($u->model_unit ?? ''));
        $kontrak = ($u->no_kontrak) ? $u->no_kontrak . ' (' . ($u->status_kontrak ?? '-') . ')' : '-';
        
        // Spec Data
        $mast = trim(($u->tipe_mast ?? '-') . ($u->tinggi_mast ? " (" . $u->tinggi_mast . "m)" : ""));
        $engine = trim(($u->merk_mesin ?? '') . ' ' . ($u->model_mesin ?? ''));
        
        // Components Data (Consolidated per type just in case there are multiple)
        $batStr = isset($mappedBatteries[$u->id_inventory_unit]) ? implode('<br>', $mappedBatteries[$u->id_inventory_unit]) : '-';
        $chgStr = isset($mappedChargers[$u->id_inventory_unit]) ? implode('<br>', $mappedChargers[$u->id_inventory_unit]) : '-';
        $attStr = isset($mappedAttachments[$u->id_inventory_unit]) ? implode('<br>', $mappedAttachments[$u->id_inventory_unit]) : '-';
        
        echo "<tr $bgClass>";
        echo "<td align='center'>{$no}</td>";
        echo "<td><b>" . htmlspecialchars($u->no_unit ?? '') . "</b></td>";
        echo "<td>" . htmlspecialchars($u->serial_number ?? '') . "</td>";
        echo "<td>" . htmlspecialchars($merkModel) . "</td>";
        echo "<td>" . htmlspecialchars($u->tipe ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->kapasitas_unit ?? '-') . "</td>";
        echo "<td align='center'>" . htmlspecialchars($u->tahun_unit ?? '-') . "</td>";
        
        // Spec Columns
        echo "<td>" . htmlspecialchars($mast ?: '-') . "</td>";
        echo "<td>" . htmlspecialchars($engine ?: '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->tipe_roda ?: '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->tipe_ban ?: '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->jumlah_valve ?: '-') . "</td>";
        
        // Component Columns
        echo "<td class='multi-line'>" . $batStr . "</td>";
        echo "<td class='multi-line'>" . $chgStr . "</td>";
        echo "<td class='multi-line'>" . $attStr . "</td>";
        
        echo "<td>" . htmlspecialchars($u->lokasi_unit ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->nama_departemen ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->status_unit ?? '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->status_silo ?? 'BELUM_ADA') . "</td>";
        echo "<td>" . htmlspecialchars($kontrak) . "</td>";
        echo "<td>" . ($u->tanggal_kirim ? date('d/m/Y', strtotime($u->tanggal_kirim)) : '-') . "</td>";
        echo "<td>" . htmlspecialchars($u->keterangan ?? '-') . "</td>";
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