<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds Command Purpose options for TARIK/TUKAR:
 * - TARIK_SPARE
 * - TARIK_TRIAL
 * - TUKAR_SPARE
 */
class AddTarikTukarSpareTrialTujuanPerintahKerja extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $jenisTarik = $db->table('jenis_perintah_kerja')->select('id')->where('kode', 'TARIK')->get()->getRowArray();
        $jenisTukar = $db->table('jenis_perintah_kerja')->select('id')->where('kode', 'TUKAR')->get()->getRowArray();

        if (!$jenisTarik || empty($jenisTarik['id'])) {
            log_message('warning', 'AddTarikTukarSpareTrialTujuanPerintahKerja: jenis TARIK not found, skipping TARIK inserts');
        }
        if (!$jenisTukar || empty($jenisTukar['id'])) {
            log_message('warning', 'AddTarikTukarSpareTrialTujuanPerintahKerja: jenis TUKAR not found, skipping TUKAR inserts');
        }

        $rows = [];
        if (!empty($jenisTarik['id'])) {
            $rows[] = [
                'jenis_perintah_id' => (int) $jenisTarik['id'],
                'kode'              => 'TARIK_SPARE',
                'nama'              => 'Tarik Spare',
                'deskripsi'         => 'Tarik unit spare dari kontrak aktif.',
                'aktif'             => 1,
            ];
            $rows[] = [
                'jenis_perintah_id' => (int) $jenisTarik['id'],
                'kode'              => 'TARIK_TRIAL',
                'nama'              => 'Tarik Trial',
                'deskripsi'         => 'Tarik unit trial saat masa trial selesai.',
                'aktif'             => 1,
            ];
        }
        if (!empty($jenisTukar['id'])) {
            $rows[] = [
                'jenis_perintah_id' => (int) $jenisTukar['id'],
                'kode'              => 'TUKAR_SPARE',
                'nama'              => 'Tukar Spare',
                'deskripsi'         => 'Tukar unit aktif dengan unit spare.',
                'aktif'             => 1,
            ];
        }

        foreach ($rows as $row) {
            $exists = $db->table('tujuan_perintah_kerja')->where('kode', $row['kode'])->countAllResults();
            if ($exists === 0) {
                $db->table('tujuan_perintah_kerja')->insert($row);
            }
        }
    }

    public function down()
    {
        $db = \Config\Database::connect();
        $db->table('tujuan_perintah_kerja')->whereIn('kode', ['TARIK_SPARE', 'TARIK_TRIAL', 'TUKAR_SPARE'])->delete();
    }
}
