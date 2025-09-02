<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('test', 'Test::index');

// Authentication Routes
$routes->group('auth', static function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('login', 'Auth::login');
    $routes->post('attempt-login', 'Auth::attemptLogin');
    $routes->get('register', 'Auth::register');
    $routes->post('attempt-register', 'Auth::attemptRegister');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('send-reset-link', 'Auth::sendResetLink');
    $routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
    $routes->post('update-password', 'Auth::updatePassword');
    $routes->get('logout', 'Auth::logout');
    $routes->get('profile', 'Auth::profile');
    $routes->post('update-profile', 'Auth::updateProfile');
    $routes->post('change-password', 'Auth::changePassword');
});

// Dashboard routes for different divisions
$routes->group('dashboard', static function ($routes) {
    $routes->get('', 'Dashboard::index');
    $routes->get('service', 'Dashboard::service');
    $routes->get('marketing', 'Dashboard::marketing');
    $routes->get('rolling', 'Dashboard::rolling');
    $routes->get('warehouse', 'Dashboard::warehouse');
}); 

$routes->get('/profile', 'System::profile');
// System routes for topbar functionality
$routes->get('/profile', 'System::profile');


$routes->get('/settings', 'System::settings');
$routes->get('/notifications', 'System::notifications');
$routes->get('/help', 'System::help');
$routes->get('/logout', 'System::logout');

// Apps routes
$routes->group('apps', static function ($routes) {
    $routes->get('calendar', 'Apps::calendar');
    $routes->get('messages', 'Apps::messages');
    $routes->get('settings', 'Apps::settings');
    $routes->get('analytics', 'Apps::analytics');
});

// Rental Management Routes
$routes->group('rentals', static function ($routes) {
    $routes->get('/', 'RentalManagement::index');
    $routes->get('create', 'RentalManagement::create');
    $routes->post('store', 'RentalManagement::store');
    $routes->get('edit/(:num)', 'RentalManagement::edit/$1');
    $routes->post('update/(:num)', 'RentalManagement::update/$1');
    $routes->post('delete/(:num)', 'RentalManagement::delete/$1');
    $routes->post('list', 'RentalManagement::getRentalList');
    $routes->post('stats', 'RentalManagement::getRentalStats');
    $routes->get('(:num)', 'RentalManagement::getRental/$1');
    $routes->post('update-status/(:num)', 'RentalManagement::updateStatus/$1');
    $routes->post('available-forklifts', 'RentalManagement::getAvailableForklifts');
    $routes->post('calculate-amount', 'RentalManagement::calculateRentalAmount');
    $routes->post('export/(:alpha)', 'RentalManagement::export/$1');
});

// Unit Rolling Routes
$routes->group('unitRolling', static function ($routes) {
    $routes->get('/', 'UnitRolling::index');
    $routes->get('history', 'UnitRolling::history');
});

// Marketing Routes
$routes->group('marketing',  static function ($routes) {
    $routes->get('/', 'Marketing::index');

    // KONTRAK CRUD
    $routes->group('kontrak', static function ($routes) {
        $routes->get('/', 'Kontrak::index'); 
        $routes->post('getDataTable', 'Kontrak::getDataTable');
        $routes->get('generate-number', 'Kontrak::generateNumber');
        $routes->post('check-duplicate', 'Kontrak::checkDuplicate');
        $routes->post('store', 'Kontrak::store');
        $routes->get('edit/(:num)', 'Kontrak::edit/$1');
        $routes->post('update/(:num)', 'Kontrak::update/$1');
        $routes->post('delete/(:num)', 'Kontrak::delete/$1');
        $routes->get('detail/(:num)', 'Kontrak::detail/$1');
        $routes->get('get/(:num)', 'Kontrak::get/$1');
        $routes->get('units/(:num)', 'Kontrak::getContractUnits/$1');
        
        // Spesifikasi management
        $routes->get('spesifikasi/(:num)', 'Kontrak::spesifikasi/$1');
        $routes->post('add-spesifikasi', 'Kontrak::addSpesifikasi');
        $routes->get('spesifikasi-detail/(:num)', 'Kontrak::spesifikasiDetail/$1');
        $routes->post('update-spesifikasi/(:num)', 'Kontrak::updateSpesifikasi/$1');
        $routes->post('delete-spesifikasi/(:num)', 'Kontrak::deleteSpesifikasi/$1');
        $routes->get('available-units/(:num)', 'Kontrak::getAvailableUnits/$1');
        $routes->post('assign-units', 'Kontrak::assignUnitsToSpesifikasi');
        
        // Debug endpoint (development only)
        $routes->get('debug-test-insert', 'Kontrak::debugTestInsert');
        
        // For SPK workflow
        $routes->get('get-active-contracts', 'Marketing::getActiveContracts');
        $routes->get('get-kontrak/(:num)', 'Marketing::getKontrak/$1');
        $routes->get('find-by-spesifikasi/(:num)', 'Marketing::findBySpesifikasi/$1');
    });
    // SPK Marketing
    $routes->get('spk', 'Marketing::spk');
    $routes->get('spk/list', 'Marketing::spkList');
    $routes->get('spk/kontrak-options', 'Marketing::kontrakOptions');
    $routes->get('spk/spec-options', 'Marketing::specOptions');
    $routes->get('spk/monitoring', 'Marketing::spkMonitoring');
    $routes->get('spk/detail/(:num)', 'Marketing::spkDetail/$1');
    $routes->post('spk/create', 'Marketing::spkCreate');
    $routes->post('spk/update-status/(:num)', 'Marketing::spkUpdateStatus/$1');
    $routes->post('spk/cleanup-zero', 'Marketing::cleanupSpkZero');
    // DI (Delivery Instruction) - Marketing creates
    $routes->post('di/create', 'Marketing::diCreate');
    // Marketing DI page & APIs
    $routes->get('di', 'Marketing::di');
    $routes->get('di/list', 'Marketing::diList');
    $routes->get('di/detail/(:num)', 'Marketing::diDetail/$1');
    $routes->get('spk/ready-options', 'Marketing::spkReadyOptions');
    
    $routes->get('penawaran', 'Marketing::penawaran');
    // $routes->get('list-unit', 'Marketing::listUnit');
    $routes->get('unit-tersedia', 'Marketing::unitTersedia');
    $routes->get('unitmarketing', 'Marketing::unitMarketing');
    $routes->get('available-units', 'Marketing::availableUnits');
    $routes->post('available-units/data', 'Marketing::availableUnitsData');
    $routes->get('unit-detail/(:num)', 'Marketing::unitDetail/$1');
    $routes->get('spk/pdf/(:num)', 'Marketing::spkPdf/$1');

});

// Service Division Routes
$routes->group('service', static function ($routes) {
    $routes->get('/', 'Service::index');
    $routes->get('work-orders', 'Service::workOrders');
    $routes->get('work-orders/history', 'Service::workOrderHistory');
    $routes->get('pmps', 'Service::pmps');
    $routes->get('data-unit', 'Service::dataUnit');
    // Data Unit (Service) AJAX endpoints
    $routes->post('data-unit/data', 'Service::dataUnitData');
    $routes->get('data-unit/detail/(:num)', 'Service::unitDetail/$1');
    $routes->post('data-unit/update/(:num)', 'Service::unitUpdate/$1');
    $routes->get('data-unit/export', 'Service::exportDataUnits');
    $routes->get('data-unit/maintenance-history/(:num)', 'Service::maintenanceHistory/$1');
    // SPK Service
    $routes->get('spk_service', 'Service::spkService');
    $routes->get('spk/list', 'Service::spkList');
    $routes->get('spk/detail/(:num)', 'Service::spkDetail/$1');
    $routes->post('spk/update-status/(:num)', 'Service::spkUpdateStatus/$1');
    $routes->post('spk/approve-stage/(:num)', 'Service::spkApproveStage/$1');
    $routes->post('spk/confirm-ready/(:num)', 'Service::spkConfirmReady/$1');
    // Simple unit search (for DI prepare)
    $routes->get('data-unit/simple', 'Service::dataUnitSimple');
    // Attachment simple search
    $routes->get('data-attachment/simple', 'Service::dataAttachmentSimple');
    // Check if no_unit already exists
    $routes->post('check-no-unit-exists', 'Service::checkNoUnitExists');
    // Smart Component Management - Unit component data endpoint
    $routes->get('unit-component-data/(:num)', 'Service::unitComponentData/$1');
    // Assign selected items (unit+attachment) to SPK and mark READY
    $routes->post('spk/assign-items', 'Service::spkAssignItems');
    $routes->get('spk/pdf/(:num)', 'Service::spkPdf/$1');
});

// Operational Routes (Delivery)
$routes->group('operational', static function ($routes) {
    $routes->get('delivery', 'Operational::delivery');
    $routes->get('delivery/list', 'Operational::diList');
    $routes->get('delivery/detail/(:num)', 'Operational::diDetail/$1');
    $routes->get('delivery/print/(:num)', 'Operational::diPrint/$1');
    $routes->post('delivery/update-status/(:num)', 'Operational::diUpdateStatus/$1');
    $routes->post('delivery/approve-stage/(:num)', 'Operational::diApproveStage/$1');
    $routes->get('tracking', 'Operational::tracking');
    $routes->post('tracking-search', 'Operational::trackingSearch');
    $routes->post('audit-trail', 'Operational::auditTrail');
});

// Warehouse Routes
$routes->group('warehouse', static function ($routes) {
    $routes->get('/', 'Warehouse::index');
    // $routes->get('sparepart', 'Warehouse::sparepart');
    
    // Unit Assets Management
    $routes->group('unit-assets', static function ($routes) {
        $routes->get('/', 'UnitAssetController::index');
        $routes->get('create', 'UnitAssetController::create');
        $routes->post('store', 'UnitAssetController::store');
        $routes->get('show/(:segment)', 'UnitAssetController::show/$1');
        $routes->get('edit/(:segment)', 'UnitAssetController::edit/$1');
        $routes->post('update/(:segment)', 'UnitAssetController::update/$1');
        $routes->post('delete/(:segment)', 'UnitAssetController::delete/$1');
        $routes->post('datatable', 'UnitAssetController::getDataTable');
        $routes->get('simple-data', 'UnitAssetController::getSimpleData');
        $routes->post('test-datatable', 'UnitAssetController::testDataTable');
        $routes->post('update-status', 'UnitAssetController::updateStatus');
        $routes->get('export', 'UnitAssetController::export');
        $routes->get('check-status', 'UnitAssetController::checkStatus');
        $routes->get('debug', 'UnitAssetController::debugData');
        $routes->post('confirm-to-asset/(:num)', 'UnitAssetController::confirmToAsset/$1');
    });

    // Purchase Order Verification Routes for Warehouse
    $routes->group('purchase-orders', static function ($routes) {
        $routes->get('/', 'WarehousePO::index');
        $routes->get('po-unit', 'WarehousePO::poUnit');
        $routes->get('print-po-units', 'WarehousePO::printPOUnits');
        $routes->get('po-attachment', 'WarehousePO::poAttachment');
        $routes->get('po-sparepart', 'WarehousePO::poSparepart');
        $routes->post('verify-po-unit', 'WarehousePO::verifyPoUnit');
        $routes->post('verify-po-attachment', 'WarehousePO::verifyPoAttachment');
        $routes->post('verify-po-sparepart', 'WarehousePO::verifyPoSparepart');
        $routes->post('update-verification', 'WarehousePO::updateVerification');
    });

    // PERBAIKAN: Grup baru untuk Inventory, sejajar dengan purchase-orders
    $routes->group('inventory', static function ($routes) {
        // ATTACHMENT
        $routes->get('invent_attachment', 'Warehouse::inventAttachment');
        $routes->post('invent_attachment', 'Warehouse::inventAttachment'); // Untuk AJAX DataTable
        $routes->get('get-attachment-detail/(:num)', 'Warehouse::getAttachmentDetail/$1'); // Untuk mengambil detail attachment
        $routes->post('update-attachment/(:num)', 'Warehouse::updateAttachment/$1'); // Untuk update attachment

        //SPAREPART
        $routes->get('invent_sparepart', 'Warehouse::inventSparepart');
        $routes->post('invent_sparepart', 'Warehouse::inventSparepart'); // Untuk AJAX DataTable
        $routes->get('get_sparepart/(:num)', 'Warehouse::getInventorySparepart/$1'); // Untuk mengambil data edit
        $routes->post('update_sparepart/(:num)', 'Warehouse::updateInventorySparepart/$1'); // Untuk menyimpan data edit

        //unit
        $routes->get('invent_unit', 'Warehouse::inventUnit'); // Halaman utama (GET)
        $routes->post('invent_unit', 'Warehouse::inventUnit'); // <--- Tambahkan ini untuk AJAX (POST)
        $routes->get('get-unit-detail/(:num)', 'Warehouse::getUnitDetail/$1');
        $routes->get('get-unit-full-detail/(:num)', 'Warehouse::getUnitFullDetail/$1'); // Full detail with all joins
        $routes->post('update-unit/(:num)', 'Warehouse::updateUnit/$1');
        $routes->post('delete-unit/(:num)', 'Warehouse::deleteUnit/$1');
    $routes->get('export-invent-unit', 'Warehouse::exportInventUnit');
    $routes->post('confirm-to-asset/(:num)', 'Warehouse::confirmUnitToAsset/$1');
        $routes->get('debug-invent-unit', 'Warehouse::debugInventUnit'); // Debug endpoint
    });
});

// Purchasing Division Routes
$routes->group('purchasing', static function ($routes) {
    $routes->get('/', 'Purchasing::index');
    
    // --- Purchase Order Unit Routes ---
    $routes->get('po-unit', 'Purchasing::poUnit'); // Menampilkan halaman utama
    $routes->get('po-unitForm', 'Purchasing::newPoUnit'); // Tampilkan form tambah
    $routes->get('print-po-unit/(:num)', 'Purchasing::printPOUnit/$1'); // Print PO
    $routes->post('reverify-po/(:num)', 'WarehousePO::reverifyPO/$1');
    $routes->post('cancel-po/(:num)', 'WarehousePO::cancelPO/$1');
    $routes->get('get-unit-form', 'Purchasing::getUnitFormFragment');
    
    // --- Form PO Dinamis Routes ---
    $routes->get('form-po', 'Purchasing::formPo'); // Tampilkan form PO dinamis
    $routes->post('store-po-dinamis', 'Purchasing::storePoDinamis'); // Simpan PO dinamis
    $routes->get('api/get-unit-form', 'Purchasing::getUnitFormAPI'); // API untuk load form unit
    $routes->get('api/get-attachment-form', 'Purchasing::getAttachmentFormAPI'); // API untuk load form attachment
    $routes->get('api/get-sparepart-form', 'Purchasing::getSparepartFormAPI'); // API untuk load form sparepart
    

    $routes->post('store-po-unit', 'Purchasing::storePoUnit'); // Simpan data baru  
    $routes->post('save-update-po-unit/(:any)', 'Purchasing::saveUpdatePoUnit/$1'); // Simpan data baru
    $routes->get('edit-po-unit/(:num)', 'Purchasing::editPoUnit/$1'); // Tampilkan form edit
    $routes->post('update-po-unit/(:num)', 'Purchasing::updatePoUnit/$1'); // Proses update
    $routes->delete('delete-po-unit/(:num)', 'Purchasing::deletePoUnit/$1'); // Hapus data

    // API Routes
    $routes->get('view-po-attachment/(:num)', 'Purchasing::viewPoAttachment/$1'); // View Detail
    $routes->get('api/po-unit/(:num)', 'Purchasing::getDetailPOAPI/$1');
    $routes->match(['get', 'post'], 'api/get-data-po/(:any)', 'Purchasing::getDataPOAPI/$1');
    $routes->get('api/get_model_unit_merk', 'Purchasing::getModelUnitMerk');
    // Added route for tipe & jenis cascading dropdown
    $routes->get('api/get-tipe-units', 'Purchasing::apiGetTipeUnits');
    // API untuk cascading dropdown attachment
    $routes->get('api/get-attachment-merk', 'Purchasing::getAttachmentMerkAPI');
    $routes->get('api/get-attachment-model', 'Purchasing::getAttachmentModelAPI');
    
    // Purchase Order Attachment & Battery Routes
    $routes->get('po-attachment', 'Purchasing::poAttachment');
    $routes->get('po-attachmentForm', 'Purchasing::newPoAttachment'); // Tampilkan form tambah
    $routes->get('print-po-attachment/(:num)', 'Purchasing::printPOAttachment/$1'); // Print PO

    $routes->post('po-attachment', 'Purchasing::poAttachment'); // Untuk DataTable AJAX 
    $routes->post('store-po-attachment', 'Purchasing::storePoAttachment'); // Simpan data baru
    $routes->post('save-update-po-attachment/(:any)', 'Purchasing::saveUpdatePoAttachment/$1'); // Simpan update
    $routes->get('edit-po-attachment/(:num)', 'Purchasing::editPoAttachment/$1'); // Tampilkan form edit
    $routes->delete('delete-po-attachment/(:num)', 'Purchasing::deletePoAttachment/$1'); // Hapus data
    $routes->post('resolve-po-attachment/(:num)', 'Purchasing::resolvePoAttachment/$1'); // Selesaikan


    // API Routes for Attachment
    $routes->get('api/po-attachment/(:num)', 'Purchasing::getDetailPOAttachmentAPI/$1');
    
    // Purchase Order Sparepart Routes
    $routes->get('po-sparepart', 'Purchasing::poSparepart');
    $routes->post('po-sparepart', 'Purchasing::poSparepart'); // For DataTable AJAX
    $routes->get('po-sparepartForm', 'Purchasing::poSparepartForm'); // Menampilkan form create
    $routes->post('store-po-sparepart', 'Purchasing::storePoSparepart'); // Menyimpan data baru
    $routes->get('view-po-sparepart/(:num)', 'Purchasing::viewPoSparepart/$1'); // Untuk AJAX view detail
    $routes->get('edit-po-sparepart/(:num)', 'Purchasing::editPoSparepart/$1'); // Menampilkan form edit
    $routes->post('update-po-sparepart/(:num)', 'Purchasing::updatePoSparepart/$1'); // Menyimpan data update
    $routes->post('delete-po-sparepart/(:num)', 'Purchasing::deletePoSparepart/$1'); // Untuk AJAX delete
    $routes->get('export-po-sparepart', 'Purchasing::exportPoSparepart');
    $routes->post('resolve-po-sparepart/(:num)', 'Purchasing::resolvePoSparepart/$1'); //resolve
    
    // Notification Routes
    $routes->get('notifications', 'Purchasing::getNotifications');
    $routes->post('mark-notification-read/(:num)', 'Purchasing::markNotificationRead/$1');
    
});

// Database Fix Routes
$routes->get('fix-status-column', 'DatabaseFix::fixStatusColumn');
$routes->get('add-sample-data', 'DatabaseFix::addSampleData');

// Customer Management Routes
$routes->group('customers', static function ($routes) {
    $routes->get('/', 'Customers::index');
    $routes->get('create', 'Customers::create');
    $routes->post('store', 'Customers::store');
    $routes->get('edit/(:num)', 'Customers::edit/$1');
    $routes->post('update/(:num)', 'Customers::update/$1');
    $routes->post('delete/(:num)', 'Customers::delete/$1');
    $routes->post('list', 'Customers::getCustomerList');
});

// Finance Management Routes
$routes->group('finance', static function ($routes) {
    $routes->get('/', 'Finance::index');
    $routes->get('invoices', 'Finance::invoices');
    $routes->get('payments', 'Finance::payments');
    $routes->get('expenses', 'Finance::expenses');
    $routes->get('reports', 'Finance::reports');
    $routes->post('invoices/create', 'Finance::createInvoice');
    $routes->post('payments/update/(:num)', 'Finance::updatePaymentStatus/$1');
});

// Reports Routes
$routes->group('reports', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Reports::index');
    $routes->get('rental', 'Reports::rental');
    $routes->get('maintenance', 'Reports::maintenance');
    $routes->get('financial', 'Reports::financial');
    $routes->get('inventory', 'Reports::inventory');
    $routes->get('custom', 'Reports::custom');
    
    // Report Generation
    $routes->post('generate', 'Reports::generateReport');
    $routes->post('quick/(:segment)', 'Reports::quickReport/$1');
    
    // Report Management
    $routes->get('download/(:num)', 'Reports::download/$1');
    $routes->get('view/(:num)', 'Reports::view/$1');
    $routes->delete('delete/(:num)', 'Reports::delete/$1');
});

// Administration Routes
$routes->group('admin', static function ($routes) {
 
    // System Administration
    $routes->get('/', 'Admin::index');
    $routes->get('settings', 'Admin::settings');
    $routes->get('configuration', 'Admin::configuration');
    $routes->post('settings/update', 'Admin::updateSettings');
    $routes->post('configuration/update', 'Admin::updateConfiguration');
    $routes->post('backup', 'Admin::systemBackup');
    $routes->post('restore', 'Admin::systemRestore');
    
    // User Management Routes - Redirect to Advanced User Management
    $routes->group('users', static function ($routes) {
        $routes->get('/', function() {
            return redirect()->to('/admin/advanced-users');
        });
        $routes->get('create', function() {
            return redirect()->to('/admin/advanced-users/create');
        });
        $routes->get('edit/(:num)', function($id) {
            return redirect()->to('/admin/advanced-users/edit/' . $id);
        });
        // Keep some API endpoints that might be needed
        $routes->post('getUserList', 'Admin\UserManagement::getUserList');
        $routes->post('checkUsername', 'Admin\UserManagement::checkUsername');
        $routes->post('checkEmail', 'Admin\UserManagement::checkEmail');
    });
    
    // Advanced User Management Routes
    $routes->group('advanced-users', static function ($routes) {
        $routes->get('/', 'Admin\AdvancedUserManagement::index');
        $routes->post('getDataTable', 'Admin\AdvancedUserManagement::getDataTable');
        $routes->get('create', 'Admin\AdvancedUserManagement::create');
        $routes->post('store', 'Admin\AdvancedUserManagement::store');
        $routes->get('show/(:num)', 'Admin\AdvancedUserManagement::show/$1');
        $routes->get('edit/(:num)', 'Admin\AdvancedUserManagement::edit/$1');
        $routes->post('update/(:num)', 'Admin\AdvancedUserManagement::update/$1');
        $routes->put('update/(:num)', 'Admin\AdvancedUserManagement::update/$1');
        $routes->delete('delete/(:num)', 'Admin\AdvancedUserManagement::delete/$1');
        $routes->post('delete/(:num)', 'Admin\AdvancedUserManagement::delete/$1'); // Alternative for compatibility
        $routes->get('export', 'Admin\AdvancedUserManagement::export');
        $routes->post('clean-expired', 'Admin\AdvancedUserManagement::cleanExpired');
        $routes->get('user-matrix/(:num)', 'Admin\AdvancedUserManagement::userMatrix/$1');
        // // Permission management routes
        
        // $routes->post('quick-assign-permission', 'Admin\AdvancedUserManagement::quickAssignPermission');
        // $routes->post('bulk-assign-permissions', 'Admin\AdvancedUserManagement::bulkAssignPermissions');
        
        // Division management routes
        $routes->get('division/(:num)', 'Admin\AdvancedUserManagement::divisionUsers/$1');
        $routes->get('division-users/(:num)', 'Admin\AdvancedUserManagement::divisionUsers/$1');
        
        // Legacy routes (keep for compatibility)
        $routes->get('permissions/(:num)', 'Admin\AdvancedUserManagement::viewUserPermissions/$1');
        $routes->post('permissions/update/(:num)', 'Admin\AdvancedUserManagement::updateUserPermissions/$1');
        $routes->post('roles/assign/(:num)', 'Admin\AdvancedUserManagement::assignRoleToUser/$1');
        $routes->post('divisions/assign/(:num)', 'Admin\AdvancedUserManagement::assignDivisionToUser/$1');
        $routes->post('positions/assign/(:num)', 'Admin\AdvancedUserManagement::assignPositionToUser/$1');
        $routes->post('bulk-roles', 'Admin\AdvancedUserManagement::bulkAssignRoles');
        $routes->post('bulk-divisions', 'Admin\AdvancedUserManagement::bulkAssignDivisions');
        $routes->get('user-data/(:num)', 'Admin\AdvancedUserManagement::getUserData/$1');
        $routes->get('divisions/json', 'Admin\AdvancedUserManagement::getDivisionsJson');
        $routes->get('positions/json', 'Admin\AdvancedUserManagement::getPositionsJson');
        $routes->get('permissions/json', 'Admin\AdvancedUserManagement::getPermissionsJson');
        $routes->get('export/users', 'Admin\AdvancedUserManagement::exportUsers');
    });
    
    
    $routes->group('permissions', static function ($routes) {
        $routes->get('/', 'Admin\PermissionController::index');
        $routes->get('list', 'Admin\PermissionController::list');
        $routes->post('getDataTable', 'Admin\PermissionController::getDataTable'); // ✓ Sudah benar
        $routes->post('store', 'Admin\PermissionController::store');
        $routes->get('show/(:num)', 'Admin\PermissionController::show/$1');
        $routes->get('getDetail/(:num)', 'Admin\PermissionController::getDetail/$1');
        $routes->post('update/(:num)', 'Admin\PermissionController::update/$1');
        $routes->post('delete/(:num)', 'Admin\PermissionController::delete/$1');
        $routes->delete('delete/(:num)', 'Admin\PermissionController::delete/$1');
        $routes->get('usage/(:num)', 'Admin\PermissionController::usage/$1');
        $routes->get('byModule/(:segment)', 'Admin\PermissionController::byModule/$1');
    });
    
    // Role Management
    $routes->group('roles', static function ($routes) {
        $routes->get('/', 'Admin\RoleController::index');
        $routes->post('store', 'Admin\RoleController::store');
        $routes->get('show/(:num)', 'Admin\RoleController::show/$1');
        $routes->post('update/(:num)', 'Admin\RoleController::update/$1');
        $routes->delete('delete/(:num)', 'Admin\RoleController::delete/$1');
        $routes->post('getDataTable', 'Admin\RoleController::getDataTable');
    });
    
    // Activity Log Routes
    $routes->group('activity-logs', ['filter' => 'permission:logs.view'], static function ($routes) {
        $routes->get('/', 'ActivityLogController::index');
        $routes->get('list', 'ActivityLogController::list');
        $routes->get('export', 'ActivityLogController::export');
        $routes->get('detail/(:num)', 'ActivityLogController::detail/$1');
        $routes->get('summary', 'ActivityLogController::summary');
        $routes->get('recent', 'ActivityLogController::recent');
        $routes->post('clean', 'ActivityLogController::clean');
        $routes->get('getActions', 'ActivityLogController::getActions');
        $routes->get('getEntityTypes', 'ActivityLogController::getEntityTypes');
        $routes->get('statistics', 'ActivityLogController::statistics');
    });
    
    // Division Management Routes
    $routes->group('divisions', ['filter' => 'permission:divisions.view'], static function ($routes) {
        $routes->get('/', 'DivisionController::index');
        $routes->get('list', 'DivisionController::list');
        $routes->get('create', 'DivisionController::create');
        $routes->post('create', 'DivisionController::create');
        $routes->get('edit/(:num)', 'DivisionController::edit/$1');
        $routes->post('edit/(:num)', 'DivisionController::edit/$1');
        $routes->delete('delete/(:num)', 'DivisionController::delete/$1');
        $routes->get('getDivision/(:num)', 'DivisionController::getDivision/$1');
        $routes->post('addUser/(:num)', 'DivisionController::addUser/$1');
        $routes->delete('removeUser/(:num)', 'DivisionController::removeUser/$1');
    });
    
    // Position Management Routes
    $routes->group('positions', ['filter' => 'permission:positions.view'], static function ($routes) {
        $routes->get('/', 'PositionController::index');
        $routes->get('list', 'PositionController::list');
        $routes->get('create', 'PositionController::create');
        $routes->post('create', 'PositionController::create');
        $routes->get('edit/(:num)', 'PositionController::edit/$1');
        $routes->post('edit/(:num)', 'PositionController::edit/$1');
        $routes->delete('delete/(:num)', 'PositionController::delete/$1');
        $routes->get('getPosition/(:num)', 'PositionController::getPosition/$1');
        $routes->post('addUser/(:num)', 'PositionController::addUser/$1');
        $routes->delete('removeUser/(:num)', 'PositionController::removeUser/$1');
    });
});

// API Routes untuk AJAX calls
$routes->group('api', static function ($routes) {
    // Service API  
    $routes->post('workorders/list', 'Service::workOrderList');
    $routes->post('workorders/create', 'Service::workOrderCreate');
    $routes->get('workorders/get/(:num)', 'Service::workOrderGet/$1');
    $routes->post('workorders/update', 'Service::workOrderUpdate');
    $routes->post('workorders/delete', 'Service::workOrderDelete');
    
    $routes->get('realtime-data', 'ApiController::getRealtimeData');
    $routes->get('get-unit-form', 'Purchasing::getUnitFormFragment');
    
    // Customer API
    $routes->post('customers/list', 'Customers::getCustomerList');
    
    // Finance API
    $routes->post('finance/invoices/create', 'Finance::createInvoice');
    $routes->post('finance/payments/update/(:num)', 'Finance::updatePaymentStatus/$1');
    
    // Reports API
    $routes->post('reports/generate', 'Reports::generateReport');
    $routes->post('reports/schedule', 'Reports::scheduleReport');
    
    // Unit Assets Form API
    $routes->get('merk', 'ApiController::getMerk');
    $routes->get('models/(:segment)', 'ApiController::getModelsByMerk/$1');
    $routes->get('models-by-merk/(:segment)', 'ApiController::getModelsByMerk/$1');
    $routes->get('form-data', 'ApiController::getFormData');
    $routes->get('dropdown/(:segment)', 'ApiController::getDropdownData/$1');
});

// System Routes (duplicate cleanup)
$routes->get('profile', 'System::profile');
$routes->get('settings', 'System::settings');
$routes->get('notifications', 'System::notifications');
$routes->get('help', 'System::help');
$routes->get('logout', 'System::logout');

// API Routes for Form Data
$routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('merk', 'ApiController::getMerk');
    $routes->get('models-by-merk/(:segment)', 'ApiController::getModelsByMerk/$1');
    $routes->get('form-data', 'ApiController::getFormData');
    $routes->get('dropdown/(:segment)', 'ApiController::getDropdownData/$1');
});

// Notifications Routes
$routes->group('notifications', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('/', 'Notifications::index');
    $routes->get('stream', 'Notifications::stream');
    $routes->post('create', 'Notifications::create');
    $routes->post('mark-read/(:num)', 'Notifications::markAsRead/$1');
    $routes->post('mark-all-read', 'Notifications::markAllAsRead');
    $routes->delete('delete/(:num)', 'Notifications::delete/$1');
    $routes->get('count', 'Notifications::getCount');
});

$routes->group('api', function($routes) {
    $routes->get('notifications', 'Api\NotificationController::index');
    $routes->post('notifications/read/(:num)', 'Api\NotificationController::markAsRead/$1');
    $routes->get('notifications/count', 'Api\NotificationController::getCount');
});

// SPK PDF routes (top-level)
$routes->get('marketing/spk/pdf/(:num)', 'Marketing::spkPdf/$1');
$routes->get('service/spk/pdf/(:num)', 'Service::spkPdf/$1');

// SPK Print (HTML) routes (top-level)
$routes->get('marketing/spk/print/(:num)', 'Marketing::spkPrint/$1');
$routes->get('service/spk/print/(:num)', 'Service::spkPrint/$1');

$routes->group('warehouse/inventory', static function($r){
    $r->get('available-attachments', 'Warehouse\InventoryApi::availableAttachments');
    $r->get('available-chargers', 'Warehouse\InventoryApi::availableChargers');
    $r->get('available-batteries', 'Warehouse\InventoryApi::availableBatteries');
    $r->get('unit-components', 'Warehouse\InventoryApi::getUnitComponents');
    $r->post('replace-component', 'Warehouse\InventoryApi::replaceComponent');
});




