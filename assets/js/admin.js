/**
 * JobPortal.lk - Admin UI Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Sidebar Toggle (Desktop Collapse & Mobile Drawer)
  const hamburgerBtn = document.getElementById('hamburgerBtn') || document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('sidebar') || document.getElementById('adminSidebar');
  const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

  if (hamburgerBtn && sidebar) {
    // Avoid double attaching if footer already bound
    hamburgerBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      if (window.innerWidth <= 768) {
        document.body.classList.toggle('sidebar-open');
        sidebar.classList.toggle('toggled');
        sidebar.classList.toggle('open');
      } else {
        document.body.classList.toggle('sidebar-collapsed');
        sidebar.classList.toggle('toggled');
        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
        localStorage.setItem('portal_sidebar_collapsed', isCollapsed);
      }
    });
  }

  if (sidebarCloseBtn && sidebar) {
    sidebarCloseBtn.addEventListener('click', () => {
      sidebar.classList.remove('open');
      sidebar.classList.remove('toggled');
      document.body.classList.remove('sidebar-open');
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (window.innerWidth <= 768 && sidebar) {
      if (document.body.classList.contains('sidebar-open') || sidebar.classList.contains('toggled') || sidebar.classList.contains('open')) {
        if (!sidebar.contains(e.target) && hamburgerBtn && !hamburgerBtn.contains(e.target)) {
          sidebar.classList.remove('open');
          sidebar.classList.remove('toggled');
          document.body.classList.remove('sidebar-open');
        }
      }
    }
  });

  // 2. Profile Dropdown Toggle
  const profileTrigger = document.getElementById('profileBtn') || document.getElementById('profileTrigger');
  const profileMenu = document.getElementById('profileMenu');
  const profileDropdown = document.getElementById('profileDropdown');

  if (profileTrigger) {
    profileTrigger.addEventListener('click', (e) => {
      e.stopPropagation();
      if (profileMenu) profileMenu.classList.toggle('open');
      if (profileDropdown) profileDropdown.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if (profileMenu && !profileMenu.contains(e.target)) {
        profileMenu.classList.remove('open');
      }
      if (profileDropdown && !profileDropdown.contains(e.target)) {
        profileDropdown.classList.remove('show');
      }
    });
  }

  // 3. Auto-dismiss flash alerts
  const alerts = document.querySelectorAll('.alert:not(.alert-permanent), #flashAlert');
  alerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-10px)';
      setTimeout(() => alert.remove(), 400);
    }, 5000);
  });

  // 4. Client-side Search Filter for Tables
  const globalSearchInput = document.getElementById('globalSearchInput');
  if (globalSearchInput) {
    globalSearchInput.addEventListener('keyup', function() {
      const filter = this.value.toLowerCase();
      const tables = document.querySelectorAll('.table tbody tr');
      tables.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(filter) ? '' : 'none';
      });
    });
  }
});

/**
 * Open Dynamic Admin Modal
 */
function openAdminModal(title, htmlContent) {
  let backdrop = document.getElementById('adminModalBackdrop');
  if (!backdrop) {
    backdrop = document.createElement('div');
    backdrop.id = 'adminModalBackdrop';
    backdrop.className = 'modal-overlay';
    backdrop.innerHTML = `
      <div class="modal-box">
        <div class="modal-header">
          <h3 id="adminModalTitle"></h3>
          <button type="button" class="modal-close" onclick="closeAdminModal()">&times;</button>
        </div>
        <div class="modal-body" id="adminModalBody"></div>
      </div>
    `;
    document.body.appendChild(backdrop);
  }

  const titleEl = document.getElementById('adminModalTitle');
  const bodyEl = document.getElementById('adminModalBody');

  if (titleEl && bodyEl) {
    titleEl.textContent = title;
    bodyEl.innerHTML = htmlContent;
    backdrop.classList.add('open');
    backdrop.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}

/**
 * Close Dynamic Admin Modal
 */
function closeAdminModal() {
  const backdrop = document.getElementById('adminModalBackdrop');
  if (backdrop) {
    backdrop.classList.remove('open');
    backdrop.classList.remove('show');
    document.body.style.overflow = '';
  }
}

// Close modal when clicking backdrop outside dialog
window.addEventListener('click', (e) => {
  const backdrop = document.getElementById('adminModalBackdrop');
  if (e.target === backdrop) {
    closeAdminModal();
  }
});

// Close modal with Escape key
window.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') {
    closeAdminModal();
  }
});

/**
 * Show Toast Notification
 */
function showToast(message, type = 'success') {
  let container = document.getElementById('toastContainer');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toastContainer';
    container.style.cssText = 'position:fixed;top:80px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:10px;';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `alert alert-${type}`;
  toast.innerHTML = `<span>${message}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
    toast.style.opacity = '0';
    toast.style.transform = 'translateY(-10px)';
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}
