<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Migration: Add FORK and SPAREPART to unit_movements.component_type ENUM
 *
 * Fixes: component_type ENUM missing FORK and SPAREPART values.
 * The FORK type references inventory_forks table (join via fork_id -> fork master).
 * The SPAREPART type references inventory_spareparts table (join via sparepart_id -> sparepart master).
 */
class AddForkSparepartToMovements extends Migration
{
    public function up()
    {
        // Extend component_type ENUM to include FORK and SPAREPART
        $this->db->query("
            ALTER TABLE unit_movements
            MODIFY COLUMN component_type
            ENUM('FORKLIFT','ATTACHMENT','CHARGER','BATTERY','FORK','SPAREPART')
            NULL DEFAULT 'FORKLIFT'
        ");
    }

    public function down()
    {
        // Revert ENUM - convert any FORK/SPAREPART rows to NULL first
        $this->db->query("UPDATE unit_movements SET component_type = NULL WHERE component_type IN ('FORK','SPAREPART')");
        $this->db->query("
            ALTER TABLE unit_movements
            MODIFY COLUMN component_type
            ENUM('FORKLIFT','ATTACHMENT','CHARGER','BATTERY')
            NULL DEFAULT 'FORKLIFT'
        ");
    }
}
