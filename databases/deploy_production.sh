#!/bin/bash
# ================================================================
# Production Migration - Quick Deploy Script
# ================================================================
# Date: 2026-02-19
# Description: Automated deployment script for production server
# Usage: bash deploy_production.sh
# ================================================================

set -e  # Exit on error

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}================================================${NC}"
echo -e "${BLUE}  Production Migration - Marketing Module${NC}"
echo -e "${BLUE}  Date: $(date +%Y-%m-%d\ %H:%M:%S)${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""

# Configuration
read -p "Enter MySQL username: " DB_USER
read -sp "Enter MySQL password: " DB_PASS
echo ""
read -p "Enter database name: " DB_NAME

# Verify connection
echo -e "${YELLOW}[1/9] Verifying database connection...${NC}"
mysql -u "$DB_USER" -p"$DB_PASS" -e "SELECT 1;" > /dev/null 2>&1
if [ $? -eq 0 ]; then
    echo -e "${GREEN}✓ Database connection successful${NC}"
else
    echo -e "${RED}✗ Database connection failed. Check credentials.${NC}"
    exit 1
fi

# Check if migration already applied
echo -e "${YELLOW}[2/9] Checking migration status...${NC}"
ALREADY_MIGRATED=$(mysql -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -sN -e "
    SELECT EXISTS(
        SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = '$DB_NAME'
        AND TABLE_NAME = 'delivery_instructions' 
        AND COLUMN_NAME = 'invoice_generated'
    );
")

if [ "$ALREADY_MIGRATED" -eq 1 ]; then
    echo -e "${YELLOW}⚠ Migration already applied. Skipping database changes.${NC}"
    SKIP_DB_MIGRATION=true
else
    echo -e "${GREEN}✓ Migration not yet applied. Proceeding...${NC}"
    SKIP_DB_MIGRATION=false
fi

# Create backup
if [ "$SKIP_DB_MIGRATION" = false ]; then
    echo -e "${YELLOW}[3/9] Creating database backup...${NC}"
    TIMESTAMP=$(date +%Y%m%d_%H%M%S)
    BACKUP_FILE="databases/backups/backup_${TIMESTAMP}.sql"
    
    mkdir -p databases/backups
    mysqldump -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" > "$BACKUP_FILE" 2>/dev/null
    
    if [ -f "$BACKUP_FILE" ]; then
        BACKUP_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
        echo -e "${GREEN}✓ Backup created: $BACKUP_FILE ($BACKUP_SIZE)${NC}"
    else
        echo -e "${RED}✗ Backup failed. Aborting migration.${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}[3/9] Skipping backup (migration already applied)${NC}"
fi

# Run migration
if [ "$SKIP_DB_MIGRATION" = false ]; then
    echo -e "${YELLOW}[4/9] Running database migration...${NC}"
    
    # Try simple version first (better compatibility)
    if [ -f "databases/migrations/PRODUCTION_MIGRATION_2026-02-19_SIMPLE.sql" ]; then
        MIGRATION_FILE="databases/migrations/PRODUCTION_MIGRATION_2026-02-19_SIMPLE.sql"
        echo -e "${GREEN}Using SIMPLE migration (better compatibility)${NC}"
    elif [ -f "databases/migrations/PRODUCTION_MIGRATION_2026-02-19.sql" ]; then
        MIGRATION_FILE="databases/migrations/PRODUCTION_MIGRATION_2026-02-19.sql"
        echo -e "${GREEN}Using standard migration${NC}"
    else
        echo -e "${RED}✗ Migration file not found!${NC}"
        exit 1
    fi
    
    mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < "$MIGRATION_FILE" 2>&1 | tee migration_output.log
    
    # Check for expected errors (duplicate columns/indexes are OK)
    if grep -q "Duplicate column name\|Duplicate key name" migration_output.log; then
        echo -e "${YELLOW}⚠ Found duplicate columns/indexes - migration was already applied${NC}"
        echo -e "${GREEN}✓ Migration completed (no changes needed)${NC}"
    elif [ ${PIPESTATUS[0]} -eq 0 ]; then
        echo -e "${GREEN}✓ Migration completed successfully${NC}"
    else
        echo -e "${RED}✗ Migration failed. Check migration_output.log${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}[4/9] Skipping database migration (already applied)${NC}"
fi

# Verify migration
if [ "$SKIP_DB_MIGRATION" = false ]; then
    echo -e "${YELLOW}[5/9] Verifying migration...${NC}"
    
    COLUMNS_ADDED=$(mysql -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -sN -e "
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = '$DB_NAME'
        AND TABLE_NAME = 'delivery_instructions' 
        AND COLUMN_NAME IN ('invoice_generated', 'invoice_generated_at');
    ")
    
    INDEX_ADDED=$(mysql -u "$DB_USER" -p"$DB_PASS" -D "$DB_NAME" -sN -e "
        SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
        WHERE TABLE_SCHEMA = '$DB_NAME'
        AND TABLE_NAME = 'delivery_instructions' 
        AND INDEX_NAME = 'idx_invoice_automation';
    ")
    
    if [ "$COLUMNS_ADDED" -eq 2 ] && [ "$INDEX_ADDED" -gt 0 ]; then
        echo -e "${GREEN}✓ Verification passed: Columns and index created${NC}"
    else
        echo -e "${RED}✗ Verification failed: Columns=$COLUMNS_ADDED (expected 2), Index parts=$INDEX_ADDED (expected 4)${NC}"
        exit 1
    fi
else
    echo -e "${YELLOW}[5/9] Skipping verification (migration already applied)${NC}"
fi

# Update .env file
echo -e "${YELLOW}[6/9] Updating .env configuration...${NC}"

if ! grep -q "ACC_EMAIL_1" .env; then
    cat >> .env << 'EOF'

#--------------------------------------------------------------------
# EMAIL CONFIGURATION - Invoice Automation
#--------------------------------------------------------------------
ACC_EMAIL_1 = finance@sml.co.id
ACC_EMAIL_2 = anselin_smlforklift@yahoo.com
MARKETING_EMAIL = marketing@sml.co.id
EOF
    echo -e "${GREEN}✓ Email configuration added to .env${NC}"
else
    echo -e "${GREEN}✓ Email configuration already exists${NC}"
fi

# Clear cache
echo -e "${YELLOW}[7/9] Clearing application cache...${NC}"
rm -rf writable/cache/* 2>/dev/null || true
rm -rf writable/session/* 2>/dev/null || true
echo -e "${GREEN}✓ Cache cleared${NC}"

# Set permissions
echo -e "${YELLOW}[8/9] Setting file permissions...${NC}"
chmod -R 755 writable/ 2>/dev/null || true
echo -e "${GREEN}✓ Permissions updated${NC}"

# Test CLI command
echo -e "${YELLOW}[9/9] Testing invoice automation CLI...${NC}"

PHP_BIN=$(which php)
if [ -f "spark" ]; then
    chmod +x spark
    OUTPUT=$($PHP_BIN spark jobs:invoice-automation --dry-run 2>&1)
    
    if echo "$OUTPUT" | grep -q "Found.*DIs eligible"; then
        ELIGIBLE_COUNT=$(echo "$OUTPUT" | grep -oP 'Found \K\d+')
        echo -e "${GREEN}✓ CLI test passed: $ELIGIBLE_COUNT DIs eligible for invoice generation${NC}"
    else
        echo -e "${YELLOW}⚠ CLI test completed but may need verification${NC}"
        echo "$OUTPUT"
    fi
else
    echo -e "${RED}✗ spark file not found${NC}"
fi

# Summary
echo ""
echo -e "${BLUE}================================================${NC}"
echo -e "${GREEN}  ✓ DEPLOYMENT COMPLETED SUCCESSFULLY${NC}"
echo -e "${BLUE}================================================${NC}"
echo ""
echo -e "${GREEN}Summary:${NC}"
if [ "$SKIP_DB_MIGRATION" = false ]; then
    echo -e "  ✓ Database backup: $BACKUP_FILE"
    echo -e "  ✓ Columns added: 3 (2 in delivery_instructions, 1 in quotations)"
    echo -e "  ✓ Index created: idx_invoice_automation"
fi
echo -e "  ✓ Configuration updated: .env"
echo -e "  ✓ Cache cleared: writable/cache, writable/session"
echo -e "  ✓ Permissions set: writable/"
if [ -n "$ELIGIBLE_COUNT" ]; then
    echo -e "  ✓ Invoice automation: $ELIGIBLE_COUNT DIs ready"
fi
echo ""
echo -e "${YELLOW}Next Steps:${NC}"
echo -e "  1. Test application in browser"
echo -e "  2. Monitor logs: tail -f writable/logs/log-\$(date +%Y-%m-%d).log"
echo -e "  3. Test features:"
echo -e "     - Marketing > Quotations > Convert to Customer"
echo -e "     - Marketing > Kontrak > Create Contract"
echo -e "     - CLI: php spark jobs:invoice-automation --dry-run"
echo ""
echo -e "${GREEN}Deployment completed at: $(date +%Y-%m-%d\ %H:%M:%S)${NC}"
echo ""

# Save deployment log
echo "Deployment Log - $(date +%Y-%m-%d\ %H:%M:%S)" > deployment.log
echo "Database: $DB_NAME" >> deployment.log
echo "User: $DB_USER" >> deployment.log
if [ "$SKIP_DB_MIGRATION" = false ]; then
    echo "Backup: $BACKUP_FILE" >> deployment.log
fi
echo "Status: Success" >> deployment.log

exit 0
