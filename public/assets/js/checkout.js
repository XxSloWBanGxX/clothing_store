document.addEventListener('DOMContentLoaded', function () {
    const paymentRadios = document.querySelectorAll('input[name="payment_method"]');
    const cardBox = document.getElementById('cardPaymentBox');
    const submitBtn = document.getElementById('checkoutSubmitBtn');
    const cardNumber = document.getElementById('card_number');
    const cardExpiry = document.getElementById('card_expiry');
    const cardHolder = document.getElementById('card_holder');
    const previewNumber = document.getElementById('cardPreviewNumber');
    const previewHolder = document.getElementById('cardPreviewHolder');
    const previewExpiry = document.getElementById('cardPreviewExpiry');

    function selectedPayment() {
        return document.querySelector('input[name="payment_method"]:checked')?.value || 'card';
    }

    function toggleCardForm() {
        const isCard = selectedPayment() === 'card';
        if (cardBox) cardBox.style.display = isCard ? 'block' : 'none';
        if (submitBtn) {
            submitBtn.textContent = isCard ? 'Оплатити та підтвердити' : 'Підтвердити замовлення';
        }

        document.querySelectorAll('.checkout-pay-option').forEach(function (label) {
            const input = label.querySelector('input[name="payment_method"]');
            label.classList.toggle('is-active', input && input.checked);
        });
    }

    function formatCardNumber(value) {
        return value.replace(/\D/g, '').slice(0, 16).replace(/(\d{4})(?=\d)/g, '$1 ').trim();
    }

    function formatExpiry(value) {
        const digits = value.replace(/\D/g, '').slice(0, 4);
        if (digits.length <= 2) return digits;
        return digits.slice(0, 2) + '/' + digits.slice(2);
    }

    cardNumber?.addEventListener('input', function () {
        this.value = formatCardNumber(this.value);
        if (previewNumber) previewNumber.textContent = this.value || '•••• •••• •••• ••••';
    });

    cardExpiry?.addEventListener('input', function () {
        this.value = formatExpiry(this.value);
        if (previewExpiry) previewExpiry.textContent = this.value || 'MM/YY';
    });

    cardHolder?.addEventListener('input', function () {
        if (previewHolder) previewHolder.textContent = this.value.toUpperCase() || 'ІМʼЯ ВЛАСНИКА';
    });

    paymentRadios.forEach(function (radio) {
        radio.addEventListener('change', toggleCardForm);
    });

    toggleCardForm();

    if (cardNumber?.value && previewNumber) previewNumber.textContent = cardNumber.value;
    if (cardExpiry?.value && previewExpiry) previewExpiry.textContent = cardExpiry.value;
    if (cardHolder?.value && previewHolder) previewHolder.textContent = cardHolder.value.toUpperCase();
});
