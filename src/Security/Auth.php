<?php
declare(strict_types=1);

namespace App\Security;

use App\Repositories\UserRepository;

final class Auth
{
    public static function check(): bool
    {
        return isset($_SESSION['admin_user_id']);
    }

    public static function id(): ?int
    {
        return isset($_SESSION['admin_user_id']) ? (int) $_SESSION['admin_user_id'] : null;
    }

    public static function login(string $username, string $password): bool
    {
        $user = (new UserRepository())->findByUsername($username);
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password_hash'])) {
            return false;
        }

        session_regenerate_id(true);
        $_SESSION['admin_user_id'] = (int) $user['id'];
        $_SESSION['admin_username'] = (string) $user['username'];
        unset($_SESSION['failed_login_count'], $_SESSION['login_lock_until']);
        return true;
    }

    public static function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
