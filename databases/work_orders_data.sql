-- Work Orders Data Insertion Script
-- Created: September 23, 2025
-- This script populates the work orders tables with initial data

-- ========================================
-- 1. Work Order Categories
-- ========================================

INSERT INTO `work_order_categories` (`category_name`, `category_code`, `description`) VALUES
('Attachments & Accessories', 'ATT_ACC', 'Semua komponen tambahan dan aksesoris forklift'),
('Braking / Pengereman', 'BRAKE', 'Sistem pengereman dan komponen terkait'),
('Chassis & Body', 'CHASSIS', 'Rangka, bodi, dan struktur utama forklift'),
('Engine / Mesin', 'ENGINE', 'Mesin dan komponen penggerak utama'),
('Hidrolik', 'HYDRAULIC', 'Sistem hidrolik dan komponen terkait'),
('Kelistrikan', 'ELECTRIC', 'Sistem kelistrikan dan komponen elektronik'),
('Pengapian / Bahan Bakar', 'FUEL_IGN', 'Sistem bahan bakar dan pengapian'),
('Roda dan Ban', 'WHEEL_TIRE', 'Roda, ban, dan komponen terkait'),
('Safety', 'SAFETY', 'Komponen keselamatan kerja'),
('Transmisi', 'TRANSMISSION', 'Sistem transmisi dan perpindahan tenaga'),
('Pelumas & Fluida', 'LUBRICANT', 'Oli, pelumas, dan cairan operasional');
-- ========================================
-- 2. Work Order Subcategories
-- ========================================

INSERT INTO `work_order_subcategories` (`category_id`, `subcategory_name`, `subcategory_code`) VALUES
-- Attachments & Accessories
(1, 'Side Shifter', 'SIDE_SHIFTER'),
(1, 'Fork Positioner', 'FORK_POS'),
(1, 'Load Backrest Extension', 'LOAD_BACKREST'),
(1, 'Rotator', 'ROTATOR'),
(1, 'Clamps (Paper Roll, Carton, dll.)', 'CLAMPS'),
(1, 'Push/Pull Attachment', 'PUSH_PULL'),
(1, 'Single/Double Pallet Handler', 'PALLET_HANDLER'),
(1, 'AC (Air Conditioner)', 'AC'),
(1, 'Heater (Pemanas)', 'HEATER'),
(1, 'Radio/Audio System', 'RADIO'),

-- Braking / Pengereman
(2, 'Pedal Rem', 'BRAKE_PEDAL'),
(2, 'Master Cylinder', 'MASTER_CYL'),
(2, 'Booster Rem', 'BRAKE_BOOSTER'),
(2, 'Saluran Rem', 'BRAKE_LINE'),
(2, 'Wheel Cylinder / Kaliper Rem', 'WHEEL_CYL'),
(2, 'Brake Shoe', 'BRAKE_SHOE'),
(2, 'Brake Pad', 'BRAKE_PAD'),
(2, 'Brake Drum', 'BRAKE_DRUM'),
(2, 'Brake Disc', 'BRAKE_DISC'),
(2, 'Parking Brake', 'PARKING_BRAKE'),
(2, 'Tuas Rem Parkir', 'PARK_BRAKE_LEVER'),
(2, 'Kabel Rem Parkir', 'PARK_BRAKE_CABLE'),
(2, 'Mekanisme Rem Parkir', 'PARK_BRAKE_MECH'),

-- Chassis & Body
(3, 'Rangka Utama (Frame)', 'MAIN_FRAME'),
(3, 'Mast', 'MAST'),
(3, 'Outer Mast', 'OUTER_MAST'),
(3, 'Inner Mast', 'INNER_MAST'),
(3, 'Intermediate Mast', 'INT_MAST'),
(3, 'Roller Mast', 'ROLLER_MAST'),
(3, 'Chain Mast', 'CHAIN_MAST'),
(3, 'Carriage', 'CARRIAGE'),
(3, 'Forks', 'FORKS'),
(3, 'Overhead Guard', 'OVERHEAD_GUARD'),
(3, 'Seat', 'SEAT'),
(3, 'Panel Instrumen', 'INSTRUMENT_PANEL'),
(3, 'Lantai Kabin', 'CABIN_FLOOR'),
(3, 'Kap Mesin', 'ENGINE_HOOD'),
(3, 'Counterweight', 'COUNTERWEIGHT'),

-- Engine / Mesin
(4, 'Mesin Pembakaran Dalam (ICE)', 'ICE_ENGINE'),
(4, 'Blok Mesin', 'ENGINE_BLOCK'),
(4, 'Kepala Silinder', 'CYLINDER_HEAD'),
(4, 'Komponen Internal Mesin', 'ENGINE_INTERNAL'),
(4, 'Sistem Pendingin', 'COOLING_SYS'),
(4, 'Radiator', 'RADIATOR'),
(4, 'Selang Radiator', 'RADIATOR_HOSE'),
(4, 'Termostat', 'THERMOSTAT'),
(4, 'Pompa Air', 'WATER_PUMP'),
(4, 'Kipas Pendingin', 'COOLING_FAN'),
(4, 'Sistem Pelumasan Mesin', 'ENGINE_LUB_SYS'),
(4, 'Pompa Oli Mesin', 'ENGINE_OIL_PUMP'),
(4, 'Filter Oli Mesin', 'ENGINE_OIL_FILTER'),
(4, 'Oil Pan', 'OIL_PAN'),
(4, 'Motor Elektrik', 'ELECTRIC_MOTOR'),
(4, 'Motor Traksi', 'TRACTION_MOTOR'),
(4, 'Motor Hidrolik', 'HYDRAULIC_MOTOR'),
(4, 'Motor Kemudi', 'STEERING_MOTOR'),
(4, 'Poros Penggerak (Drive Shaft)', 'DRIVE_SHAFT'),

-- Hidrolik
(5, 'Pompa Hidrolik', 'HYD_PUMP'),
(5, 'Tangki Hidrolik', 'HYD_TANK'),
(5, 'Filter Oli Hidrolik', 'HYD_FILTER'),
(5, 'Control Valve', 'CONTROL_VALVE'),
(5, 'Directional Control Valve', 'DIR_CONTROL_VALVE'),
(5, 'Pressure Relief Valve', 'PRESSURE_RELIEF'),
(5, 'Flow Control Valve', 'FLOW_CONTROL'),
(5, 'Silinder Hidrolik', 'HYD_CYLINDER'),
(5, 'Lift Cylinder', 'LIFT_CYLINDER'),
(5, 'Tilt Cylinder', 'TILT_CYLINDER'),
(5, 'Steering Cylinder', 'STEERING_CYL'),
(5, 'Side Shift Cylinder', 'SIDE_SHIFT_CYL'),
(5, 'Attachment Cylinder', 'ATTACH_CYL'),
(5, 'Selang Hidrolik', 'HYD_HOSE'),
(5, 'Fitting dan Konektor Hidrolik', 'HYD_FITTING'),
(5, 'Steering Wheel', 'STEERING_WHEEL'),
(5, 'Steering Column', 'STEERING_COLUMN'),
(5, 'Steering Linkage', 'STEERING_LINKAGE'),
(5, 'Power Steering System', 'POWER_STEERING_SYS'),
(5, 'Power Steering Pump', 'POWER_STEERING_PUMP'),
(5, 'Power Steering Fluid Reservoir', 'PS_RESERVOIR'),
(5, 'Power Steering Cylinder', 'PS_CYLINDER'),
(5, 'Selang Power Steering', 'PS_HOSE'),
(5, 'Steering Axle', 'STEERING_AXLE'),
(5, 'Knuckle', 'KNUCKLE'),

-- Kelistrikan
(6, 'Baterai (Aki)', 'BATTERY'),
(6, 'Kabel-kabel (Wiring Harness)', 'WIRING_HARNESS'),
(6, 'Fuse (Sekering)', 'FUSE'),
(6, 'Relay', 'RELAY'),
(6, 'Switch (Saklar)', 'SWITCH'),
(6, 'Lampu Depan', 'HEADLIGHT'),
(6, 'Lampu Belakang', 'TAILLIGHT'),
(6, 'Lampu Sein', 'TURN_SIGNAL'),
(6, 'Lampu Peringatan', 'WARNING_LIGHT'),
(6, 'Lampu Kerja', 'WORK_LIGHT'),
(6, 'Klakson', 'HORN'),
(6, 'Sensor Suhu', 'TEMP_SENSOR'),
(6, 'Sensor Tekanan Oli', 'OIL_PRESSURE_SENSOR'),
(6, 'Sensor Level Bahan Bakar', 'FUEL_LEVEL_SENSOR'),
(6, 'Sensor Kecepatan', 'SPEED_SENSOR'),
(6, 'Sensor Posisi', 'POSITION_SENSOR'),
(6, 'Instrument Cluster', 'INSTRUMENT_CLUSTER'),
(6, 'Sistem Pengisian Daya Baterai', 'BATTERY_CHARGING_SYS'),
(6, 'ECU / Modul Kontrol', 'ECU'),
(6, 'Sistem Manajemen Baterai (BMS)', 'BMS'),

-- Pengapian / Bahan Bakar
(7, 'Sistem Bahan Bakar', 'FUEL_SYSTEM'),
(7, 'Tangki Bahan Bakar', 'FUEL_TANK'),
(7, 'Saluran Bahan Bakar', 'FUEL_LINE'),
(7, 'Pompa Bahan Bakar', 'FUEL_PUMP'),
(7, 'Filter Bahan Bakar', 'FUEL_FILTER'),
(7, 'Injector (Diesel/LPG)', 'INJECTOR'),
(7, 'Karburator (LPG/Bensin)', 'CARBURETOR'),
(7, 'Regulator Tekanan Bahan Bakar', 'FUEL_REGULATOR'),
(7, 'Sistem Udara Masuk', 'AIR_INTAKE_SYS'),
(7, 'Air Filter', 'AIR_FILTER'),
(7, 'Intake Manifold', 'INTAKE_MANIFOLD'),
(7, 'Turbocharger', 'TURBOCHARGER'),
(7, 'Sistem Pembuangan', 'EXHAUST_SYSTEM'),
(7, 'Exhaust Manifold', 'EXHAUST_MANIFOLD'),
(7, 'Pipa Knalpot', 'EXHAUST_PIPE'),
(7, 'Muffler', 'MUFFLER'),
(7, 'Sistem Pengapian (Bensin/LPG)', 'IGNITION_SYSTEM'),
(7, 'Busi', 'SPARK_PLUG'),
(7, 'Coil Pengapian', 'IGNITION_COIL'),
(7, 'Distributor', 'DISTRIBUTOR'),

-- Roda dan Ban
(8, 'Ban', 'TIRE'),
(8, 'Velg', 'RIM'),
(8, 'Baut Roda (Lug Nuts)', 'LUG_NUTS'),

-- Safety
(9, 'Seat Belt', 'SEAT_BELT'),
(9, 'Alarm Mundur', 'REVERSE_ALARM'),
(9, 'Operator Presence System (OPS)', 'OPS'),
(9, 'Emergency Stop Button', 'EMERGENCY_STOP'),
(9, 'Fire Extinguisher (APAR)', 'FIRE_EXTINGUISHER'),
(9, 'Mirror (Spion)', 'MIRROR'),

-- Transmisi
(10, 'Gearbox', 'GEARBOX'),
(10, 'Kopling (Clutch)', 'CLUTCH'),
(10, 'Torque Converter', 'TORQUE_CONVERTER'),
(10, 'Final Drive (Gardan)', 'FINAL_DRIVE'),
(10, 'Poros Penggerak (Drive Shaft)', 'DRIVE_SHAFT_TRANS'),

-- Pelumas & Fluida
(11, 'Oli Mesin', 'ENGINE_OIL'),
(11, 'Oli Transmisi', 'TRANSMISSION_OIL'),
(11, 'Oli Hidrolik', 'HYDRAULIC_OIL'),
(11, 'Minyak Rem', 'BRAKE_FLUID'),
(11, 'Coolant (Cairan Pendingin)', 'COOLANT'),
(11, 'Grease (Gemuk Pelumas untuk bagian bergerak)', 'GREASE');

-- ========================================
-- 3. Work Order Priorities
-- ========================================

INSERT INTO `work_order_priorities` (`priority_name`, `priority_code`, `priority_level`, `priority_color`, `description`, `sla_hours`) VALUES
('Critical', 'CRITICAL', 5, 'danger', 'Memerlukan tindakan segera - unit tidak bisa beroperasi', 2),
('High', 'HIGH', 4, 'warning', 'Prioritas tinggi - berpotensi mengganggu operasional', 8),
('Medium', 'MEDIUM', 3, 'info', 'Prioritas sedang - perlu ditangani dalam waktu normal', 24),
('Low', 'LOW', 2, 'secondary', 'Prioritas rendah - dapat dijadwalkan', 72),
('Routine', 'ROUTINE', 1, 'success', 'Perawatan rutin - dapat dijadwalkan sesuai kebutuhan', 168);

-- ========================================
-- 4. Work Order Statuses
-- ========================================

INSERT INTO `work_order_statuses` (`status_name`, `status_code`, `status_color`, `description`, `is_final_status`, `sort_order`) VALUES
('Open', 'OPEN', 'info', 'Work order baru dibuat dan menunggu untuk ditangani', 0, 1),
('Assigned', 'ASSIGNED', 'primary', 'Work order telah ditugaskan ke teknisi', 0, 2),
('In Progress', 'IN_PROGRESS', 'warning', 'Sedang dalam proses perbaikan', 0, 3),
('Waiting Parts', 'WAITING_PARTS', 'secondary', 'Menunggu spare parts atau komponen', 0, 4),
('Testing', 'TESTING', 'info', 'Dalam tahap pengujian setelah perbaikan', 0, 5),
('Completed', 'COMPLETED', 'success', 'Perbaikan selesai dan unit siap digunakan', 1, 6),
('Closed', 'CLOSED', 'dark', 'Work order ditutup dan diselesaikan', 1, 7),
('Cancelled', 'CANCELLED', 'danger', 'Work order dibatalkan', 1, 8),
('On Hold', 'ON_HOLD', 'warning', 'Work order ditunda sementara', 0, 9);

-- ========================================
-- 5. Sample Staff Data
-- ========================================

INSERT INTO `work_order_staff` (`staff_name`, `staff_role`) VALUES
-- Admins
('Novi', 'ADMIN'),
('Sari', 'ADMIN'),
('Andi', 'ADMIN'),

-- Foremen
('YOGA', 'FOREMAN'),
('Budi', 'FOREMAN'),
('Eko', 'FOREMAN'),

-- Mechanics
('KURNIA', 'MECHANIC'),
('BAGUS', 'MECHANIC'),
('Deni', 'MECHANIC'),
('Rudi', 'MECHANIC'),
('Wahyu', 'MECHANIC'),
('Joko', 'MECHANIC'),

-- Helpers
('Agus', 'HELPER'),
('Dimas', 'HELPER'),
('Fajar', 'HELPER'),
('Hendra', 'HELPER'),
('Iwan', 'HELPER');

-- ========================================
-- 6. Sample Work Order for Testing
-- ========================================

-- Insert sample work order (Note: Make sure unit_id=1 and user_id=1 exist in your database)
INSERT INTO `work_orders` (
    `work_order_number`, 
    `report_date`, 
    `unit_id`, 
    `order_type`, 
    `priority_id`, 
    `requested_repair_time`, 
    `category_id`, 
    `subcategory_id`, 
    `complaint_description`, 
    `status_id`, 
    `admin_staff_id`,
    `foreman_staff_id`,
    `mechanic_staff_id`,
    `repair_description`,
    `sparepart_used`,
    `area`, 
    `created_by`
) VALUES (
    '15059', 
    '2025-09-18 08:52:52', 
    1, 
    'COMPLAINT', 
    2, 
    '2025-09-18 09:00:00', 
    8, 
    1, 
    'Ban depan belakang gundul', 
    1, 
    1,
    1,
    1,
    'Ban depan belakang gundul',
    'Ban hidup 700-12, ban hidup 600-9',
    'PURWAKARTA', 
    1
);

-- Add status history for the sample work order
INSERT INTO `work_order_status_history` (`work_order_id`, `from_status_id`, `to_status_id`, `changed_by`, `change_reason`) VALUES
(1, NULL, 1, 1, 'Work order created');