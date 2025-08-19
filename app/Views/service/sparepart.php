<?php
// Sample data for spareparts
$totalSpareparts = 156;
$lowStockParts = 23;
$outOfStockParts = 8;
$availableParts = 125;

$spareparts = [
    ['id' => 1, 'part_number' => 'FL-OIL-001', 'name' => 'Engine Oil Filter', 'category' => 'Engine', 'brand' => 'Toyota', 'stock' => 45, 'min_stock' => 10, 'unit_price' => 85000, 'supplier' => 'PT Spare Parts Indonesia'],
    ['id' => 2, 'part_number' => 'FL-BRK-002', 'name' => 'Brake Pad Set', 'category' => 'Brake', 'brand' => 'Mitsubishi', 'stock' => 8, 'min_stock' => 15, 'unit_price' => 350000, 'supplier' => 'CV Otomotif Jaya'],
    ['id' => 3, 'part_number' => 'FL-HYD-003', 'name' => 'Hydraulic Hose', 'category' => 'Hydraulic', 'brand' => 'Komatsu', 'stock' => 0, 'min_stock' => 5, 'unit_price' => 275000, 'supplier' => 'UD Mekanik Sejahtera'],
    ['id' => 4, 'part_number' => 'CR-CHA-001', 'name' => 'Chain Assembly', 'category' => 'Lifting', 'brand' => 'Kato', 'stock' => 12, 'min_stock' => 8, 'unit_price' => 1250000, 'supplier' => 'PT Heavy Equipment Parts'],
    ['id' => 5, 'part_number' => 'FL-TIR-004', 'name' => 'Forklift Tire', 'category' => 'Tire', 'brand' => 'Universal', 'stock' => 28, 'min_stock' => 20, 'unit_price' => 450000, 'supplier' => 'Toko Ban Forklift'],
];

function getStockStatusBadge($stock, $minStock) {
    if ($stock == 0) {
        return '<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Out of Stock</span>';
    } elseif ($stock <= $minStock) {
        return '<span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle me-1"></i>Low Stock</span>';
    } else {
        return '<span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>In Stock</span>';
    }
}

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}
?>

<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2 text-gradient fw-bold">Sparepart Management</h1>
        <p class="text-muted mb-0">Manage inventory and stock levels efficiently</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-info btn-pro" data-bs-toggle="modal" data-bs-target="#stockReportModal">
            <i class="fas fa-chart-bar me-2"></i>Stock Report
        </button>
        <button class="btn btn-outline-primary btn-pro" data-bs-toggle="modal" data-bs-target="#exportModal">
            <i class="fas fa-download me-2"></i>Export
        </button>
        <button class="btn btn-primary btn-pro" data-bs-toggle="modal" data-bs-target="#addSparepartModal">
            <i class="fas fa-plus me-2"></i>Add Sparepart
        </button>
    </div>
</div>

<!-- Stats Cards Row -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">Total Parts</div>
                        <div class="pro-stats-value"><?= $totalSpareparts; ?></div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-arrow-up me-1"></i>+8% this month
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-primary">
                            <i class="fas fa-cogs"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">Available</div>
                        <div class="pro-stats-value text-success"><?= $availableParts; ?></div>
                        <div class="pro-stats-change text-info">
                            <i class="fas fa-check-circle me-1"></i>In stock
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">Low Stock</div>
                        <div class="pro-stats-value text-warning"><?= $lowStockParts; ?></div>
                        <div class="pro-stats-change text-warning">
                            <i class="fas fa-exclamation-triangle me-1"></i>Needs reorder
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="pro-stats-card">
            <div class="pro-stats-card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="pro-stats-label">Out of Stock</div>
                        <div class="pro-stats-value text-danger"><?= $outOfStockParts; ?></div>
                        <div class="pro-stats-change text-danger">
                            <i class="fas fa-times-circle me-1"></i>Urgent action needed
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="pro-stats-icon bg-danger">
                            <i class="fas fa-times-circle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Sparepart Table using consistent template -->
<?php
// Configure table settings
$table_id = 'sparepartsTable';
$table_title = 'Spareparts Inventory';
$table_subtitle = 'Complete parts catalog with stock levels';

// Configure filters
$filters = [
    [
        'type' => 'select',
        'name' => 'category',
        'label' => 'Category',
        'placeholder' => 'All Categories',
        'col_size' => '3',
        'options' => [
            'Engine' => 'Engine',
            'Brake' => 'Brake',
            'Hydraulic' => 'Hydraulic',
            'Lifting' => 'Lifting',
            'Tire' => 'Tire',
            'Electrical' => 'Electrical'
        ]
    ],
    [
        'type' => 'select',
        'name' => 'brand',
        'label' => 'Brand',
        'placeholder' => 'All Brands',
        'col_size' => '3',
        'options' => [
            'Toyota' => 'Toyota',
            'Mitsubishi' => 'Mitsubishi',
            'Komatsu' => 'Komatsu',
            'Kato' => 'Kato',
            'Universal' => 'Universal'
        ]
    ],
    [
        'type' => 'select',
        'name' => 'stock_status',
        'label' => 'Stock Status',
        'placeholder' => 'All Stock',
        'col_size' => '3',
        'options' => [
            'available' => 'In Stock',
            'low' => 'Low Stock',
            'out' => 'Out of Stock'
        ]
    ]
];

// Configure actions
$actions = [
    ['type' => 'filter', 'label' => 'Filters'],
];

// Configure columns
$columns = [
    [
        'label' => 'Part Number',
        'type' => 'text',
        'field' => 'part_number',
        'width' => '130px'
    ],
    [
        'label' => 'Name & Details',
        'type' => 'avatar',
        'field' => 'id',
        'avatar_field' => 'category_icon',
        'avatar_class' => 'bg-primary',
        'avatar_icon' => 'fas fa-cog',
        'title_field' => 'name',
        'subtitle_field' => 'brand_category'
    ],
    [
        'label' => 'Category',
        'type' => 'badge',
        'field' => 'category',
        'badge_class' => 'status-active'
    ],
    [
        'label' => 'Stock',
        'type' => 'text',
        'field' => 'stock_display'
    ],
    [
        'label' => 'Status',
        'type' => 'badge',
        'field' => 'status_display',
        'badge_class' => 'status-active'
    ],
    [
        'label' => 'Unit Price',
        'type' => 'currency',
        'field' => 'unit_price',
        'currency_symbol' => 'Rp '
    ],
    [
        'label' => 'Supplier',
        'type' => 'text',
        'field' => 'supplier',
        'width' => '150px'
    ]
];

// Transform spareparts data for template
$sparepartsData = [];
foreach ($spareparts as $part) {
    // Determine status
    $status = 'In Stock';
    $status_class = 'status-active';
    if ($part['stock'] == 0) {
        $status = 'Out of Stock';
        $status_class = 'status-inactive';
    } elseif ($part['stock'] <= $part['min_stock']) {
        $status = 'Low Stock';
        $status_class = 'status-pending';
    }
    
    // Get category icon
    $category_icons = [
        'Engine' => 'EN',
        'Brake' => 'BR',
        'Hydraulic' => 'HY',
        'Lifting' => 'LI',
        'Tire' => 'TI',
        'Electrical' => 'EL'
    ];
    
    $sparepartsData[] = [
        'id' => $part['id'],
        'part_number' => $part['part_number'],
        'category_icon' => $category_icons[$part['category']] ?? 'SP',
        'name' => $part['name'],
        'brand_category' => $part['brand'] . ' • ' . $part['category'],
        'category' => $part['category'],
        'stock_display' => $part['stock'] . ' (Min: ' . $part['min_stock'] . ')',
        'status_display' => $status,
        'unit_price' => $part['unit_price'],
        'supplier' => $part['supplier']
    ];
}

// Pass the transformed data to template
$data = $sparepartsData;

// Include the template
include APPPATH . 'Views/_partials/datatable_template.php';
?>

<!-- Custom Action Buttons Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Override default action buttons for sparepart-specific actions
    const actionCells = document.querySelectorAll('#<?= $table_id ?> .pro-datatable-actions-cell');
    
    actionCells.forEach((cell, index) => {
        const partId = <?= json_encode(array_column($spareparts, 'id')) ?>[index];
        
        cell.innerHTML = `
            <div class="pro-datatable-btn-group">
                <button class="pro-datatable-btn pro-datatable-btn-view" onclick="viewSparepart(${partId})" data-bs-toggle="tooltip" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                <button class="pro-datatable-btn pro-datatable-btn-success" onclick="adjustStock(${partId})" data-bs-toggle="tooltip" title="Adjust Stock">
                                        <i class="fas fa-plus-minus"></i>
                                    </button>
                <button class="pro-datatable-btn pro-datatable-btn-edit" onclick="editSparepart(${partId})" data-bs-toggle="tooltip" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                <button class="pro-datatable-btn pro-datatable-btn-delete" onclick="deleteSparepart(${partId})" data-bs-toggle="tooltip" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
        `;
    });
    
    // Re-initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Sparepart action functions
function viewSparepart(id) {
    console.log('View sparepart:', id);
    // Add your view logic here
}

function adjustStock(id) {
    console.log('Adjust stock for sparepart:', id);
    // Add your stock adjustment logic here
}

function editSparepart(id) {
    console.log('Edit sparepart:', id);
    // Add your edit logic here
}

function deleteSparepart(id) {
    console.log('Delete sparepart:', id);
    // Add your delete logic here
}
</script>

<!-- Add Sparepart Modal -->
<div class="modal fade" id="addSparepartModal" tabindex="-1" aria-labelledby="addSparepartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSparepartModalLabel">Add New Sparepart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addSparepartForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="partNumber" class="form-label">Part Number</label>
                            <input type="text" class="form-control" id="partNumber" name="part_number" placeholder="e.g., FL-OIL-001" required>
                            <div class="invalid-feedback">Please provide a valid part number.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="partName" class="form-label">Part Name</label>
                            <input type="text" class="form-control" id="partName" name="name" placeholder="e.g., Engine Oil Filter" required>
                            <div class="invalid-feedback">Please provide a part name.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="category" class="form-label">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Engine">Engine</option>
                                <option value="Brake">Brake</option>
                                <option value="Hydraulic">Hydraulic</option>
                                <option value="Lifting">Lifting</option>
                                <option value="Tire">Tire</option>
                                <option value="Electrical">Electrical</option>
                                <option value="Body">Body</option>
                            </select>
                            <div class="invalid-feedback">Please select a category.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="brand" class="form-label">Brand</label>
                            <select class="form-select" id="brand" name="brand" required>
                                <option value="">Select Brand</option>
                                <option value="Toyota">Toyota</option>
                                <option value="Mitsubishi">Mitsubishi</option>
                                <option value="Komatsu">Komatsu</option>
                                <option value="Kato">Kato</option>
                                <option value="Universal">Universal</option>
                                <option value="OEM">OEM</option>
                            </select>
                            <div class="invalid-feedback">Please select a brand.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="stock" class="form-label">Current Stock</label>
                            <input type="number" class="form-control" id="stock" name="stock" min="0" required>
                            <div class="invalid-feedback">Please provide current stock.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="minStock" class="form-label">Minimum Stock</label>
                            <input type="number" class="form-control" id="minStock" name="min_stock" min="1" required>
                            <div class="invalid-feedback">Please provide minimum stock level.</div>
                        </div>
                        <div class="col-md-4">
                            <label for="unitPrice" class="form-label">Unit Price (Rp)</label>
                            <input type="number" class="form-control" id="unitPrice" name="unit_price" min="0" step="1000" required>
                            <div class="invalid-feedback">Please provide unit price.</div>
                        </div>
                        <div class="col-12">
                            <label for="supplier" class="form-label">Supplier</label>
                            <input type="text" class="form-control" id="supplier" name="supplier" placeholder="e.g., PT Spare Parts Indonesia" required>
                            <div class="invalid-feedback">Please provide supplier name.</div>
                        </div>
                        <div class="col-12">
                            <label for="description" class="form-label">Description (Optional)</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Additional part description..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Sparepart</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stock Adjustment Modal -->
<div class="modal fade" id="stockAdjustmentModal" tabindex="-1" aria-labelledby="stockAdjustmentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="stockAdjustmentModalLabel">Stock Adjustment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="stockAdjustmentForm" class="needs-validation" novalidate>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Part Information</label>
                        <div id="partInfo" class="p-3 bg-light rounded">
                            <!-- Part info will be loaded here -->
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-6">
                            <label for="adjustmentType" class="form-label">Adjustment Type</label>
                            <select class="form-select" id="adjustmentType" name="adjustment_type" required>
                                <option value="">Select Type</option>
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label for="quantity" class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" min="1" required>
                        </div>
                        <div class="col-12">
                            <label for="reason" class="form-label">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Reason for stock adjustment..." required></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Stock</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">Export Spareparts Data</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Export Format</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="excel" value="excel" checked>
                        <label class="form-check-label" for="excel">
                            <i class="fas fa-file-excel text-success"></i> Excel (.xlsx)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="pdf" value="pdf">
                        <label class="form-check-label" for="pdf">
                            <i class="fas fa-file-pdf text-danger"></i> PDF (.pdf)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="exportFormat" id="csv" value="csv">
                        <label class="form-check-label" for="csv">
                            <i class="fas fa-file-csv text-info"></i> CSV (.csv)
                        </label>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="lowStockOnly" name="low_stock_only">
                    <label class="form-check-label" for="lowStockOnly">
                        Export only low stock and out of stock items
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="exportData()">Export</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection(); ?>

<?= $this->section('scripts'); ?>
<!-- DataTables CSS & JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">

<!-- DataTables JavaScript -->
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.print.min.js"></script>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- Additional Libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script>
let sparepartsTable;

$(document).ready(function() {
    // Initialize DataTable with Bootstrap Pro styling
    sparepartsTable = $('#sparepartsTable').DataTable({
        "responsive": true,
        "language": {
            "processing": '<div class="d-flex justify-content-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>',
            "emptyTable": '<div class="text-center py-4"><i class="fas fa-inbox fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No spareparts found</h5><p class="text-muted">Add your first sparepart to get started.</p></div>',
            "zeroRecords": '<div class="text-center py-4"><i class="fas fa-search fa-3x text-muted mb-3"></i><br><h5 class="text-muted">No matching records found</h5><p class="text-muted">Try adjusting your search or filter criteria.</p></div>',
            "lengthMenu": "Show _MENU_ entries",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries"
        },
        "pageLength": 25,
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "order": [[0, 'asc']],
        "columnDefs": [
            { "targets": [8], "orderable": false, "searchable": false, "className": "text-center" }
        ],
        "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
               '<"row"<"col-sm-12"tr>>' +
               '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        "buttons": [
            {
                extend: 'excel',
                className: 'btn btn-success btn-sm',
                text: '<i class="fas fa-file-excel"></i> Excel'
            },
            {
                extend: 'pdf',
                className: 'btn btn-danger btn-sm',
                text: '<i class="fas fa-file-pdf"></i> PDF'
            },
            {
                extend: 'csv',
                className: 'btn btn-info btn-sm',
                text: '<i class="fas fa-file-csv"></i> CSV'
            }
        ]
    });

    // Custom filters
    $('#categoryFilter').on('change', function() {
        let val = $.fn.dataTable.util.escapeRegex($(this).val());
        sparepartsTable.column(2).search(val ? val : '', true, false).draw();
    });

    $('#brandFilter').on('change', function() {
        let val = $.fn.dataTable.util.escapeRegex($(this).val());
        sparepartsTable.column(3).search(val ? val : '', true, false).draw();
    });

    $('#stockFilter').on('change', function() {
        let val = $(this).val();
        if (val === 'available') {
            sparepartsTable.column(5).search('In Stock', true, false).draw();
        } else if (val === 'low') {
            sparepartsTable.column(5).search('Low Stock', true, false).draw();
        } else if (val === 'out') {
            sparepartsTable.column(5).search('Out of Stock', true, false).draw();
        } else {
            sparepartsTable.column(5).search('', true, false).draw();
        }
    });

    $('#applyFilter').on('click', function() {
        sparepartsTable.draw();
    });

    $('#resetFilter').on('click', function() {
        $('#categoryFilter, #brandFilter, #stockFilter').val('');
        sparepartsTable.search('').columns().search('').draw();
    });

    // Form validation and submission
    $('#addSparepartForm').on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'Sparepart has been added successfully.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            $('#addSparepartModal').modal('hide');
            $(this).removeClass('was-validated')[0].reset();
        });
    });

    $('#stockAdjustmentForm').on('submit', function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            e.stopPropagation();
            $(this).addClass('was-validated');
            return;
        }

        Swal.fire({
            icon: 'success',
            title: 'Stock Updated!',
            text: 'Stock adjustment has been recorded successfully.',
            timer: 2000,
            showConfirmButton: false
        }).then(() => {
            $('#stockAdjustmentModal').modal('hide');
            $(this).removeClass('was-validated')[0].reset();
        });
    });

    // Reset forms when modals are hidden
    $('.modal').on('hidden.bs.modal', function() {
        $(this).find('form').removeClass('was-validated')[0].reset();
    });
});

// CRUD Functions
function viewSparepart(id) {
    Swal.fire({
        title: 'Sparepart Details',
        html: `
            <div class="text-start">
                <p><strong>Part Number:</strong> FL-OIL-${id.toString().padStart(3, '0')}</p>
                <p><strong>Name:</strong> Engine Oil Filter</p>
                <p><strong>Category:</strong> <span class="badge bg-light text-dark">Engine</span></p>
                <p><strong>Brand:</strong> Toyota</p>
                <p><strong>Current Stock:</strong> 45 units</p>
                <p><strong>Minimum Stock:</strong> 10 units</p>
                <p><strong>Status:</strong> <span class="badge bg-success">In Stock</span></p>
                <p><strong>Unit Price:</strong> Rp 85,000</p>
                <p><strong>Total Value:</strong> Rp 3,825,000</p>
                <p><strong>Supplier:</strong> PT Spare Parts Indonesia</p>
                <p><strong>Last Updated:</strong> 2024-01-15</p>
            </div>
        `,
        confirmButtonText: 'Close',
        showCancelButton: false,
        width: '500px'
    });
}

function editSparepart(id) {
    Swal.fire({
        title: 'Edit Sparepart',
        text: 'Edit functionality will be implemented when backend is ready.',
        icon: 'info'
    });
}

function deleteSparepart(id) {
    Swal.fire({
        title: 'Are you sure?',
        text: "This action will permanently delete the sparepart from inventory!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                icon: 'success',
                title: 'Deleted!',
                text: 'Sparepart has been deleted from inventory.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function adjustStock(id) {
    // Load part info
    $('#partInfo').html(`
        <strong>FL-OIL-${id.toString().padStart(3, '0')}</strong> - Engine Oil Filter<br>
        <small class="text-muted">Current Stock: 45 units | Minimum: 10 units</small>
    `);
    $('#stockAdjustmentModal').modal('show');
}

function exportData() {
    const format = $('input[name="exportFormat"]:checked').val();
    const lowStockOnly = $('#lowStockOnly').is(':checked');
    
    if (format === 'excel') {
        sparepartsTable.button('.buttons-excel').trigger();
    } else if (format === 'pdf') {
        sparepartsTable.button('.buttons-pdf').trigger();
    } else if (format === 'csv') {
        sparepartsTable.button('.buttons-csv').trigger();
    }
    
    $('#exportModal').modal('hide');
    
    let message = `Exporting ${lowStockOnly ? 'low stock items' : 'all data'} as ${format.toUpperCase()}...`;
    
    Swal.fire({
        icon: 'success',
        title: 'Export Started',
        text: message,
        timer: 2000,
        showConfirmButton: false
    });
}
</script>
<?= $this->endSection(); ?>