/**
 * Turgis Trading Platform - Main JavaScript
 */

// Global variables
window.TurgisApp = {
    config: {
        apiUrl: '/api',
        wsUrl: 'ws://localhost:8080',
        updateInterval: 1000
    },
    user: null,
    theme: 'dark',
    language: 'tr',
    priceUpdateInterval: null,
    websocket: null
};

// Initialize app when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

/**
 * Initialize the application
 */
function initializeApp() {
    // Set initial theme
    const savedTheme = localStorage.getItem('theme') || window.currentTheme || 'dark';
    setTheme(savedTheme);
    
    // Set initial language
    const savedLanguage = localStorage.getItem('language') || window.currentLanguage || 'tr';
    setLanguage(savedLanguage);
    
    // Initialize components
    initializeDropdowns();
    initializeMobileMenu();
    initializeFlashMessages();
    initializeForms();
    
    // Start price updates if on trading pages
    if (isOnTradingPage()) {
        startPriceUpdates();
    }
    
    // Initialize WebSocket connection
    initializeWebSocket();
    
    console.log('Turgis Trading Platform initialized');
}

/**
 * Theme Management
 */
function toggleTheme() {
    const currentTheme = document.body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    setTheme(newTheme);
}

function setTheme(theme) {
    document.body.setAttribute('data-theme', theme);
    document.body.className = document.body.className.replace(/theme-\w+/, `theme-${theme}`);
    
    // Update theme icon
    const themeIcon = document.getElementById('theme-icon');
    if (themeIcon) {
        themeIcon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
    
    // Save to localStorage and session
    localStorage.setItem('theme', theme);
    window.TurgisApp.theme = theme;
    
    // Send to server
    fetch('/api/theme', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': window.csrfToken
        },
        body: JSON.stringify({ theme: theme })
    }).catch(console.error);
}

/**
 * Language Management
 */
function setLanguage(language) {
    window.TurgisApp.language = language;
    localStorage.setItem('language', language);
    
    // Send to server and reload page
    fetch('/api/language', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': window.csrfToken
        },
        body: JSON.stringify({ language: language })
    }).then(() => {
        window.location.reload();
    }).catch(console.error);
}

/**
 * Mobile Menu
 */
function toggleMobileMenu() {
    const navbar = document.querySelector('.navbar-menu');
    const toggle = document.querySelector('.navbar-toggle');
    
    if (navbar && toggle) {
        navbar.classList.toggle('active');
        toggle.classList.toggle('active');
    }
}

function initializeMobileMenu() {
    // Close mobile menu when clicking outside
    document.addEventListener('click', function(e) {
        const navbar = document.querySelector('.navbar-menu');
        const toggle = document.querySelector('.navbar-toggle');
        
        if (navbar && toggle && navbar.classList.contains('active')) {
            if (!navbar.contains(e.target) && !toggle.contains(e.target)) {
                navbar.classList.remove('active');
                toggle.classList.remove('active');
            }
        }
    });
}

/**
 * Dropdown Management
 */
function initializeDropdowns() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown');
        dropdowns.forEach(dropdown => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove('active');
            }
        });
    });
    
    // Toggle dropdowns on click
    document.querySelectorAll('.dropdown-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = this.closest('.dropdown');
            const isActive = dropdown.classList.contains('active');
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown').forEach(d => d.classList.remove('active'));
            
            // Toggle current dropdown
            if (!isActive) {
                dropdown.classList.add('active');
            }
        });
    });
}

/**
 * Flash Messages
 */
function initializeFlashMessages() {
    // Auto-hide flash messages after 5 seconds
    setTimeout(() => {
        const flashMessages = document.querySelectorAll('.flash-messages .alert');
        flashMessages.forEach(message => {
            message.style.opacity = '0';
            setTimeout(() => message.remove(), 300);
        });
    }, 5000);
}

/**
 * Form Enhancements
 */
function initializeForms() {
    // Add loading state to form submissions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> İşleniyor...';
            }
        });
    });
    
    // Real-time form validation
    document.querySelectorAll('.form-control').forEach(input => {
        input.addEventListener('blur', validateField);
        input.addEventListener('input', clearFieldError);
    });
}

function validateField(e) {
    const field = e.target;
    const value = field.value.trim();
    const rules = field.getAttribute('data-rules');
    
    if (!rules) return;
    
    const ruleList = rules.split('|');
    let error = null;
    
    for (const rule of ruleList) {
        if (rule === 'required' && !value) {
            error = 'Bu alan zorunludur';
            break;
        }
        
        if (rule === 'email' && value && !isValidEmail(value)) {
            error = 'Geçerli bir e-posta adresi giriniz';
            break;
        }
        
        if (rule.startsWith('min:')) {
            const min = parseInt(rule.split(':')[1]);
            if (value && value.length < min) {
                error = `En az ${min} karakter olmalıdır`;
                break;
            }
        }
        
        if (rule.startsWith('max:')) {
            const max = parseInt(rule.split(':')[1]);
            if (value && value.length > max) {
                error = `En fazla ${max} karakter olmalıdır`;
                break;
            }
        }
    }
    
    showFieldError(field, error);
}

function clearFieldError(e) {
    const field = e.target;
    showFieldError(field, null);
}

function showFieldError(field, error) {
    // Remove existing error
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
    
    // Add new error if exists
    if (error) {
        const errorElement = document.createElement('div');
        errorElement.className = 'field-error text-danger mt-1';
        errorElement.textContent = error;
        field.parentNode.appendChild(errorElement);
        field.classList.add('is-invalid');
    } else {
        field.classList.remove('is-invalid');
    }
}

/**
 * Price Updates
 */
function isOnTradingPage() {
    return window.location.pathname.includes('/trading') || 
           window.location.pathname.includes('/markets') ||
           window.location.pathname === '/';
}

function startPriceUpdates() {
    if (window.TurgisApp.priceUpdateInterval) {
        clearInterval(window.TurgisApp.priceUpdateInterval);
    }
    
    window.TurgisApp.priceUpdateInterval = setInterval(updatePrices, window.TurgisApp.config.updateInterval);
    
    // Initial update
    updatePrices();
}

function stopPriceUpdates() {
    if (window.TurgisApp.priceUpdateInterval) {
        clearInterval(window.TurgisApp.priceUpdateInterval);
        window.TurgisApp.priceUpdateInterval = null;
    }
}

async function updatePrices() {
    try {
        const response = await fetch('/api/prices');
        const data = await response.json();
        
        if (data.success) {
            updatePriceElements(data.prices);
        }
    } catch (error) {
        console.error('Price update failed:', error);
    }
}

function updatePriceElements(prices) {
    prices.forEach(price => {
        const elements = document.querySelectorAll(`[data-symbol="${price.symbol}"]`);
        
        elements.forEach(element => {
            const currentPrice = parseFloat(element.textContent.replace(/[^\d.-]/g, ''));
            const newPrice = parseFloat(price.price);
            
            // Update price
            element.textContent = formatPrice(newPrice, price.precision);
            
            // Add price change animation
            if (currentPrice !== newPrice) {
                element.classList.remove('price-up', 'price-down');
                
                if (newPrice > currentPrice) {
                    element.classList.add('price-up');
                } else if (newPrice < currentPrice) {
                    element.classList.add('price-down');
                }
                
                // Remove animation class after animation
                setTimeout(() => {
                    element.classList.remove('price-up', 'price-down');
                }, 1000);
            }
        });
        
        // Update change percentage
        const changeElements = document.querySelectorAll(`[data-change="${price.symbol}"]`);
        changeElements.forEach(element => {
            const change = parseFloat(price.change_percent_24h);
            element.textContent = (change >= 0 ? '+' : '') + change.toFixed(2) + '%';
            element.className = change >= 0 ? 'text-success' : 'text-danger';
        });
    });
}

/**
 * WebSocket Connection
 */
function initializeWebSocket() {
    if (!window.WebSocket) {
        console.warn('WebSocket not supported');
        return;
    }
    
    try {
        window.TurgisApp.websocket = new WebSocket(window.TurgisApp.config.wsUrl);
        
        window.TurgisApp.websocket.onopen = function() {
            console.log('WebSocket connected');
        };
        
        window.TurgisApp.websocket.onmessage = function(event) {
            const data = JSON.parse(event.data);
            handleWebSocketMessage(data);
        };
        
        window.TurgisApp.websocket.onclose = function() {
            console.log('WebSocket disconnected');
            // Reconnect after 5 seconds
            setTimeout(initializeWebSocket, 5000);
        };
        
        window.TurgisApp.websocket.onerror = function(error) {
            console.error('WebSocket error:', error);
        };
    } catch (error) {
        console.warn('WebSocket connection failed:', error);
    }
}

function handleWebSocketMessage(data) {
    switch (data.type) {
        case 'price_update':
            updatePriceElements([data.data]);
            break;
        case 'order_update':
            handleOrderUpdate(data.data);
            break;
        case 'trade_update':
            handleTradeUpdate(data.data);
            break;
        default:
            console.log('Unknown WebSocket message:', data);
    }
}

/**
 * Utility Functions
 */
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function formatPrice(price, precision = 8) {
    return parseFloat(price).toFixed(precision).replace(/\.?0+$/, '');
}

function formatNumber(number, decimals = 2) {
    return new Intl.NumberFormat('tr-TR', {
        minimumFractionDigits: decimals,
        maximumFractionDigits: decimals
    }).format(number);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible`;
    notification.innerHTML = `
        <span>${message}</span>
        <button class="alert-close" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    const container = document.querySelector('.flash-messages') || document.body;
    container.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Panoya kopyalandı', 'success');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Panoya kopyalandı', 'success');
    }
}

/**
 * AJAX Helper Functions
 */
async function apiRequest(url, options = {}) {
    const defaultOptions = {
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-Token': window.csrfToken
        }
    };
    
    const mergedOptions = { ...defaultOptions, ...options };
    
    try {
        const response = await fetch(url, mergedOptions);
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.message || 'Request failed');
        }
        
        return data;
    } catch (error) {
        console.error('API request failed:', error);
        throw error;
    }
}

// Export functions for global use
window.toggleTheme = toggleTheme;
window.setTheme = setTheme;
window.setLanguage = setLanguage;
window.toggleMobileMenu = toggleMobileMenu;
window.showNotification = showNotification;
window.copyToClipboard = copyToClipboard;
window.apiRequest = apiRequest;
window.formatPrice = formatPrice;
window.formatNumber = formatNumber;
