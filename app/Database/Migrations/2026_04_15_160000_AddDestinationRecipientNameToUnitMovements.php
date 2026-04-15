<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDestinationRecipientNameToUnitMovements extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('unit_movements')) {
            return;
        }
        if ($this->db->fieldExists('destination_recipient_name', 'unit_movements')) {
            return;
        }
        $this->forge->addColumn('unit_movements', [
            'destination_recipient_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'destination_location',
            ],
        ]);
    }

    public function down(): void
    {
        if ($this->db->fieldExists('destination_recipient_name', 'unit_movements')) {
            $this->forge->dropColumn('unit_movements', 'destination_recipient_name');
        }
    }
}
