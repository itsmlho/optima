Impor / reset data baterai dari CSV
===============================
Sumber: data_baterai.csv
Total baris inventory: 2952
  Lead Acid (item_number B####): 2177
  Lithium     (item_number BL####): 775
Kombinasi master unik `baterai`: 553

Urutan eksekusi MySQL:
  1) import_baterai_master.sql   -- lengkapi master
  2) import_inventory_batteries.sql       -- hapus semua inventory baterai + isi ulang dari CSV

Peringatan:
- File (2) akan DELETE semua baris inventory_batteries lalu INSERT dari CSV.
- Baris DELETE timeline / unit_movements / UPDATE SPK di file SQL dalam bentuk COMMENT;
  uncomment manual jika tabel/kolom tersebut ada di DB Anda.
- Backup database sebelum menjalankan.

Distribusi status:
  IN_USE: 1359
  AVAILABLE: 1319
  SOLD: 226
  SPARE: 48

Mapping status CSV -> inventory_batteries.status:
  TERPASANG -> IN_USE
  AVAILABLE -> AVAILABLE
  DIJUAL -> SOLD
  SPARE -> SPARE

Catatan teknis:
- item_number tidak lagi mengikuti kolom No Batt CSV; nomor urut per jenis (B/BL) mengikuti urutan baris file.
- Kolom notes berisi 'No Batt (CSV): ...' untuk lacak balik.
- MERK/TIPE kosong di CSV -> '-' (NOT NULL di master).

Peringatan otomatis skrip:
Baris ~611: No Batt CSV invalid '#VALUE!' -> item_number sistem B0610
Baris ~612: No Batt CSV invalid '#VALUE!' -> item_number sistem B0611
Baris ~613: No Batt CSV invalid '#VALUE!' -> item_number sistem B0612
Baris ~614: No Batt CSV invalid '#VALUE!' -> item_number sistem B0613
Baris ~615: No Batt CSV invalid '#VALUE!' -> item_number sistem B0614