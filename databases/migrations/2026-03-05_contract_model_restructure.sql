-- =====================================================
-- Migration: Contract Model Restructure
-- Date: 2026-03-05
-- Purpose: Kontrak sebagai payung umbrella (bukan 1 baris per unit)
--          Invoice contract_id jadi opsional (bisa invoice tanpa kontrak/PO)
--          nilai_total kontrak jadi computed field (dari kontrak_unit)
-- =====================================================

-- Step 1: Ubah contract_id di tabel invoices menjadi opsional (nullable)
-- Sebelumnya: NOT NULL → Sekarang: NULL boleh (invoice tanpa kontrak)
ALTER TABLE invoices MODIFY COLUMN contract_id INT NULL;

-- Step 2: Tambah kolom po_reference opsional di invoices (jika belum ada)
-- Untuk mencatat nomor PO dari customer, terpisah dari contract_id
ALTER TABLE invoices
  ADD COLUMN IF NOT EXISTS po_reference VARCHAR(100) NULL
  COMMENT 'Nomor PO dari customer, opsional, tidak terikat ke tabel kontrak';

-- Step 3: Ubah nilai_total di kontrak menjadi nullable
-- Nilai real sudah dihitung secara dinamis dari kontrak_unit → inventory_unit.harga_sewa_bulanan
-- Field ini dijadikan "legacy/vestigial" - tidak diisi saat insert baru
ALTER TABLE kontrak
  MODIFY COLUMN nilai_total DECIMAL(15,2) NULL DEFAULT NULL
  COMMENT 'Legacy: nilai real dihitung dinamis dari kontrak_unit JOIN inventory_unit';

-- Step 4: Tambah index untuk po_reference agar pencarian cepat
CREATE INDEX IF NOT EXISTS idx_invoices_po_reference ON invoices(po_reference);
CREATE INDEX IF NOT EXISTS idx_invoices_contract_id ON invoices(contract_id);

-- =====================================================
-- Verification Queries
-- =====================================================

-- Cek invoice yang tidak punya contract_id (null)
-- SELECT COUNT(*) FROM invoices WHERE contract_id IS NULL;

-- Cek kontrak yang punya banyak unit via kontrak_unit
-- SELECT k.id, k.no_kontrak, COUNT(ku.id) as jumlah_unit,
--        SUM(iu.harga_sewa_bulanan) as total_nilai_real,
--        k.nilai_total as nilai_total_lama
-- FROM kontrak k
-- LEFT JOIN kontrak_unit ku ON ku.kontrak_id = k.id AND ku.status = 'ACTIVE' AND ku.is_temporary = 0
-- LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ku.unit_id
-- GROUP BY k.id, k.no_kontrak, k.nilai_total
-- ORDER BY jumlah_unit DESC
-- LIMIT 20;

-- =====================================================
-- Rollback (jika perlu)
-- =====================================================
-- ALTER TABLE invoices MODIFY COLUMN contract_id INT NOT NULL;
-- ALTER TABLE invoices DROP COLUMN IF EXISTS po_reference;
-- ALTER TABLE kontrak MODIFY COLUMN nilai_total DECIMAL(15,2) NOT NULL DEFAULT 0;
-- DROP INDEX IF EXISTS idx_invoices_po_reference ON invoices;
