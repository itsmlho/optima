<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// Default route - redirect based on login status
$routes->get('/', function() {
    $session = session();
    if ($session->get('isLoggedIn')) {
        return redirect()->to('/welcome');
    }
    return redirect()->to('/auth/login');
});

// Welcome page - requires authentication
$routes->get('welcome', 'Welcome::index');

$routes->get('/comingsoon', '::index');

// Health Check & Monitoring Routes
$routes->group('health', static function ($routes) {
    $routes->get('/', 'HealthController::check');
    $routes->get('ping', 'HealthController::ping');
    $routes->get('info', 'HealthController::info');
    $routes->get('performance', 'HealthController::performance');
});

// Authentication Routes
$routes->group('auth', static function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('login', 'Auth::login');
    $routes->post('attempt-login', 'Auth::attemptLogin');
    $routes->get('register', 'Auth::register');
    $routes->post('attempt-register', 'Auth::attemptRegister');
    $routes->get('verify-email/(:any)', 'Auth::verifyEmail/$1');
    $routes->get('waiting-approval', 'Auth::waitingApproval');
    $routes->get('forgot-password', 'Auth::forgotPassword');
    $routes->post('send-reset-link', 'Auth::sendResetLink');
    $routes->get('reset-password/(:any)', 'Auth::resetPassword/$1');
    $routes->post('update-password', 'Auth::updatePassword');
    // OTP Routes
    $routes->get('verify-otp', 'Auth::verifyOtpPage');
    $routes->post('verify-otp', 'Auth::verifyOtp');
    $routes->post('resend-otp', 'Auth::resendOtp');
    // Session Management Routes
    $routes->post('logout-session/(:segment)', 'Auth::logoutSession/$1');
    $routes->post('logout-all-sessions', 'Auth::logoutAllSessions');
    $routes->get('logout', 'Auth::logout');
    $routes->get('profile', 'Auth::profile');
    $routes->post('update-profile', 'Auth::updateProfile');
    $routes->post('change-password', 'Auth::changePassword');
    $routes->post('toggle-otp', 'Auth::toggleOtp');
    // Get positions by division (AJAX)
    $routes->post('get-positions-by-division', 'Auth::getPositionsByDivision');
});

// Dashboard routes for different divisions
$routes->group('dashboard', static function ($routes) {
    $routes->get('', 'Dashboard::index');
    $routes->get('service', 'Dashboard::service');
    $routes->get('marketing', 'Dashboard::marketing');
    $routes->get('rolling', 'Dashboard::rolling');
    $routes->get('warehouse', 'Dashboard::warehouse');
}); 

// System routes for topbar functionality
$routes->get('/profile', 'System::profile');
$routes->post('/profile/update', 'System::updateProfile');
$routes->post('/profile/change-password', 'System::changePassword');
$routes->post('/profile/upload-avatar', 'System::uploadAvatar');
$routes->post('/profile/toggle-otp', 'System::toggleOtp');


$routes->get('/settings', 'System::settings');
$routes->get('/help', 'System::help');
$routes->get('/logout', 'System::logout');

// Apps routes
$routes->group('apps', static function ($routes) {
    $routes->get('calendar', 'Apps::calendar');
    $routes->get('messages', 'Apps::messages');
    $routes->get('settings', 'Apps::settings');
    $routes->get('analytics', 'Apps::analytics');
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
        $routes->get('customers', 'Kontrak::getCustomers');
        $routes->get('locations/(:num)', 'Kontrak::getLocationsByCustomer/$1');
        
        // Spesifikasi management
        $routes->get('spesifikasi/(:num)', 'Kontrak::spesifikasi/$1');
        $routes->post('add-spesifikasi', 'Kontrak::addSpesifikasi');
        $routes->get('spesifikasi-detail/(:num)', 'Kontrak::spesifikasiDetail/$1');
        $routes->post('update-spesifikasi/(:num)', 'Kontrak::updateSpesifikasi/$1');
        $routes->post('delete-spesifikasi/(:num)', 'Kontrak::deleteSpesifikasi/$1');
        $routes->get('available-units/(:num)', 'Kontrak::getAvailableUnits/$1');
        $routes->post('assign-units', 'Kontrak::assignUnitsToSpesifikasi');
        
        // Inventory status monitoring
        $routes->get('inventory-status/(:num)', 'Kontrak::getInventoryStatus/$1');
        $routes->post('bulk-fix-inventory-status', 'Kontrak::bulkFixInventoryStatus');
        $routes->post('trigger-status-update/(:num)', 'Kontrak::triggerStatusUpdateAfterWorkflow/$1');
        $routes->post('link-fabrication-attachments/(:num)', 'Kontrak::linkFabricationAttachments/$1');
        
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
    $routes->post('spk/update/(:num)', 'Marketing::spkUpdate/$1');
    $routes->post('spk/update-status/(:num)', 'Marketing::spkUpdateStatus/$1');
    $routes->post('spk/delete/(:num)', 'Marketing::spkDelete/$1');
    $routes->post('spk/cleanup-zero', 'Marketing::cleanupSpkZero');
    
    // DI (Delivery Instruction) - Marketing creates
    $routes->post('di/create', 'Marketing::diCreate');
    $routes->post('di/delete/(:num)', 'Marketing::diDelete/$1');
    // Marketing DI page & APIs
    $routes->get('di', 'Marketing::di');
    $routes->get('di/getData', 'Marketing::getDIData');
    $routes->get('di/list', 'Marketing::diList');
    $routes->get('di/detail/(:num)', 'Marketing::diDetail/$1');
    $routes->get('spk/ready-options', 'Marketing::spkReadyOptions');
    
    // Workflow API endpoints
    $routes->get('get-jenis-perintah-kerja', 'Marketing::getJenisPerintahKerja');
    $routes->get('get-tujuan-perintah-kerja', 'Marketing::getTujuanPerintahKerja');
    
    $routes->get('penawaran', 'Marketing::penawaran');
    // $routes->get('list-unit', 'Marketing::listUnit');
    $routes->get('unit-tersedia', 'Marketing::unitTersedia');
    $routes->get('unitmarketing', 'Marketing::unitMarketing');
    $routes->get('available-units', 'Marketing::availableUnits');
    $routes->post('available-units/data', 'Marketing::availableUnitsData');
    $routes->get('unit-detail/(:num)', 'Marketing::unitDetail/$1');
    $routes->get('spk/pdf/(:num)', 'Marketing::spkPdf/$1');
    
    // Kontrak routes in marketing
    $routes->get('kontrak', 'Marketing::kontrak');
    $routes->get('kontrak/getData', 'Marketing::getData');
    $routes->post('kontrak/getDataTable', 'Marketing::getDataTable');
    $routes->get('kontrak/units/(:num)', 'Marketing::kontrakUnits/$1');
    $routes->get('kontrak/customer-locations/(:num)', 'Marketing::customerLocations/$1');
    // Route removed - handled by kontrak/detail/(:num) in kontrak group (Kontrak::detail)
    // All kontrak CRUD operations moved to Kontrak controller for better structure
    // Routes: marketing/kontrak/customers -> kontrak/customers
    // Routes: marketing/kontrak/locations/(:num) -> kontrak/locations/(:num)
    // Routes: marketing/kontrak/store -> kontrak/store
    // Routes: marketing/kontrak/update/(:num) -> kontrak/update/(:num)
    // Routes: marketing/kontrak/delete/(:num) -> kontrak/delete/(:num)
    
    // SPK routes in marketing
    $routes->post('spk/create', 'Marketing::spkCreate');

    // Customer Management Routes
    $routes->group('customer-management', static function ($routes) {
        $routes->get('/', 'CustomerManagementController::index');
        // Customers
        $routes->post('getCustomers', 'CustomerManagementController::getCustomers');
        $routes->get('getCustomerStats', 'CustomerManagementController::getCustomerStats');
        $routes->get('getAreas', 'CustomerManagementController::getAreas');
        // Dropdown data for spesifikasi
        $routes->get('getDepartemen', 'CustomerManagementController::getDepartemen');
        $routes->get('getTipeUnit', 'CustomerManagementController::getTipeUnit');
        $routes->get('getKapasitas', 'CustomerManagementController::getKapasitas');
        $routes->get('getMerkUnit', 'CustomerManagementController::getMerkUnit');
        $routes->get('getJenisBaterai', 'CustomerManagementController::getJenisBaterai');
        $routes->get('getCharger', 'CustomerManagementController::getCharger');
        $routes->get('getAttachmentTipe', 'CustomerManagementController::getAttachmentTipe');
        $routes->get('getValve', 'CustomerManagementController::getValve');
        $routes->get('getMast', 'CustomerManagementController::getMast');
        $routes->get('getBan', 'CustomerManagementController::getBan');
        $routes->get('getRoda', 'CustomerManagementController::getRoda');
        $routes->get('getCustomerContracts/(:num)', 'CustomerManagementController::getCustomerContracts/$1');
        $routes->get('getCustomerLocations/(:num)', 'CustomerManagementController::getCustomerLocations/$1');
        $routes->get('showCustomer/(:num)', 'CustomerManagementController::showCustomer/$1');
        $routes->get('getCustomerDetail/(:num)', 'CustomerManagementController::getCustomerDetail/$1');
        $routes->post('storeCustomer', 'CustomerManagementController::storeCustomer');
        $routes->post('updateCustomer/(:num)', 'CustomerManagementController::updateCustomer/$1');
        $routes->delete('deleteCustomer/(:num)', 'CustomerManagementController::deleteCustomer/$1');
        $routes->post('deleteCustomer/(:num)', 'CustomerManagementController::deleteCustomer/$1'); // Fallback
        // Customer Locations
        $routes->post('storeCustomerLocation', 'CustomerManagementController::storeCustomerLocation');
        $routes->post('updateCustomerLocation/(:num)', 'CustomerManagementController::updateCustomerLocation/$1');
        $routes->get('showCustomerLocation/(:num)', 'CustomerManagementController::showCustomerLocation/$1');
        $routes->delete('deleteCustomerLocation/(:num)', 'CustomerManagementController::deleteCustomerLocation/$1');
        $routes->post('deleteCustomerLocation/(:num)', 'CustomerManagementController::deleteCustomerLocation/$1'); // Fallback
        // Customer Contracts
        $routes->post('getCustomerContracts', 'CustomerManagementController::getCustomerContracts');
        $routes->get('showCustomerContract/(:num)', 'CustomerManagementController::showCustomerContract/$1');
        $routes->post('storeCustomerContract', 'CustomerManagementController::storeCustomerContract');
        $routes->post('updateCustomerContract/(:num)', 'CustomerManagementController::updateCustomerContract/$1');
        $routes->delete('deleteCustomerContract/(:num)', 'CustomerManagementController::deleteCustomerContract/$1');
        $routes->post('deleteCustomerContract/(:num)', 'CustomerManagementController::deleteCustomerContract/$1'); // Fallback
        
        // PDF Generation
        $routes->get('generatePDF/(:num)', 'CustomerManagementController::generateCustomerPDF/$1');
        
        // Locations
        $routes->get('getLocations/(:num)', 'CustomerManagementController::getLocations/$1');
        $routes->get('getLocation/(:num)', 'CustomerManagementController::getLocation/$1');
        $routes->post('storeLocation', 'CustomerManagementController::storeLocation');
        $routes->post('updateLocation/(:num)', 'CustomerManagementController::updateLocation/$1');
        $routes->delete('deleteLocation/(:num)', 'CustomerManagementController::deleteLocation/$1');
        $routes->post('deleteLocation/(:num)', 'CustomerManagementController::deleteLocation/$1'); // Fallback
        
        // Stats & Dropdown
        $routes->get('getStats', 'CustomerManagementController::getStats');
        $routes->get('getCustomersDropdown', 'CustomerManagementController::getCustomersDropdown');
        $routes->get('getAreasDropdown', 'CustomerManagementController::getAreasDropdown');
    });

    // Routes removed - functionality moved to CustomerManagementController

    // Export routes
    $routes->get('export_kontrak', 'Marketing::exportKontrak');
    $routes->get('export_customer', 'Marketing::exportCustomer');

});

// Service Division Routes
$routes->group('service', static function ($routes) {
    $routes->get('/', 'Service::index');
    $routes->get('work-orders', 'Service::workOrders');
    $routes->post('work-orders/data', 'WorkOrderController::getWorkOrders');
    $routes->get('work-orders/stats', 'WorkOrderController::getStats');
    $routes->post('work-orders/update-status', 'WorkOrderController::updateStatus');
    $routes->get('work-orders/history', 'Service::workOrderHistory');
    // Work Order CRUD routes
    $routes->post('work-orders/store', 'WorkOrderController::store');
    $routes->get('work-orders/view/(:num)', 'WorkOrderController::view/$1');
    $routes->get('work-orders/edit/(:num)', 'WorkOrderController::edit/$1');
    $routes->get('work-orders/print/(:num)', 'WorkOrderController::print/$1');
    $routes->post('work-orders/update/(:num)', 'WorkOrderController::update/$1');
    $routes->delete('work-orders/delete/(:num)', 'WorkOrderController::delete/$1');
    // Work Order assignment routes
    $routes->post('work-orders/assign-employees', 'WorkOrderController::assignEmployees');
    $routes->post('work-orders/close', 'WorkOrderController::closeWorkOrder');
    // Work Order sparepart routes
    $routes->get('work-orders/spareparts/(:num)', 'WorkOrderController::getWorkOrderSpareparts/$1');
    // Work Order utility routes
    $routes->get('work-orders/generate-number', 'WorkOrderController::generateNumber');
    $routes->post('work-orders/get-subcategories', 'WorkOrderController::getSubcategories');
    $routes->post('service/work-orders/get-subcategories', 'WorkOrderController::getSubcategories'); // Menambahkan route dengan prefix service
    $routes->get('work-orders/units-dropdown', 'WorkOrderController::getUnitsDropdown');
    $routes->get('service/work-orders/units-dropdown', 'WorkOrderController::getUnitsDropdown');
    $routes->post('work-orders/search-units', 'WorkOrderController::searchUnits');
    $routes->post('work-orders/get-subcategory-priority', 'WorkOrderController::getSubcategoryPriority');
    $routes->post('service/work-orders/get-subcategory-priority', 'WorkOrderController::getSubcategoryPriority');
    $routes->post('work-orders/get-priority', 'WorkOrderController::getPriority');
    $routes->post('service/work-orders/get-priority', 'WorkOrderController::getPriority');
    $routes->get('work-orders/print/(:num)', 'WorkOrderController::print/$1');
    $routes->get('work-orders/export', 'WorkOrderController::export');
    // Work Order area and sparepart endpoints
    $routes->post('work-orders/get-unit-area', 'WorkOrderController::getUnitArea');
    $routes->get('work-orders/spareparts-dropdown', 'WorkOrderController::sparepartsDropdown');
    // Unit Verification and Sparepart Usage endpoints
    $routes->get('work-orders/test-routing', 'WorkOrderController::testRouting');
    $routes->post('work-orders/get-unit-verification-data', 'WorkOrderController::getUnitVerificationData');
    $routes->post('work-orders/save-unit-verification', 'WorkOrderController::saveUnitVerification');
    $routes->get('work-orders/print-verification', 'Service::printVerification');
    $routes->get('print-verification', 'Service::printVerification'); // Add direct route
    $routes->post('work-orders/get-sparepart-usage-data', 'WorkOrderController::getSparepartUsageData');
    $routes->post('work-orders/save-sparepart-usage', 'WorkOrderController::saveSparepartUsage');
    // Sparepart Validation endpoints
    $routes->get('work-orders/get-sparepart-validation-data', 'WorkOrderController::getSparepartValidationData');
    $routes->get('work-orders/get-sparepart-master', 'WorkOrderController::getSparepartMaster');
    $routes->post('work-orders/save-sparepart-validation', 'WorkOrderController::saveSparepartValidation');
    $routes->post('work-orders/staff-dropdown', 'WorkOrderController::staffDropdown');
    $routes->post('work-orders/get-area-staff', 'WorkOrderController::getAreaStaff');
    $routes->post('service/work-orders/get-area-staff', 'WorkOrderController::getAreaStaff');
    // PMP and other service pages
    $routes->get('pmps', 'Service::pmps');
        $routes->get('data-unit', 'Service::dataUnit');
        $routes->get('areas', 'Service::areas');
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
    $routes->get('spk/print/(:num)', 'Service::spkPrint/$1');
    $routes->post('spk/update-status/(:num)', 'Service::spkUpdateStatus/$1');
    $routes->post('spk/approve-stage/(:num)', 'Service::spkApproveStage/$1');
    $routes->post('spk/approve-fabrikasi', 'Service::approveFabrikasi');
    $routes->post('spk/assign-items', 'Service::assignItems');
    $routes->get('spk/stage-status/(:num)', 'Service::getSpkStageStatus/$1');
    $routes->get('spk/units-with-edit/(:num)', 'Service::getSpkUnitsWithEdit/$1');
    $routes->post('spk/confirm-ready/(:num)', 'Service::spkConfirmReady/$1');
    
    // Edit system routes
    $routes->get('spk/edit-options/(:num)', 'Service::getSpkEditOptions/$1');
    $routes->post('spk/edit-stage/(:num)', 'Service::editSpkStage/$1');
    $routes->post('spk/change-unit/(:num)', 'Service::changeSpkUnit/$1');
    $routes->get('spk/units-with-edit/(:num)', 'Service::getSpkUnitsWithEdit/$1');
    // Simple unit search (for DI prepare)
    $routes->get('data-unit/simple', 'Service::dataUnitSimple');
    // Attachment simple search
    $routes->get('data-attachment/simple', 'Service::dataAttachmentSimple');
    // Add new inventory attachment
    $routes->post('add-inventory-attachment', 'Service::addInventoryAttachment');
    // Master data endpoints for modal dropdowns
    $routes->get('master-attachment', 'Service::getMasterAttachment');
    $routes->get('master-baterai', 'Service::getMasterBaterai');
    $routes->get('master-charger', 'Service::getMasterCharger');
    // Check if no_unit already exists
    $routes->post('check-no-unit-exists', 'Service::checkNoUnitExists');
    // Smart Component Management - Unit component data endpoint
    $routes->get('unit-component-data/(:num)', 'Service::unitComponentData/$1');
    // Assign selected items (unit+attachment) to SPK and mark READY
    $routes->post('spk/assign-items', 'Service::spkAssignItems');
    $routes->get('spk/pdf/(:num)', 'Service::spkPdf/$1');
    
    // Service Area & Employee Management Routes  
    $routes->group('area-management', static function($routes) {
        $routes->get('/', 'ServiceAreaManagementController::index');
        // Areas
        $routes->post('getAreas', 'ServiceAreaManagementController::getAreas');
        $routes->get('showArea/(:num)', 'ServiceAreaManagementController::showArea/$1');
        $routes->post('saveArea', 'ServiceAreaManagementController::saveArea');
        $routes->post('updateArea/(:num)', 'ServiceAreaManagementController::updateArea/$1');
        $routes->delete('deleteArea/(:num)', 'ServiceAreaManagementController::deleteArea/$1');
        $routes->post('deleteArea/(:num)', 'ServiceAreaManagementController::deleteArea/$1'); // Fallback
        // Employees
        $routes->post('getEmployees', 'ServiceAreaManagementController::getEmployees');
        $routes->get('getEmployeeDetail/(:num)', 'ServiceAreaManagementController::getEmployeeDetail/$1');
        $routes->get('showEmployee/(:num)', 'ServiceAreaManagementController::showEmployee/$1');
        $routes->post('saveEmployee', 'ServiceAreaManagementController::saveEmployee');
        $routes->post('updateEmployee/(:num)', 'ServiceAreaManagementController::updateEmployee/$1');
        $routes->delete('deleteEmployee/(:num)', 'ServiceAreaManagementController::deleteEmployee/$1');
        $routes->post('deleteEmployee/(:num)', 'ServiceAreaManagementController::deleteEmployee/$1'); // Fallback
        // Assignments
        $routes->get('getAreaAssignments/(:num)', 'ServiceAreaManagementController::getAreaAssignments/$1');
        $routes->get('getEmployeeAssignments/(:num)', 'ServiceAreaManagementController::getEmployeeAssignments/$1');
        $routes->post('storeAssignment', 'ServiceAreaManagementController::storeAssignment');
        $routes->post('updateAssignment/(:num)', 'ServiceAreaManagementController::updateAssignment/$1');
        $routes->delete('deleteAssignment/(:num)', 'ServiceAreaManagementController::deleteAssignment/$1');
        $routes->post('deleteAssignment/(:num)', 'ServiceAreaManagementController::deleteAssignment/$1'); // Fallback
        $routes->post('toggleAssignmentStatus/(:num)', 'ServiceAreaManagementController::toggleAssignmentStatus/$1');
        $routes->get('showAssignment/(:num)', 'ServiceAreaManagementController::showAssignment/$1');
        // Availability
        $routes->get('getAvailableEmployees/(:num)', 'ServiceAreaManagementController::getAvailableEmployees/$1');
        $routes->get('getAvailableEmployees/(:num)/(:segment)', 'ServiceAreaManagementController::getAvailableEmployees/$1/$2');
    });

    // Export routes
    $routes->get('export_workorder', 'Service::exportWorkorder');
    $routes->get('export_employee', 'Service::exportEmployee');
    $routes->get('export_area', 'Service::exportArea');
});

// Operational Routes (Delivery)
$routes->group('operational', static function ($routes) {
    $routes->get('delivery', 'Operational::delivery');
    $routes->get('delivery/list', 'Operational::diList');
    $routes->get('delivery/detail/(:num)', 'Operational::diDetail/$1');
    $routes->get('delivery/print/(:num)', 'Operational::diPrint/$1');
    $routes->get('delivery/print-multi/(:num)', 'Operational::diPrintMulti/$1');
    $routes->post('delivery/update-status/(:num)', 'Operational::diUpdateStatus/$1');
    $routes->post('delivery/approve-stage/(:num)', 'Operational::diApproveStage/$1');
    $routes->get('tracking', 'Operational::tracking');
    $routes->post('tracking-search', 'Operational::trackingSearch');
    $routes->post('audit-trail', 'Operational::auditTrail');
    
    // DI Workflow Logic API Routes
    $routes->get('api/jenis-perintah-kerja', 'Operational::getJenisPerintahKerja');
    $routes->get('api/tujuan-perintah-kerja/(:num)', 'Operational::getTujuanPerintahKerja/$1');
    $routes->get('api/tujuan-perintah-kerja', 'Operational::getTujuanPerintahKerja');
    $routes->get('api/available-spk-with-units', 'Operational::getAvailableSpkWithUnits');
    $routes->get('api/contract-units', 'Operational::getContractUnits');
    $routes->get('api/available-units', 'Operational::getAvailableUnits');
    $routes->post('api/validate-di-data', 'Operational::validateDiData');
    $routes->get('api/workflow-info', 'Operational::getWorkflowInfo');
    $routes->post('api/process-workflow-approval', 'Operational::processWorkflowApproval');
});

// Warehouse Routes
$routes->group('warehouse', static function ($routes) {
    $routes->get('/', 'Warehouse::index');
    // $routes->get('sparepart', 'Warehouse::sparepart');
    
    // Purchase Order Verification Routes for Warehouse
    $routes->group('purchase-orders', static function ($routes) {
        $routes->get('/', 'WarehousePO::index');
        // Unified PO Verification Page
        $routes->get('wh-verification', 'WarehousePO::whVerification');
        // Rejected Items Page
        $routes->get('rejected-items', 'WarehousePO::rejectedItems');
        // Re-verification routes
        $routes->post('reverify-unit', 'WarehousePO::reverifyUnit');
        $routes->post('reverify-attachment', 'WarehousePO::reverifyAttachment');
        $routes->post('reverify-sparepart', 'WarehousePO::reverifySparepart');
        // Get unit verification options for dropdowns
        $routes->get('get-unit-verification-options', 'WarehousePO::getUnitVerificationOptions');
        
        // Individual PO Pages (masih bisa diakses langsung)
        $routes->get('po-unit', 'WarehousePO::poUnit');
        $routes->get('print-po-units', 'WarehousePO::printPOUnits');
        $routes->get('po-attachment', 'WarehousePO::poAttachment');
        $routes->get('po-sparepart', 'WarehousePO::poSparepart');
        $routes->post('verify-po-unit', 'WarehousePO::verifyPoUnit');
        $routes->post('verify-po-attachment', 'WarehousePO::verifyPoAttachment');
        $routes->post('verify-po-sparepart', 'WarehousePO::verifyPoSparepart');
        $routes->post('update-verification', 'WarehousePO::updateVerification');
        $routes->get('get-unit-verification-options', 'WarehousePO::getUnitVerificationOptions');
    });

    // Sparepart Usage & Returns Routes (Combined)
    $routes->group('sparepart-usage', static function ($routes) {
        $routes->get('/', 'Warehouse\SparepartUsageController::index');
        $routes->post('get-usage', 'Warehouse\SparepartUsageController::getUsage');
        $routes->get('get-usage-detail/(:num)', 'Warehouse\SparepartUsageController::getUsageDetail/$1');
        $routes->post('get-returns', 'Warehouse\SparepartUsageController::getReturns');
        $routes->get('get-return-detail/(:num)', 'Warehouse\SparepartUsageController::getReturnDetail/$1');
        $routes->post('confirm-return/(:num)', 'Warehouse\SparepartUsageController::confirmReturn/$1');
    });

    // PERBAIKAN: Grup baru untuk Inventory, sejajar dengan purchase-orders
    $routes->group('inventory', static function ($routes) {
        // ATTACHMENT
        $routes->get('invent_attachment', 'Warehouse::inventAttachment');
        $routes->post('invent_attachment', 'Warehouse::inventAttachment'); // Untuk AJAX DataTable
        
        // Separate data endpoints for each item type
        $routes->post('attachment-data', 'Warehouse::attachmentData');
        $routes->post('battery-data', 'Warehouse::batteryData');
        $routes->post('charger-data', 'Warehouse::chargerData');
        
        $routes->get('get-attachment-detail/(:num)', 'Warehouse::getAttachmentDetail/$1'); // Untuk mengambil detail attachment
        $routes->post('update-attachment/(:num)', 'Warehouse::updateAttachment/$1'); // Untuk update attachment
        
        // Master data API endpoints
        $routes->get('master-merk/(:segment)', 'Warehouse::masterMerk/$1');
        $routes->get('master-tipe/(:segment)', 'Warehouse::masterTipe/$1');
        $routes->get('master-jenis/(:segment)', 'Warehouse::masterJenis/$1');
        $routes->get('master-model/(:segment)', 'Warehouse::masterModel/$1');
        $routes->post('save-master-merk/(:segment)', 'Warehouse::saveMasterMerk/$1');
        $routes->post('save-master-tipe/(:segment)', 'Warehouse::saveMasterTipe/$1');
        $routes->post('save-master-jenis/(:segment)', 'Warehouse::saveMasterJenis/$1');
        $routes->post('save-master-model/(:segment)', 'Warehouse::saveMasterModel/$1');
        $routes->post('save-master-data/(:segment)', 'Warehouse::saveMasterData/$1');
        $routes->post('add-inventory-item', 'Warehouse::addInventoryItem');

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
    $routes->get('export_unit_inventory', 'Warehouse::exportUnitInventory');
    $routes->get('export_attachment_inventory', 'Warehouse::exportAttachmentInventory');
    $routes->get('export_battery_inventory', 'Warehouse::exportBatteryInventory');
    $routes->get('export_charger_inventory', 'Warehouse::exportChargerInventory');
    $routes->post('confirm-to-asset/(:num)', 'Warehouse::confirmUnitToAsset/$1');
        $routes->get('debug-invent-unit', 'Warehouse::debugInventUnit'); // Debug endpoint
    });
});

// Purchasing Division Routes
$routes->group('purchasing', static function ($routes) {
    $routes->get('/', 'Purchasing::index'); // Main entry point (redirects to purchasingHub)
    
    // --- Unified Purchasing Hub ---
    $routes->get('purchasing-hub', 'Purchasing::purchasingHub'); // Alias for hub
    
    // --- Supplier Management ---
    $routes->get('supplier-management', 'Purchasing::supplierManagement'); // Supplier DataTable AJAX
    $routes->post('supplier-management', 'Purchasing::supplierManagement'); // Supplier DataTable AJAX
    $routes->get('supplier-form', 'Purchasing::supplierForm'); // Add supplier form
    $routes->get('supplier-form/(:num)', 'Purchasing::supplierForm/$1'); // Edit supplier form (AJAX)
    $routes->post('store-supplier', 'Purchasing::storeSupplier'); // Store/update supplier
    $routes->delete('delete-supplier/(:num)', 'Purchasing::deleteSupplier/$1'); // Delete supplier
    
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
    
    // Unified PO System Routes
    $routes->get('create-po-unified', 'Purchasing::createUnifiedPO'); // Show unified PO form (Unit & Attachment)
    $routes->match(['get', 'post'], 'api/get-unified-po-data', 'Purchasing::getUnifiedPOData');
    $routes->post('store-unified-po', 'Purchasing::storeUnifiedPO');
    
    // PO Sparepart Routes
    $routes->get('po-sparepart-list', 'Purchasing::poSparepartList'); // Show PO Sparepart list page
    $routes->get('create-po-sparepart', 'Purchasing::createPOSparepart'); // Show PO Sparepart form
    $routes->post('store-po-sparepart-unified', 'Purchasing::storePOSparepartUnified');
    $routes->get('api/get-sparepart-po-data', 'Purchasing::getSparepartPOData'); // API for DataTable
    $routes->get('export-sparepart-po', 'Purchasing::exportSparepartPO');
    
    // Supplier Management Routes
    $routes->get('supplier-management-page', 'Purchasing::supplierManagementPage'); // Show Supplier Management page
    $routes->get('generate-supplier-code', 'Purchasing::generateSupplierCode'); // Generate supplier code
    $routes->get('suppliers-list', 'Purchasing::suppliersList'); // Get suppliers list for AJAX
    $routes->post('store-supplier', 'Purchasing::storeSupplier'); // Store new supplier
    $routes->get('get-supplier/(:num)', 'Purchasing::getSupplier/$1'); // Get single supplier
    $routes->post('update-supplier/(:num)', 'Purchasing::updateSupplier/$1'); // Update supplier
    $routes->post('update-supplier-status/(:num)', 'Purchasing::updateSupplierStatus/$1'); // Update supplier status
    $routes->post('delete-supplier/(:num)', 'Purchasing::deleteSupplier/$1'); // Delete supplier
    $routes->get('export-suppliers', 'Purchasing::exportSuppliers');
    $routes->get('view-po/(:num)', 'Purchasing::viewPO/$1');
    $routes->get('api/po-detail/(:num)', 'Purchasing::getPODetail/$1');
    $routes->post('reverify-po/(:num)', 'Purchasing::reverifyPO/$1');
    $routes->post('cancel-po/(:num)', 'Purchasing::cancelPO/$1');
    $routes->post('complete-po/(:num)', 'Purchasing::completePO/$1');
    $routes->delete('delete-po/(:num)', 'Purchasing::deletePO/$1');
    $routes->post('update-delivery-status', 'Purchasing::updateDeliveryStatus');
    $routes->post('verify-delivery-items', 'Purchasing::verifyDeliveryItems');
    $routes->get('api/get-item-form/(:any)', 'Purchasing::getItemForm/$1');
    $routes->get('api/get-model-units', 'Purchasing::getModelUnits');
    $routes->get('api/get-tipe-units/(:num)', 'Purchasing::getTipeUnits/$1');
    $routes->get('api/get-jenis-units/(:num)', 'Purchasing::getJenisUnits/$1');
    $routes->get('api/get-attachment-merks', 'Purchasing::getAttachmentMerks');
    $routes->get('api/get-attachment-models', 'Purchasing::getAttachmentModels');
    $routes->get('api/get-battery-merks', 'Purchasing::getBatteryMerks');
    $routes->get('api/get-battery-jenis', 'Purchasing::getBatteryJenis');
    $routes->get('api/get-battery-tipes', 'Purchasing::getBatteryTipes'); // NEW - for jenis + merk query
    $routes->get('api/get-charger-merks', 'Purchasing::getChargerMerks');
    $routes->get('api/get-charger-models', 'Purchasing::getChargerModels');
    
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
    
    // Notification Routes (moved to global notifications group)
    
    // Export routes
    $routes->get('export_po', 'Purchasing::exportPO');
    $routes->get('export_supplier', 'Purchasing::exportSupplier');
    $routes->get('export_po_progres', 'Purchasing::exportPOProgres');
    $routes->get('export_po_delivery', 'Purchasing::exportPODelivery');
    $routes->get('export_po_completed', 'Purchasing::exportPOCompleted');
    
    // --- Print Routes ---
    $routes->get('print_po/(:num)', 'Purchasing::printPO/$1');
    $routes->get('print-po/(:num)', 'Purchasing::printPO/$1');
    $routes->get('print-packing-list', 'Purchasing::printPackingList');
    
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

// Perizinan Management Routes
$routes->group('perizinan', static function ($routes) {
    $routes->get('silo', 'Perizinan::silo');
    $routes->get('get-silo-list', 'Perizinan::getSiloList');
    $routes->get('get-silo-stats', 'Perizinan::getSiloStats');
    $routes->get('get-available-units', 'Perizinan::getAvailableUnits');
    $routes->post('create-silo', 'Perizinan::createSilo');
    $routes->get('get-silo-detail/(:num)', 'Perizinan::getSiloDetail/$1');
    $routes->post('update-silo-status/(:num)', 'Perizinan::updateSiloStatus/$1');
    $routes->post('upload-file/(:num)', 'Perizinan::uploadFile/$1');
    $routes->get('preview-file/(:num)/(:segment)', 'Perizinan::previewFile/$1/$2');
    $routes->get('download-file/(:num)/(:segment)', 'Perizinan::downloadFile/$1/$2');
    $routes->get('emisi', 'Perizinan::emisi');
});

// Public Test Email Route (for testing email configuration)
$routes->post('settings/test-email', 'Settings::testEmail');

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
    
    // Cache Management
    $routes->post('cache/clear', 'Admin::clearCache');
    $routes->post('cache/test', 'Admin::testCacheConnection');
    
    // Performance Management
    $routes->post('performance/test', 'Admin::performanceTest');
    $routes->post('logs/clear', 'Admin::clearLogs');
    
    // Queue Management
    $routes->post('queue/start', 'Admin::startQueue');
    $routes->post('queue/stop', 'Admin::stopQueue');
    $routes->post('queue/clear-failed', 'Admin::clearFailedJobs');
    
    // System Health & Maintenance
    $routes->post('health/check', 'Admin::healthCheck');
    $routes->post('database/optimize', 'Admin::optimizeDatabase');
    $routes->post('sessions/clear', 'Admin::clearSessions');
    
    // Activity Log Routes - DEPRECATED: Use ActivityLogViewer instead
    // $routes->get('activity-log', 'ActivityLog::index');
    // $routes->post('activity-log/data', 'ActivityLog::getData');
    // $routes->get('activity-log/details/(:num)', 'ActivityLog::details/$1');
    // $routes->get('activity-log/statistics', 'ActivityLog::statistics');
    // $routes->get('activity-log/export', 'ActivityLog::export');
    // $routes->post('activity-log/clean', 'ActivityLog::clean');
    
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
        $routes->get('change-password/(:num)', 'Admin\AdvancedUserManagement::changePasswordForm/$1');
        $routes->post('change-password/(:num)', 'Admin\AdvancedUserManagement::changePassword/$1');
        $routes->get('export', 'Admin\AdvancedUserManagement::export');
        $routes->post('clean-expired', 'Admin\AdvancedUserManagement::cleanExpired');
        $routes->get('user-matrix/(:num)', 'Admin\AdvancedUserManagement::userMatrix/$1');
        
        // API endpoints for notification system
        $routes->get('get-users', 'Admin\AdvancedUserManagement::getUsers');
        $routes->post('get-users-by-divisions', 'Admin\AdvancedUserManagement::getUsersByDivisions');
        $routes->post('get-users-by-roles', 'Admin\AdvancedUserManagement::getUsersByRoles');
        // // Permission management routes
        
        // $routes->post('quick-assign-permission', 'Admin\AdvancedUserManagement::quickAssignPermission');
        // $routes->post('bulk-assign-permissions', 'Admin\AdvancedUserManagement::bulkAssignPermissions');
        
        // Custom Permissions routes
        $routes->get('get-available-permissions/(:num)', 'Admin\AdvancedUserManagement::getAvailablePermissions/$1');
        $routes->post('save-custom-permissions/(:num)', 'Admin\AdvancedUserManagement::saveCustomPermissions/$1');
        $routes->post('remove-custom-permission/(:num)', 'Admin\AdvancedUserManagement::removeCustomPermission/$1');
        
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
        // User approval routes
        $routes->get('get-user-for-approval/(:num)', 'Admin\AdvancedUserManagement::getUserForApproval/$1');
        $routes->post('approve-user/(:num)', 'Admin\AdvancedUserManagement::approveUser/$1');
        $routes->post('deactivate-user/(:num)', 'Admin\AdvancedUserManagement::deactivateUser/$1');
    });
    
    // Role Management Routes (Simple RBAC)
    $routes->group('roles', static function ($routes) {
        $routes->get('/', 'Admin\RoleController::index');
        $routes->get('get-roles', 'Admin\RoleController::getRoles');
        $routes->get('get-role/(:num)', 'Admin\RoleController::getRole/$1');
        $routes->get('get-permissions', 'Admin\RoleController::getPermissions');
        $routes->post('save-role', 'Admin\RoleController::saveRole');
    });
    
    // Activity Log Routes - DEPRECATED: Use ActivityLogViewer instead  
    // $routes->group('activity-log', static function ($routes) {
    //     $routes->get('/', 'ActivityLog::index');
    //     $routes->post('data', 'ActivityLog::getData');
    //     $routes->get('details/(:num)', 'ActivityLog::details/$1');
    //     $routes->get('statistics', 'ActivityLog::statistics');
    //     $routes->get('export', 'ActivityLog::export');
    //     $routes->post('clean', 'ActivityLog::clean');
    // });

    // Activity Monitor Routes (Enhanced monitoring dashboard)
    $routes->group('activity-monitor', static function ($routes) {
        $routes->get('/', 'ActivityMonitor::index');
        $routes->post('data', 'ActivityMonitor::getData');
        $routes->get('statistics', 'ActivityMonitor::statistics');
        $routes->get('recent', 'ActivityMonitor::recent');
        $routes->get('details/(:num)', 'ActivityMonitor::details/$1');
        $routes->get('export', 'ActivityMonitor::export');
        $routes->get('health', 'ActivityMonitor::healthCheck');
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
        $routes->get('getCounts', 'Admin\RoleController::getCounts');
        $routes->get('getRoleDetail/(:num)', 'Admin\RoleController::getRoleDetail/$1');
    });
    
    // Verify Resource Permissions
    $routes->get('verify-resource-permissions', 'Admin\VerifyResourcePermissions::index');
    
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
    
    $routes->get('get-unit-form', 'Purchasing::getUnitFormFragment');
    
    // Finance API
    $routes->post('finance/invoices/create', 'Finance::createInvoice');
    $routes->post('finance/payments/update/(:num)', 'Finance::updatePaymentStatus/$1');
    
    // Reports API
    $routes->post('reports/generate', 'Reports::generateReport');
    $routes->post('reports/schedule', 'Reports::scheduleReport');
    
    // Unit Assets Form API
    $routes->get('models-by-merk/(:segment)', 'Api::getModelsByMerk/$1');
});

// System Routes (duplicate cleanup) - REMOVED

// API Routes for Form Data
$routes->group('api', ['namespace' => 'App\Controllers'], function($routes) {
    $routes->get('models-by-merk/(:segment)', 'Api::getModelsByMerk/$1');
});

// ============================================================================
// NOTIFICATION SYSTEM ROUTES (CONSOLIDATED)
// ============================================================================
// Routes moved to consolidated section below

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
    
    // Manual attach/detach/swap routes
    $r->get('get-available-units', 'Warehouse::getAvailableUnits');
    $r->post('attach-to-unit', 'Warehouse::attachToUnit');
    $r->post('swap-unit', 'Warehouse::swapUnit');
    $r->post('detach-from-unit', 'Warehouse::detachFromUnit');
});

// Test Route for Activity Log
$routes->get('test-activity-log', 'TestActivityLog::index');

// Activity Log Viewer Routes - Activated for detailed descriptions
$routes->group('admin', static function ($routes) {
    $routes->get('activity-log', 'ActivityLogViewer::index');
    $routes->post('activity-log/data', 'ActivityLogViewer::getData');
    $routes->get('activity-log/details/(:num)', 'ActivityLogViewer::getDetails/$1');
});

// Additional routes outside group to ensure they work
$routes->get('kontrak/customers', 'Kontrak::getCustomers');
$routes->get('kontrak/locations/(:num)', 'Kontrak::getLocationsByCustomer/$1');
$routes->post('kontrak/store', 'Kontrak::store');
$routes->post('kontrak/update/(:num)', 'Kontrak::update/$1');
$routes->post('kontrak/delete/(:num)', 'Kontrak::delete/$1');

// ======================================================================
// DELIVERY WORKFLOW ROUTES
// ======================================================================

// Delivery Data API
$routes->post('purchasing/api/get-delivery-data', 'Purchasing::getDeliveryData');

// Delivery Management API
$routes->post('purchasing/api/create-delivery', 'Purchasing::createDelivery');
$routes->get('purchasing/api/delivery-items/(:num)', 'Purchasing::getDeliveryItems/$1');
$routes->post('purchasing/api/assign-sn', 'Purchasing::assignSerialNumbers');
$routes->post('purchasing/api/update-delivery-status', 'Purchasing::updateDeliveryStatus');

// ========================================================================
// NOTIFICATION SYSTEM API
// ========================================================================
$routes->group('notifications', function($routes) {
    // User notification center
    $routes->get('/', 'NotificationController::index');
    
    // API endpoints
    $routes->get('get', 'NotificationController::getNotifications');
    $routes->get('count', 'NotificationController::getCount');
    $routes->get('poll', 'NotificationController::poll');
    $routes->post('mark-as-read/(:num)', 'NotificationController::markAsRead/$1');
    $routes->post('mark-all-as-read', 'NotificationController::markAllAsRead');
    $routes->delete('delete/(:num)', 'NotificationController::delete/$1');
    
    // Options data for admin panel
    $routes->get('options/event-types', 'NotificationController::eventTypeOptions');
    $routes->get('options/divisions', 'NotificationController::divisionOptions');
    $routes->get('options/roles', 'NotificationController::roleOptions');
    $routes->get('options/metadata', 'NotificationController::optionsMetadata');
    
    // Real-time SSE streaming
    $routes->get('stream', 'SSEController::stream');
    $routes->get('test', 'SSEController::test');
    
    // Admin panel
    $routes->get('admin', 'NotificationController::admin');
    $routes->get('rules', 'NotificationController::rules');
    $routes->get('getRule/(:num)', 'NotificationController::getRule/$1');
    $routes->get('get-rule/(:num)', 'NotificationController::getRule/$1');
    $routes->post('rules/create', 'NotificationController::createRule');
    $routes->post('rules/update/(:num)', 'NotificationController::updateRule/$1');
    $routes->post('admin/create-rule', 'NotificationController::createRule');
    $routes->post('admin/update-rule/(:num)', 'NotificationController::updateRule/$1');
    $routes->post('admin/toggle-status/(:num)', 'NotificationController::toggleStatus/$1');
    $routes->post('admin/delete-rule/(:num)', 'NotificationController::deleteRule/$1');
    $routes->delete('rules/delete/(:num)', 'NotificationController::deleteRule/$1');
});

// ============================================================================
// QUEUE MANAGEMENT ROUTES - Background Jobs & Queue System
// ============================================================================
$routes->group('queue', function($routes) {
    $routes->get('/', 'QueueController::index');
    $routes->get('dashboard', 'QueueController::index');
    $routes->post('process', 'QueueController::process');
    $routes->get('stats', 'QueueController::stats');
    $routes->post('clean-failed', 'QueueController::cleanFailed');
    $routes->post('clear-cache', 'QueueController::clearCache');
    $routes->post('test-email', 'QueueController::testEmail');
    $routes->post('test-notification', 'QueueController::testNotification');
    $routes->get('auto-process', 'QueueController::autoProcess'); // For cron jobs
});

