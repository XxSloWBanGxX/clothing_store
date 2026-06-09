<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="cart-page">
    <section class="catalog-hero">
        <div class="container">
            <div class="catalog-hero-box">
                <span class="hero-badge">CART</span>
                <h1>Кошик</h1>
                <p>Переглянь додані товари та оформи замовлення.</p>
            </div>
        </div>
    </section>

    <section class="cart-section">
        <div class="container">
            <?php if (!empty($data['cartItems'])): ?>
                <form id="cartSelectionForm" method="POST" action="index.php?url=checkout-selected">
                    <div class="cart-modal-layout">
                        <div class="cart-modal-list">
                            <div class="cart-modal-head">
                                <h2>Товари в кошику</h2>
                                <p>
                                    Всього <?= count($data['cartItems']); ?> позицій | 
                                    Вибрано: <span id="selectedCount">0</span>
                                </p>
                            </div>

                            <?php foreach ($data['cartItems'] as $item): ?>
                                <div class="cart-modal-item">
                                    <div class="cart-modal-check">
                                        <input 
                                            type="checkbox" 
                                            class="cart-item-checkbox" 
                                            value="<?= (int)$item['cart_item_id']; ?>"
                                            data-price="<?= (float)$item['subtotal']; ?>"
                                        >
                                    </div>

                                <div class="cart-modal-image-wrap">
                                    <?php if (!empty($item['product']['image'])): ?>
                                        <img
                                            src="assets/images/products/<?= htmlspecialchars($item['product']['image']); ?>"
                                            alt="<?= htmlspecialchars($item['product']['name']); ?>"
                                            class="cart-modal-image"
                                            onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                        >
                                        <div class="cart-modal-fallback" style="display:none;">
                                            <?= htmlspecialchars($item['product']['name']); ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="cart-modal-fallback">
                                            <?= htmlspecialchars($item['product']['name']); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="cart-modal-info">
                                    <a href="index.php?url=product&id=<?= (int)$item['product']['id']; ?>" class="cart-modal-title">
                                        <?= htmlspecialchars($item['product']['name']); ?>
                                    </a>

                                    <p class="cart-modal-meta">
                                        Категорія: <?= htmlspecialchars($item['product']['category_name']); ?>
                                    </p>

                                    <?php if (!empty($item['selected_size'])): ?>
                                        <p class="cart-modal-meta">
                                            Розмір: <strong><?= htmlspecialchars($item['selected_size']); ?></strong>
                                        </p>
                                    <?php endif; ?>

                                    <?php if (!empty($item['selected_color_name'])): ?>
                                        <p class="cart-modal-meta cart-color-line">
                                            Колір:
                                            <span
                                                class="cart-color-dot"
                                                style="background: <?= htmlspecialchars($item['selected_color_hex'] ?: '#d9d9df'); ?>;"
                                            ></span>
                                            <strong><?= htmlspecialchars($item['selected_color_name']); ?></strong>
                                        </p>
                                    <?php endif; ?>

                                    <p class="cart-modal-meta">
                                        <?= (int)$item['product']['stock'] > 0 ? 'Є в наявності' : 'Немає в наявності'; ?>
                                    </p>
                                </div>

                                <div class="cart-modal-qty">
                                    <form action="index.php?url=cart-decrease" method="POST">
                                        <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id']; ?>">
                                        <button type="submit" class="cart-qty-btn">−</button>
                                    </form>

                                    <div class="cart-qty-box"><?= (int)$item['quantity']; ?></div>

                                    <form action="index.php?url=cart-increase" method="POST">
                                        <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id']; ?>">
                                        <button type="submit" class="cart-qty-btn">+</button>
                                    </form>
                                </div>

                                <div class="cart-modal-price">
                                    <strong><?= number_format((float)$item['subtotal'], 0, '.', ' '); ?> грн</strong>
                                </div>

                                <div class="cart-modal-actions">
                                    <details class="cart-item-menu">
                                        <summary>⋮</summary>
                                        <div class="cart-item-dropdown">
                                            <form action="index.php?url=cart-remove" method="POST">
                                                <input type="hidden" name="cart_item_id" value="<?= (int)$item['cart_item_id']; ?>">
                                                <button type="submit">Видалити з кошика</button>
                                            </form>
                                        </div>
                                    </details>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <aside class="cart-modal-summary">
                        <div class="cart-modal-summary-box">
                            <div class="cart-modal-summary-line">
                                <span>Всього сума:</span>
                                <strong><?= number_format((float)$data['total'], 0, '.', ' '); ?> грн</strong>
                            </div>

                            <div class="cart-modal-summary-line" style="color: #ff6b35; font-weight: bold;">
                                <span>Вибрано сума:</span>
                                <strong id="selectedTotal">0 грн</strong>
                            </div>

                            <button 
                                type="submit" 
                                class="cart-order-btn"
                                id="checkoutSelectedBtn"
                                disabled
                                style="opacity: 0.6; cursor: not-allowed;"
                            >
                                Замовити вибрані
                            </button>

                            <a href="index.php?url=checkout" class="cart-order-btn" style="background: #999; text-align: center;">
                                Замовити всі
                            </a>

                            <form action="index.php?url=cart-clear" method="POST" style="margin-top: 10px;">
                                <button type="submit" class="cart-continue-btn">Очистити кошик</button>
                            </form>
                        </div>
                    </aside>
                </div>
                </form>
            <?php else: ?>
                <div class="empty-box">
                    <h3>Кошик порожній</h3>
                    <p>Додай товари в кошик, щоб вони з’явилися тут.</p>
                    <a href="index.php?url=catalog" class="btn btn-dark">Перейти в каталог</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.cart-item-checkbox');
    const selectedCountSpan = document.getElementById('selectedCount');
    const selectedTotalSpan = document.getElementById('selectedTotal');
    const checkoutBtn = document.getElementById('checkoutSelectedBtn');
    const cartForm = document.getElementById('cartSelectionForm');

    function updateSummary() {
        let selected = 0;
        let total = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selected++;
                total += parseFloat(checkbox.dataset.price);
            }
        });

        selectedCountSpan.textContent = selected;
        selectedTotalSpan.textContent = selected > 0 
            ? total.toLocaleString('uk-UA') + ' грн'
            : '0 грн';

        // Активуємо/деактивуємо кнопку
        if (selected > 0) {
            checkoutBtn.disabled = false;
            checkoutBtn.style.opacity = '1';
            checkoutBtn.style.cursor = 'pointer';
        } else {
            checkoutBtn.disabled = true;
            checkoutBtn.style.opacity = '0.6';
            checkoutBtn.style.cursor = 'not-allowed';
        }
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSummary);
    });

    // Форма перед відправкою
    cartForm.addEventListener('submit', function(e) {
        const selected = Array.from(checkboxes)
            .filter(cb => cb.checked)
            .map(cb => cb.value);

        if (selected.length === 0) {
            e.preventDefault();
            alert('Будь ласка, виберіть товари для замовлення');
            return;
        }

        // Додаємо приховані поля для вибраних товарів
        selected.forEach(itemId => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_items[]';
            input.value = itemId;
            cartForm.appendChild(input);
        });
    });

    // Ініціалізуємо при завантаженні
    updateSummary();
});
</script>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>