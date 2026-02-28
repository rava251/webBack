<?php
// Задание 7: Скрываем ошибки для безопасности в финале, 
// но если хочешь видеть их сейчас, замени 0 на 1
ini_set('display_errors', 1); 
error_reporting(E_ALL);

header('Content-Type: text/html; charset=UTF-8');

// Проверка метода (Задание 3)
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    exit('Доступ разрешен только через форму.');
}

// 1. Валидация (Задание 3)
$errors = [];
if (empty($_POST['fio']) || !preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u', $_POST['fio'])) {
    $errors[] = "Заполните ФИО (только буквы).";
}
if (empty($_POST['phone']) || !preg_match('/^\+?[0-9\s\-]+$/', $_POST['phone'])) {
    $errors[] = "Неверный формат телефона.";
}

if (!empty($errors)) {
    foreach($errors as $err) echo "<p style='color:red;'>$err</p>";
    echo '<a href="index.html">Назад</a>';
    exit();
}

// 2. Подключение (Задание 3)
// Проверь: юзер 'admin', пароль '12345' (или какой ты ставил в MySQL)
try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    // 3. Сохранение анкеты (Задание 3 + 7: SQL Injection protection)
    $stmt = $db->prepare("INSERT INTO applications (fio, email, phone, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_POST['fio'], $_POST['email'], $_POST['phone'], 
        $_POST['birthday'], $_POST['gender'], $_POST['biography']
    ]);
    
    $app_id = $db->lastInsertId();

    // 4. Сохранение языков (Задание 3: Связь 1:M)
    if (!empty($_POST['languages'])) {
        $stmt = $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)");
        foreach ($_POST['languages'] as $lang) {
            $stmt->execute([$app_id, $lang]);
        }
    }

    echo "<h3>Успех! Данные Равана Заура оглы сохранены в базу.</h3>";
    echo "<a href='index.html'>Вернуться</a>";

} catch (PDOException $e) {
    // В случае ошибки выведет текст проблемы вместо 500
    exit("Ошибка базы данных: " . $e->getMessage());
}
