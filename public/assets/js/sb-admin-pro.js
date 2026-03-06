/*!
 * OPTIMA SB Admin Pro v3.0.0 (https://sb-admin-pro.startbootstrap.com)
 * Copyright 2013-2024 Start Bootstrap
 * Licensed under MIT (https://github.com/StartBootstrap/startbootstrap-sb-admin-pro/blob/master/LICENSE)
 */

(function($) {
  "use strict"; // Start of use strict

  // Toggle the side navigation
  $("#sidebarToggle, #sidebarToggleTop").on('click', function(e) {
    e.preventDefault();
    
    if ($(window).width() > 768) {
      // Desktop behavior - collapse sidebar
      $("body").toggleClass("sidebar-toggled");
      $(".sidebar").toggleClass("toggled");
      if ($(".sidebar").hasClass("toggled")) {
        $('.sidebar .collapse').each(function() {
          if (bootstrap.Collapse) {
            const collapseInstance = bootstrap.Collapse.getOrCreateInstance(this);
            collapseInstance.hide();
          } else {
            $(this).collapse('hide');
          }
        });
      }
    } else {
      // Mobile behavior - show/hide sidebar
      $("body").toggleClass("sidebar-toggled");
      $(".sidebar").toggleClass("show");
      $(".sidebar-overlay").toggleClass("show");
    }
  });
  
  // Close sidebar when clicking overlay on mobile
  $(document).on('click', '.sidebar-overlay', function() {
    $("body").removeClass("sidebar-toggled");
    $(".sidebar").removeClass("show");
    $(".sidebar-overlay").removeClass("show");
  });

  // Close any open menu accordions when window is resized below 768px
  $(window).resize(function() {
    if ($(window).width() < 768) {
      $('.sidebar .collapse').each(function() {
        if (bootstrap.Collapse) {
          const collapseInstance = bootstrap.Collapse.getOrCreateInstance(this);
          collapseInstance.hide();
        } else {
          $(this).collapse('hide');
        }
      });
    };
    
    // Toggle the side navigation when window is resized below 480px
    if ($(window).width() < 480 && !$(".sidebar").hasClass("toggled")) {
      $("body").addClass("sidebar-toggled");
      $(".sidebar").addClass("toggled");
      $('.sidebar .collapse').each(function() {
        if (bootstrap.Collapse) {
          const collapseInstance = bootstrap.Collapse.getOrCreateInstance(this);
          collapseInstance.hide();
        } else {
          $(this).collapse('hide');
        }
      });
    };
  });

  // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
  $('body.fixed-nav .sidebar').on('mousewheel DOMMouseScroll wheel', function(e) {
    if ($(window).width() > 768) {
      var e0 = e.originalEvent,
        delta = e0.wheelDelta || -e0.detail;
      this.scrollTop += (delta < 0 ? 1 : -1) * 30;
      e.preventDefault();
    }
  });

  // Scroll to top button appear
  $(document).on('scroll', function() {
    var scrollDistance = $(this).scrollTop();
    if (scrollDistance > 100) {
      $('.scroll-to-top').fadeIn();
    } else {
      $('.scroll-to-top').fadeOut();
    }
  });

  // Smooth scrolling using jQuery easing
  $(document).on('click', 'a.scroll-to-top', function(e) {
    var $anchor = $(this);
    $('html, body').stop().animate({
      scrollTop: ($($anchor.attr('href')).offset().top)
    }, 1000, 'easeInOutExpo');
    e.preventDefault();
  });

  // Initialize Bootstrap tooltips and popovers
  if (typeof bootstrap !== 'undefined') {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
      return new bootstrap.Popover(popoverTriggerEl);
    });
  } else {
    // Fallback for jQuery tooltips/popovers
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();
  }

  // Initialize DataTables with SB Admin Pro styling
  // DISABLED: Use OptimaDataTable.init() or manual initialization instead
  // This prevents duplicate initialization and conflicts
  /*
  if ($.fn.DataTable) {
    $('.dataTable').each(function() {
      if (!$.fn.DataTable.isDataTable(this)) {
        $(this).DataTable({
          "language": {
            "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/Indonesian.json"
          },
          "pageLength": 25,
          "responsive": true,
          "searching": true,
          "ordering": true,
          "info": true,
          "autoWidth": false,
          "columnDefs": [
            {
              "targets": [-1], // Last column (usually actions)
              "orderable": false,
              "searchable": false,
              "className": "text-center"
            }
          ],
          "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>><"row"<"col-sm-12"tr>><"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
          "drawCallback": function(settings) {
            // Re-initialize tooltips after table redraw
            $('[data-toggle="tooltip"]').tooltip();
          }
        });
      }
    });
  }
  */


  // Bootstrap modal enhancements
  $('.modal').on('shown.bs.modal', function () {
    // Auto-focus first input in modal
    $(this).find('input:visible:enabled:first').focus();
  });

  // Form validation feedback
  (function() {
    'use strict';
    
    // Add custom validation styles
    var forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
      form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  })();

  // Enhanced notification system
  window.OptimaNotifications = {
    show: function(message, type = 'info', duration = 5000) {
      const colors = {
        'success': 'success',
        'error': 'danger',
        'warning': 'warning',
        'info': 'info'
      };
      
      const icons = {
        'success': 'fas fa-check-circle',
        'error': 'fas fa-times-circle',
        'warning': 'fas fa-exclamation-triangle',
        'info': 'fas fa-info-circle'
      };
      
      const toastHtml = `
        <div class="toast align-items-center text-white bg-${colors[type] || 'info'} border-0" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">
              <i class="${icons[type] || 'fas fa-info-circle'} me-2"></i>
              ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
        </div>
      `;
      
      // Create or get toast container
      let container = document.getElementById('toast-container');
      if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'toast-container position-fixed top-0 end-0 p-3';
        container.style.zIndex = '1060';
        document.body.appendChild(container);
      }
      
      // Add toast to container
      const toastElement = document.createElement('div');
      toastElement.innerHTML = toastHtml;
      const toast = toastElement.firstElementChild;
      container.appendChild(toast);
      
      // Show toast using Bootstrap 5
      if (window.bootstrap && bootstrap.Toast) {
        const bsToast = new bootstrap.Toast(toast, { delay: duration });
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
        bsToast.show();
      } else {
        // Fallback for older Bootstrap versions
        $(toast).toast({ delay: duration });
        $(toast).toast('show');
        $(toast).on('hidden.bs.toast', function() {
          $(this).remove();
        });
      }
    },
    
    success: function(message, duration = 5000) {
      this.show(message, 'success', duration);
    },
    
    error: function(message, duration = 5000) {
      this.show(message, 'error', duration);
    },
    
    warning: function(message, duration = 5000) {
      this.show(message, 'warning', duration);
    },
    
    info: function(message, duration = 5000) {
      this.show(message, 'info', duration);
    }
  };

  // AJAX setup with CSRF token
  $.ajaxSetup({
    beforeSend: function(xhr, settings) {
      if (!/^(GET|HEAD|OPTIONS|TRACE)$/i.test(settings.type) && !this.crossDomain) {
        xhr.setRequestHeader("X-CSRF-TOKEN", window.csrfToken);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
      }
    }
  });

  // Global error handler for AJAX requests
  $(document).ajaxError(function(event, xhr, settings, thrownError) {
    if (xhr.status === 403) {
      // CSRF token validation failed - reload page to get new token
      console.error('CSRF validation failed. Page will reload to get fresh token.');
      setTimeout(function() {
        window.location.reload();
      }, 2000);
    } else if (xhr.status === 419) {
      // CSRF token expired
      OptimaNotifications.error('Session expired. Please refresh the page.');
    } else if (xhr.status === 500) {
      OptimaNotifications.error('Server error occurred. Please try again.');
    } else if (xhr.status === 404) {
      OptimaNotifications.error('Resource not found.');
    }
  });

  // Enhanced loading states
  window.OptimaLoading = {
    show: function(element) {
      $(element).addClass('loading').prop('disabled', true);
    },
    
    hide: function(element) {
      $(element).removeClass('loading').prop('disabled', false);
    },
    
    button: function(button, loading = true) {
      const $btn = $(button);
      if (loading) {
        $btn.data('original-text', $btn.html());
        $btn.html('<i class="fas fa-spinner fa-spin"></i> Loading...').prop('disabled', true);
      } else {
        $btn.html($btn.data('original-text')).prop('disabled', false);
      }
    }
  };

  // Enhanced confirmation dialogs using SweetAlert2
  window.OptimaConfirm = {
    delete: function(callback, title = 'Are you sure?', text = 'This action cannot be undone.') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: title,
          text: text,
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#e81500',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, delete it!',
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed && typeof callback === 'function') {
            callback();
          }
        });
      } else {
        // Fallback to native confirm
        if (confirm(`${title}\n\n${text}`)) {
          if (typeof callback === 'function') {
            callback();
          }
        }
      }
    },
    
    action: function(callback, title = 'Confirm Action', text = 'Are you sure you want to continue?', confirmText = 'Yes') {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: title,
          text: text,
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#0061f2',
          cancelButtonColor: '#6c757d',
          confirmButtonText: confirmText,
          cancelButtonText: 'Cancel'
        }).then((result) => {
          if (result.isConfirmed && typeof callback === 'function') {
            callback();
          }
        });
      } else {
        // Fallback to native confirm
        if (confirm(`${title}\n\n${text}`)) {
          if (typeof callback === 'function') {
            callback();
          }
        }
      }
    }
  };

  // Auto-hide alerts after 5 seconds
  $('.alert').each(function() {
    const $alert = $(this);
    if (!$alert.hasClass('alert-permanent')) {
      setTimeout(function() {
        $alert.fadeOut();
      }, 5000);
    }
  });

  // Enhanced search functionality
  $('.navbar-search input').on('input', debounce(function() {
    const query = $(this).val();
    if (query.length >= 3) {
      // Implement global search functionality here
      // Searching
    }
  }, 300));

  // Debounce function for search
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func(...args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
  }

  // Initialize Flatpickr date pickers
  if (typeof flatpickr !== 'undefined') {
    flatpickr('.datepicker', {
      dateFormat: 'd/m/Y',
      locale: 'id'
    });
    
    flatpickr('.datetimepicker', {
      enableTime: true,
      dateFormat: 'd/m/Y H:i',
      locale: 'id'
    });
  }

  // Initialize Select2 if available
  if ($.fn.select2) {
    $('.select2').select2({
      theme: 'bootstrap-5',
      width: '100%'
    });
  }

  // Card collapse functionality
  $('[data-toggle="collapse-card"]').click(function() {
    const card = $(this).closest('.card');
    const cardBody = card.find('.card-body, .card-footer');
    const icon = $(this).find('i');
    
    cardBody.collapse('toggle');
    
    cardBody.on('show.bs.collapse', function() {
      icon.removeClass('fa-minus').addClass('fa-plus');
    });
    
    cardBody.on('hide.bs.collapse', function() {
      icon.removeClass('fa-plus').addClass('fa-minus');
    });
  });

  // Auto-resize textareas
  $('textarea[data-auto-resize]').each(function() {
    this.setAttribute('style', 'height:' + (this.scrollHeight) + 'px;overflow-y:hidden;');
  }).on('input', function() {
    this.style.height = 'auto';
    this.style.height = (this.scrollHeight) + 'px';
  });

  // Initialize clipboard functionality
  if (typeof ClipboardJS !== 'undefined') {
    new ClipboardJS('[data-clipboard-text]').on('success', function(e) {
      OptimaNotifications.success('Copied to clipboard!');
    });
  }

  // Print functionality
  $('[data-print]').click(function(e) {
    e.preventDefault();
    const printContent = $($(this).data('print')).clone();
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
      <!DOCTYPE html>
      <html>
      <head>
        <title>OPTIMA - Print</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="${window.location.origin}/assets/css/optima-sb-admin-pro.css" rel="stylesheet">
        <style>
          @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
          }
        </style>
      </head>
      <body onload="window.print();window.close();">
        <div class="container-fluid">
          ${printContent.html()}
        </div>
      </body>
      </html>
    `);
    
    printWindow.document.close();
  });

  // Mobile responsive table scrolling indicator
  $('.table-responsive').scroll(function() {
    const $this = $(this);
    const scrollLeft = $this.scrollLeft();
    const scrollWidth = this.scrollWidth;
    const clientWidth = this.clientWidth;
    
    if (scrollLeft > 0) {
      $this.addClass('scrolled-left');
    } else {
      $this.removeClass('scrolled-left');
    }
    
    if (scrollLeft + clientWidth < scrollWidth) {
      $this.addClass('scrolled-right');
    } else {
      $this.removeClass('scrolled-right');
    }
  });

  // Form auto-save functionality
  $('[data-autosave]').each(function() {
    const form = this;
    const key = `autosave_${$(form).data('autosave')}`;
    
    // Load saved data
    if (localStorage.getItem(key)) {
      const savedData = JSON.parse(localStorage.getItem(key));
      Object.keys(savedData).forEach(function(name) {
        const field = form.querySelector(`[name="${name}"]`);
        if (field && field.type !== 'file') {
          field.value = savedData[name];
        }
      });
    }
    
    // Save data on input
    $(form).on('input change', debounce(function() {
      const formData = {};
      $(form).serializeArray().forEach(function(field) {
        formData[field.name] = field.value;
      });
      localStorage.setItem(key, JSON.stringify(formData));
    }, 1000));
    
    // Clear saved data on successful submit
    $(form).on('submit', function() {
      localStorage.removeItem(key);
    });
  });

  // Performance monitoring
  if (typeof performance !== 'undefined') {
    $(window).on('load', function() {
      setTimeout(function() {
        const perfData = performance.getEntriesByType('navigation')[0];
        console.log('Page Load Performance:', {
          'Total Load Time': Math.round(perfData.loadEventEnd - perfData.fetchStart) + 'ms',
          'DOM Content Loaded': Math.round(perfData.domContentLoadedEventEnd - perfData.fetchStart) + 'ms',
          'Time to Interactive': Math.round(perfData.domInteractive - perfData.fetchStart) + 'ms'
        });
      }, 1000);
    });
  }

})(jQuery); // End of use strict

// Global utility functions
window.OptimaPro = {
  // Loading Animation (Optima branded)
  showLoading: function(message = 'Loading...') {
    const loadingEl = document.getElementById('pageLoading');
    if (!loadingEl) {
      console.warn('OptimaPro.showLoading: #pageLoading element not found');
      return;
    }
    
    // Update or add loading message
    let messageEl = loadingEl.querySelector('.loading-message');
    if (!messageEl) {
      messageEl = document.createElement('div');
      messageEl.className = 'loading-message';
      const contentEl = loadingEl.querySelector('.loading-content');
      if (contentEl) {
        contentEl.appendChild(messageEl);
      }
    }
    messageEl.textContent = message;
    
    // Show with fade-in animation
    loadingEl.style.display = 'flex';
    loadingEl.classList.add('active');
    
    // Smooth fade-in
    setTimeout(() => {
      loadingEl.style.opacity = '1';
    }, 10);
  },
  
  hideLoading: function() {
    const loadingEl = document.getElementById('pageLoading');
    if (!loadingEl) return;
    
    // Quick fade-out for responsiveness (reduced from 300ms to 150ms)
    loadingEl.style.opacity = '0';
    
    setTimeout(() => {
      loadingEl.style.display = 'none';
      loadingEl.classList.remove('active');
    }, 150);
  },
  
  // Legacy support for existing notification calls
  showNotification: function(message, type = 'info', duration = 5000) {
    if (window.OptimaNotifications) {
      window.OptimaNotifications.show(message, type, duration);
    } else if (window.createOptimaToast) {
      window.createOptimaToast({
        type: type === 'danger' ? 'error' : type,
        title: type.charAt(0).toUpperCase() + type.slice(1),
        message: message,
        duration: duration
      });
    }
  },
  
  // Utility functions
  formatCurrency: function(amount) {
    return new Intl.NumberFormat('id-ID', {
      style: 'currency',
      currency: 'IDR'
    }).format(amount);
  },
  
  formatNumber: function(number) {
    return new Intl.NumberFormat('id-ID').format(number);
  },
  
  formatDate: function(date, options = {}) {
    const defaultOptions = {
      year: 'numeric',
      month: 'long',
      day: 'numeric'
    };
    return new Intl.DateTimeFormat('id-ID', {...defaultOptions, ...options}).format(new Date(date));
  }
};

// Initialize performance-critical components after DOM is ready
$(document).ready(function() {
  // Preload critical images
  const criticalImages = [
    '/assets/images/logo-optima.ico',
    '/assets/images/undraw_profile.svg'
  ];
  
  criticalImages.forEach(function(src) {
    const img = new Image();
    img.src = window.baseUrl + src;
  });
  
  // Remove preload class to enable transitions
  setTimeout(function() {
    $('body').removeClass('preload');
  }, 100);
});