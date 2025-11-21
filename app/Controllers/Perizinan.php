<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Perizinan extends BaseController
{
    /**
     * SILO (Surat Izin Layak Operasi) Management
     */
    public function silo()
    {
        if (!$this->hasPermission('perizinan.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $data = [
            'title' => 'SILO Management',
            'page_title' => 'SILO (Surat Izin Layak Operasi)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/perizinan/silo' => 'SILO Management'
            ]
        ];

        return view('perizinan/silo', $data);
    }

    /**
     * EMISI (Surat Izin Emisi Gas Buang) Management
     */
    public function emisi()
    {
        if (!$this->hasPermission('perizinan.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $data = [
            'title' => 'EMISI Management',
            'page_title' => 'EMISI (Surat Izin Emisi Gas Buang)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/perizinan/emisi' => 'EMISI Management'
            ]
        ];

        return view('perizinan/emisi', $data);
    }
}
