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

    public function __construct()
    {
        $this->deliveryInstructionModel = new \App\Models\DeliveryInstructionModel();
        $this->spkModel = new \App\Models\SpkModel();
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
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.'
            ]);
        }
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
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.'
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
                'message' => 'Terjadi kesalahan pada database. Silakan coba lagi.'
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

            // Update DI with SPK ID (if DI table exists)
            if (method_exists($this->deliveryInstructionModel, 'update')) {
                $this->deliveryInstructionModel->update($diId, [
                    'spk_id' => $spkId,
                    'status_eksekusi' => 'Persiapan Unit'
                ]);
            }

            // Log the auto-generation
            log_message('info', "Auto-generated SPK #{$spkNumber} (ID: {$spkId}) from DI #{$diId}");

            return [
                'success' => true,
                'message' => 'SPK auto-generated successfully',
                'spk_id' => $spkId,
                'spk_number' => $spkNumber
            ];

        } catch (\Exception $e) {
            log_message('error', 'Gagal memproses permintaan. Silakan coba lagi.');
            return [
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ];
        }
    }

    /**
     * Generate unique SPK number
     */
    private function generateSpkNumber()
    {
        $db = \Config\Database::connect();
        
        // Format: SPK/YYYY/MM/NNNN
        $year = date('Y');
        $month = date('m');
        
        // Get the next sequence number for this month
        $query = $db->query("
            SELECT COUNT(*) as count 
            FROM spk 
            WHERE nomor_spk LIKE ? 
        ", ["SPK/{$year}/{$month}/%"]);
        
        $result = $query->getRow();
        $sequence = ($result->count ?? 0) + 1;
        
        return sprintf('SPK/%s/%s/%04d', $year, $month, $sequence);
    }

    /**
     * Validate workflow data
     */
    public function validateWorkflowData()
    {
        $data = $this->request->getJSON(true);
        
        $validation = \Config\Services::validation();
        
        $validation->setRules([
            'jenis_perintah' => 'required|in_list[ANTAR,TARIK,TUKAR,RELOKASI]',
            'tujuan_perintah' => 'required',
            'status_eksekusi' => 'permit_empty|numeric'
        ]);

        if (!$validation->run($data)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $validation->getErrors()
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Workflow data is valid'
        ]);
    }

    /**
     * Update workflow status
     */
    public function updateWorkflowStatus()
    {
        $data = $this->request->getJSON(true);
        $spkId = $data['spk_id'] ?? null;
        $newStatusId = $data['status_eksekusi_workflow_id'] ?? null;
        
        if (!$spkId || !$newStatusId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'SPK ID and status required'
            ]);
        }

        try {
            $updateData = [
                'status_eksekusi_workflow_id' => $newStatusId,
                'workflow_updated_at' => date('Y-m-d H:i:s')
            ];
            
            if (isset($data['workflow_notes'])) {
                $updateData['workflow_notes'] = $data['workflow_notes'];
            }

            $result = $this->spkModel->update($spkId, $updateData);

            if ($result) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Workflow status updated successfully'
                ]);
            } else {
                throw new \Exception('Gagal memproses permintaan. Silakan coba lagi.');
            }
    }
}
