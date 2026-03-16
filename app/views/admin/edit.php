<section class="auth-box">
    <h2>Редагувати товар</h2>

    <form method="POST" class="form">
        <input type="text" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
        <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" required>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
        <input type="text" name="image" value="<?= htmlspecialchars($product['image']) ?>" required>
        <textarea name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
        <button type="submit" class="btn">Оновити</button>
    </form>
</section>