<?php
/*
 * TRACEGRAD - Alumni Dashboard bootstrap
 * ---------------------------------------------------------
 * IMPORTANT:
 * Load helpers BEFORE data-fetch.php because data-fetch.php
 * calls pdoTableExists() / pdoColumnExists().
 * PHP 7.2 compatible.
 */
require_once __DIR__ . '/includes/alumni-dashboard/auth.php';
require_once __DIR__ . '/includes/alumni-dashboard/helpers.php';

/*
 * Compatibility fallbacks
 * ---------------------------------------------------------
 * These are only declared when an older helpers.php does not
 * yet contain the schema-check helper functions.
 */
if (!function_exists('pdoTableExists')) {
    function pdoTableExists($pdo, $table)
    {
        static $cache = [];

        $table = (string)$table;

        if (array_key_exists($table, $cache)) {
            return $cache[$table];
        }

        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?"
            );
            $stmt->execute([$table]);

            $cache[$table] = ((int)$stmt->fetchColumn()) > 0;
        } catch (Throwable $e) {
            $cache[$table] = false;
        }

        return $cache[$table];
    }
}

if (!function_exists('pdoColumnExists')) {
    function pdoColumnExists($pdo, $table, $column)
    {
        static $cache = [];

        $key = (string)$table . '.' . (string)$column;

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        try {
            $stmt = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM information_schema.COLUMNS
                 WHERE TABLE_SCHEMA = DATABASE()
                   AND TABLE_NAME = ?
                   AND COLUMN_NAME = ?"
            );
            $stmt->execute([(string)$table, (string)$column]);

            $cache[$key] = ((int)$stmt->fetchColumn()) > 0;
        } catch (Throwable $e) {
            $cache[$key] = false;
        }

        return $cache[$key];
    }
}

require_once __DIR__ . '/includes/alumni-dashboard/data-fetch.php';

/*
 * Dedicated profile editor handles action=update_profile first.
 * For every other action it simply returns, so the existing
 * Alumni post-handler remains untouched.
 */
require_once __DIR__ . '/includes/alumni-dashboard/profile-update-handler.php';

require_once __DIR__ . '/includes/alumni-dashboard/post-handler.php';
require_once __DIR__ . '/includes/alumni-dashboard/data-loader.php';
/*
 * Alumni Dashboard asset discovery.
 * Primary organized paths:
 *   assets/css/alumni-dashboard.css
 *   assets/js/alumni-dashboard.js
 *
 * Root-level files remain supported as a fallback so older
 * TRACEGRAD copies do not immediately break.
 */
$alumniCssPath = '';
$alumniCssHref = 'assets/css/alumni-dashboard.css';

$alumniCssCandidates = [
    [__DIR__ . '/assets/css/alumni-dashboard.css', 'assets/css/alumni-dashboard.css'],
    [__DIR__ . '/alumni-dashboard.css', 'alumni-dashboard.css'],
];

foreach ($alumniCssCandidates as $candidate) {
    if (is_file($candidate[0])) {
        $alumniCssPath = $candidate[0];
        $alumniCssHref = $candidate[1];
        break;
    }
}

$alumniJsPath = '';
$alumniJsHref = 'assets/js/alumni-dashboard.js';

$alumniJsCandidates = [
    [__DIR__ . '/assets/js/alumni-dashboard.js', 'assets/js/alumni-dashboard.js'],
    [__DIR__ . '/alumni-dashboard.js', 'alumni-dashboard.js'],
];

foreach ($alumniJsCandidates as $candidate) {
    if (is_file($candidate[0])) {
        $alumniJsPath = $candidate[0];
        $alumniJsHref = $candidate[1];
        break;
    }
}

$alumniCssVersion = $alumniCssPath !== ''
    ? (@filemtime($alumniCssPath) ?: '2')
    : '2';

$alumniJsVersion = $alumniJsPath !== ''
    ? (@filemtime($alumniJsPath) ?: '2')
    : '2';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Alumni Dashboard – TRACEGRAD</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="stylesheet" href="<?= esc($alumniCssHref) ?>?v=<?= esc((string)$alumniCssVersion) ?>">

<style>
/* =========================================================
   TRACEGRAD — ALUMNI TOP-RIGHT ACCOUNT MENU
   Matches the Department Admin account dropdown behavior.
   ========================================================= */

.dash-alumni-account {
  position:relative;
  z-index:80;
}

.dash-alumni-account-button {
  display:flex;
  align-items:center;
  gap:8px;
  padding:4px 7px 4px 5px;
  border:1px solid transparent;
  border-radius:11px;
  background:transparent;
  color:inherit;
  font:inherit;
  cursor:pointer;
  transition:
    background .16s ease,
    border-color .16s ease,
    box-shadow .16s ease;
}

.dash-alumni-account-button:hover,
.dash-alumni-account-button[aria-expanded="true"] {
  border-color:var(--border, #e5e7eb);
  background:#fff;
  box-shadow:0 8px 22px rgba(16,42,67,.07);
}

.dash-alumni-account-button .dash-topbar-user {
  cursor:pointer;
}

.dash-alumni-account-caret {
  width:24px;
  height:24px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:7px;
  color:var(--text-muted, #718096);
  transition:transform .16s ease, background .16s ease;
}

.dash-alumni-account-button:hover .dash-alumni-account-caret {
  background:#f2f5f8;
}

.dash-alumni-account-button[aria-expanded="true"]
.dash-alumni-account-caret {
  transform:rotate(180deg);
}

.dash-alumni-account-menu {
  position:absolute;
  top:calc(100% + 9px);
  right:0;
  width:270px;
  overflow:hidden;
  visibility:hidden;
  opacity:0;
  transform:translateY(-6px) scale(.985);
  transform-origin:top right;
  border:1px solid #dfe6ee;
  border-radius:13px;
  background:#fff;
  box-shadow:0 18px 42px rgba(16,42,67,.16);
  pointer-events:none;
  transition:
    opacity .16s ease,
    transform .16s ease,
    visibility .16s ease;
}

.dash-alumni-account-menu.open {
  visibility:visible;
  opacity:1;
  transform:translateY(0) scale(1);
  pointer-events:auto;
}

.dash-alumni-account-menu-head {
  display:grid;
  grid-template-columns:46px minmax(0,1fr);
  gap:10px;
  align-items:center;
  padding:13px;
  border-bottom:1px solid #e8edf2;
  background:linear-gradient(135deg,#f8fbff 0%,#fffaf0 100%);
}

.dash-alumni-account-menu-avatar {
  width:46px;
  height:46px;
  display:flex;
  align-items:center;
  justify-content:center;
  overflow:hidden;
  border:2px solid #d9e5f2;
  border-radius:12px;
  background:#edf4ff;
  color:#163a63;
  font-size:.78rem;
  font-weight:800;
  background-position:center;
  background-size:cover;
  background-repeat:no-repeat;
}

.dash-alumni-account-menu-head strong,
.dash-alumni-account-menu-head span {
  display:block;
}

.dash-alumni-account-menu-head strong {
  overflow:hidden;
  color:#102a43;
  font-size:.67rem;
  line-height:1.35;
  text-overflow:ellipsis;
  white-space:nowrap;
}

.dash-alumni-account-menu-head span {
  margin-top:2px;
  overflow:hidden;
  color:#77869a;
  font-size:.51rem;
  line-height:1.35;
  text-overflow:ellipsis;
  white-space:nowrap;
}

.dash-alumni-account-menu-links {
  padding:7px;
}

.dash-alumni-account-menu-item {
  width:100%;
  min-height:39px;
  display:flex;
  align-items:center;
  gap:9px;
  padding:8px 10px;
  border:0;
  border-radius:9px;
  background:transparent;
  color:#45556a;
  text-decoration:none;
  font-size:.59rem;
  font-weight:700;
  cursor:pointer;
  transition:background .14s ease, color .14s ease;
}

.dash-alumni-account-menu-item i {
  width:29px;
  height:29px;
  flex:0 0 29px;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:8px;
  background:#edf4ff;
  color:#2b6cb0;
  font-size:.88rem;
}

.dash-alumni-account-menu-item:hover {
  background:#f5f8fb;
  color:#102a43;
}

.dash-alumni-account-menu-item.settings i {
  background:#fff7e3;
  color:#a97913;
}

.dash-alumni-account-menu-separator {
  height:1px;
  margin:6px 4px;
  background:#e8edf2;
}

.dash-alumni-account-menu-item.signout {
  color:#a93e38;
}

.dash-alumni-account-menu-item.signout i {
  background:#fff1f0;
  color:#b84a44;
}

.dash-alumni-account-menu-item.signout:hover {
  background:#fff6f5;
  color:#94352f;
}

@media(max-width:680px) {
  .dash-alumni-account-button {
    padding:3px;
  }

  .dash-alumni-account-button .dash-topbar-user > div:last-child {
    display:none;
  }

  .dash-alumni-account-caret {
    display:none;
  }

  .dash-alumni-account-menu {
    width:min(270px, calc(100vw - 28px));
  }
}
</style>


<style>
/* TRACEGRAD sidebar logo */
.dash-brand .lnav-mark.dash-brand-logo {
  width:46px;
  height:46px;
  flex:0 0 46px;
  padding:0;
  overflow:hidden;
  display:flex;
  align-items:center;
  justify-content:center;
  border-radius:12px;
  background:#fff;
  box-shadow:0 5px 14px rgba(0,0,0,.14);
}

.dash-brand .lnav-mark.dash-brand-logo img {
  width:100%;
  height:100%;
  display:block;
  object-fit:contain;
  padding:3px;
}

@media(max-width:768px) {
  .dash-brand .lnav-mark.dash-brand-logo {
    width:42px;
    height:42px;
    flex-basis:42px;
  }
}
</style>

</head>
<body>
<div class="dash-shell">

  <!-- ═══ SIDEBAR ═══ -->
  <aside class="dash-sidebar" id="dash-sidebar">
    <div class="dash-brand">
      <div class="lnav-mark dash-brand-logo">
        <img
          src="assets/images/tracegrad-logo.png"
          alt="TRACEGRAD Logo"
        >
      </div>
      <div class="lnav-brand">
        <div class="b1">TRACEGRAD</div>
        <div class="b2">Alumni Portal</div>
      </div>
    </div>
    <nav class="dash-nav">
      <div class="dash-nav-label">Main</div>
      <button class="dash-link <?= $tab==='dashboard'?'on':'' ?>" onclick="location.href='?tab=dashboard'"><i class="ti ti-home"></i> Dashboard</button>
      <button class="dash-link <?= $tab==='profile'?'on':'' ?>" onclick="location.href='?tab=profile'"><i class="ti ti-user"></i> My Profile</button>
      <button class="dash-link <?= $tab==='employment'?'on':'' ?>" onclick="location.href='?tab=employment'"><i class="ti ti-briefcase"></i> Employment</button>
      <div class="dash-nav-label">CHED Survey</div>
      <button class="dash-link <?= $tab==='survey'?'on':'' ?>" onclick="location.href='?tab=survey'"><i class="ti ti-clipboard-text"></i> Tracer Survey <?php if ($surveyStatus === 'Not Started'): ?><span class="dash-badge">New</span><?php elseif ($surveyStatus === 'Partial'): ?><span class="dash-badge">Draft</span><?php endif; ?></button>
      <div class="dash-nav-label">Gallery</div>
      <button class="dash-link <?= $tab==='gallery'?'on':'' ?>" onclick="location.href='?tab=gallery'"><i class="ti ti-photo"></i> Photo Gallery</button>
      <button class="dash-link <?= $tab==='cart'?'on':'' ?>" onclick="location.href='?tab=cart'"><i class="ti ti-shopping-cart"></i> My Cart <?php if ($cartCount > 0): ?><span class="dash-badge"><?= $cartCount ?></span><?php endif; ?></button>
      <button class="dash-link <?= $tab==='orders'?'on':'' ?>" onclick="location.href='?tab=orders'"><i class="ti ti-receipt"></i> My Orders <?php if ($pendingOrders > 0): ?><span class="dash-badge"><?= $pendingOrders ?></span><?php endif; ?></button>
      <div class="dash-nav-label">Account</div>
      <button class="dash-link <?= $tab==='settings'?'on':'' ?>" onclick="location.href='?tab=settings'"><i class="ti ti-settings"></i> Settings</button>
    </nav>
  </aside>

  <div
    class="dash-sidebar-backdrop"
    id="dash-sidebar-backdrop"
    onclick="toggleAlumniSidebar(false)"
    aria-hidden="true"
  ></div>

  <!-- ═══ MAIN ═══ -->
  <div class="dash-main">

    <!-- TOP BAR -->
    <header class="dash-topbar">
      <button type="button" class="dash-burger" onclick="toggleAlumniSidebar()" aria-label="Open navigation"><i class="ti ti-menu-2"></i></button>
      <div class="dash-topbar-title"><?= esc(ucfirst($tab)) ?></div>
      <div class="dash-topbar-actions">
        <a href="?tab=cart" class="dash-icon-btn" aria-label="My Cart">
          <i class="ti ti-shopping-cart"></i>
          <?php if ($cartCount > 0): ?><span class="dash-icon-btn-badge"><?= $cartCount ?></span><?php endif; ?>
        </a>
        <div class="dash-alumni-account">

          <button
            type="button"
            class="dash-alumni-account-button"
            id="dash-alumni-account-button"
            aria-haspopup="true"
            aria-expanded="false"
            aria-controls="dash-alumni-account-menu"
            onclick="toggleAlumniAccountMenu(event)"
          >

            <div class="dash-topbar-user">

              <div
                class="dash-avatar"
                style="background-image:url('assets/profiles/<?= esc($grad['profile_picture'] ?? 'default.png') ?>')"
              >
                <?= getInitials($grad['firstname'] . ' ' . $grad['lastname']) ?>
              </div>

              <div>
                <div class="dash-user-name">
                  <?= esc($grad['firstname'] . ' ' . $grad['lastname']) ?>
                </div>

                <div class="dash-user-role">
                  <?= esc($grad['course_code']) ?> · Batch <?= esc($grad['batch_year']) ?>
                </div>
              </div>

            </div>

            <span class="dash-alumni-account-caret" aria-hidden="true">
              <i class="ti ti-chevron-down"></i>
            </span>

          </button>


          <div
            class="dash-alumni-account-menu"
            id="dash-alumni-account-menu"
            role="menu"
            aria-labelledby="dash-alumni-account-button"
          >

            <div class="dash-alumni-account-menu-head">

              <div
                class="dash-alumni-account-menu-avatar"
                style="background-image:url('assets/profiles/<?= esc($grad['profile_picture'] ?? 'default.png') ?>')"
              >
                <?= getInitials($grad['firstname'] . ' ' . $grad['lastname']) ?>
              </div>

              <div>
                <strong>
                  <?= esc($grad['firstname'] . ' ' . $grad['lastname']) ?>
                </strong>

                <span>
                  <?= esc($grad['course_code']) ?> · Batch <?= esc($grad['batch_year']) ?>
                </span>
              </div>

            </div>


            <div class="dash-alumni-account-menu-links">

              <a
                href="?tab=profile"
                class="dash-alumni-account-menu-item"
                role="menuitem"
                onclick="setAlumniAccountMenu(false)"
              >
                <i class="ti ti-user-circle"></i>
                <span>My Profile</span>
              </a>


              <a
                href="?tab=settings"
                class="dash-alumni-account-menu-item settings"
                role="menuitem"
                onclick="setAlumniAccountMenu(false)"
              >
                <i class="ti ti-settings"></i>
                <span>My Settings</span>
              </a>


              <div class="dash-alumni-account-menu-separator"></div>


              <a
                href="admin-logout.php"
                class="dash-alumni-account-menu-item signout"
                role="menuitem"
              >
                <i class="ti ti-logout"></i>
                <span>Sign Out</span>
              </a>

            </div>

          </div>

        </div>
      </div>
    </header>

    <div class="dash-content">

      <?php if ($flash): ?>
        <div class="dash-flash <?= $flash[0]==='ok' ? '' : 'error' ?>">
          <i class="ti <?= $flash[0]==='ok' ? 'ti-circle-check' : 'ti-alert-triangle' ?>"></i>
          <span><?= esc($flash[1]) ?></span>
        </div>
      <?php endif; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/dashboard.php'; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/profile.php'; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/employment.php'; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/survey.php'; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/gallery.php'; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/cart.php'; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/orders.php'; ?>

      <?php require __DIR__ . '/includes/alumni-dashboard/views/settings.php'; ?>


    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/alumni-dashboard/modals.php'; ?>

<?php
/* Enhanced profile editor uses a unique template ID so the
 * existing modal file does not need to be replaced. */
require __DIR__ . '/includes/alumni-dashboard/profile-edit-modal.php';
?>

<script>
function setAlumniAccountMenu(open) {
  var button =
    document.getElementById('dash-alumni-account-button');

  var menu =
    document.getElementById('dash-alumni-account-menu');

  if (!button || !menu) return;

  var shouldOpen = !!open;

  menu.classList.toggle('open', shouldOpen);

  button.setAttribute(
    'aria-expanded',
    shouldOpen ? 'true' : 'false'
  );
}


function toggleAlumniAccountMenu(event) {
  if (event) {
    event.preventDefault();
    event.stopPropagation();
  }

  var menu =
    document.getElementById('dash-alumni-account-menu');

  if (!menu) return;

  setAlumniAccountMenu(
    !menu.classList.contains('open')
  );
}


document.addEventListener('click', function (event) {
  var account =
    document.querySelector('.dash-alumni-account');

  if (
    account &&
    !account.contains(event.target)
  ) {
    setAlumniAccountMenu(false);
  }
});


document.addEventListener('keydown', function (event) {
  if (event.key === 'Escape') {
    setAlumniAccountMenu(false);
  }
});


window.addEventListener('resize', function () {
  setAlumniAccountMenu(false);
});
</script>

<script src="<?= esc($alumniJsHref) ?>?v=<?= esc((string)$alumniJsVersion) ?>"></script>
</body>
</html>