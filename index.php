<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

$pdo = db();
$wireframes = $pdo->query("SELECT * FROM wireframes ORDER BY datetime(created_at) DESC")->fetchAll();

$flash = $_GET['msg'] ?? '';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Wireframe Share</title>
  <link rel="stylesheet" href="assets/style.css" />
</head>
<body>
  <div class="container">
    <header class="header">
      <h1>Wireframe Share</h1>
      <p class="muted">Upload a full-page wireframe image, share a link, preview in device frames.</p>
    </header>

    <?php if ($flash): ?>
      <div class="flash"><?= htmlspecialchars($flash) ?></div>
    <?php endif; ?>

    <section class="card form-card">
      <h2>Add wireframe</h2>
      <form class="form" action="upload.php" method="post" enctype="multipart/form-data">
        <div class="row">
          <label>
            <span>Name</span>
            <input type="text" name="name" required maxlength="120" placeholder="Homepage v1" />
          </label>

          <label>
            <span>Device size</span>
            <select name="device" required>
              <option value="mobile">Mobile (390×844)</option>
              <option value="tablet">Tablet (834×1112)</option>
              <option value="desktop" selected>Desktop (1440×900)</option>
            </select>
          </label>
        </div>

        <label class="full">
          <span>Wireframe image (PNG/JPG/WebP)</span>
          <input type="file" name="wireframe" accept="image/png,image/jpeg,image/webp" required />
        </label>

        <button class="btn primary" type="submit">Upload</button>
      </form>
      <p class="hint muted">
        Tip: export your wireframe as a tall PNG (full page). The viewer is scrollable inside the device frame.
      </p>
    </section>

    <section class="list">
      <div class="list-head">
        <h2>Saved wireframes</h2>
        <span class="pill"><?= count($wireframes) ?> total</span>
      </div>

      <?php if (count($wireframes) === 0): ?>
        <div class="empty muted">No wireframes yet. Upload your first one above.</div>
      <?php else: ?>
        <div class="grid">
          <?php foreach ($wireframes as $wf):
            $link = base_url() . '/view.php?t=' . urlencode($wf['token']);
          ?>
            <article class="card wf-card">
              <div class="wf-top">
                <div>
                  <h3><?= htmlspecialchars($wf['name']) ?></h3>
                  <div class="meta muted">
                    <span class="tag"><?= htmlspecialchars($wf['device']) ?></span>
                    <span>•</span>
                    <span><?= htmlspecialchars($wf['created_at']) ?></span>
                  </div>
                </div>
              </div>

              <div class="actions">
                <button class="btn" type="button" data-copy="<?= htmlspecialchars($link) ?>">Copy link</button>
                <a class="btn primary" href="view.php?t=<?= urlencode($wf['token']) ?>" target="_blank" rel="noopener">Visit</a>

                <form action="delete.php" method="post" onsubmit="return confirm('Delete this wireframe? This cannot be undone.');">
                  <input type="hidden" name="token" value="<?= htmlspecialchars($wf['token']) ?>">
                  <button class="btn danger" type="submit">Delete</button>
                </form>
              </div>

              <div class="linkrow">
                <input class="linkinput" type="text" readonly value="<?= htmlspecialchars($link) ?>" />
              </div>
            </article>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </div>

  <script src="assets/app.js"></script>
</body>
</html>
