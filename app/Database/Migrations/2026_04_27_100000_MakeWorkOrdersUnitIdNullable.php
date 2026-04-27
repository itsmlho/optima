<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakeWorkOrdersUnitIdNullable extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('work_orders')) {
            return;
        }

        // Drop existing FK so we can redefine it with SET NULL
        try {
            $this->forge->dropForeignKey('work_orders', 'fk_wo_unit');
        } catch (\Throwable $e) {
            // FK may not exist or have a different name — proceed
            log_message('info', '[Migration] dropForeignKey fk_wo_unit skipped: ' . $e->getMessage());
        }

        // Make unit_id nullable (INT UNSIGNED to match inventory_unit PK)
        $this->forge->modifyColumn('work_orders', [
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
                'default'    => null,
            ],
        ]);

        // Re-add FK with SET NULL so deleted units don't orphan WO records
        $this->forge->addForeignKey('unit_id', 'inventory_unit', 'id_inventory_unit', '', 'SET NULL', 'fk_wo_unit');
        $this->forge->processIndexes('work_orders');
    }

    public function down(): void
    {
        if (! $this->db->tableExists('work_orders')) {
            return;
        }

        try {
            $this->forge->dropForeignKey('work_orders', 'fk_wo_unit');
        } catch (\Throwable $e) {
            log_message('info', '[Migration] down: dropForeignKey fk_wo_unit skipped: ' . $e->getMessage());
        }

        // Restore NOT NULL (set any NULLs to 0 first to avoid constraint error)
        $this->db->query('UPDATE work_orders SET unit_id = 0 WHERE unit_id IS NULL');

        $this->forge->modifyColumn('work_orders', [
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => false,
            ],
        ]);
    }
}
