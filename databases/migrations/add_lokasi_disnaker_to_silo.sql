-- Add lokasi_disnaker column to silo table
-- This field stores the DISNAKER location/area (e.g., KARAWANG, BEKASI, etc.)

ALTER TABLE `silo`
ADD COLUMN `lokasi_disnaker` VARCHAR(255) NULL AFTER `catatan_pengajuan_uptd`;

-- Add index for better query performance
ALTER TABLE `silo`
ADD INDEX `idx_lokasi_disnaker` (`lokasi_disnaker`);

