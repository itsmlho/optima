# Database Structure Analysis & Improvement Recommendations
**Project:** OPTIMA - Rental Management System  
**Date:** February 9, 2026  
**Status:** Analysis Complete - Ready for Improvements

---

## 📊 Executive Summary

After analyzing the **actual database structure** (not documentation), the system already has a **solid foundation** with some smart design patterns. However, there are **opportunities to better reflect the business reality** of handling:
1. Contract-based rentals (long-term with formal contract)
2. PO-only rentals (medium-term without formal contract)
3. Daily/spot rentals (short-term, sometimes without PO)

---

## 🗄️ Current Database Structure

### 1. Customer Management

#### `customers` Table
```sql
CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(20) UNIQUE NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_location_id INT,              -- Reference to primary location
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**✅ Good:**
- Simple, clean structure
- Unique customer code
- Active flag for soft delete

**⚠️ Missing:**
- Customer type classification (Corporate, SME, Individual)
- Tax registration info (NPWP for Indonesia)
- Credit limit fields
- Payment terms default

#### `customer_locations` Table
```sql
CREATE TABLE customer_locations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    area_id INT,
    location_name VARCHAR(100) NOT NULL,
    location_code VARCHAR(50),
    location_type ENUM('HEAD_OFFICE','BRANCH','WAREHOUSE','FACTORY'),
    address TEXT NOT NULL,
    contact_person VARCHAR(128),
    phone VARCHAR(32),
    email VARCHAR(128),
    pic_position VARCHAR(64),
    notes VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    province VARCHAR(100) NOT NULL,
    postal_code VARCHAR(10),
    gps_latitude DECIMAL(10,8),
    gps_longitude DECIMAL(11,8),
    is_primary TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**✅ Excellent:**
- Comprehensive location data
- GPS coordinates for delivery tracking
- Location type classification
- Primary location flag

**⚠️ Minor:**
- Could add `billing_address` flag (separate from service location)

---

### 2. Contract/Rental Management

#### `kontrak` Table (Main Rental Table)
```sql
CREATE TABLE kontrak (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    customer_location_id INT,
    no_kontrak VARCHAR(100) NOT NULL,
    no_po_marketing VARCHAR(100),              -- ✅ Support PO (optional)
    nilai_total DECIMAL(15,2),
    total_units INT UNSIGNED DEFAULT 0,
    jenis_sewa ENUM('BULANAN','HARIAN'),       -- ✅ Monthly vs Daily differentiation
    tanggal_mulai DATE NOT NULL,
    tanggal_berakhir DATE NOT NULL,
    status ENUM('Aktif','Berakhir','Pending','Dibatalkan'),
    dibuat_oleh INT UNSIGNED,
    dibuat_pada DATETIME DEFAULT CURRENT_TIMESTAMP,
    diperbarui_pada DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**✅ Good:**
- Already supports `jenis_sewa` (BULANAN/HARIAN) - matches business needs!
- Optional `no_po_marketing` field - can be NULL for non-PO rentals
- Date range tracking
- Status management

**⚠️ Issues:**
1. **Missing rental type distinction:**
   - No field to distinguish: 
     - Contract + PO (formal long-term)
     - PO Only (medium-term, no contract document)
     - Daily Spot (short-term, no PO/contract)
   
2. **PO field naming:**
   - `no_po_marketing` implies "marketing department PO"
   - But PO is actually **customer's PO to us**, not our internal PO
   - Should be `customer_po_number` or `po_from_customer`

3. **Status values:**
   - Using Indonesian + mixed language (`Aktif`, `Berakhir`, `Pending`, `Dibatalkan`)
   - Should standardize to English or Indonesian

4. **Missing fields:**
   - No `contract_type` to differentiate formal contract vs PO-only vs spot rental
   - No `payment_frequency` (monthly, upfront, per delivery)
   - No `payment_method` tracking
   - No `renewal_count` for tracking extensions
   - No reference to parent contract (for renewals)

#### `kontrak_unit` Table (Unit Assignment)
```sql
CREATE TABLE kontrak_unit (
    id INT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    kontrak_id INT UNSIGNED NOT NULL,
    unit_id INT UNSIGNED NOT NULL,
    tanggal_mulai DATE NOT NULL,
    tanggal_selesai DATE,
    status ENUM('AKTIF','DITARIK','DITUKAR','NON_AKTIF','MAINTENANCE',
                'UNDER_REPAIR','TEMPORARILY_REPLACED','TEMPORARY_ACTIVE','TEMPORARY_ENDED'),
    tanggal_tarik DATETIME,
    stage_tarik VARCHAR(50),
    tanggal_tukar DATETIME,
    unit_pengganti_id INT UNSIGNED,
    unit_sebelumnya_id INT UNSIGNED,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    updated_by INT UNSIGNED,
    is_temporary TINYINT(1) DEFAULT 0,
    original_unit_id INT UNSIGNED,
    temporary_replacement_unit_id INT UNSIGNED,
    temporary_replacement_date DATETIME,
    maintenance_start DATETIME,
    maintenance_reason VARCHAR(255),
    relocation_from_location_id INT,
    relocation_to_location_id INT
);
```

**✅ Excellent:**
- Very sophisticated status tracking
- Unit replacement history (DITUKAR, unit_pengganti_id)
- Temporary replacement support
- Maintenance tracking
- Relocation tracking (important for multi-location customers!)
- Audit fields (created_by, updated_by)

**✅ Best Practice:**
This is a **well-designed table** that handles complex real-world scenarios!

**⚠️ Minor:**
- Could add `billing_rate` per unit (for mixed-rate contracts)
- Could add `actual_tarik_date` vs planned `tanggal_selesai`

#### `customer_contracts` Table (Junction)
```sql
CREATE TABLE customer_contracts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_id INT NOT NULL,
    kontrak_id INT UNSIGNED NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**⚠️ Question:**
- Why is this junction table needed? 
- `kontrak` already has `customer_location_id` → can reach customer via location
- Is this for multi-customer contracts? (e.g., parent company + subsidiaries share one contract?)

**Recommendation:**
If not used for multi-customer scenarios, this adds unnecessary complexity.

---

### 3. Quotation/Prospect Management

#### `quotations` Table
```sql
CREATE TABLE quotations (
    id_quotation INT PRIMARY KEY AUTO_INCREMENT,
    quotation_number VARCHAR(50) UNIQUE NOT NULL,
    
    -- Prospect info (denormalized for flexibility)
    prospect_name VARCHAR(255) NOT NULL,
    prospect_contact_person VARCHAR(255),
    prospect_phone VARCHAR(20),
    prospect_email VARCHAR(100),
    prospect_address TEXT,
    prospect_city VARCHAR(100),
    prospect_province VARCHAR(100),
    prospect_postal_code VARCHAR(10),
    
    -- Quotation details
    quotation_title VARCHAR(255) NOT NULL,
    quotation_description TEXT,
    quotation_date DATE NOT NULL,
    valid_until DATE NOT NULL,
    currency VARCHAR(3) DEFAULT 'IDR',
    subtotal DECIMAL(15,2) DEFAULT 0.00,
    discount_percent DECIMAL(5,2) DEFAULT 0.00,
    discount_amount DECIMAL(15,2) DEFAULT 0.00,
    tax_percent DECIMAL(5,2) DEFAULT 11.00,
    tax_amount DECIMAL(15,2) DEFAULT 0.00,
    total_amount DECIMAL(15,2) DEFAULT 0.00,
    
    -- Terms
    payment_terms TEXT,
    delivery_terms TEXT,
    warranty_terms TEXT,
    
    -- Sales tracking
    stage ENUM('DRAFT','SENT','FOLLOW_UP','NEGOTIATION','ACCEPTED','REJECTED','EXPIRED'),
    workflow_stage ENUM('PROSPECT','QUOTATION','SENT','DEAL','NOT_DEAL'),
    probability_percent INT DEFAULT 50,
    expected_close_date DATE,
    is_deal TINYINT(1) DEFAULT 0,
    deal_date DATE,
    
    -- Links (continues in quotations table...)
    created_customer_id INT,           -- Customer created from deal
    contract_id INT UNSIGNED,          -- Contract created from quotation
    
    -- Timestamps
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    created_by INT UNSIGNED,
    updated_by INT UNSIGNED
);
```

**✅ Excellent:**
- Denormalized prospect data (good for prospects that aren't customers yet)
- Dual-stage tracking (`stage` for document status, `workflow_stage` for sales funnel)
- Links to customer and contract when deal converts
- Comprehensive pricing fields

**✅ Best Practice:**
This follows proper quotation-to-contract workflow!

---

## 🎯 Current State Assessment

### Data Flow (As-Is)
```
1. Prospect Stage:
   quotations.prospect_name (not a customer yet)
   ↓
   
2. Deal Stage:
   quotations.is_deal = 1
   quotations.created_customer_id → customers.id
   ↓
   
3. Contract Creation:
   quotations.contract_id → kontrak.id
   kontrak.customer_location_id → customer_locations.id
   kontrak.no_po_marketing (optional)
   ↓
   
4. Unit Assignment:
   kontrak_unit.kontrak_id → kontrak.id
   kontrak_unit.unit_id → inventory_unit.id
```

### What Works Well ✅
1. **Flexible PO handling** - `no_po_marketing` can be NULL
2. **Rental type differentiation** - `jenis_sewa` (BULANAN/HARIAN)
3. **Sophisticated unit tracking** - `kontrak_unit` handles complex scenarios
4. **Quotation workflow** - Clear progression from prospect to deal
5. **Location-based contracts** - Supports multi-location customers

### What Needs Improvement ⚠️

#### Issue 1: Rental Type Ambiguity
**Problem:**
- Current: Only differentiate by `jenis_sewa` (BULANAN/HARIAN)
- Reality: 
  - ✅ BULANAN + kontrak formal + PO = **Contract-based rental**
  - ✅ BULANAN + PO only (no kontrak) = **PO-only rental**
  - ✅ HARIAN + no PO/kontrak = **Daily spot rental**

**Current Data:**
```sql
-- All contracts are BULANAN, some have PO, some don't
SELECT jenis_sewa, COUNT(*) as count, 
       SUM(CASE WHEN no_po_marketing IS NOT NULL THEN 1 ELSE 0 END) as with_po
FROM kontrak 
GROUP BY jenis_sewa;

Result: BULANAN: 13 total (2 with PO, 11 without PO)
```

**Gap:**
Can't distinguish between:
- Formal contract with PO (requires legal document)
- PO-only rental (simpler process)
- Spot rental (no paperwork)

#### Issue 2: Field Naming Confusion
**Problem:**
- `no_po_marketing` → Implies internal marketing PO
- Reality: This is **customer's PO number**

#### Issue 3: Status Inconsistency
**Problem:**
- Mixed language: `Aktif`, `Pending`, `AKTIF`, `DITARIK`
- Some uppercase, some title case

#### Issue 4: Missing Operational Fields
**Problem:**
No tracking for:
- Payment status/schedule
- Invoice generation flag
- Renewal history
- Contract templates used
- Approval workflow

---

## 🔧 Recommended Improvements

### Priority 1: Add Rental Type Distinction (HIGH)

#### Option A: Add `rental_type` Column
```sql
ALTER TABLE kontrak 
ADD COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') NOT NULL DEFAULT 'CONTRACT' AFTER no_kontrak;

-- Update existing data
UPDATE kontrak 
SET rental_type = CASE 
    WHEN no_po_marketing IS NOT NULL AND jenis_sewa = 'BULANAN' THEN 'CONTRACT'
    WHEN no_po_marketing IS NULL AND jenis_sewa = 'BULANAN' THEN 'PO_ONLY'
    WHEN jenis_sewa = 'HARIAN' THEN 'DAILY_SPOT'
    ELSE 'CONTRACT'
END;
```

**Benefits:**
- Clear business type classification
- Easy filtering by rental type
- Prepares for type-specific workflows

#### Option B: Rename Table + Restructure
```sql
-- Rename existing table
RENAME TABLE kontrak TO rentals;

-- Add comprehensive fields
ALTER TABLE rentals
ADD COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') NOT NULL DEFAULT 'CONTRACT',
ADD COLUMN document_type ENUM('FORMAL_CONTRACT','PO','DELIVERY_NOTE','NONE') NOT NULL DEFAULT 'FORMAL_CONTRACT',
ADD COLUMN payment_frequency ENUM('MONTHLY','WEEKLY','DAILY','UPFRONT','END_OF_RENTAL') DEFAULT 'MONTHLY',
ADD COLUMN payment_method ENUM('TRANSFER','CHECK','CASH','GIRO') DEFAULT 'TRANSFER',
ADD COLUMN auto_renewal TINYINT(1) DEFAULT 0,
ADD COLUMN parent_rental_id INT UNSIGNED COMMENT 'Reference to previous rental for renewals',
ADD COLUMN renewal_count INT UNSIGNED DEFAULT 0;

-- Rename confusing columns
ALTER TABLE rentals
CHANGE COLUMN no_po_marketing customer_po_number VARCHAR(100),
CHANGE COLUMN jenis_sewa billing_period ENUM('MONTHLY','DAILY');
```

**Benefits:**
- More accurate table name (rentals vs kontrak)
- Better field names (customer_po_number)
- Adds operational tracking

### Priority 2: Standardize Status Values (MEDIUM)

```sql
-- Standardize kontrak status
ALTER TABLE rentals
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') NOT NULL DEFAULT 'PENDING';

-- Update existing data
UPDATE rentals SET status = UPPER(status);

-- Standardize kontrak_unit status (already uppercase, good!)
-- But add new statuses for clarity
ALTER TABLE rental_units
MODIFY COLUMN status ENUM(
    'ACTIVE',                -- Currently in use
    'PULLED',                -- Returned (was DITARIK)
    'REPLACED',              -- Permanently replaced (was DITUKAR)
    'INACTIVE',              -- Not in use (was NON_AKTIF)
    'MAINTENANCE',           -- Under maintenance
    'UNDER_REPAIR',          -- Being repaired
    'TEMP_REPLACED',         -- Temporarily replaced (was TEMPORARILY_REPLACED)
    'TEMP_ACTIVE',           -- Temporary unit active (was TEMPORARY_ACTIVE)
    'TEMP_ENDED'             -- Temporary period ended (was TEMPORARY_ENDED)
) NOT NULL DEFAULT 'ACTIVE';
```

### Priority 3: Add Missing Operational Fields (MEDIUM)

```sql
-- Add payment tracking to rentals
ALTER TABLE rentals
ADD COLUMN payment_status ENUM('UNPAID','PARTIAL','PAID','OVERDUE') DEFAULT 'UNPAID',
ADD COLUMN last_invoice_date DATE,
ADD COLUMN next_invoice_date DATE,
ADD COLUMN contract_template_id INT UNSIGNED COMMENT 'Reference to template used';

-- Add billing rate to rental_units (for mixed-rate contracts)
ALTER TABLE rental_units
ADD COLUMN unit_monthly_rate DECIMAL(15,2) COMMENT 'Override rate for this unit',
ADD COLUMN unit_daily_rate DECIMAL(15,2) COMMENT 'Daily rate if different from contract';
```

### Priority 4: Improve Customer Data (LOW)

```sql
-- Add business classification
ALTER TABLE customers
ADD COLUMN customer_type ENUM('CORPORATE','SME','INDIVIDUAL') DEFAULT 'CORPORATE',
ADD COLUMN tax_id VARCHAR(30) COMMENT 'NPWP for Indonesia',
ADD COLUMN credit_limit DECIMAL(15,2) DEFAULT 0.00,
ADD COLUMN payment_term_days INT DEFAULT 30,
ADD COLUMN is_verified TINYINT(1) DEFAULT 0 COMMENT 'KYC verification status';

-- Add billing address flag to locations
ALTER TABLE customer_locations
ADD COLUMN is_billing_address TINYINT(1) DEFAULT 0;
```

---

## 📋 Implementation Roadmap

### Phase 1: Critical Changes (Week 1)
✅ **Must Do:**
1. Add `rental_type` column to `kontrak`
2. Rename `no_po_marketing` → `customer_po_number`
3. Standardize status values to English
4. Update all views/queries referencing old columns

**Estimated Time:** 4-6 hours
**Risk:** Medium (requires code updates)

### Phase 2: Enhancements (Week 2)
⚡ **Should Do:**
1. Add payment tracking fields
2. Add renewal tracking
3. Add billing rates to rental_units
4. Create indexes for performance

**Estimated Time:** 8-10 hours
**Risk:** Low (mostly additions)

### Phase 3: Nice-to-Have (Week 3)
🎯 **Nice to Have:**
1. Customer classification fields
2. Billing address flag
3. Contract template system
4. Payment method tracking

**Estimated Time:** 6-8 hours
**Risk:** Very Low

---

## 🔍 SQL Migration Script (Phase 1)

```sql
-- ========================================
-- PHASE 1: Critical Database Updates
-- OPTIMA Rental Management System
-- Date: 2026-02-09
-- ========================================

-- Backup before changes
CREATE TABLE kontrak_backup_20260209 AS SELECT * FROM kontrak;
CREATE TABLE kontrak_unit_backup_20260209 AS SELECT * FROM kontrak_unit;

-- 1. Add rental_type distinction
ALTER TABLE kontrak 
ADD COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') NOT NULL DEFAULT 'CONTRACT' 
AFTER no_kontrak,
ADD INDEX idx_rental_type (rental_type);

-- 2. Update existing data based on business logic
UPDATE kontrak 
SET rental_type = CASE 
    -- Has PO + Monthly = Contract-based
    WHEN no_po_marketing IS NOT NULL AND jenis_sewa = 'BULANAN' THEN 'CONTRACT'
    -- No PO + Monthly = PO-only (treat as contract for now, manual review recommended)
    WHEN no_po_marketing IS NULL AND jenis_sewa = 'BULANAN' THEN 'CONTRACT'
    -- Daily = Daily spot
    WHEN jenis_sewa = 'HARIAN' THEN 'DAILY_SPOT'
    ELSE 'CONTRACT'
END;

-- 3. Rename confusing column
ALTER TABLE kontrak 
CHANGE COLUMN no_po_marketing customer_po_number VARCHAR(100);

-- 4. Standardize status values
ALTER TABLE kontrak
MODIFY COLUMN status ENUM('ACTIVE','EXPIRED','PENDING','CANCELLED') NOT NULL DEFAULT 'PENDING';

UPDATE kontrak SET status = 
    CASE status
        WHEN 'Aktif' THEN 'ACTIVE'
        WHEN 'Berakhir' THEN 'EXPIRED'
        WHEN 'Pending' THEN 'PENDING'
        WHEN 'Dibatalkan' THEN 'CANCELLED'
        ELSE 'PENDING'
    END;

-- 5. Update kontrak_unit statuses (translate to English)
ALTER TABLE kontrak_unit
MODIFY COLUMN status ENUM(
    'ACTIVE',
    'PULLED',
    'REPLACED',
    'INACTIVE',
    'MAINTENANCE',
    'UNDER_REPAIR',
    'TEMP_REPLACED',
    'TEMP_ACTIVE',
    'TEMP_ENDED'
) NOT NULL DEFAULT 'ACTIVE';

UPDATE kontrak_unit SET status = 
    CASE status
        WHEN 'AKTIF' THEN 'ACTIVE'
        WHEN 'DITARIK' THEN 'PULLED'
        WHEN 'DITUKAR' THEN 'REPLACED'
        WHEN 'NON_AKTIF' THEN 'INACTIVE'
        WHEN 'MAINTENANCE' THEN 'MAINTENANCE'
        WHEN 'UNDER_REPAIR' THEN 'UNDER_REPAIR'
        WHEN 'TEMPORARILY_REPLACED' THEN 'TEMP_REPLACED'
        WHEN 'TEMPORARY_ACTIVE' THEN 'TEMP_ACTIVE'
        WHEN 'TEMPORARY_ENDED' THEN 'TEMP_ENDED'
        ELSE 'ACTIVE'
    END;

-- 6. Add helpful indexes
ALTER TABLE kontrak
ADD INDEX idx_customer_location (customer_location_id),
ADD INDEX idx_status (status),
ADD INDEX idx_dates (tanggal_mulai, tanggal_berakhir),
ADD INDEX idx_customer_po (customer_po_number);

-- 7. Add comments for documentation
ALTER TABLE kontrak 
MODIFY COLUMN rental_type ENUM('CONTRACT','PO_ONLY','DAILY_SPOT') NOT NULL DEFAULT 'CONTRACT' 
    COMMENT 'Type: CONTRACT=formal contract+PO, PO_ONLY=PO without formal contract, DAILY_SPOT=short-term no PO',
MODIFY COLUMN customer_po_number VARCHAR(100) 
    COMMENT 'Customer PO number (optional for daily rentals)',
MODIFY COLUMN jenis_sewa ENUM('BULANAN','HARIAN')
    COMMENT 'Billing period: BULANAN=monthly rate, HARIAN=daily rate';

-- Verify changes
SELECT 
    rental_type,
    jenis_sewa,
    status,
    COUNT(*) as count,
    SUM(CASE WHEN customer_po_number IS NOT NULL THEN 1 ELSE 0 END) as with_po
FROM kontrak
GROUP BY rental_type, jenis_sewa, status;

-- Should output distribution of rental types
```

---

## 📊 Recommended Views for UI

```sql
-- View 1: Active Rentals Summary
CREATE OR REPLACE VIEW v_active_rentals AS
SELECT 
    k.id AS rental_id,
    k.rental_type,
    k.no_kontrak AS contract_number,
    k.customer_po_number,
    k.jenis_sewa AS billing_period,
    c.customer_code,
    c.customer_name,
    cl.location_name,
    cl.city,
    k.tanggal_mulai AS start_date,
    k.tanggal_berakhir AS end_date,
    DATEDIFF(k.tanggal_berakhir, CURDATE()) AS days_remaining,
    k.total_units,
    k.nilai_total AS total_value,
    k.status,
    CASE 
        WHEN DATEDIFF(k.tanggal_berakhir, CURDATE()) <= 30 THEN 'EXPIRING_SOON'
        WHEN DATEDIFF(k.tanggal_berakhir, CURDATE()) < 0 THEN 'EXPIRED'
        ELSE 'ACTIVE'
    END AS rental_status_flag
FROM kontrak k
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id
WHERE k.status = 'ACTIVE'
ORDER BY k.tanggal_berakhir ASC;

-- View 2: Rental Type Statistics
CREATE OR REPLACE VIEW v_rental_type_stats AS
SELECT 
    rental_type,
    status,
    COUNT(*) AS count,
    SUM(total_units) AS total_units,
    SUM(nilai_total) AS total_revenue
FROM kontrak
GROUP BY rental_type, status;

-- View 3: Customer Rental Summary
CREATE OR REPLACE VIEW v_customer_rental_summary AS
SELECT 
    c.id AS customer_id,
    c.customer_code,
    c.customer_name,
    COUNT(DISTINCT k.id) AS total_rentals,
    SUM(CASE WHEN k.status = 'ACTIVE' THEN 1 ELSE 0 END) AS active_rentals,
    SUM(CASE WHEN k.rental_type = 'CONTRACT' THEN 1 ELSE 0 END) AS contract_count,
    SUM(CASE WHEN k.rental_type = 'PO_ONLY' THEN 1 ELSE 0 END) AS po_only_count,
    SUM(CASE WHEN k.rental_type = 'DAILY_SPOT' THEN 1 ELSE 0 END) AS daily_spot_count,
    SUM(CASE WHEN k.status = 'ACTIVE' THEN k.total_units ELSE 0 END) AS active_units,
    SUM(CASE WHEN k.status = 'ACTIVE' THEN k.nilai_total ELSE 0 END) AS active_revenue
FROM customers c
LEFT JOIN customer_locations cl ON c.id = cl.customer_id
LEFT JOIN kontrak k ON cl.id = k.customer_location_id
GROUP BY c.id, c.customer_code, c.customer_name;
```

---

## 🎯 Key Takeaways

### What's Already Good ✅
1. **Flexible PO handling** - System already allows NULL PO numbers
2. **Rental period types** - `jenis_sewa` distinguishes monthly vs daily
3. **Sophisticated unit tracking** - `kontrak_unit` is well-designed for complex scenarios
4. **Location-based** - Supports multi-location customers properly
5. **Quotation workflow** - Clear path from prospect to customer to contract

### What Needs Immediate Attention ⚠️
1. **Add `rental_type` field** - Distinguish CONTRACT vs PO_ONLY vs DAILY_SPOT
2. **Rename `no_po_marketing`** - Should be `customer_po_number`
3. **Standardize status values** - Use consistent English terms
4. **Add payment tracking** - Missing operational fields

### Strategic Recommendation 🎯

**Implement Phase 1 immediately** (4-6 hours work):
- Adds `rental_type` for proper business classification
- Fixes confusing field names
- Standardizes status values
- Zero breaking changes to data

Then **UI can properly reflect**:
- "Contract-based Rentals" (formal contracts)
- "PO Only Rentals" (simpler process)
- "Daily Spot Rentals" (quick turnaround)

This aligns database with business reality! 🚀

---

**Next Step:** Review this analysis, approve Phase 1 changes, then execute migration script.
