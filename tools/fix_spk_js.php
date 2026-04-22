<?php
$file = 'C:/laragon/www/optima/app/Views/service/spk_service.php';
$content = file_get_contents($file);

// The broken pattern: data.map callback not closed with ) and .join('')
// Lines 5311-5322 of original file - replace the broken structure
$old = "\t\t\t\t\t\t\tdata.map(item => {\n" .
       "\t\t\t\t\t\t\t\tconst name = `\${item.tipe||'-'} \${item.merk||'-'} \${item.model||''}`.trim();\n" .
       "\t\t\t\t\t\treturn `<option value=\"\${item.id}\">\${name} \xe2\x80\xa2 SN: \${item.sn_attachment||'-'}</option>`;\n" .
       "\t\t\t\t\t\t\n" .
       "\t\t\t\t\t\t// Make the select required since we're replacing\n" .
       "\t\t\t\t\t\tattachmentPick.setAttribute('required', 'required');\n" .
       "\t\t\t\t\t\t\n" .
       "\t\t\t\t\t\t// Update availability indicators after loading options\n" .
       "\t\t\t\t\t\tsetTimeout(() => {\n" .
       "\t\t\t\t\t\t\tupdateDropdownAvailability(attachmentPick, 'attachment');\n" .
       "\t\t\t\t\t\t}, 100);\n" .
       "\t\t\t\t\t}\n" .
       "\t\t\t\t})";

$new = "\t\t\t\t\t\t\tdata.map(item => {\n" .
       "\t\t\t\t\t\t\t\tconst name = `\${item.tipe||'-'} \${item.merk||'-'} \${item.model||''}`.trim();\n" .
       "\t\t\t\t\t\t\t\treturn `<option value=\"\${item.id}\">\${name} \xe2\x80\xa2 SN: \${item.sn_attachment||'-'}</option>`;\n" .
       "\t\t\t\t\t\t\t}).join('');\n" .
       "\t\t\t\t\t\t\n" .
       "\t\t\t\t\t\t// Make the select required since we're replacing\n" .
       "\t\t\t\t\t\tattachmentPick.setAttribute('required', 'required');\n" .
       "\t\t\t\t\t\t\n" .
       "\t\t\t\t\t\t// Update availability indicators after loading options\n" .
       "\t\t\t\t\t\tsetTimeout(() => {\n" .
       "\t\t\t\t\t\t\tupdateDropdownAvailability(attachmentPick, 'attachment');\n" .
       "\t\t\t\t\t\t}, 100);\n" .
       "\t\t\t\t\t}\n" .
       "\t\t\t\t})";

if (strpos($content, $old) !== false) {
    $content = str_replace($old, $new, $content);
    file_put_contents($file, $content);
    echo "Fixed successfully!\n";
} else {
    // Try with slightly different whitespace
    echo "Pattern not found, trying hex dump approach...\n";
    // Print the actual bytes around line 5311
    $lines = explode("\n", $content);
    for ($i = 5310; $i <= 5323; $i++) {
        echo "L" . ($i+1) . " hex[0:10]: " . bin2hex(substr($lines[$i], 0, 10)) . " | " . $lines[$i] . "\n";
    }
}
