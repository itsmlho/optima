<?php

namespace App\Models\Optimized;

use App\Models\UnitAssetModel;

/**
 * Optimized UnitAsset Model with JOIN optimization
 * Mengurangi kompleksitas JOIN dengan lazy loading dan caching
 */
class OptimizedUnitAssetModel extends UnitAssetModel
{
    /**
     * Get unit asset dengan optimized loading
     */
    public function getUnitAssetWithDetailsOptimized($no_unit = null)
    {
        $cache = \Config\Services::cache();
        $cacheKey = $no_unit ? "unit_asset_{$no_unit}" : "unit_assets_all";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        
        // Base query tanpa excessive JOINs
        $builder = $db->table('inventory_unit iu');
        $builder->select('iu.*');
        
        if ($no_unit) {
            $builder->where('iu.no_unit', $no_unit);
            $result = $builder->get()->getRowArray();
            
            if ($result) {
                // Lazy load additional data
                $result = $this->enrichUnitAssetData($result);
            }
        } else {
            $result = $builder->get()->getResultArray();
            
            // Batch enrich untuk multiple records
            $result = $this->batchEnrichUnitAssetData($result);
        }
        
        // Cache hasil selama 10 menit
        $cache->save($cacheKey, $result, 600);
        
        return $result;
    }

    /**
     * Enrich single unit asset data dengan lazy loading
     */
    protected function enrichUnitAssetData($unitData)
    {
        $db = \Config\Database::connect();
        
        // Load reference data secara selektif
        if ($unitData['departemen_id']) {
            $unitData['departemen'] = $this->getDepartemenById($unitData['departemen_id']);
        }
        
        if ($unitData['status_unit_id']) {
            $unitData['status_unit'] = $this->getStatusUnitById($unitData['status_unit_id']);
        }
        
        if ($unitData['tipe_unit_id']) {
            $unitData['tipe_unit'] = $this->getTipeUnitById($unitData['tipe_unit_id']);
        }
        
        if ($unitData['model_unit_id']) {
            $unitData['model_unit'] = $this->getModelUnitById($unitData['model_unit_id']);
        }
        
        if ($unitData['kapasitas_unit_id']) {
            $unitData['kapasitas'] = $this->getKapasitasById($unitData['kapasitas_unit_id']);
        }
        
        // Load kontrak info via kontrak_unit junction
        $kontrakUnit = $this->db->table('kontrak_unit')
            ->where('unit_id', $unitData['id_inventory_unit'])
            ->whereIn('status', ['ACTIVE', 'TEMP_ACTIVE'])
            ->where('is_temporary', 0)
            ->get()->getRowArray();
        if ($kontrakUnit) {
            $unitData['kontrak_info'] = $this->getKontrakInfoById($kontrakUnit['kontrak_id']);
        }
        
        return $unitData;
    }

    /**
     * Batch enrich untuk multiple records
     */
    protected function batchEnrichUnitAssetData($unitsData)
    {
        if (empty($unitsData)) {
            return $unitsData;
        }

        // Collect all IDs yang diperlukan
        $departemenIds = array_filter(array_column($unitsData, 'departemen_id'));
        $statusIds = array_filter(array_column($unitsData, 'status_unit_id'));
        $tipeIds = array_filter(array_column($unitsData, 'tipe_unit_id'));
        $modelIds = array_filter(array_column($unitsData, 'model_unit_id'));
        $kapasitasIds = array_filter(array_column($unitsData, 'kapasitas_unit_id'));
        $kontrakIds = array_filter(array_column($unitsData, 'kontrak_id'));

        // Batch load reference data
        $departemen = $this->batchGetDepartemen($departemenIds);
        $statusUnit = $this->batchGetStatusUnit($statusIds);
        $tipeUnit = $this->batchGetTipeUnit($tipeIds);
        $modelUnit = $this->batchGetModelUnit($modelIds);
        $kapasitas = $this->batchGetKapasitas($kapasitasIds);
        $kontrakInfo = $this->batchGetKontrakInfo($kontrakIds);

        // Enrich each unit dengan data yang sudah di-batch load
        foreach ($unitsData as &$unit) {
            if ($unit['departemen_id'] && isset($departemen[$unit['departemen_id']])) {
                $unit['departemen'] = $departemen[$unit['departemen_id']];
            }
            
            if ($unit['status_unit_id'] && isset($statusUnit[$unit['status_unit_id']])) {
                $unit['status_unit'] = $statusUnit[$unit['status_unit_id']];
            }
            
            if ($unit['tipe_unit_id'] && isset($tipeUnit[$unit['tipe_unit_id']])) {
                $unit['tipe_unit'] = $tipeUnit[$unit['tipe_unit_id']];
            }
            
            if ($unit['model_unit_id'] && isset($modelUnit[$unit['model_unit_id']])) {
                $unit['model_unit'] = $modelUnit[$unit['model_unit_id']];
            }
            
            if ($unit['kapasitas_unit_id'] && isset($kapasitas[$unit['kapasitas_unit_id']])) {
                $unit['kapasitas'] = $kapasitas[$unit['kapasitas_unit_id']];
            }
            
            if ($unit['kontrak_id'] && isset($kontrakInfo[$unit['kontrak_id']])) {
                $unit['kontrak_info'] = $kontrakInfo[$unit['kontrak_id']];
            }
        }

        return $unitsData;
    }

    /**
     * Single record loaders dengan caching
     */
    protected function getDepartemenById($id)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "departemen_{$id}";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('departemen')) {
            return null;
        }
        
        $result = $db->table('departemen')
                     ->where('id_departemen', $id)
                     ->get()
                     ->getRowArray();
        
        $cache->save($cacheKey, $result, 1800); // Cache 30 menit
        return $result;
    }

    protected function getStatusUnitById($id)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "status_unit_{$id}";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('status_unit')) {
            return null;
        }
        
        $result = $db->table('status_unit')
                     ->where('id_status', $id)
                     ->get()
                     ->getRowArray();
        
        $cache->save($cacheKey, $result, 1800);
        return $result;
    }

    protected function getTipeUnitById($id)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "tipe_unit_{$id}";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('tipe_unit')) {
            return null;
        }
        
        $result = $db->table('tipe_unit')
                     ->where('id_tipe_unit', $id)
                     ->get()
                     ->getRowArray();
        
        $cache->save($cacheKey, $result, 1800);
        return $result;
    }

    protected function getModelUnitById($id)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "model_unit_{$id}";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('model_unit')) {
            return null;
        }
        
        $result = $db->table('model_unit')
                     ->where('id_model_unit', $id)
                     ->get()
                     ->getRowArray();
        
        $cache->save($cacheKey, $result, 1800);
        return $result;
    }

    protected function getKapasitasById($id)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "kapasitas_{$id}";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        if (!$db->tableExists('kapasitas')) {
            return null;
        }
        
        $result = $db->table('kapasitas')
                     ->where('id_kapasitas', $id)
                     ->get()
                     ->getRowArray();
        
        $cache->save($cacheKey, $result, 1800);
        return $result;
    }

    protected function getKontrakInfoById($id)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "kontrak_info_{$id}";
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        
        $result = $db->table('kontrak k')
                     ->select('k.*, c.customer_name, (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name')
                     ->join('customers c', 'c.id = k.customer_id', 'left')
                     ->where('k.id', $id)
                     ->get()
                     ->getRowArray();
        
        $cache->save($cacheKey, $result, 1800);
        return $result;
    }

    /**
     * Batch loaders untuk efficiency
     */
    protected function batchGetDepartemen($ids)
    {
        if (empty($ids)) return [];
        
        $db = \Config\Database::connect();
        if (!$db->tableExists('departemen')) {
            return [];
        }
        
        $results = $db->table('departemen')
                      ->whereIn('id_departemen', array_unique($ids))
                      ->get()
                      ->getResultArray();
        
        $indexed = [];
        foreach ($results as $row) {
            $indexed[$row['id_departemen']] = $row;
        }
        
        return $indexed;
    }

    protected function batchGetStatusUnit($ids)
    {
        if (empty($ids)) return [];
        
        $db = \Config\Database::connect();
        if (!$db->tableExists('status_unit')) {
            return [];
        }
        
        $results = $db->table('status_unit')
                      ->whereIn('id_status', array_unique($ids))
                      ->get()
                      ->getResultArray();
        
        $indexed = [];
        foreach ($results as $row) {
            $indexed[$row['id_status']] = $row;
        }
        
        return $indexed;
    }

    protected function batchGetTipeUnit($ids)
    {
        if (empty($ids)) return [];
        
        $db = \Config\Database::connect();
        if (!$db->tableExists('tipe_unit')) {
            return [];
        }
        
        $results = $db->table('tipe_unit')
                      ->whereIn('id_tipe_unit', array_unique($ids))
                      ->get()
                      ->getResultArray();
        
        $indexed = [];
        foreach ($results as $row) {
            $indexed[$row['id_tipe_unit']] = $row;
        }
        
        return $indexed;
    }

    protected function batchGetModelUnit($ids)
    {
        if (empty($ids)) return [];
        
        $db = \Config\Database::connect();
        if (!$db->tableExists('model_unit')) {
            return [];
        }
        
        $results = $db->table('model_unit')
                      ->whereIn('id_model_unit', array_unique($ids))
                      ->get()
                      ->getResultArray();
        
        $indexed = [];
        foreach ($results as $row) {
            $indexed[$row['id_model_unit']] = $row;
        }
        
        return $indexed;
    }

    protected function batchGetKapasitas($ids)
    {
        if (empty($ids)) return [];
        
        $db = \Config\Database::connect();
        if (!$db->tableExists('kapasitas')) {
            return [];
        }
        
        $results = $db->table('kapasitas')
                      ->whereIn('id_kapasitas', array_unique($ids))
                      ->get()
                      ->getResultArray();
        
        $indexed = [];
        foreach ($results as $row) {
            $indexed[$row['id_kapasitas']] = $row;
        }
        
        return $indexed;
    }

    protected function batchGetKontrakInfo($ids)
    {
        if (empty($ids)) return [];
        
        $db = \Config\Database::connect();
        
        $results = $db->table('kontrak k')
                      ->select('k.*, c.customer_name, (SELECT cl.location_name FROM kontrak_unit ku JOIN customer_locations cl ON cl.id = ku.customer_location_id WHERE ku.kontrak_id = k.id LIMIT 1) as location_name')
                      ->join('customers c', 'c.id = k.customer_id', 'left')
                      ->whereIn('k.id', array_unique($ids))
                      ->get()
                      ->getResultArray();
        
        $indexed = [];
        foreach ($results as $row) {
            $indexed[$row['id']] = $row;
        }
        
        return $indexed;
    }

    /**
     * Create database view untuk unit assets jika diperlukan
     */
    public function createUnitAssetView()
    {
        $db = \Config\Database::connect();
        
        $sql = "
        CREATE OR REPLACE VIEW v_unit_assets_summary AS
        SELECT 
            iu.*,
            d.nama_departemen as departemen_name,
            su.status_unit as status_unit_name,
            tu.nama_tipe_unit as tipe_unit_name,
            mu.merk_unit,
            mu.model_unit,
            k_info.kapasitas_unit as kapasitas_unit_name,
            CONCAT(c.customer_name, ' - ', cl.location_name) as customer_location
        FROM inventory_unit iu
        LEFT JOIN departemen d ON d.id_departemen = iu.departemen_id
        LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
        LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
        LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
        LEFT JOIN kapasitas k_info ON k_info.id_kapasitas = iu.kapasitas_unit_id
        LEFT JOIN kontrak_unit ku_v ON ku_v.unit_id = iu.id_inventory_unit AND ku_v.status IN ('ACTIVE','TEMP_ACTIVE') AND ku_v.is_temporary = 0
        LEFT JOIN kontrak k ON ku_v.kontrak_id = k.id
        LEFT JOIN customers c ON c.id = k.customer_id
        ";

        try {
            $db->query($sql);
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to create unit asset view: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get units dengan search dan filter yang optimized
     */
    public function getUnitsOptimized($search = '', $filters = [], $page = 1, $perPage = 10)
    {
        $cache = \Config\Services::cache();
        $cacheKey = "units_search_" . md5($search . serialize($filters) . $page . $perPage);
        
        if ($cached = $cache->get($cacheKey)) {
            return $cached;
        }

        $db = \Config\Database::connect();
        
        // Gunakan view jika ada
        if ($this->hasView('v_unit_assets_summary')) {
            $builder = $db->table('v_unit_assets_summary');
        } else {
            $builder = $db->table('inventory_unit iu');
            $builder->select('iu.*');
        }

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('iu.no_unit', $search)
                    ->orLike('iu.serial_number', $search)
                    ->orLike('iu.lokasi_unit', $search)
                    ->groupEnd();
        }

        // Apply filters
        if (!empty($filters['status_unit_id'])) {
            $builder->where('iu.status_unit_id', $filters['status_unit_id']);
        }
        
        if (!empty($filters['tipe_unit_id'])) {
            $builder->where('iu.tipe_unit_id', $filters['tipe_unit_id']);
        }
        
        if (!empty($filters['departemen_id'])) {
            $builder->where('iu.departemen_id', $filters['departemen_id']);
        }

        // Pagination
        $offset = ($page - 1) * $perPage;
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        $results = $builder->limit($perPage, $offset)
                          ->orderBy('iu.no_unit', 'ASC')
                          ->get()
                          ->getResultArray();

        $response = [
            'data' => $results,
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => ceil($total / $perPage)
        ];

        // Cache hasil selama 5 menit
        $cache->save($cacheKey, $response, 300);
        
        return $response;
    }

    /**
     * DataTable optimized method
     */
    public function getDataTableOptimized($params = [])
    {
        $search = $params['search'] ?? '';
        $start = $params['start'] ?? 0;
        $length = $params['length'] ?? 25;
        $orderColumn = $params['orderColumn'] ?? 'iu.no_unit';
        $orderDir = $params['orderDir'] ?? 'ASC';
        $conditions = $params['conditions'] ?? [];

        $db = \Config\Database::connect();
        
        // Gunakan view jika ada
        if ($this->hasView('v_unit_assets_summary')) {
            $builder = $db->table('v_unit_assets_summary iu');
        } else {
            $builder = $db->table('inventory_unit iu');
            $builder->select('iu.*');
        }

        // Apply search
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('iu.no_unit', $search)
                    ->orLike('iu.serial_number', $search)
                    ->orLike('iu.lokasi_unit', $search)
                    ->groupEnd();
        }

        // Apply additional conditions
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                $builder->whereIn($field, $value);
            } else {
                $builder->where($field, $value);
            }
        }

        // Get total count
        $totalBuilder = clone $builder;
        $total = $totalBuilder->countAllResults(false);
        
        // Get filtered count
        $filteredBuilder = clone $builder;
        $filtered = $filteredBuilder->countAllResults(false);

        // Apply ordering and pagination
        $builder->orderBy($orderColumn, $orderDir)
               ->limit($length, $start);

        $data = $builder->get()->getResultArray();

        return [
            'data' => $data,
            'total' => $total,
            'filtered' => $filtered
        ];
    }

    /**
     * Check if view exists
     */
    protected function hasView($viewName)
    {
        $db = \Config\Database::connect();
        try {
            $result = $db->query("SHOW TABLES LIKE '{$viewName}'")->getResult();
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }
}