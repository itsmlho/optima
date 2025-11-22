#!/bin/bash

###############################################################################
# Database Restore Script for Windows Migration
# 
# Script ini untuk restore database setelah migrasi ke Windows
# Usage: ./restore_database.sh [database_name] [backup_file]
###############################################################################

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
BACKUP_DIR="$SCRIPT_DIR/backups"

# Default values
DB_HOST="127.0.0.1"
DB_PORT="3306"
DB_USER="root"
DB_PASS=""

echo "=========================================="
echo "DATABASE RESTORE SCRIPT"
echo "=========================================="
echo ""

# Function to restore a database
restore_database() {
    local BACKUP_FILE=$1
    
    if [ ! -f "$BACKUP_FILE" ]; then
        echo -e "${RED}✗${NC} Backup file not found: $BACKUP_FILE"
        return 1
    fi
    
    echo "Restoring from: $BACKUP_FILE"
    
    # Check if file is compressed
    if [[ "$BACKUP_FILE" == *.gz ]]; then
        echo "Decompressing and restoring..."
        gunzip -c "$BACKUP_FILE" | mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS"
    else
        echo "Restoring..."
        mysql -h"$DB_HOST" -P"$DB_PORT" -u"$DB_USER" -p"$DB_PASS" < "$BACKUP_FILE"
    fi
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} Database restored successfully"
        return 0
    else
        echo -e "${RED}✗${NC} Restore failed!"
        return 1
    fi
}

# If backup file is provided as argument
if [ $# -ge 1 ]; then
    BACKUP_FILE=$1
    if [ ! -f "$BACKUP_FILE" ]; then
        # Try in backup directory
        BACKUP_FILE="$BACKUP_DIR/$1"
    fi
    restore_database "$BACKUP_FILE"
    exit $?
fi

# Interactive mode: list available backups
echo "Available backup files:"
echo "-----------------------------------"
BACKUP_FILES=($(ls -1t "$BACKUP_DIR"/*.sql.gz 2>/dev/null))

if [ ${#BACKUP_FILES[@]} -eq 0 ]; then
    echo -e "${RED}No backup files found in $BACKUP_DIR${NC}"
    exit 1
fi

for i in "${!BACKUP_FILES[@]}"; do
    FILE=$(basename "${BACKUP_FILES[$i]}")
    SIZE=$(du -h "${BACKUP_FILES[$i]}" | cut -f1)
    echo "  $((i+1)). $FILE ($SIZE)"
done

echo ""
read -p "Enter backup file number to restore (or 'all' for all): " choice

if [ "$choice" == "all" ]; then
    echo "Restoring all databases..."
    for BACKUP_FILE in "${BACKUP_FILES[@]}"; do
        restore_database "$BACKUP_FILE"
        echo ""
    done
elif [[ "$choice" =~ ^[0-9]+$ ]] && [ "$choice" -ge 1 ] && [ "$choice" -le ${#BACKUP_FILES[@]} ]; then
    BACKUP_FILE="${BACKUP_FILES[$((choice-1))]}"
    restore_database "$BACKUP_FILE"
else
    echo -e "${RED}Invalid choice${NC}"
    exit 1
fi

echo ""
echo "=========================================="
echo "RESTORE COMPLETE"
echo "=========================================="

