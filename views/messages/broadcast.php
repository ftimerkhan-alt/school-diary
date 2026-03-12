<!-- Рассылка -->
<div class="max-w-2xl mx-auto space-y-4">

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
        <div class="flex items-center gap-2 text-sm text-gray-600">
            <i class="fas fa-bullhorn text-indigo-500"></i>
            <span class="font-semibold">Рассылка</span>
            <span class="text-gray-300">/</span>
            <a class="text-indigo-600 hover:text-indigo-800" href="<?= url('messages/inbox') ?>">Сообщения</a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-bullhorn text-indigo-500 mr-2"></i>Создать рассылку
            </h2>
            <p class="text-sm text-gray-500 mt-1">
                Рассылка придёт пользователям как обычное сообщение во “Входящие”.
            </p>
        </div>

        <form method="POST" action="<?= url('messages/send-broadcast') ?>" class="p-6 space-y-4">
            <?= csrfField() ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Аудитория <span class="text-red-500">*</span></label>
                <select name="target" id="targetSelect" required
                        class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 outline-none"
                        onchange="onTargetChange()">
                    <?php foreach ($roleTargets as $key => $label): ?>
                        <option value="<?= e($key) ?>"><?= e($label) ?></option>
                    <?php endforeach; ?>
                    <?php if (in_array(currentRole(), ['admin','director','head_teacher'])): ?>
                        <option value="class">Ученикам и родителям выбранного класса</option>
                    <?php endif; ?>
                </select>
            </div>

            <?php if (in_array(currentRole(), ['admin','director','head_teacher'])): ?>
            <div id="classBlock" class="hidden">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Класс</label>
                <select name="class_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg bg-white focus:ring-2 focus:ring-indigo-500 outline-none">
                    <option value="0">— Выберите класс —</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>"><?= e($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <p class="text-xs text-gray-400 mt-1">Используется только когда аудитория = “класс”.</p>
            </div>
            <?php endif; ?>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Тема <span class="text-red-500">*</span></label>
                <input type="text" name="subject" required
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                       placeholder="Например: Важное объявление">
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Текст <span class="text-red-500">*</span></label>
                <textarea name="message" rows="7" required
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-y"
                          placeholder="Введите текст уведомления..."></textarea>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    <i class="fas fa-paper-plane mr-1"></i> Отправить рассылку
                </button>
                <a href="<?= url('messages/inbox') ?>" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function onTargetChange() {
    const t = document.getElementById('targetSelect');
    const classBlock = document.getElementById('classBlock');
    if (!classBlock) return;
    classBlock.classList.toggle('hidden', t.value !== 'class');
}
onTargetChange();
</script>