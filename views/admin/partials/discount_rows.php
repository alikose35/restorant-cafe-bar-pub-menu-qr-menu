<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<?php
$weekdayMap = [
    1 => 'Pzt',
    2 => 'Sal',
    3 => 'Car',
    4 => 'Per',
    5 => 'Cum',
    6 => 'Cmt',
    7 => 'Paz',
];
$categoryNameMap = [];
foreach (($categories ?? []) as $category) {
    $categoryNameMap[(int) $category['id']] = (string) $category['name'];
}
$productNameMap = [];
foreach (($products ?? []) as $product) {
    $productNameMap[(int) $product['id']] = (string) $product['name'];
}
?>
<?php foreach ($discounts as $discount): ?>
    <?php
    $ruleType = (string) ($discount['rule_type'] ?? 'always');
    $scheduleText = 'Suresiz';
    if ($ruleType === 'date_range') {
        $scheduleText = trim((string) (($discount['starts_at'] ?? '') . ' - ' . ($discount['ends_at'] ?? '')));
    } elseif ($ruleType === 'weekly_time') {
        $days = array_filter(array_map('intval', explode(',', (string) ($discount['weekdays_csv'] ?? ''))));
        $dayNames = [];
        foreach ($days as $day) {
            if (isset($weekdayMap[$day])) {
                $dayNames[] = $weekdayMap[$day];
            }
        }
        $scheduleText = implode(',', $dayNames) . ' ' . (string) ($discount['start_time'] ?? '') . '-' . (string) ($discount['end_time'] ?? '');
    }
    ?>
    <tr>
        <td><?= e((string) $discount['id']) ?></td>
        <td>
            <?php if ((string) $discount['target_type'] === 'category'): ?>
                Kategori: <?= e($categoryNameMap[(int) $discount['target_id']] ?? ('#' . (string) $discount['target_id'])) ?>
            <?php else: ?>
                Urun: <?= e($productNameMap[(int) $discount['target_id']] ?? ('#' . (string) $discount['target_id'])) ?>
            <?php endif; ?>
        </td>
        <td>%<?= number_format((float) $discount['discount_percent'], 0, ',', '.') ?></td>
        <td><?= e($scheduleText) ?></td>
        <td><?= e((string) ($discount['badge_style'] ?? 'ribbon')) ?></td>
        <td>
            <form method="post" action="/admin/discount/delete" data-ajax-form="1" data-confirm="Indirim silinsin mi?">
                <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                <input type="hidden" name="id" value="<?= e((string) $discount['id']) ?>">
                <button class="btn btn-danger btn-sm py-0 px-2">x</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
