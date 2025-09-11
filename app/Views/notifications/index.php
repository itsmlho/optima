<?= $this->extend('layouts/base') ?>

<?= $this->section('title') ?>Notification Center - OPTIMA<?= $this->endSection() ?>

<?= $this->section('content') ?>

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
    /*
     * =================================================================================================
     * ENHANCED NOTIFICATION CENTER - OPTIMA
     * Modern, Feature-Rich, Performance Optimized
     * =================================================================================================
     */

    :root {
        /* OPTIMA Website Color Palette - Matching existing theme */
        --primary: #0061f2;  /* OPTIMA primary blue */
        --primary-dark: #004dc7;
        --primary-light: #e7ebfd;
        --primary-alpha-10: rgba(0, 97, 242, 0.1);
        --primary-alpha-20: rgba(0, 97, 242, 0.2);
        
        /* Semantic Colors - Matching Bootstrap vars */
        --success: #00ac69;
        --success-light: #d1fae5;
        --warning: #ffb607;
        --warning-light: #fef3c7;
        --danger: #e81500;
        --danger-light: #fee2e2;
        --info: #39afd1;
        --info-light: #dbeafe;
        
        /* Neutral Palette - Matching OPTIMA theme */
        --gray-50: #f8f9fa;
        --gray-100: #f3f4f6;
        --gray-200: #e9ecef;
        --gray-300: #dee2e6;
        --gray-400: #ced4da;
        --gray-500: #6c757d;
        --gray-600: #495057;
        --gray-700: #343a40;
        --gray-800: #212529;
        --gray-900: #000000;
        
        /* Gradients - Using OPTIMA style gradients */
        --gradient-primary: linear-gradient(135deg, #0061f2 0%, #4d8cff 100%);
        --gradient-success: linear-gradient(135deg, #00ac69 0%, #4dd289 100%);
        --gradient-warning: linear-gradient(135deg, #ffb607 0%, #ffcc47 100%);
        --gradient-danger: linear-gradient(135deg, #e81500 0%, #ff4757 100%);
        
        /* Layout - Matching OPTIMA spacing */
        --radius-sm: 6px;
        --radius-md: 8px;
        --radius-lg: 12px;
        --radius-xl: 16px;
        
        /* Shadows - Matching Bootstrap shadow system */
        --shadow-xs: 0 1px 2px rgba(0, 0, 0, 0.05);
        --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
        --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        --shadow-glow: 0 0 20px rgba(0, 97, 242, 0.15);
        
        /* Animations - Smooth like OPTIMA */
        --transition-base: all 0.15s ease-in-out;
        --transition-smooth: all 0.3s ease-in-out;
        --transition-spring: all 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    }

    * {
        box-sizing: border-box;
    }

    /* Dark Mode Support */
    @media (prefers-color-scheme: dark) {
        :root {
            --gray-50: #1f2937;
            --gray-100: #374151;
            --gray-800: #f3f4f6;
            --gray-900: #f9fafb;
        }
    }

    /* Main Container */
    .notification-center {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Header Section */
    .nc-header {
        background: var(--gradient-primary);
        border-radius: var(--radius-xl) var(--radius-xl) 0 0;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .nc-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: float 20s infinite ease-in-out;
    }

    .nc-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .nc-title {
        color: white;
        font-size: 1.875rem;
        font-weight: 700;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .nc-title i {
        font-size: 1.5rem;
        opacity: 0.9;
    }

    .nc-header-actions {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
    }

    .nc-header-btn {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.625rem 1.25rem;
        border-radius: var(--radius-lg);
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        transition: var(--transition-base);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .nc-header-btn:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
    }

    /* Stats Cards */
    .nc-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1rem;
        margin-top: -2rem;
        padding: 0 2rem 1.5rem;
        position: relative;
        z-index: 2;
    }

    .stat-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: 1.25rem;
        box-shadow: var(--shadow-md);
        transition: var(--transition-smooth);
        cursor: pointer;
        border: 2px solid transparent;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-xl);
        border-color: var(--primary);
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.75rem;
        font-size: 1.25rem;
    }

    .stat-card-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--gray-900);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-card-label {
        font-size: 0.875rem;
        color: var(--gray-500);
        font-weight: 500;
    }

    .stat-card-trend {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        border-radius: var(--radius-sm);
        margin-top: 0.5rem;
    }

    .trend-up {
        color: var(--success);
        background: var(--success-light);
    }

    .trend-down {
        color: var(--danger);
        background: var(--danger-light);
    }

    /* Main Panel */
    .nc-panel {
        background: white;
        border-radius: 0 0 var(--radius-xl) var(--radius-xl);
        box-shadow: var(--shadow-xl);
        overflow: hidden;
    }

    /* Search and Filters Bar */
    .nc-controls {
        padding: 1.5rem;
        background: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        gap: 1rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .nc-search {
        flex: 1;
        min-width: 250px;
        position: relative;
    }

    .nc-search-input {
        width: 100%;
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 2px solid var(--gray-200);
        border-radius: var(--radius-lg);
        font-size: 0.875rem;
        transition: var(--transition-base);
        background: white;
    }

    .nc-search-input:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-alpha-10);
    }

    .nc-search-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--gray-400);
        pointer-events: none;
    }

    .nc-filter-group {
        display: flex;
        gap: 0.5rem;
        align-items: center;
    }

    .nc-filter-btn {
        padding: 0.625rem 1rem;
        border: 2px solid var(--gray-200);
        background: white;
        border-radius: var(--radius-md);
        font-size: 0.875rem;
        color: var(--gray-600);
        cursor: pointer;
        transition: var(--transition-base);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 500;
    }

    .nc-filter-btn:hover {
        border-color: var(--primary);
        color: var(--primary);
        background: var(--primary-light);
    }

    .nc-filter-btn.active {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    /* Tabs Navigation */
    .nc-tabs {
        display: flex;
        padding: 0;
        background: white;
        border-bottom: 2px solid var(--gray-100);
        position: relative;
    }

    .nc-tab {
        flex: 1;
        padding: 1.25rem;
        cursor: pointer;
        color: var(--gray-500);
        font-weight: 500;
        transition: var(--transition-base);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        position: relative;
        background: transparent;
        border: none;
        font-size: 0.9375rem;
    }

    .nc-tab:hover {
        color: var(--gray-700);
        background: var(--gray-50);
    }

    .nc-tab.active {
        color: var(--primary);
        font-weight: 600;
    }

    .nc-tab.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--primary);
        border-radius: 3px 3px 0 0;
        animation: slideIn 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nc-tab-badge {
        background: var(--gray-200);
        color: var(--gray-600);
        font-size: 0.75rem;
        padding: 0.125rem 0.5rem;
        border-radius: 999px;
        font-weight: 600;
        min-width: 24px;
        text-align: center;
        transition: var(--transition-base);
    }

    .nc-tab.active .nc-tab-badge {
        background: var(--primary);
        color: white;
        animation: pulse 2s infinite;
    }

    /* Notification List */
    .nc-list {
        padding: 1rem;
        min-height: 400px;
        max-height: 600px;
        overflow-y: auto;
        scroll-behavior: smooth;
    }

    /* Custom Scrollbar */
    .nc-list::-webkit-scrollbar {
        width: 8px;
    }

    .nc-list::-webkit-scrollbar-track {
        background: var(--gray-100);
        border-radius: 4px;
    }

    .nc-list::-webkit-scrollbar-thumb {
        background: var(--gray-400);
        border-radius: 4px;
    }

    .nc-list::-webkit-scrollbar-thumb:hover {
        background: var(--gray-500);
    }

    /* Notification Card */
    .notification-item {
        display: flex;
        gap: 1rem;
        padding: 1.25rem;
        margin-bottom: 0.75rem;
        border-radius: var(--radius-lg);
        border: 2px solid var(--gray-100);
        transition: var(--transition-smooth);
        cursor: pointer;
        position: relative;
        background: white;
        overflow: hidden;
    }

    .notification-item::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: var(--gray-300);
        transition: var(--transition-base);
    }

    .notification-item:hover {
        border-color: var(--primary-alpha-20);
        box-shadow: var(--shadow-md);
        transform: translateX(4px);
        background: var(--gray-50);
    }

    .notification-item.unread {
        background: linear-gradient(90deg, var(--primary-alpha-10) 0%, transparent 100%);
        border-color: var(--primary-alpha-20);
    }

    .notification-item[data-priority="high"]::before {
        background: var(--danger);
    }

    .notification-item[data-priority="medium"]::before {
        background: var(--warning);
    }

    .notification-item[data-priority="low"]::before {
        background: var(--success);
    }

    .notification-item.is-removing {
        animation: slideOut 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    .notification-avatar {
        flex-shrink: 0;
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        position: relative;
    }

    .notification-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: var(--radius-md);
    }

    .notification-avatar-icon {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--radius-md);
    }

    .avatar-status {
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid white;
    }

    .status-online {
        background: var(--success);
    }

    .status-busy {
        background: var(--warning);
    }

    .notification-content {
        flex: 1;
        min-width: 0;
    }

    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 0.5rem;
        gap: 1rem;
    }

    .notification-title {
        font-weight: 600;
        color: var(--gray-900);
        font-size: 0.9375rem;
        line-height: 1.3;
    }

    .notification-meta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        flex-shrink: 0;
    }

    .notification-time {
        font-size: 0.75rem;
        color: var(--gray-400);
        white-space: nowrap;
    }

    .notification-message {
        color: var(--gray-600);
        font-size: 0.875rem;
        line-height: 1.5;
        margin-bottom: 0.75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notification-footer {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .notification-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .notification-tag {
        font-size: 0.75rem;
        padding: 0.25rem 0.625rem;
        border-radius: var(--radius-sm);
        font-weight: 500;
    }

    .tag-project {
        background: var(--info-light);
        color: var(--info);
    }

    .tag-urgent {
        background: var(--danger-light);
        color: var(--danger);
    }

    .tag-review {
        background: var(--warning-light);
        color: var(--warning);
    }

    .notification-actions {
        margin-left: auto;
        display: flex;
        gap: 0.5rem;
        opacity: 0;
        transition: var(--transition-base);
    }

    .notification-item:hover .notification-actions {
        opacity: 1;
    }

    .action-btn {
        width: 32px;
        height: 32px;
        border-radius: var(--radius-md);
        border: 1px solid var(--gray-200);
        background: white;
        color: var(--gray-500);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition-base);
        font-size: 0.875rem;
    }

    .action-btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        transform: scale(1.1);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--gray-400);
    }

    .empty-state-icon {
        width: 120px;
        height: 120px;
        margin: 0 auto 1.5rem;
        background: var(--gray-100);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--gray-300);
    }

    .empty-state h3 {
        font-size: 1.25rem;
        color: var(--gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        font-size: 0.875rem;
        max-width: 400px;
        margin: 0 auto;
    }

    /* Loading State */
    .loading-skeleton {
        padding: 1.25rem;
        margin-bottom: 0.75rem;
        border-radius: var(--radius-lg);
        border: 2px solid var(--gray-100);
        display: flex;
        gap: 1rem;
    }

    .skeleton-avatar {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }

    .skeleton-content {
        flex: 1;
    }

    .skeleton-line {
        height: 14px;
        border-radius: 4px;
        background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
        margin-bottom: 8px;
    }

    .skeleton-line:last-child {
        width: 60%;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideIn {
        from {
            width: 0;
        }
        to {
            width: 100%;
        }
    }

    @keyframes slideOut {
        to {
            opacity: 0;
            transform: translateX(-100%);
        }
    }

    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    @keyframes float {
        0%, 100% {
            transform: translate(0, 0) rotate(0deg);
        }
        33% {
            transform: translate(30px, -30px) rotate(120deg);
        }
        66% {
            transform: translate(-20px, 20px) rotate(240deg);
        }
    }

    @keyframes loading {
        0% {
            background-position: 200% 0;
        }
        100% {
            background-position: -200% 0;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .notification-center {
            margin: 1rem auto;
            padding: 0 0.5rem;
        }

        .nc-header {
            padding: 1.5rem 1rem;
            border-radius: var(--radius-lg) var(--radius-lg) 0 0;
        }

        .nc-title {
            font-size: 1.5rem;
        }

        .nc-stats {
            grid-template-columns: repeat(2, 1fr);
            padding: 0 1rem 1rem;
        }

        .nc-controls {
            padding: 1rem;
        }

        .nc-search {
            min-width: 100%;
        }

        .nc-tabs {
            overflow-x: auto;
        }

        .nc-tab {
            padding: 1rem 0.75rem;
            font-size: 0.875rem;
        }

        .notification-item {
            padding: 1rem;
        }

        .notification-actions {
            opacity: 1;
        }
    }

    /* Tooltip */
    [data-tooltip] {
        position: relative;
    }

    [data-tooltip]:hover::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: 100%;
        left: 50%;
        transform: translateX(-50%);
        padding: 0.5rem 0.75rem;
        background: var(--gray-800);
        color: white;
        font-size: 0.75rem;
        border-radius: var(--radius-sm);
        white-space: nowrap;
        z-index: 1000;
        pointer-events: none;
        margin-bottom: 0.5rem;
    }

    /* Priority Indicators */
    .priority-indicator {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        display: inline-block;
        margin-right: 0.5rem;
        animation: pulse 2s infinite;
    }

    .priority-high {
        background: var(--danger);
    }

    .priority-medium {
        background: var(--warning);
    }

    .priority-low {
        background: var(--success);
    }

    /* Bulk Actions Bar */
    .bulk-actions {
        position: fixed;
        bottom: 2rem;
        left: 50%;
        transform: translateX(-50%);
        background: var(--gray-800);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: var(--radius-lg);
        display: flex;
        align-items: center;
        gap: 1rem;
        box-shadow: var(--shadow-xl);
        z-index: 1000;
        opacity: 0;
        visibility: hidden;
        transition: var(--transition-smooth);
    }

    .bulk-actions.active {
        opacity: 1;
        visibility: visible;
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    @keyframes slideUp {
        from {
            transform: translateX(-50%) translateY(100%);
        }
        to {
            transform: translateX(-50%) translateY(0);
        }
    }
</style>

<div class="notification-center">
    <!-- Header Section -->
    <div class="nc-header">
        <div class="nc-header-content">
            <h1 class="nc-title">
                <i class="fas fa-bell"></i>
                Notification Center
            </h1>
            <div class="nc-header-actions">
                <button class="nc-header-btn" onclick="markAllAsRead()">
                    <i class="fas fa-check-double"></i>
                    Mark All Read
                </button>
                <button class="nc-header-btn" onclick="openSettings()">
                    <i class="fas fa-cog"></i>
                    Settings
                </button>
            </div>
        </div>
    </div>

    

    <!-- Main Panel -->
    <div class="nc-panel">
        <!-- Search and Filters -->
        <div class="nc-controls">
            <div class="nc-search">
                <i class="fas fa-search nc-search-icon"></i>
                <input type="text" class="nc-search-input" id="searchInput" placeholder="Search notifications...">
            </div>
            
            <div class="nc-filter-group">
                <button class="nc-filter-btn" id="filterPriority" onclick="togglePriorityFilter()">
                    <i class="fas fa-flag"></i>
                    Priority
                </button>
                <button class="nc-filter-btn" id="filterType" onclick="toggleTypeFilter()">
                    <i class="fas fa-tag"></i>
                    Type
                </button>
                <button class="nc-filter-btn" id="filterDate" onclick="toggleDateFilter()">
                    <i class="fas fa-calendar"></i>
                    Date
                </button>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="nc-tabs">
            <button class="nc-tab active" data-view="all" onclick="switchTab('all')">
                <i class="fas fa-inbox"></i>
                <span>All</span>
                <span class="nc-tab-badge" id="allCount">0</span>
            </button>
            <button class="nc-tab" data-view="unread" onclick="switchTab('unread')">
                <i class="fas fa-envelope"></i>
                <span>Unread</span>
                <span class="nc-tab-badge" id="tabUnreadCount">0</span>
            </button>
            <button class="nc-tab" data-view="review" onclick="switchTab('review')">
                <i class="fas fa-bookmark"></i>
                <span>For Review</span>
                <span class="nc-tab-badge" id="tabReviewCount">0</span>
            </button>
            <button class="nc-tab" data-view="archived" onclick="switchTab('archived')">
                <i class="fas fa-archive"></i>
                <span>Archived</span>
                <span class="nc-tab-badge" id="archivedCount">0</span>
            </button>
        </div>

        <!-- Notification List -->
        <div class="nc-list" id="notificationList">
            <!-- Notifications will be loaded here -->
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="empty-state" style="display: none;">
            <div class="empty-state-icon">
                <i class="fas fa-bell-slash"></i>
            </div>
            <h3>No notifications found</h3>
            <p>When you receive notifications, they'll appear here. Stay tuned!</p>
        </div>
    </div>

    <!-- Bulk Actions Bar -->
    <div class="bulk-actions" id="bulkActions">
        <span id="selectedCount">0 selected</span>
        <button class="nc-header-btn" onclick="bulkMarkAsRead()">
            <i class="fas fa-check"></i> Mark as Read
        </button>
        <button class="nc-header-btn" onclick="bulkArchive()">
            <i class="fas fa-archive"></i> Archive
        </button>
        <button class="nc-header-btn" onclick="bulkDelete()">
            <i class="fas fa-trash"></i> Delete
        </button>
    </div>
</div>

<script>
/**
 * Enhanced Notification Center JavaScript
 * Modern, feature-rich notification management system
 */

class NotificationCenter {
    constructor() {
        // Initialize data from PHP
        this.notifications = <?= json_encode($notifications ?? []) ?>;
        
        // Add enhanced properties to notifications
        this.notifications = this.notifications.map(n => ({
            ...n,
            id: n.id || this.generateId(),
            status: n.status || 'unread',
            priority: n.priority || 'low',
            type: n.type || 'info',
            created_at: n.created_at || new Date().toISOString(),
            tags: n.tags || [],
            selected: false
        }));

        // State management
        this.currentView = 'all';
        this.searchTerm = '';
        this.filters = {
            priority: null,
            type: null,
            dateRange: null
        };
        this.selectedItems = new Set();
        
        // Initialize
        this.init();
    }

    init() {
        this.bindEvents();
        this.render();
        this.updateStats();
        this.startRealTimeUpdates();
    }

    bindEvents() {
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', (e) => {
                this.searchTerm = e.target.value.toLowerCase();
                this.render();
            });
        }

        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Don't intercept browser shortcuts like Ctrl+F5, Ctrl+R, etc.
            if (e.key === 'F5' || e.key === 'r' && (e.ctrlKey || e.metaKey)) {
                return; // Let browser handle refresh
            }
            
            if (e.ctrlKey || e.metaKey) {
                switch(e.key) {
                    case 'a':
                        e.preventDefault();
                        this.selectAll();
                        break;
                    case '/':
                        e.preventDefault();
                        document.getElementById('searchInput')?.focus();
                        break;
                }
            }
        });

        // Handle notification clicks
        document.addEventListener('click', (e) => {
            if (e.target.closest('.notification-item')) {
                const item = e.target.closest('.notification-item');
                const id = item.dataset.id;
                
                if (e.ctrlKey || e.metaKey) {
                    this.toggleSelection(id);
                } else if (!e.target.closest('.action-btn')) {
                    this.openNotification(id);
                }
            }
        });
    }

    generateId() {
        return 'notif_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    getFilteredNotifications() {
        let filtered = [...this.notifications];

        // Apply view filter
        if (this.currentView !== 'all') {
            filtered = filtered.filter(n => n.status === this.currentView);
        }

        // Apply search filter
        if (this.searchTerm) {
            filtered = filtered.filter(n => 
                n.message?.toLowerCase().includes(this.searchTerm) ||
                n.sender_name?.toLowerCase().includes(this.searchTerm) ||
                n.tags?.some(tag => tag.toLowerCase().includes(this.searchTerm))
            );
        }

        // Apply priority filter
        if (this.filters.priority) {
            filtered = filtered.filter(n => n.priority === this.filters.priority);
        }

        // Apply type filter
        if (this.filters.type) {
            filtered = filtered.filter(n => n.type === this.filters.type);
        }

        // Apply date filter
        if (this.filters.dateRange) {
            const now = new Date();
            const ranges = {
                today: 24 * 60 * 60 * 1000,
                week: 7 * 24 * 60 * 60 * 1000,
                month: 30 * 24 * 60 * 60 * 1000
            };
            
            if (ranges[this.filters.dateRange]) {
                filtered = filtered.filter(n => {
                    const notifDate = new Date(n.created_at);
                    return (now - notifDate) <= ranges[this.filters.dateRange];
                });
            }
        }

        // Sort by date (newest first)
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));

        return filtered;
    }

    render() {
        const container = document.getElementById('notificationList');
        const emptyState = document.getElementById('emptyState');
        
        if (!container) return;

        const notifications = this.getFilteredNotifications();

        if (notifications.length === 0) {
            container.style.display = 'none';
            if (emptyState) emptyState.style.display = 'block';
            return;
        }

        container.style.display = 'block';
        if (emptyState) emptyState.style.display = 'none';

        container.innerHTML = notifications.map(n => this.createNotificationHTML(n)).join('');
        this.updateStats();
        this.updateBulkActions();
    }

    createNotificationHTML(notification) {
        const timeAgo = this.formatTimeAgo(notification.created_at);
        const avatar = this.getAvatar(notification);
        const priorityClass = notification.priority ? `data-priority="${notification.priority}"` : '';
        const unreadClass = notification.status === 'unread' ? 'unread' : '';
        const selectedClass = this.selectedItems.has(notification.id) ? 'selected' : '';

        return `
            <div class="notification-item ${unreadClass} ${selectedClass}" 
                 data-id="${notification.id}" 
                 ${priorityClass}>
                
                <div class="notification-avatar">
                    ${avatar}
                    ${notification.is_online ? '<span class="avatar-status status-online"></span>' : ''}
                </div>
                
                <div class="notification-content">
                    <div class="notification-header">
                        <div class="notification-title">
                            ${notification.priority === 'high' ? '<span class="priority-indicator priority-high"></span>' : ''}
                            ${notification.sender_name || 'System Notification'}
                        </div>
                        <div class="notification-meta">
                            <span class="notification-time">${timeAgo}</span>
                        </div>
                    </div>
                    
                    <div class="notification-message">
                        ${notification.message || 'No message content'}
                    </div>
                    
                    <div class="notification-footer">
                        <div class="notification-tags">
                            ${this.createTags(notification)}
                        </div>
                        
                        <div class="notification-actions">
                            ${this.createActions(notification)}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    getAvatar(notification) {
        if (notification.avatar_url) {
            return `<img src="${notification.avatar_url}" alt="${notification.sender_name}">`;
        }
        
        const icons = {
            success: 'fa-check-circle',
            warning: 'fa-exclamation-triangle',
            danger: 'fa-exclamation-circle',
            info: 'fa-info-circle',
            user: 'fa-user',
            system: 'fa-cog',
            message: 'fa-comment',
            task: 'fa-tasks',
            calendar: 'fa-calendar'
        };
        
        const icon = icons[notification.type] || 'fa-bell';
        const colors = {
            success: 'var(--success-light)',
            warning: 'var(--warning-light)',
            danger: 'var(--danger-light)',
            info: 'var(--info-light)'
        };
        
        const bgColor = colors[notification.type] || 'var(--gray-100)';
        const iconColor = notification.type ? `var(--${notification.type})` : 'var(--gray-500)';
        
        return `
            <div class="notification-avatar-icon" style="background: ${bgColor}; color: ${iconColor};">
                <i class="fas ${icon}"></i>
            </div>
        `;
    }

    createTags(notification) {
        const tags = [];
        
        if (notification.project) {
            tags.push(`<span class="notification-tag tag-project">${notification.project}</span>`);
        }
        
        if (notification.priority === 'high') {
            tags.push(`<span class="notification-tag tag-urgent">Urgent</span>`);
        }
        
        if (notification.status === 'review') {
            tags.push(`<span class="notification-tag tag-review">Review</span>`);
        }
        
        if (notification.tags && Array.isArray(notification.tags)) {
            notification.tags.forEach(tag => {
                tags.push(`<span class="notification-tag">${tag}</span>`);
            });
        }
        
        return tags.join('');
    }

    createActions(notification) {
        const actions = [];
        
        if (notification.status === 'unread') {
            actions.push(`
                <button class="action-btn" data-tooltip="Mark as Read" 
                        onclick="notificationCenter.markAsRead('${notification.id}')">
                    <i class="fas fa-check"></i>
                </button>
            `);
        }
        
        if (notification.status !== 'archived') {
            actions.push(`
                <button class="action-btn" data-tooltip="Archive" 
                        onclick="notificationCenter.archive('${notification.id}')">
                    <i class="fas fa-archive"></i>
                </button>
            `);
        }
        
        if (notification.status !== 'review') {
            actions.push(`
                <button class="action-btn" data-tooltip="Save for Review" 
                        onclick="notificationCenter.saveForReview('${notification.id}')">
                    <i class="fas fa-bookmark"></i>
                </button>
            `);
        }
        
        actions.push(`
            <button class="action-btn" data-tooltip="Delete" 
                    onclick="notificationCenter.delete('${notification.id}')">
                <i class="fas fa-trash"></i>
            </button>
        `);
        
        return actions.join('');
    }

    formatTimeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        const intervals = [
            { label: 'year', seconds: 31536000 },
            { label: 'month', seconds: 2592000 },
            { label: 'week', seconds: 604800 },
            { label: 'day', seconds: 86400 },
            { label: 'hour', seconds: 3600 },
            { label: 'minute', seconds: 60 }
        ];
        
        for (const interval of intervals) {
            const count = Math.floor(seconds / interval.seconds);
            if (count >= 1) {
                return count === 1 
                    ? `1 ${interval.label} ago` 
                    : `${count} ${interval.label}s ago`;
            }
        }
        
        return 'Just now';
    }

    updateStats() {
        const stats = {
            unread: this.notifications.filter(n => n.status === 'unread').length,
            urgent: this.notifications.filter(n => n.priority === 'high').length,
            review: this.notifications.filter(n => n.status === 'review').length,
            archived: this.notifications.filter(n => n.status === 'archived').length,
            all: this.notifications.length,
            today: this.notifications.filter(n => {
                const date = new Date(n.created_at);
                const today = new Date();
                return date.toDateString() === today.toDateString();
            }).length
        };

        // Update stat cards
        this.updateElement('unreadCount', stats.unread);
        this.updateElement('urgentCount', stats.urgent);
        this.updateElement('reviewCount', stats.review);
        this.updateElement('totalToday', stats.today);
        
        // Update tab badges
        this.updateElement('allCount', stats.all);
        this.updateElement('tabUnreadCount', stats.unread);
        this.updateElement('tabReviewCount', stats.review);
        this.updateElement('archivedCount', stats.archived);
    }

    updateElement(id, value) {
        const element = document.getElementById(id);
        if (element) element.textContent = value;
    }

    updateBulkActions() {
        const bulkBar = document.getElementById('bulkActions');
        const selectedCount = document.getElementById('selectedCount');
        
        if (this.selectedItems.size > 0) {
            bulkBar?.classList.add('active');
            if (selectedCount) {
                selectedCount.textContent = `${this.selectedItems.size} selected`;
            }
        } else {
            bulkBar?.classList.remove('active');
        }
    }

    // Action Methods
    async markAsRead(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
            notification.status = 'read';
            await this.updateServer(id, { status: 'read' });
            this.animateRemoval(id);
            setTimeout(() => this.render(), 300);
        }
    }

    async archive(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
            notification.status = 'archived';
            await this.updateServer(id, { status: 'archived' });
            this.animateRemoval(id);
            setTimeout(() => this.render(), 300);
        }
    }

    async saveForReview(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification) {
            notification.status = 'review';
            await this.updateServer(id, { status: 'review' });
            this.animateRemoval(id);
            setTimeout(() => this.render(), 300);
        }
    }

    async delete(id) {
        if (confirm('Are you sure you want to delete this notification?')) {
            const index = this.notifications.findIndex(n => n.id === id);
            if (index > -1) {
                this.notifications.splice(index, 1);
                await this.updateServer(id, { action: 'delete' });
                this.animateRemoval(id);
                setTimeout(() => this.render(), 300);
            }
        }
    }

    animateRemoval(id) {
        const element = document.querySelector(`.notification-item[data-id="${id}"]`);
        if (element) {
            element.classList.add('is-removing');
        }
    }

    async updateServer(id, data) {
        try {
            const response = await fetch(`<?= base_url('notifications/update') ?>/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.csrfToken
                },
                body: JSON.stringify(data)
            });
            const json = await response.json();
            // Refresh header/unread count
            if (typeof window.updateNotificationCount === 'function') {
                window.updateNotificationCount();
            }
            return json;
        } catch (error) {
            console.error('Failed to update notification:', error);
        }
    }

    openNotification(id) {
        const notification = this.notifications.find(n => n.id === id);
        if (notification && notification.status === 'unread') {
            this.markAsRead(id);
        }
        
        // You can implement a modal or redirect here
        console.log('Opening notification:', notification);
    }

    toggleSelection(id) {
        if (this.selectedItems.has(id)) {
            this.selectedItems.delete(id);
        } else {
            this.selectedItems.add(id);
        }
        this.render();
    }

    selectAll() {
        const filtered = this.getFilteredNotifications();
        filtered.forEach(n => this.selectedItems.add(n.id));
        this.render();
    }

    markSelectedAsRead() {
        this.selectedItems.forEach(id => this.markAsRead(id));
        this.selectedItems.clear();
    }

    // Real-time updates simulation
    startRealTimeUpdates() {
        // Simulate receiving new notifications
        setInterval(() => {
            // This would normally be a WebSocket connection or SSE
            this.checkForNewNotifications();
        }, 30000); // Check every 30 seconds
    }

    async checkForNewNotifications() {
        // Implement real-time notification checking
        console.log('Checking for new notifications...');
    }
}

// Initialize the notification center
const notificationCenter = new NotificationCenter();

// Global functions for onclick handlers
function switchTab(view) {
    // Simple tab switching for existing notification center
    document.querySelectorAll('.nc-tab').forEach(tab => {
        tab.classList.toggle('active', tab.dataset.view === view);
    });
    
    // This would filter the notifications based on view
    // Implementation depends on your data structure
}

async function markAllAsRead() {
    if (confirm('Mark all notifications as read?')) {
        try {
            const response = await fetch('<?= base_url('notifications/mark-all-read') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Reload page to show updated notifications
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Failed to mark notifications as read');
        }
    }
}

function openSettings() {
    // Redirect to admin panel if user is super admin
    <?php if (session()->get('role') === 'super_admin'): ?>
        window.location.href = '<?= base_url('notifications/admin') ?>';
    <?php else: ?>
        alert('Only administrators can access notification settings');
    <?php endif; ?>
}

// Handle notification clicks for navigation
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to notification items for navigation
    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-id');
            const url = this.getAttribute('data-url');
            const type = this.getAttribute('data-type');
            
            // Mark as read first
            fetch(`<?= base_url('notifications/mark-read') ?>/${notificationId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': window.csrfToken
                }
            }).then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update UI to show as read
                    this.classList.remove('unread');
                    const badge = this.querySelector('.notification-badge');
                    if (badge) badge.remove();
                }
            });
            
            // Navigate based on notification type or URL
            if (url) {
                if (url.startsWith('http')) {
                    window.open(url, '_blank');
                } else {
                    window.location.href = `<?= base_url() ?>${url.replace(/^\//, '')}`;
                }
            } else {
                // Default navigation based on notification type
                const typeUrls = {
                    'spk_created': 'spk',
                    'spk_approved': 'spk',
                    'spk_rejected': 'spk',
                    'di_created': 'delivery',
                    'di_approved': 'delivery',
                    'inventory_low': 'inventory',
                    'rental_due': 'rentals',
                    'maintenance_due': 'maintenance',
                    'report_ready': 'reports'
                };
                
                const defaultUrl = typeUrls[type] || 'dashboard';
                window.location.href = `<?= base_url() ?>${defaultUrl}`;
            }
        });
    });
});
</script>

<?= $this->endSection() ?>