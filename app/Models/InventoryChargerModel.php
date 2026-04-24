<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * InventoryChargerModel
 * 
 * Model untuk mengelola inventory chargers
 * Table: inventory_chargers
 * 
 * Virtual Column:
 * - item_number: Auto-generated format C-0001, C-0002, dst
 */
class InventoryChargerModel extends Model
{
    protected $table = 'inventory_chargers';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'item_number',
        'charger_type_id',
        'serial_number',
        'input_voltage',
        'output_voltage',
        'output_ampere',
        'purchase_order_id',
        'inventory_unit_id',
        'physical_condition',
        'completeness',
        'physical_notes',
        'storage_location',
        'warehouse_location_id',
        'status',
        'received_at',
        'notes',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'id'                => 'permit_empty|integer',
        'item_number'       => 'permit_empty|max_length[50]|is_unique[inventory_chargers.item_number,id,{id}]',
        'charger_type_id'   => 'permit_empty|integer',
        'serial_number'     => 'permit_empty|max_length[100]|is_unique[inventory_chargers.serial_number,id,{id}]',
        'input_voltage'     => 'permit_empty|max_length[20]',
        'output_voltage'    => 'permit_empty|max_length[20]',
        'output_ampere'     => 'permit_empty|max_length[20]',
        'purchase_order_id' => 'permit_empty|integer',
        'inventory_unit_id' => 'permit_empty|integer',
        'physical_condition'=> 'permit_empty|in_list[GOOD,MINOR_DAMAGE,MAJOR_DAMAGE]',
        'completeness'      => 'permit_empty|in_list[COMPLETE,INCOMPLETE]',
        'storage_location'  => 'permit_empty|max_length[255]',
        'warehouse_location_id' => 'permit_empty|integer',
        'status'            => 'permit_empty|in_list[AVAILABLE,IN_USE,SPARE,MAINTENANCE,BROKEN,RESERVED,SOLD]',
        'received_at'       => 'permit_empty|valid_date',
    ];

    protected $validationMessages = [
        'item_number' => [
            'is_unique' => 'Item number sudah digunakan, harus unik'
        ],
        'charger_type_id' => [
            'integer' => 'Charger Type ID harus berupa angka'
        ],
        'serial_number' => [
            'max_length' => 'Serial number tidak boleh lebih dari 100 karakter',
            'is_unique'  => 'Serial number sudah digunakan oleh charger lain. SN harus unik.'
        ],
        'status' => [
            'in_list' => 'Status must be: AVAILABLE, IN_USE, SPARE, MAINTENANCE, BROKEN, RESERVED, or SOLD'
        ],
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /**
     * Get data for DataTables with server-side processing
     */
    public function getDataTable($request)
    {
        $builder = $this->db->table($this->table . ' ic');
        
        // Select fields with proper aliases + add virtual columns for backward compatibility
        $builder->select('
            ic.*,
            ic.id as id_inventory_attachment,
            "charger" as tipe_item,
            c.merk_charger,
            c.tipe_charger,
            ic.serial_number as sn_charger,
            COALESCE(iu.no_unit, iu.no_unit_na) as no_unit
        ');

        // Add unit number if linked to a unit
        $builder->join('inventory_unit iu', 'ic.inventory_unit_id = iu.id_inventory_unit', 'left');

        // Join with charger master table
        $builder->join('charger c', 'ic.charger_type_id = c.id_charger', 'left');

        // Filter by status if provided
        if (!empty($request['status_filter']) && $request['status_filter'] !== 'all') {
            $builder->where('ic.status', $request['status_filter']);
        }

        // Filter by voltage if provided (charger type filter)
        if (!empty($request['model_filter'])) {
            $builder->like('c.tipe_charger', $request['model_filter']);
        }

        // Search functionality
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart();
            $builder->like('ic.item_number', $searchValue);
            $builder->orLike('ic.serial_number', $searchValue);
            $builder->orLike('ic.input_voltage', $searchValue);
            $builder->orLike('ic.output_voltage', $searchValue);
            $builder->orLike('ic.output_ampere', $searchValue);
            $builder->orLike('ic.physical_condition', $searchValue);
            $builder->orLike('ic.storage_location', $searchValue);
            $builder->orLike('ic.status', $searchValue);
            $builder->orLike('ic.notes', $searchValue);
            // Search in joined tables
            $builder->orLike('c.merk_charger', $searchValue);
            $builder->orLike('c.tipe_charger', $searchValue);
            $builder->orLike('iu.no_unit', $searchValue);
            $builder->groupEnd();
        }

        // Get total records before pagination
        $totalFiltered = $builder->countAllResults(false);

        // Ordering
        if (!empty($request['order'])) {
            $orderMap = [
                0 => 'ic.id',
                1 => 'ic.item_number',
                2 => 'c.merk_charger',
                3 => 'c.tipe_charger',
                4 => 'ic.serial_number',
                5 => 'ic.physical_condition',
                6 => 'ic.status',
                7 => 'ic.storage_location'
            ];

            $orderColumnIndex = $request['order'][0]['column'];
            $orderDir = $request['order'][0]['dir'];

            if (isset($orderMap[$orderColumnIndex])) {
                $builder->orderBy($orderMap[$orderColumnIndex], $orderDir);
            }
        }

        // Pagination
        if (isset($request['length']) && $request['length'] != -1) {
            $builder->limit($request['length'], $request['start']);
        }

        $query = $builder->get();
        $data = $query->getResultArray();

        return [
            'data' => $data,
            'recordsFiltered' => $totalFiltered
        ];
    }

    /**
     * Get charger statistics — single query (no N+1)
     */
    public function getStats()
    {
        $row = $this->db->query("
            SELECT
                COUNT(*) as total,
                SUM(status = 'AVAILABLE')   as available,
                SUM(status = 'IN_USE')      as in_use,
                SUM(status = 'SPARE')       as spare,
                SUM(status = 'MAINTENANCE') as maintenance,
                SUM(status = 'BROKEN')      as broken
            FROM {$this->table}
        ")->getRowArray();

        return $row ?: [
            'total' => 0, 'available' => 0, 'in_use' => 0,
            'spare' => 0, 'maintenance' => 0, 'broken' => 0,
        ];
    }

    /**
     * Get charger detail with related data
     */
    public function getChargerDetail($id)
    {
        $builder = $this->db->table($this->table . ' ic');
        
        // Select charger fields
        $builder->select('ic.*, c.merk_charger, c.tipe_charger');
        
        // Add unit info
        $builder->select('COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, iu.serial_number as unit_serial_number');
        $builder->join('inventory_unit iu', 'ic.inventory_unit_id = iu.id_inventory_unit', 'left');

        // Join charger master
        $builder->join('charger c', 'ic.charger_type_id = c.id_charger', 'left');

        // Add PO info
        $builder->select('po.no_po, po.tanggal_po, po.status as po_status, s.nama_supplier');
        $builder->join('purchase_orders po', 'ic.purchase_order_id = po.id_po', 'left');
        $builder->join('suppliers s', 'po.supplier_id = s.id_supplier', 'left');

        $builder->where('ic.id', $id);
        
        $query = $builder->get();
        return $query->getRowArray();
    }

    /**
     * Check if a charger is available
     */
    public function isAvailable(int $chargerId): bool
    {
        $row = $this->select('status')->where('id', $chargerId)->first();
        if (!$row) return false;
        return $row['status'] === 'AVAILABLE';
    }

    /**
     * Get all chargers (AVAILABLE, SPARE, and USED) for SPK selection
     * USED chargers can be detached from workshop units and re-assigned
     */
    public function getAvailableChargers(string $q = ''): array
    {
        $builder = $this->select('inventory_chargers.*, c.merk_charger, c.tipe_charger, 
                             iu.no_unit as installed_unit_no, iu.serial_number as installed_unit_sn, 
                             mu.merk_unit as installed_unit_merk, mu.model_unit as installed_unit_model,
                             iu.status_unit_id as installed_unit_status_id')
            ->join('charger c', 'c.id_charger = inventory_chargers.charger_type_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = inventory_chargers.inventory_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->groupStart()
                ->where('inventory_chargers.status', 'AVAILABLE')
                ->orGroupStart()
                    ->where('inventory_chargers.status', 'IN_USE')
                    ->whereIn('iu.status_unit_id', [1, 2, 3, 12]) // AVAILABLE_STOCK, NON_ASSET_STOCK, BOOKED, RETURNED
                ->groupEnd()
            ->groupEnd()
            ->where('inventory_chargers.charger_type_id IS NOT NULL')
            ->orderBy('inventory_chargers.status', 'ASC') // AVAILABLE first, then IN_USE
            ->orderBy('inventory_chargers.item_number', 'ASC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('inventory_chargers.item_number', $q)
                ->orLike('inventory_chargers.serial_number', $q)
                ->orLike('c.merk_charger', $q)
                ->orLike('c.tipe_charger', $q)
            ->groupEnd();
        }

        return $builder->findAll(150);
    }

    /**
     * Get unit's current charger info
     */
    public function getUnitCharger($unitId): ?array
    {
        return $this->select('inventory_chargers.*, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'c.id_charger = inventory_chargers.charger_type_id', 'left')
            ->where('inventory_chargers.inventory_unit_id', $unitId)
            ->where('inventory_chargers.charger_type_id IS NOT NULL')
            ->first();
    }

    /**
     * Detach charger from unit and return to stock — ATOMIC transaction.
     */
    public function detachFromUnit($chargerId, $reason = 'Detached'): bool
    {
        $newStatus = 'AVAILABLE';
        $newLocation = 'Workshop';

        if (stripos($reason, 'rusak') !== false || stripos($reason, 'broken') !== false) {
            $newStatus = 'BROKEN';
        } elseif (stripos($reason, 'maintenance') !== false || stripos($reason, 'repair') !== false) {
            $newStatus = 'MAINTENANCE';
        }

        $db = $this->db;
        $db->transStart();
        try {
            $row = $db->query(
                'SELECT id, inventory_unit_id FROM inventory_chargers WHERE id = ? FOR UPDATE',
                [$chargerId]
            )->getRowArray();

            $oldUnit = $row['inventory_unit_id'] ?? null;

            $ok = $this->update($chargerId, [
                'inventory_unit_id' => null,
                'status'            => $newStatus,
                'storage_location'  => $newLocation,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            if ($oldUnit) {
                $auditService = new \App\Services\ComponentAuditService($db);
                $auditService->logRemoval('CHARGER', $chargerId, $oldUnit, [
                    'notes' => $reason,
                    'triggered_by' => 'DETACH_FROM_UNIT',
                ]);
            }

            $db->transComplete();
            return $ok;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryChargerModel::detachFromUnit] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Attach charger to unit (ELECTRIC units only)
     */
    public function attachToUnit($chargerId, $unitId, $unitNumber = null): bool
    {
        $db = \Config\Database::connect();
        
        // VALIDATION: Charger can only be installed on ELECTRIC units
        $unitInfo = $db->table('inventory_unit iu')
            ->select('COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, iu.departemen_id, d.nama_departemen')
            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
            ->where('iu.id_inventory_unit', $unitId)
            ->get()->getRowArray();
        
        if ($unitInfo) {
            $deptName = strtoupper($unitInfo['nama_departemen'] ?? '');
            
            // Check if unit is NOT electric (DIESEL or GASOLINE)
            if ($deptName === 'DIESEL' || $deptName === 'GASOLINE') {
                log_message('warning', "Charger cannot be installed on non-electric unit. Unit: {$unitInfo['no_unit']}, Department: {$deptName}");
                throw new \Exception("Charger hanya dapat dipasang pada unit ELECTRIC. Unit {$unitInfo['no_unit']} adalah {$deptName}.");
            }
        }
        
        return $this->update($chargerId, [
            'inventory_unit_id' => $unitId,
            'status' => 'IN_USE',
            'storage_location' => $unitNumber ? "Terpasang di Unit {$unitNumber}" : null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Assign charger to unit — ATOMIC with SELECT FOR UPDATE to prevent double-booking.
     *
     * @throws \Exception if charger is unavailable or unit type is invalid
     */
    public function assignToUnit(int $chargerId, int $unitId, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        $db->transStart();

        try {
            // 1. Lock the row — prevents concurrent requests reading stale AVAILABLE status
            $charger = $db->query(
                'SELECT id, status FROM inventory_chargers WHERE id = ? FOR UPDATE',
                [$chargerId]
            )->getRowArray();

            if (!$charger) {
                $db->transRollback();
                throw new \Exception("Charger #{$chargerId} tidak ditemukan.");
            }

            if ($charger['status'] !== 'AVAILABLE') {
                $db->transRollback();
                throw new \Exception(
                    "Charger #{$chargerId} tidak tersedia (status: {$charger['status']}). " .
                    "Kemungkinan sudah dipasang ke unit lain secara bersamaan."
                );
            }

            // 2. Validate unit type (ELECTRIC only)
            $unitInfo = $db->table('inventory_unit iu')
                ->select('COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, d.nama_departemen')
                ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
                ->where('iu.id_inventory_unit', $unitId)
                ->get()->getRowArray();

            if ($unitInfo) {
                $deptName = strtoupper($unitInfo['nama_departemen'] ?? '');
                if ($deptName === 'DIESEL' || $deptName === 'GASOLINE') {
                    $db->transRollback();
                    throw new \Exception("Charger hanya dapat dipasang pada unit ELECTRIC. Unit {$unitInfo['no_unit']} adalah {$deptName}.");
                }
            }

            // 3. Write
            $updated = $this->update($chargerId, [
                'inventory_unit_id' => $unitId,
                'status'            => 'IN_USE',
                'storage_location'  => $unitInfo ? "Terpasang di Unit {$unitInfo['no_unit']}" : null,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            // 4. Audit log
            $auditService = new \App\Services\ComponentAuditService($db);
            $auditService->logAssignment('CHARGER', $chargerId, $unitId, [
                'notes' => $note,
                'triggered_by' => 'ASSIGN_TO_UNIT',
            ]);

            $db->transComplete();
            return $updated;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryChargerModel::assignToUnit] ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove charger from unit — ATOMIC transaction.
     */
    public function removeFromUnit(int $chargerId, ?string $lokasi = null, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        $db->transStart();

        try {
            $row = $db->query(
                'SELECT id, inventory_unit_id FROM inventory_chargers WHERE id = ? FOR UPDATE',
                [$chargerId]
            )->getRowArray();

            $oldUnit = $row['inventory_unit_id'] ?? null;

            $ok = $this->update($chargerId, [
                'inventory_unit_id' => null,
                'status'            => 'AVAILABLE',
                'storage_location'  => $lokasi ?? 'Workshop',
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            if ($oldUnit) {
                $auditService = new \App\Services\ComponentAuditService($db);
                $auditService->logRemoval('CHARGER', $chargerId, $oldUnit, [
                    'notes' => $note,
                    'triggered_by' => 'REMOVE_FROM_UNIT',
                ]);
            }

            $db->transComplete();
            return $ok;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryChargerModel::removeFromUnit] ' . $e->getMessage());
            return false;
        }
    }
}
