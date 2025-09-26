-- Script untuk memverifikasi dan update data unit dengan kontrak
-- Checking units without contract
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.serial_number,
    iu.kontrak_id,
    iu.lokasi_unit,
    k.pelanggan,
    k.lokasi as lokasi_kontrak,
    su.status_unit
FROM inventory_unit iu
LEFT JOIN kontrak k ON k.id = iu.kontrak_id
LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
WHERE iu.status_unit_id != 2 -- Exclude WORKSHOP-RUSAK
ORDER BY iu.no_unit ASC;

-- Update lokasi_unit jika kosong tapi ada di kontrak
UPDATE inventory_unit iu
JOIN kontrak k ON k.id = iu.kontrak_id
SET iu.lokasi_unit = k.lokasi
WHERE (iu.lokasi_unit IS NULL OR iu.lokasi_unit = '')
AND k.lokasi IS NOT NULL
AND k.lokasi != '';

-- Check units yang sudah ada kontrak tapi belum ada lokasi
SELECT 
    iu.id_inventory_unit,
    iu.no_unit,
    iu.kontrak_id,
    k.no_kontrak,
    k.pelanggan,
    k.lokasi,
    iu.lokasi_unit
FROM inventory_unit iu
JOIN kontrak k ON k.id = iu.kontrak_id
WHERE (iu.lokasi_unit IS NULL OR iu.lokasi_unit = '')
AND iu.status_unit_id != 2;