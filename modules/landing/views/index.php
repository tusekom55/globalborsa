<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    <span class="text-primary">Global Borsa</span> ile 
                    Dünya Piyasalarında İşlem Yapın
                </h1>
                <p class="hero-description">
                    Forex, kripto para ve hisse senedi piyasalarında profesyonel trading deneyimi. 
                    Gelişmiş analiz araçları ve güvenli platform ile başarıya ulaşın.
                </p>
                <div class="hero-actions">
                    <?php if (!$user): ?>
                        <a href="/register" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus"></i>
                            Ücretsiz Hesap Aç
                        </a>
                        <a href="/trading" class="btn btn-outline btn-lg">
                            <i class="fas fa-chart-line"></i>
                            Demo Hesap Dene
                        </a>
                    <?php else: ?>
                        <a href="/trading" class="btn btn-primary btn-lg">
                            <i class="fas fa-chart-line"></i>
                            Trading'e Başla
                        </a>
                        <a href="/markets" class="btn btn-outline btn-lg">
                            <i class="fas fa-list"></i>
                            Piyasaları İncele
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="hero-stats">
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($market_stats['total_symbols']) ?></div>
                    <div class="stat-label">Aktif Sembol</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">$<?= number_format($market_stats['total_volume'], 0) ?></div>
                    <div class="stat-label">24s Hacim</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value"><?= number_format($market_stats['active_users']) ?></div>
                    <div class="stat-label">Aktif Kullanıcı</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value">$<?= number_format($market_stats['market_cap'], 0) ?></div>
                    <div class="stat-label">Piyasa Değeri</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Symbols Section -->
<section class="featured-symbols">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Öne Çıkan Semboller</h2>
            <p class="section-description">En popüler forex ve kripto para çiftleri</p>
        </div>
        
        <div class="symbols-grid">
            <?php foreach ($featured_symbols as $symbol): ?>
                <div class="symbol-card" onclick="window.location.href='/symbol/<?= $symbol['symbol'] ?>'">
                    <div class="symbol-header">
                        <div class="symbol-info">
                            <h3 class="symbol-name"><?= htmlspecialchars($symbol['symbol']) ?></h3>
                            <p class="symbol-description"><?= htmlspecialchars($symbol['name']) ?></p>
                        </div>
                        <div class="symbol-type">
                            <span class="type-badge type-<?= $symbol['type'] ?>">
                                <?= strtoupper($symbol['type']) ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="symbol-price">
                        <div class="current-price" data-symbol="<?= $symbol['symbol'] ?>">
                            <?= $symbol['price'] ? number_format($symbol['price'], 8) : '--' ?>
                        </div>
                        <div class="price-change <?= ($symbol['change_percent_24h'] ?? 0) >= 0 ? 'positive' : 'negative' ?>">
                            <i class="fas fa-<?= ($symbol['change_percent_24h'] ?? 0) >= 0 ? 'arrow-up' : 'arrow-down' ?>"></i>
                            <span data-change="<?= $symbol['symbol'] ?>">
                                <?= $symbol['change_percent_24h'] ? (($symbol['change_percent_24h'] >= 0 ? '+' : '') . number_format($symbol['change_percent_24h'], 2) . '%') : '--' ?>
                            </span>
                        </div>
                    </div>
                    
                    <div class="symbol-details">
                        <div class="detail-item">
                            <span class="label">24s Hacim:</span>
                            <span class="value"><?= $symbol['volume_24h'] ? number_format($symbol['volume_24h'], 2) : '--' ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Tip:</span>
                            <span class="value"><?= strtoupper($symbol['type']) ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Durum:</span>
                            <span class="value"><?= $symbol['status'] === 'active' ? 'Aktif' : 'Pasif' ?></span>
                        </div>
                    </div>
                    
                    <div class="symbol-actions">
                        <a href="/trading/<?= $symbol['symbol'] ?>" class="btn btn-success btn-sm">
                            <i class="fas fa-arrow-up"></i> Al
                        </a>
                        <a href="/trading/<?= $symbol['symbol'] ?>" class="btn btn-danger btn-sm">
                            <i class="fas fa-arrow-down"></i> Sat
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="section-footer">
            <a href="/markets" class="btn btn-outline">
                <i class="fas fa-list"></i>
                Tüm Piyasaları Görüntüle
            </a>
        </div>
    </div>
</section>

<!-- Top Movers Section -->
<section class="top-movers">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Günün Hareketlileri</h2>
            <p class="section-description">En çok yükselen ve düşen semboller</p>
        </div>
        
        <div class="movers-container">
            <!-- Gainers -->
            <div class="movers-section">
                <h3 class="movers-title text-success">
                    <i class="fas fa-arrow-up"></i>
                    En Çok Yükselenler
                </h3>
                <div class="movers-list">
                    <?php foreach ($top_movers['gainers'] as $gainer): ?>
                        <div class="mover-item" onclick="window.location.href='/symbol/<?= $gainer['symbol'] ?>'">
                            <div class="mover-symbol">
                                <span class="symbol"><?= htmlspecialchars($gainer['symbol']) ?></span>
                                <span class="name"><?= htmlspecialchars($gainer['name']) ?></span>
                            </div>
                            <div class="mover-price">
                                <span class="price"><?= number_format($gainer['price'], 8) ?></span>
                                <span class="change text-success">
                                    +<?= number_format($gainer['change_percent_24h'], 2) ?>%
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Losers -->
            <div class="movers-section">
                <h3 class="movers-title text-danger">
                    <i class="fas fa-arrow-down"></i>
                    En Çok Düşenler
                </h3>
                <div class="movers-list">
                    <?php foreach ($top_movers['losers'] as $loser): ?>
                        <div class="mover-item" onclick="window.location.href='/symbol/<?= $loser['symbol'] ?>'">
                            <div class="mover-symbol">
                                <span class="symbol"><?= htmlspecialchars($loser['symbol']) ?></span>
                                <span class="name"><?= htmlspecialchars($loser['name']) ?></span>
                            </div>
                            <div class="mover-price">
                                <span class="price"><?= number_format($loser['price'], 8) ?></span>
                                <span class="change text-danger">
                                    <?= number_format($loser['change_percent_24h'], 2) ?>%
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Platform Özellikleri</h2>
            <p class="section-description">Profesyonel trading için ihtiyacınız olan her şey</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Canlı Grafikler</h3>
                <p class="feature-description">
                    Gerçek zamanlı fiyat hareketleri, teknik indikatörler ve gelişmiş analiz araçları
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h3 class="feature-title">Güvenli Platform</h3>
                <p class="feature-description">
                    SSL şifreleme, 2FA doğrulama ve gelişmiş güvenlik önlemleri ile korumalı işlemler
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>
                <h3 class="feature-title">Mobil Uyumlu</h3>
                <p class="feature-description">
                    Her cihazda mükemmel deneyim sunan responsive tasarım ve mobil optimizasyon
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <h3 class="feature-title">7/24 Trading</h3>
                <p class="feature-description">
                    Kripto piyasalarında kesintisiz, forex piyasalarında hafta içi 24 saat işlem
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <h3 class="feature-title">Eğitim Merkezi</h3>
                <p class="feature-description">
                    Trading eğitimleri, piyasa analizleri ve uzman görüşleri ile bilginizi artırın
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="feature-title">7/24 Destek</h3>
                <p class="feature-description">
                    Uzman destek ekibimiz her zaman yanınızda, canlı chat ve telefon desteği
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Latest Blog Posts -->
<?php if (!empty($latest_posts)): ?>
<section class="latest-posts">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Son Haberler ve Analizler</h2>
            <p class="section-description">Piyasa haberleri, teknik analizler ve trading ipuçları</p>
        </div>
        
        <div class="posts-grid">
            <?php foreach (array_slice($latest_posts, 0, 3) as $post): ?>
                <article class="post-card" onclick="window.location.href='/blog/<?= $post['slug'] ?>'">
                    <?php if ($post['featured_image']): ?>
                        <div class="post-image">
                            <img src="<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
                        </div>
                    <?php endif; ?>
                    
                    <div class="post-content">
                        <div class="post-meta">
                            <span class="post-category category-<?= $post['category'] ?>">
                                <?= ucfirst($post['category']) ?>
                            </span>
                            <span class="post-date">
                                <i class="fas fa-calendar"></i>
                                <?= date('d.m.Y', strtotime($post['published_at'])) ?>
                            </span>
                        </div>
                        
                        <h3 class="post-title"><?= htmlspecialchars($post['title']) ?></h3>
                        
                        <?php if ($post['excerpt']): ?>
                            <p class="post-excerpt"><?= htmlspecialchars($post['excerpt']) ?></p>
                        <?php endif; ?>
                        
                        <div class="post-footer">
                            <span class="post-author">
                                <i class="fas fa-user"></i>
                                <?= htmlspecialchars($post['author_name']) ?>
                            </span>
                            <span class="post-views">
                                <i class="fas fa-eye"></i>
                                <?= number_format($post['views']) ?>
                            </span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <div class="section-footer">
            <a href="/blog" class="btn btn-outline">
                <i class="fas fa-newspaper"></i>
                Tüm Haberleri Görüntüle
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- CTA Section -->
<section class="cta">
    <div class="container">
        <div class="cta-content">
            <h2 class="cta-title">Trading Yolculuğunuza Bugün Başlayın</h2>
            <p class="cta-description">
                Ücretsiz demo hesabınızı açın ve risk almadan platform özelliklerini keşfedin
            </p>
            <div class="cta-actions">
                <?php if (!$user): ?>
                    <a href="/register" class="btn btn-primary btn-lg">
                        <i class="fas fa-rocket"></i>
                        Hemen Başla
                    </a>
                    <a href="/trading" class="btn btn-outline btn-lg">
                        <i class="fas fa-play"></i>
                        Demo Dene
                    </a>
                <?php else: ?>
                    <a href="/trading" class="btn btn-primary btn-lg">
                        <i class="fas fa-chart-line"></i>
                        Trading Paneline Git
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
