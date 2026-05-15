<?php
/**
 * =====================================================
 * Файл: booking.php
 * Назначение: Страница бронирования отеля
 * =====================================================
 * Принимает hotel_id через GET
 * Сохраняет бронирование в таблицу bookings
 * Требует авторизации
 */
require_once 'db.php';

// Проверяем авторизацию
if (!isLoggedIn()) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

$hotelId = $_GET['hotel_id'] ?? null;
if (!$hotelId) {
    header('Location: hotels.php');
    exit;
}

// Получаем отель
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$hotelId]);
$hotel = $stmt->fetch();

if (!$hotel) {
    header('Location: hotels.php');
    exit;
}

$error = '';
$success = '';

// Обработка бронирования
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $checkIn = $_POST['check_in'] ?? '';
    $checkOut = $_POST['check_out'] ?? '';
    $guests = (int)($_POST['guests'] ?? 1);
    
    if (empty($checkIn) || empty($checkOut)) {
        $error = 'Выберите даты заезда и выезда';
    } elseif (strtotime($checkOut) <= strtotime($checkIn)) {
        $error = 'Дата выезда должна быть позже даты заезда';
    } elseif (strtotime($checkIn) < strtotime(date('Y-m-d'))) {
        $error = 'Дата заезда не может быть в прошлом';
    } else {
        // Считаем количество ночей и итоговую цену
        $nights = ceil((strtotime($checkOut) - strtotime($checkIn)) / 86400);
        $subtotal = $hotel['price_per_night'] * $nights;
        $serviceFee = $subtotal * 0.05;
        $totalPrice = $subtotal + $serviceFee;
        
        // Сохраняем бронирование
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, hotel_id, check_in, check_out, guests, total_price, status) VALUES (?, ?, ?, ?, ?, ?, 'confirmed')");
        
        if ($stmt->execute([$_SESSION['user_id'], $hotelId, $checkIn, $checkOut, $guests, $totalPrice])) {
            header('Location: profile.php?success=1');
            exit;
        } else {
            $error = 'Ошибка при бронировании. Попробуйте позже';
        }
    }
}

// Получаем минимальные даты
$defaultCheckIn = $_GET['check_in'] ?? date('Y-m-d');
$defaultCheckOut = $_GET['check_out'] ?? date('Y-m-d', strtotime('+2 days'));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование — <?= e($hotel['name']) ?></title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
    <div class="container nav-container">
        <a href="index.php" class="logo">
            <i class="fas fa-mountain-sun"></i>
            <span>Qazaq<span class="logo-accent">Stay</span></span>
        </a>
        <ul class="nav-menu">
            <li><a href="index.php">Главная</a></li>
            <li><a href="hotels.php">Все отели</a></li>
            <li><a href="profile.php">Личный кабинет</a></li>
        </ul>
        <div class="nav-actions">
            <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            <span class="user-name"><i class="fas fa-user-circle"></i> <?= e($_SESSION['user_name']) ?></span>
            <a href="logout.php" class="btn-outline">Выход</a>
            <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
        </div>
    </div>
</nav>

<div class="booking-page">
    <div class="container">
        <a href="hotels.php" style="color: var(--text-light); text-decoration: none; margin-bottom: 20px; display: inline-block;">
            <i class="fas fa-arrow-left"></i> Назад к отелям
        </a>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-circle-exclamation"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>
        
        <div class="booking-container">
            <!-- ИНФОРМАЦИЯ ОБ ОТЕЛЕ -->
            <div class="booking-hotel-info">
                <img src="<?= e($hotel['image_url']) ?>" alt="<?= e($hotel['name']) ?>">
                <div class="booking-hotel-details">
                    <div class="hotel-location" style="margin-bottom: 8px;">
                        <i class="fas fa-map-marker-alt"></i> <?= e($hotel['city']) ?>, <?= e($hotel['address']) ?>
                    </div>
                    <h1 style="font-size: 28px; margin-bottom: 12px;"><?= e($hotel['name']) ?></h1>
                    
                    <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                        <span class="hotel-badge" style="position: static; background: #fbbf24; color: #1a1a1a;">
                            <i class="fas fa-star"></i> <?= $hotel['rating'] ?>
                        </span>
                        <span style="color: var(--text-light);">
                            (<?= $hotel['reviews_count'] ?> отзывов)
                        </span>
                    </div>
                    
                    <p style="color: var(--text-light); margin-bottom: 24px; line-height: 1.7;">
                        <?= e($hotel['description']) ?>
                    </p>
                    
                    <h3 style="margin-bottom: 12px;">Удобства</h3>
                    <div class="hotel-amenities" style="margin-bottom: 0;">
                        <?php foreach (explode(',', $hotel['amenities']) as $am): ?>
                            <span class="amenity-tag" style="padding: 8px 14px; font-size: 13px;">
                                <i class="fas fa-check" style="color: var(--primary); margin-right: 4px;"></i>
                                <?= e(trim($am)) ?>
                            </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- ФОРМА БРОНИРОВАНИЯ -->
            <div class="booking-form-card">
                <h2 style="font-size: 22px; margin-bottom: 6px;">
                    <span id="pricePerNight" data-price="<?= $hotel['price_per_night'] ?>">
                        <?= number_format($hotel['price_per_night'], 0, '', ' ') ?> ₸
                    </span>
                </h2>
                <p style="color: var(--text-light); margin-bottom: 24px;">за ночь</p>
                
                <form method="POST">
                    <div class="form-group">
                        <label><i class="fas fa-calendar-check" style="color:var(--primary)"></i> Заезд</label>
                        <input type="date" name="check_in" required min="<?= date('Y-m-d') ?>" value="<?= e($defaultCheckIn) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-xmark" style="color:var(--primary)"></i> Выезд</label>
                        <input type="date" name="check_out" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" value="<?= e($defaultCheckOut) ?>">
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-user-group" style="color:var(--primary)"></i> Гости</label>
                        <select name="guests">
                            <option value="1">1 гость</option>
                            <option value="2" selected>2 гостя</option>
                            <option value="3">3 гостя</option>
                            <option value="4">4 гостя</option>
                            <option value="5">5 гостей</option>
                        </select>
                    </div>
                    
                    <div class="price-summary">
                        <div class="price-row">
                            <span><span id="nightsCount">2</span> ночей × <?= number_format($hotel['price_per_night'], 0, '', ' ') ?> ₸</span>
                            <span id="subtotal"><?= number_format($hotel['price_per_night'] * 2, 0, '', ' ') ?> ₸</span>
                        </div>
                        <div class="price-row">
                            <span>Сервисный сбор (5%)</span>
                            <span id="serviceFee"><?= number_format($hotel['price_per_night'] * 2 * 0.05, 0, '', ' ') ?> ₸</span>
                        </div>
                        <div class="price-row total">
                            <span>Итого</span>
                            <span id="totalPrice"><?= number_format($hotel['price_per_night'] * 2 * 1.05, 0, '', ' ') ?> ₸</span>
                        </div>
                    </div>
                    
                    <input type="hidden" id="totalPriceInput" name="total_price" value="<?= $hotel['price_per_night'] * 2 * 1.05 ?>">
                    
                    <button type="submit" class="btn-primary btn-block btn-large">
                        <i class="fas fa-check"></i> Подтвердить бронирование
                    </button>
                    
                    <p style="text-align: center; color: var(--text-light); font-size: 13px; margin-top: 16px;">
                        <i class="fas fa-shield-halved" style="color: var(--primary)"></i>
                        Безопасная оплата • Бесплатная отмена за 24 часа
                    </p>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
