<?= $this->extend('layouts/base') ?>
<?= $this->section('content') ?>
<div class="container-fluid py-3">
    <h5 class="mb-3"><i class="fas fa-calendar-plus me-2"></i>Booking Unit (Placeholder)</h5>
    <div class="alert alert-info small">Halaman booking masih placeholder. Param unit: <?= esc($this->request->getGet('unit') ?? '-') ?></div>
    <p class="text-muted small">Implementasi form booking, validasi ketersediaan, dan penyimpanan akan ditambahkan.</p>
    <?= ui_button('back', 'Kembali', [
        'href' => base_url('marketing/available-units'),
        'color' => 'secondary',
        'size' => 'sm'
    ]) ?>
</div>
<?= $this->endSection() ?>
