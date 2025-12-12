<?php

/**
 * COMPREHENSIVE PERMISSION GENERATOR
 * Script untuk generate semua permission berdasarkan audit komprehensif OPTIMA system
 * Berdasarkan analisis sidebar_new.php
 */

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ComprehensivePermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Clear existing permissions safely
        try {
            // Disable foreign key checks temporarily
            $db->query('SET FOREIGN_KEY_CHECKS = 0');
            
            // Clear role_permissions first
            $db->table('role_permissions')->truncate();
            
            // Clear permissions
            $db->table('permissions')->truncate();
            
            // Re-enable foreign key checks
            $db->query('SET FOREIGN_KEY_CHECKS = 1');
            
            echo "Cleared existing permissions and role_permissions...\n";
        } catch (\Exception $e) {
            echo "Warning: Could not truncate tables, attempting delete instead...\n";
            $db->table('role_permissions')->emptyTable();
            $db->table('permissions')->emptyTable();
        }
        
        echo "Generating comprehensive permissions for OPTIMA system...\n";
        
        $permissions = [
            
            // ====================================
            // MARKETING MODULE
            // ====================================
            
            // Customer Management
            [
                'module' => 'marketing',
                'page' => 'customer',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer.navigation',
                'display_name' => 'Customer Management Navigation',
                'description' => 'Access to Customer Management menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer.index',
                'display_name' => 'View Customer List',
                'description' => 'View customer management page and list customers',
                'category' => 'read'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer.create',
                'display_name' => 'Create Customer',
                'description' => 'Add new customer to the system',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer.edit',
                'display_name' => 'Edit Customer',
                'description' => 'Edit existing customer data',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer',
                'action' => 'delete',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer.delete',
                'display_name' => 'Delete Customer',
                'description' => 'Delete customer from system',
                'category' => 'delete'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer',
                'action' => 'export',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer.export',
                'display_name' => 'Export Customer Data',
                'description' => 'Export customer data to Excel/PDF',
                'category' => 'export'
            ],
            
            // Customer Database
            [
                'module' => 'marketing',
                'page' => 'customer_db',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer_db.navigation',
                'display_name' => 'Customer Database Navigation',
                'description' => 'Access to Customer Database menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer_db',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer_db.index',
                'display_name' => 'View Customer Database',
                'description' => 'View customer database page',
                'category' => 'read'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer_db',
                'action' => 'search',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer_db.search',
                'display_name' => 'Search Customer Database',
                'description' => 'Search functionality in customer database',
                'category' => 'action'
            ],
            [
                'module' => 'marketing',
                'page' => 'customer_db',
                'action' => 'export',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.customer_db.export',
                'display_name' => 'Export Customer Database',
                'description' => 'Export customer database',
                'category' => 'export'
            ],
            
            // Quotation System
            [
                'module' => 'marketing',
                'page' => 'quotation',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.quotation.navigation',
                'display_name' => 'Quotation System Navigation',
                'description' => 'Access to Quotation System menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'marketing',
                'page' => 'quotation',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.quotation.index',
                'display_name' => 'View Quotations',
                'description' => 'View quotation list and system',
                'category' => 'read'
            ],
            [
                'module' => 'marketing',
                'page' => 'quotation',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.quotation.create',
                'display_name' => 'Create Quotation',
                'description' => 'Create new quotation',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'quotation',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.quotation.edit',
                'display_name' => 'Edit Quotation',
                'description' => 'Edit existing quotation',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'quotation',
                'action' => 'delete',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.quotation.delete',
                'display_name' => 'Delete Quotation',
                'description' => 'Delete quotation',
                'category' => 'delete'
            ],
            [
                'module' => 'marketing',
                'page' => 'quotation',
                'action' => 'approve',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.quotation.approve',
                'display_name' => 'Approve Quotation',
                'description' => 'Approve quotation for processing',
                'category' => 'action'
            ],
            [
                'module' => 'marketing',
                'page' => 'quotation',
                'action' => 'print',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.quotation.print',
                'display_name' => 'Print Quotation',
                'description' => 'Print quotation document',
                'category' => 'action'
            ],
            
            // SPK Management
            [
                'module' => 'marketing',
                'page' => 'spk',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.spk.navigation',
                'display_name' => 'SPK Management Navigation',
                'description' => 'Access to SPK Management menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'marketing',
                'page' => 'spk',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.spk.index',
                'display_name' => 'View SPK List',
                'description' => 'View SPK management page',
                'category' => 'read'
            ],
            [
                'module' => 'marketing',
                'page' => 'spk',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.spk.create',
                'display_name' => 'Create SPK',
                'description' => 'Create new SPK',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'spk',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.spk.edit',
                'display_name' => 'Edit SPK',
                'description' => 'Edit existing SPK',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'spk',
                'action' => 'delete',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.spk.delete',
                'display_name' => 'Delete SPK',
                'description' => 'Delete SPK',
                'category' => 'delete'
            ],
            [
                'module' => 'marketing',
                'page' => 'spk',
                'action' => 'close',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.spk.close',
                'display_name' => 'Close SPK',
                'description' => 'Close completed SPK',
                'category' => 'action'
            ],
            
            // Delivery Instructions
            [
                'module' => 'marketing',
                'page' => 'delivery',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.delivery.navigation',
                'display_name' => 'Delivery Instructions Navigation',
                'description' => 'Access to Delivery Instructions menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'marketing',
                'page' => 'delivery',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.delivery.index',
                'display_name' => 'View Delivery Instructions',
                'description' => 'View delivery instructions page',
                'category' => 'read'
            ],
            [
                'module' => 'marketing',
                'page' => 'delivery',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.delivery.create',
                'display_name' => 'Create Delivery Instruction',
                'description' => 'Create new delivery instruction',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'delivery',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.delivery.edit',
                'display_name' => 'Edit Delivery Instruction',
                'description' => 'Edit delivery instruction',
                'category' => 'write'
            ],
            [
                'module' => 'marketing',
                'page' => 'delivery',
                'action' => 'print',
                'subaction' => '',
                'component' => '',
                'key_name' => 'marketing.delivery.print',
                'display_name' => 'Print Delivery Instruction',
                'description' => 'Print delivery instruction',
                'category' => 'action'
            ],
            
            // ====================================
            // SERVICE MODULE
            // ====================================
            
            // Work Orders
            [
                'module' => 'service',
                'page' => 'workorder',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.workorder.navigation',
                'display_name' => 'Work Orders Navigation',
                'description' => 'Access to Work Orders menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'service',
                'page' => 'workorder',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.workorder.index',
                'display_name' => 'View Work Orders',
                'description' => 'View work orders page',
                'category' => 'read'
            ],
            [
                'module' => 'service',
                'page' => 'workorder',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.workorder.create',
                'display_name' => 'Create Work Order',
                'description' => 'Create new work order',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'workorder',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.workorder.edit',
                'display_name' => 'Edit Work Order',
                'description' => 'Edit work order',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'workorder',
                'action' => 'assign',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.workorder.assign',
                'display_name' => 'Assign Work Order',
                'description' => 'Assign technician to work order',
                'category' => 'action'
            ],
            [
                'module' => 'service',
                'page' => 'workorder',
                'action' => 'complete',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.workorder.complete',
                'display_name' => 'Complete Work Order',
                'description' => 'Mark work order as completed',
                'category' => 'action'
            ],
            
            // PMPS Management
            [
                'module' => 'service',
                'page' => 'pmps',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.pmps.navigation',
                'display_name' => 'PMPS Management Navigation',
                'description' => 'Access to PMPS menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'service',
                'page' => 'pmps',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.pmps.index',
                'display_name' => 'View PMPS Schedule',
                'description' => 'View PMPS management page',
                'category' => 'read'
            ],
            [
                'module' => 'service',
                'page' => 'pmps',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.pmps.create',
                'display_name' => 'Create PMPS Schedule',
                'description' => 'Create new PMPS schedule',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'pmps',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.pmps.edit',
                'display_name' => 'Edit PMPS Schedule',
                'description' => 'Edit PMPS schedule',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'pmps',
                'action' => 'execute',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.pmps.execute',
                'display_name' => 'Execute PMPS',
                'description' => 'Execute PMPS maintenance',
                'category' => 'action'
            ],
            
            // Area Management
            [
                'module' => 'service',
                'page' => 'area',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.area.navigation',
                'display_name' => 'Area Management Navigation',
                'description' => 'Access to Area Management menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'service',
                'page' => 'area',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.area.index',
                'display_name' => 'View Service Areas',
                'description' => 'View area management page',
                'category' => 'read'
            ],
            [
                'module' => 'service',
                'page' => 'area',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.area.create',
                'display_name' => 'Create Service Area',
                'description' => 'Create new service area',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'area',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.area.edit',
                'display_name' => 'Edit Service Area',
                'description' => 'Edit service area',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'area',
                'action' => 'assign_user',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.area.assign_user',
                'display_name' => 'Assign User to Area',
                'description' => 'Assign user to service area',
                'category' => 'action'
            ],
            
            // User Management
            [
                'module' => 'service',
                'page' => 'user',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.user.navigation',
                'display_name' => 'Service User Management Navigation',
                'description' => 'Access to Service User Management menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'service',
                'page' => 'user',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.user.index',
                'display_name' => 'View Service Users',
                'description' => 'View service user management page',
                'category' => 'read'
            ],
            [
                'module' => 'service',
                'page' => 'user',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.user.create',
                'display_name' => 'Create Service User',
                'description' => 'Create new service user',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'user',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.user.edit',
                'display_name' => 'Edit Service User',
                'description' => 'Edit service user data',
                'category' => 'write'
            ],
            [
                'module' => 'service',
                'page' => 'user',
                'action' => 'assign_area',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.user.assign_area',
                'display_name' => 'Assign Service Area',
                'description' => 'Assign service area to user',
                'category' => 'action'
            ],
            [
                'module' => 'service',
                'page' => 'user',
                'action' => 'assign_branch',
                'subaction' => '',
                'component' => '',
                'key_name' => 'service.user.assign_branch',
                'display_name' => 'Assign Branch Access',
                'description' => 'Assign branch access to user',
                'category' => 'action'
            ],
            
            // ====================================
            // PURCHASING MODULE  
            // ====================================
            
            // PO Management
            [
                'module' => 'purchasing',
                'page' => 'po',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po.navigation',
                'display_name' => 'PO Management Navigation',
                'description' => 'Access to PO Management menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'purchasing',
                'page' => 'po',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po.index',
                'display_name' => 'View Purchase Orders',
                'description' => 'View PO management page',
                'category' => 'read'
            ],
            [
                'module' => 'purchasing',
                'page' => 'po',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po.create',
                'display_name' => 'Create Purchase Order',
                'description' => 'Create new purchase order',
                'category' => 'write'
            ],
            [
                'module' => 'purchasing',
                'page' => 'po',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po.edit',
                'display_name' => 'Edit Purchase Order',
                'description' => 'Edit purchase order',
                'category' => 'write'
            ],
            [
                'module' => 'purchasing',
                'page' => 'po',
                'action' => 'approve',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po.approve',
                'display_name' => 'Approve Purchase Order',
                'description' => 'Approve purchase order',
                'category' => 'action'
            ],
            
            // PO Sparepart
            [
                'module' => 'purchasing',
                'page' => 'po_sparepart',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po_sparepart.navigation',
                'display_name' => 'PO Sparepart Navigation',
                'description' => 'Access to PO Sparepart menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'purchasing',
                'page' => 'po_sparepart',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po_sparepart.index',
                'display_name' => 'View Sparepart POs',
                'description' => 'View sparepart purchase orders',
                'category' => 'read'
            ],
            [
                'module' => 'purchasing',
                'page' => 'po_sparepart',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.po_sparepart.create',
                'display_name' => 'Create Sparepart PO',
                'description' => 'Create sparepart purchase order',
                'category' => 'write'
            ],
            
            // Supplier Management
            [
                'module' => 'purchasing',
                'page' => 'supplier',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.supplier.navigation',
                'display_name' => 'Supplier Management Navigation',
                'description' => 'Access to Supplier Management menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'purchasing',
                'page' => 'supplier',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.supplier.index',
                'display_name' => 'View Suppliers',
                'description' => 'View supplier management page',
                'category' => 'read'
            ],
            [
                'module' => 'purchasing',
                'page' => 'supplier',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.supplier.create',
                'display_name' => 'Create Supplier',
                'description' => 'Add new supplier',
                'category' => 'write'
            ],
            [
                'module' => 'purchasing',
                'page' => 'supplier',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'purchasing.supplier.edit',
                'display_name' => 'Edit Supplier',
                'description' => 'Edit supplier information',
                'category' => 'write'
            ],
            
            // ====================================
            // WAREHOUSE MODULE
            // ====================================
            
            // Unit Inventory
            [
                'module' => 'warehouse',
                'page' => 'unit_inventory',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.unit_inventory.navigation',
                'display_name' => 'Unit Inventory Navigation',
                'description' => 'Access to Unit Inventory menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'warehouse',
                'page' => 'unit_inventory',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.unit_inventory.index',
                'display_name' => 'View Unit Inventory',
                'description' => 'View unit inventory page',
                'category' => 'read'
            ],
            [
                'module' => 'warehouse',
                'page' => 'unit_inventory',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.unit_inventory.create',
                'display_name' => 'Add Unit Inventory',
                'description' => 'Add new unit to inventory',
                'category' => 'write'
            ],
            [
                'module' => 'warehouse',
                'page' => 'unit_inventory',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.unit_inventory.edit',
                'display_name' => 'Edit Unit Inventory',
                'description' => 'Edit unit inventory data',
                'category' => 'write'
            ],
            
            // Attachment & Battery Inventory
            [
                'module' => 'warehouse',
                'page' => 'attachment_inventory',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.attachment_inventory.navigation',
                'display_name' => 'Attachment & Battery Navigation',
                'description' => 'Access to Attachment & Battery menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'warehouse',
                'page' => 'attachment_inventory',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.attachment_inventory.index',
                'display_name' => 'View Attachment Inventory',
                'description' => 'View attachment & battery inventory',
                'category' => 'read'
            ],
            [
                'module' => 'warehouse',
                'page' => 'attachment_inventory',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.attachment_inventory.create',
                'display_name' => 'Add Attachment Inventory',
                'description' => 'Add attachment to inventory',
                'category' => 'write'
            ],
            
            // Sparepart Inventory
            [
                'module' => 'warehouse',
                'page' => 'sparepart_inventory',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.sparepart_inventory.navigation',
                'display_name' => 'Sparepart Inventory Navigation',
                'description' => 'Access to Sparepart Inventory menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'warehouse',
                'page' => 'sparepart_inventory',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.sparepart_inventory.index',
                'display_name' => 'View Sparepart Inventory',
                'description' => 'View sparepart inventory page',
                'category' => 'read'
            ],
            [
                'module' => 'warehouse',
                'page' => 'sparepart_inventory',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.sparepart_inventory.create',
                'display_name' => 'Add Sparepart Inventory',
                'description' => 'Add sparepart to inventory',
                'category' => 'write'
            ],
            
            // Sparepart Usage & Returns
            [
                'module' => 'warehouse',
                'page' => 'sparepart_usage',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.sparepart_usage.navigation',
                'display_name' => 'Sparepart Usage & Returns Navigation',
                'description' => 'Access to Sparepart Usage menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'warehouse',
                'page' => 'sparepart_usage',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.sparepart_usage.index',
                'display_name' => 'View Sparepart Usage',
                'description' => 'View sparepart usage page',
                'category' => 'read'
            ],
            [
                'module' => 'warehouse',
                'page' => 'sparepart_usage',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.sparepart_usage.create',
                'display_name' => 'Record Sparepart Usage',
                'description' => 'Record sparepart usage',
                'category' => 'write'
            ],
            [
                'module' => 'warehouse',
                'page' => 'sparepart_usage',
                'action' => 'return',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.sparepart_usage.return',
                'display_name' => 'Process Sparepart Return',
                'description' => 'Process sparepart returns',
                'category' => 'action'
            ],
            
            // PO Verification
            [
                'module' => 'warehouse',
                'page' => 'po_verification',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.po_verification.navigation',
                'display_name' => 'PO Verification Navigation',
                'description' => 'Access to PO Verification menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'warehouse',
                'page' => 'po_verification',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.po_verification.index',
                'display_name' => 'View PO Verification',
                'description' => 'View PO verification page',
                'category' => 'read'
            ],
            [
                'module' => 'warehouse',
                'page' => 'po_verification',
                'action' => 'verify',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.po_verification.verify',
                'display_name' => 'Verify PO Items',
                'description' => 'Verify purchase order items',
                'category' => 'action'
            ],
            [
                'module' => 'warehouse',
                'page' => 'po_verification',
                'action' => 'approve',
                'subaction' => '',
                'component' => '',
                'key_name' => 'warehouse.po_verification.approve',
                'display_name' => 'Approve PO Verification',
                'description' => 'Approve verification results',
                'category' => 'action'
            ],
            
            // ====================================
            // ACCOUNTING MODULE
            // ====================================
            
            // Invoice Management
            [
                'module' => 'accounting',
                'page' => 'invoice',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.invoice.navigation',
                'display_name' => 'Invoice Management Navigation',
                'description' => 'Access to Invoice Management menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'accounting',
                'page' => 'invoice',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.invoice.index',
                'display_name' => 'View Invoices',
                'description' => 'View invoice management page',
                'category' => 'read'
            ],
            [
                'module' => 'accounting',
                'page' => 'invoice',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.invoice.create',
                'display_name' => 'Create Invoice',
                'description' => 'Create new invoice',
                'category' => 'write'
            ],
            [
                'module' => 'accounting',
                'page' => 'invoice',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.invoice.edit',
                'display_name' => 'Edit Invoice',
                'description' => 'Edit invoice data',
                'category' => 'write'
            ],
            [
                'module' => 'accounting',
                'page' => 'invoice',
                'action' => 'approve',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.invoice.approve',
                'display_name' => 'Approve Invoice',
                'description' => 'Approve invoice for processing',
                'category' => 'action'
            ],
            [
                'module' => 'accounting',
                'page' => 'invoice',
                'action' => 'print',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.invoice.print',
                'display_name' => 'Print Invoice',
                'description' => 'Print invoice document',
                'category' => 'action'
            ],
            
            // Payment Validation
            [
                'module' => 'accounting',
                'page' => 'payment',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.payment.navigation',
                'display_name' => 'Payment Validation Navigation',
                'description' => 'Access to Payment Validation menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'accounting',
                'page' => 'payment',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.payment.index',
                'display_name' => 'View Payment Validation',
                'description' => 'View payment validation page',
                'category' => 'read'
            ],
            [
                'module' => 'accounting',
                'page' => 'payment',
                'action' => 'validate',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.payment.validate',
                'display_name' => 'Validate Payment',
                'description' => 'Validate payment transactions',
                'category' => 'action'
            ],
            [
                'module' => 'accounting',
                'page' => 'payment',
                'action' => 'approve',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.payment.approve',
                'display_name' => 'Approve Payment',
                'description' => 'Approve validated payments',
                'category' => 'action'
            ],
            [
                'module' => 'accounting',
                'page' => 'payment',
                'action' => 'reject',
                'subaction' => '',
                'component' => '',
                'key_name' => 'accounting.payment.reject',
                'display_name' => 'Reject Payment',
                'description' => 'Reject payment validation',
                'category' => 'action'
            ],
            
            // ====================================
            // OPERATIONAL MODULE
            // ====================================
            
            // Delivery Process
            [
                'module' => 'operational',
                'page' => 'delivery',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'operational.delivery.navigation',
                'display_name' => 'Delivery Process Navigation',
                'description' => 'Access to Delivery Process menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'operational',
                'page' => 'delivery',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'operational.delivery.index',
                'display_name' => 'View Delivery Process',
                'description' => 'View delivery process page',
                'category' => 'read'
            ],
            [
                'module' => 'operational',
                'page' => 'delivery',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'operational.delivery.create',
                'display_name' => 'Create Delivery',
                'description' => 'Create new delivery',
                'category' => 'write'
            ],
            [
                'module' => 'operational',
                'page' => 'delivery',
                'action' => 'dispatch',
                'subaction' => '',
                'component' => '',
                'key_name' => 'operational.delivery.dispatch',
                'display_name' => 'Dispatch Delivery',
                'description' => 'Dispatch delivery for execution',
                'category' => 'action'
            ],
            [
                'module' => 'operational',
                'page' => 'delivery',
                'action' => 'track',
                'subaction' => '',
                'component' => '',
                'key_name' => 'operational.delivery.track',
                'display_name' => 'Track Delivery',
                'description' => 'Track delivery status',
                'category' => 'action'
            ],
            
            // ====================================
            // PERIZINAN MODULE
            // ====================================
            
            // SILO Management
            [
                'module' => 'perizinan',
                'page' => 'silo',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.silo.navigation',
                'display_name' => 'SILO Management Navigation',
                'description' => 'Access to SILO menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'perizinan',
                'page' => 'silo',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.silo.index',
                'display_name' => 'View SILO Documents',
                'description' => 'View SILO management page',
                'category' => 'read'
            ],
            [
                'module' => 'perizinan',
                'page' => 'silo',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.silo.create',
                'display_name' => 'Create SILO Application',
                'description' => 'Create new SILO application',
                'category' => 'write'
            ],
            [
                'module' => 'perizinan',
                'page' => 'silo',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.silo.edit',
                'display_name' => 'Edit SILO Application',
                'description' => 'Edit SILO application',
                'category' => 'write'
            ],
            [
                'module' => 'perizinan',
                'page' => 'silo',
                'action' => 'approve',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.silo.approve',
                'display_name' => 'Approve SILO',
                'description' => 'Approve SILO application',
                'category' => 'action'
            ],
            
            // EMISI Management
            [
                'module' => 'perizinan',
                'page' => 'emisi',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.emisi.navigation',
                'display_name' => 'EMISI Management Navigation',
                'description' => 'Access to EMISI menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'perizinan',
                'page' => 'emisi',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.emisi.index',
                'display_name' => 'View EMISI Documents',
                'description' => 'View EMISI management page',
                'category' => 'read'
            ],
            [
                'module' => 'perizinan',
                'page' => 'emisi',
                'action' => 'create',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.emisi.create',
                'display_name' => 'Create EMISI Application',
                'description' => 'Create new EMISI application',
                'category' => 'write'
            ],
            [
                'module' => 'perizinan',
                'page' => 'emisi',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.emisi.edit',
                'display_name' => 'Edit EMISI Application',
                'description' => 'Edit EMISI application',
                'category' => 'write'
            ],
            [
                'module' => 'perizinan',
                'page' => 'emisi',
                'action' => 'approve',
                'subaction' => '',
                'component' => '',
                'key_name' => 'perizinan.emisi.approve',
                'display_name' => 'Approve EMISI',
                'description' => 'Approve EMISI application',
                'category' => 'action'
            ],
            
            // ====================================
            // ADMINISTRATION MODULE
            // ====================================
            
            // Administration Dashboard
            [
                'module' => 'admin',
                'page' => 'dashboard',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.dashboard.navigation',
                'display_name' => 'Administration Navigation',
                'description' => 'Access to Administration menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'admin',
                'page' => 'dashboard',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.dashboard.index',
                'display_name' => 'View Administration Dashboard',
                'description' => 'View administration dashboard',
                'category' => 'read'
            ],
            [
                'module' => 'admin',
                'page' => 'dashboard',
                'action' => 'stats',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.dashboard.stats',
                'display_name' => 'View System Statistics',
                'description' => 'View system statistics and reports',
                'category' => 'read'
            ],
            [
                'module' => 'admin',
                'page' => 'dashboard',
                'action' => 'reports',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.dashboard.reports',
                'display_name' => 'Generate Admin Reports',
                'description' => 'Generate administrative reports',
                'category' => 'action'
            ],
            
            // Configuration
            [
                'module' => 'admin',
                'page' => 'config',
                'action' => 'navigation',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.config.navigation',
                'display_name' => 'Configuration Navigation',
                'description' => 'Access to Configuration menu',
                'category' => 'navigation'
            ],
            [
                'module' => 'admin',
                'page' => 'config',
                'action' => 'index',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.config.index',
                'display_name' => 'View Configuration',
                'description' => 'View system configuration',
                'category' => 'read'
            ],
            [
                'module' => 'admin',
                'page' => 'config',
                'action' => 'edit',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.config.edit',
                'display_name' => 'Edit Configuration',
                'description' => 'Edit system configuration',
                'category' => 'write'
            ],
            [
                'module' => 'admin',
                'page' => 'config',
                'action' => 'backup',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.config.backup',
                'display_name' => 'Backup Configuration',
                'description' => 'Backup system configuration',
                'category' => 'action'
            ],
            [
                'module' => 'admin',
                'page' => 'config',
                'action' => 'restore',
                'subaction' => '',
                'component' => '',
                'key_name' => 'admin.config.restore',
                'display_name' => 'Restore Configuration',
                'description' => 'Restore system configuration',
                'category' => 'action'
            ],
            
        ];
        
        // Insert all permissions
        $totalInserted = 0;
        $totalErrors = 0;
        
        foreach ($permissions as $permission) {
            try {
                $db->table('permissions')->insert($permission);
                $totalInserted++;
                echo "✓ Created: {$permission['key_name']}\n";
            } catch (\Exception $e) {
                $totalErrors++;
                echo "✗ Error creating {$permission['key_name']}: " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "COMPREHENSIVE PERMISSION GENERATION COMPLETE\n";
        echo str_repeat("=", 60) . "\n";
        echo "Total Permissions Created: {$totalInserted}\n";
        echo "Total Errors: {$totalErrors}\n";
        echo "Total Modules: 8\n";
        echo "Total Categories: navigation, read, write, delete, export, action\n";
        echo str_repeat("=", 60) . "\n";
        
        // Show module summary
        $modules = [
            'marketing' => 'Marketing (Customer, Quotation, SPK, Delivery)',
            'service' => 'Service (Work Orders, PMPS, Area, User Management)',
            'purchasing' => 'Purchasing (PO, Sparepart, Supplier)',
            'warehouse' => 'Warehouse (Unit, Attachment, Sparepart, Verification)',
            'accounting' => 'Accounting (Invoice, Payment Validation)',
            'operational' => 'Operational (Delivery Process)',
            'perizinan' => 'Perizinan (SILO, EMISI)',
            'admin' => 'Administration (Dashboard, Configuration)'
        ];
        
        echo "MODULES COVERED:\n";
        foreach ($modules as $module => $description) {
            $count = $db->table('permissions')->where('module', $module)->countAllResults();
            echo "- {$module}: {$count} permissions ({$description})\n";
        }
        
        echo "\nPERMISSION CATEGORIES:\n";
        $categories = ['navigation', 'read', 'write', 'delete', 'export', 'action'];
        foreach ($categories as $category) {
            $count = $db->table('permissions')->where('category', $category)->countAllResults();
            echo "- {$category}: {$count} permissions\n";
        }
        
        echo "\nNext Steps:\n";
        echo "1. Update controllers with permission checks\n";
        echo "2. Update views with conditional elements\n";
        echo "3. Update sidebar navigation\n";
        echo "4. Create default role-permission mappings\n";
        echo "5. Test permission system across all modules\n";
    }
}