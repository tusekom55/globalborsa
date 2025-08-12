# 🚀 Global Borsa Trading Platform

Modüler ve ölçeklenebilir forex/kripto para trading platformu. PHP ile geliştirilmiş, modern web teknolojileri kullanılarak oluşturulmuş profesyonel bir trading çözümü.

## ✨ Özellikler

### 🏠 Ana Sayfa (Landing)
- Hero alanı ve istatistikler
- Öne çıkan semboller
- Top movers (en çok yükselen/düşen)
- Blog/duyuru özetleri
- Platform özellikleri

### 📊 Piyasalar
- Arama ve filtreleme
- Favoriler sistemi
- Canlı fiyat güncellemeleri
- Sembol detay sayfaları

### 📈 Trading Sistemi
- Market/Limit emir türleri
- Açık emirler yönetimi
- İşlem geçmişi
- Demo bakiye sistemi
- Canlı grafikler (TradingView entegrasyonu)

### 👤 Kullanıcı Yönetimi
- Kayıt/Giriş sistemi
- Profil yönetimi
- Bakiye takibi
- Dil/Tema tercihleri

### 📝 Blog/İçerik Yönetimi
- Blog yazıları
- Kategori sistemi
- Yazar yönetimi
- SEO dostu URL'ler

### ⚙️ Admin Panel
- Kullanıcı yönetimi
- Sembol yönetimi
- Borsa API ayarları
- İçerik yönetimi
- Site ayarları

### 🔧 Teknik Özellikler
- **Modüler Mimari**: Her özellik ayrı modül
- **Güvenlik**: CSRF koruması, brute-force koruması, form validasyonu
- **Çok Dil**: TR/EN desteği (genişletilebilir)
- **Tema Sistemi**: Koyu/Açık tema
- **Responsive**: Mobil uyumlu tasarım
- **Canlı Veri**: WebSocket/AJAX ile gerçek zamanlı güncellemeler

## 🛠️ Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- Apache/Nginx web sunucusu
- PDO MySQL extension

### Adım 1: Dosyaları İndirin
```bash
git clone https://github.com/tusekom55/turgis.git
cd turgis
```

### Adım 2: Veritabanı Ayarları
`config/config.php` dosyasındaki veritabanı ayarlarını düzenleyin:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'turgis_trading');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
```

### Adım 3: Veritabanını Oluşturun
MySQL'de yeni bir veritabanı oluşturun:

```sql
CREATE DATABASE turgis_trading CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### Adım 4: Kurulum Scriptini Çalıştırın
Tarayıcınızda `http://yourdomain.com/setup.php` adresine gidin ve kurulum adımlarını takip edin.

### Adım 5: Dizin İzinleri
Aşağıdaki dizinlerin yazılabilir olduğundan emin olun:
```bash
chmod 755 logs/
chmod 755 uploads/
```

## 🏗️ Proje Yapısı

```
turgis/
├── config/                 # Yapılandırma dosyaları
│   ├── config.php         # Ana yapılandırma
│   └── routes.php         # URL yönlendirmeleri
├── core/                   # Çekirdek sistem
│   ├── Database.php       # Veritabanı sınıfı
│   ├── Router.php         # URL yönlendirici
│   ├── Controller.php     # Temel controller
│   ├── Model.php          # Temel model
│   └── Security.php       # Güvenlik sınıfı
├── modules/                # Modüller
│   ├── landing/           # Ana sayfa modülü
│   ├── markets/           # Piyasalar modülü
│   ├── trading/           # Trading modülü
│   ├── accounts/          # Hesap yönetimi
│   ├── admin/             # Admin panel
│   ├── blog/              # Blog modülü
│   └── integrations/      # API entegrasyonları
├── shared/                 # Ortak bileşenler
│   ├── layouts/           # Layout dosyaları
│   ├── components/        # Yeniden kullanılabilir bileşenler
│   └── assets/            # CSS, JS, resimler
├── languages/              # Dil dosyaları
├── database/               # Veritabanı şemaları
├── logs/                   # Log dosyaları
└── uploads/                # Yüklenen dosyalar
```

## 🔧 Geliştirme

### Yeni Modül Ekleme

1. `modules/` dizininde yeni klasör oluşturun
2. Controller, Model ve View dosyalarını ekleyin
3. `config/routes.php` dosyasına route'ları ekleyin

Örnek modül yapısı:
```
modules/example/
├── ExampleController.php
├── ExampleModel.php
├── views/
│   └── index.php
└── assets/
    ├── example.css
    └── example.js
```

### Veritabanı Değişiklikleri

Yeni tablolar için `database/schema.sql` dosyasını güncelleyin veya migration scriptleri oluşturun.

### API Entegrasyonu

Yeni borsa API'leri için `modules/integrations/` dizininde adapter sınıfları oluşturun.

## 🔐 Güvenlik

### Varsayılan Admin Hesabı
- **E-posta**: admin@turgis.com
- **Şifre**: password

⚠️ **Önemli**: Üretim ortamında admin şifresini mutlaka değiştirin!

### Güvenlik Özellikleri
- CSRF token koruması
- SQL injection koruması (Prepared statements)
- XSS koruması
- Brute-force login koruması
- Güvenli session yönetimi
- Rate limiting

## 🌐 API Dokümantasyonu

### Fiyat Verileri
```
GET /api/prices
```

### Grafik Verileri
```
GET /api/chart/{symbol}?timeframe=1h&limit=100
```

### Emir Verme
```
POST /api/orders
{
    "symbol": "BTCUSDT",
    "side": "buy",
    "type": "market",
    "quantity": "0.001"
}
```

## 🎨 Tema Sistemi

### Yeni Tema Ekleme

1. `shared/assets/css/themes/` dizininde yeni CSS dosyası oluşturun
2. CSS değişkenlerini tanımlayın
3. `config/config.php` dosyasında tema seçeneklerine ekleyin

### CSS Değişkenleri
```css
:root {
    --primary-color: #007bff;
    --bg-primary: #ffffff;
    --text-primary: #212529;
    /* ... diğer değişkenler */
}
```

## 🌍 Çoklu Dil Desteği

### Yeni Dil Ekleme

1. `languages/` dizininde yeni JSON dosyası oluşturun
2. Çeviri anahtarlarını tanımlayın
3. Controller'larda `__()` fonksiyonunu kullanın

Örnek: `languages/en.json`
```json
{
    "welcome": "Welcome",
    "login": "Login",
    "register": "Register"
}
```

## 📊 Performans

### Optimizasyon İpuçları

1. **Veritabanı**: İndeksleri optimize edin
2. **Cache**: Redis/Memcached kullanın
3. **CDN**: Statik dosyalar için CDN kullanın
4. **Compression**: Gzip sıkıştırma aktif edin

### Monitoring

- Error logları: `logs/` dizini
- Performance monitoring: APM araçları
- Database monitoring: Slow query log

## 🚀 Deployment

### Production Checklist

- [ ] Debug mode'u kapatın (`DEBUG_MODE = false`)
- [ ] Admin şifresini değiştirin
- [ ] SSL sertifikası kurun
- [ ] Firewall ayarlarını yapın
- [ ] Backup stratejisi oluşturun
- [ ] Monitoring kurun

### Docker Deployment

```dockerfile
FROM php:8.1-apache
COPY . /var/www/html/
RUN docker-php-ext-install pdo pdo_mysql
```

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📝 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## 🆘 Destek

### Sorun Bildirme
GitHub Issues üzerinden sorun bildirebilirsiniz.

### Dokümantasyon
Detaylı dokümantasyon için [Wiki](https://github.com/tusekom55/turgis/wiki) sayfasını ziyaret edin.

### İletişim
- E-posta: support@turgis.com
- Discord: [Turgis Community](https://discord.gg/turgis)

## 🔄 Changelog

### v1.0.0 (2025-01-12)
- ✨ İlk sürüm
- 🏠 Landing page modülü
- 📊 Temel trading altyapısı
- 👤 Kullanıcı yönetimi
- ⚙️ Admin panel
- 🔐 Güvenlik özellikleri

## 🎯 Roadmap

### v1.1.0
- [ ] Markets modülü
- [ ] Gelişmiş grafik sistemi
- [ ] WebSocket entegrasyonu

### v1.2.0
- [ ] Trading modülü
- [ ] Order management
- [ ] Portfolio tracking

### v2.0.0
- [ ] Mobile app
- [ ] Advanced analytics
- [ ] Social trading

---

**Turgis Trading Platform** - Profesyonel forex ve kripto para trading çözümü 🚀
