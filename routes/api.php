<?php
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\LoginConroller;
use App\Http\Controllers\Api\PatientController;    
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('patients/{patient_id}/audits', [PatientController::class, 'audits']);
    Route::get('appointments', [AppointmentController::class, 'index']);
    Route::post('appointments', [AppointmentController::class, 'store']);
    Route::get('appointments/patient/{patient_id}', [AppointmentController::class, 'patientAppointments']);
    Route::get('appointments/doctor/{doctor_id}', [AppointmentController::class, 'doctorAppointments']);
    Route::put('appointments/{appointment_id}', [AppointmentController::class, 'update']);
    Route::patch('appointments/{appointment_id}', [AppointmentController::class, 'update']);
    Route::delete('appointments/{appointment_id}', [AppointmentController::class, 'destroy']);
    Route::get('patients', [PatientController::class, 'index']);
    Route::post('patients', [PatientController::class, 'store']);
    Route::get('patients/{patient_id}', [PatientController::class, 'show']);
    Route::put('patients/{patient_id}', [PatientController::class, 'update']);
    Route::patch('patients/{patient_id}', [PatientController::class, 'update']);
    Route::delete('patients/{patient_id}', [PatientController::class, 'destroy']);
});


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/login', [LoginConroller::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [LoginConroller::class, 'logout']);