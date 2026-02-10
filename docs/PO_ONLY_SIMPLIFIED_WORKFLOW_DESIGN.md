# PO_ONLY Simplified Workflow - Design Document
**Created:** February 9, 2026  
**Status:** Design Phase  
**Target:** Phase 2 Enhancement

---

## 📋 Business Requirements

### **Current Problem:**
PO_ONLY rental type (government/corporate orders with PO but no formal contract) currently goes through the same heavy workflow as formal contracts:
- Quotation → Customer Creation → Contract → SPK → DI

This is unnecessary overhead for simple PO-based orders.

### **Proposed Solution:**
Create a **simplified fast-track workflow** for PO_ONLY rental types that skips quotation stage and streamlines approval.

---

## 🎯 Simplified PO_ONLY Workflow

### **New Workflow:**
```
Quick PO Entry → Minimal Contract Record → SPK (Auto-Approved) → DI
```

### **Comparison:**

| Step | Current (CONTRACT) | Proposed (PO_ONLY) |
|------|-------------------|-------------------|
| **1. Quotation** | Required | **SKIPPED** |
| **2. Customer Record** | Full details | Minimal (name, PO only) |
| **3. Contract** | Full formal contract | Minimal record for tracking |
| **4. SPK Approval** | Manual approval required | **AUTO-APPROVED** |
| **5. DI** | Standard process | Standard process |

---

## 🚀 implementation Plan

### **Phase 1: Quick PO Entry Form**

**Location:** `/marketing/po-only/quick-entry`

**Form Fields (Minimal):**
```
┌─────────────────────────────────────────┐
│  Quick PO-Only Rental Entry             │
├─────────────────────────────────────────┤
│ Customer PO Number: [________] *required │
│ Customer Name: [________________]        │
│ Contact Person: [________________]       │
│ Location/Site: [________________]        │
│                                          │
│ Start Date: [__/__/__] *required         │
│ End Date: [__/__/__] *required          │
│ Billing Period: [BULANAN ▼] HARIAN      │
│                                          │
│ Unit Selection:                          │
│ ┌──────────────────────────────────┐   │
│ │ [Checkbox] Forklift 3 TON (5 units)│   │
│ │ [Checkbox] Hand Pallet (10 units)  │   │
│ │ [Checkbox] Reach Truck (2 units)   │   │
│ └──────────────────────────────────┘   │
│                                          │
│ [Cancel] [Submit & Create SPK →]        │
└─────────────────────────────────────────┘
```

**Backend Logic:**
1. Create minimal customer record (if new)
2. Create kontrak record with `rental_type='PO_ONLY'`
3. **Auto-create SPK** with status `APPROVED` (skip manual approval)
4. Redirect to DI creation

---

### **Phase 2: Database Changes**

**New Table:** `po_only_quick_entries` (Optional - for audit trail)
```sql
CREATE TABLE IF NOT EXISTS `po_only_quick_entries` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `customer_po_number` VARCHAR(100) NOT NULL,
  `customer_name` VARCHAR(255),
  `contact_person` VARCHAR(255),
  `location_site` VARCHAR(255),
  `start_date` DATE NOT NULL,
  `end_date` DATE NOT NULL,
  `billing_period` ENUM('BULANAN', 'HARIAN') DEFAULT 'BULANAN',
  `total_units` INT UNSIGNED DEFAULT 0,
  `auto_generated_contract_id` INT UNSIGNED,
  `auto_generated_spk_id` INT UNSIGNED,
  `created_by` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_customer_po` (`customer_po_number`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`auto_generated_contract_id`) REFERENCES `kontrak`(`id`) ON DELETE SET NULL,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Alternative:** Just use existing `kontrak` table with flag/template indicating PO_ONLY quick entry.

---

### **Phase 3: Controller Implementation**

**File:** `app/Controllers/PoOnlyQuickEntry.php`

```php
class PoOnlyQuickEntry extends BaseController
{
    public function index()
    {
        // Show quick entry form
        return view('marketing/po_only_quick_entry');
    }
    
    public function submitQuickEntry()
    {
        // 1. Validate input
        // 2. Create/find customer (simple)
        // 3. Create contract (rental_type='PO_ONLY')
        // 4. Auto-create SPK with APPROVED status
        // 5. Return success + redirect to DI form
    }
    
    public function getBatchHistory()
    {
        // List all PO_ONLY quick entries
    }
}
```

---

### **Phase 4: Frontend Integration**

**Navigation:** Add to Marketing sidebar
```html
<li><a href="/marketing/po-only/quick-entry">
    <i class="fas fa-bolt"></i> Quick PO Entry
</a></li>
```

**Benefits:**
- ⚡ **80% faster** entry for PO-only orders
- 🎯 **Reduced errors** (fewer fields = less mistakes)
- 📈 **Better UX** for high-volume government/corporate orders

---

## 🔄 Workflow Integration

###  **Contract Status Flow (PO_ONLY):**
```
┌──────────────┐
│   PENDING    │ ← Created from quick entry
└──────┬───────┘
       │ Auto-generated SPK (APPROVED)
       ↓
┌──────────────┐
│    ACTIVE    │ ← When DI completed
└──────┬───────┘
       │ End date passes
       ↓
┌──────────────┐
│   EXPIRED    │
└──────────────┘
```

### **Validation Rules:**

| Rule | CONTRACT | PO_ONLY |
|------|----------|---------|
| Customer PO Number | Optional | **REQUIRED** |
| Formal Contract Document | Required | Optional |
| Quotation History | Required | Not needed |
| Manual SPK Approval | Required | **AUTO-APPROVED** |
| Contract Number Format | KTR/YYYY/#### | **PO/YYYY/####** |

---

## 📊 Reporting & Analytics

### **PO_ONLY Dashboard Widgets:**
```javascript
{
    "total_po_only_contracts": 45,
    "active_po_only": 32,
    "avg_processing_time": "15 minutes", // vs 2-3 days for full workflow
    "top_po_customers": [
        {"name": "PT ABC Pemerintah", "total_pos": 12},
        {"name": "CV XYZ Corporate", "total_pos": 8}
    ]
}
```

---

## 🚨 Business Rules

### **Auto-Approval Criteria:**
✅ **Auto-approve PO_ONLY SPK IF:**
- Customer PO number provided
- Valid date range (start < end, future dates)
- Units available in inventory
- No blacklisted customer

❌ **Require manual approval IF:**
- High value (> Rp 100,000,000)
- Long duration (> 12 months)
- First-time customer
- Special equipment requested

---

## 🔐 Security & Permissions

**New Permissions:**
```php
'po_only.quick_entry' => 'Can create PO-only quick entries',
'po_only.auto_approve' => 'Can bypass SPK approval for PO-only',
'po_only.view_history' => 'Can view PO-only entry history',
```

**Role Mapping:**
- **Marketing Officer:** `quick_entry + view_history`
- **Marketing Manager:** All permissions
- **Admin:** All permissions

---

## 📅 Implementation Timeline

### **Week 1: Backend**
- [ ] Create `PoOnlyQuickEntry` controller
- [ ] Database migrations (if new table needed)
- [ ] Business logic for auto-SPK generation
- [ ] API endpoints
- [ ] Unit tests

### **Week 2: Frontend**
- [ ] Quick entry form UI
- [ ] Unit selection component
- [ ] Success/error handling
- [ ] History/list view
- [ ] Integration with existing marketing dashboard

### **Week 3: Testing & Refinement**
- [ ] UAT with marketing team
- [ ] Performance testing (bulk entries)
- [ ] Security audit
- [ ] Documentation

### **Week 4: Deployment & Training**
- [ ] Production deployment
- [ ] User training session
- [ ] Monitor initial usage
- [ ] Gather feedback

---

## 📝 User Stories

### **Story 1: Quick Government PO**
**As a** Marketing Officer  
**I want to** quickly enter a government PO without creating full quotation  
**So that** I can process routine government orders faster

**Acceptance Criteria:**
- Can submit PO number and basic details in < 2 minutes
- System auto-creates contract and SPK
- No manual approval needed for standard orders
- Can immediately proceed to DI creation

### **Story 2: Batch PO Entry**
**As a** Marketing Manager  
**I want to** process multiple POs in one session  
**So that** I can handle high-volume days efficiently

**Acceptance Criteria:**
- "Add Another" button after successful entry
- Session persists customer data if same customer
- Batch summary at end of session
- Excel import option (future enhancement)

---

## 🎨 UI/UX Mockups

### **Success Flow:**
```
Quick Entry Form
       ↓
[ Processing... ] (spinner)
       ↓
✓ PO-ONLY-2026-001 Created!
✓ SPK Auto-Approved: SPK/2026/###
✓ Contract: PO/2026/###
       ↓
[Create Delivery Instruction →]  [Add Another PO]
```

### **Dashboard Widget:**
```
┌─────────────────────────────────────┐
│ 📋 Quick PO Entry                   │
├─────────────────────────────────────┤
│ Customer PO: [________________] [→] │
│                                      │
│ Recent PO-Only Entries:             │
│ • PO-GOV-2026-005 | 3 units | Today │
│ • PO-CORP-2026-012 | 5 units | Today│
│                                      │
│ [View All PO History]               │
└─────────────────────────────────────┘
```

---

## ⚠️ Risks & Mitigation

| Risk | Impact | Mitigation |
|------|--------|------------|
| **Auto-approval abuse** | High | Implement value limits, audit logs, manager review dashboard |
| **Customer data incomplete** | Medium | Require minimal viable data, allow edit later |
| **Unit availability conflicts** | Medium | Real-time inventory check, queue system for high demand |
| **Revenue leakage** | High | Mandatory pricing validation, alert on below-standard rates |

---

## 📈 Success Metrics

### **KPIs to Track:**
- **Processing Time:** Target < 15 min (vs current 2-3 days)
- **Error Rate:** Target < 5% (vs current ~15% rework)
- **User Adoption:** Target 70% of PO-only orders use quick entry within 3 months
- **Customer Satisfaction:** Survey score > 4.5/5 for speed & ease

### **Monthly Report:**
```
PO_ONLY QUICK ENTRY - MONTHLY STATS
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Total Entries: 128
Avg Processing Time: 12 minutes ⚡ (-85%)
Auto-Approved: 115 (90%)
Manual Review Required: 13 (10%)
Total Revenue: Rp 1,240,000,000
```

---

## 🔗 API Endpoints

```
POST   /marketing/po-only/quick-entry         Create PO-only entry
GET    /marketing/po-only/history             List all PO-only entries
GET    /marketing/po-only/stats               Get statistics
POST   /marketing/po-only/bulk-import         Import from Excel/CSV
GET    /marketing/po-only/templates           Download entry templates
```

---

## 📚 Related Documentation

- [MARKETING_WORKFLOW_VERIFICATION.md](MARKETING_WORKFLOW_VERIFICATION.md) - Phase 1 implementation
- [DATABASE_SCHEMA.md](DATABASE_SCHEMA.md) - Current schema reference
- [WORKFLOW_GUIDE.md](WORKFLOW_GUIDE.md) - Complete workflow documentation

---

**Status:** ✅ Design complete - Ready for development approval  
**Next Step:** Present to Marketing Manager for approval → Start Week 1 development if approved

**Estimated Effort:** 3-4 weeks (1 developer)  
**Priority:** Medium (can run parallel with current workflow)  
**Risk Level:** Low (separate code path, minimal impact on existing system)
