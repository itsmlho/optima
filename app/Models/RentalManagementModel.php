<?php

namespace App\Models;

use CodeIgniter\Model;

class RentalManagementModel extends Model[object Object]  protected $table = rentals;protected $primaryKey = 'rental_id;    protected $useAutoIncrement = true;
    protected $returnType = 'array;    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields =      customer_name,customer_phone',customer_email,  customer_address',unit_id', 'start_date',end_date,   rental_duration, aily_rate, tal_amount',status',
       payment_status,deposit_amount,notes',created_by,       approved_by', approved_date', 'created_at',updated_at'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime;protected $createdField =created_at;protected $updatedField = updated_at';

    // Validation
    protected $validationRules =       customer_name => equired|max_length[100],
        customer_phone => equired|max_length[20],
        customer_email => equired|valid_email',
        customer_address => equired|max_length[500],
        unit_id => equired|integer',
        start_date => equired|valid_date',
        end_date => equired|valid_date',
        rental_duration => equired|integer|greater_than0        daily_rate => equired|numeric|greater_than0      total_amount => equired|numeric|greater_than[0
        status => equired|in_list[pending,active,completed,cancelled],
        payment_status => equired|in_list[pending,partial,paid,overdue],
        deposit_amount => permit_empty|numeric|greater_than_equal_to0,
        notes => permit_empty'
    ];

    protected $validationMessages = ;

    protected $skipValidation = false;
    protected $cleanValidationRules = true;

    // Rental Management Methods
    public function getAllRentals($filters =])
    [object Object]
        $builder = $this->builder();
        $builder->select(r.*, f.unit_code, f.brand, f.model, f.capacity')
               ->join('forklifts f', f.forklift_id = r.unit_id', left;
        
        if (!empty($filters['search])) {
            $builder->groupStart()
                ->like(r.customer_name', $filters['search'])
                ->orLike('r.customer_phone', $filters['search'])
                ->orLike('r.customer_email', $filters['search'])
                ->orLike('f.unit_code', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['status])) {
            $builder->where('r.status', $filters['status']);
        }

        if (!empty($filters[payment_status])) {
            $builder->where('r.payment_status', $filters['payment_status']);
        }

        if (!empty($filters['date_from'])) {
            $builder->where(r.start_date >=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $builder->where(r.end_date <=', $filters['date_to']);
        }

        return $builder->orderBy('r.created_at', 'DESC')->get()->getResultArray();
    }

    public function getRentalById($rentalId)
    [object Object]
        $builder = $this->builder();
        $builder->select(r.*, f.unit_code, f.brand, f.model, f.capacity, f.location')
               ->join('forklifts f', f.forklift_id = r.unit_id', 'left')
               ->where('r.rental_id', $rentalId);
        
        return $builder->get()->getRowArray();
    }

    public function getActiveRentals()
    [object Object]      return $this->where('status,active)->findAll();
    }

    public function getRentalStats()
  [object Object]
        return [
            total_rentals' => $this->countAll(),
           active_rentals' => $this->where('status', 'active')->countAllResults(),
          completed_rentals' => $this->where('status', 'completed')->countAllResults(),
           pending_rentals' => $this->where(status',pending')->countAllResults(),
            cancelled_rentals' => $this->where('status', 'cancelled')->countAllResults(),
            total_revenue => $this->selectSum('total_amount')->get()->getRow()->total_amount ?? 0,
           pending_payments' => $this->where('payment_status',pending')->countAllResults(),
            overdue_payments' => $this->where('payment_status',overdue')->countAllResults()
        ];
    }

    public function createRental($data)
    [object Object]      return $this->insert($data);
    }

    public function updateRental($rentalId, $data)
    [object Object]      return $this->update($rentalId, $data);
    }

    public function approveRental($rentalId, $approvedBy)
    [object Object]      return $this->update($rentalId,         status' => 'active',
          approved_by => $approvedBy,
           approved_date=> date('Y-m-d H:i:s)       ]);
    }

    public function completeRental($rentalId)
    [object Object]      return $this->update($rentalId, ['status' => completed']);
    }

    public function cancelRental($rentalId)
    [object Object]      return $this->update($rentalId, ['status' => cancelled']);
    }

    public function updatePaymentStatus($rentalId, $paymentStatus)
    [object Object]      return $this->update($rentalId, ['payment_status' => $paymentStatus]);
    }

    public function getRentalsByCustomer($customerEmail)
    [object Object]      return $this->where('customer_email', $customerEmail)->findAll();
    }

    public function getRentalsByUnit($unitId)
    [object Object]      return $this->where(unit_id', $unitId)->findAll();
    }

    public function getOverdueRentals()
  [object Object]
        $today = date('Y-m-d);
        return $this->where('end_date <', $today)
                   ->where('status', 'active')
                   ->findAll();
    }

    public function getUpcomingRentals($days = 7)
  [object Object]
        $startDate = date(Y-m-d);
        $endDate = date(Y-m-d', strtotime("+{$days} days"));
        
        return $this->where(start_date >=', $startDate)
                   ->where(start_date <=', $endDate)
                   ->where(status', 'pending')
                   ->findAll();
    }

    // Work Order Methods
    public function getAllWorkOrders($filters =])
    [object Object]
        $builder = $this->db->table('work_orders');
        
        if (!empty($filters['search])) {
            $builder->groupStart()
                ->like(work_order_number', $filters['search'])
                ->orLike('description', $filters['search'])
                ->groupEnd();
        }

        if (!empty($filters['status'])) {
            $builder->where(status', $filters['status']);
        }

        if (!empty($filters['priority'])) {
            $builder->where('priority', $filters['priority']);
        }

        return $builder->orderBy(created_at', 'DESC')->get()->getResultArray();
    }

    public function getWorkOrderById($workOrderId)
    [object Object]      return $this->db->table('work_orders')->where(work_order_id,$workOrderId)->get()->getRowArray();
    }

    public function createWorkOrder($data)
    [object Object]      return $this->db->table('work_orders')->insert($data);
    }

    public function updateWorkOrder($workOrderId, $data)
    [object Object]      return $this->db->table('work_orders')->where(work_order_id, $workOrderId)->update($data);
    }

    public function getWorkOrderStats()
  [object Object]
        return [
            total_work_orders' => $this->db->table('work_orders')->countAllResults(),
           pending_work_orders' => $this->db->table('work_orders')->where(status',pending')->countAllResults(),
          in_progress_work_orders' => $this->db->table('work_orders')->where('status', 'in_progress')->countAllResults(),
           completed_work_orders' => $this->db->table('work_orders')->where('status', 'completed')->countAllResults(),
            cancelled_work_orders' => $this->db->table('work_orders')->where('status', 'cancelled')->countAllResults()
        ];
    }

    public function getWorkOrdersByUnit($unitId)
    [object Object]      return $this->db->table('work_orders)->where(unit_id', $unitId)->findAll();
    }

    public function getWorkOrdersByTechnician($technicianId)
    [object Object]      return $this->db->table('work_orders')->where('assigned_technician', $technicianId)->findAll();
    }

    public function getPendingWorkOrders()
    [object Object]      return $this->db->table('work_orders')
                       ->where(status', 'pending')
                       ->orderBy('priority', 'DESC')
                       ->orderBy(created_at', 'ASC')
                       ->get()
                       ->getResultArray();
    }

    public function assignWorkOrder($workOrderId, $technicianId)
    [object Object]      return $this->db->table('work_orders')
                       ->where(work_order_id', $workOrderId)
                       ->update([
                        assigned_technician=> $technicianId,
                        status' => 'in_progress',
                           assigned_date=> date('Y-m-d H:i:s')
                       ]);
    }

    public function completeWorkOrder($workOrderId, $completionData)
    [object Object]      return $this->db->table('work_orders')
                       ->where(work_order_id', $workOrderId)
                       ->update(array_merge($completionData, [
                      status =>                    actual_completion=> date('Y-m-d H:i:s')
                       ]));
    }

    // Export Methods
    public function exportRentals($format = 'csv')
  [object Object]
        $rentals = $this->getAllRentals();
        
        switch ($format) [object Object]            case 'csv:            return $this->exportToCSV($rentals);
            case excel:            return $this->exportToExcel($rentals);
            case 'pdf:            return $this->exportToPDF($rentals);
            default:
                return $this->exportToCSV($rentals);
        }
    }

    private function exportToCSV($rentals)
  [object Object]
        $filename = rentals_' . date('Y-m-d_H-i-s) ..csv';
        $filepath = WRITEPATH . 'exports/' . $filename;
        
        // Create exports directory if it doesn't exist
        if (!is_dir(WRITEPATH . 'exports/')) {
            mkdir(WRITEPATH .exports/', 0777 true);
        }
        
        $fp = fopen($filepath, 'w');
        
        // Write headers
        fputcsv($fp,    Rental ID,Customer', 'Unit', 'Start Date',End Date',
           Duration, aily Rate,Total Amount', Status,Payment Status'
        ]);
        
        // Write data
        foreach ($rentals as $rental) [object Object]           fputcsv($fp,
                $rental['rental_id'],
                $rental[customer_name],
                $rental['unit_code'],
                $rental['start_date'],
                $rental['end_date'],
                $rental['rental_duration'],
                $rental['daily_rate'],
                $rental['total_amount'],
                $rental['status'],
                $rental[payment_status']
            ]);
        }
        
        fclose($fp);
        
        return $filepath;
    }

    private function exportToExcel($rentals)
  [object Object]
        // Implementation for Excel export
        return $this->exportToCSV($rentals); // Fallback to CSV for now
    }

    private function exportToPDF($rentals)
  [object Object]
        // Implementation for PDF export
        return $this->exportToCSV($rentals); // Fallback to CSV for now
    }
} 