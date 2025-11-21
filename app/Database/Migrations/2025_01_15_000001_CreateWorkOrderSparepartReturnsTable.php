<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWorkOrderSparepartReturnsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'work_order_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'work_order_sparepart_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'sparepart_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'sparepart_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'quantity_brought' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'quantity_used' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'default'    => 0,
            ],
            'quantity_return' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'satuan' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['PENDING', 'CONFIRMED', 'CANCELLED'],
                'default'    => 'PENDING',
            ],
            'return_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'confirmed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'confirmed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('work_order_id');
        $this->forge->addKey('work_order_sparepart_id');
        $this->forge->addKey('status');
        $this->forge->createTable('work_order_sparepart_returns');
    }

    public function down()
    {
        $this->forge->dropTable('work_order_sparepart_returns');
    }
}

