<!-- Дашборд учителя -->
<div class="space-y-6">
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Мои предметы</p>
                    <p class="text-3xl font-bold text-indigo-600"><?= count($subjects) ?></p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-book text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Мои классы</p>
                    <p class="text-3xl font-bold text-emerald-600"><?= count($classes) ?></p>
                </div>
                <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-chalkboard text-emerald-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Сегодня</p>
                    <p class="text-lg font-bold text-gray-700"><?= dayOfWeekName(date('N')) ?></p>
                    <p class="text-sm text-gray-400"><?= formatDate(date('Y-m-d')) ?></p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i class="fas fa-calendar-day text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Расписание на сегодня -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-calendar-day text-blue-500 mr-2"></i>Мои уроки сегодня
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
                        <p class="text-xs text-gray-500">Класс <?= e($lesson['class_name']) ?> · каб. <?= e($lesson['room'] ?? '—') ?></p>
                    </div>
                    <span class="text-xs text-gray-400"><?= date('H:i', strtotime($lesson['time_start'])) ?>-<?= date('H:i', strtotime($lesson['time_end'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400 text-center py-8"><i class="fas fa-coffee text-4xl mb-2 block"></i>Сегодня нет уроков</p>
            <?php endif; ?>
        </div>
        
        <!-- Быстрые действия -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-bolt text-yellow-500 mr-2"></i>Быстрые действия</h3>
                <div class="grid grid-cols-2 gap-3">
                    <a href="<?= url('grades/journal') ?>" class="flex flex-col items-center gap-2 p-4 bg-indigo-50 rounded-xl hover:bg-indigo-100 transition group">
                        <i class="fas fa-book-open text-2xl text-indigo-600 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-medium text-gray-700">Журнал</span>
                    </a>
                    <a href="<?= url('attendance/mark') ?>" class="flex flex-col items-center gap-2 p-4 bg-green-50 rounded-xl hover:bg-green-100 transition group">
                        <i class="fas fa-clipboard-check text-2xl text-green-600 group-hover:scale-110 transition-transform"></i>
                        <span class="text-sm font-medium text-gray-700">Посещаемость</span>
                    </a>
                </div>
            </div>
            
            <!-- Мои предметы и классы -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-book text-purple-500 mr-2"></i>Мои предметы</h3>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($subjects as $subj): ?>
                    <span class="px-3 py-1.5 bg-purple-50 text-purple-700 rounded-lg text-sm font-medium"><?= e($subj['name']) ?></span>
                    <?php endforeach; ?>
                </div>
                
                <h4 class="text-md font-bold text-gray-800 mt-4 mb-2"><i class="fas fa-chalkboard text-emerald-500 mr-2"></i>Мои классы</h4>
                <div class="flex flex-wrap gap-2">
                    <?php foreach ($classes as $cls): ?>
                    <span class="px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-lg text-sm font-medium"><?= e($cls['name']) ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>