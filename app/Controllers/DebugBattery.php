<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class DebugBattery extends BaseController
{
    public function checkBatteryData()
    {
        $db = \Config\Database::connect();
        $results = [];
        
        // 1. Battery statistics
        $query1 = $db->query("
            SELECT 
                COUNT(*) as total_battery,
                SUM(CASE WHEN baterai_id IS NULL THEN 1 ELSE 0 END) as null_baterai_id,
                SUM(CASE WHEN baterai_id IS NOT NULL THEN 1 ELSE 0 END) as has_baterai_id,
                SUM(CASE WHEN sn_baterai IS NULL THEN 1 ELSE 0 END) as null_sn_baterai,
                SUM(CASE WHEN sn_baterai IS NOT NULL THEN 1 ELSE 0 END) as has_sn_baterai
            FROM inventory_attachment 
            WHERE LOWER(tipe_item) = 'battery'
        ");
        $results['battery_stats'] = $query1->getRow();
        
        // 2. Distinct tipe_item values
        $query2 = $db->query("
            SELECT tipe_item, COUNT(*) as jumlah
            FROM inventory_attachment
            GROUP BY tipe_item
        ");
        $results['tipe_item_values'] = $query2->getResultArray();
        
        // 3. Sample battery records with JOIN
        $query3 = $db->query("
            SELECT 
                ia.id_inventory_attachment,
                ia.tipe_item,
                ia.baterai_id,
                ia.sn_baterai,
                ia.attachment_id,
                b.id as baterai_table_id,
                b.merk_baterai,
                b.tipe_baterai,
                b.jenis_baterai,
                a.merk as attachment_merk,
                a.tipe as attachment_tipe,
                a.model as attachment_model
            FROM inventory_attachment ia
            LEFT JOIN baterai b ON ia.baterai_id = b.id
            LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
            WHERE LOWER(ia.tipe_item) = 'battery'
            LIMIT 10
        ");
        $results['sample_battery_records'] = $query3->getResultArray();
        
        // 4. All jenis_baterai values
        $query4 = $db->query("
            SELECT 
                jenis_baterai,
                COUNT(*) as jumlah,
                GROUP_CONCAT(DISTINCT merk_baterai SEPARATOR ', ') as sample_merk
            FROM baterai
            WHERE jenis_baterai IS NOT NULL
            GROUP BY jenis_baterai
            ORDER BY COUNT(*) DESC
        ");
        $results['jenis_baterai_list'] = $query4->getResultArray();
        
        // 5. Attachment table data for battery
        $query5 = $db->query("
            SELECT 
                a.model,
                a.tipe,
                COUNT(*) as jumlah
            FROM inventory_attachment ia
            LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
            WHERE LOWER(ia.tipe_item) = 'battery'
            AND ia.attachment_id IS NOT NULL
            GROUP BY a.model, a.tipe
            LIMIT 20
        ");
        $results['attachment_battery_models'] = $query5->getResultArray();
        
        // 6. Test query: LITHIUM
        $query6 = $db->query("
            SELECT COUNT(*) as count
            FROM inventory_attachment ia
            LEFT JOIN baterai b ON ia.baterai_id = b.id
            WHERE LOWER(ia.tipe_item) = 'battery'
            AND ia.baterai_id IS NOT NULL 
            AND b.jenis_baterai IS NOT NULL 
            AND UPPER(b.jenis_baterai) LIKE '%LITHIUM%'
        ");
        $results['lithium_count_primary'] = $query6->getRow()->count;
        
        // 7. Test query: LEAD
        $query7 = $db->query("
            SELECT COUNT(*) as count
            FROM inventory_attachment ia
            LEFT JOIN baterai b ON ia.baterai_id = b.id
            WHERE LOWER(ia.tipe_item) = 'battery'
            AND ia.baterai_id IS NOT NULL 
            AND b.jenis_baterai IS NOT NULL 
            AND UPPER(b.jenis_baterai) LIKE '%LEAD%'
        ");
        $results['lead_count_primary'] = $query7->getRow()->count;
        
        // 8. Test query: LITHIUM fallback (from attachment table)
        $query8 = $db->query("
            SELECT COUNT(*) as count
            FROM inventory_attachment ia
            LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
            WHERE LOWER(ia.tipe_item) = 'battery'
            AND (UPPER(a.model) LIKE '%LITHIUM%' OR UPPER(a.tipe) LIKE '%LITHIUM%')
        ");
        $results['lithium_count_fallback'] = $query8->getRow()->count;
        
        // 9. Test query: LEAD fallback
        $query9 = $db->query("
            SELECT COUNT(*) as count
            FROM inventory_attachment ia
            LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
            WHERE LOWER(ia.tipe_item) = 'battery'
            AND (UPPER(a.model) LIKE '%LEAD%' OR UPPER(a.tipe) LIKE '%LEAD%')
        ");
        $results['lead_count_fallback'] = $query9->getRow()->count;
        
        // 10. Test combined query (primary OR fallback) - LITHIUM
        $query10 = $db->query("
            SELECT COUNT(*) as count
            FROM inventory_attachment ia
            LEFT JOIN baterai b ON ia.baterai_id = b.id
            LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
            WHERE LOWER(ia.tipe_item) = 'battery'
            AND (
                (ia.baterai_id IS NOT NULL AND b.jenis_baterai IS NOT NULL AND UPPER(b.jenis_baterai) LIKE '%LITHIUM%')
                OR (UPPER(a.model) LIKE '%LITHIUM%' OR UPPER(a.tipe) LIKE '%LITHIUM%')
            )
        ");
        $results['lithium_count_combined'] = $query10->getRow()->count;
        
        // 11. Test combined query - LEAD
        $query11 = $db->query("
            SELECT COUNT(*) as count
            FROM inventory_attachment ia
            LEFT JOIN baterai b ON ia.baterai_id = b.id
            LEFT JOIN attachment a ON ia.attachment_id = a.id_attachment
            WHERE LOWER(ia.tipe_item) = 'battery'
            AND (
                (ia.baterai_id IS NOT NULL AND b.jenis_baterai IS NOT NULL AND UPPER(b.jenis_baterai) LIKE '%LEAD%')
                OR (UPPER(a.model) LIKE '%LEAD%' OR UPPER(a.tipe) LIKE '%LEAD%')
            )
        ");
        $results['lead_count_combined'] = $query11->getRow()->count;
        
        return $this->response->setJSON($results);
    }
}
