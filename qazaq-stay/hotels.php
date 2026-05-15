<?php
/**
 * =====================================================
 * Файл: hotels.php
 * Назначение: Каталог всех отелей с фильтрами
 * =====================================================
 * Поддерживает фильтры:
 * - по городу (city)
 * - по цене (price_min, price_max)
 * - по рейтингу (min_rating)
 * - сортировка (sort: price_asc/price_desc/rating)
 */
require_once 'db.php';

// Получаем параметры фильтрации из URL
$city = $_GET['city'] ?? '';
$minPrice = $_GET['price_min'] ?? '';
$maxPrice = $_GET['price_max'] ?? '';
$minRating = $_GET['min_rating'] ?? '';
$sort = $_GET['sort'] ?? 'rating';
$checkIn = $_GET['check_in'] ?? '';
$checkOut = $_GET['check_out'] ?? '';

// Строим запрос с фильтрами
$query = "SELECT * FROM hotels WHERE 1=1";
$params = [];

if ($city) {
    $query .= " AND city = ?";
    $params[] = $city;
}
if ($minPrice !== '') {
    $query .= " AND price_per_night >= ?";
    $params[] = $minPrice;
}
if ($maxPrice !== '') {
    $query .= " AND price_per_night <= ?";
    $params[] = $maxPrice;
}
if ($minRating !== '') {
    $query .= " AND rating >= ?";
    $params[] = $minRating;
}

// Сортировка
switch ($sort) {
    case 'price_asc':  $query .= " ORDER BY price_per_night ASC"; break;
    case 'price_desc': $query .= " ORDER BY price_per_night DESC"; break;
    case 'rating':     $query .= " ORDER BY rating DESC, reviews_count DESC"; break;
    default:           $query .= " ORDER BY rating DESC"; break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$hotels = $stmt->fetchAll();

// Получаем список городов для фильтра
$citiesStmt = $pdo->query("SELECT DISTINCT city FROM hotels ORDER BY city");
$allCities = $citiesStmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $city ? 'Отели в ' . e($city) : 'Все отели' ?> — Qazaq Stay</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar">
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-mountain-sun"></i>
            <span>Qazaq<span class="logo-accent">Stay</span></span>
        </a>
        <ul class="nav-menu">
            <li><a href="index.php">Главная</a></li>
            <li><a href="hotels.php" class="active">Все отели</a></li>
            <?php if (isLoggedIn()): ?>
                <li><a href="profile.php">Личный кабинет</a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-actions">
            <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            <?php if (isLoggedIn()): ?>
                <span class="user-name"><i class="fas fa-user-circle"></i> <?= e($_SESSION['user_name']) ?></span>
                <a href="logout.php" class="btn-outline">Выход</a>
            <?php else: ?>
                <a href="login.php" class="btn-outline">Войти</a>
                <a href="register.php" class="btn-primary">Регистрация</a>
            <?php endif; ?>
            <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
        </div>
    </div>
</nav>

<!-- ЗАГОЛОВОК -->
<div class="page-header">
    <div class="container">
        <h1><?= $city ? 'Отели в городе ' . e($city) : 'Все отели Казахстана' ?></h1>
        <p>Найдено <?= count($hotels) ?> отелей</p>
    </div>
</div>

<!-- ОСНОВНОЙ КОНТЕНТ -->
<div class="container" style="padding: 40px 24px;">
    
    <!-- ФИЛЬТРЫ -->
    <form class="filter-bar" method="GET">
        <div class="form-group">
            <label>Город</label>
            <select name="city">
                <option value="">Все города</option>
                <?php foreach ($allCities as $c): ?>
                    <option value="<?= e($c) ?>" <?= $city === $c ? 'selected' : '' ?>><?= e($c) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label>Цена от (₸)</label>
            <input type="number" name="price_min" placeholder="От" value="<?= e($minPrice) ?>">
        </div>
        
        <div class="form-group">
            <label>Цена до (₸)</label>
            <input type="number" name="price_max" placeholder="До" value="<?= e($maxPrice) ?>">
        </div>
        
        <div class="form-group">
            <label>Мин. рейтинг</label>
            <select name="min_rating">
                <option value="">Любой</option>
                <option value="4.5" <?= $minRating === '4.5' ? 'selected' : '' ?>>4.5+</option>
                <option value="4.0" <?= $minRating === '4.0' ? 'selected' : '' ?>>4.0+</option>
                <option value="3.5" <?= $minRating === '3.5' ? 'selected' : '' ?>>3.5+</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>Сортировка</label>
            <select name="sort">
                <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>По рейтингу</option>
                <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Сначала дешевле</option>
                <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Сначала дороже</option>
            </select>
        </div>
        
        <?php if ($checkIn): ?>
            <input type="hidden" name="check_in" value="<?= e($checkIn) ?>">
            <input type="hidden" name="check_out" value="<?= e($checkOut) ?>">
        <?php endif; ?>
        
        <button type="submit" class="btn-primary">
            <i class="fas fa-filter"></i> Применить
        </button>
    </form>
    
    <!-- СПИСОК ОТЕЛЕЙ -->
    <?php if (empty($hotels)): ?>
        <div style="text-align: center; padding: 80px 20px;">
            <i class="fas fa-hotel" style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px;"></i>
            <h2>Отели не найдены</h2>
            <p style="color: var(--text-light); margin-top: 10px;">Попробуйте изменить параметры поиска</p>
            <a href="hotels.php" class="btn-primary" style="margin-top: 24px; display: inline-flex;">Сбросить фильтры</a>
        </div>
    <?php else: ?>
        <div class="hotels-grid">
            <?php foreach ($hotels as $hotel): ?>
                <div class="hotel-card">
                    <div class="hotel-image">
                        <img src="<?= e($hotel['image_url']) ?>" alt="<?= e($hotel['name']) ?>" loading="lazy">
                        <span class="hotel-badge"><i class="fas fa-star"></i> <?= $hotel['rating'] ?></span>
                        <button class="favorite-btn"><i class="far fa-heart"></i></button>
                    </div>
                    <div class="hotel-info">
                        <div class="hotel-location">
                            <i class="fas fa-map-marker-alt"></i> <?= e($hotel['city']) ?>, <?= e($hotel['address']) ?>
                        </div>
                        <h3 class="hotel-name"><?= e($hotel['name']) ?></h3>
                        <p class="hotel-description"><?= e(mb_substr($hotel['description'], 0, 100)) ?>...</p>
                        <div class="hotel-amenities">
                            <?php foreach (array_slice(explode(',', $hotel['amenities']), 0, 4) as $am): ?>
                                <span class="amenity-tag"><?= e(trim($am)) ?></span>
                            <?php endforeach; ?>
                        </div>
                        <div class="hotel-footer">
                            <div class="hotel-price">
                                <strong><?= number_format($hotel['price_per_night'], 0, '', ' ') ?> ₸</strong>
                                <span>/ночь</span>
                            </div>
                            <a href="booking.php?hotel_id=<?= $hotel['id'] ?><?= $checkIn ? '&check_in='.urlencode($checkIn).'&check_out='.urlencode($checkOut) : '' ?>" class="btn-primary">
                                Забронировать
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script src="script.js"></script>
</body>
</html>
