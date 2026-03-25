<!-- Отметка посещаемости -->
<div class="space-y-4">
    
    <!-- Фильтры -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="<?= url('attendance/mark') ?>" class="flex flex-col md:flex-row gap-3">
            <input type="hidden" name="route" value="attendance/mark">
            
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Предмет</label>
                <select name="subject_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white text-sm">
                    <option value="">Выберите предмет</option>
                    <?php foreach ($availableSubjects as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $selectedSubjectId == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Класс</label>
                <select name="class_id" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white text-sm">
                    <option value="">Выберите класс</option>
                    <?php foreach ($availableClasses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">Дата</label>
                <input type="date" name="date" value="<?= e($selectedDate) ?>" onchange="this.form.submit()"
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                    <i class="fas fa-sync-alt mr-1"></i> Показать
                </button>
            </div>
        </form>
    </div>
    
    <?php if ($selectedClassId && $selectedSubjectId && !empty($attendanceData)): ?>
    
    <!-- Таблица посещаемости -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <h3 class="font-bold text-gray-800">
                <i class="fas fa-clipboard-check text-green-500 mr-2"></i>
                Посещаемость на <?= formatDate($selectedDate) ?>
            </h3>
            
            <?php if ($canEdit): ?>
            <div class="flex gap-2">
                <button type="button" onclick="setAllStatus('present')" class="px-3 py-1.5 bg-green-100 text-green-700 rounded-lg text-xs font-medium hover:bg-green-200 transition">
                    <i class="fas fa-check-circle mr-1"></i> Все присутствуют
                </button>
            </div>
            <?php endif; ?>
        </div>
        
        <form method="POST" action="<?= url('attendance/store') ?>" id="attendanceForm">
            <?= csrfField() ?>
            <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
            <input type="hidden" name="subject_id" value="<?= $selectedSubjectId ?>">
            <input type="hidden" name="date" value="<?= e($selectedDate) ?>">
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase w-8">№</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ученик</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">Статус</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase hidden md:table-cell">Комментарий</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($attendanceData as $i => $row): 
                            $status = $row['status'] ?? 'present';
                            $studentId = $row['student_id'];
                        ?>
                        <tr class="hover:bg-gray-50 transition attendance-row" data-student-id="<?= $studentId ?>">
                            <td class="px-4 py-3 text-sm text-gray-500"><?= $i + 1 ?></td>
                            <td class="px-4 py-3">
                                <span class="font-medium text-gray-800"><?= e($row['student_name']) ?></span>
                            </td>
                            <td class="px-4 py-3">
                                <?php if ($canEdit): ?>
                                <div class="flex justify-center gap-1">
                                    <label class="status-label cursor-pointer">
                                        <input type="radio" name="status[<?= $studentId ?>]" value="present" 
                                               <?= $status === 'present' ? 'checked' : '' ?> class="hidden status-radio" 
                                               onchange="updateRowStyle(this)">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition
                                                     <?= $status === 'present' ? 'bg-green-100 text-green-800 border-green-300' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-green-50' ?>">
                                            <i class="fas fa-check-circle"></i>
                                            <span class="hidden sm:inline">Присут.</span>
                                        </span>
                                    </label>
                                    <label class="status-label cursor-pointer">
                                        <input type="radio" name="status[<?= $studentId ?>]" value="absent"
                                               <?= $status === 'absent' ? 'checked' : '' ?> class="hidden status-radio"
                                               onchange="updateRowStyle(this)">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition
                                                     <?= $status === 'absent' ? 'bg-red-100 text-red-800 border-red-300' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-red-50' ?>">
                                            <i class="fas fa-times-circle"></i>
                                            <span class="hidden sm:inline">Отсут.</span>
                                        </span>
                                    </label>
                                    <label class="status-label cursor-pointer">
                                        <input type="radio" name="status[<?= $studentId ?>]" value="excused"
                                               <?= $status === 'excused' ? 'checked' : '' ?> class="hidden status-radio"
                                               onchange="updateRowStyle(this)">
                                        <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border transition
                                                     <?= $status === 'excused' ? 'bg-blue-100 text-blue-800 border-blue-300' : 'bg-gray-50 text-gray-500 border-gray-200 hover:bg-blue-50' ?>">
                                            <i class="fas fa-file-medical"></i>
                                            <span class="hidden sm:inline">Ув. пр.</span>
                                        </span>
                                    </label>
                                </div>
                                <?php else: ?>
                                <div class="flex justify-center">
                                    <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium border <?= attendanceColorClass($status) ?>">
                                        <?= attendanceIcon($status) ?>
                                        <?= attendanceStatusText($status) ?>
                                    </span>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 hidden md:table-cell">
                                <?php if ($canEdit): ?>
                                <input type="text" name="comment[<?= $studentId ?>]" value="<?= e($row['comment'] ?? '') ?>"
                                       class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-indigo-500 outline-none"
                                       placeholder="Комментарий...">
                                <?php else: ?>
                                <span class="text-sm text-gray-500"><?= e($row['comment'] ?? '') ?></span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($canEdit): ?>
            <div class="p-4 border-t border-gray-100 flex items-center justify-between">
                <div class="flex gap-4 text-sm text-gray-500">
                    <span><i class="fas fa-check-circle text-green-500"></i> Присутствует</span>
                    <span><i class="fas fa-times-circle text-red-500"></i> Отсутствует</span>
                    <span><i class="fas fa-clock text-yellow-500"></i> Опоздал</span>
                    <span><i class="fas fa-file-medical text-blue-500"></i> Ув. причина</span>
                </div>
                <button type="submit" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                    <i class="fas fa-save mr-1"></i> Сохранить
                </button>
            </div>
            <?php endif; ?>
        </form>
    </div>
    
    <?php elseif ($selectedClassId && $selectedSubjectId): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-clipboard text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500">Нет данных за выбранную дату</p>
    </div>
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-clipboard-check text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Выберите предмет, класс и дату</p>
    </div>
    <?php endif; ?>
</div>

<script>
function setAllStatus(status) {
    document.querySelectorAll(`.status-radio[value="${status}"]`).forEach(radio => {
        radio.checked = true;
        updateRowStyle(radio);
    });
}

function updateRowStyle(radio) {
    const row = radio.closest('.attendance-row');
    const labels = row.querySelectorAll('.status-label span');
    const statusColors = {
        present: 'bg-green-100 text-green-800 border-green-300',
        absent: 'bg-red-100 text-red-800 border-red-300',
        late: 'bg-yellow-100 text-yellow-800 border-yellow-300',
        excused: 'bg-blue-100 text-blue-800 border-blue-300',
    };
    
    row.querySelectorAll('.status-label').forEach(label => {
        const input = label.querySelector('input');
        const span = label.querySelector('span');
        // Сбрасываем стили
        span.className = span.className.replace(/bg-\S+/g, '').replace(/text-\S+/g, '').replace(/border-\S+/g, '');
        
        if (input.checked) {
            span.classList.add(...statusColors[input.value].split(' '));
        } else {
            span.classList.add('bg-gray-50', 'text-gray-500', 'border-gray-200');
        }
        // Добавляем общие классы обратно
        span.classList.add('inline-flex', 'items-center', 'gap-1', 'px-3', 'py-1.5', 'rounded-lg', 'text-xs', 'font-medium', 'border', 'transition');
    });
}
</script>