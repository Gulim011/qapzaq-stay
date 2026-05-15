<?php
/**
 * Файл: admin/hotel_add.php
 * Назначение: Добавление нового отеля
 */
require_once '../db.php';

if (!isAdmin()) {
    header('Location: ../login.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image_url = trim($_POST['image_url'] ?? '');
    $price = (float)($_POST['price_per_night'] ?? 0);
    $rating = (float)($_POST['rating'] ?? 0);
    $amenities = trim($_POST['amenities'] ?? '');
    $rooms = (int)($_POST['rooms_available'] ?? 10);
    
    if (empty($name) || empty($city) || $price <= 0) {
        $error = 'Заполните все обязательные поля';
    } else {
        $stmt = $pdo->prepare("INSERT INTO hotels (name, city, address, description, image_url, price_per_night, rating, amenities, rooms_available) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $city, $address, $description, $image_url, $price, $rating, $amenities, $rooms])) {
            header('Location: hotels.php?added=1');
            exit;
        }
        $error = 'Ошибка добавления';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добавить отель</title>
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
            Добавить отель
        </h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><i class="fas fa-circle-exclamation"></i> <?= e($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" style="background:var(--bg-card);padding:32px;border-radius:var(--radius-md);border:1px solid var(--border);max-width:800px;">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Название <span style="color:red">*</span></label>
                    <input type="text" name="name" required>
                </div>
                <div class="form-group">
                    <label>Город <span style="color:red">*</span></label>
                    <input type="text" name="city" required placeholder="Например: Астана">
                </div>
            </div>
            
            <div class="form-group">
                <label>Адрес</label>
                <input type="text" name="address" placeholder="ул. Достык, 16">
            </div>
            
            <div class="form-group">
                <label>Описание</label>
                <textarea name="description" rows="4" placeholder="Краткое описание отеля..."></textarea>
            </div>
            
            <div class="form-group">
                <label>URL фотографии</label>
                <input type="url" name="image_url" placeholder="https://images.unsplash.com/...">
            </div>
            
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                <div class="form-group">
                    <label>Цена за ночь (₸) <span style="color:red">*</span></label>
                    <input type="number" name="price_per_night" required min="0" step="100">
                </div>
                <div class="form-group">
                    <label>Рейтинг (0-5)</label>
                    <input type="number" name="rating" min="0" max="5" step="0.1" value="4.5">
                </div>
                <div class="form-group">
                    <label>Свободные номера</label>
                    <input type="number" name="rooms_available" min="0" value="10">
                </div>
            </div>
            
            <div class="form-group">
                <label>Удобства (через запятую)</label>
                <input type="text" name="amenities" placeholder="Wi-Fi,Бассейн,Спа,Ресторан,Парковка">
            </div>
            
            <div style="display:flex;gap:12px;">
                <button type="submit" class="btn-primary btn-large">
                    <i class="fas fa-plus"></i> Добавить отель
                </button>
                <a href="hotels.php" class="btn-outline btn-large">Отмена</a>
            </div>
        </form>
    </main>
</div>

<script src="../script.js"></script>
</body>
</html>
