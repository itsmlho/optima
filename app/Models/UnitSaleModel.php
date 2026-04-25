<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitSaleModel extends Model
{
    protected $table            = 'unit_sale_records';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'no_dokumen',
        'unit_id',
        'tanggal_jual',
        'nama_pembeli',
        'alamat_pembeli',
        'telepon_pembeli',
        'harga_jual',
        'metode_pembayaran',
        'no_kwitansi',
        'no_bast',
        'no_invoice',
        'status',
        'previous_status_unit_id',
        'keterangan',
        'sold_by_user_id',
        'cancelled_at',
        'cancelled_by_user_id',
        'cancelled_reason',
        'has_bundled_components',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'no_dokumen'        => 'required|max_length[50]',
        'unit_id'           => 'required|integer',
        'tanggal_jual'      => 'required|valid_date',
        'nama_pembeli'      => 'required|max_length[255]',
        'harga_jual'        => 'required|decimal',
        'metode_pembayaran' => 'required|in_list[CASH,TRANSFER,CEK,KREDIT]',
    ];

    protected $validationMessages = [
        'unit_id'      => ['required' => 'Unit harus dipilih'],
        'nama_pembeli' => ['required' => 'Nama pembeli harus diisi'],
        'harga_jual'   => ['required' => 'Harga jual harus diisi', 'decimal' => 'Harga jual harus berupa angka'],
    ];

    // ─────────────────────────────────────────────────────────
    // Auto-generate sale number: SALE-YYYY-NNNNN
    // ─────────────────────────────────────────────────────────
    public function generateSaleNumber(): string
    {
        $year   = date('Y');
        $prefix = 'SALE-' . $year . '-';

        // Query MAX from BOTH tables for unified sequential numbering
        $sql = "SELECT no_dokumen FROM (
            SELECT no_dokumen FROM unit_sale_records WHERE no_dokumen LIKE ?
            UNION ALL
            SELECT no_dokumen FROM component_sale_records WHERE no_dokumen LIKE ?
        ) AS combined ORDER BY no_dokumen DESC LIMIT 1";

        $last = $this->db->query($sql, [$prefix . '%', $prefix . '%'])->getRowArray();

        $nextNum = 1;
        if ($last) {
            $parts = explode('-', $last['no_dokumen']);
            $nextNum = ((int) end($parts)) + 1;
        }

        return $prefix . str_pad($nextNum, 5, '0', STR_PAD_LEFT);
    }

    // ─────────────────────────────────────────────────────────
    // Get sale records with full join info
    // ─────────────────────────────────────────────────────────
    public function getWithUnitInfo(array $filters = [])
    {
        $builder = $this->db->table('unit_sale_records usl')
            ->select('
                usl.*,
                iu.no_unit,
                iu.no_unit_na,
                iu.serial_number,
                mu.merk_unit,
                mu.model_unit,
                tu.tipe AS tipe_unit,
                su.status_unit AS status_unit_name,
                CONCAT(usr.first_name, \' \', COALESCE(usr.last_name, \'\')) AS seller_name,
                CONCAT(cu.first_name, \' \', COALESCE(cu.last_name, \'\')) AS canceller_name
            ', false)
            ->join('inventory_unit iu',  'iu.id_inventory_unit = usl.unit_id', 'left')
            ->join('model_unit mu',      'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu',       'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('status_unit su',     'su.id_status = usl.previous_status_unit_id', 'left')
            ->join('users usr',          'usr.id = usl.sold_by_user_id', 'left')
            ->join('users cu',           'cu.id = usl.cancelled_by_user_id', 'left');

        if (!empty($filters['status'])) {
            $builder->where('usl.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('usl.no_dokumen', $s)
                ->orLike('usl.nama_pembeli', $s)
                ->orLike('iu.no_unit', $s)
                ->orLike('iu.no_unit_na', $s)
                ->orLike('iu.serial_number', $s)
                ->orLike('mu.merk_unit', $s)
                ->orLike('mu.model_unit', $s)
                ->groupEnd();
        }
        if (!empty($filters['date_from'])) {
            $builder->where('usl.tanggal_jual >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('usl.tanggal_jual <=', $filters['date_to']);
        }

        return $builder
            ->orderBy('usl.tanggal_jual', 'DESC')
            ->orderBy('usl.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    // ─────────────────────────────────────────────────────────
    // Get single record with unit info
    // ─────────────────────────────────────────────────────────
    public function getDetailWithUnit(int $id): ?array
    {
        $row = $this->db->table('unit_sale_records usl')
            ->select('
                usl.*,
                iu.no_unit,
                iu.no_unit_na,
                iu.serial_number,
                iu.tahun_unit,
                iu.acquisition_cost,
                mu.merk_unit,
                mu.model_unit,
                tu.tipe AS tipe_unit,
                ku.kapasitas_unit AS kapasitas,
                su_prev.status_unit AS previous_status_name,
                su_curr.status_unit AS current_status_name,
                CONCAT(usr.first_name, \' \', COALESCE(usr.last_name, \'\')) AS seller_name,
                CONCAT(cu.first_name, \' \', COALESCE(cu.last_name, \'\')) AS canceller_name
            ', false)
            ->join('inventory_unit iu',       'iu.id_inventory_unit = usl.unit_id', 'left')
            ->join('model_unit mu',            'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu',             'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('kapasitas ku',             'ku.id_kapasitas = iu.kapasitas_unit_id', 'left')
            ->join('status_unit su_prev',      'su_prev.id_status = usl.previous_status_unit_id', 'left')
            ->join('status_unit su_curr',      'su_curr.id_status = iu.status_unit_id', 'left')
            ->join('users usr',                'usr.id = usl.sold_by_user_id', 'left')
            ->join('users cu',                 'cu.id = usl.cancelled_by_user_id', 'left')
            ->where('usl.id', $id)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }

    // ─────────────────────────────────────────────────────────
    // Stats for dashboard card
    // ─────────────────────────────────────────────────────────
    public function getStats(): array
    {
        $year  = date('Y');
        $month = date('Y-m');

        $total     = $this->where('status', 'COMPLETED')->countAllResults(false);
        $thisYear  = $this->where('status', 'COMPLETED')->where("YEAR(tanggal_jual)", $year)->countAllResults(false);
        $thisMonth = $this->where('status', 'COMPLETED')->where("DATE_FORMAT(tanggal_jual,'%Y-%m')", $month)->countAllResults(false);

        $revenueRow = $this->db->table($this->table)
            ->selectSum('harga_jual', 'total_revenue')
            ->where('status', 'COMPLETED')
            ->get()->getRowArray();

        return [
            'total'         => $total,
            'this_year'     => $thisYear,
            'this_month'    => $thisMonth,
            'total_revenue' => (float) ($revenueRow['total_revenue'] ?? 0),
        ];
    }

    // ─────────────────────────────────────────────────────────
    // Unified stats across both unit + component tables
    // ─────────────────────────────────────────────────────────
    public function getUnifiedStats(): array
    {
        $year  = date('Y');
        $month = date('Y-m');

        $sql = "SELECT
            COUNT(*)                                                   AS total,
            SUM(CASE WHEN YEAR(tanggal_jual) = ? THEN 1 ELSE 0 END)   AS this_year,
            SUM(CASE WHEN DATE_FORMAT(tanggal_jual,'%%Y-%%m') = ? THEN 1 ELSE 0 END) AS this_month,
            SUM(harga_jual)                                            AS total_revenue
        FROM (
            SELECT tanggal_jual, harga_jual FROM unit_sale_records      WHERE status = 'COMPLETED'
            UNION ALL
            SELECT tanggal_jual, harga_jual FROM component_sale_records WHERE status = 'COMPLETED' AND linked_unit_sale_id IS NULL
        ) AS combined";

        $row = $this->db->query($sql, [$year, $month])->getRowArray();

        return [
            'total'         => (int)   ($row['total'] ?? 0),
            'this_year'     => (int)   ($row['this_year'] ?? 0),
            'this_month'    => (int)   ($row['this_month'] ?? 0),
            'total_revenue' => (float) ($row['total_revenue'] ?? 0),
        ];
    }
}
