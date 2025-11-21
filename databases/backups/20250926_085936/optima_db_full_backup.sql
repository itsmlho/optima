-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: optima_db
-- ------------------------------------------------------
-- Server version	10.4.32-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `activity_types`
--

DROP TABLE IF EXISTS `activity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `activity_types` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activity_types`
--

LOCK TABLES `activity_types` WRITE;
/*!40000 ALTER TABLE `activity_types` DISABLE KEYS */;
INSERT INTO `activity_types` VALUES (1,'PURCHASING','PO_CREATE','Purchase Order Created','New purchase order created','HIGH',1,'2025-09-08 06:54:41'),(2,'PURCHASING','PO_APPROVE','Purchase Order Approved','Purchase order approved by authorized person','HIGH',1,'2025-09-08 06:54:41'),(3,'PURCHASING','PO_REJECT','Purchase Order Rejected','Purchase order rejected','MEDIUM',1,'2025-09-08 06:54:41'),(4,'PURCHASING','PO_CANCEL','Purchase Order Cancelled','Purchase order cancelled','HIGH',1,'2025-09-08 06:54:41'),(5,'PURCHASING','VENDOR_ADD','Vendor Added','New vendor/supplier added to system','MEDIUM',1,'2025-09-08 06:54:41'),(6,'PURCHASING','VENDOR_UPDATE','Vendor Updated','Vendor information updated','LOW',1,'2025-09-08 06:54:41'),(7,'PURCHASING','QUOTATION_REQUEST','Quotation Requested','Quotation requested from vendor','MEDIUM',1,'2025-09-08 06:54:41'),(8,'PURCHASING','QUOTATION_RECEIVE','Quotation Received','Quotation received from vendor','MEDIUM',1,'2025-09-08 06:54:41'),(9,'WAREHOUSE','STOCK_IN','Stock In','Items received into warehouse','MEDIUM',1,'2025-09-08 06:54:41'),(10,'WAREHOUSE','STOCK_OUT','Stock Out','Items issued from warehouse','MEDIUM',1,'2025-09-08 06:54:41'),(11,'WAREHOUSE','STOCK_TRANSFER','Stock Transfer','Items transferred between locations','MEDIUM',1,'2025-09-08 06:54:41'),(12,'WAREHOUSE','STOCK_ADJUSTMENT','Stock Adjustment','Stock quantity adjusted','HIGH',1,'2025-09-08 06:54:41'),(13,'WAREHOUSE','LOCATION_CREATE','Location Created','New warehouse location created','LOW',1,'2025-09-08 06:54:41'),(14,'WAREHOUSE','INVENTORY_COUNT','Inventory Count','Physical inventory count performed','HIGH',1,'2025-09-08 06:54:41'),(15,'WAREHOUSE','DAMAGE_REPORT','Damage Reported','Damaged items reported','MEDIUM',1,'2025-09-08 06:54:41'),(16,'MARKETING','LEAD_CREATE','Lead Created','New sales lead created','MEDIUM',1,'2025-09-08 06:54:41'),(17,'MARKETING','LEAD_CONVERT','Lead Converted','Lead converted to opportunity','HIGH',1,'2025-09-08 06:54:41'),(18,'MARKETING','QUOTE_GENERATE','Quote Generated','Sales quotation generated','MEDIUM',1,'2025-09-08 06:54:41'),(19,'MARKETING','CONTRACT_CREATE','Contract Created','New contract/kontrak created','HIGH',1,'2025-09-08 06:54:41'),(20,'MARKETING','CONTRACT_APPROVE','Contract Approved','Contract approved','CRITICAL',1,'2025-09-08 06:54:41'),(21,'MARKETING','CONTRACT_SIGN','Contract Signed','Contract signed by customer','CRITICAL',1,'2025-09-08 06:54:41'),(22,'MARKETING','UNIT_ASSIGN','Unit Assigned','Unit assigned to contract','HIGH',1,'2025-09-08 06:54:41'),(23,'SERVICE','SPK_CREATE','SPK Created','Service work order (SPK) created','HIGH',1,'2025-09-08 06:54:41'),(24,'SERVICE','SPK_START','SPK Started','Work on SPK started','MEDIUM',1,'2025-09-08 06:54:41'),(25,'SERVICE','SPK_COMPLETE','SPK Completed','SPK work completed','HIGH',1,'2025-09-08 06:54:41'),(26,'SERVICE','MAINTENANCE_SCHEDULE','Maintenance Scheduled','Maintenance scheduled for unit','MEDIUM',1,'2025-09-08 06:54:41'),(27,'SERVICE','MAINTENANCE_COMPLETE','Maintenance Completed','Maintenance work completed','MEDIUM',1,'2025-09-08 06:54:41'),(28,'SERVICE','REPAIR_REQUEST','Repair Requested','Repair service requested','MEDIUM',1,'2025-09-08 06:54:41'),(29,'SERVICE','PART_USED','Parts Used','Spare parts used in service','LOW',1,'2025-09-08 06:54:41'),(30,'OPERATIONAL','DI_CREATE','Delivery Instruction Created','New delivery instruction created','HIGH',1,'2025-09-08 06:54:41'),(31,'OPERATIONAL','DISPATCH','Unit Dispatched','Unit dispatched for delivery','HIGH',1,'2025-09-08 06:54:41'),(32,'OPERATIONAL','DELIVERY_COMPLETE','Delivery Completed','Unit delivered to customer','CRITICAL',1,'2025-09-08 06:54:41'),(33,'OPERATIONAL','PICKUP_SCHEDULE','Pickup Scheduled','Unit pickup scheduled','MEDIUM',1,'2025-09-08 06:54:41'),(34,'OPERATIONAL','PICKUP_COMPLETE','Pickup Completed','Unit picked up from customer','HIGH',1,'2025-09-08 06:54:41'),(35,'OPERATIONAL','ROUTE_OPTIMIZE','Route Optimized','Delivery route optimized','LOW',1,'2025-09-08 06:54:41'),(36,'ACCOUNTING','INVOICE_CREATE','Invoice Created','New invoice created','HIGH',1,'2025-09-08 06:54:41'),(37,'ACCOUNTING','INVOICE_SEND','Invoice Sent','Invoice sent to customer','MEDIUM',1,'2025-09-08 06:54:41'),(38,'ACCOUNTING','PAYMENT_RECEIVE','Payment Received','Payment received from customer','CRITICAL',1,'2025-09-08 06:54:41'),(39,'ACCOUNTING','PAYMENT_OVERDUE','Payment Overdue','Payment marked as overdue','HIGH',1,'2025-09-08 06:54:41'),(40,'ACCOUNTING','EXPENSE_RECORD','Expense Recorded','Business expense recorded','MEDIUM',1,'2025-09-08 06:54:41'),(41,'ACCOUNTING','JOURNAL_ENTRY','Journal Entry','Accounting journal entry created','MEDIUM',1,'2025-09-08 06:54:41'),(42,'ACCOUNTING','RECONCILIATION','Bank Reconciliation','Bank account reconciled','HIGH',1,'2025-09-08 06:54:41'),(43,'PERIZINAN','PERMIT_APPLY','Permit Application','New permit application submitted','HIGH',1,'2025-09-08 06:54:41'),(44,'PERIZINAN','PERMIT_APPROVE','Permit Approved','Permit application approved','CRITICAL',1,'2025-09-08 06:54:41'),(45,'PERIZINAN','PERMIT_REJECT','Permit Rejected','Permit application rejected','HIGH',1,'2025-09-08 06:54:41'),(46,'PERIZINAN','PERMIT_RENEW','Permit Renewed','Existing permit renewed','HIGH',1,'2025-09-08 06:54:41'),(47,'PERIZINAN','PERMIT_EXPIRE','Permit Expired','Permit expired','CRITICAL',1,'2025-09-08 06:54:41'),(48,'PERIZINAN','DOCUMENT_UPLOAD','Document Uploaded','Supporting document uploaded','MEDIUM',1,'2025-09-08 06:54:41'),(49,'PERIZINAN','COMPLIANCE_CHECK','Compliance Check','Regulatory compliance check performed','HIGH',1,'2025-09-08 06:54:41'),(50,'ADMIN','USER_CREATE','User Created','New user account created','MEDIUM',1,'2025-09-08 06:54:41'),(51,'ADMIN','USER_DEACTIVATE','User Deactivated','User account deactivated','HIGH',1,'2025-09-08 06:54:41'),(52,'ADMIN','ROLE_ASSIGN','Role Assigned','Role assigned to user','HIGH',1,'2025-09-08 06:54:41'),(53,'ADMIN','PERMISSION_GRANT','Permission Granted','Permission granted to user/role','HIGH',1,'2025-09-08 06:54:41'),(54,'ADMIN','SYSTEM_BACKUP','System Backup','System backup performed','CRITICAL',1,'2025-09-08 06:54:41'),(55,'ADMIN','CONFIG_CHANGE','Configuration Changed','System configuration changed','HIGH',1,'2025-09-08 06:54:41'),(56,'DASHBOARD','DASHBOARD_VIEW','Dashboard Viewed','Dashboard page accessed','LOW',1,'2025-09-08 06:54:41'),(57,'REPORTS','REPORT_GENERATE','Report Generated','Business report generated','MEDIUM',1,'2025-09-08 06:54:41'),(58,'REPORTS','REPORT_EXPORT','Report Exported','Report exported to file','MEDIUM',1,'2025-09-08 06:54:41'),(59,'REPORTS','REPORT_SCHEDULE','Report Scheduled','Automatic report scheduled','LOW',1,'2025-09-08 06:54:41');
/*!40000 ALTER TABLE `activity_types` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `area_staff_assignments`
--

DROP TABLE IF EXISTS `area_staff_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `area_staff_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_id` int(11) NOT NULL,
  `staff_id` int(11) NOT NULL,
  `assignment_type` enum('PRIMARY','BACKUP','TEMPORARY') DEFAULT 'PRIMARY',
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL COMMENT 'NULL for permanent assignment',
  `is_active` tinyint(1) DEFAULT 1,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_area_staff_assignment` (`area_id`,`staff_id`,`assignment_type`),
  KEY `idx_area_staff` (`area_id`,`staff_id`),
  KEY `idx_staff_area` (`staff_id`,`area_id`),
  KEY `idx_assignment_active` (`is_active`),
  CONSTRAINT `fk_area_staff_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_area_staff_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Staff assignments per area';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area_staff_assignments`
--

LOCK TABLES `area_staff_assignments` WRITE;
/*!40000 ALTER TABLE `area_staff_assignments` DISABLE KEYS */;
INSERT INTO `area_staff_assignments` VALUES (1,1,1,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(2,1,2,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(3,1,3,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(4,2,1,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(5,2,2,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(6,2,3,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(7,3,1,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(8,3,2,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(9,3,3,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(10,4,1,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(11,4,2,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(12,4,3,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28'),(16,4,4,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(17,1,5,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(18,2,6,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(19,3,7,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(20,4,8,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(21,1,9,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(22,2,10,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(23,3,11,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(24,4,12,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(25,1,13,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(26,2,14,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(27,3,15,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(28,4,16,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42'),(29,1,17,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42');
/*!40000 ALTER TABLE `area_staff_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `areas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `area_code` varchar(10) NOT NULL COMMENT 'A, B, C, etc',
  `area_name` varchar(100) NOT NULL COMMENT 'Jakarta Utara, Bekasi, Cikarang, etc',
  `area_description` text DEFAULT NULL COMMENT 'Detail coverage wilayah',
  `area_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'GPS coordinates untuk mapping' CHECK (json_valid(`area_coordinates`)),
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_code` (`area_code`),
  KEY `idx_area_code` (`area_code`),
  KEY `idx_area_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master Areas/Wilayah';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `areas`
--

LOCK TABLES `areas` WRITE;
/*!40000 ALTER TABLE `areas` DISABLE KEYS */;
INSERT INTO `areas` VALUES (1,'A','Jakarta Utara','Meliputi wilayah Jakarta Utara dan sekitarnya',NULL,1,'2025-09-25 03:49:57','2025-09-25 03:49:57'),(2,'B','Bekasi','Meliputi wilayah Bekasi, Cikarang, dan sekitarnya',NULL,1,'2025-09-25 03:49:57','2025-09-25 03:49:57'),(3,'C','Tangerang','Meliputi wilayah Tangerang dan sekitarnya',NULL,1,'2025-09-25 03:49:57','2025-09-25 03:49:57'),(4,'D','Jakarta Selatan','Meliputi wilayah Jakarta Selatan dan sekitarnya',NULL,1,'2025-09-25 03:49:57','2025-09-25 03:49:57');
/*!40000 ALTER TABLE `areas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `attachment`
--

DROP TABLE IF EXISTS `attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `attachment` (
  `id_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `tipe` varchar(100) NOT NULL,
  `merk` varchar(100) NOT NULL,
  `model` varchar(100) NOT NULL,
  PRIMARY KEY (`id_attachment`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `attachment`
--

LOCK TABLES `attachment` WRITE;
/*!40000 ALTER TABLE `attachment` DISABLE KEYS */;
INSERT INTO `attachment` VALUES (1,'FORK POSITIONER','CASCADE','120K-FPS-CO82'),(2,'FORK POSITIONER','CASCADE','65K-FPS'),(3,'PAPER ROLL CLAMP','CASCADE','77F-RCP-01C'),(4,'PAPER ROLL CLAMP','CASCADE','90F-RCP'),(5,'SIDE SHIFTER','CASCADE','50D-BCS-64A'),(6,'SIDE SHIFTER','CASCADE','50D-BCS-64B'),(7,'FORKLIFT SCALE','COMPULOAD','CL 2000'),(8,'PAPER ROLL CLAMP','HELI','ZJ22H-B5'),(9,'PAPER roll CLAMP','HELI','ZJ33H-B5'),(10,'FORK','HELI','FORK');
/*!40000 ALTER TABLE `attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup_inventory_unit_original`
--

DROP TABLE IF EXISTS `backup_inventory_unit_original`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_inventory_unit_original` (
  `id_inventory_unit` int(10) unsigned NOT NULL DEFAULT 0,
  `no_unit` int(10) unsigned DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL COMMENT 'Serial Number utama dari pabrikan',
  `id_po` int(11) DEFAULT NULL COMMENT 'Foreign Key ke tabel purchase_orders',
  `tahun_unit` year(4) DEFAULT NULL,
  `status_unit_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `lokasi_unit` varchar(255) DEFAULT NULL,
  `departemen_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel departemen',
  `tanggal_kirim` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `harga_sewa_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per bulan',
  `harga_sewa_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per hari',
  `kontrak_id` int(10) unsigned DEFAULT NULL COMMENT 'Foreign key ke tabel kontrak',
  `customer_id` int(11) DEFAULT NULL,
  `customer_location_id` int(11) DEFAULT NULL,
  `kontrak_spesifikasi_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi untuk tracking spek mana',
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
  `aksesoris` longtext DEFAULT NULL,
  `spk_id` int(10) unsigned DEFAULT NULL,
  `delivery_instruction_id` int(10) unsigned DEFAULT NULL,
  `di_workflow_id` int(11) DEFAULT NULL,
  `workflow_status` varchar(50) DEFAULT NULL,
  `contract_disconnect_date` datetime DEFAULT NULL,
  `contract_disconnect_stage` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_inventory_unit_original`
--

LOCK TABLES `backup_inventory_unit_original` WRITE;
/*!40000 ALTER TABLE `backup_inventory_unit_original` DISABLE KEYS */;
INSERT INTO `backup_inventory_unit_original` VALUES (1,1,'unit 1',122,2025,3,'MSI',2,'2025-09-04 00:00:00','',9000000.00,NULL,44,NULL,NULL,19,9,6,2,5,NULL,'',4,'',4,3,4,'2025-08-12 02:22:14','2025-09-10 09:01:05','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',28,123,NULL,NULL,NULL,NULL),(2,2,'unit 2',123,2025,3,'MSI',2,'2025-09-04 00:00:00','',9000000.00,NULL,44,NULL,NULL,19,10,3,10,2,NULL,'',2,'',2,2,3,'2025-08-12 03:15:49','2025-09-10 09:01:05','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',28,123,NULL,NULL,NULL,NULL),(4,4,'test',38,2025,3,'POS 1',3,'2025-09-12 00:00:00',NULL,12000000.00,NULL,55,NULL,NULL,39,12,6,6,2,NULL,'test',2,'test',2,1,3,'2025-08-12 04:47:28','2025-09-12 16:49:35','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',36,125,NULL,NULL,NULL,NULL),(5,7,'test2',38,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,19776000.00,NULL,54,NULL,NULL,37,12,6,6,2,NULL,'test2',2,'test2',2,1,3,'2025-08-12 04:49:52','2025-09-13 09:13:24','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]',38,127,NULL,NULL,NULL,NULL),(6,8,'test3',38,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,89000000.00,NULL,57,NULL,NULL,41,12,6,6,2,NULL,'test3',2,'test3',2,1,3,'2025-08-12 04:50:15','2025-09-13 10:32:41','[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA\",\"BIO METRIC\",\"P3K\"]',39,128,NULL,NULL,NULL,NULL),(7,3,'test4',38,2025,3,'POS 1',3,'2025-09-12 00:00:00',NULL,15000000.00,NULL,56,NULL,NULL,40,12,6,6,2,NULL,'test4',2,'test4',2,1,3,'2025-08-12 04:54:09','2025-09-13 08:20:35','[]',37,126,NULL,NULL,NULL,NULL),(8,11,'andara',39,2025,7,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,30,1,NULL,'andara',3,'andara',1,3,1,'2025-08-12 08:15:40','2025-09-13 10:32:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,9,'adaaaaa',38,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,89000000.00,NULL,57,NULL,NULL,41,12,6,6,2,NULL,'adaaaaa',2,'adaaaaa',2,1,3,'2025-08-12 08:21:22','2025-09-13 10:32:25','[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"ACRYLIC\",\"P3K\",\"SAFETY BELT INTERLOC\",\"SPARS ARRESTOR\"]',39,128,NULL,NULL,NULL,NULL),(10,6,'adit',38,2025,3,'POS 1',3,'2025-09-12 00:00:00',NULL,12000000.00,NULL,55,NULL,NULL,39,12,6,6,2,NULL,'adit',2,'adit',2,1,3,'2025-08-16 04:20:16','2025-09-12 16:49:35','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',36,125,NULL,NULL,NULL,NULL),(11,10,'adit',47,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,100000000.00,NULL,57,NULL,NULL,42,3,6,4,5,NULL,'adit',3,'adit',2,3,2,'2025-08-16 04:20:38','2025-09-13 12:12:21','[\"LAMPU UTAMA\",\"CAMERA AI\",\"SPEED LIMITER\",\"LASER FORK\",\"HORN KLASON\",\"APAR 3 KG\"]',40,129,NULL,NULL,NULL,NULL),(12,5,'adit',39,2025,3,'POS 1',2,'2025-09-12 00:00:00',NULL,19776000.00,NULL,54,NULL,NULL,37,2,1,3,1,NULL,'adit',3,'adit',1,3,1,'2025-08-16 04:24:32','2025-09-12 16:31:11',NULL,29,124,NULL,NULL,NULL,NULL),(13,14,'kkai',38,2025,8,'POS 1',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,12,6,6,2,NULL,'adit',2,'adit',2,1,3,'2025-08-16 04:26:43','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,12,'123',39,2025,8,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,3,1,NULL,'123',3,'123',1,3,1,'2025-08-27 02:22:59','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,13,'123',39,2025,8,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,3,1,NULL,'123',3,'123',1,3,1,'2025-08-27 02:23:14','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,15,'111',52,2025,8,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,5,3,NULL,'111',3,'111',1,2,2,'2025-08-27 15:35:47','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,16,'222',52,2025,7,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,5,3,NULL,'222',3,'222',1,2,2,'2025-08-27 15:36:17','2025-08-30 02:53:07',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `backup_inventory_unit_original` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup_kontrak_original`
--

DROP TABLE IF EXISTS `backup_kontrak_original`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_kontrak_original` (
  `id` int(10) unsigned NOT NULL DEFAULT 0,
  `no_kontrak` varchar(100) NOT NULL,
  `no_po_marketing` varchar(100) DEFAULT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `pic` varchar(255) DEFAULT NULL COMMENT 'Nama Person In Charge',
  `kontak` varchar(100) DEFAULT NULL COMMENT 'Kontak PIC (telepon/email)',
  `nilai_total` decimal(15,2) DEFAULT NULL COMMENT 'Nilai total kontrak dalam rupiah',
  `total_units` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total unit yang terkait dengan kontrak ini',
  `jenis_sewa` enum('BULANAN','HARIAN') DEFAULT 'BULANAN' COMMENT 'Jenis periode sewa',
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `status` enum('Aktif','Berakhir','Pending','Dibatalkan') NOT NULL DEFAULT 'Pending',
  `dibuat_oleh` int(10) unsigned DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_kontrak_original`
--

LOCK TABLES `backup_kontrak_original` WRITE;
/*!40000 ALTER TABLE `backup_kontrak_original` DISABLE KEYS */;
INSERT INTO `backup_kontrak_original` VALUES (44,'KNTRK/2208/0001','PO-ADIT10998','Sarana Mitra Luas','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT','Adit','09213123123',18000000.00,2,'BULANAN','2025-09-01','2025-09-01','Aktif',1,'2025-09-01 01:54:45','2025-09-10 03:08:34'),(54,'KNTRK/2209/0001',NULL,'Sarana Mitra Luas',NULL,NULL,NULL,0.00,0,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-09 09:54:01','2025-09-13 01:48:42'),(55,'SML/DS/121025',NULL,'Test',NULL,NULL,NULL,0.00,0,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-12 06:34:46','2025-09-12 09:49:35'),(56,'TEST/AUTO/001',NULL,'Test Client',NULL,NULL,NULL,0.00,0,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-12 09:54:47','2025-09-13 01:20:35'),(57,'test/1/1/5','12345','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','Adit','082134555233',0.00,0,'BULANAN','2025-09-13','2025-09-13','Aktif',1,'2025-09-13 02:56:22','2025-09-13 03:43:58'),(58,'test/1/1/6','12345','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','Adit','082134555233',0.00,0,'HARIAN','2025-09-13','2025-09-13','Pending',1,'2025-09-15 08:06:12','2025-09-15 08:06:12');
/*!40000 ALTER TABLE `backup_kontrak_original` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `backup_work_order_staff_original`
--

DROP TABLE IF EXISTS `backup_work_order_staff_original`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `backup_work_order_staff_original` (
  `id` int(11) NOT NULL DEFAULT 0,
  `staff_name` varchar(100) NOT NULL,
  `staff_role` enum('ADMIN','FOREMAN','MECHANIC','HELPER') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `backup_work_order_staff_original`
--

LOCK TABLES `backup_work_order_staff_original` WRITE;
/*!40000 ALTER TABLE `backup_work_order_staff_original` DISABLE KEYS */;
INSERT INTO `backup_work_order_staff_original` VALUES (1,'Novi','ADMIN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(2,'Sari','ADMIN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(3,'Andi','ADMIN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(4,'YOGA','FOREMAN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(5,'Budi','FOREMAN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(6,'Eko','FOREMAN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(7,'KURNIA','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(8,'BAGUS','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(9,'Deni','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(10,'Rudi','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(11,'Wahyu','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(12,'Joko','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(13,'Agus','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(14,'Dimas','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(15,'Fajar','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(16,'Hendra','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(17,'Iwan','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36');
/*!40000 ALTER TABLE `backup_work_order_staff_original` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `baterai`
--

DROP TABLE IF EXISTS `baterai`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `baterai` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merk_baterai` varchar(100) NOT NULL,
  `tipe_baterai` varchar(100) NOT NULL,
  `jenis_baterai` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `baterai`
--

LOCK TABLES `baterai` WRITE;
/*!40000 ALTER TABLE `baterai` DISABLE KEYS */;
INSERT INTO `baterai` VALUES (1,'JUNGHEINRICH (JHR)','48V / 775AH AQUAMATIC (5PZS775)','Lead Acid'),(2,'JUNGHEINRICH (JHR)','48V / 750AH AQUAMATIC (6PZS750)','Lead Acid'),(3,'JUNGHEINRICH (JHR)','48V / 620AH AQUAMATIC (4PZS620)','Lead Acid'),(4,'JUNGHEINRICH (JHR)','48V / 560AH (4PZS560)','Lead Acid'),(5,'JUNGHEINRICH (JHR)','24V / 375AH AQUAMATIC (3EPZS375)','Lead Acid'),(6,'JUNGHEINRICH (JHR)','24V / 205AH (2EPZS250L)','Lead Acid'),(7,'STILL','80V 5PZS700','Lead Acid'),(8,'STILL','24V 8PZS1000','Lead Acid'),(9,'STILL','48V 5PZS775','Lead Acid'),(10,'STILL','24V 6PZS690','Lead Acid'),(11,'STILL','24V / 375AH (3PZS345)','Lead Acid'),(12,'STILL','24V / 345AH (3PZS345)','Lead Acid'),(13,'STILL','24V / 230AH (2PZS230)','Lead Acid'),(14,'HAWKER','48V / 700AH','Lead Acid'),(15,'TAB','80V / 700AH (40EPZS700)','Lead Acid'),(16,'TAB','80V / 420AH (40EPZS420L)','Lead Acid'),(17,'TAB','48V','Lead Acid'),(18,'REMICO','24V / 250AH AQUAMATIC (12/2EPzS250L)','Lead Acid'),(19,'SLBATT','B-LFP80-410MH','Lithium-ion'),(20,'HELI','HL-C135-51.52-404','Lithium-ion'),(21,'-','80V 6PZS840','Lead Acid'),(22,'-','48V 5PZS886AH AQUA','Lead Acid'),(23,'-','40/4EDZ420V & 40/EPZ450V','Lead Acid'),(24,'-','JL-25.6F225PS','Lithium-ion'),(25,'-','80V / 404AH','Lithium-ion'),(26,'-','80V / 271AH','Lithium-ion'),(27,'-','25.6V / 150Ah','Lithium-ion');
/*!40000 ALTER TABLE `baterai` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `charger`
--

DROP TABLE IF EXISTS `charger`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `charger` (
  `id_charger` int(11) NOT NULL AUTO_INCREMENT,
  `merk_charger` varchar(100) NOT NULL,
  `tipe_charger` varchar(100) NOT NULL,
  PRIMARY KEY (`id_charger`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `charger`
--

LOCK TABLES `charger` WRITE;
/*!40000 ALTER TABLE `charger` DISABLE KEYS */;
INSERT INTO `charger` VALUES (1,'JUNGHEINRICH','SLT010nDe48/80P(48V / 80A)'),(2,'JUNGHEINRICH','SLT010nDe48/100P(48V / 100A)'),(3,'JUNGHEINRICH','SLT010nEe24/35P(24V / 35A)'),(4,'JUNGHEINRICH','(Standar)(24V / 70A)'),(5,'STILL','ECOTRON XM(80V / 125A)'),(6,'STILL','ECOTRON XM(24V / 60A)'),(7,'STILL','ECOTRON XM(48V / 126A)'),(8,'STILL','ECOTRON XM(48V / 150A)'),(9,'STILL','SLT010n(48V / 100A)'),(10,'HAWKER','SDC-ECO(24V / 60A)'),(11,'HAWKER','EX2460(24V / 60A)'),(12,'HR CHARGER','D400G24/70B-SLT100(24V / 70A)'),(13,'RIGETEK','RG-T(80V / 200A)'),(14,'RIGETEK','RG-T(48V / 100A)'),(15,'TITAN-POWER','SPC-24100(24V / 100A)'),(16,'TITAN-POWER','SPC-48100(48V / 100A)'),(17,'-','D80V/150PXS(80V / 150A)');
/*!40000 ALTER TABLE `charger` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contract_disconnection_log`
--

DROP TABLE IF EXISTS `contract_disconnection_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contract_disconnection_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kontrak_id` int(11) NOT NULL,
  `unit_id` int(10) unsigned NOT NULL,
  `stage` varchar(50) NOT NULL,
  `reason` varchar(100) DEFAULT NULL,
  `disconnected_at` datetime NOT NULL DEFAULT current_timestamp(),
  `disconnected_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_disconnect_kontrak` (`kontrak_id`),
  KEY `idx_disconnect_unit` (`unit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contract_disconnection_log`
--

LOCK TABLES `contract_disconnection_log` WRITE;
/*!40000 ALTER TABLE `contract_disconnection_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `contract_disconnection_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_contracts`
--

DROP TABLE IF EXISTS `customer_contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_contracts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `kontrak_id` int(10) unsigned NOT NULL COMMENT 'FK to existing kontrak table',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_customer_kontrak` (`customer_id`,`kontrak_id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_kontrak_id` (`kontrak_id`),
  CONSTRAINT `fk_customer_contracts_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_customer_contracts_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Link customers to kontrak (many-to-many)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_contracts`
--

LOCK TABLES `customer_contracts` WRITE;
/*!40000 ALTER TABLE `customer_contracts` DISABLE KEYS */;
INSERT INTO `customer_contracts` VALUES (1,2,44,1,'2025-09-25 03:56:50','2025-09-25 03:56:50'),(2,3,54,1,'2025-09-25 03:56:50','2025-09-25 03:56:50'),(3,4,55,1,'2025-09-25 03:56:50','2025-09-25 03:56:50'),(4,5,56,1,'2025-09-25 03:56:50','2025-09-25 03:56:50'),(5,6,57,1,'2025-09-25 03:56:50','2025-09-25 03:56:50'),(6,7,58,0,'2025-09-25 03:56:50','2025-09-25 03:56:50');
/*!40000 ALTER TABLE `customer_contracts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_locations`
--

DROP TABLE IF EXISTS `customer_locations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customer_locations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `area_id` int(11) DEFAULT NULL,
  `location_name` varchar(100) NOT NULL COMMENT 'Kantor Pusat, Pabrik 1, Gudang A, etc',
  `location_type` enum('HEAD_OFFICE','BRANCH','WAREHOUSE','FACTORY') DEFAULT 'BRANCH',
  `address` text NOT NULL,
  `contact_person` varchar(128) DEFAULT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `email` varchar(128) DEFAULT NULL,
  `pic_position` varchar(64) DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `city` varchar(100) NOT NULL,
  `province` varchar(100) NOT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0 COMMENT 'Primary location for this customer',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_location_active` (`is_active`),
  CONSTRAINT `fk_customer_locations_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Multiple locations per customer';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_locations`
--

LOCK TABLES `customer_locations` WRITE;
/*!40000 ALTER TABLE `customer_locations` DISABLE KEYS */;
INSERT INTO `customer_locations` VALUES (1,2,NULL,'Lokasi Utama','BRANCH','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-09-25 03:56:17'),(2,3,NULL,'Lokasi Utama','BRANCH','Alamat belum tersedia',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-09-25 03:56:17'),(3,4,NULL,'Lokasi Utama','BRANCH','Alamat belum tersedia',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-09-25 03:56:17'),(4,5,NULL,'Lokasi Utama','BRANCH','Alamat belum tersedia',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-09-25 03:56:17'),(5,6,NULL,'Lokasi Utama','BRANCH','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530',NULL,NULL,NULL,NULL,NULL,'Bekasi','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-09-25 03:56:17'),(6,7,NULL,'Lokasi Utama','BRANCH','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530',NULL,NULL,NULL,NULL,NULL,'Bekasi','DKI Jakarta',NULL,NULL,NULL,1,0,'2025-09-25 03:56:17','2025-09-25 03:56:17');
/*!40000 ALTER TABLE `customer_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(20) NOT NULL COMMENT 'SML001, ABC002, etc',
  `customer_name` varchar(255) NOT NULL COMMENT 'Sarana Mitra Luas, PT ABC, etc',
  `area_id` int(11) NOT NULL COMMENT 'FK to areas table',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`),
  UNIQUE KEY `uk_customer_code` (`customer_code`),
  KEY `idx_area_id` (`area_id`),
  KEY `idx_customer_active` (`is_active`),
  KEY `idx_customer_name` (`customer_name`),
  CONSTRAINT `fk_customers_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master Customers/PT Client';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (2,'CUST044','Sarana Mitra Luas',2,1,'2025-09-25 03:55:54','2025-09-25 03:55:54'),(3,'CUST054','Sarana Mitra Luas',4,1,'2025-09-25 03:55:54','2025-09-25 03:55:54'),(4,'CUST055','Test',4,1,'2025-09-25 03:55:54','2025-09-25 03:55:54'),(5,'CUST056','Test Client',4,1,'2025-09-25 03:55:54','2025-09-25 03:55:54'),(6,'CUST057','Sarana Mitra Luas',2,1,'2025-09-25 03:55:54','2025-09-25 03:55:54'),(7,'CUST058','Sarana Mitra Luas',2,0,'2025-09-25 03:55:54','2025-09-25 03:55:54');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_instructions`
--

DROP TABLE IF EXISTS `delivery_instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_instructions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_di` varchar(100) NOT NULL,
  `spk_id` int(10) unsigned DEFAULT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT') DEFAULT 'UNIT',
  `po_kontrak_nomor` varchar(100) DEFAULT NULL,
  `pelanggan` varchar(255) NOT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status` enum('SUBMITTED','PROCESSED','SHIPPED','DELIVERED','CANCELLED') NOT NULL DEFAULT 'SUBMITTED',
  `jenis_perintah_kerja_id` int(11) DEFAULT NULL,
  `tujuan_perintah_kerja_id` int(11) DEFAULT NULL,
  `status_eksekusi_workflow_id` int(11) DEFAULT 1,
  `dibuat_oleh` int(10) unsigned DEFAULT NULL,
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
  KEY `fk_di_status_eksekusi_workflow` (`status_eksekusi_workflow_id`),
  KEY `idx_delivery_instructions_jenis_spk` (`jenis_spk`),
  CONSTRAINT `fk_di_jenis_perintah_kerja` FOREIGN KEY (`jenis_perintah_kerja_id`) REFERENCES `jenis_perintah_kerja` (`id`),
  CONSTRAINT `fk_di_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_di_status_eksekusi_workflow` FOREIGN KEY (`status_eksekusi_workflow_id`) REFERENCES `status_eksekusi_workflow` (`id`),
  CONSTRAINT `fk_di_tujuan_perintah_kerja` FOREIGN KEY (`tujuan_perintah_kerja_id`) REFERENCES `tujuan_perintah_kerja` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_instructions`
--

LOCK TABLES `delivery_instructions` WRITE;
/*!40000 ALTER TABLE `delivery_instructions` DISABLE KEYS */;
INSERT INTO `delivery_instructions` VALUES (100,'DI/202509/TEST001',27,'UNIT','PO-TEST-001','PT Test Customer','Jakarta',NULL,NULL,'SUBMITTED',1,1,1,1,'2025-09-03 16:52:00','2025-09-17 10:41:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DIAJUKAN'),(122,'DI/202509/001',27,'UNIT','test12345','MONORKOBO','BEKASI','2025-09-04',NULL,'PROCESSED',1,1,1,1,'2025-09-04 03:43:23','2025-09-17 10:41:42','2025-09-04','2025-09-04','JOKO','082138848123','1231012','colt diesel','123',NULL,NULL,NULL,NULL,'SIAP_KIRIM'),(123,'DI/202509/002',28,'UNIT','MSI','MSI','EROPA','2025-09-04','a','DELIVERED',1,1,1,1,'2025-09-04 04:14:52','2025-09-17 10:41:42','2025-09-04','2025-09-04','JOKO','082138848123','1231012','colt diesel (123)','123','2025-09-04',NULL,'2025-09-04','ok','SELESAI'),(124,'DI/202509/003',29,'UNIT','KNTRK/2209/0001','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-12','DIKIRIM','DELIVERED',1,1,1,1,'2025-09-09 10:07:55','2025-09-17 10:41:42','2025-09-12','2025-09-12','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-12',NULL,'2025-09-12','sudah sampai','SELESAI'),(125,'DI/202509/004',36,'UNIT','SML/DS/121025','LG','Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190','2025-09-12',NULL,'DELIVERED',1,1,1,1,'2025-09-12 06:51:13','2025-09-17 10:41:42','2025-09-12','2025-09-12','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-12',NULL,'2025-09-12','ok','SELESAI'),(126,'DI/202509/005',37,'UNIT','TEST/AUTO/001','Test Auto Update','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-12',NULL,'DELIVERED',1,1,1,1,'2025-09-12 10:06:23','2025-09-17 10:41:42','2025-09-12','2025-09-12','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-12',NULL,'2025-09-12','123','SELESAI'),(127,'DI/202509/006',38,'UNIT','KNTRK/2209/0001','Sarana Mitra Luas',NULL,'2025-09-13',NULL,'DELIVERED',1,1,1,1,'2025-09-13 01:47:49','2025-09-17 10:41:42','2025-09-13','2025-09-13','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-13',NULL,'2025-09-13','qwe','SELESAI'),(128,'DI/202509/007',39,'UNIT','test/1/1/5','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13',NULL,'DELIVERED',1,1,1,1,'2025-09-13 02:58:51','2025-09-17 10:41:42','2025-09-13','2025-09-13','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-13',NULL,'2025-09-13','a','SELESAI'),(129,'DI/202509/008',40,'UNIT','test/1/1/5','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13',NULL,'DELIVERED',1,1,1,1,'2025-09-13 03:43:34','2025-09-17 10:41:42','2025-09-13','2025-09-13','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-13','a','2025-09-13','a','SELESAI'),(130,'DI/202509/009',NULL,'UNIT','TEST/AUTO/001','Test Client',NULL,'2025-09-16',NULL,'SUBMITTED',2,4,1,1,'2025-09-16 03:10:02','2025-09-16 10:10:02',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DIAJUKAN'),(131,'DI/202509/010',49,'ATTACHMENT','TEST/AUTO/001','Test Client',NULL,'2025-09-17',NULL,'PROCESSED',1,1,1,1,'2025-09-17 02:54:40','2025-09-23 13:21:02',NULL,NULL,'','-','-','','-',NULL,NULL,NULL,NULL,'SIAP_KIRIM');
/*!40000 ALTER TABLE `delivery_instructions` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_di_create_workflow_stages` 
AFTER INSERT ON `delivery_instructions`
FOR EACH ROW
BEGIN
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
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER sync_di_status_temp_on_update
    BEFORE UPDATE ON delivery_instructions
    FOR EACH ROW
BEGIN
    
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
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_delivery_instructions_update_unit`
    AFTER UPDATE ON `delivery_instructions`
    FOR EACH ROW
BEGIN
    
    IF NEW.tanggal_kirim IS NOT NULL AND NEW.spk_id IS NOT NULL 
       AND (OLD.tanggal_kirim IS NULL OR OLD.tanggal_kirim != NEW.tanggal_kirim) THEN
        
        UPDATE `inventory_unit` iu
        JOIN `spk` s ON iu.spk_id = s.id OR iu.kontrak_spesifikasi_id = s.kontrak_spesifikasi_id
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
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `delivery_items` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `di_id` int(10) unsigned NOT NULL,
  `item_type` enum('UNIT','ATTACHMENT') NOT NULL DEFAULT 'UNIT',
  `unit_id` int(10) unsigned DEFAULT NULL,
  `parent_unit_id` int(11) DEFAULT NULL,
  `attachment_id` int(10) unsigned DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_delivery_items_di_id` (`di_id`),
  KEY `idx_delivery_items_type` (`item_type`),
  KEY `idx_delivery_items_unit` (`unit_id`),
  KEY `idx_delivery_items_attachment` (`attachment_id`),
  CONSTRAINT `fk_delivery_items_di` FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_delivery_items_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Items untuk delivery instruction';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_items`
--

LOCK TABLES `delivery_items` WRITE;
/*!40000 ALTER TABLE `delivery_items` DISABLE KEYS */;
INSERT INTO `delivery_items` VALUES (116,122,'UNIT',1,NULL,NULL,NULL,'2025-09-04 03:43:23','2025-09-04 03:43:23'),(117,122,'UNIT',12,NULL,NULL,NULL,'2025-09-04 03:43:23','2025-09-04 03:43:23'),(118,122,'ATTACHMENT',NULL,1,4,'Battery for Unit 1','2025-09-03 20:43:23','2025-09-03 20:43:23'),(119,122,'ATTACHMENT',NULL,1,5,'Charger for Unit 1','2025-09-03 20:43:23','2025-09-03 20:43:23'),(120,122,'ATTACHMENT',NULL,12,5,'Charger for Unit 12','2025-09-03 20:43:23','2025-09-03 20:43:23'),(121,122,'ATTACHMENT',NULL,12,6,'Battery for Unit 12','2025-09-03 20:43:23','2025-09-03 20:43:23'),(122,123,'UNIT',1,NULL,NULL,NULL,'2025-09-04 04:14:52','2025-09-04 04:14:52'),(123,123,'UNIT',2,NULL,NULL,NULL,'2025-09-04 04:14:52','2025-09-04 04:14:52'),(124,123,'ATTACHMENT',NULL,1,4,'Battery for Unit 1','2025-09-03 21:14:52','2025-09-03 21:14:52'),(125,123,'ATTACHMENT',NULL,1,5,'Charger for Unit 1','2025-09-03 21:14:52','2025-09-03 21:14:52'),(126,123,'ATTACHMENT',NULL,2,4,'Battery for Unit 2','2025-09-03 21:14:52','2025-09-03 21:14:52'),(127,123,'ATTACHMENT',NULL,2,5,'Charger for Unit 2','2025-09-03 21:14:52','2025-09-03 21:14:52'),(128,124,'UNIT',12,NULL,NULL,NULL,'2025-09-09 10:07:55','2025-09-09 10:07:55'),(129,124,'ATTACHMENT',NULL,12,5,'Charger for Unit 12','2025-09-09 03:07:55','2025-09-09 03:07:55'),(130,124,'ATTACHMENT',NULL,12,6,'Battery for Unit 12','2025-09-09 03:07:55','2025-09-09 03:07:55'),(131,125,'UNIT',4,NULL,NULL,NULL,'2025-09-12 06:51:13','2025-09-12 06:51:13'),(132,125,'UNIT',10,NULL,NULL,NULL,'2025-09-12 06:51:13','2025-09-12 06:51:13'),(133,125,'ATTACHMENT',NULL,4,2,'Battery for Unit 4','2025-09-11 23:51:13','2025-09-11 23:51:13'),(134,125,'ATTACHMENT',NULL,10,2,'Battery for Unit 10','2025-09-11 23:51:13','2025-09-11 23:51:13'),(135,126,'UNIT',7,NULL,NULL,NULL,'2025-09-12 10:06:23','2025-09-12 10:06:23'),(136,126,'ATTACHMENT',NULL,7,2,'Battery for Unit 7','2025-09-12 03:06:23','2025-09-12 03:06:23'),(137,127,'UNIT',5,NULL,NULL,NULL,'2025-09-13 01:47:49','2025-09-13 01:47:49'),(138,127,'ATTACHMENT',NULL,5,2,'Battery for Unit 5','2025-09-12 18:47:49','2025-09-12 18:47:49'),(139,128,'UNIT',6,NULL,NULL,NULL,'2025-09-13 02:58:51','2025-09-13 02:58:51'),(140,128,'UNIT',9,NULL,NULL,NULL,'2025-09-13 02:58:51','2025-09-13 02:58:51'),(141,128,'ATTACHMENT',NULL,6,2,'Battery for Unit 6','2025-09-12 19:58:51','2025-09-12 19:58:51'),(142,128,'ATTACHMENT',NULL,9,2,'Battery for Unit 9','2025-09-12 19:58:51','2025-09-12 19:58:51'),(143,129,'UNIT',11,NULL,NULL,NULL,'2025-09-13 03:43:34','2025-09-13 03:43:34'),(144,129,'ATTACHMENT',NULL,11,4,'Attachment for Unit 11','2025-09-12 20:43:34','2025-09-12 20:43:34'),(145,129,'ATTACHMENT',NULL,11,3,'Battery for Unit 11','2025-09-12 20:43:34','2025-09-12 20:43:34'),(146,131,'ATTACHMENT',NULL,NULL,16,'Side Shifter - Manual Fix for Testing','2025-09-22 02:27:20','2025-09-22 02:27:20');
/*!40000 ALTER TABLE `delivery_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departemen`
--

DROP TABLE IF EXISTS `departemen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departemen` (
  `id_departemen` int(11) NOT NULL AUTO_INCREMENT,
  `nama_departemen` varchar(100) NOT NULL,
  PRIMARY KEY (`id_departemen`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departemen`
--

LOCK TABLES `departemen` WRITE;
/*!40000 ALTER TABLE `departemen` DISABLE KEYS */;
INSERT INTO `departemen` VALUES (1,'DIESEL'),(2,'ELECTRIC'),(3,'GASOLINE');
/*!40000 ALTER TABLE `departemen` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `di_workflow_stages`
--

DROP TABLE IF EXISTS `di_workflow_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `di_workflow_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `di_id` int(11) NOT NULL,
  `stage_code` varchar(50) NOT NULL,
  `stage_name` varchar(100) NOT NULL,
  `status` enum('PENDING','IN_PROGRESS','COMPLETED','SKIPPED') DEFAULT 'PENDING',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_workflow_stages_di` (`di_id`),
  KEY `idx_workflow_stages_code` (`stage_code`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `di_workflow_stages`
--

LOCK TABLES `di_workflow_stages` WRITE;
/*!40000 ALTER TABLE `di_workflow_stages` DISABLE KEYS */;
INSERT INTO `di_workflow_stages` VALUES (1,130,'DIAJUKAN','DI Diajukan','COMPLETED',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02'),(2,130,'DISETUJUI','DI Disetujui','PENDING',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02'),(3,130,'PERSIAPAN_UNIT','Persiapan Tim & Transportasi','PENDING',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02'),(4,130,'DALAM_PERJALANAN','Dalam Perjalanan ke Lokasi','PENDING',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02'),(5,130,'UNIT_DITARIK','Unit Berhasil Ditarik','PENDING',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02'),(6,130,'UNIT_PULANG','Unit Dalam Perjalanan Pulang','PENDING',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02'),(7,130,'SAMPAI_KANTOR','Unit Sampai di Kantor/Workshop','PENDING',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02'),(8,130,'SELESAI','Proses Penarikan Selesai','PENDING',NULL,NULL,NULL,NULL,'2025-09-16 10:10:02','2025-09-16 10:10:02');
/*!40000 ALTER TABLE `di_workflow_stages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `divisions`
--

DROP TABLE IF EXISTS `divisions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `divisions` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `divisions`
--

LOCK TABLES `divisions` WRITE;
/*!40000 ALTER TABLE `divisions` DISABLE KEYS */;
INSERT INTO `divisions` VALUES (1,'Administration','ADMIN','System Administration Division',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(2,'Service','SERVICE','Service Division - Unit Maintenance & Repair',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(3,'Unit Operational','UNIT_OPS','Unit Operational Division - Delivery & Rolling',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(4,'Marketing','MARKETING','Marketing Division - Sales & Customer Relations',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(5,'Warehouse & Assets','WAREHOUSE','Warehouse & Assets Management Division',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(6,'Purchasing','PURCHASING','Purchasing Division - Procurement & Vendor Management',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(7,'Perizinan','PERIZINAN','Licensing Division - Permits & Documentation',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(8,'Accounting','ACCOUNTING','Accounting Division - Finance & Bookkeeping',1,'2025-08-05 07:01:57','2025-08-05 07:01:57');
/*!40000 ALTER TABLE `divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forklifts`
--

DROP TABLE IF EXISTS `forklifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `forklifts` (
  `forklift_id` int(10) unsigned NOT NULL,
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
  `assigned_to` int(10) unsigned DEFAULT NULL COMMENT 'Assigned to user ID',
  `rental_rate_daily` decimal(10,2) DEFAULT NULL COMMENT 'Daily rental rate',
  `rental_rate_weekly` decimal(10,2) DEFAULT NULL COMMENT 'Weekly rental rate',
  `rental_rate_monthly` decimal(10,2) DEFAULT NULL COMMENT 'Monthly rental rate',
  `availability` enum('available','unavailable','reserved') NOT NULL DEFAULT 'available',
  `notes` text DEFAULT NULL,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional specifications in JSON format',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'File attachments in JSON format',
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `deleted_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`forklift_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forklifts`
--

LOCK TABLES `forklifts` WRITE;
/*!40000 ALTER TABLE `forklifts` DISABLE KEYS */;
INSERT INTO `forklifts` VALUES (1,'FL001','Toyota 8FG25 Forklift','Toyota','8FG25','gas',2.50,'gas',68.00,4.70,2022,'TYT8FG25001','2022-01-15',450000000.00,380000000.00,'Toyota Material Handling','2025-01-15','2024-12-31',NULL,NULL,250,1250,'available','excellent','Warehouse A',NULL,850000.00,5500000.00,20000000.00,'available','Unit kondisi prima, rutin maintenance',NULL,NULL,1,NULL,NULL,'2025-07-08 06:35:48',NULL,NULL),(2,'FL002','Komatsu FB20-12 Electric Forklift','Komatsu','FB20-12','electric',2.00,'electric',24.00,3.00,2023,'KMT FB20001','2023-03-10',380000000.00,340000000.00,'Komatsu Forklift Indonesia','2026-03-10','2024-12-31',NULL,NULL,300,890,'rented','excellent','Customer Site - PT ABC',NULL,750000.00,4800000.00,18000000.00,'unavailable','Sedang disewa PT ABC Industries',NULL,NULL,1,NULL,NULL,'2025-07-08 06:35:48',NULL,NULL),(3,'FL003','Hyster H3.5FT Diesel Forklift','Hyster','H3.5FT','diesel',3.50,'diesel',74.00,4.50,2021,'HYS H35001','2021-08-20',520000000.00,420000000.00,'Hyster Indonesia','2024-08-20','2024-12-31',NULL,NULL,250,2150,'maintenance','good','Service Center',NULL,950000.00,6200000.00,23000000.00,'unavailable','Maintenance rutin 2000 jam operasi',NULL,NULL,1,NULL,NULL,'2025-07-08 06:35:48',NULL,NULL),(4,'FL004','Mitsubishi FG15N Gas Forklift','Mitsubishi','FG15N','gas',1.50,'gas',42.00,3.00,2023,'MIT FG15001','2023-06-15',320000000.00,300000000.00,'Mitsubishi Forklift','2026-06-15','2024-12-31',NULL,NULL,250,456,'available','excellent','Warehouse B',NULL,650000.00,4200000.00,15000000.00,'available','Unit baru, kondisi prima',NULL,NULL,1,NULL,NULL,'2025-07-08 06:35:48',NULL,NULL),(5,'FL005','Crown FC 5200 Electric Forklift','Crown','FC 5200','electric',2.00,'electric',36.00,4.00,2022,'CRW FC5200001','2022-09-10',420000000.00,360000000.00,'Crown Equipment Indonesia','2025-09-10','2024-12-31',NULL,NULL,300,1680,'reserved','good','Warehouse A',NULL,780000.00,5000000.00,18500000.00,'reserved','Reserved untuk kontrak PT XYZ minggu depan',NULL,NULL,1,NULL,NULL,'2025-07-08 06:35:48',NULL,NULL);
/*!40000 ALTER TABLE `forklifts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_attachment`
--

DROP TABLE IF EXISTS `inventory_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_attachment` (
  `id_inventory_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_item` enum('attachment','battery','charger') NOT NULL DEFAULT 'attachment',
  `po_id` int(11) NOT NULL COMMENT 'Foreign key ke purchase_orders.id_po',
  `id_inventory_unit` int(10) unsigned DEFAULT NULL,
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
  `attachment_status` enum('AVAILABLE','USED','MAINTENANCE','RUSAK','RESERVED') DEFAULT 'AVAILABLE',
  `tanggal_masuk` datetime DEFAULT current_timestamp() COMMENT 'Tanggal masuk ke inventory',
  `catatan_inventory` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status_attachment_id` int(11) DEFAULT 1,
  PRIMARY KEY (`id_inventory_attachment`),
  KEY `fk_inventory_attachment_attachment` (`attachment_id`),
  KEY `fk_inventory_attachment_baterai` (`baterai_id`),
  KEY `fk_inventory_attachment_charger` (`charger_id`),
  KEY `fk_inventory_attachment_status_unit` (`status_unit`),
  KEY `idx_inventory_attachment_status` (`status_attachment_id`),
  KEY `idx_inventory_attachment_unit_status` (`id_inventory_unit`,`status_attachment_id`),
  KEY `idx_attachment_status` (`attachment_status`),
  CONSTRAINT `fk_inventory_attachment_attachment` FOREIGN KEY (`attachment_id`) REFERENCES `attachment` (`id_attachment`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_attachment_baterai` FOREIGN KEY (`baterai_id`) REFERENCES `baterai` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_attachment_charger` FOREIGN KEY (`charger_id`) REFERENCES `charger` (`id_charger`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_attachment_status_unit` FOREIGN KEY (`status_unit`) REFERENCES `status_unit` (`id_status`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Single source of truth untuk semua komponen: battery, charger, attachment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_attachment`
--

LOCK TABLES `inventory_attachment` WRITE;
/*!40000 ALTER TABLE `inventory_attachment` DISABLE KEYS */;
INSERT INTO `inventory_attachment` VALUES (2,'attachment',118,1,1,'123',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 1',7,'USED','2025-08-22 04:36:39','Dari verifikasi PO: Sesuai dan siap digunakan','2025-08-22 04:36:39','2025-09-13 12:26:44',2),(3,'attachment',124,11,4,'123',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,NULL,7,'USED','2025-08-22 09:18:28','Dari verifikasi PO: Sesuai','2025-08-22 09:18:28','2025-09-13 12:26:44',2),(4,'attachment',130,13,3,'123',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,NULL,7,'AVAILABLE','2025-08-22 09:19:00','Dari verifikasi PO: Sesuai','2025-08-22 09:19:00','2025-09-16 06:57:44',1),(5,'battery',143,1,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 1',3,'USED','2025-08-22 09:23:14','Dari verifikasi PO (Battery): Sesuai','2025-08-22 09:23:14','2025-09-13 12:26:44',2),(6,'charger',143,16,NULL,NULL,NULL,NULL,5,'123','Baik','Lengkap',NULL,'Terpasang di Unit 15',8,'USED','2025-08-22 09:23:14','Dari verifikasi PO (Charger): Sesuai','2025-08-22 09:23:14','2025-09-13 12:26:44',2),(7,'battery',143,2,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 2',3,'USED','2025-08-27 04:15:34','Dari verifikasi PO (Battery): Sesuai','2025-08-27 04:15:34','2025-09-13 12:26:44',2),(8,'charger',143,17,NULL,NULL,NULL,NULL,5,'123','Baik','Lengkap',NULL,'Terpasang di Unit 16',8,'USED','2025-08-27 04:15:34','Dari verifikasi PO (Charger): Sesuai','2025-08-27 04:15:34','2025-09-13 12:26:44',2),(9,'battery',143,NULL,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'POS 1',7,'AVAILABLE','2025-08-27 04:15:43','Dari verifikasi PO (Battery): Sesuai','2025-08-27 04:15:43','2025-08-27 11:41:47',1),(10,'charger',143,1,NULL,NULL,NULL,NULL,5,'123','Baik','Lengkap',NULL,'Terpasang di Unit 1',3,'USED','2025-08-27 04:15:43','Dari verifikasi PO (Charger): Sesuai','2025-08-27 04:15:43','2025-09-13 12:26:44',2),(11,'battery',143,NULL,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'POS 1',7,'AVAILABLE','2025-08-27 04:15:51','Dari verifikasi PO (Battery): Sesuai','2025-08-27 04:15:51','2025-08-27 11:41:47',1),(12,'charger',143,12,NULL,NULL,NULL,NULL,5,'123','Baik','Lengkap',NULL,'Terpasang di Unit 5',3,'USED','2025-08-27 04:15:51','Dari verifikasi PO (Charger): Sesuai','2025-08-27 04:15:51','2025-09-13 12:26:44',2),(13,'battery',143,NULL,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'POS 1',7,'AVAILABLE','2025-08-27 04:15:58','Dari verifikasi PO (Battery): Sesuai','2025-08-27 04:15:58','2025-08-27 11:41:47',1),(14,'charger',143,2,NULL,NULL,NULL,NULL,5,'123','Baik','Lengkap',NULL,'Terpasang di Unit 2',3,'USED','2025-08-27 04:15:58','Dari verifikasi PO (Charger): Sesuai','2025-08-27 04:15:58','2025-09-13 12:26:44',2),(15,'attachment',131,NULL,3,'ok',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'POS 1',7,'AVAILABLE','2025-08-27 04:50:06','Dari verifikasi PO: Sesuai','2025-08-27 04:50:06','2025-08-27 04:50:06',1),(16,'attachment',139,13,3,'a',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,NULL,7,'USED','2025-08-28 09:32:49','Dari verifikasi PO: Sesuai','2025-08-28 09:32:49','2025-09-15 15:23:52',1),(17,'battery',38,NULL,2,NULL,2,'test',6,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 4',3,'AVAILABLE','2025-08-12 04:47:28','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:47:28','2025-09-13 12:35:58',2),(18,'battery',38,NULL,2,NULL,2,'test2',6,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 7',3,'AVAILABLE','2025-08-12 04:49:52','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:49:52','2025-09-13 12:35:58',2),(19,'battery',38,NULL,2,NULL,2,'test3',6,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 8',7,'AVAILABLE','2025-08-12 04:50:15','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:50:15','2025-09-13 12:35:58',2),(20,'battery',38,NULL,3,'',2,'test4',6,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 3',3,'AVAILABLE','2025-08-12 04:54:09','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:54:09','2025-09-13 12:35:58',2),(21,'battery',39,8,NULL,NULL,6,'andara',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 11',7,'USED','2025-08-12 08:15:40','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 08:15:40','2025-09-13 12:26:44',2),(22,'battery',38,NULL,5,'wae',2,'adaaaaa',6,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 9',7,'AVAILABLE','2025-08-12 08:21:22','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 08:21:22','2025-09-13 12:35:58',2),(23,'battery',38,NULL,2,NULL,2,'adit',6,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 6',3,'AVAILABLE','2025-08-16 04:20:16','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:20:16','2025-09-13 12:35:58',2),(24,'battery',47,NULL,NULL,NULL,3,'adit',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 10',7,'AVAILABLE','2025-08-16 04:20:38','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:20:38','2025-09-13 12:12:08',1),(25,'battery',39,12,NULL,NULL,6,'adit',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 5',3,'USED','2025-08-16 04:24:32','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:24:32','2025-09-13 12:26:44',2),(26,'battery',38,NULL,2,NULL,2,'adit',6,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 14',7,'AVAILABLE','2025-08-16 04:26:43','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:26:43','2025-09-13 12:35:58',2),(27,'battery',39,14,NULL,NULL,6,'123',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 12',7,'USED','2025-08-27 02:22:59','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-27 02:22:59','2025-09-13 12:26:44',2),(28,'battery',39,15,NULL,NULL,6,'123',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 13',7,'USED','2025-08-27 02:23:14','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-27 02:23:14','2025-09-13 12:26:44',2),(29,'battery',52,16,NULL,NULL,3,'111',6,'123','Baik','Lengkap',NULL,'Terpasang di Unit 15',7,'USED','2025-08-27 15:35:47','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-27 15:35:47','2025-09-13 12:26:44',2),(30,'battery',52,17,3,'ok',3,'222',8,'123','Baik','Lengkap',NULL,'Terpasang di Unit 16',7,'USED','2025-08-27 15:36:17','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-27 15:36:17','2025-09-13 12:26:44',2);
/*!40000 ALTER TABLE `inventory_attachment` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_inventory_attachment_status_sync
AFTER UPDATE ON inventory_attachment
FOR EACH ROW
BEGIN
    
    IF (OLD.id_inventory_unit IS NULL AND NEW.id_inventory_unit IS NOT NULL) OR
       (OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NULL) OR
       (OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NOT NULL AND OLD.id_inventory_unit != NEW.id_inventory_unit) THEN
        
        
        IF NEW.id_inventory_unit IS NOT NULL THEN
            UPDATE inventory_unit iu
            JOIN kontrak_spesifikasi ks ON iu.kontrak_spesifikasi_id = ks.id
            JOIN kontrak k ON ks.kontrak_id = k.id
            SET iu.status_unit_id = 3, iu.updated_at = NOW()
            WHERE iu.id_inventory_unit = NEW.id_inventory_unit 
            AND k.status = 'Aktif' 
            AND iu.status_unit_id != 3;
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `inventory_item_unit_log`
--

DROP TABLE IF EXISTS `inventory_item_unit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_item_unit_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_inventory_attachment` int(11) NOT NULL,
  `id_inventory_unit` int(11) NOT NULL,
  `action` enum('assign','remove') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_item_unit_log`
--

LOCK TABLES `inventory_item_unit_log` WRITE;
/*!40000 ALTER TABLE `inventory_item_unit_log` DISABLE KEYS */;
INSERT INTO `inventory_item_unit_log` VALUES (1,3,11,'assign',NULL,NULL,'2025-09-13 10:43:16'),(3,16,13,'assign',NULL,NULL,'2025-09-15 15:21:59'),(4,4,13,'assign',NULL,NULL,'2025-09-16 13:57:44');
/*!40000 ALTER TABLE `inventory_item_unit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_spareparts`
--

DROP TABLE IF EXISTS `inventory_spareparts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_spareparts` (
  `id` int(11) NOT NULL,
  `sparepart_id` int(11) NOT NULL,
  `stok` int(11) NOT NULL DEFAULT 0,
  `lokasi_rak` varchar(100) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_spareparts`
--

LOCK TABLES `inventory_spareparts` WRITE;
/*!40000 ALTER TABLE `inventory_spareparts` DISABLE KEYS */;
INSERT INTO `inventory_spareparts` VALUES (1,17,1,'POS 1','2025-07-25 09:01:22'),(2,1,1,'POS 1','2025-08-12 03:36:22');
/*!40000 ALTER TABLE `inventory_spareparts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `inventory_unit`
--

DROP TABLE IF EXISTS `inventory_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_unit` (
  `id_inventory_unit` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `no_unit` int(10) unsigned DEFAULT NULL,
  `serial_number` varchar(255) DEFAULT NULL COMMENT 'Serial Number utama dari pabrikan',
  `id_po` int(11) DEFAULT NULL COMMENT 'Foreign Key ke tabel purchase_orders',
  `tahun_unit` year(4) DEFAULT NULL,
  `status_unit_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `lokasi_unit` varchar(255) DEFAULT NULL,
  `departemen_id` int(11) DEFAULT NULL COMMENT 'FK ke tabel departemen',
  `tanggal_kirim` datetime DEFAULT NULL,
  `keterangan` text DEFAULT NULL,
  `harga_sewa_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per bulan',
  `harga_sewa_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per hari',
  `kontrak_id` int(10) unsigned DEFAULT NULL COMMENT 'Foreign key ke tabel kontrak',
  `customer_id` int(11) DEFAULT NULL,
  `customer_location_id` int(11) DEFAULT NULL,
  `kontrak_spesifikasi_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi untuk tracking spek mana',
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
  `aksesoris` longtext DEFAULT NULL,
  `spk_id` int(10) unsigned DEFAULT NULL,
  `delivery_instruction_id` int(10) unsigned DEFAULT NULL,
  `di_workflow_id` int(11) DEFAULT NULL,
  `workflow_status` varchar(50) DEFAULT NULL,
  `contract_disconnect_date` datetime DEFAULT NULL,
  `contract_disconnect_stage` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_inventory_unit`),
  KEY `fk_inventory_unit_status` (`status_unit_id`),
  KEY `fk_inventory_unit_departemen` (`departemen_id`),
  KEY `fk_inventory_unit_kontrak` (`kontrak_id`),
  KEY `fk_inventory_unit_kontrak_spesifikasi` (`kontrak_spesifikasi_id`),
  KEY `fk_inventory_unit_tipe` (`tipe_unit_id`),
  KEY `fk_inventory_unit_model` (`model_unit_id`),
  KEY `fk_inventory_unit_kapasitas` (`kapasitas_unit_id`),
  KEY `fk_inventory_unit_spk` (`spk_id`),
  KEY `fk_inventory_unit_delivery_instruction` (`delivery_instruction_id`),
  KEY `idx_unit_workflow` (`di_workflow_id`),
  KEY `idx_unit_workflow_status` (`workflow_status`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_customer_location_id` (`customer_location_id`),
  CONSTRAINT `fk_inventory_unit_delivery_instruction` FOREIGN KEY (`delivery_instruction_id`) REFERENCES `delivery_instructions` (`id`),
  CONSTRAINT `fk_inventory_unit_departemen` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kapasitas` FOREIGN KEY (`kapasitas_unit_id`) REFERENCES `kapasitas` (`id_kapasitas`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kontrak_spesifikasi` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_model` FOREIGN KEY (`model_unit_id`) REFERENCES `model_unit` (`id_model_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`),
  CONSTRAINT `fk_inventory_unit_status` FOREIGN KEY (`status_unit_id`) REFERENCES `status_unit` (`id_status`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_tipe` FOREIGN KEY (`tipe_unit_id`) REFERENCES `tipe_unit` (`id_tipe_unit`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Data unit utama - komponen disimpan di inventory_attachment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_unit`
--

LOCK TABLES `inventory_unit` WRITE;
/*!40000 ALTER TABLE `inventory_unit` DISABLE KEYS */;
INSERT INTO `inventory_unit` VALUES (1,1,'unit 1',122,2025,3,'MSI',2,'2025-09-04 00:00:00','',9000000.00,NULL,44,2,1,19,9,6,2,5,NULL,'',4,'',4,3,4,'2025-08-12 02:22:14','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',28,123,NULL,NULL,NULL,NULL),(2,2,'unit 2',123,2025,3,'MSI',2,'2025-09-04 00:00:00','',9000000.00,NULL,44,2,1,19,10,3,10,2,NULL,'',2,'',2,2,3,'2025-08-12 03:15:49','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',28,123,NULL,NULL,NULL,NULL),(4,4,'test',38,2025,3,'POS 1',3,'2025-09-12 00:00:00',NULL,12000000.00,NULL,55,4,3,39,12,6,6,2,NULL,'test',2,'test',2,1,3,'2025-08-12 04:47:28','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',36,125,NULL,NULL,NULL,NULL),(5,7,'test2',38,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,19776000.00,NULL,54,3,2,37,12,6,6,2,NULL,'test2',2,'test2',2,1,3,'2025-08-12 04:49:52','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]',38,127,NULL,NULL,NULL,NULL),(6,8,'test3',38,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,89000000.00,NULL,57,6,5,41,12,6,6,2,NULL,'test3',2,'test3',2,1,3,'2025-08-12 04:50:15','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA\",\"BIO METRIC\",\"P3K\"]',39,128,NULL,NULL,NULL,NULL),(7,3,'test4',38,2025,3,'POS 1',3,'2025-09-12 00:00:00',NULL,15000000.00,NULL,56,5,4,40,12,6,6,2,NULL,'test4',2,'test4',2,1,3,'2025-08-12 04:54:09','2025-09-25 10:58:07','[]',37,126,NULL,NULL,NULL,NULL),(8,11,'andara',39,2025,7,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,30,1,NULL,'andara',3,'andara',1,3,1,'2025-08-12 08:15:40','2025-09-13 10:32:32',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,9,'adaaaaa',38,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,89000000.00,NULL,57,6,5,41,12,6,6,2,NULL,'adaaaaa',2,'adaaaaa',2,1,3,'2025-08-12 08:21:22','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"ACRYLIC\",\"P3K\",\"SAFETY BELT INTERLOC\",\"SPARS ARRESTOR\"]',39,128,NULL,NULL,NULL,NULL),(10,6,'adit',38,2025,3,'POS 1',3,'2025-09-12 00:00:00',NULL,12000000.00,NULL,55,4,3,39,12,6,6,2,NULL,'adit',2,'adit',2,1,3,'2025-08-16 04:20:16','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',36,125,NULL,NULL,NULL,NULL),(11,10,'adit',47,2025,3,'POS 1',3,'2025-09-13 00:00:00',NULL,100000000.00,NULL,57,6,5,42,3,6,4,5,NULL,'adit',3,'adit',2,3,2,'2025-08-16 04:20:38','2025-09-25 10:58:07','[\"LAMPU UTAMA\",\"CAMERA AI\",\"SPEED LIMITER\",\"LASER FORK\",\"HORN KLASON\",\"APAR 3 KG\"]',40,129,NULL,NULL,NULL,NULL),(12,5,'adit',39,2025,3,'POS 1',2,'2025-09-12 00:00:00',NULL,19776000.00,NULL,54,3,2,37,2,1,3,1,NULL,'adit',3,'adit',1,3,1,'2025-08-16 04:24:32','2025-09-25 10:58:07',NULL,29,124,NULL,NULL,NULL,NULL),(13,14,'kkai',38,2025,8,'POS 1',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,12,6,6,2,NULL,'adit',2,'adit',2,1,3,'2025-08-16 04:26:43','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,12,'123',39,2025,8,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,3,1,NULL,'123',3,'123',1,3,1,'2025-08-27 02:22:59','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,13,'123',39,2025,8,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,3,1,NULL,'123',3,'123',1,3,1,'2025-08-27 02:23:14','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(16,15,'111',52,2025,8,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,5,3,NULL,'111',3,'111',1,2,2,'2025-08-27 15:35:47','2025-08-30 03:42:03',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,16,'222',52,2025,7,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,1,5,3,NULL,'222',3,'222',1,2,2,'2025-08-27 15:36:17','2025-08-30 02:53:07',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `inventory_unit` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER update_kontrak_totals_after_unit_insert
AFTER INSERT ON inventory_unit
FOR EACH ROW
BEGIN
    IF NEW.kontrak_id IS NOT NULL THEN
        UPDATE kontrak 
        SET total_units = (
            SELECT COUNT(*) 
            FROM inventory_unit 
            WHERE kontrak_id = NEW.kontrak_id
        )
        WHERE id = NEW.kontrak_id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `inventory_unit_backup`
--

DROP TABLE IF EXISTS `inventory_unit_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `inventory_unit_backup` (
  `id_inventory_unit` int(10) unsigned NOT NULL,
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_unit_backup`
--

LOCK TABLES `inventory_unit_backup` WRITE;
/*!40000 ALTER TABLE `inventory_unit_backup` DISABLE KEYS */;
INSERT INTO `inventory_unit_backup` VALUES (4,2,'test',6,NULL,2,NULL,3,'2025-08-19 11:38:34'),(5,2,'test2',6,NULL,2,NULL,3,'2025-08-19 11:38:34'),(6,2,'test3',6,NULL,2,NULL,3,'2025-08-21 15:23:38'),(7,2,'test4',6,NULL,3,'',7,'2025-08-16 01:23:28'),(8,6,'andara',NULL,NULL,NULL,NULL,3,'2025-08-26 16:48:09'),(9,2,'adaaaaa',6,NULL,5,'wae',3,'2025-08-21 15:24:29'),(10,2,'adit',6,NULL,2,NULL,3,'2025-08-18 09:33:12'),(11,3,'adit',NULL,NULL,NULL,NULL,3,'2025-08-21 15:24:56'),(12,6,'adit',NULL,NULL,NULL,NULL,3,'2025-08-16 21:59:26'),(13,2,'adit',6,NULL,2,NULL,3,'2025-08-27 22:34:17'),(14,6,'123',NULL,NULL,NULL,NULL,3,'2025-08-27 13:31:31'),(15,6,'123',NULL,NULL,NULL,NULL,3,'2025-08-27 13:51:14'),(16,3,'111',6,'123',NULL,NULL,3,'2025-08-27 23:53:23'),(17,3,'222',8,'123',3,'ok',7,'2025-08-30 02:53:07');
/*!40000 ALTER TABLE `inventory_unit_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `inventory_unit_components`
--

DROP TABLE IF EXISTS `inventory_unit_components`;
/*!50001 DROP VIEW IF EXISTS `inventory_unit_components`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `inventory_unit_components` AS SELECT
 1 AS `id_inventory_unit`,
  1 AS `no_unit`,
  1 AS `serial_number`,
  1 AS `model_baterai_id`,
  1 AS `sn_baterai`,
  1 AS `merk_baterai`,
  1 AS `tipe_baterai`,
  1 AS `jenis_baterai`,
  1 AS `model_charger_id`,
  1 AS `sn_charger`,
  1 AS `merk_charger`,
  1 AS `tipe_charger`,
  1 AS `model_attachment_id`,
  1 AS `sn_attachment`,
  1 AS `attachment_tipe`,
  1 AS `attachment_merk`,
  1 AS `attachment_model` */;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `jenis_perintah_kerja`
--

DROP TABLE IF EXISTS `jenis_perintah_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jenis_perintah_kerja` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_perintah_kerja`
--

LOCK TABLES `jenis_perintah_kerja` WRITE;
/*!40000 ALTER TABLE `jenis_perintah_kerja` DISABLE KEYS */;
INSERT INTO `jenis_perintah_kerja` VALUES (1,'ANTAR','Antar Unit','Pengantaran unit ke lokasi pelanggan',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(2,'TARIK','Tarik Unit','Penarikan unit dari lokasi pelanggan',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(3,'TUKAR','Tukar Unit','Penukaran unit lama dengan unit baru',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(4,'RELOKASI','Relokasi Unit','Pemindahan unit antar lokasi',1,'2025-09-03 08:58:54','2025-09-03 08:58:54');
/*!40000 ALTER TABLE `jenis_perintah_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jenis_roda`
--

DROP TABLE IF EXISTS `jenis_roda`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `jenis_roda` (
  `id_roda` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_roda` varchar(100) NOT NULL,
  PRIMARY KEY (`id_roda`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jenis_roda`
--

LOCK TABLES `jenis_roda` WRITE;
/*!40000 ALTER TABLE `jenis_roda` DISABLE KEYS */;
INSERT INTO `jenis_roda` VALUES (1,'3-Wheel'),(2,'4-Wheel'),(3,'3-Way '),(4,'4-Way Multi-Directional (FFL)');
/*!40000 ALTER TABLE `jenis_roda` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kapasitas`
--

DROP TABLE IF EXISTS `kapasitas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kapasitas` (
  `id_kapasitas` int(11) NOT NULL AUTO_INCREMENT,
  `kapasitas_unit` varchar(50) NOT NULL,
  PRIMARY KEY (`id_kapasitas`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kapasitas`
--

LOCK TABLES `kapasitas` WRITE;
/*!40000 ALTER TABLE `kapasitas` DISABLE KEYS */;
INSERT INTO `kapasitas` VALUES (1,'200 kg'),(2,'300 kg'),(3,'390 kg'),(4,'430 kg'),(5,'500 kg'),(6,'600 kg'),(7,'800 kg'),(8,'1 Ton'),(9,'1,2 Ton'),(10,'1,25 Ton'),(11,'1,3 Ton'),(12,'1,35 Ton'),(13,'1,4 Ton'),(14,'1,5 Ton'),(15,'1,6 Ton'),(16,'1,7 Ton'),(17,'1,75 Ton'),(18,'1,8 Ton'),(19,'2 Ton'),(20,'2,1 Ton'),(21,'2,2 Ton'),(22,'2,4 Ton'),(23,'2,5 Ton'),(24,'2,7 Ton'),(25,'3 Ton'),(26,'3,3 Ton'),(27,'3,5 Ton'),(28,'3,8 Ton'),(29,'4 Ton'),(30,'4,5 Ton'),(31,'5 Ton'),(32,'5,5 Ton'),(33,'6 Ton'),(34,'7 Ton'),(35,'8 Ton'),(36,'9 Ton'),(37,'10 Ton'),(38,'11 Ton'),(39,'12 Ton'),(40,'13 Ton'),(41,'14 Ton'),(42,'15 Ton'),(43,'16 Ton'),(44,'18 Ton'),(45,'20 Ton'),(46,'23 Ton'),(47,'25 Ton'),(48,'30 Ton'),(49,'32 Ton'),(50,'35 Ton'),(51,'40 Ton'),(52,'45 Ton'),(53,'48 Ton'),(54,'50 Ton'),(55,'55 Ton'),(56,'60 Ton'),(57,'65 Ton'),(58,'70 Ton');
/*!40000 ALTER TABLE `kapasitas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kontrak`
--

DROP TABLE IF EXISTS `kontrak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontrak` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `customer_location_id` int(11) DEFAULT NULL,
  `no_kontrak` varchar(100) NOT NULL,
  `no_po_marketing` varchar(100) DEFAULT NULL,
  `nilai_total` decimal(15,2) DEFAULT NULL COMMENT 'Nilai total kontrak dalam rupiah',
  `total_units` int(10) unsigned NOT NULL DEFAULT 0 COMMENT 'Total unit yang terkait dengan kontrak ini',
  `jenis_sewa` enum('BULANAN','HARIAN') DEFAULT 'BULANAN' COMMENT 'Jenis periode sewa',
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `status` enum('Aktif','Berakhir','Pending','Dibatalkan') NOT NULL DEFAULT 'Pending',
  `dibuat_oleh` int(10) unsigned DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontrak`
--

LOCK TABLES `kontrak` WRITE;
/*!40000 ALTER TABLE `kontrak` DISABLE KEYS */;
INSERT INTO `kontrak` VALUES (44,NULL,'KNTRK/2208/0001','PO-ADIT10998',18000000.00,2,'BULANAN','2025-09-01','2025-09-01','Aktif',1,'2025-09-01 01:54:45','2025-09-10 03:08:34'),(54,NULL,'KNTRK/2209/0001',NULL,0.00,0,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-09 09:54:01','2025-09-13 01:48:42'),(55,NULL,'SML/DS/121025',NULL,0.00,0,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-12 06:34:46','2025-09-12 09:49:35'),(56,NULL,'TEST/AUTO/001',NULL,0.00,0,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-12 09:54:47','2025-09-13 01:20:35'),(57,NULL,'test/1/1/5','12345',0.00,0,'BULANAN','2025-09-13','2025-09-13','Aktif',1,'2025-09-13 02:56:22','2025-09-13 03:43:58'),(58,NULL,'test/1/1/6','12345',0.00,0,'HARIAN','2025-09-13','2025-09-13','Pending',1,'2025-09-15 08:06:12','2025-09-15 08:06:12');
/*!40000 ALTER TABLE `kontrak` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER tr_kontrak_status_update
AFTER UPDATE ON kontrak
FOR EACH ROW
BEGIN
    
    
    IF NEW.status != OLD.status THEN
        INSERT INTO kontrak_status_changes (kontrak_id, old_status, new_status, changed_at)
        VALUES (NEW.id, OLD.status, NEW.status, NOW())
        ON DUPLICATE KEY UPDATE 
            old_status = OLD.status,
            new_status = NEW.status,
            changed_at = NOW();
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `kontrak_spesifikasi`
--

DROP TABLE IF EXISTS `kontrak_spesifikasi`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontrak_spesifikasi` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `kontrak_id` int(10) unsigned NOT NULL,
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
  KEY `fk_kontrak_spesifikasi_kontrak` (`kontrak_id`),
  CONSTRAINT `fk_kontrak_spesifikasi_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontrak_spesifikasi`
--

LOCK TABLES `kontrak_spesifikasi` WRITE;
/*!40000 ALTER TABLE `kontrak_spesifikasi` DISABLE KEYS */;
INSERT INTO `kontrak_spesifikasi` VALUES (19,44,'SPEC-001',2,0,9000000.00,NULL,'',2,6,'HAND PALLET',41,'HELI',NULL,'FORK POSITIONER',NULL,'Lithium-ion',5,22,6,1,2,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]','2025-09-01 01:55:43','2025-09-01 01:55:43'),(37,54,'SPEC-001',2,0,19776000.00,NULL,'',2,6,'PALLET STACKER',42,'HELI',NULL,'FORKLIFT SCALE',NULL,'Lead Acid',1,14,6,1,1,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]','2025-09-09 10:03:32','2025-09-09 10:03:32'),(39,55,'SPEC-001',2,0,12000000.00,NULL,'',1,6,'THREE WHEEL',14,'HANGCHA',NULL,'FORK POSITIONER',NULL,NULL,NULL,16,6,2,2,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]','2025-09-12 06:35:44','2025-09-12 06:35:44'),(40,56,'SPEC-001',1,0,15000000.00,NULL,NULL,2,6,NULL,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-12 09:55:35','2025-09-12 09:55:35'),(41,57,'SPEC-001',2,0,89000000.00,NULL,'',3,6,'COUNTER BALANCE',40,'HELI',NULL,'FORK POSITIONER',NULL,NULL,NULL,16,3,1,3,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA\",\"BIO METRIC\",\"ACRYLIC\",\"P3K\",\"SAFETY BELT INTERLOC\",\"SPARS ARRESTOR\"]','2025-09-13 02:57:03','2025-09-13 02:57:03'),(42,57,'SPEC-002',1,0,100000000.00,NULL,'',3,6,'PALLET STACKER',42,'KOMATSU',NULL,'FORK POSITIONER',NULL,NULL,NULL,17,3,4,2,'[\"LAMPU UTAMA\",\"CAMERA AI\",\"SPEED LIMITER\",\"LASER FORK\",\"HORN KLASON\",\"APAR 3 KG\"]','2025-09-13 03:34:55','2025-09-13 03:34:55'),(43,58,'SPEC-001',2,0,NULL,100000000.00,'',3,6,'HAND PALLET',41,'LINDE',NULL,'FORK POSITIONER',NULL,NULL,NULL,15,6,1,3,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\"]','2025-09-15 08:09:13','2025-09-15 08:09:13'),(44,56,'SPEC-002',1,0,1000000.00,NULL,'',1,NULL,NULL,NULL,NULL,NULL,'SIDE SHIFTER','','',0,NULL,NULL,NULL,NULL,NULL,'2025-09-16 08:13:27','2025-09-16 08:13:27');
/*!40000 ALTER TABLE `kontrak_spesifikasi` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_kontrak_spesifikasi_aksesoris_insert`
    AFTER INSERT ON `kontrak_spesifikasi`
    FOR EACH ROW
BEGIN
    
    IF NEW.aksesoris IS NOT NULL THEN
        UPDATE `inventory_unit` 
        SET `aksesoris` = NEW.aksesoris,
            `kontrak_spesifikasi_id` = NEW.id,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `kontrak_id` = NEW.kontrak_id 
        AND `kontrak_spesifikasi_id` IS NULL;
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
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_kontrak_spesifikasi_aksesoris_update`
    AFTER UPDATE ON `kontrak_spesifikasi`
    FOR EACH ROW
BEGIN
    
    IF NEW.aksesoris IS NOT NULL AND (OLD.aksesoris IS NULL OR OLD.aksesoris != NEW.aksesoris) THEN
        UPDATE `inventory_unit` 
        SET `aksesoris` = NEW.aksesoris,
            `updated_at` = CURRENT_TIMESTAMP
        WHERE `kontrak_spesifikasi_id` = NEW.id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `kontrak_status_changes`
--

DROP TABLE IF EXISTS `kontrak_status_changes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `kontrak_status_changes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `kontrak_id` int(11) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kontrak_id` (`kontrak_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontrak_status_changes`
--

LOCK TABLES `kontrak_status_changes` WRITE;
/*!40000 ALTER TABLE `kontrak_status_changes` DISABLE KEYS */;
INSERT INTO `kontrak_status_changes` VALUES (1,44,'Berakhir','Aktif','2025-09-04 07:48:00',0),(3,54,'Berakhir','Aktif','2025-09-12 09:31:11',0),(4,55,'Pending','Aktif','2025-09-12 09:49:35',0),(11,56,'Pending','Aktif','2025-09-13 01:20:35',0),(14,57,'Pending','Aktif','2025-09-13 02:59:38',0);
/*!40000 ALTER TABLE `kontrak_status_changes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesin`
--

DROP TABLE IF EXISTS `mesin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `mesin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `merk_mesin` varchar(100) NOT NULL,
  `model_mesin` varchar(100) NOT NULL,
  `bahan_bakar` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesin`
--

LOCK TABLES `mesin` WRITE;
/*!40000 ALTER TABLE `mesin` DISABLE KEYS */;
INSERT INTO `mesin` VALUES (1,'TOYOTA','1DZ-0196006','Diesel'),(2,'TOYOTA','1DZ-0197191','Diesel'),(3,'TOYOTA','1DZ-0218846','Diesel'),(4,'TOYOTA','1DZ-0226001','Diesel'),(5,'TOYOTA','1DZ-0226519','Diesel'),(6,'TOYOTA','1DZ-0226619','Diesel'),(7,'TOYOTA','1DZ-0227007','Diesel'),(8,'TOYOTA','1DZ-0230507','Diesel'),(9,'TOYOTA','1DZ-0238684','Diesel'),(10,'TOYOTA','1DZ-0238791','Diesel'),(11,'TOYOTA','1DZ-0239268','Diesel'),(12,'TOYOTA','1DZ-0239357','Diesel'),(13,'TOYOTA','1DZ-0239361','Diesel'),(14,'TOYOTA','1DZ-0239365','Diesel'),(15,'TOYOTA','1DZ-0244305','Diesel'),(16,'TOYOTA','1DZ-0247102','Diesel'),(17,'TOYOTA','1DZ-0247201','Diesel'),(18,'TOYOTA','1DZ-0250918','Diesel'),(19,'TOYOTA','1DZ-0251498','Diesel'),(20,'TOYOTA','1DZ-0252270','Diesel'),(21,'TOYOTA','1DZ-0252764','Diesel'),(22,'TOYOTA','1DZ-0252821','Diesel'),(23,'TOYOTA','1DZ-0252893','Diesel'),(24,'TOYOTA','1DZ-0347648','Diesel'),(25,'TOYOTA','1DZ-0356711','Diesel'),(26,'TOYOTA','1DZ-0358669','Diesel'),(27,'TOYOTA','1DZ-0358919','Diesel'),(28,'TOYOTA','1DZ-0359781','Diesel'),(29,'TOYOTA','1DZ-0360733','Diesel'),(30,'TOYOTA','1DZ-0360739','Diesel'),(31,'TOYOTA','1DZ-0360741','Diesel'),(32,'TOYOTA','1DZ-0360852','Diesel'),(33,'TOYOTA','1DZ-0360939','Diesel'),(34,'TOYOTA','1DZ-0360952','Diesel'),(35,'TOYOTA','1DZ-0360953','Diesel'),(36,'TOYOTA','1DZ-0361046','Diesel'),(37,'TOYOTA','1DZ-0361052','Diesel'),(38,'TOYOTA','1DZ-0361053','Diesel'),(39,'TOYOTA','1DZ-0361055','Diesel'),(40,'TOYOTA','1DZ-0361058','Diesel'),(41,'TOYOTA','1DZ-0361063','Diesel'),(42,'TOYOTA','1DZ-0361065','Diesel'),(43,'TOYOTA','1DZ-0361137','Diesel'),(44,'TOYOTA','1DZ-0361153','Diesel'),(45,'TOYOTA','1DZ-0361345','Diesel'),(46,'TOYOTA','1DZ-0362712','Diesel'),(47,'TOYOTA','1DZ-0364387','Diesel'),(48,'TOYOTA','1DZ-0364398','Diesel'),(49,'TOYOTA','1DZ-0364710','Diesel'),(50,'TOYOTA','1DZ-0365850','Diesel'),(51,'TOYOTA','1DZ-0370103','Diesel'),(52,'TOYOTA','1DZ-0372317','Diesel'),(53,'TOYOTA','1DZ-0372319','Diesel'),(54,'TOYOTA','1DZ-0372355','Diesel'),(55,'TOYOTA','1DZ-0372358','Diesel'),(56,'TOYOTA','1DZ-0372476','Diesel'),(57,'TOYOTA','1DZ-0372530','Diesel'),(58,'TOYOTA','1DZ-0372561','Diesel'),(59,'TOYOTA','1DZ-0372563','Diesel'),(60,'TOYOTA','1DZ-0372608','Diesel'),(61,'TOYOTA','1DZ-0372612','Diesel'),(62,'TOYOTA','1DZ-0372632','Diesel'),(63,'TOYOTA','1DZ-0372634','Diesel'),(64,'TOYOTA','1DZ-0372659','Diesel'),(65,'TOYOTA','1DZ-0400513','Diesel'),(66,'TOYOTA','1DZ-0414691','Diesel'),(67,'TOYOTA','1DZ-0414776','Diesel'),(68,'TOYOTA','1DZ-0414785','Diesel'),(69,'TOYOTA','1DZ-0414786','Diesel'),(70,'TOYOTA','1DZ-0415466','Diesel'),(71,'TOYOTA','1DZ-0415779','Diesel'),(72,'TOYOTA','1DZ-0415814','Diesel'),(73,'TOYOTA','1DZ-0415816','Diesel'),(74,'TOYOTA','1DZ-0415941','Diesel'),(75,'TOYOTA','1DZ-0416059','Diesel'),(76,'TOYOTA','1DZ-0416061','Diesel'),(77,'TOYOTA','1DZ-0416087','Diesel'),(78,'TOYOTA','1DZ-0416090','Diesel'),(79,'TOYOTA','1DZ-0416241','Diesel'),(80,'TOYOTA','1DZ-0416404','Diesel'),(81,'TOYOTA','1DZ-0416894','Diesel'),(82,'TOYOTA','1DZ-0416938','Diesel'),(83,'TOYOTA','1DZ-0416943','Diesel'),(84,'TOYOTA','1DZ-0416976','Diesel'),(85,'TOYOTA','1DZ-0416980','Diesel'),(86,'TOYOTA','1DZ-0417098','Diesel'),(87,'TOYOTA','1DZ-0417218','Diesel'),(88,'TOYOTA','1DZ-0417230','Diesel'),(89,'TOYOTA','1DZ-0417607','Diesel'),(90,'TOYOTA','1DZ-0417632','Diesel'),(91,'TOYOTA','1DZ-0417662','Diesel'),(92,'TOYOTA','1DZ-0417690','Diesel'),(93,'TOYOTA','14Z-0014579','Diesel'),(94,'TOYOTA','14Z-0014581','Diesel'),(95,'TOYOTA','14Z-0015050','Diesel'),(96,'TOYOTA','14Z-0015658','Diesel'),(97,'TOYOTA','14Z-0015662','Diesel'),(98,'TOYOTA','14Z-0015671','Diesel'),(99,'TOYOTA','14Z-0015673','Diesel'),(100,'TOYOTA','14Z-0015686','Diesel'),(101,'TOYOTA','14Z-0015691','Diesel'),(102,'TOYOTA','14Z-0015692','Diesel'),(103,'TOYOTA','14Z-0028118','Diesel'),(104,'TOYOTA','14Z-0028134','Diesel'),(105,'TOYOTA','14Z-0028140','Diesel'),(106,'TOYOTA','14Z-0028150','Diesel'),(107,'TOYOTA','14Z-0028165','Diesel'),(108,'TOYOTA','14Z-0028179','Diesel'),(109,'TOYOTA','14Z-0028203','Diesel'),(110,'TOYOTA','14Z-0028241','Diesel'),(111,'TOYOTA','4Y-2351413','Bensin / LPG'),(112,'TOYOTA','4Y-2355314','Bensin / LPG'),(113,'TOYOTA','4Y-2355860','Bensin / LPG'),(114,'TOYOTA','4Y-2356048','Bensin / LPG'),(115,'TOYOTA','4Y-2356096','Bensin / LPG'),(116,'TOYOTA','4Y-2366516','Bensin / LPG'),(117,'TOYOTA','4Y-2369406','Bensin / LPG'),(118,'TOYOTA','4Y-2371882','Bensin / LPG'),(119,'TOYOTA','4Y-2372927','Bensin / LPG'),(120,'TOYOTA','4Y-2373823','Bensin / LPG'),(121,'TOYOTA','4Y-2376533','Bensin / LPG'),(122,'TOYOTA','4Y-2379846','Bensin / LPG'),(123,'TOYOTA','4Y-2380448','Bensin / LPG'),(124,'TOYOTA','4Y-2386387 matik','Bensin / LPG'),(125,'TOYOTA','4Y-2386473 matik','Bensin / LPG'),(126,'MITSUBISHI','S4S-3.331','Diesel'),(127,'MITSUBISHI','S4S-21497','Diesel'),(128,'MITSUBISHI','S4S-214563','Diesel'),(129,'MITSUBISHI','S4S-217084','Diesel'),(130,'MITSUBISHI','S4S-218625','Diesel'),(131,'MITSUBISHI','S4S-219720','Diesel'),(132,'MITSUBISHI','S4S-219725','Diesel'),(133,'MITSUBISHI','S4S-220849','Diesel'),(134,'MITSUBISHI','S4S-220851','Diesel'),(135,'MITSUBISHI','S4S-220936','Diesel'),(136,'MITSUBISHI','S4S-222774','Diesel'),(137,'MITSUBISHI','S4S-224384','Diesel'),(138,'MITSUBISHI','S4S-224766','Diesel'),(139,'MITSUBISHI','S4S-224839','Diesel'),(140,'MITSUBISHI','S4S-225323','Diesel'),(141,'MITSUBISHI','S4S-227-973','Diesel'),(142,'MITSUBISHI','S4S-228-487','Diesel'),(143,'MITSUBISHI','S4S-229-265','Diesel'),(144,'MITSUBISHI','S4S-231537','Diesel'),(145,'MITSUBISHI','S4S-232202','Diesel'),(146,'MITSUBISHI','S4S-232672','Diesel'),(147,'MITSUBISHI','S4S-232878','Diesel'),(148,'MITSUBISHI','S4S-234502','Diesel'),(149,'MITSUBISHI','S4S-234503','Diesel'),(150,'MITSUBISHI','S4S-234505','Diesel'),(151,'MITSUBISHI','S4S-234506','Diesel'),(152,'MITSUBISHI','S4S-234507','Diesel'),(153,'MITSUBISHI','S4S-237854','Diesel'),(154,'MITSUBISHI','S4S-240392','Diesel'),(155,'MITSUBISHI','S4S-240574','Diesel'),(156,'MITSUBISHI','S4S-242543','Diesel'),(157,'MITSUBISHI','S4S-242544','Diesel'),(158,'MITSUBISHI','S4S-242550','Diesel'),(159,'MITSUBISHI','S4S-242750','Diesel'),(160,'MITSUBISHI','S4S-243432','Diesel'),(161,'MITSUBISHI','S4S-243437','Diesel'),(162,'MITSUBISHI','S4S-243448','Diesel'),(163,'MITSUBISHI','S4S-243475','Diesel'),(164,'MITSUBISHI','S4S-243632','Diesel'),(165,'MITSUBISHI','S4S-243687','Diesel'),(166,'MITSUBISHI','S4S-243690','Diesel'),(167,'MITSUBISHI','S4S-243768','Diesel'),(168,'MITSUBISHI','S4S-243770','Diesel'),(169,'MITSUBISHI','S4S-243939','Diesel'),(170,'MITSUBISHI','S4S-244106','Diesel'),(171,'MITSUBISHI','S4S-244110','Diesel'),(172,'MITSUBISHI','S6S-5B1L','Diesel'),(173,'MITSUBISHI','S6S-040644','Diesel'),(174,'MITSUBISHI','S6S-072650','Diesel'),(175,'MITSUBISHI','S6S-082680','Diesel'),(176,'MITSUBISHI','S6S-082682','Diesel'),(177,'MITSUBISHI','S6S-082735','Diesel'),(178,'MITSUBISHI','S6S-082736','Diesel'),(179,'MITSUBISHI','S6S-083725','Diesel'),(180,'MITSUBISHI','S6S-083726','Diesel'),(181,'MITSUBISHI','S6S-083774','Diesel'),(182,'MITSUBISHI','S6S-088522','Diesel'),(183,'MITSUBISHI','S6S-088558','Diesel'),(184,'MITSUBISHI','S6S-089071','Diesel'),(185,'MITSUBISHI','S6S-KDN2V','Diesel'),(186,'YANMAR','4TNE98-BQDFC','Diesel'),(188,'NISSAN','QD32','Diesel'),(189,'NISSAN','K21','Bensin / LPG'),(190,'NISSAN','K25-1608228Y','Bensin / LPG'),(191,'DOOSAN','DB58S','Diesel'),(192,'ISUZU','6BG1','Diesel'),(193,'QUANCHAI','QC490GP','Diesel'),(194,'WEICHAI','WP10.380E32','Diesel');
/*!40000 ALTER TABLE `mesin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_log`
--

DROP TABLE IF EXISTS `migration_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_log` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_log`
--

LOCK TABLES `migration_log` WRITE;
/*!40000 ALTER TABLE `migration_log` DISABLE KEYS */;
INSERT INTO `migration_log` VALUES (1,'consolidate_components_to_inventory_attachment','2025-08-30 03:42:03','Konsolidasi battery/charger/attachment ke inventory_attachment sebagai single source of truth','SUCCESS',NULL),(2,'workflow_implementation_20250903','2025-09-03 15:58:54','Workflow implementation completed successfully','SUCCESS',NULL);
/*!40000 ALTER TABLE `migration_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_log_di_workflow`
--

DROP TABLE IF EXISTS `migration_log_di_workflow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migration_log_di_workflow` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) DEFAULT NULL,
  `action` text DEFAULT NULL,
  `status` enum('SUCCESS','ERROR','SKIPPED') DEFAULT NULL,
  `error_message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migration_log_di_workflow`
--

LOCK TABLES `migration_log_di_workflow` WRITE;
/*!40000 ALTER TABLE `migration_log_di_workflow` DISABLE KEYS */;
INSERT INTO `migration_log_di_workflow` VALUES (1,'delivery_instructions','Add jenis_perintah_kerja_id','SUCCESS',NULL,'2025-09-03 09:50:51'),(2,'delivery_instructions','Add tujuan_perintah_kerja_id','SUCCESS',NULL,'2025-09-03 09:50:51'),(3,'delivery_instructions','Add status_eksekusi_workflow_id','SUCCESS',NULL,'2025-09-03 09:50:51'),(4,'delivery_instructions','Add FK jenis_perintah_kerja','SUCCESS',NULL,'2025-09-03 09:50:51'),(5,'delivery_instructions','Add FK tujuan_perintah_kerja','SUCCESS',NULL,'2025-09-03 09:50:51'),(6,'delivery_instructions','Add FK status_eksekusi_workflow','SUCCESS',NULL,'2025-09-03 09:50:51'),(7,'delivery_instructions','Update existing records with default workflow values','SUCCESS',NULL,'2025-09-03 09:50:51');
/*!40000 ALTER TABLE `migration_log_di_workflow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `migrations` (
  `id` bigint(20) unsigned NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (7,'2024-01-01-000001','App\\Database\\Migrations\\CreateUsersTable','default','App',1751956548,1),(8,'2024-01-15-000001','App\\Database\\Migrations\\CreateForkliftTable','default','App',1751956548,1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_unit`
--

DROP TABLE IF EXISTS `model_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `model_unit` (
  `id_model_unit` int(11) NOT NULL AUTO_INCREMENT,
  `merk_unit` varchar(100) NOT NULL,
  `model_unit` varchar(100) NOT NULL,
  PRIMARY KEY (`id_model_unit`)
) ENGINE=InnoDB AUTO_INCREMENT=556 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_unit`
--

LOCK TABLES `model_unit` WRITE;
/*!40000 ALTER TABLE `model_unit` DISABLE KEYS */;
INSERT INTO `model_unit` VALUES (1,'AVANT','M420MSDTT'),(2,'BT','RRE160MC'),(3,'BT','LPE200'),(4,'CAT','EP15TCA'),(5,'CAT','NRS18CA'),(6,'CAT','EP20CA'),(7,'CAT','DP25ND'),(8,'CAT','DP25NT'),(9,'CAT','DP25ND 25P30'),(10,'CAT','Hand Lift'),(11,'CAT','DP25ND 2SP30'),(12,'CAT','DP25NDC'),(13,'CAT','GP25ND'),(14,'CAT','DP25ND - 2SP30'),(15,'CAT','DP45N'),(16,'CAT','GP25NT'),(17,'CAT','DP25ND-C / 2SP30'),(18,'CAT','DP30ND'),(19,'CAT','DP30ND 25P30'),(20,'CAT','DP30ND 2SP30'),(21,'CAT','DP30ND - 2SP30'),(22,'CAT','GP30ND'),(23,'CAT','DP30ND - 3FP47'),(24,'CAT','DP30ND 2SP50'),(25,'CAT','GP35ND'),(26,'CAT','DP35ND'),(27,'CAT','DP40ND'),(28,'CAT','DP40KT'),(29,'CAT','DP40KD'),(30,'CAT','DP40KLT'),(31,'CAT','DP 40 KT'),(32,'CAT','DP40NT'),(33,'CAT','DP40N'),(34,'CAT','DP45N - 3FP43 - PS/PS'),(35,'CAT','DP50NT'),(36,'CAT','DP50N'),(37,'CAT','DP70T'),(38,'CLARK','-'),(39,'CROWN','RD5725-30'),(40,'CROWN','RD5795S-30'),(41,'CROWN','RD5795 S-32TT366'),(42,'CROWN','RD5725-32'),(43,'CROWN','RDS795S-32TT442'),(44,'CROWN','RD5795S-32TT442'),(45,'CROWN','RDS5700'),(46,'CROWN','RD5795S-32'),(47,'CROWN','SC5240-40'),(48,'CROWN','RR5725-45'),(49,'CROWN','RDS7955-32TT240'),(50,'CROWN','RR5795S-45'),(51,'CROWN','RR57955-45'),(52,'CROWN','RMD6095-32'),(53,'CROWN','RMD6095S-32'),(54,'CROWN','PM 10'),(55,'CROWN','RT 09'),(56,'CROWN','RD5795S-32TT400'),(57,'CROWN','RD572532'),(58,'CROWN','RR5795S-45TT341'),(59,'CROWN','RMD 6095S-32TT505'),(60,'DOOSAN','D25G'),(61,'DOOSAN','D25G-3.210M'),(62,'DOOSAN','D25G-4.710M'),(63,'DOOSAN','D25G-3M'),(64,'DOOSAN','D25D-5.540MM'),(65,'DOOSAN','D25G-5.990MM'),(66,'DOOSAN','D25G-5.540M'),(67,'DOOSAN','G25G-5.990M'),(68,'DOOSAN','D30G'),(69,'DOOSAN','D30G-3M'),(70,'DOOSAN','D30G-4.710M'),(71,'DOOSAN','D30G-5990M'),(72,'DOOSAN','D30G-5990MM'),(73,'DOOSAN','D50C-5'),(74,'DOOSAN','D50C-5-3.050M'),(75,'DOOSAN','D50C-5-4.000M'),(76,'DOOSAN','D50C-5-5.925M'),(77,'DOOSAN','D50C-5-4.125M'),(78,'DOOSAN','D50C-5-5925M'),(79,'DOOSAN','D50C5-5.925M'),(80,'DOOSAN','D70S-5'),(81,'DOOSAN','D70S-5-3M'),(82,'DOOSAN','D70C-5-3.000M'),(83,'DOOSAN','D70S-3M'),(84,'DOOSAN','D70S-5.5M'),(85,'DOOSAN','D70S-5.6.000M'),(86,'DOOSAN','D90S-5-3.300M'),(87,'DOOSAN','D90S-5.300MM'),(88,'DOOSAN','D160S-5'),(89,'DOOSAN','D160S-5-3000M'),(90,'DOOSAN','D160S-5.300M'),(91,'DOOSAN','D160S-4'),(92,'DOOSAN','DV250S-7/dv300S-7'),(93,'DYNAPAC','CC1250'),(94,'ENSIGN','YX635'),(95,'ENSIGN','YX646'),(96,'EP','ES12-12CS'),(97,'EP','RPL301'),(98,'EP','RPL251'),(99,'EP','RSC152'),(100,'EP','ES-16-16EX'),(101,'EP','KPL201'),(102,'EP','F4'),(103,'EP','EFL253S'),(104,'EP','CPC30T3'),(105,'EP','EFL303S'),(106,'EP','CPC35T3'),(107,'EP','EFL352'),(108,'EP','EFL353S'),(109,'EP','ES-10-10CX'),(110,'EP','ES10-12CX'),(111,'HAKO','D1200RH'),(112,'HAKO','SCM-B75R'),(113,'HANGCHA','CDD12-AC1S-P'),(114,'HANGCHA','CPDB12-AC1S'),(115,'HANGCHA','CPDB12-ACIS-I'),(116,'HANGCHA','CQDB16'),(117,'HANGCHA','CPDB16-ACIS-I'),(118,'HANGCHA','CDD20-AC1S'),(119,'HANGCHA','CPD25-AC3'),(120,'HANGCHA','CBD20'),(121,'HANGCHA','CPDB20-ACIS'),(122,'HANGCHA','CPCD25'),(123,'HANGCHA','CPD25'),(124,'HANGCHA','CQD25-AC2S-I'),(125,'HANGCHA','CQD25'),(126,'HANGCHA','CPD30'),(127,'HANGCHA','CBD30-ACIS-I'),(128,'HANGCHA','CPD50'),(129,'HANGCHA','CDD20-AC1S-P'),(130,'HANGCHA','CDD12-AC18-P'),(131,'HELI','CPD15-GB3LI-M'),(132,'HELI','CPD15-GB2LI-M'),(133,'HELI','CBD15J-LI-S'),(134,'HELI','CPD15-JRD'),(135,'HELI','CQD16-GB2SZLI'),(136,'HELI','CQD16-GB2SLLI'),(137,'HELI','CQD18-A2RLIG2'),(138,'HELI','CQD20'),(139,'HELI','CQD20-GC2RLI'),(140,'HELI','CBD20J-RLI'),(141,'HELI','CDD20-D930'),(142,'HELI','CBD20-J1R'),(143,'HELI','CQD20-GB2SHDLI'),(144,'HELI','CDD20-950'),(145,'HELI','CPD20-GB6LI-S'),(146,'HELI','CPD20-JRD'),(147,'HELI','CPD20SQ-A2LIG3-M'),(148,'HELI','CPD25'),(149,'HELI','CBD15J'),(150,'HELI','CPD25-GB3LI-M'),(151,'HELI','CPCD25-M1K2'),(152,'HELI','CPD25-GB2LI-M'),(153,'HELI','CPD25-GB6LI-S'),(154,'HELI','CPD25FB-HA2HLIB3'),(155,'HELI','CPCD50-M4G3'),(156,'HELI','CPD30'),(157,'HELI','CBD30'),(158,'HELI','CBD30J'),(159,'HELI','CPCD30-Q22K2'),(160,'HELI','CPCD30-M1K2'),(161,'HELI','CBD30J-RLI'),(162,'HELI','CPC30-WS1K2'),(163,'HELI','CPD30-GB2LI-M'),(164,'HELI','CPD30-GB6LI-S'),(165,'HELI','CBD30-460'),(166,'HELI','CPD35'),(167,'HELI','CPCD35-M1K2'),(168,'HELI','CPCD35-Q22K2'),(169,'HELI','CPD35-GB2LI-M'),(170,'HELI','CPD35-GB6LI-S'),(171,'HELI','CPD38'),(172,'HELI','CPD38-GB2LI-M'),(173,'HELI','QYD40S'),(174,'HELI','QYD40S-JE3G2LI'),(175,'HELI','CPCD50-M4K2'),(176,'HELI','CPCD50'),(177,'HELI','CPCD55-M4G3'),(178,'HELI','CPD50-GB3LI'),(179,'HELI','CPD50-G2A11LI'),(180,'HELI','CPD50-GB2LI'),(181,'HELI','CPCD70-W2K2'),(182,'HELI','CPCD100-W2K2'),(183,'HELI','CPCD250-VZ2-12III'),(184,'HELI','CPCD120-CU1-06III'),(185,'HELI','CPCD150'),(186,'HELI','CPCD150-CU-06IIG'),(187,'HELI','CPD20-GE6LI-S'),(188,'HELI','CPCD25-Q22K2'),(189,'HELI','CPD25-BG6LI-S'),(190,'HELI','CQDM20J-LI'),(191,'HELI','CPD20-GB2LI-M'),(192,'HELI','CPD40-GB2LI'),(193,'HELI','CPCD40-M4G3'),(194,'HELI','CPCD160-CU-06IIG'),(195,'HELI','01F-231103'),(196,'HELI','PALETE MOVER'),(197,'HYSTER','-'),(198,'HYSTER','H3.0 TX-98'),(199,'HYUNDAI','18BR-9'),(200,'HYUNDAI','25D-7SA'),(201,'HYUNDAI','25DT-7'),(202,'HYUNDAI','25BR-9'),(203,'HYUNDAI','25B-9F'),(204,'HYUNDAI','20BR-9'),(205,'HYUNDAI','30G-7M'),(206,'HYUNDAI','33DT-7'),(207,'HYUNDAI','30D-7SA'),(208,'HYUNDAI','30DT-F'),(209,'HYUNDAI','30B-9F'),(210,'HYUNDAI','35D-7SA'),(211,'HYUNDAI','40T-9'),(212,'HYUNDAI','50B-9'),(213,'HYUNDAI','50D-9SA'),(214,'HYUNDAI','70DT-7'),(215,'JUNGHEINRICH','ERE120-1150-6700'),(216,'JUNGHEINRICH','ERE120-1150-5400'),(217,'JUNGHEINRICH','ETV116n-1150-9020DZ'),(218,'JUNGHEINRICH','ERE120'),(219,'JUNGHEINRICH','ETV214-1150-8300DZ'),(220,'JUNGHEINRICH','ETV214-1150-8420DZ'),(221,'JUNGHEINRICH','EFGMC325-GTE115-470DZ'),(222,'JUNGHEINRICH','EFGMC325-1150-4700DZ'),(223,'JUNGHEINRICH','ETVMB216-1150-9020DZ'),(224,'JUNGHEINRICH','ETV116-115-9020DZ'),(225,'JUNGHEINRICH','EVT116N-1150-9020DZ'),(226,'JUNGHEINRICH','ETVN116N-1150-9020DZ'),(227,'JUNGHEINRICH','ETV 116n-115-9020 DZ'),(228,'JUNGHEINRICH','ETV 116'),(229,'JUNGHEINRICH','ETVMC320-1150-10520DZ'),(230,'JUNGHEINRICH','ETV120n-1150-11510DZ'),(231,'JUNGHEINRICH','ETV120n-1150-10520DZ'),(232,'JUNGHEINRICH','ETV MB216'),(233,'JUNGHEINRICH','ETV MC320 GNE'),(234,'JUNGHEINRICH','ETV MB216 GE'),(235,'JUNGHEINRICH','ETV MC320'),(236,'KOMATSU','FB17RJX-1'),(237,'KOMATSU','FBR7RJX-1'),(238,'KOMATSU','FD-25C-11'),(239,'KOMATSU','FD25C-14'),(240,'KOMATSU','DP25ND'),(241,'KOMATSU','DP25NT'),(242,'KOMATSU','FD25C-12'),(243,'KOMATSU','FG25C-14'),(244,'KOMATSU','FD25C 14 DIESEL'),(245,'KOMATSU','541009'),(246,'KOMATSU','568238'),(247,'KOMATSU','568291'),(248,'KOMATSU','FD30C-12'),(249,'KOMATSU','FD30JT-12'),(250,'KOMATSU','FB30X-1'),(251,'KOMATSU','540495'),(252,'KOMATSU','WA350-3'),(253,'KOMATSU','FD50T-P'),(254,'KOMATSU','FD60T-1'),(255,'LINDE','R14'),(256,'LINDE','E 15 C'),(257,'LINDE','E 18 P'),(258,'LINDE','R1-6N'),(259,'LOGITRANS','Palet stacker + charger'),(260,'MHE DEMAG','MPR25AC-685'),(261,'MITSUBISHI','FD20-2SP40'),(262,'MITSUBISHI','DP20ND'),(263,'MITSUBISHI','DP25ND'),(264,'MITSUBISHI','FG25NT-3FP47-PS/PS'),(265,'MITSUBISHI','FG25NT-2SP30-PS/PS'),(266,'MITSUBISHI','FG25NT-2SP40-PS/PS'),(267,'MITSUBISHI','FG25ND - 3FP47 - PS/ PS'),(268,'MITSUBISHI','FG25ND - 2SP40 - PS/PS'),(269,'MITSUBISHI','FG25NT'),(270,'MITSUBISHI','FG30ND'),(271,'MITSUBISHI','FD40N-2SP50-P5/PS'),(272,'MITSUBISHI','FD40N-3F43-PS/PS'),(273,'MITSUBISHI','FD40N'),(274,'MITSUBISHI','FD40NT'),(275,'MITSUBISHI','FD50T'),(276,'MITSUBISHI','FD50KT'),(277,'MITSUBISHI','FD 50 DIESEL KT'),(278,'MITSUBISHI','FD150SNL'),(279,'MITSUBISHI','FD50NT'),(280,'MITSUBISHI','FD70NH'),(281,'MYMAX','ZL-946B'),(282,'NICHIYU','FBRM15-75-400'),(283,'NICHIYU','FBRW15-75C-500M'),(284,'NICHIYU','FBT115PN-75C-470M'),(285,'NICHIYU','FBT15PN-75C-470M'),(286,'NICHIYU','FBRW15-75C-600M'),(287,'NICHIYU','FBRW18-75C-700MSF'),(288,'NICHIYU','FBR A W18-50SB-600MWB'),(289,'NICHIYU','FBRA18-63B-400CS'),(290,'NICHIYU','FBRW18-75C-700MSF(600M)'),(291,'NICHIYU','FB20PN-75C-470M'),(292,'NICHIYU','FB20PN-72C-470M'),(293,'NICHIYU','FB20PN-72C-300'),(294,'NICHIYU','PLDP20-70-A12'),(295,'NICHIYU','FB25PN-72C-300'),(296,'NICHIYU','FB25PN-72C-470M'),(297,'NICHIYU','FB25NP-72C'),(298,'NICHIYU','FB25PN-72C-300PFL'),(299,'NICHIYU','FB25PN-72C-470PFL'),(300,'NICHIYU','FBR15-75C-500M'),(301,'NICHIYU','FB25PN-72C-300M'),(302,'NICHIYU','FB25P-75C-300'),(303,'NICHIYU','FB25PN-75C-300M'),(304,'NICHIYU','FB25PN-72C-400'),(305,'NICHIYU','FB25PN-72C-550M'),(306,'NICHIYU','FB25PN-72C-600M'),(307,'NICHIYU','FB30PN-72C-300'),(308,'NICHIYU','FB30PN-72C-300M'),(309,'NICHIYU','FB30PN-75C-300'),(310,'NICHIYU','FB30P-75C-300'),(311,'NICHIYU','FB30PN-72C-430M'),(312,'NICHIYU','FBD10-700C-350'),(313,'NICHIYU','FBD10-700C-250'),(314,'NISSAN','Y1F2M25U-2W300'),(315,'NISSAN','Y1F2M25U-003350'),(316,'NISSAN','Y1F2M25U-003351'),(317,'NISSAN','T1B2L25U-2W300'),(318,'NISSAN','T1B2L25U-3F430'),(319,'NISSAN','D1F5F40U-VM400'),(320,'NISSAN','D1F5F40U-VFH435'),(321,'NISSAN','D1F540U-VFH435'),(322,'NISSAN','D1F50F40U-VM500'),(323,'NISSAN','D1F5F40U-VFH600'),(324,'NISSAN','D1F5F40U-VM500'),(325,'NISSAN','D1F5F50U-VM300'),(326,'NISSAN','D1F5F50U-VM 300'),(327,'NISSAN','D1F5F50U-VM500'),(328,'NISSAN','1F6F70U'),(329,'NISSAN','L1FG6F704/1F6F70U-VM300'),(330,'NISSAN','L1F6F70U-VM300'),(331,'NISSAN','L1F6F70U-VFH600'),(332,'NISSAN','1F6F70U-VM300'),(333,'NISSAN','1F6F70U-VFH600'),(334,'PATRIA','PFG 20T-2'),(335,'PATRIA','PFD-25LC-1'),(336,'PATRIA','-'),(337,'PATRIA','FD30T-0'),(338,'PATRIA','FD35TA-2'),(339,'POWERLIFT','ES12-12CS'),(340,'REYMON','REYMON 762DR32TT'),(341,'REYMON','REYMON 7620'),(342,'SANY','SYZ324C-8W(R)'),(343,'SEM','SEM636D'),(344,'SEM','SEM655F'),(345,'SEM','SEM618D'),(346,'SEM','SEM636F'),(347,'SHANTUI','L-36-B5'),(348,'SHANTUI','L55-B5'),(349,'SHINKO','6FBR15'),(350,'SOOSUNG','SWP 13'),(351,'SOOSUNG','SWR - 1500L'),(352,'SOOSUNG','SBR 20'),(353,'SOOSUNG','SWP 25'),(354,'SOOSUNG','SWP-2500'),(355,'SOOSUNG','SBF-25A'),(356,'SOOSUNG','SST-4000TWG'),(357,'SOOSUNG','SST - 4000'),(358,'SOOSUNG','SWC - 1000L'),(359,'SOOSUNG','SSL-2646'),(360,'SOOSUNG','SSL-3370'),(361,'STILL','EGV-S14'),(362,'STILL','FM-X14'),(363,'STILL','RX50-15'),(364,'STILL','RX 20 -15'),(365,'STILL','RX20-15'),(366,'STILL','VNA NXV'),(367,'STILL','EXP16'),(368,'STILL','FM-X17N'),(369,'STILL','R 20-20P'),(370,'STILL','EXU-20'),(371,'STILL','FM-X20N'),(372,'STILL','FM-X20'),(373,'STILL','LTX20'),(374,'STILL','EXU- S24'),(375,'STILL','FM-X25'),(376,'STILL','ECU 30'),(377,'STILL','RX60-40'),(378,'STILL','RX60-40/600'),(379,'STILL','RX60-50'),(380,'STILL','RX60-50/600'),(381,'STILL','RX60-50/LC500'),(382,'STILL','RX 60-50'),(383,'STILL','RX60-60'),(384,'STILL','GX-X'),(385,'STILL','FM-4W25'),(386,'STILL','EXU-S 24'),(387,'SUMITOMO','61-FBRA15WX'),(388,'SUMITOMO','8FB25PX'),(389,'SUMITOMO','A.899RO2300f'),(390,'TCM','FD25Z1'),(391,'TCM','FD25Z3'),(392,'TCM','FB 25-7'),(393,'TCM','FD25C-NOMA'),(394,'TCM','FD25C-6'),(395,'TCM','FD25T3CZ'),(396,'TCM','FD25T3CZ/FD30T3CZ'),(397,'TCM','FB25P-80C-3F470'),(398,'TCM','FBRW15-85C-600M'),(399,'TCM','FB25-9'),(400,'TCM','FD30C-6'),(401,'TCM','FD30T3CZ'),(402,'TCM','FD30T9'),(403,'TCM','FD40C9 VM3000'),(404,'TCM','FD40T9'),(405,'TCM','FD40T9/FD50T9'),(406,'TCM','FD40T9/(FD50T9)'),(407,'TCM','FD50T-3'),(408,'TCM','FD50T9'),(409,'TCM','FD50T9-VFHM700LF122FDT'),(410,'TCM','FD50Z8'),(411,'TCM','FD50T9B'),(412,'TCM','FD70Z8'),(413,'TCM','FD70Z8T'),(414,'TCM','FD70T9/FD80Z8(EDIT)'),(415,'TCM','FD70T9'),(416,'TCM','FD70Z8B'),(417,'TCM','FD100T'),(418,'TCM','FD 100Z-8'),(419,'TCM','FD100Z8'),(420,'TCM','FD150T'),(421,'TCM','FD150S-3'),(422,'TCM','FD150S-3B'),(423,'TCM','FD230-2'),(424,'TCM','SSL 711'),(425,'TM','TWT 150TOWING'),(426,'TM','NTT 150 TOWING'),(427,'TM','FBR 25-02 ELEC'),(428,'TM','FBR 25-03 ELEC'),(429,'TM','FBR 20 ELEC'),(430,'TM','TEC30XQ ELEC'),(431,'TM','NTT100 TOWING'),(432,'TOW MOTOR','NPP15E2'),(433,'TOYOTA','FBR 1.3'),(434,'TOYOTA','SPE140S'),(435,'TOYOTA','60-8FD15'),(436,'TOYOTA','7FBR15'),(437,'TOYOTA','8FBE15'),(438,'TOYOTA','8FBN15'),(439,'TOYOTA','62-8FD15'),(440,'TOYOTA','LHE150'),(441,'TOYOTA','7FBR18'),(442,'TOYOTA','8FBR18'),(443,'TOYOTA','5FBR-20'),(444,'TOYOTA','5FB-20'),(445,'TOYOTA','8FBE20'),(446,'TOYOTA','8FBRS20'),(447,'TOYOTA','FD25C-14'),(448,'TOYOTA','FD25T-7'),(449,'TOYOTA','7FD25'),(450,'TOYOTA','62 8FD25'),(451,'TOYOTA','8FD25'),(452,'TOYOTA','30 8FG25'),(453,'TOYOTA','60-8FD25'),(454,'TOYOTA','32 8FG25'),(455,'TOYOTA','30-8FG25'),(456,'TOYOTA','32-8FG25'),(457,'TOYOTA','60 -8FD25'),(458,'TOYOTA','60 - 8FD25'),(459,'TOYOTA','62-8FD25'),(460,'TOYOTA','8FBN25'),(461,'TOYOTA','628FD25'),(462,'TOYOTA','FDZN25'),(463,'TOYOTA','LPE250'),(464,'TOYOTA','FBN25'),(465,'TOYOTA','32-FG25'),(466,'TOYOTA','LP250'),(467,'TOYOTA','FGZN25'),(468,'TOYOTA','8FBRS25'),(469,'TOYOTA','FBR'),(470,'TOYOTA','60 - 8FD30'),(471,'TOYOTA','60-8FD30'),(472,'TOYOTA','62-8FD30'),(473,'TOYOTA','7FB30'),(474,'TOYOTA','628FD30'),(475,'TOYOTA','8FB30'),(476,'TOYOTA','FDZN30'),(477,'TOYOTA','8FBN30'),(478,'TOYOTA','8FD30'),(479,'TOYOTA','32-8FG30'),(480,'TOYOTA','5FD35'),(481,'TOYOTA','7FD35'),(482,'TOYOTA','32-7FD35'),(483,'TOYOTA','72-8FDJ35'),(484,'TOYOTA','8FDN35'),(485,'TOYOTA','8FBJ35'),(486,'TOYOTA','CBT 4 TOWING'),(487,'TOYOTA','CBT4'),(488,'TOYOTA','4CBTK4'),(489,'TOYOTA','8FDN40'),(490,'TOYOTA','8FD40N'),(491,'TOYOTA','8FD45N'),(492,'TOYOTA','7FD50'),(493,'TOYOTA','8FD50N'),(494,'TOYOTA','5FBR15'),(495,'TOYOTA','BTLHE150'),(496,'TOYOTA','LHE50'),(497,'XCMG','LW300KN'),(498,'XILIN LI-ION','CBD20R-11'),(499,'YALE','NDR035EB'),(500,'YALE','NDR035EANL36TE143'),(501,'YALE','FBRA18WY'),(502,'YALE','FBR18SZ'),(503,'YALE','MP20XV'),(504,'YALE','MR20HD'),(505,'YALE','GDP25RK'),(506,'YALE','GLP25RK'),(507,'YALE','GLPK25RK'),(508,'YALE','FB25PYE'),(509,'YALE','FB25PZ'),(510,'YALE','FBR25SY'),(511,'YALE','GLP25MX'),(512,'YALE','FB25RZ'),(513,'YALE','GLP30TK'),(514,'YALE','FB30RZ'),(515,'YALE','GLP30MX-BL'),(516,'YALE','NRDR035EA'),(517,'YALE','NDR035EBNL36TE179'),(518,'YALE','NDR035EANL36TE157'),(519,'STILL','GX-X/GX-Q'),(520,'BOBCAT','SKID STEER LOADER, S570'),(521,'TOYOTA','SWE120'),(522,'HELI','CDD15J-RE'),(523,'MITSUBISHI','FD35'),(524,'KOMATSU','FD25JC-12'),(525,'TOYOTA','7FB25'),(526,'KOMATSU','FD50-6'),(527,'TOYOTA','FD20T-6'),(528,'MITSUBISHI','FD70T'),(529,'TOYOTA','FD30ZS700'),(530,'KOMATSU','FD2.5C-14'),(531,'MITSUBISHI','FD30'),(532,'KOMATSU','FD30C-14'),(533,'MITSUBISHI','DP30ND'),(534,'CAT','CPCD70F1'),(535,'MITSUBISHI','FD70 DIESEL'),(536,'TOYOTA','FBR 1.5'),(537,'TOYOTA','5FB25'),(538,'TOYOTA','7FB20'),(539,'SOOSUNG','SBF-255'),(540,'NISSAN','F1F1M15U-2W350'),(541,'PATRIA','PFD25CL-1'),(542,'NICHIYU','FBRW 18-75C-700MSF'),(543,'SOOSUNG','SBF-15'),(544,'TOYOTA','60 8FD30'),(545,'HYSTER','H2.50DX'),(546,'HYSTER','H2.5TX-92'),(547,'CAT','DP30NT'),(548,'JUNGHEINRICH','ETV116-1150-9020DZ'),(549,'YALE','GDP25TK'),(550,'NICHIYU','FB25PN-72C-470'),(551,'CAT','E920TCA'),(552,'NICHIYU','FB25PN-75C-300'),(553,'NICHIYU','FB25P-75C-470M'),(554,'NICHIYU','F25PN-72C-470M'),(555,'CAT','DP25ND3FP47');
/*!40000 ALTER TABLE `model_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_logs`
--

DROP TABLE IF EXISTS `notification_logs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `total_recipients` int(11) DEFAULT 0,
  `successful_deliveries` int(11) DEFAULT 0,
  `failed_deliveries` int(11) DEFAULT 0,
  `processing_time_ms` int(11) DEFAULT NULL,
  `trigger_data` longtext DEFAULT NULL COMMENT 'JSON data that triggered the notification',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_notification` (`notification_id`),
  KEY `idx_rule` (`rule_id`),
  CONSTRAINT `notification_logs_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  CONSTRAINT `notification_logs_ibfk_2` FOREIGN KEY (`rule_id`) REFERENCES `notification_rules` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_logs`
--

LOCK TABLES `notification_logs` WRITE;
/*!40000 ALTER TABLE `notification_logs` DISABLE KEYS */;
/*!40000 ALTER TABLE `notification_logs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_recipients`
--

DROP TABLE IF EXISTS `notification_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_recipients` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `notification_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `is_dismissed` tinyint(1) DEFAULT 0,
  `dismissed_at` timestamp NULL DEFAULT NULL,
  `delivery_method` enum('web','email','sms') DEFAULT 'web',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_notification_user` (`notification_id`,`user_id`),
  KEY `idx_user_unread` (`user_id`,`is_read`),
  KEY `idx_notification` (`notification_id`),
  KEY `idx_recipients_user_read_created` (`user_id`,`is_read`,`created_at`),
  CONSTRAINT `notification_recipients_ibfk_1` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_recipients`
--

LOCK TABLES `notification_recipients` WRITE;
/*!40000 ALTER TABLE `notification_recipients` DISABLE KEYS */;
INSERT INTO `notification_recipients` VALUES (1,1,1,1,'2025-09-10 19:38:11',0,NULL,'web','2025-09-10 06:37:37'),(2,2,1,1,'2025-09-10 19:38:11',1,'2025-09-10 21:52:02','web','2025-09-10 06:37:37'),(5,7,5,0,NULL,0,NULL,'web','2025-09-10 21:37:19'),(6,7,6,0,NULL,0,NULL,'web','2025-09-10 21:37:19'),(7,8,5,0,NULL,0,NULL,'web','2025-09-10 23:48:24'),(8,8,6,0,NULL,0,NULL,'web','2025-09-10 23:48:24'),(9,9,5,0,NULL,0,NULL,'web','2025-09-10 23:54:39'),(10,9,6,0,NULL,0,NULL,'web','2025-09-10 23:54:39'),(11,11,5,0,NULL,0,NULL,'web','2025-09-11 00:02:21'),(12,11,6,0,NULL,0,NULL,'web','2025-09-11 00:02:21'),(13,12,1,1,'2025-09-10 22:02:29',0,NULL,'web','2025-09-11 00:02:21'),(14,13,5,0,NULL,0,NULL,'web','2025-09-11 01:23:26'),(15,13,6,0,NULL,0,NULL,'web','2025-09-11 01:23:26'),(16,14,1,1,'2025-09-10 23:27:12',0,NULL,'web','2025-09-11 01:23:26');
/*!40000 ALTER TABLE `notification_recipients` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_rules`
--

DROP TABLE IF EXISTS `notification_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notification_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `trigger_event` varchar(100) NOT NULL COMMENT 'spk_created, spk_approved, di_processed, inventory_low, etc',
  `is_active` tinyint(1) DEFAULT 1,
  `conditions` longtext DEFAULT NULL COMMENT 'JSON conditions like {"departemen": "DIESEL", "status": "APPROVED"}',
  `target_roles` varchar(500) DEFAULT NULL COMMENT 'Comma-separated: superadmin,manager,supervisor',
  `target_divisions` varchar(500) DEFAULT NULL COMMENT 'Comma-separated: service,marketing,operational',
  `target_departments` varchar(500) DEFAULT NULL COMMENT 'Comma-separated: DIESEL,ELECTRIC,LPG',
  `target_users` varchar(500) DEFAULT NULL COMMENT 'Specific user IDs comma-separated',
  `exclude_creator` tinyint(1) DEFAULT 0 COMMENT 'Exclude notification creator',
  `title_template` varchar(500) NOT NULL COMMENT 'Template with variables like "SPK {{nomor_spk}} untuk {{departemen}}"',
  `message_template` text NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `type` enum('info','success','warning','error','critical') DEFAULT 'info',
  `priority` tinyint(4) DEFAULT 1,
  `url_template` varchar(500) DEFAULT NULL COMMENT 'URL template with variables',
  `delay_minutes` int(11) DEFAULT 0 COMMENT 'Delay notification by X minutes',
  `expire_days` int(11) DEFAULT 30 COMMENT 'Auto-delete after X days',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `auto_include_superadmin` tinyint(1) DEFAULT 1 COMMENT 'Automatically include superadmin in all notifications',
  `target_mixed` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON array for complex multi-targeting: {divisions: [], roles: [], users: [], departments: []}' CHECK (json_valid(`target_mixed`)),
  `rule_description` text DEFAULT NULL COMMENT 'Detailed description of when and why this rule triggers',
  PRIMARY KEY (`id`),
  KEY `idx_trigger_event` (`trigger_event`),
  KEY `idx_active` (`is_active`),
  KEY `idx_rules_event_active` (`trigger_event`,`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_rules`
--

LOCK TABLES `notification_rules` WRITE;
/*!40000 ALTER TABLE `notification_rules` DISABLE KEYS */;
INSERT INTO `notification_rules` VALUES (1,'SPK Created - Service Notification','Notify service division when new SPK is created','spk_created',1,'{}','manager,supervisor,technician','service',NULL,NULL,1,'SPK Baru: {{nomor_spk}} - {{departemen}}','SPK baru telah dibuat untuk {{pelanggan}} dengan departemen {{departemen}}. Silakan periksa dan proses sesuai prosedur.','spk','info',2,'service/spk_service',0,30,NULL,'2025-09-10 03:47:33','2025-09-11 04:45:27',1,NULL,NULL),(2,'SPK DIESEL - Service DIESEL Team','Notify DIESEL service team for DIESEL SPK','spk_created',1,'{\"departemen\": \"DIESEL\"}',NULL,'service','DIESEL',NULL,0,'SPK DIESEL: {{nomor_spk}} - {{pelanggan}}','SPK DIESEL baru memerlukan perhatian tim service DIESEL. Unit: {{unit_info}}, Lokasi: {{lokasi}}','spk','warning',3,'service/spk_service',0,30,NULL,'2025-09-10 03:47:33','2025-09-11 04:45:27',1,NULL,NULL),(3,'DI Ready - Operational Team','Notify operational when DI is ready for processing','di_submitted',1,'{}',NULL,'operational',NULL,NULL,0,'DI Siap Diproses: {{nomor_di}}','Delivery Instruction {{nomor_di}} untuk {{pelanggan}} siap diproses. Lokasi: {{lokasi}}','di','info',2,'/operational/delivery',0,30,NULL,'2025-09-10 03:47:33','2025-09-10 03:47:33',1,NULL,NULL),(4,'Low Stock Alert','Notify warehouse managers when inventory is low','inventory_low_stock',1,'{\"stock_level\": \"below_minimum\"}','manager,supervisor','warehouse,purchasing',NULL,NULL,0,'Stok Rendah: {{item_name}}','Item {{item_name}} memiliki stok di bawah minimum. Stok saat ini: {{current_stock}}, Minimum: {{minimum_stock}}','inventory','warning',3,'/warehouse/inventory',0,30,NULL,'2025-09-10 03:47:33','2025-09-10 03:47:33',1,NULL,NULL),(5,'Maintenance Due Alert','Notify service team when unit maintenance is due','maintenance_due',1,'{}',NULL,'service','DIESEL,ELECTRIC,LPG',NULL,0,'Maintenance Due: {{unit_no}}','Unit {{unit_no}} memerlukan maintenance {{maintenance_type}}. Due date: {{due_date}}','maintenance','warning',3,'/service/maintenance',0,30,NULL,'2025-09-10 03:47:33','2025-09-10 03:47:33',1,NULL,NULL),(6,'SPK DIESEL to Service DIESEL','Notifikasi untuk SPK departemen DIESEL ke divisi Service','spk_created',1,'{\"source_department\": \"diesel\", \"target_division\": \"service\"}',NULL,'service','diesel',NULL,0,'SPK Baru - {departemen} #{spk_id}','SPK baru telah dibuat untuk departemen {departemen}. Silakan review dan proses sesuai prosedur.',NULL,'info',2,'service/spk_service',0,30,1,'2025-09-10 03:58:43','2025-09-11 04:45:27',1,NULL,NULL),(7,'DI Processing Alert','Alert untuk DI yang perlu diproses','di_created',1,'{}',NULL,'service','',NULL,0,'DI Baru Perlu Diproses - #{di_id}','Delivery Instruction baru telah dibuat dan menunggu pemrosesan dari divisi yang bertanggung jawab.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:58:43','2025-09-10 03:58:43',1,NULL,NULL),(8,'Low Stock Alert','Alert untuk stok rendah','inventory_low_stock',1,'{}',NULL,'','',NULL,0,'Stok Rendah - {item_name}','Item {item_name} memiliki stok rendah ({current_stock} tersisa). Segera lakukan reorder.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:58:43','2025-09-10 03:58:43',1,NULL,NULL),(9,'Maintenance Due Alert','Alert untuk maintenance yang jatuh tempo','maintenance_due',1,'{}',NULL,'service','',NULL,0,'Maintenance Terjadwal - Unit {unit_code}','Unit {unit_code} memerlukan maintenance terjadwal. Silakan koordinasi dengan tim maintenance.',NULL,'info',2,NULL,0,30,1,'2025-09-10 03:58:43','2025-09-10 03:58:43',1,NULL,NULL),(10,'SPK DIESEL to Service DIESEL','Notifikasi untuk SPK departemen DIESEL ke divisi Service','spk_created',1,'{\"source_department\": \"diesel\", \"target_division\": \"service\"}',NULL,'service','diesel',NULL,0,'SPK Baru - {departemen} #{spk_id}','SPK baru telah dibuat untuk departemen {departemen}. Silakan review dan proses sesuai prosedur.',NULL,'info',2,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(11,'DI Processing Alert','Alert untuk DI yang perlu diproses','di_created',1,'{}',NULL,'service','',NULL,0,'DI Baru Perlu Diproses - #{di_id}','Delivery Instruction baru telah dibuat dan menunggu pemrosesan dari divisi yang bertanggung jawab.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(12,'Low Stock Alert','Alert untuk stok rendah','inventory_low_stock',1,'{}',NULL,'','',NULL,0,'Stok Rendah - {item_name}','Item {item_name} memiliki stok rendah ({current_stock} tersisa). Segera lakukan reorder.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(13,'Maintenance Due Alert','Alert untuk maintenance yang jatuh tempo','maintenance_due',1,'{}',NULL,'service','',NULL,0,'Maintenance Terjadwal - Unit {unit_code}','Unit {unit_code} memerlukan maintenance terjadwal. Silakan koordinasi dengan tim maintenance.',NULL,'info',2,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(14,'SPK Created - Service Notification','Notify service division when new SPK is created','SPK Created',1,'{}','superadmin','','','',1,'SPK Baru: {{nomor_spk}} - {{departemen}}','SPK baru telah dibuat untuk {{pelanggan}} dengan departemen {{departemen}}. Silakan periksa dan proses sesuai prosedur.','spk','info',2,'/service/spk/detail/{{id}}',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 21:56:11',1,NULL,NULL),(15,'SPK DIESEL - Service DIESEL Team','Notify DIESEL service team for DIESEL SPK','spk_created',1,'{\"departemen\": \"DIESEL\"}',NULL,'service','DIESEL',NULL,0,'SPK DIESEL: {{nomor_spk}} - {{pelanggan}}','SPK DIESEL baru memerlukan perhatian tim service DIESEL. Unit: {{unit_info}}, Lokasi: {{lokasi}}','spk','warning',3,'/service/spk/detail/{{id}}',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 06:37:37',1,NULL,NULL),(16,'DI Ready - Operational Team','Notify operational when DI is ready for processing','di_submitted',1,'{}',NULL,'operational',NULL,NULL,0,'DI Siap Diproses: {{nomor_di}}','Delivery Instruction {{nomor_di}} untuk {{pelanggan}} siap diproses. Lokasi: {{lokasi}}','di','info',2,'/operational/delivery',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 06:37:37',1,NULL,NULL),(18,'Maintenance Due Alert','Notify service team when unit maintenance is due','maintenance_due',1,'{}',NULL,'service','DIESEL,ELECTRIC,LPG',NULL,0,'Maintenance Due: {{unit_no}}','Unit {{unit_no}} memerlukan maintenance {{maintenance_type}}. Due date: {{due_date}}','maintenance','warning',3,'/service/maintenance',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 06:37:37',1,NULL,NULL),(19,'SPK Created - Superadmin Notification','Notify superadmin when new SPK is created for oversight','spk_created',1,NULL,'Super Administrator',NULL,NULL,NULL,0,'SPK Baru: {{nomor_spk}} - {{pelanggan}}','SPK {{nomor_spk}} telah dibuat untuk {{pelanggan}} dengan spesifikasi {{spesifikasi}}. Lokasi: {{lokasi}}. Status: Menunggu persiapan service.','spk','info',3,'service/spk_service',0,30,NULL,'2025-09-11 05:01:25','2025-09-11 05:01:25',1,NULL,NULL),(20,'Purchase Order Created - Multi-Target','Notify purchase division and specific warehouse user when PO is created','purchase_order_created',1,NULL,NULL,NULL,NULL,NULL,0,'PO Baru: {{po_number}} - {{vendor}}','Purchase Order {{po_number}} telah dibuat untuk vendor {{vendor}} dengan nilai {{amount}}. Silakan lakukan verifikasi dan proses selanjutnya.','purchase_order','info',2,'purchasing/po',0,30,NULL,'2025-09-11 06:20:23','2025-09-11 06:20:23',1,'{\"divisions\": [\"Purchase\", \"Warehouse\"], \"roles\": [\"manager\"], \"users\": [], \"departments\": []}',NULL);
/*!40000 ALTER TABLE `notification_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `target_role` varchar(100) DEFAULT NULL,
  `role` varchar(100) DEFAULT NULL,
  `division` varchar(100) DEFAULT NULL,
  `rule_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('info','success','warning','error','critical') DEFAULT 'info',
  `category` varchar(100) DEFAULT NULL COMMENT 'spk, di, inventory, maintenance, etc',
  `icon` varchar(50) DEFAULT NULL,
  `related_table` varchar(100) DEFAULT NULL COMMENT 'Table reference like spk, delivery_instruction',
  `related_id` int(11) DEFAULT NULL COMMENT 'Record ID reference',
  `url` varchar(500) DEFAULT NULL COMMENT 'Action URL for notification',
  `link` varchar(500) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `priority` tinyint(4) DEFAULT 1 COMMENT '1=low, 2=medium, 3=high, 4=critical',
  `expires_at` datetime DEFAULT NULL COMMENT 'Auto-delete after this date',
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `read_at` datetime DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_category` (`category`),
  KEY `idx_related` (`related_table`,`related_id`),
  KEY `idx_priority` (`priority`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_notifications_category_type` (`category`,`type`),
  KEY `idx_notifications_priority_created` (`priority`,`created_at`),
  KEY `idx_notifications_rule_id` (`rule_id`),
  KEY `idx_notifications_created_by` (`created_by`),
  KEY `idx_notifications_created_at` (`created_at`),
  KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,NULL,NULL,NULL,NULL,NULL,'Test Notification','This is a test notification to verify the system is working correctly.','info','system','fas fa-bell','test',1,'/dashboard',NULL,NULL,1,NULL,NULL,'2025-09-10 03:47:33',NULL,'2025-09-10 03:47:33'),(2,NULL,NULL,NULL,NULL,NULL,'Test Notification','This is a test notification to verify the system is working correctly.','info','system','fas fa-bell','test',1,'/dashboard',NULL,NULL,1,NULL,NULL,'2025-09-10 06:37:37',NULL,'2025-09-10 06:37:37'),(3,NULL,NULL,NULL,NULL,NULL,'Maintenance Unit FL-045','Engine overheat detected pada unit Forklift FL-045. Perlu segera diperiksa.','error','maintenance',NULL,NULL,NULL,NULL,NULL,NULL,3,NULL,1,'2025-09-10 06:38:28',NULL,'2025-09-10 06:38:28'),(4,NULL,NULL,NULL,NULL,NULL,'Schedule Maintenance Besok','5 unit memerlukan service rutin besok pagi.','warning','maintenance',NULL,NULL,NULL,NULL,NULL,NULL,2,NULL,1,'2025-09-10 06:38:28',NULL,'2025-09-10 06:38:28'),(5,NULL,NULL,NULL,NULL,NULL,'Invoice Overdue','Invoice INV-001234 dari PT Mandiri Logistik sudah overdue.','info','finance',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,1,'2025-09-10 06:38:28',NULL,'2025-09-10 06:38:28'),(6,NULL,NULL,NULL,NULL,NULL,'SPK Baru','SPK SPK/202509/004 diajukan oleh Marketing untuk diproses Service.','info',NULL,NULL,NULL,NULL,'http://localhost/optima1/public/service/spk_service',NULL,NULL,1,NULL,NULL,'2025-09-10 01:19:25',NULL,'2025-09-10 01:19:25'),(7,NULL,NULL,NULL,NULL,1,'Test SPK Notification','Test SPK TEST/001 telah dibuat untuk testing','info','spk',NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,1,'2025-09-10 21:37:19',NULL,'2025-09-11 02:37:19'),(8,NULL,NULL,NULL,NULL,NULL,'SPK Ready for Service','SPK/TEST/002 is ready for service team processing','info','spk',NULL,NULL,NULL,'service/spk_service',NULL,NULL,1,NULL,1,'2025-09-10 23:48:24',NULL,'2025-09-11 04:48:24'),(9,NULL,NULL,NULL,NULL,NULL,'SPK Baru Perlu Diproses','SPK SPK/202509/871 telah dibuat untuk PT Test Company Indonesia dengan spesifikasi Forklift Diesel 3 Ton untuk keperluan warehouse. Silakan periksa dan proses sesuai prosedur.','info','spk',NULL,NULL,NULL,'service/spk_service',NULL,NULL,1,NULL,1,'2025-09-10 23:54:39',NULL,'2025-09-11 04:54:39'),(11,NULL,NULL,NULL,NULL,NULL,'SPK Baru: SPK/202509/673 - PT Test Superadmin Notification','SPK baru telah dibuat untuk PT Test Superadmin Notification dengan spesifikasi Forklift Elektrik 2.5 Ton untuk warehouse automation. Silakan periksa dan proses sesuai prosedur.','info','spk',NULL,NULL,NULL,'service/spk_service',NULL,NULL,1,NULL,1,'2025-09-11 00:02:21',NULL,'2025-09-11 05:02:21'),(12,NULL,NULL,NULL,NULL,NULL,'SPK Baru: SPK/202509/673 - PT Test Superadmin Notification','SPK SPK/202509/673 telah dibuat untuk PT Test Superadmin Notification dengan spesifikasi Forklift Elektrik 2.5 Ton untuk warehouse automation. Lokasi: Jakarta Selatan. Status: Menunggu persiapan service.','info','spk',NULL,NULL,NULL,'service/spk_service',NULL,NULL,1,NULL,1,'2025-09-11 00:02:21',NULL,'2025-09-11 05:02:21'),(13,NULL,NULL,NULL,NULL,NULL,'SPK Baru: SPK/202509/905 - PT Test Superadmin Notification','SPK baru telah dibuat untuk PT Test Superadmin Notification dengan spesifikasi Forklift Elektrik 2.5 Ton untuk warehouse automation. Silakan periksa dan proses sesuai prosedur.','info','spk',NULL,NULL,NULL,'service/spk_service',NULL,NULL,1,NULL,1,'2025-09-11 01:23:26',NULL,'2025-09-11 06:23:26'),(14,NULL,NULL,NULL,NULL,NULL,'SPK Baru: SPK/202509/905 - PT Test Superadmin Notification','SPK SPK/202509/905 telah dibuat untuk PT Test Superadmin Notification dengan spesifikasi Forklift Elektrik 2.5 Ton untuk warehouse automation. Lokasi: Jakarta Selatan. Status: Menunggu persiapan service.','info','spk',NULL,NULL,NULL,'service/spk_service',NULL,NULL,1,NULL,1,'2025-09-11 01:23:26',NULL,'2025-09-11 06:23:26');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `optimization_additional_log`
--

DROP TABLE IF EXISTS `optimization_additional_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `optimization_additional_log` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `optimization_additional_log`
--

LOCK TABLES `optimization_additional_log` WRITE;
/*!40000 ALTER TABLE `optimization_additional_log` DISABLE KEYS */;
INSERT INTO `optimization_additional_log` VALUES (1,'FK_CONSTRAINT','po_sparepart_items','fk_po_sparepart_items_purchase_orders','po_sparepart_items.po_id -> purchase_orders.id_po','SUCCESS',NULL,'2025-09-03 09:32:02'),(2,'FK_CONSTRAINT','po_units','fk_po_units_purchase_orders','po_units.po_id -> purchase_orders.id_po','SUCCESS',NULL,'2025-09-03 09:32:02'),(3,'FK_CONSTRAINT','purchase_orders','fk_purchase_orders_suppliers','purchase_orders.supplier_id -> suppliers.id_supplier','SUCCESS',NULL,'2025-09-03 09:32:02'),(4,'INDEX',NULL,NULL,'Created additional performance indexes','SUCCESS',NULL,'2025-09-03 09:32:02');
/*!40000 ALTER TABLE `optimization_additional_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `optimization_log`
--

DROP TABLE IF EXISTS `optimization_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `optimization_log` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `optimization_log`
--

LOCK TABLES `optimization_log` WRITE;
/*!40000 ALTER TABLE `optimization_log` DISABLE KEYS */;
INSERT INTO `optimization_log` VALUES (1,'FK_CONSTRAINT','delivery_items','fk_delivery_items_delivery_instructions','delivery_items.di_id -> delivery_instructions.id','SUCCESS',NULL,'2025-09-03 09:30:02'),(2,'FK_CONSTRAINT','po_items','fk_po_items_purchase_orders','po_items.po_id -> purchase_orders.id_po','SUCCESS',NULL,'2025-09-03 09:30:02');
/*!40000 ALTER TABLE `optimization_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `permissions` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Access Administration','admin.access','Access to administration module','admin','access',1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(2,'User Management','admin.user_management','Manage users and their details','admin','management',1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(3,'Role Management','admin.role_management','Manage roles and role assignments','admin','management',1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(4,'Permission Management','admin.permission_management','Manage permissions and access control','admin','management',1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(5,'System Settings','admin.system_settings','Configure system settings','admin','configuration',1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(6,'Configuration','admin.configuration','Access system configuration','admin','configuration',1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(7,'Access Service','service.access','Access to service division','service','access',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(8,'View Work Orders','service.work_orders.view','View work orders','service','work_orders',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(9,'Create Work Orders','service.work_orders.create','Create new work orders','service','work_orders',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(10,'Edit Work Orders','service.work_orders.edit','Edit existing work orders','service','work_orders',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(11,'Delete Work Orders','service.work_orders.delete','Delete work orders','service','work_orders',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(12,'View PMPS','service.pmps.view','View preventive maintenance schedules','service','maintenance',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(13,'Manage PMPS','service.pmps.manage','Manage preventive maintenance schedules','service','maintenance',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(14,'View Inventory','service.inventory.view','View service inventory','service','inventory',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(15,'Manage Inventory','service.inventory.manage','Manage service inventory','service','inventory',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(16,'View Unit Inventory','service.unit_inventory.view','View unit inventory','service','inventory',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(17,'View PDI','service.pdi.view','View PDI (Pre-Delivery Inspection)','service','inspection',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(18,'Manage PDI','service.pdi.manage','Manage PDI (Pre-Delivery Inspection)','service','inspection',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(19,'View Data Unit','service.data_unit.view','View unit data','service','data',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(20,'Manage Data Unit','service.data_unit.manage','Manage unit data','service','data',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(21,'Access Unit Rolling','unit_rolling.access','Access to unit operational division','unit_rolling','access',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(22,'View Delivery Instructions','unit_rolling.delivery_instructions.view','View delivery instructions','unit_rolling','delivery',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(23,'Manage Delivery Instructions','unit_rolling.delivery_instructions.manage','Manage delivery instructions','unit_rolling','delivery',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(24,'View Delivery Unit','unit_rolling.delivery_unit.view','View delivery units','unit_rolling','delivery',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(25,'Manage Delivery Unit','unit_rolling.delivery_unit.manage','Manage delivery units','unit_rolling','delivery',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(26,'View History','unit_rolling.history.view','View operational history','unit_rolling','history',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(27,'Access Marketing','marketing.access','Access to marketing division','marketing','access',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(28,'Create Penawaran','marketing.penawaran.create','Create quotations','marketing','sales',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(29,'View Penawaran','marketing.penawaran.view','View quotations','marketing','sales',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(30,'Manage Kontrak','marketing.kontrak.manage','Manage contracts and PO','marketing','contracts',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(31,'View List Unit','marketing.list_unit.view','View unit listings','marketing','inventory',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(32,'Manage List Unit','marketing.list_unit.manage','Manage unit listings','marketing','inventory',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(33,'Access Warehouse','warehouse.access','Access to warehouse division','warehouse','access',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(34,'Manage Assets','warehouse.assets.manage','Manage unit assets','warehouse','assets',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(35,'View Inventory','warehouse.inventory.view','View warehouse inventory','warehouse','inventory',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(36,'Manage Inventory','warehouse.inventory.manage','Manage warehouse inventory','warehouse','inventory',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(37,'Verify PO','warehouse.po.verify','Verify purchase orders','warehouse','purchasing',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(38,'Access Purchasing','purchasing.access','Access to purchasing division','purchasing','access',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(39,'Manage Purchasing','purchasing.manage','Manage purchase orders and procurement','purchasing','procurement',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(40,'Create PO','purchasing.po.create','Create purchase orders','purchasing','procurement',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(41,'Approve PO','purchasing.po.approve','Approve purchase orders','purchasing','procurement',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(42,'Access Perizinan','perizinan.access','Access to licensing division','perizinan','access',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(43,'Manage Perizinan','perizinan.manage','Manage permits and licenses','perizinan','licensing',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(44,'Create SIO','perizinan.sio.create','Create operator licenses','perizinan','licensing',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(45,'Create SILO','perizinan.silo.create','Create operational worthiness certificates','perizinan','licensing',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(46,'Access Accounting','accounting.access','Access to accounting division','accounting','access',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(47,'View Finance','finance.view','View financial data','accounting','finance',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(48,'Manage Finance','finance.manage','Manage financial data','accounting','finance',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(49,'View Invoices','invoices.view','View invoices','accounting','invoicing',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(50,'Manage Invoices','invoices.manage','Manage invoices','accounting','invoicing',0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(52,'View SPK','service.spk.view','','service','access',0,1,'2025-08-07 03:52:37','2025-08-07 06:41:57'),(53,'SPK Edit','marketing.spk.manage','','marketing','general',0,1,'2025-08-07 06:52:21','2025-08-07 06:52:21'),(54,'Marketing DI Manage','marketing.di.manage','','marketing','general',0,1,'2025-08-07 06:52:51','2025-08-07 06:52:51'),(55,'Service Spk Manage','service.spk.manage','','service','general',0,1,'2025-08-07 07:26:34','2025-08-07 07:26:34');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_items`
--

DROP TABLE IF EXISTS `po_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `po_items` (
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
  KEY `idx_po_items_created_at` (`created_at`),
  CONSTRAINT `fk_po_items_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_items`
--

LOCK TABLES `po_items` WRITE;
/*!40000 ALTER TABLE `po_items` DISABLE KEYS */;
INSERT INTO `po_items` VALUES (2,27,'Battery',NULL,2,3,'','','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:15'),(3,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:44'),(4,27,'Battery',NULL,2,3,'','','','','','2025-07-22 00:29:20','2025-07-23 21:33:17'),(5,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:52'),(6,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:57'),(7,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:03'),(8,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:11'),(9,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:16'),(10,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:21'),(11,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:26'),(22,36,'Attachment',3,NULL,NULL,'11123','','','Sesuai','','2025-07-23 21:08:34','2025-07-23 23:29:16'),(23,37,'Attachment',5,NULL,NULL,'','','','Sesuai','','2025-07-23 23:32:28','2025-07-23 23:32:57'),(24,37,'Attachment',5,NULL,NULL,'ok',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-07-26 01:30:35'),(25,37,'Attachment',5,NULL,NULL,'ok',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-07-26 01:37:44'),(26,37,'Attachment',5,NULL,NULL,'test4',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-08-11 21:54:35'),(27,37,'Attachment',5,NULL,NULL,'wae',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-08-11 23:44:51'),(73,89,'Attachment',2,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-07-29 19:35:23','2025-08-21 20:57:48'),(74,92,'Attachment',2,NULL,NULL,'12333445',NULL,'','Sesuai',NULL,'2025-07-29 19:49:08','2025-08-21 21:04:40'),(75,92,'Battery',2,4,4,'123','123','','Sesuai',NULL,'2025-07-29 19:49:08','2025-08-21 21:32:36'),(76,95,'Battery',NULL,14,15,'1','1','','Sesuai',NULL,'2025-07-31 21:22:10','2025-08-21 21:35:36'),(77,95,'Attachment',13,14,15,'123',NULL,'','Sesuai',NULL,'2025-07-31 21:22:10','2025-08-21 21:36:00'),(78,118,'Attachment',1,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-08-11 18:59:05','2025-08-21 21:36:39'),(79,124,'Attachment',4,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-08-11 20:15:49','2025-08-22 02:18:28'),(80,130,'Attachment',3,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-08-11 20:27:06','2025-08-22 02:19:00'),(81,131,'Attachment',3,NULL,NULL,'ok',NULL,'','Sesuai',NULL,'2025-08-11 20:27:13','2025-08-26 21:50:06'),(82,132,'Battery',NULL,14,14,'123','123','','Sesuai',NULL,'2025-08-11 20:27:48','2025-08-22 02:18:41'),(83,139,'Attachment',3,NULL,NULL,'a',NULL,'','Sesuai',NULL,'2025-08-11 21:06:47','2025-08-28 02:32:49'),(84,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-22 02:23:14'),(85,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:34'),(86,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:43'),(87,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:51'),(88,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:58'),(89,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(90,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(91,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(92,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(93,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(94,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(95,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(96,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(97,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(98,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08');
/*!40000 ALTER TABLE `po_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_sparepart_items`
--

DROP TABLE IF EXISTS `po_sparepart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `po_sparepart_items` (
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
  KEY `idx_po_sparepart_items_po_id` (`po_id`),
  CONSTRAINT `fk_po_sparepart_items_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_sparepart_items`
--

LOCK TABLES `po_sparepart_items` WRITE;
/*!40000 ALTER TABLE `po_sparepart_items` DISABLE KEYS */;
INSERT INTO `po_sparepart_items` VALUES (14,35,5,1,'Pieces','','Sesuai',''),(15,35,23,1,'Pieces','','Tidak Sesuai',''),(16,35,33,1,'Pieces','','Sesuai',''),(17,40,3,1,'Pieces','','Sesuai',NULL),(18,40,4,1,'Pieces','','Sesuai',NULL),(19,40,28,1,'Pieces','','Sesuai',NULL),(20,40,47,1,'Pieces','','Sesuai',NULL),(35,85,1,1,'Pieces','','Sesuai',''),(36,85,3,1,'Kaleng','','Belum Dicek',NULL),(37,88,3,1,'Pieces','','Belum Dicek',NULL),(38,96,3,1,'Pieces','','Belum Dicek',NULL),(39,119,1,1,'Pieces','','Belum Dicek',NULL),(40,125,3,1,'Pieces','','Belum Dicek',NULL),(48,137,3,1,'Pieces','','Belum Dicek',NULL),(49,140,30,1,'Pieces','','Belum Dicek',NULL),(50,141,4,1,'Pieces','','Belum Dicek',NULL);
/*!40000 ALTER TABLE `po_sparepart_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_units`
--

DROP TABLE IF EXISTS `po_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `po_units` (
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
  KEY `fk_po_units_purchase_orders` (`po_id`),
  CONSTRAINT `fk_po_units_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_units`
--

LOCK TABLES `po_units` WRITE;
/*!40000 ALTER TABLE `po_units` DISABLE KEYS */;
INSERT INTO `po_units` VALUES (1,NULL,'2025-07-21 01:31:15',2,1,'Sesuai',NULL,3,3,'123123',2019,11,6,'123',1,'1',12,'123',123,'123',123,'13',5,4,3,NULL,NULL),(2,NULL,'2025-07-21 01:31:17',1,2,'Sesuai',NULL,1,4,'12331233',2025,14,2,'123',1,'1',1,'1',123,'123',123,'123',6,3,3,NULL,NULL),(42,'2025-07-20 17:00:00','2025-07-21 01:32:16',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(43,'2025-07-20 17:00:00','2025-07-21 01:32:17',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(44,'2025-07-20 17:00:00','2025-07-30 20:31:48',23,1,'Sesuai',1,1,1,'123',2025,2,1,'123',1,'123',1,'512512312312512315123',1,'123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(45,'2025-07-20 17:00:00','2025-07-21 01:32:19',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(46,'2025-07-20 17:00:00','2025-07-21 01:32:20',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(47,'2025-07-20 17:00:00','2025-07-21 01:32:35',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(48,'2025-07-20 17:00:00','2025-07-21 01:32:36',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(49,'2025-07-20 17:00:00','2025-07-21 01:32:37',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(50,'2025-07-20 17:00:00','2025-07-21 01:32:38',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(51,'2025-07-20 17:00:00','2025-07-21 01:32:39',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(52,'2025-07-20 17:00:00','2025-07-21 01:32:50',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(53,'2025-07-20 17:00:00','2025-07-21 01:32:51',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(54,'2025-07-20 17:00:00','2025-07-21 01:32:52',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(55,'2025-07-20 19:56:41','2025-07-21 01:33:04',24,2,'Sesuai',60,66,4,'TEST12341234',2025,24,3,'1112314515231233',4,'1523512312451232112',3,'5125123123125123151232',4,'11112455512314123',4,'asdafasdadasdsafasd',3,2,3,'Baru','Hadir'),(56,'2025-07-20 19:56:41','2025-07-21 01:33:05',24,2,'Sesuai',60,66,4,'TEST12341234',2025,24,3,'1112314515231233',4,'1523512312451232112',3,'5125123123125123151232',4,'11112455512314123',4,'asdafasdadasdsafasd',3,2,3,'Baru','Hadir'),(57,'2025-07-20 19:56:41','2025-07-21 01:33:06',24,2,'Sesuai',60,66,4,'TEST12341234',2025,24,3,'1112314515231233',4,'1523512312451232112',3,'5125123123125123151232',4,'11112455512314123',4,'asdafasdadasdsafasd',3,2,3,'Baru','Hadir'),(58,'2025-07-21 07:14:31','2025-07-25 19:49:17',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(59,'2025-07-21 07:14:31','2025-07-24 18:25:37',25,1,'Sesuai',4,9,12,'123',2025,47,2,NULL,1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(60,'2025-07-21 07:14:31','2025-07-24 19:40:51',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(61,'2025-07-21 07:14:31','2025-07-24 20:32:34',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(62,'2025-07-21 07:14:31','2025-07-25 20:14:26',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(63,'2025-07-21 07:14:31','2025-07-25 19:10:11',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(64,'2025-07-21 07:14:31','2025-07-25 21:00:44',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(65,'2025-07-21 07:14:31','2025-07-25 23:29:31',25,1,'Sesuai',4,9,12,'123a',2025,47,2,'123a',1,'123a',1,'',1,'123a',2,'',1,1,3,'Baru',''),(66,'2025-07-21 07:14:31','2025-07-25 19:36:11',25,1,'Sesuai',4,9,12,'132',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(67,'2025-07-21 07:14:31','2025-08-03 20:06:17',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(68,'2025-07-23 23:58:46','2025-08-03 20:07:12',38,3,'Sesuai',4,6,12,'123',2025,6,2,'123',2,'123',2,NULL,2,'123',6,NULL,1,2,3,'Baru',''),(83,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(84,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(85,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(86,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(87,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(89,'2025-07-25 23:53:49','2025-07-25 23:54:27',46,2,'Sesuai',499,501,13,'PO/SPRT/9923100',2025,37,4,'PO/SPRT/9923100',3,'PO/SPRT/9923100',NULL,NULL,16,'PO/SPRT/9923100',NULL,NULL,4,2,3,'Baru',NULL),(94,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(95,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(96,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(97,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(98,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(99,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(100,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(101,'2025-07-29 00:28:01','2025-07-29 00:28:01',58,2,'Belum Dicek',4,NULL,NULL,NULL,2025,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Baru',NULL),(102,'2025-07-29 01:44:12','2025-07-29 01:44:12',65,2,'Belum Dicek',2,3,3,NULL,2025,4,4,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,4,3,'Baru',''),(103,'2025-07-29 01:45:56','2025-07-29 01:45:56',66,2,'Belum Dicek',38,38,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,4,3,'Baru',''),(104,'2025-07-29 01:45:56','2025-07-29 01:45:56',66,2,'Belum Dicek',4,7,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,4,3,'Baru',''),(105,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(106,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(107,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(108,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(109,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(110,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(111,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(112,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(113,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(114,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(115,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(116,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(117,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(118,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(119,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(120,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(121,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(122,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(123,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(124,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(125,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(126,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(127,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(128,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(213,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(214,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(215,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(216,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(217,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(218,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(219,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(220,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(221,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(222,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(223,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(224,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(225,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(226,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(227,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(228,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(229,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(230,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(231,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(232,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(233,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(234,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(235,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(236,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(237,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(238,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(239,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(240,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(241,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(242,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(243,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(244,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(245,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(246,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(247,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(248,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(249,'2025-07-31 21:22:10','2025-07-31 21:22:10',94,2,'Belum Dicek',4,5,1,NULL,2025,2,5,NULL,5,NULL,NULL,NULL,5,NULL,NULL,NULL,5,2,3,'Baru',''),(250,'2025-07-31 21:29:00','2025-07-31 21:29:00',97,2,'Belum Dicek',4,5,1,NULL,2025,2,5,NULL,4,NULL,NULL,NULL,5,NULL,NULL,NULL,5,3,3,'Bekas',''),(251,'2025-08-11 02:37:42','2025-08-11 02:37:42',102,1,'Belum Dicek',60,64,1,NULL,2025,2,5,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,2,3,'Rekondisi',NULL),(252,'2025-08-11 18:56:29','2025-08-11 18:56:29',116,1,'Belum Dicek',4,4,4,NULL,2025,5,4,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,3,3,'Baru',''),(253,'2025-08-11 18:59:05','2025-08-11 18:59:05',117,1,'Belum Dicek',4,5,4,NULL,2025,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Baru',''),(254,'2025-08-11 19:00:10','2025-08-11 19:00:10',120,1,'Belum Dicek',2,2,2,NULL,2025,1,2,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,4,3,'Baru',NULL),(255,'2025-08-11 19:21:21','2025-08-11 19:21:21',121,1,'Belum Dicek',39,43,2,NULL,2025,5,1,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,'Baru',NULL),(256,'2025-08-11 19:22:14','2025-08-11 19:22:14',122,2,'Belum Dicek',4,6,9,NULL,2025,2,5,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,4,4,'Baru',''),(257,'2025-08-11 20:15:49','2025-08-11 20:15:49',123,2,'Belum Dicek',2,3,10,NULL,2025,10,2,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,2,3,'Baru',''),(259,'2025-08-11 21:06:47','2025-08-11 21:06:47',138,1,'Belum Dicek',2,2,6,NULL,2025,5,5,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,2,4,'Baru',''),(260,'2025-08-11 23:45:20','2025-08-11 23:45:20',142,1,'Belum Dicek',2,2,4,NULL,2025,5,5,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,2,3,'Baru',NULL);
/*!40000 ALTER TABLE `po_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `purchase_orders` (
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
  KEY `idx_purchase_orders_invoice_no` (`invoice_no`),
  CONSTRAINT `fk_purchase_orders_suppliers` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id_supplier`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,'PO-Unit-2025-07-0001','2025-07-15',1,NULL,NULL,NULL,NULL,'Unit','2025-07-16 03:44:13','2025-07-21 01:31:17','completed'),(2,'PO/ATT/2024/002','2025-07-16',2,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-07-16 03:44:13','2025-07-21 01:31:15','completed'),(23,'PO-Unit-2025-07-0004','2025-07-21',2,'12441234123123123','2025-07-21','2025-07-25','124123123','Unit','2025-07-20 18:33:37','2025-07-30 20:31:48','completed'),(24,'PO-Unit-2025-07-0005','2025-07-21',2,'1244123412312312312','2025-07-31','2025-07-31','asfffasdasdasd123','Unit','2025-07-20 19:56:41','2025-07-21 01:33:06','completed'),(25,'PO-Unit-2025-07-0006','2025-07-21',1,'PO/ASDA/ASU','2025-07-21','2025-07-21','BELI UNIT','Unit','2025-07-21 07:14:31','2025-08-03 20:06:17','completed'),(27,'PO/221/111111','2025-07-22',2,'','2025-07-22','2025-07-22','BELI ','Attachment & Battery','2025-07-22 00:29:20','2025-07-22 00:29:20','pending'),(35,'PO/SPRT/144445','2025-07-24',1,NULL,NULL,NULL,'','Sparepart','2025-07-23 20:16:40','2025-07-23 20:16:58','Selesai dengan Catatan'),(36,'PO/2000/99231','2025-07-24',1,'','2025-07-24','2025-07-24','','Attachment & Battery','2025-07-23 21:08:34','2025-07-23 23:29:16','completed'),(37,'PO/ATT/4321/211/1','2025-07-24',2,'','2025-07-25','2025-07-25','','Attachment & Battery','2025-07-23 23:32:28','2025-07-23 23:32:28','pending'),(38,'PO/278/7875','2025-07-24',1,'','2025-07-24','2025-07-24','','Unit','2025-07-23 23:58:46','2025-08-15 21:26:44','completed'),(39,'PO/221/131231','2025-07-24',1,'','2025-07-24','2025-07-24','','Unit','2025-07-24 01:33:02','2025-07-24 01:33:02','pending'),(40,'PO/221/122222','2025-07-25',2,NULL,NULL,NULL,'','Sparepart','2025-07-24 22:13:51','2025-07-24 22:15:54','completed'),(44,'PO/221/131232','2025-07-26',1,'','2025-07-24','2025-07-24','','Unit','2025-07-25 18:44:44','2025-07-25 18:44:44','pending'),(46,'PO/SPRT/9923100','2025-07-26',1,NULL,'2025-07-26','2025-07-26',NULL,'Unit','2025-07-25 23:53:49','2025-07-25 23:54:27','completed'),(47,'PO/SPRT/ADIT','2025-07-28',2,'123','2025-07-26','2025-07-26','123','Unit','2025-07-28 00:28:18','2025-07-28 00:28:18','pending'),(52,'PO/SPRT/ADIT123','2025-07-29',1,'123','2025-07-26','2025-07-26','123','Unit','2025-07-29 00:03:39','2025-07-29 00:03:39','pending'),(58,'PO/221/KALENG125551','2025-07-29',1,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 00:28:01','2025-07-29 00:28:01','pending'),(65,'PO/221/KALENG1255512','2025-07-29',1,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 01:44:12','2025-07-29 01:44:12','pending'),(66,'PO/221/asda321','2025-07-29',2,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 01:45:56','2025-07-29 01:45:56','pending'),(67,'PO/221/asda321213','2025-07-29',1,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 01:59:17','2025-07-29 01:59:17','pending'),(84,'PO/221/23155','2025-07-30',2,NULL,NULL,NULL,NULL,'Unit','2025-07-29 19:23:48','2025-07-29 19:23:48','pending'),(85,'PO/221/231551','2025-07-30',1,NULL,NULL,NULL,NULL,'Sparepart','2025-07-29 19:24:12','2025-07-29 19:24:12','pending'),(87,'PO/221/231553','2025-07-30',1,NULL,NULL,NULL,NULL,'Unit','2025-07-29 19:25:29','2025-07-29 19:25:29','pending'),(88,'PO/221/231553','2025-07-30',1,NULL,NULL,NULL,NULL,'Sparepart','2025-07-29 19:25:29','2025-07-29 19:25:29','pending'),(89,'PO/221/KALENG12355','2025-07-30',2,'','2025-07-30','2025-07-30','','Attachment & Battery','2025-07-29 19:35:23','2025-07-29 19:35:23','pending'),(92,'PO/221/2315532121','2025-07-30',1,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-07-29 19:49:08','2025-07-29 19:49:08','pending'),(93,'PO/221/2315531','2025-07-30',1,NULL,NULL,NULL,NULL,'Unit','2025-07-29 20:16:28','2025-07-29 20:16:28','pending'),(94,'PO/221/2315512355','2025-08-01',1,NULL,NULL,NULL,NULL,'Unit','2025-07-31 21:22:10','2025-07-31 21:22:10','pending'),(95,'PO/221/2315512355','2025-08-01',1,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-07-31 21:22:10','2025-07-31 21:22:10','pending'),(96,'PO/221/2315512355','2025-08-01',1,NULL,NULL,NULL,NULL,'Sparepart','2025-07-31 21:22:10','2025-07-31 21:22:10','pending'),(97,'PO/221/23155123551','2025-08-01',2,NULL,NULL,NULL,NULL,'Unit','2025-07-31 21:29:00','2025-07-31 21:29:00','pending'),(102,'tester','2025-08-11',1,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 02:37:42','2025-08-11 02:37:42','pending'),(116,'PO/221/KALENG/poas23','2025-08-12',2,NULL,NULL,NULL,NULL,'Unit','2025-08-11 18:56:29','2025-08-11 18:56:29','pending'),(117,'TESTETEST','2025-08-12',2,NULL,NULL,NULL,NULL,'Unit','2025-08-11 18:59:05','2025-08-11 18:59:05','pending'),(118,'TESTETEST','2025-08-12',2,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 18:59:05','2025-08-11 18:59:05','pending'),(119,'TESTETEST','2025-08-12',2,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 18:59:05','2025-08-11 18:59:05','pending'),(120,'tester324234','2025-08-12',2,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 19:00:10','2025-08-11 19:00:10','pending'),(121,'initest','2025-08-12',1,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 19:21:21','2025-08-11 19:21:21','pending'),(122,'iniTESTETEST','2025-08-12',2,NULL,NULL,NULL,NULL,'Unit','2025-08-11 19:22:14','2025-08-11 19:22:14','pending'),(123,'iniTESTETESTetteq','2025-08-12',1,NULL,NULL,NULL,NULL,'Unit','2025-08-11 20:15:49','2025-08-11 20:15:49','pending'),(124,'iniTESTETESTetteq','2025-08-12',1,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:15:49','2025-08-11 20:15:49','pending'),(125,'iniTESTETESTetteq','2025-08-12',1,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 20:15:49','2025-08-11 20:15:49','pending'),(130,'akucumatest','2025-08-12',1,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:27:06','2025-08-11 20:27:06','pending'),(131,'akucumatest','2025-08-12',1,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:27:13','2025-08-11 20:27:13','pending'),(132,'akucumatest1','2025-08-12',2,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:27:48','2025-08-11 20:27:48','pending'),(137,'akucumatest3','2025-08-12',1,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 20:33:56','2025-08-11 20:33:56','pending'),(138,'awdiniwane','2025-08-12',1,NULL,NULL,NULL,NULL,'Unit','2025-08-11 21:06:47','2025-08-11 21:06:47','pending'),(139,'awdiniwane','2025-08-12',1,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 21:06:47','2025-08-11 21:06:47','pending'),(140,'awdiniwane','2025-08-12',1,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 21:06:47','2025-08-11 21:06:47','pending'),(141,'awdiniwane','2025-08-12',2,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 21:11:47','2025-08-11 21:11:47','pending'),(142,'initest123','2025-08-12',1,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 23:45:20','2025-08-11 23:45:20','pending'),(143,'PO/221/KALENG/poas231','2025-08-22',1,'PO/ASDA/ADIT','2025-08-22','2025-08-22','','Attachment & Battery','2025-08-22 02:21:14','2025-08-22 02:21:14','pending'),(144,'PO-Unit-2025-07-0001','2025-07-16',1,NULL,NULL,NULL,NULL,'Unit','2025-08-27 19:36:34',NULL,'pending'),(145,'PO-Unit-2025-07-0002','2025-07-15',2,NULL,NULL,NULL,NULL,'Unit','2025-08-27 19:36:34',NULL,'approved'),(146,'PO-Unit-2025-07-0003','2025-07-14',3,NULL,NULL,NULL,NULL,'Unit','2025-08-27 19:36:34',NULL,'completed'),(147,'BATERAI','2025-08-28',1,'123123','2025-08-28','2025-08-28','1231','Attachment & Battery','2025-08-28 02:35:08','2025-08-28 02:35:08','pending');
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rbac_audit_log`
--

DROP TABLE IF EXISTS `rbac_audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rbac_audit_log` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rbac_audit_log`
--

LOCK TABLES `rbac_audit_log` WRITE;
/*!40000 ALTER TABLE `rbac_audit_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `rbac_audit_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rentals`
--

DROP TABLE IF EXISTS `rentals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `rentals` (
  `rental_id` int(10) unsigned NOT NULL,
  `rental_number` varchar(50) NOT NULL,
  `forklift_id` int(10) unsigned NOT NULL,
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
  `created_by` int(10) unsigned DEFAULT NULL,
  `updated_by` int(10) unsigned DEFAULT NULL,
  `approved_by` int(10) unsigned DEFAULT NULL,
  `cancelled_by` int(10) unsigned DEFAULT NULL,
  `completed_by` int(10) unsigned DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `confirmed_by` int(10) unsigned DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`rental_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `rentals`
--

LOCK TABLES `rentals` WRITE;
/*!40000 ALTER TABLE `rentals` DISABLE KEYS */;
/*!40000 ALTER TABLE `rentals` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `reports`
--

DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(50) NOT NULL,
  `format` varchar(20) NOT NULL,
  `filename` varchar(255) DEFAULT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `status` enum('pending','processing','completed','failed') NOT NULL DEFAULT 'pending',
  `data_count` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reports`
--

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_permissions`
--

DROP TABLE IF EXISTS `role_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `role_permissions` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (1,1,1,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(2,1,2,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(3,1,3,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(4,1,4,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(5,1,5,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(6,1,6,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(7,1,7,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(8,1,8,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(9,1,9,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(10,1,10,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(11,1,11,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(12,1,12,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(13,1,13,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(14,1,14,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(15,1,15,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(16,1,16,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(17,1,17,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(18,1,18,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(19,1,19,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(20,1,20,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(21,1,21,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(22,1,22,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(23,1,23,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(24,1,24,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(25,1,25,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(26,1,26,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(27,1,27,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(28,1,28,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(29,1,29,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(30,1,30,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(31,1,31,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(32,1,32,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(33,1,33,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(34,1,34,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(35,1,35,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(36,1,36,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(37,1,37,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(38,1,38,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(39,1,39,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(40,1,40,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(41,1,41,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(42,1,42,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(43,1,43,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(44,1,44,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(45,1,45,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(46,1,46,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(47,1,47,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(48,1,48,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(49,1,49,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57'),(50,1,50,1,NULL,'2025-08-05 07:01:57','2025-08-05 07:01:57','2025-08-05 07:01:57');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Administrator','super_admin','Full system access with all permissions',1,1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(2,'System Administrator','system_admin','System administration and configuration',1,1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(3,'Division Manager','division_manager','Manager role for division operations',NULL,1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(4,'Division Staff','division_staff','Staff role for division operations',NULL,1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(5,'Service Manager','service_manager','Service Division Manager',2,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(6,'Service Technician','service_technician','Service Division Technician',2,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(7,'Operations Manager','operations_manager','Unit Operations Manager',3,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(8,'Driver','driver','Unit Operations Driver',3,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(9,'Marketing Manager','marketing_manager','Marketing Division Manager',4,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(10,'Sales Representative','sales_rep','Marketing Sales Representative',4,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(11,'Warehouse Manager','warehouse_manager','Warehouse & Assets Manager',5,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(12,'Warehouse Staff','warehouse_staff','Warehouse & Assets Staff',5,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(13,'Purchasing Manager','purchasing_manager','Purchasing Division Manager',6,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(14,'Purchasing Staff','purchasing_staff','Purchasing Division Staff',6,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(15,'Perizinan Manager','perizinan_manager','Licensing Division Manager',7,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(16,'Perizinan Staff','perizinan_staff','Licensing Division Staff',7,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(17,'Accounting Manager','accounting_manager','Accounting Division Manager',8,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(18,'Accountant','accountant','Accounting Division Staff',8,0,1,'2025-08-05 07:01:57','2025-08-05 07:01:57');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sparepart`
--

DROP TABLE IF EXISTS `sparepart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sparepart` (
  `id_sparepart` int(11) NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) NOT NULL,
  `desc_sparepart` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_sparepart`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sparepart`
--

LOCK TABLES `sparepart` WRITE;
/*!40000 ALTER TABLE `sparepart` DISABLE KEYS */;
/*!40000 ALTER TABLE `sparepart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk`
--

DROP TABLE IF EXISTS `spk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spk` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nomor_spk` varchar(100) NOT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT') NOT NULL DEFAULT 'UNIT',
  `kontrak_id` int(10) unsigned DEFAULT NULL,
  `kontrak_spesifikasi_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
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
  KEY `fk_spk_user` (`dibuat_oleh`),
  CONSTRAINT `fk_spk_jenis_perintah` FOREIGN KEY (`jenis_perintah_kerja_id`) REFERENCES `jenis_perintah_kerja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_spk_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_spk_kontrak_spesifikasi` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_spk_status_eksekusi` FOREIGN KEY (`status_eksekusi_workflow_id`) REFERENCES `status_eksekusi_workflow` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_spk_tujuan_perintah` FOREIGN KEY (`tujuan_perintah_kerja_id`) REFERENCES `tujuan_perintah_kerja` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_spk_user` FOREIGN KEY (`dibuat_oleh`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk`
--

LOCK TABLES `spk` WRITE;
/*!40000 ALTER TABLE `spk` DISABLE KEYS */;
INSERT INTO `spk` VALUES (27,'SPK/202509/001','UNIT',NULL,NULL,2,'test12345','MONORKOBO','JAJA','09324987729','BEKASI','2025-09-03','{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"PALLET STACKER\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"14\",\"attachment_tipe\":\"PAPER ROLL CLAMP\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"9\",\"mast_id\":\"22\",\"ban_id\":\"6\",\"roda_id\":\"3\",\"valve_id\":\"3\",\"aksesoris\":[],\"persiapan_battery_action\":\"keep_existing\",\"persiapan_battery_id\":\"6\",\"persiapan_charger_action\":\"assign\",\"persiapan_charger_id\":\"12\",\"fabrikasi_attachment_id\":\"15\",\"prepared_units\":[{\"unit_id\":\"1\",\"battery_inventory_id\":\"5\",\"charger_inventory_id\":\"10\",\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-03 09:40:09\"},{\"unit_id\":\"12\",\"battery_inventory_id\":\"6\",\"charger_inventory_id\":\"12\",\"attachment_inventory_id\":\"15\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"a\",\"timestamp\":\"2025-09-03 09:41:18\"}]}','IN_PROGRESS','JOHANA - DEPI','2025-09-03','2025-09-03','2025-09-03 09:41:03',12,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\"]','JOHANA - DEPI','2025-09-02','2025-09-02','2025-09-03 09:41:10',NULL,'ARIZAL-EKA','2025-09-03','2025-09-03','2025-09-03 09:41:14','JOHANA - DEPI','2025-09-03','2025-09-03','2025-09-03 09:41:18','a',NULL,1,'2025-09-03 09:38:49','2025-09-04 03:43:23',NULL,NULL,1,NULL,NULL,NULL),(28,'SPK/202509/002','UNIT',44,19,2,'MSI','MSI','MSI','09213123123','EROPA',NULL,'{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"HAND PALLET\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"41\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"5\",\"mast_id\":\"22\",\"ban_id\":\"6\",\"roda_id\":\"1\",\"valve_id\":\"2\",\"aksesoris\":[],\"persiapan_battery_action\":\"assign\",\"persiapan_battery_id\":\"7\",\"persiapan_charger_action\":\"assign\",\"persiapan_charger_id\":\"14\",\"fabrikasi_attachment_id\":\"15\",\"prepared_units\":[{\"unit_id\":\"1\",\"battery_inventory_id\":\"5\",\"charger_inventory_id\":\"10\",\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-04 04:14:01\"},{\"unit_id\":\"2\",\"battery_inventory_id\":\"7\",\"charger_inventory_id\":\"14\",\"attachment_inventory_id\":\"15\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-04 04:14:39\"}]}','COMPLETED','JOHANA - DEPI','2025-09-04','2025-09-04','2025-09-04 04:14:20',2,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]','JOHANA - DEPI','2025-09-04','2025-09-04','2025-09-04 04:14:29',NULL,'ARIZAL-EKA','2025-09-04','2025-09-04','2025-09-04 04:14:34','JOHANA - DEPI','2025-09-04','2025-09-04','2025-09-04 04:14:39','ok',NULL,1,'2025-09-04 04:13:09','2025-09-04 08:53:00',NULL,NULL,1,NULL,NULL,NULL),(29,'SPK/202509/003','UNIT',54,37,2,'KNTRK/2209/0001','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-09','{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"PALLET STACKER\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"42\",\"attachment_tipe\":\"FORKLIFT SCALE\",\"attachment_merk\":null,\"jenis_baterai\":\"Lead Acid\",\"charger_id\":\"1\",\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"1\",\"valve_id\":\"1\",\"aksesoris\":[],\"persiapan_battery_action\":\"keep_existing\",\"persiapan_battery_id\":\"6\",\"persiapan_charger_action\":\"assign\",\"persiapan_charger_id\":\"12\",\"fabrikasi_attachment_id\":\"4\",\"prepared_units\":[{\"unit_id\":\"12\",\"battery_inventory_id\":\"6\",\"charger_inventory_id\":\"5\",\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"mekanik\":\"IYAN\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-09 10:04:37\"},{\"unit_id\":\"12\",\"battery_inventory_id\":\"6\",\"charger_inventory_id\":\"12\",\"attachment_inventory_id\":\"4\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"mekanik\":\"IYAN\",\"catatan\":\"123\",\"timestamp\":\"2025-09-09 10:06:17\"}]}','COMPLETED','IYAN','2025-09-09','2025-09-09','2025-09-09 10:06:03',12,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]','JOHANA - DEPI','2025-09-09','2025-09-09','2025-09-09 10:06:09',NULL,'JOHANA - DEPI','2025-09-09','2025-09-09','2025-09-09 10:06:13','IYAN','2025-09-09','2025-09-09','2025-09-09 10:06:17','123',NULL,1,'2025-09-09 10:03:41','2025-09-12 06:24:16',NULL,NULL,1,NULL,NULL,NULL),(36,'SPK/202509/004','UNIT',55,39,2,'SML/DS/121025','LG','ANDI','08213564778','Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190','2025-09-12','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"THREE WHEEL\",\"merk_unit\":\"HANGCHA\",\"model_unit\":null,\"kapasitas_id\":\"14\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"16\",\"ban_id\":\"6\",\"roda_id\":\"2\",\"valve_id\":\"2\",\"aksesoris\":[],\"fabrikasi_attachment_id\":\"3\",\"prepared_units\":[{\"unit_id\":\"4\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"4\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"mekanik\":\"INDRA\",\"catatan\":\"OK\",\"timestamp\":\"2025-09-12 06:37:44\"},{\"unit_id\":\"10\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"3\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"mekanik\":\"UDUD\",\"catatan\":\"ok\",\"timestamp\":\"2025-09-12 06:38:33\"}]}','COMPLETED','IYAN','2025-09-12','2025-09-12','2025-09-12 06:38:03',10,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]','BADRUN','2025-09-12','2025-09-12','2025-09-12 06:38:16',NULL,'INDRA','2025-09-12','2025-09-12','2025-09-12 06:38:24','UDUD','2025-09-12','2025-09-12','2025-09-12 06:38:33','ok',NULL,1,'2025-09-12 06:35:58','2025-09-12 06:52:30',NULL,NULL,1,NULL,NULL,NULL),(37,'SPK/202509/005','UNIT',56,40,1,'TEST/AUTO/001','Test Auto Update','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-12','{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":null,\"merk_unit\":null,\"model_unit\":null,\"kapasitas_id\":\"42\",\"attachment_tipe\":null,\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":null,\"ban_id\":null,\"roda_id\":null,\"valve_id\":null,\"aksesoris\":[],\"fabrikasi_attachment_id\":\"16\",\"prepared_units\":[{\"unit_id\":\"7\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[]\",\"mekanik\":\"123\",\"catatan\":\"123\",\"timestamp\":\"2025-09-12 10:06:06\"}]}','COMPLETED','123','2025-09-12','2025-09-12','2025-09-12 10:05:41',7,'[]','123','2025-09-12','2025-09-12','2025-09-12 10:05:52',NULL,'123','2025-09-12','2025-09-12','2025-09-12 10:05:59','123','2025-09-12','2025-09-12','2025-09-12 10:06:06','123',NULL,1,'2025-09-12 10:05:22','2025-09-12 10:06:48',NULL,NULL,1,NULL,NULL,NULL),(38,'SPK/202509/006','UNIT',54,37,1,'KNTRK/2209/0001','Sarana Mitra Luas',NULL,NULL,NULL,'2025-09-13','{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"PALLET STACKER\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"42\",\"attachment_tipe\":\"FORKLIFT SCALE\",\"attachment_merk\":null,\"jenis_baterai\":\"Lead Acid\",\"charger_id\":\"1\",\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"1\",\"valve_id\":\"1\",\"aksesoris\":[],\"fabrikasi_attachment_id\":\"16\",\"prepared_units\":[{\"unit_id\":\"5\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"mekanik\":\"123\",\"catatan\":\"1\",\"timestamp\":\"2025-09-13 01:46:42\"}]}','COMPLETED','JAJA','2025-09-13','2025-09-13','2025-09-13 01:46:15',5,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]','123','2025-09-13','2025-09-13','2025-09-13 01:46:26',NULL,'123','2025-09-13','2025-09-13','2025-09-13 01:46:32','123','2025-09-13','2025-09-13','2025-09-13 01:46:42','1',NULL,1,'2025-09-13 01:33:11','2025-09-13 01:48:42',NULL,NULL,1,NULL,NULL,NULL),(39,'SPK/202509/007','UNIT',57,41,2,'test/1/1/5','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13','{\"departemen_id\":\"3\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"COUNTER BALANCE\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"40\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"16\",\"ban_id\":\"3\",\"roda_id\":\"1\",\"valve_id\":\"3\",\"aksesoris\":[],\"fabrikasi_attachment_id\":\"15\",\"prepared_units\":[{\"unit_id\":\"6\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"P3K\\\"]\",\"mekanik\":\"123\",\"catatan\":\"a\",\"timestamp\":\"2025-09-13 02:58:09\"},{\"unit_id\":\"9\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"15\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\",\"mekanik\":\"123\",\"catatan\":\"a\",\"timestamp\":\"2025-09-13 02:58:34\"}]}','COMPLETED','JAJA','2025-09-13','2025-09-13','2025-09-13 02:58:20',9,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"ACRYLIC\",\"P3K\",\"SAFETY BELT INTERLOC\",\"SPARS ARRESTOR\"]','123','2025-09-13','2025-09-13','2025-09-13 02:58:26',NULL,'123','2025-09-13','2025-09-13','2025-09-13 02:58:29','123','2025-09-13','2025-09-13','2025-09-13 02:58:34','a',NULL,1,'2025-09-13 02:57:15','2025-09-13 02:59:38',NULL,NULL,1,NULL,NULL,NULL),(40,'SPK/202509/008','UNIT',57,42,1,'test/1/1/5','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13','{\"departemen_id\":\"3\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"PALLET STACKER\",\"merk_unit\":\"KOMATSU\",\"model_unit\":null,\"kapasitas_id\":\"42\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"17\",\"ban_id\":\"3\",\"roda_id\":\"4\",\"valve_id\":\"2\",\"aksesoris\":[],\"fabrikasi_attachment_id\":\"3\",\"prepared_units\":[{\"unit_id\":\"11\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"3\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"CAMERA AI\\\",\\\"SPEED LIMITER\\\",\\\"LASER FORK\\\",\\\"HORN KLASON\\\",\\\"APAR 3 KG\\\"]\",\"mekanik\":\"123\",\"catatan\":\"1\",\"timestamp\":\"2025-09-13 03:43:24\"}]}','COMPLETED','JAJA','2025-09-13','2025-09-13','2025-09-13 03:35:25',11,'[\"LAMPU UTAMA\",\"CAMERA AI\",\"SPEED LIMITER\",\"LASER FORK\",\"HORN KLASON\",\"APAR 3 KG\"]','123','2025-09-13','2025-09-13','2025-09-13 03:43:16',NULL,'123','2025-09-13','2025-09-13','2025-09-13 03:43:21','123','2025-09-13','2025-09-13','2025-09-13 03:43:24','1',NULL,1,'2025-09-13 03:35:03','2025-09-13 03:43:58',NULL,NULL,1,NULL,NULL,NULL),(41,'SPK/202509/009','UNIT',58,43,1,'test/1/1/6','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-15','{\"departemen_id\":\"3\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"HAND PALLET\",\"merk_unit\":\"LINDE\",\"model_unit\":null,\"kapasitas_id\":\"41\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"15\",\"ban_id\":\"6\",\"roda_id\":\"1\",\"valve_id\":\"3\",\"aksesoris\":[],\"fabrikasi_attachment_id\":\"16\",\"prepared_units\":[{\"unit_id\":\"13\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\"]\",\"mekanik\":\"JOHANA - DEPI\",\"catatan\":\"a\",\"timestamp\":\"2025-09-15 08:22:53\"}]}','READY','JAJA','2025-09-15','2025-09-15','2025-09-15 08:09:51',13,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\"]','ARIZAL-EKA','2025-09-15','2025-09-15','2025-09-15 08:21:59',NULL,'123','2025-09-15','2025-09-15','2025-09-15 08:22:44','JOHANA - DEPI','2025-09-15','2025-09-15','2025-09-15 08:22:53','a',NULL,1,'2025-09-15 08:09:31','2025-09-15 08:22:53',NULL,NULL,1,NULL,NULL,NULL),(42,'SPK/202509/010','UNIT',57,41,1,'test/1/1/5','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530',NULL,'{\"departemen_id\":\"3\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"COUNTER BALANCE\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"40\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"16\",\"ban_id\":\"3\",\"roda_id\":\"1\",\"valve_id\":\"3\",\"aksesoris\":[],\"fabrikasi_attachment_id\":\"4\",\"prepared_units\":[{\"unit_id\":\"13\",\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"4\",\"aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\",\"mekanik\":\"IYAN\",\"catatan\":\"a\",\"timestamp\":\"2025-09-16 06:57:54\"}]}','READY','ARIZAL-EKA','2025-09-16','2025-09-16','2025-09-16 06:57:35',13,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA\",\"BIO METRIC\",\"ACRYLIC\",\"P3K\",\"SAFETY BELT INTERLOC\",\"SPARS ARRESTOR\"]','IYAN','2025-09-16','2025-09-16','2025-09-16 06:57:44',NULL,'IYAN','2025-09-16','2025-09-16','2025-09-16 06:57:50','IYAN','2025-09-16','2025-09-16','2025-09-16 06:57:54','a',NULL,1,'2025-09-16 06:56:53','2025-09-16 06:57:54',NULL,NULL,1,NULL,NULL,NULL),(49,'SPK/202509/011','ATTACHMENT',56,44,1,'TEST/AUTO/001','Test Client',NULL,NULL,NULL,'2025-09-16','{\"departemen_id\":\"1\",\"tipe_unit_id\":null,\"tipe_jenis\":null,\"merk_unit\":null,\"model_unit\":null,\"kapasitas_id\":null,\"attachment_tipe\":\"SIDE SHIFTER\",\"attachment_merk\":\"\",\"jenis_baterai\":\"\",\"charger_id\":\"0\",\"mast_id\":null,\"ban_id\":null,\"roda_id\":null,\"valve_id\":null,\"aksesoris\":[],\"fabrikasi_attachment_id\":\"16\",\"prepared_units\":[{\"unit_id\":null,\"battery_inventory_id\":null,\"charger_inventory_id\":null,\"attachment_inventory_id\":\"16\",\"aksesoris_tersedia\":null,\"mekanik\":\"IYAN\",\"catatan\":\"a\",\"timestamp\":\"2025-09-16 09:10:41\"}]}','IN_PROGRESS',NULL,NULL,NULL,NULL,NULL,NULL,'IYAN','2025-09-16','2025-09-16','2025-09-16 09:10:32',NULL,'IYAN','2025-09-16','2025-09-16','2025-09-16 09:10:35','IYAN','2025-09-16','2025-09-16','2025-09-16 09:10:41','a',NULL,1,'2025-09-16 08:43:46','2025-09-17 02:54:40',NULL,NULL,1,NULL,NULL,NULL);
/*!40000 ALTER TABLE `spk` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'STRICT_TRANS_TABLES,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_spk_update_unit`
    AFTER UPDATE ON `spk`
    FOR EACH ROW
BEGIN
    
    IF NEW.status IN ('READY', 'COMPLETED') AND OLD.status != NEW.status THEN
        UPDATE `inventory_unit` iu
        SET iu.spk_id = NEW.id,
            iu.updated_at = CURRENT_TIMESTAMP
        WHERE iu.kontrak_spesifikasi_id = NEW.kontrak_spesifikasi_id
        AND iu.spk_id IS NULL;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `spk_backup_20250903`
--

DROP TABLE IF EXISTS `spk_backup_20250903`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spk_backup_20250903` (
  `id` int(10) unsigned NOT NULL DEFAULT 0,
  `nomor_spk` varchar(100) NOT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT','TUKAR') NOT NULL DEFAULT 'UNIT',
  `kontrak_id` int(10) unsigned DEFAULT NULL,
  `kontrak_spesifikasi_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
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
  `dibuat_oleh` int(10) unsigned DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT current_timestamp(),
  `diperbarui_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_backup_20250903`
--

LOCK TABLES `spk_backup_20250903` WRITE;
/*!40000 ALTER TABLE `spk_backup_20250903` DISABLE KEYS */;
INSERT INTO `spk_backup_20250903` VALUES (21,'SPK/202508/001','UNIT',14,12,10,'test/1/1/9','PURI NUSA','Adit','082134555233','Gemalapik','2025-08-26','{\"ban_id\": \"3\", \"mast_id\": \"15\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"LINDE\", \"charger_id\": null, \"model_unit\": null, \"tipe_jenis\": \"SCRUBER\", \"kapasitas_id\": \"42\", \"tipe_unit_id\": \"4\", \"departemen_id\": \"1\", \"jenis_baterai\": null, \"prepared_units\": [{\"catatan\": \"awe\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"8\", \"timestamp\": \"2025-08-26 09:16:08\", \"attachment_id\": \"4\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"CAMERA AI\\\",\\\"LASER FORK\\\",\\\"VOICE ANNOUNCER\\\",\\\"APAR 1 KG\\\",\\\"P3K\\\",\\\"BEACON\\\",\\\"SPARS ARRESTOR\\\"]\"}, {\"catatan\": \"ok\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"7\", \"timestamp\": \"2025-08-27 02:22:28\", \"attachment_id\": \"4\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"CAMERA AI\\\",\\\"CAMERA\\\",\\\"LASER FORK\\\",\\\"VOICE ANNOUNCER\\\",\\\"HORN SPEAKER\\\",\\\"ACRYLIC\\\",\\\"APAR 1 KG\\\",\\\"P3K\\\",\\\"BEACON\\\",\\\"SPARS ARRESTOR\\\"]\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\"}','IN_PROGRESS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ok','test',1,'2025-08-26 08:28:33','2025-08-27 02:22:28'),(22,'SPK/202508/002','UNIT',15,11,1,'test/1/1/10','PURI INDAH','Adit','082134555233','Gemalapik','2025-08-27','{\"ban_id\": \"3\", \"mast_id\": \"15\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"KOMATSU\", \"charger_id\": null, \"model_unit\": null, \"tipe_jenis\": \"DUMP TRUCK\", \"kapasitas_id\": null, \"tipe_unit_id\": \"1\", \"departemen_id\": \"1\", \"jenis_baterai\": null, \"prepared_units\": [{\"catatan\": \"a\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"15\", \"timestamp\": \"2025-08-27 06:51:38\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BACK BUZZER\\\",\\\"SPEED LIMITER\\\",\\\"VOICE ANNOUNCER\\\",\\\"HORN SPEAKER\\\",\\\"HORN KLASON\\\",\\\"BIO METRIC\\\",\\\"APAR 1 KG\\\",\\\"APAR 3 KG\\\",\\\"BEACON\\\"]\", \"battery_inventory_id\": \"5\", \"charger_inventory_id\": \"6\", \"attachment_inventory_id\": \"4\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\", \"persiapan_battery_id\": \"5\", \"persiapan_charger_id\": \"6\", \"fabrikasi_attachment_id\": \"4\"}','IN_PROGRESS','IYAN','2025-08-27','2025-08-27','2025-08-27 06:51:14',15,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"SPEED LIMITER\",\"VOICE ANNOUNCER\",\"HORN SPEAKER\",\"HORN KLASON\",\"BIO METRIC\",\"APAR 1 KG\",\"APAR 3 KG\",\"BEACON\"]','ARIZAL-EKA','2025-08-27','2025-08-27','2025-08-27 06:51:25',NULL,'JOHANA - DEPI','2025-08-27','2025-08-27','2025-08-27 06:51:32','SAMSURI-RIKI','2025-08-27','2025-08-27','2025-08-27 06:51:37','a',NULL,1,'2025-08-27 04:14:39','2025-08-27 15:24:37'),(23,'SPK/202508/003','UNIT',16,15,1,'test/1/1/11','Sarana Mitra Luas Tbk','kaleng','22131231231','Area Kargo Bandara Soekarno-Hatta','2025-08-27','{\"ban_id\": \"1\", \"mast_id\": \"15\", \"roda_id\": \"3\", \"valve_id\": \"3\", \"aksesoris\": [], \"merk_unit\": \"KOMATSU\", \"charger_id\": \"8\", \"model_unit\": null, \"tipe_jenis\": \"SCRUBER\", \"kapasitas_id\": \"43\", \"tipe_unit_id\": \"4\", \"departemen_id\": \"2\", \"jenis_baterai\": \"Lead Acid\", \"prepared_units\": [{\"catatan\": \"aa\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"13\", \"timestamp\": \"2025-08-27 09:01:35\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"ROTARY LAMP\\\",\\\"BACK BUZZER\\\",\\\"SENSOR PARKING\\\",\\\"SPEED LIMITER\\\"]\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"4\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\", \"fabrikasi_attachment_id\": \"4\"}','IN_PROGRESS','IYAN','2025-08-27','2025-08-27','2025-08-27 09:01:05',13,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"ROTARY LAMP\",\"BACK BUZZER\",\"SENSOR PARKING\",\"SPEED LIMITER\"]','ARIZAL-EKA','2025-08-27','2025-08-27','2025-08-27 09:01:12',NULL,'JOHANA - DEPI','2025-08-27','2025-08-27','2025-08-27 09:01:26','SAMSURI-RIKI','2025-08-27','2025-08-27','2025-08-27 09:01:35','aa',NULL,1,'2025-08-27 09:00:44','2025-08-27 15:26:16'),(24,'SPK/202508/004','UNIT',14,13,2,'test/1/1/9','PURI NUSA','Adit','082134555233','Gemalapik','2025-08-27','{\"ban_id\": \"6\", \"mast_id\": \"15\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"HYUNDAI\", \"charger_id\": \"8\", \"model_unit\": null, \"tipe_jenis\": \"SCRUBER\", \"kapasitas_id\": \"11\", \"tipe_unit_id\": \"4\", \"departemen_id\": \"2\", \"jenis_baterai\": \"Lead Acid\", \"prepared_units\": [{\"catatan\": \"a\", \"mekanik\": \"SAMSURI-RIKI\", \"unit_id\": \"16\", \"timestamp\": \"2025-08-27 16:53:55\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"HORN KLASON\\\"]\", \"battery_inventory_id\": \"3\", \"charger_inventory_id\": \"6\", \"attachment_inventory_id\": \"15\"}, {\"catatan\": \"ok\", \"mekanik\": \"IYAN\", \"unit_id\": \"17\", \"timestamp\": \"2025-08-30 02:08:03\", \"aksesoris_tersedia\": null, \"battery_inventory_id\": \"3\", \"charger_inventory_id\": \"8\", \"attachment_inventory_id\": \"16\"}], \"attachment_merk\": null, \"attachment_tipe\": \"FORK POSITIONER\", \"persiapan_battery_id\": \"3\", \"persiapan_charger_id\": \"8\", \"fabrikasi_attachment_id\": \"16\", \"persiapan_battery_action\": \"keep_existing\", \"persiapan_charger_action\": \"assign\", \"persiapan_battery_inventory_id\": \"30\", \"persiapan_charger_inventory_id\": \"null\", \"fabrikasi_attachment_inventory_id\": \"null\"}','IN_PROGRESS','test','2025-08-30','2025-08-31','2025-08-30 02:00:18',17,NULL,'JOHANA - DEPI','2025-08-30','2025-08-30','2025-08-30 02:07:39',NULL,'SAMSURI-RIKI','2025-08-30','2025-08-30','2025-08-30 02:07:52','IYAN','2025-08-30','2025-08-30','2025-08-30 02:08:03','ok',NULL,1,'2025-08-27 15:37:29','2025-08-30 03:42:03'),(25,'SPK/202508/005','UNIT',17,16,3,'test/1/1/12','LG Cibitung','AA','12312313','SAMPING TOL CIBITUNG','2025-08-28','{\"ban_id\": \"3\", \"mast_id\": \"12\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"merk_unit\": \"HELI\", \"charger_id\": \"4\", \"model_unit\": null, \"tipe_jenis\": \"PALLET MOVER\", \"kapasitas_id\": \"16\", \"tipe_unit_id\": \"6\", \"departemen_id\": \"2\", \"jenis_baterai\": \"Lead Acid\", \"attachment_merk\": null, \"attachment_tipe\": \"\"}','IN_PROGRESS',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-08-28 01:54:22','2025-08-28 01:54:29'),(26,'SPK/202509/001','UNIT',41,0,2,'TETTETESS','MONORKOBO','JAJA','09324987729','BEKASI','2025-09-01','{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"HAND PALLET\",\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"11\",\"attachment_tipe\":\"PAPER ROLL CLAMP\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"8\",\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"3\",\"valve_id\":\"3\",\"aksesoris\":[]}','SUBMITTED',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-09-01 04:16:57','2025-09-01 04:16:57');
/*!40000 ALTER TABLE `spk_backup_20250903` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_component_transactions`
--

DROP TABLE IF EXISTS `spk_component_transactions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spk_component_transactions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `spk_id` int(10) unsigned NOT NULL,
  `transaction_type` enum('ASSIGN','UNASSIGN','MODIFY') NOT NULL DEFAULT 'ASSIGN',
  `component_type` enum('UNIT','ATTACHMENT','BATTERY','CHARGER') NOT NULL,
  `component_id` int(10) unsigned NOT NULL COMMENT 'ID from respective table (inventory_unit, inventory_attachment)',
  `inventory_id` int(10) unsigned DEFAULT NULL COMMENT 'ID from inventory_attachment if applicable',
  `mekanik` varchar(100) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `created_by` int(10) unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_spk_component_spk` (`spk_id`),
  KEY `idx_spk_component_type` (`component_type`),
  KEY `idx_spk_component_id` (`component_id`),
  KEY `idx_spk_component_inventory` (`inventory_id`),
  KEY `idx_spk_component_created` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_component_transactions`
--

LOCK TABLES `spk_component_transactions` WRITE;
/*!40000 ALTER TABLE `spk_component_transactions` DISABLE KEYS */;
INSERT INTO `spk_component_transactions` VALUES (1,1,'ASSIGN','UNIT',1,NULL,'John Doe','Unit assigned for SPK preparation',1,'2025-08-30 02:22:02','2025-08-30 02:22:02'),(2,1,'ASSIGN','ATTACHMENT',1,NULL,'John Doe','Forklift attachment assigned',1,'2025-08-30 02:22:02','2025-08-30 02:22:02'),(3,1,'ASSIGN','BATTERY',1,NULL,'John Doe','Battery assigned for unit',1,'2025-08-30 02:22:02','2025-08-30 02:22:02');
/*!40000 ALTER TABLE `spk_component_transactions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_status_history`
--

DROP TABLE IF EXISTS `spk_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spk_status_history` (
  `id` int(10) unsigned NOT NULL,
  `spk_id` int(10) unsigned NOT NULL,
  `status_from` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') DEFAULT NULL,
  `status_to` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') NOT NULL,
  `changed_by` int(10) unsigned DEFAULT NULL,
  `note` varchar(255) DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_status_history`
--

LOCK TABLES `spk_status_history` WRITE;
/*!40000 ALTER TABLE `spk_status_history` DISABLE KEYS */;
INSERT INTO `spk_status_history` VALUES (0,27,'READY','IN_PROGRESS',1,'DI created: DI/202509/001','2025-09-04 10:28:19'),(22,22,'READY','IN_PROGRESS',1,'DI created: DI/202508/007','2025-08-27 15:24:37'),(23,23,'READY','IN_PROGRESS',1,'DI created: DI/202508/008','2025-08-27 15:26:16');
/*!40000 ALTER TABLE `spk_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_units`
--

DROP TABLE IF EXISTS `spk_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `spk_units` (
  `id` int(10) unsigned NOT NULL,
  `spk_id` int(10) unsigned NOT NULL,
  `unit_id` int(10) unsigned DEFAULT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_units`
--

LOCK TABLES `spk_units` WRITE;
/*!40000 ALTER TABLE `spk_units` DISABLE KEYS */;
/*!40000 ALTER TABLE `spk_units` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_code` varchar(20) NOT NULL COMMENT 'STF001, STF002, etc',
  `staff_name` varchar(100) NOT NULL,
  `staff_role` enum('ADMIN','FOREMAN','MECHANIC','HELPER','SUPERVISOR') NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_code` (`staff_code`),
  UNIQUE KEY `uk_staff_code` (`staff_code`),
  KEY `idx_staff_role` (`staff_role`),
  KEY `idx_staff_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master Staff/Karyawan';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES (1,'STF001','Novi','ADMIN',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(2,'STF002','Sari','ADMIN',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(3,'STF003','Andi','ADMIN',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(4,'STF004','YOGA','FOREMAN',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(5,'STF005','Budi','FOREMAN',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(6,'STF006','Eko','FOREMAN',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(7,'STF007','KURNIA','MECHANIC',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(8,'STF008','BAGUS','MECHANIC',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(9,'STF009','Deni','MECHANIC',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(10,'STF010','Rudi','MECHANIC',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(11,'STF011','Wahyu','MECHANIC',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(12,'STF012','Joko','MECHANIC',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(13,'STF013','Agus','HELPER',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(14,'STF014','Dimas','HELPER',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(15,'STF015','Fajar','HELPER',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(16,'STF016','Hendra','HELPER',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37'),(17,'STF017','Iwan','HELPER',NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37');
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_attachment`
--

DROP TABLE IF EXISTS `status_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_attachment` (
  `id_status_attachment` int(11) NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(50) NOT NULL,
  `deskripsi` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id_status_attachment`),
  UNIQUE KEY `nama_status` (`nama_status`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_attachment`
--

LOCK TABLES `status_attachment` WRITE;
/*!40000 ALTER TABLE `status_attachment` DISABLE KEYS */;
INSERT INTO `status_attachment` VALUES (1,'AVAILABLE','Attachment tersedia untuk digunakan',1,'2025-09-13 05:18:52','2025-09-13 05:18:52'),(2,'USED','Attachment sedang digunakan pada unit',1,'2025-09-13 05:18:52','2025-09-13 05:18:52'),(3,'MAINTENANCE','Attachment dalam pemeliharaan',1,'2025-09-13 05:18:52','2025-09-13 05:18:52'),(4,'RUSAK','Attachment rusak tidak dapat digunakan',1,'2025-09-13 05:18:52','2025-09-13 05:18:52'),(5,'RESERVED','Attachment direservasi untuk SPK tertentu',1,'2025-09-13 05:18:52','2025-09-13 05:18:52');
/*!40000 ALTER TABLE `status_attachment` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_eksekusi_workflow`
--

DROP TABLE IF EXISTS `status_eksekusi_workflow`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_eksekusi_workflow` (
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_eksekusi_workflow`
--

LOCK TABLES `status_eksekusi_workflow` WRITE;
/*!40000 ALTER TABLE `status_eksekusi_workflow` DISABLE KEYS */;
INSERT INTO `status_eksekusi_workflow` VALUES (1,'BELUM_MULAI','Belum Mulai','SPK belum dikerjakan',1,'#6c757d',1,'2025-09-03 08:58:54'),(2,'PERSIAPAN','Persiapan Unit','Sedang mempersiapkan unit',2,'#ffc107',1,'2025-09-03 08:58:54'),(3,'DALAM_PERJALANAN','Dalam Perjalanan','Unit sedang dalam perjalanan ke tujuan',3,'#17a2b8',1,'2025-09-03 08:58:54'),(4,'SAMPAI_LOKASI','Sampai di Lokasi','Unit sudah sampai di lokasi tujuan',4,'#28a745',1,'2025-09-03 08:58:54'),(5,'SELESAI','Selesai','Pekerjaan sudah selesai dikerjakan',5,'#28a745',1,'2025-09-03 08:58:54');
/*!40000 ALTER TABLE `status_eksekusi_workflow` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status_unit`
--

DROP TABLE IF EXISTS `status_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status_unit` (
  `id_status` int(11) NOT NULL,
  `status_unit` varchar(50) NOT NULL,
  PRIMARY KEY (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_unit`
--

LOCK TABLES `status_unit` WRITE;
/*!40000 ALTER TABLE `status_unit` DISABLE KEYS */;
INSERT INTO `status_unit` VALUES (1,'WORKSHOP-HIDUP'),(2,'WORKSHOP-RUSAK'),(3,'RENTAL'),(4,'UNIT PULANG'),(5,'UNIT HARIAN'),(6,'BOOKING'),(7,'STOCK ASET'),(8,'STOCK NON ASET'),(9,'JUAL');
/*!40000 ALTER TABLE `status_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `suppliers` (
  `id_supplier` int(11) NOT NULL,
  `nama_supplier` varchar(150) NOT NULL,
  `kontak_person` varchar(100) DEFAULT NULL,
  `telepon` varchar(50) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id_supplier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'PT. Forklift Jaya Abadi','Bapak Budi','081234567890',NULL,'2025-07-15 20:43:59',NULL),(2,'CV. Sinar Baterai','Ibu Susan','081122334455',NULL,'2025-07-15 20:43:59',NULL),(3,'Toko Sparepart Maju','Pak Eko','021-555-1234',NULL,'2025-07-15 20:43:59',NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_activity_log`
--

DROP TABLE IF EXISTS `system_activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_activity_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table_name` varchar(64) NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
  `record_id` int(10) unsigned NOT NULL COMMENT 'ID of the affected record',
  `action_type` enum('CREATE','READ','UPDATE','DELETE','EXPORT','IMPORT','LOGIN','LOGOUT','APPROVE','REJECT','SUBMIT','CANCEL','ASSIGN','UNASSIGN','COMPLETE','PRINT','DOWNLOAD') NOT NULL,
  `action_description` varchar(255) NOT NULL COMMENT 'Brief description of what happened',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Previous values (only changed fields)' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'New values (only changed fields)' CHECK (json_valid(`new_values`)),
  `affected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'List of fields that were changed' CHECK (json_valid(`affected_fields`)),
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'FK to users.id',
  `workflow_stage` varchar(50) DEFAULT NULL COMMENT 'Current business stage',
  `is_critical` tinyint(1) DEFAULT 0 COMMENT 'Mark critical business actions',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT') DEFAULT NULL COMMENT 'Application module where activity occurred',
  `submenu_item` varchar(100) DEFAULT NULL COMMENT 'Specific submenu item accessed',
  `business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'LOW' COMMENT 'Business impact level',
  `related_entities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'JSON object storing related entity relationships' CHECK (json_valid(`related_entities`)),
  PRIMARY KEY (`id`),
  KEY `idx_related_entities` (`related_entities`(255))
) ENGINE=InnoDB AUTO_INCREMENT=197 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_activity_log`
--

LOCK TABLES `system_activity_log` WRITE;
/*!40000 ALTER TABLE `system_activity_log` DISABLE KEYS */;
INSERT INTO `system_activity_log` VALUES (1,'kontrak',44,'CREATE','Kontrak baru dibuat dengan nomor PO-CL-0488',NULL,'{\"no_po_marketing\": \"PO-CL-0488\", \"pelanggan\": \"PT Client\", \"status\": \"ACTIVE\"}','[\"no_po_marketing\", \"pelanggan\", \"status\"]',1,'KONTRAK',1,'2025-09-08 06:43:05',NULL,NULL,'LOW',NULL),(2,'inventory_unit',1,'ASSIGN','Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan',NULL,'{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}','[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]',1,'KONTRAK',1,'2025-09-08 06:43:05',NULL,NULL,'LOW',NULL),(3,'inventory_unit',2,'ASSIGN','Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan',NULL,'{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}','[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]',1,'KONTRAK',1,'2025-09-08 06:43:05',NULL,NULL,'LOW',NULL),(7,'kontrak',48,'DELETE','Test delete logging manual',NULL,NULL,NULL,1,NULL,0,'2025-09-08 10:08:42',NULL,NULL,'LOW',NULL),(8,'kontrak',49,'DELETE','Test Delete','{}',NULL,'[]',1,NULL,0,'2025-09-09 04:14:05',NULL,NULL,'LOW',NULL),(9,'kontrak',48,'DELETE','Kontrak deleted: TEST-DELETE-LOG (Client: Test Client for Delete)','{\"id\":\"48\",\"no_kontrak\":\"TEST-DELETE-LOG\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Delete\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-08\",\"tanggal_berakhir\":\"2025-12-08\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 17:06:22\",\"diperbarui_pada\":\"2025-09-08 17:06:22\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,NULL,1,'2025-09-09 04:16:39','MARKETING',NULL,'LOW',NULL),(10,'kontrak',49,'DELETE','Kontrak deleted: TEST-DELETE-LOG-2 (Client: Test Client for Delete 2)','{\"id\":\"49\",\"no_kontrak\":\"TEST-DELETE-LOG-2\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Delete 2\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-08\",\"tanggal_berakhir\":\"2025-12-08\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 17:09:00\",\"diperbarui_pada\":\"2025-09-08 17:09:00\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,NULL,1,'2025-09-09 04:18:47','MARKETING',NULL,'LOW',NULL),(11,'kontrak',52,'DELETE','Kontrak deleted: TEST-COMPLETE-LOG (Client: Test Complete Logging)','{\"id\":\"52\",\"no_kontrak\":\"TEST-COMPLETE-LOG\",\"no_po_marketing\":null,\"pelanggan\":\"Test Complete Logging\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-09\",\"tanggal_berakhir\":\"2025-12-09\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 04:27:07\",\"diperbarui_pada\":\"2025-09-09 04:27:07\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 04:28:11','MARKETING',NULL,'HIGH',NULL),(12,'kontrak',123,'DELETE','Test delete with JSON relations',NULL,NULL,NULL,1,'DELETE_CONFIRMED',0,'2025-09-09 04:51:45','MARKETING','Data Kontrak','HIGH','{\"kontrak\": [123], \"spk\": [456, 789], \"di\": [101112]}'),(13,'kontrak',999,'CREATE','Test kontrak dengan JSON relations implementasi',NULL,NULL,NULL,1,'DRAFT',0,'2025-09-09 04:52:49','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\": [999], \"spk\": [1001, 1002], \"test_entity\": [555]}'),(15,'kontrak',51,'DELETE','Kontrak deleted: TEST-ALERT-SYSTEM (Client: Test Client for Alert System)','{\"id\":\"51\",\"no_kontrak\":\"TEST-ALERT-SYSTEM\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Alert System\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-09\",\"tanggal_berakhir\":\"2025-12-09\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 11:19:04\",\"diperbarui_pada\":\"2025-09-09 11:19:04\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 06:28:14','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[51]}'),(16,'kontrak',46,'DELETE','Kontrak deleted: TEST-1757315452 (Client: Test Client)','{\"id\":\"46\",\"no_kontrak\":\"TEST-1757315452\",\"no_po_marketing\":\"PO-TEST-1757315452\",\"pelanggan\":\"Test Client\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2024-01-01\",\"tanggal_berakhir\":\"2024-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 07:10:56\",\"diperbarui_pada\":\"2025-09-08 07:10:56\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 06:28:29','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[46]}'),(17,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-09 07:28:45','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(18,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-09 07:29:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(19,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-09 08:00:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(20,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-09 08:00:33','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(21,'kontrak',53,'DELETE','Kontrak deleted: KNTRK/2209/0002 (Client: IBR)','{\"id\":\"53\",\"no_kontrak\":\"KNTRK\\/2209\\/0002\",\"no_po_marketing\":\"PO-ADIT110999\",\"pelanggan\":\"IBR\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 06:30:00\",\"diperbarui_pada\":\"2025-09-09 06:30:00\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 09:42:59','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[53]}'),(22,'kontrak',45,'DELETE','Kontrak deleted: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"id\":\"45\",\"no_kontrak\":\"KNTRK\\/2209\\/0001\",\"no_po_marketing\":\"PO-ADIT10999\",\"pelanggan\":\"Sarana Mitra Luas\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-09-30\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 06:57:54\",\"diperbarui_pada\":\"2025-09-08 06:57:54\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 09:43:20','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[45]}'),(23,'kontrak',54,'CREATE','Kontrak created: KNTRK/2209/0001 (Client: Sarana Mitra Luas)',NULL,'{\"no_kontrak\":\"KNTRK\\/2209\\/0001\",\"no_po_marketing\":\"PO-ADIT10999\",\"pelanggan\":\"Sarana Mitra Luas\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-09 09:54:01','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54]}'),(24,'spk',29,'CREATE','Created new spk record',NULL,'{\"spk_id\":29,\"nomor_spk\":\"SPK\\/202509\\/003\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":2}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-09 10:03:41','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[29]}'),(25,'spk',29,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-09 10:03:48','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(26,'spk',29,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:03:48\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-09\",\"persiapan_unit_estimasi_selesai\":\"2025-09-09\",\"persiapan_unit_tanggal_approve\":\"2025-09-09 10:04:10\",\"diperbarui_pada\":\"2025-09-09 10:04:10\",\"persiapan_unit_id\":\"12\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\"}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:04:10','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(27,'spk',29,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:04:10\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\"}\"}','{\"fabrikasi_mekanik\":\"JOHANA - DEPI\",\"fabrikasi_estimasi_mulai\":\"2025-09-09\",\"fabrikasi_estimasi_selesai\":\"2025-09-09\",\"fabrikasi_tanggal_approve\":\"2025-09-09 10:04:19\",\"diperbarui_pada\":\"2025-09-09 10:04:19\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:04:19','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(28,'spk',29,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:04:19\"}','{\"painting_mekanik\":\"JOHANA - DEPI\",\"painting_estimasi_mulai\":\"2025-09-09\",\"painting_estimasi_selesai\":\"2025-09-09\",\"painting_tanggal_approve\":\"2025-09-09 10:04:27\",\"diperbarui_pada\":\"2025-09-09 10:04:27\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-09 10:04:27','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(29,'spk',29,'UPDATE','Updated spk record','{\"diperbarui_pada\":\"2025-09-09 10:04:27\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"persiapan_unit_id\":\"12\",\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-09\",\"persiapan_unit_estimasi_selesai\":\"2025-09-09\",\"persiapan_unit_tanggal_approve\":\"2025-09-09 10:04:10\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"fabrikasi_mekanik\":\"JOHANA - DEPI\",\"fabrikasi_estimasi_mulai\":\"2025-09-09\",\"fabrikasi_estimasi_selesai\":\"2025-09-09\",\"fabrikasi_tanggal_approve\":\"2025-09-09 10:04:19\",\"painting_mekanik\":\"JOHANA - DEPI\",\"painting_estimasi_mulai\":\"2025-09-09\",\"painting_estimasi_selesai\":\"2025-09-09\",\"painting_tanggal_approve\":\"2025-09-09 10:04:27\"}','{\"diperbarui_pada\":\"2025-09-09 10:04:37\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\",\"pdi_catatan\":\"ok\",\"persiapan_unit_id\":null,\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"persiapan_aksesoris_tersedia\":null,\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null}','[\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"persiapan_unit_id\",\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"persiapan_aksesoris_tersedia\",\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\"]',1,'UPDATED',0,'2025-09-09 10:04:37','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(30,'spk',29,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:04:37\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','{\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-09\",\"persiapan_unit_estimasi_selesai\":\"2025-09-09\",\"persiapan_unit_tanggal_approve\":\"2025-09-09 10:06:03\",\"diperbarui_pada\":\"2025-09-09 10:06:03\",\"persiapan_unit_id\":\"12\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:06:03','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(31,'spk',29,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:06:03\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','{\"fabrikasi_mekanik\":\"JOHANA - DEPI\",\"fabrikasi_estimasi_mulai\":\"2025-09-09\",\"fabrikasi_estimasi_selesai\":\"2025-09-09\",\"fabrikasi_tanggal_approve\":\"2025-09-09 10:06:09\",\"diperbarui_pada\":\"2025-09-09 10:06:09\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:06:09','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(32,'spk',29,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:06:09\"}','{\"painting_mekanik\":\"JOHANA - DEPI\",\"painting_estimasi_mulai\":\"2025-09-09\",\"painting_estimasi_selesai\":\"2025-09-09\",\"painting_tanggal_approve\":\"2025-09-09 10:06:13\",\"diperbarui_pada\":\"2025-09-09 10:06:13\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-09 10:06:13','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(33,'spk',29,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:06:13\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\",\"pdi_catatan\":\"ok\",\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"IYAN\",\"pdi_estimasi_mulai\":\"2025-09-09\",\"pdi_estimasi_selesai\":\"2025-09-09\",\"pdi_tanggal_approve\":\"2025-09-09 10:06:17\",\"diperbarui_pada\":\"2025-09-09 10:06:17\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"},{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"12\\\",\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"123\\\",\\\"timestamp\\\":\\\"2025-09-09 10:06:17\\\"}]}\",\"pdi_catatan\":\"123\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-09 10:06:17','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(34,'delivery_instruction',124,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":124,\"nomor_di\":\"DI\\/202509\\/003\",\"spk_id\":29,\"po_kontrak_nomor\":\"KNTRK\\/2209\\/0001\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[12]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-09 10:07:55','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[124]}'),(35,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-10 01:24:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(36,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-10 02:25:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(37,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-10 02:25:26','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(38,'spk',29,'UPDATE','Updated SPK workflow','{\"status\":\"IN_PROGRESS\"}','{\"status\":\"READY\",\"pdi_mekanik\":\"IYAN\",\"pdi_tanggal_approve\":\"2025-09-10 09:00:00\"}',NULL,1,NULL,0,'2025-09-10 02:27:18','SERVICE',NULL,'MEDIUM',NULL),(39,'kontrak_spesifikasi',1,'CREATE','Created kontrak spesifikasi',NULL,'{\"spek_kode\":\"SPEK001\",\"kontrak_id\":1,\"jumlah_dibutuhkan\":2,\"harga_per_unit_bulanan\":5000000}',NULL,1,NULL,0,'2025-09-10 02:36:58','MARKETING',NULL,'MEDIUM',NULL),(40,'kontrak_spesifikasi',38,'DELETE','Menghapus spesifikasi SPEC-002','{\"id\":\"38\",\"kontrak_id\":\"54\",\"spek_kode\":\"SPEC-002\",\"jumlah_dibutuhkan\":\"1\",\"jumlah_tersedia\":\"0\",\"harga_per_unit_bulanan\":\"10000000.00\",\"harga_per_unit_harian\":null,\"catatan_spek\":\"\",\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"COUNTER BALANCE\",\"kapasitas_id\":\"41\",\"merk_unit\":\"KOMATSU\",\"model_unit\":null,\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"15\",\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"1\",\"valve_id\":\"2\",\"aksesoris\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"dibuat_pada\":\"2025-09-10 02:26:21\",\"diperbarui_pada\":\"2025-09-10 02:26:21\"}',NULL,'[\"id\",\"kontrak_id\",\"spek_kode\",\"jumlah_dibutuhkan\",\"jumlah_tersedia\",\"harga_per_unit_bulanan\",\"harga_per_unit_harian\",\"catatan_spek\",\"departemen_id\",\"tipe_unit_id\",\"tipe_jenis\",\"kapasitas_id\",\"merk_unit\",\"model_unit\",\"attachment_tipe\",\"attachment_merk\",\"jenis_baterai\",\"charger_id\",\"mast_id\",\"ban_id\",\"roda_id\",\"valve_id\",\"aksesoris\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'SPECIFICATION_DELETED',1,'2025-09-10 02:49:33','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[54],\"kontrak_spesifikasi\":[38]}'),(41,'kontrak',44,'UPDATE','Kontrak updated: MSI (Client: MSI)','{\"pic\":\"MSI\",\"catatan\":null}','{\"pic\":\"Adit\",\"catatan\":\"\"}','[\"pic\",\"catatan\"]',1,'UPDATED',0,'2025-09-10 03:07:51','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[44]}'),(42,'kontrak',44,'UPDATE','Kontrak updated: KNTRK/2208/0001 (Client: Sarana Mitra Luas)','{\"no_kontrak\":\"MSI\",\"no_po_marketing\":\"MSI\",\"pelanggan\":\"MSI\",\"catatan\":null}','{\"no_kontrak\":\"KNTRK\\/2208\\/0001\",\"no_po_marketing\":\"PO-ADIT10998\",\"pelanggan\":\"Sarana Mitra Luas\",\"catatan\":\"\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"catatan\"]',1,'UPDATED',0,'2025-09-10 03:08:34','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[44]}'),(43,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"JOKO\",\"no_hp_supir\":\"082138848123\",\"no_sim_supir\":\"1231012\",\"kendaraan\":\"KOKASD\",\"no_polisi_kendaraan\":\"123123\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-10 03:35:03','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(44,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-10 04:18:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(45,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-10 04:18:23','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(46,'spk',30,'CREATE','Created new spk record',NULL,'{\"spk_id\":30,\"nomor_spk\":\"SPK\\/202509\\/004\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-10 08:19:25','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[30]}'),(47,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 02:03:59','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(48,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-11 02:07:28','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(49,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 02:08:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(50,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 02:10:27','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(51,'spk',32,'CREATE','Created new spk record',NULL,'{\"spk_id\":32,\"nomor_spk\":\"SPK\\/202509\\/872\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-11 04:55:30','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[32]}'),(52,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 07:53:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(53,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-12 01:21:56','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(54,'spk',35,'CREATE','Created new spk record',NULL,'{\"spk_id\":35,\"nomor_spk\":\"SPK\\/202509\\/906\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-12 03:52:36','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[35]}'),(55,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-10 10:35:03\",\"tanggal_kirim\":\"2025-09-09\",\"estimasi_sampai\":null,\"nama_supir\":\"JOKO\",\"no_hp_supir\":\"082138848123\",\"no_sim_supir\":\"1231012\",\"kendaraan\":\"KOKASD\",\"no_polisi_kendaraan\":\"123123\",\"catatan\":null,\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:24:06\",\"tanggal_kirim\":\"2025-09-12\",\"estimasi_sampai\":\"2025-09-12\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"catatan\":\"DIKIRIM\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"tanggal_kirim\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"catatan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:24:06','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(56,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:24:06\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:24:10\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:24:10','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(57,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:24:10\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:24:16\",\"catatan_sampai\":\"sudah sampai\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:24:16','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(58,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-12 06:29:53','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(59,'kontrak',55,'CREATE','Kontrak created: SML/DS/121025 (Client: LG)',NULL,'{\"no_kontrak\":\"SML\\/DS\\/121025\",\"no_po_marketing\":\"PO-LG998123\",\"pelanggan\":\"LG\",\"pic\":\"ANDI\",\"kontak\":\"08213564778\",\"lokasi\":\"Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5\\/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-30\",\"tanggal_berakhir\":\"2025-10-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-12 06:34:46','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[55]}'),(60,'spk',36,'CREATE','Created new spk record',NULL,'{\"spk_id\":36,\"nomor_spk\":\"SPK\\/202509\\/004\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"55\",\"kontrak_spesifikasi_id\":\"39\",\"jumlah_unit\":2}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-12 06:35:58','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[36]}'),(61,'spk',36,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-12 06:36:09','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(62,'spk',36,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:36:09\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 06:36:44\",\"diperbarui_pada\":\"2025-09-12 06:36:44\",\"persiapan_unit_id\":\"4\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-12 06:36:44','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(63,'spk',36,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:36:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"BADRUN\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 06:37:00\",\"diperbarui_pada\":\"2025-09-12 06:37:00\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-12 06:37:00','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(64,'spk',36,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:37:00\"}','{\"painting_mekanik\":\"UDUD\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 06:37:09\",\"diperbarui_pada\":\"2025-09-12 06:37:09\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-12 06:37:09','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(65,'spk',36,'UPDATE','Updated spk record','{\"diperbarui_pada\":\"2025-09-12 06:37:09\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\",\"pdi_catatan\":null,\"persiapan_unit_id\":\"4\",\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 06:36:44\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"fabrikasi_mekanik\":\"BADRUN\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 06:37:00\",\"painting_mekanik\":\"UDUD\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 06:37:09\"}','{\"diperbarui_pada\":\"2025-09-12 06:37:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\",\"pdi_catatan\":\"OK\",\"persiapan_unit_id\":null,\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"persiapan_aksesoris_tersedia\":null,\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null}','[\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"persiapan_unit_id\",\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"persiapan_aksesoris_tersedia\",\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\"]',1,'UPDATED',0,'2025-09-12 06:37:44','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(66,'spk',36,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:37:44\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 06:38:03\",\"diperbarui_pada\":\"2025-09-12 06:38:03\",\"persiapan_unit_id\":\"10\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-12 06:38:03','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(67,'spk',36,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:38:03\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\"}','{\"fabrikasi_mekanik\":\"BADRUN\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 06:38:16\",\"diperbarui_pada\":\"2025-09-12 06:38:16\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-12 06:38:16','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(68,'spk',36,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:38:16\"}','{\"painting_mekanik\":\"INDRA\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 06:38:24\",\"diperbarui_pada\":\"2025-09-12 06:38:24\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-12 06:38:24','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(69,'spk',36,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:38:24\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\",\"pdi_catatan\":\"OK\",\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"UDUD\",\"pdi_estimasi_mulai\":\"2025-09-12\",\"pdi_estimasi_selesai\":\"2025-09-12\",\"pdi_tanggal_approve\":\"2025-09-12 06:38:33\",\"diperbarui_pada\":\"2025-09-12 06:38:33\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"},{\\\"unit_id\\\":\\\"10\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"3\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"UDUD\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-12 06:38:33\\\"}]}\",\"pdi_catatan\":\"ok\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-12 06:38:33','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(70,'delivery_instruction',125,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":125,\"nomor_di\":\"DI\\/202509\\/004\",\"spk_id\":36,\"po_kontrak_nomor\":\"SML\\/DS\\/121025\",\"pelanggan\":\"LG\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[4,10]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-12 06:51:13','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[125]}'),(71,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-12 06:51:23','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(72,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:51:23\",\"estimasi_sampai\":null,\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:52:18\",\"estimasi_sampai\":\"2025-09-12\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:52:18','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(73,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:52:18\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:52:26\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:52:26','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(74,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:52:26\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:52:30\",\"catatan_sampai\":\"ok\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:52:30','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(75,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"no_po_marketing\":\"PO-ADIT10999\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":\"39552000.00\",\"total_units\":\"2\"}','{\"no_po_marketing\":null,\"pic\":null,\"kontak\":null,\"lokasi\":null,\"nilai_total\":0,\"total_units\":0}','[\"no_po_marketing\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\"]',NULL,'UPDATED',0,'2025-09-12 09:28:23','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54]}'),(76,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Aktif\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Pending\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:28:41','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(77,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Pending\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:29:01','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(78,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Aktif\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Berakhir\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:30:05','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(79,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Berakhir\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:31:11','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(80,'kontrak',55,'UPDATE','Kontrak updated: SML/DS/121025 (Client: Test)','{\"no_po_marketing\":\"PO-LG998123\",\"pelanggan\":\"LG\",\"pic\":\"ANDI\",\"kontak\":\"08213564778\",\"lokasi\":\"Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5\\/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190\",\"nilai_total\":\"24000000.00\",\"total_units\":\"2\",\"tanggal_mulai\":\"2025-09-30\",\"tanggal_berakhir\":\"2025-10-31\",\"status\":\"Aktif\"}','{\"no_po_marketing\":null,\"pelanggan\":\"Test\",\"pic\":null,\"kontak\":null,\"lokasi\":null,\"nilai_total\":0,\"total_units\":0,\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\"}','[\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:49:13','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[55],\"spk\":[36]}'),(81,'kontrak',55,'UPDATE','Kontrak updated: SML/DS/121025 (Client: Test)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Pending\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:49:35','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[55],\"spk\":[36]}'),(82,'kontrak',56,'CREATE','Kontrak created: TEST/AUTO/001 (Client: Test Auto Update)',NULL,'{\"no_kontrak\":\"TEST\\/AUTO\\/001\",\"no_po_marketing\":null,\"pelanggan\":\"Test Auto Update\",\"pic\":null,\"kontak\":null,\"lokasi\":null,\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":1}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',NULL,'DRAFT',0,'2025-09-12 09:54:47','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[56]}'),(88,'spk',37,'CREATE','Created new spk record',NULL,'{\"spk_id\":37,\"nomor_spk\":\"SPK\\/202509\\/005\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"40\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-12 10:05:22','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[37]}'),(89,'spk',37,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-12 10:05:28','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(90,'spk',37,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:28\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"123\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 10:05:41\",\"diperbarui_pada\":\"2025-09-12 10:05:41\",\"persiapan_unit_id\":\"7\",\"persiapan_aksesoris_tersedia\":\"[]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-12 10:05:41','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(91,'spk',37,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:41\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 10:05:52\",\"diperbarui_pada\":\"2025-09-12 10:05:52\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-12 10:05:52','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(92,'spk',37,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:52\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 10:05:59\",\"diperbarui_pada\":\"2025-09-12 10:05:59\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-12 10:05:59','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(93,'spk',37,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:59\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-12\",\"pdi_estimasi_selesai\":\"2025-09-12\",\"pdi_tanggal_approve\":\"2025-09-12 10:06:06\",\"diperbarui_pada\":\"2025-09-12 10:06:06\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"7\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"123\\\",\\\"timestamp\\\":\\\"2025-09-12 10:06:06\\\"}]}\",\"pdi_catatan\":\"123\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-12 10:06:06','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(94,'delivery_instruction',126,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":126,\"nomor_di\":\"DI\\/202509\\/005\",\"spk_id\":37,\"po_kontrak_nomor\":\"TEST\\/AUTO\\/001\",\"pelanggan\":\"Test Auto Update\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[7]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-12 10:06:23','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[126]}'),(95,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-12 10:06:32','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(96,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 17:06:32\",\"estimasi_sampai\":null,\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 10:06:43\",\"estimasi_sampai\":\"2025-09-12\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 10:06:43','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(97,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 17:06:43\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 10:06:45\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 10:06:45','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(98,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 17:06:45\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 10:06:48\",\"catatan_sampai\":\"123\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 10:06:48','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(99,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-13 01:06:29','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(100,'kontrak',56,'UPDATE','Kontrak updated: TEST/AUTO/001 (Client: Test Client)','{\"pelanggan\":\"Test Auto Update\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Aktif\"}','{\"pelanggan\":\"Test Client\",\"nilai_total\":0,\"total_units\":0,\"status\":\"Pending\"}','[\"pelanggan\",\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-13 01:20:06','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[56],\"spk\":[37]}'),(101,'kontrak',56,'UPDATE','Kontrak updated: TEST/AUTO/001 (Client: Test Client)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Pending\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-13 01:20:35','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[56],\"spk\":[37]}'),(102,'spk',38,'CREATE','Created new spk record',NULL,'{\"spk_id\":38,\"nomor_spk\":\"SPK\\/202509\\/006\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-13 01:33:11','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[38]}'),(103,'spk',38,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-13 01:33:20','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(104,'spk',38,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:33:20\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 01:46:15\",\"diperbarui_pada\":\"2025-09-13 01:46:15\",\"persiapan_unit_id\":\"5\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 01:46:15','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(105,'spk',38,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:46:15\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 01:46:26\",\"diperbarui_pada\":\"2025-09-13 01:46:26\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 01:46:26','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(106,'spk',38,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:46:26\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 01:46:32\",\"diperbarui_pada\":\"2025-09-13 01:46:32\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 01:46:32','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(107,'spk',38,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:46:32\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-13\",\"pdi_estimasi_selesai\":\"2025-09-13\",\"pdi_tanggal_approve\":\"2025-09-13 01:46:42\",\"diperbarui_pada\":\"2025-09-13 01:46:42\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"5\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"1\\\",\\\"timestamp\\\":\\\"2025-09-13 01:46:42\\\"}]}\",\"pdi_catatan\":\"1\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-13 01:46:42','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(108,'delivery_instruction',127,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":127,\"nomor_di\":\"DI\\/202509\\/006\",\"spk_id\":38,\"po_kontrak_nomor\":\"KNTRK\\/2209\\/0001\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[5]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-13 01:47:49','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[127]}'),(109,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-13 01:48:00','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(110,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 08:48:00\",\"estimasi_sampai\":null,\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 01:48:36\",\"estimasi_sampai\":\"2025-09-13\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 01:48:36','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(111,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 08:48:36\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 01:48:38\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 01:48:38','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(112,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 08:48:38\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 01:48:42\",\"catatan_sampai\":\"qwe\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 01:48:42','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(113,'kontrak',57,'CREATE','Kontrak created: test/1/1/5 (Client: Sarana Mitra Luas)',NULL,'{\"no_kontrak\":\"test\\/1\\/1\\/5\",\"no_po_marketing\":\"12345\",\"pelanggan\":\"Sarana Mitra Luas\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-13\",\"tanggal_berakhir\":\"2025-09-13\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-13 02:56:22','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[57]}'),(114,'spk',39,'CREATE','Created new spk record',NULL,'{\"spk_id\":39,\"nomor_spk\":\"SPK\\/202509\\/007\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"41\",\"jumlah_unit\":2}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-13 02:57:15','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[39]}'),(115,'spk',39,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-13 02:57:22','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(116,'spk',39,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:57:22\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 02:57:44\",\"diperbarui_pada\":\"2025-09-13 02:57:44\",\"persiapan_unit_id\":\"6\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"P3K\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 02:57:44','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(117,'spk',39,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:57:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 02:58:00\",\"diperbarui_pada\":\"2025-09-13 02:58:00\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 02:58:00','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(118,'spk',39,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:00\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 02:58:04\",\"diperbarui_pada\":\"2025-09-13 02:58:04\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 02:58:04','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(119,'spk',39,'UPDATE','Updated spk record','{\"diperbarui_pada\":\"2025-09-13 02:58:04\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"persiapan_unit_id\":\"6\",\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 02:57:44\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"P3K\\\"]\",\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 02:58:00\",\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 02:58:04\"}','{\"diperbarui_pada\":\"2025-09-13 02:58:09\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\",\"pdi_catatan\":\"a\",\"persiapan_unit_id\":null,\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"persiapan_aksesoris_tersedia\":null,\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null}','[\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"persiapan_unit_id\",\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"persiapan_aksesoris_tersedia\",\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\"]',1,'UPDATED',0,'2025-09-13 02:58:09','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(120,'spk',39,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:09\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 02:58:20\",\"diperbarui_pada\":\"2025-09-13 02:58:20\",\"persiapan_unit_id\":\"9\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 02:58:20','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(121,'spk',39,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:20\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 02:58:26\",\"diperbarui_pada\":\"2025-09-13 02:58:26\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"15\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 02:58:26','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(122,'spk',39,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:26\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 02:58:29\",\"diperbarui_pada\":\"2025-09-13 02:58:29\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 02:58:29','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(123,'spk',39,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:29\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"15\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\",\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-13\",\"pdi_estimasi_selesai\":\"2025-09-13\",\"pdi_tanggal_approve\":\"2025-09-13 02:58:34\",\"diperbarui_pada\":\"2025-09-13 02:58:34\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"15\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"},{\\\"unit_id\\\":\\\"9\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"15\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"ACRYLIC\\\\\\\",\\\\\\\"P3K\\\\\\\",\\\\\\\"SAFETY BELT INTERLOC\\\\\\\",\\\\\\\"SPARS ARRESTOR\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:34\\\"}]}\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"status\"]',1,'UPDATED',0,'2025-09-13 02:58:34','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(124,'delivery_instruction',128,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":128,\"nomor_di\":\"DI\\/202509\\/007\",\"spk_id\":39,\"po_kontrak_nomor\":\"test\\/1\\/1\\/5\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[6,9]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-13 02:58:51','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[128]}'),(125,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-13 02:59:24','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(126,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 09:59:24\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 02:59:33\",\"estimasi_sampai\":\"2025-09-13\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 02:59:33','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(127,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 09:59:33\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 02:59:36\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 02:59:36','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(128,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 09:59:36\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 02:59:38\",\"catatan_sampai\":\"a\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 02:59:38','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(129,'spk',40,'CREATE','Created new spk record',NULL,'{\"spk_id\":40,\"nomor_spk\":\"SPK\\/202509\\/008\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"42\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-13 03:35:03','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[40]}'),(130,'spk',40,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-13 03:35:11','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(131,'spk',40,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:35:11\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 03:35:25\",\"diperbarui_pada\":\"2025-09-13 03:35:25\",\"persiapan_unit_id\":\"11\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"CAMERA AI\\\",\\\"SPEED LIMITER\\\",\\\"LASER FORK\\\",\\\"HORN KLASON\\\",\\\"APAR 3 KG\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 03:35:25','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(132,'spk',40,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:35:25\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 03:43:16\",\"diperbarui_pada\":\"2025-09-13 03:43:16\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 03:43:16','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(133,'spk',40,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:43:16\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 03:43:21\",\"diperbarui_pada\":\"2025-09-13 03:43:21\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 03:43:21','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(134,'spk',40,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:43:21\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-13\",\"pdi_estimasi_selesai\":\"2025-09-13\",\"pdi_tanggal_approve\":\"2025-09-13 03:43:24\",\"diperbarui_pada\":\"2025-09-13 03:43:24\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"11\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"3\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"CAMERA AI\\\\\\\",\\\\\\\"SPEED LIMITER\\\\\\\",\\\\\\\"LASER FORK\\\\\\\",\\\\\\\"HORN KLASON\\\\\\\",\\\\\\\"APAR 3 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"1\\\",\\\"timestamp\\\":\\\"2025-09-13 03:43:24\\\"}]}\",\"pdi_catatan\":\"1\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-13 03:43:24','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(135,'delivery_instruction',129,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":129,\"nomor_di\":\"DI\\/202509\\/008\",\"spk_id\":40,\"po_kontrak_nomor\":\"test\\/1\\/1\\/5\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[11]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-13 03:43:34','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[129]}'),(136,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-13 03:43:43','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(137,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 10:43:43\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 03:43:52\",\"estimasi_sampai\":\"2025-09-13\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 03:43:52','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(138,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 10:43:52\",\"catatan_berangkat\":null,\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 03:43:55\",\"catatan_berangkat\":\"a\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"catatan_berangkat\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 03:43:55','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(139,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 10:43:55\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 03:43:58\",\"catatan_sampai\":\"a\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 03:43:58','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(140,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-15 07:58:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(141,'kontrak',58,'CREATE','Kontrak created: test/1/1/6 (Client: Sarana Mitra Luas)',NULL,'{\"no_kontrak\":\"test\\/1\\/1\\/6\",\"no_po_marketing\":\"12345\",\"pelanggan\":\"Sarana Mitra Luas\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"HARIAN\",\"tanggal_mulai\":\"2025-09-13\",\"tanggal_berakhir\":\"2025-09-13\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-15 08:06:12','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[58]}'),(142,'spk',41,'CREATE','Created new spk record',NULL,'{\"spk_id\":41,\"nomor_spk\":\"SPK\\/202509\\/009\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"58\",\"kontrak_spesifikasi_id\":\"43\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-15 08:09:31','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[41]}'),(143,'spk',41,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-15 08:09:36','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(144,'spk',41,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:09:36\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-15\",\"persiapan_unit_estimasi_selesai\":\"2025-09-15\",\"persiapan_unit_tanggal_approve\":\"2025-09-15 08:09:51\",\"diperbarui_pada\":\"2025-09-15 08:09:51\",\"persiapan_unit_id\":\"13\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-15 08:09:51','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(145,'spk',41,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:09:51\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"ARIZAL-EKA\",\"fabrikasi_estimasi_mulai\":\"2025-09-15\",\"fabrikasi_estimasi_selesai\":\"2025-09-15\",\"fabrikasi_tanggal_approve\":\"2025-09-15 08:21:59\",\"diperbarui_pada\":\"2025-09-15 08:21:59\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-15 08:21:59','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(146,'spk',41,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:21:59\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-15\",\"painting_estimasi_selesai\":\"2025-09-15\",\"painting_tanggal_approve\":\"2025-09-15 08:22:44\",\"diperbarui_pada\":\"2025-09-15 08:22:44\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-15 08:22:44','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(147,'spk',41,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:22:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"JOHANA - DEPI\",\"pdi_estimasi_mulai\":\"2025-09-15\",\"pdi_estimasi_selesai\":\"2025-09-15\",\"pdi_tanggal_approve\":\"2025-09-15 08:22:53\",\"diperbarui_pada\":\"2025-09-15 08:22:53\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"13\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\"]\\\",\\\"mekanik\\\":\\\"JOHANA - DEPI\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-15 08:22:53\\\"}]}\",\"pdi_catatan\":\"a\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-15 08:22:53','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(148,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-16 01:25:33','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(149,'delivery_instruction',130,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":130,\"nomor_di\":\"DI\\/202509\\/009\",\"spk_id\":null,\"po_kontrak_nomor\":\"TEST\\/AUTO\\/001\",\"pelanggan\":\"Test Client\",\"jenis_perintah_kerja_id\":2,\"tujuan_perintah_kerja_id\":4,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-16 03:10:02','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[130]}'),(150,'spk',42,'CREATE','Created new spk record',NULL,'{\"spk_id\":42,\"nomor_spk\":\"SPK\\/202509\\/010\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"41\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 06:56:53','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[42]}'),(151,'spk',42,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-16 06:57:09','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(152,'spk',42,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:09\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-16\",\"persiapan_unit_estimasi_selesai\":\"2025-09-16\",\"persiapan_unit_tanggal_approve\":\"2025-09-16 06:57:35\",\"diperbarui_pada\":\"2025-09-16 06:57:35\",\"persiapan_unit_id\":\"13\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-16 06:57:35','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(153,'spk',42,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:35\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"IYAN\",\"fabrikasi_estimasi_mulai\":\"2025-09-16\",\"fabrikasi_estimasi_selesai\":\"2025-09-16\",\"fabrikasi_tanggal_approve\":\"2025-09-16 06:57:44\",\"diperbarui_pada\":\"2025-09-16 06:57:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-16 06:57:44','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(154,'spk',42,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:44\"}','{\"painting_mekanik\":\"IYAN\",\"painting_estimasi_mulai\":\"2025-09-16\",\"painting_estimasi_selesai\":\"2025-09-16\",\"painting_tanggal_approve\":\"2025-09-16 06:57:50\",\"diperbarui_pada\":\"2025-09-16 06:57:50\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-16 06:57:50','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(155,'spk',42,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:50\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"IYAN\",\"pdi_estimasi_mulai\":\"2025-09-16\",\"pdi_estimasi_selesai\":\"2025-09-16\",\"pdi_tanggal_approve\":\"2025-09-16 06:57:54\",\"diperbarui_pada\":\"2025-09-16 06:57:54\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"13\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"ACRYLIC\\\\\\\",\\\\\\\"P3K\\\\\\\",\\\\\\\"SAFETY BELT INTERLOC\\\\\\\",\\\\\\\"SPARS ARRESTOR\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-16 06:57:54\\\"}]}\",\"pdi_catatan\":\"a\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-16 06:57:54','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(156,'spk',43,'CREATE','Created new spk record',NULL,'{\"spk_id\":43,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:15:28','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[43]}'),(157,'spk',43,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-16 08:15:35','SERVICE','Service Management','MEDIUM','{\"spk\":[43]}'),(158,'spk',44,'CREATE','Created new spk record',NULL,'{\"spk_id\":44,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:16:37','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[44]}'),(159,'spk',45,'CREATE','Created new spk record',NULL,'{\"spk_id\":45,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:26:45','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[45]}'),(160,'spk',46,'CREATE','Created new spk record',NULL,'{\"spk_id\":46,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:27:38','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[46]}'),(161,'spk',47,'CREATE','Created new spk record',NULL,'{\"spk_id\":47,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:37:20','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[47]}'),(162,'spk',48,'CREATE','Created new spk record',NULL,'{\"spk_id\":48,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"ATTACHMENT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:37:45','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[48]}'),(163,'spk',49,'CREATE','Created new spk record',NULL,'{\"spk_id\":49,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"ATTACHMENT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:43:46','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[49]}'),(164,'spk',49,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-16 08:43:55','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(165,'spk',49,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 08:43:55\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"IYAN\",\"fabrikasi_estimasi_mulai\":\"2025-09-16\",\"fabrikasi_estimasi_selesai\":\"2025-09-16\",\"fabrikasi_tanggal_approve\":\"2025-09-16 09:10:32\",\"diperbarui_pada\":\"2025-09-16 09:10:32\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-16 09:10:32','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(166,'spk',49,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 09:10:32\"}','{\"painting_mekanik\":\"IYAN\",\"painting_estimasi_mulai\":\"2025-09-16\",\"painting_estimasi_selesai\":\"2025-09-16\",\"painting_tanggal_approve\":\"2025-09-16 09:10:35\",\"diperbarui_pada\":\"2025-09-16 09:10:35\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-16 09:10:35','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(167,'spk',49,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 09:10:35\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"IYAN\",\"pdi_estimasi_mulai\":\"2025-09-16\",\"pdi_estimasi_selesai\":\"2025-09-16\",\"pdi_tanggal_approve\":\"2025-09-16 09:10:41\",\"diperbarui_pada\":\"2025-09-16 09:10:41\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":null,\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":null,\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-16 09:10:41\\\"}]}\",\"pdi_catatan\":\"a\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-16 09:10:41','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(168,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-17 01:26:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(169,'delivery_instruction',131,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":131,\"nomor_di\":\"DI\\/202509\\/010\",\"spk_id\":49,\"po_kontrak_nomor\":\"TEST\\/AUTO\\/001\",\"pelanggan\":\"Test Client\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-17 02:54:40','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[131]}'),(170,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-22 01:23:38','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(171,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-22 06:17:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(172,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-22 06:17:50','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(173,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-23 06:17:04','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(174,'delivery_instruction',131,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-23 06:21:02','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[131]}'),(175,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-24 02:00:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(176,'work_orders',5,'CREATE','Work order 15060 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:05:39','SERVICE',NULL,'LOW',NULL),(177,'work_orders',6,'CREATE','Work order 15061 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:06:37','SERVICE',NULL,'LOW',NULL),(178,'work_orders',7,'CREATE','Work order 15062 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:11:17','SERVICE',NULL,'LOW',NULL),(179,'work_orders',8,'CREATE','Work order 15063 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:11:27','SERVICE',NULL,'LOW',NULL),(180,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 01:29:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(181,'work_orders',9,'CREATE','Work order 15064 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 01:37:24','SERVICE',NULL,'LOW',NULL),(182,'work_orders',10,'CREATE','Work order 15065 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 01:40:02','SERVICE',NULL,'LOW',NULL),(183,'work_orders',10,'DELETE','Deleted work_orders record','{\"id\":\"10\",\"work_order_number\":\"15065\",\"report_date\":\"2025-09-25 01:40:02\",\"unit_id\":\"1\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"1\",\"subcategory_id\":null,\"complaint_description\":\"Test validation successful\",\"status_id\":\"1\",\"admin_staff_id\":null,\"foreman_staff_id\":null,\"mechanic_staff_id\":null,\"helper_staff_id\":null,\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":null,\"created_by\":\"1\",\"created_at\":\"2025-09-25 01:40:02\",\"updated_at\":\"2025-09-25 01:40:02\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 01:43:49','SERVICE',NULL,'LOW',NULL),(184,'work_orders',1,'DELETE','Deleted work_orders record','{\"id\":\"1\",\"work_order_number\":\"15059\",\"report_date\":\"2025-09-18 08:52:52\",\"unit_id\":\"1\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":\"2025-09-18 09:00:00\",\"category_id\":\"8\",\"subcategory_id\":\"1\",\"complaint_description\":\"Ban depan belakang gundul\",\"status_id\":\"2\",\"admin_staff_id\":\"1\",\"foreman_staff_id\":\"4\",\"mechanic_staff_id\":\"7\",\"helper_staff_id\":\"13\",\"repair_description\":\"Ban depan belakang gundul\",\"notes\":null,\"sparepart_used\":\"Ban hidup 700-12, ban hidup 600-9\",\"time_to_repair\":null,\"completion_date\":null,\"area\":\"PURWAKARTA\",\"created_by\":\"1\",\"created_at\":\"2025-09-23 15:24:36\",\"updated_at\":\"2025-09-25 08:51:49\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 01:52:30','SERVICE',NULL,'LOW',NULL),(185,'work_orders',11,'CREATE','Work order 15066 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 01:55:12','SERVICE',NULL,'LOW',NULL),(186,'work_orders',12,'CREATE','Work order 15067 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 02:07:46','SERVICE',NULL,'LOW',NULL),(187,'work_orders',12,'DELETE','Deleted work_orders record','{\"id\":\"12\",\"work_order_number\":\"15067\",\"report_date\":\"2025-09-25 02:07:46\",\"unit_id\":\"11\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"7\",\"subcategory_id\":\"121\",\"complaint_description\":\"RUSAK\",\"status_id\":\"1\",\"admin_staff_id\":\"3\",\"foreman_staff_id\":\"4\",\"mechanic_staff_id\":\"7\",\"helper_staff_id\":\"16\",\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":\"BEKASI\",\"created_by\":\"1\",\"created_at\":\"2025-09-25 02:07:46\",\"updated_at\":\"2025-09-25 02:07:46\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 02:12:04','SERVICE',NULL,'LOW',NULL),(188,'work_orders',13,'CREATE','Work order 15068 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 02:15:04','SERVICE',NULL,'LOW',NULL),(189,'work_orders',13,'DELETE','Deleted work_orders record','{\"id\":\"13\",\"work_order_number\":\"15068\",\"report_date\":\"2025-09-25 02:15:04\",\"unit_id\":\"11\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"7\",\"subcategory_id\":\"121\",\"complaint_description\":\"Test delete dari modal\",\"status_id\":\"1\",\"admin_staff_id\":\"3\",\"foreman_staff_id\":\"4\",\"mechanic_staff_id\":\"7\",\"helper_staff_id\":\"16\",\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":\"BEKASI\",\"created_by\":\"1\",\"created_at\":\"2025-09-25 02:15:04\",\"updated_at\":\"2025-09-25 02:15:04\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 02:15:23','SERVICE',NULL,'LOW',NULL),(190,'work_orders',11,'DELETE','Deleted work_orders record','{\"id\":\"11\",\"work_order_number\":\"15066\",\"report_date\":\"2025-09-25 01:55:12\",\"unit_id\":\"1\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"1\",\"subcategory_id\":null,\"complaint_description\":\"Test complaint dari curl untuk debug\",\"status_id\":\"1\",\"admin_staff_id\":null,\"foreman_staff_id\":null,\"mechanic_staff_id\":null,\"helper_staff_id\":null,\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":null,\"created_by\":\"1\",\"created_at\":\"2025-09-25 01:55:12\",\"updated_at\":\"2025-09-25 01:55:12\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 02:19:38','SERVICE',NULL,'LOW',NULL),(191,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-25 07:27:49','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(192,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 07:27:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(193,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 09:20:01','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(194,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 09:30:28','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(195,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 10:04:05','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(196,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-26 01:28:03','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}');
/*!40000 ALTER TABLE `system_activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_activity_log_backup`
--

DROP TABLE IF EXISTS `system_activity_log_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_activity_log_backup` (
  `id` int(11) NOT NULL DEFAULT 0,
  `table_name` varchar(64) NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
  `record_id` int(10) unsigned NOT NULL COMMENT 'ID of the affected record',
  `action_type` enum('CREATE','UPDATE','DELETE','ASSIGN','UNASSIGN','APPROVE','REJECT','COMPLETE','CANCEL') NOT NULL COMMENT 'Type of action performed',
  `action_description` varchar(255) NOT NULL COMMENT 'Brief description of what happened',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Previous values (only changed fields)' CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'New values (only changed fields)' CHECK (json_valid(`new_values`)),
  `affected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'List of fields that were changed' CHECK (json_valid(`affected_fields`)),
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'FK to users.id',
  `session_id` varchar(128) DEFAULT NULL COMMENT 'Session identifier for tracking',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'User IP address',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'Browser/device info (truncated)',
  `request_method` enum('GET','POST','PUT','DELETE','PATCH') DEFAULT NULL COMMENT 'HTTP method used',
  `request_url` varchar(255) DEFAULT NULL COMMENT 'Endpoint that triggered this action',
  `related_kontrak_id` int(10) unsigned DEFAULT NULL COMMENT 'Related kontrak if applicable',
  `related_spk_id` int(10) unsigned DEFAULT NULL COMMENT 'Related SPK if applicable',
  `related_di_id` int(10) unsigned DEFAULT NULL COMMENT 'Related DI if applicable',
  `workflow_stage` varchar(50) DEFAULT NULL COMMENT 'Current business stage',
  `is_critical` tinyint(1) DEFAULT 0 COMMENT 'Mark critical business actions',
  `execution_time_ms` int(10) unsigned DEFAULT NULL COMMENT 'Time taken to execute action (milliseconds)',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT') DEFAULT NULL COMMENT 'Application module where activity occurred',
  `feature_name` varchar(100) DEFAULT NULL COMMENT 'Specific feature/page within module',
  `business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL') DEFAULT 'LOW' COMMENT 'Business impact level',
  `compliance_relevant` tinyint(1) DEFAULT 0 COMMENT 'Relevant for compliance/audit',
  `financial_impact` decimal(15,2) DEFAULT NULL COMMENT 'Financial impact of this activity',
  `related_purchase_order_id` int(10) unsigned DEFAULT NULL COMMENT 'Related PO for purchasing module',
  `related_vendor_id` int(10) unsigned DEFAULT NULL COMMENT 'Related vendor/supplier',
  `related_customer_id` int(10) unsigned DEFAULT NULL COMMENT 'Related customer',
  `related_invoice_id` int(10) unsigned DEFAULT NULL COMMENT 'Related invoice for accounting',
  `related_payment_id` int(10) unsigned DEFAULT NULL COMMENT 'Related payment record',
  `related_permit_id` int(10) unsigned DEFAULT NULL COMMENT 'Related permit for perizinan',
  `related_warehouse_id` int(10) unsigned DEFAULT NULL COMMENT 'Related warehouse location',
  `device_type` enum('DESKTOP','MOBILE','TABLET','API') DEFAULT NULL COMMENT 'Device type used',
  `browser_name` varchar(50) DEFAULT NULL COMMENT 'Browser name',
  `operating_system` varchar(50) DEFAULT NULL COMMENT 'Operating system'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_activity_log_backup`
--

LOCK TABLES `system_activity_log_backup` WRITE;
/*!40000 ALTER TABLE `system_activity_log_backup` DISABLE KEYS */;
INSERT INTO `system_activity_log_backup` VALUES (1,'kontrak',44,'CREATE','Kontrak baru dibuat dengan nomor PO-CL-0488',NULL,'{\"no_po_marketing\": \"PO-CL-0488\", \"pelanggan\": \"PT Client\", \"status\": \"ACTIVE\"}','[\"no_po_marketing\", \"pelanggan\", \"status\"]',1,NULL,NULL,NULL,NULL,NULL,44,NULL,NULL,'KONTRAK',1,NULL,'2025-09-08 06:43:05',NULL,NULL,'LOW',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'inventory_unit',1,'ASSIGN','Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan',NULL,'{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}','[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]',1,NULL,NULL,NULL,NULL,NULL,44,NULL,NULL,'KONTRAK',1,NULL,'2025-09-08 06:43:05',NULL,NULL,'LOW',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'inventory_unit',2,'ASSIGN','Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan',NULL,'{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}','[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]',1,NULL,NULL,NULL,NULL,NULL,44,NULL,NULL,'KONTRAK',1,NULL,'2025-09-08 06:43:05',NULL,NULL,'LOW',0,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `system_activity_log_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_activity_log_old`
--

DROP TABLE IF EXISTS `system_activity_log_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `system_activity_log_old` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL COMMENT 'Username yang melakukan aktivitas',
  `user_id` int(10) unsigned DEFAULT NULL COMMENT 'FK ke users table',
  `action_type` enum('CREATE','READ','UPDATE','DELETE','PRINT','DOWNLOAD','LOGIN','LOGOUT') NOT NULL COMMENT 'Jenis aktivitas',
  `table_name` varchar(64) DEFAULT NULL COMMENT 'Nama tabel yang diakses (kontrak, spk, inventory, dll)',
  `record_id` int(10) unsigned DEFAULT NULL COMMENT 'ID record yang diakses',
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
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_activity_log_old`
--

LOCK TABLES `system_activity_log_old` WRITE;
/*!40000 ALTER TABLE `system_activity_log_old` DISABLE KEYS */;
INSERT INTO `system_activity_log_old` VALUES (1,'admin',1,'CREATE','kontrak',1,'Membuat kontrak baru PO-TEST-001',NULL,NULL,'Marketing','127.0.0.1',NULL,'2025-09-08 08:18:56'),(2,'admin',1,'PRINT','kontrak',1,'Print kontrak PO-TEST-001 ke PDF',NULL,NULL,'Marketing','127.0.0.1',NULL,'2025-09-08 08:18:56'),(3,'admin',1,'DOWNLOAD',NULL,NULL,'Download laporan Excel kontrak bulanan',NULL,NULL,'Reports','127.0.0.1',NULL,'2025-09-08 08:18:56');
/*!40000 ALTER TABLE `system_activity_log_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipe_ban`
--

DROP TABLE IF EXISTS `tipe_ban`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipe_ban` (
  `id_ban` int(11) NOT NULL AUTO_INCREMENT,
  `tipe_ban` varchar(100) NOT NULL,
  PRIMARY KEY (`id_ban`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipe_ban`
--

LOCK TABLES `tipe_ban` WRITE;
/*!40000 ALTER TABLE `tipe_ban` DISABLE KEYS */;
INSERT INTO `tipe_ban` VALUES (1,'Solid (Ban Mati)'),(2,'Pneumatic (Ban Angin)'),(3,'Cushion (Ban Bantal)'),(4,'Non-Marking (Ban Anti-Jejak)'),(5,'Polyurethane (Ban PU)'),(6,'Foam-Filled (Ban Isi Busa)');
/*!40000 ALTER TABLE `tipe_ban` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipe_mast`
--

DROP TABLE IF EXISTS `tipe_mast`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipe_mast` (
  `id_mast` int(11) NOT NULL,
  `tipe_mast` varchar(100) NOT NULL,
  `tinggi_mast` varchar(50) DEFAULT NULL COMMENT 'Contoh: 4500mm atau 4.5m',
  PRIMARY KEY (`id_mast`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipe_mast`
--

LOCK TABLES `tipe_mast` WRITE;
/*!40000 ALTER TABLE `tipe_mast` DISABLE KEYS */;
INSERT INTO `tipe_mast` VALUES (1,'Duplex (2-stage FFL) - ZM300 DUPLEX',NULL),(2,'Simplex (2-stage mast) - V (3000)',NULL),(3,'Simplex (2-stage mast) - V (5000)',NULL),(4,'Simplex (2-stage mast) - VM400-30K',NULL),(5,'Simplex (2-stage mast) - M300',NULL),(6,'Simplex (2-stage mast) - M370',NULL),(7,'Simplex (2-stage mast) - M400',NULL),(8,'Simplex (2-stage mast) - M450',NULL),(9,'Simplex (2-stage mast) - M500',NULL),(10,'Simplex (2-stage mast) - M600',NULL),(11,'Triplex (3-stage FFL) - FSV(4700)',NULL),(12,'Triplex (3-stage FFL) - FSV(6000)',NULL),(13,'Triplex (3-stage FFL) - FSVE61 (6000)',NULL),(14,'Triplex (3-stage FFL) - ZSM435',NULL),(15,'Triplex (3-stage FFL) - ZSM450',NULL),(16,'Triplex (3-stage FFL) - ZSM470',NULL),(17,'Triplex (3-stage FFL) - ZSM500',NULL),(18,'Triplex (3-stage FFL) - ZSM600',NULL),(19,'Triplex (3-stage FFL) - ZSM675',NULL),(20,'Triplex (3-stage FFL) - ZSM720',NULL),(21,'Triplex (3-stage FFL) - ZSM950',NULL),(22,'Triplex (3-stage FFL) - ZSM1050',NULL),(23,'Duplex (2-stage FFL) - 5M25D47',NULL);
/*!40000 ALTER TABLE `tipe_mast` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipe_unit`
--

DROP TABLE IF EXISTS `tipe_unit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tipe_unit` (
  `id_tipe_unit` int(11) NOT NULL,
  `tipe` varchar(50) NOT NULL,
  `jenis` varchar(50) NOT NULL,
  `id_departemen` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_tipe_unit`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipe_unit`
--

LOCK TABLES `tipe_unit` WRITE;
/*!40000 ALTER TABLE `tipe_unit` DISABLE KEYS */;
INSERT INTO `tipe_unit` VALUES (1,'Alat Berat','COMPACTOR / VIBRO',1),(2,'Alat Berat','DUMP TRUCK',1),(3,'Alat Berat','WHEEL LOADER',1),(4,'Alat Kebersihan','SCRUBER',1),(5,'Alat Kebersihan','SCRUBER',2),(6,'Forklift','COUNTER BALANCE',1),(7,'Forklift','COUNTER BALANCE',2),(8,'Forklift','COUNTER BALANCE',3),(9,'Forklift','HAND PALLET',2),(10,'Forklift','PALLET MOVER',2),(11,'Forklift','PALLET STACKER',2),(12,'Forklift','REACH TRUCK',2),(13,'Forklift','THREE WHEEL',2),(14,'Kendaraan Industri','TOWING',2),(15,'Peralatan Angkat','SCISSOR LIFT',2);
/*!40000 ALTER TABLE `tipe_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tujuan_perintah_kerja`
--

DROP TABLE IF EXISTS `tujuan_perintah_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tujuan_perintah_kerja` (
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
  KEY `idx_tujuan_jenis` (`jenis_perintah_id`),
  CONSTRAINT `tujuan_perintah_kerja_ibfk_1` FOREIGN KEY (`jenis_perintah_id`) REFERENCES `jenis_perintah_kerja` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tujuan_perintah_kerja`
--

LOCK TABLES `tujuan_perintah_kerja` WRITE;
/*!40000 ALTER TABLE `tujuan_perintah_kerja` DISABLE KEYS */;
INSERT INTO `tujuan_perintah_kerja` VALUES (1,1,'ANTAR_BARU','Kontrak Baru','Pengantaran unit untuk kontrak baru',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(2,1,'ANTAR_TAMBAHAN','Unit Tambahan','Pengantaran unit tambahan dari kontrak existing',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(3,1,'ANTAR_PENGGANTI','Unit Pengganti','Pengantaran unit pengganti untuk unit bermasalah',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(4,2,'TARIK_HABIS_KONTRAK','Habis Kontrak','Penarikan unit karena kontrak berakhir',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(5,2,'TARIK_PINDAH_LOKASI','Pindah Lokasi','Penarikan unit untuk dipindah ke lokasi lain',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(6,2,'TARIK_MAINTENANCE','Maintenance','Penarikan unit untuk perawatan/perbaikan',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(7,2,'TARIK_RUSAK','Unit Rusak','Penarikan unit karena mengalami kerusakan',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(8,3,'TUKAR_UPGRADE','Upgrade Unit','Penukaran dengan unit yang lebih tinggi spesifikasinya',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(9,3,'TUKAR_DOWNGRADE','Downgrade Unit','Penukaran dengan unit yang lebih rendah spesifikasinya',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(10,3,'TUKAR_RUSAK','Ganti Unit Rusak','Penukaran unit yang mengalami kerusakan',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(11,3,'TUKAR_MAINTENANCE','Ganti Saat Maintenance','Penukaran sementara selama unit di maintenance',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(12,4,'RELOKASI_INTERNAL','Antar Lokasi Client','Pemindahan unit antar lokasi dalam satu perusahaan',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(13,4,'RELOKASI_OPTIMASI','Optimasi Distribusi','Pemindahan unit untuk optimasi distribusi',1,'2025-09-03 08:58:54','2025-09-03 08:58:54'),(14,4,'RELOKASI_EMERGENCY','Kebutuhan Mendadak','Pemindahan unit untuk kebutuhan mendadak',1,'2025-09-03 08:58:54','2025-09-03 08:58:54');
/*!40000 ALTER TABLE `tujuan_perintah_kerja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_replacement_log`
--

DROP TABLE IF EXISTS `unit_replacement_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unit_replacement_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `di_id` int(11) NOT NULL,
  `old_unit_id` int(10) unsigned NOT NULL,
  `new_unit_id` int(10) unsigned NOT NULL,
  `kontrak_id` int(11) NOT NULL,
  `stage` varchar(50) NOT NULL,
  `replacement_date` datetime NOT NULL DEFAULT current_timestamp(),
  `replaced_by` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_replacement_di` (`di_id`),
  KEY `idx_replacement_kontrak` (`kontrak_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_replacement_log`
--

LOCK TABLES `unit_replacement_log` WRITE;
/*!40000 ALTER TABLE `unit_replacement_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `unit_replacement_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_workflow_log`
--

DROP TABLE IF EXISTS `unit_workflow_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unit_workflow_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `unit_id` int(10) unsigned NOT NULL,
  `di_id` int(11) NOT NULL,
  `stage` varchar(50) NOT NULL,
  `jenis_perintah` varchar(20) NOT NULL,
  `old_status` varchar(50) DEFAULT NULL,
  `new_status` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unit_workflow_unit` (`unit_id`),
  KEY `idx_unit_workflow_di` (`di_id`),
  KEY `idx_unit_workflow_stage` (`stage`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_workflow_log`
--

LOCK TABLES `unit_workflow_log` WRITE;
/*!40000 ALTER TABLE `unit_workflow_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `unit_workflow_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `unit_workflow_status`
--

DROP TABLE IF EXISTS `unit_workflow_status`;
