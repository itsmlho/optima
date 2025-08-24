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
        'po_id',
        'attachment_id','sn_attachment',
        'baterai_id','sn_baterai',
        'charger_id','sn_charger',
        'id_inventory_unit','status_unit','lokasi_penyimpanan',
        'kondisi_fisik','kelengkapan','tanggal_masuk','catatan_inventory',
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

    /** Check if an attachment is available (status_unit in [7,8] = stock). */
    public function isAvailable(int $inventoryAttachmentId): bool
    {
        $row = $this->select('status_unit')->where('id_inventory_attachment', $inventoryAttachmentId)->first();
        if (!$row) return false;
        return in_array((int)$row['status_unit'], [7,8], true);
    }

    /** Mark an attachment as used on the specified unit, recording a lokasi_penyimpanan note and setting status_unit=3. */
    public function markUsedOnUnit(int $inventoryAttachmentId, string $unitLabel): bool
    {
        return (bool)$this->update($inventoryAttachmentId, [
            'status_unit' => 3,
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
        
        // Select fields
        $builder->select('
            ia.id_inventory_attachment,
            ia.po_id,
            ia.attachment_id,
            ia.sn_attachment,
            ia.charger_id,
            ia.sn_charger,
            ia.kondisi_fisik,
            ia.kelengkapan,
            ia.catatan_fisik,
            ia.lokasi_penyimpanan,
            ia.status_unit,
            ia.tanggal_masuk,
            ia.catatan_inventory,
            ia.created_at,
            ia.updated_at
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

        // Filter by status if provided
        if (!empty($request['status_unit'])) {
            $builder->where('ia.status_unit', $request['status_unit']);
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
                0 => 'ia.id_inventory_attachment',
                1 => 'ia.sn_attachment',
                2 => 'ia.sn_charger',
                3 => 'ia.kondisi_fisik',
                4 => 'ia.kelengkapan',
                5 => 'status_unit_name',
                6 => 'ia.lokasi_penyimpanan',
                7 => 'ia.tanggal_masuk'
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
                'status_unit' => 3, // RENTAL
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
                'status_unit'       => 7, // STOCK ASET
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
        return $this->where([
            'attachment_id' => $attachmentId,
            'status_unit'   => 7,
        ])->where('id_inventory_unit', null)
          ->orderBy('tanggal_masuk','ASC')
          ->findAll(100);
    }

    public function getAvailableChargers(): array
    {
        // Ambil inventory yang punya charger_id, masih stock
        return $this->where('charger_id IS NOT NULL', null, false)
            ->where('status_unit', 7)
            ->where('id_inventory_unit', null)
            ->orderBy('tanggal_masuk','ASC')
            ->findAll(100);
    }
}