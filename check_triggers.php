<?php
// Check all triggers that reference kontrak_spesifikasi

$host = 'localhost';
$db   = 'optima_ci';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Checking all triggers in database '$db'...\n";
    echo str_repeat("=", 80) . "\n\n";
    
    // Get all triggers
    $stmt = $pdo->query("SHOW TRIGGERS");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total triggers: " . count($triggers) . "\n\n";
    
    $problemTriggers = [];
    
    foreach ($triggers as $trigger) {
        $triggerName = $trigger['Trigger'];
        $table = $trigger['Table'];
        $statement = $trigger['Statement'];
        
        // Remove comments to avoid false positives
        $statementNoComments = preg_replace('/--[^\n]*/', '', $statement);
        $statementNoComments = preg_replace('/\/\*.*?\*\//s', '', $statementNoComments);
        
        // Check if trigger ACTUALLY USES kontrak_spesifikasi_id column (not just in comments)
        if (stripos($statementNoComments, 'kontrak_spesifikasi_id') !== false) {
            $problemTriggers[] = [
                'name' => $triggerName,
                'table' => $table,
                'statement' => $statement
            ];
        }
    }
    
    if (empty($problemTriggers)) {
        echo "✓ No triggers found referencing kontrak_spesifikasi\n";
    } else {
        echo "⚠ Found " . count($problemTriggers) . " trigger(s) referencing kontrak_spesifikasi:\n";
        echo str_repeat("=", 80) . "\n\n";
        
        foreach ($problemTriggers as $i => $trigger) {
            echo ($i + 1) . ". Trigger: {$trigger['name']}\n";
            echo "   Table: {$trigger['table']}\n";
            echo "   Statement:\n";
            echo "   " . str_repeat("-", 76) . "\n";
            
            // Format statement for readability
            $formatted = preg_replace('/\s+/', ' ', $trigger['statement']);
            $formatted = str_replace([' BEGIN ', ' END', ' IF ', ' THEN ', ' SET '], 
                                   ["\n   BEGIN\n      ", "\n   END", "\n      IF ", " THEN\n         ", "\n      SET "], 
                                   $formatted);
            echo "   " . trim($formatted) . "\n";
            echo "   " . str_repeat("-", 76) . "\n\n";
        }
        
        echo "\nThese triggers need to be dropped or modified.\n";
        echo "Would you like to drop them? (They reference old kontrak_spesifikasi system)\n";
    }
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
