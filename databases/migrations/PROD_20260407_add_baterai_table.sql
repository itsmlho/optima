-- ============================================================
-- Migration: Create baterai (battery) master data table
-- Date: 2026-04-07
-- Purpose: Add baterai table required by quotation_specifications.battery_id FK
--          and SPK print equipment section (battery display)
-- Run on: Production DB (u138256737_optima_db)
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------------------------------------------
-- 1. Create baterai table (if not exists)
-- ----------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `baterai` (
    `id` int NOT NULL AUTO_INCREMENT,
    `merk_baterai` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `tipe_baterai` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `jenis_baterai` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- ----------------------------------------------------------------
-- 2. Seed baterai master data (80 records)
--    Use INSERT IGNORE to safely re-run without duplicates
-- ----------------------------------------------------------------
INSERT IGNORE INTO `baterai` (`id`, `merk_baterai`, `tipe_baterai`, `jenis_baterai`) VALUES
(1, 'BSLBAT', 'LITHIUM', '48V 460AH'),
(2, 'BSLBAT', 'LITHIUM', '72V 405AH'),
(3, 'BSLBAT', 'LITHIUM', '80V 560AH'),
(4, 'BSLBAT', 'LITHIUM', '48V 410AH'),
(5, 'BSLBAT', 'LITHIUM', '48V 540AH'),
(6, 'BSLBAT', 'LITHIUM', '48V 615AH'),
(7, 'BSLBAT', 'LITHIUM', '80V 410AH'),
(8, 'BSLBAT', 'LITHIUM', '72V 410AH'),
(9, 'BSLBAT', 'LITHIUM', '48V 280AH'),
(10, 'BSLBAT', 'LITHIUM', '36V 840AH'),
(11, 'BSLBAT', 'LITHIUM', '48V 820AH'),
(12, 'BSLBAT', 'LITHIUM', '48V 560AH'),
(13, 'BSLBAT', 'LITHIUM', '48V 205AH'),
(14, 'BSLBAT', 'LITHIUM', '48V 690AH'),
(15, 'REMICO', 'LEAD ACID', '48V 620AH (4EPzS620)'),
(16, 'REMICO', 'LEAD ACID', '36V 779AH'),
(17, 'REMICO', 'LEAD ACID', '36V 958AH'),
(18, 'REMICO', 'LEAD ACID', '80V 1120AH (8EPzS1120)'),
(19, 'REMICO', 'LEAD ACID', '48V 560AH'),
(20, 'REMICO', 'LEAD ACID', '72V 480AH (6EPzS480)'),
(21, 'REMICO', 'LEAD ACID', '24V 250AH'),
(22, 'REMICO', 'LEAD ACID', '80V 700AH (10PzB600)'),
(23, 'REMICO', 'LEAD ACID', '36V 824AH'),
(24, 'REMICO', 'LEAD ACID', '48V 640AH'),
(25, 'TAB', 'LEAD ACID', '48V 620AH (24/4EPzS620L)'),
(26, 'TAB', 'LEAD ACID', '48V 602AH (24/7PzSB602)'),
(27, 'TAB', 'LEAD ACID', '36V 779AH (18/7PzB779E)'),
(28, 'TAB', 'LEAD ACID', '80V 420AH'),
(29, 'TAB', 'LEAD ACID', '48V 560AH'),
(30, 'TAB', 'LEAD ACID', '24V 300AH'),
(31, 'TAB', 'LEAD ACID', '72V 480AH'),
(32, 'TAB', 'LEAD ACID', '80V 700AH'),
(33, 'GS YUASA', 'LEAD ACID', 'VGI565 (48V 565AH)'),
(34, 'GS YUASA', 'LEAD ACID', '2DCM250A (24V 250AH)'),
(35, 'GS YUASA', 'LEAD ACID', '3DCM375A (24V 375AH)'),
(36, 'GS YUASA', 'LEAD ACID', 'VGL400H (24V 400AH)'),
(37, 'GS YUASA', 'LEAD ACID', 'VGI470 (72V 470AH)'),
(38, 'GS YUASA', 'LEAD ACID', 'VSF4 (48V 290AH)'),
(39, 'GS YUASA', 'LEAD ACID', 'VGD565 (48V 565AH)'),
(40, 'GS YUASA', 'LEAD ACID', '4005085YI (80V 420AH)'),
(41, 'KOBE', 'LEAD ACID', '48V 560AH'),
(42, 'KOBE', 'LEAD ACID', '48V 312AH'),
(43, 'KOBE', 'LEAD ACID', '48V 280AH'),
(44, 'KOBE', 'LEAD ACID', '48V 565AH'),
(45, 'ENEROC NEW ENERGY', 'LITHIUM', 'HL-C135-51.52-404 (48V 404AH)'),
(46, 'ENEROC NEW ENERGY', 'LITHIUM', 'HL-C370M1-80.50-202 (80V 202AH)'),
(47, 'ENEROC NEW ENERGY', 'LITHIUM', 'HL-C136-80.50-271 (80V 271AH)'),
(48, 'ENEROC NEW ENERGY', 'LITHIUM', 'HL-C093-80.50-404 (80V 404AH)'),
(49, 'ENEROC NEW ENERGY', 'LITHIUM', 'HL-C098-77.28-542 (80V 542AH)'),
(50, 'ENEROC NEW ENERGY', 'LITHIUM', 'HL-C098M1-77.28-604 (80V 604AH)'),
(51, 'ENEROC NEW ENERGY', 'LITHIUM', 'HL-C042-80.50-606 (80V 606AH)'),
(52, 'HANGCHA', 'LITHIUM', 'HC-C001-25.76-125 (24V 125AH)'),
(53, 'HANGCHA', 'LITHIUM', 'HC-C009-80,50-404 (80V 404AH)'),
(54, 'HANGCHA', 'LITHIUM', 'HC-C103-77,28-542 (80V 542AH)'),
(55, 'CEIL', 'LEAD ACID', '18 EXTEF 15 (36V 756AH)'),
(56, 'CEIL', 'LEAD ACID', '18 EXTEF 19 (36V 972AH)'),
(57, 'CEIL', 'LEAD ACID', '8 IPZB (36V 864AH)'),
(58, 'HAWKER', 'LEAD ACID', '3PzB225 (24V 225AH)'),
(59, 'HAWKER', 'LEAD ACID', '4PzB400 (24V 400AH)'),
(60, 'HAWKER', 'LEAD ACID', '4PZS560 (48V 560AH)'),
(61, 'HAWKER', 'LEAD ACID', '5PzS700DRY (48V 620AH)'),
(62, 'HAWKER', 'LEAD ACID', '24-3PZS420 (48V 420AH)'),
(63, 'JUNGHEINRICH', 'LEAD ACID', '3PzS375 (24V 375AH)'),
(64, 'JUNGHEINRICH', 'LEAD ACID', '4PzS620 (48V 620AH)'),
(65, 'JUNGHEINRICH', 'LEAD ACID', '6PzS750 (48V 750AH)'),
(66, 'JUNGHEINRICH', 'LEAD ACID', '5PzS775 (48V 775AH)'),
(67, 'FAAM', 'LEAD ACID', '2PzS230 (24V 230AH)'),
(68, 'FAAM', 'LEAD ACID', 'VSF4 (48V 210AH)'),
(69, 'HELI', 'LEAD ACID', '0DAC090020 (48V 250AH)'),
(70, 'HELI', 'LEAD ACID', '0D2M0A0119 (48V 250AH)'),
(71, 'HITACHI', 'LEAD ACID', '24/VSI565 (48V 565AH)'),
(72, 'HITACHI', 'LEAD ACID', '24/VTFL280 (48V 280AH)'),
(73, 'ROCKET', 'LEAD ACID', 'VCI600 (48V 600AH)'),
(74, 'STILL', 'LEAD ACID', '6PzS840 (80V 840AH)'),
(75, 'ASIA', 'LEAD ACID', '48V 300AH Standard'),
(76, 'ENERSYS', 'LEAD ACID', '36V 1000AH Standard'),
(77, 'GENERIC LITHIUM', 'LITHIUM', 'ZL24210 (24V 210AH)'),
(78, 'GENERIC LITHIUM', 'LITHIUM', 'HXYA-LFP-25.6V210 (24V 210AH)'),
(79, 'GENERIC LITHIUM', 'LITHIUM', 'ZL80460-78 (80V 460AH)'),
(80, 'GENERIC LITHIUM', 'LITHIUM', 'LiFeP04 High Volt (153.6V 230AH)');

-- ----------------------------------------------------------------
-- 3. Restore AUTO_INCREMENT
-- ----------------------------------------------------------------
ALTER TABLE `baterai` AUTO_INCREMENT = 81;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================================
-- END OF MIGRATION
-- ============================================================
