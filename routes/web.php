<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
});
Route::middleware(['auth', 'role:CRM Agent'])->group(function () {
    Route::get('/crm/dashboard', function () {
        return view('crm.dashboard');
    })->name('crm.dashboard');
});
Route::middleware(['auth', 'role:Doctor'])->group(function () {
    Route::get('/doctor/dashboard', function () {
        return view('doctor.dashboard');
    })->name('doctor.dashboard');
});
Route::middleware(['auth', 'role:Patient'])->group(function () {
    Route::get('/patient/dashboard', function () {
        return view('patient.dashboard');
    })->name('patient.dashboard');
});
Route::middleware(['auth', 'role:Lab Manager'])->group(function () {
    Route::get('/lab/dashboard', function () {
        return view('lab.dashboard');
    })->name('lab.dashboard');
});