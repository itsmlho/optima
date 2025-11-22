<?php
/**
 * Test OTP Email Sending
 * 
 * Menggunakan endpoint Settings::testEmail via AJAX
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get email address from POST or GET parameter
$testEmail = $_POST['email'] ?? $_GET['email'] ?? 'arisaditya45@gmail.com';
$action = $_POST['action'] ?? 'form';

// Initialize variables
$emailSent = false;
$emailSuccess = false;
$emailMessage = '';
$otpCode = '';
$errorDetails = '';

// Send email if action is send
if ($action === 'send' && !empty($testEmail)) {
    // Use AJAX to call Settings::testEmail endpoint
    // We'll handle this with JavaScript instead
    $emailSent = true;
    $emailMessage = 'Processing... (Check JavaScript console for errors)';
}

// Load Email config untuk display (tanpa bootstrap penuh)
$emailConfig = [];
$emailConfigFile = __DIR__ . '/../app/Config/Email.php';
if (file_exists($emailConfigFile)) {
    $fileContent = file_get_contents($emailConfigFile);
    
    if (preg_match('/public\s+string\s+\$fromEmail\s*=\s*[\'"]([^\'";\r\n]*)/', $fileContent, $matches)) {
        $emailConfig['fromEmail'] = trim($matches[1]);
    }
    if (preg_match('/public\s+string\s+\$fromName\s*=\s*[\'"]([^\'";\r\n]*)/', $fileContent, $matches)) {
        $emailConfig['fromName'] = trim($matches[1]);
    }
    if (preg_match('/public\s+string\s+\$protocol\s*=\s*[\'"]([^\'";\r\n]*)/', $fileContent, $matches)) {
        $emailConfig['protocol'] = trim($matches[1]);
    }
    if (preg_match('/public\s+string\s+\$SMTPHost\s*=\s*[\'"]([^\'";\r\n]*)/', $fileContent, $matches)) {
        $emailConfig['SMTPHost'] = trim($matches[1]);
    }
    if (preg_match('/public\s+int\s+\$SMTPPort\s*=\s*(\d+)/', $fileContent, $matches)) {
        $emailConfig['SMTPPort'] = (int)$matches[1];
    }
    if (preg_match('/public\s+string\s+\$SMTPUser\s*=\s*[\'"]([^\'";\r\n]*)/', $fileContent, $matches)) {
        $emailConfig['SMTPUser'] = trim($matches[1]);
    }
    if (preg_match('/public\s+string\s+\$SMTPCrypto\s*=\s*[\'"]([^\'";\r\n]*)/', $fileContent, $matches)) {
        $emailConfig['SMTPCrypto'] = trim($matches[1]);
    }
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test OTP Email - OPTIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            padding: 20px;
        }
        .otp-display {
            font-size: 48px;
            font-weight: bold;
            color: #4e73df;
            background-color: #f8f9fc;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border: 3px dashed #4e73df;
            margin: 20px 0;
        }
        .btn-test {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 8px;
            transition: transform 0.2s;
        }
        .btn-test:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .config-info {
            background-color: #f8f9fc;
            padding: 15px;
            border-radius: 8px;
            border-left: 4px solid #4e73df;
        }
        pre {
            background-color: #f8f9fc;
            padding: 10px;
            border-radius: 5px;
            font-size: 12px;
            max-height: 300px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10 col-lg-8">
                <div class="card shadow">
                    <div class="card-header">
                        <h4 class="mb-0">
                            <i class="fas fa-envelope me-2"></i>Test OTP Email
                        </h4>
                        <small>Uji coba pengiriman email OTP untuk sistem OPTIMA</small>
                    </div>
                    <div class="card-body p-4">
                        <!-- Success/Error Messages -->
                        <div id="messageArea"></div>
                        
                        <!-- Email Configuration Info -->
                        <?php if (!empty($emailConfig)): ?>
                        <div class="config-info mb-4">
                            <h6 class="mb-3"><i class="fas fa-cog me-2"></i>Email Configuration:</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <strong>From Email:</strong><br>
                                    <code><?= htmlspecialchars($emailConfig['fromEmail'] ?? 'Not set') ?></code>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>From Name:</strong><br>
                                    <code><?= htmlspecialchars($emailConfig['fromName'] ?? 'Not set') ?></code>
                                </div>
                                <div class="col-md-6 mb-2">
                                    <strong>Protocol:</strong><br>
                                    <code><?= htmlspecialchars($emailConfig['protocol'] ?? 'mail') ?></code>
                                </div>
                                <?php if (($emailConfig['protocol'] ?? '') === 'smtp'): ?>
                                    <div class="col-md-6 mb-2">
                                        <strong>SMTP Host:</strong><br>
                                        <code><?= htmlspecialchars($emailConfig['SMTPHost'] ?? 'Not set') ?></code>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <strong>SMTP Port:</strong><br>
                                        <code><?= htmlspecialchars($emailConfig['SMTPPort'] ?? 'Not set') ?></code>
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <strong>SMTP User:</strong><br>
                                        <code><?= htmlspecialchars($emailConfig['SMTPUser'] ?? 'Not set') ?></code>
                                    </div>
                                    <div class="col-md-12 mb-2">
                                        <strong>SMTP Encryption:</strong><br>
                                        <code><?= htmlspecialchars($emailConfig['SMTPCrypto'] ?? 'Not set') ?></code>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Warning:</strong> Email configuration tidak ditemukan di <code>app/Config/Email.php</code>
                        </div>
                        <?php endif; ?>
                        
                        <!-- Test Form -->
                        <h6 class="mb-3"><i class="fas fa-paper-plane me-2"></i>Test Email:</h6>
                        <form method="POST" action="" id="testEmailForm">
                            <input type="hidden" name="action" value="send">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                <input type="email" 
                                       class="form-control form-control-lg" 
                                       id="email" 
                                       name="email" 
                                       value="<?= htmlspecialchars($testEmail) ?>" 
                                       placeholder="your-email@example.com" 
                                       required>
                                <div class="form-text">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Masukkan email yang ingin di-test (email Anda akan menerima OTP code)
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary btn-test text-white">
                                    <i class="fas fa-paper-plane me-2"></i>Send Test Email
                                </button>
                            </div>
                        </form>
                        
                        <hr class="my-4">
                        
                        <!-- Back Button -->
                        <div class="text-center">
                            <a href="/optima1/public/index.php/profile" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Back to Profile
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Help Text -->
                <div class="text-center mt-4">
                    <small class="text-white">
                        <i class="fas fa-question-circle me-1"></i>
                        Menggunakan endpoint Settings::testEmail (CodeIgniter controller)
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // Auto-focus email input
            const emailInput = $('#email');
            if (!emailInput.val()) {
                emailInput.val('arisaditya45@gmail.com');
            }
            emailInput.focus();
            
            // Form submission via AJAX
            $('#testEmailForm').on('submit', function(e) {
                e.preventDefault();
                
                const email = $('#email').val();
                if (!email) {
                    alert('Silakan masukkan email address terlebih dahulu!');
                    return false;
                }
                
                // Show loading
                const btn = $(this).find('button[type="submit"]');
                const originalText = btn.html();
                btn.prop('disabled', true);
                btn.html('<i class="fas fa-spinner fa-spin me-2"></i>Sending...');
                
                // Clear previous messages
                $('#messageArea').html('');
                
                // Call Settings::testEmail endpoint
                $.ajax({
                    url: '/optima1/public/index.php/settings/test-email',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        email: email,
                        test_email: email
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    success: function(response) {
                        btn.prop('disabled', false);
                        btn.html(originalText);
                        
                        if (response.success) {
                            // Success message
                            const otpCode = response.otp_code || 'XXXXXX';
                            const alertHtml = `
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Sukses!</strong> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                <div class="text-center mb-4">
                                    <h6 class="text-muted mb-2">Test OTP Code:</h6>
                                    <div class="otp-display">${otpCode}</div>
                                    <small class="text-muted">Silakan cek inbox email Anda (dan folder spam).</small>
                                </div>
                            `;
                            $('#messageArea').html(alertHtml);
                        } else {
                            // Error message
                            const errorMsg = response.error ? `<pre>${response.error}</pre>` : '';
                            const alertHtml = `
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <strong>Gagal!</strong> ${response.message}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                </div>
                                ${errorMsg ? '<div class="alert alert-warning mt-3"><strong>Detail Error:</strong>' + errorMsg + '</div>' : ''}
                            `;
                            $('#messageArea').html(alertHtml);
                        }
                    },
                    error: function(xhr, status, error) {
                        btn.prop('disabled', false);
                        btn.html(originalText);
                        
                        let errorMsg = 'Terjadi kesalahan saat mengirim email.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            errorMsg = 'Error: ' + xhr.responseText.substring(0, 200);
                        }
                        
                        const alertHtml = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle me-2"></i>
                                <strong>Error!</strong> ${errorMsg}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <div class="alert alert-warning mt-3">
                                <strong>Debug Info:</strong><br>
                                Status: ${status}<br>
                                Error: ${error}<br>
                                Response: ${xhr.responseText ? xhr.responseText.substring(0, 500) : 'No response'}
                            </div>
                        `;
                        $('#messageArea').html(alertHtml);
                    }
                });
            });
        });
    </script>
</body>
</html>
