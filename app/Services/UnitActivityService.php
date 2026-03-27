<?php

namespace App\Services;

/**
 * UnitActivityService
 * 
 * Provides comprehensive activity tracking for inventory units.
 * Queries multiple data sources and returns unified timeline data.
 */
class UnitActivityService
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Get unified timeline of all unit activities
     * Merges data from all sources, sorted by date (newest first)
     */
    public function getUnifiedTimeline(int $unitId, ?string $category = null, int $limit = 100): array
    {
        $events = [];

        // Collect events from all sources
        $events = array_merge($events, $this->getRegistrationEvents($unitId));
        $events = array_merge($events, $this->getSPKEvents($unitId));
        $events = array_merge($events, $this->getMovementEvents($unitId));
        $events = array_merge($events, $this->getDIEvents($unitId));
        $events = array_merge($events, $this->getContractEvents($unitId));
        $events = array_merge($events, $this->getServiceEvents($unitId));
        $events = array_merge($events, $this->getVerificationEvents($unitId));
        $events = array_merge($events, $this->getComponentEvents($unitId));
        $events = array_merge($events, $this->getSparepartUsageEvents($unitId));
        $events = array_merge($events, $this->getSpkSparepartEvents($unitId));
        $events = array_merge($events, $this->getStatusEvents($unitId));

        // Filter by category if specified
        if ($category && $category !== 'all') {
            $events = array_filter($events, fn($e) => strtolower($e['category']) === strtolower($category));
        }

        // Sort by date descending (newest first)
        usort($events, fn($a, $b) => strtotime($b['date']) - strtotime($a['date']));

        // Limit results
        return array_slice($events, 0, $limit);
    }

    /**
     * Get unit registration event
     */
    protected function getRegistrationEvents(int $unitId): array
    {
        $events = [];
        
        try {
            $unit = $this->db->table('inventory_unit')
                ->select('id_inventory_unit, created_at, lokasi_unit')
                ->where('id_inventory_unit', $unitId)
                ->get()->getRowArray();

            if ($unit && !empty($unit['created_at'])) {
                $events[] = [
                    'category' => 'REGISTRATION',
                    'icon' => 'box',
                    'color' => 'gray',
                    'title' => 'Unit Registered',
                    'description' => 'Unit masuk ke inventory',
                    'detail' => 'Location: ' . ($unit['lokasi_unit'] ?? 'POS 1'),
                    'date' => $unit['created_at'],
                    'reference_type' => 'unit',
                    'reference_id' => $unitId,
                    'reference_number' => null,
                    'meta' => [],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getRegistrationEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get SPK stage events (minimal for timeline)
     */
    protected function getSPKEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('spk_unit_stages')) {
                return $events;
            }

            $stages = $this->db->table('spk_unit_stages sus')
                ->select('sus.*, s.nomor_spk, s.pelanggan, s.status as spk_status')
                ->join('spk s', 's.id = sus.spk_id', 'left')
                ->where('sus.unit_id', $unitId)
                ->orderBy('sus.created_at', 'DESC')
                ->get()->getResultArray();

            foreach ($stages as $stage) {
                $stageName = $stage['stage_name'] ?? 'unknown';
                $stageLabels = [
                    'persiapan_unit' => 'Persiapan Unit',
                    'fabrikasi' => 'Fabrikasi',
                    'painting' => 'Painting',
                    'pdi' => 'PDI (Pre-Delivery Inspection)',
                ];
                $isCompleted = !empty($stage['tanggal_approve']);

                $spkNumber = $stage['nomor_spk'] ?? null;

                $events[] = [
                    'category' => 'SPK',
                    'icon' => 'clipboard-list',
                    'color' => 'blue',
                    'title' => 'SPK Stage: ' . ($stageLabels[$stageName] ?? ucfirst($stageName)),
                    'description' => $spkNumber ?? '-',
                    'detail' => $isCompleted ? 'Completed' : 'In Progress',
                    'date' => $stage['tanggal_approve'] ?? $stage['created_at'],
                    'reference_type' => 'spk',
                    'reference_id' => $stage['spk_id'],
                    'reference_number' => $spkNumber,
                    'meta' => ['url' => base_url('service/spk/detail/' . $stage['spk_id'])],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getSPKEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get internal movement events (surat jalan)
     */
    protected function getMovementEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('unit_movements')) {
                return $events;
            }

            $movements = $this->db->table('unit_movements')
                ->where('unit_id', $unitId)
                ->orderBy('movement_date', 'DESC')
                ->get()->getResultArray();

            foreach ($movements as $mov) {
                $events[] = [
                    'category' => 'MOVEMENT',
                    'icon' => 'truck',
                    'color' => 'cyan',
                    'title' => 'Internal Movement',
                    'description' => ($mov['origin_location'] ?? '-') . ' → ' . ($mov['destination_location'] ?? '-'),
                    'detail' => implode(' | ', array_filter([
                        !empty($mov['surat_jalan_number']) ? 'Surat Jalan: ' . $mov['surat_jalan_number'] : null,
                        'Status: ' . ($mov['status'] ?? '-'),
                        !empty($mov['reason']) ? 'Reason: ' . $mov['reason'] : null,
                    ])),
                    'date' => $mov['movement_date'] ?? $mov['created_at'],
                    'reference_type' => 'movement',
                    'reference_id' => $mov['id'],
                    'reference_number' => $mov['surat_jalan_number'] ?? $mov['movement_number'] ?? null,
                    'meta' => ['url' => base_url('warehouse/unit-movement')],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getMovementEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get Delivery Instruction events
     */
    protected function getDIEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('delivery_items') || !$this->db->tableExists('delivery_instructions')) {
                return $events;
            }

            $diItems = $this->db->table('delivery_items di')
                ->select('di.*, dins.nomor_di, dins.status_di, dins.pelanggan, dins.lokasi, dins.tanggal_kirim, dins.dibuat_pada')
                ->join('delivery_instructions dins', 'dins.id = di.di_id', 'left')
                ->where('di.unit_id', $unitId)
                ->where('di.item_type', 'UNIT')
                ->orderBy('dins.dibuat_pada', 'DESC')
                ->get()->getResultArray();

            foreach ($diItems as $di) {
                $statusLabels = [
                    'DIAJUKAN' => 'DI Submitted',
                    'DISETUJUI' => 'DI Approved',
                    'PERSIAPAN_UNIT' => 'Unit Preparation',
                    'SIAP_KIRIM' => 'Ready to Ship',
                    'DALAM_PERJALANAN' => 'In Transit',
                    'SAMPAI_LOKASI' => 'Delivered',
                    'SELESAI' => 'Completed',
                ];

                $diNumber = $di['nomor_di'] ?? null;

                $events[] = [
                    'category' => 'DELIVERY',
                    'icon' => 'shipping-fast',
                    'color' => 'green',
                    'title' => $statusLabels[$di['status_di']] ?? 'Delivery: ' . ($di['status_di'] ?? '-'),
                    'description' => $diNumber ?? '-',
                    'detail' => implode(' | ', array_filter([
                        'Customer: ' . ($di['pelanggan'] ?? '-'),
                        'Location: ' . ($di['lokasi'] ?? '-'),
                        !empty($di['tanggal_kirim']) ? 'Ship Date: ' . date('d M Y', strtotime($di['tanggal_kirim'])) : null,
                    ])),
                    'date' => $di['dibuat_pada'] ?? $di['created_at'],
                    'reference_type' => 'di',
                    'reference_id' => $di['di_id'],
                    'reference_number' => $diNumber,
                    'meta' => ['url' => base_url('operational/delivery/detail/' . $di['di_id'])],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getDIEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get contract events
     */
    protected function getContractEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('kontrak_unit')) {
                return $events;
            }

            $contracts = $this->db->table('kontrak_unit ku')
                ->select('ku.*, k.no_kontrak, c.customer_name')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
                ->where('ku.unit_id', $unitId)
                ->orderBy('ku.tanggal_mulai', 'DESC')
                ->get()->getResultArray();

            foreach ($contracts as $contract) {
                // Contract Start Event
                $contractUrl = base_url('marketing/kontrak/detail/' . $contract['kontrak_id']);
                if (!empty($contract['tanggal_mulai'])) {
                    $events[] = [
                        'category' => 'CONTRACT',
                        'icon' => 'file-contract',
                        'color' => 'purple',
                        'title' => 'Contract Started',
                        'description' => $contract['no_kontrak'] ?? '-',
                        'detail' => implode(' | ', array_filter([
                            'Customer: ' . ($contract['customer_name'] ?? '-'),
                        ])),
                        'date' => $contract['tanggal_mulai'],
                        'reference_type' => 'contract',
                        'reference_id' => $contract['kontrak_id'],
                        'reference_number' => $contract['no_kontrak'],
                        'meta' => ['url' => $contractUrl],
                    ];
                }

                // Contract End Event
                if (!empty($contract['tanggal_selesai'])) {
                    $events[] = [
                        'category' => 'CONTRACT',
                        'icon' => 'file-contract',
                        'color' => 'purple',
                        'title' => 'Contract Ended',
                        'description' => $contract['no_kontrak'] ?? '-',
                        'detail' => 'Status: ' . ($contract['status'] ?? '-'),
                        'date' => $contract['tanggal_selesai'],
                        'reference_type' => 'contract',
                        'reference_id' => $contract['kontrak_id'],
                        'reference_number' => $contract['no_kontrak'],
                        'meta' => ['url' => $contractUrl],
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getContractEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get service/work order events (minimal for timeline)
     */
    protected function getServiceEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('work_orders')) {
                return $events;
            }

            $workOrders = $this->db->table('work_orders wo')
                ->select('wo.*, woc.category_name, wos.status_name')
                ->join('work_order_categories woc', 'woc.id = wo.category_id', 'left')
                ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
                ->where('wo.unit_id', $unitId)
                ->where('wo.deleted_at IS NULL')
                ->orderBy('wo.report_date', 'DESC')
                ->get()->getResultArray();

            foreach ($workOrders as $wo) {
                $events[] = [
                    'category' => 'SERVICE',
                    'icon' => 'tools',
                    'color' => 'orange',
                    'title' => 'Work Order: ' . ($wo['status_name'] ?? 'Created'),
                    'description' => $wo['work_order_number'] ?? '-',
                    'detail' => $wo['category_name'] ?? '-',
                    'date' => $wo['report_date'] ?? $wo['created_at'],
                    'reference_type' => 'work_order',
                    'reference_id' => $wo['id'],
                    'reference_number' => $wo['work_order_number'],
                    'meta' => ['url' => base_url('service/work-orders/detail/' . $wo['id'])],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getServiceEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get unit verification events
     */
    protected function getVerificationEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('unit_verification_history')) {
                return $events;
            }

            $verifications = $this->db->table('unit_verification_history uvh')
                ->select('uvh.*, wo.work_order_number,
                    COALESCE(e.staff_name, CONCAT(u.first_name, " ", u.last_name)) as verifier_name')
                ->join('work_orders wo', 'wo.id = uvh.work_order_id', 'left')
                ->join('employees e', 'e.id = uvh.verified_by', 'left')
                ->join('users u', 'u.id = uvh.verified_by', 'left')
                ->where('uvh.unit_id', $unitId)
                ->orderBy('uvh.verified_at', 'DESC')
                ->get()->getResultArray();

            foreach ($verifications as $v) {
                $verificationType = $v['verification_type'] ?? 'WO';
                $title = $verificationType === 'STANDALONE' ? 'Unit Verified (Standalone)' : 'Unit Verified';
                $detail = 'By: ' . ($v['verifier_name'] ?? '-');

                // Parse verification_data for "what changed" (unit_changes, components)
                $meta = ['url' => null, 'changes' => []];
                if (!empty($v['verification_data'])) {
                    $vd = is_string($v['verification_data']) ? json_decode($v['verification_data'], true) : $v['verification_data'];
                    if (is_array($vd)) {
                        $changes = [];
                        if (!empty($vd['unit_changes']) && is_array($vd['unit_changes'])) {
                            foreach ($vd['unit_changes'] as $field => $diff) {
                                if (is_array($diff) && isset($diff['before'], $diff['after'])) {
                                    $changes[] = $field . ': ' . ($diff['before'] ?? '-') . ' → ' . ($diff['after'] ?? '-');
                                } elseif (is_string($diff)) {
                                    $changes[] = $field . ': ' . $diff;
                                }
                            }
                        }
                        if (!empty($vd['components']) && is_array($vd['components'])) {
                            foreach ($vd['components'] as $comp => $val) {
                                $changes[] = $comp . ': ' . (is_array($val) ? json_encode($val) : $val);
                            }
                        }
                        $meta['changes'] = $changes;
                        if (!empty($changes)) {
                            $detail .= "\nYang berubah: " . implode('; ', array_slice($changes, 0, 5));
                        }
                    }
                }
                if (!empty($v['work_order_id'])) {
                    $meta['url'] = base_url('service/work-orders/detail/' . $v['work_order_id']);
                }

                $events[] = [
                    'category' => 'VERIFICATION',
                    'icon' => 'check-circle',
                    'color' => 'success',
                    'title' => $title,
                    'description' => !empty($v['work_order_number']) ? $v['work_order_number'] : 'Standalone Verification',
                    'detail' => $detail,
                    'date' => $v['verified_at'],
                    'reference_type' => 'verification',
                    'reference_id' => $v['id'],
                    'reference_number' => $v['work_order_number'] ?? null,
                    'meta' => $meta,
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getVerificationEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get component change events (attachment/charger/battery attach/detach/transfer)
     * Uses from_unit_id / to_unit_id (component_audit_log schema)
     */
    protected function getComponentEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('component_audit_log')) {
                return $events;
            }

            $logs = $this->db->table('component_audit_log cal')
                ->select('cal.*, CONCAT(u.first_name, " ", u.last_name) as actor_name')
                ->join('users u', 'u.id = cal.performed_by', 'left')
                ->groupStart()
                    ->where('cal.from_unit_id', $unitId)
                    ->orWhere('cal.to_unit_id', $unitId)
                ->groupEnd()
                ->orderBy('cal.performed_at', 'DESC')
                ->get()->getResultArray();

            $eventTypeLabels = [
                'ASSIGNED' => 'Attached',
                'REMOVED' => 'Detached',
                'TRANSFERRED' => 'Transferred',
                'ATTACHED' => 'Attached',
                'DETACHED' => 'Detached',
                'REPLACED' => 'Replaced',
            ];

            foreach ($logs as $log) {
                $isIncoming = (int)($log['to_unit_id'] ?? 0) === $unitId;
                $eventType = $log['event_type'] ?? 'ASSIGNED';
                $label = $eventTypeLabels[$eventType] ?? ucfirst(strtolower($eventType));
                $direction = $isIncoming ? '→ Incoming' : '← Outgoing';

                $events[] = [
                    'category' => 'COMPONENT',
                    'icon' => 'puzzle-piece',
                    'color' => 'teal',
                    'title' => ucfirst(strtolower($log['component_type'] ?? 'Component')) . ' ' . $label . ' ' . $direction,
                    'description' => $log['event_title'] ?? ($log['triggered_by'] ?? 'Manual'),
                    'detail' => implode(' | ', array_filter([
                        !empty($log['actor_name']) ? 'By: ' . $log['actor_name'] : null,
                        !empty($log['notes']) ? $log['notes'] : null,
                    ])),
                    'date' => $log['performed_at'] ?? $log['created_at'],
                    'reference_type' => 'component',
                    'reference_id' => $log['component_id'],
                    'reference_number' => null,
                    'meta' => [
                        'url' => null,
                        'spk_id' => $log['spk_id'] ?? null,
                        'work_order_id' => $log['work_order_id'] ?? null,
                    ],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getComponentEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get sparepart usage events from work_order_spareparts.
     * Handles two perspectives for KANIBAL:
     *   - Inbound (recipient): wo.unit_id = $unitId — enriched with donor unit info when KANIBAL.
     *   - Outbound (donor): wos.source_unit_id = $unitId AND source_type='KANIBAL'.
     */
    protected function getSparepartUsageEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('work_order_spareparts') || !$this->db->tableExists('work_orders')) {
                return $events;
            }

            // --- A1: Inbound (this unit received the WO) ---
            $inboundRows = $this->db->table('work_order_spareparts wos')
                ->select('wos.*, wo.work_order_number, wo.unit_id AS wo_unit_id, wo.report_date,
                    CONCAT(u.first_name, " ", u.last_name) as mechanic_name,
                    iu_src.no_unit as donor_unit_no')
                ->join('work_orders wo', 'wo.id = wos.work_order_id', 'inner')
                ->join('users u', 'u.id = wo.mechanic_id', 'left')
                ->join('inventory_unit iu_src', 'iu_src.id_inventory_unit = wos.source_unit_id', 'left')
                ->where('wo.unit_id', $unitId)
                ->where('wo.deleted_at IS NULL')
                ->groupStart()
                    ->where('wos.quantity_used >', 0)
                    ->orWhere('wos.quantity_brought >', 0)
                ->groupEnd()
                ->orderBy('wo.report_date', 'DESC')
                ->get()->getResultArray();

            foreach ($inboundRows as $row) {
                $qty = (int)($row['quantity_used'] ?? $row['quantity_brought'] ?? 0);
                if ($qty <= 0) continue;

                $isKanibal = strtoupper($row['source_type'] ?? '') === 'KANIBAL';
                $partName  = $row['sparepart_name'] ?? $row['sparepart_code'] ?? 'Sparepart';

                $detailParts = [
                    'Qty: ' . $qty . ' ' . ($row['satuan'] ?? ''),
                ];
                if ($isKanibal && !empty($row['donor_unit_no'])) {
                    $detailParts[] = 'Dari unit: ' . $row['donor_unit_no'];
                }
                if ($isKanibal && !empty($row['source_notes'])) {
                    $detailParts[] = 'Alasan: ' . $row['source_notes'];
                }
                if (!empty($row['mechanic_name'])) {
                    $detailParts[] = 'Mekanik: ' . $row['mechanic_name'];
                }

                $events[] = [
                    'category'         => 'SPAREPART',
                    'icon'             => $isKanibal ? 'exchange-alt' : 'wrench',
                    'color'            => $isKanibal ? 'orange' : 'indigo',
                    'title'            => ($isKanibal ? 'Kanibal masuk: ' : 'Sparepart dipakai: ') . $partName,
                    'description'      => $row['work_order_number'] ?? '-',
                    'detail'           => implode(' | ', array_filter($detailParts)),
                    'date'             => $row['report_date'] ?? $row['created_at'] ?? $row['updated_at'],
                    'reference_type'   => 'work_order',
                    'reference_id'     => $row['work_order_id'],
                    'reference_number' => $row['work_order_number'],
                    'meta'             => ['url' => base_url('service/work-orders/detail/' . $row['work_order_id'])],
                ];
            }

            // --- A2: Outbound (this unit is the KANIBAL donor for another WO) ---
            $outboundRows = $this->db->table('work_order_spareparts wos')
                ->select('wos.*, wo.work_order_number, wo.unit_id AS recipient_unit_id, wo.report_date,
                    CONCAT(u.first_name, " ", u.last_name) as mechanic_name,
                    iu_tgt.no_unit as recipient_unit_no')
                ->join('work_orders wo', 'wo.id = wos.work_order_id', 'inner')
                ->join('users u', 'u.id = wo.mechanic_id', 'left')
                ->join('inventory_unit iu_tgt', 'iu_tgt.id_inventory_unit = wo.unit_id', 'left')
                ->where('wos.source_type', 'KANIBAL')
                ->where('wos.source_unit_id', $unitId)
                ->where('wo.deleted_at IS NULL')
                // Exclude rare edge case where donor == recipient
                ->where('wo.unit_id !=', $unitId)
                ->groupStart()
                    ->where('wos.quantity_used >', 0)
                    ->orWhere('wos.quantity_brought >', 0)
                ->groupEnd()
                ->orderBy('wo.report_date', 'DESC')
                ->get()->getResultArray();

            foreach ($outboundRows as $row) {
                $qty      = (int)($row['quantity_used'] ?? $row['quantity_brought'] ?? 0);
                if ($qty <= 0) continue;

                $partName = $row['sparepart_name'] ?? $row['sparepart_code'] ?? 'Sparepart';

                $detailParts = [
                    'Qty: ' . $qty . ' ' . ($row['satuan'] ?? ''),
                ];
                if (!empty($row['recipient_unit_no'])) {
                    $detailParts[] = 'Ke unit: ' . $row['recipient_unit_no'];
                }
                if (!empty($row['source_notes'])) {
                    $detailParts[] = 'Alasan: ' . $row['source_notes'];
                }
                if (!empty($row['mechanic_name'])) {
                    $detailParts[] = 'Mekanik: ' . $row['mechanic_name'];
                }

                $events[] = [
                    'category'         => 'SPAREPART',
                    'icon'             => 'exchange-alt',
                    'color'            => 'orange',
                    'title'            => 'Kanibal keluar (WO): ' . $partName,
                    'description'      => $row['work_order_number'] ?? '-',
                    'detail'           => implode(' | ', array_filter($detailParts)),
                    'date'             => $row['report_date'] ?? $row['created_at'] ?? $row['updated_at'],
                    'reference_type'   => 'work_order',
                    'reference_id'     => $row['work_order_id'],
                    'reference_number' => $row['work_order_number'],
                    'meta'             => ['url' => base_url('service/work-orders/detail/' . $row['work_order_id'])],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getSparepartUsageEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get SPK sparepart events (inbound & outbound KANIBAL) from spk_spareparts.
     * Requires spk_spareparts.unit_id (recipient) column added in the kanibal schema migration.
     *   - Inbound (recipient Y): ssp.unit_id = $unitId
     *   - Outbound (donor X): ssp.source_type='KANIBAL' AND ssp.source_unit_id = $unitId
     */
    protected function getSpkSparepartEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('spk_spareparts') || !$this->db->tableExists('spk')) {
                return $events;
            }

            // Check if unit_id column exists (added via kanibal migration)
            $cols = $this->db->getFieldNames('spk_spareparts');
            if (!in_array('unit_id', $cols)) {
                return $events;
            }

            // --- B-Inbound: this unit is the recipient in the SPK sparepart row ---
            $inboundRows = $this->db->table('spk_spareparts ssp')
                ->select('ssp.*, s.nomor_spk, s.dibuat_pada, s.id AS spk_id,
                    iu_src.no_unit AS donor_unit_no')
                ->join('spk s', 's.id = ssp.spk_id', 'inner')
                ->join('inventory_unit iu_src', 'iu_src.id_inventory_unit = ssp.source_unit_id', 'left')
                ->where('ssp.unit_id', $unitId)
                ->where('ssp.quantity_brought >', 0)
                ->orderBy('s.dibuat_pada', 'DESC')
                ->get()->getResultArray();

            foreach ($inboundRows as $row) {
                $isKanibal = strtoupper($row['source_type'] ?? '') === 'KANIBAL';
                $partName  = $row['sparepart_name'] ?? $row['sparepart_code'] ?? 'Sparepart';
                $qty       = (int)($row['quantity_brought'] ?? 0);
                if ($qty <= 0) continue;

                $detailParts = ['Qty: ' . $qty . ' ' . ($row['satuan'] ?? '')];
                if ($isKanibal && !empty($row['donor_unit_no'])) {
                    $detailParts[] = 'Dari unit: ' . $row['donor_unit_no'];
                }
                if ($isKanibal && !empty($row['source_notes'])) {
                    $detailParts[] = 'Alasan: ' . $row['source_notes'];
                }

                $events[] = [
                    'category'         => 'SPAREPART',
                    'icon'             => $isKanibal ? 'exchange-alt' : 'toolbox',
                    'color'            => $isKanibal ? 'orange' : 'teal',
                    'title'            => ($isKanibal ? 'Kanibal masuk (SPK): ' : 'Sparepart (SPK): ') . $partName,
                    'description'      => $row['nomor_spk'] ?? ('-'),
                    'detail'           => implode(' | ', array_filter($detailParts)),
                    'date'             => $row['dibuat_pada'] ?? $row['created_at'] ?? $row['updated_at'],
                    'reference_type'   => 'spk',
                    'reference_id'     => $row['spk_id'],
                    'reference_number' => $row['nomor_spk'],
                    'meta'             => ['url' => base_url('service/spk/detail/' . $row['spk_id'])],
                ];
            }

            // --- B-Outbound: this unit is the KANIBAL donor for another SPK ---
            $outboundRows = $this->db->table('spk_spareparts ssp')
                ->select('ssp.*, s.nomor_spk, s.dibuat_pada, s.id AS spk_id,
                    iu_tgt.no_unit AS recipient_unit_no')
                ->join('spk s', 's.id = ssp.spk_id', 'inner')
                ->join('inventory_unit iu_tgt', 'iu_tgt.id_inventory_unit = ssp.unit_id', 'left')
                ->where('ssp.source_type', 'KANIBAL')
                ->where('ssp.source_unit_id', $unitId)
                // Exclude edge case donor == recipient (unit_id may be NULL for old rows)
                ->where("(ssp.unit_id IS NULL OR ssp.unit_id != {$unitId})", null, false)
                ->where('ssp.quantity_brought >', 0)
                ->orderBy('s.dibuat_pada', 'DESC')
                ->get()->getResultArray();

            foreach ($outboundRows as $row) {
                $qty      = (int)($row['quantity_brought'] ?? 0);
                if ($qty <= 0) continue;

                $partName = $row['sparepart_name'] ?? $row['sparepart_code'] ?? 'Sparepart';

                $detailParts = ['Qty: ' . $qty . ' ' . ($row['satuan'] ?? '')];
                if (!empty($row['recipient_unit_no'])) {
                    $detailParts[] = 'Ke unit: ' . $row['recipient_unit_no'];
                }
                if (!empty($row['source_notes'])) {
                    $detailParts[] = 'Alasan: ' . $row['source_notes'];
                }

                $events[] = [
                    'category'         => 'SPAREPART',
                    'icon'             => 'exchange-alt',
                    'color'            => 'orange',
                    'title'            => 'Kanibal keluar (SPK): ' . $partName,
                    'description'      => $row['nomor_spk'] ?? '-',
                    'detail'           => implode(' | ', array_filter($detailParts)),
                    'date'             => $row['dibuat_pada'] ?? $row['created_at'] ?? $row['updated_at'],
                    'reference_type'   => 'spk',
                    'reference_id'     => $row['spk_id'],
                    'reference_number' => $row['nomor_spk'],
                    'meta'             => ['url' => base_url('service/spk/detail/' . $row['spk_id'])],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getSpkSparepartEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get status change events from unit_timeline
     */
    protected function getStatusEvents(int $unitId): array
    {
        $events = [];

        try {
            if (!$this->db->tableExists('unit_timeline')) {
                return $events;
            }

            $timeline = $this->db->table('unit_timeline')
                ->where('unit_id', $unitId)
                ->orderBy('performed_at', 'DESC')
                ->get()->getResultArray();

            foreach ($timeline as $event) {
                $events[] = [
                    'category' => 'STATUS',
                    'icon' => 'sync-alt',
                    'color' => 'yellow',
                    'title' => $event['event_title'] ?? 'Status Change',
                    'description' => $event['event_category'] ?? '-',
                    'detail' => $event['event_description'] ?? '',
                    'date' => $event['performed_at'] ?? $event['created_at'],
                    'reference_type' => $event['reference_type'] ?? null,
                    'reference_id' => $event['reference_id'] ?? null,
                    'reference_number' => null,
                    'meta' => [],
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getStatusEvents: ' . $e->getMessage());
        }

        return $events;
    }

    /**
     * Get detailed service history (Unified: Work Orders + SPK with spareparts)
     * Used for Service & Parts tab - returns single sorted array
     */
    public function getServiceHistory(int $unitId): array
    {
        $unified = [];

        // Get Work Orders with spareparts
        try {
            $workOrders = $this->db->table('work_orders wo')
                ->select('wo.*, woc.category_name, wos.status_name, wos.status_color,
                          CONCAT(u.first_name, " ", u.last_name) as mechanic_name')
                ->join('work_order_categories woc', 'woc.id = wo.category_id', 'left')
                ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
                ->join('users u', 'u.id = wo.mechanic_id', 'left')
                ->where('wo.unit_id', $unitId)
                ->where('wo.deleted_at IS NULL')
                ->orderBy('wo.report_date', 'DESC')
                ->get()->getResultArray();

            foreach ($workOrders as $wo) {
                $spareparts = [];
                try {
                    $spareparts = $this->db->table('work_order_spareparts')
                        ->where('work_order_id', $wo['id'])
                        ->get()->getResultArray();
                } catch (\Throwable $e) {}

                $unified[] = [
                    'type' => 'WO',
                    'type_label' => 'Work Order',
                    'reference_number' => $wo['work_order_number'] ?? '-',
                    'reference_id' => $wo['id'],
                    'stage_category' => $wo['category_name'] ?? '-',
                    'description' => $wo['complaint_description'] ?? '',
                    'mechanic' => $wo['mechanic_name'] ?? '-',
                    'customer' => null,
                    'status' => $wo['status_name'] ?? 'Pending',
                    'status_color' => $wo['status_color'] ?? 'warning',
                    'is_completed' => strtolower($wo['status_name'] ?? '') === 'completed',
                    'date' => $wo['report_date'] ?? $wo['created_at'],
                    'spareparts' => $spareparts,
                    'url' => base_url('service/work-orders/detail/' . $wo['id']),
                ];
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getServiceHistory WO: ' . $e->getMessage());
        }

        // Get SPK preparations with spareparts
        try {
            if ($this->db->tableExists('spk_unit_stages')) {
                $spkStages = $this->db->table('spk_unit_stages sus')
                    ->select('sus.*, s.nomor_spk, s.pelanggan, s.lokasi')
                    ->join('spk s', 's.id = sus.spk_id', 'left')
                    ->where('sus.unit_id', $unitId)
                    ->orderBy('sus.created_at', 'DESC')
                    ->get()->getResultArray();

                $stageLabels = [
                    'persiapan_unit' => 'Persiapan Unit',
                    'fabrikasi' => 'Fabrikasi',
                    'painting' => 'Painting',
                    'pdi' => 'PDI',
                ];

                foreach ($spkStages as $stage) {
                    $spareparts = [];
                    if ($this->db->tableExists('spk_spareparts')) {
                        try {
                            $spareparts = $this->db->table('spk_spareparts')
                                ->where('spk_id', $stage['spk_id'])
                                ->where('unit_id', $unitId)
                                ->where('stage_name', $stage['stage_name'])
                                ->get()->getResultArray();
                        } catch (\Throwable $e) {}
                    }

                    $stageName = $stage['stage_name'] ?? 'unknown';
                    $isCompleted = !empty($stage['tanggal_approve']);

                    $unified[] = [
                        'type' => 'SPK',
                        'type_label' => 'SPK Preparation',
                        'reference_number' => $stage['nomor_spk'] ?? '-',
                        'reference_id' => $stage['spk_id'],
                        'stage_category' => $stageLabels[$stageName] ?? ucfirst($stageName),
                        'description' => null,
                        'mechanic' => $stage['mekanik'] ?? '-',
                        'customer' => $stage['pelanggan'] ?? '-',
                        'status' => $isCompleted ? 'Completed' : 'In Progress',
                        'status_color' => $isCompleted ? 'success' : 'warning',
                        'is_completed' => $isCompleted,
                        'date' => $stage['tanggal_approve'] ?? $stage['created_at'],
                        'spareparts' => $spareparts,
                        'url' => base_url('service/spk/detail/' . $stage['spk_id']),
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getServiceHistory SPK: ' . $e->getMessage());
        }

        // Sort by date descending
        usort($unified, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $unified;
    }

    /**
     * Get contract history (detailed)
     * Used for Contracts tab
     */
    public function getContractHistory(int $unitId): array
    {
        $contracts = [];

        try {
            if (!$this->db->tableExists('kontrak_unit')) {
                return $contracts;
            }

            $contracts = $this->db->table('kontrak_unit ku')
                ->select('ku.*, 
                          k.no_kontrak, k.tanggal_mulai as contract_start, k.tanggal_berakhir as contract_end,
                          k.rental_type, k.jenis_sewa, k.status as contract_status,
                          c.customer_name, c.customer_code,
                          cl.location_name, cl.city, cl.address')
                ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
                ->join('customers c', 'c.id = k.customer_id', 'left')
                ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
                ->where('ku.unit_id', $unitId)
                ->orderBy('ku.tanggal_mulai', 'DESC')
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getContractHistory: ' . $e->getMessage());
        }

        return $contracts;
    }

    /**
     * Get quick stats for overview
     */
    public function getQuickStats(int $unitId): array
    {
        $stats = [
            'total_work_orders' => 0,
            'total_contracts' => 0,
            'total_movements' => 0,
            'total_spk' => 0,
        ];

        try {
            if ($this->db->tableExists('work_orders')) {
                $stats['total_work_orders'] = $this->db->table('work_orders')
                    ->where('unit_id', $unitId)
                    ->where('deleted_at IS NULL')
                    ->countAllResults();
            }

            if ($this->db->tableExists('kontrak_unit')) {
                $stats['total_contracts'] = $this->db->table('kontrak_unit')
                    ->where('unit_id', $unitId)
                    ->countAllResults();
            }

            if ($this->db->tableExists('unit_movements')) {
                $stats['total_movements'] = $this->db->table('unit_movements')
                    ->where('unit_id', $unitId)
                    ->countAllResults();
            }

            if ($this->db->tableExists('spk_unit_stages')) {
                $stats['total_spk'] = $this->db->table('spk_unit_stages')
                    ->where('unit_id', $unitId)
                    ->select('spk_id')
                    ->distinct()
                    ->countAllResults();
            }
        } catch (\Throwable $e) {
            log_message('warning', '[UnitActivityService] getQuickStats: ' . $e->getMessage());
        }

        return $stats;
    }
}
