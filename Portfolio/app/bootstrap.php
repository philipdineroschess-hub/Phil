<?php
declare(strict_types=1);

define('PROJECT_ROOT', dirname(__DIR__));
define('STORAGE_DIR', PROJECT_ROOT . DIRECTORY_SEPARATOR . 'storage');
define('CONTENT_DIR', STORAGE_DIR . DIRECTORY_SEPARATOR . 'content');
define('UPLOAD_DIR', STORAGE_DIR . DIRECTORY_SEPARATOR . 'uploads');
define('BACKUP_DIR', STORAGE_DIR . DIRECTORY_SEPARATOR . 'backups');
define('ADMIN_FILE', STORAGE_DIR . DIRECTORY_SEPARATOR . 'admin.json');
define('SITE_FILE', CONTENT_DIR . DIRECTORY_SEPARATOR . 'site.json');
define('PROJECTS_FILE', CONTENT_DIR . DIRECTORY_SEPARATOR . 'projects.json');
$scriptPath = str_replace('\\', '/', (string)(parse_url($_SERVER['SCRIPT_NAME'] ?? '/', PHP_URL_PATH) ?: '/'));
$publicMarker = strpos($scriptPath, '/public/');
$appBase = $publicMarker === false ? '' : substr($scriptPath, 0, $publicMarker) . '/public';
define('APP_URL', $appBase);

require_once __DIR__ . '/helpers.php';

$scriptName = basename((string)(parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH) ?: ''));
if (!in_array($scriptName, ['media.php', 'save.php', 'logout.php'], true)) {
    ob_start('rewrite_root_links');
}

if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    $https = !empty($_SERVER['HTTPS']) && strtolower((string)$_SERVER['HTTPS']) !== 'off';
    session_set_cookie_params(['lifetime' => 0, 'path' => '/', 'secure' => $https, 'httponly' => true, 'samesite' => 'Lax']);
    session_start();
}
