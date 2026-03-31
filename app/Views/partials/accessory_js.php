<script>
if (!window.OptimaAccessory) {
window.OptimaAccessory = (() => {
        const map = {
            // Kode utama sesuai spesifikasi terbaru
            main_light: 'Main Light Set',
            work_light: 'Work Light',
            rotary_lamp: 'Rotary Lamp',
            back_buzzer: 'Back Buzzer',
            horn_klason: 'Horn / Klakson',
            mirror: 'Mirror / Spion',
            safety_belt: 'Safety Belt Standar',
            load_backrest: 'Load Backrest',
            overhead_guard: 'Overhead Guard',
            document_holder: 'Document Holder',
            tool_kit: 'Tool Kit',
            apar_bracket: 'APAR + Bracket',
            blue_spot: 'Blue Spot',
            red_spot: 'Red Spot',
            red_line: 'Red Line',
            blue_line: 'Blue Line',
            camera_ai: 'Camera AI',
            camera: 'Camera Monitor',
            sensor_parking: 'Sensor Parking',
            speed_limiter: 'Speed Limiter',
            laser_fork: 'Laser Fork',
            voice_announcer: 'Voice Announcer',
            horn_speaker: 'Horn Speaker',
            bio_metric: 'Bio Metric',
            safety_belt_interlock: 'Safety Belt Interlock',
            spark_arrestor: 'Spark Arrestor',
            anti_static_strap: 'Anti-Static Strap',
            acrylic_roof: 'Acrylic Roof/Windshield',
            acrylic_side: 'Acrylic Side',
            acrylic_front: 'Acrylic Front',
            p3k: 'P3K / First Aid Kit',
            wheel_stopper_chock: 'Wheel Stopper / Chock',
            fork_extension: 'Fork Extension',
            fire_ext_powder_1kg: 'APAR 1 KG (Powder)',
            fire_ext_powder_3kg: 'APAR 3 KG (Powder)',
            fire_ext_lithium_af31: 'APAR Lithium AF31',
            load_weight_indicator: 'Load Weight Indicator / Timbangan',
            impact_sensor: 'Impact / Shock Sensor',
            battery_watering_sys: 'Battery Watering System',
            panoramic_mirror: 'Panoramic Mirror',

            // Fork & misc
            forks: 'Forks (Sepasang Garpu Standar)',
            beacon: 'Beacon',
            telematic: 'Telematic',

            // Backward compatibility aliases (normalize key lama)
            acrylic: 'Acrylic Roof/Windshield',
            first_aid_kit: 'First Aid Kit',
            lampu_sorot: 'Work Light',
            fire_ext: 'APAR + Bracket',
            horn: 'Horn / Klakson',
            strobe_light: 'Rotary Lamp',
            main_light_set: 'Main Light Set'
        };

        const checkboxAlias = {
            'MAIN LIGHT': 'LAMPU UTAMA',
            'MAIN LIGHT SET': 'LAMPU UTAMA',
            'HORN / KLAKSON': 'HORN KLASON',
            'CAMERA': 'CAMERA MONITOR',
            'P3K': 'FIRST AID KIT',
            'SPARS ARRESTOR': 'SPARK ARRESTOR',
            'SAFETY BELT INTERLOC': 'SAFETY BELT INTERLOCK'
        };

        const normalizeKey = (value) =>
            String(value || '').trim().toLowerCase().replace(/[^\w]+/g, '_').replace(/^_+|_+$/g, '');

        const formatLabel = (value) => map[normalizeKey(value)] || String(value || '').trim();

        const formatList = (list) => (Array.isArray(list) ? list : []).map(formatLabel);

        const normalizeCheckboxValue = (value) => {
            const raw = String(value || '').trim();
            const key = raw.toUpperCase().replace(/[_-]+/g, ' ').replace(/\s+/g, ' ');
            return checkboxAlias[key] || key;
        };

        // UI grouping & rendering helpers (single source of truth for checkbox UIs)
        const groups = {
            quotationStandard: {
                title: 'Aksesori Standar (Bawaan Pabrik)',
                colorClass: 'text-success',
                items: [
                    { code: 'main_light',    label: 'Main Light Set (Headlight, Reverse, Signal, Stop Lamp)' },
                    { code: 'work_light',    label: 'Work Light (Lampu Sorot Depan/Tiang)' },
                    { code: 'rotary_lamp',   label: 'Rotary Lamp (Lampu Peringatan Berputar)' },
                    { code: 'back_buzzer',   label: 'Back Buzzer (Alarm Mundur)' },
                    { code: 'horn_klason',   label: 'Horn / Klakson (Tipe Standar)' },
                    { code: 'mirror',        label: 'Mirror / Spion (Kiri & Kanan)' },
                    { code: 'safety_belt',   label: 'Safety Belt Standar (Manual)' },
                    { code: 'load_backrest', label: 'Load Backrest' },
                    { code: 'overhead_guard',  label: 'Overhead Guard' },
                    { code: 'document_holder', label: 'Document Holder' },
                    { code: 'tool_kit',        label: 'Tool Kit' },
                    { code: 'apar_bracket',    label: 'APAR + Bracket' }
                ]
            },
            quotationExtra: {
                title: 'Aksesori Tambahan (Optional / Safety Upgrade)',
                colorClass: 'text-primary',
                items: [
                    { code: 'blue_spot' },
                    { code: 'red_spot' },
                    { code: 'red_line' },
                    { code: 'blue_line' },
                    { code: 'camera_ai' },
                    { code: 'camera' },
                    { code: 'sensor_parking' },
                    { code: 'speed_limiter' },
                    { code: 'laser_fork' },
                    { code: 'voice_announcer' },
                    { code: 'horn_speaker' },
                    { code: 'bio_metric' },
                    { code: 'safety_belt_interlock' },
                    { code: 'spark_arrestor' },
                    { code: 'anti_static_strap' },
                    { code: 'acrylic_roof' },
                    { code: 'acrylic_side' },
                    { code: 'acrylic_front' },
                    { code: 'p3k' },
                    { code: 'wheel_stopper_chock' },
                    { code: 'fork_extension' },
                    { code: 'fire_ext_powder_1kg' },
                    { code: 'fire_ext_powder_3kg' },
                    { code: 'fire_ext_lithium_af31' },
                    { code: 'load_weight_indicator' },
                    { code: 'impact_sensor' },
                    { code: 'battery_watering_sys' },
                    { code: 'panoramic_mirror' }
                ]
            },
            // Verification groupings — mirrors quotationStandard + quotationExtra
            // values use UPPER CASE to match legacy DB storage format
            verificationUnit: {
                // Mirrors quotationStandard — aksesori bawaan pabrik
                title: 'Aksesoris Standar (Bawaan Pabrik)',
                icon: 'fa-truck',
                colorClass: 'text-success',
                items: [
                    { code: 'main_light',    label: 'Main Light Set (Headlight, Reverse, Signal, Stop Lamp)', value: 'LAMPU UTAMA'        },
                    { code: 'work_light',    label: 'Work Light (Lampu Sorot Depan/Tiang)',                   value: 'WORK LIGHT'         },
                    { code: 'rotary_lamp',   label: 'Rotary Lamp (Lampu Peringatan Berputar)',                value: 'ROTARY LAMP'        },
                    { code: 'back_buzzer',   label: 'Back Buzzer (Alarm Mundur)',                            value: 'BACK BUZZER'        },
                    { code: 'horn_klason',   label: 'Horn / Klakson (Tipe Standar)',                         value: 'HORN KLASON'        },
                    { code: 'mirror',        label: 'Mirror / Spion (Kiri & Kanan)',                         value: 'MIRROR'             },
                    { code: 'safety_belt',   label: 'Safety Belt Standar (Manual)',                          value: 'SAFETY BELT STANDAR'},
                    { code: 'load_backrest', label: 'Load Backrest',                                         value: 'LOAD BACKREST'      },
                    { code: 'overhead_guard',  label: 'Overhead Guard',   value: 'OVERHEAD GUARD'  },
                    { code: 'document_holder', label: 'Document Holder',  value: 'DOCUMENT HOLDER' },
                    { code: 'tool_kit',        label: 'Tool Kit',         value: 'TOOL KIT'        },
                    { code: 'apar_bracket',    label: 'APAR + Bracket',   value: 'APAR BRACKET'    }
                ]
            },
            verificationSafety: {
                // Mirrors quotationExtra — safety & electronic upgrades
                title: 'Aksesoris Keamanan & Elektronik (Tambahan)',
                icon: 'fa-shield-alt',
                colorClass: 'text-primary',
                items: [
                    { code: 'blue_spot',             label: 'Blue Spot',             value: 'BLUE SPOT'             },
                    { code: 'red_spot',              label: 'Red Spot',              value: 'RED SPOT'              },
                    { code: 'red_line',              label: 'Red Line',              value: 'RED LINE'              },
                    { code: 'blue_line',             label: 'Blue Line',             value: 'BLUE LINE'             },
                    { code: 'camera_ai',             label: 'Camera AI',             value: 'CAMERA AI'             },
                    { code: 'camera',                label: 'Camera Monitor',        value: 'CAMERA MONITOR'        },
                    { code: 'sensor_parking',        label: 'Sensor Parking',        value: 'SENSOR PARKING'        },
                    { code: 'speed_limiter',         label: 'Speed Limiter',         value: 'SPEED LIMITER'         },
                    { code: 'laser_fork',            label: 'Laser Fork',            value: 'LASER FORK'            },
                    { code: 'voice_announcer',       label: 'Voice Announcer',       value: 'VOICE ANNOUNCER'       },
                    { code: 'horn_speaker',          label: 'Horn Speaker',          value: 'HORN SPEAKER'          },
                    { code: 'bio_metric',            label: 'Bio Metric',            value: 'BIO METRIC'            },
                    { code: 'safety_belt_interlock', label: 'Safety Belt Interlock', value: 'SAFETY BELT INTERLOCK' },
                    { code: 'beacon',                label: 'Beacon',                value: 'BEACON'                },
                    { code: 'telematic',             label: 'Telematic',             value: 'TELEMATIC'             }
                ]
            },
            verificationOther: {
                // Mirrors quotationExtra — physical protection & special equipment
                title: 'Aksesoris Fisik & Perlindungan (Tambahan)',
                icon: 'fa-plus-circle',
                colorClass: 'text-warning',
                items: [
                    { code: 'spark_arrestor',        label: 'Spark Arrestor',               value: 'SPARK ARRESTOR'        },
                    { code: 'anti_static_strap',     label: 'Anti-Static Strap',            value: 'ANTI STATIC STRAP'     },
                    { code: 'acrylic_roof',          label: 'Acrylic Roof/Windshield',      value: 'ACRYLIC'               },
                    { code: 'acrylic_side',          label: 'Acrylic Side',                 value: 'ACRYLIC SIDE'          },
                    { code: 'acrylic_front',         label: 'Acrylic Front',                value: 'ACRYLIC FRONT'         },
                    { code: 'p3k',                   label: 'P3K / First Aid Kit',          value: 'FIRST AID KIT'         },
                    { code: 'wheel_stopper_chock',   label: 'Wheel Stopper / Chock',        value: 'WHEEL STOPPER CHOCK'   },
                    { code: 'fork_extension',        label: 'Fork Extension',               value: 'FORK EXTENSION'        },
                    { code: 'fire_ext_powder_1kg',   label: 'APAR 1 KG (Powder)',           value: 'APAR 1 KG'             },
                    { code: 'fire_ext_powder_3kg',   label: 'APAR 3 KG (Powder)',           value: 'APAR 3 KG'             },
                    { code: 'fire_ext_lithium_af31', label: 'APAR Lithium AF31',            value: 'APAR LITHIUM AF31'     },
                    { code: 'load_weight_indicator', label: 'Load Weight Indicator / Timbangan', value: 'LOAD WEIGHT INDICATOR' },
                    { code: 'impact_sensor',         label: 'Impact / Shock Sensor',        value: 'IMPACT SENSOR'         },
                    { code: 'battery_watering_sys',  label: 'Battery Watering System',      value: 'BATTERY WATERING SYSTEM'},
                    { code: 'panoramic_mirror',      label: 'Panoramic Mirror',             value: 'PANORAMIC MIRROR'      }
                ]
            }
        };

        const getGroups = () => groups;

        /**
         * Return the value/code list for a group (useful for "Set Standar" buttons).
         * @param {string} groupKey
         * @returns {string[]}
         */
        const getGroupItemCodes = (groupKey) => {
            const g = groups[groupKey];
            if (!g || !Array.isArray(g.items)) return [];
            return g.items.map(i => i.value || i.code);
        };

        /**
         * Render multiple accessory groups into a container.
         *
         * Style 'inline':  flat row with col-12 dividers between groups (quotation form)
         * Style 'section': each group in its own block with h6 heading (verification form)
         *
         * @param {HTMLElement|string} container
         * @param {string[]} groupKeyList
         * @param {Object} opts
         *   - name         : checkbox name attr      (default: 'accessories[]')
         *   - idPrefix     : id prefix               (default: 'acc_')
         *   - columnsClass : Bootstrap column class  (default: 'col-md-3 col-sm-6 mb-2')
         *   - style        : 'inline' | 'section'    (default: 'section')
         */
        const renderGroupSections = (container, groupKeyList, opts = {}) => {
            if (!container) return;
            const el = typeof container === 'string' ? document.querySelector(container) : container;
            if (!el) return;
            if (el.dataset.rendered === '1') return;

            const name      = opts.name         || 'accessories[]';
            const idPrefix  = opts.idPrefix      || 'acc_';
            const colClass  = opts.columnsClass  || 'col-md-3 col-sm-6 mb-2';
            const style     = opts.style         || 'section';

            el.innerHTML = '';

            const _buildRow = (items) => {
                const row = document.createElement('div');
                row.className = 'row';
                items.forEach(item => {
                    const code      = item.code;  if (!code) return;
                    const labelText = item.label || formatLabel(code);
                    const val       = item.value  || code;
                    const cbId      = idPrefix + code;

                    const col = document.createElement('div');  col.className = colClass;
                    const fc  = document.createElement('div');  fc.className  = 'form-check';
                    const inp = document.createElement('input');
                    inp.className = 'form-check-input'; inp.type = 'checkbox';
                    inp.name = name; inp.value = val; inp.id = cbId;
                    const lbl = document.createElement('label');
                    lbl.className = 'form-check-label'; lbl.setAttribute('for', cbId);
                    lbl.textContent = labelText;
                    fc.appendChild(inp); fc.appendChild(lbl); col.appendChild(fc); row.appendChild(col);
                });
                return row;
            };

            if (style === 'inline') {
                // Quotation style: flat row.g-2 with col-12 divider headers
                const outerRow = document.createElement('div');
                outerRow.className = 'row g-2';
                let first = true;
                groupKeyList.forEach(gKey => {
                    const group = groups[gKey]; if (!group) return;
                    const hCol = document.createElement('div');
                    hCol.className = 'col-12 ' + (first ? 'mt-1' : 'mt-3');
                    const hDiv = document.createElement('div');
                    hDiv.className = 'fw-semibold ' + (group.colorClass || 'text-muted');
                    hDiv.textContent = group.title;
                    hCol.appendChild(hDiv); outerRow.appendChild(hCol);
                    group.items.forEach(item => {
                        const code      = item.code;  if (!code) return;
                        const labelText = item.label || formatLabel(code);
                        const val       = item.value  || code;
                        const cbId      = idPrefix + code;
                        const col = document.createElement('div');  col.className = colClass;
                        const fc  = document.createElement('div');  fc.className  = 'form-check';
                        const inp = document.createElement('input');
                        inp.className = 'form-check-input'; inp.type = 'checkbox';
                        inp.name = name; inp.value = val; inp.id = cbId;
                        const lbl = document.createElement('label');
                        lbl.className = 'form-check-label'; lbl.setAttribute('for', cbId);
                        lbl.textContent = labelText;
                        fc.appendChild(inp); fc.appendChild(lbl); col.appendChild(fc); outerRow.appendChild(col);
                    });
                    first = false;
                });
                el.appendChild(outerRow);
            } else {
                // Verification style: each group in its own titled section
                groupKeyList.forEach(gKey => {
                    const group = groups[gKey]; if (!group) return;
                    const section = document.createElement('div');
                    section.className = 'mb-4';
                    const h6 = document.createElement('h6');
                    h6.className = (group.colorClass || 'text-muted') + ' mb-3';
                    h6.innerHTML = group.icon
                        ? `<i class="fas ${group.icon} me-2"></i>${group.title}`
                        : group.title;
                    section.appendChild(h6);
                    section.appendChild(_buildRow(group.items));
                    el.appendChild(section);
                });
            }

            el.dataset.rendered = '1';
        };

        /**
         * Render accessories as a checkbox grid into a container.
         * @param {HTMLElement|string} container
         * @param {Object} opts
         *   - groupKey: key from groups
         *   - name: checkbox name attribute
         *   - idPrefix: id prefix for checkbox (default: 'acc_')
         *   - columnsClass: Bootstrap column class (default: 'col-md-4 col-sm-6 mb-2')
         */
        const renderCheckboxGrid = (container, opts = {}) => {
            if (!container) return;
            const el = typeof container === 'string' ? document.querySelector(container) : container;
            if (!el) return;

            const group = groups[opts.groupKey];
            if (!group || !Array.isArray(group.items)) return;

            const name = opts.name || 'accessories[]';
            const idPrefix = opts.idPrefix || 'acc_';
            const colClass = opts.columnsClass || 'col-md-4 col-sm-6 mb-2';

            // Avoid double rendering
            if (el.dataset.rendered === '1') return;
            el.innerHTML = '';

            const row = document.createElement('div');
            row.className = 'row';

            group.items.forEach((item) => {
                const code = item.code;
                if (!code) return;
                const labelText = item.label || formatLabel(code);
                const checkboxId = `${idPrefix}${code}`;
                const valueAttr = item.value || code;

                const col = document.createElement('div');
                col.className = colClass;

                const formCheck = document.createElement('div');
                formCheck.className = 'form-check';

                const input = document.createElement('input');
                input.className = 'form-check-input';
                input.type = 'checkbox';
                input.name = name;
                input.value = valueAttr;
                input.id = checkboxId;

                const labelEl = document.createElement('label');
                labelEl.className = 'form-check-label';
                labelEl.setAttribute('for', checkboxId);
                labelEl.textContent = labelText;

                formCheck.appendChild(input);
                formCheck.appendChild(labelEl);
                col.appendChild(formCheck);
                row.appendChild(col);
            });

            el.appendChild(row);
            el.dataset.rendered = '1';
        };

        return { normalizeKey, formatLabel, formatList, normalizeCheckboxValue, getGroups, getGroupItemCodes, renderCheckboxGrid, renderGroupSections };
    })();
}
</script>
