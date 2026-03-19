/**
 * Language Helper for OPTIMA
 * Provides multilingual support for JavaScript components
 * 
 * @package OPTIMA
 * @version 1.0.0
 */

const LanguageHelper = {
    /**
     * Current active language (id or en). Default English to match app default.
     */
    currentLang: 'en',

    /**
     * Initialize language helper
     */
    init: function() {
        // Prefer server-set value; then localStorage; then app default (en)
        const stored = localStorage.getItem('user_language');
        this.currentLang = stored || 'en';
    },

    /**
     * Set current language
     */
    setLanguage: function(lang) {
        this.currentLang = lang;
        localStorage.setItem('user_language', lang);
    },

    /**
     * Get translation for a key
     */
    trans: function(key) {
        const translations = this.getTranslations();
        return translations[key] || key;
    },

    /**
     * Get all translations for current language
     */
    getTranslations: function() {
        const id = {
            // Common
            'yes': 'Ya',
            'no': 'Tidak',
            'ok': 'OK',
            'cancel': 'Batal',
            'cancel_deal': 'Batal Deal',
            'close': 'Tutup',
            'back': 'Kembali',
            'save': 'Simpan',
            'save_changes': 'Simpan Perubahan',
            'work_order': 'Perintah Kerja',
            'new_work_order': 'Perintah Kerja Baru',
            'add': 'Tambah',
            'new': 'Baru',
            'start': 'Mulai',
            'delete': 'Hapus',
            'edit': 'Edit',
            'view': 'Lihat',
            'view_detail': 'Lihat Detail',
            'search': 'Cari',
            'loading': 'Memuat...',
            'processing': 'Memproses...',
            'please_wait': 'Mohon tunggu...',

            // Confirmations
            'confirm': 'Konfirmasi',
            'are_you_sure': 'Apakah Anda yakin?',
            'confirm_delete': 'Apakah Anda yakin ingin menghapus data ini?',
            'confirm_delete_multiple': 'Apakah Anda yakin ingin menghapus {count} data?',
            'confirm_cancel': 'Apakah Anda yakin ingin membatalkan?',
            'cannot_undo': 'Tindakan ini tidak dapat dibatalkan',
            'approve': 'Setuju',
            'send': 'Kirim',
            'just_now': 'Baru saja',
            'minutes_ago': '{count} menit lalu',
            'hours_ago': '{count} jam lalu',
            'days_ago': '{count} hari lalu',
            'continue': 'Lanjutkan',
            'later': 'Nanti',
            'confirm_delete_btn': 'Ya, Hapus',
            'confirm_continue_btn': 'Ya, Lanjutkan',

            // Success Messages
            'success': 'Berhasil',
            'success_save': 'Data berhasil disimpan',
            'success_update': 'Data berhasil diperbarui',
            'success_delete': 'Data berhasil dihapus',
            'operation_success': 'Operasi berhasil dilakukan',

            // Error Messages
            'error': 'Gagal',
            'error_occurred': 'Terjadi kesalahan',
            'error_save': 'Gagal menyimpan data',
            'error_update': 'Gagal memperbarui data',
            'error_delete': 'Gagal menghapus data',
            'error_not_found': 'Data tidak ditemukan',
            'error_connection': 'Gagal terhubung ke server',

            // DataTables
            'dt_processing': 'Memproses...',
            'dt_search': 'Cari:',
            'dt_lengthMenu': 'Tampilkan _MENU_ data',
            'dt_info': 'Menampilkan _START_ sampai _END_ dari _TOTAL_ data',
            'dt_infoEmpty': 'Menampilkan 0 sampai 0 dari 0 data',
            'dt_infoFiltered': '(disaring dari _MAX_ total data)',
            'dt_loadingRecords': 'Memuat...',
            'dt_zeroRecords': 'Tidak ada data yang sesuai',
            'dt_emptyTable': 'Tidak ada data',
            'dt_paginate_first': 'Pertama',
            'dt_paginate_previous': 'Sebelumnya',
            'dt_paginate_next': 'Selanjutnya',
            'dt_paginate_last': 'Terakhir',

            // Validation
            'required_field': 'Kolom wajib diisi',
            'invalid_email': 'Format email tidak valid',
            'invalid_phone': 'Format telepon tidak valid',
            'min_length': 'Minimal {count} karakter',
            'max_length': 'Maksimal {count} karakter',

            // Work Orders
            'select_unit': 'Pilih Unit',
            'select_admin': 'Pilih Admin',
            'select_foreman': 'Pilih Foreman',
            'select_mechanic_1': 'Pilih Mekanik 1',
            'select_mechanic_2_optional': 'Pilih Mekanik 2 (Opsional)',
            'select_helper_1': 'Pilih Helper 1',
            'select_helper_2_optional': 'Pilih Helper 2 (Opsional)',
            'add_item': 'Tambah Item',
            'not_completed': 'Belum selesai',
            'no_items_brought': 'Tidak ada item yang dibawa',
            'work_details_notes': 'Detail Kerja & Catatan',
            'additional_notes': 'Catatan Tambahan',
            'edit_work_order': 'Edit Perintah Kerja',
            'delete_work_order': 'Hapus Perintah Kerja',
            'select_pause_type': 'Pilih Tipe Jeda',
            'cancel_work_order': 'Batalkan Perintah Kerja',
            'provide_reason_cancellation': 'Berikan alasan pembatalan',
            'reassign_work_order': 'Tugaskan Ulang Perintah Kerja',
            'select_new_technician': 'Pilih teknisi baru',
            'yes_start': 'Ya, Mulai',
            'delete_confirmation': 'Konfirmasi Hapus',
            'complaint_required_min': 'Deskripsi Komplain wajib diisi minimal 3 karakter',
            'access_denied_view': 'Akses Ditolak: Anda tidak memiliki izin untuk melihat detail perintah kerja.',
            'select_unit_area': 'Pilih unit untuk melihat informasi area',
        };

        const en = {
            // Common
            'yes': 'Yes',
            'no': 'No',
            'ok': 'OK',
            'cancel': 'Cancel',
            'cancel_deal': 'Cancel Deal',
            'close': 'Close',
            'back': 'Back',
            'save': 'Save',
            'save_changes': 'Save Changes',
            'work_order': 'Work Order',
            'new_work_order': 'New Work Order',
            'add': 'Add',
            'new': 'New',
            'start': 'Start',
            'delete': 'Delete',
            'edit': 'Edit',
            'view': 'View',
            'view_detail': 'View Detail',
            'search': 'Search',
            'loading': 'Loading...',
            'processing': 'Processing...',
            'please_wait': 'Please wait...',

            // Confirmations
            'confirm': 'Confirmation',
            'are_you_sure': 'Are you sure?',
            'confirm_delete': 'Are you sure you want to delete this data?',
            'confirm_delete_multiple': 'Are you sure you want to delete {count} items?',
            'confirm_cancel': 'Are you sure you want to cancel?',
            'cannot_undo': 'This action cannot be undone',
            'approve': 'Approve',
            'send': 'Send',
            'just_now': 'Just now',
            'minutes_ago': '{count} minutes ago',
            'hours_ago': '{count} hours ago',
            'days_ago': '{count} days ago',
            'continue': 'Continue',
            'later': 'Later',
            'confirm_delete_btn': 'Yes, Delete',
            'confirm_continue_btn': 'Yes, Continue',

            // Success Messages
            'success': 'Success',
            'success_save': 'Data saved successfully',
            'success_update': 'Data updated successfully',
            'success_delete': 'Data deleted successfully',
            'operation_success': 'Operation completed successfully',

            // Error Messages
            'error': 'Error',
            'error_occurred': 'An error occurred',
            'error_save': 'Failed to save data',
            'error_update': 'Failed to update data',
            'error_delete': 'Failed to delete data',
            'error_not_found': 'Data not found',
            'error_connection': 'Failed to connect to server',

            // DataTables
            'dt_processing': 'Processing...',
            'dt_search': 'Search:',
            'dt_lengthMenu': 'Show _MENU_ entries',
            'dt_info': 'Showing _START_ to _END_ of _TOTAL_ entries',
            'dt_infoEmpty': 'Showing 0 to 0 of 0 entries',
            'dt_infoFiltered': '(filtered from _MAX_ total entries)',
            'dt_loadingRecords': 'Loading...',
            'dt_zeroRecords': 'No matching records found',
            'dt_emptyTable': 'No data available',
            'dt_paginate_first': 'First',
            'dt_paginate_previous': 'Previous',
            'dt_paginate_next': 'Next',
            'dt_paginate_last': 'Last',

            // Validation
            'required_field': 'This field is required',
            'invalid_email': 'Invalid email format',
            'invalid_phone': 'Invalid phone format',
            'min_length': 'Minimum {count} characters',
            'max_length': 'Maximum {count} characters',

            // Work Orders
            'select_unit': 'Select Unit',
            'select_admin': 'Select Admin',
            'select_foreman': 'Select Foreman',
            'select_mechanic_1': 'Select Mechanic 1',
            'select_mechanic_2_optional': 'Select Mechanic 2 (Optional)',
            'select_helper_1': 'Select Helper 1',
            'select_helper_2_optional': 'Select Helper 2 (Optional)',
            'add_item': 'Add Item',
            'not_completed': 'Not completed',
            'no_items_brought': 'No items brought',
            'work_details_notes': 'Work Details & Notes',
            'additional_notes': 'Additional Notes',
            'edit_work_order': 'Edit Work Order',
            'delete_work_order': 'Delete Work Order',
            'select_pause_type': 'Select Pause Type',
            'cancel_work_order': 'Cancel Work Order',
            'provide_reason_cancellation': 'Provide reason for cancellation',
            'reassign_work_order': 'Reassign Work Order',
            'select_new_technician': 'Select new technician',
            'yes_start': 'Yes, Start',
            'delete_confirmation': 'Delete Confirmation',
            'complaint_required_min': 'Complaint Description must be at least 3 characters',
            'access_denied_view': 'Access Denied: You do not have permission to view work order details.',
            'select_unit_area': 'Select a unit to see area information',
        };

        return this.currentLang === 'en' ? en : id;
    },

    /**
     * Get DataTables language configuration
     */
    getDataTablesLanguage: function() {
        return {
            "processing": this.trans('dt_processing'),
            "search": this.trans('dt_search'),
            "lengthMenu": this.trans('dt_lengthMenu'),
            "info": this.trans('dt_info'),
            "infoEmpty": this.trans('dt_infoEmpty'),
            "infoFiltered": this.trans('dt_infoFiltered'),
            "loadingRecords": this.trans('dt_loadingRecords'),
            "zeroRecords": this.trans('dt_zeroRecords'),
            "emptyTable": this.trans('dt_emptyTable'),
            "paginate": {
                "first": this.trans('dt_paginate_first'),
                "previous": this.trans('dt_paginate_previous'),
                "next": this.trans('dt_paginate_next'),
                "last": this.trans('dt_paginate_last')
            }
        };
    },

    /**
     * Show success toast/notification (Optima)
     */
    showSuccess: function(message) {
        if (window.OptimaNotify && typeof window.OptimaNotify.success === 'function') {
            window.OptimaNotify.success(message, this.trans('success'));
            return;
        }
        alert(message);
    },

    /**
     * Show error toast/notification (Optima)
     */
    showError: function(message) {
        if (window.OptimaNotify && typeof window.OptimaNotify.error === 'function') {
            window.OptimaNotify.error(message, this.trans('error'));
            return;
        }
        alert(message);
    },

    /**
     * Show confirmation dialog (OptimaConfirm)
     */
    confirmDelete: function(callback) {
        if (window.OptimaConfirm && typeof window.OptimaConfirm.danger === 'function') {
            window.OptimaConfirm.danger({
                title: this.trans('confirm'),
                messageHtml: this.trans('confirm_delete') + '<br><small class="text-muted">' + this.trans('cannot_undo') + '</small>',
                icon: 'warning',
                confirmText: this.trans('yes'),
                cancelText: this.trans('cancel'),
                confirmButtonColor: '#dc3545',
                onConfirm: function() {
                    if (typeof callback === 'function') callback();
                }
            });
        } else {
            if (confirm(this.trans('confirm_delete'))) {
                if (typeof callback === 'function') {
                    callback();
                }
            }
        }
    },

    /**
     * Show loading overlay
     */
    showLoading: function() {
        const id = 'languageHelperLoadingOverlay';
        let el = document.getElementById(id);
        if (!el) {
            el = document.createElement('div');
            el.id = id;
            el.className = 'loading-overlay';
            el.style.display = 'flex';
            el.style.zIndex = '1055';
            el.innerHTML = `
                <div class="loading-content">
                    <div class="loading-logo">
                        <div class="spinner-border text-primary" role="status" aria-hidden="true"></div>
                    </div>
                    <div class="loading-text">OPTIMA</div>
                    <div class="loading-subtitle">${this.trans('processing')}</div>
                </div>
            `;
            document.body.appendChild(el);
        } else {
            el.style.display = 'flex';
        }
    },

    /**
     * Hide loading overlay
     */
    hideLoading: function() {
        const id = 'languageHelperLoadingOverlay';
        const el = document.getElementById(id);
        if (el) el.style.display = 'none';
    }
};

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    LanguageHelper.init();
});

// Make it globally available
window.LanguageHelper = LanguageHelper;
window.lang = function(key) {
    return LanguageHelper.trans(key);
};
