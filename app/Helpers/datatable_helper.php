<?php

/**
 * ====================================================================
 * OPTIMA DataTable Helper Functions
 * ====================================================================
 * 
 * Centralized DataTable configuration helper untuk konsistensi tabel
 * Dibuat: <?= date('Y-m-d') ?>
 * 
 * @package    OPTIMA
 * @category   Helpers
 * @author     OPTIMA Dev Team
 */

if (!function_exists('dt_config')) {
    /**
     * Generate standardized DataTable configuration
     * 
     * @param array $options    Configuration options
     * @return array            DataTable config array
     * 
     * @example
     * $config = dt_config([
     *     'ajax_url' => base_url('api/customers'),
     *     'columns' => ['name', 'email', 'phone', 'status'],
     *     'order' => [[0, 'asc']]
     * ]);
     */
    function dt_config(array $options = []): array
    {
        // Default configuration
        $defaultConfig = [
            'processing' => true,
            'serverSide' => true,
            'responsive' => true,
            'pageLength' => 10,
            'lengthMenu' => [[10, 25, 50, 100], [10, 25, 50, 100]],
            'order' => [[0, 'asc']],
            'language' => [
                'processing' => 'Processing...',
                'lengthMenu' => 'Show _MENU_ entries',
                'zeroRecords' => 'No matching records found',
                'info' => 'Showing _START_ to _END_ of _TOTAL_ entries',
                'infoEmpty' => 'Showing 0 to 0 of 0 entries',
                'infoFiltered' => '(filtered from _MAX_ total entries)',
                'search' => 'Search:',
                'paginate' => [
                    'first' => 'First',
                    'previous' => 'Previous',
                    'next' => 'Next',
                    'last' => 'Last'
                ]
            ],
            'dom' => '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' .
                     '<"row"<"col-sm-12"tr>>' .
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        ];

        // Merge with provided options
        $config = array_merge($defaultConfig, $options);

        return $config;
    }
}

if (!function_exists('dt_ajax_config')) {
    /**
     * Generate AJAX configuration for DataTable
     * 
     * @param string $url       AJAX URL
     * @param string $method    HTTP method (POST/GET)
     * @param array  $data      Additional data function
     * @return array            AJAX config
     */
    function dt_ajax_config(string $url, string $method = 'POST', array $data = []): array
    {
        return [
            'url' => $url,
            'type' => $method,
            'data' => $data
        ];
    }
}

if (!function_exists('dt_column')) {
    /**
     * Generate DataTable column configuration
     * 
     * @param string $data          Data field name or index
     * @param array  $options       Column options: title, orderable, searchable, className, render
     * @return array                Column config
     * 
     * @example
     * dt_column('name', ['title' => 'Customer Name', 'orderable' => true]);
     * dt_column(0, ['title' => 'No', 'orderable' => false, 'searchable' => false]);
     */
    function dt_column($data, array $options = []): array
    {
        $column = ['data' => $data];
        
        if (isset($options['title'])) {
            $column['title'] = $options['title'];
        }
        
        if (isset($options['orderable'])) {
            $column['orderable'] = $options['orderable'];
        }
        
        if (isset($options['searchable'])) {
            $column['searchable'] = $options['searchable'];
        }
        
        if (isset($options['className'])) {
            $column['className'] = $options['className'];
        }
        
        if (isset($options['width'])) {
            $column['width'] = $options['width'];
        }
        
        if (isset($options['render'])) {
            $column['render'] = $options['render'];
        }
        
        if (isset($options['defaultContent'])) {
            $column['defaultContent'] = $options['defaultContent'];
        }
        
        return $column;
    }
}

if (!function_exists('dt_action_column')) {
    /**
     * Generate action column configuration with standardized buttons
     * 
     * @param array $actions    Array of action types: 'view', 'edit', 'delete', etc.
     * @param array $options    Additional options: callbacks, permissions
     * @return array            Column config for actions
     * 
     * @example
     * dt_action_column(['view', 'edit', 'delete'], [
     *     'callbacks' => [
     *         'view' => 'viewRecord',
     *         'edit' => 'editRecord',
     *         'delete' => 'deleteRecord'
     *     ]
     * ]);
     */
    function dt_action_column(array $actions = ['view', 'edit', 'delete'], array $options = []): array
    {
        $callbacks = $options['callbacks'] ?? [];
        $vertical = $options['vertical'] ?? false;
        
        $render = 'function(data, type, row) {';
        $render .= 'let html = \'<div class="' . ($vertical ? 'btn-group-vertical' : 'btn-group') . '" role="group">\';';
        
        foreach ($actions as $action) {
            $callback = $callbacks[$action] ?? '';
            
            switch ($action) {
                case 'view':
                    $render .= 'html += \'<button class="btn btn-info btn-sm" onclick="' . $callback . '(\' + data + \')" title="View"><i class="fas fa-eye"></i></button>\';';
                    break;
                case 'edit':
                    $render .= 'html += \'<button class="btn btn-warning btn-sm" onclick="' . $callback . '(\' + data + \')" title="Edit"><i class="fas fa-edit"></i></button>\';';
                    break;
                case 'delete':
                    $render .= 'html += \'<button class="btn btn-danger btn-sm" onclick="' . $callback . '(\' + data + \')" title="Delete"><i class="fas fa-trash"></i></button>\';';
                    break;
                case 'print':
                    $render .= 'html += \'<button class="btn btn-primary btn-sm" onclick="' . $callback . '(\' + data + \')" title="Print"><i class="fas fa-print"></i></button>\';';
                    break;
                default:
                    if (isset($callbacks[$action])) {
                        $render .= 'html += \'<button class="btn btn-secondary btn-sm" onclick="' . $callback . '(\' + data + \')" title="' . ucfirst($action) . '"><i class="fas fa-cog"></i></button>\';';
                    }
            }
        }
        
        $render .= 'html += \'</div>\';';
        $render .= 'return html;';
        $render .= '}';
        
        return [
            'data' => 'id',
            'title' => $options['title'] ?? 'Actions',
            'orderable' => false,
            'searchable' => false,
            'className' => 'text-center',
            'render' => $render
        ];
    }
}

if (!function_exists('dt_status_column')) {
    /**
     * Generate status column with badge formatting
     * 
     * @param string $dataField     Data field name for status
     * @param array  $statusMap     Status to badge class mapping
     * @return array                Column config
     * 
     * @example
     * dt_status_column('status', [
     *     'active' => 'bg-success',
     *     'inactive' => 'bg-secondary'
     * ]);
     */
    function dt_status_column(string $dataField, array $statusMap = []): array
    {
        $defaultMap = [
            'open' => 'bg-warning',
            'in_progress' => 'bg-info',
            'completed' => 'bg-success',
            'cancelled' => 'bg-danger',
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger'
        ];
        
        $map = array_merge($defaultMap, $statusMap);
        
        $mapJson = json_encode($map);
        
        $render = <<<JS
        function(data, type, row) {
            if (!data) return '-';
            let statusMap = {$mapJson};
            let normalizedStatus = data.toLowerCase().replace(/[\\s-]/g, '_');
            let badgeClass = statusMap[normalizedStatus] || 'bg-secondary';
            return '<span class="badge ' + badgeClass + '">' + data + '</span>';
        }
        JS;
        
        return [
            'data' => $dataField,
            'title' => 'Status',
            'className' => 'text-center',
            'render' => $render
        ];
    }
}

if (!function_exists('dt_date_column')) {
    /**
     * Generate date column with formatting
     * 
     * @param string $dataField     Data field name for date
     * @param string $format        Date format (php or js style)
     * @return array                Column config
     */
    function dt_date_column(string $dataField, string $format = 'd/m/Y'): array
    {
        return [
            'data' => $dataField,
            'className' => 'text-center',
            'render' => 'function(data, type, row) {
                if (!data) return "-";
                if (type === "sort" || type === "type") return data;
                return moment(data).format("' . $format . '");
            }'
        ];
    }
}

if (!function_exists('dt_number_column')) {
    /**
     * Generate number column with formatting
     * 
     * @param string $dataField     Data field name
     * @param array  $options       Options: decimals, prefix, suffix, thousands_sep
     * @return array                Column config
     */
    function dt_number_column(string $dataField, array $options = []): array
    {
        $decimals = $options['decimals'] ?? 0;
        $prefix = $options['prefix'] ?? '';
        $suffix = $options['suffix'] ?? '';
        $sep = $options['thousands_sep'] ?? ',';
        
        return [
            'data' => $dataField,
            'className' => 'text-end',
            'render' => 'function(data, type, row) {
                if (!data) return "' . $prefix . '0' . $suffix . '";
                if (type === "sort" || type === "type") return parseFloat(data);
                return "' . $prefix . '" + parseFloat(data).toLocaleString("en-US", {
                    minimumFractionDigits: ' . $decimals . ',
                    maximumFractionDigits: ' . $decimals . '
                }) + "' . $suffix . '";
            }'
        ];
    }
}

if (!function_exists('dt_link_column')) {
    /**
     * Generate clickable link column
     * 
     * @param string $dataField     Data field name
     * @param string $urlPattern    URL pattern with {id} placeholder
     * @param array  $options       Options: target, title
     * @return array                Column config
     */
    function dt_link_column(string $dataField, string $urlPattern, array $options = []): array
    {
        $target = $options['target'] ?? '_self';
        $urlPattern = str_replace('{id}', '\' + row.id + \'', $urlPattern);
        
        return [
            'data' => $dataField,
            'render' => 'function(data, type, row) {
                return \'<a href="' . $urlPattern . '" target="' . $target . '">\' + data + \'</a>\';
            }'
        ];
    }
}

if (!function_exists('dt_export_buttons')) {
    /**
     * Generate export buttons configuration
     * 
     * @param array $buttons    Button types: 'excel', 'pdf', 'print', 'csv'
     * @param array $options    Additional options
     * @return array            Buttons config
     */
    function dt_export_buttons(array $buttons = ['excel', 'pdf', 'print'], array $options = []): array
    {
        $title = $options['title'] ?? 'Export';
        $filename = $options['filename'] ?? 'export_' . date('Y-m-d');
        
        $buttonConfigs = [];
        foreach ($buttons as $button) {
            switch ($button) {
                case 'excel':
                    $buttonConfigs[] = [
                        'extend' => 'excelHtml5',
                        'text' => '<i class="fas fa-file-excel me-1"></i> Excel',
                        'className' => 'btn btn-success btn-sm',
                        'title' => $title,
                        'filename' => $filename
                    ];
                    break;
                case 'pdf':
                    $buttonConfigs[] = [
                        'extend' => 'pdfHtml5',
                        'text' => '<i class="fas fa-file-pdf me-1"></i> PDF',
                        'className' => 'btn btn-danger btn-sm',
                        'title' => $title,
                        'filename' => $filename
                    ];
                    break;
                case 'print':
                    $buttonConfigs[] = [
                        'extend' => 'print',
                        'text' => '<i class="fas fa-print me-1"></i> Print',
                        'className' => 'btn btn-primary btn-sm',
                        'title' => $title
                    ];
                    break;
                case 'csv':
                    $buttonConfigs[] = [
                        'extend' => 'csvHtml5',
                        'text' => '<i class="fas fa-file-csv me-1"></i> CSV',
                        'className' => 'btn btn-info btn-sm',
                        'filename' => $filename
                    ];
                    break;
            }
        }
        
        return $buttonConfigs;
    }
}

if (!function_exists('dt_render_helper')) {
    /**
     * Helper untuk generate custom render functions
     * 
     * @param string $type      Render type: 'badge', 'button', 'link', 'image', 'custom'
     * @param array  $config    Configuration for the render type
     * @return string           JavaScript render function as string
     */
    function dt_render_helper(string $type, array $config = []): string
    {
        switch ($type) {
            case 'badge':
                $map = json_encode($config['map'] ?? []);
                return "function(data) {
                    let map = {$map};
                    let badgeClass = map[data] || 'bg-secondary';
                    return '<span class=\"badge ' + badgeClass + '\">' + data + '</span>';
                }";
                
            case 'button':
                $btnClass = $config['class'] ?? 'btn-primary';
                $icon = $config['icon'] ?? '';
                $callback = $config['callback'] ?? 'handleClick';
                return "function(data, type, row) {
                    return '<button class=\"btn {$btnClass} btn-sm\" onclick=\"{$callback}(' + row.id + ')\">{$icon}' + data + '</button>';
                }";
                
            case 'link':
                $url = $config['url'] ?? '#';
                return "function(data, type, row) {
                    return '<a href=\"{$url}\">' + data + '</a>';
                }";
                
            case 'image':
                $width = $config['width'] ?? '50px';
                $height = $config['height'] ?? 'auto';
                return "function(data) {
                    if (!data) return '-';
                    return '<img src=\"' + data + '\" style=\"width: {$width}; height: {$height};\" />';
                }";
                
            case 'custom':
                return $config['function'] ?? 'function(data) { return data; }';
                
            default:
                return 'function(data) { return data; }';
        }
    }
}

if (!function_exists('dt_table_standard')) {
    /**
     * Generate standard DataTable HTML structure
     * 
     * Creates a consistent table HTML with OPTIMA standard classes:
     * - table: Base Bootstrap table class
     * - table-hover: Hover effect on rows
     * - table-sm: Compact spacing for better data density
     * - table-striped: Alternating row colors for readability
     * - Responsive wrapper
     * - 100% width for DataTables
     * 
     * @param string $id Table ID attribute
     * @param array $columns Array of column header names
     * @param array $options Optional configuration:
     *   - class: Additional table classes (default: '')
     *   - wrapper: Wrapper div class (default: 'table-responsive')
     *   - caption: Table caption (optional)
     *   - footer: Include tfoot (default: false)
     * 
     * @return string HTML table structure
     * 
     * @example
     * echo dt_table_standard('customersTable', [
     *     'Customer Name',
     *     'Email',
     *     'Phone',
     *     'Status',
     *     'Actions'
     * ]);
     * 
     * // With options
     * echo dt_table_standard('productsTable', ['SKU', 'Name', 'Price'], [
     *     'class' => 'table-bordered',
     *     'caption' => 'Product List'
     * ]);
     */
    function dt_table_standard(string $id, array $columns, array $options = []): string
    {
        $defaults = [
            'class' => '',
            'wrapper' => 'table-responsive',
            'caption' => '',
            'footer' => false
        ];
        
        $opt = array_merge($defaults, $options);
        
        // Standard OPTIMA table classes
        $tableClasses = trim('table table-hover table-sm table-striped ' . $opt['class']);
        
        // Start wrapper
        $html = '<div class="' . esc($opt['wrapper']) . '">';
        
        // Table start
        $html .= '<table id="' . esc($id) . '" class="' . esc($tableClasses) . '" style="width:100%">';
        
        // Caption (optional)
        if ($opt['caption']) {
            $html .= '<caption>' . esc($opt['caption']) . '</caption>';
        }
        
        // Table header
        $html .= '<thead>';
        $html .= '<tr>';
        foreach ($columns as $col) {
            $html .= '<th>' . esc($col) . '</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        
        // Table body (will be populated by DataTables)
        $html .= '<tbody></tbody>';
        
        // Table footer (optional, mirrors header)
        if ($opt['footer']) {
            $html .= '<tfoot>';
            $html .= '<tr>';
            foreach ($columns as $col) {
                $html .= '<th>' . esc($col) . '</th>';
            }
            $html .= '</tr>';
            $html .= '</tfoot>';
        }
        
        $html .= '</table>';
        $html .= '</div>';
        
        return $html;
    }
}
