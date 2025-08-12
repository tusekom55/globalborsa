/**
 * Landing Page JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    initializeLandingPage();
});

function initializeLandingPage() {
    // Initialize symbol cards interactions
    initializeSymbolCards();
    
    // Initialize price animations
    initializePriceAnimations();
    
    // Initialize scroll animations
    initializeScrollAnimations();
    
    console.log('Landing page initialized');
}

function initializeSymbolCards() {
    const symbolCards = document.querySelectorAll('.symbol-card');
    
    symbolCards.forEach(card => {
        // Add hover effects
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-4px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
        
        // Handle click events
        card.addEventListener('click', function(e) {
            // Don't navigate if clicking on action buttons
            if (e.target.closest('.symbol-actions')) {
                e.stopPropagation();
                return;
            }
            
            const symbol = this.querySelector('[data-symbol]')?.getAttribute('data-symbol');
            if (symbol) {
                window.location.href = `/symbol/${symbol}`;
            }
        });
    });
}

function initializePriceAnimations() {
    // Override the global price update function for landing page
    const originalUpdatePriceElements = window.updatePriceElements;
    
    if (originalUpdatePriceElements) {
        window.updatePriceElements = function(prices) {
            originalUpdatePriceElements(prices);
            
            // Add landing page specific animations
            prices.forEach(price => {
                const symbolCards = document.querySelectorAll(`[data-symbol="${price.symbol}"]`);
                symbolCards.forEach(card => {
                    const symbolCard = card.closest('.symbol-card');
                    if (symbolCard) {
                        // Add pulse animation on price update
                        symbolCard.classList.add('price-updated');
                        setTimeout(() => {
                            symbolCard.classList.remove('price-updated');
                        }, 1000);
                    }
                });
            });
        };
    }
}

function initializeScrollAnimations() {
    // Intersection Observer for scroll animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);
    
    // Observe elements for animation
    const animateElements = document.querySelectorAll(
        '.symbol-card, .feature-card, .post-card, .stat-card, .mover-item'
    );
    
    animateElements.forEach(el => {
        el.classList.add('animate-on-scroll');
        observer.observe(el);
    });
}

// Add favorite functionality
function toggleFavorite(symbolId, element) {
    if (!window.csrfToken) {
        showNotification('Favorilere eklemek için giriş yapın', 'warning');
        return;
    }
    
    const icon = element.querySelector('i');
    const isFavorited = icon.classList.contains('fas');
    
    // Optimistic UI update
    if (isFavorited) {
        icon.classList.remove('fas');
        icon.classList.add('far');
        element.title = 'Favorilere Ekle';
    } else {
        icon.classList.remove('far');
        icon.classList.add('fas');
        element.title = 'Favorilerden Çıkar';
    }
    
    // Send request to server
    fetch('/api/favorites', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': window.csrfToken
        },
        body: JSON.stringify({
            symbol_id: symbolId,
            action: isFavorited ? 'remove' : 'add'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(
                isFavorited ? 'Favorilerden çıkarıldı' : 'Favorilere eklendi',
                'success'
            );
        } else {
            // Revert UI change on error
            if (isFavorited) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                element.title = 'Favorilerden Çıkar';
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                element.title = 'Favorilere Ekle';
            }
            showNotification(data.message || 'Bir hata oluştu', 'error');
        }
    })
    .catch(error => {
        console.error('Favorite toggle error:', error);
        // Revert UI change on error
        if (isFavorited) {
            icon.classList.remove('far');
            icon.classList.add('fas');
            element.title = 'Favorilerden Çıkar';
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
            element.title = 'Favorilere Ekle';
        }
        showNotification('Bağlantı hatası', 'error');
    });
}

// Quick trade functionality
function quickTrade(symbol, side) {
    if (!window.csrfToken) {
        window.location.href = '/login';
        return;
    }
    
    // Redirect to trading page with pre-selected symbol and side
    window.location.href = `/trading/${symbol}?side=${side}`;
}

// Export functions for global use
window.toggleFavorite = toggleFavorite;
window.quickTrade = quickTrade;
