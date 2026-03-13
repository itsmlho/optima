-- =============================================================================
-- CLEAN NOTIFICATION RULES - Prepare for fresh import
-- Migration: PROD_20260313_clean_notification_rules
-- Description: Membersihkan semua data lama di notification_rules untuk import data baru yang bersih
-- Author: Claude AI Assistant
-- Date: 2026-03-13
-- =============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- Langkah 1: Backup existing data sebelum dihapus (opsional - bisa di-comment jika tidak perlu)
-- CREATE TABLE IF NOT EXISTS notification_rules_backup_20260313 AS SELECT * FROM notification_rules;

-- Langkah 2: Hapus semua data di notification_rules
TRUNCATE TABLE notification_rules;

-- Langkah 3: Reset auto_increment
ALTER TABLE notification_rules AUTO_INCREMENT = 1;

SET FOREIGN_KEY_CHECKS = 1;

-- Verifikasi
SELECT COUNT(*) AS 'Records Remaining' FROM notification_rules;

-- Catatan: Setelah ini, import data baru dari file CSV notification_rules_clean.csv
-- atau gunakan INSERT statements yang sudah disediakan di file terpisah
