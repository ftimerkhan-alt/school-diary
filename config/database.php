<?php
/**
 * Конфигурация подключения к базе данных
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'school_diary');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');
define('DB_PORT', '3306');

function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET,
        ];
        
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                die('Ошибка подключения к БД: ' . $e->getMessage());
            } else {
                die('Ошибка подключения к базе данных.');
            }
        }
    }
    
    return $pdo;
}

// =====================================================
// Настройки приложения
// =====================================================

define('APP_DEBUG', true);
define('APP_NAME', 'Электронный дневник');

// --- АВТООПРЕДЕЛЕНИЕ BASE_URL ---
$scriptName = dirname($_SERVER['SCRIPT_NAME']);
$baseUrl = ($scriptName === '/' || $scriptName === '\\') ? '/' : $scriptName . '/';
define('BASE_URL', $baseUrl);

date_default_timezone_set('Europe/Moscow');
define('SESSION_LIFETIME', 3600);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 900);
define('APP_VERSION', '1.0.0');