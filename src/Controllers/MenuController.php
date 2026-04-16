<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Core\DiscountEngine;
use App\Core\View;
use App\Repositories\CategoryRepository;
use App\Repositories\DiscountRepository;
use App\Repositories\ProductRepository;
use App\Repositories\SettingRepository;

final class MenuController
{
    public function index(): void
    {
        $settings = new SettingRepository();
        $categoryRepo = new CategoryRepository();
        $productRepo = new ProductRepository();
        $discountRepo = new DiscountRepository();

        $theme = $settings->get('menu_theme', 'list') ?? 'list';
        if (!in_array($theme, ['list', 'cards', 'showcase'], true)) {
            $theme = 'list';
        }

        $colorScheme = $settings->get('menu_color_scheme', 'ember') ?? 'ember';
        if (!in_array($colorScheme, ['ember', 'ocean', 'forest', 'sunrise', 'mono'], true)) {
            $colorScheme = 'ember';
        }
        $venueTheme = $settings->get('venue_theme', 'restaurant') ?? 'restaurant';
        if (!in_array($venueTheme, ['restaurant', 'cafe', 'bar'], true)) {
            $venueTheme = 'restaurant';
        }

        $categories = $categoryRepo->all();
        $categoryIds = [];
        $productIds = [];
        foreach ($categories as &$category) {
            $category['products'] = $productRepo->activeByCategory((int) $category['id']);
            $categoryIds[] = (int) $category['id'];
            foreach ($category['products'] as $product) {
                $productIds[] = (int) $product['id'];
            }
        }

        $discounts = $discountRepo->candidatesForProducts($categoryIds, $productIds);
        $now = new \DateTimeImmutable('now');

        foreach ($categories as &$category) {
            foreach ($category['products'] as &$product) {
                $bestDiscount = $this->resolveBestDiscount((int) $category['id'], (int) $product['id'], $discounts, $now);
                $originalPrice = (float) $product['price'];
                $discountPercent = $bestDiscount['discount_percent'] ?? 0.0;
                $discountedPrice = $originalPrice;
                if ($discountPercent > 0) {
                    $discountedPrice = round($originalPrice * (100 - $discountPercent) / 100, 2);
                }

                $product['original_price'] = $originalPrice;
                $product['final_price'] = $discountedPrice;
                $product['discount_percent'] = $discountPercent;
                $product['has_discount'] = $discountPercent > 0;
                $product['discount_label'] = $bestDiscount['label'] ?? ('%' . (int) $discountPercent . ' indirim');
                $product['discount_badge_style'] = $bestDiscount['badge_style'] ?? 'ribbon';
            }
            unset($product);
        }
        unset($category);

        $publicUrl = $settings->get('menu_public_url', (getenv('APP_URL') ?: '') . '/');

        View::render('menu/index', [
            'theme' => $theme,
            'colorScheme' => $colorScheme,
            'venueTheme' => $venueTheme,
            'menuTitle' => $settings->get('menu_title', 'Restoran Menusu'),
            'menuSubtitle' => $settings->get('menu_subtitle', ''),
            'seoTitle' => $settings->get('seo_title', 'Restoran Menusu | Lezzet ve Kalite'),
            'seoDescription' => $settings->get('seo_description', 'Mobil uyumlu restoran ve kafe menusu.'),
            'seoKeywords' => $settings->get('seo_keywords', 'restoran menusu,kafe menusu,qr menu'),
            'seoRobots' => $settings->get('seo_robots', 'index,follow'),
            'seoFaviconUrl' => $this->resolveUrl($settings->get('seo_favicon_url', '/favicon.ico') ?? '/favicon.ico', (string) $publicUrl),
            'seoOgImage' => $this->resolveUrl($settings->get('seo_og_image', '') ?? '', (string) $publicUrl),
            'seoTwitterCard' => $settings->get('seo_twitter_card', 'summary_large_image'),
            'seoGoogleVerification' => $settings->get('seo_google_verification', ''),
            'seoBingVerification' => $settings->get('seo_bing_verification', ''),
            'appUrl' => $publicUrl ?: (getenv('APP_URL') ?: ''),
            'categories' => $categories,
            'pageTitle' => $settings->get('seo_title', 'Menu'),
        ]);
    }

    /**
     * @param array<int, array<string, mixed>> $discounts
     * @return array<string, mixed>|null
     */
    private function resolveBestDiscount(int $categoryId, int $productId, array $discounts, \DateTimeImmutable $now): ?array
    {
        $best = null;
        $bestPercent = 0.0;

        foreach ($discounts as $discount) {
            $targetType = (string) ($discount['target_type'] ?? '');
            $targetId = (int) ($discount['target_id'] ?? 0);
            if (
                !($targetType === 'category' && $targetId === $categoryId)
                && !($targetType === 'product' && $targetId === $productId)
            ) {
                continue;
            }

            if (!DiscountEngine::isActiveNow($discount, $now)) {
                continue;
            }

            $percent = max(0.0, min(95.0, (float) ($discount['discount_percent'] ?? 0)));
            if ($percent > $bestPercent) {
                $bestPercent = $percent;
                $best = $discount;
            }
        }

        return $best;
    }

    private function resolveUrl(string $value, string $appUrl): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $value)) {
            return $value;
        }

        if ($appUrl === '') {
            return $value;
        }

        return rtrim($appUrl, '/') . '/' . ltrim($value, '/');
    }
}
