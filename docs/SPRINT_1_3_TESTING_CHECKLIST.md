# Sprint 1-3 Testing Checklist
**Generated:** February 10, 2026  
**Integration:** Customer Management Modal  
**Status:** Ready for Testing

## ✅ Pre-Testing Verification

### Database Status
- [x] 5 new tables created (contract_renewal_workflow, contract_renewal_unit_map, unit_billing_schedules, contract_amendments, amendment_unit_rates)
- [x] 13 new columns added to kontrak table
- [x] All migrations executed successfully

### Backend Status
- [x] 9 API endpoints implemented
- [x] BillingCalculator library integrated with InvoiceModel
- [x] BackBillingService created
- [x] All routes registered in Routes.php
- [x] No syntax errors in Kontrak.php controller

### Frontend Status
- [x] 3 modal components created (renewal_wizard.php, addendum_prorate.php, asset_history.php)
- [x] Integrated into customer_management.php
- [x] Action buttons converted to dropdown menu
- [x] No syntax errors in customer_management.php

---

## 🔍 Testing Workflow

### Phase 1: Access Integration Point (5 mins)

**Navigation Path:**
```
Login → Marketing → Customer Management → Click Customer Row → Contracts & PO Tab
```

**Expected Results:**
- ✅ Customer detail modal opens
- ✅ "Contracts & PO (3)" tab visible
- ✅ Contract table displays with data
- ✅ **Actions dropdown button** visible on each contract row

**Screenshot Checklist:**
- [ ] Contracts & PO tab loaded
- [ ] Actions dropdown visible (blue button with "Actions" text)

---

### Phase 2: Test Actions Dropdown (3 mins)

**Steps:**
1. Click **Actions** dropdown button on any contract row
2. Verify dropdown menu opens with options

**Expected Dropdown Menu:**
```
┌─────────────────────────────────────┐
│ 🟢 Renew Contract                   │  ← Only for ACTIVE/expiring contracts
│ ─────────────────────────────────   │
│ 🟡 Change Rate / Amendment          │  ← Only for ACTIVE contracts
│ 🔵 View History                     │  ← Available for all contracts
│ ─────────────────────────────────   │
│ ✏️ Edit Contract                     │
│ 🗑️ Delete Contract                   │
└─────────────────────────────────────┘
```

**Validation:**
- [ ] Dropdown opens on click
- [ ] "Renew Contract" shows for ACTIVE/expiring contracts
- [ ] "Change Rate / Amendment" shows ONLY for ACTIVE contracts
- [ ] "View History" shows for all contracts
- [ ] Edit and Delete options always visible

---

### Phase 3: Test Renewal Wizard (20 mins)

**Test Contract Requirements:**
- Contract must be ACTIVE or expiring within 30 days
- Contract must have at least 1 unit

**Test Steps:**

#### Step 3.1: Open Renewal Wizard
1. Click Actions → **Renew Contract** on an ACTIVE contract
2. Wait for wizard modal to open

**Expected Results:**
- ✅ Modal opens with title "Contract Renewal Wizard"
- ✅ Step 1/5 indicator visible
- ✅ Parent contract info auto-populated:
  - Old contract number
  - Customer name
  - Current period dates
- ✅ New dates auto-calculated (gap-free, +1 year)

**Validation:**
- [ ] Wizard modal opened successfully
- [ ] Parent contract number displayed: `_____________`
- [ ] Customer name displayed: `_____________`
- [ ] Suggested start date = old end date + 1 day
- [ ] Suggested end date = start date + 1 year - 1 day

#### Step 3.2: Configure New Contract (Step 2)
1. Click **Next** to go to Step 2
2. Enter new contract number (format: `SML/SPK/XXXX/II/2026`)
3. Verify customer location auto-selected
4. Select billing method (Monthly/Daily/Prorate)
5. Select rental type (Contract Only/PO Only/Contract & PO)
6. If PO: Enter PO Number

**Validation:**
- [ ] Contract number field accepts input
- [ ] Customer location dropdown populated
- [ ] Billing method options visible
- [ ] Rental type options visible
- [ ] PO Number field shows when "PO Only" or "Contract & PO" selected

#### Step 3.3: Select Units (Step 3)
1. Click **Next** to go to Step 3
2. Review units table (all units from parent contract pre-selected)
3. Verify unit details (Unit #, Brand/Model, Current Rate)

**Validation:**
- [ ] Units table populated with parent contract units
- [ ] All units checked by default
- [ ] Unit numbers displayed correctly
- [ ] Current monthly rates shown

#### Step 3.4: Adjust Rates (Step 4)
1. Click **Next** to go to Step 4
2. Review rate adjustment options:
   - Keep existing rates
   - Apply % increase/decrease
   - Set individual unit rates
3. Test rate adjustment (e.g., 10% increase)
4. Verify calculated new rates

**Validation:**
- [ ] Rate adjustment options visible
- [ ] Percentage input works
- [ ] "Apply to All" button works
- [ ] New rates calculated correctly
- [ ] Total contract value updated

#### Step 3.5: Review & Submit (Step 5)
1. Click **Next** to go to Step 5
2. Review complete summary:
   - Contract configuration
   - Selected units (count)
   - Rate changes
   - Total value comparison
3. Click **Create Renewal Contract**

**Expected Results:**
- ✅ Success notification appears
- ✅ Modal closes
- ✅ Contract list refreshes
- ✅ New renewal contract visible with "DRAFT_RENEWAL" status

**Backend Verification:**
```sql
-- Check renewal contract created
SELECT * FROM kontrak 
WHERE parent_contract_id = [PARENT_ID] 
AND is_renewal = 1 
ORDER BY id DESC LIMIT 1;

-- Check renewal workflow record
SELECT * FROM contract_renewal_workflow 
WHERE renewal_contract_id = [NEW_CONTRACT_ID];

-- Check unit mapping
SELECT * FROM contract_renewal_unit_map 
WHERE renewal_contract_id = [NEW_CONTRACT_ID];
```

**Validation:**
- [ ] New contract record created in kontrak table
- [ ] parent_contract_id = old contract ID
- [ ] is_renewal = 1
- [ ] renewal_generation = parent generation + 1
- [ ] status = 'DRAFT_RENEWAL'
- [ ] Workflow record created
- [ ] Unit mapping records created
- [ ] Activity log entry created

---

### Phase 4: Test Amendment (Prorate Calculator) (15 mins)

**Test Contract Requirements:**
- Contract must be ACTIVE status
- Contract must have units with rates

**Test Steps:**

#### Step 4.1: Open Amendment Modal
1. Find an ACTIVE contract
2. Click Actions → **Change Rate / Amendment**
3. Wait for modal to open

**Expected Results:**
- ✅ "Addendum Prorate Calculator" modal opens
- ✅ Contract dropdown populated with active contracts
- ✅ Current contract pre-selected

**Validation:**
- [ ] Modal opened successfully
- [ ] Contract dropdown has options
- [ ] Current contract auto-selected

#### Step 4.2: Select Period & Date
1. Contract dropdown should trigger unit loading
2. Verify billing period auto-detected from contract dates
3. Select effective date (within current billing period)

**Example:**
```
Billing Period: 2026-02-01 to 2026-02-28 (28 days)
Effective Date: 2026-02-15

Expected Prorate Split:
- Part 1 (Old Rate): Feb 1-14 = 14 days
- Part 2 (New Rate): Feb 15-28 = 14 days
```

**Validation:**
- [ ] Units table populated on contract selection
- [ ] Current rates displayed
- [ ] Period dates shown correctly
- [ ] Effective date picker available

#### Step 4.3: Modify Unit Rates
1. Change rate for at least one unit (e.g., 5,000,000 → 6,000,000)
2. Verify prorate calculation updates in real-time

**Expected Calculation (example):**
```
Unit: XYZ-001
Old Rate: Rp 5,000,000/month
New Rate: Rp 6,000,000/month

Prorate Split (28-day month, change on day 15):
- Part 1: (5,000,000 / 30) × 14 days = Rp 2,333,333
- Part 2: (6,000,000 / 30) × 14 days = Rp 2,800,000
- Total Invoice: Rp 5,133,333
```

**Validation:**
- [ ] Rate input accepts changes
- [ ] Prorate Part 1 calculated correctly
- [ ] Prorate Part 2 calculated correctly
- [ ] Total displays correctly
- [ ] All units' prorates sum up correctly

#### Step 4.4: Submit Amendment
1. Enter reason (e.g., "Client requested rate adjustment")
2. Add notes (optional)
3. Click **Create Amendment**

**Expected Results:**
- ✅ Success notification
- ✅ Modal closes
- ✅ Contract units updated with new rates

**Backend Verification:**
```sql
-- Check amendment record
SELECT * FROM contract_amendments 
WHERE contract_id = [CONTRACT_ID] 
ORDER BY id DESC LIMIT 1;

-- Check unit rate changes
SELECT * FROM amendment_unit_rates 
WHERE amendment_id = [AMENDMENT_ID];

-- Verify contract_units updated
SELECT unit_id, monthly_rate, rate_changed_at 
FROM contract_units 
WHERE contract_id = [CONTRACT_ID];
```

**Validation:**
- [ ] Amendment record created with correct prorate_split JSON
- [ ] amendment_unit_rates records created for changed units
- [ ] contract_units.monthly_rate updated to new rate
- [ ] contract_units.rate_changed_at = effective date
- [ ] Activity log entry created

---

### Phase 5: Test Asset History (10 mins)

**Test Steps:**

#### Step 5.1: Open History Modal
1. Click Actions → **View History** on any contract
2. Wait for modal to load

**Expected Results:**
- ✅ "Asset & Contract History" modal opens
- ✅ Loading spinner visible initially
- ✅ 4 tabs available:
  1. Contract View (Timeline)
  2. Unit Journey
  3. Renewal Chains
  4. Rate History

**Validation:**
- [ ] Modal opened successfully
- [ ] All 4 tabs visible
- [ ] Loading state shown before data loads

#### Step 5.2: Test Contract View Tab
1. Wait for timeline to load
2. Verify timeline events displayed chronologically

**Expected Timeline Events:**
- 📄 Contract created (with date, contract number)
- 📝 Amendments (with effective date, reason, prorate amounts)
- 🔄 Renewals (with generation number, new contract number)
- 🚚 Unit off-hires (if any)

**Validation:**
- [ ] Timeline displays events
- [ ] Events sorted by date (newest first)
- [ ] Icons displayed correctly for each event type
- [ ] Contract created event always present
- [ ] Amendment events show prorate details
- [ ] Renewal events show generation

#### Step 5.3: Test Unit Journey Tab
1. Click "Unit Journey" tab
2. Verify unit assignments and movements

**Expected Data:**
- Unit list with on-hire dates
- Off-hire dates (if applicable)
- Unit transfer history (if any)

**Validation:**
- [ ] Unit list populated
- [ ] On-hire dates correct
- [ ] Off-hire dates shown if unit removed

#### Step 5.4: Test Renewal Chains Tab
1. Click "Renewal Chains" tab
2. Verify contract lineage displayed

**Expected Display:**
```
Parent Contract → Renewal Gen 1 → Renewal Gen 2
SML/001/2024        SML/002/2025       SML/003/2026
(Original)          (1st Renewal)      (2nd Renewal)
```

**Validation:**
- [ ] Renewal chain shows lineage
- [ ] Generation numbers correct
- [ ] Parent-child relationship clear
- [ ] All renewal contracts in chain visible

#### Step 5.5: Test Rate History Tab
1. Click "Rate History" tab
2. Wait for rate changes to load

**Expected Table:**
| Date       | Event Type | Unit   | Old Rate | New Rate | Change | Reason                      |
|------------|-----------|--------|----------|----------|--------|-----------------------------|
| 2026-02-15 | Amendment | XYZ-01 | 5,000,000| 6,000,000| +20%   | Client requested adjustment |
| 2025-12-01 | Renewal   | XYZ-01 | 4,500,000| 5,000,000| +11%   | Contract renewal            |

**Validation:**
- [ ] Rate changes table populated
- [ ] Sorted by date (newest first)
- [ ] Event type displayed (Amendment/Renewal)
- [ ] Old and new rates shown
- [ ] Percentage change calculated
- [ ] Reason displayed

---

## 🔬 Backend Logic Verification

### Endpoint Testing via Browser Console

#### Test Renewal Endpoint
```javascript
// Test renewal creation
fetch('/optima/kontrak/createRenewal', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
        parent_contract_id: 70,
        contract_number: 'TEST/RENEWAL/001/II/2026',
        start_date: '2026-02-01',
        end_date: '2027-01-31',
        billing_method: 'MONTHLY',
        rental_type: 'CONTRACT',
        customer_id: 1,
        location_id: 1,
        units: JSON.stringify([
            {unit_id: 123, monthly_rate: 5000000}
        ])
    })
})
.then(r => r.json())
.then(data => console.log('Renewal Result:', data));
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Renewal contract created successfully",
    "contract_id": 999,
    "contract_number": "TEST/RENEWAL/001/II/2026"
}
```

#### Test Amendment Endpoint
```javascript
// Test prorate amendment
fetch('/optima/kontrak/createProrateAmendment', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
    },
    body: JSON.stringify({
        contract_id: 70,
        effective_date: '2026-02-15',
        reason: 'Rate increase test',
        period_start: '2026-02-01',
        period_end: '2026-02-28',
        unit_rates: JSON.stringify([
            {unit_id: 123, old_rate: 5000000, new_rate: 6000000}
        ])
    })
})
.then(r => r.json())
.then(data => console.log('Amendment Result:', data));
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Amendment created successfully with prorate split",
    "amendment_id": 55,
    "prorate_total": 5133333
}
```

#### Test History Endpoint
```javascript
// Test contract history
fetch('/optima/kontrak/getContractHistory/70', {
    headers: {'X-Requested-With': 'XMLHttpRequest'}
})
.then(r => r.json())
.then(data => {
    console.log('History Result:', data);
    console.log('Total Events:', data.data.events.length);
});
```

**Expected Response:**
```json
{
    "success": true,
    "data": {
        "contract": {...},
        "events": [
            {
                "type": "contract",
                "date": "2025-12-01 10:30:00",
                "description": "Contract created",
                ...
            },
            {
                "type": "amendment",
                "date": "2026-02-15",
                "reason": "Rate adjustment",
                ...
            }
        ]
    }
}
```

#### Test Rate History Endpoint
```javascript
// Test rate history
fetch('/optima/kontrak/getRateHistory/70', {
    headers: {'X-Requested-With': 'XMLHttpRequest'}
})
.then(r => r.json())
.then(data => {
    console.log('Rate History:', data);
    console.log('Total Changes:', data.data.length);
});
```

---

## 📊 Database Verification Queries

### Check Renewal Records
```sql
-- Get latest renewal
SELECT 
    k.id,
    k.no_kontrak,
    k.parent_contract_id,
    k.is_renewal,
    k.renewal_generation,
    k.status,
    parent.no_kontrak as parent_contract_number
FROM kontrak k
LEFT JOIN kontrak parent ON parent.id = k.parent_contract_id
WHERE k.is_renewal = 1
ORDER BY k.id DESC
LIMIT 10;
```

### Check Amendment Records
```sql
-- Get latest amendments with prorate details
SELECT 
    ca.id,
    ca.contract_id,
    k.no_kontrak,
    ca.effective_date,
    ca.reason,
    ca.old_total_value,
    ca.new_total_value,
    ca.prorate_total,
    ca.prorate_split
FROM contract_amendments ca
LEFT JOIN kontrak k ON k.id = ca.contract_id
ORDER BY ca.id DESC
LIMIT 10;
```

### Check Unit Rate Changes
```sql
-- Get recent unit rate changes from amendments
SELECT 
    aur.id,
    ca.contract_id,
    k.no_kontrak,
    u.nomor_unit,
    aur.old_rate,
    aur.new_rate,
    (aur.new_rate - aur.old_rate) as rate_diff,
    ROUND(((aur.new_rate - aur.old_rate) / aur.old_rate * 100), 2) as pct_change,
    ca.effective_date
FROM amendment_unit_rates aur
LEFT JOIN contract_amendments ca ON ca.id = aur.amendment_id
LEFT JOIN kontrak k ON k.id = ca.contract_id
LEFT JOIN unit u ON u.id = aur.unit_id
ORDER BY aur.id DESC
LIMIT 20;
```

### Verify BillingCalculator Integration
```sql
-- Check if invoices use correct billing methods
SELECT 
    i.no_invoice,
    k.no_kontrak,
    k.billing_method,
    i.tanggal_mulai_periode as billing_start,
    i.tanggal_akhir_periode as billing_end,
    i.amount,
    i.created_at
FROM invoices i
LEFT JOIN kontrak k ON k.id = i.contract_id
WHERE i.created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
ORDER BY i.id DESC
LIMIT 10;
```

---

## ✅ Success Criteria

### Functional Requirements
- [x] All 3 features accessible from Customer Management modal
- [ ] Renewal wizard creates valid renewal contracts
- [ ] Amendment calculator applies prorate correctly
- [ ] History modal displays all contract events
- [ ] All endpoints return valid JSON responses
- [ ] Database transactions complete successfully

### Data Integrity
- [ ] Parent-child relationships maintained
- [ ] Renewal generation numbers increment correctly
- [ ] Unit rate changes tracked in amendment tables
- [ ] Original contract data preserved
- [ ] Activity logs created for all actions

### User Experience
- [ ] Actions dropdown easy to use
- [ ] Modals open without errors
- [ ] Loading states displayed appropriately
- [ ] Success/error notifications shown
- [ ] Forms validate input correctly
- [ ] Responsive layout on different screen sizes

### Performance
- [ ] Modals open in < 2 seconds
- [ ] AJAX requests complete in < 3 seconds
- [ ] Timeline renders < 5 seconds
- [ ] No console errors
- [ ] No PHP errors in logs

---

## 🐛 Known Issues to Watch

### Potential Edge Cases
1. **Contract with no units**: Should show warning in renewal wizard
2. **Expired contracts**: Amendment button should NOT show
3. **DRAFT contracts**: Renew button should NOT show
4. **Effective date outside period**: Should show validation error
5. **Zero or negative rates**: Should prevent submission

### Browser Compatibility
- Test in Chrome/Edge (primary)
- Test dropdown behavior on mobile (if applicable)
- Verify modal scrolling on small screens

---

## 📝 Testing Report Template

```markdown
# Sprint 1-3 Testing Report

**Tester:** ___________  
**Date:** February 10, 2026  
**Environment:** http://localhost/optima  

## Phase 1: Access ✅ / ❌
- Navigation: ___ (Pass/Fail)
- Contracts tab loaded: ___ (Pass/Fail)
- Actions dropdown visible: ___ (Pass/Fail)

## Phase 2: Actions Dropdown ✅ / ❌
- Dropdown opens: ___ (Pass/Fail)
- Conditional buttons work: ___ (Pass/Fail)

## Phase 3: Renewal Wizard ✅ / ❌
- Wizard opens: ___ (Pass/Fail)
- Auto-calculations: ___ (Pass/Fail)
- Unit selection: ___ (Pass/Fail)
- Rate adjustment: ___ (Pass/Fail)
- Contract creation: ___ (Pass/Fail)
- Database verification: ___ (Pass/Fail)

## Phase 4: Amendment (Prorate) ✅ / ❌
- Modal opens: ___ (Pass/Fail)
- Prorate calculation: ___ (Pass/Fail)
- Unit rate updates: ___ (Pass/Fail)
- Database verification: ___ (Pass/Fail)

## Phase 5: Asset History ✅ / ❌
- Timeline tab: ___ (Pass/Fail)
- Unit journey tab: ___ (Pass/Fail)
- Renewal chains tab: ___ (Pass/Fail)
- Rate history tab: ___ (Pass/Fail)

## Backend Endpoints ✅ / ❌
- createRenewal: ___ (Pass/Fail)
- createProrateAmendment: ___ (Pass/Fail)
- getContractHistory: ___ (Pass/Fail)
- getRateHistory: ___ (Pass/Fail)

## Overall Status: ✅ PASS / ❌ FAIL

**Issues Found:**
1. ___________
2. ___________

**Notes:**
___________
```

---

## 🚀 Production Deployment Checklist

- [ ] All test phases passed
- [ ] Database verified clean
- [ ] No console errors
- [ ] No PHP errors in logs
- [ ] Performance acceptable
- [ ] User training completed
- [ ] Documentation updated
- [ ] Backup created before deployment
- [ ] Rollback plan prepared

---

## 📞 Support Information

**Technical Contact:** Development Team  
**Documentation:** `/docs/SPRINT_1_3_INTEGRATION_GUIDE.md`  
**Deployment Summary:** `/docs/SPRINT_1_3_DEPLOYMENT_SUMMARY.md`

---

**END OF TESTING CHECKLIST**
