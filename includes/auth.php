<?php
/**
 * Функции авторизации и управления сессиями
 */

/**
 * Проверяет, авторизован ли пользователь
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Получает данные текущего пользователя из сессии
 */
function currentUser() {
    if (!isLoggedIn()) return null;
    return [
        'id'        => $_SESSION['user_id'],
        'login'     => $_SESSION['user_login'],
        'full_name' => $_SESSION['user_full_name'],
        'role'      => $_SESSION['user_role'],
        'role_name' => $_SESSION['user_role_name'],
        'email'     => $_SESSION['user_email'] ?? '',
    ];
}

/**
 * Получает роль текущего пользователя
 */
function currentRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * Получает ID текущего пользователя
 */
function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Попытка входа в систему
 * @param string $login
 * @param string $password
 * @return array ['success' => bool, 'message' => string, 'user' => array|null]
 */
function attemptLogin($login, $password) {
    $db = getDB();
    
    // Проверяем количество попыток входа (защита от брутфорса)
    $ip = getClientIP();
    if (isLoginLocked($ip, $login)) {
        $remainingTime = getLoginLockoutRemaining($ip, $login);
        return [
            'success' => false,
            'message' => "Слишком много попыток входа. Попробуйте через {$remainingTime} мин."
        ];
    }
    
    // Ищем пользователя
    $stmt = $db->prepare("
        SELECT u.*, r.name as role_name, r.display_name as role_display_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.login = :login
        LIMIT 1
    ");
    $stmt->execute([':login' => $login]);
    $user = $stmt->fetch();
    
    if (!$user) {
        recordLoginAttempt($ip, $login);
        return [
            'success' => false,
            'message' => 'Неверный логин или пароль'
        ];
    }
    
    // Проверяем активен ли пользователь
    if (!$user['is_active']) {
        return [
            'success' => false,
            'message' => 'Учётная запись деактивирована. Обратитесь к администратору.'
        ];
    }
    
    // Проверяем пароль
    if (!password_verify($password, $user['password_hash'])) {
        recordLoginAttempt($ip, $login);
        $attemptsLeft = MAX_LOGIN_ATTEMPTS - getLoginAttempts($ip, $login);
        $msg = 'Неверный логин или пароль';
        if ($attemptsLeft <= 3 && $attemptsLeft > 0) {
            $msg .= ". Осталось попыток: {$attemptsLeft}";
        }
        return [
            'success' => false,
            'message' => $msg
        ];
    }
    
    // Успешная авторизация — обновляем сессию
    session_regenerate_id(true);
    
    $_SESSION['user_id']        = $user['id'];
    $_SESSION['user_login']     = $user['login'];
    $_SESSION['user_full_name'] = $user['full_name'];
    $_SESSION['user_role']      = $user['role_name'];
    $_SESSION['user_role_name'] = $user['role_display_name'];
    $_SESSION['user_email']     = $user['email'];
    $_SESSION['user_role_id']   = $user['role_id'];
    $_SESSION['login_time']     = time();
    $_SESSION['csrf_token']     = generateCSRFToken();
    
    // Обновляем last_login
    $stmt = $db->prepare("UPDATE users SET last_login = NOW() WHERE id = :id");
    $stmt->execute([':id' => $user['id']]);
    
    // Очищаем попытки входа
    clearLoginAttempts($ip, $login);
    
    return [
        'success' => true,
        'message' => 'Вход выполнен успешно',
        'user' => $user
    ];
}

/**
 * Выход из системы
 */
function logout() {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
}

/**
 * Записывает попытку входа
 */
function recordLoginAttempt($ip, $login) {
    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO login_attempts (ip_address, login, attempted_at) 
        VALUES (:ip, :login, NOW())
    ");
    $stmt->execute([':ip' => $ip, ':login' => $login]);
}

/**
 * Получает количество попыток входа за период блокировки
 */
function getLoginAttempts($ip, $login) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT COUNT(*) as cnt FROM login_attempts 
        WHERE (ip_address = :ip OR login = :login)
        AND attempted_at > DATE_SUB(NOW(), INTERVAL :seconds SECOND)
    ");
    $stmt->execute([
        ':ip' => $ip,
        ':login' => $login,
        ':seconds' => LOGIN_LOCKOUT_TIME
    ]);
    $result = $stmt->fetch();
    return (int)$result['cnt'];
}

/**
 * Проверяет, заблокирован ли вход
 */
function isLoginLocked($ip, $login) {
    return getLoginAttempts($ip, $login) >= MAX_LOGIN_ATTEMPTS;
}

/**
 * Получает оставшееся время блокировки в минутах
 */
function getLoginLockoutRemaining($ip, $login) {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT MAX(attempted_at) as last_attempt FROM login_attempts
        WHERE (ip_address = :ip OR login = :login)
        AND attempted_at > DATE_SUB(NOW(), INTERVAL :seconds SECOND)
    ");
    $stmt->execute([
        ':ip' => $ip,
        ':login' => $login,
        ':seconds' => LOGIN_LOCKOUT_TIME
    ]);
    $result = $stmt->fetch();
    if ($result && $result['last_attempt']) {
        $lastAttempt = strtotime($result['last_attempt']);
        $unlockTime = $lastAttempt + LOGIN_LOCKOUT_TIME;
        $remaining = ceil(($unlockTime - time()) / 60);
        return max(1, $remaining);
    }
    return 0;
}

/**
 * Очищает попытки входа после успешной авторизации
 */
function clearLoginAttempts($ip, $login) {
    $db = getDB();
    $stmt = $db->prepare("
        DELETE FROM login_attempts 
        WHERE ip_address = :ip OR login = :login
    ");
    $stmt->execute([':ip' => $ip, ':login' => $login]);
}

/**
 * Получает IP клиента
 */
function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}