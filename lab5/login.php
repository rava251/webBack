<?php
session_start();
if (!empty($_SESSION['login'])) { header('Location: index.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345');
    $stmt = $db->prepare("SELECT * FROM users WHERE login = ?");
    $stmt->execute([$_POST['login']]);
    $user = $stmt->fetch();

    if ($user && password_verify($_POST['password'], $user['password'])) {
        $_SESSION['login'] = $user['login'];
        $_SESSION['app_id'] = $user['application_id'];
        header('Location: index.php');
    } else { $error = "Неверный логин или пароль!"; }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Вход в систему</h2>
        <?php if(isset($error)) echo "<div class='msg msg-error'>$error</div>"; ?>
        <form method="POST">
            <div class="field"><input name="login" placeholder="Логин" required></div>
            <div class="field"><input name="password" type="password" placeholder="Пароль" required></div>
            <button type="submit">Войти</button>
        </form>
    </div>
</body>
</html>