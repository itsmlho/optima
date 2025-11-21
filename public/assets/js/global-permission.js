// GLOBAL PERMISSION SYSTEM
class GlobalPermission {
    constructor() {
        this.permissions = window.globalPermissions || {};
        this.init();
    }
    
    init() {
        this.disableTables();
        this.disableButtons();
        this.disableForms();
        this.preventClicks();
    }
    
    disableTables() {
        // Disable tables for View Only users
        if (!this.permissions.create) {
            $('.data-table').addClass('table-disabled');
            $('.table').addClass('table-disabled');
        }
    }
    
    disableButtons() {
        // Disable buttons based on permissions
        if (!this.permissions.create) {
            $('.btn-create, .btn-add, .btn-new').addClass('btn-disabled');
        }
        if (!this.permissions.edit) {
            $('.btn-edit, .btn-update').addClass('btn-disabled');
        }
        if (!this.permissions.delete) {
            $('.btn-delete, .btn-remove').addClass('btn-disabled');
        }
        if (!this.permissions.export) {
            $('.btn-export, .btn-download').addClass('btn-disabled');
        }
    }
    
    disableForms() {
        // Disable forms for View Only users
        if (!this.permissions.create && !this.permissions.edit) {
            $('form').addClass('form-disabled');
        }
    }
    
    preventClicks() {
        // Prevent clicks on disabled elements
        if (!this.permissions.create) {
            $('.table-disabled tbody tr').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                alert('Access Denied: You have View Only permission');
                return false;
            });
        }
    }
}

// Initialize when document is ready
$(document).ready(function() {
    new GlobalPermission();
});
