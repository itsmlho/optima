<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublicViewTokenToInventoryUnit extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('inventory_unit')) {
            return;
        }

        if (! $this->db->fieldExists('public_view_token', 'inventory_unit')) {
            $this->forge->addColumn('inventory_unit', [
                'public_view_token' => [
                    'type' => 'VARCHAR',
                    'constraint' => 64,
                    'null' => true,
                    'after' => 'serial_number',
                    'comment' => 'Public scan token for external read-only unit page',
                ],
            ]);
        }

        try {
            $this->db->query('CREATE UNIQUE INDEX idx_inventory_unit_public_view_token ON inventory_unit (public_view_token)');
        } catch (\Throwable $e) {
            // Index may already exist in some environments.
        }
    }

    public function down()
    {
        if (! $this->db->tableExists('inventory_unit')) {
            return;
        }

        try {
            $this->db->query('DROP INDEX idx_inventory_unit_public_view_token ON inventory_unit');
        } catch (\Throwable $e) {
            // Ignore if index does not exist.
        }

        if ($this->db->fieldExists('public_view_token', 'inventory_unit')) {
            $this->forge->dropColumn('inventory_unit', 'public_view_token');
        }
    }
}

