<?php
require_once 'vendor/autoload.php';

// Load CodeIgniter
$pathsConfig = 'app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;
$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
require realpath($bootstrap) ?: $bootstrap;
$app = Config\Services::codeigniter();
$app->initialize();

$db = \Config\Database::connect();

echo "=== CHECKING DATABASE SAMPLES FOR ACCURATE PLACEHOLDERS ===\n\n";

// 1. tipe_unit (Jenis Unit)
echo "1. TIPE_UNIT (Jenis Unit)\n";
echo "   Columns: id_tipe_unit, tipe, jenis, id_departemen\n";
$query = $db->query("SELECT * FROM tipe_unit LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Tipe: {$row->tipe}, Jenis: {$row->jenis}, Dept: {$row->id_departemen}\n";
}
echo "\n";

// 2. model_unit (Brand & Model)
echo "2. MODEL_UNIT (Brand & Model)\n";
echo "   Columns: id_model_unit, merk_unit, model_unit\n";
$query = $db->query("SELECT * FROM model_unit LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Brand: {$row->merk_unit}, Model: {$row->model_unit}\n";
}
echo "\n";

// 3. kapasitas
echo "3. KAPASITAS\n";
echo "   Columns: id_kapasitas, kapasitas_unit\n";
$query = $db->query("SELECT * FROM kapasitas LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Kapasitas: {$row->kapasitas_unit}\n";
}
echo "\n";

// 4. tipe_mast
echo "4. TIPE_MAST\n";
echo "   Columns: id_mast, tipe_mast, tinggi_mast\n";
$query = $db->query("SELECT * FROM tipe_mast LIMIT 5");
foreach ($query->getResult() as $row) {
    $tinggi = $row->tinggi_mast ?? 'NULL';
    echo "   - Tipe: {$row->tipe_mast}, Tinggi: {$tinggi}\n";
}
echo "\n";

// 5. mesin
echo "5. MESIN (Engine)\n";
echo "   Columns: id, merk_mesin, model_mesin, bahan_bakar\n";
$query = $db->query("SELECT * FROM mesin LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Merk: {$row->merk_mesin}, Model: {$row->model_mesin}, Bahan Bakar: {$row->bahan_bakar}\n";
}
echo "\n";

// 6. tipe_ban
echo "6. TIPE_BAN (Tire)\n";
echo "   Columns: id_ban, tipe_ban\n";
$query = $db->query("SELECT * FROM tipe_ban LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Tipe Ban: {$row->tipe_ban}\n";
}
echo "\n";

// 7. jenis_roda
echo "7. JENIS_RODA (Wheel)\n";
echo "   Columns: id_roda, tipe_roda\n";
$query = $db->query("SELECT * FROM jenis_roda LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Jenis Roda: {$row->tipe_roda}\n";
}
echo "\n";

// 8. valve
echo "8. VALVE\n";
echo "   Columns: id_valve, jumlah_valve\n";
$query = $db->query("SELECT * FROM valve LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Jumlah Valve: {$row->jumlah_valve}\n";
}
echo "\n";

// 9. baterai
echo "9. BATERAI (Battery)\n";
echo "   Columns: id, jenis_baterai, merk_baterai, tipe_baterai\n";
$query = $db->query("SELECT * FROM baterai LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "   - Jenis: {$row->jenis_baterai}, Merk: {$row->merk_baterai}, Tipe: {$row->tipe_baterai}\n";
}
echo "\n";

// 10. attachment
echo "10. ATTACHMENT\n";
echo "    Columns: id_attachment, tipe, merk, model\n";
$query = $db->query("SELECT * FROM attachment LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "    - Tipe: {$row->tipe}, Merk: {$row->merk}, Model: {$row->model}\n";
}
echo "\n";

// 11. charger
echo "11. CHARGER\n";
echo "    Columns: id_charger, merk_charger, tipe_charger\n";
$query = $db->query("SELECT * FROM charger LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "    - Merk: {$row->merk_charger}, Tipe: {$row->tipe_charger}\n";
}
echo "\n";

// 12. departemen
echo "12. DEPARTEMEN\n";
echo "    Columns: id_departemen, nama_departemen\n";
$query = $db->query("SELECT * FROM departemen LIMIT 5");
foreach ($query->getResult() as $row) {
    echo "    - Nama: {$row->nama_departemen}\n";
}
echo "\n";

echo "=== DONE ===\n";
