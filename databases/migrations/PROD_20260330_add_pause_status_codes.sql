-- ============================================================
-- Migration: PROD_20260330_add_pause_status_codes
-- Purpose  : Add new pause/hold status codes to work_order_statuses
--            to support detailed pause reason selection on Work Orders.
-- Run on   : Production (Hostinger) via phpMyAdmin
-- Safe     : INSERT IGNORE - safe to run multiple times
-- ============================================================

-- Add new pause status codes (INSERT IGNORE skips if already exists)
INSERT IGNORE INTO `work_order_statuses` (`status_code`, `status_name`, `description`, `status_color`, `sort_order`) VALUES
('WAITING_SCHEDULE', 'Menunggu Jadwal',  'Work order ditunda menunggu jadwal/schedule',         'secondary', 40),
('WAITING_PERMIT',   'Menunggu Izin',    'Work order ditunda menunggu izin/permit kerja',         'secondary', 41),
('WAITING_TOOLS',    'Menunggu Tools',   'Work order ditunda menunggu alat/tools khusus',         'secondary', 42),
('OTHER_HOLD',       'On Hold Lainnya',  'Work order ditunda dengan alasan lainnya',               'secondary', 43);

-- Verify: run this to confirm inserted rows
-- SELECT status_code, status_name FROM work_order_statuses ORDER BY `order`;
