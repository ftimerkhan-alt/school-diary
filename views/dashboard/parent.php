<!-- Дашборд родителя -->
<div class="space-y-6">
    
    <?php if (!empty($childrenData)): ?>
    
    <?php foreach ($childrenData as $childData): 
        $child = $childData['info'];
        $grades = $childData['recent_grades'];
        $att = $childData['attendance'];
    ?>
    
    <!-- Карточка ребёнка -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-5 text-white">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-xl font-bold">
                    <?= mb_strtoupper(mb_substr($child['full_name'], 0, 1)) ?>
                </div>
                <div>
                    <h3 class="text-xl font-bold"><?= e($child['full_name']) ?></h3>
                    <p class="text-white/80">Класс <?= e($child['class_name']) ?> · <?= e($child['relationship']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                <!-- Посещаемость -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-500 mb-2">
                        <i class="fas fa-clipboard-check text-green-500 mr-1"></i>Посещаемость
                    </h4>
                    <?php if ($att && $att['total'] > 0): 
                        $pct = round(($att['present'] / $att['total']) * 100);
                    ?>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold <?= $pct >= 80 ? 'text-green-600' : 'text-red-600' ?>"><?= $pct ?>%</span>
                        <span class="text-sm text-gray-400">присутствие</span>
                    </div>
                    <div class="flex gap-3 mt-2 text-xs text-gray-500">
                        <span><i class="fas fa-times-circle text-red-400"></i> <?= $att['absent'] ?> пропусков</span>
                        <span><i class="fas fa-clock text-yellow-400"></i> <?= $att['late'] ?> опозданий</span>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-400">Нет данных</p>
                    <?php endif; ?>
                </div>
                
                <!-- Средний балл -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-500 mb-2">
                        <i class="fas fa-star text-yellow-500 mr-1"></i>Оценки
                    </h4>
                    <?php if (!empty($grades)):
                        $avg = round(array_sum(array_column($grades, 'grade')) / count($grades), 2);
                    ?>
                    <div class="flex items-baseline gap-2">
                        <span class="text-3xl font-bold <?= $avg >= 4 ? 'text-green-600' : ($avg >= 3 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $avg ?></span>
                        <span class="text-sm text-gray-400">средний балл</span>
                    </div>
                    <?php else: ?>
                    <p class="text-gray-400">Нет данных</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Последние оценки -->
            <?php if (!empty($grades)): ?>
            <h4 class="text-sm font-semibold text-gray-500 mb-3">Последние оценки</h4>
            <div class="space-y-2">
                <?php foreach ($grades as $g): ?>
                <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                    <div>
                        <span class="font-medium text-gray-800 text-sm"><?= e($g['subject_name']) ?></span>
                        <span class="text-xs text-gray-400 ml-2"><?= formatDate($g['date']) ?></span>
                    </div>
                    <span class="w-8 h-8 rounded-lg <?= gradeColorClass($g['grade']) ?> flex items-center justify-center font-bold"><?= $g['grade'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <div class="flex gap-3 mt-4">
                <a href="<?= url('grades/my-grades?student_id=' . $child['id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-star mr-1"></i>Все оценки
                </a>
                <a href="<?= url('attendance/my-attendance?student_id=' . $child['id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-clipboard-check mr-1"></i>Посещаемость
                </a>
                <a href="<?= url('schedule/my-schedule?class_id=' . $child['class_id']) ?>" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                    <i class="fas fa-calendar mr-1"></i>Расписание
                </a>
            </div>
        </div>
    </div>
    
    <?php endforeach; ?>
    
    <?php else: ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <i class="fas fa-child text-4xl text-yellow-400 mb-4"></i>
        <p class="text-yellow-700 text-lg">Дети не привязаны к вашему аккаунту</p>
        <p class="text-yellow-600 text-sm mt-2">Обратитесь к администратору для привязки</p>
    </div>
    <?php endif; ?>
</div>