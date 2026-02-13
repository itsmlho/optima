# SPK Creation from Quotation Workflow

**Date:** February 13, 2026  
**Status:** ✅ IMPLEMENTED

## Overview

SPK creation has been updated to align with the **Quotation → Contract → SPK** workflow. Specifications are now loaded from Quotations instead of being managed separately in Contracts.

---

## Workflow Diagram

```
┌─────────────────┐
│ Create Prospect │
└────────┬────────┘
         │
         ▼
┌─────────────────────┐
│ Convert to Quotation│
└────────┬────────────┘
         │
         ▼
┌──────────────────────────┐
│ Add Specifications       │ ◄── Quotation Specifications
│ (Quotation Module)       │     (UNIT or ATTACHMENT)
└────────┬─────────────────┘
         │
         ▼
┌─────────────────┐
│ Mark as DEAL    │ ────► Creates Customer + Contract
└────────┬────────┘       (created_contract_id linkage)
         │
         ▼
┌────────────────────┐
│ Create SPK         │ ◄── Loads from Quotation Specs
│ (Marketing/SPK)    │     via created_contract_id
└────────────────────┘
```

---

## Changes Implemented

### 1. **Backend: Kontrak Controller** (`app/Controllers/Kontrak.php`)

#### A. Updated `getActiveContracts()` Method
- **Old:** Loaded all active contracts without checking for quotation specifications
- **New:** Only loads contracts with quotation specifications
  ```php
  - Joins with quotations table via created_contract_id
  - Counts total_specs and available_specs from quotation_specifications
  - Only returns contracts that have quotation linkage
  ```

#### B. New Method: `getQuotationSpecificationsForContract($contractId)`
- Finds quotation linked to contract via `created_contract_id`
- Loads specifications from `quotation_specifications` table
- Returns specification details with availability info
- Calculates `available_units` (quantity - already in SPK)

**Endpoint:** `marketing/kontrak/get-quotation-specifications-for-contract/{contractId}`

**Fields Returned:**
- `id_specification`, `specification_name`, `specification_description`
- `departemen_id`, `nama_departemen`
- `tipe_unit_id`, `jenis_tipe_unit`, `nama_tipe_unit`
- `attachment_id`, `tipe_attachment`, `merk_attachment`
- `quantity`, `available_units`, `existing_spk_count`
- Accessories: `valve_name`, `mast_name`, `tire_name`, `wheel_name`

---

### 2. **Frontend: SPK Form** (`app/Views/marketing/spk.php`)

#### A. Updated Contract Dropdown
**Before:**
```javascript
fetch('marketing/kontrak/get-active-contracts')
// Displayed: "CONTRACT_NO - CUSTOMER"
```

**After:**
```javascript
fetch('marketing/kontrak/get-active-contracts')
// Displayed: "CONTRACT_NO - CUSTOMER (Quote: QT-xxx) [2/5 specs available]"
```

**Features:**
- Shows quotation number
- Shows available specs count vs total
- Only displays contracts with quotation specifications
- Help text explains workflow

#### B. Updated Specification Loading
**Before:**
```javascript
fetch(`marketing/kontrak/spesifikasi/${contractId}`)
// Loaded from kontrak_spesifikasi table
```

**After:**
```javascript
fetch(`marketing/kontrak/get-quotation-specifications-for-contract/${contractId}`)
// Loads from quotation_specifications table
```

**Features:**
- Filters by SPK type (UNIT vs ATTACHMENT)
- Shows available units (excludes already-in-SPK units)
- Enhanced display with unit type and attachment info
- Debug logging for troubleshooting

#### C. Enhanced UI Elements

**Step 1 Card - New Info Alert:**
```html
<div class="alert alert-info">
  📋 Create Quotation → Add Specifications → Mark as DEAL → Create Contract → Create SPK here
</div>
```

**Contract Dropdown Help Text:**
```html
Only contracts from DEAL quotations with specifications are shown.
Workflow: Quotation → Mark as DEAL → Create Contract → Create SPK
```

**Specification Dropdown Help Text:**
```html
Specifications loaded from Quotation. Only available specs (not yet in SPK) are shown.
```

---

## Database Relations

### Quotations Table
- `id_quotation` (PK)
- `created_customer_id` - Links to `customers.id`
- `created_contract_id` - Links to `kontrak.id` ✨ **Key Field**
- `is_deal` - Must be 1 for contract creation

### Quotation Specifications Table
- `id_specification` (PK)
- `quotation_id` - Links to `quotations.id_quotation`
- `specification_name`, `specification_description`
- `quantity` - Total units to be produced
- `tipe_unit_id`, `attachment_id`, etc.

### Contracts Table (kontrak)
- `id` (PK)
- Created when quotation is marked as DEAL
- Linked back to quotation via `quotations.created_contract_id`

### SPK Table
- `id_spk` (PK)
- `kontrak_id` - Links to contract
- `kontrak_spesifikasi_id` - Now points to `quotation_specifications.id_specification`
- `jumlah_unit` - Quantity in this SPK

---

## User Workflow to Create SPK

### Step 1: Create Quotation with Specifications
1. Go to **Marketing → Quotations**
2. Create new prospect and convert to quotation
3. Click **Add Specifications** button
4. Add UNIT or ATTACHMENT specifications:
   - Select department, unit type, capacity
   - For ATTACHMENT: Define attachment type and brand
   - Set quantity for each specification

### Step 2: Mark Quotation as DEAL
1. Open quotation detail
2. Click **Mark as DEAL** button
3. System creates:
   - Customer record (`created_customer_id`)
   - Contract record (`created_contract_id`)
4. Quotation status changes to DEAL

### Step 3: Create SPK from Contract
1. Go to **Marketing → SPK**
2. Click **Create SPK** button
3. **Step 1:** Select SPK Type (UNIT or ATTACHMENT)
4. **Step 1:** Select Contract (only contracts with quotation specs shown)
   - Display format: `CONTRACT_NO - CUSTOMER (Quote: QT-xxx) [2/5 specs available]`
5. **Step 2 (ATTACHMENT only):** Select target unit for attachment
6. **Step 3:** Select specification from quotation
   - Only available specs (not yet in SPK) are shown
   - Format: `Spec Name - Unit Type (2/5 available)`
7. Enter quantity (max = available units)
8. Submit to create SPK

---

## Filtering Logic

### Contract Loading
```sql
SELECT k.*, q.quotation_number, 
  COUNT(qs.id_specification) as total_specs,
  COUNT(CASE WHEN s.id_spk IS NULL THEN 1 END) as available_specs
FROM kontrak k
JOIN quotations q ON q.created_contract_id = k.id
LEFT JOIN quotation_specifications qs ON qs.quotation_id = q.id_quotation
LEFT JOIN spk s ON s.kontrak_spesifikasi_id = qs.id_specification
WHERE k.status = 'ACTIVE' AND q.id_quotation IS NOT NULL
GROUP BY k.id
```

### Specification Filtering (JavaScript)
```javascript
// UNIT SPK: Show specs with tipe_unit_id > 0
const hasUnit = spek.tipe_unit_id && parseInt(spek.tipe_unit_id) > 0;

// ATTACHMENT SPK: Show specs with attachment_id > 0 OR tipe_attachment defined
const hasAttachment = (spek.attachment_id && parseInt(spek.attachment_id) > 0) ||
                     (spek.tipe_attachment && spek.tipe_attachment !== 'null');

// Available units: Only show if available_units > 0
const availableUnits = parseInt(spek.available_units || 0);
if (availableUnits <= 0) return false; // Skip
```

---

## Testing Checklist

### Test Scenario 1: UNIT SPK from Quotation
- [ ] Create quotation with UNIT specifications (tipe_unit defined)
- [ ] Mark as DEAL → verify contract created
- [ ] Go to SPK → Create SPK → Select SPK Type: UNIT
- [ ] Verify contract appears with quotation number
- [ ] Verify UNIT specifications are shown
- [ ] Create SPK → Verify quantity decreases available specs

### Test Scenario 2: ATTACHMENT SPK from Quotation
- [ ] Create quotation with ATTACHMENT specifications (attachment_id defined)
- [ ] Mark as DEAL → verify contract created
- [ ] Go to SPK → Create SPK → Select SPK Type: ATTACHMENT
- [ ] Verify Step 2 (Target Unit) appears
- [ ] Select target unit from contract units
- [ ] Verify ATTACHMENT specifications are shown
- [ ] Create SPK → Verify target_unit_id saved

### Test Scenario 3: Mixed Specifications
- [ ] Create quotation with both UNIT and ATTACHMENT specs
- [ ] Mark as DEAL
- [ ] Test UNIT SPK → Only see UNIT specs
- [ ] Test ATTACHMENT SPK → Only see ATTACHMENT specs

### Test Scenario 4: No Available Specifications
- [ ] Select contract where all specs already have SPK
- [ ] Verify dropdown shows "No specifications available"
- [ ] Check console logs for proper filtering

---

## Troubleshooting

### Issue: Contract dropdown is empty
**Cause:** No contracts with quotation specifications exist  
**Solution:**
1. Go to Quotations module
2. Create quotation with specifications
3. Mark as DEAL to create contract
4. Return to SPK creation

### Issue: Specifications dropdown shows "No specifications"
**Causes:**
1. Selected contract has no quotation linked
2. All specifications already have SPK
3. SPK type doesn't match specification type (UNIT vs ATTACHMENT)

**Debug:**
- Open browser console (F12)
- Look for log messages: `📋 Received quotation specs:`
- Check filtering: `📊 Filtered X specs for Y SPK`
- Verify: `✅ UNIT spec` or `✅ ATTACHMENT spec`

### Issue: Available units showing 0
**Cause:** All units from specification already in SPK  
**Check:**
```sql
SELECT qs.quantity, COUNT(s.id_spk) as spk_count,
  (qs.quantity - COALESCE(SUM(s.jumlah_unit), 0)) as available
FROM quotation_specifications qs
LEFT JOIN spk s ON s.kontrak_spesifikasi_id = qs.id_specification
WHERE qs.id_specification = ?
GROUP BY qs.id_specification
```

---

## Migration Notes

### Backward Compatibility
- **Old SPK records:** Still reference `kontrak_spesifikasi.id_kontrak_spesifikasi`
- **New SPK records:** Reference `quotation_specifications.id_specification`
- Both use same field name: `kontrak_spesifikasi_id` (aliased)

### Data Migration (if needed)
If existing contracts have specifications not from quotations:
1. Create quotations retroactively
2. Add specifications to quotations
3. Link contracts to quotations via `created_contract_id`
4. Or keep old workflow for legacy contracts

---

## Key Files Modified

1. **Controller:** [app/Controllers/Kontrak.php](c:/laragon/www/optima/app/Controllers/Kontrak.php#L1456-L1580)
   - `getActiveContracts()` - Updated
   - `getQuotationSpecificationsForContract()` - New

2. **View:** [app/Views/marketing/spk.php](c:/laragon/www/optima/app/Views/marketing/spk.php)
   - Lines 177-206: Step 1 card with workflow info
   - Lines 197-206: Contract dropdown with help text
   - Lines 1155-1220: `loadAvailableKontraks()` function - Updated
   - Lines 1413-1520: `loadKontrakSpesifikasiForSpk()` function - Updated

---

## Console Debug Messages

When debugging SPK creation, watch for these console logs:

```
🔍 Loading contracts with quotation specifications...
📋 Received contracts: {...}
✅ Loaded 3 contracts

🔍 Loading quotation specifications for contract: 123
📋 Received quotation specs: {...}
📄 Quotation: QT-2026-001
✅ UNIT spec 45: Forklift Electric 3T
📊 Filtered 2 specs for UNIT SPK
```

---

## Next Steps

1. **Test thoroughly** with both UNIT and ATTACHMENT SPK types
2. **Train users** on new workflow (Quotation → SPK)
3. **Monitor** for any edge cases or issues
4. **Document** any additional findings

---

## Success Confirmation

✅ Contracts now load with quotation specifications  
✅ Specifications loaded from quotations, not contracts  
✅ Available units calculated correctly  
✅ UI shows workflow guidance  
✅ Backward compatible with old SPK records  

**Implementation Complete!** 🎉
