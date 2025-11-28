<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PurchasingModel;

class ApiController extends BaseController
{
    protected $formulirModel;
    protected $purchasingModel;

    public function __construct()
    {
        $this->formulirModel = new \App\Models\SystemManagementModel();
        $this->purchasingModel = new PurchasingModel();
    }

    /**
     * Get all unique merk/brands
     */
    public function getMerk()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request method']);
        }

        try {
            $merks = $this->formulirModel->getMerkUnit();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $merks,
                'message' => 'Merk retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve merk: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get models by selected merk
     */
    public function getModelsByMerk($merk = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request method']);
        }

        if (!$merk) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Merk parameter is required'
            ]);
        }

        try {
            // Decode URL encoded merk name
            $merk = urldecode($merk);
            
            $models = $this->formulirModel->getModelByMerk($merk);
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $models,
                'merk' => $merk,
                'message' => 'Models retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve models: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get all form data for AJAX requests
     */
    public function getFormData()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request method']);
        }

        try {
            $formData = $this->formulirModel->getAllFormData();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $formData,
                'message' => 'Form data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve form data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get specific dropdown data
     */
    public function getDropdownData($type = null)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON(['error' => 'Invalid request method']);
        }

        if (!$type) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Type parameter is required'
            ]);
        }

        try {
            $data = [];
            
            switch ($type) {
                case 'departemen':
                    $data = $this->formulirModel->getDepartemen();
                    break;
                case 'status_unit':
                    $data = $this->formulirModel->getStatusUnit();
                    break;
                case 'tipe_unit':
                    $data = $this->formulirModel->getTipeUnit();
                    break;
                case 'kapasitas':
                    $data = $this->formulirModel->getKapasitas();
                    break;
                case 'tipe_mast':
                    $data = $this->formulirModel->getTipeMast();
                    break;
                case 'mesin':
                    $data = $this->formulirModel->getMesin();
                    break;
                case 'attachment':
                    $data = $this->formulirModel->getAttachment();
                    break;
                case 'baterai':
                    $data = $this->formulirModel->getBaterai();
                    break;
                case 'charger':
                    $data = $this->formulirModel->getCharger();
                    break;
                case 'jenis_roda':
                    $data = $this->formulirModel->getJenisRoda();
                    break;
                case 'tipe_ban':
                    $data = $this->formulirModel->getTipeBan();
                    break;
                case 'valve':
                    $data = $this->formulirModel->getValve();
                    break;
                case 'status_aset':
                    $data = $this->formulirModel->getStatusAset();
                    break;
                case 'lokasi':
                    $data = $this->formulirModel->getLokasi();
                    break;
                default:
                    return $this->response->setStatusCode(400)->setJSON([
                        'success' => false,
                        'message' => 'Invalid dropdown type'
                    ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'type' => $type,
                'message' => ucfirst($type) . ' data retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Failed to retrieve ' . $type . ' data: ' . $e->getMessage()
            ]);
        }
    }
} 