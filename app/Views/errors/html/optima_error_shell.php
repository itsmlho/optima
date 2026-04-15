<?php
/**
 * Shared branded shell for HTTP error pages (404, 403, 400, etc.).
 *
 * Expected variables (set by each error_*.php before include):
 * @var string      $htmlTitle
 * @var string      $errorDisplayCode Short code shown large (e.g. 404)
 * @var string      $heading     Short heading under the code
 * @var string      $messageHtml Safe HTML for the main message (already escaped)
 * @var string      $homeUrl     Primary button URL
 * @var string      $homeLabel   Primary button label
 * @var bool        $showLogin   If true, show secondary link to login
 */
$htmlTitle   = $htmlTitle ?? 'Error';
$displayCode = $errorDisplayCode ?? (isset($code) && is_numeric($code) ? (string) (int) $code : '—');
$heading     = $heading ?? '';
$messageHtml = $messageHtml ?? '';
$homeUrl     = $homeUrl ?? base_url();
$homeLabel   = $homeLabel ?? lang('Errors.backHome');
$showLogin   = !empty($showLogin);
?>
<!DOCTYPE html>
<html lang="<?= esc(in_array(service('request')->getLocale(), ['id', 'en'], true) ? service('request')->getLocale() : 'id') ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title><?= esc($htmlTitle) ?> | OPTIMA</title>
    <link rel="icon" type="image/x-icon" href="<?= base_url('favicon.ico') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            background: linear-gradient(145deg, #eef2f7 0%, #e2e8f0 45%, #f8fafc 100%);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .optima-error-card {
            max-width: 520px;
            border: none;
            border-radius: 20px;
            box-shadow: 0 12px 40px rgba(15, 23, 42, 0.08);
        }
        .optima-error-code {
            font-size: clamp(3rem, 10vw, 4.25rem);
            font-weight: 800;
            line-height: 1;
            color: #0061f2;
            letter-spacing: -0.06em;
        }
        .optima-error-brand {
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container py-5 px-3">
        <div class="card optima-error-card mx-auto text-center p-4 p-md-5 bg-white">
            <div class="optima-error-brand mb-3">
                <img src="<?= base_url('assets/images/company-logo.png') ?>" alt="SML" height="28" class="me-2" onerror="this.style.display='none'">
                <img src="<?= base_url('assets/images/logo-optima.png') ?>" alt="OPTIMA" height="28" onerror="this.style.display='none'">
            </div>
            <div class="optima-error-code mb-2"><?= esc((string) $displayCode) ?></div>
            <h1 class="h4 fw-bold text-dark mb-3"><?= esc($heading) ?></h1>
            <div class="text-muted mb-4 text-start small"><?= $messageHtml ?></div>
            <div class="d-flex flex-wrap gap-2 justify-content-center">
                <a class="btn btn-primary px-4" href="<?= esc($homeUrl) ?>"><?= esc($homeLabel) ?></a>
                <?php if ($showLogin): ?>
                    <a class="btn btn-outline-secondary px-4" href="<?= base_url('auth/login') ?>"><?= esc(lang('Errors.signIn')) ?></a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
