<!-- Управление классами и предметами -->
<div class="space-y-6">
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- ========== КЛАССЫ ========== -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-school text-indigo-500 mr-2"></i>Добавить класс
                </h3>
                <form method="POST" action="<?= url('users/add-class') ?>" class="space-y-3">
                    <?= csrfField() ?>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Название</label>
                            <input type="text" name="class_name" placeholder="Например: 11Б" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-500 mb-1">Учебный год</label>
                            <input type="number" name="class_year" value="<?= currentAcademicYear() ?>" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 mb-1">Классный руководитель (необязательно)</label>
                        <select name="class_teacher_id" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                            <option value="">— Не назначен —</option>
                            <?php foreach ($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= e($t['full_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="w-full px-4 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                        <i class="fas fa-plus mr-1"></i> Добавить класс
                    </button>
                </form>
            </div>
            
            <!-- Список классов -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">
                        <i class="fas fa-list text-gray-400 mr-2"></i>Все классы
                        <span class="text-gray-400 font-normal text-sm ml-1">(<?= count($classes) ?>)</span>
                    </h3>
                </div>
                <?php if (!empty($classes)): ?>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($classes as $cls): ?>
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center font-bold text-indigo-600">
                                <?= e($cls['name']) ?>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Класс <?= e($cls['name']) ?></p>
                                <p class="text-xs text-gray-500">
                                    <?= $cls['student_count'] ?> <?= plural($cls['student_count'], 'ученик', 'ученика', 'учеников') ?>
                                    <?php if (!empty($cls['teacher_name'])): ?>
                                        · Кл. рук.: <?= e($cls['teacher_name']) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400"><?= $cls['year'] ?></span>
                            <?php if ($cls['student_count'] == 0): ?>
                            <form method="POST" action="<?= url('users/delete-class/' . $cls['id']) ?>" 
                                  onsubmit="return confirm('Удалить класс <?= e($cls['name']) ?>?')">
                                <?= csrfField() ?>
                                <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Удалить">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="p-1.5 text-gray-300" title="Нельзя удалить класс с учениками">
                                <i class="fas fa-lock text-sm"></i>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-school text-3xl mb-2 block"></i>
                    Классов пока нет
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- ========== ПРЕДМЕТЫ ========== -->
        <div class="space-y-4">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-book text-purple-500 mr-2"></i>Добавить предмет
                </h3>
                <form method="POST" action="<?= url('users/add-subject') ?>" class="flex gap-3">
                    <?= csrfField() ?>
                    <input type="text" name="subject_name" placeholder="Название предмета" required
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
                    <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        <i class="fas fa-plus mr-1"></i> Добавить
                    </button>
                </form>
            </div>
            
            <!-- Список предметов -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800">
                        <i class="fas fa-list text-gray-400 mr-2"></i>Все предметы
                        <span class="text-gray-400 font-normal text-sm ml-1">(<?= count($subjects) ?>)</span>
                    </h3>
                </div>
                <?php if (!empty($subjects)): ?>
                <div class="divide-y divide-gray-100">
                    <?php foreach ($subjects as $subj): ?>
                    <div class="flex items-center justify-between px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-purple-500 text-sm"></i>
                            </div>
                            <span class="font-medium text-gray-800"><?= e($subj['name']) ?></span>
                        </div>
                        <form method="POST" action="<?= url('users/delete-subject/' . $subj['id']) ?>" 
                              onsubmit="return confirm('Удалить предмет «<?= e($subj['name']) ?>»? Это возможно только если предмет нигде не используется.')">
                            <?= csrfField() ?>
                            <button type="submit" class="p-1.5 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Удалить">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div class="p-8 text-center text-gray-400">
                    <i class="fas fa-book text-3xl mb-2 block"></i>
                    Предметов пока нет
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>