<?php
namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * Demo Controller
 * Controller untuk demo fitur-fitur baru OPTIMA
 * 
 * @package App\Controllers
 */
class Demo extends BaseController
{
    /**
     * Modern Sidebar Demo Page
     * 
     * Menampilkan demo implementasi sidebar modern dari CodePen
     * 
     * @return string
     */
    public function modernSidebar(): string
    {
        return view('demo/modern_sidebar', [
            'title' => 'Modern Sidebar Demo',
            'description' => 'Demo sidebar modern dengan desain dari CodePen'
        ]);
    }
}
