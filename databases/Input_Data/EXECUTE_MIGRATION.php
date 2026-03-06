<?php
/**
 * MASTER EXECUTION SCRIPT
 * Production Data Migration from Accounting Source
 * 
 * This script guides you through all migration steps
 */

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║   PRODUCTION DATA MIGRATION - ACCOUNTING SOURCE                  ║\n";
echo "║   From: kontrak_acc.csv (2,008 rows)                             ║\n";
echo "║   To: 348 unique contracts + 2,008 unit relationships            ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

echo "This migration will:\n";
echo "  1. Extract 348 unique contracts from accounting data\n";
echo "  2. Backup existing kontrak & kontrak_unit tables\n";
echo "  3. TRUNCATE (reset) both tables\n";
echo "  4. Import 348 unique contracts\n";
echo "  5. Import 2,008 unit relationships\n";
echo "  6. Validate data integrity\n\n";

echo "⚠️  WARNING: This is a DESTRUCTIVE operation!\n";
echo "   All existing kontrak data will be replaced.\n\n";

echo "Prerequisites:\n";
echo "  ✓ kontrak_acc.csv must exist in this directory\n";
echo "  ✓ Database connection to optima_ci\n";
echo "  ✓ Backup of production data (recommended)\n\n";

// Check prerequisites
$missing = [];
if (!file_exists('kontrak_acc.csv')) {
    $missing[] = "kontrak_acc.csv not found";
}

$db = @new mysqli('localhost', 'root', '', 'optima_ci');
if ($db->connect_error) {
    $missing[] = "Cannot connect to database: " . $db->connect_error;
}

if (count($missing) > 0) {
    echo "❌ Missing prerequisites:\n";
    foreach ($missing as $m) {
        echo "   - $m\n";
    }
    die("\nPlease fix issues before proceeding.\n");
}

echo "✓ All prerequisites met\n\n";
echo str_repeat("─", 70) . "\n\n";

// Step-by-step execution
$steps = [
    [
        'number' => 1,
        'title' => 'Extract Unique Contracts',
        'script' => 'extract_unique_contracts.php',
        'description' => 'Analyze kontrak_acc.csv and extract 348 unique contracts',
        'output' => ['kontrak_from_accounting.csv', 'unit_to_contract_mapping.csv']
    ],
    [
        'number' => 2,
        'title' => 'Backup Current Data',
        'script' => 'backup_before_reset.php',
        'description' => 'Create safety backup of existing kontrak & kontrak_unit tables',
        'output' => ['backup_kontrak_pre_reset_*.sql', 'backup_kontrak_unit_pre_reset_*.sql']
    ],
    [
        'number' => 3,
        'title' => 'RESET Database',
        'script' => 'reset_database.php',
        'description' => 'TRUNCATE kontrak & kontrak_unit tables (requires confirmation)',
        'output' => ['Empty tables ready for fresh import']
    ],
    [
        'number' => 4,
        'title' => 'Import Unique Contracts',
        'script' => 'import_kontrak_from_accounting.php',
        'description' => 'Import 348 contracts to kontrak table',
        'output' => ['kontrak_id_mapping.json', '348 rows in kontrak table']
    ],
    [
        'number' => 5,
        'title' => 'Import Unit Relationships',
        'script' => 'import_kontrak_unit_from_accounting.php',
        'description' => 'Import 2,008 unit relationships to kontrak_unit table',
        'output' => ['~2,008 rows in kontrak_unit table']
    ],
    [
        'number' => 6,
        'title' => 'Validate & Report',
        'script' => 'validate_post_import.php',
        'description' => 'Verify data integrity and generate comprehensive report',
        'output' => ['post_import_validation_report_*.txt']
    ]
];

foreach ($steps as $step) {
    echo "STEP {$step['number']}: {$step['title']}\n";
    echo str_repeat("─", 70) . "\n";
    echo "Description: {$step['description']}\n";
    echo "Script: {$step['script']}\n";
    echo "\nExpected output:\n";
    foreach ($step['output'] as $output) {
        echo "  • $output\n";
    }
    echo "\n";
    
    if (!file_exists($step['script'])) {
        echo "❌ Script not found: {$step['script']}\n\n";
        continue;
    }
    
    echo "Ready to execute. Continue? (yes/no/skip): ";
    $response = strtolower(trim(fgets(STDIN)));
    
    if ($response === 'no') {
        echo "\n❌ Migration aborted by user at Step {$step['number']}.\n";
        exit(1);
    }
    
    if ($response === 'skip') {
        echo "⊘ Skipped Step {$step['number']}\n\n";
        echo str_repeat("═", 70) . "\n\n";
        continue;
    }
    
    echo "\n▶ Executing {$step['script']}...\n";
    echo str_repeat("─", 70) . "\n";
    
    $output = [];
    $return_code = 0;
    
    // Add flags for non-interactive mode
    $command = "php {$step['script']}";
    if ($step['script'] === 'reset_database.php') {
        $command .= " --confirm";
    } elseif ($step['script'] === 'import_kontrak_from_accounting.php') {
        $command .= " --force";
    }
    
    exec($command, $output, $return_code);
    
    echo implode("\n", $output) . "\n";
    
    if ($return_code !== 0) {
        echo "\n❌ Step {$step['number']} failed with exit code $return_code\n";
        echo "Please review errors and fix before continuing.\n";
        exit(1);
    }
    
    echo "\n✓ Step {$step['number']} completed successfully\n";
    echo str_repeat("═", 70) . "\n\n";
}

echo "╔══════════════════════════════════════════════════════════════════╗\n";
echo "║                  MIGRATION COMPLETED                             ║\n";
echo "╚══════════════════════════════════════════════════════════════════╝\n\n";

echo "Next steps:\n";
echo "  1. Review the validation report (post_import_validation_report_*.txt)\n";
echo "  2. Address any DRAFT contracts with missing data\n";
echo "  3. Test in application: http://localhost/optima/public/marketing/kontrak\n";
echo "  4. Verify modular unit workflows\n";
echo "  5. Deploy to production when ready\n\n";

echo "✓ All done!\n";

$db->close();
