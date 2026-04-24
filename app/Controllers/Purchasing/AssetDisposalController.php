<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\UnitSaleModel;
use App\Models\ComponentSaleModel;
use App\Models\InventoryUnitModel;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\API\ResponseTrait;

/**
 * AssetDisposalController — unified asset disposal (unit + component sales)
 *
 * Handles penjualan Unit, Attachment, Charger, Baterai, dan Sparepart.
 * Reuses purchasing.unit_sale.* permission keys.
 */
class AssetDisposalController extends BaseController
{
    use ResponseTrait, ActivityLoggingTrait;

    protected UnitSaleModel      $saleModel;
    protected ComponentSaleModel $compModel;
    protected InventoryUnitModel $unitModel;
    protected                    $db;

    public function __construct()
    {
        $this->saleModel = new UnitSaleModel();
        $this->compModel = new ComponentSaleModel();
        $this->unitModel = new InventoryUnitModel();
        $this->db        = \Config\Database::connect();
        helper(['form', 'url']);
    }

    // ─────────────────────────────────────────────────────────
    // INDEX — unified listing page
    // ─────────────────────────────────────────────────────────
    public function index()
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return view('errors/html/error_403');
        }

        return view('purchasing/unit_sale/index', [
            'title' => lang('Purchasing.asset_disposal'),
            'stats' => $this->saleModel->getUnifiedStats(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // DataTables AJAX — unified view
    // ─────────────────────────────────────────────────────────
    public function getSalesData()
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $request    = $this->request;
        $draw       = (int) $request->getGet('draw');
        $search     = trim((string) ($request->getGet('search')['value'] ?? ''));
        $assetType  = trim((string) ($request->getGet('asset_type') ?? ''));

        $filters = [
            'search'    => $search,
            'status'    => $request->getGet('status') ?? '',
            'date_from' => $request->getGet('date_from') ?? '',
            'date_to'   => $request->getGet('date_to') ?? '',
        ];

        $rows = [];

        // Fetch unit sales (unless filtering by a specific component type)
        if ($assetType === '' || $assetType === 'UNIT') {
            $unitRows = $this->saleModel->getWithUnitInfo($filters);
            foreach ($unitRows as $r) {
                $unitLabel  = esc($r['no_unit'] ?: $r['no_unit_na'] ?: 'UNIT-' . $r['unit_id']);
                $unitDetail = trim(($r['merk_unit'] ?? '') . ' ' . ($r['model_unit'] ?? ''));

                $rows[] = [
                    'sort_date'     => $r['tanggal_jual'],
                    'sort_id'       => 'U' . $r['id'],
                    'no_dokumen'    => '<span class="font-monospace fw-semibold">' . esc($r['no_dokumen']) . '</span>',
                    'asset_type'    => '<span class="badge badge-soft-blue">UNIT</span>',
                    'asset_info'    => '<div class="fw-semibold">' . $unitLabel . '</div>'
                                     . '<small class="text-muted">' . esc($unitDetail) . '</small>'
                                     . ($r['serial_number'] ? '<br><small class="text-muted font-monospace">SN: ' . esc($r['serial_number']) . '</small>' : ''),
                    'tanggal_jual'  => date('d/m/Y', strtotime($r['tanggal_jual'])),
                    'pembeli'       => '<div>' . esc($r['nama_pembeli']) . '</div>'
                                     . ($r['telepon_pembeli'] ? '<small class="text-muted">' . esc($r['telepon_pembeli']) . '</small>' : ''),
                    'harga_jual'    => 'Rp ' . number_format((float) $r['harga_jual'], 0, ',', '.'),
                    'metode'        => $this->getMetodeBadge($r['metode_pembayaran']),
                    'status'        => $r['status'] === 'CANCELLED'
                        ? '<span class="badge badge-soft-red">' . lang('Common.cancelled') . '</span>'
                        : '<span class="badge badge-soft-green">' . lang('Common.completed') . '</span>',
                    'actions'       => '<a href="' . base_url('purchasing/asset-disposal/detail/unit/' . $r['id']) . '" class="btn btn-sm btn-outline-primary" title="Detail"><i class="fas fa-eye"></i></a>',
                ];
            }
        }

        // Fetch component sales (unless filtering by UNIT)
        if ($assetType !== 'UNIT') {
            if ($assetType !== '') {
                $filters['asset_type'] = $assetType;
            }
            $compRows = $this->compModel->getWithAssetInfo($filters);

            $typeBadgeMap = [
                'ATTACHMENT' => 'badge-soft-purple',
                'CHARGER'    => 'badge-soft-cyan',
                'BATTERY'    => 'badge-soft-orange',
                'SPAREPART'  => 'badge-soft-gray',
            ];

            foreach ($compRows as $r) {
                $assetLabel = ComponentSaleModel::getAssetLabel($r);
                $badgeClass = $typeBadgeMap[$r['asset_type']] ?? 'badge-soft-gray';
                $bundleTag  = $r['linked_unit_sale_id'] ? ' <span class="badge badge-soft-yellow" title="Bundled with unit sale">Bundled</span>' : '';

                $rows[] = [
                    'sort_date'     => $r['tanggal_jual'],
                    'sort_id'       => 'C' . $r['id'],
                    'no_dokumen'    => '<span class="font-monospace fw-semibold">' . esc($r['no_dokumen']) . '</span>',
                    'asset_type'    => '<span class="badge ' . $badgeClass . '">' . esc($r['asset_type']) . '</span>' . $bundleTag,
                    'asset_info'    => '<div>' . esc($assetLabel) . '</div>',
                    'tanggal_jual'  => date('d/m/Y', strtotime($r['tanggal_jual'])),
                    'pembeli'       => '<div>' . esc($r['nama_pembeli']) . '</div>'
                                     . ($r['telepon_pembeli'] ? '<small class="text-muted">' . esc($r['telepon_pembeli']) . '</small>' : ''),
                    'harga_jual'    => 'Rp ' . number_format((float) $r['harga_jual'], 0, ',', '.'),
                    'metode'        => $this->getMetodeBadge($r['metode_pembayaran']),
                    'status'        => $r['status'] === 'CANCELLED'
                        ? '<span class="badge badge-soft-red">' . lang('Common.cancelled') . '</span>'
                        : '<span class="badge badge-soft-green">' . lang('Common.completed') . '</span>',
                    'actions'       => '<a href="' . base_url('purchasing/asset-disposal/detail/component/' . $r['id']) . '" class="btn btn-sm btn-outline-primary" title="Detail"><i class="fas fa-eye"></i></a>',
                ];
            }
        }

        // Sort combined rows by date DESC, then id DESC
        usort($rows, function ($a, $b) {
            $cmp = strcmp($b['sort_date'], $a['sort_date']);
            return $cmp !== 0 ? $cmp : strcmp($b['sort_id'], $a['sort_id']);
        });

        // Remove sort keys (serverSide: false — return ALL rows, client handles pagination)
        $rows = array_map(function ($r) {
            unset($r['sort_date'], $r['sort_id']);
            return $r;
        }, $rows);

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => count($rows),
            'recordsFiltered' => count($rows),
            'data'            => array_values($rows),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Select2 — eligible units (status != SOLD)
    // ─────────────────────────────────────────────────────────
    public function getEligibleUnits()
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return $this->response->setJSON(['success' => false])->setStatusCode(403);
        }

        $q = trim((string) ($this->request->getGet('q') ?? ''));

        $builder = $this->db->table('inventory_unit iu')
            ->select('
                iu.id_inventory_unit AS id,
                iu.no_unit,
                iu.no_unit_na,
                iu.serial_number,
                mu.merk_unit,
                mu.model_unit,
                su.status_unit AS status_unit_name,
                iu.status_unit_id
            ', false)
            ->join('model_unit mu',  'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->where('iu.status_unit_id !=', InventoryUnitModel::STATUS_UNIT_SOLD_ID);

        if ($q !== '') {
            $builder->groupStart()
                ->like('iu.no_unit', $q)
                ->orLike('iu.no_unit_na', $q)
                ->orLike('iu.serial_number', $q)
                ->orLike('mu.merk_unit', $q)
                ->orLike('mu.model_unit', $q)
                ->groupEnd();
        }

        $rows = $builder->orderBy('iu.no_unit')->limit(30)->get()->getResultArray();

        $results = array_map(function ($r) {
            $noUnit = $r['no_unit'] ?: $r['no_unit_na'] ?: 'UNIT-' . $r['id'];
            $desc   = trim(($r['merk_unit'] ?? '') . ' ' . ($r['model_unit'] ?? ''));
            $sn     = $r['serial_number'] ? ' | SN: ' . $r['serial_number'] : '';
            return [
                'id'              => $r['id'],
                'text'            => $noUnit . ($desc ? ' — ' . $desc : '') . $sn,
                'no_unit'         => $noUnit,
                'merk_model'      => $desc,
                'serial_number'   => $r['serial_number'] ?? '',
                'status_unit_id'  => $r['status_unit_id'],
                'status_name'     => $r['status_unit_name'] ?? '',
            ];
        }, $rows);

        return $this->response->setJSON(['success' => true, 'results' => $results]);
    }

    // ─────────────────────────────────────────────────────────
    // Select2 — eligible components (status != SOLD)
    // GET ?type=ATTACHMENT|CHARGER|BATTERY|SPAREPART&q=
    // ─────────────────────────────────────────────────────────
    public function getEligibleComponents()
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return $this->response->setJSON(['success' => false])->setStatusCode(403);
        }

        $type = strtoupper(trim((string) ($this->request->getGet('type') ?? '')));
        $q    = trim((string) ($this->request->getGet('q') ?? ''));
        $results = [];

        switch ($type) {
            case 'ATTACHMENT':
                $builder = $this->db->table('inventory_attachments ia')
                    ->select('ia.id, ia.item_number, ia.serial_number, ia.status, at.tipe, at.merk, at.model, ia.inventory_unit_id', false)
                    ->join('attachment at', 'at.id_attachment = ia.attachment_type_id', 'left')
                    ->where('ia.status !=', 'SOLD');
                if ($q !== '') {
                    $builder->groupStart()
                        ->like('ia.item_number', $q)
                        ->orLike('ia.serial_number', $q)
                        ->orLike('at.tipe', $q)
                        ->orLike('at.merk', $q)
                        ->groupEnd();
                }
                $rows = $builder->orderBy('ia.item_number')->limit(30)->get()->getResultArray();
                foreach ($rows as $r) {
                    $desc = trim(($r['merk'] ?? '') . ' ' . ($r['tipe'] ?? ''));
                    $results[] = [
                        'id'   => $r['id'],
                        'text' => $r['item_number'] . ($desc ? ' — ' . $desc : '') . ($r['serial_number'] ? ' | SN: ' . $r['serial_number'] : ''),
                        'status' => $r['status'],
                        'unit_id' => $r['inventory_unit_id'],
                    ];
                }
                break;

            case 'CHARGER':
                $builder = $this->db->table('inventory_chargers ic')
                    ->select('ic.id, ic.item_number, ic.serial_number, ic.status, ct.merk_charger, ct.tipe_charger, ic.inventory_unit_id', false)
                    ->join('charger ct', 'ct.id_charger = ic.charger_type_id', 'left')
                    ->where('ic.status !=', 'SOLD');
                if ($q !== '') {
                    $builder->groupStart()
                        ->like('ic.item_number', $q)
                        ->orLike('ic.serial_number', $q)
                        ->orLike('ct.merk_charger', $q)
                        ->orLike('ct.tipe_charger', $q)
                        ->groupEnd();
                }
                $rows = $builder->orderBy('ic.item_number')->limit(30)->get()->getResultArray();
                foreach ($rows as $r) {
                    $desc = trim(($r['merk_charger'] ?? '') . ' ' . ($r['tipe_charger'] ?? ''));
                    $results[] = [
                        'id'   => $r['id'],
                        'text' => $r['item_number'] . ($desc ? ' — ' . $desc : '') . ($r['serial_number'] ? ' | SN: ' . $r['serial_number'] : ''),
                        'status' => $r['status'],
                        'unit_id' => $r['inventory_unit_id'],
                    ];
                }
                break;

            case 'BATTERY':
                $builder = $this->db->table('inventory_batteries ib')
                    ->select('ib.id, ib.item_number, ib.serial_number, ib.status, bt.merk_baterai, bt.tipe_baterai, bt.jenis_baterai, ib.inventory_unit_id', false)
                    ->join('baterai bt', 'bt.id = ib.battery_type_id', 'left')
                    ->where('ib.status !=', 'SOLD');
                if ($q !== '') {
                    $builder->groupStart()
                        ->like('ib.item_number', $q)
                        ->orLike('ib.serial_number', $q)
                        ->orLike('bt.merk_baterai', $q)
                        ->orLike('bt.jenis_baterai', $q)
                        ->groupEnd();
                }
                $rows = $builder->orderBy('ib.item_number')->limit(30)->get()->getResultArray();
                foreach ($rows as $r) {
                    $desc = trim(($r['merk_baterai'] ?? '') . ' ' . ($r['tipe_baterai'] ?? '') . ' ' . ($r['jenis_baterai'] ?? ''));
                    $results[] = [
                        'id'   => $r['id'],
                        'text' => $r['item_number'] . ($desc ? ' — ' . $desc : '') . ($r['serial_number'] ? ' | SN: ' . $r['serial_number'] : ''),
                        'status' => $r['status'],
                        'unit_id' => $r['inventory_unit_id'],
                    ];
                }
                break;

            case 'SPAREPART':
                $builder = $this->db->table('sparepart sp')
                    ->select('sp.id_sparepart AS id, sp.kode, sp.desc_sparepart');
                if ($q !== '') {
                    $builder->groupStart()
                        ->like('sp.kode', $q)
                        ->orLike('sp.desc_sparepart', $q)
                        ->groupEnd();
                }
                $rows = $builder->orderBy('sp.kode')->limit(30)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'id'   => $r['id'],
                        'text' => $r['kode'] . ' — ' . $r['desc_sparepart'],
                        'status' => null,
                        'unit_id' => null,
                    ];
                }
                break;

            default:
                return $this->response->setJSON(['success' => false, 'message' => 'Invalid asset type.']);
        }

        return $this->response->setJSON(['success' => true, 'results' => $results]);
    }

    // ─────────────────────────────────────────────────────────
    // Get components attached to a unit (for bundled sale UI)
    // GET /purchasing/asset-disposal/getUnitComponents/:unitId
    // ─────────────────────────────────────────────────────────
    public function getUnitComponents(int $unitId)
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return $this->response->setJSON(['success' => false])->setStatusCode(403);
        }

        $components = [];

        // Attachments
        $atts = $this->db->table('inventory_attachments ia')
            ->select('ia.id, ia.item_number, ia.serial_number, ia.status, at.tipe, at.merk', false)
            ->join('attachment at', 'at.id_attachment = ia.attachment_type_id', 'left')
            ->where('ia.inventory_unit_id', $unitId)
            ->where('ia.status !=', 'SOLD')
            ->get()->getResultArray();
        foreach ($atts as $a) {
            $components[] = [
                'type'   => 'ATTACHMENT',
                'id'     => $a['id'],
                'label'  => $a['item_number'] . ' — ' . trim(($a['merk'] ?? '') . ' ' . ($a['tipe'] ?? '')),
                'serial' => $a['serial_number'] ?? '',
                'status' => $a['status'],
            ];
        }

        // Batteries
        $bats = $this->db->table('inventory_batteries ib')
            ->select('ib.id, ib.item_number, ib.serial_number, ib.status, bt.merk_baterai, bt.tipe_baterai, bt.jenis_baterai', false)
            ->join('baterai bt', 'bt.id = ib.battery_type_id', 'left')
            ->where('ib.inventory_unit_id', $unitId)
            ->where('ib.status !=', 'SOLD')
            ->get()->getResultArray();
        foreach ($bats as $b) {
            $components[] = [
                'type'   => 'BATTERY',
                'id'     => $b['id'],
                'label'  => $b['item_number'] . ' — ' . trim(($b['merk_baterai'] ?? '') . ' ' . ($b['tipe_baterai'] ?? '') . ' ' . ($b['jenis_baterai'] ?? '')),
                'serial' => $b['serial_number'] ?? '',
                'status' => $b['status'],
            ];
        }

        // Chargers
        $chrs = $this->db->table('inventory_chargers ic')
            ->select('ic.id, ic.item_number, ic.serial_number, ic.status, ct.merk_charger, ct.tipe_charger', false)
            ->join('charger ct', 'ct.id_charger = ic.charger_type_id', 'left')
            ->where('ic.inventory_unit_id', $unitId)
            ->where('ic.status !=', 'SOLD')
            ->get()->getResultArray();
        foreach ($chrs as $c) {
            $components[] = [
                'type'   => 'CHARGER',
                'id'     => $c['id'],
                'label'  => $c['item_number'] . ' — ' . trim(($c['merk_charger'] ?? '') . ' ' . ($c['tipe_charger'] ?? '')),
                'serial' => $c['serial_number'] ?? '',
                'status' => $c['status'],
            ];
        }

        return $this->response->setJSON(['success' => true, 'components' => $components]);
    }

    // ─────────────────────────────────────────────────────────
    // Generate sale number — unified across both tables
    // ─────────────────────────────────────────────────────────
    public function generateNumber()
    {
        return $this->response->setJSON([
            'success'    => true,
            'no_dokumen' => $this->saleModel->generateSaleNumber(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // STORE — POST /purchasing/asset-disposal/store
    // Handles: UNIT (with optional bundled components),
    //          standalone ATTACHMENT/CHARGER/BATTERY/SPAREPART
    // ─────────────────────────────────────────────────────────
    public function store()
    {
        if (!$this->hasPermission('purchasing.unit_sale.create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $assetType = strtoupper(trim((string) $this->request->getPost('asset_type')));

        if ($assetType === 'UNIT') {
            return $this->storeUnitSale();
        }

        if (in_array($assetType, ['ATTACHMENT', 'CHARGER', 'BATTERY', 'SPAREPART'], true)) {
            return $this->storeComponentSale($assetType);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Tipe aset tidak valid.']);
    }

    /**
     * Store unit sale (with optional bundled components)
     */
    private function storeUnitSale()
    {
        $rules = [
            'no_dokumen'        => 'required|max_length[50]',
            'unit_id'           => 'required|integer',
            'tanggal_jual'      => 'required|valid_date',
            'nama_pembeli'      => 'required|max_length[255]',
            'harga_jual'        => 'required',
            'metode_pembayaran' => 'required|in_list[CASH,TRANSFER,CEK,KREDIT]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $unitId = (int) $this->request->getPost('unit_id');
        $unit   = $this->unitModel->find($unitId);

        if (!$unit) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.']);
        }
        if ($unit['status_unit_id'] == InventoryUnitModel::STATUS_UNIT_SOLD_ID) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unit ini sudah berstatus SOLD.']);
        }

        $noDokumen = $this->request->getPost('no_dokumen');

        // Check uniqueness across both tables
        if ($this->isDocNumberUsed($noDokumen)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nomor dokumen sudah digunakan.']);
        }

        $hargaRaw = preg_replace('/[^0-9.]/', '', (string) $this->request->getPost('harga_jual'));

        // Parse bundled components from POST
        $bundledComponents = json_decode((string) $this->request->getPost('bundled_components'), true) ?: [];

        $db = $this->db;
        $db->transStart();

        try {
            // 1. Insert unit sale record
            $insertData = [
                'no_dokumen'              => $noDokumen,
                'unit_id'                 => $unitId,
                'tanggal_jual'            => $this->request->getPost('tanggal_jual'),
                'nama_pembeli'            => $this->request->getPost('nama_pembeli'),
                'alamat_pembeli'          => $this->request->getPost('alamat_pembeli') ?: null,
                'telepon_pembeli'         => $this->request->getPost('telepon_pembeli') ?: null,
                'harga_jual'              => (float) $hargaRaw,
                'metode_pembayaran'       => $this->request->getPost('metode_pembayaran'),
                'no_kwitansi'             => $this->request->getPost('no_kwitansi') ?: null,
                'no_bast'                 => $this->request->getPost('no_bast') ?: null,
                'no_invoice'              => $this->request->getPost('no_invoice') ?: null,
                'status'                  => 'COMPLETED',
                'previous_status_unit_id' => $unit['status_unit_id'],
                'keterangan'              => $this->request->getPost('keterangan') ?: null,
                'sold_by_user_id'         => (int) session('user_id'),
                'has_bundled_components'   => !empty($bundledComponents) ? 1 : 0,
            ];

            $saleId = $this->saleModel->insert($insertData, true);

            // 2. Update unit status → SOLD
            $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update(['status_unit_id' => InventoryUnitModel::STATUS_UNIT_SOLD_ID, 'updated_at' => date('Y-m-d H:i:s')]);

            // 3. Handle bundled components
            $this->handleBundledComponents($saleId, $unitId, $bundledComponents, $insertData);

            // 4. Detach remaining (unchecked) components from unit → AVAILABLE
            $this->detachRemainingComponents($unitId, $bundledComponents);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }

            $this->logActivity('CREATE', 'unit_sale_records', (int) $saleId,
                sprintf('Penjualan unit %s kepada %s (Rp %s) — Dok: %s',
                    $unit['no_unit'] ?: $unit['no_unit_na'] ?: 'UNIT-' . $unitId,
                    $insertData['nama_pembeli'],
                    number_format($insertData['harga_jual'], 0, ',', '.'),
                    $noDokumen
                ),
                ['business_impact' => 'HIGH', 'is_critical' => 1]
            );

            return $this->response->setJSON([
                'success'    => true,
                'message'    => lang('Purchasing.sale_success'),
                'detail_url' => base_url('purchasing/asset-disposal/detail/unit/' . $saleId),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[AssetDisposal::storeUnitSale] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    /**
     * Store standalone component sale
     */
    private function storeComponentSale(string $assetType)
    {
        $rules = [
            'no_dokumen'        => 'required|max_length[50]',
            'asset_id'          => 'required|integer',
            'tanggal_jual'      => 'required|valid_date',
            'nama_pembeli'      => 'required|max_length[255]',
            'harga_jual'        => 'required',
            'metode_pembayaran' => 'required|in_list[CASH,TRANSFER,CEK,KREDIT]',
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Validasi gagal.',
                'errors'  => $this->validator->getErrors(),
            ]);
        }

        $assetId   = (int) $this->request->getPost('asset_id');
        $noDokumen = $this->request->getPost('no_dokumen');

        if ($this->isDocNumberUsed($noDokumen)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nomor dokumen sudah digunakan.']);
        }

        $hargaRaw = preg_replace('/[^0-9.]/', '', (string) $this->request->getPost('harga_jual'));

        // Get current inventory info for rollback
        $prevStatus = null;
        $prevUnitId = null;

        if ($assetType !== 'SPAREPART') {
            $tableMap = [
                'ATTACHMENT' => ['table' => 'inventory_attachments', 'pk' => 'id'],
                'CHARGER'    => ['table' => 'inventory_chargers',    'pk' => 'id'],
                'BATTERY'    => ['table' => 'inventory_batteries',   'pk' => 'id'],
            ];

            $t = $tableMap[$assetType];
            $asset = $this->db->table($t['table'])->where($t['pk'], $assetId)->get()->getRowArray();

            if (!$asset) {
                return $this->response->setJSON(['success' => false, 'message' => 'Aset tidak ditemukan.']);
            }
            if (($asset['status'] ?? '') === 'SOLD') {
                return $this->response->setJSON(['success' => false, 'message' => 'Aset ini sudah berstatus SOLD.']);
            }

            $prevStatus = $asset['status'] ?? null;
            $prevUnitId = $asset['inventory_unit_id'] ?? null;
        }

        $db = $this->db;
        $db->transStart();

        try {
            $insertData = [
                'no_dokumen'        => $noDokumen,
                'asset_type'        => $assetType,
                'asset_id'          => $assetId,
                'tanggal_jual'      => $this->request->getPost('tanggal_jual'),
                'nama_pembeli'      => $this->request->getPost('nama_pembeli'),
                'alamat_pembeli'    => $this->request->getPost('alamat_pembeli') ?: null,
                'telepon_pembeli'   => $this->request->getPost('telepon_pembeli') ?: null,
                'harga_jual'        => (float) $hargaRaw,
                'metode_pembayaran' => $this->request->getPost('metode_pembayaran'),
                'no_kwitansi'       => $this->request->getPost('no_kwitansi') ?: null,
                'no_bast'           => $this->request->getPost('no_bast') ?: null,
                'no_invoice'        => $this->request->getPost('no_invoice') ?: null,
                'status'            => 'COMPLETED',
                'previous_status'   => $prevStatus,
                'previous_unit_id'  => $prevUnitId ? (int) $prevUnitId : null,
                'keterangan'        => $this->request->getPost('keterangan') ?: null,
                'sold_by_user_id'   => (int) session('user_id'),
            ];

            $compSaleId = $this->compModel->insert($insertData, true);

            // Update inventory status → SOLD + detach from unit (skip for SPAREPART)
            if ($assetType !== 'SPAREPART') {
                $t = $tableMap[$assetType];
                $db->table($t['table'])
                    ->where($t['pk'], $assetId)
                    ->update(['status' => 'SOLD', 'inventory_unit_id' => null, 'updated_at' => date('Y-m-d H:i:s')]);

                // Log to attachment_transfer_log
                $this->logComponentTransfer($assetType, $assetId, $prevUnitId, null, 'DETACH', 'ASSET_DISPOSAL_STANDALONE');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }

            $this->logActivity('CREATE', 'component_sale_records', (int) $compSaleId,
                sprintf('Penjualan %s (ID:%d) kepada %s — Dok: %s', $assetType, $assetId, $insertData['nama_pembeli'], $noDokumen),
                ['business_impact' => 'HIGH', 'is_critical' => 1]
            );

            return $this->response->setJSON([
                'success'    => true,
                'message'    => lang('Purchasing.sale_success'),
                'detail_url' => base_url('purchasing/asset-disposal/detail/component/' . $compSaleId),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[AssetDisposal::storeComponentSale] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────
    // DETAIL — GET /purchasing/asset-disposal/detail/:type/:id
    // ─────────────────────────────────────────────────────────
    public function detail(string $type, int $id)
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return view('errors/html/error_403');
        }

        if ($type === 'unit') {
            $sale = $this->saleModel->getDetailWithUnit($id);
            if (!$sale) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data penjualan tidak ditemukan.');
            }

            // Get bundled components if any
            $bundledComponents = $this->compModel->getBundledComponents($id);

            return view('purchasing/unit_sale/detail', [
                'title'              => lang('Purchasing.asset_disposal_detail') . ' — ' . $sale['no_dokumen'],
                'sale'               => $sale,
                'sale_type'          => 'unit',
                'bundled_components' => $bundledComponents,
            ]);
        }

        if ($type === 'component') {
            $sale = $this->compModel->getDetailWithAsset($id);
            if (!$sale) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data penjualan tidak ditemukan.');
            }

            return view('purchasing/unit_sale/detail', [
                'title'              => lang('Purchasing.asset_disposal_detail') . ' — ' . $sale['no_dokumen'],
                'sale'               => $sale,
                'sale_type'          => 'component',
                'bundled_components' => [],
            ]);
        }

        throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }

    // ─────────────────────────────────────────────────────────
    // CANCEL — POST /purchasing/asset-disposal/cancel/:type/:id
    // ─────────────────────────────────────────────────────────
    public function cancel(string $type, int $id)
    {
        if (!$this->hasPermission('purchasing.unit_sale.delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $reason = trim((string) $this->request->getPost('cancelled_reason'));
        if ($reason === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Alasan pembatalan harus diisi.']);
        }

        if ($type === 'unit') {
            return $this->cancelUnitSale($id, $reason);
        }
        if ($type === 'component') {
            return $this->cancelComponentSale($id, $reason);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Tipe tidak valid.']);
    }

    /**
     * Cancel unit sale — rollback unit + linked components
     */
    private function cancelUnitSale(int $id, string $reason)
    {
        $sale = $this->saleModel->find($id);
        if (!$sale) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
        }
        if ($sale['status'] !== 'COMPLETED') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya transaksi COMPLETED yang dapat dibatalkan.']);
        }

        $db = $this->db;
        $db->transStart();

        try {
            // 1. Cancel unit sale record
            $this->saleModel->update($id, [
                'status'               => 'CANCELLED',
                'cancelled_at'         => date('Y-m-d H:i:s'),
                'cancelled_by_user_id' => (int) session('user_id'),
                'cancelled_reason'     => $reason,
            ]);

            // 2. Rollback unit status
            if ($sale['previous_status_unit_id']) {
                $db->table('inventory_unit')
                    ->where('id_inventory_unit', $sale['unit_id'])
                    ->update(['status_unit_id' => $sale['previous_status_unit_id'], 'updated_at' => date('Y-m-d H:i:s')]);
            }

            // 3. Cancel and rollback all linked component sales
            $linkedComps = $this->compModel->where('linked_unit_sale_id', $id)->where('status', 'COMPLETED')->findAll();
            foreach ($linkedComps as $comp) {
                $this->compModel->update($comp['id'], [
                    'status'               => 'CANCELLED',
                    'cancelled_at'         => date('Y-m-d H:i:s'),
                    'cancelled_by_user_id' => (int) session('user_id'),
                    'cancelled_reason'     => 'Unit sale cancelled: ' . $reason,
                ]);

                // Restore component inventory
                if ($comp['asset_type'] !== 'SPAREPART') {
                    $this->restoreComponentInventory($comp);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }

            $this->logActivity('DELETE', 'unit_sale_records', $id,
                sprintf('Pembatalan penjualan unit — Dok: %s | Alasan: %s', $sale['no_dokumen'], $reason),
                ['business_impact' => 'HIGH', 'is_critical' => 1]
            );

            return $this->response->setJSON(['success' => true, 'message' => lang('Purchasing.cancel_success')]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[AssetDisposal::cancelUnitSale] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal membatalkan: ' . $e->getMessage()]);
        }
    }

    /**
     * Cancel standalone component sale — rollback inventory
     */
    private function cancelComponentSale(int $id, string $reason)
    {
        $sale = $this->compModel->find($id);
        if (!$sale) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
        }
        if ($sale['status'] !== 'COMPLETED') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya transaksi COMPLETED yang dapat dibatalkan.']);
        }

        $db = $this->db;
        $db->transStart();

        try {
            $this->compModel->update($id, [
                'status'               => 'CANCELLED',
                'cancelled_at'         => date('Y-m-d H:i:s'),
                'cancelled_by_user_id' => (int) session('user_id'),
                'cancelled_reason'     => $reason,
            ]);

            // Restore inventory (not for SPAREPART)
            if ($sale['asset_type'] !== 'SPAREPART') {
                $this->restoreComponentInventory($sale);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }

            $this->logActivity('DELETE', 'component_sale_records', $id,
                sprintf('Pembatalan penjualan %s — Dok: %s | Alasan: %s', $sale['asset_type'], $sale['no_dokumen'], $reason),
                ['business_impact' => 'HIGH', 'is_critical' => 1]
            );

            return $this->response->setJSON(['success' => true, 'message' => lang('Purchasing.cancel_success')]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[AssetDisposal::cancelComponentSale] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal membatalkan: ' . $e->getMessage()]);
        }
    }

    // ═══════════════════════════════════════════════════════════
    // PRIVATE HELPERS
    // ═══════════════════════════════════════════════════════════

    /**
     * Handle bundled component sales within a unit sale
     */
    private function handleBundledComponents(int $saleId, int $unitId, array $bundled, array $parentData): void
    {
        $tableMap = [
            'ATTACHMENT' => ['table' => 'inventory_attachments', 'pk' => 'id'],
            'CHARGER'    => ['table' => 'inventory_chargers',    'pk' => 'id'],
            'BATTERY'    => ['table' => 'inventory_batteries',   'pk' => 'id'],
        ];

        foreach ($bundled as $comp) {
            $compType    = strtoupper($comp['type'] ?? '');
            $compId      = (int) ($comp['id'] ?? 0);
            $compPrice   = (float) ($comp['price'] ?? 0);

            if (!$compId || !isset($tableMap[$compType])) {
                continue;
            }

            $t = $tableMap[$compType];
            $asset = $this->db->table($t['table'])->where($t['pk'], $compId)->get()->getRowArray();
            if (!$asset) {
                continue;
            }

            // Generate doc number for each bundled component
            $compDocNum = $this->saleModel->generateSaleNumber();

            $this->compModel->insert([
                'no_dokumen'          => $compDocNum,
                'asset_type'          => $compType,
                'asset_id'            => $compId,
                'linked_unit_sale_id' => $saleId,
                'tanggal_jual'        => $parentData['tanggal_jual'],
                'nama_pembeli'        => $parentData['nama_pembeli'],
                'alamat_pembeli'      => $parentData['alamat_pembeli'] ?? null,
                'telepon_pembeli'     => $parentData['telepon_pembeli'] ?? null,
                'harga_jual'          => $compPrice,
                'metode_pembayaran'   => $parentData['metode_pembayaran'],
                'no_kwitansi'         => $parentData['no_kwitansi'] ?? null,
                'status'              => 'COMPLETED',
                'previous_status'     => $asset['status'] ?? null,
                'previous_unit_id'    => (int) $unitId,
                'keterangan'          => 'Bundled with unit sale ' . $parentData['no_dokumen'],
                'sold_by_user_id'     => (int) session('user_id'),
            ]);

            // Update component inventory → SOLD + detach
            $this->db->table($t['table'])
                ->where($t['pk'], $compId)
                ->update(['status' => 'SOLD', 'inventory_unit_id' => null, 'updated_at' => date('Y-m-d H:i:s')]);

            $this->logComponentTransfer($compType, $compId, $unitId, null, 'DETACH', 'ASSET_DISPOSAL_BUNDLED');
        }
    }

    /**
     * Detach remaining (unchecked) components from a sold unit → status AVAILABLE
     */
    private function detachRemainingComponents(int $unitId, array $bundled): void
    {
        $bundledIds = ['ATTACHMENT' => [], 'CHARGER' => [], 'BATTERY' => []];
        foreach ($bundled as $comp) {
            $t = strtoupper($comp['type'] ?? '');
            if (isset($bundledIds[$t])) {
                $bundledIds[$t][] = (int) ($comp['id'] ?? 0);
            }
        }

        $tables = [
            'ATTACHMENT' => ['table' => 'inventory_attachments', 'pk' => 'id'],
            'CHARGER'    => ['table' => 'inventory_chargers',    'pk' => 'id'],
            'BATTERY'    => ['table' => 'inventory_batteries',   'pk' => 'id'],
        ];

        foreach ($tables as $compType => $t) {
            $builder = $this->db->table($t['table'])
                ->where('inventory_unit_id', $unitId)
                ->where('status !=', 'SOLD');

            if (!empty($bundledIds[$compType])) {
                $builder->whereNotIn($t['pk'], $bundledIds[$compType]);
            }

            $remaining = $builder->get()->getResultArray();

            foreach ($remaining as $r) {
                $this->db->table($t['table'])
                    ->where($t['pk'], $r[$t['pk']])
                    ->update(['status' => 'AVAILABLE', 'inventory_unit_id' => null, 'updated_at' => date('Y-m-d H:i:s')]);

                $this->logComponentTransfer($compType, (int) $r[$t['pk']], $unitId, null, 'DETACH', 'UNIT_SOLD_DETACH');
            }
        }
    }

    /**
     * Restore component inventory status from a cancelled sale
     */
    private function restoreComponentInventory(array $comp): void
    {
        $tableMap = [
            'ATTACHMENT' => ['table' => 'inventory_attachments', 'pk' => 'id'],
            'CHARGER'    => ['table' => 'inventory_chargers',    'pk' => 'id'],
            'BATTERY'    => ['table' => 'inventory_batteries',   'pk' => 'id'],
        ];

        $t = $tableMap[$comp['asset_type']] ?? null;
        if (!$t) {
            return;
        }

        $updateData = [
            'status'     => $comp['previous_status'] ?? 'AVAILABLE',
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        if (!empty($comp['previous_unit_id'])) {
            $updateData['inventory_unit_id'] = (int) $comp['previous_unit_id'];
        }

        $this->db->table($t['table'])
            ->where($t['pk'], $comp['asset_id'])
            ->update($updateData);
    }

    /**
     * Log component transfer to attachment_transfer_log
     */
    private function logComponentTransfer(string $compType, int $assetId, ?int $fromUnitId, ?int $toUnitId, string $transferType, string $triggeredBy): void
    {
        $typeMap = [
            'ATTACHMENT' => 'attachment',
            'CHARGER'    => 'charger',
            'BATTERY'    => 'battery',
        ];

        $componentType = $typeMap[$compType] ?? null;
        if (!$componentType) {
            return;
        }

        try {
            $this->db->table('attachment_transfer_log')->insert([
                'attachment_id'  => $assetId,
                'component_type' => $componentType,
                'from_unit_id'   => $fromUnitId,
                'to_unit_id'     => $toUnitId,
                'transfer_type'  => $transferType,
                'triggered_by'   => $triggeredBy,
                'notes'          => 'Asset disposal — ' . $triggeredBy,
                'created_by'     => (int) session('user_id'),
                'created_at'     => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            log_message('warning', '[AssetDisposal::logComponentTransfer] ' . $e->getMessage());
        }
    }

    /**
     * Check if document number exists in either table
     */
    private function isDocNumberUsed(string $docNumber): bool
    {
        $inUnit = $this->saleModel->where('no_dokumen', $docNumber)->first();
        if ($inUnit) {
            return true;
        }
        $inComp = $this->compModel->where('no_dokumen', $docNumber)->first();
        return (bool) $inComp;
    }

    /**
     * Badge helper for payment method
     */
    private function getMetodeBadge(string $metode): string
    {
        $map = [
            'CASH'     => 'badge-soft-green',
            'TRANSFER' => 'badge-soft-blue',
            'CEK'      => 'badge-soft-cyan',
            'KREDIT'   => 'badge-soft-orange',
        ];
        $class = $map[$metode] ?? 'badge-soft-gray';
        return '<span class="badge ' . $class . '">' . esc($metode) . '</span>';
    }
}
