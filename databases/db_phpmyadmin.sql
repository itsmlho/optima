-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Sep 01, 2025 at 04:18 AM
-- Server version: 8.0.43
-- PHP Version: 8.2.27

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

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `LinkInventoryToUnitAfterDelivery` (IN `p_di_id` INT, IN `p_spk_id` INT, IN `p_unit_id` INT)   BEGIN
        DECLARE v_spk_spesifikasi JSON;
        DECLARE v_persiapan_battery_id INT DEFAULT NULL;
        DECLARE v_persiapan_charger_id INT DEFAULT NULL;
        DECLARE v_fabrikasi_attachment_id INT DEFAULT NULL;
        
        DECLARE EXIT HANDLER FOR SQLEXCEPTION
        BEGIN
            ROLLBACK;
            RESIGNAL;
        END;
        
        START TRANSACTION;
        
        -- Ambil data spesifikasi dari SPK
        SELECT spesifikasi INTO v_spk_spesifikasi 
        FROM spk 
        WHERE id = p_spk_id;
        
        -- Extract inventory IDs dari JSON spesifikasi
        SET v_persiapan_battery_id = JSON_UNQUOTE(JSON_EXTRACT(v_spk_spesifikasi, '$.persiapan_battery_id'));
        SET v_persiapan_charger_id = JSON_UNQUOTE(JSON_EXTRACT(v_spk_spesifikasi, '$.persiapan_charger_id'));
        SET v_fabrikasi_attachment_id = JSON_UNQUOTE(JSON_EXTRACT(v_spk_spesifikasi, '$.fabrikasi_attachment_id'));
        
        -- Link battery ke unit jika ada
        IF v_persiapan_battery_id IS NOT NULL AND v_persiapan_battery_id != 'null' THEN
            UPDATE inventory_attachment 
            SET 
                id_inventory_unit = p_unit_id,
                status_unit = 3, -- RENTAL
                lokasi_penyimpanan = NULL,
                updated_at = NOW()
            WHERE id_inventory_attachment = v_persiapan_battery_id
            AND tipe_item = 'battery'
            AND status_unit = 7; -- STOCK ASET
            
            -- Log activity
            INSERT INTO inventory_item_unit_log (
                id_inventory_attachment,
                id_inventory_unit,
                action,
                user_id,
                note,
                created_at
            ) VALUES (
                v_persiapan_battery_id,
                p_unit_id,
                'assign_after_delivery',
                1, -- System user
                CONCAT('Auto-assigned after DI #', p_di_id, ' delivered'),
                NOW()
            );
        END IF;
        
        -- Link charger ke unit jika ada
        IF v_persiapan_charger_id IS NOT NULL AND v_persiapan_charger_id != 'null' THEN
            UPDATE inventory_attachment 
            SET 
                id_inventory_unit = p_unit_id,
                status_unit = 3, -- RENTAL
                lokasi_penyimpanan = NULL,
                updated_at = NOW()
            WHERE id_inventory_attachment = v_persiapan_charger_id
            AND tipe_item = 'charger'
            AND status_unit = 7; -- STOCK ASET
            
            -- Log activity
            INSERT INTO inventory_item_unit_log (
                id_inventory_attachment,
                id_inventory_unit,
                action,
                user_id,
                note,
                created_at
            ) VALUES (
                v_persiapan_charger_id,
                p_unit_id,
                'assign_after_delivery',
                1, -- System user
                CONCAT('Auto-assigned after DI #', p_di_id, ' delivered'),
                NOW()
            );
        END IF;
        
        -- Link attachment ke unit jika ada
        IF v_fabrikasi_attachment_id IS NOT NULL AND v_fabrikasi_attachment_id != 'null' THEN
            UPDATE inventory_attachment 
            SET 
                id_inventory_unit = p_unit_id,
                status_unit = 3, -- RENTAL
                lokasi_penyimpanan = NULL,
                updated_at = NOW()
            WHERE id_inventory_attachment = v_fabrikasi_attachment_id
            AND tipe_item = 'attachment'
            AND status_unit = 7; -- STOCK ASET
            
            -- Log activity
            INSERT INTO inventory_item_unit_log (
                id_inventory_attachment,
                id_inventory_unit,
                action,
                user_id,
                note,
                created_at
            ) VALUES (
                v_fabrikasi_attachment_id,
                p_unit_id,
                'assign_after_delivery',
                1, -- System user
                CONCAT('Auto-assigned after DI #', p_di_id, ' delivered'),
                NOW()
            );
        END IF;
        
        COMMIT;
        
    END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `update_kontrak_totals_proc` (IN `kontrak_id_param` INT UNSIGNED)   BEGIN
    DECLARE total_units_count INT DEFAULT 0;
    DECLARE nilai_total_amount DECIMAL(15,2) DEFAULT 0;
    DECLARE jenis_sewa_kontrak VARCHAR(10) DEFAULT 'BULANAN';

    -- Get kontrak jenis_sewa
    SELECT jenis_sewa INTO jenis_sewa_kontrak
    FROM kontrak
    WHERE id = kontrak_id_param;

    -- Set default jika NULL
    IF jenis_sewa_kontrak IS NULL THEN
        SET jenis_sewa_kontrak = 'BULANAN';
    END IF;

    -- Calculate totals dari kontrak_spesifikasi
    IF jenis_sewa_kontrak = 'HARIAN' THEN
        SELECT
            COALESCE(SUM(ks.jumlah_dibutuhkan), 0) as total_units,
            COALESCE(SUM(ks.jumlah_dibutuhkan * COALESCE(ks.harga_per_unit_harian, 0)), 0) as nilai_total
            INTO total_units_count, nilai_total_amount
        FROM kontrak_spesifikasi ks
        WHERE ks.kontrak_id = kontrak_id_param;
    ELSE
        -- Default BULANAN
        SELECT
            COALESCE(SUM(ks.jumlah_dibutuhkan), 0) as total_units,
            COALESCE(SUM(ks.jumlah_dibutuhkan * COALESCE(ks.harga_per_unit_bulanan, 0)), 0) as nilai_total
            INTO total_units_count, nilai_total_amount
        FROM kontrak_spesifikasi ks
        WHERE ks.kontrak_id = kontrak_id_param;
    END IF;

    -- Update kontrak
    UPDATE kontrak SET
        total_units = total_units_count,
        nilai_total = nilai_total_amount
    WHERE id = kontrak_id_param;

END$$

--
-- Functions
--
CREATE DEFINER=`root`@`%` FUNCTION `get_unit_attachment_info` (`unit_id` INT) RETURNS JSON DETERMINISTIC BEGIN
    DECLARE result JSON DEFAULT NULL;

    SELECT JSON_OBJECT(
        'attachment_id', ia.attachment_id,
        'sn_attachment', ia.sn_attachment,
        'tipe', a.tipe,
        'merk', a.merk,
        'model', a.model,
        'inventory_id', ia.id_inventory_attachment
    ) INTO result
    FROM inventory_attachment ia
    JOIN attachment a ON ia.attachment_id = a.id_attachment
    WHERE ia.id_inventory_unit = unit_id
      AND ia.tipe_item = 'attachment'
      AND ia.status_unit = 8
    LIMIT 1;

    RETURN result;
END$$

CREATE DEFINER=`root`@`%` FUNCTION `get_unit_battery_info` (`unit_id` INT) RETURNS JSON DETERMINISTIC BEGIN
    DECLARE result JSON DEFAULT NULL;

    SELECT JSON_OBJECT(
        'battery_id', ia.baterai_id,
        'sn_baterai', ia.sn_baterai,
        'merk', b.merk_baterai,
        'tipe', b.tipe_baterai,
        'jenis', b.jenis_baterai,
        'inventory_id', ia.id_inventory_attachment
    ) INTO result
    FROM inventory_attachment ia
    JOIN baterai b ON ia.baterai_id = b.id
    WHERE ia.id_inventory_unit = unit_id
      AND ia.tipe_item = 'battery'
      AND ia.status_unit = 8
    LIMIT 1;

    RETURN result;
END$$

CREATE DEFINER=`root`@`%` FUNCTION `get_unit_charger_info` (`unit_id` INT) RETURNS JSON DETERMINISTIC BEGIN
    DECLARE result JSON DEFAULT NULL;

    SELECT JSON_OBJECT(
        'charger_id', ia.charger_id,
        'sn_charger', ia.sn_charger,
        'merk', c.merk_charger,
        'tipe', c.tipe_charger,
        'inventory_id', ia.id_inventory_attachment
    ) INTO result
    FROM inventory_attachment ia
    JOIN charger c ON ia.charger_id = c.id_charger
    WHERE ia.id_inventory_unit = unit_id
      AND ia.tipe_item = 'charger'
      AND ia.status_unit = 8
    LIMIT 1;

    RETURN result;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `attachment`
--

CREATE TABLE `attachment` (
  `id_attachment` int NOT NULL,
  `tipe` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `merk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attachment`
--

INSERT INTO `attachment` (`id_attachment`, `tipe`, `merk`, `model`) VALUES
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

CREATE TABLE `baterai` (
  `id` int NOT NULL,
  `merk_baterai` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_baterai` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_baterai` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `baterai`
--

INSERT INTO `baterai` (`id`, `merk_baterai`, `tipe_baterai`, `jenis_baterai`) VALUES
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

CREATE TABLE `charger` (
  `id_charger` int NOT NULL,
  `merk_charger` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tipe_charger` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `charger`
--

INSERT INTO `charger` (`id_charger`, `merk_charger`, `tipe_charger`) VALUES
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

CREATE TABLE `delivery_instructions` (
  `id` int UNSIGNED NOT NULL,
  `nomor_di` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `spk_id` int UNSIGNED DEFAULT NULL,
  `po_kontrak_nomor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tanggal_kirim` date DEFAULT NULL,
  `catatan` text COLLATE utf8mb4_general_ci,
  `status` enum('SUBMITTED','PROCESSED','SHIPPED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'SUBMITTED',
  `dibuat_oleh` int UNSIGNED DEFAULT NULL,
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
  `catatan_sampai` text COLLATE utf8mb4_general_ci COMMENT 'Catatan kedatangan dan konfirmasi penerima'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_instructions`
--

INSERT INTO `delivery_instructions` (`id`, `nomor_di`, `spk_id`, `po_kontrak_nomor`, `pelanggan`, `lokasi`, `tanggal_kirim`, `catatan`, `status`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`, `perencanaan_tanggal_approve`, `estimasi_sampai`, `nama_supir`, `no_hp_supir`, `no_sim_supir`, `kendaraan`, `no_polisi_kendaraan`, `berangkat_tanggal_approve`, `catatan_berangkat`, `sampai_tanggal_approve`, `catatan_sampai`) VALUES
(8, 'DI/202508/001', NULL, NULL, '', NULL, '2025-08-19', NULL, 'DELIVERED', 1, '2025-08-18 04:13:18', '2025-08-27 15:26:25', '2025-08-18', '2025-08-21', 'JOKO', '082138848123', '1231012', 'colt diesel', '123', '2025-08-18', NULL, '2025-08-18', 'asd'),
(9, 'DI/202508/002', NULL, 'test/1/1/2', 'ADIT', '123', '2025-08-20', NULL, 'DELIVERED', 1, '2025-08-18 06:44:27', '2025-08-27 15:26:25', '2025-08-18', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-26', NULL, '2025-08-26', 'aa'),
(10, 'DI/202508/003', NULL, 'test/1/1/2', 'ADIT', '123', '2025-08-20', 'a', 'DELIVERED', 1, '2025-08-18 07:42:28', '2025-08-27 15:26:25', '2025-08-19', '2025-08-23', 'JOKO', '082138848123', '1231012', 'colt diesel', '123', '2025-08-19', 'a', '2025-08-19', 'asd'),
(11, 'DI/202508/004', NULL, '1233', 'kaleng', 'kaaleng', '2025-08-20', NULL, 'DELIVERED', 1, '2025-08-19 02:54:26', '2025-08-27 15:26:25', '2025-08-19', '2025-08-20', 'JOKO', '082138848123', '1231012', 'colt diesel', '123', '2025-08-19', NULL, '2025-08-19', 'as'),
(12, 'DI/202508/005', NULL, 'test/1/1/2', 'ADIT', '123', '2025-08-26', 'aa', 'PROCESSED', 1, '2025-08-19 07:35:59', '2025-08-27 15:26:25', '2025-08-26', '2025-08-26', 'JOKO', '082138848123', '9123123', 'KOKASD', '123123', NULL, NULL, NULL, NULL),
(13, 'DI/202508/006', NULL, 'PO-CL-0350', 'PT. Maju Bersama Sejahtera', 'Sunter, Jakarta Utara', '2025-08-20', NULL, 'DELIVERED', 1, '2025-08-19 07:46:13', '2025-08-27 15:26:25', '2025-08-26', NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-26', 'as', '2025-08-26', 'asd'),
(95, 'DI/202508/007', NULL, 'test/1/1/10', 'PURI INDAH', 'Gemalapik', '2025-08-27', NULL, 'SUBMITTED', 1, '2025-08-27 15:24:37', '2025-08-27 22:24:37', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 'DI/202508/008', NULL, 'test/1/1/11', 'Sarana Mitra Luas Tbk', 'Area Kargo Bandara Soekarno-Hatta', '2025-08-27', NULL, 'SUBMITTED', 1, '2025-08-27 15:26:16', '2025-08-27 22:26:16', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 'DI/202508/011', 24, 'test/1/1/9', 'PURI NUSA', 'Gemalapik', '2025-08-30', NULL, 'SUBMITTED', 1, '2025-08-30 02:26:06', '2025-08-30 02:31:44', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 'DI/202508/012', 24, 'test/1/1/9', 'PURI NUSA', 'Gemalapik', '2025-08-30', NULL, 'SUBMITTED', 1, '2025-08-30 02:32:49', '2025-08-30 02:32:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

--
-- Triggers `delivery_instructions`
--
DELIMITER $$
CREATE TRIGGER `tr_delivery_instructions_status_update` AFTER UPDATE ON `delivery_instructions` FOR EACH ROW BEGIN
        DECLARE v_unit_id INT DEFAULT NULL;
        
        -- Jika status berubah menjadi SAMPAI
        IF OLD.status != 'SAMPAI' AND NEW.status = 'SAMPAI' AND NEW.spk_id IS NOT NULL THEN
            
            -- Cari unit_id dari delivery_items yang terkait dengan DI ini
            SELECT unit_id INTO v_unit_id
            FROM delivery_items 
            WHERE di_id = NEW.id 
            AND item_type = 'UNIT' 
            AND unit_id IS NOT NULL
            LIMIT 1;
            
            -- Jika ada unit, panggil procedure untuk link inventory
            IF v_unit_id IS NOT NULL THEN
                CALL LinkInventoryToUnitAfterDelivery(NEW.id, NEW.spk_id, v_unit_id);
            END IF;
            
        END IF;
    END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `delivery_items`
--

CREATE TABLE `delivery_items` (
  `id` int UNSIGNED NOT NULL,
  `di_id` int UNSIGNED NOT NULL,
  `item_type` enum('UNIT','ATTACHMENT') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'UNIT',
  `unit_id` int UNSIGNED DEFAULT NULL,
  `attachment_id` int UNSIGNED DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `delivery_items`
--

INSERT INTO `delivery_items` (`id`, `di_id`, `item_type`, `unit_id`, `attachment_id`, `keterangan`) VALUES
(12, 10, 'UNIT', 7, NULL, NULL),
(13, 10, 'ATTACHMENT', NULL, 3, NULL),
(14, 13, 'UNIT', 7, NULL, NULL),
(15, 13, 'ATTACHMENT', NULL, 5, NULL),
(141, 95, 'UNIT', 15, NULL, NULL),
(142, 95, 'ATTACHMENT', NULL, 3, 'Attachment from SPK'),
(143, 95, 'ATTACHMENT', NULL, 4, 'Battery from SPK'),
(144, 95, 'ATTACHMENT', NULL, 5, 'Charger from SPK'),
(145, 96, 'UNIT', 13, NULL, NULL),
(146, 96, 'ATTACHMENT', NULL, 3, 'Attachment from SPK'),
(147, 96, 'ATTACHMENT', NULL, 5, 'Charger from SPK'),
(148, 98, 'UNIT', 16, NULL, NULL),
(149, 98, 'UNIT', 17, NULL, NULL),
(150, 98, 'ATTACHMENT', NULL, 3, 'Attachment from SPK'),
(151, 98, 'ATTACHMENT', NULL, 4, 'Attachment from SPK'),
(152, 98, 'ATTACHMENT', NULL, 5, 'Charger from SPK'),
(153, 98, 'ATTACHMENT', NULL, 3, 'Attachment from SPK'),
(154, 98, 'ATTACHMENT', NULL, 5, 'Charger from SPK');

-- --------------------------------------------------------

--
-- Table structure for table `departemen`
--

CREATE TABLE `departemen` (
  `id_departemen` int NOT NULL,
  `nama_departemen` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departemen`
--

INSERT INTO `departemen` (`id_departemen`, `nama_departemen`) VALUES
(1, 'DIESEL'),
(2, 'ELECTRIC'),
(3, 'GASOLINE');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `divisions`
--

INSERT INTO `divisions` (`id`, `name`, `code`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
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

CREATE TABLE `forklifts` (
  `forklift_id` int UNSIGNED NOT NULL,
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
  `assigned_to` int UNSIGNED DEFAULT NULL COMMENT 'Assigned to user ID',
  `rental_rate_daily` decimal(10,2) DEFAULT NULL COMMENT 'Daily rental rate',
  `rental_rate_weekly` decimal(10,2) DEFAULT NULL COMMENT 'Weekly rental rate',
  `rental_rate_monthly` decimal(10,2) DEFAULT NULL COMMENT 'Monthly rental rate',
  `availability` enum('available','unavailable','reserved') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `notes` text COLLATE utf8mb4_general_ci,
  `specifications` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'Additional specifications in JSON format',
  `attachments` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin COMMENT 'File attachments in JSON format',
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `deleted_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ;

--
-- Dumping data for table `forklifts`
--

INSERT INTO `forklifts` (`forklift_id`, `unit_code`, `unit_name`, `brand`, `model`, `type`, `capacity`, `fuel_type`, `engine_power`, `lift_height`, `year_manufactured`, `serial_number`, `purchase_date`, `purchase_price`, `current_value`, `supplier`, `warranty_expiry`, `insurance_expiry`, `last_service_date`, `next_service_date`, `service_interval_hours`, `total_operating_hours`, `status`, `condition`, `location`, `assigned_to`, `rental_rate_daily`, `rental_rate_weekly`, `rental_rate_monthly`, `availability`, `notes`, `specifications`, `attachments`, `created_by`, `updated_by`, `deleted_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'FL001', 'Toyota 8FG25 Forklift', 'Toyota', '8FG25', 'gas', 2.50, 'gas', 68.00, 4.70, '2022', 'TYT8FG25001', '2022-01-15', 450000000.00, 380000000.00, 'Toyota Material Handling', '2025-01-15', '2024-12-31', NULL, NULL, 250, 1250, 'available', 'excellent', 'Warehouse A', NULL, 850000.00, 5500000.00, 20000000.00, 'available', 'Unit kondisi prima, rutin maintenance', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(2, 'FL002', 'Komatsu FB20-12 Electric Forklift', 'Komatsu', 'FB20-12', 'electric', 2.00, 'electric', 24.00, 3.00, '2023', 'KMT FB20001', '2023-03-10', 380000000.00, 340000000.00, 'Komatsu Forklift Indonesia', '2026-03-10', '2024-12-31', NULL, NULL, 300, 890, 'rented', 'excellent', 'Customer Site - PT ABC', NULL, 750000.00, 4800000.00, 18000000.00, 'unavailable', 'Sedang disewa PT ABC Industries', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(3, 'FL003', 'Hyster H3.5FT Diesel Forklift', 'Hyster', 'H3.5FT', 'diesel', 3.50, 'diesel', 74.00, 4.50, '2021', 'HYS H35001', '2021-08-20', 520000000.00, 420000000.00, 'Hyster Indonesia', '2024-08-20', '2024-12-31', NULL, NULL, 250, 2150, 'maintenance', 'good', 'Service Center', NULL, 950000.00, 6200000.00, 23000000.00, 'unavailable', 'Maintenance rutin 2000 jam operasi', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(4, 'FL004', 'Mitsubishi FG15N Gas Forklift', 'Mitsubishi', 'FG15N', 'gas', 1.50, 'gas', 42.00, 3.00, '2023', 'MIT FG15001', '2023-06-15', 320000000.00, 300000000.00, 'Mitsubishi Forklift', '2026-06-15', '2024-12-31', NULL, NULL, 250, 456, 'available', 'excellent', 'Warehouse B', NULL, 650000.00, 4200000.00, 15000000.00, 'available', 'Unit baru, kondisi prima', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL),
(5, 'FL005', 'Crown FC 5200 Electric Forklift', 'Crown', 'FC 5200', 'electric', 2.00, 'electric', 36.00, 4.00, '2022', 'CRW FC5200001', '2022-09-10', 420000000.00, 360000000.00, 'Crown Equipment Indonesia', '2025-09-10', '2024-12-31', NULL, NULL, 300, 1680, 'reserved', 'good', 'Warehouse A', NULL, 780000.00, 5000000.00, 18500000.00, 'reserved', 'Reserved untuk kontrak PT XYZ minggu depan', NULL, NULL, 1, NULL, NULL, '2025-07-08 06:35:48', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `inventory_attachment`
--

CREATE TABLE `inventory_attachment` (
  `id_inventory_attachment` int NOT NULL,
  `tipe_item` enum('attachment','battery','charger') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'attachment',
  `po_id` int NOT NULL COMMENT 'Foreign key ke purchase_orders.id_po',
  `id_inventory_unit` int UNSIGNED DEFAULT NULL,
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
  `tanggal_masuk` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Tanggal masuk ke inventory',
  `catatan_inventory` text COLLATE utf8mb4_unicode_ci,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Single source of truth untuk semua komponen: battery, charger, attachment';

--
-- Dumping data for table `inventory_attachment`
--

INSERT INTO `inventory_attachment` (`id_inventory_attachment`, `tipe_item`, `po_id`, `id_inventory_unit`, `attachment_id`, `sn_attachment`, `baterai_id`, `sn_baterai`, `charger_id`, `sn_charger`, `kondisi_fisik`, `kelengkapan`, `catatan_fisik`, `lokasi_penyimpanan`, `status_unit`, `tanggal_masuk`, `catatan_inventory`, `created_at`, `updated_at`) VALUES
(2, 'attachment', 118, NULL, 1, '123', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-22 04:36:39', 'Dari verifikasi PO: Sesuai dan siap digunakan', '2025-08-22 04:36:39', '2025-08-22 04:36:39'),
(3, 'attachment', 124, NULL, 4, '123', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-22 09:18:28', 'Dari verifikasi PO: Sesuai', '2025-08-22 09:18:28', '2025-08-22 09:18:28'),
(4, 'attachment', 130, NULL, 3, '123', NULL, NULL, NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-22 09:19:00', 'Dari verifikasi PO: Sesuai', '2025-08-22 09:19:00', '2025-08-22 09:19:00'),
(5, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-22 09:23:14', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-22 09:23:14', '2025-08-27 11:41:47'),
(6, 'charger', 143, 16, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 8, '2025-08-22 09:23:14', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-22 09:23:14', '2025-08-27 23:53:23'),
(7, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:34', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:34', '2025-08-27 11:41:47'),
(8, 'charger', 143, 17, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 8, '2025-08-27 04:15:34', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:34', '2025-08-30 02:00:18'),
(9, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:43', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:43', '2025-08-27 11:41:47'),
(10, 'charger', 143, NULL, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:43', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:43', '2025-08-27 11:41:47'),
(11, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:51', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:51', '2025-08-27 11:41:47'),
(12, 'charger', 143, NULL, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:51', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:51', '2025-08-27 11:41:47'),
(13, 'battery', 143, NULL, NULL, NULL, 4, '123', NULL, NULL, 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:58', 'Dari verifikasi PO (Battery): Sesuai', '2025-08-27 04:15:58', '2025-08-27 11:41:47'),
(14, 'charger', 143, NULL, NULL, NULL, NULL, NULL, 5, '123', 'Baik', 'Lengkap', NULL, 'POS 1', 7, '2025-08-27 04:15:58', 'Dari verifikasi PO (Charger): Sesuai', '2025-08-27 04:15:58', '2025-08-27 11:41:47'),
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

--
-- Triggers `inventory_attachment`
--
DELIMITER $$
CREATE TRIGGER `trg_inventory_attachment_consistency_insert` BEFORE INSERT ON `inventory_attachment` FOR EACH ROW BEGIN
    -- Validasi: pastikan setiap record memiliki item sesuai tipe_item
    IF (NEW.tipe_item = 'attachment' AND (NEW.attachment_id IS NULL OR NEW.sn_attachment IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Attachment tipe_item requires attachment_id and sn_attachment';
    END IF;
    
    IF (NEW.tipe_item = 'battery' AND (NEW.baterai_id IS NULL OR NEW.sn_baterai IS NULL)) THEN

        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Battery tipe_item requires baterai_id and sn_baterai';
    END IF;
    
    IF (NEW.tipe_item = 'charger' AND (NEW.charger_id IS NULL OR NEW.sn_charger IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Charger tipe_item requires charger_id and sn_charger';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_inventory_attachment_consistency_update` BEFORE UPDATE ON `inventory_attachment` FOR EACH ROW BEGIN
    -- Validasi: pastikan setiap record memiliki item sesuai tipe_item
    IF (NEW.tipe_item = 'attachment' AND (NEW.attachment_id IS NULL OR NEW.sn_attachment IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Attachment tipe_item requires attachment_id and sn_attachment';
    END IF;
    
    IF (NEW.tipe_item = 'battery' AND (NEW.baterai_id IS NULL OR NEW.sn_baterai IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Battery tipe_item requires baterai_id and sn_baterai';
    END IF;
    
    IF (NEW.tipe_item = 'charger' AND (NEW.charger_id IS NULL OR NEW.sn_charger IS NULL)) THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Charger tipe_item requires charger_id and sn_charger';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_item_unit_log`
--

CREATE TABLE `inventory_item_unit_log` (
  `id` int NOT NULL,
  `id_inventory_attachment` int NOT NULL,
  `id_inventory_unit` int NOT NULL,
  `action` enum('assign','remove') COLLATE utf8mb4_general_ci NOT NULL,
  `user_id` int DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_spareparts`
--

CREATE TABLE `inventory_spareparts` (
  `id` int NOT NULL,
  `sparepart_id` int NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `lokasi_rak` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `inventory_spareparts`
--

INSERT INTO `inventory_spareparts` (`id`, `sparepart_id`, `stok`, `lokasi_rak`, `updated_at`) VALUES
(1, 17, 1, 'POS 1', '2025-07-25 09:01:22'),
(2, 1, 1, 'POS 1', '2025-08-12 03:36:22');

-- --------------------------------------------------------

--
-- Table structure for table `inventory_unit`
--

CREATE TABLE `inventory_unit` (
  `id_inventory_unit` int UNSIGNED NOT NULL,
  `no_unit` int UNSIGNED DEFAULT NULL,
  `serial_number` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Serial Number utama dari pabrikan',
  `id_po` int DEFAULT NULL COMMENT 'Foreign Key ke tabel purchase_orders',
  `tahun_unit` year DEFAULT NULL,
  `status_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `status_aset` tinyint(1) DEFAULT NULL COMMENT 'Flag status aset (misal: 1=Aktif, 0=Non-Aktif)',
  `lokasi_unit` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departemen_id` int DEFAULT NULL COMMENT 'FK ke tabel departemen',
  `tanggal_kirim` datetime DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `harga_sewa_bulanan` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per bulan',
  `harga_sewa_harian` decimal(15,2) DEFAULT NULL COMMENT 'Harga sewa per hari',
  `kontrak_id` int UNSIGNED DEFAULT NULL COMMENT 'Foreign key ke tabel kontrak',
  `kontrak_spesifikasi_id` int UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi untuk tracking spek mana',
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
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='Data unit utama - komponen disimpan di inventory_attachment';

--
-- Dumping data for table `inventory_unit`
--

INSERT INTO `inventory_unit` (`id_inventory_unit`, `no_unit`, `serial_number`, `id_po`, `tahun_unit`, `status_unit_id`, `status_aset`, `lokasi_unit`, `departemen_id`, `tanggal_kirim`, `keterangan`, `harga_sewa_bulanan`, `harga_sewa_harian`, `kontrak_id`, `kontrak_spesifikasi_id`, `tipe_unit_id`, `model_unit_id`, `kapasitas_unit_id`, `model_mast_id`, `tinggi_mast`, `sn_mast`, `model_mesin_id`, `sn_mesin`, `roda_id`, `ban_id`, `valve_id`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 122, '2025', 8, 0, 'Warehouse', 2, NULL, '', NULL, NULL, 1, NULL, 9, 6, 2, 5, NULL, '', 4, '', 4, 3, 4, '2025-08-12 02:22:14', '2025-08-30 03:42:03'),
(2, 2, NULL, 123, '2025', 8, 0, 'Warehouse', 2, NULL, '', NULL, NULL, NULL, NULL, 10, 3, 10, 2, NULL, '', 2, '', 2, 2, 3, '2025-08-12 03:15:49', '2025-08-30 03:42:03'),
(4, 4, 'test', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, 8, NULL, 12, 6, 6, 2, NULL, 'test', 2, 'test', 2, 1, 3, '2025-08-12 04:47:28', '2025-08-30 03:42:03'),
(5, 7, 'test2', 38, '2025', 8, NULL, 'POS 1', 3, NULL, NULL, NULL, NULL, 8, NULL, 12, 6, 6, 2, NULL, 'test2', 2, 'test2', 2, 1, 3, '2025-08-12 04:49:52', '2025-08-30 03:42:03'),
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
DELIMITER $$
CREATE TRIGGER `update_kontrak_totals_after_unit_delete` AFTER DELETE ON `inventory_unit` FOR EACH ROW BEGIN
    IF OLD.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_kontrak_totals_after_unit_insert` AFTER INSERT ON `inventory_unit` FOR EACH ROW BEGIN
    IF NEW.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(NEW.kontrak_id);
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_kontrak_totals_after_unit_update` AFTER UPDATE ON `inventory_unit` FOR EACH ROW BEGIN
    -- Update old kontrak if changed
    IF OLD.kontrak_id IS NOT NULL AND (OLD.kontrak_id != NEW.kontrak_id OR NEW.kontrak_id IS NULL) THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
    
    -- Update new kontrak
    IF NEW.kontrak_id IS NOT NULL THEN
        CALL update_kontrak_totals_proc(NEW.kontrak_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory_unit_backup`
--

CREATE TABLE `inventory_unit_backup` (
  `id_inventory_unit` int UNSIGNED NOT NULL,
  `model_baterai_id` int DEFAULT NULL COMMENT 'FK ke tabel baterai (sudah termasuk jenis, merk)',
  `sn_baterai` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_charger_id` int DEFAULT NULL COMMENT 'FK ke tabel charger (sudah termasuk merk)',
  `sn_charger` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `model_attachment_id` int DEFAULT NULL COMMENT 'FK ke tabel attachment (sudah termasuk tipe, merk, model)',
  `sn_attachment` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_unit_id` int DEFAULT NULL COMMENT 'FK ke tabel status_unit (misal: STOK, RENTAL, JUAL)',
  `backup_timestamp` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `inventory_unit_backup`
--

INSERT INTO `inventory_unit_backup` (`id_inventory_unit`, `model_baterai_id`, `sn_baterai`, `model_charger_id`, `sn_charger`, `model_attachment_id`, `sn_attachment`, `status_unit_id`, `backup_timestamp`) VALUES
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
CREATE TABLE `inventory_unit_components` (
`attachment_merk` varchar(100)
,`attachment_model` varchar(100)
,`attachment_tipe` varchar(100)
,`id_inventory_unit` int unsigned
,`jenis_baterai` varchar(50)
,`merk_baterai` varchar(100)
,`merk_charger` varchar(100)
,`model_attachment_id` int
,`model_baterai_id` int
,`model_charger_id` int
,`no_unit` int unsigned
,`serial_number` varchar(255)
,`sn_attachment` varchar(255)
,`sn_baterai` varchar(100)
,`sn_charger` varchar(255)
,`tipe_baterai` varchar(100)
,`tipe_charger` varchar(100)
);

-- --------------------------------------------------------

--
-- Table structure for table `jenis_roda`
--

CREATE TABLE `jenis_roda` (
  `id_roda` int NOT NULL,
  `tipe_roda` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jenis_roda`
--

INSERT INTO `jenis_roda` (`id_roda`, `tipe_roda`) VALUES
(1, '3-Wheel'),
(2, '4-Wheel'),
(3, '3-Way '),
(4, '4-Way Multi-Directional (FFL)');

-- --------------------------------------------------------

--
-- Table structure for table `kapasitas`
--

CREATE TABLE `kapasitas` (
  `id_kapasitas` int NOT NULL,
  `kapasitas_unit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kapasitas`
--

INSERT INTO `kapasitas` (`id_kapasitas`, `kapasitas_unit`) VALUES
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

CREATE TABLE `kontrak` (
  `id` int UNSIGNED NOT NULL,
  `no_kontrak` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `no_po_marketing` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pelanggan` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `lokasi` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `pic` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Nama Person In Charge',
  `kontak` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Kontak PIC (telepon/email)',
  `nilai_total` decimal(15,2) DEFAULT NULL COMMENT 'Nilai total kontrak dalam rupiah',
  `total_units` int UNSIGNED NOT NULL DEFAULT '0' COMMENT 'Total unit yang terkait dengan kontrak ini',
  `jenis_sewa` enum('BULANAN','HARIAN') COLLATE utf8mb4_general_ci DEFAULT 'BULANAN' COMMENT 'Jenis periode sewa',
  `tanggal_mulai` date NOT NULL,
  `tanggal_berakhir` date NOT NULL,
  `status` enum('Aktif','Berakhir','Pending','Dibatalkan') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Pending',
  `dibuat_oleh` int UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kontrak`
--

INSERT INTO `kontrak` (`id`, `no_kontrak`, `no_po_marketing`, `pelanggan`, `lokasi`, `pic`, `kontak`, `nilai_total`, `total_units`, `jenis_sewa`, `tanggal_mulai`, `tanggal_berakhir`, `status`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`) VALUES
(1, '001/SML/RENT/I/2025', 'PO-CL-0123', 'PT. Logistik Cepat Indonesia', 'Gudang Cikarang', NULL, NULL, 0.00, 1, 'BULANAN', '2025-01-15', '2026-01-14', 'Aktif', 1, '2025-08-04 17:08:02', '2025-08-20 13:34:08'),
(7, 'test/1/1/2', 'test/1/1/2', 'ADIT', '123', NULL, NULL, NULL, 0, 'BULANAN', '2025-08-15', '2025-08-17', 'Aktif', 1, '2025-08-15 09:42:08', '2025-08-26 07:37:56'),
(8, 'test/1/1/3', '1233', 'kaleng', 'kaaleng', NULL, NULL, 0.00, 0, 'BULANAN', '2025-08-19', '2025-08-19', 'Aktif', 1, '2025-08-19 02:47:02', '2025-08-30 03:42:03'),
(9, 'test/1/1/4', '1234', 'kalengg', 'kaalengg', NULL, NULL, NULL, 0, 'BULANAN', '2025-08-19', '2025-08-19', 'Pending', 1, '2025-08-19 02:47:14', '2025-08-19 09:47:14'),
(10, 'test/1/1/5', '12345', 'Sarana Mitra Luas', 'gemalapik', 'Adit', '082134555233', 3703701.00, 3, 'BULANAN', '2025-08-31', '2025-12-31', 'Pending', 1, '2025-08-19 09:29:28', '2025-08-20 14:26:51'),
(11, 'test/1/1/6', '123456', 'Sarana Mitra Luas', 'Gemalapik', 'Adit', '082134555233', 150000000.00, 10, 'BULANAN', '2025-08-31', '2025-12-31', 'Pending', 1, '2025-08-20 03:42:59', '2025-08-20 14:21:42'),
(12, 'test/1/1/7', '1234567', 'Sarana Mitra Luas', 'Gemalapik', 'Adit', '082134555233', 490480480.00, 4, 'BULANAN', '2025-08-31', '2025-12-31', 'Pending', 1, '2025-08-20 06:19:37', '2025-08-20 16:22:46'),
(13, 'test/1/1/8', '12345678', 'IBR', 'Gemalapik', 'Adit', '082134555233', 0.00, 0, 'BULANAN', '2025-08-31', '2025-12-31', 'Pending', 1, '2025-08-21 06:32:35', '2025-08-21 06:32:35'),
(14, 'test/1/1/9', '12345679', 'PURI NUSA', 'Gemalapik', 'Adit', '082134555233', 1241231230.00, 20, 'BULANAN', '2025-08-31', '2025-12-31', 'Pending', 1, '2025-08-21 06:48:11', '2025-08-26 15:27:24'),
(15, 'test/1/1/10', '1234567910', 'PURI INDAH', 'Gemalapik', 'Adit', '082134555233', 36000000.00, 3, 'BULANAN', '2025-08-31', '2025-12-31', 'Aktif', 1, '2025-08-21 06:50:17', '2025-08-26 07:28:17'),
(16, 'test/1/1/11', '112345', 'Sarana Mitra Luas Tbk', 'Area Kargo Bandara Soekarno-Hatta', 'kaleng', '22131231231', 9009999.00, 1, 'BULANAN', '2025-08-27', '2025-12-27', 'Pending', 1, '2025-08-27 09:00:04', '2025-08-27 16:00:33'),
(17, 'test/1/1/12', '12123456', 'LG Cibitung', 'SAMPING TOL CIBITUNG', 'AA', '12312313', 3690000.00, 3, 'BULANAN', '2025-08-31', '2025-09-30', 'Pending', 1, '2025-08-28 01:53:15', '2025-08-28 08:54:12'),
(18, 'sabtutest', 'sabtutest', 'MONORKOBO', 'BEKASI', 'JAJA', '09324987729', 0.00, 0, 'BULANAN', '2025-08-31', '2025-09-30', 'Pending', 1, '2025-08-30 03:48:42', '2025-08-30 03:48:42'),
(19, 'testsabtu', 'testsabtu', 'MONORKOBO', 'BEKASI', 'JAJA', '09324987729', 140000000.00, 2, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 03:53:57', '2025-09-01 02:34:23'),
(20, 'SPKRJASND', '2131123', 'MONORKOBO', 'BEKASI', 'JAJA', '09324987729', 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:28:11', '2025-08-30 04:28:11'),
(21, 'SPKRJASND1', '2131123', 'MONORKOBO', 'BEKASI', 'JAJA', '09324987729', 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:29:14', '2025-08-30 04:29:14'),
(22, 'ASDWAD', '2131123', 'MONORKOBO', 'BEKASI', 'JAJA', '09324987729', 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:52:51', '2025-08-30 04:52:51'),
(23, 'TEST001', NULL, 'Test Client', NULL, NULL, NULL, 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:53:11', '2025-08-30 04:53:11'),
(24, 'TEST002', NULL, 'Test Client 2', NULL, NULL, NULL, 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:53:45', '2025-08-30 04:53:45'),
(25, 'TEST003', NULL, 'Test Client 3', NULL, NULL, NULL, 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:54:03', '2025-08-30 04:54:03'),
(26, 'TEST004', NULL, 'Test Client 4', NULL, NULL, NULL, 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:54:21', '2025-08-30 04:54:21'),
(39, 'TEST005', NULL, 'Test Client 5', NULL, NULL, NULL, 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:55:27', '2025-08-30 04:55:27'),
(40, 'TEST006', NULL, 'Final Test Client', NULL, NULL, NULL, 0.00, 0, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:56:06', '2025-08-30 04:56:06'),
(41, 'TETTETESS', '2131123', 'MONORKOBO', 'BEKASI', 'JAJA', '09324987729', 32000000.00, 5, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-08-30 04:57:38', '2025-09-01 02:35:16'),
(42, 'KAMSEUPAI', 'ADAAAAA', 'MICRON', 'LIPPO CIKARANG', 'JAKA', '082134555233', 0.00, 2, 'BULANAN', '2025-09-01', '2025-10-01', 'Pending', 1, '2025-09-01 01:43:19', '2025-09-01 01:44:41'),
(43, 'test12345', '2131123', 'MONORKOBO', 'BEKASI', 'JAJA', '09324987729', 12000000.00, 2, 'BULANAN', '2025-08-30', '2025-09-30', 'Pending', 1, '2025-09-01 01:52:59', '2025-09-01 02:40:20'),
(44, 'MSI', 'MSI', 'MSI', 'EROPA', 'MSI', '09213123123', 18000000.00, 2, 'BULANAN', '2025-09-01', '2025-09-01', 'Pending', 1, '2025-09-01 01:54:45', '2025-09-01 01:55:43');

-- --------------------------------------------------------

--
-- Table structure for table `kontrak_spesifikasi`
--

CREATE TABLE `kontrak_spesifikasi` (
  `id` int UNSIGNED NOT NULL,
  `kontrak_id` int UNSIGNED NOT NULL,
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
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `kontrak_spesifikasi`
--

INSERT INTO `kontrak_spesifikasi` (`id`, `kontrak_id`, `spek_kode`, `jumlah_dibutuhkan`, `jumlah_tersedia`, `harga_per_unit_bulanan`, `harga_per_unit_harian`, `catatan_spek`, `departemen_id`, `tipe_unit_id`, `tipe_jenis`, `kapasitas_id`, `merk_unit`, `model_unit`, `attachment_tipe`, `attachment_merk`, `jenis_baterai`, `charger_id`, `mast_id`, `ban_id`, `roda_id`, `valve_id`, `aksesoris`, `dibuat_pada`, `diperbarui_pada`) VALUES
(4, 1, 'SPEC-001', 1, 0, NULL, NULL, 'Test', 1, NULL, 'Forklift', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-08-20 06:34:08', '2025-08-20 06:34:08'),
(5, 12, 'SPEC-001', 1, 0, 123123123.00, NULL, '', 1, 6, 'COUNTER BALANCE', 10, 'BT', NULL, 'FORK POSITIONER', NULL, NULL, NULL, 1, 6, 1, 2, NULL, '2025-08-20 07:17:47', '2025-08-20 07:17:47'),
(6, 12, 'SPEC-002', 1, 0, 123123123.00, NULL, '', 1, 6, 'COUNTER BALANCE', 10, 'BT', NULL, 'FORK POSITIONER', NULL, NULL, NULL, 1, 6, 1, 2, NULL, '2025-08-20 07:17:47', '2025-08-20 07:17:47'),
(7, 11, 'SPEC-001', 10, 0, 15000000.00, NULL, '', 1, 6, 'COUNTER BALANCE', 41, 'HELI', NULL, 'FORK POSITIONER', NULL, NULL, NULL, 18, 1, 1, 3, '[\"LAMPU UTAMA\", \"LAMPU MUNDUR\", \"LAMPU SIGN\", \"LAMPU STOP\", \"BLUE SPOT\", \"BACK BUZZER\", \"CAMERA\", \"SPEED LIMITER\", \"VOICE ANNOUNCER\", \"HORN SPEAKER\", \"HORN KLASON\", \"BIO METRIC\", \"ACRYLIC\", \"APAR 1 KG\", \"APAR 3 KG\", \"P3K\", \"TELEMATIC\"]', '2025-08-20 07:21:42', '2025-08-21 10:05:54'),
(8, 10, 'SPEC-001', 3, 0, 1234567.00, NULL, '', 2, 6, 'HAND PALLET', 41, 'JUNGHEINRICH', NULL, 'FORK', NULL, 'Lead Acid', 16, 17, 6, 1, 1, NULL, '2025-08-20 07:26:51', '2025-08-20 07:26:51'),
(9, 12, 'SPEC-003', 1, 0, 234234234.00, NULL, '', 2, 6, 'PALLET MOVER', 19, 'MHE DEMAG', NULL, 'FORK', NULL, 'Lead Acid', 15, 17, 2, 3, 3, NULL, '2025-08-20 07:38:35', '2025-08-20 07:38:35'),
(10, 12, 'SPEC-004', 1, 0, 10000000.00, NULL, '', 2, 6, 'PALLET STACKER', 12, 'JUNGHEINRICH', NULL, 'FORK POSITIONER', NULL, 'Lithium-ion', 9, 16, 3, 3, 1, '[\"LAMPU UTAMA\", \"LAMPU MUNDUR\", \"LAMPU SIGN\", \"LAMPU STOP\", \"BLUE SPOT\", \"BACK BUZZER\", \"CAMERA\", \"SPEED LIMITER\", \"VOICE ANNOUNCER\", \"HORN SPEAKER\", \"HORN KLASON\", \"BIO METRIC\", \"ACRYLIC\", \"APAR 1 KG\", \"APAR 3 KG\", \"P3K\", \"TELEMATIC\"]', '2025-08-20 09:22:46', '2025-08-21 13:11:55'),
(11, 15, 'SPEC-001', 3, 0, 12000000.00, NULL, '', 2, 4, 'SCRUBER', 39, 'KOMATSU', NULL, 'FORK POSITIONER', NULL, 'Lead Acid', 9, 15, 6, 1, 2, '[\"LAMPU UTAMA\", \"ROTARY LAMP\", \"BACK BUZZER\", \"CAMERA\", \"SENSOR PARKING\", \"SPEED LIMITER\", \"LASER FORK\", \"VOICE ANNOUNCER\", \"HORN SPEAKER\", \"HORN KLASON\", \"BIO METRIC\", \"APAR 1 KG\", \"APAR 3 KG\", \"BEACON\"]', '2025-08-21 06:51:13', '2025-08-27 07:05:19'),
(12, 14, 'SPEC-001', 10, 0, 1000000.00, NULL, '', 1, 4, 'SCRUBER', 42, 'LINDE', NULL, 'FORK POSITIONER', NULL, NULL, NULL, 15, 3, 1, 2, '[\"LAMPU UTAMA\", \"ROTARY LAMP\", \"CAMERA AI\", \"CAMERA\", \"LASER FORK\", \"VOICE ANNOUNCER\", \"HORN SPEAKER\", \"ACRYLIC\", \"APAR 1 KG\", \"P3K\", \"BEACON\", \"SPARS ARRESTOR\"]', '2025-08-26 07:48:30', '2025-08-26 08:21:32'),
(13, 14, 'SPEC-002', 10, 0, 123123123.00, NULL, '', 2, 4, 'SCRUBER', 11, 'HYUNDAI', NULL, 'FORK POSITIONER', NULL, 'Lead Acid', 8, 15, 6, 1, 2, '[\"LAMPU UTAMA\", \"BLUE SPOT\", \"RED LINE\", \"HORN KLASON\", \"ACRYLIC\", \"APAR 3 KG\", \"P3K\", \"SAFETY BELT INTERLOC\", \"TELEMATIC\", \"SPARS ARRESTOR\"]', '2025-08-26 07:50:37', '2025-08-26 08:21:25'),
(15, 16, 'SPEC-001', 1, 0, 9009999.00, NULL, '', 2, 4, 'SCRUBER', 43, 'KOMATSU', NULL, 'FORK POSITIONER', NULL, 'Lead Acid', 8, 15, 1, 3, 3, '[\"LAMPU UTAMA\", \"BLUE SPOT\", \"ROTARY LAMP\", \"BACK BUZZER\", \"SENSOR PARKING\", \"SPEED LIMITER\", \"HORN SPEAKER\", \"APAR 1 KG\", \"BEACON\"]', '2025-08-27 09:00:33', '2025-08-27 09:00:33'),
(16, 17, 'SPEC-001', 3, 0, 1230000.00, NULL, '', 2, 6, 'PALLET MOVER', 16, 'HELI', NULL, '', NULL, 'Lead Acid', 4, 12, 3, 1, 2, '[\"LAMPU UTAMA\", \"BLUE SPOT\", \"RED LINE\", \"ROTARY LAMP\", \"BACK BUZZER\", \"CAMERA AI\", \"SENSOR PARKING\", \"SPEED LIMITER\", \"LASER FORK\"]', '2025-08-28 01:54:12', '2025-08-28 01:54:12'),
(0, 42, 'SPEC-001', 1, 0, NULL, NULL, '', 2, 6, 'REACH TRUCK', 12, 'HELI', NULL, 'FORK POSITIONER', NULL, 'Lithium-ion', 1, 15, 6, 4, 1, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]', '2025-09-01 01:44:05', '2025-09-01 01:44:05'),
(0, 42, 'SPEC-002', 1, 0, NULL, NULL, '', 2, 6, 'REACH TRUCK', 12, 'HELI', NULL, 'FORK POSITIONER', NULL, 'Lithium-ion', 1, 15, 6, 4, 1, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]', '2025-09-01 01:44:41', '2025-09-01 01:44:41'),
(0, 44, 'SPEC-001', 2, 0, 9000000.00, NULL, '', 2, 6, 'HAND PALLET', 41, 'HELI', NULL, 'FORK POSITIONER', NULL, 'Lithium-ion', 5, 22, 6, 1, 2, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\"]', '2025-09-01 01:55:43', '2025-09-01 01:55:43'),
(0, 41, 'SPEC-001', 2, 0, 8000000.00, NULL, '', 2, 6, 'HAND PALLET', 11, 'HELI', NULL, 'PAPER ROLL CLAMP', NULL, 'Lithium-ion', 8, 14, 6, 3, 3, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]', '2025-09-01 02:05:27', '2025-09-01 02:05:27'),
(0, 41, 'SPEC-002', 2, 0, 8000000.00, NULL, '', 1, 1, 'WHEEL LOADER', 42, 'KOMATSU', NULL, 'FORK POSITIONER', NULL, NULL, NULL, 14, 6, 1, 3, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]', '2025-09-01 02:09:46', '2025-09-01 02:09:46'),
(0, 19, 'SPEC-001', 2, 0, 70000000.00, NULL, '', 2, 4, 'SCRUBER', 41, 'LINDE', NULL, 'FORK POSITIONER', NULL, 'Lead Acid', 9, 13, 4, 1, 2, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\",\"APAR 1 KG\",\"BEACON\"]', '2025-09-01 02:34:23', '2025-09-01 02:34:23'),
(0, 41, 'SPEC-003', 1, 0, NULL, NULL, '', 2, 6, 'PALLET MOVER', 42, 'KOMATSU', NULL, 'FORKLIFT SCALE', NULL, 'Lithium-ion', 5, 15, 4, 1, 3, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\"]', '2025-09-01 02:35:16', '2025-09-01 02:35:16'),
(0, 43, 'SPEC-001', 2, 0, 6000000.00, NULL, '', 2, 6, 'PALLET STACKER', 14, 'HELI', NULL, 'PAPER ROLL CLAMP', NULL, 'Lithium-ion', 9, 22, 6, 3, 3, '[\"LAMPU UTAMA\",\"ROTARY LAMP\",\"SENSOR PARKING\",\"HORN SPEAKER\"]', '2025-09-01 02:40:20', '2025-09-01 02:40:20');

--
-- Triggers `kontrak_spesifikasi`
--
DELIMITER $$
CREATE TRIGGER `update_kontrak_totals_after_spek_insert` AFTER INSERT ON `kontrak_spesifikasi` FOR EACH ROW BEGIN
    CALL update_kontrak_totals_proc(NEW.kontrak_id);
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `update_kontrak_totals_after_spek_update` AFTER UPDATE ON `kontrak_spesifikasi` FOR EACH ROW BEGIN
    CALL update_kontrak_totals_proc(NEW.kontrak_id);
    
    -- If kontrak_id changed, update old kontrak too
    IF OLD.kontrak_id != NEW.kontrak_id THEN
        CALL update_kontrak_totals_proc(OLD.kontrak_id);
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `mesin`
--

CREATE TABLE `mesin` (
  `id` int NOT NULL,
  `merk_mesin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `model_mesin` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `bahan_bakar` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mesin`
--

INSERT INTO `mesin` (`id`, `merk_mesin`, `model_mesin`, `bahan_bakar`) VALUES
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

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(7, '2024-01-01-000001', 'App\\Database\\Migrations\\CreateUsersTable', 'default', 'App', 1751956548, 1),
(8, '2024-01-15-000001', 'App\\Database\\Migrations\\CreateForkliftTable', 'default', 'App', 1751956548, 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration_log`
--

CREATE TABLE `migration_log` (
  `id` int NOT NULL,
  `migration_name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `executed_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `description` text COLLATE utf8mb4_general_ci,
  `status` enum('SUCCESS','FAILED','ROLLBACK') COLLATE utf8mb4_general_ci DEFAULT 'SUCCESS',
  `error_message` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migration_log`
--

INSERT INTO `migration_log` (`id`, `migration_name`, `executed_at`, `description`, `status`, `error_message`) VALUES
(1, 'consolidate_components_to_inventory_attachment', '2025-08-30 03:42:03', 'Konsolidasi battery/charger/attachment ke inventory_attachment sebagai single source of truth', 'SUCCESS', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `model_unit`
--

CREATE TABLE `model_unit` (
  `id_model_unit` int NOT NULL,
  `merk_unit` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `model_unit` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `model_unit`
--

INSERT INTO `model_unit` (`id_model_unit`, `merk_unit`, `model_unit`) VALUES
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

CREATE TABLE `notifications` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `target_role` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `url` varchar(500) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `role` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `division` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `target_role`, `url`, `role`, `division`, `message`, `link`, `is_read`, `created_at`, `read_at`) VALUES
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
(0, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/001 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-01 02:40:53', NULL),
(0, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/002 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-01 02:41:52', NULL),
(0, NULL, NULL, NULL, NULL, NULL, 'SPK SPK/202509/001 diajukan oleh Marketing untuk diproses Service.', NULL, 0, '2025-09-01 04:16:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `notification_logs`
--

CREATE TABLE `notification_logs` (
  `id_notification` int NOT NULL,
  `po_type` enum('unit','attachment','sparepart') COLLATE utf8mb4_general_ci NOT NULL,
  `po_id` int NOT NULL,
  `no_po` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `notification_type` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `message` text COLLATE utf8mb4_general_ci NOT NULL,
  `sent_to_division` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('pending','sent','read') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_by` int DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `key` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `module` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'general',
  `is_system_permission` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `key`, `description`, `module`, `category`, `is_system_permission`, `is_active`, `created_at`, `updated_at`) VALUES
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

CREATE TABLE `po_items` (
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

--
-- Dumping data for table `po_items`
--

INSERT INTO `po_items` (`id_po_item`, `po_id`, `item_type`, `attachment_id`, `baterai_id`, `charger_id`, `serial_number`, `serial_number_charger`, `keterangan`, `status_verifikasi`, `catatan_verifikasi`, `created_at`, `updated_at`) VALUES
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

CREATE TABLE `po_sparepart_items` (
  `id` int NOT NULL,
  `po_id` int NOT NULL,
  `sparepart_id` int NOT NULL,
  `qty` int NOT NULL DEFAULT '1',
  `satuan` enum('Pieces','Rol','Kaleng','Set','Pak','Meter','Unit','Jerigen','Lembar','Box','Pax','Drum','Batang','Pil','Dus','Kilogram','Botol','IBC Tank','Lusin','Liter','Lot') COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `status_verifikasi` enum('Belum Dicek','Sesuai','Tidak Sesuai') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Belum Dicek',
  `catatan_verifikasi` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `po_sparepart_items`
--

INSERT INTO `po_sparepart_items` (`id`, `po_id`, `sparepart_id`, `qty`, `satuan`, `keterangan`, `status_verifikasi`, `catatan_verifikasi`) VALUES
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

CREATE TABLE `po_units` (
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

--
-- Dumping data for table `po_units`
--

INSERT INTO `po_units` (`id_po_unit`, `created_at`, `updated_at`, `po_id`, `jenis_unit`, `status_verifikasi`, `merk_unit`, `model_unit_id`, `tipe_unit_id`, `serial_number_po`, `tahun_po`, `kapasitas_id`, `mast_id`, `sn_mast_po`, `mesin_id`, `sn_mesin_po`, `attachment_id`, `sn_attachment_po`, `baterai_id`, `sn_baterai_po`, `charger_id`, `sn_charger_po`, `ban_id`, `roda_id`, `valve_id`, `status_penjualan`, `keterangan`) VALUES
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

CREATE TABLE `purchase_orders` (
  `id_po` int NOT NULL,
  `no_po` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tanggal_po` date NOT NULL,
  `supplier_id` int NOT NULL,
  `invoice_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `bl_date` date DEFAULT NULL,
  `keterangan_po` text COLLATE utf8mb4_general_ci,
  `tipe_po` enum('Unit','Attachment & Battery','Sparepart','Dinamis') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending','approved','completed','cancelled','Selesai dengan Catatan') COLLATE utf8mb4_general_ci DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchase_orders`
--

INSERT INTO `purchase_orders` (`id_po`, `no_po`, `tanggal_po`, `supplier_id`, `invoice_no`, `invoice_date`, `bl_date`, `keterangan_po`, `tipe_po`, `created_at`, `updated_at`, `status`) VALUES
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

CREATE TABLE `rbac_audit_log` (
  `id` int NOT NULL,
  `user_id` int DEFAULT NULL,
  `action` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `record_id` int DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `performed_by` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ;

-- --------------------------------------------------------

--
-- Table structure for table `rentals`
--

CREATE TABLE `rentals` (
  `rental_id` int UNSIGNED NOT NULL,
  `rental_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `forklift_id` int UNSIGNED NOT NULL,
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
  `created_by` int UNSIGNED DEFAULT NULL,
  `updated_by` int UNSIGNED DEFAULT NULL,
  `approved_by` int UNSIGNED DEFAULT NULL,
  `cancelled_by` int UNSIGNED DEFAULT NULL,
  `completed_by` int UNSIGNED DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `confirmed_by` int UNSIGNED DEFAULT NULL,
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reports`
--

CREATE TABLE `reports` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `format` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `filename` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `status` enum('pending','processing','completed','failed') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `data_count` int NOT NULL DEFAULT '0',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `division_id` int DEFAULT NULL,
  `is_system_role` tinyint(1) DEFAULT '0',
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `division_id`, `is_system_role`, `is_active`, `created_at`, `updated_at`) VALUES
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

CREATE TABLE `role_permissions` (
  `id` int NOT NULL,
  `role_id` int NOT NULL,
  `permission_id` int NOT NULL,
  `granted` tinyint(1) DEFAULT '1',
  `assigned_by` int DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_permissions`
--

INSERT INTO `role_permissions` (`id`, `role_id`, `permission_id`, `granted`, `assigned_by`, `assigned_at`, `created_at`, `updated_at`) VALUES
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

CREATE TABLE `sparepart` (
  `id_sparepart` int NOT NULL,
  `kode` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `desc_sparepart` text COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `spk`
--

CREATE TABLE `spk` (
  `id` int UNSIGNED NOT NULL,
  `nomor_spk` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis_spk` enum('UNIT','ATTACHMENT','TUKAR') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'UNIT',
  `kontrak_id` int UNSIGNED DEFAULT NULL,
  `kontrak_spesifikasi_id` int UNSIGNED DEFAULT NULL COMMENT 'FK ke kontrak_spesifikasi',
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
  `dibuat_oleh` int UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime DEFAULT CURRENT_TIMESTAMP,
  `diperbarui_pada` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Dumping data for table `spk`
--

INSERT INTO `spk` (`id`, `nomor_spk`, `jenis_spk`, `kontrak_id`, `kontrak_spesifikasi_id`, `jumlah_unit`, `po_kontrak_nomor`, `pelanggan`, `pic`, `kontak`, `lokasi`, `delivery_plan`, `spesifikasi`, `status`, `persiapan_unit_mekanik`, `persiapan_unit_estimasi_mulai`, `persiapan_unit_estimasi_selesai`, `persiapan_unit_tanggal_approve`, `persiapan_unit_id`, `persiapan_aksesoris_tersedia`, `fabrikasi_mekanik`, `fabrikasi_estimasi_mulai`, `fabrikasi_estimasi_selesai`, `fabrikasi_tanggal_approve`, `fabrikasi_attachment_id`, `painting_mekanik`, `painting_estimasi_mulai`, `painting_estimasi_selesai`, `painting_tanggal_approve`, `pdi_mekanik`, `pdi_estimasi_mulai`, `pdi_estimasi_selesai`, `pdi_tanggal_approve`, `pdi_catatan`, `catatan`, `dibuat_oleh`, `dibuat_pada`, `diperbarui_pada`) VALUES
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

CREATE TABLE `spk_component_transactions` (
  `id` int UNSIGNED NOT NULL,
  `spk_id` int UNSIGNED NOT NULL,
  `transaction_type` enum('ASSIGN','UNASSIGN','MODIFY') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ASSIGN',
  `component_type` enum('UNIT','ATTACHMENT','BATTERY','CHARGER') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `component_id` int UNSIGNED NOT NULL COMMENT 'ID from respective table (inventory_unit, inventory_attachment)',
  `inventory_id` int UNSIGNED DEFAULT NULL COMMENT 'ID from inventory_attachment if applicable',
  `mekanik` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `catatan` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_by` int UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spk_component_transactions`
--

INSERT INTO `spk_component_transactions` (`id`, `spk_id`, `transaction_type`, `component_type`, `component_id`, `inventory_id`, `mekanik`, `catatan`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'ASSIGN', 'UNIT', 1, NULL, 'John Doe', 'Unit assigned for SPK preparation', 1, '2025-08-30 02:22:02', '2025-08-30 02:22:02'),
(2, 1, 'ASSIGN', 'ATTACHMENT', 1, NULL, 'John Doe', 'Forklift attachment assigned', 1, '2025-08-30 02:22:02', '2025-08-30 02:22:02'),
(3, 1, 'ASSIGN', 'BATTERY', 1, NULL, 'John Doe', 'Battery assigned for unit', 1, '2025-08-30 02:22:02', '2025-08-30 02:22:02');

-- --------------------------------------------------------

--
-- Table structure for table `spk_status_history`
--

CREATE TABLE `spk_status_history` (
  `id` int UNSIGNED NOT NULL,
  `spk_id` int UNSIGNED NOT NULL,
  `status_from` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status_to` enum('DRAFT','SUBMITTED','IN_PROGRESS','READY','COMPLETED','DELIVERED','CANCELLED') COLLATE utf8mb4_general_ci NOT NULL,
  `changed_by` int UNSIGNED DEFAULT NULL,
  `note` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `changed_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `spk_status_history`
--

INSERT INTO `spk_status_history` (`id`, `spk_id`, `status_from`, `status_to`, `changed_by`, `note`, `changed_at`) VALUES
(22, 22, 'READY', 'IN_PROGRESS', 1, 'DI created: DI/202508/007', '2025-08-27 15:24:37'),
(23, 23, 'READY', 'IN_PROGRESS', 1, 'DI created: DI/202508/008', '2025-08-27 15:26:16'),
(0, 24, 'READY', 'IN_PROGRESS', 1, 'DI created: DI/202508/012', '2025-08-30 02:32:49');

-- --------------------------------------------------------

--
-- Table structure for table `spk_units`
--

CREATE TABLE `spk_units` (
  `id` int UNSIGNED NOT NULL,
  `spk_id` int UNSIGNED NOT NULL,
  `unit_id` int UNSIGNED DEFAULT NULL,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status_unit`
--

CREATE TABLE `status_unit` (
  `id_status` int NOT NULL,
  `status_unit` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status_unit`
--

INSERT INTO `status_unit` (`id_status`, `status_unit`) VALUES
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

CREATE TABLE `suppliers` (
  `id_supplier` int NOT NULL,
  `nama_supplier` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `kontak_person` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telepon` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id_supplier`, `nama_supplier`, `kontak_person`, `telepon`, `alamat`, `created_at`, `updated_at`) VALUES
(1, 'PT. Forklift Jaya Abadi', 'Bapak Budi', '081234567890', NULL, '2025-07-15 20:43:59', NULL),
(2, 'CV. Sinar Baterai', 'Ibu Susan', '081122334455', NULL, '2025-07-15 20:43:59', NULL),
(3, 'Toko Sparepart Maju', 'Pak Eko', '021-555-1234', NULL, '2025-07-15 20:43:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tipe_ban`
--

CREATE TABLE `tipe_ban` (
  `id_ban` int NOT NULL,
  `tipe_ban` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipe_ban`
--

INSERT INTO `tipe_ban` (`id_ban`, `tipe_ban`) VALUES
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

CREATE TABLE `tipe_mast` (
  `id_mast` int NOT NULL,
  `tipe_mast` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `tinggi_mast` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Contoh: 4500mm atau 4.5m'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipe_mast`
--

INSERT INTO `tipe_mast` (`id_mast`, `tipe_mast`, `tinggi_mast`) VALUES
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

CREATE TABLE `tipe_unit` (
  `id_tipe_unit` int NOT NULL,
  `tipe` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `jenis` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `id_departemen` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tipe_unit`
--

INSERT INTO `tipe_unit` (`id_tipe_unit`, `tipe`, `jenis`, `id_departemen`) VALUES
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
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
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
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `first_name`, `last_name`, `phone`, `avatar`, `division_id`, `employee_id`, `position`, `is_super_admin`, `is_active`, `last_login`, `email_verified_at`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'admin@optima.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Super', 'Administrator', '', NULL, 1, NULL, NULL, 1, 1, NULL, NULL, NULL, '2025-08-05 00:01:57', '2025-08-17 12:30:13'),
(5, 'admindiesel', 'admindiesel@optima.com', '$2y$10$Hs4MEuJSEbxX8lGDuDNmwephtPcBnfxuCEi/aaPYPprfxWnbQiHu6', 'service', 'diesel', '082136033596', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 19:42:47', '2025-08-06 11:41:04'),
(6, 'adminelektrik', 'adminelektrik@optima.com', '$2y$10$Hs4MEuJSEbxX8lGDuDNmwephtPcBnfxuCEi/aaPYPprfxWnbQiHu6', 'service', 'elektrik', '08211111111', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 20:02:28', '2025-08-06 00:13:03'),
(9, 'operational', 'operational@optima.com', '$2y$10$Hs4MEuJSEbxX8lGDuDNmwephtPcBnfxuCEi/aaPYPprfxWnbQiHu6', 'operational', 'sml', '08211111111', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 20:37:37', '2025-08-05 03:40:00'),
(10, 'adminmarketing', 'adminmarketing@optima.com', '$2y$10$yXhHVLd2XoQXmJkVjByQMerMh8ThRtKuxpLCXfoeDqXdA7k163gEC', 'admin', 'marketing1', '08211111111', NULL, NULL, NULL, NULL, 0, 1, NULL, NULL, NULL, '2025-08-04 20:39:51', '2025-08-05 18:35:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_permissions`
--

CREATE TABLE `user_permissions` (
  `id` int NOT NULL,
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
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_permissions`
--

INSERT INTO `user_permissions` (`id`, `user_id`, `permission_id`, `division_id`, `granted`, `reason`, `assigned_by`, `assigned_at`, `expires_at`, `is_temporary`, `created_at`, `updated_at`) VALUES
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

CREATE TABLE `user_roles` (
  `id` int NOT NULL,
  `user_id` int NOT NULL,
  `role_id` int NOT NULL,
  `division_id` int DEFAULT NULL,
  `assigned_by` int DEFAULT NULL,
  `assigned_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` timestamp NULL DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_roles`
--

INSERT INTO `user_roles` (`id`, `user_id`, `role_id`, `division_id`, `assigned_by`, `assigned_at`, `expires_at`, `is_active`, `created_at`, `updated_at`) VALUES
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

CREATE TABLE `valve` (
  `id_valve` int NOT NULL,
  `jumlah_valve` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `valve`
--

INSERT INTO `valve` (`id_valve`, `jumlah_valve`) VALUES
(1, '2 Valve'),
(2, '3 Valve'),
(3, '4 Valve'),
(4, '5 Valve ');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `delivery_instructions`
--
ALTER TABLE `delivery_instructions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_items`
--
ALTER TABLE `delivery_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `inventory_attachment`
--
ALTER TABLE `inventory_attachment`
  ADD PRIMARY KEY (`id_inventory_attachment`);

--
-- Indexes for table `kontrak`
--
ALTER TABLE `kontrak`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migration_log`
--
ALTER TABLE `migration_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_migration_name` (`migration_name`),
  ADD KEY `idx_executed_at` (`executed_at`);

--
-- Indexes for table `spk`
--
ALTER TABLE `spk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `spk_component_transactions`
--
ALTER TABLE `spk_component_transactions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_spk_component_spk` (`spk_id`),
  ADD KEY `idx_spk_component_type` (`component_type`),
  ADD KEY `idx_spk_component_id` (`component_id`),
  ADD KEY `idx_spk_component_inventory` (`inventory_id`),
  ADD KEY `idx_spk_component_created` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `delivery_instructions`
--
ALTER TABLE `delivery_instructions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT for table `delivery_items`
--
ALTER TABLE `delivery_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=155;

--
-- AUTO_INCREMENT for table `inventory_attachment`
--
ALTER TABLE `inventory_attachment`
  MODIFY `id_inventory_attachment` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `kontrak`
--
ALTER TABLE `kontrak`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `migration_log`
--
ALTER TABLE `migration_log`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `spk`
--
ALTER TABLE `spk`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `spk_component_transactions`
--
ALTER TABLE `spk_component_transactions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

-- --------------------------------------------------------

--
-- Structure for view `inventory_unit_components`
--
DROP TABLE IF EXISTS `inventory_unit_components`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `inventory_unit_components`  AS SELECT `iu`.`id_inventory_unit` AS `id_inventory_unit`, `iu`.`no_unit` AS `no_unit`, `iu`.`serial_number` AS `serial_number`, `ia_battery`.`baterai_id` AS `model_baterai_id`, `ia_battery`.`sn_baterai` AS `sn_baterai`, `b`.`merk_baterai` AS `merk_baterai`, `b`.`tipe_baterai` AS `tipe_baterai`, `b`.`jenis_baterai` AS `jenis_baterai`, `ia_charger`.`charger_id` AS `model_charger_id`, `ia_charger`.`sn_charger` AS `sn_charger`, `c`.`merk_charger` AS `merk_charger`, `c`.`tipe_charger` AS `tipe_charger`, `ia_attachment`.`attachment_id` AS `model_attachment_id`, `ia_attachment`.`sn_attachment` AS `sn_attachment`, `a`.`tipe` AS `attachment_tipe`, `a`.`merk` AS `attachment_merk`, `a`.`model` AS `attachment_model` FROM ((((((`inventory_unit` `iu` left join `inventory_attachment` `ia_battery` on(((`iu`.`id_inventory_unit` = `ia_battery`.`id_inventory_unit`) and (`ia_battery`.`tipe_item` = 'battery') and (`ia_battery`.`status_unit` = 8)))) left join `baterai` `b` on((`ia_battery`.`baterai_id` = `b`.`id`))) left join `inventory_attachment` `ia_charger` on(((`iu`.`id_inventory_unit` = `ia_charger`.`id_inventory_unit`) and (`ia_charger`.`tipe_item` = 'charger') and (`ia_charger`.`status_unit` = 8)))) left join `charger` `c` on((`ia_charger`.`charger_id` = `c`.`id_charger`))) left join `inventory_attachment` `ia_attachment` on(((`iu`.`id_inventory_unit` = `ia_attachment`.`id_inventory_unit`) and (`ia_attachment`.`tipe_item` = 'attachment') and (`ia_attachment`.`status_unit` = 8)))) left join `attachment` `a` on((`ia_attachment`.`attachment_id` = `a`.`id_attachment`))) ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;