<?php

return [
    'access_denied' => 'You do not have permission to access the Reports center.',
    'no_segments_for_role' => 'No report templates are available for your current permissions.',

    'breadcrumb_dashboard' => 'Dashboard',
    'breadcrumb_reports' => 'Reports',

    'page_title_hub' => 'Reports center',
    'hub_intro_title' => 'Standard reports vs. exports in each module',
    'hub_intro_body' => 'Use this area for named, period-based reports (Excel / PDF / CSV) stored in your history. For a raw export of the current grid and filters, use the Export button on that module page.',

    'rental_page_title' => 'Rental reports',
    'maintenance_page_title' => 'Maintenance reports',
    'financial_page_title' => 'Financial reports',
    'inventory_page_title' => 'Inventory & sparepart reports',
    'custom_page_title' => 'Custom reports',

    'stats_total' => 'Total generated',
    'stats_completed' => 'Completed',
    'stats_pending' => 'Pending',
    'stats_this_month' => 'This month',

    'section_named_reports' => 'Named reports in this category',
    'btn_generate_excel' => 'Generate (Excel)',
    'btn_all_reports' => 'All report types',
    'btn_finance_module' => 'Open Finance reports module',

    'custom_intro' => 'Pick a report type, date range, and format from the main Reports center. Custom naming and parameters are saved with each generated file.',
    'custom_cta' => 'Go to Reports center',

    'generate_success' => 'Report generated successfully.',
    'generate_failed' => 'Report generation failed',
    'error_type_required' => 'Report type is required.',
    'select_report_type_placeholder' => 'Select report type',
    'error_method_not_allowed' => 'Method not allowed.',
    'schedule_not_available' => 'Scheduled reports are not enabled yet. Use Generate for on-demand exports.',

    'catalog_rental_monthly_title' => 'Monthly rental / contract overlap',
    'catalog_rental_monthly_desc' => 'Contracts overlapping the selected period (from kontrak).',
    'catalog_contract_perf_title' => 'Contract performance',
    'catalog_contract_perf_desc' => 'Same contract set with duration context for review.',
    'catalog_unit_util_title' => 'Unit utilization by status',
    'catalog_unit_util_desc' => 'Fleet counts grouped by status_unit_id.',

    'catalog_revenue_title' => 'Revenue (invoices)',
    'catalog_revenue_desc' => 'Invoices in the date range by issue_date.',
    'catalog_expenses_title' => 'Expenses (placeholder)',
    'catalog_expenses_desc' => 'Reserved for purchasing / AP when a single source is defined.',
    'catalog_pl_title' => 'Profit & loss (simple)',
    'catalog_pl_desc' => 'Revenue from invoices vs. placeholder expenses.',

    'catalog_maint_sched_title' => 'Maintenance schedule window',
    'catalog_maint_sched_desc' => 'Work orders created in period with requested repair time.',
    'catalog_wo_title' => 'Work order register',
    'catalog_wo_desc' => 'All work orders created in the selected period.',
    'catalog_downtime_title' => 'Cycle time (proxy)',
    'catalog_downtime_desc' => 'Days from WO created_at to completion_date where both exist.',

    'catalog_stock_title' => 'Sparepart stock levels',
    'catalog_stock_desc' => 'Current stock from inventory_spareparts with master codes.',
    'catalog_spare_usage_title' => 'Sparepart usage (WO)',
    'catalog_spare_usage_desc' => 'Lines from work_order_sparepart_usage in the period.',
    'catalog_asset_title' => 'Fleet list & rates',
    'catalog_asset_desc' => 'Units with monthly/daily rates (not book depreciation).',

    'summary_snapshot' => 'Summary snapshot',
    'recent_activity' => 'Recent activity',

    'hub_nav_all' => 'All sections',
    'hub_nav_rental' => 'Rental hub',
    'hub_nav_maintenance' => 'Maintenance hub',
    'hub_nav_financial' => 'Financial hub',
    'hub_nav_inventory' => 'Inventory hub',
    'hub_nav_custom' => 'Custom',

    'summary_matches_export' => 'Numbers below use the same data as each report file export for the period shown.',
    'rows_in_export' => 'Rows in export',
];
