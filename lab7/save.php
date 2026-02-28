<?php
session_start();

// 1. Information Disclosure: Скрываем ошибки от пользователя
ini_set('display_errors', 0);
error_reporting(0);

// 2. CSRF Protection: Проверка токена (если решишь добавить его в форму)
// Если в форме пока нет поля token, этот блок можно временно закомментировать
/*
if (!isset($_POST['token']) || $_POST['token'] !== $_SESSION['token']) {
    die('Security Error: CSRF token invalid');
}
*/

try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 3. SQL Injection: Используем Prepared Statements
    $stmt = $db->prepare("INSERT INTO applications (fio, email, phone, birthday, biography) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['fio'], 
        $_POST['email'], 
        $_POST['phone'], 
        $_POST['birthday'], 
        $_POST['biography']
    ]);

    echo "Данные успешно сохранены в защищенном режиме! <a href='lab7index.php'>Назад</a>";

} catch (PDOException $e) {
    // Пишем реальную ошибку в лог сервера, а не на экран
    error_log("Database error in Lab7: " . $e->getMessage());
    die("Произошла системная ошибка. Данные не сохранены.");
}
