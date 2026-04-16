<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class SettingRepository
{
    public function get(string $key, ?string $default = null): ?string
    {
        $stmt = Database::connection()->prepare('SELECT value FROM settings WHERE key = :key LIMIT 1');
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        return $row['value'] ?? $default;
    }

    public function set(string $key, string $value): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO settings (key, value) VALUES (:key, :value)
            ON CONFLICT(key) DO UPDATE SET value = excluded.value'
        );
        $stmt->execute([
            ':key' => $key,
            ':value' => $value,
        ]);
    }
}
