# 🎯 Dashboard Enhancement - Specific Recommendations
## Tailored for PT Sarana Mitra Luas Tbk (Forklift Rental Business)

---

## ✅ Just Implemented

### 1. **📊 Operational Metrics Widget**
Shows real-time operational performance:
- **On-Time Delivery** - Percentage of units delivered on schedule
- **Avg Response Time** - How fast quotations are created
- **SPK Completion Rate** - Percentage of completed work orders
- **Units in Service** - Current units under maintenance/repair

### 2. **👥 Team Performance Widget**
Displays mechanic/team productivity:
- Top 4 performers this week
- SPK completion count per person
- Medals for top 3 (🥇🥈🥉)
- Total team progress vs target

### 3. **⏳ Pending Approvals Widget**
Shows items requiring user action:
- Pending Quotations
- Pending Purchase Orders
- SPK awaiting assignment
- Badge count for quick visibility

---

## 🚀 Additional Specific Recommendations

### **A. Unit Inspection & Packing List Tracker**

#### **Widget: Unit Pre-Delivery Checklist**
```
┌─────────────────────────────────────────────────┐
│ 📋 Unit Pre-Delivery Status                     │
├─────────────────────────────────────────────────┤
│ FL-TOY-08     Ready    ✓ Inspection  ✓ P.List  │
│ EXC-KOM-02    Pending  ✓ Inspection  ✗ P.List  │
│ FD-MIT-15     In-Prep  ✗ Inspection  ✗ P.List  │
│ HY-SUM-23     Ready    ✓ Inspection  ✓ P.List  │
│                                                  │
│ Total Ready: 2/4 units (50%)                    │
└─────────────────────────────────────────────────┘
```

**Purpose:**
- Track inspection completion status
- Monitor packing list preparation
- Ensure units are delivery-ready
- Prevent incomplete handovers

**Data Sources:**
- `spk` table → unit_id, inspection status
- `packing_list` table → completion status
- `inventory_unit` table → unit availability

**Implementation Query:**
```sql
SELECT 
    iu.unit_code,
    s.nomor_spk,
    s.pdi_tanggal_approve IS NOT NULL as inspection_done,
    pl.status as packing_list_status,
    CASE 
        WHEN s.pdi_tanggal_approve IS NOT NULL AND pl.status = 'complete' THEN 'Ready'
        WHEN s.pdi_tanggal_approve IS NOT NULL THEN 'Pending'
        ELSE 'In-Prep'
    END as delivery_status
FROM inventory_unit iu
LEFT JOIN spk s ON s.persiapan_unit_id = iu.id
LEFT JOIN packing_list pl ON pl.unit_id = iu.id
WHERE s.status = 'IN_PROGRESS' OR s.status = 'READY_FOR_DELIVERY'
ORDER BY delivery_status DESC
LIMIT 5
```

**Benefits:**
- ✅ Reduce delivery delays
- ✅ Complete documentation
- ✅ Quality assurance
- ✅ Customer satisfaction

---

### **B. SPK Progress Dashboard**

#### **Widget: SPK Workflow Stages**
```
┌─────────────────────────────────────────────────┐
│ 🔧 SPK Progress Overview                        │
├─────────────────────────────────────────────────┤
│ Stage              Active    Completed    Avg   │
│ ─────────────────────────────────────────────── │
│ Persiapan Unit     3         45          2.5d   │
│ Fabrikasi          2         38          3.2d   │
│ Painting           1         42          1.8d   │
│ PDI                4         40          1.2d   │
│                                                  │
│ Total in Progress: 10 SPKs                      │
│ Avg Completion:    8.7 days per SPK            │
└─────────────────────────────────────────────────┘
```

**Purpose:**
- Visualize SPK pipeline
- Identify bottlenecks
- Track stage durations
- Optimize workflow

**Data Sources:**
- `spk` table → all workflow stages
- Calculated: time between stage approvals

**Implementation Query:**
```sql
-- Active SPKs per stage
SELECT 
    'Persiapan Unit' as stage,
    COUNT(*) as active,
    DATEDIFF(NOW(), persiapan_unit_tanggal_mulai) as avg_days
FROM spk
WHERE persiapan_unit_tanggal_mulai IS NOT NULL 
  AND persiapan_unit_tanggal_approve IS NULL

UNION ALL

SELECT 
    'Fabrikasi' as stage,
    COUNT(*) as active,
    DATEDIFF(NOW(), fabrikasi_tanggal_mulai) as avg_days
FROM spk
WHERE fabrikasi_tanggal_mulai IS NOT NULL 
  AND fabrikasi_tanggal_approve IS NULL

-- Continue for Painting and PDI...
```

**Benefits:**
- ✅ Real-time workflow visibility
- ✅ Bottleneck identification
- ✅ Resource allocation
- ✅ Process optimization

---

### **C. Delivery Instruction (DI) Tracker**

#### **Widget: Active Delivery Instructions**
```
┌─────────────────────────────────────────────────┐
│ 🚚 Active Deliveries                            │
├─────────────────────────────────────────────────┤
│ DI-2026-045  PT Astra    2 units   Tomorrow     │
│ DI-2026-046  Unilever    1 unit    Today 14:00  │
│ DI-2026-047  Semen Ind   3 units   31 Jan       │
│                                                  │
│ Urgent Today: 1  |  This Week: 6                │
└─────────────────────────────────────────────────┘
```

**Purpose:**
- Track scheduled deliveries
- Identify urgent deliveries
- Monitor delivery preparation
- Coordinate logistics

**Data Sources:**
- `delivery_instruction` table
- `kontrak` table → customer info
- Unit counts per DI

**Implementation Query:**
```sql
SELECT 
    di.di_number,
    c.customer_name,
    COUNT(diu.unit_id) as unit_count,
    di.delivery_date,
    CASE 
        WHEN DATE(di.delivery_date) = CURDATE() THEN 'Today'
        WHEN DATE(di.delivery_date) = DATE_ADD(CURDATE(), INTERVAL 1 DAY) THEN 'Tomorrow'
        ELSE DATE_FORMAT(di.delivery_date, '%d %b')
    END as delivery_label,
    di.status
FROM delivery_instruction di
JOIN kontrak c ON c.id = di.kontrak_id
LEFT JOIN delivery_instruction_units diu ON diu.di_id = di.id
WHERE di.status IN ('PENDING', 'IN_TRANSIT')
  AND di.delivery_date >= CURDATE()
GROUP BY di.id
ORDER BY di.delivery_date ASC
LIMIT 5
```

**Benefits:**
- ✅ Timely deliveries
- ✅ Logistics planning
- ✅ Customer communication
- ✅ Resource scheduling

---

### **D. Work Order Complaint Tracker**

#### **Widget: Active Complaints & Resolution**
```
┌─────────────────────────────────────────────────┐
│ ⚠️ Work Order Complaints                        │
├─────────────────────────────────────────────────┤
│ Priority  Count   Avg Resolution    Status      │
│ ───────────────────────────────────────────────│
│ HIGH      2       3.5 days         🔴 Active   │
│ MEDIUM    5       5.2 days         🟡 Active   │
│ LOW       3       7.8 days         🟢 Active   │
│                                                 │
│ Resolved This Month: 24 (avg 4.3 days)         │
│ Customer Satisfaction: 4.2/5.0                  │
└─────────────────────────────────────────────────┘
```

**Purpose:**
- Monitor complaint volume
- Track resolution speed
- Prioritize urgent issues
- Improve service quality

**Data Sources:**
- `work_orders` table
- `complaints` table (if exists)
- Resolution timestamps

**Implementation Query:**
```sql
SELECT 
    wo.priority,
    COUNT(*) as count,
    AVG(DATEDIFF(wo.resolved_date, wo.created_date)) as avg_resolution_days,
    COUNT(CASE WHEN wo.status = 'OPEN' THEN 1 END) as active_count
FROM work_orders wo
WHERE wo.type = 'COMPLAINT'
  AND wo.created_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
GROUP BY wo.priority
ORDER BY FIELD(wo.priority, 'HIGH', 'MEDIUM', 'LOW')
```

**Benefits:**
- ✅ Faster complaint resolution
- ✅ Customer retention
- ✅ Service improvement
- ✅ Issue prevention

---

### **E. KPI Team Dashboard (Enhanced)**

#### **Widget: Team KPIs & Metrics**
```
┌─────────────────────────────────────────────────┐
│ 📊 Team KPIs (This Month)                       │
├─────────────────────────────────────────────────┤
│ Mechanic        SPKs   Quality   On-Time   Eff  │
│ ───────────────────────────────────────────────│
│ Ahmad Saputra   12     98%      95%       4.8   │
│ Budi Prasetyo   10     95%      90%       4.5   │
│ Cahya Maulana   9      100%     100%      5.0   │
│ Doni Anggara    7      92%      85%       4.2   │
│                                                 │
│ Team Average:   9.5    96.25%   92.5%     4.63  │
└─────────────────────────────────────────────────┘
```

**Metrics Explained:**
- **SPKs** - Total SPKs completed
- **Quality** - % of SPKs without rework
- **On-Time** - % completed before deadline
- **Efficiency** - Rating 1-5 (time vs standard)

**Data Sources:**
- `spk` table → assignments, completion dates
- `spk_stages` → quality checks, rework flags
- Target completion times

**Implementation Query:**
```sql
SELECT 
    u.username as mechanic,
    COUNT(s.id) as spks_completed,
    -- Quality: SPKs without rework
    ROUND(COUNT(CASE WHEN s.rework_required = 0 THEN 1 END) * 100.0 / COUNT(s.id), 2) as quality_pct,
    -- On-Time: Completed before target date
    ROUND(COUNT(CASE WHEN s.completion_date <= s.target_date THEN 1 END) * 100.0 / COUNT(s.id), 2) as on_time_pct,
    -- Efficiency: Time vs standard (placeholder)
    ROUND(AVG(5.0 - (DATEDIFF(s.completion_date, s.start_date) - s.standard_days) / 2), 1) as efficiency
FROM spk s
JOIN users u ON u.id = s.assigned_mechanic_id
WHERE s.completion_date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
  AND s.status = 'COMPLETED'
GROUP BY u.id, u.username
ORDER BY spks_completed DESC
LIMIT 10
```

**Benefits:**
- ✅ Performance visibility
- ✅ Fair evaluation
- ✅ Identify training needs
- ✅ Motivate team

---

### **F. Unit Utilization Forecast**

#### **Widget: Unit Demand Forecast**
```
┌─────────────────────────────────────────────────┐
│ 📅 Unit Availability Forecast (Next 4 Weeks)    │
├─────────────────────────────────────────────────┤
│ Week    Available   Rented   Maintenance   Free │
│ ───────────────────────────────────────────────│
│ W5      30          24       3             3    │
│ W6      30          18       2             10   │
│ W7      30          21       1             8    │
│ W8      30          27       2             1    │
│                                                 │
│ ⚠️ Alert: W8 has only 1 unit available!        │
└─────────────────────────────────────────────────┘
```

**Purpose:**
- Capacity planning
- Identify high-demand periods
- Optimize unit allocation
- Plan maintenance windows

**Data Sources:**
- `kontrak` table → contract end dates
- `spk` table → maintenance schedules
- `inventory_unit` table → total units

**Implementation Query:**
```sql
-- Calculate availability by week
SELECT 
    WEEK(date_range.week_start) as week_num,
    COUNT(DISTINCT iu.id) as total_units,
    COUNT(DISTINCT CASE WHEN k.end_date >= date_range.week_start THEN k.unit_id END) as rented,
    COUNT(DISTINCT CASE WHEN s.status = 'IN_PROGRESS' THEN s.unit_id END) as maintenance,
    COUNT(DISTINCT iu.id) - 
    COUNT(DISTINCT CASE WHEN k.end_date >= date_range.week_start THEN k.unit_id END) - 
    COUNT(DISTINCT CASE WHEN s.status = 'IN_PROGRESS' THEN s.unit_id END) as available
FROM (
    -- Generate next 4 weeks
    SELECT DATE_ADD(CURDATE(), INTERVAL n WEEK) as week_start
    FROM (SELECT 0 as n UNION SELECT 1 UNION SELECT 2 UNION SELECT 3) weeks
) date_range
CROSS JOIN inventory_unit iu
LEFT JOIN kontrak k ON k.unit_id = iu.id AND k.end_date >= date_range.week_start
LEFT JOIN spk s ON s.unit_id = iu.id
GROUP BY week_num
ORDER BY week_num
```

**Benefits:**
- ✅ Proactive planning
- ✅ Revenue optimization
- ✅ Prevent overbooking
- ✅ Strategic maintenance

---

## 📐 Recommended Final Dashboard Layout

```
┌──────────────────────────────────────────────────────────────────┐
│                    OPTIMA Dashboard v2.0                          │
├──────────────────────────────────────────────────────────────────┤
│  [KPI Cards - Row 1]                                             │
│  Fleet 78% | Breakdown 5 | Contracts 42 | Pending 8             │
│                                                                  │
│  [New Widgets - Row 2]                                           │
│  ┌───────────────┐ ┌───────────────┐ ┌───────────────┐         │
│  │ Operational   │ │ Team          │ │ Pending       │         │
│  │ Metrics       │ │ Performance   │ │ Approvals     │         │
│  └───────────────┘ └───────────────┘ └───────────────┘         │
│                                                                  │
│  [Charts - Row 3]                                                │
│  ┌──────────────┐ ┌──────────────┐ ┌──────────────┐            │
│  │ Fleet Chart  │ │ Sales Trend  │ │ SPK Progress │            │
│  └──────────────┘ └──────────────┘ └──────────────┘            │
│                                                                  │
│  [Specific Trackers - Row 4]                                    │
│  ┌─────────────┐ ┌─────────────┐ ┌─────────────┐               │
│  │ Pre-Delivery│ │ Active DIs  │ │ Complaints  │               │
│  └─────────────┘ └─────────────┘ └─────────────┘               │
│                                                                  │
│  [Alerts - Row 5]                                                │
│  ┌───────────┐ ┌───────────┐ ┌───────────┐                     │
│  │ Low Stock │ │ Maint Due │ │ Expiring  │                     │
│  └───────────┘ └───────────┘ └───────────┘                     │
│                                                                  │
│  [Bottom Row]                                                    │
│  ┌──────────────────┐ ┌────────────────────────┐               │
│  │ Recent Activities│ │ Unit Forecast          │               │
│  └──────────────────┘ └────────────────────────┘               │
└──────────────────────────────────────────────────────────────────┘
```

---

## 🔧 Implementation Roadmap

### **Phase 1: Core Widgets (Already Done! ✅)**
- ✅ Operational Metrics
- ✅ Team Performance  
- ✅ Pending Approvals

### **Phase 2: Business-Specific Trackers (Next)**
1. **Unit Pre-Delivery Checklist** (Week 1)
   - Database: Add inspection_status, packing_list_status fields
   - Controller: Create endpoint for checklist data
   - View: Add widget to dashboard

2. **SPK Progress Dashboard** (Week 1-2)
   - Controller: Aggregate SPK stage data
   - View: Visual progress bars per stage
   - Analytics: Calculate average times

3. **Active Delivery Instructions** (Week 2)
   - Query: Join DI, kontrak, units tables
   - View: Sortable by date, priority
   - Alerts: Highlight today/tomorrow deliveries

### **Phase 3: Advanced Features (Week 3-4)**
4. **Work Order Complaints Tracker**
   - Database: Ensure complaint tracking
   - Controller: Priority-based filtering
   - View: Resolution time metrics

5. **Enhanced Team KPIs**
   - Calculate quality, on-time, efficiency metrics
   - Add rework tracking
   - Performance trends graph

6. **Unit Availability Forecast**
   - Week-by-week projection
   - Visual capacity heatmap
   - Alert for low availability periods

---

## 💾 Database Enhancements Needed

### **1. Packing List Table** (if not exists)
```sql
CREATE TABLE packing_list (
    id INT PRIMARY KEY AUTO_INCREMENT,
    unit_id INT NOT NULL,
    kontrak_id INT,
    spk_id INT,
    status ENUM('pending', 'in_progress', 'completed') DEFAULT 'pending',
    items_json JSON,
    completed_by INT,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (unit_id) REFERENCES inventory_unit(id),
    FOREIGN KEY (kontrak_id) REFERENCES kontrak(id),
    FOREIGN KEY (spk_id) REFERENCES spk(id)
);
```

### **2. Work Orders / Complaints Table**
```sql
CREATE TABLE work_orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    type ENUM('MAINTENANCE', 'COMPLAINT', 'INSPECTION') NOT NULL,
    priority ENUM('LOW', 'MEDIUM', 'HIGH') DEFAULT 'MEDIUM',
    unit_id INT,
    kontrak_id INT,
    description TEXT,
    status ENUM('OPEN', 'IN_PROGRESS', 'RESOLVED', 'CLOSED') DEFAULT 'OPEN',
    assigned_to INT,
    created_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    resolved_date TIMESTAMP NULL,
    customer_rating TINYINT,
    FOREIGN KEY (unit_id) REFERENCES inventory_unit(id),
    FOREIGN KEY (kontrak_id) REFERENCES kontrak(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id)
);
```

### **3. SPK Enhancement Fields**
```sql
ALTER TABLE spk ADD COLUMN rework_required TINYINT(1) DEFAULT 0;
ALTER TABLE spk ADD COLUMN target_completion_date DATE;
ALTER TABLE spk ADD COLUMN quality_check_passed TINYINT(1) DEFAULT 1;
ALTER TABLE spk ADD COLUMN efficiency_rating DECIMAL(2,1);
```

---

## 📊 Success Metrics

**Track these KPIs after implementation:**

1. **Operational Efficiency**
   - Dashboard load time < 2 seconds
   - User engagement rate > 80% daily
   - Average time to decision reduced by 30%

2. **Business Impact**
   - On-time delivery improved to 98%+
   - Complaint resolution time reduced by 25%
   - SPK completion rate increased to 95%+

3. **Team Performance**
   - Mechanic productivity +15%
   - Quality score average > 95%
   - Rework rate reduced to < 5%

4. **Customer Satisfaction**
   - Delivery delays reduced by 40%
   - Complete documentation at 100%
   - Customer rating > 4.5/5.0

---

**Version:** 2.0  
**Date:** January 28, 2026  
**Status:** ✅ Phase 1 Complete | 📋 Phase 2-3 Planned  
**Next Action:** Test Phase 1 widgets, then implement Phase 2
