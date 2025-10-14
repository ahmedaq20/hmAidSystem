<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Beneficiary extends Model
{
        protected $fillable = [
        'national_id',
        'full_name',
        'phone_number',
        'family_members',
        'address',
        'martyrs_count',
        'injured_count',
        'disabled_count',
        'status',
        'notes',
    ];

    // مساعدة بسيطة للحصول على أجزاء الاسم
    public function nameParts()
    {
        return explode(' ', $this->full_name);
    }

    protected $casts = [
        'family_members' => 'integer',
        'martyrs_count' => 'integer',
        'injured_count' => 'integer',
        'disabled_count' => 'integer',
    ];
}