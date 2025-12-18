<?php

/**
 * Indonesian Language File - Validation
 * 
 * Form validation messages
 * 
 * @package App\Language\id
 * @version 1.0.0
 */

return [
    // General
    'required' => 'Kolom {field} wajib diisi.',
    'required_with' => 'Kolom {field} wajib diisi jika {param} ada.',
    'required_without' => 'Kolom {field} wajib diisi jika {param} tidak ada.',
    'valid_email' => 'Kolom {field} harus berisi alamat email yang valid.',
    'valid_emails' => 'Kolom {field} harus berisi semua alamat email yang valid.',
    'valid_url' => 'Kolom {field} harus berisi URL yang valid.',
    'valid_ip' => 'Kolom {field} harus berisi IP yang valid.',
    'valid_date' => 'Kolom {field} harus berisi tanggal yang valid.',
    
    // String
    'min_length' => '{field} minimal harus {param} karakter.',
    'max_length' => '{field} maksimal {param} karakter.',
    'exact_length' => '{field} harus tepat {param} karakter.',
    'alpha' => '{field} hanya boleh berisi karakter alfabet.',
    'alpha_numeric' => '{field} hanya boleh berisi karakter alfanumerik.',
    'alpha_numeric_space' => '{field} hanya boleh berisi karakter alfanumerik dan spasi.',
    'alpha_dash' => '{field} hanya boleh berisi karakter alfanumerik, underscore, dan dash.',
    'alpha_numeric_punct' => '{field} hanya boleh berisi karakter alfanumerik, spasi, dan karakter tanda baca.',
    'alpha_space' => '{field} hanya boleh berisi karakter alfabet dan spasi.',
    
    // Numeric
    'numeric' => '{field} harus berisi angka saja.',
    'integer' => '{field} harus berisi bilangan bulat.',
    'decimal' => '{field} harus berisi angka desimal.',
    'is_natural' => '{field} harus berisi angka natural.',
    'is_natural_no_zero' => '{field} harus berisi angka lebih besar dari nol.',
    'greater_than' => '{field} harus berisi angka lebih besar dari {param}.',
    'greater_than_equal_to' => '{field} harus berisi angka lebih besar atau sama dengan {param}.',
    'less_than' => '{field} harus berisi angka lebih kecil dari {param}.',
    'less_than_equal_to' => '{field} harus berisi angka lebih kecil atau sama dengan {param}.',
    'in_list' => '{field} harus salah satu dari: {param}.',
    'not_in_list' => '{field} tidak boleh salah satu dari: {param}.',
    
    // Matches
    'matches' => '{field} tidak cocok dengan field {param}.',
    'differs' => '{field} harus berbeda dari field {param}.',
    'regex_match' => '{field} tidak dalam format yang benar.',
    
    // Database
    'is_unique' => '{field} harus berisi nilai yang unik.',
    'is_not_unique' => '{field} harus berisi nilai yang sudah ada sebelumnya.',
    'valid_base64' => '{field} harus berisi string base64 yang valid.',
    'valid_json' => '{field} harus berisi json yang valid.',
    
    // File
    'uploaded' => '{field} bukan file upload yang valid.',
    'max_size' => '{field} terlalu besar.',
    'max_dims' => '{field} melebihi dimensi maksimum.',
    'mime_in' => '{field} harus memiliki tipe file yang valid.',
    'ext_in' => '{field} harus memiliki ekstensi file yang valid.',
    'is_image' => '{field} harus berupa gambar yang valid.',
    
    // Authentication
    'valid_password' => 'Password harus minimal 8 karakter, mengandung huruf besar, huruf kecil, dan angka.',
    'password_match' => 'Konfirmasi password tidak cocok.',
    'old_password_match' => 'Password lama tidak sesuai.',
    'username_exists' => 'Username sudah digunakan.',
    'email_exists' => 'Email sudah terdaftar.',
    'phone_exists' => 'Nomor telepon sudah terdaftar.',
    
    // Business Logic
    'valid_unit' => 'Unit tidak valid atau tidak tersedia.',
    'unit_available' => 'Unit tidak tersedia untuk periode yang dipilih.',
    'valid_customer' => 'Pelanggan tidak valid.',
    'valid_quotation' => 'Penawaran tidak valid.',
    'valid_spk' => 'SPK tidak valid.',
    'valid_service_order' => 'Order layanan tidak valid.',
    'valid_invoice' => 'Invoice tidak valid.',
    'valid_payment' => 'Pembayaran tidak valid.',
    
    // Date & Time
    'valid_time' => '{field} harus berisi waktu yang valid.',
    'valid_datetime' => '{field} harus berisi tanggal dan waktu yang valid.',
    'date_before' => '{field} harus sebelum {param}.',
    'date_after' => '{field} harus setelah {param}.',
    'date_between' => '{field} harus antara {param} dan {param2}.',
    'start_date_before_end' => 'Tanggal mulai harus sebelum tanggal selesai.',
    'end_date_after_start' => 'Tanggal selesai harus setelah tanggal mulai.',
    
    // Custom Messages
    'invalid_credentials' => 'Username atau password salah.',
    'account_inactive' => 'Akun Anda tidak aktif.',
    'account_suspended' => 'Akun Anda telah ditangguhkan.',
    'insufficient_permission' => 'Anda tidak memiliki izin untuk melakukan aksi ini.',
    'invalid_token' => 'Token tidak valid atau telah kedaluwarsa.',
    'session_expired' => 'Sesi Anda telah berakhir. Silakan login kembali.',
    
    // Field Names
    'fields' => [
        'username' => 'Username',
        'password' => 'Password',
        'email' => 'Email',
        'name' => 'Nama',
        'phone' => 'Telepon',
        'address' => 'Alamat',
        'city' => 'Kota',
        'province' => 'Provinsi',
        'postal_code' => 'Kode Pos',
        'date' => 'Tanggal',
        'start_date' => 'Tanggal Mulai',
        'end_date' => 'Tanggal Selesai',
        'unit' => 'Unit',
        'customer' => 'Pelanggan',
        'quantity' => 'Jumlah',
        'price' => 'Harga',
        'amount' => 'Jumlah',
        'description' => 'Deskripsi',
        'notes' => 'Catatan',
        'status' => 'Status',
        'type' => 'Tipe',
        'category' => 'Kategori',
    ],
];
