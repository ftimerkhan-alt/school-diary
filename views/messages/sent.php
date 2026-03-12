<!-- Отправленные сообщения -->
<div class="space-y-4">
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3">
        <div class="flex items-center gap-2">
            <a href="<?= url('messages/inbox') ?>" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                <i class="fas fa-inbox mr-1"></i> Входящие
            </a>
            <a href="<?= url('messages/sent') ?>" class="px-4 py-2 rounded-lg text-sm font-medium bg-indigo-600 text-white transition">
                <i class="fas fa-paper-plane mr-1"></i> Отправленные
            </a>
            <a href="<?= url('messages/compose') ?>" class="ml-auto px-4 py-2 rounded-lg text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 transition">
                <i class="fas fa-pen mr-1"></i> Написать
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <?php if (empty($messages)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-paper-plane text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Отправленных сообщений нет</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($messages as $msg): ?>
            <a href="<?= url('messages/read/' . $msg['id']) ?>"
               class="flex items-center gap-4 px-4 py-4 hover:bg-gray-50 transition">
                
                <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center text-gray-500 text-sm font-bold flex-shrink-0">
                    <?= mb_strtoupper(mb_substr($msg['receiver_name'], 0, 1)) ?>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500">Кому:</span>
                        <span class="font-medium text-gray-800 text-sm truncate"><?= e($msg['receiver_name']) ?></span>
                        <span class="text-xs text-gray-400"><?= e($msg['receiver_role']) ?></span>
                    </div>
                    <p class="text-sm text-gray-600 truncate"><?= e($msg['subject']) ?></p>
                </div>
                
                <div class="flex items-center gap-2 flex-shrink-0">
                    <?php if ($msg['is_read']): ?>
                    <span class="text-xs text-green-500"><i class="fas fa-check-double"></i></span>
                    <?php else: ?>
                    <span class="text-xs text-gray-400"><i class="fas fa-check"></i></span>
                    <?php endif; ?>
                    <span class="text-xs text-gray-400">
                        <?= formatDateTime($msg['created_at'], 'd.m H:i') ?>
                    </span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>