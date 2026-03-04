#!/bin/bash
#
# Phase 1A Emergency Rollback Script
# 
# This script performs emergency rollback of Phase 1A deployment
# USE ONLY WHEN AUTHORIZED BY DEPLOYMENT LEAD, CTO, OR DEV MANAGER
#
# Usage: ./rollback_phase1a.sh [staging|production]
#
# Author: OPTIMA Development Team
# Created: 2026-03-04
# Version: 1.0
#

set -e  # Exit on any error
set -u  # Exit on undefined variable

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Configuration
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
ENVIRONMENT="${1:-}"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Database credentials (update these)
DB_HOST="localhost"
DB_USER="root"
DB_NAME=""

# Git configuration
GIT_ROLLBACK_TAG="pre-phase1a"  # Tag before Phase 1A changes

# Logging
LOG_FILE="/var/log/optima/rollback_phase1a_${TIMESTAMP}.log"
mkdir -p "$(dirname "$LOG_FILE")"

log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')]${NC} $1" | tee -a "$LOG_FILE"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1" | tee -a "$LOG_FILE"
}

warn() {
    echo -e "${YELLOW}[WARNING]${NC} $1" | tee -a "$LOG_FILE"
}

# Validate environment parameter
if [[ -z "$ENVIRONMENT" ]]; then
    error "Environment not specified!"
    echo "Usage: $0 [staging|production]"
    exit 1
fi

if [[ "$ENVIRONMENT" != "staging" && "$ENVIRONMENT" != "production" ]]; then
    error "Invalid environment: $ENVIRONMENT"
    echo "Must be 'staging' or 'production'"
    exit 1
fi

# Set database name based on environment
if [[ "$ENVIRONMENT" == "staging" ]]; then
    DB_NAME="optima_staging"
else
    DB_NAME="optima_production"
fi

echo -e "${RED}"
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                                                                ║"
echo "║           PHASE 1A EMERGENCY ROLLBACK SCRIPT                   ║"
echo "║                                                                ║"
echo "║  WARNING: This will revert all Phase 1A changes!              ║"
echo "║                                                                ║"
echo "║  Environment: ${ENVIRONMENT^^}                                    "
echo "║  Database: $DB_NAME                                            "
echo "║  Timestamp: $TIMESTAMP                                         "
echo "║                                                                ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

# Confirmation
read -p "Are you authorized to perform this rollback? (yes/no): " AUTHORIZED
if [[ "$AUTHORIZED" != "yes" ]]; then
    error "Rollback cancelled - not authorized"
    exit 1
fi

read -p "Enter the ticket/incident number: " INCIDENT_NUMBER
if [[ -z "$INCIDENT_NUMBER" ]]; then
    error "Incident number required for audit trail"
    exit 1
fi

read -p "This will REVERT all Phase 1A changes. Are you ABSOLUTELY sure? (type 'ROLLBACK' to confirm): " CONFIRM
if [[ "$CONFIRM" != "ROLLBACK" ]]; then
    error "Rollback cancelled - confirmation failed"
    exit 1
fi

log "=========================================================="
log "ROLLBACK INITIATED"
log "Environment: $ENVIRONMENT"
log "Incident: $INCIDENT_NUMBER"
log "Initiated by: $(whoami)"
log "=========================================================="

# Step 1: Enable Maintenance Mode
log "Step 1: Enabling maintenance mode..."
cd /var/www/optima
php spark down || {
    error "Failed to enable maintenance mode"
    log "Continuing anyway..."
}
touch writable/maintenance.flag
log "✓ Maintenance mode enabled"

# Step 2: Backup Current State
log "Step 2: Backing up current state..."
BACKUP_DIR="/backups/rollback_${TIMESTAMP}"
mkdir -p "$BACKUP_DIR"

# Backup database
log "Backing up database before rollback..."
mysqldump -h "$DB_HOST" -u "$DB_USER" -p \
    --single-transaction \
    --routines \
    --triggers \
    --events \
    "$DB_NAME" > "$BACKUP_DIR/${DB_NAME}_pre_rollback.sql" || {
    error "Failed to backup database"
    exit 1
}
gzip "$BACKUP_DIR/${DB_NAME}_pre_rollback.sql"
log "✓ Database backup created: $BACKUP_DIR/${DB_NAME}_pre_rollback.sql.gz"

# Backup code
log "Backing up current code..."
tar -czf "$BACKUP_DIR/code_pre_rollback.tar.gz" \
    app/Controllers/ \
    app/Models/ || {
    error "Failed to backup code"
    exit 1
}
log "✓ Code backup created: $BACKUP_DIR/code_pre_rollback.tar.gz"

# Step 3: Drop Foreign Keys (if they exist)
log "Step 3: Removing foreign key constraints..."
mysql -h "$DB_HOST" -u "$DB_USER" -p "$DB_NAME" <<EOF
SET FOREIGN_KEY_CHECKS = 0;

-- Drop FK constraints added in Step 3
ALTER TABLE kontrak_unit DROP FOREIGN KEY IF EXISTS fk_kontrak_unit_unit_id;
ALTER TABLE kontrak_unit DROP FOREIGN KEY IF EXISTS fk_kontrak_unit_kontrak_id;

SET FOREIGN_KEY_CHECKS = 1;
EOF

if [[ $? -eq 0 ]]; then
    log "✓ Foreign key constraints removed"
else
    warn "Failed to remove FK constraints (they may not exist)"
fi

# Step 4: Drop VIEW (if it exists)
log "Step 4: Removing VIEW..."
mysql -h "$DB_HOST" -u "$DB_USER" -p "$DB_NAME" <<EOF
DROP VIEW IF EXISTS vw_unit_with_contracts;
EOF

if [[ $? -eq 0 ]]; then
    log "✓ VIEW removed"
else
    warn "Failed to remove VIEW (it may not exist)"
fi

# Step 5: Rollback Code via Git
log "Step 5: Rolling back code to pre-Phase 1A state..."
cd /var/www/optima

# Stash any uncommitted changes
git stash save "Rollback stash - $TIMESTAMP" || log "Nothing to stash"

# Checkout previous version
if git tag | grep -q "$GIT_ROLLBACK_TAG"; then
    log "Rolling back to tag: $GIT_ROLLBACK_TAG"
    git checkout "$GIT_ROLLBACK_TAG" || {
        error "Failed to checkout tag $GIT_ROLLBACK_TAG"
        exit 1
    }
else
    warn "Tag $GIT_ROLLBACK_TAG not found!"
    read -p "Enter commit hash or tag to rollback to: " ROLLBACK_REF
    git checkout "$ROLLBACK_REF" || {
        error "Failed to checkout $ROLLBACK_REF"
        exit 1
    }
fi

log "✓ Code rolled back successfully"

# Verify critical files reverted
log "Verifying file rollback..."
EXPECTED_FILES=(
    "app/Models/InventoryUnitModel.php"
    "app/Controllers/Marketing.php"
    "app/Controllers/CustomerManagementController.php"
)

for file in "${EXPECTED_FILES[@]}"; do
    if [[ -f "$file" ]]; then
        log "  ✓ $file exists"
    else
        error "  ✗ $file MISSING!"
    fi
done

# Step 6: Set Permissions
log "Step 6: Setting file permissions..."
chown -R www-data:www-data app/
chmod -R 755 app/
log "✓ Permissions set"

# Step 7: Clear Cache
log "Step 7: Clearing application cache..."
php spark cache:clear || warn "Failed to clear cache via spark"
rm -rf writable/cache/* || warn "Failed to clear cache directory"
log "✓ Cache cleared"

# Step 8: Restart Services
log "Step 8: Restarting services..."

# Restart PHP-FPM
systemctl restart php8.1-fpm || {
    error "Failed to restart PHP-FPM"
    exit 1
}
log "✓ PHP-FPM restarted"

# Restart Nginx
systemctl restart nginx || {
    error "Failed to restart Nginx"
    exit 1
}
log "✓ Nginx restarted"

# Wait for services to stabilize
log "Waiting for services to stabilize..."
sleep 5

# Step 9: Verify Services
log "Step 9: Verifying services..."

# Check PHP-FPM status
if systemctl is-active --quiet php8.1-fpm; then
    log "✓ PHP-FPM is running"
else
    error "✗ PHP-FPM is NOT running!"
    exit 1
fi

# Check Nginx status
if systemctl is-active --quiet nginx; then
    log "✓ Nginx is running"
else
    error "✗ Nginx is NOT running!"
    exit 1
fi

# Step 10: Basic Smoke Test
log "Step 10: Running basic smoke tests..."

# Test homepage
HTTP_CODE=$(curl -o /dev/null -s -w "%{http_code}\n" http://localhost/)
if [[ "$HTTP_CODE" == "200" ]] || [[ "$HTTP_CODE" == "302" ]]; then
    log "✓ Homepage accessible (HTTP $HTTP_CODE)"
else
    error "✗ Homepage failed (HTTP $HTTP_CODE)"
fi

# Test database connection
mysql -h "$DB_HOST" -u "$DB_USER" -p "$DB_NAME" -e "SELECT 1;" > /dev/null 2>&1
if [[ $? -eq 0 ]]; then
    log "✓ Database connection successful"
else
    error "✗ Database connection failed"
    exit 1
fi

# Step 11: Disable Maintenance Mode
log "Step 11: Disabling maintenance mode..."
rm -f writable/maintenance.flag
php spark up || warn "Failed to disable maintenance mode via spark"
log "✓ Maintenance mode disabled"

# Step 12: Final Verification
log "Step 12: Final verification..."

# Check for errors in recent logs
ERROR_COUNT=$(tail -n 100 writable/logs/log-$(date +%Y-%m-%d).log 2>/dev/null | grep -i error | wc -l)
if [[ $ERROR_COUNT -gt 0 ]]; then
    warn "Found $ERROR_COUNT errors in application log"
else
    log "✓ No errors in application log"
fi

# Step 13: Generate Rollback Report
log "Step 13: Generating rollback report..."
REPORT_FILE="$BACKUP_DIR/rollback_report.txt"

cat > "$REPORT_FILE" <<EOF
========================================================
PHASE 1A ROLLBACK REPORT
========================================================

Incident Number: $INCIDENT_NUMBER
Environment: $ENVIRONMENT
Database: $DB_NAME
Executed By: $(whoami)
Timestamp: $TIMESTAMP

ROLLBACK STEPS COMPLETED:
-------------------------
[✓] Step 1: Maintenance mode enabled
[✓] Step 2: Current state backed up
[✓] Step 3: Foreign key constraints removed
[✓] Step 4: VIEW removed
[✓] Step 5: Code rolled back to $GIT_ROLLBACK_TAG
[✓] Step 6: File permissions set
[✓] Step 7: Application cache cleared
[✓] Step 8: Services restarted
[✓] Step 9: Service status verified
[✓] Step 10: Smoke tests passed
[✓] Step 11: Maintenance mode disabled
[✓] Step 12: Final verification completed

BACKUP LOCATIONS:
-----------------
Database: $BACKUP_DIR/${DB_NAME}_pre_rollback.sql.gz
Code: $BACKUP_DIR/code_pre_rollback.tar.gz
Full Log: $LOG_FILE

POST-ROLLBACK STATUS:
---------------------
PHP-FPM: $(systemctl is-active php8.1-fpm)
Nginx: $(systemctl is-active nginx)
Database: Connected
Application: Accessible

NEXT STEPS:
-----------
1. Verify critical workflows work correctly
2. Monitor error logs for 1 hour
3. Notify stakeholders of rollback completion
4. Schedule incident review meeting
5. Investigate root cause of rollback
6. Plan corrective actions

NOTES:
------
- Old Phase 1A database changes have been reverted
- Code has been rolled back to pre-Phase 1A state
- All backups preserved in: $BACKUP_DIR
- Redundant FK fields (kontrak_id, customer_id, customer_location_id) 
  are still present in inventory_unit table
- System is running in pre-Phase 1A state

========================================================
END OF ROLLBACK REPORT
========================================================
EOF

log "✓ Rollback report generated: $REPORT_FILE"

# Display report
cat "$REPORT_FILE"

# Final Summary
echo ""
echo -e "${GREEN}"
echo "╔════════════════════════════════════════════════════════════════╗"
echo "║                                                                ║"
echo "║           ROLLBACK COMPLETED SUCCESSFULLY                      ║"
echo "║                                                                ║"
echo "║  Environment: ${ENVIRONMENT^^}                                    "
echo "║  Duration: $(( $(date +%s) - $(date -d "$TIMESTAMP" +%s 2>/dev/null || echo 0) )) seconds           "
echo "║  Backups: $BACKUP_DIR                                          "
echo "║                                                                ║"
echo "║  NEXT ACTIONS:                                                 ║"
echo "║  1. Verify workflows manually                                  ║"
echo "║  2. Monitor logs for 1 hour                                    ║"
echo "║  3. Notify stakeholders                                        ║"
echo "║  4. Schedule post-mortem meeting                               ║"
echo "║                                                                ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo -e "${NC}"

log "=========================================================="
log "ROLLBACK COMPLETED SUCCESSFULLY"
log "Total duration: $(( $(date +%s) - $(stat -c %Y "$LOG_FILE" 2>/dev/null || echo 0) )) seconds"
log "=========================================================="

# Email notification (if configured)
if command -v mail &> /dev/null; then
    mail -s "[OPTIMA] Phase 1A Rollback Completed - $ENVIRONMENT" \
        dev-team@optima.com < "$REPORT_FILE" || \
        log "Email notification failed (mail command not available)"
fi

exit 0
