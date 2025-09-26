<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    /* Variabel Warna untuk Tema Clean */
    :root {
        --bs-primary-rgb: 78, 115, 223; /* Warna primer Bootstrap default */
        --bs-light-gray: #f8f9fc;
        --bs-border-color: #e3e6f0;
        --bs-card-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
        --bs-card-border-radius: 0.5rem; /* Radius yang lebih halus */
    }

    /* Efek halus untuk semua transisi */
    .card, .btn, .nav-link {
        transition: all 0.2s ease-in-out;
    }

    .card-stats:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
    .table-card, .card-stats { border: none; border-radius: 15px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1); }
    .modal-header { background: linear-gradient(135deg, #e9ecef 0%, #e9ecef 100%); color: white; border-radius: 15px 15px 0 0; }
    .filter-card.active { 
        transform: translateY(-3px); 
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2); 
        border: 2px solid #fff; 
    }
    .filter-card:hover { 
        transform: translateY(-5px); 
        box-shadow: 0 10px 35px rgba(0, 0, 0, 0.25); 
    }
    
    .btn:disabled {
        opacity: 0.6;
        cursor: not-allowed;
    }
    
    /* Customer Code Column - Make it narrower */
    #customerTable th:first-child,
    #customerTable td:first-child {
        width: 120px;
        max-width: 120px;
    }
    
    .customer-code {
        font-family: 'Courier New', monospace;
        font-weight: 600;
        font-size: 0.8rem;
        color: #495057;
        text-align: center;
        background: #f8f9fa;
        padding: 0.3rem 0.5rem;
        border-radius: 0.25rem;
        display: inline-block;
    }
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<!-- Statistics Cards -->

<div class="row g-4 mb-4">
<div class="col-xl-3 col-md-6"><div class="card card-stats bg-primary text-white h-100 filter-card" data-filter="all" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-total-customers"><?= $totalCustomers ?></h2><h6 class="card-title text-uppercase small">Total Customers</h6></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card card-stats bg-success text-white h-100 filter-card" data-filter="active" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-active-customers">0</h2><h6 class="card-title text-uppercase small">Active Customers</h6></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card card-stats bg-warning text-white h-100 filter-card" data-filter="locations" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-total-locations"><?= $totalLocations ?></h2><h6 class="card-title text-uppercase small">Total Locations</h6></div></div></div>
<div class="col-xl-3 col-md-6"><div class="card card-stats bg-info text-white h-100 filter-card" data-filter="areas" style="cursor: pointer;"><div class="card-body"><h2 class="fw-bold mb-1" id="stat-coverage-areas"><?= count($areas) ?></h2><h6 class="card-title text-uppercase small">Coverage Areas</h6></div></div></div>
</div>

<div class="card shadow">
    <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Customer</h6>
        <button class="btn btn-primary btn-sm" onclick="showAddCustomerModal()">
            <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Customer
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="customerTable">
                <thead>
                    <tr>
                        <th>Customer Code</th>
                        <th>Customer Name</th>
                        <th>Locations Summary</th>
                        <th>Primary Contact</th>
                        <th>Contracts/PO</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
        </div>
    </div>
</div>  

<div class="modal fade" id="addCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addCustomerForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_code">Customer Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_code" name="customer_code" required maxlength="20">
                                <small class="form-text text-muted">Unique customer identifier</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer_name">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="customer_name" name="customer_name" required maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="area_id">Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="area_id" name="area_id" required>
                                    <option value="">Select Area</option>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>"><?= $area['area_code'] ?> - <?= $area['area_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="text" class="form-control" id="phone" name="phone" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" class="form-control" id="email" name="email" maxlength="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Primary Address</label>
                        <textarea class="form-control" id="address" name="address" rows="3" maxlength="500"></textarea>
                        <small class="form-text text-muted">This will be created as the primary location</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="editCustomerForm">
                <input type="hidden" id="edit_customer_id" name="customer_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_customer_code">Customer Code <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_customer_code" name="customer_code" required maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_customer_name">Customer Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_customer_name" name="customer_name" required maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_area_id">Area <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_area_id" name="area_id" required>
                                    <option value="">Select Area</option>
                                    <?php foreach ($areas as $area): ?>
                                        <option value="<?= $area['id'] ?>"><?= $area['area_code'] ?> - <?= $area['area_name'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_contact_person">Contact Person</label>
                                <input type="text" class="form-control" id="edit_contact_person" name="contact_person" maxlength="255">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_phone">Phone</label>
                                <input type="text" class="form-control" id="edit_phone" name="phone" maxlength="20">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit_email">Email</label>
                                <input type="email" class="form-control" id="edit_email" name="email" maxlength="100">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_address">Address</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3" maxlength="500"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="edit_description">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Customer Modal -->
<div class="modal fade" id="viewCustomerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="customerBasicInfo"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" onclick="editCustomerFromView()">
                    <i class="fas fa-edit me-1"></i>Edit Customer
                </button>
                <button type="button" class="btn btn-success" onclick="manageCustomerLocations()">
                    <i class="fas fa-map-marker-alt me-1"></i>Manage Locations
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteCustomerFromView()">
                    <i class="fas fa-trash me-1"></i>Delete Customer
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Manage Locations Modal -->
<div class="modal fade" id="manageLocationsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Customer Locations</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="location_customer_id" name="customer_id">
                
                <!-- Add Location Button -->
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h6>Customer Locations</h6>
                    <button class="btn btn-primary btn-sm" onclick="showAddLocationForm()">
                        <i class="fas fa-plus me-1"></i>Add New Location
                    </button>
                </div>
                
                <!-- Location Form (Hidden by default) -->
                <div class="card mb-3" id="locationFormCard" style="display: none;">
                    <div class="card-header">
                        <h6 id="locationFormTitle">Add New Location</h6>
                    </div>
                    <div class="card-body">
                        <form id="locationForm">
                            <input type="hidden" id="location_id" name="location_id">
                            <input type="hidden" id="form_customer_id" name="customer_id">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location_name">Location Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="location_name" name="location_name" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="is_primary">Location Type</label>
                                        <select class="form-control" id="is_primary" name="is_primary">
                                            <option value="0">Secondary Location</option>
                                            <option value="1">Primary Location</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="location_address">Address <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="location_address" name="address" rows="3" required></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location_pic_name">PIC Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="location_pic_name" name="contact_person" required>
                                        <small class="text-muted">Person in charge for this location</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location_pic_phone">PIC Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="location_pic_phone" name="phone" required>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location_pic_email">PIC Email</label>
                                        <input type="email" class="form-control" id="location_pic_email" name="email">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label for="location_pic_position">PIC Position</label>
                                        <input type="text" class="form-control" id="location_pic_position" name="pic_position">
                                        <small class="text-muted">e.g., Site Manager, Warehouse Supervisor</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="location_notes">Notes</label>
                                <textarea class="form-control" id="location_notes" name="notes" rows="2"></textarea>
                            </div>
                            
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-save me-1"></i>Save Location
                                </button>
                                <button type="button" class="btn btn-secondary" onclick="hideLocationForm()">
                                    <i class="fas fa-times me-1"></i>Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Locations List -->
                <div id="locationsTable">
                    <!-- Locations will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
let customerTable;
let customersChart;

$(document).ready(function() {
    initializeCustomerTable();
    bindFormEvents();
    bindFilterEvents();
});

function bindFilterEvents() {
    $('.filter-card').on('click', function() {
        const filter = $(this).data('filter');
        
        // Remove active class from all cards
        $('.filter-card').removeClass('active-filter');
        // Add active class to clicked card
        $(this).addClass('active-filter');
        
        // Apply filter to table
        applyFilter(filter);
    });
}

function applyFilter(filter) {
    if (filter === 'all') {
        customerTable.search('').draw();
    } else if (filter === 'active') {
        // Filter for customers with recent activity
        customerTable.search('active').draw();
    } else if (filter === 'locations') {
        // Filter for customers with multiple locations
        customerTable.search('location').draw();
    } else if (filter === 'areas') {
        // Filter by specific areas
        customerTable.search('area').draw();
    }
}

function initializeCustomerTable() {
    console.log('Initializing customer table...');
    customerTable = $('#customerTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            url: '<?= base_url('marketing/customer-management/getCustomers') ?>',
            type: 'POST',
            error: function(xhr, error, thrown) {
                console.error('DataTable AJAX Error:', error);
                console.error('Response:', xhr.responseText);
            }
        },
        columns: [
            { 
                data: 'customer_code', 
                name: 'customer_code',
                width: '120px',
                render: function(data, type, row) {
                    return `<span class="customer-code">${data}</span>`;
                }
            },
            { 
                data: 'customer_name', 
                name: 'customer_name',
                render: function(data, type, row) {
                    const status = row.is_active == 1 ? 'ACTIVE' : 'INACTIVE';
                    const statusClass = row.is_active == 1 ? 'text-success' : 'text-danger';
                    return `
                        <div class="customer-name">${data}</div>
                        <span class="customer-status ${statusClass}">${status}</span>
                    `;
                }
            },
            { 
                data: 'locations_count', 
                name: 'locations_count',
                orderable: false,
                render: function(data, type, row) {
                    const locCount = data || 0;
                    const totalUnits = row.total_units || 0;
                    
                    if (locCount === 0) {
                        return '<div class="text-center text-muted">No locations</div>';
                    }
                    
                    let summary = `<div class="text-center">`;
                    summary += `<div><strong>${locCount}</strong> location${locCount > 1 ? 's' : ''}</div>`;
                    if (totalUnits > 0) {
                        summary += `<small class="text-muted">${totalUnits} units</small>`;
                    }
                    summary += `</div>`;
                    
                    return summary;
                }
            },
            { 
                data: 'pic_name', 
                name: 'pic_name',
                orderable: false,
                render: function(data, type, row) {
                    const name = data || 'TBA';
                    const phone = row.pic_phone || '';
                    
                    if (phone) {
                        return `${name}<br><small class="text-muted">${phone}</small>`;
                    }
                    return name;
                }
            },
            { 
                data: 'contracts_count', 
                name: 'contracts_count',
                orderable: false,
                render: function(data, type, row) {
                    const contractCount = data || 0;
                    const poCount = row.po_count || 0;
                    
                    let result = '<div class="text-center">';
                    
                    if (contractCount > 0 || poCount > 0) {
                        if (contractCount > 0) {
                            result += `<div>${contractCount} Contract${contractCount > 1 ? 's' : ''}</div>`;
                        }
                        if (poCount > 0) {
                            result += `<div class="text-muted">${poCount} PO${poCount > 1 ? 's' : ''}</div>`;
                        }
                    } else {
                        result += '<span class="text-muted">No contracts</span>';
                    }
                    
                    result += '</div>';
                    return result;
                }
            },
            { 
                data: 'id', 
                name: 'manage', 
                orderable: false, 
                searchable: false,
                width: '100px',
                render: function(data, type, row) {
                    return `<div class="text-center">
                        <button class="btn btn-primary btn-sm" onclick="manageLocations(${data})" title="Manage Customer Locations">
                            <i class="fas fa-cog me-1"></i>Locations
                        </button>
                    </div>`;
                }
            }
        ],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        order: [[1, 'asc']],
        rowCallback: function(row, data) {
            // Add click event to row (except manage button column)
            $(row).css('cursor', 'pointer');
            $(row).off('click').on('click', function(e) {
                // Don't trigger row click if manage button was clicked
                if (!$(e.target).closest('.btn').length) {
                    viewCustomer(data.id);
                }
            });
        }
    });
}

function bindFormEvents() {
    // Add Customer Form
    $('#addCustomerForm').on('submit', function(e) {
        e.preventDefault();
        
        $.ajax({
            url: '<?= base_url('marketing/customer-management/store') ?>',
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                showLoadingButton('#addCustomerForm button[type="submit"]');
            },
            success: function(response) {
                hideLoadingButton('#addCustomerForm button[type="submit"]', 'Save Customer');
                
                if (response.success) {
                    $('#addCustomerModal').modal('hide');
                    showAlert('success', response.message);
                    customerTable.ajax.reload();
                    $('#addCustomerForm')[0].reset();
                } else {
                    showAlert('error', response.message);
                    if (response.errors) {
                        showValidationErrors(response.errors);
                    }
                }
            },
            error: function() {
                hideLoadingButton('#addCustomerForm button[type="submit"]', 'Save Customer');
                showAlert('error', 'Error saving customer');
            }
        });
    });
    
    // Edit Customer Form
    $('#editCustomerForm').on('submit', function(e) {
        e.preventDefault();
        
        const customerId = $('#edit_customer_id').val();
        
        $.ajax({
            url: `<?= base_url('marketing/customer-management/update') ?>/${customerId}`,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                showLoadingButton('#editCustomerForm button[type="submit"]');
            },
            success: function(response) {
                hideLoadingButton('#editCustomerForm button[type="submit"]', 'Update Customer');
                
                if (response.success) {
                    $('#editCustomerModal').modal('hide');
                    showAlert('success', response.message);
                    customerTable.ajax.reload();
                } else {
                    showAlert('error', response.message);
                    if (response.errors) {
                        showValidationErrors(response.errors);
                    }
                }
            },
            error: function() {
                hideLoadingButton('#editCustomerForm button[type="submit"]', 'Update Customer');
                showAlert('error', 'Error updating customer');
            }
        });
    });
    
    // Location Form
    $('#locationForm').on('submit', function(e) {
        e.preventDefault();
        
        const locationId = $('#location_id').val();
        const url = locationId ? 
            `<?= base_url('marketing/customer-management/updateLocation') ?>/${locationId}` : 
            '<?= base_url('marketing/customer-management/storeLocation') ?>';
        
        $.ajax({
            url: url,
            type: 'POST',
            data: $(this).serialize(),
            dataType: 'json',
            beforeSend: function() {
                showLoadingButton('#locationForm button[type="submit"]');
            },
            success: function(response) {
                hideLoadingButton('#locationForm button[type="submit"]', 'Save Location');
                
                if (response.success) {
                    showAlert('success', response.message);
                    loadCustomerLocations($('#location_customer_id').val());
                    hideLocationForm();
                } else {
                    showAlert('error', response.message);
                    if (response.errors) {
                        showValidationErrors(response.errors);
                    }
                }
            },
            error: function() {
                hideLoadingButton('#locationForm button[type="submit"]', 'Save Location');
                showAlert('error', 'Error saving location');
            }
        });
    });
}

// Customer Management Functions
function showAddCustomerModal() {
    $('#addCustomerForm')[0].reset();
    $('#addCustomerModal').modal('show');
}

function viewCustomer(id) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/showCustomer') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#customerBasicInfo').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
        },
        success: function(response) {
            if (response.success) {
                displayEnhancedCustomerDetails(response.data);
                $('#viewCustomerModal').modal('show');
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'Error loading customer details');
        }
    });
}

function editCustomer(id) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/showCustomer') ?>/${id}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const customer = response.data.customer;
                
                $('#edit_customer_id').val(customer.id);
                $('#edit_customer_code').val(customer.customer_code);
                $('#edit_customer_name').val(customer.customer_name);
                $('#edit_area_id').val(customer.area_id);
                $('#edit_contact_person').val(customer.contact_person);
                $('#edit_phone').val(customer.phone);
                $('#edit_email').val(customer.email);
                $('#edit_address').val(customer.address);
                $('#edit_description').val(customer.description);
                
                $('#editCustomerModal').modal('show');
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'Error loading customer data');
        }
    });
}

function deleteCustomer(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `<?= base_url('marketing/customer-management/delete') ?>/${id}`,
                type: 'DELETE',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        customerTable.ajax.reload();
                    } else {
                        showAlert('error', response.message);
                    }
                },
                error: function() {
                    showAlert('error', 'Error deleting customer');
                }
            });
        }
    });
}

// Location Management Functions
function manageLocations(customerId) {
    $('#location_customer_id').val(customerId);
    loadCustomerLocations(customerId);
    $('#manageLocationsModal').modal('show');
}

function loadCustomerLocations(customerId) {
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getLocations') ?>/${customerId}`,
        type: 'GET',
        dataType: 'json',
        beforeSend: function() {
            $('#locationsTable').html('<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</div>');
        },
        success: function(response) {
            if (response.success) {
                displayLocationsTable(response.data);
            } else {
                $('#locationsTable').html('<div class="alert alert-warning">No locations found</div>');
            }
        },
        error: function() {
            $('#locationsTable').html('<div class="alert alert-danger">Error loading locations</div>');
        }
    });
}

function displayLocationsTable(locations) {
    let html = `<div class="table-responsive">
        <table class="table table-striped table-hover">
            <thead class="bg-light">
                <tr>
                    <th>Location Name</th>
                    <th>Address</th>
                    <th>PIC Information</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>`;
    
    if (locations && locations.length > 0) {
        locations.forEach(function(location) {
            const picInfo = location.contact_person ? 
                `<strong>${location.contact_person}</strong><br>
                 <small class="text-muted">
                    ${location.phone ? '<i class="fas fa-phone"></i> ' + location.phone : ''}
                    ${location.phone && location.email ? '<br>' : ''}
                    ${location.email ? '<i class="fas fa-envelope"></i> ' + location.email : ''}
                    ${location.pic_position ? '<br><i class="fas fa-user-tie"></i> ' + location.pic_position : ''}
                 </small>` : 
                '<span class="text-muted">No PIC assigned</span>';
            
            html += `<tr>
                <td>
                    <strong>${location.location_name || 'N/A'}</strong>
                    ${location.is_primary == 1 ? '<span class="badge bg-primary ms-2">Primary</span>' : ''}
                </td>
                <td>
                    <small class="text-muted">${location.address || 'N/A'}</small>
                    ${location.notes ? '<br><small class="text-info"><i class="fas fa-sticky-note"></i> ' + location.notes + '</small>' : ''}
                </td>
                <td>${picInfo}</td>
                <td><span class="badge ${location.is_primary == 1 ? 'bg-primary' : 'bg-secondary'}">${location.is_primary == 1 ? 'Primary' : 'Secondary'}</span></td>
                <td><span class="badge ${location.is_active == 1 ? 'bg-success' : 'bg-secondary'}">${location.is_active == 1 ? 'Active' : 'Inactive'}</span></td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-warning" onclick="editLocation(${location.id})" title="Edit Location">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-outline-danger" onclick="deleteLocation(${location.id})" title="Delete Location">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>`;
        });
    } else {
        html += '<tr><td colspan="6" class="text-center text-muted py-4">No locations found for this customer</td></tr>';
    }
    
    html += '</tbody></table></div>';
    $('#locationsTable').html(html);
}

// Location Management Functions
function manageCustomerLocations() {
    if (currentCustomerId) {
        manageLocations(currentCustomerId);
    } else {
        showAlert('error', 'Customer ID not found');
    }
}

function showAddLocationForm() {
    $('#locationFormTitle').text('Add New Location');
    $('#locationForm')[0].reset();
    $('#location_id').val('');
    $('#form_customer_id').val($('#location_customer_id').val());
    $('#locationFormCard').show();
}

function editLocation(locationId) {
    // Load location data for editing
    $.ajax({
        url: `<?= base_url('marketing/customer-management/getLocation') ?>/${locationId}`,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                const location = response.data;
                $('#locationFormTitle').text('Edit Location');
                $('#location_id').val(location.id);
                $('#form_customer_id').val(location.customer_id);
                $('#location_name').val(location.location_name);
                $('#location_address').val(location.address);
                $('#location_pic_name').val(location.contact_person);
                $('#location_pic_phone').val(location.phone);
                $('#location_pic_email').val(location.email);
                $('#location_pic_position').val(location.pic_position);
                $('#location_notes').val(location.notes);
                $('#is_primary').val(location.is_primary);
                $('#locationFormCard').show();
            } else {
                showAlert('error', response.message);
            }
        },
        error: function() {
            showAlert('error', 'Error loading location data');
        }
    });
}

function deleteLocation(locationId) {
    if (confirm('Are you sure you want to delete this location?')) {
        $.ajax({
            url: `<?= base_url('marketing/customer-management/deleteLocation') ?>/${locationId}`,
            type: 'DELETE',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    loadCustomerLocations($('#location_customer_id').val());
                } else {
                    showAlert('error', response.message);
                }
            },
            error: function() {
                showAlert('error', 'Error deleting location');
            }
        });
    }
}

function hideLocationForm() {
    $('#locationFormCard').hide();
}

function displayEnhancedCustomerDetails(customer) {
    const html = `
        <div class="row">
            <div class="col-md-6">
                <h6>Basic Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Customer Code:</strong></td><td>${customer.customer_code || 'N/A'}</td></tr>
                    <tr><td><strong>Customer Name:</strong></td><td>${customer.customer_name || 'N/A'}</td></tr>
                    <tr><td><strong>Area:</strong></td><td>${customer.area_name || 'N/A'}</td></tr>
                    <tr><td><strong>Status:</strong></td><td><span class="badge ${customer.is_active == 1 ? 'bg-success' : 'bg-secondary'}">${customer.is_active == 1 ? 'Active' : 'Inactive'}</span></td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6>Contact Information</h6>
                <table class="table table-sm">
                    <tr><td><strong>Contact Person:</strong></td><td>${customer.pic_name || 'TBA'}</td></tr>
                    <tr><td><strong>Phone:</strong></td><td>${customer.pic_phone || 'N/A'}</td></tr>
                    <tr><td><strong>Email:</strong></td><td>${customer.pic_email || 'N/A'}</td></tr>
                    <tr><td><strong>Address:</strong></td><td>${customer.address || 'N/A'}</td></tr>
                </table>
            </div>
        </div>
    `;
    $('#customerBasicInfo').html(html);
}

function showAlert(type, message) {
    // Use OptimaPro notification system if available
    if (window.OptimaPro && typeof OptimaPro.showNotification === 'function') {
        OptimaPro.showNotification(message, type, 5000);
    } else {
        // Fallback to simple alert for compatibility
        const alertClass = type === 'success' ? 'alert-success' : type === 'error' ? 'alert-danger' : 'alert-info';
        const alertHtml = `<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        
        // Add to top of page
        $('body').prepend(alertHtml);
        
        // Auto dismiss after 3 seconds
        setTimeout(function() {
            $('.alert').alert('close');
        }, 3000);
    }
}

function showLoadingButton(selector) {
    $(selector).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Loading...');
}

function hideLoadingButton(selector, originalText) {
    $(selector).prop('disabled', false).html(originalText);
}

// Additional Functions (placeholders for future implementation)
function showBulkImportModal() {
    // Implementation for bulk import
    showAlert('info', 'Bulk import feature coming soon');
}

function exportCustomers() {
    // Implementation for export
    showAlert('info', 'Export feature coming soon');
}

function showCustomerReports() {
    // Implementation for reports
    showAlert('info', 'Reports feature coming soon');
}

function showAreaAssignments() {
    // Implementation for area assignments view
    showAlert('info', 'Area assignments view coming soon');
}

// Enhanced Customer Detail Functions
let currentCustomerId = null;

function editCustomerFromView() {
    if (currentCustomerId) {
        $('#viewCustomerModal').modal('hide');
        editCustomer(currentCustomerId);
    }
}

function deleteCustomerFromView() {
    if (currentCustomerId) {
        $('#viewCustomerModal').modal('hide');
        deleteCustomer(currentCustomerId);
    }
}

function displayEnhancedCustomerDetails(data) {
    const customer = data.customer;
    const locations = data.locations || [];
    const contracts = data.contracts || [];
    
    currentCustomerId = customer.id;
    
    // Update modal title
    $('.modal-title').html(`<i class="fas fa-building"></i> ${customer.customer_name}`);
    
    // Basic Info Tab
    let basicInfo = `
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Company Information</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td><strong>Customer Code:</strong></td><td>${customer.customer_code}</td></tr>
                            <tr><td><strong>Company Name:</strong></td><td>${customer.customer_name}</td></tr>
                            <tr><td><strong>Status:</strong></td><td>
                                ${customer.is_active ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'}
                            </td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title">Primary Contact</h6>
                        <table class="table table-sm table-borderless">
                            <tr><td><strong>PIC Name:</strong></td><td>${customer.pic_name || 'Not set'}</td></tr>
                            <tr><td><strong>Phone:</strong></td><td>${customer.pic_phone || 'Not set'}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${customer.pic_email || 'Not set'}</td></tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    $('#customerBasicInfo').html(basicInfo);
    
    // Locations Tab
    displayLocationsList(locations);
    
    // Contracts Tab  
    displayContractsList(contracts);
}

function displayLocationsList(locations) {
    let html = '';
    
    if (locations.length > 0) {
        html = `<div class="row">`;
        locations.forEach((location, index) => {
            const isPrimary = location.is_primary ? '<span class="badge bg-primary ms-2">Primary</span>' : '';
            html += `
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">${location.location_name}${isPrimary}</h6>
                            <p class="card-text">
                                <i class="fas fa-map-marker-alt text-muted"></i> ${location.address}<br>
                                <i class="fas fa-user text-muted"></i> ${location.contact_person || 'No contact'}<br>
                                <i class="fas fa-phone text-muted"></i> ${location.phone || 'No phone'}
                            </p>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-warning" onclick="editLocation(${location.id})">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn btn-outline-danger" onclick="deleteLocation(${location.id})">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        html += `</div>`;
    } else {
        html = '<div class="text-center text-muted py-4">No locations found for this customer.</div>';
    }
    
    $('#customerLocationsList').html(html);
}

function displayContractsList(contracts) {
    let html = '';
    
    if (contracts.length > 0) {
        html = '<div class="table-responsive"><table class="table table-hover"><thead><tr><th>Contract ID</th><th>Type</th><th>Status</th><th>Start Date</th><th>End Date</th><th>Action</th></tr></thead><tbody>';
        contracts.forEach(contract => {
            html += `
                <tr>
                    <td><strong>#${contract.contract_id || 'N/A'}</strong></td>
                    <td>${contract.contract_type || 'Standard'}</td>
                    <td><span class="badge bg-info">${contract.status || 'Active'}</span></td>
                    <td>${contract.start_date || 'N/A'}</td>
                    <td>${contract.end_date || 'N/A'}</td>
                    <td><button class="btn btn-sm btn-outline-primary">View Details</button></td>
                </tr>
            `;
        });
        html += '</tbody></table></div>';
    } else {
        html = '<div class="text-center text-muted py-4">No contracts found for this customer.</div>';
    }
    
    $('#customerContractsList').html(html);
}
</script>
<?= $this->endSection() ?>