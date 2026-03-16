<section class="auth-box">
    <h2>Додати товар</h2>

    <form method="POST" class="form">
        <input type="text" name="title" placeholder="Назва товару" required>
        <input type="text" name="category" placeholder="Категорія" required>
        <input type="number" step="0.01" name="price" placeholder="Ціна" required>
        <input type="text" name="image" placeholder="Назва зображення (наприклад hoodie.jpg)" required>
        <textarea name="description" placeholder="Опис товару" required></textarea>
        <button type="submit" class="btn">Зберегти</button>
    </form>
</section>