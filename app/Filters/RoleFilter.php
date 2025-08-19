<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Check if user has required role before accessing route
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load permission helper
        helper('permission');

        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/auth/login');
        }

        // If no role arguments provided, allow access
        if (empty($arguments)) {
            return;
        }

        $requiredRole = $arguments[0];
        
        // Check if user has required role
        if (!has_role($requiredRole)) {
            if ($request->isAJAX()) {
                return response()->setJSON([
                    'error' => 'Unauthorized',
                    'message' => 'Anda tidak memiliki role yang diperlukan untuk mengakses fitur ini.'
                ])->setStatusCode(403);
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki role yang diperlukan untuk mengakses halaman ini.');
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