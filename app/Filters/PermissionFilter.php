<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class PermissionFilter implements FilterInterface
{
    /**
     * Check if user has required permission before accessing route
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load permission helper
        helper('permission');

        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/auth/login');
        }

        // If no permission arguments provided, allow access
        if (empty($arguments)) {
            return;
        }

        $requiredPermission = $arguments[0];
        
        // Check if user has required permission
        if (!has_permission($requiredPermission)) {
            if ($request->isAJAX()) {
                return response()->setJSON([
                    'error' => 'Unauthorized',
                    'message' => 'Anda tidak memiliki akses ke fitur ini.'
                ])->setStatusCode(403);
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses ke halaman ini.');
            }
        }
    }

    /**
     * We don't have anything to do here
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
} 