<?php

namespace App\Models;

use CodeIgniter\Model;

class UnitMovementModel extends Model
{
    protected $table            = 'unit_movements';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'movement_number',
        'unit_id',
        'component_id',
        'component_type',
        'origin_location',
        'destination_location',
        'destination_recipient_name',
        'origin_type',
        'destination_type',
        'movement_date',
        'driver_name',
        'vehicle_number',
        'vehicle_type',
        'notes',
        'surat_jalan_number',
        'status',
        'created_by_user_id',
        'confirmed_by_user_id',
        'confirmed_at',
        'verification_code',
        'movement_purpose',
    ];

    public const PURPOSE_INTERNAL_TRANSFER = 'INTERNAL_TRANSFER';
    public const PURPOSE_SCRAP_SALE        = 'SCRAP_SALE';

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules = [
        'movement_number'       => 'required|max_length[50]',
        'origin_location'      => 'required|max_length[100]',
        'destination_location' => 'required|max_length[100]',
        'destination_recipient_name' => 'permit_empty|max_length[120]',
        'origin_type'           => 'required|in_list[POS_1,POS_2,POS_3,POS_4,POS_5,CUSTOMER_SITE,WAREHOUSE,OTHER]',
        'destination_type'      => 'required|in_list[POS_1,POS_2,POS_3,POS_4,POS_5,CUSTOMER_SITE,WAREHOUSE,OTHER]',
        'movement_date'         => 'required|valid_date',
        'created_by_user_id'   => 'required|integer',
    ];

    private const LOCATION_SYNC_TYPES = ['FORKLIFT', 'ATTACHMENT', 'BATTERY', 'CHARGER', 'FORK'];

    private const CHECKPOINT_STATUS_MAP = [
        'BERANGKAT' => 'DEPARTED',
        'TRANSIT'   => 'TRANSIT_VERIFIED',
        'SAMPAI'    => 'ARRIVED',
        'DEPARTED'  => 'DEPARTED',
        'ARRIVED'   => 'ARRIVED',
    ];

    /**
     * Generate unique movement number
     */
    public function generateMovementNumber()
    {
        $prefix = 'MV';
        $date = date('Ymd');
        $prefixWithDate = $prefix . $date;

        $lastRecord = $this->select('movement_number')
                           ->like('movement_number', $prefixWithDate, 'after')
                           ->orderBy('id', 'DESC')
                           ->first();

        if ($lastRecord) {
            $lastNumber = (int)substr($lastRecord['movement_number'], -4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefixWithDate . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Surat Jalan Number
     */
    public function generateSuratJalanNumber()
    {
        $prefix = 'SJ';
        $date = date('Ym');
        $prefixWithDate = $prefix . $date;

        $lastRecord = $this->select('surat_jalan_number')
                           ->like('surat_jalan_number', $prefixWithDate, 'after')
                           ->where('surat_jalan_number IS NOT NULL')
                           ->orderBy('id', 'DESC')
                           ->first();

        if ($lastRecord) {
            $lastNumber = (int)substr($lastRecord['surat_jalan_number'], -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefixWithDate . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    public function generateVerificationCode(): string
    {
        return (string) random_int(100000, 999999);
    }

    /**
     * Get movements with unit and component info
     */
    public function getWithUnitInfo($filters = [])
    {
        $builder = $this->db->table('unit_movements um');
        $hasItemsTable = $this->db->tableExists('unit_movement_items');
        $hasStopsTable = $this->db->tableExists('unit_movement_stops');
        $hasCheckpointTable = $this->db->tableExists('unit_movement_checkpoints');
        $hasAttachmentTables = $this->db->tableExists('inventory_attachments') && $this->db->tableExists('attachment');
        $hasChargerTable = $this->db->tableExists('inventory_chargers');
        $hasBatteryTable = $this->db->tableExists('inventory_batteries');
        $hasForkTables = $this->db->tableExists('inventory_forks') && $this->db->tableExists('fork');
        $hasSparepartTables = $this->db->tableExists('inventory_spareparts') && $this->db->tableExists('sparepart');
        $extraSelect = "CASE WHEN um.unit_id IS NOT NULL OR um.component_id IS NOT NULL THEN 1 ELSE 0 END as total_items, 0 as total_stops, um.destination_location as last_stop_name, '' as last_checkpoint_status, NULL as last_checkpoint_at";
        if ($hasItemsTable || $hasStopsTable || $hasCheckpointTable) {
            $extraSelect = "IFNULL(mi_stats.total_items, CASE WHEN um.unit_id IS NOT NULL OR um.component_id IS NOT NULL THEN 1 ELSE 0 END) as total_items, IFNULL(stop_stats.total_stops, 0) as total_stops, IFNULL(stop_stats.last_stop_name, um.destination_location) as last_stop_name, IFNULL(cp_stats.last_checkpoint_status, '') as last_checkpoint_status, cp_stats.last_checkpoint_at";
        }
        $componentCase = [];
        if ($hasAttachmentTables) {
            $componentCase[] = "WHEN um.component_type = 'ATTACHMENT' THEN CONCAT(COALESCE(att.tipe,''), ' ', COALESCE(att.merk,''), ' ', COALESCE(att.model,''), ' [', COALESCE(ia.item_number,''), ']')";
        }
        if ($hasChargerTable) {
            $componentCase[] = "WHEN um.component_type = 'CHARGER' THEN CONCAT('Charger ', COALESCE(ic.item_number,''), ' SN:', COALESCE(ic.serial_number,''))";
        }
        if ($hasBatteryTable) {
            $componentCase[] = "WHEN um.component_type = 'BATTERY' THEN CONCAT('Battery ', COALESCE(ib.item_number,''), ' SN:', COALESCE(ib.serial_number,''))";
        }
        if ($hasForkTables) {
            $componentCase[] = "WHEN um.component_type = 'FORK' THEN CONCAT(COALESCE(fk.name,''), ' [', COALESCE(ifork.item_number,''), ']')";
        }
        if ($hasSparepartTables) {
            $componentCase[] = "WHEN um.component_type = 'SPAREPART' THEN CONCAT(COALESCE(sp.kode,''), ' - ', LEFT(COALESCE(sp.desc_sparepart,''), 40))";
        }
        $componentLabelSql = "''";
        if ($componentCase !== []) {
            $componentLabelSql = 'CASE ' . implode(' ', $componentCase) . " ELSE '' END";
        }

        $builder->select('um.*,
            iu.no_unit,
            iu.no_unit_na,
            iu.serial_number,
            mu.merk_unit,
            mu.model_unit,
            tu.tipe as tipe_unit,
            CONCAT(creator.first_name, \' \', COALESCE(creator.last_name, \'\')) as creator_name,
            CONCAT(confirmer.first_name, \' \', COALESCE(confirmer.last_name, \'\')) as confirmer_name,
            ' . $componentLabelSql . ' as component_label,
            ' . $extraSelect, false);

        $builder->join('inventory_unit iu', 'um.unit_id = iu.id_inventory_unit', 'left');
        $builder->join('model_unit mu', 'iu.model_unit_id = mu.id_model_unit', 'left');
        $builder->join('tipe_unit tu', 'iu.tipe_unit_id = tu.id_tipe_unit', 'left');
        $builder->join('users creator', 'um.created_by_user_id = creator.id', 'left');
        $builder->join('users confirmer', 'um.confirmed_by_user_id = confirmer.id', 'left');
        // Component joins (conditional via matching component_type)
        if ($hasAttachmentTables) {
            $builder->join('inventory_attachments ia', "ia.id = um.component_id AND um.component_type = 'ATTACHMENT'", 'left');
            $builder->join('attachment att', 'att.id_attachment = ia.attachment_type_id', 'left');
        }
        if ($hasChargerTable) {
            $builder->join('inventory_chargers ic', "ic.id = um.component_id AND um.component_type = 'CHARGER'", 'left');
        }
        if ($hasBatteryTable) {
            $builder->join('inventory_batteries ib', "ib.id = um.component_id AND um.component_type = 'BATTERY'", 'left');
        }
        if ($hasForkTables) {
            $builder->join('inventory_forks ifork', "ifork.id = um.component_id AND um.component_type = 'FORK'", 'left');
            $builder->join('fork fk', 'fk.id = ifork.fork_id', 'left');
        }
        if ($hasSparepartTables) {
            $builder->join('inventory_spareparts isp', "isp.id = um.component_id AND um.component_type = 'SPAREPART'", 'left');
            $builder->join('sparepart sp', 'sp.id_sparepart = isp.sparepart_id', 'left');
        }
        if ($hasItemsTable) {
            $builder->join('(SELECT movement_id, COUNT(*) AS total_items FROM unit_movement_items GROUP BY movement_id) mi_stats', 'mi_stats.movement_id = um.id', 'left');
        } else {
            $builder->join('(SELECT 0 as movement_id, 0 as total_items) mi_stats', 'mi_stats.movement_id = um.id', 'left');
        }
        if ($hasStopsTable) {
            $builder->join('(SELECT s1.movement_id, COUNT(*) AS total_stops, MAX(CASE WHEN s1.sequence_no = smax.max_seq THEN s1.location_name ELSE NULL END) AS last_stop_name FROM unit_movement_stops s1 JOIN (SELECT movement_id, MAX(sequence_no) AS max_seq FROM unit_movement_stops GROUP BY movement_id) smax ON smax.movement_id = s1.movement_id GROUP BY s1.movement_id) stop_stats', 'stop_stats.movement_id = um.id', 'left');
        } else {
            $builder->join('(SELECT 0 as movement_id, 0 as total_stops, NULL as last_stop_name) stop_stats', 'stop_stats.movement_id = um.id', 'left');
        }
        if ($hasCheckpointTable) {
            $builder->join('(SELECT c1.movement_id, MAX(c1.checkpoint_at) AS last_checkpoint_at, MAX(CASE WHEN c1.checkpoint_at = cmax.max_cp THEN c1.checkpoint_status ELSE NULL END) AS last_checkpoint_status FROM unit_movement_checkpoints c1 JOIN (SELECT movement_id, MAX(checkpoint_at) AS max_cp FROM unit_movement_checkpoints GROUP BY movement_id) cmax ON cmax.movement_id = c1.movement_id GROUP BY c1.movement_id) cp_stats', 'cp_stats.movement_id = um.id', 'left');
        } else {
            $builder->join('(SELECT 0 as movement_id, NULL as last_checkpoint_at, NULL as last_checkpoint_status) cp_stats', 'cp_stats.movement_id = um.id', 'left');
        }

        if (!empty($filters['status'])) {
            $builder->where('um.status', $filters['status']);
        }

        if (!empty($filters['origin_type'])) {
            $builder->where('um.origin_type', $filters['origin_type']);
        }

        if (!empty($filters['destination_type'])) {
            $builder->where('um.destination_type', $filters['destination_type']);
        }

        if (!empty($filters['unit_id'])) {
            $builder->where('um.unit_id', $filters['unit_id']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where('um.movement_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where('um.movement_date <=', $filters['date_to']);
        }

        $builder->orderBy('um.movement_date', 'DESC');
        $builder->orderBy('um.id', 'DESC');

        try {
            return $builder->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('error', '[UnitMovementModel::getWithUnitInfo] ' . $e->getMessage());
            // Safe fallback so list page remains usable.
            $fallback = $this->db->table('unit_movements um')->select('um.*')->orderBy('um.movement_date', 'DESC')->orderBy('um.id', 'DESC')->get()->getResultArray();
            foreach ($fallback as &$row) {
                $row['total_items'] = (int) (($row['unit_id'] ?? null) || ($row['component_id'] ?? null) ? 1 : 0);
                $row['last_stop_name'] = $row['destination_location'] ?? '-';
                $row['last_checkpoint_status'] = '';
                $row['component_label'] = '';
                $row['no_unit'] = null;
                $row['no_unit_na'] = null;
                $row['merk_unit'] = null;
            }
            return $fallback;
        }
    }

    public function createWithDetails(array $header, array $items, array $stops): int
    {
        $this->db->transStart();

        $movementId = (int) $this->insert($header, true);
        if ($movementId <= 0) {
            throw new \RuntimeException('Gagal membuat surat jalan.');
        }

        if ($this->db->tableExists('unit_movement_items')) {
            foreach ($items as $item) {
                $row = [
                    'movement_id'     => $movementId,
                    'component_type'  => strtoupper((string)($item['component_type'] ?? 'FORKLIFT')),
                    'unit_id'         => !empty($item['unit_id']) ? (int)$item['unit_id'] : null,
                    'component_id'    => !empty($item['component_id']) ? (int)$item['component_id'] : null,
                    'qty'             => max(1, (int)($item['qty'] ?? 1)),
                    'item_notes'      => trim((string)($item['item_notes'] ?? '')),
                    'created_at'      => date('Y-m-d H:i:s'),
                    'updated_at'      => date('Y-m-d H:i:s'),
                ];
                $this->db->table('unit_movement_items')->insert($row);
            }
        }

        if ($this->db->tableExists('unit_movement_stops')) {
            foreach ($stops as $index => $stop) {
                $this->db->table('unit_movement_stops')->insert([
                    'movement_id'    => $movementId,
                    'sequence_no'    => $index + 1,
                    'stop_type'      => strtoupper((string)($stop['stop_type'] ?? 'TRANSIT')),
                    'location_name'  => trim((string)($stop['location_name'] ?? '')),
                    'location_type'  => trim((string)($stop['location_type'] ?? 'OTHER')),
                    'eta_at'         => !empty($stop['eta_at']) ? $stop['eta_at'] : null,
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->db->transComplete();
        if (!$this->db->transStatus()) {
            throw new \RuntimeException('Transaksi pembuatan surat jalan gagal.');
        }

        return $movementId;
    }

    /**
     * Tambahkan preview daftar barang + rute untuk tampilan list (table ringkas).
     *
     * @param list<array<string,mixed>> $movements
     * @return list<array<string,mixed>>
     */
    public function appendListPreview(array $movements): array
    {
        if ($movements === []) {
            return $movements;
        }

        $movementIds = array_values(array_filter(array_map(
            static fn (array $row): int => (int) ($row['id'] ?? 0),
            $movements
        ), static fn (int $id): bool => $id > 0));

        if ($movementIds === []) {
            return $movements;
        }

        $itemLinesMap  = $this->buildItemPreviewLinesMap($movementIds);
        $routeLinesMap = $this->buildRoutePreviewLinesMap($movementIds);

        foreach ($movements as &$movement) {
            $movementId = (int) ($movement['id'] ?? 0);
            $movement['item_preview_lines'] = $itemLinesMap[$movementId] ?? [];
            $movement['route_preview_lines'] = $routeLinesMap[$movementId] ?? [];
        }
        unset($movement);

        return $movements;
    }

    /**
     * @param list<int> $movementIds
     * @return array<int, list<string>>
     */
    private function buildItemPreviewLinesMap(array $movementIds): array
    {
        $result = [];
        foreach ($movementIds as $movementId) {
            $result[$movementId] = [];
        }

        if ($this->db->tableExists('unit_movement_items')) {
            $rows = $this->db->table('unit_movement_items')
                ->select('id, movement_id, component_type, unit_id, component_id, qty, item_notes')
                ->whereIn('movement_id', $movementIds)
                ->orderBy('movement_id', 'ASC')
                ->orderBy('id', 'ASC')
                ->get()
                ->getResultArray();

            if ($rows !== []) {
                $rows = $this->enrichItemsForPrint($rows);
                $counter = [];
                foreach ($rows as $row) {
                    $movementId = (int) ($row['movement_id'] ?? 0);
                    if ($movementId <= 0) {
                        continue;
                    }
                    if (!isset($counter[$movementId])) {
                        $counter[$movementId] = 0;
                    }
                    $counter[$movementId]++;
                    $qty = max(1, (int) ($row['qty'] ?? 1));
                    $desc = trim((string) ($row['print_description'] ?? $row['component_type'] ?? '-'));
                    $result[$movementId][] = '#' . $counter[$movementId] . ' - ' . $desc . ' | qty ' . $qty;
                }
            }
        }

        return $result;
    }

    /**
     * @param list<int> $movementIds
     * @return array<int, list<string>>
     */
    private function buildRoutePreviewLinesMap(array $movementIds): array
    {
        $result = [];
        foreach ($movementIds as $movementId) {
            $result[$movementId] = [];
        }

        if (! $this->db->tableExists('unit_movement_stops')) {
            return $result;
        }

        $rows = $this->db->table('unit_movement_stops')
            ->select('movement_id, stop_type, location_name')
            ->whereIn('movement_id', $movementIds)
            ->orderBy('movement_id', 'ASC')
            ->orderBy('sequence_no', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($rows as $row) {
            $movementId = (int) ($row['movement_id'] ?? 0);
            if ($movementId <= 0) {
                continue;
            }

            $stopType = strtoupper((string) ($row['stop_type'] ?? ''));
            $stopLabel = match ($stopType) {
                'ORIGIN' => 'Asal',
                'DESTINATION' => 'Tujuan',
                'TRANSIT' => 'Transit',
                default => $stopType !== '' ? $stopType : '-',
            };

            $locationName = trim((string) ($row['location_name'] ?? '-'));
            $result[$movementId][] = $locationName . ' (' . $stopLabel . ')';
        }

        return $result;
    }

    /**
     * Get movements by unit
     */
    public function getByUnit($unitId)
    {
        return $this->getWithUnitInfo(['unit_id' => $unitId]);
    }

    /**
     * Get pending movements (in transit)
     */
    public function getInTransit()
    {
        return $this->getWithUnitInfo(['status' => 'IN_TRANSIT']);
    }

    /**
     * Konfirmasi tiba dari gudang: selaraskan dengan checkpoint + barang bila tersedia, supaya data sama dengan alur satpam.
     */
    public function confirmArrival($id, $userId)
    {
        $movement = $this->find($id);
        if (! $movement) {
            throw new \Exception('Movement tidak ditemukan');
        }

        $mid = (int) $id;
        $this->ensureStopsFromHeader($mid);
        $this->ensureItemsFromHeader($mid);

        $useCheckpointFlow = $this->db->tableExists('unit_movement_stops')
            && $this->db->tableExists('unit_movement_checkpoints')
            && $this->db->tableExists('unit_movement_items');
        $itemCount = $useCheckpointFlow
            ? (int) $this->db->table('unit_movement_items')->where('movement_id', $mid)->countAllResults()
            : 0;

        if ($useCheckpointFlow && $itemCount > 0) {
            return $this->confirmArrivalThroughCheckpoints($mid, (int) $userId, $movement);
        }

        return $this->confirmArrivalLegacy($mid, $userId, $movement);
    }

    /**
     * Lengkapi stop yang belum punya actual_at lalu ARRIVED di titik akhir — sinkron dengan submitCheckpoint satpam.
     */
    private function confirmArrivalThroughCheckpoints(int $movementId, int $userId, array $movement): bool
    {
        $stops = $this->db->table('unit_movement_stops')
            ->where('movement_id', $movementId)
            ->orderBy('sequence_no', 'ASC')
            ->get()
            ->getResultArray();

        if ($stops === []) {
            return $this->confirmArrivalLegacy($movementId, $userId, $movement);
        }

        $rows = $this->db->table('unit_movement_items')
            ->select('id')
            ->where('movement_id', $movementId)
            ->get()
            ->getResultArray();
        $itemIds = array_values(array_filter(array_map(static fn ($r) => (int) ($r['id'] ?? 0), $rows), static fn ($id) => $id > 0));
        if ($itemIds === []) {
            return $this->confirmArrivalLegacy($movementId, $userId, $movement);
        }

        $seqs   = array_map(static fn ($s) => (int) ($s['sequence_no'] ?? 0), $stops);
        $minSeq = min($seqs);
        $maxSeq = max($seqs);

        $baseMeta = [
            'verifier_phone'   => '',
            'checkpoint_at'    => date('Y-m-d H:i:s'),
            'checked_item_ids' => $itemIds,
            'dropped_item_ids' => [],
        ];

        foreach ($stops as $stop) {
            if (! empty($stop['actual_at'])) {
                continue;
            }
            $seq = (int) ($stop['sequence_no'] ?? 0);
            $sid = (int) ($stop['id'] ?? 0);
            if ($sid <= 0) {
                continue;
            }
            if ($seq === $minSeq) {
                $cpStatus = 'DEPARTED';
            } elseif ($seq === $maxSeq) {
                $cpStatus = 'ARRIVED';
            } else {
                $cpStatus = 'TRANSIT_VERIFIED';
            }
            $ok = $this->submitCheckpoint($movementId, $sid, $cpStatus, array_merge($baseMeta, [
                'verifier_name' => 'Gudang (aplikasi)',
                'notes'         => 'Pelengkapan rute — konfirmasi tiba dari aplikasi gudang',
            ]));
            if (! $ok) {
                throw new \Exception('Gagal menyelaraskan checkpoint surat jalan');
            }
        }

        $this->update($movementId, [
            'confirmed_by_user_id' => $userId,
            'updated_at'           => date('Y-m-d H:i:s'),
        ]);

        return true;
    }

    /**
     * Konfirmasi tiba versi lama (satu unit_id / component di header, tanpa baris items).
     */
    private function confirmArrivalLegacy(int $id, $userId, array $movement): bool
    {
        $this->update($id, [
            'status'               => 'ARRIVED',
            'confirmed_by_user_id' => $userId,
            'confirmed_at'         => date('Y-m-d H:i:s'),
        ]);

        if ($movement['unit_id']) {
            $unitModel = new \App\Models\InventoryUnitModel();
            $unitModel->update($movement['unit_id'], [
                'lokasi_unit' => $movement['destination_location'],
            ]);
            $this->writeAssetMovementLog('inventory_unit', (int) $movement['unit_id'], 'FORKLIFT', $movement['destination_location'], $movement);
        }

        if (! empty($movement['component_id']) && ! empty($movement['component_type'])) {
            $dest   = $movement['destination_location'];
            $compId = (int) $movement['component_id'];
            $db     = \Config\Database::connect();

            switch (strtoupper($movement['component_type'])) {
                case 'ATTACHMENT':
                    if ($db->tableExists('inventory_attachments')) {
                        $db->table('inventory_attachments')
                            ->where('id', $compId)
                            ->update(['storage_location' => $dest]);
                        $this->writeAssetMovementLog('inventory_attachments', $compId, 'ATTACHMENT', $dest, $movement);
                    }
                    break;
                case 'CHARGER':
                    if ($db->tableExists('inventory_chargers')) {
                        $db->table('inventory_chargers')
                            ->where('id', $compId)
                            ->update(['storage_location' => $dest]);
                        $this->writeAssetMovementLog('inventory_chargers', $compId, 'CHARGER', $dest, $movement);
                    }
                    break;
                case 'BATTERY':
                    if ($db->tableExists('inventory_batteries')) {
                        $db->table('inventory_batteries')
                            ->where('id', $compId)
                            ->update(['storage_location' => $dest]);
                        $this->writeAssetMovementLog('inventory_batteries', $compId, 'BATTERY', $dest, $movement);
                    }
                    break;
                case 'FORK':
                    if ($db->tableExists('inventory_forks')) {
                        $db->table('inventory_forks')
                            ->where('id', $compId)
                            ->update(['storage_location' => $dest]);
                        $this->writeAssetMovementLog('inventory_forks', $compId, 'FORK', $dest, $movement);
                    }
                    break;
            }
        }

        if ($this->db->fieldExists('movement_purpose', 'unit_movements')) {
            $purpose = strtoupper((string) ($movement['movement_purpose'] ?? self::PURPOSE_INTERNAL_TRANSFER));
            if ($purpose === self::PURPOSE_SCRAP_SALE) {
                $this->applyScrapSaleUnitsAfterArrival($id, array_merge($movement, ['id' => $id]), []);
            }
        }

        return true;
    }

    /**
     * Saat gudang klik "Jalankan": catat keberangkatan di checkpoint pertama agar selaras dengan form satpam.
     */
    public function recordDepartureFromWarehouse(int $movementId, string $driverName, string $vehicleNumber, string $notes): bool
    {
        $this->ensureStopsFromHeader($movementId);
        $this->ensureItemsFromHeader($movementId);

        if (! $this->db->tableExists('unit_movement_checkpoints') || ! $this->db->tableExists('unit_movement_stops')) {
            return false;
        }

        $first = $this->db->table('unit_movement_stops')
            ->where('movement_id', $movementId)
            ->orderBy('sequence_no', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (! $first || empty($first['id'])) {
            return false;
        }

        $rows = $this->db->table('unit_movement_items')
            ->select('id')
            ->where('movement_id', $movementId)
            ->get()
            ->getResultArray();
        $itemIds = array_values(array_filter(array_map(static fn ($r) => (int) ($r['id'] ?? 0), $rows), static fn ($id) => $id > 0));

        if ($itemIds === []) {
            return false;
        }

        $driverName = trim($driverName);
        $verifier   = $driverName !== '' ? $driverName . ' (gudang)' : 'Gudang (aplikasi)';
        $noteLine   = trim($notes);
        if ($noteLine === '') {
            $noteLine = 'Perjalanan dimulai dari aplikasi gudang';
        }
        if (trim($vehicleNumber) !== '') {
            $noteLine .= ' | No. kendaraan: ' . trim($vehicleNumber);
        }

        return $this->submitCheckpoint($movementId, (int) $first['id'], 'DEPARTED', [
            'verifier_name'    => $verifier,
            'verifier_phone'   => '',
            'notes'            => $noteLine,
            'checked_item_ids' => $itemIds,
            'dropped_item_ids' => [],
        ]);
    }

    /**
     * Cancel movement
     */
    public function cancelMovement($id)
    {
        return $this->update($id, ['status' => 'CANCELLED']);
    }

    /**
     * Get statistics
     */
    public function getStats()
    {
        $total = $this->countAllResults();

        $draft = $this->where('status', 'DRAFT')->countAllResults();
        $inTransit = $this->where('status', 'IN_TRANSIT')->countAllResults();
        $arrived = $this->where('status', 'ARRIVED')->countAllResults();
        $cancelled = $this->where('status', 'CANCELLED')->countAllResults();

        return [
            'total'      => $total,
            'draft'      => $draft,
            'in_transit'=> $inTransit,
            'arrived'    => $arrived,
            'cancelled'  => $cancelled,
        ];
    }

    public function getMovementDetailBundle(int $movementId): array
    {
        $movement = $this->find($movementId);
        if (!$movement) {
            return [];
        }

        $items = [];
        if ($this->db->tableExists('unit_movement_items')) {
            $items = $this->db->table('unit_movement_items i')
                ->select('i.*')
                ->where('i.movement_id', $movementId)
                ->orderBy('i.id', 'ASC')
                ->get()->getResultArray();
        }

        if (!$items) {
            $items = [[
                'component_type' => $movement['component_type'] ?? 'FORKLIFT',
                'unit_id'        => $movement['unit_id'] ?? null,
                'component_id'   => $movement['component_id'] ?? null,
                'qty'            => 1,
                'item_notes'     => null,
            ]];
        }

        $stops = [];
        if ($this->db->tableExists('unit_movement_stops')) {
            $stops = $this->db->table('unit_movement_stops')
                ->where('movement_id', $movementId)
                ->orderBy('sequence_no', 'ASC')
                ->get()->getResultArray();
        }
        if (!$stops) {
            $stops = [
                [
                    'id'            => null,
                    'sequence_no'   => 1,
                    'stop_type'     => 'ORIGIN',
                    'location_name' => $movement['origin_location'] ?? '-',
                    'location_type' => $movement['origin_type'] ?? 'OTHER',
                ],
                [
                    'id'            => null,
                    'sequence_no'   => 2,
                    'stop_type'     => 'DESTINATION',
                    'location_name' => $movement['destination_location'] ?? '-',
                    'location_type' => $movement['destination_type'] ?? 'OTHER',
                ],
            ];
        }

        $checkpoints = [];
        if ($this->db->tableExists('unit_movement_checkpoints')) {
            $checkpoints = $this->db->table('unit_movement_checkpoints')
                ->where('movement_id', $movementId)
                ->orderBy('checkpoint_at', 'ASC')
                ->get()->getResultArray();
        }

        return [
            'movement'    => $movement,
            'items'       => $items,
            'stops'       => $stops,
            'checkpoints' => $checkpoints,
        ];
    }

    public function findBySuratJalanNumber(string $sjNumber): ?array
    {
        $sjNumber = trim($sjNumber);
        if ($sjNumber === '') {
            return null;
        }

        $row = $this->where('surat_jalan_number', $sjNumber)->first();
        return $row ?: null;
    }

    /**
     * Jika belum ada baris di unit_movement_stops (data lama / migrasi), buat asal–tujuan dari header
     * agar form satpam punya stop_id valid untuk checkpoint.
     */
    public function ensureStopsFromHeader(int $movementId): void
    {
        if (! $this->db->tableExists('unit_movement_stops')) {
            return;
        }
        $n = (int) $this->db->table('unit_movement_stops')->where('movement_id', $movementId)->countAllResults();
        if ($n > 0) {
            return;
        }
        $m = $this->find($movementId);
        if (! $m) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $this->db->table('unit_movement_stops')->insert([
            'movement_id'   => $movementId,
            'sequence_no'   => 1,
            'stop_type'     => 'ORIGIN',
            'location_name' => (string) ($m['origin_location'] ?? '-'),
            'location_type' => (string) ($m['origin_type'] ?? 'OTHER'),
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
        $this->db->table('unit_movement_stops')->insert([
            'movement_id'   => $movementId,
            'sequence_no'   => 2,
            'stop_type'     => 'DESTINATION',
            'location_name' => (string) ($m['destination_location'] ?? '-'),
            'location_type' => (string) ($m['destination_type'] ?? 'OTHER'),
            'created_at'    => $now,
            'updated_at'    => $now,
        ]);
    }

    /**
     * Backfill satu baris unit_movement_items dari header bila tabel ada tapi kosong (SJ lama).
     */
    public function ensureItemsFromHeader(int $movementId): void
    {
        if (! $this->db->tableExists('unit_movement_items')) {
            return;
        }
        $n = (int) $this->db->table('unit_movement_items')->where('movement_id', $movementId)->countAllResults();
        if ($n > 0) {
            return;
        }
        $m = $this->find($movementId);
        if (! $m) {
            return;
        }
        $now = date('Y-m-d H:i:s');
        $this->db->table('unit_movement_items')->insert([
            'movement_id'    => $movementId,
            'component_type' => strtoupper((string) ($m['component_type'] ?? 'FORKLIFT')),
            'unit_id'        => ! empty($m['unit_id']) ? (int) $m['unit_id'] : null,
            'component_id'   => ! empty($m['component_id']) ? (int) $m['component_id'] : null,
            'qty'            => 1,
            'item_notes'     => null,
            'created_at'     => $now,
            'updated_at'     => $now,
        ]);
    }

    /**
     * Isi actual_at pada stop dari checkpoint yang sudah ada (perbaiki drift UI vs DB).
     * Legacy: status IN_TRANSIT tanpa checkpoint sama sekali → anggap titik pertama (asal) sudah lewat.
     */
    public function synchronizeStopActualFromCheckpoints(int $movementId): void
    {
        if (! $this->db->tableExists('unit_movement_stops')) {
            return;
        }

        $movement = $this->find($movementId);
        if (! $movement) {
            return;
        }

        if ($this->db->tableExists('unit_movement_checkpoints')) {
            $stops = $this->db->table('unit_movement_stops')
                ->where('movement_id', $movementId)
                ->get()
                ->getResultArray();

            foreach ($stops as $stop) {
                if (! empty($stop['actual_at'])) {
                    continue;
                }
                $sid = (int) ($stop['id'] ?? 0);
                if ($sid <= 0) {
                    continue;
                }
                $row = $this->db->table('unit_movement_checkpoints')
                    ->selectMax('checkpoint_at', 'last_at')
                    ->where('movement_id', $movementId)
                    ->where('stop_id', $sid)
                    ->get()
                    ->getRowArray();
                $lastAt = $row['last_at'] ?? null;
                if ($lastAt !== null && $lastAt !== '') {
                    $this->db->table('unit_movement_stops')->where('id', $sid)->update([
                        'actual_at'  => $lastAt,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }
            }
        }

        if (! $this->db->tableExists('unit_movement_checkpoints')) {
            return;
        }

        $cpCount = (int) $this->db->table('unit_movement_checkpoints')->where('movement_id', $movementId)->countAllResults();
        if ($cpCount > 0) {
            return;
        }

        if (strtoupper((string) ($movement['status'] ?? '')) !== 'IN_TRANSIT') {
            return;
        }

        $first = $this->db->table('unit_movement_stops')
            ->where('movement_id', $movementId)
            ->orderBy('sequence_no', 'ASC')
            ->limit(1)
            ->get()
            ->getRowArray();

        if (! $first || ! empty($first['actual_at'])) {
            return;
        }

        $ts = ! empty($movement['movement_date']) ? (string) $movement['movement_date'] : (string) ($movement['updated_at'] ?? date('Y-m-d H:i:s'));

        $this->db->table('unit_movement_stops')->where('id', (int) $first['id'])->update([
            'actual_at'  => $ts,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Keterangan baris untuk cetak / tampilan satpam.
     *
     * @param list<array<string, mixed>> $items
     *
     * @return list<array<string, mixed>>
     */
    public function enrichItemsForPrint(array $items): array
    {
        $out = [];
        foreach ($items as $it) {
            $row                 = $it;
            $type                = strtoupper((string) ($it['component_type'] ?? ''));
            $printDescription    = $type;
            $uid                 = (int) ($it['unit_id'] ?? 0);
            $cid                 = (int) ($it['component_id'] ?? 0);

            if ($type === 'FORKLIFT' && $uid > 0) {
                $u = $this->db->table('inventory_unit iu')
                    ->select('COALESCE(iu.no_unit, iu.no_unit_na) as no_unit, iu.serial_number, mu.merk_unit, mu.model_unit')
                    ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
                    ->where('iu.id_inventory_unit', $uid)
                    ->get()->getRowArray();
                if ($u) {
                    $mm   = trim((string) ($u['merk_unit'] ?? '') . ' ' . (string) ($u['model_unit'] ?? ''));
                    $printDescription = trim($mm . ' | No: ' . ($u['no_unit'] ?? '-') . ' | SN: ' . ($u['serial_number'] ?? '-'));
                } else {
                    $printDescription = 'Forklift / Unit (id ' . $uid . ')';
                }
            } elseif ($cid > 0 && $this->db->tableExists('inventory_attachments') && $type === 'ATTACHMENT') {
                $a = $this->db->table('inventory_attachments ia')
                    ->select('ia.item_number, att.merk, att.model, att.tipe')
                    ->join('attachment att', 'att.id_attachment = ia.attachment_type_id', 'left')
                    ->where('ia.id', $cid)
                    ->get()->getRowArray();
                $printDescription = $a
                    ? trim(($a['tipe'] ?? '') . ' ' . ($a['merk'] ?? '') . ' ' . ($a['model'] ?? '') . ' [' . ($a['item_number'] ?? '') . ']')
                    : 'Attachment #' . $cid;
            } elseif ($cid > 0 && $this->db->tableExists('inventory_chargers') && $type === 'CHARGER') {
                $c = $this->db->table('inventory_chargers')->select('item_number, serial_number')->where('id', $cid)->get()->getRowArray();
                $printDescription = $c ? ('Charger ' . ($c['item_number'] ?? '') . ' SN:' . ($c['serial_number'] ?? '')) : 'Charger #' . $cid;
            } elseif ($cid > 0 && $this->db->tableExists('inventory_batteries') && $type === 'BATTERY') {
                $b = $this->db->table('inventory_batteries')->select('item_number, serial_number')->where('id', $cid)->get()->getRowArray();
                $printDescription = $b ? ('Battery ' . ($b['item_number'] ?? '') . ' SN:' . ($b['serial_number'] ?? '')) : 'Battery #' . $cid;
            } elseif ($type === 'OTHERS') {
                $notes = trim((string) ($it['item_notes'] ?? ''));
                $printDescription = $notes !== '' ? $notes : 'Others';
            } else {
                $printDescription = $type . ($cid > 0 ? ' #' . $cid : '') . ($uid > 0 ? ' (unit ' . $uid . ')' : '');
            }

            $row['print_description'] = $printDescription;
            $out[]                    = $row;
        }

        return $out;
    }

    /**
     * Bundle siap cetak: pastikan stop DB + keterangan barang.
     *
     * @return array{movement: array, items: list, stops: list, checkpoints: list}|null
     */
    public function getMovementPrintBundle(int $movementId): ?array
    {
        $this->ensureStopsFromHeader($movementId);
        $this->ensureItemsFromHeader($movementId);
        $bundle = $this->getMovementDetailBundle($movementId);
        if ($bundle === []) {
            return null;
        }
        $bundle['items'] = $this->enrichItemsForPrint($bundle['items']);

        $m = &$bundle['movement'];
        $cn = trim((string) ($m['creator_name'] ?? ''));
        if ($cn === '' && ! empty($m['created_by_user_id']) && $this->db->tableExists('users')) {
            $u = $this->db->table('users')
                ->select('first_name, last_name')
                ->where('id', (int) $m['created_by_user_id'])
                ->get()->getRowArray();
            if ($u) {
                $m['creator_name'] = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? ''));
            }
        }

        return $bundle;
    }

    /**
     * Jumlah baris unit_movement_items untuk satu movement (0 jika tabel tidak ada).
     */
    public function countItemsForMovement(int $movementId): int
    {
        if (! $this->db->tableExists('unit_movement_items')) {
            return 0;
        }

        return (int) $this->db->table('unit_movement_items')->where('movement_id', $movementId)->countAllResults();
    }

    public function submitCheckpoint(int $movementId, int $stopId, string $status, array $meta = []): bool
    {
        $movement = $this->find($movementId);
        if (!$movement) {
            throw new \RuntimeException('Surat jalan tidak ditemukan.');
        }

        $status = strtoupper(trim($status));
        $status = self::CHECKPOINT_STATUS_MAP[$status] ?? $status;
        if (!in_array($status, ['DEPARTED', 'TRANSIT_VERIFIED', 'ARRIVED'], true)) {
            throw new \RuntimeException('Status checkpoint tidak valid.');
        }

        if (!$this->db->tableExists('unit_movement_stops') || !$this->db->tableExists('unit_movement_checkpoints')) {
            throw new \RuntimeException('Tabel checkpoint belum tersedia.');
        }

        $stop = $this->db->table('unit_movement_stops')
            ->where('id', $stopId)
            ->where('movement_id', $movementId)
            ->get()->getRowArray();

        if (!$stop) {
            throw new \RuntimeException('Lokasi stop tidak ditemukan.');
        }

        $seqRow = $this->db->table('unit_movement_stops')
            ->selectMax('sequence_no', 'max_seq')
            ->where('movement_id', $movementId)
            ->where('actual_at IS NOT NULL')
            ->get()->getRowArray();
        $maxDoneSeq = (int)($seqRow['max_seq'] ?? 0);
        $targetSeq = (int)($stop['sequence_no'] ?? 0);
        if ($targetSeq > ($maxDoneSeq + 1)) {
            throw new \RuntimeException('Checkpoint harus berurutan sesuai rute.');
        }

        $checkpointAt = $meta['checkpoint_at'] ?? date('Y-m-d H:i:s');
        $checkedItemIds = array_values(array_filter(array_map('intval', (array)($meta['checked_item_ids'] ?? [])), static fn ($id) => $id > 0));
        $droppedItemIds = array_values(array_filter(array_map('intval', (array)($meta['dropped_item_ids'] ?? [])), static fn ($id) => $id > 0));

        $this->db->transStart();
        $checkpointInsert = [
            'movement_id'        => $movementId,
            'stop_id'            => $stopId,
            'checkpoint_status'  => $status,
            'verifier_name'      => trim((string)($meta['verifier_name'] ?? '')),
            'verifier_phone'     => trim((string)($meta['verifier_phone'] ?? '')),
            'notes'              => trim((string)($meta['notes'] ?? '')),
            'checkpoint_at'      => $checkpointAt,
            'created_ip'         => $meta['created_ip'] ?? null,
            'user_agent'         => $meta['user_agent'] ?? null,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ];
        if ($this->db->fieldExists('checked_item_ids_json', 'unit_movement_checkpoints')) {
            $checkpointInsert['checked_item_ids_json'] = json_encode($checkedItemIds);
        }
        if ($this->db->fieldExists('dropped_item_ids_json', 'unit_movement_checkpoints')) {
            $checkpointInsert['dropped_item_ids_json'] = json_encode($droppedItemIds);
        }
        $this->db->table('unit_movement_checkpoints')->insert($checkpointInsert);

        $this->db->table('unit_movement_stops')
            ->where('id', $stopId)
            ->update(['actual_at' => $checkpointAt, 'updated_at' => date('Y-m-d H:i:s')]);

        // update movement status by progress
        $totalStops = (int)$this->db->table('unit_movement_stops')->where('movement_id', $movementId)->countAllResults();
        $doneStops  = (int)$this->db->table('unit_movement_stops')->where('movement_id', $movementId)->where('actual_at IS NOT NULL')->countAllResults();
        $newStatus = 'IN_TRANSIT';
        if ($status === 'DEPARTED') {
            $newStatus = 'IN_TRANSIT';
        } elseif ($doneStops >= $totalStops && $totalStops > 0) {
            $newStatus = 'ARRIVED';
        } elseif ($doneStops > 0) {
            $newStatus = 'IN_TRANSIT';
        }

        $this->update($movementId, [
            'status'       => $newStatus,
            'confirmed_at' => ($newStatus === 'ARRIVED') ? $checkpointAt : $movement['confirmed_at'],
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);

        $syncLocation = (string)($stop['location_name'] ?? $movement['destination_location']);
        if ($checkedItemIds !== []) {
            $this->syncSpecificItemsLocationByMovement($movementId, $syncLocation, $checkedItemIds, $movement);
        } elseif ($newStatus === 'ARRIVED') {
            $this->syncItemLocationsByMovement($movementId, $syncLocation);
        }

        if ($newStatus === 'ARRIVED') {
            $refreshed = $this->find($movementId) ?: $movement;
            $this->applyScrapSaleUnitsAfterArrival($movementId, $refreshed, $checkedItemIds);
        }

        $this->db->transComplete();
        return $this->db->transStatus();
    }

    /**
     * Set inventory unit status to SOLD (13) when SJ purpose is scrap sale and movement is completed.
     * Only FORKLIFT lines with unit_id are updated.
     */
    public function applyScrapSaleUnitsAfterArrival(int $movementId, array $movement, array $checkedItemIds = []): void
    {
        if (!$this->db->fieldExists('movement_purpose', 'unit_movements')) {
            return;
        }
        $purpose = strtoupper((string)($movement['movement_purpose'] ?? self::PURPOSE_INTERNAL_TRANSFER));
        if ($purpose !== self::PURPOSE_SCRAP_SALE) {
            return;
        }

        $unitIds = $this->collectForkliftUnitIdsForScrapSale($movementId, $movement, $checkedItemIds);
        foreach ($unitIds as $unitId) {
            if ($unitId <= 0) {
                continue;
            }
            $this->db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update([
                    'status_unit_id' => InventoryUnitModel::STATUS_UNIT_SOLD_ID,
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);
            $this->writeUnitScrapSoldLog($unitId, $movement);
        }
    }

    private function collectForkliftUnitIdsForScrapSale(int $movementId, array $movement, array $checkedItemIds): array
    {
        $ids = [];
        if ($this->db->tableExists('unit_movement_items')) {
            $builder = $this->db->table('unit_movement_items')
                ->where('movement_id', $movementId)
                ->where('component_type', 'FORKLIFT');
            if ($checkedItemIds !== []) {
                $builder->whereIn('id', $checkedItemIds);
            }
            foreach ($builder->get()->getResultArray() as $row) {
                $uid = (int)($row['unit_id'] ?? 0);
                if ($uid > 0) {
                    $ids[] = $uid;
                }
            }

            return array_values(array_unique($ids));
        }

        if (strtoupper((string)($movement['component_type'] ?? 'FORKLIFT')) === 'FORKLIFT' && !empty($movement['unit_id'])) {
            $ids[] = (int)$movement['unit_id'];
        }

        return array_values(array_unique($ids));
    }

    private function writeUnitScrapSoldLog(int $unitId, array $movement): void
    {
        if (!$this->db->tableExists('system_activity_log')) {
            return;
        }
        try {
            $this->db->table('system_activity_log')->insert([
                'user_id'             => (int)(session()->get('user_id') ?? 0),
                'module_name'         => 'WAREHOUSE',
                'action_type'         => 'UPDATE',
                'table_name'          => 'inventory_unit',
                'record_id'           => $unitId,
                'action_description'  => sprintf(
                    'Surat jalan scrab: status unit diubah ke SOLD (SJ: %s)',
                    $movement['surat_jalan_number'] ?? '-'
                ),
                'new_values'          => json_encode([
                    'status_unit_id' => InventoryUnitModel::STATUS_UNIT_SOLD_ID,
                    'movement_id'  => (int)($movement['id'] ?? 0),
                    'movement_purpose' => self::PURPOSE_SCRAP_SALE,
                ], JSON_UNESCAPED_UNICODE),
                'business_impact'     => 'HIGH',
                'is_critical'         => 1,
                'ip_address'          => service('request')->getIPAddress(),
                'user_agent'          => substr((string)service('request')->getUserAgent(), 0, 255),
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[UnitMovement] writeUnitScrapSoldLog failed: ' . $e->getMessage());
        }
    }

    private function syncSpecificItemsLocationByMovement(int $movementId, string $locationName, array $movementItemIds, ?array $movement = null): void
    {
        if (!$this->db->tableExists('unit_movement_items')) {
            if ($movement) {
                $this->syncSingleAssetLocation(
                    strtoupper((string)($movement['component_type'] ?? 'FORKLIFT')),
                    (int)($movement['unit_id'] ?? 0),
                    (int)($movement['component_id'] ?? 0),
                    $locationName,
                    $movement
                );
            }
            return;
        }

        $rows = $this->db->table('unit_movement_items')
            ->where('movement_id', $movementId)
            ->whereIn('id', $movementItemIds)
            ->get()->getResultArray();
        $mov = $movement ?: $this->find($movementId) ?: [];
        foreach ($rows as $item) {
            $this->syncSingleAssetLocation(
                strtoupper((string)($item['component_type'] ?? 'FORKLIFT')),
                (int)($item['unit_id'] ?? 0),
                (int)($item['component_id'] ?? 0),
                $locationName,
                $mov
            );
        }
    }

    public function searchUnitsForMovement(string $query, int $limit = 25): array
    {
        $query = trim($query);
        if ($query === '') {
            return [];
        }
        $like = '%' . $this->db->escapeLikeString($query) . '%';
        $soldId = InventoryUnitModel::STATUS_UNIT_SOLD_ID;

        return $this->db->query(
            "SELECT iu.id_inventory_unit, COALESCE(iu.no_unit, iu.no_unit_na) AS no_unit, iu.serial_number,
                    COALESCE(mu.merk_unit,'') AS merk_unit, COALESCE(mu.model_unit,'') AS model_unit,
                    COALESCE(iu.lokasi_unit,'') AS lokasi
             FROM inventory_unit iu
             LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
             WHERE (iu.no_unit LIKE ? OR iu.no_unit_na LIKE ? OR iu.serial_number LIKE ? OR mu.merk_unit LIKE ? OR mu.model_unit LIKE ?)
               AND (iu.status_unit_id IS NULL OR iu.status_unit_id <> ?)
             ORDER BY iu.no_unit ASC
             LIMIT {$limit}",
            [$like, $like, $like, $like, $like, $soldId]
        )->getResultArray();
    }

    private function syncItemLocationsByMovement(int $movementId, string $locationName): void
    {
        if (!$this->db->tableExists('unit_movement_items')) {
            $movement = $this->find($movementId);
            if ($movement) {
                $this->syncSingleAssetLocation(
                    strtoupper((string)($movement['component_type'] ?? 'FORKLIFT')),
                    (int)($movement['unit_id'] ?? 0),
                    (int)($movement['component_id'] ?? 0),
                    $locationName,
                    $movement
                );
            }
            return;
        }

        $movement = $this->find($movementId);
        $items = $this->db->table('unit_movement_items')->where('movement_id', $movementId)->get()->getResultArray();
        foreach ($items as $item) {
            $this->syncSingleAssetLocation(
                strtoupper((string)($item['component_type'] ?? 'FORKLIFT')),
                (int)($item['unit_id'] ?? 0),
                (int)($item['component_id'] ?? 0),
                $locationName,
                $movement ?? []
            );
        }
    }

    private function syncSingleAssetLocation(string $type, int $unitId, int $componentId, string $locationName, array $movement): void
    {
        if (!in_array($type, self::LOCATION_SYNC_TYPES, true)) {
            return;
        }

        switch ($type) {
            case 'FORKLIFT':
                if ($unitId > 0) {
                    $this->db->table('inventory_unit')->where('id_inventory_unit', $unitId)->update(['lokasi_unit' => $locationName]);
                    $this->writeAssetMovementLog('inventory_unit', $unitId, 'FORKLIFT', $locationName, $movement);
                }
                break;
            case 'ATTACHMENT':
                if ($componentId > 0 && $this->db->tableExists('inventory_attachments')) {
                    $this->db->table('inventory_attachments')->where('id', $componentId)->update(['storage_location' => $locationName]);
                    $this->writeAssetMovementLog('inventory_attachments', $componentId, 'ATTACHMENT', $locationName, $movement);
                }
                break;
            case 'BATTERY':
                if ($componentId > 0 && $this->db->tableExists('inventory_batteries')) {
                    $this->db->table('inventory_batteries')->where('id', $componentId)->update(['storage_location' => $locationName]);
                    $this->writeAssetMovementLog('inventory_batteries', $componentId, 'BATTERY', $locationName, $movement);
                }
                break;
            case 'CHARGER':
                if ($componentId > 0 && $this->db->tableExists('inventory_chargers')) {
                    $this->db->table('inventory_chargers')->where('id', $componentId)->update(['storage_location' => $locationName]);
                    $this->writeAssetMovementLog('inventory_chargers', $componentId, 'CHARGER', $locationName, $movement);
                }
                break;
            case 'FORK':
                if ($componentId > 0 && $this->db->tableExists('inventory_forks')) {
                    $this->db->table('inventory_forks')->where('id', $componentId)->update(['storage_location' => $locationName]);
                    $this->writeAssetMovementLog('inventory_forks', $componentId, 'FORK', $locationName, $movement);
                }
                break;
        }
    }

    private function writeAssetMovementLog(string $tableName, int $recordId, string $assetType, string $locationName, array $movement): void
    {
        if (!$this->db->tableExists('system_activity_log')) {
            return;
        }

        try {
            $this->db->table('system_activity_log')->insert([
                'user_id'             => (int)(session()->get('user_id') ?? 0),
                'module_name'         => 'WAREHOUSE',
                'action_type'         => 'UPDATE',
                'table_name'          => $tableName,
                'record_id'           => $recordId,
                'action_description'  => sprintf(
                    'Movement %s updated location to %s (SJ: %s)',
                    $assetType,
                    $locationName,
                    $movement['surat_jalan_number'] ?? '-'
                ),
                'new_values'          => json_encode(['location' => $locationName, 'movement_id' => (int)($movement['id'] ?? 0)], JSON_UNESCAPED_UNICODE),
                'business_impact'     => 'MEDIUM',
                'is_critical'         => 0,
                'ip_address'          => service('request')->getIPAddress(),
                'user_agent'          => substr((string)service('request')->getUserAgent(), 0, 255),
                'created_at'          => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[UnitMovement] writeAssetMovementLog failed: ' . $e->getMessage());
        }
    }

    /**
     * Get location types for dropdown
     */
    public static function getLocationTypes()
    {
        return [
            'POS_1'         => 'POS 1 (Workshop Utama)',
            'POS_2'         => 'POS 2',
            'POS_3'         => 'POS 3',
            'POS_4'         => 'POS 4',
            'POS_5'         => 'POS 5',
            'CUSTOMER_SITE' => 'Lokasi Customer',
            'WAREHOUSE'     => 'Gudang',
            'OTHER'         => 'Lainnya',
        ];
    }

    /**
     * Get component types
     */
    public static function getComponentTypes()
    {
        return [
            'FORKLIFT'   => 'Forklift / Unit',
            'ATTACHMENT' => 'Attachment',
            'CHARGER'    => 'Charger',
            'BATTERY'    => 'Baterai',
            'FORK'       => 'Fork',
            'SPAREPART'  => 'Sparepart',
            'OTHERS'     => 'Others',
        ];
    }

    /**
     * Tujuan surat jalan (operasional vs keluar jual scrab).
     */
    public static function getMovementPurposes(): array
    {
        return [
            self::PURPOSE_INTERNAL_TRANSFER => 'Pindah / operasional internal',
            self::PURPOSE_SCRAP_SALE        => 'Keluar jual scrab (unit → SOLD saat SJ selesai)',
        ];
    }
}
