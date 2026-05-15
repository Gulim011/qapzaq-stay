<?php
/**
 * =====================================================
 * Файл: setup.php
 * Назначение: Первичная настройка (создание администратора)
 * =====================================================
 * ВАЖНО: Запустите этот файл ОДИН РАЗ после импорта БД
 * Адрес: http://localhost/qazaq-stay/setup.php
 * После использования УДАЛИТЕ этот файл!
 */
require_once 'db.php';

$message = '';
$type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Создаем хешированный пароль администратора
    $hashedPassword = password_hash('admin123', PASSWORD_BCRYPT);
    
    // Удаляем старого админа если существует и создаем нового
    $pdo->exec("DELETE FROM users WHERE email = 'admin@qazaqstay.kz'");
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin')");
    
    if ($stmt->execute(['Admin', 'admin@qazaqstay.kz', $hashedPassword])) {
        $message = 'Администратор успешно создан!<br><br>'
                 . '<strong>Логин:</strong> admin@qazaqstay.kz<br>'
                 . '<strong>Пароль:</strong> admin123<br><br>'
                 . '⚠️ <strong>УДАЛИТЕ файл setup.php после использования!</strong>';
        $type = 'success';
    } else {
        $message = 'Ошибка создания администратора';
        $type = 'error';
    }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Установка — Qazaq Stay</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-header">
            <h1><i class="fas fa-gear"></i> Установка</h1>
            <p>Создание администратора Qazaq Stay</p>
        </div>
        
        <?php if ($message): ?>
            <div class="alert alert-<?= $type ?>">
                <?= $message ?>
            </div>
            <div style="text-align:center;margin-top:24px;">
                <a href="login.php" class="btn-primary"><i class="fas fa-right-to-bracket"></i> Перейти ко входу</a>
            </div>
        <?php else: ?>
            <p style="margin-bottom:20px;color:var(--text-light);">
                Нажмите кнопку ниже, чтобы создать учётную запись администратора со следующими данными:
            </p>
            <ul style="margin-bottom:24px;padding-left:20px;color:var(--text);">
                <li><strong>Email:</strong> admin@qazaqstay.kz</li>
                <li><strong>Пароль:</strong> admin123</li>
            </ul>
            <form method="POST">
                <button type="submit" class="btn-primary btn-block btn-large">
                    <i class="fas fa-user-shield"></i> Создать администратора
                </button>
            </form>
            <p style="margin-top:20px;font-size:13px;color:#ef4444;text-align:center;">
                ⚠️ После установки удалите файл <code>setup.php</code> из соображений безопасности
            </p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
