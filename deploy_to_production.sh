#!/bin/bash
# ================================================================
# PRODUCTION DEPLOYMENT SCRIPT - MARCH 7, 2026
# ================================================================
# Run this script on production server after database migration
# ================================================================

set -e  # Exit on error

echo "════════════════════════════════════════════════════════════"
echo " OPTIMA PRODUCTION DEPLOYMENT - CODE UPDATE"
echo " March 7, 2026"
echo "════════════════════════════════════════════════════════════"
echo ""

# Navigate to application directory
cd /home/u138256737/domains/sml.co.id/public_html/optima
echo "✓ Current directory: $(pwd)"
echo ""

# Check Git status
echo "1. Checking Git status..."
git status
echo ""

# Pull latest changes
echo "2. Pulling latest changes from Git..."
git pull origin main
echo "✓ Git pull completed"
echo ""

# Clear cache
echo "3. Clearing application cache..."
php spark cache:clear
echo "✓ Cache cleared"
echo ""

# Clear routes cache
echo "4. Clearing routes cache..."
php spark routes:clear 2>/dev/null || echo "  (routes:clear not available, skipping)"
echo ""

# Check current permissions count
echo "5. Verifying database migration..."
mysql -u u138256737_root_optima -p'@ITSupport25' u138256737_optima_db -e "SELECT COUNT(*) as Total_Permissions FROM permissions;"
echo ""

# Check new permissions
echo "6. Checking new menu permissions..."
mysql -u u138256737_root_optima -p'@ITSupport25' u138256737_optima_db -e "
SELECT 
  'Audit Approval' as Feature, 
  COUNT(*) as Permissions 
FROM permissions 
WHERE key_name LIKE 'marketing.audit_approval.%'
UNION ALL
SELECT 
  'Unit Audit' as Feature, 
  COUNT(*) as Permissions 
FROM permissions 
WHERE key_name LIKE 'service.unit_audit.%'
UNION ALL
SELECT 
  'Surat Jalan' as Feature, 
  COUNT(*) as Permissions 
FROM permissions 
WHERE key_name LIKE 'warehouse.movements.%';
"
echo ""

echo "════════════════════════════════════════════════════════════"
echo " DEPLOYMENT COMPLETED!"
echo "════════════════════════════════════════════════════════════"
echo ""
echo "Next steps:"
echo "1. Restart Apache (via cPanel or: systemctl restart apache2)"
echo "2. Test application:"
echo "   - Login at: https://optima.sml.co.id"
echo "   - Check Role Management"
echo "   - Check new menus (Audit Approval, Unit Audit, Surat Jalan)"
echo "3. Monitor error logs:"
echo "   tail -f writable/logs/log-*.php"
echo ""
echo "Backup location: ~/backup_march7_2026_*.sql"
echo ""
