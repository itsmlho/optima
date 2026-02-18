-- ==============================================
-- UNITS WITHOUT CONTRACT - Customer & Location Assignment
-- Generated: 2026-02-17 13:25:11
-- Total: 18 units
-- Note: kontrak_id remains NULL (no contract)
-- ==============================================

SET FOREIGN_KEY_CHECKS = 0;

UPDATE inventory_unit SET customer_id = 105 WHERE no_unit = '2755';
UPDATE inventory_unit SET customer_id = 106, customer_location_id = 307 WHERE no_unit = '5459';
UPDATE inventory_unit SET customer_id = 118 WHERE no_unit = '2762';
UPDATE inventory_unit SET customer_id = 118 WHERE no_unit = '3730';
UPDATE inventory_unit SET customer_id = 2 WHERE no_unit = '3644';
UPDATE inventory_unit SET customer_id = 22 WHERE no_unit = '3710';
UPDATE inventory_unit SET customer_id = 41, customer_location_id = 118 WHERE no_unit = '2093';
UPDATE inventory_unit SET customer_id = 41 WHERE no_unit = '3130';
UPDATE inventory_unit SET customer_id = 41 WHERE no_unit = '3132';
UPDATE inventory_unit SET customer_id = 41, customer_location_id = 118 WHERE no_unit = '3649';
UPDATE inventory_unit SET customer_id = 41, customer_location_id = 118 WHERE no_unit = '1929';
UPDATE inventory_unit SET customer_id = 41, customer_location_id = 118 WHERE no_unit = '3249';
UPDATE inventory_unit SET customer_id = 41, customer_location_id = 118 WHERE no_unit = '3567';
UPDATE inventory_unit SET customer_id = 41 WHERE no_unit = '2453';
UPDATE inventory_unit SET customer_id = 80, customer_location_id = 230 WHERE no_unit = '2686';
UPDATE inventory_unit SET customer_id = 199, customer_location_id = 448 WHERE no_unit = '1885';
UPDATE inventory_unit SET customer_id = 199, customer_location_id = 449 WHERE no_unit = '2636';
UPDATE inventory_unit SET customer_id = 5, customer_location_id = 45 WHERE no_unit = '5607';

SET FOREIGN_KEY_CHECKS = 1;
