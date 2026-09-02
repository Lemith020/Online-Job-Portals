  </main>
  
  <footer class="admin-footer">
    <div class="footer-left">
      &copy; <?php echo date('Y'); ?> <strong>JobPortal.lk</strong>. All rights reserved. Sri Lanka's Premier Job Network.
    </div>
    <div class="footer-right">
      <span>Version 1.0.0</span> &bull; 
      <a href="<?php echo BASE_URL; ?>/admin/settings.php">System Health</a>
    </div>
  </footer>
</div> <!-- /admin-main -->
</div> <!-- /admin-layout -->

<!-- Common Dynamic Modal Container -->
<div class="modal-backdrop" id="adminModalBackdrop">
  <div class="modal-dialog" id="adminModalDialog">
    <div class="modal-header">
      <h3 class="modal-title" id="adminModalTitle">Modal Title</h3>
      <button type="button" class="modal-close" onclick="closeAdminModal()">&times;</button>
    </div>
    <div class="modal-body" id="adminModalBody">
      <!-- Injected dynamically -->
    </div>
  </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Core Admin Scripts -->
<script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
<script src="<?php echo BASE_URL; ?>/assets/js/script.js"></script>
</body>
</html>
