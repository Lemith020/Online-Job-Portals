<?php
/**
 * JobPortal.lk - Master Unified Footer
 */
?>
<?php if (!empty($GLOBALS['layout_main_open'])) : ?>
  </main>
<?php endif; ?>
</div> <!-- /.layout / .admin-layout / .portal-layout -->

<!-- Global Center Footer -->
<footer class="site-footer" id="siteFooter">
  <p>&copy; <?php echo date('Y'); ?> <strong>JobPortal.lk</strong>. All rights reserved. Sri Lanka's Premier Job Network.</p>
</footer>

<!-- Global UI Controller Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
  // 1. Sidebar Collapse & Mobile Toggle
  const toggleBtns = [
    document.getElementById('hamburgerBtn'),
    document.getElementById('sidebarToggle')
  ].filter(Boolean);

  const sidebar = document.getElementById('sidebar') || document.getElementById('adminSidebar');

  // Restore saved collapse state on desktop
  if (localStorage.getItem('portal_sidebar_collapsed') === 'true') {
    if (window.innerWidth > 768) {
      document.body.classList.add('sidebar-collapsed');
      if (sidebar) sidebar.classList.add('toggled');
    }
  }

  toggleBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.stopPropagation();
      if (window.innerWidth <= 768) {
        document.body.classList.toggle('sidebar-open');
        if (sidebar) sidebar.classList.toggle('toggled');
      } else {
        document.body.classList.toggle('sidebar-collapsed');
        if (sidebar) sidebar.classList.toggle('toggled');
        const isCollapsed = document.body.classList.contains('sidebar-collapsed');
        localStorage.setItem('portal_sidebar_collapsed', isCollapsed);
      }
    });
  });

  // Close mobile sidebar when clicking outside
  document.addEventListener('click', function(e) {
    if (window.innerWidth <= 768 && sidebar && sidebar.classList.contains('toggled')) {
      if (!sidebar.contains(e.target) && !toggleBtns.some(b => b.contains(e.target))) {
        sidebar.classList.remove('toggled');
        document.body.classList.remove('sidebar-open');
      }
    }
  });

  // 2. Profile Dropdown Menu Toggle
  const profileMenu = document.getElementById('profileMenu');
  const profileBtn = document.getElementById('profileBtn') || document.getElementById('profileTrigger');

  if (profileBtn && profileMenu) {
    profileBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      profileMenu.classList.toggle('open');
    });

    document.addEventListener('click', function(e) {
      if (!profileMenu.contains(e.target)) {
        profileMenu.classList.remove('open');
      }
    });
  }

  // 3. Auto-dismiss alerts after 5 seconds
  const autoAlerts = document.querySelectorAll('.alert:not(.alert-permanent)');
  autoAlerts.forEach(alert => {
    setTimeout(() => {
      alert.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
      alert.style.opacity = '0';
      alert.style.transform = 'translateY(-6px)';
      setTimeout(() => alert.remove(), 400);
    }, 5000);
  });
});
</script>

</body>
</html>