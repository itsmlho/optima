<?= $this->extend('templates/pro_layout') ?>

<?= $this->section('content') ?>

<!-- Finance & KPI Dashboard Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-2 text-gradient">Dashboard Finance & KPI</h1>
        <p class="text-muted mb-0">Monitor financial performance, KPI metrics, dan overall business health</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-success">
            <i class="fas fa-file-excel me-2"></i>Financial Report
        </button>
        <button class="btn btn-success">
            <i class="fas fa-chart-pie me-2"></i>KPI Report
        </button>
    </div>
</div>

<!-- Financial KPI Cards -->
<div class="row g-4 mb-4">
    <!-- Total Revenue -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="pro-stats-card animate-fade-in-up">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="pro-stats-label">Total Revenue</div>
                        <div class="pro-stats-value">Rp 8.4M</div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-arrow-up me-1"></i>+18.2% YoY
                        </div>
                    </div>
                    <div class="pro-stats-icon bg-success">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Profit -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="pro-stats-card animate-fade-in-up" style="animation-delay: 0.1s;">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="pro-stats-label">Net Profit</div>
                        <div class="pro-stats-value">Rp 1.8M</div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-arrow-up me-1"></i>+22.5% margin
                        </div>
                    </div>
                    <div class="pro-stats-icon bg-primary">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Operating Costs -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="pro-stats-card animate-fade-in-up" style="animation-delay: 0.2s;">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="pro-stats-label">Operating Costs</div>
                        <div class="pro-stats-value">Rp 6.2M</div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-arrow-down me-1"></i>-8.5% optimized
                        </div>
                    </div>
                    <div class="pro-stats-icon bg-warning">
                        <i class="fas fa-calculator"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ROI -->
    <div class="col-xl-3 col-lg-6 col-md-6">
        <div class="pro-stats-card animate-fade-in-up" style="animation-delay: 0.3s;">
            <div class="pro-stats-card-body">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="pro-stats-label">ROI</div>
                        <div class="pro-stats-value">24.8%</div>
                        <div class="pro-stats-change text-success">
                            <i class="fas fa-arrow-up me-1"></i>+3.2% improvement
                        </div>
                    </div>
                    <div class="pro-stats-icon bg-info">
                        <i class="fas fa-percentage"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Performance Charts -->
<div class="row g-4 mb-4">
    <!-- Revenue & Profit Trend -->
    <div class="col-xl-8">
        <div class="pro-card">
            <div class="pro-card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="pro-card-title">Revenue & Profit Trend</h5>
                        <p class="pro-card-subtitle">Monthly financial performance</p>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            2025
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">2025</a></li>
                            <li><a class="dropdown-item" href="#">2024</a></li>
                            <li><a class="dropdown-item" href="#">2023</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="pro-card-body">
                <div class="chart-container" style="height: 350px;">
                    <canvas id="financialChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Cost Breakdown -->
    <div class="col-xl-4">
        <div class="pro-card">
            <div class="pro-card-header">
                <h5 class="pro-card-title">Cost Breakdown</h5>
                <p class="pro-card-subtitle">Operating expenses distribution</p>
            </div>
            <div class="pro-card-body">
                <div class="chart-container" style="height: 350px;">
                    <canvas id="costChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KPI Scorecard -->
<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="pro-card">
            <div class="pro-card-header">
                <h5 class="pro-card-title">KPI Scorecard</h5>
                <p class="pro-card-subtitle">Key Performance Indicators across all divisions</p>
            </div>
            <div class="pro-card-body">
                <div class="row g-4">
                    <!-- Financial KPIs -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="kpi-section">
                            <h6 class="kpi-section-title text-success">
                                <i class="fas fa-dollar-sign me-2"></i>Financial KPIs
                            </h6>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Revenue Growth</span>
                                    <span class="kpi-value text-success">+18.2%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 91%"></div>
                                </div>
                                <small class="text-muted">Target: +20%</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Profit Margin</span>
                                    <span class="kpi-value text-success">22.5%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 112%"></div>
                                </div>
                                <small class="text-muted">Target: 20%</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Cash Flow</span>
                                    <span class="kpi-value text-warning">85%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 85%"></div>
                                </div>
                                <small class="text-muted">Target: 90%</small>
                            </div>
                        </div>
                    </div>

                    <!-- Operational KPIs -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="kpi-section">
                            <h6 class="kpi-section-title text-primary">
                                <i class="fas fa-cogs me-2"></i>Operational KPIs
                            </h6>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Fleet Utilization</span>
                                    <span class="kpi-value text-success">82.5%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 103%"></div>
                                </div>
                                <small class="text-muted">Target: 80%</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Service Efficiency</span>
                                    <span class="kpi-value text-success">92.4%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 103%"></div>
                                </div>
                                <small class="text-muted">Target: 90%</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Equipment Uptime</span>
                                    <span class="kpi-value text-success">96%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 107%"></div>
                                </div>
                                <small class="text-muted">Target: 90%</small>
                            </div>
                        </div>
                    </div>

                    <!-- Customer KPIs -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="kpi-section">
                            <h6 class="kpi-section-title text-warning">
                                <i class="fas fa-users me-2"></i>Customer KPIs
                            </h6>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Customer Satisfaction</span>
                                    <span class="kpi-value text-success">4.7/5.0</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 94%"></div>
                                </div>
                                <small class="text-muted">Target: 4.5/5.0</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Contract Renewal</span>
                                    <span class="kpi-value text-success">89%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 111%"></div>
                                </div>
                                <small class="text-muted">Target: 80%</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Response Time</span>
                                    <span class="kpi-value text-success">2.4 hrs</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 120%"></div>
                                </div>
                                <small class="text-muted">Target: 4 hrs</small>
                            </div>
                        </div>
                    </div>

                    <!-- Growth KPIs -->
                    <div class="col-xl-3 col-lg-6">
                        <div class="kpi-section">
                            <h6 class="kpi-section-title text-info">
                                <i class="fas fa-chart-line me-2"></i>Growth KPIs
                            </h6>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">New Customers</span>
                                    <span class="kpi-value text-success">+12</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 120%"></div>
                                </div>
                                <small class="text-muted">Target: +10</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Market Share</span>
                                    <span class="kpi-value text-warning">15.8%</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 79%"></div>
                                </div>
                                <small class="text-muted">Target: 20%</small>
                            </div>
                            <div class="kpi-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="kpi-label">Innovation Index</span>
                                    <span class="kpi-value text-info">7.2/10</span>
                                </div>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 72%"></div>
                                </div>
                                <small class="text-muted">Target: 8.0/10</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Financial Analysis & Alerts -->
<div class="row g-4">
    <!-- Budget vs Actual -->
    <div class="col-xl-6">
        <div class="pro-card">
            <div class="pro-card-header">
                <h5 class="pro-card-title">Budget vs Actual</h5>
                <p class="pro-card-subtitle">Departmental budget performance</p>
            </div>
            <div class="pro-card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Department</th>
                                <th>Budget</th>
                                <th>Actual</th>
                                <th>Variance</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tools text-primary me-2"></i>
                                        <span>Service</span>
                                    </div>
                                </td>
                                <td>Rp 2.5M</td>
                                <td>Rp 2.3M</td>
                                <td>
                                    <span class="text-success fw-bold">-8%</span>
                                </td>
                                <td><span class="badge bg-success">Under Budget</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-bullhorn text-warning me-2"></i>
                                        <span>Marketing</span>
                                    </div>
                                </td>
                                <td>Rp 1.8M</td>
                                <td>Rp 1.9M</td>
                                <td>
                                    <span class="text-warning fw-bold">+5%</span>
                                </td>
                                <td><span class="badge bg-warning">Slight Over</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-truck text-info me-2"></i>
                                        <span>Operations</span>
                                    </div>
                                </td>
                                <td>Rp 3.2M</td>
                                <td>Rp 2.9M</td>
                                <td>
                                    <span class="text-success fw-bold">-9%</span>
                                </td>
                                <td><span class="badge bg-success">Under Budget</span></td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-users text-secondary me-2"></i>
                                        <span>HR & Admin</span>
                                    </div>
                                </td>
                                <td>Rp 1.2M</td>
                                <td>Rp 1.1M</td>
                                <td>
                                    <span class="text-success fw-bold">-8%</span>
                                </td>
                                <td><span class="badge bg-success">Under Budget</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Alerts & Insights -->
    <div class="col-xl-6">
        <div class="pro-card">
            <div class="pro-card-header">
                <h5 class="pro-card-title">Financial Alerts & Insights</h5>
                <p class="pro-card-subtitle">Important financial notifications</p>
            </div>
            <div class="pro-card-body">
                <!-- Cash Flow Alert -->
                <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-water me-2"></i>
                    <div class="flex-1">
                        <strong>Cash Flow Optimization</strong><br>
                        <small>Improve collections to enhance cash position</small>
                    </div>
                    <button class="btn btn-sm btn-outline-info">Review</button>
                </div>

                <!-- Budget Variance -->
                <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div class="flex-1">
                        <strong>Budget Performance</strong><br>
                        <small>Overall 6% under budget this quarter</small>
                    </div>
                    <button class="btn btn-sm btn-outline-success">Details</button>
                </div>

                <!-- Revenue Milestone -->
                <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-trophy me-2"></i>
                    <div class="flex-1">
                        <strong>Revenue Milestone</strong><br>
                        <small>92% of annual target achieved</small>
                    </div>
                    <button class="btn btn-sm btn-outline-warning">Plan</button>
                </div>

                <!-- Key Financial Metrics -->
                <div class="mt-4">
                    <h6 class="mb-3">Key Financial Ratios</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                                <div class="h6 mb-1 text-success">2.8</div>
                                <small class="text-muted">Current Ratio</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-primary bg-opacity-10 rounded">
                                <div class="h6 mb-1 text-primary">45 days</div>
                                <small class="text-muted">DSO</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                                <div class="h6 mb-1 text-warning">1.8x</div>
                                <small class="text-muted">Debt-to-Equity</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                                <div class="h6 mb-1 text-info">24.8%</div>
                                <small class="text-muted">ROE</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
.kpi-section {
    background: var(--bs-gray-50);
    padding: 1.5rem;
    border-radius: 0.75rem;
    border: 1px solid var(--bs-border-color-translucent);
    height: 100%;
}

.kpi-section-title {
    font-size: 0.875rem;
    font-weight: 600;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--bs-border-color-translucent);
}

.kpi-item {
    margin-bottom: 1.5rem;
}

.kpi-item:last-child {
    margin-bottom: 0;
}

.kpi-label {
    font-size: 0.8125rem;
    font-weight: 500;
    color: var(--bs-secondary);
}

.kpi-value {
    font-size: 0.875rem;
    font-weight: 700;
}

[data-theme="dark"] .kpi-section {
    background: #1f2937;
    border-color: #374151;
}

[data-theme="dark"] .kpi-section-title {
    border-bottom-color: #374151;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Financial Trend Chart
const ctx1 = document.getElementById('financialChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Revenue',
            data: [6800, 7200, 7600, 7100, 8200, 7900, 8400, 8800, 8300, 8600, 8900, 8400],
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }, {
            label: 'Profit',
            data: [1200, 1400, 1600, 1300, 1800, 1650, 1800, 2000, 1850, 1900, 2100, 1800],
            borderColor: '#6366f1',
            backgroundColor: 'rgba(99, 102, 241, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                ticks: {
                    callback: function(value) {
                        return 'Rp ' + (value/1000) + 'M';
                    }
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.1)'
                }
            }
        },
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return context.dataset.label + ': Rp ' + (context.parsed.y/1000) + 'M';
                    }
                }
            }
        }
    }
});

// Cost Breakdown Chart
const ctx2 = document.getElementById('costChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: ['Maintenance', 'Fuel', 'Salaries', 'Operations', 'Marketing', 'Others'],
        datasets: [{
            data: [2300, 1800, 1200, 800, 400, 500],
            backgroundColor: [
                '#6366f1',
                '#10b981',
                '#f59e0b',
                '#ef4444',
                '#8b5cf6',
                '#6b7280'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    padding: 20,
                    usePointStyle: true
                }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed * 100) / total).toFixed(1);
                        return context.label + ': Rp ' + (context.parsed/1000) + 'M (' + percentage + '%)';
                    }
                }
            }
        }
    }
});

// Auto refresh data every 5 minutes
setInterval(function() {
    // Add your data refresh logic here
    console.log('Refreshing finance dashboard data...');
}, 300000);
</script>
<?= $this->endSection() ?> 