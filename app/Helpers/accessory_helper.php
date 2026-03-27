<?php

if (! function_exists('accessory_label_map')) {
    function accessory_label_map(): array
    {
        return [
            'main_light'            => 'Main Light Set',
            'work_light'            => 'Work Light',
            'rotary_lamp'           => 'Rotary Lamp',
            'back_buzzer'           => 'Back Buzzer',
            'horn_klason'           => 'Horn / Klakson',
            'mirror'                => 'Mirror / Spion',
            'safety_belt'           => 'Safety Belt Standar',
            'load_backrest'         => 'Load Backrest',
            'forks'                 => 'Forks',
            'overhead_guard'        => 'Overhead Guard',
            'document_holder'       => 'Document Holder',
            'tool_kit'              => 'Tool Kit',
            'apar_bracket'          => 'APAR + Bracket',
            'blue_spot'             => 'Blue Spot',
            'red_line'              => 'Red Line',
            'camera_ai'             => 'Camera AI',
            'camera'                => 'Camera Monitor',
            'sensor_parking'        => 'Sensor Parking',
            'speed_limiter'         => 'Speed Limiter',
            'laser_fork'            => 'Laser Fork',
            'voice_announcer'       => 'Voice Announcer',
            'horn_speaker'          => 'Horn Speaker',
            'bio_metric'            => 'Bio Metric',
            'safety_belt_interlock' => 'Safety Belt Interlock',
            'spark_arrestor'        => 'Spark Arrestor',
            'anti_static_strap'     => 'Anti-Static Strap',
            'acrylic'               => 'Acrylic Roof/Windshield',
            'first_aid_kit'         => 'First Aid Kit',
            'wheel_stopper_chock'   => 'Wheel Stopper / Chock',
            'fork_extension'        => 'Fork Extension',
            // backward compatibility aliases
            'lampu_sorot'           => 'Work Light',
            'fire_ext'              => 'APAR + Bracket',
            'horn'                  => 'Horn / Klakson',
            'strobe_light'          => 'Rotary Lamp',
            'main_light_set'        => 'Main Light Set',
            'p3k'                   => 'First Aid Kit',
        ];
    }
}

if (! function_exists('normalize_accessory_key')) {
    function normalize_accessory_key($value): string
    {
        $raw = strtolower(trim((string) $value));
        $normalized = preg_replace('/[^\w]+/', '_', $raw);
        return trim((string) $normalized, '_');
    }
}

if (! function_exists('format_accessory_label')) {
    function format_accessory_label($value): string
    {
        $map = accessory_label_map();
        $key = normalize_accessory_key($value);
        return $map[$key] ?? trim((string) $value);
    }
}

if (! function_exists('format_accessory_csv')) {
    function format_accessory_csv($value): string
    {
        $items = array_values(array_filter(array_map('trim', explode(',', (string) $value))));
        if (empty($items)) {
            return '';
        }

        $formatted = array_map(static fn ($item) => format_accessory_label($item), $items);
        return implode(', ', $formatted);
    }
}
