-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Sep 09, 2025 at 11:56 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `optima_db`
--
CREATE DATABASE IF NOT EXISTS `optima_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `optima_db`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_types`
--
-- Creation: Sep 08, 2025 at 06:54 AM
--

DROP TABLE IF EXISTS `activity_types`;
CREATE TABLE IF NOT EXISTS `activity_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) NOT NULL,
  `type_code` varchar(50) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `business_impact_default` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'LOW',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_module_type` (`module_name`,`type_code`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `activity_types`:
--

--
-- Truncate table before insert `activity_types`
--

TRUNCATE TABLE `activity_types`;
--
-- Dumping data for table `activity_types`
--

INSERT DELAYED IGNORE INTO `activity_types` (`id`, `module_name`, `type_code`, `type_name`, `description`, `business_impact_default`, `is_active`, `created_at`) VALUES
(1, 'PURCHASING', 'PO_CREATE', 'Purchase Order Created', 'New purchase order created', 'HIGH', 1, '2025-09-08 06:54:41'),
(2, 'PURCHASING', 'PO_APPROVE', 'Purchase Order Approved', 'Purchase order approved by authorized person', 'HIGH', 1, '2025-09-08 06:54:41'),
(3, 'PURCHASING', 'PO_REJECT', 'Purchase Order Rejected', 'Purchase order rejected', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(4, 'PURCHASING', 'PO_CANCEL', 'Purchase Order Cancelled', 'Purchase order cancelled', 'HIGH', 1, '2025-09-08 06:54:41'),
(5, 'PURCHASING', 'VENDOR_ADD', 'Vendor Added', 'New vendor/supplier added to system', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(6, 'PURCHASING', 'VENDOR_UPDATE', 'Vendor Updated', 'Vendor information updated', 'LOW', 1, '2025-09-08 06:54:41'),
(7, 'PURCHASING', 'QUOTATION_REQUEST', 'Quotation Requested', 'Quotation requested from vendor', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(8, 'PURCHASING', 'QUOTATION_RECEIVE', 'Quotation Received', 'Quotation received from vendor', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(9, 'WAREHOUSE', 'STOCK_IN', 'Stock In', 'Items received into warehouse', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(10, 'WAREHOUSE', 'STOCK_OUT', 'Stock Out', 'Items issued from warehouse', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(11, 'WAREHOUSE', 'STOCK_TRANSFER', 'Stock Transfer', 'Items transferred between locations', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(12, 'WAREHOUSE', 'STOCK_ADJUSTMENT', 'Stock Adjustment', 'Stock quantity adjusted', 'HIGH', 1, '2025-09-08 06:54:41'),
(13, 'WAREHOUSE', 'LOCATION_CREATE', 'Location Created', 'New warehouse location created', 'LOW', 1, '2025-09-08 06:54:41'),
(14, 'WAREHOUSE', 'INVENTORY_COUNT', 'Inventory Count', 'Physical inventory count performed', 'HIGH', 1, '2025-09-08 06:54:41'),
(15, 'WAREHOUSE', 'DAMAGE_REPORT', 'Damage Reported', 'Damaged items reported', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(16, 'MARKETING', 'LEAD_CREATE', 'Lead Created', 'New sales lead created', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(17, 'MARKETING', 'LEAD_CONVERT', 'Lead Converted', 'Lead converted to opportunity', 'HIGH', 1, '2025-09-08 06:54:41'),
(18, 'MARKETING', 'QUOTE_GENERATE', 'Quote Generated', 'Sales quotation generated', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(19, 'MARKETING', 'CONTRACT_CREATE', 'Contract Created', 'New contract/kontrak created', 'HIGH', 1, '2025-09-08 06:54:41'),
(20, 'MARKETING', 'CONTRACT_APPROVE', 'Contract Approved', 'Contract approved', 'CRITICAL', 1, '2025-09-08 06:54:41'),
(21, 'MARKETING', 'CONTRACT_SIGN', 'Contract Signed', 'Contract signed by customer', 'CRITICAL', 1, '2025-09-08 06:54:41'),
(22, 'MARKETING', 'UNIT_ASSIGN', 'Unit Assigned', 'Unit assigned to contract', 'HIGH', 1, '2025-09-08 06:54:41'),
(23, 'SERVICE', 'SPK_CREATE', 'SPK Created', 'Service work order (SPK) created', 'HIGH', 1, '2025-09-08 06:54:41'),
(24, 'SERVICE', 'SPK_START', 'SPK Started', 'Work on SPK started', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(25, 'SERVICE', 'SPK_COMPLETE', 'SPK Completed', 'SPK work completed', 'HIGH', 1, '2025-09-08 06:54:41'),
(26, 'SERVICE', 'MAINTENANCE_SCHEDULE', 'Maintenance Scheduled', 'Maintenance scheduled for unit', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(27, 'SERVICE', 'MAINTENANCE_COMPLETE', 'Maintenance Completed', 'Maintenance work completed', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(28, 'SERVICE', 'REPAIR_REQUEST', 'Repair Requested', 'Repair service requested', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(29, 'SERVICE', 'PART_USED', 'Parts Used', 'Spare parts used in service', 'LOW', 1, '2025-09-08 06:54:41'),
(30, 'OPERATIONAL', 'DI_CREATE', 'Delivery Instruction Created', 'New delivery instruction created', 'HIGH', 1, '2025-09-08 06:54:41'),
(31, 'OPERATIONAL', 'DISPATCH', 'Unit Dispatched', 'Unit dispatched for delivery', 'HIGH', 1, '2025-09-08 06:54:41'),
(32, 'OPERATIONAL', 'DELIVERY_COMPLETE', 'Delivery Completed', 'Unit delivered to customer', 'CRITICAL', 1, '2025-09-08 06:54:41'),
(33, 'OPERATIONAL', 'PICKUP_SCHEDULE', 'Pickup Scheduled', 'Unit pickup scheduled', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(34, 'OPERATIONAL', 'PICKUP_COMPLETE', 'Pickup Completed', 'Unit picked up from customer', 'HIGH', 1, '2025-09-08 06:54:41'),
(35, 'OPERATIONAL', 'ROUTE_OPTIMIZE', 'Route Optimized', 'Delivery route optimized', 'LOW', 1, '2025-09-08 06:54:41'),
(36, 'ACCOUNTING', 'INVOICE_CREATE', 'Invoice Created', 'New invoice created', 'HIGH', 1, '2025-09-08 06:54:41'),
(37, 'ACCOUNTING', 'INVOICE_SEND', 'Invoice Sent', 'Invoice sent to customer', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(38, 'ACCOUNTING', 'PAYMENT_RECEIVE', 'Payment Received', 'Payment received from customer', 'CRITICAL', 1, '2025-09-08 06:54:41'),
(39, 'ACCOUNTING', 'PAYMENT_OVERDUE', 'Payment Overdue', 'Payment marked as overdue', 'HIGH', 1, '2025-09-08 06:54:41'),
(40, 'ACCOUNTING', 'EXPENSE_RECORD', 'Expense Recorded', 'Business expense recorded', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(41, 'ACCOUNTING', 'JOURNAL_ENTRY', 'Journal Entry', 'Accounting journal entry created', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(42, 'ACCOUNTING', 'RECONCILIATION', 'Bank Reconciliation', 'Bank account reconciled', 'HIGH', 1, '2025-09-08 06:54:41'),
(43, 'PERIZINAN', 'PERMIT_APPLY', 'Permit Application', 'New permit application submitted', 'HIGH', 1, '2025-09-08 06:54:41'),
(44, 'PERIZINAN', 'PERMIT_APPROVE', 'Permit Approved', 'Permit application approved', 'CRITICAL', 1, '2025-09-08 06:54:41'),
(45, 'PERIZINAN', 'PERMIT_REJECT', 'Permit Rejected', 'Permit application rejected', 'HIGH', 1, '2025-09-08 06:54:41'),
(46, 'PERIZINAN', 'PERMIT_RENEW', 'Permit Renewed', 'Existing permit renewed', 'HIGH', 1, '2025-09-08 06:54:41'),
(47, 'PERIZINAN', 'PERMIT_EXPIRE', 'Permit Expired', 'Permit expired', 'CRITICAL', 1, '2025-09-08 06:54:41'),
(48, 'PERIZINAN', 'DOCUMENT_UPLOAD', 'Document Uploaded', 'Supporting document uploaded', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(49, 'PERIZINAN', 'COMPLIANCE_CHECK', 'Compliance Check', 'Regulatory compliance check performed', 'HIGH', 1, '2025-09-08 06:54:41'),
(50, 'ADMIN', 'USER_CREATE', 'User Created', 'New user account created', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(51, 'ADMIN', 'USER_DEACTIVATE', 'User Deactivated', 'User account deactivated', 'HIGH', 1, '2025-09-08 06:54:41'),
(52, 'ADMIN', 'ROLE_ASSIGN', 'Role Assigned', 'Role assigned to user', 'HIGH', 1, '2025-09-08 06:54:41'),
(53, 'ADMIN', 'PERMISSION_GRANT', 'Permission Granted', 'Permission granted to user/role', 'HIGH', 1, '2025-09-08 06:54:41'),
(54, 'ADMIN', 'SYSTEM_BACKUP', 'System Backup', 'System backup performed', 'CRITICAL', 1, '2025-09-08 06:54:41'),
(55, 'ADMIN', 'CONFIG_CHANGE', 'Configuration Changed', 'System configuration changed', 'HIGH', 1, '2025-09-08 06:54:41'),
(56, 'DASHBOARD', 'DASHBOARD_VIEW', 'Dashboard Viewed', 'Dashboard page accessed', 'LOW', 1, '2025-09-08 06:54:41'),
(57, 'REPORTS', 'REPORT_GENERATE', 'Report Generated', 'Business report generated', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(58, 'REPORTS', 'REPORT_EXPORT', 'Report Exported', 'Report exported to file', 'MEDIUM', 1, '2025-09-08 06:54:41'),
(59, 'REPORTS', 'REPORT_SCHEDULE', 'Report Scheduled', 'Automatic report scheduled', 'LOW', 1, '2025-09-08 06:54:41');

-- --------------------------------------------------------

--
-- Table structure for table `attachment`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `attachment`;
CREATE TABLE IF NOT EXISTS `attachment` (
  `id_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `tipe` varchar(100) NOT NULL,
  `merk` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  PRIMARY KEY (`id_attachment`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `attachment`:
--

--
-- Truncate table before insert `attachment`
--

TRUNCATE TABLE `attachment`;
--
-- Dumping data for table `attachment`
--

INSERT DELAYED IGNORE INTO `attachment` (`id_attachment`, `tipe`, `merk`, `model`) VALUES
(1, 'FORK POSITIONER', 'CASCADE', '120K-FPS-CO82'),
(2, 'FORK POSITIONER', 'CASCADE', '65K-FPS'),
(3, 'PAPER ROLL CLAMP', 'CASCADE', '77F-RCP-01C'),
(4, 'PAPER ROLL CLAMP', 'CASCADE', '90F-RCP'),
(5, 'SIDE SHIFTER', 'CASCADE', '50D-BCS-64A'),
(6, 'SIDE SHIFTER', 'CASCADE', '50D-BCS-64B'),
(7, 'FORKLIFT SCALE', 'COMPULOAD', 'CL 2000'),
(8, 'PAPER ROLL CLAMP', 'HELI', 'ZJ22H-B5'),
(9, 'PAPER roll CLAMP', 'HELI', 'ZJ33H-B5'),
(10, 'FORK', 'HELI', 'FORK');

-- --------------------------------------------------------

--
-- Table structure for table `baterai`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `baterai`;
CREATE TABLE IF NOT EXISTS `baterai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merk_baterai` varchar(100) NOT NULL,
  `tipe_baterai` varchar(100) NOT NULL,
  `jenis_baterai` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `baterai`:
--

--
-- Truncate table before insert `baterai`
--

TRUNCATE TABLE `baterai`;
--
-- Dumping data for table `baterai`
--

INSERT DELAYED IGNORE INTO `baterai` (`id`, `merk_baterai`, `tipe_baterai`, `jenis_baterai`) VALUES
(1, 'JUNGHEINRICH (JHR)', '48V / 775AH AQUAMATIC (5PZS775)', 'Lead Acid'),
(2, 'JUNGHEINRICH (JHR)', '48V / 750AH AQUAMATIC (6PZS750)', 'Lead Acid'),
(3, 'JUNGHEINRICH (JHR)', '48V / 620AH AQUAMATIC (4PZS620)', 'Lead Acid'),
(4, 'JUNGHEINRICH (JHR)', '48V / 560AH (4PZS560)', 'Lead Acid'),
(5, 'JUNGHEINRICH (JHR)', '24V / 375AH AQUAMATIC (3EPZS375)', 'Lead Acid'),
(6, 'JUNGHEINRICH (JHR)', '24V / 205AH (2EPZS250L)', 'Lead Acid'),
(7, 'STILL', '80V 5PZS700', 'Lead Acid'),
(8, 'STILL', '24V 8PZS1000', 'Lead Acid'),
(9, 'STILL', '48V 5PZS775', 'Lead Acid'),
(10, 'STILL', '24V 6PZS690', 'Lead Acid'),
(11, 'STILL', '24V / 375AH (3PZS345)', 'Lead Acid'),
(12, 'STILL', '24V / 345AH (3PZS345)', 'Lead Acid'),
(13, 'STILL', '24V / 230AH (2PZS230)', 'Lead Acid'),
(14, 'HAWKER', '48V / 700AH', 'Lead Acid'),
(15, 'TAB', '80V / 700AH (40EPZS700)', 'Lead Acid'),
(16, 'TAB', '80V / 420AH (40EPZS420L)', 'Lead Acid'),
(17, 'TAB', '48V', 'Lead Acid'),
(18, 'REMICO', '24V / 250AH AQUAMATIC (12/2EPzS250L)', 'Lead Acid'),
(19, 'SLBATT', 'B-LFP80-410MH', 'Lithium-ion'),
(20, 'HELI', 'HL-C135-51.52-404', 'Lithium-ion'),
(21, '-', '80V 6PZS840', 'Lead Acid'),
(22, '-', '48V 5PZS886AH AQUA', 'Lead Acid'),
(23, '-', '40/4EDZ420V & 40/EPZ450V', 'Lead Acid'),
(24, '-', 'JL-25.6F225PS', 'Lithium-ion'),
(25, '-', '80V / 404AH', 'Lithium-ion'),
(26, '-', '80V / 271AH', 'Lithium-ion'),
(27, '-', '25.6V / 150Ah', 'Lithium-ion');

-- --------------------------------------------------------

--
-- Table structure for table `charger`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `charger`;
CREATE TABLE IF NOT EXISTS `charger` (
  `id_charger` int(11) NOT NULL AUTO_INCREMENT,
  `merk_charger` varchar(100) NOT NULL,
  `tipe_charger` varchar(100) NOT NULL,
  PRIMARY KEY (`id_charger`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `charger`:
--

--
-- Truncate table before insert `charger`
--

TRUNCATE TABLE `charger`;
--
-- Dumping data for table `charger`
--

INSERT DELAYED IGNORE INTO `charger` (`id_charger`, `merk_charger`, `tipe_charger`) VALUES
(1, 'JUNGHEINRICH', 'SLT010nDe48/80P(48V / 80A)'),
(2, 'JUNGHEINRICH', 'SLT010nDe48/100P(48V / 100A)'),
(3, 'JUNGHEINRICH', 'SLT010nEe24/35P(24V / 35A)'),
(4, 'JUNGHEINRICH', '(Standar)(24V / 70A)'),
(5, 'STILL', 'ECOTRON XM(80V / 125A)'),
(6, 'STILL', 'ECOTRON XM(24V / 60A)'),
(7, 'STILL', 'ECOTRON XM(48V / 126A)'),
(8, 'STILL', 'ECOTRON XM(48V / 150A)'),
(9, 'STILL', 'SLT010n(48V / 100A)'),
(10, 'HAWKER', 'SDC-ECO(24V / 60A)'),
(11, 'HAWKER', 'EX2460(24V / 60A)'),
(12, 'HR CHARGER', 'D400G24/70B-SLT100(24V / 70A)'),
(13, 'RIGETEK', 'RG-T(80V / 200A)'),
(14, 'RIGETEK', 'RG-T(48V / 100A)'),
(15, 'TITAN-POWER', 'SPC-24100(24V / 100A)'),
(16, 'TITAN-POWER', 'SPC-48100(48V / 100A)'),
(17, '-', 'D80V/150PXS(80V / 150A)');

-- --------------------------------------------------------

--
-- Table structure for table `delivery_instructions`
--
-- Creation: Sep 04, 2025 at 02:17 AM
--

DROP TABLE IF EXISTS `delivery_instructions`;
CREATE TABLE IF NOT EXISTS `delivery_instructions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_di` varchar(100) NOT NULL,
  `spk_id` int(10) UNSIGNED DEFAULT NULL,
  `po_kontrak_nomor` varchar(100) DEFAULT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('SUBMITTED','PROCESSED','SHIPPED','DELIVERED','CANCELLED') NOT NULL DEFAULT 'SUBMITTED',
  `jenis_perintah_kerja_id` int(11) DEFAULT NULL,
  `tujuan_perintah_kerja_id` int(11) DEFAULT NULL,
  `status_eksekusi_workflow_id` int(11) DEFAULT 1,
  `dibuat_oleh` int(10) UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `perencanaan_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval perencanaan pengiriman',
  `estimasi_sampai` date DEFAULT NULL COMMENT 'Estimasi tanggal sampai dari perencanaan',
  `nama_supir` varchar(100) DEFAULT NULL COMMENT 'Nama supir yang bertugas',
  `no_hp_supir` varchar(20) DEFAULT NULL COMMENT 'Nomor HP supir',
  `no_sim_supir` varchar(50) DEFAULT NULL COMMENT 'Nomor SIM supir',
  `kendaraan` varchar(100) DEFAULT NULL COMMENT 'Jenis/merk kendaraan yang digunakan',
  `no_polisi_kendaraan` varchar(20) DEFAULT NULL COMMENT 'Nomor polisi kendaraan',
  `berangkat_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval berangkat',
  `catatan_berangkat` text DEFAULT NULL COMMENT 'Catatan keberangkatan dan kondisi barang',
  `sampai_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval sampai',
  `catatan_sampai` text DEFAULT NULL COMMENT 'Catatan kedatangan dan konfirmasi penerima',
  `status_temp` enum('DIAJUKAN','DISETUJUI','PERSIAPAN_UNIT','SIAP_KIRIM','DALAM_PERJALANAN','SAMPAI_LOKASI','SELESAI','DIBATALKAN') DEFAULT 'DIAJUKAN',
  PRIMARY KEY (`id`),
  KEY `fk_di_spk` (`spk_id`),
  KEY `fk_di_jenis_perintah_kerja` (`jenis_perintah_kerja_id`),
  KEY `fk_di_tujuan_perintah_kerja` (`tujuan_perintah_kerja_id`),
  KEY `fk_di_status_eksekusi_workflow` (`status_eksekusi_workflow_id`)
) ENGINE=InnoDB AUTO_INCREMENT=124 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `delivery_instructions`:
--   `jenis_perintah_kerja_id`
--       `jenis_perintah_kerja` -> `id`
--   `spk_id`
--       `spk` -> `id`
--   `status_eksekusi_workflow_id`
--       `status_eksekusi_workflow` -> `id`
--   `tujuan_perintah_kerja_id`
--       `tujuan_perintah_kerja` -> `id`
--

--
-- Truncate table before insert `delivery_instructions`
--

TRUNCATE TABLE `delivery_instructions`;
--
-- Dumping data for table `delivery_instructions`
--

INSERT DELAYED IGNORE INTO `delivery_instructions` (`id`, `nomor_di`, `spk_id`, `po_kontrak_nomor`, `pelanggan`, `lokasi`, `tanggal_kirim`, `catatan`, `status`, `jenis_perintah_kerja_id`, `tujuan_perintah_kerja_id`, `status_eksekusi_workflow_id`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`, `perencanaan_tanggal_approve`, `estimasi_sampai`, `nama_supir`, `no_hp_supir`, `no_sim_supir`, `kendaraan`, `no_polisi_kendaraan`, `berangkat_tanggal_approve`, `catatan_berangkat`, `sampai_tanggal_approve`, `catatan_sampai`, `status_temp`) VALUES
(100, 'DI/202509/TEST001', 27, 'PO-TEST-001', 'PT Test Customer', 'Jakarta', NULL, NULL, 'SUBMITTED', 1, 1, 1, 1, '2025-09-03 16:52:00', '2025-09-03 16:52:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'DIAJUKAN'),
(122, 'DI/202509/001', 27, 'test12345', 'MONORKOBO', 'BEKASI', '2025-09-04', NULL, 'PROCESSED', 1, 1, 1, 1, '2025-09-04 03:43:23', '2025-09-04 17:05:12', '2025-09-04', '2025-09-04', 'JOKO', '082138848123', '1231012', 'colt diesel', '123', NULL, NULL, NULL, NULL, 'SIAP_KIRIM'),
(123, 'DI/202509/002', 28, 'MSI', 'MSI', 'EROPA', '2025-09-04', 'a', 'DELIVERED', 1, 1, 1, 1, '2025-09-04 04:14:52', '2025-09-04 15:53:00', '2025-09-04', '2025-09-04', 'JOKO', '082138848123', '1231012', 'colt diesel (123)', '123', '2025-09-04', NULL, '2025-09-04', 'ok', 'SELESAI');

--
-- Triggers `delivery_instructions`
--
DROP TRIGGER IF EXISTS `sync_di_status_temp_on_update`;
DELIMITER $$
CREATE TRIGGER `sync_di_status_temp_on_update` BEFORE UPDATE ON `delivery_instructions` FOR EACH ROW BEGIN
    
    IF NEW.status = 'DELIVERED' THEN
        SET NEW.status_temp = 'SELESAI';
    ELSEIF NEW.status = 'CANCELLED' THEN
        SET NEW.status_temp = 'DIBATALKAN';
    ELSEIF NEW.sampai_tanggal_approve IS NOT NULL THEN
        SET NEW.status_temp = 'SELESAI';
    ELSEIF NEW.berangkat_tanggal_approve IS NOT NULL THEN
        SET NEW.status_temp = 'DALAM_PERJALANAN';
    ELSEIF NEW.nama_supir IS NOT NULL AND NEW.kendaraan IS NOT NULL AND NEW.status = 'PROCESSED' THEN
        SET NEW.status_temp = 'SIAP_KIRIM';
    ELSEIF NEW.status = 'PROCESSED' THEN
        SET NEW.status_temp = 'PERSIAPAN_UNIT';
    ELSEIF NEW.status = 'SHIPPED' THEN
        SET NEW.status_temp = 'DALAM_PERJALANAN';
    ELSE
        SET NEW.status_temp = 'DIAJUKAN';
    END IF;
    
    SET NEW.diperbarui_pada = NOW();
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_items`
--
-- Creation: Sep 04, 2025 at 03:34 AM
--

DROP TABLE IF EXISTS `delivery_items`;
CREATE TABLE IF NOT EXISTS `delivery_items` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `di_id` int(10) UNSIGNED NOT NULL,
  `item_type` enum('UNIT','ATTACHMENT') NOT NULL DEFAULT 'UNIT',
  `unit_id` int(10) UNSIGNED DEFAULT NULL,
  `parent_unit_id` int(11) DEFAULT NULL,
  `attachment_id` int(10) UNSIGNED DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_delivery_items_di_id` (`di_id`),
  KEY `idx_delivery_items_type` (`item_type`),
  KEY `idx_delivery_items_unit` (`unit_id`),
  KEY `idx_delivery_items_attachment` (`attachment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=128 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Items untuk delivery instruction';

--
-- RELATIONSHIPS FOR TABLE `delivery_items`:
--   `di_id`
--       `delivery_instructions` -> `id`
--   `unit_id`
--       `inventory_unit` -> `id_inventory_unit`
--

--
-- Truncate table before insert `delivery_items`
--

TRUNCATE TABLE `delivery_items`;
--
-- Dumping data for table `delivery_items`
--

INSERT DELAYED IGNORE INTO `delivery_items` (`id`, `di_id`, `item_type`, `unit_id`, `parent_unit_id`, `attachment_id`, `keterangan`, `created_at`, `updated_at`) VALUES
(116, 122, 'UNIT', 1, NULL, NULL, NULL, '2025-09-04 03:43:23', '2025-09-04 03:43:23'),
(117, 122, 'UNIT', 12, NULL, NULL, NULL, '2025-09-04 03:43:23', '2025-09-04 03:43:23'),
(118, 122, 'ATTACHMENT', NULL, 1, 4, 'Battery for Unit 1', '2025-09-03 20:43:23', '2025-09-03 20:43:23'),
(119, 122, 'ATTACHMENT', NULL, 1, 5, 'Charger for Unit 1', '2025-09-03 20:43:23', '2025-09-03 20:43:23'),
(120, 122, 'ATTACHMENT', NULL, 12, 5, 'Charger for Unit 12', '2025-09-03 20:43:23', '2025-09-03 20:43:23'),
(121, 122, 'ATTACHMENT', NULL, 12, 6, 'Battery for Unit 12', '2025-09-03 20:43:23', '2025-09-03 20:43:23'),
(122, 123, 'UNIT', 1, NULL, NULL, NULL, '2025-09-04 04:14:52', '2025-09-04 04:14:52'),
(123, 123, 'UNIT', 2, NULL, NULL, NULL, '2025-09-04 04:14:52', '2025-09-04 04:14:52'),
(124, 123, 'ATTACHMENT', NULL, 1, 4, 'Battery for Unit 1', '2025-09-03 21:14:52', '2025-09-03 21:14:52'),
(125, 123, 'ATTACHMENT', NULL, 1, 5, 'Charger for Unit 1', '2025-09-03 21:14:52', '2025-09-03 21:14:52'),
(126, 123, 'ATTACHMENT', NULL, 2, 4, 'Battery for Unit 2', '2025-09-03 21:14:52', '2025-09-03 21:14:52'),
(127, 123, 'ATTACHMENT', NULL, 2, 5, 'Charger for Unit 2', '2025-09-03 21:14:52', '2025-09-03 21:14:52');

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `departemen`;
CREATE TABLE IF NOT EXISTS `departemen` (
  `id_departemen` int(11) NOT NULL AUTO_INCREMENT,
  `nama_departemen` varchar(100) NOT NULL,
  PRIMARY KEY (`id_departemen`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `departemen`:
--

--
-- Truncate table before insert `departemen`
--

TRUNCATE TABLE `departemen`;
--
-- Dumping data for table `departemen`
--

INSERT DELAYED IGNORE INTO `departemen` (`id_departemen`, `nama_departemen`) VALUES
(1, 'DIESEL'),
(2, 'ELECTRIC'),
(3, 'GASOLINE');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--
-- Creation: Sep 03, 2025 at 09:25 AM
--

DROP TABLE IF EXISTS `divisions`;
CREATE TABLE IF NOT EXISTS `divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `divisions`:
--

--
-- Truncate table before insert `divisions`
--

TRUNCATE TABLE `divisions`;
--
-- Dumping data for table `divisions`
--

INSERT DELAYED IGNORE INTO `divisions` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Administration', 'ADMIN', 'System Administration Division', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(2, 'Service', 'SERVICE', 'Service Division - Unit Maintenance & Repair', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(3, 'Unit Operational', 'UNIT_OPS', 'Unit Operational Division - Delivery & Rolling', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(4, 'Marketing', 'MARKETING', 'Marketing Division - Sales & Customer Relations', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(5, 'Warehouse & Assets', 'WAREHOUSE', 'Warehouse & Assets Management Division', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(6, 'Purchasing', 'PURCHASING', 'Purchasing Division - Procurement & Vendor Management', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(7, 'Perizinan', 'PERIZINAN', 'Licensing Division - Permits & Documentation', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(8, 'Accounting', 'ACCOUNTING', 'Accounting Division - Finance & Bookkeeping', 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57');

-- --------------------------------------------------------

--
-- Table structure for table `forklifts`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `forklifts`;
CREATE TABLE IF NOT EXISTS `forklifts` (
  `forklift_id` int(10) UNSIGNED NOT NULL,
  `unit_code` varchar(20) NOT NULL,
  `unit_name` varchar(255) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  `type` enum('electric','diesel','gas','hybrid') NOT NULL DEFAULT 'electric',
  `capacity` decimal(5,2) NOT NULL COMMENT 'Capacity in tons',
  `fuel_type` enum('electric','diesel','petrol','gas','hybrid') NOT NULL DEFAULT 'electric',
  `engine_power` decimal(8,2) DEFAULT NULL COMMENT 'Engine power in HP or kW',
  `lift_height` decimal(6,2) DEFAULT NULL COMMENT 'Maximum lift height in meters',
  `year_manufactured` year(4) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT NULL,
  `current_value` decimal(15,2) DEFAULT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `last_service_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `service_interval_hours` int(11) NOT NULL DEFAULT 250 COMMENT 'Service interval in operating hours',
  `total_operating_hours` int(11) NOT NULL DEFAULT 0 COMMENT 'Total operating hours',
  `status` enum('available','rented','maintenance','retired','reserved') NOT NULL DEFAULT 'available',
  `condition` enum('excellent','good','fair','poor','damaged') NOT NULL DEFAULT 'excellent',
  `location` varchar(255) DEFAULT NULL COMMENT 'Current location/warehouse',
  `assigned_to` int(10) UNSIGNED DEFAULT NULL COMMENT 'Assigned to user ID',
  `rental_rate_daily` decimal(10,2) DEFAULT NULL COMMENT 'Daily rental rate',
  `rental_rate_weekly` decimal(10,2) DEFAULT NULL COMMENT 'Weekly rental rate',
  `rental_rate_monthly` decimal(10,2) DEFAULT NULL COMMENT 'Monthly rental rate',
  `availability` enum('available','unavailable','reserved') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional specifications in JSON format',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'File attachments in JSON format',
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `deleted_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`forklift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `forklifts`:
--

--
-- Truncate table before insert `forklifts`
--

TRUNCATE TABLE `forklifts`;
--
-- Dumping data for table `forklifts`
--

INSERT DELAYED IGNORE INTO `forklifts` (`forklift_id`, `unit_code`, `unit_name`, `brand`, `model`, `type`, `capacity`, `fuel_type`, `engine_power`, `lift_height`, `year_manufactured`, `serial_number`, `purchase_date`, `purchase_price`, `current_value`, `supplier`, `warranty_expiry`, `insurance_expiry`, `last_service_date`, `next_service_date`, `service_interval_hours`, `total_operating_hours`, `status`, `condition`, `location`, `assigned_to`, `rental_rate_daily`, `rental_rate_weekly`, `rental_rate_monthly`, `availability`, `notes`, `specifications`, `attachments`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'FL001', 'Toyota 8FG25 Forklift', 'Toyota', '8FG25', 'gas', 2.50, 'gas', 68.00, 4.70, '2022', 'TYT8FG25001', '2022-01-15', 450000000.00, 380000000.00, 'Toyota Material Handling', '2025-01-15', '2024-12-31', NULL, NULL, 250, 1250, 'available', 'excellent', 'Warehouse A', NULL, 850000.00, 5500000.00, 20000000.00, 'available', 'Unit kondisi prima, rutin maintenance', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(2, 'FL002', 'Komatsu FB20-12 Electric Forklift', 'Komatsu', 'FB20-12', 'electric', 2.00, 'electric', 24.00, 3.00, '2023', 'KMT FB20001', '2023-03-10', 380000000.00, 340000000.00, 'Komatsu Forklift Indonesia', '2026-03-10', '2024-12-31', NULL, NULL, 300, 890, 'rented', 'excellent', 'Customer Site - PT ABC', NULL, 750000.00, 4800000.00, 18000000.00, 'unavailable', 'Sedang disewa PT ABC Industries', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(3, 'FL003', 'Hyster H3.5FT Diesel Forklift', 'Hyster', 'H3.5FT', 'diesel', 3.50, 'diesel', 74.00, 4.50, '2021', 'HYS H35001', '2021-08-20', 520000000.00, 420000000.00, 'Hyster Indonesia', '2024-08-20', '2024-12-31', NULL, NULL, 250, 2150, 'maintenance', 'good', 'Service Center', NULL, 950000.00, 6200000.00, 23000000.00, 'unavailable', 'Maintenance rutin 2000 jam operasi', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(4, 'FL004', 'Mitsubishi FG15N Gas Forklift', 'Mitsubishi', 'FG15N', 'gas', 1.50, 'gas', 42.00, 3.00, '2023', 'MIT FG15001', '2023-06-15', 320000000.00, 300000000.00, 'Mitsubishi Forklift', '2026-06-15', '2024-12-31', NULL, NULL, 250, 456, 'available', 'excellent', 'Warehouse B', NULL, 650000.00, 4200000.00, 15000000.00, 'available', 'Unit baru, kondisi prima', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(5, 'FL005', 'Crown FC 5200 Electric Forklift', 'Crown', 'FC 5200', 'electric', 2.00, 'electric', 36.00, 4.00, '2022', 'CRW FC5200001', '2022-09-10', 420000000.00, 360000000.00, 'Crown Equipment Indonesia', '2025-09-10', '2024-12-31', NULL, NULL, 300, 1680, 'reserved', 'good', 'Warehouse A', NULL, 780000.00, 5000000.00, 18500000.00, 'reserved', 'Reserved untuk kontrak PT XYZ minggu depan', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_attachment`
--
-- Creation: Sep 04, 2025 at 07:42 AM
--

DROP TABLE IF EXISTS `inventory_attachment`;
CREATE TABLE IF NOT EXISTS `inventory_attachment` (
  `id_inventory_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_item` enum('attachment','battery','charger') NOT NULL DEFAULT 'attachment',
  `po_id` int(11) NOT NULL COMMENT 'Foreign key ke purchase_orders.id_po',
  `id_inventory_unit` int(10) UNSIGNED DEFAULT NULL,
  `attachment_id` int(11) DEFAULT NULL COMMENT 'FK ke attachment',
  `sn_attachment` varchar(255) DEFAULT NULL,
  `baterai_id` int(11) DEFAULT NULL,
  `sn_baterai` varchar(100) DEFAULT NULL,
  `charger_id` int(11) DEFAULT NULL COMMENT 'FK ke charger',
  `sn_charger` varchar(255) DEFAULT NULL,
  `kondisi_fisik` enum('Baik','Rusak Ringan','Rusak Berat') DEFAULT 'Baik',
  `kelengkapan` enum('Lengkap','Tidak Lengkap') DEFAULT 'Lengkap',
  `catatan_fisik` text DEFAULT NULL,
  `lokasi_penyimpanan` varchar(255) DEFAULT NULL,
  `status_unit` int(11) DEFAULT 7,
  `tanggal_masuk` datetime DEFAULT current_timestamp() COMMENT 'Tanggal masuk ke inventory',
  `catatan_inventory` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_inventory_attachment`),
  KEY `fk_inventory_attachment_attachment` (`attachment_id`),
  KEY `fk_inventory_attachment_baterai` (`baterai_id`),
  KEY `fk_inventory_attachment_charger` (`charger_id`),
  KEY `fk_inventory_attachment_status_unit` (`status_unit`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Single source of truth untuk semua komponen: battery, charger, attachment';

--
-- RELATIONSHIPS FOR TABLE `inventory_attachment`:
--   `attachment_id`
--       `attachment` -> `id_attachment`
--   `baterai_id`
--       `baterai` -> `id`
--   `charger_id`
--       `charger` -> `id_charger`
--   `status_unit`
--       `status_unit` -> `id_status`
--

--
-- Truncate table before insert `inventory_attachment`
--

TRUNCATE TABLE `inventory_attachment`;
--
-- Dumping data for table `inventory_attachment`
--

INSERT DELAYED IGNORE INTO `inventory_attachment` (`id_inventory_attachment`, `tipe_item`, `po_id`, `id_inventory_unit`, `attachment_id`, `sn_attachment`, `baterai_id`, `sn_baterai`, `charger_id`, `sn_charger`, `kondisi_fisik`, `kelengkapan`, `catatan_fisik`, `lokasi_penyimpanan`, `status_unit`, `tanggal_masuk`, `catatan_inventory`, `created_at`, `updated_at`) VALUES
(2, 'attachment', 118, 1, 1, '123', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-22 04:36:39', 'Dari verifikasi PO: Sesuai dan siap digunakan', '2025-08-22 04:36:39', '2025-09-08 11:29:47'),
(3, 'attachment', 124, NULL, 4, '123', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-22 09:18:28', 'Dari verifikasi PO: Sesuai', '2025-08-22 09:18:28', '2025-08-22 09:18:28'),
(4, 'attachment', 130, NULL, 3, '123', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-22 09:19:00', 'Dari verifikasi PO: Sesuai', '2025-08-22 09:19:00', '2025-08-22 09:19:00'),
(5, 'battery', 143, 1, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 3, '2025-08-22 09:23:14', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-22 09:23:14', '2025-09-04 14:48:00'),
(6, 'charger', 143, 16, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 8, '2025-08-22 09:23:14', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-22 09:23:14', '2025-08-27 23:53:23'),
(7, 'battery', 143, 2, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 3, '2025-08-27 04:15:34', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:34', '2025-09-04 14:48:00'),
(8, 'charger', 143, 17, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 8, '2025-08-27 04:15:34', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:34', '2025-08-30 02:00:18'),
(9, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:43', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:43', '2025-08-27 11:41:47'),
(10, 'charger', 143, 1, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 3, '2025-08-27 04:15:43', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:43', '2025-09-04 14:48:00'),
(11, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:51', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:51', '2025-08-27 11:41:47'),
(12, 'charger', 143, 12, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 8, '2025-08-27 04:15:51', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:51', '2025-09-03 09:41:03'),
(13, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:58', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:58', '2025-08-27 11:41:47'),
(14, 'charger', 143, 2, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 3, '2025-08-27 04:15:58', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:58', '2025-09-04 14:48:00'),
(15, 'attachment', 131, NULL, 3, 'ok', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:50:06', 'Dari verifikasi PO: Sesuai', '2025-08-27 04:50:06', '2025-08-27 04:50:06'),
(16, 'attachment', 139, NULL, 3, 'a', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-28 09:32:49', 'Dari verifikasi PO: Sesuai', '2025-08-28 09:32:49', '2025-08-28 09:32:49'),
(17, 'battery', 38, 4, 2, NULL, 2, 'test', 6, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-12 04:47:28', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-12 04:47:28', '2025-08-19 11:38:34'),
(18, 'battery', 38, 5, 2, NULL, 2, 'test2', 6, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-12 04:49:52', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-12 04:49:52', '2025-08-19 11:38:34'),
(19, 'battery', 38, 6, 2, NULL, 2, 'test3', 6, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-12 04:50:15', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-12 04:50:15', '2025-08-21 15:23:38'),
(20, 'battery', 38, 7, 3, '', 2, 'test4', 6, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-12 04:54:09', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-12 04:54:09', '2025-08-16 01:23:28'),
(21, 'battery', 39, 8, NULL, NULL, 6, 'andara', NULL, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-12 08:15:40', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-12 08:15:40', '2025-08-26 16:48:09'),
(22, 'battery', 38, 9, 5, 'wae', 2, 'adaaaaa', 6, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-12 08:21:22', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-12 08:21:22', '2025-08-21 15:24:29'),
(23, 'battery', 38, 10, 2, NULL, 2, 'adit', 6, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-16 04:20:16', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-16 04:20:16', '2025-08-18 09:33:12'),
(24, 'battery', 47, 11, NULL, NULL, 3, 'adit', NULL, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-16 04:20:38', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-16 04:20:38', '2025-08-21 15:24:56'),
(25, 'battery', 39, 12, NULL, NULL, 6, 'adit', NULL, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-16 04:24:32', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-16 04:24:32', '2025-08-16 21:59:26'),
(26, 'battery', 38, 13, 2, NULL, 2, 'adit', 6, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-16 04:26:43', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-16 04:26:43', '2025-08-27 22:34:17'),
(27, 'battery', 39, 14, NULL, NULL, 6, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-27 02:22:59', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-27 02:22:59', '2025-08-27 13:31:31'),
(28, 'battery', 39, 15, NULL, NULL, 6, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-27 02:23:14', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-27 02:23:14', '2025-08-27 13:51:14'),
(29, 'battery', 52, 16, NULL, NULL, 3, '111', 6, '123', 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-27 15:35:47', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-27 15:35:47', '2025-08-27 23:53:23'),
(30, 'battery', 52, 17, 3, 'ok', 3, '222', 8, '123', 'Baik', 'Lengkap', NULL, NULL, 7, '2025-08-27 15:36:17', 'Migrated from inventory_unit on 2025-08-30 03:37:37', '2025-08-27 15:36:17', '2025-08-30 02:53:07');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item_unit_log`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `inventory_item_unit_log`;
CREATE TABLE IF NOT EXISTS `inventory_item_unit_log` (
  `id` int(11) NOT NULL,
  `id_inventory_attachment` int(11) NOT NULL,
  `id_inventory_unit` int(11) NOT NULL,
  `action` enum('assign','remove') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `inventory_item_unit_log`:
--

--
-- Truncate table before insert `inventory_item_unit_log`
--

TRUNCATE TABLE `inventory_item_unit_log`;
-- --------------------------------------------------------

--
-- Table structure for table `inventory_spareparts`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `inventory_spareparts`;
CREATE TABLE IF NOT EXISTS `inventory_spareparts` (
  `id` int(11) NOT NULL,
  `sparepart_id` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `lokasi_rak` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `inventory_spareparts`:
--

--
-- Truncate table before insert `inventory_spareparts`
--

TRUNCATE TABLE `inventory_spareparts`;
--
-- Dumping data for table `inventory_spareparts`
--

INSERT DELAYED IGNORE INTO `inventory_spareparts` (`id`, `sparepart_id`, `stok`, `lokasi_rak`, `updated_at`) VALUES
(1, 17, 1, 'POS 1', '2025-07-25 09:01:22'),
(2, 1, 1, 'POS 1', '2025-08-12 03:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_unit`
--
-- Creation: Sep 08, 2025 at 06:28 AM
--

DROP TABLE IF EXISTS `inventory_unit`;
CREATE TABLE IF NOT EXISTS `inventory_unit` (
  `id_inventory_unit` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_unit` int(10) UNSIGNED DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL COMMENT 'Serial Number utama dari pabrikan',
  `id_po` int(11) DEFAULT NULL COMMENT 'Foreign Key ke tabel purchase_orders',
  `tahun_unit` year(4) DEFAULT NULL,
  `status_unit_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `status_aset` tinyint(1) DEFAULT NULL COMMENT 'Flag status aset (misal: 1=Aktif, 0=Non-Aktif)',
  `lokasi_unit` varchar(255) DEFAULT NULL,
  `departemen_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel departemen',
  `tanggal_kirim` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `harga_sewa_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per bulan',
  `harga_sewa_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per hari',
  `kontrak_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Foreign key ke tabel kontrak',
  `kontrak_spesifikasi_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi untuk tracking spek mana',
  `tipe_unit_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel tipe_unit',
  `model_unit_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel model_unit (sudah termasuk merk)',
  `kapasitas_unit_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel kapasitas',
  `model_mast_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel tipe_mast',
  `tinggi_mast` varchar(50) DEFAULT NULL COMMENT 'Contoh: 4500mm atau 4.5m',
  `sn_mast` varchar(255) DEFAULT NULL,
  `model_mesin_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel mesin (sudah termasuk merk)',
  `sn_mesin` varchar(255) DEFAULT NULL,
  `roda_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel jenis_roda',
  `ban_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel tipe_ban',
  `valve_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel valve',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_inventory_unit`),
  KEY `fk_inventory_unit_status` (`status_unit_id`),
  KEY `fk_inventory_unit_departemen` (`departemen_id`),
  KEY `fk_inventory_unit_kontrak` (`kontrak_id`),
  KEY `fk_inventory_unit_kontrak_spesifikasi` (`kontrak_spesifikasi_id`),
  KEY `fk_inventory_unit_tipe` (`tipe_unit_id`),
  KEY `fk_inventory_unit_model` (`model_unit_id`),
  KEY `fk_inventory_unit_kapasitas` (`kapasitas_unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Data unit utama - komponen disimpan di inventory_attachment';

--
-- RELATIONSHIPS FOR TABLE `inventory_unit`:
--   `departemen_id`
--       `departemen` -> `id_departemen`
--   `kapasitas_unit_id`
--       `kapasitas` -> `id_kapasitas`
--   `kontrak_id`
--       `kontrak` -> `id`
--   `kontrak_spesifikasi_id`
--       `kontrak_spesifikasi` -> `id`
--   `model_unit_id`
--       `model_unit` -> `id_model_unit`
--   `status_unit_id`
--       `status_unit` -> `id_status`
--   `tipe_unit_id`
--       `tipe_unit` -> `id_tipe_unit`
--

--
-- Truncate table before insert `inventory_unit`
--

TRUNCATE TABLE `inventory_unit`;
--
-- Dumping data for table `inventory_unit`
--

INSERT DELAYED IGNORE INTO `inventory_unit` (`id_inventory_unit`, `no_unit`, `serial_number`, `id_po`, `tahun_unit`, `status_unit_id`, `status_aset`, `lokasi_unit`, `departemen_id`, `tanggal_kirim`, `keterangan`, `harga_sewa_bulanan`, `harga_sewa_harian`, `kontrak_id`, `kontrak_spesifikasi_id`, `tipe_unit_id`, `model_unit_id`, `kapasitas_unit_id`, `model_mast_id`, `tinggi_mast`, `sn_mast`, `model_mesin_id`, `sn_mesin`, `roda_id`, `ban_id`, `valve_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'unit 1', 122, '2025', 3, 0, 'Warehouse', 2, NULL, '', 9000000.00, NULL, 44, 19, 9, 6, 2, 5, NULL, '', 4, '', 4, 3, 4, '2025-08-12 02:22:14', '2025-09-08 13:24:02'),
(2, 2, 'unit 2', 123, '2025', 3, 0, 'Warehouse', 2, NULL, '', 9000000.00, NULL, 44, 19, 10, 3, 10, 2, NULL, '', 2, '', 2, 2, 3, '2025-08-12 03:15:49', '2025-09-08 13:24:02'),
(4, 4, 'test', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 12, 6, 6, 2, NULL, 'test', 2, 'test', 2, 1, 3, '2025-08-12 04:47:28', '2025-08-30 03:42:03'),
(5, 7, 'test2', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 12, 6, 6, 2, NULL, 'test2', 2, 'test2', 2, 1, 3, '2025-08-12 04:49:52', '2025-08-30 03:42:03'),
(6, 8, 'test3', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 12, 6, 6, 2, NULL, 'test3', 2, 'test3', 2, 1, 3, '2025-08-12 04:50:15', '2025-08-30 03:42:03'),
(7, 3, 'test4', 38, '2025', 7, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 12, 6, 6, 2, NULL, 'test4', 2, 'test4', 2, 1, 3, '2025-08-12 04:54:09', '2025-08-16 01:23:28'),
(8, 11, 'andara', 39, '2025', 8, NULL, 'POS 1', 2, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 30, 1, NULL, 'andara', 3, 'andara', 1, 3, 1, '2025-08-12 08:15:40', '2025-08-30 03:42:03'),
(9, 9, 'adaaaaa', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 12, 6, 6, 2, NULL, 'adaaaaa', 2, 'adaaaaa', 2, 1, 3, '2025-08-12 08:21:22', '2025-08-30 03:42:03'),
(10, 6, 'adit', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 12, 6, 6, 2, NULL, 'adit', 2, 'adit', 2, 1, 3, '2025-08-16 04:20:16', '2025-08-30 03:42:03'),
(11, 10, 'adit', 47, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 3, 6, 4, 5, NULL, 'adit', 3, 'adit', 2, 3, 2, '2025-08-16 04:20:38', '2025-08-30 03:42:03'),
(12, 5, 'adit', 39, '2025', 8, NULL, 'POS 1', 2, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 3, 1, NULL, 'adit', 3, 'adit', 1, 3, 1, '2025-08-16 04:24:32', '2025-08-30 03:42:03'),
(13, 14, 'kkai', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, NULL, NULL, 12, 6, 6, 2, NULL, 'adit', 2, 'adit', 2, 1, 3, '2025-08-16 04:26:43', '2025-08-30 03:42:03'),
(14, 12, '123', 39, '2025', 8, NULL, 'POS 1', 2, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 3, 1, NULL, '123', 3, '123', 1, 3, 1, '2025-08-27 02:22:59', '2025-08-30 03:42:03'),
(15, 13, '123', 39, '2025', 8, NULL, 'POS 1', 2, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 3, 1, NULL, '123', 3, '123', 1, 3, 1, '2025-08-27 02:23:14', '2025-08-30 03:42:03'),
(16, 15, '111', 52, '2025', 8, NULL, 'POS 1', 2, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 5, 3, NULL, '111', 3, '111', 1, 2, 2, '2025-08-27 15:35:47', '2025-08-30 03:42:03'),
(17, 16, '222', 52, '2025', 7, NULL, 'POS 1', 2, NULL, NULL, NULL, NULL, NULL, NULL, 2, 1, 5, 3, NULL, '222', 3, '222', 1, 2, 2, '2025-08-27 15:36:17', '2025-08-30 02:53:07');

--
-- Triggers `inventory_unit`
--
DROP TRIGGER IF EXISTS `update_kontrak_totals_after_unit_insert`;
DELIMITER $$
CREATE TRIGGER `update_kontrak_totals_after_unit_insert` AFTER INSERT ON `inventory_unit` FOR EACH ROW BEGIN
    IF NEW.kontrak_id IS NOT NULL THEN
        UPDATE kontrak 
        SET total_units = (
            SELECT COUNT(*) 
            FROM inventory_unit 
            WHERE kontrak_id = NEW.kontrak_id
        )
        WHERE id = NEW.kontrak_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_unit_backup`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `inventory_unit_backup`;
CREATE TABLE IF NOT EXISTS `inventory_unit_backup` (
  `id_inventory_unit` int(10) UNSIGNED NOT NULL,
  `model_baterai_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel baterai (sudah termasuk jenis, merk)',
  `sn_baterai` varchar(255) DEFAULT NULL,
  `model_charger_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel charger (sudah termasuk merk)',
  `sn_charger` varchar(255) DEFAULT NULL,
  `model_attachment_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel attachment (sudah termasuk tipe, merk, model)',
  `sn_attachment` varchar(255) DEFAULT NULL,
  `status_unit_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `backup_timestamp` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_inventory_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `inventory_unit_backup`:
--

--
-- Truncate table before insert `inventory_unit_backup`
--

TRUNCATE TABLE `inventory_unit_backup`;
--
-- Dumping data for table `inventory_unit_backup`
--

INSERT DELAYED IGNORE INTO `inventory_unit_backup` (`id_inventory_unit`, `model_baterai_id`, `sn_baterai`, `model_charger_id`, `sn_charger`, `model_attachment_id`, `sn_attachment`, `status_unit_id`, `backup_timestamp`) VALUES
(4, 2, 'test', 6, NULL, 2, NULL, 3, '2025-08-19 11:38:34'),
(5, 2, 'test2', 6, NULL, 2, NULL, 3, '2025-08-19 11:38:34'),
(6, 2, 'test3', 6, NULL, 2, NULL, 3, '2025-08-21 15:23:38'),
(7, 2, 'test4', 6, NULL, 3, '', 7, '2025-08-16 01:23:28'),
(8, 6, 'andara', NULL, NULL, NULL, NULL, 3, '2025-08-26 16:48:09'),
(9, 2, 'adaaaaa', 6, NULL, 5, 'wae', 3, '2025-08-21 15:24:29'),
(10, 2, 'adit', 6, NULL, 2, NULL, 3, '2025-08-18 09:33:12'),
(11, 3, 'adit', NULL, NULL, NULL, NULL, 3, '2025-08-21 15:24:56'),
(12, 6, 'adit', NULL, NULL, NULL, NULL, 3, '2025-08-16 21:59:26'),
(13, 2, 'adit', 6, NULL, 2, NULL, 3, '2025-08-27 22:34:17'),
(14, 6, '123', NULL, NULL, NULL, NULL, 3, '2025-08-27 13:31:31'),
(15, 6, '123', NULL, NULL, NULL, NULL, 3, '2025-08-27 13:51:14'),
(16, 3, '111', 6, '123', NULL, NULL, 3, '2025-08-27 23:53:23'),
(17, 3, '222', 8, '123', 3, 'ok', 7, '2025-08-30 02:53:07');

-- --------------------------------------------------------

--
-- Stand-in structure for view `inventory_unit_components`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `inventory_unit_components`;
CREATE TABLE IF NOT EXISTS `inventory_unit_components` (
`id_inventory_unit` int(10) unsigned
,`no_unit` int(10) unsigned
,`serial_number` varchar(255)
,`model_baterai_id` int(11)
,`sn_baterai` varchar(100)
,`merk_baterai` varchar(100)
,`tipe_baterai` varchar(100)
,`jenis_baterai` varchar(50)
,`model_charger_id` int(11)
,`sn_charger` varchar(255)
,`merk_charger` varchar(100)
,`tipe_charger` varchar(100)
,`model_attachment_id` int(11)
,`sn_attachment` varchar(255)
,`attachment_tipe` varchar(100)
,`attachment_merk` varchar(100)
,`attachment_model` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `jenis_perintah_kerja`
--
-- Creation: Sep 03, 2025 at 08:58 AM
--

DROP TABLE IF EXISTS `jenis_perintah_kerja`;
CREATE TABLE IF NOT EXISTS `jenis_perintah_kerja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `jenis_perintah_kerja`:
--

--
-- Truncate table before insert `jenis_perintah_kerja`
--

TRUNCATE TABLE `jenis_perintah_kerja`;
--
-- Dumping data for table `jenis_perintah_kerja`
--

INSERT DELAYED IGNORE INTO `jenis_perintah_kerja` (`id`, `kode`, `nama`, `deskripsi`, `aktif`, `created_at`, `updated_at`) VALUES
(1, 'ANTAR', 'Antar Unit', 'Pengantaran unit ke lokasi pelanggan', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(2, 'TARIK', 'Tarik Unit', 'Penarikan unit dari lokasi pelanggan', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(3, 'TUKAR', 'Tukar Unit', 'Penukaran unit lama dengan unit baru', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(4, 'RELOKASI', 'Relokasi Unit', 'Pemindahan unit antar lokasi', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54');

-- --------------------------------------------------------

--
-- Table structure for table `jenis_roda`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `jenis_roda`;
CREATE TABLE IF NOT EXISTS `jenis_roda` (
  `id_roda` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_roda` varchar(100) NOT NULL,
  PRIMARY KEY (`id_roda`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `jenis_roda`:
--

--
-- Truncate table before insert `jenis_roda`
--

TRUNCATE TABLE `jenis_roda`;
--
-- Dumping data for table `jenis_roda`
--

INSERT DELAYED IGNORE INTO `jenis_roda` (`id_roda`, `tipe_roda`) VALUES
(1, '3-Wheel'),
(2, '4-Wheel'),
(3, '3-Way '),
(4, '4-Way Multi-Directional (FFL)');

-- --------------------------------------------------------

--
-- Table structure for table `kapasitas`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `kapasitas`;
CREATE TABLE IF NOT EXISTS `kapasitas` (
  `id_kapasitas` int(11) NOT NULL AUTO_INCREMENT,
  `kapasitas_unit` varchar(50) NOT NULL,
  PRIMARY KEY (`id_kapasitas`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `kapasitas`:
--

--
-- Truncate table before insert `kapasitas`
--

TRUNCATE TABLE `kapasitas`;
--
-- Dumping data for table `kapasitas`
--

INSERT DELAYED IGNORE INTO `kapasitas` (`id_kapasitas`, `kapasitas_unit`) VALUES
(1, '200 kg'),
(2, '300 kg'),
(3, '390 kg'),
(4, '430 kg'),
(5, '500 kg'),
(6, '600 kg'),
(7, '800 kg'),
(8, '1 Ton'),
(9, '1,2 Ton'),
(10, '1,25 Ton'),
(11, '1,3 Ton'),
(12, '1,35 Ton'),
(13, '1,4 Ton'),
(14, '1,5 Ton'),
(15, '1,6 Ton'),
(16, '1,7 Ton'),
(17, '1,75 Ton'),
(18, '1,8 Ton'),
(19, '2 Ton'),
(20, '2,1 Ton'),
(21, '2,2 Ton'),
(22, '2,4 Ton'),
(23, '2,5 Ton'),
(24, '2,7 Ton'),
(25, '3 Ton'),
(26, '3,3 Ton'),
(27, '3,5 Ton'),
(28, '3,8 Ton'),
(29, '4 Ton'),
(30, '4,5 Ton'),
(31, '5 Ton'),
(32, '5,5 Ton'),
(33, '6 Ton'),
(34, '7 Ton'),
(35, '8 Ton'),
(36, '9 Ton'),
(37, '10 Ton'),
(38, '11 Ton'),
(39, '12 Ton'),
(40, '13 Ton'),
(41, '14 Ton'),
(42, '15 Ton'),
(43, '16 Ton'),
(44, '18 Ton'),
(45, '20 Ton'),
(46, '23 Ton'),
(47, '25 Ton'),
(48, '30 Ton'),
(49, '32 Ton'),
(50, '35 Ton'),
(51, '40 Ton'),
(52, '45 Ton'),
(53, '48 Ton'),
(54, '50 Ton'),
(55, '55 Ton'),
(56, '60 Ton'),
(57, '65 Ton'),
(58, '70 Ton');

-- --------------------------------------------------------

--
-- Table structure for table `kontrak`
--
-- Creation: Sep 03, 2025 at 08:54 AM
-- Last update: Sep 09, 2025 at 09:54 AM
--

DROP TABLE IF EXISTS `kontrak`;
CREATE TABLE IF NOT EXISTS `kontrak` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `no_kontrak` varchar(100) NOT NULL,
  `no_po_marketing` varchar(100) DEFAULT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL COMMENT 'Nama Person In Charge',
  `kontak` varchar(100) DEFAULT NULL COMMENT 'Kontak PIC (telepon/email)',
  `nilai_total` decimal(15,2) DEFAULT NULL COMMENT 'Nilai total kontrak dalam rupiah',
  `total_units` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Total unit yang terkait dengan kontrak ini',
  `jenis_sewa` enum('BULANAN','HARIAN') DEFAULT 'BULANAN' COMMENT 'Jenis periode sewa',
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `status` enum('Aktif','Berakhir','Pending','Dibatalkan') NOT NULL DEFAULT 'Pending',
  `dibuat_oleh` int(10) UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `kontrak`:
--

--
-- Truncate table before insert `kontrak`
--

TRUNCATE TABLE `kontrak`;
--
-- Dumping data for table `kontrak`
--

INSERT DELAYED IGNORE INTO `kontrak` (`id`, `no_kontrak`, `no_po_marketing`, `pelanggan`, `lokasi`, `pic`, `kontak`, `nilai_total`, `total_units`, `jenis_sewa`, `tanggal_mulai`, `tanggal_berakhir`, `status`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`) VALUES
(44, 'MSI', 'MSI', 'MSI', 'EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT', 'MSI', '09213123123', 18000000.00, 2, 'BULANAN', '2025-09-01', '2025-09-01', 'Aktif', 1, '2025-09-01 01:54:45', '2025-09-08 03:46:01'),
(54, 'KNTRK/2209/0001', 'PO-ADIT10999', 'Sarana Mitra Luas', 'Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530', 'Adit', '082134555233', 0.00, 0, 'BULANAN', '2025-09-01', '2025-12-31', 'Pending', 1, '2025-09-09 09:54:01', '2025-09-09 09:54:01');

--
-- Triggers `kontrak`
--
DROP TRIGGER IF EXISTS `tr_kontrak_status_update`;
DELIMITER $$
CREATE TRIGGER `tr_kontrak_status_update` AFTER UPDATE ON `kontrak` FOR EACH ROW BEGIN
    
    
    IF NEW.status != OLD.status THEN
        INSERT INTO kontrak_status_changes (kontrak_id, old_status, new_status, changed_at)
        VALUES (NEW.id, OLD.status, NEW.status, NOW())
        ON DUPLICATE KEY UPDATE 
            old_status = OLD.status,
            new_status = NEW.status,
            changed_at = NOW();
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `kontrak_spesifikasi`
--
-- Creation: Sep 03, 2025 at 09:10 AM
--

DROP TABLE IF EXISTS `kontrak_spesifikasi`;
CREATE TABLE IF NOT EXISTS `kontrak_spesifikasi` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `kontrak_id` int(10) UNSIGNED NOT NULL,
  `spek_kode` varchar(50) NOT NULL COMMENT 'Kode unik spesifikasi dalam kontrak (A, B, C)',
  `jumlah_dibutuhkan` int(11) NOT NULL DEFAULT 1 COMMENT 'Jumlah unit yang dibutuhkan untuk spek ini',
  `jumlah_tersedia` int(11) NOT NULL DEFAULT 0 COMMENT 'Jumlah unit yang sudah di-assign',
  `harga_per_unit_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa bulanan per unit',
  `harga_per_unit_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa harian per unit',
  `catatan_spek` text DEFAULT NULL COMMENT 'Catatan khusus untuk spesifikasi ini',
  `departemen_id` int(11) DEFAULT NULL,
  `tipe_unit_id` int(11) DEFAULT NULL,
  `tipe_jenis` varchar(100) DEFAULT NULL,
  `kapasitas_id` int(11) DEFAULT NULL,
  `merk_unit` varchar(100) DEFAULT NULL,
  `model_unit` varchar(100) DEFAULT NULL,
  `attachment_tipe` varchar(100) DEFAULT NULL,
  `attachment_merk` varchar(100) DEFAULT NULL,
  `jenis_baterai` varchar(100) DEFAULT NULL,
  `charger_id` int(11) DEFAULT NULL,
  `mast_id` int(11) DEFAULT NULL,
  `ban_id` int(11) DEFAULT NULL,
  `roda_id` int(11) DEFAULT NULL,
  `valve_id` int(11) DEFAULT NULL,
  `aksesoris` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Array aksesoris yang dibutuhkan',
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_kontrak_spesifikasi_kontrak` (`kontrak_id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `kontrak_spesifikasi`:
--   `kontrak_id`
--       `kontrak` -> `id`
--

--
-- Truncate table before insert `kontrak_spesifikasi`
--

TRUNCATE TABLE `kontrak_spesifikasi`;
--
-- Dumping data for table `kontrak_spesifikasi`
--

INSERT DELAYED IGNORE INTO `kontrak_spesifikasi` (`id`, `kontrak_id`, `spek_kode`, `jumlah_dibutuhkan`, `jumlah_tersedia`, `harga_per_unit_bulanan`, `harga_per_unit_harian`, `catatan_spek`, `departemen_id`, `tipe_unit_id`, `tipe_jenis`, `kapasitas_id`, `merk_unit`, `model_unit`, `attachment_tipe`, `attachment_merk`, `jenis_baterai`, `charger_id`, `mast_id`, `ban_id`, `roda_id`, `valve_id`, `aksesoris`, `dibuat_pada`, `diperbarui_pada`) VALUES
(19, 44, 'SPEC-001', 2, 0, 9000000.00, NULL, '', 2, 6, 'HAND PALLET', 41, 'HELI', NULL, 'FORK POSITIONER', NULL, 'Lithium-ion', 5, 22, 6, 1, 2, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]', '2025-09-01 01:55:43', '2025-09-01 01:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `kontrak_status_changes`
--
-- Creation: Sep 04, 2025 at 07:47 AM
--

DROP TABLE IF EXISTS `kontrak_status_changes`;
CREATE TABLE IF NOT EXISTS `kontrak_status_changes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kontrak_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kontrak_id` (`kontrak_id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `kontrak_status_changes`:
--

--
-- Truncate table before insert `kontrak_status_changes`
--

TRUNCATE TABLE `kontrak_status_changes`;
--
-- Dumping data for table `kontrak_status_changes`
--

INSERT DELAYED IGNORE INTO `kontrak_status_changes` (`id`, `kontrak_id`, `old_status`, `new_status`, `changed_at`, `processed`) VALUES
(1, 44, 'Berakhir', 'Aktif', '2025-09-04 07:48:00', 0);

-- --------------------------------------------------------

--
-- Table structure for table `mesin`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `mesin`;
CREATE TABLE IF NOT EXISTS `mesin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merk_mesin` varchar(100) NOT NULL,
  `model_mesin` varchar(100) NOT NULL,
  `bahan_bakar` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `mesin`:
--

--
-- Truncate table before insert `mesin`
--

TRUNCATE TABLE `mesin`;
--
-- Dumping data for table `mesin`
--

INSERT DELAYED IGNORE INTO `mesin` (`id`, `merk_mesin`, `model_mesin`, `bahan_bakar`) VALUES
(1, 'TOYOTA', '1DZ-0196006', 'Diesel'),
(2, 'TOYOTA', '1DZ-0197191', 'Diesel'),
(3, 'TOYOTA', '1DZ-0218846', 'Diesel'),
(4, 'TOYOTA', '1DZ-0226001', 'Diesel'),
(5, 'TOYOTA', '1DZ-0226519', 'Diesel'),
(6, 'TOYOTA', '1DZ-0226619', 'Diesel'),
(7, 'TOYOTA', '1DZ-0227007', 'Diesel'),
(8, 'TOYOTA', '1DZ-0230507', 'Diesel'),
(9, 'TOYOTA', '1DZ-0238684', 'Diesel'),
(10, 'TOYOTA', '1DZ-0238791', 'Diesel'),
(11, 'TOYOTA', '1DZ-0239268', 'Diesel'),
(12, 'TOYOTA', '1DZ-0239357', 'Diesel'),
(13, 'TOYOTA', '1DZ-0239361', 'Diesel'),
(14, 'TOYOTA', '1DZ-0239365', 'Diesel'),
(15, 'TOYOTA', '1DZ-0244305', 'Diesel'),
(16, 'TOYOTA', '1DZ-0247102', 'Diesel'),
(17, 'TOYOTA', '1DZ-0247201', 'Diesel'),
(18, 'TOYOTA', '1DZ-0250918', 'Diesel'),
(19, 'TOYOTA', '1DZ-0251498', 'Diesel'),
(20, 'TOYOTA', '1DZ-0252270', 'Diesel'),
(21, 'TOYOTA', '1DZ-0252764', 'Diesel'),
(22, 'TOYOTA', '1DZ-0252821', 'Diesel'),
(23, 'TOYOTA', '1DZ-0252893', 'Diesel'),
(24, 'TOYOTA', '1DZ-0347648', 'Diesel'),
(25, 'TOYOTA', '1DZ-0356711', 'Diesel'),
(26, 'TOYOTA', '1DZ-0358669', 'Diesel'),
(27, 'TOYOTA', '1DZ-0358919', 'Diesel'),
(28, 'TOYOTA', '1DZ-0359781', 'Diesel'),
(29, 'TOYOTA', '1DZ-0360733', 'Diesel'),
(30, 'TOYOTA', '1DZ-0360739', 'Diesel'),
(31, 'TOYOTA', '1DZ-0360741', 'Diesel'),
(32, 'TOYOTA', '1DZ-0360852', 'Diesel'),
(33, 'TOYOTA', '1DZ-0360939', 'Diesel'),
(34, 'TOYOTA', '1DZ-0360952', 'Diesel'),
(35, 'TOYOTA', '1DZ-0360953', 'Diesel'),
(36, 'TOYOTA', '1DZ-0361046', 'Diesel'),
(37, 'TOYOTA', '1DZ-0361052', 'Diesel'),
(38, 'TOYOTA', '1DZ-0361053', 'Diesel'),
(39, 'TOYOTA', '1DZ-0361055', 'Diesel'),
(40, 'TOYOTA', '1DZ-0361058', 'Diesel'),
(41, 'TOYOTA', '1DZ-0361063', 'Diesel'),
(42, 'TOYOTA', '1DZ-0361065', 'Diesel'),
(43, 'TOYOTA', '1DZ-0361137', 'Diesel'),
(44, 'TOYOTA', '1DZ-0361153', 'Diesel'),
(45, 'TOYOTA', '1DZ-0361345', 'Diesel'),
(46, 'TOYOTA', '1DZ-0362712', 'Diesel'),
(47, 'TOYOTA', '1DZ-0364387', 'Diesel'),
(48, 'TOYOTA', '1DZ-0364398', 'Diesel'),
(49, 'TOYOTA', '1DZ-0364710', 'Diesel'),
(50, 'TOYOTA', '1DZ-0365850', 'Diesel'),
(51, 'TOYOTA', '1DZ-0370103', 'Diesel'),
(52, 'TOYOTA', '1DZ-0372317', 'Diesel'),
(53, 'TOYOTA', '1DZ-0372319', 'Diesel'),
(54, 'TOYOTA', '1DZ-0372355', 'Diesel'),
(55, 'TOYOTA', '1DZ-0372358', 'Diesel'),
(56, 'TOYOTA', '1DZ-0372476', 'Diesel'),
(57, 'TOYOTA', '1DZ-0372530', 'Diesel'),
(58, 'TOYOTA', '1DZ-0372561', 'Diesel'),
(59, 'TOYOTA', '1DZ-0372563', 'Diesel'),
(60, 'TOYOTA', '1DZ-0372608', 'Diesel'),
(61, 'TOYOTA', '1DZ-0372612', 'Diesel'),
(62, 'TOYOTA', '1DZ-0372632', 'Diesel'),
(63, 'TOYOTA', '1DZ-0372634', 'Diesel'),
(64, 'TOYOTA', '1DZ-0372659', 'Diesel'),
(65, 'TOYOTA', '1DZ-0400513', 'Diesel'),
(66, 'TOYOTA', '1DZ-0414691', 'Diesel'),
(67, 'TOYOTA', '1DZ-0414776', 'Diesel'),
(68, 'TOYOTA', '1DZ-0414785', 'Diesel'),
(69, 'TOYOTA', '1DZ-0414786', 'Diesel'),
(70, 'TOYOTA', '1DZ-0415466', 'Diesel'),
(71, 'TOYOTA', '1DZ-0415779', 'Diesel'),
(72, 'TOYOTA', '1DZ-0415814', 'Diesel'),
(73, 'TOYOTA', '1DZ-0415816', 'Diesel'),
(74, 'TOYOTA', '1DZ-0415941', 'Diesel'),
(75, 'TOYOTA', '1DZ-0416059', 'Diesel'),
(76, 'TOYOTA', '1DZ-0416061', 'Diesel'),
(77, 'TOYOTA', '1DZ-0416087', 'Diesel'),
(78, 'TOYOTA', '1DZ-0416090', 'Diesel'),
(79, 'TOYOTA', '1DZ-0416241', 'Diesel'),
(80, 'TOYOTA', '1DZ-0416404', 'Diesel'),
(81, 'TOYOTA', '1DZ-0416894', 'Diesel'),
(82, 'TOYOTA', '1DZ-0416938', 'Diesel'),
(83, 'TOYOTA', '1DZ-0416943', 'Diesel'),
(84, 'TOYOTA', '1DZ-0416976', 'Diesel'),
(85, 'TOYOTA', '1DZ-0416980', 'Diesel'),
(86, 'TOYOTA', '1DZ-0417098', 'Diesel'),
(87, 'TOYOTA', '1DZ-0417218', 'Diesel'),
(88, 'TOYOTA', '1DZ-0417230', 'Diesel'),
(89, 'TOYOTA', '1DZ-0417607', 'Diesel'),
(90, 'TOYOTA', '1DZ-0417632', 'Diesel'),
(91, 'TOYOTA', '1DZ-0417662', 'Diesel'),
(92, 'TOYOTA', '1DZ-0417690', 'Diesel'),
(93, 'TOYOTA', '14Z-0014579', 'Diesel'),
(94, 'TOYOTA', '14Z-0014581', 'Diesel'),
(95, 'TOYOTA', '14Z-0015050', 'Diesel'),
(96, 'TOYOTA', '14Z-0015658', 'Diesel'),
(97, 'TOYOTA', '14Z-0015662', 'Diesel'),
(98, 'TOYOTA', '14Z-0015671', 'Diesel'),
(99, 'TOYOTA', '14Z-0015673', 'Diesel'),
(100, 'TOYOTA', '14Z-0015686', 'Diesel'),
(101, 'TOYOTA', '14Z-0015691', 'Diesel'),
(102, 'TOYOTA', '14Z-0015692', 'Diesel'),
(103, 'TOYOTA', '14Z-0028118', 'Diesel'),
(104, 'TOYOTA', '14Z-0028134', 'Diesel'),
(105, 'TOYOTA', '14Z-0028140', 'Diesel'),
(106, 'TOYOTA', '14Z-0028150', 'Diesel'),
(107, 'TOYOTA', '14Z-0028165', 'Diesel'),
(108, 'TOYOTA', '14Z-0028179', 'Diesel'),
(109, 'TOYOTA', '14Z-0028203', 'Diesel'),
(110, 'TOYOTA', '14Z-0028241', 'Diesel'),
(111, 'TOYOTA', '4Y-2351413', 'Bensin / LPG'),
(112, 'TOYOTA', '4Y-2355314', 'Bensin / LPG'),
(113, 'TOYOTA', '4Y-2355860', 'Bensin / LPG'),
(114, 'TOYOTA', '4Y-2356048', 'Bensin / LPG'),
(115, 'TOYOTA', '4Y-2356096', 'Bensin / LPG'),
(116, 'TOYOTA', '4Y-2366516', 'Bensin / LPG'),
(117, 'TOYOTA', '4Y-2369406', 'Bensin / LPG'),
(118, 'TOYOTA', '4Y-2371882', 'Bensin / LPG'),
(119, 'TOYOTA', '4Y-2372927', 'Bensin / LPG'),
(120, 'TOYOTA', '4Y-2373823', 'Bensin / LPG'),
(121, 'TOYOTA', '4Y-2376533', 'Bensin / LPG'),
(122, 'TOYOTA', '4Y-2379846', 'Bensin / LPG'),
(123, 'TOYOTA', '4Y-2380448', 'Bensin / LPG'),
(124, 'TOYOTA', '4Y-2386387 matik', 'Bensin / LPG'),
(125, 'TOYOTA', '4Y-2386473 matik', 'Bensin / LPG'),
(126, 'MITSUBISHI', 'S4S-3.331', 'Diesel'),
(127, 'MITSUBISHI', 'S4S-21497', 'Diesel'),
(128, 'MITSUBISHI', 'S4S-214563', 'Diesel'),
(129, 'MITSUBISHI', 'S4S-217084', 'Diesel'),
(130, 'MITSUBISHI', 'S4S-218625', 'Diesel'),
(131, 'MITSUBISHI', 'S4S-219720', 'Diesel'),
(132, 'MITSUBISHI', 'S4S-219725', 'Diesel'),
(133, 'MITSUBISHI', 'S4S-220849', 'Diesel'),
(134, 'MITSUBISHI', 'S4S-220851', 'Diesel'),
(135, 'MITSUBISHI', 'S4S-220936', 'Diesel'),
(136, 'MITSUBISHI', 'S4S-222774', 'Diesel'),
(137, 'MITSUBISHI', 'S4S-224384', 'Diesel'),
(138, 'MITSUBISHI', 'S4S-224766', 'Diesel'),
(139, 'MITSUBISHI', 'S4S-224839', 'Diesel'),
(140, 'MITSUBISHI', 'S4S-225323', 'Diesel'),
(141, 'MITSUBISHI', 'S4S-227-973', 'Diesel'),
(142, 'MITSUBISHI', 'S4S-228-487', 'Diesel'),
(143, 'MITSUBISHI', 'S4S-229-265', 'Diesel'),
(144, 'MITSUBISHI', 'S4S-231537', 'Diesel'),
(145, 'MITSUBISHI', 'S4S-232202', 'Diesel'),
(146, 'MITSUBISHI', 'S4S-232672', 'Diesel'),
(147, 'MITSUBISHI', 'S4S-232878', 'Diesel'),
(148, 'MITSUBISHI', 'S4S-234502', 'Diesel'),
(149, 'MITSUBISHI', 'S4S-234503', 'Diesel'),
(150, 'MITSUBISHI', 'S4S-234505', 'Diesel'),
(151, 'MITSUBISHI', 'S4S-234506', 'Diesel'),
(152, 'MITSUBISHI', 'S4S-234507', 'Diesel'),
(153, 'MITSUBISHI', 'S4S-237854', 'Diesel'),
(154, 'MITSUBISHI', 'S4S-240392', 'Diesel'),
(155, 'MITSUBISHI', 'S4S-240574', 'Diesel'),
(156, 'MITSUBISHI', 'S4S-242543', 'Diesel'),
(157, 'MITSUBISHI', 'S4S-242544', 'Diesel'),
(158, 'MITSUBISHI', 'S4S-242550', 'Diesel'),
(159, 'MITSUBISHI', 'S4S-242750', 'Diesel'),
(160, 'MITSUBISHI', 'S4S-243432', 'Diesel'),
(161, 'MITSUBISHI', 'S4S-243437', 'Diesel'),
(162, 'MITSUBISHI', 'S4S-243448', 'Diesel'),
(163, 'MITSUBISHI', 'S4S-243475', 'Diesel'),
(164, 'MITSUBISHI', 'S4S-243632', 'Diesel'),
(165, 'MITSUBISHI', 'S4S-243687', 'Diesel'),
(166, 'MITSUBISHI', 'S4S-243690', 'Diesel'),
(167, 'MITSUBISHI', 'S4S-243768', 'Diesel'),
(168, 'MITSUBISHI', 'S4S-243770', 'Diesel'),
(169, 'MITSUBISHI', 'S4S-243939', 'Diesel'),
(170, 'MITSUBISHI', 'S4S-244106', 'Diesel'),
(171, 'MITSUBISHI', 'S4S-244110', 'Diesel'),
(172, 'MITSUBISHI', 'S6S-5B1L', 'Diesel'),
(173, 'MITSUBISHI', 'S6S-040644', 'Diesel'),
(174, 'MITSUBISHI', 'S6S-072650', 'Diesel'),
(175, 'MITSUBISHI', 'S6S-082680', 'Diesel'),
(176, 'MITSUBISHI', 'S6S-082682', 'Diesel'),
(177, 'MITSUBISHI', 'S6S-082735', 'Diesel'),
(178, 'MITSUBISHI', 'S6S-082736', 'Diesel'),
(179, 'MITSUBISHI', 'S6S-083725', 'Diesel'),
(180, 'MITSUBISHI', 'S6S-083726', 'Diesel'),
(181, 'MITSUBISHI', 'S6S-083774', 'Diesel'),
(182, 'MITSUBISHI', 'S6S-088522', 'Diesel'),
(183, 'MITSUBISHI', 'S6S-088558', 'Diesel'),
(184, 'MITSUBISHI', 'S6S-089071', 'Diesel'),
(185, 'MITSUBISHI', 'S6S-KDN2V', 'Diesel'),
(186, 'YANMAR', '4TNE98-BQDFC', 'Diesel'),
(188, 'NISSAN', 'QD32', 'Diesel'),
(189, 'NISSAN', 'K21', 'Bensin / LPG'),
(190, 'NISSAN', 'K25-1608228Y', 'Bensin / LPG'),
(191, 'DOOSAN', 'DB58S', 'Diesel'),
(192, 'ISUZU', '6BG1', 'Diesel'),
(193, 'QUANCHAI', 'QC490GP', 'Diesel'),
(194, 'WEICHAI', 'WP10.380E32', 'Diesel');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `migrations`:
--

--
-- Truncate table before insert `migrations`
--

TRUNCATE TABLE `migrations`;
--
-- Dumping data for table `migrations`
--

INSERT DELAYED IGNORE INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(7, '2024-01-01-000001', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1751956548, 1),
(8, '2024-01-15-000001', 'App\\Database\\Migrations\\CreateForkliftTable', 'default', 'App', 1751956548, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration_log`
--
-- Creation: Sep 03, 2025 at 08:54 AM
--

DROP TABLE IF EXISTS `migration_log`;
CREATE TABLE IF NOT EXISTS `migration_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) NOT NULL,
  `executed_at` datetime DEFAULT current_timestamp(),
  `description` text DEFAULT NULL,
  `status` enum('SUCCESS','FAILED','ROLLBACK') DEFAULT 'SUCCESS',
  `error_message` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_migration_name` (`migration_name`),
  KEY `idx_executed_at` (`executed_at`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `migration_log`:
--

--
-- Truncate table before insert `migration_log`
--

TRUNCATE TABLE `migration_log`;
--
-- Dumping data for table `migration_log`
--

INSERT DELAYED IGNORE INTO `migration_log` (`id`, `migration_name`, `executed_at`, `description`, `status`, `error_message`) VALUES
(1, 'consolidate_components_to_inventory_attachment', '2025-08-30 03:42:03', 'Konsolidasi battery/charger/attachment ke inventory_attachment sebagai single source of truth', 'SUCCESS', NULL),
(2, 'workflow_implementation_20250903', '2025-09-03 15:58:54', 'Workflow implementation completed successfully', 'SUCCESS', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migration_log_di_workflow`
--
-- Creation: Sep 03, 2025 at 09:50 AM
--

DROP TABLE IF EXISTS `migration_log_di_workflow`;
CREATE TABLE IF NOT EXISTS `migration_log_di_workflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `status` enum('SUCCESS','ERROR','SKIPPED') DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `migration_log_di_workflow`:
--

--
-- Truncate table before insert `migration_log_di_workflow`
--

TRUNCATE TABLE `migration_log_di_workflow`;
--
-- Dumping data for table `migration_log_di_workflow`
--

INSERT DELAYED IGNORE INTO `migration_log_di_workflow` (`id`, `table_name`, `action`, `status`, `error_message`, `created_at`) VALUES
(1, 'delivery_instructions', 'Add jenis_perintah_kerja_id', 'SUCCESS', NULL, '2025-09-03 09:50:51'),
(2, 'delivery_instructions', 'Add tujuan_perintah_kerja_id', 'SUCCESS', NULL, '2025-09-03 09:50:51'),
(3, 'delivery_instructions', 'Add status_eksekusi_workflow_id', 'SUCCESS', NULL, '2025-09-03 09:50:51'),
(4, 'delivery_instructions', 'Add FK jenis_perintah_kerja', 'SUCCESS', NULL, '2025-09-03 09:50:51'),
(5, 'delivery_instructions', 'Add FK tujuan_perintah_kerja', 'SUCCESS', NULL, '2025-09-03 09:50:51'),
(6, 'delivery_instructions', 'Add FK status_eksekusi_workflow', 'SUCCESS', NULL, '2025-09-03 09:50:51'),
(7, 'delivery_instructions', 'Update existing records with default workflow values', 'SUCCESS', NULL, '2025-09-03 09:50:51');

-- --------------------------------------------------------

--
-- Table structure for table `model_unit`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `model_unit`;
CREATE TABLE IF NOT EXISTS `model_unit` (
  `id_model_unit` int(11) NOT NULL AUTO_INCREMENT,
  `merk_unit` varchar(100) NOT NULL,
  `model_unit` varchar(100) NOT NULL,
  PRIMARY KEY (`id_model_unit`)
) ENGINE=InnoDB AUTO_INCREMENT=556 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `model_unit`:
--

--
-- Truncate table before insert `model_unit`
--

TRUNCATE TABLE `model_unit`;
--
-- Dumping data for table `model_unit`
--

INSERT DELAYED IGNORE INTO `model_unit` (`id_model_unit`, `merk_unit`, `model_unit`) VALUES
(1, 'AVANT', 'M420MSDTT'),
(2, 'BT', 'RRE160MC'),
(3, 'BT', 'LPE200'),
(4, 'CAT', 'EP15TCA'),
(5, 'CAT', 'NRS18CA'),
(6, 'CAT', 'EP20CA'),
(7, 'CAT', 'DP25ND'),
(8, 'CAT', 'DP25NT'),
(9, 'CAT', 'DP25ND 25P30'),
(10, 'CAT', 'Hand Lift'),
(11, 'CAT', 'DP25ND 2SP30'),
(12, 'CAT', 'DP25NDC'),
(13, 'CAT', 'GP25ND'),
(14, 'CAT', 'DP25ND - 2SP30'),
(15, 'CAT', 'DP45N'),
(16, 'CAT', 'GP25NT'),
(17, 'CAT', 'DP25ND-C / 2SP30'),
(18, 'CAT', 'DP30ND'),
(19, 'CAT', 'DP30ND 25P30'),
(20, 'CAT', 'DP30ND 2SP30'),
(21, 'CAT', 'DP30ND - 2SP30'),
(22, 'CAT', 'GP30ND'),
(23, 'CAT', 'DP30ND - 3FP47'),
(24, 'CAT', 'DP30ND 2SP50'),
(25, 'CAT', 'GP35ND'),
(26, 'CAT', 'DP35ND'),
(27, 'CAT', 'DP40ND'),
(28, 'CAT', 'DP40KT'),
(29, 'CAT', 'DP40KD'),
(30, 'CAT', 'DP40KLT'),
(31, 'CAT', 'DP 40 KT'),
(32, 'CAT', 'DP40NT'),
(33, 'CAT', 'DP40N'),
(34, 'CAT', 'DP45N - 3FP43 - PS/PS'),
(35, 'CAT', 'DP50NT'),
(36, 'CAT', 'DP50N'),
(37, 'CAT', 'DP70T'),
(38, 'CLARK', '-'),
(39, 'CROWN', 'RD5725-30'),
(40, 'CROWN', 'RD5795S-30'),
(41, 'CROWN', 'RD5795 S-32TT366'),
(42, 'CROWN', 'RD5725-32'),
(43, 'CROWN', 'RDS795S-32TT442'),
(44, 'CROWN', 'RD5795S-32TT442'),
(45, 'CROWN', 'RDS5700'),
(46, 'CROWN', 'RD5795S-32'),
(47, 'CROWN', 'SC5240-40'),
(48, 'CROWN', 'RR5725-45'),
(49, 'CROWN', 'RDS7955-32TT240'),
(50, 'CROWN', 'RR5795S-45'),
(51, 'CROWN', 'RR57955-45'),
(52, 'CROWN', 'RMD6095-32'),
(53, 'CROWN', 'RMD6095S-32'),
(54, 'CROWN', 'PM 10'),
(55, 'CROWN', 'RT 09'),
(56, 'CROWN', 'RD5795S-32TT400'),
(57, 'CROWN', 'RD572532'),
(58, 'CROWN', 'RR5795S-45TT341'),
(59, 'CROWN', 'RMD 6095S-32TT505'),
(60, 'DOOSAN', 'D25G'),
(61, 'DOOSAN', 'D25G-3.210M'),
(62, 'DOOSAN', 'D25G-4.710M'),
(63, 'DOOSAN', 'D25G-3M'),
(64, 'DOOSAN', 'D25D-5.540MM'),
(65, 'DOOSAN', 'D25G-5.990MM'),
(66, 'DOOSAN', 'D25G-5.540M'),
(67, 'DOOSAN', 'G25G-5.990M'),
(68, 'DOOSAN', 'D30G'),
(69, 'DOOSAN', 'D30G-3M'),
(70, 'DOOSAN', 'D30G-4.710M'),
(71, 'DOOSAN', 'D30G-5990M'),
(72, 'DOOSAN', 'D30G-5990MM'),
(73, 'DOOSAN', 'D50C-5'),
(74, 'DOOSAN', 'D50C-5-3.050M'),
(75, 'DOOSAN', 'D50C-5-4.000M'),
(76, 'DOOSAN', 'D50C-5-5.925M'),
(77, 'DOOSAN', 'D50C-5-4.125M'),
(78, 'DOOSAN', 'D50C-5-5925M'),
(79, 'DOOSAN', 'D50C5-5.925M'),
(80, 'DOOSAN', 'D70S-5'),
(81, 'DOOSAN', 'D70S-5-3M'),
(82, 'DOOSAN', 'D70C-5-3.000M'),
(83, 'DOOSAN', 'D70S-3M'),
(84, 'DOOSAN', 'D70S-5.5M'),
(85, 'DOOSAN', 'D70S-5.6.000M'),
(86, 'DOOSAN', 'D90S-5-3.300M'),
(87, 'DOOSAN', 'D90S-5.300MM'),
(88, 'DOOSAN', 'D160S-5'),
(89, 'DOOSAN', 'D160S-5-3000M'),
(90, 'DOOSAN', 'D160S-5.300M'),
(91, 'DOOSAN', 'D160S-4'),
(92, 'DOOSAN', 'DV250S-7/dv300S-7'),
(93, 'DYNAPAC', 'CC1250'),
(94, 'ENSIGN', 'YX635'),
(95, 'ENSIGN', 'YX646'),
(96, 'EP', 'ES12-12CS'),
(97, 'EP', 'RPL301'),
(98, 'EP', 'RPL251'),
(99, 'EP', 'RSC152'),
(100, 'EP', 'ES-16-16EX'),
(101, 'EP', 'KPL201'),
(102, 'EP', 'F4'),
(103, 'EP', 'EFL253S'),
(104, 'EP', 'CPC30T3'),
(105, 'EP', 'EFL303S'),
(106, 'EP', 'CPC35T3'),
(107, 'EP', 'EFL352'),
(108, 'EP', 'EFL353S'),
(109, 'EP', 'ES-10-10CX'),
(110, 'EP', 'ES10-12CX'),
(111, 'HAKO', 'D1200RH'),
(112, 'HAKO', 'SCM-B75R'),
(113, 'HANGCHA', 'CDD12-AC1S-P'),
(114, 'HANGCHA', 'CPDB12-AC1S'),
(115, 'HANGCHA', 'CPDB12-ACIS-I'),
(116, 'HANGCHA', 'CQDB16'),
(117, 'HANGCHA', 'CPDB16-ACIS-I'),
(118, 'HANGCHA', 'CDD20-AC1S'),
(119, 'HANGCHA', 'CPD25-AC3'),
(120, 'HANGCHA', 'CBD20'),
(121, 'HANGCHA', 'CPDB20-ACIS'),
(122, 'HANGCHA', 'CPCD25'),
(123, 'HANGCHA', 'CPD25'),
(124, 'HANGCHA', 'CQD25-AC2S-I'),
(125, 'HANGCHA', 'CQD25'),
(126, 'HANGCHA', 'CPD30'),
(127, 'HANGCHA', 'CBD30-ACIS-I'),
(128, 'HANGCHA', 'CPD50'),
(129, 'HANGCHA', 'CDD20-AC1S-P'),
(130, 'HANGCHA', 'CDD12-AC18-P'),
(131, 'HELI', 'CPD15-GB3LI-M'),
(132, 'HELI', 'CPD15-GB2LI-M'),
(133, 'HELI', 'CBD15J-LI-S'),
(134, 'HELI', 'CPD15-JRD'),
(135, 'HELI', 'CQD16-GB2SZLI'),
(136, 'HELI', 'CQD16-GB2SLLI'),
(137, 'HELI', 'CQD18-A2RLIG2'),
(138, 'HELI', 'CQD20'),
(139, 'HELI', 'CQD20-GC2RLI'),
(140, 'HELI', 'CBD20J-RLI'),
(141, 'HELI', 'CDD20-D930'),
(142, 'HELI', 'CBD20-J1R'),
(143, 'HELI', 'CQD20-GB2SHDLI'),
(144, 'HELI', 'CDD20-950'),
(145, 'HELI', 'CPD20-GB6LI-S'),
(146, 'HELI', 'CPD20-JRD'),
(147, 'HELI', 'CPD20SQ-A2LIG3-M'),
(148, 'HELI', 'CPD25'),
(149, 'HELI', 'CBD15J'),
(150, 'HELI', 'CPD25-GB3LI-M'),
(151, 'HELI', 'CPCD25-M1K2'),
(152, 'HELI', 'CPD25-GB2LI-M'),
(153, 'HELI', 'CPD25-GB6LI-S'),
(154, 'HELI', 'CPD25FB-HA2HLIB3'),
(155, 'HELI', 'CPCD50-M4G3'),
(156, 'HELI', 'CPD30'),
(157, 'HELI', 'CBD30'),
(158, 'HELI', 'CBD30J'),
(159, 'HELI', 'CPCD30-Q22K2'),
(160, 'HELI', 'CPCD30-M1K2'),
(161, 'HELI', 'CBD30J-RLI'),
(162, 'HELI', 'CPC30-WS1K2'),
(163, 'HELI', 'CPD30-GB2LI-M'),
(164, 'HELI', 'CPD30-GB6LI-S'),
(165, 'HELI', 'CBD30-460'),
(166, 'HELI', 'CPD35'),
(167, 'HELI', 'CPCD35-M1K2'),
(168, 'HELI', 'CPCD35-Q22K2'),
(169, 'HELI', 'CPD35-GB2LI-M'),
(170, 'HELI', 'CPD35-GB6LI-S'),
(171, 'HELI', 'CPD38'),
(172, 'HELI', 'CPD38-GB2LI-M'),
(173, 'HELI', 'QYD40S'),
(174, 'HELI', 'QYD40S-JE3G2LI'),
(175, 'HELI', 'CPCD50-M4K2'),
(176, 'HELI', 'CPCD50'),
(177, 'HELI', 'CPCD55-M4G3'),
(178, 'HELI', 'CPD50-GB3LI'),
(179, 'HELI', 'CPD50-G2A11LI'),
(180, 'HELI', 'CPD50-GB2LI'),
(181, 'HELI', 'CPCD70-W2K2'),
(182, 'HELI', 'CPCD100-W2K2'),
(183, 'HELI', 'CPCD250-VZ2-12III'),
(184, 'HELI', 'CPCD120-CU1-06III'),
(185, 'HELI', 'CPCD150'),
(186, 'HELI', 'CPCD150-CU-06IIG'),
(187, 'HELI', 'CPD20-GE6LI-S'),
(188, 'HELI', 'CPCD25-Q22K2'),
(189, 'HELI', 'CPD25-BG6LI-S'),
(190, 'HELI', 'CQDM20J-LI'),
(191, 'HELI', 'CPD20-GB2LI-M'),
(192, 'HELI', 'CPD40-GB2LI'),
(193, 'HELI', 'CPCD40-M4G3'),
(194, 'HELI', 'CPCD160-CU-06IIG'),
(195, 'HELI', '01F-231103'),
(196, 'HELI', 'PALETE MOVER'),
(197, 'HYSTER', '-'),
(198, 'HYSTER', 'H3.0 TX-98'),
(199, 'HYUNDAI', '18BR-9'),
(200, 'HYUNDAI', '25D-7SA'),
(201, 'HYUNDAI', '25DT-7'),
(202, 'HYUNDAI', '25BR-9'),
(203, 'HYUNDAI', '25B-9F'),
(204, 'HYUNDAI', '20BR-9'),
(205, 'HYUNDAI', '30G-7M'),
(206, 'HYUNDAI', '33DT-7'),
(207, 'HYUNDAI', '30D-7SA'),
(208, 'HYUNDAI', '30DT-F'),
(209, 'HYUNDAI', '30B-9F'),
(210, 'HYUNDAI', '35D-7SA'),
(211, 'HYUNDAI', '40T-9'),
(212, 'HYUNDAI', '50B-9'),
(213, 'HYUNDAI', '50D-9SA'),
(214, 'HYUNDAI', '70DT-7'),
(215, 'JUNGHEINRICH', 'ERE120-1150-6700'),
(216, 'JUNGHEINRICH', 'ERE120-1150-5400'),
(217, 'JUNGHEINRICH', 'ETV116n-1150-9020DZ'),
(218, 'JUNGHEINRICH', 'ERE120'),
(219, 'JUNGHEINRICH', 'ETV214-1150-8300DZ'),
(220, 'JUNGHEINRICH', 'ETV214-1150-8420DZ'),
(221, 'JUNGHEINRICH', 'EFGMC325-GTE115-470DZ'),
(222, 'JUNGHEINRICH', 'EFGMC325-1150-4700DZ'),
(223, 'JUNGHEINRICH', 'ETVMB216-1150-9020DZ'),
(224, 'JUNGHEINRICH', 'ETV116-115-9020DZ'),
(225, 'JUNGHEINRICH', 'EVT116N-1150-9020DZ'),
(226, 'JUNGHEINRICH', 'ETVN116N-1150-9020DZ'),
(227, 'JUNGHEINRICH', 'ETV 116n-115-9020 DZ'),
(228, 'JUNGHEINRICH', 'ETV 116'),
(229, 'JUNGHEINRICH', 'ETVMC320-1150-10520DZ'),
(230, 'JUNGHEINRICH', 'ETV120n-1150-11510DZ'),
(231, 'JUNGHEINRICH', 'ETV120n-1150-10520DZ'),
(232, 'JUNGHEINRICH', 'ETV MB216'),
(233, 'JUNGHEINRICH', 'ETV MC320 GNE'),
(234, 'JUNGHEINRICH', 'ETV MB216 GE'),
(235, 'JUNGHEINRICH', 'ETV MC320'),
(236, 'KOMATSU', 'FB17RJX-1'),
(237, 'KOMATSU', 'FBR7RJX-1'),
(238, 'KOMATSU', 'FD-25C-11'),
(239, 'KOMATSU', 'FD25C-14'),
(240, 'KOMATSU', 'DP25ND'),
(241, 'KOMATSU', 'DP25NT'),
(242, 'KOMATSU', 'FD25C-12'),
(243, 'KOMATSU', 'FG25C-14'),
(244, 'KOMATSU', 'FD25C 14 DIESEL'),
(245, 'KOMATSU', '541009'),
(246, 'KOMATSU', '568238'),
(247, 'KOMATSU', '568291'),
(248, 'KOMATSU', 'FD30C-12'),
(249, 'KOMATSU', 'FD30JT-12'),
(250, 'KOMATSU', 'FB30X-1'),
(251, 'KOMATSU', '540495'),
(252, 'KOMATSU', 'WA350-3'),
(253, 'KOMATSU', 'FD50T-P'),
(254, 'KOMATSU', 'FD60T-1'),
(255, 'LINDE', 'R14'),
(256, 'LINDE', 'E 15 C'),
(257, 'LINDE', 'E 18 P'),
(258, 'LINDE', 'R1-6N'),
(259, 'LOGITRANS', 'Palet stacker + charger'),
(260, 'MHE DEMAG', 'MPR25AC-685'),
(261, 'MITSUBISHI', 'FD20-2SP40'),
(262, 'MITSUBISHI', 'DP20ND'),
(263, 'MITSUBISHI', 'DP25ND'),
(264, 'MITSUBISHI', 'FG25NT-3FP47-PS/PS'),
(265, 'MITSUBISHI', 'FG25NT-2SP30-PS/PS'),
(266, 'MITSUBISHI', 'FG25NT-2SP40-PS/PS'),
(267, 'MITSUBISHI', 'FG25ND - 3FP47 - PS/ PS'),
(268, 'MITSUBISHI', 'FG25ND - 2SP40 - PS/PS'),
(269, 'MITSUBISHI', 'FG25NT'),
(270, 'MITSUBISHI', 'FG30ND'),
(271, 'MITSUBISHI', 'FD40N-2SP50-P5/PS'),
(272, 'MITSUBISHI', 'FD40N-3F43-PS/PS'),
(273, 'MITSUBISHI', 'FD40N'),
(274, 'MITSUBISHI', 'FD40NT'),
(275, 'MITSUBISHI', 'FD50T'),
(276, 'MITSUBISHI', 'FD50KT'),
(277, 'MITSUBISHI', 'FD 50 DIESEL KT'),
(278, 'MITSUBISHI', 'FD150SNL'),
(279, 'MITSUBISHI', 'FD50NT'),
(280, 'MITSUBISHI', 'FD70NH'),
(281, 'MYMAX', 'ZL-946B'),
(282, 'NICHIYU', 'FBRM15-75-400'),
(283, 'NICHIYU', 'FBRW15-75C-500M'),
(284, 'NICHIYU', 'FBT115PN-75C-470M'),
(285, 'NICHIYU', 'FBT15PN-75C-470M'),
(286, 'NICHIYU', 'FBRW15-75C-600M'),
(287, 'NICHIYU', 'FBRW18-75C-700MSF'),
(288, 'NICHIYU', 'FBR A W18-50SB-600MWB'),
(289, 'NICHIYU', 'FBRA18-63B-400CS'),
(290, 'NICHIYU', 'FBRW18-75C-700MSF(600M)'),
(291, 'NICHIYU', 'FB20PN-75C-470M'),
(292, 'NICHIYU', 'FB20PN-72C-470M'),
(293, 'NICHIYU', 'FB20PN-72C-300'),
(294, 'NICHIYU', 'PLDP20-70-A12'),
(295, 'NICHIYU', 'FB25PN-72C-300'),
(296, 'NICHIYU', 'FB25PN-72C-470M'),
(297, 'NICHIYU', 'FB25NP-72C'),
(298, 'NICHIYU', 'FB25PN-72C-300PFL'),
(299, 'NICHIYU', 'FB25PN-72C-470PFL'),
(300, 'NICHIYU', 'FBR15-75C-500M'),
(301, 'NICHIYU', 'FB25PN-72C-300M'),
(302, 'NICHIYU', 'FB25P-75C-300'),
(303, 'NICHIYU', 'FB25PN-75C-300M'),
(304, 'NICHIYU', 'FB25PN-72C-400'),
(305, 'NICHIYU', 'FB25PN-72C-550M'),
(306, 'NICHIYU', 'FB25PN-72C-600M'),
(307, 'NICHIYU', 'FB30PN-72C-300'),
(308, 'NICHIYU', 'FB30PN-72C-300M'),
(309, 'NICHIYU', 'FB30PN-75C-300'),
(310, 'NICHIYU', 'FB30P-75C-300'),
(311, 'NICHIYU', 'FB30PN-72C-430M'),
(312, 'NICHIYU', 'FBD10-700C-350'),
(313, 'NICHIYU', 'FBD10-700C-250'),
(314, 'NISSAN', 'Y1F2M25U-2W300'),
(315, 'NISSAN', 'Y1F2M25U-003350'),
(316, 'NISSAN', 'Y1F2M25U-003351'),
(317, 'NISSAN', 'T1B2L25U-2W300'),
(318, 'NISSAN', 'T1B2L25U-3F430'),
(319, 'NISSAN', 'D1F5F40U-VM400'),
(320, 'NISSAN', 'D1F5F40U-VFH435'),
(321, 'NISSAN', 'D1F540U-VFH435'),
(322, 'NISSAN', 'D1F50F40U-VM500'),
(323, 'NISSAN', 'D1F5F40U-VFH600'),
(324, 'NISSAN', 'D1F5F40U-VM500'),
(325, 'NISSAN', 'D1F5F50U-VM300'),
(326, 'NISSAN', 'D1F5F50U-VM 300'),
(327, 'NISSAN', 'D1F5F50U-VM500'),
(328, 'NISSAN', '1F6F70U'),
(329, 'NISSAN', 'L1FG6F704/1F6F70U-VM300'),
(330, 'NISSAN', 'L1F6F70U-VM300'),
(331, 'NISSAN', 'L1F6F70U-VFH600'),
(332, 'NISSAN', '1F6F70U-VM300'),
(333, 'NISSAN', '1F6F70U-VFH600'),
(334, 'PATRIA', 'PFG 20T-2'),
(335, 'PATRIA', 'PFD-25LC-1'),
(336, 'PATRIA', '-'),
(337, 'PATRIA', 'FD30T-0'),
(338, 'PATRIA', 'FD35TA-2'),
(339, 'POWERLIFT', 'ES12-12CS'),
(340, 'REYMON', 'REYMON 762DR32TT'),
(341, 'REYMON', 'REYMON 7620'),
(342, 'SANY', 'SYZ324C-8W(R)'),
(343, 'SEM', 'SEM636D'),
(344, 'SEM', 'SEM655F'),
(345, 'SEM', 'SEM618D'),
(346, 'SEM', 'SEM636F'),
(347, 'SHANTUI', 'L-36-B5'),
(348, 'SHANTUI', 'L55-B5'),
(349, 'SHINKO', '6FBR15'),
(350, 'SOOSUNG', 'SWP 13'),
(351, 'SOOSUNG', 'SWR - 1500L'),
(352, 'SOOSUNG', 'SBR 20'),
(353, 'SOOSUNG', 'SWP 25'),
(354, 'SOOSUNG', 'SWP-2500'),
(355, 'SOOSUNG', 'SBF-25A'),
(356, 'SOOSUNG', 'SST-4000TWG'),
(357, 'SOOSUNG', 'SST - 4000'),
(358, 'SOOSUNG', 'SWC - 1000L'),
(359, 'SOOSUNG', 'SSL-2646'),
(360, 'SOOSUNG', 'SSL-3370'),
(361, 'STILL', 'EGV-S14'),
(362, 'STILL', 'FM-X14'),
(363, 'STILL', 'RX50-15'),
(364, 'STILL', 'RX 20 -15'),
(365, 'STILL', 'RX20-15'),
(366, 'STILL', 'VNA NXV'),
(367, 'STILL', 'EXP16'),
(368, 'STILL', 'FM-X17N'),
(369, 'STILL', 'R 20-20P'),
(370, 'STILL', 'EXU-20'),
(371, 'STILL', 'FM-X20N'),
(372, 'STILL', 'FM-X20'),
(373, 'STILL', 'LTX20'),
(374, 'STILL', 'EXU- S24'),
(375, 'STILL', 'FM-X25'),
(376, 'STILL', 'ECU 30'),
(377, 'STILL', 'RX60-40'),
(378, 'STILL', 'RX60-40/600'),
(379, 'STILL', 'RX60-50'),
(380, 'STILL', 'RX60-50/600'),
(381, 'STILL', 'RX60-50/LC500'),
(382, 'STILL', 'RX 60-50'),
(383, 'STILL', 'RX60-60'),
(384, 'STILL', 'GX-X'),
(385, 'STILL', 'FM-4W25'),
(386, 'STILL', 'EXU-S 24'),
(387, 'SUMITOMO', '61-FBRA15WX'),
(388, 'SUMITOMO', '8FB25PX'),
(389, 'SUMITOMO', 'A.899RO2300f'),
(390, 'TCM', 'FD25Z1'),
(391, 'TCM', 'FD25Z3'),
(392, 'TCM', 'FB 25-7'),
(393, 'TCM', 'FD25C-NOMA'),
(394, 'TCM', 'FD25C-6'),
(395, 'TCM', 'FD25T3CZ'),
(396, 'TCM', 'FD25T3CZ/FD30T3CZ'),
(397, 'TCM', 'FB25P-80C-3F470'),
(398, 'TCM', 'FBRW15-85C-600M'),
(399, 'TCM', 'FB25-9'),
(400, 'TCM', 'FD30C-6'),
(401, 'TCM', 'FD30T3CZ'),
(402, 'TCM', 'FD30T9'),
(403, 'TCM', 'FD40C9 VM3000'),
(404, 'TCM', 'FD40T9'),
(405, 'TCM', 'FD40T9/FD50T9'),
(406, 'TCM', 'FD40T9/(FD50T9)'),
(407, 'TCM', 'FD50T-3'),
(408, 'TCM', 'FD50T9'),
(409, 'TCM', 'FD50T9-VFHM700LF122FDT'),
(410, 'TCM', 'FD50Z8'),
(411, 'TCM', 'FD50T9B'),
(412, 'TCM', 'FD70Z8'),
(413, 'TCM', 'FD70Z8T'),
(414, 'TCM', 'FD70T9/FD80Z8(EDIT)'),
(415, 'TCM', 'FD70T9'),
(416, 'TCM', 'FD70Z8B'),
(417, 'TCM', 'FD100T'),
(418, 'TCM', 'FD 100Z-8'),
(419, 'TCM', 'FD100Z8'),
(420, 'TCM', 'FD150T'),
(421, 'TCM', 'FD150S-3'),
(422, 'TCM', 'FD150S-3B'),
(423, 'TCM', 'FD230-2'),
(424, 'TCM', 'SSL 711'),
(425, 'TM', 'TWT 150TOWING'),
(426, 'TM', 'NTT 150 TOWING'),
(427, 'TM', 'FBR 25-02 ELEC'),
(428, 'TM', 'FBR 25-03 ELEC'),
(429, 'TM', 'FBR 20 ELEC'),
(430, 'TM', 'TEC30XQ ELEC'),
(431, 'TM', 'NTT100 TOWING'),
(432, 'TOW MOTOR', 'NPP15E2'),
(433, 'TOYOTA', 'FBR 1.3'),
(434, 'TOYOTA', 'SPE140S'),
(435, 'TOYOTA', '60-8FD15'),
(436, 'TOYOTA', '7FBR15'),
(437, 'TOYOTA', '8FBE15'),
(438, 'TOYOTA', '8FBN15'),
(439, 'TOYOTA', '62-8FD15'),
(440, 'TOYOTA', 'LHE150'),
(441, 'TOYOTA', '7FBR18'),
(442, 'TOYOTA', '8FBR18'),
(443, 'TOYOTA', '5FBR-20'),
(444, 'TOYOTA', '5FB-20'),
(445, 'TOYOTA', '8FBE20'),
(446, 'TOYOTA', '8FBRS20'),
(447, 'TOYOTA', 'FD25C-14'),
(448, 'TOYOTA', 'FD25T-7'),
(449, 'TOYOTA', '7FD25'),
(450, 'TOYOTA', '62 8FD25'),
(451, 'TOYOTA', '8FD25'),
(452, 'TOYOTA', '30 8FG25'),
(453, 'TOYOTA', '60-8FD25'),
(454, 'TOYOTA', '32 8FG25'),
(455, 'TOYOTA', '30-8FG25'),
(456, 'TOYOTA', '32-8FG25'),
(457, 'TOYOTA', '60 -8FD25'),
(458, 'TOYOTA', '60 - 8FD25'),
(459, 'TOYOTA', '62-8FD25'),
(460, 'TOYOTA', '8FBN25'),
(461, 'TOYOTA', '628FD25'),
(462, 'TOYOTA', 'FDZN25'),
(463, 'TOYOTA', 'LPE250'),
(464, 'TOYOTA', 'FBN25'),
(465, 'TOYOTA', '32-FG25'),
(466, 'TOYOTA', 'LP250'),
(467, 'TOYOTA', 'FGZN25'),
(468, 'TOYOTA', '8FBRS25'),
(469, 'TOYOTA', 'FBR'),
(470, 'TOYOTA', '60 - 8FD30'),
(471, 'TOYOTA', '60-8FD30'),
(472, 'TOYOTA', '62-8FD30'),
(473, 'TOYOTA', '7FB30'),
(474, 'TOYOTA', '628FD30'),
(475, 'TOYOTA', '8FB30'),
(476, 'TOYOTA', 'FDZN30'),
(477, 'TOYOTA', '8FBN30'),
(478, 'TOYOTA', '8FD30'),
(479, 'TOYOTA', '32-8FG30'),
(480, 'TOYOTA', '5FD35'),
(481, 'TOYOTA', '7FD35'),
(482, 'TOYOTA', '32-7FD35'),
(483, 'TOYOTA', '72-8FDJ35'),
(484, 'TOYOTA', '8FDN35'),
(485, 'TOYOTA', '8FBJ35'),
(486, 'TOYOTA', 'CBT 4 TOWING'),
(487, 'TOYOTA', 'CBT4'),
(488, 'TOYOTA', '4CBTK4'),
(489, 'TOYOTA', '8FDN40'),
(490, 'TOYOTA', '8FD40N'),
(491, 'TOYOTA', '8FD45N'),
(492, 'TOYOTA', '7FD50'),
(493, 'TOYOTA', '8FD50N'),
(494, 'TOYOTA', '5FBR15'),
(495, 'TOYOTA', 'BTLHE150'),
(496, 'TOYOTA', 'LHE50'),
(497, 'XCMG', 'LW300KN'),
(498, 'XILIN LI-ION', 'CBD20R-11'),
(499, 'YALE', 'NDR035EB'),
(500, 'YALE', 'NDR035EANL36TE143'),
(501, 'YALE', 'FBRA18WY'),
(502, 'YALE', 'FBR18SZ'),
(503, 'YALE', 'MP20XV'),
(504, 'YALE', 'MR20HD'),
(505, 'YALE', 'GDP25RK'),
(506, 'YALE', 'GLP25RK'),
(507, 'YALE', 'GLPK25RK'),
(508, 'YALE', 'FB25PYE'),
(509, 'YALE', 'FB25PZ'),
(510, 'YALE', 'FBR25SY'),
(511, 'YALE', 'GLP25MX'),
(512, 'YALE', 'FB25RZ'),
(513, 'YALE', 'GLP30TK'),
(514, 'YALE', 'FB30RZ'),
(515, 'YALE', 'GLP30MX-BL'),
(516, 'YALE', 'NRDR035EA'),
(517, 'YALE', 'NDR035EBNL36TE179'),
(518, 'YALE', 'NDR035EANL36TE157'),
(519, 'STILL', 'GX-X/GX-Q'),
(520, 'BOBCAT', 'SKID STEER LOADER, S570'),
(521, 'TOYOTA', 'SWE120'),
(522, 'HELI', 'CDD15J-RE'),
(523, 'MITSUBISHI', 'FD35'),
(524, 'KOMATSU', 'FD25JC-12'),
(525, 'TOYOTA', '7FB25'),
(526, 'KOMATSU', 'FD50-6'),
(527, 'TOYOTA', 'FD20T-6'),
(528, 'MITSUBISHI', 'FD70T'),
(529, 'TOYOTA', 'FD30ZS700'),
(530, 'KOMATSU', 'FD2.5C-14'),
(531, 'MITSUBISHI', 'FD30'),
(532, 'KOMATSU', 'FD30C-14'),
(533, 'MITSUBISHI', 'DP30ND'),
(534, 'CAT', 'CPCD70F1'),
(535, 'MITSUBISHI', 'FD70 DIESEL'),
(536, 'TOYOTA', 'FBR 1.5'),
(537, 'TOYOTA', '5FB25'),
(538, 'TOYOTA', '7FB20'),
(539, 'SOOSUNG', 'SBF-255'),
(540, 'NISSAN', 'F1F1M15U-2W350'),
(541, 'PATRIA', 'PFD25CL-1'),
(542, 'NICHIYU', 'FBRW 18-75C-700MSF'),
(543, 'SOOSUNG', 'SBF-15'),
(544, 'TOYOTA', '60 8FD30'),
(545, 'HYSTER', 'H2.50DX'),
(546, 'HYSTER', 'H2.5TX-92'),
(547, 'CAT', 'DP30NT'),
(548, 'JUNGHEINRICH', 'ETV116-1150-9020DZ'),
(549, 'YALE', 'GDP25TK'),
(550, 'NICHIYU', 'FB25PN-72C-470'),
(551, 'CAT', 'E920TCA'),
(552, 'NICHIYU', 'FB25PN-75C-300'),
(553, 'NICHIYU', 'FB25P-75C-470M'),
(554, 'NICHIYU', 'F25PN-72C-470M'),
(555, 'CAT', 'DP25ND3FP47');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `target_role` varchar(100) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `division` varchar(50) DEFAULT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `notifications`:
--

--
-- Truncate table before insert `notifications`
--

TRUNCATE TABLE `notifications`;
--
-- Dumping data for table `notifications`
--

INSERT DELAYED IGNORE INTO `notifications` (`id`, `user_id`, `target_role`, `url`, `role`, `division`, `message`, `link`, `is_read`, `created_at`, `read_at`) VALUES
(1, NULL, NULL, NULL, 'warehouse', 'warehouse', 'Ada 1 unit PO baru (No: tester) yang harus diverifikasi.', '/warehouse/purchase-orders', 0, '2025-08-11 16:37:42', NULL),
(2, NULL, NULL, NULL, 'warehouse', 'warehouse', 'Ada 1 unit PO baru (No: tester324234) yang harus diverifikasi.', '/warehouse/purchase-orders', 0, '2025-08-12 09:00:10', NULL),
(3, NULL, NULL, NULL, 'warehouse', 'warehouse', 'Ada 1 unit PO baru (No: initest) yang harus diverifikasi.', '/warehouse/purchase-orders', 0, '2025-08-12 09:21:21', NULL),
(4, NULL, NULL, NULL, 'warehouse', 'warehouse', 'Ada 1 unit PO baru (No: initest123) yang harus diverifikasi.', '/warehouse/purchase-orders', 0, '2025-08-12 13:45:20', NULL),
(5, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/004 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-15 09:42:53', NULL),
(6, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/005 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-16 02:21:30', NULL),
(7, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/006 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-16 02:56:25', NULL),
(8, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/007 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-16 03:54:30', NULL),
(9, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/008 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-16 04:45:17', NULL),
(10, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/009 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-16 12:47:25', NULL),
(11, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/010 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-16 13:36:27', NULL),
(12, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/011 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-16 14:12:06', NULL),
(13, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/012 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-18 02:32:15', NULL),
(14, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/013 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-18 02:33:49', NULL),
(15, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/014 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-19 02:48:19', NULL),
(16, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/015 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-20 10:12:25', NULL),
(17, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/016 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-20 10:14:49', NULL),
(18, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/017 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-21 02:19:31', NULL),
(19, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/018 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-21 07:02:47', NULL),
(20, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/001 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-26 07:48:51', NULL),
(21, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/002 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-26 07:53:03', NULL),
(22, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/001 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-26 08:28:33', NULL),
(23, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/002 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-27 04:14:39', NULL),
(24, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/003 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-27 09:00:44', NULL),
(25, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/004 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-27 15:37:29', NULL),
(26, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202508/005 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-08-28 01:54:22', NULL),
(27, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/001 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-01 02:40:53', NULL),
(28, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/002 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-01 02:41:52', NULL),
(29, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/001 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-01 04:16:57', NULL),
(56, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/001 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-03 09:38:49', NULL),
(57, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/002 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-04 04:13:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `notification_logs`;
CREATE TABLE IF NOT EXISTS `notification_logs` (
  `id_notification` int(11) NOT NULL AUTO_INCREMENT,
  `po_type` enum('unit','attachment','sparepart') NOT NULL,
  `po_id` int(11) NOT NULL,
  `no_po` varchar(100) NOT NULL,
  `notification_type` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `sent_to_division` varchar(100) DEFAULT NULL,
  `status` enum('pending','sent','read') NOT NULL DEFAULT 'pending',
  `created_by` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_notification`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `notification_logs`:
--

--
-- Truncate table before insert `notification_logs`
--

TRUNCATE TABLE `notification_logs`;
-- --------------------------------------------------------

--
-- Table structure for table `optimization_additional_log`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `optimization_additional_log`;
CREATE TABLE IF NOT EXISTS `optimization_additional_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_type` enum('FK_CONSTRAINT','INDEX','TRIGGER','PROCEDURE') NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `constraint_name` varchar(200) DEFAULT NULL,
  `action` varchar(500) DEFAULT NULL,
  `status` enum('SUCCESS','ERROR','SKIPPED') NOT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `optimization_additional_log`:
--

--
-- Truncate table before insert `optimization_additional_log`
--

TRUNCATE TABLE `optimization_additional_log`;
--
-- Dumping data for table `optimization_additional_log`
--

INSERT DELAYED IGNORE INTO `optimization_additional_log` (`id`, `operation_type`, `table_name`, `constraint_name`, `action`, `status`, `error_message`, `created_at`) VALUES
(1, 'FK_CONSTRAINT', 'po_sparepart_items', 'fk_po_sparepart_items_purchase_orders', 'po_sparepart_items.po_id -> purchase_orders.id_po', 'SUCCESS', NULL, '2025-09-03 09:32:02'),
(2, 'FK_CONSTRAINT', 'po_units', 'fk_po_units_purchase_orders', 'po_units.po_id -> purchase_orders.id_po', 'SUCCESS', NULL, '2025-09-03 09:32:02'),
(3, 'FK_CONSTRAINT', 'purchase_orders', 'fk_purchase_orders_suppliers', 'purchase_orders.supplier_id -> suppliers.id_supplier', 'SUCCESS', NULL, '2025-09-03 09:32:02'),
(4, 'INDEX', NULL, NULL, 'Created additional performance indexes', 'SUCCESS', NULL, '2025-09-03 09:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `optimization_log`
--
-- Creation: Sep 03, 2025 at 09:27 AM
--

DROP TABLE IF EXISTS `optimization_log`;
CREATE TABLE IF NOT EXISTS `optimization_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `operation_type` enum('FK_CONSTRAINT','INDEX','TRIGGER','PROCEDURE') NOT NULL,
  `table_name` varchar(100) DEFAULT NULL,
  `constraint_name` varchar(200) DEFAULT NULL,
  `action` varchar(500) DEFAULT NULL,
  `status` enum('SUCCESS','ERROR','SKIPPED') NOT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `optimization_log`:
--

--
-- Truncate table before insert `optimization_log`
--

TRUNCATE TABLE `optimization_log`;
--
-- Dumping data for table `optimization_log`
--

INSERT DELAYED IGNORE INTO `optimization_log` (`id`, `operation_type`, `table_name`, `constraint_name`, `action`, `status`, `error_message`, `created_at`) VALUES
(1, 'FK_CONSTRAINT', 'delivery_items', 'fk_delivery_items_delivery_instructions', 'delivery_items.di_id -> delivery_instructions.id', 'SUCCESS', NULL, '2025-09-03 09:30:02'),
(2, 'FK_CONSTRAINT', 'po_items', 'fk_po_items_purchase_orders', 'po_items.po_id -> purchase_orders.id_po', 'SUCCESS', NULL, '2025-09-03 09:30:02');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `permissions`;
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `key` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `module` varchar(50) NOT NULL,
  `category` varchar(50) DEFAULT 'general',
  `is_system_permission` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `permissions`:
--

--
-- Truncate table before insert `permissions`
--

TRUNCATE TABLE `permissions`;
--
-- Dumping data for table `permissions`
--

INSERT DELAYED IGNORE INTO `permissions` (`id`, `name`, `key`, `description`, `module`, `category`, `is_system_permission`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Access Administration', 'admin.access', 'Access to administration module', 'admin', 'access', 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(2, 'User Management', 'admin.user_management', 'Manage users and their details', 'admin', 'management', 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(3, 'Role Management', 'admin.role_management', 'Manage roles and role assignments', 'admin', 'management', 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(4, 'Permission Management', 'admin.permission_management', 'Manage permissions and access control', 'admin', 'management', 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(5, 'System Settings', 'admin.system_settings', 'Configure system settings', 'admin', 'configuration', 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(6, 'Configuration', 'admin.configuration', 'Access system configuration', 'admin', 'configuration', 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(7, 'Access Service', 'service.access', 'Access to service division', 'service', 'access', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(8, 'View Work Orders', 'service.work_orders.view', 'View work orders', 'service', 'work_orders', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(9, 'Create Work Orders', 'service.work_orders.create', 'Create new work orders', 'service', 'work_orders', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(10, 'Edit Work Orders', 'service.work_orders.edit', 'Edit existing work orders', 'service', 'work_orders', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(11, 'Delete Work Orders', 'service.work_orders.delete', 'Delete work orders', 'service', 'work_orders', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(12, 'View PMPS', 'service.pmps.view', 'View preventive maintenance schedules', 'service', 'maintenance', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(13, 'Manage PMPS', 'service.pmps.manage', 'Manage preventive maintenance schedules', 'service', 'maintenance', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(14, 'View Inventory', 'service.inventory.view', 'View service inventory', 'service', 'inventory', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(15, 'Manage Inventory', 'service.inventory.manage', 'Manage service inventory', 'service', 'inventory', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(16, 'View Unit Inventory', 'service.unit_inventory.view', 'View unit inventory', 'service', 'inventory', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(17, 'View PDI', 'service.pdi.view', 'View PDI (Pre-Delivery Inspection)', 'service', 'inspection', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(18, 'Manage PDI', 'service.pdi.manage', 'Manage PDI (Pre-Delivery Inspection)', 'service', 'inspection', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(19, 'View Data Unit', 'service.data_unit.view', 'View unit data', 'service', 'data', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(20, 'Manage Data Unit', 'service.data_unit.manage', 'Manage unit data', 'service', 'data', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(21, 'Access Unit Rolling', 'unit_rolling.access', 'Access to unit operational division', 'unit_rolling', 'access', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(22, 'View Delivery Instructions', 'unit_rolling.delivery_instructions.view', 'View delivery instructions', 'unit_rolling', 'delivery', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(23, 'Manage Delivery Instructions', 'unit_rolling.delivery_instructions.manage', 'Manage delivery instructions', 'unit_rolling', 'delivery', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(24, 'View Delivery Unit', 'unit_rolling.delivery_unit.view', 'View delivery units', 'unit_rolling', 'delivery', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(25, 'Manage Delivery Unit', 'unit_rolling.delivery_unit.manage', 'Manage delivery units', 'unit_rolling', 'delivery', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(26, 'View History', 'unit_rolling.history.view', 'View operational history', 'unit_rolling', 'history', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(27, 'Access Marketing', 'marketing.access', 'Access to marketing division', 'marketing', 'access', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(28, 'Create Penawaran', 'marketing.penawaran.create', 'Create quotations', 'marketing', 'sales', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(29, 'View Penawaran', 'marketing.penawaran.view', 'View quotations', 'marketing', 'sales', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(30, 'Manage Kontrak', 'marketing.kontrak.manage', 'Manage contracts and PO', 'marketing', 'contracts', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(31, 'View List Unit', 'marketing.list_unit.view', 'View unit listings', 'marketing', 'inventory', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(32, 'Manage List Unit', 'marketing.list_unit.manage', 'Manage unit listings', 'marketing', 'inventory', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(33, 'Access Warehouse', 'warehouse.access', 'Access to warehouse division', 'warehouse', 'access', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(34, 'Manage Assets', 'warehouse.assets.manage', 'Manage unit assets', 'warehouse', 'assets', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(35, 'View Inventory', 'warehouse.inventory.view', 'View warehouse inventory', 'warehouse', 'inventory', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(36, 'Manage Inventory', 'warehouse.inventory.manage', 'Manage warehouse inventory', 'warehouse', 'inventory', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(37, 'Verify PO', 'warehouse.po.verify', 'Verify purchase orders', 'warehouse', 'purchasing', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(38, 'Access Purchasing', 'purchasing.access', 'Access to purchasing division', 'purchasing', 'access', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(39, 'Manage Purchasing', 'purchasing.manage', 'Manage purchase orders and procurement', 'purchasing', 'procurement', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(40, 'Create PO', 'purchasing.po.create', 'Create purchase orders', 'purchasing', 'procurement', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(41, 'Approve PO', 'purchasing.po.approve', 'Approve purchase orders', 'purchasing', 'procurement', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(42, 'Access Perizinan', 'perizinan.access', 'Access to licensing division', 'perizinan', 'access', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(43, 'Manage Perizinan', 'perizinan.manage', 'Manage permits and licenses', 'perizinan', 'licensing', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(44, 'Create SIO', 'perizinan.sio.create', 'Create operator licenses', 'perizinan', 'licensing', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(45, 'Create SILO', 'perizinan.silo.create', 'Create operational worthiness certificates', 'perizinan', 'licensing', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(46, 'Access Accounting', 'accounting.access', 'Access to accounting division', 'accounting', 'access', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(47, 'View Finance', 'finance.view', 'View financial data', 'accounting', 'finance', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(48, 'Manage Finance', 'finance.manage', 'Manage financial data', 'accounting', 'finance', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(49, 'View Invoices', 'invoices.view', 'View invoices', 'accounting', 'invoicing', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(50, 'Manage Invoices', 'invoices.manage', 'Manage invoices', 'accounting', 'invoicing', 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(52, 'View SPK', 'service.spk.view', '', 'service', 'access', 0, 1, '2025-08-07 03:52:37', '2025-08-07 06:41:57'),
(53, 'SPK Edit', 'marketing.spk.manage', '', 'marketing', 'general', 0, 1, '2025-08-07 06:52:21', '2025-08-07 06:52:21'),
(54, 'Marketing DI Manage', 'marketing.di.manage', '', 'marketing', 'general', 0, 1, '2025-08-07 06:52:51', '2025-08-07 06:52:51'),
(55, 'Service Spk Manage', 'service.spk.manage', '', 'service', 'general', 0, 1, '2025-08-07 07:26:34', '2025-08-07 07:26:34');

-- --------------------------------------------------------

--
-- Table structure for table `po_items`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `po_items`;
CREATE TABLE IF NOT EXISTS `po_items` (
  `id_po_item` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `item_type` enum('Attachment','Battery') NOT NULL,
  `attachment_id` int(11) DEFAULT NULL,
  `baterai_id` int(11) DEFAULT NULL,
  `charger_id` int(11) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `serial_number_charger` varchar(100) DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') NOT NULL DEFAULT 'Belum Dicek',
  `catatan_verifikasi` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_po_item`),
  KEY `fk_po_items_purchase_orders` (`po_id`),
  KEY `idx_po_items_status_verifikasi` (`status_verifikasi`),
  KEY `idx_po_items_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `po_items`:
--   `po_id`
--       `purchase_orders` -> `id_po`
--

--
-- Truncate table before insert `po_items`
--

TRUNCATE TABLE `po_items`;
--
-- Dumping data for table `po_items`
--

INSERT DELAYED IGNORE INTO `po_items` (`id_po_item`, `po_id`, `item_type`, `attachment_id`, `baterai_id`, `charger_id`, `serial_number`, `serial_number_charger`, `keterangan`, `status_verifikasi`, `catatan_verifikasi`, `created_at`, `updated_at`) VALUES
(2, 27, 'Battery', NULL, 2, 3, '', '', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:33:15'),
(3, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:33:44'),
(4, 27, 'Battery', NULL, 2, 3, '', '', '', '', '', '2025-07-22 00:29:20', '2025-07-23 21:33:17'),
(5, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:33:52'),
(6, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:33:57'),
(7, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:34:03'),
(8, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:34:11'),
(9, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:34:16'),
(10, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:34:21'),
(11, 27, 'Battery', NULL, 2, 3, '123123', '123123', '', 'Sesuai', '', '2025-07-22 00:29:20', '2025-07-23 21:34:26'),
(22, 36, 'Attachment', 3, NULL, NULL, '11123', '', '', 'Sesuai', '', '2025-07-23 21:08:34', '2025-07-23 23:29:16'),
(23, 37, 'Attachment', 5, NULL, NULL, '', '', '', 'Sesuai', '', '2025-07-23 23:32:28', '2025-07-23 23:32:57'),
(24, 37, 'Attachment', 5, NULL, NULL, 'ok', NULL, '', 'Sesuai', '', '2025-07-23 23:32:28', '2025-07-26 01:30:35'),
(25, 37, 'Attachment', 5, NULL, NULL, 'ok', NULL, '', 'Sesuai', '', '2025-07-23 23:32:28', '2025-07-26 01:37:44'),
(26, 37, 'Attachment', 5, NULL, NULL, 'test4', NULL, '', 'Sesuai', '', '2025-07-23 23:32:28', '2025-08-11 21:54:35'),
(27, 37, 'Attachment', 5, NULL, NULL, 'wae', NULL, '', 'Sesuai', '', '2025-07-23 23:32:28', '2025-08-11 23:44:51'),
(73, 89, 'Attachment', 2, NULL, NULL, '123', NULL, '', 'Sesuai', NULL, '2025-07-29 19:35:23', '2025-08-21 20:57:48'),
(74, 92, 'Attachment', 2, NULL, NULL, '12333445', NULL, '', 'Sesuai', NULL, '2025-07-29 19:49:08', '2025-08-21 21:04:40'),
(75, 92, 'Battery', 2, 4, 4, '123', '123', '', 'Sesuai', NULL, '2025-07-29 19:49:08', '2025-08-21 21:32:36'),
(76, 95, 'Battery', NULL, 14, 15, '1', '1', '', 'Sesuai', NULL, '2025-07-31 21:22:10', '2025-08-21 21:35:36'),
(77, 95, 'Attachment', 13, 14, 15, '123', NULL, '', 'Sesuai', NULL, '2025-07-31 21:22:10', '2025-08-21 21:36:00'),
(78, 118, 'Attachment', 1, NULL, NULL, '123', NULL, '', 'Sesuai', NULL, '2025-08-11 18:59:05', '2025-08-21 21:36:39'),
(79, 124, 'Attachment', 4, NULL, NULL, '123', NULL, '', 'Sesuai', NULL, '2025-08-11 20:15:49', '2025-08-22 02:18:28'),
(80, 130, 'Attachment', 3, NULL, NULL, '123', NULL, '', 'Sesuai', NULL, '2025-08-11 20:27:06', '2025-08-22 02:19:00'),
(81, 131, 'Attachment', 3, NULL, NULL, 'ok', NULL, '', 'Sesuai', NULL, '2025-08-11 20:27:13', '2025-08-26 21:50:06'),
(82, 132, 'Battery', NULL, 14, 14, '123', '123', '', 'Sesuai', NULL, '2025-08-11 20:27:48', '2025-08-22 02:18:41'),
(83, 139, 'Attachment', 3, NULL, NULL, 'a', NULL, '', 'Sesuai', NULL, '2025-08-11 21:06:47', '2025-08-28 02:32:49'),
(84, 143, 'Battery', NULL, 4, 5, '123', '123', '', 'Sesuai', NULL, '2025-08-22 02:21:14', '2025-08-22 02:23:14'),
(85, 143, 'Battery', NULL, 4, 5, '123', '123', '', 'Sesuai', NULL, '2025-08-22 02:21:14', '2025-08-26 21:15:34'),
(86, 143, 'Battery', NULL, 4, 5, '123', '123', '', 'Sesuai', NULL, '2025-08-22 02:21:14', '2025-08-26 21:15:43'),
(87, 143, 'Battery', NULL, 4, 5, '123', '123', '', 'Sesuai', NULL, '2025-08-22 02:21:14', '2025-08-26 21:15:51'),
(88, 143, 'Battery', NULL, 4, 5, '123', '123', '', 'Sesuai', NULL, '2025-08-22 02:21:14', '2025-08-26 21:15:58'),
(89, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(90, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(91, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(92, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(93, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(94, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(95, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(96, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(97, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08'),
(98, 147, 'Battery', NULL, 8, 5, '213124', NULL, '', 'Belum Dicek', NULL, '2025-08-28 02:35:08', '2025-08-28 02:35:08');

-- --------------------------------------------------------

--
-- Table structure for table `po_sparepart_items`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `po_sparepart_items`;
CREATE TABLE IF NOT EXISTS `po_sparepart_items` (
  `id` int(11) NOT NULL,
  `po_id` int(11) NOT NULL,
  `sparepart_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1,
  `satuan` enum('Pieces','Rol','Kaleng','Set','Pak','Meter','Unit','Jerigen','Lembar','Box','Pax','Drum','Batang','Pil','Dus','Kilogram','Botol','IBC Tank','Lusin','Liter','Lot') NOT NULL,
  `keterangan` text DEFAULT NULL,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') NOT NULL DEFAULT 'Belum Dicek',
  `catatan_verifikasi` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_po_sparepart_status_verifikasi` (`status_verifikasi`),
  KEY `idx_po_sparepart_items_po_id` (`po_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `po_sparepart_items`:
--   `po_id`
--       `purchase_orders` -> `id_po`
--

--
-- Truncate table before insert `po_sparepart_items`
--

TRUNCATE TABLE `po_sparepart_items`;
--
-- Dumping data for table `po_sparepart_items`
--

INSERT DELAYED IGNORE INTO `po_sparepart_items` (`id`, `po_id`, `sparepart_id`, `qty`, `satuan`, `keterangan`, `status_verifikasi`, `catatan_verifikasi`) VALUES
(14, 35, 5, 1, 'Pieces', '', 'Sesuai', ''),
(15, 35, 23, 1, 'Pieces', '', 'Tidak Sesuai', ''),
(16, 35, 33, 1, 'Pieces', '', 'Sesuai', ''),
(17, 40, 3, 1, 'Pieces', '', 'Sesuai', NULL),
(18, 40, 4, 1, 'Pieces', '', 'Sesuai', NULL),
(19, 40, 28, 1, 'Pieces', '', 'Sesuai', NULL),
(20, 40, 47, 1, 'Pieces', '', 'Sesuai', NULL),
(35, 85, 1, 1, 'Pieces', '', 'Sesuai', ''),
(36, 85, 3, 1, 'Kaleng', '', 'Belum Dicek', NULL),
(37, 88, 3, 1, 'Pieces', '', 'Belum Dicek', NULL),
(38, 96, 3, 1, 'Pieces', '', 'Belum Dicek', NULL),
(39, 119, 1, 1, 'Pieces', '', 'Belum Dicek', NULL),
(40, 125, 3, 1, 'Pieces', '', 'Belum Dicek', NULL),
(48, 137, 3, 1, 'Pieces', '', 'Belum Dicek', NULL),
(49, 140, 30, 1, 'Pieces', '', 'Belum Dicek', NULL),
(50, 141, 4, 1, 'Pieces', '', 'Belum Dicek', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `po_units`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `po_units`;
CREATE TABLE IF NOT EXISTS `po_units` (
  `id_po_unit` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `po_id` int(11) NOT NULL,
  `jenis_unit` int(11) DEFAULT NULL,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') NOT NULL DEFAULT 'Belum Dicek',
  `merk_unit` int(11) DEFAULT NULL,
  `model_unit_id` int(11) DEFAULT NULL,
  `tipe_unit_id` int(11) DEFAULT NULL,
  `serial_number_po` varchar(100) DEFAULT NULL,
  `tahun_po` int(11) DEFAULT NULL,
  `kapasitas_id` int(11) DEFAULT NULL,
  `mast_id` int(11) DEFAULT NULL,
  `sn_mast_po` varchar(100) DEFAULT NULL,
  `mesin_id` int(11) DEFAULT NULL,
  `sn_mesin_po` varchar(100) DEFAULT NULL,
  `attachment_id` int(11) DEFAULT NULL,
  `sn_attachment_po` varchar(100) DEFAULT NULL,
  `baterai_id` int(11) DEFAULT NULL,
  `sn_baterai_po` varchar(100) DEFAULT NULL,
  `charger_id` int(11) DEFAULT NULL,
  `sn_charger_po` varchar(100) DEFAULT NULL,
  `ban_id` int(11) DEFAULT NULL,
  `roda_id` int(11) DEFAULT NULL,
  `valve_id` int(11) DEFAULT NULL,
  `status_penjualan` enum('Baru','Bekas','Rekondisi') DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  PRIMARY KEY (`id_po_unit`),
  KEY `fk_po_units_purchase_orders` (`po_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `po_units`:
--   `po_id`
--       `purchase_orders` -> `id_po`
--

--
-- Truncate table before insert `po_units`
--

TRUNCATE TABLE `po_units`;
--
-- Dumping data for table `po_units`
--

INSERT DELAYED IGNORE INTO `po_units` (`id_po_unit`, `created_at`, `updated_at`, `po_id`, `jenis_unit`, `status_verifikasi`, `merk_unit`, `model_unit_id`, `tipe_unit_id`, `serial_number_po`, `tahun_po`, `kapasitas_id`, `mast_id`, `sn_mast_po`, `mesin_id`, `sn_mesin_po`, `attachment_id`, `sn_attachment_po`, `baterai_id`, `sn_baterai_po`, `charger_id`, `sn_charger_po`, `ban_id`, `roda_id`, `valve_id`, `status_penjualan`, `keterangan`) VALUES
(1, NULL, '2025-07-21 01:31:15', 2, 1, 'Sesuai', NULL, 3, 3, '123123', 2019, 11, 6, '123', 1, '1', 12, '123', 123, '123', 123, '13', 5, 4, 3, NULL, NULL),
(2, NULL, '2025-07-21 01:31:17', 1, 2, 'Sesuai', NULL, 1, 4, '12331233', 2025, 14, 2, '123', 1, '1', 1, '1', 123, '123', 123, '123', 6, 3, 3, NULL, NULL),
(42, '2025-07-20 17:00:00', '2025-07-21 01:32:16', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(43, '2025-07-20 17:00:00', '2025-07-21 01:32:17', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(44, '2025-07-20 17:00:00', '2025-07-30 20:31:48', 23, 1, 'Sesuai', 1, 1, 1, '123', 2025, 2, 1, '123', 1, '123', 1, '512512312312512315123', 1, '123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(45, '2025-07-20 17:00:00', '2025-07-21 01:32:19', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(46, '2025-07-20 17:00:00', '2025-07-21 01:32:20', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(47, '2025-07-20 17:00:00', '2025-07-21 01:32:35', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(48, '2025-07-20 17:00:00', '2025-07-21 01:32:36', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(49, '2025-07-20 17:00:00', '2025-07-21 01:32:37', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(50, '2025-07-20 17:00:00', '2025-07-21 01:32:38', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(51, '2025-07-20 17:00:00', '2025-07-21 01:32:39', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(52, '2025-07-20 17:00:00', '2025-07-21 01:32:50', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(53, '2025-07-20 17:00:00', '2025-07-21 01:32:51', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(54, '2025-07-20 17:00:00', '2025-07-21 01:32:52', 23, 1, 'Sesuai', 1, 1, 1, 'TEST1234123', 2025, 2, 1, '111231451523123', 1, '15235123124512321', 1, '512512312312512315123', 1, '11112455512314123', 1, 'asdafasdadasdsafasd', 1, 1, 1, 'Baru', 'NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),
(55, '2025-07-20 19:56:41', '2025-07-21 01:33:04', 24, 2, 'Sesuai', 60, 66, 4, 'TEST12341234', 2025, 24, 3, '1112314515231233', 4, '1523512312451232112', 3, '5125123123125123151232', 4, '11112455512314123', 4, 'asdafasdadasdsafasd', 3, 2, 3, 'Baru', 'Hadir'),
(56, '2025-07-20 19:56:41', '2025-07-21 01:33:05', 24, 2, 'Sesuai', 60, 66, 4, 'TEST12341234', 2025, 24, 3, '1112314515231233', 4, '1523512312451232112', 3, '5125123123125123151232', 4, '11112455512314123', 4, 'asdafasdadasdsafasd', 3, 2, 3, 'Baru', 'Hadir'),
(57, '2025-07-20 19:56:41', '2025-07-21 01:33:06', 24, 2, 'Sesuai', 60, 66, 4, 'TEST12341234', 2025, 24, 3, '1112314515231233', 4, '1523512312451232112', 3, '5125123123125123151232', 4, '11112455512314123', 4, 'asdafasdadasdsafasd', 3, 2, 3, 'Baru', 'Hadir'),
(58, '2025-07-21 07:14:31', '2025-07-25 19:49:17', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(59, '2025-07-21 07:14:31', '2025-07-24 18:25:37', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, NULL, 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(60, '2025-07-21 07:14:31', '2025-07-24 19:40:51', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(61, '2025-07-21 07:14:31', '2025-07-24 20:32:34', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(62, '2025-07-21 07:14:31', '2025-07-25 20:14:26', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(63, '2025-07-21 07:14:31', '2025-07-25 19:10:11', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(64, '2025-07-21 07:14:31', '2025-07-25 21:00:44', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(65, '2025-07-21 07:14:31', '2025-07-25 23:29:31', 25, 1, 'Sesuai', 4, 9, 12, '123a', 2025, 47, 2, '123a', 1, '123a', 1, '', 1, '123a', 2, '', 1, 1, 3, 'Baru', ''),
(66, '2025-07-21 07:14:31', '2025-07-25 19:36:11', 25, 1, 'Sesuai', 4, 9, 12, '132', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(67, '2025-07-21 07:14:31', '2025-08-03 20:06:17', 25, 1, 'Sesuai', 4, 9, 12, '123', 2025, 47, 2, '123', 1, '123', 1, '', 1, '123', 2, '', 1, 1, 3, 'Baru', ''),
(68, '2025-07-23 23:58:46', '2025-08-03 20:07:12', 38, 3, 'Sesuai', 4, 6, 12, '123', 2025, 6, 2, '123', 2, '123', 2, NULL, 2, '123', 6, NULL, 1, 2, 3, 'Baru', ''),
(83, '2025-07-25 18:44:44', '2025-07-25 18:44:44', 44, 1, 'Belum Dicek', 4, 5, 12, NULL, 2025, 27, 2, NULL, 2, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 2, 3, 'Baru', ''),
(84, '2025-07-25 18:44:44', '2025-07-25 18:44:44', 44, 1, 'Belum Dicek', 4, 5, 12, NULL, 2025, 27, 2, NULL, 2, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 2, 3, 'Baru', ''),
(85, '2025-07-25 18:44:44', '2025-07-25 18:44:44', 44, 1, 'Belum Dicek', 4, 5, 12, NULL, 2025, 27, 2, NULL, 2, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 2, 3, 'Baru', ''),
(86, '2025-07-25 18:44:44', '2025-07-25 18:44:44', 44, 1, 'Belum Dicek', 4, 5, 12, NULL, 2025, 27, 2, NULL, 2, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 2, 3, 'Baru', ''),
(87, '2025-07-25 18:44:44', '2025-07-25 18:44:44', 44, 1, 'Belum Dicek', 4, 5, 12, NULL, 2025, 27, 2, NULL, 2, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 2, 3, 'Baru', ''),
(89, '2025-07-25 23:53:49', '2025-07-25 23:54:27', 46, 2, 'Sesuai', 499, 501, 13, 'PO/SPRT/9923100', 2025, 37, 4, 'PO/SPRT/9923100', 3, 'PO/SPRT/9923100', NULL, NULL, 16, 'PO/SPRT/9923100', NULL, NULL, 4, 2, 3, 'Baru', NULL),
(94, '2025-07-29 00:03:39', '2025-07-29 00:03:39', 52, 2, 'Belum Dicek', 1, 1, 2, NULL, 2025, 5, 3, NULL, 3, NULL, NULL, NULL, 3, NULL, NULL, NULL, 2, 1, 2, 'Baru', NULL),
(95, '2025-07-29 00:03:39', '2025-07-29 00:03:39', 52, 2, 'Belum Dicek', 1, 1, 2, NULL, 2025, 5, 3, NULL, 3, NULL, NULL, NULL, 3, NULL, NULL, NULL, 2, 1, 2, 'Baru', NULL),
(96, '2025-07-29 00:03:39', '2025-07-29 00:03:39', 52, 2, 'Belum Dicek', 1, 1, 2, NULL, 2025, 5, 3, NULL, 3, NULL, NULL, NULL, 3, NULL, NULL, NULL, 2, 1, 2, 'Baru', NULL),
(97, '2025-07-29 00:03:39', '2025-07-29 00:03:39', 52, 2, 'Belum Dicek', 1, 1, 2, NULL, 2025, 5, 3, NULL, 3, NULL, NULL, NULL, 3, NULL, NULL, NULL, 2, 1, 2, 'Baru', NULL),
(98, '2025-07-29 00:03:39', '2025-07-29 00:03:39', 52, 2, 'Belum Dicek', 1, 1, 2, NULL, 2025, 5, 3, NULL, 3, NULL, NULL, NULL, 3, NULL, NULL, NULL, 2, 1, 2, 'Baru', NULL),
(99, '2025-07-29 00:03:39', '2025-07-29 00:03:39', 52, 2, 'Belum Dicek', 1, 1, 2, NULL, 2025, 5, 3, NULL, 3, NULL, NULL, NULL, 3, NULL, NULL, NULL, 2, 1, 2, 'Baru', NULL),
(100, '2025-07-29 00:03:39', '2025-07-29 00:03:39', 52, 2, 'Belum Dicek', 1, 1, 2, NULL, 2025, 5, 3, NULL, 3, NULL, NULL, NULL, 3, NULL, NULL, NULL, 2, 1, 2, 'Baru', NULL),
(101, '2025-07-29 00:28:01', '2025-07-29 00:28:01', 58, 2, 'Belum Dicek', 4, NULL, NULL, NULL, 2025, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Baru', NULL),
(102, '2025-07-29 01:44:12', '2025-07-29 01:44:12', 65, 2, 'Belum Dicek', 2, 3, 3, NULL, 2025, 4, 4, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 4, 3, 'Baru', ''),
(103, '2025-07-29 01:45:56', '2025-07-29 01:45:56', 66, 2, 'Belum Dicek', 38, 38, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, 4, 3, 'Baru', ''),
(104, '2025-07-29 01:45:56', '2025-07-29 01:45:56', 66, 2, 'Belum Dicek', 4, 7, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, 4, 3, 'Baru', ''),
(105, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(106, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(107, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(108, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(109, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(110, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(111, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(112, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(113, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(114, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(115, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(116, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 4, 6, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(117, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(118, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(119, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(120, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(121, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(122, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(123, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(124, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(125, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(126, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(127, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(128, '2025-07-29 01:59:17', '2025-07-29 01:59:17', 67, 2, 'Belum Dicek', 1, 1, 3, NULL, 2025, 4, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(213, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(214, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(215, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(216, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(217, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(218, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(219, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(220, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(221, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(222, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(223, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(224, '2025-07-29 19:23:48', '2025-07-29 19:23:48', 84, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 6, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 3, 4, 'Baru', ''),
(225, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(226, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(227, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(228, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(229, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(230, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(231, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(232, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(233, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(234, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(235, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(236, '2025-07-29 19:25:29', '2025-07-29 19:25:29', 87, 1, 'Belum Dicek', 2, 2, 1, NULL, 2025, 2, 5, NULL, 6, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 4, 4, 'Baru', ''),
(237, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(238, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(239, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(240, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(241, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(242, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(243, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(244, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(245, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(246, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(247, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(248, '2025-07-29 20:16:28', '2025-07-29 20:16:28', 93, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 1, 5, NULL, 5, NULL, NULL, NULL, 4, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(249, '2025-07-31 21:22:10', '2025-07-31 21:22:10', 94, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 2, 5, NULL, 5, NULL, NULL, NULL, 5, NULL, NULL, NULL, 5, 2, 3, 'Baru', ''),
(250, '2025-07-31 21:29:00', '2025-07-31 21:29:00', 97, 2, 'Belum Dicek', 4, 5, 1, NULL, 2025, 2, 5, NULL, 4, NULL, NULL, NULL, 5, NULL, NULL, NULL, 5, 3, 3, 'Bekas', ''),
(251, '2025-08-11 02:37:42', '2025-08-11 02:37:42', 102, 1, 'Belum Dicek', 60, 64, 1, NULL, 2025, 2, 5, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, 2, 3, 'Rekondisi', NULL),
(252, '2025-08-11 18:56:29', '2025-08-11 18:56:29', 116, 1, 'Belum Dicek', 4, 4, 4, NULL, 2025, 5, 4, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 4, 3, 3, 'Baru', ''),
(253, '2025-08-11 18:59:05', '2025-08-11 18:59:05', 117, 1, 'Belum Dicek', 4, 5, 4, NULL, 2025, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Baru', ''),
(254, '2025-08-11 19:00:10', '2025-08-11 19:00:10', 120, 1, 'Belum Dicek', 2, 2, 2, NULL, 2025, 1, 2, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 4, 3, 'Baru', NULL),
(255, '2025-08-11 19:21:21', '2025-08-11 19:21:21', 121, 1, 'Belum Dicek', 39, 43, 2, NULL, 2025, 5, 1, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, 1, 'Baru', NULL),
(256, '2025-08-11 19:22:14', '2025-08-11 19:22:14', 122, 2, 'Belum Dicek', 4, 6, 9, NULL, 2025, 2, 5, NULL, 4, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 3, 4, 4, 'Baru', ''),
(257, '2025-08-11 20:15:49', '2025-08-11 20:15:49', 123, 2, 'Belum Dicek', 2, 3, 10, NULL, 2025, 10, 2, NULL, 2, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, 2, 3, 'Baru', ''),
(259, '2025-08-11 21:06:47', '2025-08-11 21:06:47', 138, 1, 'Belum Dicek', 2, 2, 6, NULL, 2025, 5, 5, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6, 2, 4, 'Baru', ''),
(260, '2025-08-11 23:45:20', '2025-08-11 23:45:20', 142, 1, 'Belum Dicek', 2, 2, 4, NULL, 2025, 5, 5, NULL, 5, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 5, 2, 3, 'Baru', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `purchase_orders`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `purchase_orders`;
CREATE TABLE IF NOT EXISTS `purchase_orders` (
  `id_po` int(11) NOT NULL,
  `no_po` varchar(100) NOT NULL,
  `tanggal_po` date NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `bl_date` date DEFAULT NULL,
  `keterangan_po` text DEFAULT NULL,
  `tipe_po` enum('Unit','Attachment & Battery','Sparepart','Dinamis') NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','completed','cancelled','Selesai dengan Catatan') DEFAULT 'pending',
  PRIMARY KEY (`id_po`),
  KEY `idx_purchase_orders_status` (`status`),
  KEY `idx_po_tanggal_po` (`tanggal_po`),
  KEY `idx_purchase_orders_supplier_id` (`supplier_id`),
  KEY `idx_purchase_orders_no_po` (`no_po`),
  KEY `idx_purchase_orders_invoice_no` (`invoice_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `purchase_orders`:
--   `supplier_id`
--       `suppliers` -> `id_supplier`
--

--
-- Truncate table before insert `purchase_orders`
--

TRUNCATE TABLE `purchase_orders`;
--
-- Dumping data for table `purchase_orders`
--

INSERT DELAYED IGNORE INTO `purchase_orders` (`id_po`, `no_po`, `tanggal_po`, `supplier_id`, `invoice_no`, `invoice_date`, `bl_date`, `keterangan_po`, `tipe_po`, `created_at`, `updated_at`, `status`) VALUES
(1, 'PO-Unit-2025-07-0001', '2025-07-15', 1, NULL, NULL, NULL, NULL, 'Unit', '2025-07-16 03:44:13', '2025-07-21 01:31:17', 'completed'),
(2, 'PO/ATT/2024/002', '2025-07-16', 2, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-07-16 03:44:13', '2025-07-21 01:31:15', 'completed'),
(23, 'PO-Unit-2025-07-0004', '2025-07-21', 2, '12441234123123123', '2025-07-21', '2025-07-25', '124123123', 'Unit', '2025-07-20 18:33:37', '2025-07-30 20:31:48', 'completed'),
(24, 'PO-Unit-2025-07-0005', '2025-07-21', 2, '1244123412312312312', '2025-07-31', '2025-07-31', 'asfffasdasdasd123', 'Unit', '2025-07-20 19:56:41', '2025-07-21 01:33:06', 'completed'),
(25, 'PO-Unit-2025-07-0006', '2025-07-21', 1, 'PO/ASDA/ASU', '2025-07-21', '2025-07-21', 'BELI UNIT', 'Unit', '2025-07-21 07:14:31', '2025-08-03 20:06:17', 'completed'),
(27, 'PO/221/111111', '2025-07-22', 2, '', '2025-07-22', '2025-07-22', 'BELI ', 'Attachment & Battery', '2025-07-22 00:29:20', '2025-07-22 00:29:20', 'pending'),
(35, 'PO/SPRT/144445', '2025-07-24', 1, NULL, NULL, NULL, '', 'Sparepart', '2025-07-23 20:16:40', '2025-07-23 20:16:58', 'Selesai dengan Catatan'),
(36, 'PO/2000/99231', '2025-07-24', 1, '', '2025-07-24', '2025-07-24', '', 'Attachment & Battery', '2025-07-23 21:08:34', '2025-07-23 23:29:16', 'completed'),
(37, 'PO/ATT/4321/211/1', '2025-07-24', 2, '', '2025-07-25', '2025-07-25', '', 'Attachment & Battery', '2025-07-23 23:32:28', '2025-07-23 23:32:28', 'pending'),
(38, 'PO/278/7875', '2025-07-24', 1, '', '2025-07-24', '2025-07-24', '', 'Unit', '2025-07-23 23:58:46', '2025-08-15 21:26:44', 'completed'),
(39, 'PO/221/131231', '2025-07-24', 1, '', '2025-07-24', '2025-07-24', '', 'Unit', '2025-07-24 01:33:02', '2025-07-24 01:33:02', 'pending'),
(40, 'PO/221/122222', '2025-07-25', 2, NULL, NULL, NULL, '', 'Sparepart', '2025-07-24 22:13:51', '2025-07-24 22:15:54', 'completed'),
(44, 'PO/221/131232', '2025-07-26', 1, '', '2025-07-24', '2025-07-24', '', 'Unit', '2025-07-25 18:44:44', '2025-07-25 18:44:44', 'pending'),
(46, 'PO/SPRT/9923100', '2025-07-26', 1, NULL, '2025-07-26', '2025-07-26', NULL, 'Unit', '2025-07-25 23:53:49', '2025-07-25 23:54:27', 'completed'),
(47, 'PO/SPRT/ADIT', '2025-07-28', 2, '123', '2025-07-26', '2025-07-26', '123', 'Unit', '2025-07-28 00:28:18', '2025-07-28 00:28:18', 'pending'),
(52, 'PO/SPRT/ADIT123', '2025-07-29', 1, '123', '2025-07-26', '2025-07-26', '123', 'Unit', '2025-07-29 00:03:39', '2025-07-29 00:03:39', 'pending'),
(58, 'PO/221/KALENG125551', '2025-07-29', 1, 'PO/ASDA/ASU12', '2025-07-28', '2025-07-28', '12', 'Unit', '2025-07-29 00:28:01', '2025-07-29 00:28:01', 'pending'),
(65, 'PO/221/KALENG1255512', '2025-07-29', 1, 'PO/ASDA/ASU12', '2025-07-28', '2025-07-28', '12', 'Unit', '2025-07-29 01:44:12', '2025-07-29 01:44:12', 'pending'),
(66, 'PO/221/asda321', '2025-07-29', 2, 'PO/ASDA/ASU12', '2025-07-28', '2025-07-28', '12', 'Unit', '2025-07-29 01:45:56', '2025-07-29 01:45:56', 'pending'),
(67, 'PO/221/asda321213', '2025-07-29', 1, 'PO/ASDA/ASU12', '2025-07-28', '2025-07-28', '12', 'Unit', '2025-07-29 01:59:17', '2025-07-29 01:59:17', 'pending'),
(84, 'PO/221/23155', '2025-07-30', 2, NULL, NULL, NULL, NULL, 'Unit', '2025-07-29 19:23:48', '2025-07-29 19:23:48', 'pending'),
(85, 'PO/221/231551', '2025-07-30', 1, NULL, NULL, NULL, NULL, 'Sparepart', '2025-07-29 19:24:12', '2025-07-29 19:24:12', 'pending'),
(87, 'PO/221/231553', '2025-07-30', 1, NULL, NULL, NULL, NULL, 'Unit', '2025-07-29 19:25:29', '2025-07-29 19:25:29', 'pending'),
(88, 'PO/221/231553', '2025-07-30', 1, NULL, NULL, NULL, NULL, 'Sparepart', '2025-07-29 19:25:29', '2025-07-29 19:25:29', 'pending'),
(89, 'PO/221/KALENG12355', '2025-07-30', 2, '', '2025-07-30', '2025-07-30', '', 'Attachment & Battery', '2025-07-29 19:35:23', '2025-07-29 19:35:23', 'pending'),
(92, 'PO/221/2315532121', '2025-07-30', 1, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-07-29 19:49:08', '2025-07-29 19:49:08', 'pending'),
(93, 'PO/221/2315531', '2025-07-30', 1, NULL, NULL, NULL, NULL, 'Unit', '2025-07-29 20:16:28', '2025-07-29 20:16:28', 'pending'),
(94, 'PO/221/2315512355', '2025-08-01', 1, NULL, NULL, NULL, NULL, 'Unit', '2025-07-31 21:22:10', '2025-07-31 21:22:10', 'pending'),
(95, 'PO/221/2315512355', '2025-08-01', 1, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-07-31 21:22:10', '2025-07-31 21:22:10', 'pending'),
(96, 'PO/221/2315512355', '2025-08-01', 1, NULL, NULL, NULL, NULL, 'Sparepart', '2025-07-31 21:22:10', '2025-07-31 21:22:10', 'pending'),
(97, 'PO/221/23155123551', '2025-08-01', 2, NULL, NULL, NULL, NULL, 'Unit', '2025-07-31 21:29:00', '2025-07-31 21:29:00', 'pending'),
(102, 'tester', '2025-08-11', 1, 'PO/ASDA/asdaweaw', '2025-08-07', '2025-08-11', NULL, 'Unit', '2025-08-11 02:37:42', '2025-08-11 02:37:42', 'pending'),
(116, 'PO/221/KALENG/poas23', '2025-08-12', 2, NULL, NULL, NULL, NULL, 'Unit', '2025-08-11 18:56:29', '2025-08-11 18:56:29', 'pending'),
(117, 'TESTETEST', '2025-08-12', 2, NULL, NULL, NULL, NULL, 'Unit', '2025-08-11 18:59:05', '2025-08-11 18:59:05', 'pending'),
(118, 'TESTETEST', '2025-08-12', 2, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-08-11 18:59:05', '2025-08-11 18:59:05', 'pending'),
(119, 'TESTETEST', '2025-08-12', 2, NULL, NULL, NULL, NULL, 'Sparepart', '2025-08-11 18:59:05', '2025-08-11 18:59:05', 'pending'),
(120, 'tester324234', '2025-08-12', 2, 'PO/ASDA/asdaweaw', '2025-08-07', '2025-08-11', NULL, 'Unit', '2025-08-11 19:00:10', '2025-08-11 19:00:10', 'pending'),
(121, 'initest', '2025-08-12', 1, 'PO/ASDA/asdaweaw', '2025-08-07', '2025-08-11', NULL, 'Unit', '2025-08-11 19:21:21', '2025-08-11 19:21:21', 'pending'),
(122, 'iniTESTETEST', '2025-08-12', 2, NULL, NULL, NULL, NULL, 'Unit', '2025-08-11 19:22:14', '2025-08-11 19:22:14', 'pending'),
(123, 'iniTESTETESTetteq', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Unit', '2025-08-11 20:15:49', '2025-08-11 20:15:49', 'pending'),
(124, 'iniTESTETESTetteq', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-08-11 20:15:49', '2025-08-11 20:15:49', 'pending'),
(125, 'iniTESTETESTetteq', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Sparepart', '2025-08-11 20:15:49', '2025-08-11 20:15:49', 'pending'),
(130, 'akucumatest', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-08-11 20:27:06', '2025-08-11 20:27:06', 'pending'),
(131, 'akucumatest', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-08-11 20:27:13', '2025-08-11 20:27:13', 'pending'),
(132, 'akucumatest1', '2025-08-12', 2, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-08-11 20:27:48', '2025-08-11 20:27:48', 'pending'),
(137, 'akucumatest3', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Sparepart', '2025-08-11 20:33:56', '2025-08-11 20:33:56', 'pending'),
(138, 'awdiniwane', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Unit', '2025-08-11 21:06:47', '2025-08-11 21:06:47', 'pending'),
(139, 'awdiniwane', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Attachment & Battery', '2025-08-11 21:06:47', '2025-08-11 21:06:47', 'pending'),
(140, 'awdiniwane', '2025-08-12', 1, NULL, NULL, NULL, NULL, 'Sparepart', '2025-08-11 21:06:47', '2025-08-11 21:06:47', 'pending'),
(141, 'awdiniwane', '2025-08-12', 2, NULL, NULL, NULL, NULL, 'Sparepart', '2025-08-11 21:11:47', '2025-08-11 21:11:47', 'pending'),
(142, 'initest123', '2025-08-12', 1, 'PO/ASDA/asdaweaw', '2025-08-07', '2025-08-11', NULL, 'Unit', '2025-08-11 23:45:20', '2025-08-11 23:45:20', 'pending'),
(143, 'PO/221/KALENG/poas231', '2025-08-22', 1, 'PO/ASDA/ADIT', '2025-08-22', '2025-08-22', '', 'Attachment & Battery', '2025-08-22 02:21:14', '2025-08-22 02:21:14', 'pending'),
(144, 'PO-Unit-2025-07-0001', '2025-07-16', 1, NULL, NULL, NULL, NULL, 'Unit', '2025-08-27 19:36:34', NULL, 'pending'),
(145, 'PO-Unit-2025-07-0002', '2025-07-15', 2, NULL, NULL, NULL, NULL, 'Unit', '2025-08-27 19:36:34', NULL, 'approved'),
(146, 'PO-Unit-2025-07-0003', '2025-07-14', 3, NULL, NULL, NULL, NULL, 'Unit', '2025-08-27 19:36:34', NULL, 'completed'),
(147, 'BATERAI', '2025-08-28', 1, '123123', '2025-08-28', '2025-08-28', '1231', 'Attachment & Battery', '2025-08-28 02:35:08', '2025-08-28 02:35:08', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `rbac_audit_log`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `rbac_audit_log`;
CREATE TABLE IF NOT EXISTS `rbac_audit_log` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int(11) DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `performed_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `rbac_audit_log`:
--

--
-- Truncate table before insert `rbac_audit_log`
--

TRUNCATE TABLE `rbac_audit_log`;
-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `rentals`;
CREATE TABLE IF NOT EXISTS `rentals` (
  `rental_id` int(10) UNSIGNED NOT NULL,
  `rental_number` varchar(50) NOT NULL,
  `forklift_id` int(10) UNSIGNED NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_company` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_address` text DEFAULT NULL,
  `contact_person` varchar(255) DEFAULT NULL,
  `rental_type` enum('daily','weekly','monthly','yearly') NOT NULL DEFAULT 'daily',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `rental_duration` int(11) NOT NULL COMMENT 'Duration in days/weeks/months based on rental_type',
  `rental_rate` decimal(12,2) NOT NULL COMMENT 'Rate per period',
  `rental_rate_type` enum('daily','weekly','monthly','yearly') NOT NULL DEFAULT 'daily',
  `total_amount` decimal(15,2) NOT NULL COMMENT 'Subtotal before discounts and taxes',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `final_amount` decimal(15,2) NOT NULL COMMENT 'Final amount after all adjustments',
  `security_deposit` decimal(12,2) NOT NULL DEFAULT 0.00,
  `delivery_required` tinyint(1) NOT NULL DEFAULT 0,
  `delivery_address` text DEFAULT NULL,
  `delivery_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `pickup_required` tinyint(1) NOT NULL DEFAULT 0,
  `pickup_address` text DEFAULT NULL,
  `pickup_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `operator_required` tinyint(1) NOT NULL DEFAULT 0,
  `operator_name` varchar(255) DEFAULT NULL,
  `operator_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `fuel_included` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_included` tinyint(1) NOT NULL DEFAULT 0,
  `insurance_included` tinyint(1) NOT NULL DEFAULT 0,
  `status` enum('draft','confirmed','active','completed','cancelled') NOT NULL DEFAULT 'draft',
  `contract_status` enum('pending','signed','expired') NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','partial','paid','overdue') NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) DEFAULT NULL,
  `payment_terms` varchar(100) DEFAULT NULL,
  `po_number` varchar(100) DEFAULT NULL,
  `contract_file` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `special_terms` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `approved_by` int(10) UNSIGNED DEFAULT NULL,
  `cancelled_by` int(10) UNSIGNED DEFAULT NULL,
  `completed_by` int(10) UNSIGNED DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `confirmed_by` int(10) UNSIGNED DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`rental_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `rentals`:
--

--
-- Truncate table before insert `rentals`
--

TRUNCATE TABLE `rentals`;
-- --------------------------------------------------------

--
-- Table structure for table `reports`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `reports`;
CREATE TABLE IF NOT EXISTS `reports` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `format` varchar(20) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `data_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `reports`:
--

--
-- Truncate table before insert `reports`
--

TRUNCATE TABLE `reports`;
-- --------------------------------------------------------

--
-- Table structure for table `roles`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `is_system_role` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `roles`:
--

--
-- Truncate table before insert `roles`
--

TRUNCATE TABLE `roles`;
--
-- Dumping data for table `roles`
--

INSERT DELAYED IGNORE INTO `roles` (`id`, `name`, `slug`, `description`, `division_id`, `is_system_role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Super Administrator', 'super_admin', 'Full system access with all permissions', 1, 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(2, 'System Administrator', 'system_admin', 'System administration and configuration', 1, 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(3, 'Division Manager', 'division_manager', 'Manager role for division operations', NULL, 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(4, 'Division Staff', 'division_staff', 'Staff role for division operations', NULL, 1, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(5, 'Service Manager', 'service_manager', 'Service Division Manager', 2, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(6, 'Service Technician', 'service_technician', 'Service Division Technician', 2, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(7, 'Operations Manager', 'operations_manager', 'Unit Operations Manager', 3, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(8, 'Driver', 'driver', 'Unit Operations Driver', 3, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(9, 'Marketing Manager', 'marketing_manager', 'Marketing Division Manager', 4, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(10, 'Sales Representative', 'sales_rep', 'Marketing Sales Representative', 4, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(11, 'Warehouse Manager', 'warehouse_manager', 'Warehouse & Assets Manager', 5, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(12, 'Warehouse Staff', 'warehouse_staff', 'Warehouse & Assets Staff', 5, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(13, 'Purchasing Manager', 'purchasing_manager', 'Purchasing Division Manager', 6, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(14, 'Purchasing Staff', 'purchasing_staff', 'Purchasing Division Staff', 6, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(15, 'Perizinan Manager', 'perizinan_manager', 'Licensing Division Manager', 7, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(16, 'Perizinan Staff', 'perizinan_staff', 'Licensing Division Staff', 7, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(17, 'Accounting Manager', 'accounting_manager', 'Accounting Division Manager', 8, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(18, 'Accountant', 'accountant', 'Accounting Division Staff', 8, 0, 1, '2025-08-05 07:01:57', '2025-08-05 07:01:57');

-- --------------------------------------------------------

--
-- Table structure for table `role_permissions`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `role_permissions`;
CREATE TABLE IF NOT EXISTS `role_permissions` (
  `id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `granted` tinyint(1) DEFAULT 1,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_role_permissions_role_id` (`role_id`),
  KEY `idx_role_permissions_permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `role_permissions`:
--

--
-- Truncate table before insert `role_permissions`
--

TRUNCATE TABLE `role_permissions`;
--
-- Dumping data for table `role_permissions`
--

INSERT DELAYED IGNORE INTO `role_permissions` (`id`, `role_id`, `permission_id`, `granted`, `assigned_by`, `assigned_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(2, 1, 2, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(3, 1, 3, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(4, 1, 4, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(5, 1, 5, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(6, 1, 6, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(7, 1, 7, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(8, 1, 8, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(9, 1, 9, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(10, 1, 10, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(11, 1, 11, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(12, 1, 12, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(13, 1, 13, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(14, 1, 14, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(15, 1, 15, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(16, 1, 16, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(17, 1, 17, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(18, 1, 18, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(19, 1, 19, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(20, 1, 20, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(21, 1, 21, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(22, 1, 22, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(23, 1, 23, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(24, 1, 24, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(25, 1, 25, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(26, 1, 26, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(27, 1, 27, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(28, 1, 28, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(29, 1, 29, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(30, 1, 30, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(31, 1, 31, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(32, 1, 32, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(33, 1, 33, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(34, 1, 34, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(35, 1, 35, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(36, 1, 36, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(37, 1, 37, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(38, 1, 38, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(39, 1, 39, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(40, 1, 40, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(41, 1, 41, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(42, 1, 42, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(43, 1, 43, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(44, 1, 44, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(45, 1, 45, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(46, 1, 46, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(47, 1, 47, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(48, 1, 48, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(49, 1, 49, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57'),
(50, 1, 50, 1, NULL, '2025-08-05 07:01:57', '2025-08-05 07:01:57', '2025-08-05 07:01:57');

-- --------------------------------------------------------

--
-- Table structure for table `sparepart`
--
-- Creation: Sep 03, 2025 at 09:06 AM
--

DROP TABLE IF EXISTS `sparepart`;
CREATE TABLE IF NOT EXISTS `sparepart` (
  `id_sparepart` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) NOT NULL,
  `desc_sparepart` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_sparepart`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `sparepart`:
--

--
-- Truncate table before insert `sparepart`
--

TRUNCATE TABLE `sparepart`;
-- --------------------------------------------------------

--
-- Table structure for table `spk`
--
-- Creation: Sep 03, 2025 at 09:08 AM
--

DROP TABLE IF EXISTS `spk`;
CREATE TABLE IF NOT EXISTS `spk` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_spk` varchar(100) NOT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT','TUKAR') NOT NULL DEFAULT 'UNIT',
  `kontrak_id` int(10) UNSIGNED DEFAULT NULL,
  `kontrak_spesifikasi_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
  `jumlah_unit` int(11) DEFAULT 1 COMMENT 'Jumlah unit dalam SPK ini',
  `po_kontrak_nomor` varchar(100) DEFAULT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `kontak` varchar(255) DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `delivery_plan` date DEFAULT NULL,
  `spesifikasi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') NOT NULL DEFAULT 'SUBMITTED',
  `persiapan_unit_mekanik` varchar(100) DEFAULT NULL,
  `persiapan_unit_estimasi_mulai` date DEFAULT NULL,
  `persiapan_unit_estimasi_selesai` date DEFAULT NULL,
  `persiapan_unit_tanggal_approve` datetime DEFAULT NULL,
  `persiapan_unit_id` int(11) DEFAULT NULL,
  `persiapan_aksesoris_tersedia` text DEFAULT NULL,
  `fabrikasi_mekanik` varchar(100) DEFAULT NULL,
  `fabrikasi_estimasi_mulai` date DEFAULT NULL,
  `fabrikasi_estimasi_selesai` date DEFAULT NULL,
  `fabrikasi_tanggal_approve` datetime DEFAULT NULL,
  `fabrikasi_attachment_id` int(11) DEFAULT NULL,
  `painting_mekanik` varchar(100) DEFAULT NULL,
  `painting_estimasi_mulai` date DEFAULT NULL,
  `painting_estimasi_selesai` date DEFAULT NULL,
  `painting_tanggal_approve` datetime DEFAULT NULL,
  `pdi_mekanik` varchar(100) DEFAULT NULL,
  `pdi_estimasi_mulai` date DEFAULT NULL,
  `pdi_estimasi_selesai` date DEFAULT NULL,
  `pdi_tanggal_approve` datetime DEFAULT NULL,
  `pdi_catatan` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `dibuat_oleh` int(11) DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `jenis_perintah_kerja_id` int(11) DEFAULT NULL,
  `tujuan_perintah_kerja_id` int(11) DEFAULT NULL,
  `status_eksekusi_workflow_id` int(11) DEFAULT 1,
  `workflow_notes` text DEFAULT NULL,
  `workflow_created_at` timestamp NULL DEFAULT NULL,
  `workflow_updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_spk_workflow` (`jenis_perintah_kerja_id`,`tujuan_perintah_kerja_id`,`status_eksekusi_workflow_id`),
  KEY `idx_spk_status_workflow` (`status_eksekusi_workflow_id`),
  KEY `fk_spk_kontrak` (`kontrak_id`),
  KEY `fk_spk_kontrak_spesifikasi` (`kontrak_spesifikasi_id`),
  KEY `fk_spk_tujuan_perintah` (`tujuan_perintah_kerja_id`),
  KEY `fk_spk_user` (`dibuat_oleh`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `spk`:
--   `jenis_perintah_kerja_id`
--       `jenis_perintah_kerja` -> `id`
--   `kontrak_id`
--       `kontrak` -> `id`
--   `kontrak_spesifikasi_id`
--       `kontrak_spesifikasi` -> `id`
--   `status_eksekusi_workflow_id`
--       `status_eksekusi_workflow` -> `id`
--   `tujuan_perintah_kerja_id`
--       `tujuan_perintah_kerja` -> `id`
--   `dibuat_oleh`
--       `users` -> `id`
--

--
-- Truncate table before insert `spk`
--

TRUNCATE TABLE `spk`;
--
-- Dumping data for table `spk`
--

INSERT DELAYED IGNORE INTO `spk` (`id`, `nomor_spk`, `jenis_spk`, `kontrak_id`, `kontrak_spesifikasi_id`, `jumlah_unit`, `po_kontrak_nomor`, `pelanggan`, `pic`, `kontak`, `lokasi`, `delivery_plan`, `spesifikasi`, `status`, `persiapan_unit_mekanik`, `persiapan_unit_estimasi_mulai`, `persiapan_unit_estimasi_selesai`, `persiapan_unit_tanggal_approve`, `persiapan_unit_id`, `persiapan_aksesoris_tersedia`, `fabrikasi_mekanik`, `fabrikasi_estimasi_mulai`, `fabrikasi_estimasi_selesai`, `fabrikasi_tanggal_approve`, `fabrikasi_attachment_id`, `painting_mekanik`, `painting_estimasi_mulai`, `painting_estimasi_selesai`, `painting_tanggal_approve`, `pdi_mekanik`, `pdi_estimasi_mulai`, `pdi_estimasi_selesai`, `pdi_tanggal_approve`, `pdi_catatan`, `catatan`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`, `jenis_perintah_kerja_id`, `tujuan_perintah_kerja_id`, `status_eksekusi_workflow_id`, `workflow_notes`, `workflow_created_at`, `workflow_updated_at`) VALUES
(27, 'SPK/202509/001', 'UNIT', NULL, NULL, 2, 'test12345', 'MONORKOBO', 'JAJA', '09324987729', 'BEKASI', '2025-09-03', '{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"PALLET STACKER\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"14\",\"attachment_tipe\":\"PAPER ROLL CLAMP\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"9\",\"mast_id\":\"22\",\"ban_id\":\"6\",\"roda_id\":\"3\",\"valve_id\":\"3\",\"aksesoris\":[],\"persiapan_battery_action\":\"keep_existing\",\"persiapan_battery_id\":\"6\",\"persiapan_charger_action\":\"assign\",\"persiapan_charger_id\":\"12\",\"fabrikasi_attachment_id\":\"15\",\"prepared_units\":[{\"unit_id\":\"1\",\"battery_inventory_id\":\"5\",\"charger_inventory_id\":\"10\",\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-03 09:40:09\"},{\"unit_id\":\"12\",\"battery_inventory_id\":\"6\",\"charger_inventory_id\":\"12\",\"attachment_inventory_id\":\"15\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"a\",\"timestamp\":\"2025-09-03 09:41:18\"}]}', 'IN_PROGRESS', 'JOHANA - DEPI', '2025-09-03', '2025-09-03', '2025-09-03 09:41:03', 12, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\"]', 'JOHANA - DEPI', '2025-09-02', '2025-09-02', '2025-09-03 09:41:10', NULL, 'ARIZAL-EKA', '2025-09-03', '2025-09-03', '2025-09-03 09:41:14', 'JOHANA - DEPI', '2025-09-03', '2025-09-03', '2025-09-03 09:41:18', 'a', NULL, 1, '2025-09-03 09:38:49', '2025-09-04 03:43:23', NULL, NULL, 1, NULL, NULL, NULL),
(28, 'SPK/202509/002', 'UNIT', 44, 19, 2, 'MSI', 'MSI', 'MSI', '09213123123', 'EROPA', NULL, '{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"HAND PALLET\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"41\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"5\",\"mast_id\":\"22\",\"ban_id\":\"6\",\"roda_id\":\"1\",\"valve_id\":\"2\",\"aksesoris\":[],\"persiapan_battery_action\":\"assign\",\"persiapan_battery_id\":\"7\",\"persiapan_charger_action\":\"assign\",\"persiapan_charger_id\":\"14\",\"fabrikasi_attachment_id\":\"15\",\"prepared_units\":[{\"unit_id\":\"1\",\"battery_inventory_id\":\"5\",\"charger_inventory_id\":\"10\",\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-04 04:14:01\"},{\"unit_id\":\"2\",\"battery_inventory_id\":\"7\",\"charger_inventory_id\":\"14\",\"attachment_inventory_id\":\"15\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-04 04:14:39\"}]}', 'COMPLETED', 'JOHANA - DEPI', '2025-09-04', '2025-09-04', '2025-09-04 04:14:20', 2, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]', 'JOHANA - DEPI', '2025-09-04', '2025-09-04', '2025-09-04 04:14:29', NULL, 'ARIZAL-EKA', '2025-09-04', '2025-09-04', '2025-09-04 04:14:34', 'JOHANA - DEPI', '2025-09-04', '2025-09-04', '2025-09-04 04:14:39', 'ok', NULL, 1, '2025-09-04 04:13:09', '2025-09-04 08:53:00', NULL, NULL, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `spk_backup_20250903`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `spk_backup_20250903`;
CREATE TABLE IF NOT EXISTS `spk_backup_20250903` (
  `id` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `nomor_spk` varchar(100) NOT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT','TUKAR') NOT NULL DEFAULT 'UNIT',
  `kontrak_id` int(10) UNSIGNED DEFAULT NULL,
  `kontrak_spesifikasi_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
  `jumlah_unit` int(11) DEFAULT 1 COMMENT 'Jumlah unit dalam SPK ini',
  `po_kontrak_nomor` varchar(100) DEFAULT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `pic` varchar(255) DEFAULT NULL,
  `kontak` varchar(255) DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `delivery_plan` date DEFAULT NULL,
  `spesifikasi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `status` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') NOT NULL DEFAULT 'SUBMITTED',
  `persiapan_unit_mekanik` varchar(100) DEFAULT NULL,
  `persiapan_unit_estimasi_mulai` date DEFAULT NULL,
  `persiapan_unit_estimasi_selesai` date DEFAULT NULL,
  `persiapan_unit_tanggal_approve` datetime DEFAULT NULL,
  `persiapan_unit_id` int(11) DEFAULT NULL,
  `persiapan_aksesoris_tersedia` text DEFAULT NULL,
  `fabrikasi_mekanik` varchar(100) DEFAULT NULL,
  `fabrikasi_estimasi_mulai` date DEFAULT NULL,
  `fabrikasi_estimasi_selesai` date DEFAULT NULL,
  `fabrikasi_tanggal_approve` datetime DEFAULT NULL,
  `fabrikasi_attachment_id` int(11) DEFAULT NULL,
  `painting_mekanik` varchar(100) DEFAULT NULL,
  `painting_estimasi_mulai` date DEFAULT NULL,
  `painting_estimasi_selesai` date DEFAULT NULL,
  `painting_tanggal_approve` datetime DEFAULT NULL,
  `pdi_mekanik` varchar(100) DEFAULT NULL,
  `pdi_estimasi_mulai` date DEFAULT NULL,
  `pdi_estimasi_selesai` date DEFAULT NULL,
  `pdi_tanggal_approve` datetime DEFAULT NULL,
  `pdi_catatan` text DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `dibuat_oleh` int(10) UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `spk_backup_20250903`:
--

--
-- Truncate table before insert `spk_backup_20250903`
--

TRUNCATE TABLE `spk_backup_20250903`;
--
-- Dumping data for table `spk_backup_20250903`
--

INSERT DELAYED IGNORE INTO `spk_backup_20250903` (`id`, `nomor_spk`, `jenis_spk`, `kontrak_id`, `kontrak_spesifikasi_id`, `jumlah_unit`, `po_kontrak_nomor`, `pelanggan`, `pic`, `kontak`, `lokasi`, `delivery_plan`, `spesifikasi`, `status`, `persiapan_unit_mekanik`, `persiapan_unit_estimasi_mulai`, `persiapan_unit_estimasi_selesai`, `persiapan_unit_tanggal_approve`, `persiapan_unit_id`, `persiapan_aksesoris_tersedia`, `fabrikasi_mekanik`, `fabrikasi_estimasi_mulai`, `fabrikasi_estimasi_selesai`, `fabrikasi_tanggal_approve`, `fabrikasi_attachment_id`, `painting_mekanik`, `painting_estimasi_mulai`, `painting_estimasi_selesai`, `painting_tanggal_approve`, `pdi_mekanik`, `pdi_estimasi_mulai`, `pdi_estimasi_selesai`, `pdi_tanggal_approve`, `pdi_catatan`, `catatan`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`) VALUES
(21, 'SPK/202508/001', 'UNIT', 14, 12, 10, 'test/1/1/9', 'PURI NUSA', 'Adit', '082134555233', 'Gemalapik', '2025-08-26', '{\"ban_id\": \"3\", \"mast_id\": \"15\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"LINDE\", \"charger_id\": null, \"model_unit\": null, \"tipe_jenis\": \"SCRUBER\", \"kapasitas_id\": \"42\", \"tipe_unit_id\": \"4\", \"departemen_id\": \"1\", \"jenis_baterai\": null, \"prepared_units\": [{\"catatan\": \"awe\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"8\", \"timestamp\": \"2025-08-26 09:16:08\", \"attachment_id\": \"4\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"CAMERA AI\\\",\\\"LASER FORK\\\",\\\"VOICE ANNOUNCER\\\",\\\"APAR 1 KG\\\",\\\"P3K\\\",\\\"BEACON\\\",\\\"SPARS ARRESTOR\\\"]\"}, {\"catatan\": \"ok\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"7\", \"timestamp\": \"2025-08-27 02:22:28\", \"attachment_id\": \"4\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"CAMERA AI\\\",\\\"CAMERA\\\",\\\"LASER FORK\\\",\\\"VOICE ANNOUNCER\\\",\\\"HORN SPEAKER\\\",\\\"ACRYLIC\\\",\\\"APAR 1 KG\\\",\\\"P3K\\\",\\\"BEACON\\\",\\\"SPARS ARRESTOR\\\"]\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\"}', 'IN_PROGRESS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'ok', 'test', 1, '2025-08-26 08:28:33', '2025-08-27 02:22:28'),
(22, 'SPK/202508/002', 'UNIT', 15, 11, 1, 'test/1/1/10', 'PURI INDAH', 'Adit', '082134555233', 'Gemalapik', '2025-08-27', '{\"ban_id\": \"3\", \"mast_id\": \"15\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"KOMATSU\", \"charger_id\": null, \"model_unit\": null, \"tipe_jenis\": \"DUMP TRUCK\", \"kapasitas_id\": null, \"tipe_unit_id\": \"1\", \"departemen_id\": \"1\", \"jenis_baterai\": null, \"prepared_units\": [{\"catatan\": \"a\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"15\", \"timestamp\": \"2025-08-27 06:51:38\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BACK BUZZER\\\",\\\"SPEED LIMITER\\\",\\\"VOICE ANNOUNCER\\\",\\\"HORN SPEAKER\\\",\\\"HORN KLASON\\\",\\\"BIO METRIC\\\",\\\"APAR 1 KG\\\",\\\"APAR 3 KG\\\",\\\"BEACON\\\"]\", \"battery_inventory_id\": \"5\", \"charger_inventory_id\": \"6\", \"attachment_inventory_id\": \"4\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\", \"persiapan_battery_id\": \"5\", \"persiapan_charger_id\": \"6\", \"fabrikasi_attachment_id\": \"4\"}', 'IN_PROGRESS', 'IYAN', '2025-08-27', '2025-08-27', '2025-08-27 06:51:14', 15, '[\"LAMPU UTAMA\",\"BACK BUZZER\",\"SPEED LIMITER\",\"VOICE ANNOUNCER\",\"HORN SPEAKER\",\"HORN KLASON\",\"BIO METRIC\",\"APAR 1 KG\",\"APAR 3 KG\",\"BEACON\"]', 'ARIZAL-EKA', '2025-08-27', '2025-08-27', '2025-08-27 06:51:25', NULL, 'JOHANA - DEPI', '2025-08-27', '2025-08-27', '2025-08-27 06:51:32', 'SAMSURI-RIKI', '2025-08-27', '2025-08-27', '2025-08-27 06:51:37', 'a', NULL, 1, '2025-08-27 04:14:39', '2025-08-27 15:24:37'),
(23, 'SPK/202508/003', 'UNIT', 16, 15, 1, 'test/1/1/11', 'Sarana Mitra Luas Tbk', 'kaleng', '22131231231', 'Area Kargo Bandara Soekarno-Hatta', '2025-08-27', '{\"ban_id\": \"1\", \"mast_id\": \"15\", \"roda_id\": \"3\", \"valve_id\": \"3\", \"aksesoris\": [], \"merk_unit\": \"KOMATSU\", \"charger_id\": \"8\", \"model_unit\": null, \"tipe_jenis\": \"SCRUBER\", \"kapasitas_id\": \"43\", \"tipe_unit_id\": \"4\", \"departemen_id\": \"2\", \"jenis_baterai\": \"Lead Acid\", \"prepared_units\": [{\"catatan\": \"aa\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"13\", \"timestamp\": \"2025-08-27 09:01:35\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"ROTARY LAMP\\\",\\\"BACK BUZZER\\\",\\\"SENSOR PARKING\\\",\\\"SPEED LIMITER\\\"]\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"4\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\", \"fabrikasi_attachment_id\": \"4\"}', 'IN_PROGRESS', 'IYAN', '2025-08-27', '2025-08-27', '2025-08-27 09:01:05', 13, '[\"LAMPU UTAMA\",\"BLUE SPOT\",\"ROTARY LAMP\",\"BACK BUZZER\",\"SENSOR PARKING\",\"SPEED LIMITER\"]', 'ARIZAL-EKA', '2025-08-27', '2025-08-27', '2025-08-27 09:01:12', NULL, 'JOHANA - DEPI', '2025-08-27', '2025-08-27', '2025-08-27 09:01:26', 'SAMSURI-RIKI', '2025-08-27', '2025-08-27', '2025-08-27 09:01:35', 'aa', NULL, 1, '2025-08-27 09:00:44', '2025-08-27 15:26:16'),
(24, 'SPK/202508/004', 'UNIT', 14, 13, 2, 'test/1/1/9', 'PURI NUSA', 'Adit', '082134555233', 'Gemalapik', '2025-08-27', '{\"ban_id\": \"6\", \"mast_id\": \"15\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"HYUNDAI\", \"charger_id\": \"8\", \"model_unit\": null, \"tipe_jenis\": \"SCRUBER\", \"kapasitas_id\": \"11\", \"tipe_unit_id\": \"4\", \"departemen_id\": \"2\", \"jenis_baterai\": \"Lead Acid\", \"prepared_units\": [{\"catatan\": \"a\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"16\", \"timestamp\": \"2025-08-27 16:53:55\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"HORN KLASON\\\"]\", \"battery_inventory_id\": \"3\", \"charger_inventory_id\": \"6\", \"attachment_inventory_id\": \"15\"}, {\"catatan\": \"ok\", \"mekanik\": \"IYAN\", \"unit_id\": \"17\", \"timestamp\": \"2025-08-30 02:08:03\", \"aksesoris_tersedia\": null, \"battery_inventory_id\": \"3\", \"charger_inventory_id\": \"8\", \"attachment_inventory_id\": \"16\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\", \"persiapan_battery_id\": \"3\", \"persiapan_charger_id\": \"8\", \"fabrikasi_attachment_id\": \"16\", \"persiapan_battery_action\": \"keep_existing\", \"persiapan_charger_action\": \"assign\", \"persiapan_battery_inventory_id\": \"30\", \"persiapan_charger_inventory_id\": \"null\", \"fabrikasi_attachment_inventory_id\": \"null\"}', 'IN_PROGRESS', 'test', '2025-08-30', '2025-08-31', '2025-08-30 02:00:18', 17, NULL, 'JOHANA - DEPI', '2025-08-30', '2025-08-30', '2025-08-30 02:07:39', NULL, 'SAMSURI-RIKI', '2025-08-30', '2025-08-30', '2025-08-30 02:07:52', 'IYAN', '2025-08-30', '2025-08-30', '2025-08-30 02:08:03', 'ok', NULL, 1, '2025-08-27 15:37:29', '2025-08-30 03:42:03'),
(25, 'SPK/202508/005', 'UNIT', 17, 16, 3, 'test/1/1/12', 'LG Cibitung', 'AA', '12312313', 'SAMPING TOL CIBITUNG', '2025-08-28', '{\"ban_id\": \"3\", \"mast_id\": \"12\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"HELI\", \"charger_id\": \"4\", \"model_unit\": null, \"tipe_jenis\": \"PALLET MOVER\", \"kapasitas_id\": \"16\", \"tipe_unit_id\": \"6\", \"departemen_id\": \"2\", \"jenis_baterai\": \"Lead Acid\", \"attachment_merk\": null, \"attachment_tipe\": \"\"}', 'IN_PROGRESS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-08-28 01:54:22', '2025-08-28 01:54:29'),
(26, 'SPK/202509/001', 'UNIT', 41, 0, 2, 'TETTETESS', 'MONORKOBO', 'JAJA', '09324987729', 'BEKASI', '2025-09-01', '{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"HAND PALLET\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"11\",\"attachment_tipe\":\"PAPER ROLL CLAMP\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"8\",\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"3\",\"valve_id\":\"3\",\"aksesoris\":[]}', 'SUBMITTED', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-09-01 04:16:57', '2025-09-01 04:16:57');

-- --------------------------------------------------------

--
-- Table structure for table `spk_component_transactions`
--
-- Creation: Sep 03, 2025 at 08:54 AM
--

DROP TABLE IF EXISTS `spk_component_transactions`;
CREATE TABLE IF NOT EXISTS `spk_component_transactions` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `spk_id` int(10) UNSIGNED NOT NULL,
  `transaction_type` enum('ASSIGN','UNASSIGN','MODIFY') NOT NULL DEFAULT 'ASSIGN',
  `component_type` enum('UNIT','ATTACHMENT','BATTERY','CHARGER') NOT NULL,
  `component_id` int(10) UNSIGNED NOT NULL COMMENT 'ID from respective table (inventory_unit, inventory_attachment)',
  `inventory_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID from inventory_attachment if applicable',
  `mekanik` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_by` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_spk_component_spk` (`spk_id`),
  KEY `idx_spk_component_type` (`component_type`),
  KEY `idx_spk_component_id` (`component_id`),
  KEY `idx_spk_component_inventory` (`inventory_id`),
  KEY `idx_spk_component_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `spk_component_transactions`:
--

--
-- Truncate table before insert `spk_component_transactions`
--

TRUNCATE TABLE `spk_component_transactions`;
--
-- Dumping data for table `spk_component_transactions`
--

INSERT DELAYED IGNORE INTO `spk_component_transactions` (`id`, `spk_id`, `transaction_type`, `component_type`, `component_id`, `inventory_id`, `mekanik`, `catatan`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'ASSIGN', 'UNIT', 1, NULL, 'John Doe', 'Unit assigned for SPK preparation', 1, '2025-08-30 02:22:02', '2025-08-30 02:22:02'),
(2, 1, 'ASSIGN', 'ATTACHMENT', 1, NULL, 'John Doe', 'Forklift attachment assigned', 1, '2025-08-30 02:22:02', '2025-08-30 02:22:02'),
(3, 1, 'ASSIGN', 'BATTERY', 1, NULL, 'John Doe', 'Battery assigned for unit', 1, '2025-08-30 02:22:02', '2025-08-30 02:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `spk_status_history`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `spk_status_history`;
CREATE TABLE IF NOT EXISTS `spk_status_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `spk_id` int(10) UNSIGNED NOT NULL,
  `status_from` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') DEFAULT NULL,
  `status_to` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') NOT NULL,
  `changed_by` int(10) UNSIGNED DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `spk_status_history`:
--

--
-- Truncate table before insert `spk_status_history`
--

TRUNCATE TABLE `spk_status_history`;
--
-- Dumping data for table `spk_status_history`
--

INSERT DELAYED IGNORE INTO `spk_status_history` (`id`, `spk_id`, `status_from`, `status_to`, `changed_by`, `note`, `changed_at`) VALUES
(0, 27, 'READY', 'IN_PROGRESS', 1, 'DI created: DI/202509/001', '2025-09-04 10:28:19'),
(22, 22, 'READY', 'IN_PROGRESS', 1, 'DI created: DI/202508/007', '2025-08-27 15:24:37'),
(23, 23, 'READY', 'IN_PROGRESS', 1, 'DI created: DI/202508/008', '2025-08-27 15:26:16');

-- --------------------------------------------------------

--
-- Table structure for table `spk_units`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `spk_units`;
CREATE TABLE IF NOT EXISTS `spk_units` (
  `id` int(10) UNSIGNED NOT NULL,
  `spk_id` int(10) UNSIGNED NOT NULL,
  `unit_id` int(10) UNSIGNED DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `spk_units`:
--

--
-- Truncate table before insert `spk_units`
--

TRUNCATE TABLE `spk_units`;
-- --------------------------------------------------------

--
-- Table structure for table `status_eksekusi_workflow`
--
-- Creation: Sep 03, 2025 at 08:58 AM
--

DROP TABLE IF EXISTS `status_eksekusi_workflow`;
CREATE TABLE IF NOT EXISTS `status_eksekusi_workflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(30) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `urutan` int(11) NOT NULL,
  `warna` varchar(7) DEFAULT '#6c757d',
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `status_eksekusi_workflow`:
--

--
-- Truncate table before insert `status_eksekusi_workflow`
--

TRUNCATE TABLE `status_eksekusi_workflow`;
--
-- Dumping data for table `status_eksekusi_workflow`
--

INSERT DELAYED IGNORE INTO `status_eksekusi_workflow` (`id`, `kode`, `nama`, `deskripsi`, `urutan`, `warna`, `aktif`, `created_at`) VALUES
(1, 'BELUM_MULAI', 'Belum Mulai', 'SPK belum dikerjakan', 1, '#6c757d', 1, '2025-09-03 08:58:54'),
(2, 'PERSIAPAN', 'Persiapan Unit', 'Sedang mempersiapkan unit', 2, '#ffc107', 1, '2025-09-03 08:58:54'),
(3, 'DALAM_PERJALANAN', 'Dalam Perjalanan', 'Unit sedang dalam perjalanan ke tujuan', 3, '#17a2b8', 1, '2025-09-03 08:58:54'),
(4, 'SAMPAI_LOKASI', 'Sampai di Lokasi', 'Unit sudah sampai di lokasi tujuan', 4, '#28a745', 1, '2025-09-03 08:58:54'),
(5, 'SELESAI', 'Selesai', 'Pekerjaan sudah selesai dikerjakan', 5, '#28a745', 1, '2025-09-03 08:58:54');

-- --------------------------------------------------------

--
-- Table structure for table `status_unit`
--
-- Creation: Sep 03, 2025 at 09:08 AM
--

DROP TABLE IF EXISTS `status_unit`;
CREATE TABLE IF NOT EXISTS `status_unit` (
  `id_status` int(11) NOT NULL,
  `status_unit` varchar(50) NOT NULL,
  PRIMARY KEY (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `status_unit`:
--

--
-- Truncate table before insert `status_unit`
--

TRUNCATE TABLE `status_unit`;
--
-- Dumping data for table `status_unit`
--

INSERT DELAYED IGNORE INTO `status_unit` (`id_status`, `status_unit`) VALUES
(1, 'WORKSHOP-HIDUP'),
(2, 'WORKSHOP-RUSAK'),
(3, 'RENTAL'),
(4, 'UNIT PULANG'),
(5, 'UNIT HARIAN'),
(6, 'BOOKING'),
(7, 'STOCK ASET'),
(8, 'STOCK NON ASET'),
(9, 'JUAL');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--
-- Creation: Sep 03, 2025 at 09:22 AM
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(150) NOT NULL,
  `kontak_person` varchar(100) DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `suppliers`:
--

--
-- Truncate table before insert `suppliers`
--

TRUNCATE TABLE `suppliers`;
--
-- Dumping data for table `suppliers`
--

INSERT DELAYED IGNORE INTO `suppliers` (`id_supplier`, `nama_supplier`, `kontak_person`, `telepon`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'PT. Forklift Jaya Abadi', 'Bapak Budi', '081234567890', NULL, '2025-07-15 20:43:59', NULL),
(2, 'CV. Sinar Baterai', 'Ibu Susan', '081122334455', NULL, '2025-07-15 20:43:59', NULL),
(3, 'Toko Sparepart Maju', 'Pak Eko', '021-555-1234', NULL, '2025-07-15 20:43:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_activity_log`
--
-- Creation: Sep 09, 2025 at 04:48 AM
-- Last update: Sep 09, 2025 at 09:54 AM
--

DROP TABLE IF EXISTS `system_activity_log`;
CREATE TABLE IF NOT EXISTS `system_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(64) NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT 'ID of the affected record',
  `action_type` enum('CREATE','READ','UPDATE','DELETE','EXPORT','IMPORT','LOGIN','LOGOUT','APPROVE','REJECT','SUBMIT','CANCEL','ASSIGN','UNASSIGN','COMPLETE','PRINT','DOWNLOAD') NOT NULL,
  `action_description` varchar(255) NOT NULL COMMENT 'Brief description of what happened',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Previous values (only changed fields)' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'New values (only changed fields)' CHECK (json_valid(`new_values`)),
  `affected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'List of fields that were changed' CHECK (json_valid(`affected_fields`)),
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to users.id',
  `workflow_stage` varchar(50) DEFAULT NULL COMMENT 'Current business stage',
  `is_critical` tinyint(1) DEFAULT 0 COMMENT 'Mark critical business actions',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT') DEFAULT NULL COMMENT 'Application module where activity occurred',
  `submenu_item` varchar(100) DEFAULT NULL COMMENT 'Specific submenu item accessed',
  `business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'LOW' COMMENT 'Business impact level',
  `related_entities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON object storing related entity relationships' CHECK (json_valid(`related_entities`)),
  PRIMARY KEY (`id`),
  KEY `idx_related_entities` (`related_entities`(255))
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `system_activity_log`:
--

--
-- Truncate table before insert `system_activity_log`
--

TRUNCATE TABLE `system_activity_log`;
--
-- Dumping data for table `system_activity_log`
--

INSERT DELAYED IGNORE INTO `system_activity_log` (`id`, `table_name`, `record_id`, `action_type`, `action_description`, `old_values`, `new_values`, `affected_fields`, `user_id`, `workflow_stage`, `is_critical`, `created_at`, `module_name`, `submenu_item`, `business_impact`, `related_entities`) VALUES
(1, 'kontrak', 44, 'CREATE', 'Kontrak baru dibuat dengan nomor PO-CL-0488', NULL, '{\"no_po_marketing\": \"PO-CL-0488\", \"pelanggan\": \"PT Client\", \"status\": \"ACTIVE\"}', '[\"no_po_marketing\", \"pelanggan\", \"status\"]', 1, 'KONTRAK', 1, '2025-09-08 06:43:05', NULL, NULL, 'LOW', NULL),
(2, 'inventory_unit', 1, 'ASSIGN', 'Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan', NULL, '{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}', '[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]', 1, 'KONTRAK', 1, '2025-09-08 06:43:05', NULL, NULL, 'LOW', NULL),
(3, 'inventory_unit', 2, 'ASSIGN', 'Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan', NULL, '{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}', '[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]', 1, 'KONTRAK', 1, '2025-09-08 06:43:05', NULL, NULL, 'LOW', NULL),
(7, 'kontrak', 48, 'DELETE', 'Test delete logging manual', NULL, NULL, NULL, 1, NULL, 0, '2025-09-08 10:08:42', NULL, NULL, 'LOW', NULL),
(8, 'kontrak', 49, 'DELETE', 'Test Delete', '{}', NULL, '[]', 1, NULL, 0, '2025-09-09 04:14:05', NULL, NULL, 'LOW', NULL),
(9, 'kontrak', 48, 'DELETE', 'Kontrak deleted: TEST-DELETE-LOG (Client: Test Client for Delete)', '{\"id\":\"48\",\"no_kontrak\":\"TEST-DELETE-LOG\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Delete\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-08\",\"tanggal_berakhir\":\"2025-12-08\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 17:06:22\",\"diperbarui_pada\":\"2025-09-08 17:06:22\"}', NULL, '[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]', 1, NULL, 1, '2025-09-09 04:16:39', 'MARKETING', NULL, 'LOW', NULL),
(10, 'kontrak', 49, 'DELETE', 'Kontrak deleted: TEST-DELETE-LOG-2 (Client: Test Client for Delete 2)', '{\"id\":\"49\",\"no_kontrak\":\"TEST-DELETE-LOG-2\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Delete 2\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-08\",\"tanggal_berakhir\":\"2025-12-08\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 17:09:00\",\"diperbarui_pada\":\"2025-09-08 17:09:00\"}', NULL, '[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]', 1, NULL, 1, '2025-09-09 04:18:47', 'MARKETING', NULL, 'LOW', NULL),
(11, 'kontrak', 52, 'DELETE', 'Kontrak deleted: TEST-COMPLETE-LOG (Client: Test Complete Logging)', '{\"id\":\"52\",\"no_kontrak\":\"TEST-COMPLETE-LOG\",\"no_po_marketing\":null,\"pelanggan\":\"Test Complete Logging\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-09\",\"tanggal_berakhir\":\"2025-12-09\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 04:27:07\",\"diperbarui_pada\":\"2025-09-09 04:27:07\"}', NULL, '[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]', 1, 'DELETE_CONFIRMED', 1, '2025-09-09 04:28:11', 'MARKETING', NULL, 'HIGH', NULL),
(12, 'kontrak', 123, 'DELETE', 'Test delete with JSON relations', NULL, NULL, NULL, 1, 'DELETE_CONFIRMED', 0, '2025-09-09 04:51:45', 'MARKETING', 'Data Kontrak', 'HIGH', '{\"kontrak\": [123], \"spk\": [456, 789], \"di\": [101112]}'),
(13, 'kontrak', 999, 'CREATE', 'Test kontrak dengan JSON relations implementasi', NULL, NULL, NULL, 1, 'DRAFT', 0, '2025-09-09 04:52:49', 'MARKETING', 'Data Kontrak', 'MEDIUM', '{\"kontrak\": [999], \"spk\": [1001, 1002], \"test_entity\": [555]}'),
(15, 'kontrak', 51, 'DELETE', 'Kontrak deleted: TEST-ALERT-SYSTEM (Client: Test Client for Alert System)', '{\"id\":\"51\",\"no_kontrak\":\"TEST-ALERT-SYSTEM\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Alert System\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-09\",\"tanggal_berakhir\":\"2025-12-09\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 11:19:04\",\"diperbarui_pada\":\"2025-09-09 11:19:04\"}', NULL, '[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]', 1, 'DELETE_CONFIRMED', 1, '2025-09-09 06:28:14', 'MARKETING', 'Data Kontrak', 'HIGH', '{\"kontrak\":[51]}'),
(16, 'kontrak', 46, 'DELETE', 'Kontrak deleted: TEST-1757315452 (Client: Test Client)', '{\"id\":\"46\",\"no_kontrak\":\"TEST-1757315452\",\"no_po_marketing\":\"PO-TEST-1757315452\",\"pelanggan\":\"Test Client\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2024-01-01\",\"tanggal_berakhir\":\"2024-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 07:10:56\",\"diperbarui_pada\":\"2025-09-08 07:10:56\"}', NULL, '[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]', 1, 'DELETE_CONFIRMED', 1, '2025-09-09 06:28:29', 'MARKETING', 'Data Kontrak', 'HIGH', '{\"kontrak\":[46]}'),
(17, 'users', 1, 'LOGOUT', 'User logged out', NULL, NULL, NULL, 1, 'LOGOUT', 0, '2025-09-09 07:28:45', 'USER_MANAGEMENT', 'User Session', 'LOW', '{\"users\":[1]}'),
(18, 'users', 1, 'LOGIN', 'User logged in successfully', NULL, NULL, NULL, 1, 'LOGIN', 0, '2025-09-09 07:29:00', 'USER_MANAGEMENT', 'User Session', 'LOW', '{\"users\":[1]}'),
(19, 'users', 1, 'LOGOUT', 'User logged out', NULL, NULL, NULL, 1, 'LOGOUT', 0, '2025-09-09 08:00:32', 'USER_MANAGEMENT', 'User Session', 'LOW', '{\"users\":[1]}'),
(20, 'users', 1, 'LOGIN', 'User logged in successfully', NULL, NULL, NULL, 1, 'LOGIN', 0, '2025-09-09 08:00:33', 'USER_MANAGEMENT', 'User Session', 'LOW', '{\"users\":[1]}'),
(21, 'kontrak', 53, 'DELETE', 'Kontrak deleted: KNTRK/2209/0002 (Client: IBR)', '{\"id\":\"53\",\"no_kontrak\":\"KNTRK\\/2209\\/0002\",\"no_po_marketing\":\"PO-ADIT110999\",\"pelanggan\":\"IBR\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 06:30:00\",\"diperbarui_pada\":\"2025-09-09 06:30:00\"}', NULL, '[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]', 1, 'DELETE_CONFIRMED', 1, '2025-09-09 09:42:59', 'MARKETING', 'Data Kontrak', 'HIGH', '{\"kontrak\":[53]}'),
(22, 'kontrak', 45, 'DELETE', 'Kontrak deleted: KNTRK/2209/0001 (Client: Sarana Mitra Luas)', '{\"id\":\"45\",\"no_kontrak\":\"KNTRK\\/2209\\/0001\",\"no_po_marketing\":\"PO-ADIT10999\",\"pelanggan\":\"Sarana Mitra Luas\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-09-30\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 06:57:54\",\"diperbarui_pada\":\"2025-09-08 06:57:54\"}', NULL, '[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]', 1, 'DELETE_CONFIRMED', 1, '2025-09-09 09:43:20', 'MARKETING', 'Data Kontrak', 'HIGH', '{\"kontrak\":[45]}'),
(23, 'kontrak', 54, 'CREATE', 'Kontrak created: KNTRK/2209/0001 (Client: Sarana Mitra Luas)', NULL, '{\"no_kontrak\":\"KNTRK\\/2209\\/0001\",\"no_po_marketing\":\"PO-ADIT10999\",\"pelanggan\":\"Sarana Mitra Luas\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}', '[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]', 1, 'DRAFT', 0, '2025-09-09 09:54:01', 'MARKETING', 'Data Kontrak', 'MEDIUM', '{\"kontrak\":[54]}');

-- --------------------------------------------------------

--
-- Table structure for table `system_activity_log_backup`
--
-- Creation: Sep 08, 2025 at 08:42 AM
--

DROP TABLE IF EXISTS `system_activity_log_backup`;
CREATE TABLE IF NOT EXISTS `system_activity_log_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `table_name` varchar(64) NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
  `record_id` int(10) UNSIGNED NOT NULL COMMENT 'ID of the affected record',
  `action_type` enum('CREATE','UPDATE','DELETE','ASSIGN','UNASSIGN','APPROVE','REJECT','COMPLETE','CANCEL') NOT NULL COMMENT 'Type of action performed',
  `action_description` varchar(255) NOT NULL COMMENT 'Brief description of what happened',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Previous values (only changed fields)' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'New values (only changed fields)' CHECK (json_valid(`new_values`)),
  `affected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'List of fields that were changed' CHECK (json_valid(`affected_fields`)),
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK to users.id',
  `session_id` varchar(128) DEFAULT NULL COMMENT 'Session identifier for tracking',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'User IP address',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'Browser/device info (truncated)',
  `request_method` enum('GET','POST','PUT','DELETE','PATCH') DEFAULT NULL COMMENT 'HTTP method used',
  `request_url` varchar(255) DEFAULT NULL COMMENT 'Endpoint that triggered this action',
  `related_kontrak_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related kontrak if applicable',
  `related_spk_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related SPK if applicable',
  `related_di_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related DI if applicable',
  `workflow_stage` varchar(50) DEFAULT NULL COMMENT 'Current business stage',
  `is_critical` tinyint(1) DEFAULT 0 COMMENT 'Mark critical business actions',
  `execution_time_ms` int(10) UNSIGNED DEFAULT NULL COMMENT 'Time taken to execute action (milliseconds)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT') DEFAULT NULL COMMENT 'Application module where activity occurred',
  `feature_name` varchar(100) DEFAULT NULL COMMENT 'Specific feature/page within module',
  `business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'LOW' COMMENT 'Business impact level',
  `compliance_relevant` tinyint(1) DEFAULT 0 COMMENT 'Relevant for compliance/audit',
  `financial_impact` decimal(15,2) DEFAULT NULL COMMENT 'Financial impact of this activity',
  `related_purchase_order_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related PO for purchasing module',
  `related_vendor_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related vendor/supplier',
  `related_customer_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related customer',
  `related_invoice_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related invoice for accounting',
  `related_payment_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related payment record',
  `related_permit_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related permit for perizinan',
  `related_warehouse_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'Related warehouse location',
  `device_type` enum('DESKTOP','MOBILE','TABLET','API') DEFAULT NULL COMMENT 'Device type used',
  `browser_name` varchar(50) DEFAULT NULL COMMENT 'Browser name',
  `operating_system` varchar(50) DEFAULT NULL COMMENT 'Operating system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `system_activity_log_backup`:
--

--
-- Truncate table before insert `system_activity_log_backup`
--

TRUNCATE TABLE `system_activity_log_backup`;
--
-- Dumping data for table `system_activity_log_backup`
--

INSERT DELAYED IGNORE INTO `system_activity_log_backup` (`id`, `table_name`, `record_id`, `action_type`, `action_description`, `old_values`, `new_values`, `affected_fields`, `user_id`, `session_id`, `ip_address`, `user_agent`, `request_method`, `request_url`, `related_kontrak_id`, `related_spk_id`, `related_di_id`, `workflow_stage`, `is_critical`, `execution_time_ms`, `created_at`, `module_name`, `feature_name`, `business_impact`, `compliance_relevant`, `financial_impact`, `related_purchase_order_id`, `related_vendor_id`, `related_customer_id`, `related_invoice_id`, `related_payment_id`, `related_permit_id`, `related_warehouse_id`, `device_type`, `browser_name`, `operating_system`) VALUES
(1, 'kontrak', 44, 'CREATE', 'Kontrak baru dibuat dengan nomor PO-CL-0488', NULL, '{\"no_po_marketing\": \"PO-CL-0488\", \"pelanggan\": \"PT Client\", \"status\": \"ACTIVE\"}', '[\"no_po_marketing\", \"pelanggan\", \"status\"]', 1, NULL, NULL, NULL, NULL, NULL, 44, NULL, NULL, 'KONTRAK', 1, NULL, '2025-09-08 06:43:05', NULL, NULL, 'LOW', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'inventory_unit', 1, 'ASSIGN', 'Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan', NULL, '{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}', '[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]', 1, NULL, NULL, NULL, NULL, NULL, 44, NULL, NULL, 'KONTRAK', 1, NULL, '2025-09-08 06:43:05', NULL, NULL, 'LOW', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'inventory_unit', 2, 'ASSIGN', 'Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan', NULL, '{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}', '[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]', 1, NULL, NULL, NULL, NULL, NULL, 44, NULL, NULL, 'KONTRAK', 1, NULL, '2025-09-08 06:43:05', NULL, NULL, 'LOW', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `system_activity_log_old`
--
-- Creation: Sep 08, 2025 at 08:42 AM
--

DROP TABLE IF EXISTS `system_activity_log_old`;
CREATE TABLE IF NOT EXISTS `system_activity_log_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL COMMENT 'Username yang melakukan aktivitas',
  `user_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'FK ke users table',
  `action_type` enum('CREATE','READ','UPDATE','DELETE','PRINT','DOWNLOAD','LOGIN','LOGOUT') NOT NULL COMMENT 'Jenis aktivitas',
  `table_name` varchar(64) DEFAULT NULL COMMENT 'Nama tabel yang diakses (kontrak, spk, inventory, dll)',
  `record_id` int(10) UNSIGNED DEFAULT NULL COMMENT 'ID record yang diakses',
  `description` text NOT NULL COMMENT 'Deskripsi lengkap aktivitas yang dilakukan',
  `file_name` varchar(255) DEFAULT NULL COMMENT 'Nama file yang di-print/download',
  `file_type` varchar(50) DEFAULT NULL COMMENT 'Jenis file (PDF, Excel, Word, dll)',
  `module_name` varchar(50) DEFAULT NULL COMMENT 'Module/Menu yang diakses (Marketing, Service, dll)',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP address user',
  `user_agent` text DEFAULT NULL COMMENT 'Browser info',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Waktu aktivitas',
  PRIMARY KEY (`id`),
  KEY `idx_username` (`username`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action_type` (`action_type`),
  KEY `idx_table_record` (`table_name`,`record_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_module` (`module_name`),
  KEY `idx_username_date` (`username`,`created_at`),
  KEY `idx_action_date` (`action_type`,`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tabel untuk mencatat semua aktivitas user: CRUD, Print, Download';

--
-- RELATIONSHIPS FOR TABLE `system_activity_log_old`:
--

--
-- Truncate table before insert `system_activity_log_old`
--

TRUNCATE TABLE `system_activity_log_old`;
--
-- Dumping data for table `system_activity_log_old`
--

INSERT DELAYED IGNORE INTO `system_activity_log_old` (`id`, `username`, `user_id`, `action_type`, `table_name`, `record_id`, `description`, `file_name`, `file_type`, `module_name`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 'admin', 1, 'CREATE', 'kontrak', 1, 'Membuat kontrak baru PO-TEST-001', NULL, NULL, 'Marketing', '127.0.0.1', NULL, '2025-09-08 08:18:56'),
(2, 'admin', 1, 'PRINT', 'kontrak', 1, 'Print kontrak PO-TEST-001 ke PDF', NULL, NULL, 'Marketing', '127.0.0.1', NULL, '2025-09-08 08:18:56'),
(3, 'admin', 1, 'DOWNLOAD', NULL, NULL, 'Download laporan Excel kontrak bulanan', NULL, NULL, 'Reports', '127.0.0.1', NULL, '2025-09-08 08:18:56');

-- --------------------------------------------------------

--
-- Table structure for table `tipe_ban`
--
-- Creation: Sep 08, 2025 at 04:15 AM
--

DROP TABLE IF EXISTS `tipe_ban`;
CREATE TABLE IF NOT EXISTS `tipe_ban` (
  `id_ban` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_ban` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ban`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `tipe_ban`:
--

--
-- Truncate table before insert `tipe_ban`
--

TRUNCATE TABLE `tipe_ban`;
--
-- Dumping data for table `tipe_ban`
--

INSERT DELAYED IGNORE INTO `tipe_ban` (`id_ban`, `tipe_ban`) VALUES
(1, 'Solid (Ban Mati)'),
(2, 'Pneumatic (Ban Angin)'),
(3, 'Cushion (Ban Bantal)'),
(4, 'Non-Marking (Ban Anti-Jejak)'),
(5, 'Polyurethane (Ban PU)'),
(6, 'Foam-Filled (Ban Isi Busa)');

-- --------------------------------------------------------

--
-- Table structure for table `tipe_mast`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `tipe_mast`;
CREATE TABLE IF NOT EXISTS `tipe_mast` (
  `id_mast` int(11) NOT NULL,
  `tipe_mast` varchar(100) NOT NULL,
  `tinggi_mast` varchar(50) DEFAULT NULL COMMENT 'Contoh: 4500mm atau 4.5m',
  PRIMARY KEY (`id_mast`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `tipe_mast`:
--

--
-- Truncate table before insert `tipe_mast`
--

TRUNCATE TABLE `tipe_mast`;
--
-- Dumping data for table `tipe_mast`
--

INSERT DELAYED IGNORE INTO `tipe_mast` (`id_mast`, `tipe_mast`, `tinggi_mast`) VALUES
(1, 'Duplex (2-stage FFL) - ZM300 DUPLEX', NULL),
(2, 'Simplex (2-stage mast) - V (3000)', NULL),
(3, 'Simplex (2-stage mast) - V (5000)', NULL),
(4, 'Simplex (2-stage mast) - VM400-30K', NULL),
(5, 'Simplex (2-stage mast) - M300', NULL),
(6, 'Simplex (2-stage mast) - M370', NULL),
(7, 'Simplex (2-stage mast) - M400', NULL),
(8, 'Simplex (2-stage mast) - M450', NULL),
(9, 'Simplex (2-stage mast) - M500', NULL),
(10, 'Simplex (2-stage mast) - M600', NULL),
(11, 'Triplex (3-stage FFL) - FSV(4700)', NULL),
(12, 'Triplex (3-stage FFL) - FSV(6000)', NULL),
(13, 'Triplex (3-stage FFL) - FSVE61 (6000)', NULL),
(14, 'Triplex (3-stage FFL) - ZSM435', NULL),
(15, 'Triplex (3-stage FFL) - ZSM450', NULL),
(16, 'Triplex (3-stage FFL) - ZSM470', NULL),
(17, 'Triplex (3-stage FFL) - ZSM500', NULL),
(18, 'Triplex (3-stage FFL) - ZSM600', NULL),
(19, 'Triplex (3-stage FFL) - ZSM675', NULL),
(20, 'Triplex (3-stage FFL) - ZSM720', NULL),
(21, 'Triplex (3-stage FFL) - ZSM950', NULL),
(22, 'Triplex (3-stage FFL) - ZSM1050', NULL),
(23, 'Duplex (2-stage FFL) - 5M25D47', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tipe_unit`
--
-- Creation: Sep 03, 2025 at 09:09 AM
--

DROP TABLE IF EXISTS `tipe_unit`;
CREATE TABLE IF NOT EXISTS `tipe_unit` (
  `id_tipe_unit` int(11) NOT NULL,
  `tipe` varchar(50) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `id_departemen` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tipe_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `tipe_unit`:
--

--
-- Truncate table before insert `tipe_unit`
--

TRUNCATE TABLE `tipe_unit`;
--
-- Dumping data for table `tipe_unit`
--

INSERT DELAYED IGNORE INTO `tipe_unit` (`id_tipe_unit`, `tipe`, `jenis`, `id_departemen`) VALUES
(1, 'Alat Berat', 'COMPACTOR / VIBRO', 1),
(2, 'Alat Berat', 'DUMP TRUCK', 1),
(3, 'Alat Berat', 'WHEEL LOADER', 1),
(4, 'Alat Kebersihan', 'SCRUBER', 1),
(5, 'Alat Kebersihan', 'SCRUBER', 2),
(6, 'Forklift', 'COUNTER BALANCE', 1),
(7, 'Forklift', 'COUNTER BALANCE', 2),
(8, 'Forklift', 'COUNTER BALANCE', 3),
(9, 'Forklift', 'HAND PALLET', 2),
(10, 'Forklift', 'PALLET MOVER', 2),
(11, 'Forklift', 'PALLET STACKER', 2),
(12, 'Forklift', 'REACH TRUCK', 2),
(13, 'Forklift', 'THREE WHEEL', 2),
(14, 'Kendaraan Industri', 'TOWING', 2),
(15, 'Peralatan Angkat', 'SCISSOR LIFT', 2);

-- --------------------------------------------------------

--
-- Table structure for table `tujuan_perintah_kerja`
--
-- Creation: Sep 03, 2025 at 08:58 AM
--

DROP TABLE IF EXISTS `tujuan_perintah_kerja`;
CREATE TABLE IF NOT EXISTS `tujuan_perintah_kerja` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `jenis_perintah_id` int(11) NOT NULL,
  `kode` varchar(50) NOT NULL,
  `nama` varchar(200) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_jenis_kode` (`jenis_perintah_id`,`kode`),
  KEY `idx_tujuan_jenis` (`jenis_perintah_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `tujuan_perintah_kerja`:
--   `jenis_perintah_id`
--       `jenis_perintah_kerja` -> `id`
--

--
-- Truncate table before insert `tujuan_perintah_kerja`
--

TRUNCATE TABLE `tujuan_perintah_kerja`;
--
-- Dumping data for table `tujuan_perintah_kerja`
--

INSERT DELAYED IGNORE INTO `tujuan_perintah_kerja` (`id`, `jenis_perintah_id`, `kode`, `nama`, `deskripsi`, `aktif`, `created_at`, `updated_at`) VALUES
(1, 1, 'ANTAR_BARU', 'Kontrak Baru', 'Pengantaran unit untuk kontrak baru', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(2, 1, 'ANTAR_TAMBAHAN', 'Unit Tambahan', 'Pengantaran unit tambahan dari kontrak existing', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(3, 1, 'ANTAR_PENGGANTI', 'Unit Pengganti', 'Pengantaran unit pengganti untuk unit bermasalah', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(4, 2, 'TARIK_HABIS_KONTRAK', 'Habis Kontrak', 'Penarikan unit karena kontrak berakhir', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(5, 2, 'TARIK_PINDAH_LOKASI', 'Pindah Lokasi', 'Penarikan unit untuk dipindah ke lokasi lain', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(6, 2, 'TARIK_MAINTENANCE', 'Maintenance', 'Penarikan unit untuk perawatan/perbaikan', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(7, 2, 'TARIK_RUSAK', 'Unit Rusak', 'Penarikan unit karena mengalami kerusakan', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(8, 3, 'TUKAR_UPGRADE', 'Upgrade Unit', 'Penukaran dengan unit yang lebih tinggi spesifikasinya', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(9, 3, 'TUKAR_DOWNGRADE', 'Downgrade Unit', 'Penukaran dengan unit yang lebih rendah spesifikasinya', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(10, 3, 'TUKAR_RUSAK', 'Ganti Unit Rusak', 'Penukaran unit yang mengalami kerusakan', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(11, 3, 'TUKAR_MAINTENANCE', 'Ganti Saat Maintenance', 'Penukaran sementara selama unit di maintenance', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(12, 4, 'RELOKASI_INTERNAL', 'Antar Lokasi Client', 'Pemindahan unit antar lokasi dalam satu perusahaan', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(13, 4, 'RELOKASI_OPTIMASI', 'Optimasi Distribusi', 'Pemindahan unit untuk optimasi distribusi', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54'),
(14, 4, 'RELOKASI_EMERGENCY', 'Kebutuhan Mendadak', 'Pemindahan unit untuk kebutuhan mendadak', 1, '2025-09-03 08:58:54', '2025-09-03 08:58:54');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--
-- Creation: Sep 03, 2025 at 09:07 AM
-- Last update: Sep 09, 2025 at 08:00 AM
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `division_id` int(11) DEFAULT NULL,
  `employee_id` varchar(50) DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `is_super_admin` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `users`:
--

--
-- Truncate table before insert `users`
--

TRUNCATE TABLE `users`;
--
-- Dumping data for table `users`
--

INSERT DELAYED IGNORE INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `avatar`, `division_id`, `employee_id`, `position`, `is_super_admin`, `is_active`, `last_login`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'admin@optima.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 'Administrator', '', NULL, 1, NULL, NULL, 1, 1, NULL, NULL, NULL, '2025-08-05 00:01:57', '2025-09-09 01:00:32'),
(5, 'admindiesel', 'admindiesel@optima.com', '$2y$10$Hs4MEuJSEbxX8lGDuDNmwephtPcBnfxuCEi/aaPYPprfxWnbQiHu6', 'service', 'diesel', '082136033596', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 19:42:47', '2025-08-06 11:41:04'),
(6, 'adminelektrik', 'adminelektrik@optima.com', '$2y$10$Hs4MEuJSEbxX8lGDuDNmwephtPcBnfxuCEi/aaPYPprfxWnbQiHu6', 'service', 'elektrik', '08211111111', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 20:02:28', '2025-08-06 00:13:03'),
(9, 'operational', 'operational@optima.com', '$2y$10$Hs4MEuJSEbxX8lGDuDNmwephtPcBnfxuCEi/aaPYPprfxWnbQiHu6', 'operational', 'sml', '08211111111', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 20:37:37', '2025-08-05 03:40:00'),
(10, 'adminmarketing', 'adminmarketing@optima.com', '$2y$10$yXhHVLd2XoQXmJkVjByQMerMh8ThRtKuxpLCXfoeDqXdA7k163gEC', 'admin', 'marketing1', '08211111111', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 20:39:51', '2025-08-05 18:35:10');

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_all_permissions`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `user_all_permissions`;
CREATE TABLE IF NOT EXISTS `user_all_permissions` (
`user_id` int(11)
,`username` varchar(50)
,`email` varchar(100)
,`first_name` varchar(50)
,`last_name` varchar(50)
,`division_name` varchar(100)
,`permission_id` int(11)
,`permission_name` varchar(100)
,`permission_key` varchar(150)
,`module` varchar(50)
,`category` varchar(50)
,`source_type` varchar(6)
,`source_name` varchar(100)
,`granted` tinyint(4)
);

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE IF NOT EXISTS `user_permissions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `permission_id` int(11) NOT NULL,
  `division_id` int(11) DEFAULT NULL,
  `granted` tinyint(1) DEFAULT 1,
  `reason` varchar(255) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_temporary` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_permissions_user_id` (`user_id`),
  KEY `idx_user_permissions_permission_id` (`permission_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_permissions`:
--

--
-- Truncate table before insert `user_permissions`
--

TRUNCATE TABLE `user_permissions`;
--
-- Dumping data for table `user_permissions`
--

INSERT DELAYED IGNORE INTO `user_permissions` (`id`, `user_id`, `permission_id`, `division_id`, `granted`, `reason`, `assigned_by`, `assigned_at`, `expires_at`, `is_temporary`, `created_at`, `updated_at`) VALUES
(25, 9, 21, NULL, 1, NULL, 1, '2025-08-04 20:37:37', NULL, 0, '2025-08-05 03:37:37', '2025-08-05 03:37:37'),
(26, 9, 23, NULL, 1, NULL, 1, '2025-08-04 20:37:37', NULL, 0, '2025-08-05 03:37:37', '2025-08-05 03:37:37'),
(27, 9, 22, NULL, 1, NULL, 1, '2025-08-04 20:37:37', NULL, 0, '2025-08-05 03:37:37', '2025-08-05 03:37:37'),
(28, 9, 25, NULL, 1, NULL, 1, '2025-08-04 20:37:37', NULL, 0, '2025-08-05 03:37:37', '2025-08-05 03:37:37'),
(29, 9, 24, NULL, 1, NULL, 1, '2025-08-04 20:37:37', NULL, 0, '2025-08-05 03:37:37', '2025-08-05 03:37:37'),
(30, 9, 26, NULL, 1, NULL, 1, '2025-08-04 20:37:37', NULL, 0, '2025-08-05 03:37:37', '2025-08-05 03:37:37'),
(37, 6, 7, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(38, 6, 20, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(39, 6, 19, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(40, 6, 15, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(41, 6, 14, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(42, 6, 18, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(43, 6, 17, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(44, 6, 13, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(45, 6, 12, NULL, 1, NULL, 1, '2025-08-05 11:44:55', NULL, 0, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(46, 5, 7, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(47, 5, 20, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(48, 5, 19, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(49, 5, 15, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(50, 5, 14, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(51, 5, 18, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(52, 5, 17, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(53, 5, 13, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(54, 5, 12, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(55, 5, 16, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(56, 5, 9, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(57, 5, 11, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(58, 5, 10, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(59, 5, 8, NULL, 1, NULL, 1, '2025-08-05 12:52:24', NULL, 0, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(60, 10, 27, NULL, 1, NULL, 1, '2025-08-05 12:52:41', NULL, 0, '2025-08-05 19:52:41', '2025-08-05 19:52:41'),
(61, 10, 30, NULL, 1, NULL, 1, '2025-08-05 12:52:41', NULL, 0, '2025-08-05 19:52:41', '2025-08-05 19:52:41'),
(62, 10, 32, NULL, 1, NULL, 1, '2025-08-05 12:52:41', NULL, 0, '2025-08-05 19:52:41', '2025-08-05 19:52:41'),
(63, 10, 31, NULL, 1, NULL, 1, '2025-08-05 12:52:41', NULL, 0, '2025-08-05 19:52:41', '2025-08-05 19:52:41'),
(64, 10, 28, NULL, 1, NULL, 1, '2025-08-05 12:52:41', NULL, 0, '2025-08-05 19:52:41', '2025-08-05 19:52:41'),
(65, 10, 29, NULL, 1, NULL, 1, '2025-08-05 12:52:41', NULL, 0, '2025-08-05 19:52:41', '2025-08-05 19:52:41');

-- --------------------------------------------------------

--
-- Table structure for table `user_roles`
--
-- Creation: Sep 03, 2025 at 09:32 AM
--

DROP TABLE IF EXISTS `user_roles`;
CREATE TABLE IF NOT EXISTS `user_roles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `division_id` int(11) DEFAULT NULL,
  `assigned_by` int(11) DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT current_timestamp(),
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_roles_user_id` (`user_id`),
  KEY `idx_user_roles_role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- RELATIONSHIPS FOR TABLE `user_roles`:
--

--
-- Truncate table before insert `user_roles`
--

TRUNCATE TABLE `user_roles`;
--
-- Dumping data for table `user_roles`
--

INSERT DELAYED IGNORE INTO `user_roles` (`id`, `user_id`, `role_id`, `division_id`, `assigned_by`, `assigned_at`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
(13, 9, 7, 3, 1, '2025-08-04 20:37:37', NULL, 1, '2025-08-05 03:37:37', '2025-08-05 03:37:37'),
(15, 6, 5, 2, 1, '2025-08-05 11:44:55', NULL, 1, '2025-08-05 18:44:55', '2025-08-05 18:44:55'),
(23, 5, 5, 2, 1, '2025-08-05 12:52:24', NULL, 1, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(24, 5, 6, 2, 1, '2025-08-05 12:52:24', NULL, 1, '2025-08-05 19:52:24', '2025-08-05 19:52:24'),
(25, 10, 9, 4, 1, '2025-08-05 12:52:41', NULL, 1, '2025-08-05 19:52:41', '2025-08-05 19:52:41'),
(35, 1, 1, 1, 1, '2025-08-06 16:26:28', NULL, 1, '2025-08-06 23:26:28', '2025-08-06 23:26:28');

-- --------------------------------------------------------

--
-- Table structure for table `valve`
--
-- Creation: Sep 03, 2025 at 09:26 AM
--

DROP TABLE IF EXISTS `valve`;
CREATE TABLE IF NOT EXISTS `valve` (
  `id_valve` int(11) NOT NULL,
  `jumlah_valve` varchar(50) NOT NULL,
  PRIMARY KEY (`id_valve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- RELATIONSHIPS FOR TABLE `valve`:
--

--
-- Truncate table before insert `valve`
--

TRUNCATE TABLE `valve`;
--
-- Dumping data for table `valve`
--

INSERT DELAYED IGNORE INTO `valve` (`id_valve`, `jumlah_valve`) VALUES
(1, '2 Valve'),
(2, '3 Valve'),
(3, '4 Valve'),
(4, '5 Valve ');

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_spk_workflow`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_spk_workflow`;
CREATE TABLE IF NOT EXISTS `view_spk_workflow` (
`id` int(10) unsigned
,`nomor_spk` varchar(100)
,`jenis_spk` enum('UNIT','ATTACHMENT','TUKAR')
,`kontrak_id` int(10) unsigned
,`kontrak_spesifikasi_id` int(10) unsigned
,`jumlah_unit` int(11)
,`po_kontrak_nomor` varchar(100)
,`pelanggan` varchar(255)
,`pic` varchar(255)
,`kontak` varchar(255)
,`lokasi` varchar(255)
,`delivery_plan` date
,`spesifikasi` longtext
,`status` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED')
,`persiapan_unit_mekanik` varchar(100)
,`persiapan_unit_estimasi_mulai` date
,`persiapan_unit_estimasi_selesai` date
,`persiapan_unit_tanggal_approve` datetime
,`persiapan_unit_id` int(11)
,`persiapan_aksesoris_tersedia` text
,`fabrikasi_mekanik` varchar(100)
,`fabrikasi_estimasi_mulai` date
,`fabrikasi_estimasi_selesai` date
,`fabrikasi_tanggal_approve` datetime
,`fabrikasi_attachment_id` int(11)
,`painting_mekanik` varchar(100)
,`painting_estimasi_mulai` date
,`painting_estimasi_selesai` date
,`painting_tanggal_approve` datetime
,`pdi_mekanik` varchar(100)
,`pdi_estimasi_mulai` date
,`pdi_estimasi_selesai` date
,`pdi_tanggal_approve` datetime
,`pdi_catatan` text
,`catatan` text
,`dibuat_oleh` int(11)
,`dibuat_pada` datetime
,`diperbarui_pada` datetime
,`jenis_perintah_kerja_id` int(11)
,`tujuan_perintah_kerja_id` int(11)
,`status_eksekusi_workflow_id` int(11)
,`workflow_notes` text
,`workflow_created_at` timestamp
,`workflow_updated_at` timestamp
,`jenis_perintah_kode` varchar(20)
,`jenis_perintah_nama` varchar(100)
,`tujuan_perintah_kode` varchar(50)
,`tujuan_perintah_nama` varchar(200)
,`status_eksekusi_kode` varchar(30)
,`status_eksekusi_nama` varchar(100)
,`status_eksekusi_warna` varchar(7)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_activity_log_relations`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `v_activity_log_relations`;
CREATE TABLE IF NOT EXISTS `v_activity_log_relations` (
`id` int(11)
,`table_name` varchar(64)
,`record_id` int(10) unsigned
,`action_type` enum('CREATE','READ','UPDATE','DELETE','EXPORT','IMPORT','LOGIN','LOGOUT','APPROVE','REJECT','SUBMIT','CANCEL','ASSIGN','UNASSIGN','COMPLETE','PRINT','DOWNLOAD')
,`action_description` varchar(255)
,`module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT')
,`submenu_item` varchar(100)
,`workflow_stage` varchar(50)
,`business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL')
,`user_id` int(10) unsigned
,`created_at` timestamp
,`related_entities` longtext
,`related_kontrak` longtext
,`related_spk` longtext
,`related_di` longtext
,`related_po` longtext
);

-- --------------------------------------------------------

--
-- Structure for view `inventory_unit_components` exported as a table
--
DROP TABLE IF EXISTS `inventory_unit_components`;
CREATE TABLE IF NOT EXISTS `inventory_unit_components`(
    `id_inventory_unit` int(10) unsigned NOT NULL DEFAULT '0',
    `no_unit` int(10) unsigned DEFAULT NULL,
    `serial_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Serial Number utama dari pabrikan',
    `model_baterai_id` int(11) DEFAULT NULL,
    `sn_baterai` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `merk_baterai` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `tipe_baterai` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `jenis_baterai` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `model_charger_id` int(11) DEFAULT NULL COMMENT 'FK ke charger',
    `sn_charger` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `merk_charger` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `tipe_charger` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `model_attachment_id` int(11) DEFAULT NULL COMMENT 'FK ke attachment',
    `sn_attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `attachment_tipe` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `attachment_merk` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `attachment_model` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Structure for view `user_all_permissions` exported as a table
--
DROP TABLE IF EXISTS `user_all_permissions`;
CREATE TABLE IF NOT EXISTS `user_all_permissions`(
    `user_id` int(11) NOT NULL DEFAULT '0',
    `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
    `division_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `permission_id` int(11) DEFAULT NULL,
    `permission_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `permission_key` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `module` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `source_type` varchar(6) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '',
    `source_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `granted` tinyint(4) DEFAULT NULL
);

-- --------------------------------------------------------

--
-- Structure for view `view_spk_workflow` exported as a table
--
DROP TABLE IF EXISTS `view_spk_workflow`;
CREATE TABLE IF NOT EXISTS `view_spk_workflow`(
    `id` int(10) unsigned NOT NULL DEFAULT '0',
    `nomor_spk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
    `jenis_spk` enum('UNIT','ATTACHMENT','TUKAR') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'UNIT',
    `kontrak_id` int(10) unsigned DEFAULT NULL,
    `kontrak_spesifikasi_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
    `jumlah_unit` int(11) DEFAULT '1' COMMENT 'Jumlah unit dalam SPK ini',
    `po_kontrak_nomor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `pelanggan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
    `pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `kontak` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `lokasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `delivery_plan` date DEFAULT NULL,
    `spesifikasi` longtext COLLATE utf8mb4_bin DEFAULT NULL,
    `status` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'SUBMITTED',
    `persiapan_unit_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `persiapan_unit_estimasi_mulai` date DEFAULT NULL,
    `persiapan_unit_estimasi_selesai` date DEFAULT NULL,
    `persiapan_unit_tanggal_approve` datetime DEFAULT NULL,
    `persiapan_unit_id` int(11) DEFAULT NULL,
    `persiapan_aksesoris_tersedia` text COLLATE utf8mb4_general_ci DEFAULT NULL,
    `fabrikasi_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `fabrikasi_estimasi_mulai` date DEFAULT NULL,
    `fabrikasi_estimasi_selesai` date DEFAULT NULL,
    `fabrikasi_tanggal_approve` datetime DEFAULT NULL,
    `fabrikasi_attachment_id` int(11) DEFAULT NULL,
    `painting_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `painting_estimasi_mulai` date DEFAULT NULL,
    `painting_estimasi_selesai` date DEFAULT NULL,
    `painting_tanggal_approve` datetime DEFAULT NULL,
    `pdi_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `pdi_estimasi_mulai` date DEFAULT NULL,
    `pdi_estimasi_selesai` date DEFAULT NULL,
    `pdi_tanggal_approve` datetime DEFAULT NULL,
    `pdi_catatan` text COLLATE utf8mb4_general_ci DEFAULT NULL,
    `catatan` text COLLATE utf8mb4_general_ci DEFAULT NULL,
    `dibuat_oleh` int(11) DEFAULT NULL,
    `dibuat_pada` datetime DEFAULT 'current_timestamp()',
    `diperbarui_pada` datetime DEFAULT 'current_timestamp()',
    `jenis_perintah_kerja_id` int(11) DEFAULT NULL,
    `tujuan_perintah_kerja_id` int(11) DEFAULT NULL,
    `status_eksekusi_workflow_id` int(11) DEFAULT '1',
    `workflow_notes` text COLLATE utf8mb4_general_ci DEFAULT NULL,
    `workflow_created_at` timestamp DEFAULT NULL,
    `workflow_updated_at` timestamp DEFAULT NULL,
    `jenis_perintah_kode` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `jenis_perintah_nama` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `tujuan_perintah_kode` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `tujuan_perintah_nama` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `status_eksekusi_kode` varchar(30) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `status_eksekusi_nama` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
    `status_eksekusi_warna` varchar(7) COLLATE utf8mb4_general_ci DEFAULT '#6c757d'
);

-- --------------------------------------------------------

--
-- Structure for view `v_activity_log_relations` exported as a table
--
DROP TABLE IF EXISTS `v_activity_log_relations`;
CREATE TABLE IF NOT EXISTS `v_activity_log_relations`(
    `id` int(11) NOT NULL DEFAULT '0',
    `table_name` varchar(64) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
    `record_id` int(10) unsigned NOT NULL COMMENT 'ID of the affected record',
    `action_type` enum('CREATE','READ','UPDATE','DELETE','EXPORT','IMPORT','LOGIN','LOGOUT','APPROVE','REJECT','SUBMIT','CANCEL','ASSIGN','UNASSIGN','COMPLETE','PRINT','DOWNLOAD') COLLATE utf8mb4_general_ci NOT NULL,
    `action_description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Brief description of what happened',
    `module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Application module where activity occurred',
    `submenu_item` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Specific submenu item accessed',
    `workflow_stage` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Current business stage',
    `business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL') COLLATE utf8mb4_general_ci DEFAULT 'LOW' COMMENT 'Business impact level',
    `user_id` int(10) unsigned DEFAULT NULL COMMENT 'FK to users.id',
    `created_at` timestamp NOT NULL DEFAULT 'current_timestamp()',
    `related_entities` longtext COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON object storing related entity relationships',
    `related_kontrak` longtext COLLATE utf8mb4_bin DEFAULT NULL,
    `related_spk` longtext COLLATE utf8mb4_bin DEFAULT NULL,
    `related_di` longtext COLLATE utf8mb4_bin DEFAULT NULL,
    `related_po` longtext COLLATE utf8mb4_bin DEFAULT NULL
);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `delivery_instructions`
--
ALTER TABLE `delivery_instructions`
  ADD CONSTRAINT `fk_di_jenis_perintah_kerja` FOREIGN KEY (`jenis_perintah_kerja_id`) REFERENCES `jenis_perintah_kerja` (`id`),
  ADD CONSTRAINT `fk_di_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_di_status_eksekusi_workflow` FOREIGN KEY (`status_eksekusi_workflow_id`) REFERENCES `status_eksekusi_workflow` (`id`),
  ADD CONSTRAINT `fk_di_tujuan_perintah_kerja` FOREIGN KEY (`tujuan_perintah_kerja_id`) REFERENCES `tujuan_perintah_kerja` (`id`);

--
-- Constraints for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD CONSTRAINT `fk_delivery_items_di` FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_delivery_items_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inventory_attachment`
--
ALTER TABLE `inventory_attachment`
  ADD CONSTRAINT `fk_inventory_attachment_attachment` FOREIGN KEY (`attachment_id`) REFERENCES `attachment` (`id_attachment`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_attachment_baterai` FOREIGN KEY (`baterai_id`) REFERENCES `baterai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_attachment_charger` FOREIGN KEY (`charger_id`) REFERENCES `charger` (`id_charger`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_attachment_status_unit` FOREIGN KEY (`status_unit`) REFERENCES `status_unit` (`id_status`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `inventory_unit`
--
ALTER TABLE `inventory_unit`
  ADD CONSTRAINT `fk_inventory_unit_departemen` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_unit_kapasitas` FOREIGN KEY (`kapasitas_unit_id`) REFERENCES `kapasitas` (`id_kapasitas`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_unit_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_unit_kontrak_spesifikasi` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_unit_model` FOREIGN KEY (`model_unit_id`) REFERENCES `model_unit` (`id_model_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_unit_status` FOREIGN KEY (`status_unit_id`) REFERENCES `status_unit` (`id_status`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inventory_unit_tipe` FOREIGN KEY (`tipe_unit_id`) REFERENCES `tipe_unit` (`id_tipe_unit`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `kontrak_spesifikasi`
--
ALTER TABLE `kontrak_spesifikasi`
  ADD CONSTRAINT `fk_kontrak_spesifikasi_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `po_items`
--
ALTER TABLE `po_items`
  ADD CONSTRAINT `fk_po_items_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `po_sparepart_items`
--
ALTER TABLE `po_sparepart_items`
  ADD CONSTRAINT `fk_po_sparepart_items_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `po_units`
--
ALTER TABLE `po_units`
  ADD CONSTRAINT `fk_po_units_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `fk_purchase_orders_suppliers` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id_supplier`) ON UPDATE CASCADE;

--
-- Constraints for table `spk`
--
ALTER TABLE `spk`
  ADD CONSTRAINT `fk_spk_jenis_perintah` FOREIGN KEY (`jenis_perintah_kerja_id`) REFERENCES `jenis_perintah_kerja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spk_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spk_kontrak_spesifikasi` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spk_status_eksekusi` FOREIGN KEY (`status_eksekusi_workflow_id`) REFERENCES `status_eksekusi_workflow` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spk_tujuan_perintah` FOREIGN KEY (`tujuan_perintah_kerja_id`) REFERENCES `tujuan_perintah_kerja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_spk_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tujuan_perintah_kerja`
--
ALTER TABLE `tujuan_perintah_kerja`
  ADD CONSTRAINT `tujuan_perintah_kerja_ibfk_1` FOREIGN KEY (`jenis_perintah_id`) REFERENCES `jenis_perintah_kerja` (`id`);


--
-- Metadata
--
USE `phpmyadmin`;

--
-- Metadata for table activity_types
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table attachment
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table baterai
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table charger
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table delivery_instructions
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table delivery_items
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table departemen
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table divisions
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table forklifts
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table inventory_attachment
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table inventory_item_unit_log
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table inventory_spareparts
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table inventory_unit
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table inventory_unit_backup
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table inventory_unit_components
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table jenis_perintah_kerja
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table jenis_roda
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table kapasitas
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table kontrak
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table kontrak_spesifikasi
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table kontrak_status_changes
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table mesin
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table migrations
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table migration_log
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table migration_log_di_workflow
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table model_unit
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table notifications
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table notification_logs
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table optimization_additional_log
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table optimization_log
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table permissions
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table po_items
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table po_sparepart_items
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table po_units
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table purchase_orders
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table rbac_audit_log
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table rentals
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table reports
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table roles
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table role_permissions
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table sparepart
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table spk
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table spk_backup_20250903
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table spk_component_transactions
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table spk_status_history
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table spk_units
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table status_eksekusi_workflow
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table status_unit
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table suppliers
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table system_activity_log
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table system_activity_log_backup
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table system_activity_log_old
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table tipe_ban
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table tipe_mast
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table tipe_unit
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table tujuan_perintah_kerja
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table users
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table user_all_permissions
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table user_permissions
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table user_roles
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table valve
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table view_spk_workflow
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for table v_activity_log_relations
--
-- Error reading data for table phpmyadmin.pma__column_info: #1100 - Table &#039;pma__column_info&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__table_uiprefs: #1100 - Table &#039;pma__table_uiprefs&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__tracking: #1100 - Table &#039;pma__tracking&#039; was not locked with LOCK TABLES

--
-- Metadata for database optima_db
--
-- Error reading data for table phpmyadmin.pma__bookmark: #1100 - Table &#039;pma__bookmark&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__relation: #1100 - Table &#039;pma__relation&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__savedsearches: #1100 - Table &#039;pma__savedsearches&#039; was not locked with LOCK TABLES
-- Error reading data for table phpmyadmin.pma__central_columns: #1100 - Table &#039;pma__central_columns&#039; was not locked with LOCK TABLES
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;