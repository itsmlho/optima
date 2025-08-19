<?php

namespace App\Controllers;

use App\Models\PurchasingModel;

class Api extends BaseController
{
    protected $purchasingModel;

    public function __construct()
    {
        $this->purchasingModel = new PurchasingModel();
    }

    /**
     * Get models by brand/merk
     */
    public function getModelsByMerk($merk)
    {
        $models = $this->purchasingModel->getModelsByMerk($merk);
        return $this->response->setJSON($models);
    }
} 