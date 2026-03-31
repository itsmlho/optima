<?php

namespace App\Services;

class MasterDataLookupService
{
    protected $db;

    public function __construct($db = null)
    {
        $this->db = $db ?: \Config\Database::connect();
    }

    public function attachmentOptions(string $q = '', int $limit = 100): array
    {
        $qb = $this->db->table('attachment')
            ->select('id_attachment as id, tipe, merk, model')
            ->orderBy('tipe, merk, model')
            ->limit($limit);

        if ($q !== '') {
            $qb->groupStart()
                ->like('tipe', $q)
                ->orLike('merk', $q)
                ->orLike('model', $q)
                ->groupEnd();
        }

        $rows = $qb->get()->getResultArray();
        return array_map(static function ($r) {
            $label = trim(($r['tipe'] ?? '') . ' - ' . ($r['merk'] ?? '') . ' ' . ($r['model'] ?? ''));
            return [
                'id' => (int) ($r['id'] ?? 0),
                'text' => trim($label, ' -'),
                'tipe' => $r['tipe'] ?? null,
                'merk' => $r['merk'] ?? null,
                'model' => $r['model'] ?? null,
            ];
        }, $rows);
    }

    public function batteryOptions(string $q = '', int $limit = 100): array
    {
        $qb = $this->db->table('baterai')
            ->select('id, merk_baterai, tipe_baterai, jenis_baterai')
            ->orderBy('merk_baterai, tipe_baterai')
            ->limit($limit);

        if ($q !== '') {
            $qb->groupStart()
                ->like('merk_baterai', $q)
                ->orLike('tipe_baterai', $q)
                ->orLike('jenis_baterai', $q)
                ->groupEnd();
        }

        $rows = $qb->get()->getResultArray();
        return array_map(static function ($r) {
            $label = trim(($r['merk_baterai'] ?? '') . ' - ' . ($r['tipe_baterai'] ?? ''));
            if (!empty($r['jenis_baterai'])) {
                $label .= ' (' . $r['jenis_baterai'] . ')';
            }
            return [
                'id' => (int) ($r['id'] ?? 0),
                'text' => $label,
                'merk_baterai' => $r['merk_baterai'] ?? null,
                'tipe_baterai' => $r['tipe_baterai'] ?? null,
                'jenis_baterai' => $r['jenis_baterai'] ?? null,
            ];
        }, $rows);
    }

    public function chargerOptions(string $q = '', int $limit = 100): array
    {
        $qb = $this->db->table('charger')
            ->select('id_charger as id, merk_charger, tipe_charger')
            ->orderBy('merk_charger, tipe_charger')
            ->limit($limit);

        if ($q !== '') {
            $qb->groupStart()
                ->like('merk_charger', $q)
                ->orLike('tipe_charger', $q)
                ->groupEnd();
        }

        $rows = $qb->get()->getResultArray();
        return array_map(static function ($r) {
            $label = trim(($r['merk_charger'] ?? '') . ' - ' . ($r['tipe_charger'] ?? ''));
            return [
                'id' => (int) ($r['id'] ?? 0),
                'text' => trim($label, ' -'),
                'merk_charger' => $r['merk_charger'] ?? null,
                'tipe_charger' => $r['tipe_charger'] ?? null,
            ];
        }, $rows);
    }

    public function forkOptions(string $q = '', int $limit = 100): array
    {
        $qb = $this->db->table('fork')
            ->select('id, name, length_mm, width_mm, thickness_mm, fork_class, capacity_kg')
            ->orderBy('name')
            ->limit($limit);

        if ($q !== '') {
            $qb->groupStart()
                ->like('name', $q)
                ->orLike('fork_class', $q)
                ->groupEnd();
        }

        $rows = $qb->get()->getResultArray();
        return array_map(static function ($r) {
            $size = trim(implode('x', array_filter([
                $r['length_mm'] ?? null,
                $r['width_mm'] ?? null,
                $r['thickness_mm'] ?? null,
            ])));
            $text = $r['name'] ?? '';
            if ($size !== '') {
                $text .= ' [' . $size . ' mm]';
            }
            if (!empty($r['fork_class'])) {
                $text .= ' - ' . $r['fork_class'];
            }
            return [
                'id' => (int)($r['id'] ?? 0),
                'text' => trim($text),
                'name' => $r['name'] ?? null,
                'length_mm' => $r['length_mm'] ?? null,
                'width_mm' => $r['width_mm'] ?? null,
                'thickness_mm' => $r['thickness_mm'] ?? null,
                'fork_class' => $r['fork_class'] ?? null,
                'capacity_kg' => $r['capacity_kg'] ?? null,
            ];
        }, $rows);
    }
}

