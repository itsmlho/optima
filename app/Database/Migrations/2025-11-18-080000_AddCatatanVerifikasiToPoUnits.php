<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCatatanVerifikasiToPoUnits extends Migration
{
    public function up()
    {
        // Check if column already exists
        if ($this->db->fieldExists('catatan_verifikasi', 'po_units')) {
            log_message('info', '[Migration] Column catatan_verifikasi already exists in po_units table');
            return;
        }
        
        $this->forge->addColumn('po_units', [
            'catatan_verifikasi' => [
                'type' => 'TEXT',
                'null' => true,
                'comment' => 'Catatan verifikasi / alasan reject jika status Tidak Sesuai',
                'after' => 'keterangan'
            ],
        ]);
    }

    public function down()
    {
        // Check if column exists before dropping
        if ($this->db->fieldExists('catatan_verifikasi', 'po_units')) {
            $this->forge->dropColumn('po_units', 'catatan_verifikasi');
        }
    }
}

