<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKontrakFields extends Migration
{
    public function up()
    {
        // Menambahkan kolom pic, kontak, nilai_total, dan total_units ke tabel kontrak
        $fields = [
            'pic' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Nama Person In Charge',
                'after'      => 'lokasi'
            ],
            'kontak' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'comment'    => 'Kontak PIC (telepon/email)',
                'after'      => 'pic'
            ],
            'nilai_total' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'null'       => true,
                'comment'    => 'Nilai total kontrak dalam rupiah',
                'after'      => 'kontak'
            ],
            'total_units' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'default'    => 0,
                'null'       => false,
                'comment'    => 'Total unit yang terkait dengan kontrak ini',
                'after'      => 'nilai_total'
            ]
        ];

        $this->forge->addColumn('kontrak', $fields);

        // Update existing records to set default total_units = 0
        $this->db->query("UPDATE `kontrak` SET `total_units` = 0 WHERE `total_units` IS NULL");

        // Add index for better performance on total_units queries
        $this->forge->addKey('total_units', false, false, 'idx_total_units');
    }

    public function down()
    {
        // Drop the added columns
        $this->forge->dropColumn('kontrak', ['pic', 'kontak', 'nilai_total', 'total_units']);
        
        // Drop the index
        $this->forge->dropKey('kontrak', 'idx_total_units');
    }
}
