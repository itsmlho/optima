<?php

namespace App\Controllers;

use App\Models\ChatbotHistoryModel;
use App\Services\GroqService;
use App\Services\ChatContextService;
use CodeIgniter\Controller;

/**
 * ChatbotController
 * Menangani request AJAX dari widget chat OPTIMA Assistant.
 * Endpoint: POST /chatbot/ask
 *
 * Fitur:
 *  - Riwayat percakapan persisten via CI4 Session (tidak hilang saat refresh)
 *  - Konteks DB real-time via ChatContextService (query data nyata sesuai intent)
 *  - Rate limiting per-user (min/jam/hari) via CI4 Cache
 *
 * Rate limit defaults (override via .env):
 *   CHATBOT_RATE_PER_MIN  = 5
 *   CHATBOT_RATE_PER_HOUR = 30
 *   CHATBOT_RATE_PER_DAY  = 100
 */
class ChatbotController extends Controller
{
    private GroqService $groq;
    private ChatContextService $contextService;
    private $historyModel;
    private \CodeIgniter\Cache\CacheInterface $cache;

    // Rate limit defaults (overridable via .env)
    private int $limitPerMin;
    private int $limitPerHour;
    private int $limitPerDay;

    private int $maxHistoryTurns = 20;

    public function __construct()
    {
        $this->groq           = new GroqService();
        $this->contextService = new ChatContextService();
        $this->historyModel   = new ChatbotHistoryModel();
        $this->cache          = \Config\Services::cache();
        helper(['url', 'form']);

        $this->limitPerMin  = (int) env('CHATBOT_RATE_PER_MIN',  5);
        $this->limitPerHour = (int) env('CHATBOT_RATE_PER_HOUR', 30);
        $this->limitPerDay  = (int) env('CHATBOT_RATE_PER_DAY',  100);
    }

    /**
     * POST /chatbot/ask
    * Menerima pesan user, bangun konteks DB, kirim ke Groq, simpan history di database.
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

        // ── Rate limiting ──────────────────────────────────────────────
        $userId    = (int) session()->get('user_id');
        $rateCheck = $this->checkRateLimit($userId);
        if ($rateCheck !== null) {
            log_message('info', "[Chatbot] Rate limit hit — user #{$userId}: {$rateCheck}");
            return $this->response->setStatusCode(429)->setJSON([
                'success' => false,
                'message' => $rateCheck,
            ]);
        }

        $message = trim((string)($request->getPost('message') ?? ''));
        if ($message === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesan tidak boleh kosong.']);
        }

        // Batasi panjang pesan
        if (mb_strlen($message) > 1000) {
            return $this->response->setJSON(['success' => false, 'message' => 'Pesan terlalu panjang (maksimal 1000 karakter).']);
        }

        // ── Load history dari database (persisten lintas session/browser) ──
        $history = $this->loadRecentHistory($userId);

        // ── Bangun konteks DB real-time berdasarkan intent ─────────────
        $context = '';
        try {
            $context = $this->contextService->buildContext($message, $userId);
        } catch (\Throwable $e) {
            // Konteks gagal tidak menghentikan chat — tetap lanjut tanpa data
            log_message('warning', '[ChatbotController] Context build failed: ' . $e->getMessage());
        }

        // ── Increment rate counters ────────────────────────────────────
        $this->incrementRateCounters($userId);

        $intent = $this->contextService->detectMessageIntent($message);
        $this->storeHistoryTurn($userId, 'user', $message, $intent);

        $directReply = $this->contextService->buildDirectReply($message, $userId);
        if ($directReply !== null && $directReply !== '') {
            $this->storeHistoryTurn($userId, 'assistant', $directReply, $intent, [
                'source' => 'direct-db',
            ]);

            return $this->response->setJSON([
                'success'   => true,
                'reply'     => $directReply,
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // Kirim ke Groq
        $result = $this->groq->chat($message, $history, $context);

        if (!$result['success']) {
            log_message('warning', '[ChatbotController] Groq failed: ' . $result['error']);
            return $this->response->setJSON([
                'success' => false,
                'message' => $result['error'] ?: 'Maaf, terjadi kesalahan saat menghubungi AI.',
            ]);
        }

        $this->storeHistoryTurn($userId, 'assistant', $result['text'], $intent, [
            'source' => 'llm',
        ]);

        return $this->response->setJSON([
            'success'    => true,
            'reply'      => $result['text'],
            'csrf_hash'  => csrf_hash(),
        ]);
    }

    /**
     * POST /chatbot/clear
    * Hapus riwayat percakapan dari database.
     */
    public function clear(): \CodeIgniter\HTTP\ResponseInterface
    {
        $request = service('request');

        if (!$request->isAJAX() || !session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false]);
        }

        $userId = (int) session()->get('user_id');
        $this->clearHistoryStore($userId);

        return $this->response->setJSON(['success' => true, 'csrf_hash' => csrf_hash()]);
    }

    private function loadRecentHistory(int $userId): array
    {
        try {
            return $this->historyModel->getRecentHistory($userId, $this->maxHistoryTurns);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatbotController] History DB unavailable, fallback to session: ' . $e->getMessage());
            return session()->get($this->getSessionHistoryKey($userId)) ?? [];
        }
    }

    private function storeHistoryTurn(int $userId, string $role, string $message, ?string $intent = null, array $metadata = []): void
    {
        try {
            $this->historyModel->logMessage($userId, $role, $message, $intent, $metadata);
            return;
        } catch (\Throwable $e) {
            log_message('warning', '[ChatbotController] History DB write unavailable, fallback to session: ' . $e->getMessage());
        }

        $sessionKey = $this->getSessionHistoryKey($userId);
        $history    = session()->get($sessionKey) ?? [];
        $history[]  = [
            'role' => $role === 'assistant' ? 'model' : 'user',
            'text' => $message,
        ];

        if (count($history) > $this->maxHistoryTurns * 2) {
            $history = array_slice($history, -($this->maxHistoryTurns * 2));
        }

        session()->set($sessionKey, $history);
    }

    private function clearHistoryStore(int $userId): void
    {
        try {
            $this->historyModel->clearUserHistory($userId);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatbotController] History DB clear unavailable, fallback to session: ' . $e->getMessage());
        }

        session()->remove($this->getSessionHistoryKey($userId));
    }

    private function getSessionHistoryKey(int $userId): string
    {
        return 'chatbot_history_' . $userId;
    }

    // ──────────────────────────────────────────────────────────────────
    // Rate limiting helpers
    // ──────────────────────────────────────────────────────────────────

    /**
     * Cek apakah user sudah melewati batas.
     * Return string pesan error jika limit tercapai, null jika aman.
     */
    private function checkRateLimit(int $userId): ?string
    {
        $now      = time();
        $minKey   = "chatbot_min_{$userId}_" . floor($now / 60);
        $hourKey  = "chatbot_hour_{$userId}_" . floor($now / 3600);
        $dayKey   = "chatbot_day_{$userId}_" . date('Ymd', $now);

        $perMin  = (int)($this->cache->get($minKey)  ?? 0);
        $perHour = (int)($this->cache->get($hourKey) ?? 0);
        $perDay  = (int)($this->cache->get($dayKey)  ?? 0);

        if ($perMin >= $this->limitPerMin) {
            return "Terlalu banyak pesan dalam 1 menit. Tunggu sebentar sebelum mengirim lagi.";
        }
        if ($perHour >= $this->limitPerHour) {
            $resetIn = 3600 - ($now % 3600);
            $menit   = (int) ceil($resetIn / 60);
            return "Batas pesan per jam tercapai. Coba lagi dalam {$menit} menit.";
        }
        if ($perDay >= $this->limitPerDay) {
            return "Batas pesan harian tercapai. Coba lagi besok.";
        }

        return null;
    }

    /**
     * Tambah counter setelah request lolos validasi.
     */
    private function incrementRateCounters(int $userId): void
    {
        $now     = time();
        $minKey  = "chatbot_min_{$userId}_"  . floor($now / 60);
        $hourKey = "chatbot_hour_{$userId}_" . floor($now / 3600);
        $dayKey  = "chatbot_day_{$userId}_"  . date('Ymd', $now);

        $this->cache->save($minKey,  ((int)($this->cache->get($minKey)  ?? 0)) + 1, 60);
        $this->cache->save($hourKey, ((int)($this->cache->get($hourKey) ?? 0)) + 1, 3600);
        $this->cache->save($dayKey,  ((int)($this->cache->get($dayKey)  ?? 0)) + 1, 86400);
    }
}

