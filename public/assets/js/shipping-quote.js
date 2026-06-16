document.addEventListener('DOMContentLoaded', function () {
    const carrierInput = document.getElementById('checkout_carrier') || document.getElementById('cart_carrier');
    const shippingEl = document.getElementById('shippingAmount');
    const totalEl = document.getElementById('checkoutTotal') || document.getElementById('cartGrandTotal');
    const subtotal = parseFloat(document.body.dataset.cartSubtotal || '0');

    if (! carrierInput) {
        return;
    }

    async function refreshQuote() {
        const carrier = carrierInput.value || 'nova_poshta';

        try {
            const res = await fetch('/api/shipping/quote?carrier=' + encodeURIComponent(carrier), {
                headers: { Accept: 'application/json' },
            });
            const data = await res.json();

            if (shippingEl) {
                shippingEl.textContent = data.label || '—';
            }
            if (totalEl && typeof data.total === 'number') {
                totalEl.textContent = new Intl.NumberFormat('uk-UA').format(Math.round(data.total)) + ' грн';
            } else if (totalEl && subtotal > 0 && typeof data.amount === 'number') {
                totalEl.textContent = new Intl.NumberFormat('uk-UA').format(Math.round(subtotal + data.amount)) + ' грн';
            }
        } catch (e) {
            if (shippingEl) shippingEl.textContent = '—';
        }
    }

    document.querySelectorAll('.delivery-carrier-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            setTimeout(refreshQuote, 50);
        });
    });

    document.querySelectorAll('.cart-carrier-tab').forEach(function (btn) {
        btn.addEventListener('click', function () {
            carrierInput.value = btn.dataset.carrier || 'nova_poshta';
            document.querySelectorAll('.cart-carrier-tab').forEach(function (b) {
                b.classList.toggle('active', b === btn);
            });
            refreshQuote();
        });
    });

    refreshQuote();
});
