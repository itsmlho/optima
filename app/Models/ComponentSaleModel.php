<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * ComponentSaleModel — handles component_sale_records table
 * Tracks sales of Attachments, Chargers, Batteries, and Sparepart records.
 *
 * Model penjualan komponen: Attachment, Charger, Baterai, dan Sparepart.
 */
class ComponentSaleModel extends Model
{
    protected $table            = 'component_sale_records';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'no_dokumen',
        'asset_type',
        'asset_id',
        'linked_unit_sale_id',
        'tanggal_jual',
        'nama_pembeli',
        'alamat_pembeli',
        'telepon_pembeli',
        'harga_jual',
        'metode_pembayaran',
        'no_kwitansi',
        'status',
        'previous_status',
        'previous_unit_id',
        'keterangan',
        'sold_by_user_id',
        'cancelled_at',
        'cancelled_by_user_id',
        'cancelled_reason',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'no_dokumen'        => 'required|max_length[50]',
        'asset_type'        => 'required|in_list[ATTACHMENT,CHARGER,BATTERY,SPAREPART]',
        'asset_id'          => 'required|integer',
        'tanggal_jual'      => 'required|valid_date',
        'nama_pembeli'      => 'required|max_length[255]',
        'harga_jual'        => 'required|decimal',
        'metode_pembayaran' => 'required|in_list[CASH,TRANSFER,CEK,KREDIT]',
    ];

    // ─────────────────────────────────────────────────────────
    // Get records with asset detail via LEFT JOINs
    // ─────────────────────────────────────────────────────────
    public function getWithAssetInfo(array $filters = []): array
    {
        $builder = $this->db->table('component_sale_records csr')
            ->select('
                csr.*,
                ia.item_number  AS att_item_number,
                at.tipe         AS att_type,
                at.merk         AS att_brand,
                ia.serial_number AS att_serial,

                ic.item_number  AS chr_item_number,
                ct.merk_charger AS chr_brand,
                ct.tipe_charger AS chr_type,
                ic.serial_number AS chr_serial,

                ib.item_number  AS bat_item_number,
                bt.merk_baterai AS bat_brand,
                bt.tipe_baterai AS bat_type,
                bt.jenis_baterai AS bat_spec,
                ib.serial_number AS bat_serial,

                sp.kode         AS sp_code,
                sp.desc_sparepart AS sp_desc,

                CONCAT(usr.first_name, \' \', COALESCE(usr.last_name, \'\')) AS seller_name,
                CONCAT(cu.first_name, \' \', COALESCE(cu.last_name, \'\'))  AS canceller_name
            ', false)
            // Attachment joins
            ->join('inventory_attachments ia', 'ia.id = csr.asset_id AND csr.asset_type = "ATTACHMENT"', 'left')
            ->join('attachment at',            'at.id_attachment = ia.attachment_type_id', 'left')
            // Charger joins
            ->join('inventory_chargers ic',    'ic.id = csr.asset_id AND csr.asset_type = "CHARGER"', 'left')
            ->join('charger ct',               'ct.id_charger = ic.charger_type_id', 'left')
            // Battery joins
            ->join('inventory_batteries ib',   'ib.id = csr.asset_id AND csr.asset_type = "BATTERY"', 'left')
            ->join('baterai bt',               'bt.id = ib.battery_type_id', 'left')
            // Sparepart join
            ->join('sparepart sp',             'sp.id_sparepart = csr.asset_id AND csr.asset_type = "SPAREPART"', 'left')
            // User joins
            ->join('users usr', 'usr.id = csr.sold_by_user_id', 'left')
            ->join('users cu',  'cu.id = csr.cancelled_by_user_id', 'left');

        // Filters
        if (!empty($filters['status'])) {
            $builder->where('csr.status', $filters['status']);
        }
        if (!empty($filters['asset_type'])) {
            $builder->where('csr.asset_type', $filters['asset_type']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('csr.no_dokumen', $s)
                ->orLike('csr.nama_pembeli', $s)
                ->orLike('ia.item_number', $s)
                ->orLike('ic.item_number', $s)
                ->orLike('ib.item_number', $s)
                ->orLike('sp.kode', $s)
                ->orLike('sp.desc_sparepart', $s)
                ->groupEnd();
        }
        if (!empty($filters['date_from'])) {
            $builder->where('csr.tanggal_jual >=', $filters['date_from']);
        }
        if (!empty($filters['date_to'])) {
            $builder->where('csr.tanggal_jual <=', $filters['date_to']);
        }

        return $builder
            ->orderBy('csr.tanggal_jual', 'DESC')
            ->orderBy('csr.id', 'DESC')
            ->get()
            ->getResultArray();
    }

    // ─────────────────────────────────────────────────────────
    // Single record with full asset info
    // ─────────────────────────────────────────────────────────
    public function getDetailWithAsset(int $id): ?array
    {
        $row = $this->db->table('component_sale_records csr')
            ->select('
                csr.*,
                ia.item_number   AS att_item_number,
                ia.serial_number AS att_serial,
                ia.max_capacity  AS att_capacity,
                at.tipe          AS att_type,
                at.merk          AS att_brand,
                at.model         AS att_model,

                ic.item_number   AS chr_item_number,
                ic.serial_number AS chr_serial,
                ic.input_voltage AS chr_input,
                ic.output_voltage AS chr_output,
                ic.output_ampere AS chr_ampere,
                ct.merk_charger  AS chr_brand,
                ct.tipe_charger  AS chr_type,

                ib.item_number   AS bat_item_number,
                ib.serial_number AS bat_serial,
                ib.voltage       AS bat_voltage,
                ib.ampere_hour   AS bat_ah,
                bt.merk_baterai  AS bat_brand,
                bt.tipe_baterai  AS bat_type,
                bt.jenis_baterai AS bat_spec,

                sp.kode          AS sp_code,
                sp.desc_sparepart AS sp_desc,

                CONCAT(usr.first_name, \' \', COALESCE(usr.last_name, \'\')) AS seller_name,
                CONCAT(cu.first_name, \' \', COALESCE(cu.last_name, \'\'))  AS canceller_name
            ', false)
            ->join('inventory_attachments ia', 'ia.id = csr.asset_id AND csr.asset_type = "ATTACHMENT"', 'left')
            ->join('attachment at',            'at.id_attachment = ia.attachment_type_id', 'left')
            ->join('inventory_chargers ic',    'ic.id = csr.asset_id AND csr.asset_type = "CHARGER"', 'left')
            ->join('charger ct',               'ct.id_charger = ic.charger_type_id', 'left')
            ->join('inventory_batteries ib',   'ib.id = csr.asset_id AND csr.asset_type = "BATTERY"', 'left')
            ->join('baterai bt',               'bt.id = ib.battery_type_id', 'left')
            ->join('sparepart sp',             'sp.id_sparepart = csr.asset_id AND csr.asset_type = "SPAREPART"', 'left')
            ->join('users usr', 'usr.id = csr.sold_by_user_id', 'left')
            ->join('users cu',  'cu.id = csr.cancelled_by_user_id', 'left')
            ->where('csr.id', $id)
            ->get()
            ->getRowArray();

        if (!$row) {
            return null;
        }

        // Resolve generic serial_number and item_number from type-prefixed keys
        $prefixMap = [
            'ATTACHMENT' => ['item' => 'att_item_number', 'serial' => 'att_serial'],
            'CHARGER'    => ['item' => 'chr_item_number', 'serial' => 'chr_serial'],
            'BATTERY'    => ['item' => 'bat_item_number', 'serial' => 'bat_serial'],
        ];
        $prefix = $prefixMap[$row['asset_type']] ?? null;
        $row['item_number']    = $prefix ? ($row[$prefix['item']] ?? null) : null;
        $row['serial_number']  = $prefix ? ($row[$prefix['serial']] ?? null) : null;

        return $row;
    }

    // ─────────────────────────────────────────────────────────
    // Get bundled components for a unit sale
    // ─────────────────────────────────────────────────────────
    public function getBundledComponents(int $unitSaleId): array
    {
        return $this->db->table('component_sale_records csr')
            ->select('csr.*, 
                ia.item_number AS att_item_number, at.tipe AS att_type, at.merk AS att_brand, ia.serial_number AS att_serial,
                ic.item_number AS chr_item_number, ct.merk_charger AS chr_brand, ct.tipe_charger AS chr_type, ic.serial_number AS chr_serial,
                ib.item_number AS bat_item_number, bt.merk_baterai AS bat_brand, bt.tipe_baterai AS bat_type, bt.jenis_baterai AS bat_spec, ib.serial_number AS bat_serial,
                sp.kode AS sp_code, sp.desc_sparepart AS sp_desc', false)
            ->join('inventory_attachments ia', 'ia.id = csr.asset_id AND csr.asset_type = "ATTACHMENT"', 'left')
            ->join('attachment at',            'at.id_attachment = ia.attachment_type_id', 'left')
            ->join('inventory_chargers ic',    'ic.id = csr.asset_id AND csr.asset_type = "CHARGER"', 'left')
            ->join('charger ct',               'ct.id_charger = ic.charger_type_id', 'left')
            ->join('inventory_batteries ib',   'ib.id = csr.asset_id AND csr.asset_type = "BATTERY"', 'left')
            ->join('baterai bt',               'bt.id = ib.battery_type_id', 'left')
            ->join('sparepart sp',             'sp.id_sparepart = csr.asset_id AND csr.asset_type = "SPAREPART"', 'left')
            ->where('csr.linked_unit_sale_id', $unitSaleId)
            ->get()
            ->getResultArray();
    }

    // ─────────────────────────────────────────────────────────
    // Component-only stats
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

    /**
     * Build human-readable asset label from a record row
     */
    public static function getAssetLabel(array $row): string
    {
        switch ($row['asset_type']) {
            case 'ATTACHMENT':
                $num = $row['att_item_number'] ?? '';
                $desc = trim(($row['att_brand'] ?? '') . ' ' . ($row['att_type'] ?? ''));
                return $num . ($desc ? ' — ' . $desc : '');
            case 'CHARGER':
                $num = $row['chr_item_number'] ?? '';
                $desc = trim(($row['chr_brand'] ?? '') . ' ' . ($row['chr_type'] ?? ''));
                return $num . ($desc ? ' — ' . $desc : '');
            case 'BATTERY':
                $num = $row['bat_item_number'] ?? '';
                $desc = trim(($row['bat_brand'] ?? '') . ' ' . ($row['bat_type'] ?? '') . ' ' . ($row['bat_spec'] ?? ''));
                return $num . ($desc ? ' — ' . $desc : '');
            case 'SPAREPART':
                return ($row['sp_code'] ?? '') . ' — ' . ($row['sp_desc'] ?? '');
            default:
                return 'Unknown';
        }
    }
}
