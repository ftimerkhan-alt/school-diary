<!-- Моё расписание -->
<div class="space-y-4">
    
    <?php if (!empty($title)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 text-center">
        <h2 class="text-xl font-bold text-gray-800">
            <i class="fas fa-calendar-alt text-indigo-500 mr-2"></i><?= e($title) ?>
        </h2>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($schedule)): ?>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        <?php for ($day = 1; $day <= 6; $day++): 
            $dayLessons = $schedule[$day] ?? [];
            $isToday = ($day == $todayDow);
        ?>
        <div class="bg-white rounded-xl shadow-sm border <?= $isToday ? 'border-indigo-300 ring-2 ring-indigo-100' : 'border-gray-100' ?> overflow-hidden">
            <div class="px-4 py-3 <?= $isToday ? 'bg-indigo-50' : 'bg-gray-50' ?> border-b border-gray-100">
                <h3 class="font-bold <?= $isToday ? 'text-indigo-700' : 'text-gray-700' ?>">
                    <?php if ($isToday): ?><i class="fas fa-star text-yellow-500 mr-1"></i><?php endif; ?>
                    <?= dayOfWeekName($day) ?>
                    <?php if ($isToday): ?><span class="text-xs font-normal text-indigo-400 ml-2">Сегодня</span><?php endif; ?>
                </h3>
            </div>
            
            <?php if (!empty($dayLessons)): ?>
                <?php $currentDate = $weekDates[$day] ?? null; ?>
            <div class="divide-y divide-gray-50">
                <?php foreach ($dayLessons as $lesson):
                    $isCurrent = ($isToday && $lesson['lesson_order'] == $currentLesson);
                ?>
                <div class="flex items-center gap-3 px-4 py-3 <?= $isCurrent ? 'bg-indigo-50' : 'hover:bg-gray-50' ?> transition">
                    <div class="w-8 h-8 rounded-full <?= $isCurrent ? 'bg-indigo-500 text-white animate-pulse' : 'bg-gray-100 text-gray-500' ?> flex items-center justify-center font-bold text-sm flex-shrink-0">
                        <?= $lesson['lesson_order'] ?>
                    </div>
                    <div class="flex-1 min-w-0">
    <p class="font-medium text-gray-800 text-sm truncate"><?= e($lesson['subject_name']) ?></p>

    <p class="text-xs text-gray-400 truncate">
        <?php if (isset($lesson['class_name'])): ?>
            Класс <?= e($lesson['class_name']) ?>
        <?php else: ?>
            <?= e($lesson['teacher_name'] ?? '') ?>
        <?php endif; ?>
        <?php if (!empty($lesson['room'])): ?> · каб. <?= e($lesson['room']) ?><?php endif; ?>
    </p>

    <?php
    $hw = null;
$lessonClassId = isset($lesson['class_id']) ? (int)$lesson['class_id'] : null;

if (!empty($currentDate) && $lessonClassId && isset($homeworkMap[$currentDate][$lesson['subject_id']][$lessonClassId])) {
    $hw = $homeworkMap[$currentDate][$lesson['subject_id']][$lessonClassId];
}
    ?>

    <?php if ($hw): ?>
    <div class="mt-2 p-2 rounded-lg bg-blue-50 border border-blue-100">
        <p class="text-xs font-semibold text-blue-700">
            <i class="fas fa-book-open mr-1"></i> Домашнее задание
        </p>

        <?php if (!empty($hw['title'])): ?>
            <p class="text-xs text-gray-700 font-medium mt-1"><?= e($hw['title']) ?></p>
        <?php endif; ?>

        <p class="text-xs text-gray-600 mt-1 leading-relaxed">
            <?= e($hw['description']) ?>
        </p>

    </div>
    <?php endif; ?>
</div>
                    <div class="flex flex-col items-end gap-2 flex-shrink-0">
    <span class="text-xs text-gray-400">
        <?= date('H:i', strtotime($lesson['time_start'])) ?>-<?= date('H:i', strtotime($lesson['time_end'])) ?>
    </span>

    <?php if (in_array(currentRole(), ['teacher', 'class_teacher', 'head_teacher']) && !empty($lesson['class_name'])): ?>
<button type="button"
        class="px-2.5 py-1 text-[11px] <?= $hw ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-700 hover:bg-blue-800' ?> text-white rounded-md transition"
        onclick="openHomeworkModalFromButton(this)"
        data-class-name="<?= e($lesson['class_name']) ?>"
        data-class-id="<?= (int)$lesson['class_id'] ?>"
        data-subject-id="<?= (int)$lesson['subject_id'] ?>"
        data-subject-name="<?= e($lesson['subject_name']) ?>"
        data-lesson-date="<?= e($currentDate ?? '') ?>"
        data-hw-id="<?= $hw ? (int)$hw['id'] : '' ?>"
        data-hw-title="<?= e($hw['title'] ?? '') ?>"
        data-hw-description="<?= e($hw['description'] ?? '') ?>">
    <i class="fas fa-book-open mr-1"></i> <?= $hw ? 'Изменить Д/З' : 'Задать Д/З' ?>
</button>
<?php endif; ?>
</div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="p-6 text-center text-gray-400 text-sm">
                <i class="fas fa-coffee mr-1"></i> Нет уроков
            </div>
            <?php endif; ?>
        </div>
        <?php endfor; ?>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-calendar-alt text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Расписание не найдено</p>
    </div>
    <?php endif; ?>
</div>

<!-- Модальное окно: Домашнее задание -->
<div id="homeworkModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeHomeworkModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg relative">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800">
                    <i class="fas fa-book-open text-blue-600 mr-2"></i>Домашнее задание
                </h3>
                <button onclick="closeHomeworkModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <form method="POST" action="<?= url('schedule/save-homework') ?>" class="p-6 space-y-4">
                <?= csrfField() ?>

                <input type="hidden" name="class_id" id="hwClassId">
                <input type="hidden" name="subject_id" id="hwSubjectId">
                <input type="hidden" name="homework_date" id="hwDate">
                <input type="hidden" name="homework_id" id="hwId">

                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3 text-sm text-gray-700">
                    <p><span class="font-semibold">Класс:</span> <span id="hwClassName"></span></p>
                    <p><span class="font-semibold">Предмет:</span> <span id="hwSubjectName"></span></p>
                    <p><span class="font-semibold">Дата урока:</span> <span id="hwLessonDateText"></span></p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Тема (необязательно)</label>
                    <input type="text" name="title"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                           placeholder="Например: Параграф 12">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Описание задания <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="5" required
                              class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-y"
                              placeholder="Введите домашнее задание..."></textarea>
                </div>

                <div class="flex items-center gap-3 pt-2">
    <button type="submit" class="px-5 py-2.5 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition font-medium">
        <i class="fas fa-save mr-1"></i> Сохранить
    </button>

    <button type="button" id="deleteHomeworkBtn"
            onclick="deleteHomework()"
            class="hidden px-5 py-2.5 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
        <i class="fas fa-trash mr-1"></i> Удалить
    </button>

    <button type="button" onclick="closeHomeworkModal()" class="px-5 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
        Отмена
    </button>
</div>
            </form>
        </div>
    </div>
</div>

<script>
function openHomeworkModalFromButton(btn) {
    if (!btn) return;

    const className = btn.dataset.className || '';
    const classId = btn.dataset.classId || '';
    const subjectId = btn.dataset.subjectId || '';
    const subjectName = btn.dataset.subjectName || '';
    const lessonDate = btn.dataset.lessonDate || '';
    const hwId = btn.dataset.hwId || '';
    const hwTitle = btn.dataset.hwTitle || '';
    const hwDescription = btn.dataset.hwDescription || '';

    openHomeworkModal(className, classId, subjectId, subjectName, lessonDate, hwId, hwTitle, hwDescription);
}

function openHomeworkModal(className, classId, subjectId, subjectName, lessonDate, hwId = '', hwTitle = '', hwDescription = '') {
    const modal = document.getElementById('homeworkModal');
    if (!modal) {
        console.error('Модальное окно homeworkModal не найдено');
        return;
    }

    document.getElementById('hwClassName').textContent = className;
    document.getElementById('hwClassId').value = classId;

    document.getElementById('hwSubjectName').textContent = subjectName;
    document.getElementById('hwSubjectId').value = subjectId;

    document.getElementById('hwDate').value = lessonDate;
    document.getElementById('hwLessonDateText').textContent = formatDateRu(lessonDate);

    document.getElementById('hwId').value = hwId || '';

    const titleInput = document.querySelector('#homeworkModal input[name="title"]');
    const descInput = document.querySelector('#homeworkModal textarea[name="description"]');

    if (titleInput) titleInput.value = hwTitle || '';
    if (descInput) descInput.value = hwDescription || '';

    const deleteBtn = document.getElementById('deleteHomeworkBtn');
    if (deleteBtn) {
        if (hwId) {
            deleteBtn.classList.remove('hidden');
        } else {
            deleteBtn.classList.add('hidden');
        }
    }

    modal.classList.remove('hidden');
}

function closeHomeworkModal() {
    const modal = document.getElementById('homeworkModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function deleteHomework() {
    const hwId = document.getElementById('hwId')?.value;
    if (!hwId) return;

    if (!confirm('Удалить это домашнее задание?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '<?= url('schedule/delete-homework') ?>';

    const token = document.querySelector('#homeworkModal input[name="csrf_token"]')?.value || '';

    form.innerHTML = `
        <input type="hidden" name="csrf_token" value="${token}">
        <input type="hidden" name="id" value="${hwId}">
    `;

    document.body.appendChild(form);
    form.submit();
}

function formatDateRu(dateStr) {
    if (!dateStr) return '';

    const parts = dateStr.split('-');
    if (parts.length !== 3) return dateStr;

    return parts[2] + '.' + parts[1] + '.' + parts[0];
}
</script>