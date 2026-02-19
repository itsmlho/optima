# CRITICAL: Database Table Not Found

## ❌ Error Received
```
#1109 - Unknown table 'delivery_instructions' in information_schema
```

This means the `delivery_instructions` table **does not exist** in your production database. This could be because:

1. ✅ **Table has a different name** (Indonesian naming? `instruksi_pengiriman`?)
2. ✅ **Wrong database selected** (make sure you're in the correct database)
3. ❌ **Table doesn't exist at all** (application code may be wrong)

## 🔍 STEP 1: Run Diagnostic Script

**BEFORE running any migration**, run this diagnostic script to find the actual table names:

### Via phpMyAdmin:
```
1. Login to phpMyAdmin
2. Select your OPTIMA database (u138256737_optima or similar)
3. Click "SQL" tab
4. Upload or paste: databases/DIAGNOSTIC_CHECK_TABLES.sql
5. Click "Go"
6. **Save the output** - we need this to fix the migration!
```

### Via SSH:
```bash
ssh -p 65002 u138256737@147.93.80.45
cd optima
mysql -u [user] -p [database] < databases/DIAGNOSTIC_CHECK_TABLES.sql > diagnostic_output.txt
cat diagnostic_output.txt
```

## 📋 What the Diagnostic Script Does

It will check:
- ✅ All tables in your database
- ✅ Tables with names like: `delivery`, `instruksi`, `surat_jalan`, `di`
- ✅ Columns: `status_di`, `nomor_di`, `sampai_tanggal_approve`
- ✅ Structure of `spk` table (which we know exists)
- ✅ Structure of `kontrak` table (which we know exists)
- ✅ Whether migration was already applied (search for `invoice_generated` column)

## 🎯 Expected Findings

Based on the diagnostic output, we'll find one of these:

### Scenario A: Table exists with different name
```sql
-- Example output:
TABLE_NAME: instruksi_pengiriman (or surat_jalan, or pengiriman_barang)
```
**Solution:** Update all migration scripts to use the correct table name

### Scenario B: Table doesn't exist, data is in SPK table
```sql
-- SPK table includes DI data directly
```
**Solution:** Modify migration to add columns to SPK table instead

### Scenario C: Wrong database selected
```sql
-- current_database shows wrong database name
```
**Solution:** Connect to the correct database

### Scenario D: Migration already applied
```sql
-- invoice_generated column already exists in some table
```
**Solution:** No migration needed, just verify

## 🚨 DO NOT PROCEED WITH MIGRATION

**STOP HERE** and share the diagnostic output with me. The migration scripts are currently referencing:
- `delivery_instructions` table (might be wrong)
- `quotations` table (might be wrong)

Once we have the diagnostic output, I'll:
1. ✅ Update migration scripts with correct table names
2. ✅ Fix all SQL queries in the migration
3. ✅ Ensure compatibility with your actual schema

## 📤 What to Send Back

After running the diagnostic script, copy and paste:

1. **Section "All tables in database"** - Full list of tables
2. **Section "Looking for delivery instruction table"** - Any matches found
3. **Section "Tables with status_di column"** - Critical for identifying the DI table
4. **Section "Tables with nomor_di column"** - Critical for identifying the DI table
5. **Section "Tables with sampai_tanggal_approve column"** - Critical for identifying the DI table

## 🔄 Next Steps After Diagnostic

Once you provide the diagnostic output:
1. I'll identify the correct table name
2. Update all 3 migration files
3. Update the deploy script
4. You can then safely run the migration

---

**Bottom Line:** The `delivery_instructions` table name in our migration doesn't match your production database. We need to find the actual table name first before proceeding.
