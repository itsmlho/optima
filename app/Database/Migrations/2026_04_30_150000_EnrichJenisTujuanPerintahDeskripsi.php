<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Mengisi kolom deskripsi ringkas pada jenis_perintah_kerja dan tujuan_perintah_kerja (Create DI).
 */
class EnrichJenisTujuanPerintahDeskripsi extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();

        $jenis = [
            'ANTAR' => 'Kirim unit ke lokasi pelanggan (ikut SPK).',
            'TARIK' => 'Tarik unit dari lokasi atau kontrak pelanggan.',
            'TUKAR' => 'Tukar unit lama dengan unit lain (kirim + tarik).',
            'RELOKASI' => 'Pindah unit antar lokasi / site.',
        ];

        foreach ($jenis as $kode => $text) {
            $db->table('jenis_perintah_kerja')->where('kode', $kode)->update(['deskripsi' => $text]);
        }

        $tujuan = [
            'ANTAR_BARU' => 'Antar untuk kontrak / penempatan baru.',
            'ANTAR_TAMBAHAN' => 'Antar unit tambahan ke kontrak yang sudah jalan.',
            'ANTAR_PENGGANTI' => 'Antar unit pengganti (unit bermasalah).',
            'ANTAR_SPARE' => 'Antar unit spare / cadangan.',
            'ANTAR_TRIAL' => 'Antar untuk trial sebelum kontrak penuh.',
            'TARIK_HABIS_KONTRAK' => 'Tarik karena kontrak berakhir.',
            'TARIK_SPARE' => 'Tarik unit spare dari kontrak aktif.',
            'TARIK_TRIAL' => 'Tarik unit trial saat masa trial selesai.',
            'TARIK_PINDAH_LOKASI' => 'Tarik untuk pindah ke lokasi lain.',
            'TARIK_MAINTENANCE' => 'Tarik untuk service / perbaikan.',
            'TARIK_RUSAK' => 'Tarik karena rusak.',
            'TUKAR_UPGRADE' => 'Tukar naik spesifikasi (permanen).',
            'TUKAR_DOWNGRADE' => 'Tukar turun spesifikasi (permanen).',
            'TUKAR_RUSAK' => 'Tukar karena unit rusak (permanen).',
            'TUKAR_MAINTENANCE' => 'Tukar sementara saat maintenance.',
            'TUKAR_SPARE' => 'Tukar unit aktif dengan unit spare.',
            'RELOKASI_INTERNAL' => 'Pindah antar lokasi dalam satu grup pelanggan.',
            'RELOKASI_OPTIMASI' => 'Pindah untuk efisiensi / distribusi.',
            'RELOKASI_EMERGENCY' => 'Pindah mendesak / darurat.',
        ];

        foreach ($tujuan as $kode => $text) {
            $db->table('tujuan_perintah_kerja')->where('kode', $kode)->update(['deskripsi' => $text]);
        }
    }

    public function down()
    {
        // Restore singkat seperti seed awal (opsional rollback)
        $db = \Config\Database::connect();

        $jenisShort = [
            'ANTAR' => 'Pengantaran unit ke lokasi pelanggan',
            'TARIK' => 'Penarikan unit dari lokasi pelanggan',
            'TUKAR' => 'Penukaran unit lama dengan unit baru',
            'RELOKASI' => 'Pemindahan unit antar lokasi',
        ];
        foreach ($jenisShort as $kode => $text) {
            $db->table('jenis_perintah_kerja')->where('kode', $kode)->update(['deskripsi' => $text]);
        }

        $tujuanShort = [
            'ANTAR_BARU' => 'Pengantaran unit untuk kontrak baru',
            'ANTAR_TAMBAHAN' => 'Pengantaran unit tambahan dari kontrak existing',
            'ANTAR_PENGGANTI' => 'Pengantaran unit pengganti untuk unit bermasalah',
            'ANTAR_SPARE' => 'Pengantaran unit spare',
            'ANTAR_TRIAL' => 'Pengantaran unit untuk trial',
            'TARIK_HABIS_KONTRAK' => 'Penarikan unit karena kontrak berakhir',
            'TARIK_SPARE' => 'Penarikan unit spare dari kontrak aktif',
            'TARIK_TRIAL' => 'Penarikan unit trial setelah masa trial',
            'TARIK_PINDAH_LOKASI' => 'Penarikan unit untuk dipindah ke lokasi lain',
            'TARIK_MAINTENANCE' => 'Penarikan unit untuk perawatan/perbaikan',
            'TARIK_RUSAK' => 'Penarikan unit karena mengalami kerusakan',
            'TUKAR_UPGRADE' => 'Penukaran dengan unit yang lebih tinggi spesifikasinya',
            'TUKAR_DOWNGRADE' => 'Penukaran dengan unit yang lebih rendah spesifikasinya',
            'TUKAR_RUSAK' => 'Penukaran unit yang mengalami kerusakan',
            'TUKAR_MAINTENANCE' => 'Penukaran sementara selama unit di maintenance',
            'TUKAR_SPARE' => 'Penukaran unit aktif dengan unit spare',
            'RELOKASI_INTERNAL' => 'Pemindahan unit antar lokasi dalam satu perusahaan',
            'RELOKASI_OPTIMASI' => 'Pemindahan unit untuk optimasi distribusi',
            'RELOKASI_EMERGENCY' => 'Pemindahan unit untuk kebutuhan mendadak',
        ];
        foreach ($tujuanShort as $kode => $text) {
            $db->table('tujuan_perintah_kerja')->where('kode', $kode)->update(['deskripsi' => $text]);
        }
    }
}
