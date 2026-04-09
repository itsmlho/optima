<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationSpecificationHistoryModel extends Model
{
    protected $table            = 'quotation_specification_history';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'quotation_id',
        'specification_id',
        'action_type',
        'quotation_version',
        'summary',
        'old_snapshot',
        'new_snapshot',
        'changed_by',
        'changed_at',
    ];

    /**
     * Fields stored in JSON snapshots for audit (harga, qty, teknis).
     */
    public static function snapshotKeys(): array
    {
        return [
            'specification_name',
            'specification_type',
            'quantity',
            'spare_quantity',
            'is_spare_unit',
            'monthly_price',
            'daily_price',
            'total_price',
            'unit_accessories',
            'include_operator',
            'operator_quantity',
            'operator_monthly_rate',
            'operator_daily_rate',
            'departemen_id',
            'tipe_unit_id',
            'kapasitas_id',
            'brand_id',
            'battery_id',
            'charger_id',
            'attachment_id',
            'fork_id',
            'valve_id',
            'mast_id',
            'ban_id',
            'roda_id',
            'notes',
        ];
    }

    public static function snapshotFromRow(?array $row): ?array
    {
        if ($row === null || $row === []) {
            return null;
        }
        $out = [];
        foreach (self::snapshotKeys() as $k) {
            $out[$k] = $row[$k] ?? null;
        }

        return $out;
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getHistoryForQuotation(int $quotationId): array
    {
        $builder = $this->db->table($this->table . ' h');
        $builder->select('h.*, u.first_name, u.last_name, u.username');
        $builder->join('users u', 'u.id = h.changed_by', 'left');
        $builder->where('h.quotation_id', $quotationId);
        $builder->orderBy('h.changed_at', 'DESC');
        $builder->orderBy('h.id', 'DESC');

        return $builder->get()->getResultArray();
    }
}
