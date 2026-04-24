<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * InventoryBatteryModel
 * 
 * Model untuk mengelola inventory batteries
 * Table: inventory_batteries
 * 
 * item_number: Lead Acid = B#####, Lithium = BL##### (lihat WarehousePO::inventoryBatteryItemNumberPrefix).
 */
class InventoryBatteryModel extends Model
{
    protected $table = 'inventory_batteries';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'item_number',
        'battery_type_id',
        'serial_number',
        'voltage',
        'ampere_hour',
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
        'item_number'       => 'permit_empty|max_length[50]|is_unique[inventory_batteries.item_number,id,{id}]',
        'battery_type_id'   => 'permit_empty|integer',
        'serial_number'     => 'permit_empty|max_length[100]|is_unique[inventory_batteries.serial_number,id,{id}]',
        'voltage'           => 'permit_empty|decimal',
        'ampere_hour'       => 'permit_empty|integer',
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
        'battery_type_id' => [
            'integer' => 'Battery Type ID harus berupa angka'
        ],
        'serial_number' => [
            'max_length' => 'Serial number tidak boleh lebih dari 100 karakter',
            'is_unique'  => 'Serial number sudah digunakan oleh baterai lain. SN harus unik.'
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
        $builder = $this->db->table($this->table . ' ib');
        
        // Select fields with proper aliases + add virtual columns for backward compatibility
        $builder->select('
            ib.*,
            ib.id as id_inventory_attachment,
            "battery" as tipe_item,
            b.merk_baterai,
            b.tipe_baterai,
            b.jenis_baterai,
            ib.serial_number as sn_baterai,
            COALESCE(iu.no_unit, iu.no_unit_na) as no_unit
        ');

        // Add unit number if linked to a unit
        $builder->join('inventory_unit iu', 'ib.inventory_unit_id = iu.id_inventory_unit', 'left');

        // Join with battery master table
        $builder->join('baterai b', 'ib.battery_type_id = b.id', 'left');

        // Filter by status if provided
        if (!empty($request['status_filter']) && $request['status_filter'] !== 'all') {
            $builder->where('ib.status', $request['status_filter']);
        }

        // Filter by voltage if provided
        if (!empty($request['model_filter'])) {
            $builder->where('ib.voltage', $request['model_filter']);
        }
        
        // Filter by chemistry type (Lead Acid vs Lithium-ion)
        if (!empty($request['chemistry_filter'])) {
            if ($request['chemistry_filter'] === 'lithium') {
                $builder->groupStart();
                $builder->like('b.jenis_baterai', 'LiFeP');
                $builder->orLike('b.jenis_baterai', 'Lithium');
                $builder->orLike('b.jenis_baterai', 'Li-ion');
                $builder->groupEnd();
            } elseif ($request['chemistry_filter'] === 'lead_acid') {
                // All batteries not explicitly marked as Lithium are Lead Acid
                $builder->notLike('b.jenis_baterai', 'LiFeP');
                $builder->notLike('b.jenis_baterai', 'Lithium');
                $builder->notLike('b.jenis_baterai', 'Li-ion');
            }
        }

        // Search functionality
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart();
            $builder->like('ib.item_number', $searchValue);
            $builder->orLike('ib.serial_number', $searchValue);
            $builder->orLike('ib.voltage', $searchValue);
            $builder->orLike('ib.ampere_hour', $searchValue);
            $builder->orLike('ib.physical_condition', $searchValue);
            $builder->orLike('ib.storage_location', $searchValue);
            $builder->orLike('ib.status', $searchValue);
            $builder->orLike('ib.notes', $searchValue);
            // Search in joined tables
            $builder->orLike('b.merk_baterai', $searchValue);
            $builder->orLike('b.tipe_baterai', $searchValue);
            $builder->orLike('b.jenis_baterai', $searchValue);
            $builder->orLike('iu.no_unit', $searchValue);
            $builder->groupEnd();
        }

        // Get total records before pagination
        $totalFiltered = $builder->countAllResults(false);

        // Ordering
        if (!empty($request['order'])) {
            $orderMap = [
                0 => 'ib.id',
                1 => 'ib.item_number',
                2 => 'b.merk_baterai',
                3 => 'b.tipe_baterai',
                4 => 'ib.serial_number',
                5 => 'ib.physical_condition',
                6 => 'ib.status',
                7 => 'ib.storage_location'
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
     * Get battery statistics — single query (no N+1)
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
     * Get battery detail with related data
     */
    public function getBatteryDetail($id)
    {
        $builder = $this->db->table($this->table . ' ib');
        
        // Select battery fields
        $builder->select('ib.*, b.merk_baterai, b.tipe_baterai, b.jenis_baterai');
        
        // Add unit info
        $builder->select('COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, iu.serial_number as unit_serial_number');
        $builder->join('inventory_unit iu', 'ib.inventory_unit_id = iu.id_inventory_unit', 'left');

        // Join battery master
        $builder->join('baterai b', 'ib.battery_type_id = b.id', 'left');

        // Add PO info
        $builder->select('po.no_po, po.tanggal_po, po.status as po_status, s.nama_supplier');
        $builder->join('purchase_orders po', 'ib.purchase_order_id = po.id_po', 'left');
        $builder->join('suppliers s', 'po.supplier_id = s.id_supplier', 'left');

        $builder->where('ib.id', $id);
        
        $query = $builder->get();
        return $query->getRowArray();
    }

    /**
     * Check if a battery is available
     */
    public function isAvailable(int $batteryId): bool
    {
        $row = $this->select('status')->where('id', $batteryId)->first();
        if (!$row) return false;
        return $row['status'] === 'AVAILABLE';
    }

    /**
     * Get all batteries (AVAILABLE, SPARE, and USED) for SPK selection
     * USED batteries can be detached from workshop units and re-assigned
     */
    public function getAvailableBatteries(string $q = ''): array
    {
        $builder = $this->select('inventory_batteries.*, b.merk_baterai, b.tipe_baterai, b.jenis_baterai, 
                             iu.no_unit as installed_unit_no, iu.serial_number as installed_unit_sn, 
                             mu.merk_unit as installed_unit_merk, mu.model_unit as installed_unit_model,
                             iu.status_unit_id as installed_unit_status_id')
            ->join('baterai b', 'b.id = inventory_batteries.battery_type_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = inventory_batteries.inventory_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->groupStart()
                ->where('inventory_batteries.status', 'AVAILABLE')
                ->orGroupStart()
                    ->where('inventory_batteries.status', 'IN_USE')
                    ->whereIn('iu.status_unit_id', [1, 2, 3, 12]) // AVAILABLE_STOCK, NON_ASSET_STOCK, BOOKED, RETURNED
                ->groupEnd()
            ->groupEnd()
            ->where('inventory_batteries.battery_type_id IS NOT NULL')
            ->orderBy('inventory_batteries.status', 'ASC') // AVAILABLE first, then IN_USE
            ->orderBy('inventory_batteries.item_number', 'ASC');

        if ($q !== '') {
            $builder->groupStart()
                ->like('inventory_batteries.item_number', $q)
                ->orLike('inventory_batteries.serial_number', $q)
                ->orLike('b.merk_baterai', $q)
                ->orLike('b.tipe_baterai', $q)
                ->orLike('b.jenis_baterai', $q)
            ->groupEnd();
        }

        return $builder->findAll(150);
    }

    /**
     * Get unit's current battery info
     */
    public function getUnitBattery($unitId): ?array
    {
        return $this->select('inventory_batteries.*, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'b.id = inventory_batteries.battery_type_id', 'left')
            ->where('inventory_batteries.inventory_unit_id', $unitId)
            ->where('inventory_batteries.battery_type_id IS NOT NULL')
            ->first();
    }

    /**
     * Detach battery from unit and return to stock — ATOMIC transaction.
     */
    public function detachFromUnit($batteryId, $reason = 'Detached'): bool
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
                'SELECT id, inventory_unit_id FROM inventory_batteries WHERE id = ? FOR UPDATE',
                [$batteryId]
            )->getRowArray();

            $oldUnit = $row['inventory_unit_id'] ?? null;

            $ok = $this->update($batteryId, [
                'inventory_unit_id' => null,
                'status'            => $newStatus,
                'storage_location'  => $newLocation,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            if ($oldUnit) {
                $auditService = new \App\Services\ComponentAuditService($db);
                $auditService->logRemoval('BATTERY', $batteryId, $oldUnit, [
                    'notes' => $reason,
                    'triggered_by' => 'DETACH_FROM_UNIT',
                ]);
            }

            $db->transComplete();
            return $ok;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryBatteryModel::detachFromUnit] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Attach battery to unit (ELECTRIC units only)
     */
    public function attachToUnit($batteryId, $unitId, $unitNumber = null): bool
    {
        $db = \Config\Database::connect();
        
        // VALIDATION: Battery can only be installed on ELECTRIC units
        $unitInfo = $db->table('inventory_unit iu')
            ->select('COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, iu.departemen_id, d.nama_departemen')
            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
            ->where('iu.id_inventory_unit', $unitId)
            ->get()->getRowArray();
        
        if ($unitInfo) {
            $deptName = strtoupper($unitInfo['nama_departemen'] ?? '');
            
            // Check if unit is NOT electric (DIESEL or GASOLINE)
            if ($deptName === 'DIESEL' || $deptName === 'GASOLINE') {
                log_message('warning', "Battery cannot be installed on non-electric unit. Unit: {$unitInfo['no_unit']}, Department: {$deptName}");
                throw new \Exception("Battery hanya dapat dipasang pada unit ELECTRIC. Unit {$unitInfo['no_unit']} adalah {$deptName}.");
            }
        }
        
        return $this->update($batteryId, [
            'inventory_unit_id' => $unitId,
            'status' => 'IN_USE',
            'storage_location' => $unitNumber ? "Terpasang di Unit {$unitNumber}" : null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Assign battery to unit — ATOMIC with SELECT FOR UPDATE to prevent double-booking.
     * Replaces the old pattern that called attachToUnit() separately (causing a double-write).
     *
     * @throws \Exception if battery is unavailable or unit type is invalid
     */
    public function assignToUnit(int $batteryId, int $unitId, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        $db->transStart();

        try {
            // 1. Lock the row — prevents concurrent requests reading stale AVAILABLE status
            $battery = $db->query(
                'SELECT id, status FROM inventory_batteries WHERE id = ? FOR UPDATE',
                [$batteryId]
            )->getRowArray();

            if (!$battery) {
                $db->transRollback();
                throw new \Exception("Battery #{$batteryId} tidak ditemukan.");
            }

            if ($battery['status'] !== 'AVAILABLE') {
                $db->transRollback();
                throw new \Exception(
                    "Battery #{$batteryId} tidak tersedia (status: {$battery['status']}). " .
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
                    throw new \Exception("Battery hanya dapat dipasang pada unit ELECTRIC. Unit {$unitInfo['no_unit']} adalah {$deptName}.");
                }
            }

            // 3. Write
            $updated = $this->update($batteryId, [
                'inventory_unit_id' => $unitId,
                'status'            => 'IN_USE',
                'storage_location'  => $unitInfo ? "Terpasang di Unit {$unitInfo['no_unit']}" : null,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            // 4. Audit log
            $auditService = new \App\Services\ComponentAuditService($db);
            $auditService->logAssignment('BATTERY', $batteryId, $unitId, [
                'notes' => $note,
                'triggered_by' => 'ASSIGN_TO_UNIT',
            ]);

            $db->transComplete();
            return $updated;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryBatteryModel::assignToUnit] ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove battery from unit — ATOMIC transaction.
     */
    public function removeFromUnit(int $batteryId, ?string $lokasi = null, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        $db->transStart();

        try {
            $row = $db->query(
                'SELECT id, inventory_unit_id FROM inventory_batteries WHERE id = ? FOR UPDATE',
                [$batteryId]
            )->getRowArray();

            $oldUnit = $row['inventory_unit_id'] ?? null;

            $ok = $this->update($batteryId, [
                'inventory_unit_id' => null,
                'status'            => 'AVAILABLE',
                'storage_location'  => $lokasi ?? 'Workshop',
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            if ($oldUnit) {
                $auditService = new \App\Services\ComponentAuditService($db);
                $auditService->logRemoval('BATTERY', $batteryId, $oldUnit, [
                    'notes' => $note,
                    'triggered_by' => 'REMOVE_FROM_UNIT',
                ]);
            }

            $db->transComplete();
            return $ok;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryBatteryModel::removeFromUnit] ' . $e->getMessage());
            return false;
        }
    }
}
