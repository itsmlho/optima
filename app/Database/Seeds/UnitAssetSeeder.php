<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UnitAssetSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'no_unit' => 'FL001',
                'serial_number' => 'SN001-2023',
                'status_unit' => 'available',
                'departemen' => 'Operations',
                'lokasi_unit' => 'Warehouse A',
                'tanggal_kirim' => null,
                'keterangan' => 'Unit forklift standar untuk operasional harian',
                'tipe_unit' => 'Electric Forklift',
                'tahun_unit' => 2023,
                'model_unit' => 'Toyota 8FBE15',
                'kapasitas_unit' => 1.5,
                'status_aset' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'no_unit' => 'FL002',
                'serial_number' => 'SN002-2023',
                'status_unit' => 'rented',
                'departemen' => 'Logistics',
                'lokasi_unit' => 'Warehouse B',
                'tanggal_kirim' => '2024-01-15',
                'keterangan' => 'Unit rental untuk customer XYZ',
                'tipe_unit' => 'LPG Forklift',
                'tahun_unit' => 2022,
                'model_unit' => 'Mitsubishi FG25N',
                'kapasitas_unit' => 2.5,
                'status_aset' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'no_unit' => 'FL003',
                'serial_number' => 'SN003-2021',
                'status_unit' => 'maintenance',
                'departemen' => 'Maintenance',
                'lokasi_unit' => 'Service Bay',
                'tanggal_kirim' => null,
                'keterangan' => 'Sedang dalam perbaikan rutin',
                'tipe_unit' => 'Diesel Forklift',
                'tahun_unit' => 2021,
                'model_unit' => 'Komatsu FD30-17',
                'kapasitas_unit' => 3.0,
                'status_aset' => 'active',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Using Query Builder to insert data
        $this->db->table('unit_asset')->insertBatch($data);
    }
} 