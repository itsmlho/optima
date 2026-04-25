<?php

namespace App\Services;

/**
 * GeminiService
 * Wrapper untuk Google Gemini API (generativelanguage.googleapis.com)
 * Digunakan oleh ChatbotController untuk fitur AI Support OPTIMA.
 */
class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models/';

    // System prompt: konteks OPTIMA agar AI menjawab sesuai aplikasi
    private string $systemPrompt = <<<'PROMPT'
Kamu adalah asisten AI bernama "OPTIMA Assistant" yang membantu pengguna sistem informasi manajemen OPTIMA milik PT Sarana Mitra Luas Tbk.

OPTIMA adalah sistem manajemen berbasis web yang mencakup modul-modul berikut:
- **Warehouse**: Manajemen inventaris unit kendaraan forklift/alat berat, attachment, sparepart, silo/perizinan
- **Marketing**: Manajemen quotation, kontrak sewa (CONTRACT/PO_ONLY/DAILY_SPOT), perpanjangan kontrak, delivery instruction, customer management
- **Finance**: Invoicing, billing schedule, laporan keuangan
- **Purchasing**: Purchase order (PO), verifikasi penerimaan unit dan sparepart
- **Service**: Work order (COMPLAINT/PMPS/FABRIKASI), area management, manajemen karyawan teknisi, validasi sparepart
- **Admin/Settings**: User management, role & permission (RBAC), audit trail

Aturan jawaban:
1. Jawab dalam Bahasa Indonesia yang ramah dan profesional.
2. Fokus hanya pada pertanyaan seputar cara menggunakan OPTIMA, alur kerja, fitur, dan troubleshooting sistem.
3. Jika pertanyaan di luar konteks OPTIMA, arahkan kembali: "Saya hanya dapat membantu pertanyaan seputar sistem OPTIMA."
4. Berikan jawaban yang ringkas, jelas, dan actionable (berikan langkah-langkah jika perlu).
5. Jangan pernah mengungkapkan informasi sensitif seperti API key, password, atau konfigurasi server.
6. Jika tidak tahu jawabannya, akui dengan jujur dan sarankan menghubungi administrator OPTIMA.
PROMPT;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
        $this->model  = env('GEMINI_MODEL', 'gemini-2.0-flash');
    }

    /**
     * Kirim pesan ke Gemini dan dapatkan respons.
     *
     * @param string $userMessage  Pesan dari user
     * @param array  $history      Riwayat percakapan [{role:'user'|'model', text:'...'}]
     * @return array ['success' => bool, 'text' => string, 'error' => string]
     */
    public function chat(string $userMessage, array $history = []): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'text' => '', 'error' => 'API key tidak dikonfigurasi.'];
        }

        $contents = [];

        // Sertakan history percakapan (max 10 turn terakhir untuk efisiensi)
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $turn) {
            $role = ($turn['role'] === 'model') ? 'model' : 'user';
            $contents[] = [
                'role'  => $role,
                'parts' => [['text' => (string)($turn['text'] ?? '')]],
            ];
        }

        // Pesan user terbaru
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $this->systemPrompt]],
            ],
            'contents'           => $contents,
            'generationConfig'   => [
                'temperature'     => 0.7,
                'maxOutputTokens' => 1024,
                'topP'            => 0.9,
            ],
            'safetySettings' => [
                ['category' => 'HARM_CATEGORY_HARASSMENT',        'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_HATE_SPEECH',       'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_SEXUALLY_EXPLICIT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
                ['category' => 'HARM_CATEGORY_DANGEROUS_CONTENT', 'threshold' => 'BLOCK_MEDIUM_AND_ABOVE'],
            ],
        ];

        $url = $this->baseUrl . $this->model . ':generateContent?key=' . urlencode($this->apiKey);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            log_message('error', '[GeminiService] cURL error: ' . $curlError);
            return ['success' => false, 'text' => '', 'error' => 'Gagal terhubung ke AI service.'];
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200) {
            $errMsg = $data['error']['message'] ?? 'Unknown error';
            log_message('error', "[GeminiService] HTTP {$httpCode}: {$errMsg}");
            return ['success' => false, 'text' => '', 'error' => 'AI service error: ' . $errMsg];
        }

        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? '';

        if ($text === '') {
            // Bisa jadi blocked by safety
            $finishReason = $data['candidates'][0]['finishReason'] ?? '';
            if ($finishReason === 'SAFETY') {
                return ['success' => false, 'text' => '', 'error' => 'Pertanyaan diblokir oleh filter keamanan AI.'];
            }
            return ['success' => false, 'text' => '', 'error' => 'AI tidak memberikan respons.'];
        }

        return ['success' => true, 'text' => $text, 'error' => ''];
    }
}
