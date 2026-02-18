# DATABASE SCHEMA FIX: Contract & PO Relationships

**Date**: February 17, 2026  
**Migration**: `2026-02-17_fix_contract_location_relationships.sql`

## Problem Analysis

### Real-World Data Shows:
- **1 Contract/PO can serve MULTIPLE locations** with **DIFFERENT prices**
- **Example**: Contract `4212029724` (PT Otsuka):
  ```
  CICURUG      → Rp11.000.000 - Rp12.050.000 (varies by unit)
  SENTUL 1     → Rp10.800.000
  SENTUL 2     → Rp11.850.000
  LDC PASAR REBO → Rp22.030.000
  LDC BEKASI   → Rp22.030.000
  ```

- **4 Document Scenarios**:
  1. ✅ **KONTRAK only** (formal contract): `No. 02/PJB/AGN-SML/VI/2025`
  2. ✅ **PO only** (purchase order): `4212029724`, `IDN10029192`
  3. ✅ **BOTH** (contract + PO issued for each order)
  4. ✅ **RECURRING PO** (monthly PO): `PO PERBULAN`, `PO Perbulan`
  5. ✅ **STATUS_PENDING**: `dalam proses`, `PO On Progres`
  6. ✅ **NONE** (no formal document, direct billing)

### Old Database Schema Issues:

```sql
-- ❌ PROBLEM 1: kontrak.customer_location_id assumes 1 contract = 1 location
CREATE TABLE kontrak (
    customer_location_id INT NULL,  -- Only 1 location!
    no_kontrak VARCHAR(100),
    nilai_total DECIMAL(15,2),      -- Total, not per-location
    ...
)

-- ❌ PROBLEM 2: customer_contracts missing location & price info
CREATE TABLE customer_contracts (
    customer_id INT,
    kontrak_id INT NOT NULL,  -- Not nullable, can't handle "no contract" customers
    is_active TINYINT(1)
    -- Missing: location_id, price_per_location, contract_type
)
```

## Solution: New Database Schema

### 1. **kontrak.document_type** (NEW FIELD)
```sql
document_type ENUM('KONTRAK', 'PO', 'AGREEMENT', 'RECURRING_PO', 'STATUS_PENDING')
```
- **KONTRAK**: Formal contract with duration
- **PO**: Purchase Order (per order)
- **AGREEMENT**: Basic agreement without specific doc number
- **RECURRING_PO**: Monthly recurring PO (`PO PERBULAN`)
- **STATUS_PENDING**: Document in progress (`dalam proses`, `PO On Progres`)

### 2. **kontrak_locations** (NEW TABLE)
```sql
CREATE TABLE kontrak_locations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kontrak_id INT UNSIGNED NOT NULL,
    customer_location_id INT NOT NULL,
    harga_per_lokasi DECIMAL(15,2),      -- Price specific to this location
    jumlah_unit INT DEFAULT 0,            -- Number of units at this location
    catatan TEXT,                         -- Location-specific notes
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (kontrak_id) REFERENCES kontrak(id),
    FOREIGN KEY (customer_location_id) REFERENCES customer_locations(id),
    UNIQUE KEY uk_kontrak_location (kontrak_id, customer_location_id)
)
```

**Enables**:
- ✅ 1 contract → many locations (many-to-many)
- ✅ Different prices per location
- ✅ Track units per location

### 3. **customer_contracts.contract_type** (NEW FIELD)
```sql
contract_type ENUM('KONTRAK_ONLY', 'PO_ONLY', 'BOTH', 'RECURRING_PO', 'NONE')
```
- **KONTRAK_ONLY**: Customer uses contracts only
- **PO_ONLY**: Customer uses PO per order only
- **BOTH**: Customer has contract + issues PO per delivery
- **RECURRING_PO**: Monthly recurring PO (no fixed contract)  
- **NONE**: No formal document (direct billing)

### 4. **customer_contracts.kontrak_id** (MODIFIED)
```sql
kontrak_id INT UNSIGNED NULL  -- Now NULLABLE for customers without contracts
```

## New Relationships

```
customers (1) ←→ (M) customer_contracts ←→ (M) kontrak
                                                ↓
                                         (M) kontrak_locations (M)
                                                ↓
                                         customer_locations
```

### Flow:
1. **Customer** has a contract policy (`contract_type`)
2. **customer_contracts** links customer to kontrak (if any)
3. **kontrak** is master document (contract/PO)
4. **kontrak_locations** specifies which locations use this contract & at what price
5. **customer_locations** are the physical locations

## Migration Steps

1. ✅ Add `kontrak.document_type`
2. ✅ Update existing contracts based on `no_kontrak` pattern
3. ✅ Create `kontrak_locations` table
4. ✅ Migrate existing `kontrak.customer_location_id` → `kontrak_locations`
5. ✅ Add `customer_contracts.contract_type`
6. ✅ Make `customer_contracts.kontrak_id` NULLABLE
7. ✅ Update constraints

## SQL Generation Changes

**Old approach** (WRONG):
```php
// Generated: 1 entry per customer-contract
INSERT INTO customer_contracts (customer_id, kontrak_id)
SELECT 4, id FROM kontrak WHERE no_kontrak = '4212029724';
```

**New approach** (CORRECT):
```php
// Step 1: Insert kontrak master
INSERT INTO kontrak (no_kontrak, document_type, tanggal_mulai, tanggal_berakhir, ...)
VALUES ('4212029724', 'PO', '2025-01-01', '2025-12-31', ...);
SET @kontrak_id = LAST_INSERT_ID();

// Step 2: Link kontrak to locations with prices
INSERT INTO kontrak_locations (kontrak_id, customer_location_id, harga_per_lokasi)
VALUES 
    (@kontrak_id, 5, 12050000.00),  -- CICURUG location
    (@kontrak_id, 6, 10800000.00),  -- SENTUL 1 location
    (@kontrak_id, 7, 11850000.00),  -- SENTUL 2 location
    (@kontrak_id, 8, 22030000.00),  -- LDC PASAR REBO location
    (@kontrak_id, 9, 22030000.00);  -- LDC BEKASI location

// Step 3: Link customer to kontrak
INSERT INTO customer_contracts (customer_id, kontrak_id, contract_type)
VALUES (4, @kontrak_id, 'PO_ONLY');
```

## Model Updates Needed

### KontrakModel.php
```php
protected $allowedFields = [
    'customer_location_id',  // Keep for backward compatibility
    'document_type',         // NEW
    'no_kontrak',
    'no_po_marketing',
    // ...
];

// New methods
public function getWithLocations($kontrakId) {
    // Get kontrak with all its locations & prices
}
```

### KontrakLocationModel.php (NEW)
```php
class KontrakLocationModel extends Model {
    protected $table = 'kontrak_locations';
    protected $allowedFields = [
        'kontrak_id',
        'customer_location_id',
        'harga_per_lokasi',
        'jumlah_unit',
        'catatan',
        'is_active'
    ];
}
```

### CustomerContractModel.php
```php
protected $allowedFields = [
    'customer_id',
    'kontrak_id',    // Now nullable
    'contract_type', // NEW
    'is_active'
];
```

## Example Queries

### Get all locations using a contract with prices:
```sql
SELECT 
    k.no_kontrak,
    k.document_type,
    cl.location_name,
    cl.city,
    kl.harga_per_lokasi,
    kl.jumlah_unit
FROM kontrak k
JOIN kontrak_locations kl ON k.id = kl.kontrak_id
JOIN customer_locations cl ON kl.customer_location_id = cl.id
WHERE k.id = 123;
```

### Get all contracts for a customer location:
```sql
SELECT 
    k.no_kontrak,
    k.document_type,
    k.tanggal_mulai,
    k.tanggal_berakhir,
    kl.harga_per_lokasi
FROM kontrak k
JOIN kontrak_locations kl ON k.id = kl.kontrak_id
WHERE kl.customer_location_id = 5
AND kl.is_active = 1;
```

### Get customers without contracts:
```sql
SELECT c.customer_name
FROM customers c
LEFT JOIN customer_contracts cc ON c.id = cc.customer_id
WHERE cc.id IS NULL
OR cc.contract_type = 'NONE';
```

## Testing Checklist

- [ ] Run migration SQL
- [ ] Verify existing contracts migrated to kontrak_locations
- [ ] Test inserting new contract with multiple locations
- [ ] Test customer without CONTRACT (NULL kontrak_id)
- [ ] Test RECURRING_PO type
- [ ] Update Models with new fields
- [ ] Update Controllers to handle kontrak_locations
- [ ] Update Views to show contract-location relationships
- [ ] Generate customer_contracts.sql with new structure
- [ ] Import and verify data integrity

## Benefits

✅ **Flexible Document System**: Support all customer scenarios (KONTRAK/PO/BOTH/RECURRING/NONE)  
✅ **Multi-Location Contracts**: 1 contract → many locations with different prices  
✅ **Accurate Pricing**: Track exact price per location, not just total  
✅ **Better Reporting**: Query by location, price, document type  
✅ **Data Integrity**: Proper foreign keys and constraints  
✅ **Scalability**: Easy to add new document types or fields  

---

**Next Steps**:
1. Execute migration SQL
2. Update PHP Models
3. Regenerate customer_contracts SQL with proper structure
4. Test with real data
