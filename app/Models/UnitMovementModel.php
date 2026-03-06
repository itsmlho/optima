<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitMovementModel extends Model
{
    protected $table            = 'unit_movements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'movement_number',
        'unit_id',
        'component_id',
        'component_type',
        'origin_location',
        'destination_location',
        'origin_type',
        'destination_type',
        'movement_date',
        'driver_name',
        'vehicle_number',
        'notes',
        'surat_jalan_number',
        'status',
        'created_by_user_id',
        'confirmed_by_user_id',
        'confirmed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'movement_number'       => 'required|max_length[50]',
        'origin_location'      => 'required|max_length[100]',
        'destination_location' => 'required|max_length[100]',
        'origin_type'           => 'required|in_list[POS_1,POS_2,POS_3,POS_4,POS_5,CUSTOMER_SITE,WAREHOUSE,OTHER]',
        'destination_type'      => 'required|in_list[POS_1,POS_2,POS_3,POS_4,POS_5,CUSTOMER_SITE,WAREHOUSE,OTHER]',
        'movement_date'         => 'required|valid_date',
        'created_by_user_id'   => 'required|integer',
    ];

    /**
     * Generate unique movement number
     */
    public function generateMovementNumber()
    {
        $prefix = 'MV';
        $date = date('Ymd');
        $prefixWithDate = $prefix . $date;

        $lastRecord = $this->select('movement_number')
                           ->like('movement_number', $prefixWithDate, 'after')
                           ->orderBy('id', 'DESC')
                           ->first();

        if ($lastRecord) {
            $lastNumber = (int)substr($lastRecord['movement_number'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefixWithDate . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Surat Jalan Number
     */
    public function generateSuratJalanNumber()
    {
        $prefix = 'SJ';
        $date = date('Ym');
        $prefixWithDate = $prefix . $date;

        $lastRecord = $this->select('surat_jalan_number')
                           ->like('surat_jalan_number', $prefixWithDate, 'after')
                           ->where('surat_jalan_number IS NOT NULL')
                           ->orderBy('id', 'DESC')
                           ->first();

        if ($lastRecord) {
            $lastNumber = (int)substr($lastRecord['surat_jalan_number'], -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefixWithDate . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get movements with unit info
     */
    public function getWithUnitInfo($filters = [])
    {
        $builder = $this->db->table('unit_movements um');
        $builder->select('um.*,
            iu.no_unit,
            iu.no_unit_na,
            iu.serial_number,
            mu.merk_unit,
            mu.model_unit,
            tu.tipe as tipe_unit,
            CONCAT(creator.first_name, " ", COALESCE(creator.last_name, "")) as creator_name,
            CONCAT(confirmer.first_name, " ", COALESCE(confirmer.last_name, "")) as confirmer_name');
        $builder->join('inventory_unit iu', 'um.unit_id = iu.id_inventory_unit', 'left');
        $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
        $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
        $builder->join('users creator', 'um.created_by_user_id = creator.id', 'left');
        $builder->join('users confirmer', 'um.confirmed_by_user_id = confirmer.id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('um.status', $filters['status']);
        }

        if (!empty($filters['origin_type'])) {
            $builder->where('um.origin_type', $filters['origin_type']);
        }

        if (!empty($filters['destination_type'])) {
            $builder->where('um.destination_type', $filters['destination_type']);
        }

        if (!empty($filters['unit_id'])) {
            $builder->where('um.unit_id', $filters['unit_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('um.movement_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('um.movement_date <=', $filters['date_to']);
        }

        $builder->orderBy('um.movement_date', 'DESC');
        $builder->orderBy('um.id', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Get movements by unit
     */
    public function getByUnit($unitId)
    {
        return $this->getWithUnitInfo(['unit_id' => $unitId]);
    }

    /**
     * Get pending movements (in transit)
     */
    public function getInTransit()
    {
        return $this->getWithUnitInfo(['status' => 'IN_TRANSIT']);
    }

    /**
     * Confirm movement (arrived)
     */
    public function confirmArrival($id, $userId)
    {
        $movement = $this->find($id);
        if (!$movement) {
            throw new \Exception('Movement tidak ditemukan');
        }

        // Update movement status
        $this->update($id, [
            'status'              => 'ARRIVED',
            'confirmed_by_user_id' => $userId,
            'confirmed_at'       => date('Y-m-d H:i:s'),
        ]);

        // Update unit location if exists
        if ($movement['unit_id']) {
            $unitModel = new \App\Models\InventoryUnitModel();
            $unitModel->update($movement['unit_id'], [
                'lokasi_unit' => $movement['destination_location'],
            ]);
        }

        return true;
    }

    /**
     * Cancel movement
     */
    public function cancelMovement($id)
    {
        return $this->update($id, ['status' => 'CANCELLED']);
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        $total = $this->countAllResults();

        $draft = $this->where('status', 'DRAFT')->countAllResults();
        $inTransit = $this->where('status', 'IN_TRANSIT')->countAllResults();
        $arrived = $this->where('status', 'ARRIVED')->countAllResults();
        $cancelled = $this->where('status', 'CANCELLED')->countAllResults();

        return [
            'total'      => $total,
            'draft'      => $draft,
            'in_transit'=> $inTransit,
            'arrived'    => $arrived,
            'cancelled'  => $cancelled,
        ];
    }

    /**
     * Get location types for dropdown
     */
    public static function getLocationTypes()
    {
        return [
            'POS_1'         => 'POS 1 (Workshop Utama)',
            'POS_2'         => 'POS 2',
            'POS_3'         => 'POS 3',
            'POS_4'         => 'POS 4',
            'POS_5'         => 'POS 5',
            'CUSTOMER_SITE' => 'Lokasi Customer',
            'WAREHOUSE'     => 'Gudang',
            'OTHER'         => 'Lainnya',
        ];
    }

    /**
     * Get component types
     */
    public static function getComponentTypes()
    {
        return [
            'FORKLIFT'   => 'Forklift',
            'ATTACHMENT' => 'Attachment',
            'CHARGER'    => 'Charger',
            'BATTERY'    => 'Baterai',
        ];
    }
}
