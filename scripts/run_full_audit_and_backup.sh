#!/bin/bash

###############################################################################
# Full Database Audit and Backup Script
# 
# Script ini akan:
# 1. Menjalankan audit database
# 2. Membuat backup lengkap database aktif
# 3. Menghasilkan laporan lengkap
###############################################################################

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

echo "=========================================="
echo "FULL DATABASE AUDIT & BACKUP"
echo "=========================================="
echo ""

# Step 1: Run database audit
echo "Step 1: Running database audit..."
echo "-----------------------------------"
cd "$PROJECT_ROOT"
php "$SCRIPT_DIR/database_audit.php"

if [ $? -ne 0 ]; then
    echo "ERROR: Database audit failed!"
    exit 1
fi

echo ""
echo "Step 2: Creating database backups..."
echo "-----------------------------------"

# Step 2: Run backup script
bash "$SCRIPT_DIR/database_backup.sh"

if [ $? -ne 0 ]; then
    echo "ERROR: Database backup failed!"
    exit 1
fi

echo ""
echo "=========================================="
echo "AUDIT & BACKUP COMPLETE"
echo "=========================================="
echo ""
echo "Files created:"
echo "  1. Audit reports in: $SCRIPT_DIR/"
echo "  2. Database backups in: $SCRIPT_DIR/backups/"
echo ""
echo "Next steps for Windows migration:"
echo "  1. Copy all backup files (*.sql.gz) to Windows"
echo "  2. Install MySQL/MariaDB on Windows"
echo "  3. Restore databases using restore script"
echo ""

