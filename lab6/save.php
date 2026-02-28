<?php
session_start();
try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    if (!empty($_SESSION['editing_id'])) {
        $id = (int)$_SESSION['editing_id'];
        
        // UPDATE (Задание 6)
        $stmt = $db->prepare("UPDATE applications SET fio=?, email=?, phone=?, birthday=?, biography=? WHERE id=?");
        $stmt->execute([$_POST['fio'], $_POST['email'], $_POST['phone'], $_POST['birthday'], $_POST['biography'], $id]);

        // Обновляем языки
        $db->prepare("DELETE FROM languages WHERE application_id = ?")->execute([$id]);
        $stmt = $db->prepare("INSERT INTO languages (application_id, language_name) VALUES (?, ?)");
        foreach ($_POST['languages'] as $l) { $stmt->execute([$id, $l]); }

        unset($_SESSION['editing_id']);
        header('Location: admin.php?msg=updated');
    } else {
        // Обычный INSERT для новых записей
        $stmt = $db->prepare("INSERT INTO applications (fio, email, phone, birthday, biography) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$_POST['fio'], $_POST['email'], $_POST['phone'], $_POST['birthday'], $_POST['biography']]);
        header('Location: index.php?msg=created');
    }
} catch (PDOException $e) {
    die("Ошибка: " . $e->getMessage());
}
