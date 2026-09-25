/**
 * MarketLink - Client-side ES6 AJAX & Interactive Modules
 * Compliant with 30 Vibe-Coded Design & UX Rules
 */

document.addEventListener('DOMContentLoaded', function () {
    // 1. AJAX Add to Basket Handlers
    document.querySelectorAll('.btn-add-to-basket').forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const productId = this.dataset.productId;
            const quantityInput = document.querySelector(`#qty-${productId}`);
            const quantity = quantityInput ? parseInt(quantityInput.value) : 1;
            const originalText = this.innerHTML;

            this.disabled = true;
            this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Adding...';

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
                this.innerHTML = originalText;

                if (data.success) {
                    showMarketToast(data.message, 'success');
                    const badge = document.querySelector('#basket-count-badge');
                    if (badge) {
                        badge.textContent = data.cartCount;
                        badge.classList.remove('d-none');
                    }
                } else {
                    showMarketToast(data.message || 'Unable to add produce to basket.', 'error');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.innerHTML = originalText;
                showMarketToast('Network error while updating basket.', 'error');
            });
        });
    });

    // 2. Favorite Farmer AJAX Toggle
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
                    showMarketToast(data.message, 'success');
                }
            })
            .catch(() => {
                showMarketToast('Could not update favorite status.', 'error');
            });
        });
    });

    // 3. AI FAQ Assistant Bot Logic
    initFaqBot();
});

// Toast notification helper without emojis
function showMarketToast(message, type = 'info') {
    let container = document.getElementById('market-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'market-toast-container';
        container.style.cssText = 'position:fixed;bottom:20px;left:20px;z-index:9999;display:flex;flex-direction:column;gap:10px;';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    const bg = type === 'success' ? '#1b4332' : (type === 'error' ? '#842029' : '#2d6a4f');
    toast.style.cssText = `background:${bg};color:#FAF9F6;padding:12px 18px;border-radius:6px;box-shadow:0 10px 25px rgba(0,0,0,0.15);font-size:14px;display:flex;align-items:center;gap:10px;animation:fadeIn 0.2s ease;`;

    const iconClass = type === 'success' ? 'fa-check' : (type === 'error' ? 'fa-circle-exclamation' : 'fa-info-circle');
    toast.innerHTML = `<i class="fa-solid ${iconClass}"></i> <span>${message}</span>`;

    container.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3800);
}

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
        loadingMsg.className = 'bot-msg bot';
        loadingMsg.innerHTML = '<i class="fa-solid fa-ellipsis fa-fade"></i> Consulting harvest registry...';
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
            botMsg.textContent = 'Market desk offline. Please verify network connectivity.';
            messages.appendChild(botMsg);
        });
    }

    sendBtn.addEventListener('click', sendMessage);
    input.addEventListener('keydown', e => {
        if (e.key === 'Enter') sendMessage();
    });
}
