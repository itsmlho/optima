<?php

/**
 * English Language File - Finance
 * 
 * Finance module: Invoices, Payments, Billing, Accounting
 * 
 * @package App\Language\en
 * @version 1.0.0
 */

return [
    // Module
    'module_name' => 'Finance',
    'title' => 'Finance',
    'finance' => 'Finance',
    'accounting' => 'Accounting',
    
    // Invoice
    'invoice' => 'Invoice',
    'invoices' => 'Invoices',
    'invoice_number' => 'Invoice Number',
    'invoice_date' => 'Invoice Date',
    'invoice_due_date' => 'Due Date',
    'invoice_status' => 'Invoice Status',
    'invoice_total' => 'Invoice Total',
    'invoice_amount' => 'Invoice Amount',
    'create_invoice' => 'Create Invoice',
    'generate_invoice' => 'Generate Invoice',
    'send_invoice' => 'Send Invoice',
    'print_invoice' => 'Print Invoice',
    
    // Invoice Status
    'draft' => 'Draft',
    'pending' => 'Pending',
    'sent' => 'Sent',
    'paid' => 'Paid',
    'partial_paid' => 'Partially Paid',
    'overdue' => 'Overdue',
    'cancelled' => 'Cancelled',
    'void' => 'Void',
    
    // Payment
    'payment' => 'Payment',
    'payments' => 'Payments',
    'payment_date' => 'Payment Date',
    'payment_method' => 'Payment Method',
    'payment_status' => 'Payment Status',
    'payment_amount' => 'Payment Amount',
    'payment_received' => 'Payment Received',
    'payment_reference' => 'Payment Reference',
    'payment_note' => 'Payment Note',
    'add_payment' => 'Add Payment',
    'record_payment' => 'Record Payment',
    
    // Payment Methods
    'cash' => 'Cash',
    'bank_transfer' => 'Bank Transfer',
    'credit_card' => 'Credit Card',
    'debit_card' => 'Debit Card',
    'check' => 'Check',
    'giro' => 'Giro',
    'virtual_account' => 'Virtual Account',
    'e_wallet' => 'E-Wallet',
    
    // Billing
    'billing' => 'Billing',
    'bill' => 'Bill',
    'bills' => 'Bills',
    'bill_to' => 'Bill To',
    'billing_address' => 'Billing Address',
    'billing_period' => 'Billing Period',
    'billing_cycle' => 'Billing Cycle',
    
    // Revenue
    'revenue' => 'Revenue',
    'income' => 'Income',
    'total_revenue' => 'Total Revenue',
    'monthly_revenue' => 'Monthly Revenue',
    'yearly_revenue' => 'Yearly Revenue',
    'rental_revenue' => 'Rental Revenue',
    'service_revenue' => 'Service Revenue',
    
    // Expense
    'expense' => 'Expense',
    'expenses' => 'Expenses',
    'expense_date' => 'Expense Date',
    'expense_category' => 'Expense Category',
    'expense_amount' => 'Expense Amount',
    'total_expense' => 'Total Expense',
    'operational_expense' => 'Operational Expense',
    'maintenance_expense' => 'Maintenance Expense',
    
    // Account
    'account' => 'Account',
    'accounts' => 'Accounts',
    'account_name' => 'Account Name',
    'account_number' => 'Account Number',
    'account_type' => 'Account Type',
    'bank_account' => 'Bank Account',
    'cash_account' => 'Cash Account',
    
    // Transaction
    'transaction' => 'Transaction',
    'transactions' => 'Transactions',
    'transaction_date' => 'Transaction Date',
    'transaction_type' => 'Transaction Type',
    'transaction_amount' => 'Transaction Amount',
    'transaction_reference' => 'Transaction Reference',
    
    // Items & Details
    'item' => 'Item',
    'items' => 'Items',
    'description' => 'Description',
    'quantity' => 'Quantity',
    'unit_price' => 'Unit Price',
    'subtotal' => 'Subtotal',
    'discount' => 'Discount',
    'discount_amount' => 'Discount Amount',
    'discount_percentage' => 'Discount Percentage',
    'tax' => 'Tax',
    'tax_amount' => 'Tax Amount',
    'vat' => 'VAT',
    'ppn' => 'VAT',
    'pph' => 'Income Tax',
    
    // Amounts
    'amount' => 'Amount',
    'total' => 'Total',
    'grand_total' => 'Grand Total',
    'total_amount' => 'Total Amount',
    'paid_amount' => 'Paid Amount',
    'remaining_amount' => 'Remaining Amount',
    'outstanding_amount' => 'Outstanding Amount',
    'balance' => 'Balance',
    'balance_due' => 'Balance Due',
    
    // Terms
    'payment_terms' => 'Payment Terms',
    'net_30' => 'Net 30',
    'net_60' => 'Net 60',
    'due_on_receipt' => 'Due on Receipt',
    'terms_and_conditions' => 'Terms and Conditions',
    
    // Credit & Debit
    'credit' => 'Credit',
    'debit' => 'Debit',
    'credit_note' => 'Credit Note',
    'debit_note' => 'Debit Note',
    'refund' => 'Refund',
    'refund_amount' => 'Refund Amount',
    
    // Reports
    'report' => 'Report',
    'reports' => 'Reports',
    'financial_report' => 'Financial Report',
    'generate_custom_financial_report' => 'Generate Custom Financial Report',
    'income_statement' => 'Income Statement',
    'balance_sheet' => 'Balance Sheet',
    'cash_flow' => 'Cash Flow',
    'aged_receivables' => 'Aged Receivables',
    'aged_payables' => 'Aged Payables',
    'profit_loss' => 'Profit & Loss',
    
    // Periods
    'period' => 'Period',
    'daily' => 'Daily',
    'weekly' => 'Weekly',
    'monthly' => 'Monthly',
    'quarterly' => 'Quarterly',
    'yearly' => 'Yearly',
    'from_date' => 'From Date',
    'to_date' => 'To Date',
    
    // Actions
    'approve' => 'Approve',
    'reject' => 'Reject',
    'cancel' => 'Cancel',
    'void_invoice' => 'Void Invoice',
    'send_reminder' => 'Send Reminder',
    'mark_as_paid' => 'Mark as Paid',
    'export_pdf' => 'Export PDF',
    'export_excel' => 'Export Excel',
    
    // Messages
    'invoice_created' => 'Invoice created successfully',
    'invoice_updated' => 'Invoice updated successfully',
    'invoice_deleted' => 'Invoice deleted successfully',
    'invoice_sent' => 'Invoice sent successfully',
    'payment_recorded' => 'Payment recorded successfully',
    'payment_received' => 'Payment received successfully',
    'invoice_paid' => 'Invoice has been paid',
    'invoice_overdue' => 'Invoice is overdue',
    
    // Validations
    'invoice_number_required' => 'Invoice number is required',
    'date_required' => 'Date is required',
    'customer_required' => 'Customer is required',
    'amount_required' => 'Amount is required',
    'invalid_amount' => 'Invalid amount',
    'payment_exceeds_invoice' => 'Payment exceeds invoice total',
    'cannot_delete_paid_invoice' => 'Cannot delete paid invoice',
];
