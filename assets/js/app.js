/**
 * Электронный дневник — Клиентский JavaScript
 */

// =====================================================
// TOAST-УВЕДОМЛЕНИЯ
// =====================================================

/**
 * Показывает toast-уведомление
 * @param {string} message - текст сообщения
 * @param {string} type - тип: success, error, warning, info
 * @param {number} duration - длительность в мс (по умолчанию 4000)
 */
function showToast(message, type = 'info', duration = 4000) {
    const container = document.getElementById('toastContainer');
    if (!container) return;
    
    const icons = {
        success: 'fa-check-circle',
        error: 'fa-exclamation-circle',
        warning: 'fa-exclamation-triangle',
        info: 'fa-info-circle'
    };
    
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.style.setProperty('--toast-duration', duration + 'ms');
    toast.innerHTML = `
        <i class="fas ${icons[type] || icons.info} text-lg"></i>
        <span class="flex-1">${escapeHtml(message)}</span>
        <button class="toast-close" onclick="removeToast(this.parentElement)">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Устанавливаем длительность анимации прогресса
    toast.style.cssText += `--toast-duration: ${duration}ms;`;
    const afterStyle = document.createElement('style');
    const toastId = 'toast-' + Date.now();
    toast.id = toastId;
    afterStyle.textContent = `#${toastId}::after { animation-duration: ${duration}ms; }`;
    document.head.appendChild(afterStyle);
    
    container.appendChild(toast);
    
    // Автоудаление
    const timer = setTimeout(() => {
        removeToast(toast);
        afterStyle.remove();
    }, duration);
    
    toast.dataset.timer = timer;
}

/**
 * Удаляет toast с анимацией
 */
function removeToast(toast) {
    if (!toast || !toast.parentElement) return;
    
    if (toast.dataset.timer) {
        clearTimeout(parseInt(toast.dataset.timer));
    }
    
    toast.classList.add('animate-slide-out-down');
    setTimeout(() => {
        if (toast.parentElement) {
            toast.parentElement.removeChild(toast);
        }
    }, 300);
}

// =====================================================
// ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ
// =====================================================

/**
 * Экранирование HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

/**
 * Форматирование даты
 */
function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleDateString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric'
    });
}

/**
 * Форматирование даты и времени
 */
function formatDateTime(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    return d.toLocaleString('ru-RU', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

// =====================================================
// AJAX УТИЛИТЫ
// =====================================================

/**
 * Отправка POST-запроса через fetch
 */
async function postData(url, data = {}) {
    const formData = new FormData();
    for (const key in data) {
        formData.append(key, data[key]);
    }
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            body: formData
        });
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        showToast('Ошибка сети. Попробуйте ещё раз.', 'error');
        return { success: false, error: 'Network error' };
    }
}

/**
 * Отправка GET-запроса через fetch
 */
async function getData(url) {
    try {
        const response = await fetch(url);
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        return { success: false, error: 'Network error' };
    }
}

// =====================================================
// МОДАЛЬНЫЕ ОКНА
// =====================================================

/**
 * Открытие модального окна
 */
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Фокус на первом инпуте
        setTimeout(() => {
            const firstInput = modal.querySelector('input:not([type="hidden"]), select, textarea');
            if (firstInput) firstInput.focus();
        }, 100);
    }
}

/**
 * Закрытие модального окна
 */
function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }
}

/**
 * Закрытие модалки по Escape
 */
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.fixed:not(.hidden)').forEach(modal => {
            if (modal.querySelector('.bg-black\\/50, [class*="bg-black"]')) {
                modal.classList.add('hidden');
                document.body.style.overflow = '';
            }
        });
    }
});

// =====================================================
// ПОДТВЕРЖДЕНИЕ ДЕЙСТВИЙ
// =====================================================

/**
 * Подтверждение удаления
 */
function confirmDelete(message = 'Вы уверены, что хотите удалить?') {
    return confirm(message);
}

/**
 * Красивое подтверждение (TODO: заменить на модальное)
 */
function confirmAction(message, callback) {
    if (confirm(message)) {
        callback();
    }
}

// =====================================================
// РАБОТА С ФОРМАМИ
// =====================================================

/**
 * Сериализация формы в объект
 */
function serializeForm(form) {
    const formData = new FormData(form);
    const data = {};
    for (const [key, value] of formData.entries()) {
        data[key] = value;
    }
    return data;
}

/**
 * Проверка заполненности обязательных полей
 */
function validateRequired(form) {
    let valid = true;
    form.querySelectorAll('[required]').forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500');
            field.classList.remove('border-gray-300');
            valid = false;
            
            // Убираем красную рамку через 3 сек
            setTimeout(() => {
                field.classList.remove('border-red-500');
                field.classList.add('border-gray-300');
            }, 3000);
        }
    });
    
    if (!valid) {
        showToast('Заполните все обязательные поля', 'warning');
    }
    
    return valid;
}

// =====================================================
// ПОИСК И ФИЛЬТРАЦИЯ
// =====================================================

/**
 * Debounce-функция для поиска
 */
function debounce(func, wait = 300) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

/**
 * Фильтрация таблицы по тексту
 */
function filterTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    if (!input || !table) return;
    
    input.addEventListener('input', debounce(function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }));
}

// =====================================================
// ТЕМЫ (ОПЦИОНАЛЬНО)
// =====================================================

/**
 * Переключение тёмной темы
 */
function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
    const isDark = document.body.classList.contains('dark-mode');
    localStorage.setItem('darkMode', isDark ? '1' : '0');
}

/**
 * Восстановление темы при загрузке
 */
function restoreTheme() {
    const isDark = localStorage.getItem('darkMode') === '1';
    if (isDark) {
        document.body.classList.add('dark-mode');
    }
}

// =====================================================
// ОБНОВЛЕНИЕ СЧЁТЧИКОВ В РЕАЛЬНОМ ВРЕМЕНИ
// =====================================================

/**
 * Обновление счётчика непрочитанных сообщений
 */
function updateUnreadBadge() {
    getData(window.location.origin + '/api/unread-count')
        .then(data => {
            if (data.success) {
                const badges = document.querySelectorAll('.unread-badge');
                badges.forEach(badge => {
                    if (data.count > 0) {
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                        badge.classList.remove('hidden');
                    } else {
                        badge.classList.add('hidden');
                    }
                });
            }
        });
}

// =====================================================
// КОПИРОВАНИЕ В БУФЕР
// =====================================================

/**
 * Копирование текста в буфер обмена
 */
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showToast('Скопировано в буфер обмена', 'success', 2000);
        });
    } else {
        // Fallback
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        showToast('Скопировано в буфер обмена', 'success', 2000);
    }
}

// =====================================================
// ИНИЦИАЛИЗАЦИЯ
// =====================================================

document.addEventListener('DOMContentLoaded', function() {
    // Восстановление темы
    restoreTheme();
    
    // Автоскрытие flash-сообщений через 5 сек
    document.querySelectorAll('.flash-message').forEach(msg => {
        setTimeout(() => {
            msg.style.transition = 'opacity 0.5s, transform 0.5s';
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
            setTimeout(() => msg.remove(), 500);
        }, 5000);
    });
    
    // Подсветка текущего пункта меню в сайдбаре
    highlightCurrentMenuItem();
    
    // Автоматическое обновление даты/времени
    updateClocks();
    
    // Подсказки при наведении на оценки
    initGradeTooltips();
    
    console.log('🎓 Электронный дневник загружен');
});

/**
 * Подсветка текущего пункта меню
 */
function highlightCurrentMenuItem() {
    // Не трогаем, если подсветка уже делается сервером (PHP)
    // Чтобы не было двойной/ошибочной подсветки.
    return;
}

/**
 * Обновление часов (если есть элемент)
 */
function updateClocks() {
    const clockEl = document.getElementById('currentTime');
    if (clockEl) {
        setInterval(() => {
            clockEl.textContent = new Date().toLocaleTimeString('ru-RU', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }, 1000);
    }
}

/**
 * Инициализация тултипов для оценок
 */
function initGradeTooltips() {
    document.querySelectorAll('.grade-cell[data-comment]').forEach(cell => {
        const comment = cell.dataset.comment;
        const type = cell.dataset.type;
        if (comment || (type && type !== 'current')) {
            cell.title = [comment, type !== 'current' ? `(${type})` : ''].filter(Boolean).join(' ');
        }
    });
}

// =====================================================
// ЭКСПОРТ (для использования в inline-скриптах)
// =====================================================

window.showToast = showToast;
window.removeToast = removeToast;
window.openModal = openModal;
window.closeModal = closeModal;
window.postData = postData;
window.getData = getData;
window.confirmDelete = confirmDelete;
window.debounce = debounce;
window.copyToClipboard = copyToClipboard;