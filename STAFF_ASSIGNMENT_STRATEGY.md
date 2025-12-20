# Staff Assignment Strategy - Multiple Staff Per Area

## Current Situation

### Problem Analysis (20 Dec 2025)
1. **Work Order 15089 (ID: 37)** - Admin & Foreman kosong
   - `admin_id = NULL`, `foreman_id = NULL`
   - Unit di area_id = 6 (TANGERANG)
   - Area TANGERANG tidak ada ADMIN/FOREMAN assigned (hanya mechanic/helper)

2. **Multiple Staff Per Area** - Terjadi di beberapa area:
   ```
   Bekasi:           2 admin, 1 foreman
   Tangerang:        4 admin (Agus A, Andi, Novi, Sari) - semua PRIMARY
   Jakarta Selatan:  3 admin (Andi, Novi, Sari) - semua PRIMARY
   ```

3. **Query Issue** - JOIN di WorkOrderModel ambil random admin jika ada multiple

## Strategi Solusi

### A. Saat CREATE Work Order
**Auto-assign priority logic:**
1. Cari staff berdasarkan area unit
2. Filter berdasarkan role (ADMIN, FOREMAN, MECHANIC, HELPER)
3. Prioritas pemilihan:
   - **PRIMARY assignment** > BACKUP
   - **Earliest start_date** (yang paling lama bertugas)
   - **Lowest employee_id** (tie-breaker)

**Implementation:**
- Buat function `autoAssignStaffByArea($unitId)` di WorkOrderController
- Return: `['admin_id' => X, 'foreman_id' => Y, ...]`
- Save ke work_orders table

### B. Saat DISPLAY Work Order Details/Print
**Query dengan subquery untuk ambil staff terpilih:**
```sql
LEFT JOIN (
    SELECT aea.area_id, e.id, e.staff_name, e.phone
    FROM area_employee_assignments aea
    JOIN employees e ON aea.employee_id = e.id
    WHERE aea.is_active = 1 
      AND e.staff_role LIKE '%ADMIN%'
      AND e.is_active = 1
    ORDER BY 
        CASE aea.assignment_type WHEN 'PRIMARY' THEN 0 ELSE 1 END,
        aea.start_date ASC,
        e.id ASC
    LIMIT 1
) area_admin ON area_admin.area_id = iu.area_id
```

### C. Handling NULL Cases
- Jika `wo.admin_id` ada → gunakan yang tersimpan (PRIORITAS)
- Jika NULL → gunakan area admin fallback
- Jika area tidak ada admin → tampilkan "-"

## Implementation Steps

### Step 1: Fix Query di WorkOrderModel ✅ COMPLETED
✅ Ubah JOIN untuk area_admin menggunakan window function ROW_NUMBER()
✅ Prioritas: PRIMARY > BACKUP, earliest start_date, lowest ID
✅ Digunakan untuk display fallback jika wo.admin_id NULL

### Step 2: Dropdown Selection for Admin/Foreman ✅ COMPLETED  
✅ Ubah endpoint `getAreaStaff()` untuk return ALL admin/foreman
✅ Response format: `{admins: [...], foremans: [...]}`
✅ Sorting berdasarkan prioritas: PRIMARY first, earliest start_date
✅ Form menggunakan `<select>` dropdown (bukan readonly input)
✅ Auto-select first admin/foreman sebagai default
✅ Badge ⭐ untuk PRIMARY assignment
✅ Update PIC otomatis saat admin berubah
✅ Populate dropdown saat edit work order

### Step 3: Auto-assign saat CREATE (OPTIONAL - FUTURE)
⏳ Buat function autoAssignStaffByArea()
⏳ Apply saat create new work order
⏳ Apply saat edit work order (optional - bisa auto re-assign jika NULL)

### Step 4: UI Indicator (FUTURE)
⏳ Tampilkan assignment_type badge (PRIMARY/BACKUP) di dropdown
⏳ Allow manual override jika user mau pilih admin lain

## Benefits

1. **Konsistensi**: Staff yang sama akan dipilih untuk area yang sama
2. **Transparansi**: User tahu admin/foreman mana yang handle area
3. **Flexibility**: Manual override tetap bisa dilakukan
4. **Scalability**: Bisa handle 1 sampai N staff per area

## Testing Checklist

- [ ] Area dengan 1 admin/foreman → pilih yang itu
- [ ] Area dengan multiple PRIMARY admin → pilih earliest start_date
- [ ] Area tanpa admin/foreman → tampilkan "-"
- [ ] Work order dengan admin_id tersimpan → gunakan yang tersimpan
- [ ] Print work order menampilkan nama yang benar
