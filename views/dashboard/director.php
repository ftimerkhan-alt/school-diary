<!-- Дашборд директора -->
<div class="space-y-6">
    
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
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
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
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
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
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
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Родителей</p>
                    <p class="text-3xl font-bold text-orange-600 mt-1"><?= $roleCounts['parent'] ?? 0 ?></p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-user-friends text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Нагрузка учителей -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-briefcase text-blue-500 mr-2"></i>Нагрузка учителей
            </h3>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                <?php foreach ($teachers as $t): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800"><?= e($t['full_name']) ?></p>
                        <p class="text-xs text-gray-400"><?= $t['subject_count'] ?> предм. / <?= $t['class_count'] ?> кл.</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-semibold <?= $t['total_hours'] > 20 ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700' ?>">
                        <?= $t['total_hours'] ?> ч/нед
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <a href="<?= url('reports/teachers') ?>" class="mt-4 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                Подробный отчёт <i class="fas fa-arrow-right ml-1"></i>
            </a>
        </div>
        
        <!-- Классы -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-school text-purple-500 mr-2"></i>Классы и ученики
            </h3>
            <div class="space-y-2 max-h-80 overflow-y-auto">
                <?php foreach ($classes as $cls): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <span class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center font-bold text-indigo-600"><?= e($cls['name']) ?></span>
                        <div>
                            <p class="font-medium text-gray-800"><?= e($cls['teacher_name'] ?? 'Нет кл. рук.') ?></p>
                            <p class="text-xs text-gray-400"><?= $cls['student_count'] ?> <?= plural($cls['student_count'], 'ученик', 'ученика', 'учеников') ?></p>
                        </div>
                    </div>
                    <a href="<?= url('reports/progress?class_id=' . $cls['id']) ?>" class="text-indigo-500 hover:text-indigo-700">
                        <i class="fas fa-chart-line"></i>
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>