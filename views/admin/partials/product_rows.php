<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<?php foreach ($products as $product): ?>
    <tr>
        <td><?= e((string) $product['id']) ?></td>
        <td><?= e((string) $product['name']) ?></td>
        <td><?= e((string) ($product['category_name'] ?? $product['category_id'])) ?></td>
        <td><?= number_format((float) $product['price'], 2, ',', '.') ?> TL</td>
        <td><?= ((int) $product['is_active'] === 1) ? 'Aktif' : 'Pasif' ?></td>
        <td>
            <form method="post" action="/admin/product/delete" data-ajax-form="1" data-confirm="Urun silinsin mi?">
                <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                <input type="hidden" name="id" value="<?= e((string) $product['id']) ?>">
                <button class="btn btn-danger btn-sm py-0 px-2">x</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
