<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUnitMovementMultiItemAndCheckpoint extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('unit_movements')) {
            return;
        }

        // Extend component_type enum for legacy header compatibility.
        $this->db->query("
            ALTER TABLE unit_movements
            MODIFY COLUMN component_type
            ENUM('FORKLIFT','ATTACHMENT','CHARGER','BATTERY','FORK','SPAREPART','OTHERS')
            NULL DEFAULT 'FORKLIFT'
        ");

        if (!$this->db->fieldExists('verification_code', 'unit_movements')) {
            $this->forge->addColumn('unit_movements', [
                'verification_code' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 12,
                    'null'       => true,
                    'after'      => 'surat_jalan_number',
                ],
            ]);
        }

        if (!$this->db->tableExists('unit_movement_items')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'movement_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'component_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['FORKLIFT', 'ATTACHMENT', 'CHARGER', 'BATTERY', 'FORK', 'SPAREPART', 'OTHERS'],
                    'default'    => 'FORKLIFT',
                ],
                'unit_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'component_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'qty' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'item_notes' => [
                    'type' => 'TEXT',
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
            $this->forge->addKey('movement_id');
            $this->forge->addKey('component_type');
            $this->forge->addKey(['movement_id', 'unit_id']);
            $this->forge->createTable('unit_movement_items', true);
        }

        if (!$this->db->tableExists('unit_movement_stops')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'movement_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'sequence_no' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 1,
                ],
                'stop_type' => [
                    'type'       => 'ENUM',
                    'constraint' => ['ORIGIN', 'TRANSIT', 'DESTINATION'],
                    'default'    => 'TRANSIT',
                ],
                'location_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                ],
                'location_type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'eta_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'actual_at' => [
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
            $this->forge->addKey('movement_id');
            $this->forge->addKey(['movement_id', 'sequence_no']);
            $this->forge->addKey('stop_type');
            $this->forge->createTable('unit_movement_stops', true);
        }

        if (!$this->db->tableExists('unit_movement_checkpoints')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'movement_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'stop_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                ],
                'checkpoint_status' => [
                    'type'       => 'ENUM',
                    'constraint' => ['DEPARTED', 'TRANSIT_VERIFIED', 'ARRIVED'],
                    'default'    => 'TRANSIT_VERIFIED',
                ],
                'verifier_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'verifier_phone' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'notes' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
                'checkpoint_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'created_ip' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 45,
                    'null'       => true,
                ],
                'user_agent' => [
                    'type' => 'TEXT',
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
            $this->forge->addKey('movement_id');
            $this->forge->addKey('stop_id');
            $this->forge->addKey('checkpoint_status');
            $this->forge->addKey(['movement_id', 'checkpoint_at']);
            $this->forge->createTable('unit_movement_checkpoints', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('unit_movement_checkpoints')) {
            $this->forge->dropTable('unit_movement_checkpoints', true);
        }
        if ($this->db->tableExists('unit_movement_stops')) {
            $this->forge->dropTable('unit_movement_stops', true);
        }
        if ($this->db->tableExists('unit_movement_items')) {
            $this->forge->dropTable('unit_movement_items', true);
        }
        if ($this->db->fieldExists('verification_code', 'unit_movements')) {
            $this->forge->dropColumn('unit_movements', 'verification_code');
        }

        if ($this->db->tableExists('unit_movements')) {
            $this->db->query("
                ALTER TABLE unit_movements
                MODIFY COLUMN component_type
                ENUM('FORKLIFT','ATTACHMENT','CHARGER','BATTERY','FORK','SPAREPART')
                NULL DEFAULT 'FORKLIFT'
            ");
        }
    }
}

