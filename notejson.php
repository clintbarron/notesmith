<?php
// public/notesmitter/notejson.php
declare(strict_types=1);

// (5) no-cache
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

$file = __DIR__ . '/data/note.json';

if (!is_file($file)) {
  echo json_encode(['noteData' => ''], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
  exit;
}

readfile($file);
