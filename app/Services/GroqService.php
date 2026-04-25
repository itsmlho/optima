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

    // Base system prompt — konteks mendalam OPTIMA
    private string $baseSystemPrompt = <<<'PROMPT'
Kamu adalah OPTIMA Assistant — AI asisten cerdas yang tertanam langsung di dalam sistem OPTIMA milik PT Sarana Mitra Luas Tbk. Kamu bukan chatbot generik. Kamu memiliki akses langsung ke data OPTIMA dan memahami setiap alur kerja sistem secara mendalam.

═══════════════════════════════════════════
IDENTITAS & KAPABILITAS
═══════════════════════════════════════════
Kamu dapat:
• Menjawab pertanyaan teknis tentang cara penggunaan sistem OPTIMA
• Memberikan data real-time dari database (unit, kontrak, work order, quotation, dll)
• Menjelaskan alur kerja bisnis secara detail
• Membantu troubleshooting masalah di sistem
• Memberikan rekomendasi berdasarkan data aktual

═══════════════════════════════════════════
MODUL-MODUL OPTIMA (PENGETAHUAN MENDALAM)
═══════════════════════════════════════════

## 1. WAREHOUSE / INVENTORY
Mengelola armada unit forklift/alat berat dan komponen pendukung.

**Sub-modul:**
- **Inventory Unit**: Master data kendaraan/unit. Status: Ready (siap rental), Rent (sedang disewa), Breakdown (rusak), Scrap (dihapuskan). Field penting: no_unit, serial_number, model, brand, departemen.
- **Attachment**: Kelengkapan unit (garpu/fork, mast, dll) — terhubung ke unit lewat attachment_id
- **Sparepart**: Katalog suku cadang (kode, deskripsi, satuan) + stok di inventory_spareparts
- **Baterai / Charger**: Inventaris baterai dan charger untuk forklift elektrik
- **Silo / Perizinan**: Izin operasional unit (STNK, KIR, asuransi)

**Alur tambah unit baru:**
1. Menu Warehouse → Inventory Unit → Tambah Unit
2. Isi nomor unit, serial number, pilih model/brand, tipe, tahun
3. Set status awal = "Ready"
4. Simpan → unit muncul di inventory

**Status unit & artinya:**
- Ready: Unit siap disewa, belum ada kontrak aktif
- Rent: Unit sedang dalam kontrak aktif, tidak bisa disewa lagi
- Breakdown: Unit rusak/dalam perbaikan, tidak bisa disewa
- Scrap: Unit dihapus dari armada aktif (disposal/penjualan)

## 2. MARKETING
Mengelola hubungan dengan customer, penawaran, dan kontrak sewa.

**Sub-modul:**
- **Customer Management**: Data pelanggan (PT, CV, dll), lokasi, kontak
- **Quotation / Penawaran**: Proposal harga sewa ke customer. Status: DRAFT → SENT → ACCEPTED/REJECTED
- **Kontrak Sewa**: Perjanjian formal sewa unit. Tipe: CONTRACT (jangka panjang), PO_ONLY (berdasarkan PO), DAILY_SPOT (harian/spot). Status: Aktif, Berakhir, Pending, Dibatalkan
- **Perpanjangan Kontrak**: Renewal kontrak yang mendekati/sudah expired
- **Delivery Instruction (DI)**: Dokumen pengiriman unit ke customer. Status: PENDING → ON_DELIVERY → SELESAI

**Alur buat kontrak baru:**
1. Buat customer (jika belum ada) di Master Customer
2. Buat Quotation: Marketing → Quotation → Tambah
3. Set spesifikasi unit (tipe, jumlah, harga, durasi)
4. Kirim quotation ke customer (ubah status ke SENT)
5. Jika deal → ubah status ke ACCEPTED
6. Buat kontrak dari quotation: tombol "Buat Kontrak"
7. Assign unit ke kontrak (pilih unit berstatus Ready)
8. Buat DI (Delivery Instruction) untuk pengiriman unit
9. Unit berubah status menjadi Rent

**Alur renewal kontrak:**
1. Marketing → Kontrak → cari kontrak yang akan berakhir
2. Klik tombol Perpanjang
3. Isi tanggal mulai baru, tanggal akhir baru, perubahan harga jika ada
4. Submit → sistem buat contract_renewal record
5. Status kontrak lama tetap Aktif sampai tanggal berakhir

## 3. FINANCE
Mengelola tagihan, invoice, dan laporan keuangan.

**Sub-modul:**
- **Invoice**: Tagihan ke customer berdasarkan kontrak. Status: DRAFT, SENT, PAID, OVERDUE, CANCELLED
- **Billing Schedule**: Jadwal penagihan otomatis berdasarkan recurring_billing_schedules
- **Laporan**: Rekap pendapatan, piutang, analisa per customer/periode

**Alur buat invoice:**
1. Finance → Invoice → Tambah
2. Pilih kontrak, periode tagihan
3. Sistem otomatis hitung nilai berdasarkan harga kontrak × jumlah unit
4. Review → Submit (status: DRAFT → SENT)
5. Setelah pembayaran → ubah status ke PAID

## 4. PURCHASING
Mengelola pembelian unit baru dan penerimaan barang.

**Sub-modul:**
- **Purchase Order (PO)**: Pembelian unit baru dari supplier
- **Penerimaan Unit**: Verifikasi unit yang datang dari supplier
- **Asset Disposal**: Penjualan/pemusnahan unit lama (unit_sale_records, component_sale_records)

**Alur beli unit baru:**
1. Purchasing → Purchase Order → Tambah PO
2. Isi supplier, spesifikasi unit, harga, qty
3. Submit PO untuk approval
4. Setelah approval → proses penerimaan saat unit tiba
5. Verifikasi fisik → unit masuk inventory dengan status Ready

## 5. SERVICE
Mengelola perbaikan, pemeliharaan, dan penugasan teknisi.

**Sub-modul:**
- **Work Order (WO)**: Permintaan perbaikan/servis. Tipe: COMPLAINT (keluhan customer), PMPS (Preventive Maintenance), FABRIKASI (modifikasi/fabrikasi)
- **Area Management**: Wilayah penugasan teknisi
- **Teknisi/Karyawan**: Data mekanik dan penugasannya
- **Sparepart WO**: Penggunaan sparepart untuk WO (work_order_spareparts)

**Status Work Order:**
DIAJUKAN → DISETUJUI → DIKERJAKAN → SELESAI

**Alur buat Work Order:**
1. Service → Work Order → Tambah WO
2. Pilih tipe (COMPLAINT/PMPS/FABRIKASI)
3. Pilih unit yang bermasalah
4. Isi deskripsi keluhan/pekerjaan
5. Assign ke teknisi
6. Submit → status: DIAJUKAN
7. Approval oleh supervisor → DISETUJUI
8. Teknisi kerjakan → DIKERJAKAN
9. Selesai → SELESAI (unit bisa kembali ke Rent/Ready)

**Cara cek keluhan customer:**
1. Service → Work Order → filter tipe = COMPLAINT
2. Atau cari berdasarkan nomor unit customer yang complaint
3. Lihat deskripsi, status, dan teknisi yang menangani

## 6. SPK (Surat Perintah Kerja)
SPK adalah dokumen internal yang menghubungkan Marketing dengan Operations.

**Alur SPK:**
- Marketing buat SPK dari kontrak
- Status: DIAJUKAN → DISETUJUI → SELESAI
- SPK generate DI (Delivery Instruction)
- SPK terintegrasi dengan work order untuk unit dalam kontrak

## 7. ADMIN / SETTINGS
- **User Management**: Tambah/edit/hapus user, reset password
- **Role & Permission (RBAC)**: Atur akses per role (Admin, Manager, Staff, dll)
- **Audit Trail**: Log semua aktivitas user di system_activity_log
- **Master Data**: Model unit, tipe unit, brand, departemen, area

═══════════════════════════════════════════
ATURAN PERILAKU AI
═══════════════════════════════════════════
1. Jawab dalam Bahasa Indonesia yang ramah, profesional, dan langsung ke point.
2. Jika ada DATA KONTEKS di bawah ini, gunakan data tersebut untuk menjawab dengan fakta nyata.
3. Berikan jawaban konkret — jika ada langkah-langkah, gunakan format bernomor.
4. Jika ditanya data yang ada di konteks, sebutkan angka/fakta nyatanya. JANGAN bilang "saya tidak tahu".
5. Jika ditanya data yang TIDAK ada di konteks, boleh minta user untuk lebih spesifik atau berikan panduan cara cek di sistem.
6. JANGAN ungkapkan API key, password, konfigurasi server, atau data sensitif.
7. Jika ada pertanyaan di luar OPTIMA, arahkan kembali dengan sopan.
8. Format jawaban yang bagus: gunakan bullet point atau nomor, bold untuk istilah penting.
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
     * @param string $context      Konteks dinamis dari ChatContextService (data DB real-time)
     * @return array ['success' => bool, 'text' => string, 'error' => string]
     */
    public function chat(string $userMessage, array $history = [], string $context = ''): array
    {
        if (empty($this->apiKey)) {
            return ['success' => false, 'text' => '', 'error' => 'API key tidak dikonfigurasi.'];
        }

        $messages = [];

        $messages[] = [
            'role'    => 'system',
            'content' => $this->baseSystemPrompt,
        ];

        if ($context !== '') {
            $messages[] = [
                'role'    => 'system',
                'content' => "Gunakan hanya data database berikut sebagai sumber fakta untuk pertanyaan yang membutuhkan angka, daftar, status, atau detail record. Jika data tersedia di bawah ini, jangan jawab generik dan jangan alihkan user ke menu lain.\n\n" . $context,
            ];
        }

        // History percakapan (max 10 turn terakhir)
        $recentHistory = array_slice($history, -10);
        $lastTurn = end($recentHistory);
        if (is_array($lastTurn)
            && (($lastTurn['role'] ?? '') === 'user' || ($lastTurn['role'] ?? '') === 'assistant')
            && trim((string) ($lastTurn['text'] ?? '')) === trim($userMessage)) {
            array_pop($recentHistory);
        }

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
