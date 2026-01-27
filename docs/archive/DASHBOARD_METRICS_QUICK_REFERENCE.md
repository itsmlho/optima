# 📊 DASHBOARD METRICS - QUICK REFERENCE GUIDE
**OPTIMA System Dashboard Upgrade**

---

## 📋 METRICS SUMMARY TABLE

| # | Module | Metric Name | Visualization | Priority | Data Source | Refresh Rate |
|---|--------|-------------|---------------|----------|-------------|--------------|
| **1. ASSET & INVENTORY** |
| 1.1 | Asset | **Asset Utilization Rate** | Gauge Chart | 🔴 Critical | `inventory_unit` | 5 min |
| 1.2 | Asset | Unit Status Distribution | Donut Chart | 🔴 Critical | `inventory_unit`, `status_unit` | 5 min |
| 1.3 | Asset | Unit Aging Analysis | Stacked Bar | 🟡 Medium | `inventory_unit` | Daily |
| 1.4 | Asset | Top 10 Areas by Units | Horizontal Bar | 🟢 Low | `inventory_unit`, `areas` | 1 hour |
| 1.5 | Asset | Attachment Utilization | Progress Bar | 🔴 Critical | `inventory_attachment` | 5 min |
| 1.6 | Asset | Charger & Battery Status | Stats Cards | 🟡 Medium | `inventory_attachment` | 15 min |
| 1.7 | Warehouse | **Low Stock Alerts** | Alert Table | 🔴 Critical | `inventory_spareparts`, `sparepart` | Real-time |
| 1.8 | Warehouse | Top 10 Used Spareparts | Bar Chart | 🟢 Low | `work_order_sparepart_usage` | Daily |
| **2. MARKETING & SALES** |
|2.1 | Marketing | **Quotation Funnel** | Funnel Chart | 🔴 Critical | `quotations` | 15 min |
| 2.2 | Marketing | Quotation Conversion Rate | Gauge + Stats | 🔴 Critical | `quotations` | 15 min |
| 2.3 | Marketing | **Active Contracts** | Stats Card | 🔴 Critical | `kontrak` | 5 min |
| 2.4 | Marketing | Contract Growth Rate | Trend Line | 🟡 Medium | `kontrak` | Daily |
| 2.5 | Marketing | **Contract Expiry Alerts** | Alert Cards | 🔴 Critical | `kontrak` | Real-time |
| 2.6 | Marketing | SPK Completion Rate | Stats + Progress | 🔴 Critical | `spk` | 15 min |
| 2.7 | Marketing | SPK by Type | Pie Chart | 🟡 Medium | `spk`, `jenis_perintah_kerja` | 1 hour |
| **3. SERVICE & MAINTENANCE** |
| 3.1 | Service | **WO Completion Rate** | Gauge Chart | 🔴 Critical | `work_orders`, `work_order_statuses` | 5 min |
| 3.2 | Service | WO Status Overview | Multi-Stats | 🔴 Critical | `work_orders` | 5 min |
| 3.3 | Service | **Overdue WO** | Alert Card | 🔴 Critical | `work_orders` | Real-time |
| 3.4 | Service | WO by Priority | Horizontal Bar | 🔴 Critical | `work_orders`, `work_order_priorities` | 15 min |
| 3.5 | Service | Top 10 WO Categories | Bar Chart | 🟡 Medium | `work_orders`, `work_order_categories` | 1 hour |
| 3.6 | Service | Top 10 Areas by WO | Horizontal Bar | 🟡 Medium | `work_orders`, `areas` | 1 hour |
| 3.7 | Service | **PMPS Overdue** | Alert Card | 🔴 Critical | `work_orders` | Real-time |
| 3.8 | Service | PMPS Schedule (7/30 days) | Stats Cards | 🔴 Critical | `work_orders` | 15 min |
| 3.9 | Service | **Avg Response Time** | Stats Card | 🔴 Critical | `work_orders` | 15 min |
| 3.10 | Service | Top 10 Mechanics | Leaderboard Table | 🟢 Low | `work_order_assignments`, `employees` | Daily |
| **4. PURCHASING & PROCUREMENT** |
| 4.1 | Purchasing | PO Status Distribution | Donut Chart | 🟡 Medium | `purchase_orders` | 15 min |
| 4.2 | Purchasing | PO by Type | Pie Chart | 🟢 Low | `purchase_orders`, `po_units`, `po_attachment` | 1 hour |
| 4.3 | Purchasing | Total PO Value | Stats Card | 🟡 Medium | `purchase_orders` | 1 hour |
| 4.4 | Purchasing | **Top 5 Suppliers** | Ranked Table | 🟡 Medium | `suppliers`, `purchase_orders` | Daily |
| 4.5 | Purchasing | **On-Time Delivery Rate** | Gauge Chart | 🔴 Critical | `po_deliveries` | 1 hour |
| 4.6 | Purchasing | Pending Verifications | Alert Card | 🔴 Critical | `po_verification` | Real-time |
| 4.7 | Purchasing | Rejection Rate | Stats Card | 🟡 Medium | `po_verification` | Daily |
| 4.8 | Purchasing | Avg Procurement Cycle | Stats Card | 🟢 Low | `purchase_orders`, `po_deliveries` | Daily |
| **5. OPERATIONAL (SPK & DI)** |
| 5.1 | Operational | **DI Status Overview** | Multi-Stats | 🔴 Critical | `delivery_instructions` | 5 min |
| 5.2 | Operational | DI Completion Rate | Progress Bar | 🔴 Critical | `delivery_instructions` | 15 min |
| 5.3 | Operational | DI by Type | Pie Chart | 🟡 Medium | `delivery_instructions`, `jenis_perintah_kerja` | 1 hour |
| 5.4 | Operational | Top 10 Delivery Locations | Bar Chart | 🟢 Low | `delivery_instructions` | Daily |
| 5.5 | Operational | **Temporary Units** | Alert Card | 🔴 Critical | Custom query | Real-time |
| 5.6 | Operational | Overdue Returns | Alert Card | 🔴 Critical | Custom query | Real-time |
| 5.7 | Operational | Avg Processing Time | Stats Card | 🟡 Medium | `delivery_instructions` | Daily |
| **6. CUSTOMER MANAGEMENT** |
| 6.1 | CRM | **Total Customers** | Stats Card | 🔴 Critical | `customers` | 1 hour |
| 6.2 | CRM | Customer Growth Rate | Trend + Stats | 🟡 Medium | `customers` | Daily |
| 6.3 | CRM | Active vs Inactive | Donut Chart | 🟡 Medium | `customers`, `kontrak` | 1 hour |
| 6.4 | CRM | **Top 10 Customers** | Ranked Table | 🟡 Medium | `customers`, `kontrak` | Daily |
| 6.5 | CRM | Customer Satisfaction Score | Gauge Chart | 🔴 Critical | Calculated from WO | Daily |
| 6.6 | CRM | Churn Rate | Stats Card | 🟡 Medium | `kontrak` | Weekly |
| **7. HUMAN RESOURCES** |
| 7.1 | HR | Employees by Division | Stacked Bar | 🟢 Low | `employees`, `divisions` | Daily |
| 7.2 | HR | Active Users | Stats Card | 🟢 Low | `users`, `user_sessions` | 15 min |
| 7.3 | HR | Mechanic Workload | Table | 🟡 Medium | `work_order_assignments` | 1 hour |
| 7.4 | HR | Performance Leaderboard | Table | 🟢 Low | `work_order_assignments` | Daily |
| **8. SYSTEM HEALTH** |
| 8.1 | System | Active Sessions | Stats Card | 🟢 Low | `user_sessions` | 5 min |
| 8.2 | System | System Activity | Line Chart | 🟢 Low | `system_activity_log` | 15 min |
| 8.3 | System | Notifications Sent | Stats Card | 🟢 Low | `notifications` | 15 min |
| 8.4 | System | Unread Notifications | Alert Badge | 🟢 Low | `notifications` | Real-time |

---

## 🎨 VISUALIZATION CHEAT SHEET

### Stats Card
```
Use for: Single numeric values with trend
Best for: KPIs, totals, percentages
Example: Total Assets, Completion Rate, Growth %
```

### Donut/Pie Chart
```
Use for: Status distribution, category breakdown
Best for: 3-7 segments
Example: Unit Status, PO Status, Customer Types
```

### Bar Chart (Vertical/Horizontal)
```
Use for: Ranking, comparisons
Best for: Top N lists
Example: Top 10 Areas, Top 5 Suppliers, Top Categories
```

### Line Chart
```
Use for: Trends over time
Best for: Historical analysis
Example: Monthly trends, Year-over-year comparison
```

### Gauge Chart
```
Use for: Performance against target
Best for: KPIs with thresholds
Example: Utilization Rate, SLA Compliance, Satisfaction Score
```

### Funnel Chart
```
Use for: Process conversion tracking
Best for: Multi-stage workflows
Example: Quotation → Contract, PO → Delivery
```

### Progress Bar
```
Use for: Completion status, utilization
Best for: Visual percentage representation
Example: Task completion, Resource usage
```

### Alert Cards
```
Use for: Actionable items, warnings
Best for: Items needing immediate attention
Example: Low stock, Overdue items, Expiring contracts
```

### Table (Ranked/Detailed)
```
Use for: Detailed listings, leaderboards
Best for: Multiple attributes, drill-down
Example: Top Customers, Mechanic Performance
```

---

## 🚦 PRIORITY INDICATORS

### 🔴 CRITICAL (Phase 1 - Week 1-2)
Must-have metrics for executive decision-making:
- Asset Utilization Rate
- WO Completion Rate
- Contract Status & Alerts
- PMPS Overdue
- Low Stock Alerts
- Customer Satisfaction

### 🟡 MEDIUM (Phase 2 - Week 3-4)
Important for operational monitoring:
- Detailed breakdowns by category
- Supplier performance
- Employee workload
- Processing time metrics

### 🟢 LOW (Phase 3 - Week 5-6)
Nice-to-have for analysis:
- Historical trends
- Detailed rankings
- System health monitoring

---

## 🔄 REFRESH RATE GUIDE

| Rate | Metrics | Method |
|------|---------|--------|
| **Real-time** (SSE) | Critical alerts, notifications | Server-Sent Events |
| **5 minutes** | Core KPIs, asset status | Auto-refresh |
| **15 minutes** | Operational metrics | Auto-refresh |
| **1 hour** | Analytical data, rankings | Cached + Auto-refresh |
| **Daily** | Historical trends, reports | Scheduled job |
| **On-demand** | Full dashboard | Manual refresh button |

---

## 📐 DASHBOARD GRID LAYOUT

```
Row 1: KPI SUMMARY STRIP
[───────────────────────────────────────────────────────────────]
│  Asset Util │ Contracts │ WO Rate │ Customer Sat │ PMPS Status │
[───────────────────────────────────────────────────────────────]

Row 2: MAIN CONTENT (3 Columns)
[─────────────────] [─────────────────] [─────────────────]
│   ASSET MGMT    │ │  MARKETING/SALES│ │    SERVICE      │
│                 │ │                 │ │                 │
│ • Unit Status   │ │ • Quotation     │ │ • WO Overview   │
│ • Utilization   │ │ • Contracts     │ │ • By Priority   │
│ • Spareparts    │ │ • SPK Summary   │ │ • By Category   │
[─────────────────] [─────────────────] [─────────────────]

Row 3: SECONDARY CONTENT (3 Columns)
[─────────────────] [─────────────────] [─────────────────]
│  OPERATIONAL    │ │   CUSTOMERS     │ │    TRENDS       │
│                 │ │                 │ │                 │
│ • DI Status     │ │ • Portfolio     │ │ • Monthly       │
│ • Locations     │ │ • Top 10        │ │ • Comparisons   │
│ • Temp Units    │ │ • Satisfaction  │ │ • Forecasts     │
[─────────────────] [─────────────────] [─────────────────]

Row 4: ALERTS & ACTIONS
[───────────────────────────────────────────────────────────────]
│ 🔴 5 PMPS Overdue │ 🟡 12 Low Stock │ 🟡 8 Expiring │ [Export] │
[───────────────────────────────────────────────────────────────]
```

---

## 🎯 COLOR SCHEME

### Status Colors
- 🟢 **Success/Active:** `#28a745` (Green)
- 🔵 **Info/In Progress:** `#17a2b8` (Cyan)
- 🟡 **Warning:** `#ffc107` (Yellow)
- 🔴 **Danger/Critical:** `#dc3545` (Red)
- ⚫ **Inactive/Disabled:** `#6c757d` (Gray)

### Division Colors
- **Marketing:** `#0061f2` (Blue)
- **Service:** `#e74c3c` (Red-Orange)
- **Warehouse:** `#f39c12` (Orange)
- **Purchasing:** `#9b59b6` (Purple)
- **Operational:** `#1abc9c` (Teal)

### Performance Indicators
- **Excellent (>90%):** `#28a745` (Green)
- **Good (75-90%):** `#17a2b8` (Cyan)
- **Fair (60-75%):** `#ffc107` (Yellow)
- **Poor (<60%):** `#dc3545` (Red)

---

## 📊 SAMPLE DATA STRUCTURE

### KPI Summary Object
```javascript
{
  asset_utilization: {
    value: 82.5,
    target: 75,
    trend: +5.2,
    status: 'success'
  },
  wo_completion_rate: {
    value: 98.2,
    target: 95,
    trend: -1.2,
    status: 'success'
  },
  customer_satisfaction: {
    value: 4.8,
    target: 4.5,
    trend: +0.2,
    status: 'success'
  }
}
```

### Chart Data Object
```javascript
{
  labels: ['Available', 'Rented', 'Maintenance', 'Out of Service'],
  values: [120, 350, 25, 5],
  colors: ['#0061f2', '#28a745', '#ffc107', '#dc3545']
}
```

### Alert Object
```javascript
{
  type: 'critical', // critical, warning, info
  icon: 'fa-exclamation-triangle',
  title: 'Overdue PMPS',
  message: '5 PMPS work orders are overdue',
  count: 5,
  link: '/service/work-orders?filter=overdue_pmps'
}
```

---

## 🔧 TECHNICAL STACK

### Backend
- **Framework:** CodeIgniter 4
- **Database:** MySQL/MariaDB
- **Caching:** Redis (recommended)
- **Real-time:** Server-Sent Events (SSE)

### Frontend
- **Charts:** Chart.js 3.x
- **Framework:** Bootstrap 5
- **Icons:** Font Awesome 6
- **Ajax:** Fetch API

### Performance
- **Query Optimization:** Indexes, Views
- **Caching Strategy:** 5-min to Daily
- **Lazy Loading:** Below-fold content
- **CDN:** Static assets

---

## 📋 IMPLEMENTATION CHECKLIST

### Phase 1: Critical Dashboard (Week 1-2)
- [ ] Setup database views/indexes
- [ ] Create dashboard controller methods
- [ ] Implement caching layer
- [ ] Build 5 critical KPI cards
- [ ] Create Unit Status donut chart
- [ ] Create WO Performance charts
- [ ] Implement alert system
- [ ] Add auto-refresh functionality
- [ ] Mobile responsive design
- [ ] Testing & QA

### Phase 2: Operational Dashboard (Week 3-4)
- [ ] Add Marketing/Sales metrics
- [ ] Add Purchasing metrics
- [ ] Implement detailed breakdowns
- [ ] Create additional charts
- [ ] Add export functionality
- [ ] Implement filters
- [ ] Add drill-down capability
- [ ] Testing & QA

### Phase 3: Analytical Dashboard (Week 5-6)
- [ ] Add trend analysis
- [ ] Implement comparisons
- [ ] Add forecasting (optional)
- [ ] Create custom report builder
- [ ] Add scheduling/email reports
- [ ] Performance optimization
- [ ] Final testing & deployment

---

## 📞 CONTACTS & RESOURCES

### Documentation
- Main Audit: `DASHBOARD_AUDIT_COMPREHENSIVE.md`
- Technical Guide: `DASHBOARD_IMPLEMENTATION_TECHNICAL.md`
- This Quick Reference: `DASHBOARD_METRICS_QUICK_REFERENCE.md`

### Support
- Database Schema: `/databases/optima_db_24-11-25_FINAL.sql`
- Current Dashboard: `/app/Views/dashboard.php`
- Controller: `/app/Controllers/Dashboard.php`

---

**Document Version:** 1.0  
**Last Updated:** 23 Desember 2024  
**Quick Reference Guide for Dashboard Implementation**

---

*Print this for easy reference during development!*
