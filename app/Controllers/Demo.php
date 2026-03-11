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

    /**
     * CodingNepal Sidebar Demo Page
     * 
     * Sidebar floating smooth tanpa session - desain dari CodingNepal
     * 
     * @return string
     */
    public function sidebarCodingnepal(): string
    {
        return view('demo/sidebar_codingnepal');
    }

    /**
     * Sidebar Improved Demo - preview transisi halus, icon rapi, dropdown poles
     */
    public function sidebarImproved(): string
    {
        return view('demo/sidebar_improved_demo');
    }
}
