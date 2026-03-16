<section class="profile-box">
    <h2>Профіль користувача</h2>
    <p><strong>Ім’я:</strong> <?= htmlspecialchars($user['name']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
    <p><strong>Роль:</strong> <?= htmlspecialchars($user['role']) ?></p>
</section>