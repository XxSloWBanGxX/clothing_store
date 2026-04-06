<?php require_once __DIR__ . '/layout.php'; ?>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Створити користувача</h2>
    </div>

    <form action="index.php?url=admin-store-user" method="POST" class="admin-form">
        <div class="admin-form-grid">
            <div class="form-group">
                <label for="name">Імʼя</label>
                <input type="text" id="name" name="name" value="<?= htmlspecialchars($data['old']['name'] ?? ''); ?>">
                <?php if (!empty($data['errors']['name'])): ?><small class="form-error"><?= htmlspecialchars($data['errors']['name']); ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" value="<?= htmlspecialchars($data['old']['username'] ?? ''); ?>">
                <?php if (!empty($data['errors']['username'])): ?><small class="form-error"><?= htmlspecialchars($data['errors']['username']); ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($data['old']['email'] ?? ''); ?>">
                <?php if (!empty($data['errors']['email'])): ?><small class="form-error"><?= htmlspecialchars($data['errors']['email']); ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="phone">Телефон</label>
                <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($data['old']['phone'] ?? ''); ?>">
                <?php if (!empty($data['errors']['phone'])): ?><small class="form-error"><?= htmlspecialchars($data['errors']['phone']); ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Пароль</label>
                <input type="password" id="password" name="password">
                <?php if (!empty($data['errors']['password'])): ?><small class="form-error"><?= htmlspecialchars($data['errors']['password']); ?></small><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="role">Роль</label>
                <select id="role" name="role">
                    <option value="user" <?= (($data['old']['role'] ?? '') === 'user') ? 'selected' : ''; ?>>user</option>
                    <option value="support" <?= (($data['old']['role'] ?? '') === 'support') ? 'selected' : ''; ?>>support</option>
                    <option value="admin" <?= (($data['old']['role'] ?? '') === 'admin') ? 'selected' : ''; ?>>admin</option>
                </select>
                <?php if (!empty($data['errors']['role'])): ?><small class="form-error"><?= htmlspecialchars($data['errors']['role']); ?></small><?php endif; ?>
            </div>
        </div>

        <div class="filter-checkbox">
            <label>
                <input type="checkbox" name="is_verified" value="1" <?= !empty($data['old']['is_verified']) ? 'checked' : ''; ?>>
                Одразу позначити як підтвердженого
            </label>
        </div>

        <div class="admin-form-actions">
            <button type="submit" class="btn btn-dark">Створити користувача</button>
            <a href="index.php?url=admin-users" class="btn btn-light">Назад</a>
        </div>
    </form>
</section>

</main>
</div>
</body>
</html>