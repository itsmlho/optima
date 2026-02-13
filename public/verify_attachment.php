<?php
/**
 * Verify Attachment Data for SPK
 * Check if attachment was saved to correct unit
 */

// Hardcode database config
$dbConfig = [
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'optima_ci',
];

// Connect via mysqli
$mysqli = new mysqli(
    $dbConfig['hostname'],
    $dbConfig['username'],
    $dbConfig['password'],
    $dbConfig['database']
);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

$mysqli->set_charset('utf8mb4');

echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
echo "<style>
    body { font-family: 'Segoe UI', Arial; margin: 20px; background: #f5f5f5; }
    .container { max-width: 1200px; margin: 0 auto; background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
    h2 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }
    h3 { color: #34495e; margin-top: 30px; border-left: 4px solid #3498db; padding-left: 10px; }
    table { width: 100%; border-collapse: collapse; margin: 20px 0; }
    th { background: #3498db; color: white; padding: 12px; text-align: left; font-weight: 600; }
    td { padding: 10px; border-bottom: 1px solid #ddd; }
    tr:hover { background: #f8f9fa; }
    .badge { display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; }
    .badge-success { background: #d4edda; color: #155724; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-info { background: #d1ecf1; color: #0c5460; }
    .alert { padding: 15px; margin: 20px 0; border-radius: 4px; }
    .alert-info { background: #d1ecf1; border-left: 4px solid #0c5460; color: #0c5460; }
</style></head><body>";

echo "<div class='container'>";
echo "<h2>🔍 Verification: Attachment Data for Latest SPK</h2>";

// Get latest SPK ATTACHMENT
$spkQuery = "
    SELECT 
        s.id,
        s.nomor_spk,
        s.jenis_spk,
        s.kontrak_id,
        s.spesifikasi,
        s.status,
        s.dibuat_pada,
        k.no_kontrak,
        c.customer_name
    FROM spk s
    LEFT JOIN kontrak k ON k.id = s.kontrak_id
    LEFT JOIN customer c ON c.id = k.customer_id
    WHERE s.jenis_spk = 'ATTACHMENT'
    ORDER BY s.id DESC
    LIMIT 5
";

$result = $mysqli->query($spkQuery);

if ($result && $result->num_rows > 0) {
    echo "<h3>📋 Latest 5 SPK ATTACHMENT</h3>";
    echo "<table>";
    echo "<tr>
            <th>SPK ID</th>
            <th>No. SPK</th>
            <th>Contract</th>
            <th>Customer</th>
            <th>Target Unit in Spec</th>
            <th>Status</th>
            <th>Created</th>
          </tr>";
    
    while ($spk = $result->fetch_assoc()) {
        $spec = json_decode($spk['spesifikasi'], true);
        $targetUnitId = isset($spec['target_unit_id']) ? $spec['target_unit_id'] : 'NOT SET';
        $targetUnitSn = isset($spec['target_unit_sn']) ? $spec['target_unit_sn'] : '-';
        
        echo "<tr>";
        echo "<td><strong>#{$spk['id']}</strong></td>";
        echo "<td>{$spk['nomor_spk']}</td>";
        echo "<td>{$spk['no_kontrak']}</td>";
        echo "<td>{$spk['customer_name']}</td>";
        echo "<td>";
        if ($targetUnitId === 'NOT SET') {
            echo "<span class='badge badge-warning'>⚠ NOT SET</span> (SPK Lama)";
        } else {
            echo "<span class='badge badge-success'>✓ Unit #{$targetUnitId}</span><br><small>SN: {$targetUnitSn}</small>";
        }
        echo "</td>";
        echo "<td><span class='badge badge-info'>{$spk['status']}</span></td>";
        echo "<td>" . date('Y-m-d H:i', strtotime($spk['dibuat_pada'])) . "</td>";
        echo "</tr>";
        
        // Check stage processing for this SPK
        $stageQuery = "
            SELECT 
                stage_name,
                unit_id,
                approved_at,
                approved_by
            FROM spk_unit_stages
            WHERE spk_id = {$spk['id']}
            ORDER BY approved_at DESC
        ";
        
        $stageResult = $mysqli->query($stageQuery);
        if ($stageResult && $stageResult->num_rows > 0) {
            echo "<tr><td colspan='7' style='padding-left: 40px; background: #f8f9fa;'>";
            echo "<strong>Stage Processing:</strong><br>";
            echo "<table style='width: auto; margin: 10px 0;'>";
            echo "<tr style='background: #e9ecef;'><th>Stage</th><th>Unit ID Used</th><th>Approved At</th></tr>";
            
            while ($stage = $stageResult->fetch_assoc()) {
                $unitMatch = ($stage['unit_id'] == $targetUnitId || $targetUnitId === 'NOT SET');
                $unitBadge = $unitMatch ? 
                    "<span class='badge badge-success'>✓ Match</span>" : 
                    "<span class='badge badge-warning'>⚠ Different</span>";
                
                echo "<tr>";
                echo "<td>{$stage['stage_name']}</td>";
                echo "<td><strong>#{$stage['unit_id']}</strong> {$unitBadge}</td>";
                echo "<td>" . date('Y-m-d H:i', strtotime($stage['approved_at'])) . "</td>";
                echo "</tr>";
            }
            echo "</table></td></tr>";
        }
        
        // Check attachment assignments for units in this contract
        $attachmentQuery = "
            SELECT 
                ia.id_inventory_attachment,
                ia.id_inventory_unit,
                ia.tipe_item,
                ia.attachment_status,
                ia.updated_at,
                iu.no_unit,
                iu.serial_number,
                att.tipe as attachment_tipe,
                att.merk as attachment_merk,
                att.model as attachment_model,
                ia.sn_attachment
            FROM inventory_attachment ia
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ia.id_inventory_unit
            LEFT JOIN attachment att ON att.id_attachment = ia.attachment_id
            WHERE iu.kontrak_id = {$spk['kontrak_id']}
            AND ia.tipe_item = 'attachment'
            ORDER BY ia.updated_at DESC
            LIMIT 5
        ";
        
        $attachmentResult = $mysqli->query($attachmentQuery);
        if ($attachmentResult && $attachmentResult->num_rows > 0) {
            echo "<tr><td colspan='7' style='padding-left: 40px; background: #f0f8ff;'>";
            echo "<strong>📎 Recent Attachment Assignments (Contract Units):</strong><br>";
            echo "<table style='width: auto; margin: 10px 0;'>";
            echo "<tr style='background: #d1ecf1;'><th>Unit</th><th>Attachment</th><th>Status</th><th>Updated</th></tr>";
            
            while ($attachment = $attachmentResult->fetch_assoc()) {
                echo "<tr>";
                echo "<td>Unit #{$attachment['id_inventory_unit']}<br><small>{$attachment['serial_number']}</small></td>";
                echo "<td>{$attachment['attachment_tipe']} {$attachment['attachment_merk']} {$attachment['attachment_model']}<br><small>SN: {$attachment['sn_attachment']}</small></td>";
                echo "<td><span class='badge badge-info'>{$attachment['attachment_status']}</span></td>";
                echo "<td>" . date('Y-m-d H:i', strtotime($attachment['updated_at'])) . "</td>";
                echo "</tr>";
            }
            echo "</table></td></tr>";
        }
    }
    
    echo "</table>";
} else {
    echo "<div class='alert alert-info'>No SPK ATTACHMENT found</div>";
}

echo "<h3>💡 Summary</h3>";
echo "<ul>";
echo "<li><strong>✓ Match</strong> - Unit ID used in stage matches target_unit_id (CORRECT)</li>";
echo "<li><strong>⚠ NOT SET</strong> - SPK lama tanpa target_unit_id (menggunakan FALLBACK)</li>";
echo "<li><strong>⚠ Different</strong> - Unit ID used berbeda dengan target_unit_id (PERLU CEK)</li>";
echo "</ul>";

echo "</div></body></html>";

$mysqli->close();
?>
