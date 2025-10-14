<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Beneficiary;

class InquiryPage extends Component
{

   public $national_id;
    public $result;

    protected $rules = [
        'national_id' => 'required|string|max:20',
    ];

    public function search()
    {
        $this->validate();
        $this->result = Beneficiary::where('national_id', $this->national_id)->first();

        if (! $this->result) {
            return redirect()->route('register', ['id' => $this->national_id]);
        }
    }

    public function render()
    {
        return view('livewire.inquiry-page');
    }
}
