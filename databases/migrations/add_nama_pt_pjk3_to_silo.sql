-- Add nama_pt_pjk3 column to silo table
-- This field stores the name of the PJK3 company that performs inspection and testing

ALTER TABLE `silo` 
ADD COLUMN `nama_pt_pjk3` VARCHAR(255) NULL 
COMMENT 'Nama perusahaan PJK3 yang melakukan pemeriksaan dan testing' 
AFTER `status`;

-- Update existing records if needed (optional)
-- UPDATE `silo` SET `nama_pt_pjk3` = 'PT. GAHARU SAKTI PRATAMA' WHERE `nama_pt_pjk3` IS NULL AND `status` != 'BELUM_ADA';

