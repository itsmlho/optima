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
        // Load permission helper and auth helper
        helper(['permission', 'auth']);

        // Check if user is logged in
        if (!session()->get('user_id')) {
            return redirect()->to('/auth/login');
        }

        // If no permission arguments provided, allow access
        if (empty($arguments)) {
            return;
        }

        // ✅ BYPASS: Allow admin and superadmin full access
        $userRole = session()->get('role');
        if (in_array($userRole, ['admin', 'superadmin', 'super_admin', 'administrator'])) {
            return; // Allow access
        }

        $requiredPermission = $arguments[0];
        // Support OR: "view_service|view_marketing" = user needs any of these
        $permissions = array_map('trim', explode('|', $requiredPermission));
        $hasAny = false;
        foreach ($permissions as $perm) {
            if (hasPermission($perm)) {
                $hasAny = true;
                break;
            }
        }
        if (!$hasAny) {
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