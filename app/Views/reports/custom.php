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

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="fw-bold mb-3"><?= esc($page_title) ?></h4>
            <p class="text-muted"><?= esc(lang('Reports.custom_intro')) ?></p>
            <a href="<?= base_url('reports') ?>" class="btn btn-primary">
                <i class="fas fa-chart-bar me-1"></i><?= esc(lang('Reports.custom_cta')) ?>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-semibold"><?= esc(lang('Reports.section_named_reports')) ?> (templates)</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($report_templates as $key => $label): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= esc($label) ?></span>
                            <code class="small"><?= esc($key) ?></code>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card h-100 shadow-sm">
                <div class="card-header fw-semibold">Data sources</div>
                <ul class="list-group list-group-flush">
                    <?php foreach ($data_sources as $key => $label): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= esc($label) ?></span>
                            <code class="small"><?= esc($key) ?></code>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
