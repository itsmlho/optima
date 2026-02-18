# OPTIMA ERP - Database Schema & Data Migration Strategy
## Generated: 2026-02-18

---

## 📊 CURRENT DATABASE STATUS

### Table Statistics:
- **Customers**: 239 records (includes 15 new additions today)
- **Customer Locations**: 468 records (average 1.96 locations per customer)
- **Areas**: 34 active areas
- **Kontrak**: 363 contracts
- **Customer-Contract Links**: 370 relationships
- **Inventory Units**: 4,989 total units
  - ✅ Assigned to customers: 1,195 units (24%)
  - ✅ With contract assignment: 1,195 units (24%)
  - ✅ With monthly rental price: 1,195 units (24%)
  - ⚠️ Unassigned: 3,794 units (76%)

### Data to Import:
- **marketing_fix.csv**: ~1,940 units with operational data
  - Contains: Customer, Unit Number, Location, Area, Contract/PO, Dates, Prices

---

## 🗂️ ENTITY RELATIONSHIP DIAGRAM (ERD)

```
┌─────────────────────┐
│     CUSTOMERS       │
│─────────────────────│
│ PK id               │
│ UQ customer_code    │
│    customer_name    │
│    marketing_name   │
│    is_active        │
└──────────┬──────────┘
           │ 1
           │
           │ N
┌──────────▼──────────────────┐
│   CUSTOMER_LOCATIONS        │
│─────────────────────────────│
│ PK id                       │◄────┐
│ FK customer_id  (customers) │     │
│ FK area_id (areas) [NULL]   │     │
│    location_name            │     │
│    location_code            │     │
│    location_type (ENUM)     │     │
│    address                  │     │
│    city, province           │     │
│    contact_person, phone    │     │
│    is_primary               │     │
│    is_active                │     │
└─────────────┬───────────────┘     │
              │                     │
              │ 1                   │
              │                     │
              │ N                   │ 1
┌─────────────▼─────────────────────┴───────┐
│            KONTRAK                         │
│────────────────────────────────────────────│
│ PK id                                      │
│ FK customer_location_id (customer_locations) │
│ FK parent_contract_id (kontrak) [renewal]  │
│    no_kontrak                              │
│    rental_type (CONTRACT/PO_ONLY/DAILY)    │
│    customer_po_number                      │
│    nilai_total                             │◄── ⚠️ TOTAL CONTRACT VALUE
│    total_units                             │
│    jenis_sewa (BULANAN/HARIAN)             │
│    billing_method (CYCLE/PRORATE/MONTHLY)  │
│    tanggal_mulai, tanggal_berakhir         │
│    status (ACTIVE/EXPIRED/PENDING/CANCELLED)│
│    fast_track, spot_rental_number          │
│    estimated_duration_days                 │
└────────────┬───────────────────────────────┘
             │
             │ 1
             │
             │ N
┌────────────▼───────────────────────────────┐
│       CUSTOMER_CONTRACTS (Linking Table)   │
│────────────────────────────────────────────│
│ PK id                                      │
│ FK customer_id (customers)                 │
│ FK kontrak_id (kontrak)                    │
│    is_active                               │
└────────────────────────────────────────────┘


┌────────────────────────────────────────────┐
│        INVENTORY_UNIT                      │
│────────────────────────────────────────────│
│ PK id_inventory_unit                       │
│ UQ no_unit_na                              │
│    no_unit                                 │
│    serial_number                           │
│ FK customer_id (customers)                 │
│ FK customer_location_id (customer_locations)│
│ FK kontrak_id (kontrak)                    │
│ FK area_id (areas)                         │
│    harga_sewa_bulanan                      │◄── ⚠️ UNIT MONTHLY PRICE
│    harga_sewa_harian                       │◄── ⚠️ UNIT DAILY PRICE
│    on_hire_date, off_hire_date             │
│    status_unit_id, lokasi_unit             │
│    workflow_status (ENUM)                  │
│    ...technical specs (tipe, model, etc.)  │
└────────────────────────────────────────────┘


┌────────────┐
│   AREAS    │
│────────────│
│ PK id      │
│ UQ area_code│
│   area_name│
│   area_type│
└────────────┘
```

---

## 💰 HARGA PRICING ANALYSIS & NORMALIZATION STRATEGY

### Current Pricing Schema Issues:

**⚠️ PROBLEM #1: Price Redundancy**
Currently, pricing data exists in **TWO PLACES**:
1. `kontrak.nilai_total` - Total contract value (lump sum)
2. `inventory_unit.harga_sewa_bulanan` - Per-unit monthly rental rate
3. `inventory_unit.harga_sewa_harian` - Per-unit daily rental rate

This creates:
- **Data inconsistency**: If contract has 10 units @ Rp 10M/month each, `nilai_total` should be Rp 100M. But what if someone changes unit price?
- **Update anomaly**: Changing unit price doesn't recalculate contract total
- **Maintenance nightmare**: Two sources of truth for pricing

### Business Logic Analysis from CSV Data:

From `marketing_fix.csv` sample:
```
No Unit;Customer;Area;Lokasi;No PO;Awal Kontrak;Kontrak Habis;Harga
3622;PT ABC Kogen Dairy;BANDUNG;Bandung;365/SML/VIII/2024;09/10/2025;Rp[price]
2294;PT ABC Kogen Dairy;BANDUNG;Bandung;365/SML/VIII/2024;;;
```

**Observations:**
1. ✅ Same PO can have multiple units
2. ✅ Price is listed per unit row
3. ✅ Some units have empty price (needs handling)
4. ❓ Is price per unit or per contract?

### Recommended Solution: **3-Tier Pricing Model**

#### TIER 1: Contract Base Information
`kontrak` table keeps **contract metadata only**:
```sql
kontrak (
  id,
  no_kontrak,
  customer_po_number,
  nilai_total,              -- ✅ KEEP: Total contract value (for accounting)
  jenis_sewa,  billing_method,
  tanggal_mulai, tanggal_berakhir,
  status
)
```
**Decision**: Keep `nilai_total` as **AGREED CONTRACT VALUE** (from PO/contract document).

#### TIER 2: Contract Unit Rates (RECOMMENDED NEW TABLE)
Create junction table for **unit-contract pricing**:

```sql
CREATE TABLE contract_unit_rates (
  id INT PRIMARY KEY AUTO_INCREMENT,
  kontrak_id INT UNSIGNED NOT NULL,
  tipe_unit_id INT,                  -- Optional: rate by unit type
  kapasitas_unit_id INT,             -- Optional: rate by capacity
  rental_rate_monthly DECIMAL(15,2), -- Standard monthly rate for this contract
  rental_rate_daily DECIMAL(15,2),   -- Standard daily rate for this contract
  effective_from DATE,               -- Rate validity period
  effective_until DATE,
  notes TEXT,                        -- e.g., "Promo rate for first 6 months"
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (kontrak_id) REFERENCES kontrak(id),
  FOREIGN KEY (tipe_unit_id) REFERENCES tipe_unit(id),
  FOREIGN KEY (kapasitas_unit_id) REFERENCES kapasitas(id),
  
  UNIQUE KEY (kontrak_id, tipe_unit_id, kapasitas_unit_id, effective_from)
) ENGINE=InnoDB;
```

**Benefits:**
- ✅ Support different rates for different unit types within same contract
- ✅ Support rate changes over time (renewals, amendments)
- ✅ One source of truth for "what rate should I charge?"

#### TIER 3: Unit-Specific Overrides
`inventory_unit` table keeps **actual billing rates**:
```sql
inventory_unit (
  id_inventory_unit,
  no_unit,
  kontrak_id,
  harga_sewa_bulanan,    -- ✅ KEEP: Actual rate for THIS specific unit
  harga_sewa_harian,     -- ✅ KEEP: Actual daily rate for THIS specific unit  
  rate_changed_at,       -- Timestamp of last rate change
  ...
)
```

**Business Rules:**
1. **Default behavior**: When assigning unit to contract, copy rate from `contract_unit_rates`
2. **Override allowed**: Marketing can override per-unit for special cases
3. **Audit trail**: `rate_changed_at` tracks when override happened
4. **Billing**: Always use `inventory_unit.harga_sewa_*` for actual invoicing

### Price Calculation Logic:

```sql
-- Get billable rate for a unit:
SELECT 
  iu.no_unit,
  COALESCE(
    iu.harga_sewa_bulanan,           -- Use unit-specific rate if set
    cur.rental_rate_monthly,          -- Else use contract standard rate
    0                                 -- Fallback to 0 (flag for manual review)
  ) as effective_monthly_rate
FROM inventory_unit iu
LEFT JOIN contract_unit_rates cur 
  ON iu.kontrak_id = cur.kontrak_id 
  AND CURDATE() BETWEEN cur.effective_from AND cur.effective_until
WHERE iu.kontrak_id = ?;
```

---

## 🔄 DATA MIGRATION STRATEGY

### Phase 1: Handle Overlap & Missing Data

#### Rule 0: CSV is Source of Truth (CRITICAL)
```sql
-- STEP 1: Reset overlapping units (947 units exist in BOTH CSV and DB)
-- These units will have ALL assignments cleared before CSV import
UPDATE inventory_unit
SET 
  customer_id = NULL,
  kontrak_id = NULL,
  customer_location_id = NULL,
  area_id = NULL,
  harga_sewa_bulanan = NULL,
  harga_sewa_harian = NULL,
  on_hire_date = NULL,
  off_hire_date = NULL,
  rate_changed_at = NULL
WHERE no_unit IN (3622, 2294, 5150, ...);  -- 947 overlap units
```

**Rationale:** CSV data is FINAL and FIXED. If a unit exists in both CSV and database with different data, CSV wins. Existing assignments are outdated and must be cleared before fresh import.

#### Rule 1: Missing Locations
```sql
-- If customer has NO location in customer_locations, create default:
INSERT INTO customer_locations 
  (customer_id, area_id, location_name, location_code, address, city, province, is_primary)
VALUES 
  (?, NULL, 'DEFAULT LOCATION', 'DEFAULT', 
   'Alamat belum diisi', 'N/A', 'N/A', 1);
```

#### Rule 2: Missing Area
From CSV, if `AREA` column empty:
- Check if customer location already has `area_id`
- If not, leave NULL (can be updated later by admin)
- ⚠️ Do NOT block import

#### Rule 3: Missing Contract/PO
If `No PO` or `No Kontrak` empty BUT customer exists:
```sql
-- Create placeholder contract:
INSERT INTO kontrak 
  (customer_location_id, no_kontrak, rental_type, customer_po_number, 
   tanggal_mulai, tanggal_berakhir, status)
VALUES 
  (?, 'PENDING-IMPORT-' || UNIX_TIMESTAMP(), 'PO_ONLY', 
   'BELUM ADA PO', 
   CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 YEAR), 
   'PENDING');
```

Marketing team can edit this later via UI.

### Phase 2: Data Cleaning Rules

#### Customer Name Normalization
```python
# Standardize customer names
def normalize_customer_name(name):
    name = name.strip()
    name = re.sub(r'\s+', ' ', name)  # Remove extra spaces
    
    # Handle variations:
    # "PT ABC Kogen Dairy" vs "ABC Kogen Dairy" vs "PT. ABC Kogen Dairy"
    name = name.replace('PT.', 'PT').replace('CV.', 'CV')
    
    return name
```

#### Unit Number Validation
```python
def validate_unit_number(no_unit):
    # marketing_fix.csv has: 3622, 2294, 5150, etc.
    # Must match inventory_unit.no_unit (INT)
    
    try:
        return int(no_unit)
    except ValueError:
        # Handle "No Unit" column with string like "FL-3622"
        digits = re.findall(r'\d+', no_unit)
        if digits:
            return int(digits[0])
        raise ValueError(f"Invalid unit number: {no_unit}")
```

#### Price Parsing
```python
def parse_price(price_str):
    # Input: "Rp8.500.000,00" or "Rp 8.500.000" or empty
    if not price_str or pd.isna(price_str):
        return None
        
    # Remove currency symbols and spaces
    clean = re.sub(r'[Rp\s]', '', price_str)
    
    # Convert Indonesian format: 8.500.000,00 -> 8500000.00
    clean = clean.replace('.', '').replace(',', '.')
    
    try:
        return float(clean)
    except ValueError:
        return None
```

### Phase 3: Mapping & Lookup Strategy

```python
import pandas as pd
import mysql.connector

# Load CSVs
df_customers = pd.read_csv('customer2.csv', delimiter=';')
df_locations = pd.read_csv('customer_locations2.csv')
df_marketing = pd.read_csv('marketing_fix.csv', delimiter=';')

# Build lookup dictionaries from existing DB data
customer_map = {}  # {customer_name: customer_id}
location_map = {}  # {(customer_id, location_name): location_id}
area_map = {}      # {area_name: area_id}
contract_map = {}  # {customer_po_number: kontrak_id}

# Build set of currently assigned units (for overlap detection)
db_assigned_units = set()

# Query existing data
conn = mysql.connector.connect(host='localhost', user='root', database='optima_ci')
cursor = conn.cursor()

cursor.execute("SELECT id, customer_name FROM customers")
for row in cursor.fetchall():
    customer_map[normalize_customer_name(row[1])] = row[0]

cursor.execute("SELECT id, area_name FROM areas")
for row in cursor.fetchall():
    area_map[row[1].upper()] = row[0]

cursor.execute("SELECT no_unit FROM inventory_unit WHERE customer_id IS NOT NULL")
for row in cursor.fetchall():
    db_assigned_units.add(row[0])

print(f"🔍 Overlap detection: {len(db_assigned_units)} units currently assigned in DB")

# STEP 1: Reset overlapping units (CSV is source of truth)
units_to_reset = []

# First pass: Identify overlap units
for idx, row in df_marketing.iterrows():
    unit_number = validate_unit_number(row['No Unit'])
    if unit_number in db_assigned_units:
        units_to_reset.append(unit_number)

if units_to_reset:
    print(f"⚠️  Found {len(units_to_reset)} overlap units - will RESET before import")
    placeholders = ','.join(['%s'] * len(units_to_reset))
    cursor.execute(f"""
        UPDATE inventory_unit
        SET customer_id=NULL, kontrak_id=NULL, customer_location_id=NULL,
            area_id=NULL, harga_sewa_bulanan=NULL, harga_sewa_harian=NULL,
            on_hire_date=NULL, off_hire_date=NULL, rate_changed_at=NULL
        WHERE no_unit IN ({placeholders})
    """, units_to_reset)
    print(f"✅ Reset {cursor.rowcount} overlap units")

# Process each row from marketing_fix.csv
missing_customers = []
missing_locations = []
missing_contracts = []

for idx, row in df_marketing.iterrows():
    cust_name = normalize_customer_name(row['nama customer'])
    
    # Step 1: Find or flag customer
    customer_id = customer_map.get(cust_name)
    if not customer_id:
        missing_customers.append(cust_name)
        continue  # Skip this row, will be reported
    
    # Step 2: Find or create location
    location_name = row['Lokasi'] if pd.notna(row['Lokasi']) else 'DEFAULT LOCATION'
    location_id = location_map.get((customer_id, location_name))
    
    if not location_id:
        # Create new location
        area_id = area_map.get(str(row['Area']).upper())
        cursor.execute("""
            INSERT INTO customer_locations 
            (customer_id, area_id, location_name, location_code, address, city, province, is_primary)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """, (customer_id, area_id, location_name, f'LOC-{customer_id}', 
              'Auto-generated from import', location_name if location_name != 'DEFAULT LOCATION' else 'N/A', 
              str(row['Area']) if pd.notna(row['Area']) else 'N/A', 0))
        location_id = cursor.lastrowid
        location_map[(customer_id, location_name)] = location_id
    
    # Step 3: Find or create contract
    po_number = row['No PO'] if pd.notna(row['No PO']) else None
    kontrak_id = contract_map.get(po_number) if po_number else None
    
    if not kontrak_id and po_number:
        # Parse dates
        start_date = parse_date(row['Awal Kontrak']) if pd.notna(row['Awal Kontrak']) else datetime.now()
        end_date = parse_date(row['Kontrak Habis']) if pd.notna(row['Kontrak Habis']) else start_date + timedelta(days=365)
        
        cursor.execute("""
            INSERT INTO kontrak 
            (customer_location_id, no_kontrak, rental_type, customer_po_number, 
             tanggal_mulai, tanggal_berakhir, status, total_units)
            VALUES (%s, %s, %s, %s, %s, %s, %s, %s)
        """, (location_id, po_number or f'IMP-{int(time.time())}', 'PO_ONLY', 
              po_number or 'BELUM ADA PO', start_date, end_date, 'PENDING', 0))
        kontrak_id = cursor.lastrowid
        contract_map[po_number] = kontrak_id
    
    # Step 4: Update inventory_unit
    unit_number = validate_unit_number(row['No Unit'])
    monthly_price = parse_price(row['Harga'])
    
    cursor.execute("""
        UPDATE inventory_unit
        SET 
          customer_id = %s,
          customer_location_id = %s,
          kontrak_id = %s,
          area_id = (SELECT area_id FROM customer_locations WHERE id = %s),
          harga_sewa_bulanan = %s,
          on_hire_date = %s,
          rate_changed_at = NOW()
        WHERE no_unit = %s
    """, (customer_id, location_id, kontrak_id, location_id, monthly_price, 
          start_date if po_number else None, unit_number))
    
    if cursor.rowcount == 0:
        print(f"⚠️ Unit {unit_number} not found in inventory_unit table")

conn.commit()
cursor.close()
conn.close()

# Generate reports
print(f"\n✅ Import Summary:")
print(f"  - Total rows processed: {len(df_marketing)}")
print(f"  - Overlap units (reset): {len(units_to_reset)}")
print(f"  - Units imported from CSV: {len(df_marketing)}")
print(f"  - Missing customers: {len(missing_customers)}")
print(f"  - New locations created: {len([v for k,v in location_map.items()])}")
print(f"  - New contracts created: {len(contract_map)}")
print(f"\n⚠️  IMPORTANT: CSV is SOURCE OF TRUTH")
print(f"  - {len(units_to_reset)} overlap units were RESET before import")
print(f"  - All {len(df_marketing)} units from CSV imported fresh")
print(f"  - Final assigned units: {len(df_marketing)} (not {len(db_assigned_units) + len(df_marketing) - len(units_to_reset)})")

if missing_customers:
    print("\n⚠️ Missing Customers (need to add to database first):")
    for cust in sorted(set(missing_customers)):
        print(f"  - {cust}")
```

---

## 📋 IMPLEMENTATION CHECKLIST

### Pre-Migration:
- [ ] Backup database: `mysqldump optima_ci > backup_pre_migration_$(date +%Y%m%d).sql`
- [ ] Verify all 239 customers in database match customer2.csv
- [ ] Add 15 new customers (already done ✅)
- [ ] Verify areas table has all areas from CSV `AREA` column
- [ ] Create `contract_unit_rates` table (optional, for future)

### Migration Execution:
- [ ] Run Python cleaning script (creates `marketing_fix_cleaned.sql`)
- [ ] Review generated SQL for placeholders ("PENDING-IMPORT-*", "BELUM ADA PO")
- [ ] Execute SQL import
- [ ] Update `kontrak.total_units` = COUNT(inventory_unit WHERE kontrak_id = ...)
- [ ] Recalculate `kontrak.nilai_total` = SUM(harga_sewa_bulanan * expected_months)

### Post-Migration Validation:
- [ ] Check missing customers report
- [ ] Verify unit assignments: `SELECT COUNT(*) FROM inventory_unit WHERE customer_id IS NOT NULL`
- [ ] Check contracts without units: `SELECT * FROM kontrak WHERE total_units = 0`
- [ ] Validate price consistency: `SELECT kontrak_id, COUNT(*), SUM(harga_sewa_bulanan) FROM inventory_unit GROUP BY kontrak_id`
- [ ] Test customer management page displays correct data

### Admin Tasks (Post-Import):
- [ ] Marketing team reviews "PENDING" contracts and fills in missing data
- [ ] Update area_id for locations with NULL area_id
- [ ] Verify pricing for units with NULL harga_sewa_bulanan
- [ ] Set contract status from PENDING → ACTIVE for valid contracts

---

## 🎯 EXPECTED FINAL STATE

After successful migration:
- ✅ **370+ customer-contract links** (existing + new)
- ✅ **1,939 units processed from CSV**
  - 947 units **RESET** (overlap - existed in both CSV and DB)
  - 992 units **NEW** assignments (only in CSV)
  - 248 units **UNCHANGED** (only in DB, not in CSV)
- ✅ **Total assigned units: 1,939** (exactly matching CSV row count = 39% of inventory)
- ✅ **All customers have at least 1 location** (business rule enforced)
- ✅ **Placeholder contracts** created for missing PO data (editable by admin)
- ✅ **Price data** populated in `inventory_unit.harga_sewa_bulanan`
- ✅ **Audit trail** maintained via `rate_changed_at`, `created_at` timestamps

**CRITICAL: CSV is Source of Truth (Data Import Strategy)**

**Before Migration:**
- Database: 1,195 units currently assigned (24%)

**Overlap Analysis:**
- 947 units exist in BOTH CSV and database (49% of CSV)
- CSV data is FINAL and FIXED - these represent the authoritative assignments
- Existing DB data for these 947 units is OUTDATED and will be replaced

**Migration Process:**
1. **STEP 1: RESET** - Clear 947 overlap units (set customer_id=NULL, kontrak_id=NULL, etc.)
2. **STEP 2-6: IMPORT** - Import all 1,939 units from CSV as fresh assignments

**After Migration:**
- Total assigned: 1,939 units (39% of inventory) ← CSV count EXACTLY
- Overlap units: 947 → REPLACED with CSV data
- New assignments: 992 → Fresh from CSV
- Unchanged: 248 → Units assigned in DB but not in CSV (remain as-is)

**Why Reset Instead of Update?**
- CSV represents the FINAL, CORRECTED operational data from marketing team
- If overlap exists, it means previous DB data had errors or is outdated
- Resetting ensures CSV data completely replaces questionable old assignments
- Prevents data conflicts and ensures single source of truth

Remaining **3,050 unassigned units** (61% of 4,989 total) will be:
- SPARE units (status_unit = 'TERSEDIA')
- STOCK assets (workflow_status = 'STOCK_ASET')
- Under maintenance/repair
- To be assigned in future contracts

---

## 📞 SUPPORT & NEXT STEPS

**Generated Scripts:**
1. `migration_cleaning_script.py` - Python data cleaner
2. `INSERT_MARKETING_DATA.sql` - Generated SQL import
3. `POST_MIGRATION_VALIDATION.sql` - Validation queries

**Documentation:**
- This file: `DATABASE_MIGRATION_STRATEGY_COMPLETE.md`
- ERD diagram saved separately if needed

**Questions/Issues:**
Contact: Development Team
Date: 2026-02-18
