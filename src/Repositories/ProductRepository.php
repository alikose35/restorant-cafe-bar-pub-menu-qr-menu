<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class ProductRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return Database::connection()
            ->query(
                'SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON c.id = p.category_id
                ORDER BY p.position ASC, p.id ASC'
            )
            ->fetchAll();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function activeByCategory(int $categoryId): array
    {
        $stmt = Database::connection()->prepare(
            'SELECT * FROM products
            WHERE category_id = :category_id AND is_active = 1
            ORDER BY position ASC, id ASC'
        );
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO products (category_id, name, description, price, image_path, position, is_active, created_at)
            VALUES (:category_id, :name, :description, :price, :image_path, :position, :is_active, :created_at)'
        );
        $stmt->execute([
            ':category_id' => (int) ($data['category_id'] ?? 0),
            ':name' => trim((string) ($data['name'] ?? '')),
            ':description' => trim((string) ($data['description'] ?? '')),
            ':price' => (float) ($data['price'] ?? 0),
            ':image_path' => $data['image_path'] ?? null,
            ':position' => (int) ($data['position'] ?? 0),
            ':is_active' => !empty($data['is_active']) ? 1 : 0,
            ':created_at' => gmdate('c'),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $params = [
            ':id' => $id,
            ':category_id' => (int) ($data['category_id'] ?? 0),
            ':name' => trim((string) ($data['name'] ?? '')),
            ':description' => trim((string) ($data['description'] ?? '')),
            ':price' => (float) ($data['price'] ?? 0),
            ':position' => (int) ($data['position'] ?? 0),
            ':is_active' => !empty($data['is_active']) ? 1 : 0,
        ];

        if (array_key_exists('image_path', $data)) {
            $params[':image_path'] = $data['image_path'];
            $sql = 'UPDATE products
                SET category_id = :category_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    image_path = :image_path,
                    position = :position,
                    is_active = :is_active
                WHERE id = :id';
        } else {
            $sql = 'UPDATE products
                SET category_id = :category_id,
                    name = :name,
                    description = :description,
                    price = :price,
                    position = :position,
                    is_active = :is_active
                WHERE id = :id';
        }

        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM products WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * @param array<int, int> $orderedIds
     */
    public function reorderByCategory(int $categoryId, array $orderedIds): void
    {
        $pdo = Database::connection();
        $existingStmt = $pdo->prepare('SELECT id FROM products WHERE category_id = :category_id ORDER BY position ASC, id ASC');
        $existingStmt->execute([':category_id' => $categoryId]);
        $allIds = array_map(
            static fn (array $row): int => (int) $row['id'],
            $existingStmt->fetchAll()
        );
        if (empty($allIds)) {
            return;
        }

        $orderedIds = array_values(array_unique(array_filter($orderedIds, static fn (int $id): bool => $id > 0)));
        $orderedExisting = array_values(array_intersect($orderedIds, $allIds));
        $remaining = array_values(array_diff($allIds, $orderedExisting));
        $finalOrder = array_merge($orderedExisting, $remaining);

        $updateStmt = $pdo->prepare('UPDATE products SET position = :position WHERE id = :id AND category_id = :category_id');
        $pdo->beginTransaction();
        foreach ($finalOrder as $index => $id) {
            $updateStmt->execute([
                ':position' => $index + 1,
                ':id' => $id,
                ':category_id' => $categoryId,
            ]);
        }
        $pdo->commit();
    }
}
