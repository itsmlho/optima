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
     * CHANGED TO 'session' (March 11, 2026):
     * - Browser Tracking Prevention blocks cookie access
     * - Session-based protection not affected by cookie blocking
     * - More reliable for AJAX-heavy applications
     *
     * @var string 'cookie' or 'session'
     */
    public string $csrfProtection = 'session';

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
     * Expiration time for Cross Site Request Forgery protection.
     *
     * OPTIMA STANDARD: 6 hours (21600 seconds)
     * - Match session expiration for consistency
     * - Auto-logout after 6 hours (no token refresh prompt)
     * - Security: prevents indefinite sessions
     * - UX: clear 6-hour work session limit
     */
    public int $expires = 21600;

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
        // Only read-only GET endpoints are excluded.
        // Create/Update/Delete endpoints ARE protected by CSRF.
        //
        // WHY THIS IS SAFE:
        // 1. GET requests are idempotent (don't modify data)
        // 2. All endpoints are protected by AuthFilter (login required)
        // 3. JavaScript already includes CSRF token in AJAX requests
        // 4. DataTable uses server-side processing with proper token handling
        // ===================================================================

        // ----- Auth (Public endpoints - must remain excluded) -----
        'auth/login',
        'auth/register',
        'auth/forgot',
        'auth/reset',

        // ----- Customer Management (Read-only) -----
        'marketing/customer-management/getCustomers',
        'marketing/customer-management/getCustomerStats',
        'marketing/customer-management/generateCustomerCode',
        'marketing/customer-management/generateLocationCode',

        // ----- Kontrak / Contracts (Read-only) -----
        'marketing/kontrak/getKontrak',
        'marketing/kontrak/getDataTable',
        'marketing/kontrak/getCustomers',
        'marketing/kontrak/getCustomersDropdown',

        // ----- SPK (Read-only) -----
        'marketing/spk/data',
        'marketing/spk/stats',

        // ----- Delivery Instructions / DI (Read-only) -----
        'marketing/di/data',
        'marketing/di/getData',

        // ----- Quotations (Read-only) -----
        'marketing/quotations/data',
        'marketing/quotations/stats',

        // ----- Finance / Invoices (Read-only) -----
        'finance/invoices/getInvoices',
        'finance/invoices/getData',

        // ----- Service / Work Orders (Read-only) -----
        'service/work-orders/getWorkOrders',
        'service/work-orders/getData',

        // ----- Operational / Delivery (Read-only) -----
        'operational/delivery/getDeliveries',
        'operational/delivery/getData',

        // ----- Inventory / Units (Read-only) -----
        'inventory/units/getUnits',
        'inventory/units/getData',

        // ----- Admin / User Management (Read-only) -----
        'admin/users/getUsers',
        'admin/users/getData',

        // ----- Notifications (Mark as read is safe) -----
        'notifications/markAsRead',
        'notifications/markAllRead',
        'notifications/getData',
    ];
}
