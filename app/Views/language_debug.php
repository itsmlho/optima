<!DOCTYPE html>
<html lang="<?= service('request')->getLocale() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Language Debug - OPTIMA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        .debug-section {
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #f8f9fa;
        }
        .status-good { color: #28a745; }
        .status-bad { color: #dc3545; }
        .code-block {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 10px;
            font-family: monospace;
            font-size: 0.9em;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row">
            <div class="col-12">
                <div class="card shadow">
                    <div class="card-header bg-danger text-white">
                        <h2><i class="fas fa-bug me-2"></i>Language System Debug Panel</h2>
                    </div>
                    <div class="card-body">
                        
                        <?php if (session()->getFlashdata('success')): ?>
                            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
                        <?php endif; ?>
                        
                        <!-- Current Status -->
                        <div class="debug-section">
                            <h4><i class="fas fa-info-circle me-2"></i>Current Locale Status</h4>
                            <table class="table table-sm">
                                <tr>
                                    <td width="250"><strong>Request Locale:</strong></td>
                                    <td><code class="<?= $request_locale === $session_language ? 'status-good' : 'status-bad' ?>"><?= $request_locale ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>Language Service Locale:</strong></td>
                                    <td><code class="<?= $language_service_locale === $session_language ? 'status-good' : 'status-bad' ?>"><?= $language_service_locale ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>Session Language:</strong></td>
                                    <td><code><?= $session_language ?? '<span class="status-bad">NOT SET</span>' ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>Config Default:</strong></td>
                                    <td><code><?= $config_default ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>Config Supported:</strong></td>
                                    <td><code><?= implode(', ', $config_supported) ?></code></td>
                                </tr>
                            </table>
                            
                            <?php if ($request_locale === $language_service_locale && $request_locale === $session_language): ?>
                                <div class="alert alert-success mb-0">
                                    <i class="fas fa-check-circle me-2"></i>All locales are synchronized! ✓
                                </div>
                            <?php else: ?>
                                <div class="alert alert-danger mb-0">
                                    <i class="fas fa-exclamation-triangle me-2"></i>Locales are NOT synchronized! This is the problem.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Manual Language Switch -->
                        <div class="debug-section">
                            <h4><i class="fas fa-exchange-alt me-2"></i>Manual Language Switch (Direct Control)</h4>
                            <div class="btn-group w-100 mb-3" role="group">
                                <a href="<?= base_url('language-debug/set-manual/id') ?>" class="btn btn-lg <?= $session_language === 'id' ? 'btn-primary' : 'btn-outline-primary' ?>">
                                    🇮🇩 Set to Indonesian (ID)
                                    <?php if ($session_language === 'id'): ?><i class="fas fa-check ms-2"></i><?php endif; ?>
                                </a>
                                <a href="<?= base_url('language-debug/set-manual/en') ?>" class="btn btn-lg <?= $session_language === 'en' ? 'btn-success' : 'btn-outline-success' ?>">
                                    🇬🇧 Set to English (EN)
                                    <?php if ($session_language === 'en'): ?><i class="fas fa-check ms-2"></i><?php endif; ?>
                                </a>
                            </div>
                            <a href="<?= base_url('language-debug?clear=1') ?>" class="btn btn-warning w-100">
                                <i class="fas fa-trash me-2"></i>Clear Session & Reset
                            </a>
                        </div>
                        
                        <!-- Translation Tests -->
                        <div class="debug-section">
                            <h4><i class="fas fa-language me-2"></i>Translation Output Tests</h4>
                            
                            <div class="alert alert-warning mb-3">
                                <strong>Expected for Indonesian (ID):</strong> "Total Unit", "Kontrak Aktif", "dari bulan lalu"<br>
                                <strong>Expected for English (EN):</strong> "Total Units", "Active Contracts", "from last month"
                            </div>
                            
                            <table class="table table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="300">Translation Key</th>
                                        <th>Current Output</th>
                                        <th width="250">Expected (<?= $session_language ?? 'N/A' ?>)</th>
                                        <th width="80">Match</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($current_translations as $key => $value): ?>
                                        <?php 
                                        $keyParts = explode('.', $key);
                                        $module = $keyParts[0] ?? '';
                                        $translationKey = $keyParts[1] ?? '';
                                        $expected = $expected_translations[$session_language ?? 'id'][$key] ?? 'N/A';
                                        $matches = ($value === $expected);
                                        ?>
                                        <tr class="<?= $matches ? 'table-success' : 'table-danger' ?>">
                                            <td><code><?= $key ?></code></td>
                                            <td><strong><?= esc($value) ?></strong></td>
                                            <td><?= esc($expected) ?></td>
                                            <td class="text-center">
                                                <?php if ($matches): ?>
                                                    <i class="fas fa-check-circle text-success fa-2x"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-times-circle text-danger fa-2x"></i>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- File Check -->
                        <div class="debug-section">
                            <h4><i class="fas fa-folder me-2"></i>Language Files Check</h4>
                            <?php
                            $files = [
                                'id' => [
                                    'Dashboard' => APPPATH . 'Language/id/Dashboard.php',
                                    'Auth' => APPPATH . 'Language/id/Auth.php',
                                    'App' => APPPATH . 'Language/id/App.php',
                                ],
                                'en' => [
                                    'Dashboard' => APPPATH . 'Language/en/Dashboard.php',
                                    'Auth' => APPPATH . 'Language/en/Auth.php',
                                    'App' => APPPATH . 'Language/en/App.php',
                                ]
                            ];
                            ?>
                            <div class="row">
                                <?php foreach ($files as $locale => $localeFiles): ?>
                                    <div class="col-md-6">
                                        <h5><?= $locale === 'id' ? '🇮🇩 Indonesian' : '🇬🇧 English' ?></h5>
                                        <table class="table table-sm">
                                            <?php foreach ($localeFiles as $name => $path): ?>
                                                <tr>
                                                    <td><?= $name ?>.php</td>
                                                    <td>
                                                        <?php if (file_exists($path)): ?>
                                                            <span class="badge bg-success">✓ Exists (<?= number_format(filesize($path)) ?> bytes)</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-danger">✗ Missing</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </table>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        
                        <!-- PHP Info -->
                        <div class="debug-section">
                            <h4><i class="fas fa-code me-2"></i>PHP Debug Info</h4>
                            <div class="code-block">
                                <strong>BaseController setupLanguage() called:</strong> YES<br>
                                <strong>Session Driver:</strong> <?= config('Session')->driver ?? 'FileHandler' ?><br>
                                <strong>Session Save Path:</strong> <?= config('Session')->savePath ?? WRITEPATH . 'session' ?><br>
                                <strong>Session Cookie Name:</strong> <?= config('Session')->cookieName ?? 'ci_session' ?><br>
                                <strong>Current Session ID:</strong> <?= session_id() ?: 'Not started' ?>
                            </div>
                        </div>
                        
                        <!-- Quick Links -->
                        <div class="text-center mt-4">
                            <a href="<?= base_url('dashboard') ?>" class="btn btn-primary btn-lg">
                                <i class="fas fa-tachometer-alt me-2"></i>Test on Real Dashboard
                            </a>
                            <a href="<?= base_url('language/test') ?>" class="btn btn-info btn-lg">
                                <i class="fas fa-flask me-2"></i>Original Test Page
                            </a>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
