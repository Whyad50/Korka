<?php
require __DIR__ . '/bootstrap.php';
requireAuth();
$user = currentUser();

try {
    $stmt = db()->prepare('SELECT id, course, start_date, payment, status, date_created FROM applications WHERE user_id = ? ORDER BY date_created DESC');
    $stmt->execute([$user['id']]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $reviewStmt = db()->prepare('SELECT course, rating, text, created_at FROM reviews WHERE user_id = ? ORDER BY created_at DESC');
    $reviewStmt->execute([$user['id']]);
    $reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $applications = [];
    $reviews = [];
    error_log('Load applications error: ' . $e->getMessage());
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
    <title>Мои заявки</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo-container">
                <a href="index.php">
                    <img src="assets/media/logo.jpg" alt="Корочки.есть" class="logo-img">
                </a>
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
                    <?php if (isAdmin()): ?>
                        <a href="admin.php" class="my-applications">Админ</a>
                    <?php endif; ?>
                    <span class="user-login"><?= htmlspecialchars($user['login']) ?></span>
                    <a href="logout.php" class="register">Выйти</a>
                </div>
            </div>
        </div>
    </header>

     <div class="zagolov">
        <h2 style="color: white;">Просмотр записей</h2>
    </div>

    <main>
        <div class="table-container">
            <h2>Мои заявки</h2>
            <table id="applicationsTable">
                <thead>
                    <tr>
                        <th>№ заявки</th>
                        <th>Курс</th>
                        <th>Дата начала</th>
                        <th>Форма оплаты</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$applications): ?>
                        <tr><td colspan="5" style="text-align:center;">Нет заявок</td></tr>
                    <?php else: ?>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><?= htmlspecialchars($app['id']) ?></td>
                                <td><?= htmlspecialchars($app['course']) ?></td>
                                <td><?= htmlspecialchars($app['start_date']) ?></td>
                                <td><?= htmlspecialchars($app['payment']) ?></td>
                                <td><span class="status <?= statusClass($app['status']) ?>"><?= htmlspecialchars($app['status']) ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="reviews-section">
            <h3>Как прошли ваши курсы? Расскажите нам!</h3>
            <form id="reviewForm" method="post" action="review_submit.php">
                <div class="form-group">
                    <label for="reviewCourse">Курс</label>
                    <select id="reviewCourse" name="course" required>
                        <option value="">-- Выберите курс --</option>
                        <?php foreach ($applications as $app): ?>
                            <option value="<?= htmlspecialchars($app['course']) ?>"><?= htmlspecialchars($app['course']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="reviewRating">Оценка (1-5)</label>
                    <select id="reviewRating" name="rating" required>
                        <option value="">-- Выберите --</option>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="form-group">
                    <textarea id="reviewText" name="text" maxlength="500" placeholder="Напишите ваш отзыв здесь..." rows="5" required></textarea>
                    <div class="char-counter">
                        <span id="charCount">0</span>/500 символов
                    </div>
                </div>
                <button type="submit" class="btn">Отправить</button>
            </form>

            <div class="reviews-list" style="margin-top:20px;">
                <h3>Ваши отзывы</h3>
                <?php if (!$reviews): ?>
                    <p>Отзывов пока нет.</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="review-card">
                            <div class="review-header">
                                <div class="review-avatar"><?= htmlspecialchars(mb_substr($user['login'], 0, 1)) ?></div>
                                <div class="review-info">
                                    <div class="review-author"><?= htmlspecialchars($user['login']) ?></div>
                                    <div class="review-date"><?= htmlspecialchars($rev['created_at']) ?></div>
                                </div>
                            </div>
                            <div class="review-text">
                                <strong><?= htmlspecialchars($rev['course'] ?? '') ?></strong><br>
                                <?= htmlspecialchars($rev['text']) ?><br>
                                <small>Оценка: <?= (int) $rev['rating'] ?></small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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

    <script src="script.js"></script>
    <script>
        const reviewText = document.getElementById('reviewText');
        const charCount = document.getElementById('charCount');
        
        reviewText.addEventListener('input', function() {
            charCount.textContent = this.value.length;
        });
        
        const reviewForm = document.getElementById('reviewForm');
        reviewForm.addEventListener('submit', function(e) {
            const review = reviewText.value.trim();
            if (!review) {
                e.preventDefault();
                alert('Пожалуйста, напишите отзыв');
                return;
            }
            // allow the form to submit so the review is saved on the server
        });
    </script>
</body>
</html>
