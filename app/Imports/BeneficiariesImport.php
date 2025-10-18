<?php
// app/Imports/BeneficiariesImport.php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Beneficiary;
use Illuminate\Validation\Rule;
use Illuminate\Support\FacadesLog;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class BeneficiariesImport implements ToModel, WithValidation, WithBatchInserts, WithChunkReading, WithCustomCsvSettings
{
    private $importedCount = 0;
    private $errors = [];
    private $isFirstRow = true;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        try {
            // إذا كان الصف الأول، تخطاه (العناوين)
            if ($this->isFirstRow) {
                $this->isFirstRow = false;
                Log::info('Skipping header row');
                return null;
            }

            // إذا كان الصف فارغاً، تخطيه
            if ($this->isEmptyRow($row)) {
                Log::info('Skipping empty row');
                return null;
            }

            // تنظيف البيانات من القيم الفارغة
            $cleanedRow = $this->cleanRow($row);

            Log::info('Processing row:', $cleanedRow);

            // تعيين القيم بناءً على مواضع الأعمدة
            $data = [
                'national_id'     => $this->cleanNationalId($this->getValue($cleanedRow, 0)),
                'full_name'       => $this->getValue($cleanedRow, 1),
                'phone_number'    => $this->cleanPhoneNumber($this->getValue($cleanedRow, 2)),
                'family_members'  => $this->cleanFamilyMembers($this->getValue($cleanedRow, 3)),
                'address'         => $this->getValue($cleanedRow, 4),
                'martyrs_count'   => $this->cleanNumber($this->getValue($cleanedRow, 5, 0)),
                'injured_count'   => $this->cleanNumber($this->getValue($cleanedRow, 6, 0)),
                'disabled_count'  => $this->cleanNumber($this->getValue($cleanedRow, 7, 0)),
                'status'          => $this->convertStatus($this->getValue($cleanedRow, 8, 'new')),
                'created_at'      => $this->parseDate($this->getValue($cleanedRow, 9)),
            ];

            Log::info('Processed data:', $data);

            // التحقق من البيانات الأساسية
            if (empty($data['national_id']) || empty($data['full_name']) || empty($data['phone_number'])) {
                $this->errors[] = "سطر " . ($this->importedCount + 2) . ": بيانات ناقصة (رقم الهوية، الاسم، أو الهاتف)";
                Log::error('Missing required data in row: ' . ($this->importedCount + 2));
                return null;
            }

            // التحقق من صحة رقم الهوية
            if (strlen($data['national_id']) !== 9) {
                $this->errors[] = "سطر " . ($this->importedCount + 2) . ": رقم الهوية يجب أن يكون 9 أرقام - " . $data['national_id'];
                Log::error('Invalid national ID length: ' . $data['national_id']);
                return null;
            }

            // التحقق من صحة عدد أفراد الأسرة
            if ($data['family_members'] < 1) {
                $this->errors[] = "سطر " . ($this->importedCount + 2) . ": عدد أفراد الأسرة يجب أن يكون 1 على الأقل - القيمة المدخلة: " . $this->getValue($cleanedRow, 3);
                Log::error('Invalid family members count: ' . $this->getValue($cleanedRow, 3));
                return null;
            }

            // التحقق من تكرار رقم الهوية
            if (Beneficiary::where('national_id', $data['national_id'])->exists()) {
                $this->errors[] = "سطر " . ($this->importedCount + 2) . ": رقم الهوية " . $data['national_id'] . " مسجل مسبقاً";
                Log::error('Duplicate national ID: ' . $data['national_id']);
                return null;
            }

            $beneficiary = new Beneficiary($data);
            $this->importedCount++;

            Log::info("Successfully imported beneficiary: " . $data['national_id'] . " - " . $data['full_name'] . " - Family: " . $data['family_members'] . " - Status: " . $data['status']);

            return $beneficiary;

        } catch (\Exception $e) {
            $this->errors[] = "سطر " . ($this->importedCount + 2) . ": " . $e->getMessage();
            Log::error('Import error in row ' . ($this->importedCount + 2) . ': ' . $e->getMessage());
            Log::error('Row data: ' . json_encode($row));
            return null;
        }
    }

    /**
     * تنظيف الصف من القيم الفارغة غير المرغوب فيها
     */
    private function cleanRow(array $row): array
    {
        $cleaned = [];
        foreach ($row as $index => $value) {
            if (is_null($value) || $value === '' || $value === 'null' || $value === 'NULL') {
                $cleaned[$index] = null;
                continue;
            }
            
            // إزالة المسافات الزائدة
            if (is_string($value)) {
                $value = trim($value);
            }
            
            // إذا كانت القيمة فارغة بعد التنظيف، اجعلها null
            if (is_string($value) && $value === '') {
                $cleaned[$index] = null;
            } else {
                $cleaned[$index] = $value;
            }
        }
        
        return $cleaned;
    }

    /**
     * التحقق مما إذا كان الصف فارغاً
     */
    private function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, function ($value) {
            return !is_null($value) && $value !== '' && trim($value) !== '';
        }));
    }

    /**
     * الحصول على قيمة من الصف مع قيمة افتراضية
     */
    private function getValue(array $row, int $index, $default = null)
    {
        if (!isset($row[$index]) || $row[$index] === '' || $row[$index] === null) {
            return $default;
        }

        $value = $row[$index];

        // إذا كانت القيمة كائن، نحولها إلى نص
        if (is_object($value)) {
            $value = (string) $value;
        }

        // تنظيف القيمة من المسافات الزائدة
        if (is_string($value)) {
            $value = trim($value);
        }

        return $value;
    }

    /**
     * تنظيف رقم الهوية الوطني - محسّن
     */
    private function cleanNationalId($nationalId)
    {
        if (is_null($nationalId)) {
            return null;
        }

        // إذا كان رقماً عشرياً (مثل 4.06007E+08)، تحويله إلى عدد صحيح
        if (is_float($nationalId)) {
            $nationalId = (int) $nationalId;
        }

        // تحويل إلى نص وإزالة كل ما عدا الأرقام
        $nationalId = preg_replace('/[^0-9]/', '', (string) $nationalId);
        
        // إذا كان الرقم أقل من 9 أرقام، أضف أصفار من اليسار
        if (strlen($nationalId) < 9) {
            $nationalId = str_pad($nationalId, 9, '0', STR_PAD_LEFT);
        }
        
        // إذا كان الرقم أكثر من 9 أرقام، خذ أول 9 أرقام فقط
        if (strlen($nationalId) > 9) {
            $nationalId = substr($nationalId, 0, 9);
        }

        return $nationalId;
    }

    /**
     * تنظيف عدد أفراد الأسرة - محسّن بشكل خاص
     */
    private function cleanFamilyMembers($value)
    {
        Log::info('Cleaning family members value:', ['original' => $value, 'type' => gettype($value)]);

        if (is_null($value) || $value === '' || $value === 'null' || $value === 'NULL') {
            return 1; // قيمة افتراضية
        }

        // إذا كان نصاً فارغاً
        if (is_string($value) && trim($value) === '') {
            return 1;
        }

        // إذا كان رقماً عشرياً، تحويله إلى عدد صحيح
        if (is_float($value)) {
            $intValue = (int) $value;
            Log::info('Converted float to int:', ['float' => $value, 'int' => $intValue]);
            return $intValue > 0 ? $intValue : 1;
        }

        // إذا كان نصاً، تنظيفه وتحويله إلى عدد
        if (is_string($value)) {
            $cleaned = preg_replace('/[^0-9]/', '', $value);
            Log::info('Cleaned string value:', ['original' => $value, 'cleaned' => $cleaned]);
            
            if ($cleaned === '') {
                return 1;
            }
            
            $intValue = (int) $cleaned;
            return $intValue > 0 ? $intValue : 1;
        }

        // إذا كان قيمة منطقية
        if (is_bool($value)) {
            return $value ? 1 : 1;
        }

        $finalValue = (int) $value;
        Log::info('Final family members value:', ['original' => $value, 'final' => $finalValue]);
        
        return $finalValue > 0 ? $finalValue : 1;
    }

    /**
     * تنظيف الأرقام العامة
     */
    private function cleanNumber($value, $default = 0)
    {
        if (is_null($value) || $value === '' || $value === 'null' || $value === 'NULL') {
            return $default;
        }

        // إذا كان رقماً عشرياً، تحويله إلى عدد صحيح
        if (is_float($value)) {
            return (int) $value;
        }

        // إذا كان نصاً، تنظيفه وتحويله إلى عدد
        if (is_string($value)) {
            $value = preg_replace('/[^0-9]/', '', $value);
            return $value === '' ? $default : (int) $value;
        }

        return (int) $value;
    }

    /**
     * تنظيف رقم الهاتف
     */
    private function cleanPhoneNumber($phoneNumber)
    {
        if (is_null($phoneNumber)) {
            return null;
        }

        // إذا كان رقماً عشرياً، تحويله إلى عدد صحيح
        if (is_float($phoneNumber)) {
            $phoneNumber = (int) $phoneNumber;
        }

        // تحويل إلى نص وإزالة كل ما عدا الأرقام وعلامة +
        $phoneNumber = preg_replace('/[^0-9+]/', '', (string) $phoneNumber);
        
        return $phoneNumber;
    }

    /**
     * تحويل حالة النص إلى القيمة المناسبة في قاعدة البيانات
     */
    private function convertStatus($statusText)
    {
        if (empty($statusText)) {
            return 'new';
        }

        $statusText = strtolower(trim($statusText));
        
        return match ($statusText) {
            'معتمد', 'approved', 'موافقة', 'مقبول', '✅ معتمد', 'موافق', '1' => 'approved',
            'قيد المراجعة', 'pending', 'مراجعة', 'تحت المراجعة', '🕒 قيد المراجعة', 'قيد_المراجعة', '2' => 'pending',
            'جديد', 'new', '🆕 جديد', '0' => 'new',
            default => 'new',
        };
    }

    /**
     * معالجة وتنسيق التاريخ
     */
    private function parseDate($dateValue)
    {
        if (empty($dateValue)) {
            return now();
        }

        try {
            // إذا كان رقماً (تنسيق Excel)
            if (is_numeric($dateValue)) {
                return Carbon::createFromTimestamp((($dateValue - 25569) * 86400));
            }

            // محاولة تحليل التاريخ من نص
            $dateValue = (string) $dateValue;
            
            // استبدال الشرطات المائلة العربية إذا وجدت
            $dateValue = str_replace(['٬', '٫', '\\'], '/', $dateValue);
            
            $formats = [
                'd/m/Y', 'd-m-Y', 'Y-m-d',
                'd/m/Y H:i:s', 'd-m-Y H:i:s', 'Y-m-d H:i:s',
            ];

            foreach ($formats as $format) {
                try {
                    $parsedDate = Carbon::createFromFormat($format, $dateValue);
                    if ($parsedDate !== false) {
                        return $parsedDate;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            // محاولة التحليل التلقائي
            try {
                return Carbon::parse($dateValue);
            } catch (\Exception $e) {
                return now();
            }
            
        } catch (\Exception $e) {
            return now();
        }
    }

    /**
     * قواعد التحقق من البيانات - محدثة لتكون أكثر مرونة
     */
    public function rules(): array
    {
        return [
            '*.0' => [
                'required',
                function ($attribute, $value, $fail) {
                    $cleaned = $this->cleanNationalId($value);
                    if (strlen($cleaned) !== 9) {
                        $fail('رقم الهوية يجب أن يكون 9 أرقام. القيمة المدخلة: ' . $value);
                    }
                }
            ],
            '*.1' => 'required|string|max:255',
            '*.2' => 'required|string|max:20',
            '*.3' => [
                'required',
                function ($attribute, $value, $fail) {
                    $cleaned = $this->cleanFamilyMembers($value);
                    Log::info('Validation - family members:', ['original' => $value, 'cleaned' => $cleaned]);
                    
                    if ($cleaned < 1) {
                        $fail('عدد أفراد الأسرة يجب أن يكون 1 على الأقل. القيمة المدخلة: ' . $value);
                    }
                }
            ],
            '*.4' => 'required|string|max:500',
            '*.5' => 'nullable',
            '*.6' => 'nullable',
            '*.7' => 'nullable',
            '*.8' => 'nullable|string',
            '*.9' => 'nullable',
        ];
    }

    /**
     * تخصيص رسائل الخطأ
     */
    public function customValidationMessages()
    {
        return [
            '*.0.required' => 'حقل رقم الهوية مطلوب',
            '*.1.required' => 'حقل الاسم الرباعي مطلوب',
            '*.2.required' => 'حقل رقم الهاتف مطلوب',
            '*.3.required' => 'حقل عدد أفراد الأسرة مطلوب',
            '*.4.required' => 'حقل مكان السكن مطلوب',
        ];
    }

    /**
     * تخصيص أسماء الحقول
     */
    public function customValidationAttributes()
    {
        return [
            '0' => 'رقم الهوية',
            '1' => 'الاسم الرباعي',
            '2' => 'رقم الهاتف',
            '3' => 'عدد أفراد الأسرة',
            '4' => 'مكان السكن',
            '5' => 'عدد الشهداء',
            '6' => 'عدد الجرحى',
            '7' => 'عدد ذوي الإعاقة',
            '8' => 'الحالة',
            '9' => 'تاريخ التسجيل',
        ];
    }

    /**
     * حجم الدفعة للإدخال
     */
    public function batchSize(): int
    {
        return 50;
    }

    /**
     * حجم القطعة للقراءة
     */
    public function chunkSize(): int
    {
        return 50;
    }

    /**
     * إعدادات CSV إضافية للتعامل مع Tabs
     */
    public function getCsvSettings(): array
    {
        return [
            'delimiter' => "\t", // استخدام Tab كمفصل
            'enclosure' => '"',
            'escape' => '\\',
            'input_encoding' => 'UTF-8',
            'output_encoding' => 'UTF-8',
        ];
    }

    /**
     * الحصول على عدد السجلات المستوردة
     */
    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    /**
     * الحصول على الأخطاء
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * الحصول على معلومات الاستيراد
     */
    public function getImportSummary(): array
    {
        return [
            'imported' => $this->importedCount,
            'errors' => count($this->errors),
            'error_messages' => $this->errors,
        ];
    }
}