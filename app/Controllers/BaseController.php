<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
        
        // ===== MULTI-LANGUAGE SUPPORT =====
        // Auto-detect and set user's language preference
        $this->setupLanguage();
        
        // ===== BACKGROUND SCHEDULER (Pseudo-CRON) =====
        // Run scheduled tasks without needing server CRON
        // Only for web requests (not CLI)
        if (!$request instanceof CLIRequest) {
            $this->runBackgroundScheduler();
        }
    }
    
    /**
     * Run background scheduler for automated checks
     * 
     * Pseudo-CRON implementation for shared hosting
     * Runs with 10% probability to reduce overhead
     * 
     * Tasks:
     * - Contract Expiry Check (every 24 hours)
     * 
     * @return void
     */
    protected function runBackgroundScheduler(): void
    {
        try {
            // Random 10% chance - only 1 out of 10 requests
            // Reduces overhead while ensuring regular execution
            if (rand(1, 10) === 1) {
                helper('notification');
                
                // Check contract expiry (runs max once per 24 hours)
                check_contract_expiry_scheduled();
                
                log_message('debug', '[Scheduler] Background tasks executed');
            }
        } catch (\Exception $e) {
            // Silent fail - don't disrupt user experience
            log_message('error', '[BaseController] Background scheduler error: ' . $e->getMessage());
        }
    }
    
    /**
     * Setup Language
     * 
     * Automatically detects and sets the user's language preference
     * Priority: Session > Default (Indonesian)
     * 
     * @return void
     */
    protected function setupLanguage(): void
    {
        // Get language from session or use default
        $userLanguage = session()->get('user_language');
        
        // If no language in session, use default from config
        if (!$userLanguage) {
            $userLanguage = config('App')->defaultLocale ?? 'id';
            session()->set('user_language', $userLanguage);
        }
        
        // Validate language is supported
        $supportedLocales = config('App')->supportedLocales ?? ['id', 'en'];
        if (!in_array($userLanguage, $supportedLocales)) {
            $userLanguage = 'id'; // Fallback to Indonesian
            session()->set('user_language', $userLanguage);
        }
        
        // Set locale for current request - use proper CI4 method
        $this->request->setLocale($userLanguage);
        
        // Also set the global locale for lang() helper
        service('language')->setLocale($userLanguage);
    }

    /**
     * Centralized permission check.
     * Fallbacks to permissive mode if RBAC tables are missing.
     */
    protected function hasPermission(string $permissionKey): bool
    {
        try {
            $db = \Config\Database::connect();
            $userId = session('user_id');
            if (!$userId) { return false; }

            // Super Administrator shortcut by role name (if present)
            $super = $db->table('user_roles ur')
                ->select('1')
                ->join('roles r', 'r.id = ur.role_id', 'left')
                ->where('ur.user_id', $userId)
                ->where('r.name', 'Super Administrator')
                ->limit(1)->get()->getRowArray();
            if ($super) return true;

            // Resolve permission id by key_name (updated field)
            $perm = $db->table('permissions')->select('id')->where('key_name', $permissionKey)->get()->getRowArray();
            if (!$perm || empty($perm['id'])) {
                // Permission key not registered yet – do not block
                return true;
            }
            $permissionId = (int)$perm['id'];

            // Check role based permission
            $has = $db->table('user_roles ur')
                ->join('role_permissions rp', 'rp.role_id = ur.role_id', 'left')
                ->where('ur.user_id', $userId)
                ->where('rp.permission_id', $permissionId)
                ->select('1')->limit(1)->get()->getRowArray();
            if ($has) return true;

            // Optional: direct user_permissions table support if exists
            try {
                $direct = $db->table('user_permissions')
                    ->select('1')
                    ->where('user_id', $userId)
                    ->where('permission_id', $permissionId)
                    ->limit(1)->get()->getRowArray();
                if ($direct) return true;
            } catch (\Throwable $e) {
                // table may not exist – ignore
            }

            return false;
        } catch (\Throwable $e) {
            // Fail-CLOSED: deny access if RBAC check itself errors (e.g. DB timeout).
            // Logging is critical here — this may indicate a DB connectivity issue.
            log_message('critical', '[RBAC] hasPermission("{perm}") failed — access denied. Error: ' . str_replace('{perm}', $permissionKey, $e->getMessage()));
            return false;
        }
    }
    
    /**
     * Check if user can access a module (view/read)
     * @param string $module - Module name (admin, marketing, service, etc.)
     */
    protected function canAccess(string $module): bool
    {
        return $this->hasPermission($module . '.access');
    }
    
    /**
     * Check if user can manage resources (create/edit)
     * @param string $module - Module name
     */
    protected function canManage(string $module): bool
    {
        return $this->hasPermission($module . '.manage');
    }
    
    /**
     * Check if user can delete resources
     * @param string $module - Module name
     */
    protected function canDelete(string $module): bool
    {
        return $this->hasPermission($module . '.delete');
    }
    
    /**
     * Check if user can export data
     * @param string $module - Module name
     */
    protected function canExport(string $module): bool
    {
        return $this->hasPermission($module . '.export');
    }
    
    /**
     * Require module access or redirect/return error
     * @param string $module - Module name
     * @param bool $ajax - Whether this is an AJAX request
     */
    protected function requireAccess(string $module, bool $ajax = false)
    {
        if (!$this->canAccess($module)) {
            if ($ajax) {
                return $this->response->setJSON(['success' => false, 'message' => 'Access denied'])
                                      ->setStatusCode(403);
            }
            return redirect()->to('/')->with('error', 'Access denied to ' . $module . ' module');
        }
        return null;
    }
    
    /**
     * Require manage permission or return error
     * @param string $module - Module name
     */
    protected function requireManage(string $module)
    {
        if (!$this->canManage($module)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'You do not have permission to create/edit'])
                                      ->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'You do not have permission to create/edit');
        }
        return null;
    }
    
    /**
     * Require delete permission or return error
     * @param string $module - Module name
     */
    protected function requireDelete(string $module)
    {
        if (!$this->canDelete($module)) {
            return $this->response->setJSON(['success' => false, 'message' => 'You do not have permission to delete'])
                                  ->setStatusCode(403);
        }
        return null;
    }
    
    /**
     * Require export permission or return error
     * @param string $module - Module name
     */
    protected function requireExport(string $module)
    {
        if (!$this->canExport($module)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON(['success' => false, 'message' => 'You do not have permission to export'])
                                      ->setStatusCode(403);
            }
            return redirect()->back()->with('error', 'You do not have permission to export');
        }
        return null;
    }
    
    /**
     * Check if user can access a resource (cross-division)
     * Supports both module permission and resource permission
     * 
     * @param string $module - Module name (e.g., 'warehouse', 'marketing')
     * @param string|null $resource - Resource name (e.g., 'inventory', 'kontrak') - optional
     * @param string $action - Action (view, manage, delete, export) - default: 'view'
     * @return bool
     */
    protected function canAccessResource(string $module, ?string $resource = null, string $action = 'view'): bool
    {
        // Super admin bypass
        try {
            $db = \Config\Database::connect();
            $userId = session('user_id');
            if (!$userId) {
                return false;
            }

            // Check Super Administrator
            $super = $db->table('user_roles ur')
                ->select('1')
                ->join('roles r', 'r.id = ur.role_id', 'left')
                ->where('ur.user_id', $userId)
                ->where('r.name', 'Super Administrator')
                ->limit(1)->get()->getRowArray();
            if ($super) {
                return true;
            }

            // Build permission key
            if ($resource) {
                // Resource permission: module.resource.action
                $permissionKey = $module . '.' . $resource . '.' . $action;
            } else {
                // Module permission: module.action
                $permissionKey = $module . '.' . $action;
            }

            // Check permission
            return $this->hasPermission($permissionKey);
        } catch (\Throwable $e) {
            // Fail-CLOSED: deny access on error.
            log_message('critical', '[RBAC] canAccessResource("{mod}") failed — access denied. Error: ' . str_replace('{mod}', $module, $e->getMessage()));
            return false;
        }
    }

    /**
     * Check if user can view a resource (cross-division)
     * 
     * @param string $module - Module name
     * @param string|null $resource - Resource name (optional)
     * @return bool
     */
    protected function canViewResource(string $module, ?string $resource = null): bool
    {
        return $this->canAccessResource($module, $resource, 'view');
    }

    /**
     * Check if user can manage a resource (cross-division)
     * 
     * @param string $module - Module name
     * @param string|null $resource - Resource name (optional)
     * @return bool
     */
    protected function canManageResource(string $module, ?string $resource = null): bool
    {
        return $this->canAccessResource($module, $resource, 'manage');
    }

    /**
     * Require resource access or redirect/return error
     * Checks both module permission and resource permission
     * 
     * @param string $module - Module name
     * @param string|null $resource - Resource name (optional)
     * @param string $action - Action (view, manage) - default: 'view'
     * @param bool $ajax - Whether this is an AJAX request
     * @return mixed|null
     */
    protected function requireResourceAccess(string $module, ?string $resource = null, string $action = 'view', bool $ajax = false)
    {
        // Check module permission first (own division)
        $hasModuleAccess = $this->canAccessResource($module, null, $action);
        
        // Check resource permission (cross-division)
        $hasResourceAccess = $resource ? $this->canAccessResource($module, $resource, $action) : false;

        // Allow if either module or resource permission exists
        if (!$hasModuleAccess && !$hasResourceAccess) {
            if ($ajax || $this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Access denied: You do not have permission to access this resource'
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'Access denied: You do not have permission to access this resource');
        }
        
        return null;
    }

    /**
     * Get current theme preference
     * Check for saved theme in session/cookie, fallback to light
     */
    protected function getCurrentTheme(): string
    {
        // Check session first
        $sessionTheme = session()->get('user_theme');
        if ($sessionTheme && in_array($sessionTheme, ['light', 'dark'])) {
            return $sessionTheme;
        }

        // Check cookie as fallback
        $cookieTheme = $this->request->getCookie('optima_theme');
        if ($cookieTheme && in_array($cookieTheme, ['light', 'dark'])) {
            return $cookieTheme;
        }

        // Default to light theme
        return 'light';
    }

    /**
     * Set theme preference for user
     */
    protected function setTheme(string $theme): void
    {
        if (in_array($theme, ['light', 'dark'])) {
            // Save to session
            session()->set('user_theme', $theme);
            
            // Save to cookie using helper
            helper('cookie');
            set_cookie('optima_theme', $theme, 86400 * 30); // 30 days
        }
    }

    /**
     * Prepare common view data including theme
     */
    protected function prepareViewData(array $data = []): array
    {
        $commonData = [
            'theme' => $this->getCurrentTheme(),
            'user_name' => session()->get('first_name') . ' ' . session()->get('last_name'),
            'user_email' => session()->get('email'),
        ];

        return array_merge($commonData, $data);
    }
}
