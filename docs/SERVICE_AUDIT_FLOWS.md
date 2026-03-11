# Alur Audit di Modul Service

Ada **dua fitur audit** yang berbeda di Service. Masing-masing punya tujuan dan data sendiri.

---

## 1. Unit Audit (Ajuan Perubahan)

- **URL:** `/service/unit-audit`
- **Tujuan:** Service **mengajukan perubahan** data unit/kontrak ke Marketing (bukan verifikasi fisik di lokasi).
- **Data:** Tabel `unit_audit_requests`. Statistik: Total Request, Menunggu Approval, Approved, Rejected.
- **Alur:**
  1. Pilih customer → tampil daftar unit di kontrak.
  2. Klik "Ajukan Perubahan" per unit → pilih jenis: Lokasi Berbeda, Tukar Unit, Tambah Unit, Tandai Spare, Unit Hilang, Lainnya.
  3. Submit → masuk ke Marketing untuk approve/reject.

**Kapan dipakai:** Ketika ada ketidaksesuaian data (unit di lokasi salah, mau tukar unit, tambah spare, dll.) dan perlu persetujuan Marketing.

---

## 2. Unit Verification (Verifikasi Fisik di Lokasi)

- **URL:** `/service/unit-verification`  
  *(Dulu ada "Audit per Lokasi" di `/service/unit-audit/location` — sudah digabung ke sini, link lama di-redirect.)*
- **Tujuan:** **Verifikasi fisik** unit di lokasi customer sesuai kontrak: bikin verifikasi per lokasi → print form → mekanik cek di lapangan → input hasil → kirim ke Marketing.
- **Data:** Tabel `unit_audit_locations` + `unit_audit_location_items`. Statistik: Total, Draft, Printed, In Progress, Menunggu Approval, Approved.
- **Alur:**
  1. **Pilih lokasi** — dari "Data Unit per Customer & Lokasi" atau tombol "Buat Verifikasi Baru".
  2. **Print form** — bawa ke lokasi untuk dicek mekanik (status Printed → In Progress).
  3. **Input hasil** — setelah cek lapangan, isi hasil (Actual vs Expected) di halaman input.
  4. **Kirim ke Marketing** — untuk approval. Setelah disetujui → status Approved.

**Kapan dipakai:** Untuk audit berkala/verifikasi unit di lokasi customer sesuai kontrak (no kontrak disamarkan, periode tampil, indikator audit terakhir & due re-audit).

---

## Ringkas

| Fitur            | Unit Audit              | Unit Verification              |
|-----------------|-------------------------|--------------------------------|
| **Fokus**       | Ajuan perubahan data    | Verifikasi fisik di lokasi     |
| **Tabel**       | `unit_audit_requests`    | `unit_audit_locations`          |
| **Entry menu**  | Unit Audit              | Unit Verification (satu saja)   |
| **Approval**    | Marketing                | Marketing                      |

Tidak ada duplikasi: dua fitur ini beda tujuan dan beda data. Verifikasi lokasi hanya punya **satu halaman** (Unit Verification); "Audit per Lokasi" sudah tidak dipakai dan di-redirect ke Unit Verification.
