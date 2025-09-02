-- Script perbaikan database - tahap 1: Primary Keys dan Auto Increment
USE optima_db;

-- 1. Perbaiki kontrak_spesifikasi - tambahkan PRIMARY KEY dan AUTO_INCREMENT
ALTER TABLE `kontrak_spesifikasi` 
ADD PRIMARY KEY (`id`),
MODIFY `id` int unsigned NOT NULL AUTO_INCREMENT;

-- 2. Perbaiki attachment table
ALTER TABLE `attachment` 
ADD PRIMARY KEY (`id_attachment`),
MODIFY `id_attachment` int NOT NULL AUTO_INCREMENT;

-- 3. Perbaiki baterai table  
ALTER TABLE `baterai` 
ADD PRIMARY KEY (`id`),
MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- 4. Perbaiki charger table
ALTER TABLE `charger`
ADD PRIMARY KEY (`id_charger`),
MODIFY `id_charger` int NOT NULL AUTO_INCREMENT;

-- 5. Perbaiki departemen table
ALTER TABLE `departemen`
ADD PRIMARY KEY (`id_departemen`),
MODIFY `id_departemen` int NOT NULL AUTO_INCREMENT;

-- 6. Perbaiki jenis_roda table primary key
ALTER TABLE `jenis_roda`
ADD PRIMARY KEY (`id_roda`),
MODIFY `id_roda` int NOT NULL AUTO_INCREMENT;

-- 7. Perbaiki kapasitas table primary key
ALTER TABLE `kapasitas`
ADD PRIMARY KEY (`id_kapasitas`),
MODIFY `id_kapasitas` int NOT NULL AUTO_INCREMENT;

-- 8. Perbaiki mesin table primary key
ALTER TABLE `mesin`
ADD PRIMARY KEY (`id`),
MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- 9. Perbaiki model_unit table primary key
ALTER TABLE `model_unit`
ADD PRIMARY KEY (`id_model_unit`),
MODIFY `id_model_unit` int NOT NULL AUTO_INCREMENT;

-- 10. Perbaiki notifications table primary key
ALTER TABLE `notifications`
ADD PRIMARY KEY (`id`),
MODIFY `id` int NOT NULL AUTO_INCREMENT;

-- 11. Perbaiki notification_logs table primary key
ALTER TABLE `notification_logs`
ADD PRIMARY KEY (`id_notification`),
MODIFY `id_notification` int NOT NULL AUTO_INCREMENT;

-- 12. Perbaiki sparepart table primary key dan unique key
ALTER TABLE `sparepart`
ADD PRIMARY KEY (`id_sparepart`),
MODIFY `id_sparepart` int NOT NULL AUTO_INCREMENT,
ADD UNIQUE KEY `kode` (`kode`);

SELECT 'Primary keys and auto increment fixed' as result;
