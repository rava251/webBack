<?php
header('Content-Type: text/html; charset=UTF-8');

$errors = array();
$values = array();
// Список всех полей для автоматической обработки
$fields = ['fio', 'phone', 'email', 'birthday', 'gender', 'biography', 'contract', 'languages'];

foreach ($fields as $field) {
    // Получаем ошибки из Cookies
    if (!empty($_COOKIE[$field . '_error'])) {
        $errors[$field] = $_COOKIE[$field . '_error'];
    }
    // Получаем ранее введенные значения
    if ($field == 'languages') {
        $values[$field] = isset($_COOKIE[$field . '_value']) ? explode(',', $_COOKIE[$field . '_value']) : [];
    } else {
        $values[$field] = isset($_COOKIE[$field . '_value']) ? $_COOKIE[$field . '_value'] : '';
    }
}

// Удаляем куки с ошибками после использования
foreach ($fields as $field) {
    setcookie($field . '_error', '', 100000);
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Задание 4: Валидация</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="form-container">
        <h2>Анкета (Lab 4)</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="main-error">Пожалуйста, исправьте ошибки в полях, выделенных красным.</div>
        <?php endif; ?>

        <form action="save.php" method="POST">
            <div class="field">
                <label>ФИО:</label>
                <input type="text" name="fio" value="<?= htmlspecialchars($values['fio']) ?>" 
                       class="<?= isset($errors['fio']) ? 'error-field' : '' ?>" placeholder="Иванов Иван Иванович">
                <?php if(isset($errors['fio'])): ?><div class="error-message"><?= $errors['fio'] ?></div><?php endif; ?>
            </div>

            <div class="field">
                <label>Телефон:</label>
                <input type="tel" name="phone" value="<?= htmlspecialchars($values['phone']) ?>" 
                       class="<?= isset($errors['phone']) ? 'error-field' : '' ?>" placeholder="+7 (999) 000-00-00">
                <?php if(isset($errors['phone'])): ?><div class="error-message"><?= $errors['phone'] ?></div><?php endif; ?>
            </div>

            <div class="field">
                <label>E-mail:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($values['email']) ?>" 
                       class="<?= isset($errors['email']) ? 'error-field' : '' ?>">
                <?php if(isset($errors['email'])): ?><div class="error-message"><?= $errors['email'] ?></div><?php endif; ?>
            </div>

            <div class="field">
                <label>Дата рождения:</label>
                <input type="date" name="birthday" value="<?= htmlspecialchars($values['birthday']) ?>" 
                       class="<?= isset($errors['birthday']) ? 'error-field' : '' ?>">
                <?php if(isset($errors['birthday'])): ?><div class="error-message"><?= $errors['birthday'] ?></div><?php endif; ?>
            </div>

            <div class="field">
                <label>Пол:</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male" <?= ($values['gender'] == 'male' || empty($values['gender'])) ? 'checked' : '' ?>> Мужской</label>
                    <label><input type="radio" name="gender" value="female" <?= ($values['gender'] == 'female') ? 'checked' : '' ?>> Женский</label>
                </div>
            </div>

            <div class="field">
                <label>Любимый язык программирования:</label>
                <select name="languages[]" multiple size="5" class="<?= isset($errors['languages']) ? 'error-field' : '' ?>">
                    <?php 
                    $langs = ["Pascal", "C", "C++", "JavaScript", "PHP", "Python", "Java", "Haskell", "Clojure", "Prolog", "Scala", "Go"];
                    foreach ($langs as $l) {
                        $selected = in_array($l, $values['languages']) ? 'selected' : '';
                        echo "<option value=\"$l\" $selected>$l</option>";
                    }
                    ?>
                </select>
                <?php if(isset($errors['languages'])): ?><div class="error-message"><?= $errors['languages'] ?></div><?php endif; ?>
            </div>

            <div class="field">
                <label>Биография:</label>
                <textarea name="biography" rows="4"><?= htmlspecialchars($values['biography']) ?></textarea>
            </div>

            <div class="field">
                <label class="<?= isset($errors['contract']) ? 'error-message' : '' ?>">
                    <input type="checkbox" name="contract" value="1" <?= !empty($values['contract']) ? 'checked' : '' ?>> С контрактом ознакомлен(а)
                </label>
            </div>

            <button type="submit">Сохранить</button>
        </form>
    </div>
</body>
</html>