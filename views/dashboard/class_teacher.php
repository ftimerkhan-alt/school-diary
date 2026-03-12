<!-- Дашборд классного руководителя -->
<div class="space-y-6">
    
    <?php if ($class): ?>
    <!-- Информация о классе -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                <span class="text-2xl font-bold"><?= e($class['name']) ?></span>
            </div>
            <div>
                <h2 class="text-2xl font-bold">Класс <?= e($class['name']) ?></h2>
                <p class="text-white/80"><?= count($students) ?> <?= plural(count($students), 'ученик', 'ученика', 'учеников') ?></p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Учеников</p>
                    <p class="text-3xl font-bold text-indigo-600"><?= count($students) ?></p>
                </div>
                <i class="fas fa-user-graduate text-3xl text-indigo-200"></i>
            </div>
        </div>
        
        <?php if ($classStats && $classStats['total'] > 0): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Посещаемость</p>
                    <p class="text-3xl font-bold text-green-600">
                        <?= round(($classStats['present'] / $classStats['total']) * 100) ?>%
                    </p>
                </div>
                <i class="fas fa-chart-pie text-3xl text-green-200"></i>
            </div>
        </div>
        <?php else: ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Посещаемость</p>
                    <p class="text-xl font-bold text-gray-400 mt-1">Нет данных</p>
                </div>
                <i class="fas fa-chart-pie text-3xl text-gray-200"></i>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Сегодня</p>
                    <p class="text-lg font-bold text-gray-700"><?= formatDate(date('Y-m-d')) ?></p>
                    <p class="text-xs text-gray-400"><?= dayOfWeekName(date('N')) ?></p>
                </div>
                <i class="fas fa-calendar-day text-3xl text-blue-200"></i>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Расписание на сегодня -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar-day text-blue-500 mr-2"></i>Расписание на сегодня
            </h3>
            <?php if (!empty($todaySchedule)): ?>
            <div class="space-y-2">
                <?php 
                $currentLesson = ScheduleModel::getCurrentLesson();
                foreach ($todaySchedule as $lesson): 
                    $isCurrent = ($lesson['lesson_order'] == $currentLesson);
                ?>
                <div class="flex items-center gap-3 p-3 rounded-lg <?= $isCurrent ? 'bg-indigo-50 border-l-4 border-indigo-500' : 'bg-gray-50' ?>">
                    <span class="w-8 h-8 rounded-full <?= $isCurrent ? 'bg-indigo-500 text-white' : 'bg-gray-200 text-gray-600' ?> flex items-center justify-center font-bold text-sm">
                        <?= $lesson['lesson_order'] ?>
                    </span>
                    <div class="flex-1">
                        <p class="font-medium text-gray-800"><?= e($lesson['subject_name']) ?></p>
                        <p class="text-xs text-gray-500"><?= e($lesson['teacher_name']) ?> · каб. <?= e($lesson['room'] ?? '—') ?></p>
                    </div>
                    <span class="text-xs text-gray-400"><?= date('H:i', strtotime($lesson['time_start'])) ?>-<?= date('H:i', strtotime($lesson['time_end'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400 text-center py-8">Сегодня нет уроков</p>
            <?php endif; ?>
        </div>
        
        <!-- Список учеников -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-user-graduate text-indigo-500 mr-2"></i>Ученики класса
            </h3>
            <div class="space-y-1 max-h-72 overflow-y-auto">
                <?php foreach ($students as $i => $st): ?>
                <div class="flex items-center gap-3 p-2 rounded-lg hover:bg-gray-50 transition">
                    <span class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-xs font-medium text-gray-500"><?= $i + 1 ?></span>
                    <span class="text-sm text-gray-800"><?= e($st['full_name']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
        <p class="text-yellow-700 text-lg">Вам ещё не назначен класс</p>
        <p class="text-yellow-600 text-sm mt-2">Обратитесь к администратору</p>
    </div>
    <?php endif; ?>
</div>