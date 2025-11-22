<?php
/**
 * Test OTP Email Sending - Simple Version
 * 
 * Versi sederhana dengan bootstrap CodeIgniter yang benar
 */

// Path constants (must be defined before bootstrap)
define('ROOTPATH', __DIR__ . '/../');
define('FCPATH', __DIR__ . '/');
define('SYSTEMPATH', ROOTPATH . 'vendor/codeigniter4/framework/system/');
define('APPPATH', ROOTPATH . 'app/');
define('WRITEPATH', ROOTPATH . 'writable/');

// Bootstrap CodeIgniter properly
require_once SYSTEMPATH . 'bootstrap.php';

// Get email address from POST or GET parameter
$testEmail = $_POST['email'] ?? '';
$action = $_POST['action'] ?? 'form';

// Initialize variables
$emailSent = false;
$emailSuccess = false;
$emailMessage = '';
$otpCode = '';
$errorDetails = '';

// Get CodeIgniter instance
$app = Config\Services::codeigniter();
$app->initialize();

// Load Email config
try {
    $emailConfig = config('Email');
} catch (\Exception $e) {
    $emailConfig = null;
    $errorDetails = $e->getMessage();
}

if ($action === 'send' && !empty($testEmail)) {
    if (!$emailConfig) {
        $emailSuccess = false;
        $emailMessage = 'Email configuration tidak ditemukan! ' . ($errorDetails ? 'Error: ' . $errorDetails : '');
    } elseif (empty($emailConfig->fromEmail)) {
        $emailSuccess = false;
        $emailMessage = 'From Email belum dikonfigurasi di app/Config/Email.php';
    } else {
        try {
            $emailService = \Config\Services::email();
            
            // Configure email
            $emailService->setFrom($emailConfig->fromEmail, $emailConfig->fromName ?? 'OPTIMA System');
            $emailService->setTo($testEmail);
            
            // Generate OTP
            $otpCode = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
            
            // Simple HTML message
            $message = "<h2>Test OTP Email - OPTIMA</h2>
                       <p>Test OTP Code: <strong style='font-size:24px;color:#4e73df;'>{$otpCode}</strong></p>
                       <p>Jika Anda menerima email ini, konfigurasi email sudah benar!</p>";
            
            $emailService->setSubject('Test OTP Email - OPTIMA');
            $emailService->setMessage($message);
            
            $emailSent = true;
            if ($emailService->send()) {
                $emailSuccess = true;
                $emailMessage = "Email berhasil dikirim ke {$testEmail}";
            } else {
                $emailSuccess = false;
                $errorDetails = $emailService->printDebugger();
                $emailMessage = 'Email gagal dikirim. Cek error detail di bawah.';
            }
        } catch (\Exception $e) {
            $emailSent = true;
            $emailSuccess = false;
            $emailMessage = 'Error: ' . $e->getMessage();
            $errorDetails = $e->getFile() . ':' . $e->getLine() . ' - ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Test OTP Email - OPTIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fc; padding: 20px; }
        .card { max-width: 800px; margin: 0 auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5>Test OTP Email (Simple Version)</h5>
            </div>
            <div class="card-body">
                <?php if ($emailSent): ?>
                    <?php if ($emailSuccess): ?>
                        <div class="alert alert-success">
                            <strong>Sukses!</strong> <?= htmlspecialchars($emailMessage) ?><br>
                            <strong>OTP Code:</strong> <span style="font-size:32px;color:#4e73df;"><?= htmlspecialchars($otpCode) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <strong>Gagal!</strong> <?= htmlspecialchars($emailMessage) ?>
                            <?php if ($errorDetails): ?>
                                <pre style="background:#f0f0f0;padding:10px;margin-top:10px;overflow:auto;font-size:11px;"><?= htmlspecialchars($errorDetails) ?></pre>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <hr>
                <?php endif; ?>
                
                <?php if ($emailConfig): ?>
                <div class="mb-4">
                    <h6>Email Configuration:</h6>
                    <ul>
                        <li>From: <code><?= htmlspecialchars($emailConfig->fromEmail) ?></code></li>
                        <li>Protocol: <code><?= htmlspecialchars($emailConfig->protocol) ?></code></li>
                        <?php if ($emailConfig->protocol === 'smtp'): ?>
                            <li>SMTP Host: <code><?= htmlspecialchars($emailConfig->SMTPHost) ?></code></li>
                            <li>SMTP Port: <code><?= htmlspecialchars($emailConfig->SMTPPort) ?></code></li>
                            <li>SMTP User: <code><?= htmlspecialchars($emailConfig->SMTPUser) ?></code></li>
                            <li>SMTP Encryption: <code><?= htmlspecialchars($emailConfig->SMTPCrypto) ?></code></li>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <strong>Warning:</strong> Email configuration tidak ditemukan atau error!
                    <?php if ($errorDetails): ?>
                        <br><small>Error: <?= htmlspecialchars($errorDetails) ?></small>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <input type="hidden" name="action" value="send">
                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($testEmail) ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Send Test Email</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
