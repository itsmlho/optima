# Optima Development Roadmap: CSS → Logic
**Version:** 1.0  
**Date:** March 11, 2026  
**Purpose:** Roadmap untuk memisahkan perbaikan CSS (Cursor AI) dengan development logic bisnis (kolaborasi tim)

---

## 🎯 Philosophy: Separation of Concerns

### CSS/Visual Layer (Cursor AI)
**Scope:** UI consistency, styling, badge standards, layout improvements  
**Tools:** Cursor AI with visual standards documentation  
**Speed:** Fast iteration, minimal risk  
**Reference:** `docs/CSS_VISUAL_STANDARDS.md`

### Business Logic Layer (Team Development)
**Scope:** Workflow, validation, data processing, integrations  
**Tools:** VS Code Copilot, manual coding  
**Speed:** Careful planning, thorough testing  
**Reference:** Project requirements, user stories

---

## 📋 Phase 1: CSS Standardization (Cursor AI)

### Priority: HIGH 🔴
**Goal:** Konsistensi visual di semua module sesuai Customer Management pattern

#### Module List untuk CSS Update

| Module | File Path | Status | Notes |
|--------|-----------|--------|-------|
| ✅ Customer Management | `app/Views/marketing/customer_management.php` | **COMPLETED** | Reference implementation |
| ✅ Quotations | `app/Views/marketing/quotations.php` | **COMPLETED** | All badges updated (March 6) |
| ✅ Contracts & PO | `app/Views/marketing/kontrak.php` | **COMPLETED** | Updated March 11, 2026. Filter will be replaced with tab system (future) |
| ⏳ SPK Marketing | `app/Views/marketing/spk_marketing.php` | **PENDING** | Needs full review |
| ⏳ Delivery Instructions | `app/Views/marketing/delivery_instructions.php` | **PENDING** | Needs badge update |
| ⏳ Audit Approval | `app/Views/marketing/audit_approval.php` | **PENDING** | Needs layout fix |
| ⏳ Unit Management | `app/Views/operational/units.php` | **PENDING** | Critical - high usage |
| ⏳ Unit Deployment | `app/Views/operational/unit_deployment.php` | **PENDING** | Needs badge system |
| ⏳ Service Requests | `app/Views/service/requests.php` | **PENDING** | Needs layout update |
| ⏳ Finance Invoices | `app/Views/finance/invoices.php` | **PENDING** | Needs badge colors |
| ⏳ Finance Payments | `app/Views/finance/payments.php` | **PENDING** | Needs badge system |
| ⏳ Purchasing | `app/Views/purchasing/index.php` | **PENDING** | Needs full review |

#### Per-Module Checklist

Use this checklist for EACH module update:

**Visual Structure:**
- [ ] Page header moved INSIDE card-header
- [ ] Title dengan Bootstrap icon (bi-*)
- [ ] Subtitle dengan user tip
- [ ] Module documentation added (top comment)

**Badge System:**
- [ ] All `bg-success` → `badge-soft-green`
- [ ] All `bg-danger` → `badge-soft-red`
- [ ] All `bg-warning` → `badge-soft-yellow`
- [ ] All `bg-info` → `badge-soft-cyan`
- [ ] All `bg-primary` → `badge-soft-blue`
- [ ] All `bg-secondary` → `badge-soft-gray`
- [ ] Remove `text-dark` from badges
- [ ] Remove `text-white` from badges

**Typography:**
- [ ] IDs/Codes with `font-monospace` + `badge-soft-blue`
- [ ] Currency with `text-success fw-semibold`
- [ ] Counts with `badge-soft-blue`
- [ ] Supporting text with `text-muted`

**Dropdowns:**
- [ ] Filter labels dengan icon + `fw-semibold`
- [ ] Clean dropdown text (no emoji - keep professional)
- [ ] Customer format: CODE - Name

**Note:** Some modules may use tab-based navigation instead of dropdown filters

**Table:**
- [ ] `table table-striped table-hover mb-0`
- [ ] Header: `table-light`
- [ ] Card body: `p-0`
- [ ] Wrapper: `table-responsive`

#### Cursor AI Workflow

**Step 1: Prepare Module**
```bash
# Open file in Cursor AI
code app/Views/marketing/[module].php
```

**Step 2: Use Cursor AI Prompt** (dari `CSS_VISUAL_STANDARDS.md`)
```
Update this module to follow Optima CSS Visual Standards (docs/CSS_VISUAL_STANDARDS.md):

CRITICAL REQUIREMENTS:
1. Move page header INSIDE card-header
2. Replace ALL bg-* badge classes with badge-soft-*
3. Add module documentation comment
4. Enhance dropdowns with emoji
5. Use badge-soft-blue for IDs with font-monospace
6. Use text-success fw-semibold for currency
7. Implement 3-tier expiry warnings
8. Add icon to filter labels

REFERENCE: app/Views/marketing/customer_management.php
```

**Step 3: Test**
- [ ] Ctrl+F5 hard refresh
- [ ] Visual inspection
- [ ] No JavaScript errors
- [ ] All badges render correctly
- [ ] No console warnings

**Step 4: Commit**
```bash
git add app/Views/marketing/[module].php
git commit -m "style: standardize CSS for [Module] - badge system, layout, typography"
```

#### Estimated Timeline
- **Per module:** 30-60 minutes (Cursor AI assisted)
- **Total modules:** 12 remaining
- **Total time:** 6-12 hours (can be done incrementally)
- **Completion target:** March 15, 2026

---

## 🚀 Phase 2: Business Logic Enhancements (Team Development)

### Priority: MEDIUM-HIGH 🟠
**Goal:** Improve workflow, validation, dan business rules

#### Focus Areas

### 2.1 Marketing Workflow Improvements

**Quotation Module:**
- [ ] **Auto-numbering enhancement** - Gap detection & recovery
- [ ] **Revision workflow** - Better version control
- [ ] **Approval routing** - Multi-level approval
- [ ] **PDF generation** - Custom templates per customer
- [ ] **Email integration** - Send quotation directly
- [ ] **Expiry automation** - Auto-mark expired quotations

**Contract Module:**
- [ ] **Contract renewal automation** - Notification system
- [ ] **Contract amendment** - Track changes history
- [ ] **Multi-unit contracts** - Better unit allocation
- [ ] **Billing automation** - Auto-generate invoices
- [ ] **Contract templates** - Reusable templates
- [ ] **Document management** - Upload contract scans

**SPK Marketing:**
- [ ] **SPK workflow** - Draft → Approved → Executed
- [ ] **Unit reservation** - Prevent double booking
- [ ] **Delivery scheduling** - Integration with operations
- [ ] **Document generation** - SPK PDF templates
- [ ] **Notification system** - Alert relevant parties

### 2.2 Operational Enhancements

**Unit Management:**
- [ ] **Real-time status tracking** - Live unit status
- [ ] **Maintenance scheduling** - Preventive maintenance
- [ ] **GPS integration** - Unit location tracking (future)
- [ ] **Fuel consumption** - Tracking & reporting
- [ ] **Depreciation calculation** - Asset management
- [ ] **Transfer history** - Full audit trail

**Unit Deployment:**
- [ ] **Deployment workflow** - Multi-step approval
- [ ] **Handover checklist** - Digital checklist
- [ ] **Photo upload** - Unit condition documentation
- [ ] **Return workflow** - Return inspection
- [ ] **Damage reporting** - Quick damage log
- [ ] **Deployment analytics** - Utilization reports

**Service Management:**
- [ ] **Service request routing** - Auto-assign technician
- [ ] **SLA tracking** - Response time monitoring
- [ ] **Spare parts integration** - Auto-deduct inventory
- [ ] **Service history** - Per-unit service log
- [ ] **Customer feedback** - Service rating system
- [ ] **Preventive maintenance** - Schedule automation

### 2.3 Finance Enhancements

**Invoicing:**
- [ ] **Auto-invoice generation** - From contracts
- [ ] **Invoice approval** - Multi-level workflow
- [ ] **Payment reminders** - Auto email reminders
- [ ] **Invoice templates** - Custom per customer
- [ ] **Tax calculation** - PPN automation
- [ ] **Invoice numbering** - Sequential tracking

**Payment Processing:**
- [ ] **Payment matching** - Auto-match to invoice
- [ ] **Partial payment** - Support installments
- [ ] **Payment confirmation** - Upload proof
- [ ] **Receipt generation** - Auto PDF receipt
- [ ] **Overdue tracking** - Aging report
- [ ] **Payment gateway** - Online payment (future)

**Financial Reporting:**
- [ ] **AR/AP reports** - Real-time balances
- [ ] **Cash flow projection** - 30/60/90 day forecast
- [ ] **Profitability analysis** - Per customer/unit
- [ ] **Revenue recognition** - Accrual basis
- [ ] **Budget tracking** - Budget vs actual
- [ ] **Dashboard widgets** - Key metrics

### 2.4 Purchasing & Inventory

**Purchasing:**
- [ ] **Purchase request** - Multi-approval workflow
- [ ] **Vendor management** - Vendor database
- [ ] **Quote comparison** - Multi-vendor quotes
- [ ] **PO generation** - Auto PO from approval
- [ ] **Receiving process** - GRN workflow
- [ ] **Purchase analytics** - Spend analysis

**Inventory (Future):**
- [ ] **Spare parts inventory** - Stock tracking
- [ ] **Reorder points** - Auto purchase alerts
- [ ] **Stock movements** - In/out tracking
- [ ] **Physical count** - Stock opname
- [ ] **Valuation methods** - FIFO/LIFO/Average
- [ ] **Inventory reports** - Stock cards

### 2.5 Reporting & Analytics

**Standard Reports:**
- [ ] **Contract expiry report** - 30/60/90 days
- [ ] **Unit utilization** - Deployed vs available
- [ ] **Revenue by customer** - Top customers
- [ ] **Outstanding AR** - Aging analysis
- [ ] **Service performance** - SLA compliance
- [ ] **Quotation conversion** - Win/loss rate

**Dashboards:**
- [ ] **Executive dashboard** - KPIs overview
- [ ] **Marketing dashboard** - Pipeline metrics
- [ ] **Operations dashboard** - Fleet status
- [ ] **Finance dashboard** - Cash position
- [ ] **Service dashboard** - Ticket metrics
- [ ] **Custom widgets** - Configurable

### 2.6 System Enhancements

**Security & Access:**
- [ ] **Role-based permissions** - Granular RBAC
- [ ] **Audit logging** - All critical actions
- [ ] **Password policy** - Security enforcement
- [ ] **Session management** - Timeout handling
- [ ] **IP whitelisting** - Restrict access
- [ ] **Two-factor auth** - Enhanced security (future)

**Integration & API:**
- [ ] **REST API** - External integrations
- [ ] **Webhook support** - Event notifications
- [ ] **WhatsApp Business** - Notifications
- [ ] **Email automation** - SMTP integration
- [ ] **Export formats** - Excel, PDF, CSV
- [ ] **Import tools** - Bulk data import

**Performance:**
- [ ] **Query optimization** - Database tuning
- [ ] **Caching strategy** - Redis/Memcached
- [ ] **Lazy loading** - Optimize page loads
- [ ] **Background jobs** - Queue processing
- [ ] **Database backups** - Automated backups
- [ ] **Error monitoring** - Sentry integration

---

## 📅 Development Timeline & Priorities

### Sprint 1: CSS Standardization (Week 1-2)
**Duration:** 2 weeks  
**Owner:** Cursor AI + Team Review  
**Goal:** Complete all module CSS updates

**Week 1:**
- [ ] SPK Marketing
- [ ] Delivery Instructions
- [ ] Audit Approval
- [ ] Unit Management
- [ ] Unit Deployment
- [ ] Service Requests

**Week 2:**
- [ ] Finance Invoices
- [ ] Finance Payments
- [ ] Purchasing
- [ ] Any dashboard pages
- [ ] Review & quality check
- [ ] Update documentation

### Sprint 2: Critical Business Logic (Week 3-4)
**Duration:** 2 weeks  
**Owner:** Development Team  
**Goal:** High-priority workflow improvements

**Focus:**
- [ ] Contract renewal automation
- [ ] Unit deployment workflow
- [ ] Invoice auto-generation
- [ ] Service request routing
- [ ] Basic reporting dashboards

### Sprint 3: Marketing Enhancements (Week 5-6)
**Duration:** 2 weeks  
**Owner:** Development Team  
**Goal:** Marketing workflow optimization

**Focus:**
- [ ] Quotation revision workflow
- [ ] SPK approval workflow
- [ ] PDF template improvements
- [ ] Email integration
- [ ] Document management

### Sprint 4: Finance Automation (Week 7-8)
**Duration:** 2 weeks  
**Owner:** Development Team  
**Goal:** Finance process automation

**Focus:**
- [ ] Auto-invoice generation
- [ ] Payment matching
- [ ] Overdue tracking
- [ ] AR/AP reports
- [ ] Cash flow projection

### Sprint 5: Operations Excellence (Week 9-10)
**Duration:** 2 weeks  
**Owner:** Development Team  
**Goal:** Operational efficiency

**Focus:**
- [ ] Maintenance scheduling
- [ ] Deployment analytics
- [ ] Service SLA tracking
- [ ] Unit utilization reports
- [ ] Performance dashboards

### Sprint 6: Advanced Features (Week 11-12)
**Duration:** 2 weeks  
**Owner:** Development Team  
**Goal:** Advanced capabilities

**Focus:**
- [ ] API development
- [ ] WhatsApp integration
- [ ] Advanced analytics
- [ ] Custom dashboards
- [ ] Security enhancements

---

## 🎯 Success Metrics

### CSS Standardization Success Criteria
- ✅ 100% modules follow visual standards
- ✅ Zero `bg-*` badge classes in production
- ✅ Consistent header layout across all pages
- ✅ All dropdowns enhanced with emoji
- ✅ Mobile responsive (all breakpoints)
- ✅ Zero console errors
- ✅ Documentation complete

### Business Logic Success Criteria
- ✅ User satisfaction score > 4.5/5
- ✅ Task completion time reduced by 30%
- ✅ Error rate < 1%
- ✅ API response time < 500ms
- ✅ Test coverage > 80%
- ✅ Zero critical bugs in production
- ✅ Documentation complete

---

## 🛠️ Development Workflow

### For CSS Updates (Cursor AI)
```bash
# 1. Open file
code app/Views/[module]/[file].php

# 2. Use Cursor AI with prompt from CSS_VISUAL_STANDARDS.md

# 3. Test locally
php spark serve
# Browse to module, test visually

# 4. Commit
git add app/Views/[module]/[file].php
git commit -m "style: standardize CSS for [Module]"

# 5. Push to branch
git push origin css-standardization
```

### For Business Logic (Team Development)
```bash
# 1. Create feature branch
git checkout -b feature/[feature-name]

# 2. Implement with TDD
# - Write tests first
# - Implement logic
# - Refactor

# 3. Test thoroughly
php vendor/bin/phpunit
php spark test

# 4. Code review
# - Create PR
# - Team review
# - Address feedback

# 5. Merge to main
git checkout main
git merge feature/[feature-name]
git push origin main

# 6. Deploy to staging
# Test in staging environment

# 7. Deploy to production
# After final approval
```

---

## 📚 Reference Documents

### CSS Layer
- ✅ `docs/CSS_VISUAL_STANDARDS.md` - Complete visual guide
- ✅ `/memories/optima-badge-standards.md` - Badge quick reference
- ✅ `public/assets/css/optima-pro.css` - CSS source

### Business Logic Layer
- ✅ `docs/DATABASE_SCHEMA.md` - Database structure
- ✅ `docs/MARKETING_MODULE_AUDIT_REPORT.md` - Marketing analysis
- ✅ `.github/copilot-instructions.md` - CodeIgniter standards
- ✅ `.github/instructions/php-codeigniter.instructions.md` - PHP guidelines
- ✅ `.github/skills/database-migration/SKILL.md` - Migration workflow

---

## 🎓 Learning Resources

### For CSS Updates
- Bootstrap 5 Documentation: https://getbootstrap.com/docs/5.3/
- Bootstrap Icons: https://icons.getbootstrap.com/
- Font Awesome: https://fontawesome.com/icons

### For Business Logic
- CodeIgniter 4 Docs: https://codeigniter.com/user_guide/
- PHP Best Practices: https://www.php-fig.org/psr/
- Domain-Driven Design: https://martinfowler.com/tags/domain%20driven%20design.html

---

## 🚨 Risk Mitigation

### CSS Updates
**Low Risk** - Visual changes only
- Backup before starting
- Test in development first
- Can rollback easily if needed
- No database changes
- No logic changes

### Business Logic
**Medium-High Risk** - Data & workflow changes
- Comprehensive testing required
- Staging environment mandatory
- Database backups before deployment
- Rollback plan prepared
- User training may be needed

---

## 💡 Next Actions

### Immediate (This Week)
1. ✅ Complete CSS Standards documentation
2. ✅ Save badge standards to memory
3. ⏳ Start CSS updates with Cursor AI (SPK Marketing)
4. ⏳ Plan Sprint 1 in detail

### Short Term (This Month)
1. ⏳ Complete all CSS standardization
2. ⏳ Begin contract renewal automation
3. ⏳ Implement invoice auto-generation
4. ⏳ Deploy first business logic improvements

### Long Term (Next 3 Months)
1. ⏳ Complete all planned business logic
2. ⏳ Advanced analytics implementation
3. ⏳ API development
4. ⏳ Integration ecosystem (WhatsApp, etc)

---

**Document Owner:** Development Team  
**Last Updated:** March 11, 2026  
**Next Review:** March 25, 2026 (After Sprint 1)

---

## 📞 Contact & Support

**Questions about CSS Standards?**  
→ Refer to `docs/CSS_VISUAL_STANDARDS.md`

**Questions about Business Logic?**  
→ Create issue in GitHub or discuss in team meeting

**Found a bug?**  
→ Log in issue tracker with reproduction steps

**Need help from AI?**  
→ Use Cursor AI for CSS, VS Code Copilot for logic
