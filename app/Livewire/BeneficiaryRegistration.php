<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Beneficiary;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;

#[Layout('components.layouts.app')]
class BeneficiaryRegistration extends Component
{
    public $beneficiary;
    public $national_id;
    public $full_name;
    public $phone_number;
    public $family_members;
    public $address;
    public $martyrs_count = 0;
    public $injured_count = 0;
    public $disabled_count = 0;

    public function mount($beneficiary = null)
    {
        if ($beneficiary) {
            $this->beneficiary = $beneficiary;
            $this->fill($beneficiary->only([
                'national_id', 'full_name', 'phone_number', 'family_members',
                'address', 'martyrs_count', 'injured_count', 'disabled_count'
            ]));
        }
    }

    protected function rules()
    {
        return [
            'national_id' => 'required|digits:9|unique:beneficiaries,national_id,' . ($this->beneficiary->id ?? 'NULL'),
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'family_members' => 'required|integer|min:1',
            'address' => 'required|string|max:500',
            'martyrs_count' => 'nullable|integer|min:0',
            'injured_count' => 'nullable|integer|min:0',
            'disabled_count' => 'nullable|integer|min:0',
        ];
    }

    protected $messages = [
        'national_id.required' => 'رقم الهوية مطلوب',
        'national_id.digits' => 'رقم الهوية يجب أن يكون 9 أرقام',
        'national_id.unique' => 'رقم الهوية مسجل مسبقاً',
        'full_name.required' => 'الاسم الرباعي مطلوب',
        'phone_number.required' => 'رقم الهاتف مطلوب',
        'family_members.required' => 'عدد أفراد الأسرة مطلوب',
        'family_members.min' => 'عدد أفراد الأسرة يجب أن يكون على الأقل 1',
        'address.required' => 'مكان السكن مطلوب',
    ];

    public function save()
    {
        $this->validate();

        $data = [
            'national_id' => $this->national_id,
            'full_name' => $this->full_name,
            'phone_number' => $this->phone_number,
            'family_members' => $this->family_members,
            'address' => $this->address,
            'martyrs_count' => $this->martyrs_count,
            'injured_count' => $this->injured_count,
            'disabled_count' => $this->disabled_count,
            'status' => 'pending',
            // 'status' => $this->beneficiary ? 'pending' : 'new',
        ];

        try {
            if ($this->beneficiary) {
                $this->beneficiary->update($data);
                $message = 'تم تحديث البيانات بنجاح ✅ وسيتم مراجعتها قريباً.';
            } else {
                Beneficiary::create($data);
                $message = 'تم تسجيل المستفيد بنجاح ✅ بانتظار المراجعة.';
            }

            $this->dispatch('swal', [
                'title' => 'نجاح العملية',
                'text' => $message,
                'icon' => 'success',
            ]);

                // إرسال حدث للأب لإغلاق المودال
             $this->dispatch('beneficiarySaved');

            $this->reset(['national_id', 'full_name', 'phone_number', 'family_members', 'address', 'martyrs_count', 'injured_count', 'disabled_count']);

        } catch (\Throwable $e) {
            $this->dispatch('swal', [
                'title' => 'خطأ!',
                'text' => 'حدث خطأ أثناء حفظ البيانات. الرجاء المحاولة لاحقاً.',
                'icon' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.beneficiary-registration');
    }
}
