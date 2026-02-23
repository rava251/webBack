<?php
session_start();
// Information Disclosure: Скрываем системные ошибки от пользователя
ini_set('display_errors', 0);
error_reporting(0);

// HTTP Basic Auth (Задание 6)
if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != '123456') {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    exit('<h1>401 Unauthorized</h1>');
}

try {
    // SQL Injection: Используем PDO с подготовленными выражениями
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345');

    // CSRF Protection: Проверяем Referer для удаления
    if (isset($_GET['del'])) {
        if (isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], $_SERVER['HTTP_HOST']) !== false) {
            $id = (int)$_GET['del'];
            $db->prepare("DELETE FROM languages WHERE application_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM users WHERE application_id = ?")->execute([$id]);
            $db->prepare("DELETE FROM applications WHERE id = ?")->execute([$id]);
            header('Location: lab7admin.php');
            exit();
        } else {
            die('CSRF Attack Blocked');
        }
    }

    $stats = $db->query("SELECT language_name, COUNT(*) as count FROM languages GROUP BY language_name")->fetchAll(PDO::FETCH_ASSOC);
    $users = $db->query("SELECT * FROM applications ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    error_log($e->getMessage()); // Пишем в лог вместо вывода на экран
    exit('Внутренняя ошибка сервера');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Safe Admin Panel</title>
    <link rel="stylesheet" href="lab7admin.css">
</head>
<body>
    <div class="admin-box">
        <h1>Панель управления (Защищенная)</h1>
        <div class="stats-row">
            <?php foreach($stats as $s): ?>
                <div class="badge"><?php echo htmlspecialchars($s['count']); ?> - <?php echo htmlspecialchars($s['language_name']); ?></div>
            <?php endforeach; ?>
        </div>
        <table>
            <tr><th>ID</th><th>ФИО</th><th>Email</th><th>Действие</th></tr>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?php echo (int)$u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['fio']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                    <a href="../lab6/index.php?edit_id=<?php echo $u['id']; ?>" class="edit-link">Правка</a>
                    <a href="lab7admin.php?del=<?php echo $u['id']; ?>" class="del-link" onclick="return confirm('Удалить?')">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>