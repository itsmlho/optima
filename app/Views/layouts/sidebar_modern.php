<?php 
/**
 * Modern Sidebar Navigation - CodePen Style
 * OPTIMA System - PT Sarana Mitra Luas Tbk
 * 
 * Features:
 * - Collapsible sidebar (256px ↔ 80px)
 * - Smooth animations and transitions
 * - Active item highlight with gradient
 * - User profile footer with expandable menu
 * - Fully responsive for mobile
 */

// Get current user info from session
$userSession = session()->get('user');
$userName = $userSession['username'] ?? 'Guest';
$userRole = $userSession['role'] ?? 'User';
$userInitial = strtoupper(substr($userName, 0, 1));
?>

<!-- Modern Sidebar Navigation -->
<input type="checkbox" id="nav-toggle" />
<input type="checkbox" id="nav-footer-toggle" />

<div id="nav-bar">
    
    <!-- Sidebar Header -->
    <div id="nav-header">
        <a id="nav-title" href="<?= base_url('/') ?>">
            <i class="fas fa-cubes"></i>OPTIMA
        </a>
        <label for="nav-toggle">
            <span id="nav-toggle-burger"></span>
        </label>
        <hr>
    </div>

    <!-- Sidebar Content (Menu Items) -->
    <div id="nav-content">
        
        <!-- Dashboard -->
        <a href="<?= base_url('/dashboard') ?>" class="nav-button <?= (uri_string() === '' || uri_string() === 'dashboard') ? 'active' : '' ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>

        <!-- Units Management -->
        <a href="<?= base_url('/units') ?>" class="nav-button <?= (strpos(uri_string(), 'units') !== false) ? 'active' : '' ?>">
            <i class="fas fa-truck"></i>
            <span>Unit Management</span>
        </a>

        <!-- Contracts -->
        <a href="<?= base_url('/contracts') ?>" class="nav-button <?= (strpos(uri_string(), 'contracts') !== false) ? 'active' : '' ?>">
            <i class="fas fa-file-contract"></i>
            <span>Contracts</span>
        </a>

        <hr>

        <!-- Marketing -->
        <a href="<?= base_url('/marketing') ?>" class="nav-button <?= (strpos(uri_string(), 'marketing') !== false) ? 'active' : '' ?>">
            <i class="fas fa-bullhorn"></i>
            <span>Marketing</span>
        </a>

        <!-- Purchasing -->
        <a href="<?= base_url('/purchasing') ?>" class="nav-button <?= (strpos(uri_string(), 'purchasing') !== false) ? 'active' : '' ?>">
            <i class="fas fa-shopping-cart"></i>
            <span>Purchasing</span>
        </a>

        <!-- Service -->
        <a href="<?= base_url('/service') ?>" class="nav-button <?= (strpos(uri_string(), 'service') !== false) ? 'active' : '' ?>">
            <i class="fas fa-wrench"></i>
            <span>Service</span>
        </a>

        <hr>

        <!-- Employees -->
        <a href="<?= base_url('/employees') ?>" class="nav-button <?= (strpos(uri_string(), 'employees') !== false) ? 'active' : '' ?>">
            <i class="fas fa-users"></i>
            <span>Employees</span>
        </a>

        <!-- Reports -->
        <a href="<?= base_url('/reports') ?>" class="nav-button <?= (strpos(uri_string(), 'reports') !== false) ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Reports</span>
        </a>

        <!-- Settings -->
        <a href="<?= base_url('/settings') ?>" class="nav-button <?= (strpos(uri_string(), 'settings') !== false) ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>

        <!-- Active Highlight -->
        <div id="nav-content-highlight"></div>
    </div>

    <!-- Sidebar Footer (User Profile) -->
    <div id="nav-footer">
        <div id="nav-footer-heading">
            <div id="nav-footer-avatar">
                <?php if (isset($userSession['avatar']) && !empty($userSession['avatar'])): ?>
                    <img src="<?= base_url('assets/images/avatars/' . $userSession['avatar']) ?>" alt="<?= esc($userName) ?>">
                <?php else: ?>
                    <?= $userInitial ?>
                <?php endif; ?>
            </div>
            <div id="nav-footer-titlebox">
                <a id="nav-footer-title" href="<?= base_url('/profile') ?>"><?= esc($userName) ?></a>
                <span id="nav-footer-subtitle"><?= esc($userRole) ?></span>
            </div>
            <label for="nav-footer-toggle">
                <i class="fas fa-caret-up"></i>
            </label>
        </div>
        <div id="nav-footer-content">
            <a href="<?= base_url('/profile') ?>">
                <i class="fas fa-user"></i> My Profile
            </a>
            <a href="<?= base_url('/settings/account') ?>">
                <i class="fas fa-cog"></i> Account Settings
            </a>
            <a href="<?= base_url('/help') ?>">
                <i class="fas fa-question-circle"></i> Help & Support
            </a>
            <hr style="border-color: rgba(255,255,255,0.1); margin: 8px 0;">
            <a href="<?= base_url('/logout') ?>" class="text-danger">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </div>

</div>

<!-- Mobile Toggle Button -->
<div class="nav-mobile-toggle" onclick="toggleMobileSidebar()">
    <i class="fas fa-bars"></i>
</div>

<!-- Mobile Overlay -->
<div class="nav-mobile-overlay" onclick="toggleMobileSidebar()"></div>

<!-- Sidebar JavaScript -->
<script>
// Modern Sidebar Functionality
(function() {
    'use strict';

    // Initialize active highlight position
    function updateHighlightPosition() {
        const activeButton = document.querySelector('.nav-button.active');
        const highlight = document.getElementById('nav-content-highlight');
        
        if (activeButton && highlight) {
            const buttons = document.querySelectorAll('.nav-button');
            let activeIndex = Array.from(buttons).indexOf(activeButton);
            
            // Adjust for hr elements (they create gaps)
            const navContent = document.getElementById('nav-content');
            const elementsBeforeActive = Array.from(navContent.children).slice(0, activeIndex + 1);
            const hrCount = elementsBeforeActive.filter(el => el.tagName === 'HR').length;
            
            highlight.style.top = `calc(${activeIndex} * 54px + 16px)`;
        }
    }

    // Mobile sidebar toggle
    window.toggleMobileSidebar = function() {
        const navBar = document.getElementById('nav-bar');
        const overlay = document.querySelector('.nav-mobile-overlay');
        
        navBar.classList.toggle('mobile-show');
        overlay.classList.toggle('show');
    };

    // Update highlight on page load
    document.addEventListener('DOMContentLoaded', function() {
        updateHighlightPosition();

        // Add hover effect for all nav buttons
        const navButtons = document.querySelectorAll('.nav-button');
        navButtons.forEach((button, index) => {
            button.addEventListener('mouseenter', function() {
                const highlight = document.getElementById('nav-content-highlight');
                const buttons = Array.from(navButtons);
                const hoverIndex = buttons.indexOf(button);
                highlight.style.top = `calc(${hoverIndex} * 54px + 16px)`;
            });
        });

        // Return highlight to active item on mouse leave
        const navContent = document.getElementById('nav-content');
        navContent.addEventListener('mouseleave', function() {
            updateHighlightPosition();
        });

        // Close mobile sidebar when clicking on nav item
        if (window.innerWidth <= 768) {
            navButtons.forEach(button => {
                button.addEventListener('click', function() {
                    setTimeout(toggleMobileSidebar, 200);
                });
            });
        }
    });

    // Update on window resize
    window.addEventListener('resize', function() {
        updateHighlightPosition();
    });

})();
</script>

<!-- Mobile Responsive Style Override -->
<style>
@media (max-width: 768px) {
    /* Ensure content doesn't overlap on mobile */
    .main-content-with-modern-sidebar {
        margin-left: 0 !important;
        padding-top: 70px;
    }
}
</style>
