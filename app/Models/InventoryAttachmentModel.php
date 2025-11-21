<?php

namespace App\Models;

use CodeIgniter\Model;

class InventoryAttachmentModel extends Model
{
    protected $table = 'inventory_attachment';
    protected $primaryKey = 'id_inventory_attachment';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'tipe_item',
        'po_id',
        'attachment_id','sn_attachment',
        'baterai_id','sn_baterai',
        'charger_id','sn_charger',
        'id_inventory_unit','status_unit','lokasi_penyimpanan',
        'kondisi_fisik','kelengkapan','tanggal_masuk','catatan_inventory',
        'attachment_status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Validation
    protected $validationRules = [
    'po_id'            => 'required|integer',
    'id_inventory_unit'=> 'permit_empty|integer',
    'attachment_id'    => 'permit_empty|integer',
    'sn_attachment'    => 'permit_empty|max_length[255]',
    'baterai_id'       => 'permit_empty|integer',
    'sn_baterai'       => 'permit_empty|max_length[255]',
    'charger_id'       => 'permit_empty|integer',
    'sn_charger'       => 'permit_empty|max_length[255]',
    'kondisi_fisik'    => 'permit_empty',
    'kelengkapan'      => 'permit_empty',
    'catatan_fisik'    => 'permit_empty',
    'lokasi_penyimpanan'=> 'permit_empty|max_length[255]',
    'status_unit'      => 'permit_empty|integer',
    'tanggal_masuk'    => 'permit_empty|valid_date',
    'catatan_inventory'=> 'permit_empty'
    ];

    protected $validationMessages = [
        'po_id' => [
            'required' => 'PO ID wajib diisi',
            'integer' => 'PO ID harus berupa angka'
        ],
        'attachment_id' => [
            'required' => 'Attachment ID wajib diisi',
            'integer' => 'Attachment ID harus berupa angka'
        ],
        'sn_attachment' => [
            'max_length' => 'Serial number attachment tidak boleh lebih dari 255 karakter'
        ],
        'sn_charger' => [
            'max_length' => 'Serial number charger tidak boleh lebih dari 255 karakter'
        ],
        'kondisi_fisik' => [
            'in_list' => 'Kondisi fisik harus salah satu dari: Baik, Rusak Ringan, Rusak Berat'
        ],
        'kelengkapan' => [
            'in_list' => 'Kelengkapan harus salah satu dari: Lengkap, Tidak Lengkap'
        ],
        'status_unit' => [
            'integer' => 'Status unit harus berupa angka'
        ],
        'lokasi_unit' => [
            'max_length' => 'Lokasi unit tidak boleh lebih dari 50 karakter'
        ],
        'kondisi_unit' => [
            'max_length' => 'Kondisi unit tidak boleh lebih dari 50 karakter'
        ],
        'tanggal_masuk' => [
            'valid_date' => 'Format tanggal masuk tidak valid'
        ]
    ];

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    /** Check if an attachment is available (attachment_status = 'AVAILABLE'). */
    public function isAvailable(int $inventoryAttachmentId): bool
    {
        $row = $this->select('attachment_status')->where('id_inventory_attachment', $inventoryAttachmentId)->first();
        if (!$row) return false;
        return $row['attachment_status'] === 'AVAILABLE';
    }

    /** Mark an attachment as used on the specified unit, recording a lokasi_penyimpanan note and setting attachment_status='USED'. */
    public function markUsedOnUnit(int $inventoryAttachmentId, string $unitLabel): bool
    {
        return (bool)$this->update($inventoryAttachmentId, [
            'attachment_status' => 'USED',
            'lokasi_penyimpanan' => 'Digunakan pada unit ' . $unitLabel,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert = [];
    protected $afterInsert = [];
    protected $beforeUpdate = [];
    protected $afterUpdate = [];
    protected $beforeFind = [];
    protected $afterFind = [];
    protected $beforeDelete = [];
    protected $afterDelete = [];

    /**
     * Check if tables exist in database
     */
    protected function checkTablesExist(): array
    {
        $db = \Config\Database::connect();
        $tables = [
            'status_unit' => false,
            'purchase_orders' => false,
            'suppliers' => false
        ];

        foreach (array_keys($tables) as $table) {
            if ($db->tableExists($table)) {
                $tables[$table] = true;
            }
        }

        return $tables;
    }

    /**
     * Get data for DataTables with server-side processing
     */
    public function getDataTable($request)
    {
        $tablesExist = $this->checkTablesExist();
        
        $builder = $this->db->table($this->table . ' ia');
        
        // Select fields with proper aliases
        $builder->select('
            ia.id_inventory_attachment,
            ia.tipe_item,
            ia.po_id,
            ia.id_inventory_unit,
            ia.attachment_id,
            ia.sn_attachment,
            ia.baterai_id,
            ia.sn_baterai,
            ia.charger_id,
            ia.sn_charger,
            ia.kondisi_fisik,
            ia.kelengkapan,
            ia.catatan_fisik,
            ia.lokasi_penyimpanan,
            ia.status_unit,
            ia.attachment_status,
            ia.tanggal_masuk,
            ia.catatan_inventory,
            ia.created_at,
            ia.updated_at,
            a.merk as attachment_merk,
            a.tipe as attachment_tipe,
            a.model as attachment_model,
            b.merk_baterai,
            b.tipe_baterai,
            b.jenis_baterai,
            c.merk_charger,
            c.tipe_charger
        ');

        // Add status_unit_name if status_unit table exists
        if ($tablesExist['status_unit']) {
            $builder->select('COALESCE(su.status_unit, "Unknown") as status_unit_name');
            $builder->join('status_unit su', 'ia.status_unit = su.id_status', 'left');
        } else {
            $builder->select('
                CASE 
                    WHEN ia.status_unit = 1 THEN "WORKSHOP-HIDUP"
                    WHEN ia.status_unit = 2 THEN "WORKSHOP-RUSAK"
                    WHEN ia.status_unit = 3 THEN "RENTAL"
                    WHEN ia.status_unit = 4 THEN "UNIT PULANG"
                    WHEN ia.status_unit = 5 THEN "UNIT HARIAN"
                    WHEN ia.status_unit = 6 THEN "BOOKING"
                    WHEN ia.status_unit = 7 THEN "STOCK ASET"
                    WHEN ia.status_unit = 8 THEN "STOCK NON ASET"
                    WHEN ia.status_unit = 9 THEN "JUAL"
                    ELSE "Unknown"
                END as status_unit_name
            ');
        }

        // Add unit number if linked to a unit
        $builder->select('iu.no_unit');
        $builder->join('inventory_unit iu', 'ia.id_inventory_unit = iu.id_inventory_unit', 'left');

        // Join with attachment, charger, and baterai tables
        $builder->join('attachment a', 'ia.attachment_id = a.id_attachment', 'left');
        $builder->join('charger c', 'ia.charger_id = c.id_charger', 'left');
        $builder->join('baterai b', 'ia.baterai_id = b.id', 'left');

        // Filter by tipe_item if provided
        if (!empty($request['tipe_item'])) {
            $builder->where('ia.tipe_item', $request['tipe_item']);
        }

        // Filter by status if provided
        if (!empty($request['status_unit'])) {
            $builder->where('ia.status_unit', $request['status_unit']);
        }

        // Filter by attachment_status if provided (for tab filter)
        if (!empty($request['status_filter']) && $request['status_filter'] !== 'all') {
            $builder->where('ia.attachment_status', $request['status_filter']);
        }

        // Search functionality
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart();
            $builder->like('ia.sn_attachment', $searchValue);
            $builder->orLike('ia.sn_charger', $searchValue);
            $builder->orLike('ia.kondisi_fisik', $searchValue);
            $builder->orLike('ia.kelengkapan', $searchValue);
            $builder->orLike('ia.lokasi_penyimpanan', $searchValue);
            $builder->orLike('ia.catatan_inventory', $searchValue);
            
            if ($tablesExist['status_unit']) {
                $builder->orLike('su.status_unit', $searchValue);
            }
            
            $builder->groupEnd();
        }

        // Get total records before pagination
        $totalFiltered = $builder->countAllResults(false);

        // Ordering
        if (!empty($request['order'])) {
            $orderMap = [
                0 => 'ia.id_inventory_attachment', // ID
                1 => 'ia.tipe_item',               // Tipe Item
                2 => 'a.merk',                     // Merk (attachment.merk)
                3 => 'a.tipe',                     // Tipe (attachment.tipe)
                4 => 'b.jenis_baterai',            // Jenis (only for battery)
                5 => 'a.model',                    // Model (only for attachment)
                6 => 'ia.sn_attachment',           // SN (dynamic based on tipe_item)
                7 => 'ia.kondisi_fisik',           // Kondisi Fisik
                8 => 'ia.attachment_status',       // Status
                9 => 'ia.lokasi_penyimpanan'       // Lokasi
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
     * Get total count of filtered records
     */
    public function countFiltered($request)
    {
        $tablesExist = $this->checkTablesExist();
        
        $builder = $this->db->table($this->table . ' ia');

        // Join status_unit if exists
        if ($tablesExist['status_unit']) {
            $builder->join('status_unit su', 'ia.status_unit = su.id_status', 'left');
        }

        // Filter by status if provided
        if (!empty($request['status_unit'])) {
            $builder->where('ia.status_unit', $request['status_unit']);
        }

        // Search functionality
        if (!empty($request['search']['value'])) {
            $searchValue = $request['search']['value'];
            $builder->groupStart();
            $builder->like('ia.serial_number_po', $searchValue);
            $builder->orLike('ia.merk_attachment', $searchValue);
            $builder->orLike('ia.model_attachment', $searchValue);
            $builder->orLike('ia.tipe_attachment', $searchValue);
            $builder->orLike('ia.lokasi_unit', $searchValue);
            $builder->orLike('ia.kondisi_unit', $searchValue);
            
            if ($tablesExist['status_unit']) {
                $builder->orLike('su.status_unit', $searchValue);
            }
            
            $builder->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get total count of all records
     */
    public function countAll()
    {
        return $this->countAllResults();
    }

    /**
     * Get attachment statistics
     */
    public function getStats()
    {
        $stats = [
            'total' => $this->countAll(),
            'in_stock' => $this->where('status_unit', 7)->countAllResults(false),
            'rented' => $this->where('status_unit', 3)->countAllResults(false),
            'sold' => $this->where('status_unit', 9)->countAllResults(false)
        ];

        return $stats;
    }

    /**
     * Get attachment detail with related data
     */
    public function getAttachmentDetail($id)
    {
        $tablesExist = $this->checkTablesExist();
        
        $builder = $this->db->table($this->table . ' ia');
        
        // Select attachment fields
        $builder->select('
            ia.*
        ');

        // Add status name if status_unit table exists
        if ($tablesExist['status_unit']) {
            $builder->select('su.status_unit as status_unit_name');
            $builder->join('status_unit su', 'ia.status_unit = su.id_status', 'left');
        } else {
            $builder->select('
                CASE 
                    WHEN ia.status_unit = 1 THEN "WORKSHOP-HIDUP"
                    WHEN ia.status_unit = 2 THEN "WORKSHOP-RUSAK"
                    WHEN ia.status_unit = 3 THEN "RENTAL"
                    WHEN ia.status_unit = 4 THEN "UNIT PULANG"
                    WHEN ia.status_unit = 5 THEN "UNIT HARIAN"
                    WHEN ia.status_unit = 6 THEN "BOOKING"
                    WHEN ia.status_unit = 7 THEN "STOCK ASET"
                    WHEN ia.status_unit = 8 THEN "STOCK NON ASET"
                    WHEN ia.status_unit = 9 THEN "JUAL"
                    ELSE "Unknown"
                END as status_unit_name
            ');
        }

        // Add PO and supplier info if tables exist
        if ($tablesExist['purchase_orders']) {
            $builder->select('po.no_po, po.tanggal_po, po.status');
            $builder->join('purchase_orders po', 'ia.po_id = po.id_po', 'left');
            
            if ($tablesExist['suppliers']) {
                $builder->select('s.nama_supplier');
                $builder->join('suppliers s', 'po.supplier_id = s.id_supplier', 'left');
            }
        }

        $builder->where('ia.id_inventory_attachment', $id);
        
        $query = $builder->get();
        return $query->getRowArray();
    }

    public function assignToUnit(int $invAttachmentId, int $unitId, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        return $db->transException(true)->transStart()
            && $this->update($invAttachmentId, [
                'id_inventory_unit' => $unitId,
                'attachment_status' => 'USED', // Using new ENUM status
                'lokasi_penyimpanan' => null,
            ])
            && $db->table('inventory_item_unit_log')->insert([
                'id_inventory_attachment' => $invAttachmentId,
                'id_inventory_unit'      => $unitId,
                'action'                 => 'assign',
                'user_id'                => $userId,
                'note'                   => $note,
            ])
            && $db->transComplete();
    }

    public function removeFromUnit(int $invAttachmentId, ?string $lokasi = null, ?int $userId = null, ?string $note = null): bool
    {
        $db = $this->db;
        // ambil unit lama untuk log
        $row = $this->select('id_inventory_unit')->find($invAttachmentId);
        $oldUnit = $row['id_inventory_unit'] ?? null;

        return $db->transException(true)->transStart()
            && $this->update($invAttachmentId, [
                'id_inventory_unit' => null,
                'attachment_status' => 'AVAILABLE', // Using new ENUM status
                'lokasi_penyimpanan'=> $lokasi,
            ])
            && ($oldUnit ? $db->table('inventory_item_unit_log')->insert([
                'id_inventory_attachment' => $invAttachmentId,
                'id_inventory_unit'      => $oldUnit,
                'action'                 => 'remove',
                'user_id'                => $userId,
                'note'                   => $note,
            ]) : true)
            && $db->transComplete();
    }

    public function getAvailableForAttachment(int $attachmentId): array
    {
        return $this->select('inventory_attachment.*, a.tipe, a.merk, a.model, iu.no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
            ->join('attachment a', 'a.id_attachment = inventory_attachment.attachment_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = inventory_attachment.id_inventory_unit', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where([
                'inventory_attachment.attachment_id' => $attachmentId,
                'inventory_attachment.tipe_item'     => 'attachment',
            ])->whereIn('inventory_attachment.status_unit', [1, 11]) // available_stock (1) dan stock_non_aset (11)
              ->where('inventory_attachment.attachment_id IS NOT NULL')
              ->orderBy('inventory_attachment.tanggal_masuk','ASC')
              ->findAll(100);
    }

    public function getAvailableChargers(): array
    {
        // Ambil inventory yang punya charger_id dan tipe_item = 'charger'
        // Include AVAILABLE (status 1, 11) dan USED (untuk kanibal, hanya jika status_unit = 1 atau 11)
        return $this->select('inventory_attachment.*, c.merk_charger, c.tipe_charger, 
                             iu.no_unit as installed_unit_no, iu.serial_number as installed_unit_sn, 
                             mu.merk_unit as installed_unit_merk, mu.model_unit as installed_unit_model')
            ->join('charger c', 'c.id_charger = inventory_attachment.charger_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = inventory_attachment.id_inventory_unit', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where('inventory_attachment.tipe_item', 'charger')
            ->groupStart()
                ->where('inventory_attachment.attachment_status', 'AVAILABLE')
                ->orWhere('(inventory_attachment.attachment_status = "USED" AND inventory_attachment.status_unit IN (1, 11))')
            ->groupEnd()
            ->where('inventory_attachment.charger_id IS NOT NULL')
            ->orderBy('inventory_attachment.attachment_status', 'ASC') // AVAILABLE first, then USED
            ->orderBy('inventory_attachment.tanggal_masuk','ASC')
            ->findAll(100);
    }

    public function getAvailableBatteries(): array
    {
        // Ambil inventory yang punya baterai_id dan tipe_item = 'battery'
        // Include AVAILABLE (status 1, 11) dan USED (untuk kanibal, hanya jika status_unit = 1 atau 11)
        return $this->select('inventory_attachment.*, b.merk_baterai, b.tipe_baterai, b.jenis_baterai, 
                             iu.no_unit as installed_unit_no, iu.serial_number as installed_unit_sn, 
                             mu.merk_unit as installed_unit_merk, mu.model_unit as installed_unit_model')
            ->join('baterai b', 'b.id = inventory_attachment.baterai_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = inventory_attachment.id_inventory_unit', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where('inventory_attachment.tipe_item', 'battery')
            ->groupStart()
                ->where('inventory_attachment.attachment_status', 'AVAILABLE')
                ->orWhere('(inventory_attachment.attachment_status = "USED" AND inventory_attachment.status_unit IN (1, 11))')
            ->groupEnd()
            ->where('inventory_attachment.baterai_id IS NOT NULL')
            ->orderBy('inventory_attachment.attachment_status', 'ASC') // AVAILABLE first, then USED
            ->orderBy('inventory_attachment.tanggal_masuk','ASC')
            ->findAll(100);
    }

    /**
     * Get unit's current battery info
     */
    public function getUnitBattery($unitId): ?array
    {
        return $this->select('inventory_attachment.*, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'b.id = inventory_attachment.baterai_id', 'left')
            ->where('inventory_attachment.tipe_item', 'battery')
            ->where('inventory_attachment.id_inventory_unit', $unitId)
            ->where('inventory_attachment.baterai_id IS NOT NULL')
            ->first();
    }

    /**
     * Get unit's current charger info
     */
    public function getUnitCharger($unitId): ?array
    {
        return $this->select('inventory_attachment.*, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'c.id_charger = inventory_attachment.charger_id', 'left')
            ->where('inventory_attachment.tipe_item', 'charger')
            ->where('inventory_attachment.id_inventory_unit', $unitId)
            ->where('inventory_attachment.charger_id IS NOT NULL')
            ->first();
    }

    /**
     * Get unit's current attachment info
     */
    public function getUnitAttachment($unitId): ?array
    {
        return $this->select('inventory_attachment.*, a.tipe, a.merk, a.model')
            ->join('attachment a', 'a.id_attachment = inventory_attachment.attachment_id', 'left')
            ->where('inventory_attachment.tipe_item', 'attachment')
            ->where('inventory_attachment.id_inventory_unit', $unitId)
            ->where('inventory_attachment.attachment_id IS NOT NULL')
            ->whereIn('inventory_attachment.attachment_status', ['AVAILABLE', 'USED'])
            ->first();
    }

    /**
     * Detach battery/charger from unit and return to stock
     */
    public function detachFromUnit($attachmentId, $reason = 'Detached'): bool
    {
        // Tentukan status dan lokasi berdasarkan alasan
        $newStatus = 'AVAILABLE';
        $newLocation = 'Workshop';
        
        if (stripos($reason, 'rusak') !== false || stripos($reason, 'broken') !== false) {
            $newStatus = 'BROKEN';
        } elseif (stripos($reason, 'maintenance') !== false || stripos($reason, 'repair') !== false) {
            $newStatus = 'MAINTENANCE';
        }
        
        return $this->update($attachmentId, [
            'id_inventory_unit' => null,
            'attachment_status' => $newStatus,
            'lokasi_penyimpanan' => $newLocation,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        // Note: status_unit akan otomatis sinkronisasi dengan trigger (menjadi 7 = STOCK ASET)
    }

    /**
     * Attach battery/charger to unit
     */
    public function attachToUnit($attachmentId, $unitId, $unitNumber = null): bool
    {
        $locationNote = $unitNumber ? "Terpasang di Unit {$unitNumber}" : "Terpasang di Unit ID {$unitId}";
        
        return $this->update($attachmentId, [
            'id_inventory_unit' => $unitId,
            'attachment_status' => 'IN_USE', // Auto set to IN_USE when attached
            'lokasi_penyimpanan' => $locationNote,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        // Note: status_unit akan otomatis sinkronisasi dengan trigger berdasarkan unit status
    }

    /**
     * Swap attachment between units (untuk backup/emergency)
     * @param int $attachmentId - ID attachment yang akan dipindah
     * @param int $fromUnitId - Unit asal
     * @param int $toUnitId - Unit tujuan
     * @param string $reason - Alasan swap (backup, repair, dll)
     * @return bool
     */
    public function swapAttachmentBetweenUnits($attachmentId, $fromUnitId, $toUnitId, $reason = 'Swap for backup'): bool
    {
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {
            // Get unit numbers
            $fromUnit = $db->table('inventory_unit')
                ->select('no_unit')
                ->where('id_inventory_unit', $fromUnitId)
                ->get()->getRowArray();
                
            $toUnit = $db->table('inventory_unit')
                ->select('no_unit')
                ->where('id_inventory_unit', $toUnitId)
                ->get()->getRowArray();
            
            // Update attachment ke unit baru
            $this->update($attachmentId, [
                'id_inventory_unit' => $toUnitId,
                'attachment_status' => 'IN_USE', // Tetap IN_USE
                'lokasi_penyimpanan' => 'Terpasang di Unit ' . ($toUnit['no_unit'] ?? $toUnitId),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
            
            // Log swap activity
            $db->table('inventory_item_unit_log')->insert([
                'id_inventory_attachment' => $attachmentId,
                'id_inventory_unit' => $toUnitId,
                'action' => 'swap',
                'catatan' => "Swap from Unit {$fromUnit['no_unit']} to Unit {$toUnit['no_unit']}. Reason: {$reason}",
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            $db->transComplete();
            return $db->transStatus();
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Swap attachment error: ' . $e->getMessage());
            return false;
        }
    }
}