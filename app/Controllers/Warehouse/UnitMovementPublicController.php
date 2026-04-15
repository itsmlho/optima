<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\UnitMovementModel;

class UnitMovementPublicController extends BaseController
{
    protected UnitMovementModel $movementModel;

    public function __construct()
    {
        $this->movementModel = new UnitMovementModel();
    }

    public function index()
    {
        return view('public/surat_jalan_guard', [
            'title'         => 'Konfirmasi Surat Jalan',
            'companyName'   => 'PT Sarana Mitra Luas Tbk',
            'logoUrl'       => base_url('assets/images/company-logo.svg'),
            'apiLookup'     => base_url('surat-jalan/lookup'),
            'apiCheckpoint' => base_url('surat-jalan/submit-checkpoint'),
        ]);
    }

    /**
     * Flyer / panduan cetak untuk ditempel di ruang satpam.
     */
    public function guide()
    {
        return view('public/surat_jalan_guard_guide', [
            'title'       => 'Panduan Satpam - Konfirmasi Surat Jalan',
            'companyName' => 'PT Sarana Mitra Luas Tbk',
            'smlLogoUrl'  => base_url('assets/images/company-logo.svg'),
            'optimaLogoUrl' => base_url('assets/images/logo-optima.png'),
            'qrImageUrl'  => base_url('assets/images/surat-jalan-qr-satpam.png'),
            'guardPageUrl' => base_url('surat-jalan'),
        ]);
    }

    public function lookup()
    {
        $sj = trim((string) $this->request->getGet('surat_jalan_number'));
        if ($sj === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor surat jalan wajib diisi.',
            ]);
        }

        $movement = $this->movementModel->findBySuratJalanNumber($sj);
        if (! $movement) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nomor surat jalan tidak ditemukan.',
            ]);
        }

        $code = trim((string) $this->request->getGet('verification_code'));
        if (! empty($movement['verification_code']) && $code !== (string) $movement['verification_code']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kode verifikasi tidak sesuai.',
            ]);
        }

        $mid = (int) $movement['id'];
        $this->movementModel->ensureStopsFromHeader($mid);
        $this->movementModel->ensureItemsFromHeader($mid);
        $this->movementModel->synchronizeStopActualFromCheckpoints($mid);
        $bundle = $this->movementModel->getMovementDetailBundle($mid);
        $bundle['items'] = $this->movementModel->enrichItemsForPrint($bundle['items']);

        return $this->response->setJSON([
            'success' => true,
            'data'    => $bundle,
        ]);
    }

    /**
     * Cetak SJ (publik): wajib nomor SJ + kode verifikasi jika SJ punya kode.
     */
    public function printSj()
    {
        $sj = trim((string) $this->request->getGet('sj'));
        if ($sj === '') {
            return $this->response->setStatusCode(400)->setBody('Parameter sj wajib diisi.');
        }

        $movement = $this->movementModel->findBySuratJalanNumber($sj);
        if (! $movement) {
            return $this->response->setStatusCode(404)->setBody('Surat jalan tidak ditemukan.');
        }

        $code = trim((string) $this->request->getGet('code'));
        if (! empty($movement['verification_code']) && $code !== (string) $movement['verification_code']) {
            return $this->response->setStatusCode(403)->setBody('Kode verifikasi tidak sesuai.');
        }

        $bundle = $this->movementModel->getMovementPrintBundle((int) $movement['id']);
        if ($bundle === null) {
            return $this->response->setStatusCode(404)->setBody('Data tidak ditemukan.');
        }

        return view('public/surat_jalan_print', [
            'movement'         => $bundle['movement'],
            'items'            => $bundle['items'],
            'stops'            => $bundle['stops'],
            'checkpoints'      => $bundle['checkpoints'],
            'companyName'      => 'PT Sarana Mitra Luas Tbk',
            'isPublicContext'  => true,
        ]);
    }

    public function submitCheckpoint()
    {
        $post = $this->request->getPost();
        $movementId = (int)($post['movement_id'] ?? 0);
        $stopId = (int)($post['stop_id'] ?? 0);
        $status = strtoupper(trim((string)($post['status'] ?? '')));
        $verificationCode = trim((string)($post['verification_code'] ?? ''));

        if ($movementId <= 0 || $stopId <= 0 || $status === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data checkpoint belum lengkap.',
            ]);
        }

        $checkpointNotes = trim((string)($post['notes'] ?? ''));

        $deliveryMatch = strtolower(trim((string)($post['delivery_match'] ?? 'match')));
        if (! in_array($deliveryMatch, ['match', 'mismatch'], true)) {
            $deliveryMatch = 'match';
        }
        if ($deliveryMatch === 'mismatch') {
            $ad  = trim((string)($post['delivery_actual_driver'] ?? ''));
            $av  = trim((string)($post['delivery_actual_vehicle'] ?? ''));
            $avt = trim((string)($post['delivery_actual_vehicle_type'] ?? ''));
            $ar  = trim((string)($post['delivery_actual_reason'] ?? ''));
            if ($ad === '' || $av === '' || $avt === '' || $ar === '') {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jika detail pengiriman tidak sesuai, lengkapi semua kolom koreksi (driver, no. kendaraan, jenis kendaraan, alasan lapangan).',
                ]);
            }
            $block = "\n\n--- Koreksi detail pengiriman (satpam: tidak sesuai data gudang) ---\n"
                . 'Driver (lapangan): ' . $ad . "\n"
                . 'No. kendaraan (lapangan): ' . $av . "\n"
                . 'Jenis kendaraan (lapangan): ' . $avt . "\n"
                . 'Alasan / keterangan (lapangan): ' . $ar . "\n";
            $checkpointNotes .= $block;
        }

        $movement = $this->movementModel->find($movementId);
        if (!$movement) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Surat jalan tidak ditemukan.',
            ]);
        }

        // Lightweight security for public form.
        if (!empty($movement['verification_code']) && $verificationCode !== (string)$movement['verification_code']) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Kode verifikasi tidak sesuai.',
            ]);
        }

        $verifierPhoneRaw = trim((string)($post['verifier_phone'] ?? ''));
        if (! $this->isValidOptionalIndonesianMobile($verifierPhoneRaw)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Format nomor HP tidak valid. Gunakan 08xxxxxxxxxx (10–13 digit) atau +62 8…, atau kosongkan kolomnya.',
            ]);
        }
        $verifierPhoneNorm = $verifierPhoneRaw === '' ? '' : $this->normalizeIndonesianMobile($verifierPhoneRaw);

        try {
            $checkedItemIds = $post['checked_item_ids'] ?? [];
            $droppedItemIds = $post['dropped_item_ids'] ?? [];
            if (!is_array($checkedItemIds)) {
                $checkedItemIds = [$checkedItemIds];
            }
            if (!is_array($droppedItemIds)) {
                $droppedItemIds = [$droppedItemIds];
            }
            $checkedItemIds = array_values(array_filter(array_map('intval', $checkedItemIds), static fn ($id) => $id > 0));
            $droppedItemIds = array_values(array_filter(array_map('intval', $droppedItemIds), static fn ($id) => $id > 0));

            $stRaw = strtoupper(trim((string)($post['status'] ?? '')));
            $itemCount = $this->movementModel->countItemsForMovement($movementId);

            if ($stRaw === 'BERANGKAT') {
                if ($droppedItemIds !== []) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Di titik berangkat tidak boleh ada barang dengan status Drop — gunakan Barang ada atau tidak ada dalam pengiriman.',
                    ]);
                }
                if ($itemCount > 0 && $checkedItemIds === []) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Di titik berangkat pilih minimal satu barang yang ikut muatan (Barang ada).',
                    ]);
                }
            } elseif ($stRaw === 'SAMPAI') {
                if ($checkedItemIds !== []) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Di titik tujuan akhir tidak boleh memilih Barang ada — gunakan Drop (diterima/diturunkan) atau tidak ada dalam pengiriman.',
                    ]);
                }
                if ($itemCount > 0 && $droppedItemIds === []) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Di titik tujuan akhir pilih minimal satu barang dengan Drop jika barang diterima di lokasi ini.',
                    ]);
                }
            } elseif ($stRaw === 'TRANSIT') {
                if ($itemCount > 0 && $checkedItemIds === [] && $droppedItemIds === []) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Checklist: minimal satu barang pilih Barang ada atau Drop di titik ini.',
                    ]);
                }
            }

            $ok = $this->movementModel->submitCheckpoint($movementId, $stopId, $status, [
                'verifier_name'  => trim((string)($post['verifier_name'] ?? '')),
                'verifier_phone' => $verifierPhoneNorm,
                'notes'          => $checkpointNotes,
                'checkpoint_at'  => !empty($post['checkpoint_at']) ? $post['checkpoint_at'] : date('Y-m-d H:i:s'),
                'created_ip'     => $this->request->getIPAddress(),
                'user_agent'     => (string)$this->request->getUserAgent(),
                'checked_item_ids' => $checkedItemIds,
                'dropped_item_ids' => $droppedItemIds,
            ]);

            if (!$ok) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal menyimpan checkpoint.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Konfirmasi berhasil. Data telah disimpan.',
            ]);
        } catch (\RuntimeException $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage(),
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[UnitMovementPublicController::submitCheckpoint] ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan pada sistem.',
            ]);
        }
    }

    /**
     * Normalisasi nomor HP seluler Indonesia ke bentuk 08… untuk disimpan.
     */
    private function normalizeIndonesianMobile(string $raw): string
    {
        $s = preg_replace('/[\s.\-]/', '', trim($raw));
        if ($s === '') {
            return '';
        }
        if (str_starts_with($s, '+62')) {
            return '0' . substr($s, 3);
        }
        if (str_starts_with($s, '62') && strlen($s) > 2 && ($s[2] ?? '') === '8') {
            return '0' . substr($s, 2);
        }
        if (($s[0] ?? '') === '8' && ! str_starts_with($s, '08')) {
            return '0' . $s;
        }

        return $s;
    }

    /** Kosong = valid (opsional); isi harus pola 08… (10–13 digit). */
    private function isValidOptionalIndonesianMobile(string $raw): bool
    {
        $n = $this->normalizeIndonesianMobile($raw);
        if ($n === '') {
            return true;
        }

        return (bool) preg_match('/^08[1-9]\d{7,11}$/', $n);
    }
}

