<?php
require_once __DIR__ . '/config.php';

if (empty($_SESSION['alumni_id'])) {
    header('Location: alum-login.php');
    exit;
}
$graduateId = (int) $_SESSION['alumni_id'];

$flashOk = '';
$flashErr = '';

/* ─── Handle "Pay & Submit" form ─── */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $imageId   = (int) ($_POST['image_id'] ?? 0);
    $reference = trim($_POST['reference'] ?? '');

    $imgStmt = $pdo->prepare(
        "SELECT gi.image_id, gi.download_price FROM gallery_images gi
         WHERE gi.image_id = ? AND gi.status='Available'"
    );
    $imgStmt->execute([$imageId]);
    $img = $imgStmt->fetch();

    if (!$img) {
        $flashErr = 'That photo is no longer available.';
    } elseif ($reference === '') {
        $flashErr = 'Please enter your GCash reference number.';
    } else {
        $pdo->beginTransaction();
        try {
            $pdo->prepare(
                "INSERT INTO gallery_orders (graduate_id, image_id, amount, order_status)
                 VALUES (?, ?, ?, 'Pending')"
            )->execute([$graduateId, $img['image_id'], $img['download_price']]);
            $orderId = $pdo->lastInsertId();

            $pdo->prepare(
                "INSERT INTO payments (order_id, payment_reference, payment_method, amount_paid, payment_status, payment_date)
                 VALUES (?, ?, 'GCash', ?, 'Pending', NOW())"
            )->execute([$orderId, $reference, $img['download_price']]);

            $pdo->commit();
            $flashOk = "Payment submitted! Reference #$orderId is awaiting verification by the Alumni Affairs Office. You'll be notified once your HD download unlocks.";
        } catch (Exception $e) {
            $pdo->rollBack();
            $flashErr = 'Something went wrong submitting your payment. Please try again.';
        }
    }
}

/* ─── Data for the page ─── */
$galleryItems = $pdo->query(
    "SELECT gi.image_id, gi.title, gi.download_price, ga.album_name, ga.event_date
     FROM gallery_images gi
     JOIN gallery_albums ga ON ga.album_id = gi.album_id
     WHERE gi.status='Available' AND ga.status='Active'
     ORDER BY gi.uploaded_at DESC"
)->fetchAll();

/* Per-image purchase state for this alumni: none / pending / owned */
$stateStmt = $pdo->prepare(
    "SELECT go.image_id,
            MAX(CASE WHEN p.payment_status='Verified' THEN 1 ELSE 0 END) AS owned,
            MAX(CASE WHEN p.payment_status='Pending'  THEN 1 ELSE 0 END) AS pending
     FROM gallery_orders go
     LEFT JOIN payments p ON p.order_id = go.order_id
     WHERE go.graduate_id = ?
     GROUP BY go.image_id"
);
$stateStmt->execute([$graduateId]);
$state = [];
foreach ($stateStmt->fetchAll() as $row) {
    $state[$row['image_id']] = $row['owned'] ? 'owned' : ($row['pending'] ? 'pending' : 'none');
}

$autoOpen = isset($_GET['buy']) ? (int) $_GET['buy'] : 0;
$galColors = ['#b8c8e0','#c8b8e0','#b8e0c8','#e0d8b8','#e0b8b8','#b8d8e0','#d8e0b8','#e0c8b8'];
function esc($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Photo Gallery – TRACEGRAD</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,500;0,700;1,500&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
<link rel="stylesheet" href="assets/style.css">
</head>
<body style="background:var(--cream)">

<nav class="land-topnav">
  <div class="lnav-logo" style="cursor:pointer" onclick="location.href='alumni-dashboard.php'">
    <div class="lnav-mark">TG</div>
    <div class="lnav-brand"><div class="b1">TRACEGRAD</div><div class="b2">Alumni Gallery</div></div>
  </div>
  <div class="lnav-actions">
    <span style="color:#fff;font-size:13px;margin-right:14px"><?= esc($_SESSION['alumni_name']) ?></span>
    <button class="btn-land-admin" onclick="location.href='alumni-dashboard.php'"><i class="ti ti-arrow-left"></i> Dashboard</button>
    <button class="btn-land-alumni" onclick="location.href='logout.php'"><i class="ti ti-logout"></i> Sign Out</button>
  </div>
</nav>

<div class="pub-gal-wrap">
  <div class="pub-gal-inner">
    <div style="text-align:center;margin-bottom:30px">
      <div class="sec-chip">Alumni Gallery</div>
      <div class="sec-title">Browse & Download HD Photos</div>
      <p style="font-size:13px;color:var(--text-muted);max-width:520px;margin:0 auto;line-height:1.75">All photos are watermarked. Pay the listed price via GCash to unlock the full-resolution, watermark-free original — proceeds support the ISUFST Alumni Development Fund. Your payment is verified by the Alumni Affairs Office before the HD file unlocks.</p>
    </div>

    <?php if ($flashOk): ?>
      <div class="notice notice-success"><i class="ti ti-circle-check"></i><span><?= esc($flashOk) ?></span></div>
    <?php endif; ?>
    <?php if ($flashErr): ?>
      <div class="notice notice-warn"><i class="ti ti-alert-circle"></i><span><?= esc($flashErr) ?></span></div>
    <?php endif; ?>

    <div class="pub-gal-grid">
      <?php if ($galleryItems): foreach ($galleryItems as $i => $item):
          $free  = ((float)$item['download_price']) == 0.0;
          $price = number_format($item['download_price'], 0);
          $year  = $item['event_date'] ? date('Y', strtotime($item['event_date'])) : '';
          $bg    = $galColors[$i % count($galColors)];
          $st    = $state[$item['image_id']] ?? 'none';
      ?>
      <div class="pub-gi">
        <div class="pub-gi-inner" style="background:<?= $bg ?>">
          <i class="ti ti-photo" style="color:rgba(255,255,255,.7);font-size:34px"></i>
          <?php if ($st !== 'owned'): ?><div class="gi-watermark"></div><?php endif; ?>
        </div>
        <?php if ($free): ?><span class="gi-free">Free Preview</span>
        <?php elseif ($st === 'owned'): ?><span class="gi-free" style="background:var(--teal)">Owned</span>
        <?php elseif ($st === 'pending'): ?><span class="gi-price" style="background:var(--amber-bg);color:var(--amber)">Pending</span>
        <?php else: ?><span class="gi-price">₱<?= $price ?></span>
        <?php endif; ?>
        <span class="gi-type"><?= esc($item['album_name']) ?></span>
        <div class="gi-wm">© TRACEGRAD · ISUFST San Enrique <?= esc($year) ?></div>
        <div class="gi-buy-overlay">
          <?php if ($free): ?>
            <button class="gi-buy-btn gi-buy-free" type="button"><i class="ti ti-download"></i> Free Preview</button>
          <?php elseif ($st === 'owned'): ?>
            <button class="gi-buy-btn gi-buy-free" type="button"><i class="ti ti-download"></i> Download HD</button>
          <?php elseif ($st === 'pending'): ?>
            <button class="gi-buy-btn" type="button" style="background:var(--amber-bg);color:var(--amber)" disabled><i class="ti ti-clock"></i> Awaiting Verification</button>
          <?php else: ?>
            <button class="gi-buy-btn" type="button" onclick="openBuyModal(<?= (int)$item['image_id'] ?>,'<?= esc(addslashes($item['album_name'])) ?>',<?= (float)$item['download_price'] ?>)"><i class="ti ti-credit-card"></i> Pay ₱<?= $price ?> · Download HD</button>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; else: ?>
        <p style="grid-column:1/-1;text-align:center;color:var(--text-muted);font-size:13px;padding:24px">No gallery photos have been posted yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<!-- ══ GCASH PAYMENT MODAL ══ -->
<div class="modal-bg" id="buy-modal-bg">
  <div class="modal gcash-modal">
    <div class="modal-head">
      <h3><i class="ti ti-credit-card"></i> Complete Your Purchase</h3>
      <button class="modal-close" onclick="closeBuyModal()"><i class="ti ti-x"></i></button>
    </div>
    <form method="post">
      <input type="hidden" name="action" value="checkout">
      <input type="hidden" name="image_id" id="buy-image-id">
      <div class="modal-body">
        <div class="gcash-qr-box">
          <div class="gcash-logo-bar">
            <div class="gcash-logo-ic">TG</div>
            <div class="gcash-logo-txt">GCash</div>
          </div>
          <div class="gcash-qr-wrap">
            <svg class="gcash-qr-svg" viewBox="0 0 100 100"><rect width="100" height="100" fill="#fff"/><g fill="#00609c"><rect x="6" y="6" width="24" height="24"/><rect x="70" y="6" width="24" height="24"/><rect x="6" y="70" width="24" height="24"/><rect x="14" y="14" width="8" height="8" fill="#fff"/><rect x="78" y="14" width="8" height="8" fill="#fff"/><rect x="14" y="78" width="8" height="8" fill="#fff"/><rect x="40" y="6" width="6" height="6"/><rect x="52" y="6" width="6" height="6"/><rect x="40" y="18" width="6" height="6"/><rect x="60" y="40" width="6" height="6"/><rect x="72" y="46" width="6" height="6"/><rect x="46" y="52" width="6" height="6"/><rect x="58" y="58" width="6" height="6"/><rect x="70" y="64" width="6" height="6"/><rect x="40" y="70" width="6" height="6"/><rect x="52" y="82" width="6" height="6"/><rect x="64" y="82" width="6" height="6"/></g></svg>
          </div>
          <div class="gcash-number">0917 000 0000</div>
          <div class="gcash-name">ISUFST Alumni Development Fund</div>
          <div class="gcash-amount-tag"><i class="ti ti-tag"></i> <span id="buy-amount-tag">₱0</span></div>
        </div>
        <div class="gcash-timer"><i class="ti ti-info-circle"></i> Scan the QR in your GCash app, pay the exact amount above, then enter your reference number below.</div>
        <div class="pay-form-sec">
          <h4><i class="ti ti-receipt"></i> Payment Details</h4>
          <div class="fg">
            <label>Photo</label>
            <input type="text" id="buy-album-name" disabled>
          </div>
          <div class="fg">
            <label>GCash Reference Number</label>
            <input type="text" name="reference" placeholder="e.g. 1234567890123" required>
          </div>
        </div>
      </div>
      <div class="modal-foot">
        <button type="button" class="btn-sec" onclick="closeBuyModal()">Cancel</button>
        <button type="submit" class="btn-save"><i class="ti ti-send"></i> Submit for Verification</button>
      </div>
    </form>
  </div>
</div>

<script>
function openBuyModal(imageId, albumName, price) {
  document.getElementById('buy-image-id').value = imageId;
  document.getElementById('buy-album-name').value = albumName;
  document.getElementById('buy-amount-tag').textContent = '₱' + price.toLocaleString();
  document.getElementById('buy-modal-bg').classList.add('open');
}
function closeBuyModal() {
  document.getElementById('buy-modal-bg').classList.remove('open');
}
<?php if ($autoOpen && ($state[$autoOpen] ?? 'none') === 'none'):
    $autoItem = null;
    foreach ($galleryItems as $it) { if ((int)$it['image_id'] === $autoOpen) { $autoItem = $it; break; } }
    if ($autoItem): ?>
window.addEventListener('DOMContentLoaded', function() {
  openBuyModal(<?= (int)$autoItem['image_id'] ?>, '<?= esc(addslashes($autoItem['album_name'])) ?>', <?= (float)$autoItem['download_price'] ?>);
});
<?php endif; endif; ?>
</script>
</body>
</html>
