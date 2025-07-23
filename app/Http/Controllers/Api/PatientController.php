<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PatientController extends Controller
{
    // List all patients
    public function index()
    {
        if (!Gate::any(['isAdmin', 'isCrmAgent'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $query = Patient::query();
        if ($search = request('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                  ->orWhere('last_name', 'like', "%$search%")
                  ->orWhere('phone_number', 'like', "%$search%");
            });
        }
        return response()->json($query->paginate(10));
    }

    // Create a new patient
    public function store(Request $request)
    {
        if (!Gate::any(['isAdmin', 'isCrmAgent'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $validated = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'phone_number' => 'required|string|unique:patients',
            'email' => 'email|unique:patients',
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'insurance_details' => 'nullable|array',
        ]);
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'] ?? null,
            'password' => bcrypt(Str::random(10)), // Temporary password
        ]);
        $user->assignRole('Patient'); // Assign the 'Patient' role

        // Send password reset email
        if ($user->email) {
            app('Illuminate\Auth\Passwords\PasswordBroker')->sendResetLink(['email' => $user->email]);
        }

        $validated['patient_id'] = (string) Str::uuid();
        $user->patient()->create($validated);
        return response()->json($user->patient, 201);
    }

    // Retrieve a specific patient
    public function show($patient_id)
    {
        $patient = Patient::where('patient_id', $patient_id)->firstOrFail();
        $user = Auth::user();
        if (Gate::any(['isAdmin', 'isCrmAgent', 'isDoctor']) || ($user && $user->id === $patient->id)) {
            return response()->json($patient);
        }
        return response()->json(['error' => 'Forbidden'], 403);
    }

    // Update patient details
    public function update(Request $request, $patient_id)
    {
        if (!Gate::any(['isAdmin', 'isCrmAgent'])) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $patient = Patient::where('patient_id', $patient_id)->firstOrFail();
        $validated = $request->validate([
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'date_of_birth' => 'sometimes|date',
            'gender' => 'sometimes|in:Male,Female,Other',
            'phone_number' => 'sometimes|string|unique:patients,phone_number,' . $patient->id,
            'email' => 'email|unique:patients,email,' . $patient->id,
            'address' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'insurance_details' => 'nullable|array',
        ]);
        // update the user model if name or email is provided
        if (isset($validated['first_name']) || isset($validated['last_name']) || isset($validated['email'])) {
            $user = $patient->user;
            if ($user) {
                $user->name = ($validated['first_name'] ?? $user->name) . ' ' . ($validated['last_name'] ?? '');
                $user->email = $validated['email'] ?? $user->email;
                $user->save();
            }
        }
        $patient->update($validated);
        return response()->json($patient);
    }

    // Delete a patient
    public function destroy($patient_id)
    {
        if (!Gate::allows('isAdmin')) {
            return response()->json(['error' => 'Forbidden'], 403);
        }
        $patient = Patient::where('patient_id', $patient_id)->firstOrFail();
        // Delete the associated user
        if ($patient->user) {
            $patient->user->delete();
        }
        // Delete the patient record
        $patient->delete();
        return response()->json(['message' => 'Patient deleted']);
    }
}
