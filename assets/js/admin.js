/**
 * JobPortal.lk - Admin UI Interactive Scripts
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Sidebar Toggle
  const hamburgerBtn = document.getElementById('hamburgerBtn');
  const adminSidebar = document.getElementById('adminSidebar');
  const sidebarCloseBtn = document.getElementById('sidebarCloseBtn');

  if (hamburgerBtn && adminSidebar) {
    hamburgerBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      adminSidebar.classList.toggle('open');
    });
  }

  if (sidebarCloseBtn && adminSidebar) {
    sidebarCloseBtn.addEventListener('click', () => {
      adminSidebar.classList.remove('open');
    });
  }

  // Close sidebar when clicking outside on mobile
  document.addEventListener('click', (e) => {
    if (adminSidebar && adminSidebar.classList.contains('open') && !adminSidebar.contains(e.target) && e.target !== hamburgerBtn) {
      adminSidebar.classList.remove('open');
    }
  });

  // 2. Profile Dropdown Toggle
  const profileTrigger = document.getElementById('profileTrigger');
  const profileDropdown = document.getElementById('profileDropdown');

  if (profileTrigger && profileDropdown) {
    profileTrigger.addEventListener('click', (e) => {
      e.stopPropagation();
      profileDropdown.classList.toggle('show');
    });

    document.addEventListener('click', () => {
      profileDropdown.classList.remove('show');
    });
  }

  // 3. Auto-dismiss flash alerts
  const flashAlert = document.getElementById('flashAlert');
  if (flashAlert) {
    setTimeout(() => {
      flashAlert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      flashAlert.style.opacity = '0';
      flashAlert.style.transform = 'translateY(-10px)';
      setTimeout(() => flashAlert.remove(), 400);
    }, 5000);
  }

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
  const backdrop = document.getElementById('adminModalBackdrop');
  const titleEl = document.getElementById('adminModalTitle');
  const bodyEl = document.getElementById('adminModalBody');

  if (backdrop && titleEl && bodyEl) {
    titleEl.textContent = title;
    bodyEl.innerHTML = htmlContent;
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
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const toast = document.createElement('div');
  toast.className = `alert alert-${type}`;
  toast.innerHTML = `<span>${message}</span>`;
  container.appendChild(toast);

  setTimeout(() => {
    toast.remove();
  }, 3500);
}
