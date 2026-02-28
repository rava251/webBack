<?php
// Задание 7: Information Disclosure - отключаем вывод ошибок пользователю
ini_set('display_errors', 0);
error_reporting(0);

// Задание 6: HTTP-авторизация
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != '123456') {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="My Website"');
    die('<h1>401 Unauthorized</h1>');
}

try {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Задание 6: Удаление данных
    if (isset($_GET['del'])) {
        $id = (int)$_GET['del'];
        $stmt = $db->prepare("DELETE FROM applications WHERE id = ?"); // Задание 7: Prepared Statement
        $stmt->execute([$id]);
        header('Location: lab7admin.php');
    }

    // Задание 6: Статистика по языкам
    $stats = $db->query("SELECT language_name, COUNT(*) as count FROM languages GROUP BY language_name")->fetchAll(PDO::FETCH_ASSOC);

    // Задание 6: Просмотр всех данных
    $users = $db->query("SELECT * FROM applications ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage()); // Логируем ошибку на сервере
    die("Ошибка базы данных.");
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Админ-панель</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #eee; }
        table { width: 100%; border-collapse: collapse; background: white; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .stat-box { display: inline-block; padding: 10px; background: #007bff; color: white; margin-right: 10px; border-radius: 4px; }
    </style>
</head>
<body>
    <h1>Панель администратора</h1>
    
    <h2>Статистика языков (Задание 6):</h2>
    <?php foreach($stats as $s): ?>
        <div class="stat-box">
            <?php echo htmlspecialchars($s['language_name']); ?>: <?php echo (int)$s['count']; ?>
        </div>
    <?php endforeach; ?>

    <h2>Список пользователей:</h2>
    <table>
        <tr><th>ID</th><th>ФИО</th><th>Email</th><th>Телефон</th><th>Действия</th></tr>
        <?php foreach($users as $u): ?>
        <tr>
            <td><?php echo (int)$u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['fio']); ?></td> <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['phone']); ?></td>
            <td>
                <a href="lab7admin.php?del=<?php echo $u['id']; ?>" onclick="return confirm('Удалить?')">Удалить</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
