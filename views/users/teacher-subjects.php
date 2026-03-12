<!-- Назначение предметов и классов учителю -->
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Информация об учителе -->
    <div class="bg-gradient-to-r from-purple-500 to-indigo-600 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 bg-white/20 rounded-full flex items-center justify-center text-2xl font-bold">
                <?= mb_strtoupper(mb_substr($teacher['full_name'], 0, 1)) ?>
            </div>
            <div>
                <h2 class="text-xl font-bold"><?= e($teacher['full_name']) ?></h2>
                <p class="text-white/80">Назначение предметов и классов</p>
            </div>
        </div>
    </div>
    
    <form method="POST" action="<?= url('users/save-teacher-subjects/' . $teacherId) ?>" class="space-y-6">
        <?= csrfField() ?>
        
        <!-- Выбор предметов -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-book text-purple-500 mr-2"></i>Предметы учителя
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                <?php foreach ($subjects as $subj): ?>
                <label class="flex items-center gap-2 p-3 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition">
                    <input type="checkbox" name="subjects[]" value="<?= $subj['id'] ?>" 
                           <?= in_array($subj['id'], $teacherSubjectIds) ? 'checked' : '' ?>
                           class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                    <span class="text-sm font-medium text-gray-700"><?= e($subj['name']) ?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Привязка к классам -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="text-lg font-bold text-gray-800 mb-4">
                <i class="fas fa-school text-indigo-500 mr-2"></i>Классы по предметам
            </h3>
            <p class="text-sm text-gray-500 mb-4">Отметьте, в каких классах учитель ведёт каждый предмет</p>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-semibold text-gray-500">Предмет / Класс</th>
                            <?php foreach ($classes as $cls): ?>
                            <th class="px-3 py-2 text-center text-xs font-semibold text-gray-500"><?= e($cls['name']) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php foreach ($subjects as $subj): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 font-medium text-sm text-gray-800"><?= e($subj['name']) ?></td>
                            <?php foreach ($classes as $cls): 
                                $key = $subj['id'] . '_' . $cls['id'];
                                $checked = isset($classSubjectMap[$key]);
                            ?>
                            <td class="px-3 py-3 text-center">
                                <input type="checkbox" name="cs_<?= $subj['id'] ?>_<?= $cls['id'] ?>" value="1"
                                       <?= $checked ? 'checked' : '' ?>
                                       class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500 cursor-pointer">
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class="fas fa-save mr-1"></i> Сохранить
            </button>
            <a href="<?= url('users') ?>" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                Назад
            </a>
        </div>
    </form>
</div>