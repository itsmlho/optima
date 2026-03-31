<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateForkInventoryTables extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('fork')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'name' => ['type' => 'VARCHAR', 'constraint' => 200, 'null' => false],
                'length_mm' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'width_mm' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'thickness_mm' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'fork_class' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'capacity_kg' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
                'notes' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('name', 'uq_fork_name');
            $this->forge->createTable('fork', true);
        }

        if (! $this->db->tableExists('inventory_fork_stocks')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'item_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'fork_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'qty_available_pairs' => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 0],
                'physical_condition' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => 'GOOD'],
                'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'AVAILABLE'],
                'storage_location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false, 'default' => 'Workshop'],
                'received_at' => ['type' => 'DATE', 'null' => true],
                'notes' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('item_number', 'uq_inventory_fork_stocks_item_number');
            $this->forge->addKey('fork_id', false, false, 'idx_inventory_fork_stocks_fork_id');
            $this->forge->addKey('status', false, false, 'idx_inventory_fork_stocks_status');
            $this->forge->addForeignKey('fork_id', 'fork', 'id', 'CASCADE', 'CASCADE', 'fk_inventory_fork_stocks_fork');
            $this->forge->createTable('inventory_fork_stocks', true);
        }

        if (! $this->db->tableExists('inventory_forks')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'item_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'fork_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => false],
                'fork_stock_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'inventory_unit_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
                'qty_pairs' => ['type' => 'INT', 'constraint' => 11, 'null' => false, 'default' => 1],
                'physical_condition' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false, 'default' => 'GOOD'],
                'status' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => false, 'default' => 'AVAILABLE'],
                'storage_location' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false, 'default' => 'Workshop'],
                'assigned_at' => ['type' => 'DATETIME', 'null' => true],
                'detached_at' => ['type' => 'DATETIME', 'null' => true],
                'received_at' => ['type' => 'DATE', 'null' => true],
                'notes' => ['type' => 'TEXT', 'null' => true],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('item_number', 'uq_inventory_forks_item_number');
            $this->forge->addKey('fork_id', false, false, 'idx_inventory_forks_fork_id');
            $this->forge->addKey('fork_stock_id', false, false, 'idx_inventory_forks_fork_stock_id');
            $this->forge->addKey('inventory_unit_id', false, false, 'idx_inventory_forks_inventory_unit_id');
            $this->forge->addKey('status', false, false, 'idx_inventory_forks_status');
            $this->forge->addForeignKey('fork_id', 'fork', 'id', 'CASCADE', 'CASCADE', 'fk_inventory_forks_fork');
            $this->forge->addForeignKey('fork_stock_id', 'inventory_fork_stocks', 'id', 'SET NULL', 'CASCADE', 'fk_inventory_forks_stock');
            $this->forge->addForeignKey('inventory_unit_id', 'inventory_unit', 'id_inventory_unit', 'SET NULL', 'CASCADE', 'fk_inventory_forks_unit');
            $this->forge->createTable('inventory_forks', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('inventory_forks')) {
            $this->forge->dropTable('inventory_forks', true);
        }
        if ($this->db->tableExists('inventory_fork_stocks')) {
            $this->forge->dropTable('inventory_fork_stocks', true);
        }
        if ($this->db->tableExists('fork')) {
            $this->forge->dropTable('fork', true);
        }
    }
}

