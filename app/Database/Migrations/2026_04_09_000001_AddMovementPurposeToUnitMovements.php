<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMovementPurposeToUnitMovements extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('unit_movements')) {
            return;
        }

        if (!$this->db->fieldExists('movement_purpose', 'unit_movements')) {
            $this->forge->addColumn('unit_movements', [
                'movement_purpose' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 32,
                    'default'    => 'INTERNAL_TRANSFER',
                    'null'       => false,
                    'after'      => 'status',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('unit_movements') && $this->db->fieldExists('movement_purpose', 'unit_movements')) {
            $this->forge->dropColumn('unit_movements', 'movement_purpose');
        }
    }
}
