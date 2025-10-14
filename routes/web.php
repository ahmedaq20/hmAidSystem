<?php

use App\Livewire\InquiryPage;
use App\Livewire\RegistrationForm;
use App\Livewire\BeneficiarySearch;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', BeneficiarySearch::class)->name('home');
Route::get('/search', BeneficiarySearch::class)->name('search');