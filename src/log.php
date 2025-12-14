<?php
require __DIR__ . '/bootstrap.php';
$user = currentUser();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($login === '' || $password === '') {
        $error = 'Введите логин и пароль.';
    } else {
        try {
            $stmt = db()->prepare('SELECT * FROM users WHERE login = ? LIMIT 1');
            $stmt->execute([$login]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($password, $row['password'])) {
                $_SESSION['user'] = [
                    'id' => $row['id'],
                    'login' => $row['login'],
                    'full_name' => $row['full_name'],
                    'phone' => $row['phone'],
                    'is_admin' => (int) $row['is_admin'],
                ];
                header('Location: /index.php');
                exit;
            } else {
                $error = 'Неверный логин или пароль.';
            }
        } catch (Throwable $e) {
            $error = 'Ошибка авторизации. Попробуйте позже.';
            error_log('Login error: ' . $e->getMessage());
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход в систему</title>
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
        <h2 style="color: white;">Авторизация</h2>
    </div>

    <main>
        <div class="form-container">
            <h2>Вход в систему</h2>
            <?php if ($error): ?>
                <div class="error" style="display:block; margin-bottom:10px;"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label for="login">Логин</label>
                    <input type="text" id="login" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="password">Пароль</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <div class="link-text">
                    <p><a href="reg.php">Ещё не зарегистрированы? Регистрация</a></p>
                </div>
                
                <button type="submit" class="btn">Войти</button>
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
</body>
</html>
