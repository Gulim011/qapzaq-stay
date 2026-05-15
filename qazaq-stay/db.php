<?php
/**
 * =====================================================
 * Файл: db.php
 * Назначение: Подключение к базе данных MySQL
 * =====================================================
 * Этот файл подключается ко всем PHP-страницам через require_once
 * При запуске через XAMPP параметры по умолчанию подходят
 */

// Параметры подключения к MySQL (стандартные для XAMPP)
$host = 'localhost';        // Хост MySQL (для XAMPP всегда localhost)
$dbname = 'qazaq_stay';     // Имя базы данных
$username = 'root';         // Пользователь MySQL (root - по умолчанию в XAMPP)
$password = '';             // Пароль (пустой по умолчанию в XAMPP)

try {
    // Создаем PDO-соединение с MySQL
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,        // Включаем исключения при ошибках
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,   // Возвращаем массивы по умолчанию
            PDO::ATTR_EMULATE_PREPARES => false                 // Используем настоящие подготовленные запросы
        ]
    );
} catch (PDOException $e) {
    // Если соединение не удалось — выводим ошибку
    die("Ошибка подключения к БД: " . $e->getMessage() . 
        "<br><br>Убедитесь что:<br>1. XAMPP запущен (Apache + MySQL)<br>" .
        "2. База данных 'qazaq_stay' импортирована через phpMyAdmin");
}

// Запускаем сессию (для авторизации пользователей)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Функция: проверка авторизован ли пользователь
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Функция: проверка является ли пользователь администратором
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * Функция: получить текущего пользователя
 */
function getCurrentUser() {
    global $pdo;
    if (!isLoggedIn()) return null;
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

/**
 * Функция: безопасный вывод данных (защита от XSS)
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>
