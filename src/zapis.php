<?php
require __DIR__ . '/bootstrap.php';
requireAuth();
$user = currentUser();

$errors = [];
$course = trim($_POST['course'] ?? '');
$startDate = trim($_POST['startDate'] ?? '');
$fullName = trim($_POST['fullName'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$payment = $_POST['payment'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($course === '' || $startDate === '' || $fullName === '' || $phone === '' || $payment === '') {
        $errors[] = 'Заполните все поля формы.';
    }

    if (!$errors) {
        try {
            $stmt = db()->prepare('INSERT INTO applications (user_id, course, start_date, full_name, phone, payment, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $stmt->execute([
                $user['id'],
                $course,
                $startDate,
                $fullName,
                $phone,
                $payment === 'нал' ? 'нал' : 'безнал',
                'В обработке',
            ]);
            header('Location: /prosmotr_zapis.php');
            exit;
        } catch (Throwable $e) {
            $errors[] = 'Не удалось сохранить заявку. Попробуйте позже.';
            error_log('Create application error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Создание заявки</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo-container">
                <!-- Место для логотипа -->
                <div class="placeholder-logo">К</div>
                <!-- <img src="logo.png" alt="Корочки.есть" class="logo-img"> -->
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
        <h2 style="color: white;">Запиись на курс</h2>
    </div>

    <main>
        <div class="form-container">
            <h2>Формирование заявки</h2>
            <?php if ($errors): ?>
                <div class="error" style="display:block; margin-bottom:10px;"><?= htmlspecialchars(implode(' ', $errors)) ?></div>
            <?php endif; ?>
            <form method="post" id="applicationForm">
                <div class="form-group">
                    <label for="course">Выберите курс*</label>
                    <select id="course" name="course" required>
                        <option value="">-- Выберите курс --</option>
                        <option value="Цифровой маркетинг с нуля" <?= $course === 'Цифровой маркетинг с нуля' ? 'selected' : '' ?>>Цифровой маркетинг с нуля</option>
                        <option value="Веб разработчик с нуля" <?= $course === 'Веб разработчик с нуля' ? 'selected' : '' ?>>Веб разработчик с нуля</option>
                        <option value="Финансовый аналитик" <?= $course === 'Финансовый аналитик' ? 'selected' : '' ?>>Финансовый аналитик</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="startDate">Выберите дату начала курса*</label>
                    <input type="date" id="startDate" name="startDate" required value="<?= htmlspecialchars($startDate) ?>">
                </div>
                
                <div class="form-group">
                    <label for="fullName">ФИО*</label>
                    <input type="text" id="fullName" name="fullName" required value="<?= htmlspecialchars($fullName ?: ($user['full_name'] ?? '')) ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон* (только цифры)</label>
                    <input type="text" id="phone" name="phone" pattern="[0-9]+" required value="<?= htmlspecialchars($phone ?: ($user['phone'] ?? '')) ?>">
                </div>
                
                <div class="form-group">
                    <label>Форма оплаты*</label>
                    <div style="display: flex; gap: 20px; margin-top: 10px;">
                        <label style="display: flex; align-items: center;">
                            <input type="radio" name="payment" value="нал" required <?= $payment === 'нал' ? 'checked' : '' ?>> Нал
                        </label>
                        <label style="display: flex; align-items: center;">
                            <input type="radio" name="payment" value="безнал" <?= $payment === 'безнал' ? 'checked' : '' ?>> Безнал
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn">Подать заявку</button>
            </form>
        </div>
    </main>

    <footer id="footer">
        <div class="container">
            <div class="footer-container">
                <div class="footer-logo-container">
                    <!-- Место для логотипа в футере -->
                    <div class="placeholder-logo footer">К</div>
                    <!-- <img src="logo-white.png" alt="Корочки.есть" class="footer-logo-img"> -->
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
        // Установка минимальной и максимальной даты (ближайшие 3 месяца)
        const today = new Date();
        const minDate = new Date(today);
        minDate.setDate(today.getDate() + 1);
        const maxDate = new Date(today);
        maxDate.setMonth(today.getMonth() + 3);
        const formatDate = (date) => date.toISOString().split('T')[0];
        const startInput = document.getElementById('startDate');
        startInput.min = formatDate(minDate);
        startInput.max = formatDate(maxDate);
        if (!startInput.value) {
            startInput.value = formatDate(minDate);
        }

        // Ограничение ввода только цифр для телефона
        document.getElementById('phone').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html>
