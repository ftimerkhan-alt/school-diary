<?php
/**
 * Точка входа — Электронный дневник
 * Исправленная версия роутинга для Open Server
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Запуск сессии
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Подключение конфигурации
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/middleware.php';

// =====================================================
// ОПРЕДЕЛЕНИЕ МАРШРУТА
// =====================================================

$route = '';

// Способ 1: через GET-параметр route (от .htaccess)
if (isset($_GET['route']) && $_GET['route'] !== '') {
    $route = $_GET['route'];
}

// Способ 2: через REQUEST_URI (если .htaccess не передаёт route)
if (empty($route)) {
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = parse_url($uri, PHP_URL_PATH);
    
    // Убираем базовый путь проекта
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    if ($scriptDir !== '/' && $scriptDir !== '\\') {
        $uri = substr($uri, strlen($scriptDir));
    }
    
    $route = $uri;
}

// Очищаем маршрут
$route = trim($route, '/');
$route = preg_replace('#index\.php/?#', '', $route);
$route = preg_replace('#[^a-zA-Z0-9/_\-]#', '', $route);

// Разбиваем на части
$parts = array_values(array_filter(explode('/', $route)));
$controller = isset($parts[0]) ? $parts[0] : '';
$action     = isset($parts[1]) ? $parts[1] : 'index';
$param      = isset($parts[2]) ? $parts[2] : null;
$extraParam = isset($parts[3]) ? $parts[3] : null;

// =====================================================
// МАРШРУТИЗАЦИЯ
// =====================================================

switch ($controller) {
    
    // ---- Авторизация ----
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $ctrl = new AuthController();
        $ctrl->login();
        break;
    
    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $ctrl = new AuthController();
        $ctrl->logout();
        break;
    
    // ---- Дашборд ----
    case 'dashboard':
        requireAuth();
        require_once __DIR__ . '/controllers/DashboardController.php';
        $ctrl = new DashboardController();
        $ctrl->index();
        break;
    
    // ---- Пользователи ----
    case 'users':
        requireAuth();
        require_once __DIR__ . '/controllers/UserController.php';
        $ctrl = new UserController();
        switch ($action) {
            case 'create':      $ctrl->create(); break;
            case 'store':       $ctrl->store(); break;
            case 'edit':        $ctrl->edit($param); break;
            case 'update':      $ctrl->update($param); break;
            case 'delete':      $ctrl->delete($param); break;
            case 'teacher-subjects':      $ctrl->teacherSubjects($param); break;
            case 'save-teacher-subjects': $ctrl->saveTeacherSubjects($param); break;
            case 'classes':     $ctrl->classes(); break;
            case 'add-class':   $ctrl->addClass(); break;
            case 'create-terms': $ctrl->createTerms(); break;
            case 'delete-class': $ctrl->deleteClass($param); break;
            case 'add-subject': $ctrl->addSubject(); break;
            case 'delete-subject': $ctrl->deleteSubject($param); break;
            default:            $ctrl->index();
        }
        break;
    
    // ---- Оценки ----
    case 'grades':
        requireAuth();
        require_once __DIR__ . '/controllers/GradeController.php';
        $ctrl = new GradeController();
        switch ($action) {
            case 'journal': $ctrl->journal(); break;
            case 'store':   $ctrl->store(); break;
            case 'update':  $ctrl->update(); break;
            case 'delete':  $ctrl->delete(); break;
            case 'my':
            case 'my-grades': $ctrl->myGrades(); break;
            default:        $ctrl->journal();
        }
        break;
    
    // ---- Посещаемость ----
    case 'attendance':
        requireAuth();
        require_once __DIR__ . '/controllers/AttendanceController.php';
        $ctrl = new AttendanceController();
        switch ($action) {
            case 'mark':    $ctrl->mark(); break;
            case 'store':   $ctrl->store(); break;
            case 'my':
            case 'my-attendance': $ctrl->myAttendance(); break;
            default:        $ctrl->mark();
        }
        break;
    
    // ---- Расписание ----
    case 'schedule':
        requireAuth();
        require_once __DIR__ . '/controllers/ScheduleController.php';
        $ctrl = new ScheduleController();
        switch ($action) {
            case 'view':    $ctrl->view(); break;
            case 'my':
            case 'my-schedule': $ctrl->mySchedule(); break;
            case 'edit':    $ctrl->edit(); break;
            case 'store':   $ctrl->store(); break;
            case 'delete':  $ctrl->deleteLesson(); break;
            case 'copy':    $ctrl->copy(); break;
            default:        $ctrl->view();
        }
        break;
    
    // ---- Отчёты ----
    case 'reports':
        requireAuth();
        require_once __DIR__ . '/controllers/ReportsController.php';
        $ctrl = new ReportsController();
        switch ($action) {
            case 'progress':    $ctrl->progress(); break;
            case 'attendance':
            case 'attendance-report': $ctrl->attendanceReport(); break;
            case 'final':       $ctrl->finalReport(); break;
            case 'teachers':    $ctrl->teachers(); break;
            case 'student-profile': $ctrl->studentProfile($param); break;
            default:            $ctrl->index();
        }
        break;
    
    // ---- Сообщения ----
    case 'messages':
        requireAuth();
        require_once __DIR__ . '/controllers/MessagesController.php';
        $ctrl = new MessagesController();
        switch ($action) {
            case 'inbox':   $ctrl->inbox(); break;
            case 'sent':    $ctrl->sent(); break;
            case 'compose': $ctrl->compose(); break;
            case 'send':    $ctrl->send(); break;
            case 'read':    $ctrl->read($param); break;
            case 'delete':  $ctrl->delete($param); break;
            case 'get-recipients': $ctrl->getRecipients(); break;
            case 'broadcast': $ctrl->broadcast(); break;
            case 'send-broadcast': $ctrl->sendBroadcast(); break;
            default:        $ctrl->inbox();
        }
        break;
    
    // ---- API ----
    case 'api':
    requireAuth();
    header('Content-Type: application/json; charset=utf-8');

    switch ($action) {
        case 'students-by-class':
            require_once __DIR__ . '/models/Student.php';
            $studentModel = new Student();
            $students = $studentModel->getByClassId($param);
            echo json_encode(['success' => true, 'data' => $students]);
            break;

        case 'unread-count':
            require_once __DIR__ . '/models/Message.php';
            $msgModel = new Message();
            $count = $msgModel->getUnreadCount($_SESSION['user_id']);
            echo json_encode(['success' => true, 'count' => $count]);
            break;

        case 'teachers-by-subject':  
            require_once __DIR__ . '/models/Teacher.php';
            $teacherModel = new Teacher();
            $teachers = $teacherModel->getTeachersBySubject((int)$param);
            echo json_encode(['success' => true, 'data' => $teachers]);
            break;

        default:
            http_response_code(404);
            echo json_encode(['success' => false, 'error' => 'API endpoint not found']);
    }
    exit;
    
    // ---- Статические ресурсы (если mod_rewrite не работает) ----
    case 'assets':
        $file = __DIR__ . '/' . implode('/', $parts);
        if (file_exists($file) && !is_dir($file)) {
            $ext = pathinfo($file, PATHINFO_EXTENSION);
            $types = [
                'css' => 'text/css',
                'js'  => 'application/javascript',
                'png' => 'image/png',
                'jpg' => 'image/jpeg',
                'gif' => 'image/gif',
                'svg' => 'image/svg+xml',
            ];
            header('Content-Type: ' . ($types[$ext] ?? 'application/octet-stream'));
            readfile($file);
            exit;
        }
        http_response_code(404);
        exit;
    
    // ---- По умолчанию ----
    default:
        if (isLoggedIn()) {
            redirect('dashboard');
        } else {
            redirect('login');
        }
        break;
}