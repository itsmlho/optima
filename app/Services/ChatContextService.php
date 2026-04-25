<?php

namespace App\Services;

use CodeIgniter\Database\BaseConnection;

/**
 * ChatContextService
 *
 * Membangun context dinamis untuk OPTIMA Assistant berdasarkan intent
 * dari pesan user. Melakukan query DB nyata dan mengembalikan string
 * konteks yang akan diinjeksikan ke system prompt AI.
 *
 * Intent categories:
 *  - summary      : Ringkasan / dashboard keseluruhan
 *  - unit_stats   : Statistik unit (berapa unit, status unit)
 *  - unit_detail  : Detail unit spesifik (nomor unit tertentu)
 *  - contract     : Statistik / detail kontrak / customer
 *  - work_order   : Statistik / detail work order / SPK
 *  - quotation    : Statistik penawaran marketing
 *  - invoice      : Statistik invoice / tagihan
 *  - sparepart    : Stok sparepart
 *  - employee     : Info karyawan / teknisi
 *  - none         : Pertanyaan how-to umum, tidak perlu data DB
 */
class ChatContextService
{
    private BaseConnection $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Entry point: deteksi intent lalu bangun context string.
     *
     * @param string $message  Pesan user
     * @param int    $userId   ID user yang sedang login
     * @return string Context siap pakai untuk system prompt
     */
    public function buildContext(string $message, int $userId): string
    {
        $msg    = mb_strtolower($message);
        $intent = $this->detectIntent($msg);

        $parts = [];

        // Selalu sertakan info user
        $parts[] = $this->getUserContext($userId);

        // Inject data sesuai intent
        switch ($intent) {
            case 'summary':
                $parts[] = $this->getSummaryContext();
                break;
            case 'unit_stats':
                $parts[] = $this->getUnitStatsContext();
                break;
            case 'unit_detail':
                $unitNo  = $this->extractUnitNumber($msg);
                $parts[] = $this->getUnitDetailContext($unitNo);
                break;
            case 'contract':
                $customerName = $this->extractCustomerName($message);
                $contractNo   = $this->extractContractNumber($message);
                $parts[] = $this->getContractContext($customerName, $contractNo);
                break;
            case 'work_order':
                $parts[] = $this->getWorkOrderContext();
                break;
            case 'quotation':
                $parts[] = $this->getQuotationContext();
                break;
            case 'invoice':
                $parts[] = $this->getInvoiceContext();
                break;
            case 'sparepart':
                $parts[] = $this->getSparepartContext();
                break;
            case 'employee':
                $parts[] = $this->getEmployeeContext();
                break;
            case 'summary_all':
                $parts[] = $this->getSummaryContext();
                $parts[] = $this->getUnitStatsContext();
                $parts[] = $this->getContractContext(null, null);
                $parts[] = $this->getWorkOrderContext();
                break;
            default:
                // Tidak perlu data tambahan untuk pertanyaan how-to
                break;
        }

        return implode("\n\n", array_filter($parts));
    }

    /**
     * Return detected intent for controller-level routing.
     */
    public function detectMessageIntent(string $message): string
    {
        return $this->detectIntent(mb_strtolower($message));
    }

    /**
     * Build direct grounded answer from DB for strongly data-oriented prompts.
     * Returns null when the message should still go through the LLM.
     */
    public function buildDirectReply(string $message, int $userId): ?string
    {
        $intent = $this->detectMessageIntent($message);
        $lower  = mb_strtolower($message);

        switch ($intent) {
            case 'contract':
                if (preg_match('/30\s*hari|akan berakhir|jatuh tempo|expired|mau berakhir/', $lower)) {
                    return $this->buildExpiringContractsReply();
                }
                break;
            case 'unit_stats':
                if (preg_match('/berapa|jumlah|total|status/', $lower)) {
                    return $this->buildUnitStatsReply();
                }
                break;
            case 'work_order':
                if (preg_match('/berapa|jumlah|total|open|belum selesai|masih/', $lower)) {
                    return $this->buildOpenWorkOrderReply();
                }
                break;
            case 'summary_all':
                return $this->buildSummaryReply();
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────
    // Intent Detection
    // ─────────────────────────────────────────────────────────────────

    private function detectIntent(string $msg): string
    {
        // Summary / dashboard
        if (preg_match('/ringkasan|dashboard|rekap|ikhtisar|overview|hari ini|situasi|kondisi saat ini/', $msg)) {
            return 'summary_all';
        }

        // Unit detail (nomor unit spesifik)
        if ($this->extractUnitNumber($msg) !== null) {
            return 'unit_detail';
        }

        // Unit stats
        if (preg_match('/berapa unit|jumlah unit|total unit|status unit|unit (ready|rent|breakdown|scrap|available|tersedia)|unit yang|stok unit|inventori unit|inventory unit|fleet/', $msg)) {
            return 'unit_stats';
        }

        // Contract / customer
        if (preg_match('/kontrak|contract|sewa|customer|pelanggan|klien|renewal|perpanjangan|ktr\//', $msg)) {
            return 'contract';
        }

        // Work order / SPK
        if (preg_match('/work order|workorder|wo |keluhan|komplain|complaint|pmps|fabrikasi|spk|perintah kerja|servis|perbaikan|teknisi/', $msg)) {
            return 'work_order';
        }

        // Quotation / penawaran
        if (preg_match('/quotation|penawaran|proposal|quot|deal|draft/', $msg)) {
            return 'quotation';
        }

        // Invoice / tagihan
        if (preg_match('/invoice|tagihan|billing|pembayaran|bayar|piutang|tunggakan/', $msg)) {
            return 'invoice';
        }

        // Sparepart / stok
        if (preg_match('/sparepart|spare part|suku cadang|stok|stock|persediaan|part/', $msg)) {
            return 'sparepart';
        }

        // Karyawan / teknisi
        if (preg_match('/karyawan|teknisi|staff|pegawai|mekanik|engineer|driver|supir/', $msg)) {
            return 'employee';
        }

        // Unit (general — tanpa nomor spesifik)
        if (preg_match('/\bunit\b|\bforklift\b|\balat berat\b|\barmada\b/', $msg)) {
            return 'unit_stats';
        }

        return 'none';
    }

    // ─────────────────────────────────────────────────────────────────
    // Extraction helpers
    // ─────────────────────────────────────────────────────────────────

    /**
     * Ekstrak nomor unit dari teks (misal: F-1234, SML-001, unit #F0123)
     */
    private function extractUnitNumber(string $msg): ?string
    {
        // Pola: huruf-angka, contoh F-1234 / SML-001 / MK0012 / A123
        if (preg_match('/\b([a-z]{1,5}[-]?\d{2,6})\b/i', $msg, $m)) {
            return strtoupper($m[1]);
        }
        return null;
    }

    /**
     * Ekstrak nama customer dari teks (PT, CV, UD, dll)
     */
    private function extractCustomerName(string $message): ?string
    {
        if (preg_match('/((?:PT|CV|UD|PD|TB|Yayasan|Koperasi|Pemda)[\s\.][\w\s,.\-]+)/i', $message, $m)) {
            return trim($m[1]);
        }
        return null;
    }

    /**
     * Ekstrak nomor kontrak (KTR/YYYYMM/XXX atau sejenisnya)
     */
    private function extractContractNumber(string $message): ?string
    {
        if (preg_match('/\b(KTR\/\d{4,6}\/\d+|[A-Z]{2,5}\/\d{4,6}\/\d+)\b/i', $message, $m)) {
            return strtoupper($m[1]);
        }
        return null;
    }

    // ─────────────────────────────────────────────────────────────────
    // Context Builders — query DB nyata
    // ─────────────────────────────────────────────────────────────────

    private function getUserContext(int $userId): string
    {
        try {
            $user = $this->db->table('users')
                ->select('users.username, users.first_name, users.last_name')
                ->where('users.id', $userId)
                ->get()->getRowArray();

            if (!$user) {
                return '[INFO USER]: User ID ' . $userId;
            }

            // Ambil roles
            $roles = $this->db->table('user_roles ur')
                ->select('r.name')
                ->join('roles r', 'r.id = ur.role_id')
                ->where('ur.user_id', $userId)
                ->get()->getResultArray();

            $roleNames = implode(', ', array_column($roles, 'name'));
            $fullName  = trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''));
            if ($fullName === '') {
                $fullName = $user['username'];
            }

            return "[INFO USER SAAT INI]: Nama: {$fullName} | Role: " . ($roleNames ?: 'N/A');
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getUserContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getSummaryContext(): string
    {
        try {
            $lines = ['[RINGKASAN DATA OPTIMA SAAT INI]:'];

            // Unit counts
            $unitStats = $this->db->query("
                SELECT COALESCE(s.status_unit, 'Unknown') AS status, COUNT(*) AS total
                FROM inventory_unit u
                LEFT JOIN status_unit s ON u.status_unit_id = s.id_status
                GROUP BY s.status_unit
                ORDER BY total DESC
            ")->getResultArray();

            if ($unitStats) {
                $totalUnit = array_sum(array_column($unitStats, 'total'));
                $lines[] = "- Total unit armada: {$totalUnit}";
                foreach ($unitStats as $row) {
                    $lines[] = "  • {$row['status']}: {$row['total']} unit";
                }
            }

            // Kontrak aktif
            $kontrakAktif = $this->db->query("SELECT COUNT(*) AS c FROM kontrak WHERE status = 'Aktif'")->getRow();
            if ($kontrakAktif) {
                $lines[] = "- Kontrak aktif: {$kontrakAktif->c}";
            }

            // Work order open (not selesai)
            $woOpen = $this->db->query("
                SELECT COUNT(*) AS c FROM work_orders wo
                JOIN work_order_statuses ws ON wo.status_id = ws.id
                WHERE ws.name NOT IN ('SELESAI','CLOSED','DONE','Selesai')
            ")->getRow();
            if ($woOpen) {
                $lines[] = "- Work order belum selesai: {$woOpen->c}";
            }

            // Quotation open
            $quotOpen = $this->db->query("
                SELECT COUNT(*) AS c FROM quotations WHERE status NOT IN ('ACCEPTED','REJECTED','DEAL','NOT_DEAL')
            ")->getRow();
            if ($quotOpen) {
                $lines[] = "- Quotation masih proses: {$quotOpen->c}";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getSummaryContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getUnitStatsContext(): string
    {
        try {
            $lines = ['[DATA UNIT ARMADA]:'];

            $stats = $this->db->query("
                SELECT COALESCE(s.status_unit, 'Unknown') AS status, COUNT(*) AS total
                FROM inventory_unit u
                LEFT JOIN status_unit s ON u.status_unit_id = s.id_status
                GROUP BY s.status_unit
                ORDER BY total DESC
            ")->getResultArray();

            if (empty($stats)) {
                return '';
            }

            $total = array_sum(array_column($stats, 'total'));
            $lines[] = "Total seluruh unit: {$total}";

            foreach ($stats as $row) {
                $lines[] = "- {$row['status']}: {$row['total']} unit";
            }

            // Top 5 unit terbaru
            $recent = $this->db->query("
                SELECT u.no_unit,
                       COALESCE(m.model_unit,'?') AS model,
                       COALESCE(m.merk_unit,'?')  AS brand,
                       COALESCE(s.status_unit,'?') AS status,
                       u.created_at
                FROM inventory_unit u
                LEFT JOIN status_unit s  ON u.status_unit_id = s.id_status
                LEFT JOIN model_unit m   ON u.model_unit_id = m.id_model_unit
                ORDER BY u.created_at DESC LIMIT 5
            ")->getResultArray();

            if ($recent) {
                $lines[] = "\nUnit terbaru ditambahkan:";
                foreach ($recent as $r) {
                    $lines[] = "- {$r['no_unit']} | {$r['brand']} {$r['model']} | Status: {$r['status']}";
                }
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getUnitStatsContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getUnitDetailContext(?string $unitNo): string
    {
        if (!$unitNo) {
            return $this->getUnitStatsContext();
        }

        try {
            $rows = $this->db->query("
                SELECT u.no_unit, u.serial_number,
                       COALESCE(s.status_unit,'?') AS status,
                       COALESCE(m.model_unit,'?')  AS model,
                       COALESCE(m.merk_unit,'?')   AS brand,
                       d.nama_departemen,
                       k.no_kontrak, k.status AS kontrak_status,
                       c.customer_name
                FROM inventory_unit u
                LEFT JOIN status_unit s   ON u.status_unit_id = s.id_status
                LEFT JOIN model_unit m    ON u.model_unit_id = m.id_model_unit
                LEFT JOIN departemen d    ON u.departemen_id = d.id_departemen
                LEFT JOIN kontrak k       ON u.kontrak_id = k.id
                LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
                LEFT JOIN customers c     ON cl.customer_id = c.id
                WHERE u.no_unit LIKE ?
                LIMIT 5
            ", ["%{$unitNo}%"])->getResultArray();

            if (empty($rows)) {
                return "[DATA UNIT '{$unitNo}']: Tidak ditemukan unit dengan nomor tersebut.";
            }

            $lines = ["[DATA UNIT '{$unitNo}']:"];
            foreach ($rows as $r) {
                $lines[] = "- No Unit: {$r['no_unit']} | Model: {$r['brand']} {$r['model']} | S/N: {$r['serial_number']}";
                $lines[] = "  Status: {$r['status']} | Departemen: {$r['nama_departemen']}";
                if ($r['no_kontrak']) {
                    $lines[] = "  Kontrak: {$r['no_kontrak']} ({$r['kontrak_status']}) — Customer: {$r['customer_name']}";
                }
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getUnitDetailContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getContractContext(?string $customerName, ?string $contractNo): string
    {
        try {
            $lines = ['[DATA KONTRAK]:'];

            // Stats
            $stats = $this->db->query("
                SELECT status, COUNT(*) AS total FROM kontrak GROUP BY status ORDER BY total DESC
            ")->getResultArray();

            foreach ($stats as $row) {
                $lines[] = "- Kontrak {$row['status']}: {$row['total']}";
            }

            // Detail customer tertentu
            if ($customerName) {
                $contracts = $this->db->query("
                    SELECT k.no_kontrak, k.status, k.tanggal_mulai, k.tanggal_berakhir,
                           k.total_units, c.customer_name, cl.location_name
                    FROM kontrak k
                    JOIN customer_locations cl ON k.customer_location_id = cl.id
                    JOIN customers c ON cl.customer_id = c.id
                    WHERE c.customer_name LIKE ?
                    ORDER BY k.dibuat_pada DESC LIMIT 10
                ", ["%{$customerName}%"])->getResultArray();

                if ($contracts) {
                    $lines[] = "\nKontrak untuk '{$customerName}':";
                    foreach ($contracts as $c) {
                        $lines[] = "- {$c['no_kontrak']} | {$c['customer_name']} ({$c['location_name']}) | {$c['status']} | {$c['total_units']} unit | {$c['tanggal_mulai']} s/d {$c['tanggal_berakhir']}";
                    }
                } else {
                    $lines[] = "\nTidak ada kontrak ditemukan untuk customer '{$customerName}'.";
                }
            }

            // Detail nomor kontrak tertentu
            if ($contractNo) {
                $contract = $this->db->query("
                    SELECT k.no_kontrak, k.status, k.tanggal_mulai, k.tanggal_berakhir,
                           k.total_units, c.customer_name, cl.location_name
                    FROM kontrak k
                    JOIN customer_locations cl ON k.customer_location_id = cl.id
                    JOIN customers c ON cl.customer_id = c.id
                    WHERE k.no_kontrak = ?
                    LIMIT 1
                ", [$contractNo])->getRowArray();

                if ($contract) {
                    $lines[] = "\nDetail Kontrak {$contractNo}:";
                    $lines[] = "- Customer: {$contract['customer_name']} ({$contract['location_name']})";
                    $lines[] = "- Status: {$contract['status']}";
                    $lines[] = "- Periode: {$contract['tanggal_mulai']} s/d {$contract['tanggal_berakhir']}";
                    $lines[] = "- Jumlah unit: {$contract['total_units']}";
                } else {
                    $lines[] = "\nKontrak '{$contractNo}' tidak ditemukan.";
                }
            }

            // Kontrak akan berakhir dalam 30 hari
            $expiring = $this->db->query("
                SELECT k.no_kontrak, c.customer_name, k.tanggal_berakhir,
                       DATEDIFF(k.tanggal_berakhir, CURDATE()) AS sisa_hari
                FROM kontrak k
                JOIN customer_locations cl ON k.customer_location_id = cl.id
                JOIN customers c ON cl.customer_id = c.id
                WHERE k.status = 'Aktif'
                  AND k.tanggal_berakhir BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                ORDER BY k.tanggal_berakhir ASC LIMIT 10
            ")->getResultArray();

            if ($expiring) {
                $lines[] = "\nKontrak akan berakhir dalam 30 hari:";
                foreach ($expiring as $e) {
                    $lines[] = "- {$e['no_kontrak']} | {$e['customer_name']} | Berakhir: {$e['tanggal_berakhir']} (sisa {$e['sisa_hari']} hari)";
                }
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getContractContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function buildExpiringContractsReply(): ?string
    {
        try {
            $rows = $this->db->query("
                SELECT k.no_kontrak, c.customer_name, cl.location_name, k.tanggal_berakhir,
                       DATEDIFF(k.tanggal_berakhir, CURDATE()) AS sisa_hari,
                       k.total_units
                FROM kontrak k
                JOIN customer_locations cl ON k.customer_location_id = cl.id
                JOIN customers c ON cl.customer_id = c.id
                WHERE k.status = 'Aktif'
                  AND k.tanggal_berakhir BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
                ORDER BY k.tanggal_berakhir ASC
                LIMIT 15
            ")->getResultArray();

            if (empty($rows)) {
                return "Saat ini tidak ada kontrak aktif yang akan berakhir dalam 30 hari ke depan.";
            }

            $lines   = ["Ada **" . count($rows) . " kontrak aktif** yang akan berakhir dalam 30 hari ke depan:"];
            foreach ($rows as $row) {
                $lines[] = "- **{$row['no_kontrak']}** | {$row['customer_name']} ({$row['location_name']}) | {$row['total_units']} unit | berakhir {$row['tanggal_berakhir']} | sisa {$row['sisa_hari']} hari";
            }

            $lines[] = "\nJika perlu, saya bisa bantu lanjutkan dengan rincian customer tertentu atau kontrak yang paling mendesak untuk renewal.";

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] buildExpiringContractsReply error: ' . $e->getMessage());
            return null;
        }
    }

    private function buildUnitStatsReply(): ?string
    {
        try {
            $rows = $this->db->query("
                SELECT COALESCE(s.status_unit, 'Unknown') AS status, COUNT(*) AS total
                FROM inventory_unit u
                LEFT JOIN status_unit s ON u.status_unit_id = s.id_status
                GROUP BY s.status_unit
                ORDER BY total DESC
            ")->getResultArray();

            if (empty($rows)) {
                return 'Data unit belum tersedia.';
            }

            $total = array_sum(array_map(static fn(array $row): int => (int) $row['total'], $rows));
            $lines = ["Total unit armada saat ini: **{$total} unit**."];
            foreach ($rows as $row) {
                $lines[] = "- {$row['status']}: {$row['total']} unit";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] buildUnitStatsReply error: ' . $e->getMessage());
            return null;
        }
    }

    private function buildOpenWorkOrderReply(): ?string
    {
        try {
            $rows = $this->db->query("
                SELECT wo.work_order_number, wo.order_type,
                       COALESCE(ws.name, '?') AS status,
                       COALESCE(u.no_unit, '-') AS no_unit
                FROM work_orders wo
                LEFT JOIN work_order_statuses ws ON wo.status_id = ws.id
                LEFT JOIN inventory_unit u ON wo.unit_id = u.id_inventory_unit
                WHERE COALESCE(ws.name, '') NOT IN ('SELESAI', 'CLOSED', 'DONE', 'Selesai')
                ORDER BY wo.created_at DESC
                LIMIT 10
            ")->getResultArray();

            if (empty($rows)) {
                return 'Saat ini tidak ada work order open.';
            }

            $lines = ["Saat ini ada **" . count($rows) . " work order open** yang paling baru:"];
            foreach ($rows as $row) {
                $lines[] = "- **{$row['work_order_number']}** | {$row['order_type']} | Unit: {$row['no_unit']} | Status: {$row['status']}";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] buildOpenWorkOrderReply error: ' . $e->getMessage());
            return null;
        }
    }

    private function buildSummaryReply(): ?string
    {
        try {
            $unitTotal = $this->db->query("SELECT COUNT(*) AS total FROM inventory_unit")->getRowArray();
            $contractActive = $this->db->query("SELECT COUNT(*) AS total FROM kontrak WHERE status = 'Aktif'")->getRowArray();
            $woOpen = $this->db->query("
                SELECT COUNT(*) AS total
                FROM work_orders wo
                LEFT JOIN work_order_statuses ws ON wo.status_id = ws.id
                WHERE COALESCE(ws.name, '') NOT IN ('SELESAI', 'CLOSED', 'DONE', 'Selesai')
            ")->getRowArray();

            return implode("\n", [
                'Ringkasan kondisi OPTIMA saat ini:',
                '- Total unit armada: **' . ((int) ($unitTotal['total'] ?? 0)) . ' unit**',
                '- Kontrak aktif: **' . ((int) ($contractActive['total'] ?? 0)) . '**',
                '- Work order open: **' . ((int) ($woOpen['total'] ?? 0)) . '**',
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] buildSummaryReply error: ' . $e->getMessage());
            return null;
        }
    }

    private function getWorkOrderContext(): string
    {
        try {
            $lines = ['[DATA WORK ORDER]:'];

            // Stats by status
            $stats = $this->db->query("
                SELECT COALESCE(ws.name,'Unknown') AS status, COUNT(*) AS total
                FROM work_orders wo
                LEFT JOIN work_order_statuses ws ON wo.status_id = ws.id
                GROUP BY ws.name
                ORDER BY total DESC
            ")->getResultArray();

            foreach ($stats as $row) {
                $lines[] = "- WO Status {$row['status']}: {$row['total']}";
            }

            // Stats by type
            $types = $this->db->query("
                SELECT order_type, COUNT(*) AS total
                FROM work_orders
                GROUP BY order_type
                ORDER BY total DESC
            ")->getResultArray();

            if ($types) {
                $lines[] = "\nJenis Work Order:";
                foreach ($types as $t) {
                    $lines[] = "- {$t['order_type']}: {$t['total']}";
                }
            }

            // WO open terbaru (5)
            $open = $this->db->query("
                SELECT wo.work_order_number, wo.order_type,
                       COALESCE(ws.name,'?') AS status,
                       u.no_unit, wo.complaint_description,
                       wo.created_at
                FROM work_orders wo
                LEFT JOIN work_order_statuses ws ON wo.status_id = ws.id
                LEFT JOIN inventory_unit u ON wo.unit_id = u.id_inventory_unit
                WHERE ws.name NOT IN ('SELESAI','CLOSED','DONE','Selesai')
                ORDER BY wo.created_at DESC LIMIT 5
            ")->getResultArray();

            if ($open) {
                $lines[] = "\nWork Order masih open (terbaru):";
                foreach ($open as $w) {
                    $desc = mb_substr($w['complaint_description'] ?? '-', 0, 80);
                    $lines[] = "- {$w['work_order_number']} | {$w['order_type']} | Unit: {$w['no_unit']} | Status: {$w['status']}";
                    $lines[] = "  Keterangan: {$desc}";
                }
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getWorkOrderContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getQuotationContext(): string
    {
        try {
            $lines = ['[DATA QUOTATION / PENAWARAN]:'];

            $stats = $this->db->query("
                SELECT status, COUNT(*) AS total FROM quotations GROUP BY status ORDER BY total DESC
            ")->getResultArray();

            if (empty($stats)) {
                return '';
            }

            foreach ($stats as $row) {
                $lines[] = "- {$row['status']}: {$row['total']} quotation";
            }

            // Quotation terbaru
            $recent = $this->db->query("
                SELECT q.quotation_number, q.status, c.customer_name, q.created_at
                FROM quotations q
                LEFT JOIN customers c ON q.customer_id = c.id
                ORDER BY q.created_at DESC LIMIT 5
            ")->getResultArray();

            if ($recent) {
                $lines[] = "\nQuotation terbaru:";
                foreach ($recent as $r) {
                    $lines[] = "- {$r['quotation_number']} | {$r['customer_name']} | {$r['status']} | {$r['created_at']}";
                }
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getQuotationContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getInvoiceContext(): string
    {
        try {
            $lines = ['[DATA INVOICE / TAGIHAN]:'];

            $stats = $this->db->query("
                SELECT status, COUNT(*) AS total, SUM(total_amount) AS total_nilai
                FROM invoices
                GROUP BY status ORDER BY total DESC
            ")->getResultArray();

            if (empty($stats)) {
                return '';
            }

            foreach ($stats as $row) {
                $nilai = number_format((float)$row['total_nilai'], 0, ',', '.');
                $lines[] = "- {$row['status']}: {$row['total']} invoice (Rp {$nilai})";
            }

            // Invoice overdue
            $overdue = $this->db->query("
                SELECT COUNT(*) AS c FROM invoices
                WHERE status NOT IN ('PAID','CANCELLED')
                  AND due_date < CURDATE()
            ")->getRow();
            if ($overdue && $overdue->c > 0) {
                $lines[] = "- OVERDUE (jatuh tempo terlewat): {$overdue->c} invoice";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getInvoiceContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getSparepartContext(): string
    {
        try {
            $lines = ['[DATA SPAREPART / STOK]:'];

            // Total sparepart
            $total = $this->db->query("SELECT COUNT(*) AS c FROM sparepart")->getRow();
            if ($total) {
                $lines[] = "Total jenis sparepart: {$total->c}";
            }

            // Low stock (stok < 5)
            $lowStock = $this->db->query("
                SELECT sp.kode, sp.desc_sparepart, is2.stok, is2.lokasi_rak
                FROM sparepart sp
                JOIN inventory_spareparts is2 ON sp.id_sparepart = is2.sparepart_id
                WHERE is2.stok < 5
                ORDER BY is2.stok ASC LIMIT 10
            ")->getResultArray();

            if ($lowStock) {
                $lines[] = "\nSparepart stok rendah (< 5):";
                foreach ($lowStock as $s) {
                    $lines[] = "- [{$s['kode']}] {$s['desc_sparepart']} | Stok: {$s['stok']} | Lokasi: {$s['lokasi_rak']}";
                }
            } else {
                $lines[] = "Semua sparepart memiliki stok yang cukup (≥ 5).";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getSparepartContext error: ' . $e->getMessage());
            return '';
        }
    }

    private function getEmployeeContext(): string
    {
        try {
            $lines = ['[DATA KARYAWAN / TEKNISI]:'];

            // Count by departemen
            $stats = $this->db->query("
                SELECT COALESCE(d.nama_departemen, 'Tanpa Departemen') AS departemen,
                       COUNT(*) AS total
                FROM employees e
                LEFT JOIN departemen d ON e.departemen_id = d.id_departemen
                WHERE e.is_active = 1
                GROUP BY d.nama_departemen ORDER BY total DESC LIMIT 10
            ")->getResultArray();

            if (empty($stats)) {
                return '';
            }

            $totalKaryawan = array_sum(array_column($stats, 'total'));
            $lines[] = "Total karyawan aktif: {$totalKaryawan}";
            foreach ($stats as $row) {
                $lines[] = "- {$row['departemen']}: {$row['total']} orang";
            }

            return implode("\n", $lines);
        } catch (\Throwable $e) {
            log_message('warning', '[ChatContext] getEmployeeContext error: ' . $e->getMessage());
            return '';
        }
    }
}
