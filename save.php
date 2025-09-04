<?php
// public/notesmitter/save.php
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

$raw  = file_get_contents('php://input');
$data = json_decode($raw, true);
$text = isset($data['text']) ? (string)$data['text'] : '';

if (mb_strlen($text, 'UTF-8') > 200000) {
  http_response_code(413);
  echo json_encode(['ok' => false, 'error' => 'Payload too large']);
  exit;
}

$dir = __DIR__ . '/data';
if (!is_dir($dir)) { mkdir($dir, 0775, true); }

$file = $dir . '/note.json';
$payload = ['noteData' => $text];

// (4) skip write if unchanged
if (is_file($file)) {
  $existing = json_decode((string)file_get_contents($file), true) ?: [];
  if (($existing['noteData'] ?? null) === $text) {
    echo json_encode(['ok' => true, 'path' => 'data/note.json', 'skipped' => true]);
    exit;
  }
}

$ok = file_put_contents(
  $file,
  json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
  LOCK_EX
);

if ($ok === false) {
  http_response_code(500);
  echo json_encode(['ok' => false, 'error' => 'Failed to write file']);
  exit;
}

echo json_encode(['ok' => true, 'path' => 'data/note.json']);
