# Running Phase 1A Tests

**Quick Start Guide for Phase 1A Testing**

Created: 2026-03-04  
Status: Ready for Execution

---

## Prerequisites

### 1. Install PHPUnit (if not already installed)

```bash
cd c:\laragon\www\optima
composer install
```

This will install PHPUnit and all dependencies from `composer.json`.

### 2. Configure Test Database

Edit `app/Config/Database.php` or `.env` to configure the `tests` database group:

```php
// app/Config/Database.php

public array $tests = [
    'DSN'      => '',
    'hostname' => 'localhost',
    'username' => 'root',
    'password' => 'your_password',
    'database' => 'optima_staging',  // Use staging database for testing
    'DBDriver' => 'MySQLi',
    'DBPrefix' => '',
    'pConnect' => false,
    'DBDebug'  => true,
    'charset'  => 'utf8mb4',
    'DBCollat' => 'utf8mb4_unicode_ci',
];
```

Or in `.env`:

```env
database.tests.hostname = localhost
database.tests.database = optima_staging
database.tests.username = root
database.tests.password = your_password
database.tests.DBDriver = MySQLi
database.tests.DBPrefix = 
```

### 3. Create Test Data

```bash
# Create staging database (if not exists)
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS optima_staging;"

# Clone production data to staging (anonymized if needed)
mysqldump -u root -p optima_production > optima_staging_backup.sql
mysql -u root -p optima_staging < optima_staging_backup.sql

# Add Phase 1A test data
mysql -u root -p optima_staging < databases/migrations/create_phase1a_test_data.sql
```

---

## Running Tests

### Option 1: Run All Tests

```bash
# From project root
php vendor/bin/phpunit
```

### Option 2: Run Specific Test Suites

```bash
# Unit tests only (model tests)
php vendor/bin/phpunit tests/unit/

# Database/integration tests only
php vendor/bin/phpunit tests/database/

# Specific test file
php vendor/bin/phpunit tests/unit/Models/InventoryUnitModelTest.php
php vendor/bin/phpunit tests/database/Phase1AMigrationTest.php
```

### Option 3: Run Individual Test Methods

```bash
# Run single test method
php vendor/bin/phpunit --filter testGetWithContractInfoReturnsActiveContract tests/unit/Models/InventoryUnitModelTest.php

# Run all tests matching pattern
php vendor/bin/phpunit --filter testGetCurrentContract tests/unit/Models/InventoryUnitModelTest.php
```

### Option 4: Run with Code Coverage

```bash
# Requires XDebug installed and configured
php vendor/bin/phpunit --coverage-html coverage/
```

Then open `coverage/index.html` in browser to view coverage report.

---

## Test Suites

### 1. Unit Tests: InventoryUnitModelTest

**File:** `tests/unit/Models/InventoryUnitModelTest.php`

**Tests 13 scenarios:**
- ✅ getWithContractInfo() returns active contract
- ✅ getWithContractInfo() returns nulls when no contract
- ✅ getCurrentContract() returns active contract
- ✅ getCurrentContract() returns null when no active contract
- ✅ getContractHistory() returns chronological order
- ✅ getContractHistory() returns empty array when no contracts
- ✅ getUnitsForDropdown() excludes contracted units
- ✅ getUnitsForDropdown() returns correct format
- ✅ getUnitDetailForWorkOrder() returns complete info
- ✅ Model does not use redundant FK fields (meta-test)
- ✅ getCurrentContract() performance (< 50ms)

**Run:**
```bash
php vendor/bin/phpunit tests/unit/Models/InventoryUnitModelTest.php
```

**Expected Output:**
```
PHPUnit 9.x.x by Sebastian Bergmann and contributors.

...........                                                       11 / 11 (100%)

Time: XX seconds, Memory: XX.XX MB

OK (11 tests, XX assertions)
```

### 2. Integration Tests: Phase1AMigrationTest

**File:** `tests/database/Phase1AMigrationTest.php`

**Tests 9 scenarios:**
- ✅ Step 1: Audit finds zero mismatches
- ✅ Step 2: VIEW creation and data accuracy
- ✅ Step 2: VIEW performance (< 100ms)
- ✅ Step 3: Foreign key constraints added
- ✅ Step 3: FK constraints enforce integrity
- ✅ Data consistency after migration
- ✅ No data loss during migration

**Run:**
```bash
php vendor/bin/phpunit tests/database/Phase1AMigrationTest.php
```

**Expected Output:**
```
PHPUnit 9.x.x by Sebastian Bergmann and contributors.

.......                                                           7 / 7 (100%)

Time: XX seconds, Memory: XX.XX MB

OK (7 tests, XX assertions)
```

---

## Interpreting Test Results

### Success (All Green ✅)
```
OK (18 tests, 50 assertions)
```
✅ All tests passed - proceed to deployment

### Failures (Red ❌)
```
FAILURES!
Tests: 18, Assertions: 48, Failures: 2.
```
❌ Fix failures before deployment
- Review error messages
- Check test data
- Verify migration scripts
- Fix code issues

### Skipped Tests (Yellow ⚠️)
```
OK, but incomplete, skipped, or risky tests!
Tests: 18, Assertions: 45, Skipped: 3.
```
⚠️ Some tests skipped due to:
- Missing test data
- Prerequisites not met (e.g., VIEW not created)
- Known issues with environment

**Skipped tests are OK for initial runs**, but should pass after full setup.

---

## Common Issues

### Issue 1: "Database connection failed"

**Cause:** Test database not configured properly

**Fix:**
```bash
# Check database connection
mysql -u root -p optima_staging -e "SELECT 1;"

# Update .env with correct credentials
```

### Issue 2: "No units with active contracts found"

**Cause:** Test data not created

**Fix:**
```bash
mysql -u root -p optima_staging < databases/migrations/create_phase1a_test_data.sql
```

### Issue 3: "VIEW vw_unit_with_contracts does not exist"

**Cause:** Migration Step 2 not executed yet

**Fix:**
```bash
mysql -u root -p optima_staging < databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql
```

### Issue 4: "FK constraint fk_kontrak_unit_unit_id not found"

**Cause:** Migration Step 3 not executed yet

**Note:** This is expected. Step 3 will be executed during deployment.

**Fix (for testing only):**
```bash
# ONLY run this on staging/test database, NOT production
mysql -u root -p optima_staging < databases/migrations/phase1a_step3_add_missing_fk_constraints.sql
```

### Issue 5: "Performance test failed - took XXXms"

**Cause:** Missing indexes on kontrak_unit table

**Fix:**
```sql
-- Check indexes
SHOW INDEXES FROM kontrak_unit;

-- Add composite index if missing
CREATE INDEX idx_ku_active_contracts 
ON kontrak_unit (unit_id, status, is_temporary, kontrak_id);

ANALYZE TABLE kontrak_unit;
```

---

## Test Workflow

### Development Workflow

```bash
# 1. Make code changes
vi app/Models/InventoryUnitModel.php

# 2. Run relevant tests
php vendor/bin/phpunit tests/unit/Models/InventoryUnitModelTest.php

# 3. If tests pass, run full suite
php vendor/bin/phpunit

# 4. Commit changes
git add .
git commit -m "Fix: Improved getCurrentContract() performance"
```

### Pre-Deployment Workflow

```bash
# 1. Create fresh staging environment
mysql -u root -p -e "DROP DATABASE IF EXISTS optima_staging;"
mysql -u root -p -e "CREATE DATABASE optima_staging;"
mysqldump -u root -p optima_production > staging_backup.sql
mysql -u root -p optima_staging < staging_backup.sql

# 2. Add test data
mysql -u root -p optima_staging < databases/migrations/create_phase1a_test_data.sql

# 3. Run all tests
php vendor/bin/phpunit

# 4. Execute migration Step 1 (Audit)
mysql -u root -p optima_staging < databases/migrations/phase1a_step1_audit_inventory_unit_redundancy.sql

# 5. Execute migration Step 2 (VIEW)
mysql -u root -p optima_staging < databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql

# 6. Run integration tests again
php vendor/bin/phpunit tests/database/Phase1AMigrationTest.php

# 7. If all pass, proceed to deployment
```

---

## Cleanup After Testing

```bash
# Remove test data
mysql -u root -p optima_staging < databases/migrations/cleanup_phase1a_test_data.sql

# Or drop staging database entirely
mysql -u root -p -e "DROP DATABASE optima_staging;"
```

---

## Test Coverage Goals

### Current Coverage
- **Unit Tests:** 11 tests covering 5 new model methods
- **Integration Tests:** 7 tests covering 4 migration steps
- **Total:** 18 automated tests

### Target Coverage
- **Model Methods:** 80%+ code coverage
- **Critical Queries:** 100% tested (all 42 queries validated manually)
- **Migration Scripts:** 100% tested via integration tests

### Generate Coverage Report

```bash
# Requires XDebug with xdebug.mode=coverage
php vendor/bin/phpunit --coverage-text

# Or HTML report
php vendor/bin/phpunit --coverage-html coverage/
```

---

## Continuous Integration (Optional)

### GitHub Actions Example

Create `.github/workflows/phase1a-tests.yml`:

```yaml
name: Phase 1A Tests

on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    
    services:
      mysql:
        image: mysql:8.0
        env:
          MYSQL_ROOT_PASSWORD: root
          MYSQL_DATABASE: optima_staging
        ports:
          - 3306:3306
        options: --health-cmd="mysqladmin ping" --health-interval=10s --health-timeout=5s --health-retries=3
    
    steps:
      - uses: actions/checkout@v2
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'
          extensions: mysqli, mbstring
          coverage: xdebug
      
      - name: Install dependencies
        run: composer install
      
      - name: Create test data
        run: mysql -h 127.0.0.1 -u root -proot optima_staging < databases/migrations/create_phase1a_test_data.sql
      
      - name: Run tests
        run: php vendor/bin/phpunit
```

---

## Next Steps After Tests Pass

1. ✅ **All tests green** → Proceed to staging deployment
2. ✅ **Code review** → Get senior developer approval
3. ✅ **Documentation** → Update PHASE1A_DEPLOYMENT_CHECKLIST.md
4. ✅ **Schedule deployment** → Saturday evening (22:00-02:00)
5. ✅ **Team notification** → Inform all stakeholders

---

## Support & Troubleshooting

### Check Test Logs
```bash
# View detailed test output
php vendor/bin/phpunit --debug

# View test failures only
php vendor/bin/phpunit --stop-on-failure
```

### Database State Issues
```bash
# Reset to clean state
mysql -u root -p optima_staging < databases/migrations/cleanup_phase1a_test_data.sql
mysql -u root -p optima_staging < databases/migrations/create_phase1a_test_data.sql
```

### Performance Issues
```sql
-- Check query execution plans
EXPLAIN SELECT * FROM vw_unit_with_contracts WHERE id_inventory_unit = 1;

-- Check table statistics
ANALYZE TABLE kontrak_unit;
ANALYZE TABLE inventory_unit;
```

---

**Document Status:** Ready for Use  
**Last Updated:** 2026-03-04  
**Next Review:** After first test execution

---

**Selamat Testing! 🧪**
