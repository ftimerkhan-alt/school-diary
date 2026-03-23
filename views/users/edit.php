
<?php
/**
 * Форма редактирования пользователя
 */
$userRoleId = isset($user['role_id']) ? $user['role_id'] : 0;
$userRoleName = isset($user['role_name']) ? $user['role_name'] : '';
$userIsActive = isset($user['is_active']) ? (int)$user['is_active'] : 1;
?>
<!-- Форма редактирования пользователя -->
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-edit text-indigo-500 mr-2"></i>Редактирование: <?= e($user['full_name']) ?>
            </h2>
        </div>
        
        <form method="POST" action="<?= url('users/update/' . $user['id']) ?>" class="p-6 space-y-5">
            <?= csrfField() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ФИО <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required value="<?= e($user['full_name']) ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Роль</label>
                    <select name="role_id" id="roleSelect" onchange="onRoleChange()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" data-name="<?= e($r['name']) ?>" <?= $userRoleId == $r['id'] ? 'selected' : '' ?>>
                            <?= e($r['display_name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Логин</label>
                    <input type="text" name="login" value="<?= e($user['login']) ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Новый пароль <span class="text-gray-400 text-xs">(пусто = не менять)</span></label>
                    <input type="text" name="password"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="Новый пароль">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="<?= e($user['email'] ?? '') ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Телефон</label>
                    <input type="text" name="phone" value="<?= e($user['phone'] ?? '') ?>"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
            
            <!-- Статус -->
            <div class="flex items-center gap-3">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" <?= $userIsActive ? 'checked' : '' ?>
                           class="w-5 h-5 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="ml-2 text-sm font-medium text-gray-700">Активен</span>
                </label>
            </div>
            
            <!-- Блок для ученика -->
            <div id="studentFields" class="<?= $userRoleName === 'student' ? '' : 'hidden' ?> bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h4 class="font-semibold text-blue-800 mb-3"><i class="fas fa-user-graduate mr-1"></i> Данные ученика</h4>
                <select name="student_class_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                    <option value="">Выберите класс</option>
                    <?php foreach ($classes as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= (isset($student) && $student && $student['class_id'] == $c['id']) ? 'selected' : '' ?>>
                        <?= e($c['name']) ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Блок для классного руководителя -->
<div id="classTeacherFields" class="<?= in_array($userRoleName, ['class_teacher', 'head_teacher']) ? '' : 'hidden' ?> bg-indigo-50 rounded-lg p-4 border border-indigo-200">
    <h4 class="font-semibold text-indigo-800 mb-3">
        <i class="fas fa-chalkboard-teacher mr-1"></i> Классное руководство
    </h4>

    <label class="block text-sm font-semibold text-gray-700 mb-1">Закреплённый класс</label>
    <select name="class_teacher_class_id"
        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
    <option value="0" <?= ((int)$currentClassTeacherClassId === 0) ? 'selected' : '' ?>>
        — Не назначать класс —
    </option>

    <?php foreach ($classes as $c): ?>
    <?php
        $isCurrent = ((int)$currentClassTeacherClassId === (int)$c['id']);
        $isTakenByOther = !empty($c['teacher_name']) && !$isCurrent;
    ?>
    <option value="<?= (int)$c['id'] ?>" <?= $isCurrent ? 'selected' : '' ?>>
        <?= e($c['name']) ?>
        <?php if ($isCurrent): ?>
            (закреплён за этим руководителем)
        <?php elseif ($isTakenByOther): ?>
            (сейчас: <?= e($c['teacher_name']) ?>)
        <?php endif; ?>
    </option>
    <?php endforeach; ?>
</select>

<?php if (!empty($currentClassTeacherClassName)): ?>
<p class="text-sm text-gray-600 mb-3">
    Сейчас закреплённый класс:
    <span class="font-semibold text-blue-700"><?= e($currentClassTeacherClassName) ?></span>
</p>
<?php else: ?>
<p class="text-sm text-gray-500 mb-3">
    Сейчас класс не закреплён
</p>
<?php endif; ?>

    <p class="text-xs text-gray-500 mt-2">
        Если класс уже закреплён за другим учителем, система не позволит назначить его повторно.
    </p>
</div>
            
            <!-- Блок для родителя -->
            <div id="parentFields" class="<?= $userRoleName === 'parent' ? '' : 'hidden' ?> bg-orange-50 rounded-lg p-4 border border-orange-200">
                <h4 class="font-semibold text-orange-800 mb-3"><i class="fas fa-child mr-1"></i> Дети</h4>
                <div id="childrenContainer">
                    <?php if (!empty($parentChildren)): ?>
                        <?php foreach ($parentChildren as $pc): ?>
                        <div class="child-row grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                            <div class="md:col-span-2">
                                <select name="children[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white">
                                    <option value="">Выберите ученика</option>
                                    <?php foreach ($allStudents as $st): ?>
                                    <option value="<?= $st['id'] ?>" <?= $pc['id'] == $st['id'] ? 'selected' : '' ?>>
                                        <?= e($st['full_name']) ?> (<?= e($st['class_name']) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <select name="relationships[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white">
                                    <option value="Мать" <?= (isset($pc['relationship']) && $pc['relationship'] === 'Мать') ? 'selected' : '' ?>>Мать</option>
                                    <option value="Отец" <?= (isset($pc['relationship']) && $pc['relationship'] === 'Отец') ? 'selected' : '' ?>>Отец</option>
                                    <option value="Опекун" <?= (isset($pc['relationship']) && $pc['relationship'] === 'Опекун') ? 'selected' : '' ?>>Опекун</option>
                                    <option value="Другое" <?= (isset($pc['relationship']) && !in_array($pc['relationship'], ['Мать','Отец','Опекун'])) ? 'selected' : '' ?>>Другое</option>
                                </select>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="child-row grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                            <div class="md:col-span-2">
                                <select name="children[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white">
                                    <option value="">Выберите ученика</option>
                                    <?php foreach ($allStudents as $st): ?>
                                    <option value="<?= $st['id'] ?>"><?= e($st['full_name']) ?> (<?= e($st['class_name']) ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div>
                                <select name="relationships[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white">
                                    <option value="Мать">Мать</option>
                                    <option value="Отец">Отец</option>
                                    <option value="Опекун">Опекун</option>
                                    <option value="Другое">Другое</option>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="button" onclick="addChildRow()" class="text-sm text-orange-600 hover:text-orange-800 font-medium mt-2">
                    <i class="fas fa-plus mr-1"></i> Добавить ребёнка
                </button>
            </div>
            
            <!-- Информация -->
            <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-500">
                <p><strong>Создан:</strong> <?= formatDateTime($user['created_at'] ?? '') ?></p>
                <p><strong>Обновлён:</strong> <?= formatDateTime($user['updated_at'] ?? '') ?></p>
                <?php if (!empty($user['last_login'])): ?>
                <p><strong>Последний вход:</strong> <?= formatDateTime($user['last_login']) ?></p>
                <?php endif; ?>
            </div>
            
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    <i class="fas fa-save mr-1"></i> Сохранить
                </button>
                <a href="<?= url('users') ?>" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function onRoleChange() {
    var select = document.getElementById('roleSelect');
    var option = select.options[select.selectedIndex];
    var roleName = option.getAttribute('data-name') || '';
    
    document.getElementById('studentFields').classList.toggle('hidden', roleName !== 'student');
    document.getElementById('parentFields').classList.toggle('hidden', roleName !== 'parent');
    document.getElementById('classTeacherFields').classList.toggle(
    'hidden',
    !(roleName === 'class_teacher' || roleName === 'head_teacher')
);
}

function addChildRow() {
    var container = document.getElementById('childrenContainer');
    var firstRow = container.querySelector('.child-row');
    if (!firstRow) return;
    var newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('select').forEach(function(s) { s.selectedIndex = 0; });
    container.appendChild(newRow);
}
</script>