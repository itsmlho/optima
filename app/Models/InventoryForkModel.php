<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryForkModel extends Model
{
    protected $table = 'inventory_forks';
    protected $primaryKey = 'id';
    protected $returnType = 'array';
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $allowedFields = [
        'item_number',
        'fork_id',
        'fork_stock_id',
        'inventory_unit_id',
        'qty_pairs',
        'physical_condition',
        'status',
        'storage_location',
        'assigned_at',
        'detached_at',
        'received_at',
        'notes',
    ];

    public function getDataTable($request): array
    {
        $builder = $this->db->table($this->table . ' f')
            ->select("
                f.*,
                f.id as id_inventory_attachment,
                'fork' as tipe_item,
                f.item_number as no_item,
                f.item_number as sn_fork,
                CONCAT(COALESCE(k.name, ''), ' / ', COALESCE(k.length_mm, '-'), 'mm') as fork_name,
                k.name as fork_spec_name,
                k.length_mm,
                k.width_mm,
                k.thickness_mm,
                k.fork_class,
                k.capacity_kg,
                COALESCE(iu.no_unit, iu.no_unit_na) as no_unit
            ", false)
            ->join('fork k', 'k.id = f.fork_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = f.inventory_unit_id', 'left');

        if (!empty($request['status_filter']) && $request['status_filter'] !== 'all') {
            $builder->where('f.status', $request['status_filter']);
        }

        if (!empty($request['search']['value'])) {
            $search = $request['search']['value'];
            $builder->groupStart()
                ->like('f.item_number', $search)
                ->orLike('k.name', $search)
                ->orLike('k.fork_class', $search)
                ->orLike('f.storage_location', $search)
                ->orLike('f.status', $search)
                ->orLike('iu.no_unit', $search)
                ->groupEnd();
        }

        $totalFiltered = $builder->countAllResults(false);

        if (!empty($request['order'])) {
            $orderMap = [
                0 => 'f.id',
                1 => 'f.item_number',
                2 => 'k.name',
                3 => 'k.fork_class',
                4 => 'f.qty_pairs',
                5 => 'f.physical_condition',
                6 => 'f.status',
                7 => 'f.storage_location',
            ];
            $idx = (int)($request['order'][0]['column'] ?? 0);
            $dir = $request['order'][0]['dir'] ?? 'desc';
            $builder->orderBy($orderMap[$idx] ?? 'f.id', $dir);
        } else {
            $builder->orderBy('f.id', 'DESC');
        }

        if (isset($request['length']) && (int)$request['length'] !== -1) {
            $builder->limit((int)$request['length'], (int)($request['start'] ?? 0));
        }

        return [
            'data' => $builder->get()->getResultArray(),
            'recordsFiltered' => $totalFiltered,
        ];
    }

    public function getStats(): array
    {
        $row = $this->db->query("
            SELECT
                COUNT(*) as total,
                SUM(status = 'AVAILABLE') as available,
                SUM(status = 'IN_USE') as in_use,
                SUM(status = 'SPARE') as spare,
                SUM(status = 'MAINTENANCE') as maintenance,
                SUM(status = 'BROKEN') as broken
            FROM inventory_forks
        ")->getRowArray();

        return $row ?: [
            'total' => 0,
            'available' => 0,
            'in_use' => 0,
            'spare' => 0,
            'maintenance' => 0,
            'broken' => 0,
        ];
    }

    public function getForkDetail($id): ?array
    {
        return $this->db->table($this->table . ' f')
            ->select("
                f.*,
                k.name as fork_spec_name,
                k.length_mm,
                k.width_mm,
                k.thickness_mm,
                k.fork_class,
                k.capacity_kg,
                COALESCE(iu.no_unit, iu.no_unit_na) as no_unit,
                iu.serial_number as unit_serial_number,
                fs.item_number as stock_item_number
            ", false)
            ->join('fork k', 'k.id = f.fork_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = f.inventory_unit_id', 'left')
            ->join('inventory_fork_stocks fs', 'fs.id = f.fork_stock_id', 'left')
            ->where('f.id', $id)
            ->get()
            ->getRowArray();
    }
}

