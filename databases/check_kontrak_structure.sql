-- Check table structure and data
DESCRIBE kontrak;

-- Check if the new columns exist
SHOW COLUMNS FROM kontrak LIKE 'no_po_marketing';
SHOW COLUMNS FROM kontrak LIKE 'pic';
SHOW COLUMNS FROM kontrak LIKE 'kontak';
SHOW COLUMNS FROM kontrak LIKE 'nilai_total';
SHOW COLUMNS FROM kontrak LIKE 'total_units';

-- Show sample data
SELECT id, no_kontrak, no_po_marketing, pelanggan, pic, kontak, nilai_total, total_units, status 
FROM kontrak 
LIMIT 3;
