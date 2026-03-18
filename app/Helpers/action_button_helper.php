<?php

/**
 * ============================================================================
 * ACTION BUTTON PERMISSION HELPERS
 * ============================================================================
 * Helper functions untuk render action buttons dengan permission checks
 * 
 * Best Practice:
 * - Navigation menu → HIDE jika tidak punya akses
 * - Action buttons → SHOW DISABLED dengan tooltip/alert
 * 
 * @package Optima
 * @author  IT Support Team
 * @date    March 7, 2026
 */

if (!function_exists('renderActionButton')) {
    /**
     * Render action button dengan automatic permission check
     * 
     * @param array $config Button configuration
     *   - permission: Permission key required (e.g., 'marketing.customer.edit')
     *   - label: Button label text
     *   - icon: Font Awesome icon class (e.g., 'fa-edit')
     *   - class: Additional CSS classes (default: 'btn-primary')
     *   - onclick: JavaScript function to call
     *   - url: URL to navigate (alternative to onclick)
     *   - showDisabled: Show disabled button if no permission (default: true)
     *   - deniedMessage: Message to show when access denied
     * @return string HTML button code
     */
    function renderActionButton(array $config): string
    {
        // Default values
        $defaults = [
            'permission' => '',
            'label' => 'Action',
            'icon' => '',
            'class' => 'btn-primary',
            'onclick' => '',
            'url' => '',
            'showDisabled' => true,
            'deniedMessage' => 'Anda tidak memiliki akses untuk fitur ini',
            'size' => '', // sm, lg, or empty
            'type' => 'button',
            'id' => '',
            'dataAttributes' => []
        ];
        
        $config = array_merge($defaults, $config);
        
        // Check permission
        $hasAccess = empty($config['permission']) || hasPermission($config['permission']);
        
        // If no access and should hide
        if (!$hasAccess && !$config['showDisabled']) {
            return '';
        }
        
        // Build button classes
        $classes = ['btn'];
        if (!empty($config['size'])) {
            $classes[] = 'btn-' . $config['size'];
        }
        
        if ($hasAccess) {
            $classes[] = $config['class'];
        } else {
            $classes[] = 'btn-secondary';
        }
        
        // Build data attributes
        $dataAttrs = '';
        if (!empty($config['dataAttributes'])) {
            foreach ($config['dataAttributes'] as $key => $value) {
                $dataAttrs .= ' data-' . $key . '="' . htmlspecialchars($value) . '"';
            }
        }
        
        // Build button
        $html = '<button';
        $html .= ' type="' . $config['type'] . '"';
        $html .= ' class="' . implode(' ', $classes) . '"';
        
        if (!empty($config['id'])) {
            $html .= ' id="' . $config['id'] . '"';
        }
        
        if ($hasAccess) {
            // Has permission - button active
            if (!empty($config['onclick'])) {
                $html .= ' onclick="' . htmlspecialchars($config['onclick']) . '"';
            } elseif (!empty($config['url'])) {
                $html .= ' onclick="window.location.href=\'' . base_url($config['url']) . '\'"';
            }
        } else {
            // No permission - button disabled
            $html .= ' disabled';
            $html .= ' style="cursor: not-allowed;"';
            $html .= ' data-bs-toggle="tooltip"';
            $html .= ' title="' . htmlspecialchars($config['deniedMessage']) . '"';
            $deniedMsg = isset($config['deniedMessage']) ? $config['deniedMessage'] : 'Akses Ditolak';
            $deniedMsgJson = json_encode($deniedMsg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            $titleJson = json_encode('Akses Ditolak', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            $fallbackAlert = json_encode((string)$deniedMsg, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
            $html .= ' onclick="(window.OptimaNotify ? OptimaNotify.error(' . $deniedMsgJson . ', ' . $titleJson . ') : alert(' . $fallbackAlert . '))"';
        }
        
        $html .= $dataAttrs;
        $html .= '>';
        
        // Icon
        if (!empty($config['icon'])) {
            $html .= '<i class="' . $config['icon'] . '"></i> ';
        }
        
        // Label
        $html .= $config['label'];
        
        $html .= '</button>';
        
        return $html;
    }
}

if (!function_exists('renderCreateButton')) {
    /**
     * Render Create/Add button dengan permission check
     * 
     * @param string $permission Permission key (e.g., 'warehouse.inventory_unit.create')
     * @param string $onclick JavaScript function
     * @param array $options Additional options
     * @return string
     */
    function renderCreateButton(string $permission, string $onclick = '', array $options = []): string
    {
        return renderActionButton(array_merge([
            'permission' => $permission,
            'label' => $options['label'] ?? 'Tambah',
            'icon' => 'fas fa-plus',
            'class' => 'btn-success',
            'onclick' => $onclick,
            'deniedMessage' => 'Anda tidak memiliki permission untuk menambah data'
        ], $options));
    }
}

if (!function_exists('renderEditButton')) {
    /**
     * Render Edit button dengan permission check
     * 
     * @param string $permission Permission key (e.g., 'marketing.customer.edit')
     * @param string $onclick JavaScript function
     * @param array $options Additional options
     * @return string
     */
    function renderEditButton(string $permission, string $onclick = '', array $options = []): string
    {
        return renderActionButton(array_merge([
            'permission' => $permission,
            'label' => $options['label'] ?? 'Edit',
            'icon' => 'fas fa-edit',
            'class' => 'btn-primary',
            'onclick' => $onclick,
            'deniedMessage' => 'Anda tidak memiliki permission untuk edit data ini'
        ], $options));
    }
}

if (!function_exists('renderDeleteButton')) {
    /**
     * Render Delete button dengan permission check
     * 
     * @param string $permission Permission key
     * @param string $onclick JavaScript function
     * @param array $options Additional options
     * @return string
     */
    function renderDeleteButton(string $permission, string $onclick = '', array $options = []): string
    {
        return renderActionButton(array_merge([
            'permission' => $permission,
            'label' => $options['label'] ?? 'Delete',
            'icon' => 'fas fa-trash',
            'class' => 'btn-danger',
            'onclick' => $onclick,
            'deniedMessage' => 'Anda tidak memiliki permission untuk hapus data ini'
        ], $options));
    }
}

if (!function_exists('renderApproveButton')) {
    /**
     * Render Approve button dengan permission check
     * 
     * @param string $permission Permission key
     * @param string $onclick JavaScript function
     * @param array $options Additional options
     * @return string
     */
    function renderApproveButton(string $permission, string $onclick = '', array $options = []): string
    {
        return renderActionButton(array_merge([
            'permission' => $permission,
            'label' => $options['label'] ?? 'Approve',
            'icon' => 'fas fa-check-circle',
            'class' => 'btn-success',
            'onclick' => $onclick,
            'deniedMessage' => 'Hanya yang memiliki permission dapat approve data ini'
        ], $options));
    }
}

if (!function_exists('renderExportButton')) {
    /**
     * Render Export/Download button dengan permission check
     * 
     * @param string $permission Permission key
     * @param string $onclick JavaScript function
     * @param array $options Additional options
     * @return string
     */
    function renderExportButton(string $permission, string $onclick = '', array $options = []): string
    {
        return renderActionButton(array_merge([
            'permission' => $permission,
            'label' => $options['label'] ?? 'Export PDF',
            'icon' => 'fas fa-file-pdf',
            'class' => 'btn-success',
            'onclick' => $onclick,
            'showDisabled' => false, // Hide export button if no permission
            'deniedMessage' => 'Anda tidak memiliki permission untuk export data'
        ], $options));
    }
}

if (!function_exists('renderPrintButton')) {
    /**
     * Render Print button dengan permission check
     * 
     * @param string $permission Permission key
     * @param string $onclick JavaScript function
     * @param array $options Additional options
     * @return string
     */
    function renderPrintButton(string $permission, string $onclick = '', array $options = []): string
    {
        return renderActionButton(array_merge([
            'permission' => $permission,
            'label' => $options['label'] ?? 'Print',
            'icon' => 'fas fa-print',
            'class' => 'btn-info',
            'onclick' => $onclick,
            'showDisabled' => $options['showDisabled'] ?? true, // Show disabled (different from export)
            'deniedMessage' => 'Anda tidak memiliki permission untuk print dokumen'
        ], $options));
    }
}

if (!function_exists('canPerformAction')) {
    /**
     * Check if user can perform specific action
     * Returns array with permission status and message
     * 
     * @param string $module Module name
     * @param string $page Page name
     * @param string $action Action (edit, delete, approve, export, etc.)
     * @return array ['allowed' => bool, 'message' => string]
     */
    function canPerformAction(string $module, string $page, string $action): array
    {
        $permissionKey = "{$module}.{$page}.{$action}";
        $allowed = hasPermission($permissionKey);
        
        $messages = [
            'edit' => 'Anda tidak memiliki permission untuk mengedit data ini',
            'delete' => 'Anda tidak memiliki permission untuk menghapus data ini',
            'approve' => 'Anda tidak memiliki permission untuk approval',
            'reject' => 'Anda tidak memiliki permission untuk reject',
            'export' => 'Anda tidak memiliki permission untuk export data',
            'print' => 'Anda tidak memiliki permission untuk print dokumen',
            'create' => 'Anda tidak memiliki permission untuk membuat data baru',
            'view' => 'Anda tidak memiliki permission untuk melihat data ini',
        ];
        
        return [
            'allowed' => $allowed,
            'message' => $allowed ? 'Akses diizinkan' : ($messages[$action] ?? 'Akses ditolak'),
            'permission_key' => $permissionKey
        ];
    }
}

if (!function_exists('renderActionDropdown')) {
    /**
     * Render action dropdown menu dengan permission filtering
     * 
     * @param array $actions Array of action configs
     *   Each action: ['label', 'icon', 'permission', 'onclick' or 'url']
     * @param array $options Dropdown options
     * @return string
     */
    function renderActionDropdown(array $actions, array $options = []): string
    {
        $defaults = [
            'buttonLabel' => 'Actions',
            'buttonIcon' => 'fas fa-cog',
            'buttonClass' => 'btn-primary',
            'size' => 'sm'
        ];
        
        $options = array_merge($defaults, $options);
        
        // Filter actions based on permissions
        $allowedActions = [];
        foreach ($actions as $action) {
            $permission = $action['permission'] ?? '';
            if (empty($permission) || hasPermission($permission)) {
                $allowedActions[] = $action;
            }
        }
        
        // If no allowed actions, return empty
        if (empty($allowedActions)) {
            return '';
        }
        
        $html = '<div class="dropdown d-inline-block">';
        $html .= '<button class="btn btn-' . $options['size'] . ' ' . $options['buttonClass'] . ' dropdown-toggle" type="button" data-bs-toggle="dropdown">';
        if (!empty($options['buttonIcon'])) {
            $html .= '<i class="' . $options['buttonIcon'] . '"></i> ';
        }
        $html .= $options['buttonLabel'];
        $html .= '</button>';
        $html .= '<ul class="dropdown-menu">';
        
        foreach ($allowedActions as $action) {
            if (isset($action['divider']) && $action['divider']) {
                $html .= '<li><hr class="dropdown-divider"></li>';
                continue;
            }
            
            $html .= '<li>';
            $html .= '<a class="dropdown-item" href="' . ($action['url'] ?? '#') . '"';
            if (!empty($action['onclick'])) {
                $html .= ' onclick="' . htmlspecialchars($action['onclick']) . '; return false;"';
            }
            $html .= '>';
            if (!empty($action['icon'])) {
                $html .= '<i class="' . $action['icon'] . '"></i> ';
            }
            $html .= $action['label'];
            $html .= '</a>';
            $html .= '</li>';
        }
        
        $html .= '</ul>';
        $html .= '</div>';
        
        return $html;
    }
}
