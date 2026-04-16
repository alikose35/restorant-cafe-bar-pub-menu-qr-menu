<?php declare(strict_types=1); ?>
<section class="hero">
    <div class="hero-inner">
        <p class="eyebrow">Canli Menu</p>
        <h1><?= e($menuTitle ?? 'Restoran Menusu') ?></h1>
        <p><?= e($menuSubtitle ?? '') ?></p>
    </div>
</section>

<section class="menu-wrapper">
    <?php foreach ($categories as $category): ?>
        <article class="category-block" id="cat-<?= e((string) $category['id']) ?>">
            <header class="category-head">
                <h2><?= e((string) $category['name']) ?></h2>
                <p><?= e((string) $category['description']) ?></p>
            </header>

            <?php if (empty($category['products'])): ?>
                <p class="empty-state">Bu kategoride urun yok.</p>
            <?php endif; ?>

            <div class="product-grid fit-<?= e((string)($category['image_fit'] ?? 'cover')) ?>">
                <?php foreach ($category['products'] as $product): ?>
                    <article class="product-item">
                        <?php if (!empty($product['has_discount'])): ?>
                            <span class="discount-badge badge-<?= e((string) ($product['discount_badge_style'] ?? 'ribbon')) ?>"><?= e((string) $product['discount_label']) ?></span>
                        <?php endif; ?>
                        <figure class="thumb">
                            <?php if (!empty($product['image_path'])): ?>
                                <img src="<?= e((string) $product['image_path']) ?>" alt="<?= e((string) $product['name']) ?>" loading="lazy">
                            <?php else: ?>
                                <div class="placeholder">Foto</div>
                            <?php endif; ?>
                        </figure>
                        <div class="info">
                            <h3><?= e((string) $product['name']) ?></h3>
                            <p><?= e((string) $product['description']) ?></p>
                        </div>
                        <div class="price-box">
                            <?php if (!empty($product['has_discount'])): ?>
                                <span class="old-price"><?= number_format((float) $product['original_price'], 2, ',', '.') ?> TL</span>
                            <?php endif; ?>
                            <strong class="price"><?= number_format((float) ($product['final_price'] ?? $product['price']), 2, ',', '.') ?> TL</strong>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </article>
    <?php endforeach; ?>
</section>
