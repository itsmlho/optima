# Security Features Testing Checklist

## Overview
This document provides a comprehensive testing checklist for all security features implemented in OPTIMA system.

---

## 1. Rate Limiting (Login Attempts)

### Test Cases

#### TC-RL-001: Normal Login Success
- [ ] **Steps:**
  1. Navigate to `/auth/login`
  2. Enter valid username/email and password
  3. Click login
- [ ] **Expected:** Login successful, redirect to welcome page
- [ ] **Actual:** ___

#### TC-RL-002: Failed Login Attempts Counter
- [ ] **Steps:**
  1. Navigate to `/auth/login`
  2. Enter valid username/email but wrong password
  3. Repeat 3 times
- [ ] **Expected:** 
  - Error message shows remaining attempts (e.g., "Username/Email atau password salah. Anda memiliki 2 percobaan tersisa.")
  - Counter decreases with each failed attempt
- [ ] **Actual:** ___

#### TC-RL-003: Account Lock After 5 Failed Attempts
- [ ] **Steps:**
  1. Navigate to `/auth/login`
  2. Enter valid username/email but wrong password 5 times
- [ ] **Expected:**
  - After 5th failed attempt, account is locked
  - Error message: "Akun Anda akan terkunci selama X menit"
  - Cannot login even with correct password during lock period
- [ ] **Actual:** ___

#### TC-RL-004: Lock Duration Timer
- [ ] **Steps:**
  1. Trigger account lock (5 failed attempts)
  2. Wait and observe countdown timer on login page
- [ ] **Expected:**
  - Countdown timer displays remaining lock time
  - Timer updates every second
  - Account unlocks automatically after 15 minutes
- [ ] **Actual:** ___

#### TC-RL-005: Reset Counter After Successful Login
- [ ] **Steps:**
  1. Make 3 failed login attempts
  2. Login successfully with correct credentials
  3. Try failed login again
- [ ] **Expected:** Attempt counter resets, starts from 5 attempts again
- [ ] **Actual:** ___

#### TC-RL-006: Rate Limiting by IP Address
- [ ] **Steps:**
  1. Test from different IP addresses (if possible)
  2. Make multiple failed attempts from same IP
- [ ] **Expected:** Rate limiting applies per IP address
- [ ] **Actual:** ___

---

## 2. OTP via Email

### Test Cases

#### TC-OTP-001: Enable OTP for User
- [ ] **Steps:**
  1. Access database and set `otp_enabled = 1` for a test user
  2. Attempt login with that user
- [ ] **Expected:**
  - After entering correct password, redirect to `/auth/verify-otp`
  - OTP email sent to user's email address
  - Info message: "Kode OTP telah dikirim ke email Anda"
- [ ] **Actual:** ___

#### TC-OTP-002: OTP Email Received
- [ ] **Steps:**
  1. Trigger OTP flow (login with OTP-enabled user)
  2. Check email inbox
- [ ] **Expected:**
  - Email received from system
  - Email contains 6-digit OTP code
  - Email template is professional and readable
  - OTP code is clearly visible
- [ ] **Actual:** ___

#### TC-OTP-003: OTP Verification Success
- [ ] **Steps:**
  1. Enter OTP code received in email
  2. Click verify button (or auto-submit after 6 digits)
- [ ] **Expected:**
  - OTP verified successfully
  - Login completed, redirect to welcome page
  - Success message displayed
- [ ] **Actual:** ___

#### TC-OTP-004: OTP Verification Failure (Wrong Code)
- [ ] **Steps:**
  1. Enter incorrect OTP code
  2. Submit form
- [ ] **Expected:**
  - Error message: "Kode OTP salah. Sisa percobaan: X"
  - Attempt counter decreases
  - Can retry with correct code
- [ ] **Actual:** ___

#### TC-OTP-005: OTP Max Attempts Exceeded
- [ ] **Steps:**
  1. Enter wrong OTP code 3 times
- [ ] **Expected:**
  - After 3rd failed attempt, error message: "Terlalu banyak percobaan OTP yang salah. Silakan request OTP baru."
  - Must request new OTP to continue
- [ ] **Actual:** ___

#### TC-OTP-006: OTP Expiration
- [ ] **Steps:**
  1. Request OTP
  2. Wait 6 minutes (OTP expires after 5 minutes)
  3. Try to verify expired OTP
- [ ] **Expected:**
  - Error message: "OTP tidak ditemukan atau sudah expired. Silakan request OTP baru."
- [ ] **Actual:** ___

#### TC-OTP-007: Resend OTP Cooldown
- [ ] **Steps:**
  1. Request OTP
  2. Immediately click "Resend OTP"
- [ ] **Expected:**
  - Error message with cooldown timer: "Silakan tunggu X detik sebelum request OTP baru."
  - Button disabled during cooldown
  - Countdown timer displays
- [ ] **Actual:** ___

#### TC-OTP-008: Resend OTP After Cooldown
- [ ] **Steps:**
  1. Request OTP
  2. Wait 60 seconds
  3. Click "Resend OTP"
- [ ] **Expected:**
  - New OTP sent successfully
  - New OTP code received in email
  - Previous OTP becomes invalid
- [ ] **Actual:** ___

#### TC-OTP-009: OTP Paste Support
- [ ] **Steps:**
  1. Request OTP
  2. Copy 6-digit OTP from email
  3. Paste into OTP input field
- [ ] **Expected:**
  - OTP code pasted correctly
  - Auto-submit after paste
  - Verification proceeds automatically
- [ ] **Actual:** ___

#### TC-OTP-010: User Without OTP Enabled
- [ ] **Steps:**
  1. Login with user that has `otp_enabled = 0`
- [ ] **Expected:**
  - Normal login flow (no OTP verification)
  - Direct redirect to welcome page after login
- [ ] **Actual:** ___

---

## 3. Forgot Password via Email

### Test Cases

#### TC-FP-001: Request Password Reset
- [ ] **Steps:**
  1. Navigate to `/auth/forgot-password`
  2. Enter valid email address
  3. Submit form
- [ ] **Expected:**
  - Success message: "Jika email terdaftar, link reset password telah dikirim ke email Anda."
  - Email sent (check inbox)
- [ ] **Actual:** ___

#### TC-FP-002: Password Reset Email Received
- [ ] **Steps:**
  1. Request password reset
  2. Check email inbox
- [ ] **Expected:**
  - Email received from system
  - Email contains reset link with token
  - Email template is professional
  - Reset button works
- [ ] **Actual:** ___

#### TC-FP-003: Invalid Email Address
- [ ] **Steps:**
  1. Enter non-existent email address
  2. Submit form
- [ ] **Expected:**
  - Same success message (don't reveal if email exists - security best practice)
  - No email sent for invalid email
- [ ] **Actual:** ___

#### TC-FP-004: Password Reset Link Clicked
- [ ] **Steps:**
  1. Click reset link from email
  2. Verify redirected to reset password page
- [ ] **Expected:**
  - Redirect to `/auth/reset-password/{token}`
  - Reset password form displayed
  - Email address shown (masked or full)
- [ ] **Actual:** ___

#### TC-FP-005: Password Reset Success
- [ ] **Steps:**
  1. Access reset password page with valid token
  2. Enter new password (min 8 characters)
  3. Confirm new password
  4. Submit form
- [ ] **Expected:**
  - Password updated successfully
  - Redirect to login page
  - Success message displayed
  - Can login with new password
- [ ] **Actual:** ___

#### TC-FP-006: Password Reset Token Expiration
- [ ] **Steps:**
  1. Request password reset
  2. Wait 2 hours (token expires after 1 hour)
  3. Click reset link
- [ ] **Expected:**
  - Error message: "Token reset password tidak valid atau sudah expired. Silakan request reset password baru."
  - Redirect to forgot password page
- [ ] **Actual:** ___

#### TC-FP-007: Single-Use Token
- [ ] **Steps:**
  1. Request password reset
  2. Use reset link to change password
  3. Try to use same reset link again
- [ ] **Expected:**
  - Error message: "Token reset password sudah digunakan. Silakan request reset password baru."
  - Cannot reuse token
- [ ] **Actual:** ___

#### TC-FP-008: Rate Limiting for Forgot Password
- [ ] **Steps:**
  1. Request password reset 4 times within 1 hour from same email
- [ ] **Expected:**
  - First 3 requests succeed
  - 4th request shows rate limit error: "Terlalu banyak permintaan reset password. Silakan coba lagi nanti."
- [ ] **Actual:** ___

#### TC-FP-009: Rate Limiting by IP Address
- [ ] **Steps:**
  1. Request password reset 6 times within 1 hour from same IP (different emails)
- [ ] **Expected:**
  - First 5 requests succeed
  - 6th request shows rate limit error
- [ ] **Actual:** ___

---

## 4. Session Management

### Test Cases

#### TC-SM-001: Session Tracking on Login
- [ ] **Steps:**
  1. Login from browser A
  2. Check database `user_sessions` table
- [ ] **Expected:**
  - New session record created
  - Device info recorded (browser, OS, device type, IP)
  - `is_active = 1`
  - `login_at` timestamp set
- [ ] **Actual:** ___

#### TC-SM-002: Multiple Sessions Tracking
- [ ] **Steps:**
  1. Login from browser A (e.g., Chrome)
  2. Login from browser B (e.g., Firefox) on same device
  3. Login from mobile device
- [ ] **Expected:**
  - Each login creates separate session record
  - All sessions visible in profile page
  - Device info correctly identified for each
- [ ] **Actual:** ___

#### TC-SM-003: Session Activity Tracking
- [ ] **Steps:**
  1. Login to system
  2. Navigate to different pages
  3. Check database `user_sessions` table
- [ ] **Expected:**
  - `last_activity` timestamp updates on each page load
  - Activity tracked via AuthFilter
- [ ] **Actual:** ___

#### TC-SM-004: View Sessions in Profile
- [ ] **Steps:**
  1. Login from multiple devices/browsers
  2. Navigate to `/auth/profile`
  3. Scroll to "Active Sessions" section
- [ ] **Expected:**
  - Table displays all active sessions
  - Current session marked with "Current" badge
  - Device info displayed (browser, OS, IP, device type)
  - Last activity and login time shown
  - "Logout All Other Sessions" button visible if multiple sessions
- [ ] **Actual:** ___

#### TC-SM-005: Logout Specific Session
- [ ] **Steps:**
  1. Login from browser A and browser B
  2. From browser A, go to profile page
  3. Click logout button for browser B session
- [ ] **Expected:**
  - Confirmation dialog appears
  - After confirmation, browser B session logged out
  - Browser B session removed from table
  - Browser B user redirected to login page
  - Browser A session remains active
- [ ] **Actual:** ___

#### TC-SM-006: Logout All Other Sessions
- [ ] **Steps:**
  1. Login from browser A, B, and C
  2. From browser A, click "Logout All Other Sessions"
- [ ] **Expected:**
  - Confirmation dialog appears
  - After confirmation, sessions B and C logged out
  - Sessions B and C removed from table
  - Browser A session remains active
  - "Logout All Other Sessions" button hidden (only 1 session left)
- [ ] **Actual:** ___

#### TC-SM-007: Auto Logout Idle Sessions
- [ ] **Steps:**
  1. Login and then stop using system for 3 hours (idle timeout = 2 hours)
  2. Try to access protected page
- [ ] **Expected:**
  - Session automatically logged out
  - Redirect to login page
  - Error message: "Session Anda telah expired karena tidak aktif."
- [ ] **Actual:** ___

#### TC-SM-008: Device Detection Accuracy
- [ ] **Steps:**
  1. Login from different browsers (Chrome, Firefox, Safari, Edge)
  2. Login from different devices (Desktop, Mobile, Tablet)
  3. Check device info in profile page
- [ ] **Expected:**
  - Browser names correctly identified
  - OS correctly identified
  - Device type correctly identified (desktop/mobile/tablet)
- [ ] **Actual:** ___

#### TC-SM-009: Current Session Cannot Be Logged Out via UI
- [ ] **Steps:**
  1. Login and go to profile page
  2. Try to logout current session using logout button
- [ ] **Expected:**
  - No logout button shown for current session
  - Text "Current session" displayed instead
  - Cannot logout own current session via this method
- [ ] **Actual:** ___

---

## 5. Integration Tests

### Test Cases

#### TC-INT-001: Complete Login Flow with OTP
- [ ] **Steps:**
  1. Login with OTP-enabled user
  2. Enter correct password
  3. Verify OTP
  4. Check session tracking
- [ ] **Expected:**
  - All steps complete successfully
  - Session tracked in database
  - Rate limiting counter reset
- [ ] **Actual:** ___

#### TC-INT-002: Failed Login with Rate Limiting
- [ ] **Steps:**
  1. Make 5 failed login attempts
  2. Account gets locked
  3. Try OTP flow (should not be reached)
- [ ] **Expected:**
  - Account locked before OTP flow
  - Cannot proceed to OTP verification
- [ ] **Actual:** ___

#### TC-INT-003: Password Reset After Account Lock
- [ ] **Steps:**
  1. Lock account (5 failed attempts)
  2. Request password reset
  3. Reset password
  4. Login with new password
- [ ] **Expected:**
  - Password reset works independently
  - New password login successful
  - Rate limiting counter reset after successful login
- [ ] **Actual:** ___

#### TC-INT-004: Multiple Security Features Interaction
- [ ] **Steps:**
  1. Enable OTP for user
  2. Login with OTP
  3. Check session management
  4. Logout other sessions
  5. Request password reset
- [ ] **Expected:**
  - All features work together without conflicts
  - No errors or unexpected behavior
- [ ] **Actual:** ___

---

## 6. Security Tests

### Test Cases

#### TC-SEC-001: SQL Injection in Login
- [ ] **Steps:**
  1. Try SQL injection in username field: `admin' OR '1'='1`
- [ ] **Expected:**
  - Input sanitized
  - No SQL injection possible
  - Login fails with proper error message
- [ ] **Actual:** ___

#### TC-SEC-002: XSS in Email Fields
- [ ] **Steps:**
  1. Try XSS in forgot password email field: `<script>alert('XSS')</script>`
- [ ] **Expected:**
  - Input sanitized
  - No script execution
  - Proper validation error
- [ ] **Actual:** ___

#### TC-SEC-003: CSRF Protection
- [ ] **Steps:**
  1. Try to submit forms without CSRF token
- [ ] **Expected:**
  - Forms reject requests without valid CSRF token
  - Error message shown
- [ ] **Actual:** ___

#### TC-SEC-004: Session Hijacking Prevention
- [ ] **Steps:**
  1. Login from browser A
  2. Copy session ID from browser A
  3. Try to use session ID in browser B
- [ ] **Expected:**
  - Session ID tied to IP/device
  - Unauthorized access blocked
- [ ] **Actual:** ___

#### TC-SEC-005: Token Replay Attack Prevention
- [ ] **Steps:**
  1. Request password reset
  2. Use reset token multiple times
- [ ] **Expected:**
  - Token marked as used after first use
  - Cannot reuse token
- [ ] **Actual:** ___

---

## 7. Performance Tests

### Test Cases

#### TC-PERF-001: Multiple Concurrent Logins
- [ ] **Steps:**
  1. Login from 10 different browsers simultaneously
- [ ] **Expected:**
  - All logins succeed
  - All sessions tracked
  - No performance degradation
- [ ] **Actual:** ___

#### TC-PERF-002: Rate Limiting Performance
- [ ] **Steps:**
  1. Make 100 login attempts rapidly
- [ ] **Expected:**
  - Rate limiting works correctly
  - System remains responsive
  - No database overload
- [ ] **Actual:** ___

---

## Testing Notes

### Test Environment
- **URL:** ________________
- **Database:** ________________
- **Test Users:** ________________
- **Test Date:** ________________
- **Tester:** ________________

### Known Issues
1. ___
2. ___
3. ___

### Recommendations
1. ___
2. ___
3. ___

---

## Test Summary

- **Total Test Cases:** 40+
- **Passed:** ___
- **Failed:** ___
- **Blocked:** ___
- **Not Tested:** ___

### Critical Issues Found
1. ___
2. ___
3. ___

### Non-Critical Issues Found
1. ___
2. ___
3. ___

---

**Last Updated:** <?= date('Y-m-d H:i:s') ?>

