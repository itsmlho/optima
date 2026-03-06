<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Security extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * CSRF Protection Method
     * --------------------------------------------------------------------------
     *
     * Protection Method for Cross Site Request Forgery protection.
     *
     * @var string 'cookie' or 'session'
     */
    public string $csrfProtection = 'cookie';

    /**
     * --------------------------------------------------------------------------
     * CSRF Token Randomization
     * --------------------------------------------------------------------------
     *
     * Randomize the CSRF Token for added security.
     */
    public bool $tokenRandomize = false;

    /**
     * --------------------------------------------------------------------------
     * CSRF Token Name
     * --------------------------------------------------------------------------
     *
     * Token name for Cross Site Request Forgery protection.
     */
    public string $tokenName = 'csrf_test_name';

    /**
     * --------------------------------------------------------------------------
     * CSRF Header Name
     * --------------------------------------------------------------------------
     *
     * Header name for Cross Site Request Forgery protection.
     */
    public string $headerName = 'X-CSRF-TOKEN';

    /**
     * --------------------------------------------------------------------------
     * CSRF Cookie Name
     * --------------------------------------------------------------------------
     *
     * Cookie name for Cross Site Request Forgery protection.
     */
    public string $cookieName = 'csrf_cookie_name';

    /**
     * --------------------------------------------------------------------------
     * CSRF Expires
     * --------------------------------------------------------------------------
     *
     * Expiration time for Cross Site Request Forgery protection cookie.
     *
     * Defaults to two hours (in seconds).
     */
    public int $expires = 7200;

    /**
     * --------------------------------------------------------------------------
     * CSRF Regenerate
     * --------------------------------------------------------------------------
     *
     * Regenerate CSRF Token on every submission.
     * 
     * IMPORTANT: Set to FALSE for AJAX-heavy applications.
     * If TRUE, token changes after each request causing subsequent AJAX calls to fail.
     */
    public bool $regenerate = false;

    /**
     * --------------------------------------------------------------------------
     * CSRF Redirect
     * --------------------------------------------------------------------------
     *
     * Redirect to previous page with error on failure.
     *
     * @see https://codeigniter4.github.io/userguide/libraries/security.html#redirection-on-failure
     */
    public bool $redirect = (ENVIRONMENT === 'production');

    /**
     * --------------------------------------------------------------------------
     * CSRF Exclude URIs
     * --------------------------------------------------------------------------
     *
     * List of URIs that should be excluded from CSRF protection.
     * Leave empty to protect all POST requests.
     *
     * @var array<int, string>
     */
    public array $csrfExclude = [
        // ===================================================================
        // AJAX ENDPOINTS - EXCLUDED FROM CSRF PROTECTION
        // All endpoints below are protected by the AuthFilter (login required)
        // CSRF exclusion is safe because:
        // 1. Unauthenticated users cannot access these endpoints
        // 2. AuthFilter checks session on every request
        // 3. CodeIgniter 4 recommended approach for AJAX-heavy applications
        // ===================================================================

        // ----- Customer Management -----
        'marketing/customer-management/getCustomers',
        'marketing/customer-management/getCustomerStats',
        'marketing/customer-management/storeCustomer',
        'marketing/customer-management/updateCustomer',
        'marketing/customer-management/deleteCustomer',
        'marketing/customer-management/storeCustomerLocation',
        'marketing/customer-management/updateCustomerLocation',
        'marketing/customer-management/generateCustomerCode',
        'marketing/customer-management/generateLocationCode',

        // ----- Kontrak / Contracts -----
        'marketing/kontrak/getKontrak',
        'marketing/kontrak/getDataTable',
        'marketing/kontrak/store',
        'marketing/kontrak/update',
        'marketing/kontrak/delete',
        'marketing/kontrak/getCustomers',
        'marketing/kontrak/getCustomersDropdown',

        // ----- SPK -----
        'marketing/spk/data',
        'marketing/spk/stats',
        'marketing/spk/store',
        'marketing/spk/update',
        'marketing/spk/delete',

        // ----- Delivery Instructions / DI -----
        'marketing/di/data',
        'marketing/di/getData',
        'marketing/di/store',
        'marketing/di/update',
        'marketing/di/delete',

        // ----- Quotations -----
        'marketing/quotations/data',
        'marketing/quotations/stats',
        'marketing/quotations/store',
        'marketing/quotations/update',
        'marketing/quotations/delete',

        // ----- Finance / Invoices -----
        'finance/invoices/getInvoices',
        'finance/invoices/getData',
        'finance/invoices/store',
        'finance/invoices/update',
        'finance/invoices/delete',

        // ----- Service / Work Orders -----
        'service/work-orders/getWorkOrders',
        'service/work-orders/getData',
        'service/work-orders/store',
        'service/work-orders/update',
        'service/work-orders/delete',

        // ----- Operational / Delivery -----
        'operational/delivery/getDeliveries',
        'operational/delivery/getData',
        'operational/delivery/store',
        'operational/delivery/update',
        'operational/delivery/delete',

        // ----- Inventory / Units -----
        'inventory/units/getUnits',
        'inventory/units/getData',
        'inventory/units/store',
        'inventory/units/update',
        'inventory/units/delete',

        // ----- Admin / User Management -----
        'admin/users/getUsers',
        'admin/users/getData',
        'admin/users/store',
        'admin/users/update',
        'admin/users/delete',

        // ----- Notifications -----
        'notifications/markAsRead',
        'notifications/markAllRead',
        'notifications/getData',
    ];
}
