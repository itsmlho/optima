#!/usr/bin/env python3
"""
Audit Trigger Events vs Implementation Functions
Membandingkan trigger_event di database dengan fungsi notify_* yang tersedia
"""

import re
from pathlib import Path

# Daftar trigger_events dari notification_rules (berdasarkan SQL dump)
DATABASE_TRIGGERS = [
    'di_submitted',
    'purchase_order_created',
    'spk_created',
    'customer_created',
    'customer_updated',
    'customer_deleted',
    'customer_location_added',
    'customer_contract_created',
    'customer_contract_expired',
    'di_created',
    'di_approved',
    'di_in_progress',
    'di_delivered',
    'di_cancelled',
    'quotation_created',
    'unit_prep_started',
    'unit_prep_completed',
    'work_order_created',
    'work_order_assigned',
    'work_order_in_progress',
    'work_order_completed',
    'work_order_cancelled',
    'pmps_due_soon',
    'pmps_overdue',
    'pmps_completed',
    'employee_assigned',
    'employee_unassigned',
    'inventory_unit_added',
    'inventory_unit_status_changed',
    'inventory_unit_rental_active',
    'inventory_unit_returned',
    'inventory_unit_maintenance',
    'inventory_unit_low_stock',
    'attachment_added',
    'attachment_attached',
    'attachment_detached',
    'attachment_swapped',
    'attachment_maintenance',
    'attachment_broken',
    'sparepart_added',
    'sparepart_used',
    'sparepart_low_stock',
    'sparepart_out_of_stock',
    'po_unit_created',
    'po_attachment_created',
    'po_sparepart_created',
    'po_approved',
    'po_rejected',
    'po_received',
    'po_verified',
    'supplier_created',
    'supplier_updated',
    'supplier_deleted',
    'delivery_created',
    'delivery_assigned',
    'delivery_in_transit',
    'delivery_arrived',
    'delivery_completed',
    'delivery_delayed',
    'invoice_created',
    'invoice_sent',
    'invoice_paid',
    'invoice_overdue',
    'payment_received',
    'user_created',
    'user_updated',
    'user_deleted',
    'user_activated',
    'user_deactivated',
    'password_reset',
    'role_created',
    'role_updated',
    'permission_changed',
    'spk_assigned',
    'spk_cancelled',
    'po_created',  # Duplicate name, different context
    'attachment_uploaded',  # From workorder stages
]

def scan_notification_helper():
    """Scan notification_helper.php untuk menemukan semua fungsi notify_*"""
    helper_file = Path(r"c:\laragon\www\optima\app\Helpers\notification_helper.php")
    
    if not helper_file.exists():
        print(f"[ERROR] File tidak ditemukan: {helper_file}")
        return []
    
    content = helper_file.read_text(encoding='utf-8')
    
    # Regex untuk menemukan fungsi notify_*
    pattern = r"function\s+(notify_[a-z_]+)\s*\("
    matches = re.findall(pattern, content)
    
    return matches

def map_trigger_to_function(trigger):
    """
    Mapping trigger_event ke expected function name
    Rule: trigger_event -> notify_{trigger_event}
    """
    return f"notify_{trigger}"

def main():
    print("=" * 80)
    print("AUDIT: TRIGGER EVENTS vs IMPLEMENTATION FUNCTIONS")
    print("=" * 80)
    print()
    
    # Scan fungsi yang ada
    print("[1] Scanning notification_helper.php...")
    implemented_functions = scan_notification_helper()
    print(f"    Found {len(implemented_functions)} notify_* functions")
    print()
    
    # Unique trigger events
    unique_triggers = sorted(set(DATABASE_TRIGGERS))
    print(f"[2] Database has {len(unique_triggers)} unique trigger_events")
    print()
    
    # Mapping
    print("[3] Comparing triggers vs implementations...")
    print()
    
    missing = []
    implemented = []
    
    for trigger in unique_triggers:
        expected_func = map_trigger_to_function(trigger)
        if expected_func in implemented_functions:
            implemented.append(trigger)
        else:
            missing.append(trigger)
    
    # REPORT
    print("=" * 80)
    print(f"SUMMARY")
    print("=" * 80)
    print(f"Total Trigger Events:       {len(unique_triggers)}")
    print(f"Implemented:                {len(implemented)} ({len(implemented)/len(unique_triggers)*100:.1f}%)")
    print(f"MISSING Implementation:     {len(missing)} ({len(missing)/len(unique_triggers)*100:.1f}%)")
    print()
    
    # Detail Missing
    if missing:
        print("=" * 80)
        print(f"MISSING IMPLEMENTATIONS ({len(missing)} events)")
        print("=" * 80)
        
        # Kategorisasi
        categories = {
            'Customer Management': [],
            'Delivery/DI': [],
            'Purchase Order': [],
            'Work Order': [],
            'Inventory': [],
            'Sparepart': [],
            'Attachment': [],
            'Invoice/Payment': [],
            'User/Role': [],
            'SPK/Quotation': [],
            'PMPS': [],
            'Employee': [],
            'Supplier': [],
            'Other': []
        }
        
        for trigger in missing:
            if 'customer' in trigger:
                categories['Customer Management'].append(trigger)
            elif 'di_' in trigger or 'delivery' in trigger:
                categories['Delivery/DI'].append(trigger)
            elif 'po_' in trigger or 'purchase_order' in trigger:
                categories['Purchase Order'].append(trigger)
            elif 'work_order' in trigger:
                categories['Work Order'].append(trigger)
            elif 'inventory' in trigger:
                categories['Inventory'].append(trigger)
            elif 'sparepart' in trigger:
                categories['Sparepart'].append(trigger)
            elif 'attachment' in trigger:
                categories['Attachment'].append(trigger)
            elif 'invoice' in trigger or 'payment' in trigger:
                categories['Invoice/Payment'].append(trigger)
            elif 'user_' in trigger or 'role_' in trigger or 'permission' in trigger or 'password' in trigger:
                categories['User/Role'].append(trigger)
            elif 'spk_' in trigger or 'quotation' in trigger:
                categories['SPK/Quotation'].append(trigger)
            elif 'pmps' in trigger:
                categories['PMPS'].append(trigger)
            elif 'employee' in trigger:
                categories['Employee'].append(trigger)
            elif 'supplier' in trigger:
                categories['Supplier'].append(trigger)
            else:
                categories['Other'].append(trigger)
        
        for category, items in categories.items():
            if items:
                print(f"\n[{category}] - {len(items)} missing:")
                for item in items:
                    print(f"  - {item}")
                    print(f"    Expected function: notify_{item}()")
    
    # Implemented list
    if implemented:
        print()
        print("=" * 80)
        print(f"IMPLEMENTED ({len(implemented)} events)")
        print("=" * 80)
        for trigger in implemented:
            print(f"  [OK] {trigger} -> notify_{trigger}()")
    
    # Orphaned functions
    print()
    print("=" * 80)
    print("CHECKING FOR ORPHANED FUNCTIONS")
    print("=" * 80)
    orphaned = []
    for func in implemented_functions:
        # Extract trigger name from function name
        if func.startswith('notify_'):
            trigger = func.replace('notify_', '')
            if trigger not in unique_triggers:
                orphaned.append(func)
    
    if orphaned:
        print(f"Found {len(orphaned)} functions without database rules:")
        for func in orphaned:
            print(f"  - {func} (no matching trigger_event in database)")
    else:
        print("No orphaned functions found. All functions have matching rules.")
    
    print()
    print("=" * 80)
    print("AUDIT COMPLETE")
    print("=" * 80)

if __name__ == "__main__":
    main()
