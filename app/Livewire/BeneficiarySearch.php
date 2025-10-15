<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Beneficiary;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class BeneficiarySearch extends Component
{
    public $nationalId = '';
    public $beneficiary = null;
    public $showRegistration = false;
    public $isLoading = false;



    protected $listeners = ['beneficiarySaved' => 'handleBeneficiarySaved'];

    public function handleBeneficiarySaved()
    {
        $this->showRegistration = false; // يغلق المودال
        $this->dispatch('swal', [
            'title' => 'تم بنجاح',
            'text' => 'تم حفظ البيانات بنجاح ✅',
            'icon' => 'success',
        ]);
    }

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
        $this->isLoading = true;

        // محاكاة وقت التحميل للتصميم
        sleep(1);

        $this->beneficiary = Beneficiary::where('national_id', $this->nationalId)->first();
        $this->isLoading = false;

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

    // إحصائيات حقيقية من قاعدة البيانات
    public function getStats()
    {
        return [
            'total_families' => Beneficiary::count(),
            'approved_requests' => Beneficiary::where('status', 'approved')->count(),
            'pending_requests' => Beneficiary::where('status', 'pending')->count(),
            'new_requests' => Beneficiary::where('status', 'new')->count(),
        ];
    }

    public function render()
    {
        $stats = $this->getStats();

        return view('livewire.beneficiary-search', compact('stats'));
    }
}
