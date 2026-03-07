# 🚨 CRITICAL: CSRF Configuration Fix - Production Deployment

## Issue Fixed
**CSRF Token Mismatch** causing 403 Forbidden errors on all AJAX POST requests.

### Root Cause
`.env` and `.env_production` files had **incorrect CSRF configuration**:
- ❌ `security.tokenName = 'csrf_token_name'` (system default)
- ❌ `security.tokenRandomize = true` (breaks AJAX)
- ✅ Should be: `security.tokenName = 'csrf_test_name'` (matches Security.php config)
- ✅ Should be: `security.tokenRandomize = false` (safe for AJAX)

---

## Files Changed

### Local Development (`.env`)
```env
security.tokenRandomize = false  (was: true)
security.tokenName = 'csrf_test_name'  (was: csrf_token_name)
security.regenerate = false  (confirmed)
```

### Production (`.env_production`)
```env
security.tokenRandomize = false  (was: true)
security.tokenName = 'csrf_test_name'  (was: csrf_token_name)
security.regenerate = false  (confirmed)
```

---

## 🚀 Production Deployment Steps

### **CRITICAL: Apache MUST be restarted after deploying .env_production**

1. **Upload Files**
   ```bash
   # Via SSH or FTP, upload:
   .env_production → rename to .env on production server
   ```

2. **Restart Apache (REQUIRED!)**
   ```bash
   # Via cPanel or SSH:
   sudo systemctl restart apache2
   # OR via Hostinger control panel: "Restart Apache"
   ```

3. **Clear CodeIgniter Cache**
   ```bash
   php spark cache:clear
   rm -rf writable/cache/*
   rm -rf writable/session/*
   ```

4. **Verify Configuration**
   - Upload `public/test_csrf.php` to production
   - Access: `https://optima.sml.co.id/test_csrf.php`
   - Check: All items should show **✅ CORRECT** (green)
   - **Delete test file after verification!**

5. **Test AJAX Endpoints**
   - Login to production
   - Test Customer Management page
   - Check browser console (F12) - no 403 errors
   - Verify DataTables and statistics load properly

---

## 🔍 How to Verify Fix is Working

### Console Debug Logs (F12 Developer Tools)
**CORRECT Output:**
```javascript
🔍 [DEBUG] csrf_token() from PHP: csrf_test_name ✅
🔍 [DEBUG] Config tokenName: csrf_test_name ✅
📤 Sending data: {csrf_test_name: '561fb19d...'} ✅
```

**INCORRECT Output (OLD):**
```javascript
🔍 [DEBUG] csrf_token() from PHP: csrf_token_name ❌
📤 Sending data: {csrf_test_name: '561fb19d...'} ❌ MISMATCH!
POST /getCustomerStats 403 Forbidden ❌
```

### Network Tab Check
1. Open DevTools (F12) → Network tab
2. Filter: XHR
3. Click any POST request (e.g., `getCustomers`, `getCustomerStats`)
4. Check **Request Payload** or **Form Data**:
   - ✅ Should contain: `csrf_test_name: "abc123..."`
   - ❌ If missing or wrong name → config not loaded

---

## ⚠️ Important Notes

### Why This Happened
- CodeIgniter 4's `.env` file **overrides** `app/Config/Security.php`
- `.env` had old default values from system installation
- JavaScript was sending `csrf_test_name` (from config)
- PHP was expecting `csrf_token_name` (from .env)
- **Result**: Token name mismatch → 403 Forbidden

### Why tokenRandomize MUST be false
- When `true`, token **changes on every request**
- AJAX requests use **cached token** from page load
- Second AJAX call fails because token already changed
- **Setting to false** keeps token stable during session

### Why regenerate MUST be false
- Similar issue as tokenRandomize
- Regenerates token **after each verified request**
- Breaks subsequent AJAX calls in same session
- Safe to disable for AJAX-heavy applications

---

## 🛡️ Security Impact Assessment

**Question:** Is it safe to disable `tokenRandomize` and `regenerate`?

**Answer:** **YES** for these reasons:
1. ✅ CSRF token still **validates on every request**
2. ✅ Token is still **cryptographically strong** (64 chars)
3. ✅ Token **expires after 7200 seconds** (2 hours)
4. ✅ Token stored in **HTTP-only cookie** (XSS protection)
5. ✅ Cookie uses **Secure** flag in production (HTTPS only)
6. ✅ Cookie uses **SameSite=Lax** (CSRF mitigation)

**Trade-off:**
- ❌ Slightly less secure: Token doesn't change per-request
- ✅ AJAX functionality: All POST requests work properly
- ✅ User experience: No random 403 errors

**Verdict:** Standard practice for AJAX-heavy web applications.

---

## 📋 Pre-Deployment Checklist

- [x] `.env` updated (local development)
- [x] `.env_production` updated
- [x] `test_csrf.php` created for verification
- [ ] **Local testing**: Restart Apache, verify no 403 errors
- [ ] **Production upload**: Deploy .env_production as .env
- [ ] **Production restart**: Restart Apache server
- [ ] **Production verify**: Access test_csrf.php, check green
- [ ] **Production test**: Customer Management page, check console logs
- [ ] **Cleanup**: Delete test_csrf.php from production

---

## 🔧 Rollback Procedure (If Issues Occur)

If production has issues after deployment:

1. **SSH into production server**
2. **Restore old .env**:
   ```bash
   # Backup current .env
   cp .env .env_backup_new
   
   # Restore old .env (if you have backup)
   cp .env_backup_old .env
   
   # Restart Apache
   sudo systemctl restart apache2
   ```

3. **Contact development team** with error logs

---

## 📞 Support

**Developer:** GitHub Copilot via VS Code  
**Date Fixed:** March 7, 2026  
**Files Modified:** `.env`, `.env_production`, `public/test_csrf.php`  
**Issue Tracker:** RECENT_CHANGES_MARCH_5-6_2026.md

---

**Last Updated:** March 7, 2026 08:50 WIB
