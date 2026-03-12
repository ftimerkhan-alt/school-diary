<!-- Моя посещаемость / Посещаемость ребёнка -->
<div class="space-y-6">
    
    <?php if ($role === 'parent' && count($children) > 1): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-wrap gap-2">
            <?php foreach ($children as $ch): ?>
            <a href="<?= url('attendance/my-attendance?student_id=' . $ch['id']) ?>"
               class="px-4 py-2 rounded-lg font-medium text-sm transition <?= $studentId == $ch['id'] ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?>">
                <?= e($ch['full_name']) ?> (<?= e($ch['class_name']) ?>)
            </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($student && $stats): ?>
    
    <!-- Общая статистика -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <p class="text-2xl font-bold text-green-600"><?= $stats['present'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Присутствовал</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-times-circle text-red-600"></i>
            </div>
            <p class="text-2xl font-bold text-red-600"><?= $stats['absent'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Пропуски</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <p class="text-2xl font-bold text-yellow-600"><?= $stats['late'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Опоздания</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                <i class="fas fa-file-medical text-blue-600"></i>
            </div>
            <p class="text-2xl font-bold text-blue-600"><?= $stats['excused'] ?? 0 ?></p>
            <p class="text-xs text-gray-500">Ув. причина</p>
        </div>
    </div>
    
    <!-- Общий процент -->
    <?php 
    $totalRecords = $stats['total'] ?? 0;
    $presentCount = ($stats['present'] ?? 0);
    $percent = $totalRecords > 0 ? round(($presentCount / $totalRecords) * 100) : 0;
    ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-chart-pie text-indigo-500 mr-2"></i>Общая посещаемость
        </h3>
        <div class="flex items-center gap-6">
            <div class="relative w-28 h-28">
                <canvas id="attendancePie"></canvas>
                <div class="absolute inset-0 flex items-center justify-center">
                    <span class="text-2xl font-bold <?= $percent >= 80 ? 'text-green-600' : ($percent >= 60 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $percent ?>%</span>
                </div>
            </div>
            <div class="flex-1">
                <div class="w-full bg-gray-200 rounded-full h-4 mb-2">
                    <div class="h-4 rounded-full transition-all <?= $percent >= 80 ? 'bg-green-500' : ($percent >= 60 ? 'bg-yellow-500' : 'bg-red-500') ?>" 
                         style="width: <?= $percent ?>%"></div>
                </div>
                <p class="text-sm text-gray-500">
                    Всего записей: <?= $totalRecords ?>, Присутствовал: <?= $presentCount ?>
                </p>
            </div>
        </div>
    </div>
    
    <!-- Статистика по предметам -->
    <?php if (!empty($statsBySubject)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-book text-purple-500 mr-2"></i>По предметам
        </h3>
        <div class="space-y-3">
            <?php foreach ($statsBySubject as $ss): ?>
            <div class="flex items-center gap-4">
                <span class="w-40 text-sm font-medium text-gray-700 truncate"><?= e($ss['name']) ?></span>
                <div class="flex-1">
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all <?= $ss['percent'] >= 80 ? 'bg-green-500' : ($ss['percent'] >= 60 ? 'bg-yellow-500' : 'bg-red-500') ?>"
                             style="width: <?= $ss['percent'] ?>%"></div>
                    </div>
                </div>
                <span class="text-sm font-bold w-12 text-right <?= $ss['percent'] >= 80 ? 'text-green-600' : ($ss['percent'] >= 60 ? 'text-yellow-600' : 'text-red-600') ?>">
                    <?= $ss['percent'] ?>%
                </span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Последние записи -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800">
                <i class="fas fa-history text-gray-400 mr-2"></i>Последние записи
            </h3>
        </div>
        <div class="divide-y divide-gray-100 max-h-96 overflow-y-auto">
            <?php foreach (array_slice($attendanceRecords, 0, 50) as $r): ?>
            <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                <div class="flex items-center gap-3">
                    <?= attendanceIcon($r['status']) ?>
                    <div>
                        <p class="font-medium text-gray-800 text-sm"><?= e($r['subject_name']) ?></p>
                        <p class="text-xs text-gray-400"><?= formatDate($r['date']) ?></p>
                    </div>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 rounded text-xs font-medium <?= attendanceColorClass($r['status']) ?>">
                        <?= attendanceStatusText($r['status']) ?>
                    </span>
                    <?php if ($r['comment']): ?>
                    <p class="text-xs text-gray-400 mt-1"><?= e($r['comment']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($attendanceRecords)): ?>
            <div class="p-8 text-center text-gray-400">Записей нет</div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php elseif ($student): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-clipboard-check text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500">Данных о посещаемости пока нет</p>
    </div>
    <?php else: ?>
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-8 text-center">
        <i class="fas fa-exclamation-triangle text-4xl text-yellow-400 mb-4"></i>
        <p class="text-yellow-700">Профиль не найден</p>
    </div>
    <?php endif; ?>
</div>

<?php if ($student && $stats && $stats['total'] > 0): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('attendancePie');
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Присутствовал', 'Пропуски', 'Опоздания', 'Ув. причина'],
            datasets: [{
                data: [<?= $stats['present'] ?>, <?= $stats['absent'] ?>, <?= $stats['late'] ?>, <?= $stats['excused'] ?>],
                backgroundColor: ['#10b981', '#ef4444', '#f59e0b', '#3b82f6'],
                borderWidth: 0,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { display: false } },
            cutout: '70%',
        }
    });
});
</script>
<?php endif; ?>