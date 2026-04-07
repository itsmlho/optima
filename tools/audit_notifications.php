<?php
/**
 * AUDIT SCRIPT: Cross-reference notification_rules templates vs PHP helper variables
 * Checks every active template's {{variables}} against what PHP sends
 */
$pdo = new PDO('mysql:host=localhost;dbname=optima_ci;charset=utf8mb4', 'root', '');

// PHP sends these keys per event_type (from notification_helper.php)
$phpSends = [
    'spk_created'                  => ['nomor_spk','pelanggan','departemen','department','unit_no','no_unit','created_by'],
    'spk_ready'                    => ['nomor_spk','pelanggan','jumlah_unit','no_unit','departemen'],
    'spk_completed'                => ['nomor_spk','spk_number','unit_code','no_unit','departemen','completed_by','pelanggan'],
    'spk_assigned'                 => ['nomor_spk','pelanggan','assigned_to'],
    'spk_cancelled'                => ['nomor_spk','pelanggan','reason'],
    'spk_unit_prep_completed'      => ['nomor_spk','spk_number','pelanggan'],
    'spk_fabrication_completed'    => ['nomor_spk','spk_number','pelanggan'],
    'spk_pdi_completed'            => ['nomor_spk','spk_number','pelanggan'],
    'po_created'                   => ['po_number','supplier_name','po_type','total_amount','created_by'],
    'purchase_order_created'       => ['po_number','vendor','amount'],
    'po_approved'                  => ['po_number','approved_by','notes'],
    'po_rejected'                  => ['po_number','reason'],
    'po_received'                  => ['po_number'],
    'po_verified'                  => ['po_number'],
    'po_unit_created'              => ['po_number'],
    'po_attachment_created'        => ['po_number'],
    'po_sparepart_created'         => ['po_number'],
    'po_delivery_created'          => ['delivery_number','po_number'],
    'po_from_quotation'            => ['po_number','quotation_number'],
    'work_order_created'           => ['wo_number','unit_code','no_unit','priority'],
    'work_order_assigned'          => ['wo_number'],
    'work_order_in_progress'       => ['wo_number'],
    'work_order_completed'         => ['wo_number'],
    'work_order_cancelled'         => ['wo_number'],
    'work_order_unit_verified'     => ['wo_number','unit_code','unit_no'],
    'di_submitted'                 => ['nomor_di','pelanggan','unit_no'],
    'di_approved'                  => ['nomor_di'],
    'di_in_progress'               => ['nomor_di'],
    'di_delivered'                 => ['nomor_di'],
    'di_cancelled'                 => ['nomor_di'],
    'customer_created'             => ['customer_name','customer_code','contact_person','phone'],
    'customer_updated'             => ['customer_name'],
    'customer_deleted'             => ['customer_name'],
    'customer_location_added'      => ['customer_name'],
    'customer_status_changed'      => ['customer_name','new_status'],
    'customer_contract_created'    => ['contract_number','customer_name'],
    'invoice_created'              => ['invoice_number','customer','customer_name','amount'],
    'invoice_sent'                 => ['invoice_number','customer','customer_name','amount'],
    'invoice_paid'                 => ['invoice_number','customer','customer_name'],
    'invoice_overdue'              => ['invoice_number','customer','customer_name'],
    'payment_received'             => ['invoice_number','customer','customer_name'],
    'payment_overdue'              => ['invoice_number','customer','customer_name'],
    'sparepart_low_stock'          => ['kode_sparepart','nama_sparepart','part_number'],
    'sparepart_out_of_stock'       => ['kode_sparepart','nama_sparepart','part_number'],
    'sparepart_added'              => ['kode_sparepart','nama_sparepart','part_number','part_name'],
    'sparepart_used'               => ['kode_sparepart','reference','work_order_number','part_number','wo_number','sparepart_name'],
    'sparepart_returned'           => ['part_number','source'],
    'sparepart_validation_saved'   => ['work_order_number'],
    'service_assignment_created'   => ['employee_name','area_name'],
    'service_assignment_updated'   => ['employee_name','area_name'],
    'service_assignment_deleted'   => ['employee_name','area_name'],
    'quotation_created'            => ['quotation_number','customer_name','customer'],
    'quotation_stage_changed'      => ['quotation_number','new_stage'],
    'quotation_sent_to_customer'   => ['quote_number','customer_name'],
    'quotation_follow_up_required' => ['quote_number','quotation_number'],  // uses quote_number not quotation_number
    'contract_created'             => ['contract_number','customer_name','customer'],
    'contract_updated'             => ['contract_number'],
    'contract_deleted'             => ['contract_number'],
    'contract_completed'           => ['contract_number'],
    'supplier_created'             => ['supplier_name'],
    'supplier_updated'             => ['supplier_name'],
    'supplier_deleted'             => ['supplier_name'],
    'employee_assigned'            => ['employee_name','division'],
    'employee_unassigned'          => ['employee_name','division'],
    'unit_location_updated'        => ['unit_no','location'],
    'warehouse_unit_updated'       => ['unit_no'],
    'inventory_unit_added'         => ['unit_no','model'],
    'inventory_unit_status_changed'=> ['unit_no','new_status'],
    'inventory_unit_maintenance'   => ['unit_no'],
    'inventory_unit_returned'      => ['unit_no'],
    'attachment_added'             => ['filename','attachment_name'],
    'attachment_attached'          => ['attachment_name','unit_no'],
    'attachment_detached'          => ['attachment_name','unit_no'],
    'attachment_swapped'           => ['attachment_name','unit_no'],
    'pmps_due_soon'                => ['unit_no'],
    'pmps_overdue'                 => ['unit_no'],
    'pmps_completed'               => ['unit_no'],
    'maintenance_scheduled'        => ['maintenance_number','unit_no'],
    'maintenance_completed'        => ['maintenance_number'],
    'inspection_scheduled'         => ['inspection_number','unit_no'],
    'inspection_completed'         => ['inspection_number'],
    'workorder_ttr_updated'        => ['wo_number'],
    'user_removed_from_division'   => ['username','division'],
    'user_permissions_updated'     => ['username'],
    'permission_created'           => ['permission_name'],
    'role_created'                 => ['role_name'],
    'role_updated'                 => ['role_name'],
    'role_saved'                   => ['role_name'],
];

$rules = $pdo->query("SELECT trigger_event, title_template, message_template FROM notification_rules WHERE is_active=1 ORDER BY trigger_event")->fetchAll(PDO::FETCH_ASSOC);

$issues = [];

foreach ($rules as $rule) {
    $event = $rule['trigger_event'];
    $full = $rule['title_template'] . ' ' . $rule['message_template'];
    
    // Extract all {{vars}} from templates
    preg_match_all('/\{\{([^}]+)\}\}/', $full, $matches);
    $templateVars = $matches[1];
    
    // Check for single-brace patterns (bug)
    preg_match_all('/(?<!\{)\{([^{}]+)\}(?!\})/', $full, $singleMatches);
    $singleVars = array_filter($singleMatches[1], fn($v) => !str_contains($v, ' ')); // ignore {%...%} style
    
    if (!empty($singleVars)) {
        $issues[] = "SINGLE_BRACE [$event] → {" . implode('}, {', $singleVars) . '}';
    }
    
    if (!isset($phpSends[$event])) continue;
    
    $phpKeys = $phpSends[$event];
    foreach ($templateVars as $var) {
        $var = trim($var);
        // Skip module/id/url (internal)
        if (in_array($var, ['module','id','url','title','message'])) continue;
        if (!in_array($var, $phpKeys)) {
            $issues[] = "VAR_MISMATCH  [$event] template uses {{$var}} but PHP sends: [" . implode(', ', $phpKeys) . "]";
        }
    }
}

if (empty($issues)) {
    echo "✅ NO ISSUES FOUND - All templates match PHP keys\n";
} else {
    echo "❌ ISSUES FOUND (" . count($issues) . "):\n\n";
    foreach ($issues as $issue) {
        echo $issue . "\n";
    }
}
