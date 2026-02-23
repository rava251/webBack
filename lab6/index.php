<?php
session_start();
header('Content-Type: text/html; charset=UTF-8');

$db = new PDO('mysql:host=localhost;dbname=web_lab3', 'admin', '12345');

$target_id = null;
if (!empty($_GET['edit_id'])) {
    $target_id = (int)$_GET['edit_id'];
    $_SESSION['editing_id'] = $target_id; // Чтобы save.php знал, кого обновлять
}

$values = [];
$user_langs = [];

if ($target_id) {
    $stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$target_id]);
    $values = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $db->prepare("SELECT language_name FROM languages WHERE application_id = ?");
    $stmt->execute([$target_id]);
    $user_langs = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма редактирования</title>
    <style>
        body { font-family: sans-serif; padding: 30px; line-height: 1.6; }
        form { max-width: 450px; background: #f9f9f9; padding: 20px; border: 1px solid #ccc; }
        input, select, textarea { width: 100%; margin-bottom: 15px; padding: 8px; box-sizing: border-box; }
        .btn-save { background: #28a745; color: white; border: none; padding: 10px; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>
    <h2><?php echo $target_id ? "Редактирование профиля ID: $target_id" : "Новая анкета"; ?></h2>
    <form action="save.php" method="POST">
        ФИО: <input type="text" name="fio" value="<?php echo htmlspecialchars($values['fio'] ?? ''); ?>" required>
        Email: <input type="email" name="email" value="<?php echo htmlspecialchars($values['email'] ?? ''); ?>" required>
        Телефон: <input type="tel" name="phone" value="<?php echo htmlspecialchars($values['phone'] ?? ''); ?>" required>
        Дата рождения: <input type="date" name="birthday" value="<?php echo htmlspecialchars($values['birthday'] ?? ''); ?>" required>
        
        Пол: 
        <input type="radio" name="gender" value="male" style="width: auto" <?php if(($values['gender']??'') == 'male') echo 'checked'; ?>> Муж
        <input type="radio" name="gender" value="female" style="width: auto" <?php if(($values['gender']??'') == 'female') echo 'checked'; ?>> Жен
        
        <br><br>Языки программирования:
        <select name="languages[]" multiple required>
            <?php 
            $all_langs = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java'];
            foreach($all_langs as $l) {
                $sel = (in_array($l, $user_langs)) ? 'selected' : '';
                echo "<option value='$l' $sel>$l</option>";
            }
            ?>
        </select>

        Биография:
        <textarea name="biography"><?php echo htmlspecialchars($values['biography'] ?? ''); ?></textarea>
        
        <button type="submit" class="btn-save">Сохранить изменения</button>
    </form>
</body>
</html>