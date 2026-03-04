 # Phase 1A: Quick Deployment Guide
**Target Environment:** Staging → Production  
**Deployment Window:** Saturday 22:00 - 02:00 (4 hours)  
**Team Size:** 4 people (DBA, Developer, Tester, Monitor)

---

## ⏱️ Timeline Overview

| Time | Duration | Activity | Owner |
|------|----------|----------|-------|
| 21:30 | 30 min | Pre-deployment briefing | All |
| 22:00 | 30 min | Backup & validation | DBA |
| 22:30 | 15 min | Deploy code changes | Developer |
| 22:45 | 20 min | Run migration steps 1-3 | DBA |
| 23:05 | 30 min | Smoke testing | Tester |
| 23:35 | 60 min | Monitor & validate | All |
| 00:35 | 30 min | Performance testing | Monitor |
| 01:05 | 25 min | Final validation | All |
| 01:30 | 30 min | Documentation & handoff | Developer |
| 02:00 | - | Deployment complete | - |

**Buffer Time:** 30 minutes built into each phase

---

## 👥 Team Roles

### DBA (Database Administrator)
- Backup verification
- Migration script execution
- Data validation
- Rollback coordinator (if needed)

### Developer
- Code deployment via Git
- Application configuration
- Bug fixing (if issues found)
- Technical documentation

### Tester
- Smoke test execution
- Workflow validation
- User scenario testing
- Defect reporting

### Monitor
- Log monitoring
- Performance tracking
- Error detection
- Metrics collection

---

## 📋 Pre-Deployment (21:30 - 22:00)

### Briefing Checklist

- [ ] All team members present
- [ ] Review timeline and roles
- [ ] Review rollback criteria
- [ ] Confirm backups scheduled
- [ ] Test communication channels (Slack/WhatsApp)
- [ ] Verify access to all systems

### Required Access

- [ ] Database server SSH access
- [ ] Git repository access
- [ ] Application server access
- [ ] Monitoring dashboards
- [ ] Rollback script location

---

## 🗄️ Database Backup (22:00 - 22:30)

### DBA Tasks

```bash
# 1. Create full database backup (30 minutes)
cd /path/to/backups
mysqldump -u root -p optima_ci > optima_backup_phase1a_$(date +%Y%m%d_%H%M%S).sql

# 2. Verify backup file size
ls -lh optima_backup_phase1a_*.sql

# 3. Test backup integrity (sample restore test)
mysql -u root -p -e "CREATE DATABASE optima_backup_test;"
mysql -u root -p optima_backup_test < optima_backup_phase1a_*.sql | head -100

# 4. Create backup of specific critical tables
mysqldump -u root -p optima_ci inventory_unit kontrak kontrak_unit > phase1a_critical_tables_backup.sql

# 5. Copy backups to safe location
cp optima_backup_phase1a_*.sql /backup/remote/location/
```

### Validation

- [ ] Backup file > 100MB (or expected size)
- [ ] Sample restore successful
- [ ] Backup copied to remote location
- [ ] Backup checksum recorded

**GO/NO-GO Decision Point #1**  
✅ Proceed if all backups verified  
❌ STOP if backup fails

---

## 💻 Code Deployment (22:30 - 22:45)

### Developer Tasks

```bash
# 1. Pull latest code from main branch
cd /var/www/optima
git fetch origin
git checkout main
git pull origin main

# 2. Verify correct commit
git log -1 --oneline
# Should show: "Phase 1A: Junction table refactoring complete"

# 3. Check file changes
git diff HEAD~1 --name-only
# Should list 13 PHP files

# 4. Set proper permissions
chown -R www-data:www-data app/
chmod -R 755 app/

# 5. Clear application cache
php spark cache:clear

# 6. Restart PHP-FPM (if needed)
sudo systemctl restart php8.5-fpm
```

### Validation

- [ ] Git pull successful
- [ ] Correct commit hash
- [ ] 13 files changed
- [ ] No syntax errors (check logs)
- [ ] Application accessible

**GO/NO-GO Decision Point #2**  
✅ Proceed if code deployed successfully  
❌ ROLLBACK code if deployment fails

---

## 🔄 Migration Execution (22:45 - 23:05)

### Step 1: Audit Redundancy (5 min)

```bash
# Execute audit query
mysql -u root -p optima_ci < databases/migrations/phase1a_step1_audit_inventory_unit_redundancy.sql > audit_results.txt

# Check results
cat audit_results.txt
```

**Expected Result:** `0 mismatches found`  
**Action if mismatches found:** ⚠️ STOP - Investigate data integrity issues

- [ ] Audit executed
- [ ] Zero mismatches confirmed
- [ ] Results documented

---

### Step 2: Create VIEW (5 min)

```bash
# Create vw_unit_with_contracts VIEW
mysql -u root -p optima_ci < databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql

# Verify VIEW created
mysql -u root -p optima_ci -e "SHOW FULL TABLES WHERE Table_type = 'VIEW';" | grep vw_unit_with_contracts

# Test VIEW query
mysql -u root -p optima_ci -e "SELECT COUNT(*) FROM vw_unit_with_contracts;"
```

**Expected Result:** VIEW exists and returns data

- [ ] VIEW created successfully
- [ ] Sample query returns results
- [ ] No errors in error log

---

### Step 3: Add FK Constraints (10 min)

```bash
# Add foreign key constraints
mysql -u root -p optima_ci < databases/migrations/phase1a_step3_add_missing_fk_constraints.sql

# Verify constraints added
mysql -u root -p optima_ci -e "
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'optima_ci'
    AND CONSTRAINT_NAME LIKE 'fk_kontrak_unit_%'
;"
```

**Expected Result:** 2-3 new FK constraints added

- [ ] FK constraints added
- [ ] No constraint violation errors
- [ ] Constraints visible in SHOW CREATE TABLE

**GO/NO-GO Decision Point #3**  
✅ Proceed to smoke testing if migration successful  
❌ ROLLBACK if any step fails

---

## 🧪 Smoke Testing (23:05 - 23:35)

### Tester Tasks

#### Test Scenario 1: View Unit Inventory (5 min)

1. Login to application
2. Navigate to: **Warehouse → Unit Inventory**
3. Verify: Units display with customer/location info
4. Check: No PHP errors in browser console

- [ ] Page loads successfully
- [ ] Customer names display correctly
- [ ] Location names display correctly
- [ ] No JavaScript errors

---

#### Test Scenario 2: Create Quotation (10 min)

1. Navigate to: **Marketing → Quotation → Create**
2. Select customer
3. Select location
4. Add units to quotation
5. Save quotation

- [ ] Available units shown (without active contracts)
- [ ] Quotation created successfully
- [ ] No errors during save

---

#### Test Scenario 3: View Contract Details (5 min)

1. Navigate to: **Marketing → Contracts**
2. Select any active contract
3. View contract details
4. Verify units assigned to contract display

- [ ] Contract details load
- [ ] Units list correctly
- [ ] Customer/location info correct

---

#### Test Scenario 4: Create Work Order (10 min)

1. Navigate to: **Operations → Work Orders**
2. Create new work order
3. Select unit with active contract
4. Verify customer info auto-populated

- [ ] Unit selection works
- [ ] Customer auto-populated
- [ ] Work order created successfully

---

#### Test Scenario 5: Generate Report (5 min)

1. Navigate to: **Reports → Rental Report**
2. Set date range (last month)
3. Generate report
4. Export to Excel

- [ ] Report generates
- [ ] Customer/unit data correct
- [ ] Export works

**Critical Issues Found:** __________  
**Minor Issues Found:** __________

**GO/NO-GO Decision Point #4**  
✅ Proceed if no critical issues  
⚠️ Fix critical issues before continuing  
❌ ROLLBACK if unfixable critical issues

---

## 📊 Monitoring & Validation (23:35 - 00:35)

### Monitor Tasks

#### Error Log Monitoring

```bash
# Watch application error log
tail -f /var/www/optima/writable/logs/log-$(date +%Y-%m-%d).log | grep -i error

# Watch MySQL slow query log
tail -f /var/log/mysql/slow-query.log

# Check PHP-FPM error log
tail -f /var/log/php8.5-fpm.log
```

**Alert If:**
- Database connection errors
- Query execution errors
- PHP fatal errors
- Timeout errors

---

#### Performance Monitoring

```bash
# Monitor query performance
mysql -u root -p optima_ci -e "
SELECT 
    SUBSTRING(sql_text, 1, 100) AS query,
    exec_count,
    avg_timer_wait/1000000000000 AS avg_time_sec
FROM performance_schema.events_statements_summary_by_digest
WHERE last_seen > NOW() - INTERVAL 1 HOUR
ORDER BY avg_timer_wait DESC
LIMIT 20;
"
```

**Target Metrics:**
- [ ] getCurrentContract() < 50ms
- [ ] Unit listing queries < 200ms
- [ ] Report generation < 2 seconds
- [ ] No queries > 5 seconds

---

#### Database Health Check

```sql
-- Check kontrak_unit junction table usage
SELECT 
    COUNT(*) as total_assignments,
    COUNT(DISTINCT unit_id) as unique_units,
    COUNT(DISTINCT kontrak_id) as unique_contracts
FROM kontrak_unit;

-- Verify no orphaned records
SELECT COUNT(*) as orphaned_units
FROM kontrak_unit ku
LEFT JOIN kontrak k ON ku.kontrak_id = k.id
WHERE k.id IS NULL;

-- Check VIEW performance
SELECT COUNT(*) FROM vw_unit_with_contracts;
```

**Expected:**
- [ ] Junction table has data
- [ ] Zero orphaned records
- [ ] VIEW queries fast

---

## ⚡ Performance Testing (00:35 - 01:05)

### Load Testing Scenarios

#### Concurrent User Test (15 min)

```bash
# Simulate 50 concurrent users viewing unit inventory
ab -n 500 -c 50 http://optima.app/warehouse/units

# Simulate 20 users creating quotations
ab -n 200 -c 20 http://optima.app/marketing/quotations
```

**Success Criteria:**
- [ ] 95% of requests < 1 second response time
- [ ] 0% error rate
- [ ] Server CPU < 70%
- [ ] Database connections < 100

---

#### Report Generation Test (10 min)

1. Generate rental report (last 3 months)
2. Generate customer report
3. Generate unit usage report

**Success Criteria:**
- [ ] All reports complete < 5 seconds
- [ ] Data accuracy verified (spot check)
- [ ] Excel export works

---

## ✅ Final Validation (01:05 - 01:30)

### All Team Members

#### Validation Checklist

- [ ] No errors in last 2 hours of logs
- [ ] All smoke tests passed
- [ ] Performance meets SLA
- [ ] No database integrity issues
- [ ] Team confident in deployment

#### Data Verification

```sql
-- Sample data check: Unit contracts
SELECT 
    iu.no_unit_na,
    ku.status as assignment_status,
    k.no_kontrak,
    c.customer_name,
    cl.location_name
FROM inventory_unit iu
LEFT JOIN kontrak_unit ku ON iu.id_inventory_unit = ku.unit_id AND ku.status = 'ACTIVE'
LEFT JOIN kontrak k ON ku.kontrak_id = k.id
LEFT JOIN customer_locations cl ON k.customer_location_id = cl.id
LEFT JOIN customers c ON cl.customer_id = c.id
WHERE iu.id_inventory_unit IN (1, 2, 3, 4, 5)  -- Sample units
;
```

- [ ] Data looks correct
- [ ] No nulls where unexpected
- [ ] Relationships intact

---

## 📝 Documentation & Handoff (01:30 - 02:00)

### Developer Tasks

#### Create Deployment Report

```markdown
# Phase 1A Staging Deployment Report
**Date:** [DATE]
**Start Time:** 22:00
**End Time:** 02:00
**Status:** SUCCESS / PARTIAL / FAILED

## Deployment Summary
- Code deployed: ✅/❌
- Migration step 1 (Audit): ✅/❌
- Migration step 2 (VIEW): ✅/❌
- Migration step 3 (FK): ✅/❌
- Smoke tests: ✅/❌

## Issues Found
[List any issues and resolutions]

## Performance Metrics
- getCurrentContract(): ____ ms
- Unit listing: ____ ms
- Report generation: ____ sec

## Next Steps
[UAT plan, monitoring, etc.]
```

---

#### Update Team

- [ ] Post deployment report to Slack/WhatsApp
- [ ] Email stakeholders
- [ ] Update project management board
- [ ] Schedule UAT kickoff meeting

---

## 🚨 Rollback Procedure

**If deployment must be rolled back:**

```bash
# 1. Run rollback script
cd /var/www/optima/databases/migrations
bash rollback_phase1a.sh

# 2. Revert code changes
git revert HEAD
git push origin main

# 3. Restore database if needed
mysql -u root -p optima_ci < /backups/optima_backup_phase1a_[timestamp].sql

# 4. Clear cache
php spark cache:clear

# 5. Restart services  
sudo systemctl restart php8.5-fpm

# 6. Notify team
[Send rollback notification]
```

**Rollback Criteria:**
- ❌ Critical errors in smoke testing
- ❌ Data corruption detected
- ❌ Performance degradation > 50%
- ❌ Migration script fails

---

## 📞 Emergency Contacts

- **DBA Lead:** [Name] - [Phone]
- **Developer Lead:** [Name] - [Phone]
- **System Admin:** [Name] - [Phone]
- **Project Manager:** [Name] - [Phone]

---

## ✅ Success Criteria

Deployment considered successful if:

- ✅ All 3 migration steps executed without errors
- ✅ All 5 smoke test scenarios pass
- ✅ Performance meets SLA (<50ms for getCurrentContract)
- ✅ Zero critical errors in logs during monitoring period
- ✅ Data integrity verification passes
- ✅ Team consensus on deployment quality

---

**Estimated Duration:** 4 hours  
**Actual Duration:** _____ hours  
**Status:** _____ (Success/Partial/Failed)  
**Next Deployment (Production):** After 2-3 weeks UAT

---

**Good luck team! 🚀**
