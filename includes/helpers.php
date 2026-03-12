<?php
/**
 * Вспомогательные функции приложения
 */

/**
 * Редирект на указанный маршрут
 */
function redirect($route = '') {
    $url = url($route);
    header("Location: {$url}");
    exit;
}

/**
 * Генерирует URL
 */
function url($route = '') {
    $base = rtrim(BASE_URL, '/');
    if (empty($route)) {
        return $base . '/';
    }
    return $base . '/' . ltrim($route, '/');
}

/**
 * Экранирует строку для вывода в HTML
 */
function e($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Генерирует CSRF-токен
 */
function generateCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Возвращает скрытое поле с CSRF-токеном для формы
 */
function csrfField() {
    $token = generateCSRFToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Проверяет CSRF-токен
 */
function validateCSRF() {
    $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
    if (empty($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        setFlash('error', 'Ошибка безопасности. Попробуйте ещё раз.');
        redirect($_SERVER['HTTP_REFERER'] ?? 'dashboard');
    }
}

/**
 * Устанавливает flash-сообщение
 */
function setFlash($type, $message) {
    $_SESSION['flash'][$type] = $message;
}

/**
 * Получает flash-сообщение
 */
function getFlash($type) {
    $message = $_SESSION['flash'][$type] ?? null;
    unset($_SESSION['flash'][$type]);
    return $message;
}

/**
 * Проверяет наличие flash-сообщения
 */
function hasFlash($type) {
    return isset($_SESSION['flash'][$type]);
}

/**
 * Выводит все flash-сообщения в HTML
 */
function renderFlashMessages() {
    $types = [
        'success' => ['bg-green-100 border-green-500 text-green-700', 'fa-check-circle'],
        'error'   => ['bg-red-100 border-red-500 text-red-700', 'fa-exclamation-circle'],
        'warning' => ['bg-yellow-100 border-yellow-500 text-yellow-700', 'fa-exclamation-triangle'],
        'info'    => ['bg-blue-100 border-blue-500 text-blue-700', 'fa-info-circle'],
    ];
    
    foreach ($types as $type => $config) {
        $message = getFlash($type);
        if ($message) {
            echo '<div class="flash-message border-l-4 p-4 mb-4 rounded-r ' . $config[0] . '" role="alert">';
            echo '<div class="flex items-center">';
            echo '<i class="fas ' . $config[1] . ' mr-3"></i>';
            echo '<p>' . e($message) . '</p>';
            echo '<button onclick="this.parentElement.parentElement.remove()" class="ml-auto">';
            echo '<i class="fas fa-times"></i></button>';
            echo '</div></div>';
        }
    }
}

/**
 * Форматирует дату
 */
function formatDate($date, $format = 'd.m.Y') {
    if (empty($date)) return '—';
    return date($format, strtotime($date));
}

/**
 * Форматирует дату и время
 */
function formatDateTime($datetime, $format = 'd.m.Y H:i') {
    if (empty($datetime)) return '—';
    return date($format, strtotime($datetime));
}

/**
 * Возвращает название дня недели
 */
function dayOfWeekName($num) {
    $days = [
        1 => 'Понедельник',
        2 => 'Вторник',
        3 => 'Среда',
        4 => 'Четверг',
        5 => 'Пятница',
        6 => 'Суббота',
        0 => 'Воскресенье',
    ];
    return $days[$num] ?? '';
}

/**
 * Короткое название дня
 */
function dayOfWeekShort($num) {
    $days = [
        1 => 'Пн', 2 => 'Вт', 3 => 'Ср',
        4 => 'Чт', 5 => 'Пт', 6 => 'Сб', 0 => 'Вс',
    ];
    return $days[$num] ?? '';
}

/**
 * Возвращает CSS-класс для оценки
 */
function gradeColorClass($grade) {
    switch ((int)$grade) {
        case 5: return 'bg-green-500 text-white';
        case 4: return 'bg-blue-500 text-white';
        case 3: return 'bg-yellow-500 text-white';
        case 2: return 'bg-red-500 text-white';
        case 1: return 'bg-red-700 text-white';
        default: return 'bg-gray-300 text-gray-700';
    }
}

/**
 * Возвращает CSS-класс для статуса посещаемости
 */
function attendanceColorClass($status) {
    switch ($status) {
        case 'present': return 'bg-green-100 text-green-800 border-green-300';
        case 'absent':  return 'bg-red-100 text-red-800 border-red-300';
        case 'late':    return 'bg-yellow-100 text-yellow-800 border-yellow-300';
        case 'excused': return 'bg-blue-100 text-blue-800 border-blue-300';
        default:        return 'bg-gray-100 text-gray-800';
    }
}

/**
 * Возвращает текст статуса посещаемости
 */
function attendanceStatusText($status) {
    switch ($status) {
        case 'present': return 'Присутствует';
        case 'absent':  return 'Отсутствует';
        case 'late':    return 'Опоздал';
        case 'excused': return 'Ув. причина';
        default:        return '—';
    }
}

/**
 * Возвращает иконку статуса посещаемости
 */
function attendanceIcon($status) {
    switch ($status) {
        case 'present': return '<i class="fas fa-check-circle text-green-500"></i>';
        case 'absent':  return '<i class="fas fa-times-circle text-red-500"></i>';
        case 'late':    return '<i class="fas fa-clock text-yellow-500"></i>';
        case 'excused': return '<i class="fas fa-file-medical text-blue-500"></i>';
        default:        return '<i class="fas fa-minus-circle text-gray-400"></i>';
    }
}

/**
 * Пагинация — расчёт
 */
function paginate($totalItems, $perPage = 20, $currentPage = 1) {
    $totalPages = max(1, ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'total'       => $totalItems,
        'per_page'    => $perPage,
        'current'     => $currentPage,
        'total_pages' => $totalPages,
        'offset'      => $offset,
    ];
}

/**
 * Рендер HTML-пагинации
 */
function renderPagination($pagination, $baseUrl) {
    if ($pagination['total_pages'] <= 1) return '';
    
    $html = '<nav class="flex justify-center mt-6"><ul class="flex space-x-1">';
    
    // Кнопка "Назад"
    if ($pagination['current'] > 1) {
        $prev = $pagination['current'] - 1;
        $html .= '<li><a href="' . e($baseUrl) . '&page=' . $prev . '" class="px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">&laquo;</a></li>';
    }
    
    // Номера страниц
    $start = max(1, $pagination['current'] - 2);
    $end = min($pagination['total_pages'], $pagination['current'] + 2);
    
    if ($start > 1) {
        $html .= '<li><a href="' . e($baseUrl) . '&page=1" class="px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">1</a></li>';
        if ($start > 2) $html .= '<li><span class="px-3 py-2">...</span></li>';
    }
    
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $pagination['current']) 
            ? 'bg-indigo-600 text-white border-indigo-600' 
            : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50';
        $html .= '<li><a href="' . e($baseUrl) . '&page=' . $i . '" class="px-3 py-2 rounded-lg border ' . $active . ' transition">' . $i . '</a></li>';
    }
    
    if ($end < $pagination['total_pages']) {
        if ($end < $pagination['total_pages'] - 1) $html .= '<li><span class="px-3 py-2">...</span></li>';
        $html .= '<li><a href="' . e($baseUrl) . '&page=' . $pagination['total_pages'] . '" class="px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">' . $pagination['total_pages'] . '</a></li>';
    }
    
    // Кнопка "Вперёд"
    if ($pagination['current'] < $pagination['total_pages']) {
        $next = $pagination['current'] + 1;
        $html .= '<li><a href="' . e($baseUrl) . '&page=' . $next . '" class="px-3 py-2 rounded-lg bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition">&raquo;</a></li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

/**
 * Безопасное получение POST
 */
function post($key, $default = null) {
    return isset($_POST[$key]) ? trim($_POST[$key]) : $default;
}

/**
 * Безопасное получение GET
 */
function get($key, $default = null) {
    return isset($_GET[$key]) ? trim($_GET[$key]) : $default;
}

/**
 * Валидация email
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Склонение числительных
 * plural(5, 'ученик', 'ученика', 'учеников')
 */
function plural($n, $form1, $form2, $form5) {
    $n = abs($n) % 100;
    $n1 = $n % 10;
    if ($n > 10 && $n < 20) return $form5;
    if ($n1 > 1 && $n1 < 5) return $form2;
    if ($n1 == 1) return $form1;
    return $form5;
}

/**
 * Генерирует случайный логин
 */
function generateLogin($fullName) {
    $parts = explode(' ', $fullName);
    $login = '';
    if (isset($parts[0])) $login .= transliterate($parts[0]);
    if (isset($parts[1])) $login .= '_' . mb_strtolower(mb_substr($parts[1], 0, 1));
    return strtolower($login) . rand(10, 99);
}

/**
 * Транслитерация
 */
function transliterate($string) {
    $table = [
        'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
        'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
        'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n',
        'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
        'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts', 'ч' => 'ch',
        'ш' => 'sh', 'щ' => 'shch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
        'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
    ];
    $string = mb_strtolower($string);
    return strtr($string, $table);
}

/**
 * Текущий учебный год
 */
function currentAcademicYear() {
    $month = (int)date('m');
    $year = (int)date('Y');
    return ($month >= 9) ? $year : $year - 1;
}

function gradeTypeName($type) {
    $types = [
        'current'  => 'Текущая',
        'homework' => 'Домашняя работа',
        'test'     => 'Контрольная',
        'exam'     => 'Экзамен',
    ];
    return isset($types[$type]) ? $types[$type] : $type;
}