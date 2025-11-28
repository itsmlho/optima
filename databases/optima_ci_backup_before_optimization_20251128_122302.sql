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
-- Table structure for table `activity_types`
--

DROP TABLE IF EXISTS `activity_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activity_types` (
  `id` int NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `type_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `type_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `business_impact_default` enum('LOW','MEDIUM','HIGH','CRITICAL') COLLATE utf8mb4_general_ci DEFAULT 'LOW',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_area_staff_assignment` (`area_id`,`employee_id`,`assignment_type`),
  KEY `idx_area_staff` (`area_id`,`employee_id`),
  KEY `idx_staff_area` (`employee_id`,`area_id`),
  KEY `idx_assignment_active` (`is_active`),
  CONSTRAINT `fk_area_staff_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_area_staff_staff` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Staff assignments per area';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `area_employee_assignments`
--

LOCK TABLES `area_employee_assignments` WRITE;
/*!40000 ALTER TABLE `area_employee_assignments` DISABLE KEYS */;
INSERT INTO `area_employee_assignments` VALUES (4,2,1,'PRIMARY','2025-09-25',NULL,0,NULL,'2025-09-25 03:58:28','2025-10-01 23:31:04','2025-10-02 06:31:04'),(5,2,2,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28',NULL),(7,3,1,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28',NULL),(8,3,2,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28',NULL),(9,3,3,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28',NULL),(10,4,1,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28',NULL),(11,4,2,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28',NULL),(12,4,3,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:28','2025-09-25 03:58:28',NULL),(16,4,4,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42',NULL),(18,2,6,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42',NULL),(19,3,7,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42',NULL),(20,4,8,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42',NULL),(22,2,10,'PRIMARY','2025-09-25',NULL,1,NULL,'2025-09-25 03:58:42','2025-09-25 03:58:42',NULL),(30,2,9,'PRIMARY','2025-10-01',NULL,1,'','2025-10-01 08:29:19','2025-10-01 08:29:19',NULL),(31,2,14,'PRIMARY','2025-10-01',NULL,1,'','2025-10-01 08:31:43','2025-10-01 08:31:43',NULL),(32,2,13,'BACKUP','2025-10-01',NULL,1,'','2025-10-01 08:37:41','2025-10-01 23:52:35','2025-10-02 06:52:35'),(33,18,12,'PRIMARY','2025-10-02',NULL,1,'','2025-10-01 19:19:43','2025-10-01 19:19:43',NULL),(34,18,9,'PRIMARY','2025-10-02',NULL,1,'','2025-10-01 19:19:58','2025-10-01 19:19:58',NULL),(35,6,7,'PRIMARY','2025-10-02',NULL,1,'','2025-10-01 19:20:33','2025-10-01 19:20:33',NULL),(36,6,11,'PRIMARY','2025-10-02',NULL,1,'','2025-10-01 19:20:44','2025-10-01 19:20:44',NULL),(37,6,16,'PRIMARY','2025-10-02',NULL,1,'','2025-10-01 19:20:53','2025-10-01 19:20:53',NULL),(38,2,12,'BACKUP','2025-10-02',NULL,1,'','2025-10-01 19:44:29','2025-10-01 19:44:29',NULL),(39,6,10,'BACKUP','2025-10-02',NULL,1,'','2025-10-01 19:44:49','2025-10-01 19:44:49',NULL),(40,22,16,'BACKUP','2025-10-02',NULL,1,'','2025-10-01 20:01:54','2025-10-01 20:01:54',NULL),(41,2,7,'PRIMARY','2025-10-02',NULL,1,'','2025-10-02 01:57:00','2025-10-02 01:57:00',NULL),(42,2,8,'PRIMARY','2025-10-02',NULL,1,'','2025-10-02 01:57:09','2025-10-02 01:57:09',NULL),(43,2,11,'PRIMARY','2025-10-02',NULL,1,'','2025-10-02 01:57:17','2025-10-02 01:57:17',NULL),(44,4,15,'PRIMARY','2025-10-03',NULL,1,'','2025-10-03 01:00:40','2025-10-03 01:00:40',NULL);
/*!40000 ALTER TABLE `area_employee_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `areas`
--

DROP TABLE IF EXISTS `areas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `areas` (
  `id` int NOT NULL AUTO_INCREMENT,
  `area_code` varchar(10) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'A, B, C, etc',
  `area_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Jakarta Utara, Bekasi, Cikarang, etc',
  `area_description` text COLLATE utf8mb4_general_ci COMMENT 'Detail coverage wilayah',
  `departemen_id` int DEFAULT NULL,
  `area_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'GPS coordinates untuk mapping',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_code` (`area_code`),
  KEY `idx_area_code` (`area_code`),
  KEY `idx_area_active` (`is_active`),
  KEY `idx_areas_departemen` (`departemen_id`),
  CONSTRAINT `areas_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`),
  CONSTRAINT `fk_areas_departemen` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `areas_chk_1` CHECK (json_valid(`area_coordinates`))
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master Areas/Wilayah';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `areas`
--

LOCK TABLES `areas` WRITE;
/*!40000 ALTER TABLE `areas` DISABLE KEYS */;
INSERT INTO `areas` VALUES (2,'B','Bekasi','Meliputi wilayah Bekasi, Cikarang, dan sekitarnya',1,NULL,1,'2025-09-25 03:49:57','2025-10-02 08:10:41',NULL),(3,'C','Tangerang','Meliputi wilayah Tangerang dan sekitarnya',NULL,NULL,1,'2025-09-25 03:49:57','2025-09-25 03:49:57',NULL),(4,'D','Jakarta Selatan','Meliputi wilayah Jakarta Selatan dan sekitarnya',3,NULL,1,'2025-09-25 03:49:57','2025-11-21 06:20:55',NULL),(6,'D-TGR','TANGERANG','Area Tangerang untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(7,'D-KRW','KARAWANG','Area Karawang untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(8,'D-JAT','JATENG','Area Jawa Tengah untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(9,'D-JIM','JATIM','Area Jawa Timur untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(10,'D-LK','LUAR KOTA','Area Luar Kota untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(11,'D-BLR','BALARAJAAA','Area Balaraja untuk Departemen DIESEL',1,NULL,0,'2025-09-27 02:33:59','2025-10-01 21:44:17',NULL),(12,'D-CKR','CIKARANG','Area Cikarang untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(13,'D-CKP','CIKUPA','Area Cikupa untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(14,'D-JKB','JAKARTA - BOGOR','Area Jakarta - Bogor untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(15,'D-JWL','JAWILAN','Area Jawilan untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(16,'D-SWG','SAWANGAN','Area Sawangan untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(17,'D-SRG','SERANG','Area Serang untuk Departemen DIESEL',1,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(18,'E-TGR','TANGERANG','Area Tangerang untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(19,'E-KRW','KARAWANG','Area Karawang untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(20,'E-JAT','JATENG','Area Jawa Tengah untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(21,'E-JIM','JATIM','Area Jawa Timur untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(22,'E-LK','LUAR KOTA','Area Luar Kota untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(23,'E-SBW','STANDBY BARAT','Area Standby Barat untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(24,'E-SBT','STANDBY TIMUR','Area Standby Timur untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(25,'E-BTN','BANTEN','Area Banten untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(26,'E-MJL','MAJALENGKA','Area Majalengka untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(27,'E-PLG','PALEMBANG','Area Palembang untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(28,'E-PRW','PERAWANG','Area Perawang untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(29,'E-PSB','PUSAT BARAT','Area Pusat Barat untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(30,'E-PSC','PUSAT CIKARANG','Area Pusat Cikarang untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(31,'E-PST','PUSAT TIMUR','Area Pusat Timur untuk Departemen ELECTRIC',2,NULL,1,'2025-09-27 02:33:59','2025-09-27 02:33:59',NULL),(32,'D-BLRA','BALARAJAA','test',NULL,NULL,1,'2025-10-01 21:49:04','2025-10-02 00:27:18',NULL);
/*!40000 ALTER TABLE `areas` ENABLE KEYS */;
UNLOCK TABLES;

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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `charger` (
  `id_charger` int NOT NULL AUTO_INCREMENT,
  `merk_charger` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_charger` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contract_disconnection_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kontrak_id` int NOT NULL,
  `unit_id` int unsigned NOT NULL,
  `stage` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `reason` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `disconnected_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `disconnected_by` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `idx_disconnect_kontrak` (`kontrak_id`),
  KEY `idx_disconnect_unit` (`unit_id`),
  KEY `idx_contract_disconnect_kontrak` (`kontrak_id`)
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
-- Table structure for table `customer_contracts`
--

DROP TABLE IF EXISTS `customer_contracts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_contracts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_id` int NOT NULL,
  `kontrak_id` int unsigned NOT NULL COMMENT 'FK to existing kontrak table',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
INSERT INTO `customer_contracts` VALUES (1,2,44,1,'2025-09-25 03:56:50','2025-09-25 03:56:50'),(3,4,55,1,'2025-09-25 03:56:50','2025-09-25 03:56:50'),(4,5,56,1,'2025-09-25 03:56:50','2025-09-25 03:56:50');
/*!40000 ALTER TABLE `customer_contracts` ENABLE KEYS */;
UNLOCK TABLES;

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
  `location_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Kantor Pusat, Pabrik 1, Gudang A, etc',
  `location_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location_type` enum('HEAD_OFFICE','BRANCH','WAREHOUSE','FACTORY') COLLATE utf8mb4_general_ci DEFAULT 'BRANCH',
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `contact_person` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pic_position` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0' COMMENT 'Primary location for this customer',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_customer_id` (`customer_id`),
  KEY `idx_location_active` (`is_active`),
  CONSTRAINT `fk_customer_locations_customer` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Multiple locations per customer';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_locations`
--

LOCK TABLES `customer_locations` WRITE;
/*!40000 ALTER TABLE `customer_locations` DISABLE KEYS */;
INSERT INTO `customer_locations` VALUES (1,2,8,'Lokasi Utama','LOC-20251023-01','BRANCH','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-10-24 06:57:51'),(3,4,4,'Lokasi Utama','LOC-20251023-03','BRANCH','akasdmasbhawdawdasdawdaw','itsmils','082136033596','itsupport@sml.co.id',NULL,'','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-09-25 03:56:17','2025-10-24 06:57:51'),(4,5,4,'Lokasi Utama','LOC-20251023-04','BRANCH','Alamat belum tersedia',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-10-24 06:57:51'),(10,4,7,'Gudang Perawang','LOC-20251023-43','BRANCH','Perawang','Januari','092131231231','root@localhost','SPV','Test direct DB update','Perawang','SUMUT','',NULL,NULL,0,1,'2025-09-25 20:52:47','2025-10-24 07:12:04'),(12,2,8,'Gudang Perawang','LOC-20251023-02','BRANCH','Perawang','Januari','092131231231','sml.mkt23@gmail.com','SPV','a','Perawang','SUMUT','17320',NULL,NULL,0,1,'2025-09-25 21:37:12','2025-10-24 06:57:51'),(13,8,6,'Head Office','LOC-20251023-13','BRANCH','Test Address','Test Person','08123456789','test@example.com',NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-26 20:48:59','2025-10-24 06:57:51'),(14,9,30,'Head Office','LOC-20251023-14','BRANCH','Jalan jalan cikarang jaya raya','Joko','+6282138812312','itsupport@sml.co.id','SPV','','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-09-26 20:52:02','2025-10-24 06:57:51'),(15,10,8,'Head Office','LOC-20251023-15','BRANCH','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT','itsmils','082136033596','itsupport@sml.co.id','','awdaw','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-10-22 02:18:56','2025-10-24 06:57:51'),(16,11,12,'WORKSHOP 1','LOC-20251023-16','BRANCH','Jalan jalan cikarang jaya raya','itsmils','082136033596','itsupport@sml.co.id','','awdaw','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-10-22 02:23:15','2025-10-24 06:57:51'),(17,12,14,'Head Office','LOC-20251023-17','BRANCH','PERMATA NUSA INDAH BLOK A 1IUNAWUDA','itsmils','082136033596','itsupport@sml.co.id','','','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-10-22 02:30:29','2025-10-24 06:57:51');
/*!40000 ALTER TABLE `customer_locations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customer_locations_backup`
--

DROP TABLE IF EXISTS `customer_locations_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customer_locations_backup` (
  `id` int NOT NULL DEFAULT '0',
  `customer_id` int NOT NULL,
  `area_id` int DEFAULT NULL,
  `location_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Kantor Pusat, Pabrik 1, Gudang A, etc',
  `location_code` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `location_type` enum('HEAD_OFFICE','BRANCH','WAREHOUSE','FACTORY') COLLATE utf8mb4_general_ci DEFAULT 'BRANCH',
  `address` text COLLATE utf8mb4_general_ci NOT NULL,
  `contact_person` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(32) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pic_position` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `city` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `postal_code` varchar(10) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gps_latitude` decimal(10,8) DEFAULT NULL,
  `gps_longitude` decimal(11,8) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0' COMMENT 'Primary location for this customer',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customer_locations_backup`
--

LOCK TABLES `customer_locations_backup` WRITE;
/*!40000 ALTER TABLE `customer_locations_backup` DISABLE KEYS */;
INSERT INTO `customer_locations_backup` VALUES (1,2,NULL,'Lokasi Utama','LOC-20251023-01','BRANCH','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-10-23 01:49:36'),(3,4,NULL,'Lokasi Utama','LOC-20251023-03','BRANCH','akasdmasbhawdawdasdawdaw','itsmils','082136033596','itsupport@sml.co.id',NULL,'','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-09-25 03:56:17','2025-10-23 01:56:02'),(4,5,NULL,'Lokasi Utama','LOC-20251023-04','BRANCH','Alamat belum tersedia',NULL,NULL,NULL,NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-25 03:56:17','2025-10-23 01:58:39'),(10,4,NULL,'Gudang Perawang','LOC-20251023-43','BRANCH','Perawang','Januari','092131231231','root@localhost','SPV','Test direct DB update','Perawang','SUMUT','',NULL,NULL,0,1,'2025-09-25 20:52:47','2025-10-23 02:07:22'),(12,2,NULL,'Gudang Perawang','LOC-20251023-02','BRANCH','Perawang','Januari','092131231231','sml.mkt23@gmail.com','SPV','a','Perawang','SUMUT','17320',NULL,NULL,0,1,'2025-09-25 21:37:12','2025-10-23 01:49:36'),(13,8,NULL,'Head Office','LOC-20251023-13','BRANCH','Test Address','Test Person','08123456789','test@example.com',NULL,NULL,'Jakarta','DKI Jakarta',NULL,NULL,NULL,1,1,'2025-09-26 20:48:59','2025-10-23 01:58:39'),(14,9,NULL,'Head Office','LOC-20251023-14','BRANCH','Jalan jalan cikarang jaya raya','Joko','+6282138812312','itsupport@sml.co.id','SPV','','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-09-26 20:52:02','2025-10-23 01:58:39'),(15,10,NULL,'Head Office','LOC-20251023-15','BRANCH','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT','itsmils','082136033596','itsupport@sml.co.id','','awdaw','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-10-22 02:18:56','2025-10-23 01:58:39'),(16,11,NULL,'WORKSHOP 1','LOC-20251023-16','BRANCH','Jalan jalan cikarang jaya raya','itsmils','082136033596','itsupport@sml.co.id','','awdaw','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-10-22 02:23:15','2025-10-23 01:58:39'),(17,12,NULL,'Head Office','LOC-20251023-17','BRANCH','PERMATA NUSA INDAH BLOK A 1IUNAWUDA','itsmils','082136033596','itsupport@sml.co.id','','','Jakarta','DKI Jakarta','17320',NULL,NULL,1,1,'2025-10-22 02:30:29','2025-10-23 01:58:39');
/*!40000 ALTER TABLE `customer_locations_backup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `customers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'SML001, ABC002, etc',
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Sarana Mitra Luas, PT ABC, etc',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customer_code` (`customer_code`),
  UNIQUE KEY `uk_customer_code` (`customer_code`),
  KEY `idx_customer_active` (`is_active`),
  KEY `idx_customer_name` (`customer_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master Customers/PT Client';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `customers`
--

LOCK TABLES `customers` WRITE;
/*!40000 ALTER TABLE `customers` DISABLE KEYS */;
INSERT INTO `customers` VALUES (2,'CUST044','Sarana Mitra Luas',1,'2025-09-25 03:55:54','2025-09-27 03:06:44'),(4,'CUST055','Test',1,'2025-09-25 03:55:54','2025-09-25 03:55:54'),(5,'CUST056','Test Client',1,'2025-09-25 03:55:54','2025-09-25 03:55:54'),(8,'TEST002','Test Company',1,'2025-09-26 20:48:59','2025-09-26 20:48:59'),(9,'LG-09921','PT LG Indonesia',1,'2025-09-26 20:52:02','2025-09-26 20:52:02'),(10,'CUST-20251022-059','PT JASA MANA LAGI',1,'2025-10-22 02:18:56','2025-10-22 02:18:56'),(11,'CUST-20251022-583','PT JASA ADA AJA LAGI',1,'2025-10-22 02:23:15','2025-10-22 02:23:15'),(12,'CUST-20251022-049','PT KORUMA ADAJASA',1,'2025-10-22 02:30:29','2025-10-22 02:30:29');
/*!40000 ALTER TABLE `customers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_instructions`
--

DROP TABLE IF EXISTS `delivery_instructions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_instructions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nomor_di` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `spk_id` int unsigned DEFAULT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT') COLLATE utf8mb4_general_ci DEFAULT 'UNIT',
  `po_kontrak_nomor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `jenis_perintah_kerja_id` int DEFAULT NULL,
  `tujuan_perintah_kerja_id` int DEFAULT NULL,
  `status_eksekusi_workflow_id` int DEFAULT '1',
  `dibuat_oleh` int unsigned DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `perencanaan_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval perencanaan pengiriman',
  `estimasi_sampai` date DEFAULT NULL COMMENT 'Estimasi tanggal sampai dari perencanaan',
  `nama_supir` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nama supir yang bertugas',
  `no_hp_supir` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nomor HP supir',
  `no_sim_supir` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nomor SIM supir',
  `kendaraan` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Jenis/merk kendaraan yang digunakan',
  `no_polisi_kendaraan` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nomor polisi kendaraan',
  `berangkat_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval berangkat',
  `catatan_berangkat` text COLLATE utf8mb4_general_ci COMMENT 'Catatan keberangkatan dan kondisi barang',
  `sampai_tanggal_approve` date DEFAULT NULL COMMENT 'Tanggal approval sampai',
  `catatan_sampai` text COLLATE utf8mb4_general_ci COMMENT 'Catatan kedatangan dan konfirmasi penerima',
  `status_di` enum('DIAJUKAN','DISETUJUI','PERSIAPAN_UNIT','SIAP_KIRIM','DALAM_PERJALANAN','SAMPAI_LOKASI','SELESAI','DIBATALKAN') COLLATE utf8mb4_general_ci DEFAULT 'DIAJUKAN',
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
) ENGINE=InnoDB AUTO_INCREMENT=144 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_instructions`
--

LOCK TABLES `delivery_instructions` WRITE;
/*!40000 ALTER TABLE `delivery_instructions` DISABLE KEYS */;
INSERT INTO `delivery_instructions` VALUES (100,'DI/202509/TEST001',27,'UNIT','PO-TEST-001','PT Test Customer','Jakarta',NULL,NULL,1,1,1,1,'2025-09-03 16:52:00','2025-09-17 10:41:42',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DIAJUKAN'),(122,'DI/202509/001',27,'UNIT','test12345','MONORKOBO','BEKASI','2025-09-04',NULL,1,1,1,1,'2025-09-04 03:43:23','2025-09-17 10:41:42','2025-09-04','2025-09-04','JOKO','082138848123','1231012','colt diesel','123',NULL,NULL,NULL,NULL,'SIAP_KIRIM'),(123,'DI/202509/002',28,'UNIT','MSI','MSI','EROPA','2025-09-04','a',1,1,1,1,'2025-09-04 04:14:52','2025-09-17 10:41:42','2025-09-04','2025-09-04','JOKO','082138848123','1231012','colt diesel (123)','123','2025-09-04',NULL,'2025-09-04','ok','SELESAI'),(124,'DI/202509/003',29,'UNIT','KNTRK/2209/0001','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-12','DIKIRIM',1,1,1,1,'2025-09-09 10:07:55','2025-09-17 10:41:42','2025-09-12','2025-09-12','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-12',NULL,'2025-09-12','sudah sampai','SELESAI'),(125,'DI/202509/004',36,'UNIT','SML/DS/121025','LG','Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190','2025-09-12',NULL,1,1,1,1,'2025-09-12 06:51:13','2025-09-17 10:41:42','2025-09-12','2025-09-12','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-12',NULL,'2025-09-12','ok','SELESAI'),(126,'DI/202509/005',37,'UNIT','TEST/AUTO/001','Test Auto Update','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-12',NULL,1,1,1,1,'2025-09-12 10:06:23','2025-09-17 10:41:42','2025-09-12','2025-09-12','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-12',NULL,'2025-09-12','123','SELESAI'),(127,'DI/202509/006',38,'UNIT','KNTRK/2209/0001','Sarana Mitra Luas',NULL,'2025-09-13',NULL,1,1,1,1,'2025-09-13 01:47:49','2025-09-17 10:41:42','2025-09-13','2025-09-13','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-13',NULL,'2025-09-13','qwe','SELESAI'),(128,'DI/202509/007',39,'UNIT','test/1/1/5','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13',NULL,1,1,1,1,'2025-09-13 02:58:51','2025-09-17 10:41:42','2025-09-13','2025-09-13','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-13',NULL,'2025-09-13','a','SELESAI'),(129,'DI/202509/008',40,'UNIT','test/1/1/5','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13',NULL,1,1,1,1,'2025-09-13 03:43:34','2025-09-17 10:41:42','2025-09-13','2025-09-13','UDIN','082138881231','8992381','TRUK','B 8213 JKT','2025-09-13','a','2025-09-13','a','SELESAI'),(131,'DI/202509/010',49,'ATTACHMENT','TEST/AUTO/001','Test Client',NULL,'2025-09-17',NULL,1,1,1,1,'2025-09-17 02:54:40','2025-09-23 13:21:02',NULL,NULL,'','-','-','','-',NULL,NULL,NULL,NULL,'SIAP_KIRIM'),(132,'DI/202509/011',50,'UNIT','KNTRK/2209/0002','Sarana Mitra Luas','Gudang Perawang','2025-09-26','GAS',1,2,1,1,'2025-09-26 07:57:51','2025-09-26 14:58:37','2025-09-26','2025-09-26','IQBAL','082138881231','8992381','colt diesel','B 8213 JKT','2025-09-26','oke','2025-09-26','oke','SELESAI'),(140,'DI/202509/012',54,'UNIT','LG-9812310','PT LG Indonesia','Head Office','2025-09-30',NULL,1,1,1,1,'2025-09-30 04:38:01','2025-09-30 11:38:01',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DIAJUKAN'),(141,'DI/202509/013',54,'UNIT','LG-9812310','PT LG Indonesia','Head Office','2025-09-30',NULL,1,1,1,1,'2025-09-30 05:12:52','2025-09-30 12:12:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DIAJUKAN'),(142,'DI/202509/014',42,'UNIT','test/1/1/5','Sarana Mitra Luas','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-30',NULL,1,1,1,1,'2025-09-30 06:37:04','2025-09-30 13:37:04',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'DIAJUKAN'),(143,'DI/202509/015',56,'UNIT','LG-9812310','PT LG Indonesia','Head Office','2025-09-30',NULL,1,1,1,1,'2025-09-30 10:19:45','2025-11-21 13:21:53','2025-09-30','2025-09-30','UDIN','082138881231','8992381','colt diesel','B 8213 JKT','2025-10-24','AWDAWDADASD','2025-11-21','basanksadawd','SAMPAI_LOKASI');
/*!40000 ALTER TABLE `delivery_instructions` ENABLE KEYS */;
UNLOCK TABLES;
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
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_delivery_instructions_update_unit` AFTER UPDATE ON `delivery_instructions` FOR EACH ROW BEGIN
    
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

--
-- Table structure for table `delivery_items`
--

DROP TABLE IF EXISTS `delivery_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_items` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `di_id` int unsigned NOT NULL,
  `item_type` enum('UNIT','ATTACHMENT') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'UNIT',
  `unit_id` int unsigned DEFAULT NULL,
  `parent_unit_id` int DEFAULT NULL,
  `attachment_id` int unsigned DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_delivery_items_di_id` (`di_id`),
  KEY `idx_delivery_items_type` (`item_type`),
  KEY `idx_delivery_items_unit` (`unit_id`),
  KEY `idx_delivery_items_attachment` (`attachment_id`),
  CONSTRAINT `fk_delivery_items_di` FOREIGN KEY (`di_id`) REFERENCES `delivery_instructions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_delivery_items_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=181 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Items untuk delivery instruction';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_items`
--

LOCK TABLES `delivery_items` WRITE;
/*!40000 ALTER TABLE `delivery_items` DISABLE KEYS */;
INSERT INTO `delivery_items` VALUES (116,122,'UNIT',1,NULL,NULL,NULL,'2025-09-04 03:43:23','2025-09-04 03:43:23'),(117,122,'UNIT',12,NULL,NULL,NULL,'2025-09-04 03:43:23','2025-09-04 03:43:23'),(118,122,'ATTACHMENT',NULL,1,4,'Battery for Unit 1','2025-09-03 20:43:23','2025-09-03 20:43:23'),(119,122,'ATTACHMENT',NULL,1,5,'Charger for Unit 1','2025-09-03 20:43:23','2025-09-03 20:43:23'),(120,122,'ATTACHMENT',NULL,12,5,'Charger for Unit 12','2025-09-03 20:43:23','2025-09-03 20:43:23'),(121,122,'ATTACHMENT',NULL,12,6,'Battery for Unit 12','2025-09-03 20:43:23','2025-09-03 20:43:23'),(122,123,'UNIT',1,NULL,NULL,NULL,'2025-09-04 04:14:52','2025-09-04 04:14:52'),(123,123,'UNIT',2,NULL,NULL,NULL,'2025-09-04 04:14:52','2025-09-04 04:14:52'),(124,123,'ATTACHMENT',NULL,1,4,'Battery for Unit 1','2025-09-03 21:14:52','2025-09-03 21:14:52'),(125,123,'ATTACHMENT',NULL,1,5,'Charger for Unit 1','2025-09-03 21:14:52','2025-09-03 21:14:52'),(126,123,'ATTACHMENT',NULL,2,4,'Battery for Unit 2','2025-09-03 21:14:52','2025-09-03 21:14:52'),(127,123,'ATTACHMENT',NULL,2,5,'Charger for Unit 2','2025-09-03 21:14:52','2025-09-03 21:14:52'),(128,124,'UNIT',12,NULL,NULL,NULL,'2025-09-09 10:07:55','2025-09-09 10:07:55'),(129,124,'ATTACHMENT',NULL,12,5,'Charger for Unit 12','2025-09-09 03:07:55','2025-09-09 03:07:55'),(130,124,'ATTACHMENT',NULL,12,6,'Battery for Unit 12','2025-09-09 03:07:55','2025-09-09 03:07:55'),(131,125,'UNIT',4,NULL,NULL,NULL,'2025-09-12 06:51:13','2025-09-12 06:51:13'),(132,125,'UNIT',10,NULL,NULL,NULL,'2025-09-12 06:51:13','2025-09-12 06:51:13'),(133,125,'ATTACHMENT',NULL,4,2,'Battery for Unit 4','2025-09-11 23:51:13','2025-09-11 23:51:13'),(134,125,'ATTACHMENT',NULL,10,2,'Battery for Unit 10','2025-09-11 23:51:13','2025-09-11 23:51:13'),(135,126,'UNIT',7,NULL,NULL,NULL,'2025-09-12 10:06:23','2025-09-12 10:06:23'),(136,126,'ATTACHMENT',NULL,7,2,'Battery for Unit 7','2025-09-12 03:06:23','2025-09-12 03:06:23'),(137,127,'UNIT',5,NULL,NULL,NULL,'2025-09-13 01:47:49','2025-09-13 01:47:49'),(138,127,'ATTACHMENT',NULL,5,2,'Battery for Unit 5','2025-09-12 18:47:49','2025-09-12 18:47:49'),(139,128,'UNIT',6,NULL,NULL,NULL,'2025-09-13 02:58:51','2025-09-13 02:58:51'),(140,128,'UNIT',9,NULL,NULL,NULL,'2025-09-13 02:58:51','2025-09-13 02:58:51'),(141,128,'ATTACHMENT',NULL,6,2,'Battery for Unit 6','2025-09-12 19:58:51','2025-09-12 19:58:51'),(142,128,'ATTACHMENT',NULL,9,2,'Battery for Unit 9','2025-09-12 19:58:51','2025-09-12 19:58:51'),(143,129,'UNIT',11,NULL,NULL,NULL,'2025-09-13 03:43:34','2025-09-13 03:43:34'),(144,129,'ATTACHMENT',NULL,11,4,'Attachment for Unit 11','2025-09-12 20:43:34','2025-09-12 20:43:34'),(145,129,'ATTACHMENT',NULL,11,3,'Battery for Unit 11','2025-09-12 20:43:34','2025-09-12 20:43:34'),(146,131,'ATTACHMENT',NULL,NULL,16,'Side Shifter - Manual Fix for Testing','2025-09-22 02:27:20','2025-09-22 02:27:20'),(147,132,'UNIT',17,NULL,NULL,NULL,'2025-09-26 07:57:51','2025-09-26 07:57:51'),(148,132,'ATTACHMENT',NULL,17,3,'Attachment for Unit 17','2025-09-26 00:57:51','2025-09-26 00:57:51'),(149,132,'ATTACHMENT',NULL,17,5,'Charger for Unit 17','2025-09-26 00:57:51','2025-09-26 00:57:51'),(167,140,'UNIT',8,NULL,NULL,NULL,'2025-09-30 04:38:01','2025-09-30 04:38:01'),(168,140,'UNIT',14,NULL,NULL,NULL,'2025-09-30 04:38:01','2025-09-30 04:38:01'),(169,140,'ATTACHMENT',NULL,14,3,'Attachment (Approved in SPK Fabrikasi)','2025-09-30 05:10:38','2025-09-30 05:10:38'),(170,141,'UNIT',16,NULL,NULL,NULL,'2025-09-30 05:12:52','2025-09-30 05:12:52'),(171,141,'UNIT',13,NULL,NULL,NULL,'2025-09-30 05:12:52','2025-09-30 05:12:52'),(172,141,'ATTACHMENT',NULL,16,3,'Battery (Approved in SPK Persiapan Unit)','2025-09-29 22:12:52','2025-09-29 22:12:52'),(173,141,'ATTACHMENT',NULL,16,5,'Charger (Approved in SPK Persiapan Unit)','2025-09-29 22:12:52','2025-09-29 22:12:52'),(174,141,'ATTACHMENT',NULL,16,3,'Attachment (Approved in SPK Fabrikasi)','2025-09-29 22:12:52','2025-09-29 22:12:52'),(175,141,'ATTACHMENT',NULL,13,3,'Attachment (Approved in SPK Fabrikasi)','2025-09-29 22:12:52','2025-09-29 22:12:52'),(176,142,'UNIT',13,NULL,NULL,NULL,'2025-09-30 06:37:04','2025-09-30 06:37:04'),(177,143,'UNIT',15,NULL,NULL,NULL,'2025-09-30 10:19:45','2025-09-30 10:19:45'),(178,143,'ATTACHMENT',NULL,15,6,'Battery (Approved in SPK Persiapan Unit)','2025-09-30 03:19:45','2025-09-30 03:19:45'),(179,143,'ATTACHMENT',NULL,15,5,'Charger (Approved in SPK Persiapan Unit)','2025-09-30 03:19:45','2025-09-30 03:19:45'),(180,143,'ATTACHMENT',NULL,15,3,'Attachment (Approved in SPK Fabrikasi)','2025-09-30 03:19:45','2025-09-30 03:19:45');
/*!40000 ALTER TABLE `delivery_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `delivery_workflow_log`
--

DROP TABLE IF EXISTS `delivery_workflow_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `delivery_workflow_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operation_type` enum('COLUMN_ADD','COLUMN_MODIFY','INDEX_ADD','TRIGGER_CREATE') COLLATE utf8mb4_general_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `column_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('SUCCESS','ERROR','SKIPPED') COLLATE utf8mb4_general_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `delivery_workflow_log`
--

LOCK TABLES `delivery_workflow_log` WRITE;
/*!40000 ALTER TABLE `delivery_workflow_log` DISABLE KEYS */;
INSERT INTO `delivery_workflow_log` VALUES (1,'COLUMN_ADD','po_deliveries','driver_name','Added driver_name column','SUCCESS',NULL,'2025-10-10 07:03:30'),(2,'COLUMN_ADD','po_deliveries','driver_phone','Added driver_phone column','SUCCESS',NULL,'2025-10-10 07:03:30'),(3,'COLUMN_ADD','po_deliveries','vehicle_info','Added vehicle_info column','SUCCESS',NULL,'2025-10-10 07:03:30'),(4,'COLUMN_ADD','po_deliveries','vehicle_plate','Added vehicle_plate column','SUCCESS',NULL,'2025-10-10 07:03:30'),(5,'COLUMN_ADD','po_deliveries','serial_numbers','Added serial_numbers JSON column','SUCCESS',NULL,'2025-10-10 07:03:30'),(6,'COLUMN_ADD','po_deliveries','notes','Added notes TEXT column','SUCCESS',NULL,'2025-10-10 07:03:30'),(7,'COLUMN_MODIFY','po_deliveries','status','Modified status enum for new workflow','SUCCESS',NULL,'2025-10-10 07:03:30'),(8,'INDEX_ADD','po_deliveries',NULL,'Added index on status column','SUCCESS',NULL,'2025-10-10 07:03:30'),(9,'INDEX_ADD','po_deliveries',NULL,'Added index on delivery_date column','SUCCESS',NULL,'2025-10-10 07:03:30'),(10,'INDEX_ADD','po_deliveries',NULL,'Added unique index on packing_list_no','SUCCESS',NULL,'2025-10-10 07:03:30'),(11,'TRIGGER_CREATE','po_deliveries',NULL,'Created trigger for auto-update PO status','SUCCESS',NULL,'2025-10-10 07:03:30'),(12,'TRIGGER_CREATE','po_deliveries',NULL,'Created stored procedure for packing list number generation','SUCCESS',NULL,'2025-10-10 07:03:30'),(13,'COLUMN_ADD','po_deliveries',NULL,'Inserted sample data for testing','SUCCESS',NULL,'2025-10-10 07:03:30');
/*!40000 ALTER TABLE `delivery_workflow_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `departemen`
--

DROP TABLE IF EXISTS `departemen`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departemen` (
  `id_departemen` int NOT NULL AUTO_INCREMENT,
  `nama_departemen` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `di_workflow_stages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `di_id` int NOT NULL,
  `stage_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `stage_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('PENDING','IN_PROGRESS','COMPLETED','SKIPPED') COLLATE utf8mb4_general_ci DEFAULT 'PENDING',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `approved_by` int DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
-- Dumping data for table `divisions`
--

LOCK TABLES `divisions` WRITE;
/*!40000 ALTER TABLE `divisions` DISABLE KEYS */;
INSERT INTO `divisions` VALUES (0,'Marketing','MARKETING','Marketing Division',1,'2025-10-16 06:20:19','2025-10-16 06:20:19'),(1,'Service Diesel','SERVICE_DIESEL','Service Diesel Division',1,'2025-10-16 06:43:40','2025-10-16 06:43:40'),(2,'Service Electric','SERVICE_ELECTRIC','Service Electric Division',1,'2025-10-16 06:43:40','2025-10-16 06:43:40'),(3,'Warehouse','WAREHOUSE','Warehouse Division',1,'2025-10-16 06:43:40','2025-10-16 06:43:40'),(4,'HRD','HRD','HRD Division',1,'2025-10-16 06:43:40','2025-10-16 06:43:40'),(5,'Administrator','ADMINISTRATOR','Administrator Division',1,'2025-10-16 06:43:40','2025-10-16 06:43:40'),(6,'Purchasing','PURCHASING','Purchasing Division - Procurement & Vendor Management',1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(7,'IT','IT','IT Division',1,'2025-10-16 07:07:05','2025-10-16 07:07:05'),(8,'Accounting','ACCOUNTING','Accounting Division - Finance & Bookkeeping',1,'2025-08-05 07:01:57','2025-08-05 07:01:57');
/*!40000 ALTER TABLE `divisions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `employees`
--

DROP TABLE IF EXISTS `employees`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `employees` (
  `id` int NOT NULL AUTO_INCREMENT,
  `staff_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'STF001, STF002, etc',
  `staff_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `staff_role` enum('ADMIN','FOREMAN','MECHANIC','HELPER','SUPERVISOR') COLLATE utf8mb4_general_ci NOT NULL,
  `departemen_id` int DEFAULT NULL,
  `phone` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `hire_date` date DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `staff_code` (`staff_code`),
  UNIQUE KEY `uk_staff_code` (`staff_code`),
  KEY `idx_staff_role` (`staff_role`),
  KEY `idx_staff_active` (`is_active`),
  KEY `idx_staff_departemen` (`departemen_id`),
  KEY `idx_staff_role_departemen` (`staff_role`,`departemen_id`),
  CONSTRAINT `employees_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Master Staff/Karyawan';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `employees`
--

LOCK TABLES `employees` WRITE;
/*!40000 ALTER TABLE `employees` DISABLE KEYS */;
INSERT INTO `employees` VALUES (1,'STF001','Novi','ADMIN',2,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(2,'STF002','Sari','ADMIN',2,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(3,'STF003','Andi','ADMIN',1,'','itsupport@sml.co.id','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT',NULL,1,'2025-09-23 08:24:36','2025-10-01 21:45:08',NULL),(4,'STF004','YOGA','FOREMAN',1,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(5,'STF005','Budi','FOREMAN',1,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(6,'STF006','Eko','FOREMAN',1,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(7,'STF007','KURNIA','MECHANIC',1,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(8,'STF008','BAGUS','MECHANIC',1,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(9,'STF009','Deni','MECHANIC',1,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(10,'STF010','Rudi','MECHANIC',1,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-27 02:33:59',NULL),(11,'STF011','Wahyu','MECHANIC',NULL,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37',NULL),(12,'STF012','Joko','MECHANIC',NULL,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37',NULL),(13,'STF013','Agus','HELPER',NULL,NULL,NULL,NULL,NULL,0,'2025-09-23 08:24:36','2025-10-01 20:15:12',NULL),(14,'STF014','Dimas','HELPER',NULL,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37',NULL),(15,'STF015','Fajar','HELPER',NULL,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37',NULL),(16,'STF016','Hendra','HELPER',NULL,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37',NULL),(17,'STF017','Iwan','HELPER',NULL,NULL,NULL,NULL,NULL,1,'2025-09-23 08:24:36','2025-09-25 03:57:37',NULL),(18,'STF0099','AgusA','ADMIN',2,'082136033596','itsupport@sml.co.id','',NULL,1,'2025-10-01 21:54:12','2025-10-02 00:27:27',NULL),(19,'STF00991','AgusAa','FOREMAN',3,'082136033596','itsupport@sml.co.id','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT',NULL,1,'2025-10-01 23:19:24','2025-10-01 23:19:24',NULL);
/*!40000 ALTER TABLE `employees` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `forklifts`
--

DROP TABLE IF EXISTS `forklifts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forklifts` (
  `forklift_id` int unsigned NOT NULL,
  `unit_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `unit_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `brand` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `type` enum('electric','diesel','gas','hybrid') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'electric',
  `capacity` decimal(5,2) NOT NULL COMMENT 'Capacity in tons',
  `fuel_type` enum('electric','diesel','petrol','gas','hybrid') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'electric',
  `engine_power` decimal(8,2) DEFAULT NULL COMMENT 'Engine power in HP or kW',
  `lift_height` decimal(6,2) DEFAULT NULL COMMENT 'Maximum lift height in meters',
  `year_manufactured` year DEFAULT NULL,
  `serial_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `purchase_date` date DEFAULT NULL,
  `purchase_price` decimal(15,2) DEFAULT NULL,
  `current_value` decimal(15,2) DEFAULT NULL,
  `supplier` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `warranty_expiry` date DEFAULT NULL,
  `insurance_expiry` date DEFAULT NULL,
  `last_service_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `service_interval_hours` int NOT NULL DEFAULT '250' COMMENT 'Service interval in operating hours',
  `total_operating_hours` int NOT NULL DEFAULT '0' COMMENT 'Total operating hours',
  `status` enum('available','rented','maintenance','retired','reserved') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `condition` enum('excellent','good','fair','poor','damaged') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'excellent',
  `location` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Current location/warehouse',
  `assigned_to` int unsigned DEFAULT NULL COMMENT 'Assigned to user ID',
  `rental_rate_daily` decimal(10,2) DEFAULT NULL COMMENT 'Daily rental rate',
  `rental_rate_weekly` decimal(10,2) DEFAULT NULL COMMENT 'Weekly rental rate',
  `rental_rate_monthly` decimal(10,2) DEFAULT NULL COMMENT 'Monthly rental rate',
  `availability` enum('available','unavailable','reserved') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `notes` text COLLATE utf8mb4_general_ci,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Additional specifications in JSON format',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'File attachments in JSON format',
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `deleted_by` int unsigned DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_attachment` (
  `id_inventory_attachment` int NOT NULL AUTO_INCREMENT,
  `tipe_item` enum('attachment','battery','charger') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attachment',
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
  `status_unit` int DEFAULT '7',
  `attachment_status` enum('AVAILABLE','IN_USE','USED','MAINTENANCE','BROKEN','RUSAK','RESERVED') COLLATE utf8mb4_unicode_ci DEFAULT 'AVAILABLE',
  `tanggal_masuk` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal masuk ke inventory',
  `catatan_inventory` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `status_attachment_id` int DEFAULT '1',
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
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Single source of truth untuk semua komponen: battery, charger, attachment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_attachment`
--

LOCK TABLES `inventory_attachment` WRITE;
/*!40000 ALTER TABLE `inventory_attachment` DISABLE KEYS */;
INSERT INTO `inventory_attachment` VALUES (2,'attachment',118,11,1,'989172312A',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 1',5,'USED','2025-08-22 04:36:39','Dari verifikasi PO: Sesuai dan siap digunakan','2025-08-22 04:36:39','2025-09-29 16:36:47',2),(3,'attachment',124,13,4,'123',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 14',5,'USED','2025-08-22 09:18:28','Dari verifikasi PO: Sesuai','2025-08-22 09:18:28','2025-10-24 10:17:41',2),(4,'attachment',130,15,3,'rental',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 18',5,'USED','2025-08-22 09:19:00','Dari verifikasi PO: Sesuai','2025-08-22 09:19:00','2025-11-21 13:21:53',1),(6,'charger',143,NULL,NULL,NULL,NULL,NULL,5,'123','Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-22 09:23:14','Dari verifikasi PO (Charger): Sesuai','2025-08-22 09:23:14','2025-10-24 10:17:41',2),(9,'battery',143,13,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 14',5,'USED','2025-08-27 04:15:43','Dari verifikasi PO (Battery): Sesuai','2025-08-27 04:15:43','2025-10-24 10:17:41',1),(10,'charger',143,8,NULL,NULL,NULL,NULL,5,'sadase1','Baik','Lengkap',NULL,'Terpasang di Unit 1',5,'USED','2025-08-27 04:15:43','Dari verifikasi PO (Charger): Sesuai','2025-08-27 04:15:43','2025-09-29 16:39:37',2),(11,'battery',143,NULL,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'POS 1',1,'AVAILABLE','2025-08-27 04:15:51','Dari verifikasi PO (Battery): Sesuai','2025-08-27 04:15:51','2025-09-27 11:50:49',1),(12,'charger',143,13,NULL,NULL,NULL,NULL,5,'123','Baik','Lengkap',NULL,'Terpasang di Unit 5',5,'USED','2025-08-27 04:15:51','Dari verifikasi PO (Charger): Sesuai','2025-08-27 04:15:51','2025-09-30 11:01:46',2),(13,'battery',143,NULL,NULL,NULL,4,'123',NULL,NULL,'Baik','Lengkap',NULL,'POS 1',1,'AVAILABLE','2025-08-27 04:15:58','Dari verifikasi PO (Battery): Sesuai','2025-08-27 04:15:58','2025-09-27 11:50:49',1),(14,'charger',143,16,NULL,NULL,NULL,NULL,5,'tt12354','Baik','Lengkap',NULL,'Terpasang di Unit 2',4,'USED','2025-08-27 04:15:58','Dari verifikasi PO (Charger): Sesuai','2025-08-27 04:15:58','2025-09-30 07:05:31',2),(15,'attachment',131,NULL,3,'rental',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'POS 1',1,'AVAILABLE','2025-08-27 04:50:06','Dari verifikasi PO: Sesuai','2025-08-27 04:50:06','2025-09-30 17:17:33',1),(16,'attachment',139,13,3,'rental',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 14',5,'USED','2025-08-28 09:32:49','Dari verifikasi PO: Sesuai','2025-08-28 09:32:49','2025-10-24 10:17:41',1),(17,'battery',38,NULL,2,NULL,2,'test',6,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-12 04:47:28','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:47:28','2025-10-24 10:17:41',2),(18,'battery',38,NULL,2,NULL,2,'test2',6,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-12 04:49:52','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:49:52','2025-10-24 10:17:41',2),(19,'battery',38,NULL,2,NULL,2,'test3',6,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-12 04:50:15','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:50:15','2025-10-24 10:17:41',2),(20,'battery',38,NULL,3,'',2,'test4',6,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-12 04:54:09','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 04:54:09','2025-10-24 10:17:41',2),(21,'battery',39,8,NULL,NULL,6,'andara',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 11',5,'USED','2025-08-12 08:15:40','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 08:15:40','2025-09-29 16:39:37',2),(22,'attachment',38,NULL,5,'wae',NULL,'adaaaa',6,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-12 08:21:22','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-12 08:21:22','2025-10-24 10:17:41',2),(23,'battery',38,NULL,2,NULL,2,'adit',6,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-16 04:20:16','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:20:16','2025-10-24 10:17:41',2),(24,'battery',47,NULL,NULL,NULL,3,'adit',NULL,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-16 04:20:38','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:20:38','2025-10-24 10:17:41',1),(25,'battery',39,12,NULL,NULL,6,'adit',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 5',4,'USED','2025-08-16 04:24:32','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:24:32','2025-09-29 14:18:41',2),(26,'battery',38,NULL,2,NULL,2,'adit',6,NULL,'Baik','Lengkap',NULL,'Workshop',1,'AVAILABLE','2025-08-16 04:26:43','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-16 04:26:43','2025-10-24 10:17:41',2),(27,'battery',39,14,NULL,NULL,6,'989172312A',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 12',5,'USED','2025-08-27 02:22:59','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-27 02:22:59','2025-09-30 11:01:35',2),(28,'battery',39,15,NULL,NULL,6,'123',NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 13',5,'USED','2025-08-27 02:23:14','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-27 02:23:14','2025-09-30 17:17:52',2),(29,'battery',52,16,NULL,NULL,3,'111',6,'123','Baik','Lengkap',NULL,'Terpasang di Unit 15',4,'USED','2025-08-27 15:35:47','Migrated from inventory_unit on 2025-08-30 03:37:37','2025-08-27 15:35:47','2025-09-30 07:05:31',2),(32,'charger',27,15,NULL,NULL,NULL,NULL,5,'asdas','Baik','Lengkap',NULL,'Terpasang di Unit 18',5,'USED','2025-09-27 11:10:21',NULL,'2025-09-27 11:10:21','2025-10-24 10:17:41',1),(33,'charger',27,14,NULL,NULL,NULL,NULL,5,'989172312Aawdawd','Baik','Lengkap',NULL,'Terpasang di Unit 12',5,'USED','2025-09-27 11:10:21',NULL,'2025-09-27 11:10:21','2025-10-24 10:17:41',1),(36,'attachment',0,2,3,'khaula',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'Terpasang di Unit 2',7,'USED','2025-10-06 09:11:25',NULL,'2025-10-06 02:11:25','2025-10-24 10:17:41',1),(37,'charger',0,2,NULL,NULL,NULL,NULL,17,'khaula','Baik','Lengkap',NULL,'Terpasang di Unit 2',7,'USED','2025-10-06 09:11:25',NULL,'2025-10-06 02:11:25','2025-10-24 10:17:41',1),(38,'attachment',1,NULL,8,'9218mkSML',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'Workshop',7,'AVAILABLE','2025-10-07 13:28:39',NULL,'2025-10-07 06:28:39','2025-10-24 10:17:41',1),(46,'charger',1,NULL,NULL,NULL,NULL,NULL,10,'gijagjagaja','Baik','Lengkap',NULL,'Workshop',7,'AVAILABLE','2025-10-07 13:48:14',NULL,'2025-10-07 06:48:14','2025-10-24 10:17:41',1),(47,'battery',1,NULL,NULL,NULL,18,'123123awdaasdadasda',NULL,NULL,'Baik','Lengkap',NULL,'Workshop',7,'AVAILABLE','2025-10-07 13:49:03',NULL,'2025-10-07 06:49:03','2025-10-24 10:17:41',1),(48,'attachment',155,NULL,NULL,NULL,20,'123',NULL,NULL,'Baik','Lengkap',NULL,'POS 5',7,'AVAILABLE','2025-10-11 15:53:21','Dari verifikasi PO (Battery): Sesuai','2025-10-11 15:53:21','2025-10-11 15:53:21',1),(49,'charger',155,NULL,NULL,NULL,NULL,NULL,7,'12123','Baik','Lengkap',NULL,'POS 5',7,'AVAILABLE','2025-10-13 03:04:13','Dari verifikasi PO (Charger): Sesuai','2025-10-13 03:04:13','2025-10-13 03:04:13',1),(50,'attachment',158,NULL,9,'222',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'POS 1',7,'AVAILABLE','2025-11-19 15:21:34','Dari verifikasi PO: Sesuai','2025-11-19 15:21:34','2025-11-19 15:21:34',1),(51,'attachment',158,NULL,9,'222',NULL,NULL,NULL,NULL,'Baik','Lengkap',NULL,'POS 1',7,'AVAILABLE','2025-11-19 16:36:57','Dari verifikasi PO: Sesuai','2025-11-19 16:36:57','2025-11-19 16:36:57',1);
/*!40000 ALTER TABLE `inventory_attachment` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_attachment_before_insert` BEFORE INSERT ON `inventory_attachment` FOR EACH ROW BEGIN
    
    IF NEW.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit > 0 THEN
        SET NEW.attachment_status = 'USED';
    ELSE
        SET NEW.attachment_status = 'AVAILABLE';
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_attachment_before_update` BEFORE UPDATE ON `inventory_attachment` FOR EACH ROW BEGIN
    
    IF NEW.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit > 0 THEN
        SET NEW.attachment_status = 'USED';
    ELSE
        SET NEW.attachment_status = 'AVAILABLE';
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_attachment_status_sync` BEFORE UPDATE ON `inventory_attachment` FOR EACH ROW BEGIN
    
    
    
    IF OLD.id_inventory_unit IS NULL AND NEW.id_inventory_unit IS NOT NULL THEN
        
        SET NEW.attachment_status = 'IN_USE';
        
        
        SET NEW.lokasi_penyimpanan = (
            SELECT CONCAT('Terpasang di Unit ', iu.no_unit)
            FROM `inventory_unit` iu 
            WHERE iu.id_inventory_unit = NEW.id_inventory_unit
            LIMIT 1
        );
        
        
        IF NEW.lokasi_penyimpanan IS NULL THEN
            SET NEW.lokasi_penyimpanan = CONCAT('Terpasang di Unit ID ', NEW.id_inventory_unit);
        END IF;
    END IF;
    
    
    
    
    IF OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NULL THEN
        
        
        IF NEW.attachment_status NOT IN ('BROKEN', 'MAINTENANCE') THEN
            SET NEW.attachment_status = 'AVAILABLE';
        END IF;
        
        
        SET NEW.lokasi_penyimpanan = 'Workshop';
    END IF;
    
    
    
    
    IF OLD.id_inventory_unit IS NOT NULL AND NEW.id_inventory_unit IS NOT NULL 
       AND OLD.id_inventory_unit != NEW.id_inventory_unit THEN
        
        SET NEW.lokasi_penyimpanan = (
            SELECT CONCAT('Terpasang di Unit ', iu.no_unit)
            FROM `inventory_unit` iu 
            WHERE iu.id_inventory_unit = NEW.id_inventory_unit
            LIMIT 1
        );
        
        
        SET NEW.attachment_status = 'IN_USE';
    END IF;
    
    
    
    
    
    
    
    
    IF NEW.attachment_status IN ('IN_USE', 'USED') AND NEW.id_inventory_unit IS NULL THEN
        SIGNAL SQLSTATE '45000' 
        SET MESSAGE_TEXT = 'Validasi Error: Item dengan status IN_USE/USED harus terpasang di unit. Lepaskan atau ubah status.';
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_attachment_unit_sync` AFTER UPDATE ON `inventory_attachment` FOR EACH ROW BEGIN
    
    IF OLD.id_inventory_unit != NEW.id_inventory_unit AND NEW.id_inventory_unit IS NOT NULL THEN
        UPDATE inventory_attachment ia
        JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
        SET ia.status_unit = iu.status_unit_id
        WHERE ia.id_inventory_attachment = NEW.id_inventory_attachment;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_item_unit_log`
--

LOCK TABLES `inventory_item_unit_log` WRITE;
/*!40000 ALTER TABLE `inventory_item_unit_log` DISABLE KEYS */;
INSERT INTO `inventory_item_unit_log` VALUES (1,3,11,'assign',NULL,NULL,'2025-09-13 10:43:16'),(3,16,13,'assign',NULL,NULL,'2025-09-15 15:21:59'),(4,4,13,'assign',NULL,NULL,'2025-09-16 13:57:44'),(5,16,17,'assign',NULL,NULL,'2025-09-26 14:57:04');
/*!40000 ALTER TABLE `inventory_item_unit_log` ENABLE KEYS */;
UNLOCK TABLES;

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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `inventory_unit` (
  `id_inventory_unit` int unsigned NOT NULL AUTO_INCREMENT,
  `no_unit` int unsigned DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Serial Number utama dari pabrikan',
  `id_po` int DEFAULT NULL COMMENT 'Foreign Key ke tabel purchase_orders',
  `tahun_unit` year DEFAULT NULL,
  `status_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `lokasi_unit` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departemen_id` int DEFAULT NULL COMMENT 'FK ke tabel departemen',
  `tanggal_kirim` datetime DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `harga_sewa_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per bulan',
  `harga_sewa_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per hari',
  `kontrak_id` int unsigned DEFAULT NULL COMMENT 'Foreign key ke tabel kontrak',
  `customer_id` int DEFAULT NULL,
  `customer_location_id` int DEFAULT NULL,
  `area_id` int DEFAULT NULL,
  `kontrak_spesifikasi_id` int unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi untuk tracking spek mana',
  `tipe_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel tipe_unit',
  `model_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel model_unit (sudah termasuk merk)',
  `kapasitas_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel kapasitas',
  `model_mast_id` int DEFAULT NULL COMMENT 'FK ke tabel tipe_mast',
  `tinggi_mast` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Contoh: 4500mm atau 4.5m',
  `sn_mast` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_mesin_id` int DEFAULT NULL COMMENT 'FK ke tabel mesin (sudah termasuk merk)',
  `sn_mesin` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `roda_id` int DEFAULT NULL COMMENT 'FK ke tabel jenis_roda',
  `ban_id` int DEFAULT NULL COMMENT 'FK ke tabel tipe_ban',
  `valve_id` int DEFAULT NULL COMMENT 'FK ke tabel valve',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  `aksesoris` longtext COLLATE utf8mb4_general_ci,
  `spk_id` int unsigned DEFAULT NULL,
  `delivery_instruction_id` int unsigned DEFAULT NULL,
  `di_workflow_id` int DEFAULT NULL,
  `workflow_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contract_disconnect_date` datetime DEFAULT NULL,
  `contract_disconnect_stage` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id_inventory_unit`),
  KEY `fk_inventory_unit_departemen` (`departemen_id`),
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
  KEY `idx_inventory_unit_kontrak` (`kontrak_id`),
  KEY `fk_inventory_unit_area` (`area_id`),
  KEY `idx_inventory_unit_status` (`status_unit_id`),
  CONSTRAINT `fk_inventory_unit_area` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`),
  CONSTRAINT `fk_inventory_unit_delivery_instruction` FOREIGN KEY (`delivery_instruction_id`) REFERENCES `delivery_instructions` (`id`),
  CONSTRAINT `fk_inventory_unit_departemen` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kapasitas` FOREIGN KEY (`kapasitas_unit_id`) REFERENCES `kapasitas` (`id_kapasitas`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_kontrak_spesifikasi` FOREIGN KEY (`kontrak_spesifikasi_id`) REFERENCES `kontrak_spesifikasi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_model` FOREIGN KEY (`model_unit_id`) REFERENCES `model_unit` (`id_model_unit`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`),
  CONSTRAINT `fk_inventory_unit_status` FOREIGN KEY (`status_unit_id`) REFERENCES `status_unit` (`id_status`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_inventory_unit_tipe` FOREIGN KEY (`tipe_unit_id`) REFERENCES `tipe_unit` (`id_tipe_unit`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Data unit utama - komponen disimpan di inventory_attachment';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `inventory_unit`
--

LOCK TABLES `inventory_unit` WRITE;
/*!40000 ALTER TABLE `inventory_unit` DISABLE KEYS */;
INSERT INTO `inventory_unit` VALUES (1,1,'SN5123456',122,2023,7,'Lokasi Utama - Jakarta',2,'2025-09-04 00:00:00','Test verification from API test',9000000.00,NULL,44,2,1,2,19,9,6,2,5,'2m','test4',4,'test4',4,3,4,'2025-08-12 02:22:14','2025-10-24 10:51:40','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"CAMERA AI\",\"CAMERA\",\"VOICE ANNOUNCER\"]',28,123,NULL,NULL,NULL,NULL),(2,2,'SN6123456',123,2023,7,'Lokasi Utama - Jakarta',2,'2025-09-04 00:00:00','Test verification from API test',9000000.00,NULL,44,2,1,2,19,10,3,10,2,'3m','khaula',2,'khaula',2,2,3,'2025-08-12 03:15:49','2025-10-24 10:51:40','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA AI\",\"ACRYLIC\",\"SAFETY BELT INTERLOC\"]',28,123,NULL,NULL,NULL,NULL),(4,4,'test',38,2025,7,'Lokasi Utama - Jakarta',3,'2025-09-12 00:00:00','knjnakjndkjanwdjaw',12000000.00,NULL,55,4,3,6,39,12,6,6,2,'2m','test',2,'test',2,1,3,'2025-08-12 04:47:28','2025-10-24 10:51:40','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',36,125,NULL,NULL,NULL,NULL),(5,7,'test2',38,2025,4,'POS 1',3,'2025-09-13 00:00:00',NULL,19776000.00,NULL,54,3,2,21,37,12,6,6,2,NULL,'test2',2,'test2',2,1,3,'2025-08-12 04:49:52','2025-10-21 08:38:59','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]',38,127,NULL,NULL,NULL,NULL),(6,8,'test3',38,2025,7,'Lokasi Utama - Jakarta',3,'2025-09-13 00:00:00',NULL,89000000.00,NULL,57,6,5,6,41,12,6,6,2,NULL,'test3',2,'test3',2,1,3,'2025-08-12 04:50:15','2025-10-24 10:51:40','[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA\",\"BIO METRIC\",\"P3K\"]',39,128,NULL,NULL,NULL,NULL),(7,3,'test4',38,2025,7,'Lokasi Utama - Jakarta',3,'2025-09-12 00:00:00','LAGI TEST',15000000.00,NULL,56,5,4,6,40,12,6,6,2,'2m','test4',2,'test4',2,1,3,'2025-08-12 04:54:09','2025-10-24 10:51:40','[\"LAMPU UTAMA\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BLUE SPOT\",\"BACK BUZZER\"]',37,126,NULL,NULL,NULL,NULL),(8,11,'andara',39,2025,5,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,19,NULL,2,1,30,1,NULL,'andara',3,'andara',1,3,1,'2025-08-12 08:15:40','2025-09-29 09:39:37',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(9,9,'adaaaaa',38,2025,7,'Lokasi Utama - Jakarta',3,'2025-09-13 00:00:00',NULL,89000000.00,NULL,57,6,5,6,41,12,6,6,2,NULL,'adaaaaa',2,'adaaaaa',2,1,3,'2025-08-12 08:21:22','2025-10-24 10:51:40','[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"ACRYLIC\",\"P3K\",\"SAFETY BELT INTERLOC\",\"SPARS ARRESTOR\"]',39,128,NULL,NULL,NULL,NULL),(10,6,'adit',38,2025,4,'POS 1',3,'2025-09-12 00:00:00','awdawdwa',12000000.00,NULL,55,4,3,21,39,12,6,6,2,'12m','adit',2,'adit',2,1,3,'2025-08-16 04:20:16','2025-11-20 13:39:38','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]',36,125,NULL,NULL,NULL,NULL),(11,10,'adit',47,2025,5,'POS 1',3,'2025-09-13 00:00:00',NULL,100000000.00,NULL,57,6,5,8,42,3,6,4,5,NULL,'adit',3,'adit',2,3,2,'2025-08-16 04:20:38','2025-09-29 09:39:48','[\"LAMPU UTAMA\",\"CAMERA AI\",\"SPEED LIMITER\",\"LASER FORK\",\"HORN KLASON\",\"APAR 3 KG\"]',40,129,NULL,NULL,NULL,NULL),(12,5,'adit',39,2025,4,'POS 1',2,'2025-09-12 00:00:00',NULL,19776000.00,NULL,54,3,2,2,37,2,1,3,1,NULL,'adit',3,'adit',1,3,1,'2025-08-16 04:24:32','2025-10-02 14:48:07',NULL,29,124,NULL,NULL,NULL,NULL),(13,14,'kkai',38,2025,5,'POS 1',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,22,NULL,12,6,6,2,NULL,'adit',2,'adit',2,1,3,'2025-08-16 04:26:43','2025-09-30 04:01:46',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(14,12,'123',39,2025,5,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,30,NULL,2,1,3,1,NULL,'123',3,'123',1,3,1,'2025-08-27 02:22:59','2025-09-30 04:01:35',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(15,18,'123',39,2025,5,'POS 1',2,'2025-09-30 00:00:00',NULL,100000000.00,NULL,64,NULL,NULL,25,46,2,1,3,1,NULL,'123',3,'123',1,3,1,'2025-08-27 02:23:14','2025-11-21 13:21:53',NULL,56,143,NULL,NULL,NULL,NULL),(16,17,'111',52,2025,4,'POS 1',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,19,NULL,2,1,5,3,NULL,'111',3,'111',1,2,2,'2025-08-27 15:35:47','2025-09-30 07:05:31',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(17,16,'222',52,2025,4,'POS 1',2,'2025-09-26 00:00:00','awdawdaw',200000000.00,NULL,63,NULL,NULL,21,45,2,1,5,3,'123','222',3,'222',1,2,2,'2025-08-27 15:36:17','2025-11-21 09:59:19','[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\"]',50,132,NULL,NULL,NULL,NULL),(20,NULL,'test malming',155,2025,2,'Workshop',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,11,136,14,17,NULL,'test malming',15,'test malming',2,3,1,'2025-10-11 14:56:29','2025-11-18 14:00:47',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(21,NULL,'222',158,2025,2,'Workshop',2,NULL,'AWDAW',NULL,NULL,NULL,NULL,NULL,NULL,NULL,9,2,14,16,NULL,'222',16,'222',2,2,3,'2025-10-24 03:40:52','2025-11-18 14:00:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(22,NULL,'222',158,2025,2,'POS 2',2,NULL,'AWDAW',NULL,NULL,NULL,NULL,NULL,NULL,NULL,9,2,14,16,NULL,'222',16,'222',2,2,3,'2025-11-17 09:44:50','2025-11-18 14:00:57',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(23,NULL,'1',160,2025,2,'POS 2',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,520,8,18,NULL,'1',17,'1',2,1,2,'2025-11-17 09:54:49','2025-11-18 14:01:01',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(24,NULL,'2',160,2025,2,'POS 5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,520,8,18,NULL,'2',17,'2',2,1,2,'2025-11-18 06:54:38','2025-11-18 14:01:04',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(25,NULL,'3',160,2025,2,'POS 5',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,520,8,18,NULL,'3',17,'3',2,1,2,'2025-11-18 06:59:15','2025-11-18 14:01:08',NULL,NULL,NULL,NULL,NULL,NULL,NULL),(26,NULL,'6',160,2025,2,'POS 5',2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,11,11,14,15,NULL,'6',19,'6',3,5,2,'2025-11-18 07:06:52','2025-11-18 07:06:52',NULL,NULL,NULL,NULL,NULL,NULL,NULL);
/*!40000 ALTER TABLE `inventory_unit` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_unit_bi` BEFORE INSERT ON `inventory_unit` FOR EACH ROW BEGIN
    IF (NEW.kontrak_id IS NOT NULL) OR (NEW.kontrak_spesifikasi_id IS NOT NULL) THEN
        SET NEW.status_unit_id = 3;
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
    
    
    
    
    IF NEW.status_unit_id = 8 AND (OLD.status_unit_id IS NULL OR OLD.status_unit_id != 8) THEN
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
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_unit_attachment_sync` AFTER UPDATE ON `inventory_unit` FOR EACH ROW BEGIN
    
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        
        
        UPDATE inventory_attachment 
        SET status_unit = NEW.status_unit_id, updated_at = NOW()
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_inventory_unit_status_sync` AFTER UPDATE ON `inventory_unit` FOR EACH ROW BEGIN
    
    IF OLD.status_unit_id != NEW.status_unit_id THEN
        UPDATE inventory_attachment 
        SET status_unit = NEW.status_unit_id
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
/*!50001 DROP VIEW IF EXISTS `inventory_unit_components`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
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
 1 AS `attachment_model`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `jenis_perintah_kerja`
--

DROP TABLE IF EXISTS `jenis_perintah_kerja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_perintah_kerja` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jenis_roda` (
  `id_roda` int NOT NULL AUTO_INCREMENT,
  `tipe_roda` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kapasitas` (
  `id_kapasitas` int NOT NULL AUTO_INCREMENT,
  `kapasitas_unit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kontrak` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `customer_location_id` int DEFAULT NULL,
  `no_kontrak` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_po_marketing` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nilai_total` decimal(15,2) DEFAULT NULL COMMENT 'Nilai total kontrak dalam rupiah',
  `total_units` int unsigned NOT NULL DEFAULT '0' COMMENT 'Total unit yang terkait dengan kontrak ini',
  `jenis_sewa` enum('BULANAN','HARIAN') COLLATE utf8mb4_general_ci DEFAULT 'BULANAN' COMMENT 'Jenis periode sewa',
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `status` enum('Aktif','Berakhir','Pending','Dibatalkan') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `dibuat_oleh` int unsigned DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_kontrak_customer_location` (`customer_location_id`),
  KEY `idx_kontrak_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontrak`
--

LOCK TABLES `kontrak` WRITE;
/*!40000 ALTER TABLE `kontrak` DISABLE KEYS */;
INSERT INTO `kontrak` VALUES (44,1,'KNTRK/2208/0001','PO-ADIT10998',18000000.00,2,'BULANAN','2025-09-01','2025-09-01','Aktif',1,'2025-09-01 01:54:45','2025-09-26 09:00:29'),(54,2,'KNTRK/2209/0001',NULL,39552000.00,2,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-09 09:54:01','2025-09-26 17:27:25'),(55,3,'SML/DS/121025',NULL,24000000.00,2,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-12 06:34:46','2025-09-26 17:26:28'),(56,4,'TEST/AUTO/001',NULL,15000000.00,1,'BULANAN','2025-09-01','2025-12-31','Aktif',1,'2025-09-12 09:54:47','2025-09-26 17:26:28'),(57,3,'test/1/1/5','12345',278000000.00,3,'BULANAN','2025-09-13','2025-09-13','Aktif',1,'2025-09-13 02:56:22','2025-10-22 03:10:12'),(63,12,'KNTRK/2209/0002','PO-92131240',200000000.00,1,'BULANAN','2025-09-30','2025-10-31','Aktif',1,'2025-09-26 07:34:19','2025-09-27 11:28:22'),(64,14,'LG-9812310','0218812310',0.00,0,'BULANAN','2025-09-27','2025-10-27','Aktif',1,'2025-09-27 03:52:56','2025-11-21 13:21:53');
/*!40000 ALTER TABLE `kontrak` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_kontrak_rental_workflow` AFTER UPDATE ON `kontrak` FOR EACH ROW BEGIN
    
    IF NEW.status != OLD.status THEN
        
        
        INSERT INTO kontrak_status_changes (kontrak_id, old_status, new_status, changed_at)
        VALUES (NEW.id, OLD.status, NEW.status, NOW())
        ON DUPLICATE KEY UPDATE 
            old_status = OLD.status,
            new_status = NEW.status,
            changed_at = NOW();
        
        
        IF NEW.status = 'Aktif' AND OLD.status != 'Aktif' THEN
            
            UPDATE inventory_unit 
            SET status_unit_id = 7, updated_at = NOW() 
            WHERE kontrak_id = NEW.id;
            
            
            UPDATE inventory_attachment ia
            JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
            SET ia.status_unit = 7, ia.updated_at = NOW() 
            WHERE iu.kontrak_id = NEW.id;
            
        ELSEIF OLD.status = 'Aktif' AND NEW.status IN ('Berakhir','Dibatalkan') THEN
            
            UPDATE inventory_unit 
            SET status_unit_id = CASE 
                
                WHEN no_unit IS NOT NULL AND no_unit != '' THEN 1 
                
                ELSE 2 
            END, 
            updated_at = NOW()
            WHERE kontrak_id = NEW.id;
            
            
            UPDATE inventory_attachment ia
            JOIN inventory_unit iu ON ia.id_inventory_unit = iu.id_inventory_unit
            SET ia.status_unit = CASE 
                WHEN iu.no_unit IS NOT NULL AND iu.no_unit != '' THEN 1  
                ELSE 2  
            END, 
            ia.updated_at = NOW()
            WHERE iu.kontrak_id = NEW.id;
        END IF;
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_kontrak_status_unit_update` AFTER UPDATE ON `kontrak` FOR EACH ROW BEGIN
    
    IF OLD.status != 'Aktif' AND NEW.status = 'Aktif' THEN
        UPDATE inventory_unit 
        SET status_unit_id = 7, 
            updated_at = CURRENT_TIMESTAMP
        WHERE kontrak_id = NEW.id 
        AND status_unit_id IN (5, 6); 
        
        
        INSERT INTO unit_status_log (
            inventory_unit_id, old_status_id, new_status_id,
            reason, triggered_by, reference_id, created_by
        )
        SELECT 
            id_inventory_unit,
            status_unit_id, 
            7, 
            CONCAT('Contract #', NEW.no_kontrak, ' activated - unit ready for rental'),
            'CONTRACT_ACTIVE',
            NEW.id,
            'SYSTEM'
        FROM inventory_unit 
        WHERE kontrak_id = NEW.id 
        AND status_unit_id = 7; 
    END IF;
    
    
    IF OLD.status != 'Expired' AND OLD.status != 'Canceled' 
    AND (NEW.status = 'Expired' OR NEW.status = 'Canceled') THEN
        UPDATE inventory_unit 
        SET status_unit_id = 11, 
            updated_at = CURRENT_TIMESTAMP
        WHERE kontrak_id = NEW.id 
        AND status_unit_id = 7; 
        
        
        INSERT INTO unit_status_log (
            inventory_unit_id, old_status_id, new_status_id,
            reason, triggered_by, reference_id, created_by
        )
        SELECT 
            id_inventory_unit,
            7, 
            11, 
            CONCAT('Contract #', NEW.no_kontrak, ' status changed to ', NEW.status),
            'CONTRACT_INACTIVE',
            NEW.id,
            'SYSTEM'
        FROM inventory_unit 
        WHERE kontrak_id = NEW.id 
        AND status_unit_id = 11; 
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kontrak_spesifikasi` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `kontrak_id` int unsigned NOT NULL,
  `spek_kode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Kode unik spesifikasi dalam kontrak (A, B, C)',
  `jumlah_dibutuhkan` int NOT NULL DEFAULT '1' COMMENT 'Jumlah unit yang dibutuhkan untuk spek ini',
  `jumlah_tersedia` int NOT NULL DEFAULT '0' COMMENT 'Jumlah unit yang sudah di-assign',
  `harga_per_unit_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa bulanan per unit',
  `harga_per_unit_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa harian per unit',
  `catatan_spek` text COLLATE utf8mb4_general_ci COMMENT 'Catatan khusus untuk spesifikasi ini',
  `departemen_id` int DEFAULT NULL,
  `tipe_unit_id` int DEFAULT NULL,
  `tipe_jenis` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kapasitas_id` int DEFAULT NULL,
  `merk_unit` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_unit` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `attachment_tipe` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `attachment_merk` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jenis_baterai` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `charger_id` int DEFAULT NULL,
  `mast_id` int DEFAULT NULL,
  `ban_id` int DEFAULT NULL,
  `roda_id` int DEFAULT NULL,
  `valve_id` int DEFAULT NULL,
  `aksesoris` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Array aksesoris yang dibutuhkan',
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_kontrak_spesifikasi_kontrak` (`kontrak_id`),
  CONSTRAINT `fk_kontrak_spesifikasi_kontrak` FOREIGN KEY (`kontrak_id`) REFERENCES `kontrak` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontrak_spesifikasi`
--

LOCK TABLES `kontrak_spesifikasi` WRITE;
/*!40000 ALTER TABLE `kontrak_spesifikasi` DISABLE KEYS */;
INSERT INTO `kontrak_spesifikasi` VALUES (19,44,'SPEC-001',2,0,9000000.00,NULL,'',2,6,'HAND PALLET',41,'HELI',NULL,'FORK POSITIONER',NULL,'Lithium-ion',5,22,6,1,2,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]','2025-09-01 01:55:43','2025-09-01 01:55:43'),(37,54,'SPEC-001',2,0,19776000.00,NULL,'',2,6,'PALLET STACKER',42,'HELI',NULL,'FORKLIFT SCALE',NULL,'Lead Acid',1,14,6,1,1,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]','2025-09-09 10:03:32','2025-09-09 10:03:32'),(39,55,'SPEC-001',2,0,12000000.00,NULL,'',1,6,'THREE WHEEL',14,'HANGCHA',NULL,'FORK POSITIONER',NULL,NULL,NULL,16,6,2,2,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]','2025-09-12 06:35:44','2025-09-12 06:35:44'),(40,56,'SPEC-001',1,0,15000000.00,NULL,NULL,2,6,NULL,42,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-12 09:55:35','2025-09-12 09:55:35'),(41,57,'SPEC-001',2,0,89000000.00,NULL,'',3,6,'COUNTER BALANCE',40,'HELI',NULL,'FORK POSITIONER',NULL,NULL,NULL,16,3,1,3,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA\",\"BIO METRIC\",\"ACRYLIC\",\"P3K\",\"SAFETY BELT INTERLOC\",\"SPARS ARRESTOR\"]','2025-09-13 02:57:03','2025-09-13 02:57:03'),(42,57,'SPEC-002',1,0,100000000.00,NULL,'',3,6,'PALLET STACKER',42,'KOMATSU',NULL,'FORK POSITIONER',NULL,NULL,NULL,17,3,4,2,'[\"LAMPU UTAMA\",\"CAMERA AI\",\"SPEED LIMITER\",\"LASER FORK\",\"HORN KLASON\",\"APAR 3 KG\"]','2025-09-13 03:34:55','2025-09-13 03:34:55'),(44,56,'SPEC-002',1,0,1000000.00,NULL,'',1,NULL,NULL,NULL,NULL,NULL,'SIDE SHIFTER','','',0,NULL,NULL,NULL,NULL,NULL,'2025-09-16 08:13:27','2025-09-16 08:13:27'),(45,63,'SPEC-001',1,0,200000000.00,NULL,'',1,6,'COUNTER BALANCE',37,'JUNGHEINRICH',NULL,'FORK',NULL,NULL,NULL,14,6,3,1,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"ROTARY LAMP\"]','2025-09-26 07:35:07','2025-09-26 07:35:07'),(46,64,'SPEC-001',1,0,100000000.00,NULL,'Spare Unit',2,6,'REACH TRUCK',42,'KOMATSU',NULL,'FORKLIFT SCALE',NULL,'Lithium-ion',9,15,3,1,2,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]','2025-09-27 03:55:02','2025-09-27 03:56:47'),(47,64,'SPEC-002',4,0,1000000000.00,NULL,'',1,6,'PALLET STACKER',42,'LINDE',NULL,'FORK',NULL,NULL,NULL,6,4,1,2,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"HORN KLASON\",\"P3K\"]','2025-09-27 17:00:52','2025-09-27 17:00:52'),(48,57,'SPEC-003',10,0,123123123.00,NULL,'',1,4,'SCRUBER',38,'JUNGHEINRICH',NULL,'FORK POSITIONER',NULL,NULL,NULL,14,6,4,1,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"ROTARY LAMP\",\"BACK BUZZER\",\"SENSOR PARKING\",\"SPEED LIMITER\",\"HORN SPEAKER\",\"HORN KLASON\",\"APAR 1 KG\",\"BEACON\"]','2025-10-10 08:23:19','2025-10-10 08:23:19'),(49,63,'SPEC-002',10,0,100000000.00,NULL,'',1,1,'DUMP TRUCK',41,'KOMATSU',NULL,'FORK',NULL,NULL,NULL,14,3,1,2,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA AI\",\"CAMERA\"]','2025-10-16 02:37:37','2025-10-16 02:37:37'),(50,64,'SPEC-003',1,0,123123123.00,NULL,'',1,NULL,NULL,NULL,NULL,NULL,'FORK POSITIONER','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-21 09:19:50','2025-10-21 09:19:50'),(51,57,'SPEC-004',1,0,123131231.00,NULL,'',2,NULL,NULL,NULL,NULL,NULL,'FORKLIFT SCALE','asd',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-21 09:21:47','2025-10-21 09:21:47'),(52,63,'SPEC-003',1,0,19000000.00,NULL,'Spare Unit',2,7,NULL,14,'HELI',NULL,'FORK POSITIONER',NULL,'Lithium-ion',8,14,2,3,2,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"ROTARY LAMP\",\"BACK BUZZER\",\"SENSOR PARKING\",\"SPEED LIMITER\",\"HORN SPEAKER\",\"HORN KLASON\"]','2025-10-23 03:34:13','2025-10-23 03:34:13'),(53,57,'SPEC-005',1,0,19000000.00,NULL,'Spare Unit 2',1,1,NULL,43,'LINDE',NULL,'FORKLIFT SCALE',NULL,NULL,NULL,14,2,3,1,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"ROTARY LAMP\",\"BACK BUZZER\",\"SENSOR PARKING\",\"SPEED LIMITER\",\"HORN SPEAKER\",\"HORN KLASON\",\"P3K\"]','2025-10-23 04:12:56','2025-10-23 04:12:56');
/*!40000 ALTER TABLE `kontrak_spesifikasi` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_kontrak_spesifikasi_aksesoris_insert` AFTER INSERT ON `kontrak_spesifikasi` FOR EACH ROW BEGIN
    
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
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_kontrak_spesifikasi_aksesoris_update` AFTER UPDATE ON `kontrak_spesifikasi` FOR EACH ROW BEGIN
    
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
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_kontrak_spesifikasi_au` AFTER UPDATE ON `kontrak_spesifikasi` FOR EACH ROW BEGIN
    IF NOT (OLD.kontrak_id <=> NEW.kontrak_id) THEN
        IF OLD.kontrak_id IS NOT NULL THEN
            CALL recalc_kontrak_total(OLD.kontrak_id);
        END IF;
        IF NEW.kontrak_id IS NOT NULL THEN
            CALL recalc_kontrak_total(NEW.kontrak_id);
        END IF;
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kontrak_status_changes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kontrak_id` int NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `processed` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_kontrak_id` (`kontrak_id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kontrak_status_changes`
--

LOCK TABLES `kontrak_status_changes` WRITE;
/*!40000 ALTER TABLE `kontrak_status_changes` DISABLE KEYS */;
INSERT INTO `kontrak_status_changes` VALUES (1,44,'Berakhir','Aktif','2025-09-04 07:48:00',0),(3,54,'Berakhir','Aktif','2025-09-12 09:31:11',0),(4,55,'Pending','Aktif','2025-09-12 09:49:35',0),(11,56,'Pending','Aktif','2025-09-13 01:20:35',0),(14,57,'Pending','Aktif','2025-09-13 02:59:38',0),(15,63,'Aktif','Pending','2025-09-27 04:26:45',0),(22,64,'Pending','Aktif','2025-09-30 03:02:16',0);
/*!40000 ALTER TABLE `kontrak_status_changes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `login_attempts`
--

DROP TABLE IF EXISTS `login_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `login_attempts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `identifier` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `attempts` int NOT NULL DEFAULT '0',
  `last_attempt_at` datetime NOT NULL,
  `locked_until` datetime DEFAULT NULL,
  `is_successful` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_identifier` (`identifier`),
  KEY `idx_ip_address` (`ip_address`),
  KEY `idx_locked_until` (`locked_until`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `login_attempts`
--

LOCK TABLES `login_attempts` WRITE;
/*!40000 ALTER TABLE `login_attempts` DISABLE KEYS */;
INSERT INTO `login_attempts` VALUES (1,'arisaditya45@gmail.com','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',5,'2025-11-22 17:30:11','2025-11-22 17:45:11',0,'2025-11-22 10:17:19','2025-11-22 17:30:11'),(2,'admin@optima.com','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:145.0) Gecko/20100101 Firefox/145.0',0,'2025-11-22 10:17:27',NULL,1,'2025-11-22 10:17:27','2025-11-22 10:17:27'),(3,'service_diesel','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:145.0) Gecko/20100101 Firefox/145.0',0,'2025-11-22 10:17:33',NULL,1,'2025-11-22 10:17:33','2025-11-22 10:17:33'),(4,'admin@optima.com','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',2,'2025-11-22 15:52:11',NULL,0,'2025-11-22 12:15:21','2025-11-22 15:52:11'),(5,'itsupport@sml.co.id','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',4,'2025-11-28 11:35:15',NULL,0,'2025-11-22 15:52:23','2025-11-28 11:35:15'),(6,'superadmin','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-28 11:35:34',NULL,1,'2025-11-22 15:52:53','2025-11-28 11:35:34'),(7,'kanebokering','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-22 19:32:21',NULL,1,'2025-11-22 18:39:42','2025-11-22 19:32:21');
/*!40000 ALTER TABLE `login_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mesin`
--

DROP TABLE IF EXISTS `mesin`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mesin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `merk_mesin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `model_mesin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `departemen_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `departemen_id` (`departemen_id`),
  CONSTRAINT `mesin_ibfk_1` FOREIGN KEY (`departemen_id`) REFERENCES `departemen` (`id_departemen`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mesin`
--

LOCK TABLES `mesin` WRITE;
/*!40000 ALTER TABLE `mesin` DISABLE KEYS */;
INSERT INTO `mesin` VALUES (1,'TOYOTA','1DZ-0196006',1),(2,'TOYOTA','1DZ-0197191',1),(3,'TOYOTA','1DZ-0218846',1),(4,'TOYOTA','1DZ-0226001',1),(5,'TOYOTA','1DZ-0226519',1),(6,'TOYOTA','1DZ-0226619',1),(7,'TOYOTA','1DZ-0227007',1),(8,'TOYOTA','1DZ-0230507',1),(9,'TOYOTA','1DZ-0238684',1),(10,'TOYOTA','1DZ-0238791',1),(11,'TOYOTA','1DZ-0239268',1),(12,'TOYOTA','1DZ-0239357',1),(13,'TOYOTA','1DZ-0239361',1),(14,'TOYOTA','1DZ-0239365',1),(15,'TOYOTA','1DZ-0244305',1),(16,'TOYOTA','1DZ-0247102',1),(17,'TOYOTA','1DZ-0247201',1),(18,'TOYOTA','1DZ-0250918',1),(19,'TOYOTA','1DZ-0251498',1),(20,'TOYOTA','1DZ-0252270',1),(21,'TOYOTA','1DZ-0252764',1),(22,'TOYOTA','1DZ-0252821',1),(23,'TOYOTA','1DZ-0252893',1),(24,'TOYOTA','1DZ-0347648',1),(25,'TOYOTA','1DZ-0356711',1),(26,'TOYOTA','1DZ-0358669',1),(27,'TOYOTA','1DZ-0358919',1),(28,'TOYOTA','1DZ-0359781',1),(29,'TOYOTA','1DZ-0360733',1),(30,'TOYOTA','1DZ-0360739',1),(31,'TOYOTA','1DZ-0360741',1),(32,'TOYOTA','1DZ-0360852',1),(33,'TOYOTA','1DZ-0360939',1),(34,'TOYOTA','1DZ-0360952',1),(35,'TOYOTA','1DZ-0360953',1),(36,'TOYOTA','1DZ-0361046',1),(37,'TOYOTA','1DZ-0361052',1),(38,'TOYOTA','1DZ-0361053',1),(39,'TOYOTA','1DZ-0361055',1),(40,'TOYOTA','1DZ-0361058',1),(41,'TOYOTA','1DZ-0361063',1),(42,'TOYOTA','1DZ-0361065',1),(43,'TOYOTA','1DZ-0361137',1),(44,'TOYOTA','1DZ-0361153',1),(45,'TOYOTA','1DZ-0361345',1),(46,'TOYOTA','1DZ-0362712',1),(47,'TOYOTA','1DZ-0364387',1),(48,'TOYOTA','1DZ-0364398',1),(49,'TOYOTA','1DZ-0364710',1),(50,'TOYOTA','1DZ-0365850',1),(51,'TOYOTA','1DZ-0370103',1),(52,'TOYOTA','1DZ-0372317',1),(53,'TOYOTA','1DZ-0372319',1),(54,'TOYOTA','1DZ-0372355',1),(55,'TOYOTA','1DZ-0372358',1),(56,'TOYOTA','1DZ-0372476',1),(57,'TOYOTA','1DZ-0372530',1),(58,'TOYOTA','1DZ-0372561',1),(59,'TOYOTA','1DZ-0372563',1),(60,'TOYOTA','1DZ-0372608',1),(61,'TOYOTA','1DZ-0372612',1),(62,'TOYOTA','1DZ-0372632',1),(63,'TOYOTA','1DZ-0372634',1),(64,'TOYOTA','1DZ-0372659',1),(65,'TOYOTA','1DZ-0400513',1),(66,'TOYOTA','1DZ-0414691',1),(67,'TOYOTA','1DZ-0414776',1),(68,'TOYOTA','1DZ-0414785',1),(69,'TOYOTA','1DZ-0414786',1),(70,'TOYOTA','1DZ-0415466',1),(71,'TOYOTA','1DZ-0415779',1),(72,'TOYOTA','1DZ-0415814',1),(73,'TOYOTA','1DZ-0415816',1),(74,'TOYOTA','1DZ-0415941',1),(75,'TOYOTA','1DZ-0416059',1),(76,'TOYOTA','1DZ-0416061',1),(77,'TOYOTA','1DZ-0416087',1),(78,'TOYOTA','1DZ-0416090',1),(79,'TOYOTA','1DZ-0416241',1),(80,'TOYOTA','1DZ-0416404',1),(81,'TOYOTA','1DZ-0416894',1),(82,'TOYOTA','1DZ-0416938',1),(83,'TOYOTA','1DZ-0416943',1),(84,'TOYOTA','1DZ-0416976',1),(85,'TOYOTA','1DZ-0416980',1),(86,'TOYOTA','1DZ-0417098',1),(87,'TOYOTA','1DZ-0417218',1),(88,'TOYOTA','1DZ-0417230',1),(89,'TOYOTA','1DZ-0417607',1),(90,'TOYOTA','1DZ-0417632',1),(91,'TOYOTA','1DZ-0417662',1),(92,'TOYOTA','1DZ-0417690',1),(93,'TOYOTA','14Z-0014579',1),(94,'TOYOTA','14Z-0014581',1),(95,'TOYOTA','14Z-0015050',1),(96,'TOYOTA','14Z-0015658',1),(97,'TOYOTA','14Z-0015662',1),(98,'TOYOTA','14Z-0015671',1),(99,'TOYOTA','14Z-0015673',1),(100,'TOYOTA','14Z-0015686',1),(101,'TOYOTA','14Z-0015691',1),(102,'TOYOTA','14Z-0015692',1),(103,'TOYOTA','14Z-0028118',1),(104,'TOYOTA','14Z-0028134',1),(105,'TOYOTA','14Z-0028140',1),(106,'TOYOTA','14Z-0028150',1),(107,'TOYOTA','14Z-0028165',1),(108,'TOYOTA','14Z-0028179',1),(109,'TOYOTA','14Z-0028203',1),(110,'TOYOTA','14Z-0028241',1),(111,'TOYOTA','4Y-2351413',3),(112,'TOYOTA','4Y-2355314',3),(113,'TOYOTA','4Y-2355860',3),(114,'TOYOTA','4Y-2356048',3),(115,'TOYOTA','4Y-2356096',3),(116,'TOYOTA','4Y-2366516',3),(117,'TOYOTA','4Y-2369406',3),(118,'TOYOTA','4Y-2371882',3),(119,'TOYOTA','4Y-2372927',3),(120,'TOYOTA','4Y-2373823',3),(121,'TOYOTA','4Y-2376533',3),(122,'TOYOTA','4Y-2379846',3),(123,'TOYOTA','4Y-2380448',3),(124,'TOYOTA','4Y-2386387 matik',3),(125,'TOYOTA','4Y-2386473 matik',3),(126,'MITSUBISHI','S4S-3.331',1),(127,'MITSUBISHI','S4S-21497',1),(128,'MITSUBISHI','S4S-214563',1),(129,'MITSUBISHI','S4S-217084',1),(130,'MITSUBISHI','S4S-218625',1),(131,'MITSUBISHI','S4S-219720',1),(132,'MITSUBISHI','S4S-219725',1),(133,'MITSUBISHI','S4S-220849',1),(134,'MITSUBISHI','S4S-220851',1),(135,'MITSUBISHI','S4S-220936',1),(136,'MITSUBISHI','S4S-222774',1),(137,'MITSUBISHI','S4S-224384',1),(138,'MITSUBISHI','S4S-224766',1),(139,'MITSUBISHI','S4S-224839',1),(140,'MITSUBISHI','S4S-225323',1),(141,'MITSUBISHI','S4S-227-973',1),(142,'MITSUBISHI','S4S-228-487',1),(143,'MITSUBISHI','S4S-229-265',1),(144,'MITSUBISHI','S4S-231537',1),(145,'MITSUBISHI','S4S-232202',1),(146,'MITSUBISHI','S4S-232672',1),(147,'MITSUBISHI','S4S-232878',1),(148,'MITSUBISHI','S4S-234502',1),(149,'MITSUBISHI','S4S-234503',1),(150,'MITSUBISHI','S4S-234505',1),(151,'MITSUBISHI','S4S-234506',1),(152,'MITSUBISHI','S4S-234507',1),(153,'MITSUBISHI','S4S-237854',1),(154,'MITSUBISHI','S4S-240392',1),(155,'MITSUBISHI','S4S-240574',1),(156,'MITSUBISHI','S4S-242543',1),(157,'MITSUBISHI','S4S-242544',1),(158,'MITSUBISHI','S4S-242550',1),(159,'MITSUBISHI','S4S-242750',1),(160,'MITSUBISHI','S4S-243432',1),(161,'MITSUBISHI','S4S-243437',1),(162,'MITSUBISHI','S4S-243448',1),(163,'MITSUBISHI','S4S-243475',1),(164,'MITSUBISHI','S4S-243632',1),(165,'MITSUBISHI','S4S-243687',1),(166,'MITSUBISHI','S4S-243690',1),(167,'MITSUBISHI','S4S-243768',1),(168,'MITSUBISHI','S4S-243770',1),(169,'MITSUBISHI','S4S-243939',1),(170,'MITSUBISHI','S4S-244106',1),(171,'MITSUBISHI','S4S-244110',1),(172,'MITSUBISHI','S6S-5B1L',1),(173,'MITSUBISHI','S6S-040644',1),(174,'MITSUBISHI','S6S-072650',1),(175,'MITSUBISHI','S6S-082680',1),(176,'MITSUBISHI','S6S-082682',1),(177,'MITSUBISHI','S6S-082735',1),(178,'MITSUBISHI','S6S-082736',1),(179,'MITSUBISHI','S6S-083725',1),(180,'MITSUBISHI','S6S-083726',1),(181,'MITSUBISHI','S6S-083774',1),(182,'MITSUBISHI','S6S-088522',1),(183,'MITSUBISHI','S6S-088558',1),(184,'MITSUBISHI','S6S-089071',1),(185,'MITSUBISHI','S6S-KDN2V',1),(186,'YANMAR','4TNE98-BQDFC',1),(188,'NISSAN','QD32',1),(189,'NISSAN','K21',3),(190,'NISSAN','K25-1608228Y',3),(191,'DOOSAN','DB58S',1),(192,'ISUZU','6BG1',1),(193,'QUANCHAI','QC490GP',1),(194,'WEICHAI','WP10.380E32',1);
/*!40000 ALTER TABLE `mesin` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migration_log`
--

DROP TABLE IF EXISTS `migration_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `migration_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `executed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `description` text COLLATE utf8mb4_general_ci,
  `status` enum('SUCCESS','FAILED','ROLLBACK') COLLATE utf8mb4_general_ci DEFAULT 'SUCCESS',
  `error_message` text COLLATE utf8mb4_general_ci,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migration_log_di_workflow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action` text COLLATE utf8mb4_general_ci,
  `status` enum('SUCCESS','ERROR','SKIPPED') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `error_message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` bigint unsigned NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int unsigned NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_unit` (
  `id_model_unit` int NOT NULL AUTO_INCREMENT,
  `merk_unit` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `model_unit` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
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
-- Table structure for table `notification_rules`
--

DROP TABLE IF EXISTS `notification_rules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_rules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `trigger_event` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'spk_created, spk_approved, di_processed, inventory_low, etc',
  `is_active` tinyint(1) DEFAULT '1',
  `conditions` longtext COLLATE utf8mb4_general_ci COMMENT 'JSON conditions like {"departemen": "DIESEL", "status": "APPROVED"}',
  `target_roles` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Comma-separated: superadmin,manager,supervisor',
  `target_divisions` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Comma-separated: service,marketing,operational',
  `target_departments` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Comma-separated: DIESEL,ELECTRIC,LPG',
  `target_users` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Specific user IDs comma-separated',
  `exclude_creator` tinyint(1) DEFAULT '0' COMMENT 'Exclude notification creator',
  `title_template` varchar(500) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Template with variables like "SPK {{nomor_spk}} untuk {{departemen}}"',
  `message_template` text COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` enum('info','success','warning','error','critical') COLLATE utf8mb4_general_ci DEFAULT 'info',
  `priority` tinyint DEFAULT '1',
  `url_template` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'URL template with variables',
  `delay_minutes` int DEFAULT '0' COMMENT 'Delay notification by X minutes',
  `expire_days` int DEFAULT '30' COMMENT 'Auto-delete after X days',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `auto_include_superadmin` tinyint(1) DEFAULT '1' COMMENT 'Automatically include superadmin in all notifications',
  `target_mixed` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON array for complex multi-targeting: {divisions: [], roles: [], users: [], departments: []}',
  `rule_description` text COLLATE utf8mb4_general_ci COMMENT 'Detailed description of when and why this rule triggers',
  PRIMARY KEY (`id`),
  KEY `idx_trigger_event` (`trigger_event`),
  KEY `idx_active` (`is_active`),
  KEY `idx_rules_event_active` (`trigger_event`,`is_active`),
  CONSTRAINT `notification_rules_chk_1` CHECK (json_valid(`target_mixed`))
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_rules`
--

LOCK TABLES `notification_rules` WRITE;
/*!40000 ALTER TABLE `notification_rules` DISABLE KEYS */;
INSERT INTO `notification_rules` VALUES (3,'DI Ready - Operational Team','Notify operational when DI is ready for processing','di_submitted',1,'{}',NULL,'operational',NULL,NULL,0,'DI Siap Diproses: {{nomor_di}}','Delivery Instruction {{nomor_di}} untuk {{pelanggan}} siap diproses. Lokasi: {{lokasi}}','di','info',2,'/operational/delivery',0,30,NULL,'2025-09-10 03:47:33','2025-10-24 02:42:26',1,NULL,NULL),(20,'Purchase Order Created - Multi-Target','Notify purchase division and specific warehouse user when PO is created','purchase_order_created',1,NULL,NULL,NULL,NULL,NULL,0,'PO Baru: {{po_number}} - {{vendor}}','Purchase Order {{po_number}} telah dibuat untuk vendor {{vendor}} dengan nilai {{amount}}. Silakan lakukan verifikasi dan proses selanjutnya.','purchase_order','info',2,'purchasing/po',0,30,NULL,'2025-09-11 06:20:23','2025-09-11 06:20:23',1,'{\"divisions\": [\"Purchase\", \"Warehouse\"], \"roles\": [\"manager\"], \"users\": [], \"departments\": []}',NULL),(21,'SPK - Created (Marketing → Service)','Marketing membuat SPK, Service menerima notifikasi untuk memproses','spk_created',1,NULL,'supervisor,staff,manager','service',NULL,NULL,1,'SPK Baru: {{nomor_spk}}','SPK baru {{nomor_spk}} telah dibuat untuk pelanggan {{pelanggan}} dengan departemen {{departemen}}. Silakan proses unit sesuai prosedur.','spk','info',3,'/service/spk_service',0,30,NULL,'2025-10-17 03:12:17','2025-10-24 08:41:49',1,NULL,NULL),(27,'Customer - Created','Notifikasi ketika customer baru dibuat','customer_created',1,NULL,'manager','marketing',NULL,NULL,0,'Customer Baru: {{customer_name}}','Customer baru telah ditambahkan: {{customer_name}} ({{customer_code}})','customer','info',2,'/marketing/customer-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(28,'Customer - Updated','Notifikasi ketika data customer diupdate','customer_updated',1,NULL,'staff,supervisor','marketing',NULL,NULL,0,'Customer Diupdate: {{customer_name}}','Data customer {{customer_name}} telah diperbarui','customer','info',1,'/marketing/customer-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(29,'Customer - Deleted','Notifikasi ketika customer dihapus','customer_deleted',1,NULL,'manager','marketing',NULL,NULL,0,'Customer Dihapus: {{customer_name}}','Customer {{customer_name}} telah dihapus dari sistem','customer','warning',3,'/marketing/customer-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(30,'Customer Location - Added','Notifikasi ketika lokasi customer baru ditambahkan','customer_location_added',1,NULL,'staff,supervisor','marketing',NULL,NULL,0,'Lokasi Baru: {{customer_name}}','Lokasi baru \"{{location_name}}\" telah ditambahkan untuk customer {{customer_name}}','customer','info',2,'/marketing/customer-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(31,'Customer Contract - Created','Notifikasi ketika kontrak baru dibuat','customer_contract_created',1,NULL,'manager','marketing,accounting',NULL,NULL,0,'Kontrak Baru: {{contract_number}}','Kontrak baru {{contract_number}} telah dibuat untuk {{customer_name}}','contract','success',3,'/marketing/customer-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(32,'Customer Contract - Expired Warning','Notifikasi ketika kontrak akan expired','customer_contract_expired',1,NULL,'manager','marketing',NULL,NULL,0,'Peringatan Kontrak Expired: {{contract_number}}','Kontrak {{contract_number}} untuk {{customer_name}} akan expired dalam {{days}} hari','contract','critical',5,'/marketing/customer-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(33,'DI - Created (Marketing → Operational)','Marketing membuat DI, Operational menerima notifikasi','di_created',1,NULL,'supervisor,manager,staff','operational',NULL,NULL,0,'DI Baru: {{nomor_di}}','Delivery Instruction baru {{nomor_di}} telah dibuat untuk {{customer}}. Jenis: {{jenis_perintah}}.','delivery','info',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(34,'DI - Approved','Notifikasi ketika DI disetujui','di_approved',1,NULL,'supervisor,staff','warehouse',NULL,NULL,0,'DI Disetujui: {{nomor_di}}','DI {{nomor_di}} telah disetujui dan siap diproses','delivery','success',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(35,'DI - In Progress','Notifikasi ketika DI sedang diproses','di_in_progress',1,NULL,'supervisor,staff','marketing',NULL,NULL,0,'DI Diproses: {{nomor_di}}','DI {{nomor_di}} sedang dalam proses pengiriman','delivery','info',2,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(36,'DI - Delivered','Notifikasi ketika DI selesai dikirim','di_delivered',1,NULL,'supervisor,manager','marketing',NULL,NULL,0,'DI Selesai: {{nomor_di}}','DI {{nomor_di}} telah berhasil dikirim ke {{customer}}','delivery','success',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(37,'DI - Cancelled','Notifikasi ketika DI dibatalkan','di_cancelled',1,NULL,'manager','marketing,operational',NULL,NULL,0,'DI Dibatalkan: {{nomor_di}}','DI {{nomor_di}} telah dibatalkan. Alasan: {{alasan}}','delivery','warning',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(38,'Quotation - Created','Notifikasi ketika penawaran baru dibuat','quotation_created',1,NULL,'manager','marketing',NULL,NULL,0,'Penawaran Baru: {{quotation_number}}','Penawaran baru {{quotation_number}} telah dibuat untuk {{customer}}','quotation','info',2,'/marketing/penawaran',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(41,'Unit Preparation - Started','Notifikasi ketika persiapan unit dimulai','unit_prep_started',1,NULL,'supervisor','service',NULL,NULL,0,'Persiapan Unit Dimulai: {{no_unit}}','Persiapan unit {{no_unit}} untuk SPK {{nomor_spk}} telah dimulai','unit','info',2,'/service/spk_service',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(42,'Unit Preparation - Completed','Notifikasi ketika persiapan unit selesai','unit_prep_completed',1,NULL,'supervisor,manager','operational,marketing',NULL,NULL,0,'Unit Siap: {{no_unit}}','Unit {{no_unit}} telah siap untuk SPK {{nomor_spk}}','unit','success',3,'/service/spk_service',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(44,'Work Order - Created','Notifikasi ketika Work Order baru dibuat','work_order_created',1,NULL,'supervisor,staff','service',NULL,NULL,0,'Work Order Baru: {{nomor_wo}}','Work Order {{nomor_wo}} telah dibuat untuk unit {{no_unit}}','work_order','info',3,'/service/work-orders',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(45,'Work Order - Assigned','Notifikasi ketika WO di-assign ke mekanik','work_order_assigned',1,NULL,'staff','service',NULL,NULL,0,'WO Assigned: {{nomor_wo}}','Work Order {{nomor_wo}} telah di-assign kepada Anda','work_order','warning',3,'/service/work-orders',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(46,'Work Order - In Progress','Notifikasi ketika WO sedang dikerjakan','work_order_in_progress',1,NULL,'supervisor','service',NULL,NULL,0,'WO Dikerjakan: {{nomor_wo}}','Work Order {{nomor_wo}} sedang dalam pengerjaan oleh {{mechanic}}','work_order','info',2,'/service/work-orders',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(47,'Work Order - Completed','Notifikasi ketika WO selesai','work_order_completed',1,NULL,'manager','service',NULL,NULL,0,'WO Selesai: {{nomor_wo}}','Work Order {{nomor_wo}} telah diselesaikan','work_order','success',3,'/service/work-orders',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(48,'Work Order - Cancelled','Notifikasi ketika WO dibatalkan','work_order_cancelled',1,NULL,'manager','service',NULL,NULL,0,'WO Dibatalkan: {{nomor_wo}}','Work Order {{nomor_wo}} telah dibatalkan','work_order','warning',3,'/service/work-orders',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(49,'PMPS - Due Soon','Notifikasi ketika PMPS akan jatuh tempo','pmps_due_soon',1,NULL,'supervisor','service',NULL,NULL,0,'PMPS Due: {{unit_no}}','PMPS untuk unit {{unit_no}} akan jatuh tempo dalam {{days}} hari','maintenance','critical',5,'/service/pmps',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(50,'PMPS - Overdue','Notifikasi ketika PMPS sudah overdue','pmps_overdue',1,NULL,'manager','service',NULL,NULL,0,'PMPS OVERDUE: {{unit_no}}','URGENT! PMPS untuk unit {{unit_no}} sudah OVERDUE {{days}} hari','maintenance','critical',5,'/service/pmps',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(51,'PMPS - Completed','Notifikasi ketika PMPS selesai','pmps_completed',1,NULL,'manager','service',NULL,NULL,0,'PMPS Selesai: {{unit_no}}','PMPS untuk unit {{unit_no}} telah diselesaikan','maintenance','success',2,'/service/pmps',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(52,'Employee - Assigned to Area','Notifikasi ketika employee di-assign ke area','employee_assigned',1,NULL,'staff,manager','service',NULL,NULL,0,'Assignment Baru: {{employee_name}}','{{employee_name}} telah di-assign ke area {{area_name}}','employee','info',3,'/service/area-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(53,'Employee - Unassigned from Area','Notifikasi ketika employee di-unassign dari area','employee_unassigned',1,NULL,'staff,manager','service',NULL,NULL,0,'Assignment Dihapus: {{employee_name}}','{{employee_name}} telah di-unassign dari area {{area_name}}','employee','warning',3,'/service/area-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(54,'Inventory Unit - Added','Notifikasi ketika unit baru ditambahkan','inventory_unit_added',1,NULL,'supervisor,manager','warehouse',NULL,NULL,0,'Unit Baru: {{no_unit}}','Unit baru {{no_unit}} ({{model}}) telah ditambahkan ke inventory','inventory','info',2,'/warehouse/inventory/invent_unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(55,'Inventory Unit - Status Changed','Notifikasi ketika status unit berubah','inventory_unit_status_changed',1,NULL,'supervisor,staff','warehouse',NULL,NULL,0,'Status Unit Berubah: {{no_unit}}','Status unit {{no_unit}} berubah dari {{old_status}} menjadi {{new_status}}','inventory','info',2,'/warehouse/inventory/invent_unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(56,'Inventory Unit - Rental Active','Notifikasi ketika unit mulai di-rental','inventory_unit_rental_active',1,NULL,'supervisor','marketing,warehouse',NULL,NULL,0,'Unit Di-Rental: {{no_unit}}','Unit {{no_unit}} telah aktif di-rental ke {{customer}}','inventory','success',3,'/warehouse/inventory/invent_unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(57,'Inventory Unit - Returned','Notifikasi ketika unit dikembalikan','inventory_unit_returned',1,NULL,'supervisor','warehouse,service',NULL,NULL,0,'Unit Kembali: {{no_unit}}','Unit {{no_unit}} telah dikembalikan dari {{customer}}','inventory','info',3,'/warehouse/inventory/invent_unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(58,'Inventory Unit - Maintenance','Notifikasi ketika unit masuk maintenance','inventory_unit_maintenance',1,NULL,'supervisor,staff','service',NULL,NULL,0,'Unit Maintenance: {{no_unit}}','Unit {{no_unit}} masuk maintenance. Alasan: {{alasan}}','inventory','warning',3,'/warehouse/inventory/invent_unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(59,'Inventory Unit - Low Stock','Notifikasi ketika stock unit rendah','inventory_unit_low_stock',1,NULL,'manager','warehouse',NULL,NULL,0,'Low Stock Alert: {{tipe}}','Peringatan! Stock unit {{tipe}} rendah ({{count}} unit)','inventory','critical',5,'/warehouse/inventory/invent_unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(60,'Attachment - Added','Notifikasi ketika attachment/battery/charger baru ditambahkan','attachment_added',1,NULL,'supervisor','warehouse',NULL,NULL,0,'{{tipe_item}} Baru: {{serial_number}}','{{tipe_item}} baru dengan SN {{serial_number}} telah ditambahkan','inventory','info',2,'/warehouse/inventory/invent_attachment',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(61,'Attachment - Attached to Unit','Notifikasi ketika item dipasang ke unit','attachment_attached',1,NULL,'staff','service',NULL,NULL,0,'{{tipe_item}} Dipasang: {{no_unit}}','{{tipe_item}} {{serial_number}} telah dipasang ke unit {{no_unit}}','inventory','info',2,'/warehouse/inventory/invent_attachment',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(62,'Attachment - Detached from Unit','Notifikasi ketika item dilepas dari unit','attachment_detached',1,NULL,'staff','service',NULL,NULL,0,'{{tipe_item}} Dilepas: {{no_unit}}','{{tipe_item}} {{serial_number}} telah dilepas dari unit {{no_unit}}','inventory','info',2,'/warehouse/inventory/invent_attachment',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(63,'Attachment - Swapped','Notifikasi ketika item di-swap antar unit','attachment_swapped',1,NULL,'supervisor','service',NULL,NULL,0,'{{tipe_item}} Swap: {{old_unit}} → {{new_unit}}','{{tipe_item}} {{serial_number}} di-swap dari unit {{old_unit}} ke unit {{new_unit}}','inventory','warning',3,'/warehouse/inventory/invent_attachment',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(64,'Attachment - Maintenance','Notifikasi ketika item masuk maintenance','attachment_maintenance',1,NULL,'supervisor','service',NULL,NULL,0,'{{tipe_item}} Maintenance: {{serial_number}}','{{tipe_item}} {{serial_number}} masuk maintenance','inventory','warning',3,'/warehouse/inventory/invent_attachment',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(65,'Attachment - Broken','Notifikasi ketika item rusak','attachment_broken',1,NULL,'manager','warehouse',NULL,NULL,0,'{{tipe_item}} RUSAK: {{serial_number}}','URGENT! {{tipe_item}} {{serial_number}} dilaporkan RUSAK','inventory','critical',5,'/warehouse/inventory/invent_attachment',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(66,'Sparepart - Added','Notifikasi ketika sparepart baru ditambahkan','sparepart_added',1,NULL,'supervisor','warehouse',NULL,NULL,0,'Sparepart Baru: {{nama_sparepart}}','Sparepart {{nama_sparepart}} telah ditambahkan (Qty: {{qty}})','inventory','info',2,'/warehouse/inventory/invent_sparepart',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(67,'Sparepart - Used','Notifikasi ketika sparepart digunakan','sparepart_used',1,NULL,'staff','warehouse',NULL,NULL,0,'Sparepart Digunakan: {{nama_sparepart}}','Sparepart {{nama_sparepart}} digunakan (Qty: {{qty}})','inventory','info',1,'/warehouse/inventory/invent_sparepart',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(68,'Sparepart - Low Stock','Notifikasi ketika stock sparepart rendah','sparepart_low_stock',1,NULL,'manager','warehouse,purchasing',NULL,NULL,0,'Low Stock Alert: {{nama_sparepart}}','Peringatan! Stock {{nama_sparepart}} rendah (Sisa: {{qty}})','inventory','critical',5,'/warehouse/inventory/invent_sparepart',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(69,'Sparepart - Out of Stock','Notifikasi ketika sparepart habis','sparepart_out_of_stock',1,NULL,'manager','purchasing',NULL,NULL,0,'OUT OF STOCK: {{nama_sparepart}}','URGENT! {{nama_sparepart}} HABIS! Segera lakukan pemesanan','inventory','critical',5,'/warehouse/inventory/invent_sparepart',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(70,'PO - Created (Purchasing → Warehouse)','Notifikasi ketika PO Unit baru dibuat','po_unit_created',1,NULL,'supervisor,manager','warehouse',NULL,NULL,0,'PO Unit Baru: {{nomor_po}}','Purchase Order Unit {{nomor_po}} telah dibuat untuk {{supplier}}','purchase_order','info',3,'/purchasing/po-unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(71,'PO - Created (Purchasing → Warehouse)','Notifikasi ketika PO Attachment baru dibuat','po_attachment_created',1,NULL,'supervisor,manager','warehouse',NULL,NULL,0,'PO Attachment Baru: {{nomor_po}}','Purchase Order Attachment {{nomor_po}} telah dibuat untuk {{supplier}}','purchase_order','info',3,'/purchasing/po-attachment',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(72,'PO - Created (Purchasing → Warehouse)','Notifikasi ketika PO Sparepart baru dibuat','po_sparepart_created',1,NULL,'supervisor,manager','warehouse',NULL,NULL,0,'PO Sparepart Baru: {{nomor_po}}','Purchase Order Sparepart {{nomor_po}} telah dibuat untuk {{supplier}}','purchase_order','info',3,'/purchasing/po-sparepart',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(73,'PO - Approved','Notifikasi ketika PO disetujui','po_approved',1,NULL,'staff,supervisor','purchasing',NULL,NULL,0,'PO Disetujui: {{nomor_po}}','Purchase Order {{nomor_po}} telah disetujui','purchase_order','success',3,'/purchasing/po-unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(74,'PO - Rejected','Notifikasi ketika PO ditolak','po_rejected',1,NULL,'staff,supervisor','purchasing',NULL,NULL,0,'PO Ditolak: {{nomor_po}}','Purchase Order {{nomor_po}} ditolak. Alasan: {{alasan}}','purchase_order','warning',3,'/purchasing/po-unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(75,'PO - Received','Notifikasi ketika barang PO diterima','po_received',1,NULL,'supervisor,manager','purchasing',NULL,NULL,0,'PO Diterima: {{nomor_po}}','Barang untuk PO {{nomor_po}} telah diterima','purchase_order','success',3,'/warehouse/purchase-orders',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:41:49',1,NULL,NULL),(76,'PO - Verified','Notifikasi ketika PO terverifikasi','po_verified',1,NULL,'manager','purchasing,accounting',NULL,NULL,0,'PO Terverifikasi: {{nomor_po}}','Purchase Order {{nomor_po}} telah terverifikasi','purchase_order','success',2,'/purchasing/po-unit',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(77,'Supplier - Created','Notifikasi ketika supplier baru dibuat','supplier_created',1,NULL,'manager','purchasing',NULL,NULL,0,'Supplier Baru: {{supplier_name}}','Supplier baru {{supplier_name}} telah ditambahkan ke sistem','supplier','info',2,'/purchasing/supplier-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(78,'Supplier - Updated','Notifikasi ketika data supplier diupdate','supplier_updated',1,NULL,'staff,supervisor','purchasing',NULL,NULL,0,'Supplier Diupdate: {{supplier_name}}','Data supplier {{supplier_name}} telah diperbarui','supplier','info',1,'/purchasing/supplier-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(79,'Supplier - Deleted','Notifikasi ketika supplier dihapus','supplier_deleted',1,NULL,'manager','purchasing',NULL,NULL,0,'Supplier Dihapus: {{supplier_name}}','Supplier {{supplier_name}} telah dihapus dari sistem','supplier','warning',3,'/purchasing/supplier-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(80,'Delivery - Created','Notifikasi ketika delivery baru dibuat','delivery_created',1,NULL,'staff,supervisor','operational',NULL,NULL,0,'Delivery Baru: {{nomor_delivery}}','Delivery {{nomor_delivery}} telah dibuat untuk {{customer}}','delivery','info',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(81,'Delivery - Assigned','Notifikasi ketika delivery di-assign ke driver','delivery_assigned',1,NULL,'staff','operational',NULL,NULL,0,'Delivery Assigned: {{nomor_delivery}}','Delivery {{nomor_delivery}} telah di-assign kepada Anda','delivery','warning',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(82,'Delivery - In Transit','Notifikasi ketika delivery dalam perjalanan','delivery_in_transit',1,NULL,'staff,supervisor','marketing,operational',NULL,NULL,0,'Delivery Dalam Perjalanan: {{nomor_delivery}}','Delivery {{nomor_delivery}} sedang dalam perjalanan ke {{customer}}','delivery','info',2,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(83,'Delivery - Arrived','Notifikasi ketika delivery sampai tujuan','delivery_arrived',1,NULL,'supervisor,manager','marketing',NULL,NULL,0,'Delivery Sampai: {{nomor_delivery}}','Delivery {{nomor_delivery}} telah sampai di lokasi {{customer}}','delivery','success',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(84,'Delivery - Completed','Notifikasi ketika delivery selesai','delivery_completed',1,NULL,'manager','marketing',NULL,NULL,0,'Delivery Selesai: {{nomor_delivery}}','Delivery {{nomor_delivery}} telah selesai untuk {{customer}}','delivery','success',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(85,'Delivery - Delayed','Notifikasi ketika delivery terlambat','delivery_delayed',1,NULL,'manager','operational',NULL,NULL,0,'Delivery TERLAMBAT: {{nomor_delivery}}','URGENT! Delivery {{nomor_delivery}} mengalami keterlambatan','delivery','critical',5,'/operational/delivery',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(86,'Invoice - Created','Notifikasi ketika invoice baru dibuat','invoice_created',1,NULL,'staff,supervisor','accounting',NULL,NULL,0,'Invoice Baru: {{invoice_number}}','Invoice {{invoice_number}} telah dibuat untuk {{customer}}','invoice','info',3,'/accounting/invoice-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(87,'Invoice - Sent','Notifikasi ketika invoice dikirim ke customer','invoice_sent',1,NULL,'supervisor','accounting',NULL,NULL,0,'Invoice Terkirim: {{invoice_number}}','Invoice {{invoice_number}} telah dikirim ke {{customer}}','invoice','info',2,'/accounting/invoice-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(88,'Invoice - Paid','Notifikasi ketika invoice dibayar','invoice_paid',1,NULL,'manager','accounting',NULL,NULL,0,'Invoice Lunas: {{invoice_number}}','Invoice {{invoice_number}} telah LUNAS ({{amount}})','invoice','success',3,'/accounting/invoice-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(89,'Invoice - Overdue','Notifikasi ketika invoice overdue','invoice_overdue',1,NULL,'manager','accounting',NULL,NULL,0,'Invoice OVERDUE: {{invoice_number}}','URGENT! Invoice {{invoice_number}} untuk {{customer}} sudah OVERDUE','invoice','critical',5,'/accounting/invoice-management',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(90,'Payment - Received','Notifikasi ketika payment diterima','payment_received',1,NULL,'staff,supervisor','accounting',NULL,NULL,0,'Payment Diterima: {{amount}}','Payment sebesar {{amount}} telah diterima dari {{customer}}','payment','success',3,'/accounting/payment-validation',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(91,'User - Created','Notifikasi ketika user baru dibuat','user_created',1,NULL,'admin','admin',NULL,NULL,0,'User Baru: {{username}}','User baru {{username}} telah dibuat dengan role {{role}}','user','info',3,'/admin/advanced-users',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(92,'User - Updated','Notifikasi ketika data user diupdate','user_updated',1,NULL,'admin','admin',NULL,NULL,0,'User Diupdate: {{username}}','Data user {{username}} telah diperbarui','user','info',1,'/admin/advanced-users',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(93,'User - Deleted','Notifikasi ketika user dihapus','user_deleted',1,NULL,'admin','admin',NULL,NULL,0,'User Dihapus: {{username}}','User {{username}} telah dihapus dari sistem','user','warning',3,'/admin/advanced-users',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(94,'User - Activated','Notifikasi ketika user diaktifkan','user_activated',1,NULL,'staff',NULL,NULL,NULL,0,'Akun Aktif: {{username}}','Akun Anda telah diaktifkan. Selamat datang di OPTIMA!','user','success',3,'/dashboard',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(95,'User - Deactivated','Notifikasi ketika user dinonaktifkan','user_deactivated',1,NULL,'staff,admin','admin',NULL,NULL,0,'Akun Nonaktif: {{username}}','Akun {{username}} telah dinonaktifkan','user','warning',3,'/admin/advanced-users',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(96,'Password - Reset','Notifikasi ketika password di-reset','password_reset',1,NULL,'staff',NULL,NULL,NULL,0,'Password Reset: {{username}}','Password untuk akun {{username}} telah di-reset','user','warning',3,'/login',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(97,'Role - Created','Notifikasi ketika role baru dibuat','role_created',1,NULL,'admin','admin',NULL,NULL,0,'Role Baru: {{role_name}}','Role baru {{role_name}} telah dibuat','role','info',2,'/admin/advanced-users',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(98,'Role - Updated','Notifikasi ketika role diupdate','role_updated',1,NULL,'admin','admin',NULL,NULL,0,'Role Diupdate: {{role_name}}','Role {{role_name}} telah diperbarui','role','info',1,'/admin/advanced-users',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(99,'Permission - Changed','Notifikasi ketika permission user berubah','permission_changed',1,NULL,'staff',NULL,NULL,NULL,0,'Permission Diupdate','Permission Anda telah diperbarui. Silakan login ulang untuk melihat perubahan','permission','warning',3,'/dashboard',0,30,NULL,'2025-10-24 08:11:17','2025-10-24 08:11:17',1,NULL,NULL),(100,'SPK - Assigned to Mechanic','SPK di-assign ke mechanic tertentu','spk_assigned',1,NULL,'staff','service',NULL,NULL,0,'SPK Assigned: {{nomor_spk}}','SPK {{nomor_spk}} telah di-assign kepada Anda. Silakan proses unit {{no_unit}}.','spk','warning',3,'/service/spk_service',0,30,NULL,'2025-10-24 08:41:49','2025-10-24 08:41:49',1,NULL,NULL),(101,'Unit Preparation - Started','Persiapan unit dimulai oleh Service','unit_prep_started',1,NULL,'supervisor,manager','service',NULL,NULL,0,'Persiapan Unit Dimulai: {{no_unit}}','Persiapan unit {{no_unit}} untuk SPK {{nomor_spk}} telah dimulai oleh {{mechanic}}.','spk','info',2,'/service/spk_service',0,30,NULL,'2025-10-24 08:41:49','2025-10-24 08:41:49',1,NULL,NULL),(102,'Unit Preparation - Completed (Service → Marketing)','Unit siap, notify Marketing & Operational','unit_prep_completed',1,NULL,'supervisor,manager,staff','marketing,operational',NULL,NULL,0,'Unit Siap: {{no_unit}}','Unit {{no_unit}} telah selesai disiapkan untuk SPK {{nomor_spk}}. Siap untuk proses delivery.','spk','success',3,'/operational/delivery',0,30,NULL,'2025-10-24 08:41:49','2025-10-24 08:41:49',1,NULL,NULL),(103,'SPK - Cancelled','SPK dibatalkan, notify semua stakeholder','spk_cancelled',1,NULL,'supervisor,manager','service,marketing,operational',NULL,NULL,0,'SPK Dibatalkan: {{nomor_spk}}','SPK {{nomor_spk}} telah dibatalkan. Alasan: {{alasan}}.','spk','warning',3,'/marketing/spk',0,30,NULL,'2025-10-24 08:41:49','2025-10-24 08:41:49',1,NULL,NULL);
/*!40000 ALTER TABLE `notification_rules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_rules_backup_20250116`
--

DROP TABLE IF EXISTS `notification_rules_backup_20250116`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_rules_backup_20250116` (
  `id` int NOT NULL DEFAULT '0',
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `trigger_event` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'spk_created, spk_approved, di_processed, inventory_low, etc',
  `is_active` tinyint(1) DEFAULT '1',
  `conditions` longtext COLLATE utf8mb4_general_ci COMMENT 'JSON conditions like {"departemen": "DIESEL", "status": "APPROVED"}',
  `target_roles` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Comma-separated: superadmin,manager,supervisor',
  `target_divisions` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Comma-separated: service,marketing,operational',
  `target_departments` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Comma-separated: DIESEL,ELECTRIC,LPG',
  `target_users` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Specific user IDs comma-separated',
  `exclude_creator` tinyint(1) DEFAULT '0' COMMENT 'Exclude notification creator',
  `title_template` varchar(500) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Template with variables like "SPK {{nomor_spk}} untuk {{departemen}}"',
  `message_template` text COLLATE utf8mb4_general_ci NOT NULL,
  `category` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `type` enum('info','success','warning','error','critical') COLLATE utf8mb4_general_ci DEFAULT 'info',
  `priority` tinyint DEFAULT '1',
  `url_template` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'URL template with variables',
  `delay_minutes` int DEFAULT '0' COMMENT 'Delay notification by X minutes',
  `expire_days` int DEFAULT '30' COMMENT 'Auto-delete after X days',
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `auto_include_superadmin` tinyint(1) DEFAULT '1' COMMENT 'Automatically include superadmin in all notifications',
  `target_mixed` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON array for complex multi-targeting: {divisions: [], roles: [], users: [], departments: []}',
  `rule_description` text COLLATE utf8mb4_general_ci COMMENT 'Detailed description of when and why this rule triggers',
  CONSTRAINT `notification_rules_backup_20250116_chk_1` CHECK (json_valid(`target_mixed`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_rules_backup_20250116`
--

LOCK TABLES `notification_rules_backup_20250116` WRITE;
/*!40000 ALTER TABLE `notification_rules_backup_20250116` DISABLE KEYS */;
INSERT INTO `notification_rules_backup_20250116` VALUES (1,'SPK Created - Service Notification','Notify service division when new SPK is created','spk_created',1,'{}','manager,supervisor,technician','service',NULL,NULL,1,'SPK Baru: {{nomor_spk}} - {{departemen}}','SPK baru telah dibuat untuk {{pelanggan}} dengan departemen {{departemen}}. Silakan periksa dan proses sesuai prosedur.','spk','info',2,'service/spk_service',0,30,NULL,'2025-09-10 03:47:33','2025-10-15 19:36:11',1,NULL,NULL),(2,'SPK DIESEL - Service DIESEL Team','Notify DIESEL service team for DIESEL SPK','spk_created',1,'{\"departemen\": \"DIESEL\"}',NULL,'service','DIESEL',NULL,0,'SPK DIESEL: {{nomor_spk}} - {{pelanggan}}','SPK DIESEL baru memerlukan perhatian tim service DIESEL. Unit: {{unit_info}}, Lokasi: {{lokasi}}','spk','warning',3,'service/spk_service',0,30,NULL,'2025-09-10 03:47:33','2025-09-11 04:45:27',1,NULL,NULL),(3,'DI Ready - Operational Team','Notify operational when DI is ready for processing','di_submitted',1,'{}',NULL,'operational',NULL,NULL,0,'DI Siap Diproses: {{nomor_di}}','Delivery Instruction {{nomor_di}} untuk {{pelanggan}} siap diproses. Lokasi: {{lokasi}}','di','info',2,'/operational/delivery',0,30,NULL,'2025-09-10 03:47:33','2025-09-10 03:47:33',1,NULL,NULL),(4,'Low Stock Alert','Notify warehouse managers when inventory is low','inventory_low_stock',1,'{\"stock_level\": \"below_minimum\"}','manager,supervisor','warehouse,purchasing',NULL,NULL,0,'Stok Rendah: {{item_name}}','Item {{item_name}} memiliki stok di bawah minimum. Stok saat ini: {{current_stock}}, Minimum: {{minimum_stock}}','inventory','warning',3,'/warehouse/inventory',0,30,NULL,'2025-09-10 03:47:33','2025-09-10 03:47:33',1,NULL,NULL),(5,'Maintenance Due Alert','Notify service team when unit maintenance is due','maintenance_due',1,'{}',NULL,'service','DIESEL,ELECTRIC,LPG',NULL,0,'Maintenance Due: {{unit_no}}','Unit {{unit_no}} memerlukan maintenance {{maintenance_type}}. Due date: {{due_date}}','maintenance','warning',3,'/service/maintenance',0,30,NULL,'2025-09-10 03:47:33','2025-09-10 03:47:33',1,NULL,NULL),(6,'SPK DIESEL to Service DIESEL','Notifikasi untuk SPK departemen DIESEL ke divisi Service','spk_created',1,'{\"source_department\": \"diesel\", \"target_division\": \"service\"}',NULL,'service','diesel',NULL,0,'SPK Baru - {departemen} #{spk_id}','SPK baru telah dibuat untuk departemen {departemen}. Silakan review dan proses sesuai prosedur.',NULL,'info',2,'service/spk_service',0,30,1,'2025-09-10 03:58:43','2025-09-11 04:45:27',1,NULL,NULL),(7,'DI Processing Alert','Alert untuk DI yang perlu diproses','di_created',1,'{}',NULL,'service','',NULL,0,'DI Baru Perlu Diproses - #{di_id}','Delivery Instruction baru telah dibuat dan menunggu pemrosesan dari divisi yang bertanggung jawab.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:58:43','2025-09-10 03:58:43',1,NULL,NULL),(8,'Low Stock Alert','Alert untuk stok rendah','inventory_low_stock',1,'{}',NULL,'','',NULL,0,'Stok Rendah - {item_name}','Item {item_name} memiliki stok rendah ({current_stock} tersisa). Segera lakukan reorder.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:58:43','2025-09-10 03:58:43',1,NULL,NULL),(9,'Maintenance Due Alert','Alert untuk maintenance yang jatuh tempo','maintenance_due',1,'{}',NULL,'service','',NULL,0,'Maintenance Terjadwal - Unit {unit_code}','Unit {unit_code} memerlukan maintenance terjadwal. Silakan koordinasi dengan tim maintenance.',NULL,'info',2,NULL,0,30,1,'2025-09-10 03:58:43','2025-09-10 03:58:43',1,NULL,NULL),(10,'SPK DIESEL to Service DIESEL','Notifikasi untuk SPK departemen DIESEL ke divisi Service','spk_created',1,'{\"source_department\": \"diesel\", \"target_division\": \"service\"}',NULL,'service','diesel',NULL,0,'SPK Baru - {departemen} #{spk_id}','SPK baru telah dibuat untuk departemen {departemen}. Silakan review dan proses sesuai prosedur.',NULL,'info',2,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(11,'DI Processing Alert','Alert untuk DI yang perlu diproses','di_created',1,'{}',NULL,'service','',NULL,0,'DI Baru Perlu Diproses - #{di_id}','Delivery Instruction baru telah dibuat dan menunggu pemrosesan dari divisi yang bertanggung jawab.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(12,'Low Stock Alert','Alert untuk stok rendah','inventory_low_stock',1,'{}',NULL,'','',NULL,0,'Stok Rendah - {item_name}','Item {item_name} memiliki stok rendah ({current_stock} tersisa). Segera lakukan reorder.',NULL,'info',3,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(13,'Maintenance Due Alert','Alert untuk maintenance yang jatuh tempo','maintenance_due',1,'{}',NULL,'service','',NULL,0,'Maintenance Terjadwal - Unit {unit_code}','Unit {unit_code} memerlukan maintenance terjadwal. Silakan koordinasi dengan tim maintenance.',NULL,'info',2,NULL,0,30,1,'2025-09-10 03:59:35','2025-09-10 03:59:35',1,NULL,NULL),(14,'SPK Created - Service Notification','Notify service division when new SPK is created','SPK Created',1,'{}','superadmin','','','',1,'SPK Baru: {{nomor_spk}} - {{departemen}}','SPK baru telah dibuat untuk {{pelanggan}} dengan departemen {{departemen}}. Silakan periksa dan proses sesuai prosedur.','spk','info',2,'/service/spk/detail/{{id}}',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 21:56:11',1,NULL,NULL),(15,'SPK DIESEL - Service DIESEL Team','Notify DIESEL service team for DIESEL SPK','spk_created',1,'{\"departemen\": \"DIESEL\"}',NULL,'service','DIESEL',NULL,0,'SPK DIESEL: {{nomor_spk}} - {{pelanggan}}','SPK DIESEL baru memerlukan perhatian tim service DIESEL. Unit: {{unit_info}}, Lokasi: {{lokasi}}','spk','warning',3,'/service/spk/detail/{{id}}',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 06:37:37',1,NULL,NULL),(16,'DI Ready - Operational Team','Notify operational when DI is ready for processing','di_submitted',1,'{}',NULL,'operational',NULL,NULL,0,'DI Siap Diproses: {{nomor_di}}','Delivery Instruction {{nomor_di}} untuk {{pelanggan}} siap diproses. Lokasi: {{lokasi}}','di','info',2,'/operational/delivery',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 06:37:37',1,NULL,NULL),(18,'Maintenance Due Alert','Notify service team when unit maintenance is due','maintenance_due',1,'{}',NULL,'service','DIESEL,ELECTRIC,LPG',NULL,0,'Maintenance Due: {{unit_no}}','Unit {{unit_no}} memerlukan maintenance {{maintenance_type}}. Due date: {{due_date}}','maintenance','warning',3,'/service/maintenance',0,30,NULL,'2025-09-10 06:37:37','2025-09-10 06:37:37',1,NULL,NULL),(19,'SPK Created - Superadmin Notification','Notify superadmin when new SPK is created for oversight','spk_created',1,NULL,'Super Administrator',NULL,NULL,NULL,0,'SPK Baru: {{nomor_spk}} - {{pelanggan}}','SPK {{nomor_spk}} telah dibuat untuk {{pelanggan}} dengan spesifikasi {{spesifikasi}}. Lokasi: {{lokasi}}. Status: Menunggu persiapan service.','spk','info',3,'service/spk_service',0,30,NULL,'2025-09-11 05:01:25','2025-09-11 05:01:25',1,NULL,NULL),(20,'Purchase Order Created - Multi-Target','Notify purchase division and specific warehouse user when PO is created','purchase_order_created',1,NULL,NULL,NULL,NULL,NULL,0,'PO Baru: {{po_number}} - {{vendor}}','Purchase Order {{po_number}} telah dibuat untuk vendor {{vendor}} dengan nilai {{amount}}. Silakan lakukan verifikasi dan proses selanjutnya.','purchase_order','info',2,'purchasing/po',0,30,NULL,'2025-09-11 06:20:23','2025-09-11 06:20:23',1,'{\"divisions\": [\"Purchase\", \"Warehouse\"], \"roles\": [\"manager\"], \"users\": [], \"departments\": []}',NULL);
/*!40000 ALTER TABLE `notification_rules_backup_20250116` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notifications` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `type` enum('info','success','warning','error') COLLATE utf8mb4_unicode_ci DEFAULT 'info',
  `icon` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'bell',
  `related_module` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'spk, work_order, po, delivery, etc',
  `related_id` int DEFAULT NULL COMMENT 'ID of related record',
  `url` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Click action URL',
  `is_read` tinyint(1) DEFAULT '0',
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_related` (`related_module`,`related_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notifications`
--

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (15,1,'SPK Baru: SPK/202510/003 - N/A','SPK baru telah dibuat untuk Sarana Mitra Luas dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',66,'http://localhost/optima1/public/service/spk/detail/66',1,'2025-10-17 02:25:30','2025-10-17 02:25:08','2025-10-17 09:25:30'),(16,24,'SPK Baru: SPK/202510/004 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',67,'http://localhost/optima1/public/service/spk/detail/67',1,'2025-11-20 03:55:09','2025-10-22 21:42:39','2025-11-20 03:55:09'),(17,1,'SPK Baru: SPK/202510/004 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',67,'http://localhost/optima1/public/service/spk/detail/67',1,'2025-10-22 23:31:22','2025-10-22 21:42:39','2025-10-23 06:31:22'),(18,24,'SPK Baru: SPK/202510/005 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',68,'http://localhost/optima1/public/service/spk/detail/68',1,'2025-11-20 03:55:09','2025-10-22 21:45:39','2025-11-20 03:55:09'),(19,1,'SPK Baru: SPK/202510/005 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',68,'http://localhost/optima1/public/service/spk/detail/68',1,'2025-10-22 23:31:22','2025-10-22 21:45:39','2025-10-23 06:31:22'),(20,24,'SPK Baru: SPK/202510/006 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',69,'http://localhost/optima1/public/service/spk/detail/69',1,'2025-11-20 03:55:09','2025-10-22 23:31:05','2025-11-20 03:55:09'),(21,1,'SPK Baru: SPK/202510/006 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',69,'http://localhost/optima1/public/service/spk/detail/69',1,'2025-10-22 23:31:16','2025-10-22 23:31:05','2025-10-23 06:31:16'),(22,24,'SPK Baru: SPK/202510/007 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',70,'http://localhost/optima1/public/service/spk/detail/70',1,'2025-11-20 03:55:09','2025-10-22 23:34:07','2025-11-20 03:55:09'),(23,1,'SPK Baru: SPK/202510/007 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',70,'http://localhost/optima1/public/service/spk/detail/70',1,'2025-10-23 02:53:11','2025-10-22 23:34:07','2025-10-23 09:53:11'),(24,24,'SPK Baru: SPK/202510/008 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',71,'http://localhost/optima1/public/service/spk/detail/71',1,'2025-11-20 03:55:09','2025-10-22 23:36:38','2025-11-20 03:55:09'),(25,1,'SPK Baru: SPK/202510/008 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',71,'http://localhost/optima1/public/service/spk/detail/71',1,'2025-10-23 02:53:11','2025-10-22 23:36:38','2025-10-23 09:53:11'),(26,24,'SPK Baru: SPK/202510/009 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',72,'http://localhost/optima1/public/service/spk/detail/72',1,'2025-11-20 03:55:09','2025-10-24 01:33:36','2025-11-20 03:55:09'),(27,1,'SPK Baru: SPK/202510/009 - N/A','SPK baru telah dibuat untuk Test dengan departemen N/A. Silakan periksa dan proses sesuai prosedur.','info','bell','spk',72,'http://localhost/optima1/public/service/spk/detail/72',1,'2025-10-24 01:43:09','2025-10-24 01:33:36','2025-10-24 08:43:09'),(28,24,'SPK Baru: SPK/202510/010','SPK baru SPK/202510/010 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',73,'http://localhost/optima1/public/service/spk/detail/73',1,'2025-11-20 03:55:09','2025-10-24 01:43:33','2025-11-20 03:55:09'),(29,1,'SPK Baru: SPK/202510/010','SPK baru SPK/202510/010 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',73,'http://localhost/optima1/public/service/spk/detail/73',1,'2025-10-24 01:50:29','2025-10-24 01:43:33','2025-10-24 08:50:29'),(30,24,'SPK Baru: SPK/202510/011','SPK baru SPK/202510/011 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',74,'http://localhost/optima1/public/service/spk/detail/74',1,'2025-11-20 03:55:09','2025-10-24 02:29:25','2025-11-20 03:55:09'),(31,1,'SPK Baru: SPK/202510/011','SPK baru SPK/202510/011 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',74,'http://localhost/optima1/public/service/spk/detail/74',1,'2025-10-24 02:35:37','2025-10-24 02:29:25','2025-10-24 09:35:37'),(32,24,'SPK Baru: SPK/202510/012','SPK baru SPK/202510/012 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',75,'http://localhost/optima1/public/service/spk/detail/75',1,'2025-11-20 03:55:09','2025-10-24 02:35:54','2025-11-20 03:55:09'),(33,1,'SPK Baru: SPK/202510/012','SPK baru SPK/202510/012 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',75,'http://localhost/optima1/public/service/spk/detail/75',1,'2025-10-24 02:40:55','2025-10-24 02:35:54','2025-10-24 09:40:55'),(34,24,'SPK Baru: SPK/202510/013','SPK baru SPK/202510/013 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',76,'http://localhost/optima1/public/service/spk/detail/76',1,'2025-11-20 03:55:09','2025-10-24 02:41:11','2025-11-20 03:55:09'),(35,1,'SPK Baru: SPK/202510/013','SPK baru SPK/202510/013 telah dibuat untuk pelanggan Test dengan departemen N/A. Silakan proses unit sesuai prosedur.','info','bell','spk',76,'http://localhost/optima1/public/service/spk/detail/76',1,'2025-10-24 02:42:36','2025-10-24 02:41:11','2025-10-24 09:42:36');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `optimization_additional_log`
--

DROP TABLE IF EXISTS `optimization_additional_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `optimization_additional_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operation_type` enum('FK_CONSTRAINT','INDEX','TRIGGER','PROCEDURE') COLLATE utf8mb4_general_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `constraint_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('SUCCESS','ERROR','SKIPPED') COLLATE utf8mb4_general_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `optimization_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `operation_type` enum('FK_CONSTRAINT','INDEX','TRIGGER','PROCEDURE') COLLATE utf8mb4_general_ci NOT NULL,
  `table_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `constraint_name` varchar(200) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `action` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('SUCCESS','ERROR','SKIPPED') COLLATE utf8mb4_general_ci NOT NULL,
  `error_message` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- Table structure for table `password_resets`
--

DROP TABLE IF EXISTS `password_resets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_resets` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `attempts` int NOT NULL DEFAULT '0',
  `max_attempts` int NOT NULL DEFAULT '5',
  `is_used` tinyint(1) NOT NULL DEFAULT '0',
  `expires_at` datetime NOT NULL,
  `used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_token` (`token`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_is_used` (`is_used`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_resets`
--

LOCK TABLES `password_resets` WRITE;
/*!40000 ALTER TABLE `password_resets` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_resets` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `is_system_permission` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `level` tinyint(1) DEFAULT '1' COMMENT 'Permission level: 1=view, 2=edit, 3=full',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=160 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'Admin View','admin.view','View admin module','admin','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(2,'Admin Edit','admin.edit','Edit admin module','admin','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(3,'Admin Full','admin.full','Full admin access','admin','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(4,'Marketing View','marketing.view','View marketing module','marketing','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(5,'Marketing Edit','marketing.edit','Edit marketing module','marketing','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(6,'Marketing Full','marketing.full','Full marketing access','marketing','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(7,'Service View','service.view','View service module','service','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(8,'Service Edit','service.edit','Edit service module','service','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(9,'Service Full','service.full','Full service access','service','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(10,'Purchasing View','purchasing.view','View purchasing module','purchasing','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(11,'Purchasing Edit','purchasing.edit','Edit purchasing module','purchasing','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(12,'Purchasing Full','purchasing.full','Full purchasing access','purchasing','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(13,'Warehouse View','warehouse.view','View warehouse module','warehouse','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(14,'Warehouse Edit','warehouse.edit','Edit warehouse module','warehouse','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(15,'Warehouse Full','warehouse.full','Full warehouse access','warehouse','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(16,'Operational View','operational.view','View operational module','operational','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(17,'Operational Edit','operational.edit','Edit operational module','operational','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(18,'Operational Full','operational.full','Full operational access','operational','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(19,'Perizinan View','perizinan.view','View perizinan module','perizinan','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(20,'Perizinan Edit','perizinan.edit','Edit perizinan module','perizinan','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(21,'Perizinan Full','perizinan.full','Full perizinan access','perizinan','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(22,'Accounting View','accounting.view','View accounting module','accounting','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',1),(23,'Accounting Edit','accounting.edit','Edit accounting module','accounting','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',2),(24,'Accounting Full','accounting.full','Full accounting access','accounting','general',0,1,'2025-10-20 06:55:56','2025-10-20 06:55:56',3),(139,'Export Service Data','service.export','Export service data (included in full access)','service','general',0,1,'2025-10-20 08:30:13','2025-10-20 08:30:13',3),(140,'Export Marketing Data','marketing.export','Export marketing data (included in full access)','marketing','general',0,1,'2025-10-20 08:30:13','2025-10-20 08:30:13',3),(141,'Export Purchasing Data','purchasing.export','Export purchasing data (included in full access)','purchasing','general',0,1,'2025-10-20 08:30:13','2025-10-20 08:30:13',3),(142,'Export Warehouse Data','warehouse.export','Export warehouse data (included in full access)','warehouse','general',0,1,'2025-10-20 08:30:13','2025-10-20 08:30:13',3),(143,'Export Operational Data','operational.export','Export operational data (included in full access)','operational','general',0,1,'2025-10-20 08:30:13','2025-10-20 08:30:13',3),(144,'Export Perizinan Data','perizinan.export','Export perizinan data (included in full access)','perizinan','general',0,1,'2025-10-20 08:30:13','2025-10-20 08:30:13',3),(145,'Export Admin Data','admin.export','Export admin data (included in full access)','admin','general',0,1,'2025-10-20 08:30:13','2025-10-20 08:30:13',3),(146,'Create Service Data','service.create','Create service data (included in edit access)','service','general',0,1,'2025-10-20 08:30:30','2025-10-20 08:30:30',2),(147,'Create Marketing Data','marketing.create','Create marketing data (included in edit access)','marketing','general',0,1,'2025-10-20 08:30:30','2025-10-20 08:30:30',2),(148,'Create Purchasing Data','purchasing.create','Create purchasing data (included in edit access)','purchasing','general',0,1,'2025-10-20 08:30:30','2025-10-20 08:30:30',2),(149,'Create Warehouse Data','warehouse.create','Create warehouse data (included in edit access)','warehouse','general',0,1,'2025-10-20 08:30:30','2025-10-20 08:30:30',2),(150,'Create Operational Data','operational.create','Create operational data (included in edit access)','operational','general',0,1,'2025-10-20 08:30:30','2025-10-20 08:30:30',2),(151,'Create Perizinan Data','perizinan.create','Create perizinan data (included in edit access)','perizinan','general',0,1,'2025-10-20 08:30:30','2025-10-20 08:30:30',2),(152,'Create Admin Data','admin.create','Create admin data (included in edit access)','admin','general',0,1,'2025-10-20 08:30:30','2025-10-20 08:30:30',2),(153,'View Inventory (Cross-Division)','warehouse.inventory.view','View inventory across divisions - untuk divisi lain yang perlu cek ketersediaan unit','warehouse','resource',1,1,'2025-11-22 13:51:27','2025-11-22 13:51:27',1),(154,'Manage Inventory (Cross-Division)','warehouse.inventory.manage','Manage inventory across divisions - untuk Service yang perlu update status unit setelah maintenance','warehouse','resource',1,1,'2025-11-22 13:51:27','2025-11-22 13:51:27',1),(155,'View Kontrak (Cross-Division)','marketing.kontrak.view','View kontrak across divisions - untuk Service, Operational, Warehouse, Accounting','marketing','resource',1,1,'2025-11-22 13:51:27','2025-11-22 13:51:27',1),(156,'View Work Order (Cross-Division)','service.workorder.view','View work order across divisions - untuk Marketing, Warehouse, Accounting','service','resource',1,1,'2025-11-22 13:51:27','2025-11-22 13:51:27',1),(157,'View PO (Cross-Division)','purchasing.po.view','View purchase order across divisions - untuk Marketing, Warehouse, Accounting','purchasing','resource',1,1,'2025-11-22 13:51:27','2025-11-22 13:51:27',1),(158,'View Delivery (Cross-Division)','operational.delivery.view','View delivery across divisions - untuk Marketing, Warehouse','operational','resource',1,1,'2025-11-22 13:51:27','2025-11-22 13:51:27',1),(159,'View Financial (Cross-Division)','accounting.financial.view','View financial data across divisions - untuk Marketing, Service, Purchasing','accounting','resource',1,1,'2025-11-22 13:51:27','2025-11-22 13:51:27',1);
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_attachment`
--

DROP TABLE IF EXISTS `po_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_attachment` (
  `id_po_attachment` int NOT NULL AUTO_INCREMENT,
  `po_id` int NOT NULL,
  `item_type` enum('Attachment','Battery','Charger') COLLATE utf8mb4_general_ci NOT NULL,
  `item_id` int DEFAULT NULL,
  `qty_ordered` int DEFAULT '1',
  `qty_received` int DEFAULT '0',
  `harga_satuan` decimal(15,2) DEFAULT '0.00',
  `total_harga` decimal(15,2) DEFAULT '0.00',
  `attachment_id` int DEFAULT NULL,
  `baterai_id` int DEFAULT NULL,
  `charger_id` int DEFAULT NULL,
  `serial_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dicek',
  `catatan_verifikasi` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_po_attachment`),
  KEY `fk_po_attachment_purchase_orders` (`po_id`),
  KEY `idx_po_attachment_status_verifikasi` (`status_verifikasi`),
  KEY `idx_po_attachment_created_at` (`created_at`),
  KEY `idx_po_attachment_item_type` (`item_type`),
  KEY `idx_item_type` (`item_type`),
  KEY `idx_item_id` (`item_id`),
  KEY `idx_qty_received` (`qty_received`),
  CONSTRAINT `fk_po_attachment_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=350 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_attachment`
--

LOCK TABLES `po_attachment` WRITE;
/*!40000 ALTER TABLE `po_attachment` DISABLE KEYS */;
INSERT INTO `po_attachment` VALUES (2,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:15'),(3,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:44'),(4,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'','','','','2025-07-22 00:29:20','2025-07-23 21:33:17'),(5,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:52'),(6,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:57'),(7,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:03'),(8,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:11'),(9,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:16'),(10,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:21'),(11,27,'Battery',NULL,1,0,0.00,0.00,NULL,2,3,'123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:26'),(22,36,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,'11123','','Sesuai','','2025-07-23 21:08:34','2025-07-23 23:29:16'),(23,37,'Attachment',NULL,1,0,0.00,0.00,5,NULL,NULL,'','','Sesuai','','2025-07-23 23:32:28','2025-07-23 23:32:57'),(24,37,'Attachment',NULL,1,0,0.00,0.00,5,NULL,NULL,'ok','','Sesuai','','2025-07-23 23:32:28','2025-07-26 01:30:35'),(25,37,'Attachment',NULL,1,0,0.00,0.00,5,NULL,NULL,'ok','','Sesuai','','2025-07-23 23:32:28','2025-07-26 01:37:44'),(26,37,'Attachment',NULL,1,0,0.00,0.00,5,NULL,NULL,'test4','','Sesuai','','2025-07-23 23:32:28','2025-08-11 21:54:35'),(27,37,'Attachment',NULL,1,0,0.00,0.00,5,NULL,NULL,'wae','','Sesuai','','2025-07-23 23:32:28','2025-08-11 23:44:51'),(73,89,'Attachment',NULL,1,0,0.00,0.00,2,NULL,NULL,'123','','Sesuai',NULL,'2025-07-29 19:35:23','2025-08-21 20:57:48'),(74,92,'Attachment',NULL,1,0,0.00,0.00,2,NULL,NULL,'12333445','','Sesuai',NULL,'2025-07-29 19:49:08','2025-08-21 21:04:40'),(75,92,'Battery',NULL,1,0,0.00,0.00,2,4,4,'123','','Sesuai',NULL,'2025-07-29 19:49:08','2025-08-21 21:32:36'),(76,95,'Battery',NULL,1,0,0.00,0.00,NULL,14,15,'1','','Sesuai',NULL,'2025-07-31 21:22:10','2025-08-21 21:35:36'),(77,95,'Attachment',NULL,1,0,0.00,0.00,13,14,15,'123','','Sesuai',NULL,'2025-07-31 21:22:10','2025-08-21 21:36:00'),(78,118,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'123','','Sesuai',NULL,'2025-08-11 18:59:05','2025-08-21 21:36:39'),(79,124,'Attachment',NULL,1,0,0.00,0.00,4,NULL,NULL,'123','','Sesuai',NULL,'2025-08-11 20:15:49','2025-08-22 02:18:28'),(80,130,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,'123','','Sesuai',NULL,'2025-08-11 20:27:06','2025-08-22 02:19:00'),(81,131,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,'ok','','Sesuai',NULL,'2025-08-11 20:27:13','2025-08-26 21:50:06'),(82,132,'Battery',NULL,1,0,0.00,0.00,NULL,14,14,'123','','Sesuai',NULL,'2025-08-11 20:27:48','2025-08-22 02:18:41'),(83,139,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,'a','','Sesuai',NULL,'2025-08-11 21:06:47','2025-08-28 02:32:49'),(84,143,'Battery',NULL,1,0,0.00,0.00,NULL,4,5,'123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-22 02:23:14'),(85,143,'Battery',NULL,1,0,0.00,0.00,NULL,4,5,'123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:34'),(86,143,'Battery',NULL,1,0,0.00,0.00,NULL,4,5,'123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:43'),(87,143,'Battery',NULL,1,0,0.00,0.00,NULL,4,5,'123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:51'),(88,143,'Battery',NULL,1,0,0.00,0.00,NULL,4,5,'123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:58'),(89,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(90,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(91,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(92,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(93,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(94,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(95,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(96,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(97,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(98,147,'Battery',NULL,1,0,0.00,0.00,NULL,8,5,'213124','','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(99,2,'Battery',NULL,1,0,0.00,0.00,NULL,123,NULL,'123',NULL,'Sesuai',NULL,'2025-10-09 09:51:27','2025-07-21 01:31:15'),(101,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:16'),(102,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:17'),(103,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-30 20:31:48'),(104,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:19'),(105,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:20'),(106,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:35'),(107,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:36'),(108,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:37'),(109,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:38'),(110,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:39'),(111,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:50'),(112,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:51'),(113,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:52'),(114,24,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,'11112455512314123','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:04'),(115,24,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,'11112455512314123','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:05'),(116,24,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,'11112455512314123','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:06'),(117,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:49:17'),(118,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 18:25:37'),(119,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 19:40:51'),(120,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 20:32:34'),(121,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 20:14:26'),(122,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:10:11'),(123,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 21:00:44'),(124,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123a','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 23:29:31'),(125,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:36:11'),(126,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-08-03 20:06:17'),(127,38,'Battery',NULL,1,0,0.00,0.00,NULL,2,NULL,'123','','Sesuai',NULL,'2025-07-23 23:58:46','2025-08-03 20:07:12'),(128,46,'Battery',NULL,1,0,0.00,0.00,NULL,16,NULL,'PO/SPRT/9923100',NULL,'Sesuai',NULL,'2025-07-25 23:53:49','2025-07-25 23:54:27'),(129,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(130,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(131,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(132,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(133,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(134,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(135,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(136,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(137,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(138,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(139,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(140,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(141,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(142,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(143,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(144,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(145,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(146,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(147,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(148,94,'Battery',NULL,1,0,0.00,0.00,NULL,5,NULL,NULL,'','Belum Dicek',NULL,'2025-07-31 21:22:10','2025-07-31 21:22:10'),(149,97,'Battery',NULL,1,0,0.00,0.00,NULL,5,NULL,NULL,'','Belum Dicek',NULL,'2025-07-31 21:29:00','2025-07-31 21:29:00'),(162,2,'Battery',NULL,1,0,0.00,0.00,NULL,123,NULL,'123',NULL,'Sesuai',NULL,'2025-10-09 09:51:36','2025-07-21 01:31:15'),(164,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:16'),(165,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:17'),(166,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-30 20:31:48'),(167,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:19'),(168,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:20'),(169,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:35'),(170,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:36'),(171,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:37'),(172,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:38'),(173,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:39'),(174,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:50'),(175,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:51'),(176,23,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'11112455512314123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:52'),(177,24,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,'11112455512314123','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:04'),(178,24,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,'11112455512314123','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:05'),(179,24,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,'11112455512314123','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:06'),(180,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:49:17'),(181,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 18:25:37'),(182,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 19:40:51'),(183,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 20:32:34'),(184,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 20:14:26'),(185,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:10:11'),(186,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 21:00:44'),(187,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123a','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 23:29:31'),(188,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:36:11'),(189,25,'Battery',NULL,1,0,0.00,0.00,NULL,1,NULL,'123','','Sesuai',NULL,'2025-07-21 07:14:31','2025-08-03 20:06:17'),(190,38,'Battery',NULL,1,0,0.00,0.00,NULL,2,NULL,'123','','Sesuai',NULL,'2025-07-23 23:58:46','2025-08-03 20:07:12'),(191,46,'Battery',NULL,1,0,0.00,0.00,NULL,16,NULL,'PO/SPRT/9923100',NULL,'Sesuai',NULL,'2025-07-25 23:53:49','2025-07-25 23:54:27'),(192,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(193,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(194,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(195,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(196,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(197,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(198,52,'Battery',NULL,1,0,0.00,0.00,NULL,3,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-07-29 00:03:39','2025-07-29 00:03:39'),(199,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(200,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(201,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(202,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(203,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(204,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(205,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(206,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(207,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(208,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(209,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(210,93,'Battery',NULL,1,0,0.00,0.00,NULL,4,NULL,NULL,'','Belum Dicek',NULL,'2025-07-29 20:16:28','2025-07-29 20:16:28'),(211,94,'Battery',NULL,1,0,0.00,0.00,NULL,5,NULL,NULL,'','Belum Dicek',NULL,'2025-07-31 21:22:10','2025-07-31 21:22:10'),(212,97,'Battery',NULL,1,0,0.00,0.00,NULL,5,NULL,NULL,'','Belum Dicek',NULL,'2025-07-31 21:29:00','2025-07-31 21:29:00'),(225,2,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,123,'13',NULL,'Sesuai',NULL,'2025-10-09 09:51:36','2025-07-21 01:31:15'),(227,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:16'),(228,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:17'),(229,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-30 20:31:48'),(230,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:19'),(231,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:20'),(232,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:35'),(233,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:36'),(234,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:37'),(235,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:38'),(236,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:39'),(237,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:50'),(238,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:51'),(239,23,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,1,'asdafasdadasdsafasd','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:52'),(240,24,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,4,'asdafasdadasdsafasd','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:04'),(241,24,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,4,'asdafasdadasdsafasd','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:05'),(242,24,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,4,'asdafasdadasdsafasd','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:06'),(243,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:49:17'),(244,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 18:25:37'),(245,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 19:40:51'),(246,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 20:32:34'),(247,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 20:14:26'),(248,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:10:11'),(249,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 21:00:44'),(250,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 23:29:31'),(251,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:36:11'),(252,25,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,2,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-08-03 20:06:17'),(253,38,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,6,NULL,'','Sesuai',NULL,'2025-07-23 23:58:46','2025-08-03 20:07:12'),(256,2,'Attachment',NULL,1,0,0.00,0.00,12,NULL,NULL,'123',NULL,'Sesuai',NULL,'2025-10-09 09:51:36','2025-07-21 01:31:15'),(258,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:16'),(259,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:17'),(260,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-30 20:31:48'),(261,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:19'),(262,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:20'),(263,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:35'),(264,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:36'),(265,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:37'),(266,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:38'),(267,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:39'),(268,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:50'),(269,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:51'),(270,23,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'512512312312512315123','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir','Sesuai',NULL,'2025-07-20 17:00:00','2025-07-21 01:32:52'),(271,24,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,'5125123123125123151232','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:04'),(272,24,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,'5125123123125123151232','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:05'),(273,24,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,'5125123123125123151232','Hadir','Sesuai',NULL,'2025-07-20 19:56:41','2025-07-21 01:33:06'),(274,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:49:17'),(275,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 18:25:37'),(276,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 19:40:51'),(277,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-24 20:32:34'),(278,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 20:14:26'),(279,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:10:11'),(280,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 21:00:44'),(281,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 23:29:31'),(282,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-07-25 19:36:11'),(283,25,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'','','Sesuai',NULL,'2025-07-21 07:14:31','2025-08-03 20:06:17'),(284,38,'Attachment',NULL,1,0,0.00,0.00,2,NULL,NULL,NULL,'','Sesuai',NULL,'2025-07-23 23:58:46','2025-08-03 20:07:12'),(288,150,'Attachment',NULL,1,0,0.00,0.00,5,NULL,NULL,'aawdaw','','Belum Dicek',NULL,'2025-10-09 21:12:37','2025-10-09 21:12:37'),(290,152,'Battery',NULL,1,0,0.00,0.00,NULL,8,NULL,'awdaw12312','','Belum Dicek',NULL,'2025-10-09 21:13:52','2025-10-09 21:13:52'),(291,155,'Attachment',NULL,1,0,0.00,0.00,8,NULL,NULL,'awdaw','','Belum Dicek',NULL,'2025-10-09 21:21:55','2025-10-09 21:21:55'),(292,155,'Battery',NULL,1,0,0.00,0.00,NULL,20,NULL,'123','1231','Sesuai',NULL,'2025-10-09 21:21:55','2025-10-11 08:53:21'),(293,155,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,7,'12123','12321','Sesuai',NULL,'2025-10-09 21:21:55','2025-10-12 20:04:13'),(298,156,'Attachment',NULL,1,0,0.00,0.00,3,NULL,NULL,NULL,NULL,'Belum Dicek',NULL,'2025-10-10 04:43:58','2025-10-10 04:43:58'),(300,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'222','','Sesuai',NULL,'2025-10-10 02:31:30','2025-11-19 08:21:34'),(301,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'222','','Sesuai',NULL,'2025-10-10 02:31:30','2025-11-19 09:36:57'),(302,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(303,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(304,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(305,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(306,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(307,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(308,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(309,158,'Attachment',NULL,1,0,0.00,0.00,9,NULL,NULL,'','','Belum Dicek',NULL,'2025-10-10 02:31:30','2025-10-10 02:31:30'),(310,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(311,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(312,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(313,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(314,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(315,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(316,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(317,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(318,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(319,159,'Attachment',NULL,1,0,0.00,0.00,6,NULL,NULL,'1231awdaw','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(320,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(321,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(322,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(323,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(324,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(325,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(326,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(327,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(328,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(329,159,'Battery',NULL,1,0,0.00,0.00,NULL,19,NULL,'','','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(330,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(331,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(332,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(333,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(334,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(335,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(336,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(337,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(338,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(339,159,'Charger',NULL,1,0,0.00,0.00,NULL,NULL,10,NULL,'wadaw','Belum Dicek',NULL,'2025-10-10 19:47:11','2025-10-10 19:47:11'),(340,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'1','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(341,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'2','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(342,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'3','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(343,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'4','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(344,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'5','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(345,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'6','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(346,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'7','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(347,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'8','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(348,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'9','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42'),(349,161,'Attachment',NULL,1,0,0.00,0.00,1,NULL,NULL,'10','','Belum Dicek',NULL,'2025-10-19 19:28:32','2025-11-16 20:39:42');
/*!40000 ALTER TABLE `po_attachment` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_po_attachment_after_insert` AFTER INSERT ON `po_attachment` FOR EACH ROW BEGIN
    CALL sp_update_po_totals(NEW.po_id);
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_po_attachment_after_update` AFTER UPDATE ON `po_attachment` FOR EACH ROW BEGIN
    IF OLD.item_type != NEW.item_type OR OLD.po_id != NEW.po_id THEN
        CALL sp_update_po_totals(OLD.po_id);
        IF OLD.po_id != NEW.po_id THEN
            CALL sp_update_po_totals(NEW.po_id);
        END IF;
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_po_attachment_after_delete` AFTER DELETE ON `po_attachment` FOR EACH ROW BEGIN
    CALL sp_update_po_totals(OLD.po_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `po_deliveries`
--

DROP TABLE IF EXISTS `po_deliveries`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_deliveries` (
  `id_delivery` int NOT NULL AUTO_INCREMENT,
  `po_id` int NOT NULL,
  `delivery_sequence` int NOT NULL DEFAULT '1',
  `packing_list_no` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `delivery_date` date DEFAULT NULL,
  `driver_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `driver_phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_info` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vehicle_plate` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `expected_date` date DEFAULT NULL,
  `actual_date` date DEFAULT NULL,
  `status` enum('Scheduled','In Transit','Received','Completed','Cancelled') COLLATE utf8mb4_unicode_ci DEFAULT 'Scheduled',
  `total_items` int DEFAULT '0',
  `total_value` decimal(15,2) DEFAULT '0.00',
  `serial_numbers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_delivery`),
  UNIQUE KEY `idx_po_deliveries_packing_list` (`packing_list_no`),
  KEY `idx_po_delivery` (`po_id`,`delivery_sequence`),
  KEY `idx_packing_list` (`packing_list_no`),
  KEY `idx_status` (`status`),
  KEY `idx_po_deliveries_status` (`status`),
  KEY `idx_po_deliveries_delivery_date` (`delivery_date`),
  CONSTRAINT `po_deliveries_chk_1` CHECK (json_valid(`serial_numbers`))
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_deliveries`
--

LOCK TABLES `po_deliveries` WRITE;
/*!40000 ALTER TABLE `po_deliveries` DISABLE KEYS */;
INSERT INTO `po_deliveries` VALUES (16,155,1,'awdawdadawdad1231','2025-10-10','-','-','-','-','2025-10-10','2025-01-16','Received',3,0.00,'[{\"type\":\"unit\",\"id\":\"262\",\"name\":\"HELI | CQD16-GB2SLLI | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"battery\",\"id\":\"292\",\"name\":\"HELI | HL-C135-51.52-404 | Lithium-ion\",\"qty\":1},{\"type\":\"charger\",\"id\":\"293\",\"name\":\"STILL ECOTRON XM(48V \\/ 126A)\",\"qty\":1}]','',NULL,'2025-10-10 14:26:04','2025-10-11 09:26:33'),(17,158,1,'awdawdadawdad1232213123','2025-10-10','-','-','-','-','2025-10-10','2025-01-16','Received',4,0.00,'[{\"type\":\"unit\",\"id\":\"272\",\"name\":\"BT | RRE160MC | HAND PALLET | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"273\",\"name\":\"BT | RRE160MC | HAND PALLET | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"300\",\"name\":\"HELI ZJ33H-B5 - PAPER roll CLAMP\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"301\",\"name\":\"HELI ZJ33H-B5 - PAPER roll CLAMP\",\"qty\":1}]','',NULL,'2025-10-10 14:33:20','2025-10-11 09:26:33'),(18,23,1,'TEST-PL-001',NULL,NULL,NULL,NULL,NULL,'2025-01-15','2025-01-16','Received',0,0.00,NULL,'Test delivery for verification',NULL,'2025-10-11 09:26:02','2025-10-11 09:26:02'),(20,161,1,'sniuutann','2025-11-17','wdwa','awda','awd','awd','2025-11-17',NULL,'Received',10,0.00,'[{\"type\":\"attachment\",\"id\":\"340\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"341\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"342\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"343\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"344\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"345\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"346\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"347\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"348\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1},{\"type\":\"attachment\",\"id\":\"349\",\"name\":\"CASCADE 120K-FPS-CO82 - FORK POSITIONER\",\"qty\":1}]','awdawdawdaw',NULL,'2025-11-17 01:58:09','2025-11-19 16:23:41'),(21,160,1,'9829103775','2025-11-17','wdwa','awda','awd','awd','2025-11-17',NULL,'Received',15,0.00,'[{\"type\":\"unit\",\"id\":\"292\",\"name\":\"BOBCAT | SKID STEER LOADER, S570 | COMPACTOR \\/ VIBRO | DIESEL | 1 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"293\",\"name\":\"BOBCAT | SKID STEER LOADER, S570 | COMPACTOR \\/ VIBRO | DIESEL | 1 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"294\",\"name\":\"BOBCAT | SKID STEER LOADER, S570 | COMPACTOR \\/ VIBRO | DIESEL | 1 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"295\",\"name\":\"BOBCAT | SKID STEER LOADER, S570 | COMPACTOR \\/ VIBRO | DIESEL | 1 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"296\",\"name\":\"BOBCAT | SKID STEER LOADER, S570 | COMPACTOR \\/ VIBRO | DIESEL | 1 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"297\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"298\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"299\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"300\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"301\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"302\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"303\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"304\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"305\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"306\",\"name\":\"CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton\",\"qty\":1}]','AWDAWD',NULL,'2025-11-17 09:46:04','2025-11-17 09:47:06'),(22,159,1,'123456789011','2025-11-19','AA','AA','AA','AA','2025-11-19',NULL,'Received',5,0.00,'[{\"type\":\"unit\",\"id\":\"287\",\"name\":\"CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"288\",\"name\":\"CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"289\",\"name\":\"CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"290\",\"name\":\"CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton\",\"qty\":1},{\"type\":\"unit\",\"id\":\"291\",\"name\":\"CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton\",\"qty\":1}]','awdawdawd',NULL,'2025-11-19 16:24:49','2025-11-19 16:25:47');
/*!40000 ALTER TABLE `po_deliveries` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_po_deliveries_after_update` AFTER UPDATE ON `po_deliveries` FOR EACH ROW BEGIN
    DECLARE total_deliveries INT DEFAULT 0;
    DECLARE completed_deliveries INT DEFAULT 0;
    DECLARE po_status VARCHAR(50);
    
    
    IF NEW.status = 'Completed' AND OLD.status != 'Completed' THEN
        
        
        SELECT COUNT(*) INTO total_deliveries
        FROM po_deliveries 
        WHERE po_id = NEW.po_id;
        
        
        SELECT COUNT(*) INTO completed_deliveries
        FROM po_deliveries 
        WHERE po_id = NEW.po_id AND status = 'Completed';
        
        
        IF completed_deliveries = total_deliveries AND total_deliveries > 0 THEN
            UPDATE purchase_orders 
            SET status = 'completed' 
            WHERE id_po = NEW.po_id AND status != 'completed';
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `po_delivery_items`
--

DROP TABLE IF EXISTS `po_delivery_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_delivery_items` (
  `id_delivery_item` int NOT NULL AUTO_INCREMENT,
  `delivery_id` int NOT NULL,
  `po_id` int NOT NULL,
  `item_type` enum('unit','attachment','battery','charger') COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_po_unit` int DEFAULT NULL COMMENT 'FK to po_units table',
  `id_po_attachment` int DEFAULT NULL COMMENT 'FK to po_attachment table',
  `item_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_description` text COLLATE utf8mb4_unicode_ci,
  `qty` int NOT NULL DEFAULT '1',
  `sn_mast_po` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sn_mesin_po` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_delivery_item`),
  KEY `idx_delivery_id` (`delivery_id`),
  KEY `idx_po_id` (`po_id`),
  KEY `idx_item_type` (`item_type`),
  KEY `fk_delivery_items_unit` (`id_po_unit`),
  KEY `fk_delivery_items_attachment` (`id_po_attachment`)
) ENGINE=InnoDB AUTO_INCREMENT=84 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_delivery_items`
--

LOCK TABLES `po_delivery_items` WRITE;
/*!40000 ALTER TABLE `po_delivery_items` DISABLE KEYS */;
INSERT INTO `po_delivery_items` VALUES (47,16,155,'unit',262,NULL,'HELI | CQD16-GB2SLLI | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'1111','1111','1111','2025-10-10 07:26:04','2025-10-10 07:26:22'),(48,16,155,'battery',NULL,292,'HELI | HL-C135-51.52-404 | Lithium-ion','',1,NULL,NULL,'1111','2025-10-10 07:26:04','2025-10-10 07:26:22'),(49,16,155,'charger',NULL,293,'STILL | ECOTRON XM(48V / 126A)','',1,NULL,NULL,'1111','2025-10-10 07:26:04','2025-10-10 07:26:22'),(50,17,158,'unit',272,NULL,'BT | RRE160MC | HAND PALLET | ELECTRIC | 1,5 Ton','',1,'222','222','222','2025-10-10 07:33:20','2025-10-10 18:36:34'),(51,17,158,'unit',273,NULL,'BT | RRE160MC | HAND PALLET | ELECTRIC | 1,5 Ton','',1,'222','222','222','2025-10-10 07:33:20','2025-10-10 18:36:34'),(52,17,158,'attachment',NULL,300,'HELI ZJ33H-B5 - PAPER roll CLAMP','',1,NULL,NULL,'222','2025-10-10 07:33:20','2025-10-10 18:36:34'),(53,17,158,'attachment',NULL,301,'HELI ZJ33H-B5 - PAPER roll CLAMP','',1,NULL,NULL,'222','2025-10-10 07:33:20','2025-10-10 18:36:34'),(54,20,161,'attachment',NULL,340,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'1','2025-11-16 18:58:09','2025-11-16 20:39:42'),(55,20,161,'attachment',NULL,341,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'2','2025-11-16 18:58:09','2025-11-16 20:39:42'),(56,20,161,'attachment',NULL,342,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'3','2025-11-16 18:58:09','2025-11-16 20:39:42'),(57,20,161,'attachment',NULL,343,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'4','2025-11-16 18:58:09','2025-11-16 20:39:42'),(58,20,161,'attachment',NULL,344,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'5','2025-11-16 18:58:09','2025-11-16 20:39:42'),(59,20,161,'attachment',NULL,345,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'6','2025-11-16 18:58:09','2025-11-16 20:39:42'),(60,20,161,'attachment',NULL,346,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'7','2025-11-16 18:58:09','2025-11-16 20:39:42'),(61,20,161,'attachment',NULL,347,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'8','2025-11-16 18:58:09','2025-11-16 20:39:42'),(62,20,161,'attachment',NULL,348,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'9','2025-11-16 18:58:09','2025-11-16 20:39:42'),(63,20,161,'attachment',NULL,349,'CASCADE 120K-FPS-CO82 - FORK POSITIONER','',1,NULL,NULL,'10','2025-11-16 18:58:09','2025-11-16 20:39:42'),(64,21,160,'unit',292,NULL,'BOBCAT | SKID STEER LOADER, S570 | COMPACTOR / VIBRO | DIESEL | 1 Ton','',1,'1','1','1','2025-11-17 02:46:04','2025-11-17 02:47:02'),(65,21,160,'unit',293,NULL,'BOBCAT | SKID STEER LOADER, S570 | COMPACTOR / VIBRO | DIESEL | 1 Ton','',1,'2','2','2','2025-11-17 02:46:04','2025-11-17 02:47:02'),(66,21,160,'unit',294,NULL,'BOBCAT | SKID STEER LOADER, S570 | COMPACTOR / VIBRO | DIESEL | 1 Ton','',1,'3','3','3','2025-11-17 02:46:04','2025-11-17 02:47:02'),(67,21,160,'unit',295,NULL,'BOBCAT | SKID STEER LOADER, S570 | COMPACTOR / VIBRO | DIESEL | 1 Ton','',1,'4','4','4','2025-11-17 02:46:04','2025-11-17 02:47:02'),(68,21,160,'unit',296,NULL,'BOBCAT | SKID STEER LOADER, S570 | COMPACTOR / VIBRO | DIESEL | 1 Ton','',1,'5','5','5','2025-11-17 02:46:04','2025-11-17 02:47:02'),(69,21,160,'unit',297,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'6','6','6','2025-11-17 02:46:04','2025-11-17 02:47:02'),(70,21,160,'unit',298,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'7','7','7','2025-11-17 02:46:04','2025-11-17 02:47:02'),(71,21,160,'unit',299,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'8','8','8','2025-11-17 02:46:04','2025-11-17 02:47:02'),(72,21,160,'unit',300,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'9','9','9','2025-11-17 02:46:04','2025-11-17 02:47:02'),(73,21,160,'unit',301,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'10','10','10','2025-11-17 02:46:04','2025-11-17 02:47:02'),(74,21,160,'unit',302,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'11','11','11','2025-11-17 02:46:04','2025-11-17 02:47:02'),(75,21,160,'unit',303,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'12','12','12','2025-11-17 02:46:04','2025-11-17 02:47:02'),(76,21,160,'unit',304,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'13','13','13','2025-11-17 02:46:04','2025-11-17 02:47:02'),(77,21,160,'unit',305,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'14','14','14','2025-11-17 02:46:04','2025-11-17 02:47:02'),(78,21,160,'unit',306,NULL,'CAT | DP25ND 2SP30 | PALLET STACKER | ELECTRIC | 1,5 Ton','',1,'15','15','15','2025-11-17 02:46:04','2025-11-17 02:47:02'),(79,22,159,'unit',287,NULL,'CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton','',1,'16','16','16','2025-11-19 09:24:49','2025-11-19 09:25:42'),(80,22,159,'unit',288,NULL,'CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton','',1,'17','17','17','2025-11-19 09:24:49','2025-11-19 09:25:42'),(81,22,159,'unit',289,NULL,'CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton','',1,'18','18','18','2025-11-19 09:24:49','2025-11-19 09:25:42'),(82,22,159,'unit',290,NULL,'CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton','',1,'19','19','19','2025-11-19 09:24:49','2025-11-19 09:25:42'),(83,22,159,'unit',291,NULL,'CAT | GP25ND | COUNTER BALANCE | DIESEL | 1,4 Ton','',1,'20','20','20','2025-11-19 09:24:49','2025-11-19 09:25:42');
/*!40000 ALTER TABLE `po_delivery_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_items_backup_restructure`
--

DROP TABLE IF EXISTS `po_items_backup_restructure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_items_backup_restructure` (
  `id_po_item` int NOT NULL,
  `po_id` int NOT NULL,
  `item_type` enum('Attachment','Battery') COLLATE utf8mb4_general_ci NOT NULL,
  `attachment_id` int DEFAULT NULL,
  `baterai_id` int DEFAULT NULL,
  `charger_id` int DEFAULT NULL,
  `serial_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `serial_number_charger` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dicek',
  `catatan_verifikasi` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_items_backup_restructure`
--

LOCK TABLES `po_items_backup_restructure` WRITE;
/*!40000 ALTER TABLE `po_items_backup_restructure` DISABLE KEYS */;
INSERT INTO `po_items_backup_restructure` VALUES (2,27,'Battery',NULL,2,3,'','','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:15'),(3,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:44'),(4,27,'Battery',NULL,2,3,'','','','','','2025-07-22 00:29:20','2025-07-23 21:33:17'),(5,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:52'),(6,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:33:57'),(7,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:03'),(8,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:11'),(9,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:16'),(10,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:21'),(11,27,'Battery',NULL,2,3,'123123','123123','','Sesuai','','2025-07-22 00:29:20','2025-07-23 21:34:26'),(22,36,'Attachment',3,NULL,NULL,'11123','','','Sesuai','','2025-07-23 21:08:34','2025-07-23 23:29:16'),(23,37,'Attachment',5,NULL,NULL,'','','','Sesuai','','2025-07-23 23:32:28','2025-07-23 23:32:57'),(24,37,'Attachment',5,NULL,NULL,'ok',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-07-26 01:30:35'),(25,37,'Attachment',5,NULL,NULL,'ok',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-07-26 01:37:44'),(26,37,'Attachment',5,NULL,NULL,'test4',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-08-11 21:54:35'),(27,37,'Attachment',5,NULL,NULL,'wae',NULL,'','Sesuai','','2025-07-23 23:32:28','2025-08-11 23:44:51'),(73,89,'Attachment',2,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-07-29 19:35:23','2025-08-21 20:57:48'),(74,92,'Attachment',2,NULL,NULL,'12333445',NULL,'','Sesuai',NULL,'2025-07-29 19:49:08','2025-08-21 21:04:40'),(75,92,'Battery',2,4,4,'123','123','','Sesuai',NULL,'2025-07-29 19:49:08','2025-08-21 21:32:36'),(76,95,'Battery',NULL,14,15,'1','1','','Sesuai',NULL,'2025-07-31 21:22:10','2025-08-21 21:35:36'),(77,95,'Attachment',13,14,15,'123',NULL,'','Sesuai',NULL,'2025-07-31 21:22:10','2025-08-21 21:36:00'),(78,118,'Attachment',1,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-08-11 18:59:05','2025-08-21 21:36:39'),(79,124,'Attachment',4,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-08-11 20:15:49','2025-08-22 02:18:28'),(80,130,'Attachment',3,NULL,NULL,'123',NULL,'','Sesuai',NULL,'2025-08-11 20:27:06','2025-08-22 02:19:00'),(81,131,'Attachment',3,NULL,NULL,'ok',NULL,'','Sesuai',NULL,'2025-08-11 20:27:13','2025-08-26 21:50:06'),(82,132,'Battery',NULL,14,14,'123','123','','Sesuai',NULL,'2025-08-11 20:27:48','2025-08-22 02:18:41'),(83,139,'Attachment',3,NULL,NULL,'a',NULL,'','Sesuai',NULL,'2025-08-11 21:06:47','2025-08-28 02:32:49'),(84,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-22 02:23:14'),(85,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:34'),(86,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:43'),(87,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:51'),(88,143,'Battery',NULL,4,5,'123','123','','Sesuai',NULL,'2025-08-22 02:21:14','2025-08-26 21:15:58'),(89,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(90,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(91,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(92,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(93,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(94,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(95,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(96,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(97,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08'),(98,147,'Battery',NULL,8,5,'213124',NULL,'','Belum Dicek',NULL,'2025-08-28 02:35:08','2025-08-28 02:35:08');
/*!40000 ALTER TABLE `po_items_backup_restructure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_sparepart_items`
--

DROP TABLE IF EXISTS `po_sparepart_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_sparepart_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `po_id` int NOT NULL,
  `sparepart_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `satuan` enum('Pieces','Rol','Kaleng','Set','Pak','Meter','Unit','Jerigen','Lembar','Box','Pax','Drum','Batang','Pil','Dus','Kilogram','Botol','IBC Tank','Lusin','Liter','Lot') COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dicek',
  `catatan_verifikasi` text COLLATE utf8mb4_general_ci,
  PRIMARY KEY (`id`),
  KEY `idx_po_sparepart_status_verifikasi` (`status_verifikasi`),
  KEY `idx_po_sparepart_items_po_id` (`po_id`),
  CONSTRAINT `fk_po_sparepart_items_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
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
-- Table structure for table `po_sparepart_items_backup_restructure`
--

DROP TABLE IF EXISTS `po_sparepart_items_backup_restructure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_sparepart_items_backup_restructure` (
  `id` int NOT NULL,
  `po_id` int NOT NULL,
  `sparepart_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `satuan` enum('Pieces','Rol','Kaleng','Set','Pak','Meter','Unit','Jerigen','Lembar','Box','Pax','Drum','Batang','Pil','Dus','Kilogram','Botol','IBC Tank','Lusin','Liter','Lot') COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dicek',
  `catatan_verifikasi` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_sparepart_items_backup_restructure`
--

LOCK TABLES `po_sparepart_items_backup_restructure` WRITE;
/*!40000 ALTER TABLE `po_sparepart_items_backup_restructure` DISABLE KEYS */;
INSERT INTO `po_sparepart_items_backup_restructure` VALUES (14,35,5,1,'Pieces','','Sesuai',''),(15,35,23,1,'Pieces','','Tidak Sesuai',''),(16,35,33,1,'Pieces','','Sesuai',''),(17,40,3,1,'Pieces','','Sesuai',NULL),(18,40,4,1,'Pieces','','Sesuai',NULL),(19,40,28,1,'Pieces','','Sesuai',NULL),(20,40,47,1,'Pieces','','Sesuai',NULL),(35,85,1,1,'Pieces','','Sesuai',''),(36,85,3,1,'Kaleng','','Belum Dicek',NULL),(37,88,3,1,'Pieces','','Belum Dicek',NULL),(38,96,3,1,'Pieces','','Belum Dicek',NULL),(39,119,1,1,'Pieces','','Belum Dicek',NULL),(40,125,3,1,'Pieces','','Belum Dicek',NULL),(48,137,3,1,'Pieces','','Belum Dicek',NULL),(49,140,30,1,'Pieces','','Belum Dicek',NULL),(50,141,4,1,'Pieces','','Belum Dicek',NULL);
/*!40000 ALTER TABLE `po_sparepart_items_backup_restructure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_units`
--

DROP TABLE IF EXISTS `po_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_units` (
  `id_po_unit` int NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `po_id` int NOT NULL,
  `jenis_unit` int DEFAULT NULL,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dicek',
  `merk_unit` int DEFAULT NULL,
  `model_unit_id` int DEFAULT NULL,
  `tipe_unit_id` int DEFAULT NULL,
  `serial_number_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_po` int DEFAULT NULL,
  `kapasitas_id` int DEFAULT NULL,
  `mast_id` int DEFAULT NULL,
  `sn_mast_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mesin_id` int DEFAULT NULL,
  `sn_mesin_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ban_id` int DEFAULT NULL,
  `roda_id` int DEFAULT NULL,
  `valve_id` int DEFAULT NULL,
  `status_penjualan` enum('Baru','Bekas','Rekondisi') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `catatan_verifikasi` text COLLATE utf8mb4_general_ci COMMENT 'Catatan verifikasi / alasan reject jika status Tidak Sesuai',
  PRIMARY KEY (`id_po_unit`),
  KEY `fk_po_units_purchase_orders` (`po_id`),
  CONSTRAINT `fk_po_units_purchase_orders` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=307 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_units`
--

LOCK TABLES `po_units` WRITE;
/*!40000 ALTER TABLE `po_units` DISABLE KEYS */;
INSERT INTO `po_units` VALUES (287,'2025-10-10 19:47:11','2025-11-19 09:25:42',159,1,'Belum Dicek',4,13,6,'16',2025,13,16,'16',15,'16',2,1,2,'Baru','awda',NULL),(288,'2025-10-10 19:47:11','2025-11-19 09:25:42',159,1,'Belum Dicek',4,13,6,'17',2025,13,16,'17',15,'17',2,1,2,'Baru','awda',NULL),(289,'2025-10-10 19:47:11','2025-11-19 09:25:42',159,1,'Belum Dicek',4,13,6,'18',2025,13,16,'18',15,'18',2,1,2,'Baru','awda',NULL),(290,'2025-10-10 19:47:11','2025-11-19 09:25:42',159,1,'Belum Dicek',4,13,6,'19',2025,13,16,'19',15,'19',2,1,2,'Baru','awda',NULL),(291,'2025-10-10 19:47:11','2025-11-19 09:25:42',159,1,'Belum Dicek',4,13,6,'20',2025,13,16,'20',15,'20',2,1,2,'Baru','awda',NULL),(293,'2025-10-10 21:05:13','2025-11-17 23:54:38',160,1,'Sesuai',520,520,1,'2',2025,8,18,'2',17,'2',1,2,2,'Baru','',NULL),(294,'2025-10-10 21:05:13','2025-11-17 23:59:15',160,1,'Sesuai',520,520,1,'3',2025,8,18,'3',17,'3',1,2,2,'Baru','',NULL),(295,'2025-10-10 21:05:13','2025-11-19 02:31:22',160,1,'Belum Dicek',520,520,1,'4',2025,8,18,'4',17,'4',1,2,2,'Baru','',NULL),(296,'2025-10-10 21:05:13','2025-11-19 02:59:13',160,1,'Belum Dicek',520,520,1,'5',2025,8,18,'5',17,'5',1,2,2,'Baru','',NULL),(297,'2025-10-10 21:05:13','2025-11-18 00:06:52',160,2,'Sesuai',4,11,11,'6',2025,14,15,'6',19,'6',5,3,2,'Baru','',NULL),(298,'2025-10-10 21:05:13','2025-11-19 03:26:56',160,2,'Tidak Sesuai',4,11,11,'7',2025,14,15,'7',19,'7',5,3,2,'Baru','','Alasan Reject: awdawdwa; Model: Database = \"DP25ND 2SP30\", Real = \"CAT - DP30ND - 2SP30\"'),(299,'2025-10-10 21:05:13','2025-11-19 06:48:22',160,2,'Tidak Sesuai',4,11,11,'8',2025,14,15,'8',19,'8',5,3,2,'Baru','','Alasan Reject: awadw; Jenis Unit: Database = \"Forklift\", Real = \"Alat Berat WHEEL LOADER\"'),(300,'2025-10-10 21:05:13','2025-11-19 01:56:47',160,2,'Belum Dicek',4,11,11,'9',2025,14,15,'9',19,'9',5,3,2,'Baru','',NULL),(301,'2025-10-10 21:05:13','2025-11-19 03:26:34',160,2,'Belum Dicek',4,11,11,'10',2025,14,15,'10',19,'10',5,3,2,'Baru','',NULL),(302,'2025-10-10 21:05:13','2025-11-19 01:56:45',160,2,'Belum Dicek',4,11,11,'11',2025,14,15,'11',19,'11',5,3,2,'Baru','',NULL),(303,'2025-10-10 21:05:13','2025-11-17 02:47:02',160,2,'Belum Dicek',4,11,11,'12',2025,14,15,'12',19,'12',5,3,2,'Baru','',NULL),(304,'2025-10-10 21:05:13','2025-11-17 02:47:02',160,2,'Belum Dicek',4,11,11,'13',2025,14,15,'13',19,'13',5,3,2,'Baru','',NULL),(305,'2025-10-10 21:05:13','2025-11-17 02:47:02',160,2,'Belum Dicek',4,11,11,'14',2025,14,15,'14',19,'14',5,3,2,'Baru','',NULL),(306,'2025-10-10 21:05:13','2025-11-17 02:47:02',160,2,'Belum Dicek',4,11,11,'15',2025,14,15,'15',19,'15',5,3,2,'Baru','',NULL);
/*!40000 ALTER TABLE `po_units` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_po_units_after_insert` AFTER INSERT ON `po_units` FOR EACH ROW BEGIN
    CALL sp_update_po_totals(NEW.po_id);
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `trg_po_units_after_delete` AFTER DELETE ON `po_units` FOR EACH ROW BEGIN
    CALL sp_update_po_totals(OLD.po_id);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `po_units_backup_restructure`
--

DROP TABLE IF EXISTS `po_units_backup_restructure`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_units_backup_restructure` (
  `id_po_unit` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `po_id` int NOT NULL,
  `jenis_unit` int DEFAULT NULL,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dicek',
  `merk_unit` int DEFAULT NULL,
  `model_unit_id` int DEFAULT NULL,
  `tipe_unit_id` int DEFAULT NULL,
  `serial_number_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tahun_po` int DEFAULT NULL,
  `kapasitas_id` int DEFAULT NULL,
  `mast_id` int DEFAULT NULL,
  `sn_mast_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `mesin_id` int DEFAULT NULL,
  `sn_mesin_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `attachment_id` int DEFAULT NULL,
  `sn_attachment_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `baterai_id` int DEFAULT NULL,
  `sn_baterai_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `charger_id` int DEFAULT NULL,
  `sn_charger_po` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ban_id` int DEFAULT NULL,
  `roda_id` int DEFAULT NULL,
  `valve_id` int DEFAULT NULL,
  `status_penjualan` enum('Baru','Bekas','Rekondisi') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_units_backup_restructure`
--

LOCK TABLES `po_units_backup_restructure` WRITE;
/*!40000 ALTER TABLE `po_units_backup_restructure` DISABLE KEYS */;
INSERT INTO `po_units_backup_restructure` VALUES (1,NULL,'2025-07-21 01:31:15',2,1,'Sesuai',NULL,3,3,'123123',2019,11,6,'123',1,'1',12,'123',123,'123',123,'13',5,4,3,NULL,NULL),(2,NULL,'2025-07-21 01:31:17',1,2,'Sesuai',NULL,1,4,'12331233',2025,14,2,'123',1,'1',1,'1',123,'123',123,'123',6,3,3,NULL,NULL),(42,'2025-07-20 17:00:00','2025-07-21 01:32:16',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(43,'2025-07-20 17:00:00','2025-07-21 01:32:17',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(44,'2025-07-20 17:00:00','2025-07-30 20:31:48',23,1,'Sesuai',1,1,1,'123',2025,2,1,'123',1,'123',1,'512512312312512315123',1,'123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(45,'2025-07-20 17:00:00','2025-07-21 01:32:19',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(46,'2025-07-20 17:00:00','2025-07-21 01:32:20',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(47,'2025-07-20 17:00:00','2025-07-21 01:32:35',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(48,'2025-07-20 17:00:00','2025-07-21 01:32:36',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(49,'2025-07-20 17:00:00','2025-07-21 01:32:37',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(50,'2025-07-20 17:00:00','2025-07-21 01:32:38',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(51,'2025-07-20 17:00:00','2025-07-21 01:32:39',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(52,'2025-07-20 17:00:00','2025-07-21 01:32:50',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(53,'2025-07-20 17:00:00','2025-07-21 01:32:51',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(54,'2025-07-20 17:00:00','2025-07-21 01:32:52',23,1,'Sesuai',1,1,1,'TEST1234123',2025,2,1,'111231451523123',1,'15235123124512321',1,'512512312312512315123',1,'11112455512314123',1,'asdafasdadasdsafasd',1,1,1,'Baru','NIM : 312410362Nama : Abdul Malik IbrahimKehadiran : Hadir'),(55,'2025-07-20 19:56:41','2025-07-21 01:33:04',24,2,'Sesuai',60,66,4,'TEST12341234',2025,24,3,'1112314515231233',4,'1523512312451232112',3,'5125123123125123151232',4,'11112455512314123',4,'asdafasdadasdsafasd',3,2,3,'Baru','Hadir'),(56,'2025-07-20 19:56:41','2025-07-21 01:33:05',24,2,'Sesuai',60,66,4,'TEST12341234',2025,24,3,'1112314515231233',4,'1523512312451232112',3,'5125123123125123151232',4,'11112455512314123',4,'asdafasdadasdsafasd',3,2,3,'Baru','Hadir'),(57,'2025-07-20 19:56:41','2025-07-21 01:33:06',24,2,'Sesuai',60,66,4,'TEST12341234',2025,24,3,'1112314515231233',4,'1523512312451232112',3,'5125123123125123151232',4,'11112455512314123',4,'asdafasdadasdsafasd',3,2,3,'Baru','Hadir'),(58,'2025-07-21 07:14:31','2025-07-25 19:49:17',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(59,'2025-07-21 07:14:31','2025-07-24 18:25:37',25,1,'Sesuai',4,9,12,'123',2025,47,2,NULL,1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(60,'2025-07-21 07:14:31','2025-07-24 19:40:51',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(61,'2025-07-21 07:14:31','2025-07-24 20:32:34',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(62,'2025-07-21 07:14:31','2025-07-25 20:14:26',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(63,'2025-07-21 07:14:31','2025-07-25 19:10:11',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(64,'2025-07-21 07:14:31','2025-07-25 21:00:44',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(65,'2025-07-21 07:14:31','2025-07-25 23:29:31',25,1,'Sesuai',4,9,12,'123a',2025,47,2,'123a',1,'123a',1,'',1,'123a',2,'',1,1,3,'Baru',''),(66,'2025-07-21 07:14:31','2025-07-25 19:36:11',25,1,'Sesuai',4,9,12,'132',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(67,'2025-07-21 07:14:31','2025-08-03 20:06:17',25,1,'Sesuai',4,9,12,'123',2025,47,2,'123',1,'123',1,'',1,'123',2,'',1,1,3,'Baru',''),(68,'2025-07-23 23:58:46','2025-08-03 20:07:12',38,3,'Sesuai',4,6,12,'123',2025,6,2,'123',2,'123',2,NULL,2,'123',6,NULL,1,2,3,'Baru',''),(83,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(84,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(85,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(86,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(87,'2025-07-25 18:44:44','2025-07-25 18:44:44',44,1,'Belum Dicek',4,5,12,NULL,2025,27,2,NULL,2,NULL,NULL,NULL,0,NULL,NULL,NULL,1,2,3,'Baru',''),(89,'2025-07-25 23:53:49','2025-07-25 23:54:27',46,2,'Sesuai',499,501,13,'PO/SPRT/9923100',2025,37,4,'PO/SPRT/9923100',3,'PO/SPRT/9923100',NULL,NULL,16,'PO/SPRT/9923100',NULL,NULL,4,2,3,'Baru',NULL),(94,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(95,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(96,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(97,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(98,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(99,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(100,'2025-07-29 00:03:39','2025-07-29 00:03:39',52,2,'Belum Dicek',1,1,2,NULL,2025,5,3,NULL,3,NULL,NULL,NULL,3,NULL,NULL,NULL,2,1,2,'Baru',NULL),(101,'2025-07-29 00:28:01','2025-07-29 00:28:01',58,2,'Belum Dicek',4,NULL,NULL,NULL,2025,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Baru',NULL),(102,'2025-07-29 01:44:12','2025-07-29 01:44:12',65,2,'Belum Dicek',2,3,3,NULL,2025,4,4,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,4,3,'Baru',''),(103,'2025-07-29 01:45:56','2025-07-29 01:45:56',66,2,'Belum Dicek',38,38,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,4,3,'Baru',''),(104,'2025-07-29 01:45:56','2025-07-29 01:45:56',66,2,'Belum Dicek',4,7,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,4,3,'Baru',''),(105,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(106,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(107,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(108,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(109,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(110,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(111,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(112,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(113,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(114,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(115,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(116,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',4,6,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(117,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(118,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(119,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(120,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(121,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(122,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(123,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(124,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(125,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(126,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(127,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(128,'2025-07-29 01:59:17','2025-07-29 01:59:17',67,2,'Belum Dicek',1,1,3,NULL,2025,4,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(213,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(214,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(215,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(216,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(217,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(218,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(219,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(220,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(221,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(222,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(223,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(224,'2025-07-29 19:23:48','2025-07-29 19:23:48',84,1,'Belum Dicek',2,2,1,NULL,2025,2,6,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,3,4,'Baru',''),(225,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(226,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(227,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(228,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(229,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(230,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(231,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(232,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(233,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(234,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(235,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(236,'2025-07-29 19:25:29','2025-07-29 19:25:29',87,1,'Belum Dicek',2,2,1,NULL,2025,2,5,NULL,6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,4,4,'Baru',''),(237,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(238,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(239,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(240,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(241,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(242,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(243,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(244,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(245,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(246,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(247,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(248,'2025-07-29 20:16:28','2025-07-29 20:16:28',93,2,'Belum Dicek',4,5,1,NULL,2025,1,5,NULL,5,NULL,NULL,NULL,4,NULL,NULL,NULL,5,2,3,'Baru',''),(249,'2025-07-31 21:22:10','2025-07-31 21:22:10',94,2,'Belum Dicek',4,5,1,NULL,2025,2,5,NULL,5,NULL,NULL,NULL,5,NULL,NULL,NULL,5,2,3,'Baru',''),(250,'2025-07-31 21:29:00','2025-07-31 21:29:00',97,2,'Belum Dicek',4,5,1,NULL,2025,2,5,NULL,4,NULL,NULL,NULL,5,NULL,NULL,NULL,5,3,3,'Bekas',''),(251,'2025-08-11 02:37:42','2025-08-11 02:37:42',102,1,'Belum Dicek',60,64,1,NULL,2025,2,5,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,2,3,'Rekondisi',NULL),(252,'2025-08-11 18:56:29','2025-08-11 18:56:29',116,1,'Belum Dicek',4,4,4,NULL,2025,5,4,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,4,3,3,'Baru',''),(253,'2025-08-11 18:59:05','2025-08-11 18:59:05',117,1,'Belum Dicek',4,5,4,NULL,2025,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Baru',''),(254,'2025-08-11 19:00:10','2025-08-11 19:00:10',120,1,'Belum Dicek',2,2,2,NULL,2025,1,2,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,4,3,'Baru',NULL),(255,'2025-08-11 19:21:21','2025-08-11 19:21:21',121,1,'Belum Dicek',39,43,2,NULL,2025,5,1,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,1,1,'Baru',NULL),(256,'2025-08-11 19:22:14','2025-08-11 19:22:14',122,2,'Belum Dicek',4,6,9,NULL,2025,2,5,NULL,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,3,4,4,'Baru',''),(257,'2025-08-11 20:15:49','2025-08-11 20:15:49',123,2,'Belum Dicek',2,3,10,NULL,2025,10,2,NULL,2,NULL,NULL,NULL,NULL,NULL,NULL,NULL,2,2,3,'Baru',''),(259,'2025-08-11 21:06:47','2025-08-11 21:06:47',138,1,'Belum Dicek',2,2,6,NULL,2025,5,5,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,6,2,4,'Baru',''),(260,'2025-08-11 23:45:20','2025-08-11 23:45:20',142,1,'Belum Dicek',2,2,4,NULL,2025,5,5,NULL,5,NULL,NULL,NULL,NULL,NULL,NULL,NULL,5,2,3,'Baru',NULL);
/*!40000 ALTER TABLE `po_units_backup_restructure` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `po_verification`
--

DROP TABLE IF EXISTS `po_verification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `po_verification` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `po_type` enum('unit','attachment','sparepart') COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Tipe PO item yang diverifikasi',
  `source_id` int unsigned NOT NULL COMMENT 'ID dari po_units/po_attachment/po_sparepart_items',
  `po_id` int unsigned NOT NULL COMMENT 'ID Purchase Order',
  `field_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nama field yang tidak sesuai (e.g., sn_unit, merk, model)',
  `database_value` text COLLATE utf8mb4_general_ci COMMENT 'Nilai dari database/PO',
  `real_value` text COLLATE utf8mb4_general_ci COMMENT 'Nilai real dari lapangan',
  `discrepancy_type` enum('Minor','Major','Missing') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Minor' COMMENT 'Tipe ketidaksesuaian',
  `status_verifikasi` enum('Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Status verifikasi item ini',
  `catatan` text COLLATE utf8mb4_general_ci COMMENT 'Catatan tambahan',
  `verified_by` int unsigned DEFAULT NULL COMMENT 'User ID yang melakukan verifikasi',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_po_type_source` (`po_type`,`source_id`),
  KEY `idx_po_id` (`po_id`),
  KEY `idx_status` (`status_verifikasi`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracking detail discrepancy verifikasi PO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `po_verification`
--

LOCK TABLES `po_verification` WRITE;
/*!40000 ALTER TABLE `po_verification` DISABLE KEYS */;
INSERT INTO `po_verification` VALUES (15,'unit',298,160,'model','DP25ND 2SP30','CAT - DP30ND - 2SP30','Major','Tidak Sesuai','Alasan Reject: awdawdwa; Model: Database = \"DP25ND 2SP30\", Real = \"CAT - DP30ND - 2SP30\"',1,'2025-11-19 10:26:56'),(16,'unit',299,160,'jenis_unit','Forklift','Alat Berat WHEEL LOADER','Minor','Tidak Sesuai','Alasan Reject: awadw; Jenis Unit: Database = \"Forklift\", Real = \"Alat Berat WHEEL LOADER\"',1,'2025-11-19 13:48:22');
/*!40000 ALTER TABLE `po_verification` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `division_id` int unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_positions_code` (`code`),
  KEY `idx_positions_division` (`division_id`),
  KEY `idx_positions_active` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,'CEO','CEO','Chief Executive Officer',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(2,'Director','DIR','Direktur',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(3,'Manager','MGR','Manajer',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(4,'Head Marketing','HMKT','Kepala Divisi Marketing',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(5,'Head Service','HSVC','Kepala Divisi Service',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(6,'Head Purchasing','HPUR','Kepala Divisi Purchasing',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(7,'Head Warehouse','HWH','Kepala Divisi Warehouse',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(8,'Head Accounting','HACC','Kepala Divisi Accounting',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(9,'Head HRD','HHRD','Kepala Divisi HRD',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(10,'Staff Marketing','SMKT','Staff Divisi Marketing',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(11,'Staff Service','SSVC','Staff Divisi Service',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(12,'Staff Purchasing','SPUR','Staff Divisi Purchasing',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(13,'Staff Warehouse','SWH','Staff Divisi Warehouse',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(14,'Staff Accounting','SACC','Staff Divisi Accounting',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(15,'Staff HRD','SHRD','Staff Divisi HRD',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(16,'Technician','TECH','Teknisi',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(17,'Operator','OPR','Operator',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20'),(18,'Helper','HELP','Helper',NULL,1,'2025-10-14 03:16:20','2025-10-14 03:16:20');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `purchase_orders`
--

DROP TABLE IF EXISTS `purchase_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `purchase_orders` (
  `id_po` int NOT NULL AUTO_INCREMENT,
  `no_po` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_po` date NOT NULL,
  `supplier_id` int NOT NULL,
  `total_items` int DEFAULT '0',
  `total_value` decimal(15,2) DEFAULT '0.00',
  `delivery_terms` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_terms` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `bl_date` date DEFAULT NULL,
  `keterangan_po` text COLLATE utf8mb4_general_ci,
  `tipe_po` enum('Unit','Attachment & Battery','Sparepart','Dinamis') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','completed','cancelled','Selesai dengan Catatan') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `total_unit` int DEFAULT '0' COMMENT 'Total unit items in this PO',
  `total_attachment` int DEFAULT '0' COMMENT 'Total attachment items in this PO',
  `total_battery` int DEFAULT '0' COMMENT 'Total battery items in this PO',
  `total_charger` int DEFAULT '0' COMMENT 'Total charger items in this PO',
  PRIMARY KEY (`id_po`),
  KEY `idx_purchase_orders_status` (`status`),
  KEY `idx_po_tanggal_po` (`tanggal_po`),
  KEY `idx_purchase_orders_supplier_id` (`supplier_id`),
  KEY `idx_purchase_orders_no_po` (`no_po`),
  KEY `idx_purchase_orders_invoice_no` (`invoice_no`),
  CONSTRAINT `fk_purchase_orders_suppliers` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id_supplier`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `purchase_orders`
--

LOCK TABLES `purchase_orders` WRITE;
/*!40000 ALTER TABLE `purchase_orders` DISABLE KEYS */;
INSERT INTO `purchase_orders` VALUES (1,'PO-Unit-2025-07-0001','2025-07-15',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-07-16 03:44:13','2025-07-21 01:31:17','completed',0,0,0,0),(2,'PO/ATT/2024/002','2025-07-16',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-07-16 03:44:13','2025-07-21 01:31:15','completed',0,1,2,1),(23,'PO-Unit-2025-07-0004','2025-07-21',2,0,0.00,NULL,NULL,'12441234123123123','2025-07-21','2025-07-25','124123123','Unit','2025-07-20 18:33:37','2025-07-30 20:31:48','completed',0,13,26,13),(24,'PO-Unit-2025-07-0005','2025-07-21',2,0,0.00,NULL,NULL,'1244123412312312312','2025-07-31','2025-07-31','asfffasdasdasd123','Unit','2025-07-20 19:56:41','2025-07-21 01:33:06','completed',0,3,6,3),(25,'PO-Unit-2025-07-0006','2025-07-21',1,0,0.00,NULL,NULL,'PO/ASDA/ASU','2025-07-21','2025-07-21','BELI UNIT','Unit','2025-07-21 07:14:31','2025-08-03 20:06:17','completed',0,10,20,10),(27,'PO/221/111111','2025-07-22',2,0,0.00,NULL,NULL,'','2025-07-22','2025-07-22','BELI ','Attachment & Battery','2025-07-22 00:29:20','2025-07-22 00:29:20','pending',0,0,10,0),(35,'PO/SPRT/144445','2025-07-24',1,0,0.00,NULL,NULL,NULL,NULL,NULL,'','Sparepart','2025-07-23 20:16:40','2025-07-23 20:16:58','Selesai dengan Catatan',0,0,0,0),(36,'PO/2000/99231','2025-07-24',1,0,0.00,NULL,NULL,'','2025-07-24','2025-07-24','','Attachment & Battery','2025-07-23 21:08:34','2025-07-23 23:29:16','completed',0,1,0,0),(37,'PO/ATT/4321/211/1','2025-07-24',2,0,0.00,NULL,NULL,'','2025-07-25','2025-07-25','','Attachment & Battery','2025-07-23 23:32:28','2025-07-23 23:32:28','pending',0,5,0,0),(38,'PO/278/7875','2025-07-24',1,0,0.00,NULL,NULL,'','2025-07-24','2025-07-24','','Unit','2025-07-23 23:58:46','2025-08-15 21:26:44','completed',0,1,2,1),(39,'PO/221/131231','2025-07-24',1,0,0.00,NULL,NULL,'','2025-07-24','2025-07-24','','Unit','2025-07-24 01:33:02','2025-07-24 01:33:02','pending',0,0,0,0),(40,'PO/221/122222','2025-07-25',2,0,0.00,NULL,NULL,NULL,NULL,NULL,'','Sparepart','2025-07-24 22:13:51','2025-07-24 22:15:54','completed',0,0,0,0),(44,'PO/221/131232','2025-07-26',1,0,0.00,NULL,NULL,'','2025-07-24','2025-07-24','','Unit','2025-07-25 18:44:44','2025-07-25 18:44:44','pending',0,0,0,0),(46,'PO/SPRT/9923100','2025-07-26',1,0,0.00,NULL,NULL,NULL,'2025-07-26','2025-07-26',NULL,'Unit','2025-07-25 23:53:49','2025-07-25 23:54:27','completed',0,0,2,0),(47,'PO/SPRT/ADIT','2025-07-28',2,0,0.00,NULL,NULL,'123','2025-07-26','2025-07-26','123','Unit','2025-07-28 00:28:18','2025-07-28 00:28:18','pending',0,0,0,0),(52,'PO/SPRT/ADIT123','2025-07-29',1,0,0.00,NULL,NULL,'123','2025-07-26','2025-07-26','123','Unit','2025-07-29 00:03:39','2025-07-29 00:03:39','pending',0,0,14,0),(58,'PO/221/KALENG125551','2025-07-29',1,0,0.00,NULL,NULL,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 00:28:01','2025-07-29 00:28:01','pending',0,0,0,0),(65,'PO/221/KALENG1255512','2025-07-29',1,0,0.00,NULL,NULL,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 01:44:12','2025-07-29 01:44:12','pending',0,0,0,0),(66,'PO/221/asda321','2025-07-29',2,0,0.00,NULL,NULL,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 01:45:56','2025-07-29 01:45:56','pending',0,0,0,0),(67,'PO/221/asda321213','2025-07-29',1,0,0.00,NULL,NULL,'PO/ASDA/ASU12','2025-07-28','2025-07-28','12','Unit','2025-07-29 01:59:17','2025-07-29 01:59:17','pending',0,0,0,0),(84,'PO/221/23155','2025-07-30',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-07-29 19:23:48','2025-07-29 19:23:48','pending',0,0,0,0),(85,'PO/221/231551','2025-07-30',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-07-29 19:24:12','2025-07-29 19:24:12','pending',0,0,0,0),(87,'PO/221/231553','2025-07-30',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-07-29 19:25:29','2025-07-29 19:25:29','pending',0,0,0,0),(88,'PO/221/231553','2025-07-30',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-07-29 19:25:29','2025-07-29 19:25:29','pending',0,0,0,0),(89,'PO/221/KALENG12355','2025-07-30',2,0,0.00,NULL,NULL,'','2025-07-30','2025-07-30','','Attachment & Battery','2025-07-29 19:35:23','2025-07-29 19:35:23','pending',0,1,0,0),(92,'PO/221/2315532121','2025-07-30',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-07-29 19:49:08','2025-07-29 19:49:08','pending',0,1,1,0),(93,'PO/221/2315531','2025-07-30',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-07-29 20:16:28','2025-07-29 20:16:28','pending',0,0,24,0),(94,'PO/221/2315512355','2025-08-01',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-07-31 21:22:10','2025-07-31 21:22:10','pending',0,0,2,0),(95,'PO/221/2315512355','2025-08-01',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-07-31 21:22:10','2025-07-31 21:22:10','pending',0,1,1,0),(96,'PO/221/2315512355','2025-08-01',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-07-31 21:22:10','2025-07-31 21:22:10','pending',0,0,0,0),(97,'PO/221/23155123551','2025-08-01',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-07-31 21:29:00','2025-07-31 21:29:00','pending',0,0,2,0),(102,'tester','2025-08-11',1,0,0.00,NULL,NULL,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 02:37:42','2025-08-11 02:37:42','pending',0,0,0,0),(116,'PO/221/KALENG/poas23','2025-08-12',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-11 18:56:29','2025-08-11 18:56:29','pending',0,0,0,0),(117,'TESTETEST','2025-08-12',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-11 18:59:05','2025-08-11 18:59:05','pending',0,0,0,0),(118,'TESTETEST','2025-08-12',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 18:59:05','2025-08-11 18:59:05','pending',0,1,0,0),(119,'TESTETEST','2025-08-12',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 18:59:05','2025-08-11 18:59:05','pending',0,0,0,0),(120,'tester324234','2025-08-12',2,0,0.00,NULL,NULL,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 19:00:10','2025-08-11 19:00:10','cancelled',0,0,0,0),(121,'initest','2025-08-12',1,0,0.00,NULL,NULL,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 19:21:21','2025-08-11 19:21:21','pending',0,0,0,0),(122,'iniTESTETEST','2025-08-12',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-11 19:22:14','2025-08-11 19:22:14','pending',0,0,0,0),(123,'iniTESTETESTetteq','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-11 20:15:49','2025-08-11 20:15:49','pending',0,0,0,0),(124,'iniTESTETESTetteq','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:15:49','2025-08-11 20:15:49','pending',0,1,0,0),(125,'iniTESTETESTetteq','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 20:15:49','2025-08-11 20:15:49','pending',0,0,0,0),(130,'akucumatest','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:27:06','2025-08-11 20:27:06','pending',0,1,0,0),(131,'akucumatest','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:27:13','2025-08-11 20:27:13','pending',0,1,0,0),(132,'akucumatest1','2025-08-12',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 20:27:48','2025-08-11 20:27:48','pending',0,0,1,0),(137,'akucumatest3','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 20:33:56','2025-08-11 20:33:56','pending',0,0,0,0),(138,'awdiniwane','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-11 21:06:47','2025-08-11 21:06:47','pending',0,0,0,0),(139,'awdiniwane','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-08-11 21:06:47','2025-08-11 21:06:47','pending',0,1,0,0),(140,'awdiniwane','2025-08-12',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 21:06:47','2025-08-11 21:06:47','pending',0,0,0,0),(141,'awdiniwane','2025-08-12',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Sparepart','2025-08-11 21:11:47','2025-08-11 21:11:47','pending',0,0,0,0),(142,'initest123','2025-08-12',1,0,0.00,NULL,NULL,'PO/ASDA/asdaweaw','2025-08-07','2025-08-11',NULL,'Unit','2025-08-11 23:45:20','2025-08-11 23:45:20','pending',0,0,0,0),(143,'PO/221/KALENG/poas231','2025-08-22',1,0,0.00,NULL,NULL,'PO/ASDA/ADIT','2025-08-22','2025-08-22','','Attachment & Battery','2025-08-22 02:21:14','2025-08-22 02:21:14','pending',0,0,5,0),(144,'PO-Unit-2025-07-0001','2025-07-16',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-27 19:36:34',NULL,'pending',0,0,0,0),(145,'PO-Unit-2025-07-0002','2025-07-15',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-27 19:36:34',NULL,'approved',0,0,0,0),(146,'PO-Unit-2025-07-0003','2025-07-14',3,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-08-27 19:36:34',NULL,'completed',0,0,0,0),(147,'BATERAI','2025-08-28',1,0,0.00,NULL,NULL,'123123','2025-08-28','2025-08-28','1231','Attachment & Battery','2025-08-28 02:35:08','2025-08-28 02:35:08','pending',0,0,10,0),(150,'PO/SML/99012399','2025-10-10',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-10-10 04:12:37',NULL,'completed',0,1,0,0),(152,'PO/SML/887712318','2025-10-10',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-10-10 04:13:52',NULL,'completed',0,0,1,0),(154,'PO/SML/ad991231o','2025-10-10',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-10-10 04:20:37',NULL,'approved',0,0,0,0),(155,'PO/SML/dsgsd1231239','2025-10-10',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Dinamis','2025-10-10 04:21:55',NULL,'pending',0,1,1,1),(156,'TEST-CASCADE','2025-10-10',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-10-10 04:43:58',NULL,'pending',0,1,0,0),(158,'PO/SML/SMKUHBASULL','2025-10-10',2,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Dinamis','2025-10-10 09:31:30',NULL,'pending',0,10,0,0),(159,'PO/TERBARU/9923179','2025-10-11',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Dinamis','2025-10-11 02:47:11',NULL,'pending',5,10,10,10),(160,'PO/TERBARU/9923179123','2025-10-11',1,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Unit','2025-10-11 04:05:13','2025-11-19 03:26:37','pending',14,0,0,0),(161,'PO/SML/99012399wad','2025-10-20',3,0,0.00,NULL,NULL,NULL,NULL,NULL,NULL,'Attachment & Battery','2025-10-20 02:28:32',NULL,'pending',0,10,0,0);
/*!40000 ALTER TABLE `purchase_orders` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `rbac_audit_log`
--

DROP TABLE IF EXISTS `rbac_audit_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rbac_audit_log` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `performed_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `rentals` (
  `rental_id` int unsigned NOT NULL,
  `rental_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `forklift_id` int unsigned NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_company` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_phone` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `customer_address` text COLLATE utf8mb4_general_ci,
  `contact_person` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `rental_type` enum('daily','weekly','monthly','yearly') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'daily',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `rental_duration` int NOT NULL COMMENT 'Duration in days/weeks/months based on rental_type',
  `rental_rate` decimal(12,2) NOT NULL COMMENT 'Rate per period',
  `rental_rate_type` enum('daily','weekly','monthly','yearly') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'daily',
  `total_amount` decimal(15,2) NOT NULL COMMENT 'Subtotal before discounts and taxes',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `final_amount` decimal(15,2) NOT NULL COMMENT 'Final amount after all adjustments',
  `security_deposit` decimal(12,2) NOT NULL DEFAULT '0.00',
  `delivery_required` tinyint(1) NOT NULL DEFAULT '0',
  `delivery_address` text COLLATE utf8mb4_general_ci,
  `delivery_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pickup_required` tinyint(1) NOT NULL DEFAULT '0',
  `pickup_address` text COLLATE utf8mb4_general_ci,
  `pickup_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `operator_required` tinyint(1) NOT NULL DEFAULT '0',
  `operator_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `operator_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `fuel_included` tinyint(1) NOT NULL DEFAULT '0',
  `maintenance_included` tinyint(1) NOT NULL DEFAULT '0',
  `insurance_included` tinyint(1) NOT NULL DEFAULT '0',
  `status` enum('draft','confirmed','active','completed','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'draft',
  `contract_status` enum('pending','signed','expired') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `payment_status` enum('pending','partial','paid','overdue') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `payment_method` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `payment_terms` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `po_number` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `contract_file` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `special_terms` text COLLATE utf8mb4_general_ci,
  `created_by` int unsigned DEFAULT NULL,
  `updated_by` int unsigned DEFAULT NULL,
  `approved_by` int unsigned DEFAULT NULL,
  `cancelled_by` int unsigned DEFAULT NULL,
  `completed_by` int unsigned DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `confirmed_by` int unsigned DEFAULT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reports` (
  `id` int unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `format` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int unsigned NOT NULL,
  `status` enum('pending','processing','completed','failed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `data_count` int NOT NULL DEFAULT '0',
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_permissions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `granted` tinyint(1) DEFAULT '1',
  `assigned_by` int DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_role_permissions_role_id` (`role_id`),
  KEY `idx_role_permissions_permission_id` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=472 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_permissions`
--

LOCK TABLES `role_permissions` WRITE;
/*!40000 ALTER TABLE `role_permissions` DISABLE KEYS */;
INSERT INTO `role_permissions` VALUES (369,1,1,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(370,1,2,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(371,1,3,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(372,1,4,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(373,1,5,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(374,1,6,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(375,1,7,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(376,1,8,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(377,1,9,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(378,1,10,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(379,1,11,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(380,1,12,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(381,1,13,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(382,1,14,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(383,1,15,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(384,1,16,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(385,1,17,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(386,1,18,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(387,1,19,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(388,1,20,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(389,1,21,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(390,1,22,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(391,1,23,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(392,1,24,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(400,2,1,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(401,2,2,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(402,2,3,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(403,2,4,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(404,2,5,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(405,2,6,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(406,2,7,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(407,2,8,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(408,2,9,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(409,2,10,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(410,2,11,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(411,2,12,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(412,2,13,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(413,2,14,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(414,2,15,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(415,2,16,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(416,2,17,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(417,2,18,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(418,2,19,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(419,2,20,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(420,2,21,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(421,2,22,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(422,2,23,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(423,2,24,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(431,3,4,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(432,3,5,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(433,3,6,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(434,3,7,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(435,3,13,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(436,3,16,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(442,8,7,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(443,8,8,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(444,8,9,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(445,8,13,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(446,8,16,1,1,'2025-10-20 06:56:08','2025-10-20 06:56:08','2025-10-20 06:56:08'),(459,7,4,1,NULL,'2025-10-20 09:38:32','2025-10-20 09:38:32','2025-10-20 09:38:32'),(460,7,16,1,NULL,'2025-10-20 09:38:32','2025-10-20 09:38:32','2025-10-20 09:38:32'),(461,7,7,1,NULL,'2025-10-20 09:38:32','2025-10-20 09:38:32','2025-10-20 09:38:32'),(462,7,8,1,NULL,'2025-10-20 09:38:32','2025-10-20 09:38:32','2025-10-20 09:38:32'),(463,7,9,1,NULL,'2025-10-20 09:38:32','2025-10-20 09:38:32','2025-10-20 09:38:32'),(464,7,13,1,NULL,'2025-10-20 09:38:32','2025-10-20 09:38:32','2025-10-20 09:38:32'),(465,1,153,1,NULL,'2025-11-22 13:51:27','2025-11-22 13:51:27','2025-11-22 13:51:27'),(466,1,154,1,NULL,'2025-11-22 13:51:27','2025-11-22 13:51:27','2025-11-22 13:51:27'),(467,1,155,1,NULL,'2025-11-22 13:51:27','2025-11-22 13:51:27','2025-11-22 13:51:27'),(468,1,156,1,NULL,'2025-11-22 13:51:27','2025-11-22 13:51:27','2025-11-22 13:51:27'),(469,1,157,1,NULL,'2025-11-22 13:51:27','2025-11-22 13:51:27','2025-11-22 13:51:27'),(470,1,158,1,NULL,'2025-11-22 13:51:27','2025-11-22 13:51:27','2025-11-22 13:51:27'),(471,1,159,1,NULL,'2025-11-22 13:51:27','2025-11-22 13:51:27','2025-11-22 13:51:27');
/*!40000 ALTER TABLE `role_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_preset` tinyint(1) NOT NULL DEFAULT '1',
  `division_id` int DEFAULT NULL,
  `is_system_role` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Super Administrator','super_admin','Full system access with all permissions',1,1,1,1,'2025-08-05 07:01:57','2025-08-05 07:01:57'),(2,'Head Marketing','','Head Marketing',1,NULL,0,1,'2025-10-14 03:08:34','2025-10-14 10:09:19'),(3,'Staff Marketing','','Staff Marketing',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(4,'Head Operational','','Head Operational',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(5,'Staff Operational','','Staff Operational',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(6,'Head Service Diesel','','Head Service Diesel',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-18 03:12:38'),(7,'Staff Service Diesel','','Staff Service Diesel',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-19 23:12:07'),(8,'Head Service Electric','','Head Service Electric',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(9,'Staff Service Electric','','Staff Service Electric',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(10,'Head Purchasing','','Head Purchasing',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(11,'Staff Purchasing','','Staff Purchasing',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(12,'Head Accounting','','Head Accounting',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(13,'Staff Accounting','','Staff Accounting',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(14,'Head HRD','','Head HRD',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(15,'Staff HRD','','Staff HRD',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(16,'Head Warehouse','','Head Warehouse',1,NULL,0,1,'2025-10-14 03:09:31','2025-10-14 03:09:31'),(30,'Administrator','','Administrator',1,NULL,0,1,'2025-10-16 06:20:23','2025-10-16 06:56:15'),(32,'Staff Warehouse','','Staff Warehouse Role',1,NULL,0,1,'2025-10-16 07:06:57','2025-10-16 07:06:57'),(33,'Head IT','','Head IT Role',1,NULL,0,1,'2025-10-16 07:07:17','2025-10-16 07:07:17'),(34,'Staff IT','','Staff IT Role',1,NULL,0,1,'2025-10-16 07:07:17','2025-10-16 07:07:17');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `silo`
--

DROP TABLE IF EXISTS `silo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `silo` (
  `id_silo` int unsigned NOT NULL AUTO_INCREMENT,
  `unit_id` int unsigned NOT NULL COMMENT 'FK ke inventory_unit.id_inventory_unit',
  `status` enum('BELUM_ADA','PENGAJUAN_PJK3','TESTING_PJK3','SURAT_KETERANGAN_PJK3','PENGAJUAN_UPTD','PROSES_UPTD','SILO_TERBIT','SILO_EXPIRED') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'BELUM_ADA',
  `nama_pt_pjk3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nama perusahaan PJK3 yang melakukan pemeriksaan dan testing',
  `tanggal_pengajuan_pjk3` datetime DEFAULT NULL,
  `catatan_pengajuan_pjk3` text COLLATE utf8mb4_unicode_ci,
  `tanggal_testing_pjk3` datetime DEFAULT NULL,
  `hasil_testing_pjk3` text COLLATE utf8mb4_unicode_ci,
  `nomor_surat_keterangan_pjk3` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_surat_keterangan_pjk3` date DEFAULT NULL,
  `file_surat_keterangan_pjk3` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path ke file PDF/image',
  `tanggal_pengajuan_uptd` datetime DEFAULT NULL,
  `catatan_pengajuan_uptd` text COLLATE utf8mb4_unicode_ci,
  `lokasi_disnaker` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_proses_uptd` datetime DEFAULT NULL,
  `catatan_proses_uptd` text COLLATE utf8mb4_unicode_ci,
  `nomor_silo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tanggal_terbit_silo` date DEFAULT NULL,
  `tanggal_expired_silo` date DEFAULT NULL,
  `file_silo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Path ke file PDF/image',
  `created_by` int DEFAULT NULL COMMENT 'FK ke users.id',
  `updated_by` int DEFAULT NULL COMMENT 'FK ke users.id',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_silo`),
  KEY `idx_unit_id` (`unit_id`),
  KEY `idx_status` (`status`),
  KEY `idx_nomor_silo` (`nomor_silo`),
  KEY `idx_tanggal_expired` (`tanggal_expired_silo`),
  KEY `idx_lokasi_disnaker` (`lokasi_disnaker`),
  CONSTRAINT `fk_silo_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel untuk data SILO (Surat Izin Layak Operasi)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `silo`
--

LOCK TABLES `silo` WRITE;
/*!40000 ALTER TABLE `silo` DISABLE KEYS */;
INSERT INTO `silo` VALUES (1,1,'SILO_TERBIT',NULL,'2025-11-21 00:00:00','sadawd','2025-11-21 00:00:00','ok','11111111','2025-11-21',NULL,'2025-11-21 00:00:00','AWDAW',NULL,'2025-11-21 00:00:00','AWDAW','4324234','2025-11-21','2025-11-21',NULL,1,1,'2025-11-21 20:14:57','2025-11-21 21:16:07'),(2,23,'SILO_TERBIT',NULL,'2025-11-21 00:00:00','aaadas','2025-11-21 00:00:00','adwa','wadawd','2025-11-21',NULL,'2025-11-21 00:00:00','awdaw',NULL,'2025-11-21 00:00:00','wadaw','awdawd21231312','2025-11-21','2026-01-22',NULL,1,1,'2025-11-21 21:27:03','2025-11-21 21:32:42'),(3,16,'SILO_TERBIT','PT GAHARU SAKTI PRATAMA','2025-11-21 00:00:00','dawdwa',NULL,NULL,'awdwadaw','2025-11-21','uploads/silo/pjk3/pjk3_3_1763738373.pdf','2025-11-21 00:00:00',NULL,'wadawda',NULL,NULL,'12312312','2025-11-21','2026-02-21','uploads/silo/silo/silo_3_1763739749.pdf',1,1,'2025-11-21 22:10:50','2025-11-21 22:42:29'),(4,5,'PENGAJUAN_PJK3','PT GAHARU SAKTI PRATAMA','2025-11-21 00:00:00','awdaw',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,NULL,'2025-11-21 22:23:39','2025-11-21 22:23:39');
/*!40000 ALTER TABLE `silo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `silo_history`
--

DROP TABLE IF EXISTS `silo_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `silo_history` (
  `id_history` int unsigned NOT NULL AUTO_INCREMENT,
  `silo_id` int unsigned NOT NULL,
  `status_lama` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_baru` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci,
  `changed_by` int DEFAULT NULL,
  `changed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_history`),
  KEY `idx_silo_id` (`silo_id`),
  CONSTRAINT `fk_silo_history_silo` FOREIGN KEY (`silo_id`) REFERENCES `silo` (`id_silo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabel untuk tracking perubahan status SILO';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `silo_history`
--

LOCK TABLES `silo_history` WRITE;
/*!40000 ALTER TABLE `silo_history` DISABLE KEYS */;
INSERT INTO `silo_history` VALUES (1,1,NULL,'PENGAJUAN_PJK3','Pengajuan SILO baru dibuat',1,'2025-11-21 20:14:57'),(2,1,'PENGAJUAN_PJK3','TESTING_PJK3','ok',1,'2025-11-21 20:15:34'),(3,1,'TESTING_PJK3','SURAT_KETERANGAN_PJK3','awdaw',1,'2025-11-21 20:16:12'),(4,1,'SURAT_KETERANGAN_PJK3','PENGAJUAN_UPTD','AWDWA',1,'2025-11-21 21:14:24'),(5,1,'PENGAJUAN_UPTD','PROSES_UPTD','AWD',1,'2025-11-21 21:14:38'),(6,1,'PROSES_UPTD','SILO_TERBIT','AWDAW',1,'2025-11-21 21:16:07'),(7,2,NULL,'PENGAJUAN_PJK3','Pengajuan SILO baru dibuat',1,'2025-11-21 21:27:03'),(8,2,'PENGAJUAN_PJK3','TESTING_PJK3','awdwa',1,'2025-11-21 21:27:27'),(9,2,'TESTING_PJK3','SURAT_KETERANGAN_PJK3','adww',1,'2025-11-21 21:27:57'),(10,2,'SURAT_KETERANGAN_PJK3','PENGAJUAN_UPTD','wdawdaw',1,'2025-11-21 21:31:51'),(11,2,'PENGAJUAN_UPTD','PROSES_UPTD','awdaw',1,'2025-11-21 21:32:04'),(12,2,'PROSES_UPTD','SILO_TERBIT','wdadawd',1,'2025-11-21 21:32:42'),(13,3,NULL,'PENGAJUAN_PJK3','Pengajuan SILO baru dibuat',1,'2025-11-21 22:10:50'),(14,3,'PENGAJUAN_PJK3','SURAT_KETERANGAN_PJK3','awdaw',1,'2025-11-21 22:19:33'),(15,4,NULL,'PENGAJUAN_PJK3','Pengajuan SILO baru dibuat',1,'2025-11-21 22:23:39'),(16,3,'SURAT_KETERANGAN_PJK3','PENGAJUAN_UPTD','awdawd',1,'2025-11-21 22:41:28'),(17,3,'PENGAJUAN_UPTD','SILO_TERBIT','awdawd',1,'2025-11-21 22:42:29');
/*!40000 ALTER TABLE `silo_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sparepart`
--

DROP TABLE IF EXISTS `sparepart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sparepart` (
  `id_sparepart` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `desc_sparepart` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_sparepart`),
  UNIQUE KEY `kode` (`kode`)
) ENGINE=InnoDB AUTO_INCREMENT=146 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sparepart`
--

LOCK TABLES `sparepart` WRITE;
/*!40000 ALTER TABLE `sparepart` DISABLE KEYS */;
INSERT INTO `sparepart` VALUES (1,'ENG-001','Oil Filter - Honda GX160','2025-10-08 03:40:45','2025-10-08 03:40:45'),(2,'ENG-002','Air Filter - Honda GX160','2025-10-08 03:40:45','2025-10-08 03:40:45'),(3,'ENG-003','Spark Plug - NGK BPR6ES','2025-10-08 03:40:45','2025-10-08 03:40:45'),(4,'ENG-004','Engine Oil - SAE 10W-30','2025-10-08 03:40:45','2025-10-08 03:40:45'),(5,'ENG-005','Fuel Filter - Honda GX160','2025-10-08 03:40:45','2025-10-08 03:40:45'),(6,'ENG-006','Carburetor Gasket Set','2025-10-08 03:40:45','2025-10-08 03:40:45'),(7,'ENG-007','Valve Spring - Honda GX160','2025-10-08 03:40:45','2025-10-08 03:40:45'),(8,'ENG-008','Piston Ring Set - Honda GX160','2025-10-08 03:40:45','2025-10-08 03:40:45'),(9,'ENG-009','Connecting Rod - Honda GX160','2025-10-08 03:40:45','2025-10-08 03:40:45'),(10,'ENG-010','Crankshaft - Honda GX160','2025-10-08 03:40:45','2025-10-08 03:40:45'),(11,'HYD-001','Hydraulic Oil - AW46','2025-10-08 03:40:45','2025-10-08 03:40:45'),(12,'HYD-002','Hydraulic Filter - Parker','2025-10-08 03:40:45','2025-10-08 03:40:45'),(13,'HYD-003','Hydraulic Hose - 1/4\" x 2m','2025-10-08 03:40:45','2025-10-08 03:40:45'),(14,'HYD-004','Hydraulic Hose - 3/8\" x 3m','2025-10-08 03:40:45','2025-10-08 03:40:45'),(15,'HYD-005','Hydraulic Fitting - JIC 6','2025-10-08 03:40:45','2025-10-08 03:40:45'),(16,'HYD-006','Hydraulic Fitting - JIC 8','2025-10-08 03:40:45','2025-10-08 03:40:45'),(17,'HYD-007','Hydraulic Pump Seal Kit','2025-10-08 03:40:45','2025-10-08 03:40:45'),(18,'HYD-008','Hydraulic Cylinder Seal Kit','2025-10-08 03:40:45','2025-10-08 03:40:45'),(19,'HYD-009','Hydraulic Valve - Check Valve','2025-10-08 03:40:45','2025-10-08 03:40:45'),(20,'HYD-010','Hydraulic Valve - Relief Valve','2025-10-08 03:40:45','2025-10-08 03:40:45'),(21,'ELC-001','Battery - 12V 7Ah','2025-10-08 03:40:45','2025-10-08 03:40:45'),(22,'ELC-002','Battery - 12V 12Ah','2025-10-08 03:40:45','2025-10-08 03:40:45'),(23,'ELC-003','Battery Terminal - Positive','2025-10-08 03:40:45','2025-10-08 03:40:45'),(24,'ELC-004','Battery Terminal - Negative','2025-10-08 03:40:45','2025-10-08 03:40:45'),(25,'ELC-005','Fuse - 10A','2025-10-08 03:40:45','2025-10-08 03:40:45'),(26,'ELC-006','Fuse - 15A','2025-10-08 03:40:45','2025-10-08 03:40:45'),(27,'ELC-007','Fuse - 20A','2025-10-08 03:40:45','2025-10-08 03:40:45'),(28,'ELC-008','Relay - 12V 30A','2025-10-08 03:40:45','2025-10-08 03:40:45'),(29,'ELC-009','Switch - Toggle Switch','2025-10-08 03:40:45','2025-10-08 03:40:45'),(30,'ELC-010','Wire - 12 AWG Red','2025-10-08 03:40:45','2025-10-08 03:40:45'),(31,'ELC-011','Wire - 12 AWG Black','2025-10-08 03:40:45','2025-10-08 03:40:45'),(32,'ELC-012','Connector - Bullet 2mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(33,'ELC-013','Connector - Bullet 4mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(34,'ELC-014','LED Light - 12V White','2025-10-08 03:40:45','2025-10-08 03:40:45'),(35,'ELC-015','LED Light - 12V Red','2025-10-08 03:40:45','2025-10-08 03:40:45'),(36,'BRK-001','Brake Pad - Front','2025-10-08 03:40:45','2025-10-08 03:40:45'),(37,'BRK-002','Brake Pad - Rear','2025-10-08 03:40:45','2025-10-08 03:40:45'),(38,'BRK-003','Brake Disc - Front','2025-10-08 03:40:45','2025-10-08 03:40:45'),(39,'BRK-004','Brake Disc - Rear','2025-10-08 03:40:45','2025-10-08 03:40:45'),(40,'BRK-005','Brake Fluid - DOT 3','2025-10-08 03:40:45','2025-10-08 03:40:45'),(41,'BRK-006','Brake Line - Steel','2025-10-08 03:40:45','2025-10-08 03:40:45'),(42,'BRK-007','Brake Caliper Seal Kit','2025-10-08 03:40:45','2025-10-08 03:40:45'),(43,'BRK-008','Brake Master Cylinder','2025-10-08 03:40:45','2025-10-08 03:40:45'),(44,'BRK-009','Brake Booster','2025-10-08 03:40:45','2025-10-08 03:40:45'),(45,'BRK-010','Brake Light Switch','2025-10-08 03:40:45','2025-10-08 03:40:45'),(46,'TRN-001','Transmission Oil - 80W-90','2025-10-08 03:40:45','2025-10-08 03:40:45'),(47,'TRN-002','Clutch Plate - Dry','2025-10-08 03:40:45','2025-10-08 03:40:45'),(48,'TRN-003','Clutch Disc - Single','2025-10-08 03:40:45','2025-10-08 03:40:45'),(49,'TRN-004','Clutch Bearing - Release','2025-10-08 03:40:45','2025-10-08 03:40:45'),(50,'TRN-005','Drive Belt - V-Belt A','2025-10-08 03:40:45','2025-10-08 03:40:45'),(51,'TRN-006','Drive Belt - V-Belt B','2025-10-08 03:40:45','2025-10-08 03:40:45'),(52,'TRN-007','Chain - Roller Chain #40','2025-10-08 03:40:45','2025-10-08 03:40:45'),(53,'TRN-008','Chain - Roller Chain #50','2025-10-08 03:40:45','2025-10-08 03:40:45'),(54,'TRN-009','Sprocket - 15 Teeth','2025-10-08 03:40:45','2025-10-08 03:40:45'),(55,'TRN-010','Sprocket - 20 Teeth','2025-10-08 03:40:45','2025-10-08 03:40:45'),(56,'TIR-001','Tire - 6.00-9 4PR','2025-10-08 03:40:45','2025-10-08 03:40:45'),(57,'TIR-002','Tire - 7.00-9 6PR','2025-10-08 03:40:45','2025-10-08 03:40:45'),(58,'TIR-003','Tire - 8.00-9 8PR','2025-10-08 03:40:45','2025-10-08 03:40:45'),(59,'TIR-004','Inner Tube - 6.00-9','2025-10-08 03:40:45','2025-10-08 03:40:45'),(60,'TIR-005','Inner Tube - 7.00-9','2025-10-08 03:40:45','2025-10-08 03:40:45'),(61,'TIR-006','Wheel Rim - 9\" Steel','2025-10-08 03:40:45','2025-10-08 03:40:45'),(62,'TIR-007','Wheel Rim - 10\" Steel','2025-10-08 03:40:45','2025-10-08 03:40:45'),(63,'TIR-008','Wheel Bearing - 6203','2025-10-08 03:40:45','2025-10-08 03:40:45'),(64,'TIR-009','Wheel Bearing - 6204','2025-10-08 03:40:45','2025-10-08 03:40:45'),(65,'TIR-010','Wheel Hub - Front','2025-10-08 03:40:45','2025-10-08 03:40:45'),(66,'MST-001','Mast Seal Kit - 3 Stage','2025-10-08 03:40:45','2025-10-08 03:40:45'),(67,'MST-002','Mast Seal Kit - 2 Stage','2025-10-08 03:40:45','2025-10-08 03:40:45'),(68,'MST-003','Mast Chain - 1/2\" x 10ft','2025-10-08 03:40:45','2025-10-08 03:40:45'),(69,'MST-004','Mast Chain - 5/8\" x 12ft','2025-10-08 03:40:45','2025-10-08 03:40:45'),(70,'MST-005','Mast Roller - Upper','2025-10-08 03:40:45','2025-10-08 03:40:45'),(71,'MST-006','Mast Roller - Lower','2025-10-08 03:40:45','2025-10-08 03:40:45'),(72,'MST-007','Mast Bearing - Thrust','2025-10-08 03:40:45','2025-10-08 03:40:45'),(73,'MST-008','Mast Bearing - Radial','2025-10-08 03:40:45','2025-10-08 03:40:45'),(74,'MST-009','Fork - 1070mm x 100mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(75,'MST-010','Fork - 1220mm x 100mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(76,'MST-011','Fork - 1370mm x 100mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(77,'MST-012','Fork - 1520mm x 100mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(78,'MST-013','Fork Shaft - 30mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(79,'MST-014','Fork Shaft - 35mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(80,'MST-015','Fork Bushing - Bronze','2025-10-08 03:40:45','2025-10-08 03:40:45'),(81,'ATT-001','Side Shifter - Hydraulic','2025-10-08 03:40:45','2025-10-08 03:40:45'),(82,'ATT-002','Side Shifter - Manual','2025-10-08 03:40:45','2025-10-08 03:40:45'),(83,'ATT-003','Fork Positioner - Hydraulic','2025-10-08 03:40:45','2025-10-08 03:40:45'),(84,'ATT-004','Fork Positioner - Manual','2025-10-08 03:40:45','2025-10-08 03:40:45'),(85,'ATT-005','Rotator - 360 Degree','2025-10-08 03:40:45','2025-10-08 03:40:45'),(86,'ATT-006','Rotator - 180 Degree','2025-10-08 03:40:45','2025-10-08 03:40:45'),(87,'ATT-007','Clamp - Paper Roll','2025-10-08 03:40:45','2025-10-08 03:40:45'),(88,'ATT-008','Clamp - Carton','2025-10-08 03:40:45','2025-10-08 03:40:45'),(89,'ATT-009','Crane - 500kg','2025-10-08 03:40:45','2025-10-08 03:40:45'),(90,'ATT-010','Crane - 1000kg','2025-10-08 03:40:45','2025-10-08 03:40:45'),(91,'SAF-001','Safety Belt - 3 Point','2025-10-08 03:40:45','2025-10-08 03:40:45'),(92,'SAF-002','Safety Belt - 2 Point','2025-10-08 03:40:45','2025-10-08 03:40:45'),(93,'SAF-003','Warning Light - Amber','2025-10-08 03:40:45','2025-10-08 03:40:45'),(94,'SAF-004','Warning Light - Red','2025-10-08 03:40:45','2025-10-08 03:40:45'),(95,'SAF-005','Horn - 12V Electric','2025-10-08 03:40:45','2025-10-08 03:40:45'),(96,'SAF-006','Mirror - Convex','2025-10-08 03:40:45','2025-10-08 03:40:45'),(97,'SAF-007','Reflector - Red','2025-10-08 03:40:45','2025-10-08 03:40:45'),(98,'SAF-008','Reflector - White','2025-10-08 03:40:45','2025-10-08 03:40:45'),(99,'SAF-009','First Aid Kit','2025-10-08 03:40:45','2025-10-08 03:40:45'),(100,'SAF-010','Fire Extinguisher - 1kg','2025-10-08 03:40:45','2025-10-08 03:40:45'),(101,'TOL-001','Wrench Set - Metric','2025-10-08 03:40:45','2025-10-08 03:40:45'),(102,'TOL-002','Wrench Set - Imperial','2025-10-08 03:40:45','2025-10-08 03:40:45'),(103,'TOL-003','Socket Set - 1/4\" Drive','2025-10-08 03:40:45','2025-10-08 03:40:45'),(104,'TOL-004','Socket Set - 3/8\" Drive','2025-10-08 03:40:45','2025-10-08 03:40:45'),(105,'TOL-005','Socket Set - 1/2\" Drive','2025-10-08 03:40:45','2025-10-08 03:40:45'),(106,'TOL-006','Screwdriver Set - Phillips','2025-10-08 03:40:45','2025-10-08 03:40:45'),(107,'TOL-007','Screwdriver Set - Flat','2025-10-08 03:40:45','2025-10-08 03:40:45'),(108,'TOL-008','Pliers Set - Combination','2025-10-08 03:40:45','2025-10-08 03:40:45'),(109,'TOL-009','Hammer - Ball Peen','2025-10-08 03:40:45','2025-10-08 03:40:45'),(110,'TOL-010','Hammer - Rubber Mallet','2025-10-08 03:40:45','2025-10-08 03:40:45'),(111,'TOL-011','Grease Gun - Manual','2025-10-08 03:40:45','2025-10-08 03:40:45'),(112,'TOL-012','Grease - Lithium','2025-10-08 03:40:45','2025-10-08 03:40:45'),(113,'TOL-013','Thread Lock - Blue','2025-10-08 03:40:45','2025-10-08 03:40:45'),(114,'TOL-014','Thread Lock - Red','2025-10-08 03:40:45','2025-10-08 03:40:45'),(115,'TOL-015','Sealant - RTV Silicone','2025-10-08 03:40:45','2025-10-08 03:40:45'),(116,'CON-001','Rag - Cotton','2025-10-08 03:40:45','2025-10-08 03:40:45'),(117,'CON-002','Rag - Synthetic','2025-10-08 03:40:45','2025-10-08 03:40:45'),(118,'CON-003','Cleaning Solvent - Degreaser','2025-10-08 03:40:45','2025-10-08 03:40:45'),(119,'CON-004','Cleaning Solvent - Brake Cleaner','2025-10-08 03:40:45','2025-10-08 03:40:45'),(120,'CON-005','Sandpaper - 120 Grit','2025-10-08 03:40:45','2025-10-08 03:40:45'),(121,'CON-006','Sandpaper - 240 Grit','2025-10-08 03:40:45','2025-10-08 03:40:45'),(122,'CON-007','Sandpaper - 400 Grit','2025-10-08 03:40:45','2025-10-08 03:40:45'),(123,'CON-008','Wire Brush - Steel','2025-10-08 03:40:45','2025-10-08 03:40:45'),(124,'CON-009','Wire Brush - Brass','2025-10-08 03:40:45','2025-10-08 03:40:45'),(125,'CON-010','Shop Towel - Paper','2025-10-08 03:40:45','2025-10-08 03:40:45'),(126,'CON-011','Duct Tape - Silver','2025-10-08 03:40:45','2025-10-08 03:40:45'),(127,'CON-012','Electrical Tape - Black','2025-10-08 03:40:45','2025-10-08 03:40:45'),(128,'CON-013','Masking Tape - Blue','2025-10-08 03:40:45','2025-10-08 03:40:45'),(129,'CON-014','Cable Tie - 4\"','2025-10-08 03:40:45','2025-10-08 03:40:45'),(130,'CON-015','Cable Tie - 6\"','2025-10-08 03:40:45','2025-10-08 03:40:45'),(131,'SPC-001','Load Backrest - 1000mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(132,'SPC-002','Load Backrest - 1200mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(133,'SPC-003','Load Backrest - 1400mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(134,'SPC-004','Load Backrest - 1600mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(135,'SPC-005','Load Backrest - 1800mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(136,'SPC-006','Load Backrest - 2000mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(137,'SPC-007','Load Backrest - 2200mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(138,'SPC-008','Load Backrest - 2400mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(139,'SPC-009','Load Backrest - 2600mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(140,'SPC-010','Load Backrest - 2800mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(141,'SPC-011','Load Backrest - 3000mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(142,'SPC-012','Load Backrest - 3200mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(143,'SPC-013','Load Backrest - 3400mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(144,'SPC-014','Load Backrest - 3600mm','2025-10-08 03:40:45','2025-10-08 03:40:45'),(145,'SPC-015','Load Backrest - 3800mm','2025-10-08 03:40:45','2025-10-08 03:40:45');
/*!40000 ALTER TABLE `sparepart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk`
--

DROP TABLE IF EXISTS `spk`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `nomor_spk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'UNIT',
  `kontrak_id` int unsigned DEFAULT NULL,
  `kontrak_spesifikasi_id` int unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
  `jumlah_unit` int DEFAULT '1' COMMENT 'Jumlah unit dalam SPK ini',
  `po_kontrak_nomor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `delivery_plan` date DEFAULT NULL,
  `spesifikasi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `status` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'SUBMITTED',
  `catatan` text COLLATE utf8mb4_general_ci,
  `dibuat_oleh` int DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `jenis_perintah_kerja_id` int DEFAULT NULL,
  `tujuan_perintah_kerja_id` int DEFAULT NULL,
  `status_eksekusi_workflow_id` int DEFAULT '1',
  `workflow_notes` text COLLATE utf8mb4_general_ci,
  `workflow_created_at` timestamp NULL DEFAULT NULL,
  `workflow_updated_at` timestamp NULL DEFAULT NULL,
  `rollback_enabled` tinyint(1) DEFAULT '1',
  `last_rollback_at` timestamp NULL DEFAULT NULL,
  `rollback_count` int DEFAULT '0',
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
) ENGINE=InnoDB AUTO_INCREMENT=77 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk`
--

LOCK TABLES `spk` WRITE;
/*!40000 ALTER TABLE `spk` DISABLE KEYS */;
INSERT INTO `spk` VALUES (27,'SPK/202509/001','UNIT',NULL,NULL,2,'test12345','MONORKOBO','JAJA','09324987729','BEKASI','2025-09-03','{\"departemen_id\": \"2\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"PALLET STACKER\", \"merk_unit\": \"HELI\", \"model_unit\": null, \"kapasitas_id\": \"14\", \"attachment_tipe\": \"PAPER ROLL CLAMP\", \"attachment_merk\": null, \"jenis_baterai\": \"Lithium-ion\", \"charger_id\": \"9\", \"mast_id\": \"22\", \"ban_id\": \"6\", \"roda_id\": \"3\", \"valve_id\": \"3\", \"aksesoris\": [], \"persiapan_battery_action\": \"keep_existing\", \"persiapan_battery_id\": \"6\", \"persiapan_charger_action\": \"assign\", \"persiapan_charger_id\": \"12\", \"fabrikasi_attachment_id\": \"15\", \"prepared_units\": [{\"unit_id\": \"1\", \"battery_inventory_id\": \"5\", \"charger_inventory_id\": \"10\", \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\"]\", \"mekanik\": \"JOHANA - DEPI\", \"catatan\": \"ok\", \"timestamp\": \"2025-09-03 09:40:09\"}, {\"unit_id\": \"12\", \"battery_inventory_id\": \"6\", \"charger_inventory_id\": \"12\", \"attachment_inventory_id\": \"15\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\"]\", \"mekanik\": \"JOHANA - DEPI\", \"catatan\": \"a\", \"timestamp\": \"2025-09-03 09:41:18\"}], \"migrated_persiapan_unit_id\": 12, \"migrated_persiapan_unit_mekanik\": \"JOHANA - DEPI\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-03\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-03\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-03 09:41:03\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"JOHANA - DEPI\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-02\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-02\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-03 09:41:10\", \"migrated_painting_mekanik\": \"ARIZAL-EKA\", \"migrated_painting_estimasi_mulai\": \"2025-09-03\", \"migrated_painting_estimasi_selesai\": \"2025-09-03\", \"migrated_painting_tanggal_approve\": \"2025-09-03 09:41:14\", \"migrated_pdi_mekanik\": \"JOHANA - DEPI\", \"migrated_pdi_estimasi_mulai\": \"2025-09-03\", \"migrated_pdi_estimasi_selesai\": \"2025-09-03\", \"migrated_pdi_tanggal_approve\": \"2025-09-03 09:41:18\", \"migrated_pdi_catatan\": \"a\"}','IN_PROGRESS',NULL,1,'2025-09-03 09:38:49','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(28,'SPK/202509/002','UNIT',44,19,2,'MSI','MSI','MSI','09213123123','EROPA',NULL,'{\"departemen_id\": \"2\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"HAND PALLET\", \"merk_unit\": \"HELI\", \"model_unit\": null, \"kapasitas_id\": \"41\", \"attachment_tipe\": \"FORK POSITIONER\", \"attachment_merk\": null, \"jenis_baterai\": \"Lithium-ion\", \"charger_id\": \"5\", \"mast_id\": \"22\", \"ban_id\": \"6\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"persiapan_battery_action\": \"assign\", \"persiapan_battery_id\": \"7\", \"persiapan_charger_action\": \"assign\", \"persiapan_charger_id\": \"14\", \"fabrikasi_attachment_id\": \"15\", \"prepared_units\": [{\"unit_id\": \"1\", \"battery_inventory_id\": \"5\", \"charger_inventory_id\": \"10\", \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\", \"mekanik\": \"JOHANA - DEPI\", \"catatan\": \"ok\", \"timestamp\": \"2025-09-04 04:14:01\"}, {\"unit_id\": \"2\", \"battery_inventory_id\": \"7\", \"charger_inventory_id\": \"14\", \"attachment_inventory_id\": \"15\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\", \"mekanik\": \"JOHANA - DEPI\", \"catatan\": \"ok\", \"timestamp\": \"2025-09-04 04:14:39\"}], \"migrated_persiapan_unit_id\": 2, \"migrated_persiapan_unit_mekanik\": \"JOHANA - DEPI\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-04\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-04\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-04 04:14:20\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"JOHANA - DEPI\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-04\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-04\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-04 04:14:29\", \"migrated_painting_mekanik\": \"ARIZAL-EKA\", \"migrated_painting_estimasi_mulai\": \"2025-09-04\", \"migrated_painting_estimasi_selesai\": \"2025-09-04\", \"migrated_painting_tanggal_approve\": \"2025-09-04 04:14:34\", \"migrated_pdi_mekanik\": \"JOHANA - DEPI\", \"migrated_pdi_estimasi_mulai\": \"2025-09-04\", \"migrated_pdi_estimasi_selesai\": \"2025-09-04\", \"migrated_pdi_tanggal_approve\": \"2025-09-04 04:14:39\", \"migrated_pdi_catatan\": \"ok\"}','COMPLETED',NULL,1,'2025-09-04 04:13:09','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(29,'SPK/202509/003','UNIT',54,37,2,'KNTRK/2209/0001','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-09','{\"departemen_id\": \"2\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"PALLET STACKER\", \"merk_unit\": \"HELI\", \"model_unit\": null, \"kapasitas_id\": \"42\", \"attachment_tipe\": \"FORKLIFT SCALE\", \"attachment_merk\": null, \"jenis_baterai\": \"Lead Acid\", \"charger_id\": \"1\", \"mast_id\": \"14\", \"ban_id\": \"6\", \"roda_id\": \"1\", \"valve_id\": \"1\", \"aksesoris\": [], \"persiapan_battery_action\": \"keep_existing\", \"persiapan_battery_id\": \"6\", \"persiapan_charger_action\": \"assign\", \"persiapan_charger_id\": \"12\", \"fabrikasi_attachment_id\": \"4\", \"prepared_units\": [{\"unit_id\": \"12\", \"battery_inventory_id\": \"6\", \"charger_inventory_id\": \"5\", \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\", \"mekanik\": \"IYAN\", \"catatan\": \"ok\", \"timestamp\": \"2025-09-09 10:04:37\"}, {\"unit_id\": \"12\", \"battery_inventory_id\": \"6\", \"charger_inventory_id\": \"12\", \"attachment_inventory_id\": \"4\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\", \"mekanik\": \"IYAN\", \"catatan\": \"123\", \"timestamp\": \"2025-09-09 10:06:17\"}], \"migrated_persiapan_unit_id\": 12, \"migrated_persiapan_unit_mekanik\": \"IYAN\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-09\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-09\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-09 10:06:03\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"JOHANA - DEPI\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-09\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-09\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-09 10:06:09\", \"migrated_painting_mekanik\": \"JOHANA - DEPI\", \"migrated_painting_estimasi_mulai\": \"2025-09-09\", \"migrated_painting_estimasi_selesai\": \"2025-09-09\", \"migrated_painting_tanggal_approve\": \"2025-09-09 10:06:13\", \"migrated_pdi_mekanik\": \"IYAN\", \"migrated_pdi_estimasi_mulai\": \"2025-09-09\", \"migrated_pdi_estimasi_selesai\": \"2025-09-09\", \"migrated_pdi_tanggal_approve\": \"2025-09-09 10:06:17\", \"migrated_pdi_catatan\": \"123\"}','COMPLETED',NULL,1,'2025-09-09 10:03:41','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(36,'SPK/202509/004','UNIT',55,39,2,'SML/DS/121025','LG','ANDI','08213564778','Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190','2025-09-12','{\"departemen_id\": \"1\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"THREE WHEEL\", \"merk_unit\": \"HANGCHA\", \"model_unit\": null, \"kapasitas_id\": \"14\", \"attachment_tipe\": \"FORK POSITIONER\", \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": \"16\", \"ban_id\": \"6\", \"roda_id\": \"2\", \"valve_id\": \"2\", \"aksesoris\": [], \"fabrikasi_attachment_id\": \"3\", \"prepared_units\": [{\"unit_id\": \"4\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"4\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\", \"mekanik\": \"INDRA\", \"catatan\": \"OK\", \"timestamp\": \"2025-09-12 06:37:44\"}, {\"unit_id\": \"10\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"3\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\", \"mekanik\": \"UDUD\", \"catatan\": \"ok\", \"timestamp\": \"2025-09-12 06:38:33\"}], \"migrated_persiapan_unit_id\": 10, \"migrated_persiapan_unit_mekanik\": \"IYAN\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-12\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-12\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-12 06:38:03\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"BADRUN\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-12\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-12\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-12 06:38:16\", \"migrated_painting_mekanik\": \"INDRA\", \"migrated_painting_estimasi_mulai\": \"2025-09-12\", \"migrated_painting_estimasi_selesai\": \"2025-09-12\", \"migrated_painting_tanggal_approve\": \"2025-09-12 06:38:24\", \"migrated_pdi_mekanik\": \"UDUD\", \"migrated_pdi_estimasi_mulai\": \"2025-09-12\", \"migrated_pdi_estimasi_selesai\": \"2025-09-12\", \"migrated_pdi_tanggal_approve\": \"2025-09-12 06:38:33\", \"migrated_pdi_catatan\": \"ok\"}','COMPLETED',NULL,1,'2025-09-12 06:35:58','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(37,'SPK/202509/005','UNIT',56,40,1,'TEST/AUTO/001','Test Auto Update','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-12','{\"departemen_id\": \"2\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": null, \"merk_unit\": null, \"model_unit\": null, \"kapasitas_id\": \"42\", \"attachment_tipe\": null, \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": null, \"ban_id\": null, \"roda_id\": null, \"valve_id\": null, \"aksesoris\": [], \"fabrikasi_attachment_id\": \"16\", \"prepared_units\": [{\"unit_id\": \"7\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[]\", \"mekanik\": \"123\", \"catatan\": \"123\", \"timestamp\": \"2025-09-12 10:06:06\"}], \"migrated_persiapan_unit_id\": 7, \"migrated_persiapan_unit_mekanik\": \"123\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-12\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-12\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-12 10:05:41\", \"migrated_persiapan_aksesoris_tersedia\": \"[]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"123\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-12\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-12\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-12 10:05:52\", \"migrated_painting_mekanik\": \"123\", \"migrated_painting_estimasi_mulai\": \"2025-09-12\", \"migrated_painting_estimasi_selesai\": \"2025-09-12\", \"migrated_painting_tanggal_approve\": \"2025-09-12 10:05:59\", \"migrated_pdi_mekanik\": \"123\", \"migrated_pdi_estimasi_mulai\": \"2025-09-12\", \"migrated_pdi_estimasi_selesai\": \"2025-09-12\", \"migrated_pdi_tanggal_approve\": \"2025-09-12 10:06:06\", \"migrated_pdi_catatan\": \"123\"}','COMPLETED',NULL,1,'2025-09-12 10:05:22','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(38,'SPK/202509/006','UNIT',54,37,1,'KNTRK/2209/0001','Sarana Mitra Luas',NULL,NULL,NULL,'2025-09-13','{\"departemen_id\": \"2\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"PALLET STACKER\", \"merk_unit\": \"HELI\", \"model_unit\": null, \"kapasitas_id\": \"42\", \"attachment_tipe\": \"FORKLIFT SCALE\", \"attachment_merk\": null, \"jenis_baterai\": \"Lead Acid\", \"charger_id\": \"1\", \"mast_id\": \"14\", \"ban_id\": \"6\", \"roda_id\": \"1\", \"valve_id\": \"1\", \"aksesoris\": [], \"fabrikasi_attachment_id\": \"16\", \"prepared_units\": [{\"unit_id\": \"5\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\", \"mekanik\": \"123\", \"catatan\": \"1\", \"timestamp\": \"2025-09-13 01:46:42\"}], \"migrated_persiapan_unit_id\": 5, \"migrated_persiapan_unit_mekanik\": \"JAJA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-13\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-13\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-13 01:46:15\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"123\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-13\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-13\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-13 01:46:26\", \"migrated_painting_mekanik\": \"123\", \"migrated_painting_estimasi_mulai\": \"2025-09-13\", \"migrated_painting_estimasi_selesai\": \"2025-09-13\", \"migrated_painting_tanggal_approve\": \"2025-09-13 01:46:32\", \"migrated_pdi_mekanik\": \"123\", \"migrated_pdi_estimasi_mulai\": \"2025-09-13\", \"migrated_pdi_estimasi_selesai\": \"2025-09-13\", \"migrated_pdi_tanggal_approve\": \"2025-09-13 01:46:42\", \"migrated_pdi_catatan\": \"1\"}','COMPLETED',NULL,1,'2025-09-13 01:33:11','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(39,'SPK/202509/007','UNIT',57,41,2,'test/1/1/5','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13','{\"departemen_id\": \"3\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"COUNTER BALANCE\", \"merk_unit\": \"HELI\", \"model_unit\": null, \"kapasitas_id\": \"40\", \"attachment_tipe\": \"FORK POSITIONER\", \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": \"16\", \"ban_id\": \"3\", \"roda_id\": \"1\", \"valve_id\": \"3\", \"aksesoris\": [], \"fabrikasi_attachment_id\": \"15\", \"prepared_units\": [{\"unit_id\": \"6\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"P3K\\\"]\", \"mekanik\": \"123\", \"catatan\": \"a\", \"timestamp\": \"2025-09-13 02:58:09\"}, {\"unit_id\": \"9\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"15\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\", \"mekanik\": \"123\", \"catatan\": \"a\", \"timestamp\": \"2025-09-13 02:58:34\"}], \"migrated_persiapan_unit_id\": 9, \"migrated_persiapan_unit_mekanik\": \"JAJA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-13\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-13\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-13 02:58:20\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"123\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-13\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-13\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-13 02:58:26\", \"migrated_painting_mekanik\": \"123\", \"migrated_painting_estimasi_mulai\": \"2025-09-13\", \"migrated_painting_estimasi_selesai\": \"2025-09-13\", \"migrated_painting_tanggal_approve\": \"2025-09-13 02:58:29\", \"migrated_pdi_mekanik\": \"123\", \"migrated_pdi_estimasi_mulai\": \"2025-09-13\", \"migrated_pdi_estimasi_selesai\": \"2025-09-13\", \"migrated_pdi_tanggal_approve\": \"2025-09-13 02:58:34\", \"migrated_pdi_catatan\": \"a\"}','COMPLETED',NULL,1,'2025-09-13 02:57:15','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(40,'SPK/202509/008','UNIT',57,42,1,'test/1/1/5','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-13','{\"departemen_id\": \"3\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"PALLET STACKER\", \"merk_unit\": \"KOMATSU\", \"model_unit\": null, \"kapasitas_id\": \"42\", \"attachment_tipe\": \"FORK POSITIONER\", \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": \"17\", \"ban_id\": \"3\", \"roda_id\": \"4\", \"valve_id\": \"2\", \"aksesoris\": [], \"fabrikasi_attachment_id\": \"3\", \"prepared_units\": [{\"unit_id\": \"11\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"3\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"CAMERA AI\\\",\\\"SPEED LIMITER\\\",\\\"LASER FORK\\\",\\\"HORN KLASON\\\",\\\"APAR 3 KG\\\"]\", \"mekanik\": \"123\", \"catatan\": \"1\", \"timestamp\": \"2025-09-13 03:43:24\"}], \"migrated_persiapan_unit_id\": 11, \"migrated_persiapan_unit_mekanik\": \"JAJA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-13\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-13\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-13 03:35:25\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"CAMERA AI\\\",\\\"SPEED LIMITER\\\",\\\"LASER FORK\\\",\\\"HORN KLASON\\\",\\\"APAR 3 KG\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"123\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-13\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-13\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-13 03:43:16\", \"migrated_painting_mekanik\": \"123\", \"migrated_painting_estimasi_mulai\": \"2025-09-13\", \"migrated_painting_estimasi_selesai\": \"2025-09-13\", \"migrated_painting_tanggal_approve\": \"2025-09-13 03:43:21\", \"migrated_pdi_mekanik\": \"123\", \"migrated_pdi_estimasi_mulai\": \"2025-09-13\", \"migrated_pdi_estimasi_selesai\": \"2025-09-13\", \"migrated_pdi_tanggal_approve\": \"2025-09-13 03:43:24\", \"migrated_pdi_catatan\": \"1\"}','COMPLETED',NULL,1,'2025-09-13 03:35:03','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(41,'SPK/202509/009','UNIT',NULL,NULL,1,'test/1/1/6','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530','2025-09-15','{\"departemen_id\": \"3\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"HAND PALLET\", \"merk_unit\": \"LINDE\", \"model_unit\": null, \"kapasitas_id\": \"41\", \"attachment_tipe\": \"FORK POSITIONER\", \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": \"15\", \"ban_id\": \"6\", \"roda_id\": \"1\", \"valve_id\": \"3\", \"aksesoris\": [], \"fabrikasi_attachment_id\": \"16\", \"prepared_units\": [{\"unit_id\": \"13\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\"]\", \"mekanik\": \"JOHANA - DEPI\", \"catatan\": \"a\", \"timestamp\": \"2025-09-15 08:22:53\"}], \"migrated_persiapan_unit_id\": 13, \"migrated_persiapan_unit_mekanik\": \"JAJA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-15\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-15\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-15 08:09:51\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"ARIZAL-EKA\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-15\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-15\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-15 08:21:59\", \"migrated_painting_mekanik\": \"123\", \"migrated_painting_estimasi_mulai\": \"2025-09-15\", \"migrated_painting_estimasi_selesai\": \"2025-09-15\", \"migrated_painting_tanggal_approve\": \"2025-09-15 08:22:44\", \"migrated_pdi_mekanik\": \"JOHANA - DEPI\", \"migrated_pdi_estimasi_mulai\": \"2025-09-15\", \"migrated_pdi_estimasi_selesai\": \"2025-09-15\", \"migrated_pdi_tanggal_approve\": \"2025-09-15 08:22:53\", \"migrated_pdi_catatan\": \"a\"}','READY',NULL,1,'2025-09-15 08:09:31','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(42,'SPK/202509/010','UNIT',57,41,1,'test/1/1/5','Sarana Mitra Luas','Adit','082134555233','Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530',NULL,'{\"departemen_id\": \"3\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"COUNTER BALANCE\", \"merk_unit\": \"HELI\", \"model_unit\": null, \"kapasitas_id\": \"40\", \"attachment_tipe\": \"FORK POSITIONER\", \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": \"16\", \"ban_id\": \"3\", \"roda_id\": \"1\", \"valve_id\": \"3\", \"aksesoris\": [], \"fabrikasi_attachment_id\": \"4\", \"prepared_units\": [{\"unit_id\": \"13\", \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"4\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\", \"mekanik\": \"IYAN\", \"catatan\": \"a\", \"timestamp\": \"2025-09-16 06:57:54\"}], \"migrated_persiapan_unit_id\": 13, \"migrated_persiapan_unit_mekanik\": \"ARIZAL-EKA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-16\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-16\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-16 06:57:35\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"IYAN\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-16\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-16\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-16 06:57:44\", \"migrated_painting_mekanik\": \"IYAN\", \"migrated_painting_estimasi_mulai\": \"2025-09-16\", \"migrated_painting_estimasi_selesai\": \"2025-09-16\", \"migrated_painting_tanggal_approve\": \"2025-09-16 06:57:50\", \"migrated_pdi_mekanik\": \"IYAN\", \"migrated_pdi_estimasi_mulai\": \"2025-09-16\", \"migrated_pdi_estimasi_selesai\": \"2025-09-16\", \"migrated_pdi_tanggal_approve\": \"2025-09-16 06:57:54\", \"migrated_pdi_catatan\": \"a\"}','COMPLETED',NULL,1,'2025-09-16 06:56:53','2025-09-30 06:37:04',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(49,'SPK/202509/011','ATTACHMENT',56,44,1,'TEST/AUTO/001','Test Client',NULL,NULL,NULL,'2025-09-16','{\"departemen_id\": \"1\", \"tipe_unit_id\": null, \"tipe_jenis\": null, \"merk_unit\": null, \"model_unit\": null, \"kapasitas_id\": null, \"attachment_tipe\": \"SIDE SHIFTER\", \"attachment_merk\": \"\", \"jenis_baterai\": \"\", \"charger_id\": \"0\", \"mast_id\": null, \"ban_id\": null, \"roda_id\": null, \"valve_id\": null, \"aksesoris\": [], \"fabrikasi_attachment_id\": \"16\", \"prepared_units\": [{\"unit_id\": null, \"battery_inventory_id\": null, \"charger_inventory_id\": null, \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": null, \"mekanik\": \"IYAN\", \"catatan\": \"a\", \"timestamp\": \"2025-09-16 09:10:41\"}], \"migrated_persiapan_unit_id\": null, \"migrated_persiapan_unit_mekanik\": null, \"migrated_persiapan_unit_estimasi_mulai\": null, \"migrated_persiapan_unit_estimasi_selesai\": null, \"migrated_persiapan_unit_tanggal_approve\": null, \"migrated_persiapan_aksesoris_tersedia\": null, \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"IYAN\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-16\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-16\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-16 09:10:32\", \"migrated_painting_mekanik\": \"IYAN\", \"migrated_painting_estimasi_mulai\": \"2025-09-16\", \"migrated_painting_estimasi_selesai\": \"2025-09-16\", \"migrated_painting_tanggal_approve\": \"2025-09-16 09:10:35\", \"migrated_pdi_mekanik\": \"IYAN\", \"migrated_pdi_estimasi_mulai\": \"2025-09-16\", \"migrated_pdi_estimasi_selesai\": \"2025-09-16\", \"migrated_pdi_tanggal_approve\": \"2025-09-16 09:10:41\", \"migrated_pdi_catatan\": \"a\"}','IN_PROGRESS',NULL,1,'2025-09-16 08:43:46','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(50,'SPK/202509/012','UNIT',63,45,1,'KNTRK/2209/0002','Sarana Mitra Luas','Januari','092131231231','Gudang Perawang','2025-09-26','{\"departemen_id\": \"1\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"COUNTER BALANCE\", \"merk_unit\": \"JUNGHEINRICH\", \"model_unit\": null, \"kapasitas_id\": \"37\", \"attachment_tipe\": \"FORK\", \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": \"14\", \"ban_id\": \"6\", \"roda_id\": \"3\", \"valve_id\": \"1\", \"aksesoris\": [], \"persiapan_battery_action\": \"keep_existing\", \"persiapan_battery_id\": \"3\", \"persiapan_charger_action\": \"keep_existing\", \"persiapan_charger_id\": \"5\", \"fabrikasi_attachment_id\": \"16\", \"prepared_units\": [{\"unit_id\": \"17\", \"battery_inventory_id\": \"3\", \"charger_inventory_id\": \"5\", \"attachment_inventory_id\": \"16\", \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"ROTARY LAMP\\\"]\", \"mekanik\": \"ARIZAL-EKA\", \"catatan\": \"oke\", \"timestamp\": \"2025-09-26 07:57:16\"}], \"migrated_persiapan_unit_id\": 17, \"migrated_persiapan_unit_mekanik\": \"ARIZAL-EKA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-26\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-26\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-26 07:56:50\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"ROTARY LAMP\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"ARIZAL-EKA\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-26\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-26\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-26 07:57:04\", \"migrated_painting_mekanik\": \"ARIZAL-EKA\", \"migrated_painting_estimasi_mulai\": \"2025-09-26\", \"migrated_painting_estimasi_selesai\": \"2025-09-26\", \"migrated_painting_tanggal_approve\": \"2025-09-26 07:57:09\", \"migrated_pdi_mekanik\": \"ARIZAL-EKA\", \"migrated_pdi_estimasi_mulai\": \"2025-09-26\", \"migrated_pdi_estimasi_selesai\": \"2025-09-26\", \"migrated_pdi_tanggal_approve\": \"2025-09-26 07:57:16\", \"migrated_pdi_catatan\": \"oke\"}','COMPLETED',NULL,1,'2025-09-26 07:49:57','2025-09-28 01:31:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(53,'SPK/202509/013','UNIT',64,46,1,'LG-9812310','PT LG Indonesia','Adit','082134555233','Head Office','2025-09-27','{\"departemen_id\": \"2\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"REACH TRUCK\", \"merk_unit\": \"KOMATSU\", \"model_unit\": null, \"kapasitas_id\": \"42\", \"attachment_tipe\": \"FORKLIFT SCALE\", \"attachment_merk\": null, \"jenis_baterai\": \"Lithium-ion\", \"charger_id\": \"9\", \"mast_id\": \"15\", \"ban_id\": \"3\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"persiapan_battery_action\": \"keep_existing\", \"persiapan_battery_id\": \"3\", \"persiapan_charger_action\": \"keep_existing\", \"persiapan_charger_id\": \"5\", \"persiapan_attachment_action\": \"keep_existing\", \"persiapan_attachment_id\": \"3\", \"migrated_persiapan_unit_id\": 16, \"migrated_persiapan_unit_mekanik\": \"ARIZAL-EKA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-27\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-27\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-27 16:25:24\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": \"ARIZAL-EKA\", \"migrated_fabrikasi_estimasi_mulai\": \"2025-09-27\", \"migrated_fabrikasi_estimasi_selesai\": \"2025-09-27\", \"migrated_fabrikasi_tanggal_approve\": \"2025-09-27 16:48:26\", \"migrated_painting_mekanik\": null, \"migrated_painting_estimasi_mulai\": null, \"migrated_painting_estimasi_selesai\": null, \"migrated_painting_tanggal_approve\": null, \"migrated_pdi_mekanik\": null, \"migrated_pdi_estimasi_mulai\": null, \"migrated_pdi_estimasi_selesai\": null, \"migrated_pdi_tanggal_approve\": null, \"migrated_pdi_catatan\": null}','COMPLETED',NULL,1,'2025-09-27 16:24:51','2025-09-29 03:57:54',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(54,'SPK/202509/014','UNIT',64,47,4,'LG-9812310','PT LG Indonesia','Joko','+6282138812312','Head Office','2025-09-28','{\"departemen_id\": \"1\", \"tipe_unit_id\": \"6\", \"tipe_jenis\": \"PALLET STACKER\", \"merk_unit\": \"LINDE\", \"model_unit\": null, \"kapasitas_id\": \"42\", \"attachment_tipe\": \"FORK\", \"attachment_merk\": null, \"jenis_baterai\": null, \"charger_id\": null, \"mast_id\": \"6\", \"ban_id\": \"4\", \"roda_id\": \"1\", \"valve_id\": \"2\", \"aksesoris\": [], \"persiapan_battery_action\": \"keep_existing\", \"persiapan_battery_id\": \"6\", \"persiapan_charger_action\": \"assign\", \"persiapan_charger_id\": \"33\", \"prepared_units\": [{\"unit_id\": \"8\", \"battery_inventory_id\": \"6\", \"charger_inventory_id\": \"32\", \"attachment_inventory_id\": null, \"aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BACK BUZZER\\\",\\\"HORN KLASON\\\",\\\"P3K\\\"]\", \"mekanik\": \"ARIZAL-EKA\", \"catatan\": \"ok\", \"timestamp\": \"2025-09-27 17:02:53\"}], \"migrated_persiapan_unit_id\": 14, \"migrated_persiapan_unit_mekanik\": \"ARIZAL-EKA\", \"migrated_persiapan_unit_estimasi_mulai\": \"2025-09-27\", \"migrated_persiapan_unit_estimasi_selesai\": \"2025-09-27\", \"migrated_persiapan_unit_tanggal_approve\": \"2025-09-27 17:03:12\", \"migrated_persiapan_aksesoris_tersedia\": \"[\\\"LAMPU UTAMA\\\",\\\"BACK BUZZER\\\",\\\"HORN KLASON\\\"]\", \"migrated_fabrikasi_attachment_id\": null, \"migrated_fabrikasi_mekanik\": null, \"migrated_fabrikasi_estimasi_mulai\": null, \"migrated_fabrikasi_estimasi_selesai\": null, \"migrated_fabrikasi_tanggal_approve\": null, \"migrated_painting_mekanik\": null, \"migrated_painting_estimasi_mulai\": null, \"migrated_painting_estimasi_selesai\": null, \"migrated_painting_tanggal_approve\": null, \"migrated_pdi_mekanik\": null, \"migrated_pdi_estimasi_mulai\": null, \"migrated_pdi_estimasi_selesai\": null, \"migrated_pdi_tanggal_approve\": null, \"migrated_pdi_catatan\": \"ok\"}','COMPLETED',NULL,1,'2025-09-27 17:01:04','2025-09-30 05:12:52',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(56,'SPK/202509/015','UNIT',64,46,1,'LG-9812310','PT LG Indonesia','12','1231','Head Office',NULL,'{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"REACH TRUCK\",\"merk_unit\":\"KOMATSU\",\"model_unit\":null,\"kapasitas_id\":\"42\",\"attachment_tipe\":\"FORKLIFT SCALE\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"9\",\"mast_id\":\"15\",\"ban_id\":\"3\",\"roda_id\":\"1\",\"valve_id\":\"2\",\"aksesoris\":[]}','COMPLETED',NULL,1,'2025-09-30 06:37:43','2025-11-21 13:21:53',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(57,'SPK/202510/001','UNIT',64,46,1,'LG-9812310','PT LG Indonesia','Joko','+6282138812312','Head Office','2025-10-06','{\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"REACH TRUCK\",\"merk_unit\":\"KOMATSU\",\"model_unit\":null,\"kapasitas_id\":\"42\",\"attachment_tipe\":\"FORKLIFT SCALE\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"9\",\"mast_id\":\"15\",\"ban_id\":\"3\",\"roda_id\":\"1\",\"valve_id\":\"2\",\"aksesoris\":[]}','IN_PROGRESS',NULL,1,'2025-10-06 03:48:55','2025-10-06 03:50:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(58,'SPK/202510/002','ATTACHMENT',63,45,1,'KNTRK/2209/0002','Sarana Mitra Luas','wad','wadaw','Gudang Perawang',NULL,'{\"departemen_id\":\"1\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"COUNTER BALANCE\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"37\",\"attachment_tipe\":\"FORK\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"3\",\"valve_id\":\"1\",\"aksesoris\":[]}','IN_PROGRESS',NULL,1,'2025-10-13 04:37:30','2025-10-21 07:13:13',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(66,'SPK/202510/003','UNIT',63,49,1,'KNTRK/2209/0002','Sarana Mitra Luas','Januari','092131231231','Gudang Perawang','2025-10-17','{\"unit_id\":\"1\"}','READY',NULL,1,'2025-10-17 09:25:08','2025-10-21 15:37:59',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(67,'SPK/202510/004','UNIT',63,52,1,'KNTRK/2209/0002','Test','Test','123','Test','2025-10-25','{\"departemen_id\":\"2\",\"tipe_unit_id\":\"7\",\"tipe_jenis\":null,\"merk_unit\":\"HELI\",\"model_unit\":null,\"kapasitas_id\":\"14\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"8\",\"mast_id\":\"14\",\"ban_id\":\"2\",\"roda_id\":\"3\",\"valve_id\":\"2\",\"aksesoris\":[]}','SUBMITTED','Test',1,'2025-10-23 04:42:39','2025-10-23 11:42:39',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(68,'SPK/202510/005','UNIT',57,48,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-23','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"4\",\"tipe_jenis\":\"SCRUBER\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"38\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"4\",\"valve_id\":\"1\",\"aksesoris\":[]}','SUBMITTED','awdawdwa',1,'2025-10-23 04:45:39','2025-10-23 11:45:39',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(69,'SPK/202510/006','UNIT',57,48,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-23','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"4\",\"tipe_jenis\":\"SCRUBER\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"38\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"4\",\"valve_id\":\"1\",\"aksesoris\":[]}','SUBMITTED',NULL,1,'2025-10-23 06:31:05','2025-10-23 13:31:05',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(70,'SPK/202510/007','ATTACHMENT',57,51,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-25','{\"departemen_id\":\"2\",\"tipe_unit_id\":null,\"tipe_jenis\":null,\"merk_unit\":null,\"model_unit\":null,\"kapasitas_id\":null,\"attachment_tipe\":\"FORKLIFT SCALE\",\"attachment_merk\":\"asd\",\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":null,\"ban_id\":null,\"roda_id\":null,\"valve_id\":null,\"aksesoris\":[],\"target_unit_id\":\"6\",\"target_unit_sn\":\"test3\",\"target_unit_info\":{\"tipe\":\"N\\/A\",\"merk\":\"N\\/A\",\"model\":\"N\\/A\"},\"replacement_reason\":\"Penggantian attachment\"}','SUBMITTED','Test',1,'2025-10-23 06:34:07','2025-10-23 13:34:07',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(71,'SPK/202510/008','ATTACHMENT',57,51,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-23','{\"departemen_id\":\"2\",\"tipe_unit_id\":null,\"tipe_jenis\":null,\"merk_unit\":null,\"model_unit\":null,\"kapasitas_id\":null,\"attachment_tipe\":\"FORKLIFT SCALE\",\"attachment_merk\":\"asd\",\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":null,\"ban_id\":null,\"roda_id\":null,\"valve_id\":null,\"aksesoris\":[],\"target_unit_id\":\"6\",\"target_unit_sn\":\"test3\",\"target_unit_info\":{\"tipe\":\"N\\/A\",\"merk\":\"N\\/A\",\"model\":\"N\\/A\"},\"replacement_reason\":\"awdaw\"}','IN_PROGRESS','awdwad',1,'2025-10-23 06:36:38','2025-10-23 09:48:47',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(72,'SPK/202510/009','UNIT',57,48,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-24','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"4\",\"tipe_jenis\":\"SCRUBER\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"38\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"4\",\"valve_id\":\"1\",\"aksesoris\":[]}','SUBMITTED','awda',1,'2025-10-24 08:33:36','2025-10-24 15:33:36',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(73,'SPK/202510/010','UNIT',57,48,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-24','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"4\",\"tipe_jenis\":\"SCRUBER\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"38\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"4\",\"valve_id\":\"1\",\"aksesoris\":[]}','SUBMITTED','awdcccccccccccccccccccccccc',1,'2025-10-24 08:43:33','2025-10-24 15:43:33',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(74,'SPK/202510/011','UNIT',57,48,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-24','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"4\",\"tipe_jenis\":\"SCRUBER\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"38\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"4\",\"valve_id\":\"1\",\"aksesoris\":[]}','SUBMITTED',NULL,1,'2025-10-24 09:29:25','2025-10-24 16:29:25',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(75,'SPK/202510/012','UNIT',57,48,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-24','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"4\",\"tipe_jenis\":\"SCRUBER\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"38\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"4\",\"valve_id\":\"1\",\"aksesoris\":[]}','SUBMITTED','awda',1,'2025-10-24 09:35:54','2025-10-24 16:35:54',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0),(76,'SPK/202510/013','UNIT',57,48,1,'test/1/1/5','Test','itsmils','082136033596','Lokasi Utama','2025-10-24','{\"departemen_id\":\"1\",\"tipe_unit_id\":\"4\",\"tipe_jenis\":\"SCRUBER\",\"merk_unit\":\"JUNGHEINRICH\",\"model_unit\":null,\"kapasitas_id\":\"38\",\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":null,\"charger_id\":null,\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"4\",\"valve_id\":\"1\",\"aksesoris\":[]}','SUBMITTED',NULL,1,'2025-10-24 09:41:11','2025-10-24 16:41:11',NULL,NULL,1,NULL,NULL,NULL,1,NULL,0);
/*!40000 ALTER TABLE `spk` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_backup_20250903`
--

DROP TABLE IF EXISTS `spk_backup_20250903`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk_backup_20250903` (
  `id` int unsigned NOT NULL DEFAULT '0',
  `nomor_spk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT','TUKAR') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'UNIT',
  `kontrak_id` int unsigned DEFAULT NULL,
  `kontrak_spesifikasi_id` int unsigned DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
  `jumlah_unit` int DEFAULT '1' COMMENT 'Jumlah unit dalam SPK ini',
  `po_kontrak_nomor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `kontak` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `delivery_plan` date DEFAULT NULL,
  `spesifikasi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `status` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'SUBMITTED',
  `persiapan_unit_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `persiapan_unit_estimasi_mulai` date DEFAULT NULL,
  `persiapan_unit_estimasi_selesai` date DEFAULT NULL,
  `persiapan_unit_tanggal_approve` datetime DEFAULT NULL,
  `persiapan_unit_id` int DEFAULT NULL,
  `persiapan_aksesoris_tersedia` text COLLATE utf8mb4_general_ci,
  `fabrikasi_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fabrikasi_estimasi_mulai` date DEFAULT NULL,
  `fabrikasi_estimasi_selesai` date DEFAULT NULL,
  `fabrikasi_tanggal_approve` datetime DEFAULT NULL,
  `fabrikasi_attachment_id` int DEFAULT NULL,
  `painting_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `painting_estimasi_mulai` date DEFAULT NULL,
  `painting_estimasi_selesai` date DEFAULT NULL,
  `painting_tanggal_approve` datetime DEFAULT NULL,
  `pdi_mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pdi_estimasi_mulai` date DEFAULT NULL,
  `pdi_estimasi_selesai` date DEFAULT NULL,
  `pdi_tanggal_approve` datetime DEFAULT NULL,
  `pdi_catatan` text COLLATE utf8mb4_general_ci,
  `catatan` text COLLATE utf8mb4_general_ci,
  `dibuat_oleh` int unsigned DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk_component_transactions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `spk_id` int unsigned NOT NULL,
  `transaction_type` enum('ASSIGN','UNASSIGN','MODIFY') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ASSIGN',
  `component_type` enum('UNIT','ATTACHMENT','BATTERY','CHARGER') COLLATE utf8mb4_general_ci NOT NULL,
  `component_id` int unsigned NOT NULL COMMENT 'ID from respective table (inventory_unit, inventory_attachment)',
  `inventory_id` int unsigned DEFAULT NULL COMMENT 'ID from inventory_attachment if applicable',
  `mekanik` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `created_by` int unsigned DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
-- Table structure for table `spk_edit_permissions`
--

DROP TABLE IF EXISTS `spk_edit_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk_edit_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `spk_id` int NOT NULL,
  `stage` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `can_edit` tinyint(1) DEFAULT '1',
  `edit_reason` text COLLATE utf8mb4_general_ci,
  `restricted_until` timestamp NULL DEFAULT NULL,
  `created_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_spk_stage` (`spk_id`,`stage`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_edit_permissions`
--

LOCK TABLES `spk_edit_permissions` WRITE;
/*!40000 ALTER TABLE `spk_edit_permissions` DISABLE KEYS */;
INSERT INTO `spk_edit_permissions` VALUES (1,51,'persiapan_unit',1,'Allow unit change before fabrikasi',NULL,NULL,'2025-09-27 15:01:44'),(2,51,'fabrikasi',1,'Allow component change before painting',NULL,NULL,'2025-09-27 15:01:44');
/*!40000 ALTER TABLE `spk_edit_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_rollback_log`
--

DROP TABLE IF EXISTS `spk_rollback_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk_rollback_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `spk_id` int NOT NULL,
  `stage` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `action` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `reason` text COLLATE utf8mb4_general_ci,
  `rolled_back_by` int DEFAULT NULL,
  `rolled_back_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_spk_rollback` (`spk_id`,`stage`),
  KEY `idx_rollback_date` (`rolled_back_at`),
  CONSTRAINT `spk_rollback_log_chk_1` CHECK (json_valid(`old_data`)),
  CONSTRAINT `spk_rollback_log_chk_2` CHECK (json_valid(`new_data`))
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_rollback_log`
--

LOCK TABLES `spk_rollback_log` WRITE;
/*!40000 ALTER TABLE `spk_rollback_log` DISABLE KEYS */;
INSERT INTO `spk_rollback_log` VALUES (1,52,'status_rollback','MARKETING_ROLLBACK','{\"status\":\"READY\"}','{\"status\":\"IN_PROGRESS\"}','Marketing rollback from READY to IN_PROGRESS',1,'2025-09-27 15:25:37'),(2,52,'status_rollback','MARKETING_ROLLBACK','{\"status\":\"READY\"}','{\"status\":\"IN_PROGRESS\"}','Marketing rollback from READY to IN_PROGRESS - Status only, approval stages preserved',1,'2025-09-27 15:37:50');
/*!40000 ALTER TABLE `spk_rollback_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_status_history`
--

DROP TABLE IF EXISTS `spk_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk_status_history` (
  `id` int unsigned NOT NULL,
  `spk_id` int unsigned NOT NULL,
  `status_from` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_to` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci NOT NULL,
  `changed_by` int unsigned DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_status_history`
--

LOCK TABLES `spk_status_history` WRITE;
/*!40000 ALTER TABLE `spk_status_history` DISABLE KEYS */;
INSERT INTO `spk_status_history` VALUES (0,27,'READY','IN_PROGRESS',1,'DI created: DI/202509/001','2025-09-04 10:28:19'),(22,22,'READY','IN_PROGRESS',1,'DI created: DI/202508/007','2025-08-27 15:24:37'),(23,23,'READY','IN_PROGRESS',1,'DI created: DI/202508/008','2025-08-27 15:26:16'),(24,54,'COMPLETED','READY',1,'Manual correction: Only 2 of 4 units delivered','2025-09-30 11:13:14');
/*!40000 ALTER TABLE `spk_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_unit_stages`
--

DROP TABLE IF EXISTS `spk_unit_stages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk_unit_stages` (
  `id` int NOT NULL AUTO_INCREMENT,
  `spk_id` int NOT NULL,
  `unit_index` int NOT NULL,
  `stage_name` enum('persiapan_unit','fabrikasi','painting','pdi') COLLATE utf8mb4_general_ci NOT NULL,
  `mekanik` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `estimasi_mulai` datetime DEFAULT NULL,
  `estimasi_selesai` datetime DEFAULT NULL,
  `tanggal_approve` datetime DEFAULT NULL,
  `unit_id` int DEFAULT NULL,
  `area_id` int DEFAULT NULL,
  `aksesoris_tersedia` text COLLATE utf8mb4_general_ci,
  `battery_inventory_attachment_id` int DEFAULT NULL,
  `charger_inventory_attachment_id` int DEFAULT NULL,
  `attachment_inventory_attachment_id` int DEFAULT NULL,
  `no_unit_action` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `update_no_unit` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_stage_per_unit` (`spk_id`,`unit_index`,`stage_name`),
  KEY `idx_spk_unit` (`spk_id`,`unit_index`),
  KEY `idx_stage` (`stage_name`),
  KEY `idx_tanggal_approve` (`tanggal_approve`)
) ENGINE=InnoDB AUTO_INCREMENT=83 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_unit_stages`
--

LOCK TABLES `spk_unit_stages` WRITE;
/*!40000 ALTER TABLE `spk_unit_stages` DISABLE KEYS */;
INSERT INTO `spk_unit_stages` VALUES (1,54,1,'persiapan_unit','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 03:15:06',8,30,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"HORN KLASON\",\"P3K\"]',NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-27 18:36:09','2025-09-29 03:15:06'),(2,54,1,'fabrikasi','Test Edit','2025-01-30 00:00:00','2025-01-31 00:00:00','2025-09-29 02:48:43',8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-27 18:36:09','2025-09-30 05:07:22'),(3,54,1,'painting','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 02:40:03',8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-29 02:40:03','2025-09-30 05:07:24'),(4,54,1,'pdi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 02:40:15',8,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'asda','2025-09-29 02:40:15','2025-09-30 05:07:25'),(5,54,2,'persiapan_unit','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 03:19:35',14,30,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"HORN KLASON\",\"P3K\"]',NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-29 03:19:35','2025-09-29 03:19:35'),(6,53,1,'persiapan_unit','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 03:20:32',13,17,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]',9,12,NULL,NULL,NULL,'GOOD','2025-09-29 03:20:32','2025-09-29 06:38:16'),(13,53,1,'fabrikasi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 03:46:19',NULL,NULL,NULL,NULL,NULL,3,NULL,NULL,'AJA','2025-09-29 03:30:54','2025-09-29 06:38:19'),(14,53,1,'painting','Test','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 03:50:40',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ADAS','2025-09-29 03:46:25','2025-09-29 06:38:21'),(15,53,1,'pdi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 03:46:31',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ok','2025-09-29 03:46:31','2025-09-29 03:46:31'),(18,55,1,'persiapan_unit','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 07:55:13',8,19,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"HORN KLASON\",\"P3K\"]',21,10,NULL,NULL,NULL,NULL,'2025-09-29 07:46:10','2025-09-29 07:55:13'),(19,55,2,'persiapan_unit','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 07:55:36',11,8,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"HORN KLASON\",\"P3K\"]',NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-29 07:55:36','2025-09-29 07:55:36'),(21,55,1,'fabrikasi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 07:57:36',NULL,NULL,NULL,NULL,NULL,15,NULL,NULL,NULL,'2025-09-29 07:57:36','2025-09-29 07:57:36'),(26,55,2,'fabrikasi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 09:34:25',NULL,NULL,NULL,NULL,NULL,4,NULL,NULL,NULL,'2025-09-29 09:33:43','2025-09-29 09:34:25'),(29,55,1,'painting','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 09:39:11',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-29 09:39:11','2025-09-29 09:39:11'),(30,55,2,'painting','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 09:39:20',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-29 09:39:20','2025-09-29 09:39:20'),(31,55,1,'pdi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 09:39:37',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ok','2025-09-29 09:39:37','2025-09-29 09:39:37'),(32,55,2,'pdi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-29 09:39:48',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ok','2025-09-29 09:39:48','2025-09-29 09:39:48'),(33,54,3,'persiapan_unit','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 03:56:27',16,18,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"HORN KLASON\",\"P3K\"]',29,14,NULL,NULL,NULL,NULL,'2025-09-30 03:56:27','2025-09-30 03:56:27'),(34,54,4,'persiapan_unit','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 03:56:48',13,22,'[\"LAMPU UTAMA\",\"BACK BUZZER\",\"HORN KLASON\",\"P3K\"]',NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-30 03:56:48','2025-09-30 03:56:48'),(35,54,2,'fabrikasi','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 04:00:13',14,NULL,NULL,NULL,NULL,15,NULL,NULL,NULL,'2025-09-30 04:00:14','2025-09-30 05:07:37'),(36,54,3,'fabrikasi','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 04:00:25',16,NULL,NULL,NULL,NULL,4,NULL,NULL,NULL,'2025-09-30 04:00:25','2025-09-30 05:07:07'),(37,54,4,'fabrikasi','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 04:01:17',13,NULL,NULL,NULL,NULL,16,NULL,NULL,NULL,'2025-09-30 04:01:17','2025-09-30 05:07:00'),(38,54,2,'painting','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 04:01:24',14,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-30 04:01:24','2025-09-30 05:07:40'),(39,54,3,'painting','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 04:01:28',16,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-30 04:01:28','2025-09-30 05:07:11'),(40,54,4,'painting','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 04:01:31',13,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-30 04:01:31','2025-09-30 05:07:02'),(41,54,2,'pdi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-30 04:01:35',14,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ok','2025-09-30 04:01:35','2025-09-30 05:07:50'),(42,54,3,'pdi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-30 04:01:41',16,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ok','2025-09-30 04:01:41','2025-09-30 05:08:04'),(43,54,4,'pdi','ARIZAL-EKA','2025-09-29 00:00:00','2025-09-29 00:00:00','2025-09-30 04:01:46',13,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'ok','2025-09-30 04:01:46','2025-09-30 05:08:07'),(48,56,1,'persiapan_unit','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 07:25:32',15,25,'[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]',28,32,NULL,'AUTO_GENERATE','true',NULL,'2025-09-30 07:05:31','2025-09-30 07:25:32'),(78,56,1,'fabrikasi','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 10:17:10',15,NULL,NULL,NULL,NULL,4,NULL,NULL,NULL,'2025-09-30 10:17:10','2025-09-30 10:17:10'),(79,56,1,'painting','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 10:17:46',15,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-09-30 10:17:46','2025-09-30 10:17:46'),(80,56,1,'pdi','ARIZAL-EKA','2025-09-30 00:00:00','2025-09-30 00:00:00','2025-09-30 10:17:52',15,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'asd','2025-09-30 10:17:52','2025-09-30 10:17:52'),(81,66,1,'persiapan_unit','IYAN','2025-10-21 00:00:00','2025-10-21 00:00:00','2025-10-21 08:29:19',10,21,'[\"LAMPU UTAMA\",\"BLUE SPOT\",\"RED LINE\",\"WORK LIGHT\",\"CAMERA AI\",\"CAMERA\"]',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-21 08:29:19','2025-10-21 08:29:19'),(82,27,1,'persiapan_unit','IYAN','2025-10-21 00:00:00','2025-10-21 00:00:00','2025-10-21 08:38:59',5,21,'[]',NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-21 08:38:59','2025-10-21 08:38:59');
/*!40000 ALTER TABLE `spk_unit_stages` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `spk_units`
--

DROP TABLE IF EXISTS `spk_units`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `spk_units` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `spk_id` int unsigned NOT NULL,
  `unit_id` int unsigned DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_spk_units_unit` (`unit_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `spk_units`
--

LOCK TABLES `spk_units` WRITE;
/*!40000 ALTER TABLE `spk_units` DISABLE KEYS */;
INSERT INTO `spk_units` VALUES (4,51,16,'Manual test insert');
/*!40000 ALTER TABLE `spk_units` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_spk_units_unit_preparation` AFTER INSERT ON `spk_units` FOR EACH ROW BEGIN
    
    IF NEW.unit_id IS NOT NULL THEN
        CALL update_unit_status(
            NEW.unit_id, 
            4, 
            CONCAT('Unit assigned to SPK #', NEW.spk_id),
            'SPK_SERVICE',
            NEW.spk_id,
            'SYSTEM'
        );
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `status_attachment`
--

DROP TABLE IF EXISTS `status_attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_attachment` (
  `id_status_attachment` int NOT NULL AUTO_INCREMENT,
  `nama_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_eksekusi_workflow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `kode` varchar(30) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `urutan` int NOT NULL,
  `warna` varchar(7) COLLATE utf8mb4_general_ci DEFAULT '#6c757d',
  `aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `status_unit` (
  `id_status` int NOT NULL,
  `status_unit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status_unit`
--

LOCK TABLES `status_unit` WRITE;
/*!40000 ALTER TABLE `status_unit` DISABLE KEYS */;
INSERT INTO `status_unit` VALUES (1,'AVAILABLE_STOCK'),(2,'STOCK_NON_ASET'),(3,'BOOKED'),(4,'IN_PREPARATION'),(5,'READY_TO_DELIVER'),(6,'IN_DELIVERY'),(7,'RENTAL_ACTIVE'),(8,'MAINTENANCE'),(9,'RETURNED'),(10,'SOLD'),(11,'RENTAL_INACTIVE');
/*!40000 ALTER TABLE `status_unit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_contacts`
--

DROP TABLE IF EXISTS `supplier_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_contacts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `position` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_primary` (`is_primary`),
  CONSTRAINT `fk_supplier_contacts_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id_supplier`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_contacts`
--

LOCK TABLES `supplier_contacts` WRITE;
/*!40000 ALTER TABLE `supplier_contacts` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_contacts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_documents`
--

DROP TABLE IF EXISTS `supplier_documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_documents` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `document_type` enum('NPWP','SIUP','TDP','Contract','Certificate','PKS','Other') COLLATE utf8mb4_general_ci NOT NULL,
  `document_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `file_size` int DEFAULT NULL COMMENT 'in bytes',
  `expiry_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `uploaded_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_expiry` (`expiry_date`),
  KEY `idx_type` (`document_type`),
  CONSTRAINT `fk_supplier_documents_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id_supplier`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_documents`
--

LOCK TABLES `supplier_documents` WRITE;
/*!40000 ALTER TABLE `supplier_documents` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `supplier_performance_log`
--

DROP TABLE IF EXISTS `supplier_performance_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `supplier_performance_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `supplier_id` int NOT NULL,
  `po_id` int NOT NULL,
  `delivery_date_promised` date DEFAULT NULL,
  `delivery_date_actual` date DEFAULT NULL,
  `delivery_status` enum('On Time','Late','Early') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `days_difference` int DEFAULT NULL COMMENT 'Positive=late, Negative=early',
  `quality_rating` decimal(3,2) DEFAULT NULL COMMENT '1-5 scale',
  `service_rating` decimal(3,2) DEFAULT NULL COMMENT '1-5 scale',
  `price_competitiveness` decimal(3,2) DEFAULT NULL COMMENT '1-5 scale',
  `overall_rating` decimal(3,2) DEFAULT NULL COMMENT 'Auto-calculated average',
  `issues` text COLLATE utf8mb4_general_ci,
  `feedback` text COLLATE utf8mb4_general_ci,
  `rated_by` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_supplier` (`supplier_id`),
  KEY `idx_po` (`po_id`),
  KEY `idx_delivery_status` (`delivery_status`),
  CONSTRAINT `fk_supplier_performance_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id_supplier`) ON DELETE CASCADE,
  CONSTRAINT `supplier_performance_log_ibfk_2` FOREIGN KEY (`po_id`) REFERENCES `purchase_orders` (`id_po`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `supplier_performance_log`
--

LOCK TABLES `supplier_performance_log` WRITE;
/*!40000 ALTER TABLE `supplier_performance_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `supplier_performance_log` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_supplier_rating_after_insert` AFTER INSERT ON `supplier_performance_log` FOR EACH ROW BEGIN
    UPDATE suppliers_enhanced SET
        rating = (
            SELECT AVG(overall_rating)
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
            AND overall_rating IS NOT NULL
        ),
        total_orders = (
            SELECT COUNT(DISTINCT po_id)
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
        ),
        on_time_delivery_rate = (
            SELECT 
                (COUNT(CASE WHEN delivery_status = 'On Time' THEN 1 END) * 100.0) / COUNT(*)
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
            AND delivery_status IS NOT NULL
        ),
        quality_score = (
            SELECT AVG(quality_rating) * 20
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
            AND quality_rating IS NOT NULL
        )
    WHERE id_supplier = NEW.supplier_id;
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `update_supplier_rating_after_update` AFTER UPDATE ON `supplier_performance_log` FOR EACH ROW BEGIN
    UPDATE suppliers_enhanced SET
        rating = (
            SELECT AVG(overall_rating)
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
            AND overall_rating IS NOT NULL
        ),
        total_orders = (
            SELECT COUNT(DISTINCT po_id)
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
        ),
        on_time_delivery_rate = (
            SELECT 
                (COUNT(CASE WHEN delivery_status = 'On Time' THEN 1 END) * 100.0) / COUNT(*)
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
            AND delivery_status IS NOT NULL
        ),
        quality_score = (
            SELECT AVG(quality_rating) * 20
            FROM supplier_performance_log
            WHERE supplier_id = NEW.supplier_id
            AND quality_rating IS NOT NULL
        )
    WHERE id_supplier = NEW.supplier_id;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers` (
  `id_supplier` int NOT NULL AUTO_INCREMENT,
  `kode_supplier` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'SUP-001, SUP-002',
  `nama_supplier` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `alias` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nama pendek/alias',
  `contact_person` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `phone` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `website` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_general_ci,
  `city` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `province` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `postal_code` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `country` varchar(100) COLLATE utf8mb4_general_ci DEFAULT 'Indonesia',
  `npwp` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Tax ID',
  `business_type` enum('Distributor','Manufacturer','Wholesaler','Retailer','Other') COLLATE utf8mb4_general_ci DEFAULT 'Distributor',
  `payment_terms` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'NET 30, NET 60, COD, etc',
  `credit_limit` decimal(15,2) DEFAULT '0.00',
  `currency` varchar(10) COLLATE utf8mb4_general_ci DEFAULT 'IDR',
  `product_categories` text COLLATE utf8mb4_general_ci COMMENT 'JSON: ["Unit", "Attachment", "Battery", "Sparepart"]',
  `rating` decimal(3,2) DEFAULT '0.00' COMMENT '0.00 - 5.00',
  `total_orders` int DEFAULT '0',
  `total_value` decimal(15,2) DEFAULT '0.00',
  `on_time_delivery_rate` decimal(5,2) DEFAULT '0.00' COMMENT 'Percentage',
  `quality_score` decimal(5,2) DEFAULT '0.00' COMMENT 'Percentage',
  `bank_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_account_number` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `bank_account_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('Active','Inactive','Blacklisted') COLLATE utf8mb4_general_ci DEFAULT 'Active',
  `is_verified` tinyint(1) DEFAULT '0',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
  `updated_by` int DEFAULT NULL,
  PRIMARY KEY (`id_supplier`),
  UNIQUE KEY `kode_supplier` (`kode_supplier`),
  KEY `idx_status` (`status`),
  KEY `idx_kode` (`kode_supplier`),
  KEY `idx_rating` (`rating`),
  KEY `idx_nama` (`nama_supplier`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers`
--

LOCK TABLES `suppliers` WRITE;
/*!40000 ALTER TABLE `suppliers` DISABLE KEYS */;
INSERT INTO `suppliers` VALUES (1,'SUP-0001','PT. Forklift Jaya Abadi',NULL,'Bapak Budi','081234567890',NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-07-15 20:43:59','2025-10-09 10:04:13',NULL,NULL),(2,'SUP-0002','CV. Sinar Baterai',NULL,'Ibu Susan','081122334455',NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-07-15 20:43:59','2025-10-09 10:04:13',NULL,NULL),(3,'SUP-0003','Toko Sparepart Maju',NULL,'Pak Eko','021-555-1234',NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-07-15 20:43:59','2025-10-09 10:04:13',NULL,NULL),(4,'SUP-2025-001','JASAJASA',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-10-12 20:32:06','2025-10-13 04:07:16',NULL,NULL),(11,'SUP-2025-003','JASARAHAJRAadasdawdaw',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-10-12 21:12:56','2025-10-13 04:15:52',NULL,NULL),(15,'SUP-2025-005','KUYKUY',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-10-12 21:18:37','2025-10-13 04:20:40',NULL,NULL),(16,'SUP-2025-006','KUYKUY11111',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-10-12 21:22:38','2025-10-13 04:24:03',NULL,NULL),(17,'SUP-2025-008','ajayuuKKAWDHAW',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-10-12 21:25:55','2025-10-13 04:30:06',NULL,NULL),(20,'','123123123123',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Indonesia',NULL,'Distributor',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-10-12 21:31:16','2025-10-12 21:31:16',NULL,NULL),(27,'SUP-2025-009','Test Supplier 2',NULL,'Test Person','081234567891','test2@example.com',NULL,'Test Address',NULL,NULL,NULL,'Indonesia',NULL,'Manufacturer',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,NULL,'2025-10-12 21:52:53','2025-10-12 21:52:53',NULL,NULL),(28,'SUP-2025-010','SUKSES MAJU sejahtera',NULL,'Aries Adityanto','082136033596','itsupport@sml.co.id','','awd',NULL,NULL,NULL,'Indonesia',NULL,'Manufacturer',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Active',0,'awd','2025-10-12 21:53:41','2025-10-12 21:53:41',NULL,NULL),(29,'SUP-2025-011','SUKSES MAJU sejahtera jaya',NULL,'Aries Adityanto','082136033596','itsupport@sml.co.id','','EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT',NULL,NULL,NULL,'Indonesia',NULL,'Manufacturer',NULL,0.00,'IDR',NULL,0.00,0,0.00,0.00,0.00,NULL,NULL,NULL,'Inactive',0,NULL,'2025-10-12 21:55:07','2025-10-12 23:17:38',NULL,NULL);
/*!40000 ALTER TABLE `suppliers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `suppliers_backup_old`
--

DROP TABLE IF EXISTS `suppliers_backup_old`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `suppliers_backup_old` (
  `id_supplier` int NOT NULL,
  `nama_supplier` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `kontak_person` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `suppliers_backup_old`
--

LOCK TABLES `suppliers_backup_old` WRITE;
/*!40000 ALTER TABLE `suppliers_backup_old` DISABLE KEYS */;
INSERT INTO `suppliers_backup_old` VALUES (1,'PT. Forklift Jaya Abadi','Bapak Budi','081234567890',NULL,'2025-07-15 20:43:59',NULL),(2,'CV. Sinar Baterai','Ibu Susan','081122334455',NULL,'2025-07-15 20:43:59',NULL),(3,'Toko Sparepart Maju','Pak Eko','021-555-1234',NULL,'2025-07-15 20:43:59',NULL);
/*!40000 ALTER TABLE `suppliers_backup_old` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_activity_log`
--

DROP TABLE IF EXISTS `system_activity_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_activity_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `table_name` varchar(64) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
  `record_id` int unsigned NOT NULL COMMENT 'ID of the affected record',
  `action_type` enum('CREATE','READ','UPDATE','DELETE','EXPORT','IMPORT','LOGIN','LOGOUT','APPROVE','REJECT','SUBMIT','CANCEL','ASSIGN','UNASSIGN','COMPLETE','PRINT','DOWNLOAD') COLLATE utf8mb4_general_ci NOT NULL,
  `action_description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Brief description of what happened',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Previous values (only changed fields)',
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'New values (only changed fields)',
  `affected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'List of fields that were changed',
  `user_id` int unsigned DEFAULT NULL COMMENT 'FK to users.id',
  `workflow_stage` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Current business stage',
  `is_critical` tinyint(1) DEFAULT '0' COMMENT 'Mark critical business actions',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Application module where activity occurred',
  `submenu_item` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Specific submenu item accessed',
  `business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL') COLLATE utf8mb4_general_ci DEFAULT 'LOW' COMMENT 'Business impact level',
  `related_entities` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'JSON object storing related entity relationships',
  PRIMARY KEY (`id`),
  KEY `idx_related_entities` (`related_entities`(255)),
  CONSTRAINT `system_activity_log_chk_1` CHECK (json_valid(`old_values`)),
  CONSTRAINT `system_activity_log_chk_2` CHECK (json_valid(`new_values`)),
  CONSTRAINT `system_activity_log_chk_3` CHECK (json_valid(`affected_fields`)),
  CONSTRAINT `system_activity_log_chk_4` CHECK (json_valid(`related_entities`))
) ENGINE=InnoDB AUTO_INCREMENT=547 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `system_activity_log`
--

LOCK TABLES `system_activity_log` WRITE;
/*!40000 ALTER TABLE `system_activity_log` DISABLE KEYS */;
INSERT INTO `system_activity_log` VALUES (1,'kontrak',44,'CREATE','Kontrak baru dibuat dengan nomor PO-CL-0488',NULL,'{\"no_po_marketing\": \"PO-CL-0488\", \"pelanggan\": \"PT Client\", \"status\": \"ACTIVE\"}','[\"no_po_marketing\", \"pelanggan\", \"status\"]',1,'KONTRAK',1,'2025-09-08 06:43:05',NULL,NULL,'LOW',NULL),(2,'inventory_unit',1,'ASSIGN','Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan',NULL,'{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}','[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]',1,'KONTRAK',1,'2025-09-08 06:43:05',NULL,NULL,'LOW',NULL),(3,'inventory_unit',2,'ASSIGN','Unit forklift diassign ke kontrak dengan harga Rp 9,000,000/bulan',NULL,'{\"kontrak_id\": 44, \"harga_sewa_bulanan\": 9000000, \"status_unit_id\": 3}','[\"kontrak_id\", \"harga_sewa_bulanan\", \"status_unit_id\"]',1,'KONTRAK',1,'2025-09-08 06:43:05',NULL,NULL,'LOW',NULL),(7,'kontrak',48,'DELETE','Test delete logging manual',NULL,NULL,NULL,1,NULL,0,'2025-09-08 10:08:42',NULL,NULL,'LOW',NULL),(8,'kontrak',49,'DELETE','Test Delete','{}',NULL,'[]',1,NULL,0,'2025-09-09 04:14:05',NULL,NULL,'LOW',NULL),(9,'kontrak',48,'DELETE','Kontrak deleted: TEST-DELETE-LOG (Client: Test Client for Delete)','{\"id\":\"48\",\"no_kontrak\":\"TEST-DELETE-LOG\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Delete\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-08\",\"tanggal_berakhir\":\"2025-12-08\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 17:06:22\",\"diperbarui_pada\":\"2025-09-08 17:06:22\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,NULL,1,'2025-09-09 04:16:39','MARKETING',NULL,'LOW',NULL),(10,'kontrak',49,'DELETE','Kontrak deleted: TEST-DELETE-LOG-2 (Client: Test Client for Delete 2)','{\"id\":\"49\",\"no_kontrak\":\"TEST-DELETE-LOG-2\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Delete 2\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-08\",\"tanggal_berakhir\":\"2025-12-08\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 17:09:00\",\"diperbarui_pada\":\"2025-09-08 17:09:00\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,NULL,1,'2025-09-09 04:18:47','MARKETING',NULL,'LOW',NULL),(11,'kontrak',52,'DELETE','Kontrak deleted: TEST-COMPLETE-LOG (Client: Test Complete Logging)','{\"id\":\"52\",\"no_kontrak\":\"TEST-COMPLETE-LOG\",\"no_po_marketing\":null,\"pelanggan\":\"Test Complete Logging\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-09\",\"tanggal_berakhir\":\"2025-12-09\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 04:27:07\",\"diperbarui_pada\":\"2025-09-09 04:27:07\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 04:28:11','MARKETING',NULL,'HIGH',NULL),(12,'kontrak',123,'DELETE','Test delete with JSON relations',NULL,NULL,NULL,1,'DELETE_CONFIRMED',0,'2025-09-09 04:51:45','MARKETING','Data Kontrak','HIGH','{\"kontrak\": [123], \"spk\": [456, 789], \"di\": [101112]}'),(13,'kontrak',999,'CREATE','Test kontrak dengan JSON relations implementasi',NULL,NULL,NULL,1,'DRAFT',0,'2025-09-09 04:52:49','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\": [999], \"spk\": [1001, 1002], \"test_entity\": [555]}'),(15,'kontrak',51,'DELETE','Kontrak deleted: TEST-ALERT-SYSTEM (Client: Test Client for Alert System)','{\"id\":\"51\",\"no_kontrak\":\"TEST-ALERT-SYSTEM\",\"no_po_marketing\":null,\"pelanggan\":\"Test Client for Alert System\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":null,\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-09\",\"tanggal_berakhir\":\"2025-12-09\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 11:19:04\",\"diperbarui_pada\":\"2025-09-09 11:19:04\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 06:28:14','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[51]}'),(16,'kontrak',46,'DELETE','Kontrak deleted: TEST-1757315452 (Client: Test Client)','{\"id\":\"46\",\"no_kontrak\":\"TEST-1757315452\",\"no_po_marketing\":\"PO-TEST-1757315452\",\"pelanggan\":\"Test Client\",\"lokasi\":null,\"pic\":null,\"kontak\":null,\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2024-01-01\",\"tanggal_berakhir\":\"2024-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 07:10:56\",\"diperbarui_pada\":\"2025-09-08 07:10:56\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 06:28:29','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[46]}'),(17,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-09 07:28:45','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(18,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-09 07:29:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(19,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-09 08:00:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(20,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-09 08:00:33','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(21,'kontrak',53,'DELETE','Kontrak deleted: KNTRK/2209/0002 (Client: IBR)','{\"id\":\"53\",\"no_kontrak\":\"KNTRK\\/2209\\/0002\",\"no_po_marketing\":\"PO-ADIT110999\",\"pelanggan\":\"IBR\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-09 06:30:00\",\"diperbarui_pada\":\"2025-09-09 06:30:00\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 09:42:59','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[53]}'),(22,'kontrak',45,'DELETE','Kontrak deleted: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"id\":\"45\",\"no_kontrak\":\"KNTRK\\/2209\\/0001\",\"no_po_marketing\":\"PO-ADIT10999\",\"pelanggan\":\"Sarana Mitra Luas\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-09-30\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-08 06:57:54\",\"diperbarui_pada\":\"2025-09-08 06:57:54\"}',NULL,'[\"id\",\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"lokasi\",\"pic\",\"kontak\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-09 09:43:20','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[45]}'),(23,'kontrak',54,'CREATE','Kontrak created: KNTRK/2209/0001 (Client: Sarana Mitra Luas)',NULL,'{\"no_kontrak\":\"KNTRK\\/2209\\/0001\",\"no_po_marketing\":\"PO-ADIT10999\",\"pelanggan\":\"Sarana Mitra Luas\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-09 09:54:01','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54]}'),(24,'spk',29,'CREATE','Created new spk record',NULL,'{\"spk_id\":29,\"nomor_spk\":\"SPK\\/202509\\/003\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":2}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-09 10:03:41','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[29]}'),(25,'spk',29,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-09 10:03:48','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(26,'spk',29,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:03:48\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-09\",\"persiapan_unit_estimasi_selesai\":\"2025-09-09\",\"persiapan_unit_tanggal_approve\":\"2025-09-09 10:04:10\",\"diperbarui_pada\":\"2025-09-09 10:04:10\",\"persiapan_unit_id\":\"12\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\"}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:04:10','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(27,'spk',29,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:04:10\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\"}\"}','{\"fabrikasi_mekanik\":\"JOHANA - DEPI\",\"fabrikasi_estimasi_mulai\":\"2025-09-09\",\"fabrikasi_estimasi_selesai\":\"2025-09-09\",\"fabrikasi_tanggal_approve\":\"2025-09-09 10:04:19\",\"diperbarui_pada\":\"2025-09-09 10:04:19\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:04:19','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(28,'spk',29,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:04:19\"}','{\"painting_mekanik\":\"JOHANA - DEPI\",\"painting_estimasi_mulai\":\"2025-09-09\",\"painting_estimasi_selesai\":\"2025-09-09\",\"painting_tanggal_approve\":\"2025-09-09 10:04:27\",\"diperbarui_pada\":\"2025-09-09 10:04:27\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-09 10:04:27','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(29,'spk',29,'UPDATE','Updated spk record','{\"diperbarui_pada\":\"2025-09-09 10:04:27\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"persiapan_unit_id\":\"12\",\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-09\",\"persiapan_unit_estimasi_selesai\":\"2025-09-09\",\"persiapan_unit_tanggal_approve\":\"2025-09-09 10:04:10\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"fabrikasi_mekanik\":\"JOHANA - DEPI\",\"fabrikasi_estimasi_mulai\":\"2025-09-09\",\"fabrikasi_estimasi_selesai\":\"2025-09-09\",\"fabrikasi_tanggal_approve\":\"2025-09-09 10:04:19\",\"painting_mekanik\":\"JOHANA - DEPI\",\"painting_estimasi_mulai\":\"2025-09-09\",\"painting_estimasi_selesai\":\"2025-09-09\",\"painting_tanggal_approve\":\"2025-09-09 10:04:27\"}','{\"diperbarui_pada\":\"2025-09-09 10:04:37\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\",\"pdi_catatan\":\"ok\",\"persiapan_unit_id\":null,\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"persiapan_aksesoris_tersedia\":null,\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null}','[\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"persiapan_unit_id\",\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"persiapan_aksesoris_tersedia\",\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\"]',1,'UPDATED',0,'2025-09-09 10:04:37','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(30,'spk',29,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:04:37\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','{\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-09\",\"persiapan_unit_estimasi_selesai\":\"2025-09-09\",\"persiapan_unit_tanggal_approve\":\"2025-09-09 10:06:03\",\"diperbarui_pada\":\"2025-09-09 10:06:03\",\"persiapan_unit_id\":\"12\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:06:03','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(31,'spk',29,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:06:03\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','{\"fabrikasi_mekanik\":\"JOHANA - DEPI\",\"fabrikasi_estimasi_mulai\":\"2025-09-09\",\"fabrikasi_estimasi_selesai\":\"2025-09-09\",\"fabrikasi_tanggal_approve\":\"2025-09-09 10:06:09\",\"diperbarui_pada\":\"2025-09-09 10:06:09\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-09 10:06:09','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(32,'spk',29,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:06:09\"}','{\"painting_mekanik\":\"JOHANA - DEPI\",\"painting_estimasi_mulai\":\"2025-09-09\",\"painting_estimasi_selesai\":\"2025-09-09\",\"painting_tanggal_approve\":\"2025-09-09 10:06:13\",\"diperbarui_pada\":\"2025-09-09 10:06:13\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-09 10:06:13','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(33,'spk',29,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-09 10:06:13\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"}]}\",\"pdi_catatan\":\"ok\",\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"IYAN\",\"pdi_estimasi_mulai\":\"2025-09-09\",\"pdi_estimasi_selesai\":\"2025-09-09\",\"pdi_tanggal_approve\":\"2025-09-09 10:06:17\",\"diperbarui_pada\":\"2025-09-09 10:06:17\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"12\\\",\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-09 10:04:37\\\"},{\\\"unit_id\\\":\\\"12\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"12\\\",\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"123\\\",\\\"timestamp\\\":\\\"2025-09-09 10:06:17\\\"}]}\",\"pdi_catatan\":\"123\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-09 10:06:17','SERVICE','Service Management','MEDIUM','{\"spk\":[29]}'),(34,'delivery_instruction',124,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":124,\"nomor_di\":\"DI\\/202509\\/003\",\"spk_id\":29,\"po_kontrak_nomor\":\"KNTRK\\/2209\\/0001\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[12]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-09 10:07:55','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[124]}'),(35,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-10 01:24:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(36,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-10 02:25:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(37,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-10 02:25:26','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(38,'spk',29,'UPDATE','Updated SPK workflow','{\"status\":\"IN_PROGRESS\"}','{\"status\":\"READY\",\"pdi_mekanik\":\"IYAN\",\"pdi_tanggal_approve\":\"2025-09-10 09:00:00\"}',NULL,1,NULL,0,'2025-09-10 02:27:18','SERVICE',NULL,'MEDIUM',NULL),(39,'kontrak_spesifikasi',1,'CREATE','Created kontrak spesifikasi',NULL,'{\"spek_kode\":\"SPEK001\",\"kontrak_id\":1,\"jumlah_dibutuhkan\":2,\"harga_per_unit_bulanan\":5000000}',NULL,1,NULL,0,'2025-09-10 02:36:58','MARKETING',NULL,'MEDIUM',NULL),(40,'kontrak_spesifikasi',38,'DELETE','Menghapus spesifikasi SPEC-002','{\"id\":\"38\",\"kontrak_id\":\"54\",\"spek_kode\":\"SPEC-002\",\"jumlah_dibutuhkan\":\"1\",\"jumlah_tersedia\":\"0\",\"harga_per_unit_bulanan\":\"10000000.00\",\"harga_per_unit_harian\":null,\"catatan_spek\":\"\",\"departemen_id\":\"2\",\"tipe_unit_id\":\"6\",\"tipe_jenis\":\"COUNTER BALANCE\",\"kapasitas_id\":\"41\",\"merk_unit\":\"KOMATSU\",\"model_unit\":null,\"attachment_tipe\":\"FORK POSITIONER\",\"attachment_merk\":null,\"jenis_baterai\":\"Lithium-ion\",\"charger_id\":\"15\",\"mast_id\":\"14\",\"ban_id\":\"6\",\"roda_id\":\"1\",\"valve_id\":\"2\",\"aksesoris\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"dibuat_pada\":\"2025-09-10 02:26:21\",\"diperbarui_pada\":\"2025-09-10 02:26:21\"}',NULL,'[\"id\",\"kontrak_id\",\"spek_kode\",\"jumlah_dibutuhkan\",\"jumlah_tersedia\",\"harga_per_unit_bulanan\",\"harga_per_unit_harian\",\"catatan_spek\",\"departemen_id\",\"tipe_unit_id\",\"tipe_jenis\",\"kapasitas_id\",\"merk_unit\",\"model_unit\",\"attachment_tipe\",\"attachment_merk\",\"jenis_baterai\",\"charger_id\",\"mast_id\",\"ban_id\",\"roda_id\",\"valve_id\",\"aksesoris\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'SPECIFICATION_DELETED',1,'2025-09-10 02:49:33','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[54],\"kontrak_spesifikasi\":[38]}'),(41,'kontrak',44,'UPDATE','Kontrak updated: MSI (Client: MSI)','{\"pic\":\"MSI\",\"catatan\":null}','{\"pic\":\"Adit\",\"catatan\":\"\"}','[\"pic\",\"catatan\"]',1,'UPDATED',0,'2025-09-10 03:07:51','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[44]}'),(42,'kontrak',44,'UPDATE','Kontrak updated: KNTRK/2208/0001 (Client: Sarana Mitra Luas)','{\"no_kontrak\":\"MSI\",\"no_po_marketing\":\"MSI\",\"pelanggan\":\"MSI\",\"catatan\":null}','{\"no_kontrak\":\"KNTRK\\/2208\\/0001\",\"no_po_marketing\":\"PO-ADIT10998\",\"pelanggan\":\"Sarana Mitra Luas\",\"catatan\":\"\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"catatan\"]',1,'UPDATED',0,'2025-09-10 03:08:34','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[44]}'),(43,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"JOKO\",\"no_hp_supir\":\"082138848123\",\"no_sim_supir\":\"1231012\",\"kendaraan\":\"KOKASD\",\"no_polisi_kendaraan\":\"123123\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-10 03:35:03','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(44,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-10 04:18:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(45,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-10 04:18:23','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(46,'spk',30,'CREATE','Created new spk record',NULL,'{\"spk_id\":30,\"nomor_spk\":\"SPK\\/202509\\/004\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-10 08:19:25','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[30]}'),(47,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 02:03:59','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(48,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-11 02:07:28','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(49,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 02:08:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(50,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 02:10:27','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(51,'spk',32,'CREATE','Created new spk record',NULL,'{\"spk_id\":32,\"nomor_spk\":\"SPK\\/202509\\/872\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-11 04:55:30','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[32]}'),(52,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-11 07:53:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(53,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-12 01:21:56','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(54,'spk',35,'CREATE','Created new spk record',NULL,'{\"spk_id\":35,\"nomor_spk\":\"SPK\\/202509\\/906\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-12 03:52:36','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[35]}'),(55,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-10 10:35:03\",\"tanggal_kirim\":\"2025-09-09\",\"estimasi_sampai\":null,\"nama_supir\":\"JOKO\",\"no_hp_supir\":\"082138848123\",\"no_sim_supir\":\"1231012\",\"kendaraan\":\"KOKASD\",\"no_polisi_kendaraan\":\"123123\",\"catatan\":null,\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:24:06\",\"tanggal_kirim\":\"2025-09-12\",\"estimasi_sampai\":\"2025-09-12\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"catatan\":\"DIKIRIM\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"tanggal_kirim\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"catatan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:24:06','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(56,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:24:06\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:24:10\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:24:10','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(57,'delivery_instruction',124,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:24:10\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:24:16\",\"catatan_sampai\":\"sudah sampai\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:24:16','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[124]}'),(58,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-12 06:29:53','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(59,'kontrak',55,'CREATE','Kontrak created: SML/DS/121025 (Client: LG)',NULL,'{\"no_kontrak\":\"SML\\/DS\\/121025\",\"no_po_marketing\":\"PO-LG998123\",\"pelanggan\":\"LG\",\"pic\":\"ANDI\",\"kontak\":\"08213564778\",\"lokasi\":\"Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5\\/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-30\",\"tanggal_berakhir\":\"2025-10-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-12 06:34:46','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[55]}'),(60,'spk',36,'CREATE','Created new spk record',NULL,'{\"spk_id\":36,\"nomor_spk\":\"SPK\\/202509\\/004\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"55\",\"kontrak_spesifikasi_id\":\"39\",\"jumlah_unit\":2}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-12 06:35:58','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[36]}'),(61,'spk',36,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-12 06:36:09','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(62,'spk',36,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:36:09\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 06:36:44\",\"diperbarui_pada\":\"2025-09-12 06:36:44\",\"persiapan_unit_id\":\"4\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-12 06:36:44','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(63,'spk',36,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:36:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"BADRUN\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 06:37:00\",\"diperbarui_pada\":\"2025-09-12 06:37:00\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-12 06:37:00','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(64,'spk',36,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:37:00\"}','{\"painting_mekanik\":\"UDUD\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 06:37:09\",\"diperbarui_pada\":\"2025-09-12 06:37:09\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-12 06:37:09','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(65,'spk',36,'UPDATE','Updated spk record','{\"diperbarui_pada\":\"2025-09-12 06:37:09\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\",\"pdi_catatan\":null,\"persiapan_unit_id\":\"4\",\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 06:36:44\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\",\"fabrikasi_mekanik\":\"BADRUN\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 06:37:00\",\"painting_mekanik\":\"UDUD\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 06:37:09\"}','{\"diperbarui_pada\":\"2025-09-12 06:37:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\",\"pdi_catatan\":\"OK\",\"persiapan_unit_id\":null,\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"persiapan_aksesoris_tersedia\":null,\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null}','[\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"persiapan_unit_id\",\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"persiapan_aksesoris_tersedia\",\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\"]',1,'UPDATED',0,'2025-09-12 06:37:44','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(66,'spk',36,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:37:44\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"IYAN\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 06:38:03\",\"diperbarui_pada\":\"2025-09-12 06:38:03\",\"persiapan_unit_id\":\"10\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-12 06:38:03','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(67,'spk',36,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:38:03\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\"}','{\"fabrikasi_mekanik\":\"BADRUN\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 06:38:16\",\"diperbarui_pada\":\"2025-09-12 06:38:16\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-12 06:38:16','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(68,'spk',36,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:38:16\"}','{\"painting_mekanik\":\"INDRA\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 06:38:24\",\"diperbarui_pada\":\"2025-09-12 06:38:24\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-12 06:38:24','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(69,'spk',36,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 06:38:24\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"}]}\",\"pdi_catatan\":\"OK\",\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"UDUD\",\"pdi_estimasi_mulai\":\"2025-09-12\",\"pdi_estimasi_selesai\":\"2025-09-12\",\"pdi_tanggal_approve\":\"2025-09-12 06:38:33\",\"diperbarui_pada\":\"2025-09-12 06:38:33\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"THREE WHEEL\\\",\\\"merk_unit\\\":\\\"HANGCHA\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"14\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"2\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"4\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"INDRA\\\",\\\"catatan\\\":\\\"OK\\\",\\\"timestamp\\\":\\\"2025-09-12 06:37:44\\\"},{\\\"unit_id\\\":\\\"10\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"3\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"UDUD\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-12 06:38:33\\\"}]}\",\"pdi_catatan\":\"ok\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-12 06:38:33','SERVICE','Service Management','MEDIUM','{\"spk\":[36]}'),(70,'delivery_instruction',125,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":125,\"nomor_di\":\"DI\\/202509\\/004\",\"spk_id\":36,\"po_kontrak_nomor\":\"SML\\/DS\\/121025\",\"pelanggan\":\"LG\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[4,10]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-12 06:51:13','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[125]}'),(71,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-12 06:51:23','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(72,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:51:23\",\"estimasi_sampai\":null,\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:52:18\",\"estimasi_sampai\":\"2025-09-12\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:52:18','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(73,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:52:18\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:52:26\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:52:26','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(74,'delivery_instruction',125,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 13:52:26\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 06:52:30\",\"catatan_sampai\":\"ok\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 06:52:30','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[125]}'),(75,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"no_po_marketing\":\"PO-ADIT10999\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":\"39552000.00\",\"total_units\":\"2\"}','{\"no_po_marketing\":null,\"pic\":null,\"kontak\":null,\"lokasi\":null,\"nilai_total\":0,\"total_units\":0}','[\"no_po_marketing\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\"]',NULL,'UPDATED',0,'2025-09-12 09:28:23','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54]}'),(76,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Aktif\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Pending\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:28:41','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(77,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Pending\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:29:01','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(78,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Aktif\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Berakhir\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:30:05','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(79,'kontrak',54,'UPDATE','Kontrak updated: KNTRK/2209/0001 (Client: Sarana Mitra Luas)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Berakhir\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:31:11','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[54],\"spk\":[29]}'),(80,'kontrak',55,'UPDATE','Kontrak updated: SML/DS/121025 (Client: Test)','{\"no_po_marketing\":\"PO-LG998123\",\"pelanggan\":\"LG\",\"pic\":\"ANDI\",\"kontak\":\"08213564778\",\"lokasi\":\"Gandaria 8 Office Tower Lv. 29 BC & 31 ABCD, Jalan Sultan Iskandar Muda, Kebayoran Lama, RT.5\\/RW.3, Senayan, Jakarta Selatan, Daerah Khusus Ibukota Jakarta, 12190\",\"nilai_total\":\"24000000.00\",\"total_units\":\"2\",\"tanggal_mulai\":\"2025-09-30\",\"tanggal_berakhir\":\"2025-10-31\",\"status\":\"Aktif\"}','{\"no_po_marketing\":null,\"pelanggan\":\"Test\",\"pic\":null,\"kontak\":null,\"lokasi\":null,\"nilai_total\":0,\"total_units\":0,\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\"}','[\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:49:13','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[55],\"spk\":[36]}'),(81,'kontrak',55,'UPDATE','Kontrak updated: SML/DS/121025 (Client: Test)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Pending\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-12 09:49:35','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[55],\"spk\":[36]}'),(82,'kontrak',56,'CREATE','Kontrak created: TEST/AUTO/001 (Client: Test Auto Update)',NULL,'{\"no_kontrak\":\"TEST\\/AUTO\\/001\",\"no_po_marketing\":null,\"pelanggan\":\"Test Auto Update\",\"pic\":null,\"kontak\":null,\"lokasi\":null,\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-01\",\"tanggal_berakhir\":\"2025-12-31\",\"status\":\"Pending\",\"dibuat_oleh\":1}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',NULL,'DRAFT',0,'2025-09-12 09:54:47','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[56]}'),(88,'spk',37,'CREATE','Created new spk record',NULL,'{\"spk_id\":37,\"nomor_spk\":\"SPK\\/202509\\/005\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"40\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-12 10:05:22','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[37]}'),(89,'spk',37,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-12 10:05:28','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(90,'spk',37,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:28\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"123\",\"persiapan_unit_estimasi_mulai\":\"2025-09-12\",\"persiapan_unit_estimasi_selesai\":\"2025-09-12\",\"persiapan_unit_tanggal_approve\":\"2025-09-12 10:05:41\",\"diperbarui_pada\":\"2025-09-12 10:05:41\",\"persiapan_unit_id\":\"7\",\"persiapan_aksesoris_tersedia\":\"[]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-12 10:05:41','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(91,'spk',37,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:41\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-12\",\"fabrikasi_estimasi_selesai\":\"2025-09-12\",\"fabrikasi_tanggal_approve\":\"2025-09-12 10:05:52\",\"diperbarui_pada\":\"2025-09-12 10:05:52\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-12 10:05:52','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(92,'spk',37,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:52\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-12\",\"painting_estimasi_selesai\":\"2025-09-12\",\"painting_tanggal_approve\":\"2025-09-12 10:05:59\",\"diperbarui_pada\":\"2025-09-12 10:05:59\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-12 10:05:59','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(93,'spk',37,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 10:05:59\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-12\",\"pdi_estimasi_selesai\":\"2025-09-12\",\"pdi_tanggal_approve\":\"2025-09-12 10:06:06\",\"diperbarui_pada\":\"2025-09-12 10:06:06\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":null,\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"7\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"123\\\",\\\"timestamp\\\":\\\"2025-09-12 10:06:06\\\"}]}\",\"pdi_catatan\":\"123\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-12 10:06:06','SERVICE','Service Management','MEDIUM','{\"spk\":[37]}'),(94,'delivery_instruction',126,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":126,\"nomor_di\":\"DI\\/202509\\/005\",\"spk_id\":37,\"po_kontrak_nomor\":\"TEST\\/AUTO\\/001\",\"pelanggan\":\"Test Auto Update\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[7]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-12 10:06:23','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[126]}'),(95,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-12 10:06:32','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(96,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 17:06:32\",\"estimasi_sampai\":null,\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 10:06:43\",\"estimasi_sampai\":\"2025-09-12\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 10:06:43','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(97,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 17:06:43\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 10:06:45\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 10:06:45','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(98,'delivery_instruction',126,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-12 17:06:45\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-12\",\"diperbarui_pada\":\"2025-09-12 10:06:48\",\"catatan_sampai\":\"123\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-12 10:06:48','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[126]}'),(99,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-13 01:06:29','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(100,'kontrak',56,'UPDATE','Kontrak updated: TEST/AUTO/001 (Client: Test Client)','{\"pelanggan\":\"Test Auto Update\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Aktif\"}','{\"pelanggan\":\"Test Client\",\"nilai_total\":0,\"total_units\":0,\"status\":\"Pending\"}','[\"pelanggan\",\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-13 01:20:06','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[56],\"spk\":[37]}'),(101,'kontrak',56,'UPDATE','Kontrak updated: TEST/AUTO/001 (Client: Test Client)','{\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"status\":\"Pending\"}','{\"nilai_total\":0,\"total_units\":0,\"status\":\"Aktif\"}','[\"nilai_total\",\"total_units\",\"status\"]',NULL,'UPDATED',0,'2025-09-13 01:20:35','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[56],\"spk\":[37]}'),(102,'spk',38,'CREATE','Created new spk record',NULL,'{\"spk_id\":38,\"nomor_spk\":\"SPK\\/202509\\/006\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"54\",\"kontrak_spesifikasi_id\":\"37\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-13 01:33:11','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[38]}'),(103,'spk',38,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-13 01:33:20','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(104,'spk',38,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:33:20\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 01:46:15\",\"diperbarui_pada\":\"2025-09-13 01:46:15\",\"persiapan_unit_id\":\"5\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 01:46:15','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(105,'spk',38,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:46:15\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 01:46:26\",\"diperbarui_pada\":\"2025-09-13 01:46:26\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 01:46:26','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(106,'spk',38,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:46:26\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 01:46:32\",\"diperbarui_pada\":\"2025-09-13 01:46:32\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 01:46:32','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(107,'spk',38,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 01:46:32\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-13\",\"pdi_estimasi_selesai\":\"2025-09-13\",\"pdi_tanggal_approve\":\"2025-09-13 01:46:42\",\"diperbarui_pada\":\"2025-09-13 01:46:42\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lead Acid\\\",\\\"charger_id\\\":\\\"1\\\",\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"5\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"1\\\",\\\"timestamp\\\":\\\"2025-09-13 01:46:42\\\"}]}\",\"pdi_catatan\":\"1\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-13 01:46:42','SERVICE','Service Management','MEDIUM','{\"spk\":[38]}'),(108,'delivery_instruction',127,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":127,\"nomor_di\":\"DI\\/202509\\/006\",\"spk_id\":38,\"po_kontrak_nomor\":\"KNTRK\\/2209\\/0001\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[5]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-13 01:47:49','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[127]}'),(109,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-13 01:48:00','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(110,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 08:48:00\",\"estimasi_sampai\":null,\"nama_supir\":\"TBD\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"TBD\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 01:48:36\",\"estimasi_sampai\":\"2025-09-13\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 01:48:36','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(111,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 08:48:36\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 01:48:38\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 01:48:38','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(112,'delivery_instruction',127,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 08:48:38\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 01:48:42\",\"catatan_sampai\":\"qwe\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 01:48:42','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[127]}'),(113,'kontrak',57,'CREATE','Kontrak created: test/1/1/5 (Client: Sarana Mitra Luas)',NULL,'{\"no_kontrak\":\"test\\/1\\/1\\/5\",\"no_po_marketing\":\"12345\",\"pelanggan\":\"Sarana Mitra Luas\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-13\",\"tanggal_berakhir\":\"2025-09-13\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-13 02:56:22','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[57]}'),(114,'spk',39,'CREATE','Created new spk record',NULL,'{\"spk_id\":39,\"nomor_spk\":\"SPK\\/202509\\/007\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"41\",\"jumlah_unit\":2}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-13 02:57:15','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[39]}'),(115,'spk',39,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-13 02:57:22','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(116,'spk',39,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:57:22\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 02:57:44\",\"diperbarui_pada\":\"2025-09-13 02:57:44\",\"persiapan_unit_id\":\"6\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"P3K\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 02:57:44','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(117,'spk',39,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:57:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 02:58:00\",\"diperbarui_pada\":\"2025-09-13 02:58:00\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 02:58:00','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(118,'spk',39,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:00\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 02:58:04\",\"diperbarui_pada\":\"2025-09-13 02:58:04\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 02:58:04','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(119,'spk',39,'UPDATE','Updated spk record','{\"diperbarui_pada\":\"2025-09-13 02:58:04\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"persiapan_unit_id\":\"6\",\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 02:57:44\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"P3K\\\"]\",\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 02:58:00\",\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 02:58:04\"}','{\"diperbarui_pada\":\"2025-09-13 02:58:09\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\",\"pdi_catatan\":\"a\",\"persiapan_unit_id\":null,\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"persiapan_aksesoris_tersedia\":null,\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null}','[\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"persiapan_unit_id\",\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"persiapan_aksesoris_tersedia\",\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\"]',1,'UPDATED',0,'2025-09-13 02:58:09','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(120,'spk',39,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:09\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 02:58:20\",\"diperbarui_pada\":\"2025-09-13 02:58:20\",\"persiapan_unit_id\":\"9\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 02:58:20','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(121,'spk',39,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:20\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 02:58:26\",\"diperbarui_pada\":\"2025-09-13 02:58:26\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"15\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 02:58:26','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(122,'spk',39,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:26\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 02:58:29\",\"diperbarui_pada\":\"2025-09-13 02:58:29\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 02:58:29','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(123,'spk',39,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 02:58:29\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"15\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"}]}\",\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-13\",\"pdi_estimasi_selesai\":\"2025-09-13\",\"pdi_tanggal_approve\":\"2025-09-13 02:58:34\",\"diperbarui_pada\":\"2025-09-13 02:58:34\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"15\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"6\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:09\\\"},{\\\"unit_id\\\":\\\"9\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"15\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"ACRYLIC\\\\\\\",\\\\\\\"P3K\\\\\\\",\\\\\\\"SAFETY BELT INTERLOC\\\\\\\",\\\\\\\"SPARS ARRESTOR\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-13 02:58:34\\\"}]}\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"status\"]',1,'UPDATED',0,'2025-09-13 02:58:34','SERVICE','Service Management','MEDIUM','{\"spk\":[39]}'),(124,'delivery_instruction',128,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":128,\"nomor_di\":\"DI\\/202509\\/007\",\"spk_id\":39,\"po_kontrak_nomor\":\"test\\/1\\/1\\/5\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[6,9]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-13 02:58:51','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[128]}'),(125,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-13 02:59:24','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(126,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 09:59:24\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 02:59:33\",\"estimasi_sampai\":\"2025-09-13\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 02:59:33','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(127,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 09:59:33\",\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 02:59:36\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 02:59:36','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(128,'delivery_instruction',128,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 09:59:36\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 02:59:38\",\"catatan_sampai\":\"a\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 02:59:38','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[128]}'),(129,'spk',40,'CREATE','Created new spk record',NULL,'{\"spk_id\":40,\"nomor_spk\":\"SPK\\/202509\\/008\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"42\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-13 03:35:03','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[40]}'),(130,'spk',40,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-13 03:35:11','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(131,'spk',40,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:35:11\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-13\",\"persiapan_unit_estimasi_selesai\":\"2025-09-13\",\"persiapan_unit_tanggal_approve\":\"2025-09-13 03:35:25\",\"diperbarui_pada\":\"2025-09-13 03:35:25\",\"persiapan_unit_id\":\"11\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"CAMERA AI\\\",\\\"SPEED LIMITER\\\",\\\"LASER FORK\\\",\\\"HORN KLASON\\\",\\\"APAR 3 KG\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-13 03:35:25','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(132,'spk',40,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:35:25\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"123\",\"fabrikasi_estimasi_mulai\":\"2025-09-13\",\"fabrikasi_estimasi_selesai\":\"2025-09-13\",\"fabrikasi_tanggal_approve\":\"2025-09-13 03:43:16\",\"diperbarui_pada\":\"2025-09-13 03:43:16\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-13 03:43:16','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(133,'spk',40,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:43:16\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-13\",\"painting_estimasi_selesai\":\"2025-09-13\",\"painting_tanggal_approve\":\"2025-09-13 03:43:21\",\"diperbarui_pada\":\"2025-09-13 03:43:21\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-13 03:43:21','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(134,'spk',40,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 03:43:21\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"123\",\"pdi_estimasi_mulai\":\"2025-09-13\",\"pdi_estimasi_selesai\":\"2025-09-13\",\"pdi_tanggal_approve\":\"2025-09-13 03:43:24\",\"diperbarui_pada\":\"2025-09-13 03:43:24\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"17\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"4\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"11\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"3\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"CAMERA AI\\\\\\\",\\\\\\\"SPEED LIMITER\\\\\\\",\\\\\\\"LASER FORK\\\\\\\",\\\\\\\"HORN KLASON\\\\\\\",\\\\\\\"APAR 3 KG\\\\\\\"]\\\",\\\"mekanik\\\":\\\"123\\\",\\\"catatan\\\":\\\"1\\\",\\\"timestamp\\\":\\\"2025-09-13 03:43:24\\\"}]}\",\"pdi_catatan\":\"1\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-13 03:43:24','SERVICE','Service Management','MEDIUM','{\"spk\":[40]}'),(135,'delivery_instruction',129,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":129,\"nomor_di\":\"DI\\/202509\\/008\",\"spk_id\":40,\"po_kontrak_nomor\":\"test\\/1\\/1\\/5\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[11]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-13 03:43:34','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[129]}'),(136,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-13 03:43:43','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(137,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 10:43:43\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 03:43:52\",\"estimasi_sampai\":\"2025-09-13\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 03:43:52','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(138,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 10:43:52\",\"catatan_berangkat\":null,\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 03:43:55\",\"catatan_berangkat\":\"a\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"catatan_berangkat\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 03:43:55','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(139,'delivery_instruction',129,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-13 10:43:55\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-13\",\"diperbarui_pada\":\"2025-09-13 03:43:58\",\"catatan_sampai\":\"a\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-13 03:43:58','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[129]}'),(140,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-15 07:58:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(141,'kontrak',58,'CREATE','Kontrak created: test/1/1/6 (Client: Sarana Mitra Luas)',NULL,'{\"no_kontrak\":\"test\\/1\\/1\\/6\",\"no_po_marketing\":\"12345\",\"pelanggan\":\"Sarana Mitra Luas\",\"pic\":\"Adit\",\"kontak\":\"082134555233\",\"lokasi\":\"Jl. Gemalapik Raya No.130-111, Pasirsari, Cikarang Sel., Kabupaten Bekasi, Jawa Barat 17530\",\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"HARIAN\",\"tanggal_mulai\":\"2025-09-13\",\"tanggal_berakhir\":\"2025-09-13\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"pelanggan\",\"pic\",\"kontak\",\"lokasi\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-15 08:06:12','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[58]}'),(142,'spk',41,'CREATE','Created new spk record',NULL,'{\"spk_id\":41,\"nomor_spk\":\"SPK\\/202509\\/009\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"58\",\"kontrak_spesifikasi_id\":\"43\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-15 08:09:31','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[41]}'),(143,'spk',41,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-15 08:09:36','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(144,'spk',41,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:09:36\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"JAJA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-15\",\"persiapan_unit_estimasi_selesai\":\"2025-09-15\",\"persiapan_unit_tanggal_approve\":\"2025-09-15 08:09:51\",\"diperbarui_pada\":\"2025-09-15 08:09:51\",\"persiapan_unit_id\":\"13\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-15 08:09:51','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(145,'spk',41,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:09:51\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"ARIZAL-EKA\",\"fabrikasi_estimasi_mulai\":\"2025-09-15\",\"fabrikasi_estimasi_selesai\":\"2025-09-15\",\"fabrikasi_tanggal_approve\":\"2025-09-15 08:21:59\",\"diperbarui_pada\":\"2025-09-15 08:21:59\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-15 08:21:59','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(146,'spk',41,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:21:59\"}','{\"painting_mekanik\":\"123\",\"painting_estimasi_mulai\":\"2025-09-15\",\"painting_estimasi_selesai\":\"2025-09-15\",\"painting_tanggal_approve\":\"2025-09-15 08:22:44\",\"diperbarui_pada\":\"2025-09-15 08:22:44\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-15 08:22:44','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(147,'spk',41,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-15 08:22:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"JOHANA - DEPI\",\"pdi_estimasi_mulai\":\"2025-09-15\",\"pdi_estimasi_selesai\":\"2025-09-15\",\"pdi_tanggal_approve\":\"2025-09-15 08:22:53\",\"diperbarui_pada\":\"2025-09-15 08:22:53\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"HAND PALLET\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"41\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"13\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\"]\\\",\\\"mekanik\\\":\\\"JOHANA - DEPI\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-15 08:22:53\\\"}]}\",\"pdi_catatan\":\"a\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-15 08:22:53','SERVICE','Service Management','MEDIUM','{\"spk\":[41]}'),(148,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-16 01:25:33','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(149,'delivery_instruction',130,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":130,\"nomor_di\":\"DI\\/202509\\/009\",\"spk_id\":null,\"po_kontrak_nomor\":\"TEST\\/AUTO\\/001\",\"pelanggan\":\"Test Client\",\"jenis_perintah_kerja_id\":2,\"tujuan_perintah_kerja_id\":4,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-16 03:10:02','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[130]}'),(150,'spk',42,'CREATE','Created new spk record',NULL,'{\"spk_id\":42,\"nomor_spk\":\"SPK\\/202509\\/010\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"41\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 06:56:53','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[42]}'),(151,'spk',42,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-16 06:57:09','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(152,'spk',42,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:09\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-16\",\"persiapan_unit_estimasi_selesai\":\"2025-09-16\",\"persiapan_unit_tanggal_approve\":\"2025-09-16 06:57:35\",\"diperbarui_pada\":\"2025-09-16 06:57:35\",\"persiapan_unit_id\":\"13\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"CAMERA\\\",\\\"BIO METRIC\\\",\\\"ACRYLIC\\\",\\\"P3K\\\",\\\"SAFETY BELT INTERLOC\\\",\\\"SPARS ARRESTOR\\\"]\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\"]',1,'UPDATED',0,'2025-09-16 06:57:35','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(153,'spk',42,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:35\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"IYAN\",\"fabrikasi_estimasi_mulai\":\"2025-09-16\",\"fabrikasi_estimasi_selesai\":\"2025-09-16\",\"fabrikasi_tanggal_approve\":\"2025-09-16 06:57:44\",\"diperbarui_pada\":\"2025-09-16 06:57:44\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-16 06:57:44','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(154,'spk',42,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:44\"}','{\"painting_mekanik\":\"IYAN\",\"painting_estimasi_mulai\":\"2025-09-16\",\"painting_estimasi_selesai\":\"2025-09-16\",\"painting_tanggal_approve\":\"2025-09-16 06:57:50\",\"diperbarui_pada\":\"2025-09-16 06:57:50\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-16 06:57:50','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(155,'spk',42,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 06:57:50\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"IYAN\",\"pdi_estimasi_mulai\":\"2025-09-16\",\"pdi_estimasi_selesai\":\"2025-09-16\",\"pdi_tanggal_approve\":\"2025-09-16 06:57:54\",\"diperbarui_pada\":\"2025-09-16 06:57:54\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"3\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"HELI\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"40\\\",\\\"attachment_tipe\\\":\\\"FORK POSITIONER\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"16\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"3\\\",\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"4\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"13\\\",\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"4\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"CAMERA\\\\\\\",\\\\\\\"BIO METRIC\\\\\\\",\\\\\\\"ACRYLIC\\\\\\\",\\\\\\\"P3K\\\\\\\",\\\\\\\"SAFETY BELT INTERLOC\\\\\\\",\\\\\\\"SPARS ARRESTOR\\\\\\\"]\\\",\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-16 06:57:54\\\"}]}\",\"pdi_catatan\":\"a\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-16 06:57:54','SERVICE','Service Management','MEDIUM','{\"spk\":[42]}'),(156,'spk',43,'CREATE','Created new spk record',NULL,'{\"spk_id\":43,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:15:28','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[43]}'),(157,'spk',43,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-16 08:15:35','SERVICE','Service Management','MEDIUM','{\"spk\":[43]}'),(158,'spk',44,'CREATE','Created new spk record',NULL,'{\"spk_id\":44,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:16:37','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[44]}'),(159,'spk',45,'CREATE','Created new spk record',NULL,'{\"spk_id\":45,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:26:45','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[45]}'),(160,'spk',46,'CREATE','Created new spk record',NULL,'{\"spk_id\":46,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:27:38','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[46]}'),(161,'spk',47,'CREATE','Created new spk record',NULL,'{\"spk_id\":47,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:37:20','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[47]}'),(162,'spk',48,'CREATE','Created new spk record',NULL,'{\"spk_id\":48,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"ATTACHMENT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:37:45','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[48]}'),(163,'spk',49,'CREATE','Created new spk record',NULL,'{\"spk_id\":49,\"nomor_spk\":\"SPK\\/202509\\/011\",\"jenis_spk\":\"ATTACHMENT\",\"kontrak_id\":\"56\",\"kontrak_spesifikasi_id\":\"44\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-16 08:43:46','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[49]}'),(164,'spk',49,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-16 08:43:55','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(165,'spk',49,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 08:43:55\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[]}\"}','{\"fabrikasi_mekanik\":\"IYAN\",\"fabrikasi_estimasi_mulai\":\"2025-09-16\",\"fabrikasi_estimasi_selesai\":\"2025-09-16\",\"fabrikasi_tanggal_approve\":\"2025-09-16 09:10:32\",\"diperbarui_pada\":\"2025-09-16 09:10:32\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-16 09:10:32','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(166,'spk',49,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 09:10:32\"}','{\"painting_mekanik\":\"IYAN\",\"painting_estimasi_mulai\":\"2025-09-16\",\"painting_estimasi_selesai\":\"2025-09-16\",\"painting_tanggal_approve\":\"2025-09-16 09:10:35\",\"diperbarui_pada\":\"2025-09-16 09:10:35\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-16 09:10:35','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(167,'spk',49,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-16 09:10:35\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"IYAN\",\"pdi_estimasi_mulai\":\"2025-09-16\",\"pdi_estimasi_selesai\":\"2025-09-16\",\"pdi_tanggal_approve\":\"2025-09-16 09:10:41\",\"diperbarui_pada\":\"2025-09-16 09:10:41\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":null,\\\"tipe_jenis\\\":null,\\\"merk_unit\\\":null,\\\"model_unit\\\":null,\\\"kapasitas_id\\\":null,\\\"attachment_tipe\\\":\\\"SIDE SHIFTER\\\",\\\"attachment_merk\\\":\\\"\\\",\\\"jenis_baterai\\\":\\\"\\\",\\\"charger_id\\\":\\\"0\\\",\\\"mast_id\\\":null,\\\"ban_id\\\":null,\\\"roda_id\\\":null,\\\"valve_id\\\":null,\\\"aksesoris\\\":[],\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":null,\\\"battery_inventory_id\\\":null,\\\"charger_inventory_id\\\":null,\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":null,\\\"mekanik\\\":\\\"IYAN\\\",\\\"catatan\\\":\\\"a\\\",\\\"timestamp\\\":\\\"2025-09-16 09:10:41\\\"}]}\",\"pdi_catatan\":\"a\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-16 09:10:41','SERVICE','Service Management','MEDIUM','{\"spk\":[49]}'),(168,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-17 01:26:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(169,'delivery_instruction',131,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":131,\"nomor_di\":\"DI\\/202509\\/010\",\"spk_id\":49,\"po_kontrak_nomor\":\"TEST\\/AUTO\\/001\",\"pelanggan\":\"Test Client\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-17 02:54:40','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[131]}'),(170,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-22 01:23:38','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(171,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-22 06:17:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(172,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-22 06:17:50','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(173,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-23 06:17:04','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(174,'delivery_instruction',131,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-23 06:21:02','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[131]}'),(175,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-24 02:00:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(176,'work_orders',5,'CREATE','Work order 15060 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:05:39','SERVICE',NULL,'LOW',NULL),(177,'work_orders',6,'CREATE','Work order 15061 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:06:37','SERVICE',NULL,'LOW',NULL),(178,'work_orders',7,'CREATE','Work order 15062 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:11:17','SERVICE',NULL,'LOW',NULL),(179,'work_orders',8,'CREATE','Work order 15063 created',NULL,NULL,NULL,1,NULL,0,'2025-09-24 10:11:27','SERVICE',NULL,'LOW',NULL),(180,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 01:29:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(181,'work_orders',9,'CREATE','Work order 15064 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 01:37:24','SERVICE',NULL,'LOW',NULL),(182,'work_orders',10,'CREATE','Work order 15065 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 01:40:02','SERVICE',NULL,'LOW',NULL),(183,'work_orders',10,'DELETE','Deleted work_orders record','{\"id\":\"10\",\"work_order_number\":\"15065\",\"report_date\":\"2025-09-25 01:40:02\",\"unit_id\":\"1\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"1\",\"subcategory_id\":null,\"complaint_description\":\"Test validation successful\",\"status_id\":\"1\",\"admin_staff_id\":null,\"foreman_staff_id\":null,\"mechanic_staff_id\":null,\"helper_staff_id\":null,\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":null,\"created_by\":\"1\",\"created_at\":\"2025-09-25 01:40:02\",\"updated_at\":\"2025-09-25 01:40:02\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 01:43:49','SERVICE',NULL,'LOW',NULL),(184,'work_orders',1,'DELETE','Deleted work_orders record','{\"id\":\"1\",\"work_order_number\":\"15059\",\"report_date\":\"2025-09-18 08:52:52\",\"unit_id\":\"1\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":\"2025-09-18 09:00:00\",\"category_id\":\"8\",\"subcategory_id\":\"1\",\"complaint_description\":\"Ban depan belakang gundul\",\"status_id\":\"2\",\"admin_staff_id\":\"1\",\"foreman_staff_id\":\"4\",\"mechanic_staff_id\":\"7\",\"helper_staff_id\":\"13\",\"repair_description\":\"Ban depan belakang gundul\",\"notes\":null,\"sparepart_used\":\"Ban hidup 700-12, ban hidup 600-9\",\"time_to_repair\":null,\"completion_date\":null,\"area\":\"PURWAKARTA\",\"created_by\":\"1\",\"created_at\":\"2025-09-23 15:24:36\",\"updated_at\":\"2025-09-25 08:51:49\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 01:52:30','SERVICE',NULL,'LOW',NULL),(185,'work_orders',11,'CREATE','Work order 15066 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 01:55:12','SERVICE',NULL,'LOW',NULL),(186,'work_orders',12,'CREATE','Work order 15067 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 02:07:46','SERVICE',NULL,'LOW',NULL),(187,'work_orders',12,'DELETE','Deleted work_orders record','{\"id\":\"12\",\"work_order_number\":\"15067\",\"report_date\":\"2025-09-25 02:07:46\",\"unit_id\":\"11\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"7\",\"subcategory_id\":\"121\",\"complaint_description\":\"RUSAK\",\"status_id\":\"1\",\"admin_staff_id\":\"3\",\"foreman_staff_id\":\"4\",\"mechanic_staff_id\":\"7\",\"helper_staff_id\":\"16\",\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":\"BEKASI\",\"created_by\":\"1\",\"created_at\":\"2025-09-25 02:07:46\",\"updated_at\":\"2025-09-25 02:07:46\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 02:12:04','SERVICE',NULL,'LOW',NULL),(188,'work_orders',13,'CREATE','Work order 15068 created',NULL,NULL,NULL,1,NULL,0,'2025-09-25 02:15:04','SERVICE',NULL,'LOW',NULL),(189,'work_orders',13,'DELETE','Deleted work_orders record','{\"id\":\"13\",\"work_order_number\":\"15068\",\"report_date\":\"2025-09-25 02:15:04\",\"unit_id\":\"11\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"7\",\"subcategory_id\":\"121\",\"complaint_description\":\"Test delete dari modal\",\"status_id\":\"1\",\"admin_staff_id\":\"3\",\"foreman_staff_id\":\"4\",\"mechanic_staff_id\":\"7\",\"helper_staff_id\":\"16\",\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":\"BEKASI\",\"created_by\":\"1\",\"created_at\":\"2025-09-25 02:15:04\",\"updated_at\":\"2025-09-25 02:15:04\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 02:15:23','SERVICE',NULL,'LOW',NULL),(190,'work_orders',11,'DELETE','Deleted work_orders record','{\"id\":\"11\",\"work_order_number\":\"15066\",\"report_date\":\"2025-09-25 01:55:12\",\"unit_id\":\"1\",\"order_type\":\"COMPLAINT\",\"priority_id\":\"2\",\"requested_repair_time\":null,\"category_id\":\"1\",\"subcategory_id\":null,\"complaint_description\":\"Test complaint dari curl untuk debug\",\"status_id\":\"1\",\"admin_staff_id\":null,\"foreman_staff_id\":null,\"mechanic_staff_id\":null,\"helper_staff_id\":null,\"repair_description\":null,\"notes\":null,\"sparepart_used\":null,\"time_to_repair\":null,\"completion_date\":null,\"area\":null,\"created_by\":\"1\",\"created_at\":\"2025-09-25 01:55:12\",\"updated_at\":\"2025-09-25 01:55:12\",\"deleted_at\":null}',NULL,'[\"id\",\"work_order_number\",\"report_date\",\"unit_id\",\"order_type\",\"priority_id\",\"requested_repair_time\",\"category_id\",\"subcategory_id\",\"complaint_description\",\"status_id\",\"admin_staff_id\",\"foreman_staff_id\",\"mechanic_staff_id\",\"helper_staff_id\",\"repair_description\",\"notes\",\"sparepart_used\",\"time_to_repair\",\"completion_date\",\"area\",\"created_by\",\"created_at\",\"updated_at\",\"deleted_at\"]',1,NULL,1,'2025-09-25 02:19:38','SERVICE',NULL,'LOW',NULL),(191,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-09-25 07:27:49','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(192,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 07:27:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(193,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 09:20:01','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(194,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 09:30:28','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(195,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-25 10:04:05','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(196,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-26 01:28:03','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(197,'kontrak',0,'','Gagal menyimpan kontrak 12345 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 04:44:52','MARKETING','Data Kontrak','MEDIUM',NULL),(198,'kontrak',0,'','Gagal menyimpan kontrak PO-92131239 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 06:55:33','MARKETING','Data Kontrak','MEDIUM',NULL),(199,'kontrak',0,'','Gagal menyimpan kontrak PO-92131239 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 06:55:50','MARKETING','Data Kontrak','MEDIUM',NULL),(200,'kontrak',0,'','Gagal menyimpan kontrak PO-92131239 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 06:58:57','MARKETING','Data Kontrak','MEDIUM',NULL),(201,'kontrak',0,'','Gagal menyimpan kontrak PO-92131239 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 06:59:17','MARKETING','Data Kontrak','MEDIUM',NULL),(202,'kontrak',0,'','Gagal menyimpan kontrak PO-92131239 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 07:02:14','MARKETING','Data Kontrak','MEDIUM',NULL),(203,'kontrak',0,'','Gagal menyimpan kontrak PO-92131239 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 07:02:21','MARKETING','Data Kontrak','MEDIUM',NULL),(204,'kontrak',0,'','Gagal menyimpan kontrak PO-92131239 ke database - Error: Gagal menyimpan data ke database: Customer location harus dipilih. | DB Error: ',NULL,NULL,NULL,1,'CREATE_FAILED',1,'2025-09-26 07:05:11','MARKETING','Data Kontrak','MEDIUM',NULL),(205,'kontrak',0,'','Gagal membuat kontrak  - Validasi error: no_kontrak, tanggal_mulai, tanggal_berakhir',NULL,NULL,NULL,1,'CREATE_FAILED',0,'2025-09-26 07:27:10','MARKETING','Data Kontrak','LOW',NULL),(206,'kontrak',62,'CREATE','Kontrak created: TEST-9213191924 (Client: Sarana Mitra Luas - Gudang Perawang)',NULL,'{\"no_kontrak\":\"TEST-9213191924\",\"no_po_marketing\":\"PO-92131240\",\"customer_location_id\":12,\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-30\",\"tanggal_berakhir\":\"2025-11-30\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"customer_location_id\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-26 07:27:45','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[62]}'),(207,'kontrak',61,'DELETE','Kontrak deleted: TEST-9213191923 (Client: Test - Gudang Perawang)','{\"id\":\"61\",\"customer_location_id\":\"10\",\"no_kontrak\":\"TEST-9213191923\",\"no_po_marketing\":\"PO-92131239\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-26\",\"tanggal_berakhir\":\"2025-11-26\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-26 07:07:57\",\"diperbarui_pada\":\"2025-09-26 07:07:57\"}',NULL,'[\"id\",\"customer_location_id\",\"no_kontrak\",\"no_po_marketing\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-26 07:30:33','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[61]}'),(208,'kontrak',58,'DELETE','Kontrak deleted: test/1/1/6 (Client: Sarana Mitra Luas - Lokasi Utama)','{\"id\":\"58\",\"customer_location_id\":\"1\",\"no_kontrak\":\"test\\/1\\/1\\/6\",\"no_po_marketing\":\"12345\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"HARIAN\",\"tanggal_mulai\":\"2025-09-13\",\"tanggal_berakhir\":\"2025-09-13\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-15 08:06:12\",\"diperbarui_pada\":\"2025-09-26 13:57:58\"}',NULL,'[\"id\",\"customer_location_id\",\"no_kontrak\",\"no_po_marketing\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-26 07:30:41','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[58]}'),(209,'kontrak',62,'DELETE','Kontrak deleted: TEST-9213191924 (Client: Sarana Mitra Luas - Gudang Perawang)','{\"id\":\"62\",\"customer_location_id\":\"12\",\"no_kontrak\":\"TEST-9213191924\",\"no_po_marketing\":\"PO-92131240\",\"nilai_total\":\"0.00\",\"total_units\":\"0\",\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-30\",\"tanggal_berakhir\":\"2025-11-30\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\",\"dibuat_pada\":\"2025-09-26 07:27:45\",\"diperbarui_pada\":\"2025-09-26 07:27:45\"}',NULL,'[\"id\",\"customer_location_id\",\"no_kontrak\",\"no_po_marketing\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\",\"dibuat_pada\",\"diperbarui_pada\"]',1,'DELETE_CONFIRMED',1,'2025-09-26 07:30:50','MARKETING','Data Kontrak','HIGH','{\"kontrak\":[62]}'),(210,'kontrak',63,'CREATE','Kontrak created: KNTRK/2209/0002 (Client: Sarana Mitra Luas - Gudang Perawang)',NULL,'{\"no_kontrak\":\"KNTRK\\/2209\\/0002\",\"no_po_marketing\":\"PO-92131240\",\"customer_location_id\":12,\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-30\",\"tanggal_berakhir\":\"2025-10-31\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"customer_location_id\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-26 07:34:19','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[63]}'),(211,'spk',50,'CREATE','Created new spk record',NULL,'{\"spk_id\":50,\"nomor_spk\":\"SPK\\/202509\\/012\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"45\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-26 07:49:58','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[50]}'),(212,'spk',50,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-26 07:53:27','SERVICE','Service Management','MEDIUM','{\"spk\":[50]}'),(213,'spk',50,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-26 07:53:27\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"JUNGHEINRICH\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"37\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"3\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-26\",\"persiapan_unit_estimasi_selesai\":\"2025-09-26\",\"persiapan_unit_tanggal_approve\":\"2025-09-26 07:56:50\",\"diperbarui_pada\":\"2025-09-26 07:56:50\",\"persiapan_unit_id\":\"17\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BLUE SPOT\\\",\\\"RED LINE\\\",\\\"WORK LIGHT\\\",\\\"ROTARY LAMP\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"JUNGHEINRICH\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"37\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"3\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\"}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-26 07:56:50','SERVICE','Service Management','MEDIUM','{\"spk\":[50]}'),(214,'spk',50,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-26 07:56:50\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"JUNGHEINRICH\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"37\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"3\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\"}\"}','{\"fabrikasi_mekanik\":\"ARIZAL-EKA\",\"fabrikasi_estimasi_mulai\":\"2025-09-26\",\"fabrikasi_estimasi_selesai\":\"2025-09-26\",\"fabrikasi_tanggal_approve\":\"2025-09-26 07:57:04\",\"diperbarui_pada\":\"2025-09-26 07:57:04\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"JUNGHEINRICH\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"37\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"3\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-26 07:57:04','SERVICE','Service Management','MEDIUM','{\"spk\":[50]}'),(215,'spk',50,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-26 07:57:04\"}','{\"painting_mekanik\":\"ARIZAL-EKA\",\"painting_estimasi_mulai\":\"2025-09-26\",\"painting_estimasi_selesai\":\"2025-09-26\",\"painting_tanggal_approve\":\"2025-09-26 07:57:09\",\"diperbarui_pada\":\"2025-09-26 07:57:09\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-26 07:57:09','SERVICE','Service Management','MEDIUM','{\"spk\":[50]}'),(216,'spk',50,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-26 07:57:09\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"JUNGHEINRICH\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"37\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"3\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"ARIZAL-EKA\",\"pdi_estimasi_mulai\":\"2025-09-26\",\"pdi_estimasi_selesai\":\"2025-09-26\",\"pdi_tanggal_approve\":\"2025-09-26 07:57:16\",\"diperbarui_pada\":\"2025-09-26 07:57:16\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"COUNTER BALANCE\\\",\\\"merk_unit\\\":\\\"JUNGHEINRICH\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"37\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"14\\\",\\\"ban_id\\\":\\\"6\\\",\\\"roda_id\\\":\\\"3\\\",\\\"valve_id\\\":\\\"1\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"fabrikasi_attachment_id\\\":\\\"16\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"17\\\",\\\"battery_inventory_id\\\":\\\"3\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":\\\"16\\\",\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BLUE SPOT\\\\\\\",\\\\\\\"RED LINE\\\\\\\",\\\\\\\"WORK LIGHT\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\"]\\\",\\\"mekanik\\\":\\\"ARIZAL-EKA\\\",\\\"catatan\\\":\\\"oke\\\",\\\"timestamp\\\":\\\"2025-09-26 07:57:16\\\"}]}\",\"pdi_catatan\":\"oke\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-26 07:57:16','SERVICE','Service Management','MEDIUM','{\"spk\":[50]}'),(217,'delivery_instruction',132,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":132,\"nomor_di\":\"DI\\/202509\\/011\",\"spk_id\":50,\"po_kontrak_nomor\":\"KNTRK\\/2209\\/0002\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":2,\"unit_ids\":[17]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-26 07:57:51','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[132]}'),(218,'delivery_instruction',132,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status\":\"SUBMITTED\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status\":\"PROCESSED\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status\"]',1,'UPDATED',0,'2025-09-26 07:57:59','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[132]}'),(219,'delivery_instruction',132,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-26 14:57:59\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"catatan\":null,\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-26\",\"diperbarui_pada\":\"2025-09-26 07:58:25\",\"estimasi_sampai\":\"2025-09-26\",\"nama_supir\":\"IQBAL\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"colt diesel\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"catatan\":\"GAS\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"catatan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-26 07:58:25','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[132]}'),(220,'delivery_instruction',132,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-26 14:58:25\",\"catatan_berangkat\":null,\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-26\",\"diperbarui_pada\":\"2025-09-26 07:58:32\",\"catatan_berangkat\":\"oke\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"catatan_berangkat\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-26 07:58:32','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[132]}'),(221,'delivery_instruction',132,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-26 14:58:32\",\"catatan_sampai\":null,\"status\":\"PROCESSED\",\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-26\",\"diperbarui_pada\":\"2025-09-26 07:58:37\",\"catatan_sampai\":\"oke\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-26 07:58:37','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[132]}'),(222,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-27 01:15:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(223,'kontrak',64,'CREATE','Kontrak created: LG-9812310 (Client: PT LG Indonesia - Head Office)',NULL,'{\"no_kontrak\":\"LG-9812310\",\"no_po_marketing\":\"0218812310\",\"customer_location_id\":14,\"nilai_total\":0,\"total_units\":0,\"jenis_sewa\":\"BULANAN\",\"tanggal_mulai\":\"2025-09-27\",\"tanggal_berakhir\":\"2025-10-27\",\"status\":\"Pending\",\"dibuat_oleh\":\"1\"}','[\"no_kontrak\",\"no_po_marketing\",\"customer_location_id\",\"nilai_total\",\"total_units\",\"jenis_sewa\",\"tanggal_mulai\",\"tanggal_berakhir\",\"status\",\"dibuat_oleh\"]',1,'DRAFT',0,'2025-09-27 03:52:56','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[64]}'),(224,'kontrak_spesifikasi',46,'UPDATE','Mengubah spesifikasi SPEC-001','{\"catatan_spek\":\"\"}','{\"catatan_spek\":\"Spare Unit\"}','[\"catatan_spek\"]',1,'SPECIFICATION_UPDATED',0,'2025-09-27 03:56:47','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[64],\"kontrak_spesifikasi\":[46]}'),(225,'spk',51,'CREATE','Created new spk record',NULL,'{\"spk_id\":51,\"nomor_spk\":\"SPK\\/202509\\/013\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"64\",\"kontrak_spesifikasi_id\":\"46\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-27 04:04:12','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[51]}'),(226,'spk',51,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-27 04:04:20','SERVICE','Service Management','MEDIUM','{\"spk\":[51]}'),(227,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-27 13:26:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(228,'spk',51,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 04:04:20\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-27\",\"persiapan_unit_estimasi_selesai\":\"2025-10-31\",\"persiapan_unit_tanggal_approve\":\"2025-09-27 14:25:17\",\"diperbarui_pada\":\"2025-09-27 14:25:17\",\"persiapan_unit_id\":\"16\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"persiapan_attachment_action\\\":\\\"keep_existing\\\",\\\"persiapan_attachment_id\\\":\\\"3\\\"}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-27 14:25:17','SERVICE','Service Management','MEDIUM','{\"spk\":[51]}'),(229,'spk',52,'CREATE','Created new spk record',NULL,'{\"spk_id\":52,\"nomor_spk\":\"SPK\\/202509\\/013\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"64\",\"kontrak_spesifikasi_id\":\"46\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-27 14:52:22','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[52]}'),(230,'spk',52,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-27 14:52:29','SERVICE','Service Management','MEDIUM','{\"spk\":[52]}'),(231,'spk',52,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 14:52:29\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-27\",\"persiapan_unit_estimasi_selesai\":\"2025-10-31\",\"persiapan_unit_tanggal_approve\":\"2025-09-27 14:53:40\",\"diperbarui_pada\":\"2025-09-27 14:53:40\",\"persiapan_unit_id\":\"16\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"persiapan_attachment_action\\\":\\\"keep_existing\\\",\\\"persiapan_attachment_id\\\":\\\"3\\\"}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-27 14:53:40','SERVICE','Service Management','MEDIUM','{\"spk\":[52]}'),(232,'spk',52,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 14:53:40\"}','{\"fabrikasi_mekanik\":\"ARIZAL-EKA\",\"fabrikasi_estimasi_mulai\":\"2025-09-27\",\"fabrikasi_estimasi_selesai\":\"2025-09-27\",\"fabrikasi_tanggal_approve\":\"2025-09-27 14:58:09\",\"diperbarui_pada\":\"2025-09-27 14:58:09\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-27 14:58:09','SERVICE','Service Management','MEDIUM','{\"spk\":[52]}'),(233,'spk',52,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 14:58:09\"}','{\"painting_mekanik\":\"ARIZAL-EKA\",\"painting_estimasi_mulai\":\"2025-09-27\",\"painting_estimasi_selesai\":\"2025-09-27\",\"painting_tanggal_approve\":\"2025-09-27 14:58:14\",\"diperbarui_pada\":\"2025-09-27 14:58:14\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-27 14:58:14','SERVICE','Service Management','MEDIUM','{\"spk\":[52]}'),(234,'spk',52,'UPDATE','Updated spk record','{\"pdi_mekanik\":null,\"pdi_estimasi_mulai\":null,\"pdi_estimasi_selesai\":null,\"pdi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 14:58:14\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"persiapan_attachment_action\\\":\\\"keep_existing\\\",\\\"persiapan_attachment_id\\\":\\\"3\\\"}\",\"pdi_catatan\":null,\"status\":\"IN_PROGRESS\"}','{\"pdi_mekanik\":\"ARIZAL-EKA\",\"pdi_estimasi_mulai\":\"2025-09-27\",\"pdi_estimasi_selesai\":\"2025-09-27\",\"pdi_tanggal_approve\":\"2025-09-27 14:58:21\",\"diperbarui_pada\":\"2025-09-27 14:58:21\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"persiapan_attachment_action\\\":\\\"keep_existing\\\",\\\"persiapan_attachment_id\\\":\\\"3\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"16\\\",\\\"battery_inventory_id\\\":\\\"3\\\",\\\"charger_inventory_id\\\":\\\"5\\\",\\\"attachment_inventory_id\\\":null,\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"ROTARY LAMP\\\\\\\",\\\\\\\"SENSOR PARKING\\\\\\\",\\\\\\\"HORN SPEAKER\\\\\\\",\\\\\\\"APAR 1 KG\\\\\\\",\\\\\\\"BEACON\\\\\\\"]\\\",\\\"mekanik\\\":\\\"ARIZAL-EKA\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-27 14:58:21\\\"}]}\",\"pdi_catatan\":\"ok\",\"status\":\"READY\"}','[\"pdi_mekanik\",\"pdi_estimasi_mulai\",\"pdi_estimasi_selesai\",\"pdi_tanggal_approve\",\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"status\"]',1,'UPDATED',0,'2025-09-27 14:58:21','SERVICE','Service Management','MEDIUM','{\"spk\":[52]}'),(235,'spk',53,'CREATE','Created new spk record',NULL,'{\"spk_id\":53,\"nomor_spk\":\"SPK\\/202509\\/013\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"64\",\"kontrak_spesifikasi_id\":\"46\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-27 16:24:51','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[53]}'),(236,'spk',53,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-27 16:24:56','SERVICE','Service Management','MEDIUM','{\"spk\":[53]}'),(237,'spk',53,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 16:24:56\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-27\",\"persiapan_unit_estimasi_selesai\":\"2025-09-27\",\"persiapan_unit_tanggal_approve\":\"2025-09-27 16:25:24\",\"diperbarui_pada\":\"2025-09-27 16:25:24\",\"persiapan_unit_id\":\"16\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"ROTARY LAMP\\\",\\\"SENSOR PARKING\\\",\\\"HORN SPEAKER\\\",\\\"APAR 1 KG\\\",\\\"BEACON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"2\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"REACH TRUCK\\\",\\\"merk_unit\\\":\\\"KOMATSU\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORKLIFT SCALE\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":\\\"Lithium-ion\\\",\\\"charger_id\\\":\\\"9\\\",\\\"mast_id\\\":\\\"15\\\",\\\"ban_id\\\":\\\"3\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"3\\\",\\\"persiapan_charger_action\\\":\\\"keep_existing\\\",\\\"persiapan_charger_id\\\":\\\"5\\\",\\\"persiapan_attachment_action\\\":\\\"keep_existing\\\",\\\"persiapan_attachment_id\\\":\\\"3\\\"}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-27 16:25:24','SERVICE','Service Management','MEDIUM','{\"spk\":[53]}'),(238,'spk',53,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 16:25:24\"}','{\"fabrikasi_mekanik\":\"ARIZAL-EKA\",\"fabrikasi_estimasi_mulai\":\"2025-09-27\",\"fabrikasi_estimasi_selesai\":\"2025-09-27\",\"fabrikasi_tanggal_approve\":\"2025-09-27 16:48:26\",\"diperbarui_pada\":\"2025-09-27 16:48:26\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-27 16:48:26','SERVICE','Service Management','MEDIUM','{\"spk\":[53]}'),(239,'spk',54,'CREATE','Created new spk record',NULL,'{\"spk_id\":54,\"nomor_spk\":\"SPK\\/202509\\/014\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"64\",\"kontrak_spesifikasi_id\":\"47\",\"jumlah_unit\":4}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-27 17:01:04','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[54]}'),(240,'spk',54,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-27 17:02:06','SERVICE','Service Management','MEDIUM','{\"spk\":[54]}'),(241,'spk',54,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 17:02:06\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"6\\\",\\\"ban_id\\\":\\\"4\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-27\",\"persiapan_unit_estimasi_selesai\":\"2025-09-27\",\"persiapan_unit_tanggal_approve\":\"2025-09-27 17:02:34\",\"diperbarui_pada\":\"2025-09-27 17:02:34\",\"persiapan_unit_id\":\"8\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BACK BUZZER\\\",\\\"HORN KLASON\\\",\\\"P3K\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"6\\\",\\\"ban_id\\\":\\\"4\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"32\\\"}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-27 17:02:34','SERVICE','Service Management','MEDIUM','{\"spk\":[54]}'),(242,'spk',54,'UPDATE','Updated spk record','{\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 17:02:34\"}','{\"fabrikasi_mekanik\":\"ARIZAL-EKA\",\"fabrikasi_estimasi_mulai\":\"2025-09-27\",\"fabrikasi_estimasi_selesai\":\"2025-09-27\",\"fabrikasi_tanggal_approve\":\"2025-09-27 17:02:42\",\"diperbarui_pada\":\"2025-09-27 17:02:42\"}','[\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-27 17:02:42','SERVICE','Service Management','MEDIUM','{\"spk\":[54]}'),(243,'spk',54,'UPDATE','Updated spk record','{\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 17:02:42\"}','{\"painting_mekanik\":\"ARIZAL-EKA\",\"painting_estimasi_mulai\":\"2025-09-27\",\"painting_estimasi_selesai\":\"2025-09-27\",\"painting_tanggal_approve\":\"2025-09-27 17:02:47\",\"diperbarui_pada\":\"2025-09-27 17:02:47\"}','[\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\",\"diperbarui_pada\"]',1,'UPDATED',0,'2025-09-27 17:02:47','SERVICE','Service Management','MEDIUM','{\"spk\":[54]}'),(244,'spk',54,'UPDATE','Updated spk record','{\"diperbarui_pada\":\"2025-09-27 17:02:47\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"6\\\",\\\"ban_id\\\":\\\"4\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"32\\\"}\",\"pdi_catatan\":null,\"persiapan_unit_id\":\"8\",\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-27\",\"persiapan_unit_estimasi_selesai\":\"2025-09-27\",\"persiapan_unit_tanggal_approve\":\"2025-09-27 17:02:34\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BACK BUZZER\\\",\\\"HORN KLASON\\\",\\\"P3K\\\"]\",\"fabrikasi_mekanik\":\"ARIZAL-EKA\",\"fabrikasi_estimasi_mulai\":\"2025-09-27\",\"fabrikasi_estimasi_selesai\":\"2025-09-27\",\"fabrikasi_tanggal_approve\":\"2025-09-27 17:02:42\",\"painting_mekanik\":\"ARIZAL-EKA\",\"painting_estimasi_mulai\":\"2025-09-27\",\"painting_estimasi_selesai\":\"2025-09-27\",\"painting_tanggal_approve\":\"2025-09-27 17:02:47\"}','{\"diperbarui_pada\":\"2025-09-27 17:02:53\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"6\\\",\\\"ban_id\\\":\\\"4\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"32\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"8\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"32\\\",\\\"attachment_inventory_id\\\":null,\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BACK BUZZER\\\\\\\",\\\\\\\"HORN KLASON\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"ARIZAL-EKA\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-27 17:02:53\\\"}]}\",\"pdi_catatan\":\"ok\",\"persiapan_unit_id\":null,\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"persiapan_aksesoris_tersedia\":null,\"fabrikasi_mekanik\":null,\"fabrikasi_estimasi_mulai\":null,\"fabrikasi_estimasi_selesai\":null,\"fabrikasi_tanggal_approve\":null,\"painting_mekanik\":null,\"painting_estimasi_mulai\":null,\"painting_estimasi_selesai\":null,\"painting_tanggal_approve\":null}','[\"diperbarui_pada\",\"spesifikasi\",\"pdi_catatan\",\"persiapan_unit_id\",\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"persiapan_aksesoris_tersedia\",\"fabrikasi_mekanik\",\"fabrikasi_estimasi_mulai\",\"fabrikasi_estimasi_selesai\",\"fabrikasi_tanggal_approve\",\"painting_mekanik\",\"painting_estimasi_mulai\",\"painting_estimasi_selesai\",\"painting_tanggal_approve\"]',1,'UPDATED',0,'2025-09-27 17:02:53','SERVICE','Service Management','MEDIUM','{\"spk\":[54]}'),(245,'spk',54,'UPDATE','Updated spk record','{\"persiapan_unit_mekanik\":null,\"persiapan_unit_estimasi_mulai\":null,\"persiapan_unit_estimasi_selesai\":null,\"persiapan_unit_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-27 17:02:53\",\"persiapan_unit_id\":null,\"persiapan_aksesoris_tersedia\":null,\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"6\\\",\\\"ban_id\\\":\\\"4\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"32\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"8\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"32\\\",\\\"attachment_inventory_id\\\":null,\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BACK BUZZER\\\\\\\",\\\\\\\"HORN KLASON\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"ARIZAL-EKA\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-27 17:02:53\\\"}]}\"}','{\"persiapan_unit_mekanik\":\"ARIZAL-EKA\",\"persiapan_unit_estimasi_mulai\":\"2025-09-27\",\"persiapan_unit_estimasi_selesai\":\"2025-09-27\",\"persiapan_unit_tanggal_approve\":\"2025-09-27 17:03:12\",\"diperbarui_pada\":\"2025-09-27 17:03:12\",\"persiapan_unit_id\":\"14\",\"persiapan_aksesoris_tersedia\":\"[\\\"LAMPU UTAMA\\\",\\\"BACK BUZZER\\\",\\\"HORN KLASON\\\"]\",\"spesifikasi\":\"{\\\"departemen_id\\\":\\\"1\\\",\\\"tipe_unit_id\\\":\\\"6\\\",\\\"tipe_jenis\\\":\\\"PALLET STACKER\\\",\\\"merk_unit\\\":\\\"LINDE\\\",\\\"model_unit\\\":null,\\\"kapasitas_id\\\":\\\"42\\\",\\\"attachment_tipe\\\":\\\"FORK\\\",\\\"attachment_merk\\\":null,\\\"jenis_baterai\\\":null,\\\"charger_id\\\":null,\\\"mast_id\\\":\\\"6\\\",\\\"ban_id\\\":\\\"4\\\",\\\"roda_id\\\":\\\"1\\\",\\\"valve_id\\\":\\\"2\\\",\\\"aksesoris\\\":[],\\\"persiapan_battery_action\\\":\\\"keep_existing\\\",\\\"persiapan_battery_id\\\":\\\"6\\\",\\\"persiapan_charger_action\\\":\\\"assign\\\",\\\"persiapan_charger_id\\\":\\\"33\\\",\\\"prepared_units\\\":[{\\\"unit_id\\\":\\\"8\\\",\\\"battery_inventory_id\\\":\\\"6\\\",\\\"charger_inventory_id\\\":\\\"32\\\",\\\"attachment_inventory_id\\\":null,\\\"aksesoris_tersedia\\\":\\\"[\\\\\\\"LAMPU UTAMA\\\\\\\",\\\\\\\"BACK BUZZER\\\\\\\",\\\\\\\"HORN KLASON\\\\\\\",\\\\\\\"P3K\\\\\\\"]\\\",\\\"mekanik\\\":\\\"ARIZAL-EKA\\\",\\\"catatan\\\":\\\"ok\\\",\\\"timestamp\\\":\\\"2025-09-27 17:02:53\\\"}]}\"}','[\"persiapan_unit_mekanik\",\"persiapan_unit_estimasi_mulai\",\"persiapan_unit_estimasi_selesai\",\"persiapan_unit_tanggal_approve\",\"diperbarui_pada\",\"persiapan_unit_id\",\"persiapan_aksesoris_tersedia\",\"spesifikasi\"]',1,'UPDATED',0,'2025-09-27 17:03:12','SERVICE','Service Management','MEDIUM','{\"spk\":[54]}'),(246,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-29 01:54:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(247,'delivery_instruction',133,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":133,\"nomor_di\":\"DI\\/202509\\/012\",\"spk_id\":53,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":2,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-29 03:55:00','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[133]}'),(248,'delivery_instruction',134,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":134,\"nomor_di\":\"DI\\/202509\\/012\",\"spk_id\":53,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":2,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-29 03:57:54','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[134]}'),(249,'spk',55,'CREATE','Created new spk record',NULL,'{\"spk_id\":55,\"nomor_spk\":\"SPK\\/202509\\/015\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"64\",\"kontrak_spesifikasi_id\":\"47\",\"jumlah_unit\":2}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-29 07:02:57','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[55]}'),(250,'spk',55,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-29 07:03:04','SERVICE','Service Management','MEDIUM','{\"spk\":[55]}'),(251,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-09-30 02:05:41','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(252,'delivery_instruction',135,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":135,\"nomor_di\":\"DI\\/202509\\/013\",\"spk_id\":55,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 02:25:56','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[135]}'),(253,'delivery_instruction',135,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status_di\":\"DIAJUKAN\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_di\":\"SIAP_KIRIM\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_di\"]',1,'UPDATED',0,'2025-09-30 02:27:03','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[135]}'),(254,'delivery_instruction',135,'UPDATE','Updated delivery_instruction record','{\"status\":null}','{\"status\":\"PROCESSED\"}','[\"status\"]',1,'UPDATED',0,'2025-09-30 02:33:35','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[135]}'),(255,'delivery_instruction',135,'UPDATE','Updated delivery_instruction record','{\"status\":null}','{\"status\":\"PROCESSED\"}','[\"status\"]',1,'UPDATED',0,'2025-09-30 02:37:01','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[135]}'),(256,'delivery_instruction',134,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status_di\":\"DIAJUKAN\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_di\":\"SIAP_KIRIM\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_di\"]',1,'UPDATED',0,'2025-09-30 02:55:21','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[134]}'),(257,'delivery_instruction',135,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 09:27:03\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"catatan\":null,\"status_eksekusi\":null}','{\"perencanaan_tanggal_approve\":\"2025-09-30\",\"diperbarui_pada\":\"2025-09-30 02:55:59\",\"estimasi_sampai\":\"2025-09-30\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\",\"catatan\":\"as\",\"status_eksekusi\":\"READY\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"catatan\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-30 02:55:59','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[135]}'),(258,'delivery_instruction',135,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 02:55:59\",\"catatan_berangkat\":null,\"status_eksekusi\":null}','{\"berangkat_tanggal_approve\":\"2025-09-30\",\"diperbarui_pada\":\"2025-09-30 03:02:12\",\"catatan_berangkat\":\"ok\",\"status_eksekusi\":\"DISPATCHED\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"catatan_berangkat\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-30 03:02:12','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[135]}'),(259,'delivery_instruction',135,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 03:02:12\",\"catatan_sampai\":null,\"status\":null,\"status_eksekusi\":null}','{\"sampai_tanggal_approve\":\"2025-09-30\",\"diperbarui_pada\":\"2025-09-30 03:02:16\",\"catatan_sampai\":\"ok\",\"status\":\"DELIVERED\",\"status_eksekusi\":\"DELIVERED\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status\",\"status_eksekusi\"]',1,'UPDATED',0,'2025-09-30 03:02:16','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[135]}'),(260,'delivery_instruction',136,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":136,\"nomor_di\":\"DI\\/202509\\/012\",\"spk_id\":55,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 03:07:05','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[136]}'),(261,'delivery_instruction',136,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status_di\":\"DIAJUKAN\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_di\":\"SIAP_KIRIM\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_di\"]',1,'UPDATED',0,'2025-09-30 03:09:08','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[136]}'),(262,'delivery_instruction',136,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 10:09:08\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\"}','{\"perencanaan_tanggal_approve\":\"2025-09-30\",\"diperbarui_pada\":\"2025-09-30 03:15:24\",\"estimasi_sampai\":\"2025-09-30\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"TRUK\",\"no_polisi_kendaraan\":\"B 8213 JKT\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\"]',1,'UPDATED',0,'2025-09-30 03:15:24','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[136]}'),(263,'delivery_instruction',136,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 03:15:24\",\"catatan_berangkat\":null,\"status_di\":\"SIAP_KIRIM\"}','{\"berangkat_tanggal_approve\":\"2025-09-30\",\"diperbarui_pada\":\"2025-09-30 03:15:44\",\"catatan_berangkat\":\"gas\",\"status_di\":\"DALAM_PERJALANAN\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"catatan_berangkat\",\"status_di\"]',1,'UPDATED',0,'2025-09-30 03:15:44','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[136]}'),(264,'delivery_instruction',136,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 03:15:44\",\"catatan_sampai\":null,\"status_di\":\"DALAM_PERJALANAN\"}','{\"sampai_tanggal_approve\":\"2025-09-30\",\"diperbarui_pada\":\"2025-09-30 03:15:54\",\"catatan_sampai\":\"asd\",\"status_di\":\"SAMPAI_LOKASI\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status_di\"]',1,'UPDATED',0,'2025-09-30 03:15:54','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[136]}'),(265,'delivery_instruction',137,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":137,\"nomor_di\":\"DI\\/202509\\/012\",\"spk_id\":55,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 03:48:33','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[137]}'),(266,'delivery_instruction',137,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status_di\":\"DIAJUKAN\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_di\":\"SIAP_KIRIM\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_di\"]',1,'UPDATED',0,'2025-09-30 03:48:46','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[137]}'),(267,'delivery_instruction',138,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":138,\"nomor_di\":\"DI\\/202509\\/012\",\"spk_id\":54,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[8,14]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 04:08:29','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[138]}'),(268,'delivery_instruction',139,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":139,\"nomor_di\":\"DI\\/202509\\/013\",\"spk_id\":54,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":2,\"unit_ids\":[16,13]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 04:26:44','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[139]}'),(269,'delivery_instruction',140,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":140,\"nomor_di\":\"DI\\/202509\\/012\",\"spk_id\":54,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[8,14]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 04:38:01','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[140]}'),(270,'delivery_instruction',141,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":141,\"nomor_di\":\"DI\\/202509\\/013\",\"spk_id\":54,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[16,13]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 05:12:52','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[141]}'),(271,'delivery_instruction',142,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":142,\"nomor_di\":\"DI\\/202509\\/014\",\"spk_id\":42,\"po_kontrak_nomor\":\"test\\/1\\/1\\/5\",\"pelanggan\":\"Sarana Mitra Luas\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[13]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 06:37:04','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[142]}'),(272,'spk',56,'CREATE','Created new spk record',NULL,'{\"spk_id\":56,\"nomor_spk\":\"SPK\\/202509\\/015\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"64\",\"kontrak_spesifikasi_id\":\"46\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-09-30 06:37:43','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[56]}'),(273,'spk',56,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-09-30 06:37:47','SERVICE','Service Management','MEDIUM','{\"spk\":[56]}'),(274,'delivery_instruction',143,'CREATE','Created new delivery_instruction record',NULL,'{\"di_id\":143,\"nomor_di\":\"DI\\/202509\\/015\",\"spk_id\":56,\"po_kontrak_nomor\":\"LG-9812310\",\"pelanggan\":\"PT LG Indonesia\",\"jenis_perintah_kerja_id\":1,\"tujuan_perintah_kerja_id\":1,\"unit_ids\":[15]}','[\"di_id\",\"nomor_di\",\"spk_id\",\"po_kontrak_nomor\",\"pelanggan\",\"jenis_perintah_kerja_id\",\"tujuan_perintah_kerja_id\",\"unit_ids\"]',1,'CREATED',0,'2025-09-30 10:19:45','MARKETING','App\\s\\marketing Management','MEDIUM','{\"delivery_instruction\":[143]}'),(275,'delivery_instruction',143,'UPDATE','Updated delivery_instruction record','{\"nama_supir\":null,\"no_hp_supir\":null,\"no_sim_supir\":null,\"kendaraan\":null,\"no_polisi_kendaraan\":null,\"status_di\":\"DIAJUKAN\"}','{\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\",\"status_di\":\"SIAP_KIRIM\"}','[\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\",\"status_di\"]',1,'UPDATED',0,'2025-09-30 12:19:58','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[143]}'),(276,'delivery_instruction',143,'UPDATE','Updated delivery_instruction record','{\"perencanaan_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 19:19:58\",\"estimasi_sampai\":null,\"nama_supir\":\"\",\"no_hp_supir\":\"-\",\"no_sim_supir\":\"-\",\"kendaraan\":\"\",\"no_polisi_kendaraan\":\"-\"}','{\"perencanaan_tanggal_approve\":\"2025-09-30\",\"diperbarui_pada\":\"2025-09-30 12:20:53\",\"estimasi_sampai\":\"2025-09-30\",\"nama_supir\":\"UDIN\",\"no_hp_supir\":\"082138881231\",\"no_sim_supir\":\"8992381\",\"kendaraan\":\"colt diesel\",\"no_polisi_kendaraan\":\"B 8213 JKT\"}','[\"perencanaan_tanggal_approve\",\"diperbarui_pada\",\"estimasi_sampai\",\"nama_supir\",\"no_hp_supir\",\"no_sim_supir\",\"kendaraan\",\"no_polisi_kendaraan\"]',1,'UPDATED',0,'2025-09-30 12:20:53','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[143]}'),(277,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-01 01:30:48','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(278,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-01 06:36:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(279,'work_orders',14,'CREATE','Work order 15069 created',NULL,NULL,NULL,1,NULL,0,'2025-10-01 10:04:49','SERVICE',NULL,'LOW',NULL),(280,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-01 12:14:08','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(281,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-02 02:12:41','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(282,'work_orders',15,'CREATE','Work order 15070 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 08:37:26','SERVICE',NULL,'LOW',NULL),(284,'work_orders',17,'CREATE','Work order 15071 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 08:39:07','SERVICE',NULL,'LOW',NULL),(285,'work_orders',18,'CREATE','Work order 15072 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 08:39:36','SERVICE',NULL,'LOW',NULL),(286,'work_orders',19,'CREATE','Work order 15073 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 08:40:23','SERVICE',NULL,'LOW',NULL),(287,'work_orders',20,'CREATE','Work order 15074 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 08:44:51','SERVICE',NULL,'LOW',NULL),(288,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-02 12:21:43','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(290,'work_orders',22,'CREATE','Work order 15075 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 14:29:33','SERVICE',NULL,'LOW',NULL),(291,'work_orders',23,'CREATE','Work order 15076 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 14:37:14','SERVICE',NULL,'LOW',NULL),(292,'work_orders',24,'CREATE','Work order 15077 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 14:39:46','SERVICE',NULL,'LOW',NULL),(293,'work_orders',25,'CREATE','Work order 15078 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 14:49:57','SERVICE',NULL,'LOW',NULL),(294,'work_orders',26,'CREATE','Work order 15079 created',NULL,NULL,NULL,1,NULL,0,'2025-10-02 14:51:01','SERVICE',NULL,'LOW',NULL),(295,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-03 01:37:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(296,'work_orders',27,'CREATE','Work order 15080 created',NULL,NULL,NULL,1,NULL,0,'2025-10-03 08:20:39','SERVICE',NULL,'LOW',NULL),(298,'work_orders',29,'CREATE','Work order 15081 created',NULL,NULL,NULL,1,NULL,0,'2025-10-03 08:47:57','SERVICE',NULL,'LOW',NULL),(299,'work_orders',30,'CREATE','Work order 15082 created',NULL,NULL,NULL,1,NULL,0,'2025-10-03 08:51:23','SERVICE',NULL,'LOW',NULL),(300,'work_orders',31,'CREATE','Work order 15083 created',NULL,NULL,NULL,1,NULL,0,'2025-10-03 09:28:05','SERVICE',NULL,'LOW',NULL),(301,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-04 01:26:19','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(302,'work_orders',32,'CREATE','Work order 15084 created',NULL,NULL,NULL,1,NULL,0,'2025-10-04 01:55:19','SERVICE',NULL,'LOW',NULL),(303,'work_orders',33,'CREATE','Work order 15085 created',NULL,NULL,NULL,1,NULL,0,'2025-10-04 01:55:51','SERVICE',NULL,'LOW',NULL),(304,'work_orders',34,'CREATE','Work order 15086 created',NULL,NULL,NULL,1,NULL,0,'2025-10-04 02:04:14','SERVICE',NULL,'LOW',NULL),(305,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-04 15:16:10','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(306,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-05 13:03:12','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(307,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-06 01:22:13','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(308,'spk',57,'CREATE','Created new spk record',NULL,'{\"spk_id\":57,\"nomor_spk\":\"SPK\\/202510\\/001\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"64\",\"kontrak_spesifikasi_id\":\"46\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-06 03:48:55','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[57]}'),(309,'spk',57,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-10-06 03:50:07','SERVICE','Service Management','MEDIUM','{\"spk\":[57]}'),(310,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-06 04:11:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(311,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-06 04:11:42','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(312,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-07 02:18:45','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(313,'work_orders',35,'CREATE','Work order 15087 created',NULL,NULL,NULL,1,NULL,0,'2025-10-07 06:58:34','SERVICE',NULL,'LOW',NULL),(314,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-07 14:10:48','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(315,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-07 15:39:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(316,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-07 15:39:01','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(317,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-08 02:50:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(318,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-09 01:34:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(319,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-10 01:39:39','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(320,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-10 13:47:37','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(321,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-11 01:29:44','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(322,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-11 13:27:57','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(323,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-13 01:47:14','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(324,'spk',58,'CREATE','Created new spk record',NULL,'{\"spk_id\":58,\"nomor_spk\":\"SPK\\/202510\\/002\",\"jenis_spk\":\"ATTACHMENT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"45\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-13 04:37:30','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[58]}'),(325,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-14 01:51:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(326,'kontrak',0,'EXPORT','Export Kontrak CSV',NULL,NULL,NULL,1,'EXPORT',0,'2025-10-14 09:25:59','MARKETING','Kontrak','LOW',NULL),(327,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-14 09:44:06','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(328,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-14 09:44:07','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(329,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-15 01:57:18','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(330,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 01:36:06','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(331,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 02:26:56','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(332,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 02:31:51','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(333,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 02:33:03','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(334,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 02:33:44','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(335,'spk',59,'CREATE','Created new spk record',NULL,'{\"spk_id\":59,\"nomor_spk\":\"SPK\\/202510\\/003\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-16 02:37:47','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[59]}'),(336,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 02:51:41','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(337,'spk',60,'CREATE','Created new spk record',NULL,'{\"spk_id\":60,\"nomor_spk\":\"SPK\\/202510\\/004\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-16 03:11:02','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[60]}'),(338,'spk',61,'CREATE','Created new spk record',NULL,'{\"spk_id\":61,\"nomor_spk\":\"SPK\\/202510\\/005\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-16 03:26:06','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[61]}'),(339,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-16 03:34:54','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(340,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 03:34:55','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(341,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-16 03:35:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(342,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-16 03:36:15','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(343,'users',10,'DELETE','Deleted users record','{\"user_id\":\"10\",\"username\":\"adminmarketing\",\"email\":\"adminmarketing@optima.com\",\"first_name\":\"admin\",\"last_name\":\"marketing1\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 03:56:16','ADMIN','System Administration','HIGH','{\"users\":[10]}'),(344,'users',9,'DELETE','Deleted users record','{\"user_id\":\"9\",\"username\":\"operational\",\"email\":\"operational@optima.com\",\"first_name\":\"operational\",\"last_name\":\"sml\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 03:56:21','ADMIN','System Administration','HIGH','{\"users\":[9]}'),(345,'users',5,'DELETE','Deleted users record','{\"user_id\":\"5\",\"username\":\"admindiesel\",\"email\":\"admindiesel@optima.com\",\"first_name\":\"service\",\"last_name\":\"diesel\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 03:56:24','ADMIN','System Administration','HIGH','{\"users\":[5]}'),(346,'users',6,'DELETE','Deleted users record','{\"user_id\":\"6\",\"username\":\"adminelektrik\",\"email\":\"adminelektrik@optima.com\",\"first_name\":\"service\",\"last_name\":\"elektrik\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 03:56:27','ADMIN','System Administration','HIGH','{\"users\":[6]}'),(347,'users',11,'CREATE','Created new users record',NULL,'{\"user_id\":11,\"username\":\"adminmarketing\",\"email\":\"admin_marketing@optima.com\",\"roles\":[\"staff_marketing\"],\"divisions\":[\"marketing\"],\"permissions\":[],\"created_by\":\"1\"}','[\"user_id\",\"username\",\"email\",\"roles\",\"divisions\",\"permissions\",\"created_by\"]',1,'CREATED',0,'2025-10-16 04:40:24','ADMIN','System Administration','MEDIUM','{\"users\":[11]}'),(348,'users',11,'DELETE','Deleted users record','{\"user_id\":\"11\",\"username\":\"adminmarketing\",\"email\":\"admin_marketing@optima.com\",\"first_name\":\"Aries\",\"last_name\":\"Adityanto\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 06:12:28','ADMIN','System Administration','HIGH','{\"users\":[11]}'),(349,'users',12,'DELETE','Deleted users record','{\"user_id\":\"12\",\"username\":\"adminmarketing\",\"email\":\"admin_marketing@optima.com\",\"first_name\":\"Aries\",\"last_name\":\"Adityanto\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 06:24:06','ADMIN','System Administration','HIGH','{\"users\":[12]}'),(350,'users',13,'DELETE','Deleted users record','{\"user_id\":\"13\",\"username\":\"adminmarketing\",\"email\":\"admin_marketing@optima.com\",\"first_name\":\"Aries\",\"last_name\":\"Adityanto\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 06:28:35','ADMIN','System Administration','HIGH','{\"users\":[13]}'),(351,'users',14,'DELETE','Deleted users record','{\"user_id\":\"14\",\"username\":\"adminmarketing\",\"email\":\"admin_marketing@optima.com\",\"first_name\":\"Aries\",\"last_name\":\"Adityanto\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 06:34:08','ADMIN','System Administration','HIGH','{\"users\":[14]}'),(352,'users',15,'DELETE','Deleted users record','{\"user_id\":\"15\",\"username\":\"adminmarketing\",\"email\":\"admin_marketing@optima.com\",\"first_name\":\"Aries\",\"last_name\":\"Adityanto\",\"deleted_by\":\"1\"}',NULL,'[\"user_id\",\"username\",\"email\",\"first_name\",\"last_name\",\"deleted_by\"]',1,'DELETED',1,'2025-10-16 06:38:19','ADMIN','System Administration','HIGH','{\"users\":[15]}'),(353,'users',16,'CREATE','Created new users record',NULL,'{\"user_id\":16,\"username\":\"adminmarketing\",\"email\":\"admin_marketing@optima.com\",\"roles\":[\"3\"],\"divisions\":[\"1\"],\"permissions\":[],\"created_by\":\"1\"}','[\"user_id\",\"username\",\"email\",\"roles\",\"divisions\",\"permissions\",\"created_by\"]',1,'CREATED',0,'2025-10-16 06:38:34','ADMIN','System Administration','MEDIUM','{\"users\":[16]}'),(354,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-16 06:52:09','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(355,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-16 06:52:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(356,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-17 01:40:01','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(357,'spk',62,'CREATE','Created new spk record',NULL,'{\"spk_id\":62,\"nomor_spk\":\"SPK\\/202510\\/006\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-17 03:00:54','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[62]}'),(358,'spk',62,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-10-17 03:46:45','SERVICE','Service Management','MEDIUM','{\"spk\":[62]}'),(359,'spk',63,'CREATE','Created new spk record',NULL,'{\"spk_id\":63,\"nomor_spk\":\"SPK\\/202510\\/007\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-17 05:14:05','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[63]}'),(360,'spk',64,'CREATE','Created new spk record',NULL,'{\"spk_id\":64,\"nomor_spk\":\"SPK\\/202510\\/008\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-17 06:50:49','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[64]}'),(361,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 06:57:23','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(362,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 06:57:50','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(363,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 06:57:55','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(364,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 07:05:26','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(365,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:05:29','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(366,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 07:05:57','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(367,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:06:12','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(368,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 07:10:06','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(369,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:13:08','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(370,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:13:36','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(371,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 07:18:50','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(372,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:19:19','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(373,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 07:19:23','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(374,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:19:26','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(375,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-17 07:21:26','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(376,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:21:33','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(377,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 07:23:17','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(378,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-17 07:23:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(379,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 07:24:04','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(380,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-17 07:24:07','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(381,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-17 07:24:16','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(382,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 07:24:19','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(383,'spk',65,'CREATE','Created new spk record',NULL,'{\"spk_id\":65,\"nomor_spk\":\"SPK\\/202510\\/009\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-17 09:19:45','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[65]}'),(384,'spk',66,'CREATE','Created new spk record',NULL,'{\"spk_id\":66,\"nomor_spk\":\"SPK\\/202510\\/003\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"49\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-17 09:25:08','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[66]}'),(385,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 09:25:46','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(386,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-17 09:25:52','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(387,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-17 09:29:39','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(388,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-18 01:46:18','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(389,'users',25,'CREATE','Created new users record',NULL,'{\"user_id\":25,\"username\":\"service_elektrik\",\"email\":\"service_elektrik@optima.com\",\"roles\":[\"8\"],\"divisions\":[\"2\"],\"permissions\":[],\"created_by\":\"1\"}','[\"user_id\",\"username\",\"email\",\"roles\",\"divisions\",\"permissions\",\"created_by\"]',1,'CREATED',0,'2025-10-18 01:48:36','ADMIN','System Administration','MEDIUM','{\"users\":[25]}'),(390,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-18 01:51:21','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(391,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-18 02:45:23','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(392,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-18 02:45:29','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(393,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-18 02:45:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(394,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-18 02:45:34','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(395,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-18 02:46:09','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(396,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-18 02:56:16','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(397,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-18 02:56:19','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(398,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-20 02:00:44','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(399,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-20 02:02:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(400,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 02:02:27','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(401,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-20 02:02:41','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(402,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-20 02:02:44','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(403,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-10-20 02:03:36','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(404,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-10-20 02:03:40','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(405,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 02:03:43','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(406,'spk',66,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',24,'UPDATED',0,'2025-10-20 02:24:00','SERVICE','Service Management','MEDIUM','{\"spk\":[66]}'),(407,'purchase_orders',0,'EXPORT','Export PO Progres CSV',NULL,NULL,NULL,24,'EXPORT',0,'2025-10-20 02:26:23','PURCHASING','PO Unit & Attachment - Progres','LOW',NULL),(408,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-20 02:26:49','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(409,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 02:26:53','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(410,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-20 05:57:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(411,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 05:57:05','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(412,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-20 06:05:35','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(413,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 06:05:38','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(414,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-20 07:20:34','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(415,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 07:20:36','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(416,'inventory_unit',0,'EXPORT','Export Unit Inventory CSV',NULL,NULL,NULL,24,'EXPORT',0,'2025-10-20 09:48:46','WAREHOUSE','Unit Inventory','LOW',NULL),(417,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-20 09:49:21','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(418,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 09:49:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(419,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-20 10:00:29','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(420,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-21 01:22:04','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(421,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-21 01:45:43','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(422,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-21 01:45:46','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(423,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 01:46:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(424,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 01:56:34','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(425,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 01:58:12','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(426,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 01:58:14','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(427,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:04:15','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(428,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:04:18','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(429,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:09:01','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(430,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:09:03','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(431,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:20:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(432,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:20:03','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(433,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:20:50','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(434,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:21:08','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(435,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:25:51','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(436,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:25:54','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(437,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:29:10','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(438,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:29:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(439,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:34:34','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(440,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-21 02:39:14','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(441,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:39:17','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(442,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:45:44','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(443,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:45:46','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(444,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 02:50:08','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(445,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-21 02:50:11','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(446,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-21 02:50:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(447,'users',25,'LOGIN','User logged in successfully',NULL,NULL,NULL,25,'LOGIN',0,'2025-10-21 02:50:23','USER_MANAGEMENT','User Session','LOW','{\"users\":[25]}'),(448,'users',25,'LOGOUT','User logged out',NULL,NULL,NULL,25,'LOGOUT',0,'2025-10-21 02:50:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[25]}'),(449,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 02:50:33','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(450,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 03:02:39','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(451,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 03:02:44','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(452,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 03:04:16','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(453,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-10-21 03:04:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(454,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-10-21 03:25:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(455,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-21 03:25:27','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(456,'spk',58,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-10-21 07:13:13','SERVICE','Service Management','MEDIUM','{\"spk\":[58]}'),(457,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-22 01:32:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(458,'customers',0,'EXPORT','Export Customer CSV',NULL,NULL,NULL,1,'EXPORT',0,'2025-10-22 02:45:58','MARKETING','Customer Management','LOW',NULL),(459,'kontrak',57,'UPDATE','Kontrak updated: test/1/1/5 (Client: Test - Lokasi Utama)','{\"customer_location_id\":\"5\",\"nilai_total\":\"278000000.00\",\"catatan\":null}','{\"customer_location_id\":3,\"nilai_total\":\"278000000\",\"catatan\":\"\"}','[\"customer_location_id\",\"nilai_total\",\"catatan\"]',1,'UPDATED',0,'2025-10-22 03:10:12','MARKETING','Data Kontrak','MEDIUM','{\"kontrak\":[57]}'),(460,'customers',10,'CREATE','Customer created from Customer Management',NULL,'{\"customer_code\":\"CUST-20251022-059\",\"customer_name\":\"PT JASA MANA LAGI\",\"area_id\":\"8\",\"is_active\":\"1\"}','[\"customer_code\",\"customer_name\",\"area_id\",\"is_active\"]',1,'CREATED',0,'2025-10-22 09:18:56','MARKETING','Customer Management','MEDIUM','{\"customers\":[10],\"customer_locations\":[15]}'),(461,'customer_locations',15,'CREATE','Primary location created for customer',NULL,'{\"customer_id\":10,\"location_name\":\"Head Office\",\"location_type\":\"HEAD_OFFICE\",\"address\":\"EROPA JAYA AMERIKA, LONDON, SINGAPURE, JAKARTA BEKASI, JAWA BRAT\",\"city\":\"Jakarta\",\"province\":\"DKI Jakarta\",\"postal_code\":\"17320\",\"contact_person\":\"itsmils\",\"pic_position\":\"\",\"phone\":\"082136033596\",\"email\":\"itsupport@sml.co.id\",\"notes\":\"awdaw\",\"is_primary\":1,\"is_active\":1}','[\"customer_id\",\"location_name\",\"location_type\",\"address\",\"city\",\"province\",\"postal_code\",\"contact_person\",\"pic_position\",\"phone\",\"email\",\"notes\",\"is_primary\",\"is_active\"]',1,'CREATED',0,'2025-10-22 09:18:56','MARKETING','Customer Management','LOW','{\"customers\":[10],\"customer_locations\":[15]}'),(462,'customers',11,'CREATE','Customer created from Customer Management',NULL,'{\"customer_code\":\"CUST-20251022-583\",\"customer_name\":\"PT JASA ADA AJA LAGI\",\"area_id\":\"12\",\"is_active\":\"1\"}','[\"customer_code\",\"customer_name\",\"area_id\",\"is_active\"]',1,'CREATED',0,'2025-10-22 09:23:15','MARKETING','Customer Management','MEDIUM','{\"customers\":[11],\"customer_locations\":[16]}'),(463,'customer_locations',16,'CREATE','Primary location created for customer',NULL,'{\"customer_id\":11,\"location_name\":\"WORKSHOP 1\",\"location_type\":\"HEAD_OFFICE\",\"address\":\"Jalan jalan cikarang jaya raya\",\"city\":\"Jakarta\",\"province\":\"DKI Jakarta\",\"postal_code\":\"17320\",\"contact_person\":\"itsmils\",\"pic_position\":\"\",\"phone\":\"082136033596\",\"email\":\"itsupport@sml.co.id\",\"notes\":\"awdaw\",\"is_primary\":1,\"is_active\":1}','[\"customer_id\",\"location_name\",\"location_type\",\"address\",\"city\",\"province\",\"postal_code\",\"contact_person\",\"pic_position\",\"phone\",\"email\",\"notes\",\"is_primary\",\"is_active\"]',1,'CREATED',0,'2025-10-22 09:23:15','MARKETING','Customer Management','LOW','{\"customers\":[11],\"customer_locations\":[16]}'),(464,'customers',12,'CREATE','Customer created from Customer Management',NULL,'{\"customer_code\":\"CUST-20251022-049\",\"customer_name\":\"PT KORUMA ADAJASA\",\"area_id\":\"14\",\"is_active\":\"1\"}','[\"customer_code\",\"customer_name\",\"area_id\",\"is_active\"]',1,'CREATED',0,'2025-10-22 09:30:29','MARKETING','Customer Management','MEDIUM','{\"customers\":[12],\"customer_locations\":[17]}'),(465,'customer_locations',17,'CREATE','Primary location created for customer',NULL,'{\"customer_id\":12,\"location_name\":\"Head Office\",\"location_type\":\"HEAD_OFFICE\",\"address\":\"PERMATA NUSA INDAH BLOK A 1IUNAWUDA\",\"city\":\"Jakarta\",\"province\":\"DKI Jakarta\",\"postal_code\":\"17320\",\"contact_person\":\"itsmils\",\"pic_position\":\"\",\"phone\":\"082136033596\",\"email\":\"itsupport@sml.co.id\",\"notes\":\"\",\"is_primary\":1,\"is_active\":1}','[\"customer_id\",\"location_name\",\"location_type\",\"address\",\"city\",\"province\",\"postal_code\",\"contact_person\",\"pic_position\",\"phone\",\"email\",\"notes\",\"is_primary\",\"is_active\"]',1,'CREATED',0,'2025-10-22 09:30:29','MARKETING','Customer Management','LOW','{\"customers\":[12],\"customer_locations\":[17]}'),(466,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-23 01:38:36','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(467,'spk',67,'CREATE','Created new spk record',NULL,'{\"spk_id\":67,\"nomor_spk\":\"SPK\\/202510\\/004\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"63\",\"kontrak_spesifikasi_id\":\"52\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',NULL,'CREATED',0,'2025-10-23 04:42:39','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[67]}'),(468,'spk',68,'CREATE','Created new spk record',NULL,'{\"spk_id\":68,\"nomor_spk\":\"SPK\\/202510\\/005\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"48\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-23 04:45:39','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[68]}'),(469,'spk',69,'CREATE','Created new spk record',NULL,'{\"spk_id\":69,\"nomor_spk\":\"SPK\\/202510\\/006\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"48\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-23 06:31:05','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[69]}'),(470,'spk',70,'CREATE','Created new spk record',NULL,'{\"spk_id\":70,\"nomor_spk\":\"SPK\\/202510\\/007\",\"jenis_spk\":\"ATTACHMENT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"51\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',NULL,'CREATED',0,'2025-10-23 06:34:07','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[70]}'),(471,'spk',71,'CREATE','Created new spk record',NULL,'{\"spk_id\":71,\"nomor_spk\":\"SPK\\/202510\\/008\",\"jenis_spk\":\"ATTACHMENT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"51\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-23 06:36:38','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[71]}'),(472,'customers',0,'EXPORT','Export Customer CSV',NULL,NULL,NULL,1,'EXPORT',0,'2025-10-23 06:38:26','MARKETING','Customer Management','LOW',NULL),(473,'customers',0,'EXPORT','Export Customer CSV',NULL,NULL,NULL,1,'EXPORT',0,'2025-10-23 07:55:49','MARKETING','Customer Management','LOW',NULL),(474,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-23 07:56:46','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(475,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-23 08:05:01','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(476,'spk',71,'UPDATE','Updated spk record','{\"status\":\"SUBMITTED\"}','{\"status\":\"IN_PROGRESS\"}','[\"status\"]',1,'UPDATED',0,'2025-10-23 09:48:47','SERVICE','Service Management','MEDIUM','{\"spk\":[71]}'),(477,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-24 01:43:47','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(478,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-10-24 04:29:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(479,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-10-24 04:29:21','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(480,'delivery_instruction',143,'UPDATE','Updated delivery_instruction record','{\"berangkat_tanggal_approve\":null,\"diperbarui_pada\":\"2025-09-30 12:20:53\",\"catatan_berangkat\":null,\"status_di\":\"SIAP_KIRIM\"}','{\"berangkat_tanggal_approve\":\"2025-10-24\",\"diperbarui_pada\":\"2025-10-24 07:58:46\",\"catatan_berangkat\":\"AWDAWDADASD\",\"status_di\":\"DALAM_PERJALANAN\"}','[\"berangkat_tanggal_approve\",\"diperbarui_pada\",\"catatan_berangkat\",\"status_di\"]',1,'UPDATED',0,'2025-10-24 07:58:46','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[143]}'),(481,'spk',72,'CREATE','Created new spk record',NULL,'{\"spk_id\":72,\"nomor_spk\":\"SPK\\/202510\\/009\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"48\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-24 08:33:36','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[72]}'),(482,'spk',73,'CREATE','Created new spk record',NULL,'{\"spk_id\":73,\"nomor_spk\":\"SPK\\/202510\\/010\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"48\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-24 08:43:33','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[73]}'),(483,'spk',74,'CREATE','Created new spk record',NULL,'{\"spk_id\":74,\"nomor_spk\":\"SPK\\/202510\\/011\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"48\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-24 09:29:25','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[74]}'),(484,'spk',75,'CREATE','Created new spk record',NULL,'{\"spk_id\":75,\"nomor_spk\":\"SPK\\/202510\\/012\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"48\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-24 09:35:54','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[75]}'),(485,'spk',76,'CREATE','Created new spk record',NULL,'{\"spk_id\":76,\"nomor_spk\":\"SPK\\/202510\\/013\",\"jenis_spk\":\"UNIT\",\"kontrak_id\":\"57\",\"kontrak_spesifikasi_id\":\"48\",\"jumlah_unit\":1}','[\"spk_id\",\"nomor_spk\",\"jenis_spk\",\"kontrak_id\",\"kontrak_spesifikasi_id\",\"jumlah_unit\"]',1,'CREATED',0,'2025-10-24 09:41:11','MARKETING','App\\s\\marketing Management','MEDIUM','{\"spk\":[76]}'),(486,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-10 05:21:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(487,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-17 01:28:47','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(488,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-18 01:38:47','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(489,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-19 01:25:16','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(490,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-19 03:00:14','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(491,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-19 03:00:45','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(492,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-19 03:12:12','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(493,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-19 08:51:08','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(494,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-19 08:51:09','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(495,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-19 09:07:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(496,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-19 09:07:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(497,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-19 09:07:53','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(498,'users',24,'UPDATE','Updated users record','{\"user_id\":null,\"is_active\":\"1\",\"updated_by\":null,\"password_changed\":null}','{\"user_id\":\"24\",\"is_active\":1,\"updated_by\":\"1\",\"password_changed\":\"No\"}','[\"user_id\",\"is_active\",\"updated_by\",\"password_changed\"]',1,'UPDATED',0,'2025-11-19 10:10:24','ADMIN','System Administration','MEDIUM','{\"users\":[24]}'),(499,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-20 03:39:03','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(500,'users',24,'UPDATE','Updated users record','{\"user_id\":null,\"is_active\":\"1\",\"updated_by\":null,\"password_changed\":null}','{\"user_id\":\"24\",\"is_active\":1,\"updated_by\":\"1\",\"password_changed\":\"No\"}','[\"user_id\",\"is_active\",\"updated_by\",\"password_changed\"]',1,'UPDATED',0,'2025-11-20 03:48:49','ADMIN','System Administration','MEDIUM','{\"users\":[24]}'),(501,'users',24,'UPDATE','Updated users record','{\"action\":null,\"changed_by\":null,\"timestamp\":null}','{\"action\":\"password_changed\",\"changed_by\":\"1\",\"timestamp\":\"2025-11-20 10:49:05\"}','[\"action\",\"changed_by\",\"timestamp\"]',1,'UPDATED',0,'2025-11-20 03:49:05','ADMIN','System Administration','MEDIUM','{\"users\":[24]}'),(502,'users',24,'UPDATE','Updated users record','{\"action\":null,\"changed_by\":null,\"timestamp\":null}','{\"action\":\"password_changed\",\"changed_by\":\"1\",\"timestamp\":\"2025-11-20 10:52:21\"}','[\"action\",\"changed_by\",\"timestamp\"]',1,'UPDATED',0,'2025-11-20 03:52:21','ADMIN','System Administration','MEDIUM','{\"users\":[24]}'),(503,'users',24,'UPDATE','Updated users record','{\"action\":null,\"changed_by\":null,\"timestamp\":null}','{\"action\":\"password_changed\",\"changed_by\":\"1\",\"timestamp\":\"2025-11-20 10:54:18\"}','[\"action\",\"changed_by\",\"timestamp\"]',1,'UPDATED',0,'2025-11-20 03:54:18','ADMIN','System Administration','MEDIUM','{\"users\":[24]}'),(504,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-20 03:54:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(505,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-20 03:54:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(506,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-11-20 03:55:02','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(507,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-11-20 04:23:25','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(508,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-11-20 04:23:37','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(509,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-21 02:10:24','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(510,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-11-21 04:34:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(511,'delivery_instruction',143,'UPDATE','Updated delivery_instruction record','{\"sampai_tanggal_approve\":null,\"diperbarui_pada\":\"2025-10-24 07:58:46\",\"catatan_sampai\":null,\"status_di\":\"DALAM_PERJALANAN\"}','{\"sampai_tanggal_approve\":\"2025-11-21\",\"diperbarui_pada\":\"2025-11-21 13:21:53\",\"catatan_sampai\":\"basanksadawd\",\"status_di\":\"SAMPAI_LOKASI\"}','[\"sampai_tanggal_approve\",\"diperbarui_pada\",\"catatan_sampai\",\"status_di\"]',24,'UPDATED',0,'2025-11-21 06:21:53','OPERATIONAL','Operational Data','MEDIUM','{\"delivery_instruction\":[143]}'),(512,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-21 13:12:56','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(513,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 01:27:06','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(514,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-11-22 01:52:23','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(515,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-11-22 01:52:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(516,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-22 01:56:53','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(517,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 01:59:26','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(518,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-11-22 01:59:34','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(519,'users',24,'LOGOUT','User logged out',NULL,NULL,NULL,24,'LOGOUT',0,'2025-11-22 02:01:01','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(520,'users',16,'LOGIN','User logged in successfully',NULL,NULL,NULL,16,'LOGIN',0,'2025-11-22 02:01:22','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(521,'users',16,'LOGOUT','User logged out',NULL,NULL,NULL,16,'LOGOUT',0,'2025-11-22 02:01:46','USER_MANAGEMENT','User Session','LOW','{\"users\":[16]}'),(522,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 03:17:27','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(523,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-22 03:17:30','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(524,'users',24,'LOGIN','User logged in successfully',NULL,NULL,NULL,24,'LOGIN',0,'2025-11-22 03:17:33','USER_MANAGEMENT','User Session','LOW','{\"users\":[24]}'),(525,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-22 05:15:20','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(526,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 05:15:21','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(527,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 08:46:43','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(528,'users',1,'','User OTP_TOGGLE',NULL,NULL,NULL,1,'OTP_TOGGLE',0,'2025-11-22 08:52:00','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(529,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-22 08:52:07','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(530,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 08:52:53','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(531,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-22 10:30:41','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(532,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 10:32:47','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(533,'users',1,'LOGOUT','User logged out',NULL,NULL,NULL,1,'LOGOUT',0,'2025-11-22 10:39:37','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(534,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-22 10:55:32','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}'),(535,'users',29,'','User USER_APPROVED',NULL,NULL,NULL,1,'USER_APPROVED',0,'2025-11-22 12:32:02','USER_MANAGEMENT','User Session','LOW','{\"users\":[29]}'),(536,'users',29,'','User USER_APPROVED',NULL,NULL,NULL,1,'USER_APPROVED',0,'2025-11-22 12:32:02','USER_MANAGEMENT','User Session','LOW','{\"users\":[29]}'),(537,'users',29,'LOGIN','User logged in successfully',NULL,NULL,NULL,29,'LOGIN',0,'2025-11-22 12:32:21','USER_MANAGEMENT','User Session','LOW','{\"users\":[29]}'),(538,'user_permissions',25,'UPDATE','Custom permissions updated',NULL,NULL,NULL,1,'UPDATE',0,'2025-11-22 15:36:24','ADMIN','User Management','MEDIUM','{\"user_permissions\":[25]}'),(539,'user_permissions',25,'DELETE','Custom permission removed',NULL,NULL,NULL,1,'DELETE',0,'2025-11-22 15:36:38','ADMIN','User Management','LOW','{\"user_permissions\":[25]}'),(540,'user_permissions',25,'DELETE','Custom permission removed',NULL,NULL,NULL,1,'DELETE',0,'2025-11-22 15:36:41','ADMIN','User Management','LOW','{\"user_permissions\":[25]}'),(541,'user_permissions',25,'DELETE','Custom permission removed',NULL,NULL,NULL,1,'DELETE',0,'2025-11-22 15:36:44','ADMIN','User Management','LOW','{\"user_permissions\":[25]}'),(542,'user_permissions',25,'DELETE','Custom permission removed',NULL,NULL,NULL,1,'DELETE',0,'2025-11-22 15:36:47','ADMIN','User Management','LOW','{\"user_permissions\":[25]}'),(543,'user_permissions',25,'DELETE','Custom permission removed',NULL,NULL,NULL,1,'DELETE',0,'2025-11-22 15:36:50','ADMIN','User Management','LOW','{\"user_permissions\":[25]}'),(544,'user_permissions',25,'DELETE','Custom permission removed',NULL,NULL,NULL,1,'DELETE',0,'2025-11-22 15:36:53','ADMIN','User Management','LOW','{\"user_permissions\":[25]}'),(545,'user_permissions',25,'DELETE','Custom permission removed',NULL,NULL,NULL,1,'DELETE',0,'2025-11-22 15:36:57','ADMIN','User Management','LOW','{\"user_permissions\":[25]}'),(546,'users',1,'LOGIN','User logged in successfully',NULL,NULL,NULL,1,'LOGIN',0,'2025-11-28 04:35:34','USER_MANAGEMENT','User Session','LOW','{\"users\":[1]}');
/*!40000 ALTER TABLE `system_activity_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `system_activity_log_backup`
--

DROP TABLE IF EXISTS `system_activity_log_backup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_activity_log_backup` (
  `id` int NOT NULL DEFAULT '0',
  `table_name` varchar(64) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Target table name (kontrak, spk, inventory_unit, etc)',
  `record_id` int unsigned NOT NULL COMMENT 'ID of the affected record',
  `action_type` enum('CREATE','UPDATE','DELETE','ASSIGN','UNASSIGN','APPROVE','REJECT','COMPLETE','CANCEL') COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Type of action performed',
  `action_description` varchar(255) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Brief description of what happened',
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Previous values (only changed fields)',
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'New values (only changed fields)',
  `affected_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'List of fields that were changed',
  `user_id` int unsigned DEFAULT NULL COMMENT 'FK to users.id',
  `session_id` varchar(128) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Session identifier for tracking',
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'User IP address',
  `user_agent` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Browser/device info (truncated)',
  `request_method` enum('GET','POST','PUT','DELETE','PATCH') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'HTTP method used',
  `request_url` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Endpoint that triggered this action',
  `related_kontrak_id` int unsigned DEFAULT NULL COMMENT 'Related kontrak if applicable',
  `related_spk_id` int unsigned DEFAULT NULL COMMENT 'Related SPK if applicable',
  `related_di_id` int unsigned DEFAULT NULL COMMENT 'Related DI if applicable',
  `workflow_stage` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Current business stage',
  `is_critical` tinyint(1) DEFAULT '0' COMMENT 'Mark critical business actions',
  `execution_time_ms` int unsigned DEFAULT NULL COMMENT 'Time taken to execute action (milliseconds)',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `module_name` enum('PURCHASING','WAREHOUSE','MARKETING','SERVICE','OPERATIONAL','ACCOUNTING','PERIZINAN','ADMIN','DASHBOARD','REPORTS','SETTINGS','USER_MANAGEMENT') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Application module where activity occurred',
  `feature_name` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Specific feature/page within module',
  `business_impact` enum('LOW','MEDIUM','HIGH','CRITICAL') COLLATE utf8mb4_general_ci DEFAULT 'LOW' COMMENT 'Business impact level',
  `compliance_relevant` tinyint(1) DEFAULT '0' COMMENT 'Relevant for compliance/audit',
  `financial_impact` decimal(15,2) DEFAULT NULL COMMENT 'Financial impact of this activity',
  `related_purchase_order_id` int unsigned DEFAULT NULL COMMENT 'Related PO for purchasing module',
  `related_vendor_id` int unsigned DEFAULT NULL COMMENT 'Related vendor/supplier',
  `related_customer_id` int unsigned DEFAULT NULL COMMENT 'Related customer',
  `related_invoice_id` int unsigned DEFAULT NULL COMMENT 'Related invoice for accounting',
  `related_payment_id` int unsigned DEFAULT NULL COMMENT 'Related payment record',
  `related_permit_id` int unsigned DEFAULT NULL COMMENT 'Related permit for perizinan',
  `related_warehouse_id` int unsigned DEFAULT NULL COMMENT 'Related warehouse location',
  `device_type` enum('DESKTOP','MOBILE','TABLET','API') COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Device type used',
  `browser_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Browser name',
  `operating_system` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Operating system',
  CONSTRAINT `system_activity_log_backup_chk_1` CHECK (json_valid(`old_values`)),
  CONSTRAINT `system_activity_log_backup_chk_2` CHECK (json_valid(`new_values`)),
  CONSTRAINT `system_activity_log_backup_chk_3` CHECK (json_valid(`affected_fields`))
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `system_activity_log_old` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Username yang melakukan aktivitas',
  `user_id` int unsigned DEFAULT NULL COMMENT 'FK ke users table',
  `action_type` enum('CREATE','READ','UPDATE','DELETE','PRINT','DOWNLOAD','LOGIN','LOGOUT') COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Jenis aktivitas',
  `table_name` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nama tabel yang diakses (kontrak, spk, inventory, dll)',
  `record_id` int unsigned DEFAULT NULL COMMENT 'ID record yang diakses',
  `description` text COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Deskripsi lengkap aktivitas yang dilakukan',
  `file_name` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nama file yang di-print/download',
  `file_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Jenis file (PDF, Excel, Word, dll)',
  `module_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Module/Menu yang diakses (Marketing, Service, dll)',
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'IP address user',
  `user_agent` text COLLATE utf8mb4_general_ci COMMENT 'Browser info',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu aktivitas',
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipe_ban` (
  `id_ban` int NOT NULL AUTO_INCREMENT,
  `tipe_ban` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipe_mast` (
  `id_mast` int NOT NULL,
  `tipe_mast` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tinggi_mast` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Contoh: 4500mm atau 4.5m',
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipe_unit` (
  `id_tipe_unit` int NOT NULL,
  `tipe` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `id_departemen` int DEFAULT NULL,
  PRIMARY KEY (`id_tipe_unit`),
  KEY `id_departemen` (`id_departemen`),
  CONSTRAINT `tipe_unit_ibfk_1` FOREIGN KEY (`id_departemen`) REFERENCES `departemen` (`id_departemen`)
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tujuan_perintah_kerja` (
  `id` int NOT NULL AUTO_INCREMENT,
  `jenis_perintah_id` int NOT NULL,
  `kode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `nama` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text COLLATE utf8mb4_general_ci,
  `aktif` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_replacement_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `di_id` int NOT NULL,
  `old_unit_id` int unsigned NOT NULL,
  `new_unit_id` int unsigned NOT NULL,
  `kontrak_id` int NOT NULL,
  `stage` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `replacement_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `replaced_by` int DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
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
-- Table structure for table `unit_status_log`
--

DROP TABLE IF EXISTS `unit_status_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_status_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `inventory_unit_id` int NOT NULL,
  `old_status_id` int DEFAULT NULL,
  `new_status_id` int NOT NULL,
  `reason` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `triggered_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reference_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_unit_id` (`inventory_unit_id`),
  KEY `idx_status_change` (`old_status_id`,`new_status_id`),
  KEY `idx_triggered_by` (`triggered_by`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unit_status_log`
--

LOCK TABLES `unit_status_log` WRITE;
/*!40000 ALTER TABLE `unit_status_log` DISABLE KEYS */;
INSERT INTO `unit_status_log` VALUES (1,16,2,4,'Unit assigned to SPK #51','SPK_SERVICE',51,'2025-09-27 14:40:23','SYSTEM'),(2,16,4,5,'SPK #SPK/202509/013 status changed to READY','SPK_READY',51,'2025-09-27 14:40:54','SYSTEM'),(3,16,5,4,'SPK #SPK/202509/013 status reverted from READY to IN_PROGRESS','SPK_REVERT',51,'2025-09-27 14:47:53','SYSTEM'),(4,16,4,5,'SPK #SPK/202509/013 completed all stages - status changed to READY','SPK_COMPLETE',51,'2025-09-27 14:48:01','SYSTEM'),(5,16,5,4,'SPK #SPK/202509/013 status reverted from READY to IN_PROGRESS','SPK_REVERT',51,'2025-09-27 14:48:40','SYSTEM');
/*!40000 ALTER TABLE `unit_status_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unit_workflow_log`
--

DROP TABLE IF EXISTS `unit_workflow_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unit_workflow_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `unit_id` int unsigned NOT NULL,
  `di_id` int NOT NULL,
  `stage` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_perintah` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `old_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `new_status` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int DEFAULT NULL,
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
-- Temporary view structure for view `unit_workflow_status`
--

DROP TABLE IF EXISTS `unit_workflow_status`;
/*!50001 DROP VIEW IF EXISTS `unit_workflow_status`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `unit_workflow_status` AS SELECT 
 1 AS `id_inventory_unit`,
 1 AS `no_unit`,
 1 AS `current_status`,
 1 AS `workflow_status`,
 1 AS `di_workflow_id`,
 1 AS `kontrak_id`,
 1 AS `no_kontrak`,
 1 AS `pelanggan`,
 1 AS `nomor_di`,
 1 AS `di_status`,
 1 AS `jenis_perintah`,
 1 AS `tujuan_perintah`,
 1 AS `workflow_category`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `user_all_permissions`
--

DROP TABLE IF EXISTS `user_all_permissions`;
/*!50001 DROP VIEW IF EXISTS `user_all_permissions`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `user_all_permissions` AS SELECT 
 1 AS `user_id`,
 1 AS `username`,
 1 AS `email`,
 1 AS `first_name`,
 1 AS `last_name`,
 1 AS `division_name`,
 1 AS `permission_id`,
 1 AS `permission_name`,
 1 AS `permission_key`,
 1 AS `module`,
 1 AS `category`,
 1 AS `source_type`,
 1 AS `source_name`,
 1 AS `granted`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `user_otp`
--

DROP TABLE IF EXISTS `user_otp`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_otp` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `otp_code` varchar(6) COLLATE utf8mb4_general_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `attempts` int NOT NULL DEFAULT '0',
  `max_attempts` int NOT NULL DEFAULT '3',
  `is_verified` tinyint(1) NOT NULL DEFAULT '0',
  `expires_at` datetime NOT NULL,
  `verified_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_email` (`email`),
  KEY `idx_expires_at` (`expires_at`),
  KEY `idx_is_verified` (`is_verified`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_otp`
--

LOCK TABLES `user_otp` WRITE;
/*!40000 ALTER TABLE `user_otp` DISABLE KEYS */;
INSERT INTO `user_otp` VALUES (4,1,'227166','itsupport@sml.co.id','::1',0,3,1,'2025-11-28 11:40:15','2025-11-28 11:35:34','2025-11-28 11:35:15');
/*!40000 ALTER TABLE `user_otp` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_permissions`
--

DROP TABLE IF EXISTS `user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_permissions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `division_id` int DEFAULT NULL,
  `granted` tinyint(1) DEFAULT '1',
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assigned_by` int DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_temporary` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_permissions_user_id` (`user_id`),
  KEY `idx_user_permissions_permission_id` (`permission_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_permissions`
--

LOCK TABLES `user_permissions` WRITE;
/*!40000 ALTER TABLE `user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_roles` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `division_id` int DEFAULT NULL,
  `assigned_by` int DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user_roles_user_id` (`user_id`),
  KEY `idx_user_roles_role_id` (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,16,3,0,1,'2025-10-15 23:38:34',NULL,1,'2025-10-16 06:38:34','2025-10-16 06:39:40'),(35,1,1,NULL,1,'2025-08-06 16:26:28',NULL,1,'2025-08-06 23:26:28','2025-10-16 06:45:40'),(100,19,6,2,NULL,'2025-10-17 06:55:14',NULL,1,'2025-10-17 06:55:14','2025-10-17 06:55:14'),(101,23,6,1,1,'2025-10-17 02:40:06',NULL,1,'2025-10-17 09:40:06','2025-10-17 09:40:06'),(106,25,8,2,1,'2025-11-16 23:46:18',NULL,1,'2025-11-17 06:46:18','2025-11-17 06:46:18'),(111,24,7,1,1,'2025-11-20 03:48:49',NULL,1,'2025-11-20 03:48:49','2025-11-20 03:48:49'),(113,29,33,7,1,'2025-11-22 12:32:02',NULL,1,'2025-11-22 12:32:02','2025-11-22 12:32:02');
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_sessions`
--

DROP TABLE IF EXISTS `user_sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_sessions` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `session_id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `device_id` varchar(64) COLLATE utf8mb4_general_ci NOT NULL,
  `device_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `device_type` enum('desktop','mobile','tablet') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'desktop',
  `browser` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `os` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `user_agent` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `last_activity` datetime NOT NULL,
  `login_at` datetime NOT NULL,
  `logout_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_session_id` (`session_id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_device_id` (`device_id`),
  KEY `idx_is_active` (`is_active`),
  KEY `idx_last_activity` (`last_activity`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_sessions`
--

LOCK TABLES `user_sessions` WRITE;
/*!40000 ALTER TABLE `user_sessions` DISABLE KEYS */;
INSERT INTO `user_sessions` VALUES (1,1,'cf5ef948de6908c5db165db08849397b','cb486bc97b5387f24515d2ecb2dbf5b030dc91bed82217050b06183d3b2969bd','Desktop - Linux - Firefox','desktop','Firefox 145','Linux','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:145.0) Gecko/20100101 Firefox/145.0',0,'2025-11-22 10:17:29','2025-11-22 10:17:27','2025-11-22 10:17:30','2025-11-22 10:17:27','2025-11-22 10:17:30'),(2,24,'0b8a7b30f363b7bf891db4607fbbdb90','cb486bc97b5387f24515d2ecb2dbf5b030dc91bed82217050b06183d3b2969bd','Desktop - Linux - Firefox','desktop','Firefox 145','Linux','127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:145.0) Gecko/20100101 Firefox/145.0',0,'2025-11-22 12:15:28','2025-11-22 10:17:33','2025-11-22 15:46:43','2025-11-22 10:17:33','2025-11-22 15:46:43'),(3,1,'3338a08a10c95c75364092543711fd59','8300b504bb67165f1086744bcd32305d7e9bb8919c98ebbbcb7e16816f8cdf17','Desktop - Linux - Edge (Chromium)','desktop','Edge (Chromium) 142','Linux','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-22 12:15:22','2025-11-22 12:15:21','2025-11-22 15:46:43','2025-11-22 12:15:21','2025-11-22 15:46:43'),(4,1,'5fcc4d93d99560b436c7ada5c341f711','8300b504bb67165f1086744bcd32305d7e9bb8919c98ebbbcb7e16816f8cdf17','Desktop - Linux - Edge (Chromium)','desktop','Edge (Chromium) 142','Linux','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-22 15:51:51','2025-11-22 15:46:43','2025-11-22 17:55:32','2025-11-22 15:46:43','2025-11-22 17:55:32'),(5,1,'8f2b07c3dc75a7f2a0ed1d6100f7edbf','8300b504bb67165f1086744bcd32305d7e9bb8919c98ebbbcb7e16816f8cdf17','Desktop - Linux - Edge (Chromium)','desktop','Edge (Chromium) 142','Linux','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-22 17:30:37','2025-11-22 15:52:53','2025-11-22 19:31:05','2025-11-22 15:52:53','2025-11-22 19:31:05'),(6,1,'c88861f146cb2fed797b3b12ff1b6c72','8300b504bb67165f1086744bcd32305d7e9bb8919c98ebbbcb7e16816f8cdf17','Desktop - Linux - Edge (Chromium)','desktop','Edge (Chromium) 142','Linux','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-22 17:39:17','2025-11-22 17:32:47','2025-11-22 19:39:22','2025-11-22 17:32:47','2025-11-22 19:39:22'),(7,1,'17892727522c243702d3ab6ad3d1ac8f','8300b504bb67165f1086744bcd32305d7e9bb8919c98ebbbcb7e16816f8cdf17','Desktop - Linux - Edge (Chromium)','desktop','Edge (Chromium) 142','Linux','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-22 18:09:01','2025-11-22 17:55:32','2025-11-22 20:09:11','2025-11-22 17:55:32','2025-11-22 20:09:11'),(8,29,'d5d06452a9ca20718bcc388d92ca8be9','8300b504bb67165f1086744bcd32305d7e9bb8919c98ebbbcb7e16816f8cdf17','Desktop - Linux - Edge (Chromium)','desktop','Edge (Chromium) 142','Linux','::1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',0,'2025-11-22 23:17:22','2025-11-22 19:32:21','2025-11-28 11:35:35','2025-11-22 19:32:21','2025-11-28 11:35:35'),(9,1,'8b62d3ea2ebe37eb7e2008984c0410d0','bd9e87e66e77c7de4856314b38585da4988c9456149005186df163788154c8f1','Desktop - Windows - Edge (Chromium)','desktop','Edge (Chromium) 142','Windows 10/11','::1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/142.0.0.0 Safari/537.36 Edg/142.0.0.0',1,'2025-11-28 11:39:19','2025-11-28 11:35:34',NULL,'2025-11-28 11:35:34','2025-11-28 11:39:19');
/*!40000 ALTER TABLE `user_sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `division_id` int DEFAULT NULL,
  `employee_id` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_super_admin` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `last_login` timestamp NULL DEFAULT NULL,
  `email_verified` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `otp_enabled_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `bio` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `idx_otp_enabled` (`otp_enabled`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'superadmin','itsupport@sml.co.id','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','Aries','Adityanto','+62 812-3456-7890','uploads/avatars/avatar_1_1760691065.png',7,NULL,'System Administrator',1,1,'2025-10-17 09:05:08','2025-10-17 09:05:08',NULL,1,'2025-11-22 15:52:00','2025-08-05 00:01:57','2025-11-22 10:39:37','itsupport@sml.co.id'),(16,'adminmarketing','admin_marketing@optima.com','$2y$10$lip46JrPYidQG.OAHqfZ0.p6rVjaNKYfg3Q/c/A9L.FCYm1a00yPG','Aries','Adityanto ganteng','082136033596',NULL,0,NULL,'Marketing Staff',0,1,NULL,NULL,NULL,0,NULL,'2025-10-15 23:38:34','2025-11-22 02:01:46','Marketing staff responsible for customer management and sales activities.'),(24,'service_diesel','service_diesel@optima.com','$2y$10$6JpxvjJ2XtYPFG6Y5faSo.9qLwlNdD71zLW/xKloySjVN0SEa4AQW','Admin','Diesel','082112312312',NULL,1,NULL,NULL,0,1,NULL,NULL,NULL,0,NULL,'2025-10-17 02:40:45','2025-11-22 02:01:01',NULL),(25,'service_elektrik','service_elektrik@optima.com','$2y$10$SXxMdHlahC2CG3gF1DnX7e9tsZf8Q3SGb5B70hs.imIBMDMGERtti','Admin','Elektrik','08821888888888',NULL,NULL,NULL,NULL,0,1,NULL,NULL,NULL,0,NULL,'2025-10-17 18:48:36','2025-11-16 23:46:18',NULL),(29,'kanebokering','kanebokering15@gmail.com','$2y$10$LqwKWbueWS9mXJfWnvMDqOau7OXd/aPC1JXO/L.zKEriNMgrqz6Di','kanebo','kering','+6282136033596',NULL,7,NULL,'Head of Divisi',0,1,NULL,'2025-11-22 11:41:04',NULL,0,NULL,'2025-11-22 11:39:23','2025-11-22 12:32:02',NULL);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `v_activity_log_relations`
--

DROP TABLE IF EXISTS `v_activity_log_relations`;
/*!50001 DROP VIEW IF EXISTS `v_activity_log_relations`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_activity_log_relations` AS SELECT 
 1 AS `id`,
 1 AS `table_name`,
 1 AS `record_id`,
 1 AS `action_type`,
 1 AS `action_description`,
 1 AS `module_name`,
 1 AS `submenu_item`,
 1 AS `workflow_stage`,
 1 AS `business_impact`,
 1 AS `user_id`,
 1 AS `created_at`,
 1 AS `related_entities`,
 1 AS `related_kontrak`,
 1 AS `related_spk`,
 1 AS `related_di`,
 1 AS `related_po`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_spk_fabrikasi`
--

DROP TABLE IF EXISTS `v_spk_fabrikasi`;
/*!50001 DROP VIEW IF EXISTS `v_spk_fabrikasi`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_spk_fabrikasi` AS SELECT 
 1 AS `spk_id`,
 1 AS `nomor_spk`,
 1 AS `unit_index`,
 1 AS `fabrikasi_attachment_id`,
 1 AS `fabrikasi_mekanik`,
 1 AS `fabrikasi_estimasi_mulai`,
 1 AS `fabrikasi_estimasi_selesai`,
 1 AS `fabrikasi_tanggal_approve`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_spk_painting`
--

DROP TABLE IF EXISTS `v_spk_painting`;
/*!50001 DROP VIEW IF EXISTS `v_spk_painting`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_spk_painting` AS SELECT 
 1 AS `spk_id`,
 1 AS `nomor_spk`,
 1 AS `unit_index`,
 1 AS `painting_mekanik`,
 1 AS `painting_estimasi_mulai`,
 1 AS `painting_estimasi_selesai`,
 1 AS `painting_tanggal_approve`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_spk_pdi`
--

DROP TABLE IF EXISTS `v_spk_pdi`;
/*!50001 DROP VIEW IF EXISTS `v_spk_pdi`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_spk_pdi` AS SELECT 
 1 AS `spk_id`,
 1 AS `nomor_spk`,
 1 AS `unit_index`,
 1 AS `pdi_mekanik`,
 1 AS `pdi_estimasi_mulai`,
 1 AS `pdi_estimasi_selesai`,
 1 AS `pdi_tanggal_approve`,
 1 AS `pdi_catatan`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_spk_persiapan_unit`
--

DROP TABLE IF EXISTS `v_spk_persiapan_unit`;
/*!50001 DROP VIEW IF EXISTS `v_spk_persiapan_unit`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_spk_persiapan_unit` AS SELECT 
 1 AS `spk_id`,
 1 AS `nomor_spk`,
 1 AS `unit_index`,
 1 AS `persiapan_unit_id`,
 1 AS `area_id`,
 1 AS `persiapan_unit_mekanik`,
 1 AS `persiapan_unit_estimasi_mulai`,
 1 AS `persiapan_unit_estimasi_selesai`,
 1 AS `persiapan_unit_tanggal_approve`,
 1 AS `persiapan_aksesoris_tersedia`,
 1 AS `battery_inventory_attachment_id`,
 1 AS `charger_inventory_attachment_id`,
 1 AS `no_unit_action`,
 1 AS `update_no_unit`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_spk_rollback_status`
--

DROP TABLE IF EXISTS `v_spk_rollback_status`;
/*!50001 DROP VIEW IF EXISTS `v_spk_rollback_status`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_spk_rollback_status` AS SELECT 
 1 AS `id`,
 1 AS `nomor_spk`,
 1 AS `status`,
 1 AS `rollback_enabled`,
 1 AS `rollback_count`,
 1 AS `last_rollback_at`,
 1 AS `can_rollback`,
 1 AS `current_stage`,
 1 AS `total_rollbacks`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_unit_status_workflow`
--

DROP TABLE IF EXISTS `v_unit_status_workflow`;
/*!50001 DROP VIEW IF EXISTS `v_unit_status_workflow`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_unit_status_workflow` AS SELECT 
 1 AS `id_inventory_unit`,
 1 AS `no_unit`,
 1 AS `serial_number`,
 1 AS `merk_unit`,
 1 AS `model_unit`,
 1 AS `current_status`,
 1 AS `current_status_id`,
 1 AS `no_kontrak`,
 1 AS `kontrak_status`,
 1 AS `customer_name`,
 1 AS `location_name`,
 1 AS `last_change_reason`,
 1 AS `last_triggered_by`,
 1 AS `last_status_change`,
 1 AS `nomor_spk`,
 1 AS `spk_status`,
 1 AS `nomor_di`,
 1 AS `di_status`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `valve`
--

DROP TABLE IF EXISTS `valve`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `valve` (
  `id_valve` int NOT NULL,
  `jumlah_valve` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id_valve`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `valve`
--

LOCK TABLES `valve` WRITE;
/*!40000 ALTER TABLE `valve` DISABLE KEYS */;
INSERT INTO `valve` VALUES (1,'2 Valve'),(2,'3 Valve'),(3,'4 Valve'),(4,'5 Valve ');
/*!40000 ALTER TABLE `valve` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `view_spk_workflow`
--

DROP TABLE IF EXISTS `view_spk_workflow`;
/*!50001 DROP VIEW IF EXISTS `view_spk_workflow`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `view_spk_workflow` AS SELECT 
 1 AS `id`,
 1 AS `nomor_spk`,
 1 AS `jenis_spk`,
 1 AS `kontrak_id`,
 1 AS `kontrak_spesifikasi_id`,
 1 AS `jumlah_unit`,
 1 AS `po_kontrak_nomor`,
 1 AS `pelanggan`,
 1 AS `pic`,
 1 AS `kontak`,
 1 AS `lokasi`,
 1 AS `delivery_plan`,
 1 AS `spesifikasi`,
 1 AS `status`,
 1 AS `catatan`,
 1 AS `dibuat_oleh`,
 1 AS `dibuat_pada`,
 1 AS `diperbarui_pada`,
 1 AS `jenis_perintah_kerja_id`,
 1 AS `tujuan_perintah_kerja_id`,
 1 AS `status_eksekusi_workflow_id`,
 1 AS `workflow_notes`,
 1 AS `workflow_created_at`,
 1 AS `workflow_updated_at`,
 1 AS `jenis_perintah_kode`,
 1 AS `jenis_perintah_nama`,
 1 AS `tujuan_perintah_kode`,
 1 AS `tujuan_perintah_nama`,
 1 AS `status_eksekusi_kode`,
 1 AS `status_eksekusi_nama`,
 1 AS `status_eksekusi_warna`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_area_employee_summary`
--

DROP TABLE IF EXISTS `vw_area_employee_summary`;
/*!50001 DROP VIEW IF EXISTS `vw_area_employee_summary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_area_employee_summary` AS SELECT 
 1 AS `area_id`,
 1 AS `area_name`,
 1 AS `area_code`,
 1 AS `nama_departemen`,
 1 AS `total_employees`,
 1 AS `admin_count`,
 1 AS `foreman_count`,
 1 AS `mechanic_count`,
 1 AS `helper_count`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_attachment_installed`
--

DROP TABLE IF EXISTS `vw_attachment_installed`;
/*!50001 DROP VIEW IF EXISTS `vw_attachment_installed`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_attachment_installed` AS SELECT 
 1 AS `id_inventory_attachment`,
 1 AS `tipe_item`,
 1 AS `item_name`,
 1 AS `serial_number`,
 1 AS `no_unit`,
 1 AS `status_unit`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_attachment_status`
--

DROP TABLE IF EXISTS `vw_attachment_status`;
/*!50001 DROP VIEW IF EXISTS `vw_attachment_status`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_attachment_status` AS SELECT 
 1 AS `id_inventory_attachment`,
 1 AS `tipe_item`,
 1 AS `po_id`,
 1 AS `id_inventory_unit`,
 1 AS `attachment_id`,
 1 AS `sn_attachment`,
 1 AS `baterai_id`,
 1 AS `sn_baterai`,
 1 AS `charger_id`,
 1 AS `sn_charger`,
 1 AS `kondisi_fisik`,
 1 AS `kelengkapan`,
 1 AS `catatan_fisik`,
 1 AS `lokasi_penyimpanan`,
 1 AS `status_unit`,
 1 AS `tanggal_masuk`,
 1 AS `catatan_inventory`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `status_attachment_id`,
 1 AS `status_attachment_name`,
 1 AS `status_attachment_desc`,
 1 AS `simple_status`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_employee_performance`
--

DROP TABLE IF EXISTS `vw_employee_performance`;
/*!50001 DROP VIEW IF EXISTS `vw_employee_performance`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_employee_performance` AS SELECT 
 1 AS `employee_id`,
 1 AS `employee_name`,
 1 AS `employee_role`,
 1 AS `department`,
 1 AS `total_work_orders`,
 1 AS `completed_orders`,
 1 AS `avg_repair_time`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_employees_by_departemen_area`
--

DROP TABLE IF EXISTS `vw_employees_by_departemen_area`;
/*!50001 DROP VIEW IF EXISTS `vw_employees_by_departemen_area`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_employees_by_departemen_area` AS SELECT 
 1 AS `nama_departemen`,
 1 AS `area_name`,
 1 AS `area_code`,
 1 AS `employee_name`,
 1 AS `employee_role`,
 1 AS `assignment_type`,
 1 AS `start_date`,
 1 AS `end_date`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_overdue_work_orders`
--

DROP TABLE IF EXISTS `vw_overdue_work_orders`;
/*!50001 DROP VIEW IF EXISTS `vw_overdue_work_orders`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_overdue_work_orders` AS SELECT 
 1 AS `id`,
 1 AS `work_order_number`,
 1 AS `report_date`,
 1 AS `unit_id`,
 1 AS `order_type`,
 1 AS `priority_id`,
 1 AS `requested_repair_time`,
 1 AS `category_id`,
 1 AS `subcategory_id`,
 1 AS `complaint_description`,
 1 AS `status_id`,
 1 AS `admin_id`,
 1 AS `foreman_id`,
 1 AS `mechanic_id`,
 1 AS `helper_id`,
 1 AS `repair_description`,
 1 AS `notes`,
 1 AS `sparepart_used`,
 1 AS `time_to_repair`,
 1 AS `completion_date`,
 1 AS `area`,
 1 AS `created_by`,
 1 AS `created_at`,
 1 AS `updated_at`,
 1 AS `sla_hours`,
 1 AS `hours_elapsed`,
 1 AS `hours_overdue`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_unit_complete_info`
--

DROP TABLE IF EXISTS `vw_unit_complete_info`;
/*!50001 DROP VIEW IF EXISTS `vw_unit_complete_info`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_unit_complete_info` AS SELECT 
 1 AS `id_inventory_unit`,
 1 AS `no_unit`,
 1 AS `serial_number`,
 1 AS `lokasi_unit`,
 1 AS `customer_code`,
 1 AS `customer_name`,
 1 AS `customer_location`,
 1 AS `customer_address`,
 1 AS `area_code`,
 1 AS `area_name`,
 1 AS `no_kontrak`,
 1 AS `kontrak_jenis_sewa`,
 1 AS `kontrak_status`,
 1 AS `kontrak_mulai`,
 1 AS `kontrak_berakhir`,
 1 AS `kontrak_nilai`,
 1 AS `admin_staff`,
 1 AS `foreman_staff`,
 1 AS `mechanic_staff`,
 1 AS `helper_staff`,
 1 AS `admin_staff_ids`,
 1 AS `foreman_staff_ids`,
 1 AS `mechanic_staff_ids`,
 1 AS `helper_staff_ids`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_work_order_by_category`
--

DROP TABLE IF EXISTS `vw_work_order_by_category`;
/*!50001 DROP VIEW IF EXISTS `vw_work_order_by_category`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_work_order_by_category` AS SELECT 
 1 AS `category_name`,
 1 AS `total_work_orders`,
 1 AS `open_work_orders`,
 1 AS `closed_work_orders`,
 1 AS `avg_repair_time`,
 1 AS `min_repair_time`,
 1 AS `max_repair_time`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_work_order_sparepart_summary`
--

DROP TABLE IF EXISTS `vw_work_order_sparepart_summary`;
/*!50001 DROP VIEW IF EXISTS `vw_work_order_sparepart_summary`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_work_order_sparepart_summary` AS SELECT 
 1 AS `work_order_id`,
 1 AS `work_order_number`,
 1 AS `sparepart_id`,
 1 AS `sparepart_code`,
 1 AS `sparepart_name`,
 1 AS `quantity_brought`,
 1 AS `satuan`,
 1 AS `quantity_used`,
 1 AS `quantity_returned`,
 1 AS `quantity_available`,
 1 AS `usage_notes`,
 1 AS `return_notes`,
 1 AS `used_at`,
 1 AS `returned_at`,
 1 AS `status`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_work_order_stats`
--

DROP TABLE IF EXISTS `vw_work_order_stats`;
/*!50001 DROP VIEW IF EXISTS `vw_work_order_stats`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_work_order_stats` AS SELECT 
 1 AS `total_work_orders`,
 1 AS `open_work_orders`,
 1 AS `assigned_work_orders`,
 1 AS `in_progress_work_orders`,
 1 AS `waiting_parts_work_orders`,
 1 AS `testing_work_orders`,
 1 AS `completed_work_orders`,
 1 AS `closed_work_orders`,
 1 AS `cancelled_work_orders`,
 1 AS `on_hold_work_orders`,
 1 AS `low_priority_count`,
 1 AS `medium_priority_count`,
 1 AS `high_priority_count`,
 1 AS `critical_priority_count`,
 1 AS `complaint_orders`,
 1 AS `pmps_orders`,
 1 AS `fabrikasi_orders`,
 1 AS `persiapan_orders`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_work_orders_detail`
--

DROP TABLE IF EXISTS `vw_work_orders_detail`;
/*!50001 DROP VIEW IF EXISTS `vw_work_orders_detail`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_work_orders_detail` AS SELECT 
 1 AS `id`,
 1 AS `work_order_number`,
 1 AS `report_date`,
 1 AS `formatted_report_date`,
 1 AS `no_unit`,
 1 AS `nama_perusahaan`,
 1 AS `merk_unit`,
 1 AS `tipe_unit`,
 1 AS `kapasitas`,
 1 AS `kapasitas_unit`,
 1 AS `tipe_order`,
 1 AS `priority`,
 1 AS `priority_color`,
 1 AS `requested_repair_time`,
 1 AS `formatted_request_time`,
 1 AS `kategori`,
 1 AS `sub_kategori`,
 1 AS `keluhan_unit`,
 1 AS `status`,
 1 AS `status_color`,
 1 AS `admin`,
 1 AS `foreman`,
 1 AS `mekanik`,
 1 AS `helper`,
 1 AS `perbaikan`,
 1 AS `keterangan`,
 1 AS `sparepart`,
 1 AS `ttr`,
 1 AS `tanggal`,
 1 AS `bulan`,
 1 AS `area`,
 1 AS `created_at`,
 1 AS `updated_at`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vw_workflow_kontrak_spk_di`
--

DROP TABLE IF EXISTS `vw_workflow_kontrak_spk_di`;
/*!50001 DROP VIEW IF EXISTS `vw_workflow_kontrak_spk_di`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vw_workflow_kontrak_spk_di` AS SELECT 
 1 AS `id_inventory_unit`,
 1 AS `no_unit`,
 1 AS `serial_number`,
 1 AS `kontrak_id`,
 1 AS `no_kontrak`,
 1 AS `pelanggan`,
 1 AS `kontrak_lokasi`,
 1 AS `kontrak_status`,
 1 AS `kontrak_spesifikasi_id`,
 1 AS `spek_kode`,
 1 AS `spek_aksesoris`,
 1 AS `spk_id`,
 1 AS `nomor_spk`,
 1 AS `spk_status`,
 1 AS `delivery_plan`,
 1 AS `delivery_instruction_id`,
 1 AS `nomor_di`,
 1 AS `tanggal_kirim`,
 1 AS `di_status`,
 1 AS `unit_aksesoris`,
 1 AS `lokasi_unit`,
 1 AS `status_unit_id`,
 1 AS `nama_status`,
 1 AS `unit_created`,
 1 AS `unit_updated`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `work_order_assignments`
--

DROP TABLE IF EXISTS `work_order_assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_assignments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_id` int NOT NULL,
  `employee_id` int NOT NULL,
  `role` enum('ADMIN','FOREMAN','MECHANIC','HELPER') COLLATE utf8mb4_general_ci NOT NULL,
  `assignment_type` enum('PRIMARY','SECONDARY') COLLATE utf8mb4_general_ci DEFAULT 'PRIMARY',
  `assigned_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` int NOT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_work_order_id` (`work_order_id`),
  KEY `idx_employee_id` (`employee_id`),
  KEY `idx_role` (`role`),
  KEY `idx_assignment_type` (`assignment_type`),
  KEY `idx_is_active` (`is_active`),
  KEY `fk_woa_assigned_by_id` (`assigned_by`),
  KEY `idx_wo_assignments_lookup` (`work_order_id`,`role`,`is_active`),
  KEY `idx_woa_work_order_role` (`work_order_id`,`role`),
  CONSTRAINT `fk_woa_assigned_by_id` FOREIGN KEY (`assigned_by`) REFERENCES `employees` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_woa_employee_id` FOREIGN KEY (`employee_id`) REFERENCES `employees` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_woa_work_order_id` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Store multiple staff assignments per work order with roles and priority';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_assignments`
--

LOCK TABLES `work_order_assignments` WRITE;
/*!40000 ALTER TABLE `work_order_assignments` DISABLE KEYS */;
INSERT INTO `work_order_assignments` VALUES (12,19,7,'MECHANIC','PRIMARY','2025-10-02 02:52:58',1,'Test assignment from debug script - fixed version',0,'2025-10-02 02:52:58','2025-10-02 03:00:56',NULL),(13,19,8,'MECHANIC','SECONDARY','2025-10-02 02:52:58',1,'Test assignment from debug script - fixed version',0,'2025-10-02 02:52:58','2025-10-02 03:00:56',NULL),(14,19,14,'HELPER','PRIMARY','2025-10-02 02:52:58',1,'Test assignment from debug script - fixed version',0,'2025-10-02 02:52:58','2025-10-02 03:00:56',NULL),(15,19,7,'MECHANIC','PRIMARY','2025-10-02 09:52:59',1,'Test assignment',0,'2025-10-02 09:52:59','2025-10-02 03:00:56',NULL),(16,19,8,'MECHANIC','SECONDARY','2025-10-02 09:52:59',1,'Test assignment',0,'2025-10-02 09:52:59','2025-10-02 03:00:56',NULL),(17,19,10,'HELPER','PRIMARY','2025-10-02 09:52:59',1,'Test assignment',0,'2025-10-02 09:52:59','2025-10-02 03:00:56',NULL),(18,19,11,'HELPER','SECONDARY','2025-10-02 09:52:59',1,'Test assignment',0,'2025-10-02 09:52:59','2025-10-02 03:00:56',NULL),(19,19,7,'MECHANIC','PRIMARY','2025-10-02 02:54:22',1,'',0,'2025-10-02 02:54:22','2025-10-02 03:00:56',NULL),(20,19,11,'MECHANIC','SECONDARY','2025-10-02 02:54:22',1,'',0,'2025-10-02 02:54:22','2025-10-02 03:00:56',NULL),(21,19,16,'HELPER','PRIMARY','2025-10-02 02:54:22',1,'',0,'2025-10-02 02:54:22','2025-10-02 03:00:56',NULL),(22,19,7,'MECHANIC','PRIMARY','2025-10-02 03:00:56',1,'Test assignment from debug script - fixed version',1,'2025-10-02 03:00:56','2025-10-02 03:00:56',NULL),(23,19,8,'MECHANIC','SECONDARY','2025-10-02 03:00:56',1,'Test assignment from debug script - fixed version',1,'2025-10-02 03:00:56','2025-10-02 03:00:56',NULL),(24,19,14,'HELPER','PRIMARY','2025-10-02 03:00:56',1,'Test assignment from debug script - fixed version',1,'2025-10-02 03:00:56','2025-10-02 03:00:56',NULL),(25,19,7,'MECHANIC','PRIMARY','2025-10-02 10:00:57',1,'Test assignment',1,'2025-10-02 10:00:57','2025-10-02 10:00:57',NULL),(26,19,8,'MECHANIC','SECONDARY','2025-10-02 10:00:57',1,'Test assignment',1,'2025-10-02 10:00:57','2025-10-02 10:00:57',NULL),(27,19,10,'HELPER','PRIMARY','2025-10-02 10:00:57',1,'Test assignment',1,'2025-10-02 10:00:57','2025-10-02 10:00:57',NULL),(28,19,11,'HELPER','SECONDARY','2025-10-02 10:00:57',1,'Test assignment',1,'2025-10-02 10:00:57','2025-10-02 10:00:57',NULL),(29,29,8,'MECHANIC','PRIMARY','2025-10-03 08:47:57',1,NULL,1,'2025-10-03 08:47:57','2025-10-03 08:47:57',NULL),(30,29,15,'HELPER','PRIMARY','2025-10-03 08:47:57',1,NULL,1,'2025-10-03 08:47:57','2025-10-03 08:47:57',NULL);
/*!40000 ALTER TABLE `work_order_assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_attachments`
--

DROP TABLE IF EXISTS `work_order_attachments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_attachments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_id` int NOT NULL,
  `file_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `file_path` varchar(500) COLLATE utf8mb4_general_ci NOT NULL,
  `file_type` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `file_size` bigint DEFAULT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `attachment_type` enum('PHOTO','DOCUMENT','VIDEO','OTHER') COLLATE utf8mb4_general_ci DEFAULT 'PHOTO',
  `uploaded_by` int NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_woa_work_order` (`work_order_id`),
  KEY `fk_woa_uploaded_by` (`uploaded_by`),
  CONSTRAINT `fk_woa_uploaded_by` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_woa_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_attachments`
--

LOCK TABLES `work_order_attachments` WRITE;
/*!40000 ALTER TABLE `work_order_attachments` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_order_attachments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_categories`
--

DROP TABLE IF EXISTS `work_order_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `category_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_category_code` (`category_code`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_categories`
--

LOCK TABLES `work_order_categories` WRITE;
/*!40000 ALTER TABLE `work_order_categories` DISABLE KEYS */;
INSERT INTO `work_order_categories` VALUES (1,'Attachments & Accessories','ATT_ACC','Semua komponen tambahan dan aksesoris forklift',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(2,'Braking / Pengereman','BRAKE','Sistem pengereman dan komponen terkait',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(3,'Chassis & Body','CHASSIS','Rangka, bodi, dan struktur utama forklift',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(4,'Engine / Mesin','ENGINE','Mesin dan komponen penggerak utama',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(5,'Hidrolik','HYDRAULIC','Sistem hidrolik dan komponen terkait',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(6,'Kelistrikan','ELECTRIC','Sistem kelistrikan dan komponen elektronik',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(7,'Pengapian / Bahan Bakar','FUEL_IGN','Sistem bahan bakar dan pengapian',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(8,'Roda dan Ban','WHEEL_TIRE','Roda, ban, dan komponen terkait',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(9,'Safety','SAFETY','Komponen keselamatan kerja',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(10,'Transmisi','TRANSMISSION','Sistem transmisi dan perpindahan tenaga',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(11,'Pelumas & Fluida','LUBRICANT','Oli, pelumas, dan cairan operasional',1,'2025-09-23 08:24:36','2025-09-23 08:24:36');
/*!40000 ALTER TABLE `work_order_categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_comments`
--

DROP TABLE IF EXISTS `work_order_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_id` int NOT NULL,
  `comment_text` text COLLATE utf8mb4_general_ci NOT NULL,
  `comment_type` enum('PROGRESS','ISSUE','SOLUTION','GENERAL') COLLATE utf8mb4_general_ci DEFAULT 'GENERAL',
  `is_internal` tinyint(1) DEFAULT '0',
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_woc_work_order` (`work_order_id`),
  KEY `fk_woc_created_by` (`created_by`),
  CONSTRAINT `fk_woc_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_woc_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_comments`
--

LOCK TABLES `work_order_comments` WRITE;
/*!40000 ALTER TABLE `work_order_comments` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_order_comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_priorities`
--

DROP TABLE IF EXISTS `work_order_priorities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_priorities` (
  `id` int NOT NULL AUTO_INCREMENT,
  `priority_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `priority_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `priority_level` int NOT NULL,
  `priority_color` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'info',
  `description` text COLLATE utf8mb4_general_ci,
  `sla_hours` int DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_priority_code` (`priority_code`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_priorities`
--

LOCK TABLES `work_order_priorities` WRITE;
/*!40000 ALTER TABLE `work_order_priorities` DISABLE KEYS */;
INSERT INTO `work_order_priorities` VALUES (1,'Critical','CRITICAL',5,'danger','Memerlukan tindakan segera - unit tidak bisa beroperasi',2,1,'2025-09-23 08:24:36'),(2,'High','HIGH',4,'warning','Prioritas tinggi - berpotensi mengganggu operasional',8,1,'2025-09-23 08:24:36'),(3,'Medium','MEDIUM',3,'info','Prioritas sedang - perlu ditangani dalam waktu normal',24,1,'2025-09-23 08:24:36'),(4,'Low','LOW',2,'secondary','Prioritas rendah - dapat dijadwalkan',72,1,'2025-09-23 08:24:36'),(5,'Routine','ROUTINE',1,'success','Perawatan rutin - dapat dijadwalkan sesuai kebutuhan',168,1,'2025-09-23 08:24:36');
/*!40000 ALTER TABLE `work_order_priorities` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_sparepart_returns`
--

DROP TABLE IF EXISTS `work_order_sparepart_returns`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_sparepart_returns` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_id` int NOT NULL,
  `work_order_sparepart_id` int DEFAULT NULL,
  `sparepart_code` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sparepart_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity_brought` int NOT NULL,
  `quantity_used` int NOT NULL DEFAULT '0',
  `quantity_return` int NOT NULL,
  `satuan` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED') COLLATE utf8mb4_unicode_ci DEFAULT 'PENDING',
  `return_notes` text COLLATE utf8mb4_unicode_ci,
  `confirmed_by` int DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_id` (`work_order_id`),
  KEY `work_order_sparepart_id` (`work_order_sparepart_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_sparepart_returns`
--

LOCK TABLES `work_order_sparepart_returns` WRITE;
/*!40000 ALTER TABLE `work_order_sparepart_returns` DISABLE KEYS */;
INSERT INTO `work_order_sparepart_returns` VALUES (1,32,24,'ELC-002','Battery - 12V 12Ah',1,0,1,'PCS','CONFIRMED','',1,'2025-11-20 14:17:27','2025-11-20 13:40:19','2025-11-20 14:17:27'),(2,32,25,'SPC-005','Load Backrest - 1800mm',1,0,1,'PCS','PENDING','Auto-generated from sparepart validation',NULL,NULL,'2025-11-20 13:40:19','2025-11-20 13:40:19'),(3,35,35,'TRN-004','Clutch Bearing - Release',1,0,1,'PCS','CONFIRMED','',1,'2025-11-21 13:28:50','2025-11-21 10:02:49','2025-11-21 13:28:50'),(4,35,36,'ENG-004','Engine Oil - SAE 10W-30',1,0,1,'PCS','PENDING','Auto-generated from sparepart validation',NULL,NULL,'2025-11-21 10:02:49','2025-11-21 10:02:49');
/*!40000 ALTER TABLE `work_order_sparepart_returns` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_sparepart_usage`
--

DROP TABLE IF EXISTS `work_order_sparepart_usage`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_sparepart_usage` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_sparepart_id` int NOT NULL,
  `work_order_id` int NOT NULL,
  `quantity_used` int NOT NULL DEFAULT '0',
  `quantity_returned` int NOT NULL DEFAULT '0',
  `usage_notes` text COLLATE utf8mb4_general_ci,
  `return_notes` text COLLATE utf8mb4_general_ci,
  `used_at` timestamp NULL DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_wo_sparepart_usage_wo_id` (`work_order_id`),
  KEY `idx_wo_sparepart_usage_sparepart_id` (`work_order_sparepart_id`),
  CONSTRAINT `work_order_sparepart_usage_ibfk_1` FOREIGN KEY (`work_order_sparepart_id`) REFERENCES `work_order_spareparts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `work_order_sparepart_usage_ibfk_2` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracks actual usage and return of spareparts in work orders';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_sparepart_usage`
--

LOCK TABLES `work_order_sparepart_usage` WRITE;
/*!40000 ALTER TABLE `work_order_sparepart_usage` DISABLE KEYS */;
/*!40000 ALTER TABLE `work_order_sparepart_usage` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_work_order_sparepart_usage_insert` AFTER INSERT ON `work_order_sparepart_usage` FOR EACH ROW BEGIN
    
    IF NEW.quantity_returned = 0 AND NEW.quantity_used > 0 THEN
        UPDATE work_order_sparepart_usage 
        SET quantity_returned = (
            SELECT GREATEST(0, (wos.quantity_brought - NEW.quantity_used))
            FROM work_order_spareparts wos 
            WHERE wos.id = NEW.work_order_sparepart_id
        )
        WHERE id = NEW.id;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `work_order_spareparts`
--

DROP TABLE IF EXISTS `work_order_spareparts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_spareparts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_id` int NOT NULL,
  `sparepart_code` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `sparepart_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `quantity_brought` int NOT NULL DEFAULT '0',
  `satuan` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pcs',
  `notes` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `quantity_used` decimal(10,2) DEFAULT NULL COMMENT 'Actual quantity used during work order',
  `is_additional` tinyint(1) DEFAULT '0' COMMENT '1 if this is additional sparepart taken from warehouse',
  `sparepart_validated` tinyint(1) DEFAULT '0' COMMENT '1 if sparepart usage has been validated',
  PRIMARY KEY (`id`),
  KEY `idx_work_order_spareparts_wo_id` (`work_order_id`),
  KEY `idx_work_order_spareparts_code` (`sparepart_code`),
  KEY `idx_work_order_spareparts_composite` (`work_order_id`,`sparepart_code`),
  KEY `idx_wo_spareparts_additional` (`work_order_id`,`is_additional`),
  CONSTRAINT `work_order_spareparts_ibfk_1` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Tracks spareparts brought for work orders';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_spareparts`
--

LOCK TABLES `work_order_spareparts` WRITE;
/*!40000 ALTER TABLE `work_order_spareparts` DISABLE KEYS */;
INSERT INTO `work_order_spareparts` VALUES (6,7,'BRAKE-001','Brake Pad Set',2,'set','Kampas rem depan untuk forklift','2025-10-01 09:08:14','2025-10-01 09:08:14',NULL,0,0),(7,7,'OIL-001','Engine Oil 10W-40',4,'liter','Oli mesin ganti berkala','2025-10-01 09:08:14','2025-10-01 09:08:14',NULL,0,0),(8,7,'FILTER-001','Oil Filter',1,'pcs','Filter oli mesin','2025-10-01 09:08:14','2025-10-01 09:08:14',NULL,0,0),(9,8,'BELT-001','Drive Belt',1,'pcs','Belt penggerak yang putus','2025-10-01 09:08:14','2025-10-01 09:08:14',NULL,0,0),(10,8,'BEARING-001','Ball Bearing 6204',2,'pcs','Bearing roda depan','2025-10-01 09:08:14','2025-10-01 09:08:14',NULL,0,0),(11,14,'001','ban',2,'pcs',NULL,'2025-10-01 03:04:49','2025-10-01 03:04:49',NULL,0,0),(12,14,'00123','OLI',1,'botol',NULL,'2025-10-01 03:04:49','2025-10-01 03:04:49',NULL,0,0),(13,17,'ATT-001','Side Shifter - Hydraulic',1,'pcs',NULL,'2025-10-02 01:39:07','2025-10-02 01:39:07',NULL,0,0),(14,18,'ATT-005','Rotator - 360 Degree',1,'pcs',NULL,'2025-10-02 01:39:36','2025-10-02 01:39:36',NULL,0,0),(15,18,'BRK-001','Brake Pad - Front',1,'pcs',NULL,'2025-10-02 01:39:36','2025-10-02 01:39:36',NULL,0,0),(16,18,'ATT-002','Side Shifter - Manual',1,'pcs',NULL,'2025-10-02 01:39:36','2025-10-02 01:39:36',NULL,0,0),(17,20,'ATT-005','Rotator - 360 Degree',1,'pcs',NULL,'2025-10-02 01:44:51','2025-10-02 01:44:51',NULL,0,0),(18,20,'BRK-001','Brake Pad - Front',1,'pcs',NULL,'2025-10-02 01:44:51','2025-10-02 01:44:51',NULL,0,0),(19,20,'ATT-001','Side Shifter - Hydraulic',1,'pcs',NULL,'2025-10-02 01:44:51','2025-10-02 01:44:51',NULL,0,0),(20,24,'ATT-005','Rotator - 360 Degree',1,'pcs',NULL,'2025-10-02 07:39:46','2025-10-02 07:39:46',NULL,0,0),(21,26,'ATT-003','Fork Positioner - Hydraulic',1,'pcs','','2025-10-02 07:51:01','2025-10-05 20:01:52',1.00,0,0),(22,32,'ENG-002','Air Filter - Honda GX160',1,'PCS','','2025-10-03 18:55:19','2025-11-20 06:40:19',1.00,0,0),(23,32,'ELC-003','Battery Terminal - Positive',1,'PCS','','2025-10-03 18:55:19','2025-11-20 06:40:19',1.00,0,0),(24,32,'ELC-002','Battery - 12V 12Ah',1,'PCS','','2025-10-03 18:55:19','2025-11-20 06:40:19',0.00,0,0),(25,32,'SPC-005','Load Backrest - 1800mm',1,'PCS','','2025-10-03 18:55:19','2025-11-20 06:40:19',0.00,0,0),(26,33,'HYD-003','Hydraulic Hose - 1/4',1,'PCS','','2025-10-03 18:55:51','2025-10-05 18:32:13',1.00,0,0),(27,33,'SPC-003','Load Backrest - 1400mm',1,'PCS','','2025-10-03 18:55:51','2025-10-05 18:32:13',1.00,0,0),(28,34,'ELC-004','Battery Terminal - Negative',1,'PCS',NULL,'2025-10-03 19:04:14','2025-10-03 19:04:14',NULL,0,0),(29,34,'MST-004','Mast Chain - 5/8',10,'METER',NULL,'2025-10-03 19:04:14','2025-10-03 19:04:14',NULL,0,0),(30,34,'SAF-004','Warning Light - Red',1,'PCS',NULL,'2025-10-03 19:04:14','2025-10-03 19:04:14',NULL,0,0),(31,34,'ELC-003','Battery Terminal - Positive',1,'PCS',NULL,'2025-10-03 19:04:14','2025-10-03 19:04:14',NULL,0,0),(32,34,'SAF-004','Warning Light - Red',1,'PCS',NULL,'2025-10-03 19:04:14','2025-10-03 19:04:14',NULL,0,0),(33,34,'TOL-002','Wrench Set - Imperial',1,'PCS',NULL,'2025-10-03 19:04:14','2025-10-03 19:04:14',NULL,0,0),(34,33,'HYD-003','Hydraulic Hose - 1/4\" x 2m',1,'SET','tambahan','2025-10-05 18:32:13','2025-10-05 18:32:13',1.00,1,0),(35,35,'TRN-004','Clutch Bearing - Release',1,'PCS','','2025-10-06 23:58:34','2025-11-21 03:02:49',0.00,0,0),(36,35,'ENG-004','Engine Oil - SAE 10W-30',1,'PCS','','2025-10-06 23:58:34','2025-11-21 03:02:49',0.00,0,0),(37,35,'ELC-004','Battery Terminal - Negative',1,'PCS','','2025-11-21 03:02:49','2025-11-21 03:02:49',1.00,1,0);
/*!40000 ALTER TABLE `work_order_spareparts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_staff_backup_final`
--

DROP TABLE IF EXISTS `work_order_staff_backup_final`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_staff_backup_final` (
  `id` int NOT NULL DEFAULT '0',
  `staff_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `staff_role` enum('ADMIN','FOREMAN','MECHANIC','HELPER') COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_staff_backup_final`
--

LOCK TABLES `work_order_staff_backup_final` WRITE;
/*!40000 ALTER TABLE `work_order_staff_backup_final` DISABLE KEYS */;
INSERT INTO `work_order_staff_backup_final` VALUES (1,'Novi','ADMIN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(2,'Sari','ADMIN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(3,'Andi','ADMIN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(4,'YOGA','FOREMAN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(5,'Budi','FOREMAN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(6,'Eko','FOREMAN',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(7,'KURNIA','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(8,'BAGUS','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(9,'Deni','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(10,'Rudi','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(11,'Wahyu','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(12,'Joko','MECHANIC',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(13,'Agus','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(14,'Dimas','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(15,'Fajar','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(16,'Hendra','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(17,'Iwan','HELPER',1,'2025-09-23 08:24:36','2025-09-23 08:24:36');
/*!40000 ALTER TABLE `work_order_staff_backup_final` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_status_history`
--

DROP TABLE IF EXISTS `work_order_status_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_status_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_id` int NOT NULL,
  `from_status_id` int DEFAULT NULL,
  `to_status_id` int NOT NULL,
  `changed_by` int NOT NULL,
  `change_reason` text COLLATE utf8mb4_general_ci,
  `changed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_wosh_work_order` (`work_order_id`),
  KEY `fk_wosh_from_status` (`from_status_id`),
  KEY `fk_wosh_to_status` (`to_status_id`),
  KEY `fk_wosh_changed_by` (`changed_by`),
  CONSTRAINT `fk_wosh_changed_by` FOREIGN KEY (`changed_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wosh_from_status` FOREIGN KEY (`from_status_id`) REFERENCES `work_order_statuses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wosh_to_status` FOREIGN KEY (`to_status_id`) REFERENCES `work_order_statuses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wosh_work_order` FOREIGN KEY (`work_order_id`) REFERENCES `work_orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_status_history`
--

LOCK TABLES `work_order_status_history` WRITE;
/*!40000 ALTER TABLE `work_order_status_history` DISABLE KEYS */;
INSERT INTO `work_order_status_history` VALUES (1,1,NULL,1,1,'Work order created','2025-09-23 08:24:36'),(5,5,NULL,1,1,'Work order created','2025-09-24 10:05:39'),(6,6,NULL,1,1,'Work order created','2025-09-24 10:06:37'),(7,7,NULL,1,1,'Work order created','2025-09-24 10:11:17'),(8,8,NULL,1,1,'Work order created','2025-09-24 10:11:27'),(9,9,NULL,1,1,'Work order created','2025-09-25 01:37:24'),(10,10,NULL,1,1,'Work order created','2025-09-25 01:40:02'),(11,11,NULL,1,1,'Work order created','2025-09-25 01:55:12'),(12,12,NULL,1,1,'Work order created','2025-09-25 02:07:46'),(13,13,NULL,1,1,'Work order created','2025-09-25 02:15:04'),(14,14,NULL,1,1,'Work order created','2025-10-01 10:04:49'),(15,15,NULL,1,1,'Work order created','2025-10-02 08:37:26'),(17,17,NULL,1,1,'Work order created','2025-10-02 08:39:07'),(18,18,NULL,1,1,'Work order created','2025-10-02 08:39:36'),(19,19,NULL,1,1,'Work order created','2025-10-02 08:40:23'),(20,20,NULL,1,1,'Work order created','2025-10-02 08:44:51'),(21,19,2,2,1,'Work order assigned to mechanic and helper','2025-10-02 02:52:58'),(22,19,2,2,1,'Enhanced assignment test with multiple staff','2025-10-02 09:52:59'),(23,19,2,2,1,'Work order assigned to mechanic and helper','2025-10-02 02:54:22'),(24,19,2,2,1,'Work order assigned to mechanic and helper','2025-10-02 03:00:56'),(25,19,2,2,1,'Enhanced assignment test with multiple staff','2025-10-02 10:00:57'),(26,19,3,3,1,'Work order dimulai','2025-10-02 03:04:19'),(28,22,NULL,1,1,'Work order created','2025-10-02 14:29:33'),(29,22,3,3,1,'Staff assigned during work order creation: Test assignment','2025-10-02 07:29:33'),(30,23,NULL,1,1,'Work order created','2025-10-02 14:37:14'),(31,23,3,3,1,'Staff assigned during work order creation: Test assignment after cleanup','2025-10-02 07:37:14'),(32,20,3,3,1,'Work order assigned and started','2025-10-02 07:39:00'),(33,24,NULL,1,1,'Work order created','2025-10-02 14:39:46'),(34,24,3,3,1,'Staff assigned during work order creation: ','2025-10-02 07:39:46'),(35,25,NULL,1,1,'Work order created','2025-10-02 14:49:57'),(36,26,NULL,1,1,'Work order created','2025-10-02 14:51:01'),(37,26,3,3,1,'Work order assigned and started','2025-10-02 07:51:19'),(38,5,3,3,1,'Work order reassigned and started','2025-10-02 07:51:39'),(39,5,9,9,1,'ASDAWD','2025-10-02 07:51:52'),(40,5,3,3,1,'Work order dilanjutkan','2025-10-02 07:51:56'),(41,18,3,3,1,'Work order assigned and started','2025-10-02 07:52:23'),(42,25,3,3,1,'Work order assigned and started','2025-10-02 18:51:13'),(43,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:06:47'),(44,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:07:45'),(45,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:08:46'),(46,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:09:16'),(47,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:10:22'),(48,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:11:23'),(49,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:14:31'),(50,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:15:07'),(51,6,6,6,1,'Work order completed with unit verification','2025-10-02 19:15:15'),(52,5,6,6,1,'Work order completed with unit verification','2025-10-02 19:15:20'),(53,8,6,6,1,'Work order completed with unit verification','2025-10-02 19:15:24'),(54,1,NULL,6,1,'Work order completed with unit verification','2025-10-02 19:17:46'),(55,5,6,6,1,'Work order completed with unit verification','2025-10-02 19:17:52'),(56,6,6,6,1,'Work order completed with unit verification','2025-10-02 19:17:58'),(57,27,NULL,1,1,'Work order created','2025-10-03 08:20:39'),(59,29,NULL,1,1,'Work order created','2025-10-03 08:47:57'),(60,30,NULL,1,1,'Work order created','2025-10-03 08:51:23'),(61,31,NULL,1,1,'Work order created','2025-10-03 09:28:05'),(62,32,NULL,1,1,'Work order created','2025-10-04 01:55:19'),(63,33,NULL,1,1,'Work order created','2025-10-04 01:55:51'),(64,34,NULL,1,1,'Work order created','2025-10-04 02:04:14'),(65,34,3,3,1,'Work order dimulai','2025-10-04 09:40:31'),(66,34,9,9,1,'orangnya pergi','2025-10-04 10:26:42'),(67,34,9,3,1,'Work order dilanjutkan','2025-10-04 10:40:09'),(68,33,1,3,1,'Work order dimulai','2025-10-04 10:40:44'),(69,34,3,9,1,'orangnya pergi lagi','2025-10-04 10:44:02'),(70,34,9,3,1,'Work order dilanjutkan','2025-10-05 06:03:29'),(71,34,3,6,1,'Work order completed with unit verification','2025-10-05 06:58:20'),(72,34,6,7,1,'Work order ditutup','2025-10-05 07:15:23'),(73,33,3,3,1,'Unit verification completed - pending sparepart validation','2025-10-05 07:17:10'),(74,33,3,6,1,'Work order completed with unit verification','2025-10-05 07:23:21'),(75,33,6,7,1,'Work order closed with sparepart validation completed','2025-10-05 18:32:13'),(76,26,3,6,1,'Work order completed with unit verification','2025-10-05 19:11:25'),(77,26,6,7,1,'Work order closed with sparepart validation completed','2025-10-05 20:01:52'),(78,29,1,3,1,'Work order dimulai','2025-10-05 20:58:12'),(85,29,3,6,1,'Work order completed with unit verification','2025-10-06 23:40:19'),(86,25,3,6,1,'Work order completed with unit verification','2025-10-06 23:51:23'),(87,35,NULL,1,1,'Work order created','2025-10-07 06:58:34'),(88,35,1,1,1,'Work order created with initial status','2025-10-06 23:58:34'),(89,27,1,3,1,'Work order dimulai','2025-10-07 22:49:39'),(90,27,3,9,1,'asdawd','2025-10-07 22:50:40'),(91,27,9,3,1,'Work order dilanjutkan','2025-10-07 22:50:43'),(92,35,1,3,1,'Work order dimulai','2025-10-08 18:36:19'),(93,32,1,3,1,'Work order dimulai','2025-10-23 20:10:25'),(95,32,3,6,1,'Work order completed with unit verification','2025-11-20 06:39:38'),(96,32,6,7,1,'Work order closed with sparepart validation completed','2025-11-20 06:40:19'),(97,29,6,7,1,'Work order closed with sparepart validation completed','2025-11-21 02:11:01'),(102,35,3,6,1,'Work order completed with unit verification','2025-11-21 02:59:19'),(103,35,6,7,1,'Work order closed with sparepart validation completed','2025-11-21 03:02:49');
/*!40000 ALTER TABLE `work_order_status_history` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_statuses`
--

DROP TABLE IF EXISTS `work_order_statuses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_statuses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `status_name` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `status_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `status_color` varchar(20) COLLATE utf8mb4_general_ci DEFAULT 'secondary',
  `description` text COLLATE utf8mb4_general_ci,
  `is_final_status` tinyint(1) DEFAULT '0',
  `sort_order` int DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_status_code` (`status_code`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_statuses`
--

LOCK TABLES `work_order_statuses` WRITE;
/*!40000 ALTER TABLE `work_order_statuses` DISABLE KEYS */;
INSERT INTO `work_order_statuses` VALUES (1,'Open','OPEN','info','Work order baru dibuat dan menunggu untuk ditangani',0,1,1,'2025-09-23 08:24:36'),(2,'Assigned','ASSIGNED','primary','Work order telah ditugaskan ke teknisi',0,2,1,'2025-09-23 08:24:36'),(3,'In Progress','IN_PROGRESS','warning','Sedang dalam proses perbaikan',0,3,1,'2025-09-23 08:24:36'),(4,'Waiting Parts','WAITING_PARTS','secondary','Menunggu spare parts atau komponen',0,4,1,'2025-09-23 08:24:36'),(5,'Testing','TESTING','info','Dalam tahap pengujian setelah perbaikan',0,5,1,'2025-09-23 08:24:36'),(6,'Completed','COMPLETED','success','Perbaikan selesai dan unit siap digunakan',1,6,1,'2025-09-23 08:24:36'),(7,'Closed','CLOSED','dark','Work order ditutup dan diselesaikan',1,7,1,'2025-09-23 08:24:36'),(8,'Cancelled','CANCELLED','danger','Work order dibatalkan',1,8,1,'2025-09-23 08:24:36'),(9,'On Hold','ON_HOLD','warning','Work order ditunda sementara',0,9,1,'2025-09-23 08:24:36');
/*!40000 ALTER TABLE `work_order_statuses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_order_subcategories`
--

DROP TABLE IF EXISTS `work_order_subcategories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_order_subcategories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `category_id` int NOT NULL,
  `subcategory_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `subcategory_code` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `description` text COLLATE utf8mb4_general_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_subcategory_code` (`subcategory_code`),
  KEY `fk_subcategory_category` (`category_id`),
  CONSTRAINT `fk_subcategory_category` FOREIGN KEY (`category_id`) REFERENCES `work_order_categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=143 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_order_subcategories`
--

LOCK TABLES `work_order_subcategories` WRITE;
/*!40000 ALTER TABLE `work_order_subcategories` DISABLE KEYS */;
INSERT INTO `work_order_subcategories` VALUES (1,1,'Side Shifter','SIDE_SHIFTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(2,1,'Fork Positioner','FORK_POS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(3,1,'Load Backrest Extension','LOAD_BACKREST',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(4,1,'Rotator','ROTATOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(5,1,'Clamps (Paper Roll, Carton, dll.)','CLAMPS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(6,1,'Push/Pull Attachment','PUSH_PULL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(7,1,'Single/Double Pallet Handler','PALLET_HANDLER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(8,1,'AC (Air Conditioner)','AC',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(9,1,'Heater (Pemanas)','HEATER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(10,1,'Radio/Audio System','RADIO',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(11,2,'Pedal Rem','BRAKE_PEDAL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(12,2,'Master Cylinder','MASTER_CYL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(13,2,'Booster Rem','BRAKE_BOOSTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(14,2,'Saluran Rem','BRAKE_LINE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(15,2,'Wheel Cylinder / Kaliper Rem','WHEEL_CYL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(16,2,'Brake Shoe','BRAKE_SHOE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(17,2,'Brake Pad','BRAKE_PAD',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(18,2,'Brake Drum','BRAKE_DRUM',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(19,2,'Brake Disc','BRAKE_DISC',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(20,2,'Parking Brake','PARKING_BRAKE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(21,2,'Tuas Rem Parkir','PARK_BRAKE_LEVER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(22,2,'Kabel Rem Parkir','PARK_BRAKE_CABLE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(23,2,'Mekanisme Rem Parkir','PARK_BRAKE_MECH',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(24,3,'Rangka Utama (Frame)','MAIN_FRAME',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(25,3,'Mast','MAST',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(26,3,'Outer Mast','OUTER_MAST',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(27,3,'Inner Mast','INNER_MAST',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(28,3,'Intermediate Mast','INT_MAST',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(29,3,'Roller Mast','ROLLER_MAST',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(30,3,'Chain Mast','CHAIN_MAST',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(31,3,'Carriage','CARRIAGE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(32,3,'Forks','FORKS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(33,3,'Overhead Guard','OVERHEAD_GUARD',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(34,3,'Seat','SEAT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(35,3,'Panel Instrumen','INSTRUMENT_PANEL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(36,3,'Lantai Kabin','CABIN_FLOOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(37,3,'Kap Mesin','ENGINE_HOOD',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(38,3,'Counterweight','COUNTERWEIGHT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(39,4,'Mesin Pembakaran Dalam (ICE)','ICE_ENGINE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(40,4,'Blok Mesin','ENGINE_BLOCK',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(41,4,'Kepala Silinder','CYLINDER_HEAD',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(42,4,'Komponen Internal Mesin','ENGINE_INTERNAL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(43,4,'Sistem Pendingin','COOLING_SYS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(44,4,'Radiator','RADIATOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(45,4,'Selang Radiator','RADIATOR_HOSE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(46,4,'Termostat','THERMOSTAT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(47,4,'Pompa Air','WATER_PUMP',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(48,4,'Kipas Pendingin','COOLING_FAN',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(49,4,'Sistem Pelumasan Mesin','ENGINE_LUB_SYS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(50,4,'Pompa Oli Mesin','ENGINE_OIL_PUMP',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(51,4,'Filter Oli Mesin','ENGINE_OIL_FILTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(52,4,'Oil Pan','OIL_PAN',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(53,4,'Motor Elektrik','ELECTRIC_MOTOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(54,4,'Motor Traksi','TRACTION_MOTOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(55,4,'Motor Hidrolik','HYDRAULIC_MOTOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(56,4,'Motor Kemudi','STEERING_MOTOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(57,4,'Poros Penggerak (Drive Shaft)','DRIVE_SHAFT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(58,5,'Pompa Hidrolik','HYD_PUMP',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(59,5,'Tangki Hidrolik','HYD_TANK',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(60,5,'Filter Oli Hidrolik','HYD_FILTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(61,5,'Control Valve','CONTROL_VALVE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(62,5,'Directional Control Valve','DIR_CONTROL_VALVE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(63,5,'Pressure Relief Valve','PRESSURE_RELIEF',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(64,5,'Flow Control Valve','FLOW_CONTROL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(65,5,'Silinder Hidrolik','HYD_CYLINDER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(66,5,'Lift Cylinder','LIFT_CYLINDER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(67,5,'Tilt Cylinder','TILT_CYLINDER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(68,5,'Steering Cylinder','STEERING_CYL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(69,5,'Side Shift Cylinder','SIDE_SHIFT_CYL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(70,5,'Attachment Cylinder','ATTACH_CYL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(71,5,'Selang Hidrolik','HYD_HOSE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(72,5,'Fitting dan Konektor Hidrolik','HYD_FITTING',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(73,5,'Steering Wheel','STEERING_WHEEL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(74,5,'Steering Column','STEERING_COLUMN',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(75,5,'Steering Linkage','STEERING_LINKAGE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(76,5,'Power Steering System','POWER_STEERING_SYS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(77,5,'Power Steering Pump','POWER_STEERING_PUMP',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(78,5,'Power Steering Fluid Reservoir','PS_RESERVOIR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(79,5,'Power Steering Cylinder','PS_CYLINDER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(80,5,'Selang Power Steering','PS_HOSE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(81,5,'Steering Axle','STEERING_AXLE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(82,5,'Knuckle','KNUCKLE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(83,6,'Baterai (Aki)','BATTERY',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(84,6,'Kabel-kabel (Wiring Harness)','WIRING_HARNESS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(85,6,'Fuse (Sekering)','FUSE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(86,6,'Relay','RELAY',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(87,6,'Switch (Saklar)','SWITCH',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(88,6,'Lampu Depan','HEADLIGHT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(89,6,'Lampu Belakang','TAILLIGHT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(90,6,'Lampu Sein','TURN_SIGNAL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(91,6,'Lampu Peringatan','WARNING_LIGHT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(92,6,'Lampu Kerja','WORK_LIGHT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(93,6,'Klakson','HORN',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(94,6,'Sensor Suhu','TEMP_SENSOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(95,6,'Sensor Tekanan Oli','OIL_PRESSURE_SENSOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(96,6,'Sensor Level Bahan Bakar','FUEL_LEVEL_SENSOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(97,6,'Sensor Kecepatan','SPEED_SENSOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(98,6,'Sensor Posisi','POSITION_SENSOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(99,6,'Instrument Cluster','INSTRUMENT_CLUSTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(100,6,'Sistem Pengisian Daya Baterai','BATTERY_CHARGING_SYS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(101,6,'ECU / Modul Kontrol','ECU',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(102,6,'Sistem Manajemen Baterai (BMS)','BMS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(103,7,'Sistem Bahan Bakar','FUEL_SYSTEM',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(104,7,'Tangki Bahan Bakar','FUEL_TANK',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(105,7,'Saluran Bahan Bakar','FUEL_LINE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(106,7,'Pompa Bahan Bakar','FUEL_PUMP',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(107,7,'Filter Bahan Bakar','FUEL_FILTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(108,7,'Injector (Diesel/LPG)','INJECTOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(109,7,'Karburator (LPG/Bensin)','CARBURETOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(110,7,'Regulator Tekanan Bahan Bakar','FUEL_REGULATOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(111,7,'Sistem Udara Masuk','AIR_INTAKE_SYS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(112,7,'Air Filter','AIR_FILTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(113,7,'Intake Manifold','INTAKE_MANIFOLD',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(114,7,'Turbocharger','TURBOCHARGER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(115,7,'Sistem Pembuangan','EXHAUST_SYSTEM',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(116,7,'Exhaust Manifold','EXHAUST_MANIFOLD',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(117,7,'Pipa Knalpot','EXHAUST_PIPE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(118,7,'Muffler','MUFFLER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(119,7,'Sistem Pengapian (Bensin/LPG)','IGNITION_SYSTEM',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(120,7,'Busi','SPARK_PLUG',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(121,7,'Coil Pengapian','IGNITION_COIL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(122,7,'Distributor','DISTRIBUTOR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(123,8,'Ban','TIRE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(124,8,'Velg','RIM',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(125,8,'Baut Roda (Lug Nuts)','LUG_NUTS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(126,9,'Seat Belt','SEAT_BELT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(127,9,'Alarm Mundur','REVERSE_ALARM',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(128,9,'Operator Presence System (OPS)','OPS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(129,9,'Emergency Stop Button','EMERGENCY_STOP',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(130,9,'Fire Extinguisher (APAR)','FIRE_EXTINGUISHER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(131,9,'Mirror (Spion)','MIRROR',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(132,10,'Gearbox','GEARBOX',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(133,10,'Kopling (Clutch)','CLUTCH',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(134,10,'Torque Converter','TORQUE_CONVERTER',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(135,10,'Final Drive (Gardan)','FINAL_DRIVE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(136,10,'Poros Penggerak (Drive Shaft)','DRIVE_SHAFT_TRANS',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(137,11,'Oli Mesin','ENGINE_OIL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(138,11,'Oli Transmisi','TRANSMISSION_OIL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(139,11,'Oli Hidrolik','HYDRAULIC_OIL',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(140,11,'Minyak Rem','BRAKE_FLUID',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(141,11,'Coolant (Cairan Pendingin)','COOLANT',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36'),(142,11,'Grease (Gemuk Pelumas untuk bagian bergerak)','GREASE',NULL,1,'2025-09-23 08:24:36','2025-09-23 08:24:36');
/*!40000 ALTER TABLE `work_order_subcategories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `work_orders`
--

DROP TABLE IF EXISTS `work_orders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `work_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `work_order_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `report_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `unit_id` int unsigned NOT NULL,
  `order_type` enum('COMPLAINT','PMPS','FABRIKASI','PERSIAPAN') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'COMPLAINT',
  `priority_id` int NOT NULL,
  `requested_repair_time` datetime DEFAULT NULL,
  `category_id` int NOT NULL,
  `subcategory_id` int DEFAULT NULL,
  `complaint_description` text COLLATE utf8mb4_general_ci NOT NULL,
  `status_id` int NOT NULL,
  `admin_id` int DEFAULT NULL,
  `foreman_id` int DEFAULT NULL,
  `mechanic_id` int DEFAULT NULL,
  `helper_id` int DEFAULT NULL,
  `repair_description` text COLLATE utf8mb4_general_ci,
  `notes` text COLLATE utf8mb4_general_ci,
  `sparepart_used` text COLLATE utf8mb4_general_ci,
  `time_to_repair` decimal(5,2) DEFAULT NULL,
  `completion_date` datetime DEFAULT NULL,
  `area` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pic` text COLLATE utf8mb4_general_ci COMMENT 'PIC (Nama dan No Telp)',
  `hm` int DEFAULT NULL COMMENT 'Hour Meter',
  `created_by` int NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `unit_verified` tinyint(1) DEFAULT '0' COMMENT '1 if unit verification completed',
  `unit_verified_at` timestamp NULL DEFAULT NULL COMMENT 'When unit verification was completed',
  `sparepart_validated` tinyint(1) DEFAULT '0' COMMENT '1 if sparepart validation completed',
  `sparepart_validated_at` timestamp NULL DEFAULT NULL COMMENT 'When sparepart validation was completed',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_work_order_number` (`work_order_number`),
  KEY `fk_wo_unit` (`unit_id`),
  KEY `fk_wo_priority` (`priority_id`),
  KEY `fk_wo_category` (`category_id`),
  KEY `fk_wo_subcategory` (`subcategory_id`),
  KEY `fk_wo_status` (`status_id`),
  KEY `fk_wo_created_by` (`created_by`),
  KEY `idx_wo_report_date` (`report_date`),
  KEY `idx_wo_order_type` (`order_type`),
  KEY `fk_wo_admin_staff` (`admin_id`),
  KEY `fk_wo_foreman_staff` (`foreman_id`),
  KEY `fk_wo_mechanic_staff` (`mechanic_id`),
  KEY `fk_wo_helper_staff` (`helper_id`),
  KEY `idx_wo_pic` (`pic`(100)),
  KEY `idx_wo_hm` (`hm`),
  KEY `idx_wo_unit_verified` (`unit_verified`),
  KEY `idx_wo_sparepart_validated` (`sparepart_validated`),
  CONSTRAINT `fk_wo_admin_employee` FOREIGN KEY (`admin_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_category` FOREIGN KEY (`category_id`) REFERENCES `work_order_categories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_created_by` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_foreman_employee` FOREIGN KEY (`foreman_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_helper_employee` FOREIGN KEY (`helper_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_mechanic_employee` FOREIGN KEY (`mechanic_id`) REFERENCES `employees` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_priority` FOREIGN KEY (`priority_id`) REFERENCES `work_order_priorities` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_status` FOREIGN KEY (`status_id`) REFERENCES `work_order_statuses` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `work_order_subcategories` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `fk_wo_unit` FOREIGN KEY (`unit_id`) REFERENCES `inventory_unit` (`id_inventory_unit`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `work_orders`
--

LOCK TABLES `work_orders` WRITE;
/*!40000 ALTER TABLE `work_orders` DISABLE KEYS */;
INSERT INTO `work_orders` VALUES (1,'15059','2025-09-18 08:52:52',1,'COMPLAINT',2,'2025-09-18 09:00:00',8,1,'Ban depan belakang gundul',6,1,4,7,13,'Ban depan belakang gundul','Unit verification test completed successfully','Ban hidup 700-12, ban hidup 600-9',NULL,'2025-10-03 02:17:46','PURWAKARTA',NULL,NULL,1,'2025-09-23 08:24:36','2025-10-02 19:17:46','2025-09-24 18:52:30',0,NULL,0,NULL),(5,'15060','2025-09-24 10:05:39',1,'COMPLAINT',2,NULL,1,NULL,'Test complaint description for work order creation',6,NULL,NULL,NULL,NULL,NULL,'Unit verification test completed successfully',NULL,NULL,'2025-10-03 02:17:52',NULL,NULL,NULL,1,'2025-09-24 03:05:39','2025-10-02 19:17:52',NULL,0,NULL,0,NULL),(6,'15061','2025-09-24 10:06:37',2,'PMPS',1,NULL,1,NULL,'Testing second work order creation with all helpers enabled',6,NULL,NULL,NULL,NULL,NULL,'Unit verification test completed successfully',NULL,NULL,'2025-10-03 02:17:58',NULL,NULL,NULL,1,'2025-09-24 03:06:37','2025-10-02 19:17:58',NULL,0,NULL,0,NULL),(7,'15062','2025-09-24 10:11:17',1,'COMPLAINT',2,NULL,1,NULL,'Testing form data submission',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-09-24 03:11:17','2025-09-24 03:11:17',NULL,0,NULL,0,NULL),(8,'15063','2025-09-24 10:11:27',2,'PMPS',1,NULL,1,NULL,'Testing JSON submission',6,NULL,NULL,NULL,NULL,NULL,'Unit verification test completed successfully',NULL,NULL,'2025-10-03 02:15:24',NULL,NULL,NULL,1,'2025-09-24 03:11:27','2025-10-02 19:15:24',NULL,0,NULL,0,NULL),(9,'15064','2025-09-25 01:37:24',1,'COMPLAINT',2,NULL,1,NULL,'Test complaint with form data',7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-09-24 18:37:24','2025-10-01 01:46:07',NULL,0,NULL,0,NULL),(10,'15065','2025-09-25 01:40:02',1,'COMPLAINT',2,NULL,1,NULL,'Test validation successful',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-09-24 18:40:02','2025-09-24 18:43:49','2025-09-24 18:43:49',0,NULL,0,NULL),(11,'15066','2025-09-25 01:55:12',1,'COMPLAINT',2,NULL,1,NULL,'Test complaint dari curl untuk debug',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,1,'2025-09-24 18:55:12','2025-09-24 19:19:38','2025-09-24 19:19:38',0,NULL,0,NULL),(12,'15067','2025-09-25 02:07:46',11,'COMPLAINT',2,NULL,7,121,'RUSAK',1,3,4,7,16,NULL,NULL,NULL,NULL,NULL,'BEKASI',NULL,NULL,1,'2025-09-24 19:07:46','2025-09-24 19:12:04','2025-09-24 19:12:04',0,NULL,0,NULL),(13,'15068','2025-09-25 02:15:04',11,'COMPLAINT',2,NULL,7,121,'Test delete dari modal',1,3,4,7,16,NULL,NULL,NULL,NULL,NULL,'BEKASI',NULL,NULL,1,'2025-09-24 19:15:04','2025-09-24 19:15:23','2025-09-24 19:15:23',0,NULL,0,NULL),(14,'15069','2025-10-01 10:04:49',1,'COMPLAINT',2,NULL,3,38,'awdawdaw',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TANGERANG',NULL,NULL,1,'2025-10-01 03:04:49','2025-10-01 03:04:49',NULL,0,NULL,0,NULL),(15,'15070','2025-10-02 08:37:26',4,'COMPLAINT',1,NULL,3,NULL,'test complaint description',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TANGERANG',NULL,NULL,1,'2025-10-02 01:37:26','2025-10-02 01:37:26',NULL,0,NULL,0,NULL),(17,'15071','2025-10-02 08:39:07',4,'COMPLAINT',1,NULL,3,NULL,'test complaint description',1,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TANGERANG',NULL,NULL,1,'2025-10-02 01:39:07','2025-10-02 01:39:07',NULL,0,NULL,0,NULL),(18,'15072','2025-10-02 08:39:36',2,'COMPLAINT',2,NULL,3,30,'awdawdawdawdawd',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bekasi',NULL,NULL,1,'2025-10-02 01:39:36','2025-10-02 07:52:23',NULL,0,NULL,0,NULL),(19,'15073','2025-10-02 08:40:23',4,'COMPLAINT',1,NULL,3,NULL,'test complaint description',3,NULL,NULL,7,10,NULL,'Test multiple assignment',NULL,NULL,NULL,'TANGERANG',NULL,NULL,1,'2025-10-02 01:40:23','2025-10-02 03:04:19',NULL,0,NULL,0,NULL),(20,'15074','2025-10-02 08:44:51',14,'COMPLAINT',2,NULL,3,28,'awdawdawdawd',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'PUSAT CIKARANG',NULL,NULL,1,'2025-10-02 01:44:51','2025-10-02 07:39:00',NULL,0,NULL,0,NULL),(22,'15075','2025-10-02 14:29:33',1,'COMPLAINT',1,NULL,1,NULL,'Test complaint for integrated staff assignment',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bekasi',NULL,NULL,1,'2025-10-02 07:29:33','2025-10-02 07:29:33',NULL,0,NULL,0,NULL),(23,'15076','2025-10-02 14:37:14',1,'COMPLAINT',1,NULL,1,NULL,'Test after cleanup',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'Bekasi',NULL,NULL,1,'2025-10-02 07:37:14','2025-10-02 07:37:14',NULL,0,NULL,0,NULL),(24,'15077','2025-10-02 14:39:46',10,'COMPLAINT',2,NULL,2,17,'adawdawdaw',3,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TANGERANG',NULL,NULL,1,'2025-10-02 07:39:46','2025-10-02 07:39:46',NULL,0,NULL,0,NULL),(25,'15078','2025-10-02 14:49:57',1,'COMPLAINT',1,NULL,1,NULL,'Test corrected flow - should be OPEN status',6,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-07 06:51:23','Bekasi',NULL,NULL,1,'2025-10-02 07:49:57','2025-10-06 23:51:23',NULL,0,NULL,0,NULL),(26,'15079','2025-10-02 14:51:01',2,'COMPLAINT',2,NULL,2,18,'ASQSASDAASDASDA',7,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-06 02:11:25','Bekasi',NULL,NULL,1,'2025-10-02 07:51:01','2025-10-05 20:01:52',NULL,0,NULL,0,NULL),(27,'15080','2025-10-03 08:20:39',7,'COMPLAINT',2,NULL,2,19,'awdwdawdawdawdawdawd',3,3,4,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'TANGERANG',NULL,NULL,1,'2025-10-03 01:20:39','2025-10-07 22:50:43',NULL,0,NULL,0,NULL),(29,'15081','2025-10-03 08:47:57',7,'COMPLAINT',2,NULL,2,19,'awdawdasdawdawdawdawdawd',7,3,4,NULL,NULL,NULL,NULL,NULL,NULL,'2025-10-07 06:40:19','TANGERANG',NULL,NULL,1,'2025-10-03 01:47:57','2025-11-21 02:11:01',NULL,0,NULL,0,NULL),(30,'15082','2025-10-03 08:51:23',7,'COMPLAINT',2,NULL,2,19,'awdawdasdadwadaw',1,3,4,8,15,NULL,NULL,NULL,NULL,NULL,'TANGERANG',NULL,NULL,1,'2025-10-03 01:51:23','2025-10-03 01:51:23',NULL,0,NULL,0,NULL),(31,'15083','2025-10-03 09:28:05',7,'COMPLAINT',2,NULL,2,19,'wadawdasdawdawdaw',1,3,4,8,15,NULL,NULL,NULL,NULL,NULL,'TANGERANG','Adit',NULL,1,'2025-10-03 02:28:05','2025-10-03 02:28:05',NULL,0,NULL,0,NULL),(32,'15084','2025-10-04 01:55:19',10,'COMPLAINT',2,NULL,2,19,'awdawdasdawdawdawdawdawdaw',7,3,4,8,15,NULL,NULL,NULL,NULL,'2025-11-20 13:39:38','TANGERANG','Adit',NULL,1,'2025-10-03 18:55:19','2025-11-20 06:40:19',NULL,0,NULL,0,NULL),(33,'15085','2025-10-04 01:55:51',4,'COMPLAINT',2,NULL,2,19,'awdawdawdawdawdawd',7,3,4,8,15,NULL,NULL,NULL,NULL,'2025-10-05 14:23:21','TANGERANG','Joko',NULL,1,'2025-10-03 18:55:51','2025-10-05 18:32:13',NULL,0,NULL,0,NULL),(34,'15086','2025-10-04 02:04:14',7,'COMPLAINT',2,NULL,3,38,'awdawdasdadwadaw',7,3,4,8,15,NULL,NULL,NULL,NULL,'2025-10-05 13:58:20','TANGERANG','Adit',NULL,1,'2025-10-03 19:04:14','2025-10-05 07:15:23',NULL,0,NULL,0,NULL),(35,'15087','2025-10-07 06:58:34',17,'COMPLAINT',2,NULL,2,19,'awdawdadawdawdawdawdawd',7,NULL,NULL,9,14,NULL,NULL,NULL,NULL,'2025-11-21 09:59:19','JATIM',NULL,NULL,1,'2025-10-06 23:58:34','2025-11-21 03:02:49',NULL,1,'2025-11-21 02:59:19',0,NULL);
/*!40000 ALTER TABLE `work_orders` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_work_order_before_insert` BEFORE INSERT ON `work_orders` FOR EACH ROW BEGIN
    DECLARE v_wo_number VARCHAR(50);
    
    
    IF NEW.work_order_number IS NULL OR NEW.work_order_number = '' THEN
        CALL sp_generate_work_order_number(v_wo_number);
        SET NEW.work_order_number = v_wo_number;
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
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `tr_work_order_after_insert` AFTER INSERT ON `work_orders` FOR EACH ROW BEGIN
    
    INSERT INTO work_order_status_history 
    (work_order_id, from_status_id, to_status_id, changed_by, change_reason)
    VALUES 
    (NEW.id, NULL, NEW.status_id, NEW.created_by, 'Work order created');
    
    
    INSERT IGNORE INTO system_activity_log 
    (user_id, action_type, table_name, record_id, action_description, module_name, created_at)
    VALUES 
    (NEW.created_by, 'CREATE', 'work_orders', NEW.id, CONCAT('Work order ', NEW.work_order_number, ' created'), 'SERVICE', NOW());
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `contract_unit_summary`
--

/*!50001 DROP VIEW IF EXISTS `contract_unit_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `contract_unit_summary` AS select `k`.`id` AS `kontrak_id`,`k`.`no_kontrak` AS `no_kontrak`,`c`.`customer_name` AS `pelanggan`,`cl`.`location_name` AS `lokasi`,`cl`.`address` AS `alamat_lengkap`,`k`.`status` AS `kontrak_status`,`k`.`tanggal_mulai` AS `tanggal_mulai`,`k`.`tanggal_berakhir` AS `tanggal_berakhir`,`k`.`total_units` AS `kontrak_total_units`,count(`iu`.`id_inventory_unit`) AS `active_units`,count((case when (`iu`.`workflow_status` like '%TARIK%') then 1 end)) AS `tarik_units`,count((case when (`iu`.`workflow_status` like '%TUKAR%') then 1 end)) AS `tukar_units`,count((case when (`su`.`status_unit` in ('DISEWA','BEROPERASI')) then 1 end)) AS `operational_units`,count((case when (`iu`.`workflow_status` is not null) then 1 end)) AS `workflow_units`,`k`.`nilai_total` AS `nilai_total`,`k`.`jenis_sewa` AS `jenis_sewa`,`k`.`dibuat_pada` AS `created_at` from ((((`kontrak` `k` left join `customer_locations` `cl` on((`k`.`customer_location_id` = `cl`.`id`))) left join `customers` `c` on((`cl`.`customer_id` = `c`.`id`))) left join `inventory_unit` `iu` on((`k`.`id` = `iu`.`kontrak_id`))) left join `status_unit` `su` on((`iu`.`status_unit_id` = `su`.`id_status`))) group by `k`.`id`,`k`.`no_kontrak`,`c`.`customer_name`,`cl`.`location_name`,`cl`.`address`,`k`.`status`,`k`.`tanggal_mulai`,`k`.`tanggal_berakhir`,`k`.`total_units`,`k`.`nilai_total`,`k`.`jenis_sewa`,`k`.`dibuat_pada` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `inventory_unit_components`
--

/*!50001 DROP VIEW IF EXISTS `inventory_unit_components`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `inventory_unit_components` AS select `iu`.`id_inventory_unit` AS `id_inventory_unit`,`iu`.`no_unit` AS `no_unit`,`iu`.`serial_number` AS `serial_number`,`ia_battery`.`baterai_id` AS `model_baterai_id`,`ia_battery`.`sn_baterai` AS `sn_baterai`,`b`.`merk_baterai` AS `merk_baterai`,`b`.`tipe_baterai` AS `tipe_baterai`,`b`.`jenis_baterai` AS `jenis_baterai`,`ia_charger`.`charger_id` AS `model_charger_id`,`ia_charger`.`sn_charger` AS `sn_charger`,`c`.`merk_charger` AS `merk_charger`,`c`.`tipe_charger` AS `tipe_charger`,`ia_attachment`.`attachment_id` AS `model_attachment_id`,`ia_attachment`.`sn_attachment` AS `sn_attachment`,`a`.`tipe` AS `attachment_tipe`,`a`.`merk` AS `attachment_merk`,`a`.`model` AS `attachment_model` from ((((((`inventory_unit` `iu` left join `inventory_attachment` `ia_battery` on(((`iu`.`id_inventory_unit` = `ia_battery`.`id_inventory_unit`) and (`ia_battery`.`tipe_item` = 'battery') and (`ia_battery`.`status_unit` = 8)))) left join `baterai` `b` on((`ia_battery`.`baterai_id` = `b`.`id`))) left join `inventory_attachment` `ia_charger` on(((`iu`.`id_inventory_unit` = `ia_charger`.`id_inventory_unit`) and (`ia_charger`.`tipe_item` = 'charger') and (`ia_charger`.`status_unit` = 8)))) left join `charger` `c` on((`ia_charger`.`charger_id` = `c`.`id_charger`))) left join `inventory_attachment` `ia_attachment` on(((`iu`.`id_inventory_unit` = `ia_attachment`.`id_inventory_unit`) and (`ia_attachment`.`tipe_item` = 'attachment') and (`ia_attachment`.`status_unit` = 8)))) left join `attachment` `a` on((`ia_attachment`.`attachment_id` = `a`.`id_attachment`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `unit_workflow_status`
--

/*!50001 DROP VIEW IF EXISTS `unit_workflow_status`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `unit_workflow_status` AS select `iu`.`id_inventory_unit` AS `id_inventory_unit`,`iu`.`no_unit` AS `no_unit`,`su`.`status_unit` AS `current_status`,`iu`.`workflow_status` AS `workflow_status`,`iu`.`di_workflow_id` AS `di_workflow_id`,`iu`.`kontrak_id` AS `kontrak_id`,`k`.`no_kontrak` AS `no_kontrak`,`c`.`customer_name` AS `pelanggan`,`di`.`nomor_di` AS `nomor_di`,`di`.`status_di` AS `di_status`,`jpk`.`kode` AS `jenis_perintah`,`tpk`.`kode` AS `tujuan_perintah`,(case when (`iu`.`workflow_status` is not null) then 'IN_WORKFLOW' when (`su`.`status_unit` in ('DISEWA','BEROPERASI')) then 'OPERATIONAL' when (`su`.`status_unit` in ('TERSEDIA','STOCK')) then 'AVAILABLE' else 'OTHER' end) AS `workflow_category` from (((((((`inventory_unit` `iu` left join `status_unit` `su` on((`iu`.`status_unit_id` = `su`.`id_status`))) left join `kontrak` `k` on((`iu`.`kontrak_id` = `k`.`id`))) left join `customer_locations` `cl` on((`k`.`customer_location_id` = `cl`.`id`))) left join `customers` `c` on((`cl`.`customer_id` = `c`.`id`))) left join `delivery_instructions` `di` on((`iu`.`di_workflow_id` = `di`.`id`))) left join `jenis_perintah_kerja` `jpk` on((`di`.`jenis_perintah_kerja_id` = `jpk`.`id`))) left join `tujuan_perintah_kerja` `tpk` on((`di`.`tujuan_perintah_kerja_id` = `tpk`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `user_all_permissions`
--

/*!50001 DROP VIEW IF EXISTS `user_all_permissions`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `user_all_permissions` AS select distinct `u`.`id` AS `user_id`,`u`.`username` AS `username`,`u`.`email` AS `email`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`d`.`name` AS `division_name`,`p`.`id` AS `permission_id`,`p`.`name` AS `permission_name`,`p`.`key` AS `permission_key`,`p`.`module` AS `module`,`p`.`category` AS `category`,'role' AS `source_type`,`r`.`name` AS `source_name`,`rp`.`granted` AS `granted` from (((((`users` `u` left join `user_roles` `ur` on(((`u`.`id` = `ur`.`user_id`) and (`ur`.`is_active` = 1)))) left join `roles` `r` on(((`ur`.`role_id` = `r`.`id`) and (`r`.`is_active` = 1)))) left join `role_permissions` `rp` on(((`r`.`id` = `rp`.`role_id`) and (`rp`.`granted` = 1)))) left join `permissions` `p` on(((`rp`.`permission_id` = `p`.`id`) and (`p`.`is_active` = 1)))) left join `divisions` `d` on((`u`.`division_id` = `d`.`id`))) where (`u`.`is_active` = 1) union all select distinct `u`.`id` AS `user_id`,`u`.`username` AS `username`,`u`.`email` AS `email`,`u`.`first_name` AS `first_name`,`u`.`last_name` AS `last_name`,`d`.`name` AS `division_name`,`p`.`id` AS `permission_id`,`p`.`name` AS `permission_name`,`p`.`key` AS `permission_key`,`p`.`module` AS `module`,`p`.`category` AS `category`,'direct' AS `source_type`,'Direct Assignment' AS `source_name`,`up`.`granted` AS `granted` from (((`users` `u` left join `user_permissions` `up` on(((`u`.`id` = `up`.`user_id`) and ((`up`.`expires_at` is null) or (`up`.`expires_at` > now()))))) left join `permissions` `p` on(((`up`.`permission_id` = `p`.`id`) and (`p`.`is_active` = 1)))) left join `divisions` `d` on((`u`.`division_id` = `d`.`id`))) where (`u`.`is_active` = 1) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_activity_log_relations`
--

/*!50001 DROP VIEW IF EXISTS `v_activity_log_relations`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_activity_log_relations` AS select `system_activity_log`.`id` AS `id`,`system_activity_log`.`table_name` AS `table_name`,`system_activity_log`.`record_id` AS `record_id`,`system_activity_log`.`action_type` AS `action_type`,`system_activity_log`.`action_description` AS `action_description`,`system_activity_log`.`module_name` AS `module_name`,`system_activity_log`.`submenu_item` AS `submenu_item`,`system_activity_log`.`workflow_stage` AS `workflow_stage`,`system_activity_log`.`business_impact` AS `business_impact`,`system_activity_log`.`user_id` AS `user_id`,`system_activity_log`.`created_at` AS `created_at`,`system_activity_log`.`related_entities` AS `related_entities`,(case when (json_valid(`system_activity_log`.`related_entities`) = 1) then json_extract(`system_activity_log`.`related_entities`,'$.kontrak') else NULL end) AS `related_kontrak`,(case when (json_valid(`system_activity_log`.`related_entities`) = 1) then json_extract(`system_activity_log`.`related_entities`,'$.spk') else NULL end) AS `related_spk`,(case when (json_valid(`system_activity_log`.`related_entities`) = 1) then json_extract(`system_activity_log`.`related_entities`,'$.di') else NULL end) AS `related_di`,(case when (json_valid(`system_activity_log`.`related_entities`) = 1) then json_extract(`system_activity_log`.`related_entities`,'$.po') else NULL end) AS `related_po` from `system_activity_log` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_spk_fabrikasi`
--

/*!50001 DROP VIEW IF EXISTS `v_spk_fabrikasi`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_spk_fabrikasi` AS select `s`.`id` AS `spk_id`,`s`.`nomor_spk` AS `nomor_spk`,`sus`.`unit_index` AS `unit_index`,`sus`.`attachment_inventory_attachment_id` AS `fabrikasi_attachment_id`,`sus`.`mekanik` AS `fabrikasi_mekanik`,`sus`.`estimasi_mulai` AS `fabrikasi_estimasi_mulai`,`sus`.`estimasi_selesai` AS `fabrikasi_estimasi_selesai`,`sus`.`tanggal_approve` AS `fabrikasi_tanggal_approve` from (`spk` `s` join `spk_unit_stages` `sus` on((`s`.`id` = `sus`.`spk_id`))) where ((`sus`.`stage_name` = 'fabrikasi') and (`sus`.`tanggal_approve` is not null)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_spk_painting`
--

/*!50001 DROP VIEW IF EXISTS `v_spk_painting`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_spk_painting` AS select `s`.`id` AS `spk_id`,`s`.`nomor_spk` AS `nomor_spk`,`sus`.`unit_index` AS `unit_index`,`sus`.`mekanik` AS `painting_mekanik`,`sus`.`estimasi_mulai` AS `painting_estimasi_mulai`,`sus`.`estimasi_selesai` AS `painting_estimasi_selesai`,`sus`.`tanggal_approve` AS `painting_tanggal_approve` from (`spk` `s` join `spk_unit_stages` `sus` on((`s`.`id` = `sus`.`spk_id`))) where ((`sus`.`stage_name` = 'painting') and (`sus`.`tanggal_approve` is not null)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_spk_pdi`
--

/*!50001 DROP VIEW IF EXISTS `v_spk_pdi`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_spk_pdi` AS select `s`.`id` AS `spk_id`,`s`.`nomor_spk` AS `nomor_spk`,`sus`.`unit_index` AS `unit_index`,`sus`.`mekanik` AS `pdi_mekanik`,`sus`.`estimasi_mulai` AS `pdi_estimasi_mulai`,`sus`.`estimasi_selesai` AS `pdi_estimasi_selesai`,`sus`.`tanggal_approve` AS `pdi_tanggal_approve`,`sus`.`catatan` AS `pdi_catatan` from (`spk` `s` join `spk_unit_stages` `sus` on((`s`.`id` = `sus`.`spk_id`))) where ((`sus`.`stage_name` = 'pdi') and (`sus`.`tanggal_approve` is not null)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_spk_persiapan_unit`
--

/*!50001 DROP VIEW IF EXISTS `v_spk_persiapan_unit`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_spk_persiapan_unit` AS select `s`.`id` AS `spk_id`,`s`.`nomor_spk` AS `nomor_spk`,`sus`.`unit_index` AS `unit_index`,`sus`.`unit_id` AS `persiapan_unit_id`,`sus`.`area_id` AS `area_id`,`sus`.`mekanik` AS `persiapan_unit_mekanik`,`sus`.`estimasi_mulai` AS `persiapan_unit_estimasi_mulai`,`sus`.`estimasi_selesai` AS `persiapan_unit_estimasi_selesai`,`sus`.`tanggal_approve` AS `persiapan_unit_tanggal_approve`,`sus`.`aksesoris_tersedia` AS `persiapan_aksesoris_tersedia`,`sus`.`battery_inventory_attachment_id` AS `battery_inventory_attachment_id`,`sus`.`charger_inventory_attachment_id` AS `charger_inventory_attachment_id`,`sus`.`no_unit_action` AS `no_unit_action`,`sus`.`update_no_unit` AS `update_no_unit` from (`spk` `s` join `spk_unit_stages` `sus` on((`s`.`id` = `sus`.`spk_id`))) where ((`sus`.`stage_name` = 'persiapan_unit') and (`sus`.`tanggal_approve` is not null)) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_spk_rollback_status`
--

/*!50001 DROP VIEW IF EXISTS `v_spk_rollback_status`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_spk_rollback_status` AS select `s`.`id` AS `id`,`s`.`nomor_spk` AS `nomor_spk`,`s`.`status` AS `status`,`s`.`rollback_enabled` AS `rollback_enabled`,`s`.`rollback_count` AS `rollback_count`,`s`.`last_rollback_at` AS `last_rollback_at`,(case when ((`s`.`status` = 'COMPLETED') or (`s`.`status` = 'DELIVERED')) then 0 when (`s`.`rollback_count` >= 3) then 0 when (`s`.`rollback_enabled` = 0) then 0 else 1 end) AS `can_rollback`,(case when (not exists(select 1 from `spk_unit_stages` `sus` where ((`sus`.`spk_id` = `s`.`id`) and (`sus`.`stage_name` = 'persiapan_unit') and (`sus`.`tanggal_approve` is not null)))) then 'persiapan_unit' when (not exists(select 1 from `spk_unit_stages` `sus` where ((`sus`.`spk_id` = `s`.`id`) and (`sus`.`stage_name` = 'fabrikasi') and (`sus`.`tanggal_approve` is not null)))) then 'fabrikasi' when (not exists(select 1 from `spk_unit_stages` `sus` where ((`sus`.`spk_id` = `s`.`id`) and (`sus`.`stage_name` = 'painting') and (`sus`.`tanggal_approve` is not null)))) then 'painting' when (not exists(select 1 from `spk_unit_stages` `sus` where ((`sus`.`spk_id` = `s`.`id`) and (`sus`.`stage_name` = 'pdi') and (`sus`.`tanggal_approve` is not null)))) then 'pdi' else 'completed' end) AS `current_stage`,(select count(0) from `spk_rollback_log` where (`spk_rollback_log`.`spk_id` = `s`.`id`)) AS `total_rollbacks` from `spk` `s` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_unit_status_workflow`
--

/*!50001 DROP VIEW IF EXISTS `v_unit_status_workflow`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `v_unit_status_workflow` AS select `iu`.`id_inventory_unit` AS `id_inventory_unit`,`iu`.`no_unit` AS `no_unit`,`iu`.`serial_number` AS `serial_number`,`mu`.`merk_unit` AS `merk_unit`,`mu`.`model_unit` AS `model_unit`,`su`.`status_unit` AS `current_status`,`iu`.`status_unit_id` AS `current_status_id`,`k`.`no_kontrak` AS `no_kontrak`,`k`.`status` AS `kontrak_status`,`c`.`customer_name` AS `customer_name`,`cl`.`location_name` AS `location_name`,`usl`.`reason` AS `last_change_reason`,`usl`.`triggered_by` AS `last_triggered_by`,`usl`.`created_at` AS `last_status_change`,`spk`.`nomor_spk` AS `nomor_spk`,`spk`.`status` AS `spk_status`,`di`.`nomor_di` AS `nomor_di`,`di`.`status_di` AS `di_status` from ((((((((`inventory_unit` `iu` left join `model_unit` `mu` on((`iu`.`model_unit_id` = `mu`.`id_model_unit`))) left join `status_unit` `su` on((`iu`.`status_unit_id` = `su`.`id_status`))) left join `kontrak` `k` on((`iu`.`kontrak_id` = `k`.`id`))) left join `customer_locations` `cl` on((`k`.`customer_location_id` = `cl`.`id`))) left join `customers` `c` on((`cl`.`customer_id` = `c`.`id`))) left join (select `unit_status_log`.`inventory_unit_id` AS `inventory_unit_id`,`unit_status_log`.`reason` AS `reason`,`unit_status_log`.`triggered_by` AS `triggered_by`,`unit_status_log`.`created_at` AS `created_at`,row_number() OVER (PARTITION BY `unit_status_log`.`inventory_unit_id` ORDER BY `unit_status_log`.`created_at` desc )  AS `rn` from `unit_status_log`) `usl` on(((`iu`.`id_inventory_unit` = `usl`.`inventory_unit_id`) and (`usl`.`rn` = 1)))) left join (select `su`.`unit_id` AS `unit_id`,`spk`.`nomor_spk` AS `nomor_spk`,`spk`.`status` AS `status`,row_number() OVER (PARTITION BY `su`.`unit_id` ORDER BY `spk`.`dibuat_pada` desc )  AS `rn` from (`spk_units` `su` join `spk` on((`su`.`spk_id` = `spk`.`id`)))) `spk` on(((`iu`.`id_inventory_unit` = `spk`.`unit_id`) and (`spk`.`rn` = 1)))) left join `delivery_instructions` `di` on((`iu`.`delivery_instruction_id` = `di`.`id`))) order by `iu`.`id_inventory_unit` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `view_spk_workflow`
--

/*!50001 DROP VIEW IF EXISTS `view_spk_workflow`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `view_spk_workflow` AS select `s`.`id` AS `id`,`s`.`nomor_spk` AS `nomor_spk`,`s`.`jenis_spk` AS `jenis_spk`,`s`.`kontrak_id` AS `kontrak_id`,`s`.`kontrak_spesifikasi_id` AS `kontrak_spesifikasi_id`,`s`.`jumlah_unit` AS `jumlah_unit`,`s`.`po_kontrak_nomor` AS `po_kontrak_nomor`,`s`.`pelanggan` AS `pelanggan`,`s`.`pic` AS `pic`,`s`.`kontak` AS `kontak`,`s`.`lokasi` AS `lokasi`,`s`.`delivery_plan` AS `delivery_plan`,`s`.`spesifikasi` AS `spesifikasi`,`s`.`status` AS `status`,`s`.`catatan` AS `catatan`,`s`.`dibuat_oleh` AS `dibuat_oleh`,`s`.`dibuat_pada` AS `dibuat_pada`,`s`.`diperbarui_pada` AS `diperbarui_pada`,`s`.`jenis_perintah_kerja_id` AS `jenis_perintah_kerja_id`,`s`.`tujuan_perintah_kerja_id` AS `tujuan_perintah_kerja_id`,`s`.`status_eksekusi_workflow_id` AS `status_eksekusi_workflow_id`,`s`.`workflow_notes` AS `workflow_notes`,`s`.`workflow_created_at` AS `workflow_created_at`,`s`.`workflow_updated_at` AS `workflow_updated_at`,`jpk`.`kode` AS `jenis_perintah_kode`,`jpk`.`nama` AS `jenis_perintah_nama`,`tpk`.`kode` AS `tujuan_perintah_kode`,`tpk`.`nama` AS `tujuan_perintah_nama`,`sew`.`kode` AS `status_eksekusi_kode`,`sew`.`nama` AS `status_eksekusi_nama`,`sew`.`warna` AS `status_eksekusi_warna` from (((`spk` `s` left join `jenis_perintah_kerja` `jpk` on((`s`.`jenis_perintah_kerja_id` = `jpk`.`id`))) left join `tujuan_perintah_kerja` `tpk` on((`s`.`tujuan_perintah_kerja_id` = `tpk`.`id`))) left join `status_eksekusi_workflow` `sew` on((`s`.`status_eksekusi_workflow_id` = `sew`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_area_employee_summary`
--

/*!50001 DROP VIEW IF EXISTS `vw_area_employee_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_area_employee_summary` AS select `a`.`id` AS `area_id`,`a`.`area_name` AS `area_name`,`a`.`area_code` AS `area_code`,`d`.`nama_departemen` AS `nama_departemen`,count(distinct `e`.`id`) AS `total_employees`,count(distinct (case when (`e`.`staff_role` = 'ADMIN') then `e`.`id` end)) AS `admin_count`,count(distinct (case when (`e`.`staff_role` = 'FOREMAN') then `e`.`id` end)) AS `foreman_count`,count(distinct (case when (`e`.`staff_role` = 'MECHANIC') then `e`.`id` end)) AS `mechanic_count`,count(distinct (case when (`e`.`staff_role` = 'HELPER') then `e`.`id` end)) AS `helper_count` from (((`areas` `a` join `departemen` `d` on((`a`.`departemen_id` = `d`.`id_departemen`))) left join `area_employee_assignments` `aea` on(((`a`.`id` = `aea`.`area_id`) and (`aea`.`is_active` = 1)))) left join `employees` `e` on(((`aea`.`employee_id` = `e`.`id`) and (`e`.`is_active` = 1)))) where (`a`.`is_active` = 1) group by `a`.`id`,`a`.`area_name`,`a`.`area_code`,`d`.`nama_departemen` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_attachment_installed`
--

/*!50001 DROP VIEW IF EXISTS `vw_attachment_installed`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_attachment_installed` AS select `ia`.`id_inventory_attachment` AS `id_inventory_attachment`,`ia`.`tipe_item` AS `tipe_item`,(case when (`ia`.`tipe_item` = 'attachment') then concat(`a`.`tipe`,' - ',`a`.`merk`,' ',`a`.`model`) when (`ia`.`tipe_item` = 'battery') then concat(`b`.`jenis_baterai`,' - ',`b`.`merk_baterai`,' ',`b`.`tipe_baterai`) when (`ia`.`tipe_item` = 'charger') then concat(`c`.`tipe_charger`,' - ',`c`.`merk_charger`) end) AS `item_name`,(case when (`ia`.`tipe_item` = 'attachment') then `ia`.`sn_attachment` when (`ia`.`tipe_item` = 'battery') then `ia`.`sn_baterai` when (`ia`.`tipe_item` = 'charger') then `ia`.`sn_charger` else NULL end) AS `serial_number`,`iu`.`no_unit` AS `no_unit`,`su`.`status_unit` AS `status_unit` from (((((`inventory_attachment` `ia` left join `inventory_unit` `iu` on((`ia`.`id_inventory_unit` = `iu`.`id_inventory_unit`))) left join `attachment` `a` on((`ia`.`attachment_id` = `a`.`id_attachment`))) left join `baterai` `b` on((`ia`.`baterai_id` = `b`.`id`))) left join `charger` `c` on((`ia`.`charger_id` = `c`.`id_charger`))) left join `status_unit` `su` on((`ia`.`status_unit` = `su`.`id_status`))) order by `ia`.`id_inventory_attachment` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_attachment_status`
--

/*!50001 DROP VIEW IF EXISTS `vw_attachment_status`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_attachment_status` AS select `ia`.`id_inventory_attachment` AS `id_inventory_attachment`,`ia`.`tipe_item` AS `tipe_item`,`ia`.`po_id` AS `po_id`,`ia`.`id_inventory_unit` AS `id_inventory_unit`,`ia`.`attachment_id` AS `attachment_id`,`ia`.`sn_attachment` AS `sn_attachment`,`ia`.`baterai_id` AS `baterai_id`,`ia`.`sn_baterai` AS `sn_baterai`,`ia`.`charger_id` AS `charger_id`,`ia`.`sn_charger` AS `sn_charger`,`ia`.`kondisi_fisik` AS `kondisi_fisik`,`ia`.`kelengkapan` AS `kelengkapan`,`ia`.`catatan_fisik` AS `catatan_fisik`,`ia`.`lokasi_penyimpanan` AS `lokasi_penyimpanan`,`ia`.`status_unit` AS `status_unit`,`ia`.`tanggal_masuk` AS `tanggal_masuk`,`ia`.`catatan_inventory` AS `catatan_inventory`,`ia`.`created_at` AS `created_at`,`ia`.`updated_at` AS `updated_at`,`ia`.`status_attachment_id` AS `status_attachment_id`,`sa`.`nama_status` AS `status_attachment_name`,`sa`.`deskripsi` AS `status_attachment_desc`,(case when (`ia`.`id_inventory_unit` is not null) then 'USED' else 'AVAILABLE' end) AS `simple_status` from (`inventory_attachment` `ia` left join `status_attachment` `sa` on((`ia`.`status_attachment_id` = `sa`.`id_status_attachment`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_employee_performance`
--

/*!50001 DROP VIEW IF EXISTS `vw_employee_performance`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_employee_performance` AS select `e`.`id` AS `employee_id`,`e`.`staff_name` AS `employee_name`,`e`.`staff_role` AS `employee_role`,`d`.`nama_departemen` AS `department`,count(`wo`.`id`) AS `total_work_orders`,count((case when (`wos`.`status_name` = 'COMPLETED') then `wo`.`id` end)) AS `completed_orders`,avg((case when (`wo`.`time_to_repair` is not null) then `wo`.`time_to_repair` end)) AS `avg_repair_time` from (((`employees` `e` join `departemen` `d` on((`e`.`departemen_id` = `d`.`id_departemen`))) left join `work_orders` `wo` on(((`e`.`id` = `wo`.`admin_id`) or (`e`.`id` = `wo`.`foreman_id`) or (`e`.`id` = `wo`.`mechanic_id`) or (`e`.`id` = `wo`.`helper_id`)))) left join `work_order_statuses` `wos` on((`wo`.`status_id` = `wos`.`id`))) where (`e`.`is_active` = 1) group by `e`.`id`,`e`.`staff_name`,`e`.`staff_role`,`d`.`nama_departemen` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_employees_by_departemen_area`
--

/*!50001 DROP VIEW IF EXISTS `vw_employees_by_departemen_area`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_employees_by_departemen_area` AS select `d`.`nama_departemen` AS `nama_departemen`,`a`.`area_name` AS `area_name`,`a`.`area_code` AS `area_code`,`e`.`staff_name` AS `employee_name`,`e`.`staff_role` AS `employee_role`,`aea`.`assignment_type` AS `assignment_type`,`aea`.`start_date` AS `start_date`,`aea`.`end_date` AS `end_date` from (((`departemen` `d` join `employees` `e` on((`d`.`id_departemen` = `e`.`departemen_id`))) join `area_employee_assignments` `aea` on((`e`.`id` = `aea`.`employee_id`))) join `areas` `a` on((`aea`.`area_id` = `a`.`id`))) where ((`e`.`is_active` = 1) and (`aea`.`is_active` = 1) and (`a`.`is_active` = 1)) order by `d`.`nama_departemen`,`a`.`area_name`,`e`.`staff_role`,`e`.`staff_name` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_overdue_work_orders`
--

/*!50001 DROP VIEW IF EXISTS `vw_overdue_work_orders`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_overdue_work_orders` AS select `wo`.`id` AS `id`,`wo`.`work_order_number` AS `work_order_number`,`wo`.`report_date` AS `report_date`,`wo`.`unit_id` AS `unit_id`,`wo`.`order_type` AS `order_type`,`wo`.`priority_id` AS `priority_id`,`wo`.`requested_repair_time` AS `requested_repair_time`,`wo`.`category_id` AS `category_id`,`wo`.`subcategory_id` AS `subcategory_id`,`wo`.`complaint_description` AS `complaint_description`,`wo`.`status_id` AS `status_id`,`wo`.`admin_id` AS `admin_id`,`wo`.`foreman_id` AS `foreman_id`,`wo`.`mechanic_id` AS `mechanic_id`,`wo`.`helper_id` AS `helper_id`,`wo`.`repair_description` AS `repair_description`,`wo`.`notes` AS `notes`,`wo`.`sparepart_used` AS `sparepart_used`,`wo`.`time_to_repair` AS `time_to_repair`,`wo`.`completion_date` AS `completion_date`,`wo`.`area` AS `area`,`wo`.`created_by` AS `created_by`,`wo`.`created_at` AS `created_at`,`wo`.`updated_at` AS `updated_at`,`wop`.`sla_hours` AS `sla_hours`,timestampdiff(HOUR,`wo`.`report_date`,now()) AS `hours_elapsed`,(timestampdiff(HOUR,`wo`.`report_date`,now()) - `wop`.`sla_hours`) AS `hours_overdue` from ((`work_orders` `wo` left join `work_order_priorities` `wop` on((`wo`.`priority_id` = `wop`.`id`))) left join `work_order_statuses` `wos` on((`wo`.`status_id` = `wos`.`id`))) where ((`wos`.`is_final_status` = 0) and (timestampdiff(HOUR,`wo`.`report_date`,now()) > `wop`.`sla_hours`)) order by (timestampdiff(HOUR,`wo`.`report_date`,now()) - `wop`.`sla_hours`) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_unit_complete_info`
--

/*!50001 DROP VIEW IF EXISTS `vw_unit_complete_info`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_unit_complete_info` AS select `iu`.`id_inventory_unit` AS `id_inventory_unit`,`iu`.`no_unit` AS `no_unit`,`iu`.`serial_number` AS `serial_number`,`iu`.`lokasi_unit` AS `lokasi_unit`,`c`.`customer_code` AS `customer_code`,`c`.`customer_name` AS `customer_name`,`cl`.`location_name` AS `customer_location`,`cl`.`address` AS `customer_address`,`a`.`area_code` AS `area_code`,`a`.`area_name` AS `area_name`,`k`.`no_kontrak` AS `no_kontrak`,`k`.`jenis_sewa` AS `kontrak_jenis_sewa`,`k`.`status` AS `kontrak_status`,`k`.`tanggal_mulai` AS `kontrak_mulai`,`k`.`tanggal_berakhir` AS `kontrak_berakhir`,`k`.`nilai_total` AS `kontrak_nilai`,group_concat((case when (`e`.`staff_role` = 'ADMIN') then `e`.`staff_name` end) separator ',') AS `admin_staff`,group_concat((case when (`e`.`staff_role` = 'FOREMAN') then `e`.`staff_name` end) separator ',') AS `foreman_staff`,group_concat((case when (`e`.`staff_role` = 'MECHANIC') then `e`.`staff_name` end) separator ',') AS `mechanic_staff`,group_concat((case when (`e`.`staff_role` = 'HELPER') then `e`.`staff_name` end) separator ',') AS `helper_staff`,group_concat((case when (`e`.`staff_role` = 'ADMIN') then `e`.`id` end) separator ',') AS `admin_staff_ids`,group_concat((case when (`e`.`staff_role` = 'FOREMAN') then `e`.`id` end) separator ',') AS `foreman_staff_ids`,group_concat((case when (`e`.`staff_role` = 'MECHANIC') then `e`.`id` end) separator ',') AS `mechanic_staff_ids`,group_concat((case when (`e`.`staff_role` = 'HELPER') then `e`.`id` end) separator ',') AS `helper_staff_ids` from (((((((`inventory_unit` `iu` left join `customers` `c` on((`iu`.`customer_id` = `c`.`id`))) left join `customer_locations` `cl` on((`iu`.`customer_location_id` = `cl`.`id`))) left join `areas` `a` on((`cl`.`area_id` = `a`.`id`))) left join `customer_contracts` `cc` on(((`c`.`id` = `cc`.`customer_id`) and (`cc`.`is_active` = 1)))) left join `kontrak` `k` on((`cc`.`kontrak_id` = `k`.`id`))) left join `area_employee_assignments` `aea` on(((`a`.`id` = `aea`.`area_id`) and (`aea`.`is_active` = 1)))) left join `employees` `e` on(((`aea`.`employee_id` = `e`.`id`) and (`e`.`is_active` = 1)))) group by `iu`.`id_inventory_unit` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_work_order_by_category`
--

/*!50001 DROP VIEW IF EXISTS `vw_work_order_by_category`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_work_order_by_category` AS select `woc`.`category_name` AS `category_name`,count(0) AS `total_work_orders`,count((case when (`wos`.`is_final_status` = 0) then 1 end)) AS `open_work_orders`,count((case when (`wos`.`is_final_status` = 1) then 1 end)) AS `closed_work_orders`,avg(`wo`.`time_to_repair`) AS `avg_repair_time`,min(`wo`.`time_to_repair`) AS `min_repair_time`,max(`wo`.`time_to_repair`) AS `max_repair_time` from ((`work_orders` `wo` left join `work_order_categories` `woc` on((`wo`.`category_id` = `woc`.`id`))) left join `work_order_statuses` `wos` on((`wo`.`status_id` = `wos`.`id`))) group by `woc`.`id`,`woc`.`category_name` order by count(0) desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_work_order_sparepart_summary`
--

/*!50001 DROP VIEW IF EXISTS `vw_work_order_sparepart_summary`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_work_order_sparepart_summary` AS select `wo`.`id` AS `work_order_id`,`wo`.`work_order_number` AS `work_order_number`,`wos`.`id` AS `sparepart_id`,`wos`.`sparepart_code` AS `sparepart_code`,`wos`.`sparepart_name` AS `sparepart_name`,`wos`.`quantity_brought` AS `quantity_brought`,`wos`.`satuan` AS `satuan`,coalesce(`wou`.`quantity_used`,0) AS `quantity_used`,coalesce(`wou`.`quantity_returned`,0) AS `quantity_returned`,(`wos`.`quantity_brought` - coalesce(`wou`.`quantity_used`,0)) AS `quantity_available`,`wou`.`usage_notes` AS `usage_notes`,`wou`.`return_notes` AS `return_notes`,`wou`.`used_at` AS `used_at`,`wou`.`returned_at` AS `returned_at`,(case when (`wou`.`quantity_used` is null) then 'BROUGHT' when ((`wou`.`quantity_used` > 0) and (`wou`.`returned_at` is null)) then 'USED' when (`wou`.`returned_at` is not null) then 'COMPLETED' else 'PENDING' end) AS `status` from ((`work_orders` `wo` join `work_order_spareparts` `wos` on((`wo`.`id` = `wos`.`work_order_id`))) left join `work_order_sparepart_usage` `wou` on((`wos`.`id` = `wou`.`work_order_sparepart_id`))) order by `wo`.`id`,`wos`.`sparepart_code` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_work_order_stats`
--

/*!50001 DROP VIEW IF EXISTS `vw_work_order_stats`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_work_order_stats` AS select count(0) AS `total_work_orders`,count((case when (`wos`.`status_code` = 'OPEN') then 1 end)) AS `open_work_orders`,count((case when (`wos`.`status_code` = 'ASSIGNED') then 1 end)) AS `assigned_work_orders`,count((case when (`wos`.`status_code` = 'IN_PROGRESS') then 1 end)) AS `in_progress_work_orders`,count((case when (`wos`.`status_code` = 'WAITING_PARTS') then 1 end)) AS `waiting_parts_work_orders`,count((case when (`wos`.`status_code` = 'TESTING') then 1 end)) AS `testing_work_orders`,count((case when (`wos`.`status_code` = 'COMPLETED') then 1 end)) AS `completed_work_orders`,count((case when (`wos`.`status_code` = 'CLOSED') then 1 end)) AS `closed_work_orders`,count((case when (`wos`.`status_code` = 'CANCELLED') then 1 end)) AS `cancelled_work_orders`,count((case when (`wos`.`status_code` = 'ON_HOLD') then 1 end)) AS `on_hold_work_orders`,count((case when (`wop`.`priority_code` = 'LOW') then 1 end)) AS `low_priority_count`,count((case when (`wop`.`priority_code` = 'MEDIUM') then 1 end)) AS `medium_priority_count`,count((case when (`wop`.`priority_code` = 'HIGH') then 1 end)) AS `high_priority_count`,count((case when (`wop`.`priority_code` = 'CRITICAL') then 1 end)) AS `critical_priority_count`,count((case when (`wo`.`order_type` = 'COMPLAINT') then 1 end)) AS `complaint_orders`,count((case when (`wo`.`order_type` = 'PMPS') then 1 end)) AS `pmps_orders`,count((case when (`wo`.`order_type` = 'FABRIKASI') then 1 end)) AS `fabrikasi_orders`,count((case when (`wo`.`order_type` = 'PERSIAPAN') then 1 end)) AS `persiapan_orders` from ((`work_orders` `wo` left join `work_order_statuses` `wos` on((`wo`.`status_id` = `wos`.`id`))) left join `work_order_priorities` `wop` on((`wo`.`priority_id` = `wop`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_work_orders_detail`
--

/*!50001 DROP VIEW IF EXISTS `vw_work_orders_detail`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_work_orders_detail` AS select `wo`.`id` AS `id`,`wo`.`work_order_number` AS `work_order_number`,`wo`.`report_date` AS `report_date`,date_format(`wo`.`report_date`,'%d/%m/%Y %H:%i:%s') AS `formatted_report_date`,`iu`.`no_unit` AS `no_unit`,`c`.`customer_name` AS `nama_perusahaan`,`mu`.`merk_unit` AS `merk_unit`,`tu`.`tipe` AS `tipe_unit`,`kap`.`kapasitas_unit` AS `kapasitas`,`kap`.`kapasitas_unit` AS `kapasitas_unit`,`wo`.`order_type` AS `tipe_order`,`wop`.`priority_name` AS `priority`,`wop`.`priority_color` AS `priority_color`,`wo`.`requested_repair_time` AS `requested_repair_time`,date_format(`wo`.`requested_repair_time`,'%d-%m-%Y %H:%i') AS `formatted_request_time`,`woc`.`category_name` AS `kategori`,`wosc`.`subcategory_name` AS `sub_kategori`,`wo`.`complaint_description` AS `keluhan_unit`,`wos`.`status_name` AS `status`,`wos`.`status_color` AS `status_color`,`e_admin`.`staff_name` AS `admin`,`e_foreman`.`staff_name` AS `foreman`,`e_mechanic`.`staff_name` AS `mekanik`,`e_helper`.`staff_name` AS `helper`,`wo`.`repair_description` AS `perbaikan`,`wo`.`notes` AS `keterangan`,`wo`.`sparepart_used` AS `sparepart`,`wo`.`time_to_repair` AS `ttr`,`wo`.`completion_date` AS `tanggal`,date_format(`wo`.`completion_date`,'%M') AS `bulan`,`wo`.`area` AS `area`,`wo`.`created_at` AS `created_at`,`wo`.`updated_at` AS `updated_at` from (((((((((((((((`work_orders` `wo` left join `inventory_unit` `iu` on((`wo`.`unit_id` = `iu`.`id_inventory_unit`))) left join `kontrak` `k` on((`iu`.`kontrak_id` = `k`.`id`))) left join `customer_locations` `cl` on((`k`.`customer_location_id` = `cl`.`id`))) left join `customers` `c` on((`cl`.`customer_id` = `c`.`id`))) left join `model_unit` `mu` on((`iu`.`model_unit_id` = `mu`.`id_model_unit`))) left join `tipe_unit` `tu` on((`iu`.`tipe_unit_id` = `tu`.`id_tipe_unit`))) left join `kapasitas` `kap` on((`iu`.`kapasitas_unit_id` = `kap`.`id_kapasitas`))) left join `work_order_priorities` `wop` on((`wo`.`priority_id` = `wop`.`id`))) left join `work_order_categories` `woc` on((`wo`.`category_id` = `woc`.`id`))) left join `work_order_subcategories` `wosc` on((`wo`.`subcategory_id` = `wosc`.`id`))) left join `work_order_statuses` `wos` on((`wo`.`status_id` = `wos`.`id`))) left join `employees` `e_admin` on((`wo`.`admin_id` = `e_admin`.`id`))) left join `employees` `e_foreman` on((`wo`.`foreman_id` = `e_foreman`.`id`))) left join `employees` `e_mechanic` on((`wo`.`mechanic_id` = `e_mechanic`.`id`))) left join `employees` `e_helper` on((`wo`.`helper_id` = `e_helper`.`id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vw_workflow_kontrak_spk_di`
--

/*!50001 DROP VIEW IF EXISTS `vw_workflow_kontrak_spk_di`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vw_workflow_kontrak_spk_di` AS select `iu`.`id_inventory_unit` AS `id_inventory_unit`,`iu`.`no_unit` AS `no_unit`,`iu`.`serial_number` AS `serial_number`,`k`.`id` AS `kontrak_id`,`k`.`no_kontrak` AS `no_kontrak`,`c`.`customer_name` AS `pelanggan`,`cl`.`location_name` AS `kontrak_lokasi`,`k`.`status` AS `kontrak_status`,`ks`.`id` AS `kontrak_spesifikasi_id`,`ks`.`spek_kode` AS `spek_kode`,`ks`.`aksesoris` AS `spek_aksesoris`,`s`.`id` AS `spk_id`,`s`.`nomor_spk` AS `nomor_spk`,`s`.`status` AS `spk_status`,`s`.`delivery_plan` AS `delivery_plan`,`di`.`id` AS `delivery_instruction_id`,`di`.`nomor_di` AS `nomor_di`,`di`.`tanggal_kirim` AS `tanggal_kirim`,`di`.`status_di` AS `di_status`,`iu`.`aksesoris` AS `unit_aksesoris`,`iu`.`lokasi_unit` AS `lokasi_unit`,`iu`.`status_unit_id` AS `status_unit_id`,`su`.`status_unit` AS `nama_status`,`iu`.`created_at` AS `unit_created`,`iu`.`updated_at` AS `unit_updated` from (((((((`inventory_unit` `iu` left join `kontrak` `k` on((`iu`.`kontrak_id` = `k`.`id`))) left join `customer_locations` `cl` on((`k`.`customer_location_id` = `cl`.`id`))) left join `customers` `c` on((`cl`.`customer_id` = `c`.`id`))) left join `kontrak_spesifikasi` `ks` on((`iu`.`kontrak_spesifikasi_id` = `ks`.`id`))) left join `spk` `s` on((`iu`.`spk_id` = `s`.`id`))) left join `delivery_instructions` `di` on((`iu`.`delivery_instruction_id` = `di`.`id`))) left join `status_unit` `su` on((`iu`.`status_unit_id` = `su`.`id_status`))) order by `iu`.`id_inventory_unit` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-28 12:23:04
