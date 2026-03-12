<!-- Список пользователей -->
<div class="space-y-4">
    
    <!-- Панель фильтров -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <form method="GET" action="<?= url('users') ?>" class="flex flex-col md:flex-row gap-3">
            <input type="hidden" name="route" value="users">
            
            <div class="flex-1">
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="<?= e($filters['search']) ?>" 
                           placeholder="Поиск по имени или логину..."
                           class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition">
                </div>
            </div>
            
            <select name="role" class="px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition bg-white">
                <option value="">Все роли</option>
                <?php foreach ($roles as $r): ?>
                <option value="<?= e($r['name']) ?>" <?= $filters['role'] === $r['name'] ? 'selected' : '' ?>>
                    <?= e($r['display_name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                <i class="fas fa-filter mr-1"></i> Фильтр
            </button>
            
            <?php if (isAdmin()): ?>
            <a href="<?= url('users/create') ?>" class="px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium text-center">
                <i class="fas fa-user-plus mr-1"></i> Добавить
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Таблица пользователей -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Пользователь</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Логин</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Роль</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden md:table-cell">Email</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider hidden lg:table-cell">Статус</th>
                        <?php if (isAdmin()): ?>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Действия</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                            <i class="fas fa-users text-4xl mb-2 block"></i>
                            Пользователи не найдены
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <?php 
                    $roleColors = [
                        'admin' => 'bg-red-100 text-red-700',
                        'director' => 'bg-yellow-100 text-yellow-700',
                        'head_teacher' => 'bg-blue-100 text-blue-700',
                        'class_teacher' => 'bg-indigo-100 text-indigo-700',
                        'teacher' => 'bg-green-100 text-green-700',
                        'student' => 'bg-purple-100 text-purple-700',
                        'parent' => 'bg-orange-100 text-orange-700',
                    ];
                    foreach ($users as $u): 
                        $color = $roleColors[$u['role_name']] ?? 'bg-gray-100 text-gray-700';
                    ?>
                    <tr class="hover:bg-gray-50 transition">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 bg-gradient-to-br from-indigo-400 to-purple-500 rounded-full flex items-center justify-center text-white text-sm font-bold">
                                    <?= mb_strtoupper(mb_substr($u['full_name'], 0, 1)) ?>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-800"><?= e($u['full_name']) ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3">
                            <code class="text-sm bg-gray-100 px-2 py-0.5 rounded"><?= e($u['login']) ?></code>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2.5 py-1 rounded-full text-xs font-semibold <?= $color ?>">
                                <?= e($u['role_display_name']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell text-sm text-gray-500"><?= e($u['email'] ?? '—') ?></td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <?php if ($u['is_active']): ?>
                            <span class="inline-flex items-center gap-1 text-xs text-green-600">
                                <span class="w-2 h-2 bg-green-500 rounded-full"></span> Активен
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center gap-1 text-xs text-red-600">
                                <span class="w-2 h-2 bg-red-500 rounded-full"></span> Заблокирован
                            </span>
                            <?php endif; ?>
                        </td>
                        <?php if (isAdmin()): ?>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <?php if (in_array($u['role_name'], ['teacher', 'class_teacher'])): 
                                    // Находим teacher_id
                                    $tModel = new Teacher();
                                    $tData = $tModel->findByUserId($u['id']);
                                    if ($tData):
                                ?>
                                <a href="<?= url('users/teacher-subjects/' . $tData['id']) ?>" 
                                   class="p-2 text-purple-600 hover:bg-purple-50 rounded-lg transition" title="Предметы">
                                    <i class="fas fa-book"></i>
                                </a>
                                <?php endif; endif; ?>
                                
                                <a href="<?= url('users/edit/' . $u['id']) ?>" 
                                   class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Редактировать">
                                    <i class="fas fa-edit"></i>
                                </a>
                                
                                <?php if ($u['id'] != currentUserId()): ?>
                                <form method="POST" action="<?= url('users/delete/' . $u['id']) ?>" 
                                      onsubmit="return confirm('Удалить пользователя <?= e($u['full_name']) ?>?')" class="inline">
                                    <?= csrfField() ?>
                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Удалить">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Пагинация -->
        <div class="px-4 py-3 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <p class="text-sm text-gray-500">
                    Показано <?= count($users) ?> из <?= $pagination['total'] ?> 
                    <?= plural($pagination['total'], 'пользователь', 'пользователя', 'пользователей') ?>
                </p>
                <?= renderPagination($pagination, url('users') . '?role=' . e($filters['role']) . '&search=' . e($filters['search'])) ?>
            </div>
        </div>
    </div>
        <!-- Управление классами (для админа) -->
    <?php if (isAdmin()): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="text-lg font-bold text-gray-800 mb-4">
            <i class="fas fa-school text-indigo-500 mr-2"></i>Управление классами
        </h3>
        
        <!-- Добавление класса -->
        <form method="POST" action="<?= url('users/add-class') ?>" class="flex flex-col sm:flex-row gap-3 mb-4">
            <?= csrfField() ?>
            <input type="text" name="class_name" placeholder="Например: 11Б" required
                   class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none">
            <input type="number" name="class_year" value="<?= currentAcademicYear() ?>" 
                   class="px-4 py-2 border border-gray-300 rounded-lg w-24 focus:ring-2 focus:ring-indigo-500 outline-none">
            <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition font-medium">
                <i class="fas fa-plus mr-1"></i> Добавить класс
            </button>
        </form>
        
        <!-- Список классов -->
        <?php 
        $classModel = new ClassModel();
        $allClasses = $classModel->getAll();
        ?>
        <?php if (!empty($allClasses)): ?>
        <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-2">
            <?php foreach ($allClasses as $cls): ?>
            <div class="bg-gray-50 rounded-lg p-3 text-center">
                <p class="font-bold text-gray-800"><?= e($cls['name']) ?></p>
                <p class="text-xs text-gray-500"><?= $cls['student_count'] ?> уч.</p>
                <p class="text-xs text-gray-400"><?= $cls['year'] ?></p>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="text-gray-400 text-sm">Классов пока нет</p>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>