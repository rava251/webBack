<?php
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != '123456') {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    exit('<h1>401 Unauthorized</h1>');
}

try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    if (isset($_GET['del'])) {
        $id = (int)$_GET['del'];
        $db->prepare("DELETE FROM languages WHERE application_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM users WHERE application_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM applications WHERE id = ?")->execute([$id]);
        header('Location: admin.php');
        exit();
    }

    $stats = $db->query("SELECT language_name, COUNT(*) as count FROM languages GROUP BY language_name")->fetchAll(PDO::FETCH_ASSOC);
    $users = $db->query("SELECT * FROM applications ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) { exit('Ошибка БД: ' . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Панель управления</h1>
        <h3>Статистика по языкам:</h3>
        <?php foreach($stats as $s): ?>
            <span class="stat-badge"><?= htmlspecialchars($s['language_name']) ?>: <?= $s['count'] ?></span>
        <?php endforeach; ?>

        <table>
            <thead>
                <tr><th>ID</th><th>ФИО</th><th>Email</th><th>Действия</th></tr>
            </thead>
            <tbody>
                <?php foreach($users as $u): ?>
                <tr>
                    <td><?= $u['id'] ?></td>
                    <td><?= htmlspecialchars($u['fio']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td>
                        <a href="index.php?edit_id=<?= $u['id'] ?>" class="btn-edit">Редактировать</a>
                        <a href="admin.php?del=<?= $u['id'] ?>" class="btn-del" onclick="return confirm('Удалить?')">Удалить</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
