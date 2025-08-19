<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\UnitAssetModel;
use App\Controllers\UnitAssetController;

class UnitAssetTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $unitAssetModel;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->unitAssetModel = new UnitAssetModel();
        $this->controller = new UnitAssetController();
    }

    public function testUnitAssetModelExists()
    {
        $this->assertInstanceOf(UnitAssetModel::class, $this->unitAssetModel);
    }

    public function testControllerExists()
    {
        $this->assertInstanceOf(UnitAssetController::class, $this->controller);
    }

    public function testGetNextUnitNumber()
    {
        $nextNumber = $this->unitAssetModel->getNextUnitNumber();
        $this->assertIsString($nextNumber);
        $this->assertNotEmpty($nextNumber);
    }

    public function testGetUnitAssetStats()
    {
        $stats = $this->unitAssetModel->getUnitAssetStats();
        $this->assertIsArray($stats);
        $this->assertArrayHasKey('total', $stats);
        $this->assertArrayHasKey('available', $stats);
        $this->assertArrayHasKey('rented', $stats);
        $this->assertArrayHasKey('maintenance', $stats);
    }

    public function testGetDepartments()
    {
        $departments = $this->unitAssetModel->getDepartments();
        $this->assertIsArray($departments);
        $this->assertNotEmpty($departments);
    }

    public function testGetLocations()
    {
        $locations = $this->unitAssetModel->getLocations();
        $this->assertIsArray($locations);
    }

    public function testValidationRules()
    {
        $rules = $this->unitAssetModel->getValidationRules();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('serial_number', $rules);
        $this->assertArrayHasKey('status_unit', $rules);
        $this->assertArrayHasKey('departemen', $rules);
    }

    public function testGetFormOptions()
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('getFormOptions');
        $method->setAccessible(true);
        
        $options = $method->invoke($this->controller);
        $this->assertIsArray($options);
        $this->assertArrayHasKey('status_unit', $options);
        $this->assertArrayHasKey('departemen', $options);
        $this->assertArrayHasKey('tipe_unit', $options);
    }

    public function testStatusBadgeGeneration()
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('getStatusBadge');
        $method->setAccessible(true);
        
        $availableBadge = $method->invoke($this->controller, 'available');
        $this->assertStringContainsString('badge bg-success', $availableBadge);
        $this->assertStringContainsString('Available', $availableBadge);
        
        $rentedBadge = $method->invoke($this->controller, 'rented');
        $this->assertStringContainsString('badge bg-primary', $rentedBadge);
        $this->assertStringContainsString('Rented', $rentedBadge);
    }

    public function testAssetStatusBadgeGeneration()
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('getAssetStatusBadge');
        $method->setAccessible(true);
        
        $activeBadge = $method->invoke($this->controller, 'active');
        $this->assertStringContainsString('badge bg-success', $activeBadge);
        $this->assertStringContainsString('Active', $activeBadge);
        
        $inactiveBadge = $method->invoke($this->controller, 'inactive');
        $this->assertStringContainsString('badge bg-warning', $inactiveBadge);
        $this->assertStringContainsString('Inactive', $inactiveBadge);
    }

    public function testTableDataHelper()
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('getTableData');
        $method->setAccessible(true);
        
        $statusData = $method->invoke($this->controller, 'status_unit', 'id_status', 'status_unit');
        $this->assertIsArray($statusData);
        $this->assertNotEmpty($statusData);
    }

    public function testDefaultOptions()
    {
        $reflection = new \ReflectionClass($this->controller);
        $method = $reflection->getMethod('getDefaultOptions');
        $method->setAccessible(true);
        
        $defaults = $method->invoke($this->controller, 'status_unit');
        $this->assertIsArray($defaults);
        $this->assertNotEmpty($defaults);
        
        // Test that defaults have correct structure
        $this->assertArrayHasKey('id_status', $defaults[0]);
        $this->assertArrayHasKey('status_unit', $defaults[0]);
    }

    public function testIdToNameConversion()
    {
        $reflection = new \ReflectionClass($this->unitAssetModel);
        $method = $reflection->getMethod('_convertIdToName');
        $method->setAccessible(true);
        
        $result = $method->invoke($this->unitAssetModel, 1, 'model_mast');
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testValidationRulesForSave()
    {
        $rules = $this->unitAssetModel->getValidationRulesForSave();
        $this->assertIsArray($rules);
        $this->assertArrayHasKey('serial_number', $rules);
        $this->assertArrayHasKey('status_unit', $rules);
        $this->assertArrayHasKey('departemen', $rules);
        $this->assertArrayHasKey('lokasi_unit', $rules);
        $this->assertArrayHasKey('tipe_unit', $rules);
        $this->assertArrayHasKey('tahun_unit', $rules);
        $this->assertArrayHasKey('model_unit', $rules);
        $this->assertArrayHasKey('kapasitas_unit', $rules);
        $this->assertArrayHasKey('status_aset', $rules);
    }

    public function testUnitExistsMethod()
    {
        $exists = $this->unitAssetModel->unitExists('NONEXISTENT_UNIT');
        $this->assertFalse($exists);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
} 