<?php
/**
 * RESET Database - TRUNCATE kontrak & kontrak_unit tables
 * 
 * WARNING: This will DELETE ALL DATA from kontrak and kontrak_unit tables!
 * Make sure you have run backup_before_reset.php first!
 */

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "=== DATABASE RESET - TRUNCATE TABLES ===\n\n";
echo "⚠️  WARNING: This will DELETE ALL DATA from:\n";
echo "   - kontrak table\n";
echo "   - kontrak_unit table\n\n";

// Check current counts
$kontrak_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak")->fetch_object()->cnt;
$ku_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak_unit")->fetch_object()->cnt;

echo "Current data:\n";
echo "  Kontrak: $kontrak_count rows\n";
echo "  Kontrak_unit: $ku_count rows\n\n";

// Check for backup files
$backup_files = glob("backup_kontrak_pre_reset_*.sql");
if (count($backup_files) == 0) {
    echo "⚠️  WARNING: No backup files found!\n";
    echo "   It is HIGHLY RECOMMENDED to run backup_before_reset.php first.\n\n";
}

// Support non-interactive mode via command line argument
if (isset($argv[1]) && $argv[1] === '--confirm') {
    echo "Running in non-interactive mode (--confirm flag detected)\n";
    $confirm = 'RESET';
} else {
    echo "Are you sure you want to continue? (Type 'RESET' to confirm): ";
    $confirm = trim(fgets(STDIN));
}

if ($confirm !== 'RESET') {
    die("\nAborted by user. No changes made.\n");
}

echo "\nProceeding with RESET...\n\n";

// Disable foreign key checks temporarily
$db->query("SET FOREIGN_KEY_CHECKS = 0");

// Truncate kontrak_unit first (child table)
echo "Truncating kontrak_unit table...\n";
if ($db->query("TRUNCATE TABLE kontrak_unit")) {
    echo "✓ Kontrak_unit table truncated\n";
} else {
    die("ERROR truncating kontrak_unit: " . $db->error . "\n");
}

// Truncate kontrak table
echo "Truncating kontrak table...\n";
if ($db->query("TRUNCATE TABLE kontrak")) {
    echo "✓ Kontrak table truncated\n";
} else {
    die("ERROR truncating kontrak: " . $db->error . "\n");
}

// Re-enable foreign key checks
$db->query("SET FOREIGN_KEY_CHECKS = 1");

// Verify
$verify_kontrak = $db->query("SELECT COUNT(*) as cnt FROM kontrak")->fetch_object()->cnt;
$verify_ku = $db->query("SELECT COUNT(*) as cnt FROM kontrak_unit")->fetch_object()->cnt;

echo "\n=== VERIFICATION ===\n";
echo "Kontrak rows: $verify_kontrak (should be 0)\n";
echo "Kontrak_unit rows: $verify_ku (should be 0)\n";

if ($verify_kontrak == 0 && $verify_ku == 0) {
    echo "\n✓ RESET complete. Tables are empty.\n";
    echo "\nNext step: Run import_kontrak_from_accounting.php\n";
} else {
    echo "\n⚠️  WARNING: Tables not empty after truncate!\n";
}

$db->close();
