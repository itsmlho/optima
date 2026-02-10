# Contract & PO Renewal Workflow - Architectural Plan

## 1. Overview

Sistem perpanjangan kontrak dan PO yang terstruktur untuk memastikan tidak ada gap antara kontrak lama dan kontrak baru, dengan approval workflow dan notifikasi otomatis.

---

## 2. Renewal Triggers

### 2.1 Automatic Detection
- **90 hari sebelum berakhir**: First warning notification ke Marketing & Customer
- **60 hari sebelum berakhir**: Second warning ke Marketing & Management
- **30 hari sebelum berakhir**: Critical warning + badge "Expiring Soon"
- **7 hari sebelum berakhir**: Urgent notification + daily reminder
- **Setelah berakhir**: Status otomatis berubah "EXPIRED" + red badge

### 2.2 Manual Renewal
- Marketing dapat memulai renewal kapan saja melalui tombol "Renew" 
- Tersedia di:
  - Customer Detail Modal → Contracts & PO tab
  - Full Contract Management Page (/marketing/kontrak)

---

## 3. Renewal Workflow Stages

### Stage 1: Initiation (Pengajuan Perpanjangan)
**Dilakukan oleh**: Marketing

**Form Input**:
- [ ] Confirmation: Apakah perpanjang dengan term yang sama?
  - **Ya** → Auto-fill semua field dari kontrak lama
  - **Tidak** → Form kosong, hanya copy customer & location
  
- [ ] **New Contract Details**:
  - Tanggal Mulai Baru (default: tanggal_berakhir_lama + 1 hari)
  - Durasi (bulan) atau Tanggal Berakhir Baru
  - Jenis Sewa (BULANAN/HARIAN/SPOT)
  - Rental Type (CONTRACT/PO_ONLY/DAILY_SPOT)
  
- [ ] **Unit Selection**:
  - Tampilkan unit dari kontrak lama
  - Checkbox: "Keep same units?" 
    - **Ya** → Auto-select semua unit lama (check availability dulu)
    - **Tidak** → Manual pilih unit baru
  - Real-time unit availability check
  - Highlight unit yang tidak available (merah)
  - Suggest replacement units dengan spec sama
  
- [ ] **Pricing Adjustment**:
  - Show harga lama vs harga pasar saat ini
  - Input: Harga sewa per unit (editable)
  - Auto-calculate: Total nilai kontrak baru
  - Alasan penyesuaian harga (opsional)

**Business Rules**:
- Tidak boleh ada gap antara kontrak lama & baru
- Tidak boleh overlap tanggal dengan kontrak lain di customer yang sama
- Unit yang dipilih harus available di periode yang ditentukan

**Output**:
- Draft renewal di database dengan status: `DRAFT_RENEWAL`
- Link renewal ke kontrak lama: `parent_contract_id`

---

### Stage 2: Unit Availability Check (Automated)
**Dilakukan oleh**: System

**Process**:
1. Query unit availability:
   ```sql
   SELECT iu.id, iu.no_seri, iu.status, iu.lokasi_unit
   FROM inventory_unit iu
   WHERE iu.id IN (selected_units)
   AND NOT EXISTS (
       SELECT 1 FROM kontrak k
       JOIN kontrak_detail kd ON k.id = kd.kontrak_id
       WHERE kd.inventory_unit_id = iu.id
       AND k.status IN ('ACTIVE', 'APPROVED')
       AND (
           (k.tanggal_mulai BETWEEN '?' AND '?')
           OR (k.tanggal_berakhir BETWEEN '?' AND '?')
           OR ('?' BETWEEN k.tanggal_mulai AND k.tanggal_berakhir)
       )
   )
   ```

2. Jika ada unit tidak available:
   - **Auto-suggest replacement** dengan kriteria sama:
     - Brand sama
     - Model sama
     - Kapasitas sama
     - Status: AVAILABLE
   - Notify Marketing untuk konfirmasi replacement

3. Jika semua unit available → proceed ke Stage 3

**Business Rules**:
- Jika > 50% unit tidak available → flag for review
- Jika 100% unit tidak available → reject renewal, suggest new contract

---

### Stage 3: Approval Workflow
**Dilakukan oleh**: Management

**Approval Path**:

#### Automatic Approval (Low-Risk Renewal)
Kondisi:
- Same units (100%)
- Same pricing (±5%)
- Same duration
- Customer good standing (no overdue payment)
- Contract value < Rp 100 juta

→ **Auto-approve** → Skip ke Stage 4

#### Manual Approval (High-Risk Renewal)
Kondisi:
- Unit changed > 20%
- Pricing changed > 10%
- Contract value > Rp 100 juta
- Customer ada overdue payment
- Duration > 12 bulan

→ **Require approval** dari:
1. Marketing Manager
2. Operations Manager (jika unit changed)
3. Finance Manager (jika pricing changed > 20%)

**Approval Actions**:
- [ ] **Approve**: Lanjut ke Stage 4
- [ ] **Revision Required**: Kembali ke Stage 1 dengan notes
- [ ] **Reject**: Cancel renewal, notify Marketing

**Notification**:
- Email ke approvers dengan link direct ke approval page
- Dashboard notification badge
- Reminder setiap 48 jam jika belum di-approve

---

### Stage 4: Document Generation
**Dilakukan oleh**: System (Automated)

**Generated Documents**:

#### A. Contract Renewal
1. **Draft Kontrak Baru (PDF)**:
   - Template: Same as original contract
   - Auto-fill: Customer, lokasi, unit list, harga
   - Watermark: "DRAFT RENEWAL - PENDING ACTIVATION"
   - Timestamp: Generated date

2. **Unit Assignment List (PDF)**:
   - List unit yang akan di-assign ke kontrak baru
   - Serial numbers, brand, model, current location
   - Target delivery date

3. **Pricing Comparison Report**:
   - Side-by-side: Old vs New pricing
   - Justification untuk price changes
   - Total revenue impact

#### B. PO Only Renewal
1. **Purchase Order Template**:
   - New PO number (auto-increment)
   - Customer details
   - Unit details + pricing
   - Terms & conditions

2. **Delivery Instruction (DO)**:
   - Unit delivery schedule
   - Lokasi pengiriman
   - PIC contact

**Storage**:
- Save di: `/uploads/contracts/renewals/{customer_id}/{contract_id}/`
- Version control: V1, V2, V3... untuk revisi

---

### Stage 5: Customer Confirmation
**Dilakukan oleh**: Marketing → Customer

**Process**:
1. Marketing download draft contract
2. Send ke customer untuk review
3. Customer feedback:
   - **Setuju** → Upload signed contract → proceed
   - **Revisi** → Back to Stage 1 with customer notes
   - **Tolak** → Mark as `RENEWAL_REJECTED`

**Upload Requirements**:
- File format: PDF only
- Max size: 5MB
- Signed contract upload → trigger Stage 6

---

### Stage 6: Activation & Unit Transfer
**Dilakukan oleh**: Operations → System

**Pre-Activation Checklist**:
- [ ] Signed contract uploaded
- [ ] Payment confirmation (DP or full payment)
- [ ] All approvals received
- [ ] Units available and ready
- [ ] Tanggal mulai = tomorrow or future date

**Activation Process**:

#### Option A: Gap-Free Renewal (Recommended)
```
Old Contract: 2025-01-01 → 2025-12-31 (status: ACTIVE)
New Contract: 2026-01-01 → 2026-12-31 (status: APPROVED)

On 2026-01-01 00:00:00 (automated):
1. Old Contract → status: COMPLETED
2. New Contract → status: ACTIVE
3. Units auto-transfer → no DO needed
4. Notification: "Contract renewed successfully"
```

#### Option B: Manual Activation (for immediate renewal)
```
Old Contract: 2025-01-01 → 2025-12-31 (status: ACTIVE)
User request: Activate renewal NOW (2025-06-15)

System action:
1. Terminate old contract → status: TERMINATED_EARLY
2. Activate new contract → status: ACTIVE
3. Units transfer → DO generated
4. Note: "Early renewal activated on 2025-06-15"
```

**Unit Transfer Logic**:
- **Same units**: No physical movement → just update kontrak_id
- **Different units**: 
  - Generate DO untuk unit return (old contract)
  - Generate DO untuk unit delivery (new contract)
  - Update inventory unit status

**Database Updates**:
```sql
START TRANSACTION;

-- 1. Complete old contract
UPDATE kontrak 
SET status = 'COMPLETED', 
    tanggal_berakhir_aktual = CURDATE(),
    completed_at = NOW()
WHERE id = {old_contract_id};

-- 2. Activate new contract
UPDATE kontrak 
SET status = 'ACTIVE',
    activated_at = NOW(),
    activated_by = {user_id}
WHERE id = {new_contract_id};

-- 3. Transfer units (if same units)
UPDATE kontrak_detail 
SET kontrak_id = {new_contract_id}
WHERE kontrak_id = {old_contract_id}
AND inventory_unit_id IN (selected_units);

-- 4. Log activity
INSERT INTO activity_log (entity_type, entity_id, action, description, user_id)
VALUES ('contract', {new_contract_id}, 'renewed', 
        'Contract renewed from #{old_contract_no}', {user_id});

COMMIT;
```

---

### Stage 7: Post-Renewal Actions
**Dilakukan oleh**: System (Automated)

**Notifications**:
- [x] Marketing: "Contract renewed and activated"
- [x] Customer: "Welcome back! Your contract is active"
- [x] Operations: "Unit transfer completed" (jika ada unit change)
- [x] Finance: "New invoice generated"

**Reporting**:
- Add ke dashboard:
  - Renewal success rate (%)
  - Average renewal time (days from initiation to activation)
  - Revenue impact (old vs new contract value)

**Invoice Generation** (if needed):
- Auto-generate invoice untuk periode pertama
- Send ke Finance untuk processing
- Link invoice ke kontrak baru

---

## 4. Database Schema Changes

### 4.1 Add Renewal Tracking Fields to `kontrak` Table
```sql
ALTER TABLE kontrak 
ADD COLUMN parent_contract_id INT NULL COMMENT 'ID kontrak lama jika ini renewal',
ADD COLUMN is_renewal BOOLEAN DEFAULT FALSE,
ADD COLUMN renewal_initiated_at DATETIME NULL,
ADD COLUMN renewal_initiated_by INT NULL,
ADD COLUMN renewal_approved_at DATETIME NULL,
ADD COLUMN renewal_approved_by INT NULL,
ADD COLUMN renewal_notes TEXT NULL,
ADD FOREIGN KEY (parent_contract_id) REFERENCES kontrak(id) ON DELETE SET NULL,
ADD FOREIGN KEY (renewal_initiated_by) REFERENCES users(id) ON DELETE SET NULL,
ADD FOREIGN KEY (renewal_approved_by) REFERENCES users(id) ON DELETE SET NULL;
```

### 4.2 Create Renewal Workflow Table
```sql
CREATE TABLE contract_renewal_workflow (
    id INT AUTO_INCREMENT PRIMARY KEY,
    old_contract_id INT NOT NULL,
    new_contract_id INT NULL,
    status ENUM(
        'INITIATED',           -- Marketing mulai renewal
        'UNIT_CHECK',          -- System check unit availability
        'PENDING_APPROVAL',    -- Waiting for approval
        'APPROVED',            -- Approved, waiting activation
        'CUSTOMER_REVIEW',     -- Sent to customer
        'ACTIVATED',           -- Renewal activated
        'REJECTED',            -- Renewal ditolak
        'CANCELLED'            -- Renewal dibatalkan
    ) DEFAULT 'INITIATED',
    renewal_type ENUM('AUTO', 'MANUAL') DEFAULT 'MANUAL',
    initiated_by INT NOT NULL,
    initiated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    approved_by INT NULL,
    approved_at DATETIME NULL,
    activated_by INT NULL,
    activated_at DATETIME NULL,
    rejection_reason TEXT NULL,
    workflow_notes JSON NULL COMMENT 'Store approval chain, revision history',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (old_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (new_contract_id) REFERENCES kontrak(id) ON DELETE CASCADE,
    FOREIGN KEY (initiated_by) REFERENCES users(id),
    INDEX idx_status (status),
    INDEX idx_old_contract (old_contract_id)
) ENGINE=InnoDB;
```

### 4.3 Create Renewal Unit Mapping Table
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

### 4.4 Add Status to `kontrak` Table
```sql
ALTER TABLE kontrak 
MODIFY COLUMN status ENUM(
    'DRAFT',
    'APPROVED',
    'ACTIVE',
    'COMPLETED',
    'TERMINATED',
    'EXPIRED',
    'DRAFT_RENEWAL',      -- NEW: Renewal in draft state
    'PENDING_RENEWAL',    -- NEW: Renewal waiting approval
    'RENEWAL_REJECTED'    -- NEW: Renewal rejected
) DEFAULT 'DRAFT';
```

---

## 5. UI/UX Components

### 5.1 Renewal Button Behavior
**Location**: Customer Detail Modal → Contracts & PO tab

**Button State Logic**:
```javascript
function shouldShowRenewButton(contract) {
    const today = new Date();
    const endDate = new Date(contract.tanggal_berakhir);
    const daysLeft = Math.ceil((endDate - today) / (1000 * 60 * 60 * 24));
    
    // Show renew button if:
    return (
        contract.status === 'ACTIVE' ||        // Active contract
        (daysLeft > 0 && daysLeft <= 90) ||    // Expiring within 90 days
        daysLeft <= 0                           // Expired
    );
}
```

**Button Text**:
- Active contract (>90 days): "Renew Contract"
- Expiring soon (30-90 days): "⚠️ Renew Now"
- Expiring critical (<30 days): "🚨 Urgent: Renew Now"
- Expired: "♻️ Reactivate"

---

### 5.2 Renewal Wizard Modal (Multi-Step)

#### Step 1: Renewal Confirmation
```
┌─────────────────────────────────────────────────┐
│ Renew Contract: #CTR-2025-001                   │
├─────────────────────────────────────────────────┤
│ Customer: PT Maju Jaya                          │
│ Current Period: 2025-01-01 to 2025-12-31       │
│ Status: Active (expires in 45 days)             │
│                                                  │
│ ○ Quick Renewal (same terms)                    │
│   → Auto-fill all fields from current contract  │
│                                                  │
│ ○ Custom Renewal (modify terms)                 │
│   → Manually configure new contract             │
│                                                  │
│           [Cancel]   [Next: Configure →]        │
└─────────────────────────────────────────────────┘
```

#### Step 2: Contract Configuration
```
┌─────────────────────────────────────────────────┐
│ Configure New Contract                          │
├─────────────────────────────────────────────────┤
│ Start Date:  [2026-01-01]     ← auto: old_end+1│
│ Duration:    [12] months  or  End: [2026-12-31]│
│ Type:        [CONTRACT ▼]                       │
│ Rental Type: [BULANAN ▼]                        │
│                                                  │
│         [← Back]   [Next: Units →]              │
└─────────────────────────────────────────────────┘
```

#### Step 3: Unit Selection
```
┌─────────────────────────────────────────────────┐
│ Select Units for Renewal                        │
├─────────────────────────────────────────────────┤
│ [✓] Keep same units (5 units)                   │
│                                                  │
│ Current Units:                    Availability  │
│ [✓] FRK-001 | Forklift 3 Ton     Available ✅   │
│ [✓] FRK-002 | Forklift 3 Ton     Available ✅   │
│ [✓] FRK-003 | Forklift 3 Ton     Booked ❌      │
│     → Suggest: FRK-010 (same spec) Available ✅ │
│ [✓] GEN-001 | Generator 100 KVA  Available ✅   │
│ [✓] GEN-002 | Generator 100 KVA  Available ✅   │
│                                                  │
│ [+ Add More Units]                               │
│                                                  │
│         [← Back]   [Next: Pricing →]            │
└─────────────────────────────────────────────────┘
```

#### Step 4: Pricing Adjustment
```
┌─────────────────────────────────────────────────┐
│ Pricing Configuration                           │
├─────────────────────────────────────────────────┤
│ Unit              Old Price    New Price        │
│ FRK-001 (3 Ton)   8,000,000   [8,500,000]      │
│ FRK-002 (3 Ton)   8,000,000   [8,500,000]      │
│ FRK-010 (3 Ton)   -           [8,500,000]      │
│ GEN-001 (100 KVA) 12,000,000  [12,500,000]     │
│ GEN-002 (100 KVA) 12,000,000  [12,500,000]     │
│                                                  │
│ Old Total:  Rp 48,000,000/month                 │
│ New Total:  Rp 50,500,000/month (+5.2%)        │
│                                                  │
│ Price Adjustment Reason (optional):             │
│ [Inflasi 5% + biaya maintenance naik]          │
│                                                  │
│         [← Back]   [Next: Review →]             │
└─────────────────────────────────────────────────┘
```

#### Step 5: Review & Submit
```
┌─────────────────────────────────────────────────┐
│ Review Renewal Details                          │
├─────────────────────────────────────────────────┤
│ Customer:      PT Maju Jaya                     │
│ Contract Type: CONTRACT - BULANAN               │
│ Period:        01 Jan 2026 - 31 Dec 2026        │
│ Total Units:   5 units (1 replacement)          │
│ Monthly Value: Rp 50,500,000                    │
│ Total Value:   Rp 606,000,000 (12 months)       │
│                                                  │
│ ⚠️ This renewal requires approval from:         │
│   • Marketing Manager (price change > 5%)       │
│                                                  │
│ [ ] I confirm all information is correct        │
│                                                  │
│         [← Back]   [Submit Renewal]             │
└─────────────────────────────────────────────────┘
```

---

### 5.3 Approval Dashboard
**Location**: Marketing → Approvals menu

```
┌──────────────────────────────────────────────────┐
│ Pending Renewals Approval (3)                    │
├──────────────────────────────────────────────────┤
│ #RNW-001 | PT Maju Jaya | Rp 606M | 2 days ago  │
│ Reason: Price increase 5.2%                      │
│ [View Details] [Approve] [Request Revision]      │
├──────────────────────────────────────────────────┤
│ #RNW-002 | CV Sejahtera | Rp 120M | 5 days ago  │
│ Reason: Unit replacement 40%                     │
│ [View Details] [Approve] [Reject]                │
└──────────────────────────────────────────────────┘
```

---

## 6. Notification Templates

### Email Template: Approval Request
```
Subject: [Optima] Renewal Approval Required - #RNW-001

Dear {approver_name},

A contract renewal requires your approval:

Customer:     PT Maju Jaya
Old Contract: #CTR-2025-001 (expires 31 Dec 2025)
New Period:   01 Jan 2026 - 31 Dec 2026
Contract Value: Rp 606,000,000 (+5.2% from old contract)

Reason for Approval:
- Price increase: 5.2% (above 5% threshold)

Action Required:
[Approve]  [Request Revision]  [Reject]

Or review details: {approval_link}

---
Optima Rental Management System
```

### Email Template: Customer Renewal Notification
```
Subject: Your Contract is Expiring Soon - Action Required

Dear {customer_name},

Your contract #{contract_no} will expire in {days_left} days.

Current Contract Details:
- Period: {start_date} to {end_date}
- Units: {total_units} units
- Monthly Payment: Rp {monthly_value}

Would you like to renew? Our team will contact you shortly.

For questions, contact: {marketing_pic} - {phone}

---
Optima Rental Services
```

---

## 7. Implementation Timeline

### Phase 1: Foundation (Week 1-2)
- [ ] Database schema updates (migrations)
- [ ] Contract renewal model creation
- [ ] Basic renewal controller with workflow states

### Phase 2: Core Workflow (Week 3-4)
- [ ] Renewal wizard UI (5-step modal)
- [ ] Unit availability check logic
- [ ] Approval workflow engine
- [ ] Email notification system

### Phase 3: Automation (Week 5-6)
- [ ] Cron job: Daily expiry checker
- [ ] Auto-notification system (90/60/30/7 days)
- [ ] Automatic approval for low-risk renewals
- [ ] Document generation (PDF contracts)

### Phase 4: Advanced Features (Week 7-8)
- [ ] Renewal analytics dashboard
- [ ] Bulk renewal (multi-contract)
- [ ] Customer self-service renewal portal
- [ ] Integration with finance (invoice auto-generation)

### Phase 5: Testing & Launch (Week 9-10)
- [ ] Unit testing all workflow stages
- [ ] User acceptance testing (UAT)
- [ ] Training materials & documentation
- [ ] Production deployment

---

## 8. Key Performance Indicators (KPIs)

### Business Metrics
- **Renewal Rate**: (Renewed contracts / Expiring contracts) × 100%
  - Target: >80%
  
- **Average Renewal Time**: Days from initiation to activation
  - Target: <14 days
  
- **Revenue Retention**: (New contract value / Old contract value) × 100%
  - Target: >95%

### Operational Metrics
- **Auto-Approval Rate**: (Auto-approved / Total renewals) × 100%
  - Target: >60%
  
- **Unit Availability Rate**: (Same units retained / Total requested) × 100%
  - Target: >90%

### Customer Satisfaction
- **On-Time Activation**: Renewals activated before old contract expires
  - Target: 100%

---

## 9. Risk Mitigation

### Risk 1: Unit Tidak Available
**Mitigation**:
- Early notification (90 days before expiry)
- Auto-suggest replacement units
- Reserve popular units for high-value customers

### Risk 2: Approval Bottleneck
**Mitigation**:
- Auto-approval for low-risk renewals
- Escalation after 48 hours no response
- Delegate approval authority

### Risk 3: Customer Tidak Respond
**Mitigation**:
- Multiple notification channels (email, WhatsApp, phone)
- Follow-up protocol (7 days, 14 days, 21 days)
- Grace period after expiry (30 days before mark as LOST)

### Risk 4: Gap Between Contracts
**Mitigation**:
- Default new start date = old end date + 1
- Warning if gap detected
- Option to backdate activation (with approval)

---

## 10. Success Criteria

✅ Renewal can be initiated in <2 minutes
✅ 80% auto-approved (low-risk renewals)
✅ Zero contract gaps (100% seamless transition)
✅ Customer receive notification 90 days before expiry
✅ All renewals tracked in activity log
✅ Reports available for management review

---

**Document Version**: 1.0
**Created**: 2025-01-28
**Author**: GitHub Copilot
**Status**: PROPOSED - Awaiting Approval
