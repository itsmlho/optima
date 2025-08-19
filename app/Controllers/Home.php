<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    { 
        $data = [ 
            'title' => 'Dashboard', 
        ]; 
        return view('dashboard', $data); 
    }
    
    public function debugTopbar(): string
    {
        $data = [
            'title' => 'Debug Topbar',
        ];
        return view('dashboard', $data);
    }
}
