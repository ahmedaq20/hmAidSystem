<!-- resources/views/livewire/beneficiary-search.blade.php -->
<div>
    <!-- الشعار -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-center mb-8">
            <div class="bg-white dark-bg-gray-800 rounded-full p-6 shadow-lg transition-all">
                <div class="w-40 h-40 bg-blue-100 dark-bg-blue-900 rounded-full flex items-center justify-center transition-all">
                    <img src="{{asset('image/hmlogo.png')}}" alt="Logo">
                </div>
            </div>
        </div>

        <!-- العنوان الرئيسي -->
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 dark-text-white mb-4 transition-all">نظام إدارة المساعدات الإنسانية</h1>
            <p class="text-lg text-gray-600 dark-text-gray-300 max-w-2xl mx-auto transition-all">
                نظام مخصص لتقديم المساعدات للأسر المتضررة من الحرب. يمكنك الاستعلام عن حالتك أو تسجيل بياناتك للحصول على الدعم.
            </p>
        </div>

        <!-- مربع الاستعلام -->
        <div class="max-w-2xl mx-auto bg-white dark-bg-gray-800 rounded-xl shadow-lg p-6 md:p-8 transition-all">
            <h2 class="text-xl font-semibold text-gray-800 dark-text-white mb-6 text-center transition-all">استعلام عن حالة المستفيد</h2>

            <form wire:submit.prevent="search" class="space-y-6">
                <div>
                    <label for="nationalId" class="block text-gray-700 dark-text-gray-300 mb-2 text-right transition-all">رقم الهوية الوطنية</label>
                    <input
                        type="text"
                        id="nationalId"
                        wire:model="nationalId"
                        placeholder="أدخل رقم الهوية الوطنية"
                        class="w-full px-4 py-3 border border-gray-300 dark-border-gray-700 rounded-lg focus:ring-2 focus:ring-blue-500 dark-focus-ring-blue-600 focus:border-blue-500 dark-bg-gray-700 dark-text-white transition-all text-right placeholder:text-right"
                        required
                        dir="rtl"
                        maxlength="9"
                    >
                    @error('nationalId')
                        <p class="text-red-500 text-sm mt-2 text-right">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 dark-text-gray-400 mt-2 transition-all text-right">يرجى إدخال رقم الهوية الوطنية لرب الأسرة (الأب) أو الأم في حالة الأرملة</p>
                </div>

                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 dark-bg-blue-700 dark-hover-bg-blue-700 text-white font-medium py-3 px-4 rounded-lg transition-all duration-300 flex items-center justify-center"
                    wire:loading.attr="disabled"
                    wire:target="search"
                >
                   <span wire:loading.remove wire:target="search" class="inline-flex items-center">
    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
    </svg>
    استعلم عن بياناتي
</span>

                    <span wire:loading wire:target="search">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        جاري البحث...
                    </span>
                </button>
            </form>

            <!-- عرض بيانات المستفيد -->
            @if($beneficiary && !$showRegistration)
                <div class="mt-6 p-4 bg-green-50 dark-bg-green-900 rounded-lg border border-green-200 dark-border-gray-700 transition-all">
                    <h3 class="text-lg font-semibold text-green-800 dark-text-green-300 mb-4 text-center">✅ بيانات المستفيد</h3>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">رقم الهوية:</span>
                            <span class="text-gray-900 dark-text-white">{{ $beneficiary->national_id }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">الاسم الكامل:</span>
                            <span class="text-gray-900 dark-text-white">{{ $beneficiary->full_name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">رقم الهاتف:</span>
                            <span class="text-gray-900 dark-text-white">{{ $beneficiary->phone_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">عدد أفراد الأسرة:</span>
                            <span class="text-gray-900 dark-text-white">{{ $beneficiary->family_members }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">مكان السكن:</span>
                            <span class="text-gray-900 dark-text-white text-left">{{ $beneficiary->address }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">عدد الشهداء:</span>
                            <span class="text-gray-900 dark-text-white">{{ $beneficiary->martyrs_count }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">عدد الجرحى:</span>
                            <span class="text-gray-900 dark-text-white">{{ $beneficiary->injured_count }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">عدد ذوي الإعاقة:</span>
                            <span class="text-gray-900 dark-text-white">{{ $beneficiary->disabled_count }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-700 dark-text-gray-300">الحالة:</span>
                            <span class="{{ $beneficiary->status === 'approved' ? 'text-green-600 dark-text-green-400 font-bold' : 'text-yellow-600 dark-text-yellow-400' }}">
                                {{ $this->getStatusText($beneficiary->status) }}
                            </span>
                        </div>
                    </div>

                    <button
                        wire:click="showUpdateForm"
                        class="mt-4 w-full bg-amber-500 hover:bg-amber-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200"
                    >
                        ✏️ تحديث المعلومات
                    </button>
                </div>
            @endif

            <!-- معلومات إضافية -->
            <div class="mt-8 pt-6 border-t border-gray-200 dark-border-gray-700 transition-all">
                <h3 class="text-lg font-medium text-gray-800 dark-text-white mb-4 transition-all text-right">معلومات مهمة:</h3>
                <ul class="space-y-2 text-gray-600 dark-text-gray-300 transition-all">
                    <li class="flex items-start text-right">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 dark-text-blue-400 mt-0.5 ml-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-right">البيانات محمية ولا يتم مشاركتها مع أي جهة خارجية</span>
                    </li>
                    <li class="flex items-start text-right">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 dark-text-blue-400 mt-0.5 ml-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-right">يمكنك تحديث بياناتك في أي وقت عند حدوث أي تغيير</span>
                    </li>
                    <li class="flex items-start text-right">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500 dark-text-blue-400 mt-0.5 ml-2 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span class="text-right">جميع البيانات تخضع للمراجعة والتحقق من قبل الإدارة</span>
                    </li>
                </ul>
            </div>
        </div>

        <!-- أقسام المساعدة -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12 max-w-4xl mx-auto">
            <div class="bg-white dark-bg-gray-800 rounded-lg shadow p-6 text-center transition-all hover:shadow-lg">
                <div class="w-16 h-16 bg-blue-100 dark-bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark-text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark-text-white mb-2 transition-all">التسجيل الأولي</h3>
                <p class="text-gray-600 dark-text-gray-300 transition-all">للتسجيل لأول مرة في النظام والحصول على المساعدات</p>
            </div>

            <div class="bg-white dark-bg-gray-800 rounded-lg shadow p-6 text-center transition-all hover:shadow-lg">
                <div class="w-16 h-16 bg-green-100 dark-bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-600 dark-text-green-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark-text-white mb-2 transition-all">تحديث البيانات</h3>
                <p class="text-gray-600 dark-text-gray-300 transition-all">لتحديث بياناتك في حالة حدوث أي تغيير في وضع الأسرة</p>
            </div>

            <div class="bg-white dark-bg-gray-800 rounded-lg shadow p-6 text-center transition-all hover:shadow-lg">
                <div class="w-16 h-16 bg-purple-100 dark-bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4 transition-all">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-purple-600 dark-text-purple-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-800 dark-text-white mb-2 transition-all">الدعم والمساعدة</h3>
                <p class="text-gray-600 dark-text-gray-300 transition-all">للحصول على المساعدة في حالة وجود أي استفسارات أو مشاكل</p>
            </div>
        </div>

        <!-- إحصائيات -->
        {{-- <div class="mt-16 bg-white dark-bg-gray-800 rounded-xl shadow-lg p-8 max-w-4xl mx-auto transition-all">
            <h2 class="text-2xl font-bold text-gray-800 dark-text-white mb-8 text-center transition-all">إحصائيات النظام</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                <div class="bg-blue-50 dark-bg-blue-900 rounded-lg p-4 transition-all hover:shadow-md">
                    <div class="text-3xl font-bold text-blue-600 dark-text-blue-300 mb-2">{{ number_format($stats['total_families']) }}</div>
                    <div class="text-gray-600 dark-text-gray-300">أسرة مسجلة</div>
                </div>
                <div class="bg-green-50 dark-bg-green-900 rounded-lg p-4 transition-all hover:shadow-md">
                    <div class="text-3xl font-bold text-green-600 dark-text-green-300 mb-2">{{ number_format($stats['approved_requests']) }}</div>
                    <div class="text-gray-600 dark-text-gray-300">طلب معتمد</div>
                </div>
                <div class="bg-purple-50 dark-bg-purple-900 rounded-lg p-4 transition-all hover:shadow-md">
                    <div class="text-3xl font-bold text-purple-600 dark-text-purple-300 mb-2">{{ number_format($stats['pending_requests']) }}</div>
                    <div class="text-gray-600 dark-text-gray-300">طلب قيد المراجعة</div>
                </div>
                <div class="bg-orange-50 dark-bg-orange-900 rounded-lg p-4 transition-all hover:shadow-md">
                    <div class="text-3xl font-bold text-orange-600 dark-text-orange-300 mb-2">{{ number_format($stats['new_requests']) }}</div>
                    <div class="text-gray-600 dark-text-gray-300">طلب جديد</div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- تذييل الصفحة -->
    <footer class="bg-white dark-bg-gray-800 border-t border-gray-200 dark-border-gray-700 mt-12 transition-all">
        <!-- ... نفس التذييل من التصميم الأصلي ... -->
    </footer>

    <!-- المودال للتسجيل/التحديث -->
    @if($showRegistration)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" wire:click="hideRegistrationForm">
            <div class="bg-white dark-bg-gray-800 rounded-2xl shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" wire:click.stop>
                <livewire:beneficiary-registration :beneficiary="$beneficiary" />
            </div>
        </div>
    @endif
    <style>
    /* دعم الاتجاه من اليمين لليسار */
    .swal2-rtl {
        direction: rtl;
        text-align: right;
        font-family: "Tajawal", sans-serif;
    }
</style>

</div>





