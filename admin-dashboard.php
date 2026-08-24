<?php
/**
 * TRACEGRAD – Super Admin Dashboard (Enhanced Analytics)
 * --------------------------------------------------------
 * Full CRUD + stunning analytics dashboard with:
 * - SVG Donut charts (no external library)
 * - Gradient bars
 * - College-specific color coding
 * - AI insights with urgency tags
 * - Interactive hover effects
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config.php';


require_once __DIR__ . '/includes/admin-dashboard/auth.php';
require_once __DIR__ . '/includes/admin-dashboard/helpers.php';

// ─── TAB HANDLING ──────────────────────────────────────
$tab = $_GET['tab'] ?? 'dashboard';
$validTabs = ['dashboard','admins','roster','colleges','gallery','orders','analytics','reports','profile','settings','logs'];
if (!in_array($tab, $validTabs, true)) $tab = 'dashboard';
$flash = null;
$galleryUploadResult = null;

// Detect requests PHP discarded because post_max_size was exceeded. In that case
// both $_POST and $_FILES can be empty, so normal CSRF/action handling never runs.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($_POST) && empty($_FILES) && (int)($_SERVER['CONTENT_LENGTH'] ?? 0) > 0) {
    $message = 'Upload request was larger than PHP post_max_size (' . ini_get('post_max_size') . '). '
        . 'Try fewer photos, or increase post_max_size in C:\xampp\php\php.ini and restart Apache.';
    $isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    if ($isAjax) {
        http_response_code(413);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'message' => $message]);
        exit;
    }
    $_SESSION['flash'] = ['error', $message];
    header('Location: admin-dashboard.php?tab=' . urlencode($tab));
    exit;
}


require_once __DIR__ . '/includes/admin-dashboard/post-handler.php';
require_once __DIR__ . '/includes/admin-dashboard/data-loader.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Super Admin Dashboard – TRACEGRAD</title>
<meta name="theme-color" content="#0a1628">
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="stylesheet" href="assets/css/admin-dashboard.css?v=<?= esc((string)$adminCssVersion) ?>">

</head>
<body>
<div class="dash-shell">

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="dash-sidebar" id="dash-sidebar">
    <div class="dash-brand">
      <div class="dash-brand-mark">
        <img src="assets/images/tracegrad-logo.png" alt="TRACEGRAD Logo">
      </div>
      <div class="lnav-brand">
        <div class="b1">TRACEGRAD</div>
        <div class="b2">Super Admin</div>
      </div>
    </div>
    <nav class="dash-nav">
      <div class="dash-nav-label">Overview</div>
      <a class="dash-link <?= $tab==='dashboard'?'on':'' ?>" href="?tab=dashboard"><i class="ti ti-layout-dashboard"></i> Dashboard</a>
      <div class="dash-nav-label">Management</div>
      <a class="dash-link <?= $tab==='admins'?'on':'' ?>" href="?tab=admins"><i class="ti ti-users"></i> Dept Admins</a>
      <a class="dash-link <?= $tab==='roster'?'on':'' ?>" href="?tab=roster"><i class="ti ti-address-book"></i> Alumni Roster</a>
      <a class="dash-link <?= $tab==='colleges'?'on':'' ?>" href="?tab=colleges"><i class="ti ti-building-bank"></i> Colleges</a>
      <a class="dash-link <?= $tab==='gallery'?'on':'' ?>" href="?tab=gallery"><i class="ti ti-photo"></i> Gallery</a>
      <a class="dash-link <?= $tab==='orders'?'on':'' ?>" href="?tab=orders"><i class="ti ti-shopping-cart"></i> Orders <?php if ($pendingOrders > 0): ?><span class="dash-badge"><?= $pendingOrders ?></span><?php endif; ?></a>
      <div class="dash-nav-label">Insights</div>
      <a class="dash-link <?= $tab==='analytics'?'on':'' ?>" href="?tab=analytics"><i class="ti ti-chart-pie-2"></i> Analytics</a>
      <a class="dash-link <?= $tab==='reports'?'on':'' ?>" href="?tab=reports"><i class="ti ti-file-analytics"></i> Reports</a>
      <a class="dash-link <?= $tab==='logs'?'on':'' ?>" href="?tab=logs"><i class="ti ti-list-details"></i> Activity Logs</a>
      <div class="dash-nav-label">Account</div>
      <a class="dash-link <?= $tab==='profile'?'on':'' ?>" href="?tab=profile"><i class="ti ti-user"></i> My Profile</a>
      <div class="dash-nav-label">System</div>
      <a class="dash-link <?= $tab==='settings'?'on':'' ?>" href="?tab=settings"><i class="ti ti-settings"></i> Settings</a>
    </nav>
  </aside>
  <button class="sidebar-backdrop" type="button" data-sidebar-backdrop aria-label="Close navigation"></button>

  <!-- ═══ MAIN ═══ -->
  <div class="dash-main">

    <!-- TOP BAR -->
    <header class="dash-topbar">
      <button class="dash-burger" type="button" data-sidebar-toggle aria-label="Open navigation"><i class="ti ti-menu-2"></i></button>
      <div class="dash-topbar-title"><?= esc(ucwords(str_replace(['dashboard','logs'], ['Overview','Activity Logs'], $tab))) ?></div>
      <div class="dash-topbar-user" data-user-menu>
        <button type="button" class="dash-user-trigger" data-user-menu-toggle aria-haspopup="true" aria-expanded="false" aria-controls="dash-user-menu">
          <div class="dash-avatar" data-avatar-url="assets/profiles/<?= esc($adminPic) ?>">
              <?php if ($adminPic === 'default.png') echo esc(strtoupper(substr($adminName,0,1))); ?>
          </div>
          <div class="dash-user-trigger-text">
            <div class="dash-user-name"><?= esc($adminName) ?></div>
            <div class="dash-user-role">Super Admin</div>
          </div>
          <i class="ti ti-chevron-down dash-user-caret"></i>
        </button>
        <div class="dash-user-menu" id="dash-user-menu" data-user-menu-panel role="menu" aria-hidden="true">
          <div class="dash-user-menu-header">
            <div class="dash-user-name"><?= esc($adminName) ?></div>
            <div class="dash-user-role">Super Admin</div>
          </div>
          <div class="dash-user-menu-sep"></div>
          <a class="dash-user-menu-item" href="?tab=profile" role="menuitem"><i class="ti ti-user"></i> My Profile</a>
          <a class="dash-user-menu-item" href="?tab=settings" role="menuitem"><i class="ti ti-settings"></i> Settings</a>
          <div class="dash-user-menu-sep"></div>
          <a class="dash-user-menu-item dash-user-menu-danger" href="admin-logout.php" role="menuitem"><i class="ti ti-logout"></i> Sign Out</a>
        </div>
      </div>
    </header>

    <div class="dash-content">

      <?php if ($flash): ?>
        <div class="dash-flash <?= $flash[0]==='ok' ? '' : 'error' ?>" data-flash>
          <i class="ti <?= $flash[0]==='ok' ? 'ti-circle-check' : 'ti-alert-triangle' ?>"></i>
          <span><?= esc($flash[1]) ?></span>
        </div>
      <?php endif; ?>


      <?php require __DIR__ . '/includes/admin-dashboard/views/dashboard-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/admins-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/roster-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/colleges-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/gallery-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/orders-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/analytics-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/profile-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/reports-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/logs-tab.php'; ?>
      <?php require __DIR__ . '/includes/admin-dashboard/views/settings-tab.php'; ?>

    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/admin-dashboard/modals.php'; ?>
<?php if ($tab === 'analytics'): ?>
<script type="application/json" id="admin-analytics-data"><?= json_encode(
    $analyticsPayload,
    JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE
) ?></script>
<?php endif; ?>
<script src="assets/js/admin-dashboard.js?v=<?= urlencode((string)@filemtime(__DIR__ . '/assets/js/admin-dashboard.js')) ?>" defer></script>
</body>
</html>