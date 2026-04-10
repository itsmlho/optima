<?php
$file = __DIR__ . '/../app/Views/service/work_orders.php';
$content = file_get_contents($file);

$startMark = '    function loadUnitsDropdown() {';
$endMark   = '    function displayUnits(units) {';

$start = strpos($content, $startMark);
$end   = strpos($content, $endMark);

if ($start === false || $end === false) {
    echo "ERROR: markers not found\n";
    exit(1);
}

$newFunction = <<<'JSCODE'
    function loadUnitsDropdown() {
        const unitSelect = $('#unit_id');

        // Destroy existing Select2 instance if present
        if (unitSelect.hasClass('select2-hidden-accessible')) {
            unitSelect.select2('destroy');
        }
        unitSelect.empty().append('<option value="">-- Select Unit --</option>');

        const O2 = window.OptimaUnitSelect2;
        const s2cfg = {
            placeholder: '-- Select Unit --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#workOrderModal'),
            minimumInputLength: 1,
            language: {
                noResults:     function() { return "Unit tidak ditemukan"; },
                searching:     function() { return "Mencari..."; },
                inputTooShort: function() { return "Ketik minimal 1 karakter untuk mencari unit"; }
            },
            ajax: {
                url: '<?= base_url('service/work-orders/units-dropdown') ?>',
                dataType: 'json',
                delay: 350,
                data: function(params) {
                    return { search: params.term };
                },
                processResults: function(data) {
                    if (!data.success || !data.data) return { results: [] };

                    return {
                        results: data.data.map(function(unit) {
                            const label = (O2 && typeof O2.line1FromRow === 'function')
                                ? O2.line1FromRow(O2.normalizeRow(unit))
                                : [unit.no_unit, unit.jenis, unit.kapasitas, unit.status ? '[' + unit.status + ']' : ''].filter(Boolean).join(' - ');

                            // Cache for area auto-fill when unit is selected
                            if (!window.allUnits) window.allUnits = [];
                            const idx = window.allUnits.findIndex(function(u) { return u.id == unit.id; });
                            if (idx >= 0) window.allUnits[idx] = unit; else window.allUnits.push(unit);

                            return { id: unit.id, text: label, unit: unit };
                        })
                    };
                },
                cache: true
            }
        };

        if (O2 && typeof O2.templateResult === 'function') {
            s2cfg.templateResult = function(item) {
                if (!item.unit) return item.text;
                return O2.templateResult(item.unit, {});
            };
            s2cfg.templateSelection = function(item) {
                if (!item.unit) return item.text;
                return O2.templateSelection(item.unit, {});
            };
        }

        unitSelect.select2(s2cfg);
    }

JSCODE;

$newContent = substr($content, 0, $start) . $newFunction . substr($content, $end);
file_put_contents($file, $newContent);
echo "Done. Replaced " . ($end - $start) . " chars with " . strlen($newFunction) . " chars.\n";
