# Production Database Migration Guide - SSH Deployment
## Marketing Module Enhancement - Invoice Automation

**Date:** February 19, 2026  
**Target Server:** 147.93.80.45:65002  
**SSH User:** u138256737  
**Database:** Production MySQL  
**Estimated Time:** 10-15 minutes  
**Downtime Required:** No (safe online migration)

---

## Pre-Migration Checklist

- [ ] **Backup complete** - Full database dump created
- [ ] **Maintenance window scheduled** (optional - migration is non-breaking)
- [ ] **SSH access verified** - Can connect to production server
- [ ] **Database credentials ready** - MySQL username and password
- [ ] **Migration files uploaded** - SQL scripts transferred to server
- [ ] **Rollback plan reviewed** - Know how to revert if needed
- [ ] **Team notified** - DevOps/DBA aware of migration

---

## Quick Command Reference

```bash
# Connect to server
ssh -p 65002 u138256737@147.93.80.45

# Backup database
mysqldump -u [user] -p [database] > backup_$(date +%Y%m%d_%H%M%S).sql

# Run migration
mysql -u [user] -p [database] < databases/migrations/PRODUCTION_MIGRATION_2026-02-19.sql

# Test CLI
php spark jobs:invoice-automation --dry-run

# Rollback if needed
mysql -u [user] -p [database] < databases/migrations/PRODUCTION_ROLLBACK_2026-02-19.sql
```

---

## Step-by-Step Execution

### Step 1: Connect to Production Server

```bash
ssh -p 65002 u138256737@147.93.80.45
```

**Expected:** Password prompt, then shell access

---

### Step 2: Locate Application Directory

```bash
# Try common paths
cd ~/public_html/optima || cd ~/www/optima || cd ~/optima

# Verify correct directory
pwd
ls -la | grep -E "app|databases|public"
```

---

### Step 3: Create Backup (CRITICAL!)

```bash
# Create backup directory
mkdir -p databases/backups

# Backup with timestamp
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
mysqldump -u [YOUR_DB_USER] -p [YOUR_DB_NAME] > databases/backups/backup_${TIMESTAMP}.sql

# Verify backup size (should be > 1MB)
ls -lh databases/backups/backup_${TIMESTAMP}.sql
```

**⚠️ DO NOT PROCEED without valid backup!**

---

### Step 4: Upload Migration Files

**Option A: Git Pull**
```bash
git pull origin main
```

**Option B: SCP from Local**
```bash
# From your LOCAL machine (open new terminal)
scp -P 65002 databases/migrations/PRODUCTION_*.sql u138256737@147.93.80.45:~/optima/databases/migrations/
```

**Option C: Manual Copy-Paste**
```bash
# On server
nano databases/migrations/PRODUCTION_MIGRATION_2026-02-19.sql
# Paste content from local file, save (Ctrl+X, Y, Enter)
```

---

### Step 5: Pre-Check Current State

```bash
# Check if migration already applied
mysql -u [YOUR_DB_USER] -p -D [YOUR_DB_NAME] -e "
SELECT EXISTS(
    SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'delivery_instructions' 
    AND COLUMN_NAME = 'invoice_generated'
) AS already_migrated;
"
```

**Result:**
- `0` = Safe to proceed with migration
- `1` = Already migrated, SKIP to Step 8

---

### Step 6: Execute Migration

```bash
# Run migration script
mysql -u [YOUR_DB_USER] -p [YOUR_DB_NAME] < databases/migrations/PRODUCTION_MIGRATION_2026-02-19.sql

# Watch output carefully for errors
```

**Expected Output:**
```
current_database
[DBNAME]
...
✅ Migration completed successfully!
2026-02-19 14:05:30
```

**If errors appear:** STOP, save output, run rollback

---

### Step 7: Verify Migration Success

```bash
# Quick verification
mysql -u [YOUR_DB_USER] -p -D [YOUR_DB_NAME] << 'EOF'
-- Should return 2 rows
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'delivery_instructions' 
AND COLUMN_NAME LIKE 'invoice_generated%';

-- Should return 1 row
SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_NAME = 'quotations' 
AND COLUMN_NAME = 'customer_converted_at';

-- Should return 4 rows (index parts)
SELECT COUNT(*) AS index_columns FROM INFORMATION_SCHEMA.STATISTICS 
WHERE TABLE_NAME = 'delivery_instructions' 
AND INDEX_NAME = 'idx_invoice_automation';
EOF
```

**Expected:**
- First query: 2 rows (invoice_generated, invoice_generated_at)
- Second query: 1 row (customer_converted_at)
- Third query: 4 (index has 4 columns)

---

### Step 8: Update Application Files

```bash
# Using Git (recommended)
git pull origin main

# OR upload manually via SCP
# (see Option B in Step 4)

# Verify files updated
ls -l app/Jobs/InvoiceAutomationJob.php
ls -l app/Commands/InvoiceAutomation.php
ls -l app/Controllers/Marketing.php
```

---

### Step 9: Update Environment Config

```bash
# Edit .env file
nano .env

# Add these lines at the bottom:
```
```env
#--------------------------------------------------------------------
# EMAIL CONFIGURATION - Invoice Automation
#--------------------------------------------------------------------
ACC_EMAIL_1 = finance@sml.co.id
ACC_EMAIL_2 = anselin_smlforklift@yahoo.com
MARKETING_EMAIL = marketing@sml.co.id
```
```bash
# Save: Ctrl+X, Y, Enter
```

---

### Step 10: Clear Cache & Set Permissions

```bash
# Clear cache
rm -rf writable/cache/*
rm -rf writable/session/*

# Fix permissions (adjust www-data if needed)
chmod -R 755 writable/
chown -R www-data:www-data writable/
```

---

### Step 11: Test CLI Command

```bash
# Find PHP binary
which php

# Test invoice automation (dry-run - safe)
php spark jobs:invoice-automation --dry-run
```

**Expected Output:**
```
Found 8 DIs eligible for invoice generation:

1. DI #123
   Customer: PT Herlina Indah
   Contract: No. 069/SPj/LEG/HI/X/25
   Completed: 2025-09-04 (168 days ago)
...
```

**If command fails:**
```bash
# Try full PHP path
/usr/bin/php spark jobs:invoice-automation --dry-run

# Or check permissions
chmod +x spark
```

---

### Step 12: Monitor Application

```bash
# Watch logs in real-time
tail -f writable/logs/log-$(date +%Y-%m-%d).log

# In browser, test application
# Navigate to: https://yourdomain.com/optima
# Check: Marketing > Quotations > Convert to Customer
# Check: Marketing > Kontrak > Create Contract
```

---

## Rollback Procedure

**⚠️ ONLY if migration causes critical issues!**

```bash
# 1. Run rollback script
mysql -u [YOUR_DB_USER] -p [YOUR_DB_NAME] < databases/migrations/PRODUCTION_ROLLBACK_2026-02-19.sql

# 2. Revert application code
git revert HEAD  # Or git checkout previous-commit-hash

# 3. Clear cache
rm -rf writable/cache/*

# 4. Restore database from backup (last resort)
mysql -u [YOUR_DB_USER] -p [YOUR_DB_NAME] < databases/backups/backup_TIMESTAMP.sql
```

---

## Troubleshooting

### "Could not open input file: spark"
```bash
cd ~/optima  # Ensure in correct directory
ls -la spark  # Verify file exists
chmod +x spark  # Make executable
```

### "Access denied for user"
```bash
# Check database credentials
cat .env | grep database

# Test connection
mysql -u [user] -p -e "SELECT 1;"
```

### "Column already exists"
```
✅ This is SAFE - migration was already applied
Skip to Step 8 (Update Application Files)
```

### "Index already exists"
```
✅ This is SAFE - index was already created
Skip to Step 8
```

---

## Post-Migration Checklist

- [ ] Database migrations completed without errors
- [ ] Verification queries show all columns/indexes created
- [ ] Application files updated (git pulled or manually uploaded)
- [ ] .env file contains email configuration
- [ ] Cache cleared
- [ ] Permissions set correctly
- [ ] CLI command runs successfully (dry-run mode)
- [ ] Application accessible in browser
- [ ] No PHP errors in logs (`tail -f writable/logs/log-*.log`)
- [ ] Features tested:
  - [ ] Quotation → Customer conversion button visible
  - [ ] Direct contract creation modal working
  - [ ] Invoice automation lists 8 eligible DIs
- [ ] Backup file saved and verified

---

## Success Indicators

✅ Migration is **successful** if you see:

1. **Database:**
   - No SQL errors during migration
   - Verification queries return expected row counts
   - Index created (4 columns)

2. **CLI:**
   ```
   Found 8 DIs eligible for invoice generation:
   ✓ DI #123 - PT Herlina Indah (168 days ago)
   ✓ DI #124 - PT Indah Kiat (160 days ago)
   ...
   ```

3. **Application:**
   - No 500 errors or PHP warnings
   - Quotations page loads
   - "Convert to Customer" button appears on DEAL quotations
   - Kontrak page has "Create Contract" button

4. **Logs:**
   ```bash
   tail writable/logs/log-$(date +%Y-%m-%d).log
   # Should show no CRITICAL or ERROR messages
   ```

---

## Files Reference

**Migration Scripts:**
- `databases/migrations/PRODUCTION_MIGRATION_2026-02-19.sql` - Main migration
- `databases/migrations/PRODUCTION_ROLLBACK_2026-02-19.sql` - Rollback script

**Updated Application Files:**
- `app/Jobs/InvoiceAutomationJob.php` (303 lines)
- `app/Commands/InvoiceAutomation.php` (172 lines)
- `app/Controllers/Marketing.php` (lines 8470-8520, 9030-9160)
- `app/Models/QuotationModel.php` (line 17)
- `app/Views/marketing/kontrak.php` (lines 120-327, 1250-1420)
- `app/Views/marketing/customer_management.php` (lines 240-260, 1095, 1370-1415)
- `app/Views/marketing/quotations.php` (lines 1880-1930, 2125-2250)
- `app/Views/emails/invoice_ready_acc.php` (new file)
- `app/Views/emails/invoice_ready_marketing.php` (new file)
- `app/Config/Routes.php` (line 158)
- `.env` (email configuration added)

**Documentation:**
- `docs/DATABASE_SCHEMA_MAPPING.md` - Schema differences local vs production
- `docs/TESTING_VERIFICATION_GUIDE.md` - Comprehensive testing guide

---

## Emergency Contacts

**If critical issues occur:**
1. Immediately run rollback script
2. Restore from backup if needed
3. Document error messages
4. Check logs: `writable/logs/log-*.log`
5. Contact development team with:
   - Error messages
   - Log excerpts
   - Steps taken before error

---

## Summary

**Changes Applied:**
- ✅ 3 database columns added (2 in delivery_instructions, 1 in quotations)
- ✅ 1 composite index created (4 columns for query performance)
- ✅ 19 application files updated
- ✅ 8 new files created (Jobs, Commands, Views)
- ✅ Email configuration added
- ✅ Invoice automation ready (8 DIs eligible)

**Impact:**
- No downtime during migration
- No breaking changes
- Backward compatible
- No data loss risk
- Instant rollback available

**Next Steps After Migration:**
1. Monitor application for 24 hours
2. Test invoice automation in dry-run mode daily
3. Schedule first real invoice generation after 1 week
4. Configure SMTP settings for email delivery (production)
5. Train users on new features (customer conversion, direct contracts)

---

**Deployment Window:** Anytime (no downtime)  
**Risk Level:** Low (backward compatible, tested on staging)  
**Prepared By:** System Development Team  
**Date:** February 19, 2026
