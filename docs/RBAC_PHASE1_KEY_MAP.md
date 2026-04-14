# RBAC Phase 1 Key Map (Marketing/Purchasing/Warehouse)

Dokumen ini menjadi peta key canonical vs legacy yang dipakai kode saat ini.
Fokus: modul `marketing`, `purchasing`, `warehouse`.

## Canonical Key Source

Sumber canonical utama di seed/migration permission:

- `app/Database/Seeds/ComprehensivePermissionSeeder.php`
- `app/Database/Seeds/DefaultRolePermissionSeeder.php`

Format key canonical: `module.page.action`

## Marketing

### Canonical (ditemukan di seeder)

- `marketing.customer.*`
- `marketing.customer_db.*`
- `marketing.quotation.*`
- `marketing.spk.*`
- `marketing.delivery.*`

### Legacy / Alias yang ditemukan di kode

- `marketing.contract.*` (legacy naming, canonical page di data cenderung `kontrak`/`quotation` flow)
- `marketing.kontrak.*` (dipakai di beberapa tempat, belum konsisten dengan data seeder lama)
- `marketing.access` (module-level broad gate)
- `can_create('marketing')` / `can_edit('marketing')` / `can_export('marketing')` (module-level)

### Mapping rekomendasi Phase 1

- Contract list/detail/create/export:
  - target: `marketing.kontrak.view|create|edit|delete|export`
  - kompatibilitas sementara: fallback ke `marketing.contract.*` dan module-level check.
- Quotation:
  - target: `marketing.quotation.*` (sudah canonical).

## Purchasing

### Canonical (ditemukan di seeder)

- `purchasing.po.*`
- `purchasing.po_sparepart.*`
- `purchasing.supplier.*`

### Legacy / Alias di kode

- `can_create('purchasing')`, `can_edit('purchasing')`, dst (module-level).
- `purchasing.supplier_management.edit` (berbeda dari pola seeder `purchasing.supplier.edit`).

### Mapping rekomendasi Phase 1

- PO create/edit/delete/export -> `purchasing.po.*`
- Supplier management -> `purchasing.supplier.*`
- `supplier_management` diperlakukan alias sementara.

## Warehouse

### Canonical (ditemukan di seeder)

- `warehouse.unit_inventory.*`
- `warehouse.attachment_inventory.*`
- `warehouse.sparepart_inventory.*`
- `warehouse.sparepart_usage.*`
- `warehouse.po_verification.*`

### Legacy / Alias di kode

- `warehouse.access` (module-level broad gate)
- `can_create('warehouse')` style via global/module helper
- export key non-canonical lama: `export.inventory_unit|attachment|battery|charger`

### Mapping rekomendasi Phase 1

- Unit/attachment CRUD pakai key `warehouse.<page>.<action>` terlebih dulu.
- Export key legacy tetap didukung sementara (compat fallback), sambil transisi ke key canonical page action (`warehouse.<page>.export`) bila ditambahkan di data permission.

## Catatan Implementasi Compat Fallback

1. Endpoint backend wajib cek key granular dulu.
2. Fallback legacy/module-level hanya sementara, diletakkan di helper (bukan tersebar di view).
3. Semua fallback yang kepakai harus dilog sebagai warning agar bisa dipensiunkan.
