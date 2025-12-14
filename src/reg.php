<?php
require __DIR__ . '/bootstrap.php';
$user = currentUser();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $fullName = trim($_POST['fullName'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (strlen($login) < 5 || strlen($login) > 15) {
        $errors[] = 'Логин должен быть от 5 до 15 символов.';
    }

    if (!preg_match('/^(?=.*[A-Z])(?=.*\d).{5,}$/', $password)) {
        $errors[] = 'Пароль должен содержать минимум 5 символов, хотя бы одну заглавную букву и цифру.';
    }

    if ($fullName === '' || $phone === '') {
        $errors[] = 'Заполните ФИО и телефон.';
    }

    if (!$errors) {
        try {
            $pdo = db();
            $stmt = $pdo->prepare('SELECT id FROM users WHERE login = ? LIMIT 1');
            $stmt->execute([$login]);
            if ($stmt->fetch()) {
                $errors[] = 'Логин уже занят.';
            } else {
                $insert = $pdo->prepare('INSERT INTO users (login, password, full_name, phone) VALUES (?, ?, ?, ?)');
                $insert->execute([
                    $login,
                    password_hash($password, PASSWORD_DEFAULT),
                    $fullName,
                    $phone,
                ]);

                $userId = (int) $pdo->lastInsertId();
                $_SESSION['user'] = [
                    'id' => $userId,
                    'login' => $login,
                    'full_name' => $fullName,
                    'phone' => $phone,
                    'is_admin' => 0,
                ];
                header('Location: /index.php');
                exit;
            }
        } catch (Throwable $e) {
            $errors[] = 'Ошибка регистрации. Попробуйте позже.';
            error_log('Register error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация</title>
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
                <?php if ($user): ?>
                    <div class="user-info">
                        <a href="zapis.php" class="enroll">Записаться на курс</a>
                        <a href="prosmotr_zapis.php" class="my-applications">Мои заявки</a>
                        <?php if (isAdmin()): ?>
                            <a href="admin.php" class="my-applications">Админ</a>
                        <?php endif; ?>
                        <span class="user-login"><?= htmlspecialchars($user['login']) ?></span>
                        <a href="logout.php" class="register">Выйти</a>
                    </div>
                <?php else: ?>
                    <a href="log.php" class="login">Вход</a>
                    <a href="reg.php" class="register">Регистрация</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <div class="zagolov">
        <h2 style="color: white;">Регистрация</h2>
    </div>

    <main>

        <div class="form-container">
            <h2>Регистрация</h2>
            <?php if ($errors): ?>
                <div class="error" style="display:block; margin-bottom:10px;">
                    <?= htmlspecialchars(implode(' ', $errors)) ?>
                </div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="login">Логин* (5-15 символов)</label>
                    <input type="text" id="login" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль* (минимум 5 символов, цифры и заглавные буквы)</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="fullName">ФИО*</label>
                    <input type="text" id="fullName" name="fullName" required value="<?= htmlspecialchars($_POST['fullName'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон*</label>
                    <input type="tel" id="phone" name="phone" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                
                <button type="submit" class="btn">Зарегистрироваться</button>
            </form>
            
            <div class="link-text">
                <p>Уже есть аккаунт? <a href="log.php">Войти</a></p>
            </div>
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
</body>
</html>
