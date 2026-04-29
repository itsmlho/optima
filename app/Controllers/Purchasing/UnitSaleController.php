<?php

namespace App\Controllers\Purchasing;

use App\Controllers\BaseController;
use App\Models\UnitSaleModel;
use App\Models\InventoryUnitModel;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\API\ResponseTrait;

class UnitSaleController extends BaseController
{
    use ResponseTrait, ActivityLoggingTrait;

    protected $saleModel;
    protected $unitModel;
    protected $db;

    public function __construct()
    {
        $this->saleModel = new UnitSaleModel();
        $this->unitModel = new InventoryUnitModel();
        $this->db        = \Config\Database::connect();
        helper(['form', 'url']);
    }

    // ─────────────────────────────────────────────────────────
    // Halaman utama — daftar penjualan
    // ─────────────────────────────────────────────────────────
    public function index()
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return view('errors/html/error_403');
        }

        $data = [
            'title'            => 'Penjualan Unit',
            'stats'            => $this->saleModel->getStats(),
            'unrecorded_count' => 0,
        ];

        return view('purchasing/unit_sale/index', $data);
    }

    // ─────────────────────────────────────────────────────────
    // DataTables AJAX — GET /purchasing/unit-sale/getData
    // ─────────────────────────────────────────────────────────
    public function getSalesData()
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return $this->response->setJSON(['error' => 'Unauthorized'])->setStatusCode(403);
        }

        $request = $this->request;
        $draw    = (int) $request->getGet('draw');
        $start   = (int) ($request->getGet('start') ?? 0);
        $length  = (int) ($request->getGet('length') ?? 25);
        $search  = trim((string) ($request->getGet('search')['value'] ?? ''));

        $filters = [
            'search'    => $search,
            'status'    => $request->getGet('status') ?? '',
            'date_from' => $request->getGet('date_from') ?? '',
            'date_to'   => $request->getGet('date_to') ?? '',
        ];

        $allRows = $this->saleModel->getWithUnitInfo($filters);
        $total   = count($allRows);
        $sliced  = array_slice($allRows, $start, $length);

        $rows = [];
        foreach ($sliced as $r) {
            $unitLabel  = esc($r['no_unit'] ?: $r['no_unit_na'] ?: 'UNIT-' . $r['unit_id']);
            $unitDetail = trim(($r['merk_unit'] ?? '') . ' ' . ($r['model_unit'] ?? ''));

            $statusBadge = $r['status'] === 'CANCELLED'
                ? '<span class="badge badge-soft-red">Dibatalkan</span>'
                : '<span class="badge badge-soft-green">Selesai</span>';

            $rows[] = [
                'no_dokumen'    => '<span class="font-monospace fw-semibold">' . esc($r['no_dokumen']) . '</span>',
                'unit'          => '<div class="fw-semibold">' . $unitLabel . '</div>'
                                 . '<small class="text-muted">' . esc($unitDetail) . '</small>'
                                 . ($r['serial_number'] ? '<br><small class="text-muted font-monospace">SN: ' . esc($r['serial_number']) . '</small>' : ''),
                'tanggal_jual'  => date('d/m/Y', strtotime($r['tanggal_jual'])),
                'pembeli'       => '<div>' . esc($r['nama_pembeli']) . '</div>'
                                 . ($r['telepon_pembeli'] ? '<small class="text-muted">' . esc($r['telepon_pembeli']) . '</small>' : ''),
                'harga_jual'    => 'Rp ' . number_format((float) $r['harga_jual'], 0, ',', '.'),
                'metode'        => $this->getMetodeBadge($r['metode_pembayaran']),
                'status'        => $statusBadge,
                'actions'       => '<a href="' . base_url('purchasing/unit-sale/detail/' . $r['id']) . '" class="btn btn-sm btn-outline-primary"><i class="fas fa-eye"></i></a>',
            ];
        }

        return $this->response->setJSON([
            'draw'            => $draw,
            'recordsTotal'    => $total,
            'recordsFiltered' => $total,
            'data'            => $rows,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Select2 — unit eligible untuk dijual (status != SOLD)
    // GET /purchasing/unit-sale/getEligibleUnits?q=
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
    // Generate sale number — GET /purchasing/unit-sale/generateNumber
    // ─────────────────────────────────────────────────────────
    public function generateNumber()
    {
        return $this->response->setJSON([
            'success'     => true,
            'no_dokumen'  => $this->saleModel->generateSaleNumber(),
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Store — POST /purchasing/unit-sale/store
    // ─────────────────────────────────────────────────────────
    public function store()
    {
        if (!$this->hasPermission('purchasing.unit_sale.create')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

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

        // Cek nomor dokumen unik
        $existing = $this->saleModel->where('no_dokumen', $this->request->getPost('no_dokumen'))->first();
        if ($existing) {
            return $this->response->setJSON(['success' => false, 'message' => 'Nomor dokumen sudah digunakan.']);
        }

        // Simpan harga jual — bersihkan format Rp dan titik/koma
        $hargaRaw = preg_replace('/[^0-9.]/', '', (string) $this->request->getPost('harga_jual'));

        $db = $this->db;
        $db->transStart();

        try {
            $insertData = [
                'no_dokumen'               => $this->request->getPost('no_dokumen'),
                'unit_id'                  => $unitId,
                'tanggal_jual'             => $this->request->getPost('tanggal_jual'),
                'nama_pembeli'             => $this->request->getPost('nama_pembeli'),
                'alamat_pembeli'           => $this->request->getPost('alamat_pembeli') ?: null,
                'telepon_pembeli'          => $this->request->getPost('telepon_pembeli') ?: null,
                'harga_jual'               => (float) $hargaRaw,
                'metode_pembayaran'        => $this->request->getPost('metode_pembayaran'),
                'no_kwitansi'              => $this->request->getPost('no_kwitansi') ?: null,
                'status'                   => 'COMPLETED',
                'previous_status_unit_id'  => $unit['status_unit_id'],
                'keterangan'               => $this->request->getPost('keterangan') ?: null,
                'sold_by_user_id'          => (int) session('user_id'),
            ];

            $saleId = $this->saleModel->insert($insertData, true);

            // Update status unit jadi SOLD
            $db->table('inventory_unit')
                ->where('id_inventory_unit', $unitId)
                ->update([
                    'status_unit_id' => InventoryUnitModel::STATUS_UNIT_SOLD_ID,
                    'updated_at'     => date('Y-m-d H:i:s'),
                ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }

            // Activity log
            $this->logActivity('CREATE', 'unit_sale_records', (int) $saleId,
                sprintf('Penjualan unit %s kepada %s (Rp %s) — Dok: %s',
                    $unit['no_unit'] ?: $unit['no_unit_na'] ?: 'UNIT-' . $unitId,
                    $insertData['nama_pembeli'],
                    number_format($insertData['harga_jual'], 0, ',', '.'),
                    $insertData['no_dokumen']
                ),
                ['business_impact' => 'HIGH', 'is_critical' => 1]
            );

            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Penjualan unit berhasil dicatat.',
                'detail_url' => base_url('purchasing/unit-sale/detail/' . $saleId),
            ]);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[UnitSale::store] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan data: ' . $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────
    // Detail — GET /purchasing/unit-sale/detail/:id
    // ─────────────────────────────────────────────────────────
    public function detail(int $id)
    {
        if (!$this->hasPermission('purchasing.unit_sale.view')) {
            return view('errors/html/error_403');
        }

        $sale = $this->saleModel->getDetailWithUnit($id);
        if (!$sale) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Data penjualan tidak ditemukan.');
        }

        return view('purchasing/unit_sale/detail', [
            'title' => 'Detail Penjualan — ' . $sale['no_dokumen'],
            'sale'  => $sale,
        ]);
    }

    // ─────────────────────────────────────────────────────────
    // Cancel — POST /purchasing/unit-sale/cancel/:id
    // ─────────────────────────────────────────────────────────
    public function cancel(int $id)
    {
        if (!$this->hasPermission('purchasing.unit_sale.delete')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $sale = $this->saleModel->find($id);
        if (!$sale) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
        }
        if ($sale['status'] !== 'COMPLETED') {
            return $this->response->setJSON(['success' => false, 'message' => 'Hanya transaksi COMPLETED yang dapat dibatalkan.']);
        }

        $reason = trim((string) $this->request->getPost('cancelled_reason'));
        if ($reason === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Alasan pembatalan harus diisi.']);
        }

        $db = $this->db;
        $db->transStart();

        try {
            // Update sale record
            $this->saleModel->update($id, [
                'status'               => 'CANCELLED',
                'cancelled_at'         => date('Y-m-d H:i:s'),
                'cancelled_by_user_id' => (int) session('user_id'),
                'cancelled_reason'     => $reason,
            ]);

            // Rollback status unit ke sebelumnya
            if ($sale['previous_status_unit_id']) {
                $db->table('inventory_unit')
                    ->where('id_inventory_unit', $sale['unit_id'])
                    ->update([
                        'status_unit_id' => $sale['previous_status_unit_id'],
                        'updated_at'     => date('Y-m-d H:i:s'),
                    ]);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi database gagal.');
            }

            $this->logActivity('DELETE', 'unit_sale_records', $id,
                sprintf('Pembatalan penjualan unit — Dok: %s | Alasan: %s', $sale['no_dokumen'], $reason),
                ['business_impact' => 'HIGH', 'is_critical' => 1]
            );

            return $this->response->setJSON(['success' => true, 'message' => 'Transaksi penjualan berhasil dibatalkan.']);
        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[UnitSale::cancel] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal membatalkan: ' . $e->getMessage()]);
        }
    }

    // ─────────────────────────────────────────────────────────
    // Helper — badge metode pembayaran
    // ─────────────────────────────────────────────────────────
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
