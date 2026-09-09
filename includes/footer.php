<?php
/**
 * JobPortal.lk - Clean Center-Aligned Fixed Footer
 */
?>
      <!-- Main Content Closing -->
      </main>
    </div> <!-- /.layout / .admin-layout -->

    <!-- Global Fixed Center Footer -->
    <footer class="site-footer" id="siteFooter">
      <p>&copy; <?php echo date('Y'); ?> <strong>JobPortal.lk</strong>. All rights reserved. Sri Lanka's Premier Job Network.</p>
    </footer>

    <!-- Fixed Center Footer Styles & Dynamic Sidebar Sync -->
    <style>
      /* Bottom Fixed Position & Center Alignment */
      .site-footer {
        position: fixed;
        bottom: 0;
        right: 0;
        height: 48px;
        left: 240px; /* Standard Sidebar Width */
        background: #ffffff;
        border-top: 1px solid #e2e8f0;
        padding: 0 24px;
        display: flex;
        align-items: center;
        justify-content: center; /* Text එක මැදට ගැනීම */
        text-align: center;
        font-size: 13px;
        color: #64748b;
        z-index: 80;
        transition: left 0.2s ease; /* Sidebar Toggle එකට Smooth Adapt වීම */
      }

      .site-footer p {
        margin: 0;
      }

      /* Sidebar Toggled / Collapsed ඇති විට Footer එක Full-Width වීම */
      .sidebar.toggled ~ .site-footer,
      .sidebar.collapsed ~ .site-footer,
      body.sidebar-toggled .site-footer {
        left: 0 !important;
      }

      /* Content එක Footer එකට යටවීම වැළැක්වීම */
      .main-content {
        padding-bottom: 70px !important;
      }

      @media (max-width: 768px) {
        .site-footer {
          left: 0;
          padding: 0 16px;
          font-size: 12px;
        }
      }
    </style>

    <!-- Global Core Scripts -->
    <script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>

    <!-- Dynamic JS for Footer & Sidebar Sync -->
    <script>
      document.addEventListener('DOMContentLoaded', function() {
        const toggleBtn = document.getElementById('sidebarToggle');
        const sidebar = document.querySelector('.sidebar');
        const footer = document.getElementById('siteFooter');

        if (toggleBtn && sidebar && footer) {
          toggleBtn.addEventListener('click', function() {
            setTimeout(function() {
              if (sidebar.classList.contains('toggled') || sidebar.classList.contains('collapsed')) {
                footer.style.left = '0';
              } else {
                footer.style.left = '240px';
              }
            }, 10);
          });
        }
      });
    </script>

    <!-- Page Specific Script Include -->
    <?php if (isset($page_js) && !empty($page_js)) : ?>
      <script src="<?php echo BASE_URL; ?>/assets/js/<?php echo htmlspecialchars($page_js); ?>"></script>
    <?php endif; ?>

  </body>
</html>