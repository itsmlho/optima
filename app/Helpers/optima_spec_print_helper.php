<?php

declare(strict_types=1);

if (! function_exists('parse_optima_spec_tech_notes')) {
    /**
     * Split quotation notes into user-facing text and structured key:value lines
     * (same markers as marketing/quotations.php).
     *
     * @return array{user_notes: string, tech: array<string, string>}
     */
    function parse_optima_spec_tech_notes(?string $notes): array
    {
        $start = '[OPTIMA_SPEC_TECH]';
        $end   = '[/OPTIMA_SPEC_TECH]';
        $raw   = $notes === null ? '' : (string) $notes;
        $tech  = [
            'fork'           => '',
            'attachment'     => '',
            'mast'           => '',
            'ban'            => '',
            'battery'        => '',
            'charger'        => '',
            'valve'          => '',
            'roda'           => '',
            'unit_condition' => '',
        ];
        $p0 = strpos($raw, $start);
        $p1 = strpos($raw, $end);
        if ($p0 === false || $p1 === false || $p1 < $p0) {
            return ['user_notes' => trim($raw), 'tech' => $tech];
        }
        $before = rtrim(substr($raw, 0, $p0));
        $after  = ltrim(substr($raw, $p1 + strlen($end)));
        $parts  = [];
        if ($before !== '') {
            $parts[] = $before;
        }
        if ($after !== '') {
            $parts[] = $after;
        }
        $userNotes = trim(implode("\n\n", $parts));
        $inner     = trim(substr($raw, $p0 + strlen($start), $p1 - $p0 - strlen($start)));
        foreach (preg_split('/\R/', $inner) ?: [] as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^([a-z_]+):\s*(.*)$/', $line, $m) && isset($tech[$m[1]])) {
                $tech[$m[1]] = $m[2];
            }
        }

        $foldIntoNotes = ['battery' => 'Baterai', 'charger' => 'Charger', 'roda' => 'Roda'];
        $appendLines   = [];
        foreach ($foldIntoNotes as $key => $label) {
            $v = trim((string) ($tech[$key] ?? ''));
            if ($v !== '') {
                $appendLines[] = $label . ': ' . $v;
            }
            $tech[$key] = '';
        }
        if ($appendLines !== []) {
            $userNotes = $userNotes !== ''
                ? ($userNotes . "\n\n" . implode("\n", $appendLines))
                : implode("\n", $appendLines);
            $userNotes = trim($userNotes);
        }

        return ['user_notes' => $userNotes, 'tech' => $tech];
    }
}

if (! function_exists('spk_print_pick_detail')) {
    /** Prefer label resolved from master (JOIN), else free-text from notes tech block. */
    function spk_print_pick_detail(string $fromMaster, string $fromTech): string
    {
        $a = trim($fromMaster);

        return $a !== '' ? $a : trim($fromTech);
    }
}

if (! function_exists('optima_print_fork_or_attachment_mode')) {
    /**
     * Fork and attachment are mutually exclusive on quotation / print.
     * Prefer FKs on the spec row, then parsed tech lines, then resolved display strings.
     *
     * @param array<string,mixed>  $specRow
     * @param array<string,string> $tech
     *
     * @return 'fork'|'attachment'|'none'
     */
    function optima_print_fork_or_attachment_mode(array $specRow, array $tech, string $forkDisplay, string $attachmentDisplay): string
    {
        $hasForkId   = ! empty($specRow['fork_id']);
        $hasAttachId = ! empty($specRow['attachment_id']);
        if ($hasForkId && ! $hasAttachId) {
            return 'fork';
        }
        if ($hasAttachId && ! $hasForkId) {
            return 'attachment';
        }
        if ($hasForkId && $hasAttachId) {
            return 'fork';
        }

        $forkT   = trim((string) ($tech['fork'] ?? ''));
        $attachT = trim((string) ($tech['attachment'] ?? ''));
        if ($forkT !== '' && $attachT === '') {
            return 'fork';
        }
        if ($attachT !== '' && $forkT === '') {
            return 'attachment';
        }
        if ($forkT !== '' && $attachT !== '') {
            return 'fork';
        }

        $fd = trim($forkDisplay);
        $ad = trim($attachmentDisplay);
        if ($fd !== '' && $ad === '') {
            return 'fork';
        }
        if ($ad !== '' && $fd === '') {
            return 'attachment';
        }
        if ($fd !== '' && $ad !== '') {
            return 'fork';
        }

        return 'none';
    }
}
