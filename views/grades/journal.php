<!-- Журнал оценок -->
<div class="space-y-4">
    
    <!-- Фильтры -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="<?= url('grades/journal') ?>" class="flex flex-col md:flex-row gap-3" id="journalFilter">
            <input type="hidden" name="route" value="grades/journal">
            
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Предмет</label>
                <select name="subject_id" id="subjectSelect" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white text-sm">
                    <option value="">Выберите предмет</option>
                    <?php foreach ($availableSubjects as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $selectedSubjectId == $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex-1">
                <label class="block text-xs font-semibold text-gray-500 mb-1">Класс</label>
                <select name="class_id" id="classSelect" onchange="this.form.submit()"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white text-sm">
                    <option value="">Выберите класс</option>
                    <?php foreach ($availableClasses as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $selectedClassId == $c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">С</label>
                <input type="date" name="date_from" value="<?= e($dateFrom) ?>"
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
            </div>
            
            <div>
                <label class="block text-xs font-semibold text-gray-500 mb-1">По</label>
                <input type="date" name="date_to" value="<?= e($dateTo) ?>"
                       class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition text-sm">
            </div>
            
            <div class="flex items-end">
                <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition text-sm font-medium">
                    <i class="fas fa-sync-alt mr-1"></i> Обновить
                </button>
            </div>
        </form>
    </div>
    
    <?php if ($selectedClassId && $selectedSubjectId): ?>
    
    <!-- Журнал -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        
        <?php if ($canEdit): ?>
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <span>Средний балл класса:</span>
                <span class="font-bold text-lg <?= $classAvg >= 4 ? 'text-green-600' : ($classAvg >= 3 ? 'text-yellow-600' : 'text-red-600') ?>">
                    <?= $classAvg ?? '—' ?>
                </span>
            </div>
            <button onclick="openAddGradeModal()" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition text-sm font-medium">
                <i class="fas fa-plus mr-1"></i> Добавить оценку
            </button>
        </div>
        <?php endif; ?>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="journalTable">
                <thead class="bg-gray-50 sticky top-0">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600 whitespace-nowrap sticky left-0 bg-gray-50 z-10 min-w-[200px]">
                            Ученик
                        </th>
                        <?php foreach ($dates as $d): ?>
                        <th class="px-2 py-3 text-center font-semibold text-gray-600 whitespace-nowrap min-w-[60px]">
                            <div class="text-xs"><?= date('d.m', strtotime($d)) ?></div>
                            <div class="text-[10px] text-gray-400"><?= dayOfWeekShort(date('N', strtotime($d))) ?></div>
                        </th>
                        <?php endforeach; ?>
                        <th class="px-3 py-3 text-center font-semibold text-gray-600 whitespace-nowrap bg-indigo-50">Ср. балл</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php foreach ($students as $st): 
                        $studentGrades = $gradesMap[$st['id']] ?? [];
                        $allStudentGrades = [];
                        foreach ($studentGrades as $dateGrades) {
                            foreach ($dateGrades as $g) {
                                $allStudentGrades[] = $g['grade'];
                            }
                        }
                        $avg = count($allStudentGrades) > 0 ? round(array_sum($allStudentGrades) / count($allStudentGrades), 2) : null;
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium text-gray-800 whitespace-nowrap sticky left-0 bg-white z-10">
                            <?= e($st['full_name']) ?>
                        </td>
                        <?php foreach ($dates as $d): 
                            $dayGrades = $studentGrades[$d] ?? [];
                        ?>
                        <td class="px-1 py-2 text-center">
                            <div class="flex justify-center gap-0.5">
                                <?php foreach ($dayGrades as $g): ?>
                                <span class="grade-cell w-8 h-8 rounded-lg <?= gradeColorClass($g['grade']) ?> flex items-center justify-center font-bold text-sm cursor-pointer hover:opacity-80 transition"
                                      data-grade-id="<?= $g['id'] ?>"
                                      data-grade="<?= $g['grade'] ?>"
                                      data-comment="<?= e($g['comment'] ?? '') ?>"
                                      data-type="<?= e($g['grade_type']) ?>"
                                      <?php if ($canEdit): ?>onclick="openEditGradeModal(this)"<?php endif; ?>
                                      title="<?= $g['comment'] ? e($g['comment']) : '' ?> <?= $g['grade_type'] !== 'current' ? '(' . gradeTypeName($g['grade_type']) . ')' : '' ?>">
                                    <?= $g['grade'] ?>
                                </span>
                                <?php endforeach; ?>
                                <?php if (empty($dayGrades)): ?>
                                <span class="w-8 h-8 text-gray-300">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php endforeach; ?>
                        <td class="px-3 py-2 text-center bg-indigo-50/50">
                            <span class="font-bold <?= $avg !== null ? ($avg >= 4 ? 'text-green-600' : ($avg >= 3 ? 'text-yellow-600' : 'text-red-600')) : 'text-gray-400' ?>">
                                <?= $avg ?? '—' ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (empty($students)): ?>
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-users text-4xl mb-2 block"></i>
            В этом классе нет учеников
        </div>
        <?php elseif (empty($dates)): ?>
        <div class="p-8 text-center text-gray-400">
            <i class="fas fa-book-open text-4xl mb-2 block"></i>
            За выбранный период оценок нет
        </div>
        <?php endif; ?>
    </div>
    
    <?php else: ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
        <i class="fas fa-book-open text-5xl text-gray-300 mb-4"></i>
        <p class="text-gray-500 text-lg">Выберите предмет и класс для просмотра журнала</p>
    </div>
    <?php endif; ?>
</div>

<?php if ($canEdit): ?>
<!-- Модальное окно: Добавление оценки -->
<div id="addGradeModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('addGradeModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md relative animate-fade-in-up">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-plus-circle text-emerald-500 mr-2"></i>Новая оценка</h3>
                <button onclick="closeModal('addGradeModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Ученик</label>
                    <select id="addGradeStudent" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                        <?php foreach ($students as $st): ?>
                        <option value="<?= $st['id'] ?>"><?= e($st['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Оценка</label>
                        <div class="flex gap-2" id="gradeButtons">
                            <?php for ($i = 2; $i <= 5; $i++): ?>
                            <button type="button" onclick="selectGrade(this, <?= $i ?>)" 
                                    class="w-10 h-10 rounded-lg border-2 border-gray-300 font-bold text-gray-600 hover:border-indigo-500 transition grade-btn">
                                <?= $i ?>
                            </button>
                            <?php endfor; ?>
                        </div>
                        <input type="hidden" id="addGradeValue" value="">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1">Дата</label>
                        <input type="date" id="addGradeDate" value="<?= date('Y-m-d') ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Тип</label>
                    <select id="addGradeType" class="w-full px-3 py-2 border border-gray-300 rounded-lg bg-white">
                        <option value="current">Текущая</option>
                        <option value="homework">Домашняя работа</option>
                        <option value="test">Контрольная</option>
                        <option value="exam">Экзамен</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Комментарий</label>
                    <input type="text" id="addGradeComment" class="w-full px-3 py-2 border border-gray-300 rounded-lg" placeholder="Необязательно">
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex gap-3">
                <button onclick="submitAddGrade()" class="flex-1 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                    <i class="fas fa-check mr-1"></i> Сохранить
                </button>
                <button onclick="closeModal('addGradeModal')" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Отмена
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Модальное окно: Редактирование оценки -->
<div id="editGradeModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-black/50" onclick="closeModal('editGradeModal')"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm relative animate-fade-in-up">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-800"><i class="fas fa-edit text-blue-500 mr-2"></i>Редактировать оценку</h3>
                <button onclick="closeModal('editGradeModal')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <input type="hidden" id="editGradeId">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Оценка</label>
                    <div class="flex gap-2" id="editGradeButtons">
                        <?php for ($i = 2; $i <= 5; $i++): ?>
                        <button type="button" onclick="selectEditGrade(this, <?= $i ?>)" 
                                class="w-10 h-10 rounded-lg border-2 border-gray-300 font-bold text-gray-600 hover:border-indigo-500 transition edit-grade-btn">
                            <?= $i ?>
                        </button>
                        <?php endfor; ?>
                    </div>
                    <input type="hidden" id="editGradeValue" value="">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Комментарий</label>
                    <input type="text" id="editGradeComment" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                </div>
            </div>
            <div class="p-6 border-t border-gray-100 flex gap-3">
                <button onclick="submitEditGrade()" class="flex-1 py-2.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition font-medium">
                    <i class="fas fa-save mr-1"></i> Сохранить
                </button>
                <button onclick="submitDeleteGrade()" class="py-2.5 px-4 bg-red-600 text-white rounded-lg hover:bg-red-700 transition font-medium">
                    <i class="fas fa-trash"></i>
                </button>
                <button onclick="closeModal('editGradeModal')" class="px-4 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Отмена
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const subjectId = <?= $selectedSubjectId ?>;
const csrfToken = '<?= e(generateCSRFToken()) ?>';

function openAddGradeModal() { document.getElementById('addGradeModal').classList.remove('hidden'); }
function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

function selectGrade(btn, val) {
    document.querySelectorAll('.grade-btn').forEach(b => b.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700'));
    btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
    document.getElementById('addGradeValue').value = val;
}

function selectEditGrade(btn, val) {
    document.querySelectorAll('.edit-grade-btn').forEach(b => b.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700'));
    btn.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
    document.getElementById('editGradeValue').value = val;
}

function openEditGradeModal(el) {
    document.getElementById('editGradeId').value = el.dataset.gradeId;
    document.getElementById('editGradeValue').value = el.dataset.grade;
    document.getElementById('editGradeComment').value = el.dataset.comment;
    
    document.querySelectorAll('.edit-grade-btn').forEach(b => {
        b.classList.remove('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
        if (b.textContent.trim() == el.dataset.grade) {
            b.classList.add('border-indigo-500', 'bg-indigo-50', 'text-indigo-700');
        }
    });
    
    document.getElementById('editGradeModal').classList.remove('hidden');
}

function submitAddGrade() {
    const data = new FormData();
    data.append('student_id', document.getElementById('addGradeStudent').value);
    data.append('subject_id', subjectId);
    data.append('grade', document.getElementById('addGradeValue').value);
    data.append('date', document.getElementById('addGradeDate').value);
    data.append('grade_type', document.getElementById('addGradeType').value);
    data.append('comment', document.getElementById('addGradeComment').value);
    data.append('csrf_token', csrfToken);
    
    if (!data.get('grade')) { showToast('Выберите оценку', 'error'); return; }
    
    fetch('<?= url("grades/store") ?>', { method: 'POST', body: data })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast('Оценка добавлена', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showToast(d.error || 'Ошибка', 'error');
            }
        }).catch(() => showToast('Ошибка сети', 'error'));
}

function submitEditGrade() {
    const data = new FormData();
    data.append('id', document.getElementById('editGradeId').value);
    data.append('grade', document.getElementById('editGradeValue').value);
    data.append('comment', document.getElementById('editGradeComment').value);
    data.append('csrf_token', csrfToken);
    
    fetch('<?= url("grades/update") ?>', { method: 'POST', body: data })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast('Оценка обновлена', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showToast(d.error || 'Ошибка', 'error');
            }
        }).catch(() => showToast('Ошибка сети', 'error'));
}

function submitDeleteGrade() {
    if (!confirm('Удалить эту оценку?')) return;
    
    const data = new FormData();
    data.append('id', document.getElementById('editGradeId').value);
    data.append('csrf_token', csrfToken);
    
    fetch('<?= url("grades/delete") ?>', { method: 'POST', body: data })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                showToast('Оценка удалена', 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showToast(d.error || 'Ошибка', 'error');
            }
        }).catch(() => showToast('Ошибка сети', 'error'));
}
</script>
<?php endif; ?>