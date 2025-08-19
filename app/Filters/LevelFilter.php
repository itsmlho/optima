<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LevelFilter implements FilterInterface
{
    /**
     * Check if user has required level before accessing route
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Load permission helper
        helper('permission');

        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/auth/login');
        }

        // If no level arguments provided, allow access
        if (empty($arguments)) {
            return;
        }

        $requiredLevel = $arguments[0];
        $hasAccess = false;
        
        // Check user level based on required level
        switch ($requiredLevel) {
            case 'management':
                $hasAccess = is_management_level();
                break;
            case 'head_division':
                $hasAccess = is_head_division_level();
                break;
            case 'admin':
                $hasAccess = is_admin_level();
                break;
            case 'staff':
                $hasAccess = is_staff_level();
                break;
            default:
                $hasAccess = false;
        }
        
        // If user doesn't have required level, check if they have management level or higher
        if (!$hasAccess && is_management_level()) {
            $hasAccess = true;
        }
        
        if (!$hasAccess) {
            if ($request->isAJAX()) {
                return response()->setJSON([
                    'error' => 'Unauthorized',
                    'message' => 'Anda tidak memiliki level yang diperlukan untuk mengakses fitur ini.'
                ])->setStatusCode(403);
            } else {
                return redirect()->back()->with('error', 'Anda tidak memiliki level yang diperlukan untuk mengakses halaman ini.');
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