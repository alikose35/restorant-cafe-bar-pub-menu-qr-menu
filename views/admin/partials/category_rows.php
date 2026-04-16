<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<?php foreach ($categories as $category): ?>
    <tr>
        <td><?= e((string) $category['id']) ?></td>
        <td><?= e((string) $category['name']) ?></td>
        <td><?= e((string) $category['position']) ?></td>
        <td>
            <form method="post" action="/admin/category/delete" data-ajax-form="1" data-confirm="Kategori silinsin mi?">
                <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                <input type="hidden" name="id" value="<?= e((string) $category['id']) ?>">
                <button class="btn btn-danger btn-sm py-0 px-2">x</button>
            </form>
        </td>
    </tr>
<?php endforeach; ?>
