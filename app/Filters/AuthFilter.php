<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Check if user is logged in before accessing protected routes
     * Public paths are excluded from authentication requirement
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        $uri = service('uri');
        
        // Get route segments (most reliable way to get the path)
        $segments = $uri->getSegments();
        $currentPath = !empty($segments) ? implode('/', $segments) : '';
        
        // Normalize path (remove leading/trailing slashes)
        $currentPath = trim($currentPath, '/');
        
        // List of public paths that don't require authentication
        // Note: welcome page requires authentication, so it's not in this list
        $publicPaths = [
            '',  // Root path (empty string)
            'auth',
            'auth/login',
            'auth/attempt-login',
            'auth/register',
            'auth/attempt-register',
            'auth/forgot-password',
            'auth/send-reset-link',
            'auth/update-password',
            'auth/logout',
            'auth/index',
            'auth/verify-otp',
            'auth/resend-otp',
            'comingsoon',
        ];
        
        // Check if current path is a public path
        $isPublicPath = false;
        
        // Exact match check
        if (in_array($currentPath, $publicPaths)) {
            $isPublicPath = true;
        }
        
        // Check for paths starting with public paths (e.g., auth/reset-password/xyz)
        if (!$isPublicPath) {
            foreach ($publicPaths as $publicPath) {
                if ($publicPath !== '' && strpos($currentPath, $publicPath . '/') === 0) {
                    $isPublicPath = true;
                    break;
                }
            }
        }
        
        // Special handling for auth/reset-password/* pattern
        if (!$isPublicPath && preg_match('#^auth/reset-password/#', $currentPath)) {
            $isPublicPath = true;
        }
        
        // Debug: Log the path being checked (remove after fixing)
        if (ENVIRONMENT !== 'production') {
            log_message('debug', 'AuthFilter: Current path = "' . $currentPath . '", isPublicPath = ' . ($isPublicPath ? 'yes' : 'no') . ', isLoggedIn = ' . ($session->get('isLoggedIn') ? 'yes' : 'no'));
        }
        
        // If this is a public path, allow access
        if ($isPublicPath) {
            // Special paths that should always be allowed (like logout)
            $alwaysAllowedPaths = ['auth/logout', 'auth/index'];
            if (in_array($currentPath, $alwaysAllowedPaths)) {
                // Always allow logout and auth index regardless of login status
                return null;
            }
            
            // If user is already logged in and trying to access login/register pages, redirect to welcome
            if ($session->get('isLoggedIn')) {
                $redirectPaths = ['auth/login', 'auth/register', 'auth/forgot-password', 'auth'];
                foreach ($redirectPaths as $redirectPath) {
                    if ($currentPath === $redirectPath || strpos($currentPath, $redirectPath . '/') === 0) {
                        return redirect()->to('/welcome');
                    }
                }
            }
            // Allow access to public paths - return null means allow
            return null;
        }
        
        // If not a public path, check if user is logged in
        if (!$session->get('isLoggedIn')) {
            // Check if this is an AJAX request
            /** @var \CodeIgniter\HTTP\IncomingRequest $request */
            if ($request instanceof \CodeIgniter\HTTP\IncomingRequest && $request->isAJAX()) {
                // Return JSON error instead of redirect for AJAX requests
                $response = service('response');
                return $response->setStatusCode(401)->setJSON([
                    'success' => false,
                    'message' => 'Unauthorized: Session expired. Please login again.',
                    'redirect' => '/auth/login'
                ]);
            }
            
            // For normal requests, redirect to login
            return redirect()->to('/auth/login')
                ->with('error', 'Anda harus login terlebih dahulu untuk mengakses halaman ini.');
        }
        
        // User is logged in - update session activity for session management
        // Only if table exists and tracking is enabled
        $sessionId = session_id();
        if ($sessionId) {
            try {
                /** @var \Config\AuthSecurity $authSecurityConfig */
                $authSecurityConfig = config('AuthSecurity');
                if ($authSecurityConfig && $authSecurityConfig->trackDevices) {
                    // Check if user_sessions table exists before trying to update
                    $db = \Config\Database::connect();
                    $tableExists = $db->tableExists('user_sessions');
                    
                    if ($tableExists) {
                        $sessionService = new \App\Services\SessionService();
                        $sessionService->updateActivity($sessionId);
                        
                        // Check for idle sessions and auto logout if needed
                        if ($authSecurityConfig->autoLogoutIdleSessions) {
                            $sessionService->cleanInactiveSessions();
                        }
                    }
                }
            } catch (\Exception $e) {
                // Silently fail if table doesn't exist - migration not run yet
                // Log error for debugging
                log_message('debug', 'Session tracking skipped: ' . $e->getMessage());
            }
        }
        
        // User is logged in, allow access
        return null;
    }

    /**
     * We don't have anything to do here
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
