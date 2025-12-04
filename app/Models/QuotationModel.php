<?php

namespace App\Models;

use CodeIgniter\Model;

class QuotationModel extends Model
{
    protected $table = 'quotations';
    protected $primaryKey = 'id_quotation';
    protected $allowedFields = [
        'quotation_number', 'prospect_name', 'prospect_contact_person', 'prospect_email', 'prospect_phone',
        'prospect_address', 'prospect_city', 'prospect_province', 'prospect_postal_code',
        'quotation_title', 'quotation_description', 'quotation_date', 'valid_until', 
        'currency', 'subtotal', 'discount_percent', 'discount_amount', 'tax_percent', 'tax_amount', 'total_amount',
        'payment_terms', 'delivery_terms', 'warranty_terms', 'stage', 'workflow_stage', 'probability_percent', 'expected_close_date',
        'is_deal', 'deal_date', 'created_customer_id', 'created_contract_id', 'created_by', 'assigned_to'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'quotation_number' => 'required|max_length[50]',
        'prospect_name' => 'required|max_length[255]',
        'quotation_title' => 'required|max_length[255]',
        'quotation_date' => 'required|valid_date',
        'valid_until' => 'required|valid_date',
        'stage' => 'required|in_list[DRAFT,SENT,ACCEPTED,REJECTED,EXPIRED]'
    ];
    
    protected $validationMessages = [
        'quotation_number' => [
            'required' => 'Quotation number is required',
            'max_length' => 'Quotation number cannot exceed 50 characters'
        ],
        'prospect_name' => [
            'required' => 'Prospect name is required',
            'max_length' => 'Prospect name cannot exceed 255 characters'
        ]
    ];

    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = \Config\Database::connect();
    }

    /**
     * Get quotations for DataTables with statistics
     */
    public function getQuotationsForDataTable($start, $length, $search, $orderColumn, $orderDir)
    {
        $builder = $this->db->table($this->table . ' q');
        
        // Filter out invalid IDs (0 or null)
        $builder->where('q.id_quotation >', 0);
        $builder->where('q.id_quotation IS NOT NULL', null, false);
        
        // Search functionality
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('q.quotation_number', $search)
                    ->orLike('q.prospect_name', $search)
                    ->orLike('q.prospect_contact_person', $search)
                    ->orLike('q.quotation_title', $search)
                    ->orLike('q.stage', $search)
                    ->groupEnd();
        }

        // Get total records with search filter
        $totalRecords = $builder->countAllResults(false);

        // Reset builder for actual data query
        $builder = $this->db->table($this->table . ' q');
        
        // Select all needed columns
        $builder->select('q.id_quotation, q.quotation_number, q.prospect_name, q.prospect_contact_person, 
                         q.quotation_title, q.quotation_date, q.valid_until, q.stage, q.total_amount, 
                         q.is_deal, q.created_at, q.updated_at');
        
        // Filter out invalid IDs
        $builder->where('q.id_quotation >', 0);
        $builder->where('q.id_quotation IS NOT NULL', null, false);
        
        // Apply search again for data query
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('q.quotation_number', $search)
                    ->orLike('q.prospect_name', $search)
                    ->orLike('q.prospect_contact_person', $search)
                    ->orLike('q.quotation_title', $search)
                    ->orLike('q.stage', $search)
                    ->groupEnd();
        }

        // Ordering
        $columns = ['q.quotation_number', 'q.prospect_name', 'q.quotation_title', 'q.quotation_date', 'q.total_amount', 'q.stage'];
        if (isset($columns[$orderColumn])) {
            $builder->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $builder->orderBy('q.created_at', 'DESC');
        }

        // Pagination
        if ($length != -1) {
            $builder->limit($length, $start);
        }

        $data = $builder->get()->getResultArray();

        // Get total count without search
        $totalRecordsWithoutSearch = $this->countValidQuotations();

        // Get statistics
        $stats = $this->getQuotationStatistics();

        return [
            'data' => $data,
            'total' => $totalRecords,
            'recordsTotal' => $totalRecordsWithoutSearch,
            'recordsFiltered' => $totalRecords
        ];
    }

    /**
     * DataTable integration method
     */
    public function getDataTable($request)
    {
        $start = $request->getPost('start') ?? 0;
        $length = $request->getPost('length') ?? 10;
        $searchValue = $request->getPost('search')['value'] ?? '';
        $orderColumn = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'desc';

        return $this->getQuotationsForDataTable($start, $length, $searchValue, $orderColumn, $orderDir);
    }

    /**
     * Count valid quotations (exclude 0 and null IDs)
     */
    public function countValidQuotations()
    {
        $builder = $this->builder();
        $builder->where('id_quotation >', 0);
        $builder->where('id_quotation IS NOT NULL', null, false);
        return $builder->countAllResults();
    }

    /**
     * Count all data for DataTables
     */
    public function countAllData()
    {
        return $this->countValidQuotations();
    }

    /**
     * Count filtered data for DataTables
     */
    public function countFilteredData($request)
    {
        $searchValue = $request->getPost('search')['value'] ?? '';
        
        if (empty($searchValue)) {
            return $this->countValidQuotations();
        }

        $builder = $this->db->table($this->table . ' q');
        $builder->where('q.id_quotation >', 0);
        $builder->where('q.id_quotation IS NOT NULL', null, false);
        $builder->groupStart()
                ->like('q.quotation_number', $searchValue)
                ->orLike('q.prospect_name', $searchValue)
                ->orLike('q.prospect_company', $searchValue)
                ->orLike('q.quotation_title', $searchValue)
                ->orLike('q.stage', $searchValue)
                ->groupEnd();

        return $builder->countAllResults();
    }

    /**
     * Get statistics for dashboard
     */
    // REMOVED: Unnecessary wrapper function getStats() 
    // Use getQuotationStatistics() directly instead
    
    /**
     * Get quotation statistics
     */
    public function getQuotationStatistics()
    {
        $builder = $this->db->table($this->table);
        $builder->where('id_quotation >', 0);
        
        $total = $builder->countAllResults(false);
        
        $draft = $builder->where('stage', 'DRAFT')->countAllResults(false);
        $sent = $builder->where('stage', 'SENT')->countAllResults(false);
        $accepted = $builder->where('stage', 'ACCEPTED')->countAllResults(false);
        $rejected = $builder->where('stage', 'REJECTED')->countAllResults(false);
        $expired = $builder->where('stage', 'EXPIRED')->countAllResults(false);
        $deals = $builder->where('is_deal', 1)->countAllResults(false);
        
        // Calculate total value
        $builder->selectSum('total_amount', 'total_value');
        $builder->where('stage !=', 'REJECTED');
        $totalValueResult = $builder->get()->getRow();
        $totalValue = $totalValueResult->total_value ?? 0;
        
        // Calculate conversion rate
        $conversionRate = $total > 0 ? round(($deals / $total) * 100, 1) : 0;

        return [
            'total' => $total,
            'draft' => $draft,
            'sent' => $sent,
            'accepted' => $accepted,
            'rejected' => $rejected,
            'expired' => $expired,
            'deals' => $deals,
            'total_value' => $totalValue,
            'conversion_rate' => $conversionRate
        ];
    }


    /**
     * Generate next quotation number
     */
    public function generateQuotationNumber()
    {
        $year = date('Y');
        $month = date('m');
        $prefix = 'QUO-' . $year . $month . '-';
        
        $lastQuotation = $this->like('quotation_number', $prefix)
                             ->orderBy('id_quotation', 'DESC')
                             ->first();
        
        if (!$lastQuotation) {
            return $prefix . '001';
        }
        
        $lastNumber = str_replace($prefix, '', $lastQuotation['quotation_number']);
        $nextNumber = str_pad((int)$lastNumber + 1, 3, '0', STR_PAD_LEFT);
        
        return $prefix . $nextNumber;
    }

    /**
     * Update quotation stage
     */
    public function updateStage($quotationId, $stage)
    {
        $allowedStages = ['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'EXPIRED'];
        
        if (!in_array($stage, $allowedStages)) {
            return false;
        }

        return $this->update($quotationId, ['stage' => $stage]);
    }

    /**
     * Mark quotation as deal
     */
    public function markAsDeal($quotationId)
    {
        return $this->update($quotationId, [
            'is_deal' => 1,
            'stage' => 'ACCEPTED'
        ]);
    }

    /**
     * Get quotation with specifications
     */
    public function getQuotationWithSpecs($quotationId)
    {
        $quotation = $this->find($quotationId);
     
            
        if ($quotation) {
            $specModel = new \App\Models\QuotationSpecificationModel();
            $quotation['specifications'] = $specModel->getQuotationSpecifications($quotationId);
        }
        
        return $quotation;
    }

    /**
     * Get single quotation with customer info
     */
    public function getQuotationDetail($id)
    {
        return $this->select('quotations.*')
            ->where('quotations.id_quotation', $id)
            ->first();
    }

    /**
     * Get quotation with contract information for SPK creation
     */
    public function getQuotationWithContract($id)
    {
        $builder = $this->db->table('quotations q');
        $builder->select('
            q.*,
            c.customer_name,
            cl.location_name,
            cl.contact_person,
            cl.phone,
            cl.address,
            k.id as contract_id,
            k.no_kontrak
        ');
        $builder->join('customers c', 'q.created_customer_id = c.id', 'left');
        $builder->join('customer_locations cl', 'c.id = cl.customer_id', 'left');
        $builder->join('kontrak k', 'q.created_contract_id = k.id', 'left');
        $builder->where('q.id_quotation', $id);
        
        return $builder->get()->getRowArray();
    }
    
}