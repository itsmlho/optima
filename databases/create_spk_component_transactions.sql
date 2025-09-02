-- Create spk_component_transactions table
-- This table tracks the assignment of components (units, attachments, batteries, chargers) to SPKs
-- during the preparation and approval workflow

CREATE TABLE IF NOT EXISTS `spk_component_transactions` (
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
  -- Foreign key constraint removed due to SPK table not having primary key
  -- CONSTRAINT `fk_spk_component_spk` FOREIGN KEY (`spk_id`) REFERENCES `spk` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Add some sample data for testing
INSERT INTO `spk_component_transactions` (`spk_id`, `transaction_type`, `component_type`, `component_id`, `mekanik`, `catatan`, `created_by`) VALUES
(1, 'ASSIGN', 'UNIT', 1, 'John Doe', 'Unit assigned for SPK preparation', 1),
(1, 'ASSIGN', 'ATTACHMENT', 1, 'John Doe', 'Forklift attachment assigned', 1),
(1, 'ASSIGN', 'BATTERY', 1, 'John Doe', 'Battery assigned for unit', 1);
