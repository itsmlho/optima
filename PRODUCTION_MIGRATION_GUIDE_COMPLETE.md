# 📋 Production Migration Guide - February 2026 (COMPLETE)

## ⚠️ UPDATED: Versi Lengkap dengan SEMUA Perubahan

File migration baru: **PRODUCTION_MIGRATION_FEBRUARY_2026_COMPLETE.sql**

---

## Database Information
- **Production Database**: `u138256737_optima_db`
- **Hostname**: `localhost` (via auth-db1866.hstgr.io)
- **Username**: `u138256737_root_optima`
- **Migration File**: `PRODUCTION_MIGRATION_FEBRUARY_2026_COMPLETE.sql`

---

## 🎯 Summary Perubahan LENGKAP

### New Tables (8)
1. ✅ **contract_amendments** - Perubahan kontrak dengan prorate
2. ✅ **amendment_unit_rates** - Rate changes per unit
3. ✅ **unit_billing_schedules** - Billing schedule per unit (staggered delivery)
4. ✅ **contract_renewal_workflow** - Workflow renewal kontrak
5. ✅ **operators** - Master data operator/mechanic/driver
6. ✅ **contract_po_history** - History PO number per contract (monthly PO)
7. ✅ **contract_operator_assignments** - Assignment operator ke contract
8. ✅ **kontrak_locations** - Many-to-many kontrak ↔ location

### Modified Tables (6)

#### 1. **kontrak** (15 new columns!)
- `billing_method` - CYCLE / PRORATE / MONTHLY_FIXED
- `billing_notes` - Special billing instructions
- `billing_start_date` - Custom billing start
- `document_type` - KONTRAK / PO / AGREEMENT / RECURRING_PO
- `fast_track` - Quick rental approval
- `spot_rental_number` - Spot rental tracking
- `estimated_duration_days` - Estimated duration
- `actual_return_date` - Actual return date
- `requires_po_approval` - PO approval flag
- `parent_contract_id` - Renewal chain tracking
- `is_renewal` - Renewal flag
- `renewal_generation` - Renewal generation (1, 2, 3...)
- `renewal_initiated_at` - Renewal start timestamp
- `renewal_initiated_by` - User who initiated renewal

#### 2. **customers** (1 new column)
- `default_billing_method` - Default billing preference

#### 3. **customer_contracts** (1 new column + constraint change)
- `contract_type` - KONTRAK_ONLY / PO_ONLY / BOTH / RECURRING_PO / NONE
- `kontrak_id` - Changed to NULLABLE (for customers without contracts)

#### 4. **inventory_unit** (1 new column)
- `rate_changed_at` - Rate change timestamp

#### 5. **quotation_specifications** (5 new columns - CONDITIONAL)
- `include_operator` - Include operator in quote
- `operator_monthly_rate` - Operator monthly rate
- `operator_daily_rate` - Operator daily rate
- `operator_description` - Operator description
- `operator_certification_required` - Certification required flag

#### 6. **invoice_items** (7 new columns - CONDITIONAL)
- `item_type` - UNIT_RENTAL / OPERATOR_SERVICE / MAINTENANCE / etc
- `operator_assignment_id` - Link to operator assignment
- `operator_name` - Operator name snapshot
- `unit_id` - Link to unit
- `unit_number` - Unit number snapshot
- `billing_period_start` - Billing period start date
- `billing_period_end` - Billing period end date
- `billing_days` - Number of billing days

#### 7. **invoices** (3 new columns - CONDITIONAL)
- `po_history_id` - Link to contract_po_history
- `po_number_snapshot` - PO number at invoice time
- `po_date_snapshot` - PO date at invoice time

### Indexes Created (15+)
- Billing method indexes
- Operator status/certification indexes
- PO history indexes
- Assignment tracking indexes
- Renewal chain indexes
- Location relationship indexes

---

## 🚨 CRITICAL: Pre-Migration Checklist

### Before Running Migration:

- [ ] **BACKUP DATABASE** (MANDATORY!)
  ```bash
  # Via phpMyAdmin or automated backup
  mysqldump -u u138256737_root_optima -p u138256737_optima_db > backup_$(date +%Y%m%d).sql
  ```

- [ ] **Verify Disk Space** (estimated: 100-200 MB additional)

- [ ] **Review Conditional Migrations**
  - `invoices` table (will skip if not exists)
  - `invoice_items` table (will skip if not exists)
  - `quotation_specifications` table (will skip if not exists)
  - `unit` table (foreign key will skip if not exists)

- [ ] **Schedule Maintenance Window**
  - Estimated time: **5-10 minutes**
  - Best time: Off-peak hours (late night/early morning)

- [ ] **Notify Team**
  - Application may need restart after migration
  - Some features may not work until code deployment

---

## 📝 Migration Steps

### Step 1: Backup Database
```bash
# Via phpMyAdmin:
# 1. Login: https://auth-db1866.hstgr.io/
# 2. Select database: u138256737_optima_db
# 3. Click "Export" → "Go" (save backup)
```

### Step 2: Execute Migration
```bash
# Via phpMyAdmin:
# 1. Open: https://auth-db1866.hstgr.io/
# 2. Select database: u138256737_optima_db
# 3. Click tab "SQL"
# 4. Upload: PRODUCTION_MIGRATION_FEBRUARY_2026_COMPLETE.sql
# 5. Click "Go"
# 6. Wait for completion (5-10 minutes)
```

### Step 3: Verify Results
Run verification queries at end of migration file:
```sql
-- Check new tables count
SELECT 'NEW TABLES' as category, COUNT(*) as count
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'u138256737_optima_db' 
  AND TABLE_NAME IN (
    'contract_amendments', 'amendment_unit_rates', 
    'operators', 'contract_po_history', etc...
  );
-- Expected: 8

-- Check kontrak columns
-- Expected: 15 new columns

-- Check other tables
-- Check for any errors
```

### Step 4: Test Application
- [ ] Login to application
- [ ] Create new contract
- [ ] View existing contracts
- [ ] Check unit assignments
- [ ] Test billing features

---

## 🔄 Rollback Plan

### Full Rollback (Restore from Backup)
```bash
# Via phpMyAdmin:
# 1. Drop database
# 2. Create database
# 3. Import backup file
```

### Partial Rollback (Remove New Tables Only)
```sql
-- Drop new tables (preserves existing data)
DROP TABLE IF EXISTS amendment_unit_rates;
DROP TABLE IF EXISTS contract_amendments;
DROP TABLE IF EXISTS contract_operator_assignments;
DROP TABLE IF EXISTS contract_po_history;
DROP TABLE IF EXISTS contract_renewal_workflow;
DROP TABLE IF EXISTS kontrak_locations;
DROP TABLE IF EXISTS operators;
DROP TABLE IF EXISTS unit_billing_schedules;

-- Remove new columns (more complex - refer to backup)
```

---

## ⚠️ Known Issues & Solutions

### Issue 1: Table Already Exists Error
**Symptom**: "Table 'operators' already exists"
**Cause**: Migration ran before or partial migration exists
**Solution**: Use `CREATE TABLE IF NOT EXISTS` (already in migration)

### Issue 2: Foreign Key Constraint Error
**Symptom**: "Cannot add foreign key constraint"
**Cause**: Referenced table doesn't exist or column type mismatch
**Solution**: 
- Check if `kontrak.id` is INT UNSIGNED
- Check if `customer_locations.id` exists
- Migration uses conditional FK creation

### Issue 3: Column Already Exists
**Symptom**: "Duplicate column name 'billing_method'"
**Cause**: Column added previously
**Solution**: Use `ADD COLUMN IF NOT EXISTS` (already in migration)

### Issue 4: Slow Performance
**Symptom**: Migration takes >15 minutes
**Cause**: Large dataset + indexing
**Solution**:
- Run during off-peak hours
- Check server resources
- Consider breaking into smaller batches

---

## 📊 Expected Results

### Before Migration
```
Tables: ~50-60
Kontrak columns: ~25-30
Customer_contracts: kontrak_id NOT NULL
No operator tracking
No PO history tracking
No renewal workflow
```

### After Migration
```
Tables: ~58-68 (+8)
Kontrak columns: ~40-45 (+15)
Customer_contracts: kontrak_id NULLABLE
8 new tables created
20+ new columns added
15+ indexes created
```

---

## 🧪 Verification Queries

```sql
-- 1. Count all new tables
SELECT TABLE_NAME 
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = 'u138256737_optima_db'
  AND TABLE_NAME IN (
    'contract_amendments', 'amendment_unit_rates',
    'unit_billing_schedules', 'contract_renewal_workflow',
    'operators', 'contract_po_history',
    'contract_operator_assignments', 'kontrak_locations'
  );

-- 2. Check kontrak columns
SHOW COLUMNS FROM kontrak WHERE Field IN (
  'billing_method', 'document_type', 'fast_track',
  'parent_contract_id', 'is_renewal', 'renewal_generation'
);

-- 3. Verify operators table
SELECT COUNT(*) as operator_count FROM operators;

-- 4. Check kontrak_locations data
SELECT COUNT(*) as location_mappings FROM kontrak_locations;

-- 5. Verify document_type distribution
SELECT document_type, COUNT(*) FROM kontrak GROUP BY document_type;
```

---

## 🎯 Features Enabled After Migration

### 1. **Contract Amendments**
- Mid-period rate changes with prorate calculation
- Amendment approval workflow
- Unit-level rate tracking

### 2. **Operator Management**
- Master data for operators/mechanics/drivers
- Assignment tracking per contract
- Billing integration (operator service charges)

### 3. **PO History Tracking**
- Multiple PO numbers per contract
- Monthly PO rotation support
- PO snapshot in invoices

### 4. **Flexible Billing**
- CYCLE (30-day rolling) billing
- PRORATE (month-end) billing
- MONTHLY_FIXED (fixed date) billing
- Unit-level billing schedules

### 5. **Contract Renewal**
- Renewal chain tracking (parent → child)
- Renewal generation tracking
- Workflow approval stages

### 6. **Location Flexibility**
- Many-to-many: 1 contract → multiple locations
- Different rates per location
- Location-specific tracking

### 7. **Document Classification**
- KONTRAK (fixed contract)
- PO (purchase order)
- AGREEMENT (verbal/basic)
- RECURRING_PO (monthly PO)
- STATUS_PENDING (draft)

---

## 📞 Support & Issues

**If migration fails:**
1. **DO NOT PANIC** - Your backup is safe
2. Copy the error message
3. Restore from backup if needed
4. Contact development team with:
   - Error message
   - Step that failed
   - Database state (which tables exist)

---

## ✅ Post-Migration Checklist

- [ ] All 8 tables created successfully
- [ ] Kontrak table has 15+ new columns
- [ ] Customers table has default_billing_method
- [ ] Contract_po_history linked to contracts
- [ ] Operators table has sample data (4 operators)
- [ ] kontrak_locations migrated from existing data
- [ ] Document_type classified correctly
- [ ] No error messages in migration log
- [ ] Application login works
- [ ] Contract creation works
- [ ] Billing features accessible
- [ ] Team notified of completion

---

## 📁 Related Files

- **Main Migration**: `PRODUCTION_MIGRATION_FEBRUARY_2026_COMPLETE.sql`
- **Old Migration (Incomplete)**: `PRODUCTION_MIGRATION_FEBRUARY_2026.sql`
- **Guide**: `PRODUCTION_MIGRATION_GUIDE_COMPLETE.md` (this file)

---

## 📈 Change Summary by Date

| Date | Changes | Tables | Columns |
|------|---------|--------|---------|
| 2026-02-10 | Amendments, Billing, Renewals | +3 | +10 |
| 2026-02-15 | Operators, PO History, Assignments | +4 | +15 |
| 2026-02-17 | Location Relationships | +1 | +2 |
| **TOTAL** | **12 migration files** | **+8** | **+27** |

---

**Migration Prepared**: February 17, 2026
**Version**: Complete (All Features)
**Status**: Ready for Production
**Priority**: Medium (Enables new features, no critical bugs)
**Risk Level**: Medium (Many changes, but safe rollback plan)
**Estimated Time**: 5-10 minutes
**Downtime Required**: Optional (recommended for safety)

---

## 🚀 Ready to Execute?

1. ✅ Backup completed
2. ✅ Off-peak time scheduled
3. ✅ Team notified
4. ✅ Migration file reviewed
5. ✅ Rollback plan understood

**→ Proceed with migration via phpMyAdmin**
