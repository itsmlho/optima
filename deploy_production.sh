#!/bin/bash
# ========================================
# Production Deployment Script (SSH)
# Date: 2026-03-06
# Server: 147.93.80.4:65002
# Database: u138256737_optima_db
# ========================================

set -e  # Exit on error

echo ""
echo "========================================"
echo " OPTIMA PRODUCTION DEPLOYMENT (SSH)"
echo " Database Migration + Code Deploy"
echo "========================================"
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
DB_NAME="u138256737_optima_db"
DB_USER="u138256737"
DB_HOST="localhost"
BACKUP_DIR="backups"
MIGRATIONS_DIR="databases/migrations"

# Check if running from correct directory
if [ ! -d "$MIGRATIONS_DIR" ]; then
    echo -e "${RED}ERROR: Please run this script from the optima root directory${NC}"
    exit 1
fi

# Create backup directory if not exists
mkdir -p $BACKUP_DIR

echo "Step 1: Backup Production Database"
echo "----------------------------------------"
BACKUP_FILE="${BACKUP_DIR}/optima_prod_backup_$(date +%Y%m%d_%H%M%S).sql"
echo "Creating backup: $BACKUP_FILE"

mysql -u $DB_USER -p -h $DB_HOST $DB_NAME -e "SELECT 1" > /dev/null 2>&1
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Cannot connect to database!${NC}"
    echo "Please check credentials in .credentials/production.txt"
    exit 1
fi

mysqldump -u $DB_USER -p -h $DB_HOST $DB_NAME > $BACKUP_FILE

if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Database backup failed!${NC}"
    exit 1
fi

echo -e "${GREEN}SUCCESS: Backup created${NC}"
echo ""

echo "Step 2: Run Database Migrations"
echo "----------------------------------------"
echo ""
echo "Running 5 migration files..."
echo ""

# Priority 1: Critical Schema Changes
echo "[1/5] Adding customer_location_id to kontrak_unit..."
mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < ${MIGRATIONS_DIR}/2026-03-05_add_customer_location_id_to_kontrak_unit.sql
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Migration 1 failed!${NC}"
    echo "Rolling back..."
    mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < $BACKUP_FILE
    exit 1
fi

echo "[2/5] Restructuring contract model..."
mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < ${MIGRATIONS_DIR}/2026-03-05_contract_model_restructure.sql
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Migration 2 failed!${NC}"
    echo "Rolling back..."
    mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < $BACKUP_FILE
    exit 1
fi

echo "[3/5] Updating kontrak_unit schema..."
mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < ${MIGRATIONS_DIR}/2026-03-05_kontrak_unit_harga_spare.sql
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Migration 3 failed!${NC}"
    echo "Rolling back..."
    mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < $BACKUP_FILE
    exit 1
fi

# Priority 2: New Feature Tables
echo "[4/5] Creating unit_audit_requests table..."
mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < ${MIGRATIONS_DIR}/2026-03-05_create_unit_audit_requests_table.sql
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Migration 4 failed!${NC}"
    echo "Rolling back..."
    mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < $BACKUP_FILE
    exit 1
fi

echo "[5/5] Creating unit_movements table..."
mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < ${MIGRATIONS_DIR}/2026-03-05_create_unit_movements_table.sql
if [ $? -ne 0 ]; then
    echo -e "${RED}ERROR: Migration 5 failed!${NC}"
    echo "Rolling back..."
    mysql -u $DB_USER -p -h $DB_HOST $DB_NAME < $BACKUP_FILE
    exit 1
fi

echo ""
echo -e "${GREEN}SUCCESS: All migrations completed!${NC}"
echo ""

echo "Step 3: Populate customer_location_id Data"
echo "----------------------------------------"
echo "Updating kontrak_unit records with customer locations..."

mysql -u $DB_USER -p -h $DB_HOST $DB_NAME << 'EOF'
UPDATE kontrak_unit ku
INNER JOIN kontrak k ON ku.kontrak_id = k.id
SET ku.customer_location_id = k.customer_location_id
WHERE ku.customer_location_id IS NULL
  AND k.customer_location_id IS NOT NULL;
EOF

if [ $? -ne 0 ]; then
    echo -e "${YELLOW}WARNING: Data population had issues (check manually)${NC}"
else
    echo -e "${GREEN}SUCCESS: Data populated${NC}"
fi

echo ""

echo "Step 4: Verify Migrations"
echo "----------------------------------------"
echo "Checking new columns and tables..."
echo ""

mysql -u $DB_USER -p -h $DB_HOST $DB_NAME -e "DESCRIBE kontrak_unit" | grep customer_location_id
mysql -u $DB_USER -p -h $DB_HOST $DB_NAME -e "SHOW TABLES LIKE 'unit_audit%'"
mysql -u $DB_USER -p -h $DB_HOST $DB_NAME -e "SHOW TABLES LIKE 'unit_movements'"

echo ""
echo "Step 5: Data Integrity Check"
echo "----------------------------------------"

mysql -u $DB_USER -p -h $DB_HOST $DB_NAME << 'EOF'
SELECT COUNT(*) as kontrak_unit_records FROM kontrak_unit;
SELECT COUNT(*) as units_with_location FROM kontrak_unit WHERE customer_location_id IS NOT NULL;
SELECT COUNT(*) as orphaned FROM kontrak_unit ku 
LEFT JOIN inventory_unit iu ON ku.unit_id = iu.id_inventory_unit 
WHERE iu.id_inventory_unit IS NULL;
EOF

echo ""
echo "========================================"
echo " DEPLOYMENT COMPLETE!"
echo "========================================"
echo ""
echo "Next Steps:"
echo "1. Upload code files to production server"
echo "   - Controllers (UnitAudit.php, UnitMovementController.php, etc)"
echo "   - Models (UnitAuditRequestModel.php, UnitMovementModel.php, etc)"
echo "   - Views (unit_audit.php, unit_movement.php, etc)"
echo "   - Routes.php, sidebar_new.php"
echo ""
echo "2. Test new features:"
echo "   - /service/unit-audit"
echo "   - /warehouse/movements"
echo "   - /marketing/kontrak/edit"
echo ""
echo "3. Monitor error logs for issues"
echo "   - /writable/logs/"
echo ""
echo "Backup saved to: $BACKUP_FILE"
echo ""
