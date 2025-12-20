#!/usr/bin/env python3
"""
Audit Controller Functions vs Notification Implementation
Memetakan fungsi controller yang ADA vs notifikasi yang sudah diimplementasi
"""

from pathlib import Path
import re

# Mapping controller functions ke trigger_event yang sesuai
CONTROLLER_TO_TRIGGER = {
    # Marketing / Quotation
    'storeQuotation': 'quotation_created',
    'updateQuotationStage': 'quotation_stage_changed',
    'createSPK': 'spk_created',
    'createContract': 'customer_contract_created',
    
    # Customer Management
    'storeCustomer': 'customer_created',
    'updateCustomer': 'customer_updated',
    'deleteCustomer': 'customer_deleted',
    'storeCustomerLocation': 'customer_location_added',
    
    # Purchase Order
    'storePoUnit': 'po_unit_created',
    'storePoAttachment': 'po_attachment_created',
    'storePoSparepart': 'po_sparepart_created',
    'verifyPoUnit': 'po_verified',
    'verifyPoAttachment': 'po_verified',
    'verifyPoSparepart': 'po_verified',
    'cancelPO': 'po_rejected',
    
    # Supplier
    'storeSupplier': 'supplier_created',
    'updateSupplier': 'supplier_updated',
    'deleteSupplier': 'supplier_deleted',
    
    # Delivery Instruction (DI)
    'diCreate': 'di_created',
    'diApprove': 'di_approved',
    'diUpdateStatus': 'di_in_progress',  # atau di_delivered tergantung status
    
    # Warehouse - Inventory Unit
    'saveUnit': 'inventory_unit_added',
    'updateUnit': 'inventory_unit_status_changed',
    
    # Warehouse - Attachment
    'saveAttachment': 'attachment_added',
    'updateAttachment': 'attachment_detached',  # atau attachment_swapped
    
    # Warehouse - Sparepart
    'saveSparepartStock': 'sparepart_added',
    'updateInventorySparepart': 'sparepart_used',
    
    # Work Order
    'createWorkOrder': 'work_order_created',
    'assignWorkOrder': 'work_order_assigned',
    'updateWorkOrderStatus': 'work_order_in_progress',  # atau work_order_completed
    
    # Service / SPK
    'assignItems': 'spk_assigned',
    'saveUnitVerification': 'unit_prep_completed',
    
    # Employee Assignment
    'storeAssignment': 'employee_assigned',
    'deleteAssignment': 'employee_unassigned',
    
    # Finance
    'createInvoice': 'invoice_created',
    'updatePaymentStatus': 'payment_received',  # atau invoice_paid
}

def scan_controllers():
    """Scan semua controller untuk menemukan fungsi yang ada"""
    controllers_dir = Path(r"c:\laragon\www\optima\app\Controllers")
    
    functions_found = {}
    
    main_controllers = [
        'Marketing.php',
        'Quotation.php', 
        'CustomerManagementController.php',
        'Purchasing.php',
        'WarehousePO.php',
        'Warehouse.php',
        'Operational.php',
        'Service.php',
        'Finance.php',
        'ServiceAreaManagementController.php',
        'WorkOrderController.php',
    ]
    
    for controller_file in main_controllers:
        file_path = controllers_dir / controller_file
        if not file_path.exists():
            continue
            
        content = file_path.read_text(encoding='utf-8', errors='ignore')
        
        # Find public functions
        pattern = r'public function\s+([a-zA-Z_][a-zA-Z0-9_]*)\s*\('
        matches = re.findall(pattern, content)
        
        controller_name = controller_file.replace('.php', '')
        functions_found[controller_name] = matches
    
    return functions_found

def main():
    print("=" * 80)
    print("AUDIT: CONTROLLER FUNCTIONS vs NOTIFICATION IMPLEMENTATION")
    print("=" * 80)
    print()
    
    # Scan controllers
    print("[1] Scanning controller functions...")
    controller_functions = scan_controllers()
    
    total_functions = sum(len(funcs) for funcs in controller_functions.values())
    print(f"    Found {total_functions} functions across {len(controller_functions)} controllers")
    print()
    
    # Analyze
    print("=" * 80)
    print("MAPPING: Controller Functions → Trigger Events")
    print("=" * 80)
    print()
    
    mapped = []
    not_mapped = []
    
    for controller, functions in controller_functions.items():
        for func in functions:
            if func in CONTROLLER_TO_TRIGGER:
                trigger = CONTROLLER_TO_TRIGGER[func]
                mapped.append({
                    'controller': controller,
                    'function': func,
                    'trigger': trigger
                })
            else:
                # Check if it's a CRUD function that might need notification
                if any(keyword in func.lower() for keyword in ['store', 'create', 'save', 'update', 'delete', 'approve', 'reject', 'assign', 'verify', 'cancel', 'complete']):
                    not_mapped.append({
                        'controller': controller,
                        'function': func
                    })
    
    print(f"✅ MAPPED Functions (Sudah ada trigger_event): {len(mapped)}")
    print(f"⚠️  NOT MAPPED Functions (Perlu review): {len(not_mapped)}")
    print()
    
    # Show mapped functions
    if mapped:
        print("=" * 80)
        print("✅ SUDAH ADA MAPPING (Tinggal panggil di controller)")
        print("=" * 80)
        for item in sorted(mapped, key=lambda x: x['controller']):
            print(f"  [{item['controller']}]")
            print(f"    Function: {item['function']}()")
            print(f"    Trigger:  {item['trigger']}")
            print(f"    Action:   Panggil notify_{item['trigger']}() setelah save/update")
            print()
    
    # Show not mapped functions (need review)
    if not_mapped:
        print("=" * 80)
        print("⚠️  PERLU REVIEW (Fungsi CRUD tapi belum ada mapping)")
        print("=" * 80)
        
        by_controller = {}
        for item in not_mapped:
            if item['controller'] not in by_controller:
                by_controller[item['controller']] = []
            by_controller[item['controller']].append(item['function'])
        
        for controller in sorted(by_controller.keys()):
            print(f"\n[{controller}]")
            for func in sorted(by_controller[controller]):
                print(f"  - {func}()")
                # Try to suggest trigger event
                func_lower = func.lower()
                if 'quotation' in func_lower:
                    if 'stage' in func_lower:
                        print(f"    → Suggested: quotation_stage_changed")
                    elif 'create' in func_lower or 'store' in func_lower:
                        print(f"    → Suggested: quotation_created")
                elif 'spk' in func_lower:
                    if 'cancel' in func_lower:
                        print(f"    → Suggested: spk_cancelled")
                    elif 'assign' in func_lower:
                        print(f"    → Suggested: spk_assigned")
                elif 'contract' in func_lower:
                    if 'create' in func_lower:
                        print(f"    → Suggested: customer_contract_created")
                    elif 'complete' in func_lower:
                        print(f"    → Suggested: contract_completed (orphaned)")
                elif 'po' in func_lower:
                    if 'verify' in func_lower:
                        print(f"    → Suggested: po_verified")
                    elif 'cancel' in func_lower:
                        print(f"    → Suggested: po_rejected")
                elif 'invoice' in func_lower:
                    if 'create' in func_lower:
                        print(f"    → Suggested: invoice_created")
                    elif 'payment' in func_lower:
                        print(f"    → Suggested: payment_received")
                elif 'delivery' in func_lower:
                    if 'status' in func_lower:
                        print(f"    → Suggested: delivery_status_changed")
    
    # Recommendations
    print()
    print("=" * 80)
    print("📋 REKOMENDASI IMPLEMENTASI")
    print("=" * 80)
    print()
    print("PRIORITAS 1 - SUDAH ADA CONTROLLER (Implement sekarang):")
    priority_1 = [
        "✅ Customer Management (store/update/delete) → customer_*",
        "✅ Quotation (store/updateStage) → quotation_*",
        "✅ Purchase Order (storePoUnit/Attachment/Sparepart) → po_*_created",
        "✅ Supplier (store/update/delete) → supplier_*",
        "✅ Invoice (create) → invoice_created",
        "✅ Payment (updateStatus) → payment_received",
        "✅ Employee Assignment (store/delete) → employee_assigned/unassigned",
        "✅ SPK (create, assign) → spk_created/assigned",
    ]
    for item in priority_1:
        print(f"  {item}")
    
    print()
    print("PRIORITAS 2 - PERLU CRON/SCHEDULER (Implement belakangan):")
    priority_2 = [
        "⏰ Invoice Overdue (cron harian)",
        "⏰ Sparepart Low/Out of Stock (trigger pada update stock)",
        "⏰ PMPS Due Soon/Overdue (cron harian)",
        "⏰ Contract Expired (cron harian)",
    ]
    for item in priority_2:
        print(f"  {item}")
    
    print()
    print("=" * 80)
    print("AUDIT COMPLETE")
    print("=" * 80)

if __name__ == "__main__":
    main()
