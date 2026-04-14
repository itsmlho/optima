# RBAC UAT Role Matrix Checklist

Tanggal: 2026-04-14  
Tujuan: verifikasi bahwa UI gating + backend guard sudah konsisten setelah hardening RBAC.

## Scope UAT

- Marketing: quotation, kontrak, SPK/DI
- Purchasing: PO
- Warehouse: unit inventory, attachment, unit movement
- Service: work order

## Prasyarat

- Siapkan 3 akun uji:
  - `staff_*`
  - `head_*`
  - `admin_*`
- Pastikan data uji tersedia:
  - 1 record quotation
  - 1 record kontrak
  - 1 record PO
  - 1 inventory unit + 1 attachment
  - 1 work order
- Cache browser bersih / hard refresh sebelum test.

## Matrix Uji Wajib

Untuk setiap modul di bawah, jalankan pola yang sama:

1. Login sebagai `staff_*` (grant minimal):
   - tombol `Create` hanya muncul jika punya key `*.create`;
   - jika tombol tidak muncul, paksa akses endpoint create -> harus `403`.
2. Login sebagai `head_*`:
   - `Create/Edit` muncul sesuai grant;
   - `Delete` hanya jika key `*.delete` granted.
3. Login sebagai `admin_*`:
   - semua action prioritas tampil dan endpoint berhasil.

## Test Case Detail

### A. Marketing - Quotation

- **Create**
  - UI: tombol create quotation
  - Endpoint: submit form create quotation
  - Expected:
    - tanpa `marketing.quotation.create` -> deny/403
    - dengan grant -> sukses create
- **Edit/Delete**
  - Endpoint update/delete quotation
  - Expected:
    - edit but no delete -> update ok, delete 403

### B. Marketing - Kontrak

- **Create**
  - UI: tombol create kontrak di list kontrak
  - Endpoint: store kontrak
  - Expected:
    - cek key `marketing.kontrak.create` (atau alias transisi)
- **Edit/Delete**
  - UI: save di halaman edit kontrak hanya muncul jika boleh edit
  - Endpoint: update/delete kontrak wajib deny saat tanpa grant

### C. Marketing - SPK/DI Action

- **SPK create/delete**
  - Expected key: `marketing.spk.create` / `marketing.spk.delete`
- **DI delete**
  - Expected key: `marketing.delivery.edit/delete` sesuai guard transisi

### D. Purchasing - PO

- **Create delivery**
  - Endpoint create delivery harus 403 jika tanpa grant edit/create PO
- **Delete PO**
  - Endpoint delete PO hanya untuk role dengan key delete/edit PO sesuai policy

### E. Warehouse - Inventory/Movement

- **Delete unit**
  - Endpoint delete unit -> 403 jika tanpa key `warehouse.unit_inventory.delete/edit`
- **Delete attachment**
  - Endpoint delete attachment -> 403 jika tanpa key attachment delete/edit
- **Create/Edit/Delete movement**
  - Uji flow movement via `UnitMovementController` per role

### F. Service - Work Order

- **Create/Update/Delete WO**
  - Expected key: `service.work_order.*` (alias `service.workorder.*` masih transisi)
  - Pastikan endpoint deny saat grant tidak ada, walaupun user bisa akses menu.

## Validasi Teknis Tambahan

1. **Network check**
   - Endpoint yang ditolak harus konsisten return `403`.
2. **UI check**
   - Tombol/menu sensitive tidak muncul jika permission tidak ada.
3. **DB check**
   - Tidak ada perubahan data jika request ditolak.

## Monitoring Transisi (Compat Fallback)

Selama UAT, monitor log aplikasi untuk warning:

- `[RBAC_COMPAT] Module fallback active for ...`

Interpretasi:

- Jika warning masih muncul di modul tertentu -> modul itu belum siap strict mode.
- Jika warning tidak muncul di modul yang diuji selama beberapa hari -> kandidat strict mode.

## Exit Criteria

UAT dianggap lulus bila:

- Semua test deny/allow sesuai matrix role
- Tidak ada regresi tombol hilang untuk role yang semestinya punya akses
- Tidak ada bypass endpoint (UI hide tapi endpoint tetap lolos)
- Log fallback turun signifikan atau nol pada modul phase-1
