<?php

namespace App\Services;

/**
 * GroqService
 * Wrapper untuk Groq API (api.groq.com) — OpenAI-compatible.
 * Digunakan oleh ChatbotController untuk fitur AI Support OPTIMA.
 */
class GroqService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl = 'https://api.groq.com/openai/v1/chat/completions';

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
        $this->apiKey = env('GROQ_API_KEY', '');
        $this->model  = env('GROQ_MODEL', 'llama-3.3-70b-versatile');
    }

    /**
     * Kirim pesan ke Groq dan dapatkan respons.
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

        $messages = [];

        // System prompt
        $messages[] = [
            'role'    => 'system',
            'content' => $this->systemPrompt,
        ];

        // History percakapan (max 10 turn terakhir)
        $recentHistory = array_slice($history, -10);
        foreach ($recentHistory as $turn) {
            // Groq/OpenAI menggunakan 'assistant' bukan 'model'
            $role = ($turn['role'] === 'model' || $turn['role'] === 'assistant') ? 'assistant' : 'user';
            $messages[] = [
                'role'    => $role,
                'content' => (string)($turn['text'] ?? ''),
            ];
        }

        // Pesan user terbaru
        $messages[] = [
            'role'    => 'user',
            'content' => $userMessage,
        ];

        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => 0.7,
            'max_tokens'  => 1024,
            'top_p'       => 0.9,
            'stream'      => false,
        ];

        $ch = curl_init($this->baseUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey,
            ],
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response  = curl_exec($ch);
        $httpCode  = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            log_message('error', '[GroqService] cURL error: ' . $curlError);
            return ['success' => false, 'text' => '', 'error' => 'Gagal terhubung ke AI service.'];
        }

        $data = json_decode($response, true);

        if ($httpCode !== 200) {
            $errMsg = $data['error']['message'] ?? 'Unknown error';
            log_message('error', "[GroqService] HTTP {$httpCode}: {$errMsg}");
            return ['success' => false, 'text' => '', 'error' => 'AI service error: ' . $errMsg];
        }

        $text = $data['choices'][0]['message']['content'] ?? '';

        if ($text === '') {
            $finishReason = $data['choices'][0]['finish_reason'] ?? '';
            if ($finishReason === 'content_filter') {
                return ['success' => false, 'text' => '', 'error' => 'Pertanyaan diblokir oleh filter keamanan AI.'];
            }
            return ['success' => false, 'text' => '', 'error' => 'AI tidak memberikan respons.'];
        }

        return ['success' => true, 'text' => $text, 'error' => ''];
    }
}
