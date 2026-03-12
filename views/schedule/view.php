<!-- Просмотр расписания -->
<div class="space-y-4">
    
<?php if (!isClassTeacher()): ?>
<!-- Выбор класса -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="<?= url('schedule/view') ?>" class="flex flex-col sm:flex-row gap-3">
        <input type="hidden" name="route" value="schedule/view">
        <div class="flex-1">
            <select name="class_id" onchange="this.form.submit()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>>
                    Класс <?= e($c['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>
<?php endif; ?>

    <?php if ($selectedClassId && !empty($schedule)): ?>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
        <h2 class="text-xl font-bold text-gray-800">
            <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i>
            Расписание класса <?= e($className) ?>
        </h2>
    </div>
    
    <!-- Расписание по дням -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php for ($day = 1; $day <= 6; $day++): 
            $dayLessons = $schedule[$day] ?? [];
            $isToday = ($day == $todayDow);
        ?>
        <div class="bg-white rounded-xl shadow-sm border <?= $isToday ? 'border-indigo-300 ring-2 ring-indigo-100' : 'border-gray-100' ?> overflow-hidden">
            <div class="px-4 py-3 <?= $isToday ? 'bg-indigo-50' : 'bg-gray-50' ?> border-b border-gray-100">
                <h3 class="font-bold <?= $isToday ? 'text-indigo-700' : 'text-gray-700' ?>">
                    <?php if ($isToday): ?><i class="fas fa-star text-yellow-500 mr-1"></i><?php endif; ?>
                    <?= dayOfWeekName($day) ?>
                    <?php if ($isToday): ?><span class="text-xs font-normal text-indigo-400 ml-2">Сегодня</span><?php endif; ?>
                </h3>
            </div>
            
            <?php if (!empty($dayLessons)): ?>
            <div class="divide-y divide-gray-50">
                <?php foreach ($dayLessons as $lesson):
                    $isCurrent = ($isToday && $lesson['lesson_order'] == $currentLesson);
                ?>
                <div class="flex items-center gap-3 px-4 py-3 <?= $isCurrent ? 'bg-indigo-50' : 'hover:bg-gray-50' ?> transition">
                    <div class="w-8 h-8 rounded-full <?= $isCurrent ? 'bg-indigo-500 text-white animate-pulse' : 'bg-gray-100 text-gray-500' ?> flex items-center justify-center font-bold text-sm flex-shrink-0">
                        <?= $lesson['lesson_order'] ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="font-medium text-gray-800 text-sm truncate"><?= e($lesson['subject_name']) ?></p>
                        <p class="text-xs text-gray-400 truncate">
                            <?= e($lesson['teacher_name']) ?>
                            <?php if ($lesson['room']): ?> · каб. <?= e($lesson['room']) ?><?php endif; ?>
                        </p>
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0">
                        <?= date('H:i', strtotime($lesson['time_start'])) ?><br>
                        <?= date('H:i', strtotime($lesson['time_end'])) ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="p-6 text-center text-gray-400 text-sm">
                <i class="fas fa-coffee mr-1"></i> Нет уроков
            </div>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-calendar-alt text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Расписание не составлено</p>
    </div>
    <?php endif; ?>
</div>