<?php
/**
 * Form publik masukan / keluh kesah untuk perusahaan (bukan tiket aplikasi OPTIMA).
 *
 * @var string $title
 * @var string $companyName
 * @var string $logoUrl
 * @var string $formAction
 */
$companyName = $companyName ?? 'PT Sarana Mitra Luas Tbk';
$logoUrl     = $logoUrl ?? base_url('assets/images/company-logo.svg');
$formAction  = $formAction ?? base_url('masukan-keluhan/kirim');
$errors     = session()->getFlashdata('errors') ?? null;
$success    = session()->getFlashdata('success');
$errorFlash = session()->getFlashdata('error');
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Masukan & Keluh Kesah') ?></title>
    <link rel="icon" href="<?= esc(base_url('favicon.ico')) ?>" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --mk-navy: #1a365d;
            --mk-bg: #eef2f7;
        }
        body {
            min-height: 100vh;
            background: linear-gradient(165deg, var(--mk-bg) 0%, #e2e8f0 100%);
            font-family: 'Segoe UI', system-ui, sans-serif;
        }
        .mk-shell { max-width: 640px; margin: 0 auto; }
        .mk-card {
            border-radius: 16px;
            box-shadow: 0 12px 40px rgba(26, 54, 93, 0.12);
            border: none;
        }
        .mk-badge {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.04em;
            color: var(--mk-navy);
            background: rgba(26, 54, 93, 0.08);
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
        }
        .mk-honeypot {
            position: absolute !important;
            left: -9999px !important;
            height: 0 !important;
            overflow: hidden !important;
        }
    </style>
</head>
<body class="py-4 px-3">
    <div class="mk-shell">
        <div class="text-center mb-4">
            <img src="<?= esc($logoUrl) ?>" alt="" width="120" height="auto" class="mb-3">
            <h1 class="h4 mb-1" style="color: var(--mk-navy);"><?= esc($companyName) ?></h1>
            <p class="text-muted mb-2 small">Masukan &amp; keluh kesah untuk perusahaan</p>
            <span class="mk-badge">Tanpa login · Opsional kontak jika ingin dihubungi</span>
        </div>

        <?php if ($success): ?>
            <div class="alert alert-success mk-card border-0"><?= esc($success) ?></div>
        <?php endif; ?>
        <?php if ($errorFlash): ?>
            <div class="alert alert-danger mk-card border-0"><?= esc($errorFlash) ?></div>
        <?php endif; ?>

        <div class="card mk-card">
            <div class="card-body p-4">
                <p class="small text-muted mb-4">
                    Form ini ditujukan kepada <strong>pimpinan / HRD</strong> perihal lingkungan kerja, kebijakan, atau hal lain terkait perusahaan.
                    Ini <strong>bukan</strong> saluran bantuan teknis aplikasi OPTIMA.
                </p>

                <form action="<?= esc($formAction) ?>" method="post" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-3 mk-honeypot" aria-hidden="true">
                        <label for="company_website">Website</label>
                        <input type="text" name="company_website" id="company_website" tabindex="-1" autocomplete="off">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Jenis <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_masukan" value="masukan" <?= old('type') === 'masukan' ? 'checked' : '' ?> required>
                                <label class="form-check-label" for="type_masukan">Masukan</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_keluhan" value="keluh_kesah" <?= old('type') === 'keluh_kesah' ? 'checked' : '' ?>>
                                <label class="form-check-label" for="type_keluhan">Keluh kesah</label>
                            </div>
                        </div>
                        <?php if (! empty($errors['type'])): ?>
                            <div class="text-danger small mt-1"><?= esc($errors['type']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label fw-semibold">Pesan <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="message" id="message" rows="7" required placeholder="Tuliskan masukan atau keluhan Anda dengan jelas." minlength="10" maxlength="10000"><?= esc(old('message')) ?></textarea>
                        <div class="form-text">Minimal 10 karakter. Anda tidak perlu mencantumkan nama.</div>
                        <?php if (! empty($errors['message'])): ?>
                            <div class="text-danger small mt-1"><?= esc($errors['message']) ?></div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-4">

                    <p class="small text-muted mb-3">Kontak di bawah ini <strong>opsional</strong>. Isi hanya jika Anda bersedia dihubungi untuk tindak lanjut.</p>

                    <div class="mb-3">
                        <label for="contact_email" class="form-label">Email (opsional)</label>
                        <input type="email" class="form-control" name="contact_email" id="contact_email" value="<?= esc(old('contact_email')) ?>" maxlength="255" placeholder="nama@email.com" autocomplete="email">
                        <?php if (! empty($errors['contact_email'])): ?>
                            <div class="text-danger small mt-1"><?= esc($errors['contact_email']) ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <label for="contact_phone" class="form-label">Nomor telepon (opsional)</label>
                        <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="<?= esc(old('contact_phone')) ?>" maxlength="50" placeholder="08…" autocomplete="tel">
                        <?php if (! empty($errors['contact_phone'])): ?>
                            <div class="text-danger small mt-1"><?= esc($errors['contact_phone']) ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">Kirim</button>
                </form>
            </div>
        </div>

        <p class="text-center text-muted small mt-4 mb-0">Terima kasih atas masukan Anda.</p>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
