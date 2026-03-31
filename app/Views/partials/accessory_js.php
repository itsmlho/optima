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

            // Backward compatibility aliases (normalize key lama)
            forks: 'Forks',
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

        return { normalizeKey, formatLabel, formatList, normalizeCheckboxValue };
    })();
}
</script>
