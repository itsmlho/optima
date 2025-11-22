# Custom Permissions Feature

**Tanggal:** 2025-01-XX  
**Status:** ✅ **IMPLEMENTASI SELESAI**

---

## 🎯 OVERVIEW

Fitur **Custom Permissions** memungkinkan admin untuk memberikan permissions khusus kepada user tertentu di luar template `role_permissions` global. Ini berguna untuk user khusus yang memerlukan akses tambahan atau berbeda dari role mereka.

---

## ✅ YANG SUDAH DIIMPLEMENTASIKAN

### 1. Database
- ✅ Tabel `user_permissions` sudah ada dan siap digunakan
- ✅ Support untuk custom permissions dengan field:
  - `user_id` - ID user
  - `permission_id` - ID permission
  - `granted` - Status granted (1) atau denied (0)
  - `assigned_by` - User yang assign
  - `assigned_at` - Waktu assign
  - `expires_at` - Optional expiry date

### 2. Controller Methods
- ✅ `getAvailablePermissions($userId)` - Get semua permissions yang tersedia
- ✅ `saveCustomPermissions($userId)` - Save custom permissions untuk user
- ✅ `removeCustomPermission($userId)` - Remove satu custom permission

### 3. Routes
- ✅ `GET /admin/advanced-users/get-available-permissions/{userId}` - Get available permissions
- ✅ `POST /admin/advanced-users/save-custom-permissions/{userId}` - Save custom permissions
- ✅ `POST /admin/advanced-users/remove-custom-permission/{userId}` - Remove custom permission

### 4. View
- ✅ Section "Custom Permissions" di halaman edit user
- ✅ Button "Manage Custom Permissions"
- ✅ Modal untuk manage custom permissions
- ✅ Display current custom permissions
- ✅ Remove individual permission

---

## 📝 CARA PENGGUNAAN

### 1. Akses Halaman Edit User

```
http://localhost/optima1/public/admin/advanced-users/edit/{userId}
```

### 2. Manage Custom Permissions

1. Scroll ke section **"Custom Permissions"**
2. Klik button **"Manage Custom Permissions"**
3. Modal akan terbuka dengan semua permissions yang tersedia
4. Check permissions yang ingin diberikan ke user
5. Klik **"Save Custom Permissions"**

### 3. Remove Custom Permission

1. Di section "Custom Permissions", lihat table permissions yang sudah assigned
2. Klik button **"Remove"** pada permission yang ingin dihapus
3. Konfirmasi removal

---

## 🔍 LOGIC PERMISSION CHECK

Custom permissions akan **override** role permissions:

1. **Check Role Permissions** - Cek permissions dari role user
2. **Check Custom Permissions** - Cek custom permissions (override role)
3. **Return Result** - Custom permission akan override role permission

**Contoh:**
- User punya role "Marketing Staff" (tidak punya `warehouse.manage`)
- Admin assign custom permission `warehouse.manage` ke user
- User sekarang bisa manage warehouse (override role)

---

## 📊 DATABASE STRUCTURE

### user_permissions Table

```sql
CREATE TABLE user_permissions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    permission_id INT NOT NULL,
    division_id INT NULL,
    granted TINYINT(1) DEFAULT 1,
    assigned_by INT NULL,
    assigned_at DATETIME NULL,
    expires_at DATETIME NULL,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id)
);
```

---

## 🧪 TESTING

### Test Case 1: Assign Custom Permission

1. Login sebagai Admin
2. Edit user (misal: Marketing Staff)
3. Klik "Manage Custom Permissions"
4. Check permission `warehouse.manage`
5. Save
6. Logout dan login sebagai user tersebut
7. Coba akses warehouse manage - **Harus bisa**

### Test Case 2: Remove Custom Permission

1. Login sebagai Admin
2. Edit user yang sudah punya custom permission
3. Klik "Remove" pada permission
4. Konfirmasi
5. Logout dan login sebagai user tersebut
6. Coba akses - **Harus ditolak** (kembali ke role permission)

---

## ⚠️ PERINGATAN

1. **Custom permissions override role permissions** - Hati-hati saat assign
2. **Gunakan untuk user khusus saja** - Jangan assign ke semua user
3. **Document custom permissions** - Catat kenapa user punya custom permission
4. **Review secara berkala** - Pastikan custom permissions masih diperlukan

---

## 📚 RELATED FILES

1. **Controller:** `app/Controllers/Admin/AdvancedUserManagement.php`
   - `getAvailablePermissions()`
   - `saveCustomPermissions()`
   - `removeCustomPermission()`

2. **View:** `app/Views/admin/advanced_user_management/form.php`
   - Custom Permissions Section
   - Custom Permission Modal

3. **Routes:** `app/Config/Routes.php`
   - Custom permissions routes

4. **Model:** `app/Models/UserPermissionModel.php`
   - Methods untuk manage user permissions

---

## ✅ CHECKLIST

- [x] Database table `user_permissions` ready
- [x] Controller methods implemented
- [x] Routes configured
- [x] View section added
- [x] Modal for managing permissions
- [x] JavaScript functions
- [x] Permission check logic
- [ ] Testing dengan berbagai scenario (pending)

---

**Status:** ✅ **IMPLEMENTASI SELESAI**  
**Last Updated:** 2025-01-XX

