<?php
/**
 * =====================================================
 * Файл: register.php
 * Назначение: Регистрация новых пользователей
 * =====================================================
 * Сохраняет пользователя в таблицу users
 * Пароли хешируются через password_hash() (BCRYPT)
 */
require_once 'db.php';

// Если пользователь уже авторизован — отправляем на главную
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$error = '';
$success = '';

// Обработка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';
    
    // Валидация
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'Заполните все обязательные поля';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Введите корректный email';
    } elseif (strlen($password) < 6) {
        $error = 'Пароль должен быть не менее 6 символов';
    } elseif ($password !== $passwordConfirm) {
        $error = 'Пароли не совпадают';
    } else {
        // Проверяем не зарегистрирован ли email
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Пользователь с таким email уже зарегистрирован';
        } else {
            // Хешируем пароль и сохраняем пользователя
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $phone, $hashedPassword])) {
                // Автоматический вход после регистрации
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'user';
                
                header('Location: index.php');
                exit;
            } else {
                $error = 'Ошибка регистрации. Попробуйте позже';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Регистрация — Qazaq Stay</title>
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
            <a href="login.php" class="btn-outline">Войти</a>
        </div>
    </div>
</nav>

<div class="auth-page">
    <div class="auth-card animate-fade-in">
        <div class="auth-header">
            <h1>Создать аккаунт</h1>
            <p>Присоединяйтесь к Qazaq Stay и начните путешествовать</p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-circle-exclamation"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST" data-validate>
            <div class="form-group">
                <label>Имя <span style="color:red">*</span></label>
                <input type="text" name="name" placeholder="Айдар Сапаров" required value="<?= e($_POST['name'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Email <span style="color:red">*</span></label>
                <input type="email" name="email" placeholder="example@mail.com" required value="<?= e($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Телефон</label>
                <input type="tel" name="phone" placeholder="+7 (700) 123-45-67" value="<?= e($_POST['phone'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label>Пароль <span style="color:red">*</span></label>
                <input type="password" name="password" placeholder="Минимум 6 символов" required minlength="6">
            </div>
            
            <div class="form-group">
                <label>Подтвердите пароль <span style="color:red">*</span></label>
                <input type="password" name="password_confirm" placeholder="Повторите пароль" required minlength="6">
            </div>
            
            <button type="submit" class="btn-primary btn-block btn-large">
                <i class="fas fa-user-plus"></i> Зарегистрироваться
            </button>
        </form>
        
        <div class="auth-footer">
            Уже есть аккаунт? <a href="login.php">Войти</a>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>
