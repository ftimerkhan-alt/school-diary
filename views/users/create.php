<!-- Форма создания пользователя -->
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-user-plus text-indigo-500 mr-2"></i>Новый пользователь
            </h2>
        </div>
        
        <form method="POST" action="<?= url('users/store') ?>" id="createUserForm" class="p-6 space-y-5">
            <?= csrfField() ?>
            
            <!-- Основные данные -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">ФИО <span class="text-red-500">*</span></label>
                    <input type="text" name="full_name" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="Иванов Иван Иванович">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Роль <span class="text-red-500">*</span></label>
                    <select name="role_id" id="roleSelect" required onchange="onRoleChange()"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">Выберите роль</option>
                        <?php foreach ($roles as $r): ?>
                        <option value="<?= $r['id'] ?>" data-name="<?= e($r['name']) ?>"><?= e($r['display_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Логин <span class="text-red-500">*</span></label>
                    <input type="text" name="login" required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="ivanov">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Пароль <span class="text-red-500">*</span></label>
                    <input type="text" name="password" required value="123"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Email</label>
                    <input type="email" name="email"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="email@example.com">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Телефон</label>
                    <input type="text" name="phone"
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition"
                           placeholder="+7(999)123-45-67">
                </div>
            </div>
            
            <!-- Блок для ученика -->
            <div id="studentFields" class="hidden bg-blue-50 rounded-lg p-4 border border-blue-200">
                <h4 class="font-semibold text-blue-800 mb-3"><i class="fas fa-user-graduate mr-1"></i> Данные ученика</h4>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Класс <span class="text-red-500">*</span></label>
                    <select name="student_class_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">Выберите класс</option>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Блок для классного руководителя -->
            <div id="classTeacherFields" class="hidden bg-indigo-50 rounded-lg p-4 border border-indigo-200">
                <h4 class="font-semibold text-indigo-800 mb-3"><i class="fas fa-chalkboard-teacher mr-1"></i> Классное руководство</h4>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1">Класс</label>
                    <select name="class_teacher_class_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                        <option value="">Выберите класс</option>
                        <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= e($c['name']) ?> <?= $c['teacher_name'] ? '(' . e($c['teacher_name']) . ')' : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <!-- Блок для родителя -->
            <div id="parentFields" class="hidden bg-orange-50 rounded-lg p-4 border border-orange-200">
                <h4 class="font-semibold text-orange-800 mb-3"><i class="fas fa-child mr-1"></i> Дети</h4>
                <div id="childrenContainer">
                    <div class="child-row grid grid-cols-1 md:grid-cols-3 gap-3 mb-3">
                        <div class="md:col-span-2">
                            <select name="children[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="">Выберите ученика</option>
                                <?php foreach ($students as $st): ?>
                                <option value="<?= $st['id'] ?>"><?= e($st['full_name']) ?> (<?= e($st['class_name']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div>
                            <select name="relationships[]" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none transition bg-white">
                                <option value="Мать">Мать</option>
                                <option value="Отец">Отец</option>
                                <option value="Опекун">Опекун</option>
                                <option value="Другое">Другое</option>
                            </select>
                        </div>
                    </div>
                </div>
                <button type="button" onclick="addChildRow()" class="text-sm text-orange-600 hover:text-orange-800 font-medium">
                    <i class="fas fa-plus mr-1"></i> Добавить ребёнка
                </button>
            </div>
            
            <!-- Кнопки -->
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    <i class="fas fa-save mr-1"></i> Создать
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
    const select = document.getElementById('roleSelect');
    const option = select.options[select.selectedIndex];
    const roleName = option.getAttribute('data-name') || '';
    
    document.getElementById('studentFields').classList.toggle('hidden', roleName !== 'student');
    document.getElementById('classTeacherFields').classList.toggle(
    'hidden',
    !(roleName === 'class_teacher' || roleName === 'head_teacher')
);
    document.getElementById('parentFields').classList.toggle('hidden', roleName !== 'parent');
}

function addChildRow() {
    const container = document.getElementById('childrenContainer');
    const firstRow = container.querySelector('.child-row');
    const newRow = firstRow.cloneNode(true);
    newRow.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    container.appendChild(newRow);
}
</script>