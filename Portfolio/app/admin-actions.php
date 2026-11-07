<?php
declare(strict_types=1);
require_once __DIR__ . '/bootstrap.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verify_csrf()) {
    http_response_code(403);
    exit('Request could not be verified. Go back and try again.');
}

$action = (string)($_POST['action'] ?? '');
$site = site_data();
$projects = projects_data();

try {
    if ($action === 'save-site') {
        $fields = ['name'=>120,'eyebrow'=>120,'headline_top'=>80,'headline_accent'=>100,'intro'=>500,'hero_note'=>160,'about'=>1200,'music_intro'=>300,'email'=>180,'instagram'=>300,'github'=>300,'linkedin'=>300,'resume_url'=>300,'music_url'=>300];
        foreach ($fields as $key => $limit) if (array_key_exists($key, $_POST)) $site[$key] = allowed_text((string)$_POST[$key], $limit);
        if (array_key_exists('hero_alt', $_POST)) $site['hero_alt'] = allowed_text((string)$_POST['hero_alt'], 240);
        if (array_key_exists('skills', $_POST)) $site['skills'] = array_values(array_filter(array_map(static fn($v) => allowed_text((string)$v, 50), explode(',', (string)$_POST['skills']))));
        if (array_key_exists('music_genres', $_POST)) $site['music_genres'] = array_values(array_filter(array_map(static fn($v) => allowed_text((string)$v, 40), explode(',', (string)$_POST['music_genres']))));
        if (array_key_exists('accent', $_POST)) {
            $accent = (string)$_POST['accent'];
            $site['accent'] = preg_match('/^#[0-9a-fA-F]{6}$/', $accent) ? $accent : '#d8ff00';
        }
        foreach (['hero_image','menu_image'] as $field) {
            $uploaded = upload_image($field);
            if ($uploaded !== null) $site[$field] = $uploaded;
        }
        if (!write_json(SITE_FILE, $site)) throw new RuntimeException('Could not save site settings.');
        flash('Site content and image settings saved.');
    } elseif ($action === 'save-project') {
        $title = allowed_text((string)($_POST['title'] ?? ''), 100);
        if ($title === '') throw new RuntimeException('A project title is required.');
        $rawId = trim((string)($_POST['id'] ?? ''));
        $id = slugify($rawId !== '' ? $rawId : $title);
        $existing = null;
        foreach ($projects as $index => $project) if (($project['id'] ?? '') === $id) $existing = $index;
        $previous = $existing !== null ? $projects[$existing] : [];
        $image = upload_image('project_image') ?? (string)($previous['image'] ?? '');
        $tools = array_values(array_filter(array_map(static fn($v) => allowed_text((string)$v, 40), explode(',', (string)($_POST['tools'] ?? '')))));
        $entry = [
            'id'=>$id, 'title'=>$title,
            'category'=>allowed_text((string)($_POST['category'] ?? ''), 80),
            'summary'=>allowed_text((string)($_POST['summary'] ?? ''), 300),
            'description'=>allowed_text((string)($_POST['description'] ?? ''), 3000),
            'tools'=>$tools, 'image'=>$image,
            'alt'=>allowed_text((string)($_POST['alt'] ?? ''), 240),
            'demo_url'=>safe_url((string)($_POST['demo_url'] ?? '')),
            'source_url'=>safe_url((string)($_POST['source_url'] ?? '')),
            'published'=>isset($_POST['published']),
            'order'=>max(1, (int)($_POST['order'] ?? count($projects) + 1))
        ];
        if ($existing !== null) $projects[$existing] = $entry; else $projects[] = $entry;
        if (!write_json(PROJECTS_FILE, array_values($projects))) throw new RuntimeException('Could not save project.');
        flash('Project saved.');
    } elseif ($action === 'delete-project') {
        $id = (string)($_POST['id'] ?? '');
        $projects = array_values(array_filter($projects, static fn($project) => ($project['id'] ?? '') !== $id));
        if (!write_json(PROJECTS_FILE, $projects)) throw new RuntimeException('Could not remove project.');
        flash('Project removed. Its image file is kept so backups and other content are not broken.');
    } elseif ($action === 'add-album') {
        $albumsFile = CONTENT_DIR . DIRECTORY_SEPARATOR . 'albums.json';
        $albums = albums_data();
        if (count($albums) >= 3) throw new RuntimeException('There are already 3 albums. Remove one before adding another.');
        $title = allowed_text((string)($_POST['album_title'] ?? ''), 100);
        if ($title === '') throw new RuntimeException('Enter an album name.');
        $image = upload_image('album_image');
        if ($image === null) throw new RuntimeException('Choose an album cover image.');
        $albums[] = [
            'id' => bin2hex(random_bytes(8)),
            'title' => $title,
            'image' => $image,
            'alt' => allowed_text((string)($_POST['album_alt'] ?? ('Album cover for ' . $title)), 240)
        ];
        if (!write_json($albumsFile, $albums)) throw new RuntimeException('Could not save album.');
        flash('Album added.');
    } elseif ($action === 'delete-album') {
        $albumsFile = CONTENT_DIR . DIRECTORY_SEPARATOR . 'albums.json';
        $id = (string)($_POST['id'] ?? '');
        $albums = array_values(array_filter(albums_data(), static fn($album) => ($album['id'] ?? '') !== $id));
        if (!write_json($albumsFile, $albums)) throw new RuntimeException('Could not remove album.');
        flash('Album removed. Its cover image is kept in storage.');
    } elseif ($action === 'update-password') {
        $credentials = read_json(ADMIN_FILE, []);
        $current = (string)($_POST['current_password'] ?? '');
        $next = (string)($_POST['new_password'] ?? '');
        $confirm = (string)($_POST['confirm_password'] ?? '');
        if (empty($credentials['password_hash']) || !password_verify($current, (string)$credentials['password_hash'])) throw new RuntimeException('Current password was not recognized.');
        if (strlen($next) < 12) throw new RuntimeException('Choose a new password with at least 12 characters.');
        if ($next !== $confirm) throw new RuntimeException('The new passwords do not match.');
        if (!write_json(ADMIN_FILE, ['password_hash' => password_hash($next, PASSWORD_DEFAULT)], false)) throw new RuntimeException('Could not update the password.');
        session_regenerate_id(true);
        flash('Admin password updated.');
    } else {
        throw new RuntimeException('Unknown action.');
    }
} catch (Throwable $e) {
    flash($e->getMessage(), 'error');
}

header('Location: ' . app_url('admin/index.php'));
exit;
