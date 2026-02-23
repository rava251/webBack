<?php
header('Content-Type: text/html; charset=UTF-8');

$errors = FALSE;

// 1. Валидация ФИО [cite: 1-2]
if (empty($_POST['fio']) || !preg_match('/^[a-zA-Zа-яА-ЯёЁ\s]+$/u', $_POST['fio'])) {
    setcookie('fio_error', 'Допустимы только буквы и пробелы', time() + 24 * 3600);
    $errors = TRUE;
}
setcookie('fio_value', $_POST['fio'], time() + 30 * 24 * 3600);

// 2. Валидация Телефона [cite: 3]
if (empty($_POST['phone']) || !preg_match('/^\+?[0-9]{10,12}$/', $_POST['phone'])) {
    setcookie('phone_error', 'Введите номер в формате +79991234567 (10-12 цифр)', time() + 24 * 3600);
    $errors = TRUE;
}
setcookie('phone_value', $_POST['phone'], time() + 30 * 24 * 3600);

// 3. Валидация Email [cite: 4]
if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    setcookie('email_error', 'Некорректный e-mail', time() + 24 * 3600);
    $errors = TRUE;
}
setcookie('email_value', $_POST['email'], time() + 30 * 24 * 3600);

// 4. Валидация Даты [cite: 5]
if (empty($_POST['birthday'])) {
    setcookie('birthday_error', 'Укажите дату рождения', time() + 24 * 3600);
    $errors = TRUE;
}
setcookie('birthday_value', $_POST['birthday'], time() + 30 * 24 * 3600);

// 5. Валидация Языков [cite: 6]
if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
    setcookie('languages_error', 'Выберите хотя бы один язык', time() + 24 * 3600);
    $errors = TRUE;
} else {
    setcookie('languages_value', implode(',', $_POST['languages']), time() + 30 * 24 * 3600);
}

// 6. Валидация Контракта
if (empty($_POST['contract'])) {
    setcookie('contract_error', 'Нужно подтвердить согласие', time() + 24 * 3600);
    $errors = TRUE;
}
setcookie('contract_value', $_POST['contract'], time() + 30 * 24 * 3600);

// Сохраняем остальные значения в куки для автозаполнения
setcookie('gender_value', $_POST['gender'], time() + 30 * 24 * 3600);
setcookie('biography_value', $_POST['biography'], time() + 30 * 24 * 3600);

// Если есть ошибки - возвращаемся методом GET
if ($errors) {
    header('Location: index.php');
    exit();
}

// Если всё ОК - сохраняем в БД [cite: 8-13]
$user = 'admin'; // Твой созданный пользователь
$pass = '12345';
try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', $user, $pass, [
        PDO::ATTR_PERSISTENT => true,
        PDO::ERRMODE_EXCEPTION => PDO::ERRMODE_EXCEPTION
    ]);

    $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthday'], $_POST['gender'], $_POST['biography']]);
    
    $app_id = $db->lastInsertId();
    $stmt = $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)");
    foreach ($_POST['languages'] as $lang) {
        $stmt->execute([$app_id, $lang]);
    }

    // При успехе сохраняем данные на ГОД
    setcookie('fio_value', $_POST['fio'], time() + 365 * 24 * 3600);
    setcookie('phone_value', $_POST['phone'], time() + 365 * 24 * 3600);
    setcookie('email_value', $_POST['email'], time() + 365 * 24 * 3600);
    setcookie('birthday_value', $_POST['birthday'], time() + 365 * 24 * 3600);
    setcookie('gender_value', $_POST['gender'], time() + 365 * 24 * 3600);
    setcookie('biography_value', $_POST['biography'], time() + 365 * 24 * 3600);
    setcookie('languages_value', implode(',', $_POST['languages']), time() + 365 * 24 * 3600);
    setcookie('contract_value', $_POST['contract'], time() + 365 * 24 * 3600);

    echo "<h3>Данные успешно сохранены!</h3>";
    echo "<a href='index.php'>Вернуться назад</a>";
} catch (PDOException $e) {
    echo "Ошибка базы данных: " . $e->getMessage();
}