<?php

namespace App\Models;

use CodeIgniter\Model;

class OptimizedUnitAssetModel extends Model
{
    protected $table = 'inventory_unit';
    protected $primaryKey = 'id_inventory_unit';
    protected $allowedFields = ['no_unit', 'customer_id', 'departemen_id', 'merk_unit', 'model_unit', 'serial_number', 'tahun_pembuatan', 'kondisi', 'lokasi', 'status', 'harga_beli', 'tanggal_pembelian', 'supplier_id', 'garansi_mulai', 'garansi_selesai', 'catatan'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get optimized unit data with minimal joins
     */
    public function getOptimizedUnits($search = '', $departmentIds = [])
    {
        $builder = $this->db->table($this->table . ' iu');
        
        // Select only necessary fields for performance
        $builder->select([
            'iu.id_inventory_unit',
            'iu.no_unit',
            'iu.merk_unit',
            'iu.model_unit',
            'iu.serial_number',
            'iu.kondisi',
            'iu.lokasi',
            'iu.departemen_id',
            'c.customer_name as nama_customer',
            'd.nama_departemen'
        ]);
        
        // Optimized joins - route through kontrak_unit junction to get customer
        $builder->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN (\'ACTIVE\',\'TEMP_ACTIVE\') AND ku.is_temporary = 0', 'left');
        $builder->join('kontrak ktr', 'ktr.id = ku.kontrak_id', 'left');
        $builder->join('customer_locations cl', 'cl.id = ktr.customer_location_id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        $builder->join('departemen d', 'd.id = iu.departemen_id', 'left');
        
        // Search filter
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('iu.no_unit', $search)
                    ->orLike('iu.merk_unit', $search)
                    ->orLike('iu.model_unit', $search)
                    ->orLike('c.customer_name', $search)
                    ->groupEnd();
        }
        
        // Department filter for division-based access
        if (!empty($departmentIds)) {
            $builder->whereIn('iu.departemen_id', $departmentIds);
        }
        
        // Only active units
        $builder->where('iu.status', 'Active');
        
        $builder->orderBy('iu.no_unit', 'ASC');
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get unit details with lazy loading
     */
    public function getUnitWithLazyDetails($unitId)
    {
        $cache = \Config\Services::cache();
        $cacheKey = 'unit_details_' . $unitId;
        
        // Try cache first
        $cachedResult = $cache->get($cacheKey);
        if ($cachedResult !== null) {
            return $cachedResult;
        }
        
        $builder = $this->db->table($this->table . ' iu');
        
        $builder->select([
            'iu.*',
            'c.customer_name as nama_customer',
            'c.primary_address as customer_address', 
            'c.primary_phone as customer_phone',
            'd.nama_departemen',
            's.nama_supplier'
        ]);
        
        $builder->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN (\'ACTIVE\',\'TEMP_ACTIVE\') AND ku.is_temporary = 0', 'left');
        $builder->join('kontrak ktr', 'ktr.id = ku.kontrak_id', 'left');
        $builder->join('customer_locations cl', 'cl.id = ktr.customer_location_id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        $builder->join('departemen d', 'd.id = iu.departemen_id', 'left');
        $builder->join('supplier s', 's.id = iu.supplier_id', 'left');
        
        $builder->where('iu.id_inventory_unit', $unitId);
        
        $result = $builder->get()->getRowArray();
        
        // Cache for 10 minutes
        if ($result) {
            $cache->save($cacheKey, $result, 600);
        }
        
        return $result;
    }
    
    /**
     * Get unit maintenance history with optimized queries
     */
    public function getUnitMaintenanceHistory($unitId, $limit = 10)
    {
        $builder = $this->db->table('work_orders wo');
        
        $builder->select([
            'wo.id',
            'wo.work_order_number',
            'wo.report_date',
            'wo.completion_date',
            'wo.issue_description',
            'ws.nama_status as status',
            'ws.warna as status_color',
            'wp.nama_prioritas as priority'
        ]);
        
        $builder->join('work_order_statuses ws', 'ws.id = wo.status_id', 'left');
        $builder->join('work_order_priorities wp', 'wp.id = wo.priority', 'left');
        
        $builder->where('wo.unit_id', $unitId);
        $builder->orderBy('wo.report_date', 'DESC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get units by department with caching
     */
    public function getUnitsByDepartment($departmentIds = [])
    {
        $cacheKey = 'units_by_dept_' . md5(serialize($departmentIds));
        $cache = \Config\Services::cache();
        
        $cachedResult = $cache->get($cacheKey);
        if ($cachedResult !== null) {
            return $cachedResult;
        }
        
        $builder = $this->db->table($this->table . ' iu');
        
        $builder->select([
            'iu.id_inventory_unit',
            'iu.no_unit',
            'iu.merk_unit',
            'iu.model_unit',
            'iu.departemen_id',
            'c.customer_name as nama_customer',
            'd.nama_departemen'
        ]);
        
        $builder->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN (\'ACTIVE\',\'TEMP_ACTIVE\') AND ku.is_temporary = 0', 'left');
        $builder->join('kontrak ktr', 'ktr.id = ku.kontrak_id', 'left');
        $builder->join('customer_locations cl', 'cl.id = ktr.customer_location_id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        $builder->join('departemen d', 'd.id = iu.departemen_id', 'left');
        
        if (!empty($departmentIds)) {
            $builder->whereIn('iu.departemen_id', $departmentIds);
        }
        
        $builder->where('iu.status', 'Active');
        $builder->orderBy('d.nama_departemen', 'ASC');
        $builder->orderBy('iu.no_unit', 'ASC');
        
        $result = $builder->get()->getResultArray();
        
        // Cache for 15 minutes
        $cache->save($cacheKey, $result, 900);
        
        return $result;
    }
    
    /**
     * Search units with autocomplete optimization
     */
    public function searchUnitsAutocomplete($term, $departmentIds = [], $limit = 10)
    {
        $builder = $this->db->table($this->table . ' iu');
        
        $builder->select([
            'iu.id_inventory_unit as value',
            'CONCAT(iu.no_unit, " - ", COALESCE(c.customer_name, "N/A"), " (", iu.merk_unit, " ", iu.model_unit, ")") as label',
            'iu.no_unit',
            'iu.merk_unit',
            'iu.model_unit',
            'c.customer_name as nama_customer'
        ]);
        
        $builder->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN (\'ACTIVE\',\'TEMP_ACTIVE\') AND ku.is_temporary = 0', 'left');
        $builder->join('kontrak ktr', 'ktr.id = ku.kontrak_id', 'left');
        $builder->join('customer_locations cl', 'cl.id = ktr.customer_location_id', 'left');
        $builder->join('customers c', 'c.id = cl.customer_id', 'left');
        
        if (!empty($term)) {
            $builder->groupStart()
                    ->like('iu.no_unit', $term)
                    ->orLike('iu.merk_unit', $term)
                    ->orLike('iu.model_unit', $term)
                    ->orLike('c.customer_name', $term)
                    ->groupEnd();
        }
        
        if (!empty($departmentIds)) {
            $builder->whereIn('iu.departemen_id', $departmentIds);
        }
        
        $builder->where('iu.status', 'Active');
        $builder->orderBy('iu.no_unit', 'ASC');
        $builder->limit($limit);
        
        return $builder->get()->getResultArray();
    }
    
    /**
     * Get unit statistics with performance optimization
     */
    public function getUnitStats($departmentIds = [])
    {
        $builder = $this->db->table($this->table . ' iu');
        
        $builder->select([
            'COUNT(*) as total',
            'SUM(CASE WHEN iu.kondisi = "Baik" THEN 1 ELSE 0 END) as good_condition',
            'SUM(CASE WHEN iu.kondisi = "Rusak Ringan" THEN 1 ELSE 0 END) as minor_damage',
            'SUM(CASE WHEN iu.kondisi = "Rusak Berat" THEN 1 ELSE 0 END) as major_damage',
            'SUM(CASE WHEN iu.status = "Active" THEN 1 ELSE 0 END) as active',
            'SUM(CASE WHEN iu.status = "Inactive" THEN 1 ELSE 0 END) as inactive'
        ]);
        
        if (!empty($departmentIds)) {
            $builder->whereIn('iu.departemen_id', $departmentIds);
        }
        
        return $builder->get()->getRowArray();
    }
}