<?php declare(strict_types=1); ?>
<option value="">Hedef seciniz</option>
<?php foreach ($discountTargetOptions as $option): ?>
    <option value="<?= e((string) $option['value']) ?>"><?= e((string) $option['label']) ?></option>
<?php endforeach; ?>
