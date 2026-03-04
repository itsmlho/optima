# Phase 1A Deployment Checklist
## Data Integrity & Junction Table Migration

**Version:** 1.0  
**Created:** 2026-03-04  
**Deployment Window:** Saturday 22:00 - 02:00 (4 hours)  
**Environment:** Staging → Production

---

## Pre-Deployment Checklist (1 Week Before)

### Code Review & Validation
- [ ] All 42 queries reviewed and approved by senior developer
- [ ] Code merged to `main` branch via pull request
- [ ] Git tag created: `v1.0-phase1a`
- [ ] All syntax errors resolved (validated via `get_errors`)
- [ ] Zero remaining FK references confirmed (grep search)
- [ ] Code coverage > 80% for new methods

### Testing Validation
- [ ] All unit tests pass (5/5)
- [ ] All integration tests pass (4/4)
- [ ] All functional tests pass (42/42)
- [ ] Performance tests within SLA
- [ ] UAT sign-off received from all stakeholders
- [ ] Test execution report documented

### Documentation
- [ ] MARKETING_MODULE_REFACTORING_COMPLETE_PLAN.md updated
- [ ] PHASE1A_CONTROLLER_UPDATE_TRACKING.md shows 100% completion
- [ ] PHASE1A_TEST_PLAN.md execution completed
- [ ] README_PHASE1A.md reviewed and accurate
- [ ] Rollback procedures documented and tested

### Database Preparation
- [ ] Staging database has production-like data volume
- [ ] Test data created and validated (TEST-UNIT-* records)
- [ ] Audit query (Step 1) executed on staging - ZERO mismatches found
- [ ] Database indexes verified on `kontrak_unit` table
- [ ] Backup retention policy confirmed (keep backups for 30 days)

### Infrastructure Readiness
- [ ] Staging environment accessible and stable
- [ ] Production database credentials verified
- [ ] Backup storage has sufficient space (estimate DB size × 2)
- [ ] Monitoring dashboards configured (CPU, Memory, Disk I/O)
- [ ] Error logging enabled (writable/logs/ writable, rotation configured)
- [ ] Slow query log enabled (threshold: 200ms)

### Team Coordination
- [ ] All stakeholders notified of deployment schedule
- [ ] On-call engineer assigned for deployment window
- [ ] Rollback team identified and briefed
- [ ] Communication channels active (Slack, WhatsApp, Email)
- [ ] Emergency contact list distributed

### Rollback Preparation
- [ ] Rollback drill completed successfully on staging
- [ ] Git revert commits prepared (not pushed)
- [ ] Database restore script tested
- [ ] Rollback decision criteria documented
- [ ] Rollback authority designated (who makes the call)

---

## Deployment Day - Morning Checks (D-Day 10:00)

### Final Validations
- [ ] No urgent production issues in last 48 hours
- [ ] All team members available and confirmed attendance
- [ ] VPN/SSH access to production servers working
- [ ] Database client tools (MySQL Workbench, DBeaver) ready
- [ ] Deployment scripts copied to production server staging area
- [ ] Code deployment package ready (ZIP or Git tag)

### Pre-Deployment Communication
- [ ] Email sent to all users: "Maintenance window 22:00-02:00"
- [ ] Website banner posted: "System maintenance tonight"
- [ ] Social media/WhatsApp broadcast: Scheduled maintenance notice
- [ ] Management briefed on deployment plan

---

## Deployment Window - Staging (Saturday 22:00)

### T-30min: Final Preparations
```bash
⏱️ 21:30 - 22:00
```

- [ ] **Connect to staging server**
  ```bash
  ssh user@staging.optima.local
  cd /var/www/optima
  ```

- [ ] **Verify application status**
  ```bash
  systemctl status nginx
  systemctl status php-fpm
  mysql -u root -p -e "SELECT COUNT(*) FROM inventory_unit;"
  ```

- [ ] **Check disk space**
  ```bash
  df -h
  # Ensure at least 20% free on /var/lib/mysql
  ```

- [ ] **Verify no active sessions**
  ```sql
  SELECT COUNT(*) as active_users FROM ci_sessions WHERE timestamp > UNIX_TIMESTAMP() - 300;
  -- Should be low (< 5 users)
  ```

### T-0: Enable Maintenance Mode
```bash
⏱️ 22:00
```

- [ ] **Activate maintenance mode**
  ```bash
  php spark down
  # Or create maintenance flag
  touch writable/maintenance.flag
  ```

- [ ] **Verify maintenance page displays**
  ```bash
  curl http://staging.optima.local
  # Should return "System under maintenance"
  ```

- [ ] **Announce maintenance started** (Slack/WhatsApp)

### Step 1: Database Backup (15 minutes)
```bash
⏱️ 22:00 - 22:15
```

- [ ] **Create timestamped backup**
  ```bash
  BACKUP_FILE="optima_staging_pre_phase1a_$(date +%Y%m%d_%H%M%S).sql"
  mysqldump -u root -p \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    optima_staging > /backups/$BACKUP_FILE
  ```

- [ ] **Verify backup file created**
  ```bash
  ls -lh /backups/$BACKUP_FILE
  # Should be several hundred MB
  ```

- [ ] **Compress backup**
  ```bash
  gzip /backups/$BACKUP_FILE
  ```

- [ ] **Test backup integrity**
  ```bash
  gunzip -t /backups/${BACKUP_FILE}.gz
  echo $?  # Should return 0 (success)
  ```

- [ ] **Record backup details**
  ```
  Backup File: _______________________
  File Size: _________ MB
  Backup Time: _______ minutes
  Checksum: md5sum /backups/${BACKUP_FILE}.gz
  ```

### Step 2: Execute Migration Step 1 - Audit (10 minutes)
```bash
⏱️ 22:15 - 22:25
```

- [ ] **Run audit query**
  ```bash
  mysql -u root -p optima_staging < databases/migrations/phase1a_step1_audit_inventory_unit_redundancy.sql > /tmp/audit_results.txt
  ```

- [ ] **Review audit results**
  ```bash
  cat /tmp/audit_results.txt
  ```

- [ ] **Verify ZERO mismatches**
  ```sql
  -- If mismatches found, STOP HERE
  -- Reconcile data before proceeding
  ```

  **Mismatch Resolution (if needed):**
  ```sql
  -- Find problematic records
  SELECT iu.id_inventory_unit, iu.nomor_mesin,
         iu.kontrak_id as old_kontrak,
         ku.kontrak_id as new_kontrak,
         iu.customer_id as old_customer,
         cl.customer_id as new_customer
  FROM inventory_unit iu
  INNER JOIN kontrak_unit ku ON ku.unit_id = iu.id_inventory_unit 
      AND ku.status IN ('AKTIF','DIPERPANJANG') 
      AND ku.is_temporary = 0
  INNER JOIN kontrak k ON k.id = ku.kontrak_id
  INNER JOIN customer_locations cl ON cl.id = k.customer_location_id
  WHERE iu.kontrak_id != ku.kontrak_id
     OR iu.customer_id != cl.customer_id
     OR iu.customer_location_id != k.customer_location_id;
  
  -- Update mismatched records
  -- (Manual review required - determine correct source of truth)
  ```

- [ ] **Audit Results:**
  ```
  Total Units Checked: _________
  Mismatches Found: ________ (MUST BE 0)
  Reconciliation Required: YES / NO
  ```

### Step 3: Execute Migration Step 2 - Create VIEW (5 minutes)
```bash
⏱️ 22:25 - 22:30
```

- [ ] **Create backward compatibility VIEW**
  ```bash
  mysql -u root -p optima_staging < databases/migrations/phase1a_step2_create_vw_unit_with_contracts.sql
  ```

- [ ] **Verify VIEW created**
  ```sql
  SHOW FULL TABLES WHERE Table_type = 'VIEW';
  -- Should include: vw_unit_with_contracts
  ```

- [ ] **Test VIEW query performance**
  ```sql
  EXPLAIN SELECT * FROM vw_unit_with_contracts LIMIT 100;
  -- Check: type should be 'ref' or 'eq_ref', not 'ALL'
  ```

- [ ] **Validate VIEW data**
  ```sql
  SELECT COUNT(*) FROM vw_unit_with_contracts;
  -- Should match inventory_unit count
  
  SELECT * FROM vw_unit_with_contracts 
  WHERE nomor_mesin LIKE 'TEST-%' 
  LIMIT 5;
  -- Verify kontrak_id, customer_id, customer_location_id populated
  ```

- [ ] **VIEW Performance:**
  ```
  Query Time: _______ ms (should be < 100ms)
  Rows Returned: _________
  Index Usage: GOOD / NEEDS OPTIMIZATION
  ```

### Step 4: Execute Migration Step 3 - Add FK Constraints (10 minutes)
```bash
⏱️ 22:30 - 22:40
```

- [ ] **CRITICAL: Update marketing_name mapping first**
  ```sql
  -- Verify no NULL marketing_name in kontrak_unit
  SELECT COUNT(*) FROM kontrak_unit WHERE marketing_name IS NULL;
  
  -- If NULLs found, update them:
  UPDATE kontrak_unit ku
  INNER JOIN kontrak k ON k.id = ku.kontrak_id
  INNER JOIN spk s ON s.kontrak_id = k.id
  INNER JOIN deal d ON d.id = s.deal_id
  INNER JOIN quotation q ON q.id = d.quotation_id
  INNER JOIN users u ON u.id = q.created_by
  SET ku.marketing_name = u.nama
  WHERE ku.marketing_name IS NULL;
  ```

- [ ] **Execute FK addition script**
  ```bash
  mysql -u root -p optima_staging < databases/migrations/phase1a_step3_add_missing_fk_constraints.sql
  ```

- [ ] **Verify FK constraints added**
  ```sql
  SELECT 
      CONSTRAINT_NAME,
      TABLE_NAME,
      REFERENCED_TABLE_NAME,
      REFERENCED_COLUMN_NAME
  FROM information_schema.KEY_COLUMN_USAGE
  WHERE TABLE_SCHEMA = 'optima_staging'
    AND TABLE_NAME = 'kontrak_unit'
    AND REFERENCED_TABLE_NAME IS NOT NULL;
  
  -- Expected:
  -- fk_kontrak_unit_unit_id -> inventory_unit(id_inventory_unit)
  -- fk_kontrak_unit_kontrak_id -> kontrak(id)
  ```

- [ ] **Test FK constraint enforcement**
  ```sql
  -- Should FAIL (referential integrity)
  INSERT INTO kontrak_unit (unit_id, kontrak_id, status) 
  VALUES (999999, 1, 'AKTIF');
  -- Expected: ERROR 1452 (23000): Cannot add or update a child row
  
  -- Should FAIL (foreign key constraint)
  DELETE FROM kontrak WHERE id = 1;
  -- Expected: ERROR 1451 (23000): Cannot delete or update a parent row
  ```

- [ ] **FK Constraints Status:**
  ```
  Constraints Added: _____ (should be 2)
  Integrity Test: PASS / FAIL
  Error Handling: WORKING / ISSUES
  ```

### Step 5: Code Deployment (15 minutes)
```bash
⏱️ 22:40 - 22:55
```

- [ ] **Backup current code**
  ```bash
  cd /var/www/optima
  tar -czf /backups/optima_code_$(date +%Y%m%d_%H%M%S).tar.gz \
    app/Controllers/ app/Models/
  ```

- [ ] **Deploy new code via Git**
  ```bash
  git fetch origin
  git checkout v1.0-phase1a
  git status  # Verify clean working directory
  ```

- [ ] **Verify file changes**
  ```bash
  git diff HEAD~1 HEAD --name-only
  # Should show 13 modified files (model + 12 controllers)
  ```

- [ ] **Set proper permissions**
  ```bash
  chown -R www-data:www-data app/
  chmod -R 755 app/
  ```

- [ ] **Clear application cache**
  ```bash
  php spark cache:clear
  rm -rf writable/cache/*
  ```

- [ ] **Verify PHP syntax**
  ```bash
  php -l app/Models/InventoryUnitModel.php
  php -l app/Controllers/Marketing.php
  # Should output: "No syntax errors detected"
  ```

- [ ] **Code Deployment:**
  ```
  Git Tag: v1.0-phase1a
  Files Modified: _____ (should be 13)
  Syntax Errors: _____ (MUST BE 0)
  Cache Cleared: YES / NO
  ```

### Step 6: Application Restart (5 minutes)
```bash
⏱️ 22:55 - 23:00
```

- [ ] **Restart PHP-FPM**
  ```bash
  systemctl restart php8.1-fpm
  systemctl status php8.1-fpm
  # Should show: active (running)
  ```

- [ ] **Restart web server**
  ```bash
  systemctl restart nginx
  systemctl status nginx
  # Should show: active (running)
  ```

- [ ] **Verify processes running**
  ```bash
  ps aux | grep php-fpm
  ps aux | grep nginx
  ```

- [ ] **Check log files for errors**
  ```bash
  tail -n 50 /var/log/nginx/error.log
  tail -n 50 /var/log/php8.1-fpm.log
  tail -n 50 /var/www/optima/writable/logs/log-$(date +%Y-%m-%d).log
  # Should see no CRITICAL or ERROR entries
  ```

### Step 7: Smoke Testing (30 minutes)
```bash
⏱️ 23:00 - 23:30
```

- [ ] **Disable maintenance mode**
  ```bash
  php spark up
  # Or remove maintenance flag
  rm writable/maintenance.flag
  ```

- [ ] **Test homepage loads**
  ```bash
  curl -I http://staging.optima.local
  # Should return: HTTP/1.1 200 OK
  ```

- [ ] **Test database connectivity**
  ```bash
  php spark migrate:status
  # Should connect without errors
  ```

#### Marketing Module Tests
- [ ] **Login as marketing user**
  ```
  URL: http://staging.optima.local/login
  User: marketing.test@optima.com
  Pass: [test password]
  ```

- [ ] **View quotation list**
  ```
  URL: /marketing/quotations
  Expected: List loads, customer names visible
  Query Used: Marketing.php:165 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **View quotation detail**
  ```
  URL: /marketing/quotation-detail/1
  Expected: Unit details show customer info
  Query Used: Marketing.php:260 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **View contract units**
  ```
  URL: /marketing/contract-units/1
  Expected: Units for contract listed correctly
  Query Used: Marketing.php:344 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **Create new quotation**
  ```
  Test: Create new quotation, add unit
  Expected: Save successful, data accurate
  Result: PASS / FAIL
  ```

#### Customer Management Tests
- [ ] **View customer list**
  ```
  URL: /customer-management/customers
  Expected: Customer list with unit counts
  Query Used: CustomerManagement.php:163 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **View customer detail**
  ```
  URL: /customer-management/customer-detail/1
  Expected: Customer units listed correctly
  Query Used: CustomerManagement.php:298 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **Export customer units**
  ```
  URL: /customer-management/export-units/1
  Expected: Excel file downloads, data accurate
  Query Used: CustomerManagement.php:1948 (junction table)
  Result: PASS / FAIL
  ```

#### Work Order Tests
- [ ] **View work order list**
  ```
  URL: /work-order/list
  Expected: List loads with unit info
  Query Used: WorkOrder.php:114 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **Create work order**
  ```
  Test: Create WO, assign unit
  Expected: Unit customer info correct
  Query Used: WorkOrder.php:845, 1733 (junction table)
  Result: PASS / FAIL
  ```

#### Warehouse Tests
- [ ] **View inventory**
  ```
  URL: /warehouse/inventory
  Expected: Inventory list with customer info
  Query Used: Warehouse.php:677-678 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **View unit detail**
  ```
  URL: /warehouse/unit-detail/1
  Expected: Full unit detail with contract info
  Query Used: Warehouse.php:736-738 (junction table)
  Result: PASS / FAIL
  ```

- [ ] **Export inventory**
  ```
  URL: /warehouse/export-inventory
  Expected: Excel downloads with customer data
  Query Used: Warehouse.php:334 (junction table)
  Result: PASS / FAIL
  ```

#### Performance Validation
- [ ] **Check query execution times**
  ```sql
  -- Enable profiling
  SET profiling = 1;
  
  -- Test critical queries
  SELECT * FROM vw_unit_with_contracts LIMIT 100;
  
  -- Check execution time
  SHOW PROFILES;
  -- All queries should be < 200ms
  ```

- [ ] **Check slow query log**
  ```bash
  tail -n 50 /var/log/mysql/slow-query.log
  # Should have no new entries
  ```

- [ ] **Performance Metrics:**
  ```
  Average Query Time: _______ ms (< 200ms SLA)
  Slow Queries (>200ms): _____ (should be 0)
  Page Load Time: _______ s (< 2s SLA)
  ```

#### Error Log Review
- [ ] **Check application logs**
  ```bash
  tail -n 100 /var/www/optima/writable/logs/log-$(date +%Y-%m-%d).log | grep -i error
  # Should see NO ERRORS related to junction table queries
  ```

- [ ] **Check PHP errors**
  ```bash
  tail -n 50 /var/log/php8.1-fpm.log
  # Should have no fatal errors, warnings acceptable
  ```

- [ ] **Check database errors**
  ```bash
  tail -n 50 /var/log/mysql/error.log
  # Should have no connection errors or query failures
  ```

### Smoke Test Results Summary
```
Total Tests: 16
Passed: _____
Failed: _____
Blocked: _____

Critical Issues Found: _____ (should be 0)
Minor Issues Found: _____

Decision: PROCEED TO PRODUCTION / ROLLBACK / INVESTIGATE
```

### Step 8: Monitoring Setup (10 minutes)
```bash
⏱️ 23:30 - 23:40
```

- [ ] **Enable enhanced logging**
  ```bash
  # Set log level to DEBUG for first 48 hours
  vi app/Config/Logger.php
  # Change threshold to: 'threshold' => 9
  ```

- [ ] **Set up log monitoring**
  ```bash
  # Tail application logs in separate terminal
  tail -f writable/logs/log-*.log
  
  # Tail slow query log
  tail -f /var/log/mysql/slow-query.log
  ```

- [ ] **Configure alerting**
  ```bash
  # Set up email alerts for errors (if available)
  # Configure Slack webhook for critical errors
  ```

- [ ] **Verify monitoring dashboards**
  ```
  - [ ] CPU usage dashboard
  - [ ] Memory usage dashboard
  - [ ] Database connections dashboard
  - [ ] Query execution time dashboard
  - [ ] Error rate dashboard
  ```

### Step 9: Post-Deployment Communication (5 minutes)
```bash
⏱️ 23:40 - 23:45
```

- [ ] **Announce deployment complete**
  ```
  Slack: "@channel Phase 1A staging deployment COMPLETE ✅"
  WhatsApp: "Deployment successful. Monitoring for 48 hours."
  Email: "Phase 1A deployed to staging - UAT can begin Monday"
  ```

- [ ] **Update deployment log**
  ```
  Deployment Date: 2026-03-04
  Start Time: 22:00
  End Time: _____
  Duration: _____ minutes (target: 120 min)
  Status: SUCCESS / FAILED / PARTIAL
  Issues: _________________________
  ```

- [ ] **Schedule follow-up meeting**
  ```
  Meeting: Phase 1A Post-Deployment Review
  Date: Monday 10:00
  Attendees: Dev team, QA, DevOps, Product Owner
  ```

### Final Checklist
```bash
⏱️ 23:45 - 00:00
```

- [ ] All migration steps completed successfully
- [ ] All smoke tests passed
- [ ] No critical errors in logs
- [ ] Performance within acceptable range
- [ ] Monitoring active
- [ ] Team notified
- [ ] Documentation updated
- [ ] Staging deployment COMPLETE ✅

---

## Post-Deployment Monitoring (48 Hours)

### Hour 1-24: Intensive Monitoring

**Every 2 Hours:**
- [ ] Check application logs for errors
- [ ] Check slow query log
- [ ] Monitor CPU/Memory usage
- [ ] Test critical workflows
- [ ] Check user feedback

**Monitoring Log:**
```
| Time  | CPU % | Memory % | Errors | Slow Queries | Notes |
|-------|-------|----------|--------|--------------|-------|
| 02:00 |       |          |        |              |       |
| 04:00 |       |          |        |              |       |
| 06:00 |       |          |        |              |       |
| 08:00 |       |          |        |              |       |
| 10:00 |       |          |        |              |       |
| 12:00 |       |          |        |              |       |
| 14:00 |       |          |        |              |       |
| 16:00 |       |          |        |              |       |
| 18:00 |       |          |        |              |       |
| 20:00 |       |          |        |              |       |
| 22:00 |       |          |        |              |       |
```

### Hour 24-48: Regular Monitoring

**Every 4 Hours:**
- [ ] Check error logs
- [ ] Review performance metrics
- [ ] Collect user feedback

### Week 1: Daily Checks

**Daily at 09:00 and 17:00:**
- [ ] Review error summary
- [ ] Check slow query log
- [ ] UAT progress review
- [ ] Bug tracker review

---

## Production Deployment Checklist (Week 5)

### Pre-Production (3 Days Before)

- [ ] Staging has been stable for 2+ weeks
- [ ] All UAT feedback addressed
- [ ] Zero blocking bugs
- [ ] Performance validated
- [ ] Stakeholder approval received
- [ ] Production deployment scheduled
- [ ] All teams notified

### Production Deployment (Same Steps as Staging)

**Use this same checklist for production with these changes:**
```
Environment: optima_production
Database: optima_production
Backup File: optima_production_pre_phase1a_YYYYMMDD_HHMMSS.sql
Deployment Window: Saturday 22:00 - 02:00
```

**Additional Production-Specific Steps:**
- [ ] Announce maintenance 3 days in advance
- [ ] Prepare customer communications
- [ ] Double-check backup restoration procedure
- [ ] Have senior developer on standby
- [ ] Manager approval for go-live

---

## Rollback Decision Tree

```
┌─────────────────────────────────────────┐
│ Is there a CRITICAL issue preventing   │
│ core functionality?                     │
└─────────────┬───────────────────────────┘
              │
              ├── YES ──► ROLLBACK IMMEDIATELY
              │           (Use Scenario 1 or 2)
              │
              └── NO
                   │
   ┌───────────────┴──────────────────┐
   │ Are there MODERATE issues that   │
   │ affect some users?                │
   └───────────┬──────────────────────┘
               │
               ├── YES ──► INVESTIGATE (1 hour max)
               │           │
               │           ├─► Fix available? ──► HOTFIX
               │           └─► No quick fix? ──► ROLLBACK
               │
               └── NO ──► MONITOR & PROCEED
                          Continue with UAT
```

**CRITICAL Issues (Immediate Rollback):**
- Database corruption
- Data loss detected
- System completely inaccessible
- FK constraints preventing normal operations
- Mass user complaints (> 10 users in 1 hour)

**MODERATE Issues (Investigate):**
- Specific workflow broken
- Performance degradation > 50%
- Non-critical feature unavailable
- Isolated user complaints (< 5 users)

**MINOR Issues (Monitor):**
- Visual glitches
- Performance degradation < 20%
- Non-critical warnings in logs
- Individual user issues

---

## Contacts & Emergency Procedures

### Primary Team
- **Deployment Lead:** [Name] - [Phone] - [Email]
- **Database Admin:** [Name] - [Phone] - [Email]
- **DevOps Engineer:** [Name] - [Phone] - [Email]
- **On-Call Developer:** [Name] - [Phone] - [Email]

### Emergency Escalation
1. **Issues during deployment:** Call Deployment Lead immediately
2. **Critical database issue:** Call Database Admin
3. **Infrastructure issue:** Call DevOps Engineer
4. **Code bug:** Call On-Call Developer

### Emergency Rollback Authority
**Only these people can authorize rollback:**
1. Deployment Lead
2. CTO
3. Development Manager

**Rollback Command:**
```bash
# Execute immediately upon authorization
./databases/migrations/rollback_phase1a.sh
```

---

## Final Sign-Off

### Staging Deployment
- [ ] Deployment Lead: __________________ Date: ______
- [ ] Database Admin: __________________ Date: ______
- [ ] QA Lead: _________________________ Date: ______

### Production Deployment
- [ ] Deployment Lead: __________________ Date: ______
- [ ] Database Admin: __________________ Date: ______
- [ ] QA Lead: _________________________ Date: ______
- [ ] Product Owner: ___________________ Date: ______
- [ ] CTO: _____________________________ Date: ______

---

**Checklist Status:** Ready for Use  
**Last Updated:** 2026-03-04  
**Next Review:** After Staging Deployment

---

END OF DEPLOYMENT CHECKLIST
