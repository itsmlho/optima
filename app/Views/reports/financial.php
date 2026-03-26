<?= $this->extend('layouts/base') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb mb-0">
            <?php foreach ($breadcrumbs as $url => $label): ?>
                <?php if ($url === array_key_last($breadcrumbs)): ?>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($label) ?></li>
                <?php else: ?>
                    <?php $bcHref = $url === '/' ? base_url() : base_url(ltrim($url, '/')); ?>
                    <li class="breadcrumb-item"><a href="<?= esc($bcHref) ?>"><?= esc($label) ?></a></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ol>
    </nav>

    <div class="alert alert-info">
        <strong><?= esc(lang('Reports.hub_intro_title')) ?></strong><br>
        <?= esc(lang('Reports.hub_intro_body')) ?>
    </div>

    <h4 class="fw-bold mb-3"><i class="fas fa-coins me-2 text-success"></i><?= esc($page_title) ?></h4>

    <div class="mb-3">
        <a href="<?= base_url('finance/reports') ?>" class="btn btn-outline-success btn-sm">
            <i class="fas fa-external-link-alt me-1"></i><?= esc(lang('Reports.btn_finance_module')) ?>
        </a>
    </div>

    <?php if (!empty($financial_data['summary'])): ?>
        <div class="row mb-4">
            <?php foreach ($financial_data['summary'] as $k => $v): ?>
                <div class="col-md-3 mb-2">
                    <div class="card shadow-sm h-100">
                        <div class="card-body py-2">
                            <div class="text-muted small text-uppercase"><?= esc(str_replace('_', ' ', (string) $k)) ?></div>
                            <div class="fs-5 fw-semibold"><?= esc(is_scalar($v) ? (string) $v : json_encode($v)) ?></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <h6 class="text-uppercase text-muted mb-2"><?= esc(lang('Reports.section_named_reports')) ?></h6>
    <div class="row">
        <?php foreach ($category_reports as $item): ?>
            <div class="col-lg-6 mb-3">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex flex-column flex-md-row justify-content-between gap-2">
                        <div>
                            <h6 class="mb-1"><?= esc($item['title']) ?></h6>
                            <p class="text-muted small mb-0"><?= esc($item['description']) ?></p>
                        </div>
                        <div class="align-self-md-center">
                            <button type="button" class="btn btn-success btn-sm" data-report-type="<?= esc($item['id'], 'attr') ?>">
                                <i class="fas fa-file-excel me-1"></i><?= esc(lang('Reports.btn_generate_excel')) ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-3">
        <a href="<?= base_url('reports') ?>" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i><?= esc(lang('Reports.btn_all_reports')) ?>
        </a>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
(function () {
    function postQuick(type) {
        const body = new URLSearchParams();
        if (window.csrfTokenName && window.csrfToken) {
            body.append(window.csrfTokenName, window.csrfToken);
        }
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
        fetch('<?= base_url('reports/quick/') ?>' + encodeURIComponent(type), {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': token
            },
            body: body.toString()
        }).then(r => r.json()).then(data => {
            if (data.success && data.download_url) {
                window.open(data.download_url, '_blank');
                setTimeout(() => location.reload(), 800);
            } else {
                alert(data.message || 'Failed');
            }
        }).catch(() => alert('Request failed'));
    }
    document.querySelectorAll('[data-report-type]').forEach(btn => {
        btn.addEventListener('click', () => postQuick(btn.getAttribute('data-report-type')));
    });
})();
</script>
<?= $this->endSection() ?>
