<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <div>
        <h1 class="h4 mb-0">Yonetim Paneli</h1>
        <small class="text-muted">Kategori, urun ve tema yonetimi</small>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary btn-sm" href="/admin/site-settings">Site Ayarlari</a>
        <a class="btn btn-outline-primary btn-sm" href="/" target="_blank" rel="noreferrer">Menuyu Ac</a>
    </div>
</div>

<div id="ajax-alert" class="alert d-none"></div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e((string) $success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e((string) $error) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card panel-card h-100">
            <div class="card-body">
                <h2 class="h6">Menu Ayarlari</h2>
                <form method="post" action="/admin/settings/save" data-ajax-form="1">
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <div class="mb-2">
                        <label class="form-label">Menu Basligi</label>
                        <input class="form-control form-control-sm" name="menu_title" value="<?= e((string) $menuTitle) ?>">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Menu Alt Basligi</label>
                        <input class="form-control form-control-sm" name="menu_subtitle" value="<?= e((string) $menuSubtitle) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tema</label>
                        <select name="menu_theme" class="form-select form-select-sm">
                            <option value="list" <?= ($menuTheme === 'list') ? 'selected' : '' ?>>Satir Listesi</option>
                            <option value="cards" <?= ($menuTheme === 'cards') ? 'selected' : '' ?>>E-Ticaret Kartlari</option>
                            <option value="showcase" <?= ($menuTheme === 'showcase') ? 'selected' : '' ?>>Vitrin Akisi</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Renk Semasi</label>
                        <select name="menu_color_scheme" class="form-select form-select-sm">
                            <option value="ember" <?= ($menuColorScheme === 'ember') ? 'selected' : '' ?>>Ember (Sicak)</option>
                            <option value="ocean" <?= ($menuColorScheme === 'ocean') ? 'selected' : '' ?>>Ocean (Mavi)</option>
                            <option value="forest" <?= ($menuColorScheme === 'forest') ? 'selected' : '' ?>>Forest (Yesil)</option>
                            <option value="sunrise" <?= ($menuColorScheme === 'sunrise') ? 'selected' : '' ?>>Sunrise (Canli)</option>
                            <option value="mono" <?= ($menuColorScheme === 'mono') ? 'selected' : '' ?>>Mono (Minimal)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Isletme Temasi</label>
                        <select name="venue_theme" class="form-select form-select-sm">
                            <option value="restaurant" <?= ($venueTheme === 'restaurant') ? 'selected' : '' ?>>Restoran</option>
                            <option value="cafe" <?= ($venueTheme === 'cafe') ? 'selected' : '' ?>>Cafe</option>
                            <option value="bar" <?= ($venueTheme === 'bar') ? 'selected' : '' ?>>Bar/Pub</option>
                        </select>
                    </div>
                    <button class="btn btn-primary btn-sm w-100">Kaydet</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card panel-card h-100">
            <div class="card-body">
                <h2 class="h6">Kategori Ekle / Guncelle</h2>
                <form method="post" action="/admin/category/save" data-ajax-form="1">
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <div class="mb-2">
                        <label class="form-label">ID (guncelleme icin)</label>
                        <input class="form-control form-control-sm" name="id" type="number" min="0" placeholder="Yeni icin bos birak">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kategori Adi</label>
                        <input class="form-control form-control-sm" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Aciklama</label>
                        <input class="form-control form-control-sm" name="description">
                    </div>
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Sira</label>
                            <input class="form-control form-control-sm" name="position" type="number" value="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">Gorsel Oturumu</label>
                            <select name="image_fit" class="form-select form-select-sm">
                                <option value="cover">Cover</option>
                                <option value="contain">Contain</option>
                            </select>
                        </div>
                    </div>
                    <button class="btn btn-dark btn-sm w-100 mt-3">Kaydet</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card panel-card h-100">
            <div class="card-body">
                <h2 class="h6">Urun Ekle / Guncelle</h2>
                <form method="post" action="/admin/product/save" enctype="multipart/form-data" data-ajax-form="1">
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <div class="mb-2">
                        <label class="form-label">ID (guncelleme icin)</label>
                        <input class="form-control form-control-sm" name="id" type="number" min="0" placeholder="Yeni icin bos birak">
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-select form-select-sm js-category-select" required>
                            <?php require BASE_PATH . '/views/admin/partials/category_options.php'; ?>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Urun Adi</label>
                        <input class="form-control form-control-sm" name="name" required>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Icerik / Aciklama</label>
                        <input class="form-control form-control-sm" name="description">
                    </div>
                    <div class="row g-2">
                        <div class="col-4">
                            <label class="form-label">Fiyat</label>
                            <input class="form-control form-control-sm" name="price" type="number" min="0" step="0.01" value="0">
                        </div>
                        <div class="col-4">
                            <label class="form-label">Sira</label>
                            <input class="form-control form-control-sm" name="position" type="number" value="0">
                        </div>
                        <div class="col-4 d-flex align-items-end">
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="is_active" checked>
                                <label class="form-check-label">Aktif</label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-2 mt-2">
                        <label class="form-label">Kucuk Gorsel (opsiyonel)</label>
                        <input class="form-control form-control-sm" name="image" type="file" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                    <button class="btn btn-success btn-sm w-100 mt-2">Kaydet</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-5">
        <div class="card panel-card">
            <div class="card-body">
                <h2 class="h6">Kategoriler</h2>
                <div class="table-responsive small-table">
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ad</th>
                            <th>Sira</th>
                            <th>Sil</th>
                        </tr>
                        </thead>
                        <tbody class="js-category-rows">
                        <?php require BASE_PATH . '/views/admin/partials/category_rows.php'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card panel-card">
            <div class="card-body">
                <h2 class="h6">Urunler</h2>
                <div class="table-responsive small-table">
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Ad</th>
                            <th>Kategori</th>
                            <th>Fiyat</th>
                            <th>Durum</th>
                            <th>Sil</th>
                        </tr>
                        </thead>
                        <tbody class="js-product-rows">
                        <?php require BASE_PATH . '/views/admin/partials/product_rows.php'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mt-1">
    <div class="col-lg-5">
        <div class="card panel-card h-100">
            <div class="card-body">
                <h2 class="h6">Otomatik Indirim Kurali</h2>
                <form method="post" action="/admin/discount/save" data-ajax-form="1" id="discount-form">
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <input type="hidden" name="target_type" value="">
                    <input type="hidden" name="target_id" value="">

                    <div class="mb-2">
                        <label class="form-label">Hedef</label>
                        <select name="target_selection" class="form-select form-select-sm js-discount-target">
                            <?php require BASE_PATH . '/views/admin/partials/discount_target_options.php'; ?>
                        </select>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label">Indirim %</label>
                            <input type="number" class="form-control form-control-sm" name="discount_percent" min="1" max="95" value="10" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">Badge Tasarimi</label>
                            <select name="badge_style" class="form-select form-select-sm">
                                <option value="ribbon">Ribbon</option>
                                <option value="pill">Pill</option>
                                <option value="tag">Tag</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-2 mt-2">
                        <label class="form-label">Etiket (opsiyonel)</label>
                        <input type="text" class="form-control form-control-sm" name="label" placeholder="%30 indirim">
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Kural</label>
                        <select name="rule_type" class="form-select form-select-sm js-rule-type">
                            <option value="always">Sinirsiz (Her zaman)</option>
                            <option value="date_range">Belirli Tarih Araligi</option>
                            <option value="weekly_time">Haftalik Saat Araligi</option>
                        </select>
                    </div>

                    <div class="js-rule-date-range d-none">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label">Baslangic</label>
                                <input type="datetime-local" class="form-control form-control-sm" name="date_range_starts_at">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Bitis</label>
                                <input type="datetime-local" class="form-control form-control-sm" name="date_range_ends_at">
                            </div>
                        </div>
                    </div>

                    <div class="js-rule-weekly d-none mt-2">
                        <label class="form-label">Gunler</label>
                        <div class="d-flex flex-wrap gap-2 small">
                            <label><input type="checkbox" name="weekdays[]" value="1"> Pzt</label>
                            <label><input type="checkbox" name="weekdays[]" value="2"> Sal</label>
                            <label><input type="checkbox" name="weekdays[]" value="3"> Car</label>
                            <label><input type="checkbox" name="weekdays[]" value="4"> Per</label>
                            <label><input type="checkbox" name="weekdays[]" value="5"> Cum</label>
                            <label><input type="checkbox" name="weekdays[]" value="6"> Cmt</label>
                            <label><input type="checkbox" name="weekdays[]" value="7"> Paz</label>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <label class="form-label">Saat Baslangic</label>
                                <input type="time" class="form-control form-control-sm" name="start_time">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Saat Bitis</label>
                                <input type="time" class="form-control form-control-sm" name="end_time">
                            </div>
                        </div>
                        <div class="row g-2 mt-1">
                            <div class="col-6">
                                <label class="form-label">Opsiyonel Baslangic Tarih</label>
                                <input type="date" class="form-control form-control-sm" name="weekly_starts_at">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Opsiyonel Bitis Tarih</label>
                                <input type="date" class="form-control form-control-sm" name="weekly_ends_at">
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-warning btn-sm w-100 mt-3">Indirimi Kaydet</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card panel-card">
            <div class="card-body">
                <h2 class="h6">Aktif Indirimler</h2>
                <div class="table-responsive small-table">
                    <table class="table table-sm align-middle">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Hedef</th>
                            <th>Oran</th>
                            <th>Kural</th>
                            <th>Badge</th>
                            <th>Sil</th>
                        </tr>
                        </thead>
                        <tbody class="js-discount-rows">
                        <?php require BASE_PATH . '/views/admin/partials/discount_rows.php'; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="js-sortable-blocks">
    <?php require BASE_PATH . '/views/admin/partials/sortable_blocks.php'; ?>
</div>
