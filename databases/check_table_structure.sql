-- Check table structure before migration
USE optima_db;

-- Check inventory_unit structure
DESCRIBE inventory_unit;

-- Check kontrak_unit structure
DESCRIBE kontrak_unit;

-- Check delivery_instructions structure  
DESCRIBE delivery_instructions;

-- Check existing tables
SHOW TABLES LIKE '%workflow%';
SHOW TABLES LIKE '%jenis%';
SHOW TABLES LIKE '%tujuan%';