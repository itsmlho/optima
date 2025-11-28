<?php
// CLI script: recalc_kontrak_totals.php
// Usage: php recalc_kontrak_totals.php
// This script will iterate all kontrak rows and call the stored procedure recalc_kontrak_total(id)
// It will print a report of changed nilai_total values.

$host = '127.0.0.1';
$user = 'root';
$pass = 'root';
$db   = 'optima_ci';
$port = 3306;

$mysqli = new mysqli($host, $user, $pass, $db, $port);
if ($mysqli->connect_errno) {
    fwrite(STDERR, "Failed to connect to MySQL: (".$mysqli->connect_errno.") " . $mysqli->connect_error . "\n");
    exit(1);
}

$mysqli->set_charset('utf8mb4');

echo "Connected to {$db} on {$host}:{$port}\n";

// Get all kontrak ids
$res = $mysqli->query("SELECT id, no_kontrak, nilai_total FROM kontrak");
if (!$res) {
    fwrite(STDERR, "Failed to query kontrak: " . $mysqli->error . "\n");
    exit(1);
}

$all = [];
while ($row = $res->fetch_assoc()) {
    $all[] = $row;
}
$res->free();

if (count($all) === 0) {
    echo "No kontrak rows found. Exiting.\n";
    exit(0);
}

echo "Found " . count($all) . " kontrak rows. Recalculating...\n\n";

$updated = [];
$errors = [];

foreach ($all as $k) {
    $id = intval($k['id']);
    $before = (float)$k['nilai_total'];
    $no_kontrak = $k['no_kontrak'];

    // Call stored procedure
    $call = $mysqli->query("CALL recalc_kontrak_total({$id})");
    if ($call === false) {
        $errors[] = [ 'id' => $id, 'no_kontrak' => $no_kontrak, 'error' => $mysqli->error ];
        // try to recover by clearing multi-results
        while ($mysqli->more_results() && $mysqli->next_result()) { /* consume */ }
        continue;
    }
    // consume results and any extra result sets
    while ($mysqli->more_results() && $mysqli->next_result()) { /* consume */ }

    // Get new value
    $row2 = $mysqli->query("SELECT nilai_total FROM kontrak WHERE id = {$id}");
    if ($row2) {
        $new = (float)$row2->fetch_assoc()['nilai_total'];
        $row2->free();
        if ($new !== $before) {
            $updated[] = [ 'id' => $id, 'no_kontrak' => $no_kontrak, 'before' => $before, 'after' => $new ];
            echo "Updated kontrak id={$id} ({$no_kontrak}): {$before} -> {$new}\n";
        }
    } else {
        $errors[] = [ 'id' => $id, 'no_kontrak' => $no_kontrak, 'error' => $mysqli->error ];
    }
}

echo "\nRecalc finished.\n";
echo "Total kontrak processed: " . count($all) . "\n";
echo "Total updated: " . count($updated) . "\n";
echo "Total errors: " . count($errors) . "\n";

if (count($errors) > 0) {
    echo "\nErrors:\n";
    foreach ($errors as $e) {
        echo "  id={$e['id']} no_kontrak={$e['no_kontrak']} error={$e['error']}\n";
    }
}

$mysqli->close();

exit(0);
