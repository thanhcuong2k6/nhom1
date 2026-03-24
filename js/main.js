/**
 * FashionHub - JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // Initialize
    initializeApp();
});

function initializeApp() {
    // Add event listeners
    console.log('FashionHub app initialized');
}

// Utility Functions
function formatCurrency(amount) {
    return new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND'
    }).format(amount);
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.remove();
    }, 3000);
}

function sanitizeInput(input) {
    const div = document.createElement('div');
    div.textContent = input;
    return div.innerHTML;
}

// Product Functions
function addToCart(productId, productName, price) {
    const quantity = document.getElementById('quantity')?.value || 1;

    fetch('/api/add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            product_name: productName,
            price: price,
            quantity: parseInt(quantity)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Đã thêm sản phẩm vào giỏ hàng!', 'success');
            updateCartCount();
            if (document.getElementById('quantity')) {
                document.getElementById('quantity').value = 1;
            }
        } else {
            showNotification('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra', 'error');
    });
}

function updateCartCount() {
    fetch('/api/get-cart-count.php')
        .then(response => response.json())
        .then(data => {
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.count;
            }
        });
}

function viewProduct(productId) {
    window.location.href = `/pages/product-detail.php?id=${productId}`;
}

// Cart Functions
function updateQuantity(productId, change) {
    fetch('/api/update-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            product_id: productId,
            change: change
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
}

function removeFromCart(productId) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
        fetch('/api/remove-from-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                product_id: productId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function clearCart() {
    if (confirm('Xóa tất cả sản phẩm khỏi giỏ hàng?')) {
        fetch('/api/clear-cart.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function checkout() {
    window.location.href = '/pages/checkout.php';
}

// Quantity Controls
function increaseQty(max) {
    const input = document.getElementById('quantity');
    if (input) {
        let current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (input) {
        let current = parseInt(input.value);
        if (current > 1) {
            input.value = current - 1;
        }
    }
}

function addToCartDetail(productId, productName, price) {
    addToCart(productId, productName, price);
}

// Search
function search(keyword) {
    if (keyword.trim()) {
        window.location.href = `/pages/search.php?q=${encodeURIComponent(keyword)}`;
    }
}

// Form Validation
function validateEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validatePhone(phone) {
    const regex = /^[0-9]{10,11}$/;
    return regex.test(phone.replace(/[-\s]/g, ''));
}

// Mobile Menu
function toggleMobileMenu() {
    const menu = document.querySelector('.navbar-menu');
    if (menu) {
        menu.classList.toggle('active');
    }
}

// Theme
function setTheme(theme) {
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
}

function getTheme() {
    return localStorage.getItem('theme') || 'light';
}

// Error Handler
window.addEventListener('error', function(e) {
    console.error('Error:', e.error);
});
