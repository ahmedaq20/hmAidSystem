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
            $this->national_id = $beneficiary->national_id;
            $this->full_name = $beneficiary->full_name;
            $this->phone_number = $beneficiary->phone_number;
            $this->family_members = $beneficiary->family_members;
            $this->address = $beneficiary->address;
            $this->martyrs_count = $beneficiary->martyrs_count;
            $this->injured_count = $beneficiary->injured_count;
            $this->disabled_count = $beneficiary->disabled_count;
        }
    }

    protected function rules()
    {
        $rules = [
            'national_id' => 'required|digits:9',
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'family_members' => 'required|integer|min:1',
            'address' => 'required|string|max:500',
            'martyrs_count' => 'integer|min:0',
            'injured_count' => 'integer|min:0',
            'disabled_count' => 'integer|min:0',
        ];

        if ($this->beneficiary) {
            $rules['national_id'] = 'required|digits:9|unique:beneficiaries,national_id,' . $this->beneficiary->id;
        } else {
            $rules['national_id'] = 'required|digits:9|unique:beneficiaries,national_id';
        }

        return $rules;
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

        try {
            DB::transaction(function () {
                $data = [
                    'national_id' => $this->national_id,
                    'full_name' => $this->full_name,
                    'phone_number' => $this->phone_number,
                    'family_members' => $this->family_members,
                    'address' => $this->address,
                    'martyrs_count' => $this->martyrs_count,
                    'injured_count' => $this->injured_count,
                    'disabled_count' => $this->disabled_count,
                    'status' => $this->beneficiary ? 'pending' : 'new',
                ];

                if ($this->beneficiary) {
                    $this->beneficiary->update($data);
                    $message = 'تم تحديث البيانات بنجاح وانتظار المراجعة';
                } else {
                    Beneficiary::create($data);
                    $message = 'تم التسجيل بنجاح وانتظار المراجعة';
                }

                session()->flash('message', $message);
                $this->dispatch('registration-completed');
            });
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء حفظ البيانات');
        }
    }

    public function render()
    {
        return view('livewire.beneficiary-registration');
    }

}