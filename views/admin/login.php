<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h4 mb-3">Admin Giris</h1>
                <p class="text-muted small">Varsayilan: admin / admin12345 (ilk giriste degistirmen onerilir)</p>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= e((string) $error) ?></div>
                <?php endif; ?>
                <form method="post" action="/admin/login" novalidate>
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <div class="mb-3">
                        <label class="form-label">Kullanici Adi</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Sifre</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Giris Yap</button>
                </form>
            </div>
        </div>
    </div>
</div>
