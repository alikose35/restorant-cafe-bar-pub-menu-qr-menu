<?php
declare(strict_types=1);

use App\Security\Csrf;
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Site Ayarlari</h1>
        <small class="text-muted">SEO bilgileri ve guvenlik ayarlari</small>
    </div>
    <a class="btn btn-outline-primary btn-sm" href="/admin/dashboard">Panele Don</a>
</div>

<?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= e((string) $success) ?></div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger"><?= e((string) $error) ?></div>
<?php endif; ?>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card panel-card">
            <div class="card-body">
                <h2 class="h6">SEO Bilgileri</h2>
                <form method="post" action="/admin/seo/save">
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <div class="mb-3">
                        <label class="form-label">Menu Public URL (QR hedefi)</label>
                        <input type="text" class="form-control" id="menu_public_url" name="menu_public_url" maxlength="255" value="<?= e((string) ($menuPublicUrl ?? '')) ?>">
                        <small class="text-muted">Ornek: https://menu.com/ veya https://menu.com/menu</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Title</label>
                        <input type="text" class="form-control" name="seo_title" maxlength="70" value="<?= e((string) $seoTitle) ?>">
                        <small class="text-muted">Onerilen: 50-60 karakter.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Description</label>
                        <textarea class="form-control" name="seo_description" maxlength="170" rows="4"><?= e((string) $seoDescription) ?></textarea>
                        <small class="text-muted">Onerilen: 120-160 karakter.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Meta Keywords</label>
                        <input type="text" class="form-control" name="seo_keywords" maxlength="255" value="<?= e((string) $seoKeywords) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Robots</label>
                        <select name="seo_robots" class="form-select">
                            <option value="index,follow" <?= (($seoRobots ?? '') === 'index,follow') ? 'selected' : '' ?>>index,follow</option>
                            <option value="noindex,nofollow" <?= (($seoRobots ?? '') === 'noindex,nofollow') ? 'selected' : '' ?>>noindex,nofollow</option>
                            <option value="index,nofollow" <?= (($seoRobots ?? '') === 'index,nofollow') ? 'selected' : '' ?>>index,nofollow</option>
                            <option value="noindex,follow" <?= (($seoRobots ?? '') === 'noindex,follow') ? 'selected' : '' ?>>noindex,follow</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Favicon URL</label>
                        <input type="text" class="form-control" name="seo_favicon_url" maxlength="255" value="<?= e((string) ($seoFaviconUrl ?? '/favicon.ico')) ?>">
                        <small class="text-muted">Ornek: /favicon.ico veya tam URL.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">OG Image URL</label>
                        <input type="text" class="form-control" name="seo_og_image" maxlength="500" value="<?= e((string) ($seoOgImage ?? '')) ?>">
                        <small class="text-muted">Ornek: /uploads/share.jpg veya https://site.com/share.jpg</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Twitter Card</label>
                        <select name="seo_twitter_card" class="form-select">
                            <option value="summary" <?= (($seoTwitterCard ?? '') === 'summary') ? 'selected' : '' ?>>summary</option>
                            <option value="summary_large_image" <?= (($seoTwitterCard ?? '') === 'summary_large_image') ? 'selected' : '' ?>>summary_large_image</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Google Verification</label>
                        <input type="text" class="form-control" name="seo_google_verification" maxlength="255" value="<?= e((string) ($seoGoogleVerification ?? '')) ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Bing Verification</label>
                        <input type="text" class="form-control" name="seo_bing_verification" maxlength="255" value="<?= e((string) ($seoBingVerification ?? '')) ?>">
                    </div>
                    <button class="btn btn-primary">SEO Kaydet</button>
                </form>
            </div>
        </div>

        <div class="card panel-card mt-3">
            <div class="card-body">
                <h2 class="h6">QR Kod</h2>
                <p class="text-muted small mb-2">Musterilerin menuye hizli girisi icin bu QR kodu kullanabilirsin.</p>
                <div class="d-flex flex-column align-items-start gap-2">
                    <img id="menu-qr-image" src="" alt="Menu QR" width="240" height="240" class="border rounded bg-white p-2">
                    <a id="menu-qr-target" href="<?= e((string) ($menuPublicUrl ?? '')) ?>" target="_blank" rel="noreferrer" class="small"></a>
                    <a id="download-qr-btn" href="#" class="btn btn-outline-dark btn-sm" download="menu-qr.png">QR Indir (PNG)</a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card panel-card">
            <div class="card-body">
                <h2 class="h6">Admin Sifre Degistir</h2>
                <form method="post" action="/admin/password/change">
                    <input type="hidden" name="_token" value="<?= e(Csrf::token()) ?>">
                    <div class="mb-3">
                        <label class="form-label">Mevcut Sifre</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yeni Sifre</label>
                        <input type="password" class="form-control" name="new_password" required minlength="8">
                        <small class="text-muted">En az 8 karakter, en az 1 harf + 1 rakam.</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Yeni Sifre (Tekrar)</label>
                        <input type="password" class="form-control" name="new_password_confirm" required minlength="8">
                    </div>
                    <button class="btn btn-dark">Sifreyi Guncelle</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
    const input = document.getElementById('menu_public_url');
    const image = document.getElementById('menu-qr-image');
    const target = document.getElementById('menu-qr-target');
    const button = document.getElementById('download-qr-btn');
    if (!input || !image || !target || !button) return;

    const normalizeUrl = (value) => {
        let url = String(value || '').trim();
        if (!url) return '';
        if (!/^https?:\/\//i.test(url)) {
            url = 'https://' + url;
        }
        return url;
    };

    const drawQr = () => {
        const url = normalizeUrl(input.value);
        target.textContent = url || '-';
        target.href = url || '#';

        if (!url) {
            image.removeAttribute('src');
            button.setAttribute('href', '#');
            return;
        }

        const qrSrc = 'https://api.qrserver.com/v1/create-qr-code/?size=240x240&margin=0&data=' + encodeURIComponent(url);
        image.src = qrSrc;
        button.setAttribute('href', qrSrc);
    };

    input.addEventListener('input', drawQr);
    drawQr();
})();
</script>
