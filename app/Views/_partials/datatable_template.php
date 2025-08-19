<!-- DataTable Template -->
<!-- Usage: Include this file and pass the required variables -->
<?php
// Expected variables:
// $table_id - unique ID for the table
// $table_title - title of the table
// $table_subtitle - subtitle/description of the table
// $filters - array of filter configurations (optional)
// $columns - array of column definitions
// $actions - array of action button configurations (optional)
// $data - array of table data (optional for demo)
?>

<div class="pro-datatable-card">
    <!-- DataTable Header -->
    <div class="pro-datatable-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h5 class="pro-datatable-title"><?= $table_title ?? 'Data Table' ?></h5>
                <p class="pro-datatable-subtitle"><?= $table_subtitle ?? 'Manage your data efficiently' ?></p>
            </div>
            <div class="pro-datatable-actions">
                <?php if (isset($actions) && is_array($actions)): ?>
                    <?php foreach ($actions as $action): ?>
                        <?php if ($action['type'] === 'filter'): ?>
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#<?= $table_id ?>Filters">
                                <i class="fas fa-filter me-1"></i><?= $action['label'] ?? 'Filter' ?>
                            </button>
                        <?php elseif ($action['type'] === 'search'): ?>
                            <button class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-search me-1"></i><?= $action['label'] ?? 'Search' ?>
                            </button>
                        <?php elseif ($action['type'] === 'export'): ?>
                            <button class="btn btn-outline-success btn-sm">
                                <i class="fas fa-download me-1"></i><?= $action['label'] ?? 'Export' ?>
                            </button>
                        <?php elseif ($action['type'] === 'add'): ?>
                            <button class="btn btn-primary btn-sm">
                                <i class="fas fa-plus me-1"></i><?= $action['label'] ?? 'Add New' ?>
                            </button>
                        <?php else: ?>
                            <button class="btn <?= $action['class'] ?? 'btn-secondary' ?> btn-sm" <?= isset($action['onclick']) ? 'onclick="' . $action['onclick'] . '"' : '' ?>>
                                <?php if (isset($action['icon'])): ?>
                                    <i class="<?= $action['icon'] ?> me-1"></i>
                                <?php endif; ?>
                                <?= $action['label'] ?? 'Action' ?>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Default actions -->
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#<?= $table_id ?>Filters">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-search me-1"></i>Search
                    </button>
                    <button class="btn btn-primary btn-sm">
                        <i class="fas fa-plus me-1"></i>Add New
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <?php if (isset($filters) && is_array($filters) && count($filters) > 0): ?>
        <div class="collapse pro-datatable-filters" id="<?= $table_id ?>Filters">
            <div class="row g-3 mb-3">
                <?php foreach ($filters as $filter): ?>
                    <div class="col-md-<?= $filter['col_size'] ?? '3' ?>">
                        <label class="form-label"><?= $filter['label'] ?></label>
                        <?php if ($filter['type'] === 'select'): ?>
                            <select class="form-select" name="<?= $filter['name'] ?>">
                                <option value=""><?= $filter['placeholder'] ?? 'All' ?></option>
                                <?php if (isset($filter['options'])): ?>
                                    <?php foreach ($filter['options'] as $value => $label): ?>
                                        <option value="<?= $value ?>"><?= $label ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        <?php elseif ($filter['type'] === 'input'): ?>
                            <input type="<?= $filter['input_type'] ?? 'text' ?>" class="form-control" 
                                   name="<?= $filter['name'] ?>" placeholder="<?= $filter['placeholder'] ?? '' ?>">
                        <?php elseif ($filter['type'] === 'date'): ?>
                            <input type="date" class="form-control" name="<?= $filter['name'] ?>">
                        <?php elseif ($filter['type'] === 'daterange'): ?>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="date" class="form-control" name="<?= $filter['name'] ?>_from" placeholder="From">
                                </div>
                                <div class="col-6">
                                    <input type="date" class="form-control" name="<?= $filter['name'] ?>_to" placeholder="To">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-secondary w-100">
                        <i class="fas fa-sync me-1"></i>Reset
                    </button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Table Body -->
    <div class="pro-datatable-body">
        <div class="table-responsive">
            <table class="table pro-datatable" id="<?= $table_id ?>">
                <thead>
                    <tr>
                        <?php if (isset($columns) && is_array($columns)): ?>
                            <?php foreach ($columns as $column): ?>
                                <th class="border-0" <?= isset($column['width']) ? 'style="width: ' . $column['width'] . '"' : '' ?>>
                                    <?= $column['label'] ?>
                                </th>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <th class="border-0 pro-datatable-actions-cell">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($data) && is_array($data)): ?>
                        <?php foreach ($data as $row): ?>
                            <tr>
                                <?php foreach ($columns as $column): ?>
                                    <td>
                                        <?php if ($column['type'] === 'avatar'): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="pro-datatable-avatar <?= $column['avatar_class'] ?? 'bg-primary' ?>">
                                                    <?php if (isset($row[$column['avatar_field']])): ?>
                                                        <?= $row[$column['avatar_field']] ?>
                                                    <?php else: ?>
                                                        <i class="<?= $column['avatar_icon'] ?? 'fas fa-user' ?>"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div>
                                                    <div class="pro-datatable-item-title"><?= $row[$column['title_field']] ?? '' ?></div>
                                                    <?php if (isset($column['subtitle_field']) && isset($row[$column['subtitle_field']])): ?>
                                                        <div class="pro-datatable-item-subtitle"><?= $row[$column['subtitle_field']] ?></div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        <?php elseif ($column['type'] === 'badge'): ?>
                                            <span class="pro-datatable-badge <?= $column['badge_class'] ?? 'status-active' ?>">
                                                <?= $row[$column['field']] ?? '' ?>
                                            </span>
                                        <?php elseif ($column['type'] === 'currency'): ?>
                                            <strong><?= $column['currency_symbol'] ?? '$' ?><?= number_format($row[$column['field']] ?? 0, 2) ?></strong>
                                        <?php elseif ($column['type'] === 'date'): ?>
                                            <?= isset($row[$column['field']]) ? date($column['date_format'] ?? 'Y-m-d', strtotime($row[$column['field']])) : '' ?>
                                        <?php else: ?>
                                            <?= $row[$column['field']] ?? '' ?>
                                        <?php endif; ?>
                                    </td>
                                <?php endforeach; ?>
                                <td class="pro-datatable-actions-cell">
                                    <div class="pro-datatable-btn-group">
                                        <button class="pro-datatable-btn pro-datatable-btn-view" data-bs-toggle="tooltip" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="pro-datatable-btn pro-datatable-btn-edit" data-bs-toggle="tooltip" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="pro-datatable-btn pro-datatable-btn-delete" data-bs-toggle="tooltip" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Sample data row for demo -->
                        <tr>
                            <td colspan="<?= count($columns ?? []) + 1 ?>" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                No data available
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="pro-datatable-pagination">
        <div class="d-flex justify-content-between align-items-center w-100">
            <div class="pro-datatable-info">
                Showing 1 to 10 of 25 entries
            </div>
            <nav>
                <ul class="pagination pagination-sm">
                    <li class="page-item disabled">
                        <a class="page-link" href="#" tabindex="-1">Previous</a>
                    </li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item">
                        <a class="page-link" href="#">Next</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Initialize DataTable -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize DataTable if available
    if (typeof DataTable !== 'undefined') {
        $('#<?= $table_id ?>').DataTable({
            responsive: true,
            pageLength: 10,
            lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: -1 } // Disable sorting for action column
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "Showing 0 to 0 of 0 entries",
                infoFiltered: "(filtered from _MAX_ total entries)",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
});
</script> 