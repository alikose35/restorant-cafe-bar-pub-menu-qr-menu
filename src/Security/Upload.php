<?php
declare(strict_types=1);

namespace App\Security;

final class Upload
{
    private const MAX_SIZE = 2_000_000;
    /** @var array<string, string> */
    private const MIME_MAP = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    /**
     * @param array<string, mixed> $file
     */
    public static function image(array $file): ?string
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Gorsel yukleme hatasi olustu.');
        }

        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_SIZE) {
            throw new \RuntimeException('Gorsel boyutu en fazla 2MB olabilir.');
        }

        $tmp = (string) ($file['tmp_name'] ?? '');
        $mime = mime_content_type($tmp) ?: '';
        if (!isset(self::MIME_MAP[$mime])) {
            throw new \RuntimeException('Sadece JPG, PNG veya WEBP yukleyebilirsiniz.');
        }

        if (@getimagesize($tmp) === false) {
            throw new \RuntimeException('Yuklenen dosya gecerli bir gorsel degil.');
        }

        $uploadDir = PUBLIC_PATH . '/uploads/' . date('Y/m');
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
            throw new \RuntimeException('Upload klasoru olusturulamadi.');
        }

        $filename = bin2hex(random_bytes(12)) . '.' . self::MIME_MAP[$mime];
        $target = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($tmp, $target)) {
            throw new \RuntimeException('Gorsel kaydedilemedi.');
        }

        return '/uploads/' . date('Y/m') . '/' . $filename;
    }
}
