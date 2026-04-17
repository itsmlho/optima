<?php

namespace App\Controllers;

use App\Models\PmScheduleModel;
use App\Models\PmJobModel;
use App\Models\WorkOrderModel;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * PmpsController - Preventive Maintenance Planned Service
 *
 * Handles:
 *   Pages:   /service/pm-schedules, /service/pm-job/:id
 *   API:     /service/pmps/*, /service/pm-schedules/*
 *
 * PMPS does NOT build its own WO system. It generates Work Orders with
 * order_type='PMPS' using the existing work_orders infrastructure.
 */
class PmpsController extends BaseController
{
    protected $db;
    protected $pmScheduleModel;
    protected $pmJobModel;
    protected $woModel;

    public function __construct()
    {
        $this->db              = \Config\Database::connect();
        $this->pmScheduleModel = new PmScheduleModel();
        $this->pmJobModel      = new PmJobModel();
        $this->woModel         = new WorkOrderModel();
        helper(['form', 'url', 'auth']);
    }

    // =========================================================================
    // PAGES
    // =========================================================================

    /**
     * GET /service/pm-schedules — PM Schedule management page.
     */
    public function schedules()
    {
        $categories = $this->db->table('work_order_categories')
            ->where('is_active', 1)->orderBy('category_name', 'ASC')->get()->getResultArray();
        $priorities = $this->db->table('work_order_priorities')
            ->where('is_active', 1)->orderBy('priority_level', 'ASC')->get()->getResultArray();

        return view('service/pm_schedules', [
            'title'       => 'PM Schedules | OPTIMA',
            'page_title'  => 'Jadwal Preventive Maintenance',
            'breadcrumbs' => ['/' => 'Dashboard', '/service/pmps' => 'PMPS', '/service/pm-schedules' => 'PM Schedules'],
            'categories'  => $categories,
            'priorities'  => $priorities,
        ]);
    }

    /**
     * GET /service/pm-job/:id — PM Job detail + checklist page.
     */
    public function jobDetail(int $id)
    {
        $job = $this->pmJobModel->getJobDetail($id);
        if (!$job) {
            return redirect()->to(base_url('service/pmps'))->with('error', 'PM Job tidak ditemukan');
        }

        $categories = $this->db->table('work_order_categories')
            ->where('is_active', 1)->orderBy('category_name', 'ASC')->get()->getResultArray();
        $priorities = $this->db->table('work_order_priorities')
            ->where('is_active', 1)->orderBy('priority_level', 'ASC')->get()->getResultArray();

        return view('service/pm_job_detail', [
            'title'       => $job['pm_number'] . ' | PMPS | OPTIMA',
            'page_title'  => 'Detail PM Job',
            'breadcrumbs' => ['/' => 'Dashboard', '/service/pmps' => 'PMPS', '' => $job['pm_number']],
            'job'         => $job,
            'categories'  => $categories,
            'priorities'  => $priorities,
        ]);
    }

    // =========================================================================
    // PMPS DASHBOARD API
    // =========================================================================

    /**
     * GET /service/pmps/stats
     */
    public function getStats()
    {
        try {
            return $this->response->setJSON(['success' => true, 'data' => $this->pmJobModel->getStats()]);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::getStats] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memuat statistik']);
        }
    }

    /**
     * GET /service/pmps/getPmJobs — DataTable endpoint.
     */
    public function getPmJobs()
    {
        try {
            $draw   = (int) ($this->request->getGet('draw') ?? 1);
            $start  = (int) ($this->request->getGet('start') ?? 0);
            $length = (int) ($this->request->getGet('length') ?? 10);
            $search = $this->request->getGet('search')['value'] ?? '';

            $filters = [
                'status'        => $this->request->getGet('status') ?? '',
                'unit_id'       => $this->request->getGet('unit_id') ?? '',
                'month'         => $this->request->getGet('month') ?? '',
                'departemen_id' => $this->request->getGet('departemen_id') ?? '',
            ];

            $this->pmJobModel->markOverdueJobs();
            $result          = $this->pmJobModel->getPmJobs($filters, $start, $length, $search);
            $totalUnfiltered = (int) $this->db->table('pm_jobs')->countAllResults(false);

            return $this->response->setJSON([
                'draw'            => $draw,
                'recordsTotal'    => $totalUnfiltered,
                'recordsFiltered' => $result['total'],
                'data'            => $result['data'],
            ]);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::getPmJobs] ' . $e->getMessage());
            return $this->response->setJSON(['draw' => 0, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }
    }

    /**
     * POST /service/pmps/generate — Generate PM jobs from all due schedules.
     */
    public function generateJobs()
    {
        try {
            $userId    = session()->get('user_id') ?? 1;
            $schedules = $this->pmScheduleModel->getSchedulesDue();
            $generated = 0;

            foreach ($schedules as $schedule) {
                $dueDate = $schedule['next_pm_date'] ?? date('Y-m-d');
                if (empty($dueDate) && $schedule['interval_days']) {
                    $dueDate = date('Y-m-d');
                }
                $dueHm = $schedule['next_pm_hm'] ?? null;

                $this->pmJobModel->insert([
                    'pm_number'   => $this->pmJobModel->generatePmNumber(),
                    'schedule_id' => $schedule['id'],
                    'unit_id'     => $schedule['unit_id'],
                    'due_date'    => $dueDate,
                    'due_hm'      => $dueHm,
                    'status'      => 'SCHEDULED',
                    'created_by'  => $userId,
                ]);
                $generated++;
            }

            $msg = $generated > 0
                ? "Berhasil generate {$generated} PM job baru"
                : 'Tidak ada jadwal PM yang perlu digenerate saat ini';

            return $this->response->setJSON(['success' => true, 'message' => $msg, 'generated' => $generated]);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::generateJobs] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal generate PM jobs: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /service/pmps/getJobDetail/:id — JSON detail for modal.
     */
    public function getJobDetail(int $id)
    {
        try {
            $job = $this->pmJobModel->getJobDetail($id);
            if (!$job) {
                return $this->response->setJSON(['success' => false, 'message' => 'PM Job tidak ditemukan']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $job]);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::getJobDetail] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memuat detail']);
        }
    }

    /**
     * POST /service/pmps/createWorkOrder/:id — Create a WO from a PM job.
     * Uses existing work_orders infrastructure with order_type='PMPS'.
     */
    public function createWorkOrder(int $jobId)
    {
        try {
            $job = $this->pmJobModel->getJobDetail($jobId);
            if (!$job) {
                return $this->response->setJSON(['success' => false, 'message' => 'PM Job tidak ditemukan']);
            }
            if (!empty($job['work_order_id'])) {
                return $this->response->setJSON([
                    'success'          => false,
                    'message'          => 'WO sudah dibuat untuk PM job ini',
                    'work_order_id'    => $job['work_order_id'],
                    'work_order_number'=> $job['work_order_number'],
                ]);
            }

            $userId = session()->get('user_id') ?? 1;

            // Get initial WO status (prefer OPEN)
            $statusRow = $this->db->table('work_order_statuses')
                ->whereIn('status_code', ['OPEN', 'PENDING', 'NEW'])
                ->where('is_active', 1)
                ->orderBy('id', 'ASC')
                ->limit(1)
                ->get()->getRowArray();

            if (!$statusRow) {
                $statusRow = $this->db->table('work_order_statuses')
                    ->where('is_active', 1)->limit(1)->get()->getRowArray();
            }
            if (!$statusRow) {
                return $this->response->setJSON(['success' => false, 'message' => 'Status WO tidak tersedia']);
            }

            // Resolve category (use schedule's or first available)
            $categoryId = (int) ($job['wo_category_id'] ?? 0);
            if (!$categoryId) {
                $cat        = $this->db->table('work_order_categories')->where('is_active', 1)->limit(1)->get()->getRowArray();
                $categoryId = $cat ? (int) $cat['id'] : 1;
            }

            $priorityId = (int) ($job['priority_id'] ?? 1);

            $woNumber = $this->woModel->generateWorkOrderNumber();

            $this->db->transStart();

            $woId = $this->woModel->insert([
                'work_order_number'   => $woNumber,
                'report_date'         => date('Y-m-d H:i:s'),
                'unit_id'             => $job['unit_id'],
                'order_type'          => 'PMPS',
                'priority_id'         => $priorityId,
                'category_id'         => $categoryId,
                'subcategory_id'      => $job['wo_subcategory_id'] ?: null,
                'complaint_description' => 'Preventive Maintenance: ' . $job['schedule_name'] . ' (PM#: ' . $job['pm_number'] . ')',
                'status_id'           => $statusRow['id'],
                'hm'                  => $job['current_hm'],
                'notes'               => 'Auto-generated dari PMPS. PM Job: ' . $job['pm_number'],
                'created_by'          => $userId,
            ], true);

            $this->pmJobModel->update($jobId, ['work_order_id' => $woId, 'status' => 'IN_PROGRESS']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed');
            }

            return $this->response->setJSON([
                'success'           => true,
                'message'           => 'Work Order ' . $woNumber . ' berhasil dibuat',
                'work_order_id'     => $woId,
                'work_order_number' => $woNumber,
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[PmpsController::createWorkOrder] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal membuat WO: ' . $e->getMessage()]);
        }
    }

    /**
     * GET /service/pmps/getChecklist/:id
     */
    public function getChecklist(int $jobId)
    {
        try {
            $checklists = $this->db->table('pm_job_checklists')
                ->where('pm_job_id', $jobId)->orderBy('id', 'ASC')->get()->getResultArray();

            if (empty($checklists)) {
                $job = $this->pmJobModel->find($jobId);
                if ($job) {
                    $checklists = $this->db->table('pm_checklist_templates')
                        ->where('schedule_id', $job['schedule_id'])
                        ->orderBy('item_order', 'ASC')->get()->getResultArray();
                }
            }

            return $this->response->setJSON(['success' => true, 'data' => $checklists]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memuat checklist']);
        }
    }

    /**
     * POST /service/pmps/saveChecklist/:id
     */
    public function saveChecklist(int $jobId)
    {
        try {
            $job = $this->pmJobModel->find($jobId);
            if (!$job) {
                return $this->response->setJSON(['success' => false, 'message' => 'PM Job tidak ditemukan']);
            }

            $items  = $this->request->getPost('items') ?? [];
            $userId = session()->get('user_id') ?? 1;
            $now    = date('Y-m-d H:i:s');

            $this->db->table('pm_job_checklists')->where('pm_job_id', $jobId)->delete();

            foreach ($items as $item) {
                $this->db->table('pm_job_checklists')->insert([
                    'pm_job_id'        => $jobId,
                    'template_item_id' => $item['template_item_id'] ?? null,
                    'item_name'        => $item['item_name'],
                    'action_type'      => $item['action_type'] ?? 'CHECK',
                    'result'           => $item['result'] ?? 'OK',
                    'notes'            => $item['notes'] ?? null,
                    'checked_by'       => $userId,
                    'checked_at'       => $now,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ]);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Checklist berhasil disimpan']);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::saveChecklist] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan checklist']);
        }
    }

    /**
     * POST /service/pmps/complete/:id — Complete a PM job.
     * Updates last_pm_date/hm on the schedule and calculates next PM.
     */
    public function completeJob(int $jobId)
    {
        try {
            $job = $this->pmJobModel->find($jobId);
            if (!$job) {
                return $this->response->setJSON(['success' => false, 'message' => 'PM Job tidak ditemukan']);
            }
            if ($job['status'] === 'COMPLETED') {
                return $this->response->setJSON(['success' => false, 'message' => 'PM Job sudah selesai']);
            }

            $actualDate = $this->request->getPost('actual_date') ?? date('Y-m-d');
            $actualHm   = $this->request->getPost('actual_hm') !== null && $this->request->getPost('actual_hm') !== ''
                ? (float) $this->request->getPost('actual_hm') : null;
            $notes      = $this->request->getPost('notes') ?? null;
            $userId     = session()->get('user_id') ?? 1;

            $this->db->transStart();

            $this->pmJobModel->update($jobId, [
                'status'       => 'COMPLETED',
                'actual_date'  => $actualDate,
                'actual_hm'    => $actualHm,
                'notes'        => $notes,
                'completed_by' => $userId,
            ]);

            $this->pmScheduleModel->updateLastPm($job['schedule_id'], $actualDate, $actualHm);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed');
            }

            return $this->response->setJSON(['success' => true, 'message' => 'PM Job berhasil diselesaikan']);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', '[PmpsController::completeJob] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal: ' . $e->getMessage()]);
        }
    }

    // =========================================================================
    // PM SCHEDULES API
    // =========================================================================

    /**
     * GET /service/pm-schedules/getSchedules — DataTable.
     */
    public function getSchedules()
    {
        try {
            $draw   = (int) ($this->request->getGet('draw') ?? 1);
            $start  = (int) ($this->request->getGet('start') ?? 0);
            $length = (int) ($this->request->getGet('length') ?? 10);
            $search = $this->request->getGet('search')['value'] ?? '';

            $filters = [
                'search'    => $search,
                'unit_id'   => $this->request->getGet('unit_id') ?? '',
                'is_active' => $this->request->getGet('is_active') ?? '',
            ];

            $totalUnfiltered = (int) $this->db->table('pm_schedules')->countAllResults(false);
            $all   = $this->pmScheduleModel->getSchedules($filters);
            $total = count($all);
            $data  = array_slice($all, $start, $length);

            return $this->response->setJSON([
                'draw'            => $draw,
                'recordsTotal'    => $totalUnfiltered,
                'recordsFiltered' => $total,
                'data'            => $data,
            ]);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::getSchedules] ' . $e->getMessage());
            return $this->response->setJSON(['draw' => 0, 'recordsTotal' => 0, 'recordsFiltered' => 0, 'data' => []]);
        }
    }

    /**
     * GET /service/pm-schedules/get/:id
     */
    public function getSchedule(int $id)
    {
        try {
            $schedule = $this->pmScheduleModel->getScheduleWithTemplates($id);
            if (!$schedule) {
                return $this->response->setJSON(['success' => false, 'message' => 'Jadwal tidak ditemukan']);
            }
            return $this->response->setJSON(['success' => true, 'data' => $schedule]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memuat jadwal']);
        }
    }

    /**
     * POST /service/pm-schedules/store
     */
    public function storeSchedule()
    {
        try {
            if (!$this->validate([
                'unit_id'       => 'required|integer',
                'schedule_name' => 'required|min_length[3]',
                'trigger_type'  => 'required|in_list[CALENDAR,HM,BOTH]',
            ])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors'  => $this->validator->getErrors(),
                ]);
            }

            $userId      = session()->get('user_id') ?? 1;
            $triggerType = $this->request->getPost('trigger_type');
            $intervalDays = $this->request->getPost('interval_days') ? (int) $this->request->getPost('interval_days') : null;
            $intervalHm   = $this->request->getPost('interval_hm') ? (int) $this->request->getPost('interval_hm') : null;
            $startDate    = $this->request->getPost('start_date') ?: null;

            // Calculate initial next_pm_date / next_pm_hm
            $nextPmDate = null;
            $nextPmHm   = null;

            if ($startDate) {
                $nextPmDate = $startDate;
            } elseif (in_array($triggerType, ['CALENDAR', 'BOTH']) && $intervalDays) {
                $nextPmDate = date('Y-m-d', strtotime('+' . $intervalDays . ' days'));
            }

            if (in_array($triggerType, ['HM', 'BOTH']) && $intervalHm) {
                $unitRow  = $this->db->table('inventory_unit')
                    ->select('hour_meter')->where('id_inventory_unit', $this->request->getPost('unit_id'))->get()->getRowArray();
                $nextPmHm = $unitRow ? ((float) $unitRow['hour_meter'] + $intervalHm) : $intervalHm;
            }

            $scheduleId = $this->pmScheduleModel->insert([
                'unit_id'          => $this->request->getPost('unit_id'),
                'schedule_name'    => $this->request->getPost('schedule_name'),
                'trigger_type'     => $triggerType,
                'interval_days'    => $intervalDays,
                'interval_hm'      => $intervalHm,
                'next_pm_date'     => $nextPmDate,
                'next_pm_hm'       => $nextPmHm,
                'wo_category_id'   => $this->request->getPost('wo_category_id') ?: null,
                'wo_subcategory_id'=> $this->request->getPost('wo_subcategory_id') ?: null,
                'priority_id'      => $this->request->getPost('priority_id') ?: null,
                'is_active'        => 1,
                'notes'            => $this->request->getPost('notes') ?: null,
                'created_by'       => $userId,
            ], true);

            // Save checklist templates
            $items = $this->request->getPost('checklist_items') ?? [];
            if (!empty($items)) {
                $this->saveChecklistTemplatesForSchedule($scheduleId, $items);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Jadwal PM berhasil dibuat', 'id' => $scheduleId]);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::storeSchedule] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal membuat jadwal: ' . $e->getMessage()]);
        }
    }

    /**
     * POST /service/pm-schedules/update/:id
     */
    public function updateSchedule(int $id)
    {
        try {
            $this->pmScheduleModel->update($id, [
                'schedule_name'    => $this->request->getPost('schedule_name'),
                'trigger_type'     => $this->request->getPost('trigger_type'),
                'interval_days'    => $this->request->getPost('interval_days') ? (int) $this->request->getPost('interval_days') : null,
                'interval_hm'      => $this->request->getPost('interval_hm') ? (int) $this->request->getPost('interval_hm') : null,
                'next_pm_date'     => $this->request->getPost('start_date') ?: null,
                'next_pm_hm'       => $this->request->getPost('next_pm_hm') !== '' ? (float) $this->request->getPost('next_pm_hm') : null,
                'wo_category_id'   => $this->request->getPost('wo_category_id') ?: null,
                'wo_subcategory_id'=> $this->request->getPost('wo_subcategory_id') ?: null,
                'priority_id'      => $this->request->getPost('priority_id') ?: null,
                'is_active'        => $this->request->getPost('is_active') ?? 1,
                'notes'            => $this->request->getPost('notes') ?: null,
            ]);

            $items = $this->request->getPost('checklist_items') ?? [];
            if (!empty($items)) {
                $this->db->table('pm_checklist_templates')->where('schedule_id', $id)->delete();
                $this->saveChecklistTemplatesForSchedule($id, $items);
            }

            return $this->response->setJSON(['success' => true, 'message' => 'Jadwal PM berhasil diperbarui']);
        } catch (\Exception $e) {
            log_message('error', '[PmpsController::updateSchedule] ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal memperbarui jadwal']);
        }
    }

    /**
     * DELETE /service/pm-schedules/delete/:id — Soft-delete (set is_active=0).
     */
    public function deleteSchedule(int $id)
    {
        try {
            $activeJobs = $this->db->table('pm_jobs')
                ->where('schedule_id', $id)
                ->whereIn('status', ['SCHEDULED', 'IN_PROGRESS'])
                ->countAllResults();

            if ($activeJobs > 0) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tidak dapat menonaktifkan jadwal yang masih memiliki PM job aktif',
                ]);
            }

            $this->pmScheduleModel->update($id, ['is_active' => 0]);
            return $this->response->setJSON(['success' => true, 'message' => 'Jadwal PM berhasil dinonaktifkan']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menghapus jadwal']);
        }
    }

    /**
     * GET /service/pm-schedules/getChecklistTemplates/:id
     */
    public function getChecklistTemplates(int $scheduleId)
    {
        $items = $this->db->table('pm_checklist_templates')
            ->where('schedule_id', $scheduleId)
            ->orderBy('item_order', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON(['success' => true, 'data' => $items]);
    }

    /**
     * POST /service/pm-schedules/saveChecklistTemplates/:id
     */
    public function saveChecklistTemplates(int $scheduleId)
    {
        try {
            $items = $this->request->getPost('items') ?? [];
            $this->db->table('pm_checklist_templates')->where('schedule_id', $scheduleId)->delete();
            $this->saveChecklistTemplatesForSchedule($scheduleId, $items);
            return $this->response->setJSON(['success' => true, 'message' => 'Template checklist berhasil disimpan']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['success' => false, 'message' => 'Gagal menyimpan template']);
        }
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    private function saveChecklistTemplatesForSchedule(int $scheduleId, array $items): void
    {
        $now = date('Y-m-d H:i:s');
        foreach ($items as $order => $item) {
            if (empty(trim($item['item_name'] ?? ''))) {
                continue;
            }
            $this->db->table('pm_checklist_templates')->insert([
                'schedule_id'  => $scheduleId,
                'item_order'   => $item['item_order'] ?? $order,
                'item_name'    => trim($item['item_name']),
                'item_category'=> $item['item_category'] ?? null,
                'action_type'  => $item['action_type'] ?? 'CHECK',
                'is_required'  => $item['is_required'] ?? 1,
                'notes'        => $item['notes'] ?? null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);
        }
    }
}
