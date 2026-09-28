/**
 * MarketLink - Modern ES6 Interactive Client Script
 * Emerald Green #059669 & Sleek Toast Feedback Engine
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. AJAX Add to Basket with Sleek Emerald Toast & Loading State
    document.querySelectorAll('.btn-add-to-basket, .btn-add-modern').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`#qty-${productId}`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            const originalHtml = this.innerHTML;

            // Loading state
            this.disabled = true;
            this.style.opacity = '0.75';
            this.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> <span class="ms-1">Adding...</span>';

            fetch('/basket/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
                })
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                this.style.opacity = '1';
                this.innerHTML = originalHtml;

                if (data.success) {
                    showEmeraldToast(data.message, 'success');
                    const badge = document.querySelector('#basket-count-badge');
                    if (badge) {
                        badge.textContent = data.cartCount;
                        badge.classList.remove('d-none');
                        // Quick bounce pulse
                        badge.animate([
                            { transform: 'scale(1)' },
                            { transform: 'scale(1.3)' },
                            { transform: 'scale(1)' }
                        ], { duration: 300 });
                    }
                } else {
                    showEmeraldToast(data.message || 'Unable to add produce to basket.', 'notice');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.style.opacity = '1';
                this.innerHTML = originalHtml;
                showEmeraldToast('Stall inventory busy. Please retry.', 'notice');
            });
        });
    });

    // 2. Favorite Farmer Toggle
    document.querySelectorAll('.btn-toggle-favorite').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const farmerId = this.dataset.farmerId;
            const icon = this.querySelector('i');

            fetch(`/customer/favorite/${farmerId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.favorited) {
                        icon.classList.remove('fa-regular');
                        icon.classList.add('fa-solid');
                        this.classList.add('text-danger');
                    } else {
                        icon.classList.remove('fa-solid');
                        icon.classList.add('fa-regular');
                        this.classList.remove('text-danger');
                    }
                    showEmeraldToast(data.message, 'success');
                }
            })
            .catch(() => {
                showEmeraldToast('Could not update favorite status.', 'notice');
            });
        });
    });

    // 3. AI FAQ Assistant Bot
    initFaqBot();
});

// Sleek Emerald Toast Notification System
function showEmeraldToast(message, type = 'success') {
    let container = document.getElementById('market-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'market-toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'market-toast-item';

    const isSuccess = type === 'success';
    const iconClass = isSuccess ? 'fa-check' : 'fa-info';
    const iconBg = isSuccess ? '#ecfdf5' : '#fef3c7';
    const iconColor = isSuccess ? '#059669' : '#d97706';
    const borderLeftColor = isSuccess ? '#059669' : '#f59e0b';

    toast.style.borderLeftColor = borderLeftColor;

    toast.innerHTML = `
        <div class="market-toast-icon" style="background-color: ${iconBg}; color: ${iconColor};">
            <i class="fa-solid ${iconClass}"></i>
        </div>
        <div class="flex-grow-1" style="line-height: 1.4;">
            <div style="font-weight: 700; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.05em; color: ${iconColor}; margin-bottom: 2px;">
                ${isSuccess ? 'Market Basket Updated' : 'Notice'}
            </div>
            <div style="font-size: 0.9rem; color: #1E293B;">${message}</div>
        </div>
        <button type="button" class="btn-close btn-close-sm" style="font-size: 0.75rem;" onclick="this.parentElement.remove()"></button>
    `;

    container.appendChild(toast);

    // Trigger smooth slide in
    requestAnimationFrame(() => {
        toast.classList.add('show');
    });

    // Auto dismiss after 3.8s
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 350);
    }, 3800);
}
window.showEmeraldToast = showEmeraldToast;

// AI Bot Integration
function initFaqBot() {
    const toggleBtn = document.getElementById('botToggleBtn');
    const chatWindow = document.getElementById('botChatWindow');
    const closeBtn = document.getElementById('botCloseBtn');
    const sendBtn = document.getElementById('botSendBtn');
    const input = document.getElementById('botInput');
    const messages = document.getElementById('botMessages');

    if (!toggleBtn || !chatWindow) return;

    toggleBtn.addEventListener('click', () => {
        chatWindow.classList.toggle('d-none');
        if (!chatWindow.classList.contains('d-none')) {
            input.focus();
        }
    });

    closeBtn.addEventListener('click', () => {
        chatWindow.classList.add('d-none');
    });

    function sendMessage() {
        const text = input.value.trim();
        if (!text) return;

        // Append user message
        const userMsg = document.createElement('div');
        userMsg.className = 'bot-msg user';
        userMsg.textContent = text;
        messages.appendChild(userMsg);
        input.value = '';
        messages.scrollTop = messages.scrollHeight;

        // Skeleton loading indicator
        const loadingMsg = document.createElement('div');
        loadingMsg.className = 'bot-msg bot bot-loading-skeleton';
        loadingMsg.setAttribute('role', 'status');
        loadingMsg.setAttribute('aria-label', 'Searching market information');
        loadingMsg.innerHTML = '<span></span><span></span><span></span>';
        messages.appendChild(loadingMsg);
        messages.scrollTop = messages.scrollHeight;

        fetch('/api/faq-bot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            loadingMsg.remove();
            const botMsg = document.createElement('div');
            botMsg.className = 'bot-msg bot';
            botMsg.textContent = data.reply;
            messages.appendChild(botMsg);
            messages.scrollTop = messages.scrollHeight;
        })
        .catch(() => {
            loadingMsg.remove();
            const botMsg = document.createElement('div');
            botMsg.className = 'bot-msg bot';
            botMsg.textContent = 'Market coordinator offline. Please verify network connectivity.';
            messages.appendChild(botMsg);
        });
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') sendMessage();
    });
}
