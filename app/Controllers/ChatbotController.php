<?php

namespace App\Controllers;

use App\Services\GeminiService;
use CodeIgniter\Controller;

/**
 * ChatbotController
 * Menangani request AJAX dari widget chat OPTIMA Assistant.
 * Endpoint: POST /chatbot/ask
 */
class ChatbotController extends Controller
{
    private GeminiService $gemini;

    public function __construct()
    {
        $this->gemini = new GeminiService();
        helper(['url', 'form']);
    }

    /**
     * POST /chatbot/ask
     * Menerima pesan user + history, mengirim ke Gemini, mengembalikan respons.
     */
    public function ask(): \CodeIgniter\HTTP\ResponseInterface
    {
        $request = service('request');

        if (!$request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request.']);
        }

        // Harus login
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Sesi berakhir. Silakan login kembali.']);
        }

        $message = trim((string)($request->getPost('message') ?? ''));
        if ($message === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesan tidak boleh kosong.']);
        }

        // Batasi panjang pesan
        if (mb_strlen($message) > 1000) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesan terlalu panjang (maksimal 1000 karakter).']);
        }

        // History dari frontend (array of {role, text}) — opsional
        $historyRaw = $request->getPost('history') ?? '[]';
        $history    = [];
        if (is_string($historyRaw)) {
            $decoded = json_decode($historyRaw, true);
            if (is_array($decoded)) {
                // Sanitasi: hanya ambil role dan text
                foreach ($decoded as $turn) {
                    $role = ($turn['role'] ?? '') === 'model' ? 'model' : 'user';
                    $text = mb_substr((string)($turn['text'] ?? ''), 0, 2000);
                    if ($text !== '') {
                        $history[] = ['role' => $role, 'text' => $text];
                    }
                }
            }
        }

        $result = $this->gemini->chat($message, $history);

        if (!$result['success']) {
            log_message('warning', '[ChatbotController] Gemini failed: ' . $result['error']);
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['error'] ?: 'Maaf, terjadi kesalahan saat menghubungi AI.',
            ]);
        }

        return $this->response->setJSON([
            'success'    => true,
            'reply'      => $result['text'],
            'csrf_hash'  => csrf_hash(),
        ]);
    }
}
