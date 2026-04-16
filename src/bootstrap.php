<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('STORAGE_PATH', BASE_PATH . '/storage');
define('PUBLIC_PATH', BASE_PATH . '/public');

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (strncmp($class, $prefix, strlen($prefix)) !== 0) {
        return;
    }

    $relative = substr($class, strlen($prefix));
    $file = BASE_PATH . '/src/' . str_replace('\\', '/', $relative) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

require_once BASE_PATH . '/src/helpers.php';

$appTimezone = getenv('APP_TIMEZONE') ?: 'Europe/Istanbul';
date_default_timezone_set($appTimezone);

$sessionSecure = filter_var($_ENV['SESSION_SECURE'] ?? getenv('SESSION_SECURE') ?: false, FILTER_VALIDATE_BOOL);
session_name('menu_admin_session');
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'secure' => $sessionSecure,
    'samesite' => 'Lax',
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

App\Core\Database::initialize();
