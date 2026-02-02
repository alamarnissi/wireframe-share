<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$name = trim($_POST['name'] ?? '');
$device = $_POST['device'] ?? '';
$file = $_FILES['wireframe'] ?? null;

$allowedDevices = ['mobile', 'tablet', 'desktop'];
if ($name === '' || mb_strlen($name) > 120) {
  header('Location: index.php?msg=' . urlencode('Invalid name.'));
  exit;
}
if (!in_array($device, $allowedDevices, true)) {
  header('Location: index.php?msg=' . urlencode('Invalid device preset.'));
  exit;
}
if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
  header('Location: index.php?msg=' . urlencode('Upload failed.'));
  exit;
}

// Basic file validation
$tmpPath = $file['tmp_name'];
$maxBytes = 15 * 1024 * 1024; // 15MB
if ($file['size'] > $maxBytes) {
  header('Location: index.php?msg=' . urlencode('File too large (max 15MB).'));
  exit;
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mime = $finfo->file($tmpPath);
$allowedMimes = [
  'image/png' => 'png',
  'image/jpeg' => 'jpg',
  'image/webp' => 'webp',
];
if (!isset($allowedMimes[$mime])) {
  header('Location: index.php?msg=' . urlencode('Only PNG/JPG/WebP allowed.'));
  exit;
}

if (!is_dir(__DIR__ . '/uploads')) {
  mkdir(__DIR__ . '/uploads', 0755, true);
}

// Generate safe filename + move
$ext = $allowedMimes[$mime];
$token = random_token(16);
$filename = $token . '.' . $ext;
$destPath = __DIR__ . '/uploads/' . $filename;

if (!move_uploaded_file($tmpPath, $destPath)) {
  header('Location: index.php?msg=' . urlencode('Could not save file.'));
  exit;
}

$pdo = db();
$stmt = $pdo->prepare("
  INSERT INTO wireframes (token, name, device, file_path, created_at)
  VALUES (:token, :name, :device, :file_path, :created_at)
");
$stmt->execute([
  ':token' => $token,
  ':name' => $name,
  ':device' => $device,
  ':file_path' => 'uploads/' . $filename,
  ':created_at' => gmdate('Y-m-d H:i:s') . ' UTC',
]);

header('Location: index.php?msg=' . urlencode('Wireframe uploaded!'));
exit;
