<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?= $this->renderSection('title') ?> - OPTIMA</title>
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Metropolis:wght@400;500;600;700;800&family=Roboto+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    
    <!-- Modern Sidebar CSS -->
    <link href="<?= base_url('assets/css/desktop/optima-sidebar-modern.css') ?>" rel="stylesheet">
    
    <!-- Custom Optima CSS -->
    <link href="<?= base_url('assets/css/desktop/optima-pro.css') ?>" rel="stylesheet">
    
    <?= $this->renderSection('css') ?>
    
    <style>
        /* Base Body Styling */
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Metropolis', sans-serif;
        }
        
        /* Main Content Area */
        .main-content-wrapper {
            margin-left: calc(256px + 2vw);
            padding: 2vw;
            transition: margin-left 0.2s;
        }
        
        /* Adjust when sidebar is collapsed */
        #nav-toggle:checked ~ * .main-content-wrapper {
            margin-left: calc(80px + 2vw);
        }
        
        /* Content Container */
        .content-container {
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.1);
            min-height: calc(100vh - 4vw);
        }
        
        /* Page Header */
        .page-header {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .page-header h1 {
            margin: 0;
            font-size: 2rem;
            font-weight: 700;
            color: #212832;
        }
        
        .page-header .breadcrumb {
            margin: 0.5rem 0 0 0;
            background: transparent;
            padding: 0;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .main-content-wrapper {
                margin-left: 0 !important;
                padding: 1rem;
                padding-top: 70px;
            }
            
            .content-container {
                padding: 1rem;
                min-height: auto;
            }
            
            .page-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    
    <!-- Include Modern Sidebar -->
    <?= $this->include('layouts/sidebar_modern') ?>
    
    <!-- Main Content Wrapper -->
    <div class="main-content-wrapper">
        <div class="content-container">
            
            <!-- Page Header -->
            <div class="page-header">
                <h1><?= $this->renderSection('page_title') ?></h1>
                <?= $this->renderSection('breadcrumb') ?>
            </div>
            
            <!-- Main Content -->
            <?= $this->renderSection('content') ?>
            
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- CSRF Token for AJAX -->
    <script>
        // Global CSRF token for AJAX requests
        window.csrfTokenName = '<?= csrf_token() ?>';
        window.csrfTokenValue = '<?= csrf_hash() ?>';
        window.base_url = '<?= base_url() ?>';
    </script>
    
    <?= $this->renderSection('javascript') ?>
    
</body>
</html>
