<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Welcome extends BaseController
{
    /**
     * Welcome page - First page after login
     * Requires authentication
     */
    public function index()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/auth/login');
        }

        $data = [
            'title' => 'Welcome - OPTIMA',
            'page_title' => 'Selamat Datang di OPTIMA',
            'breadcrumbs' => [
                '/' => 'Welcome'
            ],
            'user' => [
                'name' => session()->get('first_name') . ' ' . session()->get('last_name'),
                'username' => session()->get('username'),
                'email' => session()->get('email'),
                'role' => session()->get('role'),
            ]
        ];

        return view('welcome', $data);
    }
}

