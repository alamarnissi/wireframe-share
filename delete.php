<?php
declare(strict_types=1);
require __DIR__ . '/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: index.php');
  exit;
}

$token = $_POST['token'] ?? '';
if ($token === '' || !preg_match('/^[a-f0-9]{32}$/', $token)) {
  header('Location: index.php?msg=' . urlencode('Invalid delete request.'));
  exit;
}

$pdo = db();

// Find record first (so we can delete the file)
$stmt = $pdo->prepare("SELECT file_path FROM wireframes WHERE token = :t LIMIT 1");
$stmt->execute([':t' => $token]);
$row = $stmt->fetch();

if (!$row) {
  header('Location: index.php?msg=' . urlencode('Wireframe not found.'));
  exit;
}

// Delete DB row
$del = $pdo->prepare("DELETE FROM wireframes WHERE token = :t");
$del->execute([':t' => $token]);

// Delete file from disk (safe path)
$filePath = $row['file_path'];
$fullPath = __DIR__ . '/' . $filePath;

// Only allow deleting inside /uploads
$uploadsDir = realpath(__DIR__ . '/uploads');
$realFile = realpath($fullPath);

if ($uploadsDir && $realFile && str_starts_with($realFile, $uploadsDir) && file_exists($realFile)) {
  @unlink($realFile);
}

header('Location: index.php?msg=' . urlencode('Wireframe deleted.'));
exit;
