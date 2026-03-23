<?php
/**
 * Боковое меню
 */
$currentUser = currentUser();
$role = currentRole();
$currentRoute = isset($_GET['route']) ? trim($_GET['route'], '/') : 'dashboard';

/**
 * Определяет активный пункт меню
 */
function isActive($route) {
    global $currentRoute;

    $current = trim($currentRoute, '/');
    $target  = trim($route, '/');

    // Если target содержит подмаршрут (users/classes) — сравниваем полностью
    if (strpos($target, '/') !== false) {
        return $current === $target;
    }

    // Иначе сравниваем только первый сегмент
    $parts = explode('/', $current);
    return ($parts[0] ?? '') === $target;
}

function activeClass($route) {
    return isActive($route)
        ? 'bg-white/10 text-white shadow-md'
        : 'text-blue-100 hover:bg-white/10 hover:text-white';
}

// Формируем меню в зависимости от роли
$menuItems = [];

// Дашборд — для всех
$menuItems[] = ['url' => 'dashboard', 'icon' => 'fa-home', 'label' => 'Главная'];

// Меню для каждой роли
switch ($role) {
    case 'admin':
        $menuItems[] = ['url' => 'users', 'icon' => 'fa-users-cog', 'label' => 'Пользователи'];
        $menuItems[] = ['url' => 'users/classes', 'icon' => 'fa-school', 'label' => 'Классы и предметы'];
        $menuItems[] = ['url' => 'grades/journal', 'icon' => 'fa-book-open', 'label' => 'Журнал оценок'];
        $menuItems[] = ['url' => 'attendance/mark', 'icon' => 'fa-clipboard-check', 'label' => 'Посещаемость'];
        $menuItems[] = ['url' => 'schedule/view', 'icon' => 'fa-calendar-alt', 'label' => 'Расписание'];
        $menuItems[] = ['url' => 'schedule/edit', 'icon' => 'fa-calendar-plus', 'label' => 'Редактор расписания'];
        $menuItems[] = ['url' => 'reports', 'icon' => 'fa-chart-bar', 'label' => 'Отчёты'];
        $menuItems[] = ['url' => 'messages/inbox', 'icon' => 'fa-envelope', 'label' => 'Сообщения'];
        $menuItems[] = ['url' => 'messages/broadcast', 'icon' => 'fa-bullhorn', 'label' => 'Рассылка'];
        break;
    
    case 'director':
        $menuItems[] = ['url' => 'users', 'icon' => 'fa-users', 'label' => 'Пользователи'];
        $menuItems[] = ['url' => 'grades/journal', 'icon' => 'fa-book-open', 'label' => 'Журнал оценок'];
        $menuItems[] = ['url' => 'schedule/view', 'icon' => 'fa-calendar-alt', 'label' => 'Расписание'];
        $menuItems[] = ['url' => 'reports', 'icon' => 'fa-chart-bar', 'label' => 'Отчёты'];
        $menuItems[] = ['url' => 'messages/inbox', 'icon' => 'fa-envelope', 'label' => 'Сообщения'];
        $menuItems[] = ['url' => 'messages/broadcast', 'icon' => 'fa-bullhorn', 'label' => 'Рассылка'];
        break;
    
    case 'head_teacher':
    $menuItems[] = ['url' => 'users', 'icon' => 'fa-users', 'label' => 'Пользователи'];
    $menuItems[] = ['url' => 'grades/journal', 'icon' => 'fa-book-open', 'label' => 'Журнал оценок'];
    $menuItems[] = ['url' => 'attendance/mark', 'icon' => 'fa-clipboard-check', 'label' => 'Посещаемость'];
    $menuItems[] = ['url' => 'schedule/my-schedule', 'icon' => 'fa-calendar-day', 'label' => 'Моё расписание'];
    $menuItems[] = ['url' => 'schedule/view', 'icon' => 'fa-calendar-alt', 'label' => 'Расписание классов'];
    $menuItems[] = ['url' => 'schedule/edit', 'icon' => 'fa-calendar-plus', 'label' => 'Редактор расписания'];
    $menuItems[] = ['url' => 'reports', 'icon' => 'fa-chart-bar', 'label' => 'Отчёты'];
    $menuItems[] = ['url' => 'messages/inbox', 'icon' => 'fa-envelope', 'label' => 'Сообщения'];
    $menuItems[] = ['url' => 'messages/broadcast', 'icon' => 'fa-bullhorn', 'label' => 'Рассылка'];
    break;
    
    case 'class_teacher':
        $menuItems[] = ['url' => 'grades/journal', 'icon' => 'fa-book-open', 'label' => 'Журнал оценок'];
        $menuItems[] = ['url' => 'attendance/mark', 'icon' => 'fa-clipboard-check', 'label' => 'Посещаемость'];
        $menuItems[] = ['url' => 'schedule/my-schedule', 'icon' => 'fa-calendar-alt', 'label' => 'Моё расписание'];
        $menuItems[] = ['url' => 'schedule/view', 'icon' => 'fa-calendar-week', 'label' => 'Расписание класса'];
        $menuItems[] = ['url' => 'reports', 'icon' => 'fa-chart-bar', 'label' => 'Отчёты'];
        $menuItems[] = ['url' => 'messages/inbox', 'icon' => 'fa-envelope', 'label' => 'Сообщения'];
        $menuItems[] = ['url' => 'messages/broadcast', 'icon' => 'fa-bullhorn', 'label' => 'Рассылка'];
        break;
    
    case 'teacher':
        $menuItems[] = ['url' => 'grades/journal', 'icon' => 'fa-book-open', 'label' => 'Журнал оценок'];
        $menuItems[] = ['url' => 'attendance/mark', 'icon' => 'fa-clipboard-check', 'label' => 'Посещаемость'];
        $menuItems[] = ['url' => 'schedule/my-schedule', 'icon' => 'fa-calendar-alt', 'label' => 'Моё расписание'];
        $menuItems[] = ['url' => 'messages/inbox', 'icon' => 'fa-envelope', 'label' => 'Сообщения'];
        break;
    
    case 'student':
        $menuItems[] = ['url' => 'grades/my-grades', 'icon' => 'fa-star', 'label' => 'Мои оценки'];
        $menuItems[] = ['url' => 'attendance/my-attendance', 'icon' => 'fa-clipboard-check', 'label' => 'Посещаемость'];
        $menuItems[] = ['url' => 'schedule/my-schedule', 'icon' => 'fa-calendar-alt', 'label' => 'Расписание'];
        $menuItems[] = ['url' => 'messages/inbox', 'icon' => 'fa-envelope', 'label' => 'Сообщения'];
        break;

    case 'parent':
        $menuItems[] = ['url' => 'grades/my-grades', 'icon' => 'fa-star', 'label' => 'Оценки ребёнка'];
        $menuItems[] = ['url' => 'attendance/my-attendance', 'icon' => 'fa-clipboard-check', 'label' => 'Посещаемость'];
        $menuItems[] = ['url' => 'schedule/my-schedule', 'icon' => 'fa-calendar-alt', 'label' => 'Расписание'];
        $menuItems[] = ['url' => 'messages/inbox', 'icon' => 'fa-envelope', 'label' => 'Сообщения'];
        break;
}
?>

<!-- Sidebar -->
<aside id="sidebar" class="fixed inset-y-0 left-0 w-64 bg-gradient-to-b from-slate-900 via-blue-900 to-blue-800 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out z-50 flex flex-col shadow-xl">
    
    <!-- Логотип -->
    <div class="flex items-center gap-3 px-6 py-5 border-b border-indigo-700/50">
        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
            <i class="fas fa-graduation-cap text-white text-xl"></i>
        </div>
        <div>
            <h2 class="text-white font-bold text-lg leading-tight">Дневник</h2>
            <p class="text-indigo-300 text-xs">Школьная система</p>
        </div>
        <!-- Кнопка закрытия (мобильная) -->
        <button onclick="toggleSidebar()" class="lg:hidden ml-auto text-indigo-300 hover:text-white transition">
            <i class="fas fa-times text-lg"></i>
        </button>
    </div>
    
    <!-- Навигация -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto">
        <ul class="space-y-1">
            <?php foreach ($menuItems as $item): ?>
            <li>
                <a href="<?= url($item['url']) ?>" 
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 <?= activeClass($item['url']) ?>">
                    <i class="fas <?= $item['icon'] ?> w-5 text-center text-base"></i>
                    <span><?= e($item['label']) ?></span>
                    <?php if ($item['icon'] === 'fa-envelope' && isset($unreadMessages) && $unreadMessages > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs px-2 py-0.5 rounded-full font-bold">
                        <?= $unreadMessages ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    
    <!-- Нижняя часть сайдбара -->
    <div class="px-4 py-4 border-t border-indigo-700/50">
        <div class="flex items-center gap-3 px-2">
            <div class="w-9 h-9 bg-indigo-600 rounded-lg flex items-center justify-center text-white text-sm font-bold">
                <?= mb_strtoupper(mb_substr($currentUser['full_name'] ?? 'U', 0, 1)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-white text-sm font-medium truncate"><?= e($currentUser['full_name'] ?? '') ?></p>
                <p class="text-indigo-300 text-xs truncate"><?= e($currentUser['role_name'] ?? '') ?></p>
            </div>
        </div>
        <a href="<?= url('logout') ?>" class="mt-3 flex items-center gap-2 px-4 py-2 text-sm text-indigo-300 hover:text-white hover:bg-indigo-700/50 rounded-lg transition">
            <i class="fas fa-sign-out-alt"></i>
            <span>Выйти</span>
        </a>
    </div>
</aside>