---
description: "CodeIgniter 4 development standards for Optima project. Auto-applies to all PHP files. Ensures CSRF token usage, Query Builder patterns, proper error handling, bilingual documentation, and production safety."
applyTo: "**/*.php"
---

# PHP/CodeIgniter 4 Instructions for Optima

## When Editing PHP Files

### 1. Controller Pattern
When creating or modifying controllers:

```php
<?php
namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use App\Models\YourModel;

class YourController extends BaseController
{
    protected $yourModel;
    
    public function __construct()
    {
        $this->yourModel = new YourModel();
        helper(['form', 'url']); // Load helpers if needed
    }
    
    /**
     * Description in English
     * Deskripsi dalam Bahasa Indonesia
     */
    public function yourMethod(): ResponseInterface
    {
        try {
            // Validate input
            $validation = \Config\Services::validation();
            $rules = [
                'field_name' => 'required|min_length[3]',
            ];
            
            if (!$this->validate($rules)) {
                return $this->fail([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $this->validator->getErrors()
                ], 400);
            }
            
            // Process data
            $data = $this->request->getPost();
            $result = $this->yourModel->saveData($data);
            
            return $this->respond([
                'success' => true,
                'message' => 'Data berhasil disimpan',
                'data' => $result
            ]);
            
        } catch (\Exception $e) {
            log_message('error', '[YourController::yourMethod] ' . $e->getMessage());
            return $this->fail([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem'
            ], 500);
        }
    }
}
```

### 2. Model Pattern
When creating or modifying models:

```php
<?php
namespace App\Models;

use CodeIgniter\Model;

class YourModel extends Model
{
    protected $table = 'your_table';
    protected $primaryKey = 'id';
    protected $allowedFields = ['field1', 'field2', 'field3'];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $validationRules = [
        'field1' => 'required|min_length[3]',
        'field2' => 'required|valid_email',
    ];
    
    protected $validationMessages = [
        'field1' => [
            'required' => 'Field ini wajib diisi',
            'min_length' => 'Minimal 3 karakter'
        ]
    ];
    
    /**
     * Get data with joins and conditions
     */
    public function getDataWithDetails($userId = null): array
    {
        $builder = $this->db->table($this->table);
        $builder->select('your_table.*, related_table.name');
        $builder->join('related_table', 'related_table.id = your_table.related_id', 'left');
        
        if ($userId !== null) {
            $builder->where('your_table.user_id', $userId);
        }
        
        return $builder->get()->getResultArray();
    }
}
```

### 3. MANDATORY: CSRF Token in AJAX
**EVERY** AJAX request MUST include CSRF token:

```javascript
// In your view file
<script>
// CSRF token from CodeIgniter
window.csrfTokenName = '<?= csrf_token() ?>';
window.csrfTokenValue = '<?= csrf_hash() ?>';

// AJAX request with CSRF
$.ajax({
    url: '<?= base_url('controller/method') ?>',
    type: 'POST',
    data: {
        [window.csrfTokenName]: window.csrfTokenValue,
        field1: value1,
        field2: value2
    },
    success: function(response) {
        if (response.success) {
            // Handle success
            showNotification('success', response.message);
        } else {
            // Handle error
            showNotification('error', response.message);
        }
    },
    error: function(xhr) {
        showNotification('error', 'Terjadi kesalahan sistem');
    }
});
</script>
```

### 4. Database Query Best Practices

✅ **ALWAYS USE**:
```php
// Query Builder (Recommended)
$this->db->table('units')
    ->where('status', 'active')
    ->orderBy('created_at', 'DESC')
    ->get()
    ->getResultArray();

// Prepared statements with bindings
$this->db->query(
    "SELECT * FROM units WHERE unit_id = ? AND status = ?",
    [$unitId, $status]
);

// Model methods
$this->unitModel->where('status', 'active')->findAll();
```

❌ **NEVER USE**:
```php
// Raw concatenation (SQL Injection risk!)
$this->db->query("SELECT * FROM units WHERE unit_id = " . $id);

// Direct user input in queries
$this->db->query("SELECT * FROM units WHERE name = '" . $_POST['name'] . "'");
```

### 5. Transaction Handling
For multi-table operations:

```php
public function complexOperation($data): bool
{
    $this->db->transStart();
    
    try {
        // Insert/Update operations
        $this->db->table('table1')->insert($data1);
        $insertId = $this->db->insertID();
        
        $this->db->table('table2')->insert([
            'related_id' => $insertId,
            'data' => $data2
        ]);
        
        $this->db->transComplete();
        
        if ($this->db->transStatus() === false) {
            throw new \Exception('Transaction failed');
        }
        
        return true;
        
    } catch (\Exception $e) {
        $this->db->transRollback();
        log_message('error', $e->getMessage());
        return false;
    }
}
```

### 6. View File Pattern

```php
<!-- app/Views/module/view_name.php -->
<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>Judul Halaman<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="content-header">
        <h1><?= esc($title) ?></h1>
    </div>
    
    <div class="card">
        <div class="card-body">
            <!-- Content here -->
            <p><?= esc($description) ?></p>
            
            <!-- ALWAYS escape output -->
            <form id="myForm" action="<?= base_url('controller/save') ?>" method="post">
                <?= csrf_field() ?>
                
                <input type="text" name="field_name" value="<?= esc($data['field_name'] ?? '') ?>">
                
                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>

<script>
// CSRF token for AJAX
window.csrfTokenName = '<?= csrf_token() ?>';
window.csrfTokenValue = '<?= csrf_hash() ?>';
</script>
<?= $this->endSection() ?>
```

### 7. Error Handling & Logging

```php
try {
    // Main logic
    
} catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
    // Database specific error
    log_message('error', '[ClassName::methodName] Database Error: ' . $e->getMessage());
    return $this->fail('Terjadi kesalahan database', 500);
    
} catch (\Exception $e) {
    // General error
    log_message('error', '[ClassName::methodName] Error: ' . $e->getMessage());
    return $this->fail('Terjadi kesalahan sistem', 500);
}
```

### 8. Validation Rules
Common validation patterns for Optima:

```php
// In Controller or Model
protected $validationRules = [
    'unit_polisi' => 'required|min_length[5]|max_length[20]|is_unique[units.unit_polisi,unit_id,{unit_id}]',
    'contract_date' => 'required|valid_date[Y-m-d]',
    'contract_value' => 'required|numeric|greater_than[0]',
    'email' => 'permit_empty|valid_email',
    'phone' => 'required|regex_match[/^(\+62|62|0)[0-9]{9,12}$/]',
];
```

### 9. Helper Functions Usage

```php
// Load helpers
helper(['form', 'url', 'text', 'number']);

// Common usages
base_url('path/to/resource');           // Generate full URL
site_url('controller/method');          // Generate site URL
esc($data);                             // Escape output (XSS protection)
csrf_field();                           // Generate CSRF hidden field
csrf_token();                           // Get CSRF token name
csrf_hash();                            // Get CSRF hash value
```

### 10. Response Patterns

```php
// JSON Success Response
return $this->respond([
    'success' => true,
    'message' => 'Operasi berhasil',
    'data' => $result
], 200);

// JSON Error Response
return $this->fail([
    'success' => false,
    'message' => 'Operasi gagal',
    'errors' => $errors
], 400);

// Redirect with Session Flash
session()->setFlashdata('success', 'Data berhasil disimpan');
return redirect()->to('controller/index');
```

## Critical Reminders

### Before ANY Database Modification:
1. ✅ Check if table/column exists
2. ✅ Use Query Builder or prepared statements
3. ✅ Validate all inputs
4. ✅ Log errors properly
5. ✅ Test with sample data first

### Before ANY AJAX Implementation:
1. ✅ Include CSRF token
2. ✅ Handle both success and error responses
3. ✅ Show user-friendly messages in Indonesian
4. ✅ Validate on both client and server side

### Before Production Deployment:
1. ✅ Remove all debug code (`dd()`, `var_dump()`, `print_r()`)
2. ✅ Check error logging is enabled
3. ✅ Verify CSRF tokens work
4. ✅ Test all forms and AJAX requests
5. ✅ Review `DEPLOYMENT_CHECKLIST.md`

## Common Optima-Specific Patterns

### Unit Status Management
```php
// In models/controllers dealing with units
$validStatuses = ['available', 'rented', 'maintenance', 'sold'];
```

### Contract Renewal Flow
```php
// Always check contract end date before renewal
$contractEndDate = $contract['contract_end_date'];
$renewalStartDate = date('Y-m-d', strtotime($contractEndDate . ' +1 day'));
```

### DataTable Server-Side
```php
public function datatableData()
{
    $request = service('request');
    $draw = $request->getPost('draw');
    $start = $request->getPost('start');
    $length = $request->getPost('length');
    $searchValue = $request->getPost('search')['value'];
    
    $data = $this->model->getDatatableData($start, $length, $searchValue);
    $totalRecords = $this->model->getTotalRecords();
    $totalFiltered = $this->model->getTotalFiltered($searchValue);
    
    return $this->respond([
        'draw' => $draw,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $totalFiltered,
        'data' => $data
    ]);
}
```

---

**Remember**: Code in English, Messages in Indonesian, Security First!
