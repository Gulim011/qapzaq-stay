<?php
/**
 * =====================================================
 * Файл: login.php
 * Назначение: Авторизация пользователей
 * =====================================================
 * Проверяет email и пароль (password_verify)
 * Сохраняет данные в $_SESSION
 */
require_once 'db.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Заполните все поля';
    } else {
        // Ищем пользователя
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Проверяем пароль
        if ($user && password_verify($password, $user['password'])) {
            // Сохраняем данные в сессию
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            
            // Если админ — отправляем в админ-панель
            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Неверный email или пароль';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — Qazaq Stay</title>
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
        <div class="nav-actions">
            <button class="theme-toggle" id="themeToggle"><i class="fas fa-moon"></i></button>
            <a href="register.php" class="btn-outline">Регистрация</a>
        </div>
    </div>
</nav>

<div class="auth-page">
    <div class="auth-card animate-fade-in">
        <div class="auth-header">
            <h1>С возвращением!</h1>
            <p>Войдите в свой аккаунт Qazaq Stay</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-circle-exclamation"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" data-validate>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" placeholder="example@mail.com" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Пароль</label>
                <input type="password" name="password" placeholder="Введите пароль" required>
            </div>
            
            <button type="submit" class="btn-primary btn-block btn-large">
                <i class="fas fa-right-to-bracket"></i> Войти
            </button>
        </form>
        
        <div class="auth-footer">
            Нет аккаунта? <a href="register.php">Зарегистрироваться</a>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
