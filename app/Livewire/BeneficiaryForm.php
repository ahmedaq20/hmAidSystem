<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Beneficiary;

class BeneficiaryForm extends Component
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

    protected $rules = [
        'national_id' => 'required|digits:9|unique:beneficiaries,national_id',
        'full_name' => 'required|string|max:255',
        'phone_number' => 'required|string|max:20',
        'family_members' => 'required|integer|min:1',
        'address' => 'required|string|max:500',
        'martyrs_count' => 'integer|min:0',
        'injured_count' => 'integer|min:0',
        'disabled_count' => 'integer|min:0',
    ];

    public function mount($beneficiary = null)
    {
        if ($beneficiary) {
            $this->beneficiary = $beneficiary;
            $this->fill($beneficiary->toArray());
            // تحديث القاعدة للتحقق من التفرّد عند التحديث
            $this->rules['national_id'] = 'required|digits:9|unique:beneficiaries,national_id,' . $beneficiary->id;
        }
    }

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
            'status' => $this->beneficiary ? 'pending' : 'new',
        ];

        if ($this->beneficiary) {
            $this->beneficiary->update($data);
            session()->flash('message', 'تم تحديث المعلومات بنجاح وتم إرسالها للمراجعة');
        } else {
            Beneficiary::create($data);
            session()->flash('message', 'تم التسجيل بنجاح وتم إرسال طلبك للمراجعة');
        }

        return redirect()->to('/');
    }

    public function render()
    {
        return view('livewire.beneficiary-form');
    }
}