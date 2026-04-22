<?php
$lines = file('C:/laragon/www/optima/app/Views/service/spk_service.php');
$inJs = false;
$issues = [];
foreach ($lines as $i => $l) {
    $lineNum = $i + 1;
    if (strpos($l, '<script') !== false && strpos($l, 'src=') === false) $inJs = true;
    if (strpos($l, '</script>') !== false) { $inJs = false; continue; }
    if (!$inJs) continue;

    $trimmed = trim($l);
    // Check for apostrophe in single-quoted notify/alert/confirm strings
    if (preg_match("/(?:notify|alert|confirm|warn|error)\s*\(\s*'([^']*)'/", $trimmed, $m)) {
        if (preg_match("/[a-z]'[a-z]/i", $m[1])) {
            $issues[] = "APOSTROPHE in notify/alert Line $lineNum: $trimmed";
        }
    }
    // Check for unmatched backtick (single on a line that's not clearly a template literal)
    $backticks = substr_count($trimmed, '`');
    if ($backticks % 2 !== 0 && !str_starts_with($trimmed, '//') && !str_starts_with($trimmed, '*')) {
        // Skip lines that clearly are inside template literals (they should have odd backtick count)
        // Only flag if the line starts/ends with backtick unexpectedly
        if (preg_match('/[;,]\s*`\s*$/', $trimmed) || preg_match('/^`[^`]*$/', $trimmed)) {
            // possible open backtick - this is normal
        }
    }
    // Check for base_url inside a single-quoted JS string
    if (preg_match("/'\s*<\?=.*base_url.*\?>\s*'/", $l)) {
        $issues[] = "PHP in single-quoted string Line $lineNum: $trimmed";
    }
}

if (empty($issues)) {
    echo "No obvious issues found in JS strings.\n";
} else {
    foreach ($issues as $issue) {
        echo $issue . "\n";
    }
}

// Also check: count opening and closing </script> to make sure blocks match
$scriptOpen = 0; $scriptClose = 0;
foreach ($lines as $l) {
    if (preg_match('/<script[^>]*>/', $l) && strpos($l, 'src=') === false) $scriptOpen++;
    if (strpos($l, '</script>') !== false) $scriptClose++;
}
echo "\nScript blocks: $scriptOpen open, $scriptClose close\n";
