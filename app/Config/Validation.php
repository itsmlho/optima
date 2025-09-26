<?php

namespace Config;

use App\Validation\CustomRules;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        CustomRules::class,
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    
    public array $work_order_store = [
        'unit_id' => [
            'label' => 'Unit',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'Unit harus dipilih',
                'integer' => 'Unit ID harus berupa angka'
            ]
        ],
        'order_type' => [
            'label' => 'Tipe Order',
            'rules' => 'required',
            'errors' => [
                'required' => 'Tipe order harus dipilih'
            ]
        ],
        'priority_id' => [
            'label' => 'Priority',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'Priority harus dipilih',
                'integer' => 'Priority ID harus berupa angka'
            ]
        ],
        'category_id' => [
            'label' => 'Kategori',
            'rules' => 'required|integer',
            'errors' => [
                'required' => 'Kategori harus dipilih',
                'integer' => 'Category ID harus berupa angka'
            ]
        ],
        'complaint_description' => [
            'label' => 'Deskripsi Keluhan',
            'rules' => 'required|min_length[5]',
            'errors' => [
                'required' => 'Deskripsi keluhan harus diisi',
                'min_length' => 'Deskripsi keluhan minimal 5 karakter'
            ]
        ]
    ];
}
