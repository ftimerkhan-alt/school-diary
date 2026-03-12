<!-- Дашборд администратора -->
<div class="space-y-6">
    
    <!-- Карточки статистики -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Всего пользователей</p>
                    <p class="text-3xl font-bold text-gray-800 mt-1"><?= $totalUsers ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-users text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Учеников</p>
                    <p class="text-3xl font-bold text-emerald-600 mt-1"><?= $roleCounts['student'] ?? 0 ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-graduate text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Учителей</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1"><?= ($roleCounts['teacher'] ?? 0) + ($roleCounts['class_teacher'] ?? 0) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Классов</p>
                    <p class="text-3xl font-bold text-purple-600 mt-1"><?= count($classes) ?></p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-school text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Быстрые действия -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i>Быстрые действия
        </h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="<?= url('users/create') ?>" class="flex flex-col items-center gap-2 p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition group">
                <i class="fas fa-user-plus text-2xl text-indigo-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Добавить пользователя</span>
            </a>
            <a href="<?= url('grades/journal') ?>" class="flex flex-col items-center gap-2 p-4 bg-emerald-50 rounded-xl hover:bg-emerald-100 transition group">
                <i class="fas fa-book-open text-2xl text-emerald-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Журнал оценок</span>
            </a>
            <a href="<?= url('schedule/edit') ?>" class="flex flex-col items-center gap-2 p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition group">
                <i class="fas fa-calendar-plus text-2xl text-blue-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Расписание</span>
            </a>
            <a href="<?= url('reports') ?>" class="flex flex-col items-center gap-2 p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition group">
                <i class="fas fa-chart-bar text-2xl text-purple-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700">Отчёты</span>
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Классы -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-school text-indigo-500 mr-2"></i>Классы
            </h3>
            <div class="space-y-2">
                <?php foreach ($classes as $cls): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div>
                        <span class="font-semibold text-gray-800"><?= e($cls['name']) ?></span>
                        <span class="text-sm text-gray-500 ml-2">
                            <?= $cls['student_count'] ?> <?= plural($cls['student_count'], 'ученик', 'ученика', 'учеников') ?>
                        </span>
                    </div>
                    <span class="text-xs text-gray-400"><?= e($cls['teacher_name'] ?? 'Нет кл. рук.') ?></span>
                </div>
                <?php endforeach; ?>
                <?php if (empty($classes)): ?>
                <p class="text-gray-400 text-center py-4">Классы не найдены</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Пользователи по ролям -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-users text-emerald-500 mr-2"></i>Пользователи по ролям
            </h3>
            <div class="space-y-3">
                <?php 
                $roleIcons = [
                    'admin' => 'fa-user-shield text-red-500',
                    'director' => 'fa-crown text-yellow-500',
                    'head_teacher' => 'fa-user-tie text-blue-500',
                    'class_teacher' => 'fa-chalkboard-teacher text-indigo-500',
                    'teacher' => 'fa-chalkboard text-green-500',
                    'student' => 'fa-user-graduate text-purple-500',
                    'parent' => 'fa-user-friends text-orange-500',
                ];
                foreach ($stats as $s): 
                    $icon = $roleIcons[$s['name']] ?? 'fa-user text-gray-500';
                ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <i class="fas <?= $icon ?> w-5 text-center"></i>
                        <span class="text-gray-700"><?= e($s['display_name']) ?></span>
                    </div>
                    <span class="bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-sm font-semibold"><?= $s['count'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>