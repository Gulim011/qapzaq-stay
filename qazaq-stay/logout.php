<?php
/**
 * Файл: logout.php
 * Назначение: Выход из системы (уничтожение сессии)
 */
session_start();
session_unset();
session_destroy();
header('Location: index.php');
exit;
?>
