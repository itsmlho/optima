# Spare Unit Implementation Guide
**Feature**: Unit Cadangan (Spare Units) - No Billing
**Date**: 2026-02-15
**Status**: ✅ COMPLETE

---

## 📋 Overview

Fitur ini memungkinkan marketing untuk menandai unit sebagai "spare unit" (unit cadangan) yang **tidak akan ditagih** ke customer. Unit spare tetap tercatat dalam kontrak untuk tracking maintenance/lokasi, tapi tidak muncul dalam invoice.

---

## ✅ Implementation Checklist

### 1. Database Layer ✅
**Table**: `quotation_specifications`

**Column Added**:
```sql
ALTER TABLE quotation_specifications
ADD COLUMN is_spare_unit TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Flag for spare/backup units (0=billed, 1=not billed)' 
AFTER quantity;
```

**Index Created**:
```sql
CREATE INDEX idx_spare_unit ON quotation_specifications(is_spare_unit);
```

**Status**: ✅ Column already exists (verified)

---

### 2. UI Layer ✅
**File**: `app/Views/marketing/quotations.php`

**Changes Made**:

#### A. Add Specification Form (Lines ~353-368)
Added checkbox after quantity field:

```html
<!-- Spare Unit Checkbox -->
<div class="col-12">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_spare_unit" id="isSpareUnit" value="1">
        <label class="form-check-label fw-bold text-warning" for="isSpareUnit">
            <i class="fas fa-box-open me-1"></i> Unit Cadangan (Spare Unit - Tidak Ditagih)
        </label>
        <small class="text-muted d-block ms-4">
            Unit backup/cadangan untuk customer, tidak dihitung dalam tagihan bulanan
        </small>
    </div>
</div>
```

#### B. Spare Unit Toggle Handler (Lines ~1249-1284)
JavaScript to auto-disable price fields when spare unit is checked:

```javascript
$(document).on('change', '#isSpareUnit', function() {
    const isChecked = $(this).is(':checked');
    const monthlyPriceField = $('#monthlyPrice');
    const dailyPriceField = $('#dailyPrice');
    
    if (isChecked) {
        // Spare unit - disable prices, set to 0
        monthlyPriceField.val('0').prop('disabled', true).addClass('bg-light');
        dailyPriceField.val('0').prop('disabled', true).addClass('bg-light');
        $('#monthlyPriceRequired, #dailyPriceRequired').hide();
        
        // Show info message
        monthlyPriceField.closest('.col-md-6').append(
            '<small id="spareUnitInfo" class="text-success d-block mt-1">' +
            '<i class="fas fa-check-circle"></i> Spare unit - tidak akan ditagih' +
            '</small>'
        );
    } else {
        // Normal unit - enable prices
        monthlyPriceField.val('').prop('disabled', false).removeClass('bg-light');
        dailyPriceField.val('').prop('disabled', false).removeClass('bg-light');
        $('#monthlyPriceRequired, #dailyPriceRequired').show();
        $('#spareUnitInfo').remove();
    }
});
```

#### C. Form Submit Validation Update (Lines ~2898-2908)
Skip price validation for spare units:

```javascript
// Check if spare unit is selected
const isSpareUnit = $('#isSpareUnit').is(':checked');

// Validate: at least one price must be filled (SKIP for spare units)
if (!isSpareUnit && monthlyPrice === 0 && dailyPrice === 0) {
    Swal.fire('Validation Error', 'Please fill in at least one price field...', 'warning');
    return;
}

// Ensure spare unit value is sent
if (!isSpareUnit) {
    formData.append('is_spare_unit', '0');
}
```

#### D. Form Reset Handler (Lines ~2510-2515)
Reset spare unit checkbox when opening add modal:

```javascript
// Reset spare unit checkbox and enable price fields
$('#isSpareUnit').prop('checked', false).trigger('change');
```

#### E. Specification Display View (Lines ~2354-2374)
Show SPARE UNIT badge and NO CHARGE indicator:

```javascript
// Quantity and Spare Unit Badge
const quantityBadge = spec.is_spare_unit == 1 
    ? `<div class="col-md-4">
         <small class="text-muted">Quantity</small>
         <div class="fw-bold text-primary">
           ${spec.quantity || 0} unit(s) 
           <span class="badge bg-warning text-dark ms-2">
             <i class="fas fa-box-open"></i> SPARE UNIT
           </span>
         </div>
       </div>`
    : `<div class="col-md-3">...normal quantity display...</div>`;

details.push(quantityBadge);

if (spec.is_spare_unit != 1) {
    // Show prices for normal units
    if (monthlyPrice > 0) { details.push(...); }
    if (dailyPrice > 0) { details.push(...); }
    details.push(`Total Price: Rp ${formatNumber(totalPrice)}`);
} else {
    // Spare unit - show NO CHARGE indicator
    details.push(`<div class="col-md-6">
        <small class="text-muted">Billing Status</small>
        <div class="fw-bold text-warning">
            <i class="fas fa-gift me-1"></i>TIDAK DITAGIH (No Charge)
        </div>
    </div>`);
}
```

**Status**: ✅ All UI changes complete

---

### 3. Controller Layer ✅
**File**: `app/Controllers/Quotation.php`

**Changes Made**:

#### A. addSpecification() Method (Lines ~648-677)
Capture spare unit flag and set prices to 0:

```php
$data = [
    'id_quotation' => $quotationId,
    'specification_name' => $this->request->getPost('specification_name'),
    'quantity' => (int)$this->request->getPost('quantity'),
    'is_spare_unit' => (int)$this->request->getPost('is_spare_unit') ?: 0,
    'monthly_price' => (float)$this->request->getPost('unit_price'),
    'daily_price' => (float)$this->request->getPost('harga_per_unit_harian'),
    // ... other fields ...
];

// If spare unit, set prices to 0 (no billing)
if ($data['is_spare_unit'] == 1) {
    $data['monthly_price'] = 0;
    $data['daily_price'] = 0;
}

// Calculate total price (spare units will have 0 total)
$data['total_price'] = ($data['quantity'] * $data['monthly_price']) + $data['daily_price'];
```

#### B. updateSpecification() Method (Lines ~769-792)
Handle spare unit flag during updates:

```php
// Handle spare unit flag - if spare, set prices to 0
$isSpareUnit = isset($data['is_spare_unit']) 
    ? (int)$data['is_spare_unit'] 
    : $specification['is_spare_unit'];

if ($isSpareUnit == 1) {
    $data['monthly_price'] = 0;
    $data['daily_price'] = 0;
}

// Calculate total price (spare units will have 0 total)
if (isset($data['quantity']) || isset($data['monthly_price']) || isset($data['daily_price'])) {
    $qty = $data['quantity'] ?? $specification['quantity'];
    $monthlyPrice = $data['monthly_price'] ?? $specification['monthly_price'];
    $dailyPrice = $data['daily_price'] ?? $specification['daily_price'];
    $data['total_price'] = ($qty * $monthlyPrice) + $dailyPrice;
}
```

**Status**: ✅ Controller changes complete

---

### 4. Invoice Generation Layer ✅
**File**: `app/Models/InvoiceItemModel.php`

**Changes Made**:

#### Method: addItemsFromContract() (Lines ~105-154)
Modified to **exclude spare units** from invoice items:

```php
public function addItemsFromContract(int $contractId, ?float $amendedRate = null): int
{
    // ... find invoice ...
    
    // Use quotation_specifications table directly
    $db = \Config\Database::connect();
    $builder = $db->table('quotation_specifications');
    
    // Get specs for this contract, EXCLUDING spare units
    $specs = $builder->where('kontrak_id', $contractId)
                     ->where('is_spare_unit !=', 1)  // ✅ CRITICAL: Skip spare units
                     ->where('is_active', 1)
                     ->get()
                     ->getResultArray();
    
    $itemCount = 0;
    
    foreach ($specs as $spec) {
        $description = "Rental - {$spec['specification_name']}";
        
        $itemData = [
            'invoice_id' => $invoiceId,
            'item_type' => 'UNIT_RENTAL',
            'description' => $description,
            'quantity' => $spec['quantity'] ?? 1,
            'unit_price' => $amendedRate ?? $spec['monthly_price'] ?? 0,
            // ... other fields ...
        ];
        
        if ($this->insert($itemData)) {
            $itemCount++;
        }
    }
    
    return $itemCount;
}
```

**Critical Logic**:
- Query filters: `->where('is_spare_unit !=', 1)`
- Result: Spare units are **never added to invoices**
- Billing status: **0 Rupiah for spare units**

**Status**: ✅ Invoice generation updated

---

## 🎯 Business Logic Summary

### Spare Unit Behavior

| **Aspect** | **Normal Unit** | **Spare Unit** |
|------------|-----------------|----------------|
| Quotation Specification | ✅ Price required | ✅ Price auto-set to Rp 0 |
| Contract Creation | ✅ Copied with prices | ✅ Copied with Rp 0 |
| Invoice Generation | ✅ Added to invoice items | ❌ **SKIPPED** (not billed) |
| Contract Display | Shows unit price/total | Shows "TIDAK DITAGIH" badge |
| Inventory Tracking | ✅ Tracked for maintenance | ✅ Tracked for maintenance |
| SPK/Delivery | ✅ Normal workflow | ✅ Normal workflow (unit still delivered) |

### Use Cases

1. **Customer Loyalty Bonus**:
   - "Beli 10 unit, dapat 1 unit spare gratis"
   - Spare unit tracked for maintenance tapi tidak ditagih

2. **Emergency Backup Units**:
   - Unit cadangan di lokasi customer untuk downtime prevention
   - Customer tidak bayar rental sampai unit dipakai

3. **Trial/Demo Units**:
   - Unit demo di customer site
   - Tidak ditagih selama masa trial

4. **Replacement Units**:
   - Unit pengganti sementara untuk unit yang maintenance
   - Tidak ada double billing

---

## 📊 Visual Indicators

### Quotation Form
```
┌─────────────────────────────────────────────────┐
│ ☑️ Unit Cadangan (Spare Unit - Tidak Ditagih)  │
│ Unit backup/cadangan untuk customer,           │
│ tidak dihitung dalam tagihan bulanan            │
└─────────────────────────────────────────────────┘

Monthly Rental Price: Rp 0 [DISABLED] ✅ Spare unit - tidak akan ditagih
Daily Rental Price:   Rp 0 [DISABLED]
```

### Specification Display
```
┌──────────────────────────────────────────────────────────────┐
│ Forklift Electric 2.5T - Hangcha                           │
│                                                             │
│ Quantity: 2 unit(s) [⚠️ SPARE UNIT]                         │
│                                                             │
│ Billing Status: [🎁 TIDAK DITAGIH (No Charge)]              │
└──────────────────────────────────────────────────────────────┘
```

### Invoice Items
```
NORMAL UNIT:
✅ Rental - Forklift Electric 2.5T x 10 units @ Rp 8,500,000 = Rp 85,000,000

SPARE UNIT:
❌ (Not included in invoice at all - skipped during generation)
```

---

## 🧪 Testing Scenarios

### Test Case 1: Create Quotation with Spare Unit
1. Open Add Specification modal
2. Fill in unit details (type, brand, capacity)
3. ☑️ Check "Unit Cadangan (Spare Unit)"
4. ✅ Verify: Price fields disabled, hint text shown
5. Save specification
6. ✅ Verify: Specification shows "SPARE UNIT" badge
7. ✅ Verify: Total price shows "TIDAK DITAGIH"

### Test Case 2: Mixed Quotation (Normal + Spare)
1. Add 10 units with Rp 8,500,000/month (normal)
2. Add 1 unit with spare unit flag (spare)
3. ✅ Verify: Quotation total = Rp 85,000,000 (only normal units)
4. Create contract from quotation
5. ✅ Verify: Contract shows 11 total units (10 billed + 1 spare)

### Test Case 3: Invoice Generation
1. Create contract with 5 normal + 1 spare unit
2. Generate monthly invoice
3. ✅ Verify: Invoice has 5 line items (spare unit excluded)
4. ✅ Verify: Invoice total covers only 5 units
5. ✅ Verify: Spare unit still tracked in contract specs

### Test Case 4: Edit Spare Unit Flag
1. Open existing specification
2. Uncheck "Unit Cadangan"
3. ✅ Verify: Price fields enabled, must fill required price
4. Save with Rp 7,000,000
5. ✅ Verify: Unit now shows normal price, no longer spare

---

## 🔧 Important Notes

### Database Constraints
- Column default: `0` (not spare)
- Data type: `TINYINT(1)` (boolean)
- Index created for query optimization

### Price Validation
- **Normal units**: Require at least monthly OR daily price
- **Spare units**: Prices forced to `0`, validation skipped

### Invoice Logic
- Query filter: `WHERE is_spare_unit != 1`
- Spare units **never** added to recurring invoices
- SPK/delivery unaffected (unit still delivered)

### Display Logic
- JavaScript checks: `spec.is_spare_unit == 1`
- Badge color: `bg-warning` (yellow) for visibility
- Icon: `fa-box-open` (box icon)

---

## 📝 Migration Files

1. **add_spare_unit_column.sql**:
   - Location: `databases/migrations/add_spare_unit_column.sql`
   - Status: ✅ Executed (column exists)
   - Result: `is_spare_unit TINYINT(1) DEFAULT 0`

---

## 📚 Related Documentation

- [Contract Workflow](CONTRACT_PO_RENEWAL_WORKFLOW.md)
- [Quotation System](MARKETING_WORKFLOW_VERIFICATION.md)
- [Invoice Generation](DATABASE_SCHEMA.md#invoices)

---

## ✨ Next Steps (Optional Enhancements)

1. **Reporting**:
   - Add "Spare Units" section to contract reports
   - Track spare unit utilization (how often used as replacement)

2. **Activation Workflow**:
   - Add "Activate Spare Unit" button to convert spare → normal
   - Auto-generate amendment when spare unit activated

3. **Notifications**:
   - Alert customer when spare unit is delivered
   - Monthly report showing spare unit inventory

4. **Inventory Tracking**:
   - Separate spare unit stock in inventory
   - Track spare unit deployment dates

---

**Implementation Summary**: ✅ COMPLETE  
**Files Modified**: 4 files  
**Database Changes**: 1 column + 1 index  
**Testing Status**: Ready for QA

**Author**: GitHub Copilot  
**Date**: February 15, 2026
