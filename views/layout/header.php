<?php
/**
 * Шапка (header) приложения
 * Подключает Tailwind CSS, Font Awesome, общие стили
 */
$currentUser = currentUser();
$unreadMessages = 0;
if ($currentUser) {
    require_once __DIR__ . '/../../models/Message.php';
    $msgModel = new Message();
    $unreadMessages = $msgModel->getUnreadCount($currentUser['id']);
}
?>
<!DOCTYPE html>
<html lang="ru" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($pageTitle ?? 'Главная') ?> — <?= e(APP_NAME) ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <link rel="stylesheet" href="<?= url('assets/css/style.css') ?>">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: { 50:'#eef2ff',100:'#e0e7ff',200:'#c7d2fe',300:'#a5b4fc',400:'#818cf8',500:'#6366f1',600:'#4f46e5',700:'#4338ca',800:'#3730a3',900:'#312e81' },
                    }
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gray-50">

<!-- Мобильное меню (overlay) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-40 hidden lg:hidden" onclick="toggleSidebar()"></div>

<div class="flex h-full">
    <!-- Sidebar -->
    <?php require __DIR__ . '/sidebar.php'; ?>
    
    <!-- Основной контент -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
        
        <!-- Верхняя панель -->
        <header class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-30">
            <div class="flex items-center justify-between px-4 lg:px-6 py-3">
                
                <!-- Кнопка мобильного меню -->
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <!-- Заголовок страницы -->
                <h1 class="text-lg lg:text-xl font-bold text-gray-800 truncate">
                    <?= e($pageTitle ?? 'Главная панель') ?>
                </h1>
                
                <!-- Правая часть -->
                <div class="flex items-center gap-3">
                    
                    <!-- Уведомления / Сообщения -->
                    <a href="<?= url('messages/inbox') ?>" class="relative p-2 rounded-lg text-gray-600 hover:bg-gray-100 transition">
                        <i class="fas fa-envelope text-lg"></i>
                        <?php if ($unreadMessages > 0): ?>
                        <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold animate-pulse">
                            <?= $unreadMessages > 9 ? '9+' : $unreadMessages ?>
                        </span>
                        <?php endif; ?>
                    </a>
                    
                    <!-- Профиль -->
                    <div class="relative" id="profileDropdown">
                        <button onclick="toggleProfileMenu()" class="flex items-center gap-2 p-2 rounded-lg hover:bg-gray-100 transition">
                            <div class="w-8 h-8 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1)) ?>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-semibold text-gray-700 leading-tight"><?= e($currentUser['full_name']) ?></p>
                                <p class="text-xs text-gray-400"><?= e($currentUser['role_name']) ?></p>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400 hidden md:block"></i>
                        </button>
                        
                        <!-- Выпадающее меню профиля -->
                        <div id="profileMenu" class="hidden absolute right-0 top-full mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                            <div class="px-4 py-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-800"><?= e($currentUser['full_name']) ?></p>
                                <p class="text-xs text-gray-500"><?= e($currentUser['role_name']) ?></p>
                                <p class="text-xs text-gray-400"><?= e($currentUser['login']) ?></p>
                            </div>
                            <a href="<?= url('dashboard') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-home w-4 text-gray-400"></i> Главная
                            </a>
                            <a href="<?= url('messages/inbox') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                                <i class="fas fa-envelope w-4 text-gray-400"></i> Сообщения
                                <?php if ($unreadMessages > 0): ?>
                                <span class="ml-auto bg-red-100 text-red-600 text-xs px-2 py-0.5 rounded-full"><?= $unreadMessages ?></span>
                                <?php endif; ?>
                            </a>
                            <hr class="my-2 border-gray-100">
                            <a href="<?= url('logout') ?>" class="flex items-center gap-3 px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                <i class="fas fa-sign-out-alt w-4"></i> Выйти
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- Содержимое страницы -->
        <main class="flex-1 p-4 lg:p-6">
            <?php renderFlashMessages(); ?>