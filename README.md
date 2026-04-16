# Dijital Restoran / Kafe / Bar Menüsü

MySQL gerektirmeyen, PHP + SQLite tabanlı, mobil uyumlu ve admin panelli dijital menü sistemi.

Bu proje; tek bir görsel menü yerine, kategori ve ürün bazlı gerçek bir yönetim paneli sunar. Restoran, kafe ve bar/pub işletmeleri için hızlıca kurup kullanabileceğiniz şekilde tasarlanmıştır.

## İçindekiler

- Proje Özeti
- Öne Çıkan Özellikler
- Ekran Görüntüleri
- Teknoloji Altyapısı
- Sistem Gereksinimleri
- Kurulum
- İlk Kurulum Sonrası
- Admin Panel Kullanımı
- İndirim Sistemi
- Tema ve Görünüm Yönetimi
- SEO ve Site Ayarları
- QR Kod Kullanımı
- Dosya Yapısı
- Yedekleme ve Taşıma
- Güvenlik Önerileri
- Sık Karşılaşılan Sorunlar
- GitHub About Metni ve Anahtar Kelimeler

---

## Proje Özeti

Bu uygulama ile:

- Ürünlerinizi kategori bazlı listelersiniz
- Ürün görselleri, açıklaması, fiyatı ve sırası yönetilir
- Menü teması, renk şeması ve işletme tipi tek panelden değiştirilir
- Otomatik indirim kuralları tanımlanır
- SEO ayarları ve QR menü bağlantısı yönetilir

---

## Öne Çıkan Özellikler

- MySQL yok, SQLite ile çalışır
- Temiz URL yapısı (`.php` uzantısı görünmez)
- Mobil uyumlu menü
- 3 menü görünümü:
  - Satır listesi
  - Kart görünümü
  - Vitrin görünümü
- 5 renk şeması
- 3 işletme teması:
  - Restoran
  - Kafe
  - Bar/Pub
- Ürün ve kategori için sürükle-bırak kalıcı sıralama
- Otomatik indirim sistemi:
  - Süresiz indirim
  - Tarih aralığı indirimi
  - Haftalık gün+saat indirimi
- Menüde indirim badge gösterimi (`ribbon`, `pill`, `tag`)
- QR kod üretme ve PNG indirme
- SEO paneli:
  - Meta title, description, keywords, robots
  - Open Graph, Twitter card
  - Google/Bing doğrulama meta alanları
- Admin güvenliği:
  - `password_hash`
  - CSRF koruması
  - Brute-force giriş sınırlaması

---

## Ekran Görüntüleri

Aşağıdaki görselleri eklemek için ekran görüntülerini `public/assets/screenshots/` klasörüne koyup dosya adlarını aynı bırakabilirsiniz.

![Menü - Ana Sayfa](public/assets/screenshots/menu-home.png)
![Admin - Dashboard](public/assets/screenshots/admin-dashboard.png)
![Admin - İndirim Yönetimi](public/assets/screenshots/admin-discounts.png)
![Admin - Site Ayarları](public/assets/screenshots/admin-site-settings.png)

---

## Teknoloji Altyapısı

- PHP 8.2+ (önerilen PHP 8.3)
- SQLite (PDO)
- HTML / CSS / JavaScript
- Bootstrap 5 (admin arayüzü)

---

## Sistem Gereksinimleri

- PHP 8.2 veya üstü
- `pdo_sqlite` extension aktif
- Web sunucusu:
  - Apache / Nginx
  - veya PHP built-in server

---

## Kurulum

### 1. Projeyi klonlayın

```bash
git clone https://github.com/alikose35/restorant-cafe-bar-pub-menu-qr-menu.git
cd restorant-cafe-bar-pub-menu-qr-menu
```

### 2. Geliştirme ortamında hızlı çalıştırma (önerilen)

```bash
php -S localhost:8080 router.php
```

### 3. Tarayıcıdan açın

- Menü: `http://localhost:8080/`
- Admin panel: `http://localhost:8080/admin/login`

### 4. Varsayılan admin hesabı

- Kullanıcı adı: `admin`
- Şifre: `admin12345`

---

## İlk Kurulum Sonrası

1. Admin paneline giriş yapın
2. Hemen şifrenizi değiştirin (`Site Ayarları`)
3. Menü başlığı/alt başlığı girin
4. Tema, renk ve işletme tipini seçin
5. Kategori ve ürünleri ekleyin
6. İndirim kurallarını tanımlayın
7. QR kodu indirip masalara yerleştirin

---

## Admin Panel Kullanımı

### Menü Ayarları

- Menü başlığı
- Menü alt başlığı
- Görünüm teması seçimi
- Renk şeması
- İşletme tipi (restoran/kafe/bar)

### Kategori Yönetimi

- Kategori ekleme/güncelleme/silme
- Görsel oturma tipi (`cover`, `contain`)
- Sürükle-bırak kategori sıralama

### Ürün Yönetimi

- Ürün ekleme/güncelleme/silme
- Kategoriye bağlama
- Fiyat, açıklama, aktif/pasif
- Ürün görseli yükleme
- Kategori içinde sürükle-bırak ürün sıralama

---

## İndirim Sistemi

İndirimler ürün veya kategori bazlı atanabilir.

### Kural tipleri

- Her zaman aktif (süresiz)
- Belirli tarih aralığı
- Haftalık gün+saat aralığı

### Örnek kurallar

- “İçecek kategorisine %20 indirim, her gün”
- “Burger ürününe %30 indirim, 1-10 Haziran arası”
- “Tatlılarda %15 indirim, Pazartesi 18:30-23:00”

Sistem, aynı ürüne birden fazla kural denk gelirse en yüksek indirimi uygular.

---

## Tema ve Görünüm Yönetimi

### Menü görünümü

- Satır listesi: klasik menü görünümü
- Kart görünümü: e-ticaret kart stili
- Vitrin görünümü: daha görsel, premium akış

### İşletme tipi teması

- Restoran
- Kafe
- Bar/Pub

Her tema, menü arkasında farklı atmosfer görseliyle gelir.

---

## SEO ve Site Ayarları

Site Ayarları ekranından şunları yönetebilirsiniz:

- Meta title
- Meta description
- Meta keywords
- Robots etiketi
- Favicon URL
- OG image URL
- Twitter card
- Google site verification
- Bing verification

Ayrıca admin şifre değişikliği bu sayfadan yapılır.

---

## QR Kod Kullanımı

Admin panelde `Menu Public URL` alanına gerçek alan adınızı girin.

Örnek:

`https://menu.com/`

Sistem bu URL için QR kod üretir. QR kodu PNG olarak indirip:

- masa üstü kartlara
- duvar sticker'larına
- fiş altı baskılara

kullanabilirsiniz.

---

## Dosya Yapısı

```txt
.
├─ public/
│  ├─ index.php
│  ├─ .htaccess
│  ├─ assets/
│  │  ├─ css/
│  │  ├─ img/
│  │  ├─ js/
│  │  └─ screenshots/
│  └─ uploads/
├─ src/
│  ├─ Controllers/
│  ├─ Core/
│  ├─ Repositories/
│  └─ Security/
├─ views/
├─ storage/
├─ router.php
└─ README.md
```

---

## Yedekleme ve Taşıma

Taşıma/yedekleme için şu iki alan yeterlidir:

- `storage/menu.sqlite`
- `public/uploads/`

Not: `storage/menu.sqlite` dosyası repoya eklenmez. Uygulama ilk çalıştırmada veritabanını otomatik oluşturur.

---

## Güvenlik Önerileri

- Canlı ortamda HTTPS kullanın
- `SESSION_SECURE=true` ayarlayın
- Varsayılan admin şifresini hemen değiştirin
- Admin URL'ine sadece yetkili personelin erişmesini sağlayın
- Düzenli veritabanı yedeği alın

---

## Sık Karşılaşılan Sorunlar

### 1) `could not find driver` hatası

`pdo_sqlite` aktif değildir. PHP eklentisini açın.

### 2) Görsel yüklenmiyor

`public/uploads/` klasörünün yazma iznini kontrol edin.

### 3) Veri kaydedilmiyor

`storage/` klasörü yazılabilir olmalıdır.

### 4) Route çalışmıyor

Apache kullanıyorsanız `mod_rewrite` açık olmalı ve `public/.htaccess` aktif olmalı.

---

## GitHub About Metni ve Anahtar Kelimeler

### GitHub About (kopyala-yapıştır)

`PHP + SQLite tabanlı, admin panelli, mobil uyumlu dijital restoran/kafe/bar menü sistemi. QR kod, otomatik indirim, tema yönetimi ve SEO ayarları içerir.`

### Website

`https://menu.com` (kendi domaininizle değiştirin)

### Önerilen Topic Etiketleri

- php
- sqlite
- qr-menu
- restaurant-menu
- cafe-menu
- bar-menu
- admin-panel
- digital-menu
- seo
- bootstrap

---

## Lisans

Bu proje öğrenme, geliştirme ve ticari uyarlama amaçlı kullanılabilir.
İsterseniz depoya `MIT License` ekleyebilirsiniz.
