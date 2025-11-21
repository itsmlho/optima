-- Migration: Create work_order_sparepart_returns table
-- Date: 2025-01-15
-- Description: Table untuk tracking pengembalian sparepart dari work order

CREATE TABLE IF NOT EXISTS `work_order_sparepart_returns` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `work_order_id` int(11) NOT NULL,
  `work_order_sparepart_id` int(11) DEFAULT NULL,
  `sparepart_code` varchar(50) NOT NULL,
  `sparepart_name` varchar(255) NOT NULL,
  `quantity_brought` int(11) NOT NULL,
  `quantity_used` int(11) NOT NULL DEFAULT 0,
  `quantity_return` int(11) NOT NULL,
  `satuan` varchar(50) NOT NULL,
  `status` enum('PENDING','CONFIRMED','CANCELLED') DEFAULT 'PENDING',
  `return_notes` text DEFAULT NULL,
  `confirmed_by` int(11) DEFAULT NULL,
  `confirmed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `work_order_id` (`work_order_id`),
  KEY `work_order_sparepart_id` (`work_order_sparepart_id`),
  KEY `status` (`status`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

