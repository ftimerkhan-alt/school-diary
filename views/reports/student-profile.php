<!-- Профиль ученика -->
<div class="space-y-6">
    
    <!-- Карточка -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
                <?= mb_strtoupper(mb_substr($student['full_name'], 0, 1)) ?>
            </div>
            <div>
                <h2 class="text-2xl font-bold"><?= e($student['full_name']) ?></h2>
                <p class="text-white/80">Класс <?= e($student['class_name']) ?></p>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Информация -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-info-circle text-blue-500 mr-2"></i>Информация</h3>
            <div class="space-y-3">
                <div>
                    <span class="text-xs text-gray-500">Класс</span>
                    <p class="font-medium text-gray-800"><?= e($student['class_name']) ?></p>
                </div>
                <div>
                    <span class="text-xs text-gray-500">Email</span>
                    <p class="font-medium text-gray-800"><?= e($student['email'] ?? '—') ?></p>
                </div>
                
                <?php if (!empty($parents)): ?>
                <div>
                    <span class="text-xs text-gray-500">Родители</span>
                    <?php foreach ($parents as $p): ?>
                    <p class="font-medium text-gray-800 text-sm">
                        <?= e($p['full_name']) ?>
                        <span class="text-gray-400">(<?= e($p['relationship']) ?>)</span>
                    </p>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Средние баллы -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-star text-yellow-500 mr-2"></i>Успеваемость</h3>
            <?php if (!empty($subjectAvgs)): ?>
            <div class="space-y-2">
                <?php foreach ($subjectAvgs as $sa): ?>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-700"><?= e($sa['name']) ?></span>
                    <span class="font-bold <?= $sa['avg'] >= 4 ? 'text-green-600' : ($sa['avg'] >= 3 ? 'text-yellow-600' : 'text-red-600') ?>"><?= $sa['avg'] ?></span>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <p class="text-gray-400">Нет оценок</p>
            <?php endif; ?>
        </div>
        
        <!-- Посещаемость -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-bold text-gray-800 mb-4"><i class="fas fa-clipboard-check text-green-500 mr-2"></i>Посещаемость</h3>
            <?php if ($attendanceStats && $attendanceStats['total'] > 0):
                $pct = round(($attendanceStats['present'] / $attendanceStats['total']) * 100);
            ?>
            <div class="text-center mb-4">
                <p class="text-4xl font-bold <?= $pct >= 80 ? 'text-green-600' : 'text-red-600' ?>"><?= $pct ?>%</p>
                <p class="text-sm text-gray-500">присутствие</p>
            </div>
            <div class="grid grid-cols-2 gap-2 text-center text-xs">
                <div class="bg-green-50 rounded p-2"><span class="font-bold text-green-600"><?= $attendanceStats['present'] ?></span><br>Присут.</div>
                <div class="bg-red-50 rounded p-2"><span class="font-bold text-red-600"><?= $attendanceStats['absent'] ?></span><br>Пропуски</div>
                <div class="bg-yellow-50 rounded p-2"><span class="font-bold text-yellow-600"><?= $attendanceStats['late'] ?></span><br>Опозд.</div>
                <div class="bg-blue-50 rounded p-2"><span class="font-bold text-blue-600"><?= $attendanceStats['excused'] ?></span><br>Ув. пр.</div>
            </div>
            <?php else: ?>
            <p class="text-gray-400">Нет данных</p>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- График -->
    <?php if (!empty($subjectAvgs)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-chart-bar text-indigo-500 mr-2"></i>Успеваемость по предметам</h3>
        <div class="h-64">
            <canvas id="profileChart"></canvas>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Последние оценки -->
    <?php if (!empty($recentGrades)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100">
            <h3 class="font-bold text-gray-800"><i class="fas fa-history text-gray-400 mr-2"></i>Последние оценки</h3>
        </div>
        <div class="divide-y divide-gray-100 max-h-80 overflow-y-auto">
            <?php foreach (array_slice($recentGrades, 0, 20) as $g): ?>
            <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50">
                <div>
                    <p class="font-medium text-gray-800 text-sm"><?= e($g['subject_name']) ?></p>
                    <p class="text-xs text-gray-400"><?= formatDate($g['date']) ?> · <?= e($g['teacher_name']) ?></p>
                    <?php if ($g['comment']): ?><p class="text-xs text-gray-500 italic"><?= e($g['comment']) ?></p><?php endif; ?>
                </div>
                <span class="w-9 h-9 rounded-lg <?= gradeColorClass($g['grade']) ?> flex items-center justify-center font-bold"><?= $g['grade'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <a href="javascript:history.back()" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium">
        <i class="fas fa-arrow-left mr-2"></i>Назад
    </a>
</div>

<?php if (!empty($subjectAvgs)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = <?= json_encode(array_column($subjectAvgs, 'name')) ?>;
    const data = <?= json_encode(array_column($subjectAvgs, 'avg')) ?>;
    const colors = data.map(v => v >= 4 ? '#10b981' : (v >= 3 ? '#f59e0b' : '#ef4444'));
    
    new Chart(document.getElementById('profileChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Средний балл',
                data: data,
                backgroundColor: colors.map(c => c + '40'),
                borderColor: colors,
                borderWidth: 2, borderRadius: 6,
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, max: 5, ticks: { stepSize: 1 } },
                x: { ticks: { font: { size: 10 }, maxRotation: 45 } }
            }
        }
    });
});
</script>
<?php endif; ?>