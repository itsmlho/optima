# 🔄 TEMPORARY UNITS VISUAL INDICATORS & RETURN WORKFLOW

**Date**: December 17, 2025  
**Status**: ✅ **IMPLEMENTED**

---

## 🎯 OVERVIEW

Dokumentasi ini menjelaskan:
1. **Visual Indicators** untuk temporary units
2. **Step-by-step workflow** untuk return unit setelah maintenance selesai
3. **Lokasi** di mana indicators muncul

---

## 🏷️ VISUAL INDICATORS

### **1. Indicator di Marketing DI List**

#### **Location**: `app/Views/marketing/di.php` - Tabel Delivery Instructions

**Indicator Badge**: 🔄 **TEMP**

**Appearance**:
```
| DI Number | Units                                      | Tujuan                    |
|-----------|---------------------------------------------|---------------------------|
| DI/001    | 2 Unit 🔄 TEMP                              | 🟡 TUKAR - Maintenance   |
| DI/002    | 3 Unit                                      | 🔴 HABIS_KONTRAK         |
```

**Meaning**:
- Badge **🔄 TEMP** muncul di kolom "Units" 
- Menandakan DI ini involve temporary unit replacement
- Hanya muncul untuk `TUKAR_MAINTENANCE` yang create temporary assignment

**Tooltip**: "Contains temporary units (TUKAR_MAINTENANCE)"

---

### **2. Indicator di Tujuan Perintah (Already Implemented)**

#### **Emoji Indicators**:

| Emoji | Workflow Type | Description |
|-------|---------------|-------------|
| 🔴 | **PERMANENT** | Unit disconnected permanently (HABIS_KONTRAK, TUKAR_UPGRADE/DOWNGRADE/RUSAK) |
| 🔵 | **TEMPORARY** | Unit returns after service (TARIK_MAINTENANCE/RUSAK) |
| 🟡 | **TEMP REPLACEMENT** | Original unit returns after maintenance (TUKAR_MAINTENANCE) |
| 🟢 | **RELOCATION** | Same customer, different location (TARIK_PINDAH_LOKASI) |

**Visual Example**:
```
Tujuan Perintah: 🟡 TUKAR - Maintenance
                 ↑
            TEMP REPLACEMENT indicator
```

---

### **3. Indicator di Customer Management** (Future Enhancement)

**Planned Location**: Customer detail → Contracts tab → Unit count

**Example**:
```
Total Units: 50 (2 temporary)
             ↑      ↑
         Permanent  Temporary borrowed
```

---

## 📋 RETURN WORKFLOW - STEP BY STEP

### **Scenario**: Unit customer rusak → Temporary replacement → Original unit fixed → Return

---

### **STEP 1: Original Unit Fails** ❌

**Action by Operations**:
1. Unit customer mengalami kerusakan
2. Customer complain → Unit tidak bisa dipakai

**System State**:
```
Customer: PT ABC
Contract: KNTRK/2024/001
Unit FL-100: status = RENTAL_ACTIVE (rusak)
```

---

### **STEP 2: Create DI untuk TUKAR_MAINTENANCE** 🟡

**Action by Marketing**:
1. Buka **Marketing → Delivery Instructions**
2. Click **Create New DI**
3. Pilih:
   - **Jenis Perintah**: TUKAR
   - **Tujuan**: TUKAR - Maintenance
4. Select:
   - **Contract unit** (original): FL-100 (rusak)
   - **Temporary unit** (available): FL-999

**System Process** (Automatic):
```sql
-- Create kontrak_unit entry
INSERT INTO kontrak_unit (
    kontrak_id, 
    unit_id, 
    is_temporary, 
    original_unit_id,
    temporary_replacement_date
) VALUES (
    123,              -- Contract ID
    999,              -- Temporary unit (FL-999)
    1,                -- is_temporary = TRUE
    100,              -- Original unit (FL-100)
    NOW()
);

-- Update inventory_unit
UPDATE inventory_unit 
SET 
    kontrak_id = 123,
    customer_id = ...,
    status_unit_id = 7  -- RENTAL_ACTIVE
WHERE id_inventory_unit = 999;  -- Temporary unit

UPDATE inventory_unit
SET 
    kontrak_id = NULL,
    workflow_status = 'IN_MAINTENANCE',
    status_unit_id = 8  -- MAINTENANCE
WHERE id_inventory_unit = 100;  -- Original unit
```

**Visual in DI List**:
```
DI/2024/123 | 1 Unit 🔄 TEMP | 🟡 TUKAR - Maintenance
```

---

### **STEP 3: Operational Executes DI** 🚚

**Action by Operational**:
1. **Temporary unit FL-999** dikirim ke customer
2. **Original unit FL-100** dibawa ke workshop
3. Update DI status → SELESAI

**System State After Execution**:
```
Customer Unit:
- FL-999 (temporary) → With customer (RENTAL_ACTIVE)

Workshop:
- FL-100 (original) → Under repair (MAINTENANCE)

Database:
kontrak_unit.is_temporary = 1
kontrak_unit.temporary_end_date = NULL (still borrowed)
```

**Customer sees**: Unit FL-999 (temporary replacement)

---

### **STEP 4: Maintenance Completed** ✅

**Action by Operational/Mechanic**:
1. Unit FL-100 selesai di-service
2. Update work order status → COMPLETED
3. **Update inventory_unit**:
```sql
UPDATE inventory_unit
SET workflow_status = 'MAINTENANCE_COMPLETED'
WHERE id_inventory_unit = 100;  -- Original unit FL-100
```

**System Detects**: Original unit ready to return

---

### **STEP 5: Check Temporary Units Report** 📊

**Action by Operations Manager**:
1. Go to: **Operational → Temporary Units Report**
   - URL: `/operational/temporary-units-report`

**Report Shows**:
```
╔═══════════════════════════════════════════════════════════════════════╗
║  TEMPORARY UNITS TRACKING REPORT                                      ║
╠═══════════════════════════════════════════════════════════════════════╣
║  Total Temporary: 1                                                   ║
║  Overdue (>30d): 0                                                    ║
║  Avg Days: 15 days                                                    ║
║  Ready to Return: 1  ← UNIT FL-100 READY!                            ║
╚═══════════════════════════════════════════════════════════════════════╝

┌────────────┬──────────┬──────────┬───────────┬──────────┬──────────────┐
│ Customer   │ Contract │ Temp     │ Original  │ Days     │ Status       │
│            │          │ Unit     │ Unit      │ Borrowed │              │
├────────────┼──────────┼──────────┼───────────┼──────────┼──────────────┤
│ PT ABC     │ KNTRK... │ FL-999   │ FL-100    │ 15 days  │ ✅ Ready     │
│            │          │          │           │          │ [Return BTN] │
└────────────┴──────────┴──────────┴───────────┴──────────┴──────────────┘
```

**Key Indicators**:
- Original Status: **Ready to Return** (green badge)
- Button: **Return** (enabled, green)

---

### **STEP 6: Process Return** 🔄

**Action by Operations**:
1. Click **Return** button on FL-100 row
2. Modal appears:

```
┌─────────────────────────────────────────────────┐
│  🔄 Return Original Unit                        │
├─────────────────────────────────────────────────┤
│                                                 │
│  ℹ️ This will disconnect temporary unit and    │
│     reconnect original unit to contract         │
│                                                 │
│  Customer:       PT ABC                         │
│  Contract:       KNTRK/2024/001                 │
│  Temporary Unit: FL-999                         │
│  Original Unit:  FL-100                         │
│  Days Borrowed:  15 days                        │
│                                                 │
│           [Cancel]  [✅ Process Return]         │
└─────────────────────────────────────────────────┘
```

3. Click **Process Return**

---

### **STEP 7: System Auto-Processing** ⚙️

**Automatic Actions** (No manual intervention needed):

```sql
-- 1. Disconnect temporary unit FL-999
UPDATE inventory_unit
SET 
    kontrak_id = NULL,
    customer_id = NULL,
    customer_location_id = NULL,
    workflow_status = 'RETURNED_FROM_TEMP_ASSIGNMENT',
    status_unit_id = 1  -- AVAILABLE_STOCK
WHERE id_inventory_unit = 999;

-- 2. Reconnect original unit FL-100
UPDATE inventory_unit
SET 
    kontrak_id = 123,  -- Restore contract
    customer_id = ..., -- Restore customer
    workflow_status = 'RETURNED_TO_CUSTOMER',
    status_unit_id = 7  -- RENTAL_ACTIVE
WHERE id_inventory_unit = 100;

-- 3. Mark temporary assignment as ended
UPDATE kontrak_unit
SET 
    temporary_end_date = NOW(),
    returned_by = [user_id],
    return_notes = 'Original unit returned from maintenance'
WHERE id = [kontrak_unit_id];

-- 4. Log activity
INSERT INTO activity_logs (
    module, action, description, user_id, created_at
) VALUES (
    'operational',
    'return_temporary_unit',
    'Returned temporary unit FL-999, reconnected FL-100',
    [user_id],
    NOW()
);
```

**Result**:
```
✅ Temporary unit returned successfully!
```

---

### **STEP 8: Operational Delivers Original Unit** 🚚

**Action by Operational**:
1. **FL-999** (temporary) dikembalikan dari customer
2. **FL-100** (original, sudah fixed) dikirim ke customer
3. Customer kembali menggunakan unit asli mereka

**Final State**:
```
Customer Unit:
- FL-100 (original) → With customer (RENTAL_ACTIVE) ✅

Warehouse:
- FL-999 (temporary) → Available for next assignment (AVAILABLE_STOCK) ✅

Database:
kontrak_unit.is_temporary = 1
kontrak_unit.temporary_end_date = "2024-12-17 10:30:00" (returned) ✅
```

---

## 🔍 HOW TO CHECK STATUS

### **Option 1: Temporary Units Report** (Recommended)

**Access**: `/operational/temporary-units-report`

**Shows**:
- All active temporary assignments
- Days borrowed for each
- Original unit status
- Return action buttons

**Filters**:
- By customer
- By duration (< 7 days, 7-30 days, >30 days)

---

### **Option 2: Customer Management**

**Access**: Marketing → Customer Management → View Customer

**Shows**:
- Total permanent units
- Total temporary units (if any)

**Currently**: Temporary units **excluded** from count (for accurate billing)

---

### **Option 3: DI List (Marketing)**

**Access**: Marketing → Delivery Instructions

**Look for**: Badge **🔄 TEMP** in Units column

**Meaning**: This DI created temporary assignment

---

## 🎨 VISUAL INDICATORS SUMMARY

### **In DI List** (Marketing):
```
┌────────────┬─────────────────────┬──────────────────────┐
│ DI Number  │ Units               │ Tujuan               │
├────────────┼─────────────────────┼──────────────────────┤
│ DI/001     │ 2 Unit 🔄 TEMP     │ 🟡 TUKAR-Maintenance│
│ DI/002     │ 3 Unit              │ 🔴 HABIS_KONTRAK    │
│ DI/003     │ 1 Unit              │ 🔵 TARIK-Maintenance│
└────────────┴─────────────────────┴──────────────────────┘
```

### **In Temporary Report** (Operational):
```
Status Indicators:
🟢 Ready to Return    → Original unit maintenance complete
🔴 In Maintenance     → Original unit still being serviced
🟡 < 7 days borrowed  → Recent temporary
🟠 7-30 days          → Normal duration
🔴 > 30 days          → Overdue, needs attention
```

---

## ⚠️ IMPORTANT NOTES

### **When to Check Temporary Report**:
- **Daily**: For overdue units (>30 days)
- **Weekly**: Review all temporary assignments
- **When maintenance complete**: Process returns immediately

### **Customer Billing**:
- ✅ Temporary units **NOT BILLED** (automatically excluded)
- ✅ Only permanent units charged
- ✅ Customer report shows accurate count

### **Warehouse Inventory**:
- ✅ Both permanent AND temporary counted (full tracking)
- ✅ Warehouse sees all physical units
- ❌ Filter not applied (intentional)

---

## 🎓 TRAINING POINTS

### **For Marketing Team**:
1. When creating DI with `TUKAR_MAINTENANCE`:
   - Badge **🔄 TEMP** will appear in DI list
   - Indicates temporary replacement created
   - Original unit will return after service

2. Customer billing:
   - Temporary units automatically excluded
   - No manual adjustment needed

### **For Operational Team**:
1. After maintenance complete:
   - Check **Temporary Units Report**
   - Look for **Ready to Return** status
   - Click **Return** button
   - System handles all updates automatically

2. Physical delivery:
   - Return temporary unit from customer
   - Deliver original unit back
   - No additional paperwork

### **For Finance Team**:
1. Contract billing:
   - Temporary units automatically excluded
   - Reports show accurate permanent unit count
   - No overbilling risk

---

## 📞 QUICK REFERENCE

| Action | Location | URL |
|--------|----------|-----|
| View all temporary units | Operational Report | `/operational/temporary-units-report` |
| Check DI temporary indicator | Marketing DI List | `/marketing/delivery-instructions` |
| Process return | Temporary Report | Click **Return** button |
| Check customer unit count | Customer Management | `/marketing/customer-management` |

---

## ✅ CHECKLIST FOR RETURN PROCESS

```
□ Original unit maintenance COMPLETE
□ Check Temporary Units Report
□ Verify "Ready to Return" status
□ Click Return button
□ Confirm in modal
□ ✅ System processes automatically
□ Coordinate physical swap with customer
□ Return temporary unit to warehouse
□ Deliver original unit to customer
□ ✅ Process complete!
```

---

**Document Version**: 1.0  
**Last Updated**: December 17, 2025  
**Contact**: OPTIMA Development Team
