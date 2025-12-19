<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;

class UnitRolling extends BaseController
{
    use ActivityLoggingTrait;
    public function index()
    {
        $data = [
            'title' => 'Data Rolling | OPTIMA',
            'page_title' => 'Data Rolling Unit',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/unitRolling' => 'Data Rolling'
            ],
            'rolling_units' => $this->getRollingUnits(),
        ];

        return view('unitRolling/rolling', $data);
    }

    public function history()
    {
        $data = [
            'title' => 'History Rolling | OPTIMA',
            'page_title' => 'History Rolling Unit',
            'breadcrumbs' => [
                '/' => 'Dashboard',
                '/unitRolling' => 'Data Rolling',
                '/unitRolling/history' => 'History'
            ],
            'rolling_history' => $this->getRollingHistory(),
        ];

        return view('unitRolling/history', $data);
    }

    private function getRollingUnits()
    {
        // Mock data for rolling units
        return [
            [
                'unit_code' => 'FL-001',
                'brand' => 'Toyota',
                'model' => '8FG25',
                'type' => 'Forklift',
                'capacity' => '2.5 Ton',
                'engine_hours' => 1250,
                'fuel_type' => 'Diesel',
                'status' => 'Active',
                'location' => 'Jakarta',
                'operator' => 'John Doe',
                'last_maintenance' => '2023-12-16'
            ],
            [
                'unit_code' => 'FL-002',
                'brand' => 'Mitsubishi',
                'model' => 'FG25N',
                'type' => 'Forklift',
                'capacity' => '2.5 Ton',
                'engine_hours' => 980,
                'fuel_type' => 'LPG',
                'status' => 'Active',
                'location' => 'Surabaya',
                'operator' => 'Jane Smith',
                'last_maintenance' => '2023-12-10'
            ],
            [
                'unit_code' => 'FL-003',
                'brand' => 'Komatsu',
                'model' => 'FG25T-16',
                'type' => 'Forklift',
                'capacity' => '2.5 Ton',
                'engine_hours' => 1500,
                'fuel_type' => 'Electric',
                'status' => 'Maintenance',
                'location' => 'Bandung',
                'operator' => 'Mike Johnson',
                'last_maintenance' => '2023-12-05'
            ],
        ];
    }

    private function getRollingHistory()
    {
        // Mock data for rolling history
        return [
            [
                'date' => '2024-01-15',
                'unit_code' => 'FL-001',
                'activity' => 'Deployment',
                'location' => 'Jakarta Site A',
                'operator' => 'John Doe',
                'hours_worked' => 8,
                'fuel_consumption' => 45,
                'notes' => 'Normal operation'
            ],
            [
                'date' => '2024-01-14',
                'unit_code' => 'FL-002',
                'activity' => 'Maintenance',
                'location' => 'Workshop Surabaya',
                'operator' => 'Technician',
                'hours_worked' => 4,
                'fuel_consumption' => 0,
                'notes' => 'Preventive maintenance completed'
            ],
            [
                'date' => '2024-01-13',
                'unit_code' => 'FL-003',
                'activity' => 'Transport',
                'location' => 'Bandung to Jakarta',
                'operator' => 'Driver',
                'hours_worked' => 6,
                'fuel_consumption' => 60,
                'notes' => 'Unit relocation'
            ],
        ];
    }

    public function unitList()
    {
        $request = service('request');
        
        $db = \Config\Database::connect();
        $tableName = 'unit_rolling'; 
        $builder = $db->table($tableName);

        // Ambil parameter dari DataTables
        $start = $request->getPost('start');
        $length = $request->getPost('length');
        $searchValue = $request->getPost('search')['value'];
        
        $totalBuilder = clone $builder;
        $recordsTotal = $totalBuilder->countAllResults();

        // Terapkan filter kustom
        $status = $request->getPost('status');
        $location = $request->getPost('location');

        if (!empty($status)) $builder->where('status', $status);
        if (!empty($location)) $builder->like('current_location', $location);
        
        // Terapkan pencarian global
        if (!empty($searchValue)) {
            $builder->groupStart()
                    ->like('unit_code', $searchValue)
                    ->orLike('unit_name', $searchValue)
                    ->orLike('current_location', $searchValue)
                    ->orLike('operator_name', $searchValue)
                    ->groupEnd();
        }
        
        $filteredBuilder = clone $builder;
        $recordsFiltered = $filteredBuilder->countAllResults();

        if ($length != -1) {
            $builder->limit($length, $start);
        }
        $data = $builder->get()->getResultArray();

        $preparedData = [];
        foreach ($data as $item) {
            $id = $item['id']; 

            $item['status'] = '<span class="badge bg-'. ($item['status'] == 'ACTIVE' ? 'success' : ($item['status'] == 'MAINTENANCE' ? 'warning' : 'danger')) .'">'.esc($item['status']).'</span>';
            $item['actions'] = '<div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-info" onclick="viewUnit('.$id.')"><i class="fas fa-eye"></i></button>
                                    <button class="btn btn-outline-primary" onclick="editUnit('.$id.')"><i class="fas fa-edit"></i></button>
                                    <button class="btn btn-outline-secondary" onclick="trackUnit('.$id.')"><i class="fas fa-map-marker-alt"></i></button>
                                </div>';
            $preparedData[] = $item;
        }

        return $this->response->setJSON([
            'draw' => intval($request->getPost('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $preparedData,
            'token' => csrf_hash()
        ]);
    }

    public function updateLocation()
    {
        $request = service('request');
        if (!$request->isAJAX()) return $this->response->setStatusCode(403);

        $db = \Config\Database::connect();
        $id = $request->getPost('id');
        $data = [
            'current_location' => $request->getPost('location'),
            'latitude' => $request->getPost('latitude'),
            'longitude' => $request->getPost('longitude'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        
        if ($db->table('unit_rolling')->where('id', $id)->update($data)) {
            // Get unit details for notification
            $unitRolling = $db->table('unit_rolling ur')
                ->select('ur.*, iu.no_unit')
                ->join('inventory_unit iu', 'iu.id_inventory_unit = ur.inventory_unit_id', 'left')
                ->where('ur.id', $id)
                ->get()
                ->getRowArray();
            
            // Send notification - unit location updated
            if (function_exists('notify_unit_location_updated') && $unitRolling) {
                notify_unit_location_updated([
                    'id' => $id,
                    'unit_code' => $unitRolling['no_unit'] ?? '',
                    'old_location' => $unitRolling['previous_location'] ?? 'Unknown',
                    'new_location' => $data['current_location'],
                    'updated_by' => session('username') ?? session('user_id'),
                    'url' => base_url('/operational/unit-rolling')
                ]);
            }
            
            return $this->response->setJSON(['success' => true, 'message' => 'Unit location updated successfully!']);
        }
        return $this->response->setJSON(['success' => false, 'message' => 'Failed to update unit location.']);
    }
} 