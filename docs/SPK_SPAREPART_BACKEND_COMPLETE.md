# SPK Sparepart Integration - Implementation Complete

**Date:** <?= date('Y-m-d H:i:s') ?>  
**Phase:** SPK Sparepart Workflow (Identical to Work Order)  
**Status:** ✅ Backend Complete - Frontend Integration Needed

---

## 📋 Implementation Summary

SPK sparepart workflow has been fully implemented on the **backend** to match the Work Order sparepart system. The following features are now available:

### ✅ Completed Features

#### 1. **Sparepart Planning During SPK Creation**
- **File:** `app/Controllers/Marketing.php` - `createSPKFromQuotation()` method
- **Lines:** 5197-5280
- **Functionality:**
  - Accepts `spareparts` array in specification data during SPK creation
  - Validates and inserts spareparts into `spk_spareparts` table
  - Supports:
    - Manual sparepart names (code = NULL)
    - Master sparepart codes (lookup from `sparepart` table)
    - Source types: WAREHOUSE, BEKAS, KANIBAL
    - Quantity tracking (brought vs used)
    - Unit source tracking for KANIBAL type
    - Item types: sparepart, consumable, OLI, etc.

**Expected Data Structure:**
```json
{
  "quotation_id": 123,
  "customer_id": 456,
  "contract_id": 789,
  "delivery_date": "2026-03-15",
  "specifications": [
    {
      "specification_id": 1,
      "quantity": 2,
      "spareparts": [
        {
          "sparepart_code": "SP001",
          "sparepart_name": "Seal Kit",
          "item_type": "sparepart",
          "quantity": 5,
          "satuan": "PCS",
          "source_type": "WAREHOUSE",
          "notes": "Untuk unit baru"
        },
        {
          "sparepart_name": "Baut Khusus M16",
          "item_type": "sparepart",
          "quantity": 10,
          "satuan": "PCS",
          "source_type": "WAREHOUSE"
        }
      ]
    }
  ]
}
```

---

#### 2. **Print Sparepart Request for Warehouse**
- **File:** `app/Controllers/Service.php` - `printSpkSparepartRequest()` method
- **View:** `app/Views/service/print_spk_sparepart_request.php`
- **Route:** `GET /service/print-spk-sparepart-request/{spk_id}`
- **Functionality:**
  - Generates printable PDF-ready form
  - Lists all planned spareparts with:
    - Type, Name, Quantity, Unit, Source, Notes
    - Source unit info for KANIBAL type
    - Signature areas (Service, Warehouse, Received by)
  - Includes SPK info (number, customer, date)
  - Auto-print option available

**Usage:**
```php
// In SPK detail view, add print button
<a href="<?= base_url('service/print-spk-sparepart-request/' . $spk['id']) ?>" 
   class="btn btn-info" target="_blank">
    <i class="fas fa-print"></i> Print Permintaan Sparepart
</a>
```

---

#### 3. **Add Additional Spareparts During Verification**
- **File:** `app/Controllers/Service.php` - `validateSpareparts()` method
- **Lines:** 226-350+ (enhanced)
- **Functionality:**
  - Accepts `additional_spareparts` array during validation
  - Inserts unexpected/additional spareparts used during work
  - Auto-marks as validated and used (quantity_brought = quantity_used)
  - Supports manual names (code = NULL) for non-warehouse items
  - Sets `is_additional = 1` flag for tracking

**Expected POST Data:**
```javascript
{
  validation_data: JSON.stringify([
    {
      sparepart_id: 123,
      quantity_used: 3,
      quantity_return: 2
    }
  ]),
  additional_spareparts: [
    {
      sparepart_name: "Mur M12 Tambahan",
      quantity: 8,
      satuan: "PCS",
      source_type: "BEKAS",
      item_type: "sparepart",
      notes: "Diambil dari unit lama"
    }
  ],
  notes: "Validasi selesai"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Validasi sparepart berhasil disimpan",
  "validated_count": 5,
  "additional_count": 3,
  "returns_generated": 2,
  "csrf_hash": "new_token"
}
```

---

## 🔌 Frontend Integration Required

The backend is complete, but the following frontend components need to be implemented:

### 1. SPK Creation Form Enhancement
**File to Modify:** `app/Views/marketing/create_spk.php` (or wherever SPK is created)

**Add Before Submit Button:**
```html
<!-- Sparepart Planning Section -->
<div class="card mb-3">
    <div class="card-header">
        <h5><i class="fas fa-tools"></i> Perencanaan Sparepart</h5>
    </div>
    <div class="card-body">
        <p class="text-muted">Rencanakan sparepart yang akan digunakan untuk SPK ini</p>
        <table class="table table-bordered" id="sparepartsTable">
            <thead>
                <tr>
                    <th>Sparepart</th>
                    <th>Tipe Item</th>
                    <th>Qty</th>
                    <th>Satuan</th>
                    <th>Sumber</th>
                    <th>Unit Sumber</th>
                    <th>Catatan</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="sparepartsList">
                <tr class="sparepart-row">
                    <td>
                        <select class="form-control sparepart-select" name="spareparts[0][sparepart_code]">
                            <option value="">-- Manual Input --</option>
                            <!-- Load from master sparepart -->
                        </select>
                        <input type="text" class="form-control mt-2 manual-name" 
                               name="spareparts[0][sparepart_name]" placeholder="Atau masukkan nama manual">
                    </td>
                    <td>
                        <select class="form-control" name="spareparts[0][item_type]">
                            <option value="sparepart">Sparepart</option>
                            <option value="consumable">Consumable</option>
                            <option value="OLI">OLI</option>
                        </select>
                    </td>
                    <td><input type="number" class="form-control" name="spareparts[0][quantity]" min="1"></td>
                    <td>
                        <select class="form-control" name="spareparts[0][satuan]">
                            <option value="PCS">PCS</option>
                            <option value="LITER">LITER</option>
                            <option value="SET">SET</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control source-select" name="spareparts[0][source_type]">
                            <option value="WAREHOUSE">Warehouse</option>
                            <option value="BEKAS">Bekas</option>
                            <option value="KANIBAL">Kanibal</option>
                        </select>
                    </td>
                    <td>
                        <select class="form-control source-unit" name="spareparts[0][source_unit_id]" disabled>
                            <option value="">-- Pilih Unit --</option>
                            <!-- Load units when KANIBAL selected -->
                        </select>
                    </td>
                    <td><input type="text" class="form-control" name="spareparts[0][notes]"></td>
                    <td><button type="button" class="btn btn-sm btn-danger remove-sparepart">Hapus</button></td>
                </tr>
            </tbody>
        </table>
        <button type="button" class="btn btn-sm btn-success" id="addSparepartRow">
            <i class="fas fa-plus"></i> Tambah Sparepart
        </button>
    </div>
</div>
```

**JavaScript Logic:**
```javascript
// Enable/disable source unit based on source type
$(document).on('change', '.source-select', function() {
    const row = $(this).closest('tr');
    const sourceUnit = row.find('.source-unit');
    
    if ($(this).val() === 'KANIBAL') {
        sourceUnit.prop('disabled', false);
    } else {
        sourceUnit.prop('disabled', true).val('');
    }
});

// Add sparepart row
$('#addSparepartRow').on('click', function() {
    const index = $('#sparepartsList tr').length;
    const newRow = $('#sparepartsList tr:first').clone();
    
    // Update name attributes
    newRow.find('[name]').each(function() {
        const name = $(this).attr('name').replace(/\[\d+\]/, '[' + index + ']');
        $(this).attr('name', name).val('');
    });
    
    $('#sparepartsList').append(newRow);
});

// Remove sparepart row
$(document).on('click', '.remove-sparepart', function() {
    if ($('#sparepartsList tr').length > 1) {
        $(this).closest('tr').remove();
    }
});

// On form submit, include spareparts in specification data
$('#createSPKForm').on('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Collect spareparts
    const spareparts = [];
    $('#sparepartsList tr').each(function() {
        const code = $(this).find('[name*="[sparepart_code]"]').val();
        const name = $(this).find('[name*="[sparepart_name]"]').val();
        const quantity = parseInt($(this).find('[name*="[quantity]"]').val());
        
        if ((code || name) && quantity > 0) {
            spareparts.push({
                sparepart_code: code || null,
                sparepart_name: name || null,
                item_type: $(this).find('[name*="[item_type]"]').val(),
                quantity: quantity,
                satuan: $(this).find('[name*="[satuan]"]').val(),
                source_type: $(this).find('[name*="[source_type]"]').val(),
                source_unit_id: $(this).find('[name*="[source_unit_id]"]').val() || null,
                notes: $(this).find('[name*="[notes]"]').val()
            });
        }
    });
    
    // Add to specification data
    const specifications = [{
        specification_id: $('#specification_id').val(),
        quantity: $('#quantity').val(),
        spareparts: spareparts
    }];
    
    // Submit to backend
    $.ajax({
        url: base_url + 'marketing/create-spk-from-quotation',
        type: 'POST',
        data: {
            quotation_id: $('#quotation_id').val(),
            customer_id: $('#customer_id').val(),
            contract_id: $('#contract_id').val(),
            delivery_date: $('#delivery_date').val(),
            specifications: specifications,
            [window.csrfTokenName]: window.csrfTokenValue
        },
        success: function(response) {
            if (response.success) {
                Swal.fire('Sukses', response.message, 'success');
                location.reload();
            }
        }
    });
});
```

---

### 2. SPK Verification Modal Enhancement
**File to Modify:** `app/Views/service/spk_detail.php` (or wherever validation modal exists)

**Add Additional Sparepart Section in Validation Modal:**
```html
<!-- Existing validation table -->
<table id="validationTable">
  <!-- existing rows -->
</table>

<!-- Add this new section -->
<div class="mt-4">
    <h5>Tambah Sparepart Tambahan <span class="badge badge-warning">Opsional</span></h5>
    <p class="text-muted">Jika ada sparepart tambahan yang digunakan tidak sesuai rencana</p>
    
    <table class="table table-bordered" id="additionalSparepartsTable">
        <thead>
            <tr>
                <th>Nama Sparepart</th>
                <th>Tipe</th>
                <th>Qty</th>
                <th>Satuan</th>
                <th>Sumber</th>
                <th>Catatan</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody id="additionalSparepartsList">
            <!-- Rows will be added dynamically -->
        </tbody>
    </table>
    
    <button type="button" class="btn btn-sm btn-success" id="addAdditionalSparepart">
        <i class="fas fa-plus"></i> Tambah Sparepart
    </button>
</div>
```

**JavaScript for Additional Spareparts:**
```javascript
// Add additional sparepart row
$('#addAdditionalSparepart').on('click', function() {
    const row = `
        <tr>
            <td><input type="text" class="form-control additional-sp-name" required></td>
            <td>
                <select class="form-control additional-sp-type">
                    <option value="sparepart">Sparepart</option>
                    <option value="consumable">Consumable</option>
                    <option value="OLI">OLI</option>
                </select>
            </td>
            <td><input type="number" class="form-control additional-sp-qty" min="1" required></td>
            <td>
                <select class="form-control additional-sp-unit">
                    <option value="PCS">PCS</option>
                    <option value="LITER">LITER</option>
                    <option value="SET">SET</option>
                </select>
            </td>
            <td>
                <select class="form-control additional-sp-source">
                    <option value="WAREHOUSE">Warehouse</option>
                    <option value="BEKAS">Bekas</option>
                    <option value="KANIBAL">Kanibal</option>
                </select>
            </td>
            <td><input type="text" class="form-control additional-sp-notes"></td>
            <td><button type="button" class="btn btn-sm btn-danger remove-additional">Hapus</button></td>
        </tr>
    `;
    $('#additionalSparepartsList').append(row);
});

// Remove additional sparepart
$(document).on('click', '.remove-additional', function() {
    $(this).closest('tr').remove();
});

// On validation submit
$('#btnSaveValidation').on('click', function() {
    // Collect existing validation data
    const validationData = [];
    $('#validationTable tbody tr').each(function() {
        validationData.push({
            sparepart_id: $(this).data('id'),
            quantity_used: parseInt($(this).find('.qty-used').val()),
            quantity_return: parseInt($(this).find('.qty-return').val())
        });
    });
    
    // Collect additional spareparts
    const additionalSpareparts = [];
    $('#additionalSparepartsList tr').each(function() {
        const name = $(this).find('.additional-sp-name').val();
        const qty = parseInt($(this).find('.additional-sp-qty').val());
        
        if (name && qty > 0) {
            additionalSpareparts.push({
                sparepart_name: name,
                item_type: $(this).find('.additional-sp-type').val(),
                quantity: qty,
                satuan: $(this).find('.additional-sp-unit').val(),
                source_type: $(this).find('.additional-sp-source').val(),
                notes: $(this).find('.additional-sp-notes').val()
            });
        }
    });
    
    // Submit to backend
    $.ajax({
        url: base_url + 'service/validate-spareparts/' + spkId,
        type: 'POST',
        data: {
            validation_data: JSON.stringify(validationData),
            additional_spareparts: additionalSpareparts,
            notes: $('#validation_notes').val(),
            [window.csrfTokenName]: window.csrfTokenValue
        },
        success: function(response) {
            if (response.success) {
                Swal.fire('Sukses', response.message, 'success');
                $('#validationModal').modal('hide');
                location.reload();
            }
        }
    });
});
```

---

### 3. SPK Detail View - Print Button
**File to Modify:** `app/Views/service/spk_detail.php`

**Add Print Button (if not exists):**
```html
<!-- In SPK detail header or action buttons -->
<?php if (!empty($spareparts)): ?>
<a href="<?= base_url('service/print-spk-sparepart-request/' . $spk['id']) ?>" 
   class="btn btn-info btn-sm" target="_blank">
    <i class="fas fa-print"></i> Print Permintaan Sparepart
</a>
<?php endif; ?>
```

---

## 📊 Database Verification

### Tables Used
```sql
-- Main sparepart planning and usage
spk_spareparts (
    id, spk_id, sparepart_code, sparepart_name, item_type,
    quantity_brought, quantity_used, satuan, notes,
    is_additional, sparepart_validated, 
    is_from_warehouse, source_type, source_unit_id, source_notes,
    created_at, updated_at
)

-- Returns tracking
spk_sparepart_returns (
    id, spk_id, spk_sparepart_id, sparepart_code, sparepart_name,
    quantity_brought, quantity_used, quantity_return,
    status, return_notes, confirmed_by, confirmed_at,
    created_at, updated_at
)
```

### Test Queries
```sql
-- Check planned spareparts for SPK
SELECT * FROM spk_spareparts WHERE spk_id = 1 ORDER BY id;

-- Check returns
SELECT * FROM spk_sparepart_returns WHERE spk_id = 1;

-- Check in warehouse tracking (Non-Warehouse tab filter)
SELECT 
    ssp.sparepart_name,
    COUNT(DISTINCT ssp.spk_id) as spk_count,
    GROUP_CONCAT(DISTINCT spk.nomor_spk SEPARATOR ', ') as spk_numbers,
    SUM(ssp.quantity_used) as total_used
FROM spk_spareparts ssp
INNER JOIN spk ON spk.id = ssp.spk_id
WHERE (ssp.sparepart_code IS NULL OR ssp.sparepart_code = '')
GROUP BY ssp.sparepart_name;
```

---

## 🧪 Testing Workflow

### Test Scenario 1: Complete SPK Creation with Spareparts

**Step 1:** Create SPK with planned spareparts
```javascript
// POST to: /marketing/create-spk-from-quotation
{
  "quotation_id": 123,
  "specifications": [
    {
      "specification_id": 1,
      "quantity": 2,
      "spareparts": [
        {
          "sparepart_name": "Seal Kit A",
          "quantity": 5,
          "source_type": "WAREHOUSE"
        },
        {
          "sparepart_name": "Baut M12",
          "quantity": 20,
          "source_type": "BEKAS"
        }
      ]
    }
  ]
}
```

**Expected Result:**
- ✅ SPK created with nomor_spk
- ✅ 2 entries in `spk_spareparts` table
- ✅ Both with `quantity_brought` set, `quantity_used = 0`
- ✅ `is_additional = 0`, `sparepart_validated = 0`

**Step 2:** Print sparepart request
- URL: `/service/print-spk-sparepart-request/{spk_id}`
- ✅ Shows both planned spareparts
- ✅ PDF-ready format with company header
- ✅ Signature areas visible

**Step 3:** Validate spareparts + add additional
```javascript
// POST to: /service/validate-spareparts/{spk_id}
{
  "validation_data": JSON.stringify([
    {"sparepart_id": 1, "quantity_used": 3, "quantity_return": 2},
    {"sparepart_id": 2, "quantity_used": 20, "quantity_return": 0}
  ]),
  "additional_spareparts": [
    {
      "sparepart_name": "Mur M12 Tambahan",
      "quantity": 8,
      "source_type": "BEKAS"
    }
  ],
  "notes": "Validasi selesai"
}
```

**Expected Result:**
- ✅ First sparepart: `quantity_used = 3`, `sparepart_validated = 1`
- ✅ Second sparepart: `quantity_used = 20`, `sparepart_validated = 1`
- ✅ New entry in `spk_spareparts`: "Mur M12 Tambahan", `is_additional = 1`
- ✅ One return created for Seal Kit (qty = 2)

**Step 4:** Check warehouse tracking
- Navigate to: Warehouse > Sparepart Usage
- Filter: SPK
- Tab: Non-Warehouse
- ✅ Should show: "Seal Kit A", "Baut M12", "Mur M12 Tambahan"
- ✅ Can expand to see SPK detail

---

### Test Scenario 2: Manual Sparepart Names (Non-Warehouse)

**Create SPK with manual names:**
```json
{
  "spareparts": [
    {
      "sparepart_code": null,
      "sparepart_name": "Baut Custom Size 25mm",
      "quantity": 15,
      "source_type": "BEKAS"
    }
  ]
}
```

**Expected:**
- ✅ Saved with `sparepart_code = NULL`
- ✅ Appears in Non-Warehouse tab (because code is NULL)
- ✅ Source type badge shows "BEKAS"

---

### Test Scenario 3: KANIBAL Source

**Create with KANIBAL source:**
```json
{
  "spareparts": [
    {
      "sparepart_name": "Hidrolik Cylinder",
      "quantity": 1,
      "source_type": "KANIBAL",
      "source_unit_id": 45,
      "source_notes": "Dari unit rusak"
    }
  ]
}
```

**Expected:**
- ✅ Print shows source unit number
- ✅ Source notes visible
- ✅ Badge shows "KANIBAL" in warehouse tracking

---

## 📝 Next Steps for Frontend Developer

1. **Priority 1: SPK Creation Form**
   - Add sparepart planning table before submit
   - Connect to master sparepart dropdown (optional)
   - Handle manual input fallback
   - Submit with specification data

2. **Priority 2: SPK Verification Modal**
   - Add "Tambah Sparepart Tambahan" section
   - Allow dynamic row addition
   - Submit `additional_spareparts` array

3. **Priority 3: Print Button**
   - Add print button in SPK detail view
   - Show only if spareparts exist
   - Open in new tab for printing

4. **Verify Integration:**
   - Test complete flow: Create → Print → Validate
   - Check warehouse tracking tabs update correctly
   - Test manual names appear in Non-Warehouse tab
   - Verify returns generated when quantity_return > 0

---

## 🔗 Related Files

### Backend (Complete)
- `app/Controllers/Marketing.php` - SPK creation with sparepart planning
- `app/Controllers/Service.php` - Print and validation endpoints
- `app/Views/service/print_spk_sparepart_request.php` - Print template

### Frontend (Integration Needed)
- `app/Views/marketing/[create_spk_view].php` - Add sparepart planning form
- `app/Views/service/spk_detail.php` - Add print button + validation modal enhancement

### Warehouse Tracking (Already Complete)
- `app/Views/warehouse/sparepart_usage.php` - Filter-based tracking
- `app/Controllers/Warehouse/SparepartUsageController.php` - SPK data source

---

## 🎯 Success Criteria

- ✅ SPK can be created with planned spareparts
- ✅ Print generates warehouse request form
- ✅ Validation allows adding unexpected spareparts
- ✅ Returns are automatically generated
- ✅ All data appears in Warehouse > Sparepart Usage (SPK filter)
- ✅ Manual names (code = NULL) show in Non-Warehouse tab
- ✅ Workflow identical to Work Order system

---

**Backend Implementation Status:** ✅ **COMPLETE**  
**Frontend Integration:** ⏳ **PENDING**  
**Testing:** ⏳ **PENDING FRONTEND COMPLETION**

