<?= $this->extend('layouts/layout_sidebar_improved_demo') ?>

<?= $this->section('title') ?>Sidebar Improved Demo<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <h1 class="h3 mb-3 text-dark">Demo: Sidebar OPTIMA (Improved)</h1>
    <p class="text-muted mb-4">
        Preview perbaikan sidebar: transisi halus, icon + label rapi, dropdown lebih bersih. Tetap floating dan simple untuk ERP.
    </p>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title">Yang bisa dicoba</h5>
            <ul class="mb-0">
                <li>Klik ikon <strong>chevron</strong> di header sidebar untuk collapse/expand (transisi 0.35s).</li>
                <li>Hover menu item: background dan border kiri halus.</li>
                <li>Saar sidebar collapsed, hover ikon grup: flyout dropdown dengan judul section.</li>
                <li>Section heading (MARKETING, SERVICE, …) kecil dan rapi.</li>
            </ul>
        </div>
    </div>

    <div class="d-flex flex-wrap gap-2">
        <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary">Dashboard</a>
        <a href="<?= base_url('/demo/sidebar-codingnepal') ?>" class="btn btn-outline-secondary">Demo CodingNepal</a>
        <a href="<?= base_url('/demo/modernSidebar') ?>" class="btn btn-outline-secondary">Demo Modern Sidebar</a>
    </div>
</div>
<?= $this->endSection() ?>
