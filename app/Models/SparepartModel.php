<?php

namespace App\Models;

use CodeIgniter\Model;

class SparepartModel extends Model
{
    protected $table            = 'sparepart';
    protected $primaryKey       = 'id_sparepart';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['kode', 'desc_sparepart'];
    protected $useTimestamps    = true;

    /**
     * Get active spareparts for dropdown
     */
    public function getActiveSpareparts()
    {
        return $this->select('id_sparepart as id, CONCAT(kode, " - ", desc_sparepart) as text')
                    ->orderBy('desc_sparepart', 'ASC')
                    ->findAll();
    }

    /**
     * Server-side DataTable data for master data page
     */
    public function getDataTable(int $start, int $length, string $orderColumn, string $orderDir, string $search): array
    {
        $builder = $this->db->table('sparepart');
        $builder->select('id_sparepart, kode, desc_sparepart, created_at, updated_at');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('kode', $search)
                ->orLike('desc_sparepart', $search)
                ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)->limit($length, $start);

        return $builder->get()->getResultArray();
    }

    /**
     * Count filtered results for DataTable
     */
    public function countFiltered(string $search): int
    {
        $builder = $this->db->table('sparepart');

        if (!empty($search)) {
            $builder->groupStart()
                ->like('kode', $search)
                ->orLike('desc_sparepart', $search)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Count all records for DataTable
     */
    public function countAllData(): int
    {
        return $this->db->table('sparepart')->countAllResults();
    }

    /**
     * Check if kode already exists (for uniqueness validation)
     */
    public function kodeExists(string $kode, ?int $excludeId = null): bool
    {
        $builder = $this->db->table('sparepart')->where('kode', $kode);

        if ($excludeId !== null) {
            $builder->where('id_sparepart !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }
}