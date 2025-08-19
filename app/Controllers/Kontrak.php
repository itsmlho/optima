<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\KontrakModel;

class Kontrak extends BaseController
{
    protected $kontrakModel;

    public function __construct()
    {
        $this->kontrakModel = new KontrakModel();
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
        // Add CORS headers for debugging
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
        $this->response->setHeader('Access-Control-Allow-Headers', 'Content-Type');
        
        // Log the incoming request
        log_message('debug', 'Kontrak::getDataTable called');
        log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
        
        try {
            $list = $this->kontrakModel->getDataTable($this->request);
            log_message('debug', 'Raw data from model: ' . json_encode($list));
            
            $data = [];
            
            foreach ($list as $contract) {
                $row = [];
                $row['contract_number'] = '<strong>' . esc($contract['no_kontrak']) . '</strong>';
                $row['client_name']     = esc($contract['pelanggan']);
                $row['period']          = date('d M Y', strtotime($contract['tanggal_mulai'])) . ' - ' . date('d M Y', strtotime($contract['tanggal_berakhir']));
                $row['value']           = 'Rp ' . number_format($contract['nilai_total'] ?? 0, 0, ',', '.');
                $row['total_units']     = '<span class="badge bg-dark">' . esc($contract['total_units']) . ' Unit</span>';
                
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
                "recordsTotal"    => $this->kontrakModel->countAllData(),
                "recordsFiltered" => $this->kontrakModel->countFilteredData($this->request),
                "data"            => $data,
                "stats"           => $this->kontrakModel->getStats()
            ];
            
            log_message('debug', 'Final response: ' . json_encode($response));
            
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
            'lokasi'            => $this->request->getPost('project_name'),
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
            'lokasi'            => $this->request->getPost('project_name'),
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
     * Debug endpoint untuk test data
     */
    public function debug()
    {
        $data = $this->kontrakModel->findAll();
        return $this->response->setJSON([
            'success' => true,
            'total_records' => count($data),
            'data' => $data,
            'table_name' => $this->kontrakModel->getTable()
        ]);
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
}
