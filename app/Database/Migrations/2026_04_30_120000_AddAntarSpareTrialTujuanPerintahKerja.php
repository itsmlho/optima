<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds Command Purpose options for ANTAR: Antar Spare, Antar Trial.
 */
class AddAntarSpareTrialTujuanPerintahKerja extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $row = $db->table('jenis_perintah_kerja')->select('id')->where('kode', 'ANTAR')->get()->getRowArray();
        if (!$row || empty($row['id'])) {
            log_message('warning', 'AddAntarSpareTrialTujuanPerintahKerja: jenis ANTAR not found, skipping inserts');

            return;
        }

        $jenisAntarId = (int) $row['id'];

        $rows = [
            [
                'jenis_perintah_id' => $jenisAntarId,
                'kode'              => 'ANTAR_SPARE',
                'nama'              => 'Antar Spare',
                'deskripsi'         => 'Pengantaran unit spare',
                'aktif'             => 1,
            ],
            [
                'jenis_perintah_id' => $jenisAntarId,
                'kode'              => 'ANTAR_TRIAL',
                'nama'              => 'Antar Trial',
                'deskripsi'         => 'Pengantaran unit untuk trial',
                'aktif'             => 1,
            ],
        ];

        foreach ($rows as $r) {
            $exists = $db->table('tujuan_perintah_kerja')->where('kode', $r['kode'])->countAllResults();
            if ($exists === 0) {
                $db->table('tujuan_perintah_kerja')->insert($r);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('tujuan_perintah_kerja')->whereIn('kode', ['ANTAR_SPARE', 'ANTAR_TRIAL'])->delete();
    }
}
