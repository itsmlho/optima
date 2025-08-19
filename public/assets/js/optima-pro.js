/*!
 * OPTIMA Bootstrap Pro JavaScript v1.0.0
 * Professional Admin Dashboard for PT Sarana Mitra Luas Tbk
 * Copyright 2024 PT Sarana Mitra Luas Tbk
 */

(function(global, factory) {
    'use strict';
    if (typeof module === 'object' && typeof module.exports === 'object') {
        module.exports = factory(global, true);
    } else {
        factory(global);
    }
})(typeof window !== 'undefined' ? window : this, function(window, noGlobal) {
    'use strict';

    // OPTIMA Pro Core Object
    const OptimaPro = {
        version: '1.0.0',
        initialized: false,
        config: {
            sidebarCollapsed: false,
            theme: 'light',
            notifications: {
                position: 'top-right',
                autoClose: 5000,
                maxVisible: 5
            },
            animations: {
                enabled: true,
                duration: 300
            }
        },
        
        // Initialize the application
        init: function() {
            // Prevent multiple initialization
            if (this.initialized) {
                return;
            }
            this.initialized = true;
            
            this.initializeSidebar();
            this.initializeTheme();
            this.initializeNotifications();
            this.initializeTooltips();
            this.initializePopovers();
            this.initializeDataTables();
            this.initializeCharts();
            this.initializeForms();
            this.initializeModals();
            this.initializeDropdowns();
            this.initializeScrollspy();
            this.initializeAnimations();
            this.initializeProgressBars();
            this.initializeCounters();
            this.initializeFileUploads();
            this.initializeDatePickers();
            this.initializeColorPickers();
            this.initializeRangeSliders();
            this.initializeSearchBoxes();
            this.initializeKeyboardShortcuts();
            this.initializeAutoSave();
            // this.initializeRealTimeUpdates();
            this.initializeDivisionThemes();
            this.bindEvents();
            console.log('OPTIMA Pro v' + this.version + ' initialized successfully');
        },

        // DOM Ready Handler
        initializeDOMReady: function() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', this.init.bind(this));
            } else {
                this.init();
            }
        },

        // Sidebar Management
        initializeSidebar: function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            const toggleBtn = document.querySelector('.sidebar-toggle');
            
            if (toggleBtn) {
                toggleBtn.addEventListener('click', this.toggleSidebar.bind(this));
            }

            // Load sidebar state from localStorage
            const savedState = localStorage.getItem('optima-sidebar-collapsed');
            if (savedState === 'true') {
                this.collapseSidebar();
            }

            // Handle responsive behavior
            this.handleResponsiveSidebar();
        },

        toggleSidebar: function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebar && mainContent) {
                this.config.sidebarCollapsed = !this.config.sidebarCollapsed;
                
                if (this.config.sidebarCollapsed) {
                    sidebar.classList.add('collapsed');
                    mainContent.classList.add('expanded');
                } else {
                    sidebar.classList.remove('collapsed');
                    mainContent.classList.remove('expanded');
                }
                
                // Save state to localStorage
                localStorage.setItem('optima-sidebar-collapsed', this.config.sidebarCollapsed);
                
                // Trigger resize event for charts
                window.dispatchEvent(new Event('resize'));
            }
        },

        collapseSidebar: function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebar && mainContent) {
                sidebar.classList.add('collapsed');
                mainContent.classList.add('expanded');
                this.config.sidebarCollapsed = true;
            }
        },

        expandSidebar: function() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('.main-content');
            
            if (sidebar && mainContent) {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('expanded');
                this.config.sidebarCollapsed = false;
            }
        },

        handleResponsiveSidebar: function() {
            const mediaQuery = window.matchMedia('(max-width: 991.98px)');
            
            const handleScreenChange = (e) => {
                const sidebar = document.querySelector('.sidebar');
                const overlay = document.querySelector('.sidebar-overlay');
                
                if (e.matches) {
                    // Mobile view
                    if (sidebar) {
                        sidebar.classList.remove('show');
                    }
                    if (overlay) {
                        overlay.remove();
                    }
                } else {
                    // Desktop view
                    if (sidebar) {
                        sidebar.classList.remove('show');
                    }
                    if (overlay) {
                        overlay.remove();
                    }
                }
            };
            
            mediaQuery.addListener(handleScreenChange);
            handleScreenChange(mediaQuery);
        },

        // Theme Management
        initializeTheme: function() {
            const savedTheme = localStorage.getItem('optima-theme') || 'light';
            this.config.theme = savedTheme; // Ensure config is set first
            
            // Don't initialize event listener here as it's handled in base.php
            // Just sync the config
            console.log('Theme initialized with:', savedTheme);
        },

        setTheme: function(theme) {
            console.log('setTheme called with:', theme);
            document.documentElement.setAttribute('data-bs-theme', theme);
            this.config.theme = theme;
            localStorage.setItem('optima-theme', theme);
            
            // Update theme toggle button
            const themeToggle = document.querySelector('.theme-toggle');
            if (themeToggle) {
                const icon = themeToggle.querySelector('i');
                if (icon) {
                    icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
                    console.log('Theme icon updated to:', icon.className);
                }
            }
        },

        toggleTheme: function() {
            console.log('toggleTheme called, current theme:', this.config.theme);
            const currentTheme = this.config.theme;
            const newTheme = currentTheme === 'light' ? 'dark' : 'light';
            console.log('Switching to theme:', newTheme);
            this.setTheme(newTheme);
        },

        // Notification System
        initializeNotifications: function() {
            this.createNotificationContainer();
        },

        createNotificationContainer: function() {
            if (!document.querySelector('.notification-container')) {
                const container = document.createElement('div');
                container.className = 'notification-container position-fixed';
                container.style.top = '20px';
                container.style.right = '20px';
                container.style.zIndex = '9999';
                document.body.appendChild(container);
            }
        },

        showNotification: function(message, type = 'info', duration = null) {
            const container = document.querySelector('.notification-container');
            const notification = document.createElement('div');
            const notificationId = 'notification-' + Date.now();
            const autoDismiss = duration !== null ? duration : this.config.notifications.autoClose;
            
            notification.className = `alert alert-${type} alert-dismissible fade show animate-fadeIn`;
            notification.id = notificationId;
            notification.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fas fa-${this.getNotificationIcon(type)} me-2"></i>
                    <div class="flex-grow-1">${message}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            container.appendChild(notification);
            
            // Auto dismiss
            if (autoDismiss > 0) {
                setTimeout(() => {
                    this.dismissNotification(notificationId);
                }, autoDismiss);
            }
            
            // Limit number of visible notifications
            this.limitVisibleNotifications();
        },

        getNotificationIcon: function(type) {
            const icons = {
                success: 'check-circle',
                info: 'info-circle',
                warning: 'exclamation-triangle',
                danger: 'exclamation-circle',
                primary: 'bell'
            };
            return icons[type] || 'bell';
        },

        dismissNotification: function(notificationId) {
            const notification = document.getElementById(notificationId);
            if (notification) {
                notification.classList.add('fade');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }
        },

        limitVisibleNotifications: function() {
            const notifications = document.querySelectorAll('.notification-container .alert');
            const maxVisible = this.config.notifications.maxVisible;
            
            if (notifications.length > maxVisible) {
                for (let i = 0; i < notifications.length - maxVisible; i++) {
                    notifications[i].remove();
                }
            }
        },

        // Tooltip and Popover initialization
        initializeTooltips: function() {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        },

        initializePopovers: function() {
            const popoverTriggerList = document.querySelectorAll('[data-bs-toggle="popover"]');
            const popoverList = [...popoverTriggerList].map(popoverTriggerEl => {
                return new bootstrap.Popover(popoverTriggerEl);
            });
        },

        // DataTables initialization
        initializeDataTables: function() {
            const tables = document.querySelectorAll('.data-table');
            tables.forEach(table => {
                if (typeof DataTable !== 'undefined') {
                    new DataTable(table, {
                        responsive: true,
                        pageLength: 25,
                        lengthMenu: [10, 25, 50, 100],
                        language: {
                            search: 'Pencarian:',
                            lengthMenu: 'Tampilkan _MENU_ entri',
                            info: 'Menampilkan _START_ sampai _END_ dari _TOTAL_ entri',
                            infoEmpty: 'Menampilkan 0 sampai 0 dari 0 entri',
                            infoFiltered: '(disaring dari _MAX_ total entri)',
                            paginate: {
                                first: 'Pertama',
                                last: 'Terakhir',
                                next: 'Selanjutnya',
                                previous: 'Sebelumnya'
                            },
                            emptyTable: 'Tidak ada data yang tersedia dalam tabel',
                            zeroRecords: 'Tidak ditemukan catatan yang cocok'
                        },
                        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                             '<"row"<"col-sm-12"tr>>' +
                             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
                    });
                }
            });
        },

        // Charts initialization
        initializeCharts: function() {
            this.initializeLineCharts();
            this.initializeBarCharts();
            this.initializePieCharts();
            this.initializeAreaCharts();
            this.initializeDoughnutCharts();
        },

        initializeLineCharts: function() {
            const lineCharts = document.querySelectorAll('.line-chart');
            lineCharts.forEach(chart => {
                if (typeof Chart !== 'undefined') {
                    const ctx = chart.getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Data',
                                data: [],
                                borderColor: 'rgb(0, 97, 242)',
                                backgroundColor: 'rgba(0, 97, 242, 0.1)',
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        },

        initializeBarCharts: function() {
            const barCharts = document.querySelectorAll('.bar-chart');
            barCharts.forEach(chart => {
                if (typeof Chart !== 'undefined') {
                    const ctx = chart.getContext('2d');
                    new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Data',
                                data: [],
                                backgroundColor: 'rgba(0, 97, 242, 0.8)',
                                borderColor: 'rgb(0, 97, 242)',
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        },

        initializePieCharts: function() {
            const pieCharts = document.querySelectorAll('.pie-chart');
            pieCharts.forEach(chart => {
                if (typeof Chart !== 'undefined') {
                    const ctx = chart.getContext('2d');
                    new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: [],
                            datasets: [{
                                data: [],
                                backgroundColor: [
                                    'rgba(0, 97, 242, 0.8)',
                                    'rgba(0, 172, 105, 0.8)',
                                    'rgba(255, 182, 7, 0.8)',
                                    'rgba(232, 21, 0, 0.8)',
                                    'rgba(57, 175, 209, 0.8)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            });
        },

        initializeAreaCharts: function() {
            const areaCharts = document.querySelectorAll('.area-chart');
            areaCharts.forEach(chart => {
                if (typeof Chart !== 'undefined') {
                    const ctx = chart.getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: [],
                            datasets: [{
                                label: 'Data',
                                data: [],
                                borderColor: 'rgb(0, 97, 242)',
                                backgroundColor: 'rgba(0, 97, 242, 0.3)',
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true
                                }
                            }
                        }
                    });
                }
            });
        },

        initializeDoughnutCharts: function() {
            const doughnutCharts = document.querySelectorAll('.doughnut-chart');
            doughnutCharts.forEach(chart => {
                if (typeof Chart !== 'undefined') {
                    const ctx = chart.getContext('2d');
                    new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: [],
                            datasets: [{
                                data: [],
                                backgroundColor: [
                                    'rgba(0, 97, 242, 0.8)',
                                    'rgba(0, 172, 105, 0.8)',
                                    'rgba(255, 182, 7, 0.8)',
                                    'rgba(232, 21, 0, 0.8)',
                                    'rgba(57, 175, 209, 0.8)'
                                ]
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'bottom'
                                }
                            }
                        }
                    });
                }
            });
        },

        // Form enhancements
        initializeForms: function() {
            this.initializeFormValidation();
            this.initializeFormAutoSave();
            this.initializeFormSubmission();
        },

        initializeFormValidation: function() {
            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(form => {
                form.addEventListener('submit', (event) => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        },

        initializeFormAutoSave: function() {
            const autoSaveForms = document.querySelectorAll('[data-autosave]');
            autoSaveForms.forEach(form => {
                const inputs = form.querySelectorAll('input, textarea, select');
                inputs.forEach(input => {
                    input.addEventListener('input', this.debounce(() => {
                        this.autoSaveForm(form);
                    }, 1000));
                });
            });
        },

        autoSaveForm: function(form) {
            const formData = new FormData(form);
            const data = {};
            for (let [key, value] of formData.entries()) {
                data[key] = value;
            }
            
            const formId = form.id || 'form-' + Date.now();
            localStorage.setItem('autosave-' + formId, JSON.stringify(data));
            
            this.showNotification('Form tersimpan otomatis', 'info', 2000);
        },

        loadAutoSavedForm: function(formId) {
            const savedData = localStorage.getItem('autosave-' + formId);
            if (savedData) {
                const data = JSON.parse(savedData);
                const form = document.getElementById(formId);
                if (form) {
                    Object.keys(data).forEach(key => {
                        const input = form.querySelector(`[name="${key}"]`);
                        if (input) {
                            input.value = data[key];
                        }
                    });
                }
            }
        },

        initializeFormSubmission: function() {
            const ajaxForms = document.querySelectorAll('.ajax-form');
            ajaxForms.forEach(form => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                    this.submitFormAjax(form);
                });
            });
        },

        submitFormAjax: function(form) {
            const formData = new FormData(form);
            const url = form.action || window.location.href;
            const method = form.method || 'POST';
            
            // Show loading state
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
            }
            
            fetch(url, {
                method: method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.showNotification(data.message || 'Berhasil!', 'success');
                    if (data.redirect) {
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    }
                } else {
                    this.showNotification(data.message || 'Terjadi kesalahan!', 'danger');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.showNotification('Terjadi kesalahan jaringan!', 'danger');
            })
            .finally(() => {
                // Reset submit button
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = submitBtn.dataset.originalText || 'Kirim';
                }
            });
        },

        // Modal enhancements
        initializeModals: function() {
            const modals = document.querySelectorAll('.modal');
            modals.forEach(modal => {
                modal.addEventListener('show.bs.modal', (event) => {
                    this.onModalShow(event);
                });
                
                modal.addEventListener('hidden.bs.modal', (event) => {
                    this.onModalHide(event);
                });
            });
        },

        onModalShow: function(event) {
            const modal = event.target;
            const body = modal.querySelector('.modal-body');
            
            // Load content via AJAX if data-remote is present
            if (modal.dataset.remote) {
                this.loadModalContent(modal, modal.dataset.remote);
            }
            
            // Auto-focus first input
            setTimeout(() => {
                const firstInput = modal.querySelector('input:not([type="hidden"]), textarea, select');
                if (firstInput) {
                    firstInput.focus();
                }
            }, 300);
        },

        onModalHide: function(event) {
            const modal = event.target;
            const form = modal.querySelector('form');
            
            // Reset form if present
            if (form) {
                form.reset();
                form.classList.remove('was-validated');
            }
        },

        loadModalContent: function(modal, url) {
            const body = modal.querySelector('.modal-body');
            if (body) {
                body.innerHTML = '<div class="text-center"><div class="loading-spinner"></div></div>';
                
                fetch(url)
                .then(response => response.text())
                .then(html => {
                    body.innerHTML = html;
                    this.initializeFormValidation();
                })
                .catch(error => {
                    console.error('Error loading modal content:', error);
                    body.innerHTML = '<div class="alert alert-danger">Error loading content</div>';
                });
            }
        },

        // Dropdown enhancements
        initializeDropdowns: function() {
            // Let Bootstrap handle all dropdowns automatically
            // We don't need to manually initialize them
            console.log('Bootstrap dropdowns will be initialized automatically');
        },

        // Scrollspy initialization
        initializeScrollspy: function() {
            const scrollspyElements = document.querySelectorAll('[data-bs-spy="scroll"]');
            scrollspyElements.forEach(element => {
                new bootstrap.ScrollSpy(element);
            });
        },

        // Animation handling
        initializeAnimations: function() {
            if (this.config.animations.enabled) {
                this.observeAnimations();
            }
        },

        observeAnimations: function() {
            const animatedElements = document.querySelectorAll('[data-animate]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const element = entry.target;
                        const animation = element.dataset.animate;
                        element.classList.add('animate-' + animation);
                        observer.unobserve(element);
                    }
                });
            });
            
            animatedElements.forEach(element => {
                observer.observe(element);
            });
        },

        // Progress bars
        initializeProgressBars: function() {
            const progressBars = document.querySelectorAll('.progress-bar[data-progress]');
            progressBars.forEach(bar => {
                const progress = bar.dataset.progress;
                setTimeout(() => {
                    bar.style.width = progress + '%';
                }, 100);
            });
        },

        // Counters
        initializeCounters: function() {
            const counters = document.querySelectorAll('.counter');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const counter = entry.target;
                        this.animateCounter(counter);
                        observer.unobserve(counter);
                    }
                });
            });
            
            counters.forEach(counter => {
                observer.observe(counter);
            });
        },

        animateCounter: function(counter) {
            const target = parseInt(counter.dataset.target);
            const duration = parseInt(counter.dataset.duration) || 2000;
            const step = target / (duration / 16);
            let current = 0;
            
            const timer = setInterval(() => {
                current += step;
                if (current >= target) {
                    counter.textContent = target;
                    clearInterval(timer);
                } else {
                    counter.textContent = Math.floor(current);
                }
            }, 16);
        },

        // File upload enhancements
        initializeFileUploads: function() {
            const fileInputs = document.querySelectorAll('.file-upload');
            fileInputs.forEach(input => {
                input.addEventListener('change', (event) => {
                    this.handleFileUpload(event);
                });
            });
        },

        handleFileUpload: function(event) {
            const input = event.target;
            const files = input.files;
            const preview = input.parentElement.querySelector('.file-preview');
            
            if (preview && files.length > 0) {
                Array.from(files).forEach(file => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        if (file.type.startsWith('image/')) {
                            const img = document.createElement('img');
                            img.src = e.target.result;
                            img.className = 'img-thumbnail';
                            img.style.maxWidth = '150px';
                            preview.appendChild(img);
                        } else {
                            const div = document.createElement('div');
                            div.textContent = file.name;
                            div.className = 'file-name';
                            preview.appendChild(div);
                        }
                    };
                    reader.readAsDataURL(file);
                });
            }
        },

        // Date picker initialization
        initializeDatePickers: function() {
            const datePickers = document.querySelectorAll('.date-picker');
            datePickers.forEach(picker => {
                if (typeof flatpickr !== 'undefined') {
                    flatpickr(picker, {
                        dateFormat: 'Y-m-d',
                        locale: 'id'
                    });
                }
            });
        },

        // Color picker initialization
        initializeColorPickers: function() {
            const colorPickers = document.querySelectorAll('.color-picker');
            colorPickers.forEach(picker => {
                if (typeof Pickr !== 'undefined') {
                    const pickr = Pickr.create({
                        el: picker,
                        theme: 'nano',
                        default: picker.value || '#0061f2',
                        components: {
                            preview: true,
                            opacity: true,
                            hue: true,
                            interaction: {
                                hex: true,
                                rgba: true,
                                hsla: true,
                                hsva: true,
                                cmyk: true,
                                input: true,
                                clear: true,
                                save: true
                            }
                        }
                    });
                    
                    pickr.on('change', (color) => {
                        picker.value = color.toHEXA().toString();
                    });
                }
            });
        },

        // Range slider initialization
        initializeRangeSliders: function() {
            const rangeSliders = document.querySelectorAll('.range-slider');
            rangeSliders.forEach(slider => {
                if (typeof noUiSlider !== 'undefined') {
                    noUiSlider.create(slider, {
                        start: [slider.dataset.min || 0, slider.dataset.max || 100],
                        connect: true,
                        range: {
                            'min': parseInt(slider.dataset.min) || 0,
                            'max': parseInt(slider.dataset.max) || 100
                        }
                    });
                }
            });
        },

        // Search box enhancements
        initializeSearchBoxes: function() {
            const searchBoxes = document.querySelectorAll('.search-box');
            searchBoxes.forEach(searchBox => {
                searchBox.addEventListener('input', this.debounce((event) => {
                    this.handleSearch(event);
                }, 300));
            });
        },

        handleSearch: function(event) {
            const searchBox = event.target;
            const query = searchBox.value.trim();
            const target = searchBox.dataset.target;
            
            if (target && query.length > 2) {
                const elements = document.querySelectorAll(target);
                elements.forEach(element => {
                    const text = element.textContent.toLowerCase();
                    const isMatch = text.includes(query.toLowerCase());
                    element.style.display = isMatch ? '' : 'none';
                });
            }
        },

        // Keyboard shortcuts
        initializeKeyboardShortcuts: function() {
            document.addEventListener('keydown', (event) => {
                // Ctrl + / - Toggle sidebar
                if (event.ctrlKey && event.key === '/') {
                    event.preventDefault();
                    this.toggleSidebar();
                }
                
                // Ctrl + Shift + T - Toggle theme
                if (event.ctrlKey && event.shiftKey && event.key === 'T') {
                    event.preventDefault();
                    this.toggleTheme();
                }
                
                // Escape - Close modals
                if (event.key === 'Escape') {
                    const openModal = document.querySelector('.modal.show');
                    if (openModal) {
                        const modal = bootstrap.Modal.getInstance(openModal);
                        if (modal) {
                            modal.hide();
                        }
                    }
                }
            });
        },

        // Auto-save functionality
        initializeAutoSave: function() {
            // Auto-save is handled in form initialization
        },

        // Real-time updates
        initializeRealTimeUpdates: function() {
            // Check for real-time update elements
            const realTimeElements = document.querySelectorAll('[data-realtime]');
            if (realTimeElements.length > 0) {
                this.startRealTimeUpdates();
            }
        },

        startRealTimeUpdates: function() {
            setInterval(() => {
                this.fetchRealTimeData();
            }, 30000); // Update every 30 seconds
        },

        fetchRealTimeData: function() {
            fetch('/api/realtime-data')
            .then(response => response.json())
            .then(data => {
                this.updateRealTimeElements(data);
            })
            .catch(error => {
                console.error('Error fetching real-time data:', error);
            });
        },

        updateRealTimeElements: function(data) {
            const elements = document.querySelectorAll('[data-realtime]');
            elements.forEach(element => {
                const key = element.dataset.realtime;
                if (data[key] !== undefined) {
                    element.textContent = data[key];
                }
            });
        },

        // Event binding
        bindEvents: function() {
            // Global click handler
            document.addEventListener('click', (event) => {
                this.handleGlobalClick(event);
            });
            
            // Window resize handler
            window.addEventListener('resize', this.debounce(() => {
                this.handleResize();
            }, 250));
            
            // Before unload handler
            window.addEventListener('beforeunload', (event) => {
                this.handleBeforeUnload(event);
            });
        },

        handleGlobalClick: function(event) {
            const target = event.target;
            
            // Let Bootstrap handle dropdown closing automatically
            // Removed manual dropdown close code to prevent conflicts
            
            // Handle sidebar toggle on mobile
            if (target.closest('.sidebar-toggle')) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.toggle('show');
                    this.toggleSidebarOverlay();
                }
            }
            
            // Handle sidebar overlay click
            if (target.classList.contains('sidebar-overlay')) {
                const sidebar = document.querySelector('.sidebar');
                if (sidebar) {
                    sidebar.classList.remove('show');
                    target.remove();
                }
            }
        },

        toggleSidebarOverlay: function() {
            const existingOverlay = document.querySelector('.sidebar-overlay');
            const sidebar = document.querySelector('.sidebar');
            
            if (sidebar && sidebar.classList.contains('show')) {
                if (!existingOverlay) {
                    const overlay = document.createElement('div');
                    overlay.className = 'sidebar-overlay position-fixed w-100 h-100';
                    overlay.style.top = '0';
                    overlay.style.left = '0';
                    overlay.style.backgroundColor = 'rgba(0, 0, 0, 0.5)';
                    overlay.style.zIndex = '999';
                    document.body.appendChild(overlay);
                }
            } else if (existingOverlay) {
                existingOverlay.remove();
            }
        },

        handleResize: function() {
            // Handle responsive behavior
            this.handleResponsiveSidebar();
            
            // Trigger chart resize - Fixed for Chart.js v3+
            if (typeof Chart !== 'undefined') {
                // Chart.instances doesn't exist in Chart.js v3+
                // Instead, we need to manually track charts or let them auto-resize
                try {
                    // For Chart.js v3+, charts auto-resize on window resize
                    // We can dispatch a custom resize event if needed
                    window.dispatchEvent(new Event('resize'));
                } catch (error) {
                    console.warn('Chart resize handling skipped:', error.message);
                }
            }
        },

        handleBeforeUnload: function(event) {
            // Check for unsaved changes
            const unsavedForms = document.querySelectorAll('form.dirty');
            if (unsavedForms.length > 0) {
                event.preventDefault();
                event.returnValue = 'Ada perubahan yang belum disimpan. Yakin ingin keluar?';
                return event.returnValue;
            }
        },

        // Utility functions
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        throttle: function(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        },

        // API methods
        api: {
            get: function(url) {
                return fetch(url, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => response.json());
            },
            
            post: function(url, data) {
                return fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                }).then(response => response.json());
            },
            
            put: function(url, data) {
                return fetch(url, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                }).then(response => response.json());
            },
            
            delete: function(url) {
                return fetch(url, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(response => response.json());
            }
        },

        // Public methods for external use
        showLoading: function(message = 'Loading...') {
            const loadingOverlay = document.createElement('div');
            loadingOverlay.className = 'loading-overlay';
            loadingOverlay.innerHTML = `
                <div class="text-center">
                    <div class="loading-spinner mb-3"></div>
                    <div class="fw-semibold">${message}</div>
                </div>
            `;
            document.body.appendChild(loadingOverlay);
        },

        hideLoading: function() {
            const loadingOverlay = document.querySelector('.loading-overlay');
            if (loadingOverlay) {
                loadingOverlay.remove();
            }
        },

        confirmDialog: function(message, callback) {
            if (confirm(message)) {
                callback();
            }
        },

        // Reload page with optional delay
        reload: function(delay = 0) {
            setTimeout(() => {
                window.location.reload();
            }, delay);
        },

        // Redirect to URL
        redirect: function(url, delay = 0) {
            setTimeout(() => {
                window.location.href = url;
            }, delay);
        },

        // Initialize division-specific themes
        initializeDivisionThemes: function() {
            const contentHeader = document.querySelector('.content-header');
            if (!contentHeader) return;

            // Get current path
            const currentPath = window.location.pathname.toLowerCase();
            
            // Remove any existing theme classes
            const themeClasses = [
                'admin-header', 'user-management-header', 'forklift-header', 'equipment-header',
                'marketing-header', 'service-header', 'finance-header', 'financial-header',
                'reports-header', 'warehouse-header', 'system-header', 'settings-header',
                'dashboard-header', 'rental-header', 'customers-header'
            ];
            
            themeClasses.forEach(className => {
                contentHeader.classList.remove(className);
            });
            
            // Apply theme based on current path
            if (currentPath.includes('/admin') || currentPath.includes('/user')) {
                contentHeader.classList.add('admin-header', 'themed');
            } else if (currentPath.includes('/forklift') || currentPath.includes('/equipment')) {
                contentHeader.classList.add('forklift-header', 'themed');
            } else if (currentPath.includes('/marketing')) {
                contentHeader.classList.add('marketing-header', 'themed');
            } else if (currentPath.includes('/service')) {
                contentHeader.classList.add('service-header', 'themed');
            } else if (currentPath.includes('/finance')) {
                contentHeader.classList.add('finance-header', 'themed');
            } else if (currentPath.includes('/report')) {
                contentHeader.classList.add('reports-header', 'themed');
            } else if (currentPath.includes('/warehouse')) {
                contentHeader.classList.add('warehouse-header', 'themed');
            } else if (currentPath.includes('/system') || currentPath.includes('/setting') || currentPath.includes('/profile')) {
                contentHeader.classList.add('system-header', 'themed');
            } else if (currentPath.includes('/rental')) {
                contentHeader.classList.add('rental-header', 'themed');
            } else if (currentPath.includes('/customer')) {
                contentHeader.classList.add('customers-header', 'themed');
            } else if (currentPath.includes('/dashboard') || currentPath === '/' || currentPath === '') {
                contentHeader.classList.add('dashboard-header', 'themed');
            }
            
            console.log('Division theme applied for path:', currentPath);
        }
    };

    // Initialize on DOM ready
    OptimaPro.initializeDOMReady();

    // Export to global scope
    if (typeof window !== 'undefined') {
        window.OptimaPro = OptimaPro;
    }

    return OptimaPro;
});

// Additional helper functions for common operations
const OptimaHelpers = {
    // Format currency
    formatCurrency: function(amount, currency = 'IDR') {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: currency,
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        }).format(amount);
    },

    // Format date
    formatDate: function(date, format = 'dd/MM/yyyy') {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        return new Intl.DateTimeFormat('id-ID', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(date);
    },

    // Format time
    formatTime: function(date, format = 'HH:mm') {
        if (typeof date === 'string') {
            date = new Date(date);
        }
        return new Intl.DateTimeFormat('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        }).format(date);
    },

    // Format number
    formatNumber: function(number, decimals = 0) {
        return new Intl.NumberFormat('id-ID', {
            minimumFractionDigits: decimals,
            maximumFractionDigits: decimals
        }).format(number);
    },

    // Validate email
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    // Validate phone
    validatePhone: function(phone) {
        const re = /^(\+62|62|0)8[1-9][0-9]{6,9}$/;
        return re.test(phone);
    },

    // Generate random ID
    generateId: function(length = 8) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        return result;
    },

    // Copy to clipboard
    copyToClipboard: function(text) {
        return navigator.clipboard.writeText(text).then(() => {
            OptimaPro.showNotification('Berhasil disalin ke clipboard!', 'success', 2000);
        }).catch(err => {
            console.error('Failed to copy to clipboard:', err);
            OptimaPro.showNotification('Gagal menyalin ke clipboard!', 'danger', 2000);
        });
    },

    // Download file
    downloadFile: function(url, filename) {
        const a = document.createElement('a');
        a.href = url;
        a.download = filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    },

    // Print element
    printElement: function(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Print</title>
                        <link href="/assets/css/bootstrap.min.css" rel="stylesheet">
                        <link href="/assets/css/optima-pro.css" rel="stylesheet">
                    </head>
                    <body>
                        ${element.outerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    }
};

// Export helpers to global scope
if (typeof window !== 'undefined') {
    window.OptimaHelpers = OptimaHelpers;
}

/**
 * Division Theme Detection and Application
 * Automatically applies division-based themes based on current URL
 */
function initializeDivisionTheme() {
    const currentPath = window.location.pathname.toLowerCase();
    const body = document.body;
    
    // Remove any existing division attributes
    body.removeAttribute('data-division');
    body.classList.remove('service-theme', 'admin-theme', 'marketing-theme', 'finance-theme', 'warehouse-theme', 'dashboard-theme', 'reports-theme', 'rental-theme', 'customers-theme', 'system-theme');
    
    // Detect division based on URL path
    if (currentPath.includes('/service/') || currentPath.includes('/service')) {
        body.setAttribute('data-division', 'service');
        body.classList.add('service-theme');
        console.log('Applied Service theme');
    } else if (currentPath.includes('/admin/') || currentPath.includes('/user/')) {
        body.setAttribute('data-division', 'admin');
        body.classList.add('admin-theme');
        console.log('Applied Admin theme');
    } else if (currentPath.includes('/marketing/')) {
        body.setAttribute('data-division', 'marketing');
        body.classList.add('marketing-theme');
        console.log('Applied Marketing theme');
    } else if (currentPath.includes('/finance/') || currentPath.includes('/financial/')) {
        body.setAttribute('data-division', 'finance');
        body.classList.add('finance-theme');
        console.log('Applied Finance theme');
    } else if (currentPath.includes('/warehouse/')) {
        body.setAttribute('data-division', 'warehouse');
        body.classList.add('warehouse-theme');
        console.log('Applied Warehouse theme');
    } else if (currentPath.includes('/dashboard/')) {
        body.setAttribute('data-division', 'dashboard');
        body.classList.add('dashboard-theme');
        console.log('Applied Dashboard theme');
    } else if (currentPath.includes('/reports/')) {
        body.setAttribute('data-division', 'reports');
        body.classList.add('reports-theme');
        console.log('Applied Reports theme');
    } else if (currentPath.includes('/rental/')) {
        body.setAttribute('data-division', 'rental');
        body.classList.add('rental-theme');
        console.log('Applied Rental theme');
    } else if (currentPath.includes('/customers/')) {
        body.setAttribute('data-division', 'customers');
        body.classList.add('customers-theme');
        console.log('Applied Customers theme');
    } else if (currentPath.includes('/system/') || currentPath.includes('/settings/')) {
        body.setAttribute('data-division', 'system');
        body.classList.add('system-theme');
        console.log('Applied System theme');
    }
    
    // Add visual feedback for theme changes
    const contentHeader = document.querySelector('.content-header');
    if (contentHeader && body.getAttribute('data-division')) {
        contentHeader.style.transition = 'all 0.3s ease-in-out';
        
        // Trigger a small animation to show theme is applied
        setTimeout(() => {
            contentHeader.style.transform = 'scale(1.02)';
            setTimeout(() => {
                contentHeader.style.transform = 'scale(1)';
            }, 150);
        }, 100);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeDivisionTheme();
});

// Re-initialize when navigation changes (for SPAs)
window.addEventListener('popstate', function() {
    initializeDivisionTheme();
});

// Override existing initialization if it exists
if (typeof window.initializeApp === 'function') {
    const originalInit = window.initializeApp;
    window.initializeApp = function() {
        originalInit();
        initializeDivisionTheme();
    };
} else {
    window.initializeApp = initializeDivisionTheme;
} 