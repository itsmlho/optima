<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * InventoryAttachmentModel
 * 
 * Model untuk mengelola inventory attachments (forklift attachments only)
 * Table: inventory_attachments
 * 
 * Virtual Column:
 * - item_number: Auto-generated format ATT-0001, ATT-0002, dst
 */
class InventoryAttachmentModel extends Model
{
    protected $table = 'inventory_attachments';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'item_number',
        'attachment_type_id',
        'serial_number',
        'max_capacity',
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
        'item_number'       => 'permit_empty|max_length[50]|is_unique[inventory_attachments.item_number,id,{id}]',
        'attachment_type_id'=> 'permit_empty|integer',
        'serial_number'     => 'permit_empty|max_length[100]',
        'max_capacity'      => 'permit_empty|max_length[50]',
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
        'attachment_type_id' => [
            'integer' => 'Attachment Type ID harus berupa angka'
        ],
        'serial_number' => [
            'max_length' => 'Serial number tidak boleh lebih dari 100 karakter'
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
        $builder = $this->db->table($this->table . ' ia');
        
        // Select fields with proper aliases + backward compat virtual columns
        $builder->select('
            ia.*,
            ia.id as id_inventory_attachment,
            "attachment" as tipe_item,
            ia.serial_number as sn_attachment,
            att.merk as attachment_merk,
            att.tipe as attachment_tipe,
            att.model as attachment_model,
            COALESCE(iu.no_unit, iu.no_unit_na) as no_unit
        ');

        // Add unit number if linked to a unit
        $builder->join('inventory_unit iu', 'ia.inventory_unit_id = iu.id_inventory_unit', 'left');

        // Join with attachment master table
        $builder->join('attachment att', 'ia.attachment_type_id = att.id_attachment', 'left');

        // Filter by status if provided
        if (!empty($request['status_filter']) && $request['status_filter'] !== 'all') {
            $builder->where('ia.status', $request['status_filter']);
        }

        // Search functionality
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart();
            $builder->like('ia.item_number', $searchValue);
            $builder->orLike('ia.serial_number', $searchValue);
            $builder->orLike('ia.max_capacity', $searchValue);
            $builder->orLike('ia.physical_condition', $searchValue);
            $builder->orLike('ia.storage_location', $searchValue);
            $builder->orLike('ia.status', $searchValue);
            $builder->orLike('ia.notes', $searchValue);
            // Search in joined tables
            $builder->orLike('att.merk', $searchValue);
            $builder->orLike('att.tipe', $searchValue);
            $builder->orLike('att.model', $searchValue);
            $builder->orLike('iu.no_unit', $searchValue);
            $builder->groupEnd();
        }

        // Get total records before pagination
        $totalFiltered = $builder->countAllResults(false);

        // Ordering
        if (!empty($request['order'])) {
            $orderMap = [
                0 => 'ia.id',
                1 => 'ia.item_number',
                2 => 'att.merk',
                3 => 'att.tipe',
                4 => 'ia.serial_number',
                5 => 'ia.physical_condition',
                6 => 'ia.status',
                7 => 'ia.storage_location'
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
     * Get attachment statistics — single query (no N+1)
     */
    public function getStats()
    {
        $row = $this->db->query("
            SELECT
                COUNT(*) as total,
                SUM(status = 'AVAILABLE')  as available,
                SUM(status = 'IN_USE')     as in_use,
                SUM(status = 'SPARE')      as spare,
                SUM(status = 'MAINTENANCE') as maintenance,
                SUM(status = 'BROKEN')     as broken
            FROM {$this->table}
        ")->getRowArray();

        return $row ?: [
            'total' => 0, 'available' => 0, 'in_use' => 0,
            'spare' => 0, 'maintenance' => 0, 'broken' => 0,
        ];
    }

    /**
     * Get attachment detail with related data
     */
    public function getAttachmentDetail($id)
    {
        $builder = $this->db->table($this->table . ' ia');
        
        // Select attachment fields
        $builder->select('ia.*, att.merk, att.tipe, att.model');
        
        // Add unit info
        $builder->select('COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, iu.serial_number as unit_serial_number');
        $builder->join('inventory_unit iu', 'ia.inventory_unit_id = iu.id_inventory_unit', 'left');

        // Join attachment master
        $builder->join('attachment att', 'ia.attachment_type_id = att.id_attachment', 'left');

        // Add PO info
        $builder->select('po.no_po, po.tanggal_po, po.status as po_status, s.nama_supplier');
        $builder->join('purchase_orders po', 'ia.purchase_order_id = po.id_po', 'left');
        $builder->join('suppliers s', 'po.supplier_id = s.id_supplier', 'left');

        $builder->where('ia.id', $id);
        
        $query = $builder->get();
        return $query->getRowArray();
    }

    /**
     * Check if an attachment is available
     */
    public function isAvailable(int $attachmentId): bool
    {
        $row = $this->select('status')->where('id', $attachmentId)->first();
        if (!$row) return false;
        return $row['status'] === 'AVAILABLE';
    }

    /**
     * Mark an attachment as used on the specified unit
     */
    public function markUsedOnUnit(int $attachmentId, string $unitLabel): bool
    {
        return (bool)$this->update($attachmentId, [
            'status' => 'IN_USE',
            'storage_location' => 'Digunakan pada unit ' . $unitLabel,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Assign attachment to unit — ATOMIC with SELECT FOR UPDATE to prevent double-booking.
     *
     * @throws \Exception if attachment is unavailable
     */
    public function assignToUnit(int $attachmentId, int $unitId, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        $db->transStart();

        try {
            // Lock the row first
            $attachment = $db->query(
                'SELECT id, status FROM inventory_attachments WHERE id = ? FOR UPDATE',
                [$attachmentId]
            )->getRowArray();

            if (!$attachment) {
                $db->transRollback();
                throw new \Exception("Attachment #{$attachmentId} tidak ditemukan.");
            }

            if ($attachment['status'] !== 'AVAILABLE') {
                $db->transRollback();
                throw new \Exception(
                    "Attachment #{$attachmentId} tidak tersedia (status: {$attachment['status']}). " .
                    "Kemungkinan sudah dipasang ke unit lain secara bersamaan."
                );
            }

            $updated = $this->update($attachmentId, [
                'inventory_unit_id' => $unitId,
                'status'            => 'IN_USE',
                'storage_location'  => null,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            $auditService = new \App\Services\ComponentAuditService($db);
            $auditService->logAssignment('ATTACHMENT', $attachmentId, $unitId, [
                'notes' => $note,
                'triggered_by' => 'ASSIGN_TO_UNIT',
            ]);

            $db->transComplete();
            return $updated;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryAttachmentModel::assignToUnit] ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Remove attachment from unit — ATOMIC transaction.
     */
    public function removeFromUnit(int $attachmentId, ?string $lokasi = null, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        $db->transStart();

        try {
            $row = $db->query(
                'SELECT id, inventory_unit_id FROM inventory_attachments WHERE id = ? FOR UPDATE',
                [$attachmentId]
            )->getRowArray();

            $oldUnit = $row['inventory_unit_id'] ?? null;

            $ok = $this->update($attachmentId, [
                'inventory_unit_id' => null,
                'status'            => 'AVAILABLE',
                'storage_location'  => $lokasi,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            if ($oldUnit) {
                $auditService = new \App\Services\ComponentAuditService($db);
                $auditService->logRemoval('ATTACHMENT', $attachmentId, $oldUnit, [
                    'notes' => $note,
                    'triggered_by' => 'REMOVE_FROM_UNIT',
                ]);
            }

            $db->transComplete();
            return $ok;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryAttachmentModel::removeFromUnit] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get available attachments by type
     */
    public function getAvailableForAttachment(int $attachmentTypeId): array
    {
        return $this->select('inventory_attachments.*, att.tipe, att.merk, att.model, COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
            ->join('attachment att', 'att.id_attachment = inventory_attachments.attachment_type_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = inventory_attachments.inventory_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where('inventory_attachments.attachment_type_id', $attachmentTypeId)
            ->where('inventory_attachments.status', 'AVAILABLE')
            ->where('inventory_attachments.attachment_type_id IS NOT NULL')
            ->orderBy('inventory_attachments.received_at','ASC')
            ->findAll(100);
    }

    /**
     * Get unit's current attachment info
     */
    public function getUnitAttachment($unitId): ?array
    {
        return $this->select('inventory_attachments.*, att.tipe, att.merk, att.model')
            ->join('attachment att', 'att.id_attachment = inventory_attachments.attachment_type_id', 'left')
            ->where('inventory_attachments.inventory_unit_id', $unitId)
            ->where('inventory_attachments.attachment_type_id IS NOT NULL')
            ->whereIn('inventory_attachments.status', ['IN_USE', 'SPARE'])
            ->first();
    }

    /**
     * Detach attachment from unit and return to stock — ATOMIC transaction.
     */
    public function detachFromUnit($attachmentId, $reason = 'Detached'): bool
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
                'SELECT id, inventory_unit_id FROM inventory_attachments WHERE id = ? FOR UPDATE',
                [$attachmentId]
            )->getRowArray();

            $oldUnit = $row['inventory_unit_id'] ?? null;

            $ok = $this->update($attachmentId, [
                'inventory_unit_id' => null,
                'status'            => $newStatus,
                'storage_location'  => $newLocation,
                'updated_at'        => date('Y-m-d H:i:s'),
            ]);

            if ($oldUnit) {
                $auditService = new \App\Services\ComponentAuditService($db);
                $auditService->logRemoval('ATTACHMENT', $attachmentId, $oldUnit, [
                    'notes' => $reason,
                    'triggered_by' => 'DETACH_FROM_UNIT',
                ]);
            }

            $db->transComplete();
            return $ok;
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', '[InventoryAttachmentModel::detachFromUnit] ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Attach to unit
     */
    public function attachToUnit($attachmentId, $unitId, $unitNumber = null): bool
    {
        return $this->update($attachmentId, [
            'inventory_unit_id' => $unitId,
            'status' => 'IN_USE',
            'storage_location' => $unitNumber ? "Terpasang di Unit {$unitNumber}" : null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
