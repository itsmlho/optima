<?php
$db = new mysqli('localhost', 'root', '', 'optima_ci');

echo "=== DATABASE STATUS AFTER IMPORT ===\n\n";

$total_kontrak = $db->query('SELECT COUNT(*) as cnt FROM kontrak')->fetch_object()->cnt;
echo "Total kontrak: $total_kontrak\n";

$total_kontrak_unit = $db->query('SELECT COUNT(*) as cnt FROM kontrak_unit')->fetch_object()->cnt;
echo "Total kontrak_unit: $total_kontrak_unit\n\n";

echo "Kontrak dengan nilai_total = 0 (was 'spare', 'TRIAL', etc):\n";
$spare_count = $db->query("SELECT COUNT(*) as cnt FROM kontrak WHERE nilai_total = 0")->fetch_object()->cnt;
echo "  Count: $spare_count\n\n";

echo "First 5 kontrak:\n";
$result = $db->query('SELECT id, no_kontrak, customer_id, nilai_total, status FROM kontrak ORDER BY id LIMIT 5');
while ($r = $result->fetch_object()) {
    echo "  id={$r->id}, no={$r->no_kontrak}, customer={$r->customer_id}, nilai=" . number_format($r->nilai_total) . ", {$r->status}\n";
}

echo "\nLast 5 kontrak:\n";
$result = $db->query('SELECT id, no_kontrak, customer_id, nilai_total, status FROM kontrak ORDER BY id DESC LIMIT 5');
while ($r = $result->fetch_object()) {
    echo "  id={$r->id}, no={$r->no_kontrak}, customer={$r->customer_id}, nilai=" . number_format($r->nilai_total) . ", {$r->status}\n";
}

$db->close();
