<?php

namespace App\Exports;

use App\Models\Beneficiary;
use Maatwebsite\Excel\Concerns\FromCollection;

class BeneficiariesExport implements FromCollection
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