<?php
/**
 * =====================================================
 * Файл: admin/hotels.php
 * Назначение: Управление отелями (список + удаление)
 * =====================================================
 */
require_once '../db.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

// Удаление отеля
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM hotels WHERE id = ?");
    $stmt->execute([(int)$_GET['delete']]);
    header('Location: hotels.php?deleted=1');
    exit;
}

$hotels = $pdo->query("SELECT * FROM hotels ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отели — Админ-панель</title>
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
            <a href="hotels.php" class="active"><i class="fas fa-hotel"></i> Отели</a>
            <a href="bookings.php"><i class="fas fa-calendar-check"></i> Бронирования</a>
            <a href="users.php"><i class="fas fa-users"></i> Пользователи</a>
            <a href="../index.php"><i class="fas fa-arrow-left"></i> На сайт</a>
            <a href="../logout.php"><i class="fas fa-right-from-bracket"></i> Выход</a>
        </nav>
    </aside>
    
    <main class="admin-content">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;">
            <h1 style="font-size:28px;">Управление отелями</h1>
            <a href="hotel_add.php" class="btn-primary">
                <i class="fas fa-plus"></i> Добавить отель
            </a>
        </div>
        
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> Отель удален</div>
        <?php endif; ?>
        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> Отель добавлен</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> Отель обновлен</div>
        <?php endif; ?>
        
        <div style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Фото</th>
                        <th>Название</th>
                        <th>Город</th>
                        <th>Цена</th>
                        <th>Рейтинг</th>
                        <th>Действия</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($hotels as $h): ?>
                        <tr>
                            <td>#<?= $h['id'] ?></td>
                            <td><img src="<?= e($h['image_url']) ?>" alt=""></td>
                            <td><strong><?= e($h['name']) ?></strong></td>
                            <td><?= e($h['city']) ?></td>
                            <td><?= number_format($h['price_per_night'], 0, '', ' ') ?> ₸</td>
                            <td><i class="fas fa-star" style="color:#fbbf24"></i> <?= $h['rating'] ?></td>
                            <td>
                                <a href="hotel_edit.php?id=<?= $h['id'] ?>" 
                                   style="color:var(--primary);margin-right:12px;" title="Редактировать">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <a href="?delete=<?= $h['id'] ?>" 
                                   onclick="return confirm('Удалить отель?')" 
                                   style="color:#ef4444;" title="Удалить">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
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
