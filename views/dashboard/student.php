<!-- Дашборд ученика -->
<div class="space-y-6">
    
    <?php if ($student): ?>
    <!-- Инфо-карточка -->
    <div class="bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
                <?= mb_strtoupper(mb_substr($student['full_name'], 0, 1)) ?>
            </div>
            <div>
                <h2 class="text-xl font-bold"><?= e($student['full_name']) ?></h2>
                <p class="text-white/80">Класс <?= e($student['class_name']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <?php
        // Общий средний балл
        $allGrades = [];
        foreach ($gradesBySubject as $subj) {
            foreach ($subj['grades'] as $g) $allGrades[] = $g['grade'];
        }
        $overallAvg = count($allGrades) > 0 ? round(array_sum($allGrades) / count($allGrades), 2) : 0;
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Средний балл</p>
                    <p class="text-3xl font-bold <?= $overallAvg >= 4 ? 'text-green-600' : ($overallAvg >= 3 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $overallAvg ?: '—' ?></p>
                </div>
                <i class="fas fa-star text-3xl text-yellow-200"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Посещаемость</p>
                    <?php if ($attendanceStats && $attendanceStats['total'] > 0): ?>
                    <p class="text-3xl font-bold text-green-600"><?= round(($attendanceStats['present'] / $attendanceStats['total']) * 100) ?>%</p>
                    <?php else: ?>
                    <p class="text-xl text-gray-400">Нет данных</p>
                    <?php endif; ?>
                </div>
                <i class="fas fa-clipboard-check text-3xl text-green-200"></i>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Всего оценок</p>
                    <p class="text-3xl font-bold text-indigo-600"><?= count($allGrades) ?></p>
                </div>
                <i class="fas fa-book-open text-3xl text-indigo-200"></i>
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
                        <p class="text-xs text-gray-500"><?= e($lesson['teacher_name']) ?></p>
                    </div>
                    <span class="text-xs text-gray-400"><?= date('H:i', strtotime($lesson['time_start'])) ?>-<?= date('H:i', strtotime($lesson['time_end'])) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400 text-center py-6">Сегодня нет уроков 🎉</p>
            <?php endif; ?>
        </div>
        
        <!-- Последние оценки -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-star text-yellow-500 mr-2"></i>Последние оценки
            </h3>
            <?php if (!empty($recentGrades)): ?>
            <div class="space-y-2">
                <?php foreach ($recentGrades as $g): ?>
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800"><?= e($g['subject_name']) ?></p>
                        <p class="text-xs text-gray-400"><?= formatDate($g['date']) ?><?= $g['comment'] ? ' · ' . e($g['comment']) : '' ?></p>
                    </div>
                    <span class="w-9 h-9 rounded-lg <?= gradeColorClass($g['grade']) ?> flex items-center justify-center font-bold text-lg">
                        <?= $g['grade'] ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <a href="<?= url('grades/my-grades') ?>" class="mt-4 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                Все оценки <i class="fas fa-arrow-right ml-1"></i>
            </a>
            <?php else: ?>
            <p class="text-gray-400 text-center py-6">Оценок пока нет</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Оценки по предметам -->
    <?php if (!empty($gradesBySubject)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Средний балл по предметам
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <?php foreach ($gradesBySubject as $subjId => $subj):
                $grades = array_column($subj['grades'], 'grade');
                $avg = count($grades) > 0 ? round(array_sum($grades) / count($grades), 2) : 0;
                $colorClass = $avg >= 4.5 ? 'from-green-400 to-green-600' : ($avg >= 3.5 ? 'from-blue-400 to-blue-600' : ($avg >= 2.5 ? 'from-yellow-400 to-yellow-600' : 'from-red-400 to-red-600'));
            ?>
            <div class="bg-gray-50 rounded-lg p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-medium text-gray-800 text-sm"><?= e($subj['name']) ?></span>
                    <span class="text-lg font-bold bg-gradient-to-r <?= $colorClass ?> bg-clip-text text-transparent"><?= $avg ?></span>
                </div>
                <div class="flex gap-1">
                    <?php foreach (array_slice($subj['grades'], -8) as $g): ?>
                    <span class="w-7 h-7 rounded text-xs <?= gradeColorClass($g['grade']) ?> flex items-center justify-center font-bold"><?= $g['grade'] ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
        <p class="text-yellow-700">Профиль ученика не найден. Обратитесь к администратору.</p>
    </div>
    <?php endif; ?>
</div>