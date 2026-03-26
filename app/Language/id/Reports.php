<?php

return [
    'access_denied' => 'Anda tidak memiliki akses ke pusat Laporan (Reports).',
    'no_segments_for_role' => 'Tidak ada template laporan yang tersedia untuk izin peran Anda saat ini.',

    'breadcrumb_dashboard' => 'Dashboard',
    'breadcrumb_reports' => 'Laporan',

    'page_title_hub' => 'Pusat laporan',
    'hub_intro_title' => 'Laporan standar vs. export di setiap modul',
    'hub_intro_body' => 'Gunakan halaman ini untuk laporan bernama berdasarkan periode (Excel / PDF / CSV) yang tersimpan di riwayat. Untuk export data persis seperti grid dan filter di layar, gunakan tombol Export di modul masing-masing.',

    'rental_page_title' => 'Laporan rental',
    'maintenance_page_title' => 'Laporan maintenance',
    'financial_page_title' => 'Laporan keuangan',
    'inventory_page_title' => 'Laporan inventori & suku cadang',
    'custom_page_title' => 'Laporan kustom',

    'stats_total' => 'Total dibuat',
    'stats_completed' => 'Selesai',
    'stats_pending' => 'Menunggu',
    'stats_this_month' => 'Bulan ini',

    'section_named_reports' => 'Jenis laporan di kategori ini',
    'btn_generate_excel' => 'Buat (Excel)',
    'btn_all_reports' => 'Semua jenis laporan',
    'btn_finance_module' => 'Buka modul laporan Finance',

    'custom_intro' => 'Pilih jenis laporan, rentang tanggal, dan format di pusat Laporan utama. Nama dan parameter disimpan bersama file yang dihasilkan.',
    'custom_cta' => 'Ke pusat Laporan',

    'generate_success' => 'Laporan berhasil dibuat.',
    'generate_failed' => 'Gagal membuat laporan',
    'error_type_required' => 'Jenis laporan wajib diisi.',
    'select_report_type_placeholder' => 'Pilih jenis laporan',
    'error_method_not_allowed' => 'Metode tidak diizinkan.',
    'schedule_not_available' => 'Jadwal laporan belum diaktifkan. Gunakan Generate untuk unduhan sesuai permintaan.',

    'catalog_rental_monthly_title' => 'Rental / kontrak overlap bulanan',
    'catalog_rental_monthly_desc' => 'Kontrak yang overlap periode terpilih (dari tabel kontrak).',
    'catalog_contract_perf_title' => 'Kinerja kontrak',
    'catalog_contract_perf_desc' => 'Set kontrak yang sama dengan konteks durasi untuk review.',
    'catalog_unit_util_title' => 'Utilisasi unit per status',
    'catalog_unit_util_desc' => 'Jumlah armada per status_unit_id.',

    'catalog_revenue_title' => 'Pendapatan (invoice)',
    'catalog_revenue_desc' => 'Invoice pada rentang tanggal menurut issue_date.',
    'catalog_expenses_title' => 'Biaya (placeholder)',
    'catalog_expenses_desc' => 'Untuk pembelian / AP setelah sumber data disatukan.',
    'catalog_pl_title' => 'Laba rugi (sederhana)',
    'catalog_pl_desc' => 'Pendapatan dari invoice vs. biaya placeholder.',

    'catalog_maint_sched_title' => 'Jadwal maintenance',
    'catalog_maint_sched_desc' => 'Work order dibuat dalam periode dengan requested repair time.',
    'catalog_wo_title' => 'Register work order',
    'catalog_wo_desc' => 'Semua work order yang dibuat pada periode terpilih.',
    'catalog_downtime_title' => 'Waktu siklus (proxy)',
    'catalog_downtime_desc' => 'Hari dari created_at WO sampai completion_date jika keduanya ada.',

    'catalog_stock_title' => 'Stok suku cadang',
    'catalog_stock_desc' => 'Stok saat ini dari inventory_spareparts dengan kode master.',
    'catalog_spare_usage_title' => 'Pemakaian suku cadang (WO)',
    'catalog_spare_usage_desc' => 'Baris dari work_order_sparepart_usage pada periode.',
    'catalog_asset_title' => 'Daftar armada & tarif',
    'catalog_asset_desc' => 'Unit dengan tarif bulanan/harian (bukan penyusutan buku).',

    'summary_snapshot' => 'Ringkasan',
    'recent_activity' => 'Aktivitas terkini',

    'hub_nav_all' => 'Semua bagian',
    'hub_nav_rental' => 'Hub rental',
    'hub_nav_maintenance' => 'Hub maintenance',
    'hub_nav_financial' => 'Hub keuangan',
    'hub_nav_inventory' => 'Hub inventori',
    'hub_nav_custom' => 'Kustom',

    'summary_matches_export' => 'Angka di bawah memakai data yang sama dengan file export untuk periode yang ditampilkan.',
    'rows_in_export' => 'Baris di export',
];
