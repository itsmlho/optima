<?= $this->extend('layouts/layout_codingnepal_demo') ?>

<?= $this->section('title') ?>CodingNepal Sidebar Demo<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="cn-demo-content" style="max-width: 800px; margin: 0 auto;">
    <h1 style="color: #151A2D; font-size: 2rem; margin-bottom: 0.5rem;">CodingNepal Sidebar Demo</h1>
    <p style="color: #495057; line-height: 1.6; margin-bottom: 0.5rem;">
        Sidebar bergaya CodingNepal dengan desain floating dan animasi smooth. Tidak ada ketergantungan session.
    </p>

    <div style="background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 12px rgba(0,0,0,0.08); margin-bottom: 0.5rem;">
        <h2 style="color: #151A2D; font-size: 1.25rem; margin-bottom: 0.75rem;">Fitur</h2>
        <ul style="color: #495057; line-height: 1.8; padding-left: 1.25rem;">
            <li>Floating sidebar dengan lebar 270px (expanded) / 85px (collapsed)</li>
            <li>Dropdown menu dengan animasi smooth</li>
            <li>Hover flyout saat sidebar collapsed</li>
            <li>Responsive - mobile menu button</li>
            <li>Material Symbols icons</li>
            <li>Tanpa session - menu statis</li>
        </ul>
    </div>

    <div class="cn-demo-actions" style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="<?= base_url('/dashboard') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: #0061f2; color: #fff; border-radius: 8px; text-decoration: none; font-weight: 500;">Dashboard</a>
        <a href="<?= base_url('/marketing/quotations') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: #EEF2FF; color: #151A2D; border-radius: 8px; text-decoration: none; font-weight: 500;">Quotations</a>
        <a href="<?= base_url('/demo/modernSidebar') ?>" style="display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; background: #e9ecef; color: #495057; border-radius: 8px; text-decoration: none; font-weight: 500;">Modern Sidebar Demo</a>
    </div>
</div>
<?= $this->endSection() ?>
