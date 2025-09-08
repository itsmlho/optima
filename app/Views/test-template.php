<?= $this->extend('layouts/base') ?>

<?= $this->section('css') ?>
<style>
    .test-page { padding: 2rem; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="test-page">
    <h1>Test Template Page</h1>
    <p>Jika Anda melihat halaman ini dengan styling Bootstrap, template system berfungsi normal.</p>
    <div class="alert alert-success">Template inheritance berhasil!</div>
</div>
<?= $this->endSection() ?>
