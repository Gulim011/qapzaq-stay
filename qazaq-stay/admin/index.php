<?php
/**
 * =====================================================
 * Файл: admin/index.php
 * Назначение: Дашборд админ-панели
 * =====================================================
 * Доступно только администраторам
 * Показывает статистику и быстрые действия
 */
require_once '../db.php';

// Проверяем что это администратор
if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Собираем статистику
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'user'")->fetchColumn();
$totalHotels = $pdo->query("SELECT COUNT(*) FROM hotels")->fetchColumn();
$totalBookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$totalRevenue = $pdo->query("SELECT COALESCE(SUM(total_price), 0) FROM bookings WHERE status != 'cancelled'")->fetchColumn();

// Последние бронирования
$recentBookings = $pdo->query("
    SELECT b.*, u.name AS user_name, h.name AS hotel_name, h.city
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN hotels h ON b.hotel_id = h.id
    ORDER BY b.created_at DESC LIMIT 10
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель — Qazaq Stay</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-layout">
    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <a href="../index.php" class="logo">
            <i class="fas fa-mountain-sun"></i>
            <span>Qazaq<span class="logo-accent">Stay</span></span>
        </a>
        <nav class="admin-nav">
            <a href="index.php" class="active"><i class="fas fa-gauge-high"></i> Дашборд</a>
            <a href="hotels.php"><i class="fas fa-hotel"></i> Отели</a>
            <a href="bookings.php"><i class="fas fa-calendar-check"></i> Бронирования</a>
            <a href="users.php"><i class="fas fa-users"></i> Пользователи</a>
            <a href="../index.php"><i class="fas fa-arrow-left"></i> На сайт</a>
            <a href="../logout.php"><i class="fas fa-right-from-bracket"></i> Выход</a>
        </nav>
    </aside>
    
    <!-- CONTENT -->
    <main class="admin-content">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;">
            <div>
                <h1 style="font-size:28px;">Дашборд</h1>
                <p style="color:var(--text-light);margin-top:4px;">Добро пожаловать, <?= e($_SESSION['user_name']) ?>!</p>
            </div>
            <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
        </div>
        
        <!-- СТАТИСТИКА -->
        <div class="admin-stats">
            <div class="stat-card">
                <div class="stat-card-icon"><i class="fas fa-users"></i></div>
                <div class="stat-card-value"><?= $totalUsers ?></div>
                <div class="stat-card-label">Пользователей</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background:#fef3c7;color:#92400e;">
                    <i class="fas fa-hotel"></i>
                </div>
                <div class="stat-card-value"><?= $totalHotels ?></div>
                <div class="stat-card-label">Отелей</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background:#dbeafe;color:#1e40af;">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-card-value"><?= $totalBookings ?></div>
                <div class="stat-card-label">Бронирований</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-icon" style="background:#d1fae5;color:#065f46;">
                    <i class="fas fa-tenge-sign"></i>
                </div>
                <div class="stat-card-value"><?= number_format($totalRevenue, 0, '', ' ') ?> ₸</div>
                <div class="stat-card-label">Общая выручка</div>
            </div>
        </div>
        
        <!-- БЫСТРЫЕ ДЕЙСТВИЯ -->
        <div style="display:flex;gap:12px;margin-bottom:32px;flex-wrap:wrap;">
            <a href="hotel_add.php" class="btn-primary">
                <i class="fas fa-plus"></i> Добавить отель
            </a>
            <a href="hotels.php" class="btn-outline">
                <i class="fas fa-list"></i> Управление отелями
            </a>
        </div>
        
        <!-- ПОСЛЕДНИЕ БРОНИРОВАНИЯ -->
        <h2 style="margin-bottom:16px;">Последние бронирования</h2>
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Отель</th>
                        <th>Город</th>
                        <th>Заезд</th>
                        <th>Выезд</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentBookings as $b): ?>
                        <tr>
                            <td>#<?= $b['id'] ?></td>
                            <td><?= e($b['user_name']) ?></td>
                            <td><?= e($b['hotel_name']) ?></td>
                            <td><?= e($b['city']) ?></td>
                            <td><?= date('d.m.Y', strtotime($b['check_in'])) ?></td>
                            <td><?= date('d.m.Y', strtotime($b['check_out'])) ?></td>
                            <td><strong><?= number_format($b['total_price'], 0, '', ' ') ?> ₸</strong></td>
                            <td>
                                <span class="status-badge status-<?= $b['status'] ?>">
                                    <?= ['confirmed'=>'Подтверждено','pending'=>'Ожидает','cancelled'=>'Отменено'][$b['status']] ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($recentBookings)): ?>
                        <tr><td colspan="8" style="text-align:center;padding:40px;color:var(--text-light);">Бронирований пока нет</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="../script.js"></script>
</body>
</html>
