<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class ReturnedVerificationController extends BaseController
{
    public function index()
    {
        if (!$this->canAccess('warehouse')) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $db = \Config\Database::connect();
        $units = $db->table('inventory_unit iu')
            ->select('
                iu.id_inventory_unit,
                iu.no_unit,
                iu.serial_number,
                iu.lokasi_unit,
                iu.workflow_status,
                iu.updated_at,
                su.status_unit,
                mu.merk_unit,
                mu.model_unit,
                tu.tipe as tipe_unit
            ')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->where('iu.status_unit_id', 12) // RETURNED queue
            ->orderBy('iu.updated_at', 'DESC')
            ->get()
            ->getResultArray();

        return view('warehouse/returned_verifications', [
            'title' => 'Returned Verifications | Warehouse',
            'units' => $units,
            'targetStatuses' => [
                1 => 'Available Stock',
                10 => 'Breakdown',
            ],
        ]);
    }

    public function detail(int $unitId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid']);
        }
        if (!$this->canAccess('warehouse')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak'])->setStatusCode(403);
        }

        $db = \Config\Database::connect();
        $unit = $db->table('inventory_unit iu')
            ->select('
                iu.id_inventory_unit,
                iu.no_unit,
                iu.serial_number,
                iu.tahun_unit,
                iu.departemen_id,
                iu.tipe_unit_id,
                iu.model_unit_id,
                iu.kapasitas_unit_id,
                iu.model_mast_id,
                iu.lokasi_unit,
                iu.workflow_status,
                iu.hour_meter,
                iu.keterangan,
                iu.sn_mesin,
                iu.sn_mast,
                iu.tinggi_mast,
                su.status_unit,
                tu.tipe as tipe_unit,
                mu.merk_unit,
                mu.model_unit,
                k.kapasitas_unit,
                tm.tipe_mast as model_mast_name,
                d.nama_departemen
            ')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kapasitas k', 'k.id_kapasitas = iu.kapasitas_unit_id', 'left')
            ->join('tipe_mast tm', 'tm.id_mast = iu.model_mast_id', 'left')
            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
            ->where('iu.id_inventory_unit', $unitId)
            ->get()
            ->getRowArray();

        if (!$unit) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan']);
        }

        $attachment = $db->table('inventory_attachments ia')
            ->select('ia.id, ia.serial_number, ia.physical_condition, ia.completeness, ia.notes, a.tipe, a.merk, a.model')
            ->join('attachment a', 'a.id_attachment = ia.attachment_type_id', 'left')
            ->where('ia.inventory_unit_id', $unitId)
            ->get()
            ->getRowArray();

        $battery = $db->table('inventory_batteries ib')
            ->select('ib.id, ib.serial_number, ib.physical_condition, ib.notes, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'b.id = ib.battery_type_id', 'left')
            ->where('ib.inventory_unit_id', $unitId)
            ->get()
            ->getRowArray();

        $charger = $db->table('inventory_chargers ic')
            ->select('ic.id, ic.serial_number, ic.physical_condition, ic.notes, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'c.id_charger = ic.charger_type_id', 'left')
            ->where('ic.inventory_unit_id', $unitId)
            ->get()
            ->getRowArray();

        $options = [
            'departemen' => $db->table('departemen')->select('id_departemen as id, nama_departemen as name')->orderBy('nama_departemen')->get()->getResultArray(),
            'tipe_unit' => $db->table('tipe_unit')->select('id_tipe_unit as id, tipe as name, jenis, id_departemen')->orderBy('tipe')->get()->getResultArray(),
            'model_unit' => $db->table('model_unit')->select('id_model_unit as id, CONCAT(merk_unit, " - ", model_unit) as name')->orderBy('merk_unit')->orderBy('model_unit')->get()->getResultArray(),
            'kapasitas' => $db->table('kapasitas')->select('id_kapasitas as id, kapasitas_unit as name')->orderBy('kapasitas_unit')->get()->getResultArray(),
            'model_mast' => $db->table('tipe_mast')
                ->select('id_mast as id, tipe_mast as model_name, tinggi_mast as height')
                ->orderBy('tipe_mast')
                ->orderBy('tinggi_mast')
                ->get()
                ->getResultArray(),
        ];

        $componentOptions = [
            'attachments' => $this->buildAttachmentOptions($db, $unitId),
            'batteries' => $this->buildBatteryOptions($db, $unitId),
            'chargers' => $this->buildChargerOptions($db, $unitId),
        ];

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'unit' => $unit,
                'attachment' => $attachment,
                'battery' => $battery,
                'charger' => $charger,
                'options' => $options,
                'component_options' => $componentOptions,
            ],
        ]);
    }

    public function verify()
    {
        if (!$this->canAccess('warehouse')) {
            return redirect()->to('/')->with('error', 'Akses ditolak');
        }

        $unitId = (int) ($this->request->getPost('unit_id') ?? 0);
        $targetStatus = (int) ($this->request->getPost('target_status') ?? 0);
        $notes = trim((string) ($this->request->getPost('notes') ?? ''));
        $verificationResult = (string) ($this->request->getPost('verification_result') ?? 'sesuai');
        $allowedStatuses = [1, 10];
        $allowedResults = ['sesuai', 'tidak_sesuai'];

        if ($unitId <= 0 || !in_array($targetStatus, $allowedStatuses, true) || !in_array($verificationResult, $allowedResults, true)) {
            return redirect()->back()->with('error', 'Data verifikasi tidak valid');
        }

        $db = \Config\Database::connect();
        $unit = $db->table('inventory_unit')
            ->select('
                id_inventory_unit, status_unit_id, no_unit, serial_number, tahun_unit,
                departemen_id, tipe_unit_id, model_unit_id, kapasitas_unit_id,
                model_mast_id,
                sn_mesin, sn_mast, tinggi_mast, hour_meter, keterangan
            ')
            ->where('id_inventory_unit', $unitId)
            ->get()
            ->getRowArray();

        if (!$unit) {
            return redirect()->back()->with('error', 'Unit tidak ditemukan');
        }

        if ((int) ($unit['status_unit_id'] ?? 0) !== 12) {
            return redirect()->back()->with('error', 'Unit sudah keluar dari antrean RETURNED');
        }

        $workflowStatus = $this->mapWorkflowStatus($targetStatus);
        $updateData = [
            'status_unit_id' => $targetStatus,
            'workflow_status' => $workflowStatus,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $changedFields = [];
        if ($verificationResult === 'tidak_sesuai') {
            $fieldMap = [
                'serial_number' => 'serial_number',
                'tahun_unit' => 'tahun_unit',
                'departemen_id' => 'departemen_id',
                'tipe_unit_id' => 'tipe_unit_id',
                'model_unit_id' => 'model_unit_id',
                'kapasitas_unit_id' => 'kapasitas_unit_id',
                'model_mast_id' => 'model_mast_id',
                'sn_mesin' => 'sn_mesin',
                'sn_mast' => 'sn_mast',
                'tinggi_mast' => 'tinggi_mast',
                'hour_meter' => 'hour_meter',
                'keterangan' => 'keterangan',
            ];

            foreach ($fieldMap as $postKey => $dbField) {
                $incomingRaw = $this->request->getPost($postKey);
                if ($incomingRaw === null) {
                    continue;
                }

                $incoming = is_string($incomingRaw) ? trim($incomingRaw) : $incomingRaw;
                if (in_array($dbField, ['departemen_id', 'tipe_unit_id', 'model_unit_id', 'kapasitas_unit_id', 'tahun_unit'], true)) {
                    $incoming = ($incoming === '' ? null : (int) $incoming);
                } elseif ($dbField === 'hour_meter') {
                    $incoming = ($incoming === '' ? null : (float) $incoming);
                } else {
                    $incoming = ($incoming === '' ? null : (string) $incoming);
                }

                $old = $unit[$dbField] ?? null;
                $oldComparable = is_numeric($old) ? (string) $old : (string) ($old ?? '');
                $newComparable = is_numeric($incoming) ? (string) $incoming : (string) ($incoming ?? '');
                if ($oldComparable !== $newComparable) {
                    $updateData[$dbField] = $incoming;
                    $changedFields[] = "{$dbField}: '{$oldComparable}' -> '{$newComparable}'";
                }
            }
        }

        $componentChanges = [];
        $componentChanges = array_merge(
            $componentChanges,
            $this->syncComponentForUnit(
                $db,
                $unitId,
                'attachment',
                (int) ($this->request->getPost('attachment_inventory_id') ?? 0),
                trim((string) ($this->request->getPost('sn_attachment') ?? ''))
            ),
            $this->syncComponentForUnit(
                $db,
                $unitId,
                'battery',
                (int) ($this->request->getPost('battery_inventory_id') ?? 0),
                trim((string) ($this->request->getPost('sn_baterai') ?? ''))
            ),
            $this->syncComponentForUnit(
                $db,
                $unitId,
                'charger',
                (int) ($this->request->getPost('charger_inventory_id') ?? 0),
                trim((string) ($this->request->getPost('sn_charger') ?? ''))
            )
        );

        $db->table('inventory_unit')
            ->where('id_inventory_unit', $unitId)
            ->update($updateData);

        if ($db->tableExists('unit_timeline')) {
            $label = match ($targetStatus) {
                1 => 'AVAILABLE_STOCK',
                10 => 'BREAKDOWN',
                default => (string) $targetStatus,
            };

            $changeText = !empty($changedFields)
                ? (' | Perbaikan data: ' . implode('; ', $changedFields))
                : '';
            $componentText = !empty($componentChanges)
                ? (' | Perbaikan komponen: ' . implode('; ', $componentChanges))
                : '';

            $timelineData = [
                'unit_id' => $unitId,
                'event_category' => 'STATUS',
                'event_type' => 'RETURNED_VERIFICATION',
                'event_title' => 'Returned Verification Completed',
                'event_description' => 'Hasil verifikasi: ' . strtoupper($verificationResult) . ' | Status unit ditetapkan ke ' . $label . $changeText . $componentText . ($notes !== '' ? (' | Catatan: ' . $notes) : ''),
                'reference_type' => 'warehouse_returned_verification',
                'reference_id' => (string) $unitId,
                'performed_by' => session()->get('user_id') ?? null,
            ];

            $now = date('Y-m-d H:i:s');
            if ($db->fieldExists('performed_at', 'unit_timeline')) {
                $timelineData['performed_at'] = $now;
            }
            if ($db->fieldExists('created_at', 'unit_timeline')) {
                $timelineData['created_at'] = $now;
            }
            if ($db->fieldExists('updated_at', 'unit_timeline')) {
                $timelineData['updated_at'] = $now;
            }

            $db->table('unit_timeline')->insert($timelineData);
        }

        $this->dispatchReturnedVerificationNotifications($unit, $targetStatus, $notes);

        return redirect()->to('/warehouse/returned-verifications')->with('success', 'Verifikasi RETURNED berhasil disimpan');
    }

    private function mapWorkflowStatus(int $targetStatus): string
    {
        return match ($targetStatus) {
            1 => 'TERSEDIA',
            7, 8 => 'DISEWA',
            10 => 'UNDER_REPAIR',
            default => 'TERSEDIA',
        };
    }

    private function buildAttachmentOptions($db, int $unitId): array
    {
        $allowedStatuses = ['AVAILABLE', 'available', 'Available', 'IN_USE', 'in_use', 'In Use', 'MAINTENANCE', 'maintenance', 'Maintenance'];
        $builder = $db->table('inventory_attachments ia')
            ->select('ia.id, ia.serial_number, ia.status, ia.inventory_unit_id, iu.no_unit, a.tipe, a.merk, a.model')
            ->join('attachment a', 'a.id_attachment = ia.attachment_type_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ia.inventory_unit_id', 'left')
            ->groupStart()
                ->whereIn('ia.status', $allowedStatuses)
                ->orWhere('ia.inventory_unit_id', $unitId)
            ->groupEnd()
            ->orderBy('ia.inventory_unit_id = ' . (int) $unitId, 'DESC', false)
            ->orderBy('ia.id', 'DESC');

        $rows = $builder->get()->getResultArray();
        if (empty($rows)) {
            $rows = $db->table('inventory_attachments ia')
                ->select('ia.id, ia.serial_number, ia.status, ia.inventory_unit_id, iu.no_unit, a.tipe, a.merk, a.model')
                ->join('attachment a', 'a.id_attachment = ia.attachment_type_id', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ia.inventory_unit_id', 'left')
                ->orderBy('ia.inventory_unit_id = ' . (int) $unitId, 'DESC', false)
                ->orderBy('ia.id', 'DESC')
                ->limit(200)
                ->get()
                ->getResultArray();
        }

        return array_map(static function (array $r): array {
            $sn = trim((string) ($r['serial_number'] ?? '')) ?: ('NO-SN#' . (int) $r['id']);
            $spec = trim((string) (($r['tipe'] ?? '-') . ' ' . ($r['merk'] ?? '') . ' ' . ($r['model'] ?? '')));
            $owner = !empty($r['no_unit']) ? (' | Unit: ' . $r['no_unit']) : '';
            return [
                'id' => (int) $r['id'],
                'serial_number' => $r['serial_number'] ?? null,
                'status' => $r['status'] ?? null,
                'name' => $sn . ' - ' . $spec . ' [' . ($r['status'] ?? '-') . ']' . $owner,
            ];
        }, $rows);
    }

    private function buildBatteryOptions($db, int $unitId): array
    {
        $allowedStatuses = ['AVAILABLE', 'available', 'Available', 'IN_USE', 'in_use', 'In Use', 'MAINTENANCE', 'maintenance', 'Maintenance'];
        $builder = $db->table('inventory_batteries ib')
            ->select('ib.id, ib.serial_number, ib.status, ib.inventory_unit_id, iu.no_unit, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
            ->join('baterai b', 'b.id = ib.battery_type_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ib.inventory_unit_id', 'left')
            ->groupStart()
                ->whereIn('ib.status', $allowedStatuses)
                ->orWhere('ib.inventory_unit_id', $unitId)
            ->groupEnd()
            ->orderBy('ib.inventory_unit_id = ' . (int) $unitId, 'DESC', false)
            ->orderBy('ib.id', 'DESC');

        $rows = $builder->get()->getResultArray();
        if (empty($rows)) {
            $rows = $db->table('inventory_batteries ib')
                ->select('ib.id, ib.serial_number, ib.status, ib.inventory_unit_id, iu.no_unit, b.merk_baterai, b.tipe_baterai, b.jenis_baterai')
                ->join('baterai b', 'b.id = ib.battery_type_id', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ib.inventory_unit_id', 'left')
                ->orderBy('ib.inventory_unit_id = ' . (int) $unitId, 'DESC', false)
                ->orderBy('ib.id', 'DESC')
                ->limit(200)
                ->get()
                ->getResultArray();
        }

        return array_map(static function (array $r): array {
            $sn = trim((string) ($r['serial_number'] ?? '')) ?: ('NO-SN#' . (int) $r['id']);
            $jenis = !empty($r['jenis_baterai']) ? (' (' . $r['jenis_baterai'] . ')') : '';
            $spec = trim((string) (($r['merk_baterai'] ?? '-') . ' ' . ($r['tipe_baterai'] ?? '') . $jenis));
            $owner = !empty($r['no_unit']) ? (' | Unit: ' . $r['no_unit']) : '';
            return [
                'id' => (int) $r['id'],
                'serial_number' => $r['serial_number'] ?? null,
                'status' => $r['status'] ?? null,
                'name' => $sn . ' - ' . $spec . ' [' . ($r['status'] ?? '-') . ']' . $owner,
            ];
        }, $rows);
    }

    private function buildChargerOptions($db, int $unitId): array
    {
        $allowedStatuses = ['AVAILABLE', 'available', 'Available', 'IN_USE', 'in_use', 'In Use', 'MAINTENANCE', 'maintenance', 'Maintenance'];
        $builder = $db->table('inventory_chargers ic')
            ->select('ic.id, ic.serial_number, ic.status, ic.inventory_unit_id, iu.no_unit, c.merk_charger, c.tipe_charger')
            ->join('charger c', 'c.id_charger = ic.charger_type_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ic.inventory_unit_id', 'left')
            ->groupStart()
                ->whereIn('ic.status', $allowedStatuses)
                ->orWhere('ic.inventory_unit_id', $unitId)
            ->groupEnd()
            ->orderBy('ic.inventory_unit_id = ' . (int) $unitId, 'DESC', false)
            ->orderBy('ic.id', 'DESC');

        $rows = $builder->get()->getResultArray();
        if (empty($rows)) {
            $rows = $db->table('inventory_chargers ic')
                ->select('ic.id, ic.serial_number, ic.status, ic.inventory_unit_id, iu.no_unit, c.merk_charger, c.tipe_charger')
                ->join('charger c', 'c.id_charger = ic.charger_type_id', 'left')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ic.inventory_unit_id', 'left')
                ->orderBy('ic.inventory_unit_id = ' . (int) $unitId, 'DESC', false)
                ->orderBy('ic.id', 'DESC')
                ->limit(200)
                ->get()
                ->getResultArray();
        }

        return array_map(static function (array $r): array {
            $sn = trim((string) ($r['serial_number'] ?? '')) ?: ('NO-SN#' . (int) $r['id']);
            $spec = trim((string) (($r['merk_charger'] ?? '-') . ' ' . ($r['tipe_charger'] ?? '')));
            $owner = !empty($r['no_unit']) ? (' | Unit: ' . $r['no_unit']) : '';
            return [
                'id' => (int) $r['id'],
                'serial_number' => $r['serial_number'] ?? null,
                'status' => $r['status'] ?? null,
                'name' => $sn . ' - ' . $spec . ' [' . ($r['status'] ?? '-') . ']' . $owner,
            ];
        }, $rows);
    }

    /**
     * Sync selected component and optional serial number to this unit.
     *
     * @return array<int, string>
     */
    private function syncComponentForUnit($db, int $unitId, string $type, int $selectedInventoryId, string $serialNumber): array
    {
        $map = [
            'attachment' => ['table' => 'inventory_attachments', 'label' => 'Attachment'],
            'battery' => ['table' => 'inventory_batteries', 'label' => 'Battery'],
            'charger' => ['table' => 'inventory_chargers', 'label' => 'Charger'],
        ];

        if (!isset($map[$type])) {
            return [];
        }

        $table = $map[$type]['table'];
        $label = $map[$type]['label'];
        $changes = [];

        $current = $db->table($table)
            ->select('id, serial_number, inventory_unit_id')
            ->where('inventory_unit_id', $unitId)
            ->orderBy('id', 'DESC')
            ->get()
            ->getRowArray();

        if ($selectedInventoryId > 0) {
            $selected = $db->table($table)
                ->select('id, serial_number, inventory_unit_id')
                ->where('id', $selectedInventoryId)
                ->get()
                ->getRowArray();

            if ($selected) {
                $selectedOwner = (int) ($selected['inventory_unit_id'] ?? 0);
                if ($selectedOwner !== 0 && $selectedOwner !== $unitId) {
                    return ["{$label} dipakai unit lain, update dilewati"];
                }

                if ((int) ($current['id'] ?? 0) !== $selectedInventoryId) {
                    if (!empty($current['id'])) {
                        $db->table($table)
                            ->where('id', (int) $current['id'])
                            ->update([
                                'inventory_unit_id' => null,
                                'status' => 'AVAILABLE',
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }

                    $db->table($table)
                        ->where('id', $selectedInventoryId)
                        ->update([
                            'inventory_unit_id' => $unitId,
                            'status' => 'IN_USE',
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                    $changes[] = "{$label} diubah ke ID#{$selectedInventoryId}";
                }
            }
        }

        if ($serialNumber !== '') {
            $targetId = $selectedInventoryId > 0 ? $selectedInventoryId : (int) ($current['id'] ?? 0);
            if ($targetId > 0) {
                $row = $db->table($table)->select('id, serial_number')->where('id', $targetId)->get()->getRowArray();
                if ($row && trim((string) ($row['serial_number'] ?? '')) !== $serialNumber) {
                    $db->table($table)
                        ->where('id', $targetId)
                        ->update([
                            'serial_number' => $serialNumber,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    $changes[] = "{$label} SN '{$row['serial_number']}' -> '{$serialNumber}'";
                }
            }
        }

        return $changes;
    }

    /**
     * Send notification to related divisions after returned verification.
     */
    private function dispatchReturnedVerificationNotifications(array $unit, int $targetStatus, string $notes = ''): void
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists('notifications') || !$db->tableExists('users')) {
            return;
        }

        $this->ensureDefaultReturnedVerificationRule($db);

        $unitNo = trim((string) ($unit['no_unit'] ?? '')) ?: ('ID-' . (int) ($unit['id_inventory_unit'] ?? 0));
        $statusLabel = ($targetStatus === 1) ? 'AVAILABLE_STOCK' : (($targetStatus === 10) ? 'BREAKDOWN' : (string) $targetStatus);
        $title = 'Returned Verification Completed';
        $message = "Unit {$unitNo} selesai verifikasi RETURNED. Status akhir: {$statusLabel}."
            . ($notes !== '' ? (" Catatan: {$notes}") : '');

        // Preferred path: use notification rules so targets are configurable by admin.
        $sentByRules = false;
        helper('notification');
        if (function_exists('send_notification')) {
            $ruleResult = send_notification('returned_verification_completed', [
                'module' => 'warehouse_returned_verification',
                'id' => (string) ($unit['id_inventory_unit'] ?? ''),
                'url' => '/warehouse/returned-verifications',
                'unit_no' => $unitNo,
                'status_akhir' => $statusLabel,
                'catatan' => $notes,
                'verifikator' => (string) (session()->get('name') ?? session()->get('username') ?? ('User#' . (int) (session()->get('user_id') ?? 0))),
                'tanggal' => date('d/m/Y H:i'),
            ]);
            if (is_array($ruleResult) && !empty($ruleResult['success'])) {
                $sentByRules = true;
            }
        }

        // If rules don't exist yet, fallback to default cross-division broadcast.
        if ($sentByRules) {
            return;
        }

        $targetUserIds = [];
        if ($db->tableExists('divisions')) {
            $rows = $db->table('users u')
                ->select('u.id')
                ->join('divisions d', 'd.id = u.division_id', 'left')
                ->where('u.is_active', 1)
                ->groupStart()
                    ->orLike('LOWER(d.name)', 'warehouse')
                    ->orLike('LOWER(d.name)', 'operational')
                    ->orLike('LOWER(d.name)', 'service')
                ->groupEnd()
                ->get()
                ->getResultArray();
            $targetUserIds = array_map(static fn($r) => (int) $r['id'], $rows);
        }

        // Fallback: at least notify verifier when division mapping is unavailable.
        $currentUserId = (int) (session()->get('user_id') ?? 0);
        if ($currentUserId > 0 && !in_array($currentUserId, $targetUserIds, true)) {
            $targetUserIds[] = $currentUserId;
        }

        if (empty($targetUserIds)) {
            return;
        }

        $targetUserIds = array_values(array_unique(array_filter($targetUserIds)));
        $notificationModel = new NotificationModel();
        $notificationModel->sendToMultiple($targetUserIds, $title, $message, [
            'type' => 'info',
            'icon' => 'check-circle',
            'module' => 'warehouse_returned_verification',
            'id' => (string) ($unit['id_inventory_unit'] ?? ''),
            'url' => '/warehouse/returned-verifications',
            'notification_style' => 'action_required',
        ]);
    }

    /**
     * Create default notification rule once (idempotent).
     */
    private function ensureDefaultReturnedVerificationRule($db): void
    {
        if (!$db->tableExists('notification_rules')) {
            return;
        }

        $exists = $db->table('notification_rules')
            ->where('trigger_event', 'returned_verification_completed')
            ->countAllResults();

        if ($exists > 0) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $defaultRule = [
            'name' => 'Returned Verification Completed',
            'description' => 'Notifikasi saat verifikasi unit RETURNED selesai.',
            'trigger_event' => 'returned_verification_completed',
            'target_divisions' => 'warehouse,operational,service',
            'target_roles' => '',
            'target_departments' => '',
            'target_users' => '',
            'title_template' => 'Returned Verification: {{unit_no}}',
            'message_template' => 'Unit {{unit_no}} selesai verifikasi RETURNED. Status akhir: {{status_akhir}}. Verifikator: {{verifikator}}. {{catatan}}',
            'category' => 'warehouse',
            'type' => 'info',
            'priority' => 'normal',
            'icon' => 'check-circle',
            'url_template' => '/warehouse/returned-verifications',
            'is_active' => 1,
        ];

        if ($db->fieldExists('created_at', 'notification_rules')) {
            $defaultRule['created_at'] = $now;
        }
        if ($db->fieldExists('updated_at', 'notification_rules')) {
            $defaultRule['updated_at'] = $now;
        }

        $db->table('notification_rules')->insert($defaultRule);
    }
}

