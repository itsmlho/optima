#!/usr/bin/env python3
"""
Generate standardized Available Variables for notification admin panel
Extracts variables from updated helper functions
"""

import re
import json
from collections import defaultdict

# Read updated notification helper
with open('app/Helpers/notification_helper.php', 'r', encoding='utf-8') as f:
    helper_content = f.read()

def extract_function_variables(trigger_event):
    """Extract variables sent by notification helper function"""
    # Find the function
    patterns = [
        rf'function notify_{trigger_event}\s*\([^)]+\)\s*{{\s*return send_notification\([^,]+,\s*\[(.*?)\]\s*\);',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, helper_content, re.DOTALL)
        if match:
            array_content = match.group(1)
            # Extract all 'key' => patterns
            variables = re.findall(r"'(\w+)'\s*=>", array_content)
            return list(dict.fromkeys(variables))  # Remove duplicates, preserve order
    
    return []

# Get all trigger events from database export
with open('notification_templates_db.txt', 'r', encoding='utf-8') as f:
    db_lines = f.read().strip().split('\n')[1:]  # Skip header

available_vars = {}
categories = defaultdict(list)

# Category definitions
category_map = {
    'attachment': ['attachment_added', 'attachment_attached', 'attachment_detached', 'attachment_swapped', 'attachment_broken', 'attachment_maintenance'],
    'delivery': ['delivery_created', 'delivery_assigned', 'delivery_in_transit', 'delivery_arrived', 'delivery_completed', 'delivery_delayed', 'delivery_status_changed'],
    'invoice': ['invoice_created', 'invoice_sent', 'invoice_paid', 'invoice_overdue'],
    'payment': ['payment_received', 'payment_status_updated', 'payment_overdue'],
    'pmps': ['pmps_due_soon', 'pmps_overdue', 'pmps_completed'],
    'spk': ['spk_created', 'spk_assigned', 'spk_cancelled', 'spk_completed', 'spk_fabrication_completed', 'spk_pdi_completed', 'spk_unit_prep_completed'],
    'work_order': ['work_order_created', 'work_order_assigned', 'work_order_in_progress', 'work_order_completed', 'work_order_cancelled', 'workorder_created', 'workorder_status_changed', 'workorder_assigned', 'workorder_completed', 'workorder_delayed', 'workorder_sparepart_added'],
    'po': ['po_created', 'po_approved', 'po_rejected', 'po_received', 'po_verified', 'po_unit_created', 'po_sparepart_created', 'po_attachment_created', 'po_verification_updated'],
    'sparepart': ['sparepart_added', 'sparepart_used', 'sparepart_low_stock', 'sparepart_out_of_stock', 'sparepart_returned'],
    'di': ['di_created', 'di_submitted', 'di_approved', 'di_in_progress', 'di_delivered', 'di_cancelled'],
    'customer': ['customer_created', 'customer_updated', 'customer_deleted', 'customer_location_added', 'customer_status_changed', 'customer_contract_created', 'customer_contract_expired'],
    'quotation': ['quotation_created', 'quotation_updated', 'quotation_approved', 'quotation_rejected', 'quotation_sent_to_customer', 'quotation_follow_up_required'],
    'user': ['user_created', 'user_updated', 'user_deleted', 'user_activated', 'user_deactivated', 'password_reset'],
    'inventory': ['inventory_unit_added', 'inventory_unit_status_changed', 'inventory_unit_rental_active', 'inventory_unit_returned', 'inventory_unit_maintenance', 'inventory_unit_low_stock'],
    'other': []
}

for line in db_lines:
    if not line.strip():
        continue
    
    parts = line.split('\t')
    if len(parts) < 2:
        continue
    
    trigger_event = parts[1]
    variables = extract_function_variables(trigger_event)
    
    if variables:
        # Remove internal variables
        variables = [v for v in variables if v not in ['module', 'id']]
        available_vars[trigger_event] = variables
        
        # Categorize
        found_cat = False
        for cat, events in category_map.items():
            if trigger_event in events:
                categories[cat].append(trigger_event)
                found_cat = True
                break
        if not found_cat:
            categories['other'].append(trigger_event)

# Generate JavaScript object for admin panel
js_output = "// Auto-generated: Standardized Available Variables\n"
js_output += "// Generated: 2025-12-22\n\n"
js_output += "const availableVariables = {\n"

for event, vars in sorted(available_vars.items()):
    js_output += f"    '{event}': {json.dumps(vars)},\n"

js_output += "};\n\n"

# Generate grouped view
js_output += "// Grouped by category\n"
js_output += "const variablesByCategory = {\n"

for cat, events in sorted(categories.items()):
    if not events:
        continue
    js_output += f"    '{cat}': {{\n"
    for event in sorted(events):
        if event in available_vars:
            js_output += f"        '{event}': {json.dumps(available_vars[event])},\n"
    js_output += "    },\n"

js_output += "};\n\n"

# Generate common variables
common_vars = {
    'Core': ['module', 'url'],
    'User Actions': ['performed_by', 'performed_at', 'created_by', 'updated_by', 'assigned_by', 'approved_by', 'completed_by'],
    'Unit Info': ['no_unit', 'unit_id', 'unit_code', 'unit_model', 'unit_type'],
    'Customer Info': ['customer_name', 'customer', 'customer_id', 'customer_code'],
    'Dates': ['created_at', 'updated_at', 'completed_at', 'due_date', 'delivery_date', 'start_date', 'end_date'],
    'Identifiers': ['attachment_id', 'spk_id', 'wo_id', 'po_id', 'di_id', 'quotation_id', 'invoice_number'],
    'Division': ['departemen'],
    'Attachment Info': ['tipe_item', 'attachment_info', 'serial_number', 'sn'],
    'Quantity & Amount': ['quantity', 'qty', 'amount', 'total_amount'],
}

js_output += "// Common variable groups\n"
js_output += "const commonVariables = " + json.dumps(common_vars, indent=4) + ";\n\n"

# Statistics
js_output += "// Statistics\n"
js_output += f"// Total events: {len(available_vars)}\n"
js_output += f"// Total unique variables: {len(set(v for vars in available_vars.values() for v in vars))}\n"
js_output += f"// Categories: {len([c for c in categories.values() if c])}\n"

# Save to file
with open('public/assets/js/notification-variables.js', 'w', encoding='utf-8') as f:
    f.write(js_output)

# Also save JSON for API
with open('public/assets/data/notification-variables.json', 'w', encoding='utf-8') as f:
    json.dump({
        'variables': available_vars,
        'categories': dict(categories),
        'common': common_vars,
        'generated_at': '2025-12-22'
    }, f, indent=2, ensure_ascii=False)

print("✅ Generated standardized variable definitions:")
print(f"   - JavaScript: public/assets/js/notification-variables.js")
print(f"   - JSON: public/assets/data/notification-variables.json")
print(f"   - Total events: {len(available_vars)}")
print(f"   - Total unique variables: {len(set(v for vars in available_vars.values() for v in vars))}")
