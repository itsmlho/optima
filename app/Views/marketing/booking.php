<?= $this->extend('layouts/base') ?>

<?php
/**
 * Booking Unit - Marketing (placeholder)
 * CARD: Optional card wrap; no badges/tables yet.
 */
?>
<?= $this->section('content') ?>
<div class="container-fluid py-3">
    <div class="card">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="fas fa-calendar-plus me-2"></i>Booking Unit (Placeholder)</h5>
        </div>
        <div class="card-body">
    <p class="text-muted small mb-0">Halaman booking masih placeholder. Param unit: <?= esc($this->request->getGet('unit') ?? '-') ?></p>
    <p class="text-muted small mt-2">Implementasi form booking, validasi ketersediaan, dan penyimpanan akan ditambahkan.</p>
    <?= ui_button('back', '', [
        'href' => base_url('marketing/available-units'),
        'color' => 'secondary',
        'size' => 'sm'
    ]) ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
