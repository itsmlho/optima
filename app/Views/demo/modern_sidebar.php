<?= $this->extend('layouts/layout_modern') ?>

<?= $this->section('title') ?>Modern Sidebar Demo<?= $this->endSection() ?>

<?= $this->section('page_title') ?>Modern Sidebar Demo<?= $this->endSection() ?>

<?= $this->section('breadcrumb') ?>
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Home</a></li>
        <li class="breadcrumb-item active" aria-current="page">Modern Sidebar Demo</li>
    </ol>
</nav>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="row">
    <div class="col-12">
        <div class="alert alert-info">
            <h5 class="alert-heading">
                <i class="fas fa-info-circle"></i> Modern Sidebar Installed Successfully!
            </h5>
            <p class="mb-0">
                Sidebar baru dengan desain modern dari CodePen telah berhasil diintegrasikan ke aplikasi OPTIMA Anda.
            </p>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-check-circle"></i> Features</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        <strong>Collapsible Sidebar</strong> - Toggle antara full (256px) dan minimal (80px) mode
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        <strong>Active Highlight</strong> - Gradient highlight untuk item yang aktif
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        <strong>Smooth Animations</strong> - Transisi halus dengan CSS3
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        <strong>User Profile Footer</strong> - Expandable user menu di footer
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        <strong>Fully Responsive</strong> - Mobile-friendly dengan overlay
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        <strong>Font Awesome Icons</strong> - Modern icon set
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-book"></i> How to Use</h5>
            </div>
            <div class="card-body">
                <h6 class="text-primary">1. Gunakan Layout Modern</h6>
                <pre class="bg-light p-3 rounded"><code>&lt;?= $this->extend('layouts/layout_modern') ?&gt;</code></pre>

                <h6 class="text-primary mt-3">2. Toggle Sidebar</h6>
                <p class="mb-2">Klik tombol toggle di header sidebar untuk collapse/expand</p>

                <h6 class="text-primary mt-3">3. User Profile Menu</h6>
                <p class="mb-2">Klik icon caret di footer untuk expand user menu</p>

                <h6 class="text-primary mt-3">4. Mobile Mode</h6>
                <p class="mb-0">Pada layar kecil, gunakan hamburger button untuk toggle sidebar</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-code"></i> Implementation</h5>
            </div>
            <div class="card-body">
                <h6 class="text-primary">View File Structure:</h6>
                <pre class="bg-light p-3 rounded"><code>app/Views/layouts/
├── layout_modern.php       # Layout utama dengan sidebar modern
├── sidebar_modern.php      # Sidebar component
└── base.php               # Layout lama (masih bisa digunakan)

public/assets/css/desktop/
└── optima-sidebar-modern.css  # Stylesheet untuk sidebar modern</code></pre>

                <h6 class="text-primary mt-3">Contoh Penggunaan di Controller:</h6>
                <pre class="bg-light p-3 rounded"><code>public function index()
{
    return view('your_view', [
        'title' => 'Your Page Title',
        'data' => $yourData
    ]);
}
</code></pre>

                <h6 class="text-primary mt-3">Contoh View File:</h6>
                <pre class="bg-light p-3 rounded"><code>&lt;?= $this->extend('layouts/layout_modern') ?&gt;

&lt;?= $this->section('title') ?&gt;Page Title&lt;?= $this->endSection() ?&gt;

&lt;?= $this->section('page_title') ?&gt;Your Page Title&lt;?= $this->endSection() ?&gt;

&lt;?= $this->section('content') ?&gt;
    &lt;!-- Your content here --&gt;
&lt;?= $this->endSection() ?&gt;
</code></pre>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-palette"></i> Customization</h5>
            </div>
            <div class="card-body">
                <h6 class="text-primary">Color Variables (di optima-sidebar-modern.css):</h6>
                <pre class="bg-light p-3 rounded"><code>:root {
    --navbar-width: 256px;              /* Lebar sidebar full */
    --navbar-width-min: 80px;           /* Lebar sidebar collapsed */
    --navbar-dark-primary: #18283b;     /* Background utama */
    --navbar-dark-secondary: #2c3e50;   /* Background secondary */
    --navbar-light-primary: #f5f6fa;    /* Text primary */
    --navbar-light-secondary: #8392a5;  /* Text secondary */
}</code></pre>

                <div class="alert alert-warning mt-3">
                    <i class="fas fa-lightbulb"></i> 
                    <strong>Tips:</strong> Anda bisa mengubah nilai-nilai di atas untuk menyesuaikan warna dengan brand Optima Anda!
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-12">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h4 class="text-primary mb-3">
                    <i class="fas fa-rocket"></i> Sidebar Modern Siap Digunakan!
                </h4>
                <p class="text-muted mb-4">
                    Silakan gunakan layout_modern.php untuk halaman-halaman baru Anda, atau migrate halaman existing ke layout baru ini.
                </p>
                <div class="btn-group" role="group">
                    <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt"></i> Ke Dashboard
                    </a>
                    <a href="<?= base_url('/units') ?>" class="btn btn-outline-primary">
                        <i class="fas fa-truck"></i> Ke Units
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('javascript') ?>
<script>
// Demo notification
$(document).ready(function() {
    console.log('Modern Sidebar Demo Page Loaded!');
    console.log('CSRF Token:', window.csrfTokenName, '=', window.csrfTokenValue);
});
</script>
<?= $this->endSection() ?>
