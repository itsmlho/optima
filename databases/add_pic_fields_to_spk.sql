-- Menambahkan kolom pic_name dan pic_contact ke tabel spk
ALTER TABLE `spk` ADD COLUMN `pic_name` VARCHAR(255) NULL AFTER `contact_person`;
ALTER TABLE `spk` ADD COLUMN `pic_contact` VARCHAR(255) NULL AFTER `pic_name`;

-- Update indeks jika diperlukan
-- ALTER TABLE `spk` ADD INDEX `idx_spk_pic_name` (`pic_name`);
-- ALTER TABLE `spk` ADD INDEX `idx_spk_pic_contact` (`pic_contact`);
