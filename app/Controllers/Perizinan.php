<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiloModel;
use App\Models\UnitAssetModel;

class Perizinan extends BaseController
{
    protected $siloModel;
    protected $unitModel;

    public function __construct()
    {
        $this->siloModel = new SiloModel();
        $this->unitModel = new UnitAssetModel();
    }

    /**
     * SILO (Surat Izin Layak Operasi) Management
     */
    public function silo()
    {
        if (!$this->hasPermission('perizinan.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $stats = $this->siloModel->getStatistics();
        $status = $this->request->getGet('status') ?? 'all';

        $data = [
            'title' => 'SILO Management',
            'page_title' => 'SILO (Surat Izin Layak Operasi)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/perizinan/silo' => 'SILO Management'
            ],
            'stats' => $stats,
            'current_status' => $status,
        ];

        return view('perizinan/silo', $data);
    }

    /**
     * Get SILO list (AJAX)
     */
    public function getSiloList()
    {
        if (!$this->hasPermission('perizinan.access')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        try {
            $status = $this->request->getGet('status');
            
            // If status is 'BELUM_ADA', return units without SILO
            if ($status === 'BELUM_ADA') {
                $search = $this->request->getGet('search') ?? '';
                log_message('debug', 'Perizinan::getSiloList - BELUM_ADA status requested, search: ' . $search);
                $data = $this->siloModel->getUnitsWithoutSilo($search);
                log_message('debug', 'Perizinan::getSiloList - BELUM_ADA status, found ' . count($data) . ' units');
                if (count($data) > 0) {
                    log_message('debug', 'Perizinan::getSiloList - Sample unit: ' . json_encode($data[0]));
                }
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $data
                ]);
            }

            $filters = [
                'status' => $status,
                'search' => $this->request->getGet('search'),
                'expiring_soon' => $this->request->getGet('expiring_soon'),
                'expired' => $this->request->getGet('expired'),
            ];

            // Remove empty filters
            $filters = array_filter($filters, function($value) {
                return $value !== null && $value !== '';
            });

            $data = $this->siloModel->getAllWithUnit($filters);

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Perizinan::getSiloList Error: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading data: ' . $e->getMessage(),
                'data' => []
            ])->setStatusCode(500);
        }
    }

    /**
     * Get units available for SILO application
     */
    public function getAvailableUnits()
    {
        if (!$this->hasPermission('perizinan.manage')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $units = $this->unitModel->findAll();
        $availableUnits = [];

        foreach ($units as $unit) {
            if ($this->siloModel->canCreateApplication($unit['id_inventory_unit'])) {
                $availableUnits[] = [
                    'id' => $unit['id_inventory_unit'],
                    'no_unit' => $unit['no_unit'],
                    'serial_number' => $unit['serial_number'],
                    'label' => 'FL-' . $unit['no_unit'] . ' (' . ($unit['serial_number'] ?? 'N/A') . ')'
                ];
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $availableUnits
        ]);
    }

    /**
     * Create new SILO application
     */
    public function createSilo()
    {
        if (!$this->hasPermission('perizinan.manage')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'unit_id' => 'required|is_natural_no_zero',
            'tanggal_pengajuan_pjk3' => 'required|valid_date',
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validation->getErrors()
            ])->setStatusCode(400);
        }

        $unitId = $this->request->getPost('unit_id');

        // Check if unit can create application
        if (!$this->siloModel->canCreateApplication($unitId)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit ini sudah memiliki SILO aktif. Silakan update status yang ada atau tunggu hingga expired.'
            ])->setStatusCode(400);
        }

        try {
            $data = [
                'unit_id' => $unitId,
                'status' => SiloModel::STATUS_PENGAJUAN_PJK3,
                'tanggal_pengajuan_pjk3' => $this->request->getPost('tanggal_pengajuan_pjk3'),
                'catatan_pengajuan_pjk3' => $this->request->getPost('catatan_pengajuan_pjk3'),
                'created_by' => session()->get('user_id'),
            ];

            $siloId = $this->siloModel->insert($data);

            if ($siloId) {
                // Add history
                $this->siloModel->addHistory(
                    $siloId,
                    null,
                    SiloModel::STATUS_PENGAJUAN_PJK3,
                    'Pengajuan SILO baru dibuat',
                    session()->get('user_id')
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Pengajuan SILO berhasil dibuat',
                    'data' => ['id_silo' => $siloId]
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat pengajuan SILO'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get SILO detail
     */
    public function getSiloDetail($id)
    {
        if (!$this->hasPermission('perizinan.access')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        $silo = $this->siloModel->getByIdWithUnit($id);
        
        if (!$silo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'SILO tidak ditemukan'
            ])->setStatusCode(404);
        }

        $history = $this->siloModel->getHistory($id);

        return $this->response->setJSON([
            'success' => true,
            'data' => $silo,
            'history' => $history
        ]);
    }

    /**
     * Update SILO status
     */
    public function updateSiloStatus($id)
    {
        if (!$this->hasPermission('perizinan.manage')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $silo = $this->siloModel->find($id);
        if (!$silo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'SILO tidak ditemukan'
            ])->setStatusCode(404);
        }

        $newStatus = $this->request->getPost('status');
        $nextStatus = $this->siloModel->getNextStatus($silo['status']);

        // Validate status transition
        if ($newStatus !== $nextStatus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status tidak valid. Status berikutnya yang diizinkan: ' . $this->siloModel->getStatusLabel($nextStatus)
            ])->setStatusCode(400);
        }

        try {
            $updateData = [
                'status' => $newStatus,
                'updated_by' => session()->get('user_id'),
            ];

            // Update fields based on status
            switch ($newStatus) {
                case SiloModel::STATUS_TESTING_PJK3:
                    $updateData['tanggal_testing_pjk3'] = $this->request->getPost('tanggal_testing_pjk3');
                    $updateData['hasil_testing_pjk3'] = $this->request->getPost('hasil_testing_pjk3');
                    break;

                case SiloModel::STATUS_SURAT_KETERANGAN_PJK3:
                    $updateData['nomor_surat_keterangan_pjk3'] = $this->request->getPost('nomor_surat_keterangan_pjk3');
                    $updateData['tanggal_surat_keterangan_pjk3'] = $this->request->getPost('tanggal_surat_keterangan_pjk3');
                    break;

                case SiloModel::STATUS_PENGAJUAN_UPTD:
                    $updateData['tanggal_pengajuan_uptd'] = $this->request->getPost('tanggal_pengajuan_uptd');
                    $updateData['catatan_pengajuan_uptd'] = $this->request->getPost('catatan_pengajuan_uptd');
                    break;

                case SiloModel::STATUS_PROSES_UPTD:
                    $updateData['tanggal_proses_uptd'] = $this->request->getPost('tanggal_proses_uptd');
                    $updateData['catatan_proses_uptd'] = $this->request->getPost('catatan_proses_uptd');
                    break;

                case SiloModel::STATUS_SILO_TERBIT:
                    $updateData['nomor_silo'] = $this->request->getPost('nomor_silo');
                    $updateData['tanggal_terbit_silo'] = $this->request->getPost('tanggal_terbit_silo');
                    $updateData['tanggal_expired_silo'] = $this->request->getPost('tanggal_expired_silo');
                    break;
            }

            if ($this->siloModel->update($id, $updateData)) {
                // Add history
                $this->siloModel->addHistory(
                    $id,
                    $silo['status'],
                    $newStatus,
                    $this->request->getPost('keterangan'),
                    session()->get('user_id')
                );

                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Status berhasil diupdate'
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal update status'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Upload file (PJK3 or SILO)
     */
    public function uploadFile($id)
    {
        if (!$this->hasPermission('perizinan.manage')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid request'
            ])->setStatusCode(400);
        }

        $silo = $this->siloModel->find($id);
        if (!$silo) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'SILO tidak ditemukan'
            ])->setStatusCode(404);
        }

        $fileType = $this->request->getPost('file_type'); // 'pjk3' or 'silo'
        
        if (!in_array($fileType, ['pjk3', 'silo'])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File type tidak valid'
            ])->setStatusCode(400);
        }

        $validation = \Config\Services::validation();
        $validation->setRules([
            'file' => [
                'label' => 'File',
                'rules' => 'uploaded[file]|max_size[file,5120]|ext_in[file,pdf,jpg,jpeg,png]'
            ]
        ]);

        if (!$validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'File tidak valid',
                'errors' => $validation->getErrors()
            ])->setStatusCode(400);
        }

        try {
            $file = $this->request->getFile('file');
            if (!$file || !$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File tidak valid'
                ])->setStatusCode(400);
            }

            // Create upload directory
            $uploadPath = FCPATH . 'uploads/silo/' . $fileType . '/';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0777, true);
                @chmod($uploadPath, 0777);
            }

            // Generate filename
            $extension = $file->getExtension();
            $newName = $fileType . '_' . $id . '_' . time() . '.' . $extension;
            
            if (!$file->move($uploadPath, $newName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal upload file'
                ])->setStatusCode(500);
            }

            @chmod($uploadPath . $newName, 0666);

            // Update database
            $filePath = 'uploads/silo/' . $fileType . '/' . $newName;
            $fieldName = $fileType === 'pjk3' ? 'file_surat_keterangan_pjk3' : 'file_silo';
            
            $updateData = [
                $fieldName => $filePath,
                'updated_by' => session()->get('user_id'),
            ];

            if ($this->siloModel->update($id, $updateData)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'File berhasil diupload',
                    'file_path' => base_url($filePath)
                ]);
            } else {
                // Delete uploaded file if database update fails
                @unlink($uploadPath . $newName);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal update database'
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Download file
     */
    public function downloadFile($id, $type)
    {
        if (!$this->hasPermission('perizinan.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $silo = $this->siloModel->find($id);
        if (!$silo) {
            return redirect()->to('/perizinan/silo')->with('error', 'SILO tidak ditemukan');
        }

        $fieldName = $type === 'pjk3' ? 'file_surat_keterangan_pjk3' : 'file_silo';
        $filePath = $silo[$fieldName] ?? null;

        if (!$filePath || !file_exists(FCPATH . $filePath)) {
            return redirect()->to('/perizinan/silo')->with('error', 'File tidak ditemukan');
        }

        return $this->response->download(FCPATH . $filePath, null);
    }

    /**
     * EMISI (Surat Izin Emisi Gas Buang) Management
     */
    public function emisi()
    {
        if (!$this->hasPermission('perizinan.access')) {
            return redirect()->to('/dashboard')->with('error', 'Access denied.');
        }

        $data = [
            'title' => 'EMISI Management',
            'page_title' => 'EMISI (Surat Izin Emisi Gas Buang)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/perizinan/emisi' => 'EMISI Management'
            ]
        ];

        return view('perizinan/emisi', $data);
    }
}
