# Database Schema Mapping - Optima CI
## Date: February 19, 2026

This document maps the **assumed schema** (in the code) to the **actual schema** (in optima_ci database).

---

## Table Name Differences

| Assumed (Code) | Actual (Database) |
|----------------|-------------------|
| `contracts` | `kontrak` |
| `delivery_instructions` | `delivery_instructions` ✅ (same) |
| `customers` | `customers` ✅ (same) |
| `quotations` | `quotations` ✅ (same) |
| `spk` | `spk` ✅ (same) |

---

## Column Name Differences

### `delivery_instructions` Table

| Assumed (Code) | Actual (Database) | Notes |
|----------------|-------------------|-------|
| `status` | `status_di` | Enum values |
| `completed_at` | `sampai_tanggal_approve` | Completion date |
| `contract_id` | **NO DIRECT COLUMN** | Must join through `spk.kontrak_id` |
| `created_at` | `dibuat_pada` | Creation timestamp |
| `updated_at` | `diperbarui_pada` | Update timestamp |
| `di_number` | `nomor_di` | DI number |

**Status Values (status_di):**
- Actual: `'DIAJUKAN','DISETUJUI','PERSIAPAN_UNIT','SIAP_KIRIM','DALAM_PERJALANAN','SAMPAI_LOKASI','SELESAI','DIBATALKAN'`
- **Use `'SELESAI'` for completed status** (not 'DELIVERED')

---

### `spk` Table

| Assumed (Code) | Actual (Database) | Notes |
|----------------|-------------------|-------|
| `contract_id` | `kontrak_id` | Foreign key to kontrak |
| `spk_number` | `nomor_spk` | SPK number |
| `created_at` | `dibuat_pada` | Creation timestamp |
| `updated_at` | `diperbarui_pada` | Update timestamp |

---

### `kontrak` Table

| Assumed (Code) | Actual (Database) | Notes |
|----------------|-------------------|-------|
| `contract_number` | `no_kontrak` | Contract number |
| `customer_id` | **NO DIRECT COLUMN** | Must join through `customer_contracts` |
| `start_date` | `tanggal_mulai` | Start date |
| `end_date` | `tanggal_berakhir` | End date |
| `created_at` | `dibuat_pada` | Creation timestamp |
| `updated_at` | `diperbarui_pada` | Update timestamp |

**Rental Types:**
- `'CONTRACT'`, `'PO_ONLY'`, `'DAILY_SPOT'`

---

### `customers` Table

| Assumed (Code) | Actual (Database) | Notes |
|----------------|-------------------|-------|
| `company_name` | `customer_name` | Customer name |
| `customer_code` | `customer_code` ✅ | Customer code |

---

## Relationship Mapping

### Invoice Automation Query (Eligible DIs)

**Original Query (Assumed):**
```sql
SELECT di.*, c.customer_id, c.contract_number, cust.company_name
FROM delivery_instructions di
JOIN contracts c ON c.id = di.contract_id
JOIN customers cust ON cust.id = c.customer_id
WHERE di.status = 'DELIVERED'
  AND di.contract_id IS NOT NULL
  AND DATE_ADD(di.completed_at, INTERVAL 30 DAY) <= NOW()
  AND di.invoice_generated = 0
```

**Actual Query (Corrected):**
```sql
SELECT di.*, 
       k.id AS kontrak_id,
       k.no_kontrak AS contract_number,
       c.customer_name,
       q.assigned_to AS sales_user_id
FROM delivery_instructions di
INNER JOIN spk s ON s.id = di.spk_id
INNER JOIN kontrak k ON k.id = s.kontrak_id
INNER JOIN customer_contracts cc ON cc.kontrak_id = k.id
INNER JOIN customers c ON c.id = cc.customer_id
LEFT JOIN quotations q ON q.id_quotation = s.quotation_specification_id
WHERE di.status_di = 'SELESAI'
  AND s.kontrak_id IS NOT NULL
  AND DATE_ADD(di.sampai_tanggal_approve, INTERVAL 30 DAY) <= NOW()
  AND di.invoice_generated = 0
```

**Key Changes:**
1. `status` → `status_di` with value `'SELESAI'` (not 'DELIVERED')
2. `completed_at` → `sampai_tanggal_approve`
3. Join path: `di → spk → kontrak → customer_contracts → customers`
4. No direct `contract_id` in DI, check `spk.kontrak_id IS NOT NULL`

---

## Migration Results

### Migration #1: Invoice Automation Flags ✅
- **File:** `2026-02-19_add_invoice_generated_flag.sql`
- **Columns Added:**
  - `delivery_instructions.invoice_generated` (TINYINT(1) DEFAULT 0)
  - `delivery_instructions.invoice_generated_at` (DATETIME NULL)
- **Index Added:** `idx_invoice_automation (invoice_generated, sampai_tanggal_approve, spk_id, status_di)`
- **Status:** Successfully applied
- **Eligible DIs:** 8 total SELESAI DIs, 8 eligible for auto-invoice

### Migration #2: Customer Conversion Tracking ✅
- **File:** `2026-02-19_add_customer_converted_at_column.sql`
- **Columns Added:**
  - `quotations.customer_converted_at` (DATETIME NULL)
- **Status:** Successfully applied
- **Related Column:** `quotations.created_customer_id` (already exists)

---

## Code Updates Required

### Files That Need Schema Adjustments:

1. **app/Jobs/InvoiceAutomationJob.php** ❌ NEEDS UPDATE
   - Line 95-105: `getEligibleDeliveryInstructions()` query
   - Change table names: `contracts` → `kontrak`
   - Change column names: `status` → `status_di`, `completed_at` → `sampai_tanggal_approve`
   - Update join path to include `customer_contracts`
   - Change status value: `'DELIVERED'` → `'SELESAI'`

2. **app/Controllers/Marketing.php** ❌ NEEDS UPDATE
   - Line 8470-8495: `linkSPKToContract()` late-linking logic
   - Column name: `completed_at` → `sampai_tanggal_approve`
   - Status check: `'DELIVERED'` → `'SELESAI'`

3. **app/Models/DeliveryInstructionModel.php** ⚠️ MAY NEED UPDATE
   - Check if model uses correct column names
   - Verify protected $allowedFields includes new columns

4. **app/Commands/InvoiceAutomation.php** ✅ OK
   - CLI command itself is OK, it just calls InvoiceAutomationJob

---

## Testing Checklist

Before testing invoice automation:

- [x] Database migrations applied
- [x] Columns added to `delivery_instructions` table
- [x] Index created for performance
- [ ] Update `InvoiceAutomationJob.php` with correct schema
- [ ] Update `Marketing.php` controller with correct schema
- [ ] Update `DeliveryInstructionModel.php` if needed
- [ ] Test CLI command: `php spark jobs:invoice-automation --dry-run`
- [ ] Verify query returns correct eligible DIs

---

## Environment Configuration

**Updated in `.env`:**
```env
ACC_EMAIL_1 = finance@sml.co.id
ACC_EMAIL_2 = anselin_smlforklift@yahoo.com
MARKETING_EMAIL marketing@sml.co.id
```

**SMTP Configuration:** (commented out, configure for production)

---

## Next Steps

1. **Update Code to Match Schema** - Modify InvoiceAutomationJob.php and Marketing.php controller
2. **Test Dry-Run** - Run `php spark jobs:invoice-automation --dry-run --verbose`
3. **Verify Query** - Check that eligible DIs are correctly identified
4. **Test Invoice Generation** - Generate test invoice for one DI
5. **Verify Email** - Check if emails are sent to ACC and Marketing teams
6. **Manual Testing** - Follow TESTING_VERIFICATION_GUIDE.md

---

**Status:** Database migrations complete, code updates required before testing.
