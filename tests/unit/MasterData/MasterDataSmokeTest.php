<?php

namespace Tests\Unit\MasterData;

use CodeIgniter\Test\CIUnitTestCase;

final class MasterDataSmokeTest extends CIUnitTestCase
{
    /** @var \CodeIgniter\Database\BaseConnection */
    protected $db;

    protected function setUp(): void
    {
        parent::setUp();
        $this->db = \Config\Database::connect();
    }

    public function testCoreMasterTablesExist(): void
    {
        $tables = [
            'departemen',
            'tipe_unit',
            'model_unit',
            'kapasitas',
            'tipe_mast',
            'tipe_ban',
            'jenis_roda',
            'valve',
            'status_unit',
            'attachment',
            'baterai',
            'charger',
            'mesin',
            'status_attachment',
            'work_order_categories',
            'work_order_priorities',
            'work_order_statuses',
            'jenis_perintah_kerja',
            'tujuan_perintah_kerja',
            'status_eksekusi_workflow',
        ];

        foreach ($tables as $table) {
            $this->assertTrue(
                $this->db->tableExists($table),
                "Master table '{$table}' must exist"
            );
        }
    }

    public function testScopeAdditionalTablesExistAfterGapMigration(): void
    {
        foreach (['jenis_unit', 'inventory_status'] as $table) {
            $this->assertTrue(
                $this->db->tableExists($table),
                "Gap-hardening master table '{$table}' must exist"
            );
        }
    }

    public function testMasterDataPermissionsSeeded(): void
    {
        if (!$this->db->tableExists('permissions')) {
            $this->markTestSkipped('permissions table does not exist in this environment.');
        }

        $keys = [
            'view_master_data',
            'master_data.index.navigation',
            'master_data.departemen.view',
            'master_data.model_unit.update',
            'master_data.status_unit.delete',
            'master_data.work_order_status.view',
            'master_data.status_eksekusi_workflow.create',
        ];

        foreach ($keys as $key) {
            $count = $this->db->table('permissions')->where('key_name', $key)->countAllResults();
            $this->assertGreaterThan(0, $count, "Permission '{$key}' must be seeded.");
        }
    }
}

