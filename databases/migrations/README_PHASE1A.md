# Phase 1A Migration Scripts - Critical Data Integrity Fixes

**Project:** OPTIMA Marketing Module Refactoring  
**Phase:** 1A - Critical Data Integrity Fixes  
**Timeline:** Week 1-2  
**Status:** Ready for Execution

---

## Overview

Phase 1A addresses critical data integrity issues:
1. **Remove redundant FK fields** from `inventory_unit` table
2. **Add missing FK constraints** to prevent orphaned records
3. **Convert `customers.marketing_name`** from VARCHAR to proper FK relationship

---

## Migration Scripts Execution Order

### Step 1: Audit Redundant Data
**File:** `phase1a_step1_audit_inventory_unit_redundancy.sql`  
**Purpose:** Identify data mismatches between `inventory_unit` and `kontrak_unit` junction table  
**Execution Time:** ~5-10 minutes (read-only queries)  
**Prerequisites:** Database access, SELECT permissions  

**How to Run:**
```bash
# Run in MySQL client
mysql -u root -p optima_ci < databases/migrations/phase1a_step1_audit_inventory_unit_redundancy.sql > audit_results.txt

# Review results
cat audit_results.txt
# OR open in Excel/Google Sheets for analysis
```

**Expected Output:**
- Mismatch count (ideally 0)
- Summary statistics
- List of orphaned records (if any)

**Action Required:**
- Review audit results
- Document any mismatches found
- Create reconciliation plan if mismatches exist
- Get business approval before proceeding

---

### Step 2: Create Backward Compatibility View
**File:** `phase1a_step2_create_vw_unit_with_contracts.sql`  
**Purpose:** Create VIEW that derives `kontrak_id`, `customer_id`, `customer_location_id` from junction table  
**Execution Time:** ~1-2 minutes  
**Prerequisites:** Step 1 completed, mismatches reconciled  

**How to Run:**
```bash
# BACKUP FIRST!
mysqldump -u root -p optima_ci inventory_unit kontrak_unit kontrak customer_locations customers > backup_before_view_$(date +%Y%m%d_%H%M%S).sql

# Create view
mysql -u root -p optima_ci < databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql
```

**Verification:**
```sql
-- Test view returns data
SELECT COUNT(*) FROM vw_unit_with_contracts;

-- Test performance
EXPLAIN SELECT * FROM vw_unit_with_contracts WHERE customer_id = 1;

-- Test query
SELECT * FROM vw_unit_with_contracts LIMIT 10;
```

**Success Criteria:**
- ✅ View created successfully
- ✅ Row count matches inventory_unit table
- ✅ Queries execute in < 100ms
- ✅ No errors in error log

---

### Step 3: Add Missing Foreign Key Constraints
**File:** `phase1a_step3_add_missing_fk_constraints.sql`  
**Purpose:** Add FK constraints to ensure data integrity  
**Execution Time:** ~10-15 minutes  
**Prerequisites:** Steps 1-2 completed  

**Foreign Keys to Add:**
1. `kontrak.customer_location_id → customer_locations.id` (RESTRICT)
2. `spk.kontrak_id → kontrak.id` (SET NULL)
3. `customers.marketing_user_id → users.id` (SET NULL)

**How to Run:**
```bash
# BACKUP FIRST!
mysqldump -u root -p optima_ci kontrak spk customers > backup_before_fk_$(date +%Y%m%d_%H%M%S).sql

# Run migration (execute in sections)
mysql -u root -p optima_ci < databases/migrations/phase1a_step3_add_missing_fk_constraints.sql
```

**⚠️ IMPORTANT:** This script has multiple parts. Execute each PART separately and verify before proceeding:

```bash
# Part 1: kontrak.customer_location_id FK
# Part 2: spk.kontrak_id FK
# Part 3: customers.marketing_user_id conversion
# Part 4: Audit table cascade rule updates
```

**Manual Steps Required (Part 3):**
1. Review unmatched marketing names (Step 3.3 output)
2. Decide how to handle:
   - Create user accounts?
   - Map to existing users?
   - Set to default?
3. Execute chosen resolution strategy
4. Re-run mapping query (Step 3.5)
5. Verify all customers have marketing_user_id populated

**Testing:**
```sql
-- Test FK constraints work
START TRANSACTION;

-- Should FAIL (kontrak.customer_location_id FK)
DELETE FROM customer_locations WHERE id = (
    SELECT customer_location_id FROM kontrak LIMIT 1
);

-- Should succeed (spk.kontrak_id SET NULL)
SELECT kontrak_id FROM spk WHERE kontrak_id IS NOT NULL LIMIT 1;
DELETE FROM kontrak WHERE id = [above_kontrak_id];
SELECT kontrak_id FROM spk WHERE kontrak_id = [above_kontrak_id]; -- Should be NULL

ROLLBACK;
```

**Success Criteria:**
- ✅ All FK constraints added
- ✅ Orphaned records cleaned
- ✅ Test deletions behave as expected
- ✅ `customers.marketing_user_id` fully populated
- ✅ No errors in production logs

---

### Step 4: Update Application Code
**Files to Modify:**
1. `app/Models/InventoryUnitModel.php`
2. `app/Models/CustomerModel.php`
3. `app/Controllers/Marketing.php`
4. `app/Controllers/CustomerManagementController.php`
5. Views: customer forms, unit displays

**Changes Needed:**

#### InventoryUnitModel.php
```php
// Remove from $allowedFields:
// 'kontrak_id',
// 'customer_id',
// 'customer_location_id',

// Add methods to use VIEW
public function getWithContractInfo(array $filters = []) {
    $builder = $this->db->table('vw_unit_with_contracts');
    // Apply filters...
    return $builder->get()->getResult();
}

public function getCurrentContract(int $unitId) {
    return $this->db->table('kontrak_unit')
        ->join('kontrak', 'kontrak_unit.kontrak_id = kontrak.id')
        ->where('kontrak_unit.unit_id', $unitId)
        ->whereIn('kontrak_unit.status', ['AKTIF', 'DIPERPANJANG'])
        ->get()->getRow();
}
```

#### CustomerModel.php
```php
protected $allowedFields = [
    // ...
    'marketing_user_id',  // NEW
    // 'marketing_name',  // DEPRECATED - will be removed 2026-05-04
];

// Add relationship method
public function getWithMarketingPerson(int $customerId) {
    return $this->db->table('customers')
        ->select('customers.*, users.name as marketing_person_name')
        ->join('users', 'customers.marketing_user_id = users.id', 'left')
        ->where('customers.id', $customerId)
        ->get()->getRow();
}
```

#### Marketing.php & CustomerManagementController.php
```php
// Find & Replace:
// OLD: inventory_unit.kontrak_id
// NEW: vw_unit_with_contracts.kontrak_id
//      OR JOIN to kontrak_unit

// OLD: $customer['marketing_name']
// NEW: $customer['marketing_person_name'] (from JOIN)
```

**Testing After Code Updates:**
- [ ] SPK creation workflow
- [ ] DI creation and approval
- [ ] Unit assignment to contract
- [ ] Customer CRUD with marketing assignment
- [ ] Reports and exports
- [ ] DataTables filtering

---

### Step 5: Staging Deployment & Testing
**Duration:** 2 weeks minimum  
**Environment:** staging.optima.local  

**Deployment Steps:**
1. Copy backup to staging server
2. Restore staging database from production backup
3. Run Steps 1-3 migration scripts in staging
4. Deploy updated code
5. Run comprehensive tests (see test plan in complete plan document)
6. Fix any issues found
7. Re-test until zero issues

**Test Scenarios:**
1. ✅ Create quotation → contract → SPK → DI (full workflow)
2. ✅ Assign units to contract
3. ✅ Swap unit
4. ✅ Release unit from contract
5. ✅ Create customer with marketing assignment
6. ✅ Change customer's marketing person
7. ✅ Generate reports
8. ✅ Export to Excel
9. ✅ Performance test (1000+ units)

**Sign-Off Required:**
- QA Lead
- Business Stakeholder
- Development Team Lead

---

### Step 6: Production Deployment
**Scheduled:** [DATE TBD - After 2 weeks staging validation]  
**Maintenance Window:** Saturday 22:00 - 02:00 (4 hours)  

**Pre-Deployment Checklist:**
- [ ] All staging tests pass (zero critical bugs)
- [ ] Business sign-off obtained
- [ ] Team trained on new structure
- [ ] Documentation updated
- [ ] Rollback plan prepared
- [ ] Communication sent to users (3 days notice)

**Deployment Procedure:**
```bash
# 22:00 - Enable maintenance mode
php spark down

# 22:10 - Full database backup
mysqldump -u root -p optima_ci > backup_prod_phase1a_$(date +%Y%m%d_%H%M%S).sql
# Verify backup size > 0
ls -lh backup_prod_phase1a_*.sql

# 22:20 - Run Step 1 (Audit)
mysql -u root -p optima_ci < phase1a_step1_audit_inventory_unit_redundancy.sql > audit_prod.txt
# Review results - ABORT if critical issues found

# 22:30 - Run Step 2 (Create VIEW)
mysql -u root -p optima_ci < phase1a_step2_create_vw_unit_with_contracts.sql

# 22:45 - Run Step 3 (Add FKs) - Execute each PART separately
mysql -u root -p optima_ci < phase1a_step3_add_missing_fk_constraints.sql

# 23:00 - Deploy code
git pull origin main
composer install --no-dev
php spark cache:clear

# 23:15 - Smoke tests
php spark migrate:status
# Test critical pages manually

# 23:30 - Disable maintenance mode
php spark up

# 23:30-00:30 - Monitor error logs
tail -f writable/logs/log-$(date +%Y-m-d).log
```

**Post-Deployment Monitoring:**
- [ ] Check error logs every 2 hours (first 24h)
- [ ] Performance metrics (response times)
- [ ] User-reported issues
- [ ] Database query slow log
- [ ] Fix hotfix bugs if any

---

### Step 7: Drop Redundant Columns (AFTER 2-3 WEEKS)
**File:** `phase1a_step4_drop_redundant_columns.sql`  
**Purpose:** Final cleanup - remove old redundant columns  
**Execution Date:** 2026-03-25 or later (3 weeks after Step 6)  

**Prerequisites:**
- ✅ Steps 1-6 completed successfully
- ✅ 2+ weeks of production operation without issues
- ✅ All code verified to use VIEW/junction table
- ✅ grep confirms zero references to old column names
- ✅ Business approval for final step

**⚠️ CRITICAL:** This is irreversible without restore from backup

**How to Run:**
```bash
# FINAL BACKUP
mysqldump -u root -p optima_ci inventory_unit > backup_inventory_unit_before_drop_$(date +%Y%m%d_%H%M%S).sql

# Execute drop
mysql -u root -p optima_ci < databases/migrations/phase1a_step4_drop_redundant_columns.sql
```

**Verification:**
```sql
-- Confirm columns gone
SHOW COLUMNS FROM inventory_unit;

-- Confirm VIEW still works
SELECT * FROM vw_unit_with_contracts LIMIT 10;
```

---

## Rollback Procedures

### Rollback Step 2 (View Creation)
```sql
DROP VIEW IF EXISTS vw_unit_with_contracts;
```

### Rollback Step 3 (FK Constraints)
```sql
ALTER TABLE kontrak DROP FOREIGN KEY fk_kontrak_customer_location;
ALTER TABLE spk DROP FOREIGN KEY fk_spk_kontrak;
ALTER TABLE customers DROP FOREIGN KEY fk_customers_marketing_user;
ALTER TABLE customers DROP COLUMN marketing_user_id;
-- Restore original FK constraints if needed
```

### Rollback Step 7 (Column Drop) - EMERGENCY ONLY
```bash
# Restore from backup
mysql -u root -p optima_ci < backup_inventory_unit_before_drop_[timestamp].sql
```

---

## Communication Plan

### Before Deployment
- [ ] Email to development team (3 days notice)
- [ ] Update in daily standup
- [ ] Slack announcement (#dev-announcements)
- [ ] JIRA tickets created for tracking

### During Deployment
- [ ] Status updates in Slack (#dev-ops)
- [ ] Notify if any issues found

### After Deployment
- [ ] Deployment success notification
- [ ] Daily error log summary (first week)
- [ ] Weekly status update to stakeholders

---

## Success Metrics

**Data Integrity:**
- Zero orphaned records in database
- All FK constraints enforced
- 100% referential integrity

**Performance:**
- View queries < 100ms
- No performance degradation in DataTables
- No slow query log entries for unit queries

**Code Quality:**
- Zero references to old redundant columns
- All tests passing
- No production errors related to unit relationships

**Business Impact:**
- Zero user-reported bugs related to unit assignments
- Team productivity maintained or improved
- Confidence in data accuracy increased

---

## Support & Troubleshooting

### Common Issues

**Issue 1: FK constraint violation when deleting records**
```
Error 1451: Cannot delete or update a parent row
```
**Solution:** This is expected behavior. Cannot delete customer_location if contracts exist. Reassign contracts first or use soft delete.

**Issue 2: VIEW query slow**
```sql
-- Check if indexes exist
SHOW INDEX FROM kontrak_unit;
-- Should have idx_kontrak_unit_active and idx_kontrak_unit_kontrak

-- If missing, add indexes:
CREATE INDEX idx_kontrak_unit_active 
ON kontrak_unit(unit_id, status, is_temporary);
```

**Issue 3: Unmatched marketing names**
- Review list from Step 3.3
- Create users manually or map to existing
- Re-run mapping query

---

## Contact

**Technical Lead:** [Name]  
**Email:** [email]  
**Slack:** #dev-team  
**Emergency:** [phone]  

---

## Files in This Phase

```
databases/migrations/
├── phase1a_step1_audit_inventory_unit_redundancy.sql
├── phase1a_step2_create_vw_unit_with_contracts.sql
├── phase1a_step3_add_missing_fk_constraints.sql
├── phase1a_step4_drop_redundant_columns.sql
└── README_PHASE1A.md (this file)
```

---

## Next Phase

After Phase 1A complete, proceed to:
**Phase 1B - Database Column Renaming (Indonesian → English)**

See: `docs/MARKETING_MODULE_REFACTORING_COMPLETE_PLAN.md` Section "Phase 1B"

---

**Last Updated:** March 4, 2026  
**Version:** 1.0  
**Status:** Ready for Execution
