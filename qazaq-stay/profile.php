<?php
/**
 * =====================================================
 * Файл: profile.php
 * Назначение: Личный кабинет пользователя
 * =====================================================
 * Показывает данные пользователя и его бронирования
 * Позволяет отменить активные бронирования
 */
require_once 'db.php';

if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}

// Отмена бронирования
if (isset($_GET['cancel'])) {
    $bookingId = (int)$_GET['cancel'];
    $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ? AND user_id = ?");
    $stmt->execute([$bookingId, $_SESSION['user_id']]);
    header('Location: profile.php?cancelled=1');
    exit;
}

$user = getCurrentUser();

// Получаем бронирования пользователя
$stmt = $pdo->prepare("
    SELECT b.*, h.name AS hotel_name, h.city, h.image_url, h.address
    FROM bookings b
    JOIN hotels h ON b.hotel_id = h.id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$bookings = $stmt->fetchAll();

// Статистика
$totalBookings = count($bookings);
$activeBookings = count(array_filter($bookings, fn($b) => $b['status'] === 'confirmed' && strtotime($b['check_out']) >= time()));
$totalSpent = array_sum(array_map(fn($b) => $b['status'] !== 'cancelled' ? $b['total_price'] : 0, $bookings));
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Личный кабинет — Qazaq Stay</title>
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
            <li><a href="profile.php" class="active">Личный кабинет</a></li>
            <?php if (isAdmin()): ?>
                <li><a href="admin/index.php">Админ-панель</a></li>
            <?php endif; ?>
        </ul>
        <div class="nav-actions">
            <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            <span class="user-name"><i class="fas fa-user-circle"></i> <?= e($_SESSION['user_name']) ?></span>
            <a href="logout.php" class="btn-outline">Выход</a>
            <button class="mobile-menu-btn" id="mobileMenuBtn"><i class="fas fa-bars"></i></button>
        </div>
    </div>
</nav>

<div class="profile-page">
    <div class="container">
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Бронирование успешно подтверждено!
            </div>
        <?php endif; ?>
        
        <?php if (isset($_GET['cancelled'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i> Бронирование отменено
            </div>
        <?php endif; ?>
        
        <!-- ШАПКА ПРОФИЛЯ -->
        <div class="profile-header">
            <div class="profile-avatar">
                <?= mb_strtoupper(mb_substr($user['name'], 0, 1)) ?>
            </div>
            <div class="profile-info" style="flex: 1;">
                <h2><?= e($user['name']) ?></h2>
                <p style="opacity: 0.9; margin-bottom: 4px;"><i class="fas fa-envelope"></i> <?= e($user['email']) ?></p>
                <?php if ($user['phone']): ?>
                    <p style="opacity: 0.9;"><i class="fas fa-phone"></i> <?= e($user['phone']) ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- СТАТИСТИКА -->
        <div class="admin-stats" style="margin-bottom: 40px;">
            <div class="stat-card">
                <div class="stat-card-icon"><i class="fas fa-bookmark"></i></div>
                <div class="stat-card-value"><?= $totalBookings ?></div>
                <div class="stat-card-label">Всего бронирований</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background: #fef3c7; color: #92400e;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-card-value"><?= $activeBookings ?></div>
                <div class="stat-card-label">Активных бронирований</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background: #d1fae5; color: #065f46;">
                    <i class="fas fa-tenge-sign"></i>
                </div>
                <div class="stat-card-value"><?= number_format($totalSpent, 0, '', ' ') ?> ₸</div>
                <div class="stat-card-label">Общая сумма</div>
            </div>
        </div>
        
        <!-- БРОНИРОВАНИЯ -->
        <h2 style="margin-bottom: 24px; font-size: 26px;">Мои бронирования</h2>
        
        <?php if (empty($bookings)): ?>
            <div style="text-align: center; padding: 60px 20px; background: var(--bg-card); border-radius: var(--radius-md); border: 1px solid var(--border);">
                <i class="fas fa-suitcase-rolling" style="font-size: 64px; color: var(--text-muted); margin-bottom: 20px;"></i>
                <h3>У вас пока нет бронирований</h3>
                <p style="color: var(--text-light); margin-top: 10px; margin-bottom: 24px;">Начните планировать своё следующее путешествие!</p>
                <a href="hotels.php" class="btn-primary">
                    <i class="fas fa-search"></i> Найти отель
                </a>
            </div>
        <?php else: ?>
            <div class="booking-list">
                <?php foreach ($bookings as $booking): ?>
                    <?php
                        $nights = ceil((strtotime($booking['check_out']) - strtotime($booking['check_in'])) / 86400);
                        $isPast = strtotime($booking['check_out']) < time();
                    ?>
                    <div class="booking-item">
                        <img src="<?= e($booking['image_url']) ?>" alt="<?= e($booking['hotel_name']) ?>">
                        <div>
                            <h3><?= e($booking['hotel_name']) ?></h3>
                            <div class="hotel-location" style="margin-bottom: 12px;">
                                <i class="fas fa-map-marker-alt"></i> <?= e($booking['city']) ?>, <?= e($booking['address']) ?>
                            </div>
                            <div class="booking-details">
                                <span><i class="fas fa-calendar"></i> <?= date('d.m.Y', strtotime($booking['check_in'])) ?> — <?= date('d.m.Y', strtotime($booking['check_out'])) ?></span>
                                <span><i class="fas fa-moon"></i> <?= $nights ?> ночей</span>
                                <span><i class="fas fa-user-group"></i> <?= $booking['guests'] ?> гостей</span>
                            </div>
                            <div style="margin-top: 10px;">
                                <span class="status-badge status-<?= $booking['status'] ?>">
                                    <?php
                                    $statusText = ['confirmed' => 'Подтверждено', 'pending' => 'Ожидает', 'cancelled' => 'Отменено'];
                                    echo $statusText[$booking['status']];
                                    ?>
                                </span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 24px; font-weight: 800; margin-bottom: 12px;">
                                <?= number_format($booking['total_price'], 0, '', ' ') ?> ₸
                            </div>
                            <?php if ($booking['status'] === 'confirmed' && !$isPast): ?>
                                <a href="?cancel=<?= $booking['id'] ?>" 
                                   class="btn-outline" 
                                   onclick="return confirm('Отменить бронирование?')">
                                    Отменить
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
