<?php
// public/notesmitter/reset.php
declare(strict_types=1);

// (5) no-cache
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok' => false, 'error' => 'Method Not Allowed']);
  exit;
}

$dir = __DIR__ . '/data';
if (!is_dir($dir)) { mkdir($dir, 0775, true); }

$file = $dir . '/note.json';

// (6) no-op if already empty
if (is_file($file)) {
  $curr = json_decode((string)file_get_contents($file), true) ?: [];
  if (($curr['noteData'] ?? '') === '') {
    echo json_encode(['ok' => true, 'noop' => true]);
    exit;
  }
}

$payload = ['noteData' => ''];
$ok = file_put_contents(
  $file,
  json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
  LOCK_EX
);

if ($ok === false) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Failed to reset file']);
  exit;
}

echo json_encode(['ok' => true]);
