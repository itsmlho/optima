<?php

namespace App\Models;

use CodeIgniter\Model;

class InventorySparepartModel extends Model
{
    protected $table            = 'inventory_spareparts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = ['sparepart_id', 'stok', 'lokasi_rak', 'created_at', 'updated_at'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Mengambil data untuk server-side datatable.
     */
    public function getDataTable($start, $length, $orderColumn, $orderDir, $searchValue)
    {
        $builder = $this->db->table('inventory_spareparts as inv')
            ->select('inv.id, s.kode, s.desc_sparepart, inv.stok, inv.lokasi_rak, inv.updated_at')
            ->join('sparepart as s', 's.id_sparepart = inv.sparepart_id');

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('s.kode', $searchValue)
                ->orLike('s.desc_sparepart', $searchValue)
                ->orLike('inv.lokasi_rak', $searchValue)
                ->groupEnd();
        }

        $builder->orderBy($orderColumn, $orderDir)->limit($length, $start);
        
        return $builder->get()->getResultArray();
    }

    /**
     * Menghitung semua data untuk pagination datatable.
     */
    public function countAllData()
    {
        return $this->db->table('inventory_spareparts')->countAllResults();
    }

    /**
     * Menghitung data yang terfilter untuk pagination datatable.
     */
    public function countFiltered($searchValue)
    {
        $builder = $this->db->table('inventory_spareparts as inv')
            ->join('sparepart as s', 's.id_sparepart = inv.sparepart_id');

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('s.kode', $searchValue)
                ->orLike('s.desc_sparepart', $searchValue)
                ->orLike('inv.lokasi_rak', $searchValue)
                ->groupEnd();
        }
        
        return $builder->countAllResults();
    }

    /**
     * Mengambil data statistik untuk kartu di halaman inventory.
     */
    public function getStats()
    {
        $total_jenis = $this->countAllResults(false);
        $total_stok = $this->selectSum('stok')->get()->getRow()->stok ?? 0;
        $stok_menipis = $this->where('stok > 0 AND stok <=', 10)->countAllResults(false);
        $stok_kosong = $this->where('stok', 0)->countAllResults(false);

        $this->resetQuery();

        return [
            'total_jenis' => $total_jenis,
            'total_stok' => $total_stok,
            'stok_menipis' => $stok_menipis,
            'stok_kosong' => $stok_kosong,
        ];
    }
}
