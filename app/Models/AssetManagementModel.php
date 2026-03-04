<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * @deprecated This model references non-existent 'forklifts' table
 * @see InventoryUnitModel - Use this instead for unit management
 * 
 * FIXME: Table 'forklifts' does not exist in database
 * Should use 'inventory_unit' table with primary key 'id_inventory_unit'
 * Currently NOT IN USE - instantiated in Dashboard.php but never called
 * 
 * Action Required: Remove from Dashboard.php or refactor to use InventoryUnitModel
 */
class AssetManagementModel extends Model
{
    protected $table            = 'inventory_unit';
    protected $primaryKey       = 'id_inventory_unit';
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
        // Validation disabled — this model is deprecated.
        // Use InventoryUnitModel for all write operations.
    ];

    protected $validationMessages = [];

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

        return $builder->orderBy('unit_code', 'ASC')->get()->getResultArray();
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