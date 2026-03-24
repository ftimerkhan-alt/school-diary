<!-- Итоговая ведомость -->
<div class="space-y-6">
    
<?php if (!isClassTeacher()): ?>
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
    <form method="GET" action="<?= url('reports/final') ?>" class="flex flex-col lg:flex-row gap-3 items-end">
        <input type="hidden" name="route" value="reports/final">

        <div class="flex-1">
            <label class="block text-xs font-semibold text-gray-500 mb-1">Класс</label>
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
            <label class="block text-xs font-semibold text-gray-500 mb-1">Учебный год</label>
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
            <label class="block text-xs font-semibold text-gray-500 mb-1">Период</label>
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
    
    <?php if ($classInfo && !empty($students) && !empty($subjects)): ?>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden" id="printArea">
        
        <div class="p-4 border-b border-gray-100 text-center">
    <h2 class="text-lg font-bold text-gray-800">
        Итоговая ведомость — Класс <?= e($classInfo['name']) ?>
    </h2>

    <p class="text-sm text-gray-500">
        Учебный год <?= currentAcademicYear() ?>/<?= currentAcademicYear() + 1 ?>
    </p>

    <p class="text-sm text-gray-600 mt-1">
        Период:
        <span class="font-medium text-gray-800">
            <?= !empty($selectedTerm) ? e($selectedTerm['name']) : 'Весь учебный год' ?>
        </span>
    </p>
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
    <div class="flex justify-end mt-4">
    <button type="button"
            onclick="printTable()"
            class="px-5 py-2.5 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium shadow-sm">
        <i class="fas fa-print mr-2"></i> Печать 
    </button>
</div>
    
    <?php else: ?>
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12 text-center">
    <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-blue-50 flex items-center justify-center">
        <i class="fas fa-file-alt text-3xl text-blue-600"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Класс не выбран</h3>
    <p class="text-gray-500 max-w-xl mx-auto">
        Для формирования итоговой ведомости выберите класс в верхнем списке.
        После выбора будет построена сводная таблица оценок по предметам за выбранный период.
    </p>
</div>
<?php endif; ?>
</div>

<script>
function printTable() {
    var content = document.getElementById('printArea');
    if (!content) {
        alert('Нечего печатать');
        return;
    }

    var win = window.open('', '_blank');
    win.document.write('<html><head><title>Итоговая ведомость</title>');
    win.document.write('<style>body{font-family:Arial,sans-serif;font-size:12px}table{width:100%;border-collapse:collapse}th,td{border:1px solid #333;padding:4px 8px;text-align:center}th{background:#eee}td:nth-child(2){text-align:left}h2,p{text-align:center;margin:5px 0}</style>');
    win.document.write('</head><body>');
    win.document.write(content.innerHTML);
    win.document.write('</body></html>');
    win.document.close();
    win.print();
}
</script>