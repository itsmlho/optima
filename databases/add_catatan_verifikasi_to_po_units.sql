-- Add catatan_verifikasi column to po_units table
-- This column stores verification notes/rejection reasons

ALTER TABLE `po_units` 
ADD COLUMN `catatan_verifikasi` TEXT NULL 
COMMENT 'Catatan verifikasi / alasan reject jika status Tidak Sesuai' 
AFTER `keterangan`;

