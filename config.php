<?php

declare(strict_types=1);

/**
 * Пароль для входа в cms.php.
 * Обязательно смените на свой длинный пароль перед публикацией сайта.
 */
const RIVERFISH_CMS_PASSWORD = 'riverfish70';

/**
 * Сессия для cms.php и media-api.php: на HTTPS куки с Secure и SameSite=Lax
 * (иначе вход/загрузка на хостинге могут не сохраняться между запросами).
 */
function riverfish_session_start(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || ((string) ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https')
        || (isset($_SERVER['SERVER_PORT']) && (int) $_SERVER['SERVER_PORT'] === 443);

    if (PHP_VERSION_ID >= 70300) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Lax',
        ]);
    } else {
        session_set_cookie_params(0, '/', '', $secure, true);
    }

    session_start();
}
