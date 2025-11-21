<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePoVerificationTable extends Migration
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
            'po_type' => [
                'type'       => 'ENUM',
                'constraint' => ['unit', 'attachment', 'sparepart'],
                'null'       => false,
                'comment'    => 'Tipe PO item yang diverifikasi',
            ],
            'source_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'ID dari po_units/po_attachment/po_sparepart_items',
            ],
            'po_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'ID Purchase Order',
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'comment'    => 'Nama field yang tidak sesuai (e.g., sn_unit, merk, model)',
            ],
            'database_value' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Nilai dari database/PO',
            ],
            'real_value' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Nilai real dari lapangan',
            ],
            'discrepancy_type' => [
                'type'       => 'ENUM',
                'constraint' => ['Minor', 'Major', 'Missing'],
                'default'    => 'Minor',
                'null'       => false,
                'comment'    => 'Tipe ketidaksesuaian',
            ],
            'status_verifikasi' => [
                'type'       => 'ENUM',
                'constraint' => ['Sesuai', 'Tidak Sesuai'],
                'null'       => false,
                'comment'    => 'Status verifikasi item ini',
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan tambahan',
            ],
            'verified_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'comment'    => 'User ID yang melakukan verifikasi',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['po_type', 'source_id'], false, false, 'idx_po_type_source');
        $this->forge->addKey('po_id', false, false, 'idx_po_id');
        $this->forge->addKey('status_verifikasi', false, false, 'idx_status');
        $this->forge->addKey('created_at', false, false, 'idx_created_at');

        $this->forge->createTable('po_verification', true);
    }

    public function down()
    {
        $this->forge->dropTable('po_verification', true);
    }
}

