<?php
declare(strict_types=1);

function h(mixed $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function app_url(string $path = ''): string
{
    $path = ltrim($path, '/');
    return APP_URL . ($path === '' ? '/' : '/' . $path);
}

function rewrite_root_links(string $html): string
{
    if (APP_URL === '') return $html;
    $rewritten = preg_replace_callback(
        '/\\b(href|src|action|poster)="\\/(?!\\/)([^"]*)"/i',
        static function (array $match): string {
            $path = '/' . $match[2];
            if ($path === APP_URL || str_starts_with($path, APP_URL . '/')) return $match[0];
            return $match[1] . '="' . APP_URL . $path . '"';
        },
        $html
    );
    return $rewritten ?? $html;
}

function read_json(string $path, mixed $fallback): mixed
{
    if (!is_file($path)) return $fallback;
    $raw = file_get_contents($path);
    if ($raw === false) return $fallback;
    $decoded = json_decode($raw, true);
    return json_last_error() === JSON_ERROR_NONE ? $decoded : $fallback;
}

function backup_json(string $path): void
{
    if (!is_file($path)) return;
    if (!is_dir(BACKUP_DIR)) mkdir(BACKUP_DIR, 0750, true);
    $base = basename($path, '.json');
    $target = BACKUP_DIR . DIRECTORY_SEPARATOR . $base . '-' . date('Ymd-His') . '.json';
    copy($path, $target);
}

function write_json(string $path, mixed $data, bool $createBackup = true): bool
{
    $folder = dirname($path);
    if (!is_dir($folder) && !mkdir($folder, 0750, true)) return false;
    if ($createBackup) backup_json($path);
    $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    if ($json === false) return false;
    $temp = $path . '.tmp-' . bin2hex(random_bytes(5));
    if (file_put_contents($temp, $json . PHP_EOL, LOCK_EX) === false) return false;
    if (!rename($temp, $path)) {
        @unlink($temp);
        return false;
    }
    return true;
}

function site_data(): array
{
    $site = read_json(SITE_FILE, []);
    return is_array($site) ? $site : [];
}

function projects_data(bool $publishedOnly = false): array
{
    $projects = read_json(PROJECTS_FILE, []);
    if (!is_array($projects)) $projects = [];
    $projects = array_values(array_filter($projects, static fn($p) => is_array($p) && (!$publishedOnly || !empty($p['published']))));
    usort($projects, static fn($a, $b) => ((int)($a['order'] ?? 999)) <=> ((int)($b['order'] ?? 999)));
    return $projects;
}

function albums_data(): array
{
    $albums = read_json(CONTENT_DIR . DIRECTORY_SEPARATOR . 'albums.json', []);
    if (!is_array($albums)) return [];
    return array_values(array_filter($albums, static fn($album) => is_array($album) && !empty($album['id']) && !empty($album['title'])));
}

function public_asset(string $relative): string
{
    if ($relative === '') return '';
    if (preg_match('~^https?://~i', $relative)) return $relative;
    $path = str_replace('\\', '/', $relative);
    if (!preg_match('~^uploads/[a-zA-Z0-9._-]+$~', $path)) return '';
    return app_url('media.php?file=' . rawurlencode($path));
}

function safe_url(string $url, bool $allowMailto = false): string
{
    $url = trim($url);
    if ($url === '') return '';
    if ($allowMailto && filter_var($url, FILTER_VALIDATE_EMAIL)) return 'mailto:' . $url;
    if (preg_match('~^https://~i', $url) && filter_var($url, FILTER_VALIDATE_URL)) return $url;
    return '';
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function verify_csrf(): bool
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals((string)$_SESSION['csrf'], (string)$_POST['csrf']);
}

function admin_logged_in(): bool
{
    return ($_SESSION['admin'] ?? false) === true;
}

function require_admin(): void
{
    if (admin_logged_in() && isset($_SESSION['last_activity']) && time() - (int)$_SESSION['last_activity'] > 1800) {
        $_SESSION = [];
        session_regenerate_id(true);
    }
    if (!admin_logged_in()) {
        header('Location: ' . app_url('admin/login.php'));
        exit;
    }
    $_SESSION['last_activity'] = time();
}

function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'] = ['message' => $message, 'type' => $type];
}

function take_flash(): ?array
{
    $f = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($f) ? $f : null;
}

function upload_image(string $field): ?string
{
    if (!isset($_FILES[$field]) || ($_FILES[$field]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    $file = $_FILES[$field];
    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || ($file['size'] ?? 0) > 5 * 1024 * 1024) {
        throw new RuntimeException('Image upload failed or exceeds the 5 MB limit.');
    }
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    $dimensions = @getimagesize($file['tmp_name']);
    if (!isset($allowed[$mime]) || $dimensions === false || $dimensions[0] > 12000 || $dimensions[1] > 12000) {
        throw new RuntimeException('Use a valid JPG, PNG, or WebP image.');
    }
    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0750, true)) throw new RuntimeException('Could not prepare image storage.');
    $name = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . DIRECTORY_SEPARATOR . $name)) {
        throw new RuntimeException('Could not save the uploaded image.');
    }
    return 'uploads/' . $name;
}

function allowed_text(string $value, int $limit = 2000): string
{
    $value = trim($value);
    return mb_substr($value, 0, $limit, 'UTF-8');
}

function slugify(string $value): string
{
    $slug = strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', $value) ?? '', '-'));
    return $slug !== '' ? $slug : 'project-' . bin2hex(random_bytes(3));
}
