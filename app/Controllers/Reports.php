<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Traits\ActivityLoggingTrait;
use CodeIgniter\HTTP\ResponseInterface;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * Reports center: named period-based exports stored in `reports` + files under WRITEPATH/reports.
 *
 * Segments -> RBAC (reports.{resource}.view):
 * - contract: rental_monthly, contract_performance
 * - unit: unit_utilization, stock_levels, sparepart_usage, asset_valuation
 * - revenue: revenue, expenses, profit_loss
 * - maintenance_schedule, work_orders, downtime: contract OR unit
 *
 * Hub summaries use the same getReportData() as quick/custom export for the category default date range.
 */
class Reports extends BaseController
{
    use ActivityLoggingTrait;
    protected $db;
    
    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Central reports hub: requires any reports.*.view permission (contract / revenue / unit).
     */
    protected function canAccessReports(): bool
    {
        if (!session()->get('isLoggedIn')) {
            return false;
        }

        return $this->canViewResource('reports', 'contract')
            || $this->canViewResource('reports', 'revenue')
            || $this->canViewResource('reports', 'unit');
    }

    /**
     * Per-report-type access beyond "any reports.*.view".
     */
    protected function canAccessReportSegment(string $segment): bool
    {
        if (!$this->canAccessReports()) {
            return false;
        }

        return match ($segment) {
            'rental_monthly', 'contract_performance' => $this->canViewResource('reports', 'contract'),
            'unit_utilization', 'stock_levels', 'sparepart_usage', 'asset_valuation' => $this->canViewResource('reports', 'unit'),
            'revenue', 'expenses', 'profit_loss' => $this->canViewResource('reports', 'revenue'),
            'maintenance_schedule', 'work_orders', 'downtime' => $this->canViewResource('reports', 'contract')
                || $this->canViewResource('reports', 'unit'),
            default => false,
        };
    }

    /**
     * @param list<array{id: string, title: string, description: string}> $items
     * @return list<array{id: string, title: string, description: string}>
     */
    private function filterCatalogItems(array $items): array
    {
        $out = [];
        foreach ($items as $item) {
            if (!empty($item['id']) && $this->canAccessReportSegment((string) $item['id'])) {
                $out[] = $item;
            }
        }

        return $out;
    }

    private function getDateRangeForCategory(string $category): array
    {
        // All categories default to Month-To-Date (MTD) for consistency
        return [date('Y-m-01'), date('Y-m-d')];
    }

    /**
     * Snapshot per segment using the same queries as file export.
     *
     * @return array{period_from: string, period_to: string, segments: array<string, array{title: string, summary: array, row_count: int}>}
     */
    /**
     * @param list<array{id: string, title: string, description: string}>|null $prefilteredItems
     */
    private function getCategorySummary(string $category, string $dateFrom, string $dateTo, ?array $prefilteredItems = null): array
    {
        $items = $prefilteredItems ?? $this->filterCatalogItems($this->getCatalogByCategory($category));
        $segments = [];
        foreach ($items as $item) {
            $id = (string) $item['id'];
            try {
                $data = $this->getReportData($id, $dateFrom, $dateTo, []);
                $segments[$id] = [
                    'title' => $item['title'] ?? $this->getReportTypeName($id),
                    'summary' => $data['summary'] ?? [],
                    'row_count' => count($data['data'] ?? []),
                ];
            } catch (\Throwable $e) {
                log_message('error', 'getCategorySummary ' . $id . ': ' . $e->getMessage());
                $segments[$id] = [
                    'title' => $item['title'] ?? $this->getReportTypeName($id),
                    'summary' => ['error' => $e->getMessage()],
                    'row_count' => 0,
                ];
            }
        }

        return [
            'period_from' => $dateFrom,
            'period_to' => $dateTo,
            'segments' => $segments,
        ];
    }

    /**
     * @return list<array{key: string, title: string, icon: string, header: string, items: array, summary: array, date_from: string, date_to: string}>
     */
    private function buildReportsCenterSections(): array
    {
        $meta = [
            'rental' => [
                'title' => lang('Reports.rental_page_title'),
                'icon' => 'fas fa-handshake me-2 text-primary',
                'header' => 'primary',
            ],
            'maintenance' => [
                'title' => lang('Reports.maintenance_page_title'),
                'icon' => 'fas fa-wrench me-2 text-warning',
                'header' => 'warning',
            ],
            'financial' => [
                'title' => lang('Reports.financial_page_title'),
                'icon' => 'fas fa-dollar-sign me-2 text-success',
                'header' => 'success',
            ],
            'inventory' => [
                'title' => lang('Reports.inventory_page_title'),
                'icon' => 'fas fa-boxes me-2 text-info',
                'header' => 'info',
            ],
        ];
        $sections = [];
        foreach (['rental', 'maintenance', 'financial', 'inventory'] as $cat) {
            $items = $this->filterCatalogItems($this->getCatalogByCategory($cat));
            if ($items === []) {
                continue;
            }
            [$from, $to] = $this->getDateRangeForCategory($cat);
            $sections[] = array_merge($meta[$cat], [
                'key' => $cat,
                'items' => $items,
                'summary' => $this->getCategorySummary($cat, $from, $to, $items),
                'date_from' => $from,
                'date_to' => $to,
            ]);
        }

        return $sections;
    }

    /**
     * @return array<string, string>
     */
    private function getCustomReportTypeOptions(): array
    {
        $ids = [
            'rental_monthly', 'contract_performance', 'unit_utilization',
            'revenue', 'expenses', 'profit_loss',
            'maintenance_schedule', 'work_orders', 'downtime',
            'stock_levels', 'sparepart_usage', 'asset_valuation',
        ];
        $opts = [];
        foreach ($ids as $id) {
            if ($this->canAccessReportSegment($id)) {
                $opts[$id] = $this->getReportTypeName($id);
            }
        }

        return $opts;
    }

    public function index()
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        $this->ensureReportsSchema();

        $section = strtolower(trim((string) $this->request->getGet('section')));
        $allowedSections = ['', 'rental', 'maintenance', 'financial', 'inventory', 'custom'];
        if (!in_array($section, $allowedSections, true)) {
            $section = '';
        }

        $data = [
            'title' => lang('Reports.page_title_hub'),
            'page_title' => lang('Reports.page_title_hub'),
            'breadcrumbs' => [
                '/' => lang('Reports.breadcrumb_dashboard'),
                '/reports' => lang('Reports.breadcrumb_reports'),
            ],
            'loadDataTables' => true,
            'report_stats' => $this->getReportStats(),
            'recent_reports' => $this->getRecentReports(),
            'reports_active_section' => $section,
            'reports_center_sections' => $this->buildReportsCenterSections(),
            'custom_report_type_options' => $this->getCustomReportTypeOptions(),
        ];

        return view('reports/index', $data);
    }

    public function rental()
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        return redirect()->to('/reports?section=rental');
    }

    public function maintenance()
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        return redirect()->to('/reports?section=maintenance');
    }

    public function financial()
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        return redirect()->to('/reports?section=financial');
    }

    public function inventory()
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        return redirect()->to('/reports?section=inventory');
    }

    public function custom()
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        return redirect()->to('/reports?section=custom');
    }

    /**
     * Quick generate: default date range = current month, format = Excel.
     */
    public function quickReport(string $segment): ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => lang('Reports.error_method_not_allowed'),
            ]);
        }

        if (!$this->canAccessReports()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        if (!$this->canAccessReportSegment($segment)) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        $dateFrom = date('Y-m-01');
        $dateTo = date('Y-m-d');
        $format = 'excel';
        $reportId = null;

        try {
            $reportData = $this->getReportData($segment, $dateFrom, $dateTo, []);
            $displayName = $this->getReportTypeName($segment) . ' (' . $dateFrom . ' — ' . $dateTo . ')';
            $reportId = $this->saveReportRecord($segment, $format, $reportData, $displayName, [
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'quick'     => true,
            ]);
            $this->markReportProcessing($reportId);

            $filename = $this->generateReportFile($segment, $format, $reportData, $reportId, $displayName);
            $this->logActivity('report_generated', "Quick report {$segment} ({$format})");

            return $this->response->setJSON([
                'success'      => true,
                'message'      => lang('Reports.generate_success'),
                'report_id'    => $reportId,
                'filename'     => $filename,
                'download_url' => base_url('reports/download/' . $reportId),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'quickReport failed: ' . $e->getMessage());
            if ($reportId !== null) {
                $this->markReportFailed($reportId, $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Reports.generate_failed'),
            ]);
        }
    }

    public function view($reportId)
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        $this->ensureReportsSchema();

        $report = $this->db->table('reports r')
            ->select('r.*, u.first_name, u.last_name')
            ->join('users u', 'u.id = r.user_id', 'left')
            ->where('r.id', (int) $reportId)
            ->get()
            ->getRowArray();

        if (!$report) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$this->canAccessReportSegment((string) ($report['type'] ?? ''))) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        if (empty($report['filename']) && !empty($report['file_path'])) {
            $report['filename'] = basename((string) $report['file_path']);
        }

        $data = [
            'title' => $report['name'] . ' | OPTIMA',
            'report' => $report,
        ];

        return view('reports/view', $data);
    }

    /**
     * POST reports/clear-old
     * Deletes report records (and their files) older than $days days.
     * Default retention period: 30 days.
     */
    public function clearOldReports(): ResponseInterface
    {
        if (!$this->request->is('post')) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => lang('Reports.error_method_not_allowed'),
            ]);
        }

        if (!$this->canAccessReports()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        $days = (int) ($this->request->getPost('days') ?: 30);
        if ($days < 1) {
            $days = 30;
        }

        $cutoff = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        try {
            $this->ensureReportsSchema();

            $old = $this->db->table('reports')
                ->where('created_at <', $cutoff)
                ->get()
                ->getResultArray();

            $deleted = 0;
            $filesRemoved = 0;

            foreach ($old as $record) {
                // Unlink file if it exists
                $filepath = null;
                if (!empty($record['file_path']) && is_file($record['file_path'])) {
                    $filepath = $record['file_path'];
                } elseif (!empty($record['filename'])) {
                    $candidate = WRITEPATH . 'reports/' . $record['filename'];
                    if (is_file($candidate)) {
                        $filepath = $candidate;
                    }
                }
                if ($filepath !== null && @unlink($filepath)) {
                    $filesRemoved++;
                }

                $this->db->table('reports')->where('id', $record['id'])->delete();
                $deleted++;
            }

            $this->logActivity('reports_cleared', "Cleared {$deleted} old reports (>{$days} days)");

            return $this->response->setJSON([
                'success'       => true,
                'message'       => "Deleted {$deleted} report(s) older than {$days} days ({$filesRemoved} file(s) removed).",
                'deleted_count' => $deleted,
                'files_removed' => $filesRemoved,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'clearOldReports failed: ' . $e->getMessage());

            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.',
            ]);
        }
    }

    public function scheduleReport(): ResponseInterface
    {
        if (!$this->canAccessReports()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => lang('Reports.schedule_not_available'),
        ]);
    }

    // API Methods for Report Generation
    public function generateReport()
    {
        if (!$this->canAccessReports()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        $this->ensureReportsSchema();

        $request = service('request');
        $reportType = $request->getPost('type');
        $format = $request->getPost('format') ?: 'pdf';
        $dateFrom = $request->getPost('date_from') ?: date('Y-m-01');
        $dateTo = $request->getPost('date_to') ?: date('Y-m-d');
        $filters = $request->getPost('filters') ?: [];
        $customName = $request->getPost('report_name');
        $description = $request->getPost('description');

        if (empty($reportType)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Reports.error_type_required'),
            ]);
        }

        if (!$this->canAccessReportSegment((string) $reportType)) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        $reportId = null;
        try {
            $reportData = $this->getReportData($reportType, $dateFrom, $dateTo, $filters);

            $reportId = $this->saveReportRecord($reportType, $format, $reportData, $customName, [
                'date_from'   => $dateFrom,
                'date_to'     => $dateTo,
                'filters'     => $filters,
                'description' => $description,
            ]);
            $this->markReportProcessing($reportId);

            $filename = $this->generateReportFile($reportType, $format, $reportData, $reportId, $customName);

            $this->logActivity('report_generated', "Generated {$reportType} report in {$format} format");

            return $this->response->setJSON([
                'success'      => true,
                'message'      => lang('Reports.generate_success'),
                'report_id'    => $reportId,
                'filename'     => $filename,
                'download_url' => base_url('reports/download/' . $reportId),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Report generation failed: ' . $e->getMessage());
            if ($reportId !== null) {
                $this->markReportFailed($reportId, $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Reports.generate_failed'),
            ]);
        }
    }

    public function generateCustomReport()
    {
        if (!$this->canAccessReports()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        $this->ensureReportsSchema();

        $request = service('request');
        $reportName = $request->getPost('report_name');
        $reportType = $request->getPost('report_type');
        $format = $request->getPost('format') ?: 'pdf';
        $dateFrom = $request->getPost('date_from');
        $dateTo = $request->getPost('date_to');
        $fields = $request->getPost('fields') ?: [];
        $filters = $request->getPost('filters') ?: [];

        if (empty($reportType) || !$this->canAccessReportSegment((string) $reportType)) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        $reportId = null;
        try {
            $reportData = $this->getCustomReportData($reportType, $dateFrom, $dateTo, $fields, $filters);

            $reportId = $this->saveReportRecord($reportType, $format, $reportData, $reportName, [
                'date_from' => $dateFrom,
                'date_to'   => $dateTo,
                'fields'    => $fields,
                'filters'   => $filters,
                'custom'    => true,
            ]);
            $this->markReportProcessing($reportId);

            $filename = $this->generateReportFile($reportType, $format, $reportData, $reportId, $reportName);

            $this->logActivity('custom_report_generated', "Generated custom report: {$reportName}");

            return $this->response->setJSON([
                'success'      => true,
                'message'      => lang('Reports.generate_success'),
                'report_id'    => $reportId,
                'filename'     => $filename,
                'download_url' => base_url("reports/download/{$reportId}"),
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Custom report generation failed: ' . $e->getMessage());
            if ($reportId !== null) {
                $this->markReportFailed($reportId, $e->getMessage());
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Reports.generate_failed'),
            ]);
        }
    }

    public function download($reportId)
    {
        if (!$this->canAccessReports()) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        $report = $this->getReportById($reportId);

        if (!$report) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$this->canAccessReportSegment((string) ($report['type'] ?? ''))) {
            return redirect()->to('/dashboard')->with('error', lang('Reports.access_denied'));
        }

        $filepath = null;
        if (!empty($report['file_path']) && is_file($report['file_path'])) {
            $filepath = $report['file_path'];
        } elseif (!empty($report['filename'])) {
            $candidate = WRITEPATH . 'reports/' . $report['filename'];
            if (is_file($candidate)) {
                $filepath = $candidate;
            }
        }

        if ($filepath === null) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Report file not found');
        }

        $this->logActivity('report_downloaded', 'Downloaded report: ' . ($report['name'] ?? ''));

        return $this->response->download($filepath, null);
    }

    public function delete($reportId)
    {
        if (!$this->canAccessReports()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }

        $report = $this->getReportById($reportId);
        
        if (!$report) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Laporan tidak ditemukan'
            ]);
        }

        if (!$this->canAccessReportSegment((string) ($report['type'] ?? ''))) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => lang('Reports.access_denied'),
            ]);
        }
        
        try {
            $filepath = null;
            if (!empty($report['file_path']) && is_file($report['file_path'])) {
                $filepath = $report['file_path'];
            } elseif (!empty($report['filename'])) {
                $c = WRITEPATH . 'reports/' . $report['filename'];
                if (is_file($c)) {
                    $filepath = $c;
                }
            }
            if ($filepath !== null) {
                unlink($filepath);
            }
            
            // Delete database record
            $this->db->table('reports')->where('id', $reportId)->delete();
            
            // Log activity
            $this->logActivity('report_deleted', "Deleted report: {$report['name']}");
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Report deleted successfully'
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal memproses permintaan. Silakan coba lagi.'
            ]);
        }
    }

    // Private Methods
    private function getReportCategories()
    {
        return [
            'rental' => [
                'name' => 'Rental Reports',
                'count' => 15,
                'icon' => 'fas fa-handshake',
                'color' => 'primary'
            ],
            'financial' => [
                'name' => 'Financial Reports',
                'count' => 8,
                'icon' => 'fas fa-dollar-sign',
                'color' => 'success'
            ],
            'maintenance' => [
                'name' => 'Maintenance Reports',
                'count' => 12,
                'icon' => 'fas fa-wrench',
                'color' => 'warning'
            ],
            'inventory' => [
                'name' => 'Inventory Reports',
                'count' => 6,
                'icon' => 'fas fa-boxes',
                'color' => 'info'
            ]
        ];
    }

    /**
     * Named reports for category hub pages (distinct from per-page grid export).
     *
     * @return list<array{id: string, title: string, description: string}>
     */
    private function getCatalogByCategory(string $category): array
    {
        $map = [
            'rental' => [
                ['id' => 'rental_monthly', 'title' => lang('Reports.catalog_rental_monthly_title'), 'description' => lang('Reports.catalog_rental_monthly_desc')],
                ['id' => 'contract_performance', 'title' => lang('Reports.catalog_contract_perf_title'), 'description' => lang('Reports.catalog_contract_perf_desc')],
                ['id' => 'unit_utilization', 'title' => lang('Reports.catalog_unit_util_title'), 'description' => lang('Reports.catalog_unit_util_desc')],
            ],
            'financial' => [
                ['id' => 'revenue', 'title' => lang('Reports.catalog_revenue_title'), 'description' => lang('Reports.catalog_revenue_desc')],
                ['id' => 'expenses', 'title' => lang('Reports.catalog_expenses_title'), 'description' => lang('Reports.catalog_expenses_desc')],
                ['id' => 'profit_loss', 'title' => lang('Reports.catalog_pl_title'), 'description' => lang('Reports.catalog_pl_desc')],
            ],
            'maintenance' => [
                ['id' => 'maintenance_schedule', 'title' => lang('Reports.catalog_maint_sched_title'), 'description' => lang('Reports.catalog_maint_sched_desc')],
                ['id' => 'work_orders', 'title' => lang('Reports.catalog_wo_title'), 'description' => lang('Reports.catalog_wo_desc')],
                ['id' => 'downtime', 'title' => lang('Reports.catalog_downtime_title'), 'description' => lang('Reports.catalog_downtime_desc')],
            ],
            'inventory' => [
                ['id' => 'stock_levels', 'title' => lang('Reports.catalog_stock_title'), 'description' => lang('Reports.catalog_stock_desc')],
                ['id' => 'sparepart_usage', 'title' => lang('Reports.catalog_spare_usage_title'), 'description' => lang('Reports.catalog_spare_usage_desc')],
                ['id' => 'asset_valuation', 'title' => lang('Reports.catalog_asset_title'), 'description' => lang('Reports.catalog_asset_desc')],
            ],
        ];

        return $map[$category] ?? [];
    }

    private function getRecentReports()
    {
        $this->ensureReportsSchema();
        
        try {
            return $this->db->table('reports r')
                           ->select('r.*, u.first_name, u.last_name')
                           ->join('users u', 'u.id = r.user_id', 'left')
                           ->orderBy('r.created_at', 'DESC')
                           ->limit(10)
                           ->get()
                           ->getResultArray();
        } catch (\Exception $e) {
            return [];
        }
    }

    private function getReportStats()
    {
        $this->ensureReportsSchema();
        
        try {
            return [
                'total_reports' => $this->db->table('reports')->countAll(),
                'this_month_reports' => $this->db->table('reports')
                    ->where('created_at >=', date('Y-m-01 00:00:00'))
                    ->where('created_at <=', date('Y-m-t 23:59:59'))
                    ->countAllResults(),
                'pending_reports' => $this->db->table('reports')
                                             ->whereIn('status', ['pending', 'processing'])
                                             ->countAllResults(),
                'completed_reports' => $this->db->table('reports')
                                               ->where('status', 'completed')
                                               ->countAllResults()
            ];
        } catch (\Exception $e) {
            // If tables don't exist, return default stats
            return [
                'total_reports' => 0,
                'this_month_reports' => 0,
                'pending_reports' => 0,
                'completed_reports' => 0
            ];
        }
    }

    private function getReportData($reportType, $dateFrom, $dateTo, $filters = [])
    {
        switch ($reportType) {
            case 'rental_monthly':
                return $this->getRentalMonthlyData($dateFrom, $dateTo, $filters);
            case 'contract_performance':
                return $this->getContractPerformanceData($dateFrom, $dateTo, $filters);
            case 'unit_utilization':
                return $this->getUnitUtilizationData($dateFrom, $dateTo, $filters);
            case 'revenue':
                return $this->getRevenueData($dateFrom, $dateTo, $filters);
            case 'expenses':
                return $this->getExpensesData($dateFrom, $dateTo, $filters);
            case 'profit_loss':
                return $this->getProfitLossData($dateFrom, $dateTo, $filters);
            case 'maintenance_schedule':
                return $this->getMaintenanceScheduleData($dateFrom, $dateTo, $filters);
            case 'work_orders':
                return $this->getWorkOrdersData($dateFrom, $dateTo, $filters);
            case 'downtime':
                return $this->getDowntimeData($dateFrom, $dateTo, $filters);
            case 'stock_levels':
                return $this->getStockLevelsData($dateFrom, $dateTo, $filters);
            case 'sparepart_usage':
                return $this->getSparepartUsageData($dateFrom, $dateTo, $filters);
            case 'asset_valuation':
                return $this->getAssetValuationData($dateFrom, $dateTo, $filters);
            default:
                throw new \InvalidArgumentException('Invalid report type');
        }
    }

    private function generateReportFile($reportType, $format, $reportData, $reportId, $customName = null)
    {
        $reportName = $customName ?: $this->getReportTypeName($reportType);
        $filename = $this->generateFilename($reportName, $format);
        $filepath = WRITEPATH . 'reports/' . $filename;
        
        // Create reports directory if it doesn't exist
        if (!is_dir(WRITEPATH . 'reports/')) {
            mkdir(WRITEPATH . 'reports/', 0755, true);
        }
        
        switch ($format) {
            case 'pdf':
                $this->generatePDF($reportData, $filepath, $reportName);
                break;
            case 'excel':
            case 'xlsx':
                $this->generateExcel($reportData, $filepath, $reportName);
                break;
            case 'csv':
                $this->generateCSV($reportData, $filepath);
                break;
            default:
                throw new \InvalidArgumentException('Invalid format');
        }

        $basename = basename($filepath);

        $this->db->table('reports')
                 ->where('id', $reportId)
                 ->update([
                     'file_path'  => $filepath,
                     'filename'   => $basename,
                     'file_size'  => file_exists($filepath) ? filesize($filepath) : null,
                     'data_count' => count($reportData['data'] ?? []),
                     'status'     => 'completed',
                     'updated_at' => date('Y-m-d H:i:s'),
                 ]);
        
        return $filename;
    }

    private function generatePDF($reportData, $filepath, $reportName)
    {
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);
        
        $dompdf = new Dompdf($options);
        
        $html = $this->generateReportHTML($reportData, $reportName);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        file_put_contents($filepath, $dompdf->output());
    }

    private function generateExcel($reportData, $filepath, $reportName)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $row = 1;
        $sheet->setCellValue('A' . $row, $reportName);
        $row += 2;

        $summary = $reportData['summary'] ?? [];
        if ($summary !== []) {
            $sheet->setCellValue('A' . $row, 'Summary');
            $row++;
            foreach ($summary as $key => $value) {
                $sheet->setCellValue('A' . $row, (string) $key);
                $display = is_scalar($value) || $value === null ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
                $sheet->setCellValue('B' . $row, $display);
                $row++;
            }
            $row++;
        }

        $dataRows = $reportData['data'] ?? [];
        if ($dataRows === []) {
            $sheet->setCellValue('A' . $row, 'No data rows for the selected period or filters.');
        } else {
            $headers = array_keys($dataRows[0]);
            $colIndex = 1;
            foreach ($headers as $header) {
                $sheet->setCellValue(
                    Coordinate::stringFromColumnIndex($colIndex) . $row,
                    ucfirst(str_replace('_', ' ', (string) $header))
                );
                $colIndex++;
            }
            $row++;

            foreach ($dataRows as $item) {
                $colIndex = 1;
                foreach ($item as $value) {
                    $cellVal = is_scalar($value) || $value === null ? $value : json_encode($value, JSON_UNESCAPED_UNICODE);
                    $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIndex) . $row, $cellVal);
                    $colIndex++;
                }
                $row++;
            }
        }

        (new Xlsx($spreadsheet))->save($filepath);
    }

    private function generateCSV($reportData, $filepath)
    {
        $file = fopen($filepath, 'w');
        
        // Write headers
        if (!empty($reportData['data'])) {
            fputcsv($file, array_keys($reportData['data'][0]));
            
            // Write data
            foreach ($reportData['data'] as $row) {
                fputcsv($file, $row);
            }
        }
        
        fclose($file);
    }

    private function generateReportHTML($reportData, $reportName)
    {
        $html = "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>{$reportName}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 40px; }
                .header { text-align: center; margin-bottom: 30px; }
                .company-info { margin-bottom: 20px; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f2f2f2; font-weight: bold; }
                .summary { background-color: #f9f9f9; padding: 15px; margin: 20px 0; }
                .footer { margin-top: 30px; font-size: 12px; color: #666; }
            </style>
        </head>
        <body>
            <div class='header'>
                <h1>OPTIMA - PT Sarana Mitra Luas Tbk</h1>
                <h2>{$reportName}</h2>
                <p>Generated on: " . date('d/m/Y H:i:s') . "</p>
            </div>
            
            <div class='company-info'>
                <strong>Company:</strong> PT Sarana Mitra Luas Tbk<br>
                <strong>Address:</strong> Jl. Industri No. 123, Jakarta<br>
                <strong>Phone:</strong> 021-12345678
            </div>";
        
        if (!empty($reportData['summary'])) {
            $html .= "<div class='summary'>";
            $html .= "<h3>Summary</h3>";
            foreach ($reportData['summary'] as $key => $value) {
                $html .= "<strong>" . ucfirst(str_replace('_', ' ', $key)) . ":</strong> {$value}<br>";
            }
            $html .= "</div>";
        }
        
        if (!empty($reportData['data'])) {
            $html .= "<table>";
            $html .= "<thead><tr>";
            foreach (array_keys($reportData['data'][0]) as $header) {
                $html .= '<th>' . esc(ucfirst(str_replace('_', ' ', (string) $header))) . '</th>';
            }
            $html .= "</tr></thead>";

            $html .= "<tbody>";
            foreach ($reportData['data'] as $row) {
                $html .= "<tr>";
                foreach ($row as $cell) {
                    $html .= '<td>' . esc((string) $cell) . '</td>';
                }
                $html .= "</tr>";
            }
            $html .= "</tbody>";
            $html .= "</table>";
        } else {
            $html .= "<p><em>No tabular data for this period.</em></p>";
        }
        
        $html .= "
            <div class='footer'>
                <p>This report was generated automatically by OPTIMA system.</p>
                <p>© " . date('Y') . " PT Sarana Mitra Luas Tbk. All rights reserved.</p>
            </div>
        </body>
        </html>";
        
        return $html;
    }

    private function saveReportRecord(
        string $reportType,
        string $format,
        array $reportData,
        ?string $customName = null,
        array $meta = []
    ): int {
        $this->ensureReportsSchema();

        $reportName = $customName ?: $this->getReportTypeName($reportType);

        $data = [
            'name'        => $reportName,
            'type'        => $reportType,
            'format'      => $format,
            'user_id'     => (int) (session()->get('user_id') ?: 1),
            'status'      => 'pending',
            'description' => $meta['description'] ?? null,
            'parameters'  => !empty($meta) ? json_encode($meta, JSON_UNESCAPED_UNICODE) : null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $this->db->table('reports')->insert($data);

        return (int) $this->db->insertID();
    }

    /**
     * Mark a report record as processing (called right after insert).
     */
    private function markReportProcessing(int $reportId): void
    {
        $this->db->table('reports')->where('id', $reportId)->update([
            'status'     => 'processing',
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Mark a report record as failed and persist the error message.
     */
    private function markReportFailed(int $reportId, string $errorMessage): void
    {
        $update = [
            'status'     => 'failed',
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        // Persist error in error_message if column exists, otherwise fall back to parameters.
        if ($this->db->fieldExists('error_message', 'reports')) {
            $update['error_message'] = mb_substr($errorMessage, 0, 2048);
        } else {
            $current = $this->db->table('reports')->where('id', $reportId)->get()->getRowArray();
            $params = [];
            if (!empty($current['parameters'])) {
                $params = json_decode((string) $current['parameters'], true) ?? [];
            }
            $params['_error'] = mb_substr($errorMessage, 0, 500);
            $update['parameters'] = json_encode($params, JSON_UNESCAPED_UNICODE);
        }
        $this->db->table('reports')->where('id', $reportId)->update($update);
    }

    /**
     * Ensure reports storage table exists with the correct schema.
     * CREATE TABLE columns are kept in sync with the live DB definition.
     * Existing installs get missing columns added incrementally.
     */
    private function ensureReportsSchema(): void
    {
        if (!$this->db->tableExists('reports')) {
            $forge = \Config\Database::forge();

            $forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                ],
                'type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 80,
                ],
                'format' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                ],
                'filename' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'file_path' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => true,
                ],
                'file_size' => [
                    'type'    => 'INT',
                    'null'    => true,
                ],
                'description' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'parameters' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'error_message' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'user_id' => [
                    'type'     => 'INT',
                    'unsigned' => true,
                ],
                'status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['pending', 'processing', 'completed', 'failed'],
                    'default'    => 'pending',
                ],
                'data_count' => [
                    'type'    => 'INT',
                    'default' => 0,
                ],
                'created_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'updated_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);

            $forge->addKey('id', true);
            $forge->addKey('user_id');
            $forge->addKey('type');
            $forge->addKey('status');
            $forge->createTable('reports');
        } else {
            // Incrementally add columns that may be missing from older installs.
            $addColumns = [
                'filename'      => 'ALTER TABLE reports ADD COLUMN filename VARCHAR(255) NULL AFTER format',
                'file_path'     => 'ALTER TABLE reports ADD COLUMN file_path VARCHAR(500) NULL',
                'file_size'     => 'ALTER TABLE reports ADD COLUMN file_size INT NULL',
                'description'   => 'ALTER TABLE reports ADD COLUMN description TEXT NULL',
                'parameters'    => 'ALTER TABLE reports ADD COLUMN parameters TEXT NULL',
                'error_message' => 'ALTER TABLE reports ADD COLUMN error_message TEXT NULL',
                'data_count'    => 'ALTER TABLE reports ADD COLUMN data_count INT NOT NULL DEFAULT 0',
                'user_id'       => 'ALTER TABLE reports ADD COLUMN user_id INT UNSIGNED NOT NULL DEFAULT 1',
            ];
            foreach ($addColumns as $col => $sql) {
                if (!$this->db->fieldExists($col, 'reports')) {
                    try {
                        $this->db->query($sql);
                    } catch (\Throwable $e) {
                        log_message('error', "Reports: could not add column {$col}: " . $e->getMessage());
                    }
                }
            }

            // Ensure `id` is AUTO_INCREMENT — some environments omit it on the column.
            try {
                $info = $this->db->query(
                    "SELECT EXTRA
                     FROM information_schema.COLUMNS
                     WHERE TABLE_SCHEMA = DATABASE()
                       AND TABLE_NAME   = 'reports'
                       AND COLUMN_NAME  = 'id'"
                )->getRowArray();

                if (stripos((string) ($info['EXTRA'] ?? ''), 'auto_increment') === false) {
                    $this->db->query('ALTER TABLE reports MODIFY COLUMN id INT UNSIGNED NOT NULL AUTO_INCREMENT');
                }
            } catch (\Throwable $e) {
                log_message('error', 'Reports: could not enforce AUTO_INCREMENT on id: ' . $e->getMessage());
            }
        }
    }

    private function getRentalMonthlyData($dateFrom, $dateTo, $filters)
    {
        $summary = ['period_from' => $dateFrom, 'period_to' => $dateTo];
        if (!$this->db->tableExists('kontrak')) {
            return ['summary' => array_merge($summary, ['note' => 'kontrak table not found']), 'data' => []];
        }

        try {
            $builder = $this->db->table('kontrak k');

            if ($this->db->fieldExists('tanggal_mulai', 'kontrak')) {
                $builder->where('k.tanggal_mulai <=', $dateTo);
            }
            if ($this->db->fieldExists('tanggal_berakhir', 'kontrak')) {
                $builder->groupStart()
                    ->where('k.tanggal_berakhir >=', $dateFrom)
                    ->orWhere('k.tanggal_berakhir IS NULL', null, false)
                ->groupEnd();
            }

            $select = 'k.id, k.no_kontrak, k.status, k.tanggal_mulai, k.tanggal_berakhir, k.total_units';
            if ($this->db->fieldExists('pelanggan', 'kontrak')) {
                $select .= ', k.pelanggan';
            }

            $rows = $builder->select($select)->orderBy('k.tanggal_mulai', 'DESC')->get()->getResultArray();
            $summary['contracts_in_period'] = count($rows);

            return ['summary' => $summary, 'data' => $rows];
        } catch (\Throwable $e) {
            log_message('error', 'getRentalMonthlyData: ' . $e->getMessage());

            return ['summary' => array_merge($summary, ['error' => $e->getMessage()]), 'data' => []];
        }
    }

    private function getContractPerformanceData($dateFrom, $dateTo, $filters)
    {
        $base = $this->getRentalMonthlyData($dateFrom, $dateTo, $filters);
        $data = [];
        foreach ($base['data'] as $row) {
            $start = $row['tanggal_mulai'] ?? null;
            $end = $row['tanggal_berakhir'] ?? null;
            $plannedDays = ($start && $end) ? max(0, (int) round((strtotime((string) $end) - strtotime((string) $start)) / 86400)) : null;
            $data[] = array_merge($row, [
                'planned_duration_days' => $plannedDays,
                'performance_note' => $plannedDays !== null && $plannedDays > 0 ? 'See planned vs actual in operations' : '',
            ]);
        }
        $base['data'] = $data;
        $base['summary']['report'] = 'Contract performance (overlap with selected period)';

        return $base;
    }

    private function getUnitUtilizationData($dateFrom, $dateTo, $filters)
    {
        if (!$this->db->tableExists('inventory_unit')) {
            return ['summary' => ['note' => 'inventory_unit not found'], 'data' => []];
        }

        try {
            $rows = $this->db->table('inventory_unit')
                ->select('status_unit_id, COUNT(*) AS unit_count')
                ->groupBy('status_unit_id')
                ->orderBy('status_unit_id', 'ASC')
                ->get()
                ->getResultArray();

            $total = (int) array_sum(array_map('intval', array_column($rows, 'unit_count')));

            return [
                'summary' => [
                    'total_units' => $total,
                    'distinct_statuses' => count($rows),
                    'as_of' => date('Y-m-d H:i:s'),
                ],
                'data' => $rows,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getUnitUtilizationData: ' . $e->getMessage());

            return ['summary' => ['error' => $e->getMessage()], 'data' => []];
        }
    }

    private function getRevenueData($dateFrom, $dateTo, $filters)
    {
        if (!$this->db->tableExists('invoices')) {
            return ['summary' => ['note' => 'invoices table not available'], 'data' => []];
        }

        try {
            $dateCol = $this->db->fieldExists('issue_date', 'invoices')
                ? 'issue_date'
                : ($this->db->fieldExists('billing_period_start', 'invoices') ? 'billing_period_start' : 'created_at');

            $builder = $this->db->table('invoices')
                ->select('id, invoice_number, total_amount, status, customer_id, ' . $dateCol . ' AS report_date', false)
                ->where($dateCol . ' >=', $dateFrom)
                ->where($dateCol . ' <=', $dateTo)
                ->orderBy($dateCol, 'DESC');

            $rows = $builder->get()->getResultArray();

            $sum = 0.0;
            foreach ($rows as $r) {
                $sum += (float) ($r['total_amount'] ?? 0);
            }

            return [
                'summary' => [
                    'invoice_count' => count($rows),
                    'total_amount' => round($sum, 2),
                    'currency' => 'IDR',
                ],
                'data' => $rows,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getRevenueData: ' . $e->getMessage());

            return ['summary' => ['error' => $e->getMessage()], 'data' => []];
        }
    }

    private function getExpensesData($dateFrom, $dateTo, $filters)
    {
        return [
            'summary' => [
                'note' => 'Connect purchasing / AP module tables when standardized (placeholder).',
                'period_from' => $dateFrom,
                'period_to' => $dateTo,
            ],
            'data' => [],
        ];
    }

    private function getProfitLossData($dateFrom, $dateTo, $filters)
    {
        $rev = $this->getRevenueData($dateFrom, $dateTo, $filters);
        $exp = $this->getExpensesData($dateFrom, $dateTo, $filters);

        $totalRev = (float) ($rev['summary']['total_amount'] ?? 0);
        $totalExp = 0.0;

        return [
            'summary' => [
                'revenue_total' => $totalRev,
                'expense_total' => $totalExp,
                'net' => $totalRev - $totalExp,
                'note' => 'Expenses use placeholder until AP data source is wired.',
            ],
            'data' => [
                [
                    'line' => 'Revenue (invoices)',
                    'amount' => $totalRev,
                ],
                [
                    'line' => 'Expenses (placeholder)',
                    'amount' => $totalExp,
                ],
            ],
        ];
    }

    private function getMaintenanceScheduleData($dateFrom, $dateTo, $filters)
    {
        if (!$this->db->tableExists('work_orders')) {
            return ['summary' => ['note' => 'work_orders not found'], 'data' => []];
        }

        try {
            $b = $this->db->table('work_orders')
                ->select('id, work_order_number, report_date, requested_repair_time, order_type, status_id, created_at')
                ->where('created_at >=', $dateFrom . ' 00:00:00')
                ->where('created_at <=', $dateTo . ' 23:59:59');

            if ($this->db->fieldExists('deleted_at', 'work_orders')) {
                $b->where('deleted_at', null);
            }

            $rows = $b->orderBy('requested_repair_time', 'ASC')->get()->getResultArray();

            return [
                'summary' => ['work_orders_in_period' => count($rows)],
                'data' => $rows,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getMaintenanceScheduleData: ' . $e->getMessage());

            return ['summary' => ['error' => $e->getMessage()], 'data' => []];
        }
    }

    private function getWorkOrdersData($dateFrom, $dateTo, $filters)
    {
        if (!$this->db->tableExists('work_orders')) {
            return ['summary' => ['note' => 'work_orders not found'], 'data' => []];
        }

        try {
            $b = $this->db->table('work_orders wo')
                ->select('wo.id, wo.work_order_number, wo.unit_id, wo.order_type, wo.status_id, wo.created_at, wo.completion_date, wo.complaint_description')
                ->where('wo.created_at >=', $dateFrom . ' 00:00:00')
                ->where('wo.created_at <=', $dateTo . ' 23:59:59');

            if ($this->db->fieldExists('deleted_at', 'work_orders')) {
                $b->where('wo.deleted_at', null);
            }

            $rows = $b->orderBy('wo.created_at', 'DESC')->get()->getResultArray();

            return [
                'summary' => ['count' => count($rows)],
                'data' => $rows,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getWorkOrdersData: ' . $e->getMessage());

            return ['summary' => ['error' => $e->getMessage()], 'data' => []];
        }
    }

    private function getDowntimeData($dateFrom, $dateTo, $filters)
    {
        $wo = $this->getWorkOrdersData($dateFrom, $dateTo, $filters);
        $filtered = array_values(array_filter($wo['data'], static function ($r) {
            return !empty($r['completion_date']) && !empty($r['created_at']);
        }));

        $lines = [];
        foreach ($filtered as $r) {
            $start = strtotime((string) $r['created_at']);
            $end = strtotime((string) $r['completion_date']);
            if ($start && $end && $end >= $start) {
                $days = round(($end - $start) / 86400, 2);
                $lines[] = [
                    'work_order_number' => $r['work_order_number'] ?? '',
                    'unit_id' => $r['unit_id'] ?? '',
                    'cycle_days' => $days,
                ];
            }
        }

        return [
            'summary' => [
                'records_with_completion' => count($lines),
                'note' => 'Cycle time proxy: completion_date minus created_at (refine with HM / downtime fields when available).',
            ],
            'data' => $lines,
        ];
    }

    private function getStockLevelsData($dateFrom, $dateTo, $filters)
    {
        if (!$this->db->tableExists('inventory_spareparts') || !$this->db->tableExists('sparepart')) {
            return ['summary' => ['note' => 'inventory_spareparts / sparepart not available'], 'data' => []];
        }

        try {
            $rows = $this->db->table('inventory_spareparts inv')
                ->select('sp.kode AS sparepart_code, sp.desc_sparepart AS sparepart_name, inv.stok, inv.lokasi_rak, inv.updated_at')
                ->join('sparepart sp', 'sp.id_sparepart = inv.sparepart_id', 'left')
                ->orderBy('sp.kode', 'ASC')
                ->get()
                ->getResultArray();

            return [
                'summary' => ['sku_rows' => count($rows)],
                'data' => $rows,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getStockLevelsData: ' . $e->getMessage());

            return ['summary' => ['error' => $e->getMessage()], 'data' => []];
        }
    }

    private function getSparepartUsageData($dateFrom, $dateTo, $filters)
    {
        if (!$this->db->tableExists('work_order_sparepart_usage')) {
            return ['summary' => ['note' => 'work_order_sparepart_usage not available'], 'data' => []];
        }

        try {
            $rows = $this->db->table('work_order_sparepart_usage u')
                ->select('u.id, u.work_order_id, u.quantity_used, u.used_at, u.created_at, wos.sparepart_code, wos.sparepart_name')
                ->join('work_order_spareparts wos', 'wos.id = u.work_order_sparepart_id', 'left')
                ->where('u.created_at >=', $dateFrom . ' 00:00:00')
                ->where('u.created_at <=', $dateTo . ' 23:59:59')
                ->orderBy('u.created_at', 'DESC')
                ->get()
                ->getResultArray();

            return [
                'summary' => ['usage_lines' => count($rows)],
                'data' => $rows,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getSparepartUsageData: ' . $e->getMessage());

            return ['summary' => ['error' => $e->getMessage()], 'data' => []];
        }
    }

    private function getAssetValuationData($dateFrom, $dateTo, $filters)
    {
        if (!$this->db->tableExists('inventory_unit')) {
            return ['summary' => ['note' => 'inventory_unit not found'], 'data' => []];
        }

        try {
            $rows = $this->db->table('inventory_unit')
                ->select('id_inventory_unit, no_unit, serial_number, tahun_unit, harga_sewa_bulanan, harga_sewa_harian, status_unit_id, departemen_id')
                ->orderBy('id_inventory_unit', 'ASC')
                ->get()
                ->getResultArray();

            $sumMonthly = 0.0;
            foreach ($rows as $r) {
                $sumMonthly += (float) ($r['harga_sewa_bulanan'] ?? 0);
            }

            return [
                'summary' => [
                    'unit_count' => count($rows),
                    'sum_monthly_rate' => round($sumMonthly, 2),
                    'note' => 'Rates are list prices on unit; book value requires fixed-asset schema.',
                ],
                'data' => $rows,
            ];
        } catch (\Throwable $e) {
            log_message('error', 'getAssetValuationData: ' . $e->getMessage());

            return ['summary' => ['error' => $e->getMessage()], 'data' => []];
        }
    }

    private function getReportTypeName($type)
    {
        $names = [
            'rental_monthly' => 'Monthly Rental Summary',
            'contract_performance' => 'Contract Performance Report',
            'unit_utilization' => 'Unit Utilization Report',
            'revenue' => 'Revenue Report',
            'expenses' => 'Expense Report',
            'profit_loss' => 'Profit & Loss Report',
            'maintenance_schedule' => 'Maintenance Schedule Report',
            'work_orders' => 'Work Orders Report',
            'downtime' => 'Downtime Analysis Report',
            'stock_levels' => 'Stock Levels Report',
            'sparepart_usage' => 'Sparepart Usage Report',
            'asset_valuation' => 'Asset Valuation Report'
        ];
        
        return $names[$type] ?? ucfirst(str_replace('_', ' ', $type));
    }

    private function normalizeExportExtension(string $format): string
    {
        return match ($format) {
            'excel', 'xlsx' => 'xlsx',
            'pdf' => 'pdf',
            'csv' => 'csv',
            default => 'pdf',
        };
    }

    private function generateFilename($reportName, $format)
    {
        $timestamp = date('Y-m-d_H-i-s');
        $safeName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $reportName);
        $ext = $this->normalizeExportExtension($format);

        return $safeName . '_' . $timestamp . '.' . $ext;
    }

    private function getReportById($reportId)
    {
        return $this->db->table('reports')
                       ->where('id', $reportId)
                       ->get()
                       ->getRowArray();
    }

    private function logActivity(string $action, string $description): void
    {
        try {
            (new \App\Models\SystemActivityLogModel())->logActivity([
                'table_name'         => 'reports',
                'action_type'        => $action,
                'action_description' => $description,
                'module_name'        => 'reports',
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'Reports logActivity: ' . $e->getMessage());
        }
    }

    // Additional helper methods for custom reports, filters, etc.
    private function getFilters()
    {
        return [
            'date_range' => ['last_7_days', 'last_30_days', 'last_3_months', 'last_year', 'custom'],
            'status' => ['active', 'completed', 'cancelled', 'pending'],
            'unit_type' => ['forklift_3t', 'forklift_5t', 'reach_truck', 'pallet_jack'],
            'client_type' => ['corporate', 'individual', 'government']
        ];
    }

    private function getDataSources()
    {
        return [
            'rentals' => 'Rental Data',
            'maintenance' => 'Maintenance Records',
            'inventory' => 'Inventory Data',
            'financial' => 'Financial Transactions',
            'users' => 'User Data',
            'assets' => 'Asset Information'
        ];
    }

    private function getReportTemplates()
    {
        return [
            'monthly_summary' => 'Monthly Summary Template',
            'performance_analysis' => 'Performance Analysis Template',
            'financial_overview' => 'Financial Overview Template',
            'maintenance_report' => 'Maintenance Report Template',
            'inventory_status' => 'Inventory Status Template'
        ];
    }

    private function getCustomReportData($reportType, $dateFrom, $dateTo, $fields, $filters)
    {
        // This would implement custom report data generation based on selected fields and filters
        return $this->getReportData($reportType, $dateFrom, $dateTo, $filters);
    }
} 