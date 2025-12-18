<?php
require 'vendor/autoload.php';

$db = \Config\Database::connect();
$units = $db->table('inventory_unit iu')
    ->select('iu.id_inventory_unit as id, iu.no_unit as nomor_unit, mu.merk_unit as merk, mu.model_unit as model')
    ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
    ->orderBy('iu.no_unit', 'ASC')
    ->limit(3)
    ->get()
    ->getResultArray();

echo json_encode(['success' => true, 'data' => $units, 'count' => count($units)], JSON_PRETTY_PRINT);
