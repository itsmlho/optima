# RBAC Phase 1 Smoke Test Report

Tanggal: 2026-04-14

## Scope

Perubahan yang diuji:

- Hardening helper permission:
  - `app/Helpers/rbac_helper.php`
  - `app/Controllers/BaseController.php`
- Hardening endpoint backend:
  - `app/Controllers/Marketing.php`
  - `app/Controllers/Kontrak.php`
  - `app/Controllers/Quotation.php`
  - `app/Controllers/Purchasing.php`
  - `app/Controllers/Warehouse.php`
  - `app/Controllers/Warehouse/AttachmentInventoryController.php`
  - `app/Controllers/Warehouse/UnitMovementController.php`
  - `app/Controllers/WorkOrderController.php`
- UI gating alignment:
  - `app/Views/marketing/kontrak.php`
  - `app/Views/marketing/kontrak_edit.php`
  - `app/Views/purchasing/purchasing.php`
  - `app/Views/layouts/sidebar_new.php`

## Hasil Uji Otomatis

### 1) Syntax smoke (`php -l`)

Semua file yang diubah lolos `php -l` (no syntax error).

### 2) Lint smoke

Tidak ada lint error baru pada file yang diubah.

Catatan warning lama yang masih ada (pre-existing):

- `app/Controllers/WorkOrderController.php` (3 warning tipe argumen)
- `app/Controllers/Warehouse/AttachmentInventoryController.php` (1 warning return type)
- `app/Controllers/Kontrak.php` (2 warning parameter type untuk logging)

### 3) Guard presence verification

Guard permission granular terdeteksi pada method prioritas:

- Marketing: `createQuotation`, `storeQuotation`, `createProspect`, `createSPKFromQuotation`, `spkDelete`, `diDelete`, `createContract`, `createSPK`
- Kontrak: `store`, `update`, `delete`
- Quotation: `store`, `update`, `delete`, `markAsDeal`
- Purchasing: `deletePO`, `createDelivery`
- Warehouse: `deleteUnit`
- Warehouse Attachment: `deleteAttachment`
- Warehouse Movement: `checkAccess` (view/create/edit/delete)
- WorkOrder: `store`, `update`, `delete`

### 4) Permission integrity tooling

Berhasil generate key usage dari kode:

- `docs/RBAC_PERMISSION_CODE_KEYS.txt`
- `docs/RBAC_PERMISSION_CODE_KEYS.sql`

Jumlah key dari code scan: 154.

## Fallback Warning Transition

Logging warning fallback kompatibilitas aktif di:

- `app/Helpers/rbac_helper.php`
  - log key: `[RBAC_COMPAT] Module fallback active for '{module}' ...`

Tujuan: mendeteksi modul yang masih lolos lewat fallback module-level agar bisa dipindahkan ke key granular secara bertahap.

## Manual Role Matrix (To Execute in UAT)

Gunakan minimal 3 role: `staff`, `head`, `admin`.

Per modul (Marketing/Purchasing/Warehouse/Service), verifikasi:

1. User tanpa permission `create`:
   - tombol create tidak tampil,
   - endpoint create return 403.
2. User punya `edit` tapi tidak punya `delete`:
   - tombol edit tampil, delete tidak tampil,
   - endpoint delete return 403.
3. User dengan permission `export`:
   - tombol export tampil,
   - export endpoint berhasil.
4. User override (`user_permissions`) revoke:
   - meski role grant, endpoint harus deny.

## SQL Checklist Pasca Deploy

Jalankan:

- `docs/RBAC_PERMISSION_CODE_KEYS.sql`
- `docs/RBAC_PERMISSION_INTEGRITY_AUDIT.sql`

Fokus hasil:

- `missing_in_db` harus turun bertahap,
- `unused_in_code` dipakai untuk cleanup,
- konflik override grant/revoke diselesaikan per user.
