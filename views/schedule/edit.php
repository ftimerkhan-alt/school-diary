<!-- Редактор расписания -->
<div class="space-y-4">
    
    <!-- Выбор класса и действия -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex flex-col md:flex-row gap-3 items-end">
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Класс</label>
                <form method="GET" action="<?= url('schedule/edit') ?>" id="classSelectForm">
                    <input type="hidden" name="route" value="schedule/edit">
                    <select name="class_id" onchange="this.form.submit()"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>>
                            <?= e($c['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </form>
            </div>
            
            <button onclick="document.getElementById('addLessonModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Добавить урок
            </button>
            
            <button onclick="document.getElementById('copyModal').classList.remove('hidden')"
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                <i class="fas fa-copy mr-1"></i> Копировать
            </button>
        </div>
    </div>
    
    <!-- Расписание в таблице -->
    <?php if ($selectedClassId): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 w-20">Урок</th>
                        <th class="px-3 py-3 text-left text-xs font-semibold text-gray-500 w-24">Время</th>
                        <?php foreach ($days as $dNum => $dName): ?>
                        <th class="px-3 py-3 text-center text-xs font-semibold text-gray-500"><?= $dName ?></th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($lessonTimes as $order => $time): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-3 font-bold text-gray-600 text-center">
                            <span class="w-8 h-8 bg-gray-100 rounded-full inline-flex items-center justify-center"><?= $order ?></span>
                        </td>
                        <td class="px-3 py-3 text-xs text-gray-500">
                            <?= $time['start'] ?><br><?= $time['end'] ?>
                        </td>
                        <?php foreach ($days as $dNum => $dName):
                            $lesson = null;
                            if (isset($schedule[$dNum])) {
                                foreach ($schedule[$dNum] as $l) {
                                    if ($l['lesson_order'] == $order) {
                                        $lesson = $l;
                                        break;
                                    }
                                }
                            }
                        ?>
                        <td class="px-2 py-2 text-center">
                            <?php if ($lesson): ?>
                            <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-2 relative group">
                                <p class="font-medium text-indigo-800 text-xs"><?= e($lesson['subject_name']) ?></p>
                                <p class="text-[10px] text-indigo-500"><?= e($lesson['teacher_name']) ?></p>
                                <?php if ($lesson['room']): ?>
                                <p class="text-[10px] text-gray-400">каб. <?= e($lesson['room']) ?></p>
                                <?php endif; ?>
                                
                                <!-- Кнопка удаления -->
                                <form method="POST" action="<?= url('schedule/delete') ?>" 
                                      onsubmit="return confirm('Удалить этот урок из расписания?')"
                                      class="absolute -top-2 -right-2 hidden group-hover:block">
                                    <?= csrfField() ?>
                                    <input type="hidden" name="id" value="<?= $lesson['id'] ?>">
                                    <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                                    <button type="submit" class="w-5 h-5 bg-red-500 text-white rounded-full flex items-center justify-center text-xs hover:bg-red-600 shadow">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </form>
                            </div>
                            <?php else: ?>
                            <div class="h-16 border border-dashed border-gray-200 rounded-lg flex items-center justify-center cursor-pointer hover:border-indigo-300 hover:bg-indigo-50/30 transition"
                                 onclick="quickAdd(<?= $dNum ?>, <?= $order ?>)">
                                <i class="fas fa-plus text-gray-300"></i>
                            </div>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Модальное окно: Добавление урока -->
<div id="addLessonModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i>Добавить урок</h3>
                <button onclick="this.closest('.fixed').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form method="POST" action="<?= url('schedule/store') ?>" class="p-6 space-y-4">
                <?= csrfField() ?>
                <input type="hidden" name="class_id" value="<?= $selectedClassId ?>">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">День недели</label>
                        <select name="day_of_week" id="addDayOfWeek" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" required>
                            <?php foreach ($days as $dNum => $dName): ?>
                            <option value="<?= $dNum ?>"><?= $dName ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Номер урока</label>
                        <select name="lesson_order" id="addLessonOrder" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" required>
                            <?php foreach ($lessonTimes as $order => $time): ?>
                            <option value="<?= $order ?>"><?= $order ?> (<?= $time['start'] ?>-<?= $time['end'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Предмет</label>
    <select name="subject_id" id="subjectIdSelect" onchange="loadTeachersForSubject()" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" required>
        <option value="">Выберите предмет</option>
        <?php foreach ($subjects as $s): ?>
        <option value="<?= $s['id'] ?>"><?= e($s['name']) ?></option>
        <?php endforeach; ?>
    </select>
</div>
                
                <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1">Учитель</label>
    <select name="teacher_id" id="teacherIdSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" required>
        <option value="">Сначала выберите предмет</option>
    </select>
</div>
                
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Начало</label>
                        <input type="time" name="time_start" id="addTimeStart" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Конец</label>
                        <input type="time" name="time_end" id="addTimeEnd" class="w-full px-3 py-2 border border-gray-300 rounded-lg" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Кабинет</label>
                        <input type="text" name="room" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="301">
                    </div>
                </div>
                
                <div class="flex gap-3 pt-2">
                    <button type="submit" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                        <i class="fas fa-check mr-1"></i> Добавить
                    </button>
                    <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Модальное окно: Копирование расписания -->
<div id="copyModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-copy text-blue-500 mr-2"></i>Копировать расписание</h3>
                <button onclick="this.closest('.fixed').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form method="POST" action="<?= url('schedule/copy') ?>" class="p-6 space-y-4"
                  onsubmit="return confirm('Текущее расписание выбранного класса будет заменено. Продолжить?')">
                <?= csrfField() ?>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Откуда (источник)</label>
                    <select name="from_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" required>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Куда (целевой класс)</label>
                    <select name="to_class_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white" required>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-sm text-yellow-700">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    Внимание! Текущее расписание целевого класса будет полностью заменено.
                </div>
                <div class="flex gap-3">
                    <button type="submit" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                        <i class="fas fa-copy mr-1"></i> Копировать
                    </button>
                    <button type="button" onclick="this.closest('.fixed').classList.add('hidden')" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        Отмена
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Быстрое добавление урока по клику на пустую ячейку
function quickAdd(day, order) {
    const lessonTimes = <?= json_encode($lessonTimes) ?>;
    
    document.getElementById('addDayOfWeek').value = day;
    document.getElementById('addLessonOrder').value = order;
    
    if (lessonTimes[order]) {
        document.getElementById('addTimeStart').value = lessonTimes[order].start;
        document.getElementById('addTimeEnd').value = lessonTimes[order].end;
    }
    
    document.getElementById('addLessonModal').classList.remove('hidden');
}

// Автозаполнение времени при смене урока
document.getElementById('addLessonOrder').addEventListener('change', function() {
    const lessonTimes = <?= json_encode($lessonTimes) ?>;
    const order = this.value;
    if (lessonTimes[order]) {
        document.getElementById('addTimeStart').value = lessonTimes[order].start;
        document.getElementById('addTimeEnd').value = lessonTimes[order].end;
    }
});

async function loadTeachersForSubject(preselectTeacherId = null) {
    const subjSelect = document.getElementById('subjectIdSelect');
    const teacherSelect = document.getElementById('teacherIdSelect');
    if (!subjSelect || !teacherSelect) return;

    const subjectId = subjSelect.value;

    teacherSelect.innerHTML = '<option value="">Загрузка...</option>';

    if (!subjectId) {
        teacherSelect.innerHTML = '<option value="">Сначала выберите предмет</option>';
        return;
    }

    try {
        const res = await fetch('<?= url("api/teachers-by-subject") ?>/' + encodeURIComponent(subjectId));
        const data = await res.json();

        if (!data.success) {
            teacherSelect.innerHTML = '<option value="">Ошибка загрузки</option>';
            return;
        }

        const teachers = data.data || [];
        if (teachers.length === 0) {
            teacherSelect.innerHTML = '<option value="">Нет учителей по этому предмету</option>';
            return;
        }

        teacherSelect.innerHTML = '<option value="">Выберите учителя</option>';
        teachers.forEach(t => {
            const opt = document.createElement('option');
            opt.value = t.id;
            opt.textContent = t.full_name;
            teacherSelect.appendChild(opt);
        });

        if (preselectTeacherId) teacherSelect.value = preselectTeacherId;

    } catch (e) {
        teacherSelect.innerHTML = '<option value="">Ошибка сети</option>';
    }
}
</script>