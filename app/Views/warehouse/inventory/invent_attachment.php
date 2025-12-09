<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

    <!-- Inventory Table -->
    <div class="card table-card">
        <div class="card-header">
            <div class="row align-items-center mb-3">
                <div class="col">
                    <h5 class="card-title fw-bold m-0">Daftar Stok Attachment</h5>
                </div>
                <div class="col-auto">
                    <button type="button" class="btn btn-primary" id="btnTambahItem">
                        <i class="fas fa-plus me-1"></i>Tambah Item
                    </button>
                </div>
            </div>
            
            <!-- Main Type Tabs -->
            <ul class="nav nav-tabs mb-3" id="itemTypeTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="attachment-tab" data-bs-toggle="tab" data-bs-target="#attachment" type="button" role="tab" onclick="applyTypeFilter('attachment')">
                        <i class="fas fa-puzzle-piece me-1"></i>
                        <strong>Attachment</strong>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="battery-tab" data-bs-toggle="tab" data-bs-target="#battery" type="button" role="tab" onclick="applyTypeFilter('battery')">
                        <i class="fas fa-battery-half me-1"></i>
                        <strong>Battery</strong>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="charger-tab" data-bs-toggle="tab" data-bs-target="#charger" type="button" role="tab" onclick="applyTypeFilter('charger')">
                        <i class="fas fa-plug me-1"></i>
                        <strong>Charger</strong>
                    </button>
                </li>
            </ul>
            
            <!-- Status Sub-Tabs -->
            <ul class="nav nav-pills gap-2 mb-0" id="statusFilterTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active btn-sm" id="all-status-tab" type="button" onclick="applyStatusFilter('all')">
                        <i class="fas fa-list me-1"></i>
                        All
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="available-status-tab" type="button" onclick="applyStatusFilter('AVAILABLE')">
                        <i class="fas fa-check-circle me-1"></i>
                        Available
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="inuse-status-tab" type="button" onclick="applyStatusFilter('IN_USE')">
                        <i class="fas fa-user me-1"></i>
                        In Use
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="maintenance-status-tab" type="button" onclick="applyStatusFilter('MAINTENANCE')">
                        <i class="fas fa-tools me-1"></i>
                        Maintenance
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link btn-sm" id="broken-status-tab" type="button" onclick="applyStatusFilter('BROKEN')">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Broken
                    </button>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <table id="inventory-attachment-table" class="table table-striped table-hover">
                <thead id="table-header">
                    <!-- Dynamic header will be inserted here -->
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal View Attachment Detail -->
<div class="modal fade" id="viewAttachmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-eye me-2"></i>Detail Attachment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="attachmentDetailContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <div class="me-auto">
                    <!-- Action Buttons (Left side) -->
                    <button type="button" class="btn btn-success btn-sm" id="btnAttachToUnit" onclick="openAttachModal()" style="display:none;">
                        <i class="fas fa-link me-1"></i>Pasang ke Unit
                    </button>
                    <button type="button" class="btn btn-info btn-sm" id="btnSwapUnit" onclick="openSwapModal()" style="display:none;">
                        <i class="fas fa-exchange-alt me-1"></i>Pindah Unit
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" id="btnDetachFromUnit" onclick="openDetachModal()" style="display:none;">
                        <i class="fas fa-unlink me-1"></i>Lepas dari Unit
                    </button>
                </div>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" class="btn btn-warning" id="btnEditAttachment" onclick="editCurrentAttachment()">
                    <i class="fas fa-edit me-1"></i>Edit
                </button>
                <button type="button" class="btn btn-danger" id="btnDeleteAttachment" onclick="deleteCurrentAttachment()">
                    <i class="fas fa-trash me-1"></i>Delete
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit Stok Attachment -->
<div class="modal fade" id="editAttachmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Stok Attachment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editAttachmentForm">
                <div class="modal-body">
                    <input type="hidden" id="edit_id" name="id_inventory_attachment">
                    <div class="mb-3">
                        <label class="form-label">SN Attachment</label>
                        <input type="text" class="form-control" id="edit_sn_attachment" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">SN Charger</label>
                        <input type="text" class="form-control" id="edit_sn_charger" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_status_unit" class="form-label">Status</label>
                        <select class="form-select" id="edit_status_unit" name="status_unit" required>
                            <option value="7">STOCK ASET</option>
                            <option value="3">RENTAL</option>
                            <option value="9">JUAL</option>
                            <option value="2">WORKSHOP-RUSAK</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_lokasi" class="form-label">Lokasi Penyimpanan</label>
                        <select class="form-select" id="edit_lokasi" name="lokasi_penyimpanan">
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kondisi" class="form-label">Kondisi Fisik</label>
                        <select class="form-select" id="edit_kondisi" name="kondisi_fisik">
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_kelengkapan" class="form-label">Kelengkapan</label>
                        <select class="form-select" id="edit_kelengkapan" name="kelengkapan">
                            <option value="Lengkap">Lengkap</option>
                            <option value="Tidak Lengkap">Tidak Lengkap</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Attach to Unit -->
<div class="modal fade" id="attachToUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-link me-2"></i>Pasang ke Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="attachToUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="attach_attachment_id">
                    <input type="hidden" id="attach_attachment_type">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Pilih unit untuk memasang <span id="attach_item_label"></span>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Unit <span class="text-danger">*</span></label>
                        <select class="form-select select2-search" id="attach_unit_id" required>
                            <option value="">Pilih Unit...</option>
                        </select>
                        <small class="text-muted">Cari dan pilih unit (ketik no unit atau model untuk search)</small>
                    </div>
                    
                    <div id="attach_existing_warning" class="alert alert-warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Unit ini sudah memiliki <span id="attach_existing_type"></span>. 
                        Item existing akan otomatis dilepas dan dikembalikan ke Workshop.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan (Opsional)</label>
                        <textarea class="form-control" id="attach_notes" rows="2" placeholder="Misal: Unit baru, replacement, dll"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-link me-1"></i>Pasang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Swap Unit -->
<div class="modal fade" id="swapUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-exchange-alt me-2"></i>Pindah Unit</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="swapUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="swap_attachment_id">
                    <input type="hidden" id="swap_from_unit_id">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="swap_item_label"></span> akan dipindah dari <strong id="swap_from_unit_label"></strong> ke unit lain
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Pindah ke Unit <span class="text-danger">*</span></label>
                        <select class="form-select select2-search" id="swap_to_unit_id" required>
                            <option value="">Pilih Unit Tujuan...</option>
                        </select>
                        <small class="text-muted">Cari dan pilih unit tujuan (ketik no unit atau model untuk search)</small>
                    </div>
                    
                    <div id="swap_existing_warning" class="alert alert-warning" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Perhatian:</strong> Unit tujuan sudah memiliki <span id="swap_existing_type"></span>. 
                        Item existing akan otomatis dilepas dan dikembalikan ke Workshop.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan Pindah <span class="text-danger">*</span></label>
                        <select class="form-select" id="swap_reason_select" onchange="toggleSwapReasonInput(this.value)">
                            <option value="">Pilih Alasan...</option>
                            <option value="Emergency - attachment patah">Emergency - Attachment Patah</option>
                            <option value="Swap untuk backup">Swap untuk Backup</option>
                            <option value="Unit maintenance">Unit Maintenance</option>
                            <option value="Upgrade attachment">Upgrade Attachment</option>
                            <option value="custom">Alasan Lainnya...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="swap_custom_reason_group" style="display:none;">
                        <label class="form-label">Alasan Custom</label>
                        <textarea class="form-control" id="swap_custom_reason" rows="2" placeholder="Jelaskan alasan pindah unit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-exchange-alt me-1"></i>Pindah Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Detach from Unit -->
<div class="modal fade" id="detachFromUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-unlink me-2"></i>Lepas dari Unit</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="detachFromUnitForm">
                <div class="modal-body">
                    <input type="hidden" id="detach_attachment_id">
                    <input type="hidden" id="detach_from_unit_id">
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <span id="detach_item_label"></span> akan dilepas dari <strong id="detach_from_unit_label"></strong>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Alasan Lepas <span class="text-danger">*</span></label>
                        <select class="form-select" id="detach_reason_select" onchange="toggleDetachReasonInput(this.value)" required>
                            <option value="">Pilih Alasan...</option>
                            <option value="Rusak - perlu repair">Rusak - Perlu Repair</option>
                            <option value="Maintenance rutin">Maintenance Rutin</option>
                            <option value="Lepas untuk backup">Lepas untuk Backup</option>
                            <option value="Unit pulang rental">Unit Pulang Rental</option>
                            <option value="Upgrade attachment">Upgrade Attachment</option>
                            <option value="custom">Alasan Lainnya...</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="detach_custom_reason_group" style="display:none;">
                        <label class="form-label">Alasan Custom</label>
                        <textarea class="form-control" id="detach_custom_reason" rows="2" placeholder="Jelaskan alasan melepas..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lokasi Penyimpanan Setelah Dilepas</label>
                        <select class="form-select" id="detach_new_location">
                            <option value="Workshop">Workshop</option>
                            <option value="POS 1">POS 1</option>
                            <option value="POS 2">POS 2</option>
                            <option value="POS 3">POS 3</option>
                            <option value="POS 4">POS 4</option>
                            <option value="POS 5">POS 5</option>
                        </select>
                        <small class="text-muted">Item akan disimpan di lokasi ini setelah dilepas</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-unlink me-1"></i>Lepas dari Unit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Tambah Item Baru
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h6 class="alert-heading mb-2">📋 Tambah Data Item Inventory</h6>
                    <p class="mb-0">Isi data item yang akurat sesuai kondisi di lapangan. Data ini akan disimpan ke inventory dengan status AVAILABLE.</p>
                </div>
                
                <form id="addItemForm">
                    <input type="hidden" id="new-tipe-item" name="tipe_item" value="">
                    
                    <!-- Attachment Fields -->
                    <div id="attachment-fields">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Attachment <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-attachment-id" name="attachment_id">
                                        <option value="">Pilih Jenis Attachment</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-sn-attachment" name="sn_attachment" placeholder="Masukkan SN attachment">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Battery Fields -->
                    <div id="battery-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Baterai <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-baterai-id" name="baterai_id">
                                        <option value="">Pilih Jenis Baterai</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-sn-baterai" name="sn_baterai" placeholder="Masukkan SN baterai">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Charger Fields -->
                    <div id="charger-fields" style="display: none;">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Jenis Charger <span class="text-danger">*</span></label>
                                    <select class="form-select" id="new-charger-id" name="charger_id">
                                        <option value="">Pilih Jenis Charger</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Serial Number <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new-sn-charger" name="sn_charger" placeholder="Masukkan SN charger">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Common Fields -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Unit</label>
                                <select class="form-select" id="new-unit-id" name="unit_id">
                                    <option value="">Pilih Unit (Opsional)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Kondisi Fisik</label>
                                <select class="form-select" id="new-kondisi-fisik" name="kondisi_fisik">
                                    <option value="Baik">Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Lokasi Penyimpanan <span class="text-danger">*</span></label>
                                <select class="form-select" id="new-lokasi" name="lokasi_penyimpanan" required>
                                    <option value="">Pilih Lokasi</option>
                                    <optgroup label="Workshop">
                                        <option value="Workshop" selected>Workshop</option>
                                        <option value="POS 1">POS 1</option>
                                        <option value="POS 2">POS 2</option>
                                        <option value="POS 3">POS 3</option>
                                        <option value="POS 4">POS 4</option>
                                        <option value="POS 5">POS 5</option>
                                    </optgroup>
                                    <optgroup label="Lainnya">
                                        <option value="WAREHOUSE">WAREHOUSE</option>
                                    </optgroup>
                                </select>
                                <small class="text-muted">Lokasi item saat TIDAK terpasang di unit. Jika terpasang, lokasi otomatis "Terpasang di Unit X"</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <select class="form-select" id="new-attachment-status" name="attachment_status">
                                    <option value="AVAILABLE">Available</option>
                                    <option value="IN_USE">In Use</option>
                                    <option value="MAINTENANCE">Maintenance</option>
                                    <option value="BROKEN">Broken</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" id="new-catatan" name="catatan" rows="3" placeholder="Catatan tambahan..."></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-success" id="btn-save-item">
                    <i class="fas fa-save me-1"></i>Simpan Item
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js?v=<?= time() ?>"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js?v=<?= time() ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11?v=<?= time() ?>"></script>
<script>
    // Inventory Attachment & Battery Management - Updated <?= date('Y-m-d H:i:s') ?>
    let currentTypeFilter = '';
    let currentStatusFilter = 'all';
    let currentAttachmentId = null;
    let attachmentTable;

    $(document).ready(function() {
        console.log('🔧 Inventory Attachment JavaScript loaded');

        // Initialize DataTable and other code
        setupDataTable();
        
        // Handle add item button click
        $('#btnTambahItem').on('click', function() {
            const type = currentTypeFilter || 'attachment'; // Use current filter
            console.log('🆕 Add item button clicked for type:', type);
            openAddItemModal(type);
        });

        // Remove duplicate tab handlers since applyTypeFilter handles this
    });

    function createDynamicHeader(type) {
        let headerHtml = '<tr>';
        
        // Common columns for all types
        headerHtml += '<th>ID</th>';
        headerHtml += '<th>Tipe Item</th>';
        
        // Type-specific columns
        if (type === 'attachment') {
            headerHtml += '<th>Merk</th>';
            headerHtml += '<th>Tipe</th>';
            headerHtml += '<th>Model</th>';
        } else if (type === 'battery') {
            headerHtml += '<th>Jenis</th>';
            headerHtml += '<th>Merk</th>';
            headerHtml += '<th>Tipe</th>';
        } else if (type === 'charger') {
            headerHtml += '<th>Merk</th>';
            headerHtml += '<th>Tipe</th>';
        }
        
        // Common columns continued
        headerHtml += '<th>SN</th>';
        headerHtml += '<th>Kondisi Fisik</th>';
        headerHtml += '<th>Status</th>';
        headerHtml += '<th>Lokasi</th>';
        
        headerHtml += '</tr>';
        
        $('#table-header').html(headerHtml);
    }

    function getDynamicColumns(type) {
        let columns = [
            // ID column (always first)
            { 
                data: 'id_inventory_attachment',
                render: function(data, type, row) {
                    return data || '-';
                }
            },
            // Tipe Item column (always second)
            { 
                data: 'tipe_item',
                render: function(data, type, row) {
                    const typeMap = {
                        'attachment': '<i class="fas fa-puzzle-piece me-1 text-primary"></i>Attachment',
                        'battery': '<i class="fas fa-battery-half me-1 text-success"></i>Battery',
                        'charger': '<i class="fas fa-plug me-1 text-warning"></i>Charger'
                    };
                    
                    if (data && typeMap[data]) {
                        return typeMap[data];
                    }
                    
                    // Fallback logic based on available serial numbers
                    if (row.sn_attachment) {
                        return '<i class="fas fa-puzzle-piece me-1 text-primary"></i>Attachment';
                    } else if (row.sn_baterai) {
                        return '<i class="fas fa-battery-half me-1 text-success"></i>Battery';
                    } else if (row.sn_charger) {
                        return '<i class="fas fa-plug me-1 text-warning"></i>Charger';
                    }
                    
                    return '<i class="fas fa-question me-1 text-muted"></i>Unknown';
                }
            }
        ];
        
        // Add type-specific columns
        if (type === 'attachment') {
            columns.push(
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.attachment_merk || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.attachment_tipe || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.attachment_model || '-';
                    }
                }
            );
        } else if (type === 'battery') {
            columns.push(
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.jenis_baterai || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.merk_baterai || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.tipe_baterai || '-';
                    }
                }
            );
        } else if (type === 'charger') {
            columns.push(
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.merk_charger || '-';
                    }
                },
                { 
                    data: null,
                    render: function(data, type, row) {
                        return row.tipe_charger || '-';
                    }
                }
            );
        }
        
        // Add common columns
        columns.push(
            // SN Column
            { 
                data: null,
                render: function(data, type, row) {
                    // Show the appropriate SN based on tipe_item
                    switch(row.tipe_item) {
                        case 'attachment':
                            return row.sn_attachment || '-';
                        case 'battery':
                            return row.sn_baterai || '-';
                        case 'charger':
                            return row.sn_charger || '-';
                        default:
                            // Fallback: show any available SN
                            return row.sn_attachment || row.sn_baterai || row.sn_charger || '-';
                    }
                }
            },
            // Kondisi Fisik
            { 
                data: 'kondisi_fisik',
                render: function(data, type, row) {
                    if (!data) return '-';
                    
                    const kondisiMap = {
                        'Baik': '<span class="badge bg-success">Baik</span>',
                        'Rusak Ringan': '<span class="badge bg-warning">Rusak Ringan</span>',
                        'Rusak Berat': '<span class="badge bg-danger">Rusak Berat</span>'
                    };
                    
                    return kondisiMap[data] || `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            // Status
            { 
                data: 'attachment_status',
                render: function(data, type, row) {
                    if (!data) return '-';
                    
                    const statusMap = {
                        'AVAILABLE': '<span class="badge bg-success">Available</span>',
                        'IN_USE': '<span class="badge bg-primary">In Use</span>',
                        'MAINTENANCE': '<span class="badge bg-warning">Maintenance</span>',
                        'BROKEN': '<span class="badge bg-danger">Broken</span>'
                    };
                    
                    return statusMap[data] || `<span class="badge bg-secondary">${data}</span>`;
                }
            },
            // Lokasi
            { 
                data: 'lokasi_penyimpanan',
                render: function(data, type, row) {
                    return data || '-';
                }
            }
        );
        
        return columns;
    }

    function setupDataTable() {
        // Set default filter to attachment
        currentTypeFilter = 'attachment';
        
        // Create dynamic header
        createDynamicHeader(currentTypeFilter);
        
        attachmentTable = $('#inventory-attachment-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('warehouse/inventory/invent_attachment') ?>',
                type: 'POST',
                data: function(d) {
                    d.tipe_item = currentTypeFilter;
                    d.status_filter = currentStatusFilter;
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    console.log('Sending data to server:', d);
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables Ajax Error:');
                    console.log('XHR:', xhr);
                    console.log('Error:', error);
                    console.log('Thrown:', thrown);
                    console.log('Response Text:', xhr.responseText);
                    
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memuat data. Silakan periksa console untuk detail.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            columns: getDynamicColumns(currentTypeFilter),
            order: [[ 0, "desc" ]]
        });

        // Add click event to table rows
        $('#inventory-attachment-table tbody').on('click', 'tr', function() {
            const data = attachmentTable.row(this).data();
            if (data && data.id_inventory_attachment) {
                viewAttachment(data.id_inventory_attachment);
            }
        });

        // Handle edit form submission
        $('#editAttachmentForm').on('submit', function(e) {
            e.preventDefault();
            const id = $('#edit_id').val();
            $.ajax({
                url: `<?= base_url('warehouse/inventory/update-attachment/') ?>${id}`,
                type: 'POST',
                data: $(this).serialize() + '&<?= csrf_token() ?>=<?= csrf_hash() ?>',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        $('#editAttachmentModal').modal('hide');
                        attachmentTable.ajax.reload(null, false);
                        Swal.fire('Berhasil!', response.message, 'success');
                    } else {
                        Swal.fire('Gagal!', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                }
            });
        });
    }

    window.applyTypeFilter = function(type) {
        console.log('Applying type filter:', type);
        
        // Remove active class from all tabs
        $('.nav-link').removeClass('active');
        
        // Add active class to clicked tab
        if (type === 'attachment') {
            $('#attachment-tab').addClass('active');
            $('.card-title').text('Daftar Stok Attachment');
        } else if (type === 'battery') {
            $('#battery-tab').addClass('active');
            $('.card-title').text('Daftar Stok Battery');
        } else if (type === 'charger') {
            $('#charger-tab').addClass('active');
            $('.card-title').text('Daftar Stok Charger');
        }
        
        // Update current filter
        currentTypeFilter = type;
        currentStatusFilter = 'all'; // Reset status filter when type changes
        console.log('Current type filter set to:', currentTypeFilter);
        console.log('Status filter reset to:', currentStatusFilter);
        
        // Reset status tabs to 'All'
        $('#statusFilterTab .nav-link').removeClass('active');
        $('#all-status-tab').addClass('active');
        
        // Destroy existing table
        if (attachmentTable) {
            attachmentTable.destroy();
            $('#inventory-attachment-table').empty(); // Clear table completely
        }
        
        // Recreate table structure
        $('#inventory-attachment-table').html('<thead id="table-header"></thead><tbody></tbody>');
        
        // Recreate dynamic header
        createDynamicHeader(type);
        
        // Recreate table with new columns
        attachmentTable = $('#inventory-attachment-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '<?= base_url('warehouse/inventory/invent_attachment') ?>',
                type: 'POST',
                data: function(d) {
                    d.tipe_item = currentTypeFilter;
                    d.status_filter = currentStatusFilter;
                    d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                    console.log('Sending data to server:', d);
                    return d;
                },
                error: function(xhr, error, thrown) {
                    console.log('DataTables Ajax Error:', {xhr, error, thrown});
                    Swal.fire({
                        title: 'Error!',
                        text: 'Terjadi kesalahan saat memuat data.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            columns: getDynamicColumns(currentTypeFilter),
            order: [[ 0, "desc" ]]
        });
        
        // Add click event to table rows
        $('#inventory-attachment-table tbody').off('click').on('click', 'tr', function() {
            const data = attachmentTable.row(this).data();
            if (data && data.id_inventory_attachment) {
                viewAttachment(data.id_inventory_attachment);
            }
        });
    }

    // Status Filter Function
    window.applyStatusFilter = function(status) {
        console.log('Applying status filter:', status);
        
        // Remove active class from all status tabs
        $('#statusFilterTab .nav-link').removeClass('active');
        
        // Add active class to clicked status tab
        if (status === 'all') {
            $('#all-status-tab').addClass('active');
        } else if (status === 'AVAILABLE') {
            $('#available-status-tab').addClass('active');
        } else if (status === 'IN_USE') {
            $('#inuse-status-tab').addClass('active');
        } else if (status === 'MAINTENANCE') {
            $('#maintenance-status-tab').addClass('active');
        } else if (status === 'BROKEN') {
            $('#broken-status-tab').addClass('active');
        }
        
        // Update current status filter
        currentStatusFilter = status;
        console.log('Current status filter set to:', currentStatusFilter);
        
        // Reload table with new filters
        if (attachmentTable) {
            attachmentTable.ajax.reload();
        }
    }

    window.viewAttachment = function(id) {
        console.log('viewAttachment called for ID:', id);
        currentAttachmentId = id; // Store current ID for edit/delete actions
        
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-attachment-detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {
                $('#attachmentDetailContent').html('<div class="text-center p-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><br><br>Memuat detail attachment...</div>');
                $('#viewAttachmentModal').modal('show');
            },
            success: function(response) {
                console.log('AJAX Success Response:', response);
                
                if (response.success) {
                    const data = response.data;
                    const detailHtml = createAttachmentDetailHtml(data);
                    $('#attachmentDetailContent').html(detailHtml);
                    
                    // Show/hide action buttons based on attachment status
                    $('#btnAttachToUnit, #btnSwapUnit, #btnDetachFromUnit').hide();
                    
                    if (data.id_inventory_unit === null || data.id_inventory_unit === '' || data.id_inventory_unit === 0) {
                        // Item tidak terpasang → Show "Pasang ke Unit"
                        $('#btnAttachToUnit').show();
                    } else {
                        // Item terpasang → Show "Pindah Unit" & "Lepas dari Unit"
                        $('#btnSwapUnit').show();
                        $('#btnDetachFromUnit').show();
                    }
                } else {
                    const errorHtml = `
                        <div class="alert alert-danger">
                            <h5><i class="fas fa-exclamation-triangle"></i> Gagal Memuat Detail</h5>
                            <p>${response.message || 'Terjadi kesalahan tidak diketahui'}</p>
                        </div>
                    `;
                    $('#attachmentDetailContent').html(errorHtml);
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX Error:', {xhr, status, error});
                console.log('Response Text:', xhr.responseText);
                
                let errorMessage = 'Terjadi kesalahan saat memuat detail attachment.';
                
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMessage = errorResponse.message;
                    }
                } catch (e) {
                    errorMessage += ' (Server Error ' + xhr.status + ')';
                }
                
                const errorHtml = `
                    <div class="alert alert-danger">
                        <h5><i class="fas fa-exclamation-triangle"></i> Error ${xhr.status}</h5>
                        <p>${errorMessage}</p>
                        <details class="mt-2">
                            <summary>Technical Details</summary>
                            <pre class="mt-2 text-muted small">${xhr.responseText}</pre>
                        </details>
                    </div>
                `;
                $('#attachmentDetailContent').html(errorHtml);
            }
        });
    }

    function createAttachmentDetailHtml(data) {
        const h = (str) => {
            if (str === null || str === undefined || str === '') {
                return '-';
            }
            return String(str).replace(/</g, '&lt;').replace(/>/g, '&gt;');
        };
        
        console.log('Creating detail HTML for data:', data);
        
        return `
            <div class="row">
                <!-- Basic Attachment Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-primary text-dark">
                            <h6 class="mb-0"><i class="fas fa-puzzle-piece me-2"></i><strong>Informasi Attachment</strong></h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>ID Attachment</strong></td><td>: ${h(data.id_inventory_attachment)}</td></tr>
                                <tr><td><strong>Tipe Item</strong></td><td>: ${h(data.tipe_item)}</td></tr>
                                <tr><td><strong>SN Attachment</strong></td><td>: ${h(data.sn_attachment)}</td></tr>
                                <tr><td><strong>SN Battery</strong></td><td>: ${h(data.sn_baterai)}</td></tr>
                                <tr><td><strong>SN Charger</strong></td><td>: ${h(data.sn_charger)}</td></tr>
                                <tr><td><strong>Unit</strong></td><td>: ${h(data.no_unit)}</td></tr>
                                <tr><td><strong>Status</strong></td><td>: <span class="badge bg-primary">${h(data.attachment_status)}</span></td></tr>
                                <tr><td><strong>Status Unit</strong></td><td>: ${h(data.status_unit_name)}</td></tr>
                                <tr><td><strong>Lokasi Penyimpanan</strong></td><td>: ${h(data.lokasi_penyimpanan)}</td></tr>
                                <tr><td><strong>Kondisi Fisik</strong></td><td>: <span class="badge ${data.kondisi_fisik === 'Baik' ? 'bg-success' : data.kondisi_fisik === 'Rusak Berat' ? 'bg-danger' : 'bg-warning'}">${h(data.kondisi_fisik)}</span></td></tr>
                                <tr><td><strong>Kelengkapan</strong></td><td>: <span class="badge ${data.kelengkapan === 'Lengkap' ? 'bg-success' : 'bg-warning'}">${h(data.kelengkapan)}</span></td></tr>
                                <tr><td><strong>Tanggal Masuk</strong></td><td>: ${h(data.tanggal_masuk)}</td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order Information -->
                <div class="col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-header bg-warning text-dark">
                            <h6 class="mb-0"><i class="fas fa-file-invoice me-2"></i><strong>Informasi PO</strong></h6>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-borderless">
                                <tr><td width="40%"><strong>No. PO</strong></td><td>: ${h(data.no_po) || 'Manual Entry'}</td></tr>
                                <tr><td><strong>Tanggal PO</strong></td><td>: ${h(data.tanggal_po) || '-'}</td></tr>
                                <tr><td><strong>Supplier</strong></td><td>: ${h(data.nama_supplier) || '-'}</td></tr>
                                <tr><td><strong>Status PO</strong></td><td>: <span class="badge bg-secondary">${h(data.status) || '-'}</span></td></tr>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="col-12 mb-4">
                    <div class="card">
                        <div class="card-header bg-info text-dark">
                            <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i><strong>Informasi Tambahan</strong></h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Created At:</strong> ${h(data.created_at)}</p>
                                    <p><strong>Updated At:</strong> ${h(data.updated_at)}</p>
                                </div>
                                <div class="col-md-6">
                                    ${data.catatan_inventory ? `
                                    <p><strong>Catatan Inventory:</strong></p>
                                    <p class="text-muted">${h(data.catatan_inventory)}</p>
                                    ` : '<p class="text-muted">Tidak ada catatan tambahan</p>'}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function editAttachment(id) {
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-attachment-detail/') ?>${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#edit_id').val(data.id_inventory_attachment);
                    $('#edit_sn_attachment').val(data.sn_attachment);
                    $('#edit_sn_charger').val(data.sn_charger);
                    $('#edit_status_unit').val(data.status_unit);
                    $('#edit_lokasi').val(data.lokasi_penyimpanan);
                    $('#edit_kondisi').val(data.kondisi_fisik);
                    $('#edit_kelengkapan').val(data.kelengkapan);
                    $('#editAttachmentModal').modal('show');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            }
        });
    }

    // Functions for modal action buttons
    window.editCurrentAttachment = function() {
        if (currentAttachmentId) {
            $('#viewAttachmentModal').modal('hide');
            editAttachment(currentAttachmentId);
        }
    }

    window.deleteCurrentAttachment = function() {
        if (currentAttachmentId) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Apakah Anda yakin ingin menghapus item attachment ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `<?= base_url('warehouse/inventory/delete-attachment/') ?>${currentAttachmentId}`,
                        type: 'DELETE',
                        data: { '<?= csrf_token() ?>': '<?= csrf_hash() ?>' },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                $('#viewAttachmentModal').modal('hide');
                                attachmentTable.ajax.reload();
                                Swal.fire('Berhasil!', response.message, 'success');
                            } else {
                                Swal.fire('Gagal!', response.message, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('Error!', 'Tidak dapat terhubung ke server.', 'error');
                        }
                    });
                }
            });
        }
    }

    // ===================================
    // ATTACH / DETACH / SWAP FUNCTIONS
    // ===================================
    
    let currentAttachmentData = null;
    
    // Open Attach to Unit Modal
    window.openAttachModal = function() {
        if (!currentAttachmentId) return;
        
        // Get current attachment data
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-attachment-detail/') ?>${currentAttachmentId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentAttachmentData = response.data;
                    
                    // Set form data
                    $('#attach_attachment_id').val(currentAttachmentData.id_inventory_attachment);
                    $('#attach_attachment_type').val(currentAttachmentData.tipe_item);
                    
                    // Set label
                    const label = getAttachmentLabel(currentAttachmentData);
                    $('#attach_item_label').html(`<strong>${label}</strong>`);
                    
                    // Load available units with attachment type
                    loadAvailableUnits('#attach_unit_id', currentAttachmentData.tipe_item);
                    
                    // Show modal
                    $('#viewAttachmentModal').modal('hide');
                    $('#attachToUnitModal').modal('show');
                }
            }
        });
    }
    
    // Open Swap Unit Modal
    window.openSwapModal = function() {
        if (!currentAttachmentId) return;
        
        // Get current attachment data
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-attachment-detail/') ?>${currentAttachmentId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentAttachmentData = response.data;
                    
                    // Set form data
                    $('#swap_attachment_id').val(currentAttachmentData.id_inventory_attachment);
                    $('#swap_from_unit_id').val(currentAttachmentData.id_inventory_unit);
                    
                    // Set labels
                    const label = getAttachmentLabel(currentAttachmentData);
                    $('#swap_item_label').html(`<strong>${label}</strong>`);
                    $('#swap_from_unit_label').text(`Unit ${currentAttachmentData.no_unit || currentAttachmentData.id_inventory_unit}`);
                    
                    // Load available units with attachment type
                    loadAvailableUnits('#swap_to_unit_id', currentAttachmentData.tipe_item);
                    
                    // Reset form
                    $('#swap_reason_select').val('');
                    $('#swap_custom_reason').val('');
                    $('#swap_custom_reason_group').hide();
                    
                    // Show modal
                    $('#viewAttachmentModal').modal('hide');
                    $('#swapUnitModal').modal('show');
                }
            }
        });
    }
    
    // Open Detach from Unit Modal
    window.openDetachModal = function() {
        if (!currentAttachmentId) return;
        
        // Get current attachment data
        $.ajax({
            url: `<?= base_url('warehouse/inventory/get-attachment-detail/') ?>${currentAttachmentId}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    currentAttachmentData = response.data;
                    
                    // Set form data
                    $('#detach_attachment_id').val(currentAttachmentData.id_inventory_attachment);
                    $('#detach_from_unit_id').val(currentAttachmentData.id_inventory_unit);
                    
                    // Set labels
                    const label = getAttachmentLabel(currentAttachmentData);
                    $('#detach_item_label').html(`<strong>${label}</strong>`);
                    $('#detach_from_unit_label').text(`Unit ${currentAttachmentData.no_unit || currentAttachmentData.id_inventory_unit}`);
                    
                    // Reset form
                    $('#detach_reason_select').val('');
                    $('#detach_custom_reason').val('');
                    $('#detach_custom_reason_group').hide();
                    $('#detach_new_location').val('Workshop');
                    
                    // Show modal
                    $('#viewAttachmentModal').modal('hide');
                    $('#detachFromUnitModal').modal('show');
                }
            }
        });
    }
    
    // Helper function to get attachment label
    function getAttachmentLabel(data) {
        if (data.tipe_item === 'attachment') {
            return `Attachment ${data.attachment_merk || ''} ${data.attachment_tipe || ''} (SN: ${data.sn_attachment || '-'})`;
        } else if (data.tipe_item === 'battery') {
            return `Battery ${data.merk_baterai || ''} ${data.tipe_baterai || ''} (SN: ${data.sn_baterai || '-'})`;
        } else if (data.tipe_item === 'charger') {
            return `Charger ${data.merk_charger || ''} ${data.tipe_charger || ''} (SN: ${data.sn_charger || '-'})`;
        }
        return 'Item';
    }
    
    // Load available units for dropdown with Select2
    function loadAvailableUnits(targetSelector, attachmentType = null) {
        $.ajax({
            url: '<?= base_url('warehouse/inventory/get-available-units') ?>',
            type: 'GET',
            dataType: 'json',
            data: { 
                attachment_type: attachmentType 
            },
            success: function(response) {
                if (response.success) {
                    const select = $(targetSelector);
                    select.empty();
                    select.append('<option value="">Pilih Unit...</option>');
                    
                    response.units.forEach(unit => {
                        // Add data attributes for existing attachments check
                        const option = $('<option></option>')
                            .val(unit.id_inventory_unit)
                            .text(`Unit ${unit.no_unit} - ${unit.model_unit || ''} (${unit.status_unit_name || ''})`)
                            .data('hasAttachment', unit.has_attachment || false)
                            .data('hasBattery', unit.has_battery || false)
                            .data('hasCharger', unit.has_charger || false);
                        
                        select.append(option);
                    });
                    
                    // Initialize Select2 with search
                    if (!select.hasClass('select2-hidden-accessible')) {
                        select.select2({
                            theme: 'bootstrap-5',
                            placeholder: 'Ketik untuk mencari unit...',
                            allowClear: true,
                            width: '100%',
                            dropdownParent: select.closest('.modal')
                        });
                    }
                    
                    // Add change event to show warning if unit has existing attachment
                    select.on('change', function() {
                        checkExistingAttachment($(this));
                    });
                } else {
                    Swal.fire('Error', 'Gagal memuat daftar unit', 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Tidak dapat terhubung ke server', 'error');
            }
        });
    }
    
    // Check if selected unit has existing attachment
    function checkExistingAttachment(selectElement) {
        const selectedOption = selectElement.find('option:selected');
        const modalId = selectElement.closest('.modal').attr('id');
        
        let warningDiv, typeSpan, itemType;
        
        if (modalId === 'attachToUnitModal') {
            warningDiv = $('#attach_existing_warning');
            typeSpan = $('#attach_existing_type');
            itemType = $('#attach_attachment_type').val();
        } else if (modalId === 'swapUnitModal') {
            warningDiv = $('#swap_existing_warning');
            typeSpan = $('#swap_existing_type');
            itemType = currentAttachmentData?.tipe_item;
        }
        
        if (!warningDiv || !itemType) return;
        
        // Check if unit has existing item of same type
        let hasExisting = false;
        let existingTypeName = '';
        
        if (itemType === 'attachment' && selectedOption.data('hasAttachment')) {
            hasExisting = true;
            existingTypeName = 'Attachment';
        } else if (itemType === 'battery' && selectedOption.data('hasBattery')) {
            hasExisting = true;
            existingTypeName = 'Battery';
        } else if (itemType === 'charger' && selectedOption.data('hasCharger')) {
            hasExisting = true;
            existingTypeName = 'Charger';
        }
        
        if (hasExisting) {
            typeSpan.text(existingTypeName);
            warningDiv.show();
        } else {
            warningDiv.hide();
        }
    }
    
    // Toggle custom reason input
    window.toggleSwapReasonInput = function(value) {
        if (value === 'custom') {
            $('#swap_custom_reason_group').show();
            $('#swap_custom_reason').prop('required', true);
        } else {
            $('#swap_custom_reason_group').hide();
            $('#swap_custom_reason').prop('required', false);
        }
    }
    
    window.toggleDetachReasonInput = function(value) {
        if (value === 'custom') {
            $('#detach_custom_reason_group').show();
            $('#detach_custom_reason').prop('required', true);
        } else {
            $('#detach_custom_reason_group').hide();
            $('#detach_custom_reason').prop('required', false);
        }
    }
    
    // Form submissions
    $('#attachToUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const data = {
            attachment_id: $('#attach_attachment_id').val(),
            unit_id: $('#attach_unit_id').val(),
            notes: $('#attach_notes').val(),
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url('warehouse/inventory/attach-to-unit') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#attachToUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    Swal.fire('Berhasil!', response.message, 'success');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Tidak dapat terhubung ke server', 'error');
            }
        });
    });
    
    $('#swapUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const reasonSelect = $('#swap_reason_select').val();
        const reason = reasonSelect === 'custom' ? $('#swap_custom_reason').val() : reasonSelect;
        
        if (!reason) {
            Swal.fire('Error', 'Pilih atau isi alasan pindah unit', 'error');
            return;
        }
        
        const data = {
            attachment_id: $('#swap_attachment_id').val(),
            from_unit_id: $('#swap_from_unit_id').val(),
            to_unit_id: $('#swap_to_unit_id').val(),
            reason: reason,
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url('warehouse/inventory/swap-unit') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#swapUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    Swal.fire('Berhasil!', response.message, 'success');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Tidak dapat terhubung ke server', 'error');
            }
        });
    });
    
    $('#detachFromUnitForm').on('submit', function(e) {
        e.preventDefault();
        
        const reasonSelect = $('#detach_reason_select').val();
        const reason = reasonSelect === 'custom' ? $('#detach_custom_reason').val() : reasonSelect;
        
        if (!reason) {
            Swal.fire('Error', 'Pilih atau isi alasan lepas dari unit', 'error');
            return;
        }
        
        const data = {
            attachment_id: $('#detach_attachment_id').val(),
            reason: reason,
            new_location: $('#detach_new_location').val(),
            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
        };
        
        $.ajax({
            url: '<?= base_url('warehouse/inventory/detach-from-unit') ?>',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#detachFromUnitModal').modal('hide');
                    attachmentTable.ajax.reload();
                    Swal.fire('Berhasil!', response.message, 'success');
                } else {
                    Swal.fire('Gagal!', response.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error!', 'Tidak dapat terhubung ke server', 'error');
            }
        });
    });

    // Open Add Item Modal
    window.openAddItemModal = function(type) {
        console.log(`📝 Opening add ${type} modal`);
        
        // Set the type in hidden field
        $('#new-tipe-item').val(type);
        
        // Show/hide appropriate fields based on type
        if (type === 'attachment') {
            $('#attachment-fields').show();
            $('#battery-fields').hide();
            $('#charger-fields').hide();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Tambah Attachment Baru');
            // Load attachment master data
            loadMasterData('attachment', '#new-attachment-id');
        } else if (type === 'battery') {
            $('#attachment-fields').hide();
            $('#battery-fields').show();
            $('#charger-fields').hide();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Tambah Baterai Baru');
            // Load baterai master data
            loadMasterData('baterai', '#new-baterai-id');
        } else if (type === 'charger') {
            $('#attachment-fields').hide();
            $('#battery-fields').hide();
            $('#charger-fields').show();
            $('#addItemModal .modal-title').html('<i class="fas fa-plus-circle me-2"></i>Tambah Charger Baru');
            // Load charger master data
            loadMasterData('charger', '#new-charger-id');
        }
        
        // Reset form
        $('#addItemForm')[0].reset();
        $('#new-tipe-item').val(type);
        
        // Load units data
        loadUnitsData();
        
        // Show modal
        $('#addItemModal').modal('show');
    }

    // Load master data for dropdown in modal
    function loadMasterData(type, selectElement) {
        let url = '';
        switch(type) {
            case 'attachment':
                url = '<?= base_url('warehouse/master-attachment') ?>';
                break;
            case 'baterai':
                url = '<?= base_url('warehouse/master-baterai') ?>';
                break;
            case 'charger':
                url = '<?= base_url('warehouse/master-charger') ?>';
                break;
        }
        
        if (url) {
            $.ajax({
                url: url,
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data) {
                        const $select = $(selectElement);
                        $select.empty().append('<option value="">Pilih ' + type.charAt(0).toUpperCase() + type.slice(1) + '</option>');
                        
                        response.data.forEach(function(item) {
                            $select.append(`<option value="${item.id}">${item.text}</option>`);
                        });
                        
                        console.log(`✅ ${type} master data loaded: ${response.data.length} items`);
                    } else {
                        console.error(`❌ Failed to load ${type} master data:`, response);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(`❌ Error loading ${type} master data:`, error);
                }
            });
        }
    }

    // Load units data
    function loadUnitsData() {
        $.ajax({
            url: '<?= base_url('warehouse/get-units') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success && response.data) {
                    const $select = $('#new-unit-id');
                    $select.empty().append('<option value="">Pilih Unit (Opsional)</option>');
                    
                    response.data.forEach(function(unit) {
                        $select.append(`<option value="${unit.id}">${unit.nomor_unit} - ${unit.merk} ${unit.model}</option>`);
                    });
                    
                    console.log(`✅ Units data loaded: ${response.data.length} items`);
                }
            },
            error: function(xhr, status, error) {
                console.error(`❌ Error loading units data:`, error);
            }
        });
    }

    // Save new item
    $('#btn-save-item').on('click', function() {
        const formData = new FormData($('#addItemForm')[0]);
        const type = $('#new-tipe-item').val();
        
        console.log('🔧 Debug Add Item:');
        console.log('Type from form:', type);
        console.log('Form data entries:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Validate required fields
        let isValid = true;
        let errorMessage = '';
        
        if (type === 'attachment') {
            if (!$('#new-attachment-id').val()) {
                isValid = false;
                errorMessage = 'Jenis Attachment wajib dipilih';
            } else if (!$('#new-sn-attachment').val()) {
                isValid = false;
                errorMessage = 'Serial Number wajib diisi';
            }
        } else if (type === 'battery') {
            if (!$('#new-baterai-id').val()) {
                isValid = false;
                errorMessage = 'Jenis Baterai wajib dipilih';
            } else if (!$('#new-sn-baterai').val()) {
                isValid = false;
                errorMessage = 'Serial Number wajib diisi';
            }
        } else if (type === 'charger') {
            if (!$('#new-charger-id').val()) {
                isValid = false;
                errorMessage = 'Jenis Charger wajib dipilih';
            } else if (!$('#new-sn-charger').val()) {
                isValid = false;
                errorMessage = 'Serial Number wajib diisi';
            }
        }
        
        if (!isValid) {
            Swal.fire('Validasi Error', errorMessage, 'warning');
            return;
        }
        
        // Add CSRF token
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
        
        $.ajax({
            url: '<?= base_url('warehouse/add-inventory-item') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            beforeSend: function() {
                $('#btn-save-item').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i>Menyimpan...');
            },
            success: function(response) {
                $('#btn-save-item').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan Item');
                
                if (response.success) {
                    $('#addItemModal').modal('hide');
                    Swal.fire('Berhasil!', response.message || 'Item berhasil ditambahkan!', 'success');
                    
                    // Reload the table
                    attachmentTable.ajax.reload();
                    
                } else {
                    Swal.fire('Gagal!', response.message || 'Gagal menambahkan item', 'error');
                }
            },
            error: function(xhr, status, error) {
                $('#btn-save-item').prop('disabled', false).html('<i class="fas fa-save me-1"></i>Simpan Item');
                console.error('Error adding item:', error);
                Swal.fire('Error!', 'Terjadi kesalahan saat menambahkan item', 'error');
            }
        });
    });
</script>
<?= $this->endSection() ?>
