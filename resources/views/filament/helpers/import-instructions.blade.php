<div class="space-y-4">
    <!-- تنبيه مهم -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
        <h4 class="font-medium text-blue-700">ملاحظة هامة:</h4>
        <ul class="mt-2 text-sm text-blue-700 space-y-1">
            <li>• يجب ترتيب الأعمدة حسب التسلسل التالي</li>
            <li>• يمكن أن يبدأ الملف بالعناوين أو بالبيانات مباشرة</li>
        </ul>
    </div>

    <!-- قائمة الأعمدة -->
    <div class="bg-gray-50 rounded-lg p-3">
        <h4 class="font-medium text-gray-700 mb-2">ترتيب الأعمدة:</h4>
        <div class="space-y-2">
            @foreach([
                'رقم الهوية',
                'الاسم الرباعي', 
                'رقم الهاتف',
                'عدد أفراد الأسرة',
                'مكان السكن',
                'عدد الشهداء',
                'عدد الجرحى', 
                'عدد ذوي الإعاقة',
                'الحالة',
                'تاريخ التسجيل'
            ] as $index => $column)
            <div class="flex items-center text-sm">
                <span class="bg-gray-200 text-gray-700 rounded w-6 h-6 flex items-center justify-center text-xs font-medium mr-2">
                    {{ $index + 1 }}
                </span>
                <span class="text-gray-600">{{ $column }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>