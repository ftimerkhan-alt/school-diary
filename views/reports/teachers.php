<!-- Нагрузка учителей -->
<div class="space-y-6">
    
    <!-- Общая статистика -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-sm text-gray-500">Всего учителей</p>
            <p class="text-3xl font-bold text-indigo-600"><?= count($teachers) ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-sm text-gray-500">Средняя нагрузка</p>
            <?php 
            $totalHours = array_sum(array_column($teachers, 'total_hours'));
            $avgHours = count($teachers) > 0 ? round($totalHours / count($teachers), 1) : 0;
            ?>
            <p class="text-3xl font-bold text-blue-600"><?= $avgHours ?> <span class="text-sm font-normal">ч/нед</span></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 text-center">
            <p class="text-sm text-gray-500">Общая нагрузка</p>
            <p class="text-3xl font-bold text-green-600"><?= $totalHours ?> <span class="text-sm font-normal">ч/нед</span></p>
        </div>
    </div>
    
    <!-- График -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4"><i class="fas fa-chart-bar text-blue-500 mr-2"></i>Нагрузка по учителям</h3>
        <div class="h-64">
            <canvas id="workloadChart"></canvas>
        </div>
    </div>
    
    <!-- Детализация -->
    <div class="space-y-4">
        <?php foreach ($teacherDetails as $td): 
            $info = $td['info'];
            $wl = $td['workload'];
            $cs = $td['class_subjects'];
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex items-center justify-between cursor-pointer"
                 onclick="this.nextElementSibling.classList.toggle('hidden')">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold">
                        <?= mb_strtoupper(mb_substr($info['full_name'], 0, 1)) ?>
                    </div>
                    <div>
                        <p class="font-bold text-gray-800"><?= e($info['full_name']) ?></p>
                        <p class="text-xs text-gray-500"><?= $info['subject_count'] ?> предм. · <?= $info['class_count'] ?> кл.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="px-3 py-1 rounded-full text-sm font-bold <?= $info['total_hours'] > 25 ? 'bg-red-100 text-red-700' : ($info['total_hours'] > 18 ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') ?>">
                        <?= $info['total_hours'] ?> ч/нед
                    </span>
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
            </div>
            
            <div class="hidden p-4">
                <?php if (!empty($cs)): ?>
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Предметы и классы:</h4>
                <div class="flex flex-wrap gap-2 mb-4">
                    <?php foreach ($cs as $item): ?>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-medium">
                        <?= e($item['subject_name']) ?> — <?= e($item['class_name']) ?>
                    </span>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($wl)): ?>
                <h4 class="text-sm font-semibold text-gray-600 mb-2">Нагрузка:</h4>
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Предмет</th>
                            <th class="px-3 py-2 text-left text-xs font-semibold text-gray-500">Класс</th>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500">Часов/нед</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($wl as $w): ?>
                        <tr>
                            <td class="px-3 py-2 text-gray-800"><?= e($w['subject_name']) ?></td>
                            <td class="px-3 py-2 text-gray-600"><?= e($w['class_name']) ?></td>
                            <td class="px-3 py-2 text-center font-bold text-gray-700"><?= $w['hours_per_week'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                <p class="text-gray-400 text-sm">Нагрузка не задана</p>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = <?= json_encode(array_column($teachers, 'full_name')) ?>;
    const data = <?= json_encode(array_map(function($t) { return (int)$t['total_hours']; }, $teachers)) ?>;
    const colors = data.map(v => v > 25 ? '#ef4444' : (v > 18 ? '#f59e0b' : '#10b981'));
    
    new Chart(document.getElementById('workloadChart'), {
        type: 'bar',
        data: {
            labels: labels.map(l => l.split(' ').slice(0, 2).join(' ')),
            datasets: [{
                label: 'Часов/нед',
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
                y: { beginAtZero: true, ticks: { stepSize: 5 } },
                x: { ticks: { font: { size: 10 }, maxRotation: 45 } }
            }
        }
    });
});
</script>