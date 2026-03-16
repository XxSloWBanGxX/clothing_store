<section class="admin-top">
    <h2>Адмін-панель</h2>
    <a class="btn" href="<?= BASE_URL ?>admin/create">Додати товар</a>
</section>

<div class="admin-table-wrap">
    <table class="admin-table">
        <tr>
            <th>ID</th>
            <th>Назва</th>
            <th>Категорія</th>
            <th>Ціна</th>
            <th>Дії</th>
        </tr>

        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= $product['id'] ?></td>
                <td><?= htmlspecialchars($product['title']) ?></td>
                <td><?= htmlspecialchars($product['category']) ?></td>
                <td><?= $product['price'] ?> грн</td>
                <td>
                    <a class="btn-outline small-btn" href="<?= BASE_URL ?>admin/edit?id=<?= $product['id'] ?>">Редагувати</a>
                    <a class="btn-danger small-btn" href="<?= BASE_URL ?>admin/delete?id=<?= $product['id'] ?>" onclick="return confirm('Видалити товар?')">Видалити</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>