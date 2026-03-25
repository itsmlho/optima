<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
.alert-widget {
    border-left: 4px solid;
    transition: all 0.3s ease;
}
.alert-widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.alert-widget-warning {
    border-left-color: #ffc107;
    background: linear-gradient(135deg, #fff9e6 0%, #fffbf0 100%);
}
.alert-widget-danger {
    border-left-color: #dc3545;
    background: linear-gradient(135deg, #ffe6e6 0%, #fff5f5 100%);
}
.alert-widget-info {
    border-left-color: #0dcaf0;
    background: linear-gradient(135deg, #e6f9ff 0%, #f0fcff 100%);
}
.alert-widget-success {
    border-left-color: #28a745;
    background: linear-gradient(135deg, #e6f9ec 0%, #f0fcf5 100%);
}
.days-badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}
.urgency-critical {
    background: #dc3545;
    color: white;
}
.urgency-high {
    background: #ffc107;
    color: #000;
}
.urgency-medium {
    background: #0dcaf0;
    color: #000;
}
.kpi-card {
    border-radius: 10px;
    transition: all 0.3s ease;
}
.kpi-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.kpi-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}
.table-compact tbody td {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800">
                <i class="fas fa-chart-line text-primary me-2"></i><?= lang('Finance.finance_dashboard') ?>
            </h1>
            <p class="text-muted mb-0"><?= lang('Finance.finance_monitor_subtitle') ?></p>
        </div>
        <div>
            <button class="btn btn-primary" onclick="window.location.href='<?= base_url('finance/invoices') ?>'">
                <i class="fas fa-file-invoice me-2"></i><?= lang('Finance.invoice_management') ?>
            </button>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><?= lang('Finance.unlinked_dis') ?></p>
                            <h3 class="mb-0 fw-bold text-warning"><?= count($unlinked_deliveries ?? []) ?></h3>
                        </div>
                        <div class="kpi-icon bg-warning bg-opacity-10 text-warning">
                            <i class="fas fa-file-circle-exclamation"></i>
                        </div>
                    </div>
                    <small class="text-muted"><?= lang('Finance.waiting_contract_linking') ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><?= lang('Finance.upcoming_invoices') ?></p>
                            <h3 class="mb-0 fw-bold text-info"><?= count($upcoming_invoices ?? []) ?></h3>
                        </div>
                        <div class="kpi-icon bg-info bg-opacity-10 text-info">
                            <i class="fas fa-calendar-days"></i>
                        </div>
                    </div>
                    <small class="text-muted"><?= lang('Finance.next_7_days') ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><?= lang('Finance.overdue_invoices') ?></p>
                            <h3 class="mb-0 fw-bold text-danger"><?= count($overdue_invoices ?? []) ?></h3>
                        </div>
                        <div class="kpi-icon bg-danger bg-opacity-10 text-danger">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                    <small class="text-muted"><?= lang('Finance.past_due') ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card kpi-card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <p class="text-muted mb-1 small"><?= lang('Finance.draft_invoices') ?></p>
                            <h3 class="mb-0 fw-bold text-secondary"><?= count($draft_invoices ?? []) ?></h3>
                        </div>
                        <div class="kpi-icon bg-secondary bg-opacity-10 text-secondary">
                            <i class="fas fa-file-lines"></i>
                        </div>
                    </div>
                    <small class="text-muted"><?= lang('Finance.needs_approval') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Widgets Row -->
    <div class="row g-3 mb-4">
        <!-- Unlinked DIs Alert -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-triangle-exclamation text-warning me-2"></i>
                        <?= lang('Finance.di_waiting_contract_link') ?>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($unlinked_deliveries)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-check-circle text-success fa-3x mb-3"></i>
                            <p class="text-muted mb-0"><?= lang('Finance.all_dis_linked') ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($unlinked_deliveries as $di): 
                            $daysPending = $di['days_pending'];
                            $urgencyClass = 'alert-widget-info';
                            $urgencyBadge = 'urgency-medium';
                            $urgencyText = lang('Finance.urgency_normal');
                            
                            if ($daysPending > 14) {
                                $urgencyClass = 'alert-widget-danger';
                                $urgencyBadge = 'urgency-critical';
                                $urgencyText = lang('Finance.urgency_critical');
                            } elseif ($daysPending > 7) {
                                $urgencyClass = 'alert-widget-warning';
                                $urgencyBadge = 'urgency-high';
                                $urgencyText = lang('Finance.urgency_high');
                            }
                        ?>
                            <div class="alert-widget <?= $urgencyClass ?> p-3 mb-3 rounded">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="mb-1">
                                            <a href="<?= base_url('marketing/di/detail/' . $di['id']) ?>" class="text-decoration-none text-dark fw-bold">
                                                <?= esc($di['nomor_di']) ?>
                                            </a>
                                        </h6>
                                        <small class="text-muted"><?= lang('Finance.spk_label') ?>: <?= esc($di['nomor_spk'] ?? '-') ?></small>
                                    </div>
                                    <span class="badge days-badge <?= $urgencyBadge ?>">
                                        <?= str_replace('{count}', (string) $daysPending, lang('Finance.days_pending')) ?>
                                    </span>
                                </div>
                                <p class="mb-2 small">
                                    <i class="fas fa-building me-1"></i>
                                    <strong><?= lang('Finance.customer_label') ?>:</strong> <?= esc($di['pelanggan']) ?>
                                </p>
                                <p class="mb-2 small">
                                    <i class="fas fa-calendar me-1"></i>
                                    <strong><?= lang('Finance.created_label') ?>:</strong> <?= date('d M Y', strtotime($di['dibuat_pada'])) ?>
                                </p>
                                <div class="d-flex gap-2 mt-2">
                                    <button class="btn btn-sm btn-primary" onclick="showLinkModal(<?= $di['spk_id'] ?>, <?= $di['id'] ?>)">
                                        <i class="fas fa-link me-1"></i><?= lang('Finance.link_to_contract') ?>
                                    </button>
                                    <a href="<?= base_url('marketing/di/detail/' . $di['id']) ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="fas fa-eye me-1"></i><?= lang('Common.view') ?>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Upcoming Invoices Alert -->
        <div class="col-xl-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-check text-info me-2"></i>
                        <?= lang('Finance.upcoming_recurring_invoices') ?>
                    </h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php if (empty($upcoming_invoices)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-calendar text-muted fa-3x mb-3"></i>
                            <p class="text-muted mb-0"><?= lang('Finance.no_upcoming_invoices_7d') ?></p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-compact table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><?= lang('Finance.contract') ?></th>
                                        <th><?= lang('Finance.customer_label') ?></th>
                                        <th><?= lang('Finance.due_date') ?></th>
                                        <th><?= lang('Finance.days') ?></th>
                                        <th><?= lang('Finance.frequency') ?></th>
                                        <th><?= lang('Common.action') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($upcoming_invoices as $invoice): 
                                        $daysUntil = $invoice['days_until_due'] ?? 0;
                                        $badgeClass = $daysUntil <= 1 ? 'bg-danger' : ($daysUntil <= 3 ? 'bg-warning' : 'bg-info');
                                    ?>
                                        <tr>
                                            <td>
                                                <strong><?= esc($invoice['no_kontrak'] ?? '-') ?></strong>
                                            </td>
                                            <td><?= esc($invoice['nama_customer'] ?? '-') ?></td>
                                            <td>
                                                <small><?= date('d M Y', strtotime($invoice['next_billing_date'])) ?></small>
                                            </td>
                                            <td>
                                                <span class="badge <?= $badgeClass ?>"><?= str_replace('{n}', (string) $daysUntil, lang('Finance.days_abbr')) ?></span>
                                            </td>
                                            <td>
                                                <small class="text-muted"><?= esc($invoice['frequency']) ?></small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-primary" onclick="generateRecurringInvoice(<?= $invoice['id'] ?>)">
                                                    <i class="fas fa-file-invoice"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-transparent border-bottom">
                    <h5 class="mb-0">
                        <i class="fas fa-clock-rotate-left text-secondary me-2"></i>
                        <?= lang('Finance.quick_actions') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <a href="<?= base_url('finance/invoices') ?>" class="text-decoration-none">
                                <div class="p-3 border rounded hover-shadow">
                                    <i class="fas fa-file-invoice text-primary fa-2x mb-2"></i>
                                    <h6><?= lang('Finance.manage_invoices_card') ?></h6>
                                    <small class="text-muted"><?= lang('Finance.manage_invoices_desc') ?></small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="javascript:void(0)" onclick="batchGenerateInvoices()" class="text-decoration-none">
                                <div class="p-3 border rounded hover-shadow">
                                    <i class="fas fa-gears text-success fa-2x mb-2"></i>
                                    <h6><?= lang('Finance.batch_generate_card') ?></h6>
                                    <small class="text-muted"><?= lang('Finance.batch_generate_desc') ?></small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('marketing/spk') ?>" class="text-decoration-none">
                                <div class="p-3 border rounded hover-shadow">
                                    <i class="fas fa-link text-warning fa-2x mb-2"></i>
                                    <h6><?= lang('Finance.spk_linking_card') ?></h6>
                                    <small class="text-muted"><?= lang('Finance.spk_linking_desc') ?></small>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="<?= base_url('finance/reports') ?>" class="text-decoration-none">
                                <div class="p-3 border rounded hover-shadow">
                                    <i class="fas fa-chart-bar text-info fa-2x mb-2"></i>
                                    <h6><?= lang('Finance.reports_card') ?></h6>
                                    <small class="text-muted"><?= lang('Finance.reports_card_desc') ?></small>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Link SPK to Contract Modal -->
<div class="modal fade" id="linkContractModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-link me-2"></i><?= lang('Finance.link_spk_to_contract') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="linkContractForm">
                    <input type="hidden" id="link_spk_id" name="spk_id">
                    <input type="hidden" id="link_di_id" name="di_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Finance.select_contract_label') ?></label>
                        <select class="form-select" id="link_contract_id" name="contract_id" required>
                            <option value=""><?= lang('Finance.select_contract_placeholder') ?></option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><?= lang('Finance.bast_date_optional') ?></label>
                        <input type="date" class="form-control" id="link_bast_date" name="bast_date">
                        <small class="text-muted"><?= lang('Finance.bast_date_hint') ?></small>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <?= lang('Finance.link_spk_unlock_info') ?>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-primary" onclick="submitLinkContract()">
                    <i class="fas fa-link me-2"></i><?= lang('Finance.link_contract') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php
$financeDashJs = [
    'selectContractPh' => lang('Finance.select_contract_placeholder'),
    'disUpdated' => lang('Finance.dis_updated_unlock'),
    'errorPrefix' => lang('Finance.error_prefix_plain'),
    'linkFailed' => lang('Finance.link_contract_failed'),
    'genInvoiceTitle' => lang('Finance.generate_invoice_confirm'),
    'genInvoiceText' => lang('Finance.generate_invoice_for_schedule'),
    'yesGenerate' => lang('Finance.yes_generate'),
    'cancel' => lang('Common.cancel'),
    'invoiceGenerated' => lang('Finance.invoice_generated_number'),
    'batchTitle' => lang('Finance.generate_all_invoices_confirm'),
    'batchText' => lang('Finance.all_due_schedules_processed'),
    'yesGenerateAll' => lang('Finance.yes_generate_all'),
];
?>
<?= $this->section('javascript') ?>
<script>
const financeDashI18n = <?= json_encode($financeDashJs, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;

// Show link modal
function showLinkModal(spkId, diId) {
    document.getElementById('link_spk_id').value = spkId;
    document.getElementById('link_di_id').value = diId;
    
    // Load contracts for dropdown
    fetch('<?= base_url('marketing/kontrak/get-active-contracts') ?>')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('link_contract_id');
            select.innerHTML = '<option value="">' + financeDashI18n.selectContractPh + '</option>';
            
            if (data.success && data.data) {
                data.data.forEach(contract => {
                    const option = document.createElement('option');
                    option.value = contract.id;
                    option.textContent = `${contract.no_kontrak} - ${contract.pelanggan || contract.nama_customer}`;
                    select.appendChild(option);
                });
            }
        });
    
    const modal = new bootstrap.Modal(document.getElementById('linkContractModal'));
    modal.show();
}

// Submit link contract
function submitLinkContract() {
    const formData = new FormData(document.getElementById('linkContractForm'));
    
    fetch('<?= base_url('marketing/spk/link-to-contract') ?>', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            OptimaNotify.success(`${data.di_count} DI berhasil diupdate dan di-unlock untuk invoicing.`);
            location.reload();
        } else {
            OptimaNotify.error('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        OptimaNotify.error('Gagal menghubungkan kontrak.');
    });
}

function generateRecurringInvoice(scheduleId) {
    OptimaConfirm.generic({
        title: financeDashI18n.genInvoiceTitle,
        text: financeDashI18n.genInvoiceText,
        icon: 'question',
        confirmText: financeDashI18n.yesGenerate,
        cancelText: financeDashI18n.cancel,
        confirmButtonColor: 'primary',
        onConfirm: function() {
            const formData = new FormData();
            formData.append('schedule_id', scheduleId);
            
            fetch('<?= base_url('finance/invoices/generate-recurring') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    OptimaNotify.success(financeDashI18n.invoiceGenerated.replace('{number}', data.invoice_number));
                    location.reload();
                } else {
                    OptimaNotify.error(financeDashI18n.errorPrefix + ' ' + (data.message || ''));
                }
            });
        }
    });
}

function batchGenerateInvoices() {
    OptimaConfirm.generic({
        title: financeDashI18n.batchTitle,
        text: financeDashI18n.batchText,
        icon: 'question',
        confirmText: financeDashI18n.yesGenerateAll,
        cancelText: financeDashI18n.cancel,
        confirmButtonColor: 'primary',
        onConfirm: function() {
            fetch('<?= base_url('finance/invoices/batch-generate') ?>', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    OptimaNotify.success(data.message || '');
                    location.reload();
                } else {
                    OptimaNotify.error(financeDashI18n.errorPrefix + ' ' + (data.message || ''));
                }
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
