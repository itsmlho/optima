#!/usr/bin/env python3
"""
⚠️  DEPRECATED - USE merge_marketing_accounting.py INSTEAD!
============================================================

This script has been SUPERSEDED by merge_marketing_accounting.py

REASON: 
- New data source available (data_from_acc.csv from accounting)
- data_from_acc.csv has 95% data completeness (vs 75% in marketing_fix.csv)
- Need to MERGE both files for best results (~2,100 units)

USE INSTEAD:
    python merge_marketing_accounting.py

See MERGE_QUICKSTART.md for full guide.

============================================================

OPTIMA ERP - Marketing Data Import Script (LEGACY)
==========================================
Purpose: Clean and import data from marketing_fix.csv to database
Author: Development Team
Date: 2026-02-18

Features:
- Normalize customer names and data
- Handle missing locations (create defaults)
- Handle missing contracts (create placeholders)
- Parse Indonesian price format
- Generate SQL import script
- Create validation reports

Usage:
    python migration_cleaning_script.py

Output:
    - INSERT_MARKETING_DATA.sql (SQL import script)
    - migration_report.txt (validation report)
    - missing_data_report.csv (flagged issues)
"""

import pandas as pd
import mysql.connector
import re
from datetime import datetime, timedelta
from decimal import Decimal
import sys

# ============================================================================
# CONFIGURATION
# ============================================================================

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',  # Add if needed
    'database': 'optima_ci'
}

CSV_FILES = {
    'marketing': 'marketing_fix.csv',
    'customers': 'customer2.csv',
    'locations': 'customer_locations2.csv'
}

OUTPUT_FILES = {
    'sql': 'INSERT_MARKETING_DATA.sql',
    'report': 'migration_report.txt',
    'missing': 'missing_data_report.csv'
}

# ============================================================================
# UTILITY FUNCTIONS
# ============================================================================

def normalize_customer_name(name):
    """Standardize customer name format."""
    if pd.isna(name):
        return None
    
    name = str(name).strip()
    name = re.sub(r'\s+', ' ', name)  # Remove extra spaces
    name = name.replace('PT.', 'PT').replace('CV.', 'CV')
    name = name.upper()  # Standardize to uppercase
    
    return name


def validate_unit_number(no_unit):
    """Extract valid unit number from string."""
    if pd.isna(no_unit):
        return None
    
    # Try direct conversion first
    try:
        return int(no_unit)
    except (ValueError, TypeError):
        pass
    
    # Extract digits from string like "FL-3622"
    digits = re.findall(r'\d+', str(no_unit))
    if digits:
        return int(digits[0])
    
    return None


def parse_price(price_str):
    """Convert Indonesian price format to decimal.
    
    Examples:
        "Rp8.500.000,00" -> 8500000.00
        "Rp 8.500.000" -> 8500000.00
        "" -> None
    """
    if not price_str or pd.isna(price_str):
        return None
    
    # Remove currency symbols, 'Rp', spaces
    clean = re.sub(r'[Rp\s]', '', str(price_str))
    
    # Handle Indonesian format: 8.500.000,00 -> 8500000.00
    # Replace dots (thousand separators) with nothing
    # Replace comma (decimal separator) with dot
    clean = clean.replace('.', '').replace(',', '.')
    
    try:
        value = float(clean)
        return Decimal(value) if value > 0 else None
    except (ValueError, decimal.InvalidOperation):
        print(f"⚠️ Cannot parse price: {price_str}")
        return None


def parse_date(date_str):
    """Parse Indonesian date format.
    
    Examples:
        "09/10/2025" -> 2025-10-09
        "01/01/2025" -> 2025-01-01
    """
    if not date_str or pd.isna(date_str):
        return None
    
    date_str = str(date_str).strip()
    
    # Try DD/MM/YYYY format
    try:
        parts = date_str.split('/')
        if len(parts) == 3:
            day, month, year = parts
            return f"{year}-{month.zfill(2)}-{day.zfill(2)}"
    except Exception as e:
        print(f"⚠️ Cannot parse date: {date_str} ({e})")
    
    return None


def sql_escape(value):
    """Escape SQL string values."""
    if value is None or pd.isna(value):
        return 'NULL'
    
    if isinstance(value, (int, float, Decimal)):
        return str(value)
    
    # Escape single quotes
    value = str(value).replace("'", "''")
    return f"'{value}'"


# ============================================================================
# DATABASE CONNECTION & LOOKUP BUILDERS
# ============================================================================

def get_db_connection():
    """Create MySQL database connection."""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        return conn
    except mysql.connector.Error as err:
        print(f"❌ Database connection failed: {err}")
        sys.exit(1)


def build_lookups(conn):
    """Build lookup dictionaries from database."""
    cursor = conn.cursor()
    
    lookups = {
        'customers': {},     # {normalized_name: id}
        'customer_codes': {}, # {customer_code: id}
        'locations': {},     # {(customer_id, location_name): id}
        'areas': {},         # {area_name: id}
        'contracts': {},     # {customer_po_number: kontrak_id}
        'units': set()       # {no_unit} - existing units in inventory
    }
    
    # Load customers
    cursor.execute("SELECT id, customer_code, customer_name FROM customers WHERE is_active = 1")
    for row in cursor.fetchall():
        cust_id, code, name = row
        lookups['customers'][normalize_customer_name(name)] = cust_id
        lookups['customer_codes'][code] = cust_id
    
    # Load locations
    cursor.execute("""
        SELECT id, customer_id, location_name 
        FROM customer_locations 
        WHERE is_active = 1
    """)
    for row in cursor.fetchall():
        loc_id, cust_id, loc_name = row
        lookups['locations'][(cust_id, loc_name.upper())] = loc_id
    
    # Load areas
    cursor.execute("SELECT id, area_code, area_name FROM areas WHERE is_active = 1")
    for row in cursor.fetchall():
        area_id, code, name = row
        lookups['areas'][code.upper()] = area_id
        lookups['areas'][name.upper()] = area_id
    
    # Load existing contracts
    cursor.execute("""
        SELECT id, customer_po_number 
        FROM kontrak 
        WHERE customer_po_number IS NOT NULL
    """)
    for row in cursor.fetchall():
        kontrak_id, po_number = row
        lookups['contracts'][po_number] = kontrak_id
    
    # Load existing unit numbers
    cursor.execute("SELECT no_unit FROM inventory_unit WHERE no_unit IS NOT NULL")
    for row in cursor.fetchall():
        lookups['units'].add(row[0])
    
    cursor.close()
    
    print(f"✅ Loaded lookups:")
    print(f"   - Customers: {len(lookups['customers'])}")
    print(f"   - Locations: {len(lookups['locations'])}")
    print(f"   - Areas: {len(lookups['areas'])}")
    print(f"   - Contracts: {len(lookups['contracts'])}")
    print(f"   - Units in inventory: {len(lookups['units'])}")
    
    return lookups


# ============================================================================
# DATA PROCESSING
# ============================================================================

def process_marketing_data(df, lookups, conn):
    """Process marketing_fix.csv and generate import data.
    
    Returns:
        dict: {
            'locations': [...],  # New locations to create
            'contracts': [...],  # New contracts to create
            'unit_updates': [...], # UPDATE statements for inventory_unit
            'units_to_reset': [...], # Units with overlap (reset first)
            'missing_customers': [...], # Customers not found
            'missing_units': [...], # Units not in inventory
            'stats': {...}
        }
    """
    
    result = {
        'locations': [],
        'contracts': [],
        'unit_updates': [],
        'units_to_reset': [],  # NEW: Track overlap units
        'missing_customers': [],
        'missing_units': [],
        'stats': {
            'total_rows': len(df),
            'processed': 0,
            'skipped': 0,
            'new_locations': 0,
            'new_contracts': 0,
            'units_updated': 0,
            'overlap_reset': 0  # NEW: Count overlap
        }
    }
    
    # Track created items to avoid duplicates
    created_locations = set()
    created_contracts = set()
    
    # Build set of currently assigned units in DB (for overlap detection)
    cursor = conn.cursor()
    cursor.execute("""
        SELECT no_unit 
        FROM inventory_unit 
        WHERE customer_id IS NOT NULL
    """)
    db_assigned_units = set([row[0] for row in cursor.fetchall()])
    cursor.close()
    
    print(f"🔍 Overlap detection: {len(db_assigned_units)} units currently assigned in DB")
    
    for idx, row in df.iterrows():
        # Skip rows with empty unit number
        unit_number = validate_unit_number(row.get('No Unit'))
        if not unit_number:
            print(f"⚠️ Row {idx+2}: Empty unit number, skipping")
            result['stats']['skipped'] += 1
            continue
        
        # Check if unit exists in inventory
        if unit_number not in lookups['units']:
            result['missing_units'].append({
                'row': idx+2,
                'unit_number': unit_number,
                'customer': row.get('nama customer')
            })
            result['stats']['skipped'] += 1
            continue
        
        # Check if unit is already assigned (overlap) - need to reset first
        if unit_number in db_assigned_units:
            if unit_number not in [u['unit_number'] for u in result['units_to_reset']]:
                result['units_to_reset'].append({'unit_number': unit_number})
                result['stats']['overlap_reset'] += 1
        
        # ========== STEP 1: Find Customer ==========
        cust_name = normalize_customer_name(row.get('nama customer'))
        customer_id = lookups['customers'].get(cust_name)
        
        if not customer_id:
            result['missing_customers'].append({
                'row': idx+2,
                'customer_name': row.get('nama customer'),
                'normalized': cust_name
            })
            result['stats']['skipped'] += 1
            continue
        
        # ========== STEP 2: Find or Create Location ==========
        location_name = str(row.get('Lokasi', '')).strip()
        if not location_name or location_name.upper() in ['', '#N/A', 'N/A', 'NULL']:
            location_name = 'DEFAULT LOCATION'
        
        location_key = (customer_id, location_name.upper())
        location_id = lookups['locations'].get(location_key)
        
        if not location_id and location_key not in created_locations:
            # Find area_id
            area_name = str(row.get('Area', '')).strip().upper()
            area_id = lookups['areas'].get(area_name)
            
            # Generate location INSERT
            result['locations'].append({
                'customer_id': customer_id,
                'area_id': area_id,
                'location_name': location_name,
                'location_code': f'LOC-{customer_id}-{len(created_locations)+1}',
                'city': area_name if area_id else 'N/A',
                'province': 'N/A'
            })
            
            created_locations.add(location_key)
            result['stats']['new_locations'] += 1
            
            # For next rows, assume this location ID (will be generated sequentially)
            location_id = f'NEW_LOC_{customer_id}_{location_name}'
        
        # ========== STEP 3: Find or Create Contract ==========
        po_number = row.get('No PO')
        if pd.isna(po_number) or str(po_number).strip() == '':
            po_number = None
        else:
            po_number = str(po_number).strip()
        
        kontrak_id = lookups['contracts'].get(po_number) if po_number else None
        
        if not kontrak_id and po_number and po_number not in created_contracts:
            # Parse contract dates
            start_date = parse_date(row.get('Awal Kontrak'))
            end_date = parse_date(row.get('Kontrak Habis'))
            
            if not start_date:
                start_date = datetime.now().strftime('%Y-%m-%d')
            if not end_date:
                # Default 1 year contract
                end_date = (datetime.now() + timedelta(days=365)).strftime('%Y-%m-%d')
            
            result['contracts'].append({
                'customer_id': customer_id,
                'location_id': location_id,
                'no_kontrak': po_number,
                'customer_po_number': po_number,
                'tanggal_mulai': start_date,
                'tanggal_berakhir': end_date,
                'status': 'PENDING'  # Marketing will activate later
            })
            
            created_contracts.add(po_number)
            result['stats']['new_contracts'] += 1
            
            kontrak_id = f'NEW_CONTRACT_{po_number}'
        
        # ========== STEP 4: Generate Unit Update ==========
        monthly_price = parse_price(row.get('Harga'))
        
        result['unit_updates'].append({
            'unit_number': unit_number,
            'customer_id': customer_id,
            'location_id': location_id if not str(location_id).startswith('NEW_LOC') else 'QUERY_LOCATION',
            'kontrak_id': kontrak_id if not str(kontrak_id).startswith('NEW_CONTRACT') else 'QUERY_CONTRACT',
            'monthly_price': monthly_price,
            'on_hire_date': start_date if 'start_date' in locals() else None,
            'po_number': po_number
        })
        
        result['stats']['processed'] += 1
        result['stats']['units_updated'] += 1
    
    return result


# ============================================================================
# SQL GENERATION
# ============================================================================

def generate_sql(data, output_file):
    """Generate SQL import script."""
    
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write("-- ============================================================================\n")
        f.write("-- OPTIMA ERP - Marketing Data Import SQL Script\n")
        f.write(f"-- Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write("-- ============================================================================\n\n")
        
        f.write("-- IMPORTANT: This script will RESET overlapping unit assignments first!\n")
        f.write("-- CSV data is treated as SOURCE OF TRUTH.\n")
        f.write("-- Any existing assignments for units in CSV will be cleared before import.\n\n")
        
        f.write("SET FOREIGN_KEY_CHECKS = 0;\n")
        f.write("START TRANSACTION;\n\n")
        
        # ========== RESET OVERLAP UNITS FIRST ==========
        if data['units_to_reset']:
            f.write("-- ============================================================================\n")
            f.write(f"-- STEP 1: RESET OVERLAPPING UNITS ({len(data['units_to_reset'])} units)\n")
            f.write("-- ============================================================================\n")
            f.write("-- These units exist in both CSV and database.\n")
            f.write("-- Clearing existing assignments to prepare for fresh import from CSV.\n\n")
            
            # Generate list of unit numbers to reset
            unit_numbers = [str(u['unit_number']) for u in data['units_to_reset']]
            
            # Split into batches of 100 for performance
            batch_size = 100
            for i in range(0, len(unit_numbers), batch_size):
                batch = unit_numbers[i:i+batch_size]
                f.write(f"-- Reset batch {i//batch_size + 1}\n")
                f.write(f"UPDATE inventory_unit\n")
                f.write(f"SET \n")
                f.write(f"  customer_id = NULL,\n")
                f.write(f"  kontrak_id = NULL,\n")
                f.write(f"  customer_location_id = NULL,\n")
                f.write(f"  area_id = NULL,\n")
                f.write(f"  harga_sewa_bulanan = NULL,\n")
                f.write(f"  harga_sewa_harian = NULL,\n")
                f.write(f"  on_hire_date = NULL,\n")
                f.write(f"  off_hire_date = NULL,\n")
                f.write(f"  rate_changed_at = NULL\n")
                f.write(f"WHERE no_unit IN ({', '.join(batch)});\n\n")
            
            f.write(f"-- ✅ Reset {len(data['units_to_reset'])} overlapping units\n\n")
        
        # ========== INSERT NEW LOCATIONS ==========
        if data['locations']:
            f.write("-- ============================================================================\n")
            f.write(f"-- STEP 2: INSERT NEW CUSTOMER LOCATIONS ({len(data['locations'])} records)\n")
            f.write("-- ============================================================================\n\n")
            
            for loc in data['locations']:
                f.write(f"INSERT INTO customer_locations ")
                f.write(f"(customer_id, area_id, location_name, location_code, ")
                f.write(f"address, city, province, location_type, is_primary, is_active) ")
                f.write(f"VALUES (")
                f.write(f"{loc['customer_id']}, ")
                f.write(f"{sql_escape(loc['area_id'])}, ")
                f.write(f"{sql_escape(loc['location_name'])}, ")
                f.write(f"{sql_escape(loc['location_code'])}, ")
                f.write(f"'Auto-generated from marketing data import', ")
                f.write(f"{sql_escape(loc['city'])}, ")
                f.write(f"{sql_escape(loc['province'])}, ")
                f.write(f"'BRANCH', 0, 1);\n")
            
            f.write("\n")
        
        # ========== INSERT NEW CONTRACTS ==========
        if data['contracts']:
            f.write("-- ============================================================================\n")
            f.write(f"-- STEP 3: INSERT NEW CONTRACTS ({len(data['contracts'])} records)\n")
            f.write("-- ============================================================================\n\n")
            
            for contract in data['contracts']:
                # Find location_id (if it was just created, use subquery)
                if str(contract['location_id']).startswith('NEW_LOC'):
                    # Extract customer_id from location_id like 'NEW_LOC_123_LocationName'
                    parts = contract['location_id'].split('_')
                    cust_id = parts[2]
                    loc_name_query = f"(SELECT id FROM customer_locations WHERE customer_id = {cust_id} AND location_name = {sql_escape(contract['location_id'].split('_', 3)[3])} LIMIT 1)"
                else:
                    loc_name_query = str(contract['location_id'])
                
                f.write(f"INSERT INTO kontrak ")
                f.write(f"(customer_location_id, no_kontrak, rental_type, customer_po_number, ")
                f.write(f"tanggal_mulai, tanggal_berakhir, status, dibuat_pada) ")
                f.write(f"VALUES (")
                f.write(f"{loc_name_query}, ")
                f.write(f"{sql_escape(contract['no_kontrak'])}, ")
                f.write(f"'PO_ONLY', ")
                f.write(f"{sql_escape(contract['customer_po_number'])}, ")
                f.write(f"{sql_escape(contract['tanggal_mulai'])}, ")
                f.write(f"{sql_escape(contract['tanggal_berakhir'])}, ")
                f.write(f"'{contract['status']}', ")
                f.write(f"NOW());\n")
            
            f.write("\n")
        
        # ========== UPDATE INVENTORY UNITS ==========
        if data['unit_updates']:
            f.write("-- ============================================================================\n")
            f.write(f"-- STEP 4: UPDATE INVENTORY UNITS ({len(data['unit_updates'])} records)\n")
            f.write("-- ============================================================================\n")
            f.write("-- Assigning units from CSV to customers, locations, and contracts.\n\n")
            
            for update in data['unit_updates']:
                # Build location_id query
                if update['location_id'] == 'QUERY_LOCATION':
                    loc_query = f"(SELECT id FROM customer_locations WHERE customer_id = {update['customer_id']} ORDER BY is_primary DESC LIMIT 1)"
                else:
                    loc_query = str(update['location_id'])
                
                # Build kontrak_id query
                if update['kontrak_id'] == 'QUERY_CONTRACT' and update['po_number']:
                    kontrak_query = f"(SELECT id FROM kontrak WHERE customer_po_number = {sql_escape(update['po_number'])} LIMIT 1)"
                elif str(update['kontrak_id']).startswith('NEW_CONTRACT'):
                    po = update['kontrak_id'].replace('NEW_CONTRACT_', '')
                    kontrak_query = f"(SELECT id FROM kontrak WHERE customer_po_number = {sql_escape(po)} LIMIT 1)"
                else:
                    kontrak_query = sql_escape(update['kontrak_id'])
                
                f.write(f"UPDATE inventory_unit SET\n")
                f.write(f"  customer_id = {update['customer_id']},\n")
                f.write(f"  customer_location_id = {loc_query},\n")
                f.write(f"  kontrak_id = {kontrak_query},\n")
                f.write(f"  area_id = (SELECT area_id FROM customer_locations WHERE id = {loc_query}),\n")
                f.write(f"  harga_sewa_bulanan = {sql_escape(update['monthly_price'])},\n")
                f.write(f"  on_hire_date = {sql_escape(update['on_hire_date'])},\n")
                f.write(f"  rate_changed_at = NOW()\n")
                f.write(f"WHERE no_unit = {update['unit_number']};\n\n")
        
        # ========== CREATE CUSTOMER_CONTRACTS LINKS ==========
        f.write("-- ============================================================================\n")
        f.write("-- STEP 5: CREATE/UPDATE CUSTOMER_CONTRACTS LINKS\n")
        f.write("-- ============================================================================\n\n")
        f.write("""
INSERT INTO customer_contracts (customer_id, kontrak_id, is_active, created_at, updated_at)
SELECT DISTINCT 
    iu.customer_id, 
    iu.kontrak_id, 
    1, 
    NOW(), 
    NOW()
FROM inventory_unit iu
WHERE iu.customer_id IS NOT NULL 
  AND iu.kontrak_id IS NOT NULL
  AND NOT EXISTS (
      SELECT 1 FROM customer_contracts cc 
      WHERE cc.customer_id = iu.customer_id 
        AND cc.kontrak_id = iu.kontrak_id
  );
""")
        
        # ========== UPDATE CONTRACT TOTALS ==========
        f.write("-- ============================================================================\n")
        f.write("-- STEP 6: UPDATE CONTRACT TOTAL_UNITS\n")
        f.write("-- ============================================================================\n\n")
        f.write("""
UPDATE kontrak k
SET total_units = (
    SELECT COUNT(*) 
    FROM inventory_unit iu 
    WHERE iu.kontrak_id = k.id
);
""")
        
        f.write("\n-- Commit transaction\n")
        f.write("COMMIT;\n")
        f.write("SET FOREIGN_KEY_CHECKS = 1;\n\n")
        
        f.write("-- ============================================================================\n")
        f.write("-- VERIFICATION QUERIES\n")
        f.write("-- ============================================================================\n\n")
        f.write("SELECT 'Total customers with units:' as info, COUNT(DISTINCT customer_id) as count FROM inventory_unit WHERE customer_id IS NOT NULL;\n")
        f.write("SELECT 'Total units assigned:' as info, COUNT(*) as count FROM inventory_unit WHERE customer_id IS NOT NULL;\n")
        f.write("SELECT 'Total active contracts:' as info, COUNT(*) as count FROM kontrak WHERE status = 'ACTIVE';\n")
        f.write("SELECT 'Total customer-contract links:' as info, COUNT(*) as count FROM customer_contracts;\n")
    
    print(f"✅ SQL script generated: {output_file}")


# ============================================================================
# REPORTING
# ============================================================================

def generate_reports(data, report_file, missing_file):
    """Generate migration reports."""
    
    # Text report
    with open(report_file, 'w', encoding='utf-8') as f:
        f.write("=" * 80 + "\n")
        f.write("OPTIMA ERP - Marketing Data Import Report\n")
        f.write("=" * 80 + "\n\n")
        f.write(f"Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n\n")
        
        f.write("SUMMARY\n")
        f.write("-" * 80 + "\n")
        f.write(f"Total rows in CSV:           {data['stats']['total_rows']}\n")
        f.write(f"Successfully processed:      {data['stats']['processed']}\n")
        f.write(f"Skipped (errors):            {data['stats']['skipped']}\n")
        f.write(f"New locations created:       {data['stats']['new_locations']}\n")
        f.write(f"New contracts created:       {data['stats']['new_contracts']}\n")
        f.write(f"Overlapping units (reset):   {data['stats']['overlap_reset']}\n")
        f.write(f"Units to be updated:         {data['stats']['units_updated']}\n\n")
        
        f.write("OVERLAP ANALYSIS\n")
        f.write("-" * 80 + "\n")
        f.write(f"CSV is treated as SOURCE OF TRUTH. Existing assignments for\n")
        f.write(f"{data['stats']['overlap_reset']} overlapping units will be CLEARED before import.\n")
        f.write(f"Then all {data['stats']['units_updated']} units from CSV will be assigned.\n\n")
        
        if data['missing_customers']:
            f.write("MISSING CUSTOMERS\n")
            f.write("-" * 80 + "\n")
            f.write(f"Found {len(data['missing_customers'])} rows with customers not in database:\n\n")
            for item in data['missing_customers'][:20]:  # Show first 20
                f.write(f"  Row {item['row']}: {item['customer_name']}\n")
            if len(data['missing_customers']) > 20:
                f.write(f"  ... and {len(data['missing_customers']) - 20} more\n")
            f.write("\n")
        
        if data['missing_units']:
            f.write("MISSING UNITS\n")
            f.write("-" * 80 + "\n")
            f.write(f"Found {len(data['missing_units'])} rows with units not in inventory:\n\n")
            for item in data['missing_units'][:20]:
                f.write(f"  Row {item['row']}: Unit #{item['unit_number']} (Customer: {item['customer']})\n")
            if len(data['missing_units']) > 20:
                f.write(f"  ... and {len(data['missing_units']) - 20} more\n")
            f.write("\n")
        
        f.write("NEXT STEPS\n")
        f.write("-" * 80 + "\n")
        f.write("1. Review missing_data_report.csv for flagged issues\n")
        f.write("2. Add missing customers to database if needed\n")
        f.write("3. Verify generated SQL script: INSERT_MARKETING_DATA.sql\n")
        f.write("4. Backup database before execution\n")
        f.write("5. Execute SQL import script\n")
        f.write("6. Run validation queries\n")
    
    print(f"✅ Text report generated: {report_file}")
    
    # CSV report for missing data
    missing_data = []
    
    for item in data['missing_customers']:
        missing_data.append({
            'type': 'MISSING_CUSTOMER',
            'row': item['row'],
            'value': item['customer_name'],
            'normalized': item['normalized'],
            'action_required': 'Add customer to database or fix name spelling'
        })
    
    for item in data['missing_units']:
        missing_data.append({
            'type': 'MISSING_UNIT',
            'row': item['row'],
            'value': f"Unit #{item['unit_number']}",
            'normalized': item['customer'],
            'action_required': 'Verify unit exists in inventory_unit table'
        })
    
    if missing_data:
        df_missing = pd.DataFrame(missing_data)
        df_missing.to_csv(missing_file, index=False)
        print(f"✅ Missing data report generated: {missing_file}")


# ============================================================================
# MAIN EXECUTION
# ============================================================================

def main():
    print("=" * 80)
    print("OPTIMA ERP - Marketing Data Import Tool")
    print("=" * 80)
    print()
    
    # Step 1: Load CSV file
    print("📂 Loading marketing_fix.csv...")
    try:
        df = pd.read_csv(CSV_FILES['marketing'], delimiter=';')
        print(f"✅ Loaded {len(df)} rows from CSV")
    except FileNotFoundError:
        print(f"❌ File not found: {CSV_FILES['marketing']}")
        print(f"   Please ensure the file exists in the current directory.")
        sys.exit(1)
    except Exception as e:
        print(f"❌ Error loading CSV: {e}")
        sys.exit(1)
    
    # Step 2: Connect to database
    print("\n🔌 Connecting to database...")
    conn = get_db_connection()
    print("✅ Database connected")
    
    # Step 3: Build lookup tables
    print("\n🔍 Building lookup tables...")
    lookups = build_lookups(conn)
    
    # Step 4: Process data
    print("\n⚙️ Processing marketing data...")
    result = process_marketing_data(df, lookups, conn)
    
    print(f"\n📊 Processing complete:")
    print(f"   - Processed: {result['stats']['processed']} rows")
    print(f"   - Skipped: {result['stats']['skipped']} rows")
    print(f"   - New locations: {result['stats']['new_locations']}")
    print(f"   - New contracts: {result['stats']['new_contracts']}")
    print(f"   - Units to update: {result['stats']['units_updated']}")
    print(f"   - Overlap (will be reset): {result['stats']['overlap_reset']} units")
    
    if result['stats']['overlap_reset'] > 0:
        print(f"\n⚠️  IMPORTANT: {result['stats']['overlap_reset']} units exist in BOTH CSV and database.")
        print(f"   These will be RESET first, then re-assigned from CSV.")
        print(f"   CSV is SOURCE OF TRUTH - existing assignments will be cleared!")
    
    if result['missing_customers']:
        print(f"   ⚠️ Missing customers: {len(result['missing_customers'])}")
    if result['missing_units']:
        print(f"   ⚠️ Missing units: {len(result['missing_units'])}")
    
    # Step 5: Generate SQL
    print("\n📝 Generating SQL import script...")
    generate_sql(result, OUTPUT_FILES['sql'])
    
    # Step 6: Generate reports
    print("\n📋 Generating reports...")
    generate_reports(result, OUTPUT_FILES['report'], OUTPUT_FILES['missing'])
    
    # Close database connection
    conn.close()
    
    print("\n" + "=" * 80)
    print("✅ MIGRATION SCRIPT COMPLETED")
    print("=" * 80)
    print(f"\nGenerated files:")
    print(f"   - {OUTPUT_FILES['sql']} (SQL import script)")
    print(f"   - {OUTPUT_FILES['report']} (text report)")
    print(f"   - {OUTPUT_FILES['missing']} (flagged issues)")
    print(f"\nNext steps:")
    print(f"   1. Review {OUTPUT_FILES['missing']} for any issues")
    print(f"   2. Review {OUTPUT_FILES['sql']} before execution")
    print(f"   3. Backup database: mysqldump optima_ci > backup.sql")
    print(f"   4. Execute: mysql -u root optima_ci < {OUTPUT_FILES['sql']}")
    print()


if __name__ == '__main__':
    main()
