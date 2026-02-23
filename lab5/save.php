<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] != 'POST') exit;

try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345', [
        PDO::ATTR_PERSISTENT => true, PDO::ERRMODE_EXCEPTION => PDO::ERRMODE_EXCEPTION
    ]);

    if (!empty($_SESSION['login'])) {
        $app_id = $_SESSION['app_id'];
        // Обновляем анкету
        $stmt = $db->prepare("UPDATE applications SET fio=?, phone=?, email=?, birthday=?, gender=?, biography=? WHERE id=?");
        $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthday'], $_POST['gender'], $_POST['biography'], $app_id]);

        // Обновляем языки
        $db->prepare("DELETE FROM languages WHERE application_id = ?")->execute([$app_id]);
        $stmt = $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)");
        if (!empty($_POST['languages'])) {
            foreach ($_POST['languages'] as $lang) { $stmt->execute([$app_id, $lang]); }
        }
        echo "Данные обновлены! <a href='index.php'>Вернуться</a>";
    } else {
        // Создаем новую запись
        $stmt = $db->prepare("INSERT INTO applications (fio, phone, email, birthday, gender, biography) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['fio'], $_POST['phone'], $_POST['email'], $_POST['birthday'], $_POST['gender'], $_POST['biography']]);
        $app_id = $db->lastInsertId();

        if (!empty($_POST['languages'])) {
            $stmt = $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)");
            foreach ($_POST['languages'] as $lang) { $stmt->execute([$app_id, $lang]); }
        }

        // Генерация доступов
        $login = 'user' . $app_id;
        $password = bin2hex(random_bytes(4));
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (login, password, application_id) VALUES (?, ?, ?)");
        $stmt->execute([$login, $hash, $app_id]);

        echo "<!DOCTYPE html><html><head><link rel='stylesheet' href='style.css'></head><body>";
        echo "<div class='form-container'><div class='msg-success'>";
        echo "<h3>Успешно!</h3>Ваш логин: <b>$login</b><br>Ваш пароль: <b>$password</b>";
        echo "</div><a href='login.php'>Войти и редактировать</a></div></body></html>";
    }
} catch (PDOException $e) { echo "Ошибка: " . $e->getMessage(); }