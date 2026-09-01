<?php
/**
 * JobPortal.lk - Database Connection Checker & Diagnostic Tool
 */

require_once __DIR__ . '/config/db.php';

$pdo = get_db_connection();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Connection Diagnostic - JobPortal.lk</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <style>
    body {
      background: #0f172a;
      color: #f8fafc;
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 20px;
    }
    .diag-card {
      background: #ffffff;
      color: #1e293b;
      max-width: 650px;
      width: 100%;
      border-radius: 16px;
      padding: 32px;
      box-shadow: 0 20px 25px -5px rgba(0,0,0,0.4);
    }
    .status-box {
      padding: 16px 20px;
      border-radius: 10px;
      margin: 20px 0;
      display: flex;
      align-items: center;
      gap: 14px;
      font-weight: 700;
      font-size: 16px;
    }
    .status-ok { background: #d1fae5; color: #065f46; border: 1px solid #10b981; }
    .status-err { background: #fee2e2; color: #991b1b; border: 1px solid #ef4444; }
    .info-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 14px;
      font-size: 14px;
    }
    .info-table th, .info-table td {
      padding: 10px 14px;
      border-bottom: 1px solid #e2e8f0;
      text-align: left;
    }
    .info-table th { background: #f8fafc; color: #64748b; }
  </style>
</head>
<body>
  <div class="diag-card">
    <div style="display:flex; justify-content:space-between; align-items:center;">
      <h2 style="font-size: 22px; font-weight: 800; color: #0f172a;">MySQL Connection Test</h2>
      <a href="admin/dashboard.php" class="btn btn-primary btn-sm">Go to Dashboard →</a>
    </div>

    <?php if ($pdo !== null): ?>
      <div class="status-box status-ok">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#10b981" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
          <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <div>
          <div>SUCCESS: Connected to MySQL Database!</div>
          <div style="font-size: 12.5px; font-weight: 400; color: #065f46; margin-top: 2px;">
            Active Database: <code><?php echo $pdo->query('SELECT DATABASE()')->fetchColumn(); ?></code> @ <code><?php echo DB_HOST; ?></code>
          </div>
        </div>
      </div>

      <h4 style="font-size: 15px; font-weight: 700; color: #0f172a; margin-top: 20px;">Database Table Record Counts:</h4>
      <table class="info-table">
        <thead>
          <tr>
            <th>Table Name</th>
            <th>Record Count</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $tables = ['users', 'companies', 'jobs', 'categories', 'reviews', 'settings', 'activities'];
          foreach ($tables as $t):
            try {
              $cnt = $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
              $exists = true;
            } catch (Exception $e) {
              $cnt = 'Table not found';
              $exists = false;
            }
          ?>
            <tr>
              <td><strong><?php echo $t; ?></strong></td>
              <td><?php echo $exists ? number_format($cnt) : '<span style="color:#ef4444;">Missing</span>'; ?></td>
              <td>
                <?php if ($exists): ?>
                  <span class="status-badge badge-success">OK</span>
                <?php else: ?>
                  <span class="status-badge badge-danger">Not Imported</span>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

    <?php else: ?>
      <div class="status-box status-err">
        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <line x1="12" y1="8" x2="12" y2="12"></line>
          <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div>
          <div>ERROR: Could not connect to MySQL Server!</div>
          <div style="font-size: 12.5px; font-weight: 400; color: #991b1b; margin-top: 2px;">
            The application is currently running using its built-in in-memory fallback store.
          </div>
        </div>
      </div>

      <div style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 18px; font-size: 13.5px; line-height: 1.6;">
        <h5 style="font-weight: 700; color: #0f172a; margin-bottom: 6px;">How to resolve:</h5>
        <ol style="padding-left: 20px;">
          <li>Open <strong>XAMPP Control Panel</strong> and click <strong>Start</strong> on <strong>MySQL</strong>.</li>
          <li>Open <strong>phpMyAdmin</strong> (<a href="http://localhost/phpmyadmin" target="_blank">http://localhost/phpmyadmin</a>).</li>
          <li>Import <code>database.sql</code> from your project folder.</li>
          <li>Refresh this page.</li>
        </ol>
      </div>
    <?php endif; ?>
  </div>
</body>
</html>
