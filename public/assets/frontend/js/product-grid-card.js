/**
 * Product Grid Card JavaScript
 * Handles interactive functionality for the product card component
 */

class ProductGridCard {
    constructor() {
        this.init();
    }

    init() {
        this.bindEvents();
        this.handleImageErrors();
        this.initializeTooltips();
    }

    bindEvents() {
        // Handle quick view buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('quick-view-btn') || e.target.closest('.quick-view-btn')) {
                e.preventDefault();
                const btn = e.target.classList.contains('quick-view-btn') ? e.target : e.target.closest('.quick-view-btn');
                this.handleQuickView(btn);
            }
        });

        // Handle add to cart buttons
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-to-cart-btn') || e.target.closest('.add-to-cart-btn')) {
                e.preventDefault();
                const btn = e.target.classList.contains('add-to-cart-btn') ? e.target : e.target.closest('.add-to-cart-btn');
                this.handleAddToCart(btn);
            }
        });

        // Handle keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                if (e.target.classList.contains('quick-view-btn')) {
                    e.preventDefault();
                    this.handleQuickView(e.target);
                } else if (e.target.classList.contains('add-to-cart-btn')) {
                    e.preventDefault();
                    this.handleAddToCart(e.target);
                }
            }
        });

        // Handle card hover analytics (optional)
        document.addEventListener('mouseenter', (e) => {
            if (e.target.closest('.modern-product-card')) {
                this.trackCardView(e.target.closest('.modern-product-card'));
            }
        }, true);
    }

    handleQuickView(button) {
        const productId = button.dataset.productId;
        const url = button.dataset.url;

        if (!productId) {
            console.warn('Product ID not found for quick view');
            return;
        }

        // Show loading state
        this.setButtonLoading(button, true);

        // Fire custom event for quick view
        const event = new CustomEvent('productQuickView', {
            detail: {
                productId: productId,
                url: url,
                button: button
            }
        });
        
        document.dispatchEvent(event);

        // If no custom handler, fallback to default behavior
        setTimeout(() => {
            if (url) {
                this.openQuickViewModal(url, productId);
            } else {
                this.showQuickViewPlaceholder(productId);
            }
            this.setButtonLoading(button, false);
        }, 100);
    }

    handleAddToCart(button) {
        const productId = button.dataset.productId;
        const url = button.dataset.url;

        if (!productId) {
            console.warn('Product ID not found for add to cart');
            return;
        }

        if (button.disabled) {
            return;
        }

        // Show loading state
        this.setButtonLoading(button, true);

        // Fire custom event for add to cart
        const event = new CustomEvent('productAddToCart', {
            detail: {
                productId: productId,
                url: url,
                button: button
            }
        });
        
        document.dispatchEvent(event);

        // If no custom handler, fallback to default behavior
        setTimeout(() => {
            if (url) {
                this.addToCartAjax(url, productId, button);
            } else {
                this.showAddToCartSuccess(button);
            }
        }, 100);
    }

    setButtonLoading(button, isLoading) {
        if (isLoading) {
            button.disabled = true;
            button.style.opacity = '0.7';
            
            // Store original content
            button.dataset.originalContent = button.innerHTML;
            
            // Show loading spinner
            button.innerHTML = `
                <svg class="animate-spin" width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" opacity="0.25"></circle>
                    <path fill="currentColor" opacity="0.75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span>Loading...</span>
            `;
        } else {
            button.disabled = false;
            button.style.opacity = '1';
            
            // Restore original content
            if (button.dataset.originalContent) {
                button.innerHTML = button.dataset.originalContent;
                delete button.dataset.originalContent;
            }
        }
    }

    async addToCartAjax(url, productId, button) {
        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: 1
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                this.showAddToCartSuccess(button);
                this.updateCartCount(data.cart_count);
            } else {
                throw new Error(data.message || 'Failed to add to cart');
            }
        } catch (error) {
            console.error('Add to cart error:', error);
            this.showAddToCartError(button, error.message);
        } finally {
            this.setButtonLoading(button, false);
        }
    }

    async openQuickViewModal(url, productId) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': this.getCSRFToken(),
                }
            });

            const data = await response.json();

            if (response.ok && data.html) {
                this.showModal(data.html, `Quick View - ${data.title || 'Product'}`);
            } else {
                throw new Error(data.message || 'Failed to load product details');
            }
        } catch (error) {
            console.error('Quick view error:', error);
            this.showQuickViewPlaceholder(productId);
        }
    }

    showModal(content, title = 'Product Details') {
        // Create modal backdrop
        const backdrop = document.createElement('div');
        backdrop.className = 'modal-backdrop';
        backdrop.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        `;

        // Create modal
        const modal = document.createElement('div');
        modal.className = 'product-modal';
        modal.style.cssText = `
            background: white;
            border-radius: 12px;
            max-width: 800px;
            max-height: 90vh;
            width: 100%;
            overflow-y: auto;
            position: relative;
            animation: modalSlideUp 0.3s ease-out;
        `;

        // Add modal content
        modal.innerHTML = `
            <div style="position: sticky; top: 0; background: white; padding: 20px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                <h2 style="margin: 0; font-size: 1.5rem; font-weight: 600;">${title}</h2>
                <button class="modal-close" style="background: none; border: none; font-size: 24px; cursor: pointer; padding: 5px;">&times;</button>
            </div>
            <div style="padding: 20px;">
                ${content}
            </div>
        `;

        backdrop.appendChild(modal);
        document.body.appendChild(backdrop);

        // Add close functionality
        const closeModal = () => {
            backdrop.remove();
            document.body.style.overflow = '';
        };

        backdrop.addEventListener('click', (e) => {
            if (e.target === backdrop) closeModal();
        });

        modal.querySelector('.modal-close').addEventListener('click', closeModal);

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Add animation styles if not already present
        if (!document.querySelector('#modal-animations')) {
            const style = document.createElement('style');
            style.id = 'modal-animations';
            style.textContent = `
                @keyframes modalSlideUp {
                    from {
                        opacity: 0;
                        transform: translateY(50px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    showQuickViewPlaceholder(productId) {
        const content = `
            <div style="text-align: center; padding: 40px;">
                <h3>Quick View</h3>
                <p>Product ID: ${productId}</p>
                <p>Implement your quick view content here.</p>
                <button onclick="this.closest('.modal-backdrop').remove(); document.body.style.overflow = '';" 
                        style="background: #6366f1; color: white; border: none; padding: 10px 20px; border-radius: 8px; cursor: pointer;">
                    Close
                </button>
            </div>
        `;
        this.showModal(content, 'Quick View');
    }

    showAddToCartSuccess(button) {
        // Temporarily change button appearance
        const originalContent = button.innerHTML;
        const originalBg = button.style.background;
        
        button.innerHTML = `
            <svg width="18" height="18" fill="currentColor" viewBox="0 0 24 24">
                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
            </svg>
            <span>Added!</span>
        `;
        button.style.background = 'linear-gradient(135deg, #10b981 0%, #059669 100%)';

        // Show notification
        this.showNotification('Product added to cart successfully!', 'success');

        // Revert after 2 seconds
        setTimeout(() => {
            button.innerHTML = originalContent;
            button.style.background = originalBg;
        }, 2000);
    }

    showAddToCartError(button, message) {
        // Show error notification
        this.showNotification(message || 'Failed to add product to cart', 'error');
        
        // Briefly highlight button in red
        const originalBg = button.style.background;
        button.style.background = 'linear-gradient(135deg, #ef4444 0%, #dc2626 100%)';
        
        setTimeout(() => {
            button.style.background = originalBg;
        }, 1000);
    }

    showNotification(message, type = 'info') {
        // Remove existing notifications
        document.querySelectorAll('.product-notification').forEach(n => n.remove());

        const notification = document.createElement('div');
        notification.className = 'product-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${type === 'success' ? '#10b981' : type === 'error' ? '#ef4444' : '#6366f1'};
            color: white;
            padding: 15px 20px;
            border-radius: 8px;
            z-index: 10000;
            max-width: 300px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            animation: slideInRight 0.3s ease-out;
        `;

        notification.textContent = message;
        document.body.appendChild(notification);

        // Add slide animation
        if (!document.querySelector('#notification-animations')) {
            const style = document.createElement('style');
            style.id = 'notification-animations';
            style.textContent = `
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(100%);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
            `;
            document.head.appendChild(style);
        }

        // Auto remove after 3 seconds
        setTimeout(() => {
            notification.style.animation = 'slideInRight 0.3s ease-out reverse';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }

    updateCartCount(count) {
        // Update cart count in header or wherever it's displayed
        const cartCountElements = document.querySelectorAll('.cart-count, [data-cart-count]');
        cartCountElements.forEach(element => {
            element.textContent = count;
            
            // Add animation
            element.style.animation = 'cartBounce 0.5s ease-out';
            setTimeout(() => {
                element.style.animation = '';
            }, 500);
        });

        // Add bounce animation if not already present
        if (!document.querySelector('#cart-animations')) {
            const style = document.createElement('style');
            style.id = 'cart-animations';
            style.textContent = `
                @keyframes cartBounce {
                    0%, 20%, 60%, 100% {
                        transform: translateY(0);
                    }
                    40% {
                        transform: translateY(-10px);
                    }
                    80% {
                        transform: translateY(-5px);
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    handleImageErrors() {
        document.addEventListener('error', (e) => {
            if (e.target.tagName === 'IMG' && e.target.closest('.product-image-container')) {
                e.target.src = '/images/product-placeholder.jpg';
                e.target.alt = 'Product image not available';
            }
        }, true);
    }

    initializeTooltips() {
        // Simple tooltip implementation
        document.addEventListener('mouseenter', (e) => {
            if (e.target.title && e.target.closest('.modern-product-card')) {
                this.showTooltip(e.target, e.target.title);
            }
        }, true);

        document.addEventListener('mouseleave', (e) => {
            if (e.target.title && e.target.closest('.modern-product-card')) {
                this.hideTooltip();
            }
        }, true);
    }

    showTooltip(element, text) {
        this.hideTooltip(); // Remove any existing tooltip

        const tooltip = document.createElement('div');
        tooltip.className = 'product-tooltip';
        tooltip.textContent = text;
        tooltip.style.cssText = `
            position: absolute;
            background: #1f2937;
            color: white;
            padding: 8px 12px;
            border-radius: 6px;
            font-size: 0.875rem;
            z-index: 10001;
            white-space: nowrap;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
        `;

        document.body.appendChild(tooltip);

        // Position tooltip
        const rect = element.getBoundingClientRect();
        tooltip.style.top = `${rect.top - tooltip.offsetHeight - 8}px`;
        tooltip.style.left = `${rect.left + (rect.width - tooltip.offsetWidth) / 2}px`;

        // Show tooltip
        setTimeout(() => {
            tooltip.style.opacity = '1';
        }, 50);
    }

    hideTooltip() {
        const existingTooltip = document.querySelector('.product-tooltip');
        if (existingTooltip) {
            existingTooltip.remove();
        }
    }

    trackCardView(card) {
        // Optional analytics tracking
        const productId = card.dataset.productId;
        if (productId && typeof gtag !== 'undefined') {
            gtag('event', 'view_item', {
                currency: 'USD',
                value: 0,
                items: [{
                    item_id: productId,
                    item_name: card.querySelector('.product-title')?.textContent || 'Unknown Product',
                }]
            });
        }
    }

    getCSRFToken() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    }
}

// Initialize when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        new ProductGridCard();
    });
} else {
    new ProductGridCard();
}

// Export for manual initialization
window.ProductGridCard = ProductGridCard;