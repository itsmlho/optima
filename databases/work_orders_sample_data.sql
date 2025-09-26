-- Insert sample data for work order lookup tables

-- Work Order Statuses
INSERT IGNORE INTO `work_order_statuses` (`id`, `status_name`, `status_code`, `status_color`, `description`, `is_final_status`, `sort_order`, `is_active`) VALUES
(1, 'Open', 'OPEN', 'primary', 'Work order baru yang belum ditangani', 0, 1, 1),
(2, 'In Progress', 'IN_PROGRESS', 'warning', 'Work order sedang dikerjakan', 0, 2, 1),
(3, 'On Hold', 'ON_HOLD', 'secondary', 'Work order ditangguhkan sementara', 0, 3, 1),
(4, 'Completed', 'COMPLETED', 'success', 'Work order telah selesai', 1, 4, 1),
(5, 'Cancelled', 'CANCELLED', 'danger', 'Work order dibatalkan', 1, 5, 1);

-- Work Order Priorities
INSERT IGNORE INTO `work_order_priorities` (`id`, `priority_name`, `priority_code`, `priority_level`, `priority_color`, `description`, `sla_hours`, `is_active`) VALUES
(1, 'Critical', 'CRITICAL', 5, 'danger', 'Prioritas kritis - harus segera ditangani', 2, 1),
(2, 'High', 'HIGH', 4, 'warning', 'Prioritas tinggi', 8, 1),
(3, 'Medium', 'MEDIUM', 3, 'info', 'Prioritas sedang', 24, 1),
(4, 'Low', 'LOW', 2, 'secondary', 'Prioritas rendah', 72, 1),
(5, 'Routine', 'ROUTINE', 1, 'light', 'Pekerjaan rutin', 168, 1);

-- Work Order Categories
INSERT IGNORE INTO `work_order_categories` (`id`, `category_name`, `category_code`, `description`, `is_active`) VALUES
(1, 'Mechanical', 'MECHANICAL', 'Perbaikan mekanis', 1),
(2, 'Electrical', 'ELECTRICAL', 'Perbaikan listrik', 1),
(3, 'Hydraulic', 'HYDRAULIC', 'Perbaikan sistem hidrolik', 1),
(4, 'Preventive Maintenance', 'PM', 'Perawatan preventif terjadwal', 1),
(5, 'Emergency Repair', 'EMERGENCY', 'Perbaikan darurat', 1),
(6, 'Inspection', 'INSPECTION', 'Pemeriksaan dan inspeksi', 1);

-- Work Order Subcategories
INSERT IGNORE INTO `work_order_subcategories` (`id`, `category_id`, `subcategory_name`, `subcategory_code`, `description`, `is_active`) VALUES
-- Mechanical subcategories
(1, 1, 'Engine', 'ENG', 'Perbaikan mesin', 1),
(2, 1, 'Transmission', 'TRANS', 'Perbaikan transmisi', 1),
(3, 1, 'Brakes', 'BRAKE', 'Perbaikan rem', 1),
(4, 1, 'Suspension', 'SUSP', 'Perbaikan suspensi', 1),
-- Electrical subcategories
(5, 2, 'Lighting', 'LIGHT', 'Sistem penerangan', 1),
(6, 2, 'Wiring', 'WIRE', 'Sistem kelistrikan', 1),
(7, 2, 'Battery', 'BATT', 'Sistem baterai', 1),
-- Hydraulic subcategories
(8, 3, 'Pumps', 'PUMP', 'Pompa hidrolik', 1),
(9, 3, 'Cylinders', 'CYL', 'Silinder hidrolik', 1),
(10, 3, 'Hoses', 'HOSE', 'Selang hidrolik', 1);

-- Work Order Staff
INSERT IGNORE INTO `work_order_staff` (`id`, `staff_name`, `staff_role`, `is_active`) VALUES
-- Admin Staff
(1, 'Admin Service 1', 'ADMIN', 1),
(2, 'Admin Service 2', 'ADMIN', 1),
-- Foreman Staff
(3, 'Foreman A', 'FOREMAN', 1),
(4, 'Foreman B', 'FOREMAN', 1),
(5, 'Foreman C', 'FOREMAN', 1),
-- Mechanic Staff
(6, 'Mekanik 1', 'MECHANIC', 1),
(7, 'Mekanik 2', 'MECHANIC', 1),
(8, 'Mekanik 3', 'MECHANIC', 1),
(9, 'Mekanik 4', 'MECHANIC', 1),
(10, 'Mekanik 5', 'MECHANIC', 1),
-- Helper Staff
(11, 'Helper 1', 'HELPER', 1),
(12, 'Helper 2', 'HELPER', 1),
(13, 'Helper 3', 'HELPER', 1),
(14, 'Helper 4', 'HELPER', 1),
(15, 'Helper 5', 'HELPER', 1),
(16, 'Helper 6', 'HELPER', 1);