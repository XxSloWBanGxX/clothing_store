<?php require_once __DIR__ . '/layout.php'; ?>

<section class="admin-panel-box">
    <div class="admin-panel-head">
        <h2>Користувачі</h2>
        <a href="index.php?url=admin-create-user" class="btn btn-dark">+ Створити користувача</a>
    </div>

    <div class="admin-table-wrap">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Імʼя</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Телефон</th>
                    <th>Роль</th>
                    <th>Статус</th>
                    <th>Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['users'])): ?>
                    <?php foreach ($data['users'] as $user): ?>
                        <tr>
                            <td>#<?= (int)$user['id']; ?></td>
                            <td><?= htmlspecialchars($user['name']); ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= htmlspecialchars($user['email']); ?></td>
                            <td><?= htmlspecialchars($user['phone']); ?></td>
                            <td><?= htmlspecialchars($user['role']); ?></td>
                            <td>
                                <?php if (!empty($user['is_verified'])): ?>
                                    <span class="admin-badge success">Підтверджений</span>
                                <?php else: ?>
                                    <span class="admin-badge danger">Не підтверджений</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="admin-actions">
                                    <?php if ((int)$user['id'] !== (int)$_SESSION['user']['id']): ?>
                                        <form action="index.php?url=admin-delete-user" method="POST" onsubmit="return confirm('Видалити користувача?');">
                                            <input type="hidden" name="id" value="<?= (int)$user['id']; ?>">
                                            <button type="submit" class="btn btn-dark btn-sm">Видалити</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="admin-badge success">Ти</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">Користувачів поки немає.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</section>

</main>
</div>
</body>
</html>