# Optima Workspace Instructions

## Project Overview
**Optima** adalah sistem informasi manajemen berbasis web menggunakan **CodeIgniter 4** dengan **PHP 8.1+**. Sistem ini mengelola unit kendaraan, kontrak, purchasing, marketing, dan employee management.

## Technology Stack
- **Framework**: CodeIgniter 4
- **PHP Version**: 8.1+
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap 5, jQuery, DataTables
- **Styling**: Custom Optima theme (optima-pro.css)
- **PDF Generation**: DomPDF
- **Excel**: PhpSpreadsheet

## Architecture Patterns

### MVC Structure (CodeIgniter 4)
```
app/
├── Controllers/     # Business logic handlers
├── Models/          # Database interactions (extends BaseModel)
├── Views/           # HTML templates
├── Services/        # Complex business logic layer
├── Libraries/       # Reusable components
└── Helpers/         # Utility functions
```

### Naming Conventions
- **Controllers**: PascalCase, suffix `Controller` (e.g., `UnitController.php`)
- **Models**: PascalCase, suffix `Model` (e.g., `UnitModel.php`)
- **Views**: snake_case (e.g., `unit_list.php`, `contract_detail.php`)
- **Methods**: camelCase (e.g., `getActiveUnits()`, `updateContract()`)
- **Database Tables**: snake_case (e.g., `units`, `unit_contracts`, `marketing_quotations`)

### CodeIgniter 4 Specifics
- Always use dependency injection in constructors
- Use `BaseModel` for models with Query Builder
- Implement validation rules in Models or Controllers
- Use `ResponseInterface` for JSON responses
- Leverage built-in Security features (CSRF, XSS filtering)

## Database Schema Key Tables
- **units**: Master data kendaraan (polisi, merk, model, tahun, status)
- **unit_contracts**: Kontrak sewa unit dengan customer
- **contract_renewals**: History perpanjangan kontrak
- **marketing_quotations**: Penawaran dari marketing
- **purchasing**: Data pembelian unit baru
- **employees**: Data karyawan
- **user**: User authentication (username, password, role)

## Critical Coding Rules

### 1. CSRF Token Handling
**ALWAYS** include CSRF token in AJAX requests:
```javascript
$.ajax({
    url: base_url + 'controller/method',
    type: 'POST',
    data: {
        [window.csrfTokenName]: window.csrfTokenValue,
        // ... other data
    }
});
```

### 2. Database Queries
- **NEVER** use raw SQL with user input
- Use Query Builder or prepared statements
- Always validate and sanitize inputs

```php
// ✅ GOOD - Query Builder
$this->db->table('units')
    ->where('unit_id', $id)
    ->get();

// ❌ BAD - Raw SQL with concatenation
$this->db->query("SELECT * FROM units WHERE unit_id = " . $id);
```

### 3. Error Handling
```php
try {
    // Database operation
    return $this->respond(['success' => true, 'data' => $result]);
} catch (\Exception $e) {
    log_message('error', $e->getMessage());
    return $this->fail('Operasi gagal: ' . $e->getMessage());
}
```

### 4. Response Format
Standardize JSON responses:
```php
// Success
return $this->respond([
    'success' => true,
    'message' => 'Data berhasil disimpan',
    'data' => $result
]);

// Error
return $this->fail([
    'success' => false,
    'message' => 'Terjadi kesalahan'
], 400);
```

### 5. DataTables Integration
Use centralized configuration from `optima-datatable-config.js`:
```javascript
const tableConfig = {
    ...OptimaDataTable.getDefaultConfig(),
    ajax: base_url + 'api/getData',
    columns: [/* ... */]
};
$('#myTable').DataTable(tableConfig);
```

## Production Deployment Rules

### Pre-Deployment Checklist
Before ANY production deployment, MUST verify:
1. ✅ All CSRF tokens implemented in forms/AJAX
2. ✅ Database migrations tested in staging
3. ✅ No `dd()`, `var_dump()`, or debug code
4. ✅ Error reporting disabled in production config
5. ✅ Backup database before schema changes
6. ✅ Review `PRODUCTION_DEPLOYMENT_READY.md`

### Deployment Files
- **NEVER** directly edit production database
- Use migration files in `databases/migrations/`
- Follow checklist in `DEPLOYMENT_CHECKLIST.md`
- Test SQL scripts with `prepare_production_sql.bat` first

### Configuration Files
- Development: `app/Config/Development/`
- Production: Use environment variables (.env)
- Database config: `app/Config/Database.php`

## Code Quality Standards

### PHP Code Style
- PSR-12 compliant
- Use type hints for parameters and return types
- Document complex logic with PHPDoc comments
- Keep methods under 50 lines when possible

### JavaScript Code Style
- Use ES6+ features (const, let, arrow functions)
- Avoid global variables (use modules or namespaces)
- Handle promises with async/await
- Always check AJAX responses for success/error

### Documentation
- **Bilingual**: Code comments in English, user messages in Indonesian
- Update relevant `.md` files in `docs/` when changing features
- Keep `RECENT_CHANGES_*.md` updated for tracking

## Common Anti-Patterns to Avoid

❌ **DON'T**:
- Mix business logic in Views
- Use `SELECT *` in production code
- Ignore validation errors
- Hard-code URLs (use `base_url()` helper)
- Forget to check user permissions
- Use deprecated jQuery methods (`.live()`, `.bind()`)
- Modify production database directly without backup

✅ **DO**:
- Validate all inputs (server-side AND client-side)
- Use CodeIgniter's built-in security features
- Implement proper error logging
- Test queries with sample data first
- Use transactions for multi-table operations
- Follow existing code patterns in the project

## Testing Guidelines
- All database changes tested locally first
- Use `tests/` directory for PHPUnit tests
- Manual testing checklist in `docs/PHASE1A_TEST_EXECUTION_REPORT.md`
- Verify CSRF tokens work after form submissions

## File Organization
- Public assets: `public/assets/` (css, js, images)
- Views: `app/Views/` (organized by module)
- Uploads: `writable/uploads/`
- Logs: `writable/logs/`
- Cache: `writable/cache/`

## Performance Optimization
- Cache frequently accessed data
- Optimize DataTables with server-side processing for large datasets
- Use eager loading to prevent N+1 queries
- Minify CSS/JS for production
- Enable query caching for static data

## Security Best Practices
- Always use parameterized queries
- Validate file uploads (type, size, extension)
- Sanitize output with `esc()` helper
- Enable HTTPS in production
- Use strong password hashing (Bcrypt)
- Implement proper session management

## Support & References
- CodeIgniter 4 Docs: https://codeigniter.com/user_guide/
- Project Documentation: `docs/` folder
- Database Schema: `docs/DATABASE_SCHEMA.md`
- Marketing Module: `docs/MARKETING_MODULE_AUDIT_REPORT.md`

---

**Last Updated**: March 6, 2026
**Project Phase**: Phase 1A (Production Ready)
