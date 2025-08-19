# DataTable Template Usage Guide

Template DataTable yang konsisten untuk semua halaman dalam aplikasi CodeIgniter 4.

## Struktur File

- **Template File**: `app/Views/_partials/datatable_template.php`
- **CSS Styling**: `public/assets/css/pro-fixes.css` (section: CONSISTENT DATATABLE TEMPLATE STYLING)

## Cara Penggunaan

### 1. Basic Usage

```php
<?php
// Configure table settings
$table_id = 'myTable';
$table_title = 'Data Table Title';
$table_subtitle = 'Description of the table';

// Include the template
include APPPATH . 'Views/_partials/datatable_template.php';
?>
```

### 2. Dengan Filters

```php
<?php
$filters = [
    [
        'type' => 'select',
        'name' => 'status',
        'label' => 'Status',
        'placeholder' => 'All Status',
        'col_size' => '3',
        'options' => [
            'active' => 'Active',
            'inactive' => 'Inactive'
        ]
    ],
    [
        'type' => 'input',
        'name' => 'search',
        'label' => 'Search',
        'placeholder' => 'Enter keywords...',
        'col_size' => '4'
    ],
    [
        'type' => 'date',
        'name' => 'created_date',
        'label' => 'Created Date',
        'col_size' => '3'
    ],
    [
        'type' => 'daterange',
        'name' => 'date_range',
        'label' => 'Date Range',
        'col_size' => '6'
    ]
];
?>
```

### 3. Dengan Custom Actions

```php
<?php
$actions = [
    ['type' => 'filter', 'label' => 'Filter'],
    ['type' => 'search', 'label' => 'Search'],
    ['type' => 'export', 'label' => 'Export'],
    ['type' => 'add', 'label' => 'Add New'],
    [
        'type' => 'custom',
        'label' => 'Custom Action',
        'class' => 'btn-warning',
        'icon' => 'fas fa-star',
        'onclick' => 'customFunction()'
    ]
];
?>
```

### 4. Column Configuration

```php
<?php
$columns = [
    // Text column
    [
        'label' => 'Name',
        'type' => 'text',
        'field' => 'name',
        'width' => '200px'
    ],
    
    // Avatar column with title and subtitle
    [
        'label' => 'User',
        'type' => 'avatar',
        'field' => 'user_id',
        'avatar_field' => 'initials',
        'avatar_class' => 'bg-primary',
        'avatar_icon' => 'fas fa-user',
        'title_field' => 'full_name',
        'subtitle_field' => 'email'
    ],
    
    // Badge/Status column
    [
        'label' => 'Status',
        'type' => 'badge',
        'field' => 'status',
        'badge_class' => 'status-active'
    ],
    
    // Currency column
    [
        'label' => 'Price',
        'type' => 'currency',
        'field' => 'price',
        'currency_symbol' => '$'
    ],
    
    // Date column
    [
        'label' => 'Created',
        'type' => 'date',
        'field' => 'created_at',
        'date_format' => 'M d, Y'
    ]
];
?>
```

### 5. Data Structure

```php
<?php
$data = [
    [
        'name' => 'John Doe',
        'user_id' => 'USR001',
        'initials' => 'JD',
        'full_name' => 'John Doe',
        'email' => 'john@example.com',
        'status' => 'Active',
        'price' => 199.99,
        'created_at' => '2025-01-01'
    ],
    // ... more rows
];
?>
```

## Filter Types

### Select Filter
```php
[
    'type' => 'select',
    'name' => 'field_name',
    'label' => 'Filter Label',
    'placeholder' => 'All Options',
    'col_size' => '3',
    'options' => [
        'value1' => 'Label 1',
        'value2' => 'Label 2'
    ]
]
```

### Input Filter
```php
[
    'type' => 'input',
    'name' => 'search',
    'label' => 'Search',
    'placeholder' => 'Enter keywords...',
    'input_type' => 'text', // text, email, number, etc.
    'col_size' => '4'
]
```

### Date Filter
```php
[
    'type' => 'date',
    'name' => 'date_field',
    'label' => 'Date',
    'col_size' => '3'
]
```

### Date Range Filter
```php
[
    'type' => 'daterange',
    'name' => 'date_range',
    'label' => 'Date Range',
    'col_size' => '6'
]
```

## Column Types

### Text Column
```php
[
    'label' => 'Column Title',
    'type' => 'text',
    'field' => 'data_field',
    'width' => '100px' // optional
]
```

### Avatar Column
```php
[
    'label' => 'User',
    'type' => 'avatar',
    'field' => 'user_id',
    'avatar_field' => 'initials', // field containing avatar text
    'avatar_class' => 'bg-primary', // CSS class for avatar background
    'avatar_icon' => 'fas fa-user', // fallback icon if no avatar_field
    'title_field' => 'name', // main text
    'subtitle_field' => 'email' // secondary text (optional)
]
```

### Badge Column
```php
[
    'label' => 'Status',
    'type' => 'badge',
    'field' => 'status',
    'badge_class' => 'status-active' // status-active, status-inactive, status-pending, status-processing
]
```

### Currency Column
```php
[
    'label' => 'Price',
    'type' => 'currency',
    'field' => 'amount',
    'currency_symbol' => '$' // default: $
]
```

### Date Column
```php
[
    'label' => 'Created',
    'type' => 'date',
    'field' => 'created_at',
    'date_format' => 'M d, Y' // default: Y-m-d
]
```

## Action Types

### Default Actions
- `filter` - Filter toggle button
- `search` - Search button
- `export` - Export button
- `add` - Add new button

### Custom Actions
```php
[
    'type' => 'custom',
    'label' => 'Button Text',
    'class' => 'btn-warning', // Bootstrap button class
    'icon' => 'fas fa-star', // FontAwesome icon
    'onclick' => 'myFunction()' // JavaScript function
]
```

## CSS Classes

### Badge Status Classes
- `.status-active` - Green (success)
- `.status-inactive` - Red (danger)
- `.status-pending` - Yellow (warning)
- `.status-processing` - Blue (info)

### Avatar Background Classes
- `.bg-primary` - Blue
- `.bg-success` - Green
- `.bg-danger` - Red
- `.bg-warning` - Yellow
- `.bg-info` - Light blue
- `.bg-secondary` - Gray

## Complete Example

```php
<?php
// Configure table
$table_id = 'usersTable';
$table_title = 'User Management';
$table_subtitle = 'Manage system users and their permissions';

// Configure filters
$filters = [
    [
        'type' => 'select',
        'name' => 'status',
        'label' => 'Status',
        'placeholder' => 'All Status',
        'col_size' => '3',
        'options' => [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'pending' => 'Pending'
        ]
    ],
    [
        'type' => 'select',
        'name' => 'role',
        'label' => 'Role',
        'placeholder' => 'All Roles',
        'col_size' => '3',
        'options' => [
            'admin' => 'Administrator',
            'user' => 'User',
            'manager' => 'Manager'
        ]
    ],
    [
        'type' => 'input',
        'name' => 'search',
        'label' => 'Search',
        'placeholder' => 'Search users...',
        'col_size' => '3'
    ]
];

// Configure actions
$actions = [
    ['type' => 'filter', 'label' => 'Filter'],
    ['type' => 'export', 'label' => 'Export Users'],
    ['type' => 'add', 'label' => 'Add User']
];

// Configure columns
$columns = [
    [
        'label' => 'User',
        'type' => 'avatar',
        'field' => 'id',
        'avatar_field' => 'initials',
        'avatar_class' => 'bg-primary',
        'title_field' => 'name',
        'subtitle_field' => 'email'
    ],
    [
        'label' => 'Role',
        'type' => 'badge',
        'field' => 'role',
        'badge_class' => 'status-active'
    ],
    [
        'label' => 'Status',
        'type' => 'badge',
        'field' => 'status',
        'badge_class' => 'status-active'
    ],
    [
        'label' => 'Created',
        'type' => 'date',
        'field' => 'created_at',
        'date_format' => 'M d, Y'
    ]
];

// Sample data
$data = [
    [
        'id' => 1,
        'initials' => 'JD',
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'role' => 'Admin',
        'status' => 'Active',
        'created_at' => '2025-01-01'
    ]
];

// Include template
include APPPATH . 'Views/_partials/datatable_template.php';
?>
```

## Notes

1. **Template ID**: Selalu gunakan ID unik untuk setiap table (`$table_id`)
2. **Responsive**: Template sudah responsive dan mobile-friendly
3. **Dark Theme**: Sudah support dark theme otomatis
4. **Tooltips**: Action buttons otomatis menggunakan tooltips
5. **DataTables**: Template compatible dengan DataTables.js plugin
6. **Consistent Styling**: Semua table menggunakan styling yang sama 