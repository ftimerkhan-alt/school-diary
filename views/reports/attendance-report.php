<!-- Отчёт по посещаемости -->
<div class="space-y-6">
    
    <?php if (!isClassTeacher()): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="<?= url('reports/attendance-report') ?>" class="flex flex-col lg:flex-row gap-3">
        <input type="hidden" name="route" value="reports/attendance">

        <div class="flex-1">
            <select name="class_id" onchange="this.form.submit()"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                <option value="">Выберите класс</option>
                <?php foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>>
                    Класс <?= e($c['name']) ?>
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
    
    <?php if ($classInfo && $classStats): ?>
    
    <?php
    $total = $classStats['total'] ?: 1;
    $pPresent = round(($classStats['present'] / $total) * 100);
    $pAbsent = round(($classStats['absent'] / $total) * 100);
    $pLate = round(($classStats['late'] / $total) * 100);
    $pExcused = round(($classStats['excused'] / $total) * 100);
    ?>
    
    <!-- Общая статистика -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="w-10 h-10 bg-green-100 rounded-full mx-auto mb-2 flex items-center justify-center">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <p class="text-2xl font-bold text-green-600"><?= $pPresent ?>%</p>
            <p class="text-xs text-gray-500">Присутствие (<?= $classStats['present'] ?>)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="w-10 h-10 bg-red-100 rounded-full mx-auto mb-2 flex items-center justify-center">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
            <p class="text-2xl font-bold text-red-600"><?= $pAbsent ?>%</p>
            <p class="text-xs text-gray-500">Пропуски (<?= $classStats['absent'] ?>)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="w-10 h-10 bg-yellow-100 rounded-full mx-auto mb-2 flex items-center justify-center">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <p class="text-2xl font-bold text-yellow-600"><?= $pLate ?>%</p>
            <p class="text-xs text-gray-500">Опоздания (<?= $classStats['late'] ?>)</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full mx-auto mb-2 flex items-center justify-center">
                <i class="fas fa-file-medical text-blue-600"></i>
            </div>
            <p class="text-2xl font-bold text-blue-600"><?= $pExcused ?>%</p>
            <p class="text-xs text-gray-500">Ув. причина (<?= $classStats['excused'] ?>)</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Диаграмма -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-chart-pie text-indigo-500 mr-2"></i>Диаграмма</h3>
            <div class="h-64">
                <canvas id="attendanceChart"></canvas>
            </div>
        </div>
        
        <!-- Часто пропускающие -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>Часто пропускающие</h3>
            <?php if (!empty($frequentAbsentees)): ?>
            <div class="space-y-2 max-h-64 overflow-y-auto">
                <?php foreach ($frequentAbsentees as $fa): ?>
                <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                    <div>
                        <p class="font-medium text-gray-800 text-sm"><?= e($fa['full_name']) ?></p>
                        <p class="text-xs text-gray-500"><?= e($fa['class_name']) ?></p>
                    </div>
                    <span class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-sm font-bold">
                        <?= $fa['absence_count'] ?> пр.
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400 text-center py-8">Нет часто пропускающих 🎉</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- По ученикам -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-users text-indigo-500 mr-2"></i>По ученикам</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-500">Ученик</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500">Всего</th>
                        <th class="px-4 py-3 text-center font-semibold text-green-600">Присут.</th>
                        <th class="px-4 py-3 text-center font-semibold text-red-600">Пропуски</th>
                        <th class="px-4 py-3 text-center font-semibold text-yellow-600">Опозд.</th>
                        <th class="px-4 py-3 text-center font-semibold text-gray-500">% присут.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($studentStats as $ss):
                        $stTotal = $ss['total'] ?: 1;
                        $stPct = round(($ss['present'] / $stTotal) * 100);
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800"><?= e($ss['full_name']) ?></td>
                        <td class="px-4 py-3 text-center text-gray-600"><?= $ss['total'] ?></td>
                        <td class="px-4 py-3 text-center text-green-600 font-medium"><?= $ss['present'] ?></td>
                        <td class="px-4 py-3 text-center text-red-600 font-medium"><?= $ss['absent'] ?></td>
                        <td class="px-4 py-3 text-center text-yellow-600 font-medium"><?= $ss['late'] ?></td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center gap-2 justify-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2">
                                    <div class="h-2 rounded-full <?= $stPct >= 80 ? 'bg-green-500' : ($stPct >= 60 ? 'bg-yellow-500' : 'bg-red-500') ?>"
                                         style="width: <?= $stPct ?>%"></div>
                                </div>
                                <span class="text-xs font-bold <?= $stPct >= 80 ? 'text-green-600' : ($stPct >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $stPct ?>%</span>
                            </div>
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
        <i class="fas fa-clipboard-check text-3xl text-blue-600"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Класс не выбран</h3>
    <p class="text-gray-500 max-w-xl mx-auto">
        Для просмотра отчёта по посещаемости выберите класс в верхнем списке.
        После выбора будут показаны сводные показатели, диаграмма и информация по каждому ученику.
    </p>
</div>
<?php endif; ?>
</div>

<?php if ($classStats && $classStats['total'] > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    new Chart(document.getElementById('attendanceChart'), {
        type: 'doughnut',
        data: {
            labels: ['Присутствие', 'Пропуски', 'Опоздания', 'Ув. причина'],
            datasets: [{
                data: [<?= $classStats['present'] ?>, <?= $classStats['absent'] ?>, <?= $classStats['late'] ?>, <?= $classStats['excused'] ?>],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6'],
                borderWidth: 2, borderColor: '#fff',
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 15 } } }
        }
    });
});
</script>
<?php endif; ?>