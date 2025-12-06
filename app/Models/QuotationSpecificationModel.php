<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationSpecificationModel extends Model
{
    protected $table = 'quotation_specifications';
    protected $primaryKey = 'id_specification';
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = [
        'id_quotation',
        'specification_name',
        'specification_type',
        'quantity',
        'monthly_price',
        'daily_price',
        'total_price',
        'brand_id',
        'battery_id',
        'attachment_id',
        'unit_accessories',
        'is_active',
        'departemen_id',
        'tipe_unit_id',
        'kapasitas_id',
        'charger_id',
        'mast_id',
        'ban_id',
        'roda_id',
        'valve_id',
        'original_kontrak_id'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all specifications for a quotation with related data
     * Using consistent pattern like KontrakSpesifikasiModel
     */
    public function getQuotationSpecifications($quotationId)
    {
        $builder = $this->db->table($this->table . ' qs');
        $builder->select('
            qs.*,
            d.nama_departemen,
            tu.tipe as nama_tipe_unit,
            tu.jenis as jenis_tipe_unit,
            k.kapasitas_unit as nama_kapasitas,
            mu.merk_unit,
            mu.model_unit,
            b.jenis_baterai,
            c.merk_charger,
            c.tipe_charger,
            a.tipe as attachment_tipe,
            a.merk as attachment_merk,
            v.jumlah_valve as valve_name,
            m.tipe_mast as mast_name,
            tb.tipe_ban as tire_name,
            jr.tipe_roda as wheel_name
        ');
        $builder->join('departemen d', 'qs.departemen_id = d.id_departemen', 'left');
        $builder->join('tipe_unit tu', 'qs.tipe_unit_id = tu.id_tipe_unit', 'left');
        $builder->join('kapasitas k', 'qs.kapasitas_id = k.id_kapasitas', 'left');
        $builder->join('model_unit mu', 'qs.brand_id = mu.id_model_unit', 'left');
        $builder->join('baterai b', 'qs.battery_id = b.id', 'left');
        $builder->join('charger c', 'qs.charger_id = c.id_charger', 'left');
        $builder->join('attachment a', 'qs.attachment_id = a.id_attachment', 'left');
        $builder->join('valve v', 'qs.valve_id = v.id_valve', 'left');
        $builder->join('tipe_mast m', 'qs.mast_id = m.id_mast', 'left');
        $builder->join('tipe_ban tb', 'qs.ban_id = tb.id_ban', 'left');
        $builder->join('jenis_roda jr', 'qs.roda_id = jr.id_roda', 'left');
        $builder->where('qs.id_quotation', $quotationId);
        $builder->where('qs.is_active', 1);
        $builder->orderBy('qs.id_specification, qs.created_at');
        
        return $builder->get()->getResultArray();
    }

    /**
     * Get specifications by quotation ID - alias method for compatibility
     */
    public function getByQuotationId($quotationId)
    {
        return $this->getQuotationSpecifications($quotationId);
    }

    /**
     * Get specifications summary for a quotation (alias for getQuotationSummary)
     */
    public function getSpecificationsSummary($quotationId)
    {
        return $this->getQuotationSummary($quotationId);
    }

    /**
     * Get next specification number for a quotation
     */
    public function getNextSpecificationNumber($quotationId)
    {
        $lastSpec = $this->where('id_quotation', $quotationId)
                         ->orderBy('id_specification', 'DESC')
                         ->first();
        
        if (!$lastSpec) {
            return 1;
        }

        // Extract number from specification code
        $lastCode = $lastSpec['spek_kode'] ?? '';
        if (preg_match('/QS-\d+-(\d+)/', $lastCode, $matches)) {
            return (int)$matches[1] + 1;
        }

        // Fallback: count existing specifications + 1
        $count = $this->where('id_quotation', $quotationId)->countAllResults();
        return $count + 1;
    }

    /**
     * Generate next spec code for a quotation (like KontrakSpesifikasiModel)
     */
    public function generateNextSpekKode($quotationId)
    {
        $number = $this->getNextSpecificationNumber($quotationId);
        return sprintf('QS-%03d-%03d', $quotationId, $number);
    }

    /**
     * Get quotation summary with totals
     */
    public function getQuotationSummary($quotationId)
    {
        $result = $this->select('
            COUNT(*) as total_specifications,
            SUM(quantity) as total_quantity,
            SUM(total_price) as total_value
        ')
        ->where('id_quotation', $quotationId)
        ->where('is_active', 1)
        ->first();

        return $result ?: [
            'total_specifications' => 0,
            'total_quantity' => 0,
            'total_value' => 0,
            'optional_count' => 0,
            'delivery_count' => 0,
            'installation_count' => 0
        ];
    }

    /**
     * Get specification detail with all related data
     */
    public function getSpecificationDetail($specificationId)
    {
        $builder = $this->db->table($this->table . ' qs');
        $builder->select('
            qs.*,
            q.quotation_number,
            q.prospect_name,
            q.prospect_contact_person,
            q.prospect_email,
            q.prospect_phone,
            d.nama_departemen,
            tu.tipe as nama_tipe_unit,
            tu.jenis as jenis_unit,
            k.kapasitas_unit as kapasitas_nama,
            c.merk_charger as nama_charger,
            c.tipe_charger
        ');
        $builder->join('quotations q', 'qs.id_quotation = q.id_quotation', 'left');
        $builder->join('departemen d', 'qs.departemen_id = d.id_departemen', 'left');
        $builder->join('tipe_unit tu', 'qs.tipe_unit_id = tu.id_tipe_unit', 'left');
        $builder->join('kapasitas k', 'qs.kapasitas_id = k.id_kapasitas', 'left');
        $builder->join('charger c', 'qs.charger_id = c.id_charger', 'left');
        $builder->where('qs.id_specification', $specificationId);
        
        return $builder->get()->getRowArray();
    }

    /**
     * Update quotation total based on specifications
     */
    public function updateQuotationTotal($quotationId)
    {
        $summary = $this->getQuotationSummary($quotationId);
        
        $quotationModel = new \App\Models\QuotationModel();
        return $quotationModel->update($quotationId, [
            'total_amount' => $summary['total_value'],
            'subtotal' => $summary['total_value']
        ]);
    }

    /**
     * Get next specification code for dropdown - consistent with KontrakSpesifikasiModel
     */
    public function getNextSpekKode($quotationId) 
    {
        return $this->generateNextSpekKode($quotationId);
    }
}