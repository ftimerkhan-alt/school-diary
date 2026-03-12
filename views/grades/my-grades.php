<!-- Мои оценки / Оценки ребёнка -->
<div class="space-y-6">
    
    <?php if ($role === 'parent' && count($children) > 1): ?>
    <!-- Выбор ребёнка -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-2">
            <?php foreach ($children as $ch): ?>
            <a href="<?= url('grades/my-grades?student_id=' . $ch['id']) ?>"
               class="px-4 py-2 rounded-lg font-medium text-sm transition <?= $studentId == $ch['id'] ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <?= e($ch['full_name']) ?> (<?= e($ch['class_name']) ?>)
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($student): ?>
    
    <!-- Общая статистика -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
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
                    <p class="text-sm text-gray-500">Предметов</p>
                    <p class="text-3xl font-bold text-indigo-600"><?= count($gradesBySubject) ?></p>
                </div>
                <i class="fas fa-book text-3xl text-indigo-200"></i>
            </div>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Класс</p>
                    <p class="text-3xl font-bold text-purple-600"><?= e($student['class_name']) ?></p>
                </div>
                <i class="fas fa-school text-3xl text-purple-200"></i>
            </div>
        </div>
    </div>
    
    <!-- Графики -->
    <?php if (!empty($gradesBySubject)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Средний балл по предметам
        </h3>
        <div class="h-64">
            <canvas id="subjectChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Оценки по предметам -->
    <?php foreach ($gradesBySubject as $subjId => $subj): 
        $grades = $subj['grades'];
        $gradeValues = array_column($grades, 'grade');
        $avg = round(array_sum($gradeValues) / count($gradeValues), 2);
    ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">
                <i class="fas fa-book-open text-indigo-400 mr-2"></i><?= e($subj['name']) ?>
            </h3>
            <div class="flex items-center gap-3">
                <span class="text-sm text-gray-500"><?= count($grades) ?> <?= plural(count($grades), 'оценка', 'оценки', 'оценок') ?></span>
                <span class="px-3 py-1 rounded-full text-sm font-bold <?= $avg >= 4 ? 'bg-green-100 text-green-700' : ($avg >= 3 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') ?>">
                    Ø <?= $avg ?>
                </span>
            </div>
        </div>
        <div class="p-4">
            <div class="flex flex-wrap gap-2">
                <?php foreach ($grades as $g): ?>
                <div class="group relative">
                    <div class="w-10 h-10 rounded-lg <?= gradeColorClass($g['grade']) ?> flex items-center justify-center font-bold text-lg cursor-default">
                        <?= $g['grade'] ?>
                    </div>
                    <!-- Tooltip -->
                    <div class="hidden group-hover:block absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-2 bg-gray-800 text-white text-xs rounded-lg whitespace-nowrap z-10 shadow-lg">
                        <div><?= formatDate($g['date']) ?></div>
                        <?php if ($g['comment']): ?><div class="text-gray-300"><?= e($g['comment']) ?></div><?php endif; ?>
                        <?php if ($g['grade_type'] !== 'current'): ?><div class="text-indigo-300"><?= gradeTypeName($g['grade_type']) ?></div><?php endif; ?>
                        <div class="absolute top-full left-1/2 -translate-x-1/2 -mt-1 w-2 h-2 bg-gray-800 rotate-45"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($gradesBySubject)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-star text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Оценок пока нет</p>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
        <p class="text-yellow-700">Профиль ученика не найден</p>
    </div>
    <?php endif; ?>
</div>

<?php if (!empty($gradesBySubject)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('subjectChart');
    if (!ctx) return;
    
    var labels = [];
    var data = [];
    
    <?php foreach ($gradesBySubject as $subjId => $subj): 
        $grades = array_column($subj['grades'], 'grade');
        $avg = count($grades) > 0 ? round(array_sum($grades) / count($grades), 2) : 0;
    ?>
    labels.push(<?= json_encode($subj['name']) ?>);
    data.push(<?= $avg ?>);
    <?php endforeach; ?>
    
    var colors = data.map(function(v) {
        return v >= 4 ? '#10b981' : (v >= 3 ? '#f59e0b' : '#ef4444');
    });
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Средний балл',
                data: data,
                backgroundColor: colors.map(function(c) { return c + '33'; }),
                borderColor: colors,
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } },
                x: { ticks: { font: { size: 11 }, maxRotation: 45 } }
            }
        }
    });
});
</script>
<?php endif; ?>