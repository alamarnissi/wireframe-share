<?php
declare(strict_types=1);

function db(): PDO {
  static $pdo = null;
  if ($pdo !== null) return $pdo;

  $dbPath = __DIR__ . '/data/app.sqlite';
  if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
  }

  $pdo = new PDO('sqlite:' . $dbPath);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

  // Initialize schema if needed
  $schema = file_get_contents(__DIR__ . '/schema.sql');
  $pdo->exec($schema);

  return $pdo;
}

function base_url(): string {
  // Works for http/https + subfolders
  $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
  $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
  $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
  $dir = rtrim(str_replace(basename($scriptName), '', $scriptName), '/');
  return $scheme . '://' . $host . $dir;
}

function random_token(int $bytes = 16): string {
  return bin2hex(random_bytes($bytes)); // 32 hex chars by default
}
