-- MySQL dump 10.13  Distrib 8.4.3, for Win64 (x86_64)
--
-- Host: localhost    Database: optima_ci
-- ------------------------------------------------------
-- Server version	8.4.3

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `_backup_invalid_dates_kontrak_unit`
--

DROP TABLE IF EXISTS `_backup_invalid_dates_kontrak_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_backup_invalid_dates_kontrak_unit` (
  `id` int DEFAULT NULL,
  `kontrak_id` int DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `tanggal_mulai_old` date DEFAULT NULL,
  `tanggal_selesai_old` date DEFAULT NULL,
  `tanggal_tarik_old` date DEFAULT NULL,
  `backup_timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_backup_kontrak_unit_before_sync`
--

DROP TABLE IF EXISTS `_backup_kontrak_unit_before_sync`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_backup_kontrak_unit_before_sync` (
  `id` int unsigned NOT NULL DEFAULT '0',
  `kontrak_id` int unsigned NOT NULL COMMENT 'Foreign key to kontrak table',
  `unit_id` int unsigned NOT NULL COMMENT 'Foreign key to inventory_unit table',
  `tanggal_mulai` date NOT NULL COMMENT 'Unit start date in contract',
  `tanggal_selesai` date DEFAULT NULL COMMENT 'Unit end date in contract',
  `status` enum('ACTIVE','PULLED','REPLACED','INACTIVE','MAINTENANCE','UNDER_REPAIR','TEMP_REPLACED','TEMP_ACTIVE','TEMP_ENDED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVE' COMMENT 'Unit status: ACTIVE=in use, PULLED=returned, REPLACED=swapped, INACTIVE=not in use, MAINTENANCE=servicing, UNDER_REPAIR=being fixed, TEMP_REPLACED=temporarily swapped, TEMP_ACTIVE=temp unit active, TEMP_ENDED=temp period finished',
  `tanggal_tarik` datetime DEFAULT NULL COMMENT 'Date when unit was picked up',
  `stage_tarik` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Stage when unit was marked as DITARIK',
  `tanggal_tukar` datetime DEFAULT NULL COMMENT 'Date when unit was exchanged',
  `unit_pengganti_id` int unsigned DEFAULT NULL COMMENT 'New unit ID that replaces this unit',
  `unit_sebelumnya_id` int unsigned DEFAULT NULL COMMENT 'Previous unit ID that this unit replaces',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int unsigned DEFAULT NULL COMMENT 'User who created this record',
  `updated_by` int unsigned DEFAULT NULL COMMENT 'User who last updated this record',
  `is_temporary` tinyint(1) DEFAULT '0' COMMENT 'True for temporary replacements (TUKAR_MAINTENANCE)',
  `original_unit_id` int unsigned DEFAULT NULL COMMENT 'Original unit ID for temporary replacements (when this is temp unit)',
  `temporary_replacement_unit_id` int unsigned DEFAULT NULL COMMENT 'Temp unit ID when original in maintenance',
  `temporary_replacement_date` datetime DEFAULT NULL COMMENT 'Date temporary replacement started',
  `maintenance_start` datetime DEFAULT NULL COMMENT 'Maintenance start date for TARIK_MAINTENANCE',
  `maintenance_reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Reason for maintenance pull',
  `relocation_from_location_id` int DEFAULT NULL COMMENT 'Previous location for TARIK_PINDAH_LOKASI tracking',
  `relocation_to_location_id` int DEFAULT NULL COMMENT 'New location for TARIK_PINDAH_LOKASI tracking'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `_final_backup_inventory_attachment_20260303`
--

DROP TABLE IF EXISTS `_final_backup_inventory_attachment_20260303`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `_final_backup_inventory_attachment_20260303` (
  `id_inventory_attachment` int NOT NULL DEFAULT '0',
  `tipe_item` enum('attachment','battery','charger') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attachment',
  `no_item` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `po_id` int NOT NULL COMMENT 'Foreign key ke purchase_orders.id_po',
  `id_inventory_unit` int unsigned DEFAULT NULL,
  `attachment_id` int DEFAULT NULL COMMENT 'FK ke attachment',
  `sn_attachment` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `baterai_id` int DEFAULT NULL,
  `sn_baterai` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `charger_id` int DEFAULT NULL COMMENT 'FK ke charger',
  `sn_charger` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kondisi_fisik` enum('Baik','Rusak Ringan','Rusak Berat') COLLATE utf8mb4_unicode_ci DEFAULT 'Baik',
  `kelengkapan` enum('Lengkap','Tidak Lengkap') COLLATE utf8mb4_unicode_ci DEFAULT 'Lengkap',
  `catatan_fisik` text COLLATE utf8mb4_unicode_ci,
  `lokasi_penyimpanan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_status` enum('AVAILABLE','IN_USE','SPARE','MAINTENANCE','BROKEN','RESERVED','SOLD') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'AVAILABLE',
  `tanggal_masuk` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal masuk ke inventory',
  `catatan_inventory` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `activity_types`
--

DROP TABLE IF EXISTS `activity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `business_impact_default` enum('LOW','MEDIUM','HIGH','CRITICAL') COLLATE utf8mb4_unicode_ci DEFAULT 'LOW',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_module_type` (`module_name`,`type_code`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `amendment_unit_rates`
--

DROP TABLE IF EXISTS `amendment_unit_rates`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `amendment_unit_rates` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `amendment_id` int unsigned NOT NULL,
  `unit_id` int unsigned NOT NULL,
  `old_rate` decimal(15,2) NOT NULL DEFAULT '0.00',
  `new_rate` decimal(15,2) NOT NULL DEFAULT '0.00',
  `prorate_old_amount` decimal(15,2) DEFAULT '0.00',
  `prorate_new_amount` decimal(15,2) DEFAULT '0.00',
  `prorate_days_before` int unsigned DEFAULT '0',
  `prorate_days_after` int unsigned DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_amendment_id` (`amendment_id`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_rates` (`old_rate`,`new_rate`),
  CONSTRAINT `fk_unit_rates_amendment` FOREIGN KEY (`amendment_id`) REFERENCES `contract_amendments` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `area_employee_assignments`
--

DROP TABLE IF EXISTS `area_employee_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `area_employee_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `area_id` int NOT NULL,
  `employee_id` int DEFAULT NULL,
  `assignment_type` enum('PRIMARY','BACKUP','TEMPORARY') COLLATE utf8mb4_general_ci DEFAULT 'PRIMARY',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL COMMENT 'NULL for permanent assignment',
  `is_active` tinyint(1) DEFAULT '1',
  `notes` text COLLATE utf8mb4_general_ci,
  `department_scope` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'ALL' COMMENT 'ALL=all departments, ELECTRIC=electric only, DIESEL=diesel only, DIESEL,GASOLINE=diesel+gasoline',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_area_staff_assignment` (`area_id`,`employee_id`,`assignment_type`),
  KEY `idx_area_staff` (`area_id`,`employee_id`),
  KEY `idx_staff_area` (`employee_id`,`area_id`),
  KEY `idx_assignment_active` (`is_active`),
  KEY `idx_department_scope` (`department_scope`),
  CONSTRAINT `fk_area_staff_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_area_staff_staff` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=484 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Staff assignments per area';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `areas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `area_code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A, B, C, etc',
  `area_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Jakarta Utara, Bekasi, Cikarang, etc',
  `area_description` text COLLATE utf8mb4_unicode_ci COMMENT 'Detail coverage wilayah',
  `area_type` enum('CENTRAL','BRANCH') COLLATE utf8mb4_unicode_ci DEFAULT 'BRANCH' COMMENT 'CENTRAL=Pusat (per-dept focus), BRANCH=Cabang (all-dept)',
  `area_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci COMMENT 'GPS coordinates untuk mapping',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_code` (`area_code`),
  KEY `idx_area_code` (`area_code`),
  KEY `idx_area_active` (`is_active`),
  KEY `idx_area_type` (`area_type`),
  CONSTRAINT `areas_chk_1` CHECK (json_valid(`area_coordinates`))
) ENGINE=InnoDB AUTO_INCREMENT=452 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master Areas/Wilayah';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `attachment`
--

DROP TABLE IF EXISTS `attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachment` (
  `id_attachment` int NOT NULL AUTO_INCREMENT,
  `tipe` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `merk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_attachment`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `baterai`
--

DROP TABLE IF EXISTS `baterai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `baterai` (
  `id` int NOT NULL AUTO_INCREMENT,
  `merk_baterai` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_baterai` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_baterai` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charger`
--

DROP TABLE IF EXISTS `charger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charger` (
  `id_charger` int NOT NULL AUTO_INCREMENT,
  `merk_charger` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_charger` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_charger`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `component_timeline`
--

DROP TABLE IF EXISTS `component_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `component_timeline` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `component_type` enum('BATTERY','CHARGER','ATTACHMENT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `component_id` int unsigned NOT NULL COMMENT 'ID dari tabel inventory_batteries/chargers/attachments',
  `event_category` enum('ASSIGNMENT','TRANSFER','MAINTENANCE','STATUS','LOCATION','PURCHASE','DISPOSAL') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'INSTALLED_TO_UNIT, TRANSFERRED, REPAIR, etc',
  `event_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_description` text COLLATE utf8mb4_unicode_ci,
  `from_unit_id` int unsigned DEFAULT NULL,
  `to_unit_id` int unsigned DEFAULT NULL,
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int unsigned DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `performed_by` int DEFAULT NULL,
  `performed_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ct_component` (`component_type`,`component_id`,`performed_at` DESC),
  KEY `idx_ct_unit` (`to_unit_id`,`component_type`),
  KEY `idx_ct_reference` (`reference_type`,`reference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified timeline ??? buku besar komponen (battery/charger/attachment)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_amendments`
--

DROP TABLE IF EXISTS `contract_amendments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_amendments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int unsigned NOT NULL,
  `amendment_type` enum('RATE_CHANGE','UNIT_CHANGE','TERM_EXTENSION','OTHER') COLLATE utf8mb4_unicode_ci DEFAULT 'RATE_CHANGE',
  `effective_date` date NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `status` enum('DRAFT','PENDING','APPROVED','REJECTED','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'DRAFT',
  `prorate_split` json DEFAULT NULL,
  `old_total_value` decimal(15,2) DEFAULT '0.00',
  `new_total_value` decimal(15,2) DEFAULT '0.00',
  `prorate_total` decimal(15,2) DEFAULT '0.00',
  `approved_by` int unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejected_by` int unsigned DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contract_id` (`contract_id`),
  KEY `idx_effective_date` (`effective_date`),
  KEY `idx_status` (`status`),
  KEY `idx_amendment_type` (`amendment_type`),
  CONSTRAINT `fk_amendments_contract` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_ca_contract` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_disconnection_log`
--

DROP TABLE IF EXISTS `contract_disconnection_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_disconnection_log` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kontrak_id` int unsigned NOT NULL,
  `unit_id` int unsigned NOT NULL,
  `stage` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'DI stage when disconnection occurred',
  `disconnected_at` datetime NOT NULL,
  `disconnected_by` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_disconnection_kontrak` (`kontrak_id`),
  KEY `idx_disconnection_unit` (`unit_id`),
  KEY `idx_disconnection_date` (`disconnected_at`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Audit log for contract-unit disconnections (TARIK workflow)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_operator_assignments`
--

DROP TABLE IF EXISTS `contract_operator_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_operator_assignments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int unsigned NOT NULL COMMENT 'Reference to kontrak table',
  `operator_id` int unsigned NOT NULL COMMENT 'Reference to operators table',
  `assignment_start` date NOT NULL COMMENT 'Date operator assignment begins',
  `assignment_end` date DEFAULT NULL COMMENT 'Date operator assignment ends (NULL = ongoing)',
  `actual_end_date` date DEFAULT NULL COMMENT 'Actual date operator stopped (if different from planned)',
  `billing_type` enum('MONTHLY_PACKAGE','DAILY_RATE','HOURLY_RATE') COLLATE utf8mb4_unicode_ci DEFAULT 'MONTHLY_PACKAGE',
  `monthly_billing_rate` decimal(15,2) NOT NULL DEFAULT '0.00' COMMENT 'Monthly rate for this assignment',
  `daily_billing_rate` decimal(10,2) DEFAULT '0.00' COMMENT 'Daily rate (if applicable)',
  `hourly_billing_rate` decimal(10,2) DEFAULT '0.00' COMMENT 'Hourly rate (if applicable)',
  `work_hours_per_day` int DEFAULT '8' COMMENT 'Standard work hours per day',
  `work_days_per_week` int DEFAULT '5' COMMENT 'Work days per week (5 or 6)',
  `overtime_allowed` tinyint(1) DEFAULT '1' COMMENT 'Whether overtime is allowed',
  `overtime_rate_multiplier` decimal(5,2) DEFAULT '1.50' COMMENT 'Overtime rate multiplier',
  `equipment_assigned` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Specific equipment/units operator is assigned to',
  `location_id` int unsigned DEFAULT NULL COMMENT 'Work location (customer_location_id)',
  `shift_schedule` enum('DAY_SHIFT','NIGHT_SHIFT','ROTATING','ON_CALL') COLLATE utf8mb4_unicode_ci DEFAULT 'DAY_SHIFT',
  `status` enum('PENDING','ACTIVE','COMPLETED','CANCELLED','TERMINATED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `performance_rating` decimal(3,2) DEFAULT NULL COMMENT 'Rating 0-5.0 (added after assignment)',
  `performance_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Performance feedback',
  `billing_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Special billing terms or notes',
  `contract_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Assignment terms and conditions',
  `replacement_for_assignment_id` int unsigned DEFAULT NULL COMMENT 'If this is a replacement, ID of original assignment',
  `approved_by` int DEFAULT NULL COMMENT 'User who approved this assignment',
  `approved_at` timestamp NULL DEFAULT NULL COMMENT 'Approval timestamp',
  `rejection_reason` text COLLATE utf8mb4_unicode_ci COMMENT 'If rejected, reason for rejection',
  `created_by` int DEFAULT NULL COMMENT 'User who created this assignment',
  `updated_by` int DEFAULT NULL COMMENT 'User who last updated',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  KEY `idx_assignment_contract` (`contract_id`,`status`),
  KEY `idx_assignment_operator` (`operator_id`,`status`),
  KEY `idx_assignment_dates` (`assignment_start`,`assignment_end`),
  KEY `idx_assignment_status` (`status`),
  KEY `idx_assignment_location` (`location_id`,`status`),
  KEY `idx_assignment_active` (`status`,`assignment_start`,`assignment_end`),
  CONSTRAINT `contract_operator_assignments_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contract_operator_assignments_ibfk_2` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`) ON DELETE RESTRICT,
  CONSTRAINT `fk_coa_contract` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_coa_operator` FOREIGN KEY (`operator_id`) REFERENCES `operators` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks operator assignments to rental contracts for billing and management';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_po_history`
--

DROP TABLE IF EXISTS `contract_po_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_po_history` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int unsigned NOT NULL COMMENT 'Reference to kontrak table',
  `po_number` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Customer Purchase Order number',
  `po_date` date NOT NULL COMMENT 'Date PO was issued by customer',
  `po_value` decimal(15,2) DEFAULT NULL COMMENT 'Total PO amount (if specified by customer)',
  `po_description` text COLLATE utf8mb4_unicode_ci COMMENT 'PO scope or description',
  `effective_from` date NOT NULL COMMENT 'Date this PO becomes active',
  `effective_to` date DEFAULT NULL COMMENT 'Date this PO expires (NULL = current/active)',
  `po_document` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Uploaded PO file path (PDF/image)',
  `document_upload_date` timestamp NULL DEFAULT NULL COMMENT 'When document was uploaded',
  `status` enum('ACTIVE','EXPIRED','SUPERSEDED','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'ACTIVE' COMMENT 'PO status',
  `superseded_by_po_id` int unsigned DEFAULT NULL COMMENT 'If superseded, ID of replacement PO',
  `invoice_count` int DEFAULT '0' COMMENT 'Number of invoices created with this PO',
  `total_invoiced` decimal(15,2) DEFAULT '0.00' COMMENT 'Total amount invoiced under this PO',
  `customer_contact_person` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Customer contact for this PO',
  `customer_email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email for PO correspondence',
  `customer_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Phone for PO queries',
  `notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Additional notes about this PO',
  `internal_notes` text COLLATE utf8mb4_unicode_ci COMMENT 'Internal notes (not for customer view)',
  `tags` json DEFAULT NULL COMMENT 'Tags for categorization',
  `created_by` int unsigned DEFAULT NULL COMMENT 'User who created this record',
  `updated_by` int unsigned DEFAULT NULL COMMENT 'User who last updated',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL COMMENT 'Soft delete timestamp',
  PRIMARY KEY (`id`),
  KEY `superseded_by_po_id` (`superseded_by_po_id`),
  KEY `idx_po_contract` (`contract_id`,`status`),
  KEY `idx_po_effective` (`effective_from`,`effective_to`),
  KEY `idx_po_number` (`po_number`),
  KEY `idx_po_status` (`status`),
  KEY `idx_po_active` (`contract_id`,`status`,`effective_from`,`effective_to`),
  KEY `idx_po_date_range` (`contract_id`,`effective_from`,`effective_to`),
  CONSTRAINT `contract_po_history_ibfk_1` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
  CONSTRAINT `contract_po_history_ibfk_2` FOREIGN KEY (`superseded_by_po_id`) REFERENCES `contract_po_history` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_cph_contract` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tracks multiple PO numbers per contract for customers who provide monthly POs';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_renewal_unit_map`
--

DROP TABLE IF EXISTS `contract_renewal_unit_map`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_renewal_unit_map` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_contract_id` int unsigned NOT NULL,
  `renewal_contract_id` int unsigned NOT NULL,
  `parent_unit_id` int unsigned DEFAULT NULL,
  `renewal_unit_id` int unsigned DEFAULT NULL,
  `action` enum('CARRY_OVER','ADD_NEW','REPLACE','REMOVE') COLLATE utf8mb4_unicode_ci DEFAULT 'CARRY_OVER',
  `old_rate` decimal(15,2) DEFAULT '0.00',
  `new_rate` decimal(15,2) DEFAULT '0.00',
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent_contract` (`parent_contract_id`),
  KEY `idx_renewal_contract` (`renewal_contract_id`),
  KEY `idx_parent_unit` (`parent_unit_id`),
  KEY `idx_renewal_unit` (`renewal_unit_id`),
  CONSTRAINT `fk_unit_map_parent` FOREIGN KEY (`parent_contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_unit_map_renewal` FOREIGN KEY (`renewal_contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_renewal_workflow`
--

DROP TABLE IF EXISTS `contract_renewal_workflow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_renewal_workflow` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `parent_contract_id` int unsigned NOT NULL,
  `renewal_contract_id` int unsigned DEFAULT NULL,
  `status` enum('INITIATED','PENDING_APPROVAL','APPROVED','REJECTED','COMPLETED','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'INITIATED',
  `initiated_by` int unsigned DEFAULT NULL,
  `initiated_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `approved_by` int unsigned DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `rejected_by` int unsigned DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `rejection_reason` text COLLATE utf8mb4_unicode_ci,
  `completed_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_parent_contract` (`parent_contract_id`),
  KEY `idx_renewal_contract` (`renewal_contract_id`),
  KEY `idx_status` (`status`),
  CONSTRAINT `fk_crw_parent` FOREIGN KEY (`parent_contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_crw_renewal` FOREIGN KEY (`renewal_contract_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_renewal_workflow_parent` FOREIGN KEY (`parent_contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_renewal_workflow_renewal` FOREIGN KEY (`renewal_contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `contract_timeline`
--

DROP TABLE IF EXISTS `contract_timeline`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_timeline` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contract_id` int unsigned NOT NULL COMMENT 'FK ke kontrak',
  `event_category` enum('LIFECYCLE','UNIT','DELIVERY','MAINTENANCE','OPERATOR','BILLING','AMENDMENT','DOCUMENT') COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_type` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_description` text COLLATE utf8mb4_unicode_ci,
  `unit_id` int unsigned DEFAULT NULL COMMENT 'Unit terkait (opsional)',
  `reference_type` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reference_id` int unsigned DEFAULT NULL,
  `metadata` json DEFAULT NULL,
  `performed_by` int DEFAULT NULL,
  `performed_at` datetime NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_ctl_contract_time` (`contract_id`,`performed_at` DESC),
  KEY `idx_ctl_category` (`contract_id`,`event_category`,`performed_at` DESC),
  KEY `idx_ctl_unit` (`contract_id`,`unit_id`),
  KEY `idx_ctl_reference` (`reference_type`,`reference_id`),
  KEY `idx_contract_timeline_contract_performed` (`contract_id`,`performed_at`),
  CONSTRAINT `fk_ctl_contract` FOREIGN KEY (`contract_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Unified timeline ??? buku besar kontrak (pertanggungjawaban customer)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Temporary view structure for view `contract_unit_summary`
--

DROP TABLE IF EXISTS `contract_unit_summary`;
/*!50001 DROP VIEW IF EXISTS `contract_unit_summary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `contract_unit_summary` AS SELECT 
 1 AS `kontrak_id`,
 1 AS `no_kontrak`,
 1 AS `pelanggan`,
 1 AS `lokasi`,
 1 AS `alamat_lengkap`,
 1 AS `kontrak_status`,
 1 AS `tanggal_mulai`,
 1 AS `tanggal_berakhir`,
 1 AS `kontrak_total_units`,
 1 AS `active_units`,
 1 AS `tarik_units`,
 1 AS `tukar_units`,
 1 AS `operational_units`,
 1 AS `workflow_units`,
 1 AS `nilai_total`,
 1 AS `jenis_sewa`,
 1 AS `created_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `customer_locations`
--

DROP TABLE IF EXISTS `customer_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_locations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `area_id` int DEFAULT NULL,
  `location_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Kantor Pusat, Pabrik 1, Gudang A, etc',
  `location_code` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `location_type` enum('HEAD_OFFICE','BRANCH','WAREHOUSE','FACTORY') COLLATE utf8mb4_unicode_ci DEFAULT 'BRANCH',
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pic_position` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0' COMMENT 'Primary location for this customer',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_location_active` (`is_active`),
  KEY `idx_customer_locations_customer` (`customer_id`),
  KEY `fk_customer_locations_area` (`area_id`),
  CONSTRAINT `fk_cl_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_cl_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_customer_locations_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_customer_locations_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=400 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Multiple locations per customer';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'SML001, ABC002, etc',
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Sarana Mitra Luas, PT ABC, etc',
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Phone utama perusahaan',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Email utama perusahaan',
  `marketing_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `address` text COLLATE utf8mb4_unicode_ci COMMENT 'Alamat kantor pusat',
  `npwp` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor NPWP perusahaan',
  `billing_address` text COLLATE utf8mb4_unicode_ci COMMENT 'Alamat penagihan',
  `payment_terms` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NET 30, NET 60, COD',
  `credit_limit` decimal(15,2) DEFAULT '0.00' COMMENT 'Batas kredit piutang',
  `industry_type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jenis industri',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Soft delete',
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`),
  UNIQUE KEY `uk_customer_code` (`customer_code`),
  KEY `idx_customer_active` (`is_active`),
  KEY `idx_customer_name` (`customer_name`),
  KEY `idx_customer_status_location` (`customer_code`,`is_active`),
  KEY `idx_customers_name` (`customer_name`),
  KEY `idx_marketing_name` (`marketing_name`)
) ENGINE=InnoDB AUTO_INCREMENT=247 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master Customers/PT Client';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `delivery_instructions`
--

DROP TABLE IF EXISTS `delivery_instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_instructions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nomor_di` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `spk_id` int unsigned DEFAULT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT') COLLATE utf8mb4_unicode_ci DEFAULT 'UNIT',
  `po_kontrak_nomor` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_unicode_ci,
  `jenis_perintah_kerja_id` int DEFAULT NULL,
  `tujuan_perintah_kerja_id` int DEFAULT NULL,
  `status_eksekusi_workflow_id` int DEFAULT '1',
  `dibuat_oleh` int unsigned DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `perencanaan_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval perencanaan pengiriman',
  `estimasi_sampai` date DEFAULT NULL COMMENT 'Estimasi tanggal sampai dari perencanaan',
  `nama_supir` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama supir yang bertugas',
  `no_hp_supir` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor HP supir',
  `no_sim_supir` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor SIM supir',
  `kendaraan` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Jenis/merk kendaraan yang digunakan',
  `no_polisi_kendaraan` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor polisi kendaraan',
  `berangkat_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval berangkat',
  `catatan_berangkat` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan keberangkatan dan kondisi barang',
  `sampai_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval sampai',
  `catatan_sampai` text COLLATE utf8mb4_unicode_ci COMMENT 'Catatan kedatangan dan konfirmasi penerima',
  `status_di` enum('DIAJUKAN','DISETUJUI','PERSIAPAN_UNIT','SIAP_KIRIM','DALAM_PERJALANAN','SAMPAI_LOKASI','SELESAI','DIBATALKAN') COLLATE utf8mb4_unicode_ci DEFAULT 'DIAJUKAN',
  `invoice_generated` tinyint(1) DEFAULT '0' COMMENT 'Flag: 1 if invoice has been auto-generated',
  `invoice_generated_at` datetime DEFAULT NULL COMMENT 'Timestamp when invoice was generated',
  PRIMARY KEY (`id`),
  KEY `fk_di_spk` (`spk_id`),
  KEY `fk_di_jenis_perintah_kerja` (`jenis_perintah_kerja_id`),
  KEY `fk_di_tujuan_perintah_kerja` (`tujuan_perintah_kerja_id`),
  KEY `fk_di_status_eksekusi_workflow` (`status_eksekusi_workflow_id`),
  KEY `idx_delivery_instructions_jenis_spk` (`jenis_spk`),
  KEY `idx_delivery_instructions_spk` (`spk_id`),
  KEY `idx_delivery_instructions_created` (`dibuat_pada`),
  KEY `idx_invoice_automation` (`invoice_generated`,`sampai_tanggal_approve`,`spk_id`,`status_di`),
  CONSTRAINT `fk_di_jenis_perintah_kerja` FOREIGN KEY (`jenis_perintah_kerja_id`) REFERENCES `jenis_perintah_kerja` (`id`),
  CONSTRAINT `fk_di_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_di_status_eksekusi_workflow` FOREIGN KEY (`status_eksekusi_workflow_id`) REFERENCES `status_eksekusi_workflow` (`id`),
  CONSTRAINT `fk_di_tujuan_perintah_kerja` FOREIGN KEY (`tujuan_perintah_kerja_id`) REFERENCES `tujuan_perintah_kerja` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=149 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
ALTER DATABASE `optima_ci` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_di_create_workflow_stages` AFTER INSERT ON `delivery_instructions` FOR EACH ROW BEGIN
    DECLARE v_jenis_kode VARCHAR(20);
    
    
    SELECT kode INTO v_jenis_kode 
    FROM jenis_perintah_kerja 
    WHERE id = NEW.jenis_perintah_kerja_id;
    
    
    IF v_jenis_kode = 'TARIK' THEN
        INSERT INTO di_workflow_stages (di_id, stage_code, stage_name, status) VALUES
        (NEW.id, 'DIAJUKAN', 'DI Diajukan', 'COMPLETED'),
        (NEW.id, 'DISETUJUI', 'DI Disetujui', 'PENDING'),
        (NEW.id, 'PERSIAPAN_UNIT', 'Persiapan Tim & Transportasi', 'PENDING'),
        (NEW.id, 'DALAM_PERJALANAN', 'Dalam Perjalanan ke Lokasi', 'PENDING'),
        (NEW.id, 'UNIT_DITARIK', 'Unit Berhasil Ditarik', 'PENDING'),
        (NEW.id, 'UNIT_PULANG', 'Unit Dalam Perjalanan Pulang', 'PENDING'),
        (NEW.id, 'SAMPAI_KANTOR', 'Unit Sampai di Kantor/Workshop', 'PENDING'),
        (NEW.id, 'SELESAI', 'Proses Penarikan Selesai', 'PENDING');
    ELSEIF v_jenis_kode = 'TUKAR' THEN
        INSERT INTO di_workflow_stages (di_id, stage_code, stage_name, status) VALUES
        (NEW.id, 'DIAJUKAN', 'DI Diajukan', 'COMPLETED'),
        (NEW.id, 'DISETUJUI', 'DI Disetujui', 'PENDING'),
        (NEW.id, 'PERSIAPAN_UNIT', 'Persiapan Unit Baru & Tim', 'PENDING'),
        (NEW.id, 'DALAM_PERJALANAN', 'Dalam Perjalanan ke Lokasi', 'PENDING'),
        (NEW.id, 'UNIT_DITUKAR', 'Unit Berhasil Ditukar', 'PENDING'),
        (NEW.id, 'UNIT_LAMA_PULANG', 'Unit Lama Dalam Perjalanan Pulang', 'PENDING'),
        (NEW.id, 'SAMPAI_KANTOR', 'Unit Lama Sampai di Kantor', 'PENDING'),
        (NEW.id, 'SELESAI', 'Proses Penukaran Selesai', 'PENDING');
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `optima_ci` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
ALTER DATABASE `optima_ci` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_di_status_completed` AFTER UPDATE ON `delivery_instructions` FOR EACH ROW BEGIN
    
    IF OLD.status_di != 'SELESAI' AND NEW.status_di = 'SELESAI' THEN
        UPDATE inventory_unit iu
        JOIN delivery_instruction_items dii ON iu.id_inventory_unit = dii.unit_id
        SET iu.status_unit_id = 7, 
            iu.updated_at = CURRENT_TIMESTAMP
        WHERE dii.delivery_instruction_id = NEW.id 
        AND dii.item_type = 'UNIT'
        AND iu.status_unit_id = 6; 
        
        
        INSERT INTO unit_status_log (
            inventory_unit_id, old_status_id, new_status_id,
            reason, triggered_by, reference_id, created_by
        )
        SELECT 
            iu.id_inventory_unit,
            6, 
            7, 
            CONCAT('DI #', NEW.nomor_di, ' completed - unit delivered'),
            'DI_COMPLETED',
            NEW.id,
            'SYSTEM'
        FROM inventory_unit iu
        JOIN delivery_instruction_items dii ON iu.id_inventory_unit = dii.unit_id
        WHERE dii.delivery_instruction_id = NEW.id 
        AND dii.item_type = 'UNIT'
        AND iu.status_unit_id = 7; 
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `optima_ci` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_delivery_instructions_update_unit` AFTER UPDATE ON `delivery_instructions` FOR EACH ROW BEGIN
        -- Update tanggal_kirim in inventory_unit when DI is updated
        -- Removed kontrak_spesifikasi_id join as system migrated to quotations
        IF NEW.tanggal_kirim IS NOT NULL 
           AND NEW.spk_id IS NOT NULL 
           AND (OLD.tanggal_kirim IS NULL OR OLD.tanggal_kirim != NEW.tanggal_kirim) THEN
            
            UPDATE inventory_unit iu 
            JOIN spk s ON iu.spk_id = s.id
            SET iu.tanggal_kirim = NEW.tanggal_kirim,
                iu.delivery_instruction_id = NEW.id,
                iu.updated_at = CURRENT_TIMESTAMP
            WHERE s.id = NEW.spk_id;
        END IF;
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `delivery_items`
--

DROP TABLE IF EXISTS `delivery_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `di_id` int unsigned NOT NULL,
  `item_type` enum('UNIT','ATTACHMENT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'UNIT',
  `unit_id` int unsigned DEFAULT NULL,
  `parent_unit_id` int DEFAULT NULL,
  `attachment_id` int unsigned DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_delivery_items_di_id` (`di_id`),
  KEY `idx_delivery_items_type` (`item_type`),
  KEY `idx_delivery_items_unit` (`unit_id`),
  KEY `idx_delivery_items_attachment` (`attachment_id`),
  CONSTRAINT `fk_delivery_items_di` FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_delivery_items_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Items untuk delivery instruction';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `departemen`
--

DROP TABLE IF EXISTS `departemen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departemen` (
  `id_departemen` int NOT NULL AUTO_INCREMENT,
  `nama_departemen` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id_departemen`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `di_workflow_stages`
--

DROP TABLE IF EXISTS `di_workflow_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `di_workflow_stages` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `di_id` int unsigned NOT NULL,
  `stage_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stage_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('PENDING','COMPLETED','SKIPPED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `completed_at` datetime DEFAULT NULL,
  `completed_by` int unsigned DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_di_id` (`di_id`),
  KEY `idx_status` (`status`),
  KEY `idx_di_workflow_stages_di` (`di_id`,`status`),
  CONSTRAINT `fk_di_workflow_stages_di` FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `divisions`
--

DROP TABLE IF EXISTS `divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `divisions` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'STF001, STF002, etc',
  `staff_name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `staff_role` enum('ADMIN','SUPERVISOR','FOREMAN','MECHANIC','MECHANIC_SERVICE_AREA','MECHANIC_UNIT_PREP','MECHANIC_FABRICATION','HELPER') COLLATE utf8mb4_unicode_ci NOT NULL,
  `job_description` text COLLATE utf8mb4_unicode_ci,
  `work_location` enum('CENTRAL','BRANCH','BOTH') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departemen_id` int DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `hire_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  `nik` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'NIK KTP',
  `birth_date` date DEFAULT NULL COMMENT 'Tanggal lahir',
  `gender` enum('M','F') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Untuk transfer gaji',
  `bank_account_number` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `emergency_contact_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'K3 requirement',
  `emergency_contact_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `resignation_date` date DEFAULT NULL COMMENT 'Tanggal resign',
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_code` (`staff_code`),
  UNIQUE KEY `uk_staff_code` (`staff_code`),
  KEY `idx_staff_role` (`staff_role`),
  KEY `idx_staff_active` (`is_active`),
  KEY `idx_staff_departemen` (`departemen_id`),
  KEY `idx_staff_role_departemen` (`staff_role`,`departemen_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`),
  CONSTRAINT `fk_emp_dept` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Master Staff/Karyawan';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_attachments`
--

DROP TABLE IF EXISTS `inventory_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_attachments` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `item_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A0001, A0002, etc',
  `attachment_type_id` int DEFAULT NULL COMMENT 'FK ke attachment (attachment_types)',
  `serial_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `max_capacity` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '2000 kg',
  `purchase_order_id` int DEFAULT NULL COMMENT 'FK ke purchase_orders',
  `inventory_unit_id` int unsigned DEFAULT NULL COMMENT 'FK ke inventory_unit (jika terpasang)',
  `physical_condition` enum('GOOD','MINOR_DAMAGE','MAJOR_DAMAGE') COLLATE utf8mb4_unicode_ci DEFAULT 'GOOD',
  `completeness` enum('COMPLETE','INCOMPLETE') COLLATE utf8mb4_unicode_ci DEFAULT 'COMPLETE',
  `physical_notes` text COLLATE utf8mb4_unicode_ci,
  `storage_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_location_id` int unsigned DEFAULT NULL COMMENT 'FK ke warehouse_locations',
  `status` enum('AVAILABLE','IN_USE','SPARE','MAINTENANCE','BROKEN','RESERVED','SOLD') COLLATE utf8mb4_unicode_ci DEFAULT 'AVAILABLE',
  `received_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_attachment_item_number` (`item_number`),
  KEY `idx_attachment_unit` (`inventory_unit_id`),
  KEY `idx_attachment_status` (`status`),
  KEY `idx_attachment_type` (`attachment_type_id`),
  KEY `fk_ia_warehouse` (`warehouse_location_id`),
  CONSTRAINT `fk_ia_attachment_type` FOREIGN KEY (`attachment_type_id`) REFERENCES `attachment` (`id_attachment`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ia_unit` FOREIGN KEY (`inventory_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ia_warehouse` FOREIGN KEY (`warehouse_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=528 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Inventory attachment ??? split dari inventory_attachment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_batteries`
--

DROP TABLE IF EXISTS `inventory_batteries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_batteries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `item_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'B0001, B0002, etc',
  `battery_type_id` int DEFAULT NULL COMMENT 'FK ke baterai (battery_types)',
  `serial_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `voltage` decimal(5,1) DEFAULT NULL COMMENT '48V, 36V, 24V',
  `ampere_hour` int DEFAULT NULL COMMENT '560Ah, 620Ah',
  `purchase_order_id` int DEFAULT NULL COMMENT 'FK ke purchase_orders',
  `inventory_unit_id` int unsigned DEFAULT NULL COMMENT 'FK ke inventory_unit (jika terpasang)',
  `physical_condition` enum('GOOD','MINOR_DAMAGE','MAJOR_DAMAGE') COLLATE utf8mb4_unicode_ci DEFAULT 'GOOD',
  `completeness` enum('COMPLETE','INCOMPLETE') COLLATE utf8mb4_unicode_ci DEFAULT 'COMPLETE',
  `physical_notes` text COLLATE utf8mb4_unicode_ci,
  `storage_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_location_id` int unsigned DEFAULT NULL COMMENT 'FK ke warehouse_locations',
  `status` enum('AVAILABLE','IN_USE','SPARE','MAINTENANCE','BROKEN','RESERVED','SOLD') COLLATE utf8mb4_unicode_ci DEFAULT 'AVAILABLE',
  `received_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_battery_item_number` (`item_number`),
  KEY `idx_battery_unit` (`inventory_unit_id`),
  KEY `idx_battery_status` (`status`),
  KEY `idx_battery_type` (`battery_type_id`),
  KEY `fk_ib_warehouse` (`warehouse_location_id`),
  CONSTRAINT `fk_ib_battery_type` FOREIGN KEY (`battery_type_id`) REFERENCES `baterai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ib_unit` FOREIGN KEY (`inventory_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ib_warehouse` FOREIGN KEY (`warehouse_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2931 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Inventory battery ??? split dari inventory_attachment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_chargers`
--

DROP TABLE IF EXISTS `inventory_chargers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_chargers` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `item_number` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'C0001, C0002, etc',
  `charger_type_id` int DEFAULT NULL COMMENT 'FK ke charger (charger_types)',
  `serial_number` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `input_voltage` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '220V AC',
  `output_voltage` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '48V DC',
  `output_ampere` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '80A',
  `purchase_order_id` int DEFAULT NULL COMMENT 'FK ke purchase_orders',
  `inventory_unit_id` int unsigned DEFAULT NULL COMMENT 'FK ke inventory_unit (jika terpasang)',
  `physical_condition` enum('GOOD','MINOR_DAMAGE','MAJOR_DAMAGE') COLLATE utf8mb4_unicode_ci DEFAULT 'GOOD',
  `completeness` enum('COMPLETE','INCOMPLETE') COLLATE utf8mb4_unicode_ci DEFAULT 'COMPLETE',
  `physical_notes` text COLLATE utf8mb4_unicode_ci,
  `storage_location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `warehouse_location_id` int unsigned DEFAULT NULL COMMENT 'FK ke warehouse_locations',
  `status` enum('AVAILABLE','IN_USE','SPARE','MAINTENANCE','BROKEN','RESERVED','SOLD') COLLATE utf8mb4_unicode_ci DEFAULT 'AVAILABLE',
  `received_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_charger_item_number` (`item_number`),
  KEY `idx_charger_unit` (`inventory_unit_id`),
  KEY `idx_charger_status` (`status`),
  KEY `idx_charger_type` (`charger_type_id`),
  KEY `fk_ic_warehouse` (`warehouse_location_id`),
  CONSTRAINT `fk_ic_charger_type` FOREIGN KEY (`charger_type_id`) REFERENCES `charger` (`id_charger`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ic_unit` FOREIGN KEY (`inventory_unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_ic_warehouse` FOREIGN KEY (`warehouse_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Inventory charger ??? split dari inventory_attachment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_item_unit_log`
--

DROP TABLE IF EXISTS `inventory_item_unit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_item_unit_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_inventory_attachment` int NOT NULL,
  `id_inventory_unit` int NOT NULL,
  `action` enum('assign','remove') COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_spareparts`
--

DROP TABLE IF EXISTS `inventory_spareparts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_spareparts` (
  `id` int NOT NULL,
  `sparepart_id` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `lokasi_rak` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `inventory_unit`
--

DROP TABLE IF EXISTS `inventory_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_unit` (
  `id_inventory_unit` int unsigned NOT NULL AUTO_INCREMENT,
  `no_unit` int unsigned DEFAULT NULL,
  `no_unit_na` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nomor unit untuk Non-Asset (format: NA-001 to NA-500, reusable)',
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Serial Number utama dari pabrikan',
  `id_po` int DEFAULT NULL COMMENT 'Foreign Key ke tabel purchase_orders',
  `tahun_unit` year DEFAULT NULL,
  `status_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `lokasi_unit` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `departemen_id` int DEFAULT NULL COMMENT 'FK ke tabel departemen',
  `tanggal_kirim` datetime DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `harga_sewa_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per bulan',
  `rate_changed_at` datetime DEFAULT NULL COMMENT 'Last rate change timestamp',
  `harga_sewa_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per hari',
  `kontrak_id` int unsigned DEFAULT NULL COMMENT 'Foreign key ke tabel kontrak',
  `on_hire_date` date DEFAULT NULL COMMENT 'Date unit was hired out',
  `off_hire_date` date DEFAULT NULL COMMENT 'Date unit was returned',
  `customer_id` int DEFAULT NULL,
  `customer_location_id` int DEFAULT NULL,
  `area_id` int DEFAULT NULL,
  `tipe_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel tipe_unit',
  `model_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel model_unit (sudah termasuk merk)',
  `kapasitas_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel kapasitas',
  `model_mast_id` int DEFAULT NULL COMMENT 'FK ke tabel tipe_mast',
  `tinggi_mast` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Contoh: 4500mm atau 4.5m',
  `sn_mast` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `model_mesin_id` int DEFAULT NULL COMMENT 'FK ke tabel mesin (sudah termasuk merk)',
  `sn_mesin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roda_id` int DEFAULT NULL COMMENT 'FK ke tabel jenis_roda',
  `ban_id` int DEFAULT NULL COMMENT 'FK ke tabel tipe_ban',
  `valve_id` int DEFAULT NULL COMMENT 'FK ke tabel valve',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `aksesoris` longtext COLLATE utf8mb4_unicode_ci,
  `spk_id` int unsigned DEFAULT NULL,
  `delivery_instruction_id` int unsigned DEFAULT NULL,
  `di_workflow_id` int DEFAULT NULL,
  `workflow_status` enum('TERSEDIA','STOCK_ASET','DISEWA','DALAM_PENGIRIMAN','MAINTENANCE_IN_PROGRESS','MAINTENANCE_WITH_REPLACEMENT','UNDER_REPAIR','RELOCATING','TEMPORARY_RENTAL','DECOMMISSIONED','RUSAK','HILANG') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hour_meter` decimal(10,1) DEFAULT NULL COMMENT 'Current hour meter reading (supports decimal)',
  `contract_disconnect_date` datetime DEFAULT NULL,
  `contract_disconnect_stage` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_temporary_assignment` tinyint(1) DEFAULT '0' COMMENT 'True if unit is temporary replacement',
  `maintenance_location` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Workshop/location during maintenance',
  `temporary_for_contract_id` int unsigned DEFAULT NULL COMMENT 'Original contract ID if temporary replacement',
  `expected_return_date` datetime DEFAULT NULL COMMENT 'Expected return date for maintenance/temporary',
  `asset_tag` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Label asset fisik (barcode/QR)',
  `acquisition_cost` decimal(15,2) DEFAULT NULL COMMENT 'Harga beli unit',
  `depreciation_method` enum('STRAIGHT_LINE','DECLINING') COLLATE utf8mb4_unicode_ci DEFAULT 'STRAIGHT_LINE',
  `useful_life_years` int DEFAULT NULL COMMENT 'Umur manfaat (tahun)',
  `salvage_value` decimal(15,2) DEFAULT NULL COMMENT 'Nilai residu akhir umur',
  `fuel_type` enum('DIESEL','LPG','ELECTRIC','GASOLINE') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ownership_status` enum('OWNED','LEASED','CONSIGNMENT') COLLATE utf8mb4_unicode_ci DEFAULT 'OWNED',
  `warehouse_location_id` int unsigned DEFAULT NULL COMMENT 'FK ke warehouse_locations',
  PRIMARY KEY (`id_inventory_unit`),
  UNIQUE KEY `idx_no_unit_na` (`no_unit_na`),
  KEY `fk_inventory_unit_departemen` (`departemen_id`),
  KEY `fk_inventory_unit_tipe` (`tipe_unit_id`),
  KEY `fk_inventory_unit_model` (`model_unit_id`),
  KEY `fk_inventory_unit_kapasitas` (`kapasitas_unit_id`),
  KEY `fk_inventory_unit_spk` (`spk_id`),
  KEY `fk_inventory_unit_delivery_instruction` (`delivery_instruction_id`),
  KEY `idx_unit_workflow` (`di_workflow_id`),
  KEY `idx_unit_workflow_status` (`workflow_status`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_customer_location_id` (`customer_location_id`),
  KEY `idx_inventory_unit_kontrak` (`kontrak_id`),
  KEY `fk_inventory_unit_area` (`area_id`),
  KEY `idx_inventory_unit_status` (`status_unit_id`),
  KEY `idx_inventory_status_dept` (`status_unit_id`,`departemen_id`),
  KEY `idx_inventory_serial_status` (`serial_number`,`status_unit_id`),
  KEY `idx_inventory_dates` (`created_at`,`updated_at`),
  KEY `idx_inventory_tanggal_kirim` (`tanggal_kirim`),
  KEY `idx_inventory_workflow` (`workflow_status`,`di_workflow_id`),
  KEY `idx_inventory_customer_location` (`customer_id`,`customer_location_id`,`area_id`),
  KEY `idx_inventory_kontrak_detail` (`kontrak_id`,`status_unit_id`),
  KEY `idx_inventory_po_status` (`id_po`,`status_unit_id`),
  KEY `idx_inventory_unit_workflow` (`workflow_status`),
  KEY `idx_inventory_unit_created` (`created_at`),
  KEY `idx_inv_temp_cont` (`temporary_for_contract_id`),
  KEY `idx_inv_expected_return` (`expected_return_date`),
  KEY `idx_hour_meter` (`hour_meter`),
  KEY `idx_no_unit_na_pattern` (`no_unit_na`(10)),
  KEY `fk_iu_warehouse` (`warehouse_location_id`),
  KEY `idx_inventory_unit_customer` (`customer_id`,`status_unit_id`),
  KEY `idx_inventory_unit_area` (`area_id`,`status_unit_id`),
  CONSTRAINT `fk_inventory_unit_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`),
  CONSTRAINT `fk_inventory_unit_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_customer_location` FOREIGN KEY (`customer_location_id`) REFERENCES `customer_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_delivery_instruction` FOREIGN KEY (`delivery_instruction_id`) REFERENCES `delivery_instructions` (`id`),
  CONSTRAINT `fk_inventory_unit_departemen` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_departemen_new` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kapasitas` FOREIGN KEY (`kapasitas_unit_id`) REFERENCES `kapasitas` (`id_kapasitas`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kapasitas_new` FOREIGN KEY (`kapasitas_unit_id`) REFERENCES `kapasitas` (`id_kapasitas`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kontrak_new` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_model` FOREIGN KEY (`model_unit_id`) REFERENCES `model_unit` (`id_model_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_model_new` FOREIGN KEY (`model_unit_id`) REFERENCES `model_unit` (`id_model_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`),
  CONSTRAINT `fk_inventory_unit_status` FOREIGN KEY (`status_unit_id`) REFERENCES `status_unit` (`id_status`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_status_new` FOREIGN KEY (`status_unit_id`) REFERENCES `status_unit` (`id_status`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_temp_contract` FOREIGN KEY (`temporary_for_contract_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_inventory_unit_tipe` FOREIGN KEY (`tipe_unit_id`) REFERENCES `tipe_unit` (`id_tipe_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_tipe_new` FOREIGN KEY (`tipe_unit_id`) REFERENCES `tipe_unit` (`id_tipe_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_iu_warehouse` FOREIGN KEY (`warehouse_location_id`) REFERENCES `warehouse_locations` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4990 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Data unit utama - komponen disimpan di inventory_attachment';
/*!40101 SET character_set_client = @saved_cs_client */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_unit_bi` BEFORE INSERT ON `inventory_unit` FOR EACH ROW BEGIN
            -- Set status to KONTRAK (3) if unit is linked to a contract
            -- Removed kontrak_spesifikasi_id check as system migrated to quotation_specifications
            IF NEW.kontrak_id IS NOT NULL THEN
                SET NEW.status_unit_id = 3;
            END IF;
        END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `optima_ci` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_unit_location_sync` BEFORE UPDATE ON `inventory_unit` FOR EACH ROW BEGIN
    
    
    
    IF OLD.kontrak_id IS NULL AND NEW.kontrak_id IS NOT NULL THEN
        
        IF NEW.lokasi_unit IS NULL OR NEW.lokasi_unit = '' THEN
            SET NEW.lokasi_unit = 'Workshop';
        END IF;
    END IF;
    
    
    
    
    IF NEW.status_unit_id = 7 AND (OLD.status_unit_id IS NULL OR OLD.status_unit_id != 7) THEN
        
        IF NEW.kontrak_id IS NOT NULL THEN
            SET NEW.lokasi_unit = (
                SELECT CONCAT(cl.location_name, ' - ', cl.city)
                FROM kontrak k
                JOIN customer_locations cl ON k.customer_location_id = cl.id
                WHERE k.id = NEW.kontrak_id
                LIMIT 1
            );
            
            
            IF NEW.lokasi_unit IS NULL THEN
                SET NEW.lokasi_unit = 'Customer Location (kontrak_id: ' + NEW.kontrak_id + ')';
            END IF;
        END IF;
    END IF;
    
    
    
    
    IF NEW.status_unit_id = 9 AND (OLD.status_unit_id IS NULL OR OLD.status_unit_id != 9) THEN
        SET NEW.lokasi_unit = 'Workshop';
    END IF;
    
    
    
    
    IF NEW.status_unit_id = 8 AND (OLD.statmysqldump: Couldn't execute 'SHOW FIELDS FROM `inventory_unit_components`': View 'optima_ci.inventory_unit_components' references invalid table(s) or column(s) or function(s) or definer/invoker of view lack rights to use them (1356)
us_unit_id IS NULL OR OLD.status_unit_id != 8) THEN
        SET NEW.lokasi_unit = 'Workshop';
    END IF;
    
    
    
    
    IF OLD.kontrak_id IS NOT NULL AND NEW.kontrak_id IS NULL THEN
        SET NEW.status_unit_id = 1; 
        SET NEW.lokasi_unit = 'Workshop';
    END IF;
    
    
    
    
    IF NEW.status_unit_id IN (1, 2) AND (OLD.status_unit_id IS NULL OR OLD.status_unit_id NOT IN (1, 2)) THEN
        
        IF NEW.lokasi_unit IS NULL OR NEW.lokasi_unit = '' THEN
            SET NEW.lokasi_unit = 'Workshop';
        END IF;
    END IF;
    
    
    
    
    IF NEW.status_unit_id IN (4, 5) THEN
        
        IF NEW.lokasi_unit IS NULL OR NEW.lokasi_unit = '' THEN
            SET NEW.lokasi_unit = 'Workshop';
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
ALTER DATABASE `optima_ci` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_unit_attachment_sync` AFTER UPDATE ON `inventory_unit` FOR EACH ROW BEGIN
    -- Sync status_unit_id changes to inventory_attachment
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        -- FIXED: Changed 'status_unit' to 'attachment_status'
        UPDATE inventory_attachment 
        SET attachment_status = NEW.status_unit_id, 
            updated_at = NOW()
        WHERE id_inventory_unit = NEW.id_inventory_unit;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_unit_status_sync` AFTER UPDATE ON `inventory_unit` FOR EACH ROW BEGIN
    -- Sync status_unit_id changes to inventory_attachment
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        -- FIXED: Changed 'status_unit' to 'attachment_status'
        UPDATE inventory_attachment 
        SET attachment_status = NEW.status_unit_id
        WHERE id_inventory_unit = NEW.id_inventory_unit;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Temporary view structure for view `inventory_unit_components`
--

DROP TABLE IF EXISTS `inventory_unit_components`;
