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

        try {
            $checkedItemIds = $post['checked_item_ids'] ?? [];
            $droppedItemIds = $post['dropped_item_ids'] ?? [];
            if (!is_array($checkedItemIds)) {
                $checkedItemIds = [$checkedItemIds];
            }
            if (!is_array($droppedItemIds)) {
                $droppedItemIds = [$droppedItemIds];
            }

            $ok = $this->movementModel->submitCheckpoint($movementId, $stopId, $status, [
                'verifier_name'  => trim((string)($post['verifier_name'] ?? '')),
                'verifier_phone' => trim((string)($post['verifier_phone'] ?? '')),
                'notes'          => trim((string)($post['notes'] ?? '')),
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
                'message' => 'Checkpoint berhasil dikonfirmasi.',
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
}

