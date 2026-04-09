<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Customer Contract Model
 * 
 * WARNING: This model references 'customer_contracts' junction table which may not exist.
 * Current implementation uses direct customer_id foreign key in 'kontrak' table.
 * 
 * This model is called in:
 * - CustomerManagementController::show() - line 254
 * - CustomerModel::getCustomerWithDetails() - line 201
 * 
 * Consider refactoring to use direct kontrak table queries or ensure
 * customer_contracts junction table exists and is populated.
 * 
 * @package App\Models
 */
class CustomerContractModel extends Model
{
    protected $table = 'customer_contracts';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'customer_id',
        'kontrak_id',
        'is_active'
    ];
    
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    
    protected $validationRules = [
        'customer_id' => 'required|integer|is_not_unique[customers.id]',
        'kontrak_id' => 'required|integer|is_not_unique[kontrak.id]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'customer_id' => [
            'required' => 'Customer harus dipilih',
            'integer' => 'Customer harus berupa angka',
            'is_not_unique' => 'Customer tidak valid'
        ],
        'kontrak_id' => [
            'required' => 'Kontrak harus dipilih',
            'integer' => 'Kontrak harus berupa angka',
            'is_not_unique' => 'Kontrak tidak valid'
        ]
    ];
    
    protected $skipValidation = false;
    protected $cleanValidationRules = true;
    
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Get contracts by customer
     */
    public function getContractsByCustomer($customerId)
    {
        return $this->select('customer_contracts.*, kontrak.*')
                   ->join('kontrak', 'kontrak.id = customer_contracts.kontrak_id')
                   ->where('customer_contracts.customer_id', $customerId)
                   ->orderBy('kontrak.tanggal_mulai', 'DESC')
                   ->findAll();
    }
    
    /**
     * Get active contracts by customer
     */
    public function getActiveContractsByCustomer($customerId)
    {
        return $this->select('customer_contracts.*, kontrak.*')
                   ->join('kontrak', 'kontrak.id = customer_contracts.kontrak_id')
                   ->where('customer_contracts.customer_id', $customerId)
                   ->where('customer_contracts.is_active', 1)
                   ->where('kontrak.status', 'ACTIVE')
                   ->orderBy('kontrak.tanggal_mulai', 'DESC')
                   ->findAll();
    }
    
    /**
     * Get customers by contract
     */
    public function getCustomersByContract($kontrakId)
    {
        return $this->select('customer_contracts.*, customers.*')
                   ->join('customers', 'customers.id = customer_contracts.customer_id')
                   ->where('customer_contracts.kontrak_id', $kontrakId)
                   ->findAll();
    }
    
    /**
     * Link customer to contract
     */
    public function linkCustomerContract($customerId, $kontrakId, $isActive = 1)
    {
        $data = [
            'customer_id' => $customerId,
            'kontrak_id' => $kontrakId,
            'is_active' => $isActive
        ];
        
        // Check if link already exists
        $existing = $this->where('customer_id', $customerId)
                        ->where('kontrak_id', $kontrakId)
                        ->first();
                        
        if ($existing) {
            // Update existing link
            return $this->update($existing['id'], ['is_active' => $isActive]);
        } else {
            // Create new link
            return $this->save($data);
        }
    }
    
    /**
     * Unlink customer from contract
     */
    public function unlinkCustomerContract($customerId, $kontrakId)
    {
        return $this->where('customer_id', $customerId)
                   ->where('kontrak_id', $kontrakId)
                   ->set('is_active', 0)
                   ->update();
    }
    
    /**
     * Get contract-customer relationships with full info
     */
    public function getContractCustomerRelations($customerId = null, $kontrakId = null, $activeOnly = false)
    {
        $builder = $this->select('
                        customer_contracts.*,
                        customers.customer_name, customers.customer_code,
                        kontrak.no_kontrak, kontrak.pelanggan, kontrak.jenis_sewa, 
                        kontrak.status, kontrak.tanggal_mulai, kontrak.tanggal_berakhir
                    ')
                       ->join('customers', 'customers.id = customer_contracts.customer_id')
                       ->join('kontrak', 'kontrak.id = customer_contracts.kontrak_id');
                       
        if ($customerId) {
            $builder->where('customer_contracts.customer_id', $customerId);
        }
        
        if ($kontrakId) {
            $builder->where('customer_contracts.kontrak_id', $kontrakId);
        }
        
        if ($activeOnly) {
            $builder->where('customer_contracts.is_active', 1);
        }
        
        return $builder->orderBy('kontrak.tanggal_mulai', 'DESC')
                      ->findAll();
    }
}