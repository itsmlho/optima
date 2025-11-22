#!/bin/bash

###############################################################################
# Database Backup Script
# 
# Script ini akan membuat backup lengkap database dengan:
# - Struktur database (CREATE DATABASE)
# - Struktur tabel (CREATE TABLE)
# - Foreign Keys dan Constraints
# - Data lengkap
# - Triggers, Procedures, Functions, Events
# - Views
# 
# Cocok untuk migrasi ke Windows atau backup lengkap
###############################################################################

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Load database config from PHP
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Load database config from PHP
if [ -f "$SCRIPT_DIR/get_db_config.php" ]; then
    eval $(php "$SCRIPT_DIR/get_db_config.php" | sed 's/^/export /')
else
    # Default values (fallback)
    DB_HOST="127.0.0.1"
    DB_PORT="3306"
    DB_USER="root"
    DB_PASS="root"
fi

BACKUP_DIR="$SCRIPT_DIR/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)

# Create backup directory
mkdir -p "$BACKUP_DIR"

echo "=========================================="
echo "DATABASE BACKUP SCRIPT"
echo "=========================================="
echo ""

# Check if audit result exists
if [ -f "$SCRIPT_DIR/database_audit_result.json" ]; then
    echo -e "${GREEN}✓${NC} Found audit result file"
    
    # Extract active databases from JSON (requires jq or manual parsing)
    if command -v jq &> /dev/null; then
        ACTIVE_DBS=$(jq -r '.active_databases[]' "$SCRIPT_DIR/database_audit_result.json")
    else
        # Fallback: use PHP to parse JSON
        ACTIVE_DBS=$(php -r "echo implode(' ', json_decode(file_get_contents('$SCRIPT_DIR/database_audit_result.json'), true)['active_databases'] ?? []));")
    fi
    
    # If still empty, use config database
    if [ -z "$ACTIVE_DBS" ]; then
        ACTIVE_DBS="$DB_NAME"
    fi
else
    echo -e "${YELLOW}⚠${NC} Audit result not found, using database from config: $DB_NAME"
    ACTIVE_DBS="$DB_NAME"
fi

# If ACTIVE_DBS is empty or not set, use default
if [ -z "$ACTIVE_DBS" ]; then
    ACTIVE_DBS="optima_db"
fi

echo "Databases to backup:"
for db in $ACTIVE_DBS; do
    echo "  - $db"
done
echo ""

# Function to backup a single database
backup_database() {
    local DB_NAME=$1
    local BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_${TIMESTAMP}.sql"
    local BACKUP_FILE_GZ="$BACKUP_FILE.gz"
    
    echo "=========================================="
    echo "Backing up: $DB_NAME"
    echo "=========================================="
    
    # Create temporary MySQL config file for secure password handling
    TMP_CONFIG=$(mktemp /tmp/mysql_config_XXXXXX.cnf)
    chmod 600 "$TMP_CONFIG"
    cat > "$TMP_CONFIG" << EOF
[client]
host=$DB_HOST
port=$DB_PORT
user=$DB_USER
password=$DB_PASS
EOF
    
    # Check if database exists
    DB_EXISTS=$(mysql --defaults-file="$TMP_CONFIG" -e "SHOW DATABASES LIKE '$DB_NAME';" 2>/dev/null | grep -c "$DB_NAME")
    
    if [ "$DB_EXISTS" -eq 0 ]; then
        echo -e "${RED}✗${NC} Database '$DB_NAME' does not exist. Skipping..."
        rm -f "$TMP_CONFIG"
        return 1
    fi
    
    echo "Creating backup: $BACKUP_FILE"
    
    # Full backup with all options
    mysqldump \
        --defaults-file="$TMP_CONFIG" \
        --single-transaction \
        --routines \
        --triggers \
        --events \
        --add-drop-database \
        --add-drop-table \
        --add-drop-trigger \
        --add-locks \
        --create-options \
        --disable-keys \
        --extended-insert \
        --quick \
        --lock-tables=false \
        --set-charset \
        --default-character-set=utf8mb4 \
        --databases "$DB_NAME" > "$BACKUP_FILE" 2>&1
    
    # Clean up temp config
    rm -f "$TMP_CONFIG"
    
    if [ $? -eq 0 ]; then
        echo -e "${GREEN}✓${NC} Backup created successfully"
        
        # Compress backup
        echo "Compressing backup..."
        gzip -c "$BACKUP_FILE" > "$BACKUP_FILE_GZ"
        
        if [ $? -eq 0 ]; then
            ORIGINAL_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
            COMPRESSED_SIZE=$(du -h "$BACKUP_FILE_GZ" | cut -f1)
            
            echo -e "${GREEN}✓${NC} Backup compressed successfully"
            echo "  Original size: $ORIGINAL_SIZE"
            echo "  Compressed size: $COMPRESSED_SIZE"
            
            # Remove uncompressed file to save space
            rm "$BACKUP_FILE"
            echo "  Saved as: $BACKUP_FILE_GZ"
        else
            echo -e "${YELLOW}⚠${NC} Compression failed, keeping uncompressed file"
        fi
        
        # Create checksum
        CHECKSUM_FILE="$BACKUP_FILE_GZ.md5"
        md5sum "$BACKUP_FILE_GZ" > "$CHECKSUM_FILE"
        echo "  Checksum: $CHECKSUM_FILE"
        
        return 0
    else
        echo -e "${RED}✗${NC} Backup failed!"
        rm -f "$BACKUP_FILE"
        return 1
    fi
}

# Backup all active databases
SUCCESS_COUNT=0
FAIL_COUNT=0

for DB in $ACTIVE_DBS; do
    if backup_database "$DB"; then
        ((SUCCESS_COUNT++))
    else
        ((FAIL_COUNT++))
    fi
    echo ""
done

# Create summary file
SUMMARY_FILE="$BACKUP_DIR/backup_summary_${TIMESTAMP}.txt"
cat > "$SUMMARY_FILE" << EOF
========================================
DATABASE BACKUP SUMMARY
========================================
Date: $(date)
Host: $DB_HOST:$DB_PORT
User: $DB_USER

Backed up databases:
EOF

for DB in $ACTIVE_DBS; do
    BACKUP_FILE_GZ="$BACKUP_DIR/${DB}_${TIMESTAMP}.sql.gz"
    if [ -f "$BACKUP_FILE_GZ" ]; then
        SIZE=$(du -h "$BACKUP_FILE_GZ" | cut -f1)
        echo "  - $DB: $BACKUP_FILE_GZ ($SIZE)" >> "$SUMMARY_FILE"
    fi
done

cat >> "$SUMMARY_FILE" << EOF

Results:
  Success: $SUCCESS_COUNT
  Failed: $FAIL_COUNT

Backup location: $BACKUP_DIR

To restore:
  1. Extract: gunzip database_name_${TIMESTAMP}.sql.gz
  2. Import: mysql -u root -p < database_name_${TIMESTAMP}.sql

Or directly:
  gunzip < database_name_${TIMESTAMP}.sql.gz | mysql -u root -p
EOF

echo "=========================================="
echo "BACKUP COMPLETE"
echo "=========================================="
echo -e "${GREEN}Success:${NC} $SUCCESS_COUNT"
echo -e "${RED}Failed:${NC} $FAIL_COUNT"
echo ""
echo "Backup location: $BACKUP_DIR"
echo "Summary file: $SUMMARY_FILE"
echo ""

# List all backup files
echo "Backup files created:"
ls -lh "$BACKUP_DIR"/*${TIMESTAMP}* 2>/dev/null | awk '{print "  " $9 " (" $5 ")"}'

echo ""
echo "=========================================="

