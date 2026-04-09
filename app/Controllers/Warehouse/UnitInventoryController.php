<?php

namespace App\Controllers\Warehouse;

use App\Controllers\BaseController;
use App\Models\InventoryUnitModel;
use App\Models\SiloModel;
use App\Traits\ActivityLoggingTrait;
use Config\Database;

class UnitInventoryController extends BaseController
{
    use ActivityLoggingTrait;
    
    protected InventoryUnitModel $inventoryUnitModel;

    public function __construct()
    {
        $this->inventoryUnitModel = new InventoryUnitModel();
        helper(['global_permission', 'simple_rbac', 'form', 'auth']);
    }

    // ──────────────────────────────────────────────────────
    //  INDEX
    // ──────────────────────────────────────────────────────

    public function index()
    {
        $stats              = $this->getDynamicStats();
        $departemen_options = $this->getDepartemenOptions();
        $lookup             = $this->getLookupData();

        return view('warehouse/inventory/unit/index', [
            'title'              => 'Unit Inventory Master',
            'stats'              => $stats,
            'departemen_options' => $departemen_options,
            'tipe_unit_options'  => $lookup['tipe_unit'],
        ]);
    }

    // ──────────────────────────────────────────────────────
    //  DATATABLE AJAX
    // ──────────────────────────────────────────────────────

    public function datatable()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setBody('Akses ditolak');
        }

        try {
            $start            = (int)($this->request->getPost('start')  ?? 0);
            $length           = (int)($this->request->getPost('length') ?? 20);
            $searchValue      = $this->request->getPost('search')['value'] ?? '';
            $statusFilter     = $this->request->getPost('status_unit')    ?: null;
            $departemenFilter = $this->request->getPost('departemen_id')  ?: null;
            $category         = $this->request->getPost('category')       ?: null;

            // Build scope filter for service area access control
            $scopeFilter = null;
            $scope = get_user_area_department_scope();
            if ($scope !== null) {
                $filterMode = $scope['filter_mode'] ?? 'CENTRAL';
                if ($filterMode === 'BRANCH') {
                    $scopeFilter = ['filter_mode' => 'BRANCH', 'area_ids' => $scope['areas'] ?? []];
                } elseif (!empty($scope['departments'])) {
                    $scopeFilter = ['filter_mode' => 'CENTRAL', 'dept_ids' => $scope['departments']];
                }
            }

            // Map broad categories to status ID groups (based on actual status_unit table):
            // 1=AVAILABLE_STOCK, 2=NON_ASSET_STOCK, 3=BOOKED, 4=PREPARATION, 5=READY_TO_DELIVER
            // 6=IN_DELIVERY, 7=RENTAL_ACTIVE, 8=RENTAL_DAILY, 9=TRIAL, 10=BREAKDOWN
            // 11=MAINTENANCE, 12=RETURNED, 13=SOLD, 14=RENTAL_INACTIVE, 15=SPARE, 16=NONAKTIF
            $categoryMap = [
                'stock'          => '1,2,3,9,12,15',  // AVAILABLE_STOCK, NON_ASSET, BOOKED, TRIAL, RETURNED, SPARE
                'rental'         => '7,8,14',          // RENTAL_ACTIVE, RENTAL_DAILY, RENTAL_INACTIVE
                'progress'       => '4,5,6,11',         // PREPARATION, READY_TO_DELIVER, IN_DELIVERY, MAINTENANCE
                'breakdown_sold' => '10,13',            // BREAKDOWN, SOLD
            ];
            // Sub-filter (statusFilter) takes priority over category
            if (!empty($category) && isset($categoryMap[$category]) && empty($statusFilter)) {
                $statusFilter = $categoryMap[$category];
            }

            // orderMap exactly aligned with 8 view columns (indices 0-7)
            $orderMap = [
                0 => 'iu.no_unit',
                1 => 'iu.serial_number',
                2 => 'mu.merk_unit',
                3 => 'd.nama_departemen',
                4 => 'su.status_unit',
                5 => 'iu.lokasi_unit',
                6 => 'iu.created_at',
                7 => 'iu.created_at', // actions column fallback
            ];

            $orderColumnIndex = (int)($this->request->getPost('order')[0]['column'] ?? 6);
            $orderColumn      = $orderMap[$orderColumnIndex] ?? 'iu.created_at';
            $orderDir         = $this->request->getPost('order')[0]['dir'] ?? 'desc';

            $data            = $this->inventoryUnitModel->getDataTable($start, $length, $orderColumn, $orderDir, $searchValue, $statusFilter, $departemenFilter, $scopeFilter);
            $recordsFiltered = $this->inventoryUnitModel->countFiltered($searchValue, $statusFilter, $departemenFilter, $scopeFilter);
            $recordsTotal    = $this->inventoryUnitModel->countAllData();
            $dynamicStats    = $this->getDynamicStats($searchValue, $departemenFilter);

            return $this->response->setJSON([
                'draw'            => intval($this->request->getPost('draw')),
                'recordsTotal'    => (int)$recordsTotal,
                'recordsFiltered' => (int)$recordsFiltered,
                'data'            => $data,
                'stats'           => $dynamicStats,
                'csrf_hash'       => csrf_hash(),
            ]);
        } catch (\Exception $e) {
            log_message('error', '[UnitInventoryController::datatable] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'draw'            => intval($this->request->getPost('draw')),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Terjadi kesalahan pada server. Silakan coba lagi.',
                'csrf_hash'       => csrf_hash(),
            ]);
        }
    }

    // ──────────────────────────────────────────────────────
    //  DETAIL / SHOW
    // ──────────────────────────────────────────────────────

    public function show($id)
    {
        $db = Database::connect();
        $publicToken = $this->ensurePublicViewToken((int) $id);
        $publicViewUrl = $publicToken ? base_url('unit-view/' . $publicToken) : null;

        // Build unit query with safe optional JOINs
        // Wrap entirely in try/catch because optional tables may not exist in DB
        $unit = null;
        try {
            $selectParts = [
                'iu.*',
                'mu.merk_unit, mu.model_unit',
                'CONCAT(IFNULL(tu.tipe,""), " ", IFNULL(tu.jenis,"")) AS nama_tipe_unit',
                'tu.tipe AS unit_tipe, tu.jenis AS unit_jenis',
                'd.nama_departemen AS unit_departemen',
                'su.status_unit AS status_unit_name',
                'cl.location_name AS customer_location_name, cl.city AS customer_city, cl.address AS customer_address',
                'c.customer_name, c.customer_code',
                // Contract summary fields
                'k.id AS kontrak_id',
                'k.no_kontrak',
                'k.rental_type AS kontrak_rental_type',
                'k.tanggal_berakhir AS kontrak_end_date',
                'k.customer_po_number',
                'ku.tanggal_mulai AS ku_start_date',
                'ku.tanggal_selesai AS ku_end_date',
                'ku.is_spare AS ku_is_spare',
            ];

            // Only join optional tables when safe
            $hasTipeMast  = $db->tableExists('tipe_mast');
            $hasMesin     = $db->tableExists('mesin');
            $hasKapasitas = $db->tableExists('kapasitas');
            $hasJenisRoda = $db->tableExists('jenis_roda');
            $hasTipeBan   = $db->tableExists('tipe_ban');
            $hasValve     = $db->tableExists('valve');

            // model_unit stores ban_depan & ban_belakang — always join if mu exists
            $selectParts[] = 'mu.ban_depan, mu.ban_belakang';

            if ($hasTipeMast)  $selectParts[] = 'tm.tipe_mast, tm.tinggi_mast AS mast_tinggi_default';
            if ($hasMesin) {
                $selectParts[] = 'm.merk_mesin, m.model_mesin';
                // Join engine's department as fuel_type fallback
                $selectParts[] = 'dm.nama_departemen AS fuel_type_dept';
            }
            if ($hasKapasitas) $selectParts[] = 'kap.kapasitas_unit AS kapasitas_display';
            if ($hasJenisRoda) $selectParts[] = 'r.tipe_roda AS jenis_roda';
            if ($hasTipeBan)   $selectParts[] = 'tb.tipe_ban';
            if ($hasValve)     $selectParts[] = 'vl.jumlah_valve';

            $builder = $db->table('inventory_unit iu')
                ->select(implode(', ', $selectParts))
                ->join('model_unit mu',         'mu.id_model_unit = iu.model_unit_id',        'left')
                ->join('tipe_unit tu',           'tu.id_tipe_unit  = iu.tipe_unit_id',         'left')
                ->join('departemen d',           'd.id_departemen  = iu.departemen_id',        'left')
                ->join('status_unit su',         'su.id_status     = iu.status_unit_id',       'left')
                // Updated: JOIN customers via kontrak_unit junction table (source of truth)
                ->join('kontrak_unit ku',        'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k',              'k.id = ku.kontrak_id', 'left')
                ->join('customers c',            'c.id = k.customer_id',          'left')
                ->join('customer_locations cl',  'cl.id = ku.customer_location_id', 'left');

            if ($hasTipeMast)  $builder->join('tipe_mast tm', 'tm.id_mast       = iu.model_mast_id',     'left');
            if ($hasMesin) {
                $builder->join('mesin m', 'm.id = iu.model_mesin_id', 'left');
                $builder->join('departemen dm', 'dm.id_departemen = m.departemen_id', 'left');
            }
            if ($hasKapasitas) $builder->join('kapasitas kap','kap.id_kapasitas = iu.kapasitas_unit_id', 'left');
            if ($hasJenisRoda) $builder->join('jenis_roda r', 'r.id_roda        = iu.roda_id',           'left');
            if ($hasTipeBan)   $builder->join('tipe_ban tb',  'tb.id_ban        = iu.ban_id',            'left');
            if ($hasValve)     $builder->join('valve vl',     'vl.id_valve      = iu.valve_id',          'left');

            $unit = $builder->where('iu.id_inventory_unit', (int)$id)->get()->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'UnitInventoryController::show unit query: ' . $e->getMessage());
            // Fallback to bare query
            try {
                $unit = $db->table('inventory_unit iu')
                    ->select('iu.*, su.status_unit AS status_unit_name,
                               mu.merk_unit, mu.model_unit, mu.ban_depan, mu.ban_belakang,
                               tu.tipe AS unit_tipe, tu.jenis AS unit_jenis,
                               CONCAT(IFNULL(tu.tipe,""), " ", IFNULL(tu.jenis,"")) AS nama_tipe_unit,
                               d.nama_departemen AS unit_departemen')
                    ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
                    ->join('model_unit mu',  'mu.id_model_unit = iu.model_unit_id', 'left')
                    ->join('tipe_unit tu',   'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
                    ->join('departemen d',   'd.id_departemen = iu.departemen_id', 'left')
                    ->where('iu.id_inventory_unit', (int)$id)
                    ->get()->getRowArray();
            } catch (\Throwable $e2) {
                log_message('error', 'UnitInventoryController::show fallback query: ' . $e2->getMessage());
            }
        }

        if (!$unit) {
            return redirect()->to('/warehouse/inventory/unit')->with('error', 'Unit tidak ditemukan.');
        }

        // Service area access control — restrict what BRANCH/CENTRAL users can view
        $scope = get_user_area_department_scope();
        if ($scope !== null) {
            $filterMode = $scope['filter_mode'] ?? 'CENTRAL';
            if ($filterMode === 'BRANCH') {
                $unitAreaId = (int)($unit['area_id'] ?? 0);
                $allowedAreas = array_map('intval', $scope['areas'] ?? []);
                if (empty($allowedAreas) || (!empty($allowedAreas) && !in_array($unitAreaId, $allowedAreas, true))) {
                    return redirect()->to('/warehouse/inventory/unit')->with('error', 'Akses ditolak: Unit tidak berada di Service Area Anda.');
                }
            } elseif ($filterMode === 'CENTRAL') {
                $unitDeptId = (int)($unit['departemen_id'] ?? 0);
                $allowedDepts = array_map('intval', $scope['departments'] ?? []);
                if (!empty($allowedDepts) && !in_array($unitDeptId, $allowedDepts, true)) {
                    return redirect()->to('/warehouse/inventory/unit')->with('error', 'Akses ditolak: Unit tidak berada di Departemen Anda.');
                }
            }
        }

        // ── Work Orders ─────────────────────────────────────
        $work_orders = [];
        try {
            $work_orders = $db->table('work_orders wo')
                ->select('wo.work_order_number   as wo_number,
                          wo.report_date          as date,
                          woc.category_name       as type,
                          wos.status_name         as status,
                          wos.status_color        as status_color,
                          CONCAT(u.first_name, " ", u.last_name) as technician')
                ->join('work_order_categories woc', 'woc.id = wo.category_id', 'left')
                ->join('work_order_statuses   wos', 'wos.id = wo.status_id',   'left')
                ->join('users u',                   'u.id   = wo.mechanic_id', 'left')
                ->where('wo.unit_id', (int)$id)
                ->where('wo.deleted_at IS NULL')
                ->orderBy('wo.report_date', 'DESC')
                ->limit(25)
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::show work_orders: ' . $e->getMessage());
        }

        // ── Sparepart Usages ─────────────────────────────────
        $sparepart_usages = [];
        try {
            $sparepart_usages = $db->table('work_order_spareparts wsp')
                ->select('wsp.sparepart_name  as part_name,
                          wsp.quantity_used   as qty,
                          wsp.satuan          as uom,
                          wo.work_order_number as wo_ref,
                          wo.report_date       as date')
                ->join('work_orders wo', 'wo.id = wsp.work_order_id', 'left')
                ->where('wo.unit_id', (int)$id)
                ->where('wo.deleted_at IS NULL')
                ->orderBy('wo.report_date', 'DESC')
                ->limit(30)
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::show spareparts: ' . $e->getMessage());
        }

        // ── Rental/Contract History ───────────────────────────
        $rental_history = [];
        try {
            $rental_history = $db->table('kontrak_unit ku')
                ->select('k.no_kontrak       as contract_no,
                          k.id              as kontrak_id,
                          k.rental_type     as rental_type,
                          c.customer_name   as customer,
                          cl.location_name  as location,
                          cl.city           as location_city,
                          ku.tanggal_mulai  as start_date,
                          ku.tanggal_selesai as end_date,
                          ku.is_spare       as is_spare,
                          ku.status')
                ->join('kontrak k',              'k.id   = ku.kontrak_id',              'left')
                ->join('customers c',            'c.id   = k.customer_id',              'left')
                ->join('customer_locations cl',  'cl.id  = ku.customer_location_id',   'left')
                ->where('ku.unit_id', (int)$id)
                ->orderBy('ku.tanggal_mulai', 'DESC')
                ->get()->getResultArray();
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::show rental_history: ' . $e->getMessage());
        }

        // ── SPK History ───────────────────────────────────────
        $spk_history = [];
        try {
            if ($db->tableExists('spk_unit_stages') && $db->tableExists('spk')) {
                // Avoid sus.status conflict - use column names that actually exist
                $spk_history = $db->table('spk_unit_stages sus')
                    ->select('s.nomor_spk, s.pelanggan, s.dibuat_pada AS spk_date, sus.stage_notes, sus.created_at AS stage_date')
                    ->join('spk s', 's.id = sus.spk_id', 'left')
                    ->where('sus.unit_id', (int)$id)
                    ->orderBy('sus.created_at', 'DESC')
                    ->limit(10)
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::show spk_history: ' . $e->getMessage());
            $spk_history = [];
        }

        // ── Current components (attachment, charger, battery) ─────────────
        $current_components = ['battery' => null, 'charger' => null, 'attachment' => null];
        try {
            $compHelper = new \App\Models\InventoryComponentHelper();
            $current_components = $compHelper->getUnitComponents((int)$id);
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::show current_components: ' . $e->getMessage());
        }

        // ── Active Booking ─────────────────────────────────────────────────
        $active_booking = null;
        try {
            if ($db->tableExists('unit_bookings')) {
                $active_booking = $db->table('unit_bookings ub')
                    ->select('ub.*, c.customer_name, c.customer_code,
                              q.quotation_number, q.prospect_name AS quotation_prospect,
                              CONCAT(IFNULL(u.first_name,""), " ", IFNULL(u.last_name,"")) AS booked_by_name')
                    ->join('customers c',   'c.id = ub.customer_id',         'left')
                    ->join('quotations q',  'q.id_quotation = ub.quotation_id', 'left')
                    ->join('users u',       'u.id = ub.booked_by_user_id',   'left')
                    ->where('ub.unit_id', (int)$id)
                    ->where('ub.status', 'ACTIVE')
                    ->orderBy('ub.booked_at', 'DESC')
                    ->limit(1)
                    ->get()->getRowArray();
            }
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::show active_booking: ' . $e->getMessage());
        }

        // Lookup data for inline edit
        $lookup = $this->getLookupData();
        $silo = null;
        try {
            $siloModel = new SiloModel();
            $silo = $siloModel->getByUnitId((int) $id);
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::show silo: ' . $e->getMessage());
        }

        return view('warehouse/inventory/unit/show', [
            'title'            => 'Detail Unit: ' . ($unit['no_unit'] ?: ($unit['no_unit_na'] ?: 'TEMP-' . $id)),
            'unit'             => $unit,
            'work_orders'      => $work_orders,
            'sparepart_usages' => $sparepart_usages,
            'rental_history'   => $rental_history,
            'spk_history'      => $spk_history,
            'current_components' => $current_components,
            // For inline edit
            'tipe_mast'        => $lookup['tipe_mast']      ?? [],
            'mesin'            => $lookup['mesin']           ?? [],
            'tipe_ban'         => $lookup['ban']             ?? [],
            'kapasitas'        => $lookup['kapasitas_unit']  ?? [],
            'jenis_roda'       => $lookup['roda']            ?? [],
            'valve'            => $lookup['valve']           ?? [],
            'active_booking'   => $active_booking,
            'silo'             => $silo,
            'public_view_url'  => $publicViewUrl,
        ]);
    }

    /**
     * Public unit view by token for external mechanics (no login).
     */
    public function publicView(string $token)
    {
        $token = trim($token);
        if ($token === '') {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $db = Database::connect();
        if (! $db->fieldExists('public_view_token', 'inventory_unit')) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $selectParts = [
            'iu.*',
            'su.status_unit',
            'mu.merk_unit, mu.model_unit, mu.ban_depan, mu.ban_belakang',
            'tu.tipe, tu.jenis',
            'd.nama_departemen AS unit_departemen',
            'c.customer_name, c.customer_code',
            'cl.location_name AS customer_location_name',
            'cl.city AS customer_city',
            'cl.address AS customer_address',
            'k.no_kontrak',
            'ku.tanggal_mulai AS kontrak_mulai',
            'ku.tanggal_selesai AS kontrak_selesai',
        ];

        $hasTipeMast  = $db->tableExists('tipe_mast');
        $hasMesin     = $db->tableExists('mesin');
        $hasKapasitas = $db->tableExists('kapasitas');
        $hasJenisRoda = $db->tableExists('jenis_roda');
        $hasTipeBan   = $db->tableExists('tipe_ban');
        $hasValve     = $db->tableExists('valve');
        $hasAreas     = $db->tableExists('areas');

        if ($hasTipeMast)  $selectParts[] = 'tm.tipe_mast, tm.tinggi_mast AS mast_tinggi_default';
        if ($hasMesin) {
            $selectParts[] = 'm.merk_mesin, m.model_mesin';
            $selectParts[] = 'dm.nama_departemen AS fuel_type_dept';
        }
        if ($hasKapasitas) $selectParts[] = 'kap.kapasitas_unit AS kapasitas_display';
        if ($hasJenisRoda) $selectParts[] = 'r.tipe_roda AS jenis_roda';
        if ($hasTipeBan)   $selectParts[] = 'tb.tipe_ban';
        if ($hasValve)     $selectParts[] = 'vl.jumlah_valve';
        if ($hasAreas) {
            if ($db->fieldExists('area_name', 'areas')) {
                $selectParts[] = 'a.area_name AS area_name';
            } elseif ($db->fieldExists('nama_area', 'areas')) {
                $selectParts[] = 'a.nama_area AS area_name';
            } elseif ($db->fieldExists('name', 'areas')) {
                $selectParts[] = 'a.name AS area_name';
            } else {
                $selectParts[] = 'CAST(iu.area_id AS CHAR) AS area_name';
            }
        } else {
            $selectParts[] = 'CAST(iu.area_id AS CHAR) AS area_name';
        }

        $builder = $db->table('inventory_unit iu')
            ->select(implode(', ', $selectParts))
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left')
            ->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left')
            ->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left');
        if ($hasAreas) $builder->join('areas a', 'a.id = iu.area_id', 'left');

        if ($hasTipeMast)  $builder->join('tipe_mast tm', 'tm.id_mast = iu.model_mast_id', 'left');
        if ($hasMesin) {
            $builder->join('mesin m', 'm.id = iu.model_mesin_id', 'left');
            $builder->join('departemen dm', 'dm.id_departemen = m.departemen_id', 'left');
        }
        if ($hasKapasitas) $builder->join('kapasitas kap', 'kap.id_kapasitas = iu.kapasitas_unit_id', 'left');
        if ($hasJenisRoda) $builder->join('jenis_roda r', 'r.id_roda = iu.roda_id', 'left');
        if ($hasTipeBan)   $builder->join('tipe_ban tb', 'tb.id_ban = iu.ban_id', 'left');
        if ($hasValve)     $builder->join('valve vl', 'vl.id_valve = iu.valve_id', 'left');

        $unit = $builder
            ->where('iu.public_view_token', $token)
            ->get()
            ->getRowArray();

        if (! $unit) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $events = [];
        try {
            $service = new \App\Services\UnitActivityService();
            $events = $service->getUnifiedTimeline((int) $unit['id_inventory_unit'], null, 120);
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::publicView activity: ' . $e->getMessage());
        }

        $silo = null;
        try {
            $siloModel = new SiloModel();
            $silo = $siloModel->getByUnitId((int) $unit['id_inventory_unit']);
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::publicView silo: ' . $e->getMessage());
        }

        $currentComponents = ['battery' => null, 'charger' => null, 'attachment' => null];
        try {
            $compHelper = new \App\Models\InventoryComponentHelper();
            $currentComponents = $compHelper->getUnitComponents((int)$unit['id_inventory_unit']);
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::publicView current_components: ' . $e->getMessage());
        }

        $aksesorisItems = [];
        try {
            $aksesorisRaw = $unit['aksesoris'] ?? null;
            if ($aksesorisRaw) {
                $decoded = json_decode($aksesorisRaw, true);
                if (is_array($decoded)) {
                    $isAssoc = array_keys($decoded) !== range(0, count($decoded) - 1);
                    foreach ($decoded as $k => $v) {
                        if ($isAssoc) {
                            if ($v) $aksesorisItems[] = format_accessory_label($k);
                        } else {
                            if ($v && !is_bool($v)) $aksesorisItems[] = format_accessory_label($v);
                        }
                    }
                } else {
                    $rawItems = array_values(array_filter(array_map('trim', explode(',', $aksesorisRaw))));
                    $aksesorisItems = array_map(static fn ($item) => format_accessory_label($item), $rawItems);
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', 'UnitInventoryController::publicView aksesoris: ' . $e->getMessage());
        }

        return view('public/unit_scan', [
            'title' => 'Unit View',
            'unit' => $unit,
            'events' => $events,
            'silo' => $silo,
            'current_components' => $currentComponents,
            'aksesorisItems' => $aksesorisItems,
        ]);
    }

    // ──────────────────────────────────────────────────────
    //  INLINE SPEC UPDATE (AJAX from show page)
    // ──────────────────────────────────────────────────────

    public function inlineUpdate($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Request tidak valid. Harap kirim data melalui form yang benar.'])->setStatusCode(400);
        }

        $unitId = (int)$id;
        $model  = new \App\Models\InventoryUnitModel();

        // Only allow these spec fields to be changed inline
        $allowed = [
            'model_mast_id', 'tinggi_mast', 'sn_mast',
            'model_mesin_id', 'sn_mesin',
            'ban_id', 'roda_id', 'valve_id',
            'kapasitas_unit_id',
            'fuel_type', 'ownership_status',
            'hour_meter',
            'keterangan',
        ];

        $data = [];
        foreach ($allowed as $field) {
            $val = $this->request->getPost($field);
            if ($val !== null) {
                $data[$field] = $val === '' ? null : $val;
            }
        }

        if (empty($data)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tidak ada data untuk disimpan.']);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');

        try {
            if ($model->update($unitId, $data)) {
                return $this->response->setJSON([
                    'success'    => true,
                    'message'    => 'Spesifikasi unit berhasil diperbarui.',
                    'csrf_hash'  => csrf_hash(),
                ]);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan perubahan.', 'csrf_hash' => csrf_hash()]);
        } catch (\Throwable $e) {
            log_message('error', 'inlineUpdate unit #' . $unitId . '. Silakan coba lagi.');
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada server. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
        }
    }

    // ──────────────────────────────────────────────────────
    //  MOVEMENT HISTORY AJAX (Pergerakan tab)
    // ──────────────────────────────────────────────────────

    public function getMovementHistory($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setBody('Bad Request');
        }

        $db     = Database::connect();
        $unitId = (int)$id;
        $events = [];

        // ── Source 1: Delivery Instructions via delivery_items ──────────
        try {
            if ($db->tableExists('delivery_items') && $db->tableExists('delivery_instructions')) {
                $rows = $db->table('delivery_items ditem')
                    ->select('
                        "delivery"                          AS type,
                        COALESCE(di.tanggal_kirim, di.dibuat_pada) AS event_date,
                        di.nomor_di                         AS reference,
                        di.lokasi                           AS location,
                        di.pelanggan                        AS customer,
                        di.status_di                        AS status,
                        di.nama_supir                       AS driver,
                        di.kendaraan                        AS vehicle,
                        di.no_polisi_kendaraan              AS plate,
                        di.berangkat_tanggal_approve        AS depart_date,
                        di.sampai_tanggal_approve           AS arrive_date
                    ')
                    ->join('delivery_instructions di', 'di.id = ditem.di_id', 'inner')
                    ->where('ditem.unit_id', $unitId)
                    ->where('ditem.item_type', 'UNIT')
                    ->orderBy('event_date', 'DESC')
                    ->get()->getResultArray();

                foreach ($rows as $r) {
                    $statusLabel = match(strtoupper($r['status'] ?? '')) {
                        'SELESAI', 'COMPLETED'   => ['Selesai',         'success'],
                        'DALAM_PERJALANAN'        => ['Dalam Perjalanan','primary'],
                        'SAMPAI_LOKASI'           => ['Sampai Lokasi',  'info'],
                        'SIAP_KIRIM'              => ['Siap Kirim',     'warning'],
                        'PERSIAPAN_UNIT'          => ['Persiapan Unit', 'warning'],
                        'DIAJUKAN'                => ['Diajukan',       'secondary'],
                        'DIBATALKAN', 'CANCELLED' => ['Dibatalkan',     'danger'],
                        default                   => [esc($r['status'] ?? '-'), 'secondary'],
                    };
                    $events[] = [
                        'type'       => 'delivery',
                        'icon'       => 'fa-truck',
                        'color'      => 'primary',
                        'event_date' => $r['event_date'],
                        'title'      => 'Dikirim ke ' . ($r['location'] ?: '-'),
                        'subtitle'   => $r['customer'] ?: '-',
                        'reference'  => $r['reference'] ?: '—',
                        'status'     => $statusLabel[0],
                        'status_cls' => $statusLabel[1],
                        'meta'       => array_filter([
                            'Driver'        => $r['driver']  ?: null,
                            'Kendaraan'     => trim(($r['vehicle'] ?? '') . ' ' . ($r['plate'] ?? '')) ?: null,
                            'Tgl Berangkat' => !empty($r['depart_date']) ? date('d M Y', strtotime($r['depart_date'])) : null,
                            'Tgl Tiba'      => !empty($r['arrive_date']) ? date('d M Y', strtotime($r['arrive_date'])) : null,
                        ]),
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', 'getMovementHistory delivery: ' . $e->getMessage());
        }

        // ── Source 2: kontrak_unit (rental deployment periods) ───────────
        try {
            if ($db->tableExists('kontrak_unit')) {
                $hasKontrak   = $db->tableExists('kontrak');
                $hasLocations = $db->tableExists('customer_locations');
                $hasCustomers = $db->tableExists('customers');

                $b = $db->table('kontrak_unit ku')
                    ->select('
                        ku.tanggal_mulai   AS event_date,
                        ku.tanggal_selesai AS end_date,
                        ku.tanggal_tarik   AS tarik_date,
                        ku.status,
                        ' . ($hasKontrak   ? 'k.no_kontrak        AS reference,' : '"—" AS reference,')     . '
                        ' . ($hasLocations && $hasKontrak ? 'cl.location_name AS location,' : '"—" AS location,') . '
                        ' . ($hasCustomers && $hasKontrak ? 'c.customer_name  AS customer'  : '"—" AS customer')  . '
                    ');

                if ($hasKontrak)                     $b->join('kontrak k',             'k.id  = ku.kontrak_id',            'left');
                if ($hasCustomers && $hasKontrak)    $b->join('customers c',           'c.id  = k.customer_id',            'left');

                $rows = $b->where('ku.unit_id', $unitId)
                    ->orderBy('ku.tanggal_mulai', 'DESC')
                    ->get()->getResultArray();

                foreach ($rows as $r) {
                    [$sLabel, $sCls] = match(strtoupper($r['status'] ?? '')) {
                        'AKTIF'        => ['Aktif',       'success'],
                        'DITARIK'      => ['Ditarik',     'secondary'],
                        'DITUKAR'      => ['Ditukar',     'warning'],
                        'NON_AKTIF'    => ['Non-Aktif',   'secondary'],
                        'MAINTENANCE'  => ['Maintenance', 'danger'],
                        'UNDER_REPAIR' => ['Breakdown','danger'],
                        default        => [$r['status'] ?? '-', 'secondary'],
                    };
                    $meta = [];
                    if (!empty($r['end_date']))   $meta['Tgl Selesai'] = date('d M Y', strtotime($r['end_date']));
                    if (!empty($r['tarik_date'])) $meta['Tgl Tarik']   = date('d M Y', strtotime($r['tarik_date']));

                    $events[] = [
                        'type'       => 'rental',
                        'icon'       => 'fa-file-contract',
                        'color'      => 'warning',
                        'event_date' => $r['event_date'],
                        'title'      => 'Rental Aktif — ' . ($r['location'] ?: 'Lokasi belum diketahui'),
                        'subtitle'   => $r['customer'] ?: '-',
                        'reference'  => $r['reference'] ?: '—',
                        'status'     => $sLabel,
                        'status_cls' => $sCls,
                        'meta'       => $meta,
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', 'getMovementHistory rental: ' . $e->getMessage());
        }

        // ── Source 3: unit_timeline DELIVERY / LOCATION events ───────────
        try {
            if ($db->tableExists('unit_timeline')) {
                $rows = $db->table('unit_timeline ut')
                    ->select('ut.event_category, ut.event_title, ut.event_description, ut.performed_at AS event_date, ut.reference_id AS reference')
                    ->whereIn('ut.event_category', ['DELIVERY', 'LOCATION'])
                    ->where('ut.unit_id', $unitId)
                    ->orderBy('ut.performed_at', 'DESC')
                    ->get()->getResultArray();

                foreach ($rows as $r) {
                    $isDel = $r['event_category'] === 'DELIVERY';
                    $events[] = [
                        'type'       => strtolower($r['event_category']),
                        'icon'       => $isDel ? 'fa-shipping-fast' : 'fa-map-marker-alt',
                        'color'      => $isDel ? 'info' : 'success',
                        'event_date' => $r['event_date'],
                        'title'      => $r['event_title'] ?: ($isDel ? 'Event Pengiriman' : 'Perpindahan Lokasi'),
                        'subtitle'   => $r['event_description'] ?: '',
                        'reference'  => $r['reference'] ?: '—',
                        'status'     => 'Tercatat',
                        'status_cls' => 'secondary',
                        'meta'       => [],
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', 'getMovementHistory timeline: ' . $e->getMessage());
        }

        // ── Source 4: component_audit_log (component attach/detach/transfer) ───────────
        try {
            if ($db->tableExists('component_audit_log')) {
                $rows = $db->table('component_audit_log cal')
                    ->select('cal.*, CONCAT(u.first_name, " ", u.last_name) as actor_name')
                    ->join('users u', 'u.id = cal.performed_by', 'left')
                    ->groupStart()
                        ->where('cal.from_unit_id', $unitId)
                        ->orWhere('cal.to_unit_id', $unitId)
                    ->groupEnd()
                    ->orderBy('cal.performed_at', 'DESC')
                    ->limit(50)
                    ->get()->getResultArray();

                foreach ($rows as $r) {
                    $isIncoming = (int)($r['to_unit_id'] ?? 0) === $unitId;
                    $typeIcon = match(strtoupper($r['component_type'] ?? '')) {
                        'BATTERY' => 'fa-car-battery',
                        'CHARGER' => 'fa-plug',
                        'ATTACHMENT' => 'fa-puzzle-piece',
                        default => 'fa-cog'
                    };
                    $events[] = [
                        'type'       => 'component',
                        'icon'       => $typeIcon,
                        'color'      => $isIncoming ? 'success' : 'warning',
                        'event_date' => $r['performed_at'],
                        'title'      => ($r['event_title'] ?? ucfirst(strtolower($r['component_type'] ?? 'Component'))) . ' ' . strtolower($r['event_type'] ?? ''),
                        'subtitle'   => $r['notes'] ?? '',
                        'reference'  => $r['triggered_by'] ?? '—',
                        'status'     => ucfirst(strtolower($r['event_type'] ?? 'Unknown')),
                        'status_cls' => $isIncoming ? 'success' : 'warning',
                        'meta'       => [
                            'component_type' => $r['component_type'],
                            'component_id' => $r['component_id'],
                            'actor' => $r['actor_name'] ?? 'System',
                        ],
                    ];
                }
            }
        } catch (\Throwable $e) {
            log_message('warning', 'getMovementHistory component_audit_log: ' . $e->getMessage());
        }

        // ── Merge & sort all events chronologically ───────────────────────
        usort($events, fn($a, $b) => strcmp($b['event_date'] ?? '', $a['event_date'] ?? ''));

        return $this->response->setJSON([
            'success' => true,
            'total'   => count($events),
            'events'  => $events,
        ]);
    }

    // ──────────────────────────────────────────────────────
    //  ACTIVITY (unified timeline for tab Aktivitas)
    // ──────────────────────────────────────────────────────

    public function getActivity($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setBody('Bad Request');
        }

        try {
            $unitId = (int)$id;
            $category = $this->request->getGet('category') ?: null;
            $limit = (int)($this->request->getGet('limit') ?? 100);

            $service = new \App\Services\UnitActivityService();
            $events = $service->getUnifiedTimeline($unitId, $category, $limit);

            return $this->response->setJSON([
                'success' => true,
                'events'  => $events,
            ]);
        } catch (\Throwable $e) {
            log_message('error', '[UnitInventoryController::getActivity] ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error'   => $e->getMessage(),
                'events'  => [],
            ]);
        }
    }

    //  TIMELINE AJAX (sidebar)
    // ──────────────────────────────────────────────────────

    public function getTimeline($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setBody('Bad Request');
        }

        try {
            $db     = Database::connect();
            $unitId = (int)$id;
            $allEvents = [];
            
            // Source 1: unit_timeline
            $events = $db->table('unit_timeline ut')
                ->select('ut.*, CONCAT(u.first_name, " ", u.last_name) as actor_name')
                ->join('users u', 'u.id = ut.performed_by', 'left')
                ->where('ut.unit_id', $unitId)
                ->orderBy('ut.performed_at', 'DESC')
                ->limit(40)
                ->get()->getResultArray();

            foreach ($events as $ev) {
                $allEvents[] = [
                    'source' => 'unit_timeline',
                    'event_category' => $ev['event_category'] ?? 'STATUS',
                    'event_title' => $ev['event_title'] ?? '',
                    'event_description' => $ev['event_description'] ?? '',
                    'performed_at' => $ev['performed_at'] ?? '',
                    'actor_name' => $ev['actor_name'] ?? 'System',
                ];
            }

            // Source 2: component_audit_log
            if ($db->tableExists('component_audit_log')) {
                $componentEvents = $db->table('component_audit_log cal')
                    ->select('cal.*, CONCAT(u.first_name, " ", u.last_name) as actor_name')
                    ->join('users u', 'u.id = cal.performed_by', 'left')
                    ->groupStart()
                        ->where('cal.from_unit_id', $unitId)
                        ->orWhere('cal.to_unit_id', $unitId)
                    ->groupEnd()
                    ->orderBy('cal.performed_at', 'DESC')
                    ->limit(30)
                    ->get()->getResultArray();

                foreach ($componentEvents as $ce) {
                    $isIncoming = (int)($ce['to_unit_id'] ?? 0) === $unitId;
                    $componentLabel = ucfirst(strtolower($ce['component_type'] ?? 'Component'));
                    $eventLabel = strtolower($ce['event_type'] ?? 'updated');
                    $allEvents[] = [
                        'source' => 'component_audit_log',
                        'event_category' => 'COMPONENT',
                        'event_title' => "{$componentLabel} {$eventLabel}" . ($isIncoming ? ' (dipasang)' : ' (dilepas)'),
                        'event_description' => $ce['notes'] ?? ($ce['triggered_by'] ?? ''),
                        'performed_at' => $ce['performed_at'] ?? '',
                        'actor_name' => $ce['actor_name'] ?? 'System',
                        'component_type' => $ce['component_type'],
                    ];
                }
            }

            // Sort all events by performed_at descending
            usort($allEvents, fn($a, $b) => strcmp($b['performed_at'] ?? '', $a['performed_at'] ?? ''));
            $allEvents = array_slice($allEvents, 0, 50);

            if (empty($allEvents)) {
                return $this->response->setJSON([
                    'success' => true,
                    'html'    => '<div class="text-center text-muted py-3 fst-italic small">No events recorded yet.</div>',
                ]);
            }

            $catMeta = [
                'CONTRACT'   => ['fa-file-contract', 'primary'],
                'DELIVERY'   => ['fa-truck',          'info'],
                'SERVICE'    => ['fa-tools',          'warning'],
                'MAINTENANCE'=> ['fa-wrench',         'danger'],
                'COMPONENT'  => ['fa-puzzle-piece',   'dark'],
                'STATUS'     => ['fa-tag',            'secondary'],
                'LOCATION'   => ['fa-map-marker-alt', 'success'],
                'HOUR_METER' => ['fa-tachometer-alt', 'info'],
                'PURCHASE'   => ['fa-shopping-cart',  'success'],
                'DISPOSAL'   => ['fa-trash',          'danger'],
                'FINANCIAL'  => ['fa-money-bill',     'success'],
            ];

            $html = '<ul class="list-unstyled mb-0">';
            foreach ($allEvents as $ev) {
                $cat   = strtoupper($ev['event_category'] ?? 'STATUS');
                
                // For component events, use specific icon based on component_type
                if ($cat === 'COMPONENT' && !empty($ev['component_type'])) {
                    $ico = match(strtoupper($ev['component_type'])) {
                        'BATTERY' => 'fa-car-battery',
                        'CHARGER' => 'fa-plug',
                        'ATTACHMENT' => 'fa-puzzle-piece',
                        default => 'fa-cog'
                    };
                    $col = 'dark';
                } else {
                    [$ico, $col] = $catMeta[$cat] ?? ['fa-circle', 'secondary'];
                }
                
                $time  = !empty($ev['performed_at']) ? date('d M Y H:i', strtotime($ev['performed_at'])) : '—';
                $actor = !empty($ev['actor_name']) ? esc($ev['actor_name']) : 'System';
                $html .= '<li class="d-flex gap-2 mb-3 align-items-start">';
                $html .= '<div class="flex-shrink-0 mt-1"><span class="badge rounded-circle bg-' . $col . ' p-2"><i class="fas ' . $ico . ' fa-xs"></i></span></div>';
                $html .= '<div class="flex-grow-1">';
                $html .= '<p class="mb-0 fw-semibold small lh-sm">' . esc($ev['event_title']) . '</p>';
                if (!empty($ev['event_description'])) {
                    $html .= '<p class="mb-0 text-muted" style="font-size:.78rem">' . esc($ev['event_description']) . '</p>';
                }
                $html .= '<small class="text-muted"><i class="fas fa-clock me-1"></i>' . $time . ' &bull; ' . $actor . '</small>';
                $html .= '</div></li>';
            }
            $html .= '</ul>';

            return $this->response->setJSON(['success' => true, 'html' => $html]);

        } catch (\Throwable $e) {
            log_message('error', 'UnitInventoryController::getTimeline: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'html' => '<div class="text-muted small py-2 text-center">Could not load events.</div>']);
        }
    }

    // ──────────────────────────────────────────────────────
    //  CREATE / STORE
    // ──────────────────────────────────────────────────────

    public function create()
    {
        return view('warehouse/inventory/unit/create', array_merge(
            $this->getLookupData(),
            ['title' => 'Tambah Unit Baru', 'unit' => null]
        ));
    }

    public function store()
    {
        if (!$this->validate(['status_unit_id' => 'required|integer', 'serial_number' => 'permit_empty|max_length[255]'])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->collectFormFields();

        try {
            $id = $this->inventoryUnitModel->insert($data, true);
            if (!$id) {
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan unit.');
            }
            if (empty($data['no_unit']) && empty($data['no_unit_na'])) {
                try {
                    $na = $this->inventoryUnitModel->generateNonAssetNumber();
                    $this->inventoryUnitModel->update($id, ['no_unit_na' => $na]);
                } catch (\Throwable $e) {
                    log_message('warning', 'Auto-assign NA number failed. Silakan coba lagi.');
                }
            }
            
            // Log to system_activity_log
            $unitNumber = $data['no_unit'] ?? $data['no_unit_na'] ?? "ID #{$id}";
            $this->logCreate('inventory_unit', $id, $data, [
                'module_name' => 'warehouse',
                'description' => "Unit {$unitNumber} created",
                'business_impact' => 'MEDIUM',
            ]);
            
            return redirect()->to("warehouse/inventory/unit/{$id}")->with('success', 'Unit berhasil ditambahkan.');
        } catch (\Throwable $e) {
            log_message('error', 'UnitInventoryController::store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan pada server. Silakan coba lagi.');
        }
    }

    // ──────────────────────────────────────────────────────
    //  PRINT VIEW
    // ──────────────────────────────────────────────────────

    public function printUnit($id)
    {
        $db  = Database::connect();
        $publicToken = $this->ensurePublicViewToken((int) $id);
        $publicViewUrl = $publicToken ? base_url('unit-view/' . $publicToken) : null;

        // Build SELECT with safe optional JOINs
        try {
            $b = $db->table('inventory_unit iu')
                ->select('iu.*,
                    mu.merk_unit, mu.model_unit,
                    tu.tipe AS nama_tipe_unit, tu.jenis,
                    su.status_unit AS status_unit_name,
                    dep.nama_departemen,
                    c.customer_name, cl.location_name AS customer_location_name')
                ->join('model_unit mu',        'mu.id_model_unit = iu.model_unit_id',       'left')
                ->join('tipe_unit tu',          'tu.id_tipe_unit  = iu.tipe_unit_id',        'left')
                ->join('status_unit su',        'su.id_status      = iu.status_unit_id',     'left')
                ->join('departemen dep',        'dep.id_departemen = iu.departemen_id',       'left')
                // Updated: JOIN customers via kontrak_unit junction table (source of truth)
                ->join('kontrak_unit ku',       'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left')
                ->join('kontrak k',             'k.id = ku.kontrak_id', 'left')
                ->join('customers c',           'c.id = k.customer_id',         'left');

            if ($db->tableExists('kapasitas')) {
                $b->select('kap.kapasitas_unit AS kapasitas_display')
                  ->join('kapasitas kap', 'kap.id_kapasitas = iu.kapasitas_unit_id', 'left');
            }
            if ($db->tableExists('tipe_mast')) {
                $b->select('mm.tipe_mast')
                  ->join('tipe_mast mm', 'mm.id_mast = iu.model_mast_id', 'left');
            }
            if ($db->tableExists('mesin')) {
                $b->select('me.merk_mesin, me.model_mesin')
                  ->join('mesin me', 'me.id = iu.model_mesin_id', 'left');
            }

            $row = $b->where('iu.id_inventory_unit', (int)$id)->get()->getRowArray();
        } catch (\Throwable $e) {
            log_message('error', 'printUnit query error: ' . $e->getMessage());
            $row = $db->table('inventory_unit')->where('id_inventory_unit', (int)$id)->get()->getRowArray();
        }

        if (!$row) {
            return redirect()->to('/warehouse/inventory/unit')->with('error', 'Unit not found.');
        }

        // Parse accessories
        $aksesorisRaw   = $row['aksesoris'] ?? null;
        $aksesorisMap   = [
            'rotary_lamp'  => 'Rotary Lamp',   'back_buzzer'  => 'Back Buzzer',
            'mirror'       => 'Mirror',         'lampu_sorot'  => 'Work Light',
            'fire_ext'     => 'Fire Extinguisher', 'safety_belt' => 'Seat Belt',
            'horn'         => 'Horn',           'strobe_light' => 'Strobe Light',
        ];
        $aksesorisItems = [];
        if ($aksesorisRaw) {
            $decoded = json_decode($aksesorisRaw, true);
            if (is_array($decoded)) {
                foreach ($decoded as $k => $v) {
                    if ($v) $aksesorisItems[] = $aksesorisMap[$k] ?? ucwords(str_replace('_', ' ', $k));
                }
            } else {
                $aksesorisItems = array_values(array_filter(array_map('trim', explode(',', $aksesorisRaw))));
            }
        }

        return view('warehouse/inventory/unit/print', [
            'title'          => 'Unit Print — ' . ($row['no_unit'] ?: $row['no_unit_na'] ?: 'TEMP-' . $id),
            'unit'           => $row,
            'aksesorisItems' => $aksesorisItems,
            'public_view_url' => $publicViewUrl,
        ]);
    }

    private function ensurePublicViewToken(int $unitId): ?string
    {
        if ($unitId <= 0) {
            return null;
        }
        try {
            $db = Database::connect();
            if (! $db->fieldExists('public_view_token', 'inventory_unit')) {
                return null;
            }
            $row = $db->table('inventory_unit')
                ->select('public_view_token')
                ->where('id_inventory_unit', $unitId)
                ->get()
                ->getRowArray();
            $token = trim((string) ($row['public_view_token'] ?? ''));
            if ($token !== '') {
                return $token;
            }

            $token = bin2hex(random_bytes(24));
            $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update(['public_view_token' => $token, 'updated_at' => date('Y-m-d H:i:s')]);

            return $token;
        } catch (\Throwable $e) {
            log_message('warning', 'ensurePublicViewToken: ' . $e->getMessage());
            return null;
        }
    }

    // ──────────────────────────────────────────────────────
    //  DESTROY
    // ──────────────────────────────────────────────────────

    public function destroy($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setBody('Bad Request');
        }

        $unit = $this->inventoryUnitModel->find((int)$id);
        if (!$unit) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.']);
        }

        // Block deactivation while unit is actively in use
        if (in_array((int)$unit['status_unit_id'], [4, 5, 6, 7, 8], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Unit tidak dapat dinonaktifkan, masih dalam status aktif (In Preparation / Ready / In Delivery / Rental).',
            ]);
        }

        // Block deactivation while unit has an open Work Order (MAINTENANCE / BREAKDOWN)
        if (in_array((int)$unit['status_unit_id'], [10, 11], true)) {
            return $this->response->setStatusCode(422)->setJSON([
                'success' => false,
                'message' => 'Unit sedang dalam proses perbaikan/maintenance. Selesaikan Work Order terlebih dahulu sebelum menonaktifkan unit.',
            ]);
        }

        try {
            // Use status 16 (NONAKTIF) — distinct from status 10 (BREAKDOWN) and 11 (MAINTENANCE)
            $this->inventoryUnitModel->update((int)$id, ['status_unit_id' => 16]);
            
            // Log to system_activity_log
            $unitNumber = $unit['no_unit'] ?? $unit['no_unit_na'] ?? "ID #{$id}";
            $this->logDelete('inventory_unit', $id, $unit, [
                'module_name' => 'warehouse',
                'description' => "Unit {$unitNumber} dinonaktifkan (NONAKTIF)",
                'business_impact' => 'HIGH',
                'is_critical' => true,
            ]);
            
            return $this->response->setJSON(['success' => true, 'message' => 'Unit berhasil dinonaktifkan.']);
        } catch (\Throwable $e) {
            log_message('error', 'UnitInventoryController::destroy: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['success' => false, 'message' => 'Terjadi kesalahan pada sistem. Silakan coba lagi.']);
        }
    }

    // ──────────────────────────────────────────────────────
    //  PRIVATE HELPERS
    // ──────────────────────────────────────────────────────

    private function collectFormFields(bool $allowNull = false): array
    {
        $fields = [
            'serial_number', 'no_unit', 'no_unit_na', 'id_po', 'tahun_unit',
            'status_unit_id', 'lokasi_unit', 'departemen_id', 'tanggal_kirim',
            'keterangan', 'tipe_unit_id', 'model_unit_id', 'kapasitas_unit_id',
            'model_mast_id', 'tinggi_mast', 'sn_mast', 'model_mesin_id',
            'sn_mesin', 'roda_id', 'ban_id', 'valve_id',
        ];
        $data = [];
        foreach ($fields as $f) {
            $val = $this->request->getPost($f);
            if ($allowNull) {
                $data[$f] = ($val === '' || $val === null) ? null : $val;
            } elseif ($val !== null && $val !== '') {
                $data[$f] = $val;
            }
        }
        return $data;
    }

    private function getDynamicStats(string $searchValue = '', ?string $departemenFilter = null): array
    {
        $db = Database::connect();

        $builder = $db->table('inventory_unit iu')
            ->select('iu.status_unit_id, COUNT(*) as cnt')
            ->groupBy('iu.status_unit_id');

        if ($searchValue !== '') {
            $builder->groupStart()
                ->like('iu.no_unit', $searchValue)
                ->orLike('iu.no_unit_na', $searchValue)
                ->orLike('iu.serial_number', $searchValue)
                ->orLike('iu.lokasi_unit',  $searchValue)
                ->groupEnd();
        }
        if ($departemenFilter) {
            $builder->where('iu.departemen_id', $departemenFilter);
        }

        $counts = [];
        foreach ($builder->get()->getResultArray() as $r) {
            $counts[(int)$r['status_unit_id']] = (int)$r['cnt'];
        }

        return [
            'total'            => array_sum($counts),
            'available_stock'  => $counts[1]  ?? 0,
            'stock_non_aset'   => $counts[2]  ?? 0,
            'booked'           => $counts[3]  ?? 0,
            'in_preparation'   => $counts[4]  ?? 0,
            'ready_to_deliver' => $counts[5]  ?? 0,
            'in_delivery'      => $counts[6]  ?? 0,
            'rental_active'    => $counts[7]  ?? 0,
            'rental_daily'     => $counts[8]  ?? 0,
            'trial'            => $counts[9]  ?? 0,
            'under_repair'     => $counts[10] ?? 0,   // BREAKDOWN (termasuk dalam progress)
            'maintenance'      => $counts[11] ?? 0,   // MAINTENANCE
            'returned'         => $counts[12] ?? 0,
            'sold'             => $counts[13] ?? 0,
            'rental_inactive'  => $counts[14] ?? 0,
            'spare'            => $counts[15] ?? 0,
            'nonaktif'         => $counts[16] ?? 0,   // NONAKTIF (dinonaktifkan, bukan repair)
        ];
    }

    private function getDepartemenOptions(): array
    {
        $db = Database::connect();
        try {
            if ($db->tableExists('departemen')) {
                return $db->table('departemen')
                    ->select('id_departemen, nama_departemen')
                    ->orderBy('nama_departemen', 'ASC')
                    ->get()->getResultArray();
            }
        } catch (\Throwable $e) {
            log_message('warning', 'getDepartemenOptions: ' . $e->getMessage());
        }
        return [];
    }

    private function getLookupData(): array
    {
        $db  = Database::connect();
        $get = function (string $table, string $select, string $order) use ($db): array {
            try {
                return $db->tableExists($table)
                    ? $db->table($table)->select($select)->orderBy($order, 'ASC')->get()->getResultArray()
                    : [];
            } catch (\Throwable $e) {
                return [];
            }
        };

        return [
            'tipe_unit'      => $get('tipe_unit',      'id_tipe_unit, tipe, jenis',           'tipe'),
            'model_unit'     => $get('model_unit',     'id_model_unit, merk_unit, model_unit', 'merk_unit'),
            'departemen'     => $get('departemen',     'id_departemen, nama_departemen',       'nama_departemen'),
            'status_unit'    => $get('status_unit',    'id_status, status_unit',               'id_status'),
            'kapasitas_unit' => $get('kapasitas',      'id_kapasitas, kapasitas_unit AS kapasitas', 'kapasitas_unit'),
            'tipe_mast'      => $get('tipe_mast',      'id_mast, tipe_mast, tinggi_mast',      'tipe_mast'),
            'mesin'          => $get('mesin',          'id, merk_mesin, model_mesin',          'model_mesin'),
            'roda'           => $get('jenis_roda',     'id_roda, tipe_roda',                   'tipe_roda'),
            'ban'            => $get('tipe_ban',       'id_ban, tipe_ban',                     'tipe_ban'),
            'valve'          => $get('valve',          'id_valve, jumlah_valve',               'jumlah_valve'),
        ];
    }

    // ──────────────────────────────────────────────────────
    //  BOOK UNIT
    // ──────────────────────────────────────────────────────

    public function bookUnit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.'])->setStatusCode(400);
        }

        $permissions = get_global_permission('warehouse');
        if (!$permissions['edit']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.', 'csrf_hash' => csrf_hash()])->setStatusCode(403);
        }

        $unitId = (int)$id;
        $db     = Database::connect();

        // Validate unit exists and is in a bookable status
        $unit = $db->table('inventory_unit iu')
            ->select('iu.id_inventory_unit, iu.status_unit_id, su.status_unit AS status_unit_name')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->where('iu.id_inventory_unit', $unitId)
            ->get()->getRowArray();

        if (!$unit) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.', 'csrf_hash' => csrf_hash()]);
        }

        $bookableStatuses = [1, 2, 9, 12, 15]; // AVAILABLE_STOCK, NON_ASSET_STOCK, TRIAL, RETURNED, SPARE
        if (!in_array((int)$unit['status_unit_id'], $bookableStatuses)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit dengan status "' . $unit['status_unit_name'] . '" tidak dapat di-booking.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $customerId          = $this->request->getPost('customer_id') ?: null;
        $quotationId         = $this->request->getPost('quotation_id') ?: null;
        $customerNameManual  = trim($this->request->getPost('customer_name_manual') ?? '');
        $notes               = trim($this->request->getPost('notes') ?? '');

        if (empty($customerId) && empty($quotationId) && $customerNameManual === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Pilih customer, quotation, atau isi nama customer manual.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $userId = (int)(session()->get('user_id') ?? 1);
        $now    = date('Y-m-d H:i:s');

        $db->transStart();
        try {
            // Insert booking record
            $db->table('unit_bookings')->insert([
                'unit_id'              => $unitId,
                'customer_id'          => $customerId ? (int)$customerId : null,
                'quotation_id'         => $quotationId ? (int)$quotationId : null,
                'customer_name_manual' => $customerNameManual ?: null,
                'notes'                => $notes ?: null,
                'status'               => 'ACTIVE',
                'booked_by_user_id'    => $userId,
                'booked_at'            => $now,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);

            // Update unit status to BOOKED (3)
            $oldStatusName = $unit['status_unit_name'];
            $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update(['status_unit_id' => 3, 'updated_at' => $now]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed.');
            }

            // Log to unit timeline
            try {
                $timelineService = new \App\Services\UnitTimelineService();
                $forLabel = !empty($customerNameManual)
                    ? $customerNameManual
                    : (!empty($customerId) ? 'Customer ID:' . $customerId : 'Quotation');
                $timelineService->recordStatusChange(
                    $unitId,
                    $oldStatusName,
                    'BOOKED',
                    'Booking untuk: ' . $forLabel . ($notes ? '. ' . $notes : ''),
                    $userId
                );
            } catch (\Throwable $e) {
                log_message('warning', 'bookUnit timeline: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Unit berhasil di-booking.',
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'bookUnit #' . $unitId . ': ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
        }
    }

    // ──────────────────────────────────────────────────────
    //  SCRAP UNIT
    // ──────────────────────────────────────────────────────

    public function scrapUnit($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.'])->setStatusCode(400);
        }

        $permissions = get_global_permission('warehouse');
        if (!$permissions['edit']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.', 'csrf_hash' => csrf_hash()])->setStatusCode(403);
        }

        $unitId = (int)$id;
        $db     = Database::connect();

        $unit = $db->table('inventory_unit iu')
            ->select('iu.id_inventory_unit, iu.status_unit_id, iu.no_unit, su.status_unit AS status_unit_name')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->where('iu.id_inventory_unit', $unitId)
            ->get()->getRowArray();

        if (!$unit) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.', 'csrf_hash' => csrf_hash()]);
        }

        if ((int)$unit['status_unit_id'] !== 10) { // Only BREAKDOWN
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unit hanya bisa di-scrab dari status BREAKDOWN. Status saat ini: "' . $unit['status_unit_name'] . '".',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $reason         = trim($this->request->getPost('reason') ?? '');
        $estimatedValue = $this->request->getPost('estimated_value');
        $notes          = trim($this->request->getPost('notes') ?? '');

        if ($reason === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Alasan scrab wajib diisi.', 'csrf_hash' => csrf_hash()]);
        }

        $userId = (int)(session()->get('user_id') ?? 1);
        $now    = date('Y-m-d H:i:s');

        $db->transStart();
        try {
            $db->table('unit_scrap_records')->insert([
                'unit_id'             => $unitId,
                'reason'              => $reason,
                'estimated_value'     => ($estimatedValue !== null && $estimatedValue !== '') ? (float)$estimatedValue : null,
                'notes'               => $notes ?: null,
                'scrapped_by_user_id' => $userId,
                'scrapped_at'         => $now,
                'created_at'          => $now,
            ]);

            // Update unit status to SOLD (13)
            $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update(['status_unit_id' => 13, 'updated_at' => $now]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed.');
            }

            // Log to timeline
            try {
                $timelineService = new \App\Services\UnitTimelineService();
                $timelineService->recordStatusChange($unitId, 'BREAKDOWN', 'SOLD', 'SCRAB: ' . $reason, $userId);
            } catch (\Throwable $e) {
                log_message('warning', 'scrapUnit timeline: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Unit berhasil di-scrab. Status diubah ke SOLD.',
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'scrapUnit #' . $unitId . ': ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
        }
    }

    // ──────────────────────────────────────────────────────
    //  CHANGE STATUS
    // ──────────────────────────────────────────────────────

    public function changeStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request.'])->setStatusCode(400);
        }

        $permissions = get_global_permission('warehouse');
        if (!$permissions['edit']) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.', 'csrf_hash' => csrf_hash()])->setStatusCode(403);
        }

        $unitId = (int)$id;
        $db     = Database::connect();

        $unit = $db->table('inventory_unit iu')
            ->select('iu.id_inventory_unit, iu.status_unit_id, su.status_unit AS status_unit_name')
            ->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left')
            ->where('iu.id_inventory_unit', $unitId)
            ->get()->getRowArray();

        if (!$unit) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan.', 'csrf_hash' => csrf_hash()]);
        }

        $allowedStatuses = [1, 3, 12]; // AVAILABLE_STOCK, BOOKED, RETURNED
        $currentStatusId = (int)$unit['status_unit_id'];

        if (!in_array($currentStatusId, $allowedStatuses)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Status "' . $unit['status_unit_name'] . '" tidak dapat diubah melalui fitur ini.',
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $newStatusId = (int)$this->request->getPost('new_status_id');
        $reason      = trim($this->request->getPost('reason') ?? '');

        if (!in_array($newStatusId, $allowedStatuses)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tujuan tidak valid.', 'csrf_hash' => csrf_hash()]);
        }
        if ($newStatusId === $currentStatusId) {
            return $this->response->setJSON(['success' => false, 'message' => 'Status tujuan sama dengan status saat ini.', 'csrf_hash' => csrf_hash()]);
        }
        if ($reason === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Alasan perubahan status wajib diisi.', 'csrf_hash' => csrf_hash()]);
        }

        $newStatusName = $db->table('status_unit')->select('status_unit')->where('id_status', $newStatusId)->get()->getRowArray()['status_unit'] ?? $newStatusId;
        $userId        = (int)(session()->get('user_id') ?? 1);
        $now           = date('Y-m-d H:i:s');

        $db->transStart();
        try {
            // If moving away from BOOKED (3): cancel the active booking
            if ($currentStatusId === 3 && $db->tableExists('unit_bookings')) {
                $db->table('unit_bookings')
                    ->where('unit_id', $unitId)
                    ->where('status', 'ACTIVE')
                    ->update([
                        'status'               => 'CANCELLED',
                        'cancelled_by_user_id' => $userId,
                        'cancelled_at'         => $now,
                        'cancel_reason'        => $reason,
                        'updated_at'           => $now,
                    ]);
            }

            $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update(['status_unit_id' => $newStatusId, 'updated_at' => $now]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed.');
            }

            // Log to timeline
            try {
                $timelineService = new \App\Services\UnitTimelineService();
                $timelineService->recordStatusChange($unitId, $unit['status_unit_name'], $newStatusName, $reason, $userId);
            } catch (\Throwable $e) {
                log_message('warning', 'changeStatus timeline: ' . $e->getMessage());
            }

            return $this->response->setJSON([
                'success'   => true,
                'message'   => 'Status unit berhasil diubah ke ' . $newStatusName . '.',
                'csrf_hash' => csrf_hash(),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', 'changeStatus #' . $unitId . ': ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.', 'csrf_hash' => csrf_hash()]);
        }
    }

    // ──────────────────────────────────────────────────────
    //  API: SEARCH CUSTOMERS (for booking modal)
    // ──────────────────────────────────────────────────────

    public function apiSearchCustomers()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false])->setStatusCode(400);
        }

        $q  = trim($this->request->getGet('q') ?? '');
        $db = Database::connect();

        try {
            $builder = $db->table('customers')
                ->select('id, customer_name, customer_code')
                ->where('is_active', 1)
                ->orderBy('customer_name', 'ASC')
                ->limit(30);

            if ($q !== '') {
                $builder->groupStart()
                    ->like('customer_name', $q)
                    ->orLike('customer_code', $q)
                    ->groupEnd();
            }

            $rows = $builder->get()->getResultArray();

            return $this->response->setJSON(['success' => true, 'data' => $rows]);
        } catch (\Throwable $e) {
            log_message('error', 'apiSearchCustomers: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'data' => []]);
        }
    }

    // ──────────────────────────────────────────────────────
    //  API: SEARCH QUOTATIONS (for booking modal)
    // ──────────────────────────────────────────────────────

    public function apiSearchQuotations()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false])->setStatusCode(400);
        }

        $q  = trim($this->request->getGet('q') ?? '');
        $db = Database::connect();

        try {
            $builder = $db->table('quotations')
                ->select('id_quotation, quotation_number, prospect_name, stage')
                ->whereIn('stage', ['DRAFT', 'SENT', 'ACCEPTED'])
                ->orderBy('quotation_date', 'DESC')
                ->limit(30);

            if ($q !== '') {
                $builder->groupStart()
                    ->like('prospect_name', $q)
                    ->orLike('quotation_number', $q)
                    ->groupEnd();
            }

            $rows = $builder->get()->getResultArray();

            return $this->response->setJSON(['success' => true, 'data' => $rows]);
        } catch (\Throwable $e) {
            log_message('error', 'apiSearchQuotations: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'data' => []]);
        }
    }
}
