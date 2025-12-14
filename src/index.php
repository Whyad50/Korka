<?php
require __DIR__ . '/bootstrap.php';
$user = currentUser();

// Fetch latest 4 reviews for homepage
try {
    $stmt = db()->query('SELECT user_login, course, rating, text, created_at FROM reviews ORDER BY created_at DESC LIMIT 4');
    $latestReviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    $latestReviews = [];
    error_log('Load homepage reviews error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Корочки.есть — курсы дополнительного образования</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>
    <!-- Шапка -->
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
                    <li><a href="#courses">О курсах</a></li>
                    <li><a href="#reviews">Отзывы</a></li>
                    <li><a href="#footer">Контакты</a></li>
                    
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


    <!-- Слайдер во всю ширину -->
    <div class="slider-container">
        <div class="slider">
            <div class="slide">
                <div class="slide-content">
                    <h3>Начни обучение уже сегодня</h3>
                    <p>Освойте программирование, анализ данных и другие востребованные навыки</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h3>Маркетинг и дизайн</h3>
                    <p>Станьте экспертом в цифровом маркетинге, UX/UI дизайне и брендинге</p>
                </div>
            </div>
            <div class="slide">
                <div class="slide-content">
                    <h3>Бизнес и управление</h3>
                    <p>Развивайте навыки управления проектами, финансами и командами</p>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="slider-indicators">
            <div class="indicator active" data-slide="0"></div>
            <div class="indicator" data-slide="1"></div>
            <div class="indicator" data-slide="2"></div>
        </div>
    </div>

    <!-- Раздел "О нас" -->
    <section class="about" id="about" >
        <div class="container">
            <h2 style="color: #4A21FF;">О нас</h2>
            <div class="about-content fade-in">
                <p>Хочешь освоить востребованную специальность, но нет времени на долгие поиски курсов и бумажную волокиту? Корочки.есть — это удобная информационная система, где за пару кликов ты находишь, записываешься и начинаешь обучение на курсах дополнительного профессионального образования.</p>
                <p>Будь ты студент, фрилансер или опытный специалист — у нас ты найдёшь актуальные программы по IT, маркетингу, дизайну, управлению, педагогике, финансам и другим направлениям.</p>
                <p>Все курсы — от проверенных образовательных платформ.<br>Все документы — с официальной поддержкой и возможностью трудоустройства.</p>
            </div>
        </div>
    </section>
    

    <!-- Раздел "Курсы" -->
    <section class="courses" id="courses">
        <div class="container">
            <h2 style="color: #f9f9f9;">Курсы</h2>
            <div class="courses-container">
                <!-- Карточка курса 1 -->
                <div class="course-card fade-in">
                    <div class="course-header">
                        <h3>Цифровой маркетолог с нуля</h3>
                        <div class="course-details">3 месяца • онлайн, 100% практика</div>
                        <div class="course-cert">Удостоверение о повышении квалификации</div>
                    </div>
                    <div class="course-body">
                        <p>Изучаемые темы: рекламу в социальных сетях, работать с SMM и таргетированной рекламой.</p>
                        <p>Соберемся в программу в реальных кейсах, задайте вопросы на время обучения.</p>
                        <p>Подходит новичкам без опыта.</p>
                    </div>
                    <div class="course-footer">
                        <a href="zapis.php" class="btn">Записаться</a>
                    </div>
                </div>

                <!-- Карточка курса 2 -->
                <div class="course-card fade-in">
                    <div class="course-header">
                        <h3>Веб-разработчик с нуля</h3>
                        <div class="course-details">4 месяца • онлайн, практические задания</div>
                        <div class="course-cert">Диплом о профессиональной переподготовке</div>
                    </div>
                    <div class="course-body">
                        <p>Изучаемые технологии: HTML, CSS, JavaScript, React, Node.js.</p>
                        <p>Создание реальных проектов для портфолио, помощь в трудоустройстве.</p>
                        <p>Подходит начинающим программистам.</p>
                    </div>
                    <div class="course-footer">
                        <a href="zapis.php" class="btn">Записаться</a>
                    </div>
                </div>

                <!-- Карточка курса 3 -->
                <div class="course-card fade-in">
                    <div class="course-header">
                        <h3>Финансовый аналитик</h3>
                        <div class="course-details">3 месяца • онлайн, разбор реальных кейсов</div>
                        <div class="course-cert">Удостоверение о повышении квалификации</div>
                    </div>
                    <div class="course-body">
                        <p>Изучаемые темы: анализ финансовой отчетности, бюджетирование, финансовое моделирование.</p>
                        <p>Практика на реальных данных компаний, подготовка к собеседованию.</p>
                        <p>Подходит экономистам и бухгалтерам.</p>
                    </div>
                    <div class="course-footer">
                        <a href="zapis.php" class="btn">Записаться</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Раздел "Отзывы" -->
    <section class="reviews" id="reviews">
        <div class="container">
            <h2 style="color: #4A21FF;">Отзывы</h2>
            <div class="reviews-grid">
                <?php if (!$latestReviews): ?>
                    <p>Отзывов пока нет.</p>
                <?php else: ?>
                    <?php foreach ($latestReviews as $rev): ?>
                        <div class="review-card fade-in">
                            <div class="review-header">
                                <div class="review-avatar">
                                    <?= htmlspecialchars(mb_substr($rev['user_login'] ?? 'А', 0, 1)) ?>
                                </div>
                                <div class="review-info">
                                    <div class="review-author"><?= htmlspecialchars($rev['user_login'] ?? 'Аноним') ?></div>
                                    <div class="review-date"><?= htmlspecialchars($rev['created_at']) ?></div>
                                </div>
                            </div>
                            <div class="review-text">
                                <strong><?= htmlspecialchars($rev['course'] ?? '') ?></strong><br>
                                <?= htmlspecialchars($rev['text']) ?><br>
                                <?php if (!empty($rev['rating'])): ?>
                                    <small>Оценка: <?= (int) $rev['rating'] ?>/5</small>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Футер -->
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
                <div class="footer-column">
                    <h3>Навигация</h3>
                    <ul class="footer-links">
                        <li><a href="#courses">О курсах</a></li>
                        <li><a href="#reviews">Отзывы</a></li>
                        <li><a href="#footer">Контакты</a></li>
                        <li><a href="prosmotr_zapis.php">Просмотр записей</a></li>
                        <li><a href="zapis.php">Запись на курс</a></li>
                    </ul>
                </div>
            </div>
            <div class="copyright">
                <p>© 2025 Корочки.есть. Все права защищены. <a href="#" style="color: #aaa;">Политика конфиденциальности</a></p>
            </div>
        </div>
    </footer>
    <link rel="stylesheet" href="main.css">
    <script src="main.js"></script>
    <script src="script.js"></script>
    <script src="auth-manager.js"></script>
<script>
    // Автоматическое обновление шапки
    document.addEventListener('DOMContentLoaded', function() {
        authManager.updateHeader();
    });
</script>
</body>
</html>
