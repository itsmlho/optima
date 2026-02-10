<?php

/**
 * OPTIMA Form Field Helpers
 * 
 * Provides consistent, standardized form field generation with built-in
 * validation support, required indicators, and Bootstrap 5 styling.
 * 
 * @package OPTIMA
 * @version 1.0.0
 * @created 2026-02-09
 */

if (!function_exists('form_field')) {
    /**
     * Generate a complete form field with label, input, and optional help text
     * 
     * @param string $type Input type (text, email, tel, number, date, password, etc.)
     * @param string $name Field name attribute
     * @param string $label Field label text
     * @param array $options Configuration options:
     *   - value: Field value (default: old($name) or '')
     *   - placeholder: Placeholder text
     *   - required: Whether field is required (default: false)
     *   - readonly: Make field readonly (default: false)
     *   - disabled: Disable field (default: false)
     *   - helpText: Help text below field
     *   - size: Field size 'normal' or 'sm' (default: 'normal')
     *   - wrapper: Wrapper class (default: 'mb-3')
     *   - labelClass: Additional label classes
     *   - inputClass: Additional input classes
     *   - attributes: Array of additional HTML attributes
     *   - min: Min value (for number/date)
     *   - max: Max value (for number/date)
     *   - step: Step value (for number)
     *   - pattern: Regex pattern for validation
     *   - maxlength: Maximum character length
     * 
     * @return string HTML form field
     */
    function form_field($type, $name, $label, $options = [])
    {
        $defaults = [
            'value' => old($name) ?? '',
            'placeholder' => '',
            'required' => false,
            'readonly' => false,
            'disabled' => false,
            'helpText' => '',
            'size' => 'normal',
            'wrapper' => 'mb-3',
            'labelClass' => '',
            'inputClass' => '',
            'attributes' => [],
            'min' => null,
            'max' => null,
            'step' => null,
            'pattern' => null,
            'maxlength' => null
        ];
        
        $opt = array_merge($defaults, $options);
        
        // Build attribute strings
        $required = $opt['required'] ? 'required' : '';
        $readonly = $opt['readonly'] ? 'readonly' : '';
        $disabled = $opt['disabled'] ? 'disabled' : '';
        $sizeClass = $opt['size'] === 'sm' ? 'form-control-sm' : '';
        $requiredIndicator = $opt['required'] ? ' <span class="text-danger">*</span>' : '';
        
        // Start wrapper
        $html = '<div class="' . esc($opt['wrapper']) . '">';
        
        // Label
        $labelClasses = 'form-label ' . $opt['labelClass'];
        $html .= '<label for="' . esc($name) . '" class="' . trim($labelClasses) . '">';
        $html .= esc($label) . $requiredIndicator;
        $html .= '</label>';
        
        // Input field
        $inputClasses = 'form-control ' . $sizeClass . ' ' . $opt['inputClass'];
        $html .= '<input type="' . esc($type) . '" ';
        $html .= 'class="' . trim($inputClasses) . '" ';
        $html .= 'name="' . esc($name) . '" ';
        $html .= 'id="' . esc($name) . '" ';
        $html .= 'value="' . esc($opt['value']) . '" ';
        
        if ($opt['placeholder']) {
            $html .= 'placeholder="' . esc($opt['placeholder']) . '" ';
        }
        
        // Optional attributes
        if ($opt['min'] !== null) $html .= 'min="' . esc($opt['min']) . '" ';
        if ($opt['max'] !== null) $html .= 'max="' . esc($opt['max']) . '" ';
        if ($opt['step'] !== null) $html .= 'step="' . esc($opt['step']) . '" ';
        if ($opt['pattern']) $html .= 'pattern="' . esc($opt['pattern']) . '" ';
        if ($opt['maxlength']) $html .= 'maxlength="' . esc($opt['maxlength']) . '" ';
        
        $html .= $required . ' ' . $readonly . ' ' . $disabled;
        
        // Additional custom attributes
        foreach ($opt['attributes'] as $key => $val) {
            $html .= ' ' . esc($key) . '="' . esc($val) . '"';
        }
        
        $html .= '>';
        
        // Help text
        if ($opt['helpText']) {
            $html .= '<div class="form-text">' . esc($opt['helpText']) . '</div>';
        }
        
        // Validation feedback placeholders
        $html .= '<div class="invalid-feedback"></div>';
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_select_field')) {
    /**
     * Generate a select dropdown field with label
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param array $options_array Associative array of value => label pairs
     * @param array $options Configuration options (same as form_field plus):
     *   - selected: Currently selected value
     *   - multiple: Allow multiple selections
     *   - placeholder: Placeholder option text (shown as disabled option)
     * 
     * @return string HTML select field
     */
    function form_select_field($name, $label, $options_array, $options = [])
    {
        $defaults = [
            'selected' => old($name) ?? '',
            'required' => false,
            'disabled' => false,
            'helpText' => '',
            'size' => 'normal',
            'wrapper' => 'mb-3',
            'labelClass' => '',
            'inputClass' => '',
            'placeholder' => '- Select -',
            'multiple' => false,
            'attributes' => []
        ];
        
        $opt = array_merge($defaults, $options);
        
        $required = $opt['required'] ? 'required' : '';
        $disabled = $opt['disabled'] ? 'disabled' : '';
        $multiple = $opt['multiple'] ? 'multiple' : '';
        $sizeClass = $opt['size'] === 'sm' ? 'form-select-sm' : '';
        $requiredIndicator = $opt['required'] ? ' <span class="text-danger">*</span>' : '';
        
        // Start wrapper
        $html = '<div class="' . esc($opt['wrapper']) . '">';
        
        // Label
        $labelClasses = 'form-label ' . $opt['labelClass'];
        $html .= '<label for="' . esc($name) . '" class="' . trim($labelClasses) . '">';
        $html .= esc($label) . $requiredIndicator;
        $html .= '</label>';
        
        // Select field
        $selectClasses = 'form-select ' . $sizeClass . ' ' . $opt['inputClass'];
        $html .= '<select class="' . trim($selectClasses) . '" ';
        $html .= 'name="' . esc($name) . ($opt['multiple'] ? '[]' : '') . '" ';
        $html .= 'id="' . esc($name) . '" ';
        $html .= $required . ' ' . $disabled . ' ' . $multiple;
        
        // Additional attributes
        foreach ($opt['attributes'] as $key => $val) {
            $html .= ' ' . esc($key) . '="' . esc($val) . '"';
        }
        
        $html .= '>';
        
        // Placeholder option
        if ($opt['placeholder']) {
            $html .= '<option value="" disabled' . (empty($opt['selected']) ? ' selected' : '') . '>';
            $html .= esc($opt['placeholder']);
            $html .= '</option>';
        }
        
        // Options
        foreach ($options_array as $val => $text) {
            $selected = '';
            if (is_array($opt['selected'])) {
                $selected = in_array($val, $opt['selected']) ? ' selected' : '';
            } else {
                $selected = ($val == $opt['selected']) ? ' selected' : '';
            }
            $html .= '<option value="' . esc($val) . '"' . $selected . '>';
            $html .= esc($text);
            $html .= '</option>';
        }
        
        $html .= '</select>';
        
        // Help text
        if ($opt['helpText']) {
            $html .= '<div class="form-text">' . esc($opt['helpText']) . '</div>';
        }
        
        // Validation feedback
        $html .= '<div class="invalid-feedback"></div>';
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_textarea_field')) {
    /**
     * Generate a textarea field with label
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param array $options Configuration options (same as form_field plus):
     *   - rows: Number of rows (default: 3)
     *   - cols: Number of columns (optional)
     * 
     * @return string HTML textarea field
     */
    function form_textarea_field($name, $label, $options = [])
    {
        $defaults = [
            'value' => old($name) ?? '',
            'placeholder' => '',
            'required' => false,
            'readonly' => false,
            'disabled' => false,
            'helpText' => '',
            'rows' => 3,
            'cols' => null,
            'wrapper' => 'mb-3',
            'labelClass' => '',
            'inputClass' => '',
            'attributes' => [],
            'maxlength' => null
        ];
        
        $opt = array_merge($defaults, $options);
        
        $required = $opt['required'] ? 'required' : '';
        $readonly = $opt['readonly'] ? 'readonly' : '';
        $disabled = $opt['disabled'] ? 'disabled' : '';
        $requiredIndicator = $opt['required'] ? ' <span class="text-danger">*</span>' : '';
        
        // Start wrapper
        $html = '<div class="' . esc($opt['wrapper']) . '">';
        
        // Label
        $labelClasses = 'form-label ' . $opt['labelClass'];
        $html .= '<label for="' . esc($name) . '" class="' . trim($labelClasses) . '">';
        $html .= esc($label) . $requiredIndicator;
        $html .= '</label>';
        
        // Textarea
        $textareaClasses = 'form-control ' . $opt['inputClass'];
        $html .= '<textarea class="' . trim($textareaClasses) . '" ';
        $html .= 'name="' . esc($name) . '" ';
        $html .= 'id="' . esc($name) . '" ';
        $html .= 'rows="' . (int)$opt['rows'] . '" ';
        
        if ($opt['cols']) {
            $html .= 'cols="' . (int)$opt['cols'] . '" ';
        }
        
        if ($opt['placeholder']) {
            $html .= 'placeholder="' . esc($opt['placeholder']) . '" ';
        }
        
        if ($opt['maxlength']) {
            $html .= 'maxlength="' . esc($opt['maxlength']) . '" ';
        }
        
        $html .= $required . ' ' . $readonly . ' ' . $disabled;
        
        // Additional attributes
        foreach ($opt['attributes'] as $key => $val) {
            $html .= ' ' . esc($key) . '="' . esc($val) . '"';
        }
        
        $html .= '>';
        $html .= esc($opt['value']);
        $html .= '</textarea>';
        
        // Help text
        if ($opt['helpText']) {
            $html .= '<div class="form-text">' . esc($opt['helpText']) . '</div>';
        }
        
        // Validation feedback
        $html .= '<div class="invalid-feedback"></div>';
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_checkbox_field')) {
    /**
     * Generate a checkbox field
     * 
     * @param string $name Field name
     * @param string $label Label text
     * @param array $options Configuration options:
     *   - value: Checkbox value (default: '1')
     *   - checked: Whether checked by default
     *   - disabled: Disable checkbox
     *   - helpText: Help text below checkbox
     *   - wrapper: Wrapper class (default: 'mb-3')
     *   - switch: Use switch style (default: false)
     * 
     * @return string HTML checkbox field
     */
    function form_checkbox_field($name, $label, $options = [])
    {
        $defaults = [
            'value' => '1',
            'checked' => (old($name) !== null) ? (bool)old($name) : false,
            'disabled' => false,
            'helpText' => '',
            'wrapper' => 'mb-3',
            'switch' => false,
            'attributes' => []
        ];
        
        $opt = array_merge($defaults, $options);
        
        $checked = $opt['checked'] ? 'checked' : '';
        $disabled = $opt['disabled'] ? 'disabled' : '';
        $checkClass = $opt['switch'] ? 'form-check form-switch' : 'form-check';
        
        // Start wrapper
        $html = '<div class="' . esc($opt['wrapper']) . '">';
        $html .= '<div class="' . $checkClass . '">';
        
        // Checkbox input
        $html .= '<input class="form-check-input" ';
        $html .= 'type="checkbox" ';
        $html .= 'name="' . esc($name) . '" ';
        $html .= 'id="' . esc($name) . '" ';
        $html .= 'value="' . esc($opt['value']) . '" ';
        $html .= $checked . ' ' . $disabled;
        
        // Additional attributes
        foreach ($opt['attributes'] as $key => $val) {
            $html .= ' ' . esc($key) . '="' . esc($val) . '"';
        }
        
        $html .= '>';
        
        // Label
        $html .= '<label class="form-check-label" for="' . esc($name) . '">';
        $html .= esc($label);
        $html .= '</label>';
        
        $html .= '</div>';
        
        // Help text
        if ($opt['helpText']) {
            $html .= '<div class="form-text">' . esc($opt['helpText']) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_radio_group')) {
    /**
     * Generate a radio button group
     * 
     * @param string $name Field name (same for all radios in group)
     * @param string $label Group label
     * @param array $options_array Associative array of value => label pairs
     * @param array $options Configuration options:
     *   - selected: Currently selected value
     *   - required: Whether selection is required
     *   - disabled: Disable all radios
     *   - helpText: Help text below group
     *   - wrapper: Wrapper class
     *   - inline: Display radios inline (default: false)
     * 
     * @return string HTML radio group
     */
    function form_radio_group($name, $label, $options_array, $options = [])
    {
        $defaults = [
            'selected' => old($name) ?? '',
            'required' => false,
            'disabled' => false,
            'helpText' => '',
            'wrapper' => 'mb-3',
            'inline' => false,
            'attributes' => []
        ];
        
        $opt = array_merge($defaults, $options);
        
        $required = $opt['required'] ? 'required' : '';
        $disabled = $opt['disabled'] ? 'disabled' : '';
        $requiredIndicator = $opt['required'] ? ' <span class="text-danger">*</span>' : '';
        $checkClass = $opt['inline'] ? 'form-check form-check-inline' : 'form-check';
        
        // Start wrapper
        $html = '<div class="' . esc($opt['wrapper']) . '">';
        
        // Group label
        if ($label) {
            $html .= '<label class="form-label">' . esc($label) . $requiredIndicator . '</label>';
        }
        
        // Radio buttons
        $index = 0;
        foreach ($options_array as $val => $text) {
            $checked = ($val == $opt['selected']) ? 'checked' : '';
            $radioId = esc($name) . '_' . $index;
            
            $html .= '<div class="' . $checkClass . '">';
            $html .= '<input class="form-check-input" ';
            $html .= 'type="radio" ';
            $html .= 'name="' . esc($name) . '" ';
            $html .= 'id="' . $radioId . '" ';
            $html .= 'value="' . esc($val) . '" ';
            $html .= $checked . ' ' . $required . ' ' . $disabled;
            
            // Additional attributes
            foreach ($opt['attributes'] as $key => $attrVal) {
                $html .= ' ' . esc($key) . '="' . esc($attrVal) . '"';
            }
            
            $html .= '>';
            $html .= '<label class="form-check-label" for="' . $radioId . '">';
            $html .= esc($text);
            $html .= '</label>';
            $html .= '</div>';
            
            $index++;
        }
        
        // Help text
        if ($opt['helpText']) {
            $html .= '<div class="form-text">' . esc($opt['helpText']) . '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_file_field')) {
    /**
     * Generate a file upload field
     * 
     * @param string $name Field name
     * @param string $label Field label
     * @param array $options Configuration options:
     *   - accept: Accepted file types (e.g., 'image/*', '.pdf,.doc')
     *   - multiple: Allow multiple files
     *   - required: Whether file is required
     *   - helpText: Help text below field
     *   - size: Field size
     * 
     * @return string HTML file field
     */
    function form_file_field($name, $label, $options = [])
    {
        $defaults = [
            'accept' => '',
            'multiple' => false,
            'required' => false,
            'disabled' => false,
            'helpText' => '',
            'size' => 'normal',
            'wrapper' => 'mb-3',
            'labelClass' => '',
            'inputClass' => '',
            'attributes' => []
        ];
        
        $opt = array_merge($defaults, $options);
        
        $required = $opt['required'] ? 'required' : '';
        $disabled = $opt['disabled'] ? 'disabled' : '';
        $multiple = $opt['multiple'] ? 'multiple' : '';
        $sizeClass = $opt['size'] === 'sm' ? 'form-control-sm' : '';
        $requiredIndicator = $opt['required'] ? ' <span class="text-danger">*</span>' : '';
        
        // Start wrapper
        $html = '<div class="' . esc($opt['wrapper']) . '">';
        
        // Label
        $labelClasses = 'form-label ' . $opt['labelClass'];
        $html .= '<label for="' . esc($name) . '" class="' . trim($labelClasses) . '">';
        $html .= esc($label) . $requiredIndicator;
        $html .= '</label>';
        
        // File input
        $inputClasses = 'form-control ' . $sizeClass . ' ' . $opt['inputClass'];
        $html .= '<input type="file" ';
        $html .= 'class="' . trim($inputClasses) . '" ';
        $html .= 'name="' . esc($name) . ($opt['multiple'] ? '[]' : '') . '" ';
        $html .= 'id="' . esc($name) . '" ';
        
        if ($opt['accept']) {
            $html .= 'accept="' . esc($opt['accept']) . '" ';
        }
        
        $html .= $required . ' ' . $disabled . ' ' . $multiple;
        
        // Additional attributes
        foreach ($opt['attributes'] as $key => $val) {
            $html .= ' ' . esc($key) . '="' . esc($val) . '"';
        }
        
        $html .= '>';
        
        // Help text
        if ($opt['helpText']) {
            $html .= '<div class="form-text">' . esc($opt['helpText']) . '</div>';
        }
        
        // Validation feedback
        $html .= '<div class="invalid-feedback"></div>';
        
        $html .= '</div>';
        
        return $html;
    }
}

if (!function_exists('form_row')) {
    /**
     * Generate a form row wrapper for multiple fields side-by-side
     * 
     * @param array $fields Array of field HTML strings
     * @param array $columns Array of column classes (e.g., ['col-md-6', 'col-md-6'])
     *                      If not provided, fields split equally
     * @param string $rowClass Additional row classes (default: 'row g-3 mb-3')
     * 
     * @return string HTML form row
     */
    function form_row($fields, $columns = [], $rowClass = 'row g-3 mb-3')
    {
        $fieldCount = count($fields);
        
        // Auto-generate columns if not provided
        if (empty($columns)) {
            $colSize = 12 / $fieldCount;
            $columns = array_fill(0, $fieldCount, "col-md-{$colSize}");
        }
        
        $html = '<div class="' . esc($rowClass) . '">';
        
        foreach ($fields as $index => $field) {
            $colClass = $columns[$index] ?? 'col-md-6';
            $html .= '<div class="' . esc($colClass) . '">';
            $html .= $field;
            $html .= '</div>';
        }
        
        $html .= '</div>';
        
        return $html;
    }
}
