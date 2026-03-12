<?php
/**
 * Контроллер авторизации
 */
class AuthController {
    
    /**
     * Страница входа и обработка формы
     */
    public function login() {
        // Если уже авторизован — на дашборд
        if (isLoggedIn()) {
            redirect('dashboard');
        }
        
        $error = '';
        $login = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $login = post('login', '');
            $password = post('password', '');
            
            // Валидация
            if (empty($login) || empty($password)) {
                $error = 'Заполните все поля';
            } else {
                $result = attemptLogin($login, $password);
                
                if ($result['success']) {
                    setFlash('success', 'Добро пожаловать, ' . e($_SESSION['user_full_name']) . '!');
                    redirect('dashboard');
                } else {
                    $error = $result['message'];
                }
            }
        }
        
        require __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Выход из системы
     */
    public function logout() {
        logout();
        setFlash('info', 'Вы вышли из системы');
        redirect('login');
    }
}