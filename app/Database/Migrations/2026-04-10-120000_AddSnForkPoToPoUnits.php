<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSnForkPoToPoUnits extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('po_units')) {
            return;
        }
        if ($this->db->fieldExists('sn_fork_po', 'po_units')) {
            return;
        }
        $this->forge->addColumn('po_units', [
            'sn_fork_po' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'sn_charger_po',
            ],
        ]);
    }

    public function down()
    {
        if ($this->db->tableExists('po_units') && $this->db->fieldExists('sn_fork_po', 'po_units')) {
            $this->forge->dropColumn('po_units', 'sn_fork_po');
        }
    }
}
