        </main>
        
        <!-- Подвал -->
        <footer class="bg-white border-t border-gray-200 px-6 py-3">
            <div class="flex flex-col sm:flex-row items-center justify-between text-sm text-gray-500">
                <p>&copy; <?= date('Y') ?> <?= e(APP_NAME) ?>. Все права защищены.</p>
                <p class="mt-1 sm:mt-0">Версия <?= APP_VERSION ?></p>
            </div>
        </footer>
    </div>
</div>

<!-- Toast-уведомления -->
<div id="toastContainer" class="fixed bottom-4 right-4 z-[100] space-y-2"></div>

<script src="<?= url('assets/js/app.js') ?>"></script>

<script>
// Sidebar toggle
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    sidebar.classList.toggle('-translate-x-full');
    overlay.classList.toggle('hidden');
}

// Profile menu toggle
function toggleProfileMenu() {
    const menu = document.getElementById('profileMenu');
    menu.classList.toggle('hidden');
}

// Закрытие профиль-меню при клике вне него
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('profileDropdown');
    const menu = document.getElementById('profileMenu');
    if (dropdown && !dropdown.contains(e.target)) {
        menu.classList.add('hidden');
    }
});

// Обновление счётчика непрочитанных каждые 30 сек
setInterval(function() {
    fetch('<?= url('api/unread-count') ?>')
        .then(r => r.json())
        .then(data => {
            if (data.success && data.count > 0) {
                document.querySelectorAll('.unread-badge').forEach(el => {
                    el.textContent = data.count > 9 ? '9+' : data.count;
                    el.classList.remove('hidden');
                });
            }
        }).catch(() => {});
}, 30000);
</script>

</body>
</html>