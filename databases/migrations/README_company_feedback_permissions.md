# Masukan & Keluh Kesah — izin HRD

Setelah menjalankan migrasi [`2026_04_29_company_feedback.sql`](2026_04_29_company_feedback.sql), tabel `company_feedback` dan baris permission berikut tersedia:

| key_name | Keterangan |
|----------|------------|
| `hr.feedback.navigation` | Hak akses menu navigasi (sidebar) |
| `hr.feedback.view` | Melihat halaman daftar dan API DataTables |

Untuk HRD melihat data di **Administration → Role Permissions** (atau layar permission yang dipakai organisasi), berikan kedua permission di atas pada role HRD.

### Menggunakan UI aplikasi

1. Login sebagai admin / pengguna yang boleh mengelola role.
2. Buka pengaturan **Role Permissions** (misalnya `/permission-management/role-permissions`).
3. Pilih role HRD (atau buat role khusus).
4. Centang **Masukan & Keluh Kesah** / kedua key di atas, lalu simpan.

### Menggunakan SQL (opsional)

Sesuaikan slug role (`hrd`, `hr`, dll.) dan jalankan di MySQL **setelah** memastikan ID permission ada:

```sql
SET @pid_nav := (SELECT id FROM permissions WHERE key_name = 'hr.feedback.navigation' LIMIT 1);
SET @pid_view := (SELECT id FROM permissions WHERE key_name = 'hr.feedback.view' LIMIT 1);
SET @role_id := (SELECT id FROM roles WHERE slug = 'hrd' LIMIT 1);

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT @role_id, @pid_nav, 1, NOW() FROM DUAL WHERE @role_id IS NOT NULL AND @pid_nav IS NOT NULL;

INSERT IGNORE INTO role_permissions (role_id, permission_id, granted, assigned_at)
SELECT @role_id, @pid_view, 1, NOW() FROM DUAL WHERE @role_id IS NOT NULL AND @pid_view IS NOT NULL;
```

Jika tabel `role_permissions` memiliki kolom `assigned_by`, tambahkan nilai sesuai kebijakan Anda atau gunakan UI.

### URL publik untuk dibagikan

- Form pengiriman (tanpa login): `/masukan-keluhan`  
  Contoh lengkap: `https://domain-anda/masukan-keluhan`

### Halaman internal

- Daftar untuk pengguna berizin: `/hr/masukan-keluhan`
