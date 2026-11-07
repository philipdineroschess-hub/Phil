<?php
declare(strict_types=1);
require_once dirname(__DIR__) . '/app/bootstrap.php';

$relative = (string)($_GET['file'] ?? '');
if (!preg_match('~^uploads/[a-f0-9]{32}\.(jpg|png|webp)$~', $relative)) {
    http_response_code(404);
    exit;
}
$base = realpath(UPLOAD_DIR);
$file = realpath(UPLOAD_DIR . DIRECTORY_SEPARATOR . basename($relative));
if ($base === false || $file === false || !str_starts_with($file, $base . DIRECTORY_SEPARATOR) || !is_file($file)) {
    http_response_code(404);
    exit;
}
$mime = (new finfo(FILEINFO_MIME_TYPE))->file($file);
$types = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
if (!isset($types[$mime]) || pathinfo($file, PATHINFO_EXTENSION) !== $types[$mime]) {
    http_response_code(404);
    exit;
}
header('Content-Type: ' . $mime);
header('Content-Length: ' . (string)filesize($file));
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=86400');
readfile($file);
