# Unit Verification Backend Implementation Guide

## ✅ Status Completed:
1. ✅ Database table `unit_verification_history` created
2. ✅ Modal position fixed (margin-top: 2rem)
3. ✅ Pelanggan field readonly
4. ✅ Lokasi dropdown exists
5. ✅ Tinggi Mast field exists (readonly, auto-populated)
6. ✅ Hour Meter field exists

## 🔧 Backend Endpoints to Add

### 1. GET MAST HEIGHT ENDPOINT

**Purpose:** Return `tinggi_mast` when Model Mast is selected

**File:** `app/Controllers/WorkOrderController.php`

**Add this method:**

```php
/**
 * Get mast height by mast model ID
 * AJAX endpoint called when Model Mast dropdown changes
 */
public function getMastHeight()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $mastId = $this->request->getPost('mast_id');
    
    if (!$mastId) {
        return $this->response->setJSON(['success' => false, 'message' => 'Mast ID required']);
    }

    try {
        $db = \Config\Database::connect();
        
        // Get tinggi_mast from tipe_mast table
        $mast = $db->table('tipe_mast')
            ->select('tinggi_mast')
            ->where('id_mast', $mastId)
            ->get()
            ->getRowArray();
        
        if (!$mast) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Mast type not found'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'tinggi_mast' => $mast['tinggi_mast'] ?? ''
            ]
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error getting mast height: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
```

**Add route in:** `app/Config/Routes.php`

```php
$routes->post('service/work-orders/get-mast-height', 'WorkOrderController::getMastHeight');
```

---

### 2. GET CUSTOMER LOCATIONS ENDPOINT

**Purpose:** Return customer locations for Lokasi dropdown logic

**Modify:** `getUnitVerificationData()` method to include customer locations

**In:** `app/Controllers/WorkOrderController.php`, around line 2900+

**Add this code AFTER getting `$customerData` (around line 2660):**

```php
// Get ALL customer locations for this unit's customer (for Lokasi dropdown)
$customerLocations = [];
if (!empty($customerData['pelanggan'])) {
    // Get customer_id first
    $customerId = $db->query("
        SELECT c.id 
        FROM customers c
        WHERE c.customer_name = ?
        LIMIT 1
    ", [$customerData['pelanggan']])->getRowArray();
    
    if ($customerId) {
        $customerLocations = $db->table('customer_locations')
            ->select('id, location_name')
            ->where('customer_id', $customerId['id'])
            ->orderBy('location_name', 'ASC')
            ->get()
            ->getResultArray();
    }
}

// If no customer locations (Mills unit), provide POS options
if (empty($customerLocations)) {
    $customerLocations = [
        ['id' => 'POS_1', 'location_name' => 'POS 1'],
        ['id' => 'POS_2', 'location_name' => 'POS 2'],
        ['id' => 'POS_3', 'location_name' => 'POS 3'],
        ['id' => 'POS_4', 'location_name' => 'POS 4'],
        ['id' => 'POS_5', 'location_name' => 'POS 5'],
        ['id' => 'MANUAL', 'location_name' => 'Manual'],
    ];
}
```

**Then in the final return statement (around line 2900+), add:**

```php
return $this->response->setJSON([
    'success' => true,
    'data' => [
        'work_order' => $workOrder,
        'unit' => $unit,
        'attachment' => $attachment,
        'customer_locations' => $customerLocations, // ← ADD THIS
        'dropdown_options' => [
            'departemen' => $departemenOptions,
            'tipe_unit' => $tipeUnitOptions,
            'model_unit' => $modelUnitOptions,
            'kapasitas' => $kapasitasOptions,
            'model_mast' => $modelMastOptions,
            'model_mesin' => $modelMesinOptions,
            'roda' => $rodaOptions,
            'ban' => $banOptions,
            'valve' => $valveOptions
        ],
        'accessories' => $accessories
    ]
]);
```

---

### 3. GET VERIFICATION HISTORY ENDPOINT

**Purpose:** Get last verification for this unit to show history banner

**Add this method:**

```php
/**
 * Get last verification history for a unit
 * Shows when unit was last verified and by whom
 */
public function getVerificationHistory()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Invalid request']);
    }

    $unitId = $this->request->getPost('unit_id');
    
    if (!$unitId) {
        return $this->response->setJSON(['success' => false, 'message' => 'Unit ID required']);
    }

    try {
        $db = \Config\Database::connect();
        
        // Get most recent verification for this unit
        $history = $db->query("
            SELECT 
                uvh.verified_at,
                uvh.work_order_id,
                wo.wo_number,
                e.staff_name as mechanic_name
            FROM unit_verification_history uvh
            JOIN work_orders wo ON uvh.work_order_id = wo.id
            JOIN employees e ON uvh.verified_by = e.id
            WHERE uvh.unit_id = ?
            ORDER BY uvh.verified_at DESC
            LIMIT 1
        ", [$unitId])->getRowArray();
        
        if (!$history) {
            return $this->response->setJSON([
                'success' => true,
                'has_history' => false,
                'message' => 'Unit belum pernah diverifikasi'
            ]);
        }

        return $this->response->setJSON([
            'success' => true,
            'has_history' => true,
            'data' => $history
        ]);
    } catch (\Exception $e) {
        log_message('error', 'Error getting verification history: ' . $e->getMessage());
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
```

**Add route:**

```php
$routes->post('service/work-orders/get-verification-history', 'WorkOrderController::getVerificationHistory');
```

---

### 4. SAVE VERIFICATION HISTORY

**Purpose:** Save verification data to history table when user saves

**Modify:** `saveUnitVerification()` method (around line 3084)

**Add THIS CODE at the END of the method, BEFORE the final `return` statement:**

```php
// Save to verification history table
try {
    // Get mechanic ID from work order
    $workOrder = $this->workOrderModel->find($workOrderId);
    $mechanicId = $workOrder['mechanic_id'] ?? session()->get('user_id');
    
    // Prepare verification data as JSON
    $verificationData = [
        'no_unit' => $this->request->getPost('no_unit'),
        'pelanggan' => $this->request->getPost('pelanggan'),
        'lokasi' => $this->request->getPost('lokasi'),
        'serial_number' => $this->request->getPost('serial_number'),
        'tahun_unit' => $this->request->getPost('tahun_unit'),
        'departemen_id' => $this->request->getPost('departemen_id'),
        'tipe_unit_id' => $this->request->getPost('tipe_unit_id'),
        'kapasitas_unit_id' => $this->request->getPost('kapasitas_unit_id'),
        'model_unit_id' => $this->request->getPost('model_unit_id'),
        'model_mesin_id' => $this->request->getPost('model_mesin_id'),
        'sn_mesin' => $this->request->getPost('sn_mesin'),
        'model_mast_id' => $this->request->getPost('model_mast_id'),
        'sn_mast' => $this->request->getPost('sn_mast'),
        'tinggi_mast' => $this->request->getPost('tinggi_mast'),
        'keterangan' => $this->request->getPost('keterangan'),
        'hour_meter' => $this->request->getPost('hour_meter'),
        // Add attachment data if exists
        'attachment_id' => $this->request->getPost('attachment_id'),
        'sn_attachment' => $this->request->getPost('sn_attachment'),
    ];
    
    // Insert into verification history
    $db->table('unit_verification_history')->insert([
        'unit_id' => $unitId,
        'work_order_id' => $workOrderId,
        'verified_by' => $mechanicId,
        'verified_at' => date('Y-m-d H:i:s'),
        'verification_data' => json_encode($verificationData)
    ]);
    
    log_message('info', "Verification history saved for Unit ID: {$unitId}, WO ID: {$workOrderId}");
} catch (\Exception $e) {
    // Log error but don't fail the verification save
    log_message('error', 'Failed to save verification history: ' . $e->getMessage());
}
```

---

## 📝 Frontend JavaScript Updates

**File:** `app/Views/service/unit_verification.php`

### 4.1 Load Verification History when Modal Opens

**Find:** The `window.loadUnitVerificationData` function (around line 640)

**ADD THIS CODE after setting WO number and before the main AJAX call:**

```javascript
// Load verification history first
$.ajax({
    url: '<?= base_url('service/work-orders/get-verification-history') ?>',
    type: 'POST',
    data: { 
        unit_id: woData.unitId, // You need to pass unit ID
        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
    },
    success: function(historyResponse) {
        if (historyResponse.success && historyResponse.has_history) {
            let hist = historyResponse.data;
            let date = new Date(hist.verified_at).toLocaleString('id-ID');
            let text = `Unit ini terakhir diverifikasi pada <strong>${date}</strong> oleh <strong>${hist.mechanic_name}</strong> di WO <strong>${hist.wo_number}</strong>`;
            
            $('#verification-history-text').html(text);
            $('#verification-history-banner').removeClass('d-none');
        } else {
            $('#verification-history-banner').addClass('d-none');
        }
    },
    error: function() {
        $('#verification-history-banner').addClass('d-none');
    }
});
```

### 4.2 Populate Lokasi Dropdown with Customer Locations

**Find:** Where customer locations data is received (in success callback around line 650+)

**ADD THIS CODE:**

```javascript
// Populate Lokasi dropdown
let lokasiSelect = $('#verify-lokasi');
lokasiSelect.empty().append('<option value="">Pilih Lokasi</option>');

if (data.customer_locations && data.customer_locations.length > 0) {
    data.customer_locations.forEach(function(loc) {
        lokasiSelect.append(`<option value="${loc.id}">${loc.location_name}</option>`);
    });
} else {
    // No customer locations - show POS options
    ['POS 1', 'POS 2', 'POS 3', 'POS 4', 'POS 5', 'Manual'].forEach(function(pos) {
        lokasiSelect.append(`<option value="${pos}">${pos}</option>`);
    });
}

// Set current lokasi value
if (data.unit.lokasi) {
    lokasiSelect.val(data.unit.lokasi);
}
```

---

## ✅ Implementation Checklist

- [ ] Add `getMastHeight()` method to WorkOrderController
- [ ] Add route for `get-mast-height`  
- [ ] Add `getVerificationHistory()` method
- [ ] Add route for `get-verification-history`
- [ ] Modify `getUnitVerificationData()` to include customer locations
- [ ] Modify `saveUnitVerificationHistory()` to save history
- [ ] Update frontend JS to load verification history
- [ ] Update frontend JS to populate Lokasi dropdown

---

## 🧪 Testing

1. **Mast Height Cascade:**
   - Select Model Mast → Tinggi Mast should auto-populate

2. **Customer Locations:**
   - Unit with customer → Show customer's locations
   - Mills unit (no customer) → Show POS 1-5, Manual

3. **Verification History:**
   - First time verification → No banner
   - Subsequent verification → Show: "Terakhir diverifikasi pada [tanggal] oleh [mekanik] di WO [nomor]"

4. **Save History:**
   - After saving verification → Check `unit_verification_history` table has new row
   - JSON data stored correctly

---

**Database ready:** ✅ Table `unit_verification_history` created
**Migration file:** `databases/migrations/PROD_20260313_create_unit_verification_history.sql`
