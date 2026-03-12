<?php
/**
 * Unit Component Formatter untuk SPK Print
 *
 * File ini berisi fungsi-fungsi yang digunakan untuk mengambil dan memformat
 * data komponen unit (baterai, charger, attachment) dengan cara yang konsisten.
 */

namespace App\Helpers;

class UnitComponentFormatter
{
    protected $db;

    public function __construct()
    {
        $this->db = \Config\Database::connect();
    }

    /**
     * Mengembalikan komponen unit dengan format yang lebih aman dan konsisten untuk print
     */
    public function getComponentsForPrint($unitId)
    {
        // Array komponen dasar
        $components = [
            "battery" => [
                "sn_baterai" => "",
                "model_display" => "",
                "formatted" => ""
            ],
            "charger" => [
                "sn_charger" => "",
                "model_display" => "",
                "formatted" => ""
            ],
            "attachment" => [
                "sn_attachment" => "",
                "model_display" => "",
                "formatted" => ""
            ]
        ];
        
        // Ambil data baterai
        $battery = $this->db->table("inventory_batteries ib")
            ->select("ib.id, ib.battery_type_id, ib.serial_number as sn_baterai, b.merk_baterai, b.tipe_baterai, b.jenis_baterai")
            ->join("baterai b", "ib.battery_type_id = b.id", "left")
            ->where("ib.inventory_unit_id", $unitId)
            ->where("ib.battery_type_id IS NOT NULL")
            ->whereIn("ib.status", ['AVAILABLE', 'IN_USE', 'SPARE'])
            ->get()->getRowArray();
        
        if ($battery) {
            $components["battery"]["sn_baterai"] = isset($battery["sn_baterai"]) ? $battery["sn_baterai"] : "";
            
            $model = "";
            if (isset($battery["tipe_baterai"]) && !empty($battery["tipe_baterai"])) {
                $model = $battery["tipe_baterai"];
            } else if (isset($battery["merk_baterai"]) && !empty($battery["merk_baterai"])) {
                $model = $battery["merk_baterai"];
            }
            $components["battery"]["model_display"] = $model;
            
            // Format: Model (SN) atau hanya Model jika SN kosong
            if (!empty($model)) {
                if (isset($battery["sn_baterai"]) && !empty($battery["sn_baterai"])) {
                    $components["battery"]["formatted"] = $model . " (" . $battery["sn_baterai"] . ")";
                } else {
                    $components["battery"]["formatted"] = $model;
                }
            } else if (isset($battery["sn_baterai"]) && !empty($battery["sn_baterai"])) {
                $components["battery"]["formatted"] = "Baterai (" . $battery["sn_baterai"] . ")";
            }
        }
        
        // Ambil data charger
        $charger = $this->db->table("inventory_chargers ic")
            ->select("ic.id, ic.charger_type_id, ic.serial_number as sn_charger, c.merk_charger, c.tipe_charger")
            ->join("charger c", "ic.charger_type_id = c.id_charger", "left")
            ->where("ic.inventory_unit_id", $unitId)
            ->where("ic.charger_type_id IS NOT NULL")
            ->whereIn("ic.status", ['AVAILABLE', 'IN_USE', 'SPARE'])
            ->get()->getRowArray();
        
        if ($charger) {
            $components["charger"]["sn_charger"] = isset($charger["sn_charger"]) ? $charger["sn_charger"] : "";
            
            $model = "";
            if (isset($charger["tipe_charger"]) && !empty($charger["tipe_charger"])) {
                $model = $charger["tipe_charger"];
            } else if (isset($charger["merk_charger"]) && !empty($charger["merk_charger"])) {
                $model = $charger["merk_charger"];
            }
            $components["charger"]["model_display"] = $model;
            
            // Format: Model (SN) atau hanya Model jika SN kosong
            if (!empty($model)) {
                if (isset($charger["sn_charger"]) && !empty($charger["sn_charger"])) {
                    $components["charger"]["formatted"] = $model . " (" . $charger["sn_charger"] . ")";
                } else {
                    $components["charger"]["formatted"] = $model;
                }
            } else if (isset($charger["sn_charger"]) && !empty($charger["sn_charger"])) {
                $components["charger"]["formatted"] = "Charger (" . $charger["sn_charger"] . ")";
            }
        }
        
        // Ambil data attachment
        $attachment = $this->db->table("inventory_attachments ia")
            ->select("ia.id, ia.attachment_type_id, ia.serial_number as sn_attachment, a.tipe, a.merk, a.model")
            ->join("attachment a", "ia.attachment_type_id = a.id_attachment", "left")
            ->where("ia.inventory_unit_id", $unitId)
            ->where("ia.attachment_type_id IS NOT NULL")
            ->whereIn("ia.status", ['IN_USE', 'SPARE'])
            ->get()->getRowArray();
        
        if ($attachment) {
            $components["attachment"]["sn_attachment"] = isset($attachment["sn_attachment"]) ? $attachment["sn_attachment"] : "";
            
            $displayParts = [];
            if (isset($attachment["tipe"]) && !empty($attachment["tipe"])) {
                $displayParts[] = $attachment["tipe"];
            }
            if (isset($attachment["merk"]) && !empty($attachment["merk"])) {
                $displayParts[] = $attachment["merk"];
            }
            if (isset($attachment["model"]) && !empty($attachment["model"])) {
                $displayParts[] = $attachment["model"];
            }
            
            $modelDisplay = implode(" ", $displayParts);
            $components["attachment"]["model_display"] = $modelDisplay;
            
            // Format: Model (SN) atau hanya Model jika SN kosong
            if (!empty($modelDisplay)) {
                if (isset($attachment["sn_attachment"]) && !empty($attachment["sn_attachment"])) {
                    $components["attachment"]["formatted"] = $modelDisplay . " (" . $attachment["sn_attachment"] . ")";
                } else {
                    $components["attachment"]["formatted"] = $modelDisplay;
                }
            } else if (isset($attachment["sn_attachment"]) && !empty($attachment["sn_attachment"])) {
                $components["attachment"]["formatted"] = "Attachment (" . $attachment["sn_attachment"] . ")";
            }
        }
        
        // Mapping untuk memudahkan penggunaan di template print
        $result = [
            "battery_sn" => $components["battery"]["sn_baterai"],
            "battery_model" => $components["battery"]["model_display"],
            "battery_display" => $components["battery"]["formatted"],
            "charger_sn" => $components["charger"]["sn_charger"],
            "charger_model" => $components["charger"]["model_display"],
            "charger_display" => $components["charger"]["formatted"],
            "attachment_sn" => $components["attachment"]["sn_attachment"],
            "attachment_model" => $components["attachment"]["model_display"],
            "attachment_display" => $components["attachment"]["formatted"]
        ];
        
        return $result;
    }
}
