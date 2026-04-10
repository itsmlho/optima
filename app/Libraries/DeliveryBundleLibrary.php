<?php

namespace App\Libraries;

/**
 * Pasangan baris po_delivery_items (unit + charger/baterai/attachment) untuk UI bundle.
 * Dipakai Purchasing (Assign SN) dan Warehouse (verifikasi PL).
 */
class DeliveryBundleLibrary
{
    /**
     * @param object|array<string, mixed> $item
     *
     * @return array<string, mixed>
     */
    public static function formatDeliveryItemRow($item): array
    {
        $row = is_array($item) ? (object) $item : $item;

        return [
            'id_delivery_item' => (int) $row->id_delivery_item,
            'item_type' => $row->item_type,
            'id_po_unit' => isset($row->id_po_unit) && $row->id_po_unit !== null ? (int) $row->id_po_unit : null,
            'id_po_attachment' => isset($row->id_po_attachment) && $row->id_po_attachment !== null ? (int) $row->id_po_attachment : null,
            'qty' => (int) ($row->qty ?? 1),
            'item_name' => $row->item_name,
            'item_description' => $row->item_description ?? '',
            'sn_mast_po' => $row->sn_mast_po ?? null,
            'sn_mesin_po' => $row->sn_mesin_po ?? null,
            'serial_number' => $row->serial_number ?? null,
        ];
    }

    /**
     * @param array<int, object> $pool
     * @param array<int, int>   $usedIds
     */
    public static function pickPairedAccessoryLine(array $pool, array &$usedIds, ?int $unitFk, callable $lineFkGetter): ?object
    {
        if ($pool === []) {
            return null;
        }

        // Hanya pasangkan jika FK master unit cocok dengan baris delivery (baris PI terpisah
        // tanpa link master tidak boleh "nempel" ke unit pertama).
        if ($unitFk === null || $unitFk <= 0) {
            return null;
        }

        foreach ($pool as $row) {
            $id = (int) $row->id_delivery_item;
            if (in_array($id, $usedIds, true)) {
                continue;
            }
            $lineFk = (int) $lineFkGetter($row);
            if ($lineFk > 0 && $lineFk === $unitFk) {
                $usedIds[] = $id;

                return $row;
            }
        }

        return null;
    }

    /**
     * @param array<int, object> $deliveryItems
     *
     * @return array{0: list<array<string, mixed>>, 1: array<string, list<array<string, mixed>>>}
     */
    public static function pairDeliveryItemsIntoBundles(array $deliveryItems): array
    {
        usort($deliveryItems, static fn ($a, $b) => (int) $a->id_delivery_item <=> (int) $b->id_delivery_item);

        $units = [];
        $batteries = [];
        $chargers = [];
        $attachments = [];

        foreach ($deliveryItems as $row) {
            $t = strtolower((string) ($row->item_type ?? ''));
            if ($t === 'unit') {
                $units[] = $row;
            } elseif ($t === 'battery') {
                $batteries[] = $row;
            } elseif ($t === 'charger') {
                $chargers[] = $row;
            } elseif ($t === 'attachment') {
                $attachments[] = $row;
            }
        }

        $usedBat = [];
        $usedChg = [];
        $usedAtt = [];

        $bundles = [];
        foreach ($units as $u) {
            $ub = isset($u->fk_unit_baterai) ? (int) $u->fk_unit_baterai : 0;
            $uc = isset($u->fk_unit_charger) ? (int) $u->fk_unit_charger : 0;
            $ua = isset($u->fk_unit_attachment) ? (int) $u->fk_unit_attachment : 0;

            $chgRow = self::pickPairedAccessoryLine(
                $chargers,
                $usedChg,
                $uc > 0 ? $uc : null,
                static fn ($r) => (int) ($r->line_pa_charger ?? 0)
            );
            $batRow = self::pickPairedAccessoryLine(
                $batteries,
                $usedBat,
                $ub > 0 ? $ub : null,
                static fn ($r) => (int) ($r->line_pa_baterai ?? 0)
            );
            $attRow = self::pickPairedAccessoryLine(
                $attachments,
                $usedAtt,
                $ua > 0 ? $ua : null,
                static fn ($r) => (int) ($r->line_pa_attachment ?? 0)
            );

            $bundles[] = [
                'unit' => self::formatDeliveryItemRow($u),
                'charger' => $chgRow ? self::formatDeliveryItemRow($chgRow) : null,
                'battery' => $batRow ? self::formatDeliveryItemRow($batRow) : null,
                'attachment' => $attRow ? self::formatDeliveryItemRow($attRow) : null,
            ];
        }

        $orphans = [
            'batteries' => [],
            'chargers' => [],
            'attachments' => [],
        ];

        foreach ($batteries as $row) {
            if (! in_array((int) $row->id_delivery_item, $usedBat, true)) {
                $orphans['batteries'][] = self::formatDeliveryItemRow($row);
            }
        }
        foreach ($chargers as $row) {
            if (! in_array((int) $row->id_delivery_item, $usedChg, true)) {
                $orphans['chargers'][] = self::formatDeliveryItemRow($row);
            }
        }
        foreach ($attachments as $row) {
            if (! in_array((int) $row->id_delivery_item, $usedAtt, true)) {
                $orphans['attachments'][] = self::formatDeliveryItemRow($row);
            }
        }

        return [$bundles, $orphans];
    }
}
