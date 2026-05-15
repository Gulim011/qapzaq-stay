<?php
/**
 * =====================================================
 * Файл: index.php
 * Назначение: Главная страница сайта Qazaq Stay
 * =====================================================
 * Отображает: navbar, hero-баннер с поиском,
 * популярные города, рекомендованные отели
 */
require_once 'db.php';

// Получаем список всех уникальных городов из БД
$citiesStmt = $pdo->query("SELECT DISTINCT city FROM hotels ORDER BY city");
$cities = $citiesStmt->fetchAll(PDO::FETCH_COLUMN);

// Получаем топ-8 отелей по рейтингу для главной страницы
$topHotelsStmt = $pdo->query("SELECT * FROM hotels ORDER BY rating DESC, reviews_count DESC LIMIT 8");
$topHotels = $topHotelsStmt->fetchAll();

// Получаем количество отелей для каждого города (для блока "Популярные направления")
$cityStatsStmt = $pdo->query("SELECT city, COUNT(*) as count, MIN(image_url) as image FROM hotels GROUP BY city ORDER BY count DESC LIMIT 8");
$cityStats = $cityStatsStmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Qazaq Stay — Бронирование отелей в Казахстане</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- ============ NAVBAR ============ -->
<nav class="navbar">
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-mountain-sun"></i>
            <span>Qazaq<span class="logo-accent">Stay</span></span>
        </a>
        
        <ul class="nav-menu">
            <li><a href="index.php" class="active">Главная</a></li>
            <li><a href="hotels.php">Все отели</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="profile.php">Личный кабинет</a></li>
                <?php if (isAdmin()): ?>
                    <li><a href="admin/index.php">Админ-панель</a></li>
                <?php endif; ?>
            <?php endif; ?>
        </ul>
        
        <div class="nav-actions">
            <button class="theme-toggle" id="themeToggle" title="Сменить тему">
                <i class="fas fa-moon"></i>
            </button>
            
            <?php if (isLoggedIn()): ?>
                <div class="user-menu">
                    <span class="user-name"><i class="fas fa-user-circle"></i> <?= e($_SESSION['user_name']) ?></span>
                    <a href="logout.php" class="btn-outline">Выход</a>
                </div>
            <?php else: ?>
                <a href="login.php" class="btn-outline">Войти</a>
                <a href="register.php" class="btn-primary">Регистрация</a>
            <?php endif; ?>
            
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
        </div>
    </div>
</nav>

<!-- ============ HERO БАННЕР С ПОИСКОМ ============ -->
<section class="hero">
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <h1 class="hero-title animate-fade-in">
            Откройте красоту <span class="text-gradient">Казахстана</span>
        </h1>
        <p class="hero-subtitle animate-fade-in-delay">
            Бронируйте лучшие отели от Астаны до Алматы по выгодным ценам
        </p>
        
        <!-- Форма поиска -->
        <form class="search-box animate-slide-up" action="hotels.php" method="GET">
            <div class="search-field">
                <label><i class="fas fa-map-marker-alt"></i> Город</label>
                <select name="city" required>
                    <option value="">Выберите город</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= e($city) ?>"><?= e($city) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="search-field">
                <label><i class="fas fa-calendar-check"></i> Заезд</label>
                <input type="date" name="check_in" required min="<?= date('Y-m-d') ?>">
            </div>
            
            <div class="search-field">
                <label><i class="fas fa-calendar-xmark"></i> Выезд</label>
                <input type="date" name="check_out" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>">
            </div>
            
            <div class="search-field">
                <label><i class="fas fa-user-group"></i> Гости</label>
                <select name="guests">
                    <option value="1">1 гость</option>
                    <option value="2" selected>2 гостя</option>
                    <option value="3">3 гостя</option>
                    <option value="4">4 гостя</option>
                    <option value="5">5+ гостей</option>
                </select>
            </div>
            
            <button type="submit" class="search-btn">
                <i class="fas fa-search"></i> Найти
            </button>
        </form>
        
        <!-- Статистика -->
        <div class="hero-stats">
            <div class="stat-item">
                <strong>500+</strong>
                <span>Отелей</span>
            </div>
            <div class="stat-item">
                <strong>14+</strong>
                <span>Городов</span>
            </div>
            <div class="stat-item">
                <strong>10К+</strong>
                <span>Довольных гостей</span>
            </div>
        </div>
    </div>
</section>

<!-- ============ ПОПУЛЯРНЫЕ НАПРАВЛЕНИЯ ============ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Популярные направления</h2>
            <p class="section-subtitle">Самые востребованные города для путешествий</p>
        </div>
        
        <div class="cities-grid">
            <?php foreach ($cityStats as $cityStat): ?>
                <a href="hotels.php?city=<?= urlencode($cityStat['city']) ?>" class="city-card">
                    <img src="<?= e($cityStat['image']) ?>" alt="<?= e($cityStat['city']) ?>" loading="lazy">
                    <div class="city-card-overlay">
                        <h3><?= e($cityStat['city']) ?></h3>
                        <p><?= $cityStat['count'] ?> отелей</p>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ ЛУЧШИЕ ОТЕЛИ ============ -->
<section class="section section-alt">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Лучшие отели Казахстана</h2>
            <p class="section-subtitle">Отели с самым высоким рейтингом</p>
        </div>
        
        <div class="hotels-grid">
            <?php foreach ($topHotels as $hotel): ?>
                <div class="hotel-card">
                    <div class="hotel-image">
                        <img src="<?= e($hotel['image_url']) ?>" alt="<?= e($hotel['name']) ?>" loading="lazy">
                        <span class="hotel-badge"><i class="fas fa-star"></i> <?= $hotel['rating'] ?></span>
                        <button class="favorite-btn" title="В избранное"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="hotel-info">
                        <div class="hotel-location">
                            <i class="fas fa-map-marker-alt"></i> <?= e($hotel['city']) ?>
                        </div>
                        <h3 class="hotel-name"><?= e($hotel['name']) ?></h3>
                        <p class="hotel-description"><?= e(mb_substr($hotel['description'], 0, 90)) ?>...</p>
                        <div class="hotel-amenities">
                            <?php 
                            $amenities = explode(',', $hotel['amenities']);
                            foreach (array_slice($amenities, 0, 3) as $am): 
                            ?>
                                <span class="amenity-tag"><?= e(trim($am)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="hotel-footer">
                            <div class="hotel-price">
                                <strong><?= number_format($hotel['price_per_night'], 0, '', ' ') ?> ₸</strong>
                                <span>/ночь</span>
                            </div>
                            <a href="booking.php?hotel_id=<?= $hotel['id'] ?>" class="btn-primary">
                                Забронировать
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center" style="margin-top: 40px;">
            <a href="hotels.php" class="btn-outline btn-large">
                Посмотреть все отели <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    </div>
</section>

<!-- ============ ПРЕИМУЩЕСТВА ============ -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Почему Qazaq Stay?</h2>
            <p class="section-subtitle">Мы делаем путешествия проще и удобнее</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-shield-halved"></i></div>
                <h3>Безопасные платежи</h3>
                <p>Защищенные транзакции и гарантия возврата средств</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-tag"></i></div>
                <h3>Лучшие цены</h3>
                <p>Гарантия лучшей цены и эксклюзивные скидки для пользователей</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-headset"></i></div>
                <h3>Поддержка 24/7</h3>
                <p>Наша команда всегда на связи и готова помочь</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon"><i class="fas fa-bolt"></i></div>
                <h3>Мгновенное подтверждение</h3>
                <p>Бронирование подтверждается за секунды</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ FOOTER ============ -->
<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <a href="index.php" class="logo">
                    <i class="fas fa-mountain-sun"></i>
                    <span>Qazaq<span class="logo-accent">Stay</span></span>
                </a>
                <p>Лучший сервис бронирования отелей в Казахстане. Откройте красоту нашей страны с нами.</p>
            </div>
            <div class="footer-section">
                <h4>Компания</h4>
                <ul>
                    <li><a href="#">О нас</a></li>
                    <li><a href="#">Карьера</a></li>
                    <li><a href="#">Партнерам</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Поддержка</h4>
                <ul>
                    <li><a href="#">Центр помощи</a></li>
                    <li><a href="#">Правила бронирования</a></li>
                    <li><a href="#">Контакты</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Контакты</h4>
                <p><i class="fas fa-envelope"></i> info@qazaqstay.kz</p>
                <p><i class="fas fa-phone"></i> +7 (700) 123-45-67</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-telegram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>© 2025 Qazaq Stay. Все права защищены.</p>
        </div>
    </div>
</footer>

<script src="script.js"></script>
</body>
</html>
