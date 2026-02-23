<?php
session_start();
$db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345');

$edit_id = $_SESSION['editing_id'] ?? null;

if ($edit_id) {
    // 1. Обновляем анкету
    $stmt = $db->prepare("UPDATE applications SET fio = ?, email = ?, phone = ?, birthday = ?, gender = ?, biography = ? WHERE id = ?");
    $stmt->execute([$_POST['fio'], $_POST['email'], $_POST['phone'], $_POST['birthday'], $_POST['gender'], $_POST['biography'], $edit_id]);

    // 2. Обновляем языки
    $db->prepare("DELETE FROM languages WHERE application_id = ?")->execute([$edit_id]);
    foreach ($_POST['languages'] as $lang) {
        $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)")->execute([$edit_id, $lang]);
    }

    unset($_SESSION['editing_id']);
    header('Location: admin.php'); // Возвращаемся в админку
} else {
    // Тут твой обычный код INSERT для новых пользователей
    exit('Логика для новой записи');
}