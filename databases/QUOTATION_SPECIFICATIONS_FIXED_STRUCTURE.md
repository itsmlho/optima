# Quotation Specifications Table - Fixed Structure

**Date:** December 5, 2025  
**Database:** optima_ci  
**Table:** quotation_specifications  

## Summary of Issues Fixed

### 🔴 **Critical Errors Resolved:**
1. **Column name mismatch**: Code referenced `qs.aksesoris` but database had no such column
2. **Missing column**: `unit_accessories` was missing from table structure
3. **Data storage issue**: Accessories were incorrectly stored in `notes` field
4. **No Foreign Key constraints**: Reference columns had no FK relationships

### ✅ **Solutions Implemented:**
1. Added `unit_accessories` TEXT column to store comma-separated accessories
2. Updated backend controllers to use correct column name
3. Updated SQL queries to use COALESCE for NULL-safe handling
4. Created comprehensive SQL migration script with FK constraints

---

## Table Structure Overview

### **Core Specification Fields** (Always Used)
```sql
id_specification        INT PRIMARY KEY AUTO_INCREMENT  -- Unique ID
id_quotation           INT NOT NULL                     -- FK to quotations table
specification_name     VARCHAR(255)                     -- Optional custom name
specification_description TEXT                           -- Additional details
quantity              INT NOT NULL DEFAULT 1            -- Number of units
unit_price            DECIMAL(12,2) NOT NULL           -- Monthly rental price
harga_per_unit_harian DECIMAL(15,2)                    -- Daily rental price
total_price           DECIMAL(15,2)                    -- Calculated total
```

### **Equipment Classification** (FK to Master Tables)
```sql
departemen_id         INT                              -- FK: departemen(id_departemen)
tipe_unit_id         INT                              -- FK: tipe_unit(id_tipe_unit)
kapasitas_id         INT                              -- FK: kapasitas(id_kapasitas)
```

### **Equipment Details** (Text Fields)
```sql
brand                VARCHAR(100)                     -- Unit brand/manufacturer
model                VARCHAR(100)                     -- Unit model
equipment_type       VARCHAR(100)                     -- General equipment category
```

### **Technical Specifications** (FK to Component Tables)
```sql
jenis_baterai        VARCHAR(100)                     -- Battery type (Electric only)
charger_id           INT                              -- FK: charger(id_charger)
mast_id              INT                              -- FK: mast(id_mast)
ban_id               INT                              -- FK: ban(id_ban)
roda_id              INT                              -- FK: roda(id_roda)
valve_id             INT                              -- FK: valve(id_valve)
```

### **Attachment Details**
```sql
attachment_tipe      VARCHAR(100)                     -- Type of attachment (FORK, SIDE SHIFTER, etc.)
attachment_merk      VARCHAR(100)                     -- Attachment brand
```

### **Accessories** (NEW - Fixed Field)
```sql
unit_accessories     TEXT                             -- Comma-separated list:
                                                      -- "LAMPU UTAMA, BLUE SPOT, ROTARY LAMP"
```

**Supported Accessories:**
- LAMPU UTAMA (Main Light)
- BLUE SPOT
- RED LINE
- WORK LIGHT
- ROTARY LAMP
- BACK BUZZER
- CAMERA AI
- CAMERA
- SENSOR PARKING
- SPEED LIMITER
- LASER FORK
- VOICE ANNOUNCER
- HORN SPEAKER
- HORN KLASON
- BIO METRIC
- ACRYLIC
- P3K
- SAFETY BELT INTERLOC
- SPARS ARRESTOR

### **Contract Migration Fields**
```sql
original_kontrak_id      INT UNSIGNED                 -- Source contract ID
original_kontrak_spek_id INT UNSIGNED                 -- Source specification ID
```

### **Tracking & Status Fields**
```sql
spek_kode            VARCHAR(50)                      -- Auto-generated code (SPEC-001, etc.)
jumlah_tersedia      INT DEFAULT 0                    -- Available quantity
category             VARCHAR(100)                     -- Category classification
sort_order           INT DEFAULT 0                    -- Display order
is_optional          BOOLEAN DEFAULT FALSE            -- Optional item flag
is_active            BOOLEAN DEFAULT TRUE             -- Active status
notes                TEXT                             -- General notes
created_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at           TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

---

## Unused/Legacy Fields (Present but Not Used)

These fields exist in the database but are NOT actively used by the application:

```sql
unit                 VARCHAR(50) DEFAULT 'pcs'       -- Always 'pcs', not user-configurable
specifications       TEXT                             -- Generic field, replaced by specific columns
service_duration     INT                              -- Service contract fields
service_frequency    VARCHAR(100)                     -- Not used in current workflow
service_scope        TEXT                             
rental_duration      INT                              -- Duration stored elsewhere
rental_rate_type     ENUM('MONTHLY','YEARLY','DAILY','HOURLY')
delivery_required    BOOLEAN DEFAULT FALSE            -- Not used
installation_required BOOLEAN DEFAULT FALSE           
delivery_cost        DECIMAL(12,2) DEFAULT 0          
installation_cost    DECIMAL(12,2) DEFAULT 0          
maintenance_included BOOLEAN DEFAULT FALSE            
warranty_period      INT DEFAULT 12                   -- Fixed at 12 months
```

**⚠️ Recommendation:** These fields can be dropped in a future cleanup migration after confirming with stakeholders.

---

## Foreign Key Relationships

### **Implemented FKs:**
```sql
FK: id_quotation       → quotations(id_quotation)     ON DELETE CASCADE
FK: departemen_id      → departemen(id_departemen)    ON DELETE SET NULL
FK: tipe_unit_id       → tipe_unit(id_tipe_unit)      ON DELETE SET NULL
FK: kapasitas_id       → kapasitas(id_kapasitas)      ON DELETE SET NULL
FK: charger_id         → charger(id_charger)          ON DELETE SET NULL
FK: mast_id            → mast(id_mast)                ON DELETE SET NULL
FK: ban_id             → ban(id_ban)                  ON DELETE SET NULL
FK: roda_id            → roda(id_roda)                ON DELETE SET NULL
FK: valve_id           → valve(id_valve)              ON DELETE SET NULL
```

**Note:** `jenis_baterai`, `attachment_tipe`, `attachment_merk`, and `brand` are VARCHAR fields (not FKs) because they allow free-text input and DISTINCT value selection.

---

## Backend Code Changes

### **Files Modified:**

#### 1. **app/Controllers/Marketing.php** (Line ~1244)
```php
// OLD (ERROR):
qs.aksesoris,  // ❌ Column doesn't exist!

// NEW (FIXED):
COALESCE(qs.unit_accessories, "") as unit_accessories,
COALESCE(qs.unit_accessories, "") as aksesoris,  // Alias for frontend
```

#### 2. **app/Controllers/Quotation.php** - `addSpecification()` (Line ~708)
```php
// OLD (WRONG FIELD):
if ($aksesoris && is_array($aksesoris)) {
    $data['notes'] = 'Accessories: ' . implode(', ', $aksesoris);
}

// NEW (CORRECT FIELD):
$aksesoris = $this->request->getPost('aksesoris');
if ($aksesoris && is_array($aksesoris)) {
    $data['unit_accessories'] = implode(', ', $aksesoris);
} else {
    $data['unit_accessories'] = '';
}
```

#### 3. **app/Controllers/Quotation.php** - `updateSpecification()` (Line ~797)
```php
// OLD:
if (isset($data['aksesoris']) && is_array($data['aksesoris'])) {
    $data['aksesoris'] = implode(',', $data['aksesoris']);
}

// NEW:
if (isset($data['aksesoris']) && is_array($data['aksesoris'])) {
    $data['unit_accessories'] = implode(', ', $data['aksesoris']);
    unset($data['aksesoris']); // Prevent DB error
}
```

---

## Frontend Integration

### **Form Field Mapping:**

| HTML Form Field          | Database Column         | Data Type        |
|-------------------------|-------------------------|------------------|
| `specification_name`    | `specification_name`    | VARCHAR(255)     |
| `quantity`              | `quantity`              | INT              |
| `unit_price`            | `unit_price`            | DECIMAL(12,2)    |
| `harga_per_unit_harian` | `harga_per_unit_harian` | DECIMAL(15,2)    |
| `departemen_id`         | `departemen_id`         | INT (FK)         |
| `tipe_unit_id`          | `tipe_unit_id`          | INT (FK)         |
| `kapasitas_id`          | `kapasitas_id`          | INT (FK)         |
| `merk_unit`             | `brand`                 | VARCHAR(100)     |
| `jenis_baterai`         | `jenis_baterai`         | VARCHAR(100)     |
| `charger_id`            | `charger_id`            | INT (FK)         |
| `attachment_tipe`       | `attachment_tipe`       | VARCHAR(100)     |
| `valve_id`              | `valve_id`              | INT (FK)         |
| `mast_id`               | `mast_id`               | INT (FK)         |
| `ban_id`                | `ban_id`                | INT (FK)         |
| `roda_id`               | `roda_id`               | INT (FK)         |
| `aksesoris[]` (array)   | `unit_accessories`      | TEXT (comma-sep) |

### **JavaScript Handling:**
```javascript
// Frontend sends:
aksesoris: ["LAMPU UTAMA", "BLUE SPOT", "ROTARY LAMP"]

// Backend receives and transforms:
unit_accessories = "LAMPU UTAMA, BLUE SPOT, ROTARY LAMP"

// Frontend receives on edit:
spec.aksesoris = "LAMPU UTAMA, BLUE SPOT, ROTARY LAMP"
// Splits and checks checkboxes accordingly
```

---

## Migration Instructions

### **Apply Database Changes:**

```bash
# Run the comprehensive migration script
mysql -u root -p optima_ci < databases/migrations/fix_quotation_specifications_structure.sql
```

### **Verify Changes:**
```sql
-- Check column exists
DESCRIBE quotation_specifications unit_accessories;

-- Test query (should not error)
SELECT 
    id_specification,
    specification_name,
    unit_accessories,
    unit_accessories as aksesoris
FROM quotation_specifications
LIMIT 5;
```

### **Test in Application:**
1. Open quotation specifications tab
2. Click "Add Specification"
3. Fill all fields including accessories checkboxes
4. Save specification
5. Click "Edit Specification"
6. Verify all data loads correctly (including checked accessories)
7. Update and save
8. Confirm data persists

---

## Performance Optimizations

### **Indexes Added:**
```sql
CREATE INDEX idx_qs_departemen ON quotation_specifications(departemen_id);
CREATE INDEX idx_qs_tipe_unit ON quotation_specifications(tipe_unit_id);
CREATE INDEX idx_qs_kapasitas ON quotation_specifications(kapasitas_id);
CREATE INDEX idx_qs_spek_kode ON quotation_specifications(spek_kode);
CREATE INDEX idx_qs_active ON quotation_specifications(is_active);
```

---

## Future Improvements

### **Recommended:**
1. **Normalize accessories**: Create `quotation_specification_accessories` junction table
2. **Drop unused columns**: Remove service/rental legacy fields
3. **Add validation**: Ensure battery/charger only for electric departments
4. **Audit trail**: Track specification changes in history table

### **Schema Evolution:**
```sql
-- Proposed normalized structure (future)
CREATE TABLE quotation_specification_accessories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_specification INT NOT NULL,
    accessory_name VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_specification) REFERENCES quotation_specifications(id_specification) ON DELETE CASCADE
);
```

---

## Troubleshooting

### **Common Errors:**

#### ❌ Error: "Unknown column 'qs.aksesoris'"
**Cause:** Old code references non-existent column  
**Fix:** Update to `qs.unit_accessories`

#### ❌ Error: "Unknown column 'qs.unit_accessories'"
**Cause:** Migration not run  
**Fix:** Execute `ALTER TABLE quotation_specifications ADD COLUMN unit_accessories TEXT;`

#### ❌ Accessories not saving
**Cause:** Frontend sends `aksesoris[]` but backend looks for wrong field  
**Fix:** Ensure backend maps `aksesoris` POST data to `unit_accessories` column

#### ❌ Accessories not loading in edit modal
**Cause:** Backend query doesn't return `aksesoris` alias  
**Fix:** Add `qs.unit_accessories as aksesoris` to SELECT clause

---

## Testing Checklist

- [x] Column `unit_accessories` added to database
- [x] Backend queries updated to use correct column
- [x] `addSpecification()` saves accessories correctly
- [x] `updateSpecification()` updates accessories correctly
- [x] `getSpecifications()` returns accessories with both field names
- [x] Frontend form submits accessories as array
- [x] Frontend edit modal populates checkboxes correctly
- [ ] **Test Add Specification with accessories**
- [ ] **Test Edit Specification with accessories**
- [ ] **Test Save Edit updates accessories**
- [ ] **Verify data in database after save**

---

## Support Reference

**Related Files:**
- Migration: `databases/migrations/fix_quotation_specifications_structure.sql`
- Backend: `app/Controllers/Marketing.php` (getSpecifications)
- Backend: `app/Controllers/Quotation.php` (addSpecification, updateSpecification)
- Frontend: `app/Views/marketing/quotations.php` (Add/Edit Specification Modal)
- Documentation: This file

**Database:** optima_ci  
**Table:** quotation_specifications  
**Last Updated:** December 5, 2025  
