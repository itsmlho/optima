<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    use ActivityLoggingTrait;
    
    protected $userModel;
    protected $session;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->session = session();
        
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
        
        return redirect()->to('/dashboard');
    }

    public function login()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $data = [
            'title' => 'Login',
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

        // Find user by username or email
        $user = $this->userModel->where('username', $username)
                                ->orWhere('email', $username)
                                ->where('is_active', 1)
                                ->first();

        if (!$user) {
            return redirect()->back()->withInput()->with('error', 'Username/Email atau password salah.');
        }

        // Verify password
        if (!password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Username/Email atau password salah.');
        }

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

        // Log successful login using trait
        $this->logAuthActivity('LOGIN', $user['id'], [
            'username' => $user['username'],
            'email' => $user['email'],
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => $this->request->getUserAgent()->getAgentString(),
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
        switch ($role) {
            case 'super_admin':
            case 'admin':
                return '/admin/advanced-users';
            case 'manager':
                return '/dashboard';
            default:
                return '/dashboard';
        }
    }

    public function register()
    {
        // If already logged in, redirect to dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
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
        $user = $this->userModel->where('email', $email)->first();

        if ($user) {
            $token = bin2hex(random_bytes(32));
            $this->userModel->update($user['id'], [
                'reset_token' => $token,
                'reset_token_expires' => date('Y-m-d H:i:s', strtotime('+1 hour'))
            ]);

            // Here you would send the email with the reset link
            // For now, we'll just show a success message
            return redirect()->back()->with('success', 'Password reset link has been sent to your email address.');
        } else {
            return redirect()->back()->with('error', 'Email address not found.');
        }
    }

    public function resetPassword($token = null)
    {
        if (!$token) {
            return redirect()->to('/auth/forgot-password');
        }

        $user = $this->userModel->where('reset_token', $token)
                                ->where('reset_token_expires >', date('Y-m-d H:i:s'))
                                ->first();

        if (!$user) {
            return redirect()->to('/auth/forgot-password')->with('error', 'Invalid or expired reset token.');
        }

        $data = [
            'title' => 'Reset Password',
            'token' => $token,
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

        $user = $this->userModel->where('reset_token', $token)
                                ->where('reset_token_expires >', date('Y-m-d H:i:s'))
                                ->first();

        if (!$user) {
            return redirect()->to('/auth/forgot-password')->with('error', 'Invalid or expired reset token.');
        }

        $updateData = [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'reset_token' => null,
            'reset_token_expires' => null
        ];

        if ($this->userModel->update($user['id'], $updateData)) {
            return redirect()->to('/auth/login')->with('success', 'Password updated successfully! Please login with your new password.');
        } else {
            return redirect()->back()->with('error', 'Failed to update password. Please try again.');
        }
    }

    public function logout()
    {
        // Get user data before destroying session
        $userId = $this->session->get('user_id');
        $username = $this->session->get('username');
        
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
        }

        $this->session->destroy();
        $this->response->deleteCookie('remember_token');

        return redirect()->to('/auth/login')->with('success', 'You have been logged out successfully.');
    }

    public function profile()
    {
        if (!$this->session->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $user = $this->userModel->find($this->session->get('user_id'));
        
        $data = [
            'title' => 'Profile',
            'user' => $user,
            'validation' => $this->validator ?? null
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