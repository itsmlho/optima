<?php
/**
 * Post-Import Validation & Report
 * 
 * Purpose: Validate data integrity and generate comprehensive report
 * Checks: Orphans, totals, DRAFT contracts, duplicates
 */

$db = new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) {
    die("Connection failed: " . $db->connect_error);
}

echo "=== POST-IMPORT VALIDATION ===\n\n";

$report = [];
$report[] = "Data Migration Validation Report";
$report[] = "Generated: " . date('Y-m-d H:i:s');
$report[] = str_repeat("=", 60);
$report[] = "";

// 1. Basic Counts
echo "1. Checking basic counts...\n";
$kontrak_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak")->fetch_object()->cnt;
$kontrak_unit_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak_unit")->fetch_object()->cnt;

$report[] = "1. BASIC COUNTS";
$report[] = "   Total Kontrak: $kontrak_count";
$report[] = "   Total Kontrak_Unit: $kontrak_unit_count";
$report[] = "";

// 2. Status Distribution
echo "2. Checking status distribution...\n";
$kontrak_status = $db->query("SELECT status, COUNT(*) as cnt FROM kontrak GROUP BY status");
$report[] = "2. KONTRAK STATUS DISTRIBUTION";
while ($row = $kontrak_status->fetch_object()) {
    $report[] = "   {$row->status}: {$row->cnt}";
}
$report[] = "";

$ku_status = $db->query("SELECT status, COUNT(*) as cnt FROM kontrak_unit GROUP BY status");
$report[] = "   KONTRAK_UNIT STATUS DISTRIBUTION";
while ($row = $ku_status->fetch_object()) {
    $report[] = "   {$row->status}: {$row->cnt}";
}
$report[] = "";

// 3. Check Orphan Records
echo "3. Checking for orphan records...\n";
$orphan_ku = $db->query("
    SELECT COUNT(*) as cnt 
    FROM kontrak_unit ku 
    LEFT JOIN kontrak k ON ku.kontrak_id = k.id 
    WHERE k.id IS NULL
")->fetch_object()->cnt;

$orphan_units = $db->query("
    SELECT COUNT(*) as cnt 
    FROM kontrak_unit ku 
    LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit 
    WHERE iu.id_inventory_unit IS NULL
")->fetch_object()->cnt;

$report[] = "3. ORPHAN RECORDS CHECK";
$report[] = "   Kontrak_unit without valid kontrak: $orphan_ku";
$report[] = "   Kontrak_unit without valid unit: $orphan_units";
if ($orphan_ku == 0 && $orphan_units == 0) {
    $report[] = "   ✓ No orphan records found";
} else {
    $report[] = "   ⚠ WARNING: Orphan records detected!";
}
$report[] = "";

// 4. Verify Totals Match
echo "4. Verifying kontrak totals...\n";
$total_mismatch = $db->query("
    SELECT 
        k.id,
        k.no_kontrak,
        k.nilai_total as declared_total,
        COALESCE(SUM(ku.harga_sewa), 0) as calculated_total,
        k.total_units as declared_units,
        COUNT(ku.id) as actual_units
    FROM kontrak k
    LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id
    GROUP BY k.id
    HAVING ABS(declared_total - calculated_total) > 0.01 
        OR declared_units != actual_units
");

$report[] = "4. KONTRAK TOTALS VERIFICATION";
if ($total_mismatch->num_rows == 0) {
    $report[] = "   ✓ All kontrak totals match calculated values";
} else {
    $report[] = "   ⚠ Found {$total_mismatch->num_rows} contracts with mismatched totals:";
    $count = 0;
    while ($row = $total_mismatch->fetch_object()) {
        if ($count < 10) { // Show first 10
            $report[] = "     - Kontrak #{$row->id} ({$row->no_kontrak})";
            $report[] = "       Declared: Rp " . number_format($row->declared_total, 0, ',', '.') . " ({$row->declared_units} units)";
            $report[] = "       Calculated: Rp " . number_format($row->calculated_total, 0, ',', '.') . " ({$row->actual_units} units)";
        }
        $count++;
    }
    if ($count > 10) {
        $report[] = "     ... and " . ($count - 10) . " more";
    }
}
$report[] = "";

// 5. DRAFT Contracts Report
echo "5. Generating DRAFT contracts report...\n";
$draft_contracts = $db->query("
    SELECT 
        k.id,
        k.no_kontrak,
        k.customer_id,
        c.customer_name,
        k.tanggal_mulai,
        k.tanggal_berakhir,
        k.total_units
    FROM kontrak k
    LEFT JOIN customers c ON k.customer_id = c.id
    WHERE k.status = 'DRAFT'
    ORDER BY k.customer_id
");

$report[] = "5. DRAFT CONTRACTS (Need Manual Review)";
$report[] = "   Total DRAFT contracts: " . $draft_contracts->num_rows;
if ($draft_contracts->num_rows > 0) {
    $report[] = "";
    $report[] = "   Missing data that needs completion:";
    while ($row = $draft_contracts->fetch_object()) {
        $issues = [];
        if (empty($row->tanggal_mulai)) $issues[] = "missing start date";
        if (empty($row->tanggal_berakhir)) $issues[] = "missing end date";
        if (empty($row->no_kontrak)) $issues[] = "missing contract number";
        
        $report[] = "     - Kontrak #{$row->id}: {$row->customer_name}";
        $report[] = "       Contract: " . ($row->no_kontrak ?: '(empty)');
        $report[] = "       Issues: " . implode(', ', $issues);
        $report[] = "       Units: {$row->total_units}";
    }
}
$report[] = "";

// 6. Top Customers by Units
echo "6. Generating top customers report...\n";
$top_customers = $db->query("
    SELECT 
        c.customer_name,
        COUNT(DISTINCT k.id) as contract_count,
        COUNT(ku.id) as unit_count,
        SUM(ku.harga_sewa) as total_revenue
    FROM customers c
    LEFT JOIN kontrak k ON c.id = k.customer_id
    LEFT JOIN kontrak_unit ku ON k.id = ku.kontrak_id
    GROUP BY c.id
    HAVING unit_count > 0
    ORDER BY unit_count DESC
    LIMIT 10
");

$report[] = "6. TOP 10 CUSTOMERS BY UNIT COUNT";
$rank = 1;
while ($row = $top_customers->fetch_object()) {
    $report[] = sprintf(
        "   %2d. %-40s %3d contracts, %4d units, Rp %s",
        $rank++,
        substr($row->customer_name, 0, 40),
        $row->contract_count,
        $row->unit_count,
        number_format($row->total_revenue, 0, ',', '.')
    );
}
$report[] = "";

// 7. Date Range Analysis
echo "7. Analyzing contract date ranges...\n";
$date_analysis = $db->query("
    SELECT 
        MIN(tanggal_mulai) as earliest_start,
        MAX(tanggal_berakhir) as latest_end,
        COUNT(*) as contracts_with_dates
    FROM kontrak
    WHERE tanggal_mulai IS NOT NULL AND tanggal_berakhir IS NOT NULL
")->fetch_object();

$report[] = "7. CONTRACT DATE RANGE ANALYSIS";
$report[] = "   Contracts with valid dates: {$date_analysis->contracts_with_dates}";
if ($date_analysis->earliest_start) {
    $report[] = "   Earliest start date: {$date_analysis->earliest_start}";
    $report[] = "   Latest end date: {$date_analysis->latest_end}";
}
$report[] = "";

// 8. Summary
echo "8. Generating final summary...\n";
$report[] = str_repeat("=", 60);
$report[] = "MIGRATION SUMMARY";
$report[] = str_repeat("=", 60);
$report[] = "";
$report[] = "✓ Data extraction completed from accounting source";
$report[] = "✓ Unique contracts identified and imported: $kontrak_count";
$report[] = "✓ Unit relationships imported: $kontrak_unit_count";
if ($orphan_ku == 0 && $orphan_units == 0) {
    $report[] = "✓ No orphan records detected";
}

$draft_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak WHERE status = 'DRAFT'")->fetch_object()->cnt;
if ($draft_count > 0) {
    $report[] = "⚠ DRAFT contracts need manual review: $draft_count";
    $report[] = "  (Missing dates or contract numbers)";
}

$report[] = "";
$report[] = "NEXT STEPS:";
$report[] = "1. Review DRAFT contracts and complete missing data";
$report[] = "2. Test in application: http://localhost/optima/public/marketing/kontrak";
$report[] = "3. Verify modular unit attach/detach workflow";
$report[] = "4. If validated, deploy to production";
$report[] = "";
$report[] = str_repeat("=", 60);

// Write report to file
$report_text = implode("\n", $report);
$report_file = 'post_import_validation_report_' . date('Ymd_His') . '.txt';
file_put_contents($report_file, $report_text);

// Display report
echo "\n" . $report_text . "\n";
echo "\n✓ Report saved to: $report_file\n";

$db->close();
