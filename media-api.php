<?php

declare(strict_types=1);

/**
 * API управления файлами в папке media/ (только после входа тем же паролем, что и CMS).
 * Вызывается из cms.html по fetch с credentials: 'include'.
 */

ob_start();

session_start();

require_once __DIR__ . '/config.php';

function riverfish_media_json(array $payload, int $code = 200): void
{
    while (ob_get_level() > 0) {
        ob_end_clean();
    }
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');

    $flags = JSON_UNESCAPED_UNICODE;
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }

    $out = json_encode($payload, $flags);
    if ($out === false) {
        $out = json_encode(['ok' => false, 'error' => 'json_encode_failed'], JSON_UNESCAPED_UNICODE);
    }

    echo $out;
    exit;
}

function riverfish_media_dir(): string
{
    return __DIR__ . DIRECTORY_SEPARATOR . 'media';
}

function riverfish_media_authorized(): bool
{
    return !empty($_SESSION['riverfish_cms']);
}

function riverfish_media_csrf(): string
{
    if (empty($_SESSION['riverfish_media_csrf'])) {
        $_SESSION['riverfish_media_csrf'] = bin2hex(random_bytes(16));
    }

    return $_SESSION['riverfish_media_csrf'];
}

function riverfish_media_csrf_ok(?string $token): bool
{
    return is_string($token)
        && isset($_SESSION['riverfish_media_csrf'])
        && hash_equals($_SESSION['riverfish_media_csrf'], $token);
}

function riverfish_media_safe_name(string $name): ?string
{
    $name = basename(str_replace('\\', '/', $name));
    if ($name === '' || $name === '.' || $name === '..') {
        return null;
    }
    if (preg_match('/[\x00-\x1f\/\\\\]/', $name)) {
        return null;
    }
    if (strpos($name, '..') !== false) {
        return null;
    }

    return $name;
}

/** Имя файла для JSON: безопасная строка UTF-8 */
function riverfish_media_entry_name(string $entry): string
{
    $entry = (string) $entry;
    if (function_exists('mb_scrub')) {
        return mb_scrub($entry, 'UTF-8');
    }
    if (function_exists('iconv')) {
        $c = @iconv('UTF-8', 'UTF-8//IGNORE', $entry);

        return is_string($c) ? $c : $entry;
    }

    return $entry;
}

$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

if ($method === 'POST' && ($_POST['action'] ?? '') === 'login') {
    $pass = (string) ($_POST['password'] ?? '');
    if (hash_equals(RIVERFISH_CMS_PASSWORD, $pass)) {
        session_regenerate_id(true);
        $_SESSION['riverfish_cms'] = true;
        riverfish_media_csrf();

        riverfish_media_json([
            'ok' => true,
            'csrf' => riverfish_media_csrf(),
        ]);
    }
    riverfish_media_json(['ok' => false, 'error' => 'Неверный пароль'], 401);
}

if (!riverfish_media_authorized()) {
    riverfish_media_json(['ok' => false, 'error' => 'auth_required', 'hint' => 'Сначала войдите: POST action=login'], 401);
}

if ($method === 'GET' && ($_GET['action'] ?? '') === 'list') {
    $dir = riverfish_media_dir();
    if (!is_dir($dir)) {
        if (!@mkdir($dir, 0755, true) && !is_dir($dir)) {
            riverfish_media_json(['ok' => false, 'error' => 'Не удалось создать папку media'], 500);
        }
    }

    $files = [];
    $dh = opendir($dir);
    if ($dh === false) {
        riverfish_media_json(['ok' => false, 'error' => 'Не удалось прочитать папку'], 500);
    }
    while (($entry = readdir($dh)) !== false) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }
        $path = $dir . DIRECTORY_SEPARATOR . $entry;
        if (!is_file($path)) {
            continue;
        }
        if ($entry === '.htaccess' || $entry === '.gitkeep') {
            continue;
        }
        $files[] = [
            'name' => riverfish_media_entry_name($entry),
            'size' => filesize($path) ?: 0,
            'mtime' => filemtime($path) ?: 0,
        ];
    }
    closedir($dh);
    usort($files, function (array $a, array $b): int {
        return strcmp($a['name'], $b['name']);
    });

    riverfish_media_json([
        'ok' => true,
        'csrf' => riverfish_media_csrf(),
        'files' => $files,
    ]);
}

if ($method === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $csrf = (string) ($_POST['csrf'] ?? '');
    if (!riverfish_media_csrf_ok($csrf)) {
        riverfish_media_json(['ok' => false, 'error' => 'csrf'], 403);
    }
    $name = riverfish_media_safe_name((string) ($_POST['name'] ?? ''));
    if ($name === null) {
        riverfish_media_json(['ok' => false, 'error' => 'bad_name'], 400);
    }
    $path = riverfish_media_dir() . DIRECTORY_SEPARATOR . $name;
    if (!is_file($path)) {
        riverfish_media_json(['ok' => false, 'error' => 'not_found'], 404);
    }
    if (!@unlink($path)) {
        riverfish_media_json(['ok' => false, 'error' => 'unlink_failed'], 500);
    }
    $_SESSION['riverfish_media_csrf'] = bin2hex(random_bytes(16));

    riverfish_media_json(['ok' => true, 'csrf' => riverfish_media_csrf()]);
}

if ($method === 'POST' && ($_POST['action'] ?? '') === 'upload') {
    $csrf = (string) ($_POST['csrf'] ?? '');
    if (!riverfish_media_csrf_ok($csrf)) {
        riverfish_media_json(['ok' => false, 'error' => 'csrf'], 403);
    }
    if (empty($_FILES['file']) || !is_uploaded_file($_FILES['file']['tmp_name'])) {
        riverfish_media_json(['ok' => false, 'error' => 'no_file'], 400);
    }

    $allowedExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'avif'];
    $maxBytes = 8 * 1024 * 1024;

    $orig = (string) ($_FILES['file']['name'] ?? 'upload.bin');
    $base = riverfish_media_safe_name($orig);
    if ($base === null) {
        riverfish_media_json(['ok' => false, 'error' => 'bad_name'], 400);
    }
    $ext = strtolower(pathinfo($base, PATHINFO_EXTENSION));
    if ($ext === '' || !in_array($ext, $allowedExt, true)) {
        riverfish_media_json(['ok' => false, 'error' => 'bad_extension'], 400);
    }

    if (($_FILES['file']['size'] ?? 0) > $maxBytes) {
        riverfish_media_json(['ok' => false, 'error' => 'too_large'], 400);
    }

    $tmp = $_FILES['file']['tmp_name'];
    $mime = '';
    if (class_exists('finfo')) {
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($tmp) ?: '';
    }
    if ($mime === '') {
        $map = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'avif' => 'image/avif',
        ];
        $mime = $map[$ext] ?? '';
    }
    $allowedMime = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
        'image/svg+xml',
        'image/avif',
    ];
    if (!in_array($mime, $allowedMime, true)) {
        riverfish_media_json(['ok' => false, 'error' => 'bad_mime'], 400);
    }

    $destDir = riverfish_media_dir();
    if (!is_dir($destDir)) {
        @mkdir($destDir, 0755, true);
    }
    $dest = $destDir . DIRECTORY_SEPARATOR . $base;
    if (file_exists($dest)) {
        $stem = pathinfo($base, PATHINFO_FILENAME);
        $base = $stem . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $dest = $destDir . DIRECTORY_SEPARATOR . $base;
    }

    if (!move_uploaded_file($tmp, $dest)) {
        riverfish_media_json(['ok' => false, 'error' => 'move_failed'], 500);
    }
    @chmod($dest, 0644);
    $_SESSION['riverfish_media_csrf'] = bin2hex(random_bytes(16));

    riverfish_media_json([
        'ok' => true,
        'csrf' => riverfish_media_csrf(),
        'savedAs' => $base,
    ]);
}

riverfish_media_json(['ok' => false, 'error' => 'unknown_action'], 400);
