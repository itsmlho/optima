<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use App\Models\UnitAssetModel;

class UnitAssetController extends BaseController
{
    use ActivityLoggingTrait;
    protected $unitAssetModel;

    public function __construct()
    {
        ini_set('memory_limit', '512M');
        $this->unitAssetModel = new UnitAssetModel();
        
        // Load auth helper for division filtering
        helper('auth');
    }

    public function index()
    {
        $data = [
            'title' => 'Unit Assets | OPTIMA',
            'page_title' => 'Unit Assets',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/warehouse/unit-assets' => 'Unit Assets'
            ],
            'stats' => $this->unitAssetModel->getUnitAssetStats(),
            'departments' => $this->unitAssetModel->getDepartments(),
            'locations' => $this->unitAssetModel->getLocations(),
            'form_options' => $this->getFormOptions()
        ];
        return view('warehouse/unit_assets/index', $data);
    }

    /**
     * (Optional) Create new unit directly in inventory_unit (not fully implemented post-migration)
     */
    public function store()
    {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Store not implemented after migration to inventory_unit'
        ]);
    }

    /** Convert a Stock unit (status 7) to Rental/Asset (status 3). */
    public function confirmToAsset($id)
    {
        try {
            $inventoryUnitModel = new \App\Models\InventoryUnitModel();
            $unit = $inventoryUnitModel->find($id);
            if (!$unit) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit tidak ditemukan']);
            }
            if ((int)($unit['status_unit_id'] ?? 0) === 3) {
                return $this->response->setJSON(['success' => false, 'message' => 'Unit sudah berstatus RENTAL/ASET']);
            }
            if ($inventoryUnitModel->update($id, ['status_unit_id' => 3])) {
                return $this->response->setJSON(['success' => true, 'message' => 'Status unit berhasil diubah menjadi RENTAL/ASET']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal mengubah status unit']);
        } catch (\Exception $e) {
            log_message('error', 'Error confirmToAsset: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Kesalahan server: ' . $e->getMessage()]);
        }
    }

    public function getDataTable()
    {
        $this->response->setContentType('application/json');
        try {
            $db = \Config\Database::connect();
            foreach (['inventory_unit', 'model_unit', 'departemen', 'status_unit'] as $tbl) {
                if (!$db->tableExists($tbl)) {
                    return $this->response->setStatusCode(500)->setJSON(['error' => "Missing required table: $tbl"]);
                }
            }
            $start = (int)($this->request->getPost('start') ?? 0);
            $length = (int)($this->request->getPost('length') ?? 10);
            $searchPost = $this->request->getPost('search');
            $searchValue = is_array($searchPost) ? ($searchPost['value'] ?? '') : '';
            $order = $this->request->getPost('order');
            $colIdx = $order[0]['column'] ?? 0; $dir = $order[0]['dir'] ?? 'asc';
            $colMap = [
                0 => 'iu.no_unit',
                1 => 'iu.serial_number',
                2 => 'model_unit_display',
                3 => 'departemen_name',
                4 => 'iu.lokasi_unit',
                5 => 'status_unit_name',
                6 => 'iu.status_aset'
            ];
            $orderCol = $colMap[$colIdx] ?? 'iu.no_unit';
            $statusFilter = $this->request->getPost('status_unit_filter');
            $departemenFilter = $this->request->getPost('departemen_filter');
            $lokasiFilter = $this->request->getPost('lokasi_filter');

            $builder = $db->table('inventory_unit iu');
            $builder->select('iu.no_unit, iu.serial_number as serial_number_po, iu.lokasi_unit, iu.status_aset, iu.status_unit_id, ku.kontrak_id, iu.tanggal_masuk, ' .
                'mu.merk_unit, mu.model_unit, CONCAT("Forklift ", tu.jenis, " ", tu.tipe) as nama_tipe_unit, ' .
                'd.nama_departemen, su.status_unit AS status_unit_name, ' .
                'cl.location_name as customer_location_name, cl.city as customer_city, cl.address as customer_address, ' .
                'c.customer_name as customer_name');
            $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
            $builder->join('tipe_unit tu', 'tu.id_tipe_unit = iu.tipe_unit_id', 'left');
            $builder->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left');
            $builder->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left');
            $builder->join('kontrak_unit ku', 'ku.unit_id = iu.id_inventory_unit AND ku.status IN ("ACTIVE","TEMP_ACTIVE") AND ku.is_temporary = 0', 'left');
            $builder->join('kontrak k', 'k.id = ku.kontrak_id', 'left');
            $builder->join('customer_locations cl', 'cl.id = k.customer_location_id', 'left');
            $builder->join('customers c', 'c.id = cl.customer_id', 'left');

            if ($statusFilter !== null && $statusFilter !== '') {
                $builder->where('iu.status_unit_id', $statusFilter);
            }
            
            // Apply division-based department filter for warehouse assets using global helper
            $allowedDepartments = get_user_division_departments();
            
            if ($allowedDepartments !== null && is_array($allowedDepartments)) {
                // Override user's manual filter with division-based filter
                $builder->whereIn('iu.departemen_id', $allowedDepartments);
            } elseif ($departemenFilter) {
                // Only apply manual filter if division filter is not active
                $builder->where('iu.departemen_id', $departemenFilter);
            }
            
            if ($lokasiFilter) $builder->like('iu.lokasi_unit', $lokasiFilter);

            if ($searchValue) {
                $builder->groupStart()
                    ->like('iu.no_unit', $searchValue)
                    ->orLike('iu.serial_number', $searchValue)
                    ->orLike('iu.lokasi_unit', $searchValue)
                    ->orLike('mu.merk_unit', $searchValue)
                    ->orLike('mu.model_unit', $searchValue)
                    ->orLike('d.nama_departemen', $searchValue)
                    ->orLike('su.status_unit', $searchValue)
                    ->groupEnd();
            }

            $countBuilder = clone $builder; $recordsFiltered = $countBuilder->countAllResults();
            $recordsTotal = $db->table('inventory_unit')->countAllResults();
            $builder->orderBy($orderCol, $dir); if ($length > 0) $builder->limit($length, $start);
            $rows = $builder->get()->getResultArray();
            $data = [];
            foreach ($rows as $r) {
                $id = esc($r['no_unit'], 'js');
                $actions = '<div class="dropdown">'
                    .'<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">'
                    .'<i class="fas fa-ellipsis-h"></i>'
                    .'</button>'
                    .'<ul class="dropdown-menu dropdown-menu-end">'
                    .'<li><a class="dropdown-item" href="#" onclick="viewUnitAsset(\''.$id.'\')"><i class="fas fa-eye me-2"></i>Lihat Detail</a></li>'
                    .'<li><a class="dropdown-item" href="#" onclick="editUnitAsset(\''.$id.'\')"><i class="fas fa-edit me-2"></i>Edit</a></li>'
                    .'<li><a class="dropdown-item text-danger" href="#" onclick="deleteUnitAsset(\''.$id.'\')"><i class="fas fa-trash me-2"></i>Hapus</a></li>'
                    .'</ul>'
                    .'</div>';
                $data[] = [
                    'no_unit' => $r['no_unit'],
                    'serial_number_po' => $r['serial_number_po'] ?? '-',
                    'merk_unit' => $r['merk_unit'] ?? '-',
                    'model_unit' => $r['model_unit'] ?? '-',
                    'nama_tipe_unit' => $r['nama_tipe_unit'] ?? '-',
                    'nama_departemen' => $r['nama_departemen'] ?? '-',
                    'status_unit_name' => $r['status_unit_name'] ?? 'Unknown',
                    'lokasi_unit' => $r['lokasi_unit'] ?? '-',
                    'tanggal_masuk' => $r['tanggal_masuk'] ?? '-',
                    // Metadata for rendering
                    'status_unit_id' => $r['status_unit_id'] ?? null,
                    'customer_location_name' => $r['customer_location_name'] ?? null,
                    'customer_city' => $r['customer_city'] ?? null,
                    'customer_address' => $r['customer_address'] ?? null,
                    'customer_name' => $r['customer_name'] ?? null,
                    'actions' => $actions
                ];
            }
            return $this->response->setJSON([
                'draw' => (int)($this->request->getPost('draw') ?? 0),
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getDataTable error: '.$e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Server error']);
        }
    }

    public function updateStatus()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
        }
        $id = $this->request->getPost('id');
        $status = $this->request->getPost('status');
        if ($this->unitAssetModel->updateUnitStatus($id, $status)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Status unit berhasil diperbarui']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui status unit']);
    }

    public function export()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('inventory_unit iu');
        $builder->select('iu.no_unit, iu.serial_number, iu.tahun_unit, iu.lokasi_unit, iu.status_aset, iu.tanggal_kirim, iu.keterangan, '
            .'su.status_unit, d.nama_departemen, CONCAT(mu.merk_unit, " - ", mu.model_unit) AS model_unit_display');
        $builder->join('status_unit su', 'su.id_status = iu.status_unit_id', 'left');
        $builder->join('departemen d', 'd.id_departemen = iu.departemen_id', 'left');
        $builder->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left');
        $rows = $builder->get()->getResultArray();
        $filename = 'unit_assets_'.date('Y-m-d_H-i-s').'.csv';
        $this->response->setHeader('Content-Type', 'text/csv');
        $this->response->setHeader('Content-Disposition', 'attachment; filename="'.$filename.'"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['No Unit','Serial Number','Model Unit','Departemen','Lokasi','Status Unit','Status Aset','Tahun','Tanggal Kirim','Keterangan']);
        foreach ($rows as $r) {
            fputcsv($out, [
                $r['no_unit'],
                $r['serial_number'],
                $r['model_unit_display'],
                $r['nama_departemen'],
                $r['lokasi_unit'],
                $r['status_unit'],
                $r['status_aset'],
                $r['tahun_unit'],
                $r['tanggal_kirim'],
                $r['keterangan']
            ]);
        }
        fclose($out);
        return $this->response;    
    }

    private function formatStatusBadge($status, $type = 'unit')
    {
        $s = strtolower($status);
        if ($type === 'unit') {
            $map = [
                'available' => 'bg-success',
                'stock aset' => 'bg-success',
                'rental' => 'bg-warning',
                'rented' => 'bg-warning',
                'jual' => 'bg-info',
                'maintenance' => 'bg-danger',
                'workshop-rusak' => 'bg-danger',
                'retired' => 'bg-secondary'
            ];
        } else {
            $map = [
                'active' => 'bg-success',
                'inactive' => 'bg-warning',
                'damaged' => 'bg-danger',
                'disposed' => 'bg-dark'
            ];
        }
        $cls = $map[$s] ?? 'bg-secondary';
        return '<span class="badge '.$cls.'">'.esc(ucwords($status)).'</span>';
    }

    private function getFormOptions()
    {
        return [
            'status_unit' => $this->getTableData('status_unit', 'id_status', 'status_unit'),
            'departemen' => $this->getTableData('departemen', 'id_departemen', 'nama_departemen'),
            'model_unit' => $this->getTableData('model_unit', 'id_model_unit', 'model_unit', false, 'merk_unit'),
            'kapasitas' => $this->getTableData('kapasitas', 'id_kapasitas', 'kapasitas_unit'),
        ];
    }

    private function getTableData($table, $idField, $nameField, $distinct = false, $additionalField = null)
    {
        $db = \Config\Database::connect();
        if (!$db->tableExists($table)) return [];
        $b = $db->table($table);
        if ($distinct) $b->distinct();
        $select = "$idField, $nameField"; if ($additionalField) $select .= ", $additionalField"; $b->select($select);
        return $b->get()->getResultArray();
    }

    public function debugSpecifications($no_unit)
    {
        $this->response->setContentType('text/plain');
        $data = $this->unitAssetModel->getUnitAssetWithDetails($no_unit);
        return print_r($data, true);
    }

    public function getSimpleData()
    {
        $this->response->setContentType('application/json');
        $db = \Config\Database::connect();
        if (!$db->tableExists('inventory_unit')) {
            return $this->response->setJSON(['success'=>false,'message'=>'inventory_unit missing','data'=>[]]);
        }
        $b = $db->table('inventory_unit iu');
        $b->select('iu.no_unit, iu.serial_number, iu.status_unit_id, iu.status_aset, iu.lokasi_unit, su.status_unit AS status_unit_name');
        if ($db->tableExists('status_unit')) $b->join('status_unit su','su.id_status = iu.status_unit_id','left');
        $rows = $b->orderBy('iu.no_unit','ASC')->get()->getResultArray();
        return $this->response->setJSON(['success'=>true,'data'=>$rows,'total'=>count($rows)]);
    }
}