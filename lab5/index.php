<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

$values = [];
$user_langs = [];

if (!empty($_SESSION['login'])) {
    try {
        $db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345');
        $stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
        $stmt->execute([$_SESSION['app_id']]);
        $values = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $db->prepare("SELECT language_name FROM languages WHERE application_id = ?");
        $stmt->execute([$_SESSION['app_id']]);
        $user_langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) { echo "Ошибка: " . $e->getMessage(); }
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Задание 5: Анкета</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <div class="nav-panel">
            <?php if (!empty($_SESSION['login'])): ?>
                <span>Логин: <b><?php echo $_SESSION['login']; ?></b></span>
                <a href="logout.php">Выйти</a>
            <?php else: ?>
                <a href="login.php">Войти в кабинет</a>
            <?php endif; ?>
        </div>

        <h2>Анкета</h2>
        <form action="save.php" method="POST">
            <div class="field">
                <label>ФИО:</label>
                <input type="text" name="fio" value="<?php echo htmlspecialchars($values['fio'] ?? ''); ?>" required>
            </div>
            <div class="field">
                <label>Телефон:</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>" required>
            </div>
            <div class="field">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>" required>
            </div>
            <div class="field">
                <label>Дата рождения:</label>
                <input type="date" name="birthday" value="<?php echo htmlspecialchars($values['birthday'] ?? ''); ?>" required>
            </div>

            <div class="field">
                <label>Пол:</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male" <?php if(($values['gender'] ?? '') == 'male') echo 'checked'; ?> required> Мужской</label>
                    <label><input type="radio" name="gender" value="female" <?php if(($values['gender'] ?? '') == 'female') echo 'checked'; ?>> Женский</label>
                </div>
            </div>

            <div class="field">
                <label>Любимые языки программирования:</label>
                <div class="check-group">
                    <?php 
                    $langs = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala'];
                    foreach ($langs as $lang) {
                        $checked = in_array($lang, $user_langs) ? 'checked' : '';
                        echo "<label><input type='checkbox' name='languages[]' value='$lang' $checked> $lang</label>";
                    }
                    ?>
                </div>
            </div>

            <div class="field">
                <label>Биография:</label>
                <textarea name="biography"><?php echo htmlspecialchars($values['biography'] ?? ''); ?></textarea>
            </div>

            <button type="submit">
                <?php echo !empty($_SESSION['login']) ? 'Обновить данные' : 'Отправить'; ?>
            </button>
        </form>
    </div>
</body>
</html>