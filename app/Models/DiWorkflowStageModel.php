<?php

namespace App\Models;

use CodeIgniter\Model;

class DiWorkflowStageModel extends Model
{
    protected $table            = 'di_workflow_stages';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'di_id',
        'stage',
        'status',
        'completed_at',
        'completed_by',
        'notes',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = '';

    protected $validationRules      = [
        'di_id' => 'required|integer',
        'stage' => 'required|max_length[50]',
        'status' => 'required|in_list[PENDING,IN_PROGRESS,COMPLETED,CANCELLED]',
    ];
    protected $validationMessages   = [
        'di_id' => [
            'required' => 'Delivery Instruction ID harus diisi',
            'integer'  => 'Delivery Instruction ID harus berupa angka',
        ],
        'stage' => [
            'required'   => 'Stage harus diisi',
            'max_length' => 'Stage maksimal 50 karakter',
        ],
    ];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Get stages for a specific DI
     */
    public function getByDI(int $diId): array
    {
        return $this->where('di_id', $diId)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }

    /**
     * Get current stage for a DI
     */
    public function getCurrentStage(int $diId): ?array
    {
        return $this->where('di_id', $diId)
                    ->orderBy('created_at', 'DESC')
                    ->first();
    }
}
