<div class="flex justify-center items-center min-h-screen bg-gray-50">
    <div class="bg-white shadow-xl rounded-2xl w-full max-w-2xl p-6 border border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h3 class="text-2xl font-bold text-blue-600">
                {{ $beneficiary ? '✏️ تحديث بيانات المستفيد' : '📝 تسجيل مستفيد جديد' }}
            </h3>
        </div>

        <form wire:submit.prevent="save" class="space-y-4">
            <!-- الهوية والاسم -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهوية</label>
                    <input type="text" wire:model="national_id" maxlength="9" dir="ltr"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('national_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">الاسم الرباعي</label>
                    <input type="text" wire:model="full_name"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('full_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- الهاتف وعدد الأسرة -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">رقم الهاتف</label>
                    <input type="text" wire:model="phone_number"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('phone_number') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عدد أفراد الأسرة</label>
                    <input type="number" wire:model="family_members" min="1"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    @error('family_members') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- مكان السكن -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">مكان السكن</label>
                <textarea wire:model="address" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"></textarea>
                @error('address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- الإحصائيات -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عدد الشهداء</label>
                    <input type="number" wire:model="martyrs_count" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عدد الجرحى</label>
                    <input type="number" wire:model="injured_count" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">عدد ذوي الإعاقة</label>
                    <input type="number" wire:model="disabled_count" min="0"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <!-- الأزرار -->
            <div class="flex gap-3 pt-4">
                <button type="submit"
                    class="flex-1 bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition">
                    {{ $beneficiary ? 'تحديث البيانات' : 'تسجيل' }}
                </button>
                <button type="button" wire:click="$reset"
                    class="flex-1 bg-gray-500 text-white py-2 px-4 rounded-lg hover:bg-gray-600 transition">
                    إلغاء
                </button>
            </div>
        </form>
    </div>
</div>
