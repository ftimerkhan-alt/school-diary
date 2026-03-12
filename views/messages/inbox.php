<!-- Входящие сообщения -->
<div class="space-y-4">
    
    <!-- Навигация -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-3">
        <div class="flex items-center gap-2">
            <a href="<?= url('messages/inbox') ?>" class="px-4 py-2 rounded-lg text-sm font-medium <?= !isset($viewingMessage) ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' ?> transition">
                <i class="fas fa-inbox mr-1"></i> Входящие
            </a>
            <a href="<?= url('messages/sent') ?>" class="px-4 py-2 rounded-lg text-sm font-medium bg-gray-100 text-gray-700 hover:bg-gray-200 transition">
                <i class="fas fa-paper-plane mr-1"></i> Отправленные
            </a>
            <a href="<?= url('messages/compose') ?>" class="ml-auto px-4 py-2 rounded-lg text-sm font-medium bg-emerald-600 text-white hover:bg-emerald-700 transition">
                <i class="fas fa-pen mr-1"></i> Написать
            </a>
        </div>
    </div>
    
    <?php if (isset($viewingMessage)): ?>
    <!-- Просмотр сообщения -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <div class="flex items-start justify-between">
                <div>
                    <h2 class="text-xl font-bold text-gray-800"><?= e($viewingMessage['subject']) ?></h2>
                    <div class="flex items-center gap-2 mt-2 text-sm text-gray-500">
                        <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm">
                            <?= mb_strtoupper(mb_substr($viewingMessage['sender_name'], 0, 1)) ?>
                        </div>
                        <div>
                            <span class="font-medium text-gray-700"><?= e($viewingMessage['sender_name']) ?></span>
                            <span class="text-gray-400">(<?= e($viewingMessage['sender_role']) ?>)</span>
                        </div>
                        <span class="text-gray-300">→</span>
                        <span class="text-gray-600"><?= e($viewingMessage['receiver_name']) ?></span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1"><?= formatDateTime($viewingMessage['created_at']) ?></p>
                </div>
                <div class="flex gap-2">
                    <?php if ($viewingMessage['sender_id'] != currentUserId()): ?>
                    <a href="<?= url('messages/compose?reply_to=' . $viewingMessage['id']) ?>" 
                       class="px-3 py-1.5 bg-indigo-100 text-indigo-700 rounded-lg text-sm hover:bg-indigo-200 transition">
                        <i class="fas fa-reply mr-1"></i> Ответить
                    </a>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('messages/delete/' . $viewingMessage['id']) ?>" onsubmit="return confirm('Удалить сообщение?')">
                        <?= csrfField() ?>
                        <button class="px-3 py-1.5 bg-red-100 text-red-700 rounded-lg text-sm hover:bg-red-200 transition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="prose max-w-none text-gray-700 whitespace-pre-wrap"><?= e($viewingMessage['message']) ?></div>
        </div>
        <div class="p-4 border-t border-gray-100">
            <a href="<?= url('messages/inbox') ?>" class="text-sm text-indigo-600 hover:text-indigo-800">
                <i class="fas fa-arrow-left mr-1"></i> Назад к входящим
            </a>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Список сообщений -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <?php if (empty($messages)): ?>
        <div class="p-12 text-center">
            <i class="fas fa-inbox text-5xl text-gray-300 mb-4"></i>
            <p class="text-gray-500 text-lg">Входящих сообщений нет</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-gray-100">
            <?php foreach ($messages as $msg): ?>
            <a href="<?= url('messages/read/' . $msg['id']) ?>" 
               class="flex items-center gap-4 px-4 py-4 hover:bg-gray-50 transition <?= !$msg['is_read'] ? 'bg-indigo-50/50' : '' ?>">
                
                <div class="w-10 h-10 <?= !$msg['is_read'] ? 'bg-indigo-500' : 'bg-gray-200' ?> rounded-full flex items-center justify-center text-sm font-bold flex-shrink-0 <?= !$msg['is_read'] ? 'text-white' : 'text-gray-500' ?>">
                    <?= mb_strtoupper(mb_substr($msg['sender_name'], 0, 1)) ?>
                </div>
                
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2">
                        <span class="font-<?= !$msg['is_read'] ? 'bold' : 'medium' ?> text-gray-800 text-sm truncate">
                            <?= e($msg['sender_name']) ?>
                        </span>
                        <span class="text-xs text-gray-400"><?= e($msg['sender_role']) ?></span>
                        <?php if (!$msg['is_read']): ?>
                        <span class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0"></span>
                        <?php endif; ?>
                    </div>
                    <p class="text-sm <?= !$msg['is_read'] ? 'font-semibold text-gray-800' : 'text-gray-600' ?> truncate">
                        <?= e($msg['subject']) ?>
                    </p>
                    <p class="text-xs text-gray-400 truncate"><?= e(mb_substr($msg['message'], 0, 80)) ?>...</p>
                </div>
                
                <div class="text-xs text-gray-400 flex-shrink-0 text-right">
                    <?= formatDateTime($msg['created_at'], 'd.m') ?><br>
                    <?= formatDateTime($msg['created_at'], 'H:i') ?>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        
        <?php if (isset($pagination)): ?>
        <div class="p-4 border-t border-gray-100">
            <?= renderPagination($pagination, url('messages/inbox') . '?x=1') ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>