<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SiloModel;
use App\Models\UnitAssetModel;
use App\Models\DepartemenModel;

class Perizinan extends BaseController
{
    protected $siloModel;
    protected $unitModel;
    protected $departemenModel;

    public function __construct()
    {
        $this->siloModel = new SiloModel();
        $this->unitModel = new UnitAssetModel();
        $this->departemenModel = new DepartemenModel();
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
        $departments = $this->departemenModel->findAll();

        $data = [
            'title' => 'SILO Management',
            'page_title' => 'SILO (Surat Izin Layak Operasi)',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/perizinan/silo' => 'SILO Management'
            ],
            'stats' => $stats,
            'current_status' => $status,
            'departments' => $departments,
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
                $filterDepartemen = $this->request->getGet('filter_departemen');
                log_message('debug', 'Perizinan::getSiloList - BELUM_ADA status requested, search: ' . $search);
                $data = $this->siloModel->getUnitsWithoutSilo($search, $filterDepartemen);
                log_message('debug', 'Perizinan::getSiloList - BELUM_ADA status, found ' . count($data) . ' units');
                if (count($data) > 0) {
                    log_message('debug', 'Perizinan::getSiloList - Sample unit: ' . json_encode($data[0]));
                }
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $data
                ]);
            }

            // Handle special statuses for expired
            if ($status === 'akan-expired') {
                $filters = [
                    'status' => 'SILO_TERBIT',
                    'search' => $this->request->getGet('search'),
                    'filter_departemen' => $this->request->getGet('filter_departemen'),
                    'expiring_soon' => 30, // Will expire in 30 days
                ];
                $data = $this->siloModel->getAllWithUnit($filters);
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $data
                ]);
            }
            
            if ($status === 'sudah-expired') {
                $filters = [
                    'status' => 'SILO_TERBIT',
                    'search' => $this->request->getGet('search'),
                    'filter_departemen' => $this->request->getGet('filter_departemen'),
                    'expired' => true,
                ];
                $data = $this->siloModel->getAllWithUnit($filters);
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $data
                ]);
            }

            // If status is 'all', combine data with SILO and units without SILO
            if ($status === 'all' || empty($status)) {
                $filters = [
                    'search' => $this->request->getGet('search'),
                    'filter_status' => $this->request->getGet('filter_status'),
                    'filter_departemen' => $this->request->getGet('filter_departemen'),
                    'expiring_soon' => $this->request->getGet('expiring_soon'),
                    'expired' => $this->request->getGet('expired'),
                ];

                // Remove empty filters
                $filters = array_filter($filters, function($value) {
                    return $value !== null && $value !== '';
                });

                // Get data with SILO
                $dataWithSilo = $this->siloModel->getAllWithUnit($filters);
                
                // Get units without SILO (only if no filter_status is applied)
                $dataWithoutSilo = [];
                if (empty($filters['filter_status'])) {
                    $search = $filters['search'] ?? '';
                    $filterDepartemen = $filters['filter_departemen'] ?? null;
                    $dataWithoutSilo = $this->siloModel->getUnitsWithoutSilo($search, $filterDepartemen);
                }
                
                // Combine both datasets
                $data = array_merge($dataWithSilo, $dataWithoutSilo);
            } else {
                $filters = [
                    'status' => $status,
                    'search' => $this->request->getGet('search'),
                    'filter_status' => $this->request->getGet('filter_status'),
                    'filter_departemen' => $this->request->getGet('filter_departemen'),
                    'expiring_soon' => $this->request->getGet('expiring_soon'),
                    'expired' => $this->request->getGet('expired'),
                ];

                // Remove empty filters
                $filters = array_filter($filters, function($value) {
                    return $value !== null && $value !== '';
                });

                $data = $this->siloModel->getAllWithUnit($filters);
            }

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
     * Get SILO statistics (AJAX)
     */
    public function getSiloStats()
    {
        if (!$this->hasPermission('perizinan.access')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Access denied'
            ])->setStatusCode(403);
        }

        try {
            $stats = $this->siloModel->getStatistics();
            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Perizinan::getSiloStats Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading statistics: ' . $e->getMessage()
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

        try {
            $db = \Config\Database::connect();
            $builder = $db->table('inventory_unit iu');
            $builder->select('iu.id_inventory_unit, iu.no_unit, iu.serial_number,
                c.customer_name as nama_perusahaan,
                tu.jenis as jenis_unit,
                k.kapasitas_unit,
                d.nama_departemen as departemen');
            $builder->join('customers c', 'c.id = iu.customer_id', 'left');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            $builder->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left');
            $builder->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left');
            
            $units = $builder->get()->getResultArray();
            $availableUnits = [];

            foreach ($units as $unit) {
                if ($this->siloModel->canCreateApplication($unit['id_inventory_unit'])) {
                    // Format: no_unit - nama_perusahaan (sn - jenis_unit - kapasitas_unit - departemen)
                    $sn = $unit['serial_number'] ?? 'N/A';
                    $jenis = $unit['jenis_unit'] ?? 'N/A';
                    $kapasitas = $unit['kapasitas_unit'] ?? 'N/A';
                    $departemen = $unit['departemen'] ?? 'N/A';
                    $namaPerusahaan = $unit['nama_perusahaan'] ?? 'N/A';
                    
                    $label = $unit['no_unit'] . ' - ' . $namaPerusahaan . ' (' . $sn . ' - ' . $jenis . ' - ' . $kapasitas . ' - ' . $departemen . ')';
                    
                    $availableUnits[] = [
                        'id' => $unit['id_inventory_unit'],
                        'no_unit' => $unit['no_unit'],
                        'serial_number' => $unit['serial_number'],
                        'nama_perusahaan' => $namaPerusahaan,
                        'label' => $label
                    ];
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $availableUnits
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Perizinan::getAvailableUnits Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error loading units: ' . $e->getMessage(),
                'data' => []
            ])->setStatusCode(500);
        }
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

        // Get unit_ids (can be array for multiple units)
        $unitIds = $this->request->getPost('unit_ids');
        if (is_string($unitIds)) {
            $unitIds = [$unitIds];
        }
        
        if (empty($unitIds) || !is_array($unitIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih minimal 1 unit'
            ])->setStatusCode(400);
        }

        $namaPtPjk3 = $this->request->getPost('nama_pt_pjk3');
        if (empty($namaPtPjk3) || trim($namaPtPjk3) === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama PT PJK3 harus diisi'
            ])->setStatusCode(400);
        }

        $tanggalPengajuan = $this->request->getPost('tanggal_pengajuan_pjk3');
        if (empty($tanggalPengajuan)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Tanggal pengajuan harus diisi'
            ])->setStatusCode(400);
        }

        try {
            $createdCount = 0;
            $failedUnits = [];
            
            foreach ($unitIds as $unitId) {
                // Validate unit ID
                if (empty($unitId) || !is_numeric($unitId)) {
                    $failedUnits[] = $unitId . ' (ID tidak valid)';
                    continue;
                }
                
                // Check if unit can create application
                if (!$this->siloModel->canCreateApplication($unitId)) {
                    $failedUnits[] = $unitId . ' (sudah ada SILO aktif)';
                    continue;
                }

                $data = [
                    'unit_id' => $unitId,
                    'status' => SiloModel::STATUS_PENGAJUAN_PJK3,
                    'nama_pt_pjk3' => trim($namaPtPjk3),
                    'tanggal_pengajuan_pjk3' => $tanggalPengajuan,
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
                    $createdCount++;
                } else {
                    $failedUnits[] = $unitId . ' (gagal insert)';
                }
            }
            
            if ($createdCount > 0) {
                $message = 'Berhasil membuat ' . $createdCount . ' pengajuan SILO';
                if (!empty($failedUnits)) {
                    $message .= '. Gagal: ' . implode(', ', $failedUnits);
                }
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message,
                    'created_count' => $createdCount,
                    'failed_count' => count($failedUnits)
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal membuat pengajuan SILO. ' . implode(', ', $failedUnits)
                ])->setStatusCode(400);
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
                case SiloModel::STATUS_SURAT_KETERANGAN_PJK3:
                    $updateData['nomor_surat_keterangan_pjk3'] = $this->request->getPost('nomor_surat_keterangan_pjk3');
                    $updateData['tanggal_surat_keterangan_pjk3'] = $this->request->getPost('tanggal_surat_keterangan_pjk3');
                    break;

                case SiloModel::STATUS_PENGAJUAN_UPTD:
                    $lokasiDisnaker = $this->request->getPost('lokasi_disnaker');
                    if (empty($lokasiDisnaker) || trim($lokasiDisnaker) === '') {
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Lokasi DISNAKER harus diisi'
                        ])->setStatusCode(400);
                    }
                    $updateData['tanggal_pengajuan_uptd'] = $this->request->getPost('tanggal_pengajuan_uptd');
                    $updateData['lokasi_disnaker'] = trim($lokasiDisnaker);
                    // Catatan disimpan ke history via keterangan, tidak perlu catatan_pengajuan_uptd lagi
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
                'rules' => 'uploaded[file]|max_size[file,15360]|ext_in[file,pdf,jpg,jpeg,png]'
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
            if (!$file) {
                log_message('error', 'Upload File: File tidak ditemukan di request');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File tidak ditemukan'
                ])->setStatusCode(400);
            }
            
            if (!$file->isValid()) {
                $error = $file->getErrorString();
                log_message('error', 'Upload File: File tidak valid - ' . $error);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File tidak valid: ' . $error
                ])->setStatusCode(400);
            }

            // Check file size (15MB = 15728640 bytes)
            $maxSize = 15 * 1024 * 1024; // 15MB in bytes
            if ($file->getSize() > $maxSize) {
                log_message('error', 'Upload File: File terlalu besar - ' . $file->getSize() . ' bytes');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'File terlalu besar. Maksimal 15MB'
                ])->setStatusCode(400);
            }

            // Create upload directory with full path
            $baseUploadPath = FCPATH . 'uploads/silo/';
            $uploadPath = $baseUploadPath . $fileType . '/';
            
            // Ensure base directory exists and is writable
            if (!is_dir($baseUploadPath)) {
                if (!mkdir($baseUploadPath, 0777, true)) {
                    log_message('error', 'Upload File: Gagal membuat base direktori - ' . $baseUploadPath);
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal membuat direktori upload'
                    ])->setStatusCode(500);
                }
                @chmod($baseUploadPath, 0777);
            } else {
                @chmod($baseUploadPath, 0777);
            }
            
            // Create type-specific directory
            if (!is_dir($uploadPath)) {
                if (!mkdir($uploadPath, 0777, true)) {
                    log_message('error', 'Upload File: Gagal membuat direktori - ' . $uploadPath);
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal membuat direktori upload'
                    ])->setStatusCode(500);
                }
                @chmod($uploadPath, 0777);
            } else {
                @chmod($uploadPath, 0777);
            }

            // Final check if directory is writable
            if (!is_writable($uploadPath)) {
                $perms = file_exists($uploadPath) ? substr(sprintf('%o', fileperms($uploadPath)), -4) : 'unknown';
                log_message('error', 'Upload File: Direktori tidak writable - Path: ' . $uploadPath . ' | Permission: ' . $perms);
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Direktori upload tidak dapat ditulis. Silakan hubungi administrator untuk memperbaiki permission.'
                ])->setStatusCode(500);
            }

            // Generate filename
            $extension = $file->getExtension();
            $newName = $fileType . '_' . $id . '_' . time() . '.' . $extension;
            $fullPath = $uploadPath . $newName;
            
            // Try to move file using CodeIgniter's move method
            if (!$file->move($uploadPath, $newName)) {
                // If move fails, try using move_uploaded_file directly
                $tempPath = $file->getTempName();
                if (is_uploaded_file($tempPath)) {
                    if (!move_uploaded_file($tempPath, $fullPath)) {
                        $error = $file->getErrorString();
                        $lastError = error_get_last();
                        log_message('error', 'Upload File: Gagal move file - ' . $error . ' | Last error: ' . ($lastError ? $lastError['message'] : 'none'));
                        return $this->response->setJSON([
                            'success' => false,
                            'message' => 'Gagal upload file: ' . ($lastError ? $lastError['message'] : $error)
                        ])->setStatusCode(500);
                    }
                } else {
                    $error = $file->getErrorString();
                    log_message('error', 'Upload File: File tidak valid atau sudah dipindahkan - ' . $error);
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal upload file: ' . $error
                    ])->setStatusCode(500);
                }
            }

            // Set file permissions
            $fullPath = $uploadPath . $newName;
            @chmod($fullPath, 0644);

            // Update database
            $filePath = 'uploads/silo/' . $fileType . '/' . $newName;
            $fieldName = $fileType === 'pjk3' ? 'file_surat_keterangan_pjk3' : 'file_silo';
            
            $updateData = [
                $fieldName => $filePath,
                'updated_by' => session()->get('user_id'),
            ];

            if ($this->siloModel->update($id, $updateData)) {
                log_message('info', 'Upload File: File berhasil diupload - ' . $filePath);
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'File berhasil diupload',
                    'file_path' => base_url($filePath)
                ]);
            } else {
                // Delete uploaded file if database update fails
                @unlink($fullPath);
                $errors = $this->siloModel->errors();
                log_message('error', 'Upload File: Gagal update database - ' . json_encode($errors));
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Gagal update database: ' . implode(', ', $errors)
                ])->setStatusCode(500);
            }
        } catch (\Exception $e) {
            log_message('error', 'Upload File Exception: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Preview file (for viewing in iframe/img)
     */
    public function previewFile($id, $type)
    {
        if (!$this->hasPermission('perizinan.access')) {
            return $this->response->setStatusCode(403)->setBody('Access denied');
        }

        $silo = $this->siloModel->find($id);
        if (!$silo) {
            return $this->response->setStatusCode(404)->setBody('SILO tidak ditemukan');
        }

        $fieldName = $type === 'pjk3' ? 'file_surat_keterangan_pjk3' : 'file_silo';
        $filePath = $silo[$fieldName] ?? null;

        if (!$filePath || !file_exists(FCPATH . $filePath)) {
            log_message('error', 'Preview File: File tidak ditemukan - Path: ' . FCPATH . $filePath);
            return $this->response->setStatusCode(404)->setBody('File tidak ditemukan');
        }

        $fullPath = FCPATH . $filePath;
        
        // Get file extension to determine MIME type
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);
        $mimeTypes = [
            'pdf' => 'application/pdf',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];
        
        $mimeType = $mimeTypes[strtolower($extension)] ?? mime_content_type($fullPath) ?? 'application/octet-stream';
        
        // Set appropriate headers for preview
        $this->response->setHeader('Content-Type', $mimeType);
        $this->response->setHeader('Content-Disposition', 'inline; filename="' . basename($filePath) . '"');
        $this->response->setHeader('Cache-Control', 'public, max-age=3600');
        $this->response->setHeader('X-Content-Type-Options', 'nosniff');
        
        // Read and output file
        $fileContent = file_get_contents($fullPath);
        if ($fileContent === false) {
            log_message('error', 'Preview File: Gagal membaca file - Path: ' . $fullPath);
            return $this->response->setStatusCode(500)->setBody('Gagal membaca file');
        }
        
        return $this->response->setBody($fileContent);
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
