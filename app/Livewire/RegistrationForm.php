<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Beneficiary;

class RegistrationForm extends Component
{
    public $national_id;
    public $full_name;
    public $phone_number;
    public $family_members = 1;
    public $address;
    public $martyrs_count = 0;
    public $injured_count = 0;
    public $disabled_count = 0;

    protected $rules = [
        'national_id' => 'required|unique:beneficiaries,national_id|max:20',
        'full_name' => 'required|string|max:255',
        'phone_number' => 'nullable|string|max:30',
        'family_members' => 'required|integer|min:1',
        'address' => 'nullable|string|max:255',
        'martyrs_count' => 'nullable|integer|min:0',
        'injured_count' => 'nullable|integer|min:0',
        'disabled_count' => 'nullable|integer|min:0',
    ];

    public function mount()
    {
        if(request()->has('id')) $this->national_id = request('id');
    }

    public function submit()
    {
        $data = $this->validate();
        Beneficiary::create(array_merge($data, ['status' => 'new']));

        // يمكن إرسال إشعار للإدارة هنا

        session()->flash('success', 'تم إنشاء الطلب بنجاح.');
        return redirect()->route('inquiry');
    }

    public function render()
    {
        return view('livewire.registration-form');
    }
}
