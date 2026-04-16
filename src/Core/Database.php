<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function connection(): PDO
    {
        if (self::$pdo === null) {
            $dbFile = STORAGE_PATH . '/menu.sqlite';
            if (!is_dir(dirname($dbFile))) {
                mkdir(dirname($dbFile), 0775, true);
            }

            self::$pdo = new PDO('sqlite:' . $dbFile);
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->exec('PRAGMA foreign_keys = ON');
        }

        return self::$pdo;
    }

    public static function initialize(): void
    {
        $pdo = self::connection();

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                password_hash TEXT NOT NULL,
                created_at TEXT NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                description TEXT,
                position INTEGER NOT NULL DEFAULT 0,
                image_fit TEXT NOT NULL DEFAULT "cover",
                created_at TEXT NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS products (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                category_id INTEGER NOT NULL,
                name TEXT NOT NULL,
                description TEXT,
                price REAL NOT NULL DEFAULT 0,
                image_path TEXT,
                position INTEGER NOT NULL DEFAULT 0,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            )'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS settings (
                key TEXT PRIMARY KEY,
                value TEXT NOT NULL
            )'
        );

        $pdo->exec(
            'CREATE TABLE IF NOT EXISTS discounts (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                target_type TEXT NOT NULL CHECK(target_type IN ("category","product")),
                target_id INTEGER NOT NULL,
                discount_percent REAL NOT NULL DEFAULT 0,
                rule_type TEXT NOT NULL CHECK(rule_type IN ("always","date_range","weekly_time")),
                starts_at TEXT,
                ends_at TEXT,
                weekdays_csv TEXT,
                start_time TEXT,
                end_time TEXT,
                badge_style TEXT NOT NULL DEFAULT "ribbon",
                label TEXT,
                is_active INTEGER NOT NULL DEFAULT 1,
                created_at TEXT NOT NULL
            )'
        );

        self::seedAdmin($pdo);
        self::seedDefaults($pdo);
    }

    private static function seedAdmin(PDO $pdo): void
    {
        $count = (int) $pdo->query('SELECT COUNT(*) AS count FROM users')->fetch()['count'];
        if ($count > 0) {
            return;
        }

        $username = getenv('ADMIN_USERNAME') ?: 'admin';
        $password = getenv('ADMIN_PASSWORD') ?: 'admin12345';
        $stmt = $pdo->prepare('INSERT INTO users (username, password_hash, created_at) VALUES (:username, :password_hash, :created_at)');
        $stmt->execute([
            ':username' => $username,
            ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
            ':created_at' => gmdate('c'),
        ]);
    }

    private static function seedDefaults(PDO $pdo): void
    {
        $stmt = $pdo->prepare('INSERT OR IGNORE INTO settings (key, value) VALUES (:key, :value)');
        $stmt->execute([':key' => 'menu_theme', ':value' => 'list']);
        $stmt->execute([':key' => 'menu_color_scheme', ':value' => 'ember']);
        $stmt->execute([':key' => 'venue_theme', ':value' => 'restaurant']);
        $stmt->execute([':key' => 'menu_public_url', ':value' => (getenv('APP_URL') ?: 'http://localhost:8080') . '/']);
        $stmt->execute([':key' => 'menu_title', ':value' => 'Restoran Menusu']);
        $stmt->execute([':key' => 'menu_subtitle', ':value' => 'Taze urunler, hizli servis, net fiyat']);
        $stmt->execute([':key' => 'seo_title', ':value' => 'Restoran Menusu | Lezzet ve Kalite']);
        $stmt->execute([':key' => 'seo_description', ':value' => 'Mobil uyumlu restoran ve kafe menusu. Gunluk taze urunler ve net fiyatlar.']);
        $stmt->execute([':key' => 'seo_keywords', ':value' => 'restoran menusu,kafe menusu,qr menu']);
        $stmt->execute([':key' => 'seo_robots', ':value' => 'index,follow']);
        $stmt->execute([':key' => 'seo_favicon_url', ':value' => '/favicon.ico']);
        $stmt->execute([':key' => 'seo_og_image', ':value' => '']);
        $stmt->execute([':key' => 'seo_twitter_card', ':value' => 'summary_large_image']);
        $stmt->execute([':key' => 'seo_google_verification', ':value' => '']);
        $stmt->execute([':key' => 'seo_bing_verification', ':value' => '']);

        $categoryCount = (int) $pdo->query('SELECT COUNT(*) AS count FROM categories')->fetch()['count'];
        if ($categoryCount > 0) {
            return;
        }

        $pdo->beginTransaction();
        $insertCategory = $pdo->prepare('INSERT INTO categories (name, description, position, image_fit, created_at) VALUES (:name, :description, :position, :image_fit, :created_at)');
        $insertProduct = $pdo->prepare('INSERT INTO products (category_id, name, description, price, image_path, position, is_active, created_at) VALUES (:category_id, :name, :description, :price, :image_path, :position, :is_active, :created_at)');
        $now = gmdate('c');

        $categories = [
            ['name' => 'Kahvalti', 'description' => 'Gune enerji dolu baslangic', 'position' => 1, 'products' => [
                ['name' => 'Serpme Kahvalti', 'description' => 'Peynir cesitleri, zeytin, recel, sicak urun', 'price' => 390],
                ['name' => 'Menemen', 'description' => 'Domates, biber, yumurta', 'price' => 145],
            ]],
            ['name' => 'Ana Yemek', 'description' => 'Ustanin ozel tarifleri', 'position' => 2, 'products' => [
                ['name' => 'Izgara Tavuk', 'description' => 'Mevsim salata ve pilav ile', 'price' => 265],
                ['name' => 'Kasap Kofte', 'description' => 'Patates kizartmasi ile', 'price' => 285],
            ]],
            ['name' => 'Icecek', 'description' => 'Sicak ve soguk secenekler', 'position' => 3, 'products' => [
                ['name' => 'Limonata', 'description' => 'Taze sikim', 'price' => 95],
                ['name' => 'Filtre Kahve', 'description' => 'Gunluk cekim', 'price' => 110],
            ]],
        ];

        foreach ($categories as $category) {
            $insertCategory->execute([
                ':name' => $category['name'],
                ':description' => $category['description'],
                ':position' => $category['position'],
                ':image_fit' => 'cover',
                ':created_at' => $now,
            ]);
            $categoryId = (int) $pdo->lastInsertId();

            foreach ($category['products'] as $index => $product) {
                $insertProduct->execute([
                    ':category_id' => $categoryId,
                    ':name' => $product['name'],
                    ':description' => $product['description'],
                    ':price' => $product['price'],
                    ':image_path' => null,
                    ':position' => $index + 1,
                    ':is_active' => 1,
                    ':created_at' => $now,
                ]);
            }
        }

        $pdo->commit();
    }
}
