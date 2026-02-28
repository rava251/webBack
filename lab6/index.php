<?php
session_start();
$db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345');

$values = [];
$user_langs = [];

if (!empty($_GET['edit_id'])) {
    $target_id = (int)$_GET['edit_id'];
    $_SESSION['editing_id'] = $target_id; // Важно для save.php
    
    $stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$target_id]);
    $values = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT language_name FROM languages WHERE application_id = ?");
    $stmt->execute([$target_id]);
    $user_langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
} else {
    unset($_SESSION['editing_id']);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2><?= !empty($values) ? "Редактирование профиля #" . $_SESSION['editing_id'] : "Новая анкета" ?></h2>
        <form action="save.php" method="POST">
            <div class="field">
                <label>ФИО:</label>
                <input name="fio" value="<?= htmlspecialchars($values['fio'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($values['email'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Телефон:</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($values['phone'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Дата рождения:</label>
                <input type="date" name="birthday" value="<?= htmlspecialchars($values['birthday'] ?? '') ?>" required>
            </div>
            <div class="field">
                <label>Любимые языки:</label>
                <select name="languages[]" multiple required size="5">
                    <?php
                    $langs = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Go"];
                    foreach($langs as $l) {
                        $sel = in_array($l, $user_langs) ? 'selected' : '';
                        echo "<option value='$l' $sel>$l</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="field">
                <label>Биография:</label>
                <textarea name="biography" rows="4"><?= htmlspecialchars($values['biography'] ?? '') ?></textarea>
            </div>
            <button type="submit" style="background:#28a745; color:white; padding:10px; border:none; cursor:pointer;">Сохранить изменения</button>
            <br><br>
            <a href="admin.php">Вернуться в админ-панель</a>
        </form>
    </div>
</body>
</html>
