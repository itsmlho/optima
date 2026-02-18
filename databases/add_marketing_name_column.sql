-- =====================================================
-- ADD MARKETING NAME COLUMN TO CUSTOMERS
-- Date: 2026-02-17
-- Description: Add marketing_name column to track 
--              which marketing person handles each customer
-- =====================================================

USE optima_ci;

-- Add marketing_name column
ALTER TABLE customers 
ADD COLUMN marketing_name VARCHAR(50) NULL 
AFTER customer_name;

-- Add index for faster filtering
CREATE INDEX idx_marketing_name ON customers(marketing_name);

-- Verify the change
DESCRIBE customers;

-- =====================================================
-- USAGE:
-- mysql -u root < databases/add_marketing_name_column.sql
-- =====================================================
