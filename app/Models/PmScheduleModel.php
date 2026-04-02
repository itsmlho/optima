<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * PmScheduleModel - Manages PM schedule definitions per unit.
 * Each schedule defines WHEN and WHAT to do for preventive maintenance.
 * Trigger types: CALENDAR (every N days), HM (every N hour-meter), BOTH.
 */
class PmScheduleModel extends Model
{
    protected $table      = 'pm_schedules';
    protected $primaryKey = 'id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'unit_id', 'schedule_name', 'trigger_type',
        'interval_days', 'interval_hm',
        'last_pm_date', 'last_pm_hm',
        'next_pm_date', 'next_pm_hm',
        'wo_category_id', 'wo_subcategory_id', 'priority_id',
        'is_active', 'notes', 'created_by',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Get all schedules with joined unit and category info.
     */
    public function getSchedules(array $filters = []): array
    {
        $builder = $this->db->table('pm_schedules ps')
            ->select("ps.*,
                iu.no_unit, iu.hour_meter, iu.serial_number,
                mu.merk_unit AS merk, mu.model_unit AS model,
                c.customer_name, cl.location_name,
                woc.category_name AS wo_category_name,
                p.priority_name,
                CONCAT(u.first_name, ' ', u.last_name) AS created_by_name")
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ps.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->join('kontrak_unit ku', "ku.unit_id = iu.id_inventory_unit AND ku.status IN ('ACTIVE','TEMP_ACTIVE') AND ku.is_temporary = 0", 'left')
            ->join('kontrak k', 'k.id = ku.kontrak_id', 'left')
            ->join('customers c', 'c.id = k.customer_id', 'left')
            ->join('customer_locations cl', 'cl.id = ku.customer_location_id', 'left')
            ->join('work_order_categories woc', 'woc.id = ps.wo_category_id', 'left')
            ->join('work_order_priorities p', 'p.id = ps.priority_id', 'left')
            ->join('users u', 'u.id = ps.created_by', 'left');

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $builder->where('ps.is_active', (int) $filters['is_active']);
        }
        if (!empty($filters['unit_id'])) {
            $builder->where('ps.unit_id', (int) $filters['unit_id']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart()
                ->like('ps.schedule_name', $filters['search'])
                ->orLike('iu.no_unit', $filters['search'])
                ->orLike('c.customer_name', $filters['search'])
                ->groupEnd();
        }

        return $builder->orderBy('ps.next_pm_date', 'ASC')->get()->getResultArray();
    }

    /**
     * Get a single schedule with its checklist template items.
     */
    public function getScheduleWithTemplates(int $id): ?array
    {
        $schedule = $this->db->table('pm_schedules ps')
            ->select('ps.*, iu.no_unit, iu.hour_meter, iu.serial_number, mu.merk_unit AS merk, mu.model_unit AS model')
            ->join('inventory_unit iu', 'iu.id_inventory_unit = ps.unit_id', 'left')
            ->join('model_unit mu', 'mu.id_model_unit = iu.model_unit_id', 'left')
            ->where('ps.id', $id)
            ->get()->getRowArray();

        if (!$schedule) {
            return null;
        }

        $schedule['checklist_templates'] = $this->db->table('pm_checklist_templates')
            ->where('schedule_id', $id)
            ->orderBy('item_order', 'ASC')
            ->get()->getResultArray();

        return $schedule;
    }

    /**
     * Get all active schedules that are due for PM job generation.
     * Due = next_pm_date <= today + 7 days, OR current unit HM >= next_pm_hm - 10.
     * Skips schedules that already have an open (SCHEDULED / IN_PROGRESS) PM job.
     */
    public function getSchedulesDue(): array
    {
        $lookaheadDate = date('Y-m-d', strtotime('+7 days'));

        $sql = "
            SELECT ps.*, iu.no_unit, iu.hour_meter AS current_hm
            FROM pm_schedules ps
            LEFT JOIN inventory_unit iu ON iu.id_inventory_unit = ps.unit_id
            WHERE ps.is_active = 1
              AND NOT EXISTS (
                  SELECT 1 FROM pm_jobs pj
                  WHERE pj.schedule_id = ps.id
                    AND pj.status IN ('SCHEDULED','IN_PROGRESS')
              )
              AND (
                  (
                      ps.trigger_type IN ('CALENDAR','BOTH')
                      AND (ps.next_pm_date IS NULL OR ps.next_pm_date <= ?)
                  )
                  OR (
                      ps.trigger_type IN ('HM','BOTH')
                      AND ps.next_pm_hm IS NOT NULL
                      AND iu.hour_meter >= ps.next_pm_hm - 10
                  )
              )
        ";

        return $this->db->query($sql, [$lookaheadDate])->getResultArray();
    }

    /**
     * After a PM job is completed, update last PM info and calculate next PM dates.
     */
    public function updateLastPm(int $id, string $date, ?float $hm): bool
    {
        $schedule = $this->find($id);
        if (!$schedule) {
            return false;
        }

        $updateData = [
            'last_pm_date' => $date,
            'last_pm_hm'   => $hm,
        ];

        if (in_array($schedule['trigger_type'], ['CALENDAR', 'BOTH']) && $schedule['interval_days']) {
            $updateData['next_pm_date'] = date('Y-m-d', strtotime($date . ' +' . $schedule['interval_days'] . ' days'));
        }

        if (in_array($schedule['trigger_type'], ['HM', 'BOTH']) && $schedule['interval_hm'] && $hm !== null) {
            $updateData['next_pm_hm'] = $hm + $schedule['interval_hm'];
        }

        return $this->update($id, $updateData);
    }
}
