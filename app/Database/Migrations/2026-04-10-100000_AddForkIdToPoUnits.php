<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddForkIdToPoUnits extends Migration
{
    public function up(): void
    {
        if (! $this->db->tableExists('po_units')) {
            return;
        }
        if ($this->db->fieldExists('fork_id', 'po_units')) {
            return;
        }
        $this->forge->addColumn('po_units', [
            'fork_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'Master fork (tabel fork) — dipilih saat verifikasi gudang / PO',
            ],
        ]);
    }

    public function down(): void
    {
        if ($this->db->fieldExists('fork_id', 'po_units')) {
            $this->forge->dropColumn('po_units', 'fork_id');
        }
    }
}
