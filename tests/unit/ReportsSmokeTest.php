<?php

use CodeIgniter\Test\CIUnitTestCase;

/**
 * Smoke tests for the Reports module.
 *
 * Validates DB schema alignment and basic insert/update logic without
 * requiring a live HTTP request or an authenticated session.
 *
 * @internal
 */
final class ReportsSmokeTest extends CIUnitTestCase
{
    /** @var \CodeIgniter\Database\BaseConnection */
    protected $reportsDb;

    protected function setUp(): void
    {
        parent::setUp();
        $this->reportsDb = \Config\Database::connect();
    }

    // ── DB Schema ────────────────────────────────────────────────────────────

    public function testReportsTableExists(): void
    {
        $this->assertTrue(
            $this->reportsDb->tableExists('reports'),
            'reports table must exist'
        );
    }

    public function testReportsTableHasRequiredColumns(): void
    {
        $required = [
            'id', 'name', 'type', 'format', 'filename',
            'file_path', 'file_size', 'description', 'parameters',
            'user_id', 'status', 'data_count', 'created_at', 'updated_at',
        ];

        foreach ($required as $col) {
            $this->assertTrue(
                $this->reportsDb->fieldExists($col, 'reports'),
                "Column '{$col}' must exist in reports table"
            );
        }
    }

    public function testReportsStatusEnumAcceptsAllValues(): void
    {
        $this->reportsDb->table('reports')->insert([
            'name'       => '_smoke_test_',
            'type'       => 'smoke',
            'format'     => 'csv',
            'user_id'    => 1,
            'status'     => 'pending',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        $id = (int) $this->reportsDb->insertID();
        $this->assertGreaterThan(0, $id, 'Insert must return a valid ID');

        foreach (['processing', 'completed', 'failed', 'pending'] as $s) {
            $this->reportsDb->table('reports')->where('id', $id)->update(['status' => $s]);
            $row = $this->reportsDb->table('reports')->where('id', $id)->get()->getRowArray();
            $this->assertSame($s, $row['status'], "Status '{$s}' must be accepted by the enum");
        }

        $this->reportsDb->table('reports')->where('id', $id)->delete();
    }

    // ── Permission helper ─────────────────────────────────────────────────────

    public function testPermissionsTableExists(): void
    {
        $this->assertTrue(
            $this->reportsDb->tableExists('permissions'),
            'permissions table must exist for RBAC to function'
        );
    }
}
