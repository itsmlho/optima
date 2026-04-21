<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationSpecTemplateModel extends Model
{
    protected $table            = 'quotation_spec_templates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields = [
        'template_name',
        'template_description',
        'specification_type',
        'departemen_id',
        'departemen_text',
        'tipe_unit_id',
        'tipe_unit_text',
        'kapasitas_id',
        'kapasitas_text',
        'brand_id',
        'merk_unit_text',
        'fork_id',
        'battery_id',
        'attachment_id',
        'charger_id',
        'mast_id',
        'ban_id',
        'roda_id',
        'valve_id',
        'unit_accessories',
        'notes',
        'default_monthly_price',
        'default_daily_price',
        'include_operator',
        'operator_quantity',
        'operator_monthly_rate',
        'operator_daily_rate',
        'created_by',
        'is_active',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'template_name' => 'required|max_length[100]',
    ];

    /**
     * Get all active templates with human-readable names via JOIN.
     * Returns lightweight list suitable for a dropdown or DataTable.
     */
    public function getTemplatesWithDetails(): array
    {
        return $this->db->table($this->table . ' t')
            ->select('
                t.id,
                t.template_name,
                t.template_description,
                t.specification_type,
                t.default_monthly_price,
                t.default_daily_price,
                t.include_operator,
                t.operator_quantity,
                t.created_at,
                t.updated_at,
                d.nama_departemen,
                tu.tipe   AS nama_tipe_unit,
                tu.jenis  AS jenis_tipe_unit,
                k.kapasitas_unit AS nama_kapasitas,
                mu.merk_unit,
                mu.model_unit
            ')
            ->join('departemen d',   'd.id_departemen = t.departemen_id',  'left')
            ->join('tipe_unit tu',   'tu.id_tipe_unit  = t.tipe_unit_id',  'left')
            ->join('kapasitas k',    'k.id_kapasitas   = t.kapasitas_id',  'left')
            ->join('model_unit mu',  'mu.id_model_unit = t.brand_id',      'left')
            ->where('t.is_active', 1)
            ->orderBy('t.template_name', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Get a single template with all fields needed to pre-fill the spec form.
     */
    public function getTemplateForForm(int $id): ?array
    {
        $row = $this->db->table($this->table . ' t')
            ->select('
                t.*,
                d.nama_departemen,
                COALESCE(t.departemen_text, d.nama_departemen) AS resolved_dept,
                tu.tipe   AS nama_tipe_unit,
                tu.jenis  AS jenis_tipe_unit,
                COALESCE(t.tipe_unit_text, tu.jenis) AS resolved_tipe,
                k.kapasitas_unit AS nama_kapasitas,
                COALESCE(t.kapasitas_text, k.kapasitas_unit) AS resolved_kapasitas,
                mu.merk_unit,
                mu.model_unit,
                COALESCE(t.merk_unit_text, mu.merk_unit) AS resolved_merk,
                b.jenis_baterai,
                c.merk_charger, c.tipe_charger,
                a.tipe AS attachment_tipe, a.merk AS attachment_merk,
                v.jumlah_valve AS valve_name,
                m.tipe_mast AS mast_name,
                tb.tipe_ban AS tire_name,
                jr.tipe_roda AS wheel_name
            ')
            ->join('departemen d',   'd.id_departemen  = t.departemen_id', 'left')
            ->join('tipe_unit tu',   'tu.id_tipe_unit  = t.tipe_unit_id',  'left')
            ->join('kapasitas k',    'k.id_kapasitas   = t.kapasitas_id',  'left')
            ->join('model_unit mu',  'mu.id_model_unit = t.brand_id',      'left')
            ->join('baterai b',      'b.id             = t.battery_id',    'left')
            ->join('charger c',      'c.id_charger     = t.charger_id',    'left')
            ->join('attachment a',   'a.id_attachment  = t.attachment_id', 'left')
            ->join('valve v',        'v.id_valve       = t.valve_id',      'left')
            ->join('tipe_mast m',    'm.id_mast        = t.mast_id',       'left')
            ->join('tipe_ban tb',    'tb.id_ban        = t.ban_id',        'left')
            ->join('jenis_roda jr',  'jr.id_roda       = t.roda_id',       'left')
            ->where('t.id', $id)
            ->where('t.is_active', 1)
            ->get()
            ->getRowArray();

        return $row ?: null;
    }
}
