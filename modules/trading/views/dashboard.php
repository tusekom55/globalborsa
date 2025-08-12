<div class="trading-container">
    <!-- Trading Header -->
    <div class="trading-header">
        <div class="header-left">
            <h1 class="page-title">
                <i class="fas fa-chart-line"></i>
                Trading Dashboard
            </h1>
            <p class="page-subtitle">Demo hesap ile risk almadan trading yapın</p>
        </div>
        <div class="header-right">
            <div class="balance-card">
                <div class="balance-label">Demo Bakiye</div>
                <div class="balance-amount">$10,000.00</div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="quick-stats">
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-wallet"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">$10,000.00</div>
                <div class="stat-label">Toplam Bakiye</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">+$250.00</div>
                <div class="stat-label">Günlük P&L</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">3</div>
                <div class="stat-label">Açık Emirler</div>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon">
                <i class="fas fa-history"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value">12</div>
                <div class="stat-label">Bugünkü İşlemler</div>
            </div>
        </div>
    </div>

    <!-- Main Trading Content -->
    <div class="trading-content">
        <!-- Left Panel - Market Overview -->
        <div class="left-panel">
            <div class="panel-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-chart-bar"></i>
                        Piyasa Özeti
                    </h3>
                    <a href="/markets" class="btn btn-outline btn-sm">
                        <i class="fas fa-external-link-alt"></i>
                        Tümünü Gör
                    </a>
                </div>
                <div class="card-body">
                    <div class="market-list">
                        <div class="market-item" onclick="window.location.href='/trading/BTCUSDT'">
                            <div class="market-symbol">
                                <span class="symbol">BTCUSDT</span>
                                <span class="name">Bitcoin / Tether</span>
                            </div>
                            <div class="market-price">
                                <span class="price">$45,000.00</span>
                                <span class="change positive">+2.5%</span>
                            </div>
                        </div>
                        <div class="market-item" onclick="window.location.href='/trading/ETHUSDT'">
                            <div class="market-symbol">
                                <span class="symbol">ETHUSDT</span>
                                <span class="name">Ethereum / Tether</span>
                            </div>
                            <div class="market-price">
                                <span class="price">$3,200.00</span>
                                <span class="change negative">-1.2%</span>
                            </div>
                        </div>
                        <div class="market-item" onclick="window.location.href='/trading/EURUSD'">
                            <div class="market-symbol">
                                <span class="symbol">EURUSD</span>
                                <span class="name">Euro / US Dollar</span>
                            </div>
                            <div class="market-price">
                                <span class="price">1.0850</span>
                                <span class="change positive">+0.3%</span>
                            </div>
                        </div>
                        <div class="market-item" onclick="window.location.href='/trading/GBPUSD'">
                            <div class="market-symbol">
                                <span class="symbol">GBPUSD</span>
                                <span class="name">British Pound / US Dollar</span>
                            </div>
                            <div class="market-price">
                                <span class="price">1.2650</span>
                                <span class="change negative">-0.5%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Trade Panel -->
            <div class="panel-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-bolt"></i>
                        Hızlı İşlem
                    </h3>
                </div>
                <div class="card-body">
                    <form class="quick-trade-form" id="quickTradeForm">
                        <div class="form-group">
                            <label class="form-label">Sembol</label>
                            <select class="form-control" name="symbol" required>
                                <option value="">Sembol Seçin</option>
                                <option value="BTCUSDT">BTCUSDT</option>
                                <option value="ETHUSDT">ETHUSDT</option>
                                <option value="EURUSD">EURUSD</option>
                                <option value="GBPUSD">GBPUSD</option>
                            </select>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">Miktar</label>
                                <input type="number" class="form-control" name="quantity" step="0.001" min="0.001" placeholder="0.001" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tür</label>
                                <select class="form-control" name="type">
                                    <option value="market">Market</option>
                                    <option value="limit">Limit</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group limit-price" style="display: none;">
                            <label class="form-label">Fiyat</label>
                            <input type="number" class="form-control" name="price" step="0.01" placeholder="0.00">
                        </div>
                        <div class="trade-buttons">
                            <button type="submit" class="btn btn-success" name="side" value="buy">
                                <i class="fas fa-arrow-up"></i>
                                Al
                            </button>
                            <button type="submit" class="btn btn-danger" name="side" value="sell">
                                <i class="fas fa-arrow-down"></i>
                                Sat
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Panel - Orders & Portfolio -->
        <div class="right-panel">
            <!-- Open Orders -->
            <div class="panel-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-list"></i>
                        Açık Emirler
                    </h3>
                    <a href="/orders" class="btn btn-outline btn-sm">
                        <i class="fas fa-external-link-alt"></i>
                        Tümünü Gör
                    </a>
                </div>
                <div class="card-body">
                    <div class="orders-list" id="openOrdersList">
                        <div class="loading-state">
                            <i class="fas fa-spinner fa-spin"></i>
                            Emirler yükleniyor...
                        </div>
                    </div>
                </div>
            </div>

            <!-- Portfolio Summary -->
            <div class="panel-card">
                <div class="card-header">
                    <h3>
                        <i class="fas fa-briefcase"></i>
                        Portfolio Özeti
                    </h3>
                    <a href="/portfolio" class="btn btn-outline btn-sm">
                        <i class="fas fa-external-link-alt"></i>
                        Detay
                    </a>
                </div>
                <div class="card-body">
                    <div class="portfolio-summary">
                        <div class="portfolio-item">
                            <div class="asset-info">
                                <span class="asset-symbol">USD</span>
                                <span class="asset-name">US Dollar</span>
                            </div>
                            <div class="asset-balance">
                                <span class="balance">$9,750.00</span>
                                <span class="percentage">97.5%</span>
                            </div>
                        </div>
                        <div class="portfolio-item">
                            <div class="asset-info">
                                <span class="asset-symbol">BTC</span>
                                <span class="asset-name">Bitcoin</span>
                            </div>
                            <div class="asset-balance">
                                <span class="balance">0.00556 BTC</span>
                                <span class="percentage">2.5%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trading Actions -->
    <div class="trading-actions">
        <a href="/markets" class="action-card">
            <div class="action-icon">
                <i class="fas fa-chart-bar"></i>
            </div>
            <div class="action-content">
                <h4>Piyasalar</h4>
                <p>Tüm sembolleri inceleyin</p>
            </div>
        </a>
        <a href="/orders" class="action-card">
            <div class="action-icon">
                <i class="fas fa-list"></i>
            </div>
            <div class="action-content">
                <h4>Emirlerim</h4>
                <p>Açık ve geçmiş emirler</p>
            </div>
        </a>
        <a href="/portfolio" class="action-card">
            <div class="action-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <div class="action-content">
                <h4>Portfolio</h4>
                <p>Varlık dağılımı ve performans</p>
            </div>
        </a>
        <a href="/profile" class="action-card">
            <div class="action-icon">
                <i class="fas fa-user"></i>
            </div>
            <div class="action-content">
                <h4>Hesabım</h4>
                <p>Profil ve ayarlar</p>
            </div>
        </a>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Load open orders
    loadOpenOrders();
    
    // Quick trade form handling
    const quickTradeForm = document.getElementById('quickTradeForm');
    const typeSelect = quickTradeForm.querySelector('select[name="type"]');
    const limitPriceGroup = quickTradeForm.querySelector('.limit-price');
    
    typeSelect.addEventListener('change', function() {
        if (this.value === 'limit') {
            limitPriceGroup.style.display = 'block';
            limitPriceGroup.querySelector('input').required = true;
        } else {
            limitPriceGroup.style.display = 'none';
            limitPriceGroup.querySelector('input').required = false;
        }
    });
    
    quickTradeForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        const clickedButton = e.submitter;
        formData.append('side', clickedButton.value);
        
        // Add loading state
        clickedButton.classList.add('loading');
        clickedButton.disabled = true;
        
        fetch('/api/orders', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Emir başarıyla oluşturuldu!', 'success');
                quickTradeForm.reset();
                loadOpenOrders(); // Refresh orders list
            } else {
                showNotification(data.message || 'Emir oluşturulamadı', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Bir hata oluştu', 'error');
        })
        .finally(() => {
            clickedButton.classList.remove('loading');
            clickedButton.disabled = false;
        });
    });
});

function loadOpenOrders() {
    const ordersList = document.getElementById('openOrdersList');
    
    fetch('/api/orders')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayOrders(data.orders);
            } else {
                ordersList.innerHTML = '<div class="empty-state">Açık emir bulunamadı</div>';
            }
        })
        .catch(error => {
            console.error('Error loading orders:', error);
            ordersList.innerHTML = '<div class="error-state">Emirler yüklenemedi</div>';
        });
}

function displayOrders(orders) {
    const ordersList = document.getElementById('openOrdersList');
    
    if (orders.length === 0) {
        ordersList.innerHTML = '<div class="empty-state">Açık emir bulunamadı</div>';
        return;
    }
    
    const ordersHtml = orders.map(order => `
        <div class="order-item">
            <div class="order-header">
                <span class="order-symbol">${order.symbol}</span>
                <span class="order-side ${order.side}">${order.side === 'buy' ? 'AL' : 'SAT'}</span>
            </div>
            <div class="order-details">
                <div class="order-info">
                    <span class="label">Miktar:</span>
                    <span class="value">${order.quantity}</span>
                </div>
                <div class="order-info">
                    <span class="label">Fiyat:</span>
                    <span class="value">$${order.price.toFixed(2)}</span>
                </div>
                <div class="order-info">
                    <span class="label">Durum:</span>
                    <span class="value status-${order.status}">${getStatusText(order.status)}</span>
                </div>
            </div>
            ${order.status === 'pending' ? `
                <button class="btn btn-danger btn-sm" onclick="cancelOrder('${order.id}')">
                    <i class="fas fa-times"></i>
                    İptal
                </button>
            ` : ''}
        </div>
    `).join('');
    
    ordersList.innerHTML = ordersHtml;
}

function getStatusText(status) {
    const statusMap = {
        'pending': 'Bekliyor',
        'filled': 'Gerçekleşti',
        'cancelled': 'İptal Edildi',
        'partial': 'Kısmi'
    };
    return statusMap[status] || status;
}

function cancelOrder(orderId) {
    if (!confirm('Bu emri iptal etmek istediğinizden emin misiniz?')) {
        return;
    }
    
    fetch(`/api/orders/${orderId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Emir başarıyla iptal edildi', 'success');
            loadOpenOrders();
        } else {
            showNotification(data.message || 'Emir iptal edilemedi', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Bir hata oluştu', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'exclamation-triangle' : 'info'}"></i>
        <span>${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to page
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}
</script>
