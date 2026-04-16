<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;

final class DiscountRepository
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return Database::connection()
            ->query('SELECT * FROM discounts ORDER BY id DESC')
            ->fetchAll();
    }

    /**
     * @param array<string, mixed> $data
     */
    public function create(array $data): void
    {
        $stmt = Database::connection()->prepare(
            'INSERT INTO discounts (
                target_type, target_id, discount_percent, rule_type, starts_at, ends_at, weekdays_csv, start_time, end_time, badge_style, label, is_active, created_at
             ) VALUES (
                :target_type, :target_id, :discount_percent, :rule_type, :starts_at, :ends_at, :weekdays_csv, :start_time, :end_time, :badge_style, :label, :is_active, :created_at
             )'
        );

        $stmt->execute([
            ':target_type' => $data['target_type'],
            ':target_id' => (int) $data['target_id'],
            ':discount_percent' => (float) $data['discount_percent'],
            ':rule_type' => $data['rule_type'],
            ':starts_at' => $data['starts_at'] ?? null,
            ':ends_at' => $data['ends_at'] ?? null,
            ':weekdays_csv' => $data['weekdays_csv'] ?? null,
            ':start_time' => $data['start_time'] ?? null,
            ':end_time' => $data['end_time'] ?? null,
            ':badge_style' => $data['badge_style'] ?? 'ribbon',
            ':label' => $data['label'] ?? null,
            ':is_active' => !empty($data['is_active']) ? 1 : 0,
            ':created_at' => gmdate('c'),
        ]);
    }

    public function delete(int $id): void
    {
        $stmt = Database::connection()->prepare('DELETE FROM discounts WHERE id = :id');
        $stmt->execute([':id' => $id]);
    }

    /**
     * @param array<int, int> $categoryIds
     * @param array<int, int> $productIds
     * @return array<int, array<string, mixed>>
     */
    public function candidatesForProducts(array $categoryIds, array $productIds): array
    {
        $categoryIds = array_values(array_unique(array_filter($categoryIds, static fn (int $id): bool => $id > 0)));
        $productIds = array_values(array_unique(array_filter($productIds, static fn (int $id): bool => $id > 0)));
        if (empty($categoryIds) && empty($productIds)) {
            return [];
        }

        $clauses = [];
        $params = [];

        if (!empty($categoryIds)) {
            $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
            $clauses[] = "(target_type = 'category' AND target_id IN ($placeholders))";
            foreach ($categoryIds as $id) {
                $params[] = $id;
            }
        }

        if (!empty($productIds)) {
            $placeholders = implode(',', array_fill(0, count($productIds), '?'));
            $clauses[] = "(target_type = 'product' AND target_id IN ($placeholders))";
            foreach ($productIds as $id) {
                $params[] = $id;
            }
        }

        $sql = 'SELECT * FROM discounts WHERE is_active = 1 AND (' . implode(' OR ', $clauses) . ')';
        $stmt = Database::connection()->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
}
