<!-- resources/views/livewire/beneficiary-registration.blade.php -->

<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-xl font-bold text-blue-600">
            {{ $beneficiary ? '✏️ تحديث البيانات' : '📝 تسجيل مستفيد جديد' }}
        </h3>
        <button wire:click="$dispatch('registration-completed')" class="text-gray-500 hover:text-gray-700 text-2xl">
            ✕
        </button>
    </div>

    <form wire:submit.prevent="save" class="space-y-4">
        <!-- المعلومات الأساسية -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- رقم الهوية -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">🆔 رقم الهوية الوطنية</label>
                <input
                    type="text"
                    wire:model="national_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-center"
                    maxlength="9"
                    dir="ltr"
                >
                @error('national_id') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- الاسم الرباعي -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">👤 الاسم الرباعي</label>
                <input
                    type="text"
                    wire:model="full_name"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                >
                @error('full_name') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>
        </div>

        <!-- معلومات الاتصال -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- رقم الهاتف -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">📞 رقم الهاتف</label>
                <input
                    type="text"
                    wire:model="phone_number"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                    dir="ltr"
                >
                @error('phone_number') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>

            <!-- عدد أفراد الأسرة -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">👨‍👩‍👧‍👦 عدد أفراد الأسرة</label>
                <input
                    type="number"
                    wire:model="family_members"
                    min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-center"
                >
                @error('family_members') 
                    <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
                @enderror
            </div>
        </div>

        <!-- مكان السكن -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">🏠 مكان السكن</label>
            <textarea
                wire:model="address"
                rows="3"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                placeholder="أدخل العنوان التفصيلي..."
            ></textarea>
            @error('address') 
                <span class="text-red-500 text-sm mt-1">{{ $message }}</span> 
            @enderror
        </div>

        <!-- الإحصائيات -->
        <div class="bg-gray-50 p-4 rounded-lg">
            <h4 class="font-medium text-gray-700 mb-3">📊 الإحصائيات (اختياري)</h4>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">🕊️ عدد الشهداء</label>
                    <input
                        type="number"
                        wire:model="martyrs_count"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-center"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">🏥 عدد الجرحى</label>
                    <input
                        type="number"
                        wire:model="injured_count"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-center"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">♿ عدد ذوي الإعاقة</label>
                    <input
                        type="number"
                        wire:model="disabled_count"
                        min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-center"
                    >
                </div>
            </div>
        </div>

        <!-- الأزرار -->
        <div class="flex gap-3 pt-4">
            <button
                type="submit"
                class="flex-1 bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold shadow-md"
            >
                {{ $beneficiary ? '💾 حفظ التحديثات' : '✅ تسجيل البيانات' }}
            </button>
            <button
                type="button"
                wire:click="$dispatch('registration-completed')"
                class="flex-1 bg-gray-500 text-white py-3 px-4 rounded-lg hover:bg-gray-600 transition duration-200 font-semibold"
            >
                ❌ إلغاء
            </button>
        </div>
    </form>
</div>