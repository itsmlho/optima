<?= $this->extend('layouts/base') ?>

<?php
helper('global_permission');
$permissions = get_global_permission('service');
$can_edit   = $permissions['edit'];
$can_delete = $permissions['delete'];
?>

<?= $this->section('content') ?>

<?php
// Helper — TTR formatting
$ttrHours = (int)($wo['ttr_hours'] ?? 0);
$ttrDays  = floor($ttrHours / 24);
$ttrRem   = $ttrHours % 24;
$ttrLabel = $ttrDays > 0 ? "{$ttrDays}d {$ttrRem}h" : "{$ttrHours}h";

// Status color map fallback
$statusColor = $wo['status_color'] ?? 'secondary';
$priorityColor = $wo['priority_color'] ?? 'secondary';

// Format dates helper
function fmtDate(?string $dt, string $format = 'd M Y, H:i'): string {
    if (!$dt || $dt === '0000-00-00 00:00:00') return '-';
    return date($format, strtotime($dt));
}
// Map Bootstrap color to badge-soft-* for Optima standards
function softBadgeClass(?string $c): string {
    $m = ['primary'=>'blue','success'=>'green','warning'=>'yellow','danger'=>'red','info'=>'cyan','secondary'=>'gray','dark'=>'gray','orange'=>'orange'];
    return 'badge-soft-'.($m[$c ?? ''] ?? 'gray');
}
?>

<!-- Page Header -->
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="<?= base_url('service/work-orders') ?>"><i class="fas fa-wrench me-1"></i>Work Orders</a></li>
                <li class="breadcrumb-item active"><?= esc($wo['work_order_number']) ?></li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-0">
            <i class="fas fa-clipboard-check me-2 text-primary"></i>
            <?= esc($wo['work_order_number']) ?>
        </h4>
        <p class="text-muted small mb-0">
            <?= fmtDate($wo['report_date']) ?>
            &bull; <?= esc($wo['order_type']) ?>
            &bull; <span class="badge <?= softBadgeClass($statusColor) ?>"><?= esc($wo['status_name']) ?></span>
            &bull; <span class="badge <?= softBadgeClass($priorityColor === 'warning' ? 'warning' : $priorityColor) ?>"><?= esc($wo['priority_name']) ?></span>
        </p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
        <a href="<?= base_url('service/work-orders/print/' . $wo['id']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-print me-1"></i>Print
        </a>
        <?php if ($can_edit): ?>
        <a href="<?= base_url('service/work-orders/edit/' . $wo['id']) ?>" class="btn btn-warning btn-sm">
            <i class="fas fa-edit me-1"></i>Edit
        </a>
        <?php endif; ?>
        <a href="<?= base_url('service/work-orders') ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- LEFT COLUMN: Main Info -->
    <div class="col-lg-8">

        <!-- Work Order Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center py-3">
                <i class="fas fa-info-circle me-2 text-primary"></i>
                <h5 class="mb-0">Informasi Work Order</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">No. Work Order</label>
                        <span class="fw-bold font-monospace fs-5"><?= esc($wo['work_order_number']) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Tipe Order</label>
                        <span class="badge badge-soft-cyan fs-6"><?= esc($wo['order_type']) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Status</label>
                        <span class="badge <?= softBadgeClass($statusColor) ?> fs-6"><?= esc($wo['status_name']) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Priority</label>
                        <span class="badge <?= softBadgeClass($priorityColor === 'warning' ? 'warning' : $priorityColor) ?> fs-6"><?= esc($wo['priority_name']) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Kategori</label>
                        <span><?= esc($wo['category_name'] ?? '-') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Sub-Kategori</label>
                        <span><?= esc($wo['subcategory_name'] ?? '-') ?></span>
                    </div>
                    <div class="col-12">
                        <label class="small text-muted d-block mb-1">Deskripsi Keluhan</label>
                        <p class="mb-0 p-3 bg-light rounded border-start border-primary border-3">
                            <?= nl2br(esc($wo['complaint_description'] ?? '-')) ?>
                        </p>
                    </div>
                    <?php if (!empty($wo['work_notes'])): ?>
                    <div class="col-12">
                        <label class="small text-muted d-block mb-1">Catatan Pekerjaan</label>
                        <p class="mb-0 p-3 bg-light rounded border-start border-success border-3">
                            <?= nl2br(esc($wo['work_notes'])) ?>
                        </p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Unit Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center py-3">
                <i class="fas fa-forklift me-2 text-primary"></i>
                <h5 class="mb-0">Informasi Unit</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <label class="small text-muted d-block mb-1">No. Unit</label>
                        <span class="fw-bold font-monospace"><?= esc($wo['no_unit'] ?? '-') ?></span>
                    </div>
                    <div class="col-sm-4">
                        <label class="small text-muted d-block mb-1">Merk / Model</label>
                        <span><?= esc(($wo['merk_unit'] ?? '') . ' ' . ($wo['model_unit'] ?? '')) ?></span>
                    </div>
                    <div class="col-sm-4">
                        <label class="small text-muted d-block mb-1">Hour Meter</label>
                        <span class="fw-bold text-success"><?= number_format($wo['unit_hm'] ?? 0) ?> HM</span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Customer</label>
                        <span class="fw-bold"><?= esc($wo['pelanggan'] ?? '-') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Lokasi</label>
                        <span><?= esc($wo['location_name'] ?? '-') ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">Area</label>
                        <span><?= esc(($wo['area_code'] ?? '') . ' — ' . ($wo['area_name'] ?? '-')) ?></span>
                    </div>
                    <div class="col-sm-6">
                        <label class="small text-muted d-block mb-1">PIC</label>
                        <span><?= esc($wo['pic'] ?? '-') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Spareparts Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center justify-content-between py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-toolbox me-2 text-primary"></i>
                    <h5 class="mb-0">Sparepart Digunakan</h5>
                </div>
                <span class="badge badge-soft-gray"><?= count($spareparts) ?> item</span>
            </div>
            <?php if (empty($spareparts)): ?>
            <div class="card-body text-center text-muted py-4">
                <i class="fas fa-box-open fa-2x mb-2 opacity-50"></i>
                <p class="mb-0">Tidak ada sparepart yang digunakan.</p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover table-sm mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Kode</th>
                            <th>Nama Sparepart</th>
                            <th class="text-center">Qty Bawa</th>
                            <th class="text-center">Qty Pakai</th>
                            <th>Status</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spareparts as $i => $sp): ?>
                        <tr>
                            <td class="text-muted"><?= $i + 1 ?></td>
                            <td>
                                <?php if (empty($sp['sparepart_code'])): ?>
                                    <span class="badge bg-warning text-dark">Manual Entry</span>
                                    <div class="small text-muted">(Menunggu kode WH)</div>
                                <?php else: ?>
                                    <code><?= esc($sp['sparepart_code']) ?></code>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= esc($sp['sparepart_name'] ?? esc($sp['item_name'] ?? '-')) ?>
                                
                                <!-- Source Type Badge -->
                                <?php if (!empty($sp['source_type'])): ?>
                                    <div class="mt-1">
                                        <?php if ($sp['source_type'] === 'WAREHOUSE'): ?>
                                            <span class="badge badge-soft-green">
                                                <i class="fas fa-warehouse"></i> Warehouse
                                            </span>
                                        <?php elseif ($sp['source_type'] === 'BEKAS'): ?>
                                            <span class="badge badge-soft-yellow">
                                                <i class="fas fa-recycle"></i> Bekas
                                            </span>
                                        <?php elseif ($sp['source_type'] === 'KANIBAL'): ?>
                                            <span class="badge badge-soft-orange">
                                                <i class="fas fa-exchange-alt"></i> Kanibal
                                            </span>
                                            <?php if (!empty($sp['source_unit_number'])): ?>
                                                <div class="small text-muted mt-1">
                                                    Dari Unit: <strong><?= esc($sp['source_unit_number']) ?></strong>
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($sp['source_notes'])): ?>
                                                <div class="small text-muted">
                                                    Alasan: <?= esc($sp['source_notes']) ?>
                                                </div>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= number_format($sp['quantity_brought'] ?? 0) ?></td>
                            <td class="text-center fw-bold text-primary"><?= number_format($sp['quantity_used'] ?? 0) ?></td>
                            <td>
                                <?php $spStatus = $sp['status'] ?? 'N/A'; ?>
                                <span class="badge badge-soft-<?= $spStatus === 'USED' ? 'green' : ($spStatus === 'RETURNED' ? 'cyan' : 'gray') ?>">
                                    <?= esc($spStatus) ?>
                                </span>
                            </td>
                            <td class="text-muted small"><?= esc($sp['notes'] ?? '-') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-4">

        <!-- KPI / TTR Card -->
        <div class="card shadow-sm mb-4 border-0 bg-primary text-white">
            <div class="card-body py-4">
                <div class="text-center">
                    <div class="fs-1 fw-bold"><?= $ttrLabel ?></div>
                    <div class="small opacity-75">
                        <?= $wo['status_code'] === 'CLOSED' ? 'Total TTR (Time To Repair)' : 'Sudah Berjalan' ?>
                    </div>
                </div>
                <hr class="opacity-25">
                <div class="row text-center g-0">
                    <div class="col-6 border-end border-white border-opacity-25">
                        <div class="small opacity-75">Dibuat</div>
                        <div class="fw-bold small"><?= fmtDate($wo['report_date'], 'd M Y') ?></div>
                    </div>
                    <div class="col-6">
                        <div class="small opacity-75">Selesai</div>
                        <div class="fw-bold small"><?= fmtDate($wo['closed_date'] ?? null, 'd M Y') ?: 'Belum selesai' ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Staff Assignment Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center py-3">
                <i class="fas fa-users me-2 text-primary"></i>
                <h5 class="mb-0">Tim Pengerjaan</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php
                    $staffList = [
                        ['role' => 'Admin',   'icon' => 'fa-user-shield',  'name' => $wo['admin_name']   ?? null],
                        ['role' => 'Foreman', 'icon' => 'fa-user-tie',     'name' => $wo['foreman_name'] ?? null],
                        ['role' => 'Mechanic','icon' => 'fa-user-cog',     'name' => $wo['mechanic_name']?? null],
                        ['role' => 'Helper',  'icon' => 'fa-user-friends', 'name' => $wo['helper_name']  ?? null],
                    ];
                    foreach ($staffList as $staff): ?>
                    <li class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <i class="fas <?= $staff['icon'] ?> text-muted fa-fw"></i>
                        <div>
                            <div class="small text-muted"><?= $staff['role'] ?></div>
                            <div class="fw-bold"><?= esc($staff['name'] ?? '<span class="text-muted fst-italic">Belum di-assign</span>') ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                    <?php if (!empty($assignments)): ?>
                        <?php foreach ($assignments as $asgn): ?>
                        <li class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                            <i class="fas fa-user-plus text-muted fa-fw"></i>
                            <div>
                                <div class="small text-muted"><?= esc($asgn['staff_role'] ?? 'Assignment') ?></div>
                                <div class="fw-bold"><?= esc($asgn['staff_name']) ?></div>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Dates Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center py-3">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                <h5 class="mb-0">Timeline Tanggal</h5>
            </div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                    <?php
                    $dateFields = [
                        ['label' => 'Tanggal Lapor',   'value' => $wo['report_date']    ?? null, 'icon' => 'fa-flag',     'color' => 'primary'],
                        ['label' => 'Tanggal Assign',  'value' => $wo['assigned_date']  ?? null, 'icon' => 'fa-user-check','color' => 'info'],
                        ['label' => 'Tanggal Mulai',   'value' => $wo['start_date']     ?? null, 'icon' => 'fa-play',     'color' => 'warning'],
                        ['label' => 'Tanggal Selesai', 'value' => $wo['completed_date'] ?? null, 'icon' => 'fa-check',    'color' => 'success'],
                        ['label' => 'Tanggal Close',   'value' => $wo['closed_date']    ?? null, 'icon' => 'fa-lock',     'color' => 'dark'],
                    ];
                    foreach ($dateFields as $df):
                        $val = fmtDate($df['value']);
                        if ($val === '-') continue;
                    ?>
                    <li class="list-group-item d-flex align-items-center gap-3 px-3 py-2">
                        <span class="badge <?= softBadgeClass($df['color']) ?> rounded-circle p-2">
                            <i class="fas <?= $df['icon'] ?>"></i>
                        </span>
                        <div>
                            <div class="small text-muted"><?= $df['label'] ?></div>
                            <div class="fw-bold small"><?= $val ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <!-- Status History / Timeline -->
        <div class="card shadow-sm mb-4">
            <div class="card-header d-flex align-items-center py-3">
                <i class="fas fa-history me-2 text-primary"></i>
                <h5 class="mb-0">Riwayat Status</h5>
            </div>
            <div class="card-body">
                <?php if (empty($statusHistory)): ?>
                <p class="text-muted small mb-0">Belum ada riwayat status.</p>
                <?php else: ?>
                <div class="timeline-container" style="position:relative; padding-left: 1.5rem;">
                    <div style="position:absolute;left:.45rem;top:0;bottom:0;width:2px;background:#dee2e6;border-radius:1px;"></div>
                    <?php foreach ($statusHistory as $history): ?>
                    <div class="timeline-item mb-3" style="position:relative;">
                        <div style="position:absolute;left:-1.35rem;top:4px;width:14px;height:14px;border-radius:50%;background:var(--bs-<?= $history['to_color'] ?? 'secondary' ?>);border:2px solid #fff;box-shadow:0 0 0 2px var(--bs-<?= $history['to_color'] ?? 'secondary' ?>);"></div>
                        <div class="ps-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge <?= softBadgeClass($history['to_color'] ?? 'secondary') ?> badge-sm"><?= esc($history['to_status'] ?? 'Unknown') ?></span>
                                <?php if (!empty($history['from_status'])): ?>
                                <small class="text-muted">dari <?= esc($history['from_status']) ?></small>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($history['notes'])): ?>
                            <p class="small mb-1 text-muted fst-italic">"<?= esc($history['notes']) ?>"</p>
                            <?php endif; ?>
                            <small class="text-muted"><?= fmtDate($history['created_at']) ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Flash message support
<?php if (session()->has('error')): ?>
document.addEventListener('DOMContentLoaded', () => {
    alertSwal('error', '<?= addslashes(session('error')) ?>');
});
<?php endif; ?>
<?php if (session()->has('success')): ?>
document.addEventListener('DOMContentLoaded', () => {
    alertSwal('success', '<?= addslashes(session('success')) ?>');
});
<?php endif; ?>
</script>
<?= $this->endSection() ?>
