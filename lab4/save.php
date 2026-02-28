<?php
header('Content-Type: text/html; charset=UTF-8');

// 1. Проверка метода
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header('Location: index.php');
    exit();
}

$errors = false;

// 2. Валидация ФИО
if (empty($_POST['fio']) || !preg_match('/^[a-zA-Zа-яА-ЯёЁ\s\-]+$/u', $_POST['fio'])) {
    setcookie('fio_error', 'Введите корректное ФИО (только буквы)', time() + 24 * 3600);
    $errors = true;
}
setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 3600 * 12);

// 3. Валидация Телефона
if (empty($_POST['phone']) || !preg_match('/^\+?[0-9\s\-\(\)]+$/', $_POST['phone'])) {
    setcookie('phone_error', 'Неверный формат телефона', time() + 24 * 3600);
    $errors = true;
}
setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 3600 * 12);

// 4. Валидация Email
if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', 'Некорректный email', time() + 24 * 3600);
    $errors = true;
}
setcookie('email_value', $_POST['email'], time() + 30 * 24 * 3600 * 12);

// 5. Валидация Языков
if (empty($_POST['languages'])) {
    setcookie('languages_error', 'Выберите хотя бы один язык', time() + 24 * 3600);
    $errors = true;
} else {
    setcookie('languages_value', implode(',', $_POST['languages']), time() + 30 * 24 * 3600 * 12);
}

// Сохраняем остальные значения, чтобы не пропали
setcookie('birthday_value', $_POST['birthday'], time() + 30 * 24 * 3600 * 12);
setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 3600 * 12);
setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 3600 * 12);
setcookie('contract_value', isset($_POST['contract']) ? '1' : '', time() + 30 * 24 * 3600 * 12);

// 6. Если есть ошибки — редирект (Задание 4)
if ($errors) {
    header('Location: index.php');
    exit();
}

// 7. Если ошибок нет — запись в БД (Задание 3)
try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthday'], $_POST['gender'], $_POST['biography']]);

    $app_id = $db->lastInsertId();

    $stmt = $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)");
    foreach ($_POST['languages'] as $lang) {
        $stmt->execute([$app_id, $lang]);
    }

    echo "<h3>Данные успешно сохранены!</h3><a href='index.php'>Назад</a>";

} catch (PDOException $e) {
    exit("Ошибка базы данных: " . $e->getMessage());
}

