<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class Finance extends BaseController
{
    public function index()
    {
        // Check permission: User harus punya akses ke accounting module
        if (!$this->canAccess('accounting')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }
        
        $data = [
            'title' => 'Financial Management',
            'page_title' => 'Financial Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/finance' => 'Finance'
            ],
            'financial_summary' => $this->getFinancialSummary(),
            'recent_transactions' => $this->getRecentTransactions(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'loadCharts' => true, // Enable Chart.js loading
        ];

        return view('finance/index', $data);
    }

    public function invoices()
    {
        $data = [
            'title' => 'Invoice Management',
            'page_title' => 'Invoice Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/finance' => 'Finance',
                '/finance/invoices' => 'Invoices'
            ],
            'invoices' => $this->getInvoices()
        ];

        return view('finance/invoices', $data);
    }

    public function payments()
    {
        $data = [
            'title' => 'Payment Management',
            'page_title' => 'Payment Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/finance' => 'Finance',
                '/finance/payments' => 'Payments'
            ],
            'payments' => $this->getPayments()
        ];

        return view('finance/payments', $data);
    }

    public function expenses()
    {
        $data = [
            'title' => 'Expense Management',
            'page_title' => 'Expense Management',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/finance' => 'Finance',
                '/finance/expenses' => 'Expenses'
            ],
            'expenses' => $this->getExpenses()
        ];

        return view('finance/expenses', $data);
    }

    public function reports()
    {
        $data = [
            'title' => 'Financial Reports',
            'page_title' => 'Financial Reports',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/finance' => 'Finance',
                '/finance/reports' => 'Reports'
            ],
            'report_data' => $this->getFinancialReports()
        ];

        return view('finance/reports', $data);
    }

    // API Methods
    public function getInvoiceList()
    {
        $invoices = $this->getInvoices();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $invoices,
            'token' => csrf_hash()
        ]);
    }

    public function createInvoice()
    {
        // Check permission: User harus punya manage permission untuk accounting
        if (!$this->canManage('accounting')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied: You do not have permission to create invoice'
            ])->setStatusCode(403);
        }
        
        // Mock successful response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Invoice created successfully',
            'token' => csrf_hash()
        ]);
    }

    public function updatePaymentStatus($id)
    {
        // Mock successful response
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'token' => csrf_hash()
        ]);
    }

    private function getFinancialSummary()
    {
        return [
            'total_revenue' => 2500000000, // 2.5 M
            'total_expenses' => 1200000000, // 1.2 M
            'net_profit' => 1300000000,    // 1.3 M
            'outstanding_invoices' => 350000000, // 350 juta
            'pending_payments' => 180000000,     // 180 juta
            'cash_flow' => 850000000            // 850 juta
        ];
    }

    private function getRecentTransactions()
    {
        return [
            [
                'id' => 'TXN-2024-001',
                'type' => 'Income',
                'amount' => 50000000,
                'description' => 'Rental payment - PT. Maju Jaya',
                'date' => '2024-01-15',
                'status' => 'Completed'
            ],
            [
                'id' => 'TXN-2024-002',
                'type' => 'Expense',
                'amount' => 15000000,
                'description' => 'Fuel for fleet',
                'date' => '2024-01-14',
                'status' => 'Completed'
            ],
            [
                'id' => 'TXN-2024-003',
                'type' => 'Income',
                'amount' => 75000000,
                'description' => 'Rental payment - CV. Sukses Mandiri',
                'date' => '2024-01-13',
                'status' => 'Pending'
            ]
        ];
    }

    private function getMonthlyRevenue()
    {
        return [
            'January' => 250000000,
            'February' => 280000000,
            'March' => 320000000,
            'April' => 290000000,
            'May' => 310000000,
            'June' => 340000000,
            'July' => 360000000,
            'August' => 380000000,
            'September' => 350000000,
            'October' => 390000000,
            'November' => 410000000,
            'December' => 450000000
        ];
    }

    private function getInvoices()
    {
        return [
            [
                'id' => 'INV-2024-001',
                'customer' => 'PT. Maju Jaya',
                'amount' => 50000000,
                'due_date' => '2024-01-20',
                'status' => 'Paid',
                'created_at' => '2024-01-01'
            ],
            [
                'id' => 'INV-2024-002',
                'customer' => 'CV. Sukses Mandiri',
                'amount' => 75000000,
                'due_date' => '2024-01-25',
                'status' => 'Pending',
                'created_at' => '2024-01-05'
            ],
            [
                'id' => 'INV-2024-003',
                'customer' => 'PT. Konstruksi Prima',
                'amount' => 30000000,
                'due_date' => '2024-01-18',
                'status' => 'Overdue',
                'created_at' => '2024-01-03'
            ]
        ];
    }

    private function getPayments()
    {
        return [
            [
                'id' => 'PAY-2024-001',
                'invoice_id' => 'INV-2024-001',
                'customer' => 'PT. Maju Jaya',
                'amount' => 50000000,
                'method' => 'Bank Transfer',
                'date' => '2024-01-15 10:30:00',
                'status' => 'Completed'
            ],
            [
                'id' => 'PAY-2024-002',
                'invoice_id' => 'INV-2024-002',
                'customer' => 'CV. Sukses Mandiri',
                'amount' => 75000000,
                'method' => 'Cash',
                'date' => '2024-01-20 14:15:00',
                'status' => 'Pending'
            ],
            [
                'id' => 'PAY-2024-003',
                'invoice_id' => 'INV-2024-003',
                'customer' => 'PT. Berkah Selalu',
                'amount' => 35000000,
                'method' => 'Check',
                'date' => '2024-01-12 09:15:00',
                'status' => 'Completed'
            ],
            [
                'id' => 'PAY-2024-004',
                'invoice_id' => 'INV-2024-004',
                'customer' => 'CV. Mandiri Jaya',
                'amount' => 45000000,
                'method' => 'Credit Card',
                'date' => '2024-01-11 16:45:00',
                'status' => 'Failed'
            ]
        ];
    }

    private function getExpenses()
    {
        return [
            [
                'id' => 'EXP-2024-001',
                'category' => 'Fuel',
                'amount' => 15000000,
                'description' => 'Monthly fuel for fleet',
                'date' => '2024-01-10',
                'status' => 'Approved',
                'submitted_by' => 'John Doe'
            ],
            [
                'id' => 'EXP-2024-002',
                'category' => 'Maintenance',
                'amount' => 25000000,
                'description' => 'Forklift maintenance',
                'date' => '2024-01-12',
                'status' => 'Pending',
                'submitted_by' => 'Jane Smith'
            ],
            [
                'id' => 'EXP-2024-003',
                'category' => 'Equipment',
                'amount' => 8000000,
                'description' => 'Spare parts purchase',
                'date' => '2024-01-14',
                'status' => 'Approved',
                'submitted_by' => 'Mike Johnson'
            ],
            [
                'id' => 'EXP-2024-004',
                'category' => 'Office Supplies',
                'amount' => 3500000,
                'description' => 'Monthly office supplies',
                'date' => '2024-01-08',
                'status' => 'Approved',
                'submitted_by' => 'Sarah Wilson'
            ],
            [
                'id' => 'EXP-2024-005',
                'category' => 'Transportation',
                'amount' => 12000000,
                'description' => 'Transportation costs',
                'date' => '2024-01-16',
                'status' => 'Rejected',
                'submitted_by' => 'David Brown'
            ],
            [
                'id' => 'EXP-2024-006',
                'category' => 'Utilities',
                'amount' => 18000000,
                'description' => 'Monthly utility bills',
                'date' => '2024-01-05',
                'status' => 'Approved',
                'submitted_by' => 'Lisa Anderson'
            ]
        ];
    }

    private function getFinancialReports()
    {
        return [
            'profit_loss' => [
                'revenue' => 2500000000,
                'cost_of_goods' => 800000000,
                'gross_profit' => 1700000000,
                'operating_expenses' => 400000000,
                'net_profit' => 1300000000
            ],
            'cash_flow' => [
                'operating_activities' => 1200000000,
                'investing_activities' => -200000000,
                'financing_activities' => -150000000,
                'net_cash_flow' => 850000000
            ],
            'balance_sheet' => [
                'total_assets' => 5000000000,
                'total_liabilities' => 1500000000,
                'equity' => 3500000000
            ]
        ];
    }
} 