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

            // Resolve permission id by key
            $perm = $db->table('permissions')->select('id')->where('key', $permissionKey)->get()->getRowArray();
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
            // If anything goes wrong, do not block UI
            return true;
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
}
