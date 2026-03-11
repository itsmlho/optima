# Alur Audit — Modul Service

Ada **dua fitur audit** yang berbeda di Service. Masing-masing punya tujuan dan data sendiri.

---

## 1. Unit Audit

**URL:** `/service/unit-audit` | **Data:** `unit_audit_requests` (ajuan perubahan) + `unit_audit_locations` (verifikasi lokasi)

### Alur:

1. **Pilih Customer** — dropdown semua customer dengan unit aktif.
2. **Pilih Lokasi** — daftar lokasi customer masing-masing dengan **badge status audit** (Belum Audit / Menunggu Approval / Approved / Rejected) agar tidak double audit.
3. **Lihat Unit** — unit di lokasi yang dipilih (dari kontrak aktif).

### Aksi per Lokasi:

| Aksi | Keterangan |
|------|------------|
| **Print** | Print form untuk mekanik (Customer, Lokasi, daftar Unit, kolom isian di lapangan). Tidak perlu ada audit record sebelumnya. |
| **Verifikasi** | Buka modal — isi hasil audit lapangan: Sesuai / Tidak sesuai, alasan ketidaksesuaian (Lokasi Unit Salah, Unit Berbeda, Tandai Spare, Unit Tidak Ada), keterangan. Submit → **membuat record di `unit_audit_locations`** dengan status `PENDING_APPROVAL` langsung → Marketing dapat approve/reject. |
| **Tambah Unit** | Ajukan penambahan unit ke kontrak untuk lokasi ini → masuk `unit_audit_requests` dengan jenis `ADD_UNIT` → Marketing approve. |

### Riwayat Audit Request (bawah halaman):

Daftar `unit_audit_requests` (per unit, untuk perubahan seperti Lokasi Berbeda, Tukar Unit, Tandai Spare, dll.) dengan filter status dan detail.

---

## 2. Unit Verification

**URL:** `/service/unit-verification` | **Data:** `unit_audit_locations` + `unit_audit_location_items`

### Fungsi:

Menampilkan **verifikasi yang sudah dibuat dari Unit Audit**. Tidak ada tombol "Buat Verifikasi Baru" di sini.

**Tampilan:** Customer → Lokasi → Daftar Audit (accordion bertingkat)

Untuk setiap audit, aksi yang tersedia:

| Status | Print | Input Hasil | Kirim ke Marketing |
|--------|-------|-------------|-------------------|
| DRAFT, PRINTED, IN_PROGRESS, RESULTS_ENTERED | ✅ | ✅ | — (hanya RESULTS_ENTERED) |
| PENDING_APPROVAL | ✅ | — | — |
| APPROVED / REJECTED | ✅ | — | — |

**Untuk membuat verifikasi baru** → arahkan ke Unit Audit.

---

## Ringkas

| | Unit Audit | Unit Verification |
|--|---|---|
| **Tujuan** | Lakukan audit di lokasi, ajukan perubahan | Proses lanjutan verifikasi yang sudah dibuat |
| **Buat baru** | ✅ (Verifikasi, Tambah Unit) | ❌ |
| **Print form mekanik** | ✅ (tanpa audit record) | ✅ (dari audit record) |
| **Input hasil** | ❌ | ✅ |
| **Kirim ke Marketing** | Otomatis dari Verifikasi modal | ✅ RESULTS_ENTERED |
| **Data** | `unit_audit_requests` + `unit_audit_locations` | `unit_audit_locations` |

---

## Status Flow: unit_audit_locations

```
[Unit Audit → Verifikasi modal] → PENDING_APPROVAL → APPROVED / REJECTED

[Unit Audit → Buat dari halaman] → DRAFT → PRINTED → IN_PROGRESS → RESULTS_ENTERED → PENDING_APPROVAL → APPROVED / REJECTED
```

---

## Catatan Teknis

- **No. Kontrak dan No. PO** disamarkan di tampilan Service (format: 2 karakter awal + *** + 2 karakter akhir per segmen).
- **Periode** ditampilkan (tanggal mulai – tanggal berakhir) dan **Status Periode** untuk mekanik: *Aktif (tinggal X hari)*, *Tinggal X hari — perhatikan pengembalian* (≤30 hari), *Sudah lewat X hari — ajukan unit pulang* (kontrak sudah lewat).
- Badge lokasi di Unit Audit menunjukkan status audit **terbaru** per lokasi — jika `PENDING_APPROVAL` atau `APPROVED`, tombol Verifikasi berubah menjadi "Audit Ulang".
- Data grouped di Unit Verification diambil dari `unit_audit_locations` saja (bukan semua lokasi kontrak).
