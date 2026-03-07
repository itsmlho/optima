---
name: database-migration
description: "Database migration workflow for Optima project. Use when: creating SQL migrations, modifying database schema, adding/changing tables/columns, preparing production deployment SQL. Ensures data integrity, backup safety, proper transaction handling, and production deployment checklist compliance."
---

# Database Migration Skill

## Purpose
Safely create and execute database migrations for the Optima project with data integrity checks, rollback capability, and production deployment safety.

## When to Use This Skill
- Creating new tables or altering existing table schemas
- Adding, modifying, or removing columns
- Creating or updating indexes, foreign keys, or constraints
- Preparing SQL scripts for production deployment
- Reviewing migration files before execution

## Constraints & Safety Rules

### 🚨 CRITICAL SAFETY CHECKS

1. **ALWAYS backup before schema changes**
2. **NEVER use `DROP TABLE` without explicit user confirmation**
3. **TEST on local database first, then staging, then production**
4. **Use transactions for multi-statement migrations**
5. **Check for existing data before ALTER TABLE**
6. **Verify foreign key references before adding constraints**

## Migration File Structure

All migration files go in: `databases/migrations/YYYY_MM_DD_description.sql`

### Standard Migration Template

```sql
-- ============================================================
-- Migration: [Description]
-- Date: YYYY-MM-DD
-- Author: [Name]
-- Purpose: [Detailed explanation]
-- ============================================================

-- Pre-execution checks
-- 1. Backup database: mysqldump -u root optima > backup_YYYYMMDD.sql
-- 2. Verify in development environment first
-- 3. Review with team before production deployment

START TRANSACTION;

-- ============================================================
-- MIGRATION LOGIC
-- ============================================================

-- Example: Create new table
CREATE TABLE IF NOT EXISTS `new_table` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Example: Add column to existing table
-- Check if column exists first
SET @col_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'existing_table' 
    AND COLUMN_NAME = 'new_column'
);

SET @query = IF(
    @col_exists = 0,
    'ALTER TABLE `existing_table` ADD COLUMN `new_column` VARCHAR(100) NULL AFTER `some_column`',
    'SELECT "Column already exists" AS message'
);

PREPARE stmt FROM @query;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================
-- VERIFICATION QUERIES
-- ============================================================

-- Verify table structure
DESCRIBE `new_table`;

-- Verify data integrity
SELECT COUNT(*) AS total_records FROM `new_table`;

-- ============================================================
-- ROLLBACK SCRIPT (commented out)
-- ============================================================
/*
-- In case of issues, execute these commands:

DROP TABLE IF EXISTS `new_table`;

ALTER TABLE `existing_table` DROP COLUMN `new_column`;

-- Then restore from backup
*/

COMMIT;

-- ============================================================
-- POST-MIGRATION VERIFICATION
-- ============================================================
-- Run these queries to verify success:
-- 1. SHOW TABLES LIKE 'new_table';
-- 2. SELECT * FROM new_table LIMIT 10;
-- 3. Check application functionality
-- ============================================================
```

## Common Migration Patterns

### Pattern 1: Add New Column with Data Migration

```sql
START TRANSACTION;

-- Add new column
ALTER TABLE `units` 
ADD COLUMN `chassis_number` VARCHAR(50) NULL AFTER `unit_model`;

-- Migrate existing data if needed
UPDATE `units` 
SET `chassis_number` = CONCAT('CH-', unit_id) 
WHERE `chassis_number` IS NULL;

-- Add index if needed
CREATE INDEX `idx_chassis_number` ON `units`(`chassis_number`);

COMMIT;
```

### Pattern 2: Create New Table with Foreign Keys

```sql
START TRANSACTION;

-- Create child table
CREATE TABLE IF NOT EXISTS `unit_maintenance_logs` (
    `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `unit_id` INT UNSIGNED NOT NULL,
    `maintenance_date` DATE NOT NULL,
    `description` TEXT NULL,
    `cost` DECIMAL(15,2) NULL,
    `created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`log_id`),
    INDEX `idx_unit_id` (`unit_id`),
    INDEX `idx_maintenance_date` (`maintenance_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key constraint
ALTER TABLE `unit_maintenance_logs`
ADD CONSTRAINT `fk_maintenance_unit`
FOREIGN KEY (`unit_id`) REFERENCES `units`(`unit_id`)
ON DELETE RESTRICT
ON UPDATE CASCADE;

COMMIT;
```

### Pattern 3: Modify Column Type Safely

```sql
START TRANSACTION;

-- Check for data that would be truncated
SELECT unit_id, LENGTH(unit_notes) AS notes_length 
FROM units 
WHERE LENGTH(unit_notes) > 255;

-- If safe, proceed with modification
ALTER TABLE `units` 
MODIFY COLUMN `unit_notes` TEXT NULL;

COMMIT;
```

### Pattern 4: Add Unique Constraint with Duplicate Check

```sql
START TRANSACTION;

-- Find duplicates first
SELECT unit_polisi, COUNT(*) as duplicate_count
FROM units
GROUP BY unit_polisi
HAVING COUNT(*) > 1;

-- If no duplicates or after resolving:
ALTER TABLE `units`
ADD UNIQUE INDEX `unique_unit_polisi` (`unit_polisi`);

COMMIT;
```

## Production Deployment Workflow

### Step 1: Prepare Migration File
```bash
# Create migration file
cd databases/migrations
notepad YYYY_MM_DD_description.sql
```

### Step 2: Test Locally
```bash
# Import to local database
mysql -u root -p optima < databases/migrations/YYYY_MM_DD_description.sql

# Verify changes
mysql -u root -p optima -e "DESCRIBE table_name;"
```

### Step 3: Test in Staging (if available)
```bash
# Same process but on staging server
```

### Step 4: Production Deployment Checklist

✅ Complete this checklist before production deployment:

```markdown
## Pre-Deployment Checklist
- [ ] Migration tested successfully in local environment
- [ ] Migration tested successfully in staging environment (if available)
- [ ] Backup of production database created
- [ ] Migration file reviewed by team member
- [ ] Rollback script prepared and tested
- [ ] Estimated execution time calculated
- [ ] Maintenance window scheduled (if needed)
- [ ] Application tested with new schema locally

## During Deployment
- [ ] Announce maintenance window to users
- [ ] Create production backup: mysqldump -u user -p optima > backup_YYYYMMDD_HHMM.sql
- [ ] Execute migration script
- [ ] Verify schema changes: DESCRIBE table_name;
- [ ] Verify data integrity: SELECT COUNT(*) FROM table_name;
- [ ] Test critical application features

## Post-Deployment
- [ ] Application running without errors
- [ ] Users notified of completion
- [ ] Migration documented in RECENT_CHANGES.md
- [ ] Backup file stored securely
- [ ] Monitor application logs for 24 hours
```

## Optima-Specific Database Rules

### Table Naming Convention
- Singular for entity tables: `unit`, `contract`, `employee`
- Plural for junction tables: `unit_contracts`, `contract_renewals`
- Lowercase with underscores: `marketing_quotations`

### Column Naming Convention
- Primary key: `table_id` (e.g., `unit_id`, `contract_id`)
- Foreign key: `referenced_table_id` (e.g., `unit_id` in contracts table)
- Timestamps: `created_at`, `updated_at`, `deleted_at`
- Booleans: `is_active`, `is_deleted`, `is_verified`

### Common Column Types for Optima
```sql
-- IDs
`id` INT UNSIGNED NOT NULL AUTO_INCREMENT

-- Text fields
`name` VARCHAR(255) NOT NULL
`description` TEXT NULL
`notes` TEXT NULL

-- Dates
`contract_date` DATE NOT NULL
`created_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP
`updated_at` DATETIME NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- Money
`contract_value` DECIMAL(15,2) NOT NULL DEFAULT 0.00

-- Status/Enum
`status` ENUM('active','inactive','pending') NOT NULL DEFAULT 'active'

-- Boolean
`is_active` TINYINT(1) NOT NULL DEFAULT 1
```

### Indexes for Performance
```sql
-- Add indexes for frequently queried columns
INDEX `idx_status` (`status`)
INDEX `idx_created_at` (`created_at`)
INDEX `idx_unit_polisi` (`unit_polisi`)
INDEX `idx_customer_name` (`customer_name`)
```

## Migration Verification Queries

After running migration, execute these to verify:

```sql
-- 1. Check table structure
SHOW CREATE TABLE `table_name`;

-- 2. Check columns
DESCRIBE `table_name`;

-- 3. Check indexes
SHOW INDEX FROM `table_name`;

-- 4. Check foreign keys
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'table_name'
AND REFERENCED_TABLE_NAME IS NOT NULL;

-- 5. Check data integrity
SELECT COUNT(*) FROM `table_name`;
SELECT * FROM `table_name` LIMIT 10;
```

## Rollback Procedures

### If Migration Fails:

1. **STOP immediately** - Don't execute more statements
2. **ROLLBACK transaction** (if not auto-committed)
3. **Restore from backup**:
   ```bash
   mysql -u user -p optima < backup_YYYYMMDD_HHMM.sql
   ```
4. **Investigate error** - Check MySQL error log
5. **Fix migration script** - Correct the issue
6. **Re-test locally** before retry

### Common Rollback Commands

```sql
-- Drop newly created table
DROP TABLE IF EXISTS `new_table`;

-- Remove newly added column
ALTER TABLE `table_name` DROP COLUMN `column_name`;

-- Remove newly added index
ALTER TABLE `table_name` DROP INDEX `index_name`;

-- Remove foreign key
ALTER TABLE `table_name` DROP FOREIGN KEY `fk_constraint_name`;
```

## Error Handling

### Common Errors and Solutions

1. **Error 1050: Table already exists**
   - Use `CREATE TABLE IF NOT EXISTS`

2. **Error 1060: Duplicate column name**
   - Check if column exists with INFORMATION_SCHEMA query first

3. **Error 1215: Cannot add foreign key constraint**
   - Verify referenced table and column exist
   - Ensure data types match exactly
   - Check if referenced column has an index

4. **Error 1451: Cannot delete or update a parent row**
   - Change foreign key to `ON DELETE RESTRICT` or `ON DELETE CASCADE`

5. **Error 1406: Data too long for column**
   - Check existing data before reducing column size
   - Migrate data first if needed

## Resources

- Project DB Schema: `docs/DATABASE_SCHEMA.md`
- Migration Files: `databases/migrations/`
- Deployment Guide: `PRODUCTION_DEPLOYMENT_READY.md`
- Backup Scripts: `prepare_production_sql.bat`

---

**Remember**: Backup First, Test Thoroughly, Deploy Safely!
