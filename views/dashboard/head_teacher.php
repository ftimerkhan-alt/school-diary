<!-- Дашборд завуча -->
<div class="space-y-6">
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Классов</p>
                    <p class="text-3xl font-bold text-indigo-600 mt-1"><?= count($classes) ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-school text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Учителей</p>
                    <p class="text-3xl font-bold text-blue-600 mt-1"><?= count($teachers) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Сегодня</p>
                    <p class="text-xl font-bold text-gray-700 mt-1"><?= dayOfWeekName(date('N')) ?></p>
                    <p class="text-sm text-gray-400 mt-1"><?= formatDate(date('Y-m-d')) ?></p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Быстрые действия -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-bolt text-yellow-500 mr-2"></i>Действия</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <a href="<?= url('schedule/edit') ?>" class="flex flex-col items-center gap-2 p-4 bg-blue-50 rounded-xl hover:bg-blue-100 transition group">
                <i class="fas fa-calendar-plus text-2xl text-blue-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 text-center">Редактор расписания</span>
            </a>
            <a href="<?= url('reports/progress') ?>" class="flex flex-col items-center gap-2 p-4 bg-green-50 rounded-xl hover:bg-green-100 transition group">
                <i class="fas fa-chart-line text-2xl text-green-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 text-center">Успеваемость</span>
            </a>
            <a href="<?= url('reports/attendance') ?>" class="flex flex-col items-center gap-2 p-4 bg-orange-50 rounded-xl hover:bg-orange-100 transition group">
                <i class="fas fa-clipboard-list text-2xl text-orange-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 text-center">Посещаемость</span>
            </a>
            <a href="<?= url('reports/teachers') ?>" class="flex flex-col items-center gap-2 p-4 bg-purple-50 rounded-xl hover:bg-purple-100 transition group">
                <i class="fas fa-briefcase text-2xl text-purple-600 group-hover:scale-110 transition-transform"></i>
                <span class="text-sm font-medium text-gray-700 text-center">Нагрузка</span>
            </a>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-school text-indigo-500 mr-2"></i>Классы</h3>
            <div class="space-y-2">
                <?php foreach ($classes as $cls): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                    <div>
                        <span class="font-semibold text-gray-800"><?= e($cls['name']) ?></span>
                        <span class="text-sm text-gray-500 ml-2"><?= $cls['student_count'] ?> уч.</span>
                    </div>
                    <span class="text-xs text-gray-400"><?= e($cls['teacher_name'] ?? '—') ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-chalkboard-teacher text-blue-500 mr-2"></i>Учителя</h3>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <?php foreach ($teachers as $t): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <span class="text-gray-800 text-sm"><?= e($t['full_name']) ?></span>
                    <span class="text-xs text-gray-500"><?= $t['total_hours'] ?> ч/нед</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>