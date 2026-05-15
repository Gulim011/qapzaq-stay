<?php
/**
 * Файл: admin/users.php
 * Назначение: Просмотр всех пользователей
 */
require_once '../db.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$users = $pdo->query("
    SELECT u.*, COUNT(b.id) AS bookings_count, COALESCE(SUM(b.total_price), 0) AS total_spent
    FROM users u
    LEFT JOIN bookings b ON u.id = b.user_id AND b.status != 'cancelled'
    GROUP BY u.id
    ORDER BY u.created_at DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Пользователи — Админ</title>
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
            <a href="bookings.php"><i class="fas fa-calendar-check"></i> Бронирования</a>
            <a href="users.php" class="active"><i class="fas fa-users"></i> Пользователи</a>
            <a href="../index.php"><i class="fas fa-arrow-left"></i> На сайт</a>
            <a href="../logout.php"><i class="fas fa-right-from-bracket"></i> Выход</a>
        </nav>
    </aside>
    
    <main class="admin-content">
        <h1 style="font-size:28px;margin-bottom:32px;">Пользователи</h1>
        
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Имя</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Роль</th>
                        <th>Бронирований</th>
                        <th>Потрачено</th>
                        <th>Регистрация</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td>#<?= $u['id'] ?></td>
                            <td><strong><?= e($u['name']) ?></strong></td>
                            <td><?= e($u['email']) ?></td>
                            <td><?= e($u['phone']) ?: '—' ?></td>
                            <td>
                                <span class="status-badge" style="background:<?= $u['role']==='admin'?'#fef3c7':'#dbeafe' ?>;color:<?= $u['role']==='admin'?'#92400e':'#1e40af' ?>">
                                    <?= $u['role'] === 'admin' ? 'Админ' : 'Юзер' ?>
                                </span>
                            </td>
                            <td><?= $u['bookings_count'] ?></td>
                            <td><strong><?= number_format($u['total_spent'], 0, '', ' ') ?> ₸</strong></td>
                            <td><?= date('d.m.Y', strtotime($u['created_at'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<script src="../script.js"></script>
</body>
</html>
