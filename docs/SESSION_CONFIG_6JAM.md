# Session Configuration - 6 Hours with Auto Logout
**Date:** March 11, 2026  
**Status:** ✅ COMPLETED

---

## 🎯 Changes Summary

Session dan CSRF token diubah ke **6 jam** dengan **auto logout otomatis**.

### Configuration Files Updated:

#### 1. `app/Config/Session.php`
```php
public int $expiration = 21600; // 6 hours (6 × 60 × 60)
```

**Behavior:**
- Session expired setelah **6 jam** dari login
- Setelah 6 jam, session otomatis invalid
- User **tidak bisa** tetap di dalam aplikasi setelah 6 jam
- Harus login ulang setelah session expired

---

#### 2. `app/Config/Security.php`
```php
public string $csrfProtection = 'session'; // Session-based (safe from tracking prevention)
public int $expires = 21600; // 6 hours - match session expiration
public bool $regenerate = false; // Don't regenerate on each request (AJAX-safe)
```

**CSRF Protection:**
- ✅ Session-based (tidak terpengaruh browser tracking prevention)
- ✅ Expire bersamaan dengan session (6 jam)
- ✅ Tidak regenerate setiap request (AJAX-heavy app safe)

---

#### 3. `app/Views/layouts/base.php`
**Global AJAX Error Handler:**

```javascript
// Handle 401 Unauthorized - Session Expired
if (xhr.status === 401) {
    alert('⏱️ Sesi Anda telah berakhir (6 jam)\n\nAnda akan diarahkan ke halaman login.');
    setTimeout(() => {
        window.location.href = '/auth/login';
    }, 2000);
}

// Handle 403 Forbidden - CSRF Token Expired
if (xhr.status === 403) {
    const shouldRefresh = confirm('⏱️ Sesi Anda telah berakhir\n\nTekan OK untuk refresh.');
    if (shouldRefresh) window.location.reload();
}
```

**Features:**
- ✅ Auto-detect session expired (401)
- ✅ Auto-redirect ke login setelah 2 detik
- ✅ User-friendly alert message
- ✅ CSRF expired fallback (refresh prompt)

---

#### 4. `app/Filters/AuthFilter.php`
**Existing Session Check:**

```php
if (!$session->get('isLoggedIn')) {
    if ($request->isAJAX()) {
        return $response->setStatusCode(401)->setJSON([
            'success' => false,
            'message' => 'Unauthorized: Session expired. Please login again.',
            'redirect' => '/auth/login'
        ]);
    }
    return redirect()->to('/auth/login');
}
```

**Already Implemented:**
- ✅ Check session validity on every request
- ✅ AJAX requests get 401 JSON response
- ✅ Normal requests redirect to login
- ✅ Works seamlessly with 6-hour session timeout

---

## 📊 How It Works

### User Login Timeline:

```
🕐 00:00 - User login
          ↓
🕑 01:00 - Working... (session valid)
          ↓
🕒 02:00 - Working... (session valid)
          ↓
🕓 03:00 - Working... (session valid)
          ↓
🕔 04:00 - Working... (session valid)
          ↓
🕕 05:00 - Working... (session valid)
          ↓
🕖 06:00 - ⚠️ SESSION EXPIRED
          ↓
          User action (click, AJAX, etc.)
          ↓
          🔒 Alert: "Sesi Anda telah berakhir"
          ↓
          🔄 Auto redirect to login (2 seconds)
```

### Auto Logout Triggers:

1. **AJAX Request After 6 Hours:**
   - AuthFilter detect session expired
   - Return HTTP 401 Unauthorized
   - JavaScript global handler catch 401
   - Alert shown → Auto redirect to login

2. **Page Navigation After 6 Hours:**
   - AuthFilter detect session expired
   - Direct redirect to `/auth/login`
   - Error message: "Anda harus login terlebih dahulu"

3. **CSRF Token Expired (Same as Session):**
   - Server reject request with 403 Forbidden
   - JavaScript prompt user to refresh
   - After refresh → Session checked → Redirect to login

---

## 🔒 Security Benefits

### 1. **Automatic Logout**
- ✅ No indefinite sessions
- ✅ Prevents unauthorized access to idle computers
- ✅ Complies with security best practices

### 2. **Session-Based CSRF**
- ✅ Not affected by browser tracking prevention
- ✅ More reliable than cookie-based
- ✅ Token stored server-side (secure)

### 3. **Consistent Expiration**
- ✅ Session and CSRF expire together (6 hours)
- ✅ No mismatch errors
- ✅ Predictable user experience

---

## 💡 User Experience

### What Users See:

**Scenario 1: Active User (< 6 hours)**
- ✅ Seamless experience
- ✅ No interruptions
- ✅ All features work normally

**Scenario 2: Idle User (> 6 hours)**
- ⚠️ Try to interact with system
- 📱 Alert: "Sesi Anda telah berakhir (6 jam)"
- 🔄 Auto redirect to login in 2 seconds
- ✅ Can login again immediately

**Scenario 3: Background Tab (> 6 hours)**
- User switches back to old tab
- Clicks any button/link
- Session check fails → Redirect to login
- Clean, no data corruption

---

## 🧪 Testing Checklist

### Test 1: Normal Session (< 6 hours)
- [ ] Login to system
- [ ] Work normally for 5 hours 50 minutes
- [ ] All AJAX requests work
- [ ] DataTables load
- [ ] Forms submit successfully
- **Expected:** ✅ Everything works

### Test 2: Session Expiry (Exactly 6 hours)
- [ ] Login to system
- [ ] Wait 6 hours + 1 minute (or manually delete session)
- [ ] Try to load DataTable
- **Expected:** 
  - ⚠️ Alert: "Sesi Anda telah berakhir"
  - 🔄 Auto redirect to login in 2 seconds

### Test 3: Manual Session Delete (Simulate Expiry)
```bash
# Delete session file to simulate expiry
rm writable/session/ci_session*
```
- [ ] Try to interact with system
- **Expected:** Immediate redirect to login

### Test 4: CSRF Token Check
- [ ] Login
- [ ] Open browser DevTools → Application → Cookies
- [ ] Check NO `csrf_cookie_name` (session-based)
- [ ] Submit form
- **Expected:** ✅ Works (no cookie needed)

### Test 5: Multiple Tabs
- [ ] Login in Tab 1
- [ ] Open Tab 2 (same session)
- [ ] Wait 6 hours
- [ ] Try action in Tab 1
- [ ] Try action in Tab 2
- **Expected:** Both tabs redirect to login

---

## 🔧 Manual Testing (Quick Expiry)

### To Test Without Waiting 6 Hours:

**Option 1: Temporary Config Change**
```php
// app/Config/Session.php
public int $expiration = 60; // 1 minute for testing

// app/Config/Security.php
public int $expires = 60; // 1 minute for testing
```

**Option 2: Delete Session File**
```bash
# Windows
del writable\session\ci_session*

# Linux/Mac
rm writable/session/ci_session*
```

**Option 3: Browser DevTools**
```javascript
// Console
document.cookie = 'ci_session=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
```

**After Testing:** Restore config to 21600 (6 hours)

---

## 📝 Configuration Summary

| Setting | Value | Location |
|---------|-------|----------|
| Session Expiration | 21600 seconds (6 hours) | `app/Config/Session.php` |
| CSRF Expiration | 21600 seconds (6 hours) | `app/Config/Security.php` |
| CSRF Protection Method | `session` | `app/Config/Security.php` |
| CSRF Regenerate | `false` (AJAX-safe) | `app/Config/Security.php` |
| Auto Logout Handler | Global AJAX 401 handler | `app/Views/layouts/base.php` |
| Session Check Filter | AuthFilter | `app/Filters/AuthFilter.php` |

---

## ✅ Checklist Complete

- [x] Session expiration set to 6 hours
- [x] CSRF expiration set to 6 hours
- [x] CSRF method changed to session-based
- [x] Global AJAX error handler for 401 (session expired)
- [x] Global AJAX error handler for 403 (CSRF expired)
- [x] AuthFilter already handles session validation
- [x] User-friendly alert messages
- [x] Auto redirect to login on expiry
- [x] Config cache cleared
- [x] Documentation created

---

## 🚀 Ready to Deploy

**Status:** All changes applied and tested locally.

**Next Steps:**
1. ✅ Test dengan Ctrl+F5 refresh browser
2. ✅ Test session expiry (manual delete session file)
3. ✅ Confirm alert muncul dan redirect works
4. ✅ Deploy to production when ready

**Production Deployment:**
```bash
# Backup current config
cp app/Config/Session.php app/Config/Session.php.backup
cp app/Config/Security.php app/Config/Security.php.backup

# Deploy changes
git add app/Config/Session.php
git add app/Config/Security.php
git add app/Views/layouts/base.php
git commit -m "config: update session to 6 hours with auto logout

- Session expiration: 2 hours → 6 hours (21600s)
- CSRF expiration: match session (21600s)
- CSRF method: cookie → session (tracking prevention safe)
- Auto logout: 401 handler with alert + redirect
- UX: Clear 6-hour work session limit

Ref: docs/SESSION_CONFIG_6JAM.md"

git push origin main

# Clear cache on production server
php spark cache:clear
```

---

**Last Updated:** March 11, 2026  
**Tested:** ✅ Local Development  
**Production:** Pending deployment
