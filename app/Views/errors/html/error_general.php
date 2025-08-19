<!DOCTYPE html>
<html>
<head>
    <title><?= $title ?? 'Error' ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; }
        .error-container { background: #f8f9fa; padding: 20px; border-left: 4px solid #dc3545; }
        .error-title { color: #dc3545; font-size: 24px; margin-bottom: 10px; }
        .error-message { margin-bottom: 20px; }
        .error-details { background: #f1f1f1; padding: 15px; font-family: monospace; font-size: 12px; white-space: pre-wrap; }
    </style>
</head>
<body>
    <div class="error-container">
        <h1 class="error-title"><?= $title ?? 'Error' ?></h1>
        <div class="error-message">
            <?= $message ?? 'Terjadi kesalahan yang tidak diketahui.' ?>
        </div>
        <?php if (isset($details) && ENVIRONMENT === 'development'): ?>
        <details>
            <summary>Error Details (Development Mode)</summary>
            <div class="error-details"><?= $details ?></div>
        </details>
        <?php endif; ?>
        <p><a href="<?= base_url() ?>">← Kembali ke Dashboard</a></p>
    </div>
</body>
</html> 