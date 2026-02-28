<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="lab7style.css">
    <title>Аудит безопасности - Лабораторная 7</title>
</head>
<body>
    <h1>Отчет по аудиту безопасности приложения</h1>
    <section>
        <h2>1. SQL Injection</h2>
        <p>Применены подготовленные выражения PDO (Prepared Statements). Данные передаются отдельно от SQL-кода.</p>
    </section>
    <section>
        <h2>2. XSS (Cross-Site Scripting)</h2>
        <p>Весь вывод данных из БД экранируется функцией <code>htmlspecialchars()</code>.</p>
    </section>
    <section>
        <h2>3. CSRF</h2>
        <p>Добавлена проверка <code>HTTP_REFERER</code> для подтверждения легитимности запросов на удаление.</p>
    </section>
    <section>
        <h2>4. Information Disclosure</h2>
        <p>Вывод ошибок на экран отключен через <code>ini_set('display_errors', 0)</code>. Ошибки БД обрабатываются в блоках try-catch.</p>
    </section>
    <section>
        <h2>5. Include & Upload</h2>
        <p>Функционал загрузки файлов отсутствует, что исключает риск Remote Code Execution. Динамический Include не используется.</p>
    </section>
</body>
</html>
