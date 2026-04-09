<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddItemChecklistToMovementCheckpoints extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('unit_movement_checkpoints')) {
            return;
        }

        if (!$this->db->fieldExists('checked_item_ids_json', 'unit_movement_checkpoints')) {
            $this->forge->addColumn('unit_movement_checkpoints', [
                'checked_item_ids_json' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'notes',
                ],
            ]);
        }

        if (!$this->db->fieldExists('dropped_item_ids_json', 'unit_movement_checkpoints')) {
            $this->forge->addColumn('unit_movement_checkpoints', [
                'dropped_item_ids_json' => [
                    'type' => 'TEXT',
                    'null' => true,
                    'after' => 'checked_item_ids_json',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('unit_movement_checkpoints')) {
            if ($this->db->fieldExists('dropped_item_ids_json', 'unit_movement_checkpoints')) {
                $this->forge->dropColumn('unit_movement_checkpoints', 'dropped_item_ids_json');
            }
            if ($this->db->fieldExists('checked_item_ids_json', 'unit_movement_checkpoints')) {
                $this->forge->dropColumn('unit_movement_checkpoints', 'checked_item_ids_json');
            }
        }
    }
}

