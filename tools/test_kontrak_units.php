<?php
/**
 * Quick test: Call kontrak units endpoint
 */

$ch = curl_init('http://localhost/optima/public/marketing/kontrak/units/509');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "HTTP Status: $httpCode\n\n";

$json = json_decode($response, true);
if ($json) {
    echo "Success: " . ($json['success'] ? 'YES' : 'NO') . "\n";
    if (isset($json['data'])) {
        echo "Units found: " . count($json['data']) . "\n";
        foreach ($json['data'] as $unit) {
            echo "  - {$unit['no_unit']} | {$unit['merk']} {$unit['model']} | Status: {$unit['status']} | Lokasi: {$unit['lokasi']}\n";
        }
    }
    if (isset($json['summary'])) {
        echo "\nSummary:\n";
        echo "  Total unit: {$json['summary']['total_unit_dibutuhkan']}\n";
        echo "  Nilai bulanan: " . number_format($json['summary']['total_nilai_bulanan']) . "\n";
    }
    if (isset($json['message'])) {
        echo "Message: {$json['message']}\n";
    }
} else {
    echo "Response (first 1000 chars):\n";
    echo substr($response, 0, 1000) . "\n";
}
