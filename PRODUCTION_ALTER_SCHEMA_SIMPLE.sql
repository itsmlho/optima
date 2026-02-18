-- ========================================
-- PRODUCTION SCHEMA UPDATE (SIMPLE VERSION)
-- ========================================
-- Purpose: Add rental_type column to kontrak table
-- Run this in PHPMyAdmin SQL tab
-- Date: 2026-02-18
-- ========================================

-- Select database
USE u138256737_optima_db;

-- Add rental_type column to kontrak table
ALTER TABLE kontrak 
ADD COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') 
NOT NULL DEFAULT 'PO_ONLY'
AFTER no_kontrak;

-- Verify it was added
SHOW COLUMNS FROM kontrak LIKE 'rental_type';
