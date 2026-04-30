<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Fork assignment/removal logs ke component_audit_log memakai component_type = FORK.
 * Schema lama hanya ENUM('ATTACHMENT','BATTERY','CHARGER') → INSERT gagal → transaksi rollback.
 */
class AddForkToComponentAuditLogComponentType extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('component_audit_log')) {
            return;
        }

        $this->db->query("
            ALTER TABLE component_audit_log
            MODIFY COLUMN component_type
            ENUM('ATTACHMENT','BATTERY','CHARGER','FORK')
            NOT NULL
        ");
    }

    public function down()
    {
        if (!$this->db->tableExists('component_audit_log')) {
            return;
        }

        $this->db->query("
            UPDATE component_audit_log
            SET component_type = 'ATTACHMENT'
            WHERE component_type = 'FORK'
        ");

        $this->db->query("
            ALTER TABLE component_audit_log
            MODIFY COLUMN component_type
            ENUM('ATTACHMENT','BATTERY','CHARGER')
            NOT NULL
        ");
    }
}
