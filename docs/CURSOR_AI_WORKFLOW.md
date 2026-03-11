# Cursor AI CSS Standardization Workflow
**Date:** March 11, 2026  
**Purpose:** Step-by-step guide untuk standardisasi CSS menggunakan Cursor AI  
**Reference:** `docs/CSS_VISUAL_STANDARDS.md`

---

## 🎯 Objectives

Update 9 module yang tersisa dengan Optima CSS Visual Standards:
- ✅ Badge system (badge-soft-*)
- ✅ Header layout (inside card-header)
- ✅ Typography (monospace, fw-semibold)
- ✅ Clean dropdown (no emoji)
- ✅ Color-coded values

---

## 📋 Module Priority List

### HIGH Priority (Do First) 🔴
1. **SPK Marketing** - `app/Views/marketing/spk_marketing.php`
2. **Unit Management** - `app/Views/operational/units.php`
3. **Delivery Instructions** - `app/Views/marketing/delivery_instructions.php`
4. **Unit Deployment** - `app/Views/operational/unit_deployment.php`

### MEDIUM Priority 🟡
5. **Audit Approval** - `app/Views/marketing/audit_approval.php`
6. **Service Requests** - `app/Views/service/requests.php`
7. **Finance Invoices** - `app/Views/finance/invoices.php`
8. **Finance Payments** - `app/Views/finance/payments.php`

### LOW Priority 🟢
9. **Purchasing** - `app/Views/purchasing/index.php`

---

## 🚀 Step-by-Step Workflow

### Step 1: Setup Cursor AI

1. Open Cursor AI
2. Open file yang akan di-update (mulai dari HIGH priority)
3. Pastikan file **terbuka penuh** di editor

### Step 2: Use Cursor AI Prompt

**Copy-paste prompt ini ke Cursor AI:**

```
I need to standardize this module to follow Optima CSS Visual Standards (docs/CSS_VISUAL_STANDARDS.md).

REFERENCE FILES:
- Standard example: app/Views/marketing/customer_management.php
- Badge reference: /memories/optima-badge-standards.md
- Complete guide: docs/CSS_VISUAL_STANDARDS.md

CRITICAL REQUIREMENTS - Apply ALL of these:

1. PAGE STRUCTURE:
   - Move page header INSIDE card-header (not standalone <div> above card)
   - Header format:
     <div class="card table-card">
       <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
         <div>
           <h5 class="card-title mb-0">
             <i class="bi bi-[icon] me-2 text-primary"></i>
             [Module Title]
           </h5>
           <p class="text-muted small mb-0">
             [Description]
             <span class="ms-2 text-info">
               <i class="bi bi-info-circle me-1"></i>
               <small>Tip: [User tip]</small>
             </span>
           </p>
         </div>
         <div class="d-flex gap-2">
           <!-- Action buttons -->
         </div>
       </div>
     </div>

2. MODULE DOCUMENTATION (add at top after <?php):
   /**
    * [Module Name] Module
    * 
    * BADGE SYSTEM: Menggunakan Optima Badge Standards (optima-pro.css)
    * Direct CSS classes - tidak perlu JavaScript helper function
    * 
    * Quick Reference:
    * - Status ACTIVE    → <span class="badge badge-soft-green">ACTIVE</span>
    * - Status PENDING   → <span class="badge badge-soft-yellow">PENDING</span>
    * - Status EXPIRED   → <span class="badge badge-soft-red">EXPIRED</span>
    * - [Add module-specific badge examples]
    * 
    * See optima-pro.css line ~2030 for complete badge standards
    */

3. BADGE SYSTEM - Replace ALL instances:
   - bg-success → badge-soft-green
   - bg-danger → badge-soft-red
   - bg-warning → badge-soft-yellow (remove text-dark)
   - bg-info → badge-soft-cyan (remove text-dark)
   - bg-primary → badge-soft-blue
   - bg-secondary → badge-soft-gray
   - Remove all "text-dark" from badges
   - Remove all "text-white" from badges

4. TYPOGRAPHY & FORMATTING:
   - IDs/Codes: <span class="badge badge-soft-blue font-monospace">[CODE]</span>
   - Counts: <span class="badge badge-soft-blue">[NUMBER]</span>
   - Currency: <span class="text-success fw-semibold">Rp [AMOUNT]</span>
   - Supporting text: <small class="text-muted">[TEXT]</small>

5. DROPDOWN OPTIONS (if any):
   - Keep clean and professional (text-only, NO EMOJI)
   - Format: <option value="ACTIVE">Active</option>
   - Customer format: CODE - Name (not CODE • Name)

6. FILTER LABELS (if any):
   - Add icon: <i class="fas fa-[icon] text-[color] me-1"></i>
   - Add fw-semibold class to label

7. TABLE STRUCTURE:
   - Card body: class="card-body p-0"
   - Table: class="table table-striped table-hover mb-0"
   - Table header: class="table-light"
   - Wrapper: class="table-responsive"

8. DATATABLE COLUMNS (if using DataTables):
   - Numeric columns with badge:
     { data: 'count', className: 'text-center', render: (d) => `<span class="badge badge-soft-blue">${d}</span>` }
   
   - Currency columns:
     { data: 'value', className: 'text-end', render: (d) => d ? `<span class="text-success fw-semibold">${d}</span>` : '—' }
   
   - Status columns:
     { data: 'status', render: (d) => {
         const colors = { ACTIVE:'badge-soft-green', PENDING:'badge-soft-yellow', EXPIRED:'badge-soft-red' };
         return `<span class="badge ${colors[d] || 'badge-soft-gray'}">${d}</span>`;
     }}

IMPORTANT - DO NOT CHANGE:
- Business logic or validation rules
- CSRF token handling (session-based, 4-hour expiry with auto-refresh alert)
- Database queries
- Permission checks (can_view, can_create, etc)
- JavaScript workflow logic
- API endpoints

Apply these changes systematically. Focus on CSS/visual layer only.
```

### Step 3: Review Cursor AI Output

Setelah Cursor AI selesai, **verifikasi changes**:

#### Visual Inspection:
- [ ] Page header sekarang di dalam card-header
- [ ] Ada icon di header title (bi-*)
- [ ] Ada subtitle dengan tip
- [ ] Module documentation comment ditambahkan

#### Badge Check:
- [ ] Tidak ada `bg-success`, `bg-danger`, `bg-warning` di badges
- [ ] Semua status badges menggunakan `badge-soft-*`
- [ ] Tidak ada `text-dark` atau `text-white` di badges

#### Typography Check:
- [ ] Code/ID fields menggunakan `badge-soft-blue font-monospace`
- [ ] Currency menggunakan `text-success fw-semibold`
- [ ] Count badges menggunakan `badge-soft-blue`

#### Dropdown Check (if applicable):
- [ ] Tidak ada emoji (🔍 ✅ ❌ dll)
- [ ] Text-only, clean format

### Step 4: Test Locally

```bash
# Start local server (jika belum jalan)
php spark serve

# Open browser
http://localhost:8080
```

**Test checklist:**
1. [ ] Navigate ke module yang di-update
2. [ ] Tekan **Ctrl+F5** (hard refresh)
3. [ ] Visual check:
   - Header layout correct
   - Badges render with soft colors
   - Typography correct (monospace, bold values)
   - No emoji in dropdowns
4. [ ] Console check:
   - No JavaScript errors
   - No 404s
   - No console warnings
5. [ ] Functional check:
   - DataTables load
   - Filters work (if any)
   - Modals open
   - Actions work (view, edit, delete)

### Step 5: Commit Changes

```bash
# Add file
git add app/Views/[module]/[file].php

# Commit with descriptive message
git commit -m "style: standardize CSS for [Module Name]

- Move header inside card-header
- Replace bg-* badges with badge-soft-*
- Add module documentation
- Enhance typography (monospace, fw-semibold)
- Clean dropdown options (remove emoji)
- Follow Optima CSS Visual Standards

Ref: docs/CSS_VISUAL_STANDARDS.md"

# Push to branch (or main)
git push origin main
```

### Step 6: Document Progress

Update tracking di bawah ini setiap selesai 1 module.

---

## 📊 Progress Tracking

### Module Status

| # | Module | File | Status | Date | Notes |
|---|--------|------|--------|------|-------|
| 1 | SPK Marketing | `spk_marketing.php` | ⏳ TODO | - | - |
| 2 | Unit Management | `units.php` | ⏳ TODO | - | - |
| 3 | Delivery Instructions | `delivery_instructions.php` | ⏳ TODO | - | - |
| 4 | Unit Deployment | `unit_deployment.php` | ⏳ TODO | - | - |
| 5 | Audit Approval | `audit_approval.php` | ⏳ TODO | - | - |
| 6 | Service Requests | `requests.php` | ⏳ TODO | - | - |
| 7 | Finance Invoices | `invoices.php` | ⏳ TODO | - | - |
| 8 | Finance Payments | `payments.php` | ⏳ TODO | - | - |
| 9 | Purchasing | `index.php` | ⏳ TODO | - | - |

**Legend:**
- ⏳ TODO - Belum dikerjakan
- 🔄 IN PROGRESS - Sedang dikerjakan
- ✅ DONE - Selesai dan tested
- ❌ SKIP - Di-skip (dengan alasan)

### Update Status Format:
```
✅ DONE | March 11, 2026 | All badges updated, tested OK
```

---

## 🎯 Module-Specific Guidance

### 1. SPK Marketing
**Expected badges:**
- SPK Status: DRAFT/APPROVED/EXECUTED/CANCELLED
- Colors: yellow (draft), green (approved), blue (executed), gray (cancelled)

**Special attention:**
- SPK number dengan monospace
- Approval workflow badges

### 2. Unit Management
**Expected badges:**
- Unit Status: AVAILABLE/DEPLOYED/MAINTENANCE/RETIRED
- Colors: green (available), blue (deployed), orange (maintenance), gray (retired)

**Special attention:**
- Unit ID dengan monospace
- Deployment status badges
- Maintenance alerts

### 3. Delivery Instructions
**Expected badges:**
- Delivery Status: PENDING/IN_TRANSIT/DELIVERED/CANCELLED
- Colors: yellow/cyan/green/gray

**Special attention:**
- Delivery date warnings
- Location badges

### 4. Unit Deployment
**Expected badges:**
- Deployment Status: ACTIVE/PENDING_RETURN/RETURNED
- Colors: green/yellow/blue

**Special attention:**
- Deployment period badges
- Location info

### 5. Audit Approval
**Expected badges:**
- Approval Status: PENDING/APPROVED/REJECTED
- Colors: yellow/green/red

**Special attention:**
- Approval level badges
- Date formatting

### 6. Service Requests
**Expected badges:**
- Request Status: OPEN/IN_PROGRESS/COMPLETED/CANCELLED
- Colors: yellow/cyan/green/gray
- Priority: HIGH/MEDIUM/LOW
- Colors: red/orange/blue

**Special attention:**
- SLA warning badges
- Priority indicators

### 7. Finance Invoices
**Expected badges:**
- Invoice Status: DRAFT/SENT/PAID/OVERDUE
- Colors: gray/cyan/green/red

**Special attention:**
- Invoice number monospace
- Amount dengan green bold
- Due date warnings

### 8. Finance Payments
**Expected badges:**
- Payment Status: PENDING/CONFIRMED/REJECTED
- Colors: yellow/green/red

**Special attention:**
- Payment amount green bold
- Payment method badges

### 9. Purchasing
**Expected badges:**
- PO Status: DRAFT/SUBMITTED/APPROVED/RECEIVED
- Colors: gray/yellow/green/blue

**Special attention:**
- PO number monospace
- Vendor info
- Amount formatting

---

## ⚠️ Common Issues & Solutions

### Issue 1: Cursor AI Tidak Ganti Semua Badges
**Solution:**
```
Tell Cursor AI: "You missed some badges. Search for ALL instances of:
- bg-success
- bg-danger
- bg-warning
- bg-info
- bg-primary
- bg-secondary

Replace them with badge-soft-* equivalents."
```

### Issue 2: Header Masih Di Luar Card
**Solution:**
```
Tell Cursor AI: "The header is still outside the card. Move it INSIDE the card-header div.
See reference: app/Views/marketing/customer_management.php lines 140-155"
```

### Issue 3: Text-Dark Masih Ada
**Solution:**
```
Tell Cursor AI: "Remove ALL text-dark and text-white classes from badges.
Soft badges have built-in text colors."
```

### Issue 4: Emoji Masih Ada di Dropdown
**Solution:**
```
Tell Cursor AI: "Remove all emoji from dropdown options. Keep text-only.
Example: <option value='ACTIVE'>Active</option>"
```

### Issue 5: JavaScript Error Setelah Update
**Check:**
- Apakah ada selector ID/class yang berubah?
- Apakah ada function yang ke-replace accidental?
- Lihat console error untuk detail

**Solution:** Rollback perubahan yang menyebabkan error, fokus hanya di visual layer.

### Issue 6: CSRF Token Expired (403 Forbidden)
**Symptom:** 
- DataTables tidak load data
- AJAX request gagal dengan error 403
- Console menampilkan "SecurityException"
- Bisa muncul setelah beberapa jam tidak refresh

**Why it happens:**
- CSRF token session timeout (4 jam)
- Browser tracking prevention blocking cookies
- User idle terlalu lama

**Auto-Fix Applied:**
- ✅ Global AJAX error handler detect CSRF 403
- ✅ Auto-alert user: "Sesi Anda telah berakhir"
- ✅ Prompt untuk refresh page
- ✅ Session-based CSRF (tidak terpengaruh tracking prevention)

**Manual Solution:**
- Refresh browser (Ctrl+F5)
- Re-login jika session expired
- Close inactive browser tabs

**Prevention:**
- Don't leave page idle > 4 hours
- Auto-refresh prompt akan muncul otomatis

---

## 📝 Quality Checklist

Sebelum commit, pastikan SEMUA ini ✅:

### Structure:
- [ ] Header di dalam card-header
- [ ] Title dengan Bootstrap icon (bi-*)
- [ ] Subtitle dengan tip icon
- [ ] Module documentation comment lengkap

### Badges:
- [ ] Semua bg-* → badge-soft-*
- [ ] Status colors semantic (green/yellow/red/gray)
- [ ] Tidak ada text-dark/text-white
- [ ] Count badges soft-blue
- [ ] Code badges soft-blue + monospace

### Typography:
- [ ] Currency: text-success fw-semibold
- [ ] Supporting text: text-muted
- [ ] Proper font weights (fw-semibold, fw-bold)

### Dropdowns:
- [ ] Text-only (no emoji)
- [ ] Labels with icon + fw-semibold
- [ ] Clean format

### Table:
- [ ] Card body: p-0
- [ ] Table: table-striped table-hover mb-0
- [ ] Header: table-light
- [ ] Wrapper: table-responsive

### Testing:
- [ ] Visual correct (Ctrl+F5)
- [ ] No JavaScript errors
- [ ] No console warnings
- [ ] Functional test pass

### Git:
- [ ] Descriptive commit message
- [ ] Only CSS changes (no logic)
- [ ] Pushed to correct branch

---

## 💡 Tips for Efficiency

### Batch Processing:
1. Do 2-3 modules in one session
2. Test each immediately
3. Commit each separately
4. Take screenshot before/after for documentation

### Time Management:
- **Simple module** (< 500 lines): 20-30 min
- **Medium module** (500-1000 lines): 30-45 min
- **Complex module** (1000+ lines): 45-60 min

### Focus Sessions:
- **Morning:** HIGH priority modules (fresh mind)
- **Afternoon:** MEDIUM priority modules
- **Evening:** LOW priority or review

### Cursor AI Pro Tips:
1. Give specific line numbers if Cursor misses something
2. Reference good examples (customer_management.php)
3. Ask Cursor to "double-check" if unsure
4. Use "explain what you changed" for verification

---

## 🎓 Learning from Each Module

After each module, note down:
- **What worked well?**
- **What did Cursor AI miss?**
- **Any new badge patterns discovered?**
- **Any edge cases to document?**

Update this file with learnings!

---

## 📞 When to Stop & Ask for Help

**STOP and discuss if:**
- ❌ Cursor AI suggests changing business logic
- ❌ Cursor AI wants to modify CSRF handling
- ❌ JavaScript errors appear after changes
- ❌ DataTables break after update
- ❌ Permission checks get removed
- ❌ Database queries get modified

**Just rollback and ping team for review.**

---

## 🎉 Completion Celebration

When all 9 modules DONE:
1. ✅ Take full screenshot walkthrough
2. ✅ Update ROADMAP_CSS_TO_LOGIC.md
3. ✅ Create summary report (optional)
4. ✅ Move to Phase 2: Business Logic Enhancement

**You'll have:**
- Consistent UI across all modules
- Professional, clean interface
- Maintainable CSS standards
- Ready for business logic work

---

**Good luck with CSS standardization!** 🚀

**Next File to Open in Cursor AI:**  
`app/Views/marketing/spk_marketing.php`

**Prompt to use:** See "Step 2" above
