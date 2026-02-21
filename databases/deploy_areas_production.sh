#!/bin/bash
# ============================================================================
# Quick Production Deployment Script - Area CENTRAL Migration
# Date: February 21, 2026
# COPY-PASTE THIS TO PRODUCTION SERVER
# ============================================================================

echo "=== OPTIMA AREA CENTRAL MIGRATION - PRODUCTION ==="
echo "Starting deployment at: $(date)"
echo ""

# ============================================================================
# CONFIGURATION - UPDATE THESE VALUES!
# ============================================================================
DB_USER="REPLACE_WITH_YOUR_DB_USER"
DB_NAME="REPLACE_WITH_YOUR_DB_NAME"
DB_HOST="localhost"  # or 127.0.0.1

# ✅ Set these after checking Step 6 in deployment guide!
DIESEL_EMPLOYEE_IDS="8,9"      # Replace with actual DIESEL staff IDs (comma-separated)
ELECTRIC_EMPLOYEE_IDS="1,2,18" # Replace with actual ELECTRIC staff IDs (comma-separated)

# ============================================================================
# PRE-FLIGHT CHECKS
# ============================================================================
echo "🔍 Pre-flight checks..."

# Check if we're in correct directory
if [ ! -f "spark" ] || [ ! -d "app" ]; then
    echo "❌ ERROR: Not in optima application directory!"
    echo "Please cd to the correct directory and try again."
    exit 1
fi

# Check if migration files exist
if [ ! -f "databases/migrations/2026_02_20_add_central_areas_diesel_electric.sql" ]; then
    echo "❌ ERROR: Migration files not found!"
    echo "Please upload migration files first (see deployment guide Step 4)"
    exit 1
fi

echo "✅ Directory check passed"
echo ""

# ============================================================================
# BACKUP DATABASE
# ============================================================================
echo "💾 Creating backup..."
mkdir -p databases/backups
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
BACKUP_FILE="databases/backups/backup_areas_${TIMESTAMP}.sql"

echo "Enter MySQL password for backup:"
mysqldump -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} > ${BACKUP_FILE}

if [ $? -eq 0 ]; then
    BACKUP_SIZE=$(ls -lh ${BACKUP_FILE} | awk '{print $5}')
    echo "✅ Backup created: ${BACKUP_FILE} (${BACKUP_SIZE})"
else
    echo "❌ Backup failed! Aborting migration."
    exit 1
fi
echo ""

# ============================================================================
# CHECK CURRENT STATE
# ============================================================================
echo "📊 Checking current database state..."
echo "Enter MySQL password:"

mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} << 'EOF'
SELECT 'Current state before migration:' as info;
SELECT 
    'CENTRAL areas' as type, 
    COUNT(*) as count 
FROM areas 
WHERE area_type='CENTRAL' AND is_active=1;

SELECT 
    'D-* CENTRAL' as type, 
    COUNT(*) as count 
FROM areas 
WHERE area_code LIKE 'D-%' AND area_type='CENTRAL' AND is_active=1;

SELECT 
    'E-* CENTRAL' as type, 
    COUNT(*) as count 
FROM areas 
WHERE area_code LIKE 'E-%' AND area_type='CENTRAL' AND is_active=1;
EOF

echo ""
read -p "⚠️  If D-* and E-* counts are already 64 and 57, stop now! Continue? (y/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Migration cancelled by user"
    exit 1
fi
echo ""

# ============================================================================
# EXECUTE AREA MIGRATION
# ============================================================================
echo "🚀 Executing area migration (115 new CENTRAL areas)..."
echo "Enter MySQL password:"

mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} < databases/migrations/2026_02_20_add_central_areas_diesel_electric.sql

if [ $? -eq 0 ]; then
    echo "✅ Area migration completed"
else
    echo "❌ Area migration failed!"
    echo "To rollback, run: mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} < databases/migrations/2026_02_20_rollback_central_areas.sql"
    exit 1
fi
echo ""

# ============================================================================
# EXECUTE EMPLOYEE ASSIGNMENTS
# ============================================================================
echo "👥 Executing employee assignments..."
echo "⚠️  Using employee IDs:"
echo "   DIESEL: ${DIESEL_EMPLOYEE_IDS}"
echo "   ELECTRIC: ${ELECTRIC_EMPLOYEE_IDS}"
read -p "Are these correct? (y/n): " -n 1 -r
echo ""
if [[ ! $REPLY =~ ^[Yy]$ ]]; then
    echo "❌ Please update DIESEL_EMPLOYEE_IDS and ELECTRIC_EMPLOYEE_IDS in this script"
    echo "✅ Areas already migrated. You can run assignments separately later."
    exit 1
fi

echo "Enter MySQL password:"
mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} < databases/migrations/2026_02_20_execute_employee_assignments.sql

if [ $? -eq 0 ]; then
    echo "✅ Employee assignments completed"
else
    echo "⚠️  Employee assignment failed (but areas are migrated)"
    echo "To rollback assignments: mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} < databases/migrations/2026_02_20_rollback_employee_assignments.sql"
fi
echo ""

# ============================================================================
# VERIFICATION
# ============================================================================
echo "✅ Running verification..."
echo "Enter MySQL password:"

mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} << 'EOF'
SELECT '=== VERIFICATION RESULTS ===' as report;

SELECT 
    area_type, 
    COUNT(*) as total 
FROM areas 
WHERE is_active=1 
GROUP BY area_type;

SELECT 
    d.nama_departemen as department,
    COUNT(DISTINCT aea.employee_id) as employees,
    COUNT(DISTINCT aea.area_id) as areas,
    COUNT(*) as assignments
FROM area_employee_assignments aea
JOIN areas a ON aea.area_id = a.id
JOIN employees e ON aea.employee_id = e.id
JOIN departemen d ON e.departemen_id = d.id_departemen
WHERE a.area_type = 'CENTRAL'
  AND (a.area_code LIKE 'D-%' OR a.area_code LIKE 'E-%')
  AND aea.start_date = '2026-02-20'
GROUP BY d.nama_departemen;
EOF

echo ""
echo "=== MIGRATION COMPLETED ==="
echo "Completed at: $(date)"
echo "Backup saved: ${BACKUP_FILE}"
echo ""
echo "📝 Next steps:"
echo "1. Test in browser: Login and check Service Area Management"
echo "2. Verify employee access to new areas"
echo "3. Monitor application logs for errors"
echo "4. Archive backup file somewhere safe"
echo ""
echo "🔄 To rollback if needed:"
echo "   mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} < databases/migrations/2026_02_20_rollback_employee_assignments.sql"
echo "   mysql -u ${DB_USER} -p -h ${DB_HOST} ${DB_NAME} < databases/migrations/2026_02_20_rollback_central_areas.sql"
echo ""
