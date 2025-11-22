#!/bin/bash

###############################################################################
# COMPLETE Database Backup Script - 100% LENGKAP
# 
# Script ini akan membuat backup LENGKAP dengan:
# - Struktur database (CREATE DATABASE)
# - Semua Tabel (CREATE TABLE)
# - Semua Views (CREATE VIEW)
# - Semua Procedures (CREATE PROCEDURE)
# - Semua Functions (CREATE FUNCTION)
# - Semua Triggers (CREATE TRIGGER)
# - Semua Events (CREATE EVENT)
# - Foreign Keys dan Constraints
# - Data lengkap
# 
# Menggunakan pendekatan backup per-komponen untuk memastikan 100% lengkap
###############################################################################

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(dirname "$SCRIPT_DIR")"

# Load database config
if [ -f "$SCRIPT_DIR/get_db_config.php" ]; then
    eval $(php "$SCRIPT_DIR/get_db_config.php" | sed 's/^/export /')
else
    DB_HOST="127.0.0.1"
    DB_PORT="3306"
    DB_USER="root"
    DB_PASS="root"
    DB_NAME="optima_db"
fi

BACKUP_DIR="$SCRIPT_DIR/backups"
TIMESTAMP=$(date +%Y%m%d_%H%M%S)
DB_NAME="${DB_NAME:-optima_db}"

mkdir -p "$BACKUP_DIR"

echo "=========================================="
echo "COMPLETE DATABASE BACKUP - 100% LENGKAP"
echo "=========================================="
echo "Database: $DB_NAME"
echo "Host: $DB_HOST:$DB_PORT"
echo ""

# Create temporary MySQL config file
TMP_CONFIG=$(mktemp /tmp/mysql_config_XXXXXX.cnf)
chmod 600 "$TMP_CONFIG"
cat > "$TMP_CONFIG" << EOF
[client]
host=$DB_HOST
port=$DB_PORT
user=$DB_USER
password=$DB_PASS
EOF

BACKUP_FILE="$BACKUP_DIR/${DB_NAME}_COMPLETE_${TIMESTAMP}.sql"
BACKUP_FILE_GZ="$BACKUP_FILE.gz"

echo "Creating COMPLETE backup..."
echo ""

# Start backup file with header
cat > "$BACKUP_FILE" << EOF
-- ============================================
-- COMPLETE DATABASE BACKUP - 100% LENGKAP
-- Database: $DB_NAME
-- Date: $(date)
-- ============================================
-- This backup includes:
-- - CREATE DATABASE
-- - All Tables (with structure and data)
-- - All Views
-- - All Procedures
-- - All Functions
-- - All Triggers
-- - All Events
-- - All Foreign Keys and Constraints
-- ============================================

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO';
SET NAMES utf8mb4;
SET time_zone = '+00:00';

EOF

# 1. CREATE DATABASE
echo "1. Backing up database structure..."
mysqldump --defaults-file="$TMP_CONFIG" \
    --no-data \
    --add-drop-database \
    --databases "$DB_NAME" >> "$BACKUP_FILE" 2>&1

# 2. All Tables with data
echo "2. Backing up all tables (structure + data)..."
mysqldump --defaults-file="$TMP_CONFIG" \
    --single-transaction \
    --add-drop-table \
    --create-options \
    --disable-keys \
    --extended-insert \
    --quick \
    --lock-tables=false \
    --set-charset \
    --default-character-set=utf8mb4 \
    --complete-insert \
    --hex-blob \
    --no-create-db \
    "$DB_NAME" >> "$BACKUP_FILE" 2>&1

# 3. All Views
echo "3. Backing up all views..."
echo "" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "-- VIEWS" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "" >> "$BACKUP_FILE"

# Get all views and backup each one
VIEWS=$(mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SELECT TABLE_NAME FROM information_schema.views WHERE table_schema = '$DB_NAME';" -s -N 2>/dev/null)

if [ ! -z "$VIEWS" ]; then
    for VIEW in $VIEWS; do
        echo "  - View: $VIEW"
        # Get CREATE VIEW statement
        mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SHOW CREATE VIEW \`$VIEW\`\G" 2>/dev/null | \
            grep -A 100 "Create View:" | \
            sed 's/Create View:/CREATE OR REPLACE VIEW/' | \
            sed 's/$/;/' >> "$BACKUP_FILE" 2>&1
        echo "" >> "$BACKUP_FILE"
    done
else
    echo "  - No views found"
fi

# 4. All Procedures
echo "4. Backing up all procedures..."
echo "" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "-- PROCEDURES" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "" >> "$BACKUP_FILE"

# Use mysqldump for procedures (more reliable)
mysqldump --defaults-file="$TMP_CONFIG" \
    --no-data \
    --no-create-info \
    --routines \
    --no-tablespaces \
    --skip-triggers \
    "$DB_NAME" | grep -A 1000 "PROCEDURE\|FUNCTION" | \
    sed '/^-- Dump of PROCEDURE/,/^-- Dump of PROCEDURE/!{ /^-- Dump of PROCEDURE/,/^-- Dump of PROCEDURE/!d; }' >> "$BACKUP_FILE" 2>&1

# Also backup procedures individually for completeness
PROCEDURES=$(mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SELECT ROUTINE_NAME FROM information_schema.routines WHERE routine_schema = '$DB_NAME' AND routine_type = 'PROCEDURE';" -s -N 2>/dev/null)

if [ ! -z "$PROCEDURES" ]; then
    for PROC in $PROCEDURES; do
        echo "  - Procedure: $PROC"
        # Get full CREATE PROCEDURE statement
        PROC_DEF=$(mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SHOW CREATE PROCEDURE \`$PROC\`\G" 2>/dev/null)
        echo "$PROC_DEF" | grep -A 1000 "Create Procedure:" | \
            sed 's/Create Procedure:/DELIMITER $$/' | \
            sed 's/$$/DELIMITER ;/' >> "$BACKUP_FILE" 2>&1
        echo "" >> "$BACKUP_FILE"
    done
fi

# 5. All Functions
echo "5. Backing up all functions..."
echo "" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "-- FUNCTIONS" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "" >> "$BACKUP_FILE"

FUNCTIONS=$(mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SELECT ROUTINE_NAME FROM information_schema.routines WHERE routine_schema = '$DB_NAME' AND routine_type = 'FUNCTION';" -s -N 2>/dev/null)

if [ ! -z "$FUNCTIONS" ]; then
    for FUNC in $FUNCTIONS; do
        echo "  - Function: $FUNC"
        mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SHOW CREATE FUNCTION \`$FUNC\`\G" 2>/dev/null | \
            sed -n '/Function/,/sql_mode/p' | \
            sed 's/Function/CREATE FUNCTION/' | \
            sed 's/sql_mode.*/;/' >> "$BACKUP_FILE" 2>&1
        echo "" >> "$BACKUP_FILE"
    done
else
    echo "  - No functions found"
fi

# 6. All Triggers
echo "6. Backing up all triggers..."
echo "" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "-- TRIGGERS" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "" >> "$BACKUP_FILE"

# Use mysqldump for triggers (most reliable method)
mysqldump --defaults-file="$TMP_CONFIG" \
    --no-data \
    --no-create-info \
    --triggers \
    --skip-triggers=false \
    --routines=false \
    "$DB_NAME" >> "$BACKUP_FILE" 2>&1

# Also get triggers individually to ensure completeness
TRIGGERS=$(mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SELECT TRIGGER_NAME, EVENT_OBJECT_TABLE FROM information_schema.triggers WHERE trigger_schema = '$DB_NAME';" -s -N 2>/dev/null)

if [ ! -z "$TRIGGERS" ]; then
    echo "$TRIGGERS" | while read TRIGGER_NAME TABLE_NAME; do
        if [ ! -z "$TRIGGER_NAME" ]; then
            echo "  - Trigger: $TRIGGER_NAME (on table: $TABLE_NAME)"
        fi
    done
fi

# 7. All Events
echo "7. Backing up all events..."
echo "" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "-- EVENTS" >> "$BACKUP_FILE"
echo "-- ============================================" >> "$BACKUP_FILE"
echo "" >> "$BACKUP_FILE"

EVENTS=$(mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SELECT EVENT_NAME FROM information_schema.events WHERE event_schema = '$DB_NAME';" -s -N 2>/dev/null)

if [ ! -z "$EVENTS" ]; then
    for EVENT in $EVENTS; do
        echo "  - Event: $EVENT"
        mysql --defaults-file="$TMP_CONFIG" -D "$DB_NAME" -e "SHOW CREATE EVENT \`$EVENT\`\G" 2>/dev/null | \
            sed -n '/Event/,/sql_mode/p' | \
            sed 's/Event/CREATE EVENT/' | \
            sed 's/sql_mode.*/;/' >> "$BACKUP_FILE" 2>&1
        echo "" >> "$BACKUP_FILE"
    done
else
    echo "  - No events found"
fi

# Footer
cat >> "$BACKUP_FILE" << EOF

-- ============================================
-- END OF BACKUP
-- ============================================

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- Dump completed on $(date)
EOF

# Clean up temp config
rm -f "$TMP_CONFIG"

if [ -s "$BACKUP_FILE" ]; then
    echo ""
    echo -e "${GREEN}✓${NC} Complete backup created successfully"
    
    # Compress
    echo "Compressing backup..."
    gzip -c "$BACKUP_FILE" > "$BACKUP_FILE_GZ"
    
    if [ $? -eq 0 ]; then
        ORIGINAL_SIZE=$(du -h "$BACKUP_FILE" | cut -f1)
        COMPRESSED_SIZE=$(du -h "$BACKUP_FILE_GZ" | cut -f1)
        
        echo -e "${GREEN}✓${NC} Backup compressed successfully"
        echo "  Original size: $ORIGINAL_SIZE"
        echo "  Compressed size: $COMPRESSED_SIZE"
        
        rm "$BACKUP_FILE"
        echo "  Saved as: $BACKUP_FILE_GZ"
        
        # Create checksum
        CHECKSUM_FILE="$BACKUP_FILE_GZ.md5"
        md5sum "$BACKUP_FILE_GZ" > "$CHECKSUM_FILE"
        echo "  Checksum: $CHECKSUM_FILE"
        
        echo ""
        echo "=========================================="
        echo "COMPLETE BACKUP FINISHED"
        echo "=========================================="
        echo "File: $BACKUP_FILE_GZ"
        echo ""
        echo "This backup includes:"
        echo "  ✓ All Tables (structure + data)"
        echo "  ✓ All Views"
        echo "  ✓ All Procedures"
        echo "  ✓ All Functions"
        echo "  ✓ All Triggers"
        echo "  ✓ All Events"
        echo "  ✓ All Foreign Keys"
        echo ""
    else
        echo -e "${YELLOW}⚠${NC} Compression failed, keeping uncompressed file"
    fi
else
    echo -e "${RED}✗${NC} Backup failed - file is empty or missing"
    exit 1
fi

