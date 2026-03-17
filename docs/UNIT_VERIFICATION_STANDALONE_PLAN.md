# Unit Verification Standalone - Implementasi

## Alur (Sudah Diterapkan)

1. **Unit Audit** → User melakukan audit unit di lokasi (print form, input hasil)
2. **Unit Verification Page** (`/service/unit-verification`) → Menampilkan **list audit** yang perlu diverifikasi (file: `unit_audit_verification.php`)
3. **Verifikasi Unit** → Klik "Verifikasi Unit" → ke `/service/unit-verification/unit/{auditId}/1` (file: `unit_audit_verification_form.php`)
4. **Navigasi AJAX** → Next/Prev unit tanpa reload halaman, data dimuat via AJAX
5. **Simpan** → Langsung update inventory_unit tanpa approval, insert unit_verification_history (STANDALONE)

## File Views

| File | Deskripsi |
|------|-----------|
| `unit_audit_verification.php` | Index page - list audit yang perlu diverifikasi (renamed dari `unit_verification_index.php`) |
| `unit_audit_verification_form.php` | Form verifikasi dengan AJAX navigation (renamed dari `unit_verification_unit.php`) |
| `unit_verification.php` | WO modal - verifikasi dari Work Order completion |

## Yang Sudah Dikerjakan

- [x] **File rename** - `unit_verification_index.php` → `unit_audit_verification.php`, `unit_verification_unit.php` → `unit_audit_verification_form.php`
- [x] **verificationIndex** - Load `unit_audit_verification` (index page)
- [x] **verifyUnit** - Load `unit_audit_verification_form` dengan mode='audit', pass items array
- [x] **AJAX Navigation** - Next/Prev unit tanpa reload, data dimuat via `getUnitVerificationMasterData`
- [x] **saveUnitVerificationFromAudit** - Insert unit_verification_history dengan verification_type=STANDALONE, work_order_id=NULL
- [x] **UnitActivityService** - getVerificationEvents support work_order_id NULL, fallback verifier_name dari users
- [x] **Form variables** - Support `$mode` ('audit'|'wo'), `$embed` (true|false), `$items` array untuk navigasi

## Yang Belum (Future)

- **WO Modal Unification** - `unit_verification.php` (WO modal) masih terpisah. Field IDs berbeda (`#verify-*` vs `#rl-*`). Unifikasi memerlukan refactoring field IDs.
- **verified_by** - Saat ini pakai user_id. Tabel punya FK ke employees; jika user_id ≠ employee_id, bisa tambah kolom verified_by_user_id.

## Migration Wajib

Jalankan `PROD_20260314_modify_unit_verification_history.sql` agar work_order_id nullable dan verification_type tersedia.

## Struktur Form (unit_audit_verification_form.php)

```php
// Variables
$mode   = 'audit' | 'wo';     // Mode: audit standalone atau WO modal
$embed  = true | false;       // Jika true, skip layout extend (untuk modal)
$items  = [];                 // Array semua audit items untuk navigasi AJAX
$index  = 1;                  // Index item saat ini (1-based)

// AJAX Navigation
// - Next/Prev button call navigateUnit(delta)
// - Load data via /service/unit-audit/unit-master-data/{unitId}
// - Update form fields tanpa reload
```

## Routes

| Route | Controller | Deskripsi |
|-------|------------|-----------|
| `GET unit-verification` | `UnitAudit::verificationIndex` | Index page |
| `GET unit-verification/unit/{auditId}/{index}` | `UnitAudit::verifyUnit` | Form verifikasi |
| `GET unit-audit/unit-master-data/{unitId}` | `UnitAudit::getUnitVerificationMasterData` | AJAX data unit |
| `POST unit-audit/save-unit-verification` | `UnitAudit::saveUnitVerificationFromAudit` | Save verification |
