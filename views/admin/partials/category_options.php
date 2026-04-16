<?php declare(strict_types=1); ?>
<option value="">Seciniz</option>
<?php foreach ($categories as $category): ?>
    <option value="<?= e((string) $category['id']) ?>"><?= e((string) $category['name']) ?></option>
<?php endforeach; ?>
