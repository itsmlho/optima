<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Link unit PO line to master baterai / charger / attachment + SN fields (Purchasing unified + WH verification).
 * Safe if columns already exist (e.g. applied manually on production).
 */
class AddPoUnitsBatteryChargerAttachmentColumns extends Migration
{
    public function up()
    {
        $fields = [
            'attachment_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
                'comment'    => 'FK attachment.id_attachment',
                'after'      => 'valve_id',
            ],
            'sn_attachment_po' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'attachment_id',
            ],
            'baterai_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
                'comment'    => 'FK baterai.id',
                'after'      => 'sn_attachment_po',
            ],
            'sn_baterai_po' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'baterai_id',
            ],
            'charger_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => false,
                'null'       => true,
                'comment'    => 'FK charger.id_charger',
                'after'      => 'sn_baterai_po',
            ],
            'sn_charger_po' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'charger_id',
            ],
        ];

        foreach ($fields as $name => $def) {
            if ($this->db->fieldExists($name, 'po_units')) {
                continue;
            }
            if ($name === 'attachment_id' && ! $this->db->fieldExists('valve_id', 'po_units')) {
                unset($def['after']);
            }
            $this->forge->addColumn('po_units', [$name => $def]);
        }
    }

    public function down()
    {
        foreach (['sn_charger_po', 'charger_id', 'sn_baterai_po', 'baterai_id', 'sn_attachment_po', 'attachment_id'] as $col) {
            if ($this->db->fieldExists($col, 'po_units')) {
                $this->forge->dropColumn('po_units', $col);
            }
        }
    }
}
