#!/usr/bin/env python3
"""
Script to check if all trigger_events in notification_rules have corresponding implementations
"""

import re
from collections import defaultdict

# Read trigger events from database (from temp file)
with open(r'C:\laragon\www\optima\temp_trigger_events_db.txt', 'r') as f:
    db_triggers = set(line.strip() for line in f if line.strip())

print(f"📊 Total trigger_events in database: {len(db_triggers)}")
print("="*80)

# Read notification_helper.php to find implementations
with open(r'c:\laragon\www\optima\app\Helpers\notification_helper.php', 'r', encoding='utf-8') as f:
    helper_content = f.read()

# Find all send_notification() calls with event type
send_notification_pattern = r"send_notification\('([^']+)',"
implementations = set(re.findall(send_notification_pattern, helper_content))

print(f"📝 Total implementations found: {len(implementations)}")
print("="*80)

# Find trigger_events WITHOUT implementations
missing_implementations = db_triggers - implementations

# Find implementations WITHOUT database trigger_events (orphaned)
orphaned_implementations = implementations - db_triggers

# Categorize missing implementations
categories = defaultdict(list)
for trigger in sorted(missing_implementations):
    if trigger.startswith('attachment_'):
        categories['Attachment Management'].append(trigger)
    elif trigger.startswith('inventory_unit_'):
        categories['Inventory Unit'].append(trigger)
    elif trigger.startswith('sparepart_'):
        categories['Sparepart'].append(trigger)
    elif trigger.startswith('pmps_'):
        categories['PMPS (Preventive Maintenance)'].append(trigger)
    elif trigger.startswith('po_'):
        categories['Purchase Order'].append(trigger)
    elif trigger.startswith('employee_'):
        categories['Employee Assignment'].append(trigger)
    elif trigger.startswith('di_'):
        categories['Delivery Instruction (DI)'].append(trigger)
    elif trigger.startswith('delivery_'):
        categories['Delivery'].append(trigger)
    elif trigger.startswith('invoice_'):
        categories['Invoice'].append(trigger)
    elif trigger.startswith('supplier_'):
        categories['Supplier'].append(trigger)
    elif trigger.startswith('user_'):
        categories['User Management'].append(trigger)
    elif trigger.startswith('role_'):
        categories['Role Management'].append(trigger)
    elif trigger.startswith('permission_'):
        categories['Permission Management'].append(trigger)
    elif trigger.startswith('password_'):
        categories['Password/Auth'].append(trigger)
    elif trigger.startswith('spk_'):
        categories['SPK'].append(trigger)
    elif trigger.startswith('quotation_'):
        categories['Quotation'].append(trigger)
    elif trigger.startswith('customer_'):
        categories['Customer'].append(trigger)
    elif trigger.startswith('unit_'):
        categories['Unit Management'].append(trigger)
    elif trigger.startswith('work_order_'):
        categories['Work Order'].append(trigger)
    elif trigger.startswith('workorder_'):
        categories['WorkOrder (New)'].append(trigger)
    elif trigger.startswith('service_'):
        categories['Service Assignment'].append(trigger)
    elif trigger.startswith('warehouse_'):
        categories['Warehouse'].append(trigger)
    else:
        categories['Others'].append(trigger)

# Print results
print("\n🚨 TRIGGER EVENTS MISSING IMPLEMENTATIONS:")
print("="*80)
if missing_implementations:
    for category, triggers in sorted(categories.items()):
        if triggers:
            print(f"\n📁 {category} ({len(triggers)} events):")
            for trigger in triggers:
                print(f"   ❌ {trigger}")
    print(f"\n❗ TOTAL MISSING: {len(missing_implementations)}")
else:
    print("✅ All trigger events have implementations!")

print("\n" + "="*80)
print("\n⚠️  ORPHANED IMPLEMENTATIONS (no database rule):")
print("="*80)
if orphaned_implementations:
    for impl in sorted(orphaned_implementations):
        print(f"   ⚠️  {impl}")
    print(f"\n❗ TOTAL ORPHANED: {len(orphaned_implementations)}")
else:
    print("✅ No orphaned implementations found!")

# Summary
print("\n" + "="*80)
print("📊 SUMMARY:")
print("="*80)
print(f"Database trigger_events:     {len(db_triggers)}")
print(f"Implemented functions:       {len(implementations)}")
print(f"Missing implementations:     {len(missing_implementations)}")
print(f"Orphaned implementations:    {len(orphaned_implementations)}")

# Check if there are any with both issues
both_issues = missing_implementations.intersection(orphaned_implementations)
if both_issues:
    print(f"⚠️  Both missing AND orphaned: {len(both_issues)}")

print("\n✨ Analysis complete!")
