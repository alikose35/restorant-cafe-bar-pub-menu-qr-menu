<?php
declare(strict_types=1);

use App\Controllers\AdminController;
use App\Controllers\MenuController;
use App\Core\Router;

require_once dirname(__DIR__) . '/src/bootstrap.php';

$router = new Router();
$menu = new MenuController();
$admin = new AdminController();

$router->get('/', static function () use ($menu): void {
    $menu->index();
});
$router->get('/admin', static function (): void {
    redirect('/admin/dashboard');
});
$router->get('/admin/login', static function () use ($admin): void {
    $admin->loginPage();
});
$router->post('/admin/login', static function () use ($admin): void {
    $admin->login();
});
$router->post('/admin/logout', static function () use ($admin): void {
    $admin->logout();
});
$router->get('/admin/dashboard', static function () use ($admin): void {
    $admin->dashboard();
});
$router->get('/admin/site-settings', static function () use ($admin): void {
    $admin->siteSettingsPage();
});

$router->post('/admin/category/save', static function () use ($admin): void {
    $admin->saveCategory();
});
$router->post('/admin/category/delete', static function () use ($admin): void {
    $admin->deleteCategory();
});
$router->post('/admin/product/save', static function () use ($admin): void {
    $admin->saveProduct();
});
$router->post('/admin/category/reorder', static function () use ($admin): void {
    $admin->reorderCategories();
});
$router->post('/admin/product/reorder', static function () use ($admin): void {
    $admin->reorderProducts();
});
$router->post('/admin/discount/save', static function () use ($admin): void {
    $admin->saveDiscount();
});
$router->post('/admin/discount/delete', static function () use ($admin): void {
    $admin->deleteDiscount();
});
$router->post('/admin/product/delete', static function () use ($admin): void {
    $admin->deleteProduct();
});
$router->post('/admin/settings/save', static function () use ($admin): void {
    $admin->saveSettings();
});
$router->post('/admin/seo/save', static function () use ($admin): void {
    $admin->saveSeo();
});
$router->post('/admin/password/change', static function () use ($admin): void {
    $admin->changePassword();
});

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$router->dispatch($method, $path);
