<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Ali Kose">
    <meta name="description" content="<?= e((string) ($seoDescription ?? 'Mobil uyumlu dinamik restoran kafe menusu')) ?>">
    <meta name="keywords" content="<?= e((string) ($seoKeywords ?? 'restoran menusu,kafe menusu')) ?>">
    <meta name="robots" content="<?= e((string) ($seoRobots ?? 'index,follow')) ?>">
    <?php if (!empty($seoGoogleVerification)): ?>
        <meta name="google-site-verification" content="<?= e((string) $seoGoogleVerification) ?>">
    <?php endif; ?>
    <?php if (!empty($seoBingVerification)): ?>
        <meta name="msvalidate.01" content="<?= e((string) $seoBingVerification) ?>">
    <?php endif; ?>
    <meta property="og:title" content="<?= e((string) ($seoTitle ?? 'Menu')) ?>">
    <meta property="og:description" content="<?= e((string) ($seoDescription ?? 'Mobil uyumlu dinamik restoran kafe menusu')) ?>">
    <meta property="og:type" content="website">
    <?php if (!empty($seoOgImage)): ?>
        <meta property="og:image" content="<?= e((string) $seoOgImage) ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="<?= e((string) ($seoTwitterCard ?? 'summary_large_image')) ?>">
    <meta name="twitter:title" content="<?= e((string) ($seoTitle ?? 'Menu')) ?>">
    <meta name="twitter:description" content="<?= e((string) ($seoDescription ?? 'Mobil uyumlu dinamik restoran kafe menusu')) ?>">
    <?php if (!empty($seoOgImage)): ?>
        <meta name="twitter:image" content="<?= e((string) $seoOgImage) ?>">
    <?php endif; ?>
    <?php if (!empty($appUrl)): ?>
        <meta property="og:url" content="<?= e((string) $appUrl) ?>">
        <link rel="canonical" href="<?= e((string) $appUrl) ?>">
    <?php endif; ?>
    <title><?= e((string) ($seoTitle ?? $pageTitle ?? 'Menu')) ?></title>
    <?php if (!empty($seoFaviconUrl)): ?>
        <link rel="icon" href="<?= e((string) $seoFaviconUrl) ?>">
    <?php endif; ?>
    <link rel="me" href="https://github.com/alikose35">
    <script type="application/ld+json">
    {
      "@context":"https://schema.org",
      "@type":"WebSite",
      "name": <?= json_encode((string) ($menuTitle ?? 'Restoran Menusu'), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
      "url": <?= json_encode((string) ($appUrl ?? ''), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>,
      "creator": {
        "@type":"Person",
        "name":"Ali Kose",
        "sameAs":["https://github.com/alikose35"]
      }
    }
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&family=Fraunces:opsz,wght@9..144,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/base.css">
    <?php if (($theme ?? 'list') === 'cards'): ?>
        <link rel="stylesheet" href="/assets/css/theme-cards.css">
    <?php elseif (($theme ?? 'list') === 'showcase'): ?>
        <link rel="stylesheet" href="/assets/css/theme-showcase.css">
    <?php else: ?>
        <link rel="stylesheet" href="/assets/css/theme-list.css">
    <?php endif; ?>
</head>
<body class="theme-<?= e((string)($theme ?? 'list')) ?> scheme-<?= e((string)($colorScheme ?? 'ember')) ?> venue-<?= e((string)($venueTheme ?? 'restaurant')) ?>">
    <?= $content ?>
    <div style="max-width:1100px;margin:8px auto 18px;padding:0 16px;text-align:right;">
        <a href="https://github.com/alikose35" target="_blank" rel="me noopener noreferrer" style="font-size:11px;opacity:.55;color:inherit;text-decoration:none;">alikose35</a>
    </div>
    <script src="/assets/js/menu.js"></script>
</body>
</html>
