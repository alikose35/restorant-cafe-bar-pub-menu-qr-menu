<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($pageTitle ?? 'Admin') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="container d-flex justify-content-between align-items-center py-3 gap-2">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <strong>Menu Admin</strong>
                <?php if (!empty($_SESSION['admin_user_id'])): ?>
                    <a href="/admin/dashboard" class="btn btn-sm btn-outline-light">Panel</a>
                    <a href="/admin/site-settings" class="btn btn-sm btn-outline-light">Site Ayarlari</a>
                <?php endif; ?>
            </div>
            <?php if (!empty($_SESSION['admin_user_id'])): ?>
                <form method="post" action="/admin/logout" class="m-0">
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <button type="submit" class="btn btn-sm btn-outline-light">Cikis</button>
                </form>
            <?php endif; ?>
        </div>
    </header>
    <main class="container py-4">
        <?= $content ?>
    </main>
    <script src="/assets/js/admin.js"></script>
</body>
</html>
