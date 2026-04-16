<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\DiscountRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SettingRepository;
use App\Repositories\UserRepository;
use App\Security\Auth;
use App\Security\Csrf;
use App\Security\Upload;

final class AdminController
{
    public function loginPage(): void
    {
        if (Auth::check()) {
            redirect('/admin/dashboard');
        }

        View::render('admin/login', [
            'pageTitle' => 'Admin Giris',
            'error' => get_flash('error'),
        ], 'admin');
    }

    public function login(): void
    {
        if (!Csrf::verify($_POST['_token'] ?? null)) {
            set_flash('error', 'Guvenlik dogrulamasi basarisiz.');
            redirect('/admin/login');
        }

        $lockUntil = (int) ($_SESSION['login_lock_until'] ?? 0);
        if ($lockUntil > time()) {
            set_flash('error', 'Cok fazla deneme yapildi. Lutfen bir dakika bekleyin.');
            redirect('/admin/login');
        }

        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        if (Auth::login($username, $password)) {
            set_flash('success', 'Hos geldiniz.');
            redirect('/admin/dashboard');
        }

        $_SESSION['failed_login_count'] = (int) ($_SESSION['failed_login_count'] ?? 0) + 1;
        if ((int) $_SESSION['failed_login_count'] >= 5) {
            $_SESSION['login_lock_until'] = time() + 60;
            $_SESSION['failed_login_count'] = 0;
        }

        set_flash('error', 'Kullanici adi veya sifre hatali.');
        redirect('/admin/login');
    }

    public function logout(): void
    {
        if (Csrf::verify($_POST['_token'] ?? null)) {
            Auth::logout();
        }
        redirect('/admin/login');
    }

    public function dashboard(): void
    {
        $this->guard();
        View::render('admin/dashboard', array_merge($this->dashboardData(), [
            'pageTitle' => 'Admin Paneli',
            'success' => get_flash('success'),
            'error' => get_flash('error'),
        ]), 'admin');
    }

    public function saveCategory(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $repo = new CategoryRepository();
        $id = (int) ($_POST['id'] ?? 0);
        $payload = [
            'name' => (string) ($_POST['name'] ?? ''),
            'description' => (string) ($_POST['description'] ?? ''),
            'position' => (int) ($_POST['position'] ?? 0),
            'image_fit' => (string) ($_POST['image_fit'] ?? 'cover'),
        ];

        if ($payload['name'] === '') {
            $this->fail('Kategori adi bos olamaz.');
        }

        if ($id > 0) {
            $repo->update($id, $payload);
            $this->done('Kategori guncellendi.');
        } else {
            $repo->create($payload);
            $this->done('Kategori eklendi.');
        }
    }

    public function deleteCategory(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            (new CategoryRepository())->delete($id);
            $this->done('Kategori silindi.');
        }
        $this->done('Kategori kaydi bulunamadi.');
    }

    public function saveProduct(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $repo = new ProductRepository();
        $id = (int) ($_POST['id'] ?? 0);
        $payload = [
            'category_id' => (int) ($_POST['category_id'] ?? 0),
            'name' => (string) ($_POST['name'] ?? ''),
            'description' => (string) ($_POST['description'] ?? ''),
            'price' => (float) ($_POST['price'] ?? 0),
            'position' => (int) ($_POST['position'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];

        if ($payload['category_id'] <= 0 || trim($payload['name']) === '') {
            $this->fail('Urun adi ve kategori zorunludur.');
        }

        try {
            if (!empty($_FILES['image'])) {
                $imagePath = Upload::image($_FILES['image']);
                if ($imagePath !== null) {
                    $payload['image_path'] = $imagePath;
                }
            }
        } catch (\RuntimeException $exception) {
            $this->fail($exception->getMessage());
        }

        if ($id > 0) {
            $repo->update($id, $payload);
            $this->done('Urun guncellendi.');
        } else {
            $repo->create($payload);
            $this->done('Urun eklendi.');
        }
    }

    public function reorderCategories(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $ids = $_POST['ids'] ?? [];
        if (!is_array($ids)) {
            $this->fail('Kategori sirasi okunamadi.');
        }

        (new CategoryRepository())->reorder(array_map('intval', $ids));
        $this->done('Kategori sirasi guncellendi.');
    }

    public function reorderProducts(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $ids = $_POST['ids'] ?? [];
        if ($categoryId <= 0 || !is_array($ids)) {
            $this->fail('Urun sirasi okunamadi.');
        }

        (new ProductRepository())->reorderByCategory($categoryId, array_map('intval', $ids));
        $this->done('Urun sirasi guncellendi.');
    }

    public function deleteProduct(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $id = (int) ($_POST['id'] ?? 0);
        if ($id > 0) {
            (new ProductRepository())->delete($id);
            $this->done('Urun silindi.');
        }
        $this->done('Urun kaydi bulunamadi.');
    }

    public function saveSettings(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $theme = (string) ($_POST['menu_theme'] ?? 'list');
        if (!in_array($theme, ['list', 'cards', 'showcase'], true)) {
            $theme = 'list';
        }
        $colorScheme = (string) ($_POST['menu_color_scheme'] ?? 'ember');
        if (!in_array($colorScheme, ['ember', 'ocean', 'forest', 'sunrise', 'mono'], true)) {
            $colorScheme = 'ember';
        }
        $venueTheme = (string) ($_POST['venue_theme'] ?? 'restaurant');
        if (!in_array($venueTheme, ['restaurant', 'cafe', 'bar'], true)) {
            $venueTheme = 'restaurant';
        }

        $repo = new SettingRepository();
        $repo->set('menu_theme', $theme);
        $repo->set('menu_color_scheme', $colorScheme);
        $repo->set('venue_theme', $venueTheme);
        $repo->set('menu_title', trim((string) ($_POST['menu_title'] ?? 'Restoran Menusu')));
        $repo->set('menu_subtitle', trim((string) ($_POST['menu_subtitle'] ?? '')));
        $this->done('Menu ayarlari kaydedildi.');
    }

    public function saveDiscount(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $targetType = (string) ($_POST['target_type'] ?? '');
        $targetId = (int) ($_POST['target_id'] ?? 0);
        if (($targetType === '' || $targetId <= 0) && !empty($_POST['target_selection'])) {
            [$rawType, $rawId] = array_pad(explode(':', (string) $_POST['target_selection']), 2, '');
            $targetType = $rawType;
            $targetId = (int) $rawId;
        }
        $discountPercent = (float) ($_POST['discount_percent'] ?? 0);
        $ruleType = (string) ($_POST['rule_type'] ?? 'always');
        $startsAt = '';
        $endsAt = '';
        $weekdays = $_POST['weekdays'] ?? [];
        $startTime = trim((string) ($_POST['start_time'] ?? ''));
        $endTime = trim((string) ($_POST['end_time'] ?? ''));
        $badgeStyle = (string) ($_POST['badge_style'] ?? 'ribbon');
        $label = trim((string) ($_POST['label'] ?? ''));

        if (!in_array($targetType, ['category', 'product'], true) || $targetId <= 0) {
            $this->fail('Indirim hedefi secmelisiniz.');
        }
        if ($discountPercent <= 0 || $discountPercent > 95) {
            $this->fail('Indirim orani 0-95 arasinda olmali.');
        }
        if (!in_array($ruleType, ['always', 'date_range', 'weekly_time'], true)) {
            $this->fail('Indirim kurali gecersiz.');
        }
        if (!in_array($badgeStyle, ['ribbon', 'pill', 'tag'], true)) {
            $badgeStyle = 'ribbon';
        }

        $weekdaysCsv = null;
        if ($ruleType === 'date_range') {
            $startsAt = trim((string) ($_POST['date_range_starts_at'] ?? ''));
            $endsAt = trim((string) ($_POST['date_range_ends_at'] ?? ''));
            if ($startsAt === '' || $endsAt === '') {
                $this->fail('Tarih araligi kuralinda baslangic ve bitis zorunlu.');
            }
            if (strtotime($startsAt) === false || strtotime($endsAt) === false || strtotime($startsAt) > strtotime($endsAt)) {
                $this->fail('Tarih araligi gecersiz.');
            }
            $startTime = '';
            $endTime = '';
        } elseif ($ruleType === 'weekly_time') {
            if (!is_array($weekdays) || empty($weekdays)) {
                $this->fail('Haftalik indirim icin en az bir gun secmelisiniz.');
            }
            if ($startTime === '' || $endTime === '') {
                $this->fail('Haftalik indirim icin saat araligi zorunlu.');
            }
            $startsAt = trim((string) ($_POST['weekly_starts_at'] ?? ''));
            $endsAt = trim((string) ($_POST['weekly_ends_at'] ?? ''));
            $validDays = array_values(array_unique(array_filter(array_map('intval', $weekdays), static fn (int $day): bool => $day >= 1 && $day <= 7)));
            if (empty($validDays)) {
                $this->fail('Gun secimi gecersiz.');
            }
            $weekdaysCsv = implode(',', $validDays);
            if ($startsAt !== '' && strtotime($startsAt) === false) {
                $this->fail('Baslangic tarihi gecersiz.');
            }
            if ($endsAt !== '' && strtotime($endsAt) === false) {
                $this->fail('Bitis tarihi gecersiz.');
            }
            if ($startsAt !== '' && $endsAt !== '' && strtotime($startsAt) > strtotime($endsAt)) {
                $this->fail('Baslangic tarihi bitisten buyuk olamaz.');
            }
        } else {
            $startsAt = '';
            $endsAt = '';
            $startTime = '';
            $endTime = '';
        }

        (new DiscountRepository())->create([
            'target_type' => $targetType,
            'target_id' => $targetId,
            'discount_percent' => $discountPercent,
            'rule_type' => $ruleType,
            'starts_at' => $startsAt !== '' ? $startsAt : null,
            'ends_at' => $endsAt !== '' ? $endsAt : null,
            'weekdays_csv' => $weekdaysCsv,
            'start_time' => $startTime !== '' ? $startTime : null,
            'end_time' => $endTime !== '' ? $endTime : null,
            'badge_style' => $badgeStyle,
            'label' => $label !== '' ? $label : null,
            'is_active' => 1,
        ]);

        $this->done('Indirim kaydedildi.');
    }

    public function deleteDiscount(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();
        $id = (int) ($_POST['id'] ?? 0);
        if ($id <= 0) {
            $this->fail('Silinecek indirim secilmedi.');
        }
        (new DiscountRepository())->delete($id);
        $this->done('Indirim silindi.');
    }

    public function siteSettingsPage(): void
    {
        $this->guard();
        $repo = new SettingRepository();

        View::render('admin/site_settings', [
            'pageTitle' => 'Site Ayarlari',
            'menuPublicUrl' => $repo->get('menu_public_url', (getenv('APP_URL') ?: 'http://localhost:8080') . '/'),
            'seoTitle' => $repo->get('seo_title', ''),
            'seoDescription' => $repo->get('seo_description', ''),
            'seoKeywords' => $repo->get('seo_keywords', ''),
            'seoRobots' => $repo->get('seo_robots', 'index,follow'),
            'seoFaviconUrl' => $repo->get('seo_favicon_url', '/favicon.ico'),
            'seoOgImage' => $repo->get('seo_og_image', ''),
            'seoTwitterCard' => $repo->get('seo_twitter_card', 'summary_large_image'),
            'seoGoogleVerification' => $repo->get('seo_google_verification', ''),
            'seoBingVerification' => $repo->get('seo_bing_verification', ''),
            'success' => get_flash('success'),
            'error' => get_flash('error'),
        ], 'admin');
    }

    public function saveSeo(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $seoTitle = trim((string) ($_POST['seo_title'] ?? ''));
        $menuPublicUrl = trim((string) ($_POST['menu_public_url'] ?? ''));
        $seoDescription = trim((string) ($_POST['seo_description'] ?? ''));
        $seoKeywords = trim((string) ($_POST['seo_keywords'] ?? ''));
        $seoRobots = trim((string) ($_POST['seo_robots'] ?? 'index,follow'));
        $seoFaviconUrl = trim((string) ($_POST['seo_favicon_url'] ?? '/favicon.ico'));
        $seoOgImage = trim((string) ($_POST['seo_og_image'] ?? ''));
        $seoTwitterCard = trim((string) ($_POST['seo_twitter_card'] ?? 'summary_large_image'));
        $seoGoogleVerification = trim((string) ($_POST['seo_google_verification'] ?? ''));
        $seoBingVerification = trim((string) ($_POST['seo_bing_verification'] ?? ''));

        if (!in_array($seoRobots, ['index,follow', 'noindex,nofollow', 'index,nofollow', 'noindex,follow'], true)) {
            $seoRobots = 'index,follow';
        }
        if (!in_array($seoTwitterCard, ['summary', 'summary_large_image'], true)) {
            $seoTwitterCard = 'summary_large_image';
        }
        $menuPublicUrl = $this->normalizeUrl($menuPublicUrl);

        $repo = new SettingRepository();
        $repo->set('menu_public_url', mb_substr($menuPublicUrl, 0, 255));
        $repo->set('seo_title', mb_substr($seoTitle, 0, 70));
        $repo->set('seo_description', mb_substr($seoDescription, 0, 170));
        $repo->set('seo_keywords', mb_substr($seoKeywords, 0, 255));
        $repo->set('seo_robots', $seoRobots);
        $repo->set('seo_favicon_url', mb_substr($seoFaviconUrl, 0, 255));
        $repo->set('seo_og_image', mb_substr($seoOgImage, 0, 500));
        $repo->set('seo_twitter_card', $seoTwitterCard);
        $repo->set('seo_google_verification', mb_substr($seoGoogleVerification, 0, 255));
        $repo->set('seo_bing_verification', mb_substr($seoBingVerification, 0, 255));

        set_flash('success', 'SEO ayarlari kaydedildi.');
        redirect('/admin/site-settings');
    }

    private function normalizeUrl(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return (getenv('APP_URL') ?: 'http://localhost:8080') . '/';
        }
        if (!preg_match('#^https?://#i', $value)) {
            $value = 'https://' . $value;
        }
        return rtrim($value, '/') . '/';
    }

    public function changePassword(): void
    {
        $this->guard();
        $this->verifyCsrfOrFail();

        $currentPassword = (string) ($_POST['current_password'] ?? '');
        $newPassword = (string) ($_POST['new_password'] ?? '');
        $newPasswordConfirm = (string) ($_POST['new_password_confirm'] ?? '');
        $userId = Auth::id() ?? 0;

        if ($userId <= 0) {
            set_flash('error', 'Oturum bilgisi bulunamadi.');
            redirect('/admin/login');
        }

        $userRepo = new UserRepository();
        $user = $userRepo->findById($userId);
        if (!$user || !password_verify($currentPassword, (string) $user['password_hash'])) {
            set_flash('error', 'Mevcut sifre dogru degil.');
            redirect('/admin/site-settings');
        }

        if (strlen($newPassword) < 8) {
            set_flash('error', 'Yeni sifre en az 8 karakter olmali.');
            redirect('/admin/site-settings');
        }

        if (!preg_match('/[A-Za-z]/', $newPassword) || !preg_match('/\d/', $newPassword)) {
            set_flash('error', 'Yeni sifre en az bir harf ve bir rakam icermeli.');
            redirect('/admin/site-settings');
        }

        if (!hash_equals($newPassword, $newPasswordConfirm)) {
            set_flash('error', 'Yeni sifre ve tekrar sifresi eslesmiyor.');
            redirect('/admin/site-settings');
        }

        $userRepo->updatePasswordHash($userId, password_hash($newPassword, PASSWORD_DEFAULT));
        set_flash('success', 'Admin sifresi basariyla guncellendi.');
        redirect('/admin/site-settings');
    }

    private function guard(): void
    {
        if (!Auth::check()) {
            redirect('/admin/login');
        }
    }

    private function verifyCsrfOrFail(): void
    {
        if (!Csrf::verify($_POST['_token'] ?? null)) {
            $this->fail('Guvenlik dogrulamasi basarisiz.', 419);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function dashboardData(): array
    {
        $categoryRepo = new CategoryRepository();
        $productRepo = new ProductRepository();
        $discountRepo = new DiscountRepository();
        $settingRepo = new SettingRepository();
        $categories = $categoryRepo->all();
        $products = $productRepo->all();
        $discounts = $discountRepo->all();
        $discountTargetOptions = [];
        foreach ($categories as $category) {
            $discountTargetOptions[] = [
                'value' => 'category:' . $category['id'],
                'label' => 'Kategori: ' . $category['name'],
            ];
        }
        foreach ($products as $product) {
            $discountTargetOptions[] = [
                'value' => 'product:' . $product['id'],
                'label' => 'Urun: ' . $product['name'],
            ];
        }
        $productsByCategory = [];
        foreach ($products as $product) {
            $categoryId = (int) $product['category_id'];
            $productsByCategory[$categoryId][] = $product;
        }

        return [
            'categories' => $categories,
            'products' => $products,
            'productsByCategory' => $productsByCategory,
            'discounts' => $discounts,
            'discountTargetOptions' => $discountTargetOptions,
            'menuTheme' => $settingRepo->get('menu_theme', 'list'),
            'menuColorScheme' => $settingRepo->get('menu_color_scheme', 'ember'),
            'venueTheme' => $settingRepo->get('venue_theme', 'restaurant'),
            'menuTitle' => $settingRepo->get('menu_title', 'Restoran Menusu'),
            'menuSubtitle' => $settingRepo->get('menu_subtitle', ''),
        ];
    }

    private function done(string $message): never
    {
        if ($this->isAjax()) {
            $data = $this->dashboardData();
            $this->json([
                'ok' => true,
                'message' => $message,
                'fragments' => [
                    'categoryRows' => $this->renderPartial('admin/partials/category_rows', $data),
                    'productRows' => $this->renderPartial('admin/partials/product_rows', $data),
                    'categoryOptions' => $this->renderPartial('admin/partials/category_options', $data),
                    'sortableBlocks' => $this->renderPartial('admin/partials/sortable_blocks', $data),
                    'discountRows' => $this->renderPartial('admin/partials/discount_rows', $data),
                    'discountTargetOptions' => $this->renderPartial('admin/partials/discount_target_options', $data),
                ],
            ]);
        }

        set_flash('success', $message);
        redirect('/admin/dashboard');
    }

    private function fail(string $message, int $statusCode = 422): never
    {
        if ($this->isAjax()) {
            $this->json([
                'ok' => false,
                'message' => $message,
            ], $statusCode);
        }

        set_flash('error', $message);
        redirect('/admin/dashboard');
    }

    private function isAjax(): bool
    {
        return strtolower((string) ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '')) === 'xmlhttprequest';
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function json(array $payload, int $statusCode = 200): never
    {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function renderPartial(string $view, array $data): string
    {
        $viewFile = BASE_PATH . '/views/' . $view . '.php';
        extract($data, EXTR_SKIP);
        ob_start();
        require $viewFile;
        return (string) ob_get_clean();
    }
}
