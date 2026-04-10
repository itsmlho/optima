<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPublicViewTokenToInventoryComponents extends Migration
{
    private array $tables = [
        'inventory_attachments',
        'inventory_batteries',
        'inventory_chargers',
        'inventory_forks',
    ];

    public function up()
    {
        $db = \Config\Database::connect();

        foreach ($this->tables as $table) {
            if (! $db->tableExists($table)) {
                continue;
            }

            if (! $db->fieldExists('public_view_token', $table)) {
                $this->forge->addColumn($table, [
                    'public_view_token' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 64,
                        'null'       => true,
                        'after'      => 'item_number',
                        'comment'    => 'Public scan token for external read-only component page',
                    ],
                ]);
            }

            $indexName = 'idx_' . $table . '_public_view_token';
            try {
                $db->query("CREATE UNIQUE INDEX {$indexName} ON {$table} (public_view_token)");
            } catch (\Throwable $e) {
                // Index may already exist in some environments.
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();

        foreach ($this->tables as $table) {
            if (! $db->tableExists($table)) {
                continue;
            }

            $indexName = 'idx_' . $table . '_public_view_token';
            try {
                $db->query("DROP INDEX {$indexName} ON {$table}");
            } catch (\Throwable $e) {
                // Ignore if index does not exist.
            }

            if ($db->fieldExists('public_view_token', $table)) {
                $this->forge->dropColumn($table, 'public_view_token');
            }
        }
    }
}
