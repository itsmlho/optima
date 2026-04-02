<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PmJobModel - Manages PM job instances.
 * Each PM job is a scheduled maintenance event for a specific unit,
 * generated from a PmSchedule. It tracks due dates, status, and links to Work Orders.
 */
class PmJobModel extends Model
{
    protected $table      = 'pm_jobs';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'pm_number', 'schedule_id', 'unit_id',
        'due_date', 'due_hm', 'actual_date', 'actual_hm',
        'work_order_id', 'status', 'notes',
        'created_by', 'completed_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get PM jobs for DataTable with all joined info.
     * Returns ['data' => [...], 'total' => int].
     */
    public function getPmJobs(array $filters = [], int $start = 0, int $length = 10, string $search = ''): array
    {
        $builder = $this->db->table('pm_jobs pj')
            ->select("pj.*,
                CASE
                    WHEN pj.status = 'SCHEDULED' AND pj.due_date < CURDATE() THEN 'OVERDUE'
                    ELSE pj.status
                END AS display_status,
                ps.schedule_name, ps.trigger_type, ps.interval_days, ps.interval_hm,
                iu.no_unit, iu.hour_meter AS current_hm,
                mu.merk_unit AS merk, mu.model_unit AS model,
                c.customer_name, cl.location_name,
                wo.work_order_number,
                wos.status_name AS wo_status,
                CONCAT(u.first_name,' ',u.last_name) AS created_by_name")
            ->join('pm_schedules ps', 'ps.id = pj.schedule_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = pj.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kontrak_unit ku', "ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0", 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
            ->join('work_orders wo', 'wo.id = pj.work_order_id', 'left')
            ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
            ->join('users u', 'u.id = pj.created_by', 'left');

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'OVERDUE') {
                $builder->where("(pj.status = 'OVERDUE' OR (pj.status = 'SCHEDULED' AND pj.due_date < CURDATE()))");
            } elseif ($filters['status'] === 'ACTIVE') {
                $builder->whereIn('pj.status', ['SCHEDULED', 'IN_PROGRESS', 'OVERDUE']);
            } else {
                $builder->where('pj.status', $filters['status']);
            }
        }
        if (!empty($filters['unit_id'])) {
            $builder->where('pj.unit_id', (int) $filters['unit_id']);
        }
        if (!empty($filters['month'])) {
            $builder->where("DATE_FORMAT(pj.due_date, '%Y-%m')", $filters['month']);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('pj.pm_number', $search)
                ->orLike('iu.no_unit', $search)
                ->orLike('ps.schedule_name', $search)
                ->orLike('c.customer_name', $search)
                ->groupEnd();
        }

        $total = (clone $builder)->countAllResults(false);
        $data  = $builder->orderBy('pj.due_date', 'ASC')->limit($length, $start)->get()->getResultArray();

        return ['data' => $data, 'total' => $total];
    }

    /**
     * Get dashboard statistics.
     */
    public function getStats(): array
    {
        $db = $this->db;
        return [
            'total'               => (int) $db->query("SELECT COUNT(*) AS cnt FROM pm_jobs")->getRowArray()['cnt'],
            'overdue'             => (int) $db->query("SELECT COUNT(*) AS cnt FROM pm_jobs WHERE status='OVERDUE' OR (status='SCHEDULED' AND due_date < CURDATE())")->getRowArray()['cnt'],
            'due_this_week'       => (int) $db->query("SELECT COUNT(*) AS cnt FROM pm_jobs WHERE status='SCHEDULED' AND due_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)")->getRowArray()['cnt'],
            'in_progress'         => (int) $db->query("SELECT COUNT(*) AS cnt FROM pm_jobs WHERE status='IN_PROGRESS'")->getRowArray()['cnt'],
            'completed_this_month'=> (int) $db->query("SELECT COUNT(*) AS cnt FROM pm_jobs WHERE status='COMPLETED' AND DATE_FORMAT(actual_date,'%Y-%m')=DATE_FORMAT(CURDATE(),'%Y-%m')")->getRowArray()['cnt'],
            'active_schedules'    => (int) $db->query("SELECT COUNT(*) AS cnt FROM pm_schedules WHERE is_active=1")->getRowArray()['cnt'],
        ];
    }

    /**
     * Generate unique PM number: PM-YYYYMMDD-NNNN
     */
    public function generatePmNumber(): string
    {
        $prefix  = 'PM-' . date('Ymd') . '-';
        $lastJob = $this->db->table('pm_jobs')
            ->like('pm_number', $prefix, 'after')
            ->orderBy('pm_number', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $seq = 1;
        if ($lastJob) {
            $parts = explode('-', $lastJob['pm_number']);
            $seq   = (int) end($parts) + 1;
        }

        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Bulk-mark SCHEDULED jobs with past due_date as OVERDUE.
     * Called automatically before DataTable queries.
     */
    public function markOverdueJobs(): void
    {
        $this->db->query("UPDATE pm_jobs SET status='OVERDUE' WHERE status='SCHEDULED' AND due_date < CURDATE()");
    }

    /**
     * Get full detail of a PM job including checklist and history.
     */
    public function getJobDetail(int $id): ?array
    {
        $job = $this->db->table('pm_jobs pj')
            ->select("pj.*,
                ps.schedule_name, ps.trigger_type, ps.interval_days, ps.interval_hm,
                ps.wo_category_id, ps.wo_subcategory_id, ps.priority_id,
                iu.no_unit, iu.hour_meter AS current_hm, iu.serial_number,
                mu.merk_unit AS merk, mu.model_unit AS model,
                c.customer_name, cl.location_name,
                wo.work_order_number,
                wos.status_name AS wo_status, wos.status_code AS wo_status_code,
                CONCAT(u.first_name,' ',u.last_name) AS created_by_name,
                CONCAT(uc.first_name,' ',uc.last_name) AS completed_by_name,
                woc.category_name AS wo_category_name")
            ->join('pm_schedules ps', 'ps.id = pj.schedule_id', 'left')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = pj.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kontrak_unit ku', "ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0", 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
            ->join('work_orders wo', 'wo.id = pj.work_order_id', 'left')
            ->join('work_order_statuses wos', 'wos.id = wo.status_id', 'left')
            ->join('users u', 'u.id = pj.created_by', 'left')
            ->join('users uc', 'uc.id = pj.completed_by', 'left')
            ->join('work_order_categories woc', 'woc.id = ps.wo_category_id', 'left')
            ->where('pj.id', $id)
            ->get()->getRowArray();

        if (!$job) {
            return null;
        }

        // Checklist: saved results OR templates (not yet started)
        $checklists = $this->db->table('pm_job_checklists')
            ->where('pm_job_id', $id)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        if (empty($checklists)) {
            $checklists = $this->db->table('pm_checklist_templates')
                ->where('schedule_id', $job['schedule_id'])
                ->orderBy('item_order', 'ASC')
                ->get()->getResultArray();
            $job['checklist_from_template'] = true;
        } else {
            $job['checklist_from_template'] = false;
        }

        $job['checklists'] = $checklists;

        // Last 5 completed PM jobs for this unit (excluding current)
        $job['history'] = $this->db->table('pm_jobs pj2')
            ->select('pj2.pm_number, pj2.actual_date, pj2.actual_hm, pj2.status, pj2.work_order_id, wo2.work_order_number')
            ->join('work_orders wo2', 'wo2.id = pj2.work_order_id', 'left')
            ->where('pj2.unit_id', $job['unit_id'])
            ->where('pj2.id !=', $id)
            ->where('pj2.status', 'COMPLETED')
            ->orderBy('pj2.actual_date', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        return $job;
    }
}
