<?php
/**
 * Файл: admin/bookings.php
 * Назначение: Управление всеми бронированиями
 */
require_once '../db.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Изменение статуса
if (isset($_GET['confirm'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status='confirmed' WHERE id=?");
    $stmt->execute([(int)$_GET['confirm']]);
    header('Location: bookings.php');
    exit;
}
if (isset($_GET['cancel'])) {
    $stmt = $pdo->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
    $stmt->execute([(int)$_GET['cancel']]);
    header('Location: bookings.php');
    exit;
}

$bookings = $pdo->query("
    SELECT b.*, u.name AS user_name, u.email, h.name AS hotel_name, h.city
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN hotels h ON b.hotel_id = h.id
    ORDER BY b.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Бронирования — Админ</title>
    <link rel="stylesheet" href="../style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="admin-layout">
    <aside class="admin-sidebar">
        <a href="../index.php" class="logo">
            <i class="fas fa-mountain-sun"></i>
            <span>Qazaq<span class="logo-accent">Stay</span></span>
        </a>
        <nav class="admin-nav">
            <a href="index.php"><i class="fas fa-gauge-high"></i> Дашборд</a>
            <a href="hotels.php"><i class="fas fa-hotel"></i> Отели</a>
            <a href="bookings.php" class="active"><i class="fas fa-calendar-check"></i> Бронирования</a>
            <a href="users.php"><i class="fas fa-users"></i> Пользователи</a>
            <a href="../index.php"><i class="fas fa-arrow-left"></i> На сайт</a>
            <a href="../logout.php"><i class="fas fa-right-from-bracket"></i> Выход</a>
        </nav>
    </aside>
    
    <main class="admin-content">
        <h1 style="font-size:28px;margin-bottom:32px;">Все бронирования</h1>
        
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Пользователь</th>
                        <th>Email</th>
                        <th>Отель</th>
                        <th>Даты</th>
                        <th>Гости</th>
                        <th>Сумма</th>
                        <th>Статус</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($bookings as $b): ?>
                        <tr>
                            <td>#<?= $b['id'] ?></td>
                            <td><?= e($b['user_name']) ?></td>
                            <td><?= e($b['email']) ?></td>
                            <td><strong><?= e($b['hotel_name']) ?></strong><br><small style="color:var(--text-light)"><?= e($b['city']) ?></small></td>
                            <td><?= date('d.m.Y', strtotime($b['check_in'])) ?> — <?= date('d.m.Y', strtotime($b['check_out'])) ?></td>
                            <td><?= $b['guests'] ?></td>
                            <td><strong><?= number_format($b['total_price'], 0, '', ' ') ?> ₸</strong></td>
                            <td>
                                <span class="status-badge status-<?= $b['status'] ?>">
                                    <?= ['confirmed'=>'Подтв.','pending'=>'Ожидает','cancelled'=>'Отмен.'][$b['status']] ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($b['status'] !== 'confirmed'): ?>
                                    <a href="?confirm=<?= $b['id'] ?>" style="color:#10b981;margin-right:8px;" title="Подтвердить"><i class="fas fa-check"></i></a>
                                <?php endif; ?>
                                <?php if ($b['status'] !== 'cancelled'): ?>
                                    <a href="?cancel=<?= $b['id'] ?>" onclick="return confirm('Отменить?')" style="color:#ef4444;" title="Отменить"><i class="fas fa-times"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($bookings)): ?>
                        <tr><td colspan="9" style="text-align:center;padding:40px;color:var(--text-light);">Бронирований пока нет</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="../script.js"></script>
</body>
</html>
