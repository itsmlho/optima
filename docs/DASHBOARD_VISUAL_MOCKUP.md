# 🎨 DASHBOARD VISUAL MOCKUP & DESIGN GUIDE
**OPTIMA Executive Dashboard - UI/UX Specifications**

---

## 🖼️ FULL DASHBOARD LAYOUT MOCKUP

```
╔═══════════════════════════════════════════════════════════════════════════════╗
║  🏢 OPTIMA                    📊 Executive Dashboard              👤 Admin ▼ ║
║  Fleet Management System       Real-time Performance Overview    🔔 [5] ⚙️   ║
╠═══════════════════════════════════════════════════════════════════════════════╣
║                                                                                ║
║  🎯 KEY PERFORMANCE INDICATORS                    📅 23 Dec 2024, 14:35  🔄   ║
║  ┌──────────────┬──────────────┬──────────────┬──────────────┬─────────────┐ ║
║  │ 📦 ASSET     │ 🎯 CONTRACT  │ 🔧 SERVICE   │ 👥 CUSTOMER  │ 💰 REVENUE  │ ║
║  │ UTILIZATION  │ GROWTH       │ COMPLETION   │ SATISFACTION │ THIS MONTH  │ ║
║  │              │              │              │              │             │ ║
║  │   82.5%      │    +12.3%    │    98.2%     │   4.8/5.0    │  Rp 125.5M  │ ║
║  │   ━━━━━━━━   │   ━━━━━━━━   │   ━━━━━━━━   │   ⭐⭐⭐⭐⭐    │  ↑ +8.5%    │ ║
║  │  🟢 Optimal  │  🟢 Growing  │  🟢 Excellent│  🟢 High     │  🟢 Up      │ ║
║  │  Target: 75% │  +15 new     │  Target: 95% │  ↑ +0.2     │  vs Last Mo │ ║
║  └──────────────┴──────────────┴──────────────┴──────────────┴─────────────┘ ║
║                                                                                ║
╠════════════════════════════════╦════════════════════════════════╦════════════╣
║                                ║                                ║            ║
║  📦 ASSET & INVENTORY          ║  🎯 MARKETING & SALES          ║ 🔧 SERVICE ║
║  ══════════════════════════    ║  ══════════════════════════    ║ ══════════ ║
║                                ║                                ║            ║
║  ┌──────────────────────────┐ ║  ┌──────────────────────────┐ ║ ┌────────┐ ║
║  │ 🚛 Unit Inventory Status │ ║  │ 📋 Quotation Funnel      │ ║ │WO Stats│ ║
║  ├──────────────────────────┤ ║  ├──────────────────────────┤ ║ ├────────┤ ║
║  │         ╱───╲            │ ║  │      Created: 45         │ ║ │Total:  │ ║
║  │        │  🟢 │           │ ║  │         ↓                │ ║ │  185   │ ║
║  │        │350  │           │ ║  │      Approved: 28 (62%)  │ ║ │        │ ║
║  │         ╲───╱            │ ║  │         ↓                │ ║ │Done:   │ ║
║  │  🔵120  ⚪  🟡25         │ ║  │      Converted: 28       │ ║ │  168   │ ║
║  │         🔴5              │ ║  │                          │ ║ │  91%   │ ║
║  │                          │ ║  │  📊 Conversion: 62.2%    │ ║ └────────┘ ║
║  │ • Rented: 350 (70%)      │ ║  └──────────────────────────┘ ║            ║
║  │ • Available: 120 (24%)   │ ║                                ║ ┌────────┐ ║
║  │ • Maintenance: 25 (5%)   │ ║  ┌──────────────────────────┐ ║ │Priority│ ║
║  │ • Out of Service: 5 (1%) │ ║  │ ⚠️ Contract Expiry Alerts│ ║ │        │ ║
║  └──────────────────────────┘ ║  ├──────────────────────────┤ ║ │Critical│ ║
║                                ║  │ 🔴 7 days:   5 contracts │ ║ │  12    │ ║
║  ┌──────────────────────────┐ ║  │ 🟡 30 days: 18 contracts │ ║ │High    │ ║
║  │ 🔌 Attachment & Battery  │ ║  │ 🔵 90 days: 42 contracts │ ║ │  45    │ ║
║  ├──────────────────────────┤ ║  └──────────────────────────┘ ║ │Medium  │ ║
║  │ Attachments: 85/120 (71%)│ ║                                ║ │  98    │ ║
║  │ ██████████░░░░           │ ║  ┌──────────────────────────┐ ║ │Low     │ ║
║  │                          │ ║  │ 📄 SPK Performance       │ ║ │  30    │ ║
║  │ Chargers: 120/150 (80%)  │ ║  ├──────────────────────────┤ ║ └────────┘ ║
║  │ ████████████░            │ ║  │   Mobilisasi     45  ██  │ ║            ║
║  │                          │ ║  │   Demobilisasi   32  ██  │ ║ ┌────────┐ ║
║  │ Batteries: 145/180 (81%) │ ║  │   Perpindahan    28  ██  │ ║ │ PMPS   │ ║
║  │ ████████████░            │ ║  │   Penggantian    15  █   │ ║ │ Alert  │ ║
║  └──────────────────────────┘ ║  │                          │ ║ ├────────┤ ║
║                                ║  │ Completion: 85.2%        │ ║ │🔴 Over │ ║
║  ┌──────────────────────────┐ ║  └──────────────────────────┘ ║ │  due:5 │ ║
║  │ ⚠️ Low Stock Alerts      │ ║                                ║ │🟡 7day │ ║
║  ├──────────────────────────┤ ║                                ║ │   :12  │ ║
║  │ SP-0023 | Oil Filter  | 3│ ║                                ║ │🔵 30day│ ║
║  │ SP-0145 | Brake Pad   | 5│ ║                                ║ │   :45  │ ║
║  │ SP-0287 | Hydraulic   | 2│ ║                                ║ └────────┘ ║
║  │ SP-0412 | Tire 28x9   | 4│ ║                                ║            ║
║  └──────────────────────────┘ ║                                ║            ║
║                                ║                                ║            ║
╠════════════════════════════════╩════════════════════════════════╩════════════╣
║                                                                                ║
║  📊 DETAILED PERFORMANCE METRICS                                              ║
║  ┌──────────────────────────┬──────────────────────────┬────────────────────┐║
║  │ 🚚 OPERATIONAL (DI/SPK)  │ 👥 TOP CUSTOMERS         │ 📈 MONTHLY TRENDS  │║
║  ├──────────────────────────┼──────────────────────────┼────────────────────┤║
║  │ DI Status:               │ 1. PT Sinar Jaya      ★★││      ╱╲            │║
║  │  ✅ Completed:  70 (82%) │    • Units: 45           ││     ╱  ╲    ╱╲    │║
║  │  🔄 In Transit: 12 (14%) │    • Value: Rp 12.5M     ││    ╱    ╲  ╱  ╲   │║
║  │  ⏸️ Pending:     3 (4%)  │    • WO Avg: 2.5 hrs     ││   ╱      ╲╱    ╲  │║
║  │                          │                          ││  ╱              ╲ │║
║  │ Top Locations:           │ 2. CV Maju Terus      ★★││ Jul Aug Sep Oct Nov│║
║  │  Jakarta     ████████ 45 │    • Units: 38           ││                    │║
║  │  Surabaya    ██████   32 │    • Value: Rp 9.8M      ││ Revenue Trend ↗    │║
║  │  Bandung     ████      28│    • WO Avg: 3.1 hrs     ││                    │║
║  │  Semarang    ███       18│                          ││ WO Volume Trend ↗  │║
║  │  Medan       ██        15│ 3. UD Sejahtera       ★★││                    │║
║  │                          │    • Units: 32           ││ Utilization: 📈    │║
║  │ ⚠️ Temporary Units:      │    • Value: Rp 8.2M      ││                    │║
║  │  Active: 8 units         │    • WO Avg: 2.8 hrs     ││                    │║
║  │  Overdue: 2 returns 🔴   │                          ││                    │║
║  └──────────────────────────┴──────────────────────────┴────────────────────┘║
║                                                                                ║
╠═══════════════════════════════════════════════════════════════════════════════╣
║  🚨 CRITICAL ALERTS & ACTIONS                                                 ║
║  ┌──────────────────────────┬──────────────────────────┬────────────────────┐║
║  │ 🔴 5 PMPS Overdue        │ 🟡 12 Sparepart Low Stock│ 🟡 8 Contracts     │║
║  │    Immediate attention!  │    Restock needed        │    Expiring in 7d  │║
║  │    [View Details →]      │    [View Inventory →]    │    [Contact Now →] │║
║  └──────────────────────────┴──────────────────────────┴────────────────────┘║
║                                                                                ║
║  System Health: 🟢 All Systems Operational  │  Active Users: 24  │  [Export ▼]║
║  Last Updated: 23 Dec 2024, 14:35:22        │  Load Time: 1.2s   │  [Print 🖨]║
╚═══════════════════════════════════════════════════════════════════════════════╝
```

---

## 🎨 DETAILED COMPONENT DESIGNS

### 1. KPI Summary Card
```
┌────────────────────────────┐
│ 📦 ASSET UTILIZATION       │
├────────────────────────────┤
│                            │
│         82.5%              │  ← Large, bold number
│       ━━━━━━━━━            │  ← Visual indicator bar
│                            │
│   🟢 Optimal Performance   │  ← Status badge with color
│   Target: 75%              │  ← Target reference
│   ↑ +5.2% vs Last Month    │  ← Trend indicator
│                            │
│   350 Rented | 120 Avail.  │  ← Quick breakdown
└────────────────────────────┘
```

**CSS Classes:**
- `.kpi-card` - Main container
- `.kpi-value` - Large number display
- `.kpi-indicator` - Progress bar
- `.kpi-status` - Status badge (success/warning/danger)
- `.kpi-trend` - Trend arrow and percentage

---

### 2. Donut Chart with Legend
```
┌────────────────────────────┐
│ 🚛 Unit Status             │
├────────────────────────────┤
│                            │
│         ╱─────╲            │
│        │ 🟢    │           │  ← Donut chart center shows
│        │ 350   │           │     largest segment
│         ╲─────╱            │
│   🔵120   ⚪    🟡25        │  ← Surrounding values
│          🔴5                │
│                            │
│ Legend:                    │
│ 🟢 Rented: 350 (70%)       │
│ 🔵 Available: 120 (24%)    │
│ 🟡 Maintenance: 25 (5%)    │
│ 🔴 Out of Service: 5 (1%)  │
└────────────────────────────┘
```

**Implementation:**
- Chart.js doughnut type
- `cutout: 65%` for donut effect
- Custom center label plugin
- Interactive tooltips on hover

---

### 3. Alert Card (Critical)
```
┌────────────────────────────┐
│ 🔴 CRITICAL ALERT          │  ← Red header for critical
├────────────────────────────┤
│                            │
│  ⚠️ 5 PMPS Work Orders     │  ← Clear icon + count
│     are OVERDUE!           │
│                            │
│  • WO-2024-0245 (3 days)   │  ← List of items
│  • WO-2024-0312 (2 days)   │
│  • WO-2024-0455 (1 day)    │
│  • +2 more...              │
│                            │
│  [View All Details →]      │  ← Action button
└────────────────────────────┘
```

**Color Scheme:**
- 🔴 Critical: `#dc3545` (Red) - Immediate action
- 🟡 Warning: `#ffc107` (Yellow) - Attention needed
- 🔵 Info: `#17a2b8` (Cyan) - FYI

---

### 4. Horizontal Bar Chart (Rankings)
```
┌────────────────────────────┐
│ 📍 Top Service Areas       │
├────────────────────────────┤
│                            │
│ Jakarta    ████████████ 45 │  ← Longest bar, darker color
│                            │
│ Surabaya   ████████     32 │
│                            │
│ Bandung    ██████       28 │
│                            │
│ Semarang   ████         18 │
│                            │
│ Medan      ███          15 │
│                            │
│            [View All →]    │
└────────────────────────────┘
```

**Features:**
- Sorted descending by value
- Bars scaled proportionally
- Values displayed at end of bars
- Hover shows percentage

---

### 5. Funnel Chart (Conversion)
```
┌────────────────────────────┐
│ 📋 Quotation Funnel        │
├────────────────────────────┤
│                            │
│  ┌────────────────────┐    │
│  │   Created: 45      │    │  ← Widest (100%)
│  └────────────────────┘    │
│       ↓ Review             │
│    ┌──────────────┐        │
│    │ Approved: 28 │        │  ← Medium (62%)
│    └──────────────┘        │
│       ↓ Contract           │
│      ┌─────────┐           │
│      │ Won: 28 │           │  ← Narrowest (62%)
│      └─────────┘           │
│                            │
│  Conversion Rate: 62.2% ✓  │  ← Final metric
└────────────────────────────┘
```

**Implementation:**
- Custom SVG or CSS-based funnel
- Animated transitions
- Percentage labels on each stage
- Color gradient from top to bottom

---

### 6. Progress Bar with Stats
```
┌────────────────────────────┐
│ 🔌 Attachment Utilization  │
├────────────────────────────┤
│                            │
│ Used: 85 / Total: 120      │
│                            │
│ ████████████░░░░░          │  ← 71% filled
│                            │
│ 71% Utilization            │
│ Target: 70% ✓              │  ← Met target
│                            │
└────────────────────────────┘
```

**Variants:**
- Green: Above target
- Yellow: Near target (±5%)
- Red: Below target

---

### 7. Table with Ranking
```
┌──────────────────────────────────────────────────┐
│ 🏆 Top 10 Performing Mechanics                   │
├────┬───────────────┬──────┬─────────┬───────────┤
│ #  │ Name          │ WO   │ Avg Time│ Rate      │
├────┼───────────────┼──────┼─────────┼───────────┤
│ 🥇 │ Ahmad Zaki    │ 45   │ 2.5 hrs │ 98% ✓     │  ← Gold medal for #1
│ 🥈 │ Budi Santoso  │ 42   │ 2.8 hrs │ 97% ✓     │  ← Silver for #2
│ 🥉 │ Chandra Lee   │ 38   │ 3.1 hrs │ 95% ✓     │  ← Bronze for #3
│ 4  │ Dedi Kurniawa │ 35   │ 3.2 hrs │ 94% ✓     │
│ 5  │ Eko Prasetyo  │ 32   │ 3.5 hrs │ 92% ✓     │
│ 6  │ Fajar Hidayat │ 30   │ 3.8 hrs │ 90% ✓     │
│ 7  │ Gunawan Wijay │ 28   │ 4.0 hrs │ 88% ⚠     │
│ 8  │ Hendra Susant │ 25   │ 4.2 hrs │ 85% ⚠     │
│ 9  │ Irfan Maulana │ 22   │ 4.5 hrs │ 82% ⚠     │
│ 10 │ Joko Widodo   │ 20   │ 4.8 hrs │ 80% ⚠     │
└────┴───────────────┴──────┴─────────┴───────────┘
```

**Features:**
- Sortable columns (click header)
- Highlight top 3 performers
- Color-coded performance ratings
- Hover for detailed tooltip

---

### 8. Gauge Chart (Semi-circle)
```
┌────────────────────────────┐
│ 🎯 Customer Satisfaction   │
├────────────────────────────┤
│                            │
│        ╱───────╲           │
│       │  4.8   │           │  ← Large score display
│       │  ⭐⭐⭐⭐⭐  │           │  ← Star rating
│        ╲───────╱           │
│   ├─────┼─────┼─────┤      │  ← Gauge needle
│   3.0   4.0   4.5   5.0    │  ← Scale markers
│   🔴    🟡    🟢    🔵     │  ← Color zones
│                            │
│ Target: 4.5 ✓              │
│ ↑ +0.2 vs Last Month       │
└────────────────────────────┘
```

**Configuration:**
- Red zone: 0-3.0 (Poor)
- Yellow zone: 3.0-4.0 (Fair)
- Green zone: 4.0-4.5 (Good)
- Blue zone: 4.5-5.0 (Excellent)

---

### 9. Timeline/Trend Line Chart
```
┌────────────────────────────┐
│ 📈 Monthly Revenue Trend   │
├────────────────────────────┤
│                            │
│ Rp                  ●      │  ← Peak point highlighted
│ 150M            ╱─●─╲      │
│             ╱──●     ╲     │
│ 125M    ╱──●           ●   │  ← Current point
│     ●──●                   │
│ 100M                       │
│                            │
│ Jul Aug Sep Oct Nov Dec    │
│                            │
│ 📊 +8.5% Growth (6 months) │
└────────────────────────────┘
```

**Features:**
- Smooth bezier curves
- Data points marked with dots
- Hover shows exact values
- Shaded area under curve (optional)
- Comparison lines (e.g., target, last year)

---

### 10. Multi-Stat Dashboard Widget
```
┌────────────────────────────┐
│ 🔧 Work Order Overview     │
├──────────┬─────────────────┤
│  Total   │      185        │  ← Large number
├──────────┼─────────────────┤
│ Progress │ ████████░ 91%   │  ← Progress bar
├──────────┼─────────────────┤
│ Details  │ Open: 17        │  ← Breakdown stats
│          │ In Progress: 30 │
│          │ Completed: 168  │
│          │ Cancelled: 5    │
├──────────┼─────────────────┤
│ Avg Time │ 3.2 hours       │  ← Additional metrics
│ SLA Met  │ 98.2% ✓         │
└──────────┴─────────────────┘
```

---

## 🎨 COLOR PALETTE SPECIFICATIONS

### Primary Colors
```css
--primary:        #0061f2;  /* Blue - Marketing */
--success:        #28a745;  /* Green - Success/Active */
--warning:        #ffc107;  /* Yellow - Warning */
--danger:         #dc3545;  /* Red - Critical/Danger */
--info:           #17a2b8;  /* Cyan - Info */
--secondary:      #6c757d;  /* Gray - Secondary */
```

### Division Colors
```css
--marketing:      #0061f2;  /* Blue */
--service:        #e74c3c;  /* Red-Orange */
--warehouse:      #f39c12;  /* Orange */
--purchasing:     #9b59b6;  /* Purple */
--operational:    #1abc9c;  /* Teal */
```

### Background Colors
```css
--bg-light:       #f8f9fa;  /* Light background */
--bg-white:       #ffffff;  /* Card background */
--bg-dark:        #343a40;  /* Dark elements */
--border-color:   #dee2e6;  /* Borders */
```

### Text Colors
```css
--text-primary:   #212529;  /* Main text */
--text-secondary: #6c757d;  /* Secondary text */
--text-muted:     #adb5bd;  /* Muted text */
```

---

## 📏 SPACING & SIZING STANDARDS

### Card Spacing
```css
--card-padding:     1.5rem;
--card-gap:         1rem;
--card-radius:      8px;
--card-shadow:      0 2px 4px rgba(0,0,0,0.05);
```

### Typography
```css
--font-family:      'Inter', -apple-system, sans-serif;
--font-size-base:   14px;
--font-size-large:  18px;
--font-size-small:  12px;
--font-size-kpi:    36px;
--line-height:      1.5;
```

### Grid System
```css
--container-max:    1400px;
--grid-columns:     12;
--grid-gap:         1rem;
```

---

## 🔄 RESPONSIVE BREAKPOINTS

### Desktop (Default - 1200px+)
- Full 3-4 column layout
- All charts visible
- Sidebar navigation
- Large KPI cards

### Tablet (768px - 1199px)
- 2 column layout
- Collapsible sidebar
- Medium KPI cards
- Stacked charts

### Mobile (< 768px)
- Single column
- Hamburger menu
- Small KPI cards
- Swipeable chart carousel
- Simplified tables (card view)

---

## ✨ ANIMATION & INTERACTIONS

### Card Hover Effects
```css
.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}
```

### Chart Animations
- Entrance: Fade in + slide up (300ms)
- Data update: Smooth interpolation (600ms)
- Hover: Scale up 5% (200ms)

### Button Interactions
- Primary: Solid color → Darken on hover
- Secondary: Outline → Fill on hover
- Danger: Red outline → Red fill on hover

### Loading States
```
┌────────────────────────────┐
│ 📊 Loading...              │
├────────────────────────────┤
│                            │
│    ⏳ Fetching data...     │  ← Loading spinner
│                            │
│    ░░░░░░░░░░░░░           │  ← Skeleton placeholder
│    ░░░░░░░░░░░░░           │
│    ░░░░░░░░░░░░░           │
│                            │
└────────────────────────────┘
```

---

## 🎯 ACCESSIBILITY STANDARDS

### WCAG 2.1 AA Compliance
- ✅ Color contrast ratio ≥ 4.5:1 for text
- ✅ Focus indicators on interactive elements
- ✅ Alt text for all images/icons
- ✅ Keyboard navigation support
- ✅ Screen reader compatible
- ✅ Semantic HTML structure

### Color-blind Friendly
- Use patterns in addition to colors
- High contrast between adjacent colors
- Text labels for all data points

---

## 📱 MOBILE-FIRST CARD DESIGN

### Mobile Card Example
```
┌─────────────────┐
│ 📦 ASSETS       │
│                 │
│    82.5%        │  ← Large touch target
│    ━━━━━━━━     │
│  🟢 Optimal     │
│                 │
│ [Details →]     │  ← Clear action button
└─────────────────┘
```

**Mobile Optimizations:**
- Larger touch targets (44x44px min)
- Simplified charts (fewer data points)
- Collapsible details
- Swipe gestures for navigation
- Bottom navigation bar

---

## 🖨️ PRINT STYLESHEET

### Print-Friendly Design
```css
@media print {
    /* Hide navigation, buttons, alerts */
    .navbar, .btn, .alert { display: none; }
    
    /* Show all collapsed sections */
    .collapse { display: block !important; }
    
    /* Black & white optimization */
    * { color: #000 !important; }
    
    /* Page breaks */
    .page-break { page-break-after: always; }
    
    /* Show URLs for links */
    a[href]:after { content: " (" attr(href) ")"; }
}
```

---

## 🚀 PERFORMANCE OPTIMIZATION

### Image Optimization
- SVG for icons (scalable, small filesize)
- Lazy loading for below-fold content
- WebP format with fallback
- Responsive images with srcset

### Code Splitting
- Critical CSS inline
- Async loading for non-critical JS
- Chart library loaded on-demand
- Separate bundles for each section

### Caching Strategy
```
Static assets:  Cache-Control: max-age=31536000
API data:       Cache-Control: max-age=300
Dashboard HTML: Cache-Control: no-cache
```

---

## 📊 CHART.JS CONFIGURATION TEMPLATES

### 1. Donut Chart Template
```javascript
{
    type: 'doughnut',
    data: {
        labels: ['Label1', 'Label2', 'Label3'],
        datasets: [{
            data: [value1, value2, value3],
            backgroundColor: ['#0061f2', '#28a745', '#ffc107'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 15,
                    usePointStyle: true,
                    font: { size: 11 }
                }
            }
        },
        cutout: '65%'
    }
}
```

### 2. Bar Chart Template
```javascript
{
    type: 'bar',
    data: {
        labels: ['Item1', 'Item2', 'Item3'],
        datasets: [{
            label: 'Count',
            data: [45, 32, 28],
            backgroundColor: '#0061f2',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
}
```

### 3. Line Chart Template
```javascript
{
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
        datasets: [{
            label: 'Trend',
            data: [100, 115, 108, 125, 130, 135],
            borderColor: '#0061f2',
            backgroundColor: 'rgba(0, 97, 242, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            y: { beginAtZero: true }
        }
    }
}
```

---

## 📋 IMPLEMENTATION PRIORITY

### Phase 1: Critical (Week 1-2)
1. ✅ KPI Summary Cards (5 metrics)
2. ✅ Unit Status Donut Chart
3. ✅ WO Overview Multi-Stats
4. ✅ Critical Alerts Section
5. ✅ PMPS Schedule Cards

### Phase 2: Important (Week 3-4)
6. ✅ Marketing Funnel Chart
7. ✅ Top Areas Bar Chart
8. ✅ Attachment Progress Bars
9. ✅ Customer Rankings Table
10. ✅ DI Status Overview

### Phase 3: Enhanced (Week 5-6)
11. ✅ Monthly Trend Lines
12. ✅ Mechanic Leaderboard
13. ✅ Supplier Performance
14. ✅ System Health Indicators
15. ✅ Export/Print Functionality

---

**Design Version:** 1.0  
**Last Updated:** 23 Desember 2024  
**Visual Design Guide for Dashboard Implementation**

---

*Print this mockup for reference during UI development!*
