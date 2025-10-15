<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نظام المساعدات الإنسانية</title>
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
    <style>
        .rtl { direction: rtl; text-align: right; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="rtl bg-gray-100 min-h-screen flex items-center justify-center">

    <div class="container mx-auto px-4 py-8">
        {{ $slot }}
    </div>

    @livewireScripts
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // إشعار SweetAlert من Livewire
        window.addEventListener('swal', (event) => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: event.detail.icon,
                confirmButtonText: 'حسناً'
            });
        });
    </script>

</body>
</html>
