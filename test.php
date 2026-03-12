<?php
/**
 * Диагностический скрипт
 * Откройте: http://school-diary/test.php
 * Удалите после проверки!
 */

echo "<h2>🔍 Диагностика Электронного дневника</h2>";

// 1. PHP версия
echo "<h3>1. PHP</h3>";
echo "Версия PHP: <b>" . phpversion() . "</b><br>";
echo "Сессии: <b>" . (function_exists('session_start') ? '✅ OK' : '❌ НЕТ') . "</b><br>";
echo "PDO MySQL: <b>" . (extension_loaded('pdo_mysql') ? '✅ OK' : '❌ НЕТ') . "</b><br>";

// 2. mod_rewrite
echo "<h3>2. Apache mod_rewrite</h3>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "mod_rewrite: <b>" . (in_array('mod_rewrite', $modules) ? '✅ Включён' : '❌ Выключен') . "</b><br>";
} else {
    echo "Не удаётся проверить (не Apache или CGI-режим)<br>";
    echo "Попробуйте открыть: <a href='login'>http://school-diary/login</a><br>";
}

// 3. .htaccess
echo "<h3>3. .htaccess</h3>";
$htaccess = __DIR__ . '/.htaccess';
echo "Файл .htaccess: <b>" . (file_exists($htaccess) ? '✅ Существует' : '❌ Не найден') . "</b><br>";

// 4. Файлы проекта
echo "<h3>4. Ключевые файлы</h3>";
$files = [
    'config/database.php',
    'includes/auth.php',
    'includes/helpers.php',
    'includes/middleware.php',
    'controllers/AuthController.php',
    'controllers/DashboardController.php',
    'views/auth/login.php',
    'views/layout/header.php',
    'views/layout/sidebar.php',
    'views/layout/footer.php',
    'models/User.php',
    'database/schema.sql',
    'assets/css/style.css',
    'assets/js/app.js',
];

$allOk = true;
foreach ($files as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $icon = $exists ? '✅' : '❌';
    if (!$exists) $allOk = false;
    echo "{$icon} {$file}<br>";
}

// 5. База данных
echo "<h3>5. База данных</h3>";
try {
    require_once __DIR__ . '/config/database.php';
    $db = getDB();
    echo "Подключение: <b>✅ OK</b><br>";
    
    $stmt = $db->query("SELECT COUNT(*) as cnt FROM users");
    $result = $stmt->fetch();
    echo "Пользователей в БД: <b>{$result['cnt']}</b><br>";
    
    // Проверяем пароли
    $stmt = $db->prepare("SELECT login, password_hash FROM users WHERE login = 'admin'");
    $stmt->execute();
    $admin = $stmt->fetch();
    if ($admin) {
        $passwordOk = password_verify('123', $admin['password_hash']);
        echo "Пароль admin (123): <b>" . ($passwordOk ? '✅ Верный' : '❌ Неверный — запустите setup_passwords.php') . "</b><br>";
    } else {
        echo "Пользователь admin: <b>❌ Не найден — импортируйте schema.sql</b><br>";
    }
    
    $stmt = $db->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Таблиц в БД: <b>" . count($tables) . "</b> (должно быть ~17)<br>";
    
} catch (Exception $e) {
    echo "Подключение: <b>❌ ОШИБКА: " . $e->getMessage() . "</b><br>";
}

// 6. URL
echo "<h3>6. Пути</h3>";
echo "SCRIPT_NAME: <b>{$_SERVER['SCRIPT_NAME']}</b><br>";
echo "REQUEST_URI: <b>{$_SERVER['REQUEST_URI']}</b><br>";
echo "DOCUMENT_ROOT: <b>{$_SERVER['DOCUMENT_ROOT']}</b><br>";
echo "Корень проекта: <b>" . __DIR__ . "</b><br>";
if (defined('BASE_URL')) {
    echo "BASE_URL: <b>" . BASE_URL . "</b><br>";
}

// 7. Тестовые ссылки
echo "<h3>7. Тестовые ссылки (нажмите для проверки)</h3>";
$base = dirname($_SERVER['SCRIPT_NAME']);
$base = ($base === '/' || $base === '\\') ? '' : $base;
echo "<a href='{$base}/login'>→ Страница входа</a><br>";
echo "<a href='{$base}/index.php?route=login'>→ Страница входа (прямой вызов)</a><br>";
echo "<a href='{$base}/dashboard'>→ Дашборд</a><br>";

echo "<hr>";
echo "<p style='color:orange;'>⚠️ <b>Удалите test.php после проверки!</b></p>";