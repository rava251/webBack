<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Аудит безопасности - Задание 7</title>
    <link rel="stylesheet" href="lab7style.css">
</head>
<body>
    <h1>Отчет по аудиту безопасности (Задание 7)</h1>

    <section>
        <h2>1. SQL Injection</h2>
        <p><b>Решение:</b> Использование подготовленных запросов PDO во всех операциях с БД.</p>
        <pre><code>$stmt = $db->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);</code></pre>
    </section>

    <section>
        <h2>2. XSS (Cross-Site Scripting)</h2>
        <p><b>Решение:</b> Функция <code>htmlspecialchars()</code> для всех выводимых из БД данных.</p>
        <pre><code>&lt;?php echo htmlspecialchars($u['fio']); ?&gt;</code></pre>
    </section>

    <section>
        <h2>3. CSRF (Cross-Site Request Forgery)</h2>
        <p><b>Решение:</b> Проверка <code>HTTP_REFERER</code>, чтобы запросы на изменение данных шли только с нашего домена.</p>
    </section>

    <section>
        <h2>4. Information Disclosure</h2>
        <p><b>Решение:</b> Отключен вывод ошибок через <code>ini_set('display_errors', 0)</code>.</p>
    </section>

    <section>
        <h2>5. Include & Upload</h2>
        <p><b>Статус:</b> Загрузка файлов отсутствует. Динамический Include не используется.</p>
    </section>
</body>
</html>