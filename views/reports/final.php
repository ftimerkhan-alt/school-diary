<!-- Итоговая ведомость -->
<div class="space-y-6">
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="<?= url('reports/final') ?>" class="flex flex-col sm:flex-row gap-3 items-end">
            <input type="hidden" name="route" value="reports/final">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Класс</label>
                <select name="class_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>>
                        Класс <?= e($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="button" onclick="printTable()" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition text-sm font-medium">
                <i class="fas fa-print mr-1"></i> Печать
            </button>
        </form>
    </div>
    
    <?php if ($classInfo && !empty($students) && !empty($subjects)): ?>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="printArea">
        <div class="p-4 border-b border-gray-100 text-center">
            <h2 class="text-lg font-bold text-gray-800">
                Итоговая ведомость — Класс <?= e($classInfo['name']) ?>
            </h2>
            <p class="text-sm text-gray-500">Учебный год <?= currentAcademicYear() ?>/<?= currentAcademicYear() + 1 ?></p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="finalTable">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left font-semibold text-gray-500 text-xs sticky left-0 bg-gray-50 z-10 min-w-[50px]">№</th>
                        <th class="px-3 py-3 text-left font-semibold text-gray-500 text-xs sticky left-[50px] bg-gray-50 z-10 min-w-[200px]">Ученик</th>
                        <?php foreach ($subjects as $subj): ?>
                        <th class="px-2 py-3 text-center font-semibold text-gray-500 text-xs whitespace-nowrap min-w-[80px]" title="<?= e($subj['name']) ?>">
                            <?= e(mb_substr($subj['name'], 0, 8)) ?><?= mb_strlen($subj['name']) > 8 ? '.' : '' ?>
                        </th>
                        <?php endforeach; ?>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 text-xs bg-indigo-50">Ø Общий</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($gradesTable as $i => $row): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-2 text-gray-500 sticky left-0 bg-white z-10"><?= $i + 1 ?></td>
                        <td class="px-3 py-2 font-medium text-gray-800 sticky left-[50px] bg-white z-10 whitespace-nowrap">
                            <?= e($row['student']['full_name']) ?>
                        </td>
                        <?php foreach ($subjects as $subj): 
                            $avg = $row['grades'][$subj['id']];
                        ?>
                        <td class="px-2 py-2 text-center">
                            <?php if ($avg !== null): ?>
                            <span class="inline-block w-10 py-0.5 rounded text-xs font-bold <?= gradeColorClass(round($avg)) ?>">
                                <?= $avg ?>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-300">—</span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                        <td class="px-3 py-2 text-center bg-indigo-50/50">
                            <?php if ($row['overall'] !== null): ?>
                            <span class="font-bold text-sm <?= $row['overall'] >= 4 ? 'text-green-600' : ($row['overall'] >= 3 ? 'text-yellow-600' : 'text-red-600') ?>">
                                <?= $row['overall'] ?>
                            </span>
                            <?php else: ?>
                            <span class="text-gray-300">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-file-alt text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500">Нет данных для формирования ведомости</p>
    </div>
    <?php endif; ?>
</div>

<script>
function printTable() {
    const content = document.getElementById('printArea');
    const win = window.open('', '_blank');
    win.document.write('<html><head><title>Итоговая ведомость</title>');
    win.document.write('<style>body{font-family:Arial,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #333;padding:4px 8px;text-align:center}th{background:#eee}td:nth-child(2){text-align:left}.text-center{text-align:center}h2{text-align:center;margin:10px 0 5px}p{text-align:center;margin:0 0 15px}</style>');
    win.document.write('</head><body>');
    win.document.write(content.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
}
</script>