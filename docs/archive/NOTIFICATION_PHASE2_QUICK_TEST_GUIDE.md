# ⚡ PHASE 2 NOTIFICATION - QUICK TEST GUIDE

**Phase**: HIGH Priority Notifications  
**Functions**: 22 (All implemented)  
**Controllers**: 9 modified  
**Database Rules**: 14 new rules  

---

## 📋 PRE-TEST CHECKLIST

### 1. Database Setup
```sql
-- Run Phase 2 migration
SOURCE databases/migrations/add_high_priority_notification_rules_phase2.sql;

-- Verify rules installed
SELECT COUNT(*) FROM notification_rules 
WHERE trigger_event IN (
    'quotation_created', 'quotation_stage_changed', 'contract_completed', 
    'po_created_from_quotation', 'workorder_ttr_updated', 'unit_verification_saved',
    'sparepart_validation_saved', 'sparepart_used', 'service_assignment_created',
    'service_assignment_updated', 'service_assignment_deleted', 'unit_location_updated',
    'warehouse_unit_updated', 'user_removed_from_division', 'user_permissions_updated',
    'permission_created', 'role_saved'
);
-- Expected: 14 rules (Phase 2) + 8 rules (Phase 1) = 22 total
```

### 2. Helper Functions Check
```bash
# Verify notification_helper.php loaded
grep -n "notify_quotation_created\|notify_contract_created\|notify_role_saved" app/Helpers/notification_helper.php
# Should show line numbers for all 22 HIGH priority helper functions
```

### 3. Browser Setup
- Open browser console (F12)
- Monitor Network tab for SSE connections
- Keep Notifications panel visible

---

## 🧪 TEST SCENARIOS BY CATEGORY

### CATEGORY 1: Marketing / Quotation (4 tests)

#### Test 1.1: Create Quotation
**Steps:**
1. Navigate to `/marketing/quotations`
2. Click "Create New Quotation"
3. Fill form: prospect_name, quotation_title, date, valid_until
4. Submit form

**Expected Notifications:**
- Title: "Quotation Baru: QT-XXXX"
- Message: "Quotation baru telah dibuat untuk customer [NAME]"
- Recipients: Marketing, Management, Sales Manager

#### Test 1.2: Change Quotation Stage
**Steps:**
1. Open existing quotation
2. Change stage: DRAFT → SENT → NEGOTIATION
3. Save changes

**Expected Notifications:**
- Title: "Stage Quotation Berubah: QT-XXXX"
- Message: "Stage quotation berubah dari DRAFT menjadi SENT"
- Recipients: Marketing, Management

#### Test 1.3: Mark Contract Complete
**Steps:**
1. Open quotation in DEAL stage
2. Click "Complete Contract" button
3. Confirm action

**Expected Notifications:**
- Title: "Kontrak Selesai: QT-XXXX"
- Message: "Kontrak telah diselesaikan. Nilai total: XXX"
- Recipients: Marketing, Finance, Management, Director

#### Test 1.4: Create PO from Quotation
**Steps:**
1. Open accepted quotation
2. Click "Create PO" button
3. Confirm action

**Expected Notifications:**
- Title: "PO Dibuat dari Quotation: QT-XXXX"
- Message: "Purchase Order PENDING telah dibuat"
- Recipients: Purchasing, Marketing, Management

---

### CATEGORY 2: WorkOrder Extended (4 tests)

#### Test 2.1: Update TTR
**Steps:**
1. Open work order
2. Update time_to_repair field
3. Save changes

**Expected Notifications:**
- Title: "TTR Update: WO-XXXX"
- Message: "Time To Repair telah diupdate menjadi XX jam"
- Recipients: Service, Management, Supervisor

#### Test 2.2: Save Unit Verification
**Steps:**
1. Open work order in progress
2. Fill unit verification form (serial, specs, etc.)
3. Submit verification

**Expected Notifications:**
- Title: "Unit Terverifikasi: UNIT-XXX"
- Message: "Unit telah diverifikasi untuk WO-XXXX"
- Recipients: Service, Warehouse, Management, Supervisor

#### Test 2.3: Validate Spareparts
**Steps:**
1. Open work order with planned spareparts
2. Fill sparepart validation (used quantity)
3. Submit validation

**Expected Notifications:**
- Title: "Sparepart Divalidasi: WO-XXXX"
- Message: "Validasi sparepart selesai. Total sparepart: X"
- Recipients: Service, Warehouse, Management

#### Test 2.4: Save Sparepart Usage
**Steps:**
1. Open work order ready for closure
2. Record sparepart usage (quantity used, returned)
3. Close work order

**Expected Notifications:**
- Title: "Sparepart Digunakan: [NAME]"
- Message: "Sparepart XXX (Y pcs) telah digunakan"
- Recipients: Service, Warehouse, Management

---

### CATEGORY 3: Service Assignments (3 tests)

#### Test 3.1: Create Assignment
**Steps:**
1. Navigate to `/service/area-management`
2. Click "New Assignment"
3. Fill: employee, area, role (PRIMARY/BACKUP), dates
4. Submit

**Expected Notifications:**
- Title: "Assignment Baru: [EMPLOYEE]"
- Message: "Employee ditugaskan ke area [AREA] dengan role [ROLE]"
- Recipients: Service, HR, Management

#### Test 3.2: Update Assignment
**Steps:**
1. Select existing assignment
2. Change assignment_type or dates
3. Save changes

**Expected Notifications:**
- Title: "Assignment Diupdate: [EMPLOYEE]"
- Message: "Assignment di area [AREA] telah diupdate"
- Recipients: Service, HR, Management

#### Test 3.3: Delete Assignment
**Steps:**
1. Select assignment to remove
2. Click Delete
3. Confirm deletion

**Expected Notifications:**
- Title: "Assignment Dihapus: [EMPLOYEE]"
- Message: "Assignment di area [AREA] telah dihapus"
- Recipients: Service, HR, Management

---

### CATEGORY 4: Unit Management (2 tests)

#### Test 4.1: Update Unit Location
**Steps:**
1. Navigate to `/operational/unit-rolling`
2. Select unit
3. Update current_location, latitude, longitude
4. Save

**Expected Notifications:**
- Title: "Lokasi Unit Diupdate: UNIT-XXX"
- Message: "Lokasi unit berubah dari [OLD] ke [NEW]"
- Recipients: Operational, Service, Management

#### Test 4.2: Update Warehouse Unit
**Steps:**
1. Navigate to `/warehouse/units`
2. Select unit
3. Update status_unit or lokasi_unit
4. Save changes

**Expected Notifications:**
- Title: "Unit Warehouse Diupdate: UNIT-XXX"
- Message: "Status and location updated"
- Recipients: Warehouse, Service, Management

---

### CATEGORY 5: Kontrak Management (3 tests)

#### Test 5.1: Create Contract
**Steps:**
1. Navigate to `/marketing/contracts`
2. Click "New Contract"
3. Fill: no_kontrak, customer_location, dates, nilai_total
4. Submit

**Expected Notifications:**
- Title: "Kontrak Baru: KTR-XXXX"
- Message: "Kontrak baru dibuat untuk [CUSTOMER]"
- Recipients: Marketing, Finance, Management, Director

#### Test 5.2: Update Contract
**Steps:**
1. Open existing contract
2. Change status (Pending → Aktif) or nilai_total
3. Save changes

**Expected Notifications:**
- Title: "Kontrak Diupdate: KTR-XXXX"
- Message: "Status: Pending → Aktif" (shows changes)
- Recipients: Marketing, Finance, Management

#### Test 5.3: Delete Contract
**Steps:**
1. Select contract (not in RENTAL status)
2. Click Delete
3. Confirm deletion

**Expected Notifications:**
- Title: "Kontrak Dihapus: KTR-XXXX"
- Message: "Kontrak untuk [CUSTOMER] telah dihapus"
- Recipients: Marketing, Finance, Management

---

### CATEGORY 6: User/Permission Security (4 tests) ⚠️ CRITICAL

#### Test 6.1: Remove User from Division
**Steps:**
1. Navigate to `/admin/user-management`
2. Select user with multiple divisions
3. Click "Remove from Division" for one division
4. Confirm

**Expected Notifications:**
- Title: "⚠️ User Dihapus dari Divisi: [USERNAME]"
- Message: "User dihapus dari divisi [DIVISION]. Segera lakukan review akses."
- Recipients: Admin, IT, Management, Super Admin
- **Priority**: CRITICAL - Security audit

#### Test 6.2: Update User Permissions
**Steps:**
1. Navigate to user edit page
2. Modify custom permissions (add/remove permissions)
3. Save changes

**Expected Notifications:**
- Title: "⚠️ Permission Diubah: [USERNAME]"
- Message: "Custom permission telah diubah. Lakukan audit."
- Recipients: Admin, IT, Management, Super Admin
- **Priority**: CRITICAL - Security compliance

#### Test 6.3: Create Permission
**Steps:**
1. Navigate to `/admin/permissions`
2. Click "Create New Permission"
3. Fill: key_name, display_name, module, page, action
4. Submit

**Expected Notifications:**
- Title: "🔐 Permission Baru Dibuat: [NAME]"
- Message: "Permission [CODE] telah dibuat untuk module [MODULE]"
- Recipients: Admin, IT, Super Admin

#### Test 6.4: Save Role (Create/Update)
**Steps:**
1. Navigate to `/admin/roles`
2. Create new role or edit existing
3. Assign permissions
4. Save

**Expected Notifications:**
- Title: "Role Saved: [ROLE_NAME]"
- Message: "Role created/updated with X permissions"
- Recipients: Admin, IT, Super Admin

---

## ✅ TEST VERIFICATION CHECKLIST

### For Each Test:
- [ ] Notification appears in UI within 5 seconds
- [ ] Title template correctly populated
- [ ] Message template correctly populated
- [ ] Correct users receive notification (check target_divisions, target_roles)
- [ ] URL link works and navigates to correct page
- [ ] Notification is persistent (survives page refresh)
- [ ] Notification can be marked as read
- [ ] Database record created in `notifications` table

### Database Verification Query:
```sql
-- Check last 10 notifications
SELECT 
    n.id,
    n.event_type,
    n.title,
    n.message,
    u.username as recipient,
    n.is_read,
    n.created_at
FROM notifications n
LEFT JOIN users u ON n.user_id = u.id
ORDER BY n.created_at DESC
LIMIT 10;
```

---

## 🐛 TROUBLESHOOTING

### Problem: No Notifications Appearing
**Check:**
1. Helper functions loaded: `php spark routes | grep notification`
2. Database rules exist: `SELECT COUNT(*) FROM notification_rules`
3. Browser console for JavaScript errors
4. Network tab for SSE connection

### Problem: Wrong Users Receiving Notifications
**Check:**
1. User divisions: `SELECT * FROM user_roles WHERE user_id = X`
2. User roles: `SELECT * FROM user_roles WHERE user_id = X`
3. Notification rule targets: `SELECT target_divisions, target_roles FROM notification_rules WHERE trigger_event = 'xxx'`

### Problem: Template Variables Not Replaced
**Check:**
1. Notification data in controller call
2. Template in notification_rules table
3. Browser console for data structure

---

## 📊 TEST COMPLETION MATRIX

| Category | Tests | Completed | Status |
|----------|-------|-----------|--------|
| Marketing/Quotation | 4 | __ / 4 | ⏳ |
| WorkOrder Extended | 4 | __ / 4 | ⏳ |
| Service Assignments | 3 | __ / 3 | ⏳ |
| Unit Management | 2 | __ / 2 | ⏳ |
| Kontrak Management | 3 | __ / 3 | ⏳ |
| User/Permission Security | 4 | __ / 4 | ⏳ |
| **TOTAL** | **22** | **__ / 22** | **__%** |

---

## 🎯 SUCCESS CRITERIA

**Phase 2 Testing Complete When:**
- ✅ All 22 tests pass successfully
- ✅ All notification templates render correctly
- ✅ All target users receive appropriate notifications
- ✅ No JavaScript errors in console
- ✅ Database records match expectations
- ✅ Security notifications (Category 6) audited and verified
- ✅ Performance acceptable (< 5 sec delivery time)

**After Testing:**
- Document any issues found
- Update notification rules if needed
- Proceed to Phase 3 (MEDIUM priority)
