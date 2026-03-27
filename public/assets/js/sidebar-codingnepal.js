/**
 * OPTIMA Sidebar - CodingNepal Style
 * Dropdown: click to open (bukan hover). Toggle sidebar via header only.
 *
 * @version 1.1.0
 * @date March 2026
 */

(function () {
  'use strict';

  const safeLocalStorage = {
    available: (() => {
      try {
        const testKey = '__optima_sidebar_storage_test__';
        window.localStorage.setItem(testKey, '1');
        window.localStorage.removeItem(testKey);
        return true;
      } catch (_) {
        return false;
      }
    })(),
    get(key) {
      if (!this.available) return null;
      try {
        return window.localStorage.getItem(key);
      } catch (_) {
        return null;
      }
    },
    set(key, value) {
      if (!this.available) return;
      try {
        window.localStorage.setItem(key, value);
      } catch (_) {}
    }
  };

  const toggleDropdown = (dropdown, menu, isOpen) => {
    dropdown.classList.remove('flyout-open-up');
    dropdown.classList.toggle('open', isOpen);
    if (menu) {
      var sidebar = document.querySelector('.sidebar');
      if (sidebar && sidebar.classList.contains('collapsed')) {
        menu.style.height = '';
        if (isOpen) {
          requestAnimationFrame(function () {
            var trigger = dropdown.querySelector('.dropdown-toggle');
            var menuRect = menu.getBoundingClientRect();
            var spaceBelow = (window.innerHeight || document.documentElement.clientHeight) - (trigger ? trigger.getBoundingClientRect().bottom : 0);
            if (menuRect.height > 0 && spaceBelow < menuRect.height + 20) {
              dropdown.classList.add('flyout-open-up');
            }
          });
        }
      } else {
        menu.style.height = isOpen ? `${menu.scrollHeight}px` : '0';
      }
    }
  };

  const closeAllDropdowns = () => {
    document.querySelectorAll('.dropdown-container.open').forEach((openDropdown) => {
      const menu = openDropdown.querySelector('.dropdown-menu');
      toggleDropdown(openDropdown, menu, false);
    });
  };

  const STORAGE_KEY = 'optima-sidebar-collapsed';

  const init = () => {
    const sidebar = document.querySelector('.sidebar');
    if (!sidebar) return;

    // Restore expand/collapse state (desktop only)
    const saved = safeLocalStorage.get(STORAGE_KEY);
    if (saved !== null) {
      if (saved === 'true') sidebar.classList.add('collapsed');
      else sidebar.classList.remove('collapsed');
    }

    // Click pada group: buka/tutup dropdown (expanded = inline, collapsed = flyout)
    document.querySelectorAll('.cn-sidebar-layout .sidebar .dropdown-toggle').forEach((dropdownToggle) => {
      dropdownToggle.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        const dropdown = dropdownToggle.closest('.dropdown-container');
        const menu = dropdown ? dropdown.querySelector('.dropdown-menu') : null;
        if (!dropdown || !menu) return;
        const isOpen = dropdown.classList.contains('open');
        closeAllDropdowns();
        toggleDropdown(dropdown, menu, !isOpen);
      });
    });

    // Tombol menu: di desktop = expand/collapse sidebar; di mobile = buka/tutup overlay
    document.querySelectorAll('.sidebar-menu-button').forEach((button) => {
      button.addEventListener('click', (e) => {
        e.stopPropagation();
        if (window.innerWidth <= 768) {
          document.body.classList.toggle('body-sidebar-mobile-open');
          closeAllDropdowns();
        } else {
          sidebar.classList.toggle('collapsed');
          safeLocalStorage.set(STORAGE_KEY, sidebar.classList.contains('collapsed'));
          closeAllDropdowns();
        }
      });
    });

    // Click outside: tutup flyout
    document.addEventListener('click', (e) => {
      if (sidebar.classList.contains('collapsed') && !sidebar.contains(e.target)) {
        closeAllDropdowns();
      }
    });
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
