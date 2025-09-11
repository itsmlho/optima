#!/bin/bash

# Activity Logging Audit Script for OPTIMA
# This script scans all controllers to identify CRUD operations that might need logging

echo "=== OPTIMA Activity Logging Audit ==="
echo "Scanning controllers for potential missing activity logs..."
echo ""

CONTROLLERS_DIR="/opt/lampp/htdocs/optima1/app/Controllers"

echo "1. Controllers using ActivityLoggingTrait:"
grep -r "ActivityLoggingTrait" "$CONTROLLERS_DIR" --include="*.php" | cut -d: -f1 | sort | uniq

echo ""
echo "2. Controllers with CRUD operations (insert/update/delete) but no ActivityLoggingTrait:"
echo ""

# Find all PHP files in Controllers
find "$CONTROLLERS_DIR" -name "*.php" -type f | while read file; do
    # Check if file has CRUD operations
    has_crud=$(grep -E "(->insert\(|->update\(|->delete\(|->save\()" "$file" | wc -l)
    
    # Check if file uses ActivityLoggingTrait
    has_trait=$(grep -c "ActivityLoggingTrait" "$file")
    
    if [ "$has_crud" -gt 0 ] && [ "$has_trait" -eq 0 ]; then
        echo "❌ $file (has $has_crud CRUD operations, no logging trait)"
        # Show the CRUD operations found
        grep -n -E "(->insert\(|->update\(|->delete\(|->save\()" "$file" | head -3
        echo ""
    fi
done

echo ""
echo "3. Methods that might need logging (CREATE/UPDATE/DELETE actions):"
echo ""

# Find methods that look like CRUD operations
grep -r -n -E "public function (store|create|update|edit|delete|destroy)" "$CONTROLLERS_DIR" --include="*.php" | head -20

echo ""
echo "4. Database table operations without logging:"
echo ""

# Find direct database operations
grep -r -n -E "\\\$this->db->table.*->(insert|update|delete)" "$CONTROLLERS_DIR" --include="*.php" | head -10

echo ""
echo "=== Summary ==="
echo "Controllers scanned: $(find "$CONTROLLERS_DIR" -name "*.php" -type f | wc -l)"
echo "Controllers with ActivityLoggingTrait: $(grep -r "ActivityLoggingTrait" "$CONTROLLERS_DIR" --include="*.php" | cut -d: -f1 | sort | uniq | wc -l)"
echo ""
echo "Recommendation: Review files marked with ❌ to add activity logging"
