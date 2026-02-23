<?php
header('Content-Type: text/html; charset=UTF-8');

// 1. Валидация данных (базовая проверка) [cite: 1-6]
if (empty($_POST['fio']) || empty($_POST['phone']) || empty($_POST['email'])) {
    echo "Пожалуйста, заполните обязательные поля.";
    exit();
}

// 2. Подключение к БД 
$user = 'admin'; // Твой созданный пользователь
$pass = '12345'; // Твой пароль
try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ERRMODE_EXCEPTION => PDO::ERRMODE_EXCEPTION
    ]);

    // 3. Сохранение основной анкеты [cite: 9-10]
    $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthday'], $_POST['gender'], $_POST['biography']]);
    
    $app_id = $db->lastInsertId();

    // 4. Сохранение языков (связь 1:M) [cite: 10-12]
    $stmt = $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)");
    foreach ($_POST['languages'] as $lang) {
        $stmt->execute([$app_id, $lang]);
    }

    echo "<h3>Данные успешно сохранены!</h3>";
    echo "<a href='index.html'>Вернуться назад</a>";
} catch (PDOException $e) {
    echo "Ошибка базы данных: " . $e->getMessage();
}