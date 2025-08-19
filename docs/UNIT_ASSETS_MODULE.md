# Unit Assets Module Documentation

## Overview

The Unit Assets module is a comprehensive management system for tracking and managing unit assets (vehicles, equipment, machinery) within the OPTIMA application. It provides complete CRUD (Create, Read, Update, Delete) functionality with advanced features like statistics, filtering, search, and export capabilities.

## Architecture

### Components

1. **UnitAssetController** - Main controller handling HTTP requests
2. **UnitAssetModel** - Data access layer with validation and business logic
3. **ApiController** - API endpoints for AJAX requests
4. **ModelFormulir** - Helper model for form dropdown data
5. **Views** - User interface components

### Database Structure

The module uses the following primary table:
- `unit_asset` - Main table storing unit asset information
- Related tables: `departemen`, `status_unit`, `tipe_unit`, `model_unit`, `kapasitas`, etc.

## Features

### Core Features

1. **CRUD Operations**
   - Create new unit assets
   - Read/view unit asset details
   - Update existing unit assets
   - Delete unit assets (with validation)

2. **Statistics Dashboard**
   - Total assets count
   - Available units count
   - Rented units count
   - Maintenance units count
   - Utilization rate calculation

3. **Advanced Filtering**
   - Filter by status (available, rented, maintenance, retired)
   - Filter by department
   - Filter by location
   - Search functionality

4. **Export Functionality**
   - Export filtered data to CSV
   - Customizable export fields

5. **Real-time Validation**
   - Client-side validation
   - Server-side validation
   - Interactive form feedback

### User Interface Features

1. **DataTables Integration**
   - Server-side processing
   - Pagination
   - Sorting
   - Search

2. **Modal Forms**
   - Create/Edit forms in modals
   - View details in modals
   - Responsive design

3. **Form Dependencies**
   - Merk-Model dropdown dependency
   - Dynamic form field updates

## API Endpoints

### Main Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/warehouse/unit-assets` | Main index page |
| POST | `/warehouse/unit-assets/store` | Create new unit asset |
| GET | `/warehouse/unit-assets/show/{id}` | View unit asset details |
| POST | `/warehouse/unit-assets/update/{id}` | Update unit asset |
| POST | `/warehouse/unit-assets/delete/{id}` | Delete unit asset |
| POST | `/warehouse/unit-assets/datatable` | DataTables data |
| GET | `/warehouse/unit-assets/export` | Export data |

### API Endpoints

| Method | URL | Description |
|--------|-----|-------------|
| GET | `/api/merk` | Get all brands |
| GET | `/api/models-by-merk/{merk}` | Get models by brand |
| GET | `/api/form-data` | Get all form data |
| GET | `/api/dropdown/{type}` | Get specific dropdown data |

## Usage Examples

### Creating a Unit Asset

```javascript
// JavaScript example
const formData = new FormData();
formData.append('serial_number', 'SN123456');
formData.append('status_unit', '1');
formData.append('departemen', '1');
formData.append('lokasi_unit', 'Warehouse A');
formData.append('tipe_unit', '1');
formData.append('tahun_unit', '2023');
formData.append('merk_unit', 'Toyota');
formData.append('model_unit', '1');
formData.append('kapasitas', '1');
formData.append('status_aset', 'active');

fetch('/warehouse/unit-assets/store', {
    method: 'POST',
    body: formData
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        console.log('Unit asset created successfully');
    }
});
```

### Fetching Unit Assets for DataTables

```javascript
$('#unitAssetsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: '/warehouse/unit-assets/datatable',
        type: 'POST',
        data: function(d) {
            d.status_unit_filter = $('#statusFilter').val();
            d.departemen_filter = $('#departmentFilter').val();
            d.lokasi_filter = $('#locationFilter').val();
        }
    },
    columns: [
        { data: 0, name: 'no_unit' },
        { data: 1, name: 'serial_number' },
        { data: 2, name: 'model_unit' },
        { data: 3, name: 'departemen' },
        { data: 4, name: 'lokasi_unit' },
        { data: 5, name: 'status_unit', orderable: false },
        { data: 6, name: 'status_aset', orderable: false },
        { data: 7, name: 'actions', orderable: false, searchable: false }
    ]
});
```

### Getting Statistics

```php
// PHP example
$unitAssetModel = new UnitAssetModel();
$stats = $unitAssetModel->getUnitAssetStats();

// Returns:
// [
//     'total' => 150,
//     'available' => 120,
//     'rented' => 25,
//     'maintenance' => 5,
//     'retired' => 0,
//     'utilization_rate' => 16.67
// ]
```

## Configuration

### Form Options

The module supports configurable form options through the `getFormOptions()` method:

```php
$formOptions = [
    'status_unit' => [...],      // Unit status options
    'departemen' => [...],       // Department options
    'tipe_unit' => [...],       // Unit type options
    'merk_unit' => [...],       // Brand options
    'kapasitas' => [...],       // Capacity options
    'status_aset' => [...],     // Asset status options
    'tipe_mast' => [...],       // Mast type options
    'mesin' => [...],           // Engine options
    'attachment' => [...],      // Attachment options
    'baterai' => [...],         // Battery options
    'charger' => [...],         // Charger options
    'jenis_roda' => [...],      // Wheel type options
    'tipe_ban' => [...],        // Tire type options
    'valve' => [...]            // Valve options
];
```

### Validation Rules

The module includes comprehensive validation rules:

```php
$validationRules = [
    'serial_number' => 'required|min_length[3]|max_length[100]',
    'status_unit' => 'required',
    'departemen' => 'required',
    'lokasi_unit' => 'required|max_length[100]',
    'tipe_unit' => 'required',
    'tahun_unit' => 'required|integer|greater_than[1900]|less_than_equal_to[2024]',
    'model_unit' => 'required',
    'kapasitas_unit' => 'required|decimal',
    'status_aset' => 'required|in_list[active,inactive,disposed]'
];
```

## Error Handling

The module implements comprehensive error handling:

### Server-side Error Handling

```php
try {
    // Operation
    $result = $this->unitAssetModel->insert($data);
    
    if ($result) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Unit asset created successfully',
            'data' => ['no_unit' => $nextUnitNumber]
        ]);
    } else {
        return $this->response->setJSON([
            'success' => false,
            'message' => 'Failed to create unit asset',
            'errors' => $this->unitAssetModel->errors()
        ]);
    }
} catch (\Exception $e) {
    log_message('error', 'Unit asset creation failed: ' . $e->getMessage());
    return $this->response->setJSON([
        'success' => false,
        'message' => 'System error occurred: ' . $e->getMessage()
    ]);
}
```

### Client-side Error Handling

```javascript
function displayValidationErrors(errors) {
    clearValidationErrors();
    
    Object.keys(errors).forEach(field => {
        const input = document.querySelector(`[name="${field}"]`);
        if (input) {
            input.classList.add('is-invalid');
            
            let feedback = input.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
                feedback = document.createElement('div');
                feedback.classList.add('invalid-feedback');
                input.parentNode.insertBefore(feedback, input.nextSibling);
            }
            
            feedback.textContent = errors[field];
        }
    });
}
```

## Security Features

1. **CSRF Protection** - All forms include CSRF tokens
2. **Input Validation** - Server-side and client-side validation
3. **SQL Injection Prevention** - Using CodeIgniter's Query Builder
4. **XSS Prevention** - Using `esc()` function for output
5. **Authorization** - AJAX-only endpoints for sensitive operations

## Performance Optimizations

1. **Efficient Database Queries**
   - Single query for statistics instead of multiple queries
   - JOINs instead of N+1 queries
   - Proper indexing on foreign keys

2. **Caching**
   - Form options caching
   - Statistics caching (where applicable)

3. **Pagination**
   - Server-side pagination for large datasets
   - Configurable page sizes

## Testing

The module includes comprehensive tests:

```php
// Unit test example
public function testGetUnitAssetStats()
{
    $stats = $this->unitAssetModel->getUnitAssetStats();
    $this->assertIsArray($stats);
    $this->assertArrayHasKey('total', $stats);
    $this->assertArrayHasKey('available', $stats);
    $this->assertArrayHasKey('rented', $stats);
    $this->assertArrayHasKey('maintenance', $stats);
}
```

## Troubleshooting

### Common Issues

1. **DataTables not loading**
   - Check AJAX endpoint URL
   - Verify server response format
   - Check browser console for errors

2. **Form validation errors**
   - Ensure all required fields are filled
   - Check field format requirements
   - Verify CSRF token is present

3. **Export not working**
   - Check file permissions
   - Verify export parameters
   - Check server memory limits

### Debug Mode

Enable debug mode in the controller:

```php
ini_set('memory_limit', '1024M');
log_message('debug', 'Debug information: ' . json_encode($data));
```

## Future Enhancements

1. **Bulk Operations**
   - Bulk import from CSV/Excel
   - Bulk update operations
   - Bulk delete with confirmation

2. **Advanced Reporting**
   - Utilization reports
   - Maintenance scheduling
   - Asset depreciation

3. **Mobile App Integration**
   - QR code scanning
   - Mobile-friendly interface
   - Offline capability

4. **Integration Features**
   - API webhooks
   - Third-party integrations
   - Automated notifications

## Support

For technical support or questions about the Unit Assets module:

1. Check the logs in `writable/logs/`
2. Review the test results
3. Consult the API documentation
4. Contact the development team

---

**Last Updated:** December 2024
**Version:** 1.0.0
**Author:** AI Assistant 