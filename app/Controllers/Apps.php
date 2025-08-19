<?php

namespace App\Controllers;

class Apps extends BaseController
{
    public function calendar(): string
    {
        $data = [
            'title' => 'Calendar & Scheduler',
        ];
        return view('apps/calendar', $data);
    }

    public function messages(): string
    {
        $data = [
            'title' => 'Messages & Communication',
        ];
        return view('apps/messages', $data);
    }

    public function settings(): string
    {
        $data = [
            'title' => 'System Settings',
        ];
        return view('apps/settings', $data);
    }

    public function analytics(): string
    {
        $data = [
            'title' => 'Analytics & Reports',
        ];
        return view('apps/analytics', $data);
    }
} 