<?php
/**
 * JobPortal.lk - Standard Shared HTML Footer & Modals
 * Member 1 - Admin & Core/Shared
 */
$flash = get_flash();
?>
  <!-- Global Toast Notification Container -->
  <div id="toastContainer" class="toast-container" aria-live="polite">
    <?php if ($flash): ?>
      <div class="toast toast-<?php echo htmlspecialchars($flash['type']); ?>">
        <?php echo htmlspecialchars($flash['message']); ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Admin JavaScript Logic -->
  <script src="<?php echo BASE_URL; ?>/assets/js/admin.js"></script>
</body>
</html>
