<?php

namespace Tests\Unit\Models;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\InventoryUnitModel;

/**
 * Phase 1A: InventoryUnitModel Unit Tests
 * 
 * Tests the new junction table methods added in Phase 1A refactoring:
 * - getWithContractInfo()
 * - getCurrentContract()
 * - getContractHistory()
 * - getUnitsForDropdown()
 * - getUnitDetailForWorkOrder()
 * 
 * @internal
 */
final class InventoryUnitModelTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $model;
    protected $db;

    /**
     * Setup test environment before each test
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new InventoryUnitModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Test: getWithContractInfo() returns unit with current contract details
     * 
     * Verifies that:
     * - Unit with active contract returns contract info
     * - Contract info uses junction table (kontrak_unit)
     * - Customer and location data correctly joined
     * 
     * @group integration
     */
    public function testGetWithContractInfoReturnsActiveContract(): void
    {
        // Check if VIEW exists (created in migration step 2)
        $tables = $this->db->listTables();
        if (!in_array('vw_unit_with_contracts', $tables)) {
            $this->markTestSkipped('vw_unit_with_contracts VIEW not available - requires migration step 2');
        }
        
        // Find a unit with active contract for testing
        $query = $this->db->query("
            SELECT iu.id_inventory_unit
            FROM inventory_unit iu
            INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
                AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
                AND ku.is_temporary = 0
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units with active contracts found in database');
        }

        // Execute method under test
        $results = $this->model->getWithContractInfo(['unit_id' => $testUnit->id_inventory_unit]);
        $unit = !empty($results) ? $results[0] : null;

        // Assertions
        $this->assertNotNull($unit, 'Unit should be found');
        $this->assertArrayHasKey('kontrak_id', $unit, 'Should have kontrak_id from junction table');
        $this->assertArrayHasKey('customer_name', $unit, 'Should have customer name');
        $this->assertArrayHasKey('location_name', $unit, 'Should have location name');
        $this->assertNotNull($unit['kontrak_id'], 'Contract ID should not be null for active contract');
    }

    /**
     * Test: getWithContractInfo() returns nulls for unit without contract
     * 
     * @group integration
     */
    public function testGetWithContractInfoReturnsNullsWhenNoContract(): void
    {
        // Check if VIEW exists (created in migration step 2)
        $tables = $this->db->listTables();
        if (!in_array('vw_unit_with_contracts', $tables)) {
            $this->markTestSkipped('vw_unit_with_contracts VIEW not available - requires migration step 2');
        }
        
        // Find a unit without active contract
        $query = $this->db->query("
            SELECT iu.id_inventory_unit
            FROM inventory_unit iu
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
                AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
                AND ku.is_temporary = 0
            WHERE ku.id IS NULL
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units without contracts found in database');
        }

        // Execute method with correct parameter type (array with filters)
        $results = $this->model->getWithContractInfo(['unit_id' => $testUnit->id_inventory_unit]);
        
        // Should return array with one unit
        $this->assertIsArray($results, 'Should return array of results');
        
        if (count($results) > 0) {
            $unit = $results[0];
            $this->assertNotNull($unit, 'Unit should be found');
            $this->assertNull($unit['current_kontrak_id'] ?? null, 'Contract ID should be null when no active contract');
            $this->assertNull($unit['current_customer_name'] ?? null, 'Customer name should be null');
            $this->assertNull($unit['current_customer_location_name'] ?? null, 'Location name should be null');
        }
    }

    /**
     * Test: getCurrentContract() returns active contract details
     */
    public function testGetCurrentContractReturnsActiveContract(): void
    {
        // Find a unit with active contract
        $query = $this->db->query("
            SELECT iu.id_inventory_unit
            FROM inventory_unit iu
            INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
                AND ku.status IN ('ACTIVE', 'TEMP_ACTIVE')
                AND ku.is_temporary = 0
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units with active contracts found');
        }

        // Execute method
        $contract = $this->model->getCurrentContract($testUnit->id_inventory_unit);

        // Assertions
        $this->assertNotNull($contract, 'Should return contract object');
        $this->assertIsArray($contract, 'Contract should be an array');
        $this->assertArrayHasKey('status', $contract, 'Should have status field');
        $this->assertContains($contract['status'], ['ACTIVE', 'TEMP_ACTIVE'], 'Status should be ACTIVE or TEMP_ACTIVE');
        $this->assertArrayHasKey('is_temporary', $contract, 'Should have is_temporary field');
        $this->assertEquals(0, $contract['is_temporary'], 'is_temporary should be 0 for permanent assignments');
    }

    /**
     * Test: getCurrentContract() returns null when no active contract
     */
    public function testGetCurrentContractReturnsNullWhenNoActiveContract(): void
    {
        // Find a unit without active contract (use ACTIVE status matching current schema)
        $query = $this->db->query("
            SELECT iu.id_inventory_unit
            FROM inventory_unit iu
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
                AND ku.status IN ('ACTIVE', 'PULLED', 'REPLACED')
                AND ku.is_temporary = 0
            WHERE ku.id IS NULL
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units without contracts found');
        }

        // Execute method
        $contract = $this->model->getCurrentContract($testUnit->id_inventory_unit);

        // Assertion
        $this->assertNull($contract, 'Should return null when no active contract');
    }

    /**
     * Test: getContractHistory() returns chronologically ordered history
     */
    public function testGetContractHistoryReturnsChronologicalOrder(): void
    {
        // Find a unit with multiple historical contracts
        $query = $this->db->query("
            SELECT ku.unit_id, COUNT(*) as contract_count
            FROM kontrak_unit ku
            GROUP BY ku.unit_id
            HAVING COUNT(*) > 1
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units with multiple contracts found');
        }

        // Execute method
        $history = $this->model->getContractHistory($testUnit->unit_id);

        // Assertions
        $this->assertIsArray($history, 'Should return an array');
        $this->assertGreaterThan(1, count($history), 'Should have multiple history records');

        // Verify chronological order (most recent first)
        for ($i = 0; $i < count($history) - 1; $i++) {
            $currentDate = strtotime($history[$i]['start_date'] ?? '1970-01-01');
            $nextDate = strtotime($history[$i + 1]['start_date'] ?? '1970-01-01');
            
            $this->assertGreaterThanOrEqual(
                $nextDate,
                $currentDate,
                'Contract history should be ordered by start_date DESC (most recent first)'
            );
        }
    }

    /**
     * Test: getContractHistory() returns empty array for unit with no contracts
     */
    public function testGetContractHistoryReturnsEmptyArrayWhenNoContracts(): void
    {
        // Find a unit without any contracts
        $query = $this->db->query("
            SELECT iu.id_inventory_unit
            FROM inventory_unit iu
            LEFT JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
            WHERE ku.id IS NULL
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units without contracts found');
        }

        // Execute method
        $history = $this->model->getContractHistory($testUnit->id_inventory_unit);

        // Assertion
        $this->assertIsArray($history, 'Should return an array');
        $this->assertEmpty($history, 'Should return empty array when no contracts');
    }

    /**
     * Test: getUnitsForDropdown() excludes units with active contracts
     * 
     * This is critical for preventing double-assignment of units.
     * Only units without active contracts should be available for new contracts.
     */
    public function testGetUnitsForDropdownExcludesContractedUnits(): void
    {
        // Execute method
        $units = $this->model->getUnitsForDropdown();

        // Must always return an array
        $this->assertIsArray($units, 'Should return an array');
        
        // Verify each returned unit does NOT have an active contract
        foreach ($units as $unit) {
            $contract = $this->model->getCurrentContract($unit['id_inventory_unit']);
            
            $this->assertNull(
                $contract,
                "Unit {$unit['nomor_mesin']} should not have active contract but found contract ID: " . 
                ($contract['id'] ?? 'unknown')
            );
        }
    }

    /**
     * Test: getUnitsForDropdown() returns properly formatted data
     */
    public function testGetUnitsForDropdownReturnsCorrectFormat(): void
    {
        // Execute method
        $units = $this->model->getUnitsForDropdown();

        // Should return array (may be empty if all units contracted)
        $this->assertIsArray($units, 'Should return an array');

        // If there are units, verify format
        if (count($units) > 0) {
            $firstUnit = $units[0];
            
            $this->assertArrayHasKey('id_inventory_unit', $firstUnit, 'Should have ID field');
            $this->assertArrayHasKey('nomor_mesin', $firstUnit, 'Should have machine number');
            // Add more field assertions based on actual implementation
        }
    }

    /**
     * Test: getUnitDetailForWorkOrder() returns complete unit information
     * 
     * @group integration
     */
    public function testGetUnitDetailForWorkOrderReturnsCompleteInfo(): void
    {
        // Check if kapasitas_unit table exists (may not exist in staging)
        $tables = $this->db->listTables();
        if (!in_array('kapasitas_unit', $tables)) {
            $this->markTestSkipped('kapasitas_unit table not available in test database');
        }
        
        // Find any unit for testing
        $query = $this->db->query("
            SELECT id_inventory_unit 
            FROM inventory_unit 
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units found in database');
        }

        // Execute method
        $detail = $this->model->getUnitDetailForWorkOrder($testUnit->id_inventory_unit);

        // Assertions - verify required fields for work order context
        $this->assertNotNull($detail, 'Should return unit detail');
        $this->assertIsArray($detail, 'Detail should be an array');
        
        // Core unit fields
        $this->assertArrayHasKey('id_inventory_unit', $detail, 'Should have unit ID');
        $this->assertArrayHasKey('nomor_mesin', $detail, 'Should have machine number');
        
        // Brand information (commonly needed for work orders)
        $this->assertArrayHasKey('brand_name', $detail, 'Should have brand name');
        
        // Customer information (via junction table)
        // Note: May be null if unit has no active contract
        $this->assertArrayHasKey('customer_name', $detail, 'Should have customer name field (may be null)');
        $this->assertArrayHasKey('customer_location_name', $detail, 'Should have location name field (may be null)');
    }

    /**
     * Test: Verify junction table pattern is used (not redundant FK fields)
     * 
     * This is a meta-test to ensure Phase 1A refactoring was successful.
     * Tests that model methods use kontrak_unit junction table, not old FK fields.
     */
    public function testModelDoesNotUseRedundantFKFields(): void
    {
        // Get model's allowed fields using reflection
        $reflection = new \ReflectionClass($this->model);
        $property = $reflection->getProperty('allowedFields');
        $property->setAccessible(true);
        $allowedFields = $property->getValue($this->model);

        // Verify deprecated fields are NOT in allowedFields
        $this->assertNotContains('kontrak_id', $allowedFields, 
            'kontrak_id should be deprecated (removed from allowedFields)');
        $this->assertNotContains('customer_id', $allowedFields, 
            'customer_id should be deprecated (removed from allowedFields)');
        $this->assertNotContains('customer_location_id', $allowedFields, 
            'customer_location_id should be deprecated (removed from allowedFields)');
    }

    /**
     * Test: Performance - getCurrentContract() executes in acceptable time
     * 
     * Verifies that junction table queries are optimized with proper indexes.
     * Target: < 50ms for single unit lookup
     */
    public function testGetCurrentContractPerformance(): void
    {
        // Find a unit with active contract
        $query = $this->db->query("
            SELECT iu.id_inventory_unit
            FROM inventory_unit iu
            INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit
            LIMIT 1
        ");
        
        $testUnit = $query->getRow();
        
        if (!$testUnit) {
            $this->markTestSkipped('No units found for performance test');
        }

        // Measure execution time
        $startTime = microtime(true);
        $contract = $this->model->getCurrentContract($testUnit->id_inventory_unit);
        $endTime = microtime(true);

        $executionTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        // Assert execution time is acceptable (< 50ms)
        $this->assertLessThan(
            50,
            $executionTime,
            "getCurrentContract() should execute in < 50ms, took {$executionTime}ms. Check indexes on kontrak_unit table."
        );
    }

    /**
     * Cleanup after each test
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
