-- Menambahkan kolom contact_person ke tabel spk
ALTER TABLE `spk` ADD COLUMN `contact_person` VARCHAR(255) NULL AFTER `pelanggan`;

-- Update indeks jika diperlukan
-- ALTER TABLE `spk` ADD INDEX `idx_spk_contact_person` (`contact_person`);
