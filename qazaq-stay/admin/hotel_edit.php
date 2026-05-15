<?php
/**
 * Файл: admin/hotel_edit.php
 * Назначение: Редактирование существующего отеля
 */
require_once '../db.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM hotels WHERE id = ?");
$stmt->execute([$id]);
$hotel = $stmt->fetch();

if (!$hotel) {
    header('Location: hotels.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $pdo->prepare("UPDATE hotels SET name=?, city=?, address=?, description=?, image_url=?, price_per_night=?, rating=?, amenities=?, rooms_available=? WHERE id=?");
    
    if ($stmt->execute([
        trim($_POST['name']),
        trim($_POST['city']),
        trim($_POST['address']),
        trim($_POST['description']),
        trim($_POST['image_url']),
        (float)$_POST['price_per_night'],
        (float)$_POST['rating'],
        trim($_POST['amenities']),
        (int)$_POST['rooms_available'],
        $id
    ])) {
        header('Location: hotels.php?updated=1');
        exit;
    }
    $error = 'Ошибка обновления';
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать отель</title>
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
        <h1 style="font-size:28px;margin-bottom:32px;">
            <a href="hotels.php" style="color:var(--text-light);"><i class="fas fa-arrow-left"></i></a>
            Редактировать: <?= e($hotel['name']) ?>
        </h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> <?= e($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" style="background:var(--bg-card);padding:32px;border-radius:var(--radius-md);border:1px solid var(--border);max-width:800px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Название</label>
                    <input type="text" name="name" required value="<?= e($hotel['name']) ?>">
                </div>
                <div class="form-group">
                    <label>Город</label>
                    <input type="text" name="city" required value="<?= e($hotel['city']) ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Адрес</label>
                <input type="text" name="address" value="<?= e($hotel['address']) ?>">
            </div>
            
            <div class="form-group">
                <label>Описание</label>
                <textarea name="description" rows="4"><?= e($hotel['description']) ?></textarea>
            </div>
            
            <div class="form-group">
                <label>URL фотографии</label>
                <input type="url" name="image_url" value="<?= e($hotel['image_url']) ?>">
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Цена за ночь (₸)</label>
                    <input type="number" name="price_per_night" required min="0" step="100" value="<?= $hotel['price_per_night'] ?>">
                </div>
                <div class="form-group">
                    <label>Рейтинг</label>
                    <input type="number" name="rating" min="0" max="5" step="0.1" value="<?= $hotel['rating'] ?>">
                </div>
                <div class="form-group">
                    <label>Свободные номера</label>
                    <input type="number" name="rooms_available" min="0" value="<?= $hotel['rooms_available'] ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Удобства (через запятую)</label>
                <input type="text" name="amenities" value="<?= e($hotel['amenities']) ?>">
            </div>
            
            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn-primary btn-large"><i class="fas fa-save"></i> Сохранить</button>
                <a href="hotels.php" class="btn-outline btn-large">Отмена</a>
            </div>
        </form>
    </main>
</div>

<script src="../script.js"></script>
</body>
</html>
