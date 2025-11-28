-- MariaDB dump 10.19  Distrib 10.4.32-MariaDB, for Linux (x86_64)
--
-- Host: localhost    Database: optima_ci
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-09-26  9:00:00
