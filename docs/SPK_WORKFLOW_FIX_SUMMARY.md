# SPK Workflow Correction - Summary

**Date:** February 13, 2026  
**Status:** 🔧 IN PROGRESS

## Problem Identified

User menemukan inkonsistensi:

### ❌ Masalah:
1. **Di Quotation Module**: Bisa create SPK tanpa contract (contract  optional)
2. **Di SPK Form**: Require contract (hanya load contracts dengan quotations)
3. **Database**: `spk.kontrak_id` bisa NULL (documented as "if Rental")
4. **SpkModel**: Ada method `createFromQuotation()` dengan `kontrak_id => null`

### ✅ Workflow Yang Benar:

**SPK Creation Path:**
```
Quotation (DEAL) → Create SPK
├─ Required: created_customer_id ✅
├─ Required: quotation_specifications ✅
└─ Optional: created_contract_id (bisa NULL) ⏳
```

**Database Schema:**
```php
// spk table
'kontrak_id' => NULL allowed  // Contract optional
'quotation_id' => Required    // Source of truth
'quotation_specification_id' => Required
'source_type' => 'QUOTATION'   // Or 'CONTRACT' if from contract
```

---

## Changes Made

### 1. Backend: New Endpoints ([Kontrak.php](c:/laragon/www/optima/app/Controllers/Kontrak.php))

#### A. `getActiveQuotationsForSPK()` ✅ 
**New Primary Endpoint**
- Load DEAL quotations (is_deal = 1)
- Requires: created_customer_id NOT NULL
- Requires: Has specifications (total_specs > 0)
- Requires: Available specs (available_specs > 0)
- Contract info included but optional (LEFT JOIN)

**URL:** `marketing/kontrak/get-active-quotations-for-spk`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id_quotation": 1,
      "quotation_number": "QT-2026-001",
      "prospect_name": "ABC Company",
      "customer_name": "ABC Registered",
      "created_customer_id": 5,
      "created_contract_id": 10,      // NULL if no contract
      "contract_id": 10,               // NULL if no contract
      "no_kontrak": "K-2026-001",     // NULL if no contract
      "contract_status": "ACTIVE",     // NULL if no contract
      "total_specs": 5,
      "available_specs": 3
    }
  ]
}
```

#### B. `getQuotationSpecificationsForSPK($quotationId)` ✅
**Load specifications from quotation**
- Get quotation info with customer + contract (if exists)
- Get all specifications
- Calculate available_units (quantity - already in SPK)
- Return quotation + specifications

**URL:** `marketing/kontrak/get-quotation-specifications-for-spk/{quotationId}`

---

### 2. Frontend: SPK Form Updates

#### A. UI Changes ✅ DONE

**Old HTML:**
```html
<label>Select Contract *</label>
<select id="kontrakSelect" required>
  <!-- Contracts only -->
</select>
```

**New HTML:**
```html
<label>Select DEAL Quotation *</label>
<select id="quotationSelect" required>
  <!-- DEAL Quotations - contract info shown but optional -->
</select>
<div class="form-text">
  Contract info displayed if available (optional)
</div>
```

**Workflow Alert Updated:**
```html
<div class="alert alert-info">
  📋 Create Quotation → Add Specifications → Mark as DEAL → Create SPK here
  💡 Contract is optional - can be linked later
</div>
```

#### B. JavaScript Changes ✅ PARTIAL

**Variables Renamed:**
```javascript
// Old
const kontrakSelect = document.getElementById('kontrakSelect');

// New
const quotationSelect = document.getElementById('quotationSelect');
```

**Function Renamed:**
```javascript
// Old
loadAvailableKontraks() // Load contracts

// New  
loadAvailableQuotations() // Load DEAL quotations
```

**Display Format:**
```javascript
// Old
`${contractNum} - ${customerName} (Quote: ${quotationNum}) [2/5 specs]`

// New
`${quotationNum} - ${customerName} [2/5 specs] - ✅ Contract: ${contractNum}`
//                                              - ⏳ Contract Pending
```

---

### 3. Remaining Work 🔧 TODO

#### Step 1: Update quotation change event handler
**File:** `app/Views/marketing/spk.php`
**Line:** ~1260

```javascript
// OLD: if (kontrakSelect)
if (quotationSelect) {
    quotationSelect.addEventListener('change', function() {
        const quotationId = this.value;
        
        if (quotationId) {
            // Load quotation info (customer, contract if exists)
            loadQuotationInfo(quotationId);
            
            // Load specifications for this quotation
            loadQuotationSpesifikasiForSpk(quotationId);
            
            // Load units for ATTACHMENT if SPK type is ATTACHMENT
            const jenisSpk = document.getElementById('jenisSpkSelect');
            if (jenisSpk && jenisSpk.value === 'ATTACHMENT') {
                // Load units from contract if exists, or show warning
                const selectedOption = this.options[this.selectedIndex];
                const contractId = selectedOption.dataset.contract;
                
                if (contractId) {
                    loadContractUnitsForAttachment(contractId);
                } else {
                    // Show warning: Attachment SPK needs contract with units
                    showAttachmentContractWarning();
                }
            }
            
            // Show sections
            showInfoSections();
        } else {
            hideAllSections();
        }
    });
}
```

#### Step 2: Rename loadKontrakSpesifikasiForSpk
**File:** `app/Views/marketing/spk.php`
**Line:** ~1413

```javascript
// OLD function name
function loadKontrakSpesifikasiForSpk(kontrakId) {
    fetch(`marketing/kontrak/get-quotation-specifications-for-contract/${kontrakId}`)
}

// NEW function name + endpoint
function loadQuotationSpesifikasiForSpk(quotationId) {
    console.log('🔍 Loading specifications for quotation:', quotationId);
    
    fetch(`<?= base_url('marketing/kontrak/get-quotation-specifications-for-spk/') ?>${quotationId}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Content-Type': 'application/json'
        }
    })
    // ... rest of logic same
}
```

#### Step 3: Update loadQuotationInfo function
**NEW Function Needed:**

```javascript
function loadQuotationInfo(quotationId) {
    fetch(`<?= base_url('marketing/quotations/getQuotation/') ?>${quotationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                const quotation = data.data;
                
                // Populate customer fields
                if (inpPelanggan) inpPelanggan.value = quotation.customer_name || quotation.prospect_name;
                if (inpPic) inpPic.value = quotation.pic_name ||'';
                if (inpKontak) inpKontak.value = quotation.pic_phone || '';
                if (inpLokasi) inpLokasi.value = quotation.location_name ||'';
                
                // Fill contract number if exists
                if (inpPoKontrak) {
                    const contractNum = quotation.no_kontrak || quotation.kontrak_number || quotation.po_number;
                    inpPoKontrak.value = contractNum || 'Contract Pending';
                    
                    if (!contractNum) {
                        inpPoKontrak.classList.add('text-warning');
                    } else {
                        inpPoKontrak.classList.remove('text-warning');
                    }
                }
                
                // Load customer locations
                if (quotation.created_customer_id) {
                    loadCustomerLocations(quotation.created_customer_id, quotation.location_id);
                }
            }
        })
        .catch(error => {
            console.error('Error loading quotation info:', error);
        });
}
```

#### Step 4: Handle ATTACHMENT SPK without contract
**NEW Function:**

```javascript
function showAttachmentContractWarning() {
    const attachmentSection = document.getElementById('attachmentTargetSection');
    if (attachmentSection) {
        attachmentSection.classList.remove('d-none');
        attachmentSection.innerHTML = `
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Contract Required for ATTACHMENT SPK</strong><br>
                <small>
                    ATTACHMENT SPK needs to select a target unit from existing contract units.<br>
                    Please create a contract for this quotation first, or use UNIT SPK type.
                </small>
            </div>
        `;
    }
}
```

#### Step 5: Update form submit to include quotation_id
**File:** Form submit handler

```javascript
// Ensure these hidden fields exist:
<input type="hidden" name="quotation_id" id="quotationIdInput">
<input type="hidden" name="kontrak_id" id="kontrakIdInput"> // Can be NULL
<input type="hidden" name="customer_id" id="customerIdInput">

// On quotation selection:
$('#quotationIdInput').val(quotation.id_quotation);
$('#customerIdInput').val(quotation.created_customer_id);
$('#kontrakIdInput').val(quotation.created_contract_id || ''); // NULL if no contract
```

---

## Testing Plan

### Test Case 1: SPK from Quotation WITHOUT Contract ✅
1. Create quotation with specifications
2. Mark as DEAL (creates customer only, no contract)
3. Go to SPK → Create SPK
4. Verify quotation appears with "⏳ Contract Pending"
5. Create UNIT SPK successfully
6. Verify in database: `spk.kontrak_id = NULL` and `spk.quotation_id = X`

### Test Case 2: SPK from Quotation WITH Contract ✅
1. Create quotation with specifications
2. Mark as DEAL
3. Create contract
4. Go to SPK → Create SPK
5. Verify quotation appears with "✅ Contract: K-xxx"
6. Create UNIT SPK successfully
7. Verify in database: `spk.kontrak_id = Y` and `spk.quotation_id = X`

### Test Case 3: ATTACHMENT SPK without Contract ⚠️
1. Create quotation with ATTACHMENT specs
2. Mark as DEAL (no contract)
3. Go to SPK → Select ATTACHMENT type
4. Should show warning: "Contract required for ATTACHMENT (need target unit)"
5. Cannot proceed without contract

### Test Case 4: ATTACHMENT SPK with Contract ✅
1. Create quotation with ATTACHMENT specs
2. Mark as DEAL → Create contract with units
3. Go to SPK → Select ATTACHMENT type
4. Select target unit from contract units
5. Create SPK successfully
6. Verify `spk.target_unit_id` is set

---

## Benefits of New Workflow

1. ✅ **Consistent with Quotation Module**: Same workflow across modules
2. ✅ **Flexible**: Contract optional, can be linked later
3. ✅ **Clear Source of Truth**: Quotation is primary source, not contract
4. ✅ **Better UX**: Shows contract status clearly (Pending vs Active)
5. ✅ **Database Alignment**: Matches database schema (kontrak_id nullable)
6. ✅ **Future-proof**: Supports non-rental scenarios

---

## Backward Compatibility

**Old SPK records:**
- Reference contract via `kontrak_id`
- May not have `quotation_id`
- Still work with existing code

**New SPK records:**
- Have `quotation_id` (source of truth)
- May have `kontrak_id` NULL (if no contract yet)
- Can link contract later via update

---

## Next Actions

1. ⬜ Complete remaining JavaScript updates (Step 1-5 above)
2. ⬜ Test all 4 test cases
3. ⬜ Update SPK controller to handle NULL kontrak_id
4. ⬜ Update SPK form validation (contract optional except for ATTACHMENT)
5. ⬜ User training documentation

---

**Implementation Status:** 60% Complete  
**Blocker:** Need to finish JavaScript event handler updates  
**ETA:** Complete within next session
