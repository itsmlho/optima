<?php

namespace App\Models;

use CodeIgniter\Model;

class AssetManagementModel extends Model
{
    protected $table            = 'forklifts';
    protected $primaryKey       = 'forklift_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'unit_code', 'unit_name', 'brand', 'model', 'type', 'capacity',
        'fuel_type', 'engine_power', 'lift_height', 'year_manufactured',
        'serial_number', 'purchase_date', 'purchase_price', 'current_value',
        'supplier', 'warranty_expiry', 'insurance_expiry', 'service_interval_hours',
        'total_operating_hours', 'status', 'condition', 'location',
        'availability', 'rental_rate_daily', 'rental_rate_weekly',
        'rental_rate_monthly', 'notes', 'created_at', 'updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'unit_code'         => 'required|max_length[20]|is_unique[forklifts.unit_code,forklift_id,{forklift_id}]',
        'unit_name'         => 'required|max_length[255]',
        'brand'             => 'required|max_length[100]',
        'model'             => 'required|max_length[100]',
        'type'              => 'required|in_list[electric,diesel,gas,hybrid]',
        'capacity'          => 'required|numeric|greater_than[0]',
        'fuel_type'         => 'required|in_list[electric,diesel,petrol,gas,hybrid]',
        'status'            => 'required|in_list[available,rented,maintenance,retired,reserved]',
        'condition'         => 'required|in_list[excellent,good,fair,poor,damaged]',
        'availability'      => 'required|in_list[available,unavailable,reserved]',
        'engine_power'      => 'permit_empty|numeric',
        'lift_height'       => 'permit_empty|numeric',
        'year_manufactured' => 'permit_empty|numeric|greater_than[1900]',
        'serial_number'     => 'permit_empty|max_length[100]',
        'purchase_date'     => 'permit_empty|valid_date',
        'purchase_price'    => 'permit_empty|numeric|greater_than[0]',
        'current_value'     => 'permit_empty|numeric|greater_than[0]',
        'supplier'          => 'permit_empty|max_length[255]',
        'warranty_expiry'   => 'permit_empty|valid_date',
        'insurance_expiry'  => 'permit_empty|valid_date',
        'location'          => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'unit_code' => [
            'is_unique' => 'Unit code sudah ada dalam sistem.'
        ]
    ];

    // --- Metode Kustom ---

    public function getAllAssets($filters = [])
    {
        $builder = $this->builder();
        
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('unit_code', $filters['search'])
                ->orLike('unit_name', $filters['search'])
                ->orLike('brand', $filters['search'])
                ->orLike('model', $filters['search'])
                ->groupEnd();
        }
        if (!empty($filters['status'])) {
            $builder->where('status', $filters['status']);
        }
        if (!empty($filters['condition'])) {
            $builder->where('condition', $filters['condition']);
        }

        return $builder->orderBy('unit_code', 'ASC')->findAll();
    }

    public function getAssetStats()
    {
        return [
            'total_assets'      => $this->countAllResults(false),
            'available_assets'  => $this->where('status', 'available')->countAllResults(false),
            'rented_assets'     => $this->where('status', 'rented')->countAllResults(false),
            'maintenance_assets' => $this->where('status', 'maintenance')->countAllResults(false),
            'retired_assets'    => $this->where('status', 'retired')->countAllResults(),
        ];
    }
}