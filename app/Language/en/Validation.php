<?php

/**
 * English Language File - Validation
 * 
 * Form validation messages
 * 
 * @package App\Language\en
 * @version 1.0.0
 */

return [
    // General
    'required' => 'The {field} field is required.',
    'required_with' => 'The {field} field is required when {param} is present.',
    'required_without' => 'The {field} field is required when {param} is not present.',
    'valid_email' => 'The {field} field must contain a valid email address.',
    'valid_emails' => 'The {field} field must contain all valid email addresses.',
    'valid_url' => 'The {field} field must contain a valid URL.',
    'valid_ip' => 'The {field} field must contain a valid IP.',
    'valid_date' => 'The {field} field must contain a valid date.',
    
    // String
    'min_length' => 'The {field} field must be at least {param} characters in length.',
    'max_length' => 'The {field} field cannot exceed {param} characters in length.',
    'exact_length' => 'The {field} field must be exactly {param} characters in length.',
    'alpha' => 'The {field} field may only contain alphabetical characters.',
    'alpha_numeric' => 'The {field} field may only contain alpha-numeric characters.',
    'alpha_numeric_space' => 'The {field} field may only contain alpha-numeric characters and spaces.',
    'alpha_dash' => 'The {field} field may only contain alpha-numeric characters, underscores, and dashes.',
    'alpha_numeric_punct' => 'The {field} field may only contain alpha-numeric characters, spaces, and punctuation.',
    'alpha_space' => 'The {field} field may only contain alphabetical characters and spaces.',
    
    // Numeric
    'numeric' => 'The {field} field must contain only numbers.',
    'integer' => 'The {field} field must contain an integer.',
    'decimal' => 'The {field} field must contain a decimal number.',
    'is_natural' => 'The {field} field must contain only natural numbers.',
    'is_natural_no_zero' => 'The {field} field must contain a number greater than zero.',
    'greater_than' => 'The {field} field must contain a number greater than {param}.',
    'greater_than_equal_to' => 'The {field} field must contain a number greater than or equal to {param}.',
    'less_than' => 'The {field} field must contain a number less than {param}.',
    'less_than_equal_to' => 'The {field} field must contain a number less than or equal to {param}.',
    'in_list' => 'The {field} field must be one of: {param}.',
    'not_in_list' => 'The {field} field must not be one of: {param}.',
    
    // Matches
    'matches' => 'The {field} field does not match the {param} field.',
    'differs' => 'The {field} field must differ from the {param} field.',
    'regex_match' => 'The {field} field is not in the correct format.',
    
    // Database
    'is_unique' => 'The {field} field must contain a unique value.',
    'is_not_unique' => 'The {field} field must contain a previously existing value.',
    'valid_base64' => 'The {field} field must contain a valid base64 string.',
    'valid_json' => 'The {field} field must contain valid json.',
    
    // File
    'uploaded' => 'The {field} is not a valid uploaded file.',
    'max_size' => 'The {field} file is too large.',
    'max_dims' => 'The {field} exceeds maximum dimensions.',
    'mime_in' => 'The {field} must have a valid file type.',
    'ext_in' => 'The {field} must have a valid file extension.',
    'is_image' => 'The {field} must be a valid image.',
    
    // Authentication
    'valid_password' => 'Password must be at least 8 characters, contain uppercase, lowercase, and numbers.',
    'password_match' => 'Password confirmation does not match.',
    'old_password_match' => 'Old password does not match.',
    'username_exists' => 'Username is already taken.',
    'email_exists' => 'Email is already registered.',
    'phone_exists' => 'Phone number is already registered.',
    
    // Business Logic
    'valid_unit' => 'Unit is invalid or not available.',
    'unit_available' => 'Unit is not available for the selected period.',
    'valid_customer' => 'Customer is invalid.',
    'valid_quotation' => 'Quotation is invalid.',
    'valid_spk' => 'SPK is invalid.',
    'valid_service_order' => 'Service order is invalid.',
    'valid_invoice' => 'Invoice is invalid.',
    'valid_payment' => 'Payment is invalid.',
    
    // Date & Time
    'valid_time' => 'The {field} field must contain a valid time.',
    'valid_datetime' => 'The {field} field must contain a valid date and time.',
    'date_before' => 'The {field} must be before {param}.',
    'date_after' => 'The {field} must be after {param}.',
    'date_between' => 'The {field} must be between {param} and {param2}.',
    'start_date_before_end' => 'Start date must be before end date.',
    'end_date_after_start' => 'End date must be after start date.',
    
    // Custom Messages
    'invalid_credentials' => 'Invalid username or password.',
    'account_inactive' => 'Your account is inactive.',
    'account_suspended' => 'Your account has been suspended.',
    'insufficient_permission' => 'You do not have permission to perform this action.',
    'invalid_token' => 'Invalid or expired token.',
    'session_expired' => 'Your session has expired. Please login again.',
    
    // Field Names
    'fields' => [
        'username' => 'Username',
        'password' => 'Password',
        'email' => 'Email',
        'name' => 'Name',
        'phone' => 'Phone',
        'address' => 'Address',
        'city' => 'City',
        'province' => 'Province',
        'postal_code' => 'Postal Code',
        'date' => 'Date',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'unit' => 'Unit',
        'customer' => 'Customer',
        'quantity' => 'Quantity',
        'price' => 'Price',
        'amount' => 'Amount',
        'description' => 'Description',
        'notes' => 'Notes',
        'status' => 'Status',
        'type' => 'Type',
        'category' => 'Category',
    ],
];
