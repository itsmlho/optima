# Contract & PO Renewal - Quick Reference

## ✅ Implemented (Sudah Selesai)

### 1. Customer Modal Enhancement
**File**: [app/Views/marketing/customer_management.php](app/Views/marketing/customer_management.php)

**Features**:
- ✅ Flattened modal structure dengan description lists
- ✅ Activity Log tab dengan timeline UI
- ✅ Contracts & PO tab dengan rental type badges
- ✅ Expiring soon warnings (30 hari sebelum berakhir)
- ✅ Expired contract indicators (badge merah)
- ✅ **Action buttons**: Edit, Delete, Renew

**Action Button Details**:

#### Edit Button
```javascript
function editContract(contractId) {
    // Redirect ke halaman edit kontrak
    window.location.href = `/marketing/kontrak/edit/${contractId}`;
}
```
- **Always visible** untuk semua kontrak
- Redirect ke `/marketing/kontrak/edit/{id}`
- Menggunakan endpoint existing: `Kontrak::edit($id)`

#### Delete Button
```javascript
function deleteContract(contractId) {
    // Show SweetAlert confirmation
    // AJAX delete ke /marketing/kontrak/delete/{id}
    // Refresh contracts tab after success
}
```
- **Always visible** untuk semua kontrak
- Confirmation dialog dengan SweetAlert2
- Endpoint: `POST /marketing/kontrak/delete/{id}`
- Auto-refresh contract list setelah berhasil

#### Renew Button
```javascript
function renewContract(contractId) {
    // Show informasi renewal workflow
    // TODO: Implement full renewal wizard
}
```
- **Conditional visibility**:
  - ✅ Kontrak ACTIVE
  - ✅ Kontrak expiring soon (≤30 hari)
  - ✅ Kontrak expired
- Currently: Menampilkan informasi workflow
- **Next implementation**: Multi-step renewal wizard

**Button Behavior**:
- `event.stopPropagation()` → Prevent row click
- Icon-only design dengan tooltip
- Color coding: Blue (edit), Red (delete), Green (renew)

---

### 2. Full Contract Management Page
**File**: [app/Views/marketing/kontrak.php](app/Views/marketing/kontrak.php)

**Features**:
- ✅ Statistics cards (4 KPIs)
- ✅ Advanced filters (Type, Status, Customer)
- ✅ DataTable dengan rental type badges
- ✅ CSV export functionality
- ✅ Full CRUD actions

**Status**: Paused development (per user request)
- User quote: "simpan dulu saja halaman kontrak ini"
- Focus: Customer modal enhancement first

---

### 3. Backend Enhancements
**Files Modified**:

#### [app/Controllers/CustomerManagementController.php](app/Controllers/CustomerManagementController.php)
- ✅ `getCustomerContracts($customerId)` - dengan stats & limit
- ✅ `getCustomerActivity($customerId)` - timeline dengan filters

#### [app/Controllers/Kontrak.php](app/Controllers/Kontrak.php)
- ✅ `getDataTable()` - rental type badge column
- ✅ `getStats()` - contract statistics
- ✅ `export()` - CSV export
- ✅ `getCustomersDropdown()` - filter customers
- ✅ `edit($id)` - **EXISTS** ✅
- ✅ `delete($id)` - **EXISTS** ✅

#### [app/Models/KontrakModel.php](app/Models/KontrakModel.php)
- ✅ Fixed duplicate JOIN bug (line 147)
- ✅ Optimized DataTables query

---

## 📋 Renewal Workflow Plan

**Document**: [docs/CONTRACT_PO_RENEWAL_WORKFLOW.md](docs/CONTRACT_PO_RENEWAL_WORKFLOW.md)

### Workflow Stages

```
1. INITIATION (Marketing)
   ↓ User clicks "Renew" button
   ↓ Choose: Quick Renewal vs Custom Renewal
   
2. CONFIGURATION
   ↓ Set: Dates, Duration, Type
   
3. UNIT SELECTION
   ↓ Keep same units OR choose new units
   ↓ Auto-check availability
   ↓ Suggest replacements if unavailable
   
4. PRICING ADJUSTMENT
   ↓ Review old vs new pricing
   ↓ Apply adjustments
   
5. APPROVAL WORKFLOW
   ↓ Auto-approve (low-risk) OR Manual approval (high-risk)
   
6. CUSTOMER CONFIRMATION
   ↓ Send draft contract
   ↓ Upload signed contract
   
7. ACTIVATION
   ↓ Gap-free transition
   ↓ Unit transfer
   ↓ Notifications sent
```

### Key Features

#### Automatic Triggers
- **90 days before expiry**: First warning
- **60 days before expiry**: Second warning
- **30 days before expiry**: Critical warning + "Expiring Soon" badge
- **7 days before expiry**: Urgent + daily reminders
- **After expiry**: Auto-status "EXPIRED"

#### Auto-Approval Criteria (Low-Risk)
✅ Same units (100%)
✅ Same pricing (±5%)
✅ Same duration
✅ Contract value < Rp 100jt
✅ Customer good standing

→ **Auto-approve** → Skip approval stage

#### Manual Approval Required (High-Risk)
⚠️ Unit changed >20%
⚠️ Pricing changed >10%
⚠️ Contract value > Rp 100jt
⚠️ Customer ada overdue payment

→ **Require approval** dari:
1. Marketing Manager
2. Operations Manager (if units changed)
3. Finance Manager (if price changed >20%)

#### Gap-Free Renewal (Recommended)
```
Old Contract: 2025-01-01 → 2025-12-31 ⚫ ACTIVE
New Contract: 2026-01-01 → 2026-12-31 🟡 APPROVED

On 2026-01-01 00:00:00 (automated):
✅ Old Contract → COMPLETED
✅ New Contract → ACTIVE
✅ Units auto-transfer (no DO needed)
```

---

## 🗄️ Database Schema Changes

### 1. Add Renewal Fields to `kontrak` Table
```sql
ALTER TABLE kontrak 
ADD COLUMN parent_contract_id INT NULL COMMENT 'ID kontrak lama jika renewal',
ADD COLUMN is_renewal BOOLEAN DEFAULT FALSE,
ADD COLUMN renewal_initiated_at DATETIME NULL,
ADD COLUMN renewal_initiated_by INT NULL,
ADD COLUMN renewal_approved_at DATETIME NULL,
ADD COLUMN renewal_approved_by INT NULL,
ADD COLUMN renewal_notes TEXT NULL,
ADD FOREIGN KEY (parent_contract_id) REFERENCES kontrak(id),
ADD FOREIGN KEY (renewal_initiated_by) REFERENCES users(id),
ADD FOREIGN KEY (renewal_approved_by) REFERENCES users(id);
```

### 2. Create Renewal Workflow Table
```sql
CREATE TABLE contract_renewal_workflow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    old_contract_id INT NOT NULL,
    new_contract_id INT NULL,
    status ENUM(
        'INITIATED',
        'UNIT_CHECK',
        'PENDING_APPROVAL',
        'APPROVED',
        'CUSTOMER_REVIEW',
        'ACTIVATED',
        'REJECTED',
        'CANCELLED'
    ) DEFAULT 'INITIATED',
    renewal_type ENUM('AUTO', 'MANUAL') DEFAULT 'MANUAL',
    initiated_by INT NOT NULL,
    initiated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    approved_by INT NULL,
    approved_at DATETIME NULL,
    activated_by INT NULL,
    activated_at DATETIME NULL,
    rejection_reason TEXT NULL,
    workflow_notes JSON NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (old_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (new_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (initiated_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_old_contract (old_contract_id)
) ENGINE=InnoDB;
```

### 3. Create Unit Mapping Table
```sql
CREATE TABLE contract_renewal_unit_map (
    id INT AUTO_INCREMENT PRIMARY KEY,
    renewal_workflow_id INT NOT NULL,
    old_unit_id INT NULL COMMENT 'Unit dari kontrak lama',
    new_unit_id INT NOT NULL COMMENT 'Unit untuk kontrak baru',
    is_replacement BOOLEAN DEFAULT FALSE,
    replacement_reason VARCHAR(255) NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (renewal_workflow_id) REFERENCES contract_renewal_workflow(id) ON DELETE CASCADE,
    FOREIGN KEY (old_unit_id) REFERENCES inventory_unit(id) ON DELETE SET NULL,
    FOREIGN KEY (new_unit_id) REFERENCES inventory_unit(id) ON DELETE CASCADE
) ENGINE=InnoDB;
```

### 4. Add Status Values to `kontrak` Table
```sql
ALTER TABLE kontrak 
MODIFY COLUMN status ENUM(
    'DRAFT',
    'APPROVED',
    'ACTIVE',
    'COMPLETED',
    'TERMINATED',
    'EXPIRED',
    'DRAFT_RENEWAL',      -- NEW
    'PENDING_RENEWAL',    -- NEW
    'RENEWAL_REJECTED'    -- NEW
) DEFAULT 'DRAFT';
```

---

## 🎨 UI Components

### Renewal Wizard Modal (5 Steps)

#### Step 1: Renewal Confirmation
```
┌─────────────────────────────────────┐
│ Quick Renewal (same terms)          │
│ → Auto-fill all fields              │
│                                      │
│ Custom Renewal (modify terms)       │
│ → Manual configuration               │
└─────────────────────────────────────┘
```

#### Step 2: Contract Configuration
- Start Date (default: old_end + 1)
- Duration (months)
- Rental Type (CONTRACT/PO_ONLY/DAILY_SPOT)

#### Step 3: Unit Selection
- [✓] Keep same units OR
- [ ] Choose new units
- Real-time availability check
- Auto-suggest replacements

#### Step 4: Pricing Adjustment
- Old Price vs New Price comparison
- Per-unit price editing
- Total value calculation
- Price change justification field

#### Step 5: Review & Submit
- Summary of renewal details
- Approval requirements (if any)
- Final confirmation checkbox
- Submit button

---

## 📊 KPIs for Renewal System

### Business Metrics
- **Renewal Rate**: (Renewed / Expiring) × 100%
  - Target: >80%
  
- **Average Renewal Time**: Days from initiation to activation
  - Target: <14 days
  
- **Revenue Retention**: (New value / Old value) × 100%
  - Target: >95%

### Operational Metrics
- **Auto-Approval Rate**: (Auto-approved / Total) × 100%
  - Target: >60%
  
- **Unit Availability**: (Same units retained / Total) × 100%
  - Target: >90%

### Customer Satisfaction
- **On-Time Activation**: Renewals activated before expiry
  - Target: 100%

---

## 📅 Implementation Timeline

### Phase 1: Foundation (Week 1-2)
- [ ] Database migrations (schema changes)
- [ ] Renewal models & migrations
- [ ] Basic controller structure

### Phase 2: Core Workflow (Week 3-4)
- [ ] Renewal wizard UI (5-step modal)
- [ ] Unit availability checker
- [ ] Approval workflow engine
- [ ] Email notifications

### Phase 3: Automation (Week 5-6)
- [ ] Cron job: Daily expiry checker
- [ ] Auto-notification system
- [ ] Auto-approval logic
- [ ] PDF contract generation

### Phase 4: Advanced Features (Week 7-8)
- [ ] Renewal analytics dashboard
- [ ] Bulk renewal (multi-contract)
- [ ] Customer self-service portal
- [ ] Finance integration

### Phase 5: Testing & Launch (Week 9-10)
- [ ] Unit testing
- [ ] UAT (User Acceptance Testing)
- [ ] Training & documentation
- [ ] Production deployment

**Estimated Total**: 10 weeks (2.5 months)

---

## 🚀 Next Steps

### Immediate (Now)
1. ✅ Review renewal workflow plan
2. ✅ Approve database schema changes
3. ✅ Test Edit/Delete buttons in customer modal

### Short Term (Week 1-2)
1. [ ] Run database migrations
2. [ ] Create RenewalController skeleton
3. [ ] Design renewal wizard UI mockups
4. [ ] Implement Step 1: Renewal Confirmation

### Medium Term (Week 3-6)
1. [ ] Complete all 5 wizard steps
2. [ ] Implement approval workflow
3. [ ] Setup email notifications
4. [ ] Unit availability checker logic

### Long Term (Week 7-10)
1. [ ] Automation: Cron jobs
2. [ ] Analytics dashboard
3. [ ] Testing & UAT
4. [ ] Production launch

---

## 📝 Testing Checklist

### Manual Testing
- [ ] Click Edit button → redirects to edit page
- [ ] Click Delete button → shows confirmation → deletes contract
- [ ] Click Renew button → shows workflow info modal
- [ ] Expiring soon badge shows for contracts ≤30 days
- [ ] Expired badge shows for past-due contracts
- [ ] Renew button hidden for non-active contracts (>90 days left)

### Automated Testing (Future)
- [ ] Unit test: Contract renewal workflow states
- [ ] Unit test: Unit availability checker
- [ ] Integration test: Gap-free renewal activation
- [ ] E2E test: Full renewal wizard flow

---

## 📚 Documentation References

- **Full Workflow Guide**: [CONTRACT_PO_RENEWAL_WORKFLOW.md](../docs/CONTRACT_PO_RENEWAL_WORKFLOW.md)
- **Database Schema**: [DATABASE_SCHEMA.md](../docs/DATABASE_SCHEMA.md)
- **Activity Log**: [ACTIVITY_LOG_ENHANCEMENTS.md](../docs/ACTIVITY_LOG_ENHANCEMENTS.md)

---

**Last Updated**: 2025-01-28
**Status**: ✅ Action Buttons Implemented | 📋 Renewal Workflow Planned
**Next**: Approve workflow plan → Start Phase 1 implementation
