<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Http\Livewire\InquiryPage;
use App\Http\Livewire\RegistrationForm;


return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('national_id', 20)->unique();
            $table->string('full_name');
            $table->string('phone_number')->nullable();
            $table->unsignedSmallInteger('family_members')->default(1);
            $table->string('address')->nullable();
            $table->unsignedSmallInteger('martyrs_count')->default(0);
            $table->unsignedSmallInteger('injured_count')->default(0);
            $table->unsignedSmallInteger('disabled_count')->default(0);
            $table->enum('status', ['new','pending','approved'])->default('new');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiaries');
    }
};