<?php
$file = realpath(__DIR__ . '/../app/Controllers/Warehouse.php');
if (!$file) { die('File not found'); }
$c = file_get_contents($file);

// The orphaned code starts right after closing brace of new getUnitHistory on line 1110
// It starts with "\n\n\n            $spkRows = $db->query('"
// and ends before "    /**\n     * Memperbarui data stok unit."
$startMarker = "\n\n\n            \$spkRows = \$db->query('";
$endMarker   = "\n\n    /**\n     * Memperbarui data stok unit.";

$startPos = strpos($c, $startMarker);
$endPos   = strpos($c, $endMarker);

if ($startPos === false) {
    die('Start marker not found — file may already be clean or marker changed.<br>Searching for $spkRows: ' . (strpos($c, '$spkRows') !== false ? 'FOUND' : 'NOT FOUND'));
}
if ($endPos === false) {
    die('End marker not found');
}

echo "Start pos: $startPos<br>";
echo "End pos: $endPos<br>";
echo "Removing " . ($endPos - $startPos) . " characters of orphaned code<br>";

// Remove the orphaned block
$fixed = substr($c, 0, $startPos) . "\n" . substr($c, $endPos);
file_put_contents($file, $fixed);
echo "Done! File saved. New size: " . strlen($fixed) . " bytes<br>";
echo "<a href=''>Run again to verify</a>";
?>
