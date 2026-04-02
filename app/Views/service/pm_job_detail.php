<?= $this->extend('layouts/base') ?>
<?php
/**
 * PM Job Detail — Checklist execution, WO link, PM history.
 */
helper('global_permission');
$permissions = get_global_permission('service');
$can_edit    = $permissions['edit'];
$job         = $job ?? [];
$checklists  = $job['checklists'] ?? [];
$history     = $job['history'] ?? [];
$fromTemplate = $job['checklist_from_template'] ?? true;
?>

<?= $this->section('content') ?>

<div class="row g-4">
    <!-- LEFT: Job Info -->
    <div class="col-lg-4">
        <!-- PM Job Header Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-file-earmark-check me-2 text-primary"></i>Informasi PM Job</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">PM Number</dt>
                    <dd class="col-7 fw-bold"><?= esc($job['pm_number'] ?? '-') ?></dd>

                    <dt class="col-5 text-muted">Status</dt>
                    <dd class="col-7" id="job-status-badge">
                        <?php
                        $statusMap = [
                            'SCHEDULED'   => '<span class="badge badge-soft-blue">Terjadwal</span>',
                            'IN_PROGRESS' => '<span class="badge badge-soft-cyan">Berjalan</span>',
                            'COMPLETED'   => '<span class="badge badge-soft-green">Selesai</span>',
                            'OVERDUE'     => '<span class="badge badge-soft-red">Overdue</span>',
                            'SKIPPED'     => '<span class="badge badge-soft-gray">Dilewati</span>',
                        ];
                        echo $statusMap[$job['status'] ?? ''] ?? '<span class="badge badge-soft-gray">-</span>';
                        ?>
                    </dd>

                    <dt class="col-5 text-muted">Jadwal</dt>
                    <dd class="col-7"><?= esc($job['schedule_name'] ?? '-') ?></dd>

                    <dt class="col-5 text-muted">Trigger</dt>
                    <dd class="col-7">
                        <?php
                        $tBadge = ['CALENDAR' => 'badge-soft-blue', 'HM' => 'badge-soft-orange', 'BOTH' => 'badge-soft-purple'];
                        $tt = $job['trigger_type'] ?? '';
                        echo "<span class=\"badge " . ($tBadge[$tt] ?? 'badge-soft-gray') . "\">$tt</span>";
                        ?>
                        <?php if ($job['interval_days'] ?? null): ?>
                            <small class="text-muted">setiap <?= $job['interval_days'] ?> hari</small>
                        <?php endif; ?>
                        <?php if ($job['interval_hm'] ?? null): ?>
                            <small class="text-muted">/ <?= $job['interval_hm'] ?> HM</small>
                        <?php endif; ?>
                    </dd>

                    <dt class="col-5 text-muted">Due Date</dt>
                    <dd class="col-7">
                        <?php
                        $dueDate = $job['due_date'] ?? null;
                        if ($dueDate) {
                            $diff = (strtotime($dueDate) - strtotime(date('Y-m-d'))) / 86400;
                            $cls  = $diff < 0 ? 'text-danger fw-bold' : ($diff <= 7 ? 'text-warning' : '');
                            echo "<span class=\"$cls\">$dueDate</span>";
                        } else { echo '-'; }
                        ?>
                    </dd>

                    <dt class="col-5 text-muted">Due HM</dt>
                    <dd class="col-7"><?= $job['due_hm'] ? $job['due_hm'] . ' HM' : '-' ?></dd>

                    <?php if ($job['actual_date'] ?? null): ?>
                    <dt class="col-5 text-muted">Dikerjakan</dt>
                    <dd class="col-7"><?= esc($job['actual_date']) ?>
                        <?php if ($job['actual_hm'] ?? null): ?>
                            <br><small class="text-muted"><?= $job['actual_hm'] ?> HM</small>
                        <?php endif; ?>
                    </dd>
                    <?php endif; ?>
                </dl>
            </div>
        </div>

        <!-- Unit Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-truck me-2 text-info"></i>Informasi Unit</h6>
            </div>
            <div class="card-body">
                <dl class="row mb-0 small">
                    <dt class="col-5 text-muted">No. Unit</dt>
                    <dd class="col-7 fw-bold"><?= esc($job['no_unit'] ?? '-') ?></dd>
                    <dt class="col-5 text-muted">Merk/Model</dt>
                    <dd class="col-7"><?= esc(trim(($job['merk'] ?? '') . ' ' . ($job['model'] ?? ''))) ?: '-' ?></dd>
                    <dt class="col-5 text-muted">Serial</dt>
                    <dd class="col-7"><?= esc($job['serial_number'] ?? '-') ?></dd>
                    <dt class="col-5 text-muted">HM Saat Ini</dt>
                    <dd class="col-7 fw-semibold"><?= $job['current_hm'] ? $job['current_hm'] . ' HM' : '-' ?></dd>
                    <dt class="col-5 text-muted">Customer</dt>
                    <dd class="col-7"><?= esc($job['customer_name'] ?? '-') ?></dd>
                    <dt class="col-5 text-muted">Lokasi</dt>
                    <dd class="col-7"><?= esc($job['location_name'] ?? '-') ?></dd>
                </dl>
            </div>
        </div>

        <!-- Work Order Card -->
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0"><i class="bi bi-wrench me-2 text-warning"></i>Work Order</h6>
            </div>
            <div class="card-body">
                <?php if ($job['work_order_id'] ?? null): ?>
                <div class="d-flex align-items-center gap-2">
                    <a href="<?= base_url('service/work-orders/view/' . $job['work_order_id']) ?>"
                       class="btn btn-outline-primary btn-sm" target="_blank">
                        <i class="bi bi-box-arrow-up-right me-1"></i><?= esc($job['work_order_number']) ?>
                    </a>
                    <span class="badge badge-soft-cyan"><?= esc($job['wo_status'] ?? '') ?></span>
                </div>
                <?php elseif (in_array($job['status'] ?? '', ['SCHEDULED','OVERDUE','IN_PROGRESS'])): ?>
                <?php if ($can_edit): ?>
                <button class="btn btn-success btn-sm w-100" id="btn-create-wo" data-id="<?= $job['id'] ?>">
                    <i class="bi bi-plus-circle me-1"></i>Buat Work Order
                </button>
                <small class="text-muted d-block mt-1">WO tipe PMPS akan dibuat otomatis</small>
                <?php else: ?>
                <span class="text-muted small">Belum ada Work Order</span>
                <?php endif; ?>
                <?php else: ?>
                <span class="text-muted small">Tidak ada Work Order</span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Actions Card -->
        <?php if (in_array($job['status'] ?? '', ['SCHEDULED','OVERDUE','IN_PROGRESS']) && $can_edit): ?>
        <div class="card mb-4 border-success">
            <div class="card-header bg-success bg-opacity-10">
                <h6 class="card-title mb-0 text-success"><i class="bi bi-check-circle me-2"></i>Selesaikan PM Job</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Tanggal Dikerjakan <span class="text-danger">*</span></label>
                    <input type="date" id="actual-date" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Hour Meter Aktual</label>
                    <input type="number" step="0.1" id="actual-hm" class="form-control form-control-sm"
                           placeholder="HM saat PM dikerjakan" value="<?= $job['current_hm'] ?? '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold small">Catatan</label>
                    <textarea id="complete-notes" class="form-control form-control-sm" rows="2"></textarea>
                </div>
                <button class="btn btn-success btn-sm w-100" id="btn-complete-job" data-id="<?= $job['id'] ?>">
                    <i class="bi bi-check-lg me-1"></i>Tandai Selesai
                </button>
            </div>
        </div>
        <?php endif; ?>

        <!-- PM History -->
        <?php if (!empty($history)): ?>
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0"><i class="bi bi-clock-history me-2 text-secondary"></i>Riwayat PM Unit</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                <?php foreach ($history as $h): ?>
                <div class="list-group-item px-3 py-2 small">
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold"><?= esc($h['pm_number']) ?></span>
                        <span class="text-muted"><?= esc($h['actual_date'] ?? '-') ?></span>
                    </div>
                    <?php if ($h['work_order_number'] ?? null): ?>
                    <a href="<?= base_url('service/work-orders/view/' . ($h['work_order_id'] ?? '#')) ?>" class="text-primary small" target="_blank">
                        <?= esc($h['work_order_number']) ?>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- RIGHT: Checklist -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Checklist PM</h6>
                    <?php if ($fromTemplate): ?>
                    <small class="text-muted">Template checklist — isi hasil saat PM dikerjakan</small>
                    <?php else: ?>
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i>Checklist sudah diisi</small>
                    <?php endif; ?>
                </div>
                <?php if (in_array($job['status'] ?? '', ['SCHEDULED','OVERDUE','IN_PROGRESS']) && $can_edit): ?>
                <button class="btn btn-outline-primary btn-sm" id="btn-save-checklist" data-id="<?= $job['id'] ?>">
                    <i class="bi bi-save me-1"></i>Simpan Checklist
                </button>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php if (empty($checklists)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-clipboard2 fs-1 d-block mb-3 opacity-25"></i>
                    <p>Belum ada item checklist untuk jadwal PM ini.</p>
                    <a href="<?= base_url('service/pm-schedules') ?>" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-gear me-1"></i>Atur Template Checklist
                    </a>
                </div>
                <?php else: ?>
                <!-- Checklist Header -->
                <div class="row g-2 fw-semibold text-muted small border-bottom pb-2 mb-3 d-none d-md-flex">
                    <div class="col-md-4">Item</div>
                    <div class="col-md-2">Kategori</div>
                    <div class="col-md-2">Tindakan</div>
                    <div class="col-md-2">Hasil</div>
                    <div class="col-md-2">Catatan</div>
                </div>
                <div id="checklist-items">
                <?php foreach ($checklists as $idx => $item): ?>
                <div class="row g-2 align-items-center mb-3 checklist-row" data-idx="<?= $idx ?>">
                    <div class="col-md-4">
                        <div class="fw-semibold"><?= esc($item['item_name']) ?></div>
                        <?php if ($item['is_required'] ?? 1): ?>
                        <span class="badge badge-soft-orange small">Wajib</span>
                        <?php endif; ?>
                        <input type="hidden" class="ci-template-id" value="<?= $item['template_item_id'] ?? ($item['id'] ?? '') ?>">
                        <input type="hidden" class="ci-item-name" value="<?= esc($item['item_name']) ?>">
                        <input type="hidden" class="ci-action-type" value="<?= $item['action_type'] ?? 'CHECK' ?>">
                    </div>
                    <div class="col-md-2">
                        <small class="text-muted"><?= esc($item['item_category'] ?? '-') ?></small>
                    </div>
                    <div class="col-md-2">
                        <span class="badge badge-soft-blue"><?= esc($item['action_type'] ?? 'CHECK') ?></span>
                    </div>
                    <div class="col-md-2">
                        <?php
                        $currentResult = $item['result'] ?? 'OK';
                        $readonly = !in_array($job['status'] ?? '', ['SCHEDULED','OVERDUE','IN_PROGRESS']) || !$can_edit;
                        $resultOptions = ['OK' => 'OK', 'NOT_OK' => 'Not OK', 'REPLACED' => 'Diganti', 'ADJUSTED' => 'Disetel', 'N/A' => 'N/A'];
                        ?>
                        <?php if (!$readonly): ?>
                        <select class="form-select form-select-sm ci-result">
                            <?php foreach ($resultOptions as $val => $label): ?>
                            <option value="<?= $val ?>" <?= $currentResult === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php else: ?>
                        <?php
                        $rBadge = ['OK' => 'badge-soft-green', 'NOT_OK' => 'badge-soft-red', 'REPLACED' => 'badge-soft-orange', 'ADJUSTED' => 'badge-soft-blue', 'N/A' => 'badge-soft-gray'];
                        echo '<span class="badge ' . ($rBadge[$currentResult] ?? 'badge-soft-gray') . '">' . ($resultOptions[$currentResult] ?? $currentResult) . '</span>';
                        ?>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-2">
                        <?php if (!$readonly): ?>
                        <input type="text" class="form-control form-control-sm ci-notes"
                               placeholder="Catatan" value="<?= esc($item['notes'] ?? '') ?>">
                        <?php else: ?>
                        <small class="text-muted"><?= esc($item['notes'] ?? '-') ?></small>
                        <?php endif; ?>
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
// BASE_URL already declared as var by base layout — do not redeclare
const JOB_ID = <?= (int) ($job['id'] ?? 0) ?>;

function getCsrf() {
    return {
        name: window.csrfTokenName,
        hash: (typeof window.getCsrfToken === 'function') ? window.getCsrfToken() : window.csrfToken
    };
}

$(document).ready(function() {

    // Create Work Order
    $('#btn-create-wo').on('click', function() {
        const pm = '<?= esc($job['pm_number'] ?? '', 'js') ?>';
        OptimaConfirm.generic({
            title: 'Buat Work Order PMPS',
            html: `<p class="mb-0">Buat Work Order untuk PM Job <strong>${pm}</strong>?<br><small class="text-muted">WO tipe PMPS akan dibuat otomatis.</small></p>`,
            icon: 'question',
            confirmText: 'Buat WO',
            cancelText: 'Batal',
            confirmButtonColor: 'success',
            onConfirm: () => {
                const $btn = $('#btn-create-wo').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Membuat WO...');
                const csrf = getCsrf();
                $.post(BASE_URL + 'service/pmps/createWorkOrder/' + JOB_ID, { [csrf.name]: csrf.hash }, function(res) {
                    if (res.success) {
                        OptimaNotify.success(res.message);
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        OptimaNotify.error(res.message || 'Gagal membuat WO');
                    }
                }).fail(() => OptimaNotify.error('Gagal menghubungi server'))
                  .always(() => $btn.prop('disabled', false).html('<i class="bi bi-plus-circle me-1"></i>Buat Work Order'));
            }
        });
    });

    // Save Checklist
    $('#btn-save-checklist').on('click', function() {
        const items = [];
        $('#checklist-items .checklist-row').each(function() {
            items.push({
                template_item_id: $(this).find('.ci-template-id').val() || null,
                item_name:   $(this).find('.ci-item-name').val(),
                action_type: $(this).find('.ci-action-type').val(),
                result:      $(this).find('.ci-result').val() || $(this).find('.ci-result-badge').data('val') || 'OK',
                notes:       $(this).find('.ci-notes').val() || '',
            });
        });

        const csrf    = getCsrf();
        const payload = { [csrf.name]: csrf.hash };
        items.forEach((item, i) => {
            Object.entries(item).forEach(([k, v]) => { payload[`items[${i}][${k}]`] = v ?? ''; });
        });

        const $btn = $(this).prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Menyimpan...');
        $.post(BASE_URL + 'service/pmps/saveChecklist/' + JOB_ID, payload, function(res) {
            if (res.success) {
                OptimaNotify.success(res.message || 'Checklist berhasil disimpan');
            } else {
                OptimaNotify.error(res.message || 'Gagal menyimpan checklist');
            }
        }).fail(() => OptimaNotify.error('Gagal menghubungi server'))
          .always(() => $btn.prop('disabled', false).html('<i class="bi bi-save me-1"></i>Simpan Checklist'));
    });

    // Complete Job
    $('#btn-complete-job').on('click', function() {
        const date = $('#actual-date').val();
        if (!date) { OptimaNotify.warning('Tanggal pengerjaan wajib diisi', 'Validasi'); return; }
        OptimaConfirm.generic({
            title: 'Tandai Selesai',
            html: '<p class="mb-0">Tandai PM Job ini sebagai <strong>SELESAI</strong>?<br><small class="text-muted">Jadwal PM akan diperbarui secara otomatis.</small></p>',
            icon: 'question',
            confirmText: 'Selesaikan',
            cancelText: 'Batal',
            confirmButtonColor: 'success',
            onConfirm: () => {
                const $btn = $('#btn-complete-job').prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Menyimpan...');
                const csrf = getCsrf();
                $.post(BASE_URL + 'service/pmps/complete/' + JOB_ID, {
                    [csrf.name]: csrf.hash,
                    actual_date: date,
                    actual_hm:   $('#actual-hm').val(),
                    notes:       $('#complete-notes').val(),
                }, function(res) {
                    if (res.success) {
                        OptimaNotify.success(res.message || 'PM Job berhasil diselesaikan');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        OptimaNotify.error(res.message || 'Gagal menyimpan');
                    }
                }).fail(() => OptimaNotify.error('Gagal menghubungi server'))
                  .always(() => $btn.prop('disabled', false).html('<i class="bi bi-check-lg me-1"></i>Tandai Selesai'));
            }
        });
    });
});
</script>
<?= $this->endSection() ?>
