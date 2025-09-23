<?php

namespace App\Config;

/**
 * Jenis Perintah Kerja Constants
 * Berdasarkan data dari tabel jenis_perintah_kerja
 */
class JenisPerintahKerja
{
    // Jenis Perintah Kerja Constants
    const ANTAR = 'ANTAR';
    const TARIK = 'TARIK';
    const TUKAR = 'TUKAR';
    const RELOKASI = 'RELOKASI';

    /**
     * Get all jenis perintah kerja
     */
    public static function getAll()
    {
        return [
            self::ANTAR => 'Antar Unit',
            self::TARIK => 'Tarik Unit',
            self::TUKAR => 'Tukar Unit',
            self::RELOKASI => 'Relokasi Unit'
        ];
    }

    /**
     * Get jenis perintah description
     */
    public static function getDescription($kode)
    {
        $descriptions = [
            self::ANTAR => 'Pengantaran unit ke lokasi pelanggan',
            self::TARIK => 'Penarikan unit dari lokasi pelanggan',
            self::TUKAR => 'Penukaran unit lama dengan unit baru',
            self::RELOKASI => 'Pemindahan unit antar lokasi'
        ];

        return $descriptions[$kode] ?? '';
    }

    /**
     * Check if jenis perintah requires unit preparation from contractor side
     */
    public static function requiresUnitPreparation($kode)
    {
        // ANTAR dan TUKAR memerlukan persiapan unit
        return in_array($kode, [self::ANTAR, self::TUKAR]);
    }

    /**
     * Check if jenis perintah requires contract validation
     */
    public static function requiresContractValidation($kode)
    {
        // Semua jenis perintah memerlukan validasi kontrak
        return true;
    }

    /**
     * Get valid next jenis perintah for workflow
     */
    public static function getWorkflowTransitions($currentJenis = null)
    {
        if ($currentJenis === null) {
            return [self::ANTAR, self::TARIK, self::TUKAR, self::RELOKASI];
        }

        // Define workflow transitions
        $transitions = [
            self::ANTAR => [self::TARIK, self::TUKAR, self::RELOKASI],
            self::TARIK => [self::ANTAR, self::TUKAR, self::RELOKASI],
            self::TUKAR => [self::TARIK, self::ANTAR, self::RELOKASI],
            self::RELOKASI => [self::TARIK, self::TUKAR, self::ANTAR]
        ];

        return $transitions[$currentJenis] ?? [];
    }
}