#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
OPTIMA ERP - Marketing & Accounting Data Merger
==============================================
Merges marketing_fix.csv and data_from_acc.csv into unified dataset

Strategy:
- BASE: marketing_fix.csv (1,940 units) - has cust_id & Area (CRITICAL)
- ENRICH: data_from_acc.csv (1,412 units) - more complete data from accounting
- OUTPUT: ~2,100 units with best data from both sources

Author: Generated for OPTIMA ERP
Date: 2026-02-18
"""

import pandas as pd
import mysql.connector
import re
from datetime import datetime
from decimal import Decimal

# ========== CONFIGURATION ==========

DB_CONFIG = {
    'host': 'localhost',
    'user': 'root',
    'password': '',
    'database': 'optima_ci'
}

CSV_FILES = {
    'marketing': 'marketing_fix.csv',      # Base source (has cust_id)
    'accounting': 'data_from_acc.csv'      # Enrichment source (more complete)
}

OUTPUT_FILES = {
    'sql': 'PRODUCTION_MIGRATION_FEBRUARY_2026_COMPLETE.sql',
    'report': 'merge_report.txt',
    'missing_customers': 'missing_customer_mapping.csv',
    'conflicts': 'data_conflicts_report.csv'
}

# Production compatibility mode (older database schema)
# Set to True if production doesn't have 'rental_type' column in kontrak table
PRODUCTION_MODE = False  # DISABLED - Production akan di-ALTER dulu untuk match schema

# ========== UTILITY FUNCTIONS ==========

def escape_sql_string(value):
    """Escape single quotes for SQL string safety"""
    if value is None or pd.isna(value):
        return ''
    value = str(value).replace("'", "''")  # Escape single quotes
    return value

def calculate_contract_end_date(start_date_str, end_date_str):
    """Calculate contract end date, default to start + 1 year if not provided"""
    if end_date_str:
        return end_date_str
    
    # If no end date, calculate start_date + 1 year
    try:
        if isinstance(start_date_str, str):
            from dateutil.relativedelta import relativedelta
            start = datetime.strptime(start_date_str, '%Y-%m-%d')
            end = start + relativedelta(years=1)
            return end.strftime('%Y-%m-%d')
    except:
        pass
    
    # Fallback: use start_date + 365 days (simple calculation)
    return (datetime.now() + pd.Timedelta(days=365)).strftime('%Y-%m-%d')

def normalize_customer_name(name):
    """Normalize customer name for matching"""
    if pd.isna(name) or not name:
        return ''
    
    name = str(name).strip().upper()
    # Remove extra spaces
    name = re.sub(r'\s+', ' ', name)
    # Standardize PT/CV formats
    name = name.replace('PT.', 'PT').replace('CV.', 'CV')
    # Remove location/context suffixes in parentheses
    name = re.sub(r'\s*\([^)]*\)', '', name)
    # Remove PT prefix for matching
    name = re.sub(r'^PT\s+', '', name)
    
    return name

def parse_price(price_str):
    """Parse Indonesian price format to float"""
    if not price_str or pd.isna(price_str):
        return None
    
    price_str = str(price_str).strip()
    if not price_str or price_str.lower() in ['', '-', 'n/a', '#n/a', '???', '??']:
        return None
    
    # Remove currency symbols and spaces
    clean = re.sub(r'[Rp\s]', '', price_str)
    
    # Convert Indonesian format: 8.500.000,00 -> 8500000.00
    clean = clean.replace('.', '').replace(',', '.')
    
    try:
        return float(clean)
    except ValueError:
        return None

def parse_date(date_str):
    """Parse various date formats to YYYY-MM-DD"""
    if not date_str or pd.isna(date_str):
        return None
    
    date_str = str(date_str).strip()
    if not date_str or date_str in ['', '-', 'N/A']:
        return None
    
    # Try multiple formats
    formats = [
        '%d/%m/%Y',    # 09/10/2025
        '%d-%m-%Y',    # 09-10-2025
        '%d-%b-%y',    # 30-May-24
        '%d-%b-%Y',    # 30-May-2024
        '%Y-%m-%d',    # 2025-10-09
    ]
    
    for fmt in formats:
        try:
            dt = datetime.strptime(date_str, fmt)
            return dt.strftime('%Y-%m-%d')
        except ValueError:
            continue
    
    return None

def validate_unit_number(no_unit):
    """Validate and extract unit number"""
    if pd.isna(no_unit):
        return None
    
    try:
        return int(no_unit)
    except (ValueError, TypeError):
        # Try to extract digits
        digits = re.findall(r'\d+', str(no_unit))
        if digits:
            return int(digits[0])
    return None

# ========== DATABASE CONNECTION ==========

def connect_database():
    """Connect to MySQL database"""
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        print(f"✅ Database connected: {DB_CONFIG['database']}")
        return conn
    except mysql.connector.Error as err:
        print(f"❌ Database connection failed: {err}")
        raise

def build_lookups(conn):
    """Build lookup dictionaries from database"""
    cursor = conn.cursor()
    
    # Customer lookup (by normalized name)
    cursor.execute("SELECT id, customer_name FROM customers WHERE is_active = 1")
    customer_map = {}
    for row in cursor.fetchall():
        normalized = normalize_customer_name(row[1])
        customer_map[normalized] = {
            'id': row[0],
            'name': row[1]
        }
    print(f"✅ Loaded {len(customer_map)} customers")
    
    # Area lookup
    cursor.execute("SELECT id, area_name FROM areas WHERE is_active = 1")
    area_map = {}
    for row in cursor.fetchall():
        area_map[row[1].upper()] = row[0]
    print(f"✅ Loaded {len(area_map)} areas")
    
    # Location lookup
    cursor.execute("""
        SELECT id, customer_id, location_name, area_id 
        FROM customer_locations 
        WHERE is_active = 1
    """)
    location_map = {}
    for row in cursor.fetchall():
        key = (row[1], row[2])  # (customer_id, location_name)
        location_map[key] = {
            'id': row[0],
            'area_id': row[3]
        }
    print(f"✅ Loaded {len(location_map)} locations")
    
    # Contract lookup (by PO number)
    cursor.execute("""
        SELECT id, customer_po_number, customer_location_id 
        FROM kontrak 
        WHERE status != 'CANCELLED'
    """)
    contract_map = {}
    for row in cursor.fetchall():
        if row[1]:  # If has PO number
            contract_map[row[1]] = {
                'id': row[0],
                'location_id': row[2]
            }
    print(f"✅ Loaded {len(contract_map)} contracts")
    
    # Currently assigned units (for overlap detection)
    cursor.execute("SELECT no_unit FROM inventory_unit WHERE customer_id IS NOT NULL")
    db_assigned_units = set([row[0] for row in cursor.fetchall()])
    print(f"🔍 Overlap detection: {len(db_assigned_units)} units currently assigned in DB")
    
    cursor.close()
    
    return {
        'customers': customer_map,
        'areas': area_map,
        'locations': location_map,
        'contracts': contract_map,
        'db_assigned': db_assigned_units
    }

# ========== MERGE LOGIC ==========

def merge_data(df_marketing, df_accounting, lookups):
    """
    Merge marketing_fix.csv and data_from_acc.csv
    
    Strategy:
    1. Start with marketing_fix as base (has cust_id & Area)
    2. For overlap units: enrich with data_from_acc (better data)
    3. Add units only in data_from_acc (match customer by name)
    """
    
    result = {
        'units_to_reset': [],
        'locations_to_create': [],
        'contracts_to_create': [],
        'unit_updates': [],
        'missing_customers': [],
        'conflicts': [],
        'stats': {
            'base_marketing': len(df_marketing),
            'base_accounting': len(df_accounting),
            'overlap': 0,
            'enriched': 0,
            'added_from_accounting': 0,
            'total_processed': 0,
            'skipped': 0,
            'new_locations': 0,
            'new_contracts': 0,
            'units_reset': 0
        }
    }
    
    # Build unit lookup from marketing data
    marketing_units = {}
    for idx, row in df_marketing.iterrows():
        unit_num = validate_unit_number(row.get('No Unit'))
        if unit_num:
            marketing_units[unit_num] = row
    
    # Build unit lookup from accounting data
    accounting_units = {}
    for idx, row in df_accounting.iterrows():
        unit_num = validate_unit_number(row.get('NOUNIT'))
        if unit_num:
            accounting_units[unit_num] = row
    
    # Identify overlap
    overlap_units = set(marketing_units.keys()) & set(accounting_units.keys())
    marketing_only = set(marketing_units.keys()) - set(accounting_units.keys())
    accounting_only = set(accounting_units.keys()) - set(marketing_units.keys())
    
    result['stats']['overlap'] = len(overlap_units)
    
    print(f"\n📊 Data Analysis:")
    print(f"   Marketing units:     {len(marketing_units)}")
    print(f"   Accounting units:    {len(accounting_units)}")
    print(f"   Overlap:             {len(overlap_units)} units")
    print(f"   Marketing only:      {len(marketing_only)} units")
    print(f"   Accounting only:     {len(accounting_only)} units")
    
    # Check overlap with DB
    db_overlap = overlap_units & lookups['db_assigned']
    print(f"   DB overlap (reset):  {len(db_overlap)} units")
    
    # Add units to reset list
    for unit_num in db_overlap:
        result['units_to_reset'].append({'unit_number': unit_num})
    result['stats']['units_reset'] = len(db_overlap)
    
    processed_units = set()
    
    # ========== PROCESS 1: Overlap Units (Enrich) ==========
    print(f"\n⚙️  Processing {len(overlap_units)} overlap units (marketing base + accounting enrichment)...")
    
    for unit_num in overlap_units:
        m_row = marketing_units[unit_num]
        a_row = accounting_units[unit_num]
        
        # Get customer_id from marketing (CRITICAL)
        cust_id = m_row.get('cust_id')
        if pd.isna(cust_id) or not cust_id:
            result['stats']['skipped'] += 1
            continue
        
        try:
            cust_id = int(cust_id)
        except (ValueError, TypeError):
            result['stats']['skipped'] += 1
            continue
        
        # Get area from marketing
        area_name = str(m_row.get('Area', '')).upper() if not pd.isna(m_row.get('Area')) else None
        area_id = lookups['areas'].get(area_name) if area_name else None
        
        # ENRICHMENT: Use better data from accounting
        # Location: accounting has more detail
        lokasi_marketing = str(m_row.get('Lokasi', '')).strip() if not pd.isna(m_row.get('Lokasi')) else ''
        lokasi_accounting = str(a_row.get('LOKASI', '')).strip() if not pd.isna(a_row.get('LOKASI')) else ''
        
        # Choose better location
        if lokasi_accounting and lokasi_accounting not in ['', '#N/A', 'N/A']:
            location_name = lokasi_accounting
            enriched_field = 'location'
        elif lokasi_marketing and lokasi_marketing not in ['', '#N/A', 'N/A']:
            location_name = lokasi_marketing
            enriched_field = None
        else:
            location_name = 'DEFAULT LOCATION'
            enriched_field = None
        
        # ENRICHMENT: Price from accounting (more current)
        harga_marketing = parse_price(m_row.get('Harga'))
        harga_accounting = parse_price(a_row.get('HARGA'))
        
        if harga_accounting:
            monthly_price = harga_accounting
            if not harga_marketing or abs(harga_marketing - harga_accounting) > 100:
                enriched_field = 'price'
        elif harga_marketing:
            monthly_price = harga_marketing
        else:
            monthly_price = None
        
        # ENRICHMENT: PO from accounting (user choice)
        po_marketing = str(m_row.get('No PO', '')).strip() if not pd.isna(m_row.get('No PO')) else ''
        po_accounting = str(a_row.get('PO', '')).strip() if not pd.isna(a_row.get('PO')) else ''
        
        if po_accounting and po_accounting not in ['', 'N/A', '-']:
            po_number = po_accounting
            if po_marketing and po_marketing != po_accounting:
                # Log conflict
                result['conflicts'].append({
                    'unit': unit_num,
                    'field': 'PO',
                    'marketing': po_marketing,
                    'accounting': po_accounting,
                    'used': 'accounting'
                })
                enriched_field = 'po'
        elif po_marketing and po_marketing not in ['', 'N/A', '-']:
            po_number = po_marketing
        else:
            po_number = None
        
        # ENRICHMENT: Dates from accounting (better format)
        unit_antar = parse_date(a_row.get('UNIT ANTAR'))
        
        # Get or create location
        location_key = (cust_id, location_name)
        if location_key in lookups['locations']:
            location_id = lookups['locations'][location_key]['id']
        else:
            # Create new location
            result['locations_to_create'].append({
                'customer_id': cust_id,
                'area_id': area_id,
                'location_name': location_name,
                'location_code': f'LOC-{cust_id}-{len(result["locations_to_create"])+1}',
                'address': 'Auto-generated from merge',
                'city': location_name if location_name != 'DEFAULT LOCATION' else 'N/A',
                'province': area_name if area_name else 'N/A'
            })
            location_id = f"@location_{len(result['locations_to_create'])}"
            lookups['locations'][location_key] = {'id': location_id, 'area_id': area_id}
            result['stats']['new_locations'] += 1
        
        # Get or create contract
        kontrak_id = None
        if po_number and po_number in lookups['contracts']:
            kontrak_id = lookups['contracts'][po_number]['id']
        elif po_number:
            # Create new contract
            start_date = parse_date(m_row.get('Awal Kontrak')) or parse_date(a_row.get('PERIODE KONTRAK/PO'))
            if not start_date:
                start_date = datetime.now().strftime('%Y-%m-%d')
            end_date = parse_date(m_row.get('Kontrak Habis'))
            end_date = calculate_contract_end_date(start_date, end_date)
            
            result['contracts_to_create'].append({
                'customer_location_id': location_id,
                'no_kontrak': po_number,
                'customer_po_number': po_number,
                'tanggal_mulai': start_date,
                'tanggal_berakhir': end_date,
                'status': 'Aktif' if parse_date(m_row.get('Kontrak Habis')) else 'Pending',
                'rental_type': 'PO_ONLY'
            })
            kontrak_id = f"@contract_{len(result['contracts_to_create'])}"
            lookups['contracts'][po_number] = {'id': kontrak_id, 'location_id': location_id}
            result['stats']['new_contracts'] += 1
        
        # Generate unit update
        result['unit_updates'].append({
            'unit_number': unit_num,
            'customer_id': cust_id,
            'customer_location_id': location_id,
            'kontrak_id': kontrak_id,
            'area_id': area_id,
            'harga_sewa_bulanan': monthly_price,
            'on_hire_date': unit_antar,
            'source': 'overlap_enriched'
        })
        
        if enriched_field:
            result['stats']['enriched'] += 1
        
        processed_units.add(unit_num)
        result['stats']['total_processed'] += 1
    
    # ========== PROCESS 2: Marketing Only Units ==========
    print(f"\n⚙️  Processing {len(marketing_only)} marketing-only units...")
    
    for unit_num in marketing_only:
        m_row = marketing_units[unit_num]
        
        # Similar processing as overlap, but no enrichment
        cust_id = m_row.get('cust_id')
        if pd.isna(cust_id) or not cust_id:
            result['stats']['skipped'] += 1
            continue
        
        try:
            cust_id = int(cust_id)
        except (ValueError, TypeError):
            result['stats']['skipped'] += 1
            continue
        
        # Standard processing (same as overlap but without accounting data)
        area_name = str(m_row.get('Area', '')).upper() if not pd.isna(m_row.get('Area')) else None
        area_id = lookups['areas'].get(area_name) if area_name else None
        
        lokasi = str(m_row.get('Lokasi', '')).strip() if not pd.isna(m_row.get('Lokasi')) else ''
        if not lokasi or lokasi in ['#N/A', 'N/A']:
            lokasi = 'DEFAULT LOCATION'
        
        monthly_price = parse_price(m_row.get('Harga'))
        po_number = str(m_row.get('No PO', '')).strip() if not pd.isna(m_row.get('No PO')) else None
        
        # Get or create location
        location_key = (cust_id, lokasi)
        if location_key in lookups['locations']:
            location_id = lookups['locations'][location_key]['id']
        else:
            result['locations_to_create'].append({
                'customer_id': cust_id,
                'area_id': area_id,
                'location_name': lokasi,
                'location_code': f'LOC-{cust_id}-{len(result["locations_to_create"])+1}',
                'address': 'Auto-generated from merge',
                'city': lokasi if lokasi != 'DEFAULT LOCATION' else 'N/A',
                'province': area_name if area_name else 'N/A'
            })
            location_id = f"@location_{len(result['locations_to_create'])}"
            lookups['locations'][location_key] = {'id': location_id, 'area_id': area_id}
            result['stats']['new_locations'] += 1
        
        # Get or create contract
        kontrak_id = None
        if po_number and po_number in lookups['contracts']:
            kontrak_id = lookups['contracts'][po_number]['id']
        elif po_number and po_number not in ['', 'N/A', '-']:
            start_date = parse_date(m_row.get('Awal Kontrak'))
            if not start_date:
                start_date = datetime.now().strftime('%Y-%m-%d')
            end_date = parse_date(m_row.get('Kontrak Habis'))
            end_date = calculate_contract_end_date(start_date, end_date)
            
            result['contracts_to_create'].append({
                'customer_location_id': location_id,
                'no_kontrak': po_number,
                'customer_po_number': po_number,
                'tanggal_mulai': start_date,
                'tanggal_berakhir': end_date,
                'status': 'Aktif' if parse_date(m_row.get('Kontrak Habis')) else 'Pending',
                'rental_type': 'PO_ONLY'
            })
            kontrak_id = f"@contract_{len(result['contracts_to_create'])}"
            lookups['contracts'][po_number] = {'id': kontrak_id, 'location_id': location_id}
            result['stats']['new_contracts'] += 1
        
        result['unit_updates'].append({
            'unit_number': unit_num,
            'customer_id': cust_id,
            'customer_location_id': location_id,
            'kontrak_id': kontrak_id,
            'area_id': area_id,
            'harga_sewa_bulanan': monthly_price,
            'on_hire_date': None,
            'source': 'marketing_only'
        })
        
        processed_units.add(unit_num)
        result['stats']['total_processed'] += 1
    
    # ========== PROCESS 3: Accounting Only Units ==========
    print(f"\n⚙️  Processing {len(accounting_only)} accounting-only units...")
    
    for unit_num in accounting_only:
        a_row = accounting_units[unit_num]
        
        # Match customer by name
        customer_name = str(a_row.get('CUSTOMER', '')).strip()
        normalized = normalize_customer_name(customer_name)
        
        if normalized in lookups['customers']:
            cust_id = lookups['customers'][normalized]['id']
        else:
            # Flag for manual review
            result['missing_customers'].append({
                'unit': unit_num,
                'customer_name': customer_name,
                'normalized': normalized
            })
            result['stats']['skipped'] += 1
            continue
        
        # Process with accounting data only (no Area field)
        lokasi = str(a_row.get('LOKASI', '')).strip() if not pd.isna(a_row.get('LOKASI')) else 'DEFAULT LOCATION'
        monthly_price = parse_price(a_row.get('HARGA'))
        po_number = str(a_row.get('PO', '')).strip() if not pd.isna(a_row.get('PO')) else None
        unit_antar = parse_date(a_row.get('UNIT ANTAR'))
        
        # Get or create location
        location_key = (cust_id, lokasi)
        if location_key in lookups['locations']:
            location_id = lookups['locations'][location_key]['id']
            area_id = lookups['locations'][location_key]['area_id']
        else:
            result['locations_to_create'].append({
                'customer_id': cust_id,
                'area_id': None,  # No Area field from accounting
                'location_name': lokasi,
                'location_code': f'LOC-{cust_id}-{len(result["locations_to_create"])+1}',
                'address': 'Auto-generated from accounting data',
                'city': lokasi,
                'province': 'N/A'
            })
            location_id = f"@location_{len(result['locations_to_create'])}"
            lookups['locations'][location_key] = {'id': location_id, 'area_id': None}
            result['stats']['new_locations'] += 1
            area_id = None
        
        # Get or create contract
        kontrak_id = None
        if po_number and po_number in lookups['contracts']:
            kontrak_id = lookups['contracts'][po_number]['id']
        elif po_number and po_number not in ['', 'N/A', '-']:
            periode = str(a_row.get('PERIODE KONTRAK/PO', '')).strip()
            start_date = None
            end_date = None
            
            if periode:
                # Parse "01/01/26-31/12/26"
                match = re.match(r'(\d{2}/\d{2}/\d{2,4})\s*-\s*(\d{2}/\d{2}/\d{2,4})', periode)
                if match:
                    start_date = parse_date(match.group(1))
                    end_date = parse_date(match.group(2))
            
            if not start_date:
                start_date = datetime.now().strftime('%Y-%m-%d')
            end_date = calculate_contract_end_date(start_date, end_date)
            
            result['contracts_to_create'].append({
                'customer_location_id': location_id,
                'no_kontrak': po_number,
                'customer_po_number': po_number,
                'tanggal_mulai': start_date,
                'tanggal_berakhir': end_date,
                'status': 'Aktif' if end_date and end_date != calculate_contract_end_date(start_date, None) else 'Pending',
                'rental_type': 'PO_ONLY'
            })
            kontrak_id = f"@contract_{len(result['contracts_to_create'])}"
            lookups['contracts'][po_number] = {'id': kontrak_id, 'location_id': location_id}
            result['stats']['new_contracts'] += 1
        
        result['unit_updates'].append({
            'unit_number': unit_num,
            'customer_id': cust_id,
            'customer_location_id': location_id,
            'kontrak_id': kontrak_id,
            'area_id': area_id,
            'harga_sewa_bulanan': monthly_price,
            'on_hire_date': unit_antar,
            'source': 'accounting_only'
        })
        
        processed_units.add(unit_num)
        result['stats']['added_from_accounting'] += 1
        result['stats']['total_processed'] += 1
    
    print(f"\n📊 Merge complete:")
    print(f"   Total processed:     {result['stats']['total_processed']}")
    print(f"   - Overlap enriched:  {len(overlap_units)}")
    print(f"   - Marketing only:    {len(marketing_only)}")
    print(f"   - Accounting only:   {result['stats']['added_from_accounting']}")
    print(f"   Skipped:             {result['stats']['skipped']}")
    print(f"   New locations:       {result['stats']['new_locations']}")
    print(f"   New contracts:       {result['stats']['new_contracts']}")
    print(f"   Enriched fields:     {result['stats']['enriched']}")
    print(f"   Conflicts logged:    {len(result['conflicts'])}")
    
    return result

# ========== SQL GENERATION ==========

def generate_sql(data, output_file):
    """Generate SQL import script"""
    with open(output_file, 'w', encoding='utf-8') as f:
        f.write("-- OPTIMA ERP - MERGED MARKETING & ACCOUNTING DATA IMPORT\n")
        f.write(f"-- Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write(f"-- Total units to import: {len(data['unit_updates'])}\n\n")
        
        f.write("SET FOREIGN_KEY_CHECKS=0;\n")
        f.write("START TRANSACTION;\n\n")
        
        # STEP 1: Reset overlapping units
        if data['units_to_reset']:
            f.write("-- ========================================\n")
            f.write("-- STEP 1: RESET OVERLAPPING UNITS\n")
            f.write(f"-- Total: {len(data['units_to_reset'])} units\n")
            f.write("-- Reason: CSV/Accounting data is SOURCE OF TRUTH\n")
            f.write("-- ========================================\n\n")
            
            # Batch updates (100 units per batch)
            batch_size = 100
            unit_numbers = [str(u['unit_number']) for u in data['units_to_reset']]
            
            for i in range(0, len(unit_numbers), batch_size):
                batch = unit_numbers[i:i+batch_size]
                f.write(f"-- Reset batch {i//batch_size + 1}\n")
                f.write("UPDATE inventory_unit\n")
                f.write("SET customer_id = NULL,\n")
                f.write("    kontrak_id = NULL,\n")
                f.write("    customer_location_id = NULL,\n")
                f.write("    area_id = NULL,\n")
                f.write("    harga_sewa_bulanan = NULL,\n")
                f.write("    harga_sewa_harian = NULL,\n")
                f.write("    on_hire_date = NULL,\n")
                f.write("    off_hire_date = NULL,\n")
                f.write("    rate_changed_at = NULL\n")
                f.write(f"WHERE no_unit IN ({', '.join(batch)});\n\n")
        
        # STEP 2: Insert new locations
        if data['locations_to_create']:
            f.write("-- ========================================\n")
            f.write("-- STEP 2: INSERT NEW CUSTOMER LOCATIONS\n")
            f.write(f"-- Total: {len(data['locations_to_create'])} locations\n")
            f.write("-- ========================================\n\n")
            
            for idx, loc in enumerate(data['locations_to_create'], 1):
                area_val = loc['area_id'] if loc['area_id'] else 'NULL'
                # Escape all string values
                loc_name = escape_sql_string(loc['location_name'])
                loc_code = escape_sql_string(loc['location_code'])
                address = escape_sql_string(loc['address'])
                city = escape_sql_string(loc['city'])
                province = escape_sql_string(loc['province'])
                f.write(f"-- Location {idx}: {loc['location_name']}\n")
                f.write("INSERT INTO customer_locations ")
                f.write("(customer_id, area_id, location_name, location_code, address, city, province, is_primary, is_active)\n")
                f.write(f"VALUES ({loc['customer_id']}, {area_val}, '{loc_name}', '{loc_code}', ")
                f.write(f"'{address}', '{city}', '{province}', 0, 1);\n")
                f.write(f"SET @location_{idx} = LAST_INSERT_ID();\n\n")
        
        # STEP 3: Insert new contracts
        if data['contracts_to_create']:
            f.write("-- ========================================\n")
            f.write("-- STEP 3: INSERT NEW CONTRACTS\n")
            f.write(f"-- Total: {len(data['contracts_to_create'])} contracts\n")
            f.write("-- ========================================\n\n")
            
            for idx, contract in enumerate(data['contracts_to_create'], 1):
                loc_id = contract['customer_location_id']
                if isinstance(loc_id, str) and loc_id.startswith('@location_'):
                    loc_id = f"@location_{loc_id.split('_')[1]}"
                
                # Escape all string values
                no_kontrak = escape_sql_string(contract['no_kontrak'])
                rental_type = escape_sql_string(contract['rental_type'])
                po_number = escape_sql_string(contract['customer_po_number'])
                tgl_mulai = escape_sql_string(contract['tanggal_mulai'])
                tgl_berakhir = escape_sql_string(contract['tanggal_berakhir']) if contract['tanggal_berakhir'] else 'NULL'
                end_date = f"'{tgl_berakhir}'" if contract['tanggal_berakhir'] else 'NULL'
                status = escape_sql_string(contract['status'])
                
                f.write(f"-- Contract {idx}: {contract['customer_po_number']}\n")
                f.write("INSERT INTO kontrak ")
                
                # Production mode: use production schema (no_po_marketing)
                if PRODUCTION_MODE:
                    f.write("(customer_location_id, no_kontrak, no_po_marketing, ")
                    f.write("tanggal_mulai, tanggal_berakhir, status, total_units)\n")
                    f.write(f"VALUES ({loc_id}, '{no_kontrak}', ")
                    f.write(f"'{po_number}', '{tgl_mulai}', {end_date}, ")
                    f.write(f"'{status}', 0);\n")
                else:
                    # Full schema with rental_type and no_po_marketing
                    f.write("(customer_location_id, no_kontrak, rental_type, no_po_marketing, ")
                    f.write("tanggal_mulai, tanggal_berakhir, status, total_units)\n")
                    f.write(f"VALUES ({loc_id}, '{no_kontrak}', '{rental_type}', ")
                    f.write(f"'{po_number}', '{tgl_mulai}', {end_date}, ")
                    f.write(f"'{status}', 0);\n")
                
                f.write(f"SET @contract_{idx} = LAST_INSERT_ID();\n\n")
        
        # STEP 4: Update inventory units
        f.write("-- ========================================\n")
        f.write("-- STEP 4: UPDATE INVENTORY UNITS\n")
        f.write(f"-- Total: {len(data['unit_updates'])} units\n")
        f.write("-- ========================================\n\n")
        
        for idx, unit in enumerate(data['unit_updates'], 1):
            loc_id = unit['customer_location_id']
            if isinstance(loc_id, str) and loc_id.startswith('@location_'):
                loc_id = f"@location_{loc_id.split('_')[1]}"
            
            kontrak_id = unit['kontrak_id']
            if kontrak_id:
                if isinstance(kontrak_id, str) and kontrak_id.startswith('@contract_'):
                    kontrak_id = f"@contract_{kontrak_id.split('_')[1]}"
            else:
                kontrak_id = 'NULL'
            
            area_id = unit['area_id'] if unit['area_id'] else 'NULL'
            price = unit['harga_sewa_bulanan'] if unit['harga_sewa_bulanan'] else 'NULL'
            hire_date = f"'{unit['on_hire_date']}'" if unit['on_hire_date'] else 'NULL'
            
            f.write(f"UPDATE inventory_unit SET\n")
            f.write(f"  customer_id = {unit['customer_id']},\n")
            f.write(f"  customer_location_id = {loc_id},\n")
            f.write(f"  kontrak_id = {kontrak_id},\n")
            f.write(f"  area_id = {area_id},\n")
            f.write(f"  harga_sewa_bulanan = {price},\n")
            f.write(f"  on_hire_date = {hire_date},\n")
            f.write(f"  rate_changed_at = NOW()\n")
            f.write(f"WHERE no_unit = {unit['unit_number']};\n")
            
            if idx % 100 == 0:
                f.write(f"\n-- Processed {idx}/{len(data['unit_updates'])} units\n\n")
        
        # STEP 5: Create customer_contracts links
        f.write("\n-- ========================================\n")
        f.write("-- STEP 5: CREATE CUSTOMER_CONTRACTS LINKS\n")
        f.write("-- ========================================\n\n")
        
        f.write("INSERT IGNORE INTO customer_contracts (customer_id, kontrak_id, is_active)\n")
        f.write("SELECT DISTINCT iu.customer_id, iu.kontrak_id, 1\n")
        f.write("FROM inventory_unit iu\n")
        f.write("WHERE iu.kontrak_id IS NOT NULL\n")
        f.write("  AND iu.customer_id IS NOT NULL\n")
        f.write("  AND NOT EXISTS (\n")
        f.write("    SELECT 1 FROM customer_contracts cc\n")
        f.write("    WHERE cc.customer_id = iu.customer_id\n")
        f.write("      AND cc.kontrak_id = iu.kontrak_id\n")
        f.write("  );\n\n")
        
        # STEP 6: Update contract totals
        f.write("-- ========================================\n")
        f.write("-- STEP 6: UPDATE CONTRACT TOTAL UNITS\n")
        f.write("-- ========================================\n\n")
        
        f.write("UPDATE kontrak k\n")
        f.write("SET total_units = (\n")
        f.write("  SELECT COUNT(*)\n")
        f.write("  FROM inventory_unit iu\n")
        f.write("  WHERE iu.kontrak_id = k.id\n")
        f.write(");\n\n")
        
        f.write("COMMIT;\n")
        f.write("SET FOREIGN_KEY_CHECKS=1;\n\n")
        f.write("-- ========================================\n")
        f.write("-- MIGRATION COMPLETE\n")
        f.write("-- ========================================\n")
    
    print(f"✅ SQL script generated: {output_file}")

# ========== REPORT GENERATION ==========

def generate_reports(data, output_files):
    """Generate merge reports"""
    
    # Main report
    with open(output_files['report'], 'w', encoding='utf-8') as f:
        f.write("OPTIMA ERP - MARKETING & ACCOUNTING DATA MERGE REPORT\n")
        f.write("=" * 80 + "\n")
        f.write(f"Generated: {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n\n")
        
        f.write("SUMMARY\n")
        f.write("-" * 80 + "\n")
        f.write(f"Base from marketing_fix.csv:     {data['stats']['base_marketing']} units\n")
        f.write(f"Base from data_from_acc.csv:     {data['stats']['base_accounting']} units\n")
        f.write(f"Overlap (enriched):              {data['stats']['overlap']} units\n")
        f.write(f"Added from accounting only:      {data['stats']['added_from_accounting']} units\n")
        f.write(f"Total units processed:           {data['stats']['total_processed']}\n")
        f.write(f"Skipped (errors):                {data['stats']['skipped']}\n\n")
        
        f.write("ENRICHMENT STATS\n")
        f.write("-" * 80 + "\n")
        f.write(f"Fields enriched from accounting: {data['stats']['enriched']}\n")
        f.write(f"New locations created:           {data['stats']['new_locations']}\n")
        f.write(f"New contracts created:           {data['stats']['new_contracts']}\n")
        f.write(f"Units to be reset (DB overlap):  {data['stats']['units_reset']}\n")
        f.write(f"Data conflicts logged:           {len(data['conflicts'])}\n")
        f.write(f"Missing customer mappings:       {len(data['missing_customers'])}\n\n")
        
        f.write("MERGE STRATEGY\n")
        f.write("-" * 80 + "\n")
        f.write("1. Start with marketing_fix.csv as BASE (has cust_id & Area)\n")
        f.write("2. For overlap units: ENRICH with data_from_acc (better data)\n")
        f.write("   - Lokasi: Use accounting (more detailed)\n")
        f.write("   - Harga: Use accounting (more current)\n")
        f.write("   - PO: Use accounting (from official records)\n")
        f.write("3. Add units only in data_from_acc (match customer by name)\n")
        f.write("4. Final output: ~2,100 units with best data from both sources\n\n")
        
        f.write("EXPECTED FINAL STATE\n")
        f.write("-" * 80 + "\n")
        f.write(f"Total assigned units:  {data['stats']['total_processed']} (~42% of inventory)\n")
        f.write(f"Unassigned units:      ~{4989 - data['stats']['total_processed']} (~58% spare/stock)\n\n")
        
        if data['missing_customers']:
            f.write("WARNING: MISSING CUSTOMER MAPPINGS\n")
            f.write("-" * 80 + "\n")
            f.write(f"{len(data['missing_customers'])} units from accounting could not be matched to customers.\n")
            f.write(f"See {output_files['missing_customers']} for details.\n\n")
        
        if data['conflicts']:
            f.write("DATA CONFLICTS RESOLVED\n")
            f.write("-" * 80 + "\n")
            f.write(f"{len(data['conflicts'])} conflicts found and resolved (accounting data used).\n")
            f.write(f"See {output_files['conflicts']} for details.\n\n")
    
    # Missing customers report
    if data['missing_customers']:
        df_missing = pd.DataFrame(data['missing_customers'])
        df_missing.to_csv(output_files['missing_customers'], index=False)
        print(f"⚠️  Missing customers report: {output_files['missing_customers']}")
    
    # Conflicts report
    if data['conflicts']:
        df_conflicts = pd.DataFrame(data['conflicts'])
        df_conflicts.to_csv(output_files['conflicts'], index=False)
        print(f"⚠️  Conflicts report: {output_files['conflicts']}")
    
    print(f"✅ Main report generated: {output_files['report']}")

# ========== MAIN EXECUTION ==========

def main():
    """Main execution function"""
    print("\n" + "=" * 80)
    print("OPTIMA ERP - MARKETING & ACCOUNTING DATA MERGER")
    print("=" * 80 + "\n")
    
    try:
        # Load CSV files
        print("📂 Loading CSV files...")
        df_marketing = pd.read_csv(CSV_FILES['marketing'], delimiter=';')
        print(f"✅ Loaded {len(df_marketing)} rows from {CSV_FILES['marketing']}")
        
        df_accounting = pd.read_csv(CSV_FILES['accounting'], delimiter=';')
        print(f"✅ Loaded {len(df_accounting)} rows from {CSV_FILES['accounting']}")
        
        # Connect to database
        print("\n🔌 Connecting to database...")
        conn = connect_database()
        
        # Build lookups
        print("\n📋 Building lookup tables...")
        lookups = build_lookups(conn)
        
        # Merge data
        print("\n🔀 Merging marketing and accounting data...")
        merged_data = merge_data(df_marketing, df_accounting, lookups)
        
        # Generate SQL
        print(f"\n📝 Generating SQL script...")
        generate_sql(merged_data, OUTPUT_FILES['sql'])
        
        # Generate reports
        print(f"\n📊 Generating reports...")
        generate_reports(merged_data, OUTPUT_FILES)
        
        # Close database connection
        conn.close()
        
        print("\n" + "=" * 80)
        print("✅ MERGE COMPLETE!")
        print("=" * 80)
        print(f"\nGenerated files:")
        print(f"  1. {OUTPUT_FILES['sql']} - SQL import script")
        print(f"  2. {OUTPUT_FILES['report']} - Detailed merge report")
        if merged_data['missing_customers']:
            print(f"  3. {OUTPUT_FILES['missing_customers']} - Missing customer mappings")
        if merged_data['conflicts']:
            print(f"  4. {OUTPUT_FILES['conflicts']} - Data conflicts log")
        
        print(f"\n📊 Summary:")
        print(f"  Total units to import: {merged_data['stats']['total_processed']}")
        print(f"  - From overlap (enriched): {merged_data['stats']['overlap']}")
        print(f"  - From marketing only: {merged_data['stats']['base_marketing'] - merged_data['stats']['overlap']}")
        print(f"  - From accounting only: {merged_data['stats']['added_from_accounting']}")
        print(f"  New locations: {merged_data['stats']['new_locations']}")
        print(f"  New contracts: {merged_data['stats']['new_contracts']}")
        print(f"  Enriched fields: {merged_data['stats']['enriched']}")
        
        print(f"\n⚠️  Next steps:")
        print(f"  1. Review {OUTPUT_FILES['report']}")
        print(f"  2. Check {OUTPUT_FILES['sql']} for accuracy")
        print(f"  3. Backup database: mysqldump optima_ci > backup.sql")
        print(f"  4. Execute: mysql -u root optima_ci < {OUTPUT_FILES['sql']}")
        print(f"  5. Validate: mysql -u root optima_ci < POST_MIGRATION_VALIDATION.sql")
        
    except Exception as e:
        print(f"\n❌ Error: {e}")
        import traceback
        traceback.print_exc()
        return 1
    
    return 0

if __name__ == '__main__':
    exit(main())
