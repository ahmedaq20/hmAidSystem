<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Beneficiary;

class RegistrationForm extends Component
{
    public $nationalId = '';
    public $beneficiary = null;
    public $showRegistration = false;

    protected $rules = [
        'nationalId' => 'required|digits:9'
    ];

    protected $messages = [
        'nationalId.required' => 'يرجى إدخال رقم الهوية',
        'nationalId.digits' => 'رقم الهوية يجب أن يكون 9 أرقام'
    ];

    public function search()
    {
        $this->validate();
        
        $this->beneficiary = Beneficiary::where('national_id', $this->nationalId)->first();
        
        if (!$this->beneficiary) {
            $this->showRegistration = true;
        }
    }

    public function showUpdateForm()
    {
        $this->showRegistration = true;
    }

    public function hideRegistrationForm()
    {
        $this->showRegistration = false;
        $this->beneficiary = null;
        $this->nationalId = '';
    }

    public function getStatusText($status)
    {
        return match ($status) {
            'new' => 'جديد',
            'pending' => 'قيد المراجعة',
            'approved' => 'معتمد',
            default => $status,
        };
    }

    public function render()
    {
        return view('livewire.beneficiary-search')
            ->layout('layouts.app');
    }
}