<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSiloTables extends Migration
{
    public function up()
    {
        // Create silo table
        $this->forge->addField([
            'id_silo' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'unit_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'FK ke inventory_unit.id_inventory_unit',
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => [
                    'BELUM_ADA',
                    'PENGAJUAN_PJK3',
                    'TESTING_PJK3',
                    'SURAT_KETERANGAN_PJK3',
                    'PENGAJUAN_UPTD',
                    'PROSES_UPTD',
                    'SILO_TERBIT',
                    'SILO_EXPIRED'
                ],
                'default'    => 'BELUM_ADA',
            ],
            // Data Pengajuan ke PJK3
            'tanggal_pengajuan_pjk3' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'catatan_pengajuan_pjk3' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Data Testing PJK3
            'tanggal_testing_pjk3' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'hasil_testing_pjk3' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Data Surat Keterangan PJK3
            'nomor_surat_keterangan_pjk3' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tanggal_surat_keterangan_pjk3' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'file_surat_keterangan_pjk3' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path ke file PDF/image',
            ],
            // Data Pengajuan ke UPTD
            'tanggal_pengajuan_uptd' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'catatan_pengajuan_uptd' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Data Proses UPTD
            'tanggal_proses_uptd' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'catatan_proses_uptd' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            // Data SILO Terbit
            'nomor_silo' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'tanggal_terbit_silo' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'tanggal_expired_silo' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'file_silo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'comment'    => 'Path ke file PDF/image',
            ],
            // Metadata
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'FK ke users.id',
            ],
            'updated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'comment'    => 'FK ke users.id',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id_silo', true);
        $this->forge->addKey('unit_id', false, false, 'idx_unit_id');
        $this->forge->addKey('status', false, false, 'idx_status');
        $this->forge->addKey('nomor_silo', false, false, 'idx_nomor_silo');
        $this->forge->addKey('tanggal_expired_silo', false, false, 'idx_tanggal_expired');
        
        // Add foreign key constraint
        $this->forge->addForeignKey('unit_id', 'inventory_unit', 'id_inventory_unit', 'RESTRICT', 'CASCADE', 'fk_silo_unit');
        
        $this->forge->createTable('silo', true);

        // Create silo_history table (optional - for tracking)
        $this->forge->addField([
            'id_history' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'silo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'status_lama' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'status_baru' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'keterangan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'changed_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'changed_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id_history', true);
        $this->forge->addKey('silo_id', false, false, 'idx_silo_id');
        $this->forge->addForeignKey('silo_id', 'silo', 'id_silo', 'CASCADE', 'CASCADE', 'fk_silo_history_silo');
        
        $this->forge->createTable('silo_history', true);
    }

    public function down()
    {
        // Drop foreign keys first
        $this->forge->dropForeignKey('silo_history', 'fk_silo_history_silo');
        $this->forge->dropForeignKey('silo', 'fk_silo_unit');
        
        // Drop tables
        $this->forge->dropTable('silo_history', true);
        $this->forge->dropTable('silo', true);
    }
}

