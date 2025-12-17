<?php
// Fix triggers that reference kontrak_spesifikasi

$host = 'localhost';
$db   = 'optima_ci';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Fixing triggers that reference kontrak_spesifikasi...\n";
    echo str_repeat("=", 80) . "\n\n";
    
    // 1. Fix tr_inventory_unit_bi - Remove kontrak_spesifikasi_id check
    echo "1. Fixing trigger: tr_inventory_unit_bi\n";
    echo "   Action: Remove kontrak_spesifikasi_id reference (column doesn't exist)\n\n";
    
    $pdo->exec("DROP TRIGGER IF EXISTS tr_inventory_unit_bi");
    
    $sql1 = "CREATE TRIGGER tr_inventory_unit_bi 
    BEFORE INSERT ON inventory_unit 
    FOR EACH ROW 
    BEGIN
        -- Set status to KONTRAK (3) if unit is linked to a contract
        -- Removed kontrak_spesifikasi_id check as system migrated to quotation_specifications
        IF NEW.kontrak_id IS NOT NULL THEN
            SET NEW.status_unit_id = 3;
        END IF;
    END";
    
    $pdo->exec($sql1);
    echo "   ✓ Recreated tr_inventory_unit_bi (removed kontrak_spesifikasi_id)\n\n";
    
    // 2. Fix tr_delivery_instructions_update_unit - Remove kontrak_spesifikasi_id join
    echo "2. Fixing trigger: tr_delivery_instructions_update_unit\n";
    echo "   Action: Remove kontrak_spesifikasi_id join condition\n\n";
    
    $pdo->exec("DROP TRIGGER IF EXISTS tr_delivery_instructions_update_unit");
    
    $sql2 = "CREATE TRIGGER tr_delivery_instructions_update_unit
    AFTER UPDATE ON delivery_instructions
    FOR EACH ROW
    BEGIN
        -- Update tanggal_kirim in inventory_unit when DI is updated
        -- Removed kontrak_spesifikasi_id join as system migrated to quotations
        IF NEW.tanggal_kirim IS NOT NULL 
           AND NEW.spk_id IS NOT NULL 
           AND (OLD.tanggal_kirim IS NULL OR OLD.tanggal_kirim != NEW.tanggal_kirim) THEN
            
            UPDATE inventory_unit iu 
            JOIN spk s ON iu.spk_id = s.id
            SET iu.tanggal_kirim = NEW.tanggal_kirim,
                iu.delivery_instruction_id = NEW.id,
                iu.updated_at = CURRENT_TIMESTAMP
            WHERE s.id = NEW.spk_id;
        END IF;
    END";
    
    $pdo->exec($sql2);
    echo "   ✓ Recreated tr_delivery_instructions_update_unit (removed kontrak_spesifikasi_id join)\n\n";
    
    echo str_repeat("=", 80) . "\n";
    echo "✓ All triggers fixed successfully!\n\n";
    echo "Summary:\n";
    echo "- Removed kontrak_spesifikasi_id reference from tr_inventory_unit_bi\n";
    echo "- Removed kontrak_spesifikasi_id join from tr_delivery_instructions_update_unit\n";
    echo "- System now uses quotation_specifications instead\n\n";
    echo "You can now retry the warehouse verification. The error should be gone.\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
