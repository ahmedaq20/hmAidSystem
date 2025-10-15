BeneficiaryResource fixes:
- Fix navigationIcon type to BackedEnum|string|null
- Correct table filters type hints to Eloquent\Builder
- Update BulkAction export to use Excel::download()
- Update BeneficiariesExport with WithHeadings and WithMapping
- Fix table columns labels to display proper Arabic headings<?php

namespace App\Exports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class BeneficiariesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $beneficiaries;

    public function __construct($beneficiaries)
    {
        $this->beneficiaries = $beneficiaries;
    }

    public function collection()
    {
        return $this->beneficiaries;
    }

    // عناوين الأعمدة
    public function headings(): array
    {
        return [
            'رقم الهوية',
            'الاسم الرباعي',
            'رقم الهاتف',
            'عدد أفراد الأسرة',
            'مكان السكن',
            'عدد الشهداء',
            'عدد الجرحى',
            'عدد ذوي الإعاقة',
            'الحالة',
            'تاريخ التسجيل',
        ];
    }

    // كيفية تحويل كل سجل إلى صف في الإكسل
    public function map($beneficiary): array
    {
        return [
            $beneficiary->national_id,
            $beneficiary->full_name,
            $beneficiary->phone_number,
            $beneficiary->family_members,
            $beneficiary->address,
            $beneficiary->martyrs_count,
            $beneficiary->injured_count,
            $beneficiary->disabled_count,
            $this->getStatusText($beneficiary->status),
            $beneficiary->created_at->format('d/m/Y'),
        ];
    }

    private function getStatusText($status)
    {
        return match ($status) {
            'new' => 'جديد',
            'pending' => 'قيد المراجعة',
            'approved' => 'معتمد',
            default => $status,
        };
    }
}