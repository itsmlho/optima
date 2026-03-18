<!-- Complete Work Order Modal -->
<div class="modal fade" id="completeWorkOrderModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-success text-muted">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i>Complete Work Order - <span id="complete-wo-number">Loading...</span>
                </h5>
                <button type="button" class="btn-close btn-close-muted" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">
                        <i class="fas fa-info-circle me-2"></i>Instruksi Penyelesaian Work Order
                    </h6>
                    <p class="mb-2">Silakan lengkapi informasi berikut sebelum melanjutkan ke verifikasi unit:</p>
                    <ul class="mb-0 small">
                        <li><strong>Analysis & Repair:</strong> Jelaskan analisa masalah dan perbaikan yang telah dilakukan</li>
                        <li><strong>Additional Notes:</strong> Catatan tambahan atau informasi penting lainnya (optional)</li>
                    </ul>
                    <small class="text-danger fw-bold mt-2 d-block">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Setelah save, modal verifikasi unit akan terbuka otomatis dan WAJIB diselesaikan
                    </small>
                </div>
                
                <form id="completeWorkOrderForm">
                    <input type="hidden" id="complete-work-order-id" name="work_order_id">
                    
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">
                                <i class="fas fa-tools me-2"></i>Repair Information
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="repair_description_complete" class="form-label">
                                    Analysis & Repair <span class="text-danger">*</span>
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="repair_description_complete" 
                                    name="repair_description" 
                                    rows="5" 
                                    placeholder="Jelaskan analisa masalah dan perbaikan yang telah dilakukan...
Contoh:
- Masalah: Fork tidak bisa naik
- Analisa: Hydraulic pump rusak
- Perbaikan: Ganti hydraulic pump dengan yang baru"
                                    required></textarea>
                                <div class="invalid-feedback">
                                    Analysis & Repair wajib diisi
                                </div>
                                <small class="form-text text-muted">
                                    <i class="fas fa-lightbulb me-1"></i>
                                    Jelaskan secara detail analisa masalah dan langkah perbaikan yang dilakukan
                                </small>
                            </div>
                            
                            <div class="mb-0">
                                <label for="notes_complete" class="form-label">
                                    Additional Notes
                                </label>
                                <textarea 
                                    class="form-control" 
                                    id="notes_complete" 
                                    name="notes" 
                                    rows="3" 
                                    placeholder="Catatan tambahan, rekomendasi, atau informasi penting lainnya (optional)..."></textarea>
                                <small class="form-text text-muted">
                                    <i class="fas fa-sticky-note me-1"></i>
                                    Tambahkan catatan penting jika diperlukan (optional)
                                </small>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-success" id="btn-save-complete">
                    <i class="fas fa-check me-1"></i>Save & Verify Unit
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Global variables to store work order info (outside document.ready)
let currentCompleteWoId = null;
let currentCompleteWoNumber = null;

$(document).ready(function() {
    // Function to open Complete Modal and load data
    window.openCompleteModal = function(workOrderId, woNumber) {
        console.log('🔵 Opening Complete Modal - WO ID:', workOrderId, 'WO Number:', woNumber);
        
        // Store current WO info
        currentCompleteWoId = workOrderId;
        currentCompleteWoNumber = woNumber;
        
        // Reset form
        $('#completeWorkOrderForm')[0].reset();
        $('#completeWorkOrderForm').removeClass('was-validated');
        $('#complete-work-order-id').val(workOrderId);
        $('#complete-wo-number').text(woNumber);
        
        // Load existing data if any
        $.ajax({
            url: '<?= base_url('service/work-orders/get-complete-data') ?>',
            type: 'POST',
            data: { 
                work_order_id: workOrderId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            beforeSend: function() {
                $('#btn-save-complete').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Loading...');
            },
            success: function(response) {
                console.log('📥 Complete data loaded:', response);
                
                if (response.success && response.data) {
                    // Pre-fill form with existing data
                    if (response.data.repair_description) {
                        $('#repair_description_complete').val(response.data.repair_description);
                        console.log('✅ Pre-filled repair_description');
                    }
                    if (response.data.notes) {
                        $('#notes_complete').val(response.data.notes);
                        console.log('✅ Pre-filled notes');
                    }
                }
                
                // Show modal
                $('#completeWorkOrderModal').modal('show');
            },
            error: function(xhr, status, error) {
                console.error('❌ Error loading complete data:', error);
                // Still show modal even if load fails
                $('#completeWorkOrderModal').modal('show');
            },
            complete: function() {
                $('#btn-save-complete').prop('disabled', false).html('<i class="fas fa-check me-1"></i>Save & Verify Unit');
            }
        });
    };
    
    // Save Complete button
    $('#btn-save-complete').on('click', function(e) {
        e.preventDefault();
        saveCompleteWorkOrder();
    });
    
    // Submit form on Enter (Ctrl+Enter in textarea)
    $('#completeWorkOrderForm').on('submit', function(e) {
        e.preventDefault();
        saveCompleteWorkOrder();
        return false;
    });
    
    // Function to save Complete data
    function saveCompleteWorkOrder() {
        let form = $('#completeWorkOrderForm')[0];
        
        // Validate form
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            OptimaNotify.warning(
                'Please fill in all required fields (Analysis & Repair)',
                'Validation Error'
            );
            return;
        }
        
        let formData = $('#completeWorkOrderForm').serialize();
        console.log('💾 Saving complete data:', formData);
        
        // Add CSRF token to form data
        formData += '&<?= csrf_token() ?>=<?= csrf_hash() ?>';
        
        // Disable button
        $('#btn-save-complete').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Saving...');
        
        $.ajax({
            url: '<?= base_url('service/work-orders/save-complete') ?>',
            type: 'POST',
            data: formData,
            success: function(response) {
                console.log('✅ Complete data saved:', response);
                
                if (response.success) {
                    // Show success message
                    OptimaNotify.success(
                        'Analysis & Repair data saved. Opening Unit Verification...',
                        'Data Saved'
                    );
                    
                    // Close Complete modal
                    $('#completeWorkOrderModal').modal('hide');
                    
                    // Store values before modal closes (to prevent reset on hidden.bs.modal)
                    const savedWoId = currentCompleteWoId;
                    const savedWoNumber = currentCompleteWoNumber;
                    
                    // Wait for modal close animation, then open Verification modal
                    setTimeout(function() {
                        console.log('🔵 Auto-opening Unit Verification Modal');
                        console.log('🔍 Passing WO ID:', savedWoId, 'WO Number:', savedWoNumber);
                        
                        $('#unitVerificationModal').modal('show');
                        
                        // Load unit verification data - use window.loadUnitVerificationData
                        if (typeof window.loadUnitVerificationData === 'function') {
                            window.loadUnitVerificationData(savedWoId, savedWoNumber);
                        } else {
                            console.error('❌ window.loadUnitVerificationData function not found');
                        }
                    }, 500);
                } else {
                    OptimaNotify.error(
                        response.message || 'Failed to save data',
                        'Error'
                    );
                    
                    // Re-enable button
                    $('#btn-save-complete').prop('disabled', false).html('<i class="fas fa-check me-1"></i>Save & Verify Unit');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error saving complete data:', error);
                
                OptimaNotify.error(
                    'Failed to save data. Please try again.',
                    'Error'
                );
                
                // Re-enable button
                $('#btn-save-complete').prop('disabled', false).html('<i class="fas fa-check me-1"></i>Save & Verify Unit');
            }
        });
    }
    
    // Reset form when modal is hidden
    $('#completeWorkOrderModal').on('hidden.bs.modal', function() {
        $('#completeWorkOrderForm')[0].reset();
        $('#completeWorkOrderForm').removeClass('was-validated');
        currentCompleteWoId = null;
        currentCompleteWoNumber = null;
    });
});
</script>
