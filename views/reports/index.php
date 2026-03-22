<!-- Главная страница отчётов -->
<div class="space-y-6">
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        
        <a href="<?= url('reports/progress') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-indigo-200 transition-all group">
            <div class="w-14 h-14 bg-indigo-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-chart-line text-2xl text-indigo-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Успеваемость</h3>
            <p class="text-sm text-gray-500">Средние баллы по классам и предметам, рейтинг учеников, распределение оценок</p>
        </a>
        
        <a href="<?= url('reports/attendance-report') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-green-200 transition-all group">
            <div class="w-14 h-14 bg-green-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-clipboard-list text-2xl text-green-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Посещаемость</h3>
            <p class="text-sm text-gray-500">Процент посещаемости по классам, часто пропускающие ученики</p>
        </a>
        
        <a href="<?= url('reports/final') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-purple-200 transition-all group">
            <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-file-alt text-2xl text-purple-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Итоговая ведомость</h3>
            <p class="text-sm text-gray-500">Сводная таблица средних баллов по всем предметам для класса</p>
        </a>
        
        <?php if (in_array(currentRole(), ['admin', 'director', 'head_teacher'])): ?>
        <a href="<?= url('reports/teachers') ?>" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 hover:shadow-lg hover:border-blue-200 transition-all group">
            <div class="w-14 h-14 bg-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                <i class="fas fa-briefcase text-2xl text-blue-600"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-800 mb-1">Нагрузка учителей</h3>
            <p class="text-sm text-gray-500">Часы по предметам, занятость по классам, детализация</p>
        </a>
        <?php endif; ?>
    </div>
    
    <?php if (!isClassTeacher()): ?>
<!-- Быстрый доступ к классам -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="text-lg font-bold text-gray-800 mb-4">
        <i class="fas fa-school text-indigo-500 mr-2"></i>Быстрый доступ по классам
    </h3>
    <?php if (!empty($classes)): ?>
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-5 gap-3">
        <?php foreach ($classes as $cls): ?>
        <div class="bg-gray-50 rounded-lg p-4 text-center hover:bg-indigo-50 transition">
            <p class="text-xl font-bold text-gray-800 mb-2"><?= e($cls['name']) ?></p>
            <p class="text-xs text-gray-500 mb-3"><?= $cls['student_count'] ?> уч.</p>
            <div class="flex flex-col gap-1">
                <a href="<?= url('reports/progress?class_id=' . $cls['id']) ?>" class="text-xs text-indigo-600 hover:text-indigo-800">
                    <i class="fas fa-chart-line mr-1"></i>Успеваемость
                </a>
                <a href="<?= url('reports/attendance-report?class_id=' . $cls['id']) ?>" class="text-xs text-green-600 hover:text-green-800">
                    <i class="fas fa-clipboard-check mr-1"></i>Посещаемость
                </a>
                <a href="<?= url('reports/final?class_id=' . $cls['id']) ?>" class="text-xs text-purple-600 hover:text-purple-800">
                    <i class="fas fa-file-alt mr-1"></i>Ведомость
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <p class="text-gray-400 text-center py-4">Классы не найдены</p>
    <?php endif; ?>
</div>
<?php endif; ?>
</div>