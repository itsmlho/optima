<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KontrakModel;

class Kontrak extends BaseController
{
    protected $kontrakModel;
    protected $db;

    public function __construct()
    {
        $this->kontrakModel = new KontrakModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Menampilkan halaman utama Manajemen Kontrak.
     */
    public function index()
    {
        $data = [
            'title' => 'Manajemen Kontrak Rental',
        ];
        return view('marketing/kontrak', $data);
    }

    /**
     * Menyediakan data untuk DataTables Server-Side.
     */
    public function getDataTable()
    {
        try {
            $result = $this->kontrakModel->getDataTable($this->request);
            
            $data = [];
            
            foreach ($result['data'] as $contract) {
                $row = [];
                $row['id']              = $contract['id'];
                $row['contract_number'] = '<strong>' . esc($contract['no_kontrak']) . '</strong>';
                $row['po']              = esc(isset($contract['no_po_marketing']) ? $contract['no_po_marketing'] : '-');
                $row['client_name']     = esc($contract['pelanggan']);
                $row['period']          = date('d M Y', strtotime($contract['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($contract['tanggal_berakhir']));
                
                // Status badge with proper color
                $statusClass = 'bg-secondary';
                switch($contract['status']) {
                    case 'Aktif':
                        $statusClass = 'bg-success';
                        break;
                    case 'Pending':
                        $statusClass = 'bg-warning';
                        break;
                    case 'Berakhir':
                        $statusClass = 'bg-danger';
                        break;
                    case 'Dibatalkan':
                        $statusClass = 'bg-secondary';
                        break;
                }
                $row['status'] = '<span class="badge ' . $statusClass . '">' . esc($contract['status']) . '</span>';
                
                $row['actions'] = '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-info" onclick="viewContractUnits(' . $contract['id'] . ')" title="Lihat Unit Terkait">
                            <i class="fas fa-truck"></i>
                        </button>
                        <button class="btn btn-warning" onclick="editContract(' . $contract['id'] . ')" title="Edit Kontrak">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-danger" onclick="deleteContract(' . $contract['id'] . ')" title="Hapus Kontrak">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                ';
                $data[] = $row;
            }

            $response = [
                "draw"            => intval($this->request->getPost('draw') ?? 1),
                "recordsTotal"    => $result['recordsTotal'],
                "recordsFiltered" => $result['recordsFiltered'],
                "data"            => $data,
                "stats"           => $result['stats']
            ];
            
            return $this->response->setJSON($response);
            
        } catch (\Exception $e) {
            log_message('error', 'Error in Kontrak::getDataTable: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => 'Server error: ' . $e->getMessage(),
                'draw' => intval($this->request->getPost('draw') ?? 1),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }

    /**
     * Menyimpan kontrak baru dari modal.
     */
    public function store()
    {
        // Validasi input
        $rules = [
            'contract_number' => 'required|is_unique[kontrak.no_kontrak]',
            'client_name'     => 'required',
            'start_date'      => 'required|valid_date',
            'end_date'        => 'required|valid_date',
            'status'          => 'required|in_list[Aktif,Pending,Berakhir,Dibatalkan]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Validasi gagal: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        // Mapping data dari form ke database
        $data = [
            'no_kontrak'        => $this->request->getPost('contract_number'),
            'no_po_marketing'   => $this->request->getPost('po_number'),
            'pelanggan'         => $this->request->getPost('client_name'),
            'pic'               => $this->request->getPost('pic') ?: null,
            'kontak'            => $this->request->getPost('kontak') ?: null,
            'lokasi'            => $this->request->getPost('project_name'),
            'nilai_total'       => $this->request->getPost('contract_value') ?: 0,
            'total_units'       => $this->request->getPost('total_units') ?: 0,
            'tanggal_mulai'     => $this->request->getPost('start_date'),
            'tanggal_berakhir'  => $this->request->getPost('end_date'),
            'status'            => $this->request->getPost('status'),
            'dibuat_oleh'       => session()->get('user_id') ?? 1, // Default user ID jika session kosong
        ];

        if ($this->kontrakModel->insert($data)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kontrak baru berhasil disimpan.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal menyimpan data ke database: ' . implode(', ', $this->kontrakModel->errors())
            ]);
        }
    }

    /**
     * Update kontrak
     */
    public function update($id)
    {
        $rules = [
            'contract_number' => "required|is_unique[kontrak.no_kontrak,id,{$id}]",
            'client_name'     => 'required',
            'start_date'      => 'required|valid_date',
            'end_date'        => 'required|valid_date',
            'status'          => 'required|in_list[Aktif,Pending,Berakhir,Dibatalkan]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Validasi gagal: ' . implode(', ', $this->validator->getErrors())
            ]);
        }

        $data = [
            'no_kontrak'        => $this->request->getPost('contract_number'),
            'no_po_marketing'   => $this->request->getPost('po_number'),
            'pelanggan'         => $this->request->getPost('client_name'),
            'pic'               => $this->request->getPost('pic') ?: null,
            'kontak'            => $this->request->getPost('kontak') ?: null,
            'lokasi'            => $this->request->getPost('project_name'),
            'nilai_total'       => $this->request->getPost('contract_value') ?: 0,
            'total_units'       => $this->request->getPost('total_units') ?: 0,
            'tanggal_mulai'     => $this->request->getPost('start_date'),
            'tanggal_berakhir'  => $this->request->getPost('end_date'),
            'status'            => $this->request->getPost('status'),
        ];

        if ($this->kontrakModel->update($id, $data)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kontrak berhasil diperbarui.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal memperbarui data: ' . implode(', ', $this->kontrakModel->errors())
            ]);
        }
    }

    /**
     * Hapus kontrak
     */
    public function delete($id)
    {
        if ($this->kontrakModel->delete($id)) {
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Kontrak berhasil dihapus.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Gagal menghapus kontrak.'
            ]);
        }
    }

    /**
     * Get contract detail
     */
    public function detail($id)
    {
        $contract = $this->kontrakModel->find($id);
        if ($contract) {
            return $this->response->setJSON([
                'success' => true, 
                'data' => $contract
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Kontrak tidak ditemukan.'
            ]);
        }
    }

    /**
     * Edit contract - return view with data
     */
    public function edit($id)
    {
        $contract = $this->kontrakModel->find($id);
        if (!$contract) {
            return redirect()->to('marketing/contracts')->with('error', 'Kontrak tidak ditemukan.');
        }

        $data = [
            'title' => 'Edit Kontrak',
            'contract' => $contract
        ];
        
        return view('marketing/kontrak_edit', $data);
    }

    /**
     * Get units related to a contract for display in detail modal.
     */
    public function getContractUnits($kontrakId)
    {
        try {
            // Validasi kontrak ID
            $kontrak = $this->kontrakModel->find($kontrakId);
            if (!$kontrak) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Kontrak tidak ditemukan'
                ]);
            }
            
            // Query dengan foreign key yang benar
            $query = "
                SELECT DISTINCT
                    iu.id_inventory_unit,
                    iu.no_unit,
                    COALESCE(mu.merk_unit, 'N/A') as merk,
                    COALESCE(mu.model_unit, 'N/A') as model,
                    COALESCE(kap.kapasitas_unit, 'N/A') as kapasitas,
                    COALESCE(tu.jenis, 'N/A') as jenis_unit,
                    COALESCE(dept.nama_departemen, 'N/A') as departemen,
                    COALESCE(su.status_unit, 'UNKNOWN') as status,
                    iu.status_unit_id,
                    iu.kontrak_id
                FROM inventory_unit iu
                LEFT JOIN model_unit mu ON mu.id_model_unit = iu.model_unit_id
                LEFT JOIN kapasitas kap ON kap.id_kapasitas = iu.kapasitas_unit_id
                LEFT JOIN tipe_unit tu ON tu.id_tipe_unit = iu.tipe_unit_id
                LEFT JOIN departemen dept ON dept.id_departemen = iu.departemen_id
                LEFT JOIN status_unit su ON su.id_status = iu.status_unit_id
                WHERE iu.kontrak_id = ?
                ORDER BY iu.no_unit ASC
            ";
            
            $result = $this->db->query($query, [$kontrakId]);
            $units = $result->getResultArray();
            
            // Format response
            $formattedUnits = [];
            foreach ($units as $unit) {
                $formattedUnits[] = [
                    'id' => $unit['id_inventory_unit'],
                    'no_unit' => $unit['no_unit'] ?: '-',
                    'merk' => $unit['merk'],
                    'model' => $unit['model'],
                    'kapasitas' => $unit['kapasitas'],
                    'jenis_unit' => $unit['jenis_unit'],
                    'departemen' => $unit['departemen'],
                    'status' => $unit['status']
                ];
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $formattedUnits,
                'total' => count($formattedUnits),
                'kontrak_id' => $kontrakId,
                'kontrak_number' => $kontrak['no_kontrak']
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Kontrak::getContractUnits Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error retrieving contract units: ' . $e->getMessage()
            ]);
        }
    }
}
