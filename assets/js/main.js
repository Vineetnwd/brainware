/**
 * Brainware Computer Academy - Public Portal JS
 */

document.addEventListener('DOMContentLoaded', () => {
  // Mobile Nav Toggle with icon switch
  const mobileToggle = document.querySelector('.mobile-toggle');
  const navMenu = document.querySelector('.nav-menu');

  // Close all open dropdowns and submenus
  const closeAllDropdowns = () => {
    document.querySelectorAll('.nav-item-dropdown.open').forEach(el => el.classList.remove('open'));
    document.querySelectorAll('.dropdown-submenu-parent.open').forEach(el => el.classList.remove('open'));
  };

  const closeMobileMenu = () => {
    if (navMenu && navMenu.classList.contains('show')) {
      navMenu.classList.remove('show');
      closeAllDropdowns();
      if (mobileToggle) {
        const icon = mobileToggle.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-xmark');
          icon.classList.add('fa-bars');
        }
      }
    }
  };

  if (mobileToggle && navMenu) {
    mobileToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      const willOpen = !navMenu.classList.contains('show');
      navMenu.classList.toggle('show');
      const icon = mobileToggle.querySelector('i');
      if (icon) {
        if (willOpen) {
          icon.classList.remove('fa-bars');
          icon.classList.add('fa-xmark');
        } else {
          icon.classList.remove('fa-xmark');
          icon.classList.add('fa-bars');
          closeAllDropdowns();
        }
      }
    });

    // Close when clicking outside header or dropdown
    document.addEventListener('click', (e) => {
      if (!e.target.closest('.nav-item-dropdown')) {
        closeAllDropdowns();
      }
      if (!e.target.closest('.site-header')) {
        closeMobileMenu();
      }
    });

    // Close on Escape key
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') {
        closeAllDropdowns();
        closeMobileMenu();
      }
    });

    // Close menu when tapping normal links
    const directLinks = navMenu.querySelectorAll('a:not(.nav-dropdown-toggle):not(.dropdown-submenu-parent > a)');
    directLinks.forEach(link => {
      link.addEventListener('click', () => {
        if (window.innerWidth <= 1080) {
          closeMobileMenu();
        }
      });
    });
  }

  // Dropdown Accordion Toggle (both click to open and click to close)
  const dropdownToggles = document.querySelectorAll('.nav-dropdown-toggle');
  dropdownToggles.forEach(toggle => {
    toggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const parent = toggle.closest('.nav-item-dropdown');
      if (parent) {
        const isOpen = parent.classList.contains('open');
        // Close other top-level dropdowns
        document.querySelectorAll('.nav-item-dropdown.open').forEach(el => {
          if (el !== parent) {
            el.classList.remove('open');
            el.querySelectorAll('.dropdown-submenu-parent.open').forEach(sub => sub.classList.remove('open'));
          }
        });

        if (isOpen) {
          parent.classList.remove('open');
          parent.querySelectorAll('.dropdown-submenu-parent.open').forEach(sub => sub.classList.remove('open'));
        } else {
          parent.classList.add('open');
        }
      }
    });
  });

  const submenuParents = document.querySelectorAll('.dropdown-submenu-parent > a');
  submenuParents.forEach(subToggle => {
    subToggle.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      const parent = subToggle.closest('.dropdown-submenu-parent');
      if (parent) {
        const isOpen = parent.classList.contains('open');
        // Close sibling submenus in the same menu
        const container = parent.closest('.dropdown-menu');
        if (container) {
          container.querySelectorAll('.dropdown-submenu-parent.open').forEach(sub => {
            if (sub !== parent) sub.classList.remove('open');
          });
        }

        if (isOpen) {
          parent.classList.remove('open');
        } else {
          parent.classList.add('open');
        }
      }
    });
  });

  // Auto dismiss flash messages after 6 seconds
  const alerts = document.querySelectorAll('.alert-dismissible');
  alerts.forEach(alert => {
    setTimeout(() => {
      if (alert && alert.parentElement) {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.5s ease';
        setTimeout(() => alert.remove(), 500);
      }
    }, 6000);
  });
});
