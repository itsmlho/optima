# Rencana Perbaikan Audit Approval

## Ringkasan

Memperbaiki loading yang stuck, mencegah approval unit tanpa lokasi (logic error), reorder urutan tabel sesuai alur bisnis, dan error handling.

---

## 1. Penyebab Loading Terus "Memuat..." (ROOT CAUSE FIXED)

**Root cause:** View `audit_approval_location.php` memakai `$this->section('scripts')` sedangkan layout `base.php` hanya merender `renderSection('javascript')` dan `renderSection('script')`. Section `scripts` tidak pernah di-render → **JavaScript tidak pernah dijalankan** → fetch tidak jalan → tabel tetap "Memuat...".

**Solusi:** Ubah `section('scripts')` → `section('javascript')`.

---

## 2. Penyebab Sebelumnya (Permission)

**Root cause:** Tabel **Pengajuan Unit** memanggil API yang membutuhkan permission. User Marketing punya `view_marketing`, Service punya `view_service`. Route `service/unit_audit/*` hanya terima `view_service` → Marketing 403.

**Solusi (v2):**
- **Pengajuan Unit:** Pakai `service/unit_audit/*` (sama seperti halaman Audit Unit yang sudah jalan)
- **PermissionFilter:** Support OR — `view_service|view_marketing` = user cukup punya salah satu
- **Route service/unit_audit:** getAuditRequests, getAuditDetail → filter `permission:view_service|view_marketing`
- **Request Lokasi, Approve Audit Lokasi, Riwayat:** Route `marketing/unit-audit/*` dipindah ke dalam marketing group

---

## 3. Logic Error: Unit Di-approve Tanpa Lokasi

**Masalah:** ADD_UNIT untuk lokasi pending punya `kontrak_id = NULL`. Jika di-approve via Pengajuan Unit, insert ke `kontrak_unit` tidak jalan (kondisi `$request['kontrak_id']` gagal). Status jadi APPROVED tapi unit tidak masuk kontrak.

**Solusi:**

- Sembunyikan ADD_UNIT pending-location dari tabel Pengajuan Unit, ATAU
- Tampilkan dengan tombol Approve disabled + tooltip "Approve via Request Lokasi Baru"
- Backend `approveAndApply`: return error jika ADD_UNIT punya `customer_location_id` tapi `kontrak_id` null

---

## 4. Urutan Tabel (Sesuai Alur Bisnis)

**Urutan yang benar di halaman:**

1. **Request Lokasi Baru** — Prioritas tertinggi. Lokasi baru harus diapprove dulu.
2. **Pengajuan Unit** — Unit untuk lokasi yang sudah approved.
3. **Approve Audit Lokasi** — Hasil verifikasi per lokasi; sifatnya **review** (setelah lokasi ada).
4. **Riwayat Approval** — Read-only, sama seperti review.

**Catatan:** Approve Audit Lokasi dan Riwayat Approval sama-sama bersifat review — ditempatkan setelah Request Lokasi Baru dan Pengajuan Unit.

---

## 5. File yang Diubah


| File                                              | Perubahan                                                                                             |
| ------------------------------------------------- | ----------------------------------------------------------------------------------------------------- |
| `app/Views/marketing/audit_approval_location.php` | **FIX:** `section('scripts')` → `section('javascript')`; reorder card; fetch URL; .catch()           |
| `app/Config/Routes.php`                           | marketing/unit-audit/* di dalam group; service/unit_audit permission OR                               |
| `app/Filters/PermissionFilter.php`                | Support OR: `view_service\|view_marketing`                                                           |
| `app/Models/UnitAuditRequestModel.php`            | Validasi ADD_UNIT pending lokasi di approveAndApply                                                  |
| **Dihapus:** `app/Views/marketing/audit_approval.php` | File lama tidak dipakai (diganti audit_approval_location)                                         |


---

## Todos (SELESAI)

- [x] **fix-loading:** ROOT CAUSE: section('scripts')→section('javascript'); Route + URL + .catch()
- [x] **fix-logic:** ADD_UNIT pending lokasi — tombol disabled + backend validation
- [x] **reorder-tables:** Request Lokasi Baru → Pengajuan Unit → Approve Audit Lokasi → Riwayat

