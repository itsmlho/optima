<?php

/**
 * Indonesian Language File - Service
 * 
 * Service module: Maintenance, Repairs, Mechanics, Parts
 * 
 * @package App\Language\id
 * @version 1.0.0
 */

return [
    // Module
    'module_name' => 'Service',
    'title' => 'Service',
    
    // Service Order
    'service_order' => 'Order Layanan',
    'service_list' => 'Daftar Layanan',
    'service_create' => 'Buat Layanan Baru',
    'service_edit' => 'Edit Layanan',
    'service_detail' => 'Detail Layanan',
    'service_number' => 'Nomor Layanan',
    'service_date' => 'Tanggal Layanan',
    'service_type' => 'Tipe Layanan',
    'service_status' => 'Status Layanan',
    'service_priority' => 'Prioritas Layanan',
    
    // Service Types
    'maintenance' => 'Pemeliharaan',
    'repair' => 'Perbaikan',
    'inspection' => 'Inspeksi',
    'preventive' => 'Preventif',
    'corrective' => 'Korektif',
    'breakdown' => 'Kerusakan',
    'overhaul' => 'Overhaul',
    
    // Priority
    'low_priority' => 'Prioritas Rendah',
    'normal_priority' => 'Prioritas Normal',
    'high_priority' => 'Prioritas Tinggi',
    'urgent_priority' => 'Prioritas Mendesak',
    
    // Status
    'pending' => 'Tertunda',
    'in_progress' => 'Sedang Dikerjakan',
    'on_hold' => 'Ditunda',
    'completed' => 'Selesai',
    'cancelled' => 'Dibatalkan',
    'waiting_parts' => 'Menunggu Sparepart',
    'ready_for_pickup' => 'Siap Diambil',
    
    // Mechanics
    'mechanic' => 'Mekanik',
    'mechanics' => 'Mekanik',
    'assign_mechanic' => 'Tugaskan Mekanik',
    'mechanic_name' => 'Nama Mekanik',
    'lead_mechanic' => 'Mekanik Utama',
    'assistant_mechanic' => 'Mekanik Pembantu',
    'mechanic_assigned' => 'Mekanik Ditugaskan',
    'mechanic_availability' => 'Ketersediaan Mekanik',
    
    // Work Details
    'work_description' => 'Deskripsi Pekerjaan',
    'work_performed' => 'Pekerjaan yang Dilakukan',
    'findings' => 'Temuan',
    'diagnosis' => 'Diagnosis',
    'root_cause' => 'Penyebab Utama',
    'solution' => 'Solusi',
    'recommendation' => 'Rekomendasi',
    
    // Time & Duration
    'start_time' => 'Waktu Mulai',
    'end_time' => 'Waktu Selesai',
    'estimated_time' => 'Estimasi Waktu',
    'actual_time' => 'Waktu Aktual',
    'duration' => 'Durasi',
    'working_hours' => 'Jam Kerja',
    'downtime' => 'Downtime',
    
    // Parts & Materials
    'parts' => 'Suku Cadang',
    'spareparts' => 'Suku Cadang',
    'part_number' => 'Nomor Suku Cadang',
    'part_name' => 'Nama Suku Cadang',
    'part_quantity' => 'Jumlah Suku Cadang',
    'part_used' => 'Suku Cadang yang Digunakan',
    'part_replaced' => 'Suku Cadang yang Diganti',
    'materials' => 'Bahan',
    'consumables' => 'Bahan Habis Pakai',
    'lubricants' => 'Pelumas',
    'oil_change' => 'Ganti Oli',
    'filter_change' => 'Ganti Filter',
    
    // Costs
    'labor_cost' => 'Biaya Tenaga Kerja',
    'part_cost' => 'Biaya Part',
    'material_cost' => 'Biaya Bahan',
    'total_cost' => 'Total Biaya',
    'service_charge' => 'Biaya Layanan',

    // Additional Keys
    'work_orders' => 'Perintah Kerja',
    'work_order' => 'Perintah Kerja',
    'new_work_order' => 'Perintah Kerja Baru',
    'open' => 'Buka',
    'no_permission_view_spk' => 'Anda tidak memiliki izin untuk melihat SPK ini',
    'preventive_maintenance_system' => 'Sistem Pemeliharaan Berkala',
    'pmps_coming_soon_description' => 'Modul PMPS sedang dalam pengembangan untuk mengelola jadwal pemeliharaan preventif unit forklift. Fitur ini akan membantu tim service dalam merencanakan dan melacak maintenance rutin.',
    'maintenance_schedule' => 'Jadwal Pemeliharaan',
    'service_tracking' => 'Tracking Layanan',
    'performance_analysis' => 'Analisis Performa',
    
    // Unit Information
    'unit' => 'Unit',
    'unit_code' => 'Kode Unit',
    'unit_type' => 'Tipe Unit',
    'unit_location' => 'Lokasi Unit',
    'unit_condition' => 'Kondisi Unit',
    'unit_mileage' => 'Kilometer Unit',
    'unit_hours' => 'Jam Operasi Unit',
    'serial_number' => 'Nomor Seri',
    'chassis_number' => 'Nomor Rangka',
    'engine_number' => 'Nomor Mesin',
    
    // Inspection
    'inspection_checklist' => 'Daftar Periksa Inspeksi',
    'inspection_report' => 'Laporan Inspeksi',
    'inspection_date' => 'Tanggal Inspeksi',
    'inspection_result' => 'Hasil Inspeksi',
    'pass' => 'Lulus',
    'fail' => 'Tidak Lulus',
    'needs_attention' => 'Perlu Perhatian',
    
    // Checklist Items
    'engine' => 'Mesin',
    'transmission' => 'Transmisi',
    'brakes' => 'Rem',
    'steering' => 'Kemudi',
    'electrical' => 'Elektrikal',
    'hydraulic' => 'Hidrolik',
    'tires' => 'Ban',
    'lights' => 'Lampu',
    'horn' => 'Klakson',
    'wipers' => 'Wiper',
    'battery' => 'Aki',
    'coolant' => 'Cairan Pendingin',
    'brake_fluid' => 'Minyak Rem',
    
    // Actions
    'start_service' => 'Mulai Layanan',
    'complete_service' => 'Selesaikan Layanan',
    'pause_service' => 'Jeda Layanan',
    'resume_service' => 'Lanjutkan Layanan',
    'cancel_service' => 'Batalkan Layanan',
    'approve_service' => 'Setujui Layanan',
    'reject_service' => 'Tolak Layanan',
    'print_report' => 'Cetak Laporan',
    'print_invoice' => 'Cetak Invoice',
    
    // Messages
    'service_created' => 'Layanan berhasil dibuat',
    'service_updated' => 'Layanan berhasil diperbarui',
    'service_deleted' => 'Layanan berhasil dihapus',
    'service_started' => 'Layanan dimulai',
    'service_completed' => 'Layanan selesai',
    'service_cancelled' => 'Layanan dibatalkan',
    'mechanic_assigned_success' => 'Mekanik berhasil ditugaskan',
    'parts_added' => 'Sparepart berhasil ditambahkan',
    
    // Validations
    'unit_required' => 'Unit wajib dipilih',
    'service_type_required' => 'Tipe service wajib dipilih',
    'mechanic_required' => 'Mekanik wajib dipilih',
    'description_required' => 'Deskripsi wajib diisi',
    'date_required' => 'Tanggal wajib diisi',
    
    // Reports
    'service_report' => 'Laporan Layanan',
    'maintenance_report' => 'Laporan Pemeliharaan',
    'repair_report' => 'Laporan Perbaikan',
    'cost_report' => 'Laporan Biaya',
    'mechanic_performance' => 'Performa Mekanik',
    
    // Work Order Form Labels
    'main_info_work_order' => 'Informasi Utama Work Order',
    'wo_number' => 'Nomor WO',
    'wo_number_auto' => 'Nomor WO akan terisi otomatis (+1 dari WO terakhir)',
    'order_type' => 'Tipe Order',
    'select_order_type' => 'Pilih Tipe Order',
    'complaint' => 'Komplain',
    'pmps' => 'PMPS',
    'fabrication' => 'Fabrikasi',
    'preparation' => 'Persiapan',
    'category' => 'Kategori',
    'sub_category' => 'Sub Kategori',
    'select_sub_category' => 'Pilih Sub Kategori (jika ada)',
    'sub_category_after_category' => 'Sub kategori akan muncul setelah memilih kategori',
    'priority' => 'Prioritas',
    'priority_auto' => 'Prioritas akan diatur otomatis berdasarkan kategori & sub kategori',
    'priority_based_category' => 'Prioritas akan diatur otomatis berdasarkan kategori & sub kategori',
    'area_auto' => 'Area akan diatur otomatis berdasarkan unit',
    'area_based_unit' => 'Area akan diatur otomatis berdasarkan unit yang dipilih',
    'enter_pic_name' => 'Masukkan nama PIC',
    'pic_example' => 'contoh: Adit (082136033596)',
    'complaint_description' => 'Deskripsi Komplain',
    'explain_complaint_detail' => 'Jelaskan komplain atau permintaan kerja secara detail...',
];
