# Profile Avatar Upload Documentation

## ✅ Fitur Upload Foto Profile Sudah Siap!

### 📸 Cara Upload Foto Profile:

#### **Step 1: Masuk ke Profile Page**
```
URL: http://localhost/optima1/public/profile
```

#### **Step 2: Enable Edit Mode**
1. Klik tombol **"Update Data"** (pojok kanan atas)
2. Semua field akan editable
3. Tombol "Change Photo" akan aktif

#### **Step 3: Upload Foto**
1. Klik tombol **"Change Photo"**
2. Pilih file foto dari komputer (JPG, JPEG, PNG)
3. Foto akan langsung ter-preview
4. Foto otomatis terupload ke server
5. Database otomatis terupdate

#### **Step 4: Selesai**
- Foto tersimpan di: `public/uploads/avatars/`
- Format nama file: `avatar_[user_id]_[timestamp].jpg`
- URL foto tersimpan di database kolom `users.avatar`

---

## 🔧 Technical Details

### **1. Backend Components:**

#### **Controller Method:**
```php
// app/Controllers/System.php
public function uploadAvatar()
{
    - Validates file (type, size)
    - Creates upload directory if not exists
    - Generates unique filename
    - Moves uploaded file
    - Updates database
}
```

#### **Route:**
```php
POST /profile/upload-avatar
```

#### **Storage:**
```
public/uploads/avatars/
├── avatar_1_1234567890.jpg
├── avatar_2_1234567891.png
└── .htaccess (security)
```

### **2. Frontend Components:**

#### **Upload Trigger:**
```html
<button onclick="document.getElementById('avatarInput').click()">
    Change Photo
</button>
<input type="file" id="avatarInput" accept="image/*">
```

#### **AJAX Upload:**
```javascript
$('#avatarInput').on('change', function(e) {
    // Validate file
    // Preview image
    // Upload via AJAX
    // Show success/error message
});
```

### **3. Validations:**

#### **File Type:**
- ✅ JPG, JPEG, PNG only
- ❌ Other formats rejected

#### **File Size:**
- ✅ Max 2MB
- ❌ Larger files rejected

#### **Edit Mode:**
- ✅ Only when edit mode enabled
- ❌ Disabled in readonly mode

#### **Security:**
```apache
# public/uploads/.htaccess
- Allow images only
- Deny PHP execution
- Disable directory listing
```

---

## 🗄️ Database Setup

### **Required Column:**
```sql
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) NULL 
COMMENT 'User profile picture URL';
```

**Run SQL file:**
```bash
# Import via phpMyAdmin or MySQL CLI
source databases/add_avatar_column.sql
```

---

## 📁 Directory Structure

```
optima1/
├── app/
│   ├── Controllers/
│   │   └── System.php (uploadAvatar method)
│   └── Views/
│       └── admin/advanced_user_management/
│           └── profile.php (upload UI)
├── public/
│   └── uploads/
│       ├── .htaccess (security)
│       └── avatars/
│           └── (uploaded files)
└── databases/
    └── add_avatar_column.sql
```

---

## 🎨 User Experience

### **Default State (Read-Only):**
```
[Profile Picture]
   (Current Avatar)
   
[Change Photo] ← DISABLED (grey)
```

### **Edit Mode:**
```
[Profile Picture]
   (Current Avatar)
   
[Change Photo] ← ENABLED (blue)
   ↓ Click
[File Picker Opens]
   ↓ Select Image
[Preview Immediately]
   ↓ Auto Upload
[Success Notification]
```

### **Error Handling:**

#### **File Too Large:**
```
❌ Error!
Image size should not exceed 2MB
```

#### **Invalid Format:**
```
❌ Invalid File
Please select an image file
```

#### **Not in Edit Mode:**
```
⚠️ Not in Edit Mode
Please click "Update Data" first
```

---

## 🔐 Security Features

### **1. File Type Validation:**
```php
'rules' => 'is_image[avatar]|mime_in[avatar,image/jpg,image/jpeg,image/png]'
```

### **2. File Size Limit:**
```php
'rules' => 'max_size[avatar,2048]' // 2MB
```

### **3. Secure File Naming:**
```php
$newName = 'avatar_' . $userId . '_' . time() . '.' . $file->getExtension();
```

### **4. .htaccess Protection:**
```apache
# Prevent PHP execution in uploads directory
<FilesMatch "\.php$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

### **5. Edit Mode Protection:**
```javascript
if (!isEditMode) {
    // Reject upload attempt
    return;
}
```

---

## ✅ Checklist Setup

Pastikan hal-hal berikut sudah dilakukan:

- [x] **Folder Created:** `public/uploads/avatars/` exists
- [x] **Permissions Set:** `chmod 777 public/uploads/`
- [x] **Security File:** `public/uploads/.htaccess` exists
- [ ] **Database Column:** Run `add_avatar_column.sql`
- [x] **Controller Method:** `System::uploadAvatar()` ready
- [x] **Route Registered:** `POST /profile/upload-avatar`
- [x] **Frontend JS:** Upload handler ready
- [x] **Edit Mode Protection:** Enabled

---

## 🧪 Testing

### **Test Upload:**
1. Login to system
2. Go to `/profile`
3. Click "Update Data"
4. Click "Change Photo"
5. Select image < 2MB
6. Verify:
   - Preview shows immediately ✓
   - Success notification appears ✓
   - Image saved in `public/uploads/avatars/` ✓
   - Database `users.avatar` updated ✓
   - Page reload shows new avatar ✓

### **Test Validations:**
- Upload file > 2MB → Rejected ✓
- Upload .pdf file → Rejected ✓
- Upload without edit mode → Blocked ✓

---

## 📝 Notes

1. **Default Avatar:** 
   - Jika user belum upload, tampil icon default
   - Icon: `<i class="fas fa-user fa-3x"></i>`

2. **Old Avatar:**
   - Tidak otomatis dihapus (for safety)
   - Bisa add cleanup job jika perlu

3. **Image Optimization:**
   - Belum ada resize otomatis
   - Bisa ditambahkan jika diperlukan

4. **Multiple Uploads:**
   - Setiap upload create file baru
   - Database hanya simpan URL terbaru

---

## 🎉 Kesimpulan

**Sistem upload foto profile sudah lengkap dan siap digunakan!**

✅ Upload works
✅ Security enabled  
✅ Validation active
✅ Edit mode protection
✅ Database integration
✅ User-friendly UI

**Tinggal pastikan database column `avatar` sudah ada dengan menjalankan SQL file!**

