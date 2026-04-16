<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<div class="card panel-card mt-3">
    <div class="card-body">
        <h2 class="h6 mb-1">Surukle Birak Siralama</h2>
        <p class="text-muted small mb-3">Siralama kalici olarak kaydedilir ve menu ekrani ayni sirayla listeler.</p>

        <div class="row g-3">
            <div class="col-lg-4">
                <h3 class="h6">Kategoriler</h3>
                <ul class="sortable-list js-sortable-list"
                    data-url="/admin/category/reorder"
                    data-token="<?= e(Csrf::token()) ?>">
                    <?php foreach ($categories as $category): ?>
                        <li class="sortable-item" draggable="true" data-id="<?= e((string) $category['id']) ?>">
                            <span class="drag-handle">::</span>
                            <span><?= e((string) $category['name']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="col-lg-8">
                <h3 class="h6">Urunler (Kategori Icine Gore)</h3>
                <div class="row g-2">
                    <?php foreach ($categories as $category): ?>
                        <div class="col-md-6">
                            <div class="border rounded p-2 h-100 bg-white">
                                <strong class="small d-block mb-2"><?= e((string) $category['name']) ?></strong>
                                <ul class="sortable-list js-sortable-list"
                                    data-url="/admin/product/reorder"
                                    data-token="<?= e(Csrf::token()) ?>"
                                    data-category-id="<?= e((string) $category['id']) ?>">
                                    <?php foreach (($productsByCategory[(int) $category['id']] ?? []) as $product): ?>
                                        <li class="sortable-item" draggable="true" data-id="<?= e((string) $product['id']) ?>">
                                            <span class="drag-handle">::</span>
                                            <span><?= e((string) $product['name']) ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
