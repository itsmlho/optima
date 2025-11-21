# Testing: Cascading Dropdown Unit Form

## 📋 Overview
Testing untuk dropdown bertingkat pada form tambah unit di Create PO Modal.

## 🔗 Cascading Flow

```
Departemen → Jenis Unit → Brand → Model
   (col-6)      (col-6)    (col-4)  (col-4)
```

## ✅ Testing Steps

### 1. **Open Modal**
```
1. Buka halaman Purchasing
2. Klik tombol "Buat PO"
3. Modal Create PO terbuka
4. Klik tombol "Tambah Unit"
5. Modal Item Detail terbuka dengan form unit
```

**Expected Result:**
- ✅ Form unit ter-load dengan baik
- ✅ Dropdown "Jenis Unit" disabled (abu-abu)
- ✅ Dropdown "Model" disabled (abu-abu)
- ✅ Console log: "✅ Unit form loaded and Select2 initialized"

---

### 2. **Test Departemen → Jenis**
```
1. Pilih Departemen (contoh: "Electric Forklift")
```

**Expected Result:**
- ✅ Console log: "📍 Departemen changed: [ID]"
- ✅ Dropdown "Jenis Unit" show "Loading..."
- ✅ AJAX call ke `/purchasing/api/get-tipe-units?departemen=[ID]`
- ✅ Console log: "✅ Jenis loaded: {success: true, data: [...]}"
- ✅ Dropdown "Jenis Unit" ter-populate dengan data
- ✅ Dropdown "Jenis Unit" enabled (tidak disabled lagi)
- ✅ Options diurutkan alphabetically
- ✅ Jika departemen "Electric", battery field muncul

**Test Cases:**
| Departemen | Expected Jenis Options |
|---|---|
| Electric Forklift | 3 Wheels, 4 Wheels, Reach Truck, dll |
| Diesel Forklift | 3 Wheels, 4 Wheels, dll |
| LPG Forklift | 3 Wheels, 4 Wheels, dll |

---

### 3. **Test Brand → Model**
```
1. Pilih Brand (contoh: "Toyota")
```

**Expected Result:**
- ✅ Console log: "🏷️ Merk changed: [ID]"
- ✅ Dropdown "Model" show "Loading..."
- ✅ AJAX call ke `/purchasing/api/get-model-units?merk=[BRAND_NAME]`
- ✅ Console log: "✅ Models loaded: {success: true, data: [...]}"
- ✅ Dropdown "Model" ter-populate dengan data
- ✅ Dropdown "Model" enabled

**Test Cases:**
| Brand | Expected Model Options |
|---|---|
| Toyota | 8FBE15, 8FBE18, 8FBN25, dll |
| Mitsubishi | FG25N, FG30N, dll |
| Komatsu | FD30-17, FD40-11, dll |

---

### 4. **Test Reset Dropdown**
```
1. Pilih Departemen → Jenis ter-populate
2. Ubah Departemen ke yang lain
```

**Expected Result:**
- ✅ Dropdown "Jenis Unit" reset ke "Loading..."
- ✅ Dropdown "Jenis Unit" reload dengan data baru sesuai departemen

```
1. Pilih Brand → Model ter-populate
2. Ubah Brand ke yang lain
```

**Expected Result:**
- ✅ Dropdown "Model" reset ke "Loading..."
- ✅ Dropdown "Model" reload dengan data baru sesuai brand

---

### 5. **Test Clear Selection**
```
1. Pilih Departemen yang sudah ada
2. Clear/Hapus pilihan Departemen (pilih "Pilih Departemen...")
```

**Expected Result:**
- ✅ Dropdown "Jenis Unit" reset ke "Pilih Departemen Dulu..."
- ✅ Dropdown "Jenis Unit" disabled kembali

```
1. Pilih Brand yang sudah ada
2. Clear/Hapus pilihan Brand
```

**Expected Result:**
- ✅ Dropdown "Model" reset ke "Pilih Brand Dulu..."
- ✅ Dropdown "Model" disabled kembali

---

### 6. **Test Complete Form Submission**
```
1. Pilih semua required fields:
   - Departemen: "Electric Forklift"
   - Jenis Unit: "3 Wheels"
   - Brand: "Toyota"
   - Model: "8FBE15"
   - Tahun: "2025"
   - Kapasitas: "1.5 Ton"
   - Kondisi: "Baru"
   - Quantity: "1"
2. Klik "Tambahkan ke PO"
```

**Expected Result:**
- ✅ Validasi pass (tidak ada error "Harap lengkapi...")
- ✅ Item masuk ke tabel dengan deskripsi:
  ```
  Toyota 8FBE15 | Electric Forklift - 3 Wheels | 1.5 Ton | Tahun 2025 (Baru)
  ```
- ✅ Modal Item Detail tertutup
- ✅ Notification success muncul

---

### 7. **Test Error Handling**

#### Test API Error
```
1. Disable network atau simulate API error
2. Pilih Departemen
```

**Expected Result:**
- ✅ Console log: "❌ Error loading jenis: [error]"
- ✅ Dropdown "Jenis Unit" show "Error loading data"

#### Test Empty Data
```
1. Pilih Departemen yang tidak punya data jenis
```

**Expected Result:**
- ✅ Console log: "⚠️ No jenis data found"
- ✅ Dropdown "Jenis Unit" show "Tidak ada data"

---

## 🐛 Known Issues & Solutions

### Issue 1: Dropdown tidak ter-populate
**Solution:**
- Check console log untuk error AJAX
- Verify API endpoint: `/purchasing/api/get-tipe-units`
- Check database table `tipe_unit` ada data

### Issue 2: Select2 tidak responsive setelah update
**Solution:**
- Dropdown di-reinitialize setelah content update
- Code: `$jenis.select2('destroy').select2({...})`

### Issue 3: Disabled state tidak terlihat di Select2
**Solution:**
- Trigger `change.select2` event setelah set disabled
- Code: `$jenis.prop('disabled', true).trigger('change.select2')`

---

## 🔍 Debug Console Logs

Saat testing, perhatikan console logs berikut:

```javascript
🔧 Initializing Unit Dropdowns (Simplified)...
✅ Unit form loaded and Select2 initialized

// Saat pilih Departemen:
📍 Departemen changed: 1
✅ Jenis loaded: {success: true, data: [...]}

// Saat pilih Brand:
🏷️ Merk changed: Toyota
✅ Models loaded: {success: true, data: [...]}
```

---

## 📊 Test Checklist

- [ ] Modal terbuka dengan benar
- [ ] Form unit ter-load
- [ ] Jenis dropdown disabled saat awal
- [ ] Model dropdown disabled saat awal
- [ ] Departemen → Jenis cascading bekerja
- [ ] Brand → Model cascading bekerja
- [ ] Select2 berfungsi dengan baik
- [ ] Reset dropdown saat change parent
- [ ] Clear selection reset ke disabled
- [ ] Battery field muncul untuk Electric
- [ ] Form validation bekerja
- [ ] Submit data berhasil
- [ ] Item display di tabel benar
- [ ] Error handling bekerja
- [ ] Console logs sesuai

---

## 🔗 Related Files

- `/app/Views/purchasing/purchasing.php` - Main JavaScript
- `/app/Views/purchasing/forms/unit_form_fragment.php` - Form HTML
- `/app/Controllers/Purchasing.php` - API endpoints
- `/app/Models/TipeUnitModel.php` - Data source

---

**Last Updated:** 2025-10-10  
**Tested By:** _[Your Name]_  
**Status:** ✅ Ready for Testing

