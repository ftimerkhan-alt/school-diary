<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вход — <?= e(APP_NAME) ?></title>
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🎓</text></svg>" type="image/svg+xml">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .login-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .glass-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
        }
        .input-focus:focus {
            box-shadow: 0 0 0 3px rgba(102,126,234,0.3);
        }
        @keyframes fadeInUp {
            from { opacity:0; transform:translateY(30px); }
            to { opacity:1; transform:translateY(0); }
        }
        .animate-fade-in-up { animation: fadeInUp 0.6s ease-out; }
        @keyframes float {
            0%,100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        .animate-float { animation: float 3s ease-in-out infinite; }
        .demo-card { transition: all 0.2s; }
        .demo-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    </style>
</head>
<body class="login-bg flex items-center justify-center p-4">
    
    <div class="w-full max-w-5xl flex flex-col lg:flex-row gap-8 items-center animate-fade-in-up">
        
        <!-- Левая часть: информация -->
        <div class="hidden lg:flex flex-col text-white flex-1 pr-8">
            <div class="animate-float mb-8">
                <i class="fas fa-graduation-cap text-7xl opacity-90"></i>
            </div>
            <h1 class="text-4xl font-bold mb-4">Электронный дневник</h1>
            <p class="text-xl opacity-90 mb-6">Современная платформа для управления учебным процессом</p>
            <div class="space-y-3 text-lg opacity-80">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span>Журнал оценок и посещаемости</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span>Расписание уроков</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span>Отчёты и аналитика</span>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span>Внутренняя почта</span>
                </div>
            </div>
        </div>
        
        <!-- Правая часть: форма входа -->
        <div class="w-full max-w-md">
            <div class="glass-card rounded-2xl shadow-2xl p-8">
                
                <!-- Мобильный логотип -->
                <div class="lg:hidden text-center mb-6">
                    <i class="fas fa-graduation-cap text-5xl text-indigo-600"></i>
                    <h1 class="text-2xl font-bold text-gray-800 mt-2"><?= e(APP_NAME) ?></h1>
                </div>
                
                <h2 class="text-2xl font-bold text-gray-800 mb-2 hidden lg:block">Вход в систему</h2>
                <p class="text-gray-500 mb-6 hidden lg:block">Введите ваши учётные данные</p>
                
                <?php if (!empty($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r-lg" role="alert">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        <p><?= e($error) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (hasFlash('info')): ?>
                <div class="bg-blue-50 border-l-4 border-blue-500 text-blue-700 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        <p><?= e(getFlash('info')) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if (hasFlash('warning')): ?>
                <div class="bg-yellow-50 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-6 rounded-r-lg">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-triangle mr-2"></i>
                        <p><?= e(getFlash('warning')) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <form method="POST" action="<?= url('login') ?>" autocomplete="off">
                    <div class="mb-5">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user mr-1 text-indigo-500"></i> Логин
                        </label>
                        <input type="text" name="login" value="<?= e($login) ?>" required
                               class="input-focus w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all text-gray-800"
                               placeholder="Введите логин" autofocus>
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-indigo-500"></i> Пароль
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password" required
                                   class="input-focus w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:border-indigo-500 transition-all text-gray-800"
                                   placeholder="Введите пароль">
                            <button type="button" id="togglePasswordBtn"
        class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
    <i class="fas fa-eye" id="eyeIcon"></i>
</button>
                        </div>
                    </div>
                    
                    <button type="submit" 
                            class="w-full py-3 px-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-indigo-300 transition-all transform hover:scale-[1.02] active:scale-[0.98] shadow-lg">
                        <i class="fas fa-sign-in-alt mr-2"></i> Войти
                    </button>
                </form>
                
                <!-- Демо-аккаунты 
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500 mb-3 font-semibold">
                        <i class="fas fa-key mr-1"></i> Демо-аккаунты (пароль: 123)
                    </p>
                    <div class="grid grid-cols-2 gap-2">
                        <button onclick="fillLogin('admin')" class="demo-card text-left text-xs px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300">
                            <span class="font-semibold text-indigo-600">admin</span>
                            <span class="text-gray-400 block">Администратор</span>
                        </button>
                        <button onclick="fillLogin('director')" class="demo-card text-left text-xs px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300">
                            <span class="font-semibold text-indigo-600">director</span>
                            <span class="text-gray-400 block">Директор</span>
                        </button>
                        <button onclick="fillLogin('headteacher')" class="demo-card text-left text-xs px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300">
                            <span class="font-semibold text-indigo-600">headteacher</span>
                            <span class="text-gray-400 block">Завуч</span>
                        </button>
                        <button onclick="fillLogin('classteacher')" class="demo-card text-left text-xs px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300">
                            <span class="font-semibold text-indigo-600">classteacher</span>
                            <span class="text-gray-400 block">Кл. руководитель</span>
                        </button>
                        <button onclick="fillLogin('teacher1')" class="demo-card text-left text-xs px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300">
                            <span class="font-semibold text-indigo-600">teacher1</span>
                            <span class="text-gray-400 block">Учитель</span>
                        </button>
                        <button onclick="fillLogin('student1')" class="demo-card text-left text-xs px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300">
                            <span class="font-semibold text-indigo-600">student1</span>
                            <span class="text-gray-400 block">Ученик</span>
                        </button>
                        <button onclick="fillLogin('parent1')" class="demo-card text-left text-xs px-3 py-2 bg-gray-50 rounded-lg border border-gray-200 hover:border-indigo-300 col-span-2">
                            <span class="font-semibold text-indigo-600">parent1</span>
                            <span class="text-gray-400">— Родитель</span>
                        </button>
                    </div>
                </div>
                -->
            </div>
            
            <p class="text-center text-white/70 text-sm mt-4">
                <?= e(APP_NAME) ?> v<?= APP_VERSION ?> &copy; <?= date('Y') ?>
            </p>
        </div>
    </div>
    
    <script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('password');
    const btn = document.getElementById('togglePasswordBtn');
    const icon = document.getElementById('eyeIcon');

    if (!input || !btn || !icon) return;

    btn.addEventListener('click', function() {
        const isHidden = input.type === 'password';
        input.type = isHidden ? 'text' : 'password';

        if (isHidden) {
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});
</script>
</body>
</html>