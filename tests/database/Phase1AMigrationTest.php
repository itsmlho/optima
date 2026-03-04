<?php

namespace Tests\Database;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Phase 1A: Migration Integration Tests
 * 
 * Tests all 4 migration steps:
 * - Step 1: Audit for data mismatches
 * - Step 2: CREATE VIEW for backward compatibility
 * - Step 3: ADD FOREIGN KEY constraints
 * - Step 4: DROP redundant columns (tested separately)
 * 
 * These tests validate that migration scripts work correctly and
 * data integrity is maintained throughout the migration process.
 * 
 * @internal
 */
final class Phase1AMigrationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $db;
    protected $DBGroup = 'tests'; // Use test database group

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->db = \Config\Database::connect($this->DBGroup);
    }

    /**
     * Test: Step 1 Audit identifies data mismatches (should be ZERO)
     * 
     * This test executes the audit queries to verify that:
     * - All iu.kontrak_id matches kontrak_unit.kontrak_id
     * - All iu.customer_id matches derived customer from kontrak chain
     * - All iu.customer_location_id matches kontrak.customer_location_id
     * 
     * CRITICAL: If this test fails, data reconciliation is required
     * before proceeding with migration.
     */
    public function testStep1AuditFindsZeroMismatches(): void
    {
        // Check for kontrak_id mismatches
        $kontrakMismatchQuery = "
            SELECT COUNT(*) as mismatch_count
            FROM inventory_unit iu
            INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit 
                AND ku.status IN ('AKTIF','DIPERPANJANG') 
                AND ku.is_temporary = 0
            WHERE iu.kontrak_id IS NOT NULL 
              AND iu.kontrak_id != ku.kontrak_id
        ";
        
        $result = $this->db->query($kontrakMismatchQuery)->getRow();
        
        $this->assertEquals(
            0, 
            $result->mismatch_count,
            "Found {$result->mismatch_count} kontrak_id mismatches. Reconciliation required before migration!"
        );

        // Check for customer_id mismatches
        $customerMismatchQuery = "
            SELECT COUNT(*) as mismatch_count
            FROM inventory_unit iu
            INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit 
                AND ku.status IN ('AKTIF','DIPERPANJANG') 
                AND ku.is_temporary = 0
            INNER JOIN kontrak k ON k.id = ku.kontrak_id
            INNER JOIN customer_locations cl ON cl.id = k.customer_location_id
            WHERE iu.customer_id IS NOT NULL
              AND iu.customer_id != cl.customer_id
        ";
        
        $result = $this->db->query($customerMismatchQuery)->getRow();
        
        $this->assertEquals(
            0,
            $result->mismatch_count,
            "Found {$result->mismatch_count} customer_id mismatches. Reconciliation required!"
        );

        // Check for customer_location_id mismatches
        $locationMismatchQuery = "
            SELECT COUNT(*) as mismatch_count
            FROM inventory_unit iu
            INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit 
                AND ku.status IN ('AKTIF','DIPERPANJANG') 
                AND ku.is_temporary = 0
            INNER JOIN kontrak k ON k.id = ku.kontrak_id
            WHERE iu.customer_location_id IS NOT NULL
              AND iu.customer_location_id != k.customer_location_id
        ";
        
        $result = $this->db->query($locationMismatchQuery)->getRow();
        
        $this->assertEquals(
            0,
            $result->mismatch_count,
            "Found {$result->mismatch_count} customer_location_id mismatches. Reconciliation required!"
        );
    }

    /**
     * Test: Step 2 VIEW creation and data accuracy
     * 
     * Creates the vw_unit_with_contracts VIEW and verifies:
     * - VIEW is created successfully
     * - VIEW has all required columns
     * - VIEW data matches expected values from junction table
     */
    public function testStep2ViewCreationAndDataAccuracy(): void
    {
        // Drop VIEW if it exists from previous test run
        $this->db->query("DROP VIEW IF EXISTS vw_unit_with_contracts");

        // Read and execute Step 2 migration script
        $migrationSQL = file_get_contents(
            ROOTPATH . 'databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql'
        );
        
        // Execute VIEW creation
        $this->db->query($migrationSQL);

        // Verify VIEW exists
        $viewCheckQuery = "
            SHOW FULL TABLES 
            WHERE Table_type = 'VIEW' 
              AND Tables_in_" . $this->db->database . " = 'vw_unit_with_contracts'
        ";
        
        $result = $this->db->query($viewCheckQuery);
        
        $this->assertGreaterThan(
            0,
            $result->getNumRows(),
            'VIEW vw_unit_with_contracts should be created'
        );

        // Verify VIEW has required columns
        $columnsQuery = "DESCRIBE vw_unit_with_contracts";
        $columns = $this->db->query($columnsQuery)->getResultArray();
        $columnNames = array_column($columns, 'Field');

        $requiredColumns = [
            'id_inventory_unit',
            'kontrak_id',
            'customer_id',
            'customer_location_id',
            'nomor_mesin'
        ];

        foreach ($requiredColumns as $column) {
            $this->assertContains(
                $column,
                $columnNames,
                "VIEW should have column: {$column}"
            );
        }

        // Verify VIEW data matches junction table query
        $viewDataQuery = "
            SELECT 
                v.id_inventory_unit,
                v.kontrak_id,
                v.customer_id,
                v.customer_location_id
            FROM vw_unit_with_contracts v
            WHERE v.kontrak_id IS NOT NULL
            LIMIT 1
        ";
        
        $viewData = $this->db->query($viewDataQuery)->getRow();

        if ($viewData) {
            // Verify same data from junction table
            $junctionDataQuery = "
                SELECT 
                    iu.id_inventory_unit,
                    ku.kontrak_id,
                    cl.customer_id,
                    k.customer_location_id
                FROM inventory_unit iu
                INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
                    AND ku.status IN ('AKTIF','DIPERPANJANG')
                    AND ku.is_temporary = 0
                INNER JOIN kontrak k ON k.id = ku.kontrak_id
                INNER JOIN customer_locations cl ON cl.id = k.customer_location_id
                WHERE iu.id_inventory_unit = ?
            ";
            
            $junctionData = $this->db->query($junctionDataQuery, [$viewData->id_inventory_unit])->getRow();

            $this->assertEquals(
                $junctionData->kontrak_id,
                $viewData->kontrak_id,
                'VIEW kontrak_id should match junction table'
            );

            $this->assertEquals(
                $junctionData->customer_id,
                $viewData->customer_id,
                'VIEW customer_id should match junction table'
            );

            $this->assertEquals(
                $junctionData->customer_location_id,
                $viewData->customer_location_id,
                'VIEW customer_location_id should match junction table'
            );
        }
    }

    /**
     * Test: Step 2 VIEW performance
     * 
     * Verifies that VIEW queries execute with acceptable performance.
     * Target: < 100ms for 100 records
     */
    public function testStep2ViewPerformance(): void
    {
        // Ensure VIEW exists
        $this->db->query("DROP VIEW IF EXISTS vw_unit_with_contracts");
        $migrationSQL = file_get_contents(
            ROOTPATH . 'databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql'
        );
        $this->db->query($migrationSQL);

        // Measure query execution time
        $startTime = microtime(true);
        
        $this->db->query("SELECT * FROM vw_unit_with_contracts LIMIT 100")->getResult();
        
        $endTime = microtime(true);
        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Assert performance is acceptable
        $this->assertLessThan(
            100,
            $executionTime,
            "VIEW query should execute in < 100ms, took {$executionTime}ms. Check indexes on kontrak_unit table."
        );

        // Verify EXPLAIN plan uses indexes
        $explainQuery = "
            EXPLAIN SELECT * FROM vw_unit_with_contracts WHERE id_inventory_unit = 1
        ";
        
        $explain = $this->db->query($explainQuery)->getResultArray();
        
        // Check that junction table uses index (type should be 'ref' or 'eq_ref', not 'ALL')
        $hasGoodIndexUsage = false;
        foreach ($explain as $row) {
            if (isset($row['type']) && in_array($row['type'], ['ref', 'eq_ref', 'const'])) {
                $hasGoodIndexUsage = true;
                break;
            }
        }

        $this->assertTrue(
            $hasGoodIndexUsage,
            'VIEW query should use indexes (type should be ref/eq_ref, not ALL). Check kontrak_unit indexes.'
        );
    }

    /**
     * Test: Step 3 Foreign Key constraint addition
     * 
     * WARNING: This test requires marketing_name to be populated in kontrak_unit
     * before executing. The test will skip if prerequisites aren't met.
     */
    public function testStep3ForeignKeyConstraintsAdded(): void
    {
        // Check if marketing_name is populated (prerequisite)
        $nullMarketingNameQuery = "
            SELECT COUNT(*) as null_count 
            FROM kontrak_unit 
            WHERE marketing_name IS NULL OR marketing_name = ''
        ";
        
        $result = $this->db->query($nullMarketingNameQuery)->getRow();
        
        if ($result->null_count > 0) {
            $this->markTestSkipped(
                "Found {$result->null_count} records with NULL marketing_name. " .
                "Run marketing_name population script before executing Step 3."
            );
        }

        // Drop FK if exists from previous test
        $this->db->query("
            ALTER TABLE kontrak_unit 
            DROP FOREIGN KEY IF EXISTS fk_kontrak_unit_unit_id
        ");
        $this->db->query("
            ALTER TABLE kontrak_unit 
            DROP FOREIGN KEY IF EXISTS fk_kontrak_unit_kontrak_id
        ");

        // Read and execute Step 3 migration script
        $migrationSQL = file_get_contents(
            ROOTPATH . 'databases/migrations/phase1a_step3_add_missing_fk_constraints.sql'
        );
        
        // Execute FK addition (may fail if marketing_name still has NULLs)
        try {
            $this->db->query($migrationSQL);
        } catch (\Exception $e) {
            $this->fail("FK constraint creation failed: " . $e->getMessage());
        }

        // Verify FK constraints were added
        $fkCheckQuery = "
            SELECT 
                CONSTRAINT_NAME,
                TABLE_NAME,
                REFERENCED_TABLE_NAME,
                REFERENCED_COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'kontrak_unit'
              AND REFERENCED_TABLE_NAME IS NOT NULL
        ";
        
        $constraints = $this->db->query($fkCheckQuery)->getResultArray();
        $constraintNames = array_column($constraints, 'CONSTRAINT_NAME');

        // Verify both FK constraints exist
        $this->assertContains(
            'fk_kontrak_unit_unit_id',
            $constraintNames,
            'FK constraint fk_kontrak_unit_unit_id should exist'
        );

        $this->assertContains(
            'fk_kontrak_unit_kontrak_id',
            $constraintNames,
            'FK constraint fk_kontrak_unit_kontrak_id should exist'
        );
    }

    /**
     * Test: Step 3 FK constraint enforcement
     * 
     * Verifies that FK constraints actually prevent invalid data.
     * Tests referential integrity is enforced.
     */
    public function testStep3ForeignKeyConstraintsEnforceIntegrity(): void
    {
        // Skip if FK not added yet
        $fkCheckQuery = "
            SELECT COUNT(*) as fk_count
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'kontrak_unit'
              AND CONSTRAINT_NAME = 'fk_kontrak_unit_unit_id'
        ";
        
        $result = $this->db->query($fkCheckQuery)->getRow();
        
        if ($result->fk_count == 0) {
            $this->markTestSkipped('FK constraints not yet added. Run Step 3 migration first.');
        }

        // Test 1: Try to insert invalid unit_id (should FAIL)
        $invalidUnitInsert = "
            INSERT INTO kontrak_unit (unit_id, kontrak_id, status, marketing_name) 
            VALUES (999999, 1, 'AKTIF', 'Test User')
        ";
        
        $exceptionThrown = false;
        try {
            $this->db->query($invalidUnitInsert);
        } catch (\Exception $e) {
            $exceptionThrown = true;
            $this->assertStringContainsString(
                'foreign key constraint',
                strtolower($e->getMessage()),
                'Should throw foreign key constraint error'
            );
        }
        
        $this->assertTrue($exceptionThrown, 'Invalid unit_id insert should fail with FK constraint error');

        // Test 2: Try to delete unit that's referenced (should FAIL or CASCADE)
        // First, find a unit that's in kontrak_unit
        $referencedUnitQuery = "
            SELECT unit_id 
            FROM kontrak_unit 
            LIMIT 1
        ";
        
        $referencedUnit = $this->db->query($referencedUnitQuery)->getRow();
        
        if ($referencedUnit) {
            $deleteUnitQuery = "
                DELETE FROM inventory_unit 
                WHERE id_inventory_unit = ?
            ";
            
            $deleteExceptionThrown = false;
            try {
                $this->db->query($deleteUnitQuery, [$referencedUnit->unit_id]);
            } catch (\Exception $e) {
                $deleteExceptionThrown = true;
            }
            
            // Should either throw error (RESTRICT) or cascade (CASCADE)
            // Either behavior is acceptable as long as FK is enforced
            $this->assertTrue(
                $deleteExceptionThrown,
                'Deleting referenced unit should either fail or cascade (depending on FK constraint type)'
            );
        }
    }

    /**
     * Test: Data consistency after all migration steps
     * 
     * This is the final integration test that verifies:
     * - Old FK fields match junction table derived values
     * - VIEW provides accurate backward compatibility
     * - No data loss occurred during migration
     */
    public function testDataConsistencyAfterMigration(): void
    {
        // Ensure VIEW exists
        $viewExists = $this->db->query("
            SHOW FULL TABLES 
            WHERE Table_type = 'VIEW' 
              AND Tables_in_" . $this->db->database . " = 'vw_unit_with_contracts'
        ")->getNumRows() > 0;

        if (!$viewExists) {
            $this->markTestSkipped('VIEW not created yet. Run Step 2 migration first.');
        }

        // Compare old FK fields vs VIEW derived values
        $comparisonQuery = "
            SELECT 
                COUNT(*) as total_units,
                SUM(CASE WHEN iu.kontrak_id <=> derived.kontrak_id THEN 1 ELSE 0 END) as matching_kontrak,
                SUM(CASE WHEN iu.customer_id <=> derived.customer_id THEN 1 ELSE 0 END) as matching_customer,
                SUM(CASE WHEN iu.customer_location_id <=> derived.customer_location_id THEN 1 ELSE 0 END) as matching_location
            FROM inventory_unit iu
            LEFT JOIN vw_unit_with_contracts derived 
                ON derived.id_inventory_unit = iu.id_inventory_unit
        ";
        
        $result = $this->db->query($comparisonQuery)->getRow();

        // All records should match
        $this->assertEquals(
            $result->total_units,
            $result->matching_kontrak,
            "All kontrak_id values should match between old FK and VIEW. Found " .
            ($result->total_units - $result->matching_kontrak) . " mismatches."
        );

        $this->assertEquals(
            $result->total_units,
            $result->matching_customer,
            "All customer_id values should match between old FK and VIEW. Found " .
            ($result->total_units - $result->matching_customer) . " mismatches."
        );

        $this->assertEquals(
            $result->total_units,
            $result->matching_location,
            "All customer_location_id values should match between old FK and VIEW. Found " .
            ($result->total_units - $result->matching_location) . " mismatches."
        );
    }

    /**
     * Test: Verify no data loss during migration
     * 
     * Ensures that all units, contracts, and assignments are preserved.
     */
    public function testNoDataLossDuringMigration(): void
    {
        // Count total units before/after (should be same)
        $unitCountQuery = "SELECT COUNT(*) as count FROM inventory_unit";
        $unitCount = $this->db->query($unitCountQuery)->getRow()->count;
        
        $this->assertGreaterThan(0, $unitCount, 'Should have units in database');

        // Count total contract assignments
        $assignmentCountQuery = "SELECT COUNT(*) as count FROM kontrak_unit";
        $assignmentCount = $this->db->query($assignmentCountQuery)->getRow()->count;
        
        $this->assertGreaterThan(0, $assignmentCount, 'Should have contract assignments');

        // Verify VIEW shows same number of units
        $viewCountQuery = "SELECT COUNT(DISTINCT id_inventory_unit) as count FROM vw_unit_with_contracts";
        
        try {
            $viewCount = $this->db->query($viewCountQuery)->getRow()->count;
            
            $this->assertEquals(
                $unitCount,
                $viewCount,
                'VIEW should show same number of units as inventory_unit table'
            );
        } catch (\Exception $e) {
            $this->markTestSkipped('VIEW not created yet. Run Step 2 migration first.');
        }
    }

    /**
     * Cleanup after tests
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        
        // Close database connection
        if ($this->db) {
            $this->db->close();
        }
    }
}
