<?php

/**
 * ====================================================================
 * OPTIMA UI/UX Helper Functions
 * ====================================================================
 * 
 * Centralized UI component helper untuk konsistensi design system
 * Dibuat: <?= date('Y-m-d') ?>
 * 
 * @package    OPTIMA
 * @category   Helpers
 * @author     OPTIMA Dev Team
 */

if (!function_exists('ui_button')) {
    /**
     * Generate standardized button HTML
     * 
     * @param string $type      Button type: 'add', 'edit', 'delete', 'save', 'cancel', 'view', 'export', 'filter', 'print', 'custom'
     * @param string $text      Button text (optional, uses default if empty)
     * @param array  $options   Additional options: id, class, onclick, href, icon, size, disabled, attributes
     * @return string           HTML button element
     * 
     * @example
     * echo ui_button('add', 'Add Customer');
     * echo ui_button('edit', '', ['id' => 'btn-edit', 'onclick' => 'editRecord(123)']);
     * echo ui_button('custom', 'Submit', ['class' => 'btn-info', 'icon' => 'fas fa-paper-plane']);
     */
    function ui_button(string $type, string $text = '', array $options = []): string
    {
        // Define standard button configurations
        $configs = [
            'add' => [
                'class' => 'btn-primary',
                'icon' => 'fas fa-plus',
                'text' => 'Add'
            ],
            'edit' => [
                'class' => 'btn-warning',
                'icon' => 'fas fa-edit',
                'text' => 'Edit'
            ],
            'delete' => [
                'class' => 'btn-danger',
                'icon' => 'fas fa-trash',
                'text' => 'Delete'
            ],
            'save' => [
                'class' => 'btn-success',
                'icon' => 'fas fa-save',
                'text' => 'Save'
            ],
            'cancel' => [
                'class' => 'btn-secondary',
                'icon' => 'fas fa-times',
                'text' => 'Cancel'
            ],
            'view' => [
                'class' => 'btn-info',
                'icon' => 'fas fa-eye',
                'text' => 'View'
            ],
            'export' => [
                'class' => 'btn-success',
                'icon' => 'fas fa-file-export',
                'text' => 'Export'
            ],
            'filter' => [
                'class' => 'btn-outline-secondary',
                'icon' => 'fas fa-filter',
                'text' => 'Filter'
            ],
            'print' => [
                'class' => 'btn-primary',
                'icon' => 'fas fa-print',
                'text' => 'Print'
            ],
            'refresh' => [
                'class' => 'btn-outline-primary',
                'icon' => 'fas fa-sync-alt',
                'text' => 'Refresh'
            ],
            'search' => [
                'class' => 'btn-outline-primary',
                'icon' => 'fas fa-search',
                'text' => 'Search'
            ],
            'back' => [
                'class' => 'btn-secondary',
                'icon' => 'fas fa-arrow-left',
                'text' => 'Back'
            ],
            'submit' => [
                'class' => 'btn-primary',
                'icon' => 'fas fa-check',
                'text' => 'Submit'
            ],
            'approve' => [
                'class' => 'btn-success',
                'icon' => 'fas fa-check-circle',
                'text' => 'Approve'
            ],
            'reject' => [
                'class' => 'btn-danger',
                'icon' => 'fas fa-times-circle',
                'text' => 'Reject'
            ]
        ];

        // Get base config or use custom
        $config = $configs[$type] ?? ['class' => 'btn-secondary', 'icon' => '', 'text' => $text ?: 'Button'];

        // Override with options
        // Handle 'color' option (e.g., 'outline-success' -> 'btn-outline-success')
        if (isset($options['color'])) {
            $btnClass = strpos($options['color'], 'btn-') === 0 ? $options['color'] : 'btn-' . $options['color'];
        } else {
            $btnClass = $options['class'] ?? $config['class'];
        }
        
        $icon = $options['icon'] ?? $config['icon'];
        $btnText = $text ?: ($options['text'] ?? $config['text']);
        $size = $options['size'] ?? 'btn-sm';
        $disabled = isset($options['disabled']) && $options['disabled'] ? 'disabled' : '';

        // Build class string
        $classString = "btn {$btnClass} {$size} {$disabled}";
        
        // Add custom classes (for margins, etc)
        if (isset($options['add_class'])) {
            $classString .= " {$options['add_class']}";
        }
        
        // Support legacy 'class' for additional classes (e.g., 'me-2')
        // Only if it doesn't look like a button color class
        if (isset($options['class']) && !isset($options['color']) && strpos($options['class'], 'btn-') !== 0) {
            $classString .= " {$options['class']}";
        }

        // Build attributes
        $attributes = '';
        
        // Reserved keys that are not HTML attributes
        $reservedKeys = ['icon', 'text', 'size', 'disabled', 'class', 'color', 'add_class', 'type', 'href', 'data', 'attributes'];
        
        // Process all options as potential HTML attributes
        foreach ($options as $key => $value) {
            // Skip reserved keys
            if (in_array($key, $reservedKeys)) {
                continue;
            }
            
            // Handle data-* and aria-* attributes directly (e.g., 'data-bs-dismiss' => 'modal')
            if (strpos($key, 'data-') === 0 || strpos($key, 'aria-') === 0) {
                $attributes .= " {$key}=\"" . esc($value) . "\"";
            }
            // Handle standard HTML attributes (id, onclick, title, target, rel, etc.)
            elseif (in_array($key, ['id', 'onclick', 'title', 'name', 'value', 'placeholder', 'tabindex', 'role', 'target', 'rel'])) {
                $attributes .= " {$key}=\"" . esc($value) . "\"";
            }
        }
        
        // Legacy support: 'data' array format
        if (isset($options['data'])) {
            foreach ($options['data'] as $key => $value) {
                $attributes .= " data-{$key}=\"" . esc($value) . "\"";
            }
        }
        
        // Legacy support: raw 'attributes' string
        if (isset($options['attributes'])) {
            $attributes .= " {$options['attributes']}";
        }

        // Build icon HTML
        $iconHtml = $icon ? "<i class=\"{$icon} me-1\"></i>" : '';

        // Return button or link
        if (isset($options['href'])) {
            return "<a href=\"{$options['href']}\" class=\"{$classString}\"{$attributes}>{$iconHtml}{$btnText}</a>";
        } else {
            $btnType = $options['type'] ?? 'button';
            return "<button type=\"{$btnType}\" class=\"{$classString}\"{$attributes}>{$iconHtml}{$btnText}</button>";
        }
    }
}

if (!function_exists('ui_badge')) {
    /**
     * Generate standardized badge HTML
     * 
     * @param string $type      Badge type or status name
     * @param string $text      Badge text (optional, uses type if empty)
     * @param array  $options   Additional options: class, icon, pill
     * @return string           HTML badge element
     * 
     * @example
     * echo ui_badge('success', 'Active');
     * echo ui_badge('pending');
     * echo ui_badge('custom', 'VIP', ['class' => 'bg-purple', 'icon' => 'fas fa-star']);
     */
    function ui_badge(string $type, string $text = '', array $options = []): string
    {
        // Define status badge configurations
        $statusConfigs = [
            // Work Order Status
            'open' => ['class' => 'bg-warning', 'text' => 'Open', 'icon' => ''],
            'in_progress' => ['class' => 'bg-info', 'text' => 'In Progress', 'icon' => ''],
            'on_hold' => ['class' => 'bg-secondary', 'text' => 'On Hold', 'icon' => ''],
            'waiting_parts' => ['class' => 'bg-warning', 'text' => 'Waiting Parts', 'icon' => 'fas fa-clock'],
            'completed' => ['class' => 'bg-success', 'text' => 'Completed', 'icon' => 'fas fa-check'],
            'cancelled' => ['class' => 'bg-danger', 'text' => 'Cancelled', 'icon' => 'fas fa-times'],
            'closed' => ['class' => 'bg-dark', 'text' => 'Closed', 'icon' => ''],
            
            // Quotation Status
            'draft' => ['class' => 'bg-secondary', 'text' => 'Draft', 'icon' => ''],
            'sent' => ['class' => 'bg-warning', 'text' => 'Sent', 'icon' => ''],
            'pending' => ['class' => 'bg-warning', 'text' => 'Pending', 'icon' => ''],
            'accepted' => ['class' => 'bg-success', 'text' => 'Accepted', 'icon' => 'fas fa-check'],
            'approved' => ['class' => 'bg-success', 'text' => 'Approved', 'icon' => 'fas fa-check-circle'],
            'rejected' => ['class' => 'bg-danger', 'text' => 'Rejected', 'icon' => 'fas fa-times-circle'],
            
            // SPK/DI Status
            'submitted' => ['class' => 'bg-secondary', 'text' => 'Submitted', 'icon' => ''],
            'ready' => ['class' => 'bg-success', 'text' => 'Ready', 'icon' => 'fas fa-check'],
            'delivered' => ['class' => 'bg-primary', 'text' => 'Delivered', 'icon' => 'fas fa-truck'],
            'dispatched' => ['class' => 'bg-info', 'text' => 'Dispatched', 'icon' => ''],
            'arrived' => ['class' => 'bg-success', 'text' => 'Arrived', 'icon' => ''],
            
            // Priority Levels
            'low' => ['class' => 'bg-secondary', 'text' => 'Low', 'icon' => ''],
            'medium' => ['class' => 'bg-warning', 'text' => 'Medium', 'icon' => ''],
            'high' => ['class' => 'bg-danger', 'text' => 'High', 'icon' => 'fas fa-exclamation'],
            'urgent' => ['class' => 'bg-danger', 'text' => 'Urgent', 'icon' => 'fas fa-exclamation-triangle'],
            
            // General Status
            'active' => ['class' => 'bg-success', 'text' => 'Active', 'icon' => ''],
            'inactive' => ['class' => 'bg-secondary', 'text' => 'Inactive', 'icon' => ''],
            'available' => ['class' => 'bg-success', 'text' => 'Available', 'icon' => ''],
            'unavailable' => ['class' => 'bg-danger', 'text' => 'Unavailable', 'icon' => ''],
            
            // Item Types
            'sparepart' => ['class' => 'bg-primary', 'text' => 'Sparepart', 'icon' => ''],
            'tool' => ['class' => 'bg-secondary', 'text' => 'Tool', 'icon' => ''],
            'warehouse' => ['class' => 'bg-success', 'text' => 'Warehouse', 'icon' => ''],
            'non_warehouse' => ['class' => 'bg-warning text-dark', 'text' => 'Non-Warehouse', 'icon' => ''],
            
            // Generic colors
            'primary' => ['class' => 'bg-primary', 'text' => '', 'icon' => ''],
            'secondary' => ['class' => 'bg-secondary', 'text' => '', 'icon' => ''],
            'success' => ['class' => 'bg-success', 'text' => '', 'icon' => ''],
            'danger' => ['class' => 'bg-danger', 'text' => '', 'icon' => ''],
            'warning' => ['class' => 'bg-warning text-dark', 'text' => '', 'icon' => ''],
            'info' => ['class' => 'bg-info', 'text' => '', 'icon' => ''],
            'light' => ['class' => 'bg-light text-dark', 'text' => '', 'icon' => ''],
            'dark' => ['class' => 'bg-dark', 'text' => '', 'icon' => '']
        ];

        // Normalize type to lowercase with underscores
        $normalizedType = strtolower(str_replace([' ', '-'], '_', $type));
        
        // Get config or use provided class
        $config = $statusConfigs[$normalizedType] ?? null;
        
        if ($config) {
            $badgeClass = $options['class'] ?? $config['class'];
            $badgeText = $text ?: $config['text'];
            $icon = $options['icon'] ?? $config['icon'];
        } else {
            // Custom badge
            $badgeClass = $options['class'] ?? 'bg-secondary';
            $badgeText = $text ?: $type;
            $icon = $options['icon'] ?? '';
        }

        // Add pill style if requested
        $pill = isset($options['pill']) && $options['pill'] ? 'rounded-pill' : '';

        // Build icon HTML
        $iconHtml = $icon ? "<i class=\"{$icon} me-1\"></i>" : '';

        // Build additional attributes
        $attributes = '';
        if (isset($options['id'])) {
            $attributes .= " id=\"{$options['id']}\"";
        }
        if (isset($options['title'])) {
            $attributes .= " title=\"{$options['title']}\"";
        }

        return "<span class=\"badge {$badgeClass} {$pill}\"{$attributes}>{$iconHtml}{$badgeText}</span>";
    }
}

if (!function_exists('ui_alert')) {
    /**
     * Generate standardized alert HTML
     * 
     * @param string $type      Alert type: 'success', 'danger', 'warning', 'info', 'primary', 'secondary'
     * @param string $message   Alert message
     * @param array  $options   Additional options: dismissible, icon, title
     * @return string           HTML alert element
     * 
     * @example
     * echo ui_alert('success', 'Data berhasil disimpan!');
     * echo ui_alert('danger', 'Terjadi kesalahan!', ['dismissible' => true]);
     */
    function ui_alert(string $type, string $message, array $options = []): string
    {
        $icons = [
            'success' => 'fas fa-check-circle',
            'danger' => 'fas fa-exclamation-circle',
            'warning' => 'fas fa-exclamation-triangle',
            'info' => 'fas fa-info-circle',
            'primary' => 'fas fa-info-circle',
            'secondary' => 'fas fa-info-circle'
        ];

        $icon = $options['icon'] ?? $icons[$type] ?? '';
        $dismissible = isset($options['dismissible']) && $options['dismissible'];
        $title = $options['title'] ?? '';

        $dismissClass = $dismissible ? 'alert-dismissible fade show' : '';
        $iconHtml = $icon ? "<i class=\"{$icon} me-2\"></i>" : '';
        $titleHtml = $title ? "<strong>{$title}</strong><br>" : '';
        $closeBtn = $dismissible ? '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' : '';

        return <<<HTML
        <div class="alert alert-{$type} {$dismissClass}" role="alert">
            {$iconHtml}{$titleHtml}{$message}
            {$closeBtn}
        </div>
        HTML;
    }
}

if (!function_exists('ui_status_badge')) {
    /**
     * Generate status badge with automatic color based on status type
     * Shortcut for ui_badge with status auto-detection
     * 
     * @param string $status    Status value
     * @param string $module    Module context: 'wo', 'quotation', 'spk', 'po', etc.
     * @return string           HTML badge element
     * 
     * @example
     * echo ui_status_badge('IN_PROGRESS', 'wo');
     * echo ui_status_badge('SENT', 'quotation');
     */
    function ui_status_badge(string $status, string $module = ''): string
    {
        // Normalize status
        $normalizedStatus = strtolower(str_replace([' ', '-'], '_', $status));
        
        // Try to display with ui_badge (will auto-detect color)
        return ui_badge($normalizedStatus);
    }
}

if (!function_exists('ui_priority_badge')) {
    /**
     * Generate priority badge
     * 
     * @param string $priority  Priority level: 'low', 'medium', 'high', 'urgent'
     * @param array  $options   Additional options
     * @return string           HTML badge element
     * 
     * @example
     * echo ui_priority_badge('high');
     * echo ui_priority_badge('urgent', ['pill' => true]);
     */
    function ui_priority_badge(string $priority, array $options = []): string
    {
        return ui_badge(strtolower($priority), '', $options);
    }
}

if (!function_exists('ui_action_buttons')) {
    /**
     * Generate group of action buttons (view, edit, delete)
     * 
     * @param array $actions    Array of actions with configs
     * @param int   $recordId   Record ID to pass to actions
     * @param array $options    Additional options: size, vertical
     * @return string           HTML button group
     * 
     * @example
     * echo ui_action_buttons([
     *     'view' => ['onclick' => 'viewRecord'],
     *     'edit' => ['onclick' => 'editRecord'],
     *     'delete' => ['onclick' => 'deleteRecord']
     * ], 123);
     */
    function ui_action_buttons(array $actions, int $recordId = 0, array $options = []): string
    {
        $size = $options['size'] ?? 'btn-sm';
        $vertical = isset($options['vertical']) && $options['vertical'] ? 'btn-group-vertical' : 'btn-group';
        
        $buttons = '';
        foreach ($actions as $type => $config) {
            $btnOptions = array_merge($config, ['size' => $size]);
            
            // Add record ID to function calls if present
            if (isset($btnOptions['onclick']) && $recordId > 0) {
                $onclick = $btnOptions['onclick'];
                // Replace {id} placeholder or append if not present
                if (strpos($onclick, '{id}') !== false) {
                    $btnOptions['onclick'] = str_replace('{id}', $recordId, $onclick);
                } elseif (strpos($onclick, '(') !== false) {
                    $btnOptions['onclick'] = str_replace('()', "({$recordId})", $onclick);
                } else {
                    $btnOptions['onclick'] = "{$onclick}({$recordId})";
                }
            }
            
            $buttons .= ui_button($type, '', $btnOptions);
        }
        
        return "<div class=\"{$vertical}\" role=\"group\">{$buttons}</div>";
    }
}

if (!function_exists('ui_empty_state')) {
    /**
     * Generate empty state HTML
     * 
     * @param string $message   Empty state message
     * @param array  $options   Additional options: icon, button
     * @return string           HTML empty state element
     */
    function ui_empty_state(string $message = 'No data available', array $options = []): string
    {
        $icon = $options['icon'] ?? 'fas fa-inbox';
        $button = $options['button'] ?? '';
        
        $buttonHtml = '';
        if ($button && is_array($button)) {
            $buttonHtml = '<div class="mt-3">' . ui_button($button['type'], $button['text'], $button['options'] ?? []) . '</div>';
        }
        
        return <<<HTML
        <div class="text-center py-5 text-muted">
            <i class="{$icon} fa-3x mb-3" style="opacity: 0.3;"></i>
            <p class="mb-0">{$message}</p>
            {$buttonHtml}
        </div>
        HTML;
    }
}

if (!function_exists('mask_contract_number')) {
    /**
     * Mask contract/PO number for Service view (show partial with asterisks)
     *
     * @param string|null $noKontrak Contract or PO number
     * @param int        $visible   Number of chars to keep at start/end (default 2)
     * @return string               Masked string
     */
    function mask_contract_number(?string $noKontrak, int $visible = 2): string
    {
        if (empty($noKontrak)) {
            return '-';
        }
        $len = strlen($noKontrak);
        if ($len <= 6) {
            return substr($noKontrak, 0, 2) . str_repeat('*', max(0, $len - 2));
        }
        $parts = preg_split('/[\/\-]/', $noKontrak);
        $masked = [];
        foreach ($parts as $i => $p) {
            $pLen = strlen($p);
            if ($pLen <= $visible * 2) {
                $masked[] = str_repeat('*', $pLen);
            } else {
                $masked[] = substr($p, 0, $visible) . str_repeat('*', $pLen - $visible * 2) . substr($p, -$visible);
            }
        }
        return implode('/', $masked);
    }
}

if (!function_exists('ui_loading')) {
    /**
     * Generate loading spinner HTML
     * 
     * @param string $message   Loading message
     * @param string $size      Size: 'sm', 'md', 'lg'
     * @return string           HTML loading element
     */
    function ui_loading(string $message = 'Loading...', string $size = 'md'): string
    {
        $sizeClass = [
            'sm' => 'spinner-border-sm',
            'md' => '',
            'lg' => 'spinner-border-lg'
        ][$size] ?? '';
        
        return <<<HTML
        <div class="text-center py-4">
            <div class="spinner-border {$sizeClass} text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="text-muted mt-2 mb-0">{$message}</p>
        </div>
        HTML;
    }
}
