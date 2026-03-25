# OPTIMA Multi-Language Guide

**Default language:** English (`en`)  
**Supported:** Indonesian (`id`), English (`en`)  
**Purpose:** Menghindari teks hardcode campur (ID/EN) dan memastikan semua teks UI bisa mengikuti pilihan bahasa user (tombol ID/EN).

---

## 1. Konvensi

- **Jangan** menulis teks langsung di view/JS (mis. `Simpan`, `Save`, `Berhasil`, `Notifikasi`).
- **Gunakan** key bahasa dan helper:
  - **PHP (View/Controller):** `lang('App.key')` atau `lang('Module.key')`
  - **JavaScript:** `window.lang('key')` (key yang ada di `language-helper.js` getTranslations)

---

## 2. File Bahasa

| File | Isi |
|------|-----|
| `app/Language/en/App.php` | Teks global (tombol, notifikasi, navigasi, dll.) — **English** |
| `app/Language/id/App.php` | Padanan Indonesia untuk key yang sama |
| `app/Language/en/Common.php` | Kata umum (CRUD, status, field) — **English** |
| `app/Language/id/Common.php` | Padanan Indonesia |
| `app/Language/{en,id}/Marketing.php` | Teks khusus modul Marketing |
| `app/Language/{en,id}/Service.php` | Teks khusus modul Service |
| … (Auth, Finance, Warehouse, Dashboard, Validation) | Per modul |

- **Tambah key baru:** selalu di **en** dan **id** dengan key yang **persis sama**, nilai terjemahan sesuai bahasa.

**Modules updated (multi-language scan):** Service, Marketing, Warehouse (index + quick access), **Admin** (`admin/index.php`), **Purchasing** (`purchasing.php` intro), **Finance** (`finance/index.php` dashboard), **Dashboard** (`dashboard/service.php`), **Settings** (`settings/index.php` header/tabs). New files: `Admin.php`, `Purchasing.php` (en/id). Keys also in `App.php`, `Finance.php`, `Dashboard.php`, `language-helper.js`.

---

## 3. Di Mana Menambah Key?

- **Teks global (header, notifikasi, tombol umum):** `App.php`
- **Kata umum (Simpan, Batal, Hapus, Cari, Status, dll.):** sudah banyak di `Common.php` / `App.php` — cek dulu, jangan duplikat
- **Teks khusus modul (mis. "Quotation", "SPK", "Work Order"):** file modul yang sesuai, mis. `Marketing.php`, `Service.php`

Contoh penambahan di `App.php`:

```php
// app/Language/en/App.php
'notifications' => 'Notifications',
'view_all_notifications' => 'View All Notifications',

// app/Language/id/App.php (key sama, nilai beda)
'notifications' => 'Notifikasi',
'view_all_notifications' => 'Lihat Semua Notifikasi',
```

---

## 4. Pemakaian di View (PHP)

```php
<!-- Salah: hardcode -->
<button>Simpan</button>
<title>Notifikasi</title>

<!-- Benar: pakai lang() -->
<button><?= lang('Common.save') ?></button>
<title><?= lang('App.notifications') ?></title>
```

- Namespace: `lang('App.xxx')` untuk key di `App.php`, `lang('Common.xxx')` untuk `Common.php`, `lang('Marketing.xxx')` untuk `Marketing.php`, dst.

---

## 5. Pemakaian di JavaScript

- `window.lang` diset dari `language-helper.js`; key-nya mengikuti objek terjemahan di dalam helper (biasanya tanpa prefix `App.`).
- Untuk teks yang di-render dari PHP (mis. label tombol di layout), bisa di-inject dari PHP:

```javascript
// Contoh: label dari PHP
var searchLabel = <?= json_encode(lang('App.search_placeholder')) ?>;
triggerBtn.innerHTML = '<span>' + searchLabel + '</span>';
```

- Untuk teks yang murni di JS, gunakan key yang sudah ada di `language-helper.js` (getTranslations) atau tambahkan di sana (id + en) lalu panggil `window.lang('key')`.

---

## 6. Checklist Saat Menambah / Edit Halaman

1. Cari teks yang masih hardcode (Indonesia atau Inggris) di view dan script.
2. Cek apakah key-nya sudah ada di `App.php` / `Common.php` / file modul; kalau belum, tambah di **en** dan **id**.
3. Ganti teks tersebut dengan `<?= lang('Namespace.key') ?>` (PHP) atau `window.lang('key')` / nilai dari PHP (JS).
4. Tes ganti bahasa lewat tombol ID/EN; pastikan teks ikut berubah.

---

## 7. Contoh yang Sudah Diperbaiki

- **Layout utama (`app/Views/layouts/base.php`):**
  - Judul dropdown notifikasi, tombol "Tandai Semua Dibaca", "Memuat notifikasi...", "Lihat Semua Notifikasi" → pakai `lang('App.notifications')`, `lang('App.mark_all_read')`, `lang('App.loading_notifications')`, `lang('App.view_all_notifications')`.
  - Pencarian cepat (Ctrl+K): label dan aria-label → `lang('App.search_placeholder')`, `lang('App.quick_search_title')`.

Semua key di atas ada di `App.php` (en + id). Untuk halaman lain, gunakan pola yang sama: tambah key di file bahasa yang sesuai, lalu ganti teks hardcode dengan `lang(...)`.
