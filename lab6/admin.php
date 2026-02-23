<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW']) ||
    $_SERVER['PHP_AUTH_USER'] != 'admin' || $_SERVER['PHP_AUTH_PW'] != '123456') {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="Admin Panel"');
    exit('<h1>401 Ошибка</h1>');
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

} catch (PDOException $e) { exit('Ошибка: ' . $e->getMessage()); }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель управления</title>
    <style>
        body { font-family: sans-serif; background: #f0f0f0; padding: 20px; }
        .box { background: #fff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .stat-card { display: inline-block; background: #007bff; color: #fff; padding: 8px 15px; border-radius: 4px; margin: 0 10px 20px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        .btn-edit { color: #28a745; text-decoration: none; font-weight: bold; margin-right: 15px; }
        .btn-del { color: #dc3545; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="box">
        <h1>Панель управления (Задание 6)</h1>
        <h3>Статистика:</h3>
        <?php foreach($stats as $s): ?>
            <div class="stat-card"><b><?php echo $s['count']; ?></b> - <?php echo htmlspecialchars($s['language_name']); ?></div>
        <?php endforeach; ?>
        <table>
            <tr><th>ID</th><th>ФИО</th><th>Email</th><th>Действие</th></tr>
            <?php foreach($users as $u): ?>
            <tr>
                <td><?php echo $u['id']; ?></td>
                <td><?php echo htmlspecialchars($u['fio']); ?></td>
                <td><?php echo htmlspecialchars($u['email']); ?></td>
                <td>
                    <a href="index.php?edit_id=<?php echo $u['id']; ?>" class="btn-edit">Редактировать</a>
                    <a href="admin.php?del=<?php echo $u['id']; ?>" class="btn-del" onclick="return confirm('Удалить?')">Удалить</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>