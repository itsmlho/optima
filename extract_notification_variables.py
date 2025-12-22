#!/usr/bin/env python3
"""
Generate standardized Available Variables from updated notification helper functions
Extracts ALL variables actually provided by each function
"""

import re
import json
from collections import defaultdict

# Read the updated notification helper
with open('app/Helpers/notification_helper.php', 'r', encoding='utf-8') as f:
    content = f.read()

def extract_variables_from_function(function_name):
    """Extract variables array from a notification helper function"""
    # Pattern to match the function and extract the array passed to send_notification
    pattern = rf'function\s+{re.escape(function_name)}\s*\([^)]*\)\s*{{[^}}]*?return\s+send_notification\s*\([^,]+,\s*\[(.*?)\]\s*\);'
    
    match = re.search(pattern, content, re.DOTALL)
    if not match:
        return []
    
    array_content = match.group(1)
    
    # Extract all 'key' => patterns
    var_pattern = r"'([a-z_0-9]+)'\s*=>"
    variables = re.findall(var_pattern, array_content)
    
    # Remove duplicates while preserving order
    seen = set()
    unique_vars = []
    for var in variables:
        if var not in seen and var not in ['module']:  # Exclude internal vars
            seen.add(var)
            unique_vars.append(var)
    
    return unique_vars

# List of all notification functions (extracted from helper file)
all_functions = re.findall(r'function\s+(notify_\w+)\s*\(', content)
all_functions = list(dict.fromkeys(all_functions))  # Remove duplicates

print(f"Found {len(all_functions)} notification functions")

# Extract variables for each function
notification_data = {}
for func in all_functions:
    # Convert notify_something to something (trigger event name)
    event_name = func.replace('notify_', '')
    variables = extract_variables_from_function(func)
    
    if variables:
        notification_data[event_name] = {
            'variables': variables,
            'count': len(variables)
        }
        print(f"✓ {event_name}: {len(variables)} variables")
    else:
        print(f"✗ {event_name}: No variables found")

# Add variable descriptions and categories
variable_info = {
    # Core
    'url': 'Link to detail page',
    'performed_by': 'Username who performed action',
    'performed_at': 'Timestamp of action',
    'created_by': 'Username who created',
    'updated_by': 'Username who updated',
    'assigned_by': 'Username who assigned',
    'approved_by': 'Username who approved',
    'completed_by': 'Username who completed',
    'created_at': 'Creation timestamp',
    'updated_at': 'Last update timestamp',
    'completed_at': 'Completion timestamp',
    
    # Unit (STANDARDIZED)
    'no_unit': 'Unit number (STANDARD)',
    'unit_code': 'Unit code (alias of no_unit)',
    'unit_no': 'Unit number (alias of no_unit)',
    'unit_id': 'Unit database ID',
    'unit_model': 'Unit model/type',
    'unit_type': 'Type of unit',
    
    # Customer (STANDARDIZED)
    'customer': 'Customer name (STANDARD)',
    'customer_name': 'Customer name (alias)',
    'customer_id': 'Customer database ID',
    'customer_code': 'Customer code',
    
    # Division
    'departemen': 'Department/Division name',
    'division': 'Division name',
    
    # Attachment
    'attachment_info': 'Full attachment details (merk model type)',
    'attachment_id': 'Attachment database ID',
    'tipe_item': 'Attachment type (charger/baterai/attachment)',
    'serial_number': 'Attachment serial number',
    'sn': 'Serial number (alias)',
    
    # Delivery
    'nomor_delivery': 'Delivery number (STANDARD)',
    'delivery_number': 'Delivery number (alias)',
    'delivery_date': 'Scheduled delivery date',
    'delivery_id': 'Delivery database ID',
    
    # SPK/Work Order
    'nomor_spk': 'SPK number',
    'spk_number': 'SPK number (alias)',
    'spk_id': 'SPK database ID',
    'wo_number': 'Work Order number',
    'wo_id': 'Work Order database ID',
    'workorder_number': 'Work Order number (alias)',
    
    # Purchase Order
    'po_number': 'PO number',
    'po_id': 'PO database ID',
    'supplier_name': 'Supplier name',
    
    # Invoice & Payment
    'invoice_number': 'Invoice number',
    'invoice_amount': 'Invoice total amount',
    'payment_amount': 'Payment amount',
    'payment_method': 'Payment method',
    
    # Sparepart
    'sparepart_name': 'Sparepart name (STANDARD)',
    'nama_sparepart': 'Sparepart name (alias)',
    'sparepart_code': 'Sparepart code',
    'quantity': 'Quantity (STANDARD)',
    'qty': 'Quantity (alias)',
    
    # Dates
    'due_date': 'Due date',
    'start_date': 'Start date',
    'end_date': 'End date',
    'scheduled_date': 'Scheduled date',
    
    # PMPS
    'pmps_type': 'PMPS type',
    'interval_days': 'Interval in days',
    'last_service_date': 'Last service date',
    
    # Status
    'status': 'Current status',
    'old_status': 'Previous status',
    'new_status': 'New status',
    'reason': 'Reason for change',
    'notes': 'Additional notes',
    'remarks': 'Remarks',
    
    # Quotation
    'quotation_number': 'Quotation number',
    'quotation_id': 'Quotation database ID',
    'total_amount': 'Total amount',
    'valid_until': 'Valid until date',
}

# Categorize variables
categories = {
    'Core System': ['url', 'performed_by', 'performed_at', 'created_by', 'updated_by', 'assigned_by', 'approved_by', 'completed_by', 'created_at', 'updated_at', 'completed_at'],
    'Unit (Standardized)': ['no_unit', 'unit_code', 'unit_no', 'unit_id', 'unit_model', 'unit_type'],
    'Customer (Standardized)': ['customer', 'customer_name', 'customer_id', 'customer_code'],
    'Division': ['departemen', 'division'],
    'Attachment': ['attachment_info', 'attachment_id', 'tipe_item', 'serial_number', 'sn'],
    'Delivery': ['nomor_delivery', 'delivery_number', 'delivery_date', 'delivery_id'],
    'SPK & Work Order': ['nomor_spk', 'spk_number', 'spk_id', 'wo_number', 'wo_id', 'workorder_number'],
    'Purchase Order': ['po_number', 'po_id', 'supplier_name'],
    'Invoice & Payment': ['invoice_number', 'invoice_amount', 'payment_amount', 'payment_method'],
    'Sparepart (Standardized)': ['sparepart_name', 'nama_sparepart', 'sparepart_code', 'quantity', 'qty'],
    'Dates': ['due_date', 'start_date', 'end_date', 'scheduled_date'],
    'Status & Notes': ['status', 'old_status', 'new_status', 'reason', 'notes', 'remarks'],
}

# Generate final JSON output
output = {
    'generated_at': '2025-12-22',
    'total_events': len(notification_data),
    'events': notification_data,
    'variable_descriptions': variable_info,
    'variable_categories': categories,
    'standards': {
        'unit_field': 'no_unit (use this instead of unit_code/unit_no)',
        'customer_field': 'customer (use this instead of customer_name)',
        'quantity_field': 'quantity (use this instead of qty)',
        'sparepart_field': 'sparepart_name (use this instead of nama_sparepart)',
        'delivery_field': 'nomor_delivery (use this instead of delivery_number)'
    }
}

# Save to JSON file
with open('public/assets/data/notification_variables.json', 'w', encoding='utf-8') as f:
    json.dump(output, f, indent=2, ensure_ascii=False)

# Generate summary
print("\n" + "="*60)
print("✅ STANDARDIZED VARIABLES GENERATED")
print("="*60)
print(f"Total Events: {len(notification_data)}")
print(f"Total Unique Variables: {len(set(v for data in notification_data.values() for v in data['variables']))}")
print(f"Output: public/assets/data/notification_variables.json")

# Show top 10 most common variables
all_vars = []
for data in notification_data.values():
    all_vars.extend(data['variables'])

from collections import Counter
var_counts = Counter(all_vars)
print("\n📊 Top 10 Most Common Variables:")
for var, count in var_counts.most_common(10):
    print(f"   {var}: used in {count} events")

# Show events by category
event_categories = defaultdict(list)
for event, data in notification_data.items():
    # Categorize by event prefix
    if event.startswith('attachment_'):
        event_categories['Attachment'].append(event)
    elif event.startswith('delivery_'):
        event_categories['Delivery'].append(event)
    elif event.startswith('spk_'):
        event_categories['SPK'].append(event)
    elif event.startswith('work_order_') or event.startswith('workorder_'):
        event_categories['Work Order'].append(event)
    elif event.startswith('pmps_'):
        event_categories['PMPS'].append(event)
    elif event.startswith('po_'):
        event_categories['Purchase Order'].append(event)
    elif event.startswith('sparepart_'):
        event_categories['Sparepart'].append(event)
    elif event.startswith('invoice_'):
        event_categories['Invoice'].append(event)
    elif event.startswith('payment_'):
        event_categories['Payment'].append(event)
    elif event.startswith('di_'):
        event_categories['DI'].append(event)
    elif event.startswith('customer_'):
        event_categories['Customer'].append(event)
    elif event.startswith('quotation_'):
        event_categories['Quotation'].append(event)
    elif event.startswith('user_'):
        event_categories['User'].append(event)
    elif event.startswith('inventory_'):
        event_categories['Inventory'].append(event)
    else:
        event_categories['Other'].append(event)

print("\n📁 Events by Category:")
for cat, events in sorted(event_categories.items()):
    print(f"   {cat}: {len(events)} events")

print("\n✨ Done! Update admin_panel.php to load this file.")
