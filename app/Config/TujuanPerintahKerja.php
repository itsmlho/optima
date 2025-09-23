<?php

namespace App\Config;

/**
 * Tujuan Perintah Kerja Constants
 * Berdasarkan data dari tabel tujuan_perintah_kerja
 */
class TujuanPerintahKerja
{
    // ANTAR - Pengantaran Unit
    const ANTAR_BARU = 'ANTAR_BARU';
    const ANTAR_TAMBAHAN = 'ANTAR_TAMBAHAN';
    const ANTAR_PENGGANTI = 'ANTAR_PENGGANTI';

    // TARIK - Penarikan Unit
    const TARIK_HABIS_KONTRAK = 'TARIK_HABIS_KONTRAK';
    const TARIK_PINDAH_LOKASI = 'TARIK_PINDAH_LOKASI';
    const TARIK_MAINTENANCE = 'TARIK_MAINTENANCE';
    const TARIK_RUSAK = 'TARIK_RUSAK';

    // TUKAR - Penukaran Unit
    const TUKAR_UPGRADE = 'TUKAR_UPGRADE';
    const TUKAR_DOWNGRADE = 'TUKAR_DOWNGRADE';
    const TUKAR_RUSAK = 'TUKAR_RUSAK';
    const TUKAR_MAINTENANCE = 'TUKAR_MAINTENANCE';

    // RELOKASI - Pemindahan Unit
    const RELOKASI_INTERNAL = 'RELOKASI_INTERNAL';
    const RELOKASI_OPTIMASI = 'RELOKASI_OPTIMASI';
    const RELOKASI_EMERGENCY = 'RELOKASI_EMERGENCY';

    /**
     * Get tujuan by jenis perintah
     */
    public static function getByJenis($jenisKode)
    {
        $mapping = [
            JenisPerintahKerja::ANTAR => [
                self::ANTAR_BARU => 'Kontrak Baru',
                self::ANTAR_TAMBAHAN => 'Unit Tambahan',
                self::ANTAR_PENGGANTI => 'Unit Pengganti'
            ],
            JenisPerintahKerja::TARIK => [
                self::TARIK_HABIS_KONTRAK => 'Habis Kontrak',
                self::TARIK_PINDAH_LOKASI => 'Pindah Lokasi', 
                self::TARIK_MAINTENANCE => 'Maintenance',
                self::TARIK_RUSAK => 'Unit Rusak'
            ],
            JenisPerintahKerja::TUKAR => [
                self::TUKAR_UPGRADE => 'Upgrade Unit',
                self::TUKAR_DOWNGRADE => 'Downgrade Unit',
                self::TUKAR_RUSAK => 'Ganti Unit Rusak',
                self::TUKAR_MAINTENANCE => 'Ganti Saat Maintenance'
            ],
            JenisPerintahKerja::RELOKASI => [
                self::RELOKASI_INTERNAL => 'Antar Lokasi Client',
                self::RELOKASI_OPTIMASI => 'Optimasi Distribusi',
                self::RELOKASI_EMERGENCY => 'Kebutuhan Mendadak'
            ]
        ];

        return $mapping[$jenisKode] ?? [];
    }

    /**
     * Get description for tujuan
     */
    public static function getDescription($kode)
    {
        $descriptions = [
            // ANTAR
            self::ANTAR_BARU => 'Pengantaran unit untuk kontrak baru',
            self::ANTAR_TAMBAHAN => 'Pengantaran unit tambahan dari kontrak existing',
            self::ANTAR_PENGGANTI => 'Pengantaran unit pengganti untuk unit bermasalah',
            
            // TARIK
            self::TARIK_HABIS_KONTRAK => 'Penarikan unit karena kontrak berakhir',
            self::TARIK_PINDAH_LOKASI => 'Penarikan unit untuk dipindah ke lokasi lain',
            self::TARIK_MAINTENANCE => 'Penarikan unit untuk perawatan/perbaikan',
            self::TARIK_RUSAK => 'Penarikan unit karena mengalami kerusakan',
            
            // TUKAR
            self::TUKAR_UPGRADE => 'Penukaran dengan unit yang lebih tinggi spesifikasinya',
            self::TUKAR_DOWNGRADE => 'Penukaran dengan unit yang lebih rendah spesifikasinya',
            self::TUKAR_RUSAK => 'Penukaran unit yang mengalami kerusakan',
            self::TUKAR_MAINTENANCE => 'Penukaran sementara selama unit di maintenance',
            
            // RELOKASI
            self::RELOKASI_INTERNAL => 'Pemindahan unit antar lokasi dalam satu perusahaan',
            self::RELOKASI_OPTIMASI => 'Pemindahan unit untuk optimasi distribusi',
            self::RELOKASI_EMERGENCY => 'Pemindahan unit untuk kebutuhan mendadak'
        ];

        return $descriptions[$kode] ?? '';
    }

    /**
     * Check if tujuan requires active contract
     */
    public static function requiresActiveContract($kode)
    {
        // Tujuan yang memerlukan kontrak aktif
        $activeContractRequired = [
            self::ANTAR_TAMBAHAN,
            self::ANTAR_PENGGANTI,
            self::TARIK_PINDAH_LOKASI,
            self::TUKAR_UPGRADE,
            self::TUKAR_DOWNGRADE,
            self::TUKAR_RUSAK,
            self::TUKAR_MAINTENANCE,
            self::RELOKASI_INTERNAL,
            self::RELOKASI_OPTIMASI,
            self::RELOKASI_EMERGENCY
        ];

        return in_array($kode, $activeContractRequired);
    }

    /**
     * Check if tujuan requires inactive/expired contract
     */
    public static function requiresInactiveContract($kode)
    {
        // Tujuan yang memerlukan kontrak non-aktif/habis
        return in_array($kode, [
            self::TARIK_HABIS_KONTRAK
        ]);
    }

    /**
     * Check if tujuan allows new contract
     */
    public static function allowsNewContract($kode)
    {
        // Tujuan yang memungkinkan kontrak baru
        return in_array($kode, [
            self::ANTAR_BARU
        ]);
    }

    /**
     * Check if tujuan requires unit replacement (2 units involved)
     */
    public static function requiresUnitReplacement($kode)
    {
        // Tujuan yang memerlukan penggantian unit (ada unit lama dan baru)
        $replacementRequired = [
            self::ANTAR_PENGGANTI,
            self::TUKAR_UPGRADE,
            self::TUKAR_DOWNGRADE,
            self::TUKAR_RUSAK,
            self::TUKAR_MAINTENANCE
        ];

        return in_array($kode, $replacementRequired);
    }

    /**
     * Check if tujuan allows same location movement
     */
    public static function allowsSameLocation($kode)
    {
        // Tujuan yang memungkinkan perpindahan dalam lokasi yang sama
        return in_array($kode, [
            self::TUKAR_UPGRADE,
            self::TUKAR_DOWNGRADE,
            self::TUKAR_RUSAK,
            self::TUKAR_MAINTENANCE
        ]);
    }

    /**
     * Get contract status filter for SPK selection
     */
    public static function getContractStatusFilter($kode)
    {
        if (self::requiresActiveContract($kode)) {
            return 'AKTIF';
        }
        
        if (self::requiresInactiveContract($kode)) {
            return 'NON_AKTIF';
        }
        
        if (self::allowsNewContract($kode)) {
            return 'BARU'; // atau bisa NULL untuk kontrak baru
        }
        
        return null; // Tidak ada filter khusus
    }

    /**
     * Get validation rules for unit selection based on tujuan
     */
    public static function getUnitSelectionRules($kode)
    {
        return [
            'requires_active_contract' => self::requiresActiveContract($kode),
            'requires_inactive_contract' => self::requiresInactiveContract($kode),
            'allows_new_contract' => self::allowsNewContract($kode),
            'requires_unit_replacement' => self::requiresUnitReplacement($kode),
            'allows_same_location' => self::allowsSameLocation($kode),
            'contract_status_filter' => self::getContractStatusFilter($kode)
        ];
    }
}