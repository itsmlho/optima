<?php
/**
 * Fungsi untuk memperbaiki pencetakan komponen unit pada SPK Print
 * Gunakan fungsi ini untuk menggantikan fungsi getUnitComponentsForPrint di Service.php
 * 
 * CARA PENGGUNAAN:
 * 1. Salin fungsi getUnitComponentsForPrint berikut ke dalam class Service
 * 2. Gunakan dengan cara memanggil $unitComponents = $this->getUnitComponentsForPrint($unitId)
 * 3. Akses dengan format: $unitComponents['battery_display'], $unitComponents['charger_display'], dll
 */

/**
 * Fungsi yang direkomendasikan untuk digunakan di spkPrint dalam Service.php
 */
private function getUnitComponentsForPrint($unitId)
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
    $battery = $this->db->table("inventory_attachment ia")
        ->select("ia.id_inventory_attachment, ia.baterai_id, ia.sn_baterai, b.merk_baterai, b.tipe_baterai, b.jenis_baterai")
        ->join("baterai b", "ia.baterai_id = b.id", "left")
        ->where("ia.id_inventory_unit", $unitId)
        ->where("ia.tipe_item", "battery")
        ->whereIn("ia.status_unit", [7, 8]) // Available or In use
        ->get()->getRowArray();
    
    if ($battery) {
        $components["battery"]["sn_baterai"] = $battery["sn_baterai"] ?? "";
        
        $model = "";
        if (!empty($battery["tipe_baterai"])) {
            $model = $battery["tipe_baterai"];
        } else if (!empty($battery["merk_baterai"])) {
            $model = $battery["merk_baterai"];
        }
        $components["battery"]["model_display"] = $model;
        
        // Format: Model (SN) atau hanya Model jika SN kosong
        if (!empty($model)) {
            if (!empty($battery["sn_baterai"])) {
                $components["battery"]["formatted"] = $model . " (" . $battery["sn_baterai"] . ")";
            } else {
                $components["battery"]["formatted"] = $model;
            }
        } else if (!empty($battery["sn_baterai"])) {
            $components["battery"]["formatted"] = "Baterai (" . $battery["sn_baterai"] . ")";
        }
    }
    
    // Ambil data charger
    $charger = $this->db->table("inventory_attachment ia")
        ->select("ia.id_inventory_attachment, ia.charger_id, ia.sn_charger, c.merk_charger, c.tipe_charger")
        ->join("charger c", "ia.charger_id = c.id_charger", "left")
        ->where("ia.id_inventory_unit", $unitId)
        ->where("ia.tipe_item", "charger")
        ->whereIn("ia.status_unit", [7, 8]) // Available or In use
        ->get()->getRowArray();
    
    if ($charger) {
        $components["charger"]["sn_charger"] = $charger["sn_charger"] ?? "";
        
        $model = "";
        if (!empty($charger["tipe_charger"])) {
            $model = $charger["tipe_charger"];
        } else if (!empty($charger["merk_charger"])) {
            $model = $charger["merk_charger"];
        }
        $components["charger"]["model_display"] = $model;
        
        // Format: Model (SN) atau hanya Model jika SN kosong
        if (!empty($model)) {
            if (!empty($charger["sn_charger"])) {
                $components["charger"]["formatted"] = $model . " (" . $charger["sn_charger"] . ")";
            } else {
                $components["charger"]["formatted"] = $model;
            }
        } else if (!empty($charger["sn_charger"])) {
            $components["charger"]["formatted"] = "Charger (" . $charger["sn_charger"] . ")";
        }
    }
    
    // Ambil data attachment
    $attachment = $this->db->table("inventory_attachment ia")
        ->select("ia.id_inventory_attachment, ia.attachment_id, ia.sn_attachment, a.tipe, a.merk, a.model")
        ->join("attachment a", "ia.attachment_id = a.id_attachment", "left")
        ->where("ia.id_inventory_unit", $unitId)
        ->where("ia.tipe_item", "attachment")
        ->where("ia.status_unit", 8) // In use
        ->get()->getRowArray();
    
    if ($attachment) {
        $components["attachment"]["sn_attachment"] = $attachment["sn_attachment"] ?? "";
        
        $displayParts = [];
        if (!empty($attachment["tipe"])) {
            $displayParts[] = $attachment["tipe"];
        }
        if (!empty($attachment["merk"])) {
            $displayParts[] = $attachment["merk"];
        }
        if (!empty($attachment["model"])) {
            $displayParts[] = $attachment["model"];
        }
        
        $modelDisplay = implode(" ", $displayParts);
        $components["attachment"]["model_display"] = $modelDisplay;
        
        // Format: Model (SN) atau hanya Model jika SN kosong
        if (!empty($modelDisplay)) {
            if (!empty($attachment["sn_attachment"])) {
                $components["attachment"]["formatted"] = $modelDisplay . " (" . $attachment["sn_attachment"] . ")";
            } else {
                $components["attachment"]["formatted"] = $modelDisplay;
            }
        } else if (!empty($attachment["sn_attachment"])) {
            $components["attachment"]["formatted"] = "Attachment (" . $attachment["sn_attachment"] . ")";
        }
    }
    
    return $components;
}

/**
 * Bagian yang perlu diubah di spkPrint untuk menggunakan getUnitComponentsForPrint
 */
if ($u) {
    // Get unit components from inventory_attachment (single source of truth for serial numbers)
    $unitComponents = $this->getUnitComponentsForPrint($u["id_inventory_unit"]);
    
    // Prepare label for unit
    $noUnit = isset($u["no_unit"]) ? $u["no_unit"] : "-";
    $merkUnit = isset($u["merk_unit"]) ? $u["merk_unit"] : "-";
    $modelUnit = isset($u["model_unit"]) ? $u["model_unit"] : "";
    $lokasiUnit = isset($u["lokasi_unit"]) ? $u["lokasi_unit"] : "-";
    
    $label = trim($noUnit . " - " . $merkUnit . " " . $modelUnit . " @ " . $lokasiUnit);
    
    // Get valve, mast, roda, ban data
    $valveId = isset($u["valve_id"]) ? $u["valve_id"] : null;
    $mastId = isset($u["model_mast_id"]) ? $u["model_mast_id"] : null;
    $rodaId = isset($u["roda_id"]) ? $u["roda_id"] : null;
    $banId = isset($u["ban_id"]) ? $u["ban_id"] : null;
    
    $valve = $this->db->table("valve")->select("jumlah_valve")->where("id_valve", $valveId)->get()->getRowArray();
    $mast = $this->db->table("tipe_mast")->select("tipe_mast")->where("id_mast", $mastId)->get()->getRowArray();
    $roda = $this->db->table("jenis_roda")->select("tipe_roda")->where("id_roda", $rodaId)->get()->getRowArray();
    $ban = $this->db->table("tipe_ban")->select("tipe_ban")->where("id_ban", $banId)->get()->getRowArray();
    
    $valveValue = "";
    if (!empty($valve) && isset($valve["jumlah_valve"])) {
        $valveValue = $valve["jumlah_valve"];
    }
    
    $mastValue = "";
    if (!empty($mast) && isset($mast["tipe_mast"])) {
        $mastValue = $mast["tipe_mast"];
    }
    
    $rodaValue = "";
    if (!empty($roda) && isset($roda["tipe_roda"])) {
        $rodaValue = $roda["tipe_roda"];
    }
    
    $banValue = "";
    if (!empty($ban) && isset($ban["tipe_ban"])) {
        $banValue = $ban["tipe_ban"];
    }
    
    // Format: Model (SN) atau hanya Model jika SN kosong
    $snMast = isset($u["sn_mast"]) ? $u["sn_mast"] : null;
    $snMesin = isset($u["sn_mesin"]) ? $u["sn_mesin"] : null;
    $mastModel = isset($u["mast_model"]) ? $u["mast_model"] : "Mast";
    $mesinModel = isset($u["mesin_model"]) ? $u["mesin_model"] : "Mesin";
    
    $snMastFormatted = $mastModel;
    if (!empty($snMast)) {
        $snMastFormatted = $mastModel . " (" . $snMast . ")";
    }
    
    $snMesinFormatted = $mesinModel;
    if (!empty($snMesin)) {
        $snMesinFormatted = $mesinModel . " (" . $snMesin . ")";
    }
    
    // Create the enriched unit data
    $enriched["selected"]["unit"] = array(
        "id" => (int)$u["id_inventory_unit"],
        "label" => $label,
        "no_unit" => isset($u["no_unit"]) ? $u["no_unit"] : null,
        "serial_number" => isset($u["serial_number"]) ? $u["serial_number"] : null,
        "tahun_unit" => isset($u["tahun_unit"]) ? $u["tahun_unit"] : null,
        "merk_unit" => isset($u["merk_unit"]) ? $u["merk_unit"] : null,
        "model_unit" => isset($u["model_unit"]) ? $u["model_unit"] : null,
        "tipe_jenis" => isset($u["tipe_jenis"]) ? $u["tipe_jenis"] : null,
        "jenis_unit" => isset($u["jenis_unit"]) ? $u["jenis_unit"] : null,
        "lokasi_unit" => isset($u["lokasi_unit"]) ? $u["lokasi_unit"] : null,
        "kapasitas_name" => isset($u["kapasitas_name"]) ? $u["kapasitas_name"] : null,
        "departemen_name" => isset($u["departemen_name"]) ? $u["departemen_name"] : null,
        "valve" => $valveValue,
        "mast" => $mastValue,
        "roda" => $rodaValue,
        "ban" => $banValue,
        "sn_mast" => $snMast,
        "sn_mesin" => $snMesin,
        "sn_baterai" => $unitComponents["battery"]["sn_baterai"],
        "sn_charger" => $unitComponents["charger"]["sn_charger"],
        "baterai_model" => $unitComponents["battery"]["model_display"],
        "charger_model" => $unitComponents["charger"]["model_display"],
        "sn_mast_formatted" => $snMastFormatted,
        "sn_mesin_formatted" => $snMesinFormatted,
        "sn_baterai_formatted" => $unitComponents["battery"]["formatted"],
        "sn_charger_formatted" => $unitComponents["charger"]["formatted"],
        "attachment_display" => $unitComponents["attachment"]["formatted"]
    );
}
