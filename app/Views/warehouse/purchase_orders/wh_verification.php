<?= $this->extend('layouts/base') ?>

<?php 
// Load global permission helper
helper('global_permission');

// Get permissions for warehouse module
$permissions = get_global_permission('warehouse');
$can_view = $permissions['view'];
$can_create = $permissions['create'];
$can_edit = $permissions['edit'];
$can_delete = $permissions['delete'];
$can_export = $permissions['export'];
?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
<!-- CSS umum sudah ada di optima-pro.css -->
<style>
    /* Custom PO Verification page */
    .modal-header { background-color: #343a40; color: white; border-radius: 15px 15px 0 0; }
    
    /* PO List (Left Panel) */
    .po-group-header { cursor: pointer; background-color: #f8f9fa; border-bottom: 1px solid #e9ecef; padding: 0.75rem 1.25rem; transition: background-color 0.2s ease; }
    .po-group-header:hover { background-color: #e9ecef; }
    .po-group-header .arrow-icon { transition: transform 0.3s ease; }
    .po-group-header.open .arrow-icon { transform: rotate(180deg); }
    .item-child-item, .unit-child-item { 
        padding-left: 2.5rem; 
        border-left: 3px solid #dee2e6;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out, opacity 0.3s ease-out, padding 0.3s ease-out;
        opacity: 0;
        padding-top: 0;
        padding-bottom: 0;
        margin: 0;
    }
    .item-child-item.show, .unit-child-item.show {
        max-height: 500px;
        opacity: 1;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .item-child-item:hover, .unit-child-item:hover { border-left-color: #0d6efd; }
    .list-group-item.active { background-color: #e9ecef; border-color: #dee2e6; color: #212529; }
    .list-group-item.active .text-muted { color: #6c757d !important; }

    /* Verification Components - Compact & Neat */
    .verification-component { 
        border-bottom: 1px solid #f1f3f4; 
        padding: 12px 16px;
        transition: background-color 0.15s ease;
    }
    .verification-component:last-child { 
        border-bottom: none; 
    }
    .verification-component:hover {
        background-color: #fafbfc;
    }
    .component-row { 
        display: flex; 
        justify-content: space-between; 
        align-items: center;
        margin-bottom: 6px;
    }
    .component-row strong {
        font-size: 0.95rem;
        color: #1a1a1a;
        font-weight: 600;
    }
    .component-info { 
        font-size: 0.85rem; 
        color: #5f6368;
        margin: 6px 0;
        line-height: 1.4;
    }
    .note-input-group, .sn-input-group { 
        display: none; 
        margin-top: 10px;
        padding: 10px 12px;
        background-color: #f8f9fa;
        border-radius: 6px;
        border: 1px solid #e8eaed;
    }
    .sn-input-group {
        display: flex;
        gap: 10px;
        align-items: center;
    }
    .sn-input-group input { 
        font-size: 0.85rem; 
        padding: 8px 12px;
        border: 1px solid #dadce0;
        border-radius: 4px;
        flex: 1;
        background-color: #ffffff;
    }
    .sn-input-group input:focus {
        border-color: #1a73e8;
        outline: none;
        box-shadow: 0 0 0 2px rgba(26,115,232,0.1);
    }
    .btn-verify { 
        font-size: 0.85rem; 
        padding: 6px 12px;
        margin: 0 3px;
        border-radius: 4px;
        font-weight: 500;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .btn-verify:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .btn-verify.active {
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);
        transform: scale(1.02);
    }
    
    /* Modal Enhancements */
    .modal-content {
        border: none;
    }
    
    /* Status Indicators */
    .verification-component[data-status="sesuai"] {
        border-left: 3px solid #28a745;
        background-color: #f8fff9;
        border-radius: 0 6px 6px 0;
    }
    .verification-component[data-status="tidak-sesuai"] {
        border-left: 3px solid #dc3545;
        background-color: #fff8f8;
        border-radius: 0 6px 6px 0;
    }
    
    /* Smooth Animations */
    .note-input-group, .sn-input-group {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Button States */
    .btn-verify:not(.active) {
        opacity: 0.7;
    }
    .btn-verify:not(.active):hover {
        opacity: 1;
    }
    
    /* Tab Content */
    .tab-pane { min-height: 400px; }

    /* Tab Styling - Persis seperti inventory attachment */
    .nav-tabs {
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 0;
        background-color: transparent;
    }

    .nav-tabs .nav-item {
        margin-bottom: 0;
    }

    .nav-tabs .nav-link {
        padding: 1.25rem 2.5rem;
        border: 1px solid transparent;
        border-top-left-radius: 0.375rem;
        border-top-right-radius: 0.375rem;
        color: #6c757d;
        transition: all 0.15s ease-in-out;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: transparent;
        font-weight: 500;
        font-size: 1.1rem;
        min-height: 70px;
    }

    .nav-tabs .nav-link:hover {
        border-color: #e9ecef #e9ecef #dee2e6;
        isolation: isolate;
        color: #4e73df;
        background-color: #f8f9fc;
    }

    .nav-tabs .nav-link.active {
        color: white !important;
        background-color: #4e73df !important;
        border-color: #4e73df !important;
        box-shadow: 0 2px 4px rgba(78, 115, 223, 0.2);
        font-weight: 600;
    }

    .nav-tabs .nav-link i {
        font-size: 1.4rem;
        width: 24px;
        text-align: center;
    }

    /* Tab content area */
    .tab-content {
        background-color: white;
        border-radius: 0 0 0.375rem 0.375rem;
        min-height: 500px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .nav-tabs .nav-link {
            padding: 1rem 2rem;
            font-size: 1rem;
            min-height: 60px;
        }
        
        .nav-tabs .nav-link i {
            font-size: 1.2rem;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Main Card with Tabs -->
<div class="card table-card">
    <!-- Tab Navigation -->
    <div class="card-body p-0">
        <ul class="nav nav-tabs nav-fill mb-0" id="whVerificationTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="unit-tab" data-bs-toggle="tab" data-bs-target="#unit-verification" type="button" role="tab">
                    <i class="fas fa-truck me-1"></i>
                    <span>Unit</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="attachment-tab" data-bs-toggle="tab" data-bs-target="#attachment-verification" type="button" role="tab">
                    <i class="fas fa-puzzle-piece me-1"></i>
                    <span>Attachment</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sparepart-tab" data-bs-toggle="tab" data-bs-target="#sparepart-verification" type="button" role="tab">
                    <i class="fas fa-cogs me-1"></i>
                    <span>Sparepart</span>
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- ========== UNIT VERIFICATION TAB ========== -->
        <div class="tab-pane fade show active" id="unit-verification" role="tabpanel">
            <?php echo view('warehouse/purchase_orders/tabs/unit_verification_tab', ['detailGroup' => $detailGroupUnit ?? []]); ?>
        </div>

        <!-- ========== ATTACHMENT VERIFICATION TAB ========== -->
        <div class="tab-pane fade" id="attachment-verification" role="tabpanel">
            <?php echo view('warehouse/purchase_orders/tabs/attachment_verification_tab', ['detailGroup' => $detailGroupAttachment ?? []]); ?>
        </div>

        <!-- ========== SPAREPART VERIFICATION TAB ========== -->
        <div class="tab-pane fade" id="sparepart-verification" role="tabpanel">
            <?php echo view('warehouse/purchase_orders/tabs/sparepart_verification_tab', ['detailGroup' => $detailGroupSparepart ?? []]); ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// Define base URL for AJAX requests
const baseUrl = '<?= base_url() ?>';

// ========================================
// TAB SWITCHING LOGIC - FIX STACKING ISSUE
// ========================================
$(document).ready(function() {
    // Initialize tab switching
    $('#whVerificationTabs button[data-bs-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        const target = $(this).data('bs-target');
        
        // Remove active class from all tabs and panes
        $('#whVerificationTabs .nav-link').removeClass('active');
        $('.tab-pane').removeClass('show active');
        
        // Add active class to clicked tab and target pane
        $(this).addClass('active');
        $(target).addClass('show active');
        
        // Force refresh to prevent stacking
        setTimeout(function() {
            $(target).trigger('shown.bs.tab');
        }, 100);
    });
    
    // Ensure first tab is properly initialized
    $('#unit-tab').trigger('click');
});
</script>

<!-- Include scripts from each tab -->
<?php echo view('warehouse/purchase_orders/tabs/unit_verification_script'); ?>
<?php echo view('warehouse/purchase_orders/tabs/attachment_verification_script'); ?>
<?php echo view('warehouse/purchase_orders/tabs/sparepart_verification_script'); ?>

<?= $this->endSection() ?>

