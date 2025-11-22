<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Services\RateLimitService;
use App\Services\OtpService;
use App\Services\PasswordResetService;
use App\Services\SessionService;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $userModel;
    protected $session;
    protected $rateLimitService;
    protected $otpService;
    protected $passwordResetService;
    protected $sessionService;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
        $this->rateLimitService = new RateLimitService();
        $this->otpService = new OtpService();
        $this->passwordResetService = new PasswordResetService();
        $this->sessionService = new SessionService();
        
        // Load cookie helper
        helper('cookie');
        
        // Check remember token if not logged in
        $this->checkRememberTokens();
    }

    /**
     * Check remember token for auto-login
     */
    private function checkRememberTokens()
    {
        if (!$this->session->get('isLoggedIn')) {
            $rememberToken = get_cookie('remember_token');
            
            if ($rememberToken) {
                $user = $this->userModel->where('remember_token', $rememberToken)
                                        ->where('status', 'active')
                                        ->first();
                
                if ($user) {
                    // Auto login user
                    $sessionData = [
                        'isLoggedIn' => true,
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'email' => $user['email'],
                        'first_name' => $user['first_name'] ?? '',
                        'last_name' => $user['last_name'] ?? '',
                        'department' => $user['department'] ?? '',
                        'position' => $user['position'] ?? '',
                        'avatar' => $user['avatar'] ?? null,
                        'role' => $this->getUserRole($user['id']),
                        'is_active' => $user['is_active'] ?? 1
                    ];
                    
                    $this->session->set($sessionData);
                }
            }
        }
    }

    public function index()
    {
        // Redirect to login if not authenticated
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }
        
        return redirect()->to('/welcome');
    }

    public function login()
    {
        // If already logged in, redirect to welcome page
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/welcome');
        }

        $data = [
            'title' => 'Login - OPTIMA',
            'page_title' => 'Login',
            'validation' => $this->validator ?? null
        ];

        return view('auth/login', $data);
    }

    public function attemptLogin()
    {
        $rules = [
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[6]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent()->getAgentString();

        // Check rate limiting BEFORE checking user/password
        $rateLimit = $this->rateLimitService->checkAndRecord($username, $ipAddress, $userAgent, false);
        
        if (!$rateLimit['allowed']) {
            // Account is locked or rate limited
            return redirect()->back()
                ->withInput()
                ->with('error', $rateLimit['message'])
                ->with('rate_limit', [
                    'remaining_attempts' => $rateLimit['remaining_attempts'],
                    'locked_until' => $rateLimit['locked_until'] ?? null,
                    'locked_until_timestamp' => $rateLimit['locked_until_timestamp'] ?? null,
                ]);
        }

        // Find user by username or email
        $user = $this->userModel->where('username', $username)
                                ->orWhere('email', $username)
                                ->where('is_active', 1)
                                ->first();

        $isPasswordValid = false;
        if ($user) {
            // Verify password
            $isPasswordValid = password_verify($password, $user['password_hash']);
        }

        // Record attempt (success or failure)
        if (!$isPasswordValid) {
            // Record failed attempt
            $rateLimit = $this->rateLimitService->checkAndRecord($username, $ipAddress, $userAgent, false);
            
            if (!$rateLimit['allowed']) {
                // After failed attempt, check if we need to lock
                return redirect()->back()
                    ->withInput()
                    ->with('error', $rateLimit['message'] ?? 'Username/Email atau password salah.')
                    ->with('rate_limit', [
                        'remaining_attempts' => $rateLimit['remaining_attempts'],
                        'locked_until' => $rateLimit['locked_until'] ?? null,
                        'locked_until_timestamp' => $rateLimit['locked_until_timestamp'] ?? null,
                    ]);
            }

            // Still have attempts remaining
            return redirect()->back()
                ->withInput()
                ->with('error', 'Username/Email atau password salah.')
                ->with('rate_limit', [
                    'remaining_attempts' => $rateLimit['remaining_attempts'],
                ]);
        }

        // Check if OTP is enabled for this user
        if (!empty($user['otp_enabled']) && $user['otp_enabled'] == 1) {
            // Store remember me preference
            $this->session->set('remember_login', $remember);

            // Generate OTP and send email
            $otpResult = $this->otpService->generateOtp($user['id'], $user['email']);

            if (!$otpResult || isset($otpResult['error'])) {
                $errorMessage = $otpResult['message'] ?? 'Gagal mengirim OTP. Silakan coba lagi.';
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage);
            }

            // Set temporary session data (not logged in yet, waiting for OTP)
            $this->session->set([
                'temp_user_id' => $user['id'],
                'otp_required' => true,
                'otp_email' => $user['email'],
                'remember_login' => $remember,
            ]);

            // Redirect to OTP verification page
            return redirect()->to('/auth/verify-otp')
                ->with('info', 'Kode OTP telah dikirim ke email Anda. Silakan cek email dan masukkan kode OTP.');
        }

        // OTP not enabled - proceed with normal login
        // Reset rate limiting after successful login
        $this->rateLimitService->resetAttempts($username, $ipAddress);
        
        // Record successful login attempt
        $this->rateLimitService->checkAndRecord($username, $ipAddress, $userAgent, true);

        // Set session data
        $sessionData = [
            'isLoggedIn' => true,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'division_id' => $user['division_id'] ?? null,
            'position' => $user['position'] ?? '',
            'avatar' => $user['avatar'] ?? null,
            'role' => $this->getUserRole($user['id']),
            'is_active' => $user['is_active'] ?? 1,
            'is_super_admin' => $user['is_super_admin'] ?? 0
        ];

        $this->session->set($sessionData);

        // Track session untuk session management
        // Only if table exists and tracking is enabled
        $currentSessionId = session_id();
        if ($currentSessionId) {
            try {
                $db = \Config\Database::connect();
                if ($db->tableExists('user_sessions')) {
                    $this->sessionService->trackSession($user['id'], $currentSessionId);
                }
            } catch (\Exception $e) {
                // Silently fail if table doesn't exist - migration not run yet
                log_message('debug', 'Session tracking skipped: ' . $e->getMessage());
            }
        }

        // Log successful login using trait
        $this->logAuthActivity('LOGIN', $user['id'], [
            'username' => $user['username'],
            'email' => $user['email'],
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'remember_me' => $remember ? true : false,
            'description' => 'User Login Successful'
        ]);

        // Handle remember me
        if ($remember) {
            $this->setRememberToken($user['id']);
        }

        // Redirect based on user role
        $redirectUrl = $this->getRedirectUrl($sessionData['role']);
        
        return redirect()->to($redirectUrl)->with('success', 'Login berhasil! Selamat datang, ' . $user['username']);
    }

    /**
     * Show OTP verification page
     */
    public function verifyOtpPage()
    {
        // Check if user is in OTP verification flow
        if (!$this->session->get('otp_required') || !$this->session->get('temp_user_id')) {
            return redirect()->to('/auth/login')
                ->with('error', 'Sesi verifikasi OTP tidak valid. Silakan login kembali.');
        }

        $data = [
            'title' => 'Verifikasi OTP - OPTIMA',
            'page_title' => 'Verifikasi OTP',
            'email' => $this->session->get('otp_email'),
            'validation' => $this->validator ?? null
        ];

        return view('auth/verify_otp', $data);
    }

    /**
     * Verify OTP code
     */
    public function verifyOtp()
    {
        // Check if user is in OTP verification flow
        if (!$this->session->get('otp_required') || !$this->session->get('temp_user_id')) {
            return redirect()->to('/auth/login')
                ->with('error', 'Sesi verifikasi OTP tidak valid. Silakan login kembali.');
        }

        $rules = [
            'otp_code' => 'required|exact_length[6]|numeric'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('validation', $this->validator);
        }

        $otpCode = $this->request->getPost('otp_code');
        $userId = $this->session->get('temp_user_id');

        // Validate OTP
        $otpResult = $this->otpService->validateOtp($otpCode, $userId);

        if (!$otpResult['valid']) {
            return redirect()->back()
                ->withInput()
                ->with('error', $otpResult['message']);
        }

        // OTP is valid - complete login
        $user = $this->userModel->find($userId);

        if (!$user) {
            $this->session->remove(['temp_user_id', 'otp_required', 'otp_email', 'remember_login']);
            return redirect()->to('/auth/login')
                ->with('error', 'User tidak ditemukan. Silakan login kembali.');
        }

        // Get login data from session
        $username = $user['username'];
        $remember = $this->session->get('remember_login') ?? false;
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent()->getAgentString();

        // Reset rate limiting after successful login
        $this->rateLimitService->resetAttempts($username, $ipAddress);
        
        // Record successful login attempt
        $this->rateLimitService->checkAndRecord($username, $ipAddress, $userAgent, true);

        // Set session data (complete login)
        $sessionData = [
            'isLoggedIn' => true,
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'division_id' => $user['division_id'] ?? null,
            'position' => $user['position'] ?? '',
            'avatar' => $user['avatar'] ?? null,
            'role' => $this->getUserRole($user['id']),
            'is_active' => $user['is_active'] ?? 1,
            'is_super_admin' => $user['is_super_admin'] ?? 0
        ];

        $this->session->set($sessionData);
        
        // Track session untuk session management
        // Only if table exists and tracking is enabled
        $currentSessionId = session_id();
        if ($currentSessionId) {
            try {
                $db = \Config\Database::connect();
                if ($db->tableExists('user_sessions')) {
                    $this->sessionService->trackSession($user['id'], $currentSessionId);
                }
            } catch (\Exception $e) {
                // Silently fail if table doesn't exist - migration not run yet
                log_message('debug', 'Session tracking skipped: ' . $e->getMessage());
            }
        }
        
        // Remove temporary OTP session data
        $this->session->remove(['temp_user_id', 'otp_required', 'otp_email', 'remember_login']);

        // Log successful login using trait
        $this->logAuthActivity('LOGIN', $user['id'], [
            'username' => $user['username'],
            'email' => $user['email'],
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'remember_me' => $remember ? true : false,
            'otp_verified' => true,
            'description' => 'User Login Successful with OTP'
        ]);

        // Handle remember me
        if ($remember) {
            $this->setRememberToken($user['id']);
        }

        // Redirect based on user role
        $redirectUrl = $this->getRedirectUrl($sessionData['role']);
        
        return redirect()->to($redirectUrl)->with('success', 'Login berhasil! Selamat datang, ' . $user['username']);
    }

    /**
     * Resend OTP
     */
    public function resendOtp()
    {
        // Check if user is in OTP verification flow
        if (!$this->session->get('otp_required') || !$this->session->get('temp_user_id')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sesi verifikasi OTP tidak valid.'
            ]);
        }

        $userId = $this->session->get('temp_user_id');
        $email = $this->session->get('otp_email');

        // Check cooldown
        $canRequest = $this->otpService->canRequestNewOtp($userId);

        if (!$canRequest['allowed']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Silakan tunggu {$canRequest['remaining_seconds']} detik sebelum request OTP baru.",
                'remaining_seconds' => $canRequest['remaining_seconds'],
            ]);
        }

        // Generate new OTP
        $otpResult = $this->otpService->generateOtp($userId, $email);

        if (!$otpResult || isset($otpResult['error'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $otpResult['message'] ?? 'Gagal mengirim OTP. Silakan coba lagi.',
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'OTP baru telah dikirim ke email Anda.',
            'remaining_seconds' => config('AuthSecurity')->otpResendCooldownSeconds,
        ]);
    }

    /**
     * Get user role from various tables
     */
    private function getUserRole($userId)
    {
        $db = \Config\Database::connect();
        
        // Check if user is super admin
        $user = $db->table('users')
                  ->where('id', $userId)
                  ->get()
                  ->getRow();
        
        if ($user && $user->is_super_admin == 1) {
            return 'super_admin';
        }
        
        // Get user's primary role from RBAC system
        $role = $db->table('user_roles ur')
                  ->join('roles r', 'r.id = ur.role_id')
                  ->where('ur.user_id', $userId)
                  ->where('ur.is_active', 1)
                  ->where('r.is_active', 1)
                  ->orderBy('r.is_system_role', 'DESC') // System roles first
                  ->orderBy('ur.created_at', 'ASC') // First assigned role
                  ->get()
                  ->getRow();
                      
        if ($role) {
            return $role->slug;
        }
        
        // Default role if no role assigned
        return 'division_staff';
    }

    /**
     * Set remember token
     */
    private function setRememberToken($userId)
    {
        $token = bin2hex(random_bytes(32));
        
        $this->userModel->update($userId, [
            'remember_token' => $token,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        // Set cookie
        $this->response->setCookie('remember_token', $token, 30 * 24 * 3600); // 30 days
    }

    /**
     * Get redirect URL based on role
     */
    private function getRedirectUrl($role)
    {
        // All users redirect to welcome page after login
        return '/welcome';
    }

    public function register()
    {
        // If already logged in, redirect to welcome page
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/welcome');
        }

        $data = [
            'title' => 'Register',
            'validation' => $this->validator ?? null
        ];

        return view('auth/register', $data);
    }

    public function attemptRegister()
    {
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
            'terms' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'password_hash' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->insert($userData)) {
            return redirect()->to('/auth/login')->with('success', 'Registration successful! Please login with your credentials.');
        } else {
            return redirect()->back()->with('error', 'Registration failed. Please try again.');
        }
    }

    public function forgotPassword()
    {
        $data = [
            'title' => 'Forgot Password',
            'validation' => $this->validator ?? null
        ];

        return view('auth/forgot_password', $data);
    }

    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $email = $this->request->getPost('email');
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent()->getAgentString();

        // Find user by email
        $user = $this->userModel->where('email', $email)->first();

        if (!$user) {
            // Don't reveal if email exists or not (security best practice)
            return redirect()->back()->with('success', 'Jika email terdaftar, link reset password telah dikirim ke email Anda.');
        }

        // Generate reset token dan kirim email menggunakan PasswordResetService
        $result = $this->passwordResetService->generateResetToken(
            $user['id'],
            $email,
            $ipAddress,
            $userAgent
        );

        if (!$result['success']) {
            $errorMessage = $result['message'] ?? 'Gagal mengirim email reset password. Silakan coba lagi.';
            
            // Check if rate limited
            if (isset($result['error']) && $result['error'] === 'rate_limit') {
                $rateLimit = $result['rate_limit'] ?? [];
                return redirect()->back()
                    ->withInput()
                    ->with('error', $errorMessage)
                    ->with('rate_limit', $rateLimit);
            }
            
            return redirect()->back()
                ->withInput()
                ->with('error', $errorMessage);
        }

        // Log activity
        $this->logAuthActivity('PASSWORD_RESET_REQUEST', $user['id'], [
            'username' => $user['username'],
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'description' => 'Password Reset Link Requested'
        ]);

        // Don't reveal if email exists or not (security best practice)
        return redirect()->back()->with('success', 'Jika email terdaftar, link reset password telah dikirim ke email Anda. Silakan cek inbox dan folder spam.');
    }

    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Token reset password tidak valid.');
        }

        // Validate token menggunakan PasswordResetService
        $resetRecord = $this->passwordResetService->validateToken($token);

        if (!$resetRecord) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Token reset password tidak valid atau sudah expired. Silakan request reset password baru.');
        }

        // Get user
        $user = $this->userModel->find($resetRecord['user_id']);

        if (!$user) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'User tidak ditemukan.');
        }

        $data = [
            'title' => 'Reset Password',
            'token' => $token,
            'email' => $resetRecord['email'],
            'validation' => $this->validator ?? null
        ];

        return view('auth/reset_password', $data);
    }

    public function updatePassword()
    {
        $rules = [
            'token' => 'required',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $ipAddress = $this->request->getIPAddress();
        $userAgent = $this->request->getUserAgent()->getAgentString();

        // Validate token menggunakan PasswordResetService
        $resetRecord = $this->passwordResetService->validateToken($token);

        if (!$resetRecord) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Token reset password tidak valid atau sudah expired. Silakan request reset password baru.');
        }

        // Get user
        $user = $this->userModel->find($resetRecord['user_id']);

        if (!$user) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'User tidak ditemukan.');
        }

        // Check if token is single use and already used
        $authSecurityConfig = config('AuthSecurity');
        if ($authSecurityConfig->resetTokenSingleUse && $resetRecord['is_used'] == 1) {
            return redirect()->to('/auth/forgot-password')
                ->with('error', 'Token reset password sudah digunakan. Silakan request reset password baru.');
        }

        // Update password
        $updateData = [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ];

        if ($this->userModel->update($user['id'], $updateData)) {
            // Mark token as used
            $this->passwordResetService->markTokenAsUsed($token);
            
            // Invalidate all other reset tokens untuk user ini
            $this->passwordResetService->invalidateUserTokens($user['id']);

            // Clear old reset_token from users table (if exists)
            $this->userModel->update($user['id'], [
                'reset_token' => null,
                'reset_token_expires' => null
            ]);

            // Log activity
            $this->logAuthActivity('PASSWORD_RESET', $user['id'], [
                'username' => $user['username'],
                'email' => $user['email'],
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'description' => 'Password Successfully Reset'
            ]);

            return redirect()->to('/auth/login')
                ->with('success', 'Password berhasil direset! Silakan login dengan password baru Anda.');
        } else {
            return redirect()->back()
                ->with('error', 'Gagal mereset password. Silakan coba lagi.');
        }
    }

    public function logout()
    {
        // Get user data before destroying session
        $userId = $this->session->get('user_id');
        $username = $this->session->get('username');
        $currentSessionId = session_id();
        
        // Log logout activity using trait
        if ($userId) {
            $this->logAuthActivity('LOGOUT', $userId, [
                'username' => $username,
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'description' => 'User Logout Successful'
            ]);
            
            // Clear remember token if exists
            $this->userModel->update($userId, ['remember_token' => null]);
            
            // Logout session from database (only if table exists)
            if ($currentSessionId) {
                try {
                    $db = \Config\Database::connect();
                    if ($db->tableExists('user_sessions')) {
                        $this->sessionService->logoutSession($currentSessionId, $userId);
                    }
                } catch (\Exception $e) {
                    // Silently fail if table doesn't exist
                    log_message('debug', 'Session logout skipped: ' . $e->getMessage());
                }
            }
        }

        $this->session->destroy();
        $this->response->deleteCookie('remember_token');

        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully.');
    }

    /**
     * Logout specific session (for session management)
     */
    public function logoutSession($sessionId)
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu.'
            ]);
        }

        $userId = $this->session->get('user_id');
        $currentSessionId = session_id();

        // Prevent user from logging out their own current session via this endpoint
        if ($sessionId === $currentSessionId) {
            // Use regular logout instead
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Untuk logout session saat ini, gunakan tombol logout di menu.'
            ]);
        }

        $result = $this->sessionService->logoutSession($sessionId, $userId);

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message']
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);
        }
    }

    /**
     * Logout all other sessions (for session management)
     */
    public function logoutAllSessions()
    {
        if (!$this->session->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Anda harus login terlebih dahulu.'
            ]);
        }

        $userId = $this->session->get('user_id');
        $result = $this->sessionService->logoutAllSessions($userId, true); // Except current

        if ($result['success']) {
            return $this->response->setJSON([
                'success' => true,
                'message' => $result['message']
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['message']
            ]);
        }
    }


    public function profile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);
        $currentSessionId = session_id();
        
        // Get user sessions for session management
        // Only if table exists and tracking is enabled
        $sessions = [];
        $activeSessionCount = 0;
        
        try {
            $db = \Config\Database::connect();
            $tableExists = $db->tableExists('user_sessions');
            
            if ($tableExists && config('AuthSecurity')->trackDevices) {
                $sessions = $this->sessionService->getUserSessions($userId, true);
                $activeSessionCount = $this->sessionService->getActiveSessionCount($userId);
            }
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist - migration not run yet
            log_message('debug', 'Session management skipped: ' . $e->getMessage());
        }
        
        $data = [
            'title' => 'Profile',
            'user' => $user,
            'validation' => $this->validator ?? null,
            'sessions' => $sessions,
            'current_session_id' => $currentSessionId,
            'active_session_count' => $activeSessionCount,
            'track_devices' => config('AuthSecurity')->trackDevices ?? true,
            'otp_enabled' => !empty($user['otp_enabled']) && $user['otp_enabled'] == 1,
        ];

        return view('auth/profile', $data);
    }

    public function updateProfile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $userId = $this->session->get('user_id');
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[50]',
            'last_name' => 'required|min_length[2]|max_length[50]',
            'email' => "required|valid_email|is_unique[users.email,id,{$userId}]",
            'phone' => 'permit_empty|min_length[10]|max_length[15]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('validation', $this->validator);
        }

        $updateData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->update($userId, $updateData)) {
            // Update session data
            $this->session->set([
                'first_name' => $updateData['first_name'],
                'last_name' => $updateData['last_name'],
                'email' => $updateData['email']
            ]);

            return redirect()->back()->with('success', 'Profile updated successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to update profile. Please try again.');
        }
    }

    public function changePassword()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[new_password]'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->with('validation', $this->validator);
        }

        $userId = $this->session->get('user_id');
        $user = $this->userModel->find($userId);

        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Current password is incorrect.');
        }

        $updateData = [
            'password' => password_hash($this->request->getPost('new_password'), PASSWORD_DEFAULT),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($this->userModel->update($userId, $updateData)) {
            return redirect()->back()->with('success', 'Password changed successfully!');
        } else {
            return redirect()->back()->with('error', 'Failed to change password. Please try again.');
        }
    }
} 