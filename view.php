<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

$t = $_GET['t'] ?? '';
if ($t === '' || !preg_match('/^[a-f0-9]{32}$/', $t)) {
  http_response_code(404);
  echo "Not found";
  exit;
}

$pdo = db();
$stmt = $pdo->prepare("SELECT * FROM wireframes WHERE token = :t LIMIT 1");
$stmt->execute([':t' => $t]);
$wf = $stmt->fetch();

if (!$wf) {
  http_response_code(404);
  echo "Not found";
  exit;
}

$device = $wf['device'];
// Allow user to switch device in viewer (optional)
if (isset($_GET['device']) && in_array($_GET['device'], ['mobile','tablet','desktop'], true)) {
  $device = $_GET['device'];
}

$name = $wf['name'];
$filePath = $wf['file_path'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($name) ?> — Wireframe</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body class="viewer">
  <div class="viewer-topbar">
    <div class="viewer-title">
      <strong><?= htmlspecialchars($name) ?></strong>
      <span class="muted">• <?= htmlspecialchars($wf['device']) ?> preset</span>
    </div>

    <div class="viewer-controls">
      <label class="select">
        <span class="muted">Device</span>
        <select id="deviceSelect">
          <option value="mobile" <?= $device==='mobile'?'selected':'' ?>>Mobile (390×844)</option>
          <option value="tablet" <?= $device==='tablet'?'selected':'' ?>>Tablet (834×1112)</option>
          <option value="desktop" <?= $device==='desktop'?'selected':'' ?>>Desktop (1440×900)</option>
        </select>
      </label>
      <button class="btn" id="fitBtn" type="button">Fit to width</button>
      <button class="btn" id="actualBtn" type="button">Actual size</button>
    </div>
  </div>

  <main class="viewer-main">
    <div class="device-stage">
      <div id="deviceFrame" class="device-frame <?= htmlspecialchars($device) ?>">
        <div id="deviceViewport" class="device-viewport">
          <img id="wireframeImg" src="<?= htmlspecialchars($filePath) ?>" alt="<?= htmlspecialchars($name) ?>" />
        </div>
      </div>
    </div>
  </main>

  <script>
    (function(){
      const token = <?= json_encode($t) ?>;
      const deviceSelect = document.getElementById('deviceSelect');
      deviceSelect.addEventListener('change', () => {
        const d = deviceSelect.value;
        const url = new URL(window.location.href);
        url.searchParams.set('t', token);
        url.searchParams.set('device', d);
        window.location.href = url.toString();
      });

      const viewport = document.getElementById('deviceViewport');
      const img = document.getElementById('wireframeImg');

      let mode = 'fit'; // 'fit' or 'actual'
      function fitToWidth(){
        mode = 'fit';
        img.style.width = '100%';
        img.style.height = 'auto';
      }
      function actualSize(){
        mode = 'actual';
        img.style.width = 'auto';
        img.style.maxWidth = 'none';
      }

      document.getElementById('fitBtn').addEventListener('click', fitToWidth);
      document.getElementById('actualBtn').addEventListener('click', actualSize);

      // default to fit
      fitToWidth();
      // start at top
      viewport.scrollTop = 0;
    })();
  </script>
</body>
</html>
