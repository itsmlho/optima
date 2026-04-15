<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddVehicleTypeToUnitMovements extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('unit_movements')) {
            return;
        }
        if ($this->db->fieldExists('vehicle_type', 'unit_movements')) {
            return;
        }
        $this->forge->addColumn('unit_movements', [
            'vehicle_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
                'after'      => 'vehicle_number',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->fieldExists('vehicle_type', 'unit_movements')) {
            $this->forge->dropColumn('unit_movements', 'vehicle_type');
        }
    }
}
