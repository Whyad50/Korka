<?php
require __DIR__ . '/bootstrap.php';
requireAdmin();
$user = currentUser();

$statusOptions = ['В обработке', 'Одобрена', 'Отклонена', 'Завершена'];
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['app_id'], $_POST['status'])) {
    $appId = (int) $_POST['app_id'];
    $newStatus = $_POST['status'];
    if (in_array($newStatus, $statusOptions, true)) {
        try {
            $stmt = db()->prepare('UPDATE applications SET status = ? WHERE id = ?');
            $stmt->execute([$newStatus, $appId]);
            $message = "Статус заявки #{$appId} обновлен.";
        } catch (Throwable $e) {
            $message = 'Не удалось обновить статус.';
            error_log('Admin update status error: ' . $e->getMessage());
        }
    } else {
        $message = 'Некорректный статус.';
    }
}

try {
    $stmt = db()->query('
        SELECT a.id, a.course, a.start_date, a.payment, a.status, a.date_created,
               u.login AS user_login, u.full_name AS user_full_name, u.phone AS user_phone
        FROM applications a
        LEFT JOIN users u ON u.id = a.user_id
        ORDER BY a.date_created DESC
    ');
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $applications = [];
    error_log('Admin load applications error: ' . $e->getMessage());
}

function statusClass(string $status): string {
    switch ($status) {
        case 'Одобрена':
            return 'status-approved';
        case 'Отклонена':
            return 'status-rejected';
        default:
            return 'status-pending';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ: заявки</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo-container">
                <div class="placeholder-logo">К</div>
                <a href="index.php" class="logo-text">Корочки.есть</a>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php#courses">О курсах</a></li>
                    <li><a href="index.php#reviews">Отзывы</a></li>
                    <li><a href="index.php#footer">Контакты</a></li>
                </ul>
            </nav>
            <div class="auth-buttons">
                <div class="user-info">
                    <a href="zapis.php" class="enroll">Записаться на курс</a>
                    <a href="prosmotr_zapis.php" class="my-applications">Мои заявки</a>
                    <a href="admin.php" class="my-applications">Админ</a>
                    <span class="user-login"><?= htmlspecialchars($user['login']) ?></span>
                    <a href="logout.php" class="register">Выйти</a>
                </div>
            </div>
        </div>
    </header>

    <div class="zagolov">
        <h2 style="color: white;">Администрирование заявок</h2>
    </div>

    <main>
        <div class="table-container">
            <h2>Все заявки</h2>
            <?php if ($message): ?>
                <div class="error" style="display:block; margin-bottom:10px;"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Курс</th>
                        <th>Дата начала</th>
                        <th>Оплата</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$applications): ?>
                        <tr><td colspan="7" style="text-align:center;">Нет заявок</td></tr>
                    <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= htmlspecialchars($app['id']) ?></td>
                                <td>
                                    <?= htmlspecialchars($app['user_login'] ?? '—') ?><br>
                                    <small><?= htmlspecialchars($app['user_full_name'] ?? '') ?> <?= htmlspecialchars($app['user_phone'] ?? '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($app['course']) ?></td>
                                <td><?= htmlspecialchars($app['start_date']) ?></td>
                                <td><?= htmlspecialchars($app['payment']) ?></td>
                                <td><span class="status <?= statusClass($app['status']) ?>"><?= htmlspecialchars($app['status']) ?></span></td>
                                <td>
                                    <form method="post" style="display:flex; gap:6px; align-items:center;">
                                        <input type="hidden" name="app_id" value="<?= (int) $app['id'] ?>">
                                        <select name="status" class="status-select">
                                            <?php foreach ($statusOptions as $status): ?>
                                                <option value="<?= htmlspecialchars($status) ?>" <?= $status === $app['status'] ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="submit" class="btn" style="padding:6px 10px;">Сохранить</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer id="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-logo-container">
                    <div class="placeholder-logo footer">К</div>
                    <a href="#" class="footer-logo-text">Корочки.есть</a>
                    <p>Образовательная платформа с лучшими курсами дополнительного профессионального образования.</p>
                    <div class="social-icons">
                        <a href="https://vk.com" target="_blank"><i class="fab fa-vk"></i></a>
                        <a href="https://telegram.org" target="_blank"><i class="fab fa-telegram"></i></a>
                        <a href="https://rutube.ru" target="_blank"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-column">
                    <h3>Контакты</h3>
                    <ul class="footer-links">
                        <li><a href="mailto:info@kozochki.net"><i class="fas fa-envelope"></i> info@kozochki.net</a></li>
                        <li><a href="tel:+79151919191"><i class="fas fa-phone"></i> +7 (915) 191-91-91</a></li>
                        <li><a href="https://yandex.ru/maps/-/CLgYvL13" target="_blank"><i class="fas fa-map-marker-alt"></i> 115035, г. Москва, ул. Садовническая, д. 3, офис 407</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© 2025 Корочки.есть. Все права защищены. <a href="#" style="color: #aaa;">Политика конфиденциальности</a></p>
            </div>
        </div>
    </footer>
</body>
</html>
