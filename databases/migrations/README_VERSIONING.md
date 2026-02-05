# Quotation Versioning & Revision Tracking System

## Overview
Enterprise-grade versioning and audit trail system for quotations management.

## Implementation Date
**2026-01-30**

## Features Implemented

### 1. **Database Schema** ✅
- **quotations table** - Added versioning columns:
  - `version` (INT) - Auto-incrementing version number
  - `revision_status` (ENUM: ORIGINAL/REVISED) - Tracks if quotation was modified after SENT
  - `revised_at` (DATETIME) - Timestamp of revision
  - `revised_by` (INT) - User who made the revision

- **quotation_history table** - Complete audit trail:
  - Tracks all changes (CREATED, UPDATED, REVISED, DELETED, etc.)
  - Stores old and new values in JSON format
  - Records user, IP address, and browser info
  - Human-readable changes summary

- **quotation_notifications table** - Customer notification queue:
  - Tracks when quotations are sent/updated
  - Email notification management
  - Delivery status tracking

### 2. **Backend Logic** ✅
- **Marketing.php Controller**:
  - `updateQuotation()` - Enhanced with versioning:
    * Auto-increment version if quotation was SENT or DEAL
    * Mark as REVISED when editing sent quotations
    * Log all changes to history table
  - `buildChangesSummary()` - Creates human-readable change descriptions
  - `logQuotationChange()` - Saves detailed audit trail
  - `deleteQuotation()` - Logs deletion with context
  - `getQuotationHistory()` - Retrieves complete change history

### 3. **Frontend UI** ✅
- **quotations.php View**:
  - Version badge display (v1, v2, v3...)
  - REVISED status badge (yellow warning)
  - History button on quotation detail modal
  - History viewer with formatted timeline:
    * Version numbers
    * Action types with colored badges
    * User who made changes
    * Timestamp
    * Changes summary

### 4. **Business Logic** ✅
- **Workflow Rules**:
  - Editing quotations in PROSPECT/QUOTATION stage → Normal update (version stays same)
  - Editing quotations in SENT/DEAL stage → Auto-revision (version increments)
  - Quotations with contracts → Cannot edit/delete
  - All changes tracked in audit log

## Workflow Diagram

```
PROSPECT → QUOTATION → SENT → DEAL
   |          |          |      |
   v          v          v      v
[Edit OK]  [Edit OK] [REVISION] [REVISION]
Version 1  Version 1  Version 2  Version 3
```

## Database Migration Steps

### Step 1: Run SQL Migration
```sql
-- Execute file: quotation_versioning_system.sql
-- Location: databases/migrations/quotation_versioning_system.sql
```

### Step 2: Verify Tables Created
```sql
-- Check tables exist
SHOW TABLES LIKE 'quotation%';

-- Expected output:
-- quotation_history
-- quotation_notifications  
-- quotations (with new columns)
```

### Step 3: Verify Data Migrated
```sql
-- Check existing quotations have version info
SELECT id_quotation, quotation_number, version, revision_status 
FROM quotations 
LIMIT 10;

-- Check initial history records created
SELECT COUNT(*) FROM quotation_history;
```

## Usage Examples

### View Quotation History
1. Open quotation detail modal
2. Click "History" button (gray button with clock icon)
3. View complete timeline of all changes

### Edit Quotation (Creates Revision)
1. Open quotation in SENT stage
2. Click "Edit" button
3. Modify price or description
4. Click "Save"
5. System automatically:
   - Increments version number
   - Marks as REVISED
   - Logs changes to history
   - Shows success message with new version

### Delete Quotation (Logged)
1. Open quotation detail
2. Click "Delete" button
3. Confirm deletion
4. System logs deletion details before removing

## API Endpoints

### Get Quotation History
```javascript
GET /marketing/quotations/history/{id}

Response:
{
  "success": true,
  "data": [
    {
      "version": 2,
      "action_type": "REVISED",
      "changed_by_name": "Admin User",
      "changed_at": "2026-01-30 14:30:00",
      "changes_summary": "Total amount changed from Rp 100,000,000 to Rp 120,000,000"
    }
  ]
}
```

## Testing Checklist

- [ ] Run SQL migration without errors
- [ ] Verify all tables and columns created
- [ ] Check existing quotations have version=1
- [ ] Edit quotation in PROSPECT stage (should NOT create revision)
- [ ] Edit quotation in SENT stage (SHOULD create revision)
- [ ] View history shows all changes
- [ ] Delete quotation logs deletion
- [ ] Version badge displays correctly
- [ ] REVISED badge shows for modified quotations
- [ ] History modal displays formatted timeline

## Rollback Instructions

If you need to rollback these changes:

```sql
-- Run the rollback section in quotation_versioning_system.sql
-- (Uncomment the rollback script at bottom of file)

DROP VIEW IF EXISTS vw_quotation_history_detail;
DROP PROCEDURE IF EXISTS sp_log_quotation_change;
DROP TABLE IF EXISTS quotation_notifications;
DROP TABLE IF EXISTS quotation_history;

ALTER TABLE quotations 
DROP COLUMN version,
DROP COLUMN revision_status,
DROP COLUMN original_quotation_id,
DROP COLUMN revised_at,
DROP COLUMN revised_by;
```

## Benefits

### ✅ Enterprise Compliance
- Complete audit trail for compliance
- All changes tracked with user/timestamp
- Cannot alter history (immutable log)

### ✅ Business Intelligence
- Understand revision patterns
- Track pricing negotiations
- Identify frequently changed items

### ✅ Customer Trust
- Transparent change history
- Clear version tracking
- Professional revision process

### ✅ Risk Management
- Prevent unauthorized changes
- Audit who changed what
- Rollback capability (if needed)

## Future Enhancements

### Phase 2 (Planned):
- [ ] Email notifications for revisions
- [ ] Compare versions side-by-side
- [ ] Customer acceptance tracking
- [ ] Automated approval workflow
- [ ] Change request forms
- [ ] Digital signatures

### Phase 3 (Future):
- [ ] Integration with contract versioning
- [ ] Automated pricing approval rules
- [ ] Customer portal access to history
- [ ] Export history to PDF/Excel
- [ ] Advanced analytics dashboard

## Support

For issues or questions:
- Check database connection
- Verify migration ran completely
- Check browser console for JS errors
- Review server logs for PHP errors

## Version History

- **v1.0** (2026-01-30) - Initial implementation
  - Database schema created
  - Backend versioning logic
  - Frontend history viewer
  - Audit trail complete
