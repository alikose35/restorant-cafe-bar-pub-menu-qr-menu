<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class CategoryRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return Database::connection()
            ->query('SELECT * FROM categories ORDER BY position ASC, id ASC')
            ->fetchAll();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO categories (name, description, position, image_fit, created_at)
             VALUES (:name, :description, :position, :image_fit, :created_at)'
        );
        $stmt->execute([
            ':name' => trim((string) ($data['name'] ?? '')),
            ':description' => trim((string) ($data['description'] ?? '')),
            ':position' => (int) ($data['position'] ?? 0),
            ':image_fit' => in_array(($data['image_fit'] ?? 'cover'), ['cover', 'contain'], true) ? $data['image_fit'] : 'cover',
            ':created_at' => gmdate('c'),
        ]);
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(int $id, array $data): void
    {
        $stmt = Database::connection()->prepare(
            'UPDATE categories
             SET name = :name, description = :description, position = :position, image_fit = :image_fit
             WHERE id = :id'
        );
        $stmt->execute([
            ':id' => $id,
            ':name' => trim((string) ($data['name'] ?? '')),
            ':description' => trim((string) ($data['description'] ?? '')),
            ':position' => (int) ($data['position'] ?? 0),
            ':image_fit' => in_array(($data['image_fit'] ?? 'cover'), ['cover', 'contain'], true) ? $data['image_fit'] : 'cover',
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM categories WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * @param array<int, int> $orderedIds
     */
    public function reorder(array $orderedIds): void
    {
        $pdo = Database::connection();
        $allIds = array_map(
            static fn (array $row): int => (int) $row['id'],
            $pdo->query('SELECT id FROM categories ORDER BY position ASC, id ASC')->fetchAll()
        );
        if (empty($allIds)) {
            return;
        }

        $orderedIds = array_values(array_unique(array_filter($orderedIds, static fn (int $id): bool => $id > 0)));
        $orderedExisting = array_values(array_intersect($orderedIds, $allIds));
        $remaining = array_values(array_diff($allIds, $orderedExisting));
        $finalOrder = array_merge($orderedExisting, $remaining);

        $stmt = $pdo->prepare('UPDATE categories SET position = :position WHERE id = :id');
        $pdo->beginTransaction();
        foreach ($finalOrder as $index => $id) {
            $stmt->execute([
                ':position' => $index + 1,
                ':id' => $id,
            ]);
        }
        $pdo->commit();
    }
}
