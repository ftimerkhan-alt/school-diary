<!-- Написать сообщение -->
<div class="max-w-2xl mx-auto space-y-4">
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3">
        <div class="flex items-center gap-2">
            <a href="<?= url('messages/inbox') ?>" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                <i class="fas fa-inbox mr-1"></i> Входящие
            </a>
            <a href="<?= url('messages/sent') ?>" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                <i class="fas fa-paper-plane mr-1"></i> Отправленные
            </a>
            <span class="ml-auto px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white">
                <i class="fas fa-pen mr-1"></i> Написать
            </span>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">
                <i class="fas fa-pen-fancy text-indigo-500 mr-2"></i>
                <?= $replyMessage ? 'Ответ на сообщение' : 'Новое сообщение' ?>
            </h2>
        </div>
        
        <form method="POST" action="<?= url('messages/send') ?>" class="p-6 space-y-4">
            <?= csrfField() ?>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Получатель <span class="text-red-500">*</span></label>
                <select name="receiver_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none bg-white">
                    <option value="">Выберите получателя</option>
                    <?php 
                    $currentGroup = '';
                    foreach ($recipients as $r): 
                        if ($r['role_name'] !== $currentGroup) {
                            if ($currentGroup) echo '</optgroup>';
                            $currentGroup = $r['role_name'];
                            echo '<optgroup label="' . e($currentGroup) . '">';
                        }
                        $selected = ($replyMessage && $replyMessage['sender_id'] == $r['id']) ? 'selected' : '';
                    ?>
                    <option value="<?= $r['id'] ?>" <?= $selected ?>><?= e($r['full_name']) ?></option>
                    <?php endforeach; ?>
                    <?php if ($currentGroup) echo '</optgroup>'; ?>
                </select>
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Тема <span class="text-red-500">*</span></label>
                <input type="text" name="subject" required
                       value="<?= $replyMessage ? e('Re: ' . $replyMessage['subject']) : '' ?>"
                       class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none"
                       placeholder="Тема сообщения">
            </div>
            
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1">Сообщение <span class="text-red-500">*</span></label>
                <textarea name="message" required rows="8"
                          class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 outline-none resize-y"
                          placeholder="Текст сообщения..."><?php if ($replyMessage): ?>

---
<?= e($replyMessage['sender_name']) ?> написал(а) <?= formatDateTime($replyMessage['created_at']) ?>:
<?= e($replyMessage['message']) ?><?php endif; ?></textarea>
            </div>
            
            <?php if ($replyMessage): ?>
            <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                <p class="text-xs text-gray-500 mb-1">Исходное сообщение от <?= e($replyMessage['sender_name']) ?>:</p>
                <p class="text-sm text-gray-700"><?= e(mb_substr($replyMessage['message'], 0, 200)) ?>...</p>
            </div>
            <?php endif; ?>
            
            <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition font-medium">
                    <i class="fas fa-paper-plane mr-1"></i> Отправить
                </button>
                <a href="<?= url('messages/inbox') ?>" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                    Отмена
                </a>
            </div>
        </form>
    </div>
</div>