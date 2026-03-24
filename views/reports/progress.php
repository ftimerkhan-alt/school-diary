<!-- Отчёт по успеваемости -->
<div class="space-y-6">
    
    <!-- Выбор класса -->
    <?php if (!isClassTeacher()): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="<?= url('reports/progress') ?>" class="flex flex-col lg:flex-row gap-3">
        <input type="hidden" name="route" value="reports/progress">

        <div class="flex-1">
            <select name="class_id" onchange="this.form.submit()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                <option value="">Выберите класс</option>
                <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>>
                    Класс <?= e($c['name']) ?> (<?= $c['student_count'] ?> уч.)
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1">
            <select name="academic_year" onchange="this.form.submit()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                <?php foreach ($availableYears as $year): ?>
                <option value="<?= $year ?>" <?= $selectedAcademicYear == $year ? 'selected' : '' ?>>
                    <?= $year ?>/<?= $year + 1 ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="flex-1">
            <select name="term_id" onchange="this.form.submit()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                <option value="0" <?= $selectedTermId == 0 ? 'selected' : '' ?>>Весь учебный год</option>
                <?php foreach ($terms as $term): ?>
                <option value="<?= $term['id'] ?>" <?= $selectedTermId == $term['id'] ? 'selected' : '' ?>>
                    <?= e($term['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>
<?php else: ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Учебный год</p>
            <p class="text-lg font-bold text-gray-800"><?= $selectedAcademicYear ?>/<?= $selectedAcademicYear + 1 ?></p>
        </div>
        <div>
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Период</p>
            <p class="text-lg font-bold text-gray-800"><?= $selectedTerm ? e($selectedTerm['name']) : 'Весь учебный год' ?></p>
        </div>
    </div>
</div>
<?php endif; ?>
    
    <?php if ($classInfo && !empty($students)): ?>
    
    <!-- Статистика -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-sm text-gray-500">Учеников</p>
            <p class="text-3xl font-bold text-indigo-600"><?= count($students) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-sm text-gray-500">Отличников</p>
            <p class="text-3xl font-bold text-green-600"><?= $excellentCount ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-sm text-gray-500">Хорошистов</p>
            <p class="text-3xl font-bold text-blue-600"><?= $goodCount ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-sm text-gray-500">Качество знаний</p>
            <?php $quality = count($students) > 0 ? round((($excellentCount + $goodCount) / count($students)) * 100) : 0; ?>
            <p class="text-3xl font-bold <?= $quality >= 50 ? 'text-green-600' : 'text-red-600' ?>"><?= $quality ?>%</p>
        </div>
    </div>
    
    <?php if ($selectedTerm): ?>
<div class="text-sm text-gray-500">
    Период: <span class="font-medium text-gray-700"><?= e($selectedTerm['name']) ?></span>
    (<?= formatDate($selectedTerm['start_date']) ?> — <?= formatDate($selectedTerm['end_date']) ?>)
</div>
<?php else: ?>
<div class="text-sm text-gray-500">
    Период: <span class="font-medium text-gray-700">весь учебный год</span>
</div>
<?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- График средних баллов по предметам -->
        <?php if (!empty($subjectAverages)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Средний балл по предметам
            </h3>
            <div class="h-72">
                <canvas id="subjectAvgChart"></canvas>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Распределение оценок -->
        <?php if (!empty($gradeDistribution)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-purple-500 mr-2"></i>Распределение оценок
            </h3>
            <div class="h-72">
                <canvas id="gradeDistChart"></canvas>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Рейтинг учеников -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-trophy text-yellow-500 mr-2"></i>Рейтинг учеников</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Место</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Ученик</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500">Средний балл</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500 hidden md:table-cell">Статус</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500">Действия</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($students as $i => $st): 
                        $place = $i + 1;
                        $avg = $st['average'];
                        $medal = '';
                        if ($place === 1) $medal = '🥇';
                        elseif ($place === 2) $medal = '🥈';
                        elseif ($place === 3) $medal = '🥉';
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3 font-bold text-gray-600">
                            <?= $medal ?: $place ?>
                        </td>
                        <td class="px-4 py-3 font-medium text-gray-800"><?= e($st['full_name']) ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($avg !== null): ?>
                            <span class="inline-block px-3 py-1 rounded-full text-sm font-bold <?= $avg >= 4.5 ? 'bg-green-100 text-green-700' : ($avg >= 3.5 ? 'bg-blue-100 text-blue-700' : ($avg >= 2.5 ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')) ?>">
                                <?= $avg ?>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center hidden md:table-cell">
                            <?php if ($avg >= 4.5): ?>
                            <span class="text-xs text-green-600 font-medium">Отличник</span>
                            <?php elseif ($avg >= 3.5): ?>
                            <span class="text-xs text-blue-600 font-medium">Хорошист</span>
                            <?php elseif ($avg !== null): ?>
                            <span class="text-xs text-gray-500">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="<?= url('reports/student-profile/' . $st['id']) ?>" 
                               class="text-indigo-600 hover:text-indigo-800 text-sm">
                                <i class="fas fa-user-circle mr-1"></i>Профиль
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php else: ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
    <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-blue-50 flex items-center justify-center">
        <i class="fas fa-school text-3xl text-blue-600"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Класс не выбран</h3>
    <p class="text-gray-500 max-w-xl mx-auto">
        Для просмотра отчёта по успеваемости выберите класс в верхнем списке.
        После выбора система покажет средние баллы, рейтинг учеников и аналитику по предметам.
    </p>
</div>
<?php endif; ?>

<?php if (!empty($subjectAverages) || !empty($gradeDistribution)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (!empty($subjectAverages)): ?>
    const ctxSubj = document.getElementById('subjectAvgChart');
    if (ctxSubj) {
        const labels = <?= json_encode(array_column($subjectAverages, 'name')) ?>;
        const data = <?= json_encode(array_map(function($s) { return (float)$s['avg_grade']; }, $subjectAverages)) ?>;
        const colors = data.map(v => v >= 4 ? '#10b981' : (v >= 3 ? '#f59e0b' : '#ef4444'));
        
        new Chart(ctxSubj, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Средний балл',
                    data: data,
                    backgroundColor: colors.map(c => c + '40'),
                    borderColor: colors,
                    borderWidth: 2,
                    borderRadius: 6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } },
                    x: { ticks: { font: { size: 10 }, maxRotation: 45 } }
                }
            }
        });
    }
    <?php endif; ?>
    
    <?php if (!empty($gradeDistribution)): ?>
    const ctxDist = document.getElementById('gradeDistChart');
    if (ctxDist) {
        const distLabels = <?= json_encode(array_map(function($d) { return 'Оценка ' . $d['grade']; }, $gradeDistribution)) ?>;
        const distData = <?= json_encode(array_column($gradeDistribution, 'cnt')) ?>;
        const distColors = ['#10b981', '#3b82f6', '#f59e0b', '#ef4444', '#7c3aed'];
        
        new Chart(ctxDist, {
            type: 'doughnut',
            data: {
                labels: distLabels,
                datasets: [{
                    data: distData,
                    backgroundColor: distColors.slice(0, distData.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12 }, padding: 15 } }
                }
            }
        });
    }
    <?php endif; ?>
});
</script>
<?php endif; ?>