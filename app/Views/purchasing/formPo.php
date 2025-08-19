<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<style>
    .form-section { border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); margin-bottom: 2rem; }
    .section-header { background: #f8f9fa; color: #333; padding: 1rem 1.5rem; border-radius: 10px 10px 0 0; }
    .table thead th { background: #f1f3f6; }
    .table tbody tr.selected { background: #e9ecef; }
    .item-card { border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 1rem; }
    .modal-lg { max-width: 900px; }
    
    /* ===== SIMPLIFIED SELECT2 + MODAL STYLES (stabil & klik-able) ===== */
    .modal-backdrop { z-index: 1040; }
    .modal { z-index: 1050; }
    /* Kontainer Select2 (default) */
    .select2-container { width:100% !important; }
    /* Dropdown di luar modal */
    .select2-container .select2-dropdown { z-index: 1060; }
    /* Dropdown di dalam modal diletakkan lebih tinggi */
    .modal .select2-container .select2-dropdown { z-index: 2000; }
    /* Pastikan modal tidak memotong dropdown (biarkan root modal visible) */
    .modal, .modal-dialog, .modal-content { overflow: visible; }
    /* Modal body scrollable agar tombol di bagian bawah form tetap dapat diakses */
    #itemModal .modal-body {
        overflow-y: auto;
        overflow-x: hidden;
        max-height: calc(100vh - 220px); /* ruang untuk header+footer */
        padding-right: .75rem;
        position: relative;
    }
    /* Hilangkan double scroll bila fragment punya container internal */
    #itemModal .modal-body .form-section:last-child { margin-bottom: 2rem; }
    /* Tabs tetap bisa diklik */
    #itemTabs { position: relative; z-index: 1070; }
    #itemTabs .nav-link { cursor: pointer; }
    /* Hilangkan force positioning lama */
    .select2-dropdown { position: absolute; }
    /* Spasi kecil antar field */
    .modal .form-select { width:100%; }
    .modal-body .select2-container {
        position: relative !important;
    }
    
    .modal-body .select2-dropdown {
        position: absolute !important;
        width: 100% !important;
    }
    
    /* Prevent dropdown from being cut off */
    .modal-dialog {
        overflow: visible !important;
    }
    
    .modal-content {
        overflow: visible !important;
    }
</style>

<!-- Fungsi JavaScript untuk modal -->
<script>
// Function untuk menampilkan modal langsung dari onclick
// Fungsi global untuk membuka modal
function showModalDirect() {
    // Cek jika form unit sudah dimuat
    if ($('#unitForm').length) {
        $('#unitForm')[0].reset(); // 1. Reset input biasa seperti 'Tahun' dan 'Keterangan'
        $('#unitForm .form-select').val(null).trigger('change'); // 2. Reset semua dropdown Select2 di dalam form secara khusus
    }
    if ($('#attachmentForm').length) {
        $('#attachmentForm')[0].reset();
        $('#attachmentForm .form-select').val(null).trigger('change');
    }
    if ($('#sparepartForm').length) {
        $('#sparepartForm')[0].reset();
        $('#sparepartForm .form-select').val(null).trigger('change');
    }

    // Tampilkan modal
    const myModal = new bootstrap.Modal(document.getElementById('itemModal'));
    myModal.show();
}

</script>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Flash messages now handled globally via top-right toast in base layout -->
    <form action="<?= base_url('/purchasing/store-po-dinamis') ?>" method="post" id="formPoDinamis">
        <?= csrf_field() ?>
        <!-- Header PO -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-info-circle me-2"></i>Header Purchase Order</h5>
            <div class="row g-3 p-4">
                <div class="col-md-4">
                    <label for="no_po" class="form-label">PO Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="no_po" required>
                </div>
                <div class="col-md-4">
                    <label for="tanggal_po" class="form-label">PO Date <span class="text-danger">*</span></label>
                    <input type="date" class="form-control" name="tanggal_po" value="<?= date('Y-m-d') ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="id_supplier" class="form-label">Supplier <span class="text-danger">*</span></label>
                    <select name="id_supplier" id="id_supplier" class="form-select select2-basic" required>
                        <option value="">Pilih Supplier...</option>
                        <?php if (isset($suppliers) && is_array($suppliers)): ?>
                            <?php foreach ($suppliers as $item): ?>
                                <option value="<?= $item['id_supplier'] ?>"><?= esc($item['nama_supplier']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="">Tidak ada data supplier</option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="invoice_no" class="form-label">Invoice Number</label>
                    <input type="text" class="form-control" name="invoice_no">
                </div>
                <div class="col-md-4">
                    <label for="invoice_date" class="form-label">Invoice Date</label>
                    <input type="date" class="form-control" name="invoice_date">
                </div>
                <div class="col-md-4">
                    <label for="bl_date" class="form-label">BL Date</label>
                    <input type="date" class="form-control" name="bl_date">
                </div>
                <div class="col-12">
                    <label for="keterangan_po" class="form-label">Keterangan PO</label>
                    <input type="text" class="form-control" name="keterangan_po">
                </div>
            </div>
        </div>
        <!-- Tabel Item PO -->
        <div class="form-section">
            <h5 class="section-header"><i class="fas fa-list me-2"></i>Daftar Item PO</h5>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-bordered align-middle" id="po-item-table">
                        <thead>
                            <tr>
                                <th style="width:5%;">No</th>
                                <th style="width:15%;">Item</th>
                                <th style="width:45%;">Deskripsi</th>
                                <th style="width:10%;">Qty</th>
                                <th style="width:15%;">Hasil Verifikasi</th>
                                <th style="width:10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Baris item akan di-generate JS -->
                        </tbody>
                    </table>
                </div>
                    <button type="button" class="btn btn-outline-primary mt-3" id="add-item-row" data-bs-toggle="modal" data-bs-target="#itemModal">
                     <i class="fas fa-plus me-2"></i>Tambah Item
                 </button>
            </div>
        </div>
        <!-- Modal Input Item Dinamis -->
        <div class="modal fade" id="itemModal" tabindex="1" aria-labelledby="itemModalLabel" aria-hidden="true"> <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="itemModalLabel">Pilih Jenis Item PO</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <ul class="nav nav-tabs" id="itemTabs" role="tablist"> <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="unit-tab" data-bs-toggle="tab" data-bs-target="#unit" type="button" role="tab" aria-controls="unit" aria-selected="true"> <i class="fas fa-truck me-2"></i>Unit
                            </button>
                        </li>
                        
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="attachment-tab" data-bs-toggle="tab" data-bs-target="#attachment" type="button" role="tab" aria-controls="attachment" aria-selected="false">
                                <i class="fas fa-battery-full me-2"></i>Attachment & Battery
                            </button>
                        </li>

                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="sparepart-tab" data-bs-toggle="tab" data-bs-target="#sparepart" type="button" role="tab" aria-controls="sparepart" aria-selected="false">
                                <i class="fas fa-tools me-2"></i>Sparepart
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content mt-3" id="itemTabContent">
                        <div class="tab-pane fade show active" id="unit" role="tabpanel"></div>

                        <div class="tab-pane fade" id="attachment" role="tabpanel"></div>

                        <div class="tab-pane fade" id="sparepart" role="tabpanel"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <div class="d-flex justify-content-end mb-4">
            <a href="<?= base_url('/purchasing/po-list') ?>" class="btn btn-secondary me-2">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i>Simpan PO
            </button>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('script') ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>

<script>

    $(document).ready(function() {
    // --- GLOBAL VARIABLES & INITIALIZATION ---
        let items = [];

        // Cleanup artifacts (backdrop / open select2) setelah modal ditutup paksa
        function cleanupModalArtifacts() {
            // Tutup semua select2 terbuka di modal
            $('#itemModal .select2-basic').each(function(){
                if ($(this).data('select2')) { try { $(this).select2('close'); } catch(e){} }
            });
            // Hapus ONLY dropdown floating (.select2-dropdown) yang terpisah dari container
            // Jangan hapus .select2-container karena bisa menghilangkan UI select (supplier jadi tidak bisa diklik)
            $('.select2-dropdown').remove();
            // Hapus backdrop jika masih ada (kadang tidak terhapus jika ada error JS)
            $('.modal-backdrop').remove();
            // Pastikan body class dilepas agar dapat scroll & klik
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('padding-right');
            // Pulihkan scroll utama halaman (kadang body overflow masih hidden)
            document.body.style.overflow = 'auto';
            document.documentElement.style.overflowY = 'auto';
            ensureSupplierSelect2();
        }

        // Pastikan select supplier tetap ter-inisialisasi
        function ensureSupplierSelect2(){
            const $sup = $('#id_supplier');
            if ($sup.length && !$sup.data('select2')) {
                $sup.show(); // tampilkan jika sempat tersembunyi tanpa container
                try { $sup.select2({theme:'bootstrap-5', width:'100%'}); } catch(e) { console.warn('Reinit supplier select2 fail', e); }
            }
        }

        // Tambahan keamanan untuk memulihkan scroll setelah modal benar2 tertutup
        $('#itemModal').on('hidden.bs.modal', function(){
            document.body.style.overflow = 'auto';
            document.documentElement.style.overflowY = 'auto';
    });

        // Listener keamanan: setiap 1 detik cek apakah ada backdrop tanpa modal aktif
        setInterval(function(){
            if (!$('.modal.show').length && $('.modal-backdrop').length) {
                cleanupModalArtifacts();
            }
        }, 1000);
        

        // SIMPLE SELECT2 INIT (stable inside & outside modal)
        function initializeSelect2(scope=document){
            const $scope = $(scope);
            // Destroy existing instances to avoid duplicates
            $scope.find('.select2-basic').each(function(){
                if($(this).data('select2')) $(this).select2('destroy');
            });
            // Outside modal
            $scope.find('.select2-basic').filter(function(){
                return $(this).closest('#itemModal').length === 0;
            }).select2({theme:'bootstrap-5', width:'100%'});
            // Inside modal
            $('#itemModal .select2-basic').select2({
                theme:'bootstrap-5',
                width:'100%',
                dropdownParent: $('#itemModal')
            });
        }
        initializeSelect2();

        // --- MODAL & DYNAMIC CONTENT LOADING ---
        function loadTabContent(tabElement) {
            const targetPaneId = $(tabElement).data('bs-target');
            const targetPane = $(targetPaneId);

            if (targetPane && targetPane.html().trim() === '') {
                targetPane.html('<p class="text-center p-5"><i class="fas fa-spinner fa-spin"></i> Loading...</p>');
                
                let urlToLoad = '';
                if (targetPaneId === '#unit') {
                    urlToLoad = '<?= base_url('/purchasing/api/get-unit-form') ?>';
                } else if (targetPaneId === '#attachment') {
                    urlToLoad = '<?= base_url('/purchasing/api/get-attachment-form') ?>';
                } else if (targetPaneId === '#sparepart') {
                    urlToLoad = '<?= base_url('/purchasing/api/get-sparepart-form') ?>';
                }

                // Use jQuery load with proper error handling
                targetPane.load(urlToLoad, function(response, status, xhr) {
                    if (status === "error") {
                        console.error('Load tab content error:', xhr.status, xhr.statusText);
                        targetPane.html(`<p class="text-danger text-center p-5">Gagal memuat form: ${xhr.status} ${xhr.statusText}</p>`);
                        return;
                    }
                    
                    // Check if response contains valid HTML
                    if (response && response.includes('<!DOCTYPE')) {
                        console.error('Received HTML error page instead of form fragment');
                        targetPane.html(`<p class="text-danger text-center p-5">Error: Received invalid response format</p>`);
                        return;
                    }
                    
                    console.log('Successfully loaded tab content for:', targetPaneId);
                    
                    // Single clean init (hindari compat errors)
                    initializeSelect2(targetPane);
                });
            }
        }

        // 1. Logika untuk chained dropdown Brand -> Model
        $(document).on('change', '#unit_merk', function() {
            const selectedMerk = $(this).find('option:selected').data('merk');
            const $modelDropdown = $('#unit_model');
            
            $modelDropdown.html('<option value="">Loading...</option>').prop('disabled', true);

            if (!selectedMerk) {
                $modelDropdown.html('<option value="">Pilih Brand Dahulu</option>').prop('disabled', true);
                return;
            }

            // Kita butuh API baru untuk mengambil model berdasarkan merk
            $.ajax({
                url: '<?= base_url('purchasing/api/get_model_unit_merk') ?>', // BUAT API BARU INI
                method: 'GET',
                data: { "merk": selectedMerk },
                dataType: 'json',
                success: function(response) {
                    $modelDropdown.html('<option value="">Pilih Model</option>').prop('disabled', false);
                    if (response && response.data && Array.isArray(response.data)) {
                        response.data.forEach(item => {
                            // Kirim NAMA MODEL, bukan ID, agar cocok dengan logika penyimpanan
                            $modelDropdown.append(`<option value="${item.id_model_unit}">${item.model_unit}</option>`);
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error:', status, error);
                    $modelDropdown.html('<option value="">Gagal memuat</option>');
                }
            });
        });
        
        // Modal tampil: load tab pertama & init select2 dalam modal
        $('#itemModal').on('shown.bs.modal', function () {
            const activeTab = document.querySelector('#itemTabs .nav-link.active');
            if (activeTab) loadTabContent(activeTab);
            initializeSelect2(this);
        });

        // Listener saat tab baru dipilih
        $('#itemTabs button[data-bs-toggle="tab"]').on('shown.bs.tab', function (event) {
            console.log('Tab shown:', event.target);
            loadTabContent(event.target);
        });
        
        // Additional explicit click handler for tabs to ensure they work
        $('#itemTabs button[data-bs-toggle="tab"]').on('click', function (event) {
            console.log('Tab clicked:', this);
            event.preventDefault();
            
            // Remove active classes from all tabs
            $('#itemTabs button[data-bs-toggle="tab"]').removeClass('active');
            $('.tab-pane').removeClass('show active');
            
            // Add active class to clicked tab
            $(this).addClass('active');
            const targetPaneId = $(this).data('bs-target');
            $(targetPaneId).addClass('show active');
            
            // Load content
            loadTabContent(this);
        });

        // Focus otomatis saat Select2 dibuka
        $(document).on('select2:open', function(){
            setTimeout(()=>{
                const sf = document.querySelector('.select2-container--open .select2-search__field');
                if(sf) sf.focus();
            },0);
        });
        
    // (Removed aggressive scroll/resize handlers to keep dropdown stable)
        
        // Listener untuk menampilkan/menyembunyikan field Battery Type
        $(document).on('change', '#unit_jenis', function() {
            const selectedJenis = $(this).val();
            const batteryField = $('#battery_type_container');
            if (selectedJenis === 'ELECTRIC') {
                batteryField.slideDown();
            } else {
                batteryField.slideUp();
            }
        });

        // --- CRUD LOGIC FOR PO ITEMS ---

        // CREATE: Handler untuk tombol Simpan Unit
        $(document).on('click', '#saveUnitButton', function(e) {
            e.preventDefault();

            // -- Validasi Manual --
            if ($('#jenis_unit').val() === null || $('#jenis_unit').val() === '') {
                OptimaPro.showNotification('Harap pilih Departemen.', 'warning');
                return;
            }
            if ($('#tipe_select').val() === null || $('#tipe_select').val() === '') {
                OptimaPro.showNotification('Harap pilih Tipe.', 'warning');
                return;
            }
            if ($('#jenis_select').val() === null || $('#jenis_select').val() === '') {
                OptimaPro.showNotification('Harap pilih Jenis.', 'warning');
                return;
            }
            if ($('#unit_merk').val() === null || $('#unit_merk').val() === '') {
                OptimaPro.showNotification('Harap pilih Brand Unit.', 'warning');
                return;
            }
            if ($('#unit_model').val() === null || $('#unit_model').val() === '') {
                OptimaPro.showNotification('Harap pilih Model Unit.', 'warning');
                return;
            }
            if ($('#unit_tahun').val() === null || $('#unit_tahun').val() === '') {
                OptimaPro.showNotification('Harap pilih Tahun Unit.', 'warning');
                return;
            }
            if ($('#unit_kapasitas').val() === null || $('#unit_kapasitas').val() === '') {
                OptimaPro.showNotification('Harap pilih Kapasitas Unit.', 'warning');
                return;
            }
            if ($('#unit_kondisi').val() === null || $('#unit_kondisi').val() === '') {
                OptimaPro.showNotification('Harap pilih Kondisi Unit.', 'warning');
                return;
            }
            if ($('#unit_qty').val() === null || $('#unit_qty').val() === '') {
                OptimaPro.showNotification('Harap masukkan Jumlah.', 'warning');
                return;
            }

            const unitData = { 
                item_type: 'unit', 
                jenis: $('#jenis_unit').val(),
                tipe: $('#tipe_select').val(), 
                jenis_unit: $('#jenis_select').val(),
                merk: $('#unit_merk').val(),
                model: $('#unit_model').val(),
                tahun: $('#unit_tahun').val(),
                kapasitas: $('#unit_kapasitas').val(),
                kondisi: $('#unit_kondisi').val(),
                mast: $('#unit_mast').val() || null,
                engine: $('#unit_engine').val() || null,
                tire: $('#unit_tire').val() || null,
                wheel: $('#unit_wheel').val() || null,
                valve: $('#unit_valve').val() || null,
                battery: $('#unit_battery').val() || null,
                qty: $('#unit_qty').val(),

                // Untuk deskripsi, kita ambil teksnya secara terpisah
                jenis_text: $('#jenis_unit option:selected').text(),
                tipe_text: $('#tipe_select option:selected').text(),
                jenis_unit_text: $('#jenis_select option:selected').text(),
                merk_text: $('#unit_merk option:selected').data('merk'),
                model_text: $('#unit_model option:selected').text(),
                kapasitas_text: $('#unit_kapasitas option:selected').text(),
                kondisi_text: $('#unit_kondisi option:selected').text(),
                mast_text: $('#unit_mast option:selected').text() || '',
                engine_text: $('#unit_engine option:selected').text() || '',
                tire_text: $('#unit_tire option:selected').text() || '',
                wheel_text: $('#unit_wheel option:selected').text() || '',
                valve_text: $('#unit_valve option:selected').text() || '',
                battery_text: $('#unit_battery option:selected').text() || '',
                
                keterangan: $('#keterangan').val() || ''
            };
            
            items.push(unitData);
            displayItems();
            const modalEl = document.getElementById('itemModal');
            const inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) {
                // Tutup select2 sebelum hide untuk mencegah overlay tertinggal
                $('#itemModal .select2-basic').each(function(){ if($(this).data('select2')) $(this).select2('close'); });
                inst.hide();
                // Setelah transisi selesai (fallback 400ms jika event tidak terpanggil)
                modalEl.addEventListener('hidden.bs.modal', function handler(){
                    cleanupModalArtifacts();
                    modalEl.removeEventListener('hidden.bs.modal', handler);
                });
                setTimeout(cleanupModalArtifacts, 450);
            } else {
                cleanupModalArtifacts();
            }
        });
        
        // Cascading dropdown untuk attachment: Tipe -> Merk -> Model
        $(document).on('change', '#att_tipe', function() {
            const selectedTipe = $(this).val();
            const $merkDropdown = $('#att_merk');
            const $modelDropdown = $('#att_model');
            
            // Reset dropdowns berikutnya
            $merkDropdown.html('<option value="">Loading...</option>').prop('disabled', true);
            $modelDropdown.html('<option value="">-- Pilih Merk Dulu --</option>').prop('disabled', true);

            if (!selectedTipe) {
                $merkDropdown.html('<option value="">-- Pilih Tipe Dulu --</option>').prop('disabled', false);
                return;
            }

            // Load merk berdasarkan tipe
            $.ajax({
                url: '<?= base_url('/purchasing/api/get-attachment-merk') ?>',
                method: 'GET',
                data: { tipe: selectedTipe },
                dataType: 'json',
                success: function(response) {
                    $merkDropdown.prop('disabled', false);
                    if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
                        $merkDropdown.html('<option value="">-- Pilih Merk --</option>');
                        response.data.forEach(function(item) {
                            $merkDropdown.append(`<option value="${item}">${item}</option>`);
                        });
                    } else {
                        $merkDropdown.html('<option value="">-- Tidak ada merk tersedia --</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error (get-attachment-merk):', status, error);
                    $merkDropdown.html('<option value="">-- Error loading merk --</option>').prop('disabled', false);
                }
            });
        });

        $(document).on('change', '#att_merk', function() {
            const selectedTipe = $('#att_tipe').val();
            const selectedMerk = $(this).val();
            const $modelDropdown = $('#att_model');
            
            $modelDropdown.html('<option value="">Loading...</option>').prop('disabled', true);

            if (!selectedTipe || !selectedMerk) {
                $modelDropdown.html('<option value="">-- Pilih Merk Dulu --</option>').prop('disabled', false);
                return;
            }

            // Load model berdasarkan tipe dan merk
            $.ajax({
                url: '<?= base_url('/purchasing/api/get-attachment-model') ?>',
                method: 'GET',
                data: { tipe: selectedTipe, merk: selectedMerk },
                dataType: 'json',
                success: function(response) {
                    $modelDropdown.prop('disabled', false);
                    if (response && response.data && Array.isArray(response.data) && response.data.length > 0) {
                        $modelDropdown.html('<option value="">-- Pilih Model --</option>');
                        response.data.forEach(function(item) {
                            $modelDropdown.append(`<option value="${item.id_attachment}">${item.model}</option>`);
                        });
                    } else {
                        $modelDropdown.html('<option value="">Tidak ada model tersedia</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.log('AJAX Error (get-attachment-model):', status, error);
                    $modelDropdown.html('<option value="">-- Error loading model --</option>').prop('disabled', false);
                }
            });
        });
        
        // 1. Event listener untuk menampilkan/menyembunyikan field
        $(document).on('change', '#att_po_type', function() {
            const selectedType = $(this).val();
            const attachmentFields = $('.attachment-fields');
            const batteryFields = $('.battery-fields');

            // Reset dan sembunyikan semua field, hapus atribut 'required'
            attachmentFields.hide().find('select').prop('required', false);
            batteryFields.hide().find('select').prop('required', false);

            // Tampilkan field yang relevan dan tambahkan 'required'
            if (selectedType === 'Attachment') {
                attachmentFields.slideDown();
                attachmentFields.find('select').prop('required', true);
            } else if (selectedType === 'Battery') {
                batteryFields.slideDown();
                batteryFields.find('select').prop('required', true);
            }
        });

        // READ: Fungsi untuk menampilkan semua item ke tabel
    function displayItems() {
        const tableBody = $('#po-item-table tbody');
        tableBody.empty();
        if (items.length === 0) {
            tableBody.html('<tr><td colspan="6" class="text-center">Belum ada item yang ditambahkan.</td></tr>');
            return;
        }
        items.forEach((item, index) => {
            let itemLabel = '';
            let deskripsi = '';
            
            if (item.item_type === 'unit') {
                itemLabel = 'Unit';
                
                // PERBAIKAN: Gunakan field dengan akhiran '_text' untuk deskripsi
                let mainSpecs = [
                    item.merk_text,
                    item.model_text,
                    item.jenis_text,
                    item.tipe_text,
                    item.jenis_unit_text,
                    item.kapasitas_text,
                    'Tahun ' + item.tahun,
                    '(' + item.kondisi_text + ')'
                ].filter(Boolean).join(' | ');

                let componentSpecs = [];
                if (item.mast_text && !item.mast_text.includes('--') && item.mast_text.trim() !== '') componentSpecs.push(`Mast: ${item.mast_text}`);
                if (item.engine_text && !item.engine_text.includes('--') && item.engine_text.trim() !== '') componentSpecs.push(`Engine: ${item.engine_text}`);
                if (item.tire_text && !item.tire_text.includes('--') && item.tire_text.trim() !== '') componentSpecs.push(`Tire: ${item.tire_text}`);
                if (item.wheel_text && !item.wheel_text.includes('--') && item.wheel_text.trim() !== '') componentSpecs.push(`Wheel: ${item.wheel_text}`);
                if (item.valve_text && !item.valve_text.includes('--') && item.valve_text.trim() !== '') componentSpecs.push(`Valve: ${item.valve_text}`);
                if (item.battery_text && !item.battery_text.includes('--') && item.battery_text.trim() !== '') componentSpecs.push(`Battery: ${item.battery_text}`);
                
                deskripsi = mainSpecs;
                if (componentSpecs.length > 0) {
                    deskripsi += `<br><small class="text-muted">Komponen: ${componentSpecs.join(', ')}</small>`;
                }
                if (item.keterangan) {
                     deskripsi += `<br><small class="text-info">Catatan: ${item.keterangan}</small>`;
                }
            } else if (item.item_type === 'attachment') {
                itemLabel = item.po_type; // Menampilkan "Attachment" atau "Battery"
                let details = [];
                if (item.po_type === 'Attachment') {
                    if (item.tipe_text && item.tipe_text.trim() !== '') details.push(`Tipe: ${item.tipe_text}`);
                    if (item.merk_text && item.merk_text.trim() !== '') details.push(`Merk: ${item.merk_text}`);
                    if (item.model_text && !item.model_text.includes('--') && item.model_text.trim() !== '') details.push(`Model: ${item.model_text}`);
                } else if (item.po_type === 'Battery') {
                    if (item.battery_type_text && !item.battery_type_text.includes('--') && item.battery_type_text.trim() !== '') details.push(`Baterai: ${item.battery_type_text}`);
                    if (item.charger_type_text && !item.charger_type_text.includes('--') && item.charger_type_text.trim() !== '') details.push(`Charger: ${item.charger_type_text}`);
                }
                if (item.serial_number) details.push(`SN: ${item.serial_number}`);
                deskripsi = details.join(' | ');
                if (item.keterangan) {
                    deskripsi += `<br><small class="text-info">Catatan: ${item.keterangan}</small>`;
                }

            } else if (item.item_type === 'sparepart') {
                    itemLabel = 'Sparepart';
                    let details = [item.name_text, `(${item.qty} ${item.satuan})`];
                    deskripsi = details.filter(Boolean).join(' ');
                    if (item.keterangan) {
                        deskripsi += `<br><small class="text-info">Catatan: ${item.keterangan}</small>`;
                    }
                }
                
                const row = `
                    <tr data-index="${index}">
                        <td>${index + 1}</td>
                        <td><span class="badge bg-primary">${itemLabel}</span></td>
                        <td>${deskripsi}</td>
                        <td class="text-center">${item.qty || ''}</td>
                        <td class="text-center">${item.hasil_verifikasi || '-'}</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger delete-item-row" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>`;
                tableBody.append(row);
            });
        }

        // Handler untuk tombol Simpan Attachment
        $(document).on('click', '#saveAttachmentButton', function(e) {
            e.preventDefault();
            // Validasi form attachment
            const poType = $('#att_po_type').val();
            if (!poType) {
                OptimaPro.showNotification('Harap pilih PO Type.', 'warning');
                return;
            }

            let isValid = true;
            if (poType === 'Attachment') {
                if (!$('#att_model').val()) {
                    OptimaPro.showNotification('Harap pilih Model untuk Attachment.', 'warning');
                    isValid = false;
                }
            } else if (poType === 'Battery') {
                if (!$('#att_battery_type').val()) {
                    OptimaPro.showNotification('Harap pilih Battery Type.', 'warning');
                    isValid = false;
                }
                if (!$('#att_charger_type').val()) {
                    OptimaPro.showNotification('Harap pilih Charger Type.', 'warning');
                    isValid = false;
                }
            }

            if (!$('#att_qty').val()) {
                OptimaPro.showNotification('Harap masukkan Quantity.', 'warning');
                isValid = false;
            }

            if (!isValid) return;

            const attachmentData = {
                item_type: 'attachment',
                po_type: $('#att_po_type').val(),
                attachment_id: $('#att_model').val(),
                baterai_id: $('#att_battery_type').val(),
                charger_id: $('#att_charger_type').val(),
                
                // Untuk deskripsi: ambil text dari cascading dropdown
                tipe_text: $('#att_tipe option:selected').text(),
                merk_text: $('#att_merk option:selected').text(),
                model_text: $('#att_model option:selected').text(),
                battery_type_text: $('#att_battery_type option:selected').text(),
                charger_type_text: $('#att_charger_type option:selected').text(),
                
                serial_number: $('#att_serial_number').val(),
                qty: $('#att_qty').val(),
                keterangan: $('#att_keterangan').val()
            };
            
            items.push(attachmentData);
            displayItems();
            const modalEl = document.getElementById('itemModal');
            const inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) {
                $('#itemModal .select2-basic').each(function(){ if($(this).data('select2')) $(this).select2('close'); });
                inst.hide();
                modalEl.addEventListener('hidden.bs.modal', function handler(){
                    cleanupModalArtifacts();
                    modalEl.removeEventListener('hidden.bs.modal', handler);
                });
                setTimeout(cleanupModalArtifacts, 450);
            } else {
                cleanupModalArtifacts();
            }
        });

        // Handler untuk tombol Simpan Sparepart
        $(document).on('click', '#saveSparepartButton', function(e) {
            e.preventDefault();

            // Validasi form sparepart
            if (!$('#sparepart_id').val()) {
                OptimaPro.showNotification('Harap pilih Sparepart.', 'warning');
                return;
            }
            if (!$('#sparepart_qty').val()) {
                OptimaPro.showNotification('Harap masukkan Quantity.', 'warning');
                return;
            }
            if (!$('#sparepart_satuan').val()) {
                OptimaPro.showNotification('Harap pilih Satuan.', 'warning');
                return;
            }

            const sparepartData = {
                item_type: 'sparepart',
                sparepart_id: $('#sparepart_id').val(), // Ambil ID
                name_text: $('#sparepart_id option:selected').text(), // Ambil Teks
                qty: $('#sparepart_qty').val(),
                satuan: $('#sparepart_satuan').val(),
                keterangan: $('#sparepart_keterangan').val()
            };
            
            items.push(sparepartData);
            displayItems();
            const modalEl = document.getElementById('itemModal');
            const inst = bootstrap.Modal.getInstance(modalEl);
            if (inst) {
                $('#itemModal .select2-basic').each(function(){ if($(this).data('select2')) $(this).select2('close'); });
                inst.hide();
                modalEl.addEventListener('hidden.bs.modal', function handler(){
                    cleanupModalArtifacts();
                    modalEl.removeEventListener('hidden.bs.modal', handler);
                });
                setTimeout(cleanupModalArtifacts, 450);
            } else {
                cleanupModalArtifacts();
            }
        });
        
        // Tampilkan tabel (kosong) saat halaman pertama kali dimuat
        displayItems();

        // DELETE: Handler untuk tombol hapus item
        $(document).on('click', '.delete-item-row', function() {
            OptimaPro.showConfirmDialog({
                title: 'Konfirmasi Hapus',
                message: 'Apakah Anda yakin ingin menghapus item ini?'
            }).then(result => {
                if (result.isConfirmed) {
                    const index = $(this).closest('tr').data('index');
                    items.splice(index, 1); // Hapus item dari array
                    displayItems(); // Gambar ulang tabel
                    OptimaPro.showNotification('Item berhasil dihapus', 'success');
                }
            });
        });

        // Listener untuk menampilkan/menyembunyikan field Battery Type
        $(document).on('change', '#jenis_select', function() {
            // Ambil TEKS dari opsi yang dipilih dan ubah ke huruf besar
            const selectedJenisText = $(this).find('option:selected').text().toUpperCase();
            const batteryContainer = $('#battery_type_container');

            // Cek apakah teksnya adalah 'ELECTRIC'
            if (selectedJenisText.includes('ELECTRIC')) {
                batteryContainer.slideDown(); // Tampilkan dengan animasi
            } else {
                batteryContainer.slideUp(); // Sembunyikan dengan animasi
            }
        });

        // --- MAIN PO FORM SUBMISSION ---
        
        // Handler untuk submit form PO utama
        $('#formPoDinamis').on('submit', function(e) {
            
            // BARU: Validasi manual untuk field Supplier
            const supplierId = $('select[name="id_supplier"]').val();
            if (!supplierId) {
                e.preventDefault(); // Hentikan submit
                OptimaPro.showNotification('Silakan pilih Supplier terlebih dahulu!', 'warning');
                return;
            }

            if (items.length === 0) {
                e.preventDefault(); // Hentikan submit
                OptimaPro.showNotification('Silakan tambahkan minimal satu item PO terlebih dahulu!', 'warning');
                return;
            }

            // Kode di bawah ini hanya berjalan jika semua validasi lolos
            $(this).find('input[name="items_json"]').remove();
            $(this).append(`<input type="hidden" name="items_json" value='${JSON.stringify(items)}'>`);
        });
    });
</script>

<?= $this->endSection() ?>