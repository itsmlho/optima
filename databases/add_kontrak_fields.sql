-- Menambahkan kolom pic, kontak, nilai_total, dan total_units ke tabel kontrak
-- Tanggal: 2025-08-19

ALTER TABLE `kontrak` 
ADD COLUMN `pic` VARCHAR(255) NULL DEFAULT NULL COMMENT 'Nama Person In Charge' AFTER `lokasi`,
ADD COLUMN `kontak` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Kontak PIC (telepon/email)' AFTER `pic`,
ADD COLUMN `nilai_total` DECIMAL(15,2) NULL DEFAULT NULL COMMENT 'Nilai total kontrak dalam rupiah' AFTER `kontak`,
ADD COLUMN `total_units` INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total unit yang terkait dengan kontrak ini' AFTER `nilai_total`;

-- Update existing records to set default total_units = 0 if needed
UPDATE `kontrak` SET `total_units` = 0 WHERE `total_units` IS NULL;

-- Optional: Add index for better performance on total_units queries
ALTER TABLE `kontrak` ADD INDEX `idx_total_units` (`total_units`);

-- Verify the changes
DESCRIBE `kontrak`;
