<?php

namespace App\Controllers\Marketing;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Workflow Controller
 * Handles new workflow system for Delivery Instructions and SPK auto-generation
 * 
 * New Workflow Components:
 * 1. Jenis Perintah Kerja (ANTAR, TARIK, TUKAR, RELOKASI)
 * 2. Tujuan Perintah (Dynamic based on Jenis)
 * 3. Status Eksekusi (System-controlled status)
 */
class Workflow extends BaseController
{
    protected $deliveryInstructionModel;
    protected $spkModel;
    protected $workflowTujuanModel;

    public function __construct()
    {
        $this->deliveryInstructionModel = new \App\Models\DeliveryInstructionModel();
        $this->spkModel = new \App\Models\SpkModel();
        // $this->workflowTujuanModel = new \App\Models\WorkflowTujuanModel(); // Will be created if needed
    }

    /**
     * Get available tujuan perintah based on jenis perintah
     * API endpoint for dynamic dropdown
     */
    public function getTujuanOptions()
    {
        $jenis = $this->request->getGet('jenis');
        
        if (!$jenis) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Jenis perintah parameter required'
            ]);
        }

        $db = \Config\Database::connect();
        
        try {
            // Get jenis_perintah_kerja ID by kode
            $jenisQuery = $db->query("SELECT id FROM jenis_perintah_kerja WHERE kode = ?", [$jenis]);
            $jenisData = $jenisQuery->getRow();
            
            if (!$jenisData) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Jenis perintah tidak ditemukan'
                ]);
            }
            
            // Get tujuan options for this jenis
            $tujuanQuery = $db->query("
                SELECT kode as value, nama as label, deskripsi 
                FROM tujuan_perintah_kerja 
                WHERE jenis_perintah_id = ? AND aktif = 1
                ORDER BY nama
            ", [$jenisData->id]);
            
            $options = $tujuanQuery->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $options
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all jenis perintah kerja
     * API endpoint for dropdown
     */
    public function getJenisPerintahOptions()
    {
        $db = \Config\Database::connect();
        
        try {
            $query = $db->query("
                SELECT kode as value, nama as label, deskripsi 
                FROM jenis_perintah_kerja 
                WHERE aktif = 1
                ORDER BY nama
            ");
            
            $options = $query->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $options
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get status eksekusi workflow options
     * API endpoint for dropdown
     */
    public function getStatusEksekusiOptions()
    {
        $db = \Config\Database::connect();
        
        try {
            $query = $db->query("
                SELECT id as value, nama as label, deskripsi, warna
                FROM status_eksekusi_workflow 
                WHERE aktif = 1
                ORDER BY urutan
            ");
            
            $options = $query->getResultArray();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $options
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Auto-generate SPK when DI is created with ANTAR or TUKAR jenis perintah
     * This method should be called from DI controller after DI creation
     */
    public function autoGenerateSpk($diId, $diData)
    {
        $db = \Config\Database::connect();
        
        try {
            // Only generate SPK for jenis that require unit preparation
            if (!in_array($diData['jenis_perintah'], ['ANTAR', 'TUKAR'])) {
                return [
                    'success' => true,
                    'message' => 'No SPK needed for this jenis perintah',
                    'spk_id' => null
                ];
            }

            // Get jenis_perintah_kerja_id and tujuan_perintah_kerja_id
            $jenisQuery = $db->query("SELECT id FROM jenis_perintah_kerja WHERE kode = ?", [$diData['jenis_perintah']]);
            $jenisData = $jenisQuery->getRow();
            
            $tujuanQuery = $db->query("
                SELECT id FROM tujuan_perintah_kerja 
                WHERE jenis_perintah_id = ? AND kode = ?
            ", [$jenisData->id, $diData['tujuan_perintah']]);
            $tujuanData = $tujuanQuery->getRow();

            // Generate SPK number
            $spkNumber = $this->generateSpkNumber();

            // Prepare SPK data with workflow
            $spkData = [
                'nomor_spk' => $spkNumber,
                'jenis_spk' => 'UNIT',
                'po_kontrak_nomor' => $diData['po_kontrak_nomor'] ?? null,
                'pelanggan' => $diData['pelanggan'],
                'lokasi' => $diData['lokasi'] ?? null,
                'delivery_plan' => $diData['tanggal_kirim'] ?? null,
                'status' => 'SUBMITTED',
                'jenis_perintah_kerja_id' => $jenisData->id,
                'tujuan_perintah_kerja_id' => $tujuanData->id,
                'status_eksekusi_workflow_id' => 2, // Persiapan
                'workflow_notes' => 'Auto-generated from DI: ' . ($diData['nomor_di'] ?? $diId),
                'workflow_created_at' => date('Y-m-d H:i:s'),
                'workflow_updated_at' => date('Y-m-d H:i:s'),
                'dibuat_oleh' => session()->get('user_id') ?? 1,
                'dibuat_pada' => date('Y-m-d H:i:s'),
                'catatan' => 'Auto-generated from DI'
            ];

            // Insert SPK
            $spkId = $this->spkModel->insert($spkData);

            if (!$spkId) {
                throw new \Exception('Failed to insert SPK');
            }

            // Update DI with SPK ID
            $this->deliveryInstructionModel->update($diId, [
                'spk_id' => $spkId,
                'status_eksekusi' => 'Persiapan Unit'
            ]);

            // Log the auto-generation
            log_message('info', "Auto-generated SPK #{$spkNumber} (ID: {$spkId}) from DI #{$diId}");

            return [
                'success' => true,
                'message' => 'SPK auto-generated successfully',
                'spk_id' => $spkId,
                'spk_number' => $spkNumber
            ];

        } catch (\Exception $e) {
            log_message('error', 'Auto-generate SPK failed: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Failed to auto-generate SPK: ' . $e->getMessage(),
                'spk_id' => null
            ];
        }
    }

    /**
     * Generate unique SPK number
     */
    private function generateSpkNumber()
    {
        $prefix = 'SPK/' . date('Ym') . '/';
        
        // Get the last SPK number for current month
        $lastSpk = $this->spkModel
            ->where('nomor_spk LIKE', $prefix . '%')
            ->orderBy('id', 'DESC')
            ->first();

        if ($lastSpk) {
            // Extract sequence number from last SPK
            $lastNumber = (int) substr($lastSpk['nomor_spk'], -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Update status eksekusi for DI
     * API endpoint for status updates
     */
    public function updateStatusEksekusi()
    {
        $diId = $this->request->getPost('di_id');
        $newStatus = $this->request->getPost('status_eksekusi');
        $notes = $this->request->getPost('notes');

        if (!$diId || !$newStatus) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'DI ID and status_eksekusi are required'
            ]);
        }

        // Validate status
        $validStatuses = ['Direncanakan', 'Persiapan Unit', 'Siap Kirim', 'Siap Ambil', 'Dalam Perjalanan', 'Selesai', 'Dibatalkan'];
        
        if (!in_array($newStatus, $validStatuses)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid status eksekusi'
            ]);
        }

        try {
            // Update DI status
            $updated = $this->deliveryInstructionModel->update($diId, [
                'status_eksekusi' => $newStatus,
                'diperbarui_pada' => date('Y-m-d H:i:s')
            ]);

            if (!$updated) {
                throw new \Exception('Failed to update DI status');
            }

            // Auto-update related SPK status if needed
            $this->autoUpdateSpkStatus($diId, $newStatus);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Status updated successfully'
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Auto-update SPK status based on DI status changes
     */
    private function autoUpdateSpkStatus($diId, $diStatus)
    {
        // Get DI data
        $di = $this->deliveryInstructionModel->find($diId);
        
        if (!$di || !$di['spk_id']) {
            return; // No SPK to update
        }

        // Map DI status to SPK status
        $statusMapping = [
            'Persiapan Unit' => 'IN_PROGRESS',
            'Siap Kirim' => 'READY',
            'Dalam Perjalanan' => 'READY', // SPK stays READY until delivery is complete
            'Selesai' => 'COMPLETED',
            'Dibatalkan' => 'CANCELLED'
        ];

        $spkStatus = $statusMapping[$diStatus] ?? null;

        if ($spkStatus) {
            $this->spkModel->update($di['spk_id'], [
                'status' => $spkStatus,
                'diperbarui_pada' => date('Y-m-d H:i:s')
            ]);

            log_message('info', "Auto-updated SPK #{$di['spk_id']} status to {$spkStatus} based on DI #{$diId} status change to {$diStatus}");
        }
    }

    /**
     * Get workflow statistics for dashboard
     */
    public function getWorkflowStats()
    {
        try {
            $stats = [
                // DI statistics by jenis perintah
                'di_by_jenis' => $this->deliveryInstructionModel
                    ->select('jenis_perintah, COUNT(*) as count')
                    ->groupBy('jenis_perintah')
                    ->findAll(),
                
                // DI statistics by status eksekusi
                'di_by_status' => $this->deliveryInstructionModel
                    ->select('status_eksekusi, COUNT(*) as count')
                    ->groupBy('status_eksekusi')
                    ->findAll(),
                
                // Auto-generated SPK count
                'auto_spk_count' => $this->spkModel
                    ->where('auto_generated', true)
                    ->countAllResults(),
                
                // Total SPK count
                'total_spk_count' => $this->spkModel->countAllResults(),
                
                // DI without SPK (TARIK, RELOKASI)
                'di_without_spk' => $this->deliveryInstructionModel
                    ->whereIn('jenis_perintah', ['TARIK', 'RELOKASI'])
                    ->countAllResults()
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to get stats: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate workflow data before saving
     */
    public function validateWorkflowData($jenisPerintah, $tujuanPerintah)
    {
        // Basic validation
        if (empty($jenisPerintah) || empty($tujuanPerintah)) {
            return [
                'valid' => false,
                'message' => 'Jenis Perintah and Tujuan Perintah are required'
            ];
        }

        // Validate jenis perintah
        $validJenis = ['ANTAR', 'TARIK', 'TUKAR', 'RELOKASI'];
        if (!in_array($jenisPerintah, $validJenis)) {
            return [
                'valid' => false,
                'message' => 'Invalid Jenis Perintah'
            ];
        }

        // Validate tujuan based on jenis (this could be enhanced with database lookup)
        $validTujuanMap = [
            'ANTAR' => ['unit_baru_kontrak_baru', 'penambahan_unit_existing', 'unit_trial', 'unit_spare', 'unit_pengganti_sementara'],
            'TARIK' => ['selesai_kontrak', 'putus_kontrak', 'selesai_trial', 'pengambilan_unit_rusak', 'pengambilan_unit_tukar_guling'],
            'TUKAR' => ['ganti_spesifikasi', 'pengganti_unit_rusak', 'peremajaan_unit'],
            'RELOKASI' => ['pindah_lokasi_customer']
        ];

        if (!in_array($tujuanPerintah, $validTujuanMap[$jenisPerintah] ?? [])) {
            return [
                'valid' => false,
                'message' => 'Invalid Tujuan Perintah for selected Jenis Perintah'
            ];
        }

        return [
            'valid' => true,
            'message' => 'Workflow data is valid'
        ];
    }
}
