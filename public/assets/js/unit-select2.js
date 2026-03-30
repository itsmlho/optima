/**
 * Select2 inventory unit (shared). Load after select2.min.js (defer).
 */
(function (global, $) {
    'use strict';
    if (!$) {
        return;
    }

    function stripBracketNoUnit(raw, id) {
        var s = (raw == null ? '' : String(raw)).trim();
        if (!s) {
            return s;
        }
        if (id != null && id !== '') {
            var idEsc = String(id).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            s = s.replace(new RegExp('^\\[' + idEsc + '\\]\\s*', 'i'), '');
        }
        var prev;
        do {
            prev = s;
            s = s.replace(/^\[[\d\s]+\]\s*/, '');
            s = s.replace(/^\[[^\]]+\]\s*/, '');
            s = s.replace(/^\s*[·•]\s*/, '');
        } while (s !== prev);
        return s.trim();
    }

    function normalizeRow(raw) {
        if (raw == null) {
            return null;
        }
        var id = raw.id != null ? raw.id : (raw.id_inventory_unit != null ? raw.id_inventory_unit : raw.unit_id);
        var idForStrip = raw.id_inventory_unit != null && raw.id_inventory_unit !== ''
            ? raw.id_inventory_unit
            : (raw.unit_id != null ? raw.unit_id : id);
        var noSrc = raw.no_unit != null ? raw.no_unit : (raw.unit_number != null ? raw.unit_number : raw.noUnit);
        var merk = raw.merk != null ? raw.merk : raw.merk_unit;
        var model_unit = raw.model_unit != null ? raw.model_unit : raw.model;
        var jenis = raw.jenis != null ? raw.jenis : (raw.unit_type != null ? raw.unit_type : raw.tipe);
        var kap = raw.kapasitas != null ? raw.kapasitas : raw.kapasitas_unit;
        var status = raw.status != null ? raw.status : (raw.status_name != null ? raw.status_name : raw.status_unit);
        var lokasi = raw.lokasi != null ? raw.lokasi : raw.location_name;
        var sn = raw.serial_number != null ? String(raw.serial_number) : '';
        if (sn === '-') {
            sn = '';
        }
        var no_unit = stripBracketNoUnit(String(noSrc != null ? noSrc : '').trim(), idForStrip);
        var dep = raw.departemen_name != null ? raw.departemen_name : raw.departemen;
        return {
            id: id !== '' && id != null ? String(id) : '',
            no_unit: no_unit,
            merk: String(merk != null ? merk : ''),
            model_unit: String(model_unit != null ? model_unit : ''),
            jenis: String(jenis != null ? jenis : ''),
            kapasitas: String(kap != null ? kap : ''),
            status: String(status != null ? status : ''),
            lokasi: String(lokasi != null ? lokasi : ''),
            serial_number: sn,
            pelanggan: String(raw.pelanggan != null ? raw.pelanggan : (raw.customer_name != null ? raw.customer_name : '')),
            tipe_unit: String(raw.tipe_unit != null ? raw.tipe_unit : raw.tipe != null ? raw.tipe : ''),
            departemen: dep != null ? String(dep) : '',
            departemen_id: raw.departemen_id,
            is_assigned: !!(raw.is_assigned_in_spk || raw.is_assigned === true || raw.is_assigned === 'true')
        };
    }

    function rowFromSelect2Item(item) {
        if (!item || item.loading) {
            return null;
        }
        if (!item.id && item.id !== 0) {
            return null;
        }
        if (item.element && item.element.nodeType === 1) {
            var el = item.element;
            function get(k) {
                return el.getAttribute('data-' + k) || '';
            }
            var assigned = get('is-assigned');
            return normalizeRow({
                id: item.id,
                no_unit: get('no-unit'),
                merk: get('merk-unit') || get('merk'),
                model_unit: get('model-unit'),
                serial_number: get('serial-number'),
                jenis: get('jenis') || get('tipe-unit'),
                kapasitas: get('kapasitas'),
                status: get('status-name') || get('status'),
                lokasi: get('location-name') || get('lokasi'),
                departemen: get('departemen'),
                departemen_id: get('departemen-id'),
                is_assigned: assigned === 'true' || assigned === true
            });
        }
        return normalizeRow(item);
    }

    /**
     * Select2 AJAX Work Order search-units (POST). Option value = no_unit (sesuai backend kanibal).
     */
    function buildWorkOrderSearchUnitsSelect2Config(cfg) {
        cfg = cfg || {};
        var url = cfg.url || '';
        var tokenName = cfg.csrfTokenName || global.csrfTokenName || '';
        var tokenValue = cfg.csrfTokenValue != null ? cfg.csrfTokenValue : (global.csrfTokenValue || '');
        if (typeof global.getCsrfToken === 'function' && !tokenValue) {
            tokenValue = global.getCsrfToken();
        }
        var templateOpts = cfg.templateOpts || {};

        return {
            placeholder: cfg.placeholder || '-- Cari No. Unit / SN / Model / Tipe --',
            allowClear: cfg.allowClear !== false,
            width: cfg.width || '100%',
            dropdownParent: cfg.dropdownParent,
            minimumInputLength: cfg.minimumInputLength != null ? cfg.minimumInputLength : 1,
            ajax: {
                url: url,
                type: 'POST',
                dataType: 'json',
                delay: cfg.delay != null ? cfg.delay : 300,
                data: function (params) {
                    var d = { query: params.term || '' };
                    if (tokenName) {
                        d[tokenName] = tokenValue;
                    }
                    return d;
                },
                processResults: function (data) {
                    if (!data || !data.success || !Array.isArray(data.data)) {
                        return { results: [] };
                    }
                    return {
                        results: data.data.map(function (u) {
                            var row = normalizeRow({
                                id: u.id_inventory_unit,
                                id_inventory_unit: u.id_inventory_unit,
                                no_unit: u.no_unit,
                                serial_number: u.serial_number,
                                merk: u.merk_unit,
                                model_unit: u.model_unit,
                                jenis: u.jenis || u.unit_type,
                                kapasitas: u.kapasitas || '',
                                status: u.status || '',
                                lokasi: u.lokasi || '',
                                pelanggan: u.pelanggan
                            });
                            return {
                                id: u.no_unit,
                                text: row.no_unit,
                                id_inventory_unit: u.id_inventory_unit,
                                no_unit: row.no_unit,
                                serial_number: row.serial_number,
                                merk: row.merk,
                                model_unit: row.model_unit,
                                jenis: row.jenis,
                                kapasitas: row.kapasitas,
                                status: row.status,
                                lokasi: row.lokasi,
                                pelanggan: row.pelanggan
                            };
                        })
                    };
                },
                cache: cfg.cache !== false
            },
            templateResult: function (item) {
                return templateResult(item, templateOpts);
            },
            templateSelection: function (item) {
                return templateSelection(item, templateOpts);
            },
            language: cfg.language || {}
        };
    }

    function line1FromRow(row) {
        if (!row) {
            return '';
        }
        var no = row.no_unit || '';
        var mm = [row.merk, row.model_unit].filter(Boolean).join(' ');
        var kap = (row.kapasitas || '').trim();
        var jenis = (row.jenis || '').trim();
        var parts = [no];
        if (mm) {
            parts.push(mm);
        } else if (jenis) {
            parts.push(jenis);
        }
        if (kap) {
            parts.push(kap);
        }
        var out = parts.filter(Boolean).join(' · ');
        return out || '—';
    }

    function line1(item) {
        if (!item || item.loading) {
            return (item && item.text) ? item.text : '';
        }
        if (!item.id && item.id !== 0) {
            return item.text || '';
        }
        var row = rowFromSelect2Item(item);
        if (!row || !row.no_unit) {
            var rawNo = item.no_unit != null && String(item.no_unit).trim() !== '' ? String(item.no_unit).trim() : '';
            if (!rawNo) {
                var t = String(item.text || '').trim();
                var sep = ' · ';
                var j = t.indexOf(sep);
                rawNo = j === -1 ? t : t.slice(0, j).trim();
            }
            var no = stripBracketNoUnit(rawNo, item.id);
            var mm = [item.merk, item.model_unit].filter(Boolean).join(' ');
            var kap = (item.kapasitas || '').trim();
            var jenis = (item.jenis || '').trim();
            var parts = [no];
            if (mm) {
                parts.push(mm);
            } else if (jenis) {
                parts.push(jenis);
            }
            if (kap) {
                parts.push(kap);
            }
            return parts.filter(Boolean).join(' · ');
        }
        return line1FromRow(row);
    }

    function statusBadgeClass(status) {
        var su = String(status || '').toUpperCase();
        if (su.includes('AVAILABLE') || su.includes('NON_ASSET') || su.includes('BOOKED')) {
            return 'badge-soft-green';
        }
        if (su.includes('RETURN')) {
            return 'badge-soft-blue';
        }
        if (su.includes('RENTAL')) {
            return 'badge-soft-cyan';
        }
        if (su.includes('INACTIVE') || su.includes('JUAL')) {
            return 'badge-soft-red';
        }
        return 'badge-soft-gray';
    }

    function spkStatusBadgeSoftClass(statusName) {
        var statusUpper = String(statusName || '').toUpperCase();
        var color = 'secondary';
        if (statusUpper.includes('AVAILABLE')) {
            color = 'success';
        } else if (statusUpper.includes('RETURNED')) {
            color = 'cyan';
        } else if (statusUpper.includes('BOOKED')) {
            color = 'warning';
        } else if (statusUpper.includes('SPARE')) {
            color = 'purple';
        } else if (statusUpper.includes('NON_ASSET') || statusUpper.includes('NON ASSET')) {
            color = 'info';
        } else if (statusUpper.includes('RENTAL') || statusUpper.includes('RENTED')) {
            color = 'orange';
        } else if (statusUpper.includes('PREPARATION') || statusUpper.includes('READY')) {
            color = 'indigo';
        } else if (statusUpper.includes('MAINTENANCE') || statusUpper.includes('REPAIR')) {
            color = 'danger';
        }
        return 'badge-soft-' + color;
    }

    function templateResult(item, opts) {
        opts = opts || {};
        if (item.loading) {
            return item.text;
        }
        if (!item.id && item.id !== 0) {
            return item.text;
        }
        var row = rowFromSelect2Item(item);
        if (!row) {
            return item.text;
        }

        var $container = $('<div class="d-flex flex-column lh-sm"></div>');
        $container.append($('<div class="fw-semibold"></div>').text(line1FromRow(row)));

        var jenis = (row.jenis || '').trim();
        var sn = (row.serial_number || '').trim();
        var mm = [row.merk, row.model_unit].filter(Boolean).join(' ');
        var line2Parts = [];
        if (mm && jenis) {
            line2Parts.push(jenis);
        }
        if (sn) {
            line2Parts.push('SN : ' + sn);
        }
        var $line2 = $('<div class="small"></div>');
        $line2.text(line2Parts.length ? line2Parts.join(' · ') : '—');

        var status = (row.status || '—').trim();
        var loc = (row.lokasi && String(row.lokasi).trim()) ? String(row.lokasi).trim() : 'N/A';
        var badgeCls = opts.extraSpkRow ? spkStatusBadgeSoftClass(status) : statusBadgeClass(status);
        var $line3 = $('<div class="small text-muted d-flex align-items-center flex-wrap gap-2 mt-1"></div>');
        $line3.append($('<span class="badge ' + badgeCls + '"></span>').text(status));
        var $loc = $('<span class="d-inline-flex align-items-center"></span>');
        $loc.append($('<i class="fas fa-map-marker-alt me-1" aria-hidden="true"></i>'));
        $loc.append(document.createTextNode(loc));
        $line3.append($loc);
        $container.append($line2, $line3);

        if (opts.extraSpkRow) {
            var $extra = $('<div class="small mt-1"></div>');
            var kap = (row.kapasitas || '').trim();
            var dep = (row.departemen || '').toUpperCase();
            if (kap && kap !== '-') {
                $extra.append($('<span class="badge badge-soft-orange me-1" style="font-size:0.65rem;padding:2px 6px"></span>').text(kap));
            }
            if (dep) {
                var deptColor = dep === 'ELECTRIC' ? 'green' : (dep === 'GASOLINE' ? 'orange' : (dep === 'DIESEL' ? 'blue' : 'gray'));
                $extra.append($('<span class="badge badge-soft-' + deptColor + ' me-1" style="font-size:0.65rem;padding:2px 6px"></span>').text(dep));
            }
            if (row.is_assigned) {
                $extra.append($('<span class="badge badge-soft-danger me-1" style="font-size:0.65rem;padding:2px 6px"></span>').text('USED IN SPK'));
            }
            if ($extra.children().length) {
                $container.append($extra);
            }
        }

        return $container;
    }

    function templateSelection(item, opts) {
        if (!item || (!item.id && item.id !== 0)) {
            return item.text;
        }
        return line1(item);
    }

    function optionDataAttributes(unit) {
        var r = normalizeRow(unit);
        var sn = unit.serial_number != null && unit.serial_number !== '' ? unit.serial_number : (r.serial_number || '-');
        return {
            'data-no-unit': r.no_unit,
            'data-merk': r.merk,
            'data-merk-unit': r.merk,
            'data-model-unit': r.model_unit,
            'data-serial-number': sn,
            'data-jenis': r.jenis,
            'data-kapasitas': r.kapasitas,
            'data-status-name': r.status,
            'data-status': r.status,
            'data-location-name': r.lokasi,
            'data-lokasi': r.lokasi,
            'data-departemen': r.departemen,
            'data-departemen-id': unit.departemen_id != null ? unit.departemen_id : ''
        };
    }

    function attrsHtml(attrs) {
        var parts = [];
        Object.keys(attrs).forEach(function (k) {
            var v = attrs[k];
            if (v === '' || v == null || v === false) {
                return;
            }
            parts.push(' ' + k + '="' + String(v).replace(/"/g, '&quot;') + '"');
        });
        return parts.join('');
    }

    /**
     * Map row from GET service/data-unit/simple (JSON) for normalizeRow / Select2 AJAX.
     */
    function mapDataUnitSimpleApiRow(x) {
        if (!x) {
            return null;
        }
        return normalizeRow({
            id: x.id,
            id_inventory_unit: x.id,
            no_unit: x.no_unit,
            serial_number: x.serial_number,
            merk: x.merk_unit,
            model_unit: x.model_unit,
            jenis: x.tipe_unit,
            kapasitas: x.kapasitas_unit,
            status: x.status_name,
            lokasi: x.location_name,
            departemen: x.departemen_name,
            departemen_id: x.departemen_id,
            is_assigned_in_spk: x.is_assigned_in_spk,
            needs_no_unit: x.needs_no_unit
        });
    }

    /**
     * Select2 AJAX for GET service/data-unit/simple — option id = id_inventory_unit.
     * @param {Object} cfg
     * @param {string} cfg.url — full URL to data-unit/simple
     * @param {function():Object} [cfg.extraAjaxParams] — e.g. exclude_spk_id, spk_department
     * @param {string[]|null} [cfg.allowedStatuses] — filter by status_name; null = no filter
     * @param {boolean} [cfg.disableAssigned=true] — disable is_assigned_in_spk rows
     */
    function buildServiceDataUnitSimpleSelect2Config(cfg) {
        cfg = cfg || {};
        var url = cfg.url || '';
        var extraParams = cfg.extraAjaxParams || function () {
            return {};
        };
        var allowedStatuses = cfg.allowedStatuses;
        var disableAssigned = cfg.disableAssigned !== false;
        var templateOpts = $.extend({ extraSpkRow: cfg.extraSpkRow !== false }, cfg.templateOpts || {});

        return {
            placeholder: cfg.placeholder || '- Select Unit -',
            allowClear: cfg.allowClear !== false,
            width: cfg.width || '100%',
            dropdownParent: cfg.dropdownParent,
            minimumInputLength: cfg.minimumInputLength != null ? cfg.minimumInputLength : 0,
            ajax: {
                url: url,
                type: 'GET',
                dataType: 'json',
                delay: cfg.delay != null ? cfg.delay : 250,
                data: function (params) {
                    return $.extend({ q: params.term || '' }, extraParams());
                },
                processResults: function (res) {
                    if (!res || !res.success || !Array.isArray(res.data)) {
                        return { results: [] };
                    }
                    var out = [];
                    res.data.forEach(function (x) {
                        if (allowedStatuses && allowedStatuses.length &&
                            allowedStatuses.indexOf(x.status_name) === -1) {
                            return;
                        }
                        var row = mapDataUnitSimpleApiRow(x);
                        if (!row) {
                            return;
                        }
                        var assigned = !!x.is_assigned_in_spk;
                        var dis = disableAssigned && assigned;
                        out.push({
                            id: String(x.id),
                            text: row.no_unit || String(x.id),
                            disabled: dis,
                            no_unit: row.no_unit,
                            serial_number: row.serial_number,
                            merk: row.merk,
                            model_unit: row.model_unit,
                            jenis: row.jenis,
                            kapasitas: row.kapasitas,
                            status: row.status,
                            lokasi: row.lokasi,
                            departemen: row.departemen,
                            departemen_id: x.departemen_id,
                            id_inventory_unit: x.id,
                            needs_no_unit: x.needs_no_unit,
                            status_unit_id: x.status_unit_id,
                            is_assigned_in_spk: assigned
                        });
                    });
                    return { results: out };
                },
                cache: cfg.cache !== false
            },
            templateResult: function (item) {
                if (item.loading) {
                    return item.text;
                }
                if (item.disabled) {
                    var $d = templateResult(item, templateOpts);
                    if ($d && $d.jquery) {
                        $d.css('opacity', '0.55');
                    }
                    return $d;
                }
                return templateResult(item, templateOpts);
            },
            templateSelection: function (item) {
                return templateSelection(item, templateOpts);
            },
            language: cfg.language || {}
        };
    }

    function buildAjaxConfig(cfg) {
        cfg = cfg || {};
        var baseUrl = (cfg.baseUrl != null ? cfg.baseUrl : global.baseUrl) || '';
        baseUrl = String(baseUrl).replace(/\/?$/, '/');
        var searchPath = cfg.searchPath != null ? cfg.searchPath : '';
        var fullUrl = cfg.searchUrl || (baseUrl + String(searchPath).replace(/^\//, ''));
        var purpose = cfg.purpose === 'add_location' ? 'add_location' : (cfg.purpose || 'unit_swap');
        var filterRow = cfg.filterRow || function () {
            return true;
        };
        var extraParams = cfg.extraAjaxParams || function () {
            return {};
        };
        var templateOpts = cfg.templateOpts || {};

        return {
            dropdownParent: cfg.dropdownParent,
            width: cfg.width || '100%',
            minimumInputLength: cfg.minimumInputLength != null ? cfg.minimumInputLength : 1,
            allowClear: cfg.allowClear !== false,
            placeholder: cfg.placeholder || (purpose === 'add_location'
                ? 'No unit, SN, model, merk… (min. 1 huruf/angka)'
                : 'No unit, SN, model, merk… (min. 1 huruf/angka)'),
            language: cfg.language || {
                inputTooShort: function () {
                    return 'Ketik minimal 1 karakter (no unit bisa 1 digit)';
                }
            },
            ajax: {
                url: fullUrl,
                dataType: 'json',
                delay: cfg.delay != null ? cfg.delay : 300,
                data: function (params) {
                    return $.extend({ q: params.term || '', purpose: purpose }, extraParams(params));
                },
                processResults: function (res) {
                    if (!res || !res.success || !Array.isArray(res.data)) {
                        return { results: [] };
                    }
                    var out = [];
                    res.data.forEach(function (u) {
                        if (!filterRow(u)) {
                            return;
                        }
                        var row = normalizeRow(u);
                        if (!row.no_unit) {
                            return;
                        }
                        row.text = row.no_unit;
                        out.push(row);
                    });
                    return { results: out };
                },
                cache: cfg.cache !== false
            },
            templateResult: function (item) {
                return templateResult(item, templateOpts);
            },
            templateSelection: function (item) {
                return templateSelection(item, templateOpts);
            }
        };
    }

    global.OptimaUnitSelect2 = {
        stripBracketNoUnit: stripBracketNoUnit,
        normalizeRow: normalizeRow,
        rowFromSelect2Item: rowFromSelect2Item,
        line1: line1,
        line1FromRow: line1FromRow,
        templateResult: templateResult,
        templateSelection: templateSelection,
        optionDataAttributes: optionDataAttributes,
        optionAttrsHtml: attrsHtml,
        buildAjaxConfig: buildAjaxConfig,
        buildWorkOrderSearchUnitsSelect2Config: buildWorkOrderSearchUnitsSelect2Config,
        buildServiceDataUnitSimpleSelect2Config: buildServiceDataUnitSimpleSelect2Config,
        mapDataUnitSimpleApiRow: mapDataUnitSimpleApiRow
    };
})(window, window.jQuery);
