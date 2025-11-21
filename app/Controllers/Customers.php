<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Customers extends BaseController
{
    public function index()
    {
        $data = [
            'title' => 'Customer Management',
            'page_title' => 'Customer Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/customers' => 'Customers'
            ],
            'customers' => $this->getCustomers(),
            'stats' => $this->getCustomerStats()
        ];

        return view('customers/index', $data);
    }

    public function create()
    {
        $data = [
            'title' => 'Add New Customer',
            'page_title' => 'Add New Customer',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/customers' => 'Customers',
                '/customers/create' => 'Add New'
            ]
        ];

        return view('customers/create', $data);
    }

    public function store()
    {
        // Validate input
        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[customers.email]',
            'phone' => 'required|min_length[10]|max_length[15]',
            'address' => 'required|min_length[10]',
            'company' => 'required|min_length[3]|max_length[100]',
            'tax_number' => 'permit_empty|max_length[50]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $this->validator->getErrors(),
                'token' => csrf_hash()
            ]);
        }

        // Mock successful response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Customer created successfully',
            'token' => csrf_hash()
        ]);
    }

    public function edit($id)
    {
        $customer = $this->getCustomer($id);
        
        if (!$customer) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException("Customer not found");
        }

        $data = [
            'title' => 'Edit Customer',
            'page_title' => 'Edit Customer',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/customers' => 'Customers',
                '/customers/edit/' . $id => 'Edit'
            ],
            'customer' => $customer
        ];

        return view('customers/edit', $data);
    }

    public function update($id)
    {
        // Mock successful response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Customer updated successfully',
            'token' => csrf_hash()
        ]);
    }

    public function delete($id)
    {
        // Mock successful response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Customer deleted successfully',
            'token' => csrf_hash()
        ]);
    }

    // API Methods
    public function getCustomerList()
    {
        $customers = $this->getCustomers();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $customers,
            'token' => csrf_hash()
        ]);
    }

    public function getCustomer($id)
    {
        // Mock customer data
        return [
            'id' => $id,
            'name' => 'PT. Contoh Perusahaan',
            'email' => 'contact@contohperusahaan.com',
            'phone' => '021-12345678',
            'address' => 'Jl. Contoh No. 123, Jakarta',
            'company' => 'PT. Contoh Perusahaan',
            'tax_number' => '01.234.567.8-901.000',
            'status' => 'Active',
            'created_at' => '2024-01-01'
        ];
    }

    private function getCustomers()
    {
        // Mock customer data
        return [
            [
                'id' => 1,
                'name' => 'PT. Maju Jaya',
                'email' => 'info@majujaya.com',
                'phone' => '021-12345678',
                'company' => 'PT. Maju Jaya',
                'status' => 'Active',
                'total_rentals' => 15,
                'last_rental' => '2024-01-15',
                'created_at' => '2023-06-15'
            ],
            [
                'id' => 2,
                'name' => 'CV. Sukses Mandiri',
                'email' => 'contact@suksesmandiri.com',
                'phone' => '021-98765432',
                'company' => 'CV. Sukses Mandiri',
                'status' => 'Active',
                'total_rentals' => 8,
                'last_rental' => '2024-01-10',
                'created_at' => '2023-08-20'
            ],
            [
                'id' => 3,
                'name' => 'PT. Konstruksi Prima',
                'email' => 'admin@konstruksiprima.com',
                'phone' => '021-11223344',
                'company' => 'PT. Konstruksi Prima',
                'status' => 'Inactive',
                'total_rentals' => 3,
                'last_rental' => '2023-12-05',
                'created_at' => '2023-09-10'
            ]
        ];
    }

    private function getCustomerStats()
    {
        return [
            'total_customers' => 25,
            'active_customers' => 20,
            'inactive_customers' => 5,
            'new_this_month' => 3
        ];
    }
} 