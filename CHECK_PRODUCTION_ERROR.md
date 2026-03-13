# Check Production Error - Quotation Specifications

## Error Details
- **URL**: `quotations/get-specifications/24`
- **HTTP Status**: 500 (Internal Server Error)
- **Frontend Error**: "Error loading specifications: HTTP 500:"

## How to Debug Production

### 1. **Check Production Error Log** (SSH ke Hostinger)
```bash
ssh username@yourdomain.com
cd public_html/writable/logs
tail -100 log-2026-03-13.log | grep -i "error\|exception"
```

Cari error message yang terkait dengan:
- `getSpecifications`
- `quotation_specifications`
- SQL error
- Column not found

### 2. **Common Causes for HTTP 500**

#### A. Database Column Missing
Jika ada column baru di development yang belum di production:
```sql
-- Cek apakah column 'spare_quantity' ada
DESCRIBE quotation_specifications;

-- Jika tidak ada, tambahkan:
ALTER TABLE quotation_specifications 
ADD COLUMN spare_quantity INT(11) DEFAULT 0 AFTER quantity;

ALTER TABLE quotation_specifications 
ADD COLUMN is_spare_unit TINYINT(1) DEFAULT 0 AFTER spare_quantity;
```

#### B. Missing JOIN Table
Error bisa karena table baru belum ada:
- `baterai`
- `charger`
- `attachment`
- `tipe_mast`
- `tipe_ban`
- `jenis_roda`
- `valve`

#### C. Model Method Issue
Cek apakah `QuotationSpecificationModel::getQuotationSpecifications()` ada di production.

### 3. **Quick Fix: Add Debug Logging**

Edit production `app/Controllers/Quotation.php` line 560, tambahkan debug:

```php
public function getSpecifications($quotationId)
{
    try {
        log_message('debug', "=== START getSpecifications ===");
        log_message('debug', "quotationId: $quotationId");
        
        if (!$this->session->get('isLoggedIn')) {
            log_message('debug', "Session check failed");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Session expired'
            ]);
        }

        log_message('debug', "Checking quotation exists...");
        $quotation = $this->quotationModel->find($quotationId);
        if (!$quotation) {
            log_message('debug', "Quotation not found");
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Quotation not found'
            ]);
        }

        log_message('debug', "Loading specifications...");
        $specifications = $this->quotationSpecificationModel->getQuotationSpecifications($quotationId);
        
        log_message('debug', "Specifications loaded: " . count($specifications));
        log_message('debug', "=== END getSpecifications ===");
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $specifications,
            'summary' => []
        ]);
        
    } catch (\Exception $e) {
        log_message('error', '!!! getSpecifications EXCEPTION !!!');
        log_message('error', 'Message: ' . $e->getMessage());
        log_message('error', 'File: ' . $e->getFile() . ':' . $e->getLine());
        log_message('error', 'Trace: ' . $e->getTraceAsString());
        
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}
```

### 4. **Temporary Workaround**

Jika perlu quick fix, comment summary loading:
```php
// Temporarily disable summary
// $summary = $this->quotationSpecificationModel->getSpecificationsSummary($quotationId);

return $this->response->setJSON([
    'success' => true,
    'data' => $specifications,
    'summary' => [] // Empty array instead
]);
```

### 5. **Check Model Method Exists**

SSH ke production, cek file:
```bash
cat app/Models/QuotationSpecificationModel.php | grep "getQuotationSpecifications"
```

Jika tidak ada, method harus ditambahkan.

## Action Required

**PRIORITY HIGH** - Cek production log ASAP:
```bash
tail -f writable/logs/log-2026-03-13.log
```

Lalu buka Quotation Details di browser lagi, lihat error yang muncul real-time di log.

**Report back** dengan:
1. Full error message dari log
2. Output `DESCRIBE quotation_specifications;`
3. Apakah `QuotationSpecificationModel::getQuotationSpecifications()` method ada?
