-- Contoh implementasi untuk SPK Service dengan tipe_item

-- 1. Query untuk mendapatkan attachment yang tersedia
SELECT 
    ia.id_inventory_attachment,
    ia.sn_attachment as serial_number,
    a.tipe,
    a.merk,
    a.model,
    ia.kondisi_fisik,
    ia.kelengkapan,
    ia.lokasi_penyimpanan,
    ia.status_unit
FROM inventory_attachment ia
JOIN attachment a ON ia.attachment_id = a.id_attachment
WHERE ia.tipe_item = 'attachment' 
AND ia.status_unit = 7 -- status tersedia
ORDER BY a.tipe, a.merk, a.model;

-- 2. Query untuk mendapatkan battery yang tersedia
SELECT 
    ia.id_inventory_attachment,
    ia.sn_baterai as serial_number,
    b.merk_baterai,
    b.tipe_baterai,
    b.jenis_baterai,
    ia.kondisi_fisik,
    ia.kelengkapan,
    ia.lokasi_penyimpanan,
    ia.status_unit
FROM inventory_attachment ia
JOIN baterai b ON ia.baterai_id = b.id
WHERE ia.tipe_item = 'battery' 
AND ia.status_unit = 7 -- status tersedia
ORDER BY b.merk_baterai, b.tipe_baterai;

-- 3. Query untuk mendapatkan charger yang tersedia
SELECT 
    ia.id_inventory_attachment,
    ia.sn_charger as serial_number,
    c.merk_charger,
    c.tipe_charger,
    ia.kondisi_fisik,
    ia.kelengkapan,
    ia.lokasi_penyimpanan,
    ia.status_unit
FROM inventory_attachment ia
JOIN charger c ON ia.charger_id = c.id_charger
WHERE ia.tipe_item = 'charger' 
AND ia.status_unit = 7 -- status tersedia
ORDER BY c.merk_charger, c.tipe_charger;

-- 4. Query untuk SPK Service - mendapatkan semua tipe dengan union
(SELECT 
    'attachment' as tipe_kategori,
    ia.id_inventory_attachment,
    ia.sn_attachment as serial_number,
    CONCAT(a.tipe, ' - ', a.merk, ' ', a.model) as nama_item,
    ia.kondisi_fisik,
    ia.lokasi_penyimpanan
FROM inventory_attachment ia
JOIN attachment a ON ia.attachment_id = a.id_attachment
WHERE ia.tipe_item = 'attachment' AND ia.status_unit = 7)

UNION ALL

(SELECT 
    'battery' as tipe_kategori,
    ia.id_inventory_attachment,
    ia.sn_baterai as serial_number,
    CONCAT(b.merk_baterai, ' - ', b.tipe_baterai, ' (', b.jenis_baterai, ')') as nama_item,
    ia.kondisi_fisik,
    ia.lokasi_penyimpanan
FROM inventory_attachment ia
JOIN baterai b ON ia.baterai_id = b.id
WHERE ia.tipe_item = 'battery' AND ia.status_unit = 7)

UNION ALL

(SELECT 
    'charger' as tipe_kategori,
    ia.id_inventory_attachment,
    ia.sn_charger as serial_number,
    CONCAT(c.merk_charger, ' - ', c.tipe_charger) as nama_item,
    ia.kondisi_fisik,
    ia.lokasi_penyimpanan
FROM inventory_attachment ia
JOIN charger c ON ia.charger_id = c.id_charger
WHERE ia.tipe_item = 'charger' AND ia.status_unit = 7)

ORDER BY tipe_kategori, nama_item;
