<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام إدارة المساعدات الإنسانية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Livewire Styles -->
    @livewireStyles
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
        
        .dark-mode {
            background-color: #1a202c;
            color: #e2e8f0;
        }
        
        .dark-mode .dark-bg-gray-800 {
            background-color: #2d3748;
        }
        
        .dark-mode .dark-bg-gray-900 {
            background-color: #1a202c;
        }
        
        .dark-mode .dark-text-white {
            color: #f7fafc;
        }
        
        .dark-mode .dark-text-gray-300 {
            color: #cbd5e0;
        }
        
        .dark-mode .dark-border-gray-700 {
            border-color: #4a5568;
        }
        
        .dark-mode .dark-bg-blue-900 {
            background-color: #2a4365;
        }
        
        .dark-mode .dark-bg-green-900 {
            background-color: #22543d;
        }
        
        .dark-mode .dark-bg-purple-900 {
            background-color: #44337a;
        }
        
        .dark-mode .dark-bg-orange-900 {
            background-color: #7b341e;
        }
        
        .dark-mode .dark-text-blue-300 {
            color: #90cdf4;
        }
        
        .dark-mode .dark-text-green-300 {
            color: #9ae6b4;
        }
        
        .dark-mode .dark-text-purple-300 {
            color: #d6bcfa;
        }
        
        .dark-mode .dark-text-orange-300 {
            color: #fbd38d;
        }
        
        .dark-mode .dark-hover-bg-blue-700:hover {
            background-color: #2b6cb0;
        }
        
        .dark-mode .dark-focus-ring-blue-600:focus {
            --tw-ring-color: #2b6cb0;
        }
        
        .dark-mode .dark-border-blue-800 {
            border-color: #2a4365;
        }
        
        .transition-all {
            transition: all 0.3s ease;
        }
        
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gradient-to-bl from-blue-50 to-blue-100 min-h-screen transition-all">
    <!-- زر تبديل الوضع -->
    <div class="fixed top-4 left-4 z-50">
        <button id="themeToggle" class="bg-white dark:bg-gray-800 rounded-full p-3 shadow-lg transition-all hover:shadow-xl">
            <svg id="sunIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <svg id="moonIcon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-300 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
        </button>
    </div>

    <!-- المحتوى الرئيسي -->
    {{ $slot }}

    <!-- Livewire Scripts -->
    @livewireScripts
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // التحكم في وضع الظلام/الضوء
        const themeToggle = document.getElementById('themeToggle');
        const sunIcon = document.getElementById('sunIcon');
        const moonIcon = document.getElementById('moonIcon');
        const body = document.body;
        
        // التحقق من التفضيل المحفوظ
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme === 'dark') {
            enableDarkMode();
        }
        
        themeToggle.addEventListener('click', () => {
            if (body.classList.contains('dark-mode')) {
                disableDarkMode();
            } else {
                enableDarkMode();
            }
        });
        
        function enableDarkMode() {
            body.classList.add('dark-mode');
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
            localStorage.setItem('theme', 'dark');
        }
        
        function disableDarkMode() {
            body.classList.remove('dark-mode');
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
            localStorage.setItem('theme', 'light');
        }

        // إغلاق المودال بـ ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                Livewire.dispatch('close-modal');
            }
        });

        // معالجة إشعارات Livewire
        window.addEventListener('show-alert', (event) => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.type,
                confirmButtonText: 'حسناً'
            });
        });
    </script>
</body>
</html>