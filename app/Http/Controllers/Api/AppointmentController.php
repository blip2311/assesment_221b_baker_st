<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class AppointmentController extends Controller
{
    // List all appointments
    public function index()
    {
        if (!Gate::any(['isAdmin', 'isCrmAgent'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        return response()->json(Appointment::paginate(10));
    }

    // Schedule a new appointment
    public function store(Request $request)
    {
        if (!Gate::any(['isAdmin', 'isCrmAgent'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required',
            'status' => 'required|in:Scheduled,Confirmed,Completed,Cancelled,No-Show',
            'notes' => 'nullable|string',
        ]);
        $exists = Appointment::where('doctor_id', $validated['doctor_id'])
            ->where('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->exists();
        if ($exists) {
            return response()->json(['error' => 'Doctor not available at this time'], 422);
        }
        $appointment = Appointment::create($validated);
        return response()->json($appointment, 201);
    }

    // List appointments for a specific patient
    public function patientAppointments($patient_id)
    {
        $user = Auth::user();
        $patient = Patient::findOrFail($patient_id);
        if (Gate::any(['isAdmin', 'isCrmAgent']) || ($user && $user->id === $patient->id) || ($user && $user->doctor && $user->doctor->patients->contains($patient))) {
            return response()->json($patient->appointments()->paginate(10));
        }
        return response()->json(['error' => 'Forbidden'], 403);
    }

    // List appointments for a specific doctor
    public function doctorAppointments($doctor_id)
    {
        $user = Auth::user();
        $doctor = Doctor::findOrFail($doctor_id);
        if (Gate::any(['isAdmin', 'isCrmAgent']) || ($user && $user->id === $doctor->id)) {
            return response()->json($doctor->appointments()->paginate(10));
        }
        return response()->json(['error' => 'Forbidden'], 403);
    }

    // Update appointment
    public function update(Request $request, $appointment_id)
    {
        $appointment = Appointment::findOrFail($appointment_id);
        $user = Auth::user();
        if (!Gate::any(['isAdmin', 'isCrmAgent']) && !($user && $user->doctor && $user->doctor->id === $appointment->doctor_id)) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'appointment_date' => 'sometimes|date',
            'appointment_time' => 'sometimes',
            'status' => 'sometimes|in:Scheduled,Confirmed,Completed,Cancelled,No-Show',
            'notes' => 'nullable|string',
        ]);
        // If rescheduling, check doctor's availability
        if (isset($validated['appointment_date']) && isset($validated['appointment_time'])) {
            $exists = Appointment::where('doctor_id', $appointment->doctor_id)
                ->where('appointment_date', $validated['appointment_date'])
                ->where('appointment_time', $validated['appointment_time'])
                ->where('id', '!=', $appointment_id)
                ->exists();
            if ($exists) {
                return response()->json(['error' => 'Doctor not available at this time'], 422);
            }
        }
        $appointment->update($validated);
        return response()->json($appointment);
    }

    // Cancel appointment
    public function destroy($appointment_id)
    {
        $user = Auth::user();
        if (!Gate::any(['isAdmin', 'isCrmAgent'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $appointment = Appointment::findOrFail($appointment_id);
        $appointment->delete();
        return response()->json(['message' => 'Appointment cancelled']);
    }
}
